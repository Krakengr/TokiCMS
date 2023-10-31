<?php $xtraDataGallery = ( isset( $xtraData['gallery'] ) ? $xtraData['gallery'] : null ) ?>
<!-- Gallery Images -->
<div class="mb-3">
	<div id="gallery_wrap" class="widget meta-boxes">
		<div class="widget-title">
			<h4><span><?php echo __( 'gallery-images' ) ?></span></h4>
		</div>
		<div class="widget-body">
		<?php if ( $canViewAttachments ) : ?>
		<!-- Media Button -->
		<div class="form-group">
			<div class="d-inline-block editor-action-item">
				<a href="javascript: void(0);" data-toggle="modal" data-target="#addImage" id="imageGalleryModal" class="btn_gallery btn btn-outline-primary mb-4" data-id="<?php echo $Post->PostID() ?>" data-focus="false"> <i class="far fa-image"></i> <?php echo __( 'add-media' ) ?></a>
			</div>
		</div>
		<div class="clearfix"></div>
		<?php endif ?>
		<div>
			<div class="list-photos-gallery">
				<div class="row" id="list-photos-items">
				<?php if ( $xtraDataGallery ) :
						foreach ( $xtraDataGallery as $gId => $g ) :
				?>
					<div class="col-md-2 col-sm-3 col-4 photo-gallery-item" data-id="<?php echo $gId ?>"><div class="gallery_image_wrapper"><img src="<?php echo ( !empty( $g['childs'] ) ? $g['childs']['0']['url'] : $g['url'] ) ?>" alt="image"></div><input type="hidden" id="gallery_item_id" name="gallery[<?php echo $gId ?>]" value="<?php echo $g['url'] ?>"></div>
				<?php endforeach; endif; ?>
				</div>
			</div>
				<div class="clearfix"></div>
			<div class="form-group">
				<a href="javascript: void(0);" type="button" id="resetGallery" class="text-danger reset-gallery"><?php echo __( 'reset-gallery' ) ?></a>
			</div>
			</div>
		</div>
	</div>
</div>