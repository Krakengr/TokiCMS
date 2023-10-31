<div id="mapPostsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal centered" role="document" style="padding-right: 17px; display: block; width:100%;">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title"><?php echo __( 'search-for-a-post-or-page' ) ?></h5>
</div>
<div class="modal-body">
<div class="mb-3">
	<select class="form-control select2" id="searchSyncPosts" style="width: 100%;"></select>
	<input type="hidden" id="modalPostId" value="" />
	<input type="hidden" id="modalPostUrl" value="" />
</div>

<div id="modalP-post-detail"></div>

<div class="modal-footer">
	<div class="text-left"><button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button></div>
	<div class="text-right"><button id="mapPosts" type="button" class="btn btn-primary"><?php echo __( 'sync' ) ?></button></div>
</div>
</div>
</div>
</div>
</div>

<script type="application/javascript">
$(document).ready(function() {
	var post = $("#searchSyncPosts").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		dropdownParent: $("#mapPostsModal"),
		minimumInputLength: 2,
		ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>search-sync-posts/",
				data: function (params) {
					var query = {
						postType: "<?php echo $Admin->CurrentAction() ?>",
						postSite: "<?php echo $Admin->GetSite() ?>",
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
	
	$(".quickSearch").on("click", function(e)
	{
		e.preventDefault();
		var sId = $(this).data('id');
		var sUrl = $(this).data('url');
		$('#searchSyncPosts').val(null).trigger('change');
		$('#mapPostsModal').modal('show');
		$('#modalPostId').val(sId);
		$('#modalPostUrl').val(sUrl);
	});
	
	$("#mapPosts").on("click", function(e)
	{
		e.preventDefault();
		var pId = $('#searchSyncPosts option:selected').val();
		var sId = $('#modalPostId').val();
		var sUrl = $('#modalPostUrl').val();
		var siteId = '<?php echo $Admin->GetSite() ?>';

		$.ajax(
		{
			url: '<?php echo AJAX_ADMIN_PATH ?>map-single-post/',
			type: 'POST',
			data: {pId:pId,sId:sId,sUrl:sUrl,siteId:siteId},
			dataType: 'html',
			cache: false
		})
		.done(function(data)
		{
			console.log(data);
			$('#post-row' + sId).remove();
			$('#mapPostsModal').modal('hide');
		 })
		.fail(function(){
			$('#modalP-post-detail').html('<?php echo __( 'an-error-happened' ) ?>');
		});
	});
});
</script>