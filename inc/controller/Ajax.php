<?php defined('TOKICMS') or die('Hacking attempt...');

// No cache headers
header("Expires: Sat, 1 Jan 2000 01:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header('Content-type: text/xml');

class Ajax extends Controller {
	
    public function process() 
	{
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) && ( $_SERVER['REQUEST_METHOD'] !== 'GET' ) )
			exit;
		
		$this->setVariable( 'Lang', $this->lang );

		if ( !IsAllowedTo( 'view-site' ) )
		{
			exit;
		}

		//$Hooks = $this->getVariable( 'Hooks' );
		
		$token = $action = $request = null;
		
		if ( $_SERVER['REQUEST_METHOD'] === 'GET' )
		{
			$request = $_GET;
			
			$token = ( isset( $_GET['token'] ) ? Sanitize( $_GET['token'], false ) : null );
			$action = ( isset( $_GET['action'] ) ? Sanitize( $_GET['action'], false ) : null );
		}
		else
		{
			$request = $_POST;
			
			$token = ( isset( $_POST['token'] ) ? Sanitize( $_POST['token'], false ) : null );
			$action = ( isset( $_POST['action'] ) ? Sanitize( $_POST['action'], false ) : null );
		}

		//Check the token
		if ( empty( $token ) )
		{
			Log::Set( 'Ajax bad request', 'No token given', $request, 'system' );
			die( 'No token given' );
		}
		
		if ( !VerifySessionToken( 'ajax', $token ) )
		{
			Log::Set( 'Ajax bad request', 'Invalid token given', $request, 'system' );
			die( 'Invalid token given' );
		}

		if ( empty( $action ) )
		{
			Log::Set( 'Ajax bad request', 'No Function given', $request, 'system' );
			die( 'No Function given' );
		}
		
		//Load the generic functions
		require( FUNCTIONS_ROOT . 'ajax-site-functions.php' );

		if ( !function_exists( $action ) )
		{
			//Get the hook list as an array
			$hookList = null;//$Hooks->hooks['siteAjax'];

			if ( !empty( $hookList ) )
			{
				foreach ( $hookList as $pr => $hook )
				{
					if ( isset( $hook[$action] ) )
					{
						if( !empty( $hook[$action]['file'] ) && file_exists( $hook[$action]['file'] ) )
						{
							require_once $hook[$action]['file'];
						}
						
						$action = $hook[$action]['function'];

						break;
					}
				}
			}
		}
		
		if ( !function_exists( $action ) || !is_callable( $action ) )
		{
			die( 'No valid Function found' );
			exit;
		}
		
		call_user_func( $action );
		
		exit(0);
	}
}