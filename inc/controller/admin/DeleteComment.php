<?php defined('TOKICMS') or die('Hacking attempt...');

class DeleteComment extends Controller {

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
		
		$referrer = ( isset( $_SERVER['HTTP_REFERER'] ) ? Sanitize( $_SERVER['HTTP_REFERER'], false ) : null );
		
		$host = GetTheHostName( SITE_URL );

		if ( empty( $referrer ) || ( strpos( $referrer, $host ) === false ) )
		{
			$referrer = $Admin->GetUrl( 'comments' );
		}

		//Check if the user can edit posts other than their own
		if ( !IsAllowedTo( 'manage-comments' ) && IsAllowedTo( 'manage-own-comments' ) )
		{
			if ( $tmp['user_id'] != $userId )
			{
				Redirect( $Admin->GetUrl( 'comments' ) );
			}
		}
		
		//If the comment is already in recyble bin, remove it from the DB
		if ( $tmp['status'] == 'deleted' )
		{
			$this->db->delete( 'comments' )->where( "id", $id )->run();
			
			//Redirect to comments page
			Redirect( $Admin->GetUrl( 'comments' ) );
		}
		
		else
		{
			//We can update the DB
			$q = $this->db->update( "comments" )->where( 'id', $id )->set( "status", "deleted" );
			
			if ( $q )
			{
				//Update the posts's num items
				$this->db->update( POSTS )->where( "id_post", $tmp['id_post'] )->decrease( "num_comments" );
				
				//Update the blog's num items too
				if ( $tmp['id_blog'] > 0 )
				{
					$this->db->update( "blogs" )->where( "id_blog", $tmp['id_blog'] )->decrease( "num_comments" );
				}
			}
		}
		
		//Get this post 
		$pst = GetSinglePost( $tmp['id_post'], null, false, false, false, false );
		
		if ( $pst )
		{
			//Delete the post's cache file
			$Admin->DeleteFileCache( $pst['id'], $pst['site']['id'], $pst['sef'], $pst['language']['key'] );
		}

		Redirect( $referrer );
	}
}