<?php
	require ( ARRAYS_ROOT . 'language-arrays.php');
	
	$totalArr = count( $gTransLangs );
	
	$grids = array(
		'grid1' => array_slice( $gTransLangs,  0, 25 ),
		'grid2' => array_slice( $gTransLangs, 25, 25 ),
		'grid3' => array_slice( $gTransLangs, 50, 25 ),
		'grid4' => array_slice( $gTransLangs, 75, 25 ),
		'grid5' => array_slice( $gTransLangs, 90, $totalArr )
	);
?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form class="tab-content" id="form" method="post" action="" autocomplete="off">
					<div class="form-row">
						<div class="form-group col-md-10">
							<h4><?php echo __( 'automatic-translator' ) ?></h4>
							
							<?php 
							$args = array(
								'id' 		=> 'enable-auto-translate',
								'label' 	=> __( 'enable-auto-translate' ),
								'tip' 		=> __( 'enable-auto-translate-tip' ),
								'name'		=> 'auto_translate',
								'checked' 	=> ( !empty( $autoTrans['auto_translate'] ) ? true : false )
							);

							CheckBox( $args );
							?>
							
							<div class="form-group<?php echo ( empty( $autoTrans['auto_translate'] ) ? ' d-none' : '' ) ?>" id="selectLanguages">
								<div class="callout callout-info">
									<p><?php echo sprintf( __( 'automatic-translator-seo-tip' ), $Admin->GetUrl( 'langs' ) ) ?></p>
								</div>
								<label for="selectLanguages"><?php echo $L['choose-language'] ?></label>
								<select multiple name="autolangs[]" class="form-control">
								<?php if ( !empty( $otherLangs ) ) :
										foreach ( $otherLangs as $lang ): ?>
									<option value="<?php echo $lang['lang']['id'] ?>" data-flag="<?php echo SITE_URL . 'languages' . PS . 'flags' . PS . $key . '.png' ?>" <?php echo ( ( !empty( $autoTrans['auto_langs'] ) && isset( $autoTrans['auto_langs'][$lang['lang']['code']] ) ) ? 'selected' : '' ) ?>><?php echo $lang['lang']['title'] ?></option>
								<?php endforeach; endif; ?>
								</select>
							</div>
	
							<div id="checkLanguagesTables" class="<?php echo ( !empty( $autoTrans['auto_translate'] ) ? 'd-none' : '' ) ?>">
							
								<div class="callout callout-info">
									<p><?php echo __( 'auto-translate-tip' ) ?></p>
								</div>

								<table class="form-table">
									<tr valign="top">
										<th scope="row" style="width: 20%;">
											<?php echo __( 'base-language' ) ?><br/>
											<small id="baseLanguageTip" class="form-text text-muted"><?php echo __( 'base-language-tip' ) ?></small>
										</th>
										
										<td>
										<?php echo '<strong>' . $Admin->DefaultLang()['title'] . '</strong>' ?>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<?php echo __( 'supported-languages' ) ?><br/>
											<small id="langsTip" class="form-text text-muted"><?php echo __( 'select-supported-languages-tip' ) ?></small>
										</th>
										<?php foreach( $grids as $_ => $grid ) : ?>
										<td>
										<?php 
											foreach( $grid as $__ => $lang ) :
												$args = array(
													'id' 		=> 'lang-' . $lang['name'],
													'label' 	=> $lang['title'],
													'name'		=> 'checkLangs[' . $lang['name'] . ']',
													'checked' 	=> in_array( $lang['name'], $autoTrans['checked_langs'] )
												);

												CheckBox( $args );
												endforeach; ?>
										</td>
									<?php endforeach ?>
									</tr>
								</table>        
							</div>
							
							<input type="hidden" name="_token" value="<?php echo generate_token( 'automatic-translator' ) ?>">
		
							<div class="align-middle">
								<div class="float-left mt-1">
									<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
									<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
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
    $("[data-bs-toggle=tooltip]").tooltip();
});</script>