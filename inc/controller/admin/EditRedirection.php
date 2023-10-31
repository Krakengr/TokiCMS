<?php defined('TOKICMS') or die('Hacking attempt...');

class EditRedirection extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-redirections' ) || !$Admin->Settings()::IsTrue( 'enable_redirect' ) )
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$Redir = GetRedir( $id, $Admin->GetSite() );

		if ( !$Redir )
			Redirect( $Admin->GetUrl( 'redirections' ) );
		
		$this->setVariable( 'Redir', $Redir );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-redirection' ) . ': "' . $Redir['title'] . '" | ' . $Admin->SiteName() );

		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		// Verify if the token is correct
		if ( !verify_token( 'editRedir' . $id ) )
			Redirect( $Admin->GetUrl( 'redirections' ) );
		
		$data = $_POST['redir'];

		//If we want to delete a blog, do it here
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete']  ) )
		{
			$this->db->delete( 'redirs' )->where( "id", $id )->run();

			//Redirect to the dashboard
			@header('Location: ' . $Admin->GetUrl( 'redirections' ) );
			exit;
		}
		
		//We can update the DB
		$dbarr = array(
			"title" 		=> $data['title'],
			"uri" 			=> $data['source-url'],
			"target" 		=> $data['target-url'],
			"when_matched" 	=> $data['when-matched'],
			"http_code" 	=> $data['add-http-code'],
			"exclude_logs" 	=> ( ( isset( $data['exclude-from-logs'] ) && !empty( $data['exclude-from-logs'] ) ) ? 1 : 0 ),
			"disable_redir" => ( ( isset( $data['disable'] ) && !empty( $data['disable'] ) ) ? 1 : 0 )
		);

		$this->db->update( 'redirs' )->where( 'id', $id )->set( $dbarr );

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( 'edit-redirection' . PS . 'id' . PS . $id ) );
		exit;
	}
}