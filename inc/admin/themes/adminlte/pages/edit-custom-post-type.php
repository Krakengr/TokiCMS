<div class="row">
  <div class="col-12">
  <div class="card">
  <div class="card-body">
	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
	<div class="form-row">
    <div class="form-group col-md-6">
		<h4><?php echo $L['edit-custom-post-type'] ?>: <?php echo $Custom['title'] ?></h4>
			
		<div class="form-group">
			<label for="inputTitle"><?php echo $L['title'] ?></label>
			<input type="text" class="form-control" name="title" id="inputTitle" value="<?php echo htmlspecialchars( $Custom['title'] ) ?>">
			<small id="titleHelp" class="form-text text-muted"><?php echo $L['add-title-tip'] ?></small>
		</div>

		<div class="form-group">
			<label for="inputSlug"><?php echo $L['slug'] ?></label>
			<input type="text" class="form-control" id="inputSlug" name="slug" value="<?php echo htmlspecialchars( $Custom['sef'] ) ?>">
			<small id="slugHelp" class="form-text text-muted"><?php echo $L['category-slug-tip'] ?></small>
		</div>

		<div class="form-group">
			<label for="inputDescription"><?php echo $L['description'] ?></label>
			<textarea class="form-control" id="inputDescription" name="description" rows="3"><?php echo htmlspecialchars( $Custom['description'] ) ?></textarea>
			<small id="descriptionHelp" class="form-text text-muted"><?php echo $L['category-descr-tip'] ?></strong></small>
		</div>

		<div class="form-group">
			<label for="postTypeParent"><?php echo $L['parent'] ?></label>
			<select class="form-control form-select" id="postTypeParent" name="postTypeParent">
				<option value="0"><?php echo $L['none'] ?></option>
			<?php 
			if ( !empty( $PostTypes ) ) : 
				foreach( $PostTypes as $type ) : 
				
					if ( $type['id'] == $Custom['id'] )
						continue;
			?>
				<option value="<?php echo $type['id'] ?>" <?php echo ( ( ( $Custom['id_parent'] > 0 ) && ( $Custom['id_parent'] == $type['id'] ) ? 'selected' : '' ) ) ?>><?php echo $type['title'] ?></option>
				<?php unset( $type ); endforeach; endif; ?>
			</select>
			<small id="parentHelp" class="form-text text-muted"><?php echo $L['custom-post-type-parent-tip'] ?></strong></small>
		</div>
		
		<?php if ( $Admin->MultiLang() && !empty( $Langs ) ) : ?>
		<hr />
		<h4><?php echo __( 'translations' ) ?></h4>
		<?php 
			$transData = Json( $Custom['trans_data'] );
			
			foreach( $Langs as $li => $la ) :

				$cTitle  = ( ( !empty( $transData ) && isset( $transData[$la['lang']['code']] ) ) ? $transData[$la['lang']['code']]['title'] : '' );
				
				$cDescr  = ( ( !empty( $transData ) && isset( $transData[$la['lang']['code']] ) ) ? $transData[$la['lang']['code']]['description'] : '' );
		?>
		<h6 class="mt-4 mb-2 pb-2 border-bottom text-uppercase">
			<img src="<?php echo  SITE_URL . 'languages' . PS . 'flags' . PS . $la['lang']['flagicon'] ?>" title="<?php echo $la['lang']['title'] ?>" alt="<?php echo $la['lang']['title'] ?>" width="16" height="11" style="width: 16px; height: 11px;" />
			<?php echo $la['lang']['title'] ?>
		</h6>
				
		<div class="form-group">
			<label for="lang-<?php echo $la['lang']['id'] ?>-title" class="col-sm-2 col-form-label"><?php echo $L['title'] ?></label>
			<input class="form-control " id="lang-<?php echo $la['lang']['id'] ?>-title" name="trans[<?php echo $li ?>][title]" value="<?php echo $cTitle ?>" type="text" />
			<small id="customTitleHelpLang-<?php echo $la['lang']['id'] ?>" class="form-text text-muted"><?php echo sprintf( $L['title-translation-tip'], $la['lang']['title'] ) ?></small>
		</div>
				
		<div class="form-group">
			<label for="lang-<?php echo $la['lang']['id'] ?>-descr" class="col-sm-2 col-form-label"><?php echo $L['description'] ?></label>
			<textarea class="form-control" id="lang-<?php echo $la['lang']['id'] ?>-descr" name="trans[<?php echo $li ?>][description]" rows="3"><?php echo htmlspecialchars( $cDescr ) ?></textarea>
			<small id="customDescrHelpLang-<?php echo $la['lang']['id'] ?>" class="form-text text-muted"><?php echo sprintf( $L['description-translation-tip'], $la['lang']['title'] ) ?></strong></small>
		</div>
		<?php endforeach ?>
	<?php endif ?>
		<hr />
		<?php 
		$defaultImage = HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS . 'default.svg';

		$customImage = GetMainImageUrl( $Custom['id_image'] );
		?>
		<div class="form-group">
			<label for="categoryImage"><?php echo $L['featured-image'] ?></label>
			<div class="col-lg-8 col-sm-12 p-0 text-center">
				<img id="customLogoPreview" width="400" class="img-fluid img-thumbnail" alt="<?php echo $L['featured-image'] . ' ' . $L['preview'] ?>" src="<?php echo ( $customImage ? $customImage : $defaultImage ) ?>" />
			</div>
			
			<div class="card-footer">
				<button type="button" class="btn btn-primary float-left" data-toggle="modal" data-target="#addImage" id="customImageModal"><i class="far fa-image"></i> <?php echo __( 'add-media' ) ?></button>
				
				<button type="button" class="btn btn-danger float-right<?php echo ( $customImage ? '' : ' d-none' ) ?>" id="buttonRemoveLogo"><i class="fa fa-trash"></i> <?php echo __( 'remove-logo' ) ?></button>
			</div>
			<input type="hidden" id="customLogoFile" name="customLogoFile" value="<?php echo ( $customImage ? $Custom['id_image'] : 0 ) ?>">		
		</div>
		
		<hr />
		
		<div class="form-group">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
				<label class="form-check-label" for="deleteCheckBox">
					<?php echo $L['delete'] ?>
				</label>
				<small id="deleteHelp" class="form-text text-muted"><?php echo $L['delete-custom-post-type-tip'] ?></small>
			</div>
		</div>
		
		<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_custom_type_' . $Custom['id'] ) ?>">
		
		<div class="align-middle">
			<div class="float-left mt-1">
				<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
				<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'custom-post-types' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
			</div>
		</div>
	</div>
	</div>
	</form>
</div>
</div>
</div>
</div>