<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/bs-stepper/bs-stepper.min.js"></script>

<?php

$customHtml = '<select id="customFieldSelect" class="form-select shadow-none" style="width: 100%; height:36px;" name="custom_fields[\' + custom_row + \']" aria-label="' . __( 'custom-fields' ) . '">';

$customHtml .= '<optgroup label=" ' . __( 'predefined-keys' ) . '"><option value="time_added">' . __( 'time-added' ) . '</option><option value="time_updated">' . __( 'time-updated' ) . '</option><option value="subtitle">' . __( 'subtitle' ) . '</option><option value="price">' . __( 'price' ) . '</option></optgroup>';

$atrrs = AdminGetAllAttributes( $Admin->GetSite() );

if ( !empty( $atrrs ) ) 
{
	$customHtml .= '<optgroup label=" ' . __( 'post-attributes' ) . '">';
	
	foreach ( $atrrs as $atrr ) 
	{
		$customHtml .= '<option value="att::' . $atrr['id'] . '">' . $atrr['name'] . ' (' . $atrr['gn'] . ')</option>';
	}
	
	$customHtml .= '</optgroup>';
}

$customHtml .= '</select>';

?>

<script type='text/javascript'>
var errors = 0;

document.addEventListener('DOMContentLoaded', function () {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
})

function loadXmlItems(file)
{
	$("#importContent").addClass("d-none");
	$("#importBar").removeClass("d-none");

	var errors = 0;
	
	$.ajax(
	{
		url: "<?php echo AJAX_ADMIN_PATH ?>import-content-xml/",
		data: {fileUrl:file},
		cache: false,
		type: 'post',
		dataType: 'json'
	})
	.done(function(data)
	{
		if (data.status=='ok') 
		{
			var percentComplete = Math.ceil( ( data.currentItems / data.totalItems ) * 100 );
			
			$("#importBar").children(".progress-bar").width(percentComplete + "%");

			$("#post-status").append(data.message);
			console.log(data.message);
			if (data.totalItems > data.currentItems ) 
			{
				loadXmlItems(file);
			}
			else
			{
				done(false);
				confirmChange = false;
			}
		}
		else 
		{
			if (data.message )
			{
				console.log(data.message);
				$("#post-status").append(data.message);
			}
			else
			{
				console.log("Error...");
			}
			
			done(true);
			confirmChange = false;
		}
	})
	.fail(function(){
		$("#importBar").addClass("d-none");
		$("#post-error").removeClass("d-none");
		$("#importContent").removeClass("d-none");
		confirmChange = false;
	});
}

function addCustomField() {
	var custom_row = 0;
	
	html  = '<tr id="customField-row' + custom_row + '">';
	html += '  <td class="text-right"><?php echo $customHtml ?></td>';
    html += '  <td class="text-right"><input type="text" name="custom_values[' + custom_row + ']" value="" placeholder="<span class=\'product-price-total\'>\d+\,?\d*</span>" class="form-control" /><input type="text" name="price_values[' + custom_row + ']" value="" placeholder="price id" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#customField-row' + custom_row + '\').remove();" data-toggle="tooltip" title="<?php echo __( 'remove' ) ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';

	$('#customFieldsTable tbody').append(html);
	
	custom_row++;
}

function addCustomReplaceField() {
	var custom_row = 0;
	
	html  = '<tr id="findReplaceField-row' + custom_row + '">';
	html += '  <td class="text-right"><input type="text" name="find_fields[' + custom_row + ']" value="" placeholder="eg. find" class="form-control" /></td>';
    html += '  <td class="text-right"><input type="text" name="find_values[' + custom_row + ']" value="" placeholder="eg. replace" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#findReplaceField-row' + custom_row + '\').remove();" data-toggle="tooltip" title="<?php echo __( 'remove' ) ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';

	$('#findReplaceFieldsTable tbody').append(html);
	
	custom_row++;
}

$(document).ready(function()
{
	<?php if ( $Admin->MultiBlog() ) : ?>
	//Check for changes in blog selection
	$("#blogSelect").change(function()
	{
		var id = $(this).val();

		id = id.trim();
		
		if ( ( id !== "" ) && ( id > 0 ) )
		{
			$("#category").val('0').trigger("change");
			$("#categoryDiv").addClass("d-none");
		}
		else
		{
			$("#categoryDiv").removeClass("d-none");
		}
	});	
	<?php endif ?>
	
	<!-- Reset Button (resets the import) -->
	$("#resetButton").click(function (e)
	{
		e.preventDefault();
		
		//Ask if they really want to reset the import
		if( !confirm("<?php echo __( 'are-you-sure' ) ?>"))
		{
            return false;
        }
		
		var fileUrl 	= $("#enterXmlFileUrl").val();
		var site 		= "<?php echo $Admin->GetSite() ?>";

		$.ajax(
		{
			url: "<?php echo AJAX_ADMIN_PATH ?>import-reset/",
			data: {fileUrl:fileUrl,site:site},
			cache: false,
			type: 'post',
			dataType: 'json'
		})
		.done(function(data)
		{
			if (data.status == 'ok') 
			{
				$("#post-error").html("");
				$("#post-error").addClass("d-none");
				$("#resetButton").addClass("d-none");
				$("#importButtonXml").removeClass("d-none");
				$("#goBackFromImport").removeClass("d-none");
			}
			else
			{
				alert( "<?php echo __( 'an-error-happened-refresh-page' ) ?>" );
			}
		})
		.fail(function(){
			console.log('Failed resseting import');
		});
	});
	
	<!-- Start importing from XML -->
	$("#importButtonXml").click(function (e)
	{
		e.preventDefault();
		
		confirmChange = true;
		
		var postData 	= [];
		var crawlData 	= [];
		var postFields 	= [];
		var rplFields 	= [];
		
		var postAuthor 	= $("#postAuthor").val();
		var user 		= '<?php echo $Admin->UserID() ?>';
		var site 		= "<?php echo $Admin->GetSite() ?>";
		var lang 		= "<?php echo $Admin->GetLang() ?>";
		var fileUrl 	= $("#enterXmlFileUrl").val();
		var blog 		= $("#blogSelect").val();
		var oldUrl 		= $("#oldUrl").val();
		var system 		= $("#importSystems").val();
		var custom 		= $("#customTypes").val();
		var type 		= $("#postTypes").val();
		var cat 		= $("#category").val();
		var copyImg 	= $("#copyImages").is(":checked");

		var fields = $('select[name^=custom_fields]').map(function(idx, elem) { return $(elem).val() } );
		var values = $('input[name^=custom_values]').map(function(idx, elem) { return $(elem).val() } );
		
		var rpl_fields = $('input[name^=find_fields]').map(function(idx, elem) { return $(elem).val() } );
		var rpl_values = $('input[name^=find_values]').map(function(idx, elem) { return $(elem).val() } );

		for (let i = 0; i < fields.length; ++i) {
			var nam = fields[i];
			var val = values[i];
			
			postFields[i] = {name : nam, value : val };
		}
		
		for (let i = 0; i < rpl_fields.length; ++i) {
			var nam = rpl_fields[i];
			var val = rpl_values[i];
			
			rplFields[i] = {name : nam, value : val };
		}
		
		crawlData = [
			{ removeLinks 		: $("#removeLinks").is(":checked") },
			{ sourceText 		: $("#link-text").val() },
			{ avoidWords 		: $("#words-to-avoid").val() },
			{ removeOldPosts	: $("#remove-old-posts").val() },
			{ postStatus 		: $("#postStatus").val() },
			{ numItems 			: $("#num-items").val() },
			{ removeHtml 		: $("#removeHtml").is(":checked") },
			{ addSourceLink 	: $("#addSourceLink").is(":checked") },
			{ autoSetFirstImage : $("#autoSetFirstImage").is(":checked") },
			{ updatePost 		: $("#updatePost").is(":checked") },
			{ crawlAsGoogleBot	: $("#crawlAsGoogleBot option:selected").val() },
			{ randomIp 			: $("#randomIp").is(":checked") },
			{ removeSmarty 		: $("#removeSmarty").is(":checked") },
			{ removeServerSide 	: $("#removeServerSide").is(":checked") },
			{ removeScripts 	: $("#removeScripts").is(":checked") },
			{ removeStyles 		: $("#removeStyles").is(":checked") },
			{ removeLineBreaks 	: $("#removeLineBreaks").is(":checked") }
		];

		postData = [
			{ title 	: $("#post-title").val() },
			{ descr 	: $("#post-descr").val() },
			{ category 	: $("#post-category").val() },
			{ image 	: $("#post-image").val() },
			{ content 	: $("#post-content").val() },
			{ container	: $("#post-tags-container").val() },
			{ tags 		: $("#post-tags").val() }
		];
		
		if ( ( typeof(fileUrl) == "undefined" ) || ( fileUrl === "" ) )
		{
			Toast.fire({
				icon: "error",
				title: "<?php echo __( 'please-enter-a-valid-url' ) ?>"
			})
			return;
		}
	
		$.ajax(
		{
			url: "<?php echo AJAX_ADMIN_PATH ?>import-details-xml/",
			data: {fileUrl:fileUrl,site:site,lang:lang,arr:postData,fields:postFields,rpl:rplFields,oldUrl:oldUrl,author:postAuthor,system:system,custom:custom,type:type,cat:cat,copyImg:copyImg,blog:blog,user:user,crawl:crawlData},
			cache: false,
			type: 'post',
			dataType: 'json'
		})
		.done(function(data)
		{
			if (data.status == 'ok') 
			{
				if ( data.items > 0 )
				{
					if ( data.completed > 0 )
					{
						var message = "<?php echo __( 'file-imported' ) ?>";
						
						if ( data.date != '' )
						{
							message += " at <strong>" + data.date + "</strong>";
						}
						
						//console.log(message);

						$("#post-error").removeClass("d-none");
						$("#post-error").html(message);
						$("#importXmlInputFile").val("");
						$("#importXmlInputFile").attr("disabled", true);
						$("#importButtonXml").addClass("d-none");
						$("#goBackFromImport").addClass("d-none");
						$("#resetButton").removeClass("d-none");
					}
					
					else
					{
						loadXmlItems(fileUrl);
					}
				}
				else
				{
					Toast.fire({
						icon: "error",
						title: "<?php echo __( 'no-posts-found' ) ?>"
					})
				}
			}
			else 
			{
				if (data.message)
				{
					console.log(data.message);
					$("#post-error").removeClass("d-none");
					$("#post-error").append(data.message);
				}
				else
				{
					console.log('Error getting import details');
				}
			}
		})
		.fail(function(){
			console.log('Error getting import details');
		});		
	});

	//Check for changes in import selection
	$("#importSystems").change(function()
	{
		$("#nextPageButton").prop("disabled", true);
		$("#uploadXmlDiv").addClass("d-none");
		$("#uploadDiv").addClass("d-none");
		
		$("#importButtonXml").addClass("d-none");
		$("#importButtonXml").prop("disabled", true);
		$("#importButton").addClass("d-none");
		$("#importButton").prop("disabled", true);

		var id = $(this).val();

		id = id.trim();
		
		if ( id === "choose" )
		{
			$("#nextPageButton").prop("disabled", true);
		}
		else if ( id === "xml" )
		{
			$("#customSettingsDiv").removeClass("d-none");
			$("#nextPageButton").prop("disabled", false);
			$("#uploadXmlDiv").removeClass("d-none");
			$("#importButtonXml").removeClass("d-none");
			$("#importButtonXml").prop("disabled", false);
		}
		else
		{
			$("#customSettingsDiv").addClass("d-none");
			$("#nextPageButton").prop("disabled", false);
			$("#uploadDiv").removeClass("d-none");
			$("#importButton").removeClass("d-none");
			$("#importButton").prop("disabled", false);
		}
	});
	
	$("#importButton").click(function (e)
	{
		e.preventDefault();
		var id = $("#importId").val();
		var system = $("#importSystems").val();

		$.ajax(
		{
			url: "<?php echo AJAX_ADMIN_PATH ?>import-details/",
			data: {id:id,system:system},
			cache: false,
			type: 'post',
			dataType: 'json'
		})
		.done(function(data)
		{
			if (data.status == 'ok') 
			{
				if ( data.items > 0 )
				{
					if ( data.completed > 0 )
					{
						var message = "<?php echo __( 'file-imported' ) ?>";
						
						if ( data.date != '' )
						{
							message += " at <strong>" + data.date + "</strong>";
						}

						$("#post-error").removeClass("d-none");
						$("#post-error").html(message);
						$("#importXmlInputFile").val('');
						$("#importXmlInputFile").attr("disabled", true);
						$("#importButton").addClass("d-none");
						$("#goBackFromImport").addClass("d-none");
					}
					
					else
					{
						loadItems();
					}
				}
				else
				{
					alert( "<?php echo __( 'nothing-found' ) ?>" );
				}
			}
			else 
			{
				console.log('Error getting import details');
			}
		})
		.fail(function(){
			console.log('Error getting import details');
		});
		
	});

	var parent = $("#postAuthor").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "<?php echo AJAX_ADMIN_PATH ?>get-users/",
			data: function (params) {
				var query = {
					postSite: "<?php echo $Admin->GetSite() ?>",
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
			var html = data.text
			if (data.type=="draft") {
				html += '<span class="badge badge-pill badge-light">'+data.type+'</span>';
			}
			return html;
		}
	});
});
</script>

<script type='text/javascript'>
function loadItems()
{
	$("#importContent").addClass("d-none");
	var oldUrl = $("#oldUrl").val();
	var siteUrl = "<?php echo $Admin->SiteUrl() ?>";
	var siteId = "<?php echo $Admin->GetSite() ?>";
	var langId = "<?php echo $Admin->GetLang() ?>";
	var postAuthor = $("#postAuthor").val();
	var system = $("#importSystems").val();
	var custom = $("#customTypes").val();
	var type = $("#postTypes").val();
	var cat = $("#category").val();
	var id = $("#importId").val();
	var copyImg = $("#copyImages").is(":checked");
	
	var errors = 0;

	$("#importBar").removeClass("d-none");
	
	$.ajax(
	{
		url: "<?php echo AJAX_ADMIN_PATH ?>import-content/",
		data: {oldUrl:oldUrl,postAuthor:postAuthor,system:system,custom:custom,type:type,copyImg:copyImg,cat:cat,id:id,site:siteId,lang:langId,siteurl:siteUrl},
		cache: false,
		type: 'post',
		dataType: 'json',
		 xhrFields: {
            onprogress: function(r) {
                }
            },
	})
	.done(function(data)
	{
		if (data.status=='ok') 
		{
			var percentComplete = Math.ceil( ( data.currentItems / data.totalItems ) * 100 );
			console.log(percentComplete);
			$("#importBar").children(".progress-bar").width(percentComplete + "%");

			$("#post-status").append(data.message);
			
			if (data.totalItems > data.currentItems ) 
			{
				loadItems();
			}
			else
			{
				done();
			}
		}
		else 
		{
			errors++;
			
			if (data.message )
			{
				console.log(data.message);
				$("#post-status").append(data.message);
			}
			else
			{
				console.log("Error...");
			}
		}
	})
	.fail(function(){
		$("#importBar").addClass("d-none");
		$("#post-error").removeClass("d-none");
		$("#importContent").removeClass("d-none");
		formData = null;
	});
}

function done(errors)
{
	$("#importBar").addClass("d-none");
	$("#post-status").append(""); //Clean the post's text
		
	if ( !errors )
	{
		$("#post-success").removeClass("d-none");
	}
	else
	{
		$("#post-error").removeClass("d-none");
	}
}
</script>

<script type='text/javascript'>
$(document).ready(function()
{
	$("#importXmlInputFile").on('change',function()
	{
		$("#upload_button").removeClass("d-none");
	});
	
	$("#upload_button").click(function (e)
	{
		e.preventDefault();
		$("#submitSpinner").removeClass("d-none");
		$("#progressBar").removeClass("d-none");
		$("#progressBar").children(".progress-bar").width("0");

		var formData = new FormData();	
		formData.append('token', "<?php echo $Admin->GetToken() ?>");
		formData.append('site', "<?php echo $Admin->GetSite() ?>");
		formData.append('lang', "<?php echo $Admin->GetLang() ?>");
		formData.append('file', $("#importXmlInputFile")[0].files[0]);
		$.ajax(
		{
			url: "<?php echo AJAX_ADMIN_PATH ?>import-file-upload/",
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			xhr: function() {
				var xhr = $.ajaxSettings.xhr();
				if (xhr.upload) {
					$("#cancel_button").removeClass("d-none");
					$('#upload_button i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
					$('#upload_button').prop('disabled', true);
					$("#upload_button").addClass("disabled");
					xhr.upload.addEventListener("progress", function(e) {
						if (e.lengthComputable) {
							var percentComplete = (e.loaded / e.total) * 100;
							$("#progressBar").children(".progress-bar").width(percentComplete + "%");
						}
					}, false);
					
					$('#cancel_button').click(function (e) {
						if (xhr)
						{
							xhr.abort(); 
							xhr = null;
						}
						formData = null;
						$("#submitSpinner").addClass("d-none");
						$("#progressBar").addClass("d-none");
						$("#upload_button").addClass("d-none");
						$(this).addClass("d-none");
					});
				}
				return xhr;
			}
		})
		.done(function(data)
		{
			$("#submitSpinner").addClass("d-none");
			$("#progressBar").addClass("d-none");
			$("#cancel_button").addClass("d-none");
			$("#upload_button").addClass("d-none");

			if (data.status==0) 
			{
				$("#importId").attr("value",data.importId);
				$("#importButton").removeClass("d-none");
				$("#importButton").removeClass("disabled");
				$("#importButton").button("refresh");
			} else 
			{
				if (data.message )
				{
					alert(data.message);
				}
				else
				{
					alert('Error');
				}
			}
		});
	});
});
</script>