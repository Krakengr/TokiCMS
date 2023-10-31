<?php

class WP extends _Import
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

		//Load the attachments into an array
		$this->attachments();
		
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
		foreach( $this->xml->channel->item as $item ) 
		{
			$type = $item->xpath('wp:post_type');
			$type = (string) $type['0'];			

			//Skip any item that is not a post or a page
			if( ( $type != 'post' ) && ( $type != 'page' ) )
			{
				continue;
			}

			$this->totalItems++;
		}
	}
	
	private function convertXML() 
	{
		$this->totalItems = 0;
		
		//Count the items
		foreach( $this->xml->channel->item as $item ) 
		{
			$type = $item->xpath('wp:post_type');
			$type = (string) $type['0'];			

			//Skip any item that is not a post or a page
			if( ( $type != 'post' ) && ( $type != 'page' ) )
			{
				continue;
			}
			
			$this->totalItems++;
		}

		//Let's begin the conversion...
		foreach( $this->xml->channel->item as $item ) 
		{
			$type = $item->xpath('wp:post_type');
			$type = (string) $type['0'];			

			//Skip any item that is not a post or a page
			if( ( $type != 'post' ) && ( $type != 'page' ) )
			{
				continue;
			}
			
			$this->countItem++;

			//Get the post's status
			$status = $item->xpath('wp:status');
			$status = $status['0'];
			
			//Get the post's title
			$title = (string) $item->title;
			
			//Get the post's date
			$date = $item->xpath('wp:post_date');
			$date = (string) $date['0'];

			//Keep the original post sef
			$sef = $item->xpath('wp:post_name');
			$sef = (string) $sef['0'];

			//To avoid empty titles
			if ( $title == '' )
			{
				$this->emptyTitle++;
				$title = 'Empty Title - ' . $this->emptyTitle;
			}
			
			if ( $sef == '' )
			{
				$sef = CreateSlug( $title );
			}
			
			//Get the post's id
			$p_id = $item->xpath('wp:post_id');
			$p_id = (int) $p_id['0'];
			
			
			if ( $this->CheckItemInImports( $p_id ) )
			{
				continue;
			}

			//Get the post's comment status
			$comm = $item->xpath('wp:comment_status');
			$comm = $comm['0'];
				
			//Let's set the post's status
			$post_status = ( ($status == 'draft') ? 'draft' : ( ( strtotime($date) > time() ) ? 'scheduled' : 'published' ) );
			
			//...and the type of the post
			$post_type = ( $this->postType ? $this->postType : ( ( $type == 'post' ) ? 'post' : 'page' ) );
				
			//Set the comment status of the post
			$comment_status = ( ($comm == 'open') ? 'true' : 'false' );
				
			//Does this post has any comment?
			$comment = $item->xpath('wp:comment');
			
			//Get the post's content
			$content = $item->xpath('content:encoded');
			$content = (string) $content['0'];
			
			//Find the category of the post
			$cat_name = '';
			$cat_pos = 0;
			
			foreach ($item->category as $c) 
			{
				$att = $c->attributes();

				if ( $att['domain'] == 'category' ) 
				{
					//We need only one category, at least for now...
					if ($cat_pos == 1)
						break;
	
					$cat_pos++;

					$cat_name = (string) $c;
				}
			}
			
			//Add the tags
			$p_tags = array();
		
			foreach ( $item->category as $c) 
			{
				$att = $c->attributes();

				if ($att['domain'] == 'post_tag') 
				{
					$tag_name = (string) $c;

					$p_tags[] = array( 'value' => $tag_name );
				}
				
			}

			//Set this category, or add a new one if needed
			$this->category( $cat_name );

			//Add the post to the DB
			$this->AddPost( $title, $content, $post_status, $post_type, $date );
			
			$this->AddItems( $p_id );
			
			//Stop here if the post exists or not added into the DB
			if ( !$this->postId )
			{
				continue;
			}

			//Replace the content images
			$this->ReplaceContentImages( $content, $date );
			
			
			

			if ( $this->brake )
				break;
		}
		
		$this->_exit();
		$this->SetFinished();
	}
	
	public function attachImage( $pId ) 
	{
		if ( empty( $this->attachments ) || !isset( $this->attachments[$pId] ) )
			return;
		
		//Add the image
		$this->AddAttachImage( $this->attachments[$pId]['url'], $this->attachments[$pId]['name'] );
	}

	public function attachments() 
	{
		//Keep all the nessecary attachments in an array. We need them for later...
		foreach($this->xml->channel->item as $item) 
		{			
			$type = $item->xpath('wp:post_type');
			$type = $type['0'];

			if ($type == 'attachment') 
			{
				$p_id = $item->xpath('wp:post_id');
				$p_id = (int) $p_id['0'];
				
				$parent_id = $item->xpath('wp:post_parent');
				$parent_id = (int) $parent_id['0'];
				
				$attachment_url = $item->xpath('wp:attachment_url');
				$attachment_url = (string) $attachment_url['0'];
					
				$info = pathinfo($attachment_url);
				$attachment_name =  $info['basename'];
				
				if (!empty($info['extension']) && strpos( $attachment_name, $info['extension'] ) === false )
					$attachment_name = $info['filename'] . '.' . $info['extension'];

				//Put every attachment in an array, we will need them later...
				$this->attachments[$parent_id] = array(
					'name' => $attachment_name, 
					'url' => $attachment_url 
				);
			}
		}
	}
}