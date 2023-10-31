<?php defined('TOKICMS') or die('Hacking attempt...');

class AutoContentSources extends Controller {
	
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
		
		if (
			( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-auto-content' ) ) || !$Admin->Settings()::IsTrue( 'enable_autoblog' )
		)
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'auto-content-sources' ) . ' | ' . $Admin->SiteName() );
	}
}