<!-- Modal for Add a price -->
<div id="modal-newPrice" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-focus-on="input:first">
<div class="modal-dialog modal centered" role="document" style="padding-right: 17px; display: block; width:100%;">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title"><?php echo __( 'add-new-price' ) ?></h5>
</div>
<div class="modal-body">
<div class="mb-3">
<label for="input-store" class="form-label"><?php echo __( 'choose-store' ) ?></label> <select class="form-control select2" data-placeholder="<?php echo __( 'choose-store' ) ?>" id="priceStore" style="width: 100%;"></select>
</div>
<div class="mb-3">
<label for="input-price" class="form-label"><?php echo __( 'price' ) ?></label> <input type="number" id="salePrice" name="salePrice" value="" placeholder="0" step="any" min="0" class="form-control" />	
</div>
<div class="mb-3">
<label for="input-currency" class="form-label"><?php echo __( 'currency' ) ?></label>
<select class="form-select select2" style="width: 100%;" id="priceCurrency" name="currency" aria-label="Currencies"><?php if ( !empty( $currencies ) ) : foreach( $currencies as $currId => $currency ) : ?><option value="<?php echo $currency['id'] ?>"><?php echo $currency['name'] ?> (<?php echo $currency['symbol'] ?>)</option><?php endforeach; endif; ?></select>
</div>

<div class="mb-3">
<label for="input-title" class="form-label"><?php echo __( 'title' ) ?></label> <input type="text" id="priceTitle" name="title" value="" placeholder="" class="form-control" />	<small id="input-title" class="form-text text-muted"><?php echo __( 'price-title-tip' ) ?></small>
</div>

<div class="mb-3">
<label for="link-text" class="form-label"><?php echo __( 'link-text' ) ?></label> <input type="text" id="linkTitle" name="link-text" value="" placeholder="View now at &quot;Store&quot;" class="form-control" />	<small id="link-title-tip" class="form-text text-muted"><?php echo __( 'link-text-tip' ) ?></small>
</div>

<div class="mb-3">
<label for="extra-text" class="form-label"><?php echo __( 'extra-text' ) ?></label> <input type="text" id="extraText" name="extra-text" value="" placeholder="&quot;Best Value&quot;" class="form-control" /> <small id="extra-title-tip" class="form-text text-muted"><?php echo __( 'extra-text-tip' ) ?></small>
</div>

<?php 
	$args = array(
		'id' => 'starting-price',
		'label' => __( 'starting-price' ),
		'name' => '',
		'checked' => false, 
		'disabled' => false,
		'tip' => __( 'starting-price-tip' )
	);

	CheckBox( $args );
?>

<!-- Description -->
<div class="mb-3">
	<label for="priceDescr" class="form-label"><?php echo __( 'description' ) ?></label><br />
	<button type="button" class="btn btn-default btn-flat btn-sm mr-1" onclick="addPriceImage(0,'pricePost');" id="priceImagePost"><i class="fa fa-image"></i> <?php echo __( 'add-media' ) ?></button><br />
	<?php echo $Editor->Init( null, '120px', 'priceDescr', false ) ?>
</div>

<div class="mb-3">
<label for="input-url" class="form-label"><?php echo __( 'product-url' ) ?></label> <input type="text" id="priceUrl" name="url" value="" placeholder="https://" class="form-control" />	<small id="input-url" class="form-text text-muted"><?php echo __( 'price-url-tip' ) ?></small>
</div>

<div class="mb-3">
<label for="input-aff-url" class="form-label"><?php echo __( 'affiliate-url' ) ?></label> <input type="text" id="priceAffUrl" name="aff-url" value="" placeholder="https://" class="form-control" /> <small id="input-aff-url" class="form-text text-muted"><?php echo __( 'price-aff-url-tip' ) ?></small>
</div>

<div id="modalD-error-detail"></div>

<input type="hidden" id="editPriceId" value="">

<div class="modal-footer">
	<div class="text-left"><button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button></div>
	<div class="text-right"><button id="insertNewPrice" type="button" class="btn btn-primary d-none"><?php echo __( 'add-price' ) ?></button><button id="editSinglePrice" type="submit" class="btn btn-primary d-none"><?php echo __( 'save' ) ?></button></div>
</div>
</div>
</div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function()
{
	//$("#priceImagePost").on("click", function(e)
	//{
	//	e.preventDefault();
	//});
	
	$("#newPriceButton").on("click", function(e)
	{
		e.preventDefault();
		
		//Clean the values
		cleanModalValues();
		
		//Show the button
		$("#insertNewPrice").removeClass("d-none");
	});
	
	$(".removePriceButton").unbind().click(function(e)
	{
		e.preventDefault();
			
		//Ask if they really want to remove this price
		if( !confirm("<?php echo __( 'are-you-sure' ) ?>"))
		{
			return false;
		}

		var pId = $(this).data('id');

		removePriceFromList(pId,false);
	});
	
	
	$("#editSinglePrice").unbind().click(function(e)
	{
		e.preventDefault();

		var url = "";

		var storeId 	= $('#priceStore option:selected').val();
		var storeName 	= $('#priceStore option:selected').text();
		var currId 		= $('#priceCurrency option:selected').val();
		var currName 	= $('#priceCurrency option:selected').text();
		var inputTitle 	= $('#priceTitle').val();
		var salePrice 	= $('#salePrice').val();
		var priceUrl 	= $('#priceUrl').val();
		var priceAffUrl = $('#priceAffUrl').val();
		var priceDescr  = editorGetContent('price');
		var prId		= $("#editPriceId").val();
		var postData = [];

		postData = [
			{ prId 				: prId },
			{ storeId 			: storeId },
			{ currId 			: currId },
			{ inputTitle 		: inputTitle },
			{ priceUrl			: priceUrl },
			{ storeName			: storeName },
			{ currName			: currName },
			{ salePrice			: salePrice },
			{ priceDescr		: priceDescr },
			{ linkTitle			: $('#linkTitle').val() },
			{ extraText			: $('#extraText').val() },
			{ startingPrice		: $('#starting-price').is(':checked') },
			{ priceAffUrl 		: priceAffUrl }
		];

		if(typeof(storeId) == "undefined" || ( storeId === "" ) || ( storeId === 0 ) )
		{
			$("#modal-newPrice").modal("hide");
			return;
		}
	
		$.ajax(
		{
			url: '<?php echo AJAX_ADMIN_PATH ?>edit-price/',
			type: 'POST',
			data: {arr:postData},
			dataType: 'json',
			cache: false
		})
		.done(function(data)
		{
			if (data.status == 'ok') 
			{
				addCode(prId,data.url,data.price,data.curr,data.date,data.updated,false);
			}
			
			else if (data.status == 'error') 
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'price-edit-error' ) ?>"
				})
			}

			$('#modalP-loader').hide();
		})
		.fail(function(){
			$('#modalP-post-detail').html('<?php echo __( 'an-error-happened' ) ?>');
			$('#modalP-loader').hide();
		});
		
		//Close the modal
		$('#modal-newPrice').removeClass("in");
		$('#modal-newPrice').modal("hide");
	});
	
	$(".editPriceButton").unbind().click(function(e)
	{
		e.preventDefault();
		
		var pId = $(this).data('id');
		
		editSinglePrice(pId);
	});
	
	/*   */
	$('#priceCurrency').select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		dropdownParent: $("#modal-newPrice"),
		minimumInputLength: 2
	});
		
	/*   */
	var priceStore = $("#priceStore").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		dropdownParent: $('#modal-newPrice'),
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

$("#insertNewPrice").unbind().click(function(e) {
	e.preventDefault();

	var url = "";
	
	var date 		= '<?php echo postDate( time() ) ?>';
	var postId 		= '<?php echo (int) Router::GetVariable( 'key' ) ?>';
	var siteId 		= '<?php echo $Post->Site()->id ?>';
	var userId 		= '<?php echo $Admin->UserID() ?>';
	var storeId 	= $('#priceStore option:selected').val();
	var storeName 	= $('#priceStore option:selected').text();
	var currId 		= $('#priceCurrency option:selected').val();
	var currName 	= $('#priceCurrency option:selected').text();
	var inputTitle 	= $('#priceTitle').val();
	var salePrice 	= $('#salePrice').val();
	var priceUrl 	= $('#priceUrl').val();
	var priceAffUrl = $('#priceAffUrl').val();
	var priceDescr  = editorGetContent('price');
	
	var postData = [];

	postData = [
		{ postId 			: postId },
		{ storeId 			: storeId },
		{ siteId 			: siteId },
		{ userId 			: userId },
		{ currId 			: currId },
		{ inputTitle 		: inputTitle },
		{ priceUrl			: priceUrl },
		{ salePrice			: salePrice },
		{ priceDescr		: priceDescr },
		{ linkTitle			: $('#linkTitle').val() },
		{ extraText			: $('#extraText').val() },
		{ startingPrice		: $('#starting-price').is(':checked') },
		{ priceAffUrl 		: priceAffUrl }
	];

	if(typeof(priceUrl) != "undefined" && priceUrl !== "")
	{
		url += '<a href=\"' + priceUrl + '\" target=\"_blank\" rel=\"noopener noreferrer\">' + storeName + '</a>';
	}
	else
	{
		url += storeName;
	}

	if(typeof(storeId) == "undefined" || ( storeId === "" ) || ( storeId === 0 ) )
	{
		$("#modal-newPrice").modal("hide");
		return;
	}
	
	$.ajax(
	{
		url: '<?php echo AJAX_ADMIN_PATH ?>add-new-price/',
		type: 'POST',
		data: {arr:postData},
		dataType: 'json',
		cache: false
	})
	.done(function(data)
	{
		if (data.status == 'ok') 
		{
			addCode(data.id,url,salePrice,currName,date,null,true);
		}
		
		else if (data.status == 'exists') 
		{
			Toast.fire({
				icon: "error",
				title: "<?php echo __( 'price-already-exists-error' ) ?>"
			})
		}
		
		else if (data.status == 'error') 
		{
			Toast.fire({
				icon: "error",
				title: "<?php echo __( 'price-add-error' ) ?>"
			})
		}
		
		$('#modalP-post-detail').html('');    
		$('#modalP-post-detail').html(data);
		$('#modalP-loader').hide();
	})
	.fail(function(){
		$('#modalP-post-detail').html('<?php echo __( 'an-error-happened' ) ?>');
		$('#modalP-loader').hide();
	});
		
	//Close the modal
	$('#modal-newPrice').removeClass("in");
	$('#modal-newPrice').modal("hide");
});
</script>

<script type="text/javascript">

function addPriceImage(id,called)
{
	fileManagerOpen();

	var post = '<?php echo Router::GetVariable( 'key' ) ?>';
	var action = id;
	var calledFrom = called;
	var token = '<?php echo $Admin->GetToken() ?>';
	$('#post-detail').html(''); 
	$('#modal-loader').show();  

	$.ajax(
	{
		url: '<?php echo AJAX_ADMIN_PATH ?>media-manager-gallery/',
		type: 'POST',
		data: {action:action,post:post,token:token,calledFrom:calledFrom},
		dataType: 'html',
		cache: false
	})
	.done(function(data)
	{
		var html = data;
		
		if ( called == 'priceCover' )
		{
			html += '<input type="hidden" id="priceMediaFile" value="' + id + '">';
		}
		
		$('#post-detail').html('');    
		$('#post-detail').html(html);
		$('#modal-loader').hide();
	 })
	 .fail(function(){
		$('#post-detail').html('Error. Please try again...');
		$('#modal-loader').hide();
	});
}

function loadPriceDetails(id)
{
	$.ajax(
	{
		url: '<?php echo AJAX_ADMIN_PATH ?>get-single-price/',
		type: 'POST',
		data: {id:id},
		dataType: 'json',
		cache: false
	})
	.done(function(data)
	{
		if (data.status == 'ok') 
		{
			priceDescr.value(data.data.content);

			$('#priceCurrency').val(data.data.id_currency).trigger('change');
			$('#salePrice').val(data.data.sale_price);
			$('#priceTitle').val(data.data.title);
			$('#linkTitle').val(data.data.link_text);
			$('#extraText').val(data.data.extra_text);
			$('#priceUrl').val(data.data.main_page_url);
			$('#priceAffUrl').val(data.data.aff_page_url);
				
			var $newOption = $("<option selected='selected'></option>").val(data.data.id_store).text(data.data.st);

			$("#priceStore").append($newOption).trigger("change");
				
			if ( data.data.is_starting_price == 1 )
			{
				$("#starting-price").attr("checked", true);
			}
			else
			{
				$("#starting-price").attr("checked", false);
			}
		}
			
		else if (data.status == 'error') 
		{
			Toast.fire({
				icon: "error",
				title: "<?php echo __( 'price-add-error' ) ?>"
			})
				
			$("#modal-newPrice").modal("hide");
		}
	})
	.fail(function(){
		Toast.fire({
			icon: "error",
			title: "<?php echo __( 'price-add-error' ) ?>"
		})
			
		$("#modal-newPrice").modal("hide");
	});
}

function editSinglePrice(id)
{
	//Clean the values
	cleanModalValues();
		
	//Show the button
	$("#editSinglePrice").removeClass("d-none");

	//Bind the id to the modal's button
	$("#editPriceId").val(id);		

	//Bind the price details
	loadPriceDetails(id);
		
	//Show the modal
	$("#modal-newPrice").modal("show");		
}

function cleanModalValues()
{
	//Hide the insert button
	$("#insertNewPrice").addClass("d-none");

	//Hide the insert button
	$("#editSinglePrice").addClass("d-none");
	
	$('#priceStore').val(null).trigger('change');
	$('#priceTitle').val('');
	$('#priceAffUrl').val('');
	$('#priceUrl').val('');
	$('#linkTitle').val('');
	$('#extraText').val('');
	$('#salePrice').val('0');
	$('#modalD-error-detail').append("");
	priceDescr.value("");
	$("#starting-price").attr("checked", false);
}

function addCode(id,url,pr,curr,date,updated,isnew)
{
	var html = "";
	var temp = "";
	
	html += '<tr id=\"priceField-row' + id + '\">';
	
	temp += '  <td class=\"text-start\">' + url + '</td>';
	temp += '  <td class=\"text-end\">' + pr + ' (' + curr + ')</td>';
	temp += '  <td class=\"text-end\">' + date + '</td>';
	
	if ( isnew )
	{
		temp += '  <td class=\"text-end\">---</td>';
	}
	else
	{
		temp += '  <td class=\"text-end\">' + updated + '</td>';
	}
	
	temp += '  <td class=\"table-action\"><div class=\"btn-group\"><button type=\"button\" onclick=\"editSinglePrice(' + id + ');\" class="btn btn-default btn-flat btn-xs mr-1 editPriceButton" data-toggle=\"tooltip\" title=\"<?php echo __( 'edit' ) ?>\"><i class=\"fa fa-cog\"></i></button><button type=\"button\" onclick=\"addPriceImage(\' + id + \',\'priceCover\');\" class=\"btn btn-default btn-flat btn-xs mr-1\" data-toggle=\"tooltip\" title=\"<?php echo __( 'cover-image' ) ?>\"><i class=\"fa fa-image\"></i></button><button type=\"button\" onclick=\"removePriceFromList(' + id + ',true);\" data-toggle=\"tooltip\" title=\"<?php echo __( 'remove' ) ?>\" data-id=\"' + id + '\" class=\"btn btn-danger btn-flat btn-xs\" data-toggle=\"tooltip\" title=\"<?php echo __( 'edit' ) ?>\"><i class=\"fa fa-minus-circle\"></i></button></div></td>';
	
	html += temp;
	
	html += '</tr>';
	
	if ( isnew )
	{
		$('#postPricesTable tbody').append(html);
	}
	else
	{
		var field = 'priceField-row' + id;
		$('#postPricesTable #' + field).html(temp);
	}
	
	console.log(html);
	
	//Clean some values but keep the currency
	cleanModalValues();
}

function removePriceFromList(id,button)
{
	if ( button )
	{	//Ask if they really want to remove this price
		if( !confirm("<?php echo __( 'are-you-sure' ) ?>"))
		{
			return false;
		}
	}
	$.ajax(
	{
		url: '<?php echo AJAX_ADMIN_PATH ?>remove-single-price/',
		type: 'POST',
		data: {pid:id},
		dataType: 'json',
		cache: false
	})
	.done(function(data)
	{
		if (data.status == 'ok') 
		{
			$('#priceField-row' + id).remove();
		}
			
		else if (data.status == 'error') 
		{
			Toast.fire({
				icon: "error",
				title: "<?php echo __( 'an-error-happened' ) ?>"
			})
		}
	})
	.fail(function(){
		Toast.fire({
			icon: "error",
			title: "<?php echo __( 'an-error-happened' ) ?>"
		})
	});
}
</script>