<?php

class _Import
{
	public  $attachments;		// Stored attachments
	public  $comId;				// Comment ID
	public  $postId;			// Current post ID
	public  $emptyTitle;		// Empty Title ID
	public  $siteUrl;			// Site URL
	public  $importFile;		// The file we want to import data from
	public  $importID;			// Import ID (from DB)
	public  $prvSiteUrl;		// Previous Site URL
	public 	$siteId;			// Site ID for the posts
	public 	$langId;			// Lang ID for the posts
	public 	$blogId;			// Blog ID for the posts
	public  $catID;				// Category ID
	public  $userID;			// User ID
	public  $copyImages;		// Copy Images
	public  $postType;			// The type of the post
	public  $postStatus;		// The status of the post
	public  $currentCatId;		// Current Category
	public  $postTypeId;		// Post Type ID
	public  $xml;
	public  $message;
	public  $currentItem;
	public  $countItem;
	public  $countItems;
	public  $maxItemsPerTime;
	public  $itemsDb;
	public  $totalItems;
	public  $brake;
	public  $dbFilename;
	public  $genericXml;
	public  $data;
	public  $replace;
	public  $crawl;
	public  $custom;
	public  $failed;
	
	public function __construct()
	{
		$this->attachments = array();
		$this->itemsDb = array();
		$this->postId = 0;
		$this->currentItem = 0;
		$this->countItems = 0;
		$this->countItem = 0;
		$this->maxItemsPerTime = 5;
		$this->emptyTitle = 0;
		$this->blogId = 0;
		$this->comId = 0;
		$this->failed = 0;
		$this->brake = false;
		$this->genericXml = false;
		$this->message = '';
	}
	
	public function AddItemInImports( $id )
	{
		if ( empty( $id ) )
			return;
		
		$id = 'post-' . $id;
		
		if ( empty( $id ) || isset( $this->itemsDb[$id] ) )
			return;
		
		array_push( $this->itemsDb, $id );
	}
	
	public function CheckItemInImports( $id )
	{
		$id = 'post-' . $id;
		
		if ( empty( $id ) || empty( $this->itemsDb ) || !in_array( $id, $this->itemsDb ) )
			return false;
		
		return true;
	}
	
	public function SetFinished()
	{
		if ( ( $this->totalItems == 0 ) || ( $this->countItem < $this->totalItems ) )
			return;
		
		global $Admin;
		
		$query = array(
			'UPDATE'    => DB_PREFIX . "imports",
			'SET'		=> "completed = :completed, completed_time = :time",
			'WHERE'		=> "id = :id",

			'PARAMS' 	=> array( 'NO_PREFIX' => true ),

			'BINDS' 	=> array(
					array( 'PARAM' => ':completed', 'VAR' => 1, 'FLAG' => 'INT' ),
					array( 'PARAM' => ':time', 'VAR' => time(), 'FLAG' => 'INT' ),
					array( 'PARAM' => ':id', 'VAR' => $this->importID, 'FLAG' => 'INT' )
			)
		);

		Query( $query, false, false, true );
		
		$Admin->EmptyCaches( $this->siteId );
	}
	
	public function AddItems( $id )
	{
		if ( empty( $id ) )
			return;
		
		$id = 'post-' . $id;
		
		if ( empty( $id ) || isset( $this->itemsDb[$id] ) )
			return;
		
		array_push( $this->itemsDb, $id );
	}
	
	public function ImgFilename( $img, $title = null )
	{
		$name = pathinfo( $img );
	
		if ( empty( $name ) || !isset( $name['extension'] ) || empty( $name['extension'] ) )
		{
			return false;
		}
	
		return URLify( ( $title ? $title : $name['filename'] ) ) . '.' . $name['extension'];
	}
	
	public function _exit()
	{
		sort( $this->itemsDb );
		
		WriteFileDB ( $this->itemsDb, $this->dbFilename );
		
		if ( is_array( $this->itemsDb ) && !empty( $this->itemsDb ) )
		{
			$this->countItems = count( $this->itemsDb );
		}
	}
	
	public function LoadImportDb()
	{
		$query = array(
			'SELECT'	=>  "*",
			'FROM'		=> DB_PREFIX . "imports",
			'WHERE'		=> "id = :id",
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
			'BINDS' 	=> array(
				array( 'PARAM' => ':id', 'VAR' => $this->importID, 'FLAG' => 'INT' )
			)
		);

		$q = Query( $query );
		
		if ( !$q )
			return;
		
		if ( $this->genericXml && !empty( $q['extra_data'] ) )
		{
			$data = Json( $q['extra_data'] );
			
			$this->crawl 	= ( isset( $data['crawl'] ) ? $data['crawl'] : null );
			$this->data 	= ( isset( $data['data'] ) ? $data['data'] : null );
			$this->replace  = ( isset( $data['replace'] ) ? $data['replace'] : null );
			$this->custom  	= ( isset( $data['fields'] ) ? $data['fields'] : null );
		}
		
		//Set the num items per time
		if ( !empty( $this->data['numItems']['value'] ) && is_numeric( $this->data['numItems']['value'] ) &&
			( $this->data['numItems']['value'] > 0 ) )
		{
			$this->maxItemsPerTime = $this->data['numItems']['value'];
		}

		//Set a few settings here
		$this->prvSiteUrl 	= $q['old_url'];
		$this->postType 	= $q['post_status'];
		$this->postTypeId 	= $q['id_custom_type'];
		$this->catID 		= $q['id_category'];
		$this->copyImages 	= $q['copy_images'];
		$this->userID 		= $q['id_member'];
		$this->siteId 		= $q['id_site'];
		$this->blogId 		= $q['id_blog'];
		$this->langId 		= $q['id_lang'];
		$this->postStatus	= ( !empty( $this->data['postStatus']['value'] ) ? $this->data['postStatus']['value'] : 'published' );
		
		$query = array(
			'SELECT'	=>  "url",
			'FROM'		=> DB_PREFIX . "sites",
			'WHERE'		=> "id = :id",
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
			'BINDS' 	=> array(
				array( 'PARAM' => ':id', 'VAR' => $this->siteId, 'FLAG' => 'INT' )
			)
		);
		
		$s = Query( $query );
		
		$this->siteUrl 		= $s['url'];
		
		$this->dbFilename = DB_DATA_ROOT . $q['file_id'] . '.php';

		$this->itemsDb = ( file_exists( $this->dbFilename ) ? OpenFileDB( $this->dbFilename ) : array() );
	}
	
	public function LoadXml()
	{
		//Load the XML data
		$this->xml = @simplexml_load_string( StripInvalidXml( @file_get_contents( $this->importFile ) ) );
		
		//Is this a valid xml data?
		if ( $this->xml === false )
		{
			$this->message = sprintf( __( 'file-is-not-a-valid-xml-file' ), $this->xml );
		}
	}
	
	public function descr( $content, $flag = '<!--more-->' )
	{
		if ( !empty( $flag ) && strpos( $content, $flag ) )
		{
			$descr = explode ( $flag, $content );
			$descr = strip_tags( $descr['0'] );
			$descr = mb_strcut( $descr, 0, 160, "UTF-8" );
		}
		
		else
			$descr = generateDescr( $content, 160 ) ;

		return $descr;
	}

	// Returns the first image from the content
	public function GetFirstImage( $content )
	{
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/ii', $content, $matches);
			
		if ( !empty( $matches ) && !empty( $matches['1'] ) && isset( $matches['1']['0'] ) )
			return $matches['1']['0'];

		return false;
	}
	
	private function ReturnImgUrl( $img )
	{
		if ( strpos( $img, '?' ) !== false ) 
		{
			$img = explode('?', $img);
			$img = trim( $img['0'] );
		}
		
		return $img;
	}
	
	public function ReplaceContentImages( $content, $date = null, $returnContent = false )
	{
		if ( !$this->copyImages )
			return;
		
		$changed = 0;
		
		preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
			
		if ( empty( $matches ) || !isset( $matches['1'] ) || empty( $matches['1'] ) )
			return;

		$date = ( !$date ? time() : $date );

		foreach ( $matches['1'] as $key => $value )
		{
			$img = $this->ReturnImgUrl( $value );
			
			$copy = CreateTheImgs( $img, $this->siteId, $this->userID, 'post', 0, $date, $this->langId, $this->postId, true );
			
			if ( !$copy )
				continue;
			
			//Aling the image to the center
			$newImg = '<img width="' . $copy['width'] . '" src="' . $copy['url'] . '" alt="" class="size-full aligncenter" align="center" />';

			$content = str_replace( $matches['0'][$key], $newImg, $content );

			$changed++;
		}

		//If we only need the content, then return it
		if ( $returnContent )
			return $content;

		//If we've changed any image, we should update the post content
		if ( $changed > 0 )
		{
			$query = array(
						'UPDATE' 	=> DB_PREFIX . POSTS,
						'SET'		=> "post = :post",
						'WHERE'		=> "id_post = :id",
						
						'PARAMS' 	=> array( 'NO_PREFIX' => true ),
						
						'BINDS' 	=> array(
									array( 'PARAM' => ':id', 'VAR' => $this->postId, 'FLAG' => 'INT' ),
									array( 'PARAM' => ':post', 'VAR' => $content, 'FLAG' => 'STR' )
						)
			);

			Query( $query, false, false, true );
		}
	}
	
	public function SkipWords( $title )
	{
		$found = false;
		
		if ( !empty( $this->data['avoidWords']['value'] ) )
		{
			$val = trim( $this->data['avoidWords']['value'] );
				
			$words = array();
				
			if ( strpos($val, ',') !== false )
			{
				$_words = explode( ',', $val );
					
				foreach( $_words as $_word )
				{
					array_push( $words, trim( strtolower( $_word ) ) );
				}
			}
				
			else
			{
				array_push( $words, trim( strtolower( $_word ) ) );
			}
				
			if ( !empty( $words ) )
			{
				$temp = strtolower( $title );
					
				foreach( $words as $word )
				{
					if ( strpos($temp, $word) !== false )
					{
						$found = true;
						break;
					}
				}
			}
		}
		
		return $found;
	}

	public function AddPost( $args )
	{
		global $Query;
		
		//Create the post's slug
		$slug = CreateSlug( $args['title'], true );
		
		//Change the content's urls
		if ( !empty( $this->prvSiteUrl ) )
		{
			$args['content'] = $this->replaceURL( $args['content'] );
		}
		
		//Add the source url
		if ( !empty( $this->data['addSourceLink']['value'] ) || !empty( $this->data['sourceText']['value'] ) )
		{
			$content  = $args['content'];
			
			$content .= PHP_EOL;
			
			if ( empty( $this->data['addSourceLink']['value'] ) )
			{
				$content .= $this->data['sourceText']['value'];
			}
			
			else
			{
				$source = ( !empty( $this->data['sourceText']['value'] ) ? $this->data['sourceText']['value'] : __( 'source' ) );
				
				$content .= '<a target="_blank" href="' . $args['postSource'] . '">' . $source . '</a>';
			}			

			$args['content'] = $content;
			
			unset( $content );
		}
		
		//Add the langCode into the array
		$args['langCode'] = GetLangCode( $this->langId );

		//Check if we have this post
		$query = array(
				'SELECT'	=>  "id_post, blocks, id_parent, cover_img, disable_comments",
				'FROM'		=> DB_PREFIX . POSTS,
				'WHERE'		=> "sef = :sef AND id_site = :site AND id_blog = :blog AND id_lang = :lang",
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
						array( 'PARAM' => ':sef', 'VAR' => $slug, 'FLAG' => 'INT' ), 
						array( 'PARAM' => ':site', 'VAR' => $this->siteId, 'FLAG' => 'INT' ), 
						array( 'PARAM' => ':blog', 'VAR' => $this->blogId, 'FLAG' => 'INT' ), 
						array( 'PARAM' => ':lang', 'VAR' => $this->langId, 'FLAG' => 'INT' )
				)
		);

		$pExists = Query( $query );

		if ( $pExists )
		{
			$this->message .= sprintf( __( 'post-exists' ), $args['title'] );
			$this->postId = $pExists['id_post'];
			
			if ( !empty( $this->data['updatePost']['value'] ) )
			{
				//Add a few more values into the array
				$args['postId'] 			= $this->postId;
				$args['userId'] 			= $this->userID;
				$args['slug'] 				= $slug;
				$args['blocksData'] 		= $pExists['blocks'];
				$args['parent'] 			= $pExists['id_parent'];
				$args['cover'] 				= $pExists['cover_img'];
				$args['disableComments'] 	= $pExists['disable_comments'];
				$args['postStatus'] 		= 'published';
				$args['editedTime'] 		= time();
				
				$Query->EditPost( $args );
				
				$this->ReplaceContentImages( $args['content'], $args['postDate'] );
				
				$this->message .= ' (<strong>' . __( 'updated' ) . '</strong>)';
			}
			
			
			$this->message .= '<br />';
			
			return true;
		}
		
		$cat = $this->currentCatId;
		$subCat = 0;

		//Do we have a Sub Category here? We need the parent aswell
		$query = array(
				'SELECT'	=>  "id_trans_parent, id_custom_type, id_parent",
				'FROM'		=> DB_PREFIX . "categories",
				'WHERE'		=> "id = :id",
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
					array( 'PARAM' => ':id', 'VAR' => $this->currentCatId, 'FLAG' => 'INT' )
				)
		);

		$catt = Query( $query );
		
		if ( $catt )
		{
			//Check if this category have a custom id and apply it
			if ( !$this->postTypeId && $catt['id_custom_type'] )
				$this->postTypeId = $catt['id_custom_type'];
			
			//If this category is a child, get its parent
			if ( $catt['id_parent'] )
			{
					$query = array(
						'SELECT'	=>  "id",
						'FROM'		=> DB_PREFIX . "categories",
						'WHERE'		=> "id = :id",
						'PARAMS' 	=> array( 'NO_PREFIX' => true ),
						'BINDS' 	=> array(
							array( 'PARAM' => ':id', 'VAR' => $catt['id_parent'], 'FLAG' => 'INT' )
						)
				);

				$parr = Query( $query );
				
				if ( $parr )
				{
					$cat = $parr['id'];
					$subCat = $this->currentCatId;
				}
			}
		}
		
		$id = $Query->AddPost( $args );

		if ( !$id )
		{
			$this->postId = 0;
			$this->message .= sprintf( __( 'post-added' ), $args['title'] ) . '<br />';
			return false;
		}
		
		$this->postId = $id;
		
		//Replace the content images
		$this->ReplaceContentImages( $args['content'], $args['postDate'] );

		$this->message .= sprintf( __( 'post-added' ), $args['title'] ) . '<br />';
		
		return true;
	}

	public function category( $title )
	{
		//Check if we want every post into a particular category
		if ( !empty( $this->catID ) )
		{
			return $this->currentCatId = $this->catID;
		}
		
		//If we have a blank name, get the default category
		if ( empty( $title ) && !empty( $this->blogId ) )
		{
			$catID = GetBlogDefaultCategory( $this->blogId, $this->langId );
			
			return $this->currentCatId = $catID;
		}
		
		$sef = CreateSlug( $title );

		$query = array(
				'SELECT'	=>  "id",
				'FROM'		=> DB_PREFIX . "categories",
				'WHERE'		=> "sef = :sef AND id_site = :site AND id_lang = :lang AND id_blog = :blog",
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
						array( 'PARAM' => ':sef', 'VAR' => $sef, 'FLAG' => 'INT' ), 
						array( 'PARAM' => ':site', 'VAR' => $this->siteId, 'FLAG' => 'INT' ), 
						array( 'PARAM' => ':blog', 'VAR' => $this->blogId, 'FLAG' => 'INT' ),
						array( 'PARAM' => ':lang', 'VAR' => $this->langId, 'FLAG' => 'INT' )
				)
		);

		$catt = Query( $query );

		if ( $catt )
		{
			return $this->currentCatId = $catt['id'];
		}
		
		//Get the default custom type for this site
		$query = array(
				'SELECT'	=>  "id",
				'FROM'		=> DB_PREFIX . "post_types",
				'WHERE'		=> "id_site = :site AND is_default = '1'",
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
					array( 'PARAM' => ':site', 'VAR' => $this->siteId, 'FLAG' => 'INT' )
				)
		);

		$ctp = Query( $query );

		//We don't have this category, so add it now
		$query = array(
				'INSERT'	=> "name, sef, id_lang, id_blog, id_site, id_custom_type",
				'VALUES' => ":name, :sef, :lang, :blog, :site, :custom",
				'INTO'		=> DB_PREFIX . "categories",
				'PARAMS' => array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
							array( 'PARAM' => ':name', 'VAR' => $title, 'FLAG' => 'STR' ),
							array( 'PARAM' => ':sef', 'VAR' => $sef, 'FLAG' => 'STR' ),
							array( 'PARAM' => ':site', 'VAR' => $this->siteId, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':blog', 'VAR' => $this->blogId, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':lang', 'VAR' => $this->langId, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':custom', 'VAR' => ( $ctp ? $ctp['id'] : 0 ), 'FLAG' => 'INT' )
					)
		);

		$catt = Query( $query, false, false, false, false, true );

		if ( $catt )
			return $this->currentCatId = $catt;
		
		//This part shouldn't be executed, but better safe than sorry
		$query = array(
				'SELECT'	=>  'id',
				'FROM'		=> DB_PREFIX . "categories",
				'WHERE'		=> "id_site = :id_site AND id_lang = :id_lang AND id_blog = :id_blog AND is_default = '1'",
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
							array( 'PARAM' => ':id_site', 'VAR' => $this->siteId, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':id_lang', 'VAR' => $this->langId, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':id_blog', 'VAR' => 0, 'FLAG' => 'INT' )
				)
		);

		$cat = Query( $query );

		return $this->currentCatId = ( !empty( $cat ) ? $cat['id'] : 0 );
	}
	
	public function replaceURL ( $content ) 
	{
		if ( !empty ( $this->prvSiteUrl ) )
		{
			$exURL = str_replace (array("/", "."), array ("\/", "\."), $this->prvSiteUrl );

			$content = preg_replace('/' . $exURL . '([0-9]{4}\/)?([0-9]{2}\/)?([0-9]{2}\/)?(([^_]+)\/)?([^_]+)\//', $this->siteUrl . "$6/",  $content);
			
		}
		
		return $content;
	}
	
	public function __destruct(){}
}