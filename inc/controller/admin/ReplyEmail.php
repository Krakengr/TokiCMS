<?php defined('TOKICMS') or die('Hacking attempt...');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'Exception.php';
require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'PHPMailer.php';
require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'SMTP.php';

class ReplyEmail extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'view-emails' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$id = (int) Router::GetVariable( 'key' );
		
		$Email = GetSingleEmail( $id, $Admin->GetSite(), true );

		if ( !$Email )
			Redirect( $Admin->GetUrl( 'emails' ) );
		
		$this->setVariable( 'Email', $Email );
		
		Theme::SetVariable( 'headerTitle', __( 'reply-mail' ) . ': "' . htmlspecialchars( $Email['subject'] ) . '" | ' . $Admin->SiteName() );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
		{
			return;
		}
		
		//if ( !verify_token( 'reply_email_' . $id ) )
		//	return;
		
		if ( !Validate( $_POST['email'], 'email' ) )
		{
			$Admin->SetErrorMessage( __( 'error-please-enter-valid-email-address' ) );
			Redirect( $Admin->GetUrl( 'reply-mail' . PS . 'id' . PS . $id ) );
		}

		//Save this mail as draft
		if ( isset( $_POST['draft'] ) )
		{
			//Check if we have draft email already
			$dr = $this->db->from( null, "
			SELECT id
			FROM `" . DB_PREFIX . "mails`
			WHERE (id_member = " . $Admin->UserID() . ") AND (id_parent = " . $id . ") AND (status = 'draft')"
			)->single();
			
			if ( !$dr )
			{
				$dbarr = array(
					"id_site" 		=> $Admin->GetSite(),
					"added_time"    => time(),
					"name" 			=> '',
					"subject"		=> $_POST['subject'],
					"email"			=> $_POST['email'],
					"post"			=> $_POST['message'],
					"ip"			=> GetRealIp(),
					"status"		=> 'draft',
					"id_parent" 	=> $id,
					"id_member" 	=> $Admin->UserID(),
				);
				
				$draftId = $this->db->insert( 'mails' )->set( $dbarr, null, true );
			}
			
			else
			{
				$draftId = $dr['id'];
				
				$dbarr = array(
					"subject" 	=> $_POST['subject'],
					"post" 		=> $_POST['message']
				);
				
				$this->db->update( "mails" )->where( 'id', $draftId )->set( $dbarr );
			}

			Redirect( $Admin->GetUrl( 'reply-mail' . PS . 'id' . PS . $id ) );
		}
		
		elseif ( isset( $_POST['send'] ) )
		{
			$email = Settings::Get()['contact_email'];
			
			//Send the email
			$mail = new PHPMailer;
			$mail->SMTPDebug = 0;
			$mail->isSendmail();
			$mail->CharSet    = 'UTF-8'; 
			$mail->setFrom( $email, $this->lang['data']['site_name'] );
			$mail->addAddress( $_POST['email'] );
			$mail->addReplyTo( $email, $this->lang['data']['site_name'] );
				
			$mail->isHTML(true);                     
			$mail->Subject = $_POST['subject'];
			$mail->Body    = $_POST['message'];
			$mail->AltBody = $_POST['message'];
			
			//Try to send this email before make any DB changes
			if ( !$mail->send() )
			{
				Log::Set( __( 'message-could-not-be-sent' ), $mail->ErrorInfo, $_POST, 'system' );
				$Admin->SetErrorMessage( __( 'Error: Message could not be sent' ) );
				Redirect( $Admin->GetUrl( 'reply-mail' . PS . 'id' . PS . $id ) );
			}
			
			else
			{
				//Check if we have draft email already
				$dr = $this->db->from( null, "
				SELECT id
				FROM `" . DB_PREFIX . "mails`
				WHERE (id_member = " . $Admin->UserID() . ") AND (id_parent = " . $id . ") AND (status = 'draft')"
				)->single();

				if ( $dr )
				{
					//Set this email as "sent"
					$dbarr = array(
						"parent" 	=> 0,
						"id_member" => 0,
						"status" 	=> 'sent'
					);
					
					$this->db->update( "mails" )->where( 'id', $dr['id'] )->set( $dbarr );
				}
				else
				{
					//Add this email as "sent" 
					$dbarr = array(
						"id_site" 		=> $Admin->GetSite(),
						"added_time"    => time(),
						"name" 			=> '',
						"subject"		=> $_POST['subject'],
						"email"			=> $_POST['email'],
						"post"			=> $_POST['message'],
						"ip"			=> GetRealIp(),
						"status"		=> 'sent'
					);
					
					$this->db->insert( 'mails' )->set( $dbarr );
				}

				//Check if we have a reply to this email
				$ex = $this->db->from( null, "
				SELECT added_time
				FROM `" . DB_PREFIX . "mail_replies`
				WHERE (id_member = " . $Admin->UserID() . ") AND (id_mail = " . $id . ")"
				)->single();

				if ( !$ex )
				{
					$dbarr = array(
						"id_member" 	=> $Admin->UserID(),
						"id_mail"    	=> $id,
						"added_time" 	=> time(),
						"message"		=> $_POST['message']
					);
					
					$this->db->insert( 'mails' )->set( $dbarr );
				}
				else
				{
					$dbarr = array(
						"added_time" 	=> time(),
						"message"		=> $_POST['message']
					);
					
					$this->db->update( "mail_replies" )->where( 'id_member', $Admin->UserID() )->where( 'id_mail', $id )->set( $dbarr );
				}
				
				$Admin->SetErrorMessage( __( 'your-message-was-sent-successfully' ), 'info' );
				
				Redirect( $Admin->GetUrl( 'emails' ) );
			}
		}
		
		Redirect( $Admin->GetUrl( 'emails' ) );
	}
}