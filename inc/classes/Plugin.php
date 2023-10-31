<?php defined('TOKICMS') or die('Hacking attempt...');

class Plugin
{
	/**
	 * The hooks to which plugins can be attached.
	 *
	 * @var array
	 */
	public $hooks;
	
	/**
	 * The current hook which we're in (if any)
	 *
	 * @var string
	 */
	public $current_hook;

	/**
	 * Load all plugins.
	 */
	function load()
	{
		if ( DISABLE_HOOKS )
			return;
		/*global $cache, $plugins;

		$plugin_list = $cache->read("plugins");
		if(!empty($plugin_list['active']) && is_array($plugin_list['active']))
		{
			foreach($plugin_list['active'] as $plugin)
			{
				if($plugin != "" && file_exists(MYBB_ROOT."inc/plugins/".$plugin.".php"))
				{
					require_once MYBB_ROOT."inc/plugins/".$plugin.".php";
				}
			}
		}*/
	}

	/**
	 * Add a hook onto which a plugin can be attached.
	 *
	 * @param string       $hook     The hook name.
	 * @param array|string $function The function of this hook.
	 * @param int          $priority The priority this hook has.
	 * @param string       $file     The optional file belonging to this hook.
	 * @return boolean Whether the hook was added.
	 */
	function add_hook($hook, $function, $priority=10, $file="")
	{
		if(is_array($function))
		{
			if(!count($function) == 2)
			{ // must be an array of two items!
				return false;
			}

			if(is_string($function[0]))
			{
				// Static class method
				$method_representation = sprintf('%s::%s', $function[0], $function[1]);
			}
			elseif(is_object($function[0]))
			{
				// Instance class method
				$method_representation = sprintf('%s->%s', spl_object_hash($function[0]), $function[1]);
			}
			else
			{
				// Unknown array type
				return false;
			}

			// Check to see if we already have this hook running at this priority
			if(!empty($this->hooks[$hook][$priority][$method_representation]) && is_array($this->hooks[$hook][$priority][$method_representation]))
			{
				return true;
			}

			// Add the hook
			$this->hooks[$hook][$priority][$method_representation] = array(
				'class_method' => $function,
				'file'         => $file
			);
		}
		else
		{
			// Check to see if we already have this hook running at this priority
			if(!empty($this->hooks[$hook][$priority][$function]) && is_array($this->hooks[$hook][$priority][$function]))
			{
				return true;
			}

			// Add the hook
			$this->hooks[$hook][$priority][$function] = array(
				'function' => $function,
				'file'     => $file
			);
		}

		return true;
	}
	
	/**
	 * Run the hooks that have plugins.
	 *
	 * @param string $hook      The name of the hook that is run.
	 * @param mixed $arguments The argument for the hook that is run. The passed value MUST be a variable
	 * @return mixed The arguments for the hook.
	 */
	function get_hook($hook, &$arguments="")
	{
		if ( DISABLE_HOOKS )
			return null;
		
		if( !isset( $this->hooks[$hook] ) || !is_array( $this->hooks[$hook] ) )
		{
			return $arguments;
		}
		
		$this->current_hook = $hook;
		
	}

	/**
	 * Run the hooks that have plugins.
	 *
	 * @param string $hook      The name of the hook that is run.
	 * @param mixed $arguments The argument for the hook that is run. The passed value MUST be a variable
	 * @return mixed The arguments for the hook.
	 */
	function get_hooks($hook, &$arguments="")
	{
		if ( DISABLE_HOOKS )
			return null;
		
		if(!isset($this->hooks[$hook]) || !is_array($this->hooks[$hook]))
		{
			return $arguments;
		}
		
		$this->current_hook = $hook;
		
		//Sorts hooks by priority in ascending order
		ksort($this->hooks[$hook]);
		
		foreach($this->hooks[$hook] as $priority => $hooks)
		{
			if(is_array($hooks))
			{
				foreach($hooks as $key => $hook)
				{
					if($hook['file'])
					{
						require_once $hook['file'];
					}

					if(array_key_exists('class_method', $hook))
					{
						$return_args = call_user_func_array($hook['class_method'], array(&$arguments));
					}
					else
					{
						$func = $hook['function'];

						$return_args = $func($arguments);
					}

					if($return_args)
					{
						$arguments = $return_args;
					}
				}
			}
		}
		
		$this->current_hook = '';

		return $arguments;
	}

	/**
	 * Remove a specific hook.
	 *
	 * @param string       $hook     The name of the hook.
	 * @param array|string $function The function of the hook.
	 * @param string       $file     The filename of the plugin.
	 * @param int          $priority The priority of the hook.
	 * @return bool Whether the hook was removed successfully.
	 */
	function remove_hook($hook, $function, $file="", $priority=10)
	{
		if(is_array($function))
		{
			if(is_string($function[0]))
			{ // Static class method
				$method_representation = sprintf('%s::%s', $function[0], $function[1]);
			}
			elseif(is_object($function[0]))
			{ // Instance class method
				$method_representation = sprintf('%s->%s', get_class($function[0]), $function[1]);
			}
			else
			{ // Unknown array type
				return false;
			}

			if(!isset($this->hooks[$hook][$priority][$method_representation]))
			{
				return true;
			}
			unset($this->hooks[$hook][$priority][$method_representation]);
		}
		else
		{
			// Check to see if we don't already have this hook running at this priority
			if(!isset($this->hooks[$hook][$priority][$function]))
			{
				return true;
			}
			unset($this->hooks[$hook][$priority][$function]);
		}

		return true;
	}
}

