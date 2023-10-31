<?php defined('TOKICMS') or die('Hacking attempt...');

class AddAttributeGroup extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'manage-post-attributes' ) ) 
				|| !$Admin->Settings()::IsTrue( 'enable_post_attributes' ) 
		)
		{
			Router::SetNotFound();
			return;
		}

		//Don't do anything if there is no POST
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !verify_token( 'add-attribute-group' ) )
		{
			Redirect( $Admin->GetUrl( 'attribute-groups' ) );
		}

		$slug = SetShortSef( "post_attr_group", 'id', 'sef', CreateSlug( $_POST['name'] ) );

		//This is simple, because we don't have to check if we already have the same data in the db
		//We can add as many as we want
		$dbarr = array(
			"id_site" 			=> $Admin->GetSite(),
			"id_lang" 			=> $Admin->GetLang(),
			"id_blog" 			=> $Admin->GetBlog(),
			"name" 				=> $_POST['name'],
			"group_order"		=> $_POST['order'],
			"id_custom_type" 	=> ( isset( $_POST['postType'] ) ? $_POST['postType'] : 0 ),
			"trans_data" 		=> json_encode( array() ),
			"sef" 				=> $slug
		);

		$id = $this->db->insert( 'post_attr_group' )->set( $dbarr, null, true );

		if ( $id )
		{
			Redirect( $Admin->GetUrl( 'edit-attribute-group' . PS . 'id' . PS . $id ) );
		}
		
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'attribute-groups' ) );
		}
		
		exit;
	}
}