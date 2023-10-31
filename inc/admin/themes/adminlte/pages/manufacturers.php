<div class="row">
  <div class="col-12">
	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['manufacturers'] ?> <a href="#" type="button" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo $L['manufacturers-tip'] ?>"><i class="bi bi-info-circle"></i></a>
			</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
							<thead class="thead-light">
								<tr>
									<th><?php echo $L['name'] ?></th>
									<th><?php echo $L['products'] ?></th>
								</tr>
							</thead>

							<tbody>
						<?php $manus = Manufacturers( $Admin->GetSite(), false );
						if ( !empty( $manus ) ) :
							foreach( $manus as $key => $man ) : ?>
								<tr>
									<td  class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-manufacturer' . PS . 'id' . PS . $key ) ?>"><?php echo $man['title'] ?></a></td>
									<td><?php echo $man['num_items'] ?></td>
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
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'add-manufacturer' ) ?>" role="button"><?php echo $L['add-new-manufacturer'] ?></a>
		</div>
	</div>
	</form>
</div>