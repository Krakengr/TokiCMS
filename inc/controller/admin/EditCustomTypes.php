<?php defined('TOKICMS') or die('Hacking attempt...');

class EditCustomTypes extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-post-types' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			Redirect( $Admin->GetUrl( 'custom-post-types' ) );
	
		if ( !verify_token( 'edit_custom_types' ) )
			Redirect( $Admin->GetUrl( 'custom-post-types' ) );
		
		if ( empty( $_POST['customTypesBulkAction'] ) )
			Redirect( $Admin->GetUrl( 'custom-post-types' ) );
	
		if ( ( $_POST['customTypesBulkAction'] == 'delete' ) && !empty( $_POST['del'] ) )
		{
			foreach( $_POST['del'] as $id )
			{
				DeleteCustomType( $id );
			}
		}
		
		elseif ( $_POST['customTypesBulkAction'] == 'update' )
		{
			foreach( $_POST['sort_order'] as $id => $order )
			{
				$this->db->update( "post_types" )->where( 'id', $id )->set( "type_order", $order );
			}
		}

		$Admin->EmptyCaches();
		
		Redirect( $Admin->GetUrl( 'custom-post-types' ) );
	}
}