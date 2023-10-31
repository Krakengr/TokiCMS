<?php defined('TOKICMS') or die('Hacking attempt...');

class AddApi extends Controller {
	
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

		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-api' ) ) || !$Admin->Settings()::IsTrue( 'enable_api' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'add-api' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'add_api' ) )
			Redirect( $Admin->GetUrl( 'api' ) );
		
		if ( empty( $_POST['apiName'] ) )
		{
			$Admin->SetAdminMessage( __( 'enter-a-valid-title' ) );
			return;
		}
		
		$token = sha1( time() . GenerateRandomKey( 15 ) );
		$token = substr( $token, 0, 20 );
		
		$dbarr = array(
			"id_site" 		=> $Admin->GetSite(),
			"name" 			=> $_POST['apiName'],
			"descr" 		=> $_POST['apiDescr'],
			"token" 		=> $token,
			"allow_data" 	=> json_encode( array() ),
			"api_limit" 	=> 1000,
			"added_time" 	=> time(),
			"items_limit" 	=> 20
		);

		$id = $this->db->insert( 'api_obj' )->set( $dbarr, null, true );

		if ( $id )
		{
			Redirect( $Admin->GetUrl( 'edit-api' . PS . 'id' . PS . $id ) );
		}
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'api' ) );
		}
	}
}