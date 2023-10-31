<?php include ( ARRAYS_ROOT . 'generic-arrays.php') ?>
<div class="row">
  <div class="col-12">
  <div class="card">
  <div class="card-body">
	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
	<div class="form-row">
    <div class="form-group col-md-6">
		<h4><?php echo $L['redirection-settings'] ?></h4>
			
		<div class="form-group">
			<label for="inputTitle"><?php echo $L['title'] ?></label>
			<input type="text" class="form-control" name="redir[title]" id="inputTitle" value="<?php echo htmlspecialchars( $Redir['title'] ) ?>">
			<small id="titleHelp" class="form-text text-muted"><?php echo $L['the-title-how-it-appears'] ?></small>
		</div>

		<div class="form-group">
			<label for="inputSourceUrl"><?php echo $L['source-url'] ?></label>
			<input type="text" class="form-control" id="inputSourceUrl" name="redir[source-url]" value="<?php echo htmlspecialchars( $Redir['uri'] ) ?>">
			<small id="titleHelp" class="form-text text-muted"><?php echo $L['source-url-tip'] ?></small>
		</div>

		<div class="form-group">
			<label for="inputTarget"><?php echo $L['target-url'] ?></label>
			<input type="text" class="form-control" id="inputTarget" name="redir[target-url]" value="<?php echo htmlspecialchars( $Redir['target'] ) ?>">
			<small id="titleHelp" class="form-text text-muted"><?php echo $L['target-url-tip'] ?></small>
		</div>

		<div class="form-group">
			<label for="inputWhenMatched"><?php echo $L['when-matched'] ?></label>
			<select  name="redir[when-matched]" class="form-control selectpicker" id="slcCountry" >
			<?php foreach ( $redirMatchedOptions as $key => $option ): ?>
				<option value="<?php echo $key ?>" <?php echo ( ( $Redir['when_matched'] == $key ) ? 'selected' : '' ) ?>><?php echo $option['title'] ?></option>
			<?php endforeach ?>
			</select>
		</div>
		
		<div class="form-group">
			<label for="inputHttp"><?php echo $L['add-http-code'] ?></label>
			<select  name="redir[add-http-code]" class="form-control selectpicker" id="slcCountry" >
			<?php foreach ( $redirHttpOptions as $key => $option ): ?>
				<option value="<?php echo $key ?>" <?php echo ( ( $Redir['http_code'] == $key ) ? 'selected' : '' ) ?>><?php echo $option['title'] ?></option>
			<?php endforeach ?>
			</select>
		</div>
		
		<div class="form-check">
			<input class="form-check-input" type="checkbox" value="1" name="redir[exclude-from-logs]" <?php echo ( $Redir['exclude_logs'] ? 'checked' : '' ) ?>>
			<label class="form-check-label" for="excludeRedir">
				<?php echo $L['exclude-from-logs'] ?>
			</label>
		</div>

		<div class="form-check">
			<input class="form-check-input" type="checkbox" value="1" name="redir[disable]" <?php echo ( $Redir['disable_redir'] ? 'checked' : '' ) ?>>
			<label class="form-check-label" for="disableRedir">
				<?php echo $L['disable-this-redirection'] ?>
			</label>
			<small id="titleHelp" class="form-text text-muted"><?php echo $L['disable-redirection-tip'] ?></small>
		</div>
	
	</div>
	</div>
	
	<div class="form-row">
    <div class="form-group col-md-6">
		<hr />
		<div class="form-check">
			<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
			<label class="form-check-label" for="deleteCheckBox">
				<?php echo $L['delete'] ?>
			</label>
		</div>

		<input type="hidden" name="_token" value="<?php echo generate_token( 'editRedir' . $Redir['id'] ) ?>">
		
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