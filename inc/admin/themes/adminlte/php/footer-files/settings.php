<?php
include( ARRAYS_ROOT . 'generic-arrays.php'); 

$i = 0;
?><script type="application/javascript">
$(document).ready(function() {
	
	//Check for changes in comment system selection
	$("#comment-sys").change(function()
	{
		<?php foreach ( $externalCommentsArray as $key => $row ) :
			if ( $key == 'none' ) 
				continue;
		?>$('#<?php echo $key ?>').addClass('d-none');
		<?php endforeach ?>
		
		var id = $(this).val();
		id = id.trim();
		
		<?php foreach ( $externalCommentsArray as $key => $row ) :
			if ( $key == 'none' ) 
				continue;
		?><?php echo ( ( $i > 0 ) ? 'else ' : '' ) ?>if( id === '<?php echo $key ?>' )
		{
			$('#<?php echo $key ?>').removeClass('d-none');
		}
		<?php $i++; endforeach; ?>
	});
});
</script>	

<script type="application/javascript">
		//Cover Modal
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
		
		$(document).on('click', '#siteBackgModal', function(e)
		{
			e.preventDefault();
			var post = '0';
			var action = ''; //TODO?
			var calledFrom = 'maintenance';
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
		
		$("#buttonRemoveBackg").on("click", function()
		{
			$("#siteBackgFile").val('');
			$("#buttonRemoveBackg").addClass("d-none");
			$("#backgPreview").attr("src", "<?php echo HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS ?>default.svg");
		});
		
		$("#buttonRemoveLogo").on("click", function()
		{
			$("#siteLogoPreview").attr("src", "");
			$("#siteLogoFile").val('');
			$("#buttonRemoveLogo").addClass("d-none");
			$("#siteLogoPreview").attr("src", "<?php echo HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS ?>default.svg");
		});
	</script>
