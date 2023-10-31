<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Site Notifications Form
#
#####################################################
$L = $this->lang;

$form = array
(
	'site-information' => array
	(
		'title' => $L['site-information'],
		'data' => array
		(
			'site-information' => array(
				'title' => null, 'data' => array
				(
					'title'=>array( 'label'=>$L['site-title'], 'type'=>'text', 'name' => 'lang[site_name]', 'value' => $this->currentLang['settings']['site_name'], 'tip'=>null ),
					'slogan'=>array( 'label'=>$L['site-slogan'], 'type'=>'text', 'name' => 'lang[site_slogan]', 'value' => $this->currentLang['settings']['site_slogan'], 'tip'=>$L['use-this-field-to-add-a-catchy-phrase'] ),
					'description'=>array( 'label'=>$L['site-description'], 'type'=>'text', 'name' => 'lang[site_description]', 'value' => $this->currentLang['settings']['site_description'], 'tip'=>$L['you-can-add-a-site-description'] ),
					'about'=>array( 'label'=>$L['site-about'], 'type'=>'text', 'name' => 'lang[site_about]', 'value' => $this->currentLang['settings']['site_about'], 'tip'=>$L['you-can-add-a-site-about'] ),
					'disqus'=>array( 'label'=>$L['disqus-shortname'], 'type'=>'text', 'name' => 'lang[disqus_code]', 'value' => $this->currentLang['settings']['disqus_code'], 'tip'=>$L['disqus-shortname-tip'] ),
					'cookie-consent-message'=>array( 'label'=>$L['cookie-consent-message'], 'type'=>'textarea', 'name' => 'lang[cookie_consent_message]', 'value' => $this->currentLang['settings']['cookie_consent_message'], 'tip'=>$L['cookie-consent-message-tip'] ),
					'cookie-consent-url'=>array( 'label'=>$L['cookie-consent-url'], 'type'=>'text', 'name' => 'lang[cookie_consent_url]', 'value' => $this->currentLang['settings']['cookie_consent_url'], 'tip'=>$L['cookie-consent-url-tip'] ),
					'cookie-consent-more-txt'=>array( 'label'=>$L['cookie-consent-more-txt'], 'type'=>'text', 'name' => 'lang[cookie_consent_more_txt]', 'value' => $this->currentLang['settings']['cookie_consent_more_txt'], 'tip'=>$L['cookie-consent-more-txt-tip'] ),
					'cookie-consent-more-button'=>array( 'label'=>$L['cookie-consent-more-button'], 'type'=>'text', 'name' => 'lang[cookie_consent_more_button]', 'value' => $this->currentLang['settings']['cookie_consent_more_button'], 'tip'=>$L['cookie-consent-more-button-tip'] )
				)
			)
		)
			
	),
	
	'site-maintenance' => array
	(
		'title' => $L['maintenance-mode'],
		'data' => array
		(
			'maintenance-mode' => array(
				'title' => null, 'data' => array
				(
					'enable-maintenance-mode'=>array('label'=>$L['enable-maintenance-mode'], 'name' => 'site[enable_maintenance]', 'type'=>'checkbox', 'value'=>$this->settings::Site()['enable_maintenance'], 'tip'=>null ),
					'maintenance-subject'=>array('label'=>$L['maintenance-subject'], 'type'=>'text', 'name' => 'site[maintenance_subject]', 'value' => $this->settings::Site()['maintenance_subject'], 'tip'=>null),
					'maintenance-text'=>array('label'=>$L['maintenance-text'], 'type'=>'textarea', 'name' => 'site[maintenance_text]', 'value' => $this->settings::Site()['maintenance_text'], 'tip'=>null),
					'enable-login-maintenance'=>array('label'=>$L['enable-login-maintenance'], 'name' => 'site[enable_login_maintenance]', 'type'=>'checkbox', 'value'=>$this->settings::Site()['enable_login_maintenance'], 'tip'=>null ),
				)
			)
		)
	),
	
	'general-Settings' => array
	(
		'title' => $L['general-settings'],
		'data' => array
		(
			'site-Settings' => array(
				'title' => null, 'data' => array
				(
					'enable-multiblog-mode'=>array('label'=>$L['enable-multiblog-mode'], 'name' => 'site[enable_multiblog]', 'type'=>'checkbox', 'value'=>$this->settings::Site()['enable_multiblog'], 'tip'=>$L['multiblog-tip'], 'disabled' => false ),
					'enable-multisite-mode'=>array('label'=>$L['enable-multisite-mode'], 'name' => 'site[enable_multisite]', 'type'=>'checkbox', 'value'=>$this->settings::Site()['enable_multisite'], 'tip'=>$L['multisite-tip'], 'disabled' => false ),
					'enable-polylang-mode'=>array('label'=>$L['enable-polylang-mode'], 'name' => 'site[enable_multilang]', 'type'=>'checkbox', 'value'=>$this->settings::Site()['enable_multilang'], 'tip'=>$L['polylang-tip'], 'disabled' => false )
				)
			)
		)
	),
	
	'site-logo' => array
	(
		'title' => $L['site-logo'],
		'data' => array
		(
			'site-logo' => array(
				'title' => null, 'data' => array
				(
					'upload-site-logo'=>array('label'=>null, 'name' => 'settings[site_default_image]', 'type'=>'custom-html', 'value'=>$uploadHtml, 'tip'=>null, 'disabled' => false )
				)
			)
		)
	)
);