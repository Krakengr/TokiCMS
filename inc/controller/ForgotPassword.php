<?php defined('TOKICMS') or die('Hacking attempt...');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'Exception.php';
require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'PHPMailer.php';
require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'SMTP.php';

class ForgotPassword extends Controller {
	
	private $notifyMessage 		= null;
	private $disableButtons 	= false;
	private $notifyType 		= 'error';
	private $ip 				= null;
	private $user 				= null;
	private $hash 				= null;
	
    public function process() 
	{		
		$this->setVariable( 'Lang', $this->lang );
		$this->Run();
		
		$this->setVariable( 'notifyMessage', $this->notifyMessage );
		$this->setVariable( 'disableButtons', $this->disableButtons );
		$this->setVariable( 'notifyType', $this->notifyType );
		
		$this->view();
	}
	
	public function Run()
	{
		//User is already logged in
		if ( $this->getVariable( 'AuthUser' ) )
		{
			@header('Location: ' . SITE_URL );
			exit;
		}
		
		global $CurrentLang;
		
		$isValid = $this->getVariable("isValid");

		//Grab the IP
		$this->ip = GetRealIp();
	
		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Check if the token is correct
		if ( !$isValid )
			return;
		
		if( !isset( $_SESSION['forgot_attempt'] ) )
			$_SESSION['forgot_attempt'] = array();
		
		if ( isset( $_SESSION['forgot_attempt']['time'] ) && ( $_SESSION['forgot_attempt']['time'] + 30 ) > time() )
		{
			$this->notifyMessage = __( 'forgot-password-too-many-retries-error' );
			return;
		}
		
		if ( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'lost-password-form' ) )
		{
			//If we have honeypot enabled, then some fields should be empty	
			if ( Settings::IsTrue( 'enable_honeypot' ) )
			{
				//Don't notify the user, quietly go to the main URL
				if ( !empty( $_POST['name'] ) || !empty( $_POST['email'] ) )
				{
					$message = sprintf( __( 'possible-spam-bot-recovery-message' ), $_SERVER['HTTP_USER_AGENT'], $this->ip );
					
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
		/*
		
		//If we have honeypot enabled, then some fields should be empty
		if ( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'registration-form' ) )
		{
			//Check if we want honeypot
			if ( Settings::IsTrue( 'enable_honeypot' ) )
			{
				//Don't notify the user, quietly go to the main URL
				if ( !empty( $_POST['name'] ) || !empty( $_POST['email'] ) )
				{
					$message = sprintf( __( 'possible-spam-bot-register-message' ), $_SERVER['HTTP_USER_AGENT'], $this->ip );
					
					SysLogs( __( 'possible-spam-bot' ), $message, __( 'reset-password-form' ), $this->ip, 'system' );
					
					@header('Location: ' . SITE_URL );
					exit;
				}
			}
		}*/
		
		//Check if we have an empty value
		if ( empty( $_POST['uu'] ) )
		{
			$this->notifyMessage = __( 'error-please-fill-all-the-required-fields' );
			return;
		}

		//Do we have an email?
		if ( Validate( $_POST['uu'] ) )
		{
			$this->user = UserByEmail( $_POST['uu'] );
			$this->notifyMessage = __( 'email-doesnt-exist' );
		}
		
		else
		{
			$this->user = UserByName( Sanitize( $_POST['uu'] ) );
			$this->notifyMessage = __( 'username-doesnt-exist' );
		}
		
		if ( !$this->user )
		{
			$_SESSION['forgot_attempt']['time'] = time();
			$_SESSION['forgot_attempt']['attempts'] = ( isset( $_SESSION['forgot_attempt']['attempts'] ) ? ( $_SESSION['forgot_attempt']['attempts'] + 1 ) : 1 );
			
			return;
		}

		//Check user's activation status
		if ( $this->user['is_activated'] == 0 )
		{
			$this->notifyMessage = __( 'your-account-is-not-activated' ); //your-account-has-been-banned
			return;
		}

		//Make sure we don't have any previous request
		$emExists = $this->db->from( null, "
		SELECT *
		FROM `" . DB_PREFIX . "password_reset`
		WHERE (user_id = " . $this->user['id_member'] . ")"
		)->single();

		if ( $emExists )
		{
			$expire = ( $emExists["reset_time"] + 21600 );//valid for 6 hours
			
			if ( time() > $expire )
			{
				$this->notifyMessage = __( 'you-already-requested-to-reset-your-password' );
				return;
			}
			
			else
			{
				$this->db->delete( 'password_reset' )->where( 'user_id', $this->user['id_member'] )->run();
			}
		}

		$this->hash = strtolower( GenerateRandomKey( 8 ) );
		
		//Create the reset value
		$dbarr = array(
			"user_id" 		=> $this->user['id_member'],
			"reset_hash"    => $this->hash,
			"reset_time" 	=> time()
        );
        
		$ok = $this->db->insert( 'password_reset' )->set( $dbarr );

		if ( $ok && $this->Email() )
		{
			$this->disableButtons = true;
			$this->notifyType = 'info';
			$this->notifyMessage = __( 'a-mail-has-been-sent-to-your-email-address' );
			return;
		}
		
		else
		{
			$this->notifyMessage = __( 'an-error-happened' );
			return;
		}
	}
	
	private function Email()
	{
		// Default sender address
		$from_name = $this->lang['data']['site_name'];
		$from_email = Settings::Get()['website_email'];
		
		$emailHtml = __( 'reset-password-email-message' ) . PHP_EOL;
		$emailHtml .= '<a href="' . SITE_URL . 'recovery/' . $this->hash . '/" class="btn">' . __( 'reset-my-password' ) . '</a>'; 
		
		//Send the email
		$mail = new PHPMailer;
		$mail->SMTPDebug = 0;
		$mail->isSendmail();
		$mail->CharSet = 'UTF-8'; 
		$mail->setFrom( $from_email, $from_name );
		$mail->addAddress( $this->user['email_address'] );
		$mail->addReplyTo( $this->user['email_address'], $this->user['user_name'] );
			
		$mail->isHTML(true);                     
		$mail->Subject = __('reset-password');
		$mail->Body    = $emailHtml;
		$mail->AltBody = $emailHtml;
		
		if ( !$mail->send() )
		{
			Log::Set( __( 'message-could-not-be-sent' ), $mail->ErrorInfo, $_POST, 'system' );
			return false;
		}
		
		return true;
			
		/*
		// Do a little spring cleaning
		$to = trim( preg_replace( '#[\n\r]+#s', '', $this->user['email_address'] ) );
		$subject = trim( preg_replace( '#[\n\r]+#s', '', __('forgot-password') ) );
		$from_email = trim( preg_replace( '#[\n\r:]+#s', '', $from_email ) );
		$from_name = trim( preg_replace( '#[\n\r:]+#s', '', str_replace( '"', '', $from_name ) ) );

		// Set up some headers to take advantage of UTF-8
		$from = "=?UTF-8?B?" . base64_encode( $from_name ) ."?=".' <' . $from_email . '>';
		$subject = "=?UTF-8?B?".base64_encode( $subject ) . "?=";

		$headers = 'From: ' . $from . "\r\n".'Date: ' . gmdate( 'r' ) . "\r\n".'MIME-Version: 1.0'."\r\n".'Content-transfer-encoding: 8bit'."\r\n".'Content-type: text/html; charset=utf-8'."\r\n".'X-Mailer: TokiCMS Mailer';
		
		$emailHtml = __( 'reset-password-email-message' ) . PHP_EOL;
		
		$emailHtml .= '<a href="' . SITE_URL . 'recovery/' . $this->hash . '/" class="btn">' . __( 'reset-my-password' ) . '</a>'; 

		// Make sure all linebreaks are CRLF in message (and strip out any NULL bytes)
		$message = str_replace( array( "\n", "\0" ), array( "\r\n", '' ), str_replace( array( "\r\n", "\r" ), "\n", $emailHtml ) );

		// Change the linebreaks used in the headers according to OS
		if ( strtoupper( substr( PHP_OS, 0, 3 ) ) != 'WIN' )
			$headers = str_replace( "\r\n", "\n", $headers );
		
		return mail( $to, $subject, $message, $headers )*/
	}
}