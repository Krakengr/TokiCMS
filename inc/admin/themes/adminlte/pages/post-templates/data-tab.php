<?php include_once ( ARRAYS_ROOT . 'generic-arrays.php') ?>
<h5><?php echo __( 'data' ) ?></h5>
<div class="mb-3">
	<?php 
	foreach( $modelProductArray as $modId => $mod )
	{
		$args = array(
			'label' 		=> ( !empty( $mod['title'] ) ? $mod['title'] : null ),
			'tip' 			=> ( !empty( $mod['tip'] ) ? $mod['tip'] : null ),
			'id' 			=> $mod['name'],
			'name' 			=> 'pdata[' . $mod['dbname'] . ']',
			'value' 		=> null,
			'addAfterInput' => true,
			'placeholder' 	=> ( !empty( $mod['placeholder'] ) ? $mod['placeholder'] : null ),
			'required' 		=> ( !empty( $mod['required'] ) ? true : false ),
			'disabled' 		=> ( !empty( $mod['disabled'] ) ? true : false )
		);
		FormInput( $args );
	}
	?>
</div>