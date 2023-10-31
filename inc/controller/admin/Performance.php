<?php defined('TOKICMS') or die('Hacking attempt...');

class Performance extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'performance-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'performance' ) )
			return;
		
		//Get the needed POST values
		$settings = $_POST['settings'];
		
		$site = ( isset( $_POST['site'] ) ? $_POST['site'] : null );
		
		$this->db->update( "sites" )->where( 'id', $Admin->GetSite() )->set( "enable_registration", ( isset( $site['enable_registration'] ) ? "true" : "false" ) );
		
		$headerCode = $footerCode = array();
		
		if ( !empty( $_POST['header_code'] ) && is_array( $_POST['header_code'] ) )
		{
			foreach ( $_POST['header_code'] as $_id => $code )
			{
				$headerCode[$_id] = array(
							'language' => ( isset( $code['language'] ) ? (int) $code['language'] : 0 ),
							'code' => trim( htmlentities( $code['code'] ) )
				);
			}
		}
		
		if ( !empty( $_POST['footer_code'] ) && is_array( $_POST['footer_code'] ) )
		{
			foreach ( $_POST['footer_code'] as $_id => $code )
			{
				$footerCode[$_id] = array(
							'language' => ( isset( $code['language'] ) ? (int) $code['language'] : 0 ),
							'code' => trim( htmlentities( $code['code'] ) )
				);
			}
		}
		
		$s = array(
			'only_on_pc' => ( isset( $_POST['instantpage']['only_on_pc'] ) ? true : false ),
			'delay_time' => ( isset( $_POST['instantpage']['delay'] ) ? (int) $_POST['instantpage']['delay'] : 65 ),
		);

		$settingsArray = array(
			'blank_icon' => ( isset( $settings['blank_icon'] ) ? "true" : "false" ),
			'enable_registration' => ( isset( $settings['enable_registration'] ) ? "true" : "false" ),
			'enable_preloading' => ( isset( $settings['enable_preloading'] ) ? "true" : "false" ),
			'enable_cache' => ( isset( $settings['enable_cache'] ) ? "true" : "false" ),
			'cache_all_visitors' => ( isset( $settings['cache_all_visitors'] ) ? "true" : "false" ),
			'cache_type' => ( isset( $settings['cache_type'] ) ? Sanitize ( $settings['cache_type'], false, false ) : 'normal' ),
			'enable_lazyloader' => ( isset( $settings['enable_lazyloader'] ) ? "true" : "false" ),
			'enable_instantpage' => ( isset( $settings['enable_instantpage'] ) ? "true" : "false" ),
			'cache_time' => ( isset( $settings['cache_time'] ) ? (int) $settings['cache_time'] : 86400 ),
			'instantpage_settings' => json_encode( $s ),
			'header_code' => json_encode( $headerCode, JSON_UNESCAPED_UNICODE ),
			'footer_code' => json_encode( $footerCode, JSON_UNESCAPED_UNICODE ),
		);

		$Admin->UpdateSettings( $settingsArray );

		$Admin->DeleteSettingsCacheSite( 'settings' );

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}