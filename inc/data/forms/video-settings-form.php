<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Video Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

$videoSettings = Json( $settings['video_data'] );

$form = array
(
	'video-settings' => array
	(
		'title' => $L['seo-videos-settings'],
		'data' => array(
			'video-settings' => array(
				'title' => null, 'tip' =>$L['video-settings-tip'], 'data' => array
				(
					'enable-videos-sitemap'=>array('label'=>$L['enable-videos-sitemap'], 'name' => 'settings[enable_videos_sitemap]', 'type'=>'checkbox', 'value'=>( isset( $videoSettings['enable_videos_sitemap'] ) ? $videoSettings['enable_videos_sitemap'] : false ), 'tip'=>$L['enable-videos-sitemap-tip'] ),
					'include-categories-in-sitemap'=>array('label'=>$L['include-categories-in-sitemap'], 'name' => 'settings[enable_categories_sitemap]', 'type'=>'checkbox', 'value'=>( isset( $videoSettings['enable_categories_sitemap'] ) ? $videoSettings['enable_categories_sitemap'] : false ), 'tip'=>$L['include-categories-in-sitemap-tip'] ),
					'include-tags-in-sitemap'=>array('label'=>$L['include-tags-in-sitemap'], 'name' => 'settings[enable_tags_sitemap]', 'type'=>'checkbox', 'value'=>( isset( $videoSettings['enable_tags_sitemap'] ) ? $videoSettings['enable_tags_sitemap'] : false ), 'tip'=>$L['include-tags-in-sitemap-tip'] ),
					'indexation-videos-in-content'=>array('label'=>$L['indexation-of-videos-in-the-content'], 'name' => 'settings[enable_indexation_videos]', 'type'=>'checkbox', 'value'=>( isset( $videoSettings['enable_indexation_videos'] ) ? $videoSettings['enable_indexation_videos'] : false ), 'tip'=>$L['indexation-of-videos-in-the-content-tip'] ),
				)
			)
		)
	)
);