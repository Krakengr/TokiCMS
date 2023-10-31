<?php

function frontGraphMedia( $calledFrom = null )
{
	global $Admin;
	
	if ( isset( $_POST['post'] ) && !empty( $_POST['post'] ) )
	{
		$postID = ( is_numeric( $_POST['post'] ) ? $_POST['post'] : SafeFormField( $_POST['post'], true ) );
	}
	else
		$postID = null;
	
	$db 		= db();

	$page 		= ( ( (int) Router::GetVariable( 'pageNum' ) > 0 ) ? (int) Router::GetVariable( 'pageNum' ) : 1 );

	$isFolder 	= ( ( ( Router::GetVariable( 'ajaxFunction' ) == 'media-folder' ) && Router::GetVariable( 'key' ) ) ? true : false );
	
	$calledFrom = ( isset( $_POST['calledFrom'] ) ? $_POST['calledFrom'] : $calledFrom );
	
	$folderId 	= ( $isFolder ? Router::GetVariable( 'key' ) : 0 );
	
	$p = null;
	
	if ( $postID && is_numeric( $postID ) )
	{
		$p = $db->from( 
		null, 
		"SELECT id_lang, id_site
		FROM `" . DB_PREFIX . POSTS . "`
		WHERE (id_post = " . (int) $postID . ")"
		)->single();
	}
	
	$lang = ( $p ? $p['id_lang'] : $Admin->GetLang() );
	$site = ( $p ? $p['id_site'] : $Admin->GetSite() );
	
	//Reload the default site's settings, we need a few vars	
	$S = new Settings( SITE_ID, false );
	
	$shareImagesLangs = $S::IsTrue( 'share_images_langs' );
	$shareImagesSites = $S::IsTrue( 'share_images_sites' );

	//We don't need this anymore
	unset( $S );
	
	$local = $Admin->ImageUpladDir( SITE_ID );
	
	$imageUploadUrl = ( !empty( $local ) ? $local['html'] : SITE_URL . 'uploads' . PS );

	$data = '<div class="row">';
	
	$data .= '<div class="col-md-9">
              <div class="card">
				<div class="card-body"><div class="row">';
	
	if ( $page == 1 )
	{
		$whereString  = '(' . ( $isFolder ? 'id_parent = ' . $folderId : "id_parent = 0" ) . ')';
		$whereString .= ( !$shareImagesLangs ? ' AND (id_lang = ' . $lang . ')' : '' );
		$whereString .= ( !$shareImagesSites ? ' AND (id_site = ' . $site . ')' : '' );
		
		$res = $db->from( 
		null, 
		"SELECT id, name, sef, is_default
		FROM `" . DB_PREFIX . "image_folders`
		WHERE " . $whereString
		)->all();

		if ( $res )
		{
			foreach( $res as $r )
			{
				$data .= '
				<div class="col-sm-3 col-xs-6 text-center">
					<div class="text-center"><a href="' . AJAX_ADMIN_PATH . 'media-folder/id/' . $r['id'] . '/" class="directory" style="vertical-align: middle;"><i class="fa fa-folder fa-5x"></i></a></div>
						<label>' . $r['name'];
				
						if ( !$r['is_default'] && IsAllowedTo( 'manage-attachments' ) )
						{
							$data .= '
								<input type="checkbox" name="data[]" value="folder::' . $r['id'] . '" />';
						}
						
						$data .= '
						</label>
					</div>';
			}
		}
	}
	
	$res = $db->from( null, 
	"SELECT COUNT(id_image) as total
	FROM `" . DB_PREFIX . "images`
	WHERE (id_parent = 0) AND (img_status = 'full') AND (aproved = 1) AND (external_url IS NULL OR external_url = '') AND (" . ( $isFolder ? "id_folder = " . $folderId : "id_folder = 0" ) . ")" . ( !$shareImagesLangs ? " AND (id_lang = " . $lang . ")" : "" ) . ( !$shareImagesSites ? " AND (id_site = " . $site . ")" : "" )
	)->total();
	
	$total = ( $res ? $res : 0 );
	
	$from = (($page * HOMEPAGE_ITEMS) - HOMEPAGE_ITEMS);
	
	$totalPages = ceil( $total / HOMEPAGE_ITEMS );
	
	$hasPrevPage = ( ( $page > 1 ) ? true : false );
	$hasNextPage = ( ( $page < $totalPages ) ? true : false );
	
	$res = $db->from( 
	null, 
	"SELECT id_image, filename, added_time, mime_type
	FROM `" . DB_PREFIX . "images`
	WHERE (img_status = 'full') AND (aproved = 1) AND (external_url IS NULL OR external_url = '') AND (" . ( $isFolder ? "id_folder = " . $folderId : "id_folder = 0" ) . ")" . ( !$shareImagesLangs ? " AND (id_lang = " . $lang . ")" : "" ) . ( !$shareImagesSites ? " AND (id_site = " . $site . ")" : "" ) . "
	ORDER BY id_image DESC
	LIMIT " . $from . ", " . HOMEPAGE_ITEMS
	)->all();
	
	if ( $res )
	{
		foreach( $res as $r )
		{
			if ( $r['mime_type'] == 'image' )
			{
				$imgUrl = FolderUrlByDate( $r['added_time'], $imageUploadUrl ) . $r['filename'];
			}
			
			elseif ( $r['mime_type'] == 'video' )
			{
				$imgUrl = HTML_ADMIN_PATH_THEME . PS .'assets' . PS . 'img' . PS . 'video-filetype.png';
			}
			
			elseif ( $r['mime_type'] == 'audio' )
			{
				$imgUrl = HTML_ADMIN_PATH_THEME . PS .'assets' . PS . 'img' . PS . 'audio-filetype.png';
			}
			
			else
			{
				$imgUrl = HTML_ADMIN_PATH_THEME . PS .'assets' . PS . 'img' . PS . 'generic-filetype.png';
			}
			
			$data .= '<div class="col-sm-3 col-xs-6 text-center">';
			
			$data .= '<a href="javascript:;" class="mr-3 text-secondary photo-gallery-modal-item" id="photo-gallery-modal-item" data-id="' . $r['id_image'] . '"><img src="' . $imgUrl . '" alt="' . $r['filename'] . '" title="' . $r['filename'] . '" width="80" /></a>';

			$data .= '<label>';
			
			if ( IsAllowedTo( 'manage-attachments' ) )
			{
				$data .= '<input type="checkbox" name="data[]" value="image::' . $r['id_image'] . '" />';
			}
			
			$data .= ( ( strlen( $r['filename'] ) > 15 ) ? substr( $r['filename'], 0, 15 ) . '...' : $r['filename'] );

			$data .= '</label>
			</div>';
		}
	}
	
	$data .= '</div>';
	
	if ( $totalPages > 1 )
	{
		$data .= '<ul class="pagination pagination-sm float-right">';
		
		$pagUrl = AJAX_ADMIN_PATH . ( $isFolder ? 'media-folder/id/' . $folderId . '/' : 'media-manager-graph/' );
		
		if ( $hasPrevPage )
		{
			if ( $page > 3 )
				$data .= '<li class="page-item"><a class="page-link" href="' . $pagUrl . 'page/1/">|&lt;</a></li>';
			
			if ( $page > 1 )
				$data .= '<li class="page-item"><a class="page-link" href="' . $pagUrl . 'page/' . ( $page - 1 ) . '/">&lt;</a></li>';
		}
		
		for ( $i = ( $page - 2 ); $i <= ( $page + 3 ); $i++)
		{
			if (($i >= 1) && ( $i <= $totalPages ) ) 
			{
				if ( $i == $page )
					$data .= '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
				
				else
					$data .= '<li class="page-item"><a class="page-link" href="' . $pagUrl . 'page/' . $i . '/">' . $i . '</a></li>';
			}
			
		}
		
		if ( $hasNextPage )
		{
			$data .= '<li class="page-item"><a class="page-link" href="' . $pagUrl . 'page/' . ( $page + 1 ) . '/">&gt;</a></li>';
			
			if ( $totalPages > ( $page + 1 ) )
				$data .= '<li class="page-item"><a class="page-link" href="' . $pagUrl . 'page/' . $totalPages . '/">&gt;|</a></li>';
		}
			
		$data .= '</ul>';
	}
	
	$data .= '</div>
              </div>
            </div>';
	
	$data .= '<div class="col-md-3">';
	$data .= '<div class="media-details">
				<div class="media-thumbnail"></div>
				<div class="media-description"></div>';
	
	$data .= '</div>';
	
	$data .= '</div>';
	
	$data .= '<input type="hidden" name="calledFrom" id="calledFrom" value="' . ( $calledFrom ? $calledFrom : 'null' ) . '">';

	return $data;
}

function frontMedia( $calledFrom = null )
{
	$db 		= db();
	
	$calledFrom = ( isset( $_POST['calledFrom'] ) ? $_POST['calledFrom'] : $calledFrom );
	
	$isFolder = ( ( ( Router::GetVariable( 'ajaxFunction' ) == 'media-folder' ) && Router::GetVariable( 'key' ) ) ? true : false );
	
	$refreshUri = ( !$isFolder ? AJAX_ADMIN_PATH . 'media-manager-graph/' . ( ( Router::GetVariable( 'pageNum' ) <= 1 ) ? '' 
				: 'page/' . Router::GetVariable( 'pageNum' ) . '/' ) : AJAX_ADMIN_PATH . 'media-folder/id/' . Router::GetVariable( 'key' ) . '/' . ( ( Router::GetVariable( 'pageNum' ) <= 1 ) ? '' 
				: 'page/' . Router::GetVariable( 'pageNum' ) . '/' ) );

	$res = null;
	
	if ( $isFolder )
	{
		$res = $db->from( 
		null, 
		"SELECT id_parent
		FROM `" . DB_PREFIX . "image_folders`
		WHERE (id = " . (int) Router::GetVariable( 'key' ) . ")"
		)->single();
	}
	
	$parentUri = ( ( !$isFolder || ( $isFolder && $res && ( $res['id_parent'] == 0 ) ) ) ? AJAX_ADMIN_PATH . 'media-manager-graph/' 
					: AJAX_ADMIN_PATH . 'media-folder/id/' . $res['id_parent'] . '/' );
	
	$canGoToParent = ( $isFolder ? true : false );
	
	$data = '<div class="row">
<div class="col-sm-5">';

if ( $canGoToParent )
	$data .= '<a href="' . $parentUri . '" data-toggle="tooltip" title="' . __('parent' ) . '" id="button-parent" class="btn btn-default"><i class="bi bi-arrow-return-left"></i></a>'; 

$data .= ' <a href="' .$refreshUri . '" data-toggle="tooltip" title="' . __('refresh' ) . '" id="button-refresh" class="btn btn-default"><i class="bi bi-arrow-clockwise"></i></a>';

if ( IsAllowedTo( 'manage-attachments' ) )
{
	$data .= ' <button type="button" data-toggle="tooltip" title="' . __('upload' ) . '" id="button-upload" class="btn btn-primary"><i class="bi bi-cloud-upload"></i></button>';

	$data .= ' <button type="button" data-toggle="popover" title="' . __('import-external-image' ) . '" id="button-external" class="btn btn-warning"><i class="bi bi-cloud-plus"></i></button>';


	if ( !$isFolder || ( $isFolder && $res && ( $res['id_parent'] == 0 ) ) )
	{
		$data .= ' <button type="button" data-bs-toggle="popover" title="' . __('new-folder' ) . '" id="button-folder" class="btn btn-success"><i class="bi bi-folder-plus"></i></button>';
	}

	$data .= ' <button type="button" data-toggle="tooltip" title="' . __('delete' ) . '" id="button-delete" class="btn btn-danger"><i class="bi bi-trash"></i></button>';
}

$data .= '</div>
<div class="col-sm-7">
<div class="input-group">
<input type="text" name="search" value="" placeholder="' . __('search' ) . '..." class="form-control">
<span class="input-group-btn">
<button type="button" data-toggle="tooltip" title="' . __('search' ) . '" id="button-search" class="btn btn-primary"><i class="bi bi-search"></i></button>
</span></div>
</div>
</div>
<input type="hidden" name="postID" id="postID" value="' . ( isset( $_POST['post'] ) ? ( is_numeric( $_POST['post'] ) ? $_POST['post'] : SafeFormField( $_POST['post'], true ) ) : 0 ) . '">';

$data .= '<input type="hidden" name="folderID" id="folderID" value="' . ( $isFolder ? (int) Router::GetVariable( 'key' ) : 0 ) . '">';

$data .= '<input type="hidden" name="calledFrom" id="calledFrom" value="' . ( $calledFrom ? $calledFrom : 'null' ) . '">';

$data .= '<hr />';

return $data;
}

//Search images
function searchGraphMedia( $calledFrom = null )
{
	global $Admin;
	
	$db 		= db();
	
	$calledFrom = ( isset( $_POST['calledFrom'] ) ? $_POST['calledFrom'] : $calledFrom );
	
	$data = '<div class="row">';
	
	if ( isset( $_POST['search_term'] ) && !empty( $_POST['search_term'] ) )
	{
		$postID = ( isset( $_POST['post'] ) ? (int) $_POST['post'] : null );
		$p 		= null;
		
		if ( $postID )
		{
			$p = $db->from( 
			null, 
			"SELECT id_lang, id_site
			FROM `" . DB_PREFIX . POSTS . "`
			WHERE (id_post = " . $postID . ")"
			)->single();
		}
	
		$lang = ( $p ? $p['id_lang'] : $Admin->GetLang() );
		$site = ( $p ? $p['id_site'] : $Admin->GetSite() );
		
		$local = $Admin->ImageUpladDir( SITE_ID );
	
		$imageUploadUrl = ( !empty( $local ) ? $local['html'] : GetDefaultSiteUrl() . 'uploads' . PS );

		//Reload the default site's settings, we need a few vars
		$S = new Settings( $Admin->GetSite(), false);
	
		$shareImagesLangs = $S::IsTrue( 'share_images_langs' );
		$shareImagesSites = $S::IsTrue( 'share_images_sites' );

		//We don't need this anymore
		unset( $S );
		
		$res = $db->from( 
		null, 
		"SELECT id_image, filename, added_time, mime_type
		FROM `" . DB_PREFIX . "images`
		WHERE (img_status = 'full') AND (aproved = 1) AND (external_url IS NULL OR external_url = '') AND (" . ( $isFolder ? "id_folder = " . $folderId : "id_folder = 0" ) . ")" . ( !$shareImagesLangs ? " AND (id_lang = " . $lang . ")" : "" ) . ( !$shareImagesSites ? " AND (id_site = " . $site . ")" : "" ) . " AND (filename LIKE '%" . Sanitize ( $_POST['search_term'], false ) . "%' OR title LIKE '%" . Sanitize ( $_POST['search_term'], false ) . "%')
		LIMIT " . HOMEPAGE_ITEMS
		)->all();

		if ( $res )
		{
			foreach( $res as $r )
			{
				if ( $r['mime_type'] == 'image' )
				{
					$imgUrl = FolderUrlByDate( $r['added_time'], $imageUploadUrl ) . $r['filename'];
				}
				
				elseif ( $r['mime_type'] == 'video' )
				{
					$imgUrl = HTML_ADMIN_PATH_THEME . PS .'assets' . PS . 'img' . PS . 'video-filetype.png';
				}
				
				elseif ( $r['mime_type'] == 'audio' )
				{
					$imgUrl = HTML_ADMIN_PATH_THEME . PS .'assets' . PS . 'img' . PS . 'audio-filetype.png';
				}
				
				else
				{
					$imgUrl = HTML_ADMIN_PATH_THEME . PS .'assets' . PS . 'img' . PS . 'generic-filetype.png';
				}
			
				$data .= '<div class="col-sm-3 col-xs-6 text-center">';
				
				$data .= '<a href="javascript:;" class="mr-3 text-secondary photo-gallery-modal-item" id="photo-gallery-modal-item" data-id="' . $r['id_image'] . '"><img src="' . $imgUrl . '" alt="' . $r['filename'] . '" title="' . $r['filename'] . '" width="80" /></a>';

				$data .= '<label>';
				
				if ( IsAllowedTo( 'manage-attachments' ) )
				{
					$data .= '<input type="checkbox" name="data[]" value="image::' . $r['id_image'] . '" />';
				}
				
				$data .= ( ( strlen( $r['filename'] ) > 15 ) ? substr( $r['filename'], 0, 15 ) . '...' : $r['filename'] );
				
				$data .= '</label>
				</div>';
			}
		}
	}
	
	$data .= '
	<div class="col-md-3">
		<div class="media-details">
			<div class="media-thumbnail"></div>
			<div class="media-description"></div>
		</div>
	</div>';
	
	$data .= '<input type="hidden" name="calledFrom" id="calledFrom" value="' . ( $calledFrom ? $calledFrom : 'null' ) . '">';
	
	$data .= '</div>';
	
	return $data;
}