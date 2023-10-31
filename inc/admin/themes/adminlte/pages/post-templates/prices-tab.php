<?php
	$prices 	 = GetAdminPrices( $Post->PostID() );
	$stores		 = Stores( $Post->Site()->id, false );
	$currencies  = Currencies( $Post->Site()->id, false );
	$lastPriceId = 0;
?>
<h5><?php echo __( 'price-list' ) ?></h5>
<div class="mb-3">
	<div class="form-group row">
		<label for="priceList" class="col-sm-2 col-form-label"><?php __( 'price-list' ) ?></label>
		<div class="col-md-12">
			
			<div class="mb-3">
				<small class="form-text text-muted"><?php echo __( 'price-list-tip' ) ?></strong></small>
			</div>
			
			<div class="mb-3">
				<label for="list-title" class="form-label"><?php echo __( 'list-title' ) ?></label>
				<input type="text" id="priceListTitle" name="postExtra[priceListTitle]" value="<?php echo $Post->ExtraData( 'pricesTitle' ) ?>" placeholder="" class="form-control" />
				<small id="input-list-title" class="form-text text-muted"><?php echo __( 'list-title-tip' ) ?></strong></small>
			</div>

			<?php 
				$args = array(
					'id' => 'allow-voting',
					'label' => __( 'allow-voting' ),
					'name' => 'postExtra[allowVoting]',
					'checked' => ( !empty( $Post->ExtraData( 'allowVoting' ) ) ? true : false ), 
					'disabled' => false,
					'tip' => __( 'allow-voting-tip' )
				);

				CheckBox( $args );
			?>
			
			<?php 
				$args = array(
					'id' => 'add-price-num',
					'label' => __( 'add-price-num' ),
					'name' => 'postExtra[addPriceNum]',
					'checked' => ( !empty( $Post->ExtraData( 'addPriceNum' ) ) ? true : false ), 
					'disabled' => false,
					'tip' => __( 'add-price-num-tip' )
				);

				CheckBox( $args );
			?>

			<div class="table-responsive">				
				<table id="postPricesTable" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-left"><?php echo __( 'store' ) ?></th>
							<th class="text-center"><?php echo __( 'sale-price' ) ?></th>
							<th class="text-center"><?php echo __( 'added' ) ?></th>
							<th class="text-center"><?php echo __( 'updated' ) ?></th>
							<th class="text-center" style="width: 1px;"><?php echo __( 'actions' ) ?></th>
						</tr>
					</thead>
					<tbody>
					<?php if ( !empty( $prices ) ) :
						foreach ( $prices as $price ) : 
							
							if( !empty( $price['main_page_url'] ) ) 
							{
								$url = '<a href="' . $price['main_page_url'] . '" target="_blank" rel="noreferrer noopener">' . $price['st'] . '</a>';
							}
							else
							{
								$url = $price['st'];
							}
						?>
						<tr id="priceField-row<?php echo $price['id_price'] ?>">
							<td id="url" class="text-left"><?php echo $url ?></td>
							<td class="text-center"><?php echo $price['sale_price'] ?> (<?php echo $price['cs'] ?>)</td>
							<td class="text-center"><?php echo postDate( $price['time_added'] ) ?></td>
							<td class="text-center"><?php echo ( !empty( $price['lu'] ) ? postDate( $price['lu'] ) : '-' ) ?></td>
							<td class="text-left">
								<div class="btn-group">

									<button type="button" data-toggle="modal" data-target="#editPriceModal" data-id="<?php echo $price['id_price'] ?>" class="btn btn-default btn-flat btn-xs mr-1 editPriceButton" title="<?php echo __( 'edit' ) ?>"><i class="fa fa-cog"></i></button>
									
									<?php if ( $canViewAttachments ) : ?>
									<button type="button" onclick="addPriceImage(<?php echo $price['id_price'] ?>,'priceCover');" class="btn btn-default btn-flat btn-xs mr-1" data-toggle="tooltip" title="<?php echo __( 'cover-image' ) ?>"><i class="fa fa-image"></i></button>
									<?php endif ?>
									
									<button type="button" data-toggle="tooltip" title="<?php echo __( 'remove' ) ?>" data-id="<?php echo $price['id_price'] ?>" class="btn btn-danger btn-flat btn-xs removePriceButton" title="<?php echo __( 'remove' ) ?>"><i class="fa fa-minus-circle"></i></button>
								</div>
							</td>
						</tr>
					<?php endforeach ?>
					<?php $lastPriceId = $price['id_price'] ?>
					<?php endif ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6"></td>
							<td class="text-left"><button id="newPriceButton" type="button" data-toggle="modal" data-target="#modal-newPrice" title="<?php echo __( 'add-new-price' ) ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
						</tr>
					</tfoot>
				</table>
				<input type="hidden" id="lastPriceId" name="lastPriceId" value="<?php echo $lastPriceId ?>">
			</div>
		</div>
	</div>
</div>