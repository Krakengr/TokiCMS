<?php if ( $Admin->GetDraft() == 0 ) : ?>
<script type="application/javascript">
// Open current tab after refresh page
var activeTab = localStorage.getItem('activeTab');

if (activeTab) {
	$('a[data-target="' + activeTab + '"]').tab('show');
}

$('a[data-toggle="tab"]').on('click', function(e) {
  e.preventDefault();
  var tab_name = $(this).data('target');
  localStorage.setItem('activeTab', tab_name);
  $(this).tab('show');
  return false;
});
</script>
<?php endif ?>

<script type="application/javascript">
//Reset Gallery Button
$("#resetGallery").on("click", function(e)
	{
		e.preventDefault();
		var post = '<?php echo $Post->PostID() ?>';
		var token = '<?php echo $Admin->GetToken() ?>';
		$.ajax({
			url: '<?php echo AJAX_ADMIN_PATH ?>reset-gallery/',
			type: 'post',
			dataType: 'json',
			data: {post:post,token:token},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
					$('.photo-gallery-item').remove();
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
});
</script>
	
	<script type="application/javascript">
	$(document).ready(function()
	{
		// Prevent the form submit when press enter key.
		$("form").keypress(function(e) {
			if ((e.which == 13) && (e.target.type !== "textarea")) {
				return false;
			}
		});
		
		//Check Minor Edit Button
		$("#minor-edit").change(function()
		{
			if(!this.checked)
			{
				$("#editReasonDiv").removeClass("d-none");
			}
			
			else
			{
				$("#editReasonDiv").addClass("d-none");
			}
		});
		
		//Remove Graph Image
		$("#buttonRemoveGraph").on("click", function()
		{
			$("#graphImagePreview").attr('src', '');
			$("#graphImageFile").attr('value', '');
			$("#buttonRemoveGraph").toggleClass("d-none");
		});
		
		// Remove Cover Image
		$(document).on('click', '#removeCover', function(e)
		{
			e.preventDefault();
			$('.thumbnail').addClass("d-none");
			$('#coverImage').attr("src", '');
			$('#internalImage').attr("value", '');
			$('#coverImageID').attr('value', '');
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
		
		//Graph Image Gallery
		$(document).on('click', '#imageGraphModal', function(e)
		{
			e.preventDefault();
			var calledFrom = 'graph';
			var ajaxUrl = '<?php echo AJAX_ADMIN_PATH ?>media-manager-graph/';
			DoWork( calledFrom, ajaxUrl );
		});
		
		//Cover Modal
		$(document).on('click', '#imageCoverModal', function(e)
		{
			e.preventDefault();
			var calledFrom = 'cover';
			var ajaxUrl = '<?php echo AJAX_ADMIN_PATH ?>media-manager-gallery/';
			DoWork( calledFrom, ajaxUrl );
		});
		
		//Gallery Modal
		$(document).on('click', '#imageGalleryModal', function(e)
		{
			e.preventDefault();
			var calledFrom = 'gallery';
			var ajaxUrl = '<?php echo AJAX_ADMIN_PATH ?>media-manager-gallery/';
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

	<script type="application/javascript">
	$(document).ready(function() {
		
		//Clone a page/post
		//Remove Graph Image
		$("#buttonRemoveGraph").on("click", function()
		{
			
		});
		
		//Check for changes in move post selection
		$("#movePostSelection").change(function()
		{
			$("#siteDiv").addClass("d-none");
			$("#blogDiv").addClass("d-none");
			$("#loaderShow").removeClass("d-none");
			
			var id 			= $(this).val();
			var targetId 	= $(this).find(':selected').attr('data-id');
			
			id = id.trim();
			
			if( id == 'site' )
			{
				var ajaxUrl = '<?php echo AJAX_ADMIN_PATH ?>get-move-site/';
				$("#movePostTypeInput").val(id);
				$("#movePostIdInput").val(targetId);
				GetSelectData( targetId, id, ajaxUrl );
			}
			
			else if ( id == 'blog' )
			{
				var ajaxUrl = '<?php echo AJAX_ADMIN_PATH ?>get-move-blogs/';
				$("#movePostTypeInput").val(id);
				$("#movePostIdInput").val(targetId);
				GetSelectData( targetId, id, ajaxUrl );
				
			}
			else
			{
				$("#loaderShow").addClass("d-none");
				$("#movePostTypeInput").val('0');
				$("#movePostIdInput").val('');
			}
		});
		
		function GetSelectData( id, type, ajaxUrl )
		{
			var isPage = '<?php echo ( $Post->IsPage() ? 'true' : 'false' ) ?>';
			
			$.ajax(
			{
				url: ajaxUrl,
				type: 'POST',
				data: {id:id,isPage:isPage},
				dataType: 'json',
				cache: false
			})
			.done(function(data)
			{
				if ( type == 'blog' )
				{
					if ( data['results'] > 0 )
					{
						$("#movePostBlogSelection").empty().append(data['html']);
						
						if ( !data['isPage'] )
						{
							$("#moveChildsInput").removeClass("d-none");
						}
					}
					else
					{
						$("#movePostBlogSelection").empty().append('');
					}
					
					$("#loaderShow").addClass("d-none");
					$("#blogDiv").removeClass("d-none");
				}
				else if ( type == 'site' )
				{
					if ( data['results'] > 0 )
					{
						$("#movePostSiteSelection").empty().append(data['html']);
						
						if ( !data['isPage'] )
						{
							$("#moveChildsInput").removeClass("d-none");
						}
					}
					else
					{
						$("#movePostSiteSelection").empty().append('');
					}
					
					$("#loaderShow").addClass("d-none");
					$("#siteDiv").removeClass("d-none");
				}
			 })
			 .fail(function(){
			});
		}
		
		<?php if ( $Admin->Schema() && IsAllowedTo( 'manage-seo' ) ) : ?>
		<!-- TODO -->
		<?php endif ?>
		<?php if ( $hasAttrs ) : ?>
		
		//Add new field
		$(".newAttFieldButton").unbind().click(function(e)
		{
			e.preventDefault();
			var attId = $(this).data('id');
			var html = "";
			
			if(typeof(attId) == "undefined" || ( attId === "" ) || ( attId === 0 ) )
			{
				return;
			}
			
			html += '  <div class="form-group"><textarea class="form-control" rows="2" name="att[' + attId + '][0][]" cols="50" id="att' + attId + '"></textarea></div>';
			
			$("#attrFieldGroup" + attId).append(html);
		});
		
		<?php endif ?>
		<?php if ( $canProduct ) : ?>
		
		//Add new Variation
		$("#newVariantButton").unbind().click(function(e)
		{
			e.preventDefault();
			
			var postId = $(this).data('id');
			var langId = '<?php echo $Admin->GetLang() ?>';
			var siteId = '<?php echo $Admin->GetSite() ?>';
			var errorMess	= '<?php echo __( 'an-error-happened' ) ?>';
			var html   = "";
			
			$.ajax(
			{
				url: '<?php echo AJAX_ADMIN_PATH ?>add-variation-group/',
				type: 'POST',
				data: {id:postId,lang:langId,site:siteId},
				dataType: 'json',
				cache: false
			})
			.done(function(data)
			{
				if (data.status == 'ok') 
				{
					html += '<tr id=\"varField-row' + postId + '\">';
	
					html += '  <td class=\"text-center\"><input type=\"text\" id=\"varTitle\" name=\"variations[' + postId + '][title]\" class=\"form-control mb-4\" placeholder=\"red, blue, 128GB, etc...\" value=\"' + data.vTitle + '\" /></td>';
			
					html += '  <td class=\"text-center\"><input type=\"text\" id=\"varpostTitle\" name=\"variations[' + postId + '][postTitle]\" class=\"form-control mb-4\" value=\"' + data.pTitle + '\" disabled /></td>';
			
					html += '  <td class=\"text-center\"><input type=\"number\" id=\"varOrder\" name=\"variations[' + postId + '][order]\" class=\"form-control mb-4\" value=\"0\" min=\"0\" /></td>';
	
					html += '  <td class=\"text-center\"><button type=\"button\" id=\"removePostVarButton\" title=\"<?php echo __( 'remove' ) ?>\" data-id=\"' + postId + '\" class=\"btn btn-danger btn-flat btn-sm removePostVarButton\" data-toggle=\"tooltip\" title=\"<?php echo __( 'remove' ) ?>\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	
					html += '</tr>';
			
					$("#postVarTable tbody").append(html);
					
					$("#variationParentId").val(data.gId);
					$("#variationParentName").val(data.gTitle);
					
					//Hide this button and the selection
					$("#varParentDiv").addClass("d-none");
			
					//Show the table and the parent input
					$("#varHolder").removeClass("d-none");
					$("#inputVarParent").removeClass("d-none");
				}
					
				else if (data.status == 'error') 
				{
					alert(errorMess);
				}				
			})
			.fail(function(){
				alert(errorMess);
			});
		});
		
		//Choose a parent from selection
		$("#postParentVar").on("select2:select", function (e)
		{
			var gId 		= $('#postParentVar option:selected').val();
			var gName 		= $('#postParentVar option:selected').text();
			var postId 		= '<?php echo $Post->PostID() ?>';
			var errorMess	= '<?php echo __( 'an-error-happened' ) ?>';
			var html   		= "";

			$.ajax(
			{
				url: '<?php echo AJAX_ADMIN_PATH ?>add-single-variation/',
				type: 'POST',
				data: {gId:gId,postId:postId},
				dataType: 'json',
				cache: false
			})
			.done(function(data)
			{
				if (data.status == 'ok') 
				{
					$("#postVarTable tbody").append(data.html);
					
					$("#variationParentId").val(gId);
					$("#variationParentName").val(gName);
					
					//Hide the add button and the selection
					$("#varParentDiv").addClass("d-none");
			
					//Show the table and the parent input
					$("#varHolder").removeClass("d-none");
					$("#inputVarParent").removeClass("d-none");					
				}

				else
				{
					alert(errorMess);
				}				
			})
			.fail(function(){
				alert(errorMess);
			});
		});
		
		//Remove Single Variation
		$(".removePostVarButton").unbind().click(function(e)
		{
			e.preventDefault();
			
			//Ask if they really want to remove this variation
			if( !confirm("<?php echo __( 'are-you-sure' ) ?>"))
			{
				return false;
			}
			
			var id = $(this).data('id');
			var errorMess	= '<?php echo __( 'an-error-happened' ) ?>';
			
			$.ajax(
			{
				url: '<?php echo AJAX_ADMIN_PATH ?>remove-single-variation/',
				type: 'POST',
				data: {id:id},
				dataType: 'json',
				cache: false
			})
			.done(function(data)
			{
				if (data.status == 'ok') 
				{
					$('#varField-row' + id).remove();
					
					//If this is the last item, let them choose another parent
					if ( data.items == 0 ) 
					{
						//Show the add button and the selection
						$("#varParentDiv").removeClass("d-none");
			
						//Hide the table and the parent input
						$("#varHolder").addClass("d-none");
						$("#inputVarParent").addClass("d-none");
						
						$("#variationParentId").val("0");
						$("#variationParentName").val("");
					}
				}
				//Anything else must be an error
				else
				{
					alert(errorMess);
				}				
			})
			.fail(function(){
				alert(errorMess);
			});			
		});
		
		//Delete Variation Group Button
		$("#removeParentGroupButton").unbind().click(function(e)
		{
			e.preventDefault();
			
			//Ask if they really want to remove this group
			if( !confirm("<?php echo __( 'are-you-sure-parent-variation-group' ) ?>"))
			{
				return false;
			}
			
			var groupId = $(this).data('id');
			var errorMess	= '<?php echo __( 'an-error-happened' ) ?>';
			
			$.ajax(
			{
				url: '<?php echo AJAX_ADMIN_PATH ?>remove-variation-group/',
				type: 'POST',
				data: {id:groupId},
				dataType: 'json',
				cache: false
			})
			.done(function(data)
			{
				if (data.status == 'ok') 
				{
					$("#varParentDiv").removeClass("d-none");

					$("#inputVarParent").addClass("d-none");
					
					$("#varHolder").addClass("d-none");
					
					//Clean the table
					$("#postVarTable tbody").html("");
					
					$("#variationParentId").val("0");
					$("#variationParentName").val("");
				}
					
				else if (data.status == 'error') 
				{
					alert(errorMess);
				}				
			})
			.fail(function(){
				alert(errorMess);
			});
		});
		
		//Add new Variation Button
		$("#addAVarButton").unbind().click(function(e)
		{
			var postId 		= $('#postVarList option:selected').val();
			var postName 	= $('#postVarList option:selected').text();
			var errorMess	= '<?php echo __( 'an-error-happened' ) ?>';
			
			var html = "";
			
			if(typeof(postId) == "undefined" || ( postId === "" ) || ( postId === 0 ) )
			{
				$("#addNewPostVariation").modal("hide");
				alert(errorMess);
				return;
			}
			
			confirmChange = true;
		
			html += '<tr id=\"varField-row' + postId + '\">';
	
			html += '  <td class=\"text-center\"><input type=\"text\" id=\"varTitle\" name=\"variations[' + postId + '][title]\" class=\"form-control mb-4\" placeholder=\"red, blue, 128GB, etc...\" value=\"\" /></td>';

			html += '  <td class=\"text-center\"><input type=\"text\" id=\"varpostTitle\" name=\"variations[' + postId + '][postTitle]\" class=\"form-control mb-4\" value=\"' + postName + '\" disabled /></td>';
			
			html += '  <td class=\"text-center\"><input type=\"number\" id=\"varOrder\" name=\"variations[' + postId + '][order]\" class=\"form-control mb-4\" value=\"0\" min=\"0\" /></td>';
	
			html += '  <td class=\"text-center\"><button type=\"button\" id=\"removePostVarButton\" title=\"<?php echo __( 'remove' ) ?>\" data-id=\"' + postId + '\" class=\"btn btn-danger btn-flat btn-sm removePostVarButton\" data-toggle=\"tooltip\" title=\"<?php echo __( 'remove' ) ?>\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	
			html += '</tr>';
			
			$("#postVarTable tbody").append(html);
			
			$("#addNewPostVariation").modal("hide");

		});
		
		//Product Varations
		var parentVar = $("#postParentVar").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>get-variants/",
				data: function (params) {
					var query = {
						site: "<?php echo $Admin->GetSite() ?>",
						lang: "<?php echo $Admin->GetLang() ?>",
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
				return data.text;
			}
		});
		
		var postVarList = $("#postVarList").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>get-posts/",
				data: function (params) {
					var query = {
						postSite	: "<?php echo $Post->Site()->id ?>",
						postID		: "<?php echo $Post->PostID() ?>",
						getParent	: true,
						getDrafts	: false,
						query		: params.term
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
		<?php endif ?>
		
		<?php if ( $Post->IsPage() ) : ?>
		//Page Parent
		var parent = $("#pageParent").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>get-posts/",
				data: function (params) {
					var query = {
						postLang	: "<?php echo $Post->Language()->id ?>",
						postBlog	: "<?php echo $Post->Blog()->id ?>",
						postSite	: "<?php echo $Post->Site()->id ?>",
						postType	: "<?php echo $Post->PostType() ?>",
						postID		: "<?php echo $Post->PostID() ?>",
						pageParent	: "true",
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
					
				if (data.type=="draft") {
					html += '<span class="badge badge-pill badge-light">'+data.type+'</span>';
				}

				return html;
			}
		});
		<?php endif ?>
		
		//Post Parent
		var parent = $("#postParent").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>get-posts/",
				data: function (params) {
					var query = {
						postLang: "<?php echo $Post->Language()->id ?>",
						postBlog: "<?php echo $Post->Blog()->id ?>",
						postSite: "<?php echo $Post->Site()->id ?>",
						postType: "<?php echo $Post->PostType() ?>",
						postID: "<?php echo $Post->PostID() ?>",
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
				
				if (data.type=="draft") {
					html += '<span class="badge badge-pill badge-light">'+data.type+'</span>';
				}
				
				return html;
			}
		});
	});
	</script>
	
	<?php if ( $canProduct ) : ?>
	<!-- Modal for adding a new variation -->
	<div class="modal fade" id="addNewPostVariation" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo __( 'add-a-variation' ) ?></h4>
				</div>
				<div class="modal-body">
					<div id="modalForm-post-detail">
						
						<div class="mb-3">
							<div class="form-group">
								<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="postVarList"><?php echo $L['post'] ?></label>
								<select id="postVarList" style="width: 100%; height:36px;" class="select2"></select>
								<small class="form-text text-muted"><?php echo $L['start-typing-a-title-to-see-a-list-of-suggestions'] ?></small>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button>
					<button id="addAVarButton" type="submit" class="btn btn-primary" data-id=""><?php echo __( 'add' ) ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php endif ?>
	
	<!-- Modal for adding a new interlink -->
	<div class="modal fade" id="addNewInterlinkInEditor" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo __( 'add-link' ) ?></h4>
				</div>
				<div class="modal-body">
					<div id="modalForm-post-detail">
						<small class="form-text text-muted"><?php echo $L['add-interlink-tip'] ?></small>
						
						<div class="mb-3">
							<div class="form-group">
								<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="postInterlink"><?php echo $L['post'] ?></label>
								<select id="postInterlink" style="width: 100%; height:36px;" class="select2">
								</select>
								<small class="form-text text-muted"><?php echo $L['start-typing-a-title-to-see-a-list-of-suggestions'] ?></small>
							</div>
						</div>
						
						<div class="mb-3">
							<div class="form-group">
								<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="linkTarget"><?php echo $L['target'] ?></label>
								<select id="linkTarget" style="width: 100%; height:36px;" class="form-control">
									<option value="self">_self</option>
									<option value="blank">_blank</option>
									<option value="top">_top</option>
								</select>
							</div>
						</div>
						
						<?php 
							$args = array(
								'id' => 'add-interlink-description',
								'label' => __( 'add-the-description' ),
								'name' => 'description',
								'checked' => false, 
								'disabled' => false,
								'tip' => __( 'add-interlink-description-tip' )
							);

							CheckBox( $args );
						?>
						
						<?php 
							$args = array(
								'id' => 'add-interlink-prices',
								'label' => __( 'add-post-prices' ),
								'name' => 'description',
								'checked' => false, 
								'disabled' => false,
								'tip' => __( 'add-post-prices-tip' )
							);

							CheckBox( $args );
						?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button>
					<button id="addNewInterlinkButton" type="submit" class="btn btn-primary" data-id=""><?php echo __( 'add' ) ?></button>
				</div>
			</div>
		</div>
	</div>
	
	<script type="application/javascript">
	$(document).ready(function() {
		var interlink = $("#postInterlink").select2({
				placeholder: "",
				allowClear: true,
				theme: "bootstrap4",
				minimumInputLength: 2,
				ajax: {
					type: "POST",
					url: "<?php echo AJAX_ADMIN_PATH ?>get-posts/",
					data: function (params) {
						var query = {
							postSite	: "<?php echo $Post->Site()->id ?>",
							postID		: "<?php echo $Post->PostID() ?>",
							getParent	: true,
							getDrafts	: false,
							query		: params.term
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
	});
	</script>	
		
	<?php if ( $hasPrices ) : ?>
	<!-- Modal for adding a price list -->
	<div class="modal fade" id="addPriceListInEditor" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo __( 'price-list' ) ?></h4>
				</div>
				<div class="modal-body">
					<div id="modalForm-post-detail">
						
						<div class="mb-3">
							<div class="form-group">
								<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="postPriceList"><?php echo $L['post'] ?></label>
								<select id="postPriceList" style="width: 100%; height:36px;" class="select2">
								</select>
								<small class="form-text text-muted"><?php echo $L['start-typing-a-title-to-see-a-list-of-suggestions'] ?></small>
							</div>
						</div>
	
						<?php 
							$args = array(
								'id' => 'from-this-post',
								'label' => __( 'from-this-post' ),
								'name' => 'from-this-post',
								'checked' => false, 
								'disabled' => false,
								'tip' => __( 'from-this-post-tip' )
							);

							CheckBox( $args );
						?>
						<input type="hidden" id="thisPostId" value="<?php echo $Post->PostID() ?>">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button>
					<button id="addPriceListButton" type="submit" class="btn btn-primary" data-id=""><?php echo __( 'add' ) ?></button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Modal for adding a single price -->
	<div class="modal fade" id="addSinglePriceInEditor" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo __( 'add-price' ) ?></h4>
				</div>
				<div class="modal-body">
					<div id="modalForm-post-detail">
						<small class="form-text text-muted"><?php echo $L['add-price-tip'] ?></small>
						
						<div class="mb-3">
							<div class="form-group">
								<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="singlePrice"><?php echo $L['search'] ?></label>
								<select id="singlePrice" style="width: 100%; height:36px;" class="select2">
								</select>
								<small class="form-text text-muted"><?php echo $L['start-typing-a-title-to-see-a-list-of-suggestions'] ?></small>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button>
					<button id="addSinglePriceButton" type="submit" class="btn btn-primary" data-id=""><?php echo __( 'add' ) ?></button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Modal for adding a best price -->
	<div class="modal fade" id="addBestPriceInEditor" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo __( 'add-price' ) ?></h4>
				</div>
				<div class="modal-body">
					<div id="modalForm-post-detail">
						<small class="form-text text-muted"><?php echo $L['best-price-tip'] ?></small>
						
						<div class="mb-3">
							<div class="form-group">
								<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="bestPrice"><?php echo $L['search'] ?></label>
								<select id="bestPrice" style="width: 100%; height:36px;" class="select2">
								</select>
								<small class="form-text text-muted"><?php echo $L['start-typing-a-title-to-see-a-list-of-suggestions'] ?></small>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button>
					<button id="addBestPriceButton" type="submit" class="btn btn-primary" data-id=""><?php echo __( 'add' ) ?></button>
				</div>
			</div>
		</div>
	</div>

<script type="application/javascript">
$(document).ready(function() {		
		var singlePrice = $("#singlePrice").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>search-prices/",
				data: function (params) {
					var query = {
						postSite	: "<?php echo $Post->Site()->id ?>",
						query		: params.term
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
				
				if ( ( typeof(data.store) !== "undefined" ) || ( data.store !== "" ) )
				{
					html += ' <span class="badge badge-pill badge-light text-right">';
				
					html += data.price;
				
					html += ' - ' + data.store;
					html += '</span>';
				}

				return html;
			}
		});
		
		var bestPrice = $("#bestPrice").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>search-posts-prices/",
				data: function (params) {
					var query = {
						site	: "<?php echo $Post->Site()->id ?>",
						lang	: "<?php echo $Admin->GetLang() ?>",
						query		: params.term
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
				
				if ( ( typeof(data.num) !== "undefined" ) || ( data.num !== "" ) )
				{
					html += ' <span class="badge badge-pill badge-light text-right">';
					html += data.num;
					html += '</span>';
				}

				return html;
			}
		});
		
		var postPriceList = $("#postPriceList").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>get-posts/",
				data: function (params) {
					var query = {
						postSite	: "<?php echo $Post->Site()->id ?>",
						postID		: "<?php echo $Post->PostID() ?>",
						getParent	: true,
						getDrafts	: false,
						query		: params.term
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
});
</script>
<?php endif ?>

<?php 
if ( IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'manage-forms' ) ) :
	$forms = GetAdminForms( $Admin->GetSite(), 'all' ) ?>
	
	<!-- Modal for adding a new form -->
	<div class="modal fade" id="addNewFormInEditor" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo __( 'add-form' ) ?></h4>
				</div>
				<div class="modal-body">
					<div id="modalForm-post-detail">
						<div class="mb-3">
							<div class="form-group">
								<select class="form-control" name="newFormSelection" id="newFormSelection">
								<?php if ( !empty( $forms ) ) :
									foreach ( $forms as $form ) : ?>
									<option value="<?php echo $form['id'] ?>"><?php echo $form['title'] ?><?php if ( $form['form_type'] != 'form' ) : ?> <span class="text-secondary">[<?php echo __( $form['form_type'] ) ?>]</span><?php endif ?></option>
									<?php endforeach ?>
								<?php endif ?>
								</select>
							</div>
						</div>
						
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button>
					<button id="addNewFormButton" type="submit" class="btn btn-primary" data-id=""><?php echo __( 'add' ) ?></button>
				</div>
			</div>
		</div>
	</div>
<?php unset( $forms ) ?>
<?php endif ?>

<?php if ( IsAllowedTo( 'manage-posts' ) ) : ?>
<!-- Modal for adding google map -->
<div class="modal fade" id="addGMapInEditor" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo __( 'add-google-map-iframe' ) ?></h4>
			</div>
			<div class="modal-body">
				<div id="modalForm-post-detail">
					<div class="mb-3">
						<div class="form-group">
							<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="googleMapWidth"><?php echo $L['width'] ?></label>
							<input type="number" class="form-control" id="googleMapWidth" value="600" min="10" max="1024" />
							<small class="form-text text-muted"><?php echo $L['map-width'] ?></small>
						</div>

						<div class="form-group">
							<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="googleMapHeight"><?php echo $L['height'] ?></label>
							<input type="number" class="form-control" id="googleMapHeight" value="400" min="10" max="1024" />
							<small class="form-text text-muted"><?php echo $L['map-height'] ?></small>
						</div>
						
						<div class="form-group">
							<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="googleMapMarker"><?php echo $L['marker'] ?></label>
							<input type="text" class="form-control" id="googleMapMarker" value="" placeholder="New York" />
							<small class="form-text text-muted"><?php echo $L['marker-tip'] ?></small>
						</div>
						
						<div class="form-group">
							<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="googleMapZoom"><?php echo $L['zoom'] ?></label>
							<input type="number" class="form-control" id="googleMapZoom" value="8" min="0" max="21" />
							<small class="form-text text-muted"><?php echo $L['zoom-tip'] ?></small>
						</div>
						
						<div class="form-group">
							<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="googleMapTitle"><?php echo $L['title'] ?></label>
							<input type="text" class="form-control" id="googleMapTitle" value=""/>
							<small class="form-text text-muted"><?php echo $L['map-title-tip'] ?></small>
						</div>
						
						<div class="form-group">
							<label class="mt-4 mb-2 pb-2 border-bottom text-uppercase w-100" for="googleMapCss"><?php echo $L['extra-css-class'] ?></label>
							<input type="text" class="form-control" id="googleMapCss" value=""/>
							<small class="form-text text-muted"><?php echo $L['extra-css-class-tip'] ?></small>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button>
				<button id="addGMapButton" type="submit" class="btn btn-primary" data-id=""><?php echo __( 'add' ) ?></button>
			</div>
		</div>
	</div>
</div>


<!-- Modal for adding a new category -->
<div class="modal fade" id="addNewCategory" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?php echo __( 'add-new-category' ) ?></h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-success d-none success"></div>
				<div class="alert alert-danger d-none error"></div>
				<div id="modalCat-loader" style="text-align: center;">
					<img src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/loading.gif">
				</div>  
				<div id="modalCat-post-detail"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button>
				<button id="addNewCategoryButton" type="submit" class="btn btn-primary" data-id=""><?php echo __( 'add' ) ?></button>
			</div>
		</div>
	</div>
</div>

<script type="application/javascript">
	$(document).on('click', '#addNewCat', function(e)
	{		
		e.preventDefault();
		var post = '<?php echo $Post->PostID() ?>';
		var token = '<?php echo $Admin->GetToken() ?>';
		$('#addNewCategory').find('#modalCat-post-detail').html('');
		$('addNewCategory').find('#modalCat-loader').show();
		
		$.ajax(
		{
			url: '<?php echo AJAX_ADMIN_PATH ?>add-category-form/',
			type: 'POST',
			data: {post:post,token:token},
			dataType: 'html',
			cache: false
		})
		.done(function(data)
		{
			$('#addNewCategory').find('#modalCat-post-detail').html('');    
			$('#addNewCategory').find('#modalCat-post-detail').html(data);
			$('#addNewCategory').find('#modalCat-loader').hide();
		})
		.fail(function(){
			$('#addNewCategory').find('#modalCat-post-detail').html('Error. Please try again...');
			$('#addNewCategory').find('#modalCat-loader').hide();
		});
	});

	$("#addNewCategoryButton").unbind().click(function(e) {
		e.preventDefault();
		var vars = "";
		var title = $("#newCatTitle").val();
		var slug = $("#newCatSlug").val();
		var descr = $("#newCatDescr").val();
		var postId = $("#newCatPostId").val();
		
		$.ajax({
			url: '<?php echo AJAX_ADMIN_PATH ?>add-new-category-form/',
			type: 'post',
			dataType: 'json',
			data: {title:title,slug:slug,descr:descr,postId:postId},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
					$("#addNewCategory").modal("hide");
					return;
				}

				if (json['success'])
				{
					vars += '<div class="form-check" id="catInputs">';
					
					vars += '<input class="form-check-input cat_check" type="checkbox" id="' + json['id'] + '" value="' + json['id'] + '" name="category">';
					
					vars += '<label for="' + json['id'] + '" class="form-check-label">' + json['title'] + '</label>';
					
					vars += '</div>';
					
					$('#catInputDiv').find('input[type=checkbox]:checked').prop('checked', false);
					$('#catInputDiv').append(vars).trigger("create");
					$('#catInputDiv').find('input[id=' + json['id'] + ']').prop('checked', true);
					$("#addNewCategory").modal("hide");
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
	
</script>
<?php endif ?>