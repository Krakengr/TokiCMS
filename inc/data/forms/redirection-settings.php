<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Redirection Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

$ipLoggingData = array(
			'disable' => array( 'name' => 'disable', 'title'=> $L['disable'], 'disabled' => false, 'data' => array() ),
			'full-ip-logging' => array( 'name' => 'full-ip-logging', 'title'=> $L['full-ip-logging'], 'disabled' => false, 'data' => array() ),
			'anonymize-ip' => array( 'name' => 'anonymize-ip', 'title'=> $L['anonymize-ip'], 'disabled' => false, 'data' => array() ),
);

$redirectionSettings = Json( $settings['redirection_data'] );

$form = array
(
	'redirection-settings' => array
	(
		'title' => $L['redirection-settings'],
		'data' => array(

			'general-settings' => array( 
				'title' => null, 'data' => array
				(
					'monitor-links'=>array('label'=>$L['monitor-permalink-changes-in-posts-and-pages'], 'type'=>'checkbox', 'name' => 'settings[monitor_permalink_changes]', 'value' => ( isset( $redirectionSettings['monitor_permalink_changes'] ) ? $redirectionSettings['monitor_permalink_changes'] : null ), 'tip'=>$L['monitor-permalink-changes-tip'] ),
					
					'keep-log'=>array('label'=>$L['keep-a-log-of-all-redirects-and-404-errors'], 'type'=>'checkbox', 'name' => 'settings[keep_log_redirects_errors]', 'value' => ( isset( $redirectionSettings['keep_log_redirects_errors'] ) ? $redirectionSettings['keep_log_redirects_errors'] : null ), 'tip'=>$L['keep-a-log-of-all-redirects-tip'] ),
					
					'keep-logs'=>array('label'=>$L['keep-logs'], 'name' => 'settings[keep_logs]', 'type'=>'num', 'value'=>( isset( $redirectionSettings['keep_logs'] ) ? $redirectionSettings['keep_logs'] : 0 ), 'tip'=>$L['keep-logs-tip'], 'min'=>'0', 'max'=>'180'),
					
					'ip-logging'=>array('label'=>$L['ip-logging'], 'name' => 'settings[ip_logging]', 'type'=>'select', 'value'=>( isset( $redirectionSettings['ip_logging'] ) ? $redirectionSettings['ip_logging'] : null ), 'tip'=>$L['ip-logging-tip'], 'firstNull' => false, 'disabled' => false, 'data' => $ipLoggingData ),
					
					'case-insensitive-matches'=>array('label'=>$L['case-insensitive-matches'], 'type'=>'checkbox', 'name' => 'settings[case_insensitive_matches]', 'value' => ( isset( $redirectionSettings['case_insensitive_matches'] ) ? $redirectionSettings['case_insensitive_matches'] : null ), 'tip'=>$L['case-insensitive-matches-tip'] ),
					
					'ignore-trailing-slashes'=>array('label'=>$L['ignore-trailing-slashes'], 'type'=>'checkbox', 'name' => 'settings[ignore_trailing_slashes]', 'value' => ( isset( $redirectionSettings['ignore_trailing_slashes'] ) ? $redirectionSettings['ignore_trailing_slashes'] : null ), 'tip'=>$L['ignore-trailing-slashes-tip'] ),
				)
			),
		)
	)
);