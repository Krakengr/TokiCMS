<script type="application/javascript">
$(document).ready(function() {
	
	//Check for changes in clone post selection
	$("#clonePostSelection").change(function()
	{
		var id = $(this).val();
		id = id.trim();

		if( id == 0 )
		{
			$("#cloneThePost").attr("disabled", true);
		}
		
		else
		{
			$("#cloneThePost").attr("disabled", false);
		}
	});			

	$(".clonePost").on("click", function(e)
	{
		e.preventDefault();
		$("#clonePostSelection").val('0').trigger("change");
		$('#cloneDateInput').prop('checked', false);
		$('#clonePostId').val('0');
		$('#clonePostModal').find('#error').addClass('d-none');
		$('#clonePostModal').find('#success').addClass('d-none');
		$('#cloneThePost').attr('disabled', false);
		$("#clonePostModal").modal("show");
		
		var id = $(this).data('id');

		$('#clonePostId').val(id);
	});

	$("#cloneThePost").on("click", function(e)
	{
		e.preventDefault();

		var id 			= $('#clonePostId').val();
		var errorMess 	= '<?php echo __( 'an-error-happened' ) ?>';
		var cId 		= $('#clonePostSelection option:selected').val();
		var bId 		= $('#clonePostSelection option:selected').data("id");
		var lId 		= $('#clonePostSelection option:selected').data("lang");
		var keepDate 	= $('#cloneDateInput').is(':checked');
		var userId		= '<?php echo $Admin->UserID() ?>';
		var siteId		= '<?php echo $Admin->GetSite() ?>';
		var html 		= "";
		
		if(typeof(id) == "undefined" || ( id === "" ) || ( id === 0 ) )
		{
			$("#clonePostModal").modal("hide");
			alert(errorMess);
			return;
		}
		
		if(typeof(lId) == "undefined" || ( lId === "" ) || ( lId === 0 ) )
		{
			$("#clonePostModal").modal("hide");
			alert(errorMess);
			return;
		}
		
		if(typeof(cId) == "undefined" || ( cId === "" ) || ( cId === 0 ) )
		{
			$("#clonePostModal").modal("hide");
			alert(errorMess);
			return;
		}
		
		if(typeof(bId) == "undefined" || ( bId === "" ) )
		{
			bId = 0;
		}

		$.ajax({
			url: '<?php echo AJAX_ADMIN_PATH ?>clone-post/',
			type: 'post',
			dataType: 'json',
			data: {id:id,cId:cId,bId:bId,lId:lId,keep:keepDate,userId:userId,siteId:siteId},
			success: function(json) {
				if ( json['status'] == 'error' ) {
					html += "<p><?php echo __( 'post-add-error' ) ?></p>";
					$('#clonePostModal').find('#error').removeClass('d-none');
					$('#clonePostModal').find('#error').html(html);
				}
				
				else
				{
					html += "<p>" + json['url'] + "</p>";
					$('#cloneThePost').attr('disabled', true);
					$('#clonePostModal').find('#success').removeClass('d-none');
					$('#clonePostModal').find('#success').html(html);
				}
				
				//Reset the value of clonePostId
				$('#clonePostId').val('0');
			},
			error: function() {
				alert("<?php echo __( 'an-error-happened-refresh-page' ) ?>");
			}
		});		
	});
});
</script>
	
<?php if ( IsAllowedTo( 'manage-posts' ) && !empty( $data ) && !$isOnSync ) : ?>
<!-- Modal for edit post -->
<div class="modal fade" id="editPostModal" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $L['quick-edit'] . ' ' . __( $type ) ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-success d-none success"></div>
				<div class="alert alert-danger d-none error"></div>
				<div id="modal-loader" style="text-align: center;">
					<img src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/loading.gif">
				</div>  
				<div id="post-detail">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $L['cancel'] ?></button>
				<button id="editPost" type="submit" class="btn btn-primary" data-id=""><?php echo $L['save'] ?></button>
			</div>
		</div>
	</div>
</div>

<!-- Modal for post clone -->
<div class="modal fade" id="clonePostModal" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo $L['clone'] . ' ' . __( $type ) ?></h4>
			</div>
			<div class="modal-body">
				<div id="success" class="alert alert-success d-none success"></div>
				<div id="error" class="alert alert-danger d-none error"></div> 
				<div id="post-detail">
					<div class="form-group">
						<select id="clonePostSelection" class="form-control shadow-none" style="width: 100%; height:36px;" name="clone-post" aria-label="Clone Post">
						<?php echo AdminGetClonePostsInfo( $Admin->GetSite(), ( $Admin->CurrentAction() == 'pages' ) ) ?>
						</select>
					</div>
				</div>
				<div class="form-check">
					<input id="cloneDateInput" class="form-check-input" type="checkbox" value="1">
					<label for="cloneDateInput" class="form-check-label"><?php echo __( 'keep-original-published-date' ) ?></label>
					<small class="form-text text-muted"><?php echo __( 'keep-original-published-date-tip' ) ?></small>
				</div>
				<input type="hidden" id="clonePostId" value="0">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $L['cancel'] ?></button>
				<button id="cloneThePost" type="submit" class="btn btn-primary" data-id="" disabled><?php echo $L['clone'] ?></button>
			</div>
		</div>
	</div>
</div>
<?php endif ?>