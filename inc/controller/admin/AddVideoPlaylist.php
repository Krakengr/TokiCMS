<?php defined('TOKICMS') or die('Hacking attempt...');

class AddVideoPlaylist extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $Query;

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-video-content' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		if ( !verify_token( 'add-playlist' ) )
			return;
		
		$catId  = (int) $_POST['category'];
	
		$query = array(
			'SELECT'	=>  "id_lang, id_site, id_blog, id_parent",
			'FROM'		=> DB_PREFIX . "categories",
			'WHERE'		=> "id = :id",
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
			'BINDS' 	=> array(
				array( 'PARAM' => ':id', 'VAR' => $catId, 'FLAG' => 'INT' )
			)
		);

		$Cat = Query( $query );

		$site   = $Admin->GetSite();
		$lang   = $Admin->GetLang();
		$blog   = $Admin->GetBlog();
		$subCat = 0;
		
		if ( !empty( $Cat['id_parent'] ) )
		{
			$subCat = $catId;
			$catId  = $Cat['id_parent'];
		}
		
		$listId = null;
		
		if ( !empty( $_POST['url'] ) )
		{
			preg_match('%(?:youtube(?:-nocookie)?\.com/.*[?&]list=)([^"&?/\s](.*))%i', $_POST['url'], $match );
			
			if ( !empty( $match ) )
			{
				$listId = $match['1'];
			}
			
			else
			{
				parse_str( parse_url( $_POST['url'], PHP_URL_QUERY ), $vars );
				
				if ( !empty( $vars ) && isset( $vars['list'] ) )
				{
					$listId = $vars['list'];
				}
			}
		}
		
		if ( !empty( $listId ) )
		{
			//Make sure that we don't have this name in the DB
			$query = array(
				'SELECT'	=>  "id",
				'FROM'		=> DB_PREFIX . "playlists",
				'PARAMS' => array( 'NO_PREFIX' => true ),
				'WHERE' => "source_play_id = :id",
				'BINDS' 	=> array(
					array( 'PARAM' => ':id', 'VAR' => $listId, 'FLAG' => 'INT' )
				)
			);
			
			// Get the data
			$q = Query( $query );
			
			if ( $q )
			{
				$Admin->SetAdminMessage( __( 'playlist-same-url-found-in-the-db' ) );
				return;
			}
		}
	
		$query = array(
				'INSERT'	=> "source_play_id, source_play_url, title, descr, added_time, id_site",
				'VALUES' => ":id, :url, :title, :descr, :time, :site",
				'INTO'		=> DB_PREFIX . "playlists",
				'PARAMS' => array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
						array( 'PARAM' => ':id', 'VAR' => $listId, 'FLAG' => 'STR' ),
						array( 'PARAM' => ':url', 'VAR' => $_POST['url'], 'FLAG' => 'STR' ),
						array( 'PARAM' => ':title', 'VAR' => $_POST['title'], 'FLAG' => 'STR' ),
						array( 'PARAM' => ':descr', 'VAR' => $_POST['descr'], 'FLAG' => 'STR' ),
						array( 'PARAM' => ':time', 'VAR' => time(), 'FLAG' => 'INT' ),
						array( 'PARAM' => ':site', 'VAR' => $Admin->GetSite(), 'FLAG' => 'INT' )
				)
		);
		
		$data = Query( $query, false, false, false, false, true );

		if ( $data )
		{
			if ( isset( $_POST['grab_videos'] ) && !empty( $listId ) )
			{
				$keys = Json( $Admin->Settings()::Get()['api_keys'] );
				
				$apiKey = ( isset( $keys['youtube'] ) ? $keys['youtube'] : null );
				
				$feed = array();
				
				$i = 0;

				if ( !empty( $apiKey ) )
				{
					$vidsUri = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet%2CcontentDetails&maxResults=99&playlistId=' . $listId . '&key=' . trim( $apiKey );
					
					$vidz = PingSite( $vidsUri );
					
					if ( !empty( $vidz ) && !empty( $vidz['items'] ) )
					{
						foreach( $vidz['items'] as $item )
						{
							$feed[$i] = array(
									'dateUnix' 	=> strtotime( $item['contentDetails']['videoPublishedAt'] ),
									'date' 		=> $item['contentDetails']['videoPublishedAt'],
									'vidId' 	=> $item['contentDetails']['videoId'],
									'listId' 	=> $listId,
									'title' 	=> $item['snippet']['title'],
									'desc' 		=> $item['snippet']['description']
							);
							
							if ( isset( $item['snippet']['thumbnails']['maxres'] ) && !empty( $item['snippet']['thumbnails']['maxres'] ) )
							{
								$feed[$i]['img_url'] = $item['snippet']['thumbnails']['maxres']['url'];
								$feed[$i]['width'] = $item['snippet']['thumbnails']['maxres']['width'];
								$feed[$i]['height'] = $item['snippet']['thumbnails']['maxres']['height'];
							}
							
							elseif ( isset( $item['snippet']['thumbnails']['high'] ) && !empty( $item['snippet']['thumbnails']['high'] ) )
							{
								$feed[$i]['img_url'] = $item['snippet']['thumbnails']['high']['url'];
								$feed[$i]['width'] = $item['snippet']['thumbnails']['high']['width'];
								$feed[$i]['height'] = $item['snippet']['thumbnails']['high']['height'];
							}
							
							else
							{
								$feed[$i]['img_url'] = $item['snippet']['thumbnails']['default']['url'];
								$feed[$i]['width'] = $item['snippet']['thumbnails']['default']['width'];
								$feed[$i]['height'] = $item['snippet']['thumbnails']['default']['height'];
							}
							
							$i++;
						}
					}
				}
				
				else
				{
					$vidsUri = 'https://www.youtube.com/feeds/videos.xml?playlist_id=' . $listId;
						
					$vidz = GetFeed( $vidsUri );
						
					if ( !empty( $vidz ) )
					{
						foreach( $vidz as $vid )
						{
							parse_str( parse_url( $vid['url'], PHP_URL_QUERY ), $vars );

							$yt_id = $vars['v'];

							$feed[$i] = array(
									'dateUnix' 	=> $vid['dateUnix'],
									'date' 		=> $vid['date'],
									'vidId' 	=> $yt_id,
									'listId' 	=> $listId,
									'title' 	=> $vid['title'],
									'desc' 		=> $vid['descr']
							);
								
							$i++;
						}
					}
				}
				
				if ( !empty( $feed ) )
				{
					foreach( $feed as $post )
					{
						//Check if this id exists
						$query = array(
							'SELECT'	=>  'id',
							'FROM'		=> DB_PREFIX . "posts_data",
							'WHERE'		=> "uuid = :id",
							'PARAMS' 	=> array( 'NO_PREFIX' => true ),
							'BINDS' 	=> array(
								array( 'PARAM' => ':id', 'VAR' => $post['vidId'], 'FLAG' => 'INT' )
							)
						);

						$ex = Query( $query );
						
						if ( $ex )
						{
							continue;
						}
	
						$args = array(
								'siteId' 		=> $site,
								'langId' 		=> $lang,
								'blogId' 		=> $blog,
								
								//Generate a random date, to avoid posts being flagged by engines as bot generated
								'postDate' 		=> ( ( isset( $_POST['set_date_from_video'] ) && $_POST['set_date_from_video'] ) ? $post['dateUnix'] : ( time() - rand( 100, 99999 ) ) ),
								
								'userId' 		=> $Admin->UserID(),
								'postStatus' 	=> 'published',
								'postType' 		=> 'post',
								'tags' 			=> null,
								'title' 		=> $post['title'],
								'uuid' 			=> $post['vidId'],
								'playlistId' 	=> $data,
								'description' 	=> generateDescr ( $post['desc'] ),
								'content' 		=> $post['desc'],
								'image' 		=> $post['img_url'],
								'categoryId' 	=> $catId,
								'subCategoryId' => $subCat,
								'slug' 			=> null,
								'videoData'		=> array(

									'id_playlist' 			=> $data,
									'id_source_playlist' 	=> $post['listId'],
									'video_url' 			=> 'https://www.youtube.com/watch?v=' . $post['vidId']
								)
						);
						
						$Query->AddPost( $args );
					}
				}
				
				if ( $Admin->IsDefaultSite() )
				{
					BuildSitemap();
				}
				else
				{
					$Admin->PingChildSite( 'build-sitemap', null, null, $Admin->GetSite() );
				}
				
				//Delete any home data for this site
				$Admin->EmptyCaches( $this->postSiteId );
			}
			
			Redirect( $Admin->GetUrl( 'edit-playlist' . PS . 'id' . PS . $data ) );
		}
		
		else
		{
			//Redirect( $Admin->GetUrl( 'video-playlists' ) );
			$Admin->SetAdminMessage( __( 'an-error-happened' ) );
			return;
		}
	}
}