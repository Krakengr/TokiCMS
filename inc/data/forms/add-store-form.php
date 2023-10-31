<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# New Store Form
#
#####################################################
global $Admin;

$form = array
(
	'add-store' => array
	(
		'title' => __( 'add-new-store' ),
		'col' => 6,
		'data' => array(
		
			'name' => array( 
				'title' => null, 'data' => array
				(
					'name'=>array('label'=>__( 'name' ), 'type'=>'text', 'name' => 'name', 'value' =>null, 'tip'=>__('the-title-how-it-appears' ), 'required' => true )
				)
			),
			
			'sef' => array( 
				'title' => null, 'data' => array
				(
					'name'=>array('label'=>__( 'slug' ), 'type'=>'text', 'name' => 'sef', 'value' =>null, 'tip'=>__('slug-tip' ) )
				)
			),
			
			'url' => array( 
				'title' => null, 'data' => array
				(
					'name'=>array('label'=>__( 'url' ), 'type'=>'text', 'name' => 'url', 'value' =>null, 'tip'=>null )
				)
			)
		)
	)
);