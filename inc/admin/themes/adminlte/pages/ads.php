<?php
	require ( ARRAYS_ROOT . 'generic-arrays.php')
?><div class="row mb-5">
  <div class="col-lg-4">
    <div class="card mb-4 mb-lg-0">
      <div class="card-body">
        <div class="mb-4">
		<form id="adForm" method="post" action="<?php echo $Admin->GetUrl( 'create-ad' ) ?>" role="form">
          <label class="form-label" for="adName"><?php echo $L['name'] ?></label>
          <input class="form-control" id="adName" name="adName" type="text" required>
          <div class="form-text"><?php echo $L['category-name-tip'] ?></div>
        </div>
        <div class="mb-4">
          <label class="form-label" for="adType"><?php echo $L['type'] ?></label>
          <select class="form-select" id="adType" name="adType">
			<?php foreach( $adsTypes as $a => $b ) : ?>
				<option value="<?php echo $b['name'] ?>"><?php echo $b['title'] ?></option>
			<?php endforeach ?>
          </select>
          <div class="form-text"><?php echo $L['ads-type-tip'] ?></div>
        </div>
		<div class="mb-4">
          <label class="form-label" for="adPosition"><?php echo $L['placement'] ?></label>
          <select class="form-select" id="adPosition" name="adPosition">
			<?php foreach( $adsPosition as $a => $b ) : ?>
				<option value="<?php echo $b['name'] ?>"><?php echo $b['title'] ?></option>
			<?php endforeach ?>
          </select>
          <div class="form-text"><?php echo $L['placement-ads-tip'] ?></div>
        </div>
        <div class="mb-4">
          <label class="form-label" for="adCode"><?php echo $L['ad-code'] ?></label>
          <textarea class="form-control" id="adCode" name="adCode"></textarea>
          <div class="form-text"><?php echo $L['ad-code-tip'] ?></div>
        </div>
        <button class="btn btn-primary mb-4"><?php echo $L['create-ad'] ?></button>
		<input type="hidden" name="_token" value="<?php echo generate_token( 'add_ad' ) ?>">
		</form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card card-table">
      <div class="preload-wrapper">
	  <?php		
		if ( empty( $ads ) ) : ?>
			<div class="alert alert-warning" role="alert">
				<?php echo $L['nothing-found'] ?>
			</div>
		<?php else : ?>
		<form id="adsBulkForm" method="post" action="<?php echo $Admin->GetUrl( 'ads-bulk' ) ?>" role="form">
        <table class="table table-hover mb-0" id="categoryDatatable">
          <thead>
            <tr>
              <th style="width: 20px;"> </th>
              <th class="text-center"><?php echo $L['name'] ?></th>
              <th class="text-center"><?php echo $L['type'] ?></th>
              <th class="text-center"><?php echo $L['placement'] ?></th>
			  <th class="text-center"><?php echo $L['added'] ?></th>
            </tr>
          </thead>
          <tbody>
		  <?php foreach ( $ads as $ad ) : ?>
            <tr>
              <td class="text-center"><span class="form-check"><input class="form-check-input" name="del[]" value="<?php echo $ad['id'] ?>" type="checkbox"></span></td>
              <td class="text-center"><a href="<?php echo $Admin->GetUrl( 'edit-ad' . PS . 'id' . PS . $ad['id'] ) ?>" class="text-decoration-none text-reset fw-bolder"><?php echo $ad['title'] ?></a></td>
              <td class="text-center"><?php echo __( $ad['type'] ) ?></td>
              <td class="text-center"><?php echo __( $ad['ad_pos'] ) ?></td>
			  <td class="text-center"><?php echo postDate( $ad['added_time'] ) ?></td>
            </tr>
		<?php endforeach ?>
          </tbody>
        </table>
        <span class="me-2" id="categoryBulkAction">
          <select class="form-select form-select-sm d-inline w-auto mb-1 mb-lg-0" name="categoryBulkAction">
            <option value="0"><?php echo $L['bulk-actions'] ?></option>
            <option value="delete"><?php echo $L['delete'] ?></option>
          </select>
          <button class="btn btn-sm btn-outline-primary align-top mb-1 mb-lg-0"><?php echo $L['save'] ?></button>
        </span>
		</form>
	<?php endif ?>
      </div>
    </div>
  </div>
</div>