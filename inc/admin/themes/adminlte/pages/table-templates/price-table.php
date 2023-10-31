<div class="card card-default">
	<div class="card-header">
		<h3 class="card-title">
			<?php echo __( 'table-builder' ) ?>
		</h3>
	</div>

	<div class="card-body" id="tableBuilder">
		<div class="row">
		<?php 
		if ( !empty( $Form['elements'] ) ) :
		
			foreach( $Form['elements'] as $elmnt ) : ?>
		
			<div class="col-sm">
				<section id="formBuilder" class="connectedSortable">
				<?php 
					if ( !empty( $Form['elements'] ) ) :

						foreach( $Form['elements'] as $elmnt ) : ?>

						<div data-id="<?php echo $elmnt['id'] ?>" id="form-item-<?php echo $elmnt['id'] ?>" class="card collapsed-card">
							<div class="card-header bg-light">
								<h3 class="card-title">
									<?php echo __( $elmnt['elementId'] ) ?>
								</h3>
								
								<div class="card-tools">
									<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
										<i class="fas fa-plus"></i>
									</button>
													
									<button type="button" id="close" data-id="<?php echo $elmnt['id'] ?>" class="btn btn-tool remElementButton">
										<i class="fas fa-times"></i>
									</button>
								</div>
							</div>
											
							<div class="card-body">
								<?php if ( !empty( $elmnt['data'] ) ) : ?>
									<?php// BuildFormElementHtml( $elmnt['data'], $elmnt['elementId'], $elmnt['id'], true ) ?>
								<?php endif ?>
							</div>
						</div>
						<?php endforeach ?>
					<?php endif ?>
				</section>
			</div>
		<?php endforeach ?>
		<?php endif ?>
		</div>
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
					<li class="nav-item">
						<a class="nav-link" id="template-tab" data-toggle="pill" href="#template" role="tab" aria-controls="template" aria-selected="false"><?php echo __( 'template' ) ?></a>
					</li>
				</ul>
			</div>
			<form class="tab-content" id="form" method="post" action="" autocomplete="off">
			
			<div class="card-body">
				<div class="tab-content" id="form-settings-tabsContent">
					<div class="tab-pane fade show active" id="table-options" role="tabpanel" aria-labelledby="table-options-tab">
						<div class="form-group">
							<label for="formName"><?php echo __( 'name' ) ?></label>
							<input class="form-control" type="text" id="formName" name="title" value="<?php echo $Form['name'] ?>">
						</div>
						
						<div class="form-group">
							<label for="inputMembergroups"><?php echo __( 'membergroups' ) ?></label>
							<select name="membergroups[]" class="form-control select2 form-select shadow-none mt-3" multiple id="slcAmp" >
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
								<option value="custom" <?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] == 'custom' ) ) ? 'selected' : '' ) ?>><?php echo __( 'custom-location' ) ?></option>
								<option value="beginning" <?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] == 'beginning' ) ) ? 'selected' : '' ) ?>><?php echo __( 'at-the-beginning-of-the-post' ) ?></option>
								<option value="middle" <?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] == 'middle' ) ) ? 'selected' : '' ) ?>><?php echo __( 'at-the-middle-of-the-post' ) ?></option>
								<option value="end" <?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] == 'end' ) ) ? 'selected' : '' ) ?>><?php echo __( 'at-the-end-of-the-post' ) ?></option>
							</select>
							
							<small id="autoInsertTableHelp" class="form-text text-muted"><?php echo __( 'auto-insert-table-tip' ) ?></small>
						</div>
						
						<div id="autoInsertFormGroup" class="<?php echo ( ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] ) != '' ) ? '' : 'd-none' ) ?>">

							<!-- Display Rules -->
							<div id="displayRulesOptionDiv" class="row mb-3">
								
								<div class="col-sm-4">
									<div class="form-group">
										<label><?php echo __( 'show-this-table-if' ) ?></label>
										<select id="showTableIf" name="show-table-if" class="form-control">
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
					
					<div class="tab-pane fade" id="template" role="tabpanel" aria-labelledby="template-tab">
					
					<?php if ( !isset( $Form['data']['saved_as_template'] ) || ( isset( $Form['data']['saved_as_template'] ) && !$Form['data']['saved_as_template'] ) ) : ?>
						
						<div class="form-check">
							<input id="saveFormTemplate" class="form-check-input" type="checkbox" value="1" name="save-template" />
							<label class="form-check-label" for="saveFormTemplate">
								<?php echo __( 'save-this-form-to-use-as-a-template' ) ?>
							</label>
							<small id="saveFormTemplateHelp" class="form-text text-muted"><?php echo __( 'save-form-as-template-tip' ) ?></small>
						</div>
						
						<div class="form-group">
							<label for="formTemplateName"><?php echo __( 'name' ) ?></label>
							<input class="form-control" type="text" id="formTemplateName" name="formTemplateName" value="">
							<small id="formTemplateNameHelp" class="form-text text-muted"><?php echo __( 'enter-the-name-of-your-template-tip' ) ?></small>
						</div>
					<?php else : ?>
						<div class="form-check">
							<input id="deleteFormTemplate" class="form-check-input" type="checkbox" value="1" name="delete-template"/>
							<label class="form-check-label" for="deleteFormTemplate">
								<?php echo __( 'delete' ) ?>
							</label>
							<small id="deleteFormTemplateHelp" class="form-text text-muted"><?php echo sprintf( __( 'delete-form-template-tip' ), $Form['data']['template_name'] ) ?></small>
						</div>
						
						<input type="hidden" name="saved-template" value="<?php echo $Form['data']['template_id'] ?>">
					<?php endif ?>
					</div>
					
				</div>
			</div>
		</div>