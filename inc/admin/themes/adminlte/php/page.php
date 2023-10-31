<?php
	$data = $Admin->FormData();
	$token = $data['token']; 
?>
<div class="row">
	<div class="col-12">
		<form class="tab-content" id="form" method="post" action="">
		<?php 
			$i = 0;
			$form = array();
		
		//Count the items
		foreach ( $data['form'] as $id => $row )
		{
			if ( isset( $row['hide'] ) && !empty( $row['hide'] ) )
				continue;
			
			$form[$id] = $row;
			
			$i++;
		}
		
		if ( $i > 1 ) : 
			$a = $b = 0;
		?>
		<div class="row">
			<div class="col-auto">
				<div class="card card-secondary card-tabs">
					<div class="card-header p-0 pt-1">
						<ul class="nav nav-tabs justify-content-left" id="menu-tabs" role="tablist">
						<?php 
						foreach ( $form as $id => $row ) : 
							$a++;
						?>
							<li class="nav-item">
								<a class="nav-link<?php echo ( ( $a == 1 ) ? ' active' : '' ) ?>" data-toggle="pill" href="#<?php echo $id ?>" role="tab" aria-controls="<?php echo $id ?>" aria-selected="<?php echo ( ( $a == 1 ) ? 'true' : 'false' ) ?>"><?php echo $row['title'] ?></a>
							</li>
						<?php endforeach ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
			<div class="tab-content" id="menu-tabs-tabContent">
				<?php 
				foreach ( $form as $id => $row ) : 
					$b++;
				?>
					<div class="tab-pane fade<?php echo ( ( $b == 1 ) ? ' show active' : '' ) ?>" id="<?php echo $id ?>" role="tabpanel" aria-labelledby="<?php echo $id ?>-tab">
						<?php include( ADMIN_THEME_PHP_ROOT . 'form.php' ) ?>
					</div>
				<?php endforeach ?>
			</div>
		
		<?php else :
			foreach ( $data['form'] as $id => $row ) :
					//Hide any element should not be visible
					if ( isset( $row['hide'] ) && !empty( $row['hide'] ) )
						continue;
			?>
				<!-- <?php echo $row['title'] ?> Group -->
				<?php include( ADMIN_THEME_PHP_ROOT . 'form.php' ) ?>
			<?php endforeach ?>
		<?php endif ?>
		<?php HiddenFormInput( array( 'name' => '_token', 'value' => generate_token( $token ) ) ) ?>

		<?php if ( !$Admin->DisableFormButtons() )
		{
			$html = '<div class="float-left mt-1">';
			
			$args = array(
					'id' => 'submitButton',
					'name' => 'save',
					'title' => __( 'save' ),
					'type' => 'submit',
					'class' => 'btn-primary btn-sm',
					'addAfterButton' => '<span class="spinner-border spinner-border-sm d-none" id="submitSpinner" role="status" aria-hidden="true"></span>'
			);
			
			$html .= Button( $args, false );
			
			$args = array(
					'id' => 'cancelButton',
					'title' => __( 'cancel' ),
					'href' => $Admin->Url(),
					'tag' => 'a',
					'type' => '',
					'role' => 'button',
					'class' => 'btn-secondary btn-sm'
			);
			
			$html .= Button( $args, false );
			{

				
				$html .= '</div>';
				
				$args = array(
					'footer' => $html
				);
		
				BootstrapCard( $args );
			}
		}
		/*
			<div class="card">
				<div class="card-footer">
					<div class="float-left mt-1">
						<button type="submit" class="btn btn-primary btn-sm" id="submitButton" name="save">
							<span class="spinner-border spinner-border-sm d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
							<?php echo $L['save'] ?>
						</button>
						<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
					</div>
				</div>
			</div>
			<?php endif */?>
		</form>
	</div>
</div>
