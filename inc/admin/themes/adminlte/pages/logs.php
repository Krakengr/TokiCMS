<div class="container-fluid">
	<div class="row">
			<div class="card col-12">
				<div class="card-header">
					<?php echo $L['system-log'] ?>
				</div>
				<div class="card-body">
					<div class="table-responsive">
					<form class="tab-content" id="form" method="post" action="" autocomplete="off">
						<table id="logsTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
							<thead class="thead-light">
								<tr>
									<th><?php echo $L['title'] ?></th>
									<th><?php echo $L['content'] ?></th>
									<th><?php echo $L['user'] ?></th>
									<th><?php echo $L['date'] ?></th>
									<th><?php echo $L['type'] ?></th>
									<th><?php echo $L['ip-address'] ?></th>
									<?php if ( $ShowAll ) : ?>
									<th><?php echo $L['site'] ?></th>
									<?php endif ?>
								</tr>
							</thead>
							<tbody>
							<?php if ( !empty( $Logs ) ) :
								foreach( $Logs as $log ) : ?>
									<tr>
										<td><?php echo $log['title'] ?></td>
										<td><a href="#" type="button" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo $log['descr'] ?>"><i class="bi bi-info-circle"></i></a></td>
										<td><?php echo ( !empty( $log['user_name'] ) ? $log['user_name'] : '' ) ?></td>
										<td><?php echo date( 'Y-m-d H:i:s', $log['added_time'] ) ?></td>
										<td><?php echo __( $log['type'] ) ?></td>
										<td><?php echo $log['ip'] ?></td>
										<?php if ( $ShowAll ) : ?>
											<th><?php echo $log['sna'] ?></th>
										<?php endif ?>
									</tr>
								<?php endforeach ?>
							<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
			</div>
		</div>
		
		<input type="hidden" name="_token" value="<?php echo generate_token( 'logs' ) ?>">
	</form>
</div>