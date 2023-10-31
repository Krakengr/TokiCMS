<?php defined('TOKICMS') or die('Hacking attempt...');

class MaintenanceSettings extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'maintenance-settings' ) . ' | ' . $Admin->SiteName() );
	
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'maintenance-settings' ) )
			return;
		
		//$settings = $Admin->Settings()::Get();
		
		$logSettings = array(
			'disable_javascript_call_scheduled_tasks' => ( isset( $_POST['disable_javascript_call_scheduled_tasks'] ) ? true : false ),
					
			'enable_error_log' => ( isset( $_POST['enable_error_log'] ) ? true : false ),

			'include_database_query' => ( isset( $_POST['include_database_query'] ) ? true : false ),

			'enable_redirection_log' => ( isset( $_POST['enable_redirection_log'] ) ? true : false ),

			'enable_moderation_log' => ( isset( $_POST['enable_moderation_log'] ) ? true : false ),

			'enable_administration_log' => ( isset( $_POST['enable_administration_log'] ) ? true : false ),

			'enable_profile_edits_log' => ( isset( $_POST['enable_profile_edits_log'] ) ? true : false ),

			'enable_not_found_log' => ( isset( $_POST['enable_not_found_log'] ) ? true : false ),
					
			'enable_bot_error_log' => ( isset( $_POST['enable_bot_error_log'] ) ? true : false ),
					
			'enable_pruning' => ( isset( $_POST['enable_pruning'] ) ? true : false ),
					
			'automatically_mark_boards_read' => ( isset( $_POST['automatically_mark_boards_read'] ) ? (int) $_POST['automatically_mark_boards_read'] : 0 ),
					
			'automatically_purge_board_information' => ( isset( $_POST['automatically_purge_board_information'] ) ? (int) $_POST['automatically_purge_board_information'] : 0 ),
					
			'maximum_users_to_process' => ( isset( $_POST['maximum_users_to_process'] ) ? (int) $_POST['maximum_users_to_process'] : 0 ),
					
			'remove_error_log_entries' => ( isset( $_POST['remove_error_log_entries'] ) ? (int) $_POST['remove_error_log_entries'] : 0 ),
					
			'remove_moderation_log_entries' => ( isset( $_POST['remove_moderation_log_entries'] ) ? (int) $_POST['remove_moderation_log_entries'] : 0 ),
					
			'remove_ban_hit_log_entries' => ( isset( $_POST['remove_ban_hit_log_entries'] ) ? (int) $_POST['remove_ban_hit_log_entries'] : 0 ),
					
			'remove_scheduled_task_log_entries' => ( isset( $_POST['remove_scheduled_task_log_entries'] ) ? (int) $_POST['remove_scheduled_task_log_entries'] : 0 ),
					
			'remove_redirection_log_entries' => ( isset( $_POST['remove_redirection_log_entries'] ) ? (int) $_POST['remove_redirection_log_entries'] : 0 ),
			
			
			'delete_published_posts' => ( isset( $_POST['delete_published_posts'] ) ? (int) $_POST['delete_published_posts'] : 0 ),
			
			'delete_draft_posts' => ( isset( $_POST['delete_draft_posts'] ) ? (int) $_POST['delete_draft_posts'] : 0 ),
			
			'delete_auto_draft_posts' => ( isset( $_POST['delete_auto_draft_posts'] ) ? (int) $_POST['delete_auto_draft_posts'] : 0 ),
			
			'delete_published_comments' => ( isset( $_POST['delete_published_comments'] ) ? (int) $_POST['delete_published_comments'] : 0 ),
			
			'delete_other_comments' => ( isset( $_POST['delete_other_comments'] ) ? (int) $_POST['delete_other_comments'] : 0 ),
			
			'delete_inbox_emails' => ( isset( $_POST['delete_inbox_emails'] ) ? (int) $_POST['delete_inbox_emails'] : 0 ),
			
			'delete_other_emails' => ( isset( $_POST['delete_other_emails'] ) ? (int) $_POST['delete_other_emails'] : 0 ),
			
			'hide_prices' => ( isset( $_POST['hide_prices'] ) ? (int) $_POST['hide_prices'] : 0 ),
			
			'delete_prices' => ( isset( $_POST['delete_prices'] ) ? (int) $_POST['delete_prices'] : 0 )
		);
		
		$settingsArray = array(
			'log_settings' => json_encode( $logSettings, JSON_UNESCAPED_UNICODE )
		);

		$Admin->UpdateSettings( $settingsArray );

		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}