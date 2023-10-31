<?php defined('TOKICMS') or die('Hacking attempt...');

class AdminPosts extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-own-posts' ) && !IsAllowedTo( 'manage-posts' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
		{
			$type   		= ( ( $Admin->CurrentAction() === 'pages' ) ? 'page' : 'post' );
			$numItems 		= 20;
			$fileDb 		= ( ( $type === 'page' ) ? EXTERNAL_PAGES_FILE : EXTERNAL_POSTS_FILE );
			$isPost 		= ( ( $Admin->CurrentAction() === 'pages' ) ? false : true );
			$counts 		= array();
			$status 		= Router::GetVariable( 'subAction' );
			$countSyncs 	= $totalRecord = 0;
			$orderBy 		= ( Router::GetVariable( 'orderBy' ) ? Router::GetVariable( 'orderBy' ) : 'date' );
			$order 	 		= ( ( Router::GetVariable( 'order' ) && ( Router::GetVariable( 'order' ) == 'asc' ) ) ? 'asc' : 'desc' );
			$nextOrder 		= ( ( $order == 'asc' ) ? 'desc' : 'asc' );
			$arrow 	 		= ( ( $nextOrder == 'desc' ) ? 'down' : 'up' );
			$search			= Sanitize( $Admin->GetSearchString(), false );
			$catId			= $Admin->GetCatId();
			$currentUrl 	= $Admin->CurrentAction() . PS . ( Router::GetVariable( 'subAction' ) ? Router::GetVariable( 'subAction' ) . PS : '' ) . ( $orderBy ? 'sort' . PS . $orderBy . PS . Router::GetVariable( 'order' )  . PS : '' );
			$showAll 		= $Admin->Settings()::IsTrue( 'parent_site_shows_everything' );
			$showAllSites 	= ( ( $showAll && MULTISITE && $Admin->IsDefaultSite() ) ? true : false );
			$isSelfHosted 	= $Admin->SiteIsSelfHosted();
			$langs 			= $Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) ? Langs( $Admin->GetSite(), false ) : array();
			$isOnSync 		= false;
			$data 	  		= array();
			$dbOrder  		= '';
			
			if ( $orderBy == 'date' )
			{
				$dbOrder = 'p.added_time';
			}
			
			elseif ( $orderBy == 'title' )
			{
				$dbOrder = 'p.title';
			}
				
			elseif ( $orderBy == 'category' )
			{
				$dbOrder = 'cat_name';
			}
				
			elseif ( $orderBy == 'blog' )
			{
				$dbOrder = 'blog_name';
			}
				
			elseif ( $orderBy == 'site' )
			{
				$dbOrder = 'st';
			}
			
			else
			{
				$dbOrder = 'p.added_time';
			}
			
			$nav  = ( ( Router::GetVariable( 'pageNum' ) > 0 ) ? Router::GetVariable( 'pageNum' ) : 1 );
			
			$from = ( ( $nav * $numItems ) - $numItems );
			
			if ( !$isSelfHosted )
			{
				if ( Router::GetVariable( 'subAction' ) == 'sync' )
				{
					$data 		= GetAdminCachedPosts( $fileDb, $Admin->GetSite() );
					$isOnSync   = ( ( Router::GetVariable( 'subAction' ) == 'sync' ) ? true : false );
					$countSyncs = $data['totalItems'];
				}
				else
				{
					$data 		= GetAdminCachedPosts( $fileDb, $Admin->GetSite() );
					$countSyncs = $temp['totalItems'];
					unset( $temp );
				}
			}
			
			$qWhere = $qCount = ( $showAllSites ? "" : " (p.id_site = " . $Admin->GetSite() . ") AND" ) . ( $showAll ? "" : " (p.id_lang = " . $Admin->GetLang() . ") AND (p.id_blog = " . $Admin->GetBlog() . ") AND" ) .
			( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) ) ? 
			" (p.id_member = " . $Admin->UserID() . ") AND" : "" ) . " (p.post_type = '" . $type . "')"
			. ( !empty( $search ) ? " AND (p.title LIKE '%" . $search . "%' OR p.post LIKE '%" . $search . "%')" : '' )
			. ( !empty( $catId ) ? " AND (p.id_category = " . $catId . ")" : '' );
			
			$qWhere .= " AND " . ( !empty( $status ) ? "(p.post_status = '" . $status . "')" : "(p.post_status = 'published' OR p.post_status = 'draft')" );
			
			if ( $isSelfHosted || ( !$isSelfHosted && ( Router::GetVariable( 'subAction' ) != 'sync' ) ) )
			{
				// Query: total
				$counts['postsAll'] = $this->db->from( null, "
				SELECT 
				count(id_post) as total 
				FROM `" . DB_PREFIX . POSTS . "` as p WHERE 1=1 AND" . $qCount . " AND (p.post_status = 'published' OR p.post_status = 'draft')" )->total();

				// Query: deleted
				$counts['postsDeleted'] = $this->db->from( null, "
				SELECT 
				count(id_post) as total 
				FROM `" . DB_PREFIX . POSTS . "` as p WHERE 1=1 AND" . $qCount . " AND p.post_status = 'deleted'" )->total();
				
				// Query: pending
				$counts['postsPending'] = $this->db->from( null,"
				SELECT 
				count(id_post) as total 
				FROM `" . DB_PREFIX . POSTS . "` as p WHERE 1=1 AND" . $qCount . " AND p.post_status = 'pending'" )->total();
				
				// Query: scheduled
				$counts['postsScheduled'] = $this->db->from( null, "
				SELECT 
				count(id_post) as total 
				FROM `" . DB_PREFIX . POSTS . "` as p WHERE 1=1 AND" . $qCount . " AND p.post_status = 'scheduled'" )->total();
				
				// Query: draft
				$counts['postsDraft'] = $this->db->from( null, "
				SELECT 
				count(id_post) as total 
				FROM `" . DB_PREFIX . POSTS . "` as p WHERE 1=1 AND" . $qCount . " AND p.post_status = 'draft'" )->total();
				
				// Query: published
				$counts['postsPublished'] = $this->db->from( null, "
				SELECT 
				count(id_post) as total 
				FROM `" . DB_PREFIX . POSTS . "` as p WHERE 1=1 AND" . $qCount . " AND p.post_status = 'published'" )->total();
		
				$query = PostsDefaultQuery( $qWhere, $from . ", " . $numItems, $dbOrder . " " . strtoupper( $order ), null, false );

				$tmp = $this->db->from( null, $query )->all();
				
				$s = GetSettingsData( $Admin->GetSite() );
				
				if( !empty( $tmp ) && !empty( $s ) )
				{
					$i = 0;
					
					foreach ( $tmp as $p )
					{
						if ( ( $p['post_type'] == 'page' ) && !empty( $p['id_page_parent'] ) )
						{
							continue;
						}
						
						$p 					= array_merge( $p, $s );
						$data[$i] 			= BuildPostVars( $p );
						$data[$i]['trans'] 	= PostTrans( $p, $data[$i]['postUrl'] );
						
						if ( $p['post_type'] == 'page' )
						{
							$q = "(p.id_page_parent = " . $p['id_post'] . ") AND (p.post_type = 'page') AND (d.external_url = '' OR d.external_url IS NULL)";
			
							$query = PostsDefaultQuery( $q, null, 'p.title, p.page_order ASC' );
			
							//Query: pages
							$tmp = $this->db->from( null, $query )->all();
							
							if ( $tmp )
							{
								foreach( $tmp as $ch )
								{
									$i++;
									
									$ch 				= array_merge( $ch, $s );
									$data[$i] 			= BuildPostVars( $ch );
									$data[$i]['trans'] 	= PostTrans( $ch, $data[$i]['postUrl'] );
								}
							}
						}

						$i++;
					}
				}
			}
		}
		//POST
		else
		{
			if ( !empty( $_POST['search'] ) )
			{
				$url = $Admin->CustomAdminUrl( $Admin->GetSite(), $Admin->GetLang(), $Admin->GetBlog(), $_POST['search'], $_POST['order'], $_POST['sort'] );
				
				Redirect( $url );
			}

			Redirect( $Admin->GetUrl( 'posts' ) );
		}
		
		if ( !Router::GetVariable( 'subAction' ) )
		{
			$totalItems = $counts['postsAll'];
			$subAction	= null;
		}
		else
		{
			$subAction	= __( Router::GetVariable( 'subAction' ) );
			
			switch ( Router::GetVariable( 'subAction' ) )
			{
				case 'published':
					$totalItems = $counts['postsPublished'];
					break;
				case 'draft':
					$totalItems = $counts['postsDraft'];
					break;
				case 'deleted':
					$totalItems = $counts['postsDeleted'];
					break;
				case 'pending':
					$totalItems = $counts['postsPending'];
					break;
				case 'scheduled':
					$totalItems = $counts['postsScheduled'];
					break;
				default:
					$totalItems = 0;
			}
		}
		
		$this->setVariable( "data", $data );
		$this->setVariable( "langs", $langs );
		$this->setVariable( "type", $type );
		$this->setVariable( "fileDb", $fileDb );
		$this->setVariable( "isPost", $isPost );
		$this->setVariable( "counts", $counts );
		$this->setVariable( "countSyncs", $countSyncs );
		$this->setVariable( "orderBy", $orderBy );
		$this->setVariable( "order", $nextOrder );
		$this->setVariable( "arrow", $arrow );
		$this->setVariable( "currentUrl", $currentUrl );
		$this->setVariable( "showAll", $showAll );
		$this->setVariable( "showAllSites", $showAllSites );
		$this->setVariable( "isSelfHosted", $isSelfHosted );
		$this->setVariable( "isOnSync", $isOnSync );
		$this->setVariable( 'isSearch', 	(!empty( $search ) ? true : false ) );
		$this->setVariable( "search", $search );
		
		Theme::SetVariable( 'headerTitle', __( $Admin->CurrentAction() ) . ( $subAction ? ' - ' . $subAction : '' ) . ( ( Router::GetVariable( 'pageNum' ) > 0 ) ? ' - ' . __( 'page' ) . ': ' . Router::GetVariable( 'pageNum' ) : '' ) . ' | ' . $Admin->SiteName() );
		
		//Set up the paginator
		Paginator::SetVariable( 'currentPage', $nav );
		Paginator::SetVariable( 'maxItemsPerPage', $numItems );
		Paginator::SetVariable( 'totalItems', $totalItems );
		Paginator::Run();
	}
}