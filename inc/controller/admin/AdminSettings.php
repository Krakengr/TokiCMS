<?php defined('TOKICMS') or die('Hacking attempt...');

class AdminSettings extends Controller {
	
	private $siteId;
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'general-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'settings' ) )
			return;
		
		$site = ( isset( $_POST['site'] ) ? $_POST['site'] : null );
		$lang = $_POST['lang'];
		
		$maint = array();
		
		if ( isset( $_POST['data'] ) && is_array( $_POST['data'] ) && !empty( $_POST['data'] ) )
		{
			foreach ( $_POST['data'] as $i => $d )
			{
				$maint[$i] = SafeFormField( $d, true );
			}
		}

		$maint['background_image'] = $_POST['siteBackgFile'];
		
		$this->siteId = $Admin->GetSite();
		
		//If this is a child site, let's check if we want to share images
		if ( !$Admin->IsDefaultSite() )
		{
			$_site = $Admin->Settings()::Site();
			
			$share = ( ( isset( $_site['share_data'] ) && !empty( $_site['share_data'] ) ) ? Json( $_site['share_data'] ) : array() );
			
			if ( isset( $share['sync_uploads'] ) && $share['sync_uploads'] )
			{
				//Check the maintenance image
				if ( !empty( $_POST['siteBackgFile'] ) )
				{
					$tmp = $this->db->from( 
					null, 
					"SELECT id_image, filename, added_time
					FROM `" . DB_PREFIX . "images`
					WHERE (id_site = " . $this->siteId . ") AND (img_type = 'maintenance') AND (img_status = 'full')"
					)->single();
					
					if ( $tmp )
					{
						$pingUrl = $_site['site_ping_url'] . '?token=' . $_site['site_secret'] . '&do=sync&type=image&url=' . urlencode( $_POST['siteBackgFile'] ) . '&time=' . $tmp['added_time'];
						
						$ping = PingSite( $pingUrl );
						
						if ( !empty( $ping ) && isset( $ping['message'] ) && ( $ping['message'] == 'Success' ) && !empty( $ping['url'] ) )
							$maint['background_image'] = $ping['url'];
					}
				}	
			}
		}
		
		$hosted = Json( $Admin->Settings()::Site()['hosted'] );

		$hosted[$Admin->LangKey()]['blog-' . $Admin->GetBlog()] = ( isset( $_POST['siteHost'] ) ? Sanitize ( $_POST['siteHost'], false ) : 'self' );

		//Update the site's settings
		$dbarr = array(
			"enable_multiblog" => ( isset( $site['enable_multiblog'] ) ? "true" : "false" ),
			"enable_multisite" => ( isset( $site['enable_multisite'] ) ? "true" : "false" ),
			"enable_multilang" => ( isset( $site['enable_multilang'] ) ? "true" : "false" ),
			"enable_login_maintenance" => ( isset( $site['enable_login_maintenance'] ) ? "true" : "false" ),
			"enable_maintenance" => ( isset( $site['enable_maintenance'] ) ? "true" : "false" ),
			"maintenance_data" => json_encode( $maint, JSON_UNESCAPED_UNICODE ),
			"hosted" => json_encode( $hosted )
        );

		$this->db->update( 'sites' )->where( 'id', $this->siteId )->set( $dbarr );

		//Update the site's name, if we are in the default language
		if ( $Admin->IsDefaultLang() )
		{
			$this->db->update( 'sites' )->where( 'id', $this->siteId )->set( "title", SafeFormField( $lang['site_name'], true ) );
		}
		
		$social = ( ( isset( $_POST['social'] ) && is_array( $_POST['social'] ) ) ? $_POST['social'] : array() );
		
		$shortname = '';
		
		if ( $lang['comment_sys'] == 'disqus' )
		{
			$shortname = SafeFormField( $lang['disqus_shortname'], true );
		}
		
		elseif ( $lang['comment_sys'] == 'intensedebate' )
		{
			$shortname = SafeFormField( $lang['intensedebate_shortname'], true );
		}
		
		elseif ( $lang['comment_sys'] == 'fb-comments' )
		{
			$shortname = SafeFormField( $lang['facebook_shortname'], true ); 
		}
		
		//Update the langs' settings
		$dbarr = array(
			"site_name" 			=> SafeFormField( $lang['site_name'], 			true ),
			"site_description" 		=> SafeFormField( $lang['site_description'], 	true ),
			"site_slogan" 			=> SafeFormField( $lang['site_slogan'], 		true ),
			"footer_text" 			=> SafeFormField( $lang['footer_text'], 		true ),
			"after_content_text" 	=> SafeFormField( $lang['after_content_text'], 	true ),
			"ext_comm_system" 		=> SafeFormField( $lang['comment_sys'], 		true ),
			"ext_comm_shortname" 	=> $shortname,
			"social" 				=> json_encode( $social )
        );

		$this->db->update( 'languages_config' )->where( 'id_lang', $Admin->GetLang() )->set( $dbarr );
		
		if ( !empty( $_POST['siteLogoFile'] ) && is_numeric( $_POST['siteLogoFile'] ) )
		{
			$arr = $this->SiteImg( $_POST['siteLogoFile'] );
		}
		else
			$arr = array();
		
		$apiSettings = Json( $Admin->Settings()::Get()['api_keys'] );
		
		$langKey = $Admin->LangKey();
		
		//Set the Api Keys
		$apiSettings[$langKey]['blog-' . $Admin->GetBlog()]['blogger'] = array(
					'api' 			=> ( isset( $_POST['bloggerApi'] ) ? Sanitize ( $_POST['bloggerApi'], false ) : null ),
					'oath2' 		=> ( isset( $_POST['bloggerOath'] ) ? Sanitize ( $_POST['bloggerOath'], false ) : null ),
					'blog-id' 		=> ( isset( $_POST['bloggerBlogId'] ) ? Sanitize ( $_POST['bloggerBlogId'], false ) : null )
		);

		$apiSettings[$langKey]['blog-' . $Admin->GetBlog()]['wordpress'] = array(
					'client-id' 	=> ( isset( $_POST['wordpressClientApi'] ) ? Sanitize ( $_POST['wordpressClientApi'], false ) : null ),
					'client-secret' => ( isset( $_POST['wordpressClientSecret'] ) ? Sanitize ( $_POST['wordpressClientSecret'], false ) : null ),
					'blog-id' 		=> ( isset( $_POST['wpBlogId'] ) ? Sanitize ( $_POST['wpBlogId'], false ) : null )
		);

		$settingsArray = array(
				'site_image' => json_encode( $arr, JSON_UNESCAPED_UNICODE ),
				'api_keys' => json_encode( $apiSettings, JSON_UNESCAPED_UNICODE )
		);

		$Admin->UpdateSettings( $settingsArray );

		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
	
	//Generate site's image(s)
	private function SiteImg( $id )
	{
		global $Admin;
		
		$arr = array();
		
		$share = $Admin->ImageUpladDir( $this->siteId );
		
		$img = $this->db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . "images`
		WHERE (id_image = " . $id . ")"
		)->single();
		
		if ( $img )
		{
			$arr['default'] = array(
							'filename' 	=> $img['filename'],
							'url' 		=> FolderUrlByDate( $img['added_time'], $share['html'] ) . $img['filename'],
							'width' 	=> $img['width'],
							'height' 	=> $img['height'],
							'id' 	 	=> $img['id_image'],
							'ext' 		=> $img['file_ext'],
							'type' 		=> $img['mime_type']
			);
			
			//Get the site's images
			$imgs = $this->db->from( 
			null, 
			"SELECT id_image, filename, width, height
			FROM `" . DB_PREFIX . "images`
			WHERE (id_parent = " . $img['id_image'] . ") AND (img_status = 'cropped')"
			)->all();

			if ( $imgs )
			{
				foreach( $imgs as $im )
				{
					$arr[$im['width']] = array(
									'filename' 	=> $im['filename'],
									'url' 		=> FolderUrlByDate( $img['added_time'], $share['html'] ) . $im['filename'],
									'width' 	=> $im['width'],
									'height' 	=> $im['height'],
									'id' 	 	=> $im['id_image'],
									'width' 	=> $im['width']
			
					);
				}
			}
		}
		
		return $arr;
	}
}