<?php
	require ( ARRAYS_ROOT . 'generic-arrays.php');
	$widgets = GetWidgets( $Admin->GetSite(), $Admin->GetLang(), false );
	
	$builInLeft = $builInRight = array();
	
	$buildInWidgetsTotal = count( $builtInWidgets );
	$buildInWidgetsNum = ( ( $buildInWidgetsTotal > 0 ) ? ceil( ( $buildInWidgetsTotal / 2 ) ) : 0 );
	
	if ( $buildInWidgetsTotal > 10 )
	{
		$builInLeft = array_slice( $builtInWidgets, 0, $buildInWidgetsNum );
		$builInRight = array_slice( $builtInWidgets, $buildInWidgetsNum, $buildInWidgetsTotal );
	}
	else
	{
		$builInLeft = $builtInWidgets;
	}

	$pos = $Admin->ThemePosition();

	if ( empty( $pos ) )
	{
		$pos = array( 'primary' => array( 'name' => __( 'primary' ) ) );
	}
?>
<div class="row mb-5">
	
	<div class="col-md-7">
		<div class="card card-default">
			<div class="card-header">
				<h3 class="card-title">
					<?php echo __( 'add-new-widget' ) ?>
				</h3>
			</div>
			
			<div class="card-body">
				<div class="row">
				<?php if ( !empty( $builInLeft ) ) : ?>
					<div class="col-sm-6">
					<?php foreach ( $builInLeft as $w => $widget ) : ?>
					<div class="card" data-id="<?php echo $w ?>">
						<div class="card-header border-transparent">
							<h4 class="card-title"><?php echo $widget['title'] ?></h4>
							<div class="card-tools">
					
								<div class="btn-group">
									<button type="button" class="btn btn-tool" data-toggle="dropdown">
										<i class="fas fa-plus"></i>
									</button>
									<div class="dropdown-menu dropdown-menu-right" role="menu">
									<?php foreach( $pos as $k => $w ) : ?>
										<a class="dropdown-item dropdownPosButton" href="javascript: void(0);" data-id="<?php echo $k ?>" data-widget="<?php echo $widget['name'] ?>"><?php echo __( $k ) ?></a>
									<?php endforeach ?>
									</div>
								</div>
							</div>
						  </div>
					</div>
					<?php endforeach ?>
					<?php /*
						<form id="wgForm" method="post" action="<?php echo $Admin->GetUrl( 'add-widget' ) ?>" role="form">
							<div class="mb-4">
								<label class="form-label" for="widgetName"><?php echo $L['name'] ?></label>
								<input class="form-control" id="widgetName" name="widgetName" type="text" required>
								<div class="form-text"><?php echo $L['widget-name-tip'] ?></div>
							</div>
							<div class="mb-4">
								<label class="form-label" for="widgetType"><?php echo $L['widget-type'] ?></label>
								<select class="form-select" id="widgetType" name="widgetType">
								<?php foreach( $widgetTypes as $w => $t ) : ?>
									<option value="<?php echo $t['name'] ?>"><?php echo $t['title'] ?></option>
								<?php endforeach ?>
								</select>
								<div class="form-text"><?php echo $L['widget-type-tip'] ?></div>
							</div>
				
							<div class="mb-4">
								<label class="form-label" for="widgetThemePos"><?php echo $L['theme-position'] ?></label>
								<?php if ( empty( $pos ) ) : ?>
									<p class="font-weight-bold text-muted text-sm">
										<?php echo $L['your-theme-does-not-natively-support-widgets'] ?>
									</p>
								<?php else : ?>
								<select class="form-select" id="widgetThemePos" name="widgetThemePos">
								<?php
								foreach( $pos as $k => $w ) : ?>
									<option value="<?php echo $k ?>"><?php echo $w['name'] ?></option>
								<?php endforeach ?>
								</select>
								<?php endif ?>
							</div>

							<div class="mb-4">
								<label class="form-label" for="widgetCode"><?php echo $L['widget-code-text'] ?></label>
								<textarea class="form-control" id="widgetCode" name="widgetCode"></textarea>
								<div class="form-text"><?php echo $L['widget-code-tip'] ?></div>
							</div>
				
							<button class="btn btn-primary mb-4"><?php echo $L['add-new-widget'] ?></button>
							<input type="hidden" name="_token" value="<?php echo generate_token( 'add_widget' ) ?>">
						</form>*/?>
					</div>
				<?php endif ?>
				<?php if ( !empty( $builInRight ) ) : ?>
					<div class="col-sm-6">
						<?php foreach ( $builInRight as $w => $widget ) : ?>
						<div class="card" data-id="<?php echo $w ?>">
							<div class="card-header border-transparent">
								<h4 class="card-title"><?php echo $widget['title'] ?></h4>
								<div class="card-tools">
						
									<div class="btn-group">
										<button type="button" class="btn btn-tool" data-toggle="dropdown">
											<i class="fas fa-plus"></i>
										</button>
										<div class="dropdown-menu dropdown-menu-right" role="menu">
										<?php foreach( $pos as $k => $w ) : ?>
											<a class="dropdown-item dropdownPosButton" href="javascript: void(0);" data-id="<?php echo $k ?>" data-widget="<?php echo $widget['name'] ?>"><?php echo __( $k ) ?></a>
										<?php endforeach ?>
										</div>
									</div>
								</div>
							  </div>
						</div>
					<?php endforeach ?>
					</div>
					<?php endif ?>
					
			
			
				</div>
			</div>
		</div>
		
		<div class="card card-default">
				<div class="card-header">
						<h3 class="card-title">
						<?php echo __( 'inactive-widgets' ) ?>
					</h3>
				</div>

				<div class="card-body">
					<section data-id="inactive" class="connectedSortable">
						<?php 
						$row = ( isset( $widgets['inactive'] ) ? $widgets['inactive'] : null );
						
						$theme = $Admin->ActiveTheme();
						
						if ( !empty( $row ) ) :
							foreach( $row as $widget ) :
								$name = ( ( $widget['theme'] != $theme ) ? ' [' . sprintf( __( 'enabled-on-theme-s' ), ucfirst( $widget['theme'] ) ) . ']' : '' );

								$cardArgs = array(
										'body-class' 	=> 'p-0',
										'header' 	 	=> $widget['title'] . $name,
										'card-class' 	=> 'collapsed-card',
										'header-class'  => 'border-transparent',
										'ids'  			=> 'id="sort" data-id="' . $widget['id'] . '"',

										'tools'			=> '
										<div class="btn-group">
											<button type="button" class="btn btn-tool" data-toggle="dropdown">
												<i class="fas fa-edit"></i>
											</button>
											<div class="dropdown-menu dropdown-menu-right" role="menu">
												<a href="' . $Admin->GetUrl( 'edit-widget' . PS . 'id' . PS . $widget['id'] ) . '" class="dropdown-item">' . __( 'edit' ) . '</a>
												<a href="' . $Admin->GetUrl( 'delete-widget' . PS . 'id' . PS . $widget['id'] ) . '" class="dropdown-item" onclick="return confirm(\'' . __( 'are-you-sure-you-want-to-delete-this-widget' ) . '\');">' . __( 'delete' ) . '</a>
											</div>
										</div>'
								);
								
								BootstrapCard( $cardArgs );
								?>
							<?php endforeach ?>
						<?php endif ?>
					</section>
				</div>
			</div>
			
		
	</div>
		
	<div class="col-md-5">
	<?php /*
	<div class="col-md-12">
			<div class="card card-default">
				<div class="card-header">
						<h3 class="card-title">
						<?php echo __( 'inactive-widgets' ) ?>
					</h3>
				</div>

				<div class="card-body">
					<section data-id="inactive" class="connectedSortable">
						<?php 
						$row = ( isset( $widgets['inactive'] ) ? $widgets['inactive'] : null );
						
						$theme = $Admin->ActiveTheme();
						
						if ( !empty( $row ) ) :
							foreach( $row as $widget ) :
								$name = ( ( $widget['theme'] != $theme ) ? ' [' . sprintf( __( 'enabled-on-theme-s' ), ucfirst( $widget['theme'] ) ) . ']' : '' );

								$cardArgs = array(
										'body-class' 	=> 'p-0',
										'header' 	 	=> $widget['title'] . $name,
										'card-class' 	=> 'collapsed-card',
										'header-class'  => 'border-transparent',
										'ids'  			=> 'id="sort" data-id="' . $widget['id'] . '"',

										'tools'			=> '
										<div class="btn-group">
											<button type="button" class="btn btn-tool" data-toggle="dropdown">
												<i class="fas fa-edit"></i>
											</button>
											<div class="dropdown-menu dropdown-menu-right" role="menu">
												<a href="' . $Admin->GetUrl( 'edit-widget' . PS . 'id' . PS . $widget['id'] ) . '" class="dropdown-item">' . __( 'edit' ) . '</a>
												<a href="' . $Admin->GetUrl( 'delete-widget' . PS . 'id' . PS . $widget['id'] ) . '" class="dropdown-item" onclick="return confirm(\'' . __( 'are-you-sure-you-want-to-delete-this-widget' ) . '\');">' . __( 'delete' ) . '</a>
											</div>
										</div>'
								);
								
								BootstrapCard( $cardArgs );
								?>
							<?php endforeach ?>
						<?php endif ?>
					</section>
				</div>
			</div>
		</div>*/?>
		
	<?php if ( empty( $widgets ) ) : ?>
		<div class="alert alert-warning" role="alert">
			<?php echo $L['no-widget-found'] ?>
		</div>
			
	<?php endif ?>
	
	<?php
	/*
	if ( !empty( $pos ) )
	{
		//$pos = ( isset( ThemeValue( 'widget-position' )['0'] ) ? ThemeValue( 'widget-position' )['0'] : ThemeValue( 'widget-position' ) );
	}
	else
	{
		$pos = array( 'primary' => array( 'name' => __( 'primary' ) ) );
	}
	*/
	$widgets = GetWidgets( $Admin->GetSite(), $Admin->GetLang(), false );

	foreach( $pos as $k => $w ) : ?>

		<div class="card h-300">
			<div class="card-header">
				<h3 class="card-title"><?php echo __( $k ) ?></h3>
			</div>
			<div class="card-body">
				<section data-id="<?php echo $k ?>" id="pos-<?php echo $k ?>" class="connectedSortable">
				
				<?php if ( !empty( $widgets ) && isset( $widgets[$k] ) && !empty( $widgets[$k] ) ) :
					$data = $widgets[$k];
					foreach( $data as $widget ) :
						$cardArgs = array(
									'body-class' 	=> 'p-0',
									'header' 	 	=> $widget['title'],
									'card-class' 	=> 'collapsed-card',
									'header-class'  => 'border-transparent',
									'ids'  			=> 'id="sort" data-id="' . $widget['id'] . '"',

									'tools'			=> '
									<div class="btn-group">
										<button type="button" class="btn btn-tool" data-toggle="dropdown">
											<i class="fas fa-edit"></i>
										</button>
										<div class="dropdown-menu dropdown-menu-right" role="menu">
											<a href="' . $Admin->GetUrl( 'edit-widget' . PS . 'id' . PS . $widget['id'] ) . '" class="dropdown-item">' . __( 'edit' ) . '</a>
											<a href="' . $Admin->GetUrl( 'delete-widget' . PS . 'id' . PS . $widget['id'] ) . '" class="dropdown-item" onclick="return confirm(\'' . __( 'are-you-sure-you-want-to-delete-this-widget' ) . '\');">' . __( 'delete' ) . '</a>
										</div>
									</div>'
						);
								
						BootstrapCard( $cardArgs );
								
						/*
                        <div data-id="<?php echo $widget['id'] ?>" class="card collapsed-card">
                            <div class="card-header bg-light">
								<h3 class="card-title">
									<?php echo $widget['title'] ?>
								</h3>
								<div class="card-tools">
									<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
										<i class="fas fa-plus"></i>
									</button>
									<button type="button" id="close" data-id="<?php echo $widget['id'] ?>" class="btn btn-tool">
										<i class="fas fa-times"></i>
									</button>
								</div>
                            </div>
                            <div class="card-body">
                                ggg
                            </div>
                        </div>*/ ?>
						<?php endforeach ?>
					<?php endif ?>
				</section>
                </div>
            </div>
		<?php endforeach ?>
	</div>
</div>