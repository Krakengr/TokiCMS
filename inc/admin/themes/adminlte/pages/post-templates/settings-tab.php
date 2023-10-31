<?php
	require ( ARRAYS_ROOT . 'seo-arrays.php' );
	$xtraPostData = ( isset( $xtraData['postData'] ) ? $xtraData['postData'] : null );
?>
<h5><?php echo $L['post-settings'] ?></h5>
<div class="mb-3">
<?php if ( !$Admin->SiteIsSelfHosted() ) : ?>
	<div id="externalPostUrl" class="form-group">
		<label for="externalPostUrl"><?php echo $L['external-url-address'] ?></label>
		<input type="text" id="externalUrl" name="postExtra[redirection_url]" class="form-control mb-4" placeholder="https://" value="<?php echo $Post->ExternalUrl() ?>">
		<small id="externalUrlHelp" class="form-text text-muted"><?php echo $L['external-url-address-tip'] ?></strong></small>
	</div>

	<div id="externalPostId" class="form-group">
		<label for="externalPostId"><?php echo $L['external-sync-id'] ?></label>
		<input type="text" id="externalId" name="postExtra[ext_id]" class="form-control mb-4" value="<?php echo $Post->ExternalId() ?>">
		<small id="externalIdHelp" class="form-text text-muted"><?php echo $L['external-sync-id-tip'] ?></strong></small>
	</div>

<?php else : ?>

	<div class="form-group">
		<label for="inputSubtitle"><?php echo $L['subtitle'] ?></label>
		<textarea class="form-control" id="inputSubtitle" name="postExtra[subtitle]" rows="3"><?php echo ( isset( $xtraPostData['subtitle'] ) ? html_entity_decode( $xtraPostData['subtitle'] ) : '' ) ?></textarea>
		<small id="inputSubtitle" class="form-text text-muted"><?php echo $L['post-subtitle-tip'] ?></strong></small>
	</div>
	
	<?php if ( $canManufact ) : ?>
	
	<div class="form-group">
		<label for="input-manufacturer" class="form-label"><?php echo __( 'choose-manufacturer' ) ?></label>
		<select class="form-control select2" name="manufacturer" data-placeholder="<?php echo __( 'choose-manufacturer' ) ?>" id="selManufacturer" style="width: 100%;"><?php if ( !empty( $xtraData['manufacturer'] ) ) : ?>
		<option value="<?php echo $xtraData['manufacturer']['id'] ?>"><?php echo $xtraData['manufacturer']['title'] ?></option>
		<?php endif ?></select>
		<small id="inputManufacturer" class="form-text text-muted"><?php echo __( 'choose-manufacturer-tip' ) ?></strong></small>
	</div>
	<?php endif ?>
	
	<?php 
	$args = array(
			'id' => 'disable-comments',
			'label' => __( 'disable-comments' ),
			'name' => 'postExtra[disable_comments]',
			'checked' => ( !empty( $xtraPostData['disable_comments'] ) ? true : false ), 
			'disabled' => ( !$Admin->Settings()::IsTrue( 'enable_comments' ) ? true : false ),
			'tip' => sprintf( $L['disable-s-setting-tip'], $L['comments'] )
	);

	CheckBox( $args );
	?>

	<?php if ( $Admin->Settings()::IsTrue( 'enable_amp' ) )
	{
		$args = array(
			'id' => 'disable-amp',
			'label' => __( 'disable-amp' ),
			'name' => 'postExtra[disable_amp]',
			'checked' => ( ( isset( $xtraPostData['disable_amp'] ) && !empty( $xtraPostData['disable_amp'] ) ) ? true : false ), 
			'disabled' => false,
			'tip' => sprintf( $L['disable-s-setting-tip'], $L['amp'] )
		);

		CheckBox( $args );
	}
	?>
	
	<?php if ( $Admin->Settings()::IsTrue( 'notify_search_engines' ) )
	{
		$args = array(
			'id' => 'ping-search-engines',
			'label' => __( 'dont-ping-search-engines' ),
			'name' => 'postExtra[dont_ping_search_engines]',
			'checked' => ( ( isset( $xtraPostData['dont_ping_search_engines'] ) && !empty( $xtraPostData['dont_ping_search_engines'] ) ) ? true : false ), 
			'disabled' => false,
			'tip' => $L['dont-ping-search-engines-tip']
		);

		CheckBox( $args );
	}
	?>
	
	<?php
	$args = array(
		'id' => 'hide-from-home-page',
		'label' => __( 'hide-post-from-home-page' ),
		'name' => 'postExtra[hideOnHome]',
		'checked' => ( !empty( $Post->HideOnHome() ) ? true : false ), 
		'disabled' => false,
		'tip' => $L['hide-post-from-home-page-tip']
	);

	CheckBox( $args );
	?>
	
	Hide a Post From Home Page
	
	<?php if ( $Admin->Settings()::IsTrue( 'enable_ads' ) )
	{
		$args = array(
			'id' => 'disable-ads',
			'label' => __( 'disable-ads' ),
			'name' => 'postExtra[disable_ads]',
			'checked' => ( ( isset( $xtraPostData['disable_ads'] ) && !empty( $xtraPostData['disable_ads'] ) ) ? true : false ), 
			'disabled' => false,
			'tip' => sprintf( $L['disable-s-setting-tip'], $L['ads'] )
		);

		CheckBox( $args );
	}
	?>
	
	<div id="redirectPost" class="form-group">
		<label for="inputredirectPost"><?php echo $L['redirect-to-another-web-address'] ?></label>
		<input type="text" id="redirectPost" name="postExtra[redirection_url]" class="form-control mb-4" placeholder="https://" value="<?php echo $Post->ExternalUrl() ?>">
		<small id="inputredirectPost" class="form-text text-muted"><?php echo $L['redirect-to-another-web-address-tip'] ?></strong></small>
	</div>
	
	<?php
	$args = array(
		'id' => 'minor-edit',
		'label' => __( 'this-is-a-minor-edit' ),
		'name' => 'minor_edit',
		'checked' => true, 
		'disabled' => false,
		'tip' => __( 'this-is-a-minor-edit-tip' )
	);

	CheckBox( $args );
	?>
	
	<div id="editReasonDiv" class="form-group d-none">
		<label for="inputEditReason"><?php echo $L['reason-for-edit'] ?></label>
		<textarea class="form-control" id="editReason" name="postExtra[modified_reason]" rows="3"><?php echo ( isset( $xtraPostData['modified_reason'] ) ? html_entity_decode( $xtraPostData['modified_reason'] ) : '' ) ?></textarea>
		<small id="inputEditReason" class="form-text text-muted"><?php echo $L['reason-for-edit-tip'] ?></strong></small>
	</div>
<?php endif ?>
</div>