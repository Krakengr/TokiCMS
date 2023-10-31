<?php defined('TOKICMS') or die('Hacking attempt...');

class CookieConsent extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) || !$Admin->Settings()::IsTrue( 'enable_cookie_concent' ) )
		{
			Router::SetNotFound();
			return;
		}

		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'cookie-consent-settings' ) . ' | ' . $Admin->SiteName() );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify the token
		if ( !verify_token( 'cookie-consent' ) )
			return;
		
		$arr = array(
			'theme' => Sanitize( $_POST['theme'], false ),
			'consent_message' => Sanitize( $_POST['consent_message'], false ),
			'consent_url' => Sanitize( $_POST['consent_url'], false ),
			'consent_more_txt' => Sanitize( $_POST['consent_more_txt'], false ),
			'consent_dismiss' => Sanitize( $_POST['consent_dismiss'], false )
		);
		
		$this->db->update( "languages_config" )->where( 'id_lang', $Admin->GetLang() )->set( "cookie_data", json_encode( $arr, JSON_UNESCAPED_UNICODE ) );
		
		$Admin->EmptyCaches();
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( 'cookie-consent' ) );
		exit;
	}
}