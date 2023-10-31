<?php defined('TOKICMS') or die('Hacking attempt...');

class AddLang extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $L;
		
		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-languages' ) || !$Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'add-new-language' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !verify_token( 'add-lang' ) || empty( $_POST['new-lang'] ) )
			return;
		
		//Load the $langs array
		include ( ARRAYS_ROOT . 'generic-arrays.php');
		
		$lang = SafeFormField( $_POST['new-lang'], true );
	
		//Do we have the lang's data?
		if ( !isset( $langs[$lang] ) )
		{
			$Admin->SetAdminMessage( __( 'language-error' ) );
			return;
		}

		$lang = $langs[$lang];
		
		$code = $lang['code'];
		
		$icon = ( ( isset( $lang['icon'] ) && !empty( $lang['icon'] ) ) ? $lang['icon'] : $lang['lang'] . '.png' );
		
		//Check if we already have this language
		$data = $this->db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "languages`
		WHERE (id_site = " . $Admin->GetSite() . ") AND (code = :code)",
		array( $code => ':code' )
		)->single();
		
		if ( $data )
		{
			$Admin->SetAdminMessage( __( 'language-add-error' ) );
			return;
		}
		
		$dbarr = array(
			"id_site" 		=> $Admin->GetSite(),
			"code"			=> $code,
			"title"			=> $lang['name'],
			"locale"		=> $lang['locale'],
			"direction"		=> $_POST['text-direction'],
			"flagicon"		=> $icon,
			"lang_order"	=> (int) $_POST['order']
		);

		$id = $this->db->insert( 'languages' )->set( $dbarr, null, true );

		if ( !$id )
		{
			$Admin->SetAdminMessage( __( 'language-add-error' ) );
			return;
		}
		
		$dbarr = array(
			"id_lang" 		=> $id,
			"site_name"		=> htmlspecialchars( $Admin->SiteName() ),
			"date_format"	=> 'F j, Y',
			"time_format"	=> 'h:i:s',
			"footer_text"	=> '{{copyright}} {{current-year}} <a href=\"{{site-url}}\">{{site-title}}</a> ~ {{powered-by-toki-cms}}'
		);

		$this->db->insert( 'languages_config' )->set( $dbarr );
	
		//Add a default category for this language
		$dbarr = array(
			"id_lang" 		=> $id,
			"id_site" 		=> $Admin->GetSite(),
			"name"			=> 'Uncategorized',
			"sef"			=> 'uncategorized' . ( $Admin->Settings()::IsTrue( 'share_slugs' ) ? '' : '-' . $lang['code'] ),
			"is_default"	=> 1
		);

		$this->db->insert( 'categories' )->set( $dbarr );
		
		//Check if we want to translate this language
		if ( isset( $_POST['copy-translate'] ) && !empty( $_POST['copy-translate'] ) )
		{
			$file = LANG_ROOT . $code . '.json';
			
			//For now, English is the default language, maybe this will be changed
			$orFile = LANG_ROOT . 'en.json';
			
			//Check if we already have this file
			if ( !file_exists( $file ) )
			{
				$langArr = json_decode( file_get_contents( $orFile ), TRUE );
				
				if ( !empty( $langArr ) )
				{
					unset( $langArr['language-data'] );
					
					//array_multisort( $langArr, SORT_ASC, SORT_NATURAL );
					
					$langArrTemp = array();
					
					$langArrTemp['language-data'] = array(
						'native' => $lang['name'],
						'english-name' => $lang['name'],
						'locale' => $lang['locale'],
						'last-update' => date( 'Y/m/d', time() ),
						'authors' => array( SITE_URL, $Admin->Settings()::Site()['title'] )
					);
					
					$_Arr = array_merge( $langArrTemp, $langArr );
					
					$_Arr = json_encode( $_Arr, JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT );
					
					file_put_contents( $file, $_Arr, LOCK_EX );
				}
			}
			
			//Now, let's check if we already have a translation data
			$data = $this->db->from( 
			null, 
			"SELECT id
			FROM `" . DB_PREFIX . "translations`
			WHERE (id_site = " . $Admin->GetSite() . ") AND (lang_code = :code)",
			array( $code => ':code' )
			)->single();

			if ( !$data )
			{
				$dbarr = array(
					"id_lang" 		=> $Admin->DefaultLang()['id'],
					"id_site" 		=> $Admin->GetSite(),
					"lang_key"		=> $_POST['new-lang'],
					"trans_type"	=> 'lang',
					"lang_code"		=> $code
				);

				$this->db->insert( 'translations' )->set( $dbarr );
			}
			
		}

		$Admin->EmptyCaches();

		//Redirect to the lang's edit page
		@header('Location: ' . ADMIN_URI . 'edit-lang' . PS . 'id' . PS . $id . PS );
		exit;
	}
	
	// Comparison function
	//function date_compare($element1, $element2) {
	//	return $element1 - $element2;
	//} 

}