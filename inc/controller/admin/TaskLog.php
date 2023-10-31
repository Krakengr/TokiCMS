<?php defined('TOKICMS') or die('Hacking attempt...');

class TaskLog extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'task-log' ) . ' | ' . $Admin->SiteName() );
		
		$showAll 	= ( $Admin->IsDefaultSite() ? $Admin->Settings()::IsTrue( 'parent_site_shows_everything' ) : false );
		$logs		= array();
		
		$tmp = $this->db->from( 
		null, 
		"SELECT lo.time_run, lo.time_taken, lo.added_time, s.url, s.title as sna, t.task
		FROM `" . DB_PREFIX . "log_scheduled_tasks` AS lo
		INNER JOIN `" . DB_PREFIX . "sites` AS s ON s.id = lo.id_site
		INNER JOIN `" . DB_PREFIX . "scheduled_tasks` AS t ON t.id_task = lo.id_task" . 
		( $showAll ? "" : " WHERE (lo.id_site = " . $Admin->GetSite() . ")"  ) . "
		ORDER BY time_run DESC"
		)->all();
		
		if ( !empty( $tmp ) )
		{
			$logs = $tmp;
		}

		$this->setVariable( 'Logs', $logs );
		$this->setVariable( 'ShowAll', $showAll );
	}
}