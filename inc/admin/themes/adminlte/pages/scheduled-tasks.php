<div class="container-fluid">
	<div class="row">
			<div class="card col-12">
				<div class="card-body">
					<div class="table-responsive">
					<form class="tab-content" id="form" method="post" action="" autocomplete="off">
						<table id="tasksTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
							<thead class="thead-light">
								<tr>
									<th><?php echo $L['task-name'] ?></th>
									<th><?php echo $L['next-due'] ?></th>
									<th><?php echo $L['regularity'] ?></th>
									<th><?php echo $L['run-now'] ?></th>
									<th><?php echo $L['enabled'] ?></th>
								</tr>
							</thead>
							<tbody>
							<?php if ( !empty( $Tasks ) ) :
								foreach( $Tasks as $task ) : ?>
									<tr>
										<td><?php echo $task['name'] ?><br />
											<span class="text-gray text-sm text-muted"><?php echo $task['tip'] ?></span>
										</td>
										<td><?php echo $task['next'] ?></td>
										<td><?php echo $task['rep'] ?></td>
										<td class="dt-body-center"><input name="run[<?php echo $task['id'] ?>]" value="1" type="checkbox" /></td>
										<td class="dt-body-center"><input name="tasks[<?php echo $task['id'] ?>][enabled]" value="1" type="checkbox" <?php echo ( !$task['disabled'] ? 'checked' : '' ) ?> /></td>
									</tr>
								<?php endforeach ?>
							<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer clearfix">
				<button class="btn btn-sm btn-info align-top mb-1 mb-lg-0" name="save"><?php echo $L['save'] ?></button>
				<button type="submit" class="btn btn-sm btn-secondary" onclick="return false;" name="run"><?php echo $L['run-now'] ?></button>
			</div>

			</div>
		</div>
		
		<input type="hidden" name="_token" value="<?php echo generate_token( 'scheduled-tasks' ) ?>">
	</form>
</div>