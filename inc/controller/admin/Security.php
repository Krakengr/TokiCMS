<?php defined('TOKICMS') or die('Hacking attempt...');

class Security extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'security-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'security' ) )
			return;
		
		//Get the needed POST values
		$settings = $_POST['settings'];
	
		$settingsArray = array(
			'num_login_retries' => (int) $settings['num_login_retries'],
			'num_login_lockout_time' => $settings['num_login_lockout_time'],
			'enable_recaptcha' => ( isset( $settings['enable_recaptcha'] ) ? $settings['enable_recaptcha'] : "false" ),
			'notify_the_user_about_remaining_retries' => ( isset( $settings['notify_the_user_about_remaining_retries'] ) ? "true" : "false" ),
			'notify_the_user_failed_login' => ( isset( $settings['notify_the_user_failed_login'] ) ? "true" : "false" ),
			'put_spam_in_spam_folder' => ( isset( $settings['put_spam_in_spam_folder'] ) ? "true" : "false" ),
			'enable_honeypot' => ( isset( $settings['enable_honeypot'] ) ? "true" : "false" ),
			'add_allow_origin_tag' => ( isset( $settings['add_allow_origin_tag'] ) ? "true" : "false" ),
			'recaptcha_site_key' => Sanitize ( $settings['recaptcha_site_key'], false, false ),
			'recaptcha_secret_key' => Sanitize ( $settings['recaptcha_secret_key'], false, false ),
			'hide_captcha_logged_in_users' => ( isset( $settings['hide_captcha_logged_in_users'] ) ? "true" : "false" ),
			'show_captcha_in_forms' => Sanitize ( $settings['show_captcha_in_forms'], false, false ),
			'referrer_policy' => Sanitize ( $settings['referrer_policy'], false, false ),
		);

		$Admin->UpdateSettings( $settingsArray );
		
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}