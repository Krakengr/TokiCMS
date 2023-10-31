<?php defined('TOKICMS') or die('Hacking attempt...');

class Tools extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'tools' ) . ' | ' . $Admin->SiteName() );
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'tools' ) )
			return;
		
		//Get the needed POST values
		$settings = $_POST['settings'];
	
		//Update Settings
		$settingsArray = array(
			'enable_amp' 					=> ( isset( $settings['enable_amp'] ) ? "true" : "false" ),
			'enable_seo' 					=> ( isset( $settings['enable_seo'] ) ? "true" : "false" ),
			'enable_autoblog' 				=> ( isset( $settings['enable_autoblog'] ) ? "true" : "false" ),
			'enable_store' 					=> ( isset( $settings['enable_store'] ) ? "true" : "false" ),
			'enable_forum' 					=> ( isset( $settings['enable_forum'] ) ? "true" : "false" ),
			'enable_redirect' 				=> ( isset( $settings['enable_redirect'] ) ? "true" : "false" ),
			'enable_post_attributes' 		=> ( isset( $settings['enable_post_attributes'] ) ? "true" : "false" ),
			'enable_social_auto_publish'	=> ( isset( $settings['enable_social_auto_publish'] ) ? "true" : "false" ),
			'enable_marketplace' 			=> ( isset( $settings['enable_marketplace'] ) ? "true" : "false" ),
			'enable_price_comparison' 		=> ( isset( $settings['enable_price_comparison'] ) ? "true" : "false" ),
			'enable_deals' 					=> ( isset( $settings['enable_deals'] ) ? "true" : "false" ),
			'enable_ads' 					=> ( isset( $settings['enable_ads'] ) ? "true" : "false" ),
			'enable_auto_translate' 		=> ( isset( $settings['enable_auto_translate'] ) ? "true" : "false" ),
			'enable_forms' 					=> ( isset( $settings['enable_forms'] ) ? "true" : "false" ),
			'enable_link_manager' 			=> ( isset( $settings['enable_link_manager'] ) ? "true" : "false" ),
			'enable_api' 					=> ( isset( $settings['enable_api'] ) ? "true" : "false" ),
			'enable_galleries'	 			=> ( isset( $settings['enable_galleries'] ) ? "true" : "false" ),
			'enable_cookie_concent' 		=> ( isset( $settings['enable_cookie_concent'] ) ? "true" : "false" )
		);

		$dis = ( isset( $settings['enable_link_manager'] ) ? 0 : 1 );
		
		//Enable the related scheduled task
		$this->db->update( "scheduled_tasks" )->where( "task", "broken-link-check" )->where( "id_site", $Admin->GetSite() )->set( "disabled", $dis );

		//Update the rest of the settings
		$Admin->UpdateSettings( $settingsArray );

		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}