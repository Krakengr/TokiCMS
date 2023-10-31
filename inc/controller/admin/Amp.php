<?php defined('TOKICMS') or die('Hacking attempt...');

class Amp extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) || !$Admin->Settings()::IsTrue( 'enable_amp' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'amp-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'amp-settings' ) )
			return;
		
		//Get the needed POST values
		$data = $_POST['amp'];

		//We are going to create a new array, so there is no need to get the settings from the DB
		$s = array(
				'redirect_mobile_visitors' => ( isset( $data['redirect_mobile_visitors'] ) ? true : false ),
				'theme' => ( isset( $data['theme'] ) ? Sanitize ( $data['theme'], false, false ) : '' ),
				'content_types' => ( ( isset( $data['content_types'] ) && is_array( $data['content_types'] ) ) ? array_values( $data['content_types'] ) : array() ),
				'archive_types' => ( ( isset( $data['archive_types'] ) && is_array( $data['archive_types'] ) ) ? array_values( $data['archive_types'] ) : array() ),
				'enable_auto_ads' => ( isset( $data['enable_auto_ads'] ) ? true : false ),
				'codes' => array(
							'google_ad_client_code' => ( isset( $data['google_ad_client_code'] ) ? Sanitize ( $data['google_ad_client_code'], false, false ) : '' ),
							'google_ad_adslot_code' => ( isset( $data['google_ad_adslot_code'] ) ? Sanitize ( $data['google_ad_adslot_code'], false, false ) : '' ),
							'google_analytics_code' => ( isset( $data['google_analytics_code'] ) ? Sanitize ( $data['google_analytics_code'], false, false ) : '' )
				)
		);
		
		$settingsArray['amp_data'] = json_encode( $s );

		//Update the rest of the settings
		$Admin->UpdateSettings( $settingsArray );
		
		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}