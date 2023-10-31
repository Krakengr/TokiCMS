<?php defined('TOKICMS') or die('Hacking attempt...');

class Langs extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-languages' ) || !$Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'langs' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
			
		// Verify if the token is correct
		if ( !verify_token( 'langs' ) )
			return;
		
		if ( !empty( $_POST['default_lang'] ) )
		{
			$this->db->update( "languages" )->where( 'id_site', $Admin->GetSite() )->set( "is_default", 0 );
			$this->db->update( "languages" )->where( 'id', $_POST['default_lang'] )->set( "is_default", 1 );
		}
		
		foreach ( $_POST['lang-order'] as $lang_id => $order )
		{
			//Check it this languages exists
			$data = $this->db->from( 
			null, 
			"SELECT id
			FROM `" . DB_PREFIX . "languages`
			WHERE (id_site = " . $Admin->GetSite() . ") AND (id = " . $lang_id . ")"
			)->single();
			
			if ( !$data )
			{
				continue;
			}
			
			$this->db->update( "languages" )->where( 'id', $lang_id )->set( "lang_order", $order );
		}
	
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}