<?php defined('TOKICMS') or die('Hacking attempt...');

class Comments extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-comments' ) && !IsAllowedTo( 'manage-own-comments' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'comments' ) . ' | ' . $Admin->SiteName() );
		
		$showAll = ( $Admin->IsDefaultSite() ? $Admin->Settings()::IsTrue( 'parent_site_shows_everything' ) : false );
		
		$status = Router::GetVariable( 'subAction' );
		
		$binds = null;
		
		if ( !empty( $status ) )
		{
			$binds = array( $status => ':status' );
		}
		
		$p = $this->db->from( null, "
		SELECT co.id, co.name, co.url AS cu, co.email, co.id_site AS cosite, co.id_blog, co.comment, co.added_time, co.status, 
		co.id_parent, co.ip, p.sef AS sef, p.id_post, p.title AS tl, b.sef AS blog_sef, b.name AS blog_name, COALESCE(u.real_name, u.user_name) AS user_name, u.image_data, la.code AS ls, la.title AS lt, la.locale AS ll, la.flagicon, s.url, s.title AS sna
		FROM `" . DB_PREFIX . "comments` AS co
		INNER JOIN `" . DB_PREFIX . POSTS . "` AS p ON p.id_post = co.id_post
		INNER JOIN `" . DB_PREFIX . "languages` AS la ON la.id = co.id_lang
		INNER JOIN `" . DB_PREFIX . "sites` AS s ON s.id = co.id_site
		LEFT  JOIN `" . DB_PREFIX . "blogs` AS b ON b.id_blog = co.id_blog
		LEFT  JOIN `" . DB_PREFIX . USERS . "` AS u ON u.id_member = co.user_id
		WHERE " . ( !empty( $status ) ? "(co.status = :status)" : "(co.status = 'approved')" ) . " AND (co.id_lang = " . $Admin->GetLang() . ")" . ( !$showAll ? " AND (co.id_site = " . $Admin->GetSite() . ") AND (co.id_blog = " . $Admin->GetBlog() . ")" : '' ) . ( ( ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-comments' ) ) && IsAllowedTo( 'manage-own-comments' ) ) ? " AND (co.user_id = " . $Admin->UserID() . ")" : "" ) . "
		ORDER BY co.added_time DESC",
		$binds
		)->all();
		
		$data = array();

		if ( $p )
		{
			foreach( $p AS $c )
			{
				$image = 'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=';
				
				$imageData = array();
				
				$url = '#';
			
				if ( !empty( $c['image_data'] ) )
				{
					$imageData = Json( $c['image_data'] );
					
					if ( !empty( $imageData ) && isset( $imageData['default'] ) )
					{
						$image = $imageData['default']['imageUrl'];
					}
				}
				
				$tmp = GetSinglePost( $c['id_post'], null, false, false, false, false );
				
				if ( $tmp )
				{
					$url = $tmp['postUrl'] . '#comment-' . $c['id'];
				}
				
				$data[] = array(
					'id' 		=> $c['id'],
					'status' 	=> $c['status'],
					'parentId' 	=> $c['id_parent'],
					'ip' 		=> $c['ip'],
					'imageData' => $imageData,
					'imageUrl' 	=> $image,
					'url'		=> $c['cu'],
					'email'		=> $c['email'],
					'postTitle'	=> $c['tl'],
					'postUrl'	=> $url,
					'postId'	=> $c['id_post'],
					'time'		=> postDate( $c['added_time'], false ),
					'niceTime'	=> niceTime( $c['added_time'] ),
					'timeRaw'	=> $c['added_time'],
					'name'		=> ( !empty( $c['user_name'] ) ? $c['user_name'] : $c['name'] ),
					'rTime'		=> date( 'r', $c['added_time'] ),
					'timeC'		=> postDate( $c['added_time'], true ),
					'comment'	=> $c['comment']
				);
			}
		}

		$this->setVariable( 'Comments', $data );
		$this->setVariable( 'ShowAll', $showAll );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
	}
}