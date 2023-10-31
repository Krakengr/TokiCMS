<?php defined('TOKICMS') or die('Hacking attempt...');

class BrokenLinkChecker extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) || !$Admin->Settings()::IsTrue( 'enable_link_manager' ) )
		{
			Router::SetNotFound();
			return;
		}

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'broken-link-checker' ) )
			return;
	}
}