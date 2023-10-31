<script type="application/javascript">
	$(document).ready(function() {
		var parent = $("#catParent").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
					url: "<?php echo AJAX_ADMIN_PATH ?>get-categories/",
					data: function (params) {
						var query = 
						{
							lang: "<?php echo $Cat['id_lang'] ?>",
							blog: "<?php echo $Cat['id_blog'] ?>",
							site: "<?php echo $Cat['id_site'] ?>",
							catID: "<?php echo $Cat['id'] ?>",
							query: params.term
						}
						return query;
				},
				processResults: function (data) {
					return data;
				}
			},
			templateResult: function(data) {
					var html = data.text;
					return html;
			}
		});
	});
</script>

<script type="application/javascript">
	$(document).on('click', '#catImageModal', function(e)
	{
			e.preventDefault();
			var post = '0';
			var action = ''; //TODO?
			var calledFrom = 'category';
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
			$("#catLogoPreview").attr("src", "");
			$("#catLogoFile").val('');
			$("#buttonRemoveLogo").addClass("d-none");
			$("#catLogoPreview").attr("src", "<?php echo HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS ?>default.svg");
		});
</script>