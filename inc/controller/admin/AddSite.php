<?php defined('TOKICMS') or die('Hacking attempt...');

class AddSite extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;

		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-sites' ) ) || !MULTISITE )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'add-site' ) . ' | ' . $Admin->SiteName() );
		
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		if ( !verify_token( 'add-site' ) )
			return;
	
		//We can't add a new site if we don't have a name and a URL
		if ( empty( $_POST['title'] ) || empty( $_POST['url'] ) )
		{
			$Admin->SetAdminMessage( __( 'empty-form-error' ) );
			return;
		}
		
		//Make sure we have a trailing slash
		$url = LastTrailCheck( $_POST['url'] );

		// Check it this URL exists in the DB
		$exists = $this->db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "sites`
		WHERE (url = :url)",
		array( $url => ':url' )
		)->single();

		if ( $exists )
		{
			$Admin->SetAdminMessage( __( 'site-already-exists' ) );
			return;
		}
		
		$langs = $Admin->Settings()::AllLangs();

		$maint = array();
		
		$host = $_POST['select-host'];
		
		$selfHosted = ( ( $host == 'self' ) ? true : false );
	
		//Add a temporary image
		$maint['background_image'] = $url . 'inc/tools/theme_files/assets/frontend/img/sample-background.jpg';
	
		$maint 			= json_encode( $maint );
		$pingSlash 		= ( ( isset( $_POST['ping'] ) && !empty( $_POST['ping'] ) ) ? Sanitize( $_POST['ping'] ) : 'ping' );
		$siteSecret 	= GenerateStrongRandomKey( 15 );
		$previewHash 	= GenerateStrongRandomKey( 15 );
		$updateHash 	= GenerateStrongRandomKey( 15 );
		$secretHash 	= GenerateStrongRandomKey( 15 );
		$cacheHash 		= GenerateStrongRandomKey( 15 );
		$pingUrl 		= $url . $pingSlash . PS;

		//Set the Api Keys
		$apiSettings = array(
			'blogger' => array(
				'api' 			=> ( isset( $_POST['bloggerApi'] ) ? Sanitize ( $_POST['bloggerApi'], false ) : null ),
				'blog-id' 		=> ( isset( $_POST['bloggerBlogId'] ) ? Sanitize ( $_POST['bloggerBlogId'], false ) : null )
			),
			
			'wordpress' => array(
				'client-id' 	=> ( isset( $_POST['wordpressClientApi'] ) ? Sanitize ( $_POST['wordpressClientApi'], false ) : null ),
				'client-secret' => ( isset( $_POST['wordpressClientSecret'] ) ? Sanitize ( $_POST['wordpressClientSecret'], false ) : null ),
				'blog-id' 		=> ( isset( $_POST['wpBlogId'] ) ? Sanitize ( $_POST['wpBlogId'], false ) : null )
			)
		);
		
		//Insert the new site into the DB
		$dbarr = array(
			"title" 				=> $_POST['title'],
			"url" 					=> $url,
			"site_secret" 			=> $secretHash,
			"site_ping_url" 		=> $pingUrl,
			"enable_multilang" 		=> ( ( isset( $_POST['enable_polylang'] ) && !empty( $_POST['enable_polylang'] ) ) ? 'true' : 'false' ),
			"enable_multiblog" 		=> ( ( isset( $_POST['enable_multiblog'] ) && !empty( $_POST['enable_multiblog'] ) ) ? 'true' : 'false'  ),
			"enable_maintenance" 	=> ( ( isset( $_POST['enable_maintenance'] ) && !empty( $_POST['enable_maintenance'] ) ) ? 'true' : 'false'  ),
			"enable_registration" 	=> ( ( isset( $_POST['enable_registration'] ) && !empty( $_POST['enable_registration'] ) ) ? 'true' : 'false'  ),
			"cache_hash" 			=> $cacheHash,
			"maintenance_data" 		=> $maint,
			"update_hash" 			=> $updateHash,
			"ping_slash" 			=> $pingSlash,
			"preview_hash" 			=> $previewHash,
			"hosted" 				=> $host
		);

		$siteId = $this->db->insert( 'sites' )->set( $dbarr, null, true );

		if ( !$siteId )
		{
			$Admin->SetAdminMessage( __( 'site-add-error' ) );
			return;
		}
		
		if ( !isset( $_POST['copy_settings'] ) )
		{
			$db = dbLoad();
			
			// Load the installation data
			include_once ( DATA_ROOT . 'install-data.php');
			
			require ( ARRAYS_ROOT . 'generic-arrays.php');
			
			$installData = str_replace( '___SITEID___', $siteId, $siteInstallSettings );

			// Add the default settings for this site
			try 
			{
				$set = $db->prepare( $installData );
				$set->execute();
			}
			catch(PDOException $e) 
			{
				//
			}
			
			unset( $db );
		}
		
		//Load the settings
		else
		{
			$_setts = $this->db->from( 
			null, 
			"SELECT variable, config_group, value
			FROM `" . DB_PREFIX . "config`
			WHERE (id_site = " . $Admin->GetSite() . ")
			ORDER BY variable ASC"
			)->all();

			//Don't continue without the settings
			if ( !$_setts )
			{
				$Admin->SetAdminMessage( __( 'site-add-error' ) );
				return;
			}
			
			foreach( $_setts as $_sett )
			{
				//Clone the settings
				$dbarr = array(
					"variable" 		=> $_sett['variable'],
					"id_site" 		=> $siteId,
					"config_group" 	=> $_sett['config_group'],
					"value" 		=> $_sett['value']
				);

				$this->db->insert( 'config' )->set( $dbarr );
			}
		}
		
		//Add the APIs settings
		$this->db->update( "config" )->where( 'variable', 'api_keys' )->where( 'id_site', $siteId )->set( "value", json_encode( $apiSettings, JSON_UNESCAPED_UNICODE ) );
		
		if ( ( $_POST['site_lang'] == 'default' ) || !isset( $langs[$_POST['site_lang']] ) )
		{
			// Grab the default language data before continue
			$lang = $this->db->from( 
			null, 
			"SELECT la.*, co.date_format, co.time_format
			FROM `" . DB_PREFIX . "languages` AS la
			INNER JOIN `" . DB_PREFIX . "languages_config` AS co ON co.id_lang = la.id
			WHERE (la.id_site = " . $Admin->GetSite() . ") AND (la.is_default = 1)"
			)->single();
		}
		
		else
		{
			$_lang = $langs[$_POST['site_lang']];
			
			$lang = array(
				'code' => $_lang['code'],
				'title' => $_lang['name'],
				'locale' => $_lang['locale'],
				'direction' => 'ltr',
				'flagicon' => $_lang['icon'],
				'date_format' => 'F j, Y',
				'time_format' => 'h:i:s'
			);
		}

		// Now add the default language settings to this site
		$dbarr = array(
			"id_site" 		=> $siteId,
			"code" 			=> $lang['code'],
			"title" 		=> $lang['title'],
			"locale" 		=> $lang['locale'],
			"direction" 	=> $lang['direction'],
			"is_default" 	=> 1,
			"flagicon" 		=> $lang['flagicon'],
			"lang_order" 	=> 1
		);

		$langId = $this->db->insert( 'languages' )->set( $dbarr, null, true );
		
		$dbarr = array(
			"id_lang" 		=> $langId,
			"site_name" 	=> $_POST['title'],
			"date_format" 	=> $lang['date_format'],
			"time_format" 	=> $lang['time_format'],
			"footer_text"	=> '{{copyright}} {{current-year}} <a href=\"{{site-url}}\">{{site-title}}</a> ~ {{powered-by-toki-cms}}'
		);

		$this->db->insert( 'languages_config' )->set( $dbarr );
		
		//Add a default category to this site
		$dbarr = array(
			"id_lang" 		=> $langId,
			"id_site" 		=> $siteId,
			"is_default" 	=> 1,
			"name" 			=> 'Uncategorized',
			"sef" 			=> 'uncategorized'
		);

		$this->db->insert( 'categories' )->set( $dbarr );
			
		//Clone the current user, membergroups and set a relation
		$auth = $this->getVariable( 'AuthUser' );
		
		$userId = $auth['id_member'];
		$groupId = $auth['id_group'];
		
		//Get the group_permissions and clone them
		$rels = $this->db->from( 
		null, 
		"SELECT id_group, group_permissions
		FROM `" . DB_PREFIX . "membergroup_relation`
		WHERE (id_site = " . $Admin->GetSite() . ")"
		)->all();

		foreach( $rels as $rel )
		{
			$dbarr = array(
				"id_group" 			=> $rel['id_group'],
				"id_site" 			=> $siteId,
				"group_permissions" => $rel['group_permissions']
			);

			$this->db->insert( 'membergroup_relation' )->set( $dbarr );
		}

		//Get user's details
		$usr = $this->db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . USERS . "`
		WHERE (id_member = " . $userId . ")"
		)->single();
			
		// Copy the user's details
		$dbarr = array(
			"id_group" 			=> $groupId,
			"id_site" 			=> $siteId,
			"id_lang" 			=> $langId,
			"user_name" 		=> $usr['user_name'],
			"date_registered" 	=> time(),
			"email_address" 	=> $usr['email_address'],
			"passwd" 			=> $usr['passwd'],
			"password_hash" 	=> $usr['password_hash'],
		);

		$uId = $this->db->insert( USERS )->set( $dbarr, null, true );
		
		//Now create the relation for this user
		if ( $uId )
		{
			$dbarr = array(
				"id_member" 		=> $userId,
				"id_cloned_member" 	=> $uId,
				"id_site" 			=> $siteId
			);

			$this->db->insert( "members_relationships" )->set( $dbarr );
		}
		
		if ( !Settings::IsTrue( 'share_images_sites' ) )
		{
			$dbarr = array(
				"id_site" 		=> $siteId,
				"id_lang" 		=> $langId,
				"is_default" 	=> 1,
				"name" 			=> $_POST['title'],
				"sef" 			=> URLify( $_POST['title'] )
			);

			$this->db->insert( "image_folders" )->set( $dbarr );
		}

		//Edit the new site's settings
		$this->db->update( "config" )->where( 'variable', 'enable_registrations' )->where( 'id_site', $siteId )->set( "value", ( ( isset( $_POST['enable_registration'] ) && !empty( $_POST['enable_registration'] ) ) ? 'true' : 'false' ) );
		
		//Let's clone the scheduled tasks
		$tasks = $this->db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . "scheduled_tasks`
		WHERE (id_site = " . SITE_ID . ")"
		)->all();
		
		foreach( $tasks as $task )
		{
			$dbarr = array(
				"id_site" 			=> $siteId,
				"time_offset" 		=> $task['time_offset'],
				"time_regularity" 	=> $task['time_regularity'],
				"time_unit" 		=> $task['time_unit'],
				"disabled" 			=> $task['disabled'],
				"task" 				=> $task['task']
			);

			$this->db->insert( "scheduled_tasks" )->set( $dbarr );
		}

		//Reload the settings
		$Admin->DeleteSettingsCacheSite( 'settings' );

		//Redirect to the sites page, for checking, php code etc...
		@header('Location: ' . $Admin->GetUrl( 'sites' ) );
		exit;
	}
}