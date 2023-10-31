<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Add A New Link Form
#
#####################################################

include ( ARRAYS_ROOT . 'generic-arrays.php');

$linksRedirection = array(
	'default' => array( 'name' => 'default', 'title'=> $L['default'], 'disabled' => false, 'data' => array() ),
	'direct' => array( 'name' => 'direct', 'title'=> $L['direct'], 'disabled' => false, 'data' => array() )
);

foreach ( $linksRedirectionArray as $key => $redir )
{
	$linksRedirection[$key] = array( 'name' => $redir['name'], 'title'=> $redir['title'], 'disabled' => false, 'data' => array() );
}

$form = array
(
	'add-link' => array
	(
		'title' => __( 'add-new-link' ),
		'col' => 8,
		'data' => array(
		
			'generic' => array( 
				'title' => null, 'data' => array
				(
					'link-title'=>array('label'=>__( 'name' ), 'type'=>'text', 'name' => 'name', 'value' =>null, 'tip'=>__( 'category-name-tip' ), 'required' => true ),
					
					'url'=>array('label'=>__( 'target-url' ), 'type'=>'text', 'name' => 'url', 'value' =>null, 'tip'=> null, 'required' => true ), 
					
					'description'=>array('label'=>__( 'description' ), 'type'=>'textarea', 'name' => 'description', 'value' =>null, 'tip'=>__( 'add-new-link-descr-tip' ) ), 
					
					'short-link'=>array('label'=>__( 'short-link' ), 'type'=>'checkbox', 'name' => 'short-link', 'value' => null, 'tip'=>__( 'short-link-tip' ) ),
					
					'no-follow'=>array('label'=>__( 'no-follow' ), 'type'=>'checkbox', 'name' => 'no-follow', 'value' => null, 'tip'=>__( 'add-new-link-nofollow-tip' ) ),
					
					'sponsored'=>array('label'=>__( 'sponsored' ), 'type'=>'checkbox', 'name' => 'sponsored', 'value' => null, 'tip'=>__( 'add-new-link-sponsored-tip' ) ),
					
					'redirection'=>array(
						'label'=>$L['redirection'], 'name' => 'redirection', 'type'=>'select', 'value'=>null, 'tip'=>__( 'add-new-link-redirection-tip' ), 'firstNull' => false, 'data' => $linksRedirection, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3'
					),
				)
			)
		)
	)
);