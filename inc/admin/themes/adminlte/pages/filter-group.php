<?php
	include ( ARRAYS_ROOT . 'generic-arrays.php');
	$edit = ( isset( $Filter ) ? true : false );
	$countData = ( isset( $Filter ) ? count( $Filter['data'] ) : 0 );
	$groupData = ( isset( $Filter ) ? $Filter['groupData'] : null );
	$lastFilterId = 0;
?>
<div class="row">
	<div class="col-12">
		<form class="tab-content" id="form" method="post" action="" autocomplete="off">
			<div class="card mb-4">
				<div class="card-header">
					<?php echo ( $edit ? __( 'edit-filter' ) : __( 'add-new-filter' ) ) ?></a>
				</div>
				<div class="card-body">
						<fieldset>
							<legend><?php echo __( 'filter-group' ) ?></legend>
								<div class="row mb-3">
									<label class="col-sm-2 col-form-label required"><?php echo __( 'filter-group-name' ) ?></label>
									<div class="col-sm-10">
										<div class="input-group">
											<input type="text" name="name" value="<?php echo ( $edit ? $Filter['name'] : '' ) ?>" placeholder="<?php echo __( 'filter-group-name' ) ?>" id="input-group-1" class="form-control" required />
										</div>
									</div>
								</div>
								<div class="row mb-3">
									<label for="input-sort-order" class="col-sm-2 col-form-label"><?php echo __( 'sort-order' ) ?></label>
									<div class="col-sm-6">
										<input type="number" name="sort_order" value="<?php echo ( $edit ? $Filter['order'] : '0' ) ?>" placeholder="<?php echo __( 'sort-order' ) ?>" id="input-sort-order" class="form-control" min="0" max="100" />
									</div>
								</div>
								<?php 
								if ( $edit ) : 
									$cats = GetAdminCategories();
									$atts = AdminGetAttributes( $Admin->GetSite(), $Admin->GetLang(), $Admin->GetBlog() );
									$tags = AdminGetTags( $Admin->GetLang() );
									$custom = AdminCustomTypes( null, $Admin->GetSite() );
									$groups = GetAdminAttributeGroups();
								?>								
								<div class="row mb-3">
									<label for="input-group-type" class="col-sm-2 col-form-label"><?php echo __( 'type' ) ?></label>
									<div class="col-sm-6">
										<select name="option-type" class="form-control">
											<option value="" <?php echo ( ( isset( $groupData['type'] ) && ( $groupData['type'] == '' ) ) ? 'selected' : '' ) ?>>---</option>
											<?php foreach( $sortTypesArray as $sId => $sType ) : ?>
												<option value="<?php echo $sId ?>" <?php echo ( ( isset( $groupData['type'] ) && ( $groupData['type'] == $sId ) ) ? 'selected' : '' ) ?>><?php echo $sType['title'] ?></option>
											<?php endforeach ?>
										</select>
										<small id="typeHelp" class="form-text text-muted"><?php echo __( 'option-type-tip' ) ?></small>
									</div>
								</div>
								<hr />
								
								<div class="row mb-3">
									<label for="input-group-source" class="col-sm-2 col-form-label"><?php echo __( 'source-of-options' ) ?></label>
									<div class="col-sm-6">
										<select name="source" id="sourceOptions" class="form-control">
											<option value="">---</option>
											<?php foreach( $sourceOptionsArray as $sId => $sType ) : ?>
												<option value="<?php echo $sId ?>" <?php echo ( ( isset( $groupData['source'] ) && ( $groupData['source'] == $sId ) ) ? 'selected' : '' ) ?>><?php echo $sType['title'] ?></option>
											<?php endforeach ?>
										</select>
										<small id="sourceHelp" class="form-text text-muted"><?php echo __( 'source-of-options-tip' ) ?></small>
									</div>
								</div>
								
								<div id="loader2" class="row mb-3 d-none">
									<label for="input-group-loader" class="col-sm-2 col-form-label">&nbsp;</label>
									<div class="col-sm-2">
										<div class="form-group">
											<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
										</div>
									</div>
								</div>
								
								<div id="storesDiv" class="row mb-3 d-none">
									<label for="input-group-categories" class="col-sm-2 col-form-label"><?php echo __( 'order-by' ) ?></label>
									<div class="col-sm-8">
										<div class="form-group clearfix">
											<div class="icheck-primary d-inline mr-1">
												<input type="radio" id="merchantOrder1" name="merchantOrder" value="name" <?php echo ( ( !empty( $groupData['merchant-order'] ) && ( $groupData['merchant-order'] == 'name' ) ) ? 'checked' : ( !isset( $groupData['merchant-order'] ) ? 'checked' : '' ) ) ?>>
												<label for="merchantOrder1"><?php echo __( 'name' ) ?></label>
											</div>
											<div class="icheck-primary d-inline mr-1">
												<input type="radio" id="merchantOrder2" name="merchantOrder" value="order" <?php echo ( ( !empty( $groupData['merchant-order'] ) && ( $groupData['merchant-order'] == 'order' ) ) ? 'checked' : '' ) ?>>
												<label for="merchantOrder2"><?php echo __( 'order' ) ?></label>
											</div>
											<div class="icheck-primary d-inline">
												<input class="form-check-input" type="radio" id="merchantOrder3" name="merchantOrder" value="count"<?php echo ( ( !empty( $groupData['merchant-order'] ) && ( $groupData['merchant-order'] == 'count' ) ) ? 'checked' : '' ) ?>>
												<label for="merchantOrder3"><?php echo __( 'count' ) ?></label>
											</div>
											
											<div class="d-inline mr-1">&nbsp;&nbsp;&dash;&nbsp;&nbsp;</div>
											<div class="icheck-primary d-inline">
												<input class="form-check-input" type="radio" id="merchantOrder4" name="merchantArrange" value="asc" <?php echo ( ( !empty( $groupData['sourceData'] ) && ( $groupData['sourceData']['arrange'] == 'asc' ) ) ? 'checked' : ( !isset( $groupData['sourceData']['arrange'] ) ? 'checked' : '' ) ) ?>>
												<label for="merchantOrder4"><?php echo __( 'asc' ) ?></label>
											</div>

											<div class="icheck-primary d-inline">
												<input class="form-check-input" type="radio" id="merchantOrder5" name="merchantArrange" value="desc" <?php echo ( ( !empty( $groupData['sourceData'] ) && ( $groupData['sourceData']['arrange'] == 'desc' ) ) ? 'checked' : '' ) ?>>
												<label for="merchantOrder5"><?php echo __( 'desc' ) ?></label>
											</div>
										</div>
									</div>
									<!--
									<label for="input-stores" class="col-sm-2 col-form-label"><?php echo __( 'stores' ) ?></label>
									<div class="col-sm-6">
										<select id="storesSource" style="width: 100%; height:36px;" name="storesSource" class="select2"><?php if ( isset( $groupData['merchantDetails'] ) && !empty( $groupData['merchantDetails'] ) ) : ?>
										<option value="<?php echo $groupData['merchantDetails']['id'] ?>"><?php echo $groupData['merchantDetails']['name'] ?></option>
										<?php endif ?></select>
										<small id="storesHelp" class="form-text text-muted"><?php echo __( 'group-stores-tip' ) ?></small>
									</div>-->
								</div>
								
								<div id="manufacturersDiv" class="row mb-3 d-none">
									<label for="input-manufacturer-sort" class="col-sm-2 col-form-label"><?php echo __( 'order-by' ) ?></label>
									<div class="col-sm-8">
										<div class="form-group clearfix">
											<div class="icheck-primary d-inline mr-1">
												<input type="radio" id="manufacturerOrder1" name="manufacturerOrder" value="name" <?php echo ( ( !empty( $groupData['manufacturer-order'] ) && ( $groupData['manufacturer-order'] == 'name' ) ) ? 'checked' : ( !isset( $groupData['manufacturer-order'] ) ? 'checked' : '' ) ) ?>>
												<label for="manufacturerOrder1"><?php echo __( 'name' ) ?></label>
											</div>
											<div class="icheck-primary d-inline mr-1">
												<input type="radio" id="manufacturerOrder2" name="manufacturerOrder" value="order" <?php echo ( ( !empty( $groupData['manufacturer-order'] ) && ( $groupData['manufacturer-order'] == 'order' ) ) ? 'checked' : '' ) ?>>
												<label for="manufacturerOrder2"><?php echo __( 'order' ) ?></label>
											</div>
											<div class="icheck-primary d-inline">
												<input class="form-check-input" type="radio" id="manufacturerOrder3" name="manufacturerOrder" value="count"<?php echo ( ( !empty( $groupData['manufacturer-order'] ) && ( $groupData['manufacturer-order'] == 'count' ) ) ? 'checked' : '' ) ?>>
												<label for="manufacturerOrder3"><?php echo __( 'count' ) ?></label>
											</div>
											
											<div class="d-inline mr-1">&nbsp;&nbsp;&dash;&nbsp;&nbsp;</div>
											<div class="icheck-primary d-inline">
												<input class="form-check-input" type="radio" id="manufacturerOrder4" name="manufacturerArrange" value="asc" <?php echo ( ( !empty( $groupData['sourceData'] ) && ( $groupData['sourceData']['arrange'] == 'asc' ) ) ? 'checked' : ( !isset( $groupData['sourceData']['arrange'] ) ? 'checked' : '' ) ) ?>>
												<label for="manufacturerOrder4"><?php echo __( 'asc' ) ?></label>
											</div>

											<div class="icheck-primary d-inline">
												<input class="form-check-input" type="radio" id="manufacturerOrder5" name="manufacturerArrange" value="desc" <?php echo ( ( !empty( $groupData['sourceData'] ) && ( $groupData['sourceData']['arrange'] == 'desc' ) ) ? 'checked' : '' ) ?>>
												<label for="manufacturerOrder5"><?php echo __( 'desc' ) ?></label>
											</div>
										</div>
									</div>
								
									<!--
									<label for="input-stores" class="col-sm-2 col-form-label"><?php echo __( 'manufacturers' ) ?></label>
									<div class="col-sm-6">
										<select id="manufacturerSource" style="width: 100%; height:36px;" name="manufacturerSource" class="select2"><?php if ( isset( $groupData['manufacturerDetails'] ) && !empty( $groupData['manufacturerDetails'] ) ) : ?>
										<option value="<?php echo $groupData['manufacturerDetails']['id'] ?>"><?php echo $groupData['manufacturerDetails']['name'] ?></option>
										<?php endif ?></select>
										<small id="manufacturersHelp" class="form-text text-muted"><?php echo __( 'group-manufacturers-tip' ) ?></small>
									</div>-->
								</div>

								<div id="customTypeDiv" class="row mb-3 d-none">
									<label for="input-group-custom-post" class="col-sm-2 col-form-label"><?php echo __( 'custom-post-type' ) ?></label>
									<div class="col-sm-6">
										<select name="customType" class="form-control">
											<option value="" <?php echo ( ( isset( $groupData['custom-type'] ) && ( $groupData['custom-type'] == '' ) ) ? 'selected' : '' ) ?>>---</option>
											<?php if ( !empty( $custom ) ):
												foreach( $custom as $cus ) : ?>
												<option value="<?php echo $cus['id'] ?>" <?php echo ( ( isset( $groupData['custom-type'] ) && ( $groupData['custom-type'] == $cus['id'] ) ) ? 'selected' : '' ) ?>><?php echo $cus['title'] ?></option>
											<?php endforeach; endif; ?>
										</select>
										<small id="attributeHelp" class="form-text text-muted"><?php echo __( 'custom-post-type-tip' ) ?></small>
									</div>
								</div>
								
								<!--
								<div id="attributeDiv" class="row mb-3 d-none">
									<label for="input-attribute" class="col-sm-2 col-form-label"><?php echo __( 'attribute' ) ?></label>
									<div class="col-sm-6">
										<select name="attribute" class="form-control">
											<option value="">---</option>
											<?php if ( !empty( $atts ) ):
												foreach( $atts as $att ) : ?>
												<option value="<?php echo $att['id'] ?>" <?php echo ( ( isset( $groupData['attribute'] ) && ( $groupData['attribute'] == $att['id'] ) ) ? 'selected' : '' ) ?>><?php echo $att['name'] ?></option>
											<?php endforeach; endif; ?>
										</select>
										<small id="attributeHelp" class="form-text text-muted"><?php echo __( 'group-attribute-tip' ) ?></small>
									</div>
								</div>
								
								<div id="attributeGroupDiv" class="row mb-3 d-none">
									<label for="input-group-attribute-group" class="col-sm-2 col-form-label"><?php echo __( 'attribute-group' ) ?></label>
									<div class="col-sm-6">
										<select name="attribute-group" class="form-control">
											<option value="" <?php echo ( ( isset( $groupData['att-group'] ) && ( $groupData['att-group'] == '' ) ) ? 'selected' : '' ) ?>>---</option>
											<?php if ( !empty( $groups ) ):
												foreach( $groups as $att ) : ?>
												<option value="<?php echo $att['id'] ?>" <?php echo ( ( isset( $groupData['att-group'] ) && ( $groupData['att-group'] == $att['id'] ) ) ? 'selected' : '' ) ?>><?php echo $att['name'] ?></option>
											<?php endforeach; endif; ?>
										</select>
										<small id="attributeHelp" class="form-text text-muted"><?php echo __( 'group-attribute-group-tip' ) ?></small>
									</div>
								</div>
								
								<div id="priceOrderDiv" class="row mb-3 d-none">
									<label for="input-group-priceOrder" class="col-sm-2 col-form-label"><?php echo __( 'order-by' ) ?></label>
									<div class="col-sm-8">
										<div class="form-group clearfix">
											<div class="icheck-primary d-inline mr-1">
												<input type="radio" id="priceOrder1" name="priceOrder" value="store" checked>
												<label for="priceOrder1"><?php echo __( 'store' ) ?></label>
											</div>
											<div class="icheck-primary d-inline mr-1">
												<input type="radio" id="priceOrder2" name="priceOrder" value="price-asc">
												<label for="priceOrder2"><?php echo __( 'price-asc' ) ?></label>
											</div>
											<div class="icheck-primary d-inline">
												<input class="form-check-input" type="radio" id="priceOrder3" name="priceOrder" value="price-desc">
												<label for="priceOrder3"><?php echo __( 'price-desc' ) ?></label>
											</div>
										</div>
									</div>
								</div>-->
									
								<div id="stockDiv" class="d-none">
									<div class="row mb-3">
										<label for="input-group-displayed-statuses" class="col-sm-2 col-form-label"><?php echo __( 'displayed-statuses' ) ?></label>
										<div class="col-sm-6">
											<div class="form-group">
												<div class="form-check">
													<input class="form-check-input" id="inStockInput" type="checkbox" name="inStock" value="1" <?php echo ( !empty( $groupData['stock-status'] ) && ( $groupData['stock-status']['in-stock'] ) ? 'checked' : ( !isset( $groupData['stock-status'] ) ? 'checked' : '' ) ) ?>>
													<label class="form-check-label"><?php echo __( 'in-stock' ) ?></label>
												</div>
												<div class="form-check">
													<input class="form-check-input" id="outOfStockInput" type="checkbox" name="outOfStock" value="1" <?php echo ( !empty( $groupData['stock-status'] ) && ( $groupData['stock-status']['out-of-stock'] ) ? 'checked' : ( !isset( $groupData['stock-status'] ) ? 'checked' : '' ) ) ?>>
													<label class="form-check-label"><?php echo __( 'out-of-stock' ) ?></label>
												</div>
												<div class="form-check">
													<input class="form-check-input" id="onBackorder" type="checkbox" name="onBackorder" value="1" <?php echo ( !empty( $groupData['stock-status'] ) && ( $groupData['stock-status']['on-backorder'] ) ? 'checked' : ( !isset( $groupData['stock-status'] ) ? 'checked' : '' ) ) ?>>
													<label class="form-check-label"><?php echo __( 'on-backorder' ) ?></label>
												</div>
											</div>
										</div>
									</div>
									
									<div id="inStockDisplayDiv" class="row mb-3">
										<label for="input-group-in-stock-display" class="col-sm-2 col-form-label"><?php echo __( 'in-stock-text' ) ?></label>
										<div class="col-sm-6">
											<input class="form-control form-check-input" name="inStockText" type="text" value="<?php echo ( ( !empty( $groupData['stock-status'] ) && isset( $groupData['stock-status']['in-stock-text'] ) ) ? $groupData['stock-status']['in-stock-text'] : ( !isset( $groupData['stock-status'] ) ? __( 'in-stock' ) : '' ) ) ?>" placeholder="<?php echo __( 'in-stock' ) ?>">
										</div>
									</div>
									
									<div id="outOfStockDisplayDiv" class="row mb-3">
										<label for="input-group-out-of-stock-display" class="col-sm-2 col-form-label"><?php echo __( 'out-of-stock-text' ) ?></label>
										<div class="col-sm-6">
											<input class="form-control form-check-input" name="outOfStockText" type="text" value="<?php echo ( ( !empty( $groupData['stock-status'] ) && isset( $groupData['stock-status']['out-of-stock-text'] ) ) ? $groupData['stock-status']['out-of-stock-text'] : ( !isset( $groupData['stock-status'] ) ? __( 'out-of-stock' ) : '' ) ) ?>" placeholder="<?php echo __( 'out-of-stock' ) ?>">
										</div>
									</div>
									
									<div id="onBackorderDisplayDiv" class="row mb-3">
										<label for="input-group-on-backorder-display" class="col-sm-2 col-form-label"><?php echo __( 'on-backorder-text' ) ?></label>
										<div class="col-sm-6">
											<input class="form-control form-check-input" name="onBackorderText" type="text" value="<?php echo ( ( !empty( $groupData['stock-status'] ) && isset( $groupData['stock-status']['on-backorder-text'] ) ) ? $groupData['stock-status']['on-backorder-text'] : ( !isset( $groupData['stock-status'] ) ? __( 'on-backorder' ) : '' ) ) ?>" placeholder="<?php echo __( 'on-backorder' ) ?>">
										</div>
									</div>
								</div>
								
								<div id="tagsDiv" class="d-none">
									<div class="row mb-3">
										<label for="input-group-tags" class="col-sm-2 col-form-label"><?php echo __( 'tags' ) ?></label>
										<div class="col-sm-6">
											<select name="tag" class="custom-select">
												<option value="0" <?php echo ( empty( $groupData['tag'] ) ? 'selected' : '' ) ?>><?php echo __( 'all' ) ?></option>
												<?php foreach( $tags as $tag ) : ?>
													<option value="<?php echo $tag['id'] ?>" <?php echo ( ( !empty( $groupData['tag'] ) && ( $groupData['tag'] == $tag['id'] ) ) ? 'selected' : '' ) ?>><?php echo $tag['name'] ?></option>
												<?php endforeach ?>
											</select>
											<small id="tagsHelp" class="form-text text-muted"><?php echo __( 'group-tags-tip' ) ?></small>
										</div>
									</div>
									
									<div id="tagsDisplayDiv" class="row mb-3">
										<label for="input-group-tags-display" class="col-sm-2 col-form-label"><?php echo __( 'display' ) ?></label>
										<div class="col-sm-6">
											<select id="tagsDisplay" name="tagsDisplay" class="custom-select">
												<?php foreach( $sourceTagsDisplayArray as $dId => $display ) : ?>
													<option value="<?php echo $dId ?>" <?php echo ( ( !empty( $groupData['tags-display'] ) && ( $groupData['tags-display'] == $dId ) ) ? 'selected' : '' ) ?>><?php echo $display['title'] ?></option>
												<?php endforeach ?>
											</select>
										</div>
									</div>
									
									<div id="loader4" class="row mb-3 d-none">
										<label for="input-group-loader" class="col-sm-2 col-form-label">&nbsp;</label>
										<div class="col-sm-2">
											<div class="form-group">
												<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
											</div>
										</div>
									</div> 
									
									<div id="tagsIncludedDiv" class="row mb-3 d-none">
										<label for="input-group-tags-only" class="col-sm-2 col-form-label"><?php echo __( 'select-options' ) ?></label>
										<div class="col-sm-6">
											<select name="tags-included[]" class="custom-select" multiple>
												<?php foreach( $tags as $tag ) : ?>
													<option value="<?php echo $tag['id'] ?>" <?php echo ( ( !empty( $groupData['tags-included'] ) && in_array( $tag['id'], $groupData['tags-included'] ) ) ? 'selected' : '' ) ?>><?php echo $tag['name'] ?></option>
												<?php endforeach ?>
											</select>
											<small id="tagsIncludedHelp" class="form-text text-muted"><?php echo __( 'group-categories-included-tip' ) ?></small>
										</div>
									</div>
									
									<div id="tagsExcludedDiv" class="row mb-3 d-none">
										<label for="input-group-tags-except" class="col-sm-2 col-form-label"><?php echo __( 'select-options' ) ?></label>
										<div class="col-sm-6">
											<select name="tags-excluded[]" class="custom-select" multiple>
												<?php foreach( $tags as $tag ) : ?>
													<option value="<?php echo $tag['id'] ?>" <?php echo ( ( !empty( $groupData['tags-excluded'] ) && in_array( $tag['id'], $groupData['tags-excluded'] ) ) ? 'selected' : '' ) ?>><?php echo $tag['name'] ?></option>
												<?php endforeach ?>
											</select>
											<small id="tagsExcludedHelp" class="form-text text-muted"><?php echo __( 'group-categories-excluded-tip' ) ?></small>
										</div>
									</div>

									<div id="tagsOrderDiv" class="row mb-3">
										<label for="input-group-tags-order" class="col-sm-2 col-form-label"><?php echo __( 'order-by' ) ?></label>
										<div class="col-sm-8">
											<div class="form-group clearfix">
												<div class="icheck-primary d-inline mr-1">
													<input type="radio" id="tagsOrder1" name="tagsOrder" value="name" <?php echo ( ( !empty( $groupData['tags-order'] ) && ( $groupData['tags-order'] == 'name' ) ) ? 'checked' : ( !isset( $groupData['tags-order'] ) ? 'checked' : '' ) ) ?>>
													<label for="tagsOrder1"><?php echo __( 'name' ) ?></label>
												</div>
												<div class="icheck-primary d-inline mr-1">
													<input type="radio" id="tagsOrder2" name="tagsOrder" value="order" <?php echo ( ( !empty( $groupData['tags-order'] ) && ( $groupData['tags-order'] == 'order' ) ) ? 'checked' : ( !isset( $groupData['tags-order'] ) ? 'checked' : '' ) ) ?>>
													<label for="tagsOrder2"><?php echo __( 'order' ) ?></label>
												</div>
												<div class="icheck-primary d-inline">
													<input class="form-check-input" type="radio" id="tagsOrder3" name="tagsOrder" value="count" <?php echo ( ( !empty( $groupData['tags-order'] ) && ( $groupData['tags-order'] == 'count' ) ) ? 'checked' : ( !isset( $groupData['tags-order'] ) ? 'checked' : '' ) ) ?>>
													<label for="tagsOrder3"><?php echo __( 'count' ) ?></label>
												</div>
												
												<div class="d-inline mr-1">&nbsp;&nbsp;&dash;&nbsp;&nbsp;</div>
												<div class="icheck-primary d-inline">
													<input class="form-check-input" type="radio" id="tagsOrder4" name="tagsArrange" value="asc" <?php echo ( ( !empty( $groupData['tags-arrange'] ) && ( $groupData['tags-arrange'] == 'asc' ) ) ? 'checked' : ( !isset( $groupData['tags-arrange'] ) ? 'checked' : '' ) ) ?>>
													<label for="tagsOrder4"><?php echo __( 'asc' ) ?></label>
												</div>

												<div class="icheck-primary d-inline">
													<input class="form-check-input" type="radio" id="tagsOrder5" name="tagsArrange" value="desc" <?php echo ( ( !empty( $groupData['tags-arrange'] ) && ( $groupData['tags-arrange'] == 'desc' ) ) ? 'checked' : '' ) ?>>
													<label for="tagsOrder5"><?php echo __( 'desc' ) ?></label>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<div id="categoryDiv" class="d-none">
								<!--
									<div id="categoryMainDiv" class="row mb-3">
										<label for="input-group-category" class="col-sm-2 col-form-label"><?php echo __( 'category' ) ?></label>
										<div class="col-sm-6">
											<select name="category" class="custom-select">
												<option value="0" <?php echo ( empty( $groupData['category'] ) ? 'selected' : '' ) ?>><?php echo __( 'all' ) ?></option>
												<?php foreach( $cats as $cat ) : ?>
													<option value="<?php echo $cat['id'] ?>" <?php echo ( ( !empty( $groupData['category'] ) && ( $groupData['category'] == $cat['id'] ) ) ? 'selected' : '' ) ?>><?php echo $cat['name'] ?></option>
												<?php endforeach ?>
											</select>
											<small id="categoriesHelp" class="form-text text-muted"><?php echo __( 'select-filter-category-tip' ) ?></small>
										</div>
									</div>-->
									
									<div id="categoryDisplayDiv" class="row mb-3">
										<label for="input-group-category-display" class="col-sm-2 col-form-label"><?php echo __( 'display' ) ?></label>
										<div class="col-sm-6">
											<select id="categoryDisplay" name="categoryDisplay" class="custom-select">
												<?php foreach( $sourceCategoryDisplayArray as $dId => $display ) : ?>
													<option value="<?php echo $dId ?>" <?php echo ( ( !empty( $groupData['category-display'] ) && ( $groupData['category-display'] == $dId ) ) ? 'selected' : '' ) ?>><?php echo $display['title'] ?></option>
												<?php endforeach ?>
											</select>
										</div>
									</div>
									
									<div id="loader3" class="row mb-3 d-none">
										<label for="input-group-loader" class="col-sm-2 col-form-label">&nbsp;</label>
										<div class="col-sm-2">
											<div class="form-group">
												<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
											</div>
										</div>
									</div> 
									
									<div id="categoryIncludedDiv" class="row mb-3 d-none">
										<label for="input-group-categories-only" class="col-sm-2 col-form-label"><?php echo __( 'select-options' ) ?></label>
										<div class="col-sm-6">
											<select name="categories-included[]" class="custom-select" multiple>
												<?php foreach( $cats as $cat ) : ?>
													<option value="<?php echo $cat['id'] ?>" <?php echo ( ( !empty( $groupData['categories-included'] ) && in_array( $cat['id'], $groupData['categories-included'] ) ) ? 'selected' : '' ) ?>><?php echo $cat['name'] ?></option>
												<?php endforeach ?>
											</select>
											<small id="categoryIncludedHelp" class="form-text text-muted"><?php echo __( 'group-categories-included-tip' ) ?></small>
										</div>
									</div>
									
									<div id="categoryExcludedDiv" class="row mb-3 d-none">
										<label for="input-group-categories-except" class="col-sm-2 col-form-label"><?php echo __( 'select-options' ) ?></label>
										<div class="col-sm-6">
											<select name="categories-excluded[]" class="custom-select" multiple>
												<?php foreach( $cats as $cat ) : ?>
													<option value="<?php echo $cat['id'] ?>" <?php echo ( ( !empty( $groupData['categories-excluded'] ) && in_array( $cat['id'], $groupData['categories-excluded'] ) ) ? 'selected' : '' ) ?>><?php echo $cat['name'] ?></option>
												<?php endforeach ?>
											</select>
											<small id="categoryExcludedHelp" class="form-text text-muted"><?php echo __( 'group-categories-excluded-tip' ) ?></small>
										</div>
									</div>

									<div id="categoryOrderDiv" class="row mb-3">
										<label for="input-group-categories" class="col-sm-2 col-form-label"><?php echo __( 'order-by' ) ?></label>
										<div class="col-sm-8">
											<div class="form-group clearfix">
												<div class="icheck-primary d-inline mr-1">
													<input type="radio" id="categoryOrder1" name="categoryOrder" value="name" <?php echo ( ( !empty( $groupData['category-order'] ) && ( $groupData['category-order'] == 'name' ) ) ? 'checked' : ( !isset( $groupData['category-order'] ) ? 'checked' : '' ) ) ?>>
													<label for="categoryOrder1"><?php echo __( 'name' ) ?></label>
												</div>
												<div class="icheck-primary d-inline mr-1">
													<input type="radio" id="categoryOrder2" name="categoryOrder" value="order" <?php echo ( ( !empty( $groupData['category-order'] ) && ( $groupData['category-order'] == 'order' ) ) ? 'checked' : '' ) ?>>
													<label for="categoryOrder2"><?php echo __( 'order' ) ?></label>
												</div>
												<div class="icheck-primary d-inline">
													<input class="form-check-input" type="radio" id="categoryOrder3" name="categoryOrder" value="count"<?php echo ( ( !empty( $groupData['category-order'] ) && ( $groupData['category-order'] == 'count' ) ) ? 'checked' : '' ) ?>>
													<label for="categoryOrder3"><?php echo __( 'count' ) ?></label>
												</div>
												
												<div class="d-inline mr-1">&nbsp;&nbsp;&dash;&nbsp;&nbsp;</div>
												<div class="icheck-primary d-inline">
													<input class="form-check-input" type="radio" id="catsOrderAsc1" name="categoryArrange" value="asc" <?php echo ( ( !empty( $groupData['category-arrange'] ) && ( $groupData['category-arrange'] == 'asc' ) ) ? 'checked' : ( !isset( $groupData['category-arrange'] ) ? 'checked' : '' ) ) ?>>
													<label for="catsOrderAsc1"><?php echo __( 'asc' ) ?></label>
												</div>
													
												<div class="icheck-primary d-inline">
													<input class="form-check-input" type="radio" id="catsOrderAsc2" name="categoryArrange" value="desc" <?php echo ( ( !empty( $groupData['category-arrange'] ) && ( $groupData['category-arrange'] == 'desc' ) ) ? 'checked' : '' ) ?>>
													<label for="catsOrderAsc2"><?php echo __( 'desc' ) ?></label>
												</div>
											</div>
										</div>
									</div>
								</div>
								<hr />
								
								<!-- Target Options -->
								<div class="row mb-3">
									<label for="input-group-target" class="col-sm-2 col-form-label"><?php echo __( 'select-target' ) ?></label>
									<div class="col-sm-6">
										<select name="target" id="targetOptions" class="form-control">
											<option value="">---</option>
											<?php foreach( $targetOptionsArray as $sId => $sType ) : ?>
												<option value="<?php echo $sId ?>" <?php echo ( ( isset( $groupData['target'] ) && ( $groupData['target'] == $sId ) ) ? 'selected' : '' ) ?>><?php echo $sType['title'] ?></option>
											<?php endforeach ?>
										</select>
										<small id="targetHelp" class="form-text text-muted"><?php echo __( 'select-target-filter-tip' ) ?></small>
									</div>
								</div>
								
								<div id="loader6" class="row mb-3 d-none">
									<label for="input-group-target-loader" class="col-sm-2 col-form-label">&nbsp;</label>
									<div class="col-sm-2">
										<div class="form-group">
											<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
										</div>
									</div>
								</div>
								
								<div id="targetAttributeDiv" class="row mb-3 d-none">
									<label for="input-target-attribute" class="col-sm-2 col-form-label"><?php echo __( 'attribute' ) ?></label>
									<div class="col-sm-6">
										<select name="attributeToLook" class="form-control">
											<option value="">---</option>
											<?php if ( !empty( $atts ) ):
												foreach( $atts as $att ) : ?>
												<option value="<?php echo $att['id'] ?>" <?php echo ( ( isset( $groupData['target-att'] ) && ( $groupData['target-att'] == $att['id'] ) ) ? 'selected' : '' ) ?>><?php echo $att['name'] ?></option>
											<?php endforeach; endif; ?>
										</select>
										<small id="attributeHelp" class="form-text text-muted"><?php echo __( 'group-attribute-target-tip' ) ?></small>
									</div>
								</div>
								<hr />
								
								<!-- Display Rules -->
								<div id="displayRulesOptionDiv" class="row mb-3">
									<label for="input-group-display-rules" class="col-sm-2 col-form-label"><?php echo __( 'display-rules' ) ?></label>
									<div class="col-sm-4">
										<div class="form-group">
											<label><?php echo __( 'show-this-element-if' ) ?></label>
											<select id="showElementIf" name="showElementIf" class="form-control">
											<?php foreach( $sourceOptionsSelectIfArray as $_id => $option ) : ?>
											<option value="<?php echo $_id ?>" <?php echo ( ( !empty( $groupData['show-element-if'] ) && ( $groupData['show-element-if'] == $_id ) ) ? 'selected' : '' ) ?>><?php echo $option['title'] ?></option>
											<?php endforeach ?>
											</select>
										</div>
									</div>
									
									<div class="col-sm-2">
										<div class="form-group">
										<label>&nbsp;</label>
										<select name="showElementOption" class="form-control">
											<option value="is-equal" <?php echo ( ( !empty( $groupData['show-element-option'] ) && ( $groupData['show-element-option'] == 'is-equal' ) ) ? 'selected' : '' ) ?>><?php echo __( 'is-equal-to' ) ?></option>
											<option value="is-not-equal" <?php echo ( ( !empty( $groupData['show-element-option'] ) && ( $groupData['show-element-option'] == 'is-not-equal' ) ) ? 'selected' : '' ) ?>><?php echo __( 'is-not-equal-to' ) ?></option>
										</select>
										</div>
									</div>
									
									<div id="loader1" class="col-sm-1 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
										</div>
									</div> 
									
									<div id="displayTargetCategoryDiv" class="col-sm-3 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<select id="displayTargetCategory" style="width: 100%; height:36px;" name="targetCategory" class="select2"><?php if ( isset( $groupData['targetCategoryDetails'] ) && !empty( $groupData['targetCategoryDetails'] ) ) : ?><option value="<?php echo $groupData['targetCategoryDetails']['id'] ?>"><?php echo $groupData['targetCategoryDetails']['name'] ?></option><?php endif ?></select>
										</div>
									</div>
									
									<div id="displayTargetTagDiv" class="col-sm-3 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<select id="displayTargetTag" style="width: 100%; height:36px;" name="targetTag" class="select2"><?php if ( isset( $groupData['targetTagDetails'] ) && !empty( $groupData['targetTagDetails'] ) ) : ?><option value="<?php echo $groupData['targetTagDetails']['id'] ?>"><?php echo $groupData['targetTagDetails']['name'] ?></option><?php endif ?>
											</select>
										</div>
									</div>
									
									<div id="displayTargetPagesDiv" class="col-sm-3 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<select id="displayTargetPages" style="width: 100%; height:36px;" name="targetPage" class="select2">
											</select>
										</div>
									</div>
									
									<div id="displayTargetAttDiv" class="col-sm-3 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<select name="targetAttribute" class="form-control">
												<option value="" <?php echo ( ( isset( $groupData['attribute'] ) && ( $groupData['attribute'] == '' ) ) ? 'selected' : '' ) ?>>---</option>
												<?php if ( !empty( $atts ) ):
													foreach( $atts as $att ) : ?>
													<option value="<?php echo $att['id'] ?>" <?php echo ( ( isset( $groupData['attribute'] ) && ( $groupData['attribute'] == $att['id'] ) ) ? 'selected' : '' ) ?>><?php echo $att['name'] ?></option>
												<?php endforeach; endif; ?>
											</select>
										</div>
									</div>
									
									<div id="displayTargetCustomDiv" class="col-sm-3 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<select name="displayTargetCustom" class="form-control">
												<option value="0">---</option>
												<?php if ( !empty( $custom ) ):
													foreach( $custom as $cus ) : ?>
													<option value="<?php echo $att['id'] ?>" <?php echo ( ( isset( $groupData['attribute'] ) && ( $groupData['attribute'] == $att['id'] ) ) ? 'selected' : '' ) ?>><?php echo $att['title'] ?></option>
												<?php endforeach; endif; ?>
											</select>
										</div>
									</div>
								</div>
								
								<div id="displayRulesOptionHideDiv" class="row mb-3">
									<label for="input-group-display-rules-hide" class="col-sm-2 col-form-label">&nbsp;</label>
									<div class="col-sm-4">
										<div class="form-group">
											<label><?php echo __( 'hide-this-element-if' ) ?></label>
											<select id="hideElementIf" name="hideElementIf" class="form-control">
											<option value="">---</option>
											<?php foreach( $sourceOptionsSelectIfArray as $_id => $option ) : ?>
											<option value="<?php echo $_id ?>" <?php echo ( ( !empty( $groupData['hide-element-if'] ) && ( $groupData['hide-element-if'] == $_id ) ) ? 'selected' : '' ) ?>><?php echo $option['title'] ?></option>
											<?php endforeach ?>
											</select>
										</div>
									</div>
									
									<div class="col-sm-2">
										<div class="form-group">
										<label>&nbsp;</label>
										<select name="hideElementOption" class="form-control">
										<option value="is-equal" <?php echo ( ( !empty( $groupData['hide-element-option'] ) && ( $groupData['hide-element-option'] == 'is-equal' ) ) ? 'selected' : '' ) ?>><?php echo __( 'is-equal-to' ) ?></option>
										<option value="is-not-equal" <?php echo ( ( !empty( $groupData['hide-element-option'] ) && ( $groupData['hide-element-option'] == 'is-not-equal' ) ) ? 'selected' : '' ) ?>><?php echo __( 'is-not-equal-to' ) ?></option>
										</select>
										</div>
									</div>
									
									<div id="loader5" class="col-sm-1 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
										</div>
									</div>
									
									<div id="displayTargetCategoryHideDiv" class="col-sm-3 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<select id="displayTargetCategoryHide" style="width: 100%; height:36px;" name="targetCategoryHide" class="select2"><?php if ( isset( $groupData['targetCategoryHideDetails'] ) && !empty( $groupData['targetCategoryHideDetails'] ) ) : ?><option value="<?php echo $groupData['targetCategoryHideDetails']['id'] ?>"><?php echo $groupData['targetCategoryHideDetails']['name'] ?></option><?php endif ?></select>
										</div>
									</div>
									
									<div id="displayTargetTagHideDiv" class="col-sm-3 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<select id="displayTargetTagHide" style="width: 100%; height:36px;" name="targetTagHide" class="select2"><?php if ( isset( $groupData['targetTagHideDetails'] ) && !empty( $groupData['targetTagHideDetails'] ) ) : ?><option value="<?php echo $groupData['targetTagHideDetails']['id'] ?>"><?php echo $groupData['targetTagHideDetails']['name'] ?></option><?php endif ?>
											</select>
										</div>
									</div>
									
									<div id="displayTargetPagesHideDiv" class="col-sm-3 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<select id="displayTargetPagesHide" style="width: 100%; height:36px;" name="targetPageHide" class="select2">
											</select>
										</div>
									</div>
									
									<div id="displayTargetAttHideDiv" class="col-sm-3 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<select name="targetAttributeHide" class="form-control">
												<option value="" <?php echo ( ( isset( $groupData['attribute'] ) && ( $groupData['attribute'] == '' ) ) ? 'selected' : '' ) ?>>---</option>
												<?php if ( !empty( $atts ) ):
													foreach( $atts as $att ) : ?>
													<option value="<?php echo $att['id'] ?>" <?php echo ( ( isset( $groupData['attribute'] ) && ( $groupData['attribute'] == $att['id'] ) ) ? 'selected' : '' ) ?>><?php echo $att['name'] ?></option>
												<?php endforeach; endif; ?>
											</select>
										</div>
									</div>
									
									<div id="displayTargetCustomHideDiv" class="col-sm-3 d-none">
										<div class="form-group">
											<label>&nbsp;</label>
											<select name="displayTargetCustomHide" class="form-control">
												<option value="0">---</option>
												<?php if ( !empty( $custom ) ):
													foreach( $custom as $cus ) : ?>
													<option value="<?php echo $att['id'] ?>" <?php echo ( ( isset( $groupData['attribute'] ) && ( $groupData['attribute'] == $att['id'] ) ) ? 'selected' : '' ) ?>><?php echo $att['title'] ?></option>
												<?php endforeach; endif; ?>
											</select>
										</div>
									</div>
								</div>
								<?php endif ?>
						</fieldset>
						
						<div id="loader7" class="col-sm-1 d-none">
							<div class="form-group">
								<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
							</div>
						</div> 

						<fieldset id="filterValues" class="d-none">
						<legend><?php echo __( 'filter-values' ) ?></legend>
							<table id="filter" class="table table-bordered table-hover">
								<thead>
									<tr>
										<td class="text-start required"><?php echo __( 'filter-name' ) ?></td>
										<td class="text-end"><?php echo __( 'sort-order' ) ?></td>
										<td></td>
									</tr>
								</thead>
								<tbody>
								<?php 
								if ( $edit && !empty( $Filter['data'] ) ) : 

									foreach ( $Filter['data'] as $data ) :
										
										//Set the current ID as last filter id
										$lastFilterId = $data['id'];

								?>
									<tr id="filter-row<?php echo $data['id'] ?>">
										<td><input type="text" name="filter[<?php echo $data['id'] ?>][name]" value="<?php echo $data['name'] ?>" placeholder="<?php echo __( 'filter-name' ) ?>" id="input-filter-<?php echo $data['id'] ?>" class="form-control"/></td>
										<td><input type="number" name="filter[<?php echo $data['id'] ?>][sort_order]" value="<?php echo $data['order'] ?>" placeholder="<?php echo __( 'sort-order' ) ?>" id="input-sort-order-<?php echo $data['id'] ?>" class="form-control" min="0" max="100" /></td>
										<td class="text-end"><button type="button" onclick="$('#filter-row<?php echo $data['id'] ?>').remove();" data-bs-toggle="tooltip" title="<?php echo __( 'remove' ) ?>" class="btn btn-danger"><i class="fas fa-minus-circle"></i></button></td>
									</tr>
								<?php endforeach ?>
								<?php endif ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="2"></td>
										<td class="text-end"><button type="button" id="button-filter" data-bs-toggle="tooltip" title="<?php echo __( 'add-filter' ) ?>" class="btn btn-primary"><i class="fas fa-plus-circle"></i></button></td>
									</tr>
								</tfoot>
							</table>
						</fieldset>
				</div>
			</div>
			
			<input type="hidden" id="lastFilterId" name="lastFilterId" value="<?php echo $lastFilterId ?>">
			
			<input type="hidden" id="hasFilters" name="hasFilters" value="<?php ( ( $edit && !empty( $Filter['data'] ) ) ? 'true' : 'false' ) ?>">
			
			<input type="hidden" name="_token" value="<?php echo ( $edit ? generate_token( 'edit-filter_' . $Filter['id'] ) : generate_token( 'add-filter' ) ) ?>">
			<div class="align-middle">
				<div class="float-left mt-1">
					<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo __( 'save' ) ?></button>
					<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'filters' ) ?>" role="button"><?php echo __( 'cancel' ) ?></a>
				</div>
			</div>
		</form>
	</div>
</div>