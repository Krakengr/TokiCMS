<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# New Manufacturer Form
#
#####################################################
global $Admin;

$form = array
(
	'add-manufacturer' => array
	(
		'title' => __( 'add-new-manufacturer' ),
		'col' => 6,
		'data' => array(
		
			'name' => array( 
				'title' => null, 'data' => array
				(
					'name'=>array('label'=>__( 'name' ), 'type'=>'text', 'name' => 'name', 'value' =>null, 'tip'=>__('the-title-how-it-appears' ) )
				)
			),
			
			'sef' => array( 
				'title' => null, 'data' => array
				(
					'name'=>array('label'=>__( 'slug' ), 'type'=>'text', 'name' => 'sef', 'value' =>null, 'tip'=>__('slug-tip' ) )
				)
			)
		)
	)
);