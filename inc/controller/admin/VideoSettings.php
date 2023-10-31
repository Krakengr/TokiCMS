<?php defined('TOKICMS') or die('Hacking attempt...');

class VideoSettings extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-seo' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'video-settings' ) )
			return;
	
		//Get the needed POST values
		$settings = ( isset( $_POST['settings'] ) ? $_POST['settings'] : null );

		//We are going to create a new array, so there is no need to get the settings from the DB
		$s = array(
				'enable_videos_sitemap' => ( isset( $settings['enable_videos_sitemap'] ) ? true : false ),
				'enable_categories_sitemap' => ( isset( $settings['enable_categories_sitemap'] ) ? true : false ),
				'enable_tags_sitemap' => ( isset( $settings['enable_tags_sitemap'] ) ? true : false ),
				'enable_indexation_videos' => ( isset( $settings['enable_indexation_videos'] ) ? true : false )
		);
	
		$settingsArray['video_data'] = json_encode( $s );

		//Update the settings
		$Admin->UpdateSettings( $settingsArray );

		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}