<?php

class XML extends _Import
{	
	function __construct()
	{
		parent::__construct();
	}
	
	public function process() 
	{
		$this->LoadXml();
		
		if ( $this->xml === false )
			return;

		$this->LoadImportDb();

		$this->convertXML();
	}
	
	public function countItems() 
	{
		$this->totalItems = 0;
		
		$this->LoadXml();
		
		if ( $this->xml === false )
			return;

		//Count the items
		foreach( $this->xml->url as $item ) 
		{
			$loc = (string) $item->loc;

			//Skip any empty URL
			if( empty( $loc ) )
			{
				continue;
			}

			$this->totalItems++;
		}
	}
	
	private function convertXML() 
	{
		require( CLASSES_ROOT . 'Bot.php' );
		
		$Bot = new Bot;
		
		$data = $this->crawl;
		
		if ( empty( $data ) )
			return;
		
		$regTitle  = $data['title']['value'];
		$regCat    = $data['category']['value'];
		$regDescr  = $data['descr']['value'];
		$regImg    = $data['image']['value'];
		$regCont   = $data['content']['value'];
		$regConten = $data['container']['value'];
		$regTags   = $data['tags']['value'];

		//Count items
		$this->countItems();

		//Let's begin the conversion...
		foreach( $this->xml->url as $item )
		{
			$loc = (string) $item->loc;

			$image = @$item->xpath('image:image');
			
			if ( !empty( $image ) && isset( $image['0'] ) )
			{
				$image = $image['0']->xpath('image:loc');
				$image = ( isset( $image['0'] ) ? (string) $image['0'] : null );
			}

			//Skip any item that is not a post or a page
			if( empty( $loc ) )
			{
				$this->failed++;
				continue;
			}
			
			$this->countItem++;
			
			$p_id = md5( $loc );
			
			//Check if we have already added this item
			if ( $this->CheckItemInImports( $p_id ) )
			{
				continue;
			}
			
			$Bot->url 		= $loc;
			$Bot->options 	= $this->data;
			$Bot->process();
			
			if ( $Bot->status !== 200 )
			{
				$this->failed++;
				$this->message .= $loc . ' get error (code: ' . $Bot->status . ')<br />';
				continue;
			}
			
			$date = strtotime( $item->lastmod );

			//Add this item into DB array
			$this->AddItems( $p_id );
			
			$this->currentItem++;

			//Remove old posts
			if ( !empty( $this->data['removeOldPosts']['value'] ) && is_numeric( $this->data['removeOldPosts']['value'] ) && ( $this->data['removeOldPosts']['value'] > 0 ) )
			{
				if ( $date < ( time() - ( $this->data['removeOldPosts']['value'] * 86400 ) ) )
				{
					$this->message .= $loc . ' skipped (post is older than <strong>' . ( $this->data['removeOldPosts']['value'] * 86400 ) . '</strong> days)<br />';
					continue;
				}
			}
			
			$str = '<br><em><p><strong>';
			
			if ( !empty( $this->data ) )
			{
				if ( empty( $this->data['removeLinks']['value'] ) )
				{
					$str .= '<a>';
				}

				if ( empty( $this->data['removeHtml']['value'] ) )
				{
					$str .= '<ul><li><quote><blockqoute><code><embed><img>';
				}
			}
			
			else
			{
				$str .= '<a><ul><li><quote><blockqoute><code><embed><img>';
			}

			$title 	 = $Bot->match( $regTitle );
			$descr 	 = $Bot->match( $regDescr );
			$cat 	 = $Bot->match( $regCat );
			$img 	 = $Bot->match( $regImg );
			$content = $Bot->match( $regCont );
			
			$content = strip_tags( $content, $str );
			
			$content = stripslashes( html_entity_decode( $content ) );
			
			//Skip words
			if ( $this->SkipWords( $title ) )
			{
				$this->message .= $title . ' <strong>skipped</strong> (title contains a skip word)<br />';
				continue;
			}

			if ( empty( $img ) && !empty( $this->data['autoSetFirstImage']['value'] ) )
			{
				$img = $this->GetFirstImage( $content );
			}
			
			$fileName = null;
			
			if ( !empty( $img ) )
			{
				$fileName = $this->ImgFilename( $img, $title );
			}
			
			if ( !empty( $this->replace ) )
			{
				foreach( $this->replace as $re )
				{
					$content = str_replace( $re['name'], $re['value'], $content );
				}
			}
			
			//$content = preg_replace('#(?:<br.*/? >\s*?){2,}#', '</p><p>', $content);

			$tags 	 = array();
			$tag 	 = $Bot->match( $regConten );
			
			if ( !empty( $regConten ) && !empty( $tag ) )
			{
				$_tags = $Bot->match( $regTags, $tag );
			}
			
			else
			{
				$_tags = $Bot->match( $regTags, $tag );
			}
			
			if ( !empty( $_tags ) )
			{
				if ( is_array( $_tags ) )
				{
					foreach( $_tags as $_tag )
					{
						$tags[] = array( 'value' => $_tag );
					}
				}
					
				//This is for only one tag
				elseif ( is_string( $_tags ) )
				{
					$tags[] = array( 'value' => (string) $_tags );
				}
			}
	
			//To avoid empty titles
			if ( $title == '' )
			{
				$this->emptyTitle++;
				$title = 'Empty Title - ' . $this->emptyTitle;
			}

			$args = array(
				'siteId' 		=> $this->siteId,
				'langId' 		=> $this->langId,
				'blogId' 		=> $this->blogId,
				'postDate' 		=> $date,
				'postSource' 	=> $loc,
				'uuid' 			=> $p_id,
				'userId' 		=> $this->userID,
				'postFormat' 	=> $this->postTypeId,
				'postStatus' 	=> $this->postStatus,
				'postType' 		=> 'post',
				'fileName' 		=> $fileName,
				'tags' 			=> $tags,
				'title' 		=> $title,
				'description' 	=> ( !empty( $descr ) ? $this->descr( $descr ) : $this->descr( $content ) ),
				'content' 		=> $content,
				'image' 		=> $img,
				'subCategoryId' => 0,
				'copyImage' 	=> $this->copyImages,
				'categoryId' 	=> $this->category( $cat )
			);

			//Add the post to the DB
			$this->AddPost( $args );
			
			if ( $this->currentItem == $this->maxItemsPerTime )
			{
				break;
			}
		}

		$this->_exit();
		$this->SetFinished();
	}
}