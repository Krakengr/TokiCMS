<div class="row mb-5">
  <div class="col-lg-4">
    <div class="card mb-4 mb-lg-0">
      <div class="card-body">
		<?php
			$atts = AdminGetStoreAttributes( $Admin->GetSite(), $Admin->GetLang() );
		?>
		<form id="typeForm" method="post" action="<?php echo $Admin->GetUrl( 'add-stores-attribute' ) ?>" role="form">
			<div class="form-group">
				<label class="form-label" for="attName"><?php echo $L['name'] ?></label>
				<input class="form-control" id="attName" name="name" type="text" required>
				<div class="form-text"><?php echo $L['category-name-tip'] ?></div>
			</div>

			<div class="form-group">
			  <label class="form-label" for="order"><?php echo $L['sort-order'] ?></label>
			  <input class="form-control" value="<?php echo ( count( $atts ) + 1 ) ?>" type="number" name="order" step="any" min="1" max="99">
			</div>

			<button class="btn btn-primary mb-4"><?php echo $L['add-new'] ?></button>
			<input type="hidden" name="_token" value="<?php echo generate_token( 'add_stores_attribute' ) ?>">
		</form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card card-table">
      <div class="preload-wrapper">
		<form id="customTypesBulkForm" method="post" action="<?php echo $Admin->GetUrl( 'edit-stores-attributes' ) ?>" role="form">
        <table class="table table-hover mb-0" id="customTypesDatatable">
          <thead>
            <tr>
              <th style="width: 20px;"> </th>
              <th><?php echo $L['name'] ?></th>
			  <?php if ( $Admin->MultiLang() ) : ?>
				<th><?php echo $L['lang'] ?></th>
			  <?php endif ?>
            </tr>
          </thead>
          <tbody>
		  <?php 
			if ( !empty( $atts ) ) :
			foreach ( $atts as $att ) : ?>
            <tr>
				<td class="text-center"><span class="form-check"><input class="form-check-input" name="del[]" value="<?php echo $att['id'] ?>" type="checkbox"></span></td>
				<td><a href="<?php echo $Admin->GetUrl( 'edit-stores-attribute' . PS . 'id' . PS . $att['id'] ) ?>"><?php echo stripslashes( $att['name'] ) ?></a></td>
				<?php if ( $Admin->MultiLang() ) : ?>
					<td><?php echo stripslashes( $att['lt'] ) ?></td>
				<?php endif ?>
            </tr>
		<?php endforeach ?>
		<?php endif ?>
          </tbody>
        </table>
		
        <span class="me-2" id="attributeBulkAction">
          <select class="form-select form-select-sm d-inline w-auto mb-1 mb-lg-0" name="attributeBulkAction">
            <option value="0"><?php echo $L['bulk-actions'] ?></option>
            <option value="delete"><?php echo $L['delete'] ?></option>
          </select>
		  <input type="hidden" name="_token" value="<?php echo generate_token( 'stores_attributes' ) ?>">
          <button class="btn btn-sm btn-outline-primary align-top mb-1 mb-lg-0"><?php echo $L['save'] ?></button>
        </span>
		</form>
      </div>
    </div>
	<div class="alert alert-info" role="alert">
		<?php echo $L['stores-attributes-tip'] ?>
	</div>
  </div>
</div>