<div class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0"><?php echo $Admin->PageTitle() ?></h1>
			</div>
			
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
				<?php if ( !is_null ( $Admin->CurrentAction() ) && ( $Admin->CurrentAction() != 'dashboard' ) ) : ?>
					<li class="breadcrumb-item"><a href="<?php echo $Admin->Url() ?>"><?php echo __( 'dashboard' ) ?></a></li>
					<li class="breadcrumb-item active"><?php echo $Admin->PageTitle() ?></li>
				<?php endif ?>
				</ol>
			</div>
		</div>
	</div>
</div>