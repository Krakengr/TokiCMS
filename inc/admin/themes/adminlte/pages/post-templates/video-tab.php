<?php $xtraDataVideo = ( isset( $xtraData['video'] ) ? $xtraData['video'] : null ) ?>
<h5><?php echo $L['video-settings'] ?></h5>
<div class="mb-3">
	<div class="form-group">
		<label class="form-label" for="videoURL"><?php echo __( 'video-url' )?></label>
		<input type="text" id="videoURL" name="video[url]" class="form-control mb-4" placeholder="https://www.youtube.com/watch?v=xxx" value="<?php echo ( isset( $xtraDataVideo['videoUrl'] ) ? $xtraDataVideo['videoUrl'] : '' ) ?>">
		<small id="videoURL" class="form-text text-muted"><?php echo sprintf( $L['video-url-tip'], $Admin->GetUrl( 'media-embedder' ) ) ?></small>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label" for="videoEmbedCode"><?php echo __( 'video-embed-code' )?></label>
		<textarea class="form-control" rows="4" name="video[embed_code]" cols="50" id="videoEmbedCode"><?php echo ( isset( $xtraDataVideo['embedCode'] ) ? $xtraDataVideo['embedCode'] : '' ) ?></textarea>
		<small id="videoEmbedCode" class="form-text text-muted"><?php echo $L['video-embed-code-tip'] ?></small>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label" for="videoDuration"><?php echo __( 'video-duration' )?></label>
		<strong><?php echo __( 'minutes' ) ?>:</strong> <input type="number" id="videoDuration" name="video[duration_min]" value="<?php echo ( ( isset( $xtraDataVideo['duration'] ) && !empty( $xtraDataVideo['duration'] ) ) ? $xtraDataVideo['duration']['min'] : 0 ) ?>"> <strong><?php echo __( 'seconds' ) ?>:</strong> <input type="number" id="videoDuration" name="video[duration_sec]" value="<?php echo ( ( isset( $xtraDataVideo['duration'] ) && !empty( $xtraDataVideo['duration'] ) ) ? $xtraDataVideo['duration']['sec'] : 0 ) ?>">
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label" for="videoDimensions"><?php echo __( 'video-dimensions' )?></label>
		<strong><?php echo __( 'width' ) ?>:</strong> <input type="number" id="videoDimensions" name="video[width]" value="<?php echo ( isset( $xtraDataVideo['videoWidth'] ) ? $xtraDataVideo['videoWidth'] : 0 ) ?>"> <strong><?php echo __( 'height' ) ?>:</strong> <input type="number" id="videoDimensions" name="video[height]" value="<?php echo ( isset( $xtraDataVideo['videoHeight'] ) ? $xtraDataVideo['videoHeight'] : 0 ) ?>">
	</div>

	<div class="form-group form-check ">
		<input class="form-check-input" type="checkbox" value="1" id="familyFriendly" name="video[family_friendly]" <?php echo ( ( isset( $xtraDataVideo['familyFriendly'] ) && $xtraDataVideo['familyFriendly'] ) ? 'checked' : ( ( isset( $xtraDataVideo['familyFriendly'] ) && !$xtraDataVideo['familyFriendly'] ) ? '' : 'checked' ) ) ?>>
		<label class="form-check-label" for="familyFriendly">
			<?php echo $L['family-friendly'] ?>
		</label>
		<small id="familyFriendly" class="form-text text-muted"><?php echo $L['family-friendly-tip'] ?></small>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label" for="videoPlaylist"><?php echo __( 'video-playlist' )?></label>
		<?php $plays = Playlists() ?>
		<select name="video[playlist]" class="form-control" id="videoPlaylist">
			<option value="0">---</option>
		<?php if ( !empty( $plays ) ) :
			foreach( $plays as $play ) : ?>
				<option value="<?php echo $play['id'] ?>" <?php echo ( ( isset( $xtraDataVideo['playlistId'] ) &&  ( $xtraDataVideo['playlistId'] == $play['id'] ) ) ? 'selected' : '' ) ?>><?php echo $play['title'] ?></option>
			<?php endforeach ?>
		<?php endif ?>
		</select>
		<small id="videoPlaylist" class="form-text text-muted"><?php echo sprintf( $L['video-playlist-tip'], $Admin->GetUrl( 'video-playlists' ) ) ?></small>
	</div>
</div>