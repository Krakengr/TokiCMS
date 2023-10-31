<?php defined('TOKICMS') or die('Hacking attempt...');

class SocialMediaAutoPublishSettings extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'social-media-auto-publish-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'social-media-auto-publish-settings' ) )
			return;
		
		$temp = Json( $Admin->Settings()::Get()['auto_social_data'] );
		
		$lang = 'lang-' . $Admin->GetLang();
		
		if ( empty( $temp ) )
		{
			$temp[$lang] = $temp[$lang]['social'] = $temp[$lang]['settings'] = array();
		}
		
		if ( isset( $_POST['facebook'] ) && !empty( $_POST['facebook'] ) )
		{
			$data = $_POST['facebook'];
			
			$temp[$lang]['social']['facebook'] = array(
				'enable' => ( isset( $data['enable_auto_publish_post_to_facebook'] ) ? true : false ),
				'app_id' => Sanitize ( $data['app_id'], false ),
				'app_secret' => Sanitize ( $data['app_secret'], false ),
				'format' => Sanitize ( $data['format'], false )
			);
		}
		
		if ( isset( $_POST['settings'] ) && !empty( $_POST['settings'] ) )
		{
			$data = $_POST['settings'];
			
			$temp[$lang]['settings'] = array(
				'auto_publish_method' => Sanitize ( $data['auto_publish_method'], false )
			);
		}
		
		$settingsArray = array( 
				'auto_social_data' => json_encode( $temp, JSON_UNESCAPED_UNICODE )
		);
		
		$Admin->UpdateSettings( $settingsArray );

		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}