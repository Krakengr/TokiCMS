<?php defined('TOKICMS') or die('Hacking attempt...');

class PostSettings extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'post-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'post-settings' ) )
			return;
		
		$L = $this->lang;
		
		include ( ARRAYS_ROOT . 'generic-arrays.php');

		//Get the needed POST values
		$settings = $_POST['settings'];
		$drafts = $_POST['drafts'];
		
		$htmlEditor = ( isset( $settings['html_editor'] ) ? Sanitize ( $settings['html_editor'], false ) : 'simplemde' );
		
		$editorData = array();
		
		if ( isset( $_POST['editor'] ) && !empty( $_POST['editor'] ) )
		{
			foreach ( $_POST['editor'] as $e => $v )
			{
				$editorData[$e] = html_entity_decode( $v );
			}
		}
		
		//If we have changed the editor, load its default values
		if ( $htmlEditor != $Admin->Settings()::Get()['html_editor'] )
		{
			$editorData = null;
			
			$editorData = ( isset( $postEditorOptions[$htmlEditor] ) ? $postEditorOptions[$htmlEditor]['default-values'] : $editorData );
		}

		//We are going to create a new array, so there is no need to get the settings from the DB
		$s = array(
			'hide_comments' 		=> ( isset( $settings['hide_comments'] ) ? true : false ),
			'allow' 				=> ( ( isset( $settings['allow'] ) && is_array( $settings['allow'] ) ) ? array_values( $settings['allow'] ) : array() ),
			'comments_limit' 		=> ( isset( $settings['comments_limit'] ) ? (int) $settings['comments_limit'] : 0 ),
			'auto_comments_close' 	=> ( isset( $settings['auto_comments_close'] ) ? (int) $settings['auto_comments_close'] : 0 ),
			'sort_by' 				=> ( isset( $settings['sort_by'] ) ? Sanitize ( $settings['sort_by'], false ) : null ),
			'redirect_with_message' => ( isset( $settings['redirect_with_message'] ) ? "true" : "false" ),
		);
		
		$autoSave = ( ( isset( $drafts['auto_save'] ) && is_numeric( $drafts['auto_save'] ) && ( $drafts['auto_save'] >= 30 ) ) ? (int) $drafts['auto_save'] : 30 );
		
		//Drafts Array
		$d = array(
			'enable_auto_drafts' 	=> ( isset( $drafts['enable_auto_drafts'] ) ? true : false ),
			'enable_post_drafts' 	=> ( isset( $drafts['enable_post_drafts'] ) ? true : false ),
			'auto_save' 			=> $autoSave,
			'keep_auto_saved' 		=> ( isset( $drafts['keep_auto_saved'] ) ? (int) $drafts['keep_auto_saved'] : 10 )
		);
		
		$_settings = $Admin->Settings()::Get();
		
		$frontPage = ( $Admin->IsDefaultLang() ? $settings['front_page'] : $_settings['front_page'] );
		$frontStaticPage = ( $Admin->IsDefaultLang() ? $settings['front_static_page'] : $_settings['front_static_page'] );
			
		$settingsArray = array(
			'article_limit' 		=> (int) $settings['article_limit'],
			'front_page' 			=> SafeFormField( $frontPage, true ),
			'front_static_page' 	=> ( ( isset( $settings['front_page'] ) && ( $settings['front_page'] === 'static-page' ) ) ? (int) $frontStaticPage : '' ),
			'enable_comments' 		=> ( isset( $settings['enable_comments'] ) ? "true" : "false" ),
			'mail_on_comments' 		=> ( isset( $settings['mail_on_comments'] ) ? "true" : "false" ),
			'comment_repost_timer' 	=> ( isset( $settings['comment_repost_timer'] ) ? (int) $settings['comment_repost_timer'] : 0 ),
			'share_images_sites' 	=> ( isset( $settings['share_images_sites'] ) ? "true" : "false" ),
			'share_tags_langs' 		=> ( isset( $settings['share_tags_langs'] ) ? "true" : "false" ),
			'html_editor' 			=> $htmlEditor,
			'editor_data' 			=> json_encode( $editorData ),
			'drafts_data' 			=> json_encode( $d ),
			'comments_data' 		=> json_encode( $s )
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