<script type="application/javascript">
$(document).ready(function()
{
	// Prevent the form submit when press enter key.
	$("form").keypress(function(e) {
		if ((e.which == 13) && (e.target.type !== "textarea")) {
			return false;
		}
	});
	
	$("#updateButton").on("click", function(e)
	{
		confirmChange = false;
	});
	
	$("#deleteButton").on("click", function(e)
	{
		confirmChange = false;
	});

	//Editor.js Modal
	$(document).on('click', '#imageEditorJsModal', function(e)
	{
		e.preventDefault();
		var calledFrom = 'editorjs';
		var ajaxUrl = '<?php echo AJAX_ADMIN_PATH ?>media-manager-editor/';
		DoWork( calledFrom, ajaxUrl );
	});
		
	//Editor Modal
	$(document).on('click', '#imageEditorModal', function(e)
	{
		e.preventDefault();
		var calledFrom = 'editor';
		var ajaxUrl = '<?php echo AJAX_ADMIN_PATH ?>media-manager-editor/';
		DoWork( calledFrom, ajaxUrl );
	});
	
	function DoWork(calledFrom, ajaxUrl )
	{
		var post = '<?php echo Router::GetVariable( 'key' ) ?>';
		var action = ''; //TODO?
		var token = '<?php echo $Admin->GetToken() ?>';

		$('#post-detail').html(''); 
		$('#modal-loader').show();  
		$.ajax(
		{
			url: ajaxUrl,
			type: 'POST',
			data: {action:action,post:post,token:token,calledFrom:calledFrom},
			dataType: 'html',
			cache: false
		})
		.done(function(data)
		{	
			$('#post-detail').html('');    
			$('#post-detail').html(data);
			$('#modal-loader').hide();
		})
			.fail(function(){
			$('#post-detail').html('Error. Please try again...');
			$('#modal-loader').hide();
		});
	}
});
</script>