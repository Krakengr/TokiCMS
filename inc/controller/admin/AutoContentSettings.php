<?php defined('TOKICMS') or die('Hacking attempt...');

class AutoContentSettings extends Controller {
	
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

		if (
			( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-auto-content' ) ) || !$Admin->Settings()::IsTrue( 'enable_autoblog' )
		)
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'auto-content-settings' ) . ' | ' . $Admin->SiteName() );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'auto-content-settings' ) )
			return;
	
		$data = $_POST['autocontent'];

		//We are going to create a new array, so there is no need to get the settings from the DB
		$s = array(
			'enable_cache' => ( isset( $data['enable_cache'] ) ? true : false )
		);

		$settingsArray = array( 'auto_content_data' => json_encode( $s, JSON_UNESCAPED_UNICODE ) );
	
		//Update the settings
		$Admin->UpdateSettings( $settingsArray );
		
		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}