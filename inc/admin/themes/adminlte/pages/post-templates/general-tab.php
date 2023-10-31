<!-- Title -->
<div class="mb-3">
	<div class="form-group">
		<span class="charcounter" id="titleNum"></span>
		<label class="form-label required" for="postTitle"><?php echo $L['title'] ?></label>
		<input type="text" id="postTitle" name="title" onkeyup="countChar(this, 120, '#titleNum');" class="form-control mb-4" placeholder="<?php echo $L['enter-title'] ?>" value="<?php echo htmlspecialchars( $Post->Title() ) ?>">
	</div>
</div>

<!-- Slug -->
<div class="mb-3">
	<div class="form-group ">
	<?php 
		$postUrl = AdminBuildPostUrl( $Post->GetRawData(), false );
		$postFieldUrl = AdminBuildPostUrl( $Post->GetRawData(), true ); //Skip the slug

		$postPreviewUrl = null;
		
		if ( ( $Post->Status() !== 'published' ) && ( $Post->Status() !== 'deleted' ) && $PreviewUri )
		{
			$postPreviewUrl = $PreviewUri;
		}
	
		if ( empty( $Post->Sef() ) ) : ?>
		<label class="form-label required" for="current-slug"><?php echo $L['permalink'] ?></label>
		<input type="text" id="current-slug" class="form-control" name="slug" placeholder="<?php echo $L['leave-empty-for-autocomplete'] ?>" value="<?php echo $Post->Sef() ?>">
		<script type='text/javascript'>
			// Generate slug when the user type the title 
			//TODO: test keyup funtion
			/*
			$('#postTitle').change(function(e) {
				$.post('<?php echo AJAX_ADMIN_PATH ?>slug/', 
				{ 'slug': $(this).val(), 'lang': '<?php echo $Admin->GetLang() ?>', 'site': '<?php echo $Admin->GetSite() ?>' }, 
					function( data ) {
						$('#current-slug').val(data);
					}
				);
			});*/
		</script>

		<?php else : ?>
		
				<div id="edit-slug-box">
					<label class="control-label required" for="current-slug"><?php echo $L['permalink'] ?>:</label>
					<span id="sample-permalink" class="d-inline-block">
					<a class="permalink" target="_blank" href="<?php echo ( $postPreviewUrl ? $postPreviewUrl : $postUrl ) ?>">
					<span class="default-slug"><?php echo $postFieldUrl ?><span id="editable-post-name"><?php echo $Post->Sef() ?></span>/</span>
					</a>
					</span>
					<span id="edit-slug-buttons">
					<button type="button" class="btn btn-secondary" id="change_slug"><?php echo $L['edit'] ?></button>
					<button type="button" class="save btn btn-secondary" id="btn-ok"><?php echo $L['ok'] ?></button>
					<button type="button" class="cancel button-link"><?php echo $L['cancel'] ?></button>
					
					<?php if ( $postPreviewUrl ) : ?>
					<a class="btn btn-info btn-preview" target="_blank" href="<?php echo $postPreviewUrl ?>"><?php echo $L['preview'] ?></a>
					<?php endif ?>
					
					</span>
					<input type="hidden" id="current-slug" name="slug" value="<?php echo $Post->Sef() ?>">
					<div data-url="<?php echo AJAX_ADMIN_PATH ?>create-slug/" data-view="<?php echo $Post->Site()->url ?>" id="slug_id" data-id="<?php echo $Post->PostID() ?>"></div>
					<input type="hidden" name="slug_id" value="<?php echo $Post->PostID() ?>">
				</div>
			<?php endif ?>
			</div>
</div>
			
<!-- Description -->
<div class="mb-3">
	<div class="form-group"><span class="charcounter" id="descrNum"></span>
		<label for="description" class="form-label"><?php echo $L['description'] ?></label>
		<textarea class="form-control" onkeyup="countChar(this, 400, '#descrNum');" rows="4" placeholder="<?php echo $L['enter-a-short-snippet-from-your-post'] ?>" name="description" cols="50" id="description"><?php echo $Post->Description() ?></textarea>
	 </div>
</div>
		
<!-- Media Button -->
<div class="form-group">
	<div class="d-inline-block editor-action-item">
		<div class="btn-group">
			<div class="margin">
			<?php 
			$idModal = ( ( $Admin->Settings()::Get()['html_editor'] == 'editor-js' ) ? 'imageEditorJsModal' : 'imageEditorModal' );
			
			if ( $canViewAttachments ) : ?>
				<a href="javascript: void(0);" data-toggle="modal" data-target="#addImage" id="<?php echo $idModal ?>" class="btn_gallery btn btn-outline-primary mb-4" data-id="<?php echo $Post->PostID() ?>" data-focus="false"> <i class="far fa-image"></i> <?php echo __( 'add-media' ) ?></a>
			<?php endif ?>
			
			<?php 
				$shorts = array(
					'form' => array( 'title' => __( 'form' ), 'description' => __( 'add-form' ) ),
					'form' => array( 'title' => __( 'form' ), 'description' => __( 'add-form' ) ),
					'contact-form' => array( 'title' => __( 'contact-form' ), 'description' => __( 'add-contact-form' ) ),
					'interlink-post' => array( 'title' => __( 'interlink-post' ), 'description' => __( 'auto-interlink-post' ) ),
					'top-posts' => array( 'title' => __( 'top-posts' ), 'description' => __( 'add-top-posts' ) ),
					'related-posts' => array( 'title' => __( 'related-posts' ), 'description' => __( 'add-related-posts' ) ),
					'gallery-images' => array( 'title' => __( 'gallery-images' ), 'description' => __( 'add-gallery-images' ) ),
					'google-map' => array( 'title' => __( 'google-map' ), 'description' => __( 'add-google-map-iframe' ) )
				);
				
				if ( !empty( $Admin->Settings()::ApiKeys()['gmaps'] ) )
				{
					//$shorts['google-map'] = array( 'title' => __( 'google-map' ), 'description' => __( 'add-google-map' ) );
				}
				
				if ( $Admin->MultiBlog() )
				{
					$shorts['blog-posts'] = array( 'title' => __( 'blog-posts' ), 'description' => __( 'add-blog-posts' ) );
				}
				
				if ( $hasPrices )
				{
					$shorts['price-list'] = array( 'title' => __( 'price-list' ), 'description' => __( 'add-price-list' ) );
					$shorts['single-price'] = array( 'title' => __( 'single-price' ), 'description' => __( 'add-single-price' ) );
					$shorts['best-price'] = array( 'title' => __( 'best-price' ), 'description' => __( 'add-best-price' ) );
				}
				
				asort( $shorts );
				?>
				<!-- Shortcode Button -->
				<a href="javascript: void(0);" id="shortcodeButton" class="btn_gallery btn btn-outline-secondary mb-4 dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false"> <i class="fas fa-code"></i> <?php echo __( 'insert-shortcode' ) ?></a>
				<div class="dropdown-menu">
					<?php foreach ( $shorts as $id => $short ) : ?>
					<a class="dropdown-item shortcodeButton" href="javascript: void(0);" id="<?php echo $id ?>" data-key="<?php echo $id ?>" data-description="<?php echo $short['description'] ?>"><?php echo $short['title'] ?></a>
					<?php endforeach ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Content -->
<div class="mb-3">
	<label for="mainEditor" class="form-label"><?php echo $L['content'] ?></label>
	<?php echo $Editor->Init( $Post->PostRaw(), '600px', 'mainEditor', true ) ?>
</div>

<div id="progressBar" class="progress mb-3 d-none">
	<div class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
	</div>
</div>

<!-- Search Engine Preview -->
<div class="mb-3">
	<div id="seo_wrap" class="widget meta-boxes">
		<div class="widget-title">
			<h4>
				<span><?php echo $L['search-engine-meta-visibility'] ?></span>
			</h4>
		</div>
		<div class="widget-body">
			<div class="seo-preview">
				<p class="default-seo-description  hidden"><?php echo $L['search-engine-meta-visibility-tip'] ?></p>
				<div class="existed-seo-meta ">
					<span class="page-title-seo"> <?php echo $Post->Title() ?> </span>
					<div class="page-url-seo ws-nm">
						<p><?php echo $postFieldUrl . $Post->Sef() ?></p>
					</div>
					<div class="ws-nm">
						<span style="color: #70757a;"><?php echo ( empty( $Post->Added()->raw ) ? date( GetLangInfo( 'dateFormat', $Post->Language()->id ), time() ) : $Post->Added()->time ) ?> - </span>
						<span class="page-description-seo"> <?php echo $Post->Description() ?> </span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>