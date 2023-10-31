<?php defined('TOKICMS') or die('Hacking attempt...');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'Exception.php';
require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'PHPMailer.php';
require TOOLS_ROOT . 'PHPMailer' . DS . 'src' . DS . 'SMTP.php';

class PostComment extends Controller {
	
	private $ip;
	private $commentName = '';
	private $commentStatus;
	private $commentEmail = '';
	private $commentPost = '';
	private $commentUrl = '';
	private $responseMessage = '';
	private $redirUrl;
	private $languageId;
	private $commentId;
	private $blogId;
	private $postId;
	private $postTitle;
	private $postUrl;
	private $userId = 0;
	private $rating = 0;
	private $ratingData = array();
	private $showRedir = false;

    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		//We shouldn't be here without POST
		if ( 
			( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || empty( $_POST ) || !isset( $_POST['postID'] ) || !is_numeric( $_POST['postID'] )
		)
		{
			@header('HTTP/1.1 404 Not Found');

			//Redirect to the home page
			@header('Location: ' . SITE_URL );
			exit;
		}
		
		//Include the generic file for messages
		Router::SetVariable( 'includeFile', INC_ROOT . 'message.php' );

		//You have no access?
		if ( !IsAllowedTo( 'view-site' ) )
		{
			//Don't include this file while on login or register
			if ( ( Router::WhereAmI() != 'login' ) || ( Router::WhereAmI() != 'register' ) )
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );

			$this->view();
			return;
		}
		
		//We shouldn't be here
		if ( !ENABLE_COMMENTS || !IsAllowedTo( 'post-comments' ) )
		{
			@header('HTTP/1.1 404 Not Found');

			$message = sprintf( __( 'possible-spam-bot-comment-generic' ), $_SERVER['HTTP_USER_AGENT'], $ip );
			$title 	 = __( 'possible-spam-bot-title-generic' );
			
			$message .= PHP_EOL . $title . PHP_EOL . $ip;

			Log::Set( __( 'possible-spam-bot' ), $message, $_POST, 'system' );

			//Redirect to the home page
			@header('Location: ' . SITE_URL );
			exit;
		}
		
		$isValid 	= $this->getVariable("isValid");
		$auth 		= $this->getVariable( 'AuthUser' );
		$Post 		= GetSinglePost( $_POST['postID'], SITE_ID, false, true );

		//Check if we have this post and has comments enabled
		if ( !$Post || !$Post->CanComment() || !$isValid )
		{
			//Redirect to the home or post page
			@header('Location: ' . ( !$Post ? SITE_URL : $Post->Url() ) );
			exit;
		}
		
		//TODO
		//$ip = MaskIp( $ip );
		$this->ip 				= GetRealIp();
		$this->commentStatus 	= ( !IsAllowedTo( 'auto-publish-comments' ) ? 'pending' : 'approved' );
		$this->postId 			= $Post->PostId();
		$this->postTitle 		= $Post->Title();
		$this->postUrl 			= $Post->Url();
		$this->languageId 		= $Post->Language()->id;
		$this->blogId 			= $Post->Blog()->id;

		//Check if we require term of service Agreement 
		$privacySettings = Settings::PrivacySettings();
		
		$commentsSettings = Settings::Comments();
		
		if ( !empty( $commentsSettings ) && !empty( $commentsSettings['redirect_with_message'] ) )
		{
			$this->showRedir = true;
		}
		
		if ( !empty( $privacySettings ) && isset( $privacySettings['require_users_agree_terms_of_service'] ) && isset( $privacySettings['show_required_terms_in'] ) && $privacySettings['require_users_agree_terms_of_service'] )
		{
			if ( ( $privacySettings['show_required_terms_in'] == 'everywhere' ) || ( $privacySettings['show_required_terms_in'] == 'comment-form' ) )
			{
				if ( !isset( $_POST['terms-of-service'] ) || empty( $_POST['terms-of-service'] ) )
				{
					$this->responseMessage  = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (' . __( 'terms-of-service' ) . ')</p>';
					$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
						
					$this->Response();
				}
			}
		}
		
		//Check if we want honeypot and/or captcha
		if ( 
			( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'comment-form' )
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
					$this->SetSpam();

					//Redirect to the post page
					@header('Location: ' . $this->postUrl );
					exit;
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
						$this->SetSpam();

						//Redirect to the post page
						@header('Location: ' . $this->postUrl );
						exit;
					}
				}
			}
		}
		
		//We can continue with comment
		$this->commentPost = Sanitize( $_POST['comment'], false );

		if ( empty( $_POST['comment'] )  )
		{
			$this->responseMessage  = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (comment)</p>';
			$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';
			$this->Response();
		}

		//There is a member who's posting? Check if this value is valid
		if ( isset( $_POST['userID'] ) && !empty( $_POST['userID'] ) )
		{
			$this->userId 		= $auth['id_member'];
			$this->commentName 	= ( !empty( $auth['real_name'] ) ? $auth['real_name'] : $auth['user_name'] );
			$this->commentEmail = $auth['email_address'];
		}
		
		else
		{
			if ( empty( $_POST['aemail'] ) )
			{
				$this->responseMessage  = '<p>' . __ ( 'error-please-fill-the-required-fields' ) . ' (email)</p>';
				$this->responseMessage .= '<p><a href="javascript:history.back()">&laquo; ' . __( 'go-back' ) . '</a></p>';

				$this->Response();
			}
			
			if ( empty( $_POST['author'] ) )
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
			
			$this->commentEmail = Sanitize( $_POST['aemail'], false );
			$this->commentName 	= Sanitize( $_POST['author'], false );
			$this->commentUrl 	= ( !Validate( $_POST['url'], 'url' ) ? '' : Sanitize( $_POST['url'], false ) );
		}
		
		//Check if we want to add a rating
		if ( Settings::IsTrue( 'enable_reviews' ) )
		{
			$UserGroup = UserGroup();
			
			$groups = Json( Settings::Get()['allow_reviews_group'] );
			
			//Allow this review to be added
			if ( ( $UserGroup == 1 ) || ( !empty( $groups ) && in_array( $UserGroup, $groups ) ) )
			{
				if ( isset( $_POST['rating'] ) && !empty( $_POST['rating'] ) )
				{
					$this->rating = (int) $_POST['rating'];
				}
				
				$this->ratingData['pos'] = Sanitize( $_POST['pos_comment'], false );
				$this->ratingData['neg'] = Sanitize( $_POST['neg_comment'], false );
			}
		}
		
		//Now let's add the comment
		$this->AddComment();
		
		//Notify the visitor if the comment could not be added
		if ( !$this->commentId )
		{
			$this->responseMessage  = '<p>' . __ ( 'sorry-we-could-not-save-your-comment' ) . '</p>';
			$this->responseMessage .= '<p><a href="' . $this->postUrl . '">&laquo; ' . __( 'click-here-to-return-to-the-post' ) . '</a></p>';

			$this->Response();			
		}
		
		//Notify the admins
		$this->Notify();

		//Don't update post commment number, if this post will be awaiting moderation
		if ( !IsAllowedTo( 'auto-publish-comments' ) )
		{
			$this->responseMessage  = '<p>' . __ ( 'your-comment-has-been-added-but-awaiting-moderation' ) . '</p>';
			$this->responseMessage .= '<p><a href="' . $this->postUrl . '">&laquo; ' . __( 'click-here-to-return-to-the-post' ) . '</a></p>';

			$this->Response();
		}
		
		//Update post stats
		$this->UpdateNums();
		
		//Delete the post's cache, so the comment can be seen by its author
		DeletePostCaches( $Post->Sef(), $Post->Language()->key );
		
		$this->redirUrl = $this->postUrl . ( $this->commentId ? '#comment-' . $this->commentId : '' );
		
		//Redirect to the post page
		if ( $this->showRedir )
		{
			$this->responseMessage  = '<p>' . __ ( 'your-comment-has-been-successfully-submitted' ) . '</p>';
			$this->responseMessage .= '<p>' . sprintf( __( 'you-are-being-redirected' ), $this->redirUrl ) . '</p>';
			
			$this->Response( true );
		}

		@header('Location: ' . $this->redirUrl );
		exit;
	}
	
	private function Notify()
	{
		if ( !Settings::IsTrue( 'mail_on_comments' ) )
			return;
		
		$this->Email();
	}
	
	private function UpdateNums()
	{
		//Update the posts's num items
		$this->db->update( POSTS )->where( "id_post", $this->postId )->increase( "num_comments" );

		//Update the blog's num items too
		if ( $this->blogId > 0 )
		{
			$this->db->update( "blogs" )->where( "id_blog", $this->blogId )->increase( "num_comments" );
		}
	}
	
	private function SetSpam()
	{
		$message = sprintf( __( 'possible-spam-bot-comment-message' ), $this->postId, $_SERVER['HTTP_USER_AGENT'], $this->ip );

		$title 	 = sprintf( __( 'possible-spam-bot-comment-title' ), $this->postTitle );

		Log::Set( __( 'possible-spam-bot' ), $message . PHP_EOL . $title, $_POST, 'system' );

		if ( Settings::IsTrue( 'put_spam_in_spam_folder' ) )
		{
			$this->commentEmail 	= Sanitize( $_POST['email'], false );
			$this->commentName 		= Sanitize( $_POST['name'], false );
			$this->commentPost 		= Sanitize( $_POST['comment'], false );
			$this->commentStatus 	= 'spam';

			$this->AddComment();
		}
	}
	
	private function AddComment()
	{
		//Now let's add the comment
		$dbarr = array(
			"id_post" 		=> $this->postId,
			"id_lang" 		=> $this->languageId,
			"id_blog" 		=> $this->blogId,
			"id_site" 		=> SITE_ID,
			"user_id" 		=> $this->userId,
			"name" 			=> $this->commentName,
			"url" 			=> $this->commentUrl,
			"email" 		=> $this->commentEmail,
			"comment" 		=> $this->commentPost,
			"added_time" 	=> time(),
			"status" 		=> $this->commentStatus,
			"id_parent" 	=> 0,
			"ip" 			=> $this->ip,
			"rating" 		=> $this->rating,
			"rating_data" 	=> json_encode( $this->ratingData, JSON_UNESCAPED_UNICODE )
		);
		
		$this->commentId = $this->db->insert( 'comments' )->set( $dbarr, null, true );
	}
	
	//Send the email
	private function Email()
	{
		$title 	= $this->postTitle;
		$name 	= $this->commentName;
		$url 	= $this->postUrl;

		// Default sender address
		$from_name 	= $this->lang['data']['site_name'];
		$email 		= Settings::Get()['contact_email'];
		$from_email = Settings::Get()['website_email'];
		$subject 	= '[' . $this->lang['data']['site_name'] . '] ' . __('comment') . ': "' . $title . '"';
		$emailHtml = '<h1>' . sprintf( __( 'new-comment-on' ), $this->lang['data']['site_name'] ) .'</h1>';
		$emailHtml .= '<p>' . $name . ' ' . __('commented-on') . ' "' . $title . '"</p>'; 
		
		if ( $this->commentStatus == 'approved' )
		{
			$emailHtml .= '<p><a href="' . $url . '#comment-' . $this->commentId . '" class="btn">' . __('view-the-comment') . '</a></p>';
		}
		
		//Add a backup mail
		$dbarr = array(
			"id_site" 		=> SITE_ID,
			"added_time"	=> time(),
			"name" 			=> $from_name,
			"subject" 		=> $subject,
			"email" 		=> $email,
			"post" 			=> $emailHtml,
			"ip" 			=> $this->ip,
			"status" 		=> 'inbox'
		);

		$this->db->insert( 'mails' )->set( $dbarr );
		
		//Send the email
		$mail = new PHPMailer;
		$mail->SMTPDebug = 0;
			//$mail->isSMTP();
			//$mail->Host       = 'localhost';  
			//$mail->SMTPAuth   = true;    
			//$mail->Username   = '';
			//$mail->Password   = '';
			//$mail->SMTPSecure = '';
			//$mail->Port       = 25;
		$mail->isSendmail();
		$mail->CharSet    = 'UTF-8'; 
		$mail->setFrom( $from_email, $from_name );
		$mail->addAddress( $email );
		$mail->addReplyTo( $email, $from_name );
			
		$mail->isHTML(true);                     
		$mail->Subject = $subject;
		$mail->Body    = $emailHtml;
		$mail->AltBody = $name . ' ' . __('commented-on') . ' "' . $title . '"';
		
		if ( !$mail->send() )
		{
			Log::Set( __( 'message-could-not-be-sent' ), $mail->ErrorInfo, $_POST, 'system' );
		}
	}
	
	//Return the response
	private function Response( $redir = false )
	{
		header( 'HTTP/1.1 200 OK' );
		header( 'Access-Control-Allow-Origin: *' );//TODO
		header( 'Access-Control-Allow-Methods: GET ');
		header( 'Content-Type: text/html; charset=UTF-8' );
		
		/*
			This is a generic responsive theme to show various messages
			TODO: Create a generic function to use this for generic messages
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
				<div class="container">';
				
				if ( $redir )
				{
					$html .= '<span style="margin-top: 10px;"><img src="' . TOOLS_HTML . 'theme_files/assets/frontend/img/loading.gif" alt="loading" /></span>';
				}
				
				$html .= $this->responseMessage;
				
				$html .= '
				</div>';
				
				if ( $redir )
				{
					$html .= '
					<script>
						window.setTimeout(function(){
							window.location.href = "' . $this->redirUrl . '";
						}, 3000);
					</script>';
				}
				
			$html .= '
			</body>
		</html>';
		
		echo $html;
		
		exit;
	}
}