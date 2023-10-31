<?php defined('TOKICMS') or die('Hacking attempt...');

// No cache headers
header("Expires: Sat, 1 Jan 2000 01:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

require( CLASSES_ROOT . 'Bot.php' );

class Cron extends Controller {
	
	private $action;
	private $settings;
	private $log;
	private $id;
	private $taskId;
	private $taskName;
	private $timeExec;
	private $coverImg;
	private $userId;
	private $siteId;
	private $langId;
	private $img;
	
    public function process() 
	{		
		$this->setVariable( 'Lang', $this->lang );
		
		$res = array();

		//Check the needed parameters
		if ( !isset( $_GET['token'] ) )
		{
			Log::Set( 'Cron bad request', 'Missing GET parameter (token)', $_GET, 'system', 0, $this->siteId );
			$this->Response( 400, 'Bad Request', array( 'message' => 'Missing GET parameter (token).' ) );
		}
		
		$token 			= Sanitize( trim( $_GET['token'] ), false );
		$this->action 	= ( isset( $_GET['action'] ) 	? Sanitize( trim( $_GET['action'] ), false ) : null );
		$this->id 		= ( isset( $_GET['id'] ) 		? (int) $_GET['id'] : 0 );
		$this->siteId 	= ( isset( $_GET['site'] ) 		? (int) $_GET['site'] : SITE_ID );

		//Check the TOKEN
		if ( !hash_equals( $token, MAIN_HASH ) )
		{
			Log::Set( 'Cron unauthorized token', $token, $_GET, 'system', 0, $this->siteId );
			$this->Response( 401, 'Unauthorized', array( 'message' => 'Invalid token.' ) );
		}
		
		//Avoid system dying...
		@set_time_limit( 600 );

		//We need memory for some functions
		if ( @ini_get( 'memory_limit' ) < 256 )
			@ini_set( 'memory_limit', '256M' );

		//Load the settings for this site
		$this->LoadSettings();
		
		//Set the log settings
		$this->log = $this->settings::LogSettings();

		//If we want a particular function, do it here
		if ( $this->action )
		{
			switch ( $this->action )
			{
				//Run the maintenance functions
				case 'maintenance':
					$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
				break;
				
				//Check for prices
				case 'prices':
					$this->CheckPrices();
					$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
				break;
				
				//Check for articles
				case 'autoblog':
					$this->AutoBlog();
					$this->Response( 200, 'OK', array( 'message' => 'Success' ) );
				break;

				default:
					$this->Response( 400, 'Bad Request', array( 'message' => 'Bad Request' ) );
			}
		}
		
		//Check and run the task
		else
		{
			$this->GetNextTask();
			
			if ( !empty( $this->taskName ) )
			{
				$this->Response( 200, 'OK', array( 'message' => 'Success', 'task' => $this->taskName ) );
			}
			
			else
			{
				$this->Response( 401, 'Error', array( 'message' => 'An error occurred, try again' ) );
			}
		}
		
		exit;
	}
	
	#####################################################
	#
	# Get the next task function
	#
	#####################################################
	private function GetNextTask()
	{
		$q = $this->db->from( null, 
		"SELECT id_task, task, time_offset, time_regularity, time_unit, times_run
		FROM `" . DB_PREFIX . "scheduled_tasks`
		WHERE (id_site = " . $this->siteId . ") AND (disabled = 0) AND (next_time < " . time() . ")
		ORDER BY id_task ASC
		LIMIT 1"
		)->single();
		
		if ( !$q )
			return;
		
		$this->taskId 	= $q['id_task'];
		$this->taskName = $q['task'];
		
		// Start clock time in seconds
		$start_time = microtime(true);
		$run_time   = time();
		
		switch ( $q['task'] ) {
			case 'prune-log-topics':
				$this->LogPruning();
				break;

			case 'mark-boards-as-read':
				$this->MarkBoardsRead();
				break;
				
			case 'daily-maintenance':
				$this->DailyMaintenance();
				break;
				
			case 'backup-db':
				BackupDBToFile();
				break;
				
			case 'bot-digest':
				$this->BotDigest();
				var_dump( $q );exit;exit;
				break;
				
			case 'broken-link-check':
				$this->BrokenLinkCheck();
				break;
		}
		
		// End clock time in seconds
		$end_time = microtime(true);
  
		// Calculate script execution time
		$this->timeExec = ( $end_time - $start_time );
		
		$next_time = $times_run = 0;
		
		if ( $q['time_unit'] == 'd' )
		{
			if ( $q['time_regularity'] == 1 )
			{
				$next_time = strtotime('+1 day');
			}
			
			elseif ( ( $q['time_regularity'] > 1 ) && ( $q['times_run'] < $q['time_regularity'] ) && ( $q['time_offset'] > 60 ) )
			{
				$next_time = ( time() + ( 60 * $q['time_offset'] ) );
				
				$times_run = $q['times_run'] + 1;
			}
		}
		
		elseif ( $q['time_unit'] == 'm' )
		{
			if ( $q['time_regularity'] == 1 )
			{
				$next_time = ( time() + 60 );
			}
			
			elseif ( ( $q['time_regularity'] > 1 ) && ( $q['times_run'] < $q['time_regularity'] ) && ( $q['time_offset'] > 60 ) )
			{
				$next_time = ( time() + ( 60 * $q['time_offset'] ) );
				$times_run = $q['times_run'] + 1;
			}
		}
		
		elseif ( $q['time_unit'] == 'h' )
		{
			if ( $q['time_regularity'] == 1 )
			{
				$next_time = ( time() + ( 60 * 60 ) );
			}
			
			elseif ( ( $q['time_regularity'] > 1 ) && ( $q['times_run'] < $q['time_regularity'] ) && ( $q['time_offset'] > 60 ) )
			{
				$next_time = ( time() + ( 60 * $q['time_offset'] ) );
				$times_run = $q['times_run'] + 1;
			}
		}
		
		elseif ( $q['time_unit'] == 'w' )
		{
			if ( $q['time_regularity'] == 1 )
			{
				$next_time = strtotime('+1 week');
			}
			
			elseif ( ( $q['time_regularity'] > 1 ) && ( $q['times_run'] < $q['time_regularity'] ) && ( $q['time_offset'] > 60 ) )
			{
				$times = ( ( $q['time_regularity'] > 7 ) ? 7 : $q['time_regularity'] );
				$days_in_week = ceil( 7 / $times );
				$next_time = ( strtotime('+' . $days_in_week . ' day') + ( 60 * $q['time_offset'] ) );
				$times_run = $q['times_run'] + 1;
			}
		}
		
		$dbarr = array(
			"next_time" => $next_time,
			"times_run" => $times_run
        );
		
		//Update the task
		$this->db->update( "scheduled_tasks" )->where( 'id_task', $this->taskId )->set( $dbarr );
		
		//Add the log
		$dbarr = array(
			"id_task" 		=> $this->taskId,
			"time_run" 		=> $run_time,
			"time_taken" 	=> $this->timeExec,
			"added_time" 	=> time(),
			"id_site" 	 	=> $this->siteId
        );
            
		$this->db->insert( 'log_scheduled_tasks' )->set( $dbarr );
	}
	
	#####################################################
	#
	# Retrieves latest posts and other information such as price changes, autoblog posts etc. function
	#
	#####################################################
	private function BotDigest()
	{
		//Check of prices changes
		$this->CheckPrices();
		
		//Get one auto content source
		$this->AutoBlog();
	}
	
	#####################################################
	#
	# Retrieves/updates posts changes function
	#
	#####################################################
	private function AutoBlog()
	{
		if ( $this->settings::IsTrue( 'enable_autoblog' ) )
		{
			if ( !empty( $this->id ) )
			{
				$q = $this->db->from( null, 
				"SELECT *
				FROM `" . DB_PREFIX . "auto_sources`
				WHERE (id_site = " . $this->siteId . ") AND (id = " . $this->id . ")"
				)->single();
			}
			
			else
			{
				$q = $this->db->from( null, 
				"SELECT *
				FROM `" . DB_PREFIX . "auto_sources`
				WHERE (id_site = " . $this->siteId . ")
				ORDER BY last_checked ASC
				LIMIT 1"
				)->single();
			}
			
			if ( $q )
			{
				if ( $q['source_type'] == 'rss' )
				{
					$this->GetAutoContentByFeed( $q );
				}
				
				elseif ( $q['source_type'] == 'multi' )
				{
					$this->GetAutoContentByMultiFeed( $q );
				}
				
				//else if ( $q['source_type'] == 'html' )
				//{
				//	$this->GetAutoContentByHtml( $q );
				//}
				
				else if ( $q['source_type'] == 'xml' )
				{
					$this->GetAutoContentByXml( $q );
				}
				
				$this->db->update( "auto_sources" )->where( 'id', $q['id'] )->set( "last_checked", time() );
			}			
		}
	}
	
	#####################################################
	#
	# Retrieves latest autoblog posts by multifeed function
	#
	#####################################################
	private function GetAutoContentByMultiFeed( $arr )
	{
		$Bot 			= new Bot;
		$this->img 		= new Image( $this->siteId );
		$options 		= array();
		$url 			= $arr['url'];
		$posts 			= GetFeed( $url, $arr['max_posts'] );
		$arrData		= array();
		
		if ( !$posts )
		{
			return;
		}
		
		foreach( $posts as $post )
		{
			$Bot->url = $post['url'];

			try
			{
				$Bot->process();
				
				if ( $Bot->status == 200 )
				{
					$url = $Bot->match( "data-n-au=\"(.*)\"" );
					$url = ( !empty( $url ) ? $url : $Bot->match( "<a.+href=\"(.*)\".+>" ) );

					if ( !empty( $url ) )
					{
						$host = GetTheHostName( $url );
						
						$x = $this->db->from( 
						null, 
						"SELECT * 
						FROM `" . DB_PREFIX . "auto_sources`
						WHERE (id_site = " . $this->siteId . ") AND (url LIKE '%" . $host . "%')"
						)->single();

						if ( !$x )
						{
							continue;
						}
						
						$arrData[] = array(
							'url' 	=> $url,
							'cron'	=> $x
						);
					}
				}
			}
				
			catch (\Exception $e)
			{
				continue;
			}
		}
		
		if ( empty( $arrData ) )
		{
			return;
		}
		
		foreach( $arrData as $cron )
		{
			GetContentFromUrl( $cron['cron'], $this->siteId, null, null, null, $cron['url'] );
		}
		
		var_dump( $arrData );exit;
		
		exit;
	}
	
	#####################################################
	#
	# Retrieves latest autoblog posts function
	#
	#####################################################
	private function GetAutoContentByFeed( $arr )
	{
		$Bot 			= new Bot;
		$this->img 		= new Image( $this->siteId );
		$options 		= array();
		$url 			= $arr['url'];
		$posts 			= GetFeed( $url, $arr['max_posts'] );
		$custom 		= Json( $arr['custom_data'] );
		$regex  		= ( !empty( $custom['regex'] ) ? $custom['regex'] : null );
		$search 		= ( !empty( $custom['search_replace'] ) ? $custom['search_replace'] : null );
		$customFields 	= ( !empty( $regex['custom_fields'] ) ? $regex['custom_fields'] : null );
		$skip			= ( ( $arr['skip_posts_days'] > 0 ) ? ( $arr['skip_posts_days'] * 86400 ) : null );
		$catData		= array();
		
		if ( !$posts )
		{
			return;
		}

		$options['randomIp']['value'] 			= ( isset( $regex['rotate_ip_address'] ) ? $regex['rotate_ip_address'] : null );
		$options['crawlAsGoogleBot']['value'] 	= ( isset( $regex['crawl_as'] ) ? $regex['crawl_as'] : 'normal' );
		
		//Get some data from the category
		if ( !empty( $arr['id_category'] ) )
		{
			$catData = $this->db->from( 
			null, 
			"SELECT id_lang, id_blog, id_parent
			FROM `" . DB_PREFIX . "categories`
			WHERE (id = " . $arr['id_category'] . ") AND (id_site = " . $arr['id_site'] . ")"
			)->single();
				
			if ( !$catData )
			{
				return;
			}
		}

		elseif ( !empty( $arr['auto_category'] ) )
		{
			
		}

		else
		{
			return;
		}
		
		$Bot->options 	= $options;
		$copyImg		= IsTrue( $arr['copy_images'] );
		$firstAsCover 	= IsTrue( $arr['set_first_image_cover'] );
		$setSource 		= IsTrue( $arr['set_source_link'] );
		$skipNoImg 		= IsTrue( $arr['skip_posts_no_images'] );
		$template		= $arr['post_template'];
		$status			= $arr['post_status'];
		$type			= $arr['post_type'];
		$this->userId	= $arr['user_id'];
		$siteId			= $arr['id_site'];
		$this->langId	= ( !empty( $catData['id_lang'] ) ? $catData['id_lang'] : 0 );
		$blogId			= ( !empty( $catData['id_blog'] ) ? $catData['id_blog'] : 0 );
		$catId			= ( empty( $catData['id_parent'] ) ? $arr['id_category'] : $catData['id_parent'] );
		$subCatId		= ( empty( $catData['id_parent'] ) ? 0 : $arr['id_category'] );
		$addTitleTags	= (int) $arr['add_tags'];
		$avoid			= trim( strtolower( $arr['avoid_words'] ) );
		$have			= trim( strtolower( $arr['required_words'] ) );
		$haveWords 		= null;
		$avoidWords 	= null;
		$langCode 		= ( !empty( $this->langId ) ? GetLangKey( $this->langId ) : null );

		if ( !empty( $avoid ) && str_contains( $avoid, ',' ) )
		{
			$avoidWords = explode( ',', $avoid );
		}
			
		if ( !empty( $have ) && str_contains( $have, ',' ) )
		{
			$haveWords = explode( ',', $have );
		}
		
		$str = '<br><em><p><strong>';
			
		if ( !IsTrue( $arr['strip_links'] ) )
		{
			$str .= '<a>';
		}
		
		if ( !IsTrue( $arr['remove_images'] ) )
		{
			$str .= '<img>';
		}

		if ( !IsTrue( $arr['strip_html'] ) )
		{
			$str .= '<ul><li><quote><blockqoute><code><embed>';
		}
		
		//Set generic info for images
		$this->img->userId 		= $this->userId;
		$this->img->langId 		= $this->langId;
		$this->img->isExternal 	= true;
		
		foreach( $posts as $post )
		{
			if ( $skip && ( ( time() - $skip ) > $post['dateUnix']  ) )
			{
				continue;
			}

			$postDate 		= ( !IsTrue( $arr['set_original_date'] ) ? time() : $post['dateUnix'] );
			$postUpdated	= 0;
			$postTitle		= $post['title'];
			$postDescr		= $post['descr'];
			$postAlias		= '';
			$postSubtitle	= '';
			$xtraPostData	= '';
			$postImg		= null;
			$postHas		= ( !empty( $have ) ? false : true );
			$postContains	= false;
			$postTags		= array();
			$postContent	= $postDescr;
			$uri			= preg_replace( '/((\?|&amp;|&#038;)utm_source=.*)?((&amp;|&#038;)utm_campaign=.*)?(&amp;|&#038;)?/', '', $post['url'] );
			$tags			= array();
			$postImages		= array();
			$tempTitle 		= strtolower( $postTitle );
			$Bot->url 		= $uri;
			$uuid 			= md5( $uri );
			
			//Check if this post exists
			$ex = $this->db->from( 
			null, 
			"SELECT id, id_post
			FROM `" . DB_PREFIX . "posts_data`
			WHERE (uuid = :uuid)",
			array( $uuid => ':uuid' )
			)->single();
			
			if ( $ex )
			{
				continue;
			}
			
			//Let's see if there is a word we don't want (or do?)
			$postContains 	= ( ( empty( $avoid ) && empty( $avoidWords ) ) ? false : $this->Search( $avoid, $avoidWords, $tempTitle ) );
			
			//Do the same for "have" words
			$postHas 		= ( ( empty( $have ) && empty( $haveWords ) ) ? true : $this->Search( $have, $haveWords, $tempTitle ) );

			if ( $postContains || !$postHas )
			{
				continue;
			}

			if ( !empty( $regex ) )
			{
				try
				{
					$Bot->process();
				
					if ( $Bot->status == 200 )
					{
						if ( !empty( $regex['regex_title'] ) )
						{
							$title 		= $Bot->match( $regex['regex_title'] );
							$postTitle	= ( !empty( $title ) ? StripContent( $title ) : $postTitle );
						}

						if ( !empty( $regex['regex_image'] ) )
						{
							$im 		= $Bot->match( $regex['regex_image'] );
							$postImg	= ( !empty( $im ) ? StripContent( $im ) : $postImg );
						}
						
						$this->coverImg = ( !empty( $postImg ) ? $postImg : null );

						if ( !empty( $regex['regex_descr'] ) )
						{
							$descr 		= $Bot->match( $regex['regex_descr'] );
							$postDescr	= ( !empty( $descr ) ? StripContent( $descr ) : $postDescr );
						}

						if ( !empty( $regex['regex_content'] ) )
						{
							$con 			= $Bot->match( $regex['regex_content'] );
							$postContent	= ( !empty( $con ) ? StripContent( $con ) : $postContent );
						}
						
						if ( $copyImg )
						{
							$postImages = $this->SearchContentImages( $postContent, $firstAsCover );
						}
						
						//Skip this post if no images found
						if ( $skipNoImg && empty( $postImages['1'] ) )
						{
							continue;
						}

						if ( !empty( $search ) )
						{
							foreach( $search as $re )
							{
								$postContent = str_replace( $re['search'], $re['replace'], $postContent );
							}
						}
						
						if ( $type == 'post' )
						{
							if ( !empty( $regex['regex_tags'] ) )
							{
								$tags = $Bot->match( $regex['regex_tags'] );
							}
						
							//The previous match found nothing. Check the container
							if ( empty( $tags ) && !empty( $regex['regex_tags'] && !empty( $regex['regex_tags_container'] ) ) )
							{
								$tagC = $Bot->match( $regex['regex_tags_container'] );
								
								if ( !empty( $tagC ) )
								{
									$tags = $Bot->match( $regex['regex_tags'], $tagC );
								}
							}
							
							//Add the tags from the title
							if ( ( $addTitleTags > 0 ) && empty( $tags ) )
							{
								$i 		= 0;
								$tags 	= array();
								$tags_ 	= preg_split("/[\s]+/", $postTitle, -1, PREG_SPLIT_NO_EMPTY );
								
								if ( !empty( $tags_ ) )
								{
									foreach( $tags_ as $tag )
									{
										if ( $i >= $addTitleTags )
										{
											break;
										}
										
										$str = preg_replace( '/[\W]/', '', $tag );
										
										if ( strlen( $str ) > 2 )
										{
											$tags[] = $str;
											$i++;
										}
									}
								}
							}
							
							if ( !empty( $tags ) )
							{
								if ( is_array( $tags ) )
								{
									foreach( $tags as $tag )
									{
										$postTags[] = array( 'value' => $tag );
									}
								}

								//This is for only one tag
								elseif ( is_string( $tags ) )
								{
									$postTags[] = array( 'value' => $tags );
								}
							}
						}

						if ( !empty( $customFields ) )
						{
							foreach( $customFields as $cus )
							{
								$s = _explode( $cus['field'], '::' );
								
								if ( $s['target'] == 'pre' )
								{
									if ( $s['id'] == 'updated' )
									{
										$updt 			= $Bot->match( $cus['value'] );
										$postUpdated	= ( !empty( $updt ) ? @strtotime( $updt ) : 0 );
									}
									
									if ( $s['id'] == 'alias' )
									{
										$alias 		= $Bot->match( $cus['value'] );
										$postAlias	= ( !empty( $alias ) ? StripContent( $alias ) : '' );
									}
									
									if ( $s['id'] == 'subtitle' )
									{
										$subtitle 		= $Bot->match( $cus['value'] );
										$postSubtitle	= ( !empty( $subtitle ) ? StripContent( $subtitle ) : '' );
										
										$xtraPostData = array(
											'subtitle' => $postSubtitle
										);
									}
								}
							}
						}

						//Add the post
						$dbarr = array(
							"id_site" 			=> $siteId,
							"id_lang" 			=> $this->langId,
							"id_blog" 			=> $blogId,
							"title" 			=> $postTitle,
							"post" 				=> '',
							"description" 		=> $postDescr,
							"post_status" 		=> $status,
							"added_time" 		=> $postDate,
							"edited_time" 		=> $postUpdated,
							"sef" 				=> '',
							"id_category" 		=> $catId,
							"post_type" 		=> $type,
							"poster_ip" 		=> GetRealIp(),
							"id_member" 		=> $this->userId,
							"id_sub_category" 	=> $subCatId
						);

						$postId = $this->db->insert( POSTS )->set( $dbarr, null, true );

						if ( !$postId )
						{
							continue;
						}

						$this->img->time 	= $postDate;
						$this->img->postId 	= $postId;
		
						$slug = SetShortSef( POSTS, 'id_post', 'sef', CreateSlug( $postTitle, true ), $postId );
		
						$this->db->update( POSTS )->where( 'id_post', $postId )->set( "sef", $slug );
						
						//Check for any images in the content and update it
						if ( !empty( $postImages ) && $copyImg )
						{
							$postContent = $this->ReplaceContentImages( $postContent, $postImages, $postId, $postDate );
						}
						
						$postContent = trim( strip_tags( $postContent, $str ) );
						
						if ( !empty( $template ) )
						{
							$search 		= array ( '{{title}}', '{{description}}', '{{content}}', '{{source-url}}', '{{image-url}}', '{{more}}' );
							
							$replace 		= array ( $postTitle, $postDescr, $postContent, $uri, $this->coverImg, '<!--more-->' );
							
							$postContent 	= trim( str_replace( $search, $replace, $postContent ) );
							
						}
						
						if ( $setSource )
						{
							$postContent .= '
							<p><a href="' . $uri . '" target="_blank" rel="noopener nofollow">' . __( 'source' ) . '</a></p>';
						}

						$this->db->update( POSTS )->where( 'id_post', $postId )->set( "post", $postContent );
						
						if ( $blogId > 0 )
						{
							$this->db->update( "blogs" )->where( "id_blog", $blogId )->increase( "num_posts" );
						}
						
						if ( $type == 'post' )
						{
							if ( $catId > 0 )
							{
								$this->db->update( "categories" )->where( "id", $catId )->increase( "num_items" );
							}
							
							if ( $subCatId > 0 )
							{
								$this->db->update( "categories" )->where( "id", $subCatId )->increase( "num_items" );
							}
							
							if ( !empty( $postTags ) )
							{
								AddTags( $postTags, $postId, $this->langId, $siteId, 0 );
							}
						}

						$dbarr = array(
							"id_post" 				=> $postId,
							"uuid" 					=> $uuid,
							"original_import_time" 	=> time(),
							"ext_id" 				=> $arr['id'],
							"title_alias" 			=> $postAlias,
							"value1" 				=> '',
							"value2" 				=> '',
							"value3" 				=> '',
							"value4" 				=> $xtraPostData
						);

						$this->db->insert( "posts_data" )->set( $dbarr );
						
						//Add the post product data
						$this->db->insert( "posts_product_data" )->set( array( "id_post" => $postId ) );
						
						//Set the cover image
						$this->AddImageAsCover();
						
						//Update the cover_img value
						$coverImg = PostImageDetails( $postId, $langCode );

						if ( !empty( $coverImg ) )
						{
							$this->db->update( POSTS )->where( 'id_post', $postId )->set( "cover_img", json_encode( $coverImg, JSON_UNESCAPED_UNICODE ) );
						}
						
						//Add the custom data as post attributes
						if ( !empty( $customFields ) )
						{
							foreach( $customFields as $cus )
							{
								$s = _explode( $cus['field'], '::' );
								
								if ( ( $s['target'] == 'att' ) && is_numeric( $s['id'] ) )
								{
									$val 	= $Bot->match( $cus['value'] );
									$value	= ( !empty( $val ) ? StripContent( $val ) : null );
									
									if ( !empty( $value ) )
									{
										$dbarr = array(
											"id_post" 	=> $postId,
											"id_attr" 	=> $s['id'],
											"value" 	=> $value
										);

										$this->db->insert( "post_attribute_data" )->set( $dbarr );
									}
								}
							}
						}
					}
				}
				
				catch (\Exception $e)
				{
					continue;
				}
				
			}
		}
	}
	
	#####################################################
	#
	# Retrieves data from HTML function
	#
	#####################################################
	private function GetAutoContentByHtml( $arr )
	{
		
	}
	
	#####################################################
	#
	# Retrieves/updates price changes function
	#
	#####################################################
	private function CheckPrices()
	{
		$Bot 			= new Bot;
		$this->img		= new Image( $this->siteId );
		$botData 		= array();
		$botError		= array();
		$deletePrices	= ( !empty( $this->log['delete_prices'] ) ? (int) $this->log['delete_prices'] : 0 );

		$q = $this->db->from( null, 
		"SELECT p.main_page_url, p.id_price, p.id_store, p.id_post, p.sale_price,
		p.original_price, p.title, pi.last_time_checked, pi.num_retries, pi.in_stock,
		s.scrape_as, s.rotate_ip, s.retrieve_json_data, s.json_data
		FROM `" . DB_PREFIX . "prices` AS p
		INNER JOIN `" . DB_PREFIX . "stores`     AS s  ON s.id_store = p.id_store
		LEFT JOIN `" . DB_PREFIX . "price_info`  AS pi ON pi.id_price = p.id_price
		WHERE (p.id_site = " . $this->siteId . ")
		ORDER BY pi.last_time_checked ASC
		LIMIT 5"
		)->all();
		
		if ( empty( $q ) )
		{
			return;
		}

		foreach( $q as $pr )
		{
			//Query: price info data
			$in = $this->db->from( null, "
			SELECT id
			FROM `" . DB_PREFIX . "price_info`
			WHERE (id_price = " . $pr['id_price'] . ")"
			)->single();
			
			//Add the price info if is missing
			if ( !$in )
			{
				$this->db->insert( 'price_info' )->set( array( "id_price" => $pr['id_price'] ) );
			}
			
			//Query: store data
			$s = $this->db->from( null, "
			SELECT reg_data, key_value
			FROM `" . DB_PREFIX . "stores_data`
			WHERE (id_store = " . $pr['id_store'] . ")"
			)->all();
			
			if ( !$s )
			{
				if ( !empty( $this->log ) && $this->log['enable_bot_error_log'] )
				{
					$errorMessage = 'Store has no data (Store id: "' . $pr['id_store'] . '" - URL: ' . $pr['main_page_url'] . ')';

					Log::Set( $errorMessage, null, null, 'system', 0, $this->siteId );
				}

				$dbarr = array(
					"last_time_checked"	=> time(),
					"num_retries" 		=> ( $pr['num_retries'] + 1 )
				);
				
				$this->db->update( "price_info" )->where( 'id_price', $pr['id_price'] )->set( $dbarr );

				continue;
			}
			
			$options 								= array();
			$errors 								= array();
			$Bot->options 							= $options;
			$Bot->url 								= $pr['main_page_url'];
			$postId 								= $pr['id_post'];
			$jsonData								= ( !empty( $pr['json_data'] ) ? Json( $pr['json_data'] ) : null );
			$getJsonData							= $pr['retrieve_json_data'];
			$options['randomIp']['value'] 			= ( !empty( $pr['rotate_ip'] ) ? true : false );
			$options['crawlAsGoogleBot']['value'] 	= ( !empty( $pr['scrape_as'] ) ? $pr['scrape_as'] : 'normal' );
			$botData[$pr['id_price']] 				= array( 'data' => array() );
			$botData[$pr['id_price']]['sale_price'] = $pr['sale_price'];
			$botData[$pr['id_price']]['or_price'] 	= $pr['original_price'];
			$botData[$pr['id_price']]['retries']	= $pr['num_retries'];
			$botData[$pr['id_price']]['url']		= $pr['main_page_url'];
			$botData[$pr['id_price']]['title']		= $pr['title'];
			$botData[$pr['id_price']]['in_stock']	= $pr['in_stock'];
			
			//Set the URL if JSON data is requested
			if ( $getJsonData && !empty( $jsonData ) )
			{
				if ( !empty( $jsonData['url'] ) && !empty( $jsonData['values'] ) )
				{
					$search = $replace = array();
					
					$url = $jsonData['url'];
					
					foreach( $jsonData['values'] as $val )
					{
						$search[] = '{{' . $val['key'] . '}}';
						
						if ( $val['field'] === 'custom' )
						{
							$replace[] = $val['value'];
						}
						
						elseif ( is_numeric( $val['field'] ) )
						{
							$attDb = $this->db->from( 
							null, 
							"SELECT value
							FROM `" . DB_PREFIX . "post_attribute_data`
							WHERE (id_attr = " . $val['field'] . ") AND (id_post = " . $postId . ")"
							)->single();
							
							if ( $attDb )
							{
								$replace[] = $attDb['value'];
							}
						}
					}
					
					$url = str_replace( $search, $replace, $url );
					
					if ( !empty( $url ) )
					{
						$Bot->url = $url;
					}
				}
			}
			
			//Try to parse the page
			try
			{
				$Bot->process();

				if ( $Bot->status == 200 )
				{
					foreach( $s as $k )
					{
						if ( empty( $k['reg_data'] ) )
						{
							continue;
						}
						
						$regex = RegexReplace( $k['reg_data'] );
						
						$val = $Bot->match( $regex );
						
						if ( !empty( $val ) )
						{
							$botData[$pr['id_price']]['data'][$k['key_value']] = $val;
						}
						
						else
						{
							$errors[] = $k['key_value'];
						}
					}
				}
			}
			
			catch (\Exception $e)
			{
				//
			}
			
			$botData[$pr['id_price']]['status'] = $Bot->status;
			
			if ( !empty( $errors ) )
			{
				$botError[$pr['id_price']] = array( 'url' => $Bot->url, 'keys' => $errors );
			}
			
			/*if ( !empty( $errors ) && !empty( $this->log ) && $this->log['enable_bot_error_log'] )
			{
				$errorMessage = 'Request data couldn\'t be parsed for key(s): ' . implode( ', ', $errors );
				
				Log::Set( $errorMessage, null, $pr, 'system', 0, $this->siteId );
			}*/
		}
		
		//Now update the DB
		if ( !empty( $botData ) )
		{
			foreach( $botData as $pid => $pt )
			{
				$notFound 	= ( ( $pt['status'] == 200 ) ? 0 : 1 );
				
				if ( empty( $pt['data'] ) )
				{
					$retries 	= ( $pt['retries'] + 1 );
					$pDeleted 	= false;
					
					if ( $notFound && ( $deletePrices > 0 ) && ( $retries > $deletePrices ) )
					{
						DeletePrice( $pid );
						$pDeleted = true;
					}
					
					else
					{
						$dbarr = array(
							"last_time_checked"	=> time(),
							"num_retries" 		=> $retries,
							"not_found" 		=> $notFound
						);

						$this->db->update( "price_info" )->where( 'id_price', $pid )->set( $dbarr );
					}
					
					if ( !empty( $this->log ) && $this->log['enable_bot_error_log'] && $this->log['enable_not_found_log'] )
					{
						$errorMessage = 'Request data couldn\'t be found' . ( $pDeleted ? ' [Deleted]' : '' ) . ' (Price id: "' . $pid . '" - URL: ' . $pt['url'] . ')';
						
						if ( !empty( $botError[$pid]['keys'] ) )
						{
							$errorMessage .= PHP_EOL . 'Request data couldn\'t be parsed for key(s): ' . implode( ', ', $botError[$pr['id_price']]['keys'] );
						}

						Log::Set( $errorMessage, null, $pt, 'system', 0, $this->siteId );
					}
				}

				else
				{
					//If the "current price" is empty, then the "price old" is the current sales price
					$old 	= ( !empty( $pt['data']['price-old'] ) 		? $pt['data']['price-old'] 						: 0 );
					$curr 	= ( !empty( $pt['data']['current-price'] ) 	? $pt['data']['current-price'] 					: 0 );
					$title 	= ( !empty( $pt['data']['title'] ) 			? $pt['data']['title'] 		: $pt['title'] );
					$stock 	= ( !empty( $pt['data']['in-stock'] ) 		? 1 						: (int) $pt['in_stock'] );
					$start 	= ( !empty( $pt['data']['discount-start'] ) ? strtotime( $pt['data']['discount-start'] ) 	: 0 );
					$end 	= ( !empty( $pt['data']['discount-end'] ) 	? strtotime( $pt['data']['discount-end'] ) 		: 0 );
					$dbCurr	= ( !empty( $pt['sale_price'] ) 			? $pt['sale_price'] 							: 0 );
					$dbOld 	= ( !empty( $pt['or_price'] ) 				? $pt['or_price'] 								: 0 );
					$pr2see	= ( empty( $old ) ? $curr : $old );
					
					//If no price found, set the log data
					if ( empty( $pr2see ) )
					{
						$retries = ( $pt['retries'] + 1 );
						
						//Set the log for empty price value
						if ( !empty( $this->log ) && $this->log['enable_bot_error_log'] && $this->log['enable_not_found_log'] )
						{
							$errorMessage = 'Price value couldn\'t be found. (Price id: "' . $pid . '" - URL: ' . $pt['url'] . ')';

							Log::Set( $errorMessage, null, $pt, 'system', 0, $this->siteId );
						}
						
						$dbarr = array(
							"last_time_checked"	=> time(),
							"num_retries" 		=> $retries,
							"not_found" 		=> $notFound
						);

						$this->db->update( "price_info" )->where( 'id_price', $pid )->set( $dbarr );
					}
					
					else
					{
						//Check if there is a difference with the price in the DB
						if ( abs( $dbCurr - $pr2see ) > 0.00001 )
						{
							$dbarr = array(
								"sale_price"		=> $curr,
								"regular_price" 	=> $old,
								"title" 			=> $title,
								"available_since" 	=> $start,
								"expire_time" 		=> $end
							);

							$this->db->update( "prices" )->where( 'id_price', $pid )->set( $dbarr );
							
							$dbarr = array(
								"id_price"		=> $pid,
								"time_added" 	=> time(),
								"price" 		=> $pr2see,
								"price_before" 	=> $dbCurr
							);

							$this->db->insert( 'price_update_info' )->set( $dbarr );
							
							$dbarr = array(
								"last_time_checked"	=> time(),
								"last_time_updated" => time(),
								"in_stock"		 	=> $stock,
								"num_retries" 		=> 0,
								"not_found" 		=> 0
							);

							$this->db->update( "price_info" )->where( 'id_price', $pid )->set( $dbarr );
						}
					
						//Price is the same, so just update its info
						else
						{
							//Update its info to avoid infinite checking
							$dbarr = array(
								"last_time_checked"	=> time(),
								"num_retries" 		=> 0,
								"not_found" 		=> 0
							);

							$this->db->update( "price_info" )->where( 'id_price', $pid )->set( $dbarr );
						}
					}
				}
			}
		}
	}
	
	#####################################################
	#
	# Runs daily maintenance tasks function
	#
	#####################################################
	private function DailyMaintenance()
	{
		$this->DeleteOldData();
	}
	
	#####################################################
	#
	# Closes the comments function
	#
	#####################################################
	private function ClosePostComments()
	{
		$settings = Json( $this->settings::Get()['comments_data'] );

		if ( !empty( $settings['auto_comments_close'] ) && ( $settings['auto_comments_close'] > 0 ) )
		{
			$days = (int) $settings['auto_comments_close'] * 86400;
		
			$time = ( time() - $days );
			
			$this->db->update( POSTS )->where( 'id_site', $this->siteId )->where( 'added_time', $time, true, '<' )->set( "disable_comments", 1 );
		}
	}
	
	#####################################################
	#
	# Marks Comments, Logs and Posts as read function
	#
	#####################################################
	private function MarkBoardsRead()
	{
		if ( !empty( $this->log['automatically_purge_board_information'] ) && ( $this->log['automatically_purge_board_information'] > 0 ) )
		{
			$errorEntries = (int) $this->log['automatically_purge_board_information'] * 86400;
			
			$time = ( time() - $errorEntries );

			$this->db->delete( 'log_posts' )->where( "id_site", $this->siteId )->where( 'added_time', $time, true, '<' )->run();
			
			$this->db->delete( 'log_boards' )->where( "id_site", $this->siteId )->where( 'added_time', $time, true, '<' )->run();
			
			$this->db->delete( 'log_comments' )->where( "id_site", $this->siteId )->where( 'added_time', $time, true, '<' )->run();
			
			$this->db->delete( 'log_emails' )->where( 'added_time', $time, true, '<' )->run();
		}
		
		//TODO
		//[automatically_mark_boards_read]
		//[maximum_users_to_process]
	}
	
	#####################################################
	#
	# Cleans the logs function
	#
	#####################################################
	private function LogPruning()
	{
		if ( empty( $this->log ) || !isset( $this->log['enable_pruning'] ) || !$this->log['enable_pruning'] )
			return;
		
		if ( !empty( $this->log['remove_error_log_entries'] ) && ( $this->log['remove_error_log_entries'] > 0 ) )
		{
			$errorEntries = (int) $this->log['remove_error_log_entries'] * 86400;
			
			$time = ( time() - $errorEntries );
			
			$this->db->delete( 'logs' )->where( 'added_time', $time, true, '<' )->run();
			
			$this->db->delete( 'log_log' )->where( 'added_time', $time, true, '<' )->run();
			
			$this->db->delete( 'log_mark_read' )->where( 'added_time', $time, true, '<' )->run();
		}
		
		if ( !empty( $this->log['remove_scheduled_task_log_entries'] ) && ( $this->log['remove_scheduled_task_log_entries'] > 0 ) )
		{
			$errorEntries = (int) $this->log['remove_scheduled_task_log_entries'] * 86400;
			
			$time = ( time() - $errorEntries );
			
			$this->db->delete( 'log_scheduled_tasks' )->where( 'added_time', $time, true, '<' )->run();			
		}
		
		if ( !empty( $this->log['remove_redirection_log_entries'] ) && ( $this->log['remove_redirection_log_entries'] > 0 ) )
		{
			$errorEntries = (int) $this->log['remove_redirection_log_entries'] * 86400;
			
			$time = ( time() - $errorEntries );
			
			//$this->db->delete( 'log_scheduled_tasks' )->where( 'added_time', $time, true, '<' )->run();			
		}
		
		if ( !empty( $this->log['remove_moderation_log_entries'] ) && ( $this->log['remove_moderation_log_entries'] > 0 ) )
		{
			$errorEntries = (int) $this->log['remove_moderation_log_entries'] * 86400;
			
			$time = ( time() - $errorEntries );
			
			//$this->db->delete( 'log_scheduled_tasks' )->where( 'added_time', $time, true, '<' )->run();			
		}
		
		if ( !empty( $this->log['remove_ban_hit_log_entries'] ) && ( $this->log['remove_ban_hit_log_entries'] > 0 ) )
		{
			$errorEntries = (int) $this->log['remove_ban_hit_log_entries'] * 86400;
			
			$time = ( time() - $errorEntries );
			
			//$this->db->delete( 'log_scheduled_tasks' )->where( 'added_time', $time, true, '<' )->run();
		}
	}
	
	#####################################################
	#
	# Cleans the DB from old/unused data function
	#
	#####################################################
	private function DeleteOldData()
	{
		if ( empty( $this->log ) )
			return;
		
		//Published Posts
		if ( !empty( $this->log['delete_published_posts'] ) && ( $this->log['delete_published_posts'] > 0 ) )
		{
			$errorEntries = (int) $this->log['delete_published_posts'] * 86400;
			$time = ( time() - $errorEntries );
			
			$this->db->delete( POSTS )->where( "id_site", $this->siteId )->where( "post_status", 'published' )->where( 'added_time', $time, true, '<' )->run();
		}
		
		//Draft posts
		if ( !empty( $this->log['delete_draft_posts'] ) && ( $this->log['delete_draft_posts'] > 0 ) )
		{
			$errorEntries = (int) $this->log['delete_draft_posts'] * 86400;
			$time = ( time() - $errorEntries );
			
			$this->db->delete( POSTS )->where( "id_site", $this->siteId )->where( "post_status", 'draft' )->where( 'added_time', $time, true, '<' )->run();
		}
		
		//Auto drafts
		if ( !empty( $this->log['delete_auto_draft_posts'] ) && ( $this->log['delete_auto_draft_posts'] > 0 ) )
		{
			$errorEntries = (int) $this->log['delete_auto_draft_posts'] * 86400;
			$time = ( time() - $errorEntries );
			
			$this->db->delete( "posts_autosaves" )->where( "id_site", $this->siteId )->where( 'added_time', $time, true, '<' )->run();
		}
		
		//Published Comments
		if ( !empty( $this->log['delete_published_comments'] ) && ( $this->log['delete_published_comments'] > 0 ) )
		{
			$errorEntries = (int) $this->log['delete_published_comments'] * 86400;
			$time = ( time() - $errorEntries );
			
			$this->db->delete( 'comments' )->where( "id_site", $this->siteId )->where( "status", 'approved' )->where( 'added_time', $time, true, '<' )->run();
		}
		
		//Other Comments
		if ( !empty( $this->log['delete_other_comments'] ) && ( $this->log['delete_other_comments'] > 0 ) )
		{
			$errorEntries = (int) $this->log['delete_other_comments'] * 86400;
			$time = ( time() - $errorEntries );
			
			$this->db->delete( 'comments' )->where( "id_site", $this->siteId )->where( "status", 'pending' )->where( 'added_time', $time, true, '<' )->run();
			
			$this->db->delete( 'comments' )->where( "id_site", $this->siteId )->where( "status", 'spam' )->where( 'added_time', $time, true, '<' )->run();
			
			$this->db->delete( 'comments' )->where( "id_site", $this->siteId )->where( "status", 'deleted' )->where( 'added_time', $time, true, '<' )->run();
		}
		
		//Inbox Emails
		if ( !empty( $this->log['delete_inbox_emails'] ) && ( $this->log['delete_inbox_emails'] > 0 ) )
		{
			$errorEntries = (int) $this->log['delete_inbox_emails'] * 86400;
			$time = ( time() - $errorEntries );
			
			$this->db->delete( 'mails' )->where( "id_site", $this->siteId )->where( "status", 'inbox' )->where( 'added_time', $time, true, '<' )->run();
		}
		
		//Other Emails
		if ( !empty( $this->log['delete_other_emails'] ) && ( $this->log['delete_other_emails'] > 0 ) )
		{
			$errorEntries = (int) $this->log['delete_other_emails'] * 86400;
			$time = ( time() - $errorEntries );
			
			$this->db->delete( 'mails' )->where( "id_site", $this->siteId )->where( "status", 'sent' )->where( 'added_time', $time, true, '<' )->run();
			
			$this->db->delete( 'mails' )->where( "id_site", $this->siteId )->where( "status", 'draft' )->where( 'added_time', $time, true, '<' )->run();
			
			$this->db->delete( 'mails' )->where( "id_site", $this->siteId )->where( "status", 'junk' )->where( 'added_time', $time, true, '<' )->run();
			
			$this->db->delete( 'mails' )->where( "id_site", $this->siteId )->where( "status", 'deleted' )->where( 'added_time', $time, true, '<' )->run();
		}
	}

	#####################################################
	#
	# Set an image as cover function
	#
	#####################################################
	private function AddImageAsCover()
	{
		if ( empty( $this->coverImg ) )
		{
			return;
		}
		
		$this->img->imgFile 	= $this->coverImg;
		$this->img->GetImage( true );
	}

	#####################################################
	#
	# Replace any images found in content function
	#
	#####################################################
	private function ReplaceContentImages( $content, $imgs, $postId, $time = null )
	{
		if ( !empty( $imgs['1'] ) )
		{
			foreach( $imgs['1'] as $key => $img )
			{
				$this->img->imgFile = $img;
				
				$imageId = $this->img->GetImage();

				if ( !empty( $imageId ) && is_numeric( $imageId ) )
				{
					$imData = $this->db->from( 
					null, 
					"SELECT width
					FROM `" . DB_PREFIX . "images`
					WHERE (id_image = " . $imageId . ")"
					)->single();
					
					if ( $imData )
					{
						$newImg = '[image id="' . $imageId . '" width="' . $imData['width'] . '" align="center"]';

						$content = str_replace( $imgs['0'][$key], $newImg, $content );
					}
				}
			}
		}

		return $content;
	}

	#####################################################
	#
	# Search for images in content function
	#
	#####################################################
	private function SearchContentImages( $content, $firstAsCover = false )
	{
		preg_match_all('/<figure.+>?<img.+src=[\'"]([^\'"]+)[\'"].*><\/figure>?/i', $content, $matches);

		if ( empty( $matches ) || !isset( $matches['1'] ) || empty( $matches['1'] ) )
			return null;
		
		if ( $firstAsCover && empty( $this->coverImg ) )
		{
			$this->coverImg = trim( $matches['1']['0'] );
		}
		
		return $matches;
	}
	
	#####################################################
	#
	# Search for a word function
	#
	#####################################################
	private function Search( $string, $array = null, $text, $whatToReturn = true )
	{
		$return = null;
		
		if ( !empty( $string ) || !empty( $array ) )
		{
			if ( !empty( $string ) && str_contains( $text, $string ) )
			{
				$return = $whatToReturn;
			}

			else
			{
				if ( is_array( $array ) )
				{
					foreach( $array as $word )
					{
						if ( empty( $word ) )
						{
							continue;
						}

						if ( str_contains( $text, strtolower( $word ) ) )
						{
							$return = $whatToReturn;
							break;
						}
					}
				}

				else
				{
					if ( str_contains( $text, strtolower( $array ) ) )
					{
						$return = $whatToReturn;
					}
				}
			}
		}
		
		return $return;
	}
	
	#####################################################
	#
	# Tests all internal links & external links function
	#
	#####################################################
	private function BrokenLinkCheck()
	{
		$settings = Json( $this->settings::Get()['link_checker_options'] );
		
		//We need these values
		if ( empty( $settings ) || empty( $settings['content_types'] ) || empty( $settings['content_status'] ) || empty( $settings['link_types'] ) )
		{
			return;
		}

		$hasComments = in_array( 'comments', $settings['content_types'] );
		$hasPosts 	 = ( in_array( 'posts', $settings['content_types'] ) || in_array( 'pages', $settings['content_types'] ) );
		
		$isPost = $isComment = false;
		$postId = $commentId = 0;
		
		if ( $hasComments )
		{
			$q = $this->db->from( null, 
			"SELECT id, comment, name
			FROM `" . DB_PREFIX . "comments`
			WHERE (id_site = " . $this->siteId . ") AND (last_checked = 0)
			ORDER BY id ASC
			LIMIT 1"
			)->single();
				
			if ( $q )
			{
				$isComment = true;
				$commentId = $q['id'];
				
				$links = $this->CheckLinks( $q['comment'] );

				$this->UpdateLinksInDb( $links, 0, $commentId, 0, $q['name'] . ' ' . __( 'comment' ) );
			}
		}

		if ( $hasPosts )
		{
			$types = '(';

			if ( in_array( 'posts', $settings['content_types'] ) && !in_array( 'pages', $settings['content_types'] ) )
				$types .= "p.post_type = 'post'";
				
			elseif ( in_array( 'pages', $settings['content_types'] ) && !in_array( 'posts', $settings['content_types'] ) )
				$types .= "p.post_type = 'page'";
					
			elseif ( in_array( 'pages', $settings['content_types'] ) && in_array( 'posts', $settings['content_types'] ) )
				$types .= "p.post_type = 'page' OR p.post_type = 'post'";
			
			$types .= ')';
			
			$p = $d = $s = false;
			
			$status = '(';

			if ( in_array( 'published', $settings['content_status'] ) )
			{
				$p = true;
				$status .= "p.post_status = 'published'";
			}
			
			if ( in_array( 'draft', $settings['content_status'] ) )
			{
				$d = true;
				$status .= ( $p ? " OR " : "" ) . "p.post_status = 'draft'";
			}
			
			if ( in_array( 'scheduled', $settings['content_status'] ) )
			{
				$s = true;
				$status .= ( ( $p || $d ) ? " OR " : "" ) . "p.post_status = 'scheduled'";
			}
			
			if ( in_array( 'pending', $settings['content_status'] ) )
			{
				$status .= ( ( $p || $d || $s ) ? " OR " : "" ) . "p.post_status = 'pending'";
			}
			
			$status .= ')';
			
			$q = $this->db->from( null, 
			"SELECT p.id_post, d.id as data_id
			FROM `" . DB_PREFIX . POSTS . "`
			LEFT JOIN `" . DB_PREFIX . "posts_data` AS d ON d.id_post = p.id_post
			WHERE (p.id_site = " . $this->siteId . ") AND " . $types . " AND " . $status . " AND (d.last_time_checked = 0)
			ORDER BY p.id_post ASC
			LIMIT 1"
			)->single();
			
			if ( $q )
			{
				$post = GetSinglePost( $q['id_post'], null, false );
				
				if ( $post )
				{
					$isPost = true;
					
					$postId = $q['id_post'];
					
					$links = $this->CheckLinks( $post->PostRaw() );
		
					$this->UpdateLinksInDb( $links, $postId, 0, $q['data_id'] );
				}
			}
		}
	}
	
	#####################################################
	#
	# Updates the links in DB function
	#
	#####################################################
	private function UpdateLinksInDb( $links, $postId, $commentId, $data_id, $title = null )
	{
		$settings = Json( $this->settings::Get()['link_checker_options'] );
		
		if ( !empty( $links ) )
		{
			foreach( $links as $link )
			{
				$url = $link['url'];
				
				$title = ( $title ? $title : $link['title'] );
				
				if ( !empty( $link['type'] ) && ( $link['type'] == 'youtube' ) && !empty( $link['id'] ) )
				{
					$url = 'https://www.youtube.com/watch?v=' . $link['id'];
				}
				
				$code = PingSite( $url, false, false, true );
				
				$resCode = ResponseCode( $code['code'] );
				
				$headers = '';
				
				if ( !empty( $code['headers'] ) )
				{
					foreach( $code['headers'] as $id => $header )
					{
						$headers .= $id . ': ' . $header['0'] . PHP_EOL;
					}
				}
				
				$ps = $this->db->from( null, 
				"SELECT id_check
				FROM `" . DB_PREFIX . "link_checks`
				WHERE (id_post = " . $postId . ") AND (id_comment = " . $commentId . ") AND (url = :url)",
				array( $url => ':url' )
				)->single();
		
				if ( $ps )
				{
					$dbarr = array(
						"last_checked" 		=> time(),
						"times_checked" 	=> "times_checked + 1",
						"url_status" 		=> $resCode,
						"url_response_code" => $code['code'],
						"response_headers" 	=> $headers
					);

					$this->db->update( 'link_checks' )->where( 'id', $ps['id_check'] )->set( $dbarr );
				}

				else
				{
					$dbarr = array(
						"id_post" 				=> $postId,
						"id_comment" 			=> $commentId,
						"id_site" 				=> $this->siteId,
						"url" 					=> $url,
						"url_status" 			=> $resCode,
						"url_response_code" 	=> $code['code'],
						"link_text" 			=> $title,
						"added_time" 			=> time(),
						"last_checked" 			=> time(),
						"times_checked" 		=> 1,
						"response_headers" 		=> $headers
					);
						
					$this->db->insert( 'link_checks' )->set( $dbarr );
				}
			}
		}
			
		if ( $postId > 0 )
		{
			$this->db->update( "posts_data" )->where( 'id', $data_id )->set( "last_time_checked", time() );

			//Update the edited time in post
			if ( isset( $settings['post_modified_date'] ) && $settings['post_modified_date'] )
			{
				$this->db->update( POSTS )->where( 'id_post', $postId )->set( "edited_time", time() );
			}
		}
			
		if ( $commentId > 0 )
		{
			$this->db->update( "comments" )->where( 'id', $commentId )->set( "last_checked", time() );
		}
	}
	
	#####################################################
	#
	# Checks the links function
	#
	#####################################################
	private function CheckLinks( $post )
	{
		$settings = Json( $this->settings::Get()['link_checker_options'] );
		
		//Convert Markdown to html and strip any slashes
		$post = Parsedown( StripContent( $post ) );
		
		$links = array();
		
		if ( in_array( 'html-links', $settings['link_types'] ) )
		{
			preg_match_all( '/<a.*href=[\'"]([^\'"]+)[\'"].*>([^<]*)<\/a>/iU', $post, $out );
			
			if ( !empty( $out ) )
			{
				foreach ( $out['0'] as $i => $l )
				{
					$links[] = array(
							'url' 	=> $out['1'][$i],
							'title' => $out['2'][$i],
							'type' => null
					);
				}
			}
		}
		
		if ( in_array( 'plaintext-urls', $settings['link_types'] ) )
		{
			$rexProtocol = '(http|https)://';
			$rexDomain   = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
			$rexPort     = '(:[0-9]{1,5})?';
			$rexPath     = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
			$rexQuery    = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
			$rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
			
			preg_match_all( "&\\b$rexProtocol$rexDomain$rexPort$rexPath$rexQuery$rexFragment(?=[?.!,;:\"]?(\s|$))&", $post, $out );
			
			$yt = in_array( 'embedded-youtube-videos', $settings['link_types'] );
			
			if ( !empty( $out ) )
			{
				foreach ( $out['0'] as $i => $l )
				{
					$isYt = $vars = null;
					
					$l = (string) $out['0'][$i];

					if ( !$yt && ( strpos( $l, 'youtube' ) !== false ) )
					{
						continue;
					}
					else
					{
						preg_match( '/embed\/([\w+\-+]+)[\"\?]/iU', $l, $v );
						$vars = ( $v ? $v['1'] : '' );
						$isYt = true;
					}
					
					$links[] = array(
						'url' 	=> $l,
						'title' => ( $isYt ? __( 'youtube-link' ) : __( 'plaintext-url' ) ),
						'id' 	=> $vars,
						'type' 	=> ( $isYt ? 'youtube' : null )
					);
				}
			}
		}
		
		if ( in_array( 'html-images', $settings['link_types'] ) )
		{
			preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/iU', $post, $out );
			
			if ( !empty( $out ) )
			{
				foreach ( $out['0'] as $i => $l )
				{
					$l = (string) $out['1'][$i];
					
					$links[] = array(
						'url' 	=> $l,
						'title' => __( 'image-file' ),
						'type' => 'image'
					);
				}
			}
		}
	}
	
	#####################################################
	#
	# Loads the Settings function
	#
	#####################################################
	private function LoadSettings()
	{
		$this->settings = new Settings( $this->siteId, false );
	}

	//Return the response
	private function Response( $code = 200, $message = 'OK', $data = array() )
	{
		header( 'HTTP/1.1 ' . $code . ' ' . $message );
		header( 'Access-Control-Allow-Origin: *' );//TODO
		header( 'Access-Control-Allow-Methods: GET ');
		header( 'Content-Type: application/json' );
		
		if ( !empty( $this->timeExec ) )
		{
			$data['time_to_execute'] = ceil( $this->timeExec ) . ' ' . __( 'seconds' );
		}
		
		echo json_encode( $data );
		
		exit;
	}
}