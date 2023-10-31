<?php defined('TOKICMS') or die('Hacking attempt...');

class EditLink extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $Link;
		
		if ( !IsAllowedTo( 'admin-site' ) && !$Admin->Settings()::IsTrue( 'enable_link_manager' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		$id = (int) Router::GetVariable( 'key' );

		if ( !$Link )
		{
			$Admin->SetErrorMessage( __( 'nothing-found' ), 'warning' );
			Redirect( $Admin->GetUrl( 'links' ) );
		}
		
		Theme::SetVariable( 'headerTitle', __( 'edit-link' ) . ': "' . $Link['title'] . '" | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'edit-link' ) )
			return;
		
		//Check if this url is valid
		if ( empty( $_POST['url'] ) || !Validate( $_POST['url'], 'url' ) )
		{
			$Admin->SetAdminMessage( __( 'you-should-enter-a-valid-url-address' ) );
			return;
		}
		
		//Make sure we have a trailing slash
		$url = LastTrailCheck( $_POST['url'] );
		
		if ( isset( $_POST['delete'] ) )
		{
			//Delete the link
			$this->db->delete( 'links' )->where( "id", $id )->run();
				
			Redirect( $Admin->GetUrl( 'links' ) );
		}
		
		$s = array(
			'no_follow' 	=> ( isset( $_POST['no-follow'] ) ? true : false ),
			'sponsored' 	=> ( isset( $_POST['sponsored'] ) ? true : false ),
			'add_rel' 		=> ( ( isset( $_POST['add_rel'] ) && is_array( $_POST['add_rel'] ) ) ? array_values( $_POST['add_rel'] ) : array() ),
			'redirection' 	=> ( isset( $_POST['redirection'] ) ? Sanitize ( $_POST['redirection'], false ) : '' )
		);
		
		$active = ( isset( $_POST['disable-link'] ) ? 'inactive' : 'active' );
		
		$dbarr = array(
			"title" 	=> $_POST['title'],
			"url" 		=> $url,
			"descr" 	=> $_POST['description'],
			"link_data" => json_encode( $s ),
			"status" 	=> $active
		);

		$this->db->update( 'links' )->where( 'id', $id )->set( $dbarr );
		
		Redirect( $Admin->GetUrl( 'edit-link' . PS . 'id' . PS . $id ) );
		exit;
	}
}