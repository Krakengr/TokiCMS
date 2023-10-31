<script type='text/javascript'>
$(document).ready(function()
{
	var parent = $("#confirmationPage").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "<?php echo AJAX_ADMIN_PATH ?>filter-get-pages/",
			data: function (params) {
				var query = {
					postLang: "<?php echo $Admin->GetLang() ?>",
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
	
	$('#deleteFormTemplate').change(function() {
		if($(this).is(":checked")) 
		{
			if ( confirm(v2['deleteCheckBoxAlert']) )
				this.checked = true;
			else
				this.checked = false;
		}      
	});
		
	$(".remElementButton").click(function (e)
	{
		e.preventDefault();
		
		var id = $(this).data('id');
		var formId = '<?php echo (int) Router::GetVariable( 'key' ) ?>';
		
		if( !confirm("<?php echo __( 'are-you-sure-you-want-to-remove-this-field' ) ?>"))
		{
            return false;
        }
		
		$.ajax({
			url: '<?php echo AJAX_ADMIN_PATH ?>remove-form-element/',
			type: 'POST',
			data: {id:id,formId:formId},
			dataType: 'json',
			cache: false
		})
		.done(function(data){
			
			if ( data.status == 'ok' )
			{
				$("#form-item-" + id).remove();
			}
			
			if ( data.code !== '' )
			{
				$("#demoForm").html(data.code);
			}
			
			if ( data.items == 0 )
			{
				$("#emptyFormAlert").removeClass("d-none");
			}
		})
		.fail(function(){
			console.log("Remove error for id: " + id);
		});
	});

	$("#sendNotificationOption").change(function()
	{
		$( "#sendValueName" ).prop( "disabled", false );
		
		var id = $(this).val();
		id = id.trim();
		
		if ( ( id === 'empty' ) || ( id === 'not-empty' ) )
		{
			$( "#sendValueName" ).prop( "disabled", true );
			$( "#sendValueName" ).val('');
		}
	});
	
	$("#confirmationType").change(function()
	{
		$("#confirmationMessageDiv").addClass("d-none");
		$("#confirmationUrlDiv").addClass("d-none");
		$("#confirmationPageDiv").addClass("d-none");
		
		var id = $(this).val();
		id = id.trim();
		
		if ( id === 'message' )
		{
			$("#confirmationMessageDiv").removeClass("d-none");
		}
		
		else if ( id === 'url' )
		{
			$("#confirmationUrlDiv").removeClass("d-none");
		}
		
		else if ( id === 'page' )
		{
			$("#confirmationPageDiv").removeClass("d-none");
		}
	});
	
	$("#dontSendNotificationOption").change(function()
	{
		$( "#dontSendValueName" ).prop( "disabled", false );
		
		var id = $(this).val();
		id = id.trim();
		
		if ( ( id === 'empty' ) || ( id === 'not-empty' ) )
		{
			$( "#dontSendValueName" ).prop( "disabled", true );
			$( "#sendValueName" ).val('');
		}
	});
	
	$("#enableNotifications").click(function()
	{
		if (this.checked)
		{
			$("#notificationsFormGroup").removeClass("d-none");
		}
		else
		{
			$("#notificationsFormGroup").addClass("d-none");
		}
	});

	$(".addElementButton").click(function (e)
	{
		e.preventDefault();
	
		var id = $(this).data('id');
		var formId = '<?php echo (int) Router::GetVariable( 'key' ) ?>';

		$.ajax({
			data: {id:id,formId:formId},
			type: 'POST',
			url: "<?php echo AJAX_ADMIN_PATH ?>add-form-element/",
			dataType: 'json',
			cache: false
		})
		.done(function(data){
			if ( data.status == 'ok' )
			{
				if ( data.html !== '' )
				{
					var itemPos = '#formBuilder';
					
					$(itemPos).append(data.html);
				}
				
				if ( data.code !== '' )
				{
					$("#demoForm").html(data.code);
				}
				
				$("#emptyFormAlert").addClass("d-none");
			}
			else
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'an-error-happened' ) ?>"
				})
			}
		});
	});
});
</script>

<script type="application/javascript">
$(function(){'use strict'
	$('.connectedSortable').sortable({
		update: function(event, ui) {
			var formId = '<?php echo (int) Router::GetVariable( 'key' ) ?>';
			var idsInOrder = $(this).sortable('toArray', { attribute: 'data-id' });
			$.ajax({
				data: {ids:idsInOrder,formId:formId},
				type: 'POST',
				url: "<?php echo AJAX_ADMIN_PATH ?>sort-form/",
				dataType: 'json',
				cache: false
			})
			.done(function(data){
				if ( data.status == 'ok' )
				{
					Toast.fire({
						icon: "success",
						title: data.message
					})
					
					if ( data.code !== '' )
					{
						$('#demoForm').html(data.code);
					}
				}
				else
				{
					Toast.fire({
						icon: "error",
						title: "<?php echo __( 'an-error-happened' ) ?>"
					})
				}
			});
		},
		placeholder:'sort-highlight',
		connectWith:'.connectedSortable',
		handle:'.card-header, .nav-tabs',
		forcePlaceholderSize:true,
		dropOnEmpty: true,
		zIndex:999999
	});
	$('.connectedSortable .card-header').css('cursor','move');
});
</script>