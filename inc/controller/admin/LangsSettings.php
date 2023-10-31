<?php defined('TOKICMS') or die('Hacking attempt...');

class LangsSettings extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-languages' ) || !$Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'lang-settings' ) )
			return;
		
		//Get the needed POST values
		$settings = $_POST['settings'];
	
		$settingsArray = array(
				'hide_default_lang_slug' => ( isset( $settings['hide_default_lang_slug'] ) ? "true" : "false" ),
				'language_set_from_code' => ( isset( $settings['language_set_from_code'] ) ? "true" : "false" ),
				'detect_browser_language' => ( isset( $settings['detect_browser_language'] ) ? "true" : "false" ),
				'share_slugs' => ( isset( $settings['share_slugs'] ) ? "true" : "false" ),
				'share_images_langs' => ( isset( $settings['share_images_langs'] ) ? "true" : "false" ),
				'translate_slugs' => ( isset( $settings['translate_slugs'] ) ? "true" : "false" )
		);
		
		$Admin->UpdateSettings( $settingsArray );
		
		EmptyCaches();
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}