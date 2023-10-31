<?php
	include ( ARRAYS_ROOT . 'generic-arrays.php');
	
	$defaultImage 	= HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS . 'default.svg';
	$userImage 		= ( !empty( $User['image_data'] ) ? Json( $User['image_data'] ) : null );
	$userImage 		= ( $userImage ? $userImage['default']['imageUrl'] : null );
	$transData 		= ( !empty( $User['trans_data'] ) ? Json( $User['trans_data'] ) : null );
	$socialData 	= ( !empty( $User['social_data'] ) ? Json( $User['social_data'] ) : null );
	$socialData 	= ( ( !empty( $socialData ) && isset( $socialData[$Admin->CurrentLang()['lang']['code']] ) ) ? $socialData[$Admin->CurrentLang()['lang']['code']] : null );
?>
<div class="row">
  <div class="col-12">
  <div class="card">
  <div class="card-body">
	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
	<div class="form-row">
    <div class="form-group col-md-6">
		<h4><?php echo $L['edit-user'] ?>: <?php echo $User['user_name'] ?></h4>
		
		<nav class="mb-3">
			<div class="nav nav-tabs" id="nav-tab" role="tablist">
				<a class="nav-item nav-link active" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"><?php echo $L['profile'] ?></a>
				<a class="nav-item nav-link" id="nav-picture-tab" data-toggle="tab" href="#picture" role="tab" aria-controls="nav-picture" aria-selected="false"><?php echo $L['profile-picture'] ?></a>
				<?php if ( $Admin->MultiLang() ) : ?>
					<a class="nav-item nav-link" id="nav-profile-trans-tab" data-toggle="tab" href="#profile-trans" role="tab" aria-controls="nav-profile-trans" aria-selected="false"><?php echo $L['profile-translations'] ?></a>
				<?php endif ?>
				<a class="nav-item nav-link" id="nav-security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="nav-security" aria-selected="false"><?php echo $L['security'] ?></a>
				<a class="nav-item nav-link" id="nav-social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="nav-social" aria-selected="false"><?php echo $L['social-networks'] ?></a>
			</div>
		</nav>

		<div class="tab-content" id="nav-tabContent">
			
			<!-- Profile tab -->
			<div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="nav-profile-tab">
				<div class="form-group row">
					<label for="usernameDisabled" class="col-sm-2 col-form-label"><?php echo $L['username'] ?></label>
					<div class="col-sm-10">
						<input class="form-control " id="usernameDisabled" name="usernameDisabled" value="<?php echo $User['user_name'] ?>" type="text" disabled >
						<small class="form-text text-muted"></small>
					</div>
				</div>
			
				<div class="form-group row">
					<label for="group" class="col-sm-2 col-form-label"><?php echo $L['membergroup'] ?></label>
					<div class="col-sm-10">
						<select id="group" name="group" class="form-control">
						<?php $groups = AdminGroups( $Admin->GetSite(), false );
						if ( !empty( $groups ) ) :
							foreach( $groups as $group ) : ?>
							<option  value="<?php echo $group['id_group'] ?>" <?php echo ( ( $group['id_group'] == $User['id_group'] ) ? 'selected' : '' ) ?>><?php echo $group['group_name'] ?></option>
						<?php endforeach ?> <?php endif ?>
						</select>
						<small class="form-text text-muted"></small>
					</div>
				</div>
				
				<div class="form-group row">
					<label for="email" class="col-sm-2 col-form-label"><?php echo $L['email'] ?></label>
					<div class="col-sm-10">
						<input class="form-control " id="email" name="email" value="<?php echo $User['email_address'] ?>" placeholder="my@email.com" type="text"  >
						<small class="form-text text-muted"></small>
					</div>
				</div>
			<?php if ( $Admin->DefaultLang()['id'] == $Admin->GetLang() ) : ?>
				<div class="form-group row">
					<label for="nickname" class="col-sm-2 col-form-label"><?php echo $L['nickname'] ?></label>
					<div class="col-sm-10">
						<input class="form-control " id="nickname" name="nickname" value="<?php echo $User['real_name'] ?>" type="text"  >
						<small class="form-text text-muted"><?php echo $L['the-nickname-is-being-used-in-the-themes-to-display-the-author-of-the-content'] ?></small>
					</div>
				</div>
				
				<div class="form-group row">
					<label for="userBio" class="col-sm-2 col-form-label"><?php echo $L['user-bio'] ?></label>
					<div class="col-sm-10">
						<textarea class="form-control" id="userBio" name="user_bio" rows="3"><?php echo html_entity_decode( $User['user_bio'] ) ?></textarea>
						<small id="userBioHelp" class="form-text text-muted"><?php echo $L['user-bio-tip'] ?></strong></small>
					</div>
				</div>
			<?php endif ?>
				<div class="form-group row">
					<label for="registered" class="col-sm-2 col-form-label"><?php echo $L['registered'] ?></label>
					<div class="col-sm-10">
						<input class="form-control " id="registered" name="registered" value="<?php echo postDate( $User['date_registered'] ) ?>" type="text" disabled />
					</div>
				</div>
			
			<?php if ( ( $User['id_group'] == '1' ) && ( $User['id_member'] == $AuthUser['id_member'] ) ) : ?>
				<div class="form-group row">
					<label for="currentPassword" class="col-sm-2 col-form-label"><?php echo $L['current-password'] ?></label>
					<div class="col-sm-10">
						<input class="form-control " autocomplete="chrome-off" list="autocompleteOff" name="currentPassword" value="" type="password"  >
						<small class="form-text text-muted"><?php echo $L['current-password-tip'] ?></small>
					</div>
				</div>
			<?php endif ?>
			</div>

			<!-- Profile picture tab -->
			<div class="tab-pane fade" id="picture" role="tabpanel" aria-labelledby="nav-picture-tab">
				<div class="container">
					<div class="row">
						<div class="col-lg-4 col-sm-12 p-0 pr-2">
							<div class="custom-file">
								<input type="file" class="form-control" id="profilePictureInputFile" name="profilePictureInputFile" accept="image/*">
								<input type="hidden" name="ProfileImageFile" id="ProfileImageFile" value="<?php echo ( $userImage ? $userImage : '' ) ?>">
								<label class="custom-file-label" for="profilePictureInputFile"><?php echo $L['upload-image'] ?></label>
							</div>
						</div>
						<div class="col-lg-8 col-sm-12 p-0 text-center">
							<img id="profilePicturePreview" class="img-fluid img-thumbnail" alt="Profile picture preview" src="<?php echo ( !$userImage ? $defaultImage : $userImage ) ?>" />
							<button id="buttonRemoveImage" type="button" class="btn btn-primary w-100 mt-4 mb-4 <?php echo ( !$userImage ? 'd-none' : '' ) ?>"><i class="fa fa-trash"></i><?php echo $L['remove-image'] ?></button>
						</div>
					</div>
				</div>
				
				<script>
				$("#buttonRemoveImage").on("click", function()
				{
					$("#ProfileImageFile").val('');
					$("#profilePicturePreview").attr("src", "<?php echo $defaultImage ?>");
					$("#buttonRemoveImage").addClass('d-none');
				});
				
				$("#profilePictureInputFile").on("change", function() {
					var formData = new FormData();
					formData.append('token', "<?php echo $Admin->GetToken() ?>");
					formData.append('site', "<?php echo $Admin->GetSite() ?>");
					formData.append('file', $(this)[0].files[0]);
					formData.append('userid', "<?php echo $User['id_member'] ?>");
					$.ajax({
						url: "<?php echo AJAX_ADMIN_PATH ?>user-logo-upload/",
						type: "POST",
						data: formData,
						cache: false,
						contentType: false,
						processData: false
					}).done(function(data) {
						if (data.status==0) {
							$("#profilePicturePreview").attr('src',data.imageURL);
							$("#ProfileImageFile").attr('value',data.imageURL);
							$("#buttonRemoveImage").removeClass('d-none');
						} else {
							showAlert(data.message);
						}
					});
				});
				</script>
			</div>

			<!-- Security tab -->
			<div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="nav-security-tab">

				<div class="form-group row">
					<label for="status" class="col-sm-2 col-form-label"><?php echo $L['status'] ?></label>
					<div class="col-sm-10">
						<select id="status" name="status" class="form-control">
							<option value="enabled" <?php echo ( $User['is_activated'] ? 'selected' : '' ) ?>><?php echo $L['enabled'] ?></option>
							<option value="disabled" <?php echo ( !$User['is_activated'] ? 'selected' : '' ) ?>><?php echo $L['disabled'] ?></option>
						</select>
					</div>
				</div>
				
				<h6 class="mt-4 mb-2 pb-2 border-bottom text-uppercase"><?php echo $L['change-password'] ?></h6>
				
				<div class="form-group row">
					<label for="status" class="col-sm-2 col-form-label"><?php echo $L['new-password'] ?></label>
					<div class="col-sm-10">
						<input class="form-control " id="newPass" name="newPass" value="" placeholder="" type="password"  >
						<small class="form-text text-muted"><?php echo $L['new-password-tip'] ?></small>
					</div>
				</div>
				<div class="form-group row">
					<label for="status" class="col-sm-2 col-form-label"><?php echo $L['confirm-password'] ?></label>
					<div class="col-sm-10">
						<input class="form-control " id="newPass2" name="newPass2" value="" placeholder="" type="password"  >
					</div>
				</div>
			</div>
		
		<?php if ( $Admin->MultiLang() ) : ?>
		<!-- Translation tab -->
			<div class="tab-pane fade" id="profile-trans" role="tabpanel" aria-labelledby="nav-profile-trans-tab">
			<div class="alert alert-info" role="alert"><?php echo $L['profile-translations-tip'] ?></div>
			<?php $Langs = $Admin->Settings()::AllLangs();
					foreach( $Langs as $li => $la ) :
						
						if ( $la['lang']['id'] == $Admin->DefaultLang()['id'] )
							continue;

						$pName = ( ( !empty( $transData ) && isset( $transData[$la['lang']['code']] ) ) ? $transData[$la['lang']['code']]['name'] : '' );
						$pBio  = ( ( !empty( $transData ) && isset( $transData[$la['lang']['code']] ) ) ? $transData[$la['lang']['code']]['bio'] : '' );
			?>
				<h6 class="mt-4 mb-2 pb-2 border-bottom text-uppercase">
					<img src="<?php echo  SITE_URL . 'languages' . PS . 'flags' . PS . $la['lang']['flagicon'] ?>" title="<?php echo $la['lang']['title'] ?>" alt="<?php echo $la['lang']['title'] ?>" width="16" height="11" style="width: 16px; height: 11px;" />
					<?php echo $la['lang']['title'] . 
						( ( $la['lang']['id'] == $Admin->GetLang() ) ? ' (' . $L['current'] . ')' : '' ) . 
						( ( $la['lang']['id'] == $Admin->DefaultLang()['id'] ) ? ' (' . $L['default'] . ')' : '' )?>
				</h6>
				<div class="form-group">
					<label for="lang-<?php echo $la['lang']['id'] ?>-name" class="col-sm-2 col-form-label"><?php echo $L['nickname'] ?></label>
					<input class="form-control " id="lang-<?php echo $la['lang']['id'] ?>-name" name="trans[<?php echo $li ?>][name]" value="<?php echo $pName ?>" type="text" />
					<small id="userNameHelpLang-<?php echo $la['lang']['id'] ?>" class="form-text text-muted"><?php echo $L['the-nickname-is-being-used-in-the-themes-to-display-the-author-of-the-content'] ?></small>
				</div>
				
				<div class="form-group">
					<label for="lang-<?php echo $la['lang']['id'] ?>-bio" class="col-sm-2 col-form-label"><?php echo $L['user-bio'] ?></label>
					<textarea class="form-control" id="lang-<?php echo $la['lang']['id'] ?>-bio" name="trans[<?php echo $li ?>][bio]" rows="3"><?php echo htmlspecialchars( $pBio ) ?></textarea>
					<small id="userBioHelpLang-<?php echo $la['lang']['id'] ?>" class="form-text text-muted"><?php echo $L['user-bio-tip'] ?></strong></small>
				</div>
			<?php endforeach ?>
			</div>
		<?php endif ?>
		
			<!-- Social Networks tab -->
			<div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="nav-social-tab">
				<?php if ( $Admin->DefaultLang()['id'] != $Admin->GetLang() ) : ?>
					<div class="alert alert-info" role="alert"><?php echo sprintf( $L['social-networks-non-default-language-tip'], $Admin->CurrentLang()['lang']['title'], $Admin->DefaultLang()['title'], $Admin->DefaultLang()['title'] ) ?></div>
				<?php endif ?>
			<?php foreach( $socialNetworksArray as $id => $row ) :
			
			?>
				<div class="form-group row">
					<label for="<?php echo $id ?>" class="col-sm-2 col-form-label"><?php echo $row['title'] ?></label>
					<div class="col-sm-10">
						<input class="form-control " id="<?php echo $id ?>" name="social[<?php echo $Admin->CurrentLang()['lang']['code'] ?>][<?php echo $row['name'] ?>]" value="<?php echo ( ( !empty( $socialData ) && isset( $socialData[$id] ) ) ? $socialData[$id] : '' ) ?>" type="text">
						<small class="form-text text-muted"></small>
					</div>
				</div>
			<?php endforeach ?>
			</div>
	</div>
		
		
		
		
		
		<?php /*
			
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

					if ( $c['isChild'] || ( $c['id'] == $Cat['id'] ) )
						continue;
				?>
					<option value="<?php echo $c['id'] ?>" <?php echo ( ( ( $Cat['id_parent'] > 0 ) && ( $Cat['id_parent'] == $c['id'] ) ? 'selected' : '' ) ) ?>><?php echo $c['name'] ?></option>
				<?php unset( $c ); endforeach; endif; ?>
			</select>
			<small id="parentHelp" class="form-text text-muted"><?php echo $L['category-parent-tip'] ?></strong></small>
		</div>
		
		<div class="form-group">
			<label for="inputDescription"><?php echo $L['description'] ?></label>
			<textarea class="form-control" id="inputDescription" name="description" rows="3"><?php echo htmlspecialchars( $Cat['descr'] ) ?></textarea>
			<small id="descriptionHelp" class="form-text text-muted"><?php echo $L['category-descr-tip'] ?></strong></small>
		</div>
		
		<div class="form-group">
			<label for="categoryParent"><?php echo $L['post-type'] ?></label>
			<select class="form-select shadow-none" style="width: 100%; height:36px;" name="postFormat" aria-label="Select Format">
			<?php $types = GetAdminCustomTypes();
				foreach( $types as $type ) : ?>
					<option value="<?php echo $type['id'] ?>" <?php echo ( ( !empty( $Cat['id_custom_type'] ) && ( $Cat['id_custom_type'] == $type['id'] ) ) ? 'selected' : '' ) ?><?php echo ( ( empty( $Cat['id_custom_type'] ) && $type['is_default'] ) ? 'selected' : '' ) ?>><?php echo $type['title'] ?></option>
			<?php unset( $type ); endforeach; unset( $types ); ?>
			</select>
			<small id="parentHelp" class="form-text text-muted"><?php echo $L['custom-post-types-categores-tip'] ?></strong></small>
		</div>

		<?php if ( !$Cat['is_default'] && ( MULTISITE || $Admin::MultiLang() || $Admin::MultiBlog() ) ) : ?>
		<div class="form-group">
			<label for="inputCatFilterTrans"><?php echo $L['move-category'] ?></label>
			<select class="form-select shadow-none" style="width: 100%; height:36px;" name="move-category" aria-label="Move select">
					<option value="0" selected><?php echo $L['choose'] ?>...</option>
					<?php if ( MULTISITE ) : ?>
						<optgroup label="<?php echo $L['sites'] ?>">
						<?php if ( ( $Admin::GetSite() != SITE_ID ) && ( $Cat['id_site'] != SITE_ID ) ): ?>
							<option value="site::<?php echo SITE_ID ?>"><?php echo $site['title'] ?></option>
						<?php endif ?>	
							<?php if ( !empty( $sites ) ) :
									foreach ( $sites as $singeSite ) :
										if ( $Cat['id_site'] == $singeSite['id'] )
											continue;
							?>
							<option value="site::<?php echo $singeSite['id'] ?>"><?php echo $singeSite['title'] ?></option>
							<?php endforeach ?>
							<?php endif ?>	
						</optgroup>
					<?php endif ?>
					
					<?php if ( $Admin::MultiLang() ) : 
							$Langs = Langs( $Admin::GetSite(), false );
					?>
						<optgroup label="<?php echo $L['langs'] ?>">
						<?php if ( ( $Admin::DefaultLang()['lang']['id'] != $Admin::GetLang() ) && ( $Cat['id_lang'] != $Admin::DefaultLang()['lang']['id'] ) ): ?>
							<option value="lang::<?php echo $Admin::DefaultLang()['lang']['id'] ?>"><?php echo $Admin::DefaultLang()['lang']['title'] ?></option>
						<?php endif ?>	
							<?php $Langs = $Admin::OtherLangs(); 
								if ( !empty( $Langs ) ) :
									foreach( $Langs as $lId => $lData ) :
										if ( $Cat['id_lang'] == $lId )
											continue;
							?>
							<option value="lang::<?php echo $lId ?>"><?php echo $lData['lang']['title'] ?></option>
							<?php endforeach ?>
							<?php endif ?>	
						</optgroup>
					<?php endif ?>
					
					<?php if ( $Admin::MultiBlog() ) : ?>
						<optgroup label="<?php echo $L['blogs'] ?>">
						<?php $Blogs = Blogs( $Admin::GetSite(), false );
								if ( !empty( $Blogs ) ) : 
									foreach( $Blogs as $bId => $bData ) :
										if ( ( $Cat['id_blog'] > 0 ) && ( $Cat['id_blog'] == $bId ) )
											continue;
						?><option value="blog::<?php echo $bId ?>"><?php echo $bData['name'] ?></option>
						<?php endforeach ?>
						<?php endif ?>	
						</optgroup>
					<?php endif ?>
				</select>
			<small id="inputCatFilterTransHelp" class="form-text text-muted"><?php echo $L['move-category-tip'] ?></strong></small>
		</div>
		<?php endif ?>
		
		<?php 
		//Only categories assigned in other languages but not the default 
		if ( $Admin::MultiLang() && ( $Cat['id_lang'] != $Admin::DefaultLang()['lang']['id'] ) ) : 
			$translations = adminGetCatTrans( $Cat['id_trans_parent'] );
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
			<script>
				$(document).ready(function() {
					var parent = $("#catParent").select2({
					placeholder: "",
					allowClear: true,
					theme: "bootstrap4",
					minimumInputLength: 2,
					ajax: {
						type: "POST",
						url: "<?php echo AJAX_ADMIN_PATH ?>get-categories/",
						data: function (params) {
							var query = 
							{
								lang: "<?php echo $Cat['id_lang'] ?>",
								blog: "<?php echo $Cat['id_blog'] ?>",
								site: "<?php echo $Cat['id_site'] ?>",
								catID: "<?php echo $Cat['id'] ?>",
								query: params.term
							}
							return query;
					},
					processResults: function (data) {
						return data;
					}
				},
				templateResult: function(data) {
						var html = data.text;
						return html;
				}
			});});</script>
		</div>
		<?php endif ?>
		
		<div class="form-group">
			<input class="form-check-input" type="checkbox" value="1" name="hideFront" id="hideCheckBox" <?php echo ( $Cat['hide_front'] ? 'checked' : '' ) ?>>
			<label class="form-check-label" for="hideFrontCheckBox">
				<?php echo $L['hide-category-from-the-homepage'] ?>
			</label>
			<small id="hideFrontHelp" class="form-text text-muted"><?php echo $L['hide-category-from-the-homepage-tip'] ?></small>
		</div>
		
		<?php if ( !$Cat['is_default'] ) : ?>
		<hr />
	
		<div class="form-check">
			<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
			<label class="form-check-label" for="deleteCheckBox">
				<?php echo $L['delete'] ?>
			</label>
			<small id="titleHelp" class="form-text text-muted"><?php echo $L['delete-category-tip'] ?></small>
		</div>
		<?php endif */?>
		
		<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_user_' . $User['id_member'] ) ?>">
		
		<div class="align-middle">
			<div class="float-left mt-1">
				<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
				<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
			</div>
		</div>
	</div>
	</div>
	</form>
</div>
</div>
</div>
</div>