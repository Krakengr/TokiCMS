<?php defined('TOKICMS') or die('Hacking attempt...');

class Image
{
	private $db;
	private $folder;
	private $fileName;
	private $targetFile;
	private $imgUrl;
	private $local;
	private $share;
	private	$siteId;
	private $allowed;
	private $extension;
	private $imageId;
	public 	$userId;
	public 	$langId;
	public 	$postId;
	public 	$imgFile;
	public 	$targetName;
	public 	$time;
	public 	$imgType 	= 'image';
	public 	$type 		= 'post';
	public 	$isExternal = false;
	public 	$folderId	= 0;

	public function __construct( $siteId = SITE_ID )
	{
		$this->db 		= db();
		$this->siteId 	= $siteId;
		$this->allowed 	= AllowedExt( $this->siteId );
		$this->local 	= ImageUpladDir( SITE_ID );
		$this->share 	= ( ( $this->siteId == SITE_ID ) ? null : ImageUpladDir( $this->siteId ) );
	}
	
	#####################################################
	#
	# Check image function function
	#
	#####################################################
	public function GetImage( $setCover = false )
	{
		$this->time = ( $this->time ? $this->time : time() );

		if ( empty( $this->imgFile ) )
		{
			return;
		}
		
		$name = pathinfo( strtolower( $this->imgFile ) );

		if ( empty( $name ) || empty( $name['extension'] ) )
		{
			return;
		}

		$this->extension = $name['extension'];

		if ( empty( $this->allowed ) || !in_array( $this->extension, $this->allowed ) )
		{
			return;
		}

		$this->folder	= FolderRootByDate( $this->time, ( !empty( $this->local ) ? $this->local['root'] : null ) );
	
		if ( !$this->fileName )
		{
			$this->fileName = URLify( $name['filename'] ) . '.' . $this->extension;
		}
		
		//Set the image's url
		$this->imgUrl 	= ( !empty( $this->local ) ? FolderUrlByDate( $this->time, $this->local['html'] ) : FolderUrlByDate( $this->time ) ) . $this->fileName;

		$this->targetFile = $this->folder . $this->fileName;

		$this->CopyImage();

		if ( !file_exists( $this->targetFile ) )
		{
			return;
		}
		
		//Convert webP image format to png image file
		if ( $this->extension == 'webp' )
		{
			$this->fileName 	= $name['filename'] . '.png';
			$this->extension 	= 'png';

			if ( file_exists( $this->targetFile ) && !file_exists( $this->folder . $this->fileName ) )
			{
				if ( function_exists( "exif_read_data" ) && exif_imagetype( $this->targetFile ) === IMAGETYPE_WEBP )
				{
					// Load the WebP file
					$im = imagecreatefromwebp( $this->targetFile );

					// Convert it to a png file with 90% quality
					imagepng( $im, $this->folder . $this->fileName );

					imagedestroy( $im );
				}
			}
			
			@unlink( $this->folder . $name['filename'] . '.webp' );

			$this->targetFile = $this->folder . $this->fileName;
		}
		
		//Now add this image into DB
		$this->AddImgToDb( $this->targetFile, $this->fileName );
		
		//Now create the smaller images
		$this->AddChildImages();
		
		//Set this image as cover if needed
		if ( $setCover )
		{
			$this->SetAsCover();
		}
		
		//Ping the child site to get this image
		$this->PingChildSite();
		
		return $this->imageId;
	}
	
	#####################################################
	#
	# Add Image Into DB function
	#
	#####################################################
	private function AddImgToDb( $targetFile, $fileName, $status = 'full', $isChild = false )
	{
		if ( !file_exists( $targetFile ) )
		{
			return;
		}
		
		//Check if we have this image into the DB
		$imgData = $this->db->from( null, "
		SELECT id_image
		FROM `" . DB_PREFIX . "images`
		WHERE (filename = :name) AND (id_site = " . $this->siteId . ")",
		array( $fileName => ':name' )
		)->single();
			
		if ( $imgData )
		{
			$this->imageId = $imgData['id_image'];
			return;
		}

		list( $width, $height, $type ) = @getimagesize( $targetFile );
	
		$width = ( ( !empty( $width ) && is_numeric( $width ) ) ? $width : 0 );
		$height = ( ( !empty( $height ) && is_numeric( $height ) ) ? $height : 0 );
	
		$mime = GetMimeType( $targetFile );
		
		$dbarr = array(
			"id_site" 		=> $this->siteId,
			"id_member" 	=> ( !$isChild ? $this->userId : 0 ),
			"width" 		=> $width,
			"height" 		=> $height,
			"img_type" 		=> $this->type,
			"file_ext" 		=> $this->extension,
			"mime_type" 	=> $mime,
			"img_status" 	=> $status,
			"filename" 		=> $fileName,
			"id_folder" 	=> ( !$isChild ? $this->folderId : 0 ),
			"size" 			=> filesize( $targetFile ),
			"added_time" 	=> $this->time,
			"id_parent" 	=> ( $isChild ? $this->imageId : 0 ),
			"id_lang" 		=> ( ( $this->langId && !$isChild ) ? $this->langId : 0 ),
			"id_post" 		=> ( ( $this->postId && !$isChild ) ? $this->postId : 0 )
		);

		$q = $this->db->insert( 'images' )->set( $dbarr, null, true );
		
		if ( !$q || $isChild )
		{
			return;
		}
		
		$this->imageId = $q;
	}
	
	#####################################################
	#
	# Set an image as cover function
	#
	#####################################################
	private function SetAsCover()
	{
		if ( empty( $this->postId ) )
		{
			return;
		}
		
		$ex = $this->db->from( 
		null, 
		"SELECT id_attach
		FROM `" . DB_PREFIX . "image_attachments`
		WHERE (post_id = " . $this->postId . ")"
		)->single();

		if ( !$ex )
		{
			$dbarr = array(
				"post_id" 	=> $this->postId,
				"image_id" 	=> $this->imageId,
				"user_id" 	=> $this->userId
			);

			$this->db->insert( 'image_attachments' )->set( $dbarr );
		}
			
		else
		{
			$dbarr = array(
				"image_id" 	=> $this->imageId,
				"user_id" 	=> $this->userId
			);
				
			$this->db->update( "image_attachments" )->where( 'id_attach', $ex['id_attach'] )->set( $dbarr );
		}
	}
	
	#####################################################
	#
	# Ping a child site to clone this image function
	#
	#####################################################
	private function PingChildSite()
	{
		if ( empty( $this->imageId ) )
		{
			return;
		}

		if ( !empty( $this->share ) && isset( $this->share['share'] ) && $this->share['share'] )
		{
			PingChildSite( 'sync', 'image', $this->imageId, $this->siteId );
		}
	}
	
	#####################################################
	#
	# Add the smaller images function
	#
	#####################################################
	private function AddChildImages()
	{
		if ( empty( $this->imageId ) )
		{
			return;
		}
		
		$sizes = array( '75', '50', '25' );

		foreach( $sizes as $_size )
		{
			//Calculate the img size
			$size = $this->CalculateImgSize( $_size );

			if ( empty( $size ) )
				continue;

			$newImg = $this->fileName . '-' . $size['f'] . '.' . $this->extension;
		
			//Check if we have this image already
			$imgData = $this->db->from( null, "
			SELECT id_image
			FROM `" . DB_PREFIX . "images`
			WHERE (filename = :name) AND (id_parent = " . $this->imageId . ")",
			array( $newImg => ':name' )
			)->single();
			
			//We don't have that image, so try to create it
			if ( !$imgData )
			{
				CreateImage( $this->targetFile, $this->folder . $newImg, array( 'w' => $size['w'], 'h' => $size['h'] ) );
			}

			if ( file_exists( $this->folder . $newImg ) )
			{
				$this->AddImgToDb( $this->folder . $newImg, $newImg, 'cropped', true );
			}
		}
	}

	#####################################################
	#
	# Copy Image function
	#
	#####################################################
	private function CopyImage()
	{
		if ( file_exists( $this->targetFile ) )
			return;
		
		if ( $this->isExternal )
		{
			try
			{
				CopyRemoteFile( $this->imgFile, $this->targetFile, true );
			}
			
			catch( Exception $err )
			{
				//
			}
			
			return;
		}
		
		else
		{
			if ( $this->imgType != 'image' )
			{
				try
				{
					CopyRemoteFile( $this->imgFile, $this->targetFile, false );
				}
				
				catch( Exception $err )
				{
					//
				}
				
				return;
			}
		
			else
			{
				require_once ( TOOLS_ROOT . 'SimpleImage' . DS . 'src' . DS . 'claviska' . DS . 'SimpleImage.php');
				
				try
				{
					// Create a new SimpleImage object
					$image = new \claviska\SimpleImage();
					
					$image
						->fromFile( $this->imgFile )     // load image file
						->autoOrient()                  // adjust orientation based on exif data
						->toFile( $this->targetFile ); // convert and save the file
				}
				
				catch( Exception $err )
				{
					//echo $err->getMessage();
					return;
				}
				
				@unlink( $this->imgFile );
			}
		}
	}
	
	#####################################################
	#
	# Calculate the image size function
	#
	#####################################################
	private function CalculateImgSize( $pcnt = 75 )
	{
		if ( !file_exists( $this->targetFile ) || !is_file( $this->targetFile ) )
			return null;

		list( $originalWidth, $originalHeight ) = @getimagesize( $this->targetFile );
		
		if ( !is_numeric( $originalWidth ) )
			$originalWidth = @imagesx( $this->targetFile );
		
		if ( !is_numeric( $originalHeight ) )
			$originalHeight = @imagesy( $this->targetFile );
		
		$targetWidth = round( (int) $originalWidth * ( $pcnt / 100 ) );
		$targetHeight = round( (int) $originalHeight * ( $pcnt / 100 ) );

		return array( 'w' => $targetWidth, 'h' => $targetHeight, 'f' => $targetWidth . 'x' . $targetHeight );
	}
	
	public function Blank()
	{
		return  'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=';
	}
}