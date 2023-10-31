<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Ad Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

$adSettings = Json( $settings['ads_data'] );
//$adNotice = ( ( !empty( $adSettings['ad_notice'] ) && isset( $adSettings['ad_notice'][$this->LangKey()] ) ) ? Json( $adSettings['ad_notice'][$this->LangKey()] ) : null );

$form = array
(
	'ad-settings' => array
	(
		'title' => $L['ad-settings'],
		'data' => array(
			/*
			'ad-block-notice' => array(
				'title' => $L['ad-block-notice'], 'tip' =>$L['ad-block-notice-tip'] . ( $$this->adminSettings::IsTrue( 'enable_multilang', 'site' ) ? '<br />' . sprintf( $L['ad-block-multilang-notice-tip'], $this->LangName() ) : '' ), 'data' => array
				(
					'enable-ad-block-notice'=>array('label'=>$L['enable-ad-block-notice'], 'name' => 'adnotice[' . $this->LangKey() . '][enable_ad_block_notice]', 'type'=>'checkbox', 'value'=>( isset( $adNotice['enable_ad_block_notice'] ) ? $adNotice['enable_ad_block_notice'] : false ), 'tip'=>sprintf( $L['enable-ad-block-notice-tip'], SITE_URL . 'ads.txt' ) ),
					'ad-block-notice-title'=>array( 'label'=>$L['title'], 'type'=>'text', 'name' => 'adnotice[' . $this->LangKey() . '][title]', 'value' => ( isset( $adNotice['title'] ) ? $adNotice['title'] : '' ), 'placeholder' => $L['you-are-blocking-ads'], 'tip'=>$L['you-can-use-html-code-here'] ),
					'ad-block-notice-content'=>array('label'=>$L['content'], 'name' => 'adnotice[' . $this->LangKey() . '][content]', 'type'=>'textarea', 'placeholder' => $L['ad-block-generic-content'], 'value'=>( isset( $adNotice['content'] ) ? $adSettings['content'] : '' ), 'tip'=> $L['you-can-use-html-code-here'] )
				)
			),
			*/
			'generic-settings' => array(
				'title' => $L['generic-settings'], 'tip' =>null, 'data' => array
				(
					'rotate-ads'=>array('label'=>$L['rotate-ads'], 'name' => 'settings[rotate_ads]', 'type'=>'checkbox', 'value'=>( isset( $adSettings['rotate_ads'] ) ? $adSettings['rotate_ads'] : false ), 'tip'=>$L['rotate-ads-tip'] ),
					'hide-ads-from-bot'=>array('label'=>$L['hide-ads-from-bot'], 'name' => 'settings[hide_ads_bot]', 'type'=>'checkbox', 'value'=>( isset( $adSettings['hide_ads_bot'] ) ? $adSettings['hide_ads_bot'] : false ), 'tip'=>$L['hide-ads-from-bot-tip'] )
				)
			)
		)
	),
	
	'ads-txt-settings' => array
	(
		'title' => $L['ads-txt-settings'],
		'data' => array(
			'ads-txt-settings' => array(
				'title' => $L['ads-txt-settings'], 'tip' =>null, 'data' => array
				(
					'enable-ads-txt'=>array('label'=>$L['enable-ads-txt'], 'name' => 'settings[enable_ad_txt]', 'type'=>'checkbox', 'value'=>( isset( $adSettings['enable_ad_txt'] ) ? $adSettings['enable_ad_txt'] : false ), 'tip'=>sprintf( $L['enable-ads-txt-tip'], SITE_URL . 'ads.txt' ) ),
					'ads-txt-content'=>array('label'=>$L['content'], 'name' => 'settings[ad_txt_content]', 'type'=>'textarea', 'placeholder' => 'google.com, pub-0000000000000000, DIRECT, f98c47fec0942fa9', 'value'=>( isset( $adSettings['ad_txt_content'] ) ? html_entity_decode( $adSettings['ad_txt_content'] ) : '' ), 'tip'=> $L['ads-txt-content-tip'] )
				)
			)
		)
	),
	
	'app-ads-txt-settings' => array
	(
		'title' => $L['app-ads-txt-settings'],
		'data' => array(
			'ads-txt-settings' => array(
				'title' => $L['app-ads-txt-settings'], 'tip' =>null, 'data' => array
				(
					'enable-app-ads-txt'=>array('label'=>$L['enable-app-ads-txt'], 'name' => 'settings[enable_app_ad_txt]', 'type'=>'checkbox', 'value'=>( isset( $adSettings['enable_app_ad_txt'] ) ? $adSettings['enable_app_ad_txt'] : false ), 'tip'=>sprintf( $L['enable-app-ads-txt-tip'], SITE_URL . 'app-ads.txt' ) ),
					'app-ads-txt-content'=>array('label'=>$L['content'], 'name' => 'settings[app_ad_txt_content]', 'type'=>'textarea', 'placeholder' => 'google.com, pub-0000000000000000, DIRECT, f98c47fec0942fa9', 'value'=>( isset( $adSettings['app_ad_txt_content'] ) ? html_entity_decode( $adSettings['app_ad_txt_content'] ) : '' ), 'tip'=> $L['ads-txt-content-tip'] )
				)
			)
		)
	)
);