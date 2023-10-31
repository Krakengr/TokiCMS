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
						<a href="<?php echo $Admin->GetUrl( 'emails/drafts' ) ?>" class="nav-link">
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
				<h3 class="card-title"><?php echo __( 'reply-mail' ) ?></h3>
			</div>
			
			<form class="tab-content" id="form" method="post" action="" autocomplete="off">
				<div class="card-body">
					
					<div class="form-group">
						<label for="inputTo"><?php echo __( 'to' ) ?></label>
						<input id="inputTo" name="email" class="form-control" placeholder="<?php echo __( 'to' ) ?>:" value="<?php echo $Email['email'] ?>">
					</div>
					<div class="form-group">
						<label for="inputSubject"><?php echo __( 'subject' ) ?></label>
						<input id="inputSubject" name="subject" class="form-control" placeholder="<?php echo __( 'subject' ) ?>:" value="RE: <?php echo $Email['subject'] ?>">
					</div>
					<div class="form-group">
						<textarea id="compose-textarea" class="form-control" name="message" style="height: 300px"><?php echo '<br><br><br><hr>' . $Email['post'] ?></textarea>
					</div><!--
					<div class="form-group">
					<div class="btn btn-default btn-file">
					<i class="fas fa-paperclip"></i> Attachment
					<input type="file" name="attachment">
					</div>
					<p class="help-block">Max. 32MB</p>
					</div>-->
				</div>

				<div class="card-footer">
					<div class="float-right">
						<button type="submit" class="btn btn-default" name="draft"><i class="fas fa-pencil-alt"></i> <?php echo __( 'draft' ) ?></button>
						<button type="submit" class="btn btn-primary" name="send"><i class="far fa-envelope"></i> <?php echo __( 'send' ) ?></button>
					</div>
					<a class="btn btn-default" href="<?php echo $Admin->GetUrl( 'emails' ) ?>" id="resetButton" role="button" onclick="return confirm_alert2(this);"><i class="fas fa-times"></i> <?php echo __( 'discard' ) ?></a>
				</div>
				
				<input type="hidden" name="_token" value="<?php echo generate_token( 'reply_email_' . $Email['id'] ) ?>">
			</form>
		</div>
	</div>
</div>