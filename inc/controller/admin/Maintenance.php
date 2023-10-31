<?php defined('TOKICMS') or die('Hacking attempt...');

class Maintenance extends Controller {
	
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
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'maintenance' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'maintenance' ) )
		return;
	
		if ( !isset( $_POST['func'] ) || empty( $_POST['func'] ) )
			return;
	
		//Avoid system from dying...
		@set_time_limit( 600 );
	
		//We need memory for some functions
		if ( @ini_get( 'memory_limit' ) < 256 )
			@ini_set( 'memory_limit', '256M' );
	
		//Get the MaintenanceTasks array
		$maintenanceTasks = $Admin->MaintenanceTasks();
	
		//Get the key of what we want
		$func = array_keys( $_POST['func'] );
	
		if ( !isset( $maintenanceTasks[$func['0']] ) )
		{
			$Admin->SetAdminMessage( __( 'an-error-happened' ) );
			return;
		}

		if ( is_callable( $maintenanceTasks[$func['0']]['function'] ) )
		{
			try
			{
				call_user_func( $maintenanceTasks[$func['0']]['function'] );
				$Admin->SetAdminMessage( sprintf( __( 'task-executed-successfully' ), $maintenanceTasks[$func['0']]['name'] ) );
				return;
			}
			catch(PDOException $e)
			{
				$Admin->SetAdminMessage( sprintf( __( 'task-executed-error' ), $maintenanceTasks[$func['0']]['name'] ) );
				return;
			}
		}
		else
		{
			$Admin->SetAdminMessage( __( 'an-error-happened' ) );
			return;
		}
	}
}