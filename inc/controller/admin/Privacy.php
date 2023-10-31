<?php defined('TOKICMS') or die('Hacking attempt...');

class Privacy extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'privacy-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'privacy' ) )
			return;
		
		//$Posts = $this->getVariable( 'Posts' );
		
		//Get the needed POST values
		$settings = $_POST['settings'];
		
		$_settings = $Admin->Settings()::Get();
		
		$code = $Admin->LangCode();
		
		$legalPages = Json( $_settings['legal_pages'] );
		
		$contactPage = Json( $_settings['contact_page'] );

		$privacySettings = array(
			'add_contact_form_to_contact_page' => ( isset( $settings['add_contact_form_to_contact_page'] ) ? true : false ),
			'require_users_agree_terms_of_service' => ( isset( $settings['require_users_agree_terms_of_service'] ) ? true : false ),
			'show_required_terms_in' => ( isset( $settings['show_required_terms_in'] ) ? Sanitize( $settings['show_required_terms_in'], false ) : null )
		);

		//Get the privacy page and build an array
		if ( !empty( $settings['privacy_policy_page'] ) )
		{
			$post = GetSinglePost( (int) $settings['privacy_policy_page'], $Admin->GetSite(), false );

			if ( $post )
			{
				$privacyPage = array(
					'id' => $post['id'],
					'title' => $post['title'],
					'url' => $post['postUrl']
				);
		
				unset( $post );
			}
			else
				$privacyPage = array();
		}
		else
			$privacyPage = array();
		
		//Add the "privacy" page into the array
		$legalPages['privacy'][$code] = $privacyPage;
		
		//Do the same for the terms_conditions_page
		if ( !empty( $settings['terms_conditions_page'] ) )
		{
			$post = GetSinglePost( $settings['terms_conditions_page'], $Admin->GetSite(), false );
			
			if ( $post )
			{
				$termsPage = array(
					'id' => $post['id'],
					'title' => $post['title'],
					'url' => $post['postUrl']
				);
		
				unset( $post );
			}
			else
				$termsPage = array();
		}
		else
			$termsPage = array();
		
		//Add the "terms" page into the array
		$legalPages['terms'][$code] = $termsPage;
		
		//Do the same for the contact us page
		if ( !empty( $settings['contact_page'] ) )
		{
			$post = GetSinglePost( $settings['contact_page'], $Admin->GetSite(), false );
			
			if ( $post )
			{
				$contactPage[$code] = array(
					'id' => $post['id'],
					'title' => $post['title'],
					'url' => $post['postUrl']
				);
		
				unset( $post );
			}
			else
				$contactPage[$code] = array();
		}
		else
			$contactPage[$code] = array();
		
		//Do the same for the registration agreement page
		if ( !empty( $settings['registration_agreement_page'] ) )
		{
			$post = GetSinglePost( $settings['registration_agreement_page'], $Admin->GetSite(), false );
			
			if ( $post )
			{
				$regAgreementPage = array(
					'id' => $post['id'],
					'title' => $post['title'],
					'url' => $post['postUrl']
				);
		
				unset( $post );
			}
			else
				$regAgreementPage = array();
		}
		else
			$regAgreementPage = array();
		
		//Add the "registration" page into the array
		$legalPages['registration'][$code] = $regAgreementPage;

		$settingsArray = array(
			'legal_pages' => json_encode( $legalPages, JSON_UNESCAPED_UNICODE ),
			'contact_page' => json_encode( $contactPage, JSON_UNESCAPED_UNICODE ),
			'privacy_settings' => json_encode( $privacySettings, JSON_UNESCAPED_UNICODE )
		);

		$Admin->UpdateSettings( $settingsArray );
		
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}