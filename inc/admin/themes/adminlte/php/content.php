<div class="container-fluid">
	<?php if ( $Admin->GetAdminMessage() ) : ?>
	<div class="alert alert-<?php echo $Admin->GetAdminMessage( true ) ?> alert-dismissible fade show" role="alert">
	<?php echo $Admin->GetAdminMessage() ?>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<?php endif ?>
	<?php if ( !empty( $Admin->GetAdminMessages() ) ) : 
			foreach ( $Admin->GetAdminMessages() as $mess ) :
	?>
	<div class="alert alert-<?php echo $mess['type'] ?> alert-dismissible fade show" role="alert">
	<?php echo $mess['message'] ?>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<?php endforeach ?>
	<?php endif ?>
	<?php
	if ( file_exists( $Admin->ThemeFile() ) )
	{
		include( $Admin->ThemeFile() );
	}
	?>
</div>