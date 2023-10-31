<?php defined('TOKICMS') or die('Hacking attempt...');

class Register extends Controller {
	
	private $notifyMessage = null;
	private $disableButtons = false;
	private $notifyType = 'error';
	private $ip = null;
	
    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->BeforeRegister();
		
		$this->setVariable( 'notifyMessage', $this->notifyMessage );
		$this->setVariable( 'disableButtons', $this->disableButtons );
		$this->setVariable( 'notifyType', $this->notifyType );

		$this->view();
	}
	
	private function BeforeRegister()
	{
		//User is already logged in
		if ( $this->getVariable( 'AuthUser' ) )
		{
			@header('Location: ' . SITE_URL );
			exit;
		}

		$isValid = $this->getVariable("isValid");

		//Grab the IP
		$this->ip = GetRealIp();

		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		// Check if the token is correct
		if ( !$isValid )
			return;
		
		//Sorry, we don't allow registrations
		if ( !Settings::IsTrue( 'enable_registration', 'site' ) )
		{
			return;
		}
		
		//Does someone trying to register while we are under maintenance mode?
		if ( Settings::IsTrue( 'enable_maintenance', 'site' ) )
		{
			return;
		}
		
		//Check if the user agreed with the privacy policy
		if ( Settings::IsTrue( 'require_accept_privacy_policy' ) )
		{
			if ( !isset( $_POST['privacy-policy-agreement'] ) || empty( $_POST['privacy-policy-agreement'] ) )
			{
				$message = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (' . __( 'privacy-policy' ) . ')</p>';
				$message .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
				
				$this->notifyMessage = $message;
				return;
			}
		}
		
		//Check if the user agreed with the registration agreement
		if ( Settings::IsTrue( 'require_accept_reg_agreement' ) )
		{
			if ( !isset( $_POST['registration-agreement'] ) || empty( $_POST['registration-agreement'] ) )
			{
				$message = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (' . __( 'registration-agreement' ) . ')</p>';
				$message .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
				
				$this->notifyMessage = $message;
				return;
			}
		}
		
		//Check if we require term of service Agreement 
		$privacySettings = Settings::PrivacySettings();
		
		if ( !empty( $privacySettings ) && isset( $privacySettings['require_users_agree_terms_of_service'] ) && isset( $privacySettings['show_required_terms_in'] ) && $privacySettings['require_users_agree_terms_of_service'] )
		{
			if ( ( $privacySettings['show_required_terms_in'] == 'everywhere' ) || ( $privacySettings['show_required_terms_in'] == 'registration-form' ) )
			{
				if ( !isset( $_POST['terms-of-service'] ) || empty( $_POST['terms-of-service'] ) )
				{
					$message = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (' . __( 'terms-of-service' ) . ')</p>';
					$message .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
					
					$this->notifyMessage = $message;
					return;
				}
			}
		}

		$registeredSlugs = $this->getVariable( 'RegisteredSlugs' );
		
		if ( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'registration-form' ) )
		{
			//If we have honeypot enabled, then some fields should be empty	
			if ( Settings::IsTrue( 'enable_honeypot' ) )
			{
				//Don't notify the user, quietly go to the main URL
				if ( !empty( $_POST['name'] ) || !empty( $_POST['email'] ) )
				{
					$message = sprintf( __( 'possible-spam-bot-register-message' ), $_SERVER['HTTP_USER_AGENT'], $this->ip );
					
					//SysLogs( __( 'possible-spam-bot' ), $message, '', $this->ip, 'system' );
					
					Log::Set( __( 'possible-spam-bot' ), $message, $_POST, 'system' );
					
					@header('Location: ' . SITE_URL );
					exit;
				}
			}
			
			//Recaptcha
			if ( Settings::Get()['enable_recaptcha'] != 'false' )
			{
				if ( !isset( $_POST['g-recaptcha-response'] ) || empty( $_POST['g-recaptcha-response'] ) )
				{
					$this->notifyMessage = __ ( 'error-please-fill-the-required-fields' ) . ' (captcha)';
					return;
				}
				
				//V2
				if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v2' )
				{
					$check = CheckCaptcha ( $_POST['g-recaptcha-response'] );
					
					if ( !$check )
					{
						$this->notifyMessage = __( 'error-please-fill-the-required-fields' ) . ' (captcha)';
						return;
					}
				}
				
				//V3
				else
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
		
		//Check if we have an empty value
		if ( empty( $_POST['uu'] ) || empty( $_POST['up'] ) || empty( $_POST['up2'] ) || empty( $_POST['um'] ) )
		{
			$this->notifyMessage = __( 'error-please-fill-all-the-required-fields' );
			return;
		}
		
		//We need a valid email
		if ( !Validate( $_POST['um'] ) )
		{
			$this->notifyMessage = __( 'error-please-enter-valid-email-address' );
			return;
		}
		
		//We don't want a small password
		if ( strlen( $_POST['up'] ) < 5 )
		{
			$this->notifyMessage = __( 'password-too-short' );
			return;
		}
		
		//We don't want a small or big username
		if ( ( strlen( $_POST['uu'] ) < 3 ) || ( strlen( $_POST['uu'] ) > 15 ) )
		{
			$this->notifyMessage = __( 'registration-username-bad' );
			return;
		}

		//Check if the passwords match
		if ( !hash_equals( $_POST['up'], $_POST['up2'] ) )
		{
			$this->notifyMessage = __( 'the-password-and-confirmation-password-do-not-match' );
			return;
		}
		
		//Make sure the username is not something we don't want
		if ( in_array( $_POST['uu'], $registeredSlugs ) )
		{
			$this->notifyMessage = __( 'add-user-username-error' );
			return;
		}
		
		//Make sure the username is unique
		$unExists = UserByName( Sanitize( $_POST['uu'] ) );

		if ( $unExists )
		{
			$this->notifyMessage = __( 'add-user-username-exists-error' );
			return;
		}
		
		//Make sure the email is unique
		$unExists = UserByEmail( Sanitize( $_POST['um'] ) );

		if ( $emExists )
		{
			$this->notifyMessage = __( 'add-user-email-exists-error' );
			return;
		}
		
		//We can continue with the registration
		$userHash = GenerateRandomKey( 8 );
		
		$password = Sanitize( $_POST['up'], false );
	
		$userPass = sha1( $password . $userHash );
		
		$username = Sanitize( $_POST['uu'], false );
		
		$activated = ( ( Settings::Get()['registration_method'] == 'immediate' ) ? 1 : 0 );
		
		//Create the user
		$dbarr = array(
			"id_group" 			=> 4,
			"user_name" 		=> $username,
			"date_registered" 	=> time(),
			"id_lang" 			=> $this->lang['lang']['id'],
			"passwd" 			=> $userPass,
			"email_address" 	=> $_POST['um'],
			"is_activated" 		=> $activated,
			"id_site" 			=> SITE_ID,
			"password_hash" 	=> $userHash,
			"member_ip" 		=> $this->ip
        );
            
		$uId = $this->db->insert( USERS )->set( $dbarr );

		if ( $uId )
		{
			$this->disableButtons = true;
			$this->notifyType = 'info';
			
			if ( Settings::Get()['registration_method'] == 'email' )
			{
				$this->notifyMessage = __( 'registration-email-activation-link-message' );
				return;
			}
			
			elseif ( Settings::Get()['registration_method'] == 'admin' )
			{
				$this->notifyMessage = __( 'registration-admin-activation-message' );
				return;
			}
			else
			{
				$this->notifyMessage = sprintf( __( 'registration-account-has-been-activated-message' ), SITE_URL . 'login/' );
				return;
			}
		}
		
		else
		{
			$this->notifyMessage = __( 'an-error-happened-refresh-page' );
			return;
		}
	}
}