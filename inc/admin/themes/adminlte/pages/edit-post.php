<?php 

	$xtraData = $Post->ExtraData();
	
	$hasPrices = ( ( ( $Admin->IsEnabled( 'multivendor-marketplace' ) || $Admin->IsEnabled( 'compare-prices' ) ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-prices' ) ) ) ? true : false );
	
	$hasCoupons = ( ( $Admin->IsEnabled( 'coupons-and-deals' ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-coupons-deals' ) ) ) ? true : false );
	
	$hasAttrs = ( ( $Admin->Settings()::IsTrue( 'enable_post_attributes' ) && IsAllowedTo( 'manage-post-attributes' ) ) ? true : false );
	
	$canManufact = ( ( ( $Admin->IsEnabled( 'coupons-and-deals' ) || $Admin->IsEnabled( 'compare-prices' ) || $Admin->IsEnabled( 'multivendor-marketplace' ) || $Admin->IsEnabled( 'store' ) ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-manufacturers' ) ) ) ? true : false );
	
	$canProduct = ( ( $Admin->IsEnabled( 'multivendor-marketplace' ) || $Admin->IsEnabled( 'coupons-and-deals' ) || $Admin->IsEnabled( 'store' ) || $Admin->IsEnabled( 'compare-prices' ) ) ? true : false );
	
	$canVarFull = ( ( $canProduct && ( $Admin->IsEnabled( 'multivendor-marketplace' ) || $Admin->IsEnabled( 'store' ) ) ) ? true : false );
	
	$canViewAttachments = ( ( IsAllowedTo( 'view-attachments' ) || IsAllowedTo( 'manage-attachments' ) ) ? true : false );
	
	$hasVariations = ( ( $canProduct && $Post->Variations( true ) ) ? true : false );
	
	$hasLinksEnabled = ( $Admin->Settings()::IsTrue( 'enable_link_manager' ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-links' ) ) );
?>
<div class="row">
	<div class="col-md-9 col-sm-9 col-md-push-3">
		<form action="<?php echo $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $Post->PostID() ) ?>" method="post" id="post" role="form">
		<?php
			HiddenFormInput( array( 'name' => 'post_id', 		'value' => $Post->PostID() ) );
			HiddenFormInput( array( 'name' => 'post_lang_id', 	'value' => $Post->Language()->id ) );
			HiddenFormInput( array( 'name' => 'post_site_id', 	'value' => $Post->Site()->id ) );
			HiddenFormInput( array( 'name' => 'post_old_slug', 	'value' => $Post->Sef() ) );
			HiddenFormInput( array( 'name' => 'post_draft_id', 	'value' => $Admin->GetDraft() ) );
			
			FormInput( array( 'name' => 'filesToUpload[]', 'id' => 'filesToUpload', 'xtra' => 'multiple hidden', 'type' => 'file' ) );
		?>
			<div class="card card-tabs">
				<div class="card-header p-0 pt-1">
				
				<?php 
					$navArgs = array(
							'id' => 'postTabs',
							'role' => 'tablist',
							'lis' => array(
								'general' => array(
									'active' => true,
									'id' => 'home-tab',
									'toggle' => 'tab',
									'data-target' => 'general',
									'aria-selected' => 'true',
									'title' => __( 'general' )
								),
								
								'settings' => array(
									'id' => 'settings-tab',
									'toggle' => 'tab',
									'data-target' => 'settings',
									'aria-selected' => 'false',
									'title' => __( 'settings' )
								)
							)
					);
					
					if ( $Admin->Settings()::IsTrue( 'enable_seo' ) && IsAllowedTo( 'manage-seo' ) )
					{
						$navArgs['lis']['seo'] = array(
									'id' => 'seo-tab',
									'toggle' => 'tab',
									'data-target' => 'seo',
									'aria-selected' => 'false',
									'title' => __( 'seo' )
						);
					}
					
					if ( $Admin->OpenGraph() && IsAllowedTo( 'manage-seo' ) )
					{
						$navArgs['lis']['open-graph'] = array(
									'id' => 'open-graph-tab',
									'toggle' => 'tab',
									'data-target' => 'open-graph',
									'aria-selected' => 'false',
									'title' => __( 'open-graph' )
						);
					}
					
					if ( $Admin->Settings()::IsTrue( 'enable_galleries' ) )
					{
						$navArgs['lis']['gallery'] = array(
									'id' => 'gallery-tab',
									'toggle' => 'tab',
									'data-target' => 'gallery',
									'aria-selected' => 'false',
									'title' => __( 'gallery' )
						);
					}
					
					if ( $hasPrices )
					{
						$navArgs['lis']['prices'] = array(
									'id' => 'prices-tab',
									'toggle' => 'tab',
									'data-target' => 'prices',
									'aria-selected' => 'false',
									'title' => __( 'price-list' )
						);
					}
					
					if ( $hasCoupons )
					{
						$navArgs['lis']['deals'] = array(
									'id' => 'deals-tab',
									'toggle' => 'tab',
									'data-target' => 'deals',
									'aria-selected' => 'false',
									'title' => __( 'deals-coupons' )
						);
					}
					
					if ( $canProduct )
					{
						$navArgs['lis']['data'] = array(
									'id' => 'data-tab',
									'toggle' => 'tab',
									'data-target' => 'data',
									'aria-selected' => 'false',
									'title' => __( 'data' )
						);
						
						$navArgs['lis']['variations'] = array(
									'id' => 'var-tab',
									'toggle' => 'tab',
									'data-target' => 'variations',
									'aria-selected' => 'false',
									'title' => __( 'variations' )
						);
					}
					
					if ( $Admin->IsVideoBlog() && IsAllowedTo( 'manage-video-content' ) )
					{
						$navArgs['lis']['video'] = array(
									'id' => 'video-tab',
									'toggle' => 'tab',
									'data-target' => 'video',
									'aria-selected' => 'false',
									'title' => __( 'video' )
						);
					}
					
					if ( $Admin->Schema() && IsAllowedTo( 'manage-seo' ) )
					{
						$navArgs['lis']['schema'] = array(
									'id' => 'schema-tab',
									'toggle' => 'tab',
									'data-target' => 'schema',
									'aria-selected' => 'false',
									'title' => __( 'schema' )
						);
					}
					
					if ( $hasAttrs )
					{
						$navArgs['lis']['attributes'] = array(
									'id' => 'attributes-tab',
									'toggle' => 'tab',
									'data-target' => 'attributes',
									'aria-selected' => 'false',
									'title' => __( 'post-attributes' )
						);
					}
					
					if ( !empty( $Drafts ) && $Drafts['enable_post_drafts'] && IsAllowedTo( 'save-drafts' ) )
					{
						$navArgs['lis']['drafts'] = array(
									'id' => 'drafts-tab',
									'toggle' => 'tab',
									'data-target' => 'drafts',
									'aria-selected' => 'false',
									'title' => __( 'drafts' )
						);
					}
					
					NavTabs( $navArgs );
				?>
				</div>
				
				<div class="card-body">
					<div class="col-xl-12">
						<div class="tab-content" id="postTabContent">
							<div class="tab-pane show active" id="general" role="tabpanel" aria-labelledby="general-tab">
								<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'general-tab.php' ) ?>
							</div>
							<div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'settings-tab.php' ) ?>
							</div>
							<?php if ( $Admin->Settings()::IsTrue( 'enable_seo' ) && IsAllowedTo( 'manage-seo' ) ) : ?>
								<div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'seo-tab.php' ) ?>
								</div>
							<?php endif ?>
							
							<?php if ( $Admin->OpenGraph() && IsAllowedTo( 'manage-seo' ) ) : ?>
								<div class="tab-pane fade" id="open-graph" role="tabpanel" aria-labelledby="open-graph-tab">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'open-graph-tab.php' ) ?>
								</div>
							<?php endif ?>
							
							<?php if ( $Admin->Settings()::IsTrue( 'enable_galleries' ) ) : ?>
								<div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'gallery-tab.php' ) ?>
								</div>
							<?php endif ?>
							
							<?php if ( $hasPrices ) : ?>
								<div class="tab-pane fade" id="prices" role="tabpanel" aria-labelledby="prices-tab">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'prices-tab.php' ) ?>
								</div>
							<?php endif ?>
							
							<?php if ( $hasCoupons ) : ?>
								<div class="tab-pane fade" id="deals" role="tabpanel" aria-labelledby="deals-tab">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'deals-tab.php' ) ?>
								</div>
							<?php endif ?>
							
							<?php if ( $canProduct ) : ?>
								<div class="tab-pane fade" id="data" role="tabpanel" aria-labelledby="data-tab">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'data-tab.php' ) ?>
								</div>
								
								<div class="tab-pane fade" id="variations" role="tabpanel" aria-labelledby="var-tab">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'var-tab.php' ) ?>
								</div>
							<?php endif ?>
							
							<?php if ( $Admin->IsVideoBlog() && IsAllowedTo( 'manage-video-content' ) ) : ?>
								<div class="tab-pane fade" id="video" role="tabpanel" aria-labelledby="video-tab">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'video-tab.php' ) ?>
								</div>
							<?php endif ?>
							
							<?php if ( $Admin->Schema() && IsAllowedTo( 'manage-seo' ) ) : ?>
								<div class="tab-pane fade" id="schema" role="tabpanel" aria-labelledby="schema-tab">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'schema-tab.php' ) ?>
								</div>
							<?php endif ?>
							
							<?php if ( $Admin->Settings()::IsTrue( 'enable_post_attributes' ) && IsAllowedTo( 'manage-post-attributes' ) ) : ?>
								<div class="tab-pane fade" id="attributes" role="tabpanel" aria-labelledby="attributes-tab">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'attributes.php' ) ?>
								</div>
							<?php endif ?>
							
							<?php if ( !empty( $Drafts ) && $Drafts['enable_post_drafts'] && IsAllowedTo( 'save-drafts' ) ) : ?>
								<div class="tab-pane fade" id="drafts" role="tabpanel" aria-labelledby="drafts-tab">
									<?php include( ADMIN_THEME_PAGES_ROOT . 'post-templates' . DS . 'drafts-tab.php' ) ?>
								</div>
							<?php endif ?>
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
						<?php if ( !$Post->IsPublished() ) : ?>
							<button class="btn btn-sm btn-outline-secondary" type="submit" id="draftButton" name="save-draft"><?php echo $L['save-draft'] ?></button>
						<?php else :?>
							<button class="btn btn-sm btn-outline-primary" type="submit" id="updateButton" name="update"><?php echo $L['update'] ?></button>
						<?php endif ?>
							<a class="btn btn-sm btn-danger float-right" href="<?php echo $Admin->GetUrl( 'delete-post' . PS . 'id' . PS . $Post->PostID() ) ?>" id="deleteButton" role="button" onclick="return confirm_alert2(this);"><?php echo $L['delete'] ?></a>
						</div>
						<hr class="bg-gray-500">
						
						<!-- Post Status -->
						<div class="mb-3">
							<?php echo $L['status'] ?>: <strong><?php echo __( $Post->Status() ) ?> </strong><a class="ms-2 text-sm" data-toggle="collapse" href="#collapseStatus" role="button" aria-expanded="false" aria-controls="collapseStatus"><?php echo $L['edit'] ?></a>
							<div class="collapse" id="collapseStatus">
								<div class="form-group">
									<select class="form-control" name="status" aria-label="Post select">
										<option value="published" <?php echo ( ( $Post->Status() == 'published' ) ? 'selected' : '' ) ?>><?php echo $L['published'] ?></option>
										<option value="draft" <?php echo ( ( $Post->Status() == 'draft' ) ? 'selected' : '' ) ?>><?php echo $L['draft'] ?></option>
										<option value="pending" <?php echo ( ( $Post->Status() == 'pending' ) ? 'selected' : '' ) ?>><?php echo $L['pending-review'] ?></option>
									</select>
								</div>
							</div>
						</div>
						
						<!-- Post Date -->
						<div class="mb-3">
						   <?php echo $L['publish'] ?>: <strong id="inputPostDate"><?php echo ( empty( $Post->Added()->time ) ? $L['immediately'] : $Post->Added()->time ) ?></strong><a class="ms-2 text-sm" data-toggle="collapse" href="#collapsePublish" role="button" aria-expanded="false" aria-controls="collapsePublish"><?php echo $L['edit'] ?></a>
							<div class="collapse" id="collapsePublish">
								<div class="py-3">
									<div class="row g-2">
										<div class="col-lg-6">    
											<input type="text" name="date" class="form-control postDatepicker" value="<?php echo ( empty( $Post->Added()->raw ) ? date( 'm/d/Y', time() ) : date( 'm/d/Y', $Post->Added()->raw ) ) ?>" id="postDatepicker" placeholder="mm/dd/Y">
										</div>
										<div class="col-lg-6">
											<div class="d-flex align-items-center text-sm"><span class="me-1"><?php echo $L['at'] ?></span>
												<input class="form-control form-control-sm text-center" name="hoursPublished" id="hoursPublished" type="text" value="<?php echo ( empty( $Post->Added()->raw ) ? date( 'H', time() ) : date( 'H', $Post->Added()->raw ) ) ?>"><span class="mx-1">:</span>
												<input class="form-control form-control-sm text-center" name="minutesPublished" id="minutesPublished" type="text" value="<?php echo ( empty( $Post->Added()->raw ) ? date( 'i', time() ) : date( 'i', $Post->Added()->raw ) ) ?>">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Post Type -->
						<div class="mb-3">
							<?php echo $L['post-type'] ?>: <strong id="inputPostType"><?php echo __( $Post->PostType() ) ?></strong> <a class="ms-2 text-sm" data-toggle="collapse" href="#collapseType" role="button" aria-expanded="false" aria-controls="collapseType"><?php echo $L['edit'] ?></a>
							<div class="collapse" id="collapseType">
								<div class="py-2">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="postType" value="post" id="type1"<?php echo ( ( $Post->PostType() == 'post' ) ? ' checked' : '' ) ?>>
										<label class="form-check-label" for="type1"><?php echo $L['post'] ?></label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="postType" value="page" id="type2"<?php echo ( ( $Post->PostType() == 'page' ) ? ' checked' : '' ) ?>>
										<label class="form-check-label" for="type2"><?php echo $L['page'] ?></label>
									</div>
								</div>
						  </div>
						</div>
						
						<?php if ( IsAllowedTo( 'manage-attachments' ) ) : ?>
						<hr class="bg-gray-500">
						<!-- Copy External Images -->
						<div class="mb-3">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" name="copyRemoteImages" value="1" id="copyRemoteImages">
								<label class="form-check-label" for="copyRemoteImages"><?php echo $L['copy-remote-images-locally'] ?></label>
								<small id="copyRemoteImages" class="form-text text-muted"><?php echo __( 'copy-remote-images-locally-tip' ) ?></small>
							</div>
						</div>
						<?php endif ?>
					</div>
					
					<!-- Publish Button -->
					<div class="card-footer text-end">
					<?php if ( !$Post->IsPublished() ) : ?>
						<button class="btn btn-primary" type="submit" id="publishButton" name="publish" <?php echo ( empty( $Post->Sef() ) ? 'disabled' : '' ) ?>><?php echo $L['publish'] ?></button>
					<?php endif ?>
					</div>
				</div>
				
				<?php 
				if ( $hasLinksEnabled ) : 

					$lSet = $Admin->Settings()::Get()['link_manager_options'];
					$lSet = Json( $lSet );
					
					if ( !empty( $lSet['short-link-settings']['enable'] ) ) :
					$urlPrefix = $Post->Site()->url . $lSet['short-link-settings']['base_slug_prefix'] . PS;
					
					$shortUrl = '';

					$t = $Admin->db->from( null, "
					SELECT short_link, num_views FROM `" . DB_PREFIX . "links`
					WHERE (id_post = " . $Post->PostID() . ") AND (id_site = " . $Admin->GetSite() . ")"
					)->single();
					
					if ( $t )
					{
						$shortUrl = $t['short_link'];
					}
				?>
				<!-- Short Links -->
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['short-url'] ?></h4>
					</div>
					<div class="card-body">
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text">/</span>
							</div>
							<input type="text" class="form-control" name="shortUrlSlug" id="shortUrlSlug" value="<?php echo $shortUrl ?>" />
						</div>
						
						<?php if ( !empty( $shortUrl ) ) : ?>
							<small class="form-text bg-info px-2"><?php echo $urlPrefix . $shortUrl . PS ?></small>
							<?php endif ?>
					</div>
				</div>	
				<?php endif ?>
				<?php endif ?>
				
				<?php if ( $Admin->MultiLang() ) :
						$Langs = Langs( $Admin->GetSite(), false );
						$translations = $Post->Translations();
				?>
				<!-- Languages -->
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['langs'] ?></h4>
					</div>
					
					<div class="card-body">
						<div class="mb-4">
							<table class="select-language-table">
								<tbody>
									<tr>
										<td class="active-language">
											<img src="<?php echo FLAGS_HTML . GetLangInfo( 'flag', $Post->Language()->id ) ?>" title="<?php echo $Post->Language()->name ?>" width="16" alt="<?php echo $Post->Language()->name ?>">
										</td>
										<td class="translation-column">
											<div class="ui-select-wrapper">
												<select name="language" id="post_lang_choice" style="width: 100%; height:36px;" class="form-control shadow-none">
												<?php foreach( $Langs as $lCode => $lData ) :
														if ( $Post->Language()->id != $lData['id'] )
															continue;
													?><option value="<?php echo $lData['id'] ?>" data-flag="<?php echo $lData['flagicon'] ?>" selected="selected"><?php echo $lData['title'] ?></option>
												<?php endforeach ?>
										
												<?php foreach( $Langs as $lCode => $lData ) :
														if ( $Post->Language()->id == $lData['id'] )
															continue;
												?><option value="<?php echo $lData['id'] ?>" data-flag="<?php echo $lData['flagicon'] ?>"><?php echo $lData['title'] ?></option>
												<?php endforeach ?>
												</select>
												<svg class="flag-next-icon flag-next-icon-size-16">
													<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
												</svg>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
							
							<div class="form-group">
							<?php if ( $Post->Language()->id == $Admin->DefaultLang()['id'] ) : ?>
								<strong><?php echo $L['translations'] ?></strong>
								<div id="list-others-language">
								<?php foreach( $Langs as $lCode => $lData ) :
										
										if ( $Post->Language()->id == $lData['id'] )
											continue;
										
										if ( !empty( $translations ) && isset( $translations[$lCode] ) )
										{
											$transUrl = $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $translations[$lCode]['id'] );
											$linkIcon = '<i class="fa fa-edit"></i>';
										}
										else
										{
											$transUrl = ADMIN_URI . 'add-translation' . PS . 'id' . PS . $Post->PostID() . PS . '?lang=' . $lData['id'];
											$linkIcon = '<i class="fa fa-plus"></i>';
										}
									
								?><img src="<?php echo FLAGS_HTML . $lData['flagicon'] ?>" title="<?php echo $lData['title'] ?>" width="16" alt="<?php echo $lData['title'] ?>">
									<a target="_blank" href="<?php echo $transUrl ?>"> <?php echo $lData['title'] ?> <?php echo $linkIcon ?></a><br />
								<?php endforeach ?>
								</div>
							
							<?php else : ?>
								<!-- Search for Parent Post -->
								<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="postParent"><?php echo $L['parent'] ?></label>
								<select id="postParent" style="width: 100%; height:36px;" name="parent" class="select2">
								<?php if ( !empty( $translations ) && isset( $translations[$Admin->DefaultLang()['code']] ) ) : ?>
									<option value="<?php echo $translations[$Admin->DefaultLang()['code']]['id'] ?>"><?php echo $translations[$Admin->DefaultLang()['code']]['title'] ?></option>
								<?php endif ?>
								</select>
								<small class="form-text text-muted"><?php echo $L['start-typing-a-page-title-to-see-a-list-of-suggestions'] ?></small>
							<?php endif ?>
						   </div>
							<input type="hidden" id="lang_meta_created_from" name="ref_from" value="">
							<input type="hidden" id="route_create" value="<?php echo ADMIN_URI . 'add-translation' . PS . 'id' . PS . $Post->PostID() . PS ?>">
							<input type="hidden" id="route_edit" value="<?php echo $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $Post->PostID() ) ?>">
							<input type="hidden" id="language_flag_path" value="<?php echo FLAGS_HTML ?>">
							<div data-change-language-route="<?php echo AJAX_ADMIN_PATH ?>change-post-language/"></div>
							<div id="confirm-change-language-modal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
								<div class="modal-dialog ">
									<div class="modal-content">
										<div class="modal-header bg-secondary">
											<h4 class="modal-title">
												<i class="til_img"></i>
												<strong><?php echo $L['confirm-change-language'] ?></strong>
											</h4>
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body with-padding"> <?php echo $L['change-lang-warning'] ?> </div>
										<div class="modal-footer">
											<button class="float-left btn btn-warning" data-dismiss="modal"><?php echo $L['cancel'] ?></button>
											<a class="float-right btn btn-warning" id="confirm-change-language-button" href="#"><?php echo $L['confirm-change'] ?></a>
										</div>
									</div>
								</div>
						  </div>
						</div>
					</div>
				</div>
				<?php endif ?>
				
				<?php AdminMovePost( ( !empty( $Post->Blog()->id ) ? $Post->Blog()->id : 0 ), $Post->Site()->id, $Post->ParentId() ) ?>
				
				<?php if ( $Post->IsPage() ) : ?>
				<!-- Page Parent -->
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['page-attributes'] ?></h4>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label class="border-bottom w-100" for="pageParent"><?php echo $L['parent'] ?> <a href="#" type="button" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo __( 'page-parent-tip' ) ?>"><i class="bi bi-info-circle"></i></a></label>
							<select id="pageParent" style="width: 100%; height:36px;" name="pageParent" class="select2">
							<?php if ( !empty( $Post->PageParentId() ) && !empty( $Post->PageParentTitle() ) ) : ?>
								<option value="<?php echo $Post->PageParentId() ?>"><?php echo $Post->PageParentTitle() ?></option>
							<?php endif ?>
							</select>
							<small class="form-text text-muted"><?php echo $L['page-parent-typing-tip'] ?></small>
						</div>
						
						<div class="form-group">
								<label for="amount-items"><?php echo __( 'order' ) ?></label>
								<input type="number" step="1" min="0" class="form-control" id="page-order" value="<?php echo $Post->PageOrder() ?>" name="page_order">
								<small id="pageOrderTip" class="form-text text-muted"><?php echo __( 'page-order-tip' ) ?></small>
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
							
								<div class="col-sm-10">
									<div class="form-group d-none" id="catInputDivs"></div>
									<div class="form-group" id="catInputDiv">
									<?php $cats = AdminCategoriesPost( $Post->Language()->id );
											if ( !empty( $cats ) ) :
												foreach ( $cats as $id => $cat ):
												
												$catName = ( ( !empty( $cat['childs'] ) ) ? '<strong>' . $cat['name'] . '</strong>' : $cat['name'] );
										?>
										<div class="form-check" id="catInputs">
											<input class="form-check-input cat_check" type="checkbox" id="cat<?php echo $id ?>" name="category" value="<?php echo $id ?>" <?php echo ( ( !empty( $Post->Category()->id ) && ( $Post->Category()->id == $id ) ) ? 'checked' : '' ) ?>>
											<label for="<?php echo $id ?>" class="form-check-label"><?php echo $catName ?></label>
										</div>
										<?php if ( !empty( $cat['childs'] ) ) : 
											foreach( $cat['childs'] as $cid => $child ) : ?>
											<div class="form-check">
												<input class="form-check-input cat_check" type="checkbox" id="sub<?php echo $cid ?>" data-parent="<?php echo $id ?>" name="subcat" value="<?php echo $cid ?>" <?php echo ( ( !empty( $Post->SubCategory()->id ) && ( $Post->SubCategory()->id == $cid ) ) ? 'checked' : '' ) ?>>
												<label class="form-check-label">¦&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $child['name'] ?></label>
											</div>
										<?php endforeach; endif; ?>
										<?php endforeach; endif; ?>
									</div>
									<script>
									$(document).on('click','.cat_check',function(){
										var parent = $(this).data('parent');
										$('.cat_check').not(this).prop('checked', false);
										if (parent !== undefined)
										{
											var obj = $('#cat' + parent).prop('checked', true);
										}
									});
									
									</script>
									
									<?php if ( IsAllowedTo( 'manage-posts' ) ) : ?>
									<a href="javascript: void(0);" id="addNewCat" class="ms-2 text-sm" data-toggle="modal" data-target="#addNewCategory" data-id="<?php echo $Post->PostID() ?>" data-focus="false">+ <?php echo __( 'add-new-category' ) ?></a>
									<?php endif ?>
								</div>
							</div>
						</div>
					</div>
				</div>				
		
				<?php 
					$types = GetAdminCustomTypes();
					
					if ( !empty( $types ) ) :
				?>
				<!-- Custom Post Types -->
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['custom-post-types'] ?></h4>
					</div>
					
					<div class="card-body">
						<div class="accordion">
						<?php 
								foreach( $types as $type ) :
									$topTags = null;
									
									$cusName = ( ( !empty( $type['childs'] ) ) ? '<strong>' . $type['title'] . '</strong>' : $type['title'] );
	
									$tags = GetAssocTags( $Post->PostID(), $type['id'], true );
									$cEx = ( !empty( $Post->CustomTypes() ) && isset( $Post->CustomTypes()[$type['id']] ) );
									
									if ( $cEx )
									{
										$topTags = GetTopTags( $Admin->GetSite(), $Admin->GetLang(), $type['id'] );
									}
								?>
							<div class="card">
								<div class="card-header" id="heading<?php echo $type['id'] ?>">
									<div class="form-check" id="customTypeInputs<?php echo $type['id'] ?>">
										<input class="form-check-input customType_check" type="checkbox" data-id="<?php echo $type['id'] ?>" id="customType" name="customType[]" value="<?php echo $type['id'] ?>" data-toggle="collapse" data-target="#collapseCus<?php echo $type['id'] ?>" aria-expanded="true" aria-controls="collapseCus<?php echo $type['id'] ?>" <?php echo ( $cEx ? ' checked="true"' : '' ) ?>>
										<label for="<?php echo $id ?>" class="form-check-label"><?php echo $cusName ?></label>
									</div>
								</div>

								<div id="collapseCus<?php echo $type['id'] ?>" class="collapse<?php echo ( $cEx ? ' show' : '' ) ?>" aria-labelledby="headingOne">
									<div class="card-body">
										<div id="customTagsDiv<?php echo $type['id'] ?>">
											<input class="tags tagify--outside" id="customtags<?php echo $type['id'] ?>" placeholder="<?php echo $L['enter-something'] ?>" name="customTags[<?php echo $type['id'] ?>]" type="text" value="<?php echo ( !empty( $tags ) ? implode( ', ', $tags ) : '' ) ?>">
											
											<p class="mb-3">
												<a class="ms-2 text-sm" data-toggle="collapse" href="#collapseCustomTopTags<?php echo $type['id'] ?>" role="button" aria-expanded="false" aria-controls="collapseStatus"><?php echo $L['choose-from-the-most-used-entries'] ?></a>
												<div class="collapse" id="collapseCustomTopTags<?php echo $type['id'] ?>">
													<div class="form-group col-sm-12">
														<div class="pt-3">
															<span class="text-danger" id="kt_tagify_custom<?php echo $type['id'] ?>_suggestions">
															<?php if ( !empty( $topTags ) ) : ?>
																<span class="text-danger" id="at_tagify_custom<?php echo $type['id'] ?>_suggestions">
																<?php foreach( $topTags as $tag ) : 
																
																$text = ( ( $tag['num_items'] > 100 ) ? '1.4' : ( ( $tag['num_items'] > 75 ) ? '1.3' : ( ( $tag['num_items'] > 50 ) ? '1.2' : ( ( $tag['num_items'] > 25 ) ? '1.1' : '1.0' ) ) ) );?>
																<a href="javascript: void(0);"><span style="font-size: <?php echo $text ?>em;" class="cursor-pointer link-black" data-cus<?php echo $type['id'] ?>-suggestion="true" data-id="<?php echo $type['id'] ?>"><?php echo $tag['title'] ?></span></a>
																<?php endforeach ?>
																</span>
															<?php endif ?>
															</span>
														</div>
													</div>
												</div>
											</p>
										</div>
									</div>
								</div>
							</div>
					  <?php endforeach ?>
							<div id="cusTagsHelp" class="form-text text-sm text-muted d-none"><?php echo $L['associate-custom-tags-list'] ?></div>
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
							$tags = GetAssocTags( $Post->PostID(), 0, true );
							$topTags = GetTopTags( $Admin->GetSite(), $Admin->GetLang(), 0, 30 );
						?>
						<input class="tags tagify--outside" id="tags" placeholder="<?php echo $L['enter-something'] ?>" name="tag" type="text" value="<?php echo ( !empty( $tags ) ? implode( ', ', $tags ) : '' ) ?>">
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
				
				<?php if ( $canViewAttachments ) : ?>
				<!-- Cover Image -->
				<div class="card shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['cover-image'] ?></h4>
					</div>
					
					<div class="card-body">
						<div class="row">
							<div class="col-xl-12">
								<div class="col-md-55">
									<div class="thumbnail <?php echo ( !$Post->HasCoverImage() ? 'd-none' : '' ) ?>" id="thumbnail">
										<div class="image view view-first">
											<img height="180" width="100%" class="display: block;" src="<?php echo ( $Post->HasCoverImage() ? $Post->CoverImage() : '' ) ?>" alt="coverImage" id="coverImage" />
											<div class="mask">
												<div class="tools tools-bottom">
													<a id="removeCover"><i class="fa fa-times"></i></a>
												</div>
											</div>
										</div>
										
										<div id="captionImg" class="caption <?php echo ( !$Post->HasCoverImage() ? 'd-none' : '' ) ?>">
											<p><?php echo $L['click-the-image-to-remove-it'] ?></p>
										</div>
										
										<input type="hidden" id="internalImage" name="internalImage" value="<?php echo ( !$Post->HasCoverImage() ? '' : $Post->CoverImage( false, 'default', false )->imageFilename ) ?>" />

										<input type="hidden" id="coverImageID" name="coverImageID" value="<?php echo ( !$Post->HasCoverImage() ? '' : $Post->CoverImage( false, 'default', false )->imageId ) ?>" />
									</div>
									
									<div class="mb-3">
										<a href="javascript: void(0);" data-toggle="modal" data-target="#addImage" id="imageCoverModal" class="ms-2 text-sm" data-id="<?php echo $Post->PostID() ?>" data-focus="false"> <i class="far fa-image"></i> <?php echo __( 'add-media' ) ?></a>
									</div>
									
									<?php if ( IsAllowedTo( 'manage-attachments' ) ) : ?>
									<hr />
									<div class="mb-3">
										<label for="postTitle" class="form-label"><?php echo $L['external-cover-image'] ?></label>
										<input type="text" id="externalImage" name="externalImage" class="form-control mb-4" placeholder="https://" value="">
										
										<div id="externalImage" class="form-text text-muted"><?php echo $L['set-a-cover-image-from-external-url-such-as-a-cdn-or-some-server-dedicated-for-images'] ?></div>
										<!--
										<div class="form-check">
											<input class="form-check-input" type="checkbox" name="copyRemoteImage" value="1" id="copyRemoteImage">
											<label class="form-check-label" for="copyRemoteImage"><?php echo $L['copy-remote-image-locally'] ?></label>
											<div id="copyRemoteImage" class="form-text text-muted"><?php echo $L['copy-remote-image-locally-tip'] ?></div>
										</div>-->
									</div>
									<?php endif ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php endif ?>
				<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_single_post_' . $Post->PostID() ) ?>">
			</form>
		</div>
	</div>
</div>