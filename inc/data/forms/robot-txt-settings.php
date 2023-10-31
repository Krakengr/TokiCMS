<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Robots txt Settings Form
#
#####################################################
$L = $this->lang;
$settings = $this->adminSettings::Get();

$robots = $this->adminSettings::Robots();

#####################################################
#
# robots.txt Data Array
#
#####################################################
$robotsTxtData = array();

$robotsTxtData['disable'] = array( 'name' => 'disable', 'title'=> $L['disable'], 'disabled' => false, 'data' => array() );
$robotsTxtData['disallow'] = array( 'name' => 'disallow', 'title'=> $L['disallow'], 'disabled' => false, 'data' => array() );
$robotsTxtData['allow'] = array( 'name' => 'allow', 'title'=> $L['allow'], 'disabled' => false, 'data' => array() );

$form = array
(
	'robot-txt' => array
	(
		'title' => $L['robots-txt-settings'],
		'data' => array(
		
			'robot-txt-settings' => array( 
				'title' => $L['robots-txt-settings'], 'tip'=>null, 'data' => array
				(
					'enable-robots-txt'=>array('label'=>$L['enable-robots-txt'], 'type'=>'checkbox', 'name' => 'settings[enable_robots_txt]', 'value' => $settings['enable_robots_txt'], 'tip'=>sprintf( $L['enable-robots-txt-tip'], SITE_URL . 'robots.txt' ) ),
					
					'add-your-sitemap-in-the-robots-file'=>array('label'=>$L['add-your-sitemap-in-the-robots-file'], 'type'=>'checkbox', 'name' => 'robots[add_your_sitemap_in_the_robots_file]', 'value' => ( isset( $robots['add_your_sitemap_in_the_robots_file'] ) ? $robots['add_your_sitemap_in_the_robots_file'] : false ), 'tip'=>$L['add-your-sitemap-in-the-robots-file-tip'] ),
					
					'allow-ads-txt'=>array('label'=>$L['allow-ads-txt'], 'type'=>'checkbox', 'name' => 'robots[add_ads_in_the_robots_file]', 'value' => ( isset( $robots['add_ads_in_the_robots_file'] ) ? $robots['add_ads_in_the_robots_file'] : false ), 'tip'=>$L['allow-ads-txt-tip'] ),
					
					'allow-app-ads-txt'=>array('label'=>$L['allow-app-ads-txt'], 'type'=>'checkbox', 'name' => 'robots[add_app_ads_in_the_robots_file]', 'value' => ( isset( $robots['add_app_ads_in_the_robots_file'] ) ? $robots['add_app_ads_in_the_robots_file'] : false ), 'tip'=>$L['allow-app-ads-txt-tip'] ),
					
					'stop-crawling-useless-links'=>array('label'=>$L['stop-crawling-useless-links'], 'type'=>'checkbox', 'name' => 'robots[add_stop_crawling_in_the_robots_file]', 'value' => ( isset( $robots['add_stop_crawling_in_the_robots_file'] ) ? $robots['add_stop_crawling_in_the_robots_file'] : false ), 'tip'=>$L['stop-crawling-useless-links-tip'] ),
					
					'spam-backlink-blocker'=>array('label'=>$L['spam-backlink-blocker'], 'type'=>'checkbox', 'name' => 'robots[add_spam_backlink_blocker_in_the_robots_file]', 'value' => ( isset( $robots['add_spam_backlink_blocker_in_the_robots_file'] ) ? $robots['add_spam_backlink_blocker_in_the_robots_file'] : false ), 'tip'=>$L['spam-backlink-blocker-tip'] ),
					
					'bad-bot-blocker'=>array('label'=>$L['bad-bot-blocker'], 'type'=>'checkbox', 'name' => 'robots[add_bad_bot_blocker_in_the_robots_file]', 'value' => ( isset( $robots['add_bad_bot_blocker_in_the_robots_file'] ) ? $robots['add_bad_bot_blocker_in_the_robots_file'] : false ), 'tip'=>$L['bad-bot-blocker-tip'] ),
					
					'backlink-protector'=>array('label'=>$L['backlink-protector'], 'type'=>'checkbox', 'name' => 'robots[add_backlink_protector_in_the_robots_file]', 'value' => ( isset( $robots['add_backlink_protector_in_the_robots_file'] ) ? $robots['add_backlink_protector_in_the_robots_file'] : false ), 'tip'=>$L['backlink-protector-tip'] ),
					
					'crawl-delay'=>array('label'=>$L['crawl-delay'], 'name' => 'robots[crawl_delay]', 'type'=>'num', 'value'=>( isset( $robots['crawl_delay'] ) ? $robots['crawl_delay'] : 0 ), 'tip'=>$L['crawl-delay-tip'], 'min'=>'0', 'max'=>'200'),
					
				)
			),
			
			'robot-txt-crawl-settings' => array( 
				'title' => $L['robot-txt-crawl-settings'], 'tip'=>$L['robots-txt-tip'], 'data' => array
				(
					'google-bot'=>array('label'=>$L['google-bot'], 'name' => 'robots[show_google_bot_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_google_bot_in_robots'] ) ? $robots['show_google_bot_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'google-images'=>array('label'=>$L['google-images'], 'name' => 'robots[show_google_images_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_google_images_in_robots'] ) ? $robots['show_google_images_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'google-media-partners'=>array('label'=>$L['google-media-partners'], 'name' => 'robots[show_media_partners_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_media_partners_in_robots'] ) ? $robots['show_media_partners_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'google-adsbot'=>array('label'=>$L['google-adsbot'], 'name' => 'robots[show_google_adsbot_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_google_adsbot_in_robots'] ) ? $robots['show_google_adsbot_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'google-mobile'=>array('label'=>$L['google-mobile'], 'name' => 'robots[show_google_mobile_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_google_mobile_in_robots'] ) ? $robots['show_google_mobile_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'bing-bot'=>array('label'=>$L['bing-bot'], 'name' => 'robots[show_bing_bot_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_bing_bot_in_robots'] ) ? $robots['show_bing_bot_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'msn-bot'=>array('label'=>$L['msn-bot'], 'name' => 'robots[show_msn_bot_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_msn_bot_in_robots'] ) ? $robots['show_msn_bot_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'msn-bot-media'=>array('label'=>$L['msn-bot-media'], 'name' => 'robots[show_msn_bot_media_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_msn_bot_media_in_robots'] ) ? $robots['show_msn_bot_media_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'apple-bot'=>array('label'=>$L['apple-bot'], 'name' => 'robots[show_apple_bot_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_apple_bot_in_robots'] ) ? $robots['show_apple_bot_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'yandex-bot'=>array('label'=>$L['yandex-bot'], 'name' => 'robots[show_yandex_bot_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_yandex_bot_in_robots'] ) ? $robots['show_yandex_bot_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'yandex-images'=>array('label'=>$L['yandex-images'], 'name' => 'robots[show_yandex_images_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_yandex_images_in_robots'] ) ? $robots['show_yandex_images_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'yahoo-search'=>array('label'=>$L['yahoo-search'], 'name' => 'robots[show_yahoo_search_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_yahoo_search_in_robots'] ) ? $robots['show_yahoo_search_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'duckduckgo-search'=>array('label'=>$L['duckduckgo-search'], 'name' => 'robots[show_duckduckgo_search_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_duckduckgo_search_in_robots'] ) ? $robots['show_duckduckgo_search_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'qwant'=>array('label'=>$L['qwant'], 'name' => 'robots[show_qwant_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_qwant_in_robots'] ) ? $robots['show_qwant_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					'baidu-sogou-soso-youdao'=>array('label'=>$L['baidu-sogou-soso-youdao'], 'name' => 'robots[show_chinese_search_engines_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_chinese_search_engines_in_robots'] ) ? $robots['show_chinese_search_engines_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'facebook-instagram-whatsapp'=>array('label'=>$L['facebook-instagram-whatsapp'], 'name' => 'robots[show_facebook_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_facebook_in_robots'] ) ? $robots['show_facebook_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'twitter'=>array('label'=>$L['twitter'], 'name' => 'robots[show_twitter_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_twitter_in_robots'] ) ? $robots['show_twitter_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'linkedin'=>array('label'=>$L['linkedin'], 'name' => 'robots[show_linkedin_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_linkedin_in_robots'] ) ? $robots['show_linkedin_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
					
					'pinterest'=>array('label'=>$L['pinterest'], 'name' => 'robots[show_pinterest_in_robots]', 'type'=>'select', 'value'=>( isset( $robots['show_pinterest_in_robots'] ) ? $robots['show_pinterest_in_robots'] : null ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $robotsTxtData ),
				)
			),
			
			'personalize-robots-txt' => array(
				'title' => null, 'tip'=>null, 'data' => array
				(
					'personalize-robots-txt'=>array('label'=>$L['personalize-robots-txt'], 'type'=>'textarea', 'name' => 'robots[personal_code]', 'value' => ( isset( $robots['personal_code'] ) ? $robots['personal_code'] : null ), 'tip'=>$L['personalize-robots-txt-tip'])
				)
				
			)
		)
	)

);