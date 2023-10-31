<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Security Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

require ( ARRAYS_ROOT . 'generic-arrays.php');

$secFormData = array();
$secFormForms = array();
$secReferrerForms = array();

$secReferrerForms['false'] = array( 'name' => 'false', 'title'=> $L['disabled'], 'disabled' => false, 'data' => array() );
$secReferrerForms['no-referrer'] = array( 'name' => 'no-referrer', 'title'=> 'no-referrer', 'disabled' => false, 'data' => array() );
$secReferrerForms['no-referrer-when-downgrade'] = array( 'name' => 'no-referrer-when-downgrade', 'title'=> 'no-referrer-when-downgrade', 'disabled' => false, 'data' => array() );
$secReferrerForms['same-origin'] = array( 'name' => 'same-origin', 'title'=> 'same-origin', 'disabled' => false, 'data' => array() );
$secReferrerForms['origin'] = array( 'name' => 'origin', 'title'=> 'origin', 'disabled' => false, 'data' => array() );
$secReferrerForms['strict-origin'] = array( 'name' => 'strict-origin', 'title'=> 'strict-origin', 'disabled' => false, 'data' => array() );
$secReferrerForms['origin-when-cross-origin'] = array( 'name' => 'origin-when-cross-origin', 'title'=> 'origin-when-cross-origin', 'disabled' => false, 'data' => array() );
$secReferrerForms['strict-origin-when-cross-origin'] = array( 'name' => 'strict-origin-when-cross-origin', 'title'=> 'strict-origin-when-cross-origin', 'disabled' => false, 'data' => array() );
$secReferrerForms['unsafe-url'] = array( 'name' => 'unsafe-url', 'title'=> 'unsafe-url', 'disabled' => false, 'data' => array() );

$secFormData['false'] = array( 'name' => 'false', 'title'=> $L['disabled'], 'disabled' => false, 'data' => array() );
$secFormData['google-recaptcha-v2'] = array( 'name' => 'google-recaptcha-v2', 'title'=> $L['google-recaptcha-v2'], 'disabled' => false, 'data' => array() );
$secFormData['google-recaptcha-v3'] = array( 'name' => 'google-recaptcha-v3', 'title'=> $L['google-recaptcha-v3'], 'disabled' => false, 'data' => array() );

$secFormForms = $secFormArray;
/*
$secFormForms['everywhere'] = array( 'name' => 'everywhere', 'title'=> $L['everywhere'], 'disabled' => false, 'data' => array() );
$secFormForms['login-form'] = array( 'name' => 'login-form', 'title'=> $L['login-form'], 'disabled' => false, 'data' => array() );
$secFormForms['registration-form'] = array( 'name' => 'registration-form', 'title'=> $L['registration-form'], 'disabled' => false, 'data' => array() );
$secFormForms['comment-form'] = array( 'name' => 'comment-form', 'title'=> $L['comment-form'], 'disabled' => false, 'data' => array() );
$secFormForms['lost-password-form'] = array( 'name' => 'lost-password-form', 'title'=> $L['lost-password-form'], 'disabled' => false, 'data' => array() );*/

$form = array
(
	'security-settings' => array
	(
		'title' => $L['security-settings'],
		'data' => array(

			
			'general-settings' => array( 
				'title' => null, 'data' => array
				(
					'failed-login-attempts'=>array('label'=>$L['failed-login-attempts'], 'name' => 'settings[num_login_retries]', 'type'=>'num', 'value'=>$settings['num_login_retries'], 'tip'=>$L['failed-login-attempts-tip'], 'min'=>'0', 'max'=>'10'),
					'failed-login-lock-time'=>array('label'=>$L['failed-login-lock-time'], 'name' => 'settings[num_login_lockout_time]', 'type'=>'num', 'value'=>$settings['num_login_lockout_time'], 'tip'=>$L['failed-login-lock-time-tip'], 'min'=>'1', 'max'=>'99999'),
					'notify-the-user-about-remaining-retries'=>array('label'=>$L['notify-the-user-about-remaining-retries'], 'name' => 'settings[notify_the_user_about_remaining_retries]', 'type'=>'checkbox', 'value'=>$settings['notify_the_user_about_remaining_retries'], 'tip'=>null ),
					'notify-the-user-when-a-failed-login-attempt-occurs'=>array('label'=>$L['notify-the-user-when-a-failed-login-attempt-occurs'], 'name' => 'settings[notify_the_user_failed_login]', 'type'=>'checkbox', 'value'=>$settings['notify_the_user_failed_login'], 'tip'=>__( 'notify-the-user-when-a-failed-login-attempt-occurs-tip' ) ),
					'hide-captcha-for-logged-in-users'=>array('label'=>$L['hide-captcha-for-logged-in-users'], 'name' => 'settings[hide_captcha_logged_in_users]', 'type'=>'checkbox', 'value'=>$settings['hide_captcha_logged_in_users'], 'tip'=>null ),
					'put-spam-in-the-spam-folder-for-review'=>array('label'=>$L['put-spam-in-the-spam-folder-for-review'], 'name' => 'settings[put_spam_in_spam_folder]', 'type'=>'checkbox', 'value'=>( isset( $settings['put_spam_in_spam_folder'] ) ? $settings['put_spam_in_spam_folder'] : false ), 'tip'=>$L['put-spam-in-the-spam-folder-for-review-tip'] ),
					'enable-honeypot-spam-fields'=>array('label'=>$L['enable-honeypot-spam-fields'], 'name' => 'settings[enable_honeypot]', 'type'=>'checkbox', 'value'=>$settings['enable_honeypot'], 'tip'=>$L['enable-honeypot-spam-fields-tip']),
					'enabled-in'=>array('label'=>$L['enabled-in'], 'name' => 'settings[show_captcha_in_forms]', 'type'=>'select', 'value'=>$settings['show_captcha_in_forms'], 'tip'=>$L['enabled-in-tip'], 'firstNull' => false, 'disabled' => false, 'data' => $secFormForms ),
					'enable-recaptcha'=>array('label'=>$L['enable-recaptcha'], 'name' => 'settings[enable_recaptcha]', 'type'=>'select', 'value'=>$settings['enable_recaptcha'], 'tip'=>$L['enable-recaptcha-tip'], 'firstNull' => false, 'disabled' => false, 'data' => $secFormData ),
					'recaptcha-site-key'=>array( 'label'=>$L['recaptcha-site-key'], 'type'=>'text', 'name' => 'settings[recaptcha_site_key]', 'value' => $settings['recaptcha_site_key'], 'tip'=>$L['recaptcha-site-key-tip'] ),
					'recaptcha-secret-key'=>array( 'label'=>$L['recaptcha-secret-key'], 'type'=>'text', 'name' => 'settings[recaptcha_secret_key]', 'value' => $settings['recaptcha_secret_key'], 'tip'=>$L['recaptcha-secret-key-tip'] ),
					
					'add-access-control-allow-origin-response'=>array('label'=>$L['add-access-control-allow-origin-response'], 'name' => 'settings[add_allow_origin_tag]', 'type'=>'checkbox', 'value'=>$settings['add_allow_origin_tag'], 'tip'=>$L['add-access-control-allow-origin-response-tip']),
					
					'referrer-policy'=>array('label'=>$L['referrer-policy'], 'type'=>'select', 'name' => 'settings[referrer_policy]', 'value'=>$settings['referrer_policy'], 'tip'=>$L['referrer-policy-tip'], 'firstNull' => false, 'data' => $secReferrerForms )
				)
			),
		)
	)
);