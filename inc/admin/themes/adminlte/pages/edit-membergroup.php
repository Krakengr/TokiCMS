<?php
	require ( ARRAYS_ROOT . 'generic-arrays.php');
	$permissions = ( ( $Group['group_permissions'] != 'all' ) ? Json( $Group['group_permissions'] ) : null );
?><div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form class="tab-content" id="form" method="post" action="" autocomplete="off">
					<div class="form-row">
						<div class="form-group col-md-6">
							<h4><?php echo $L['edit-membergroup'] ?>: <?php echo $Group['group_name'] ?></h4>

							<div class="form-group">
								<label for="inputTitle"><?php echo $L['title'] ?></label>
								<input type="text" class="form-control" name="title" id="inputTitle" value="<?php echo htmlspecialchars( $Group['group_name'] ) ?>">
								<small id="titleHelp" class="form-text text-muted"><?php echo $L['add-title-tip'] ?></small>
							</div>

				<div class="form-group">
					<label for="inputDescription"><?php echo $L['description'] ?></label>
					<textarea class="form-control" id="inputDescription" name="description" rows="3"><?php echo htmlspecialchars( $Group['description'] ) ?></textarea>
				</div>
				
				<div class="form-group">
					<label><?php echo __( 'group-color' ) ?></label>
					<input type="text" name="color" id="cp" value="<?php echo $Group['group_color'] ?>" class="form-control">
					<small id="groupColorHelp" class="form-text text-muted"><?php echo __( 'group-color-tip' ) ?></small>
				</div>
		
				<?php if ( $Group['id_group'] != 1 ) : ?>
				<div class="form-group">
					<label for="inputMaxPersonalMessages"><?php echo $L['max-personal-messages'] ?></label>
					<input value="<?php echo $Group['max_messages'] ?>" type="number" class="form-control" name="max_messages" step="any" min="0" max="10000">
					<small id="inputMaxPersonalMessagesHelp" class="form-text text-muted"><?php echo $L['max-personal-messages-tip'] ?></small>
				</div>
				
				<div class="form-group">
						<label for="inputDescription"><?php echo $L['permissions'] ?></label>
						
						<?php foreach ( $groupPermissions as $id => $per ) : ?>
						<div class="custom-control custom-switch">
							<input class="custom-control-input" type="checkbox" name="permissions[<?php echo $per['name'] ?>]" id="Permission-<?php echo $id ?>" <?php echo ( ( !empty( $permissions ) && in_array( $per['name'], $permissions ) ) ? 'checked' : '' ) ?>>
							<label class="custom-control-label" for="Permission-<?php echo $id ?>"><?php echo $per['title'] ?></label> <?php if ( $per['tip'] ) : ?><a href="#" type="button" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo $per['tip'] ?>"><i class="bi bi-info-circle"></i></a><?php endif ?>
						</div>
						<?php endforeach ?>
				</div>
				<?php endif ?>
				
				<?php if ( $Group['group_type'] !== 'system' ) : ?>
				
				<div class="form-group">
					<label for="inputMinPosts"><?php echo $L['required-posts'] ?></label>
					<input value="<?php echo $Group['min_posts'] ?>" type="number" class="form-control" name="min_posts" step="any" min="-1" max="10000">
					<small id="inputMinPostsHelp" class="form-text text-muted"><?php echo $L['required-posts-tip'] ?></small>
				</div>
				
				<hr />
				
				<div class="row mb-3">
					<div class="col-lg-4">
						<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
						<label class="form-check-label" for="deleteCheckBox">
							<?php echo $L['delete'] ?>
						</label>
					</div>
					
					<div class="col-lg-7">
						<label class="form-label" for="moveGroupMembers">
							<?php echo $L['move-group-members'] ?>
						</label>
						<select class="form-select shadow-none" name="new_group" aria-label="<?php echo $L['move-group-members'] ?>">
						<option value="0">---</option>
						<?php $groups = AdminGroups( $Admin->GetSite(), false );
							if ( !empty( $groups ) ) :
								foreach( $groups as $_group ) :
								
								if ( $_group['id_group'] == $Group['id_group'] )
									continue;
						?>
							<option value="<?php echo $_group['id_group'] ?>"><?php echo $_group['group_name'] ?></option>
						<?php endforeach; endif; ?>
						</select>
					</div>
					
					<small id="deleteCheckBox" class="form-text text-muted"><?php echo $L['delete-membergroup-tip'] ?></small>
				</div>
				<?php endif ?>
				
				<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_group_' . $Group['id_group'] ) ?>">
				
				<div class="align-middle">
					<div class="float-left mt-1">
						<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
						<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'membergroups' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
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
    $("[data-toggle=tooltip]").tooltip();
});</script>