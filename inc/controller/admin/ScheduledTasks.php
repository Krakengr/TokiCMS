<?php defined('TOKICMS') or die('Hacking attempt...');

class ScheduledTasks extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'scheduled-tasks' ) . ' | ' . $Admin->SiteName() );
		
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
		{
			// Verify if the token is correct
			if ( !verify_token( 'scheduled-tasks' ) )
				return;
			
			if ( isset( $_POST['save'] ) && isset( $_POST['tasks'] ) )
			{
				//Set every task as disabled
				$this->db->update( 'scheduled_tasks' )->where( 'id_site', $Admin->GetSite() )->set( "disabled", 1 );
					
				foreach( $_POST['tasks'] as $taskId => $task )
				{
					$this->db->update( 'scheduled_tasks' )->where( 'id_task', $taskId )->where( 'id_site', $Admin->GetSite() )->set( "disabled", 0 );
				}
			}

			//Redirect to the same page
			@header('Location: ' . $Admin->GetUrl( null, null, true ) );
			exit;
		}

		else
		{
			$tasks = GetScheduledTasks( $Admin->GetSite() );
			
			if ( !$tasks )
				return $this->setVariable( 'Tasks', $tasks );
			
			require ( ARRAYS_ROOT . 'cron-arrays.php');
			
			$arr = array();

			foreach ( $tasks as $task )
			{
				if ( !isset( $scheduledTasks[$task['task']] ) )
					continue;
				
				$curr = $scheduledTasks[$task['task']];
				
				$unit = ( ( $task['time_unit'] == 'w' ) ? __( 'weeks' ) : ( ( $task['time_unit'] == 'h' ) ? __( 'hours' ) : ( ( $task['time_unit'] == 'm' ) ? __( 'minutes' ) : __( 'days' ) ) ) );
				
				$rep = sprintf( __( 'repeating-every-x' ), $task['time_regularity'], $unit );
				
				$arr[] = array(
					'id' => $task['id_task'],
					'name' => $curr['name'],
					'tip' => $curr['tip'],
					'disabled' => $task['disabled'],
					'next' => ( !empty( $task['next_time'] ) ? date( 'Y-m-d H:i:s', $task['next_time'] ) : 'N/A' ),
					'rep' => $rep
				);
			}

			$this->setVariable( 'Tasks', $arr );
		}
	}
}