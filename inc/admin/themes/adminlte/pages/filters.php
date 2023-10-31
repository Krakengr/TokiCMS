<div class="row">
  <div class="col-12">
	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-filter mr-1"></i>
				<?php echo $L['filters'] ?> <a href="#" type="button" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo $L['filters-tip'] ?>"><i class="bi bi-info-circle"></i></a>
			</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered" width="100%" cellspacing="0">
							<thead class="thead-light">
								<tr>
									<th><?php echo $L['filter-group'] ?></th>
									<th><?php echo $L['order'] ?></th>
									<th style="width: 10%;"><?php echo $L['actions'] ?></th>
								</tr>
							</thead>

							<tbody>
						<?php $filters = AdminFilters( $Admin->GetSite(), false, false );
						if ( !empty( $filters ) ) :
							foreach( $filters as $key => $filter ) : ?>
								<tr>
									<td  class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-filter' . PS . 'id' . PS . $key ) ?>"><?php echo $filter['name'] ?></a></td>
									<td><?php echo $filter['order'] ?></td>
									<td></td>
								</tr>
						<?php endforeach ?>
						<?php endif ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		
	<input type="hidden" name="_token" value="<?php echo generate_token( 'manufacturers' ) ?>">
	
	<div class="align-middle">
		<div class="float-left mt-1">
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'add-filter' ) ?>" role="button"><?php echo $L['add-new-filter'] ?></a>
		</div>
	</div>
	</form>
</div>