<script type="application/javascript">
	$(document).ready(function()
	{
		//Check Minor Edit Button
		$("#retrieve-json-data-from-an-api").change(function()
		{
			if(!this.checked)
			{
				$("#jsonDataDiv").addClass("d-none");
			}
			
			else
			{
				$("#jsonDataDiv").removeClass("d-none");
			}
		});
	});
	
	//Check JSON Button
	$(document).on('click', '#siteImageModal', function(e)
	{
		e.preventDefault();
			var post = '0';
			var action = ''; //TODO?
			var calledFrom = 'site-image';
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
				console.log(data);	
				$('#post-detail').html('');    
				$('#post-detail').html(data);
				$('#modal-loader').hide();
			 })
			 .fail(function(){
				$('#post-detail').html('Error. Please try again...');
				$('#modal-loader').hide();
			});
	});
	$(document).on('click', '#featuredImageModal', function(e)
	{
			e.preventDefault();
			var post = '0';
			var action = ''; //TODO?
			var calledFrom = 'store';
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
				console.log(data);	
				$('#post-detail').html('');    
				$('#post-detail').html(data);
				$('#modal-loader').hide();
			 })
			 .fail(function(){
				$('#post-detail').html('Error. Please try again...');
				$('#modal-loader').hide();
			});
	});
		
		$("#buttonRemoveLogo").on("click", function()
		{
			$("#logoPreview").attr("src", "");
			$("#logoFile").val('');
			$("#buttonRemoveLogo").addClass("d-none");
			$("#logoPreview").attr("src", "<?php echo HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS ?>default.svg");
		});
</script>