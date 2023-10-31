<script type='text/javascript'>
$(document).ready(function()
{
	$(".dropdownPosButton").click(function (e)
	{
		e.preventDefault();
		
		var pos = $(this).data('id');
		var w = $(this).data('widget');
		var site = '<?php echo $Admin->GetSite() ?>';
		var theme = '<?php echo $Admin->ActiveTheme() ?>';
		var lang = '<?php echo $Admin->GetLang() ?>';

		$.ajax({
			data: {pos:pos,site:site,lang:lang,theme:theme,w:w},
			type: 'POST',
			url: "<?php echo AJAX_ADMIN_PATH ?>add-widget/",
			dataType: 'json',
			cache: false
		})
		.done(function(data){
			if ( data.status == 'ok' )
			{
				if ( data.html !== '' )
				{
					var itemPos = '#pos-' + pos;
					
					$(itemPos).append(data.html);
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
	});
});
</script>

<script type="application/javascript">
/*
$(".buildInWidgets, .connectedSortable").sortable({
	update: function(event, ui) {
			var pos = $(this).data("id");
			var site = '<?php //echo $Admin->GetSite() ?>';
			var theme = '<?php //echo $Admin->ActiveTheme() ?>';
			var action = 'sort';
			var idsInOrder = $(this).sortable('toArray', { attribute: 'data-id' });
			$.ajax({
				data: {pos:pos,ids:idsInOrder,site:site,theme:theme},
				type: 'POST',
				url: "<?php echo AJAX_ADMIN_PATH ?>sort-widgets/",
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
	connectWith:'.connectedSortable',
	handle:'.card-header, .nav-tabs',
	forcePlaceholderSize:true,
	dropOnEmpty: true,
	zIndex:999999,
    remove: function (e, li) {
        li.item.clone().insertAfter(li.item);
        $(this).sortable('cancel');
        return li.item.clone();
    }
}).disableSelection();
$('.connectedSortable, .buildInWidgets .card-header').css('cursor','move');*/

$(function(){'use strict'
	$('.connectedSortable').sortable({
		update: function(event, ui) {
			var pos = $(this).data("id");
			var site = '<?php echo $Admin->GetSite() ?>';
			var theme = '<?php echo $Admin->ActiveTheme() ?>';
			var action = 'sort';
			var idsInOrder = $(this).sortable('toArray', { attribute: 'data-id' });
			$.ajax({
				data: {pos:pos,ids:idsInOrder,site:site,theme:theme},
				type: 'POST',
				url: "<?php echo AJAX_ADMIN_PATH ?>sort-widgets/",
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
		connectWith:'.connectedSortable',
		handle:'.card-header, .nav-tabs',
		forcePlaceholderSize:true,
		dropOnEmpty: true,
		zIndex:999999
	});
	$('.connectedSortable .card-header').css('cursor','move');
});
</script>