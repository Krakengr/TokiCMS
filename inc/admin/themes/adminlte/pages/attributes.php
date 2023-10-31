<div class="row mb-5">
  <div class="col-lg-4">
    <div class="card mb-4 mb-lg-0">
      <div class="card-body">
		<?php
			$groups = GetAdminAttributeGroups();
			$atts = AdminGetAttributes( $Admin->GetSite(), $Admin->GetLang(), $Admin->GetBlog() );
		?>
		<form id="typeForm" method="post" action="<?php echo $Admin->GetUrl( 'add-attribute' ) ?>" role="form">
			<div class="form-group">
				<label class="form-label" for="attName"><?php echo $L['name'] ?></label>
				<input class="form-control" id="attName" name="name" type="text" required <?php echo ( empty( $groups ) ? 'disabled' : '' ) ?>>
				<div class="form-text"><?php echo $L['category-name-tip'] ?></div>
			</div>
			
			<div class="form-group">
				<label for="inputGroup"><?php echo $L['attribute-group'] ?></label>
				<select name="group" class="form-control" id="inputGroup">
				<?php if ( !empty( $groups ) ) :
					foreach( $groups as $group ) : ?>
						<option value="<?php echo $group['id'] ?>"><?php echo $group['name'] ?></option>
					<?php endforeach ?>
				<?php endif ?>
				</select>
			</div>
		
			<div class="form-group">
			  <label class="form-label" for="order"><?php echo $L['sort-order'] ?></label>
			  <input class="form-control" value="<?php echo ( count( $atts ) + 1 ) ?>" type="number" name="order" step="any" min="1" max="99" <?php echo ( empty( $groups ) ? 'disabled' : '' ) ?>>
			</div>
			
			<?php if ( empty( $groups ) ) : ?>
			<div class="alert alert-warning" role="alert">
				<?php echo sprintf( $L['no-attribute-groups-tip'], $Admin->GetUrl( 'attribute-groups' ) ) ?>
			</div>
			<?php endif ?>
			<button class="btn btn-primary mb-4" <?php echo ( empty( $groups ) ? 'disabled' : '' ) ?>><?php echo $L['add-new'] ?></button>
			<input type="hidden" name="_token" value="<?php echo generate_token( 'add_attribute' ) ?>">
		</form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card card-table">
      <div class="preload-wrapper">
		<form id="customTypesBulkForm" method="post" action="<?php echo $Admin->GetUrl( 'edit-post-attributes' ) ?>" role="form">
        <table class="table table-hover mb-0" id="customTypesDatatable">
          <thead>
            <tr>
              <th style="width: 20px;"> </th>
              <th><?php echo $L['name'] ?></th>
			  <th><?php echo $L['attribute-group'] ?></th>
			  <th><?php echo $L['custom-post-type'] ?></th>
			  <th><?php echo $L['category'] ?></th>
			  <?php if ( $Admin->MultiLang() ) : ?>
				<th><?php echo $L['lang'] ?></th>
			  <?php endif ?>
			  <?php if ( $Admin->MultiBlog() ) : ?>
				<th><?php echo $L['blog'] ?></th>
			  <?php endif ?>
            </tr>
          </thead>
          <tbody>
		  <?php 
			if ( !empty( $atts ) ) :
			foreach ( $atts as $att ) : ?>
            <tr>
				<td class="text-center"><span class="form-check"><input class="form-check-input" name="del[]" value="<?php echo $att['id'] ?>" type="checkbox"></span></td>
				<td><a href="<?php echo $Admin->GetUrl( 'edit-post-attribute' . PS . 'id' . PS . $att['id'] ) ?>"><?php echo stripslashes( $att['name'] ) ?></a></td>
				<td><?php echo stripslashes( $att['gn'] ) ?></td>
				<td><?php echo stripslashes( $att['t'] ) ?></td>
				<td><?php echo stripslashes( $att['cat'] ) ?></td>
				<?php if ( $Admin->MultiLang() ) : ?>
					<td><?php echo stripslashes( $att['lt'] ) ?></td>
				<?php endif ?>
				<?php if ( $Admin->MultiBlog() ) : ?>
					<td><?php echo stripslashes( $att['bname'] ) ?></td>
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
		  <input type="hidden" name="_token" value="<?php echo generate_token( 'post_attributes' ) ?>">
          <button class="btn btn-sm btn-outline-primary align-top mb-1 mb-lg-0"><?php echo $L['save'] ?></button>
        </span>
		</form>
      </div>
    </div>
	<div class="alert alert-info" role="alert">
		<?php echo $L['post-attributes-tip'] ?>
	</div>
  </div>
</div>