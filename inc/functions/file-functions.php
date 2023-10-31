<?php defined('TOKICMS') or die('Hacking attempt...');

//
// File Functions
//

#####################################################
#
# Create (and share) an image and three smaller copies from the original function
#
#####################################################
function CreateChildImgs( $source, $imageID, $date, $folder = null, $siteId = SITE_ID, $langId = 0, $userID = 0, $type = 'post', $return = false )
{	
	global $Admin;

	//An empty imageID means that the image couldn't be added to the DB
	if ( !is_numeric( $imageID ) || !file_exists( $source ) )
		return false;
	
	$db = db();
	
	//Get local image dirs
	$local = ImageUpladDir( SITE_ID );

	//And child site(s), if there is any
	$share = ImageUpladDir( $siteId );
	
	$sizes = array( '75', '50', '25' );
	
	$info = pathinfo( $source );

	if ( empty( $info ) || !isset( $info['extension'] ) || empty( $info['extension'] ) )
		return;
	
	$arr = array();

	foreach( $sizes as $_size )
	{
		//Calculate the img size
		$size = CalculateImgSize( $source, $_size );

		if ( empty( $size ) )
			continue;

		$newImg = $info['filename'] . '-' . $size['f'] . '.' . $info['extension'];
	
		//Check if we have this image already
		$imgData = $db->from( null, "
		SELECT id_image
		FROM `" . DB_PREFIX . "images`
		WHERE (filename = :name) AND (id_parent = " . $imageID . ")",
		array( $newImg => ':name' )
		)->single();
		
		//We don't have that image, so try to create it
		if ( !$imgData )
		{
			CreateImage( $source, $folder . $newImg, array( 'w' => $size['w'], 'h' => $size['h'] ) );
		}

		if ( file_exists( $folder . $newImg ) )
		{
			addDbImage( $newImg, $folder, $siteId, 0, $type, 'cropped', 0, null, $imageID );

			$imgUrl = ( !empty( $local ) ? FolderUrlByDate( $date, $local['html'] ) : FolderUrlByDate( $date ) );
			
			$imgUrl .= $newImg;
			
			$arr[] = array( 'file' => $newImg, 'url' => $imgUrl );
		}
	}
	
	//Copy this image(s) to the child site, if needed
	if ( !empty( $arr ) && !empty( $folder ) && !empty( $share ) && isset( $share['share'] ) && $share['share'] )
	{
		PingChildSite( 'sync', 'image', $imageID, $siteId );
	}

	if ( $return )
		return $arr;
	
	return true;
}

#####################################################
#
# Add Image to DB function
#
#####################################################
function addDbImage( $img, $folder, $site, $member, $ptype, $status = 'full', $idFolder = 0, $time = null, $parent = null, $lang = null, $postID = null, $asCover = false )
{
	if ( !file_exists( $folder . $img ) )
		return false;
	
	$db = db();
	
	list( $width, $height, $type ) = @getimagesize( $folder . $img );
	
	$width = ( ( !empty( $width ) && is_numeric( $width ) ) ? $width : 0 );
	$height = ( ( !empty( $height ) && is_numeric( $height ) ) ? $height : 0 );
	
	$mime = GetMimeType( $img );
	
	$extension = pathinfo( strtolower( $img ), PATHINFO_EXTENSION );
	
	$time = ( !empty( $time ) && !is_numeric( $time ) ? strtotime( $time ) : ( ( !empty( $time ) && is_numeric( $time ) ) ? $time : time() ) );
	
	$dbarr = array(
		"id_site" 		=> $site,
		"id_member" 	=> $member,
		"width" 		=> $width,
		"height" 		=> $height,
		"img_type" 		=> $ptype,
		"file_ext" 		=> $extension,
		"mime_type" 	=> $mime,
		"img_status" 	=> $status,
		"filename" 		=> $img,
		"id_folder" 	=> $idFolder,
		"size" 			=> filesize( $folder . $img ),
		"added_time" 	=> $time,
		"id_parent" 	=> ( $parent ? $parent : 0 ),
		"id_lang" 		=> ( $lang ? $lang : 0 ),
		"id_post" 		=> ( $postID ? $postID : 0 )
	);

	$q = $db->insert( 'images' )->set( $dbarr, null, true );

	if ( !$q )
		return null;
	
	if ( $asCover && $postID )
	{
		AddImageAsCover( $postID, $q, $member );
	}

	return $q;
}

#####################################################
#
# Calculate the image size function
#
#####################################################
function CalculateImgSize( $file, $pcnt = 75 )
{
	if ( !file_exists( $file ) || !is_file( $file ) )
		return null;

	list( $originalWidth, $originalHeight ) = @getimagesize( $file );
	
	if ( !is_numeric( $originalWidth ) )
		$originalWidth = @imagesx( $file );
	
	if ( !is_numeric( $originalHeight ) )
		$originalHeight = @imagesy( $file );
	
	$targetWidth = round( (int) $originalWidth * ( $pcnt / 100 ) );
	$targetHeight = round( (int) $originalHeight * ( $pcnt / 100 ) );

	return array( 'w' => $targetWidth, 'h' => $targetHeight, 'f' => $targetWidth . 'x' . $targetHeight );
}

//Get the allowed extensions
function AllowedExt( $siteId = SITE_ID )
{
	if ( $siteId == SITE_ID )
	{
		$allowed = Settings::Get()['allowed_extensions'];
	}
	
	else
	{
		$S = new Settings( $siteId, false );
		
		$allowed = $S::Get()['allowed_extensions'];
	}
	
	if ( empty( $allowed ) )
		return null;
	
	if ( strpos( $allowed, ',' ) !== false )
	{
		$allowed = explode( ',', $allowed );
		
		if ( empty( $allowed ) )
			return null;
	}
	
	return $allowed;
}

//Delete an image from cover
function DelCoverImage( $postId = null, $imgId = null )
{
	if ( empty( $postId ) && empty( $imgId ) )
		return;
	
	$db = db();
	
	$q = null;
	
	if ( $postId )
	{
		if ( empty( $imgId ) )
		{
			$q = $db->delete( 'image_attachments' )->where( "post_id", $postId )->run();
		}
		else
		{
			$q = $db->delete( 'image_attachments' )->where( "post_id", $postId )->where( "image_id", $imgId )->run();
		}
	}
	
	//This is a bit different method
	elseif ( $imgId )
	{
		//Search for posts that have this image as cover
		$dt = $db->from( 
		null, 
		"SELECT post_id
		FROM `" . DB_PREFIX . "image_attachments`
		WHERE (image_id = " . (int) $imgId . ")"
		)->all();
		
		if ( !$dt )
			return;
		
		//No update and empty the cover data
		foreach( $dt as $p )
		{
			$db->update( POSTS )->where( 'id_post', $p['post_id'] )->set( 'cover_img', '' );
		}
		
		//Delete any keys this image has
		$q = $db->delete( 'image_attachments' )->where( "image_id", $imgId )->run();
	}

	return $q;
}

#####################################################
#
# Create Image function
#
#####################################################
function CreateImage( $imgSource, $newImage, $resz = array(), $new = true )
{	
	if ( !file_exists( $imgSource ) )
		return false;

	require_once ( TOOLS_ROOT . 'SimpleImage' . DS . 'src' . DS . 'claviska' . DS . 'SimpleImage.php');

	try
	{
		$image = new \claviska\SimpleImage();
		
		if ( !empty( $resz ) && !empty( $resz['w'] ) && !empty( $resz['h'] ) )
		{
			$image->fromFile( $imgSource )->autoOrient()->resize( $resz['w'], $resz['h'] )->toFile( $newImage );
		}
		
		else
		{
			$image->fromFile( $imgSource )->autoOrient()->toFile( $newImage );
		}
		
		return array( 'success' => true );
	}

	catch(Exception $err)
	{
		return array( 'error' => $err->getMessage() );
	}
}

#####################################################
#
# Copy Image function
#
#####################################################
function CopyImage( $sourceFile, $targetPath, $targetFilename = null, $ext = true, $type = 'image' )
{
	if ( !$ext && !file_exists( $sourceFile ) )
		return false;

	$name = pathinfo( $sourceFile );
	
	if ( empty( $name ) || !isset( $name['extension'] ) || empty( $name['extension'] ) )
	{
		return false;
	}
	
	$allowed = AllowedExt();
	
	//Make sure we allow the extension
	if ( empty( $allowed ) || !in_array( $name['extension'], $allowed ) )
	{
		return false;
	}
	
	if ( !$targetFilename )
	{
		$targetFilename = URLify( $name['filename'] ) . '.' . $name['extension'];
	}
	
	$fileToCopy = $targetPath . $targetFilename;
	
	if ( file_exists( $fileToCopy ) )
		return $targetFilename;
	
	if ( $ext )
	{
		if ( !CopyRemoteFile( $sourceFile, $fileToCopy, true ) )
			return false;
	}
	
	else
	{
		if ( $type != 'image' )
		{
			if ( !CopyRemoteFile( $sourceFile, $targetPath . $targetFilename, false ) )
				return false;
		}
	
		else
		{
			require_once ( TOOLS_ROOT . 'SimpleImage' . DS . 'src' . DS . 'claviska' . DS . 'SimpleImage.php');
			
			try
			{
				// Create a new SimpleImage object
				$image = new \claviska\SimpleImage();
				
				$image
					->fromFile( $sourceFile )             // load image file
					->autoOrient()                              // adjust orientation based on exif data
					->toFile( $targetPath . $targetFilename );  // convert and save the file

			} catch(Exception $err)
			{
				echo $err->getMessage();
				return false;
			}
			
			@unlink( $sourceFile );
		}
	}

	return $targetFilename;
}

#####################################################
#
# Get Site Dirs function
#
#####################################################
function ImageUpladDir( $siteId )
{
	//Load the default settings
	$S = new Settings( SITE_ID, false );

	//There is no need to load the settings if the current site is the default
	if ( $siteId == SITE_ID )
	{
		return array(
			'html' => ( ( $S::Get()['images_html'] != '' ) ? $S::Get()['images_html'] : SITE_URL . 'uploads' . PS ),
			'root' => ( ( $S::Get()['images_root'] != '' ) ? $S::Get()['images_root'] : ROOT . 'uploads' . DS ),
			'share' => false
		);
	}

	//Continue and load the site's settings if needed
	$Settings = new Settings( $siteId, false );
		
	//This is not going to happen, but better safe than sorry
	if ( !$Settings )
	{
		return array(
			'html' => ( !empty( $S::Get()['images_html'] ) ? $S::Get()['images_html'] : $S::Site()['url'] . 'uploads' . PS ),
			'root' => ( !empty( $S::Get()['images_root'] ) ? $S::Get()['images_root'] : $S::Site()['url'] . 'uploads' . DS ),
			'share' => false
		);
	}

	$data = Json( $Settings::Site()['share_data'] );

	if ( empty( $data ) || !isset( $data['sync_uploads'] ) || !$data['sync_uploads'] )
	{
		return array(
			'html' => ( !empty( $Settings::Get()['images_html'] ) ? $Settings::Get()['images_html'] : $Settings::Site()['url'] . 'uploads' . PS ),
			'root' => ( !empty( $Settings::Get()['images_root'] ) ? $Settings::Get()['images_root'] : $Settings::Site()['url'] . 'uploads' . DS ),
			'share' => false
		);
	}

	return array(
		'html' => ( !empty( $S::Get()['images_html'] ) ? $S::Get()['images_html'] : $S::Site()['url'] . 'uploads' . PS ),
		'root' => ( !empty( $S::Get()['images_root'] ) ? $S::Get()['images_root'] : $S::Site()['url'] . 'uploads' . DS ),
		'share' => true,
	);
}

#####################################################
#
# Add External image to DB function
#
#####################################################
function addExternalImage( $file, $id, $langId, $blogId, $siteId, $userId, $imgType, $title = null, $time = null, $cover = true )
{
	$mime = GetMimeType( $file );
	
	$width = $height = 0;
	
	$db = db();
	
	if ( $mime == 'image' ) 
	{
		list( $width, $height, $type ) = @getimagesize( $file );
		$width = ( ( !empty( $width ) && is_numeric( $width ) ) ? $width : 0 );
		$height = ( ( !empty( $height ) && is_numeric( $height ) ) ? $height : 0 );
	}
	
	$time = ( !empty( $time ) ? $time : time() );
	$type = ( !empty( $type ) ? getimageTypes( $type ) : '' );

	$name = pathinfo( $file );

	$title = ( $title ? $title : URLify( $name['filename'] ) );
	
	//Get the code of this lang
	$la = $db->from( 
	null, 
	"SELECT code
	FROM `" . DB_PREFIX . "languages`
	WHERE (id = " . (int) $langId . ")"
	)->single();
	
	if ( !$la )
	{
		global $Admin;
		
		$langCode = ( $Admin ? $Admin->LangCode() : Settings::LangData()['lang']['code'] );
	}
	else
	{
		$langCode = $la['code'];
	}
	
	$s = array(
			$langCode => array(
				'title' 	=> htmlentities( $title ),
				'alt' 		=> '',
				'descr' 	=> '',
				'caption' 	=> '',
			)
	);
	
	$dbarr = array(
		"id_post" 		=> $id,
		"id_lang" 		=> $langId,
		"id_blog" 		=> $blogId,
		"id_site" 		=> $siteId,
		"id_member" 	=> $userId,
		"width" 		=> $width,
		"height" 		=> $height,
		"img_type" 		=> $imgType,
		"file_ext" 		=> $type,
		"mime_type" 	=> $mime,
		"added_time" 	=> $time,
		"external_url" 	=> $file,
		"filename" 		=> $name['filename'] . $type,
		"trans_data" 	=> json_encode( $s, JSON_UNESCAPED_UNICODE )
	);

	$q = $db->insert( 'images' )->set( $dbarr );
	
	if ( $q )
	{
		$im = $db->lastId();
	}
	else
	{
		return null;
	}

	//Set this image as cover
	if ( $cover )
	{
		SetCoverImg( $im, $id, $userId );
	}
	
	return $im;
}

#####################################################
#
# Add Local image to DB function
#
#####################################################
function addLocalImage( $filename, $postId, $langId, $blogId, $siteId, $userId, $imgType, $title, $time, $cover = false )
{
	$postId = (int) $postId;
	
	$db = db();
	
	$folder = FolderRootByDate( $time );
	
	//Query: image
	$data = $db->from( 
	null, 
	"SELECT id_image
	FROM `" . DB_PREFIX . "images`
	WHERE (id_post = " . $postId . ") AND (filename = :filename)",
	array( $filename => ':filename' )
	)->single();
	
	//Check if we already have this image as cover
	if ( $cover && $data )
	{
		$imgId = $data['id_image'];
		
		$data = $db->from( 
		null, 
		"SELECT id_attach
		FROM `" . DB_PREFIX . "image_attachments`
		WHERE (id_post = " . $postId . ") AND (image_id = " . $imgId . ")"
		)->single();
	}

	if ( $data )
		return;
	
	$mime = GetMimeType( $filename );
	
	$width = $height = 0;
	
	if ( $mime == 'image' ) 
	{
		list( $width, $height, $type ) = @getimagesize( $folder . $filename );
	
		$filesize = filesize( $folder . $filename );
		$width = ( ( !empty( $width ) && is_numeric( $width ) ) ? $width : 0 );
		$height = ( ( !empty( $height ) && is_numeric( $height ) ) ? $height : 0 );
	}
	
	$type = ( !empty( $type ) ? getimageTypes( $type ) : '' );
	
	//Get the code of this lang
	$la = $db->from( 
	null, 
	"SELECT code
	FROM `" . DB_PREFIX . "languages`
	WHERE (id = " . $langId . ")"
	)->single();
	
	if ( !$la )
	{
		global $Admin;
		
		$langCode = ( $Admin ? $Admin->LangCode() : Settings::LangData()['lang']['code'] );
	}
	else
	{
		$langCode = $la['code'];
	}
	
	$s = array(
			$langCode => array(
				'title' 	=> htmlentities( $title ),
				'alt' 		=> '',
				'descr' 	=> '',
				'caption' 	=> '',
			)
	);
	
	$dbarr = array(
		"id_post" 		=> $postId,
		"id_lang" 		=> $langId,
		"id_blog" 		=> $blogId,
		"id_site" 		=> $siteId,
		"id_member" 	=> $userId,
		"width" 		=> $width,
		"height" 		=> $height,
		"img_type" 		=> $imgType,
		"file_ext" 		=> $type,
		"mime_type" 	=> $mime,
		"added_time" 	=> $time,
		"filename" 		=> $filename,
		"size" 			=> ( $filesize ? $filesize : 0 ),
		"trans_data" 	=> json_encode( $s, JSON_UNESCAPED_UNICODE )
	);

	$q = $db->insert( 'images' )->set( $dbarr );
	
	if ( $q )
	{
		$id = $db->lastId();
	}
	else
	{
		return null;
	}
	
	if ( !$id )
		return null;
	
	//Set this image as cover
	if ( $cover )
	{
		SetCoverImg( $id, $postId, $userId );
	}

	return $id;
}

#####################################################
#
# Build Image Array function
#
#####################################################
function BuildImageArray( $id )
{
	if ( empty( $id ) )
	{
		return null;
	}
	
	$data = array();
	
	$db = db();
	
	//Query: image
	$_img = $db->from( null, "
	SELECT id_image, filename, width, height, size, mime_type, title, alt, descr, caption, added_time, external_url
	FROM `" . DB_PREFIX . "images`
	WHERE (id_image = " . $id . ")"
	)->single();
	
	if ( !$_img )
		return $data;

	$data['default'] = array(
			'imageWidth' => $_img['width'],
			'imageHeight' => $_img['height'],
			'imageUrl' => ( !empty( $_img['external_url'] ) ? $_img['external_url'] : FolderUrlByDate( $_img['added_time'] ) . $_img['filename'] ),
			'imageId' => $_img['id_image'],
			'imageFilename' => $_img['filename'],
			'mimeType' => $_img['mime_type'],
			'imageCaption' => stripslashes( htmlspecialchars( $_img['caption'] ) ),
			'imageTitle' => stripslashes( htmlspecialchars( $_img['title'] ) ),
			'imageAlt' => stripslashes( htmlspecialchars( $_img['alt'] ) ),
			'imageDescr' => stripslashes( htmlspecialchars( $_img['caption'] ) )
	);
	
	//Query: image
	$imgs = $db->from( null, "
	SELECT id_image, filename, width, height, size, mime_type, title, alt, descr, caption
	FROM `" . DB_PREFIX . "images`
	WHERE (id_parent = " . $_img['id_image'] . ") ORDER BY width ASC"
	)->all();
	
	if ( $imgs )
	{
		foreach( $imgs as $img )
		{
			$data[$img['width']] = array(
					'imageWidth' => $img['width'],
					'imageHeight' => $img['height'],
					'imageUrl' => FolderUrlByDate( $_img['added_time'] ) . $img['filename'],
					'imageId' => $img['id_image'],
					'mimeType' => $_img['mime_type'],
					'imageFilename' => $img['filename'],
					'imageCaption' => stripslashes( htmlspecialchars( $img['caption'] ) ),
					'imageTitle' => stripslashes( htmlspecialchars( $img['title'] ) ),
					'imageAlt' => stripslashes( htmlspecialchars( $img['alt'] ) ),
					'imageDescr' => stripslashes( htmlspecialchars( $img['descr'] ) )
			);
		}
	}

	return $data;
}

function GetMimeType( $file )
{
	include ( ARRAYS_ROOT . 'generic-arrays.php');
	
	$types = $validMediaTypes;
	
	$extension = pathinfo( strtolower( $file ), PATHINFO_EXTENSION );
	
	if ( empty( $extension ) )
		return false;
	
	$mime = null;
	
	foreach( $types as $type => $arr )
	{
		if ( !empty( $arr['data'] ) && in_array( $extension, $arr['data'] ) )
		{
			$mime = $arr['name'];
			break;
		}
	}
	
	return $mime;
}

#####################################################
#
# Scan Dirs function (Generic)
#
# This function is being used to scan a dir and returns the results as an array
#
#####################################################
function ScanDirs( $dir, $scanSubFolders = false )
{
	if( !is_dir( $dir ) )
		return false;
	
	//Scan the folder for files
	$files = scandir( $dir );
	
	//Remove the dots from the array
	unset( $files[array_search( '.', $files, true )] );
	unset( $files[array_search( '..', $files, true )] );
	
	// prevent empty array
    if ( empty( $files ) )
        return;
	
	$arr = array();

	foreach( $files as $file )
	{
		$arr[] = $file;
	}
	
	return $arr;
}

#####################################################
#
# Convert Image To WebP (WIP/TODO)
#
#####################################################
function ConvertImageToWebP( $source, $destination, $quality = 80 ) 
{
    $extension = pathinfo( $source, PATHINFO_EXTENSION );

	if ( $extension == 'jpeg' || $extension == 'jpg' ) 
    	$image = imagecreatefromjpeg($source);

    elseif ( $extension == 'gif' ) 
		$image = imagecreatefromgif( $source );
    
	elseif ( $extension == 'png' ) 
		$image = imagecreatefrompng( $source );
	
	else
		return false;

	return imagewebp( $image, $destination, $quality );
}

//Get the child images from an image
function GetChildImages( $id )
{
	$db = db();
	
	//Query: image
	$imgs = $db->from( null, "
	SELECT id_lang, id_site, filename, added_time
	FROM `" . DB_PREFIX . "images`
	WHERE (id_parent = " . (int) $id . ")
	ORDER BY width ASC"
	)->all();
	
	return $imgs;
}

//Delete an image and its child images by its ID
function DeleteChildImages( $id, $time )
{
	if ( !is_numeric( $id ) || !is_numeric( $time ) )
		return array( 'title' => 'Error', 'num' => '400', 'message' => 'Bad Request' );
	
	$db = db();
	
	//Query: image
	$imgs = $db->from( null, "
	SELECT filename
	FROM `" . DB_PREFIX . "images`
	WHERE (id_parent = " . (int) $id . ")"
	)->all();

	if ( $imgs )
	{
		foreach( $imgs as $img )
		{
			$file = FolderRootByDate( $time ) . $img['filename'];

			@unlink( $file );
		}
	}
	
	$db->delete( 'images' )->where( "id_parent", $id )->run();

	return array( 'ok' => true );
}

//Delete an image and its child images by its ID
function DeleteImage( $id )
{
	if ( !is_numeric( $id ) )
		return array( 'title' => 'Error', 'num' => '400', 'message' => 'Bad Request' );
	
	$db = db();
	
	$imgData = $db->from( null, "
	SELECT filename, added_time
	FROM `" . DB_PREFIX . "images`
	WHERE (id_image = " . (int) $id . ") AND (id_site = " . SITE_ID . ")"
	)->single();
	
	if ( !$imgData )
		return array( 'title' => 'No Content', 'num' => '204', 'message' => 'Nothing Found for this ID' );
	
	$file = FolderRootByDate( $imgData['added_time'] ) . $imgData['filename'];
	
	$q = $db->delete( 'images' )->where( "id_image", $id )->run();

	//Delete and its childs
	if ( $q )
	{
		@unlink( $file );

		//Continue and delete the child images
		$imgs = $db->from( null, "
		SELECT filename, id_image
		FROM `" . DB_PREFIX . "images`
		WHERE (id_parent = " . (int) $id . ")"
		)->all();

		if ( $imgs )
		{
			foreach( $imgs as $img )
			{
				$file = FolderRootByDate( $imgData['added_time'] ) . $img['filename'];

				@unlink( $file );
			}
		}
		
		$db->delete( 'images' )->where( "id_parent", $id )->run();
		
		return array( 'ok' => true );
	}

	return array( 'title' => 'No Content', 'num' => '204', 'message' => 'Nothing Found' );
}

//Get all the themes function
function GetThemes( $type = 'normal' )
{
	$themes = array();

	//Load the folders first
	$files = ScanDirs( THEMES_ROOT );
		
	if ( empty( $files ) )
		return null;
		
	foreach( $files as $file )
	{
		if ( !is_dir( THEMES_ROOT . $file . DS ) )
			continue;
			
		$themeDir = THEMES_ROOT . $file . DS;
			
		$dirFiles = ScanDirs( THEMES_ROOT . $file . DS);
			
		if ( empty( $dirFiles ) )
			continue;

		foreach( $dirFiles as $dirFile )
		{
			$themeData = array();
				
			$subFile = THEMES_ROOT . $file . DS . $dirFile;
				
			if ( ( strpos( $dirFile, 'index.php' ) === false ) && ( strpos( $dirFile, 'metadata.json' ) === false ) )
				continue;
					
			if ( strpos( $dirFile, 'metadata.json' ) !== false )
			{
				//Open the metadata file
				$meta = json_decode( file_get_contents( $subFile ), TRUE );
					
				if ( empty( $meta ) )
					continue;
						
				if ( ( $type != 'all' ) && ( $meta['type'] != $type ) )
					continue;
					
				$langFile = $themeDir . 'languages' . DS . 'en.json';

				if ( file_exists( $langFile ) )
				{
					$themeData = json_decode( file_get_contents( $langFile ), TRUE );
					
					$options = ( ( isset( $themeData['options-data'] ) && !empty( $themeData['options-data'] ) ) ? $themeData['options-data'] : null );
						
					if ( !empty( $themeData ) )
						$themes[$file] = array( 'title' => $themeData['theme-data']['name'], 'description' => $themeData['theme-data']['description'], 'options-lang' => $options, 'data' => $meta );
				}
			}
		}
	}
	
	return $themes;
}

#####################################################
#
# Get the type of the image based on its number function
#
#####################################################
function getimageTypes( $type )
{
	$types = array(
			'1' => 'GIF',
			'2' => 'JPG',
			'3' => 'PNG',
			'4' => 'SWF',
			'5' => 'PSD',
			'6' => 'BMP',
			'7' => 'TIFF',//(intel byte order)
			'8' => 'TIFF',//(motorola byte order)
			'9' => 'JPC',
			'10' => 'JP2',
			'11' => 'JPX',
			'12' => 'JB2',
			'13' => 'SWC',
			'14' => 'IFF',
			'15' => 'WBMP',
			'16' => 'XBM'
			
	);
	
	return ( isset( $types[$type] ) ? strtolower( $types[$type] ) : '' );
}

//Set an image as cover
function SetCoverImg( $id, $postId, $userId )
{
	$db = db();
	
	$ex = $db->from( null, "
	SELECT id_attach
	FROM `" . DB_PREFIX . "image_attachments`
	WHERE (post_id = " . (int) $postId . ")"
	)->single();

	if ( !$ex )
	{
		$dbarr = array(
			"post_id" 	=> $postId,
			"image_id" 	=> $id,
			"user_id" 	=> $userId
		);

		$db->insert( 'image_attachments' )->set( $dbarr );
	}
	
	else
	{
		$dbarr = array(
            "image_id" 	=> $id,
			"user_id" 	=> $userId
        );

		$db->update( 'image_attachments' )->where( 'id_attach', $ex['id_attach'] )->set( $dbarr );
	}
}

//Update Site Stats From A File
function UpdateSiteStats()
{
	if ( !Settings::IsTrue( 'enable_stats' ) || !file_exists( STATS_FILE ) )
		return;
	
	$data = OpenFileDB( STATS_FILE );

	if ( empty( $data ) )
		return;
	
	$db = db();
	
	$stats = Json( Settings::Get()['stats_data'] );
	
	$log_user_agents = ( isset( $stats['log_full_user_agent_string'] ) ? $stats['log_full_user_agent_string'] : false );
	
	foreach( $data as $date => $ip )
	{
		foreach( $ip as $p )
		{
			$c = ' ';
			
			if ( $log_user_agents )
			{
				$c .= "AND user_agent = '" . $p['user_agent'] . "' ";
			}
			
			else
			{
				foreach ( array( 'browser', 'version', 'platform' ) as $key )
				{
					$c .= "AND " . $key . " = '" . $key . "' ";
				}
			}
			
			$c .= "AND (TIMEDIFF( '" .  $time . "', start_time ) < '00:30:00')";
			
			$query = "SELECT id FROM `" . DB_PREFIX . "stats` WHERE (id_site = " . SITE_ID . ") AND (date = '" . $date . "')
			AND (remote_ip = '" . $ip . "')" . $c;

			//Query: stats
			$stat = $db->from( null, $query )->single();

			$statId = null;

			if ( $stat )
			{
				$dbarr = array(
					"hits" => "hits + " . $p['hits'],
					"end_time" => $p['start_time']
				);

				$q = $db->update( 'stats' )->where( 'id', $stat['id'] )->set( $dbarr );
				
				if ( $q )
				{
					$statId = $stat['id'];
				}
			}
			
			else
			{
				$dbarr = array(
					"remote_ip" 	=> $p['remote_ip'],
					"country" 		=> $p['country'],
					"language" 		=> $p['language'],
					"domain" 		=> $p['domain'],
					"referrer" 		=> $p['referrer'],
					"search_terms" 	=> $p['search_terms'],
					"user_agent" 	=> $p['user_agent'],
					"platform" 		=> $p['platform'],
					"browser" 		=> $p['browser'],
					"version" 		=> $p['version'],
					"date" 			=> $p['date'],
					"start_time" 	=> $p['start_time'],
					"end_time" 		=> $p['end_time'],
					"offset" 		=> $p['offset'],
					"hits" 			=> $p['hits'],
					"id_site" 		=> SITE_ID
				);
					
				$put = $db->insert( 'stats' )->set( $dbarr );
				
				if ( $put )
				{
					$statId = $db->lastId();
				}
			}
			
			if ( $statId && !empty( $p['resource'] ) )
			{
				foreach( $p['resource'] as $res )
				{
					$dbarr = array( 'resource' => array( "CONCAT( :resource, '\\n' )", $res ) );
					
					$db->update( 'stats' )->where( 'id', $statId )->set( $dbarr );
				}
			}
		}

		unset( $data[$date] );
	}

	WriteFileDB ( $data, STATS_FILE );
}

//Update Posts Views From A File
function UpdatePostsViews()
{
	if ( !file_exists( POSTS_VIEWS_FILE ) )
		return;
	
	$data = OpenFileDB( POSTS_VIEWS_FILE );

	if ( empty( $data ) )
		return;
	
	$db = db();
	
	foreach( $data as $id => $p )
	{
		$dbarr = array(
            "last_time_viewed" 	=> $p['time'],
			"views" 			=> $p['views']
        );

		$q = $db->update( POSTS )->where( 'id_post', $id )->set( $dbarr );

		if ( $q )
		{
			unset( $data[$id] );
		}
	}
	
	WriteFileDB ( $data, POSTS_VIEWS_FILE );
}

//Opens a DB file
function OpenFileDB( $file, $keys = false )
{
	if ( !$keys )
		return ( file_exists( $file ) ? json_decode( file_get_contents( $file, false, null, 45 ), true ) : array() );
		
	else
	{
		$data = ( file_exists( $file ) ? json_decode( file_get_contents( $file, false, null, 45 ), true ) : array() );

		return array_keys( $data );
	}
}

//Writes data to a file, as a DB
function WriteFileDB ( $data, $file, $pretty = true ) 
{
	$data = ( $pretty ? json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE | JSON_PRETTY_PRINT ) : json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE ) );

	$data_dt = "<?php defined('TOKICMS') or die('Error.'); ?>" . PHP_EOL . $data;
		
	return file_put_contents( $file, $data_dt, LOCK_EX);
}

//Return the folder based on date
function FolderUrlByDate( $date, $url = null )
{
	if ( !is_numeric( $date ) )
		$date = time();
	
	$y_letter = date( 'Y', $date );
	$m_letter = date( 'm', $date);
	
	if ( !$url )
	{
		if ( LOAD_IMAGES_LOCALLY )
			$url = IMAGES_HTML;
		
		elseif ( !empty( Settings::Get()['images_html'] ) )
			$url = Settings::Get()['images_html'];
			
		else
			$url = SITE_URL . 'uploads' . PS;
	}

	return $url . $y_letter . PS . $m_letter . PS;
}

//Return the folder based on date
function FolderRootByDate( $date, $path = null, $createDirs = true )
{
	if ( !is_numeric( $date ) )
		$date = time();
	
	if ( !$path )
	{
		if ( LOAD_IMAGES_LOCALLY )
			$path = UPLOADS_ROOT;
		
		elseif ( !empty( Settings::Get()['images_root'] ) )
			$path = Settings::Get()['images_root'];
			
		else
			$path = ROOT . 'uploads' . DS;
	}
	
	$dir = $path . date( 'Y', $date ) . DS;
	
	if ( $createDirs && !is_dir( $dir ) )
		@mkdir( $dir );
	
	$dir .= date( 'm', $date ) . DS;
	
	if ( $createDirs && !is_dir( $dir ) )
		@mkdir( $dir );

	return $dir;
}

//Check if the file cache exists and is valid if we don't have have debug mode enabled
function ValidCache( $cacheFile, $expire = EXPIRE_CACHE )
{
	if ( !Settings::IsTrue( 'enable_debug_mode' ) && ENABLE_CACHE && file_exists( $cacheFile ) && filemtime( $cacheFile ) > ( time() - ( empty( $expire ) ? 3600 : $expire  ) ) )
		return true;
		
	return false;
}

//Check if the file cache exists and is valid if we have cache enabled
function ValidOtherCache( $cacheFile, $expire = EXPIRE_CACHE )
{
	return ( ENABLE_CACHE && file_exists( $cacheFile ) && filemtime( $cacheFile ) > ( time() - $expire ) ) ? true : false;
}

//Writes the file cache and we are not in DEBUG MODE
function WriteCacheFile( $data, $cacheFile, $isPost = false )
{
	if ( Settings::IsTrue( 'enable_debug_mode' ) || !ENABLE_CACHE )
		return false;
		
	WriteCache( $data, CreateCacheFolders( $cacheFile, $isPost ) );
}

//Writes the file cache only if we have cache enabled
function WriteOtherCacheFile( $data, $cacheFile, $isPost = false )
{
	if ( !ENABLE_CACHE )
		return false;
		
	WriteCache( $data, CreateCacheFolders( $cacheFile, $isPost ) );
}

function CreateCacheFolders( $cacheFile, $isPost )
{
	if ( !is_dir( CACHE_ROOT ) )
		mkdir(CACHE_ROOT, 0755, true);
	
	if ( !$isPost )
		return $cacheFile;
	
	$dir = CACHE_ROOT . 'posts' . DS;

	if (!is_dir($dir))
		mkdir($dir);
	
	if ( strpos( $cacheFile, DS ) !== false )
	{
		$t = explode( DS, $cacheFile );
		$t = end( $t );
	}
	else
		$t = $cacheFile;

	$dir .= GetLetter( $t, 1 ) . DS;

	if ( !is_dir( $dir ) )
		mkdir( $dir );
	
	$dir .= GetLetter( $t, 2 ) . DS;
	
	if ( !is_dir( $dir ) )
		mkdir( $dir );
	
	return $cacheFile;
}

//Delete Cache File
function DelCache( $file )
{
	$cacheFile = CacheFile( $file );

	if ( file_exists( $cacheFile ) )
	{
		@unlink( $cacheFile );
		return true;
	}
		
	return false;
}

// Deletes everything inside a folder
function DeleteFolderFiles( $folder )
{
	$files = glob( $folder . '*');

	if ( !empty( $files ) )
	{
		foreach( $files as $file )
		{
			if( is_file( $file ) )
				unlink( $file );
		}
	}
}

//Creates the cache filename
function CacheFile( $name, $langKey = null )
{
	$cache = CACHE_ROOT . $name . ( $langKey ? '-' . $langKey : '' ) . '-' . sha1 ( $name . CACHE_HASH ) . '.php';

	return strtolower( $cache );
}

//Creates the complete cache filename
function CacheFileName( $name, $folder = null, $langId = null, $blogId = null, $page = null, $items = null, $langKey = null, $siteId = SITE_ID, $theme = null )
{
	$cache = CACHE_ROOT . 'content' . DS;
	
	if ( !empty( $folder ) )
	{
		$cache .= $folder . DS;
		
		//Make sure this folder exists
		if ( !is_dir( $cache ) )
		{
			mkdir($cache, 0755, true);
		}
	}
	
	$theme = ( ( empty( $theme ) && ( $siteId == SITE_ID ) ) ? Settings::Get()['theme'] : $theme );
	
	$tmp = $name . '-' . ( $items ? 'items_' . $items . '-'  : '' ) . ( $page ? 'page_' . $page . '-' : '' ) . ( $langId ? 'langid_' . $langId . '-' : '' ) . ( $blogId ? 'blogid_' . $blogId . '-' : '' ) . ( $langKey ? 'langkey_' . $langKey  . '-' : '' ) . ( !empty( $theme ) ? 'theme_' . $theme . '-' : '' ) . 'siteid_' . $siteId;

	$cache .= $tmp . '-' . substr( sha1( $tmp . CACHE_HASH ), 0, 10 ) . '.php';

	return strtolower( $cache );
}

//Fully Delete Cache Data
function DelFullCache()
{
	$files = glob( CACHE_ROOT . '*' );
	
	//First clean the root files
	foreach ( $files as $file )
	{
		//Don't delete the DB file
		if ( is_dir( $file ) || ( strpos( $file, 'db.' ) !== false ) || ( strpos( $file, '.xml' ) !== false ) )
			continue;

		//Delete files
		if( is_file( $file ) )
			@unlink( $file ); // delete file
	}
	
	//Now clean the folders, but posts
	foreach ( $files as $file )
	{
		if ( !is_dir( $file ) || ( is_dir( $file ) && ( strpos( $file, 'posts' ) !== false ) ) )
			continue;
		
		$folder = $file . DS;
		
		DeleteFolderFiles( $folder );
	}
	
	//Now clean the posts folder 
	$folder = CACHE_ROOT. 'posts' . DS;
	$files = glob( $folder . '*' );
	
	foreach ( $files as $file )
	{
		$folder = $file . DS;
		
		$files_ = glob( $folder . '*' );
		
		foreach ( $files_ as $file_ )
		{
			$folder = $file_ . DS;
			
			DeleteFolderFiles( $folder );
		}
	}
}

//Delete Cache File(s) but posts
function DelCacheFiles( $tag = null, $all = false, $folder = CACHE_ROOT )
{
	//We can't delete a file if we haven't its name
	if ( !$all && empty( $tag ) )
		return false;
	
	//We can't delete everything while we have a tag name
	if ( $all && !empty( $tag ) )
		return false;
		
	$files = glob( $folder . '*'); // get all file names

	foreach( $files as $file )
	{
		//Don't delete the DB file
		if ( ( strpos( $file, 'db.' ) !== false ) || ( strpos( $file, '.xml' ) !== false ) )
			continue;
		
		if ( is_dir( $file ) && ( strpos( $file, 'posts' ) !== false ) )
			continue;
			
		if ( $all )
		{
			//Delete files
			if( is_file( $file ) )
				@unlink( $file ); // delete file

			$folder = $file . DS;
		
			DeleteFolderFiles( $folder );
		}
		
		//Delete only file(s) with a specific name
		else
		{
			if ( ( strpos( $file, $tag ) !== false ) && is_file( $file ) )
				@unlink( $file );
			
			$folder = $file . DS;
			
			$files_ = glob( $folder . '*');
			
			foreach( $files_ as $file_ )
			{
				if ( ( strpos( $file, 'db.' ) !== false ) || ( strpos( $file, '.xml' ) !== false ) )
					continue;

				if ( ( strpos( $file_, $tag ) !== false ) && is_file( $file_ ) )
					@unlink( $file_ );
			}
		}
	}
}

//Creates the cache filename of a file
function PostCacheFile( $file, $type = null, $langKey = null, $amp = false, $theme = null, $static = false, $comments = false )
{
	$cachedir 	= CACHE_ROOT . 'posts' . DS . GetLetter( $file, 1 ) . DS . GetLetter( $file, 2 ) . DS;
	
	$tmp 		= $cachedir . $file . '-' . ( !empty( $type ) ? 'type_' . $type . '-' : '' ) . ( $langKey ? 'langkey_' . $langKey . '-' : '' ) . ( $amp ? 'amp-' : '' ) . ( $theme ? 'theme_' . $theme . '-' : '' ) . ( $static ? 'static-' : '' ) . ( $comments ? 'comments-' : '' );
	
	$fileRoot 	= $tmp . substr( sha1( $tmp . CACHE_HASH ), 0, 10 ) . '.php';

	return strtolower( $fileRoot );
}

#####################################################
#
# Delete post Cache Files function
#
#####################################################
function DeletePostCaches( $sef, $langKey, $theme = null )
{
	$theme = ( !$theme ? Settings::Get()['theme'] : $theme );
	
	$files = array(
		'cacheFile' 		=> PostCacheFile( $sef, null, $langKey, null, $theme ),
		'cacheFileAmp' 		=> PostCacheFile( $sef, null, $langKey, true ),
		'cacheFileComm' 	=> PostCacheFile( $sef, null, $langKey, null, $theme, false, true ),
		'cacheFileStatic' 	=> PostCacheFile( $sef, null, $langKey, null, $theme, true )
	);
			
	foreach( $files as $id => $file )
	{
		if ( file_exists( $file ) )
			@unlink( $file );
	}
}

#####################################################
#
# Get Sitemap Cache File function
#
#####################################################
function SitemapCacheFile ( $file )
{
	if ( empty( $file ) )
		return null;
	
	if ( !is_dir( CACHE_ROOT ) )
		mkdir( CACHE_ROOT, 0755, true );
	
	$cacheRoot = CACHE_SITEMAP_ROOT;
	
	if ( !is_dir( $cacheRoot ) )
		mkdir( $cacheRoot, 0755, true );
	
	return $cacheRoot . sha1 ( $file . CACHE_HASH ) . '-' . $file; //return ( ValidCache( $cacheFile ) ? $cacheFile : null );
}

#####################################################
#
# Copy Remote File by using curl function
#
#####################################################
function CurlCopyFile( $source_file, $target_file )
{
	$ch = curl_init();
	$fp = fopen( $target_file, "w" );
	curl_setopt( $ch, CURLOPT_URL, $source_file );
	curl_setopt( $ch, CURLOPT_HEADER, 0 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)' );
	curl_setopt( $ch, CURLOPT_FILE, $fp );
			
	curl_exec( $ch ); 
			
	if( curl_error( $ch ) )
	{
		return false;
	}

	curl_close( $ch );
	fclose( $fp );

	return true;
}

#####################################################
#
# Copy Remote File function
#
#####################################################
function CopyRemoteFile( $source_file, $target_file, $ext = false )
{
	if ( $ext )
	{
		if ( ini_get( 'allow_url_fopen' ) )
		{
			$image = @file_get_contents( $source_file );
			
			if ( !$image )
			{
				//Go to plan b
				return CurlCopyFile( $source_file, $target_file ); //return false;
			}
			
			$save = file_put_contents( $target_file, $image );
			
			if ( $save === false )
				return false;
			
			return true;
		}
		
		else
		{
			return CurlCopyFile( $source_file, $target_file );
		}
	}
	
	//If copy function doesn't work, go to plan B
	if ( !@copy( $source_file, $target_file ) )
	{
		$file = @fopen ( $source_file, "rb" );
		
		if ( $file ) 
		{
			$newfile = fopen ($target_file, "wb");
			
			if ( $newfile )
			{
				while( !feof( $file ) )
					fwrite( $newfile, fread( $file, 1024 * 8 ), 1024 * 8 );
			}

			if ( $file )
				fclose( $file );

			if ( $newfile )
				fclose( $newfile );
		
			return true;
		}

		return false;
	}
	
	return true;
}

#####################################################
#
# Load Sitemap function
#
#####################################################
function LoadXmlFile( $file )
{
	// Send XML header
	$doc = new DOMDocument();

	// Workaround for a bug https://bugs.php.net/bug.php?id=62577
	libxml_disable_entity_loader( false );

	// Load the XML
	$doc->load( $file );
	
	libxml_disable_entity_loader( true );
			
	// Print the XML
	echo $doc->saveXML();
}


#####################################################
#
# Backup Database to file function
#
#####################################################
function BackupDBToFile()
{
	$db = dbLoad();
	
	$fileName = BACKUPS_ROOT . DATABASE . '_complete_' . date( 'd-m-Y-H-i-s' ) . '_' . GenerateStrongRandomKey( 10 ) . '.sql';
	
	//We want to zip the file?
	$fileName .= '.gz';

	//We will use this string often
	$eol = "\r\n";

	// SQL Dump Header.
	$sgl =
		'-- =========================================================='. $eol.
		'--'. $eol.
		'-- Database dump of tables in `'. DATABASE. '`'. $eol.
		'-- '. postDate( time() ). $eol.
		'--'. $eol.
		'-- =========================================================='. $eol.
		$eol;
	
	try 
	{
		$get = $db->prepare( "SHOW TABLES" );

		$get->execute();
			
		$tables = $get->fetchAll(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) 
	{
		//
	}
	
	if ( empty( $tables ) )
	{
		return;
	}
	
	//Avoid dying...
	if ( function_exists( 'apache_reset_timeout' ) )
		@apache_reset_timeout();

	foreach( $tables as $table )
	{
		//Get only Toki's tables
		if ( !str_contains( $table['Tables_in_' . DATABASE], DB_PREFIX ) )
		{
			continue;
		}
		
		try 
		{
			$tbl = $db->prepare( sprintf( "DESCRIBE %s", $table['Tables_in_' . DATABASE] ) );

			if ( $tbl->execute() )
			{
				$descr = $tbl->fetchAll(PDO::FETCH_ASSOC);
					
				$sgl .=
						$eol.
						'--'. $eol.
						'-- Table structure for table `'. $table['Tables_in_' . DATABASE] . '`' . $eol.
						'--'. $eol.
						$eol.
						dbTableSql( $table['Tables_in_' . DATABASE] ) . ';' . $eol;
			}
		}
		catch(PDOException $e) 
		{
			continue;
		}

		$rows = DbInsertSql( $table['Tables_in_' . DATABASE] );

		if ( empty( $rows ) )
			continue;
		
		$sgl .=
			$eol.
			'--'. $eol.
			'-- Dumping data in `'. $table['Tables_in_' . DATABASE]. '`'. $eol.
			'--'. $eol.
			$eol.
			$rows.
			'-- --------------------------------------------------------'. $eol;
	}
	
	$sgl .= $eol. '-- Done' . $eol;
	
	$fileHandler = gzopen ($fileName, 'w9');
	gzwrite ( $fileHandler, $sgl );
	gzclose($fileHandler);
}

#####################################################
#
# DataSeek function 
#
#####################################################
function DataSeek( $arr, $getId = true )
{
	if ( empty( $arr ) )
		return;
	
	$data = array();
	
	//We only need one row
	$val = $arr['0'];
	
	foreach( $val as $_ => $__ )
	{
		if ( $getId )
			$data[] = $_;
		
		else
			$data[] = $__;
	}
	
	return $data;
}

#####################################################
#
# Get the content (INSERTs) for a table function 
#
#####################################################
function DbInsertSql( $tableName )
{
	$db = dbLoad();

	//We will use this string often
	$eol = "\r\n";
	
	try 
	{
		// Get everything from the table.
		$tbl = $db->prepare( sprintf( "SELECT /*!40001 SQL_NO_CACHE */ * FROM `%s`", $tableName ) );
		$tbl->execute();
		$rows = $tbl->fetchAll(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) 
	{
		//
	}
	
	// Return an empty string if there are no rows.
	if ( empty( $rows ) )
		return '';
	
	$num_rows = count( $rows );
	
	$fields = DataSeek( $rows );

	$current_row = 0;

	// Start it off with the basic INSERT INTO.
	$data = 'INSERT INTO `' . $tableName . '`' . $eol . "\t" . '(`' . implode('`, `', $fields ) . '`)' . $eol . 'VALUES ';

	// Loop through each row.
	foreach( $rows as $row )
	{
		$current_row++;

		// Get the fields in this row...
		$field_list = array();
		
		foreach( $row as $r => $k )
		{
			$field_list[] = ( is_numeric( $k ) ? $k : "'" . addslashes( $k ) . "'" );
		}

		// 'Insert' the data.
		$data .= '(' . implode(', ', $field_list ) . ')';

		// All done!
		if ( $current_row == $num_rows )
			$data .= ';' . $eol;
		
		// Start a new INSERT statement after every 250....
		elseif ( $current_row > 249 && ( $current_row % 250 == 0 ) )
		{
			$data .= ';' . $eol . 'INSERT INTO `' . $tableName . '`' . $eol . "\t" . '(`' . implode('`, `', $fields) . '`)' . $eol . 'VALUES ';
		}
		// Otherwise, go to the next line.
		else
			$data .= ',' . $eol . "\t";
	}
	
	$tbl->closeCursor();

	// Return an empty string if there were no rows.
	return $data;
}

#####################################################
#
# Create Table sql function 
#
#####################################################
function dbTableSql( $tableName )
{
	$db = dbLoad();
	
	//We will use this string often
	$eol = "\r\n";

	// Drop it if it exists.
	$schema_create = 'DROP TABLE IF EXISTS `' . $tableName . '`;' . $eol . $eol;

	// Start the create table...
	$schema_create .= 'CREATE TABLE `' . $tableName . '` (' . $eol;
	
	try 
	{
		$flds = $db->prepare( sprintf( "SHOW FIELDS FROM %s", $tableName ) );
		$flds->execute();
		
		$fields = $flds->fetchAll(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) 
	{
		//
	}

	// Return an empty string if there are no fields.
	if ( empty( $fields ) )
		return '';
	
	foreach( $fields as $field )
	{
		// Make the CREATE for this column.
		$schema_create .= ' `' . $field['Field'] . '` ' . $field['Type'] . ($field['Null'] != 'YES' ? ' NOT NULL' : '');
		
		// Add a default...?
		if ( !empty($field['Default']) || $field['Null'] !== 'YES' )
		{
			// Make a special case of auto-timestamp.
			if ($field['Default'] == 'CURRENT_TIMESTAMP')
				$schema_create .= ' /*!40102 NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP * /';
			
			// Text shouldn't have a default.
			elseif ( $field['Default'] !== null )
			{
				// If this field is numeric the default needs no escaping.
				$type = strtolower($field['Type']);
				
				$isNumericColumn = strpos($type, 'int') !== false || strpos($type, 'bool') !== false || strpos( $type, 'bit' ) !== false || strpos($type, 'float') !== false || strpos($type, 'double') !== false || strpos($type, 'decimal') !== false;

				$schema_create .= ' default ' . ( $isNumericColumn ? $field['Default'] : '\'' . $field['Default'] . '\'' );
			}
		
		}
		
		// And now any extra information. (such as auto_increment.)
		$schema_create .= ( $field['Extra'] != '' ? ' ' . $field['Extra'] : '' ) . ',' . $eol;

	}
	
	// Take off the last comma.
	$schema_create = substr($schema_create, 0, -strlen($eol) - 1);
	
	// Find the keys.
	try 
	{
		$keyz = $db->prepare( sprintf( "SHOW KEYS FROM %s", $tableName ) );
		$keyz->execute();
		$keys = $keyz->fetchAll(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) 
	{
		//
	}
	
	if ( empty( $keys ) )
		return '';
	
	$indexes = array();
	
	foreach( $keys as $key )
	{
		// IS this a primary key, unique index, or regular index?
		$key['Key_name'] = $key['Key_name'] == 'PRIMARY' ? 'PRIMARY KEY' : (empty($key['Non_unique']) ? 'UNIQUE ' : ($key['Comment'] == 'FULLTEXT' || (isset($key['Index_type']) && $key['Index_type'] == 'FULLTEXT') ? 'FULLTEXT ' : 'KEY ')) . '`' . $key['Key_name'] . '`';

		// Is this the first column in the index?
		if (empty($indexes[$key['Key_name']]))
			$indexes[$key['Key_name']] = array();

		// A sub part, like only indexing 15 characters of a varchar.
		if (!empty($key['Sub_part']))
			$indexes[$key['Key_name']][$key['Seq_in_index']] = '`' . $key['Column_name'] . '`(' . $key['Sub_part'] . ')';
		
		else
			$indexes[$key['Key_name']][$key['Seq_in_index']] = '`' . $key['Column_name'] . '`';
	}
	
	// Build the CREATEs for the keys.
	foreach ($indexes as $keyname => $columns)
	{
		// Ensure the columns are in proper order.
		ksort($columns);

		$schema_create .= ',' . $eol . ' ' . $keyname . ' (' . implode( ', ', $columns ) . ')';
	}

	// Now just get the comment and type... (MyISAM, etc.)
	try 
	{
		$sts = $db->prepare( sprintf( "SHOW TABLE STATUS LIKE '%s'", $tableName ) );
		$sts->execute();
		$stat = $sts->fetch(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) 
	{
		//
	}
	
	if ( !empty( $stat ) )
	{
		// Probably MyISAM.... and it might have a comment and collation.
		$schema_create .= $eol . ') ENGINE=' . (isset($stat['Type']) ? $stat['Type'] : $stat['Engine']) . ($stat['Comment'] != '' ? ' COMMENT="' . $stat['Comment'] . '"' : '') . ($stat['Collation'] != '' ? ' DEFAULT CHARSET="' . $stat['Collation'] . ';"' : '');
	}

	return $schema_create;
}

#####################################################
#
# Backup Database (From Maintenance Mode) function
#
#####################################################
function BackupDB()
{
	global $Admin;
	
	if ( !isset( $_POST['db'] ) || empty( $_POST['db'] ) || !is_array( $_POST['db'] ) )
		return;
	
	// Try to clean any data already outputted.
	if ( ob_get_length() != 0 )
	{
		ob_end_clean();
	}
	
	$db = dbLoad();
	
	$fileName = DATABASE . '_' . ( !isset( $_POST['db']['data'] ) ? 'structure' : ( !isset( $_POST['db']['structure'] ) ? 'data' : 'complete' ) ) . '_' . date( 'd-m-Y-H-i-s' );


	$fileName = $fileName . '-' . strtolower( substr( sha1( $fileName . CACHE_HASH ), 0, 10 ) ) . '.sql';
	
	//Do we want to zip the file?
	if ( !empty( $_POST['db']['gzip'] ) )
	{
		header('Content-Type: application/x-gzip');
		header('Accept-Ranges: bytes');
		
		//Add the 'gz' file extension...
		$fileName .= '.gz';
	}
	
	else
	{
		header( 'Content-Type: application/octet-stream' );
	}
		
	//We will use this string often
	$eol = "\r\n";

	// SQL Dump Header.
	$sgl =
		'-- =========================================================='. $eol .
		'--'. $eol.
		'-- Database dump of tables in `'. DATABASE. '`'. $eol.
		'-- '. postDate( time() ). $eol.
		'--'. $eol.
		'-- =========================================================='. $eol .
	$eol;
	
	try 
	{
		$get = $db->prepare( "SHOW TABLES" );

		$get->execute();
		
		$tables = $get->fetchAll(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) 
	{
		//
	}
	
	if ( empty( $tables ) )
	{
		$Admin->SetAdminMessage( __( 'an-error-happened' ) );
		return;
	}

	//Avoid dying...
	if ( function_exists( 'apache_reset_timeout' ) )
		@apache_reset_timeout();
	
	foreach( $tables as $table )
	{
		//Get only Toki's tables
		if ( !str_contains( $table['Tables_in_' . DATABASE], DB_PREFIX ) )
		{
			continue;
		}
		
		if ( isset( $_POST['db']['structure'] ) )
		{
			try 
			{
				$tbl = $db->prepare( sprintf( "DESCRIBE %s", $table['Tables_in_' . DATABASE ] ) );

				if ( $tbl->execute() )
				{
					//$descr = $tbl->fetchAll(PDO::FETCH_ASSOC);
					
					$sgl .=
						$eol.
						'--'. $eol.
						'-- Table structure for table `'. $table['Tables_in_' . DATABASE ] . '`' . $eol.
						'--'. $eol.
						$eol.
						dbTableSql( $table['Tables_in_' . DATABASE ] ) . ';' . $eol;
				}
			}
			catch(PDOException $e) 
			{
				//
			}
		}
		
		if ( !isset( $_POST['db']['data'] ) )
		{
			continue;
		}
		
		try 
		{
			$rows = DbInsertSql( $table['Tables_in_' . DATABASE] );
		}
		
		catch(PDOException $e) 
		{
			//
		}
		
		if ( empty( $rows ) )
			continue;
		
		$sgl .=
			$eol.
			'--'. $eol.
			'-- Dumping data in `'. $table['Tables_in_' . DATABASE]. '`'. $eol.
			'--'. $eol.
			$eol.
			$rows.
			'-- --------------------------------------------------------'. $eol;
	}
	
	$sgl .= $eol. '-- Done' . $eol;

	if ( isset( $_POST['db']['gzip'] ) )
	{
		$fileHandler = gzopen ($fileName, 'w9');
		gzwrite ( $fileHandler, $sgl );
		gzclose($fileHandler);
	}
	
	else
	{
		$fileHandler = fopen( $fileName, 'w+' );
		$number_of_lines = fwrite( $fileHandler, $sgl );
		fclose( $fileHandler ); 
	}
	
	// Send the proper headers to let them download this file.
	header('Content-Encoding: none');
    header('Content-Description: File Transfer');
	header('Content-Disposition: filename=' . basename($fileName));
	header('Cache-Control: private');
    header('Content-Length: ' . filesize($fileName));
	
    ob_clean();
    flush();
    readfile($fileName);
    exec('rm ' . $fileName); 
	header('Connection: close');
	exit;
}