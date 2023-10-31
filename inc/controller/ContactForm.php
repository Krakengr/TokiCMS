<?php defined('TOKICMS') or die('Hacking attempt...');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'Exception.php';
require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'PHPMailer.php';
require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'SMTP.php';

class ContactForm extends Controller {
	
	private $ip;
	private $responseMessage	= '';
	private $name 				= '';
	private $email 				= '';
	private $message 			= '';
	private $subject 			= '';
	private $type 				= 'inbox';

    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || empty( $_POST ) )
		{
			@header('HTTP/1.1 404 Not Found');

			//Redirect to the home page
			@header('Location: ' . SITE_URL );
			exit;
		}
		
		//Include the generic file for messages
		Router::SetVariable( 'includeFile', INC_ROOT . 'message.php' );

		/*$isValid = $this->getVariable("isValid");
		
		if ( !$isValid )
		{
			//Redirect to the home or post page
			@header( 'Location: ' . SITE_URL );
			exit;
		}*/
		
		$auth = $this->getVariable( 'AuthUser' );
		
		//Check if we require term of service Agreement 
		$privacySettings = Settings::PrivacySettings();
		
		if ( !empty( $privacySettings ) && isset( $privacySettings['require_users_agree_terms_of_service'] ) && isset( $privacySettings['show_required_terms_in'] ) && $privacySettings['require_users_agree_terms_of_service'] )
		{
			if ( ( $privacySettings['show_required_terms_in'] == 'everywhere' ) || ( $privacySettings['show_required_terms_in'] == 'contact-form' ) )
			{
				if ( !isset( $_POST['terms-of-service'] ) || empty( $_POST['terms-of-service'] ) )
				{
					$this->responseMessage = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (' . __( 'terms-of-service' ) . ')</p>';
					
					$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
					
					$this->Response();
				}
			}
		}
		
		//Check if we want honeypot and/or captcha
		if ( 
			( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'contact-form' )
		)
		{
			//Honeypot
			if ( Settings::IsTrue( 'enable_honeypot' ) )
			{
				//Check if this is a bot
				//These fields should be empty, so there is no reason to check if this mode is enabled for guests and/or members only
				if (
					( isset( $_POST['name'] ) && !empty( $_POST['name'] ) ) ||
					( isset( $_POST['email'] ) && !empty( $_POST['email'] ) )
				)
				{					
					$this->type = 'junk';
				}
			}
			
			//Recaptcha
			if ( 
				( Settings::Get()['enable_recaptcha'] != 'false' )
				&& 
				( 
					!Settings::IsTrue( 'hide_captcha_logged_in_users' )
				|| 
				( Settings::IsTrue( 'hide_captcha_logged_in_users' ) && ( empty( $auth['id_member'] ) || ( $auth['id_member'] == 0 ) ) )
				)
			)
			{
				//V2
				if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v2' )
				{
					if ( !isset( $_POST['g-recaptcha-response'] ) || empty( $_POST['g-recaptcha-response'] ) )
					{
						$this->responseMessage  = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (captcha)</p>';
						$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
						$this->Response();
					}

					$check = CheckCaptcha ( $_POST['g-recaptcha-response'] );
					
					if ( !$check )
					{
						$this->responseMessage  = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (captcha)</p>';
						$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
						
						$this->Response();
					}
				}
				
				//V3
				else
				{
					if ( !isset( $_POST['recaptcha_response'] ) || empty( $_POST['recaptcha_response'] ) )
					{
						$this->responseMessage  = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (captcha)</p>';
						$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
						$this->Response();
					}
					
					$check = CheckCaptcha ( $_POST['recaptcha_response'] );
					
					if ( !$check )
					{
						$this->type = 'junk';
					}
				}
			}
		}
		
		//We can continue with the email
		if ( empty( $_POST['message'] )  )
		{
			$this->responseMessage  = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (message)</p>';
			$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
			$this->Response();
		}
		
		if ( empty( $_POST['aemail'] ) )
		{
			$this->responseMessage  = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (email)</p>';
			$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
			$this->Response();
		}
			
		if ( empty( $_POST['aname'] ) )
		{
			$this->responseMessage  = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (name)</p>';
			$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
			$this->Response();
		}

		if ( !Validate( $_POST['aemail'], 'email' ) )
		{
			$this->responseMessage  = '<p>' . __ ( 'error-please-enter-valid-email-address' ) . '</p>';
			$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
			$this->Response();
		}
		
		$this->ip = GetRealIp();
		$this->message = Sanitize( $_POST['message'], false );
		$this->email = Sanitize( $_POST['aemail'], false );
		$this->name = Sanitize( $_POST['aname'], false );
		$this->subject = ( !empty( $_POST['subject'] ) ? Sanitize( $_POST['subject'], false ) : __( 'contact-form' ) );
		
		//Now let's insert the email into the DB
		$this->AddEmail();
		
		//Sent the email to the site's Admin
		$this->Email();
		
		$this->responseMessage  = '<p>' . __ ( 'your-message-has-been-sent-successfully' ) . '</p>';
		$this->responseMessage .= '<p><a href="' . SITE_URL . '">&laquo; ' . __( 'click-here-to-return-to-the-homepage' ) . '</a></p>';
		$this->Response();
	}
	
	private function AddEmail()
	{
		$dbarr = array(
			"id_site" 			=> SITE_ID,
			"added_time"		=> time(),
			"name" 				=> $this->name,
			"subject" 			=> $this->subject,
			"email" 			=> $this->email,
			"post" 				=> $this->message,
			"ip" 				=> $this->ip,
			"status" 			=> $this->type,
			"default_status" 	=> $this->type
		);

		return $this->db->insert( 'mails' )->set( $dbarr, null, true );
	}

	private function Email()
	{
		// Default email address
		$email 		= Settings::Get()['contact_email'];
		$subject 	= '[' . $this->lang['data']['site_name'] . '] ' . $this->subject;
		
		//Send the email
		$mail = new PHPMailer;
		$mail->SMTPDebug = 0;
		$mail->isSendmail();
		$mail->CharSet    = 'UTF-8'; 
		$mail->setFrom( $this->email, $this->name );
		$mail->addAddress( $email );
		$mail->addReplyTo( $this->email, $this->name );
			
		$mail->isHTML(true);                     
		$mail->Subject = $subject;
		$mail->Body    = $this->message;
		$mail->AltBody = $this->message;
		
		if ( !$mail->send() )
		{
			Log::Set( __( 'message-could-not-be-sent' ), $mail->ErrorInfo, $_POST, 'system' );
		}
	}
	
	//Return the response
	private function Response()
	{
		header( 'HTTP/1.1 200 OK' );
		header( 'Access-Control-Allow-Origin: *' );//TODO
		header( 'Access-Control-Allow-Methods: GET ');
		header( 'Content-Type: text/html; charset=UTF-8' );
		
		/*
			This is a generic responsive theme to show various messages
			TODO: Create a function to use this for generic messages
		*/
		$html = '<!DOCTYPE html>
		<html>
			  <head>
				<meta charset="utf-8">
				<title>' . $this->lang['data']['site_name'] . '</title>
				<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
				<style>*{box-sizing:border-box}body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";font-size:18px;text-align:center;line-height:1.5;color:#333}input,button,select,textarea{font-family:inherit;font-size:inherit;line-height:inherit}a{cursor:pointer;color:#181818;text-decoration:none;font-weight:700}a:hover{text-decoration:underline}.container{margin:100px auto;max-width:450px;padding:0 15px}.button{display:inline-block;padding:15px 25px;background:#181818;color:#fff;text-decoration:none;text-align:center;vertical-align:middle;border-radius:4px;cursor:pointer;white-space:nowrap;font-weight:700;border:0}.button:hover{text-decoration:none}.button:active,.button.active{box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}.form{max-width:300px;margin:0 auto}.form .button{display:block;width:100%}.form-control{display:block;width:100%;text-align:center;padding:15px 20px;background-color:#fff;border:2px solid #eee;border-radius:4px;transition:border-color .15s}.form-control:focus,.form-control.focus{border-color:#181818}.errors{color:#d83e3e}.errors ul{list-style-type:none;margin:0;padding:0}</style>
			  </head>

			  <body>
				<div class="container">
					' . $this->responseMessage . '
				</div>
			</body>
		</html>';
		
		echo $html;
		
		exit(0);
	}
}