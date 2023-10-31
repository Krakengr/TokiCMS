<?php defined('TOKICMS') or die('Hacking attempt...');

class Sites extends Controller {
	
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
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-sites' ) ) || !MULTISITE )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'sites' ) . ' | ' . $Admin->SiteName() );
		
		$sites = Sites( false );
		
		$this->setVariable( 'DataSites', $sites );

		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		if ( !verify_token( 'sites' ) )
			return;
		
		if ( empty( $_POST['sites'] ) )
		{
			foreach( $sites as $site )
			{
				//Default site should be edited from another page
				if ( $site['is_primary'] == 1 )
				{
					continue;
				}
				
				//Update this site's settings
				$dbarr = array(
					"enable_multilang" 		=> 'false',
					"enable_multiblog" 		=> 'false',
					"enable_maintenance" 	=> 'false',
					"enable_registration" 	=> 'false',
					"disabled" 				=> 0
				);
				
				$this->db->update( "sites" )->where( 'id', $site['id'] )->set( $dbarr );
			}
		}
		
		else
		{
			foreach( $_POST['sites'] as $id => $site )
			{
				//Update this site's settings
				$dbarr = array(
					"enable_multilang" 		=> ( isset( $site['polylang'] ) ? 'true' : 'false' ),
					"enable_multiblog" 		=> ( isset( $site['multiblog'] ) ? 'true' : 'false' ),
					"enable_maintenance" 	=> ( isset( $site['maintenance'] ) ? 'true' : 'false' ),
					"enable_registration" 	=> ( isset( $site['registrations'] ) ? 'true' : 'false' ),
					"disabled" 				=> ( isset( $site['disable'] ) ? 1 : 0 )
				);
				
				$this->db->update( "sites" )->where( 'id', $id )->set( $dbarr );
			}
		}
		
		if ( isset( $_POST['default_site'] ) )
		{
			//Set the primary as 0 for every site
			$this->db->update( 'sites' )->set( "is_primary", 0 );

			//Now we can set the primary site
			$this->db->update( 'sites' )->where( 'id', $_POST['default_site'] )->set( "is_primary", 1 );
		}
		
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}