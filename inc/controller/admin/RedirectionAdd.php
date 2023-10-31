<?php defined('TOKICMS') or die('Hacking attempt...');

class RedirectionAdd extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-redirections' ) || !$Admin->Settings()::IsTrue( 'enable_redirect' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'add-redirection' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !verify_token( 'add-redirection' ) )
			return;
		
		//Get the needed POST values
		$data = $_POST['redir'];
		
		$dbarr = array(
			"id_site" 		=> $Admin->GetSite(),
			"title" 		=> $data['title'],
			"uri" 			=> $data['source-url'],
			"target" 		=> $data['target-url'],
			"when_matched" 	=> $data['when-matched'],
			"http_code" 	=> $data['add-http-code'],
			"added_time" 	=> time(),
			"exclude_logs" 	=> ( isset( $data['exclude-from-logs'] ) ? 1 : 0 )
		);
		
		$id = $this->db->insert( 'redirs' )->set( $dbarr, null, true );

		$q = "INSERT INTO " . DB_PREFIX . "redirs ( id_site, title, uri, target, when_matched, http_code, added_time, exclude_logs ) VALUES (:id_site, :title, :uri, :target, :when_matched, :http_code, :added_time, :exclude_logs )";

		if ( $id )
		{
			$Admin->DeleteSettingsCacheSite( 'settings' );
			
			Redirect( $Admin->GetUrl( 'edit-redirection' . PS . 'id' . PS . $id ) );
		}

		else
		{
			$Admin->SetAdminMessage( __( 'an-error-occurred' ) );
			return;
		}
	}
}