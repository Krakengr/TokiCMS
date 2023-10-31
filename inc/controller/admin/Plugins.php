<?php defined('TOKICMS') or die('Hacking attempt...');

class Plugins extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) )
		{
			Router::SetNotFound();
			return;
		}

		// Load the plugins before return so they can be available in the plugins form
		$plugins = LoadPlugins();

		// Load the available plugins from the DB
		$pluginsDb = GetPluginsDB( $Admin->GetSite() );
		
		$pluginsDb = ( !empty( $pluginsDb['plugins'] ) ? $pluginsDb['plugins'] : array() );
		
		$this->setVariable( 'Plugins', $plugins );
		
		$this->setVariable( 'pluginsDb', $pluginsDb );
		
		Theme::SetVariable( 'headerTitle', __( 'plugins' ) . ' | ' . $Admin->SiteName() );

		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !verify_token( 'plugins' ) )
			return;

		$this->db->update( "plugins" )->where( 'id_site', $Admin->GetSite() )->set( "status", 'inactive' );
		
		$order = $this->db->from( 
		null, 
		"SELECT plugin_order
		FROM `" . DB_PREFIX . "plugins`
		WHERE (id_site = " . $Admin->GetSite() . ")
		ORDER BY plugin_order DESC
		LIMIT 1"
		)->single();
		
		$plugin_order = ( $order ? ( $order['plugin_order'] + 1 ) : 0 );

		if ( !empty( $_POST['plugin-enable'] ) )
		{
			foreach ( $_POST['plugin-enable'] as $plugin_id => $_ )
			{
				if ( !isset( $plugins[$plugin_id] ) )
				{
					continue;
				}
				
				//Check if we have this data
				$plugin = $this->db->from( 
				null, 
				"SELECT id
				FROM `" . DB_PREFIX . "plugins`
				WHERE (id_site = " . $Admin->GetSite() . ") AND (plugin_id = :id)",
				array( $plugin_id => ':id' )
				)->single();
				
				if ( !$plugin )
				{
					$data = $plugins[$plugin_id];

					$dbarr = array(
						"id_site" 		=> $Admin->GetSite(),
						"plugin_id" 	=> $plugin_id,
						"name" 			=> $data['title'],
						"description" 	=> $data['description'],
						"status" 		=> 'active',
						"plugin_order" 	=> $plugin_order
					);

					$itm = $this->db->insert( 'plugins' )->set( $dbarr, null, true );
					
					if ( $itm )
					{
						if ( !empty( $data['hooks'] ) )
						{
							//Delete any previous data
							$this->db->delete( 'plugin_hooks' )->where( 'id_plugin', $plugin_id )->run();
							
							foreach( $data['hooks'] as $hook )
							{
								$dbarr = array(
									"id_plugin" 	=> $itm,
									"hook_id" 		=> $hook['hood_id'],
									"function_name" => $hook['function'],
									"file_include" 	=> $hook['file']
								);

								$this->db->insert( 'plugin_hooks' )->set( $dbarr, null, true );
							}
						}

						//Increase the order number
						$plugin_order++;
					}
				}
				
				else
				{
					$this->db->update( "plugins" )->where( 'id', $plugin['id'] )->set( "status", 'active' );
				}
			}
		}

		$Admin->EmptyCaches();

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}