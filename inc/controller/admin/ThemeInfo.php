<?php defined('TOKICMS') or die('Hacking attempt...');

class ThemeInfo extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

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
		
		$themes = LoadThemes( 'normal', false );
		
		$this->setVariable( 'Themes', $themes );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'themes' ) )
			return;
		
		//Don't continue if we already have this theme as default
		if ( $_POST['default_theme'] == $Admin->Settings()::Get()['theme'] )
		{
			//Redirect to the same page
			@header('Location: ' . $Admin->GetUrl( null, null, true ) );
			exit;
		}

		$settingsArray = array( 'theme' => Sanitize ( $_POST['default_theme'], false, false ) );

		//Update the rest of the settings
		$Admin->UpdateSettings( $settingsArray );
		
		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}