<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Tools Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

$form = array
(
	'tools' => array
	(
		'title' => $L['tools'],
		'data' => array
		(
			'tools-settings' => array(
				'title' => null, 'data' => array
				(
					'enable-amp-mode'=>array('label'=>$L['enable-amp-mode'], 'name' => 'settings[enable_amp]', 'type'=>'checkbox', 'value'=>$settings['enable_amp'], 'tip'=>$L['amp-tip'], 'disabled' => false, 'hide' => ( !$this->siteIsSelfHosted ? true : false ) ),
					
					'enable-seo-mode'=>array('label'=>$L['enable-seo-mode'], 'name' => 'settings[enable_seo]', 'type'=>'checkbox', 'value'=>$settings['enable_seo'], 'tip'=>$L['seo-tip'], 'disabled' => false, 'hide' => ( !$this->siteIsSelfHosted ? true : false ) ),
					
					'auto-content-mode'=>array('label'=>$L['enable-auto-content-mode'], 'name' => 'settings[enable_autoblog]', 'type'=>'checkbox', 'value'=>$settings['enable_autoblog'], 'tip'=>$L['auto-content-tip'], 'disabled' => false ),
					
					'enable-redirect-mode'=>array('label'=>$L['enable-redirect-mode'], 'name' => 'settings[enable_redirect]', 'type'=>'checkbox', 'value'=>$settings['enable_redirect'], 'tip'=>$L['redirect-mode-tip'], 'disabled' => false ),
					
					'enable-cookie'=>array('label'=>$L['enable-cookie-consent'], 'name' => 'settings[enable_cookie_concent]', 'type'=>'checkbox', 'value'=>( $this->adminSettings::IsTrue( 'enable_cookie_concent' ) ? true : false ), 'tip'=>$L['enable-cookie-consent-tip'], 'disabled' => false, 'hide' => ( !$this->siteIsSelfHosted ? true : false ) ),
					
					'enable-api'=>array('label'=>$L['enable-api'], 'name' => 'settings[enable_api]', 'type'=>'checkbox', 'value'=>$settings['enable_api'], 'tip'=>$L['enable-api-tip'], 'disabled' => false ),
					
					'enable-post-attributes'=>array('label'=>$L['enable-post-attributes'], 'name' => 'settings[enable_post_attributes]', 'type'=>'checkbox', 'value'=>$settings['enable_post_attributes'], 'tip'=>$L['enable-post-attributes-tip'], 'disabled' => false, 'hide' => ( !$this->siteIsSelfHosted ? true : false ) ),
					
					'enable-auto-social-publish'=>array('label'=>$L['enable-social-media-auto-publish'], 'name' => 'settings[enable_social_auto_publish]', 'type'=>'checkbox', 'value'=>$settings['enable_social_auto_publish'], 'tip'=>$L['enable-social-media-auto-publish-tip'], 'disabled' => false ),
					
					'enable-media-gallery'=>array('label'=>$L['enable-media-gallery'], 'name' => 'settings[enable_galleries]', 'type'=>'checkbox', 'value'=>$settings['enable_galleries'], 'tip'=>$L['enable-media-gallery-tip'], 'disabled' => false, 'hide' => ( !$this->siteIsSelfHosted ? true : false ) ),
					
					'enable-ad-manager'=>array('label'=>$L['enable-ad-manager'], 'name' => 'settings[enable_ads]', 'type'=>'checkbox', 'value'=>$settings['enable_ads'], 'tip'=>$L['enable-ad-manager-tip'], 'disabled' => false, 'hide' => ( !$this->siteIsSelfHosted ? true : false ) ),
					
					'enable-automatic-translator'=>array('label'=>$L['enable-automatic-translator'], 'name' => 'settings[enable_auto_translate]', 'type'=>'checkbox', 'value'=>$settings['enable_auto_translate'], 'tip'=>$L['enable-automatic-translator-tip'], 'disabled' => false, 'hide' => ( !$this->siteIsSelfHosted ? true : false ) ),
					
					'enable-forms-tables'=>array('label'=>$L['enable-forms-tables'], 'name' => 'settings[enable_forms]', 'type'=>'checkbox', 'value'=>$settings['enable_forms'], 'tip'=>$L['enable-forms-tables-tip'], 'disabled' => false, 'hide' => ( !$this->siteIsSelfHosted ? true : false ) ),
					
					'enable-links-manager'=>array('label'=>$L['enable-links-manager'], 'name' => 'settings[enable_link_manager]', 'type'=>'checkbox', 'value'=>$settings['enable_link_manager'], 'tip'=>$L['enable-links-manager-tip'], 'disabled' => false, 'hide' => ( !$this->siteIsSelfHosted ? true : false ) )
				)
			)
		)
	)
);