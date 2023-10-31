<?php
	$deals = GetAdminDeals( $Post->PostID() );
	$stores = Stores( $Post->Site()->id, false );
	$currencies = Currencies( $Post->Site()->id, false );
	$lastDealId = 0;
?>
<h5><?php echo __( 'deals-list' ) ?></h5>
<div class="mb-3">
	<div class="form-group row">
		<label for="priceList" class="col-sm-2 col-form-label"><?php __( 'price-list' ) ?></label>
		<div class="col-md-12">
			<div class="table-responsive">				
				<table id="postDealsTable" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-left"><?php echo __( 'store' ) ?></th>
							<th class="text-center"><?php echo __( 'price' ) ?></th>
							<th class="text-center"><?php echo __( 'coupon-code' ) ?></th>
							<th class="text-center"><?php echo __( 'date' ) ?></th>
							<th class="text-center"><?php echo __( 'expiration-date' ) ?></th>
							<th class="text-center" style="width: 1px;"><?php echo __( 'actions' ) ?></th>
						</tr>
					</thead>
					<tbody>
					<?php if ( !empty( $deals ) ) :
						foreach ( $deals as $deal ) : 
							
							if( !empty( $deal['main_page_url'] ) ) 
							{
								$url = '<a href="' . $deal['main_page_url'] . '" target="_blank" rel="noreferrer noopener">' . $deal['st'] . '</a>';
							}
							else
							{
								$url = $deal['st'];
							}
							
							$exp = ( !empty( $deal['expire_time'] ) ? postDate( $deal['expire_time'] ) : '---' );
							$pri = formatPrice( $deal['sale_price'], $deal['cf'] );
							$pri .= ( !empty( $deal['discount_title'] ) ? ' (' . $deal['discount_title'] . ')' : '' );
							$cpn = ( !empty( $deal['coupon_code'] ) ? $deal['coupon_code'] : '---' );
						?>
						<tr id="dealField-row<?php echo $deal['id_price'] ?>">
							<td id="url" class="text-left"><?php echo $url ?></td>
							<td class="text-center"><?php echo $pri ?></td>
							<td class="text-center"><?php echo $cpn ?></td>
							<td class="text-center"><?php echo postDate( $deal['available_since'] ) ?></td>
							<td class="text-center"><?php echo $exp ?></td>
							<td class="text-left">
								<div class="btn-group">
									<button type="button" id="editDealButton" data-toggle="modal" data-target="#editDealModal" data-id="<?php echo $deal['id_price'] ?>" class="btn btn-default btn-flat btn-xs mr-1" data-toggle="tooltip" title="<?php echo __( 'edit' ) ?>"><i class="fa fa-cog"></i></button>
									<button type="button" id="removeDealButton" data-toggle="tooltip" title="<?php echo __( 'remove' ) ?>" data-id="<?php echo $deal['id_price'] ?>" class="btn btn-danger btn-flat btn-xs" data-toggle="tooltip" title="<?php echo __( 'edit' ) ?>"><i class="fa fa-minus-circle"></i></button>
								</div>
							</td>
							<input type="hidden" name="dealsDb[]" value="<?php echo $deal['id_price'] ?>">
						</tr>
					<?php endforeach ?>
					<?php $lastDealId = $deal['id_price'] ?>
					<?php endif ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6"></td>
							<td class="text-left"><button id="newDealButton" type="button" data-toggle="modal" data-target="#modal-newDeal" title="<?php echo __( 'add-new-price' ) ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
						</tr>
					</tfoot>
				</table>
				<input type="hidden" id="lastDealId" name="lastDealId" value="<?php echo $lastDealId ?>">
			</div>
		</div>
	</div>
</div>