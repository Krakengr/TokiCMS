<div class="row">
	<div class="col-md-9 col-sm-9 col-md-push-3">
		<form action="" method="post" id="post" role="form" class="form-horizontal">
			<input type="hidden" id="comment_id" value="<?php echo $Comment['id'] ?>">
			<div class="card">
				<div class="card-body">
					<div class="tab-content" id="postTabContent">
						<div class="col-xl-12">

							<div class="mb-3">
								<div class="form-group">
									<label class="form-label" for="name"><?php echo $L['name'] ?></label>
									<input type="text" id="name" name="name" class="form-control mb-4" value="<?php echo $Comment['name'] ?>" />
								</div>
							</div>

							<div class="mb-3">
								<div class="form-group">
									<label class="form-label" for="email"><?php echo $L['email'] ?></label>
									<input type="email" id="email" name="email" class="form-control mb-4" value="<?php echo $Comment['email'] ?>" />
								</div>
							</div>

							<div class="mb-3">
								<div class="form-group">
									<label class="form-label" for="url"><?php echo $L['url'] ?></label>
									<input type="text" id="url" name="url" class="form-control mb-4" value="<?php echo $Comment['url'] ?>" />
								</div>
							</div>
							
							<?php if ( $canViewAttachments ) : ?>
							<!-- Media Button -->
							<div class="form-group">
								<div class="d-inline-block editor-action-item">
									<div class="btn-group">
										<div class="margin">
										<?php 
										$idModal = ( ( $Admin->Settings()::Get()['html_editor'] == 'editor-js' ) ? 'imageEditorJsModal' : 'imageEditorModal' ); ?>
											<a href="javascript: void(0);" data-toggle="modal" data-target="#addImage" id="<?php echo $idModal ?>" class="btn_gallery btn btn-outline-primary mb-4" data-id="<?php echo $Comment['id_post'] ?>" data-focus="false"> <i class="far fa-image"></i> <?php echo __( 'add-media' ) ?></a>										
										</div>
									</div>
								</div>
							</div>
							<?php endif ?>
							
							<!-- Content -->
							<div class="mb-3">
								<div class="form-group">
									<label for="commentEditor" class="form-label"><?php echo $L['content'] ?></label>
									<?php echo $Editor->Init( $Comment['comment'], '400px', 'mainEditor', true ) ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="col-md-3 col-sm-3 col-md-pull-9">
			<div class="flex-row">
				<div class="card card-sm shadow-sm mb-4">
					<div class="card-header">
						<h4 class="card-heading"><?php echo $L['publish'] ?></h4>
					</div>
					
					<div class="card-body text-gray-700">						
						<!-- Comment Status -->
						<div class="mb-3">
							<?php echo $L['status'] ?>: <strong><?php echo $L[$Comment['status']] ?> </strong><a class="ms-2 text-sm" data-toggle="collapse" href="#collapseStatus" role="button" aria-expanded="false" aria-controls="collapseStatus"><?php echo $L['edit'] ?></a>
							<div class="collapse" id="collapseStatus">
								<div class="py-2">
									<select class="form-select form-select-sm" name="status" aria-label="Post select">
										<option value="approved" <?php echo ( ( $Comment['status'] == 'approved' ) ? 'selected' : '' ) ?>><?php echo $L['approved'] ?></option>
										<option value="pending" <?php echo ( ( $Comment['status'] == 'pending' ) ? 'selected' : '' ) ?>><?php echo $L['pending-review'] ?></option>
										<option value="spam" <?php echo ( ( $Comment['status'] == 'spam' ) ? 'selected' : '' ) ?>><?php echo $L['spam'] ?></option>
									</select>
								</div>
							</div>
						</div>
						
						<!-- Comment Date -->
						<div class="mb-3">
						   <?php echo $L['submitted-on'] ?>: <strong><?php echo postDate( $Comment['added_time'], false ) ?></strong>
						</div>
						
						<!-- Comment Date -->
						<div class="mb-3">
						   <?php echo $L['submitted-in'] ?>: <strong><?php echo $Comment['tl'] ?></strong>
						</div>
					</div>
					
					<div class="card-footer">
						<button class="btn btn-sm btn-primary float-left" type="submit" id="updateButton" name="update"><?php echo $L['update'] ?></button>
							
						<button class="btn btn-sm btn-default float-right" style="color:red;" role="button" href="<?php echo $Admin->GetUrl( 'delete-comment' . PS . 'id' . PS . $Comment['id'] ) ?>" onclick="return confirm_alert2(this);" id="deleteButton"><?php echo $L['delete'] ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>