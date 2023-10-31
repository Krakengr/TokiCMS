<?php defined('TOKICMS') or die('Hacking attempt...');

class Redirections extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-redirections' ) || !$Admin->Settings()::IsTrue( 'enable_redirect' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'redirections' ) . ' | ' . $Admin->SiteName() );
	}
}