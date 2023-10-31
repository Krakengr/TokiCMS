<script type='text/javascript'>
$(document).ready(function(){
	$(document).on('click', '#close', function(e)
	{
		e.preventDefault();
		
		if( !confirm("<?php echo __( 'are-you-sure-this-widget-will-be-disappeared' ) ?>"))
		{
            return false;
        }

		var id = $(this).data('id');
		var site = '<?php echo $Admin->GetSite() ?>';
		var user = '<?php echo $Admin->UserID() ?>';
		var action = 'close';
		var card = $(this).closest('.card');

		$.ajax({
			url: '<?php echo AJAX_ADMIN_PATH ?>dashboard-sort/',
			type: 'POST',
			data: {id:id,site:site,user:user,action:action},
			cache: false
		})
		.done(function(data){
			card.CardWidget('remove');
			Toast.fire({
				icon: "success",
				title: "<?php echo __( 'data-updated' ) ?>"
			})
		})
		.fail(function(){
			console.log("Closure error for id: " + id);
		});
	});	
	
	/*
	$(this).closest('.card').CardWidget('toggle');
	$(document).on('click', '#minimize', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var site = '<?php echo $Admin->GetSite() ?>';
		var user = '<?php echo $Admin->UserID() ?>';
		var action = 'minimize';
		$.ajax({
			url: '<?php echo AJAX_ADMIN_PATH ?>dashboard-sort/',
			type: 'POST',
			data: {id:id,site:site,user:user,action:action},
			cache: false
		})
		.done(function(data){
			console.log(data);
		})
		.fail(function(){
			console.log("Minimize error for id: " + id);
		});
	});*/
});


$(function(){'use strict'
	$('.connectedSortable').sortable({
		update: function(event, ui) {
			var pos = $(this).data("id");
			var site = '<?php echo $Admin->GetSite() ?>';
			var user = '<?php echo $Admin->UserID() ?>';
			var action = 'sort';
			var idsInOrder = $(this).sortable('toArray', { attribute: 'data-id' });
			$.ajax({
				data: {pos:pos,ids:idsInOrder,site:site,user:user,action:action},
				type: 'POST',
				url: "<?php echo AJAX_ADMIN_PATH ?>dashboard-sort/",
				cache: false
			})
			.done(function(data){
				Toast.fire({
					icon: "success",
					title: "<?php echo __( 'data-updated' ) ?>"
				})
			});
		},
		placeholder:'sort-highlight',
		connectWith:'.connectedSortable',
		handle:'.card-header, .nav-tabs',
		forcePlaceholderSize:true,
		zIndex:999999
	});
	$('.connectedSortable .card-header').css('cursor','move');
});
</script>

<script type='text/javascript'>
$(function () 
{
    $('#draftForm').on('submit', function (e) 
	{
        if (!e.isDefaultPrevented()) 
		{
            $.ajax(
			{
                type: "POST",
                url: "<?php echo AJAX_ADMIN_PATH ?>add-draft/",
                data: $(this).serialize(),
                success: function (data)
                {
                    var messageAlert = 'alert-' + data.type;
                    var messageText = data.message;

                    var alertBox = '<div class="alert ' + messageAlert + '" id="' + messageAlert + '-alert"><button type="button" class="close" data-bs-dismiss="alert">x</button>' + messageText + '</div>';
					
                    if (messageAlert && messageText) {
                        $('#draftForm').find('.messages').html(alertBox);
						if ( messageAlert == 'alert-success' )
						{
							$('#draftForm')[0].reset();
						}
                    }
                }
            });
			return false;
        }
    })
});
</script>