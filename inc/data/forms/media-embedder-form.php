<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Media Embedder Settings Form
#
#####################################################
$L = $this->lang;
$settings = $this->adminSettings::Get();

include ( ARRAYS_ROOT . 'generic-arrays.php');

$embedderSettings = Json( $this->adminSettings::Get()['embedder_data'] );

$form = array
(
	'media-embedder' => array
	(
		'title' => $L['auto-embedder-settings'],
		'data' => array(
			'embedder-settings' => array(
				'title' => null, 'tip' =>null, 'data' => array
				(
					'enable-media-embedder'=>array('label'=>$L['enable-media-embedder'], 'name' => 'settings[enable_media_embedder]', 'type'=>'checkbox', 'value'=>( isset( $this->adminSettings::Get()['enable_media_embedder'] ) ? $this->adminSettings::IsTrue( 'enable_media_embedder' ) : false ), 'tip'=>$L['enable-media-embedder-tip'] ),
					
					'default-video-player-height'=>array('label'=>$L['default-video-player-height'], 'name' => 'embedder[default_video_player_height]', 'type'=>'num', 'value'=>( isset( $embedderSettings['default_video_player_height'] ) ? $embedderSettings['default_video_player_height'] : 0 ), 'tip'=>$L['use-media-players-default'], 'min'=>'0', 'max'=>'2000'),
					
					'default-video-player-width'=>array('label'=>$L['default-video-player-width'], 'name' => 'embedder[default_video_player_width]', 'type'=>'num', 'value'=>( isset( $embedderSettings['default_video_player_width'] ) ? $embedderSettings['default_video_player_width'] : 0 ), 'tip'=>$L['use-media-players-default'], 'min'=>'0', 'max'=>'2000'),
					
					'default-video-player-height-amp'=>array('label'=>$L['default-video-player-height-amp'], 'name' => 'embedder[default_video_player_height_amp]', 'type'=>'num', 'value'=>( isset( $embedderSettings['default_video_player_height_amp'] ) ? $embedderSettings['default_video_player_height_amp'] : 0 ), 'tip'=>$L['use-media-players-default'], 'min'=>'0', 'max'=>'2000'),
					
					'default-video-player-width-amp'=>array('label'=>$L['default-video-player-width-amp'], 'name' => 'embedder[default_video_player_width_amp]', 'type'=>'num', 'value'=>( isset( $embedderSettings['default_video_player_width_amp'] ) ? $embedderSettings['default_video_player_width_amp'] : 0 ), 'tip'=>$L['use-media-players-default'], 'min'=>'0', 'max'=>'2000'),
					
					'maximum-number-of-embeds-to-show-per-post'=>array('label'=>$L['maximum-number-of-embeds-to-show-per-post'], 'name' => 'embedder[maximum_number_of_embeds]', 'type'=>'num', 'value'=>( isset( $embedderSettings['maximum_number_of_embeds'] ) ? $embedderSettings['maximum_number_of_embeds'] : 0 ), 'tip'=>$L['maximum-number-of-embeds-tip'], 'min'=>'0', 'max'=>'10'),
					
					'disable-embedding-in-mobile-devices'=>array('label'=>$L['disable-embedding-in-amp-mode'], 'name' => 'embedder[disable_embeding_in_mobile]', 'type'=>'checkbox', 'value'=>( isset( $embedderSettings['disable_embeding_in_mobile'] ) ? $embedderSettings['disable_embeding_in_mobile'] : false ), 'tip'=>null ),
					
					'disable-embedding-in-the-comments'=>array('label'=>$L['disable-embedding-in-the-comments'], 'name' => 'embedder[disable_embedding_in_comments]', 'type'=>'checkbox', 'value'=>( isset( $embedderSettings['disable_embedding_in_comments'] ) ? $embedderSettings['disable_embedding_in_comments'] : false ), 'tip'=>null ),
					
					'enable-auto-embed-text-links'=>array('label'=>$L['enable-auto-embed-of-text-links'], 'name' => 'embedder[enable_auto_embed_text_links]', 'type'=>'checkbox', 'value'=>( isset( $embedderSettings['enable_auto_embed_text_links'] ) ? $embedderSettings['enable_auto_embed_text_links'] : false ), 'tip'=>$L['enable-auto-embed-of-text-links-tip'] ),

					'show-original-link-embed-after-embed'=>array('label'=>$L['show-original-link-embed-after-embed'], 'name' => 'embedder[show_original_link]', 'type'=>'checkbox', 'value'=>( isset( $embedderSettings['show_original_link'] ) ? $embedderSettings['show_original_link'] : false ), 'tip'=>null ),
					
					'restrict-auto-embedding'=>array('label'=>$L['restrict-auto-embedding'], 'name' => 'embedder[sources][]', 'type'=>'select', 'value'=>( isset( $embedderSettings['sources'] ) ? $embedderSettings['sources'] : null ), 'tip'=>$L['restrict-auto-embedding-tip'], 'firstNull' => false, 'data' => $EmbedContentSources, 'id' => 'slcAmp', 'class' => 'form-control form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ), 'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
				)
			)
		)
	)
);