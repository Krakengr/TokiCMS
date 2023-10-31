<?php if ( ( $Admin->IsDefaultSite() && DEBUG_MODE ) || ( !$Admin->IsDefaultSite() && $Admin->Settings()::IsTrue( 'enable_debug_mode' ) ) ) : ?>
<div class="alert alert-danger" role="alert">
	<?php echo $L['debug-mode-warning'] ?>
</div>
<?php endif ?>

<?php if ( $Admin->MultiBlog() && ( $Admin->GetBlog() > 0 ) ) : ?>
<div class="alert alert-info" role="alert">
	<?php echo sprintf( $L['you-are-now-browsing-blog'], $Admin->BlogName() ) ?>
</div>
<?php endif ?>

<?php if ( $Admin->MultiLang() && !$Admin->IsDefaultLang() ) : ?>
<div class="alert alert-info" role="alert">
	<?php echo sprintf( $L['you-are-now-browsing-lang'], $Admin->LangName() ) ?>
</div>
<?php endif ?>

<?php if ( MULTISITE && !$Admin->IsDefaultSite() ) : ?>
<div class="alert alert-info" role="alert">
	<?php echo sprintf( $L['you-are-now-browsing-site'], $Admin->SiteName() ) ?>
</div>
<?php endif ?>

<div class="row">
	<section data-id="left" class="col-lg-7 connectedSortable">
		<?php AdminDashboard( 'left' ) ?>
	</section>
	
	<section data-id="right" class="col-lg-5 connectedSortable">
		<?php AdminDashboard( 'right' ) ?>
	</section>
	
	<div class="col-md-3">
		<button type="button" id="manage-widgets" data-toggle="modal" data-target="#manageWidgets" class="btn btn-block btn-info float-left"><?php echo __( 'manage-widgets' ) ?></button>
	</div>
</div>