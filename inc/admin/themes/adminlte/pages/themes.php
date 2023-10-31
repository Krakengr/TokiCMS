<div class="page-header d-flex justify-content-between align-items-right"><a class="btn btn-primary text-uppercase" href="<?php echo $Admin->GetUrl( 'add-themes' ) ?>"> <i class="fa fa-plus me-2"> </i><?php echo $L['add-new'] ?></a></div>
<div class="row">
  <div class="col-12">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['themes'] ?>
			</div>
				<div class="card-body">
					<div class="table-responsive">
			<form class="tab-content" id="form" method="post" action="" autocomplete="off">
				<table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
					<thead class="thead-light">
						<tr>
							<th><?php echo $L['name'] ?></th>
							<th><?php echo $L['description'] ?></th>
							<th><?php echo $L['author'] ?></th>
							<th><?php echo $L['default'] ?></th>
							<th><?php echo $L['version'] ?></th>
							<th><?php echo $L['tools'] ?></th>
						</tr>
					</thead>
					<tbody>
				<?php if ( !empty( $Themes ) ) :
					foreach( $Themes as $key => $theme ) : 
					?>
						<tr <?php echo ( ( $Admin->Settings()::Get()['theme'] == $key ) ? 'class="table-secondary"' : '' ) ?>>
							<td  class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-theme' . PS . $key ) ?>"><?php echo $theme['title'] ?></a></td>
							<td  class="dt-body"><?php echo $theme['description'] ?></td>
							<td  class="dt-body-center"><a href="<?php echo ( !empty( $theme['data']['website'] ) ? $theme['data']['website'] : '#' ) ?>"><?php echo $theme['data']['author'] ?></a></td>
							<td  class="dt-body-center"><input type="radio"<?php echo ( ( $Admin->Settings()::Get()['theme'] != $key ) ? ' title="' . $L['select-as-default-theme'] . '"' : '' ) ?> name="default_theme" value="<?php echo $key ?>" <?php echo ( ( $Admin->Settings()::Get()['theme'] == $key ) ? 'checked' : '' ) ?> /></td>
							<td><?php echo $theme['data']['version'] ?></td>
							<td><a href="<?php echo $Admin->GetUrl( 'edit-theme' . PS . $key ) ?>" role="button" class="openPopup" title="<?php echo $L['quick-edit'] ?>" id="themeInfo"><i class="bi bi-clipboard"></i></a><?php if ( $Admin->Settings()::Get()['theme'] != $key ) : ?> <a href="<?php echo $Admin->GetUrl( 'delete-theme' . PS . $key ) ?>" id="deleteTheme" title="<?php echo $L['delete'] ?>" class="action-icon" role="button" onclick="return confirm_alert()"><i class="bi bi-trash"></i></a><?php endif ?></td>
						</tr>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
				</div>
			</div>
		</div>
		
	<input type="hidden" name="_token" value="<?php echo generate_token( 'themes' ) ?>">
	
	<div class="align-middle">
		<div class="float-left mt-1">
			<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
		</div>
	</div>
	</form>
</div>