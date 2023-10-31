<div class="row">
  <div class="col-12">
	<div class="card mb-4">
		<div class="card-header">
			<?php echo __( 'forms' ) ?>
		</div>
		<div class="card-body">
        <table class="table table-hover mb-0" id="categoryDatatable" style="table-layout: fixed; width: 100% !important;">
          <thead>
            <tr>
              <th class="text-center"><?php echo $L['name'] ?></th>
              <th class="text-center"><?php echo $L['type'] ?></th>
            </tr>
          </thead>
          <tbody>
		  <?php 
			if ( !empty( $forms ) ) :
				foreach ( $forms as $form ) : ?>
            <tr>
              <td class="text-center"><a href="<?php echo $Admin->GetUrl( 'edit-form' . PS . 'id' . PS . $form['id'] ) ?>" class="text-decoration-none text-reset fw-bolder"><?php echo $form['title'] ?></a></td>
              <td class="text-center"><?php //echo $form['descr'] ?></td>
            </tr>
		<?php endforeach ?>
		<?php endif ?>
          </tbody>
        </table>
				</div>
				
				<div class="card-footer"></div>
			</div>
		</div>
		
	<input type="hidden" name="_token" value="<?php echo generate_token( 'forms' ) ?>">
	
	<div class="align-middle">
		<div class="float-left mt-1">
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'add-form' ) ?>" role="button"><?php echo __( 'add-new-form' ) ?></a>
		</div>
	</div>
	</form>
</div>