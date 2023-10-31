<script type="application/javascript">
$(function(){'use strict'
	$('.connectedSortable').sortable({
		update: function(event, ui) {
			var formId = '<?php echo (int) Router::GetVariable( 'key' ) ?>';
			var idsInOrder = $(this).sortable('toArray', { attribute: 'data-id' });
			$.ajax({
				data: {ids:idsInOrder,formId:formId},
				type: 'POST',
				url: "<?php echo AJAX_ADMIN_PATH ?>sort-content/",
				dataType: 'json',
				cache: false
			})
			.done(function(data){
				if ( data.status == 'ok' )
				{
					if ( data.code !== '' )
					{
						$("#tablePreview").html(data.code);
					}
				
					Toast.fire({
						icon: "success",
						title: data.message
					})
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

$(function(){'use strict'
	$('.connectedSortable2').sortable({
		update: function(event, ui) {
			//var columnId = $(this).closest('div').attr("parent");
			var idsInOrder = $(this).sortable('toArray', { attribute: 'data-id' });

			$.ajax({
				data: {ids:idsInOrder},
				type: 'POST',
				url: "<?php echo AJAX_ADMIN_PATH ?>sort-theme-columns/",
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
		//connectWith:'.connectedSortable2',
		handle:'.card-header, .nav-tabs',
		forcePlaceholderSize:true,
		dropOnEmpty: true,
		zIndex:999999
	});
	$('.connectedSortable2 .card-header').css('cursor','move');
});
</script>


<script type='text/javascript'>
//Assing the color picker
$(function () {
	$('.color-picker').colorpicker();
});

$(document).ready(function()
{
	//Prevent enter key to submit the form
	$(window).keydown(function(event){
		if(event.keyCode == 13) {
			event.preventDefault();
			return false;
		}
	});
	
	//Add a new row
	$("#addRow").click(function (e)
	{
		e.preventDefault();
		
		var themeId = '<?php echo Router::GetVariable( 'key' ) ?>';

		$.ajax({
			data: {themeId:themeId},
			type: 'POST',
			url: "<?php echo AJAX_ADMIN_PATH ?>add-table-column/",
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
					$("#tablePreview").html(data.code);
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
	});
	
});

</script>