<?php defined('TOKICMS') or die('Hacking attempt...');

class AddCustomPostType extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'manage-post-types' ) )
		{
			Router::SetNotFound();
			return;
		}

		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			Redirect( $Admin->GetUrl( 'custom-post-types' ) );
	
		if ( !verify_token( 'add_custom_type' ) || empty( $_POST['typeName'] ) )
			Redirect( $Admin->GetUrl( 'custom-post-types' ) );
	
		$slug = SetShortSef( 'post_types', 'id', 'sef', CreateSlug( ( !empty( $_POST['typeSlug'] ) ? $_POST['typeSlug'] : $_POST['typeName'] ) ) );
		
		$dbarr = array(
			"id_site" 	=> $Admin->GetSite(),
			"title" 	=> $_POST['typeName'],
			"sef" 		=> $slug
		);

		$id = $this->db->insert( "post_types" )->set( $dbarr, null, true );
		
		if ( $id )
		{
			$Admin->DeleteSettingsCacheSite( 'settings' );

			Redirect( $Admin->GetUrl( 'edit-custom-post-type' . PS . 'id' . PS . $id ) );
		}
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'custom-post-types' ) );
		}
	}
}