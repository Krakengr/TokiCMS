<?php defined('TOKICMS') or die('Hacking attempt...');

class AddLink extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) && !$Admin->Settings()::IsTrue( 'enable_link_manager' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'add-link' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'add-link' ) )
			return;
		
		//We can't add a new site if we don't have a name and a URL
		if ( empty( $_POST['name'] ) || empty( $_POST['url'] ) )
		{
			$Admin->SetAdminMessage( __( 'empty-form-error' ) );
			return;
		}
		
		//Check if this url is valid
		if ( empty( $_POST['url'] ) || !Validate( $_POST['url'], 'url' ) )
		{
			$Admin->SetAdminMessage( __( 'you-should-enter-a-valid-url-address' ) );
			return;
		}
		
		//Make sure we have a trailing slash
		$url = LastTrailCheck( $_POST['url'] );

		// Check it this URL exists in the DB
		$x = $this->db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "links`
		WHERE (url = :url) AND (id_site = " . $Admin->GetSite() . ")",
		array( $url => ':url' )
		)->single();

		if ( $x )
		{
			$Admin->SetAdminMessage( __( 'link-already-exists' ) );
			return;
		}
		
		$shortLink = ( isset( $_POST['short-link'] ) ? generate_short_key( $Admin->GetSite() ) : '' );
		
		$s = array(
			'no_follow' 	=> ( isset( $_POST['no-follow'] ) ? true : false ),
			'sponsored' 	=> ( isset( $_POST['sponsored'] ) ? true : false ),
			'add_rel' 		=> array(),
			'redirection' 	=> ( isset( $_POST['redirection'] ) ? Sanitize ( $_POST['redirection'], false ) : '' )
		);
		
		//INSERT the new link into the DB
		$dbarr = array(
			"id_site"		=> $Admin->GetSite(),
			"added_time"	=> time(),
			"short_link"	=> $shortLink,
			"title"			=> $_POST['name'],
			"url"			=> $url,
			"descr"			=> $_POST['description'],
			"link_data"		=> json_encode( $s ),
			"id_member"		=> $Admin->UserID()
		);

		$id = $this->db->insert( 'links' )->set( $dbarr, null, true );	
		
		if ( $id )
		{
			Redirect( $Admin->GetUrl( 'edit-link' . PS . 'id' . PS . $id ) );
		}
		
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'links' ) );
		}
		
		exit;
	}
}