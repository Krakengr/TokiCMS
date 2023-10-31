<?php
include( ARRAYS_ROOT . 'generic-arrays.php');
$extSys 	= $Lang['ext_comm_system'];
$extSysName = $Lang['ext_comm_shortname'];
?>
<div class="row">
  <div class="col-12">
  <div class="card">
  <div class="card-body">
	<?php if ( $Lang['is_default'] ) : ?>
		<div class="alert alert-primary" role="alert">
			<?php echo sprintf( $L['can-not-edit-default-lang'], $Admin->GetUrl( 'language' )  ) ?>
		</div>
	<?php else : ?>

	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
	<div class="form-row">
    <div class="form-group col-md-6">
		<h4><?php echo $L['lang-settings'] ?></h4>
			
		<div class="form-group">
			<label for="inputTitle"><?php echo $L['title'] ?></label>
			<input type="text" class="form-control" name="title" id="inputTitle" value="<?php echo htmlspecialchars( $Lang['title'] ) ?>">
			<small id="titleHelp" class="form-text text-muted"><?php echo $L['add-title-tip'] ?></small>
		</div>

		<div class="form-group">
			<label for="inputLocale"><?php echo $L['locale'] ?></label>
			<input type="text" class="form-control" id="inputLocale" name="locale" value="<?php echo htmlspecialchars( $Lang['locale'] ) ?>">
			<small id="inputLocaleHelp" class="form-text text-muted"><?php echo $L['with-the-locales-you-can-set-the-regional-user-interface'] ?></small>
		</div>

		<div class="form-group">
			<label for="inputDateFormat"><?php echo $L['date-format'] ?></label>
			<input type="text" class="form-control" id="inputDateFormat" name="date_format" value="<?php echo htmlspecialchars( $Lang['date_format'] ) ?>">
			<small id="inputDateFormatHelp" class="form-text text-muted"><?php echo $L['current-format'] ?>: <strong><?php echo date( $Lang['date_format'] , time() ) ?></strong></small>
		</div>

		<div class="form-group">
			<label for="inputTimeFormat"><?php echo $L['time-format'] ?></label>
			<input type="text" class="form-control" id="inputTimeFormat" name="time_format" value="<?php echo htmlspecialchars( $Lang['time_format'] ) ?>">
			<small id="inputTimeFormatHelp" class="form-text text-muted"><?php echo $L['current-format'] ?>: <strong><?php echo date( $Lang['time_format'], time() ) ?></strong></small>
		</div>

		<div class="form-group">
			<label for="textDirection"><?php echo $L['text-direction'] ?></label>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="textDirection" id="textDirectionLtr" value="ltr" <?php echo ( $Lang['direction'] == 'ltr' ) ? 'checked' : '' ?>>
				<label class="form-check-label" for="textDirectionLtr">
				<?php echo $L['left-to-right'] ?>
				</label>
			</div>
			<div class="form-check">
				<input class="form-check-input" type="radio" name="textDirection" id="textDirectionRtl" value="rtl" <?php echo ( $Lang['direction'] == 'rtl' ) ? 'checked' : '' ?>>
				<label class="form-check-label" for="textDirectionRtl">
				<?php echo $L['right-to-left'] ?>
				</label>
			</div>
			<small id="textDirectionHelp" class="form-text text-muted"><?php echo $L['text-direction-tip'] ?></small>
		</div>

	</div>
	</div>
	
	<div class="form-row">
    <div class="form-group col-md-6">
	<h4><?php echo $L['site-information'] ?></h4>

		<div class="form-group">
			<label for="inputSiteName"><?php echo $L['site-title'] ?></label>
			<input type="text" class="form-control" id="inputSiteName" name="site_name" value="<?php echo htmlspecialchars( $Lang['site_name'] ) ?>">
			<small id="inputSiteNameHelp" class="form-text text-muted"><?php echo $L['use-this-field-to-name-your-site'] ?></strong></small>
		</div>

		<div class="form-group">
			<label for="inputSiteSlogan"><?php echo $L['site-slogan'] ?></label>
			<input type="text" class="form-control" id="inputSiteSlogan" name="site_slogan" value="<?php echo htmlspecialchars( $Lang['site_slogan'] ) ?>">
			<small id="inputSiteSloganHelp" class="form-text text-muted"><?php echo $L['use-this-field-to-add-a-catchy-phrase'] ?></strong></small>
		</div>

		<div class="form-group">
			<label for="inputSiteDescr"><?php echo $L['site-description'] ?></label>
			<textarea class="form-control" id="inputSiteDescr" name="site_description" rows="3"><?php echo htmlspecialchars( $Lang['site_description'] ) ?></textarea>
			<small id="inputSiteDescrHelp" class="form-text text-muted"><?php echo $L['you-can-add-a-site-description'] ?></strong></small>
		</div>
		
		<hr />
		
		<div class="form-group">
			<label for="inputCommentSystem"><?php echo __( 'comment-systems' ) ?></label>
			<select class="form-control" id="inputCommentSystem" name="comment_sys">
			<?php foreach ( $externalCommentsArray as $key => $row ) : ?>
				<option value="<?php echo $key ?>" <?php echo ( ( $extSys == $key ) ? 'selected' : '' ) ?>><?php echo $row['title'] ?></option>
			<?php endforeach ?>
			</select>
			<small id="inputCommentSystemHelp" class="form-text text-muted"><?php echo $L['comment-systems-tip'] ?></strong></small>
		</div>
		
		<?php 
		foreach ( $externalCommentsArray as $key => $row ) : 
			if ( $key == 'none' )
				continue;
		?>
		<div id="<?php echo $key ?>" class="form-group<?php echo ( ( $extSys == $key ) ? '' : ' d-none' ) ?>">
			<label for="<?php echo $key ?>"><?php echo $row['label'] ?></label>
			<input type="text" class="form-control" id="<?php echo $key ?>" name="ext[<?php echo $key ?>]" value="<?php echo ( ( $extSys == $key ) ? $extSysName : '' ) ?>">
			<small id="<?php echo $key ?>Help" class="form-text text-muted"><?php echo $row['tip'] ?></strong></small>
		</div>
		<?php endforeach ?>
		<hr />
		<h4><?php echo $L['not-found-page-details'] ?></h4>
			
		<div class="form-group">
			<label for="inputNotFoundTitle"><?php echo $L['not-found-title'] ?></label>
			<input type="text" class="form-control" id="inputNotFoundTitle" name="notfound[not_found_title]" value="<?php echo ( !empty( $NotFound['not_found_title'] ) ? htmlspecialchars( $NotFound['not_found_title'] ) : '' ) ?>">
		</div>
		
		<div class="form-group">
			<label for="inputNotFoundMessage"><?php echo $L['not-found-message'] ?></label>
			<textarea class="form-control" id="inputNotFoundMessage" name="notfound[not_found_message]" rows="3"><?php echo ( !empty( $NotFound['not_found_message'] ) ? htmlspecialchars( $NotFound['not_found_message'] ) : '' ) ?></textarea>
		</div>
	
		<hr />
	
		<div class="form-check">
			<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
			<label class="form-check-label" for="deleteCheckBox">
				<?php echo $L['delete'] ?>
			</label>
			<small id="deleteCheckBoxHelp" class="form-text text-muted"><?php echo $L['delete-language-tip'] ?></small>
		</div>

		<input type="hidden" name="_token" value="<?php echo generate_token( 'editLang' . $Lang['id_lang'] ) ?>">
		<input type="hidden" name="is_default" value="<?php echo ( $Lang['is_default'] ? 'true' : 'false') ?>">
		
		<div class="align-middle">
			<div class="float-left mt-1">
				<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
				<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
			</div>
		</div>
	</div>
	</div>
	</form>
	
	<?php endif ?>
</div>
</div>
</div>
</div>