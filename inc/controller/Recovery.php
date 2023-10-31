<?php defined('TOKICMS') or die('Hacking attempt...');

class Recovery extends Controller {
	
	private $notifyMessage = null;
	private $disableButtons = false;
	private $notifyType = 'error';
	
	public function process() 
	{		
		$this->setVariable( 'Lang', $this->lang );
		$this->Run();
		
		$this->setVariable( 'notifyMessage', $this->notifyMessage );
		$this->setVariable( 'disableButtons', $this->disableButtons );
		$this->setVariable( 'notifyType', $this->notifyType );
		
		$this->view();
	}
	
	private function Run()
	{
		//User is already logged in?
		if ( $this->getVariable( 'AuthUser' ) )
		{
			@header('Location: ' . SITE_URL );
			exit;
		}
		
		$key = Sanitize( Router::GetVariable( 'slug' ) );

		if ( empty( $key ) )
		{
			@header('Location: ' . SITE_URL );
			exit;
		}

		//Check if the key is valid
		$query = "SELECT user_id, reset_time
		FROM `" . DB_PREFIX . "password_reset`
		WHERE (reset_hash = :hash)";
		
		$binds = array( $key => ':hash' );
		
		//Query: reset key
		$emExists = $this->db->from( null, $query, $binds )->single();

		if ( !$emExists )
		{
			@header('Location: ' . SITE_URL );
			exit;
		}
		
		if ( time() > ( $emExists["reset_time"] + 21600 ) )
		{
			$this->disableButtons = true;
			$this->notifyMessage = __( 'recovery-key-is-invalid' );
			//Delete the reset data
			$this->db->delete( 'password_reset' )->where( 'user_id', $emExists["user_id"] )->run();
			return;
		}
	
		$isValid = $this->getVariable("isValid");
		
		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Check if the token is correct
		if ( !$isValid )
			return;
		
		//Check if we require term of service Agreement 
		$privacySettings = Settings::PrivacySettings();
		
		if ( !empty( $privacySettings ) && isset( $privacySettings['require_users_agree_terms_of_service'] ) && isset( $privacySettings['show_required_terms_in'] ) && $privacySettings['require_users_agree_terms_of_service'] )
		{
			if ( ( $privacySettings['show_required_terms_in'] == 'everywhere' ) || ( $privacySettings['show_required_terms_in'] == 'lost-password-form' ) )
			{
				if ( !isset( $_POST['terms-of-service'] ) || empty( $_POST['terms-of-service'] ) )
				{
					echo '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (' . __( 'terms-of-service' ) . ')</p>';
					echo '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
					exit;
				}
			}
		}
		
		//If we have honeypot enabled, then some fields should be empty
		if ( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'registration-form' ) )
		{
			//Check if we want honeypot
			if ( Settings::IsTrue( 'enable_honeypot' ) )
			{
				//Don't notify the user, quietly go to the main URL
				if ( !empty( $_POST['name'] ) || !empty( $_POST['email'] ) )
				{
					$message = sprintf( __( 'possible-spam-bot-recovery-message' ), $_SERVER['HTTP_USER_AGENT'], $this->ip );
					
					//SysLogs( __( 'possible-spam-bot' ), $message, __( 'recover-password-form' ), $this->ip, 'system' );
					Log::Set( __( 'possible-spam-bot' ), $message . __( 'recover-password-form' ), $_POST, 'system' );
					
					@header('Location: ' . SITE_URL );
					exit;
				}
			}
		}
		
		//Check if we have an empty value
		if ( empty( $_POST['up'] ) || empty( $_POST['up2'] ) )
		{
			$this->notifyMessage = __( 'error-please-fill-all-the-required-fields' );
			return;
		}
		
		if ( strlen( $_POST['up'] ) < 5 )
		{
			$Admin->notifyMessage = __( 'password-too-short' );
			return;
		}
		
		if ( !hash_equals( $_POST['up'], $_POST['up2'] ) )
		{
			$this->notifyMessage = __( 'the-password-and-confirmation-password-do-not-match' );
			return;
		}
		
		$userHash = GenerateRandomKey( 8 );
		
		$password = Sanitize( $_POST['up'], false );
	
		$userPass = sha1( $password . $userHash );
		
		//UPDATE user's password
		$dbarr = array(
            "password_hash" => $userHash,
			"passwd" 		=> $userPass
        );

		$q = $this->db->update( USERS )->where( 'id_member', $emExists["user_id"] )->set( $dbarr );

		if ( $q )
		{
			$this->disableButtons = true;
			$this->notifyMessage = sprintf( __( 'your-password-has-been-changed' ), SITE_URL . 'login/' );

			//It's time to delete the reset data
			$this->db->delete( 'password_reset' )->where( 'user_id', $emExists["user_id"] )->run();
		}
		else
		{
			$this->notifyMessage = __( 'an-error-happened' );
			return;
		}
	}
}