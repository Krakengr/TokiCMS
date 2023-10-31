<?php defined('TOKICMS') or die('Hacking attempt...');

class MovePosts extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'bulk-move-posts' ) . ' | ' . $Admin->SiteName() );
		
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
	}
}