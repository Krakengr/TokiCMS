<?php 
	$variationParentId 	 = ( !empty( $Post->Variations()['id'] ) ? $Post->Variations()['id'] : null );
	$variationParentName = ( !empty( $Post->Variations()['title'] ) ? $Post->Variations()['title'] : '' );
?>
<h5><?php echo __( 'post-variations' ) ?></h5>
<div class="mb-3">
	<div class="form-group">
		<small id="var-tab-tip" class="form-text text-muted"><?php echo __( 'variation-tab-tip' ) ?></small>
	</div>

	<?php if ( !$canVarFull ) : ?>
	
	<div id="varParentDiv" class="<?php echo ( $hasVariations ? 'd-none' : '' ) ?>">

		<div id="selectVarParent" class="form-group row">
			<label for="input-parent" class="form-label"><?php echo __( 'parent' ) ?></label>
			
			<div class="col-md-8">
				<select class="select2" data-placeholder="<?php echo __( 'choose-parent' ) ?>" id="postParentVar" style="width: 100%;"></select>
				<small id="var-parent-tip" class="form-text text-muted"><?php echo __( 'choose-variation-parent-tip' ) ?></small>
			</div>
		</div>
	
		<button id="newVariantButton" type="button" data-id="<?php echo $Post->PostID() ?>" data-toggle="tooltip" title="<?php echo __( 'add-new-variation-group' ) ?>" class="btn btn-primary float-right"><i class="fa fa-plus-circle"></i></button>
	</div>
	
	<div id="inputVarParent" class="form-group<?php echo ( !$hasVariations ? ' d-none' : '' ) ?>">
		<label for="input-parent" class="form-label"><?php echo __( 'parent-group' ) ?></label>
		
		<div class="input-group mb-3">
			<input placeholder="Group name" type="text" id="variationParentName" name="variationParent[title]" class="form-control" value="<?php echo $variationParentName ?>" />

			<span class="input-group-append">
				<button type="button" data-id="<?php echo $variationParentId ?>" id="removeParentGroupButton" class="btn btn-danger" data-toggle="tooltip" title="<?php echo __( 'delete' ) ?>"><?php echo __( 'delete' ) ?></button>
			</span>
		</div>
	</div>

	<div id="varHolder" class="form-group<?php echo ( $hasVariations ? '' : ' d-none' ) ?>">
	
		<label for="postVarTable" class="form-label"><?php echo __( 'variations' ) ?></label>
	
		<div class="table-responsive">				
			<table id="postVarTable" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="text-left" style="width: 24%;"><?php echo __( 'title' ) ?></th>
						<th class="text-center" style="width: 60%;"><?php echo __( 'product' ) ?></th>
						<th class="text-center" style="width: 10%;"><?php echo __( 'order' ) ?></th>
						<th class="text-center" style="width: 1px;"><?php echo __( 'actions' ) ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( !empty( $Post->Variations()['variations'] ) ) :
					foreach( $Post->Variations()['variations'] as $var ) : ?>
					<tr id="varField-row<?php echo $var['postId'] ?>">
						<td class="text-center"><input type="text" id="varTitle" name="variations[<?php echo $var['postId'] ?>][title]" class="form-control mb-4" placeholder="red, blue, 128GB, etc..." value="<?php echo $var['title'] ?>" /></td>
						<td class="text-center"><input type="text" id="varpostTitle" name="variations[<?php echo $var['postId'] ?>][postTitle]" class="form-control mb-4" value="<?php echo htmlspecialchars( $var['postTitle'] ) ?>" disabled /></td>
						<td class="text-center"><input type="number" id="varOrder" name="variations[<?php echo $var['postId'] ?>][order]" class="form-control mb-4" value="<?php echo $var['order'] ?>" min="0" /></td>
						<td class="text-center">
							<button type="button" id="removePostVarButton" title="<?php echo __( 'remove' ) ?>" data-id="<?php echo $var['postId'] ?>" class="btn btn-danger btn-flat btn-sm removePostVarButton" data-toggle="tooltip" title="<?php echo __( 'remove' ) ?>"><i class="fa fa-minus-circle"></i></button>
						</td>
					</tr>
					<?php endforeach ?>
				<?php endif ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6"></td>
						<td class="text-left"><button id="newVarButton" type="button" data-toggle="modal" data-target="#addNewPostVariation" title="<?php echo __( 'add-a-variation' ) ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
					</tr>
				</tfoot>
			</table>
			
			<input type="hidden" id="variationParentId" name="variationParent[id]" value="<?php echo $variationParentId ?>">
		</div>
	</div>

	<?php else : ?>
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
									<button type="button" id="editDealButton" data-toggle="modal" data-target="#editDealModal" data-id="<?php echo $deal['id_price'] ?>" class="btn btn-default btn-flat btn-xs mr-1" title="<?php echo __( 'edit' ) ?>"><i class="fa fa-cog"></i></button>
									<button type="button" id="removeDealButton" title="<?php echo __( 'remove' ) ?>" data-id="<?php echo $deal['id_price'] ?>" class="btn btn-danger btn-flat btn-xs" data-toggle="tooltip" title="<?php echo __( 'edit' ) ?>"><i class="fa fa-minus-circle"></i></button>
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
	<?php endif ?>
	
</div>