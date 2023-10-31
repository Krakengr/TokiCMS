<?php defined('TOKICMS') or die('Hacking attempt...');

class AdminPages extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-own-posts' ) && !IsAllowedTo( 'manage-posts' ) )
		{
			Router::SetNotFound();
			return;
		}

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'pages' ) )
			return;

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}