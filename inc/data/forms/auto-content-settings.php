<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Edit Schema Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

$autoContentSettings = Json( $settings['auto_content_data'] );

$form = array
(
	'auto-content-settings' => array
	(
		'title' => $L['auto-content-settings'],
		'data' => array(
		
			'auto-content-settings' => array( 
				'title' => $L['global-settings'], 'tip'=>null, 'data' => array
				(
					'enable-cache'=>array('label'=>$L['enable-cache'], 'type'=>'checkbox', 'name' => 'autocontent[enable_cache]', 'value' => ( isset( $autoContentSettings['enable_cache'] ) ? $autoContentSettings['enable_cache'] : null ), 'tip'=>$L['enable-auto-content-tip'] ),
					
				)
			),

		)
	)

);