<?php defined('TOKICMS') or die('Hacking attempt...');

class AddMenu extends Controller {
	
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
	
		//Create a new menu
		$dbarr = array(
			"id_site" 		=> $Admin->GetSite(),
			"id_lang" 		=> $Admin->GetLang(),
			"title" 		=> $_POST['title'],
			"added_time" 	=> time()
		);

		$id = $this->db->insert( 'menus' )->set( $dbarr, null, true );
		
		if ( $id )
		{
			Redirect( $Admin->GetUrl( 'edit-menu' . PS . 'id' . PS . $id ) );
		}
		else
		{
			Redirect( $Admin->GetUrl( 'menus' ) );
		}
		
		exit;
	}
}