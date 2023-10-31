<?php
	require ( ARRAYS_ROOT . 'generic-arrays.php');
	$aGroup = ( !empty( $Ad['groups'] ) ? Json( $Ad['groups'] ) : null );
	$cGroup = ( !empty( $Ad['exclude_ads'] ) ? Json( $Ad['exclude_ads'] ) : null );
?><div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form class="tab-content" id="form" method="post" action="" autocomplete="off">
					<div class="form-row">
						<div class="form-group col-md-6">
							<h4><?php echo $L['edit-ad'] ?>: <?php echo $Ad['title'] ?></h4>

							<div class="form-group">
								<label for="adName"><?php echo $L['name'] ?></label>
								<input type="text" class="form-control" name="adName" id="adName" value="<?php echo htmlspecialchars( $Ad['title'] ) ?>">
								<small id="adNameHelp" class="form-text text-muted"><?php echo $L['category-name-tip'] ?></small>
							</div>
							
							<div class="form-group">
								<label for="adType"><?php echo $L['type'] ?></label>
								<select class="form-select" id="adType" name="adType">
								<?php foreach( $adsTypes as $a => $b ) : ?>
									<option value="<?php echo $b['name'] ?>" <?php echo ( ( $Ad['type'] == $b['name'] ) ? 'selected' : '' ) ?>><?php echo $b['title'] ?></option>
								<?php endforeach ?>
								</select>
								<small id="adTypeHelp" class="form-text text-muted"><?php echo $L['ads-type-tip'] ?></small>
							</div>
							
							<div class="form-group">
								<label for="adPosition"><?php echo $L['placement'] ?></label>
								<select class="form-select" id="adPosition" name="adPosition">
								<?php foreach( $adsPosition as $a => $b ) : ?>
									<option value="<?php echo $b['name'] ?>" <?php echo ( ( $Ad['ad_pos'] == $b['name'] ) ? 'selected' : '' ) ?>><?php echo $b['title'] ?></option>
								<?php endforeach ?>
								</select>
								<small id="adPositionHelp" class="form-text text-muted"><?php echo $L['placement-ads-tip'] ?></small>
							</div>
							
							<div class="form-group">
								<label for="adSize"><?php echo $L['size'] ?></label>
								<?php echo $L['width'] ?> (px): <input class="form-control" min="0" max="999" type="number" value="<?php echo $Ad['width'] ?>" name="width"><br />
								<?php echo $L['height'] ?> (px): <input class="form-control" min="0" max="999" type="number" value="<?php echo $Ad['height'] ?>" name="height">
							</div>
							
							<?php if ( $Ad['type'] == 'plain-text' ) : ?>
							<div class="form-group">
								<label for="adCode"><?php echo $L['ad-code'] ?></label>
								<textarea class="form-control" id="adCode" name="adCode"><?php echo html_entity_decode( $Ad['ad_code'] ) ?></textarea>
								<small id="adCodeHelp" class="form-text text-muted"><?php echo $L['ad-code-tip'] ?></small>
							</div>
							<?php endif ?>
							
							<?php if ( $Ad['type'] == 'image' ) : ?>
							<div class="form-group">
								<label for="imgUrl"><?php echo $L['url'] ?></label>
								<input type="text" class="form-control" name="imgUrl" id="imgUrl" value="<?php echo $Ad['ad_img_url'] ?>" placeholder="https://" />
								<small id="imgUrlHelp" class="form-text text-muted"><?php echo $L['image-ad-url-tip'] ?></small>
							</div>
							<?php endif ?>

							<h4><?php echo $L['ad-visibility'] ?></h4>
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
								<small id="membergroupsHelp" class="form-text text-muted"><?php echo $L['select-ad-membergroup-tip'] ?></small>
							</div>
							
							<div class="form-group">
								<label for="suppress-settings" class="col-sm-2 col-form-label"><?php echo $L['suppress-ad-on'] ?></label>
								<select  name="content_types[]" class="form-control select2 form-select shadow-none mt-3" multiple id="slcAd" >
									<option value="posts" <?php echo ( ( !empty( $cGroup ) && in_array( 'posts', $cGroup ) ) ? 'selected' : '' ) ?> ><?php echo $L['posts'] ?></option>
									<option value="pages" <?php echo ( ( !empty( $cGroup ) && in_array( 'pages', $cGroup ) ) ? 'selected' : '' ) ?> ><?php echo $L['pages'] ?></option>
								</select>
								<small id="content-types" class="form-text text-muted"><?php echo $L['suppress-ad-on-tip'] ?></small>
							</div>
							
							<div class="form-group">
								<label for="align-settings" class="col-sm-2 col-form-label"><?php echo $L['position'] ?></label>
								<select class="form-select" id="adAlign" name="adAlign">
								<?php foreach( $adsBoxPosition as $a => $b ) : ?>
									<option value="<?php echo $b['name'] ?>" <?php echo ( ( $Ad['ad_align'] == $b['name'] ) ? 'selected' : '' ) ?>><?php echo $b['title'] ?></option>
								<?php endforeach ?>
								</select>
							</div>
							
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="disable" <?php echo ( ( $Ad['disabled'] == 1 ) ? 'checked' : '' ) ?> />
								<label class="form-check-label" for="disableCheckBox">
									<?php echo $L['disable'] ?>
								</label>
								<small id="disableCheckBox" class="form-text text-muted"><?php echo $L['disable-ad-tip'] ?></small>
							</div>
		
							<hr />
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
								<label class="form-check-label" for="deleteCheckBox">
									<?php echo $L['delete'] ?>
								</label>
								<small id="deleteCheckBox" class="form-text text-muted"><?php echo $L['delete-ad-tip'] ?></small>
							</div>
							
							<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_ad_' . $Ad['id'] ) ?>">
		
							<div class="align-middle">
								<div class="float-left mt-1">
									<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
									<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'ads' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
								</div>
							</div>
							
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>