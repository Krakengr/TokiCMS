<?php defined('TOKICMS') or die('Hacking attempt...');

class Logs extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'logs' ) . ' | ' . $Admin->SiteName() );
		
		$showAll = ( $Admin->IsDefaultSite() ? $Admin->Settings()::IsTrue( 'parent_site_shows_everything' ) : false );
		
		$logs = $this->db->from( 
		null, 
		"SELECT lo.user_id, lo.title, lo.id_site, lo.descr, lo.added_time, lo.ip, lo.type, s.url, s.title as sna, COALESCE(u.real_name, u.user_name, NULL) AS user_name
		FROM `" . DB_PREFIX . "logs` AS lo
		INNER JOIN `" . DB_PREFIX . "sites` AS s ON s.id = lo.id_site
		LEFT JOIN `" . DB_PREFIX . USERS . "` AS u ON u.id_member = lo.user_id" . 
		( $showAll ? "" : " WHERE (lo.id_site = " . $Admin->GetSite() . ")"  )
		)->all();	

		$this->setVariable( 'Logs', $logs );
		$this->setVariable( 'ShowAll', $showAll );
	}
}