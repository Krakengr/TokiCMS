<div class="container-fluid">
    <div class="row">
		<?php if ( empty( $Themes ) ) : ?>
			<p><?php echo __( 'nothing-found' ) ?></p>
		<?php else :
			foreach ( $Themes['themes'] as $theme ) : ?>
		<div class="col-md-3">
			<div class="card">
				<?php if ( !empty( $theme['coverImage'] ) ) : ?>
				<img class="card-img-top img-fluid" src="<?php echo $theme['coverImage'] ?>" alt="<?php echo htmlspecialchars( $theme['title'] ) ?>">
				<?php endif ?>
				<div class="card-block">
					<h4 class="card-title"><?php echo $theme['title'] ?></h4>
					<p class="card-text"><?php echo $theme['description'] ?></p>
					<div class="card-body">
					<?php if ( isset( $theme['attributes']['theme-id'] ) && !empty( $theme['attributes']['theme-id'] ) ) : ?>
						<a href="javascript: void(0);" class="btn btn-primary card-link ms-2 text-sm"><?php echo __( 'install' ) ?></a>
						<a href="javascript: void(0);" data-bs-toggle="modal" data-bs-target="#newThemeDetails" id="newThemeDetails" class="btn btn-secondary card-link ms-2 text-sm" data-id="<?php echo $theme['attributes']['theme-id'] ?>" data-bs-focus="false"><?php echo __( 'preview' ) ?></a>
					<?php endif ?>
					</div>
				</div>
			</div>
        </div>
		<?php endforeach ?>
		
		<?php endif ?>
	</div>
</div>

<div class="modal fade newThemeDetails" id="newThemeDetails" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document" style="padding-right: 17px; display: block; width:100%;">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-success d-none success"></div>
				<div class="alert alert-danger d-none error"></div>
				<div id="modal-loader" style="text-align: center;">
					<img src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/loading.gif">
				</div>  
				<div id="post-detail"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo $L['cancel'] ?></button>
				<button id="installTheme" type="button" class="btn btn-primary disabled"><?php echo $L['install'] ?></button>
			</div>
		</div>
	</div>
</div>