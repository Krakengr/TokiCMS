<?php defined('TOKICMS') or die('Hacking attempt...');

class ApproveComment extends Controller {

	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
	
		$this->Run();

		$this->view();
	}
	
	#####################################################
	#
	# Run function
	#
	#####################################################
	private function Run() 
	{
		global $Admin;

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-comments' ) && !IsAllowedTo( 'manage-own-comments' ) )
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$tmp = $this->db->from( null, "
		SELECT id, status, user_id, id_post, id_blog
		FROM `" . DB_PREFIX . "comments`
		WHERE (id = " . $id . ")"
		)->single();

		if ( !$tmp )
			Redirect( $Admin->GetUrl( 'comments' ) );

		//Set the current user ID for the current site
		$userId = $Admin->UserID();
		
		//Check if the user can edit comments other than their own
		if ( !IsAllowedTo( 'manage-comments' ) && IsAllowedTo( 'manage-own-comments' ) )
		{
			if ( $tmp['user_id'] != $userId )
			{
				Redirect( $Admin->GetUrl( 'comments' ) );
			}
		}
		
		$referrer = Sanitize( $_SERVER['HTTP_REFERER'], false );
		
		$host = GetTheHostName( SITE_URL );

		if ( strpos( $referrer, $host ) === false )
			$referrer = $Admin->GetUrl( 'comments' );
		
		//Check if someone tries to fool the system
		if (
			( ( $Admin->CurrentAction() == 'unapprove-comment' ) && ( $tmp['status'] == 'approved' ) )
			||
			( ( $Admin->CurrentAction() == 'approve-comment' ) && ( $tmp['status'] == 'pending' ) )
		)
		{
			$status = ( ( $tmp['status'] == 'pending' ) ? 'approved' : 'pending' );
			
			$this->db->update( "comments" )->where( 'id', $id )->set( "status", $status );
		}
		
		Redirect( $referrer );
	}
}