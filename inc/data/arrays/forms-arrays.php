<?php defined('TOKICMS') or die('Hacking attempt...');

//Generic array for table's cells desing
$genericTablesCellDesingArray = array(

	'width' => array(
		'name' => 'width', 'title' => __( 'width' ), 'disabled' => false, 'tip'=>null, 'type'=>'text'
	),
	
	'font-size' => array(
		'name' => 'font-size', 'title' => __( 'font-size' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'placeholder' => '16px'
	),
	
	'line-height' => array(
		'name' => 'line-height', 'title' => __( 'line-height' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'placeholder' => '1.2em'
	),
	
	'font-weight' => array(
		'name' => 'font-weight', 'title' => __( 'font-weight' ), 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
		array(
			'normal' => array( 'name' => 'normal' ),
			'bold' => array( 'name' => 'bold' ),
			'light' => array( 'name' => 'light' ),
			'100' => array( 'name' => 100 ),
			'200' => array( 'name' => 200 ),
			'300' => array( 'name' => 300 ),
			'400' => array( 'name' => 400 ),
			'500' => array( 'name' => 500 ),
			'600' => array( 'name' => 600 ),
			'700' => array( 'name' => 700 ),
			'800' => array( 'name' => 800 ),
			'900' => array( 'name' => 900 )
		)
	),
	
	'font-family' => array(
		'name' => 'font-family', 'title' => __( 'font-family' ), 'disabled' => false, 'tip'=>null, 'type'=>'text'
	),
	
	'font-color' => array(
		'name' => 'color', 'title' => __( 'font-color' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'colorpicker' => true, 'placeholder' => '#000'
	),
	
	'text-align' => array(
		'name' => 'text-align', 'title' => __( 'text-align' ), 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
		array(
			'auto' => array( 'name' => 'auto' ),
			'center' => array( 'name' => 'center' ),
			'left' => array( 'name' => 'left' ),
			'right' => array( 'name' => 'right' )
		)
	),
	
	'text-transform' => array(
		'name' => 'text-transform', 'title' => __( 'text-transform' ), 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
		array(
			'none' => array( 'name' => 'none' ),
			'uppercase' => array( 'name' => 'uppercase' ),
			'capitalize' => array( 'name' => 'capitalize' ),
			'lowercase' => array( 'name' => 'lowercase' )
		)
	),
	
	'letter-spacing' => array(
		'name' => 'letter-spacing', 'title' => __( 'letter-spacing' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'placeholder' => '0px'
	),

	'background-color' => array(
		'name' => 'background-color', 'title' => __( 'background-color' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'colorpicker' => true, 'placeholder' => '#000'
	),
	
	'padding' => array(
		'name' => 'padding', 'title' => __( 'padding' ), 'disabled' => false, 'tip'=>null, 'type'=>'item-group', 'items' => 
		array(
			'padding-top' => array( 'name' => 'padding-top', 'type'=>'text', 'placeholder' => 'top' ),
			'padding-bottom' => array( 'name' => 'padding-bottom', 'type'=>'text', 'placeholder' => 'bottom' ),
			'padding-left' => array( 'name' => 'padding-left', 'type'=>'text', 'placeholder' => 'left' ),
			'padding-right' => array( 'name' => 'padding-right', 'type'=>'text', 'placeholder' => 'right' )
		)
	)
);

//Generic array for element style
//Grab the generic table array for now
$genericElementDesingArray = $genericTablesCellDesingArray;

//Add a few more itmes in this array
$genericElementDesingArray['height'] = array(
	'name' => 'height', 'title' => __( 'height' ), 'disabled' => false, 'tip'=> null, 'type'=> 'text'
);

$genericElementDesingArray['border-radius'] = array(
	'name' => 'border-radius', 'title' => __( 'border-radius' ), 'disabled' => false, 'tip'=> null, 'type'=> 'text'
);

$genericElementDesingArray['background-color-on-hover'] = array(
	'name' => 'background-color-on-hover', 'title' => __( 'background-color-on-hover' ), 'disabled' => false, 'tip'=> null, 'type'=> 'text', 'colorpicker' => true, 'placeholder' => '#000'
);

$genericElementDesingArray['border-color-on-hover'] = array(
	'name' => 'border-color-on-hover', 'title' => __( 'border-color-on-hover' ), 'disabled' => false, 'tip'=> null, 'type'=> 'text', 'colorpicker' => true, 'placeholder' => '#000'
);

$genericElementDesingArray['border'] = array(
	'name' => 'border', 'title' => __( 'border' ), 'disabled' => false, 'tip'=>null, 'type'=>'item-group', 'items' => 
	array(
		'width' => array( 'name' => 'border-width', 'type'=>'text', 'placeholder' => 'width' ),
		'style' => array(
			'name' => 'border-style', 'title' => null, 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
				array(
					'solid' => array( 'name' => __( 'solid' ) ),
					'dashed' => array( 'name' => __( 'dashed' ) ),
					'dotted' => array( 'name' => __( 'dotted' ) ),
					'none' => array( 'name' => __( 'none' ) ),
				)
		),
	
		'color' => array(
			'name' => 'border-color', 'title' => __( 'font-color' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'colorpicker' => true, 'placeholder' => '#000'
		)
	)
);	
	
$genericElementDesingArray['margin'] = array(
	'name' => 'margin', 'title' => __( 'margin' ), 'disabled' => false, 'tip'=>null, 'type'=>'item-group', 'items' => 
	array(
		'margin-top' => array( 'name' => 'top', 'type'=>'text', 'placeholder' => 'top' ),
		'margin-bottom' => array( 'name' => 'bottom', 'type'=>'text', 'placeholder' => 'bottom' ),
		'margin-left' => array( 'name' => 'left', 'type'=>'text', 'placeholder' => 'left' ),
		'margin-right' => array( 'name' => 'right', 'type'=>'text', 'placeholder' => 'right' )
	)
);

$genericElementDesingArray['display'] = array(
	'name' => 'display', 'title' => __( 'display' ), 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
	array(
		'null' => array( 'name' => '' ),
		'block' => array( 'name' => __( 'block' ) ),
		'inline' => array( 'name' => __( 'inline' ) ),
		'inline-block' => array( 'name' => __( 'inline-block' ) ),
		'none' => array( 'name' => __( 'none' ) )
	)
);

$genericElementDesingArray['vertical-align'] = array(
	'name' => 'vertical-align', 'title' => __( 'vertical-align' ), 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
	array(
		'null' => array( 'name' => '' ),
		'middle' => array( 'name' => __( 'middle' ) ),
		'top' => array( 'name' => __( 'top' ) ),
		'bottom' => array( 'name' => __( 'bottom' ) ),
		'baseline' => array( 'name' => __( 'baseline' ) )
	)
);

//Image Elements Desing array
$imageElementsDesingArray = array(

	'width' => array(
		'name' => 'width', 'title' => __( 'width' ), 'disabled' => false, 'tip'=>__( 'image-width-style-tip' ), 'type'=>'text'
	),
	
	'border' => array(
		'name' => 'border', 'title' => __( 'border' ), 'disabled' => false, 'tip'=>null, 'type'=>'item-group', 'items' => 
		array(
			'border-width' => array( 'name' => 'border-width', 'type'=>'text', 'placeholder' => 'width' ),
			'border-style' => array(
				'name' => 'border-style', 'title' => null, 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
					array(
						'solid' => array( 'name' => __( 'solid' ) ),
						'dashed' => array( 'name' => __( 'dashed' ) ),
						'dotted' => array( 'name' => __( 'dotted' ) ),
						'none' => array( 'name' => __( 'none' ) ),
					)
			),
	
			'border-color' => array(
				'name' => 'border-color', 'title' => __( 'font-color' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'colorpicker' => true, 'placeholder' => '#000'
			)
		)
	),
	
	'border-radius' => array(
		'name' => 'border-radius', 'title' => __( 'border-radius' ), 'disabled' => false, 'tip'=>null, 'type'=>'text' 
	),
	
	'padding' => array(
		'name' => 'padding', 'title' => __( 'padding' ), 'disabled' => false, 'tip'=>null, 'type'=>'item-group', 'items' => 
		array(
			'padding-top' => array( 'name' => 'top', 'type'=>'text', 'placeholder' => 'top' ),
			'padding-bottom' => array( 'name' => 'bottom', 'type'=>'text', 'placeholder' => 'bottom' ),
			'padding-left' => array( 'name' => 'left', 'type'=>'text', 'placeholder' => 'left' ),
			'padding-right' => array( 'name' => 'right', 'type'=>'text', 'placeholder' => 'right' )
		)
	),
	
	'margin' => array(
		'name' => 'margin', 'title' => __( 'margin' ), 'disabled' => false, 'tip'=>null, 'type'=>'item-group', 'items' => 
		array(
			'margin-top' => array( 'name' => 'top', 'type'=>'text', 'placeholder' => 'top' ),
			'margin-bottom' => array( 'name' => 'bottom', 'type'=>'text', 'placeholder' => 'bottom' ),
			'margin-left' => array( 'name' => 'left', 'type'=>'text', 'placeholder' => 'left' ),
			'margin-right' => array( 'name' => 'right', 'type'=>'text', 'placeholder' => 'right' )
		)
	)
);

//Generic Tables Header And Cell Desing arrays
$genericTablesHeaderDesingArray = array(

	'text-align' => array(
		'name' => 'text-align', 'title' => __( 'text-align' ), 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
		array(
			'auto' => array( 'name' => 'auto' ),
			'center' => array( 'name' => 'center' ),
			'left' => array( 'name' => 'left' ),
			'right' => array( 'name' => 'right' )
		)
	),
	
	'text-transform' => array(
		'name' => 'text-transform', 'title' => __( 'text-transform' ), 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
		array(
			'none' => array( 'name' => 'none' ),
			'uppercase' => array( 'name' => 'uppercase' ),
			'capitalize' => array( 'name' => 'capitalize' ),
			'lowercase' => array( 'name' => 'lowercase' )
		)
	),
	
	'letter-spacing' => array(
		'name' => 'letter-spacing', 'title' => __( 'letter-spacing' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'placeholder' => '0px'
	),
	
	'font-family' => array(
		'name' => 'font-family', 'title' => __( 'font-family' ), 'disabled' => false, 'tip'=>null, 'type'=>'text' 
	),
	
	'color' => array(
		'name' => 'color', 'title' => __( 'font-color' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'colorpicker' => true, 'placeholder' => '#000'
	),
	
	'background-color' => array(
		'name' => 'background-color', 'title' => __( 'background-color' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'colorpicker' => true, 'placeholder' => '#000'
	),
	
	'padding' => array(
		'name' => 'padding', 'title' => __( 'padding' ), 'disabled' => false, 'tip'=>null, 'type'=>'item-group', 'items' => 
		array(
			'padding-top' => array( 'name' => 'padding-top', 'type'=>'text', 'placeholder' => 'top' ),
			'padding-bottom' => array( 'name' => 'padding-bottom', 'type'=>'text', 'placeholder' => 'bottom' ),
			'padding-left' => array( 'name' => 'padding-left', 'type'=>'text', 'placeholder' => 'left' ),
			'padding-right' => array( 'name' => 'padding-right', 'type'=>'text', 'placeholder' => 'right' )
		)
	),

);

$genericTablesCellDesingArray = array(

	'width' => array(
		'name' => 'width', 'title' => __( 'width' ), 'disabled' => false, 'tip'=>null, 'type'=>'text'
	),
	
	'text-align' => array(
		'name' => 'text-align', 'title' => __( 'text-align' ), 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
		array(
			'auto' => array( 'name' => 'auto' ),
			'center' => array( 'name' => 'center' ),
			'left' => array( 'name' => 'left' ),
			'right' => array( 'name' => 'right' )
		)
	),
	
	'font-size' => array(
		'name' => 'font-size', 'title' => __( 'font-size' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'placeholder' => '16px'
	),
	
	'line-height' => array(
		'name' => 'line-height', 'title' => __( 'line-height' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'placeholder' => '1.2em'
	),
	
	'font-weight' => array(
		'name' => 'font-weight', 'title' => __( 'font-weight' ), 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
		array(
			'normal' => array( 'name' => 'normal' ),
			'bold' => array( 'name' => 'bold' ),
			'light' => array( 'name' => 'light' ),
			'100' => array( 'name' => 100 ),
			'200' => array( 'name' => 200 ),
			'300' => array( 'name' => 300 ),
			'400' => array( 'name' => 400 ),
			'500' => array( 'name' => 500 ),
			'600' => array( 'name' => 600 ),
			'700' => array( 'name' => 700 ),
			'800' => array( 'name' => 800 ),
			'900' => array( 'name' => 900 )
		)
	),
	
	'font-family' => array(
		'name' => 'font-family', 'title' => __( 'font-family' ), 'disabled' => false, 'tip'=>null, 'type'=>'text'
	),
	
	'font-color' => array(
		'name' => 'font-color', 'title' => __( 'font-color' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'colorpicker' => true, 'placeholder' => '#000'
	),
	
	'text-transform' => array(
		'name' => 'text-transform', 'title' => __( 'text-transform' ), 'disabled' => false, 'tip'=>null, 'type'=>'select', 'options' => 
		array(
			'none' => array( 'name' => 'none' ),
			'uppercase' => array( 'name' => 'uppercase' ),
			'capitalize' => array( 'name' => 'capitalize' ),
			'lowercase' => array( 'name' => 'lowercase' )
		)
	),
	
	'letter-spacing' => array(
		'name' => 'letter-spacing', 'title' => __( 'letter-spacing' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'placeholder' => '0px'
	),

	'background-color' => array(
		'name' => 'background-color', 'title' => __( 'background-color' ), 'disabled' => false, 'tip'=>null, 'type'=>'text', 'colorpicker' => true, 'placeholder' => '#000'
	),
	
	'padding' => array(
		'name' => 'padding', 'title' => __( 'padding' ), 'disabled' => false, 'tip'=>null, 'type'=>'item-group', 'items' => 
		array(
			'padding-top' => array( 'name' => 'padding-top', 'type'=>'text', 'placeholder' => 'top' ),
			'padding-bottom' => array( 'name' => 'padding-bottom', 'type'=>'text', 'placeholder' => 'bottom' ),
			'padding-left' => array( 'name' => 'padding-left', 'type'=>'text', 'placeholder' => 'left' ),
			'padding-right' => array( 'name' => 'padding-right', 'type'=>'text', 'placeholder' => 'right' )
		)
	)
);

############# End of Desing Arrays #############

//Generic Forms Array
$genericFormsArray = $genericPageBuildArray = array(

	'button' => array( 
		'name' => 'button', 'title' => __( 'button' ), 'disabled' => false, 'style' => $genericElementDesingArray, 'data' => 
		array(
			'label' 		=> array( 'label'=> __( 'label' ), 'type'=>'text', 'value' => __( 'button' ), 'tip'=>null ),
			'button-name' 	=> array( 'label'=> __( 'button-name' ), 'type'=>'text', 'value' => __( 'button' ), 'tip'=>null ),
			
			'type' => array( 'label'=> __( 'type' ), 'type'=>'select', 'value' => __( 'button' ), 'tip'=>null, 
				'options' => array(
					'button' => array( 'name' => __( 'button' ) ),
					'submit' => array( 'name' => __( 'submit' ) ),
					'reset' => array( 'name' => __( 'reset' ) )
				)
			),
			'class' => array( 'label'=> __( 'class' ), 'type'=>'text', 'value' => 'btn-default btn', 'tip'=> __( 'button-class-tip' ) ),
			'value' => array( 'label'=> __( 'value' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'name'  => array( 'label'=> __( 'name' ), 'type'=>'text', 'value' => 'button-' . GenerateStrongRandomKey( 6 ), 'tip'=> __( 'unique-id-and-name' ) ),
			'display-label' => array( 'label'=> __( 'display-label' ), 'type'=>'checkbox', 'value' => "1", 'tip'=> null ),
		)
	),
	
	'header' => array( 
		'name' => 'header', 'title' => __( 'header' ), 'disabled' => false, 'allowInTables' => false, 'style' => $genericElementDesingArray, 'data' => 
		array(
			'type' => array( 'label'=> __( 'type' ), 'type'=>'select', 'value' => '', 'tip'=>null, 
				'options' => array(
					'h1' => array( 'name' => 'h1' ),
					'h2' => array( 'name' => 'h2' ),
					'h3' => array( 'name' => 'h3' ),
					'h4' => array( 'name' => 'h4' ),
					'h5' => array( 'name' => 'h5' ),
					'h6' => array( 'name' => 'h6' )
				)
			),
			'value' => array( 'label'=> __( 'value' ), 'type'=>'text', 'value' => __( 'header' ), 'tip'=> null ),
			'class' => array( 'label'=> __( 'class' ), 'type'=>'text', 'value' => '', 'tip'=> null ),
			'name'  => array( 'label'=> __( 'name' ), 'type'=>'text', 'value' => 'header-' . GenerateStrongRandomKey( 6 ), 'tip'=> __( 'unique-id-and-name' ) ),
		)
	),
	
	'hidden-input' => array( 
		'name' => 'hidden-input', 'title' => __( 'hidden-input' ), 'disabled' => false, 'allowInTables' => false, 'style' => $genericElementDesingArray, 'data' => 
		array(
			'value' => array( 'label'=> __( 'value' ), 'type'=>'text', 'value' => __( 'hidden-input' ), 'tip'=> null ),
			'name'  		=> array( 'label'=> __( 'name' ), 'type'=>'text', 'value' => 'hidden-input-' . GenerateStrongRandomKey( 6 ), 'tip'=> __( 'unique-id-and-name' ) )
		)
	),
	
	'text-field' => array( 
		'name' => 'text-field', 'title'=> __( 'text-field' ), 'disabled' => false, 'allowInTables' => false, 'style' => $genericElementDesingArray, 'data' => 
		array(
			'label' 		=> array( 'label'=> __( 'label' ), 'type'=>'text', 'value' => __( 'text-field' ), 'tip'=>null ),
			'class' 		=> array( 'label'=> __( 'class' ), 'type'=>'text', 'value' => 'form-control', 'tip'=> null ),
			'placeholder' 	=> array( 'label'=> __( 'placeholder' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'prepend' 		=> array( 'label'=> __( 'prepend' ), 'type'=>'text', 'value' => null, 'tip'=> __( 'prepend-field-tip' ) ),
			'append' 		=> array( 'label'=> __( 'append' ), 'type'=>'text', 'value' => null, 'tip'=> __( 'append-field-tip' ) ),
			'limit-length'	=> array( 'label'=> __( 'limit-length' ), 'type'=>'num', 'value' => 0, 'tip'=> __( 'limit-length-tip' ), 'min' => 0, 'max' => 100 ),
			'help-text' 	=> array( 'label'=> __( 'help-text' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'name'  		=> array( 'label'=> __( 'name' ), 'type'=>'text', 'value' => 'text-field-' . GenerateStrongRandomKey( 6 ), 'tip'=> __( 'unique-id-and-name' ) ),
			'required' 		=> array( 'label'=> __( 'required-field' ), 'type'=>'checkbox', 'value' => "1", 'tip'=> null ),
			'display-label' => array( 'label'=> __( 'display-label' ), 'type'=>'checkbox', 'value' => "1", 'tip'=> null ),
		)
	),
	
	'text-area' => array( 
		'name' => 'text-area', 'title' => __( 'text-area' ), 'disabled' => false, 'allowInTables' => false, 'style' => $genericElementDesingArray,  'data' => 
		array(
			'label' 		=> array( 'label'=> __( 'label' ), 'type'=>'text', 'value' => __( 'text-field' ), 'tip'=>null ),
			'class' 		=> array( 'label'=> __( 'class' ), 'type'=>'text', 'value' => 'form-control', 'tip'=> null ),
			'placeholder' 	=> array( 'label'=> __( 'placeholder' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'rows' 			=> array( 'label'=> __( 'rows' ), 'type'=>'num', 'value' => 5, 'tip'=> null, 'min' => 3, 'max' => 99 ),
			'display-label' => array( 'label'=> __( 'display-label' ), 'type'=>'checkbox', 'value' => "1", 'tip'=> null ),
			'limit-length'	=> array( 'label'=> __( 'limit-length' ), 'type'=>'num', 'value' => 0, 'tip'=> __( 'limit-length-tip' ), 'min' => 0, 'max' => 500 ),
			'name'  		=> array( 'label'=> __( 'name' ), 'type'=>'text', 'value' => 'text-field-' . GenerateStrongRandomKey( 6 ), 'tip'=> __( 'unique-id-and-name' ) ),
			'help-text' 	=> array( 'label'=> __( 'help-text' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'required' 		=> array( 'label'=> __( 'required-field' ), 'type'=>'checkbox', 'value' => "1", 'tip'=> null ),
		)
	),
	
	'password' => array( 
		'name' => 'password', 'title' => __( 'password' ), 'disabled' => false, 'allowInTables' => false, 'style' => $genericElementDesingArray,  'data' => 
		array(
			'label' 		=> array( 'label'=> __( 'label' ), 'type'=>'text', 'value' => __( 'password' ), 'tip'=>null ),
			'class' 		=> array( 'label'=> __( 'class' ), 'type'=>'text', 'value' => 'form-control', 'tip'=> null ),
			'placeholder' 	=> array( 'label'=> __( 'placeholder' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'prepend' 		=> array( 'label'=> __( 'prepend' ), 'type'=>'text', 'value' => null, 'tip'=> __( 'prepend-field-tip' ) ),
			'append' 		=> array( 'label'=> __( 'append' ), 'type'=>'text', 'value' => null, 'tip'=> __( 'append-field-tip' ) ),
			'help-text' 	=> array( 'label'=> __( 'help-text' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'limit-length'	=> array( 'label'=> __( 'limit-length' ), 'type'=>'num', 'value' => 0, 'tip'=> __( 'limit-length-tip' ), 'min' => 0, 'max' => 100 ),
			'name'  		=> array( 'label'=> __( 'name' ), 'type'=>'text', 'value' => 'password-field-' . GenerateStrongRandomKey( 6 ), 'tip'=> __( 'unique-id-and-name' ) ),
			'display-label' => array( 'label'=> __( 'display-label' ), 'type'=>'checkbox', 'value' => "1", 'tip'=> null ),
			'required' 		=> array( 'label'=> __( 'required-field' ), 'type'=>'checkbox', 'value' => "1", 'tip'=> null ),
		)
	),
	
	//TODO:
	'checkbox-group' => array( 
		'name' => 'checkbox-group', 'title' => __( 'checkbox-group' ), 'disabled' => false, 'allowInTables' => false, 'data' => 
		array()
	),
);

//Create the table array
$genericTablesArray = array
(
	'button' => array( 
		'name' => 'button', 'title' => __( 'button' ), 'disabled' => false, 'data' => 
		array(
			'label' => array( 'label'=> __( 'label' ), 'type'=>'text', 'value' => __( 'button' ), 'tip'=>null ),			
			'type' 	=> array( 'label'=> __( 'type' ), 'type'=>'select', 'value' => __( 'button' ), 'tip'=>null, 
				'options' => array(
					'button' => array( 'name' => __( 'button' ) ),
					'submit' => array( 'name' => __( 'submit' ) ),
					'reset' => array( 'name' => __( 'reset' ) )
				)
			),
			'class' => array( 'label'=> __( 'class' ), 'type'=>'text', 'value' => null, 'tip'=> __( 'button-class-tip' ) ),
			'value' => array( 'label'=> __( 'value' ), 'type'=>'text', 'value' => null, 'tip'=> null )
		)
	),
	
	'space' => array( 
		'name' => 'space', 'title' => __( 'space' ), 'disabled' => false, 'data' => 
		array(
			'width' => array( 'label'=> __( 'width' ), 'type'=>'text', 'value' => null, 'tip'=> __( 'space-width-tip' ) )
		)
	),
	
	'text' => array( 
		'name' => 'text', 'title'=> __( 'text' ), 'disabled' => false, 'style' => $genericElementDesingArray, 'data' => 
		array(
			'class' => array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' ),
			'value' => array( 'label'=> __( 'value' ), 'type'=>'text', 'value' => '' )
		)
	),
	
	'date' => array( 
		'name' => 'date', 'title'=> __( 'date' ), 'disabled' => false, 'style' => $genericElementDesingArray, 'data' => 
		array(
			'class' => array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' )
		)
	),
	
	'last-time-checked' => array( 
		'name' => 'last-time-checked', 'title'=> __( 'last-time-checked' ), 'disabled' => false, 'tip' => __( 'last-time-checked-tip' ), 'style' => $genericElementDesingArray, 'data' => 
		array(
			'class' => array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' )
		)
	),
	
	'dot' => array( 
		'name' => 'dot', 'title' => __( 'dot' ), 'disabled' => false, 'data' => 
		array(
			'font-size' => array( 'label'=> __( 'size' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'color' => array( 'label'=> __( 'color' ), 'type'=>'text', 'value' => null, 'colorpicker' => true, 'placeholder' => '#000', 'tip'=> null ),
			
			'padding' => array(
				'name' => 'padding', 'label' => __( 'padding' ), 'disabled' => false, 'tip'=>null, 'type'=>'item-group', 'items' => 
				array(
					'padding-top' => array( 'name' => 'padding-top', 'type'=>'text', 'placeholder' => 'top' ),
					'padding-bottom' => array( 'name' => 'padding-bottom', 'type'=>'text', 'placeholder' => 'bottom' ),
					'padding-left' => array( 'name' => 'padding-left', 'type'=>'text', 'placeholder' => 'left' ),
					'padding-right' => array( 'name' => 'padding-right', 'type'=>'text', 'placeholder' => 'right' )
				)
			)
		)
	),
	
	'number-of-visits' => array( 
		'name' => 'number-of-visits', 'title'=> __( 'number-of-visits' ), 'disabled' => false, 'tip' => __( 'number-of-visits-tip' ), 'style' => $genericElementDesingArray, 'data' => 
		array(
			'class' => array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' )
		)
	),
	
	'category' => array( 
		'name' => 'category', 'title'=> __( 'category' ), 'disabled' => false, 'style' => $genericElementDesingArray, 'data' => 
		array(
			'class' 	=> array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' ),
			'on-click' 	=> array( 'label'=> __( 'action-on-click' ), 'type'=>'select', 'value' => null, 'tip'=>null, 
				'options' => array(
					'nothing' 	=> array( 'name' => __( 'do-nothing' ) ),
					'go-to-archive-page' => array( 'name' => __( 'go-to-archive-page' ) ),
					'go-to-archive-page-new-tab' => array( 'name' => __( 'go-to-archive-page-new-tab' ) )
				)
			)
		)
	),
	
	'store-name' => array( 
		'name' => 'store-name', 'title'=> __( 'store-name' ), 'disabled' => false, 'style' => $genericElementDesingArray, 'data' => 
		array(
			'class' 	=> array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' ),
			'on-click' 	=> array( 'label'=> __( 'action-on-click' ), 'type'=>'select', 'value' => null, 'tip'=>null, 
				'options' => array(
					'nothing' 	=> array( 'name' => __( 'do-nothing' ) ),
					'go-to-store-page' => array( 'name' => __( 'go-to-store-page' ) ),
					'go-to-store-page-new-tab' => array( 'name' => __( 'go-to-store-page-new-tab' ) )
				)
			)
		)
	),
	
	'store-image' => array( 
		'name' => 'store-image', 'title'=> __( 'store-image' ), 'disabled' => false, 'style' => $imageElementsDesingArray, 'data' => 
		array(
			'class' 	=> array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' ),
			'on-click' 	=> array( 'label'=> __( 'action-on-click' ), 'type'=>'select', 'value' => null, 'tip'=>null, 
				'options' => array(
					'nothing' 	=> array( 'name' => __( 'do-nothing' ) ),
					'go-to-store-page' => array( 'name' => __( 'go-to-store-page' ) ),
					'go-to-store-page-new-tab' => array( 'name' => __( 'go-to-store-page-new-tab' ) )
				)
			)
		)
	),
	
	'price' => array( 
		'name' => 'price', 'title'=> __( 'price' ), 'disabled' => false, 'tip' => __( 'price-column-tip' ), 'style' => $genericElementDesingArray, 'data' => 
		array(
			'class' 	=> array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' ),
			'price-template' => array( 'label'=> __( 'price-template' ), 'type'=>'select', 'value' => null, 'tip'=>null, 
				'options' => array(
					'sale-price' => array( 'name' => __( 'sale-price' ) ),
					'regular-price' => array( 'name' => __( 'regular-price' ) ),
					'regular-sale-price' => array( 'name' => __( 'regular-price' ) . ' - ' . __( 'sale-price' ) ),
					'sale-regular-price' => array( 'name' => __( 'sale-price' ) . ' - ' . __( 'regular-price' ) ),
				)
			),
			
			'currency' => array( 'label'=> __( 'currency' ), 'type'=>'currency', 'value' => '' ),

			'on-click' 	=> array( 'label'=> __( 'action-on-click' ), 'type'=>'select', 'value' => null, 'tip'=>null, 
				'options' => array(
					'nothing' 	=> array( 'name' => __( 'do-nothing' ) ),
					'open-page' => array( 'name' => __( 'open-post-page' ) ),
					'open-page-new-tab' => array( 'name' => __( 'open-post-page-new-tab' ) ),
					'open-store-new-tab' => array( 'name' => __( 'go-to-store-page-new-tab' ) )
				)
			)
		)
	),
	
	'attribute' => array( 
		'name' => 'attribute', 'title'=> __( 'attribute' ), 'disabled' => false, 'style' => $genericElementDesingArray, 'data' => 
		array(
			'class' 	=> array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' ),
			'attribute' => array( 'label'=> __( 'attribute' ), 'type'=>'attribute', 'value' => '' )
		)
	),
	
	'cover-image' => array( 
		'name' => 'cover-image', 'title'=> __( 'cover-image' ), 'disabled' => false, 'style' => $imageElementsDesingArray, 'data' => 
		array(
			'display-placeholder' => array( 'label'=> __( 'display-placeholder-if-the-image-is-not-available' ), 'type'=>'checkbox', 'value' 	 => "1", 'tip'=> null ),
			'class' 	=> array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' ),
			'on-click' 	=> array( 'label'=> __( 'action-on-click' ), 'type'=>'select', 'value' => null, 'tip'=>null, 
				'options' => array(
					'nothing' 	=> array( 'name' => __( 'do-nothing' ) ),
					'open-page' => array( 'name' => __( 'open-post-page' ) ),
					'open-page-new-tab' => array( 'name' => __( 'open-post-page-new-tab' ) ),
					'open-cover-new-tab' => array( 'name' => __( 'open-cover-image-new-tab' ) )
				)
			),
		)
	),
	
	'title' => array( 
		'name' => 'html-tag', 'title'=> __( 'title' ), 'disabled' => false, 'style' => $genericElementDesingArray, 'data' => 
		array(
			'link-title' => array( 'label'=> __( 'link-title-to-the-posts-page' ), 'type'=>'checkbox', 'value' => "1", 'tip'=> null ),
			'new-page' => array( 'label'=> __( 'open-the-post-link-on-a-new-page' ), 'type'=>'checkbox', 'value' => "1", 'tip'=> null ),
			'type' 	=> array( 'label'=> __( 'html-tag' ), 'type'=>'select', 'value' => null, 'tip'=>null, 
				'options' => array(
					'span' => array( 'name' => 'span' ),
					'h1' => array( 'name' => 'H1' ),
					'h2' => array( 'name' => 'H2' ),
					'h3' => array( 'name' => 'H3' ),
					'h4' => array( 'name' => 'H4' )
				)
			),
			'html-class' 	=> array( 'label'=> __( 'html-class' ), 'type'=>'text', 'value' => '' ),
		)
	),
);

//Create the table header array
$genericTablesHeaderArray = array
(
	'dot' => array( 
		'name' => 'dot', 'title' => __( 'dot' ), 'disabled' => false, 'data' => 
		array(
			'font-size' => array( 'label'=> __( 'size' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'color' => array( 'label'=> __( 'color' ), 'type'=>'text', 'value' => null, 'colorpicker' => true, 'placeholder' => '#000', 'tip'=> null ),
			
			'padding' => array(
				'name' => 'padding', 'label' => __( 'padding' ), 'disabled' => false, 'tip'=>null, 'type'=>'item-group', 'items' => 
				array(
					'padding-top' => array( 'name' => 'padding-top', 'type'=>'text', 'placeholder' => 'top' ),
					'padding-bottom' => array( 'name' => 'padding-bottom', 'type'=>'text', 'placeholder' => 'bottom' ),
					'padding-left' => array( 'name' => 'padding-left', 'type'=>'text', 'placeholder' => 'left' ),
					'padding-right' => array( 'name' => 'padding-right', 'type'=>'text', 'placeholder' => 'right' )
				)
			)
		)
	),
	
	'text' => array( 
		'name' => 'text', 'title'=> __( 'text' ), 'disabled' => false, 'data' => 
		array(
			'class' => array( 'label'=> __( 'class' ), 'type'=>'text', 'value' => '' ),
			'value' => array( 'label'=> __( 'value' ), 'type'=>'text', 'value' => '' )
		)
	),

	'header' => array( 
		'name' => 'header', 'title' => __( 'header' ), 'disabled' => false, 'data' => 
		array(
			'type' => array( 'label'=> __( 'type' ), 'type'=>'select', 'value' => '', 'tip'=>null, 
				'options' => array(
					'h1' => array( 'name' => 'h1' ),
					'h2' => array( 'name' => 'h2' ),
					'h3' => array( 'name' => 'h3' ),
					'h4' => array( 'name' => 'h4' ),
					'h5' => array( 'name' => 'h5' ),
					'h6' => array( 'name' => 'h6' )
				)
			),
			'value' => array( 'label'=> __( 'value' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'class' => array( 'label'=> __( 'class' ), 'type'=>'text', 'value' => '', 'tip'=> null ),
		)
	),
	
	'html' => array( 
		'name' => 'html', 'title' => __( 'html' ), 'disabled' => false, 'data' => 
		array(
			'html' 	=> array( 'label'=> __( 'html' ), 'type'=>'text-area', 'value' => null, 'tip'=> null ),
			'class' => array( 'label'=> __( 'class' ), 'type'=>'text', 'value' => 'form-control', 'tip'=> null )
		)
	),
	
	'space' => array( 
		'name' => 'space', 'title' => __( 'space' ), 'disabled' => false, 'data' => 
		array(
			'width' => array( 'label'=> __( 'width' ), 'type'=>'text', 'value' => null, 'tip'=> __( 'space-width-tip' ) )
		)
	),
	
	'sorting' => array( 
		'name' => 'sorting', 'title' => __( 'sorting' ), 'disabled' => false, 'data' => 
		array(
			'class' => array( 'label'=> __( 'class' ), 'type'=>'text', 'value' => null, 'tip'=> null ),
			'sort-by' => array( 'label' => __( 'sort-by' ), 'type'=>'select', 'value' => null, 'tip'=>null, 
				'options' => 
					array(
						'title' 		=> array( 'name' => __( 'title' ) ),
						'price' 		=> array( 'name' => __( 'price' ) ),
						'popularity' 	=> array( 'name' => __( 'popularity' ) ),
						'rating' 		=> array( 'name' => __( 'rating' ) ),
						'date' 			=> array( 'name' => __( 'date' ) ),
						'category' 		=> array( 'name' => __( 'category' ) ),
						'attribute' 	=> array( 'name' => __( 'attribute' ) )
				)
			)
		)
	),
);

/*
//To avoid copy each item, let's fill the table array automatically
foreach ( $genericFormsArray as $gID => $gData )
{
	if ( !isset( $gData['allowInTables'] ) || !$gData['allowInTables'] )
		continue;
	
	$genericTablesArray[$gID] = array(
		'name' 		=> $gData['name'], 
		'title' 	=> $gData['title'], 
		'disabled' 	=> $gData['disabled'],
		'data' 		=> $gData['data']
	);
}

//Do the same for table header array
foreach ( $genericFormsArray as $gID => $gData )
{
	if ( !isset( $gData['allowInTableHeader'] ) || !$gData['allowInTableHeader'] )
		continue;
	
	$genericTablesHeaderArray[$gID] = array(
		'name' 		=> $gData['name'], 
		'title' 	=> $gData['title'], 
		'disabled' 	=> $gData['disabled'],
		'data' 		=> $gData['data']
	);
}
*/
//email Notifications Group Array
$emailNotificationsGroup = array(
		'is' => array( 'name' => 'is', 'title'=> __( 'is' ), 'disabled' => false, 'data' => array() ),
		'is-not' => array( 'name' => 'is-not', 'title'=> __( 'is-not' ), 'disabled' => false, 'data' => array() ),
		'empty' => array( 'name' => 'empty', 'title'=> __( 'empty' ), 'disabled' => false, 'data' => array() ),
		'not-empty' => array( 'name' => 'not-empty', 'title'=> __( 'not-empty' ), 'disabled' => false, 'data' => array() ),
		'contains' => array( 'name' => 'contains', 'title'=> __( 'contains' ), 'disabled' => false, 'data' => array() ),
		'does-not-contain' => array( 'name' => 'does-not-contain', 'title'=> __( 'does-not-contain' ), 'disabled' => false, 'data' => array() ),
		'starts-with' => array( 'name' => 'starts-with', 'title'=> __( 'starts-with' ), 'disabled' => false, 'data' => array() ),
		'ends-with' => array( 'name' => 'ends-with', 'title'=> __( 'ends-with' ), 'disabled' => false, 'data' => array() ),
);