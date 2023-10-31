<?php
	require ( ARRAYS_ROOT . 'generic-arrays.php');
	$aGroup = ( !empty( $Widget['groups'] ) ? Json( $Widget['groups'] ) : null );
	
	$pos = $Admin->ThemePosition();
?><div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form class="tab-content" id="form" method="post" action="" autocomplete="off">
					<div class="form-row">
						<div class="form-group col-md-6">
							<h4><?php echo $L['edit-widget'] ?>: <?php echo $Widget['title'] ?></h4>

							<div class="form-group">
								<label for="widgetName"><?php echo $L['name'] ?></label>
								<input type="text" class="form-control" name="widgetName" id="widgetName" value="<?php echo htmlspecialchars( $Widget['title'] ) ?>">
								<small id="widgetNameHelp" class="form-text text-muted"><?php echo $L['widget-name-tip'] ?></small>
							</div>
							
							<div class="form-group">
								<label for="widgetType"><?php echo $L['widget-type'] ?></label>
								<select class="form-select" id="widgetType" name="widgetType">
								  <?php foreach( $widgetTypes as $w => $t ) : ?>
									<option value="<?php echo $t['name'] ?>" <?php echo ( ( $t['name'] == $Widget['type'] ) ? 'selected' : '' ) ?>><?php echo $t['title'] ?></option>
									<?php endforeach ?>
								  </select>
								<small id="widgetTypeHelp" class="form-text text-muted"><?php echo $L['widget-type-tip'] ?></small>
							</div>
							
							<div class="form-group">
								<label for="widgetType"><?php echo $L['theme-position'] ?></label>
								<?php if ( empty( ThemeValue( 'widget-position' ) ) ) : ?>
									<?php echo $L['your-theme-does-not-natively-support-widgets'] ?>
								<?php else : ?>
									<select class="form-select" id="widgetThemePos" name="widgetThemePos">
										<?php 
										//$pos = ( isset( ThemeValue( 'widget-position' )['0'] ) ? ThemeValue( 'widget-position' )['0'] : ThemeValue( 'widget-position' ) );
										foreach( $pos as $k => $w ) : ?>
											<option value="<?php echo $k ?>" <?php echo ( ( $k == $Widget['theme_pos'] ) ? 'selected' : '' ) ?>><?php echo $w['name'] ?></option>
										<?php endforeach ?>
									</select>
							<?php endif ?>
							</div>

							<?php if ( $Widget['type'] == 'built-in' ) : ?>
							<div class="form-group">
								<label for="built-in"><?php echo $L['built-in-widgets'] ?></label>
								<select class="form-select" id="built-in" name="built-in">
									<option value="">...</option>
									<?php foreach( $builtInWidgets as $w => $t ) : ?>
										<option value="<?php echo $t['name'] ?>" <?php echo ( ( $t['name'] == $Widget['build_in'] ) ? 'selected' : '' ) ?>><?php echo $t['title'] ?></option>
									<?php endforeach ?>
								</select>
							</div>
							
								<?php if ( ( $Widget['build_in'] == 'latest-posts' ) || ( $Widget['build_in'] == 'latest-comments' ) ) : ?>
										
									<div class="form-group">
										<label for="widgetCode"><?php echo $L['number-of-items-to-show'] ?></label>
										<input class="form-control" value="<?php echo $Widget['num'] ?>" type="number" name="num" step="any" min="1" max="10">
									</div>
								<?php endif ?>
								
								<?php if ( ( $Widget['build_in'] == 'categories-list' ) || ( $Widget['build_in'] == 'tags-list' ) || ( $Widget['build_in'] == 'languages-list' ) ) : ?>
									
									<div class="form-group">
										<label for="widgetdropDown"><?php echo $L['show-drop-down-list'] ?></label>
										<input type="checkbox" name="dropDown" value="true" <?php echo ( $Widget['show_dropdown_list'] ? 'checked' : '' ) ?>>
									</div>
								<?php endif ?>
								
								<?php if ( ( $Widget['build_in'] == 'categories-list' ) || ( $Widget['build_in'] == 'tags-list' ) ) : ?>
									
									<div class="form-group">
										<label for="showPostNum"><?php echo $L['show-number-of-posts'] ?></label>
										<input type="checkbox" name="showPostNum" value="true" <?php echo ( $Widget['show_num_posts'] ? 'checked' : '' ) ?>>
									</div>
									
								<?php endif ?>
								
							<?php elseif ( $Widget['type'] == 'ad' ) :
								
								$ads = GetAdminAds( 'sidebar' );
							?>
								<div class="form-group">
									<label for="widgetAds"><?php echo $L['ads'] ?></label>
									<select class="form-select" id="widgetAd" name="widgetAd">
										<option value="0">...</option>
										<?php if ( !empty( $ads ) ) :
											foreach( $ads as $ad ) : ?>
											<option value="<?php echo $ad['id'] ?>" <?php echo ( ( $ad['id'] == $Widget['id_ad'] ) ? 'selected' : '' ) ?>><?php echo $ad['title'] ?></option>
										<?php endforeach ?><?php endif ?>
									</select>
									<small id="widgetAdHelp" class="form-text text-muted"><?php echo sprintf( $L['widget-choose-ad-tip'], $Admin->GetUrl( 'tools' ) ) ?></small>
								</div>
							
							<?php else : ?>
								<?php if ( $Widget['type'] == 'php' ) : ?>
									<div class="form-group">
										<label for="functionName"><?php echo $L['function-name'] ?></label>
										<input type="text" class="form-control" name="functionName" id="functionName" value="<?php echo htmlspecialchars( $Widget['function_name'] ) ?>">
										<small id="functionNameHelp" class="form-text text-muted"><?php echo $L['function-name-tip'] ?></small>
									</div>
								<?php endif ?>
							
								<div class="form-group">
									<label for="widgetCode"><?php echo $L['widget-code-text'] ?></label>
									<textarea class="form-control" id="widgetCode" name="widgetCode" rows="3"><?php echo htmlspecialchars( html_entity_decode( $Widget['data'] ) ) ?></textarea>
									<small id="widgetCodeHelp" class="form-text text-muted"><?php echo $L['widget-code-tip'] ?></small>
								</div>

							<?php endif ?>
							
							<h4><?php echo $L['widget-visibility'] ?></h4>
							<div class="form-group">
								<label for="inputFrontpagePage"><?php echo $L['membergroups'] ?></label>
								<select  name="membergroups[]" class="form-control select2 form-select shadow-none mt-3" multiple id="slcAmp" >
									<?php $groups = AdminGroups( $Admin->GetSite(), false );
										if ( !empty( $groups ) ) :
											foreach( $groups as $group ) : ?>
											<option  value="<?php echo $group['id_group'] ?>" <?php echo ( ( !empty( $aGroup ) && in_array( $group['id_group'], $aGroup ) ) ? 'selected' : '' ) ?>><?php echo $group['group_name'] ?></option>
										<?php endforeach ?>
									<?php endif ?>
								</select>
								<small id="membergroupsHelp" class="form-text text-muted"><?php echo $L['select-widget-membergroup-tip'] ?></small>
							</div>
							<div class="form-group">
								<label for="widgetVisibility1"><?php echo $L['show-if'] ?></label>
								<select class="form-select" id="widgetVisibility1" name="widgetVisibilityShow">
									<option value="">...</option>
									<?php foreach( $widgetVisibilityOptions as $w => $t ) : ?>
										<option value="<?php echo $t['name'] ?>" <?php echo ( ( $t['name'] == $Widget['enable_on'] ) ? 'selected' : '' ) ?>><?php echo $t['title'] ?></option>
									<?php endforeach ?>
								</select>
								<small id="widgetVisibility1Help" class="form-text text-muted"><?php echo $L['widget-visibility-show-tip'] ?></small>
							</div>
							<div class="form-group">
								<label for="widgetVisibility2"><?php echo $L['hide-if'] ?></label>
								<select class="form-select" id="widgetVisibility2" name="widgetVisibilityHide">
									<option value="">...</option>
									<?php foreach( $widgetVisibilityOptions as $w => $t ) : ?>
										<option value="<?php echo $t['name'] ?>" <?php echo ( ( $t['name'] == $Widget['exclude_from'] ) ? 'selected' : '' ) ?>><?php echo $t['title'] ?></option>
									<?php endforeach ?>
								</select>
								<small id="widgetVisibility2Help" class="form-text text-muted"><?php echo $L['widget-visibility-hide-tip'] ?></small>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="disable" <?php echo ( ( $Widget['disabled'] == 1 ) ? 'checked' : '' ) ?> />
								<label class="form-check-label" for="disableCheckBox">
									<?php echo $L['disable'] ?>
								</label>
								<small id="disableCheckBox" class="form-text text-muted"><?php echo $L['disable-widget-tip'] ?></small>
							</div>
		
							<hr />
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
								<label class="form-check-label" for="deleteCheckBox">
									<?php echo $L['delete'] ?>
								</label>
								<small id="deleteCheckBox" class="form-text text-muted"><?php echo $L['delete-widget-tip'] ?></small>
							</div>
							
							<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_widget_' . $Widget['id'] ) ?>">
							
							<div class="align-middle">
								<div class="float-left mt-1">
									<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
									<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'widgets' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
								</div>
							</div>
						
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>