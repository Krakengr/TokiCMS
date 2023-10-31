<div class="row">
  <div class="col-12">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['schemas'] ?>
			</div>
				<div class="card-body">
					<div class="table-responsive">
			<form class="tab-content" id="form" method="post" action="" autocomplete="off">
				<table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
					<thead class="thead-light">
						<tr>
							<th><?php echo $L['name'] ?></th>
							<th><?php echo $L['target-location'] ?></th>
							<th><?php echo $L['type'] ?></th>
							<th><?php echo $L['date'] ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
				<?php $schemas = Schemas();
				if ( !empty( $schemas ) ) :
					foreach( $schemas as $schema ) : 
						$data = ( !empty( $schema['data'] ) ? json_decode( $schema['data'], true ) : null );
				?>
						<tr>
							<td class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-schema' . PS . 'id' . PS . $schema['id'] ) ?>"><?php echo $schema['title'] ?></a></td>
							<td class="dt-body-center"><strong><?php echo __ ( 'enabled' ) ?></strong>: <?php echo ( ( isset( $data['enableOn'] ) && !empty( $data['enableOn']['0'] ) ) ? $data['enableOn']['0']['target'] : 'Null' ) ?>
							<?php echo ( ( isset( $data['exludeOn'] ) && !empty( $data['exludeOn']['0'] ) ) ? '<br /><strong>' . __ ( 'excluded' ) . '</strong>: ' . $data['exludeOn']['0']['target'] : '' ) ?></td>
							<td class="dt-body-center"><?php echo $schema['type'] ?></td>
							<td class="dt-body-center"><?php echo postDate( $schema['added_time'] );  ?></td>
							<td class="dt-body-center"></td>
						</tr>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
				</div>
			</div>
		</div>

	<div class="align-middle">
		<div class="float-left mt-1">
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'add-schema' ) ?>" role="button"><?php echo $L['add-schema'] ?></a>
		</div>
	</div>
	</form>
</div>