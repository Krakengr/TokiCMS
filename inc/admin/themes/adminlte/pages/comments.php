<div class="container-fluid">
<?php 
	$counts = AdminCommentsCount(); 
?>
	<div class="page-header d-flex justify-content-between align-items-right">
		<ul class="list-inline text-sm">
			<li class="list-inline-item" id="navtab"><a class="text-gray-600 <?php echo ( ( Router::GetVariable( 'subAction' ) == null ) ? 'active' : 'text-reset' ) ?>" href="<?php echo $Admin->GetUrl( 'comments' ) ?>"><i class="fas fa-comment me-2"> </i> <?php echo $L['approved'] ?></a> (<?php echo $counts['comApproved'] ?>)</li>
			<li class="list-inline-item" id="navtab"><a class="text-gray-600 <?php echo ( ( Router::GetVariable( 'subAction' ) == 'pending' ) ? 'active' : 'text-reset' ) ?>" href="<?php echo $Admin->GetUrl( 'comments/pending' ) ?>"><i class="fas fa-clock me-2"> </i> <?php echo $L['pending'] ?></a> (<?php echo $counts['comPending'] ?>)</li>
			<li class="list-inline-item" id="navtab"><a class="text-gray-600 <?php echo ( ( Router::GetVariable( 'subAction' ) == 'spam' ) ? 'active' : 'text-reset' ) ?>" href="<?php echo $Admin->GetUrl( 'comments/spam' ) ?>"><i class="bi bi-bug"></i> <?php echo $L['spam'] ?></a> (<?php echo $counts['comSpam'] ?>)</li>
			<li class="list-inline-item" id="navtab"><a class="text-gray-600 <?php echo ( ( Router::GetVariable( 'subAction' ) == 'deleted' ) ? 'active' : 'text-reset' ) ?>" href="<?php echo $Admin->GetUrl( 'comments/deleted' ) ?>"><i class="fas fa-trash-alt me-2"> </i> <?php echo $L['deleted'] ?></a> (<?php echo $counts['comDeleted'] ?>)</li>
		</ul>
	</div>
	<div class="row">
			<div class="card col-12">
				<div class="card-header">
					<i class="fas fa-comments mr-1"></i>
					<?php echo $L['comments'] ?>
				</div>

				<div class="card-body">
					<div class="table-responsive">
					<form class="tab-content" id="form" method="post" action="" autocomplete="off">
						<table id="commentsTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
							<thead class="thead-light">
								<tr>
									<th><?php echo $L['author'] ?></th>
									<th><?php echo $L['comment'] ?></th>
									<th><?php echo $L['submitted-in'] ?></th>
									<th><?php echo $L['submitted-on'] ?></th>
									<?php if ( $ShowAll ) : ?>
									<th><?php echo $L['site'] ?></th>
									<?php endif ?>
									<th></th>
								</tr>
							</thead>
							<tbody>
							<?php if ( !empty( $Comments ) ) :
								foreach( $Comments as $co ) : ?>
									<tr>
										<td><?php echo $co['name'] ?></td>
										<td><?php echo $co['comment'] ?></td>
										<td><a href="<?php echo $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $co['postId'] ) ?>"><?php echo $co['postTitle'] ?></a></td>
										<td class="dt-body-center"><span class="d-none"><?php echo $co['timeRaw'] ?></span><a target="_blank" href="<?php echo $co['postUrl'] ?>"><?php echo $co['time'] ?></a></td>
										<?php if ( $ShowAll ) : ?>
											<th><?php echo $co['siteName'] ?></th>
										<?php endif ?>
										<td>
											<?php if ( $co['status'] == 'deleted' ) : ?>
											<a href="<?php echo $Admin->GetUrl( 'restore-comment' . PS . 'id' . PS . $co['id'] ) ?>" onclick="return confirm_alert2()"><i class="fa fa-file dropdown-icon text-secondary me-2"></i> <?php echo  __( 'restore' ) ?></a><br />
											
											<a id="deleteComment" onclick="return confirm_alert()" href="<?php echo $Admin->GetUrl( 'delete-comment' . PS . 'id' . PS . $co['id'] ) ?>"><i class="fa fa-trash dropdown-icon text-danger me-2"></i> <?php echo __( 'delete' ) ?></a>

											<?php else : ?>

												<?php if ( $co['status'] == 'pending' ) : ?>
													<a href="<?php echo $Admin->GetUrl( 'approve-comment' . PS . 'id' . PS . $co['id'] ) ?>"><i class="fa fa-desktop dropdown-item-icon text-secondary me-2"></i> <?php echo __( 'approve' ) ?></a><br />
											
												<?php elseif ( $co['status'] == 'approved' ) : ?>
													<a target="_blank" href="<?php echo $Admin->GetUrl( 'unapprove-comment' . PS . 'id' . PS . $co['id'] ) ?>"><i class="fa fa-desktop dropdown-item-icon text-secondary me-2"></i> <?php echo __( 'unapprove' ) ?></a><br />
												<?php endif ?>
									
												<a href="<?php echo $Admin->GetUrl( 'edit-comment' . PS . 'id' . PS . $co['id'] ) ?>"><i class="fa fa-edit dropdown-icon text-secondary me-2"></i> <?php echo  __( 'edit' ) ?></a><br />

												<a id="deleteComment" onclick="return confirm_alert2()" href="<?php echo $Admin->GetUrl( 'delete-comment' . PS . 'id' . PS . $co['id'] ) ?>"><i class="fa fa-trash dropdown-icon text-danger me-2"></i> <?php echo __( 'delete' ) ?></a>

											<?php endif ?>
										</td>
									</tr>
								<?php endforeach ?>
							<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
			</div>
		</div>
		<input type="hidden" name="_token" value="<?php echo generate_token( 'comments' ) ?>">
	</form>
</div>