<?php defined('TOKICMS') or die('Hacking attempt...');

class AddPostAttribute extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if 
		( 
			( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'manage-post-attributes' ) ) || !$Admin->Settings()::IsTrue( 'enable_post_attributes' )
		)
		{
			Router::SetNotFound();
			return;
		}
		
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !verify_token( 'add_attribute' ) )
		{
			Redirect( $Admin->GetUrl( 'post-attributes' ) );
		}
		
		$slug = SetShortSef( 'post_attributes', 'id', 'sef', CreateSlug( $_POST['name'], true ), null, null, false );
		
		//This is simple, because we don't have to check if we already have the same data in the db
		//We can add as many as we want
		$dbarr = array(
			"id_group" 		=> $_POST['group'],
			"name" 			=> $_POST['name'],
			"sef" 			=> $slug,
			"attr_order" 	=> $_POST['order'],
			"trans_data"	=> json_encode( array() )
		);

		$id = $this->db->insert( 'post_attributes' )->set( $dbarr, null, true );
	
		if ( $id )
		{
			Redirect( $Admin->GetUrl( 'edit-post-attribute' . PS . 'id' . PS . $id ) );
		}
		
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'post-attributes' ) );
		}
	}
}