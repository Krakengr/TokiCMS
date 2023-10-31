<?php defined('TOKICMS') or die('Hacking attempt...');

class AddUser extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-members' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'add-user' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'add-user' ) )
			return;
		
		//Check if we have an empty value
		if ( empty( $_POST['username'] ) || empty( $_POST['password'] ) || empty( $_POST['password2'] ) || empty( $_POST['email'] ) )
		{
			$Admin->SetAdminMessage( __( 'error-please-fill-all-the-required-fields' ) );
			return;
		}
		
		//We need a valid email
		if ( !Validate( $_POST['email'] ) )
		{
			$Admin->SetAdminMessage( __( 'error-please-enter-valid-email-address' ) );
			return;
		}
		
		//Check if the passwords match
		if ( !hash_equals( $_POST['password'], $_POST['password2'] ) )
		{
			$Admin->SetAdminMessage( __( 'the-password-and-confirmation-password-do-not-match' ) );
			return;
		}
		
		//Make sure the username is not something we don't want
		if ( in_array( $_POST['username'], RegisteredSlugs() ) )
		{
			$Admin->SetAdminMessage( __( 'add-user-username-error' ) );
			return;
		}
		
		if ( strlen( $_POST['password'] ) < 5 )
		{
			$Admin->SetAdminMessage( __( 'password-too-short' ) );
			return;
		}
		
		//Make sure the username is unique
		$u = $this->db->from( 
		null, 
		"SELECT id_member
		FROM `" . DB_PREFIX . USERS . "`
		WHERE (user_name = :name) AND (id_site = " . $Admin->GetSite() . ")",
		array( Sanitize( $_POST['username'], false ) => ':name' )
		)->single();

		if ( $u )
		{
			$Admin->SetAdminMessage( __( 'add-user-username-exists-error' ) );
			return;
		}

		$userHash = GenerateRandomKey( 8 );
		
		$password = Sanitize( $_POST['password'], false );
	
		$userPass = sha1( $password . $userHash );
		
		$username = Sanitize( $_POST['username'], false );
		
		//Create the user and redirect it there
		$dbarr = array(
			"id_group" 			=> $_POST['role'],
			"id_site" 			=> $Admin->GetSite(),
			"id_lang" 			=> $Admin->DefaultLang()['id'],
			"user_name" 		=> $username,
			"date_registered" 	=> time(),
			"passwd"			=> $userPass,
			"email_address" 	=> $_POST['email'],
			"is_activated" 		=> 1,
			"password_hash" 	=> $userHash
		);

		$id = $this->db->insert( USERS )->set( $dbarr, null, true );
	
		if ( !$id )
		{
			$Admin->SetAdminMessage( __( 'user-add-error' ) );
			return;
		}
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( 'edit-user' . PS . 'id' . PS . $id ) );
		exit;
	}
}