<?php defined('TOKICMS') or die('Hacking attempt...');

class RedirectionSettings extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-redirections' ) || !$Admin->Settings()::IsTrue( 'enable_redirect' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'redirection-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'redirection-settings' ) )
			return;
		
		//Get the needed POST values
		$data = $_POST['settings'];

		//We are going to create a new array, so there is no need to get the settings from the DB
		$s = array(
				'monitor_permalink_changes' => ( isset( $data['monitor_permalink_changes'] ) ? true : false ),
				'keep_log_redirects_errors' => ( isset( $data['keep_log_redirects_errors'] ) ? true : false ),
				'case_insensitive_matches' => ( isset( $data['case_insensitive_matches'] ) ? true : false ),
				'ignore_trailing_slashes' => ( isset( $data['ignore_trailing_slashes'] ) ? true : false ),
				'keep_logs' => ( isset( $data['keep_logs'] ) ? (int) $data['keep_logs'] : 0 ),
				'ip_logging' => ( isset( $data['ip_logging'] ) ? Sanitize ( $data['ip_logging'], false, false ) : '' )
		);
		
		$settingsArray['redirection_data'] = json_encode( $s );

		//Update the rest of the settings
		$Admin->UpdateSettings( $settingsArray );
		
		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}