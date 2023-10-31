<div class="col-xl-<?php echo ( isset( $row['col'] ) ? $row['col'] : '12' ) ?>">
	<div class="card card-default">
		<div class="card-header">
			<?php echo $row['title'] ?>
		</div>
		<div class="card-body">
		
		<?php if ( !empty( $row['data'] ) ) :
			foreach( $row['data'] as $id_ => $data ) :
			
			//Hide any element should not be visible
			if ( isset( $data['hide'] ) && !empty( $data['hide'] ) )
				continue;
		
			if ( isset( $data['dnone'] ) && !empty( $data['dnone'] ) ) : ?>
			<div <?php echo ( isset( $data['div-id'] ) ? 'id="' . $data['div-id'] . '" ' : null ) ?>class="d-none">
			<?php endif ?>

		<?php if ( $data['title'] ) : ?>
			<!-- <?php echo $data['title'] ?> -->
			<h5><?php echo $data['title'] ?></h5>
			<hr />
		<?php endif ?>
		
		<?php if ( isset( $data['tip'] ) && !empty( $data['tip'] ) ) : ?>
		<div class="alert alert-info" role="alert">
			<?php echo $data['tip'] ?>
		</div>
		<?php endif ?>
		
		<?php

			foreach( $data['data'] as $id__ => $key ) :
				
				//Hide any element should not be visible
				if ( isset( $key['hide'] ) && !empty( $key['hide'] ) )
					continue;
				
				//Custom html
				if ( $key['type'] == 'custom-html' )
				{
					echo $key['value'];
				}
				
				//Button
				if ( $key['type'] == 'button' )
				{
					$args = array(
							'id' => $id__,
							'title' => __( 'run-task' ),
							'label' => $key['label'],
							'name' => 'func[' . $id__ . ']',
							'type' => 'submit',
							'class' => 'btn-secondary',
							'tip' => ( $key['tip'] ? $key['tip'] : null ),
							'form-group' => true,
					);
				
					Button( $args );
				}
				
				//Check inline
				if ( $key['type'] == 'check-inline' )
				{
					$args = array(
							'id' => $id__,
							'label' => $key['label'],
							'name' => $id___,
							'type' => 'radio',
							'class' => 'row',
							'checked' => ( ( $key['value'] == 'true' ) ? true : false ), 
							'disabled' => ( ( isset( $key['disabled'] ) && $key['disabled'] ) ? true : false ),
							'tip' => ( $key['tip'] ? $key['tip'] : false ),
							'radio-data' => array()
					);
					
					if ( !empty( $key['data'] ) )
					{
						foreach( $key['data'] as $id___ => $option )
						{
							$args['radio-data'][$id__] = array(
										'name' => $id___,
										'value' => $option['name'],
										'title' => $option['title'],
										'checked' => ( ( $key['value'] == 'true' ) ? true : false ),
										'disabled' => ( ( isset( $key['disabled'] ) && $key['disabled'] ) ?  true : false ),
							);
						}
					}
					
					CheckBox( $args );
				}
				
				//Checkbox
				if ( $key['type'] == 'checkbox' )
				{
					$class = ( isset( $key['class'] ) ? $key['class'] : '' ) . ( ( isset( $key['dnone'] ) && !empty( $key['dnone'] ) ) ? ' d-none' : '' );
					
					$args = array(
							'id' => ( isset( $key['id'] ) ? $key['id'] : $id__ ),
							'label' => $key['label'],
							'name' => $key['name'],
							'class' => $class,
							'div-id' => ( isset( $key['div-id'] ) ? $key['div-id'] : null ),
							'checked' => ( ( $key['value'] == 'true' ) ? true : false ), 
							'disabled' => ( ( isset( $key['disabled'] ) && $key['disabled'] ) ? true : false ),
							'tip' => ( $key['tip'] ? $key['tip'] : false )
					);
					
					CheckBox( $args );
				}
				
				//Password
				if ( $key['type'] == 'password' )
				{
					$args = array(
							'id' => $id__,
							'name' => $key['name'],
							'label' => $key['label'],
							'value' => $key['value'],
							'type' => 'password',
							'placeholder' => ( ( isset( $key['placeholder'] ) && !empty( $key['placeholder'] ) ) ? $key['placeholder'] : null ),
							'required' => ( ( isset( $key['required'] ) && $key['required'] ) ? true : false ),
							'disabled' => ( ( isset( $key['disabled'] ) && $key['disabled'] ) ? true : false ),
							'tip' => ( $key['tip'] ? $key['tip'] : null )
					);
				?>
				<div class="form-group row">
					<label for="<?php echo $id_ ?>" class="col-sm-2 col-form-label"><?php echo $key['label'] ?></label>
					<div class="col-sm-10">
						<?php FormInput( $args ) ?>
					</div>
				</div>
				<?php 
				}
				
				//Add button
				if ( $key['type'] == 'add' )
				{
					$args = array(
							'id' => $id__,
							'align' => 'center',
							'class' => 'btn-default add',
							'value' => __( 'add-item' ),
							'addBefore' => '<div class="repeatable"></div>'
					);
					
					Button( $args );
				}
				
				//Hidden form
				if ( $key['type'] == 'hidden' )
				{
					HiddenFormInput( array( 'name' => $key['name'], 'value' => $key['value'] ) );
				}
			?>
			
			<?php if ( $key['type'] == 'simple-text' ) : ?>
			<div class="form-group row">
				<legend class="col-form-label col-sm-2 pt-0"><?php echo $key['label'] ?></legend>
				<div class="col-sm-10">
					<label for="<?php echo $id_ ?>" class="col-sm-2 col-form-label"><?php echo $key['value'] ?></label>
				</div>
			</div>
			<?php endif ?>

			<?php if ( $key['type'] == 'textarea' ) : ?>
			<div class="form-group row<?php echo ( ( isset( $key['dnone'] ) && !empty( $key['dnone'] ) ) ? ' d-none' : '' ) ?>">
			<label for="<?php echo $id_ ?>" class="col-sm-2 col-form-label"><?php echo $key['label'] ?></label>
			<div class="col-sm-10">
			  <textarea class="form-control" id="<?php echo $id__ ?>" name="<?php echo $key['name'] ?>" <?php echo ( ( isset( $key['rows'] ) && !empty( $key['rows'] ) ) ? 'rows="' . $key['rows'] . '"' : 'rows="3"' ) ?> <?php echo ( ( isset( $key['placeholder'] ) && !empty( $key['placeholder'] ) ) ? 'placeholder="' . $key['placeholder'] . '"' : '' ) ?>><?php echo $key['value'] ?></textarea>
			  
			  <?php if ( isset( $key['buttons'] ) && !empty( $key['buttons'] ) ) : ?>
			<?php foreach( $key['buttons'] as $bId => $bRow ) : ?>
				<button type="button" class="btn btn-secondary btn-sm" id="<?php echo $bId ?>-<?php echo $id__ ?>" data-value="<?php echo $bRow['var'] ?>"><?php echo $bRow['title'] ?></button>
				<script type="text/javascript">
				$('button[id^="<?php echo $bId ?>-<?php echo $id__ ?>"]').on('click', function() {
					var $target = $('#<?php echo $id__ ?>'),
						text = $('#<?php echo $id__ ?>').val(),
						buttonVal = $(this).data('value');
					$target.val(`${text}${buttonVal}`);
				});</script>
			<?php endforeach ?>
			<?php endif ?>
			  
			<?php if ( $key['tip'] ) : ?>
				<small id="<?php echo $id__ ?>-tip" class="form-text text-muted"><?php echo $key['tip'] ?></small>
			<?php endif ?>
			</div>
			  </div>
			<?php endif ?>
			
			<?php if ( $key['type'] == 'radio' ) : ?>
			<fieldset class="form-group">
			<div class="row">
			  <legend class="col-form-label col-sm-2 pt-0"><?php echo $key['label'] ?></legend>
			  <div class="col-sm-10">
				<?php if ( !empty( $key['data'] ) ) :
					foreach( $key['data'] as $id___ => $option ) :
				?>
				<div class="form-check <?php echo ( isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '' ) ?>">
				  <input class="form-check-input" type="radio" name="<?php echo $id__ ?>" id="<?php echo $id___ ?>" value="<?php echo $option['value'] ?>" <?php echo ( isset( $option['checked'] ) && $option['checked'] ? 'checked' : '' ) ?><?php echo ( isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '' ) ?>>
				  
				  <label class="form-check-label" for="<?php echo $id___ ?>">
					<?php echo $option['title'] ?>
				</label>
				</div>
				<?php endforeach ?>
				<?php endif ?>
				
				<?php if ( $key['tip'] ) : ?>
				  <small id="<?php echo $id__ ?>-tip" class="form-text text-muted"><?php echo $key['tip'] ?></small>
				<?php endif ?>
			  </div>
			</div>
			</fieldset>
			<?php endif ?>
			
			<?php if ( $key['type'] == 'select-group' ) : ?>
			<div class="form-group row">
				<label for="<?php echo $id_ ?>" class="col-sm-2 col-form-label"><?php echo $key['label'] ?></label>
				<div class="col-md-4">
					<select <?php echo ( isset( $key['disabled'] ) && $key['disabled'] ? 'disabled' : '' ) ?> name="<?php echo $key['name'] ?>" class="form-control <?php echo ( isset( $key['class'] ) ? $key['class'] : '' ) ?>" id="<?php echo ( isset( $key['id'] ) ? $key['id'] : $id_ ) ?>" <?php echo ( isset( $key['extraKeys'] ) && !empty($key['extraKeys'] ) ? $key['extraKeys']['name'] . '="' . $key['extraKeys']['data'] . '"' : '' ) ?>>
					<?php if ( $key['firstNull'] ) : ?>
						<option value=""><?php echo $L['choose'] ?>...</option>
					<?php endif ?>
					<?php if ( !empty( $key['data'] ) ) :
						foreach( $key['data'] as $id___ => $row ) :
					?>
					<optgroup label="<?php echo $row['name'] ?>">
					<?php if ( !empty( $row['data'] ) ) :
						foreach( $row['data'] as $id____ => $option ) :
					?>
					<option value="<?php echo $option['name'] ?>" <?php echo ( ( isset( $option['disabled'] ) && $option['disabled'] ) ? 'disabled' : '' ) ?> <?php echo ( ( $key['value'] == $option['name'] ) ? 'selected' : '' ) ?> <?php echo ( isset( $option['data'] ) && !empty( $option['data'] ) ? $option['data']['name'] . '="' . $option['data']['value'] . '"' : '' ) ?>><?php echo $option['title'] ?></option>
					<?php endforeach ?>
					<?php endif ?>
					
					<?php endforeach ?>
					<?php endif ?>
					</select>
					
					<?php if ( $key['tip'] ) : ?>
				<small id="<?php echo $id__ ?>-tip" class="form-text text-muted"><?php echo $key['tip'] ?></small>
				<?php endif ?>
				</div>
			</div>
			<?php endif ?>
			
			<?php if ( $key['type'] == 'select-group-multi' ) : ?>
			<div class="form-group row">
				<label for="<?php echo $id_ ?>" class="col-sm-2 col-form-label"><?php echo $key['label'] ?></label>
				<div class="col-md-4">
					<select <?php echo ( isset( $key['disabled'] ) && $key['disabled'] ? 'disabled' : '' ) ?> name="<?php echo $key['name'] ?>" class="form-control <?php echo ( isset( $key['class'] ) ? $key['class'] : '' ) ?>" id="<?php echo ( isset( $key['id'] ) ? $key['id'] : $id_ ) ?>" <?php echo ( isset( $key['extraKeys'] ) && !empty($key['extraKeys'] ) ? $key['extraKeys']['name'] . '="' . $key['extraKeys']['data'] . '"' : '' ) ?>>
					<?php if ( $key['firstNull'] ) : ?>
						<option value=""><?php echo $L['choose'] ?>...</option>
					<?php endif ?>
					
					<?php
						if ( !empty( $key['data'] ) ) :
							if ( $Admin->MultiBlog() ) : //Multiblog has more keys
								foreach( $key['data'] as $id___ => $row ) : ?>
									<optgroup label="<?php echo $row['name'] ?>">
									<?php if ( !empty( $row['childs'] ) ) : ?>
										<?php foreach( $row['childs'] as $id____ => $childs1 ) : ?>
											<?php if ( !empty( $childs1['childs'] ) ) : ?>
												<?php if ( $childs1['type'] == 'blog' ) : ?>
													<optgroup label="&nbsp;-<?php echo $childs1['name'] ?>">
												<?php endif ?>
												<?php foreach( $childs1['childs'] as $id_____ => $childs2 ) : ?>
													<option value="<?php echo $childs2['id'] ?>" <?php echo ( ( $key['value'] == $childs2['id'] ) ? 'selected' : '' ) ?>>&nbsp;&nbsp;<?php echo ( !empty( $childs2['childs'] ) ? '<strong>' . $childs2['name'] . '</strong>': $childs2['name'] ) ?></option>
												
													<?php if ( !empty( $childs2['childs'] ) ) : ?>
														<?php foreach( $childs2['childs'] as $id______ => $child ) : ?>
															<option value="<?php echo $child['id'] ?>" <?php echo ( ( $key['value'] == $child['id'] ) ? 'selected' : '' ) ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---<?php echo $child['name'] ?></option>
														<?php endforeach ?>
													<?php endif ?>
												<?php endforeach ?>
											<?php endif ?>

										<?php endforeach ?>
									<?php endif ?>
							
								<?php endforeach ?>
							<?php else : ?>
								<?php foreach( $key['data'] as $id___ => $row ) : ?>
								<optgroup label="<?php echo $id___ ?>">
								<?php if ( !empty( $row['childs'] ) ) : ?>
									<?php foreach( $row['childs'] as $id____ => $childs1 ) : ?>
										<optgroup label="&nbsp;-<?php echo $childs1['name'] ?>">
										<?php foreach( $childs1['childs'] as $id_____ => $childs2 ) : ?>
											<option value="<?php echo $childs2['id'] ?>" <?php echo ( ( $key['value'] == $childs2['id'] ) ? 'selected' : '' ) ?>>&nbsp;&nbsp;<?php echo ( !empty( $childs2['childs'] ) ? '<strong>' . $childs2['name'] . '</strong>': $childs2['name'] ) ?></option>
											
											<?php if ( !empty( $childs2['childs'] ) ) : ?>
												<?php foreach( $childs2['childs'] as $id______ => $child ) : ?>
													<option value="<?php echo $child['id'] ?>" <?php echo ( ( $key['value'] == $child['id'] ) ? 'selected' : '' ) ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---<?php echo $child['name'] ?></option>
												<?php endforeach ?>
											<?php endif ?>
											
										<?php endforeach ?>
										</optgroup>
									<?php endforeach ?>
								<?php endif ?>
								</optgroup>
								<?php endforeach ?>
							<?php endif ?>
						<?php endif ?>
					</select>
					
					<?php if ( $key['tip'] ) : ?>
				<small id="<?php echo $id__ ?>-tip" class="form-text text-muted"><?php echo $key['tip'] ?></small>
				<?php endif ?>
				</div>
			</div>
			<?php endif ?>
			
			<?php if ( $key['type'] == 'select' ) : ?>
			<div <?php echo ( ( isset( $key['id'] ) && !empty( $key['id'] ) ) ? 'id="' . $key['id'] . '"' : '' ) ?> class="form-group row<?php echo ( ( isset( $key['dnone'] ) && !empty( $key['dnone'] ) ) ? ' d-none' : '' ) ?>">
			<label for="<?php echo $id__ ?>" class="col-sm-2 col-form-label"><?php echo $key['label'] ?></label>
			<div class="col-md-4">
			<?php if ( isset( $key['addBefore'] ) )
				echo $key['addBefore'];
			?>
				<select <?php echo ( isset( $key['disabled'] ) && $key['disabled'] ? 'disabled' : '' ) ?> name="<?php echo $key['name'] ?>" class="<?php echo ( isset( $key['class'] ) ? $key['class'] : 'form-control' ) ?>" <?php echo ( isset( $key['multiple'] ) && $key['multiple'] ? 'multiple' : '' ) ?> id="<?php echo ( isset( $key['id'] ) ? $key['id'] : $id__ ) ?>" <?php echo ( isset( $key['extraKeys'] ) && !empty($key['extraKeys'] ) ? $key['extraKeys']['name'] . '="' . $key['extraKeys']['data'] . '"' : '' ) ?>>
				<?php if ( $key['firstNull'] ) : ?>
				<option value=""><?php echo $L['choose'] ?>...</option>
				<?php endif ?>
				<?php if ( !empty( $key['data'] ) ) :
				foreach( $key['data'] as $id___ => $option ) :
				?>
				<option value="<?php echo $option['name'] ?>" <?php echo ( ( isset( $option['disabled'] ) && $option['disabled'] ) ? 'disabled' : '' ) ?> <?php echo ( ( ( $key['value'] == $option['name'] ) && !is_array( $key['value'] ) ) ? 'selected' : '' ) ?> <?php echo ( ( is_array( $key['value'] ) && !empty( $key['value'] ) && in_array( $option['name'], $key['value'] ) ) ? 'selected' : '' ) ?> <?php echo ( isset( $option['data'] ) && !empty( $option['data'] ) ? $option['data']['name'] . '="' . $option['data']['value'] . '"' : '' ) ?>><?php echo $option['title'] ?></option>
				<?php endforeach ?>
				<?php endif ?>
				</select>
				
				<?php if ( $key['tip'] ) : ?>
				<small id="<?php echo $id__ ?>-tip" class="form-text text-muted"><?php echo $key['tip'] ?></small>
				<?php endif ?>
			</div>
			<?php if ( isset( $key['addAfter'] ) )
				echo $key['addAfter'];
			?>
			</div>
			<?php endif ?>

			<?php if ( $key['type'] == 'num' ) : ?>
			<div <?php if ( isset( $key['div-id'] ) ) : echo ' id="' . $key['div-id'] . '"'; endif; ?> class="form-group row<?php echo ( isset( $key['class'] ) ? ' ' . $key['class'] : '' ) . ( ( isset( $key['dnone'] ) && $key['dnone'] ) ? ' d-none' : '' ) ?>">
				<label for="<?php echo $id__ ?>" class="col-sm-2 col-form-label"><?php echo $key['label'] ?></label>
				<div class="col-sm-10">
				<input id="<?php echo $id__ ?>" value="<?php echo $key['value'] ?>" type="number" class="form-control-border border-width-2" name="<?php echo $key['name'] ?>" <?php echo ( ( isset( $key['step'] ) && $key['step'] ) ? 'step="' . $key['step'] . '"' : 'step="any"') ?> <?php if ( isset( $key['min'] ) ) : ?> min="<?php echo $key['min'] ?>"<?php endif ?> <?php if ( isset( $key['max'] ) ) : ?> max="<?php echo $key['max'] ?>"<?php endif ?> <?php echo ( ( isset( $option['disabled'] ) && $option['disabled'] ) ? 'disabled' : '' ) ?>>
				
				<?php if ( $key['tip'] ) : ?>
					<small id="<?php echo $id__ ?>-tip" class="form-text text-muted"><?php echo $key['tip'] ?></small>
				<?php endif ?>
				</div>
			</div>
			<?php endif ?>
			
			<?php if ( $key['type'] == 'text' ) : 
				$idc = ( ( isset( $key['id'] ) && !empty( $key['id'] ) ) ? $key['id'] : $id__ );
			?>
			<div <?php echo ( ( isset( $key['div-id'] ) && !empty( $key['div-id'] ) ) ? 'id="' . $key['div-id'] . '" ' : '' ) ?>class="form-group row<?php echo ( ( isset( $key['dnone'] ) && !empty( $key['dnone'] ) ) ? ' d-none' : '' ) ?>">
			<label for="<?php echo $idc ?>" class="col-sm-2 col-form-label"><?php echo $key['label'] ?></label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="<?php echo $id__ ?>" name="<?php echo $key['name'] ?>" value="<?php echo $key['value'] ?>" <?php echo ( isset( $key['required'] ) && $key['required'] ? 'required' : '' ) ?> <?php echo ( ( isset( $key['placeholder'] ) && !empty( $key['placeholder'] ) ) ? 'placeholder="' . $key['placeholder'] . '"' : '' ) ?> <?php echo ( isset( $key['disabled'] ) && $key['disabled'] ? 'disabled' : '' ) ?>>
			<?php if ( isset( $key['buttons'] ) && !empty( $key['buttons'] ) ) : ?>
			<?php foreach( $key['buttons'] as $bId => $bRow ) : ?>
				<button type="button" class="btn btn-secondary btn-sm" id="<?php echo $bId ?>-<?php echo $id__ ?>" data-value="<?php echo $bRow['var'] ?>"><?php echo $bRow['title'] ?></button>
				<script type="text/javascript">
				$('button[id^="<?php echo $bId ?>-<?php echo $id__ ?>"]').on('click', function() {
					var $target = $('#<?php echo $id__ ?>'),
						text = $('#<?php echo $id__ ?>').val(),
						buttonVal = $(this).data('value');
					$target.val(`${text}${buttonVal}`);
				});</script>
			<?php endforeach ?>
			<?php endif ?>
			<?php if ( $key['tip'] ) : ?>
				<small id="<?php echo $id__ ?>-tip" class="form-text text-muted"><?php echo $key['tip'] ?></small>
			<?php endif ?>

			</div>
			  </div>
			<?php endif ?>
			
			<?php
			/*
			/*
			<div class="form-group row">
			<div class="col-sm-2"><?php echo $key['label'] ?></div>
			<div class="col-sm-10">
				<?php if ( $key['tip'] ) : ?>
					<small id="<?php echo $id__ ?>" class="form-text text-muted"><?php echo $key['tip'] ?></small><br />
				<?php endif ?>
				
				<button type="submit" class="btn btn-secondary" id="<?php echo $id__ ?>" name="func[<?php echo $id__ ?>]">
					<?php echo __ ( 'run-task' ) ?>
				</button>
			</div>
			  </div>
			<?php endif 
			
			<?php if ( $key['type'] == 'check-inline' )
			{
				$args = array(
						'id' => $id__,
						'label' => $key['label'],
						'name' => $id___,
						'type' => 'radio',
						'class' => 'row',
						'checked' => ( ( $key['value'] == 'true' ) ? true : false ), 
						'disabled' => ( ( isset( $key['disabled'] ) && $key['disabled'] ) ? true : false ),
						'tip' => ( $key['tip'] ? $key['tip'] : false ),
						'radio-data' => array()
				);
				
				if ( !empty( $key['data'] ) )
				{
					foreach( $key['data'] as $id___ => $option )
					{
						$args['radio-data'][$id__] = array(
									'name' => $id___,
									'value' => $option['name'],
									'title' => $option['title'],
									'checked' => ( ( $key['value'] == 'true' ) ? true : false ),
									'disabled' => ( ( isset( $key['disabled'] ) && $key['disabled'] ) ?  true : false ),
						);
					}
				}
				
				CheckBox( $args );
			}*
			/*
			<div class="form-group row">
			<?php if ( !empty( $key['data'] ) ) :
				foreach( $key['data'] as $id___ => $option ) :
			?>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="<?php echo $id___ ?>" id="<?php echo $id__ ?>" value="<?php echo $option['name'] ?>" <?php echo ( ( $key['value'] == 'true' ) ? 'checked' : '' ) ?> <?php echo ( isset( $key['disabled'] ) && $key['disabled'] ? 'disabled' : '' ) ?>>
				  <label class="form-check-label" for="<?php echo $id__ ?>"><?php echo $option['title'] ?></label>
				</div>
			<?php endforeach; endif; ?>
				<?php if ( $key['tip'] ) : ?>
					<small id="<?php echo $id__ ?>" class="form-text text-muted"><?php echo $key['tip'] ?></small>
				<?php endif ?>
			</div>
			<?php endif 
			
			<?php if ( $key['type'] == 'checkbox' )
			{
				$args = array(
						'id' => ( isset( $key['id'] ) ? $key['id'] : $id__ ),
						'label' => $key['label'],
						'name' => $key['name'],
						'checked' => ( ( $key['value'] == 'true' ) ? true : false ), 
						'disabled' => ( ( isset( $key['disabled'] ) && $key['disabled'] ) ? true : false ),
						'tip' => ( $key['tip'] ? $key['tip'] : false )
				);
				
				CheckBox( $args );
			}
			
			<div class="form-group row">
			<div class="col-sm-2"><?php echo $key['label'] ?></div>
			<div class="col-sm-10">
			  <div class="form-check">
				<input class="form-check-input" name="<?php echo $key['name'] ?>" id="<?php echo (isset( $key['id'] ) ? $key['id'] : $id__ ) ?>" value="1" type="checkbox" <?php echo ( ( $key['value'] == 'true' ) ? 'checked' : '' ) ?> <?php echo ( isset( $key['disabled'] ) && $key['disabled'] ? 'disabled' : '' ) ?>>
				
				<?php if ( $key['tip'] ) : ?>
				<small id="<?php echo $id__ ?>" class="form-text text-muted"><?php echo $key['tip'] ?></small>
				<?php endif ?>
			  </div>
			</div>
			  </div>
			<?php endif * /?>
			if ( $key['type'] == 'password' )
			{
				$args = array(
						'id' => $id__,
						'name' => $key['name'],
						'label' => $key['label'],
						'value' => $key['value'],
						'type' => 'password',
						'placeholder' => ( ( isset( $key['placeholder'] ) && !empty( $key['placeholder'] ) ) ? $key['placeholder'] : null ),
						'required' => ( ( isset( $key['required'] ) && $key['required'] ) ? true : false ),
						'disabled' => ( ( isset( $key['disabled'] ) && $key['disabled'] ) ? true : false ),
						'tip' => ( $key['tip'] ? $key['tip'] : null )
				);
			
				FormInput( $args );
			}
			<div class="form-group row">
			<label for="<?php echo $id_ ?>" class="col-sm-2 col-form-label"><?php echo $key['label'] ?></label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="<?php echo $id__ ?>" name="<?php echo $key['name'] ?>" value="<?php echo $key['value'] ?>" <?php echo ( isset( $key['required'] ) && $key['required'] ? 'required' : '' ) ?> <?php echo ( ( isset( $key['placeholder'] ) && !empty( $key['placeholder'] ) ) ? 'placeholder="' . $key['placeholder'] . '"' : '' ) ?> <?php echo ( isset( $key['disabled'] ) && $key['disabled'] ? 'disabled' : '' ) ?>>
			<?php if ( $key['tip'] ) : ?>
				<small id="<?php echo $id__ ?>" class="form-text text-muted"><?php echo $key['tip'] ?></small>
			<?php endif ?>

			</div>
			  </div>
			<?php endif
			
			if ( $key['type'] == 'add' )
			{
				$args = array(
						'id' => $id__,
						'align' => 'center',
						'class' => 'btn-default add',
						'value' => __( 'add-item' ),
						'addBefore' => '<div class="repeatable"></div>'
				);
				
				Button( $args );
			}
			
			if ( $key['type'] == 'hidden' )
			{
				HiddenFormInput( array( 'name' => $key['name'], 'value' => $key['value'] ) );
			}
			<div class="repeatable"></div>

			<div class="form-group row">
				<input type="button" value="<?php echo $L['add-item'] ?>" class="btn btn-default add" align="center">
			</div>
			<?php endif
			
			
			if ( $key['type'] == 'hidden' )
			{
				HiddenFormInput( array( 'name' => $key['name'], 'value' => $key['value'] ) );
			}
			<input type="hidden" name="<?php echo $key['name'] ?>" value="<?php echo $key['value'] ?>">
			<?php endif */?>
			<?php endforeach ?>
			
		<?php if ( isset( $data['dnone'] ) && !empty( $data['dnone'] ) ) : ?>
			</div>
		<?php endif ?>
			
		<?php endforeach ?>
		<?php endif ?>
		</div>
	</div>
</div>
