<?php

$stores = Stores( $Price['id_site'], false );
$currencies = Currencies( $Price['id_site'], false );

$string = '
<form>
	<div class="form-group">
		<label for="input-store" class="form-label required">' . __( 'choose-store' ) . '</label>
		<select class="form-select select2" id="editPriceStore" data-placeholder="' . __( 'choose-store' ) . '"style="width: 100%;">';
		
		if ( !empty( $stores ) )
		{
			foreach( $stores as $sId => $store )
			{
				$string .= '<option value="' . $store['id_store'] . '"' . ( ( $Price['id_store'] == $store['id_store'] ) ? ' selected' : '' ) . '>' . $store['name'] . '</option>';
			}
		}
		
		$string .= '</select>
	</div>

	<div class="form-group">
		<label for="input-price" class="form-label">' . __( 'price' ) . '</label>
		<input type="number" name="salePrice" id="editSalePrice" value="' . $Price['sale_price'] . '" step="any" placeholder="0" min="0" class="form-control" />	
	</div>
	
	<div class="form-group">
		<label for="input-currency" class="form-label">' . __( 'currency' ) . '</label>
		<select class="form-select select2" id="editPriceCurrency" style="width: 100%;" name="currency" aria-label="Currencies">';
		
		if ( !empty( $currencies ) ) 
		{
			foreach( $currencies as $currId => $currency )
			{
				$string .= '<option value="' . $currency['id'] . '"' . ( ( $Price['id_currency'] == $currency['id'] ) ? ' selected' : '' ) . '>' . $currency['name'] . '(' . $currency['symbol'] . ')</option>';
			}
		}
		
		$string .= '</select>
	</div>
	
	<div class="form-group">
		<label for="input-title" class="form-label">' . __( 'title' ) . '</label>
		<input type="text" name="title" id="editPriceTitle" value="' . StripContent( $Price['title'] ) . '" placeholder="" class="form-control" />
		<small class="form-text text-muted">' . __( 'price-title-tip' ) . '</small>
	</div>
	
	<div class="form-group">
		<label for="link-text" class="form-label">' . __( 'link-text' ) . '</label>
		<input type="text" id="editLinkTitle" name="link-text" value="' . StripContent( $Price['link_text'] ) . '" placeholder="View now at &quot;Store&quot;" class="form-control" />
		<small class="form-text text-muted">' . __( 'link-text-tip' ) . '</small>
	</div>
	
	<div class="form-group">
		<label for="extra-text" class="form-label">' . __( 'extra-text' ) . '</label>
		<input type="text" id="editExtraText" name="extra-text" value="' . StripContent( $Price['extra_text'] ) . '" placeholder="&quot;Best Value&quot;" class="form-control" />
		<small class="form-text text-muted">' . __( 'extra-text-tip' ) . '</small>
	</div>';
	
	$args = array(
		'id' => 'starting-priceEdit',
		'label' => __( 'starting-price' ),
		'name' => '',
		'checked' => ( !empty( $Price['is_starting_price'] ) ? true : false ), 
		'disabled' => false,
		'tip' => __( 'starting-price-tip' )
	);

	$string .= CheckBox( $args, false );
	
	$string .= '
	<div class="form-group">
		<label for="description" class="form-label">' . __( 'description' ) . '</label>
		' . Editor( StripContent( $Price['content'] ), '120px', 'priceDescr', false ) . '
	</div>
	
	<div class="form-group">
		<label for="input-product-url" class="form-label">' . __( 'product-url' ) . '</label>
		<input type="text" name="url" id="editPriceUrl" value="' . StripContent( $Price['main_page_url'] ) . '" placeholder="https://" class="form-control" />
		<small class="form-text text-muted">' . __( 'price-url-tip' ) . '</small>
	</div>
	
	<div class="form-group">
		<label for="input-affiliate-url" class="form-label">' . __( 'affiliate-url' ) . '</label>
		<input type="text" name="url" id="editPriceAffUrl" value="' . StripContent( $Price['aff_page_url'] ) . '" placeholder="https://" class="form-control" />
		<small class="form-text text-muted">' . __( 'price-aff-url-tip' ) . '</small>
	</div>
</form>

<script type="text/javascript">
$("#editSinglePrice").unbind().click(function(e) {
	e.preventDefault();
	
	var postData = [];
	
	var pId = $(this).data("id");
	var priceDescr  = editorGetContent("priceDescr");
a;ert(pId);return;
	postData = [
		{ pId 			: pId },
		{ storeId 		: $("#priceStoreModal option:selected").val() },
		{ currId 	 	: $("#priceCurrencyModal option:selected").val() },
		{ inputTitle 	: $("#priceTitleModal").val() },
		{ salePrice 	: $("#salePriceModal").val() },
		{ priceUrl		: $("#priceUrlModal").val() },
		{ priceAffUrl 	: $("#priceAffUrlModal").val() },
		{ priceDescr	: priceDescr },
		{ linkTitle		: $("#linkTitle").val() },
		{ extraText		: $("#extraText").val() },
		{ startingPrice	: $("#starting-price").is(":checked") }
	];
	
	if(typeof(pId) == "undefined" || ( pId === "" ) || ( pId === 0 ) )
	{
		alert("' . __( 'data-update-error' ) . '");
		$("#editPriceModal").modal("hide");
		return;
	}
	
	$.ajax({
		url: "' . AJAX_ADMIN_PATH . 'edit-single-price/",
		type: "post",
		dataType: "json",
		data: {arr:postData},
		success: function(json) {
			if (json["error"]) {
				alert(json["error"]);
			}

			if (json["success"])
			{
				var field = \'priceField-row\' + pId;
				var vars = "";

				vars += \'  <td class=\"text-start\">\' + json[\'url\'] + \'</td>\';
				vars += \'  <td class=\"text-end\">\' + json[\'price\'] + \'</td>\';
				vars += \'  <td class=\"text-end\">\' + json[\'date\'] + \'</td>\';
				vars += \'  <td class=\"text-end\">\' + json[\'updated\'] + \'</td>\';
				vars += \'  <td class=\"table-action\"><div class=\"btn-group\"><button type=\"button\" id=\"editPriceButton\" data-toggle=\"modal\" data-target=\"#editPriceModal\" data-id=\"\' + pId + \'\" class="btn btn-default btn-flat btn-xs mr-1 editPriceButton" data-toggle=\"tooltip\" title=\"' . __( 'edit' ) . '\"><i class=\"fa fa-cog\"></i></button><button type=\"button\" id=\"removePriceButton\" onclick=\"removePriceFromList(\' + pId + \');\" data-toggle=\"tooltip\" title=\"' . __( 'remove' ) . '\" data-id=\"\' + pId + \'\" class=\"btn btn-danger btn-flat btn-xs\" data-toggle=\"tooltip\" title=\"' . __( 'edit' ) . '\"><i class=\"fa fa-minus-circle\"></i></button></div></td>\';

				$(\'#postPricesTable #\' + field).html(vars);
				$("#editPriceModal").modal("hide");
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
</script>

<script type="text/javascript">
var editPriceStore = $("#editPriceStore").select2({
	placeholder: "",
	allowClear: true,
	theme: "bootstrap4",
	dropdownParent: $("#editPriceModal"),
	minimumInputLength: 2,
	ajax: {
		type: "POST",
		url: "' . AJAX_ADMIN_PATH . 'get-stores/",
		data: function (params) {
			var query = {
				postSite: "' . $Price['id_site'] . '",
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

var editPriceCurrency = $("#editPriceCurrency").select2({
	placeholder: "",
	allowClear: true,
	theme: "bootstrap4",
	dropdownParent: $("#editPriceModal"),
	minimumInputLength: 2
});</script>';

unset( $stores, $currencies, $Price );