<?php defined('TOKICMS') or die('Hacking attempt...');

class Schemas extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-seo' ) ) || !$Admin->Settings()::IsTrue( 'enable_seo' ) )
		{
			Router::SetNotFound();
			return;
		}
	}
}