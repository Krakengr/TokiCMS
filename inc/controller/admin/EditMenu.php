<?php defined('TOKICMS') or die('Hacking attempt...');

class EditMenu extends Controller {
	
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

		$id = (int) Router::GetVariable( 'key' );
		
		$menu = GetAdminMenu( $id, $Admin->GetLang(), $Admin->GetSite() );

		if ( !$menu )
			Redirect( $Admin->GetUrl( 'menus' ) );
		
		$menus = Menus( $Admin->GetSite(), false );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-menu' ) . ': "' . $menu['title'] . '" | ' . $Admin->SiteName() );

		$this->setVariable( 'Menus', $menus );
		$this->setVariable( 'Menu', $menu );

		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
	}
}