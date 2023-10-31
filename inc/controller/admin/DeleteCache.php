<?php defined('TOKICMS') or die('Hacking attempt...');

class DeleteCache extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

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

		$Admin->EmptyCaches();
		
		Redirect( $Admin->GetUrl() );
	}
}