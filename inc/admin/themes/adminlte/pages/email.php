<?php 
	$counts = AdminEmailsCounts();
?>
<div class="row">
	<div class="col-md-3">
		<a href="<?php echo $Admin->GetUrl( 'emails' ) ?>" class="btn btn-primary btn-block mb-3"><?php echo __( 'back-to-inbox' ) ?></a>
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><?php echo __( 'folders' ) ?></h3>
			</div>
			
			<div class="card-body p-0">
				<ul class="nav nav-pills flex-column">
					<li class="nav-item active">
						<a href="<?php echo $Admin->GetUrl( 'emails' ) ?>" class="nav-link">
							<i class="fas fa-inbox"></i> <?php echo __( 'inbox' ) ?>
							<?php if ( !empty( $counts ) && ( $counts['inbox'] > 0 ) ) : ?>
							<span class="badge bg-primary float-right"><?php echo $counts['inbox'] ?></span>
						<?php endif ?>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo $Admin->GetUrl( 'emails/sent' ) ?>" class="nav-link">
							<i class="far fa-envelope"></i> <?php echo __( 'sent' ) ?>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo $Admin->GetUrl( 'emails/draft' ) ?>" class="nav-link">
							<i class="far fa-file-alt"></i> <?php echo __( 'drafts' ) ?>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo $Admin->GetUrl( 'emails/junk' ) ?>" class="nav-link">
							<i class="fas fa-filter"></i> <?php echo __( 'junk' ) ?>
							<?php if ( !empty( $counts ) && ( $counts['junk'] > 0 ) ) : ?>
							<span class="badge bg-warning float-right"><?php echo $counts['junk'] ?></span>
							<?php endif ?>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo $Admin->GetUrl( 'emails/deleted' ) ?>" class="nav-link">
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
				<h3 class="card-title"><?php echo __( 'read-mail' ) ?></h3><!--
				<div class="card-tools">
					<a href="#" class="btn btn-tool" title="Previous"><i class="fas fa-chevron-left"></i></a>
					<a href="#" class="btn btn-tool" title="Next"><i class="fas fa-chevron-right"></i></a>
				</div>-->
			</div>

			<div class="card-body p-0">
				<div class="mailbox-read-info">
					<h5><?php echo $Email['subject'] ?></h5>
					<h6>From: <?php echo ( !empty( $Email['name'] ) ? $Email['name'] . ' &lt;' . $Email['email'] . '&gt;' : $Email['email'] ) ?>
					<span class="mailbox-read-time float-right"><?php echo $Email['time'] ?></span></h6>
				</div>

				<?php if ( !empty( $Email['replied'] ) ) : ?>
				<div class="mailbox-controls with-border text-center">
					<?php echo sprintf( __( 'you-replied-to-this-email-on' ), $Email['replied'] ) ?>
				</div>
				<?php endif ?>
				
				<div class="mailbox-read-message">
					<?php echo $Email['post'] ?>
				</div>
			</div>

			<div class="card-footer">
				<div class="float-right">
					<a href="<?php echo $Admin->GetUrl( 'reply-mail' . PS . 'id' . PS . $Email['id'] ) ?>"><button type="button" class="btn btn-default" id="buttonReply"><i class="fas fa-reply"></i> <?php echo __( 'reply' ) ?></button></a>
					<a href="<?php echo $Admin->GetUrl( 'forward-mail' . PS . 'id' . PS . $Email['id'] ) ?>"><button type="button" class="btn btn-default" id="buttonForward"><i class="fas fa-share"></i> <?php echo __( 'forward' ) ?></button></a>
				</div>
				<a class="btn btn-default" href="<?php echo $Admin->GetUrl( 'delete-mail' . PS . 'id' . PS . $Email['id'] ) ?>" id="deleteButton" role="button" onclick="return <?php echo ( ( $Email['status'] == 'deleted' ) ? 'confirm_alert();' : 'confirm_alert2(this);' ) ?>"><i class="far fa-trash-alt"></i> <?php echo __( 'delete' ) ?></a>
			</div>
		</div>
	</div>
</div>