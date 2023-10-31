<?php defined('TOKICMS') or die('Hacking attempt...');

class RemovePost extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-own-posts' ) && !IsAllowedTo( 'manage-posts' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$id = (int) Router::GetVariable( 'key' );
		
		$type = ( ( !empty( $_SERVER['HTTP_REFERER'] ) && str_contains( $_SERVER['HTTP_REFERER'], 'pages' ) ) ? 'pages' : 'posts' );
		
		$Post = GetSinglePost( $id, null, false, true, true );

		if ( !$Post )
			Redirect( $Admin->GetUrl( $type ) );
		
		$redir = ( $Post->IsPage() ? 'pages' : 'posts' );

		$auth = $this->getVariable( 'AuthUser' );
		
		//Check if the user can delete posts other than their own
		if ( !IsAllowedTo( 'manage-posts' ) && IsAllowedTo( 'manage-own-posts' ) )
		{
			if ( $Post->UserId() != $auth['id_member'] )
			{
				Redirect( $Admin->GetUrl( $redir ) );
			}
		}

		// Make sure that the post is in the recycled bin
		if ( $Post->Status() !== 'deleted' )
		{
			Redirect( $Admin->GetUrl( $redir ) );
		}
		
		//Delete the Post
		DeletePost( $Post );
		
		$Admin->EmptyCaches();

		Redirect( $Admin->GetUrl( $redir ) );
	}
}