<?php
	require ( ARRAYS_ROOT . 'generic-arrays.php');
	$permissions = ( ( $Api['allow_data'] != 'all' ) ? Json( $Api['allow_data'] ) : null );
?><div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form class="tab-content" id="form" method="post" action="" autocomplete="off">
					<div class="form-row">
						<div class="form-group col-md-6">
							<h4><?php echo $L['edit-api-object'] ?>: <?php echo $Api['name'] ?></h4>
							
							<div class="form-group">
								<label for="url" class="col-sm-2 col-form-label"><?php echo __( 'url' ) ?></label>
								<input type="text" class="form-control" name="apiUrl" id="apiUrl" value="<?php echo sprintf( $L['api-url'], substr( $Admin->SiteUrl(), 0, -1 ) ) ?>" readonly />
							</div>
								
							<div class="form-group">
								<label for="apiToken"><?php echo $L['api-token'] ?></label>
								<input type="text" class="form-control" name="apiToken" id="apiToken" value="<?php echo $Api['token'] ?>" readonly />
							</div>

							<div class="form-group">
								<label for="apiName"><?php echo $L['title'] ?></label>
								<input type="text" class="form-control" name="apiName" id="apiName" value="<?php echo htmlspecialchars( $Api['name'] ) ?>">
								<small id="titleHelp" class="form-text text-muted"><?php echo $L['add-title-tip'] ?></small>
							</div>

							<div class="form-group">
								<label for="apiDescr"><?php echo $L['description'] ?></label>
								<textarea class="form-control" id="apiDescr" name="apiDescr" rows="3"><?php echo htmlspecialchars( $Api['descr'] ) ?></textarea>
							</div>
							
							<div class="form-group">
								<label for="amount-items"><?php echo __( 'amount-of-items' ) ?></label>
								<input type="number" step="1" min="1" max="1000" class="form-control" id="amount-items" value="<?php echo $Api['items_limit'] ?>" name="items">
								<small id="amountItemsTip" class="form-text text-muted"><?php echo __( 'amount-of-items-tip' ) ?></small>
							</div>
							
							<div class="form-group">
								<label for="api-requests"><?php echo __( 'limit-api-requests' ) ?></label>
								<input type="number" step="1" min="0" max="10000" class="form-control" id="api-requests" value="<?php echo $Api['api_limit'] ?>" name="limit">
								<small id="apiRequestsTip" class="form-text text-muted"><?php echo __( 'limit-api-requests-tip' ) ?></small>
							</div>
							
							<?php if ( $Api['is_primary'] !== 1 ) : ?>
							
							<div class="form-group">
								<label for="inputDescription"><?php echo $L['permissions'] ?></label>
								
								<?php foreach ( $apiPermissions as $id => $per ) : ?>
								<div class="custom-control custom-switch">
									<input class="custom-control-input" type="checkbox" name="permissions[<?php echo $per['name'] ?>]" id="Permission-<?php echo $id ?>" <?php echo ( ( !empty( $permissions ) && in_array( $per['name'], $permissions ) ) ? 'checked' : '' ) ?>>
									<label class="custom-control-label" for="Permission-<?php echo $id ?>"><?php echo $per['title'] ?></label> <?php if ( $per['tip'] ) : ?><a href="#" type="button" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo $per['tip'] ?>"><i class="bi bi-info-circle"></i></a><?php endif ?>
								</div>
								<?php endforeach ?>
							</div>

							<?php else : ?>
							<div class="form-group">
								<label for="inputDescription"><?php echo $L['permissions'] ?>: <?php echo $L['all'] ?></label>
							</div>
							<?php endif ?>
							
							<div class="form-group">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" value="1" name="disable" id="disableCheckBox" <?php echo ( $Api['disabled'] ? 'checked' : '' ) ?>>
									<label class="form-check-label" for="disableCheckBox"><?php echo __( 'disable' ) ?></label>
									<small id="disableCheckBoxTip" class="form-text text-muted"><?php echo __( 'disable-api-tip' ) ?></small>
								</div>
							</div>
							
							<?php if ( $Api['is_primary'] !== 1 ) : ?>
							
							<div class="form-group">
								<div class="form-check">
									<input type="checkbox" class="form-check-input" value="1" name="delete" id="deleteCheckBox">
									<label class="form-check-label" for="deleteCheckBox"><?php echo __( 'delete' ) ?></label>
								</div>
							</div>
							<?php endif ?>
							
							<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_api_' . $Api['id'] ) ?>">
		
							<div class="align-middle">
								<div class="float-left mt-1">
									<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
									<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'api' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
								</div>
							</div>
							
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function (){
    $("[data-bs-toggle=tooltip]").tooltip();
});</script>