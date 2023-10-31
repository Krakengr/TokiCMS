<?php defined('TOKICMS') or die('Hacking attempt...');

class Users extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		if ( !IsAllowedTo( 'view-mlist' ) && !IsAllowedTo( 'admin-site' ) )
		{
			Router::SetNotFound();
			return;
		}
	}
}