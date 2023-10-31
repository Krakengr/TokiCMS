<?php defined('TOKICMS') or die('Hacking attempt...');

class Login extends Controller {
	
	private $notifyMessage 	= null;
	private $disableButtons = false;
	private $hideForm 		= false;
	private $ip 			= null;
	private $userId 		= 0;
	
    public function process() 
	{		
		$this->setVariable( 'Lang', $this->lang );
		$this->BeforeLogin();
		
		$this->setVariable( 'notifyMessage', $this->notifyMessage );
		$this->setVariable( 'disableButtons', $this->disableButtons );
		$this->setVariable( 'hideForm', $this->hideForm );

		$this->view();
	}
	
	private function BeforeLogin()
	{
		//User is already logged in
		if ( $this->getVariable( 'AuthUser' ) || Settings::IsTrue( 'disable_user_login' ) )
		{
			@header('Location: ' . SITE_URL );
			exit;
		}
		
		$isValid = $this->getVariable("isValid");
		
		$this->userId = ( isset( $_SESSION['user_try_login'] ) ? (int) $_SESSION['user_try_login'] : 0 );
		
		//Get the IP
		$this->ip = GetRealIp();
		
		//Do we want someone to stop this user from trying too hard? Do it now
		if ( ( Settings::Get()['num_login_retries'] > 0 ) && isset( $_SESSION['failed_login'] ) && ( $_SESSION['failed_login'] >= Settings::Get()['num_login_retries'] ) )
		{
			//Do we have set a lockout time?
			if ( Settings::Get()['num_login_lockout_time'] > 0 )
			{
				if ( time() < ( $_SESSION['last_time_failed'] + Settings::Get()['num_login_lockout_time'] ) )
				{
					if ( Settings::IsTrue( 'notify_the_user_about_remaining_retries' ) )
					{
						$timeRemaining = ( ( $_SESSION['last_time_failed'] + Settings::Get()['num_login_lockout_time'] ) - time() );
						
						$timeinSeconds = ( ( $timeRemaining < 60 ) ? $timeRemaining : round( ( $timeRemaining / 60 ) ) );
							
						$this->notifyMessage = sprintf( __( 'failed-login-lock-message' ), $timeinSeconds ) . ' ' . ( ( $timeRemaining < 60 ) ? __( 'seconds' ) : __( 'minutes' ) ) ;
						
						$this->disableButtons = true;
						
						Log::Set( __( 'login-error' ), sprintf( __( 'too-many-failed-login-attempts-for-user' ), $this->userId ), null, 'system', $this->userId );
					}
					
					else
					{
						$this->notifyMessage = sprintf( __( 'could-not-login-error' ) );
					}
					
					return;
				}
				else
				{
					unset( $_SESSION['failed_login'], $_SESSION['last_time_failed'] );
				}
			}
			
			//If we didn't set a lockout time, we still have to reset the Session after 2 minutes
			else
			{
				if ( time() > ( $_SESSION['last_time_failed'] + 120 ) )
				{
					unset( $_SESSION['failed_login'], $_SESSION['last_time_failed'] );
				}
				
				//Let them know that we can't log them in right now
				else
				{
					$this->notifyMessage = __( 'could-not-login-error' );
					Log::Set( __( 'login-error' ), sprintf( __( 'too-many-failed-login-attempts-for-user' ), $this->userId ), null, 'system', $this->userId );
					//SysLogs( __( 'login-error' ), sprintf( __( 'too-many-failed-login-attempts-for-user' ), $this->userId ), '', $this->ip, 'system', $this->userId );
					return;
				}
			}
		}
		
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
			$pages = Settings::LegalPages();
			
			$code = CurrentLang()['lang']['code'];

			if ( isset( $pages['terms'] ) && isset( $pages['terms'][$code] ) && !empty( $pages['terms'][$code] ) && ( ( $privacySettings['show_required_terms_in'] == 'everywhere' ) || ( $privacySettings['show_required_terms_in'] == 'login-form' ) ) )
			{
				if ( !isset( $_POST['terms-of-service'] ) || empty( $_POST['terms-of-service'] ) )
				{
					echo '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (' . __( 'terms-of-service' ) . ')</p>';
					echo '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
					exit;
				}
			}
		}

		//Check if we want honeypot and/or captcha
		if ( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'login-form' ) )
		{
			//If we have honeypot enabled, then some fields should be empty
			if ( Settings::IsTrue( 'enable_honeypot' ) )
			{
				//Don't notify the user, quietly go to the main URL
				if ( !empty( $_POST['name'] ) || !empty( $_POST['email'] ) )
				{
					$message = sprintf( __( 'possible-spam-bot-login-message' ), $_SERVER['HTTP_USER_AGENT'], $this->ip );
					Log::Set( __( 'possible-spam-bot' ), $message, null, 'system' );
					//SysLogs( __( 'possible-spam-bot' ), $message, '', $this->ip, 'system' );
					
					@header('Location: ' . SITE_URL );
					exit;
				}
			}

			//Recaptcha
			if ( Settings::Get()['enable_recaptcha'] !== 'false' )
			{
				//V2
				if ( Settings::Get()['enable_recaptcha'] === 'google-recaptcha-v2' )
				{
					if ( !isset( $_POST['g-recaptcha-response'] ) || empty( $_POST['g-recaptcha-response'] ) )
					{
						$this->notifyMessage = __( 'error-please-fill-the-required-fields' ) . ' (captcha)';
						return;
					}

					$check = CheckCaptcha ( $_POST['g-recaptcha-response'] );
					
					if ( !$check )
					{
						$this->notifyMessage = __( 'error-please-fill-the-required-fields' ) . ' (captcha)';
						return;
					}
				}
				
				//V3
				elseif ( Settings::Get()['enable_recaptcha'] === 'google-recaptcha-v3' )
				{
					if ( !isset( $_POST['recaptcha_response'] ) || empty( $_POST['recaptcha_response'] ) )
					{
						$this->notifyMessage = __ ( 'error-please-fill-the-required-fields' ) . ' (captcha)';
						return;
					}
					
					$check = CheckCaptcha ( $_POST['recaptcha_response'] );
					
					if ( !$check )
					{
						$this->notifyMessage = __( 'error-please-fill-the-required-fields' ) . ' (captcha)';
						return;
					}
				}
			}
		}

		// Check if the username is empty
		if ( !isset( $_POST['uu'] ) || $_POST['uu'] == '' )
		{
			$this->notifyMessage = sprintf( __( 'failed-login-empty-values' ), __( 'username' ) );
			return;
		}
		
		// Don't forget the password
		if ( !isset( $_POST['up'] ) || $_POST['up'] == '' )
		{
			$this->notifyMessage = sprintf( __( 'failed-login-empty-values' ), __( 'password' ) );
			return;
		}
		
		//We can continue with the login
		$this->UserLogin();
	}
	
	private function UserLogin()
	{
		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		//Does this username exists?
		$user = UserByName( Sanitize( $_POST['uu'] ) );

		if ( !$user )
		{
			$this->notifyMessage = __( 'username-doesnt-exist' );
			return;
		}
		
		$this->userId = $user['id_member'];
		
		$_SESSION['user_try_login'] = $this->userId;
			
		//Check user's activation status
		if ( $user['is_activated'] == 0 )
		{
			$this->notifyMessage = __( 'your-account-is-not-activated' );
			return;
		}
		
		$query = "SELECT b.id_ban, b.ip, b.id_member, g.expire_time, g.deny_access, g.cannot_login, g.reason
		FROM `" . DB_PREFIX . "ban_items` AS b
		INNER JOIN `" . DB_PREFIX . "ban_groups` as g ON g.id_ban_group = b.id_ban_group
		WHERE (g.id_site = " . SITE_ID . ") AND (b.ip = :ip OR b.id_member = " . $this->userId . ")";
		
		$binds = array( $this->ip => ':ip' );
		
		//Query: ban
		$x = $this->db->from( null, $query, $binds )->single();
		
		if ( !empty( $x ) )
		{
			if ( ( !empty( $x['deny_access'] ) || !empty( $x['cannot_login'] ) )
					&& ( empty( $x['expire_time'] ) || ( $x['expire_time'] > time () ) )
			)
			{
				if ( !empty( $x['id_member'] ) )
				{
					$message  = __( 'your-account-has-been-banned' );
					$message .= '<br />';
					$message .= sprintf( __( 'reason-s' ), $x['reason'] );
				}
				
				else
				{
					$message  = __( 'your-ip-has-been-banned' );
					$message .= '<br />';
					$message .= sprintf( __( 'reason-s' ), $x['reason'] );
				}
				
					$this->notifyMessage = $message;
					$this->hideForm = true;
					return;
			}
		}
				
		$tempNum = $tempNum2 = null;
		
		//If this filed is not empty, things are getting pretty serious... 
		if ( !empty( $user['passwd_flood'] ) && ( strpos( $user['passwd_flood'], '::' ) !== false ) )
		{
			//Even if the admin don't want to have a security limit, let's add one
			$userNumRetries = ( ( Settings::Get()['num_login_retries'] > 0 ) ? ( (int) Settings::Get()['num_login_retries'] + 1 ) : 5 );
			
			$temp = explode( '::', $user['passwd_flood'] );
			
			if ( !empty( $temp ) )
			{
				$tempTime = $temp['0'];
				$tempNum = $temp['1'];
				$tempNum2 = ( isset( $temp['2'] ) ? $temp['2'] : null );
				
				//"This is where it ends, Frost. I can't allow you to go any further"
				if ( $tempNum2 && is_numeric( $tempNum2 ) && ( $tempNum2 >= 3 ) )
				{
					//Ban this IP
					$dbarr = array(
						"id_site" 		=> SITE_ID,
						"name"    		=> __( 'too-many-requests' ),
						"ban_time" 		=> time(),
						"deny_access" 	=> 1,
						"reason" 		=> __( 'youve-exceeded-the-number-of-login-attempts' ),
						"notes" 		=> sprintf( __( 'too-many-login-attempts-from-this-ip' ), $this->ip )
					);
					
					$ban = $this->db->insert( 'ban_groups' )->set( $dbarr );
					
					if ( $ban )
					{
						$banId = $this->db->lastId();
					
						if ( !empty( $banId ) )
						{
							$dbarr = array(
								"id_ban_group" 	=> $banId,
								"ip"    		=> $this->ip
							);
							
							$this->db->insert( 'ban_items' )->set( $dbarr );
						}
						
						header("HTTP/1.1 429 Too Many Requests");
						echo "You've exceeded the number of login attempts. We've blocked your IP address.";
						exit();
					}
				}
				
				//You can't continue, for now at least...
				if ( $tempNum >= $userNumRetries )
				{
					$tempNum2 = ( ( $tempNum2 && is_numeric( $tempNum2 ) ) ? $tempNum2 : 1 );
					
					//Increase the time limit every time they try to login
					if ( ( time() < ( $tempTime + ( $tempNum2 * 180 ) ) ) )
					{
						$this->notifyMessage = __( 'could-not-login-error' );
						
						Log::Set( __( 'login-error' ), sprintf( __( 'too-many-failed-login-attempts-for-user' ), $this->userId ), null, 'system', $this->userId );

						return;
					}
					
					//Reset the number of the attempts, but keep the number of notifies
					else
					{
						$tempNum = 0;
						
						$flood = time() . '::' . $tempNum;
			
						$flood = $flood . ( ( $tempNum2 && is_numeric( $tempNum2 ) ) ? '::' . $tempNum2 : 1 );

						$this->db->update( USERS )->where( 'id_member', $this->userId )->set( "passwd_flood", $flood );
					}
				}
			}			
		}
		
		//Ok, we have a valid user, but should we log the user in while the site is offline?
		//Right now only the admin(s) can have access.
		if ( Settings::IsTrue( 'enable_maintenance', 'site' ) && ( $user['id_group'] != 1 ) )
		{
			//Query: permissions
			$x = $this->db->from( null, "
			SELECT group_permissions
			FROM `" . DB_PREFIX . "membergroups`
			WHERE (id_group = " . $user['id_group'] . ")"
			)->single();

			if ( !$x || empty( $x['group_permissions'] ) )
			{
				$this->notifyMessage = __( 'login-unavailable-site-maintenance-mode' );
				return;
			}
			
			$gPer = Json( $x['group_permissions'] );

			if ( empty( $gPer ) || !in_array( 'admin-site', $gPer ) )
			{
				$this->notifyMessage = __( 'login-unavailable-site-maintenance-mode' );
				return;
			}
		}

		$pass = CheckUserPass( $user, Sanitize( $_POST['up'], false ) );

		if ( !$pass )
		{
			$this->notifyMessage = __( 'password-incorrect' );
			$_SESSION['failed_login'] = isset( $_SESSION['failed_login'] ) ? ( $_SESSION['failed_login'] + 1 ) : 1;
			$_SESSION['last_time_failed'] = time();
			
			$flood = time() . '::' . ( $tempNum ? ( $tempNum + 1 ) : 1 );
			
			$flood = $flood . ( ( $tempNum2 && is_numeric( $tempNum2 ) ) ? '::' . ( $tempNum2 + 1 ) : 1 );
			
			//UPDATE user's passwd flood
			$this->db->update( USERS )->where( 'id_member', $this->userId )->set( "passwd_flood", $flood );
			
			return;
		}

		// Create new session
		session_regenerate_id();

		//Set the user id in Session, right now has no purpose but who knows
		$_SESSION['id_member'] = $user['id_member'];
			
		//Set a secure thumbprint in session. Right now this has no purpose
		$_SESSION['thumbprint'] = nonce( session_id() . 'thumbprint', $user['password_hash'], EXPIRE_CACHE );
			
		// Get Current time
		$currentTime = time();

		// Set Auth Cookies if 'Remember Me' is checked
		if ( isset( $_POST['remember_me'] ) && !empty( $_POST['remember_me'] ) ) 
		{
			$currentDate = date("Y-m-d H:i:s", $currentTime);
				
			$selector = base64_encode(RandomBytes(9));
			$authenticator = RandomBytes(33);

			// Set Cookie expiration for 1 month
			$cookieExpiration = $currentTime + (30 * 24 * 60 * 60);  //for 1 month

			// Set the Cookie
			$cookie = new \Delight\Cookie\Cookie( 'Auth' );
			$cookie->setValue( $selector . ':' . base64_encode( $authenticator ) );
			$cookie->setMaxAge( $cookieExpiration );
			// $cookie->setExpiryTime(time() + 60 * 60 * 24);
			$cookie->setPath( '/' );
			$cookie->setDomain( GetTheHostName ( SITE_URL ) );
			$cookie->setHttpOnly( true );
			$cookie->setSecureOnly( true );
			$cookie->setSameSiteRestriction( 'Strict' );
			$cookie->save();
			
			/*setcookie(
				'Auth',
				$selector.':'.base64_encode($authenticator),
				$cookieExpiration,
				'/',
				GetTheHostName ( SITE_URL ),
				true, // TLS-only
				true  // http-only
			);*/
			
			//Delete any old keys from this user
			$this->db->delete( 'auth_tokens' )->where( 'userid', $user['id_member'] )->where( "expires", 'CURDATE()', true, '<' )->run();

			//Add a fresh key into the DB
			$dbarr = array(
				"selector" 	=> $selector,
				"token"    	=> hash( 'sha256', $authenticator ),
				"userid" 	=> $user['id_member'],
				"expires" 	=> date('Y-m-d\TH:i:s', $cookieExpiration )
            );
            
			$this->db->insert( 'auth_tokens' )->set( $dbarr );
		} 

		else 
		{
			setcookie( 'Auth', '', time() - (86400 * 530), "/" ); // empty value and old timestamp
			unset ( $_COOKIE['Auth'] );
		}
			
		//UPDATE user's last visit time and clear the passwd flood
		$dbarr = array(
            "last_visit" => $currentTime,
			"last_login" => $currentTime,
			"passwd_flood" => ""
        );

		$this->db->update( USERS )->where( 'id_member', $this->userId )->set( $dbarr );
		
		//Free all session variables
		session_unset();
		session_destroy();

		@header('Location: ' . SITE_URL );
		exit;
	}
}