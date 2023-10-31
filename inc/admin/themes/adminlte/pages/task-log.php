<div class="container-fluid">
	<div class="row">
			<div class="card col-12">
				<div class="card-body">
					<div class="table-responsive">
					<form class="tab-content" id="form" method="post" action="" autocomplete="off">
						<table id="logsTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
							<thead class="thead-light">
								<tr>
									<th><?php echo $L['title'] ?></th>
									<th><?php echo __( 'time-run' ) ?></th>
									<th><?php echo __( 'time-taken' ) ?></th>
									<?php if ( $ShowAll ) : ?>
									<th><?php echo $L['site'] ?></th>
									<?php endif ?>
								</tr>
							</thead>
							<tbody>
							<?php if ( !empty( $Logs ) ) :
								foreach( $Logs as $log ) : ?>
									<tr>
										<td class="text-center"><?php echo __( $log['task'] ) ?></td>
										<td class="text-center"><?php echo date( 'Y-m-d H:i:s', $log['time_run'] ) ?></td>
										<td class="text-center"><?php echo $log['time_taken'] ?> <?php echo __( 'seconds ' ) ?></td>
										<?php if ( $ShowAll ) : ?>
											<td class="text-center"><?php echo $log['sna'] ?></td>
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