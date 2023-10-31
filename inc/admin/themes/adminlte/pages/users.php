<div class="page-header d-flex justify-content-between align-items-right">
  <div><a class="btn btn-primary text-uppercase" href="<?php echo $Admin->GetUrl( 'add-user' ) ?>"> <i class="fa fa-plus me-2"> </i><?php echo $L['add-new'] ?></a></div>
</div>
<div class="row">
  <div class="col-12">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['users'] ?>
			</div>
				<div class="card-body">
					<div class="table-responsive">
				<table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th><?php echo $L['username'] ?></th>
							<th><?php echo $L['nickname'] ?></th>
							<th><?php echo $L['email'] ?></th>
							<th><?php echo $L['role'] ?></th>
							<th><?php echo $L['status'] ?></th>
							<th><?php echo $L['posts'] ?></th>
							<th><?php echo $L['registered'] ?></th>
							<!--<th><?php //echo $L['permissions'] ?></th>-->
						</tr>
					</thead>
					<tbody>
				<?php $users = AdminUsers( $Admin->GetSite(), false );
				
					if ( !empty( $users ) ) :
						foreach( $users as $user ) : ?>
						<tr>
							<td  class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-user' . PS . 'id' . PS . $user['id_member'] ) ?>"><?php echo $user['user_name'] ?></a></td>
							<td  class="dt-body-center"><?php echo $user['real_name'] ?></td>
							<td  class="dt-body-center"><?php echo $user['email_address'] ?></td>
							<td  class="dt-body-center"><?php echo $user['gname'] ?></td>
							<td  class="dt-body-center"><?php echo ( $user['is_activated'] ? $L['enabled'] : $L['disabled'] ) ?></td>
							<td  class="dt-body-center"><?php echo $user['num_posts'] ?></td>
							<td  class="dt-body-center"><?php echo postDate ( $user['date_registered'] ) ?></td>
							<!--<td  class="dt-body-center"><?php //echo $permissions ?></td>-->
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