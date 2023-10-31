<?php
	$atts = AdminGetAttributes( $Admin->GetSite(), $Admin->GetLang(), $Admin->GetBlog(), ( $Post->IsPage() ? 0 : $Post->Category()->id ) );
?>
<h5><?php echo $L['post-attributes'] ?></h5>
<div class="mb-3">
<?php if ( !empty( $atts ) ) :
	foreach ( $atts as $att ) :
		$attTrans = Json( $att['trans_data'] );
		$attName = ( ( !empty( $attTrans ) && isset( $attTrans['lang-' . $Post->Language()->id] ) ) ? $attTrans['lang-' . $Post->Language()->id]['value'] : $att['name'] );
		
		$attValues = AdminGetPostAttributesData( $att['id'], $Post->PostID() );
?>
	<div id="attrFieldGroup<?php echo $att['id'] ?>">
		<div class="form-group">
			<label class="form-label" for="att<?php echo $att['id'] ?>"><?php echo $attName ?></label>
			<?php if ( empty( $attValues ) ) : ?>
			<div class="form-group">
				<textarea class="form-control" rows="2" name="att[<?php echo $att['id'] ?>][0][]" cols="50" id="att<?php echo $att['id'] ?>"></textarea>
			</div>
			<?php else :
				foreach( $attValues as $attVal ) : ?>
				<div class="form-group">
					<textarea class="form-control" rows="2" name="att[<?php echo $att['id'] ?>][<?php echo $attVal['id'] ?>]" cols="50" id="att<?php echo $att['id'] ?>"><?php echo $attVal['value'] ?></textarea>
				</div>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</div>
	
	<button type="button" data-id="<?php echo $att['id'] ?>" data-toggle="tooltip" title="<?php echo sprintf( __( 'add-new-field-for-att' ), $attName ) ?>" class="btn btn-primary float-right mt-2 mb-1 newAttFieldButton"><i class="fa fa-plus-circle"></i></button>
	<br />
	<?php endforeach ?>
<?php else : ?>
	<div class="alert alert-warning" role="alert">
		<?php echo sprintf( $L['no-attributes-post-tip'], $Admin->GetUrl( 'post-attributes' ) ) ?>
	</div>
<?php endif ?>
</div>