<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Privacy Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

require ( ARRAYS_ROOT . 'generic-arrays.php');

$query = PostsDefaultQuery( "(p.id_site = " . $this->siteID . ") AND (p.id_lang = " . $this->langID . ") AND (p.post_type = 'page') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)", null, "p.title ASC" );

$q = $this->db->from( null, $query )->all();

$pagesData = array();

$code = $this->LangCode();

$legalPages = Json( $settings['legal_pages'] );

$privacySettings = Json( $settings['privacy_settings'] );
$privacySettings = ( !empty( $privacySettings ) ? $privacySettings : null );

$contactPage = Json( $settings['contact_page'] );
$contactPage = ( ( !empty( $contactPage ) && isset( $contactPage[$code] ) ) ? $contactPage[$code] : null );

$privacyPage = ( ( !empty( $legalPages ) && isset( $legalPages['privacy'][$code] ) ) ? $legalPages['privacy'][$code] : null );

$regAgreementPage = ( ( !empty( $legalPages ) && isset( $legalPages['registration'][$code] ) ) ? $legalPages['registration'][$code] : null );

$termsConditionsPage = ( ( !empty( $legalPages ) && isset( $legalPages['terms'][$code] ) ) ? $legalPages['terms'][$code] : null );

//Create Pages Array
if ( $q )
{
	foreach ( $q as $page )
		$pagesData[$page['id_post']] = array( 'name' => $page['id_post'], 'title'=>$page['title'], 'disabled' => false );
}

$form = array
(
	'privacy-settings' => array
	(
		'title' => $L['privacy-settings'],
		'data' => array(
			'privacy-page' => array( 
				'title' => null, 'data' => array
				(
					'privacy-page'=>array('label'=>$L['select-privacy-page'], 'name' => 'settings[privacy_policy_page]', 'type'=>'select', 'value'=>( !empty( $privacyPage ) ? $privacyPage['id'] : null ), 'tip'=>$L['privacy-page-tip'], 'firstNull' => true, 'data' => $pagesData, 'disabled' => false ),
					
					'contact-page'=>array('label'=>$L['select-contact-page'], 'name' => 'settings[contact_page]', 'type'=>'select', 'value'=>( !empty( $contactPage ) ? $contactPage['id'] : null ), 'tip'=>$L['contact-page-tip'], 'firstNull' => true, 'data' => $pagesData, 'disabled' => false ),
					
					'add-contact-form'=>array('label'=>$L['add-contact-form-to-your-contact-page'], 'name' => 'settings[add_contact_form_to_contact_page]', 'type'=>'checkbox', 'value'=> ( isset( $privacySettings['add_contact_form_to_contact_page'] ) ? $privacySettings['add_contact_form_to_contact_page'] : null ), 'tip'=>$L['add-contact-form-to-your-contact-page-tip'] ),

					'registration-agreement'=>array('label'=>$L['registration-agreement-page'], 'name' => 'settings[registration_agreement_page]', 'type'=>'select', 'value'=>( !empty( $regAgreementPage ) ? $regAgreementPage['id'] : null ), 'tip'=>$L['registration-agreement-page-tip'], 'firstNull' => true, 'data' => $pagesData, 'disabled' => false ),
					
					'terms-page'=>array('label'=>$L['select-terms-and-conditions-page'], 'name' => 'settings[terms_conditions_page]', 'type'=>'select', 'value'=>( !empty( $termsConditionsPage ) ? $termsConditionsPage['id'] : null ), 'tip'=>$L['select-terms-and-conditions-page-tip'], 'firstNull' => true, 'data' => $pagesData, 'disabled' => false ),
	
					'require-terms-page'=>array('label'=>$L['require-users-to-agree-to-your-terms-of-service'], 'name' => 'settings[require_users_agree_terms_of_service]', 'type'=>'checkbox', 'value'=>( isset( $privacySettings['require_users_agree_terms_of_service'] ) ? $privacySettings['require_users_agree_terms_of_service'] : null ), 'tip'=>null ),
					
					'terms-page-enabled-in'=>array('label'=>$L['enabled-in'], 'name' => 'settings[show_required_terms_in]', 'type'=>'select', 'value'=>( isset( $privacySettings['show_required_terms_in'] ) ? $privacySettings['show_required_terms_in'] : null ), 'tip'=>sprintf( $L['add-a-required-checkbox-in'], $L['terms-and-conditions'] ), 'firstNull' => false, 'disabled' => false, 'data' => $secFormArray ),
				)
			)
		)
	)
);