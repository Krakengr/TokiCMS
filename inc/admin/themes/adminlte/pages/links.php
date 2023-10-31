<?php $links = GetAdminLinks( $Admin->GetSite(), false ) ?>
<div class="row">
  <div class="col-12">
	<div class="card mb-4">
		<div class="card-header">
			<?php echo $L['links'] ?>
		</div>
		<div class="card-body">
			<form id="catBulkForm" method="post" action="<?php echo $Admin->GetUrl( 'links-bulk-edit' ) ?>" role="form">
        <table class="table table-hover mb-0" id="categoryDatatable" style="table-layout: fixed; width: 100% !important;">
          <thead>
            <tr>
              <th style="width: 20px;"> </th>
              <th class="text-center"><?php echo $L['name'] ?></th>
              <th class="text-center"><?php echo $L['description'] ?></th>
			  <th class="text-center"><?php echo $L['status'] ?></th>
              <th class="text-center"><?php echo $L['url'] ?></th>
			  <th class="text-center"><?php echo $L['short-key'] ?></th>
			  <th class="text-center"><?php echo $L['added'] ?></th>
			  <th class="text-center"><?php echo $L['last-accessed'] ?></th>
              <th class="text-center"><?php echo $L['views'] ?></th>
            </tr>
          </thead>
          <tbody>
		  <?php 
			if ( !empty( $links ) ) :
				foreach ( $links as $link ) : ?>
            <tr>
              <td class="text-center"><span class="form-check"><input class="form-check-input" name="del[]" value="<?php echo $link['id'] ?>" type="checkbox"></span></td>
              <td class="text-center"><a href="<?php echo $Admin->GetUrl( 'edit-link' . PS . 'id' . PS . $link['id'] ) ?>" class="text-decoration-none text-reset fw-bolder"><?php echo $link['title'] ?></a></td>
              <td class="text-center"><?php echo $link['descr'] ?></td>
			  <td class="text-center"><?php echo $link['status'] ?></td>
              <td class="text-center"><?php echo $link['url'] ?></td>
			   <td class="text-center"><?php echo $link['short_link'] ?></td>
			   <td class="text-center"><?php echo postDate( $link['added_time'] ) ?></td>
			  <td class="text-center"><?php echo ( !empty( $link['last_time_viewed'] ) ? postDate( $link['last_time_viewed'] ) : '-') ?></td>
              <td class="text-center"><?php echo $link['num_views'] ?></td>
            </tr>
		<?php endforeach ?>
		<?php endif ?>
          </tbody>
        </table>
        <span class="me-2" id="categoryBulkAction">
          <select class="form-select form-select-sm d-inline w-auto mb-1 mb-lg-0" name="categoryBulkAction">
            <option value="0"><?php echo $L['bulk-actions'] ?></option>
			<!--<option value="update"><?php echo $L['update'] ?></option>-->
            <option value="delete"><?php echo $L['delete'] ?></option>
          </select>
		  <!--<input type="hidden" name="_token" value="<?php //echo generate_token( 'delete_bulk_categories' ) ?>">-->
          <button class="btn btn-sm btn-outline-primary align-top mb-1 mb-lg-0"><?php echo $L['save'] ?></button>
        </span>
		</form>
				</div>
				
				<div class="card-footer">
<button type="submit" class="btn btn-primary">Submit</button>
</div>
			</div>
		</div>
		
	<input type="hidden" name="_token" value="<?php echo generate_token( 'links' ) ?>">
	
	<div class="align-middle">
		<div class="float-left mt-1">
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'add-link' ) ?>" role="button"><?php echo $L['add-new-link'] ?></a>
		</div>
	</div>
	</form>
</div>