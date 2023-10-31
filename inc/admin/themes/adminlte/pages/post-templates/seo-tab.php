<?php $xtraDataSeo = ( isset( $xtraData['seo'] ) ? $xtraData['seo']['seo'] : null ) ?>
<h5><?php echo $L['meta-robots-settings'] ?></h5>
<div class="mb-3">
	<?php 
	$args = array(
			'id' => 'no-index',
			'label' => __( 'no-index' ),
			'name' => 'seo[noindex]',
			'checked' => ( ( isset( $xtraDataSeo['noindex'] ) && !empty( $xtraDataSeo['noindex'] ) ) ? true : false ), 
			'disabled' => false,
			'tip' => __( 'no-index-tip' )
	);

	CheckBox( $args );
	?>
	
	<?php 
	$args = array(
			'id' => 'no-follow',
			'label' => __( 'no-follow' ),
			'name' => 'seo[nofollow]',
			'checked' => ( ( isset( $xtraDataSeo['nofollow'] ) && !empty( $xtraDataSeo['nofollow'] ) ) ? true : false ), 
			'disabled' => false,
			'tip' => __( 'no-follow-tip' )
	);

	CheckBox( $args );
	?>
	
	<?php 
	$args = array(
			'id' => 'noimageindex',
			'label' => __( 'noimageindex' ),
			'name' => 'seo[noimageindex]',
			'checked' => ( ( isset( $xtraDataSeo['noimageindex'] ) && !empty( $xtraDataSeo['noimageindex'] ) ) ? true : false ), 
			'disabled' => false,
			'tip' => __( 'noimageindex-tip' )
	);

	CheckBox( $args );
	?>
	
	<?php 
	$args = array(
			'id' => 'noodp',
			'label' => __( 'noodp' ),
			'name' => 'seo[noodp]',
			'checked' => ( ( isset( $xtraDataSeo['noodp'] ) && !empty( $xtraDataSeo['noodp'] ) ) ? true : false ), 
			'disabled' => false,
			'tip' => __( 'noodp-tip' )
	);

	CheckBox( $args );
	?>
	
	<?php 
	$args = array(
			'id' => 'nosnippet',
			'label' => __( 'nosnippet' ),
			'name' => 'seo[nosnippet]',
			'checked' => ( ( isset( $xtraDataSeo['nosnippet'] ) && !empty( $xtraDataSeo['nosnippet'] ) ) ? true : false ), 
			'disabled' => false,
			'tip' => __( 'nosnippet-tip' )
	);

	CheckBox( $args );
	?>
	
	<?php 
	$args = array(
			'id' => 'noarchive',
			'label' => __( 'noarchive' ),
			'name' => 'seo[noarchive]',
			'checked' => ( ( isset( $xtraDataSeo['noarchive'] ) && !empty( $xtraDataSeo['noarchive'] ) ) ? true : false ), 
			'disabled' => false,
			'tip' => __( 'noarchive-tip' )
	);

	CheckBox( $args );
	?>
</div>