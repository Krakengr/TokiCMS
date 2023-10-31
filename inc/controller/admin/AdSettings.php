<?php defined('TOKICMS') or die('Hacking attempt...');

class AdSettings extends Controller {
	
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

		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-ads' ) ) || !$Admin->Settings()::IsTrue( 'enable_ads' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'ad-settings' ) . ' | ' . $Admin->SiteName() );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'ad-settings' ) )
			return;
	
		//Get the needed POST values
		$settings = ( isset( $_POST['settings'] ) ? $_POST['settings'] : null );

		//We are going to create a new array, so there is no need to get the settings from the DB
		$s = array(
				'enable_ad_txt' 		=> ( isset( $settings['enable_ad_txt'] ) ? true : false ),
				'enable_app_ad_txt' 	=> ( isset( $settings['enable_app_ad_txt'] ) ? true : false ),
				'ad_txt_content' 		=> ( isset( $settings['ad_txt_content'] ) ? htmlentities( $settings['ad_txt_content'] ) : '' ),
				'app_ad_txt_content' 	=> ( isset( $settings['app_ad_txt_content'] ) ? htmlentities( $settings['app_ad_txt_content'] ) : '' ),
				'rotate_ads' 			=> ( isset( $settings['rotate_ads'] ) ? true : false ),
				'ad_notice' 			=> array(),
				'hide_ads_bot' 			=> ( isset( $settings['hide_ads_bot'] ) ? true : false )
		);

		$settingsArray['ads_data'] = json_encode( $s );

		//Update the settings
		$Admin->UpdateSettings( $settingsArray );

		//Delete Caches
		$Admin->EmptyCaches();

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}