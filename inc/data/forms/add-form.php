<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Add A New form Form
#
#####################################################

$templs = GetFormTemplates( $this->siteID );

$templates = array();

$templates['0'] = array( 'name' => 'blank', 'title'=>__( 'blank' ), 'disabled' => false, 'data' => null );

if ( !empty( $templs ) )
{
	foreach ( $templs as $templ )
	{
		$templates[$templ['id']] = array( 'name' => $templ['id'], 'title'=> $templ['title'], 'disabled' => false, 'data' => null );
	}
}

$form = array
(
	'add-form' => array
	(
		'title' => __( 'add-new-form' ),
		'col' => 10,
		'data' => array(
		
			'name' => array( 
				'title' => null, 'data' => array
				(
					'title'=>array('label'=>__( 'name' ), 'type'=>'text', 'name' => 'name', 'value' =>null, 'required' => true, 'tip'=>__( 'form-name-tip' ) ),
					
					'description'=>array('label'=>__( 'description-optional' ), 'type'=>'textarea', 'name' => 'description', 'value' =>null, 'tip'=>__( 'form-description-tip' ) ),
					
					'template'=>array('label'=>__( 'select-a-template' ), 'name' => 'select-template', 'type'=>'select', 'value'=>null, 'tip'=>__('select-a-template-tip' ), 'firstNull' => false, 'data' => $templates, 'id' => 'slcCountry', 'class' => 'form-control' ),
				)
			)
		)
	)
);