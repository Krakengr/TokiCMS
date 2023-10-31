<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Language Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

$form = array
(
	'url-modifications' => array
	(
		'title' => $L['url-modifications'],
		'data' => array(
	
			'hide-default-lang-slug' => array( 
				'title' => null, 'data' => array
				(
					'hide-hide_default_lang_slug-lang-slug'=>array('label'=>$L['hide-default-lang-slug'], 'type'=>'checkbox', 'name' => 'settings[hide_default_lang_slug]', 'value' => $settings['hide_default_lang_slug'], 'tip'=>$L['hide-default-lang-slug-tip'])
				)
			),
			
			'language-set-from-code' => array( 
				'title' => null, 'data' => array
				(
					'language-set-from-code'=>array('label'=>$L['language-set-from-code'], 'type'=>'checkbox', 'name' => 'settings[language_set_from_code]', 'value' => $settings['language_set_from_code'], 'tip'=>$L['language-set-from-code-tip'])
				)
			),
			
			'detect-browser-language' => array( 
				'title' => null, 'data' => array
				(
					'detect-browser-language'=>array('label'=>$L['detect-browser-language'], 'type'=>'checkbox', 'name' => 'settings[detect_browser_language]', 'value' => $settings['detect_browser_language'], 'tip'=>$L['detect-browser-language-tip'])
				)
			),
			
			'share-slugs' => array( 
				'title' => null, 'data' => array
				(
					'share-slugs'=>array('label'=>$L['share-slugs'], 'type'=>'checkbox', 'name' => 'settings[share_slugs]', 'value' => $settings['share_slugs'], 'tip'=>$L['share-slugs-tip'])
				)
			),
			
			'share-images' => array( 
				'title' => null, 'data' => array
				(
					'share-images'=>array('label'=>$L['share-images'], 'type'=>'checkbox', 'name' => 'settings[share_images_langs]', 'value' => $settings['share_images_langs'], 'tip'=>$L['share-images-tip'])
				)
			),
			
			'translate-slugs' => array( 
				'title' => null, 'data' => array
				(
					'translate-slugs'=>array('label'=>$L['translate-url-slugs'], 'type'=>'checkbox', 'name' => 'settings[translate_slugs]', 'value' => $settings['translate_slugs'], 'tip'=>$L['translate-url-slugs-tip'])
				)
			),
		)
	)
);