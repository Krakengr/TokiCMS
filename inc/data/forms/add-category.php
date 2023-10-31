<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Add A New Category Form
#
#####################################################
$form = array
(
	'add-category' => array
	(
		'title' => __( 'add-new-category' ),
		'col' => 6,
		'data' => array(
		
			'name' => array( 
				'title' => null, 'data' => array
				(
					'category-title'=>array('label'=>__( 'name' ), 'type'=>'text', 'name' => 'categoryName', 'value' =>null, 'tip'=>__( 'category-name-tip' ) )
				)
			),
			
			'slug' => array( 
				'title' => null, 'data' => array
				(
					'slug'=>array('label'=>__( 'slug' ), 'type'=>'text', 'name' => 'categorySlug', 'value' =>null, 'tip'=> __('category-slug-tip' ) )
				)
			),
			
			'description' => array( 
				'title' => null, 'data' => array
				(
					'description'=>array('label'=>__( 'description' ), 'type'=>'textarea', 'name' => 'categoryDescription', 'value' =>null, 'tip'=>__( 'category-descr-tip' ) )
				)
			)
		)
	)
);