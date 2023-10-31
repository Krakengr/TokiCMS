<?php require ( ARRAYS_ROOT . 'generic-arrays.php') ?>
<div class="row mb-5">
  <div class="col-lg-4">
    <div class="card mb-4 mb-lg-0">
      <div class="card-body">
	  <form id="wgForm" method="post" action="<?php echo $Admin->GetUrl( 'add-api' ) ?>" role="form">
        <div class="mb-4">
          <label class="form-label" for="apiName"><?php echo $L['new-api-object'] ?></label>
          <input class="form-control" id="apiName" name="apiName" type="text" required>
          <div class="form-text"><?php echo $L['category-name-tip'] ?></div>
        </div>
		
		<div class="mb-4">
          <label class="form-label" for="apiDescr"><?php echo $L['description'] ?></label>
          <textarea class="form-control" id="apiDescr" name="apiDescr"></textarea>
        </div>
		
        <button class="btn btn-primary mb-4"><?php echo $L['add-api-object'] ?></button>
		<input type="hidden" name="_token" value="<?php echo generate_token( 'add_api' ) ?>">
		</form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card card-body">
      <h5 class="card-title"><?php echo $L['api-objects'] ?></h5>
	  <?php 
		$apis = GetApis( $Admin->GetSite(), false );
		$apisArray = array();
		
			if ( empty( $apis ) ) :
		?>
			<div class="alert alert-warning" role="alert">
				<?php echo $L['nothing-found'] ?>
			</div>
		<?php else : ?>

		<table class="table table-hover col-md-1 table-bordered table-sm" id="apiDatatable" style="table-layout: fixed; width: 100% !important;">
          <thead>
            <tr>
              <th class="text-center"><?php echo $L['name'] ?></th>
              <th class="text-center"><?php echo $L['description'] ?></th>
              <th class="text-center"><?php echo $L['token'] ?></th>
			  <th class="text-center"><?php echo $L['enabled'] ?></th>
              <th class="text-center"><?php echo $L['permissions'] ?></th>
            </tr>
          </thead>
          <tbody>
		  <?php foreach ( $apis as $api ) : 
				
				$_id = 'apiCheckBox-' . $api['id'];
				$apisArray[] = '#' . $_id;
		  ?>
            <tr>
              <td class="text-center"><a href="<?php echo $Admin->GetUrl( 'edit-api' . PS . 'id' . PS . $api['id'] ) ?>" class="text-decoration-none text-reset fw-bolder"><?php echo $api['name'] ?></a></td>
              <td class="text-center"><?php echo $api['descr'] ?></td>
              <td class="text-center"><?php echo $api['token'] ?></td>
			  <td class="text-center">
				<div class="form-check form-switch">
					<input class="form-check-input" type="checkbox" value="<?php echo $api['id'] ?>" name="enable[<?php echo $api['id'] ?>]" id="<?php echo $_id ?>" <?php echo ( !$api['disabled'] ? 'checked' : '' ) ?>>
						<label class="form-check-label" for="Api-<?php echo $api['id'] ?>"><?php echo $L['enabled'] ?></label>
				</div>
			</td>
              <td class="text-center"><?php echo $api['allow_data'] ?></td>
            </tr>
		<?php endforeach ?>
          </tbody>
        </table>
	<?php endif ?>
    </div>
  </div>
</div>