<?php defined('TOKICMS') or die('Hacking attempt...');

//This array contains all the tasks scheduled by TOKICMS
$scheduledTasks = array(
	'daily-maintenance' => array( 'function' => 'DailyMaintenance', 'name' => __ ( 'daily-maintenance' ), 'tip' => __( 'daily-maintenance-tip' ) ),
		
	'daily-digest' => array( 'function' => 'DailyDigest', 'name' => __ ( 'daily-digest' ), 'tip' => __( 'daily-digest-tip' ) ),
		
	'weekly-digest' => array( 'function' => 'WeeklyDigest', 'name' => __ ( 'weekly-digest' ), 'tip' => __( 'weekly-digest-tip' ) ),
		
	'weekly-maintenance' => array( 'function' => 'WeeklyMaintenance', 'name' => __ ( 'weekly-maintenance' ), 'tip' => __( 'weekly-maintenance-tip' ) ),

	'prune-logs' => array( 'function' => 'PruneLogs', 'name' => __ ( 'prune-logs' ), 'tip' => __( 'prune-logs-tip' ) ),
		
	'backup-db' => array( 'function' => 'BackupDbTask', 'name' => __ ( 'backup-db' ), 'tip' => __( 'backup-db-tip' ) ),
		
	'broken-link-check' => array( 'function' => 'BrokenLinkCheck', 'name' => __ ( 'broken-link-check' ), 'tip' => __( 'broken-link-check-tip' ) ),
		
	'bot-digest' => array( 'function' => 'BotDigest', 'name' => __ ( 'bot-digest' ), 'tip' => __( 'bot-digest-tip' ) ),
		
	'mark-boards-as-read' => array( 'function' => 'MarkBoardsAsRead', 'name' => __ ( 'mark-boards-as-read' ), 'tip' => __( 'mark-boards-as-read-tip' ) )		
);

//This array contains all the maintenance tasks
$maintenanceTasks = array(
	'empty-file-cache' => array( 'function' => 'EmptyCaches', 'name' => __ ( 'empty-file-cache' ), 'tip' => __( 'empty-file-cache-tip' ) ),
	'recount-all-statistics' => array( 'function' => 'RecountStatistics', 'name' => __ ( 'recount-all-statistics' ), 'tip' => __( 'recount-all-statistics-tip' ) ),
	'optimize-db-tables' => array( 'function' => 'OptimizeDB', 'name' => __ ( 'optimize-db-tables' ), 'tip' => __( 'optimize-db-tables-tip' ) ),
);

//This array contains all the maintenance task URLs by action
$maintenanceTasksActions = array(
	'maintenance' 	=> array( 'name' => __ ( 'maintenance' ), 	'tip' => null ),
	'prices' 		=> array( 'name' => __ ( 'prices' ), 		'tip' => null ),
	'autoblog' 		=> array( 'name' => __ ( 'autoblog' ), 		'tip' => null ),
);