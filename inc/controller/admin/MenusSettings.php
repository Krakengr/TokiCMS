<?php defined('TOKICMS') or die('Hacking attempt...');

class MenusSettings extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$menus = Menus( $Admin->GetSite(), false );
		
		$this->setVariable( 'Menus', $menus );
		
		Theme::SetVariable( 'headerTitle', __( 'menus' ) . ' | ' . $Admin->SiteName() );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		if ( !verify_token( 'menus' ) )
			return;

		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}