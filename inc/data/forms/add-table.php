<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Add A New form Form
#
#####################################################

$templs = GetFormTemplates( $this->siteID, 'table' );

$templates = array();

$templates['blank'] = array( 'name' => 'blank', 'title'=>__( 'blank' ), 'disabled' => false, 'data' => null );
$templates['price']  = array( 'name' => 'price', 'title'=>__( 'price-comparison-table' ), 'disabled' => false, 'data' => null );
$templates['product']  = array( 'name' => 'product', 'title'=>__( 'product-table' ), 'disabled' => false, 'data' => null );

if ( !empty( $templs ) )
{
	foreach ( $templs as $templ )
	{
		$templates[$templ['id']] = array( 'name' => $templ['id'], 'title'=> $templ['title'], 'disabled' => false, 'data' => null );
	}
}

$form = array
(
	'add-table' => array
	(
		'title' => __( 'add-new-table' ),
		'col' => 10,
		'data' => array(
		
			'name' => array( 
				'title' => null, 'data' => array
				(
					'title'=>array('label'=>__( 'name' ), 'type'=>'text', 'name' => 'name', 'value' =>null, 'required' => true, 'tip'=>__( 'table-name-tip' ) ),
					
					'description'=>array('label'=>__( 'description-optional' ), 'type'=>'textarea', 'name' => 'description', 'value' =>null, 'tip'=>__( 'table-description-tip' ) ),
					
					//'number-of-rows'=>array('label'=>__( 'number-of-rows' ), 'name' => 'number-of-rows', 'type'=>'num', 'value'=>1, 'tip'=>__( 'number-of-rows-tip' ), 'min'=>'1', 'max'=>'20'),
					
					//'number-of-columns'=>array('label'=>__( 'number-of-columns' ), 'name' => 'number-of-columns', 'type'=>'num', 'value'=>1, 'tip'=>__( 'number-of-columns-tip' ), 'min'=>'1', 'max'=>'20'),
					
					'template'=>array('label'=>__( 'select-a-template' ), 'name' => 'select-template', 'type'=>'select', 'value'=>null, 'tip'=>__('select-a-template-tip' ), 'firstNull' => false, 'data' => $templates, 'id' => 'slcCountry', 'class' => 'form-control' ),
				)
			)
		)
	)
);