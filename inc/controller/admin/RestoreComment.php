<?php defined('TOKICMS') or die('Hacking attempt...');

class RestoreComment extends Controller {

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
		{
			Redirect( $Admin->GetUrl( 'comments' ) );
		}
		
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
		
		//Check if the comment can be restored
		if ( $tmp['status'] == 'deleted' )
		{
			$q = $this->db->update( "comments" )->where( 'id', $id )->set( "status", "approved" );

			if ( $q )
			{
				//Update the posts's num items
				$this->db->update( POSTS )->where( "id_post", $tmp['id_post'] )->increase( "num_comments" );
				
				//Update the blog's num items too
				if ( $tmp['id_blog'] > 0 )
				{
					$this->db->update( "blogs" )->where( "id_blog", $tmp['id_blog'] )->increase( "num_comments" );
				}
			}

			//Get this post 
			$pst = GetSinglePost( $tmp['id_post'], null, false, false, false, false );

			if ( $pst )
			{
				//Delete the post's cache file
				$Admin->DeleteFileCache( $pst['id'], $pst['site']['id'], $pst['sef'], $pst['language']['key'] );
			}
			
			Redirect( $Admin->GetUrl( 'edit-comment' . PS . 'id' . PS . $id ) );
		}
		
		Redirect( $Admin->GetUrl( 'comments' ) );
	}
}