<?php defined('TOKICMS') or die('Hacking attempt...');

class CustomPostTypes extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'manage-post-types' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'custom-post-types' ) . ' | ' . $Admin->SiteName() );
		
		$types = GetAdminCustomTypes();
		
		$this->setVariable( 'PostTypes', $types );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
	}
}