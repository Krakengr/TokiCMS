<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form class="tab-content" id="form" method="post" action="" autocomplete="off">
							<h4><?php echo $L['edit-theme'] ?>: <?php echo $ThemeDt['title'] ?></h4>
							
							<?php if ( !empty( $ThemeDt['settings'] ) ) : ?>
							<p><?php echo __( 'notes' ) ?>: <?php echo $ThemeDt['notes'] ?></p>
							<?php endif ?>
							
							<?php if ( isset( $ThemeDt['data']['settings'] ) && !empty( $ThemeDt['data']['settings'] ) ) :
								$hasData = true;
								
								$Editor->addExtraValues = false;
								
								$i = 0;
								
								$themeData = ( ( isset( $ThemeDt['db'] ) && !empty( $ThemeDt['db'] ) ) ? $ThemeDt['db'] : null );

								$_L = $ThemeDt['options-lang'];

								$pages = GetAdminPages();
								$blogs = GetAdminBlogs( $Admin->GetSite(), $Admin->GetLang() );
								$ads   = GetAllAdminAds( $Admin->GetSite(), $Admin->GetLang() );
								$cats  = GetAllAdminCategories();
								
								foreach ( $ThemeDt['data']['settings'] as $_set => $set ) : 
									$showTip 	= true;
									$string 	= '';
									
									if ( $set['type'] == 'pagebuilder' ) : ?>
									<div class="form-group col-md-10">
									<?php $string .= AdminPageBuilder( ( ( $themeData && isset( $themeData[$_set] ) ) ? $themeData[$_set] : null ), false );?>
									</div>
									<?php endif ?>
							<div class="form-group col-md-6">
								<label for="<?php echo $_set ?>"><?php echo ( isset( $L[$_set] ) ? $L[$_set] : ( isset( $_L[$_set] ) ? $_L[$_set] : '' ) ) ?></label>
								<?php
								
								if ( $set['type'] == 'select' )
								{
									$multiSelect = ( ( isset( $set['multiselect'] ) && $set['multiselect'] ) ? true : false );
									$system = ( ( isset( $set['system'] ) && $set['system'] ) ? true : false );
									
									$values = ( isset( $set['values'] ) ? $set['values'] : null );
									
									$string .= '<select ' . ( $multiSelect ? 'multiple ' : '' ) . 'class="form-control" style="width: 100%;" id="' . $_set . '" name="' . $_set . ( $multiSelect ? '[]' : '' ) . '" aria-label="Select">';
									
									if ( $system )
									{
										if ( $set['data'] == 'categories' )
											$values = $cats;
									
										elseif ( $set['data'] == 'pages' )
											$values = $pages;
										
										elseif ( $set['data'] == 'ads' )
											$values = $ads;
										
										elseif ( $set['data'] == 'blogs' )
											$values = $blogs;
									}
							
									if ( !$multiSelect && isset( $set['first-null'] ) && !empty( $set['first-null'] ) )
										$string .= '<option value="">---</option>';
									
									if ( !empty( $values ) )
									{
										if ( !$system )
										{
											foreach( $values as $v )
											{
												$string .= '<option value="' . $v . '" ';

												$string .= ( ( !$multiSelect && $themeData && isset( $themeData[$_set] ) && ( $themeData[$_set] == $v ) ) ? 'selected' : '' );
												
												$string .= ( ( $multiSelect && $themeData && isset( $themeData[$_set] ) && is_array( $themeData[$_set] ) && in_array( $v, $themeData[$_set] ) ) ? 'selected' : '' );

												$string .= ( ( !$themeData && isset( $set['default-value'] ) && ( $v == $set['default-value'] ) ) ? 'selected' : '' );
												
												$string .= '>';

												$string .= ( isset( $_L[$v] ) ? $_L[$v] : __( $v ) );
												
												$string .= '</option>';
											}
										}
										
										else
										{
											foreach( $values as $v => $va )
											{
												$string .= '<option name="' . $_set . '" value="' . $v . '" ';
												
												$string .= ( ( !$multiSelect && $themeData && isset( $themeData[$_set] ) && ( $themeData[$_set] == $v ) ) ? 'selected' : '' );
										
												$string .= ( ( !$themeData && isset( $set['default-value'] ) && ( $v == $set['default-value'] ) ) ? 'selected' : '' );
												
												$string .= '>';

												$string .= $va['name'];
												
												$string .= '</option>';
											}
										}
									}
									
									$string .= '</select>';
								}
		
								if ( $set['type'] == 'num' )
								{
									$string .= '<input value="' . ( ( $themeData && isset( $themeData[$_set] ) ) ? $themeData[$_set] : $set['default-value'] ) . '" id="' . $_set . '" type="number" class="form-control" name="' . $_set . '" step="' . ( isset( $set['step'] ) ? $set['step'] : 'any' ) . '" ' . ( isset( $set['min'] ) ? 'min="' . $set['min'] . '"' : '' ) .  ( isset( $set['max'] ) ? 'max="' . $set['max'] . '"' : '' ) . '>';
								}

								if ( $set['type'] == 'text' )
									$string .= '<input type="text" id="' . $_set . '" name="' . $_set . '" class="form-control" value="' . ( ( $themeData && isset( $themeData[$_set] ) ) ? $themeData[$_set] : $set['default-value'] ) . '">';
								
								if ( $set['type'] == 'textarea' )
								{
									$i++;
									
									$Editor->name = $_set;

									$string .= $Editor->Init( ( ( $themeData && isset( $themeData[$_set] ) ) ? $themeData[$_set] : $set['default-value'] ), '300px', 'editor' . $i );
								}

								if ( $set['type'] == 'checkbox' )
								{
									$checked = ( ( $themeData && isset( $themeData[$_set] ) && ( $themeData[$_set] == 'true' ) ) ? true : false );

									$checked = ( ( !$themeData && isset( $set['default-value'] ) && ( $set['default-value'] == 'true' ) ) ? true : $checked );

									$args = array(
											'id' => $_set,
											'name' => $_set,
											'value' => 'true',
											'checked' => $checked,
											'tip' => ( isset( $L[$set['tip']] ) ? $L[$set['tip']] : ( isset( $_L[$set['tip']] ) ? $_L[$set['tip']] : null ) )
											
									);
									
									$string .= CheckBox( $args, false );
									
									$showTip = false;
								}
								
								echo $string;
								?>
								<?php if ( $showTip && isset( $set['tip'] ) && !empty( $set['tip'] ) ) : ?>
									<small id="<?php echo $_set ?>" class="form-text text-muted"><?php echo ( isset( $L[$set['tip']] ) ? $L[$set['tip']] : ( isset( $_L[$set['tip']] ) ? $_L[$set['tip']] : '' ) ) ?></small>
								<?php endif ?>
							</div>
							<?php endforeach ?>
							<input type="hidden" name="_token" value="<?php echo generate_token( 'edit-theme_' . Router::GetVariable( 'subAction' ) ) ?>">
							<?php else : 
								$hasData = false; ?>
							<div class="alert alert-warning" role="alert"><?php echo __( 'this-theme-has-no-configuration-settings' ) ?></div>
							<?php endif ?>

							<div class="align-middle">
								<div class="float-left mt-1">
									<?php if ( $hasData ) : ?>
									<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
									<?php endif ?>
									<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'themes' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
								</div>
							</div>
				</form>
			</div>
		</div>
	</div>
</div>