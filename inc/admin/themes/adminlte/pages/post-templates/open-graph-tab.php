<?php $xtraDataGraph = ( isset( $xtraData['seo']['graph'] ) ? $xtraData['seo']['graph'] : null ) ?>
<div class="alert alert-info" role="alert">
	<?php echo __( 'open-graph-edit-tip' )?>
</div>

<div class="mb-3">
	<div class="form-group">
		<label class="form-label" for="graphTitle"><?php echo __( 'title' )?></label>
		<input type="text" id="graphTitle" name="graph[title]" class="form-control mb-4" placeholder="<?php echo $L['enter-title'] ?>" value="<?php echo ( isset( $xtraDataGraph['title'] ) ? $xtraDataGraph['title'] : '' ) ?>">
	</div>
</div>

<div class="mb-3">
	<div class="form-group">
		<label class="col-sm-2 control-label" for="graphDescription"><?php echo __( 'description' )?></label>
		<textarea class="form-control" rows="4" placeholder="<?php echo __( 'enter-a-short-snippet-from-your-post' )?>" name="graph[description]" cols="50" id="graphDescription"><?php echo ( isset( $xtraDataGraph['description'] ) ? $xtraDataGraph['description'] : '' ) ?></textarea>
	</div>
</div>

<?php if ( $canViewAttachments ) : ?>
<div class="container">
	<div class="row">
		<div class="col-lg-4 col-sm-12 p-0 pr-2">
			<div class="form-group">
				<label class="col-sm-2 control-label" for="graphDescription"><?php echo __( 'image' )?></label>
				<button id="buttonRemoveGraph" type="button" class="btn btn-primary w-100 mt-4 mb-4 <?php echo ( ( isset( $xtraDataGraph['image'] ) && !empty( $xtraDataGraph['image'] ) ) ? '' : 'd-none' ) ?>"><i class="fa fa-trash"></i> <?php echo __( 'remove-image' ) ?></button>
			</div>
			<div class="form-group">
				<a href="javascript: void(0);" data-bs-toggle="modal" data-bs-target="#addImage" id="imageGraphModal" class="btn btn-outline-primary mb-4" data-id="<?php echo $Post->PostID() ?>" data-bs-focus="false"><?php echo __( 'add-media' ) ?></a>
			</div>
		</div>
		<div  id="thumbnail" class="thumbnail col-lg-8 col-sm-12 p-0 text-center">
			<img id="graphImagePreview" width="400" class="img-fluid img-thumbnail" alt="" src="<?php echo ( ( isset( $xtraDataGraph['image'] ) && !empty( $xtraDataGraph['image'] ) ) ? $xtraDataGraph['image'] : '' ) ?>" />
			<input type="hidden" name="graph[graphImageFile]" id="graphImageFile" value="<?php echo ( isset( $xtraDataGraph['image'] ) ? $xtraDataGraph['image'] : '' ) ?>" />
		</div>
	</div>
</div>
<?php endif ?>