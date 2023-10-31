<div class="row mb-5">
  <div class="col-lg-4">
    <div class="card mb-4 mb-lg-0">
      <div class="card-body">
        <div class="mb-4">
		<form id="typeForm" method="post" action="<?php echo $Admin->GetUrl( 'add-custom-type' ) ?>" role="form">
          <label class="form-label" for="typeName"><?php echo $L['name'] ?></label>
          <input class="form-control" id="typeName" name="typeName" type="text" required>
          <div class="form-text"><?php echo $L['category-name-tip'] ?></div>
        </div>
		<div class="mb-4">
          <label class="form-label" for="typeSlug"><?php echo $L['slug'] ?></label>
          <input class="form-control" id="typeSlug" name="typeSlug" type="text">
          <div class="form-text"><?php echo $L['category-slug-tip'] ?></div>
        </div>
        <button class="btn btn-primary mb-4"><?php echo $L['add-new'] ?></button>
		<input type="hidden" name="_token" value="<?php echo generate_token( 'add_custom_type' ) ?>">
		</form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card card-table">
      <div class="preload-wrapper">
	  <?php if ( empty( $PostTypes ) ) : ?>
			<div class="alert alert-warning" role="alert">
				<?php echo $L['nothing-found'] ?>
			</div>
		<?php else : ?>
		<form id="customTypesBulkForm" method="post" action="<?php echo $Admin->GetUrl( 'edit-custom-post-types' ) ?>" role="form">
        <table class="table table-hover mb-0" id="customTypesDatatable">
          <thead>
            <tr>
              <th style="width: 20px;"> </th>
              <th><?php echo $L['name'] ?></th>
			  <th><?php echo $L['slug'] ?></th>
              <th class="text-center"><?php echo $L['count'] ?></th>
			  <th class="text-center"><?php echo $L['order'] ?></th>
            </tr>
          </thead>
          <tbody>
		  <?php foreach ( $PostTypes as $type ) : ?>
            <tr>
              <td class="text-center"><span class="form-check"><input class="form-check-input" name="del[]" value="<?php echo $type['id'] ?>" type="checkbox"></span></td>
              <td><a href="<?php echo $Admin->GetUrl( 'edit-custom-post-type' . PS . 'id' . PS . $type['id'] ) ?>" class="text-decoration-none text-reset fw-bolder"><?php echo stripslashes( $type['title'] ) ?></a></td>
			  <td><?php echo $type['sef'] ?></td>
              <td class="text-center"><?php echo $type['num'] ?></td>
			  <td class="text-center"><input type="number" name="sort_order[<?php echo $type['id'] ?>]" value="<?php echo $type['order'] ?>" placeholder="<?php echo __( 'sort-order' ) ?>" id="input-sort-order"  min="0" max="100" /></td>
            </tr>
		<?php endforeach ?>
          </tbody>
        </table>
        <span class="me-2" id="customTypesBulkAction">
          <select class="form-select form-select-sm d-inline w-auto mb-1 mb-lg-0" name="customTypesBulkAction">
            <option value="0"><?php echo $L['bulk-actions'] ?></option>
			<option value="update"><?php echo $L['update'] ?></option>
            <option value="delete"><?php echo $L['delete'] ?></option>
          </select>
		  <input type="hidden" name="_token" value="<?php echo generate_token( 'edit_custom_types' ) ?>">
          <button class="btn btn-sm btn-outline-primary align-top mb-1 mb-lg-0"><?php echo $L['save'] ?></button>
        </span>
		</form>
	<?php endif ?>
      </div>
	  
	
    </div>
	<div class="alert alert-info" role="alert">
		<?php echo $L['custom-post-types-tip'] ?>
	</div>
  </div>
</div>