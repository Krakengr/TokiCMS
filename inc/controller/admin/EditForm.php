<?php defined('TOKICMS') or die('Hacking attempt...');

class EditForm extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-forms' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$id = (int) Router::GetVariable( 'key' );
		
		$Form = GetSingleForm( $id );

		if ( !$Form )
			Redirect( $Admin->GetUrl( 'forms' ) );
		
		//Make sure we have the correct type
		if ( $Form['type'] == 'table' )
		{
			Redirect( $Admin->GetUrl( 'edit-table' . PS . 'id' . PS . $id ) );
		}
		
		Theme::SetVariable( 'headerTitle', __( 'edit-form' ) . ': "' . $Form['name'] . '" | ' . $Admin->SiteName() );

		$this->setVariable( 'Form', $Form );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		//Maybe we want to delete this form?
		if ( isset( $_POST['delete'] ) )
		{
			$q = $this->db->delete( 'forms' )->where( "id", $id )->run();
	
			if ( $q )
			{
				//Delete also any elements this form may have
				$this->db->delete( 'form_elements' )->where( "id_form", $id )->run();
			}

			Redirect( $Admin->GetUrl( 'forms' ) );
		}
		
		$error = null;
		
		$templName = ( !empty( $_POST['formTemplateName'] ) ? $_POST['formTemplateName'] : $_POST['title'] );
		
		$templateName = ( isset( $_POST['save-template'] ) ? $templName : ( isset( $_POST['delete-template'] ) ? '' : ( isset( $Form['data']['template_name'] ) ? $Form['data']['template_name'] : '' ) ) );
		
		$templateId = ( isset( $Form['data']['template_id'] ) ? $Form['data']['template_id'] : 0 );

		$fromEmail 		= Sanitize( $_POST['from-email'], false );
		$emailAddress 	= Sanitize( $_POST['email-address'], false );
		
		if ( !empty( $_POST['from-email'] ) && !Validate( $_POST['from-email'] ) )
		{
			$error = true;
			$fromEmail = '';
		}
		
		if ( !empty( $_POST['email-address'] ) && !Validate( $_POST['email-address'] ) )
		{		
			$error = true;	
			$emailAddress = '';
		}
		
		if ( $error )
		{
			$Admin->SetErrorMessage( __( 'error-please-enter-valid-email-address' ) );
		}
		
		$confirmationPage = array();
		
		$confirmationMessage = $confirmationUrl = null;
		
		if ( ( $_POST['confirmationType'] == 'message' ) && !empty( $_POST['confirmationUrl'] ) )
		{
			$confirmationUrl = Sanitize( $_POST['confirmationUrl'], false );
		}
		
		if ( ( $_POST['confirmationType'] == 'url' ) && !empty( $_POST['confirmationMessage'] ) )
		{
			$confirmationMessage = Sanitize( $_POST['confirmationMessage'], false );
		}
		
		if ( isset( $_POST['confirmationPage'] ) && !empty( $_POST['confirmationPage'] ) )
		{
			$pg = GetSinglePost( $_POST['confirmationPage'], null, false );
			
			if ( $pg )
			{
				$confirmationPage = array(
					'title' => $pg['title'],
					'id'	=> $pg['id'],
					'url'	=> $pg['postUrl']
				);
			}
		}

		$s = array(
			'anti_spam' 					=> ( isset( $_POST['anti-spam'] ) ? true : false ),
			'form_css' 						=> Sanitize( $_POST['form-css'], false ),
			'enable_notifications' 			=> ( isset( $_POST['enable-notifications'] ) ? true : false ),
			'email_address' 				=> $emailAddress,
			'email_subject' 				=> Sanitize( $_POST['email-subject'], false ),
			'from_name' 					=> Sanitize( $_POST['from-name'], false ),
			'from_email' 					=> $fromEmail,
			'email_message' 				=> Sanitize( $_POST['email-message'], false ),
			'send_notification_if' 			=> Sanitize( $_POST['sendNotificationIf'], false ),
			'send_notification_option' 		=> Sanitize( $_POST['sendNotificationOption'], false ),
			
			'send_notification_value' 		=> ( isset( $_POST['sendNotificationValue'] ) ? Sanitize( $_POST['sendNotificationValue'], false ) : '' ),
			
			'dont_send_notification_if' 	=> Sanitize( $_POST['dontSendNotificationIf'], false ),
			'dont_send_notification_option' => Sanitize( $_POST['dontSendNotificationOption'], false ),
			
			'dont_send_notification_value' 	=> ( isset( $_POST['dontSendNotificationValue'] ) ? Sanitize( $_POST['dontSendNotificationValue'], false ) : '' ),
			
			'confirmation_type' 			=> Sanitize( $_POST['confirmationType'], false ),
			'confirmation_message' 			=> $confirmationMessage,
			'confirmation_url' 				=> $confirmationUrl,
			'confirmation_page'				=> $confirmationPage,
		);
		
		$groups = ( ( isset( $_POST['membergroups'] ) && !empty( $_POST['membergroups'] ) && is_array( $_POST['membergroups'] ) ) ? $_POST['membergroups'] : array() );

		//Maybe we want to save this form as template?
		if ( ( isset( $_POST['save-template'] ) && ( $templateId == 0 ) ) || ( isset( $_POST['saved-template'] ) && ( $templateId > 0 ) ) )
		{
			$templateName = $templName;
			
			$templ = array(
				'settings' => $s,
				'elements' => array()			
			);
			
			if ( !empty( $_POST['element'] ) )
			{
				foreach ( $_POST['element'] as $elid => $el )
				{
					$elDt = $this->db->from( 
					null, 
					"SELECT elem_id
					FROM `" . DB_PREFIX . "form_elements`
					WHERE (id = " . $elid . ")"
					)->single();
					
					if ( $elDt )
					{
						$templ['elements'][] = array( 'id' => $elDt['elem_id'], 'data' => $el );
					}
				}
			}

			if ( $templateId == 0 )
			{
				$dbarr = array(
					"id_site" 	=> $Admin->GetSite(),
					"title" 	=> $templateName,
					"data" 		=> json_encode( $templ, JSON_UNESCAPED_UNICODE )
				);
				
				$templateId = $this->db->insert( 'form_templates' )->set( $dbarr, null, true );
			}
				
			else
			{
				$dbarr = array(
					"title" => $templateName,
					"data" 	=> json_encode( $templ, JSON_UNESCAPED_UNICODE )
				);

				$this->db->update( 'form_templates' )->where( 'id', $templateId )->set( $dbarr );
			}
		}
		
		//or we want to delete it?
		if ( isset( $_POST['delete-template'] ) && ( $templateId > 0 ) )
		{
			$this->db->delete( 'form_templates' )->where( "id", $templateId )->run();
			
			$templateName 	= '';
			$templateId 	= 0;
		}
		
		//We don't want these settings in the template' data, so we add them here
		$s['template_name']		= $templateName;
		$s['template_id']		= $templateId;
		$s['saved_as_template'] = ( isset( $_POST['save-template'] ) ? true : ( isset( $_POST['delete-template'] ) ? false : ( isset( $Form['data']['saved_as_template'] ) ? $Form['data']['saved_as_template'] : false ) ) );
		
		$disabled = ( isset( $_POST['disable'] ) ? 1 : 0 );
		
		$dbarr = array(
			"title" 		=> $_POST['title'],
			"groups_data" 	=> json_encode( $groups, JSON_UNESCAPED_UNICODE ),
			"disabled" 		=> $disabled,
			"form_data" 	=> json_encode( $s, JSON_UNESCAPED_UNICODE )
		);

		$this->db->update( 'forms' )->where( 'id', $id )->set( $dbarr );
		
		if ( !empty( $_POST['element'] ) )
		{
			foreach ( $_POST['element'] as $elid => $el )
			{
				$elArr = array();
				
				//Check if we have this item
				$elem = $this->db->from( 
				null, 
				"SELECT id
				FROM `" . DB_PREFIX . "form_elements`
				WHERE (id = " . $elid . ") AND (id_form = " . $id . ")"
				)->single();
				
				if ( !$elem )
					continue;
				
				foreach( $el as $l => $el_ )
				{
					$elArr[$l] = htmlspecialchars_decode( $el_ );
				}
				
				//$data = json_encode( $el, JSON_UNESCAPED_UNICODE );
				$data = json_encode( $elArr, JSON_UNESCAPED_UNICODE );
				
				//Update the DB
				$this->db->update( 'form_elements' )->where( 'id', $elid )->set( "data", $data );
			}
		}

		Redirect( $Admin->GetUrl( 'edit-form' . PS . 'id' . PS . $id ) );
	}
}