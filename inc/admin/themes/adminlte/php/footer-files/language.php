<?php $i = 0 ?><script type="application/javascript">
$(document).ready(function() {
	
	//Check for changes in comment system selection
	$("#inputCommentSystem").change(function()
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