<script type="text/javascript"><!--

$('#insertMedia').unbind().click(function(e) {
	e.preventDefault();
	var id = $('#imgSize').find(":selected").val();
	var fileId = $('#fileId').val();
	var aling = $('#imgAlign').find(":selected").val();
	var token = '';
	var calledFrom = $('#calledFrom').val();
	var lang = v2['langId'];

	//Make sure calledFrom in not empty
	if ( ( calledFrom != 'null' ) && ( calledFrom.length > 0 ) )
	{
		if ( ( calledFrom == 'editor' ) || ( calledFrom == 'editorjs' ) || ( calledFrom == 'pricePost' ) || ( calledFrom == 'priceCover' ) )
		{
			$.ajax(
			{
				url: '<?php echo AJAX_ADMIN_PATH ?>insert-media-editor/',
				type: 'POST',
				data: {id:id,fileId:fileId,aling:aling,lang:lang,token:token},
				dataType: 'json',
				complete: function() {
					//
				},
				success: function(json) {
					if (json['error']) {
						alert(json['error']);
					}
					
					var editorId = 'main';

					if ( json['data'] )
					{
						if ( calledFrom == 'editorjs' )
						{
							if ( json['data']['mime'] == 'image' )
							{
								var type = 'image';
								var content = {"url":json['data']['url'],"align":json['data']['align'],"caption":json['data']['caption'],"width":json['data']['width'],"imageId":json['data']['id']};

								editorInsertContent(type, content);
							}
							
							else if ( json['data']['mime'] == 'video' )
							{
								///html += "[video id=\""+json['data']['id']+"\"]";
							}
						}
						
						else if ( calledFrom == 'priceCover' )
						{
							var priceId = $('#priceMediaFile').val();
							
							$.ajax(
							{
								url: '<?php echo AJAX_ADMIN_PATH ?>add-cover-price/',
								type: 'POST',
								data: {imId:json['data']['id'],prId:priceId},
								dataType: 'json',
								cache: false
							})
							.done(function(data)
							{
								if ( data.status == 'ok' )
								{
									Toast.fire({
										icon: "success",
										title: "<?php echo __( 'data-updated' ) ?>"
									})
								}
								else
								{
									Toast.fire({
										icon: "error",
										title: "<?php echo __( 'an-error-happened' ) ?>"
									})
								}
							})
							.fail(function(){
								Toast.fire({
									icon: "error",
									title: "<?php echo __( 'an-error-happened' ) ?>"
								})
							});
						}

						else
						{
							if ( calledFrom == 'pricePost' )
							{
								editorId = 'price';
							}
						
							var html = '';
							
							if ( json['data']['mime'] == 'image' )
							{
								html += "[image id=\""+json['data']['id']+"\" width=\""+json['data']['width']+"\" align=\""+json['data']['align']+"\"]";
							}
							
							else if ( json['data']['mime'] == 'video' )
							{
								html += "[video id=\""+json['data']['id']+"\"]";
							}
							
							else if ( json['data']['mime'] == 'audio' )
							{
								html += "[audio id=\""+json['data']['id']+"\"]";
							}
							
							else if ( ( json['data']['mime'] == 'text' ) || ( json['data']['mime'] == 'application' ) || ( json['data']['mime'] == 'compressed' ) )
							{
								html += "[file id=\""+json['data']['id']+"\"]";
							}
							
							else
							{
								var mimeError = '<?php echo __( 'mime-type-file-error' ) ?>';

								alert(mimeError);
							}
							
							editorInsertContent(html,editorId);
						}
						
						$('#addImage').modal('hide');
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
		
		//Maintenance
		else if ( calledFrom == 'maintenance' )
		{
			var imageUrl = $('#file_details_url').val();

			$('#backgPreview').attr('src', imageUrl);
			$('#siteBackgFile').attr('value', imageUrl);
			$('#buttonRemoveBackg').removeClass('d-none');
			$('#addImage').modal('hide');
		}
		
		//Manufacturer
		else if ( calledFrom == 'manufacturer' )
		{
			var imageUrl = $('#file_details_url').val();
			var imageId = $('#file_details_id').val();

			$('#manufactLogoPreview').attr('src', imageUrl);
			$('#manufactLogoFile').attr('value', imageId);
			$('#buttonRemoveLogo').removeClass('d-none');
			$('#addImage').modal('hide');
		}
		
		//Category
		else if ( calledFrom == 'category' )
		{
			var imageUrl = $('#file_details_url').val();
			var imageId = $('#file_details_id').val();

			$('#catLogoPreview').attr('src', imageUrl);
			$('#catLogoFile').attr('value', imageId);
			$('#buttonRemoveLogo').removeClass('d-none');
			$('#addImage').modal('hide');
		}
		
		//Custom post type
		else if ( calledFrom == 'custom-type' )
		{
			var imageUrl = $('#file_details_url').val();
			var imageId = $('#file_details_id').val();

			$('#customLogoPreview').attr('src', imageUrl);
			$('#customLogoFile').attr('value', imageId);
			$('#buttonRemoveLogo').removeClass('d-none');
			$('#addImage').modal('hide');
		}
		
		//Vendor/
		else if ( ( calledFrom == 'vendor' ) || ( calledFrom == 'store' ) )
		{
			var imageUrl = $('#file_details_url').val();
			var imageId = $('#file_details_id').val();

			$('#logoPreview').attr('src', imageUrl);
			$('#logoFile').attr('value', imageId);
			$('#buttonRemoveLogo').removeClass('d-none');
			$('#addImage').modal('hide');
		}
		
		//Site Image
		else if ( calledFrom == 'site-image' )
		{
			var imageUrl = $('#file_details_url').val();
			var id = $('#file_details_id').val();

			$('#siteLogoPreview').attr('src',imageUrl);
			$('#buttonRemoveLogo').removeClass('d-none');
			$('#siteLogoFile').attr('value',id);
			$('#addImage').modal('hide');
		}
		
		//Graph
		else if ( calledFrom == 'graph' )
		{
			var imageUrl = $('#file_details_url').val();

			$('#graphImagePreview').attr('src', imageUrl);
			$('#graphImageFile').attr('value', imageUrl);
			$('#addImage').modal('hide');
			$('#buttonRemoveGraph').removeClass("d-none");
		}
		
		//Gallery
		else if ( calledFrom == 'gallery' )
		{
			var imageUrl = $('#file_details_url').val();
			var id = $('#file_details_id').val();
			
			addGalleryImage(imageUrl, id);
			$('#addImage').modal('hide');
		}
		
		//Cover
		else if ( calledFrom == 'cover' )
		{
			var imageUrl = $('#file_details_url').val();
			var id = $('#file_details_id').val();
			
			$('.thumbnail').removeClass("d-none");
			
			$('#coverImage').attr("src", '');
			$('#internalImage').attr("value", '');
			$('#coverImageID').attr('value', '');
			
			$('#coverImage').attr("src", imageUrl);
			$('#internalImage').attr("value", imageUrl);
			$('#coverImageID').attr('value', id);
			$('#captionImg').removeClass("d-none");
			
			$('#addImage').modal('hide');
		}
	}

	else
	{
		alert('Error. Please try again...');
	}
});

$('a.photo-gallery-modal-item').on('click', function(e)
{
	e.preventDefault();
	var id = $(this).data('id');
	var token = '';
	var calledFrom = $('#calledFrom').val();
	var lang = v2['langId'];

	$.ajax(
	{
		url: '<?php echo AJAX_ADMIN_PATH ?>media-get-single/',
		type: 'POST',
		data: {id:id,lang:lang,token:token},
		dataType: 'json',
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}
			
			if (json['data'])
			{
				$('.media-thumbnail').html('');
				$('.media-description').html('');

				var thumb = '';
				var caption = '<?php echo __( 'file-caption' ) ?>';
				var descr = '<?php echo __( 'file-descr' ) ?>';
				
				if ( json['data']['mime'] == 'image' )
				{
					thumb += '<img style="width:100%;" src="' + json['data']['thumb'] + '" alt="' + json['data']['alt'] + '">';
					
					caption = '<?php echo __( 'image-caption' ) ?>';
					descr = '<?php echo __( 'image-descr' ) ?>';
				}
				
				else if ( json['data']['mime'] == 'video' )
				{
					thumb += '<video controls crossorigin playsinline style="width: 267px; height: 150px;" poster="' + json['data']['videoThumbnailUrl'] + '" data-plyr-config=\'{"controls": ["fullscreen"]}\'><source src="' + json['data']['thumb'] + '" type="video/' + json['data']['ext'] + '" size="267"></video>';
				}
				
				else
				{
					thumb += '<img style="width:100%;" src="' + json['data']['thumb'] + '" alt="' + json['data']['alt'] + '">';
				}

				var html = '<div id="modal-loader-2" style="text-align: center;"><img src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/loading.gif"></div><br /><div id="post-detail-2"></div>';

				html += '<label><?php echo __( 'file-info' ) ?></label><br /><div class="media-name"><div class="form-group"><label><?php echo __( 'url' ) ?></label><input id="file_details_url" type="text" value="' + json['data']['url'] + '" class="form-control" disabled><input id="file_details_id" type="hidden" value="' + id + '"></div></div>';

				if ( json['data']['mime'] == 'video' )
				{
					console.log( json['data'] );
					html += '<hr /><label><?php echo __( 'controls' ) ?></label><br />';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="playLargeVideo" ' + ( ( ( json['data']['playLargeVideo'] === true ) || ( json['data']['playLargeVideo'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="playLargeVideo"><?php echo __( 'play-large' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="playVideo" ' + ( ( ( json['data']['playVideo'] === true ) || ( json['data']['playVideo'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="playVideo"><?php echo __( 'play' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="videoProgress" ' + ( ( ( json['data']['videoProgress'] === true ) || ( json['data']['videoProgress'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="videoProgress"><?php echo __( 'progress' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="currentTime" ' + ( ( ( json['data']['currentTime'] === true ) || ( json['data']['currentTime'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="currentTime"><?php echo __( 'current-time' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="mute" ' + ( ( ( json['data']['mute'] === true ) || ( json['data']['mute'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="mute"><?php echo __( 'mute' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="volume" ' + ( ( ( json['data']['volume'] === true ) || ( json['data']['volume'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="volume"><?php echo __( 'volume' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="fileSettings" ' + ( ( ( json['data']['settings'] === true ) || ( json['data']['settings'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="fileSettings"><?php echo __( 'settings' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="fullscreen" ' + ( ( ( json['data']['fullscreen'] === true ) || ( json['data']['fullscreen'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="fullscreen"><?php echo __( 'fullscreen' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="familyFriendly" ' + ( ( ( json['data']['familyFriendly'] === true ) || ( json['data']['familyFriendly'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="familyFriendly"><?php echo __( 'family-friendly' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="allowEmbed" ' + ( ( ( json['data']['allowEmbed'] === true ) || ( json['data']['allowEmbed'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="allowEmbed"><?php echo __( 'allow-video-to-be-played-directly' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="autoplay" ' + ( ( ( json['data']['autoplay'] === true ) || ( json['data']['autoplay'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="autoplay"><?php echo __( 'autoplay' ) ?></label></div></div></div>';
					
					html += '<hr /><label><?php echo __( 'settings' ) ?></label><br />';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="speed" ' + ( ( ( json['data']['speed'] === true ) || ( json['data']['speed'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="speed"><?php echo __( 'speed' ) ?></label></div></div></div>';
					
					html += '<div class="media-name"><div class="form-group"><div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="loop" ' + ( ( ( json['data']['loop'] === true ) || ( json['data']['loop'] === "true" ) ) ? 'checked' : '' ) + '><label class="custom-control-label" for="loop"><?php echo __( 'loop' ) ?></label></div></div></div>';
				}
				
				html += '<hr /><label><?php echo __( 'file-details' ) ?></label><br />';
				
				html += '<div class="media-name"><div class="form-group"><label><?php echo __( 'name' ) ?></label><input type="text" name="imgGalleryTitle" id="imgGalleryTitle" class="form-control" placeholder="<?php echo __( 'enter-title' ) ?>" value="' + json['data']['title'] + '"></div></div>';
				
				if ( json['data']['mime'] == 'image' )
				{
					html += '<div class="media-name"><div class="form-group"><label><?php echo __( 'alt-text' ) ?></label><input type="text" name="imgGalleryAlt" id="imgGalleryAlt" class="form-control" placeholder="<?php echo __( 'alt-text' ) ?>" value="' + json['data']['alt'] + '"></div></div>';
				}
				else
				{
					//html += '<input type="hidden" id="fileId" name="fileId" value="' + json['data']['id'] + '">';
				}
				
				html += '<input type="hidden" id="fileId" name="fileId" value="' + json['data']['id'] + '">';
				
				if ( json['data']['mime'] == 'video' )
				{
					html += '<div class="media-name"><div class="form-group"><label><?php echo __( 'video-thumbnail' ) ?></label><input type="text" name="videoThumbnailUrl" id="videoThumbnailUrl" class="form-control" placeholder="<?php echo __( 'video-thumbnail' ) ?>" value="' + json['data']['videoThumbnailUrl'] + '"></div></div>';
				}

				html += '<div class="media-name"><div class="form-group"><label>' + caption + '</label><textarea class="form-control" rows="4" placeholder="' + caption + '" name="imgGalleryCaption" id="imgGalleryCaption" cols="50" id="caption">' + json['data']['caption'] + '</textarea></div></div>';
				
				html += '<div class="media-name"><div class="form-group"><label>' + descr + '</label><textarea class="form-control" rows="4" placeholder="' + descr + '" name="imgGalleryDescription" cols="50" id="imgGalleryDescription">' + json['data']['descr'] + '</textarea></div></div>';
				
				if ( ( json['data']['mime'] == 'image' ) && ( calledFrom != 'null' ) && ( calledFrom.length > 0 ) && ( ( calledFrom == 'editor' ) || ( calledFrom == 'editorjs' ) || ( calledFrom == 'pricePost' ) ) )
				{
					html += '<hr /><label><?php echo __( 'image-display-settings' ) ?></label>';
					
					html += json['data']['selectAling'];
					
					html += json['data']['selectSize'];
				}
				
				html += json['data']['script'];
				
				$('.media-thumbnail').append(thumb);
				$('.media-description').append(html);
				$('#insertMedia').removeClass("disabled");
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	
});

$('input[name=\'search\']').on('keydown', function(e) {
	if (e.which == 13) {
		$('#button-search').trigger('click');
	}
});

/* Close Image Modal */
function closeMediaModal() {
	$('#addImage').modal('hide');
}

function addGalleryImage(image, id) {
	html = '<div class="col-md-2 col-sm-3 col-4 photo-gallery-item" data-id="' + id + '"><div class="gallery_image_wrapper"><img src="' + image + '" alt="image"></div><input type="hidden" id="gallery_item_id" name="gallery[' + id + ']" value="' + image + ']"></div>';
	
	$('#list-photos-items').append(html);
}

$('#button-search').on('click', function(e) {
	e.preventDefault();
	var post = $('#postID').val();
	var calledFrom = $('#calledFrom').val();
	var action = 'search';
	var token = '';
	var search_term = $('input[name=\'search\']').val();
	$('#post-detail').html('');
	$('#modal-loader').show();
	$.ajax(
	{
		url: '<?php echo AJAX_ADMIN_PATH ?>media-manager-search/',
		type: 'POST',
		data: {action:action,post:post,token:token,search_term:search_term,calledFrom:calledFrom},
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
});

$('a.directory').on('click', function(e) {
	e.preventDefault();
	var post = $('#postID').val();
	var calledFrom = $('#calledFrom').val();
	var folder = $('#folderID').val();
	var action = 'directory';
	var token = '';
	$('#post-detail').html(''); 
	$('#modal-loader').show();  
	$.ajax(
	{
		url: $(this).attr('href'),
		type: 'POST',
		data: {action:action,post:post,token:token,folder:folder,calledFrom:calledFrom},
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
});

$('#post-detail #button-delete').on('click', function(e) {
	if (confirm('Are you sure?')) {
		$.ajax({
			url: '<?php echo AJAX_ADMIN_PATH ?>media-manager-delete/',
			type: 'post',
			dataType: 'json',
			data: $('input[name^=\'data\']:checked'),
			beforeSend: function() {
				$('#button-delete').prop('disabled', true);
			},
			complete: function() {
				$('#button-delete').prop('disabled', false);
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
					alert(json['success']);

					$('#button-refresh').trigger('click');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
});

$('.pagination a').on('click', function(e) {
	e.preventDefault();
	var post = $('#postID').val();
	var calledFrom = $('#calledFrom').val();
	var action = 'pagination';
	var token = '';
	var page = '';
	$('#post-detail').html(''); 
	$('#modal-loader').show();  
	$.ajax(
	{
		url: $(this).attr('href'),
		type: 'POST',
		data: {action:action,post:post,token:token,page:page,calledFrom:calledFrom},
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
});

$('#button-parent').on('click', function(e) {
	e.preventDefault();
	var post = $('#postID').val();
	var calledFrom = $('#calledFrom').val();
	var action = 'button-refresh';
	var token = '';
	$('#post-detail').html(''); 
	$('#modal-loader').show();  
	$.ajax(
	{
		url: $(this).attr('href'),
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
});

$('#button-folder').popover({
	html: true,
	container: 'body',
	sanitize: false,
	trigger: 'click',
	placement: 'bottom',
	title: '<?php echo __( 'folder-name' ) ?>',
	content: function() {
		html = '<div class="input-group">';
		html += '  <input type="text" name="folder" class="form-control" placeholder="<?php echo __( 'folder-name' ) ?>">';
		html += '  <span class="input-group-btn"><button type="button" title="<?php echo __( 'new-folder' ) ?>" id="button-create" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></span>';
		html += '</div>';

		return html;
	}
});
  
$('#button-external').popover({
	html: true,
	container: 'body',
	sanitize: false,
	trigger: 'click',
	placement: 'bottom',
	title: '<?php echo __( 'import-external-image' ) ?>',
	content: function() {
		html = '<div class="input-group">';
		html += '  <input type="text" id="imageUrl" name="url" class="form-control" placeholder="<?php echo 'https://' ?>">';
		html += '  <span class="input-group-btn"><button type="button" title="<?php echo __( 'import' ) ?>" id="button-import" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></span>';
		html += '</div>';

		return html;
	}
});

$('#button-external').on('shown.bs.popover', function() {
	$('#button-import').on('click', function() {
		var post = $('#postID').val();
		var url = $('#imageUrl').val();
		var parent = $('#folderID').val();
		$.ajax({
			url: '<?php echo AJAX_ADMIN_PATH ?>import-external-image/',
			type: 'post',
			dataType: 'json',
			data: {url:url,post:post,parent:parent},
			beforeSend: function() {
				$('#button-import').prop('disabled', true);
				$('#button-external i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
				$('#button-external').prop('disabled', true);
			},
			complete: function() {
				$('#button-import').prop('disabled', false);
				$('#button-external i').replaceWith('<i class="bi bi-cloud-plus"></i>');
				$('#button-external').prop('disabled', false);
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
					$('#button-import').prop('disabled', false);
				}

				if (json['success']) {
					alert(json['success']);
					$('#button-external').popover('hide');
					$('#button-refresh').trigger('click');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
});

$('#button-folder').on('shown.bs.popover', function() {
	$('#button-create').on('click', function() {
		var post = $('#postID').val();
		var folder = $('input[name=\'folder\']').val();
		var parent = $('#folderID').val();
		$.ajax({
			url: '<?php echo AJAX_ADMIN_PATH ?>create-manager-folder/',
			type: 'post',
			dataType: 'json',
			data: {folder:folder,post:post,parent:parent},
			beforeSend: function() {
				$('#button-create').prop('disabled', true);
			},
			complete: function() {
				$('#button-create').prop('disabled', false);
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
					alert(json['success']);
					$('#button-folder').popover('hide');
					$('#button-refresh').trigger('click');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
});

$('#button-refresh').on('click', function(e) {
	e.preventDefault();
	var post = $('#postID').val();
	var calledFrom = $('#calledFrom').val();
	var action = 'button-refresh';
	var token = '';
	$('#post-detail').html(''); 
	$('#modal-loader').show();  
	$.ajax(
	{
		url: $(this).attr('href'),
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
});
//--></script>

<script type="text/javascript"><!--
$('#button-upload').on('click', function() {
	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file[]" value="" multiple="multiple" /></form>');

	$('#form-upload input[name=\'file[]\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file[]\']').val() != '') {
			clearInterval(timer);
			var post = $('#postID').val();
			var calledFrom = $('#calledFrom').val();
			var action = 'form-post-upload';
			var formData = new FormData($('#form-upload')[0]);
			var parent = $('#folderID').val();
			var lang = v2['langId'];
			var site = v2['siteID'];
			//var blog = v2['blogID'];
			
			formData.append('token', "<?php echo $Admin->GetToken() ?>");
			formData.append('lang', lang);
			formData.append('site', site);
			formData.append('post', post);
			formData.append('parent', parent);
			formData.append('calledFrom', calledFrom);
			$.ajax(
			{
				url: "<?php echo AJAX_ADMIN_PATH ?>media-manager-upload/",
				type: "POST",
				data: formData,
				dataType: 'json',
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$('#button-upload i').replaceWith('<i class="fa fa-spinner fa-spin"></i>');
					$('#button-upload').prop('disabled', true);
				},
				complete: function() {
					$('#button-upload i').replaceWith('<i class="bi bi-cloud-upload"></i>');
					$('#button-upload').prop('disabled', false);
				},
				success: function(json) {
					if (json['error']) {
						alert(json['error']);
						$('#button-upload').prop('disabled', false);
					}

					if (json['success']) {
						alert(json['success']);

						$('#button-refresh').trigger('click');
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});
//--></script>