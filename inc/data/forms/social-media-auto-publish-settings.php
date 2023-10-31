<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Social Media Auto Publish Form
#
#####################################################

$buttons = array(
	'author-link' => array( 'title' => __( 'author-link' ), 'var' => '{{author-link}} ' ),
	'author-name' => array( 'title' => __( 'author-name' ), 'var' => '{{author-name}} ' ),
	'post-description' => array( 'title' => __( 'post-description' ), 'var' => '{{post-description}} ' ),
	'post-link' => array( 'title' => __( 'post-link' ), 'var' => '{{post-link}} ' ),
	'post-title' => array( 'title' => __( 'post-title' ), 'var' => '{{post-title}} ' ),
	'post-date' => array( 'title' => __( 'post-date' ), 'var' => '{{post-date}} ' ),
	'site-link' => array( 'title' => __( 'site-link' ), 'var' => '{{site-link}} ' ),
	'site-title' => array( 'title' => __( 'site-title' ), 'var' => '{{site-title}} ' ),
	'site-slogan' => array( 'title' => __( 'site-slogan' ), 'var' => '{{site-slogan}} ' ),
	'site-description' => array( 'title' => __( 'site-description' ), 'var' => '{{site-description}} ' ),
);

$pubSettingsArray = array(
	'all' => array( 'name' => 'all', 'title'=> __( 'auto-publish-posts-pages-custom-post-types' ), 'disabled' => false, 'data' => array() ),
	'posts' => array( 'name' => 'posts', 'title'=> __( 'auto-publish-posts-to-social-media' ), 'disabled' => false, 'data' => array() ),
	'pages' => array( 'name' => 'pages', 'title'=> __( 'auto-publish-pages-to-social-media' ), 'disabled' => false, 'data' => array() ),
	'manual' => array( 'name' => 'manual', 'title'=> __( 'use-settings-from-post-creation' ), 'disabled' => false, 'data' => array() ),
);

$temp = Json( $this->adminSettings::Get()['auto_social_data'] );

$lang = 'lang-' . $this->GetLang();

if ( !empty( $temp ) && isset( $temp[$lang] ) )
{
	$data = $temp[$lang];
}
else
{
	$data = array();
}

$form = array
(
	'fb-settings' => array
	(
		'title' => __( 'facebook-settings' ),
		'data' => array
		(
			'facebook-information' => array(
				'title' => null, 'tip' => null, 'data' => array
				(
					'enable-auto-publish-post-to-facebook'=> array('label'=> __( 'enable-auto-publish-post-to-facebook' ), 'name' => 'facebook[enable_auto_publish_post_to_facebook]', 'type'=>'checkbox', 'value'=>( isset( $data['social']['facebook']['enable'] ) ? $data['social']['facebook']['enable'] : null ), 'tip'=>null ),
					
					'app-id'=>array( 'label'=>__( 'app-id' ), 'type'=>'text', 'name' => 'facebook[app_id]', 'value' => ( isset( $data['social']['facebook']['app_id'] ) ? $data['social']['facebook']['app_id'] : '' ), 'tip'=> null ),
					
					'app-secret'=>array( 'label'=>__( 'app-secret' ), 'type'=>'text', 'name' => 'facebook[app_secret]', 'value' => ( isset( $data['social']['facebook']['app_secret'] ) ? $data['social']['facebook']['app_secret'] : '' ), 'tip'=> null ),
					
					'message-format-for-posting'=>array('label'=>__( 'message-format-for-posting' ), 'type'=>'textarea', 'name' => 'facebook[format]', 'value' => ( isset( $data['social']['facebook']['format'] ) ? $data['social']['facebook']['format'] : 'New post added at {{site-title}} - {{post-title}} - {{post-link}}' ), 'tip'=>__( 'message-format-for-posting-tip' ), 'buttons' =>  $buttons )
				)
			)
		)
	),
	
	'twitter-settings' => array
	(
		'title' => __( 'twitter-settings' ),
		'data' => array
		(
			'twitter-information' => array(
				'title' => null, 'tip' => null, 'data' => array
				(
					'enable-auto-publish-post-to-twitter'=> array('label'=> __( 'enable-auto-publish-post-to-twitter' ), 'name' => 'twitter[enable_auto_publish_post_to_facebook]', 'type'=>'checkbox', 'value'=>( isset( $data['social']['twitter']['enable'] ) ? $data['social']['twitter']['enable'] : null ), 'tip'=>null ),
					
					'app-id'=>array( 'label'=>__( 'app-id' ), 'type'=>'text', 'name' => 'facebook[app_id]', 'value' => ( isset( $data['social']['facebook']['app_id'] ) ? $data['social']['facebook']['app_id'] : '' ), 'tip'=> null ),
					
					'app-secret'=>array( 'label'=>__( 'app-secret' ), 'type'=>'text', 'name' => 'facebook[app_secret]', 'value' => ( isset( $data['social']['facebook']['app_secret'] ) ? $data['social']['facebook']['app_secret'] : '' ), 'tip'=> null ),
					
					'message-format-for-posting'=>array('label'=>__( 'message-format-for-posting' ), 'type'=>'textarea', 'name' => 'facebook[format]', 'value' => ( isset( $data['social']['facebook']['format'] ) ? $data['social']['facebook']['format'] : 'New post added at {{site-title}} - {{post-title}} - {{post-link}}' ), 'tip'=>__( 'message-format-for-posting-tip' ), 'buttons' =>  $buttons ),
					
					'twitter-info'=>array( 'label'=>__( 'instructions-to-get-api-keys' ), 'type'=>'simple-text', 'name' => null, 'value' => __( 'get-twitter-api-tip' ), 'tip'=> null )
				)
			)
		)
	),

	'general-settings' => array
	(
		'title' => __( 'general-settings' ),
		'data' => array
		(
			'general-settings' => array(
				'title' => null, 'data' => array
				(
					'auto-publish'=>array('label'=> __( 'auto-publish-posts-pages-custom-post-types' ), 'type'=>'select', 'name' => 'settings[auto_publish_method]', 'value'=>( isset( $data['settings']['auto_publish_method'] ) ? $data['settings']['auto_publish_method'] : null ), 'tip'=>null, 'firstNull' => false, 'data' => $pubSettingsArray )
				)
			)
		)
	)
);