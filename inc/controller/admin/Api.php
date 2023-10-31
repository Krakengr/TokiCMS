<?php defined('TOKICMS') or die('Hacking attempt...');

class Api extends Controller {
	
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
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-api' ) ) || !$Admin->Settings()::IsTrue( 'enable_api' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'api' ) . ' | ' . $Admin->SiteName() );
	}
}