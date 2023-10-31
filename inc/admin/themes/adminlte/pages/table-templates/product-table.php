<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title">
			<?php echo __( 'preview' ) ?>
		</h3>
	</div>

	<div class="card-body" id="tablePreview">
		<?php BuildTablePreviewHtml( $Form['elements'], true ) ?>
	</div>
</div>

<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title">
			<?php echo __( 'table-builder' ) ?>
		</h3>
		
		<div class="card-tools" id="addColumnTools">
			<button title="<?php echo __( 'add-a-column' ) ?>" type="button" id="addColumn" class="btn btn-tool">
				<i class="fas fa-plus"></i> <?php echo __( 'add-a-column' ) ?>
			</button>
			
			<button title="<?php echo __( 'expand-contract-all' ) ?>" type="button" id="expandAll" class="btn btn-tool">
				<i class="fas fa-compress"></i> <?php echo __( 'expand-contract-all' ) ?>
			</button>
		</div>
	</div>

	<div class="card-body" id="tableBuilder">  
		<section id="formBuilder" class="connectedSortable">
		<?php 
		if ( !empty( $Form['elements'] ) ) :

			foreach( $Form['elements'] as $elmnt ) : ?>

			<div data-id="<?php echo $elmnt['id'] ?>" id="table-item-<?php echo $elmnt['id'] ?>" class="card multi-collapse">
				<div class="card-header bg-light">
					<h3 class="card-title">
						<span id="elemntTitle<?php echo $elmnt['id'] ?>"><?php echo $elmnt['name'] ?></span>

						<div id="columnTitleDiv<?php echo $elmnt['id'] ?>" class="btn-group d-none">
							<input placeholder="<?php echo __( 'column-name' ) ?>" class="form-control" type="text" id="elemntTitleInput<?php echo $elmnt['id'] ?>" value="<?php echo $elmnt['name'] ?>" />
							<button type="button" id="cancelTitle<?php echo $elmnt['id'] ?>" data-id="<?php echo $elmnt['id'] ?>" class="btn btn-tool cancelTitleButton"><i class="fa fa-times"></i></button>
							<button type="button" id="saveTitle<?php echo $elmnt['id'] ?>" data-id="<?php echo $elmnt['id'] ?>" class="btn btn-tool saveTitleButton"><i class="fa fa-check"></i></button>
						</div>
						<button type="button" id="changeTitle<?php echo $elmnt['id'] ?>" data-id="<?php echo $elmnt['id'] ?>" class="btn btn-tool changeTitleButton">
							<i class="fas fa-edit"></i>
						</button>
					</h3>

					<div class="card-tools">
						<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-minus"></i>
						</button>

						<button type="button" id="close" data-id="<?php echo $elmnt['id'] ?>" class="btn btn-tool remColumnButton">
							<i class="fas fa-times"></i>
						</button>
					</div>
				</div>
				
				<!-- Head -->
				<div class="card-body">
				
					<ul class="nav nav-tabs" id="tabs-header-<?php echo $elmnt['id'] ?>-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="tab-header-<?php echo $elmnt['id'] ?>-head-tab" data-toggle="pill" href="#tab-header-<?php echo $elmnt['id'] ?>-head" role="tab" aria-controls="tab-header-<?php echo $elmnt['id'] ?>-head" aria-selected="true"><?php echo __( 'heading' ) ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tab-header-<?php echo $elmnt['id'] ?>-design-tab" data-toggle="pill" href="#tab-header-<?php echo $elmnt['id'] ?>-design" role="tab" aria-controls="tab-header-<?php echo $elmnt['id'] ?>-design" aria-selected="false"><?php echo __( 'design' ) ?></a>
						</li>
					</ul>

					<div class="card-body">
						<div class="tab-content" id="tabs-header-<?php echo $elmnt['id'] ?>-tabContent">
						
							<div class="tab-pane fade show active" parent="<?php echo $elmnt['id'] ?>" id="tab-header-<?php echo $elmnt['id'] ?>-head" role="tabpanel" aria-labelledby="tab-header-<?php echo $elmnt['id'] ?>-tab">

								<section id="contentHeaderBuilder<?php echo $elmnt['id'] ?>" class="connectedSortable2">
									<?php if ( !empty( $elmnt['elements']['header'] ) ) : ?>
										<?php BuildTableElementHtml( $elmnt['elements']['header'], 'header', true ) ?>
									<?php endif ?>
								</section>
								
								<button title="<?php echo __( 'add-element' ) ?>" data-id="<?php echo $elmnt['id'] ?>" type="button" class="btn btn-tool addColumnHeadElement">
									<i class="fas fa-plus"></i> <?php echo __( 'add-element' ) ?>
								</button>
							</div>
							<div class="tab-pane fade" id="tab-header-<?php echo $elmnt['id'] ?>-design" role="tabpanel" aria-labelledby="tab-<?php echo $elmnt['id'] ?>-design-tab">
								<?php BuildTableDesingHtml( $elmnt['id'], ( isset( $elmnt['data']['header'] ) ? $elmnt['data']['header'] : null ), 'header', true ) ?>
							</div>
						</div>
					</div>

				</div>
				
				<!-- Cell -->
				<div class="card-body">
				
					<ul class="nav nav-tabs" id="tabs-<?php echo $elmnt['id'] ?>-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="tab-<?php echo $elmnt['id'] ?>-cell-tab" data-toggle="pill" href="#tab-<?php echo $elmnt['id'] ?>-cell" role="tab" aria-controls="tab-<?php echo $elmnt['id'] ?>-cell" aria-selected="true"><?php echo __( 'cell-template' ) ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="tab-<?php echo $elmnt['id'] ?>-design-tab" data-toggle="pill" href="#tab-<?php echo $elmnt['id'] ?>-design" role="tab" aria-controls="tab-<?php echo $elmnt['id'] ?>-design" aria-selected="false"><?php echo __( 'design' ) ?></a>
						</li>
					</ul>

					<div class="card-body">
						<div class="tab-content" id="tabs-<?php echo $elmnt['id'] ?>-tabContent">
							<div class="tab-pane fade show active" parent="<?php echo $elmnt['id'] ?>" id="tab-<?php echo $elmnt['id'] ?>-cell" role="tabpanel" aria-labelledby="tab-<?php echo $elmnt['id'] ?>-cell-tab">

								<section id="contentCellBuilder<?php echo $elmnt['id'] ?>" class="connectedSortable2">
									<?php if ( !empty( $elmnt['elements']['cell'] ) ) : ?>
										<?php BuildTableElementHtml( $elmnt['elements']['cell'], 'cell', true ) ?>
									<?php endif ?>
								</section>
								
								<button title="<?php echo __( 'add-element' ) ?>" data-id="<?php echo $elmnt['id'] ?>" type="button" id="cell" class="btn btn-tool addColumnCellElement">
									<i class="fas fa-plus"></i> <?php echo __( 'add-element' ) ?>
								</button>
							</div>
							<div class="tab-pane fade" id="tab-<?php echo $elmnt['id'] ?>-design" role="tabpanel" aria-labelledby="tab-<?php echo $elmnt['id'] ?>-design-tab">
								<?php BuildTableDesingHtml( $elmnt['id'], ( isset( $elmnt['data']['cell'] ) ? $elmnt['data']['cell'] : null ), 'cell', true ) ?>
							</div>
						</div>
					</div>

				</div>
			</div>
			<?php endforeach ?>
		<?php endif ?>
		</section>
	</div>
</div>
	
		<!-- Options Card -->
		<div class="card card-primary card-outline card-tabs">
			<div class="card-header p-0 pt-1">
				<ul class="nav nav-tabs" id="form-settings-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="table-options-tab" data-toggle="pill" href="#table-options" role="tab" aria-controls="table-options" aria-selected="false"><?php echo __( 'table-options' ) ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="display-rules-tab" data-toggle="pill" href="#display-rules" role="tab" aria-controls="display-rules" aria-selected="false"><?php echo __( 'display-rules' ) ?></a>
					</li>
				</ul>
			</div>

			<div class="card-body">
				<div class="tab-content" id="form-settings-tabsContent">
					<div class="tab-pane fade show active" id="table-options" role="tabpanel" aria-labelledby="table-options-tab">
						<div class="form-group">
							<label for="formName"><?php echo __( 'name' ) ?></label>
							<input class="form-control" type="text" id="formName" name="title" value="<?php echo $Form['name'] ?>">
						</div>
						
						<div class="form-group">
							<label for="max-items"><?php echo __( 'max-items-per-page' ) ?></label>
							<input type="number" step="1" min="1" max="1000" class="form-control" id="max-items" value="<?php echo ( isset( $Form['data']['max_items'] ) ? $Form['data']['max_items'] : HOMEPAGE_ITEMS ) ?>" name="limit">
						</div>
						
						<div class="form-group">
							<label for="inputMembergroups"><?php echo __( 'membergroups' ) ?></label>
							<select name="membergroups[]" class="form-control select2 shadow-none mt-3" multiple id="slcAmp" >
								<?php $groups = AdminGroups( $Admin->GetSite(), false );
									if ( !empty( $groups ) ) :
										foreach( $groups as $group ) : ?>
										<option  value="<?php echo $group['id_group'] ?>" <?php echo ( ( !empty( $Form['groups'] ) && in_array( $group['id_group'], $Form['groups'] ) ) ? 'selected' : '' ) ?>><?php echo $group['group_name'] ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
							<small id="membergroupsHelp" class="form-text text-muted"><?php echo __( 'select-table-membergroup-tip' ) ?></small>
						</div>
						
						<div class="form-group">
							<label for="tableCss"><?php echo __( 'table-css-class' ) ?></label>
							<input class="form-control" type="text" id="tableCss" name="table-css" value="<?php echo ( isset( $Form['data']['table_css'] ) ? $Form['data']['table_css'] : '' ) ?>">
							<small id="tableCssHelp" class="form-text text-muted"><?php echo __( 'table-css-class-tip' ) ?></small>
						</div>

						<hr />
						
						<div class="form-check">
							<input id="disableCheckBox" class="form-check-input" type="checkbox" value="1" name="disable" <?php echo ( ( $Form['disabled'] == 1 ) ? 'checked' : '' ) ?> />
							<label class="form-check-label" for="disableCheckBox">
								<?php echo __( 'disable' ) ?>
							</label>
							<small id="disableCheckBox" class="form-text text-muted"><?php echo __( 'disable-table-tip' ) ?></small>
						</div>
				
						<div class="form-check">
							<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
							<label class="form-check-label" for="deleteCheckBox">
								<?php echo __( 'delete' ) ?>
							</label>
							<small id="deleteCheckBox" class="form-text text-muted"><?php echo __( 'delete-table-tip' ) ?></small>
						</div>
					</div>
					
					<div class="tab-pane fade" id="display-rules" role="tabpanel" aria-labelledby="display-rules-tab">
						
						<div class="form-group">
							<label><?php echo __( 'auto-insert-table' ) ?></label>
							<select id="autoInsertTable" name="auto-insert-table" class="form-control">
								<option value=""><?php echo __( 'select' ) ?>...</option>
								<option value="posts-archive" <?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] == 'posts-archive' ) ) ? 'selected' : '' ) ?>><?php echo __( 'post-archive-pages' ) ?></option>
								<option value="beginning" <?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] == 'beginning' ) ) ? 'selected' : '' ) ?>><?php echo __( 'at-the-beginning-of-the-post' ) ?></option>
								<option value="middle" <?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] == 'middle' ) ) ? 'selected' : '' ) ?>><?php echo __( 'at-the-middle-of-the-post' ) ?></option>
								<option value="end" <?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] == 'end' ) ) ? 'selected' : '' ) ?>><?php echo __( 'at-the-end-of-the-post' ) ?></option>
							</select>
							
							<small id="autoInsertTableHelp" class="form-text text-muted"><?php echo __( 'auto-insert-table-tip' ) ?></small>
						</div>
						
						<div id="autoInsertFormGroupCategory" class="<?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] == 'posts-archive' ) ) ? '' : 'd-none' ) ?>">
						
							<!-- Display Rules -->
							<div id="displayRulesCatOptionDiv" class="row mb-3">
								
								<div class="col-sm-4">
									<div class="form-group">
										<label><?php echo __( 'show-this-table-if' ) ?></label>
										<select id="showTableIfAuto" name="show-table-if-auto" class="form-control">
										<?php foreach( $sourceOptionsCatSelectIfArray as $_id => $option ) : ?>
										<option value="<?php echo $_id ?>" <?php echo ( ( !empty( $Form['data']['show_table_if'] ) && ( $Form['data']['show_table_if'] == $_id ) ) ? 'selected' : '' ) ?>><?php echo $option['title'] ?></option>
										<?php endforeach ?>
										</select>
									</div>
								</div>
									
								<div class="col-sm-3">
									<div class="form-group">
										<label>&nbsp;</label>
										<select name="show-element-option" class="form-control">
											<option value="is-equal" <?php echo ( ( !empty( $Form['data']['show_table_option'] ) && ( $Form['data']['show_table_option'] == 'is-equal' ) ) ? 'selected' : '' ) ?>><?php echo __( 'is-equal-to' ) ?></option>
											<option value="is-not-equal" <?php echo ( ( !empty( $Form['data']['show_table_option'] ) && ( $Form['data']['show_table_option'] == 'is-not-equal' ) ) ? 'selected' : '' ) ?>><?php echo __( 'is-not-equal-to' ) ?></option>
										</select>
									</div>
								</div>
									
								<div id="loaderShowAuto" class="col-sm-1 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
									</div>
								</div> 
									
								<div id="displayTargetCategoryDivAuto" class="col-sm-5 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<select id="displayTargetCategoryAuto" style="width: 100%; height:36px;" name="targetCategoryAuto" class="select2"><?php if ( isset( $Form['data']['show_target_category_auto'] ) && !empty( $Form['data']['show_target_category_auto'] ) ) : ?><option value="<?php echo $Form['data']['show_target_category_auto']['id'] ?>"><?php echo $Form['data']['show_target_category_auto']['name'] ?></option><?php endif ?></select>
									</div>
								</div>
									
								<div id="displayTargetTagDivAuto" class="col-sm-5 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<select id="displayTargetTagAuto" style="width: 100%; height:36px;" name="targetTagAuto" class="select2"><?php if ( isset( $Form['data']['show_target_tag_auto'] ) && !empty( $Form['data']['show_target_tag_auto'] ) ) : ?><option value="<?php echo $Form['data']['show_target_tag_auto']['id'] ?>"><?php echo $Form['data']['show_target_tag_auto']['name'] ?></option><?php endif ?>
										</select>
									</div>
								</div>
								
								<div id="displayTargetBlogDivAuto" class="col-sm-5 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<select id="displayTargetBlogAuto" style="width: 100%; height:36px;" name="targetBlogAuto" class="select2"><?php if ( isset( $Form['data']['show_target_blog_auto'] ) && !empty( $Form['data']['show_target_blog_auto'] ) ) : ?><option value="<?php echo $Form['data']['show_target_blog_auto']['id'] ?>"><?php echo $Form['data']['show_target_blog_auto']['name'] ?></option><?php endif ?>
										</select>
									</div>
								</div>
								
								<small id="displayTargetBlogAutoHelp" class="form-text text-muted d-none"><?php echo __( 'display-table-blog-tip' ) ?></small>
							</div>
						</div>
						
						<div id="autoInsertFormGroup" class="<?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( ( $Form['data']['auto_insert_table'] != '' ) && ( $Form['data']['auto_insert_table'] != 'posts-archive' ) ) ) ? '' : 'd-none' ) ?>">

							<!-- Display Rules -->
							<div id="displayRulesOptionDiv" class="row mb-3">
								
								<div class="col-sm-4">
									<div class="form-group">
										<label><?php echo __( 'show-this-table-if' ) ?></label>
										<select id="showTableIf" name="show-table-if" class="form-control">
										<option value="">---</option>
										<?php foreach( $sourceOptionsSelectIfArray as $_id => $option ) : ?>
										<option value="<?php echo $_id ?>" <?php echo ( ( !empty( $Form['data']['show_table_if'] ) && ( $Form['data']['show_table_if'] == $_id ) ) ? 'selected' : '' ) ?>><?php echo $option['title'] ?></option>
										<?php endforeach ?>
										</select>
									</div>
								</div>
									
								<div class="col-sm-3">
									<div class="form-group">
										<label>&nbsp;</label>
										<select name="show-element-option" class="form-control">
											<option value="is-equal" <?php echo ( ( !empty( $Form['data']['show_table_option'] ) && ( $Form['data']['show_table_option'] == 'is-equal' ) ) ? 'selected' : '' ) ?>><?php echo __( 'is-equal-to' ) ?></option>
											<option value="is-not-equal" <?php echo ( ( !empty( $Form['data']['show_table_option'] ) && ( $Form['data']['show_table_option'] == 'is-not-equal' ) ) ? 'selected' : '' ) ?>><?php echo __( 'is-not-equal-to' ) ?></option>
										</select>
									</div>
								</div>
									
								<div id="loaderShow" class="col-sm-1 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
									</div>
								</div> 
									
								<div id="displayTargetCategoryDiv" class="col-sm-5 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<select id="displayTargetCategory" style="width: 100%; height:36px;" name="targetCategory" class="select2"><?php if ( isset( $Form['data']['show_target_category'] ) && !empty( $Form['data']['show_target_category'] ) ) : ?><option value="<?php echo $Form['data']['show_target_category']['id'] ?>"><?php echo $Form['data']['show_target_category']['name'] ?></option><?php endif ?></select>
									</div>
								</div>
									
								<div id="displayTargetTagDiv" class="col-sm-5 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<select id="displayTargetTag" style="width: 100%; height:36px;" name="targetTag" class="select2"><?php if ( isset( $Form['data']['show_target_tag'] ) && !empty( $Form['data']['show_target_tag'] ) ) : ?><option value="<?php echo $Form['data']['show_target_tag']['id'] ?>"><?php echo $Form['data']['show_target_tag']['name'] ?></option><?php endif ?>
										</select>
									</div>
								</div>

								<div id="displayTargetCustomDiv" class="col-sm-5 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<select name="displayTargetCustom" id="displayTargetCustom" class="form-control">
											<option value="0">---</option>
											<?php if ( !empty( $custom ) ):
												foreach( $custom as $cus ) : ?>
												<option value="<?php echo $att['id'] ?>" <?php echo ( ( isset( $Form['data']['display_custom_type'] ) && ( $Form['data']['display_custom_type'] == $att['id'] ) ) ? 'selected' : '' ) ?>><?php echo $att['title'] ?></option>
											<?php endforeach; endif; ?>
										</select>
									</div>
								</div>

							</div>
							
							<div id="displayRulesOptionHideDiv" class="row mb-3">
								<div class="col-sm-4">
									<div class="form-group">
										<label><?php echo __( 'hide-this-table-if' ) ?></label>
										<select id="hideTableIf" name="hide-table-if" class="form-control">
											<option value="">---</option>
											<?php foreach( $sourceOptionsSelectIfArray as $_id => $option ) : ?>
											<option value="<?php echo $_id ?>" <?php echo ( ( !empty( $Form['data']['hide_table_if'] ) && ( $Form['data']['hide_table_if'] == $_id ) ) ? 'selected' : '' ) ?>><?php echo $option['title'] ?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
									
								<div class="col-sm-3">
									<div class="form-group">
										<label>&nbsp;</label>
										<select name="hide-element-option" class="form-control">
											<option value="is-equal" <?php echo ( ( !empty( $Form['data']['hide_table_option'] ) && ( $Form['data']['hide_table_option'] == 'is-equal' ) ) ? 'selected' : '' ) ?>><?php echo __( 'is-equal-to' ) ?></option>
											<option value="is-not-equal" <?php echo ( ( !empty( $Form['data']['hide_table_option'] ) && ( $Form['data']['hide_table_option'] == 'is-not-equal' ) ) ? 'selected' : '' ) ?>><?php echo __( 'is-not-equal-to' ) ?></option>
										</select>
									</div>
								</div>
									
								<div id="loaderHide" class="col-sm-1 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
									</div>
								</div>
									
								<div id="displayTargetCategoryHideDiv" class="col-sm-5 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<select id="displayTargetCategoryHide" style="width: 100%; height:36px;" name="targetCategoryHide" class="select2"><?php if ( isset( $Form['data']['hide_target_category'] ) && !empty( $Form['data']['hide_target_category'] ) ) : ?><option value="<?php echo $Form['data']['hide_target_category']['id'] ?>"><?php echo $Form['data']['hide_target_category']['name'] ?></option><?php endif ?></select>
									</div>
								</div>
									
								<div id="displayTargetTagHideDiv" class="col-sm-5 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<select id="displayTargetTagHide" style="width: 100%; height:36px;" name="targetTagHide" class="select2"><?php if ( isset( $Form['data']['hide_target_tag'] ) && !empty( $Form['data']['hide_target_tag'] ) ) : ?><option value="<?php echo $Form['data']['hide_target_tag']['id'] ?>"><?php echo $Form['data']['hide_target_tag']['name'] ?></option><?php endif ?>
										</select>
									</div>
								</div>
									
								<div id="displayTargetCustomHideDiv" class="col-sm-5 d-none">
									<div class="form-group">
										<label>&nbsp;</label>
										<select name="displayTargetCustomHide" id="displayTargetCustomHide" class="form-control">
											<option value="0">---</option>
											<?php if ( !empty( $custom ) ):
													foreach( $custom as $cus ) : ?>
													<option value="<?php echo $att['id'] ?>" <?php echo ( ( isset( $Form['data']['hide_custom_type'] ) && ( $Form['data']['hide_custom_type'] == $att['id'] ) ) ? 'selected' : '' ) ?>><?php echo $att['title'] ?></option>
											<?php endforeach; endif; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>