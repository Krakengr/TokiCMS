<?php
	$postType = ( ( $Admin->CurrentAction() == 'add-post' ) ? 'post' : 'page' );
	$showAll = $Admin->Settings()::IsTrue( 'parent_site_shows_everything' );
?>
<div class="row">
	<div class="col-md-9 col-sm-9 col-md-push-3">
		<form action="" method="post" id="post" role="form">
		<?php 
			HiddenFormInput( array( 'name' => 'post_id', 'value' => 0 ) );
			HiddenFormInput( array( 'name' => 'post_lang_id', 'value' => $Admin->GetLang() ) );
			HiddenFormInput( array( 'name' => 'post_site_id', 'value' => $Admin->GetSite() ) );
			HiddenFormInput( array( 'name' => 'post_blog_id', 'value' => $Admin->GetBlog() ) );
			
			FormInput( array( 'name' => 'filesToUpload[]', 'id' => 'filesToUpload', 'xtra' => 'multiple hidden', 'type' => 'file' ) );
		?>
			<div class="card">
				<div class="card-header">
					<?php echo __( $Admin->CurrentAction() ) ?>
				</div>
				
				<div class="card-body">
					<div class="tab-content" id="postTabContent">
						<div class="col-xl-12">
							<!-- Title -->
							<div class="mb-3">
								<div class="form-group">
									<span class="charcounter" id="titleNum"></span>
									<label class="form-label required" for="postTitle"><?php echo $L['title'] ?></label>
									<input type="text" id="postTitle" name="title" onkeyup="countChar(this, 120, '#titleNum');" class="form-control mb-4" placeholder="<?php echo $L['enter-title'] ?>" value="" required />
								</div>
							</div>

							<!-- Slug -->
							<div class="mb-3">
								<div class="form-group ">
									<label class="form-label required" for="current-slug"><?php echo $L['permalink'] ?></label>
									<input type="text" id="current-slug" class="form-control" name="slug" placeholder="<?php echo $L['leave-empty-for-autocomplete'] ?>" value="">
									<script type='text/javascript'>
										// Generate slug when the user type the title 
										//TODO: test keyup funtion
										$('#postTitle').change(function(e) {
											$.post('<?php echo AJAX_ADMIN_PATH ?>slug/', 
											{ 'slug': $(this).val() }, 
												function( data ) {
													$('#current-slug').val(data);
												}
											);
										});
									</script>
								</div>
							</div>
										
							<!-- Description -->
							<div class="mb-3">
								<div class="form-group"><span class="charcounter" id="descrNum"></span>
									<label for="description" class="form-label"><?php echo $L['description'] ?></label>
									<textarea class="form-control" onkeyup="countChar(this, 400, '#descrNum');" rows="4" placeholder="<?php echo $L['enter-a-short-snippet-from-your-post'] ?>" name="description" cols="50" id="description"></textarea>
								 </div>
							</div>

							<!-- Content -->
							<!-- Content -->
							<div class="mb-3">
								<label for="mainEditor" class="form-label"><?php echo $L['content'] ?></label>
								<?php echo $Editor->Init( null, '600px', 'mainEditor', true ) ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="col-md-3 col-sm-3 col-md-pull-9">
			<div class="flex-row">
				<div class="card card-sm shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['publish'] ?></h4>
					</div>
					
					<div class="card-body text-gray-700">
						<!-- Edit/Publish Buttons -->
						<div class="d-flex mb-4 justify-content-between">
							<button class="btn btn-sm btn-outline-secondary" type="submit" id="draftButton" name="save-draft"><?php echo $L['save-draft'] ?></button>
						</div>
						<hr class="bg-gray-500">
						
						<!-- Post Status -->
						<div class="mb-3">
							<?php echo $L['status'] ?>: <strong><?php echo $L['draft'] ?> </strong>
						</div>
						
						<!-- Post Date -->
						<div class="mb-3">
						   <?php echo $L['publish'] ?>: <strong id="inputPostDate"><?php echo $L['immediately'] ?></strong><a class="ms-2 text-sm" data-toggle="collapse" href="#collapsePublish" role="button" aria-expanded="false" aria-controls="collapsePublish"><?php echo $L['edit'] ?></a>
							<div class="collapse" id="collapsePublish">
								<div class="py-3">
									<div class="row g-2">
										<div class="col-lg-6">    
											<input type="text" name="date" class="form-control postDatepicker" value="<?php echo date( 'm/d/Y', time() ) ?>" id="postDatepicker" placeholder="mm/dd/Y">
										</div>
										<div class="col-lg-6">
											<div class="d-flex align-items-center text-sm"><span class="me-1"><?php echo $L['at'] ?></span>
												<input class="form-control form-control-sm text-center" name="hoursPublished" id="hoursPublished" type="text" value="<?php echo date( 'H', time() ) ?>"><span class="mx-1">:</span>
												<input class="form-control form-control-sm text-center" name="minutesPublished" id="minutesPublished" type="text" value="<?php echo date( 'i', time() ) ?>">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Post Type -->
						<div class="mb-3">
							<?php echo $L['post-type'] ?>: <strong id="inputPostType"><?php echo $L[$postType] ?></strong> <a class="ms-2 text-sm" data-toggle="collapse" href="#collapseType" role="button" aria-expanded="false" aria-controls="collapseType"><?php echo $L['edit'] ?></a>
							<div class="collapse" id="collapseType">
								<div class="py-2">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="postType" value="post" id="type1"<?php echo ( ( $postType == 'post' ) ? ' checked' : '' ) ?>>
										<label class="form-check-label" for="type1"><?php echo $L['post'] ?></label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="postType" value="page" id="type2"<?php echo ( ( $postType == 'page' ) ? ' checked' : '' ) ?>>
										<label class="form-check-label" for="type2"><?php echo $L['page'] ?></label>
									</div>
								</div>
						  </div>
						</div>
					</div>
				</div>

				<?php if ( $postType == 'post' ) :
				
					if ( $showAll && ( $Admin->GetBlog() == 0 ) && $Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) ) :
						$cats = GetFullSiteCats( $Admin->GetLang() );
				?>
				<!-- Full Categories -->
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['category'] ?></h4>
					</div>
					<div class="card-body">
						<div class="mb-4">
							<div class="current_language_cats">
								<div class="form-group">
									<select class="form-control shadow-none" style="width: 100%; height:36px;" name="category" aria-label="Category select">
										<?php foreach ( $cats as $_sid => $site ) : ?>
											<optgroup label="<?php echo $site['name'] ?>">
											<?php foreach ( $site['langs'] as $_lid => $lang ) : ?>
												<optgroup label="-<?php echo $lang['name'] ?>">
												<?php foreach ( $lang['blogs'] as $_bid => $blog ) : ?>
													<optgroup label="--<?php echo $blog['name'] ?>">
														
														<?php foreach ( $blog['cats'] as $_cid => $cat ) : ?>
															<option value="cat::<?php echo $cat['id'] ?>" <?php echo ( ( $site['primary'] && $cat['default'] && ( $_bid == 'orphanCats' ) && ( $lang['id'] == $Admin->GetLang() ) ) ? 'selected' : '' ) ?>>&nbsp;<?php echo $cat['name'] ?></option>
															
															<?php 
															if ( !empty( $cat['childs'] ) ) : 
																foreach( $cat['childs'] as $_chid => $child ) : ?>
																
																<option value="sub::<?php echo $child['id'] ?>">&nbsp;-<?php echo $child['name'] ?></option>
																
																<?php endforeach ?>
															
															<?php endif ?>
														<?php endforeach ?>
														
													</optgroup>
												<?php endforeach ?>
												</optgroup>
											<?php endforeach ?>
											</optgroup>
										<?php endforeach ?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php else : ?>
				<!-- Categories -->
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['category'] ?></h4>
					</div>
					<div class="card-body">
						<div class="mb-4">
							<div class="current_language_cats">
								<select class="form-control" style="width: 100%;" name="category" aria-label="Category select" tabindex="-1" aria-hidden="true">
									<?php $cats = AdminCategoriesPost( $Admin->GetLang() );
										if ( !empty( $cats ) ) :
											foreach ( $cats as $id => $cat ):
												if ( !empty( $cat['childs'] ) ) :
									?>
									<optgroup label="&nbsp;<?php echo htmlspecialchars( $cat['name'] ) ?>">
									
									<?php foreach( $cat['childs'] as $cid => $child ) : ?>
										<option value="sub::<?php echo $cid ?>"><?php echo $child['name'] ?></option>
									<?php endforeach ?>
									</optgroup>
									<?php else : ?>
										<option value="cat::<?php echo $id ?>"><?php echo $cat['name'] ?></option>
									<?php endif ?>
									<?php unset( $cat ); endforeach; endif; ?>
								</select>
							</div>

						</div><!--<a class="btn-link" href="#">+ Add New Category</a>-->
					</div>
				</div>
				<?php endif ?>
			<?php endif ?>

				<?php if ( $postType == 'post' ) : ?>
				
				<?php $types = GetAdminCustomTypes();
						if ( !empty( $types ) ) :
				?>
				<!-- Post Type -->
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['post-type'] ?></h4>
					</div>
					<div class="card-body">
						<div class="form-check">
							<select class="form-select shadow-none" style="width: 100%; height:36px;" name="postFormat" aria-label="Select Format">
							<?php 
								foreach( $types as $type ) : ?>
								<option value="<?php echo $type['id'] ?>"><?php echo $type['title'] ?></option>
							<?php unset( $type ); endforeach; unset( $types ); ?>
							</select>
						</div>
					</div>
				</div>
				<?php endif ?>
				
				<!-- Post Tags -->
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['tags'] ?></h4>
					</div>
					<div class="card-body">
						<?php 
							$topTags = GetTopTags( $Admin->GetSite(), $Admin->GetLang(), 0, 30 );
						?>
						<input class="tags tagify--outside" id="tags" placeholder="<?php echo $L['enter-something'] ?>" name="tag" type="text" value="">
						<br />
						<p class="mb-3">
							<a class="ms-2 text-sm" data-toggle="collapse" href="#collapseTopTags" role="button" aria-expanded="false" aria-controls="collapseStatus"><?php echo $L['choose-from-the-most-used-tags'] ?></a>
							<div class="collapse" id="collapseTopTags">
								<div class="form-group">
								
									<div class="pt-3">
										<span class="text-danger" id="pt_tagify_custom_suggestions">
										<?php 
										if ( !empty( $topTags ) ) :
											foreach( $topTags as $tag ) :
												$text = ( ( $tag['num_items'] > 500 ) ? '1.4' : ( ( $tag['num_items'] > 150 ) ? '1.3' : ( ( $tag['num_items'] > 100 ) ? '1.2' : ( ( $tag['num_items'] > 50 ) ? '1.1' : '1.0' ) ) ) );
										?>
											<a href="javascript: void(0);"><span style="font-size: <?php echo $text ?>em;" class="cursor-pointer link-black" title="<?php echo htmlspecialchars( $tag['title'] ) ?> (<?php echo $tag['num_items'] ?> items)" data-tag-suggestion="true" data-id="<?php echo $tag['id'] ?>"><?php echo $tag['title'] ?></span></a>
											<?php endforeach ?>
										<?php endif ?>
										</span>
									</div>

								</div>
							</div>
						</p>
					</div>
				</div>
				<?php endif ?>
				
				<!-- Cover Image -->
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['cover-image'] ?></h4>
					</div>
					
					<div class="card-body">
						<div class="row">
							<div class="col-xl-12">
								<div class="col-md-55">

									<div class="mb-3">
										<label for="postTitle" class="form-label"><?php echo $L['external-cover-image'] ?></label>
										<input type="text" id="externalImage" name="externalImage" class="form-control mb-4" placeholder="https://" value="">
										
										<div id="externalImage" class="form-text text-muted"><?php echo $L['set-a-cover-image-from-external-url-such-as-a-cdn-or-some-server-dedicated-for-images'] ?></div>
										
										<div class="form-check">
											<input class="form-check-input" type="checkbox" name="copyRemoteImage" value="1" id="copyRemoteImage">
											<label class="form-check-label" for="copyRemoteImage"><?php echo $L['copy-remote-image-locally'] ?></label>
											<div id="copyRemoteImage" class="form-text text-muted"><?php echo $L['copy-remote-image-locally-tip'] ?></div>
										</div>
										
									</div>
									<input type="hidden" name="_token" value="<?php echo generate_token( 'add-content' ) ?>">
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>