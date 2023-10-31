<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Maintenance Settings Form
#
#####################################################

$L = $this->lang;

$settings = $this->adminSettings::Get();
$logSettings = Json( $settings['log_settings'] );

$cronUrl = CRON_URL;

if ( $this->isChildSite )
{
	$cronUrl .= '&site=' . $this->siteID;
}

#####################################################
#
# Form array
#
#####################################################
$form = array
(
	'task-log-settings' => array
	(
		'title' => $L['task-log'],
		'data' => array(

			'disable-javascript-based-scheduled-tasks' => array( 
				'title' => null, 'data' => array
				(
					'disable-javascript'=>array('label'=>$L['disable-javascript-based-scheduled-tasks'], 'type'=>'checkbox', 'name' => 'disable_javascript_call_scheduled_tasks', 'value' => ( isset( $logSettings['disable_javascript_call_scheduled_tasks'] ) ? $logSettings['disable_javascript_call_scheduled_tasks'] : false ), 'tip'=>sprintf( $L['disable-javascript-based-scheduled-tasks-tip'], $cronUrl ) )
				)
			)
		)
	),
	
	'error-log' => array
	(
		'title' => __( 'error-log' ),
		'data' => array
		(
			'settings' => array( 
				'title' => null, 'tip' => $L['error-log-tip'], 'data' => array
				(
					'error-logging'=>array('label'=>$L['enable-error-logging'], 'name' => 'enable_error_log', 'type'=>'checkbox', 'value'=>( isset( $logSettings['enable_error_log'] ) ? $logSettings['enable_error_log'] : false ), 'tip'=>$L['enable-error-logging-tip'] ),
					
					'include-database-query'=>array('label'=>$L['include-database-query-in-the-error-log'], 'name' => 'include_database_query', 'type'=>'checkbox', 'value'=>( isset( $logSettings['include_database_query'] ) ? $logSettings['include_database_query'] : false ), 'tip'=>$L['include-database-query-in-the-error-log-tip'] ),
					
					'enable-not-found-error'=>array('label'=>$L['enable-not-found-error-log'], 'name' => 'enable_not_found_log', 'type'=>'checkbox', 'value'=>( isset( $logSettings['enable_not_found_log'] ) ? $logSettings['enable_not_found_log'] : false ), 'tip'=>$L['enable-not-found-error-log-tip'] ),
					
					'enable-bot-log'=>array('label'=>$L['enable-bot-error-log'], 'name' => 'enable_bot_error_log', 'type'=>'checkbox', 'value'=>( isset( $logSettings['enable_bot_error_log'] ) ? $logSettings['enable_bot_error_log'] : false ), 'tip'=>$L['enable-bot-error-log-tip'] ),
					
					'redirection-log'=>array('label'=>$L['enable-the-redirection-log'], 'name' => 'enable_redirection_log', 'type'=>'checkbox', 'value'=>( isset( $logSettings['enable_redirection_log'] ) ? $logSettings['enable_redirection_log'] : false ), 'tip'=>sprintf( $L['enable-the-redirection-log-tip'], $this->GetUrl( 'tools' ) ) ),
				)
			)
		)
	),
	
	'log-settings' => array
	(
		'title' => __( 'log-settings' ),
		'hide' => ( !$this->siteIsSelfHosted ? true : false ),
		'data' => array
		(
			'settings' => array( 
				'title' => null, 'data' => array
				(
					'moderation-log'=>array('label'=>$L['enable-the-moderation-log'], 'name' => 'enable_moderation_log', 'type'=>'checkbox', 'value'=>( isset( $logSettings['enable_moderation_log'] ) ? $logSettings['enable_moderation_log'] : false ), 'tip'=>$L['enable-the-moderation-log-tip'] ),
					
					'administration-log'=>array('label'=>$L['enable-the-administration-log'], 'name' => 'enable_administration_log', 'type'=>'checkbox', 'value'=>( isset( $logSettings['enable_administration_log'] ) ? $logSettings['enable_administration_log'] : false ), 'tip'=>$L['enable-the-administration-log-tip'] ),
					
					'profile-edits-log'=>array('label'=>$L['enable-the-profile-edits-log'], 'name' => 'enable_profile_edits_log', 'type'=>'checkbox', 'value'=>( isset( $logSettings['enable_profile_edits_log'] ) ? $logSettings['enable_profile_edits_log'] : false ), 'tip'=>$L['enable-the-profile-edits-log-tip'] ),
				)
			)
		)
	),
	
	'read-logs' => array
	(
		'title' => __( 'read-logs' ),
		'hide' => ( !$this->siteIsSelfHosted ? true : false ),
		'data' => array
		(
			'settings' => array( 
				'title' => null, 'tip' => $L['read-logs-tip'], 'data' => array
				(
					'automatically-mark-boards'=>array('label'=>$L['automatically-mark-boards-as-read-for-inactive-users'], 'name' => 'automatically_mark_boards_read', 'type'=>'num', 'value'=>( isset( $logSettings['automatically_mark_boards_read'] ) ? $logSettings['automatically_mark_boards_read'] : 90 ), 'tip'=>$L['set-to-zero-to-disable'], 'min'=>'0' ),
					
					'automatically-purge-information'=>array('label'=>$L['automatically-purge-information-about-boards'], 'name' => 'automatically_purge_board_information', 'type'=>'num', 'value'=>( isset( $logSettings['automatically_purge_board_information'] ) ? $logSettings['automatically_purge_board_information'] : 365 ), 'tip'=>$L['set-to-zero-to-disable'], 'min'=>'0' ),
					
					'maximum-users-to-process'=>array('label'=>$L['maximum-users-to-process-at-a-time'], 'name' => 'maximum_users_to_process', 'type'=>'num', 'value'=>( isset( $logSettings['maximum_users_to_process'] ) ? $logSettings['maximum_users_to_process'] : 200 ), 'tip'=>$L['set-to-zero-to-disable'], 'min'=>'0' ),
				)
			)
		)
	),
	
	'log-pruning' => array
	(
		'title' => __( 'log-pruning' ),
		'data' => array
		(
			'settings' => array( 
				'title' => null, 'tip' => $L['log-pruning-tip'], 'data' => array
				(
					'enable-pruning'=>array('label'=>$L['enable-pruning-of-log-entries'], 'name' => 'enable_pruning', 'type'=>'checkbox', 'value'=>( isset( $logSettings['enable_pruning'] ) ? $logSettings['enable_pruning'] : false ), 'tip'=>null ),
					
					'remove-error-log-entries'=>array('label'=>$L['remove-error-log-entries-older-than'], 'name' => 'remove_error_log_entries', 'type'=>'num', 'value'=>( isset( $logSettings['remove_error_log_entries'] ) ? $logSettings['remove_error_log_entries'] : 200 ), 'tip'=>$L['set-to-zero-to-disable'], 'min'=>'0' ),
					
					'remove-moderation-log-entries'=>array('label'=>$L['remove-moderation-log-entries-older-than'], 'name' => 'remove_moderation_log_entries', 'type'=>'num', 'value'=>( isset( $logSettings['remove_moderation_log_entries'] ) ? $logSettings['remove_moderation_log_entries'] : 200 ), 'tip'=>$L['set-to-zero-to-disable'], 'min'=>'0' ),
					
					'remove-ban-hit-log-entries'=>array('label'=>$L['remove-ban-hit-log-entries-older-than'], 'name' => 'remove_ban_hit_log_entries', 'type'=>'num', 'value'=>( isset( $logSettings['remove_ban_hit_log_entries'] ) ? $logSettings['remove_ban_hit_log_entries'] : 200 ), 'tip'=>$L['set-to-zero-to-disable'], 'min'=>'0' ),
					
					'remove-scheduled-task-log-entries'=>array('label'=>$L['remove-scheduled-task-log-entries-older-than'], 'name' => 'remove_scheduled_task_log_entries', 'type'=>'num', 'value'=>( isset( $logSettings['remove_scheduled_task_log_entries'] ) ? $logSettings['remove_scheduled_task_log_entries'] : 200 ), 'tip'=>$L['set-to-zero-to-disable'], 'min'=>'0' ),
					
					'remove-redirection-log-entries'=>array('label'=>$L['remove-redirection-log-entries-older-than'], 'name' => 'remove_redirection_log_entries', 'type'=>'num', 'value'=>( isset( $logSettings['remove_redirection_log_entries'] ) ? $logSettings['remove_redirection_log_entries'] : 200 ), 'tip'=>$L['set-to-zero-to-disable'], 'min'=>'0' ),
				)
			)
		)
	),
	
	'delete-data-settings' => array
	(
		'title' => $L['delete-old-data'],
		'data' => array(
			'delete-posts' => array( 
				'title' => null, 'data' => array
				(
					'delete-published-posts'=>array('label'=>sprintf( $L['delete-data-older-than-x-days'], __( 'published-posts' ) ), 'type'=>'num', 'name' => 'delete_published_posts', 'value' => ( isset( $logSettings['delete_published_posts'] ) ? $logSettings['delete_published_posts'] : 0 ), 'tip'=> sprintf( $L['delete-data-older-than-x-days-tip'], __( 'published-posts' ) ), 'min'=>'0' ),
					
					'delete-draft-posts'=>array('label'=>sprintf( $L['delete-data-older-than-x-days'], __( 'draft-posts' ) ), 'type'=>'num', 'name' => 'delete_draft_posts', 'value' => ( isset( $logSettings['delete_draft_posts'] ) ? $logSettings['delete_draft_posts'] : 0 ), 'tip'=>sprintf( $L['delete-data-older-than-x-days-tip'], __( 'draft-posts' ) ), 'min'=>'0', 'max' => '10000' ),
		
					'delete-auto-save-drafts'=>array('label'=>sprintf( $L['delete-data-older-than-x-days'], __( 'auto-save-drafts' ) ), 'type'=>'num', 'name' => 'delete_auto_draft_posts', 'value' => ( isset( $logSettings['delete_auto_draft_posts'] ) ? $logSettings['delete_auto_draft_posts'] : 0 ), 'tip'=>sprintf( $L['delete-data-older-than-x-days-tip'], __( 'auto-save-drafts' ) ), 'min'=>'0', 'max' => '10000' ),
					
					'delete-published-comments'=>array('label'=>sprintf( $L['delete-data-older-than-x-days'], __( 'published-comments' ) ), 'type'=>'num', 'name' => 'delete_published_comments', 'value' => ( isset( $logSettings['delete_published_comments'] ) ? $logSettings['delete_published_comments'] : 0 ), 'tip'=> sprintf( $L['delete-data-older-than-x-days-tip'], __( 'published-comments' ) ), 'min'=>'0', 'max' => '10000' ),
					
					'delete-other-comments'=>array('label'=>sprintf( $L['delete-data-older-than-x-days'], __( 'pending-spam-deleted-comments' ) ), 'type'=>'num', 'name' => 'delete_other_comments', 'value' => ( isset( $logSettings['delete_other_comments'] ) ? $logSettings['delete_other_comments'] : 0 ), 'tip'=> sprintf( $L['delete-data-older-than-x-days-tip'], __( 'pending-spam-deleted-comments' ) ), 'min'=>'0', 'max' => '10000' ),
					
					'delete-inbox-emails'=>array('label'=>sprintf( $L['delete-data-older-than-x-days'], __( 'inbox-emails' ) ), 'type'=>'num', 'name' => 'delete_inbox_emails', 'value' => ( isset( $logSettings['delete_inbox_emails'] ) ? $logSettings['delete_inbox_emails'] : 0 ), 'tip'=> sprintf( $L['delete-data-older-than-x-days-tip'], __( 'inbox-emails' ) ), 'min'=>'0', 'max' => '10000' ),
					
					'delete-other-emails'=>array('label'=>sprintf( $L['delete-data-older-than-x-days'], __( 'sent-drafts-junk-trash-emails' ) ), 'type'=>'num', 'name' => 'delete_other_emails', 'value' => ( isset( $logSettings['delete_other_emails'] ) ? $logSettings['delete_other_emails'] : 0 ), 'tip'=> sprintf( $L['delete-data-older-than-x-days-tip'], __( 'sent-drafts-junk-trash-emails' ) ), 'min'=>'0', 'max' => '10000'  ),
					
					'hide-prices'=>array('label'=>$L['hide-prices-in-post-after-x-retries'], 'type'=>'num', 'name' => 'hide_prices', 'value' => ( isset( $logSettings['hide_prices'] ) ? $logSettings['hide_prices'] : 0 ), 'tip'=> $L['hide-prices-in-post-after-x-retries-tip'], 'min'=>'0', 'max' => '100' ),
					
					'delete-prices'=>array('label'=>$L['delete-prices-after-page-not-found-x-times'], 'type'=>'num', 'name' => 'delete_prices', 'value' => ( isset( $logSettings['delete_prices'] ) ? $logSettings['delete_prices'] : 0 ), 'tip'=> $L['delete-prices-after-page-not-found-x-times-tip'], 'min'=>'0', 'max' => '100' )
				)
			)
		)
	),
	
);