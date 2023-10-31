<?php defined('TOKICMS') or die('Hacking attempt...');

class LanguageSettings extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-languages' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'lang-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'language' ) )
			return;
		
			// Verify that we have a language key
		if ( !isset( $_POST['langID'] ) || empty( $_POST['langID'] ) )
			return;

		$langID = (int) $_POST['langID'];
		
		//Get the lang data from POST values
		$lang = $_POST['lang'];

		//Update the language data
		$dbarr = array(
			"title" 	=> $lang['title'],
			"locale" 	=> $lang['locale'],
			"direction" => $lang['direction']
        );

		$this->db->update( 'languages' )->where( 'id', $langID )->set( $dbarr );
		
		//Update the language settings
		$dbarr = array(
			"date_format" 	=> $lang['date_format'],
			"time_format" 	=> $lang['time_format']
		);

		$this->db->update( 'languages_config' )->where( 'id_lang', $langID )->set( $dbarr );

		//Update the rest of the settings
		$Admin->UpdateSettings( $_POST['settings'] );
		
		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}