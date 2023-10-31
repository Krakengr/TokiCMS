<?php defined('TOKICMS') or die('Hacking attempt...');

class EditComment extends Controller {

	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
	
		$this->Run();
		
		Theme::Build();

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
		
		$Comment = AdminSingleComment( $id );

		if ( !$Comment )
			Redirect( $Admin->GetUrl( 'comments' ) );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-comment' ) . ': "' . $Comment['name'] . '" | ' . $Admin->SiteName() );

		//Set the current user ID for the current site
		$userId = $Admin->UserID();
		
		$editUri = AdminCommentEditUri( $id, $Comment['id_blog'], $Comment['id_site'], $Comment['id_lang'], $Admin->DefaultLang()['id'] );
		
		//Check if the user can edit posts other than their own
		if ( !IsAllowedTo( 'manage-comments' ) && IsAllowedTo( 'manage-own-comments' ) )
		{
			if ( $Comment['user_id'] != $userId )
			{
				Redirect( $Admin->GetUrl( 'comments' ) );
			}
		}
		
		$canViewAttachments = ( ( IsAllowedTo( 'view-attachments' ) || IsAllowedTo( 'manage-attachments' ) ) ? true : false );

		$this->setVariable( 'Comment', $Comment );
		$this->setVariable( 'canViewAttachments', $canViewAttachments );
		
		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
		{
			//Maybe we need a redirection?
			//Put this in here, to avoid any redirection when updating the comment
			if (
				( $Comment['id_lang'] != $Admin->GetLang() )
				||
				( $Comment['id_site'] != $Admin->GetSite() )
				||
				( ( $Comment['id_blog'] > 0 ) && ( $Comment['id_blog'] != $Admin->GetBlog() ) )
			)
			{
				Redirect( $editUri );
			}

			return;
		}
		
		//We can update the DB
		$dbarr = array(
			"name" 		=> $_POST['name'],
			"email" 	=> ( !Validate( $_POST['email'], 'email' ) ? $Comment['email'] : $_POST['email'] ),
			"url" 		=> ( !Validate( $_POST['url'], 'url' ) ? $Comment['url'] : $_POST['url'] ),
			"comment" 	=> $_POST['content'],
			"status" 	=> $_POST['status']
		);

		$this->db->update( 'comments' )->where( 'id', $id )->set( $dbarr );
			
		//Delete the cache
		$Admin->DeleteFileCache( $Comment['pid'], $Admin->GetSite(), $Comment['ts'], $Admin->LangKey() );

		//Redirect to the same page
		Redirect( $Admin->GetUrl( 'edit-comment' . PS . 'id' . PS . $id ) );
	}
}