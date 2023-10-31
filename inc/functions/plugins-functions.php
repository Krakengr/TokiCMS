<?php defined('TOKICMS') or die('Hacking attempt...');

//
// Plugins Functions
//

// Return all plugins that hook into $hook_id
function RunHooks( $hook_id, &$arguments = "" )
{
	if ( DISABLE_HOOKS )
		return false;
	
	$mods = GetPluginsDB()['hooks'];

	if ( empty( $mods ) || empty( $mods[$hook_id] ) )
	{
		return null;
	}
	
	$hooks = $mods[$hook_id];

	foreach( $hooks as $id => $hook )
	{
		$dir = PLUGINS_ROOT . $id . DS;
		
		$return_args = null;

		if ( !empty( $hook['file'] ) && file_exists ( $dir . $hook['file'] ) )
		{
			require_once ( $dir . $hook['file'] );
		}

		elseif ( !empty( $hook['function'] ) && function_exists( $hook['function'] ) && is_callable( $hook['function'] ) )
		{
			$return_args = call_user_func_array( $hook['function'], array( &$arguments ) );
		}
		
		if( $return_args )
		{
			$arguments = $return_args;
		}
	}

	return $arguments;
}

//Get the plugins from the DB
function GetPluginsDB( $siteId = SITE_ID )
{
	$cacheFile = CACHE_ROOT . 'content' . DS . 'plugins-db_site-' . $siteId . '-' . sha1 ( 'plugins-db_' . $siteId . CACHE_HASH ) . '.php';

	if ( file_exists( $cacheFile )  )
	{
		$array = ReadCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		//Query: plugins
		$data = $db->from( null, "
		SELECT *
		FROM `" . DB_PREFIX . "plugins`
		WHERE (id_site = " . $siteId . ")
		ORDER BY plugin_order ASC" //Order by is needed in admin page
		)->all();

		$array = array(
			'plugins' 	=> array(),
			'hooks' 	=> array()
		);
		
		if ( $data )
		{
			foreach( $data as $plugin )
			{
				if ( $plugin['status'] == 'active' )
				{
					$hooks = $db->from( null, "
					SELECT *
					FROM `" . DB_PREFIX . "plugin_hooks`
					WHERE (id_plugin = " . $plugin['id'] . ")"
					)->all();
					
					if ( !empty( $hooks ) )
					{
						foreach( $hooks as $hook )
						{
							$array['hooks'][$hook['hook_id']][$plugin['plugin_id']] = array
							(
								'file' 		=> $hook['file_include'],
								'function' 	=> $hook['function_name']
							);
						}
					}
					
				}
				
				$array['plugins'][$plugin['plugin_id']] = array
				(
					'name' 			=> $plugin['name'],
					'description' 	=> $plugin['description'],
					'status' 		=> $plugin['status'],
					'plugin_order' 	=> $plugin['plugin_order']
				);
			}
			
			WriteCache( $array, $cacheFile );
		}
	}

	return $array;
}