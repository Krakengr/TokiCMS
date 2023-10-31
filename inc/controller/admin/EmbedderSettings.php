<?php defined('TOKICMS') or die('Hacking attempt...');

class EmbedderSettings extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'auto-embedder-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'media-embedder' ) )
			return;
		
		//Get the needed POST values
		$settings = $_POST['settings'];
		$embedder = $_POST['embedder'];
	
		//We are going to create a new array, so there is no need to get the settings from the DB
		$s = array(
			'default_video_player_height' => ( isset( $embedder['default_video_player_height'] ) ? Sanitize ( $embedder['default_video_player_height'], false, false ) : 0 ),
			'default_video_player_width' => ( isset( $embedder['default_video_player_width'] ) ? Sanitize ( $embedder['default_video_player_width'], false, false ) : 0 ),
			'default_video_player_height_amp' => ( isset( $embedder['default_video_player_height_amp'] ) ? Sanitize ( $embedder['default_video_player_height_amp'], false, false ) : 0 ),
			'default_video_player_width_amp' => ( isset( $embedder['default_video_player_width_amp'] ) ? Sanitize ( $embedder['default_video_player_width_amp'], false, false ) : 0 ),
			'maximum_number_of_embeds' => ( isset( $embedder['maximum_number_of_embeds'] ) ? Sanitize ( $embedder['maximum_number_of_embeds'], false, false ) : 0 ),
			'disable_embeding_in_mobile' => ( isset( $embedder['disable_embeding_in_mobile'] ) ? "true" : "false" ),
			'enable_auto_embed_text_links' => ( isset( $embedder['enable_auto_embed_text_links'] ) ? "true" : "false" ),
			'disable_embedding_in_comments' => ( isset( $embedder['disable_embedding_in_comments'] ) ? "true" : "false" ),
			'show_original_link' => ( isset( $embedder['show_original_link'] ) ? "true" : "false" ),
			'sources' => ( ( isset( $embedder['sources'] ) && is_array( $embedder['sources'] ) ) ? array_values( $embedder['sources'] ) : array() ),
		);

		//Set the settings Array
		$settingsArray = array(
			'enable_media_embedder' => ( isset( $settings['enable_media_embedder'] ) ? "true" : "false" ),
			'embedder_data' => json_encode( $s )
		);

		//Update the rest of the settings
		$Admin->UpdateSettings( $settingsArray );
		
		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}