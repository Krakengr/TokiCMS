<?php defined('TOKICMS') or die('Hacking attempt...');

class EditApi extends Controller {
	
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

		$id = (int) Router::GetVariable( 'key' );
		
		$Api = AdminGetSingleApi( $id );

		if ( !$Api )
			Redirect( $Admin->GetUrl( 'api' ) );
	
		Theme::SetVariable( 'headerTitle', __( 'edit-api-object' ) . ': "' . $Api['name'] . '" | ' . $Admin->SiteName() );
		
		$this->setVariable( 'Api', $Api );

		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		// Verify if the token is correct
		if ( !verify_token( 'edit_api_' . $id ) )
			Redirect( $Admin->GetUrl( 'api' ) );
		
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete'] ) )
		{
			//You can't delete a system api
			if ( $Api['is_primary'] == 1 )
				return;
			
			$this->db->delete( 'api_obj' )->where( 'id', $id )->run();

			$Admin->EmptyCaches();

			//Redirect to the main page
			@header('Location: ' . $Admin->GetUrl( 'api' ) );
			exit;
		}
		
		if ( $Api['is_primary'] == 1 )
		{
			$permissions = 'all';
		}

		else
		{
			$permissions = array();
			
			if ( !empty( $_POST['permissions'] ) )
			{
				$permissions = array_keys( $_POST['permissions'] );
			}
			
			$permissions = json_encode( $permissions );
		}
		
		$items = (int) $_POST['items'];
		$limit = (int) $_POST['limit'];
		
		$items = ( ( $items == 0 ) ? HOMEPAGE_ITEMS : ( ( $items > 1000 ) ? 1000 : $items ) );
		
		$limit = ( ( $items > 10000 ) ? 10000 : $limit );
		
		$dbarr = array(
            "disabled" 		=> ( !empty( $_POST['disable'] ) ? 1 : 0 ),
			"name" 			=> $_POST['apiName'],
			"descr" 		=> $_POST['apiDescr'],
			"allow_data" 	=> $permissions,
			"api_limit" 	=> $limit,
			"items_limit" 	=> $items
        );

		$this->db->update( "api_obj" )->where( "id", $id )->set( $dbarr );

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( 'edit-api/id/' . $id, null, false ) );
		exit;
	}
}