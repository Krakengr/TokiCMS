<!-- Modal for Add a deal -->
<div id="modal-newDeal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal centered" role="document" style="padding-right: 17px; display: block; width:100%;">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title"><?php echo __( 'add-new-deal-coupon' ) ?></h5>
</div>
<div class="modal-body">
<div class="mb-3">
	<label for="couponType"><?php echo __( 'coupon-type' ) ?></label>
	<select name="deal[couponType]" id="couponTypeModal" class="form-control selectpicker">
		<option value="coupon"><?php echo __( 'coupon' ) ?></option>
		<option value="deal"><?php echo __( 'deal' ) ?></option>
		<!--<option value="image"><?php echo __( 'image' ) ?></option>-->
	</select>
	<small id="couponType" class="form-text text-muted"><?php echo __( 'coupon-type-tip' ) ?></strong></small>
</div>
<div class="mb-3">
	<label for="input-store" class="form-label"><?php echo __( 'choose-store' ) ?></label>
	<select class="form-control select2" data-placeholder="<?php echo __( 'choose-store' ) ?>" id="priceStoreModal" style="width: 100%;"></select>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="mb-3">
			<label><?php echo __( 'date' ) ?></label>
			<input type="text" name="deal[date]" class="form-control postDatepicker" value="<?php echo date( 'm/d/Y', time() ) ?>" id="dealDateModal" placeholder="mm/dd/Y">
			<small id="couponDate" class="form-text text-muted"><?php echo __( 'deal-available-date-tip' ) ?></strong></small>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="mb-3">
			<label><?php echo __( 'expiration-date' ) ?></label>
			<input type="text" name="deal[expire]" class="form-control postDatepicker" value="" id="dealExpModal" placeholder="mm/dd/Y">
			<small id="couponExpirationDate" class="form-text text-muted"><?php echo __( 'deal-expiration-date-tip' ) ?></strong></small>
		</div>
	</div>
</div>
<div class="mb-3">
	<label for="input-price" class="form-label"><?php echo __( 'price' ) ?></label>
	<input type="number" id="salePriceModal" name="salePrice" value="0" placeholder="0" step="any" min="0" class="form-control" />
	<small id="couponPrice" class="form-text text-muted"><?php echo __( 'deal-free-price-tip' ) ?></small>
</div>
<div class="mb-3">
	<label for="input-currency" class="form-label"><?php echo __( 'currency' ) ?></label>
	<select class="form-select select2" style="width: 100%;" id="priceCurrencyModal" name="currency" aria-label="Currencies"><?php if ( !empty( $currencies ) ) : foreach( $currencies as $currId => $currency ) : ?><option value="<?php echo $currency['id'] ?>"><?php echo $currency['name'] ?> (<?php echo $currency['symbol'] ?>)</option><?php endforeach; endif; ?></select>
</div>
<div class="mb-3">
	<label for="input-discount-amount" class="form-label"><?php echo __( 'discount-amount-text' ) ?></label> <input type="text" id="dealDiscountTextModal" name="discount-amount" value="" placeholder="60% off" class="form-control" />
	<small id="input-discount-amount" class="form-text text-muted"><?php echo __( 'discount-amount-text-tip' ) ?></small>
</div>
<div class="mb-3">
	<label for="input-coupon" class="form-label"><?php echo __( 'coupon-code' ) ?></label>
	<input type="text" id="couponCodeModal" name="couponCode" value="" placeholder="" class="form-control" />
	<small id="input-coupon" class="form-text text-muted"><?php echo __( 'coupon-code-tip' ) ?></small>
</div>
<div class="mb-3">
	<label for="input-title" class="form-label"><?php echo __( 'title' ) ?></label>
	<input type="text" id="priceTitleModal" name="title" value="" placeholder="" class="form-control" />
	<small id="input-title" class="form-text text-muted"><?php echo __( 'price-title-tip' ) ?></small>
</div>
<div class="mb-3">
	<label for="input-description" class="form-label"><?php echo __( 'description' ) ?></label>
	<textarea id="dealDescriptionModal" name="discount-description" class="form-control" rows="5" cols="33"></textarea>
	<small id="input-description" class="form-text text-muted"><?php echo __( 'discount-description-tip' ) ?></small>
</div>
<div class="mb-3">
	<label for="input-url" class="form-label"><?php echo __( 'product-url' ) ?></label>
	<input type="text" id="dealUrlModal" name="url" value="" placeholder="https://" class="form-control" />
	<small id="input-url" class="form-text text-muted"><?php echo __( 'price-url-tip' ) ?></small>
</div>
<div class="mb-3">
	<label for="input-aff-url" class="form-label"><?php echo __( 'affiliate-url' ) ?></label>
	<input type="text" id="dealAffUrlModal" name="aff-url" value="" placeholder="https://" class="form-control" />
	<small id="input-aff-url" class="form-text text-muted"><?php echo __( 'price-aff-url-tip' ) ?></small>
</div>

<div id="modalD-error-detail"></div>

<div class="modal-footer">
	<div class="text-left"><button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button></div>
	<div class="text-right"><button id="insertNewDeal" type="button" class="btn btn-primary"><?php echo __( 'add-deal-coupon' ) ?></button></div>
</div>
</div>
</div>
</div>
</div>

<!-- Modal for edit a deal/coupon -->
<div class="modal fade" id="editDealModal" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo __( 'edit-price-details' ) ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-success d-none success"></div>
				<div class="alert alert-danger d-none error"></div>
				<div id="modalP-loader" style="text-align: center;">
					<img src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/loading.gif">
				</div>  
				<div id="modalP-post-detail"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button>
				<button id="editSingleDeal" type="submit" class="btn btn-primary" data-id=""><?php echo __( 'save' ) ?></button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript"><!--
$("#editSingleDeal").unbind().click(function(e) {
	e.preventDefault();
	
	var pId = $(this).data('id');
	
	var storeId = $('#priceStoreModal option:selected').val();
	var currId = $('#priceCurrencyModal option:selected').val();
	var type = $('#couponTypeModal option:selected').val();
	var date = $('#dealDateModal').val();
	var exp = $('#dealExpModal').val();
	var amount = $('#dealDiscountTextModal').val();
	var descr = $('#dealDescriptionModal').val();
	var inputTitle = $('#priceTitleModal').val();
	var couponCode = $('#couponCodeModal').val();
	var salePrice = $('#salePriceModal').val();
	var dealUrl = $('#dealUrlModal').val();
	var dealAffUrl = $('#dealAffUrlModal').val();
	
	if(typeof(pId) == "undefined" || ( pId === "" ) || ( pId === 0 ) )
	{
		alert("<?php echo __( 'data-update-error' ) ?>");
		$("#editDealModal").modal("hide");
		return;
	}
	
	$.ajax({
		url: '<?php echo AJAX_ADMIN_PATH ?>edit-single-deal/',
		type: 'post',
		dataType: 'json',
		data: {id:pId,store:storeId,curr:currId,title:inputTitle,price:salePrice,url:dealUrl,aff:dealAffUrl},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}

			if (json['success'])
			{
				var field = 'priceField-row' + pId;
				var vars = '';

				vars += '  <td class=\"text-start\">' + json['url'] + '</td>';
				vars += '  <td class=\"text-end\">' + json['price'] + '</td>';
				vars += '  <td class=\"text-end\">' + json['couponCode'] + '</td>';
				vars += '  <td class=\"text-end\">' + json['date'] + '</td>';
				vars += '  <td class=\"text-end\">' + json['updated'] + '</td>';
				vars += '  <td class=\"table-action\"><div class=\"btn-group\"><button type=\"button\" id=\"editPriceButton\" data-toggle=\"modal\" data-target=\"#editDealModal\" data-id=\"' + pId + '\" class="btn btn-default btn-flat btn-xs mr-1" data-toggle=\"tooltip\" title=\"<?php echo __( 'edit' ) ?>\"><i class=\"fa fa-cog\"></i></button><button type=\"button\" id=\"removePriceButton\" onclick=\"removePriceFromList(' + pId + ');\" data-toggle=\"tooltip\" title=\"<?php echo __( 'remove' ) ?>\" data-id=\"' + pId + '\" class=\"btn btn-danger btn-flat btn-xs\" data-toggle=\"tooltip\" title=\"<?php echo __( 'edit' ) ?>\"><i class=\"fa fa-minus-circle\"></i></button></div></td>';

				$('#postPricesTable #' + field).html(vars);
				$("#editDealModal").modal("hide");
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

//Insert New Deal
$("#insertNewDeal").unbind().click(function(e) {
	e.preventDefault();
	
	var html = "";
	var url = "";
	var amount = "";

	var storeId = $('#priceStoreModal option:selected').val();
	var currId = $('#priceCurrencyModal option:selected').val();
	var type = $('#couponTypeModal option:selected').val();
	var storeName = $('#priceStoreModal option:selected').text();
	var date = $('#dealDateModal').val();
	var exp = $('#dealExpModal').val();
	var amountTxt = $('#dealDiscountTextModal').val();
	var descr = $('#dealDescriptionModal').val();
	var inputTitle = $('#priceTitleModal').val();
	var couponCode = $('#couponCodeModal').val();
	var salePrice = $('#salePriceModal').val();
	var dealUrl = $('#dealUrlModal').val();
	var dealAffUrl = $('#dealAffUrlModal').val();
	var lastDealId = $('#lastDealId').val();
	var currName = $('#priceCurrencyModal option:selected').text();
	
	if(typeof(dealUrl) != "undefined" && dealUrl !== "")
	{
		url += '<a href=\"' + dealUrl + '\" target=\"_blank\" rel=\"noopener noreferrer\">' + storeName + '</a>';
	}
	else
	{
		url += storeName;
	}
	
	if(typeof(amountTxt) != "undefined" && amountTxt !== "")
	{
		amount += " (" + amountTxt + ")";
	}

	if(typeof(storeId) == "undefined" || ( storeId === "" ) || ( storeId === 0 ) )
	{
		//$("#modal-newDeal").modal("hide");
		var errorMessage = "<div class=\"alert alert-info alert-dismissible\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><?php echo __( 'select-store-error-message' ) ?></div>";
		$('#modalD-error-detail').append(errorMessage);
		return;
	}
	
	lastDealId++;
	
	html += '<tr id=\"dealField-row' + lastDealId + '\">';
	html += '  <td class=\"text-start\">' + url + '</td>';
	html += '  <td class=\"text-end\">' + salePrice + amount + ' ' + currName + '</td>';
	html += '  <td class=\"text-end\">' + couponCode + '</td>';
    html += '  <td class=\"text-end\">' + date + '</td>';
	html += '  <td class=\"text-end\">' + exp + '</td>';
	html += '  <td class=\"table-action\"><button type=\"button\" onclick=\"$(\'#dealField-row' + lastDealId + '\').remove();\" data-toggle=\"tooltip\" title=\"<?php echo __( 'remove' ) ?>\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';
	
	//Add a few hidden fields needed for adding the deal
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][storeId]\" value=\"' + storeId + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][salePrice]\" value=\"' + salePrice + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][currId]\" value=\"' + currId + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][dealUrl]\" value=\"' + dealUrl + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][dealAffUrl]\" value=\"' + dealAffUrl + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][priceTitle]\" value=\"' + inputTitle + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][amountTxt]\" value=\"' + amountTxt + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][descr]\" value=\"' + descr + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][type]\" value=\"' + type + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][couponCode]\" value=\"' + couponCode + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][exp]\" value=\"' + exp + '\">';
	html += '<input type=\"hidden\" name=\"dealList[' + lastDealId + '][date]\" value=\"' + date + '\">';
	
	$('#postDealsTable tbody').append(html);
	$('#lastDealId').val(lastDealId);

	confirmChange = true;
	
	//Clean some values but keep the currency
	$('#priceStoreModal').val(null).trigger('change');
	$('#priceTitleModal').val('');
	$('#couponCodeModal').val('');
	$('#dealDescriptionModal').val('');
	$('#dealAffUrlModal').val('');
	$('#dealUrlModal').val('');
	$('#salePrice').val('0');
	$('#modalD-error-detail').empty();
	
	//Close the modal
	$('#modal-newDeal').removeClass("in");
	$('#modal-newDeal').modal("hide");
});
--></script>

<script type="text/javascript">
	function removePriceFromList(id)
	{
		$('#dealField-row' + id).remove();

		confirmChange = true;
	}

	$(document).ready(function() {
		
		$("#removeDealButton").on("click", function(e)
		{
			e.preventDefault();
			var pId = $(this).data('id');

			$('#dealField-row' + pId).remove();
				
			confirmChange = true;
		});
		
		//Clear the error message
		$("#newDealButton").on("click", function(e)
		{
			e.preventDefault();
			$('#modalD-error-detail').empty();
		});
	
		$("#editDealButton").on("click", function(e)
		{
			e.preventDefault();
			var pId = $(this).data('id');
			
			//Bind the id to the modal's button
			$('#editSinglePrice').data('id', pId);
			
			$('#modalP-post-detail').html(''); 
			$('#modalP-loader').show();  
			$.ajax(
			{
				url: '<?php echo AJAX_ADMIN_PATH ?>get-single-price/',
				type: 'POST',
				data: {id:pId},
				dataType: 'html',
				cache: false
			})
			.done(function(data)
			{
				console.log(data);	
				$('#modalP-post-detail').html('');    
				$('#modalP-post-detail').html(data);
				$('#modalP-loader').hide();
			 })
			 .fail(function(){
				$('#modalP-post-detail').html('<?php echo __( 'an-error-happened' ) ?>');
				$('#modalP-loader').hide();
			});
		});
		
		/*   */
		$('#priceCurrencyModal').select2({
			dropdownParent: $('#modal-newDeal')
		});
		
		/*   */
		var store = $("#priceStoreModal").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			dropdownParent: $('#modal-newDeal > div > div'),
			minimumInputLength: 2,
			ajax: {
					type: "POST",
					url: "<?php echo AJAX_ADMIN_PATH ?>get-stores/",
					data: function (params) {
						var query = {
							postSite: "<?php echo $Post->Site()->id ?>",
							query: params.term
						}
						return query;
					},
					processResults: function (data) {
						return data;
					}
				},
			escapeMarkup: function(markup) {
				return markup;
			},
			templateResult: function(data) {
				var html = data.text;
				return html;
			}
		});
	});
</script>