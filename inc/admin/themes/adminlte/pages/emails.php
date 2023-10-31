<div class="row">
	<div class="col-md-3">
		<a href="compose.html" class="btn btn-primary btn-block mb-3"><?php echo __( 'compose' ) ?></a>
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><?php echo __( 'folders' ) ?></h3>
			</div>
			
			<div class="card-body p-0">
				<ul class="nav nav-pills flex-column">
					<li class="nav-item active">
						<a href="<?php echo ( ( $where != 'inbox' ) ? $Admin->GetUrl( 'emails' ) : '#' ) ?>" class="nav-link">
							<i class="fas fa-inbox"></i> <?php echo __( 'inbox' ) ?>
							<?php if ( !empty( $counts ) && ( $counts['inbox'] > 0 ) ) : ?>
							<span class="badge bg-primary float-right"><?php echo $counts['inbox'] ?></span>
						<?php endif ?>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo ( ( $where != 'sent' ) ? $Admin->GetUrl( 'emails/sent' ) : '#' ) ?>" class="nav-link">
							<i class="far fa-envelope"></i> <?php echo __( 'sent' ) ?>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo ( ( $where != 'draft' ) ? $Admin->GetUrl( 'emails/draft' ) : '#' ) ?>" class="nav-link">
							<i class="far fa-file-alt"></i> <?php echo __( 'drafts' ) ?>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo ( ( $where != 'junk' ) ? $Admin->GetUrl( 'emails/junk' ) : '#' ) ?>" class="nav-link">
							<i class="fas fa-filter"></i> <?php echo __( 'junk' ) ?>
							<?php if ( !empty( $counts ) && ( $counts['junk'] > 0 ) ) : ?>
							<span class="badge bg-warning float-right"><?php echo $counts['junk'] ?></span>
							<?php endif ?>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo ( ( $where != 'deleted' ) ? $Admin->GetUrl( 'emails/deleted' ) : '#' ) ?>" class="nav-link">
							<i class="far fa-trash-alt"></i> <?php echo __( 'trash' ) ?>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="col-md-9">
		<div class="card card-primary card-outline">
			<div class="card-header">
				<h3 class="card-title"><?php echo __( $where ) ?></h3>
				<div class="card-tools">
					<div class="input-group input-group-sm">
						<input type="text" class="form-control" placeholder="<?php echo htmlspecialchars( __( 'search-mail' ) ) ?>">
						<div class="input-group-append">
							<div class="btn btn-primary">
								<i class="fas fa-search"></i>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="card-body p-0">
				<div class="mailbox-controls">
					<button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i></button>
					<div class="btn-group">
						<button type="button" class="btn btn-default btn-sm">
							<i class="far fa-trash-alt"></i>
						</button>
						<!--
						<button type="button" class="btn btn-default btn-sm">
							<i class="fas fa-reply"></i>
						</button>
						<button type="button" class="btn btn-default btn-sm">
							<i class="fas fa-share"></i>
						</button>
						-->
					</div>
					
					<button type="button" class="btn btn-default btn-sm">
						<i title="<?php echo htmlspecialchars( __( 'mark-all-messages-as-read' ) ) ?>" class="fa fa-check"></i>
					</button>
					<!--
					<button type="button" class="btn btn-default btn-sm">
						<i class="fas fa-sync-alt"></i>
					</button>
					-->
					<?php if ( $emails['totalItems'] > 0 ) : ?>
					<div class="float-right">
						<?php echo count( $emails['emails'] ) ?>/<?php echo $emails['totalItems'] ?>
						<?php if ( Paginator::NumberOfPages() > 1 ) : ?>
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-sm">
								<i class="fas fa-chevron-left"></i>
							</button>
							<button type="button" class="btn btn-default btn-sm">
								<i class="fas fa-chevron-right"></i>
							</button>
						</div>
						<?php endif ?>
					</div>
					<?php endif ?>
				</div>
				
				<div class="table-responsive mailbox-messages">
					<table class="table table-hover table-striped">
						<tbody>
						<?php if ( !empty( $emails['emails'] ) ) :
							foreach ( $emails['emails'] as $email ) :
							
								$isReplied = false;
								
								$isDeleted = ( ( $email['status'] == 'deleted' ) ? true : false );
								
								$url = $Admin->GetUrl( 'email' . PS . 'id' . PS . $email['id'] );
								
								if ( ( $email['status'] == 'junk' ) || ( $email['status'] == 'draft' ) || $isDeleted )
								{
									if ( $isDeleted )
									{
										$title = __( 'restore' );
										
										$url = $Admin->GetUrl( 'restore-mail' . PS . 'id' . PS . $email['id'] );
									}
									
									else
									{
										$title = __( 'edit' );
									}
									
									if ( $email['status'] == 'draft' )
									{
										$url = $Admin->GetUrl( 'edit-draft-mail' . PS . 'id' . PS . $email['id'] );
									}
								}
								else
								{
									$title = $email['name'];
									
									$isReplied = $email['isReplied'];
								}
								
								$subject = ( !empty( $email['subject'] ) ? $email['subject'] : $email['postSum'] );
						?>
							<tr>
								<td>
									<div class="icheck-primary">
										<input type="checkbox" value="<?php echo $email['id'] ?>" id="check<?php echo $email['id'] ?>">
										<label for="check<?php echo $email['id'] ?>"></label>
									</div>
								</td>
								<?php if ( !$isDeleted ) : ?>
									<td class="mailbox-name"><a href="<?php echo $url ?>"><?php echo $title ?></a><?php echo ( $isReplied ? ' <i title="' . __( 'replied' ) . '" class="fas fa-reply fa-xs"></i>' : '' ) ?></td>
									<td class="mailbox-email"><?php echo $email['email'] ?></td>
									<td class="mailbox-subject"><?php echo ( !$email['isRead'] ? '<b>' . $subject . '</b>' : $subject ) ?></td>
									<td class="mailbox-date"><?php echo $email['timeNice'] ?></td>
								
								<?php else : ?>
									<td class="mailbox-email"><?php echo $email['email'] ?></td>
									<td class="mailbox-date"><?php echo $email['timeNice'] ?></td>
									<td class="mailbox-tools"><a href="<?php echo $Admin->GetUrl( 'restore-mail' . PS . 'id' . PS . $email['id'] ) ?>" title="<?php echo __( 'restore' ) ?>" id="restoreMail" onclick="return confirm_alert2(this);"> <i class="bi bi-arrow-counterclockwise"></i></a> <a href="<?php echo $Admin->GetUrl( 'delete-mail' . PS . 'id' . PS . $email['id'] ) ?>" id="deleteMail" title="<?php echo __( 'delete' ) ?>" class="action-icon" role="button" onclick="return confirm_alert()"><i class="bi bi-trash"></i></a></td>
								<?php endif ?>
							</tr>
						<?php endforeach ?>
						
						<?php else : ?>
							<tr>
								<td><?php echo __( 'nothing-found' ) ?></td>
							</tr>
						<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>

<div class="card-footer p-0">
<div class="mailbox-controls">

<button type="button" class="btn btn-default btn-sm checkbox-toggle">
<i class="far fa-square"></i>
</button>
<div class="btn-group">
<button type="button" class="btn btn-default btn-sm">
<i class="far fa-trash-alt"></i>
</button>
<button type="button" class="btn btn-default btn-sm">
<i class="fas fa-reply"></i>
</button>
<button type="button" class="btn btn-default btn-sm">
<i class="fas fa-share"></i>
</button>
</div>

<button type="button" class="btn btn-default btn-sm">
<i class="fas fa-sync-alt"></i>
</button>
<div class="float-right">
1-50/200
<div class="btn-group">
<button type="button" class="btn btn-default btn-sm">
<i class="fas fa-chevron-left"></i>
</button>
<button type="button" class="btn btn-default btn-sm">
<i class="fas fa-chevron-right"></i>
</button>
</div>

</div>

</div>
</div>
</div>

</div>

</div>