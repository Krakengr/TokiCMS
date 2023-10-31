<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# AMP Settings Form
#
#####################################################
$themes = LoadThemes( 'amp', false );

$L = $this->lang;

$settings = $this->adminSettings::Get();

$ampSettings = Json( $settings['amp_data'] );
$ampThemes = array();
$ampContentTypes = $ampArchiveTypes = array();

if ( !empty( $themes ) )
{
	foreach( $themes as $key => $row )
		$ampThemes[$key] = array( 'name' => $key, 'title'=> $row['title'], 'disabled' => false, 'data' => array() );
}

//Content Types Array
$ampContentTypes['posts'] = array( 'name' => 'posts', 'title'=> __( 'posts' ), 'disabled' => false, 'data' => array() );
$ampContentTypes['pages'] = array( 'name' => 'pages', 'title'=> __( 'pages' ), 'disabled' => false, 'data' => array() );

//Archive Array
$ampArchiveTypes['authors'] = array( 'name' => 'authors', 'title'=> __( 'authors' ), 'disabled' => false, 'data' => array() );
$ampArchiveTypes['categories'] = array( 'name' => 'categories', 'title'=> __( 'categories' ), 'disabled' => false, 'data' => array() );
$ampArchiveTypes['tags'] = array( 'name' => 'tags', 'title'=> __( 'tags' ), 'disabled' => false, 'data' => array() );
$ampArchiveTypes['blogs'] = array( 'name' => 'blogs', 'title'=> __( 'blogs' ), 'disabled' => false, 'data' => array() );
$ampArchiveTypes['homepage'] = array( 'name' => 'homepage', 'title'=> __( 'homepage' ), 'disabled' => false, 'data' => array() );

$form = array
(
	'amp-settings' => array
	(
		'title' => $L['general-settings'],
		'data' => array(
			'general-settings' => array( 
				'title' => null, 'data' => array
				(
					'redirect-mobile-visitors-to-amp'=>array('label'=>$L['redirect-mobile-visitors-to-amp'], 'type'=>'checkbox', 'name' => 'amp[redirect_mobile_visitors]', 'value' => ( isset( $ampSettings['redirect_mobile_visitors'] ) ? $ampSettings['redirect_mobile_visitors'] : false ), 'tip'=>null ),
					'choose-theme'=>array('label'=>$L['choose-theme'], 'name' => 'amp[theme]', 'type'=>'select', 'value'=>( isset( $ampSettings['theme'] ) ? $ampSettings['theme'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $ampThemes ),
					
					'content-types'=>array(
						'label'=>$L['content-types'], 'name' => 'amp[content_types][]', 'type'=>'select', 'value'=>( isset( $ampSettings['content_types'] ) ? $ampSettings['content_types'] : null ), 'tip'=>$L['content-types-tip'], 'firstNull' => false, 'data' => $ampContentTypes, 'id' => 'slcAmp', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>'
					),
					
					'archive-types'=>array(
						'label'=> __( 'archives' ), 'name' => 'amp[archive_types][]', 'type'=>'select', 'value'=>( isset( $ampSettings['archive_types'] ) ? $ampSettings['archive_types'] : null ), 'tip'=>__( 'archive-types-tip' ), 'firstNull' => false, 'data' => $ampArchiveTypes, 'id' => 'slcAmp2', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>'
					),
					
					'enable-auto-ads'=>array('label'=>$L['enable-auto-ads'], 'type'=>'checkbox', 'name' => 'amp[enable_auto_ads]', 'value' => ( isset( $ampSettings['enable_auto_ads'] ) ? $ampSettings['enable_auto_ads'] : false ), 'tip'=>$L['enable-auto-ads-tip'] )
				)
			),
			
			'generic-codes' => array(
				'title' => $L['generic-codes'], 'tip' =>null, 'data' => array
				(
					'google-ad-client-code'=>array('label'=>$L['google-ad-client-code'], 'name' => 'amp[google_ad_client_code]', 'type'=>'text', 'value'=>( isset( $ampSettings['codes']['google_ad_client_code'] ) ? $ampSettings['codes']['google_ad_client_code'] : null ), 'tip'=>sprintf( $L['more-info-s'], 'https://support.google.com/adsense/answer/7183212?hl=en' ), 'placeholder' => 'ca-pub-xxxxxxxxxxxxx' ),
					
					'google-ad-adslot-code'=>array('label'=>$L['google-ad-adslot-code'], 'name' => 'amp[google_ad_adslot_code]', 'type'=>'text', 'value'=>( isset( $ampSettings['codes']['google_ad_adslot_code'] ) ? $ampSettings['codes']['google_ad_adslot_code'] : null ), 'tip'=>sprintf( $L['more-info-s'], 'https://support.google.com/adsense/answer/7183212?hl=en' ), 'placeholder' => 'xxxxxxxxx' ),
					
					'google-analytics-code'=>array('label'=>$L['google-analytics-code'], 'name' => 'amp[google_analytics_code]', 'type'=>'text', 'value'=>( isset( $ampSettings['codes']['google_analytics_code'] ) ? $ampSettings['codes']['google_analytics_code'] : null ), 'tip'=>$L['google-analytics-code-tip'], 'placeholder' => 'UA-XXXXXX-XX' ),
				)
			)
		)
	)
);

unset( $ampThemesData, $ampThemes, $ampSettings );