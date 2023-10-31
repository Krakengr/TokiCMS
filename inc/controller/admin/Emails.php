<?php defined('TOKICMS') or die('Hacking attempt...');

class Emails extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'view-emails' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$counts = AdminEmailsCounts();
		$sub 	= Router::GetVariable( 'subAction' );
		$emails	= $this->AdminGetEmails( $sub );
		$where	= ( $sub ? $sub : 'inbox' );
		
		Theme::SetVariable( 'headerTitle', __( 'emails' ) . ' ' . ( !empty( $sub ) ? ' - ' . __( $sub ) : '' ) . ' | ' . $Admin->SiteName() );
		
		$this->setVariable( 'counts', $counts );
		$this->setVariable( 'sub', $sub );
		$this->setVariable( 'emails', $emails );
		$this->setVariable( 'where', $where );
	}
	
	#####################################################
	#
	# Gets the emails
	#
	#####################################################
	private function AdminGetEmails( $status = null, $items = 20 )
	{
		global $Admin;
		
		$status = ( $status ? $status : Router::GetVariable( 'subAction' ) );
		
		$status = ( $status ? $status : 'inbox' );
		
		$siteId = $Admin->GetSite();
		
		$pageNum = Router::GetVariable( 'pageNum' );
		
		$navPage = ( ( is_numeric( $pageNum ) && $pageNum > 0 ) ? $pageNum : 1 );

		$from = ( ( $navPage * $items ) - $items );
		
		$userId = $Admin->UserID();
		
		$drafts = ( ( $status == 'draft' ) ? true : false );
		
		$data = array(
			'totalItems' 	=> 0,
			'currentPage' 	=> $navPage,
			'itemsPerPage' 	=> $items,
			'from' 			=> $from,
			'emails'		=> array()
		);
		
		$query = "
		SELECT e.id, e.added_time, e.name, e.subject, e.email, e.post, e.status,
		(SELECT lo.added_time
		FROM " . DB_PREFIX . "log_emails AS lo
		WHERE lo.id_mail = e.id AND lo.id_member = " . $userId . " AND lo.id_site = " . $Admin->GetSite() . ") AS is_read, 
		(SELECT re.added_time FROM " . DB_PREFIX . "mail_replies AS re WHERE re.id_mail = e.id AND re.id_member = " . $userId . ") AS is_replied
		FROM `" . DB_PREFIX . "mails` AS e
		WHERE (e.id_site = " . $siteId . ") AND (e.status = :status)" . ( $drafts ? " AND (id_member = " . $userId . ")" : "" ) . "
		ORDER BY e.added_time DESC LIMIT " . $from . ", " . $items;

		$binds = array( $status => ':status' );
		
		//Query: mails
		$q = $this->db->from( null, $query, $binds )->all();

		if ( $q )
		{
			//Count all items
			$tmp = $this->db->from( null, 
			"SELECT COUNT(id) as total
			FROM `" . DB_PREFIX . "mails`
			WHERE (id_site = " . $siteId . ") AND (status = :status)",
			$binds
			)->total();
			
			$data['totalItems'] = ( $tmp ? $tmp : 0 );
				
			foreach( $q as $e )
			{
				$data['emails'][] = array(
					'id' 		=> $e['id'],
					'name' 		=> StripContent( $e['name'] ),
					'subject' 	=> StripContent( $e['subject'] ),
					'email' 	=> StripContent( $e['email'] ),
					'post' 		=> StripContent( $e['post'] ),
					'postSum' 	=> generateDescr( $e['post'] ),
					'status' 	=> $e['status'],
					'isRead' 	=> ( !empty( $e['is_read'] ) ? true : false ),
					'isReplied' => ( !empty( $e['is_replied'] ) ? true : false ),
					'timeRaw' 	=> $e['added_time'],
					'time' 		=> postDate( $e['added_time'] ),
					'timeNice' 	=> niceTime( $e['added_time'] ),
				);
			}

			Paginator::SetVariable( 'totalItems', $data['totalItems'] );
			Paginator::SetVariable( 'currentPage', $navPage );
			Paginator::SetVariable( 'maxItemsPerPage', $items );
			Paginator::Run();
		}

		return $data;
	}
}