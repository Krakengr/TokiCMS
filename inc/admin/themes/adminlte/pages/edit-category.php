<?php
	require ( ARRAYS_ROOT . 'seo-arrays.php');
	
	$cGroup = ( !empty( $Cat['groups_data'] ) ? Json( $Cat['groups_data'] ) : null );
?><div class="row">
  <div class="col-12">
  <div class="card">
  <div class="card-body">
	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
	<div class="form-row">
    <div class="form-group col-md-10">
		<h4><?php echo $L['edit-category'] ?>: <?php echo $Cat['name'] ?></h4>
			
		<div class="form-group">
			<label for="inputTitle"><?php echo $L['title'] ?></label>
			<input type="text" class="form-control" name="title" id="inputTitle" value="<?php echo htmlspecialchars( $Cat['name'] ) ?>">
			<small id="titleHelp" class="form-text text-muted"><?php echo $L['add-title-tip'] ?></small>
		</div>

		<div class="form-group">
			<label for="inputSlug"><?php echo $L['slug'] ?></label>
			<input type="text" class="form-control" id="inputSlug" name="slug" value="<?php echo htmlspecialchars( $Cat['sef'] ) ?>">
			<small id="slugHelp" class="form-text text-muted"><?php echo $L['category-slug-tip'] ?></small>
		</div>

		<div class="form-group">
			<label for="categoryParent"><?php echo $L['parent'] ?></label>
			<select class="form-select" id="categoryParent" name="categoryParent">
			  <option value="0"><?php echo $L['none'] ?></option>
			  <?php if ( !empty( $cats ) ) :
					foreach( $cats as $c ) : 

					if ( ( isset( $c['isChild'] ) && $c['isChild'] ) || ( $c['id'] == $Cat['id'] ) )
						continue;
				?>
					<option value="<?php echo $c['id'] ?>" <?php echo ( ( ( $Cat['id_parent'] > 0 ) && ( $Cat['id_parent'] == $c['id'] ) ? 'selected' : '' ) ) ?>><?php echo $c['name'] ?></option>
				<?php unset( $c ); endforeach; endif; ?>
			</select>
			<small id="parentHelp" class="form-text text-muted"><?php echo $L['category-parent-tip'] ?></strong></small>
		</div>
		
		<div class="form-group">
			<label for="inputFrontpagePosts"><?php echo $L['limit-number-of-posts'] ?></label>
			<input value="<?php echo ( empty( $Cat['article_limit'] ) ? $Admin->Settings()::Get()['article_limit'] : $Cat['article_limit'] ) ?>" type="number" class="form-control border-width-2" name="article_limit" step="any" min="1" max="100" >
			<small id="inputFrontpagePostsHelp" class="form-text text-muted"><?php echo $L['limit-number-of-posts-category-tip'] ?></small>
		</div>

		<div class="form-group">
			<label for="category-format"><?php echo $L['description'] ?></label>
			<textarea class="form-control" id="category-format" name="description" rows="3"><?php echo htmlspecialchars( $Cat['descr'] ) ?></textarea>
			<br />
			
			<?php foreach( $categoryCustomMetaFormat as $cusId => $cus ) : ?>
				<button type="button" class="btn btn-secondary btn-sm" id="<?php echo $cus['id'] ?>" data-value="{{<?php echo $cusId ?>}} "><?php echo $cus['value'] ?></button>
				
				<script type="text/javascript">
				$('button[id^="<?php echo $cus['id'] ?>"]').on('click', function() {
					var $target = $('#category-format'),
					text = $('#category-format').val(),
					buttonVal = $(this).data('value');
					$target.val(`${text}${buttonVal}`);
				});
			</script>
			<?php endforeach ?>

			<small id="descriptionHelp" class="form-text text-muted"><?php echo $L['category-descr-tip'] ?> <?php echo __( 'variables-allowed' ) ?>: <?php foreach( $categoryCustomMetaFormat as $cusId => $cus ) : ?><code>{{<?php echo $cusId ?>}}</code> <?php endforeach ?>.</small>
			
		</div>

		<?php if ( !$Cat['is_default'] && ( MULTISITE || $Admin->MultiLang() || $Admin->MultiBlog() ) ) : ?>
		<div class="form-group">
			<label for="inputCatFilterTrans"><?php echo $L['move-category'] ?></label>
			<select class="form-select shadow-none" style="width: 100%; height:36px;" name="move-category" aria-label="Move select">
					<option value="0" selected><?php echo $L['choose'] ?>...</option>
					<?php if ( MULTISITE && !empty( $sites ) ) : ?>
						<optgroup label="<?php echo $L['sites'] ?>">
						<?php if ( ( $Admin->GetSite() != SITE_ID ) && ( $Cat['id_site'] != SITE_ID ) ) : ?>
							<option value="site::<?php echo SITE_ID ?>"><?php echo $Admin->DefaultSiteName() ?></option>
						<?php endif ?>
							<?php 
							//$sites value is in "navbar.php" file
							if ( !empty( $sites ) ) :
									foreach ( $sites as $singeSite ) :
										if ( $Cat['id_site'] == $singeSite['id'] )
											continue;
							?>
							<option value="site::<?php echo $singeSite['id'] ?>"><?php echo $singeSite['title'] ?></option>
							<?php endforeach ?>
							<?php endif ?>	
						</optgroup>
					<?php endif ?>
					
					<?php if ( $Admin->MultiLang() && !empty( $dataLangs ) ) : ?>
						<optgroup label="<?php echo $L['langs'] ?>">
						<?php if ( ( $Admin->DefaultLang()['id'] != $Admin->GetLang() ) && ( $Cat['id_lang'] != $Admin->DefaultLang()['id'] ) ): ?>
							<option value="lang::<?php echo $Admin->DefaultLang()['id'] ?>"><?php echo $Admin->DefaultLang()['title'] ?></option>
						<?php endif ?>	
							<?php foreach( $dataLangs as $lId => $lData ) :
								if ( $Cat['id_lang'] == $lId )
									continue;
							?><option value="lang::<?php echo $lId ?>"><?php echo $lData['lang']['title'] ?></option>
							<?php endforeach ?>
						</optgroup>
					<?php endif ?>
					
					<?php if ( $Admin->MultiBlog() && !empty( $dataBlogs ) ) : ?>
						<optgroup label="<?php echo $L['blogs'] ?>">
						<?php 
							foreach( $dataBlogs as $blog ) :
								if ( ( $Cat['id_blog'] > 0 ) && ( $Cat['id_blog'] == $blog['id_blog'] ) )
									continue;
						?><option value="blog::<?php echo $blog['id_blog'] ?>"><?php echo $blog['name'] ?></option>
						<?php endforeach ?>
						</optgroup>
					<?php endif ?>
				</select>
			<small id="inputCatFilterTransHelp" class="form-text text-muted"><?php echo $L['move-category-tip'] ?></strong></small>
		</div>
		<?php endif ?>

		<div class="form-group">
			<label for="inputFrontpagePage"><?php echo $L['membergroups'] ?></label>
			<select  name="membergroups[]" class="form-control select2 form-select shadow-none mt-3" multiple id="slcAmp" >
				<?php $groups = AdminGroups( $Admin->GetSite(), false );
					if ( !empty( $groups ) ) :
						foreach( $groups as $group ) : ?>
						<option  value="<?php echo $group['id_group'] ?>" <?php echo ( ( !empty( $cGroup ) && in_array( $group['id_group'], $cGroup ) ) ? 'selected' : '' ) ?>><?php echo $group['group_name'] ?></option>
					<?php endforeach ?>
				<?php endif ?>
			</select>
			<small id="membergroupsHelp" class="form-text text-muted"><?php echo $L['select-category-membergroup-tip'] ?></small>
		</div>
		
		<?php 
		//Only categories assigned in other languages but not the default 
		if ( $Admin->MultiLang() && !$Admin->IsDefaultLang() ) : 
			$translations = GetCatTrans( $Cat['id_trans_parent'] );
		?>
		<div class="form-group">
		<!-- Search for Parent Category -->
		<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="catParent"><?php echo $L['translation-parent'] ?></label>
			<select id="catParent" style="width: 100%; height:36px;" name="transParent" class="select2">
			<?php if ( $translations ) : ?>
					<option  value="<?php echo $translations['id'] ?>"><?php echo $translations['name'] ?></option>
			<?php endif ?>
			</select>
			<small class="form-text text-muted"><?php echo $L['start-typing-a-title-to-see-a-list-of-suggestions'] ?></small>
		</div>
		<?php endif ?>

		<div class="form-group">
			<label><?php echo __( 'category-color' ) ?></label>
			<input type="text" name="color" id="cp" value="<?php echo $Cat['cat_color'] ?>" class="form-control">
			<small id="categoryColorHelp" class="form-text text-muted"><?php echo __( 'category-color-tip' ) ?></small>
		</div>
		
		<div class="form-group">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="hideFront" id="hideCheckBox" <?php echo ( $Cat['hide_front'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="hideFrontCheckBox">
					<?php echo $L['hide-category-from-the-homepage'] ?>
				</label>
				<small id="hideFrontHelp" class="form-text text-muted"><?php echo $L['hide-category-from-the-homepage-tip'] ?></small>
			</div>
		</div>
		
		<?php if ( ( $Admin->GetBlog() > 0 ) && ( $Cat['id_blog'] > 0 ) ) : ?>
		<div class="form-group">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="hideBlogPage" id="hideBlogPageCheckBox" <?php echo ( $Cat['hide_blog'] ? 'checked' : '' ) ?>>
				<label class="form-check-label" for="hideBlogPageCheckBox">
					<?php echo $L['hide-category-from-the-blog-page'] ?>
				</label>
				<small id="hideFrontHelp" class="form-text text-muted"><?php echo $L['hide-category-from-the-blog-page-tip'] ?></small>
			</div>
		</div>
		<?php endif ?>
		
		<?php if ( IsAllowedTo( 'view-attachments' ) || IsAllowedTo( 'manage-attachments' ) ) : ?>
		<hr />
		<?php 
		$defaultImage = HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS . 'default.svg';

		$catImage = GetMainImageUrl( $Cat['id_image'] );
		?>
		<div class="form-group">
			<label for="categoryImage"><?php echo $L['featured-image'] ?></label>
			<div class="col-lg-8 col-sm-12 p-0 text-center">
				<img id="catLogoPreview" width="400" class="img-fluid img-thumbnail" alt="<?php echo $L['featured-image'] . ' ' . $L['preview'] ?>" src="<?php echo ( $catImage ? $catImage : $defaultImage ) ?>" />
			</div>
			
			<div class="card-footer">
				<button type="button" class="btn btn-primary float-left" data-toggle="modal" data-target="#addImage" id="catImageModal"><i class="far fa-image"></i> <?php echo __( 'add-media' ) ?></button>
				
				<button type="button" class="btn btn-danger float-right<?php echo ( $catImage ? '' : ' d-none' ) ?>" id="buttonRemoveLogo"><i class="fa fa-trash"></i> <?php echo __( 'remove-logo' ) ?></button>
			</div>
			<input type="hidden" id="catLogoFile" name="catLogoFile" value="<?php echo ( $catImage ? $Cat['id_image'] : 0 ) ?>">		
		</div>
		<?php endif ?>
		
		<?php if ( !$Cat['is_default'] ) : ?>
		<hr />
		
		<div class="form-group">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
				<label class="form-check-label" for="deleteCheckBox">
					<?php echo $L['delete'] ?>
				</label>
				<small id="titleHelp" class="form-text text-muted"><?php echo $L['delete-category-tip'] ?></small>
			</div>
		</div>
		<?php endif ?>
		
		<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_category_' . $Cat['id'] ) ?>">
		
		<div class="align-middle">
			<div class="float-left mt-1">
				<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
				<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'categories' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
			</div>
		</div>
	</div>
	</div>
	</form>
</div>
</div>
</div>
</div>