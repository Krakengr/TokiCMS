<div class="page-header d-flex justify-content-between align-items-right">
  <div><a class="btn btn-primary text-uppercase" href="<?php echo $Admin->GetUrl( 'add-membergroup' ) ?>"> <i class="fa fa-plus me-2"> </i><?php echo $L['add-new'] ?></a></div>
</div>
<div class="row">
  <div class="col-12">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['membergroups'] ?>
			</div>
				<div class="card-body">
					<div class="table-responsive">
				<table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th><?php echo $L['name'] ?></th>
							<th><?php echo $L['description'] ?></th>
							<th><?php echo $L['permissions'] ?></th>
						</tr>
					</thead>
					<tbody>
				<?php $groups = AdminGroups( $Admin->GetSite(), false );
				if ( !empty( $groups ) ) :
					foreach( $groups as $group ) : 
						
						if ( $group['group_permissions'] === 'all' )
							$permissions = $group['group_permissions'];
						else
						{
							$arr = Json( $group['group_permissions'] );
								
							$permissions = '';
								
							if ( !empty( $arr ) )
							{
								foreach( $arr as $ar )
									$permissions .= __( $ar ) . ',';
							}
						}
				?>
						<tr>
							<td  class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-membergroup' . PS . 'id' . PS . $group['id_group'] ) ?>"><?php echo $group['group_name'] ?></a></td>
							<td  class="dt-body-center"><?php echo $group['description'] ?></td>
							<td  class="dt-body-center"><?php echo $permissions ?></td>
						</tr>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
				</div>
			</div>
		</div>
</div>