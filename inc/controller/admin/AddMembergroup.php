<?php defined('TOKICMS') or die('Hacking attempt...');

class AddMembergroup extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-members' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'add-membergroup' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'add-membergroup' ) )
			return;
		
		$slug = CreateSlug( $_POST['name'] );
		
		$dbarr = array(
			"group_name" 		=> $_POST['name'],
			"description" 		=> $_POST['description'],
			"slug" 				=> $slug,
			"min_posts" 		=> ( isset( $_POST['min_posts'] ) ? $_POST['min_posts'] : -1 ),
			"max_messages"		=> ( isset( $_POST['max_messages'] ) ? $_POST['max_messages'] : 0 ),
			"group_type" 		=> 'custom'
		);

		$id = $this->db->insert( 'membergroups' )->set( $dbarr, null, true );

		if ( $id )
		{
			$dbarr = array(
				"id_group" 				=> $id,
				"id_site" 				=> $Admin->GetSite(),
				"group_permissions" 	=> json_encode( array() ),
				"time_permissions" 		=> json_encode( array() )
			);

			$this->db->insert( 'membergroup_relation' )->set( $dbarr );

			@unlink( GUESTS_PERMISSIONS_FILE );
			
			Redirect( $Admin->GetUrl( 'edit-membergroup' . PS . 'id' . PS . $id ) );
		}
		else
		{
			Redirect( $Admin->GetUrl( 'membergroups' ) );
		}
	}
}