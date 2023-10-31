<?php defined('TOKICMS') or die('Hacking attempt...');

class EditMembergroup extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-members' ) )
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$Group = AdminGetSingleGroup( $id );

		if ( !$Group )
			Redirect( $Admin->GetUrl( 'membergroups' ) );
		
		$this->setVariable( 'Group', $Group );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-membergroup' ) . ': "' . $Group['group_name'] . '" | ' . $Admin->SiteName() );

		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		// Verify if the token is correct
		if ( !verify_token( 'edit_group_' . $id ) )
			Redirect( $Admin->GetUrl( 'membergroups' ) );
		
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete'] ) )
		{
			//You can't delete a system group
			if ( ( $Group['group_type'] == 'system' ) || !isset( $_POST['new_group'] ) || ( $_POST['new_group'] == 0 ) )
				return;
			
			$this->db->update( 'members' )->where( 'id_group', $id )->set( "id_group", $_POST['new_group'] );
	
			$this->db->delete( 'membergroups' )->where( "id_group", $id )->run();

			//Delete the relations also
			$this->db->delete( 'membergroup_relation' )->where( "id_group", $id )->run();
				
			$Admin->EmptyCaches();

			//Redirect to the dashboard
			@header('Location: ' . $Admin->GetUrl( 'membergroups' ) );
			exit;
		}
		
		if ( $id == 1 )
		{
			$permissions = 'all';
			$timeLimits = 'none';
		}
		
		else
		{
			$permissions = $timeLimits = array();
			
			if ( !empty( $_POST['permissions'] ) )
			{
				$permissions = array_keys( $_POST['permissions'] );
			}
			
			$permissions = json_encode( $permissions );
			
			$timeLimits = json_encode( $timeLimits );
		}
		
		$dbarr = array(
			"group_name" 	=> $_POST['title'],
			"group_color" 	=> $_POST['color'],
			"description" 	=> $_POST['description'],
			"min_posts" 	=> ( isset( $_POST['min_posts'] ) ? $_POST['min_posts'] : -1 ),
			"max_messages" 	=> ( isset( $_POST['max_messages'] ) ? $_POST['max_messages'] : 0 )
		);
		
		$this->db->update( "membergroups" )->where( 'id_group', $id )->set( $dbarr );

		//Update the permissions for this group
		$dbarr = array(
			"group_permissions" => $permissions,
			"time_permissions" 	=> $timeLimits
		);
		
		$this->db->update( "membergroup_relation" )->where( 'id_group', $id )->where( 'id_site', $Admin->GetSite() )->set( $dbarr );
		
		if ( $Admin->IsDefaultSite() )
		{
			@unlink( GUESTS_PERMISSIONS_FILE );
		}
		
		else
		{
			$Admin->DeleteChildDataCacheSite( 'permissions' );
		}

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( 'edit-membergroup/id/' . $id, null, false ) );
		exit;
	}
}