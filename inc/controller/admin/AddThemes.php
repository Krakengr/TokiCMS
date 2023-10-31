<?php defined('TOKICMS') or die('Hacking attempt...');

class AddThemes extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		if ( !IsAllowedTo( 'admin-site' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		global $Admin;
		
		$url = $Admin->themesUri;
		
		$data = PingSite( $url );
		
		if ( empty( $data ) || !isset( $data['results'] ) || ( $data['results'] == 0 ) || empty( $data['themes'] ) )
		{
			$this->setVariable( 'Themes', null );
			
			return;
		}
		
		$this->setVariable( 'Themes', $data );
	}
}