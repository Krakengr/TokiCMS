<div class="row mb-5">
  <div class="col-lg-4">
    <div class="card mb-4 mb-lg-0">
      <div class="card-body">
		<?php 
			$groups = GetAdminAttributeGroups();
			$types = GetAdminCustomTypes();
		?>
		<form id="typeForm" method="post" action="<?php echo $Admin->GetUrl( 'add-attribute-group' ) ?>" role="form">
			<div class="form-group">
				<label class="form-label" for="groupName"><?php echo $L['name'] ?></label>
				<input class="form-control" id="groupName" name="name" type="text" required />
				<div class="form-text"><?php echo $L['category-name-tip'] ?></div>
			</div>
			
		<?php if ( !empty( $types ) ) : ?>
			<div class="form-group">
				<label for="inputPostType"><?php echo $L['post-type'] ?></label>
				<select name="postType" class="form-control" id="inputPostType" >
				<option value="0">---</option>
				<?php foreach( $types as $type ) : ?>
					<option value="<?php echo $type['id'] ?>"><?php echo $type['title'] ?></option>
				<?php endforeach ?>
				</select>
				<div class="form-text"><?php echo $L['select-attribute-group-types-tip'] ?></div>
			</div>
		<?php endif ?>
		
			<div class="form-group">
			  <label class="form-label" for="order"><?php echo $L['sort-order'] ?></label>
			  <input class="form-control" value="<?php echo ( count( $groups ) + 1 ) ?>" type="number" name="order" step="any" min="1" max="99">
			</div>
			
			<button class="btn btn-primary mb-4"><?php echo $L['add-new'] ?></button>
			<input type="hidden" name="_token" value="<?php echo generate_token( 'add-attribute-group' ) ?>">
		</form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card card-table">
      <div class="preload-wrapper">
        <table class="table table-hover mb-0" id="groupSttributesDatatable">
          <thead>
            <tr>
              <th><?php echo $L['name'] ?></th>
			  <th><?php echo $L['sort-order'] ?></th>
            </tr>
          </thead>
          <tbody>
		  <?php if ( !empty( $groups ) ) :
				foreach ( $groups as $group ) : ?>
            <tr>
              <td><a href="<?php echo $Admin->GetUrl( 'edit-attribute-group' . PS . 'id' . PS . $group['id'] ) ?>"><?php echo stripslashes( $group['name'] ) ?></a></td>
			  <td><?php echo $group['group_order'] ?></td>
            </tr>
			<?php endforeach ?>
		<?php endif ?>
          </tbody>
        </table>
      </div>
	  
    </div>
	<div class="alert alert-info" role="alert">
		<?php echo $L['attribute-groups-tip'] ?>
	</div>
  </div>
</div>