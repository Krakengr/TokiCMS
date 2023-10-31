<?php defined('TOKICMS') or die('Hacking attempt...');

class Logout extends Controller {
	
    public function process() 
	{		
		$this->setVariable( 'Lang', $this->lang );
		$this->UserLogout();
		$this->view();
	}
	
	public function UserLogout()
	{
		$AuthUser = $this->getVariable( 'AuthUser' );

		if ( !empty( $AuthUser ) )
		{
			//Logout the user from every system by deleting all the tokens
			$this->db->delete( 'auth_tokens' )->where( 'userid', $AuthUser['id_member'] )->run();
		}
		
		setcookie('Auth', '', time() - (86400 * 530), "/");
		//session_unset();
		session_destroy();
		$_SESSION = array();
		
		@header('Location: ' . SITE_URL );
		exit;
	}
}