<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Link Checker Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get()['link_manager_options'];

$settings = Json( $settings );

$broken = ( isset( $settings['broken-link-settings'] ) ? $settings['broken-link-settings'] : array() );
$internal = ( isset( $settings['internal-link-settings'] ) ? $settings['internal-link-settings'] : array() );
$external = ( isset( $settings['external-link-settings'] ) ? $settings['external-link-settings'] : array() );

$contentTypes = $contentStatus = $linkTypes = $addRelLinks = array();

$addRelLinks['noopener'] = array( 'name' => 'noopener', 'title'=> $L['add-noopener'], 'disabled' => false, 'data' => array() );
$addRelLinks['noreferrer'] = array( 'name' => 'noreferrer', 'title'=> $L['add-noreferrer'], 'disabled' => false, 'data' => array() );
$addRelLinks['external'] = array( 'name' => 'external', 'title'=> $L['add-external'], 'disabled' => false, 'data' => array() );
$addRelLinks['sponsored'] = array( 'name' => 'sponsored', 'title'=> $L['add-sponsored'], 'disabled' => false, 'data' => array() );
$addRelLinks['ugc'] = array( 'name' => 'ugc', 'title'=> $L['add-ugc'], 'disabled' => false, 'data' => array() );

$contentTypes['posts'] = array( 'name' => 'posts', 'title'=> $L['posts'], 'disabled' => false, 'data' => array() );
$contentTypes['pages'] = array( 'name' => 'pages', 'title'=> $L['pages'], 'disabled' => false, 'data' => array() );
$contentTypes['comments'] = array( 'name' => 'comments', 'title'=> $L['comments'], 'disabled' => false, 'data' => array() );

$contentStatus['published'] = array( 'name' => 'published', 'title'=> $L['published'], 'disabled' => false, 'data' => array() );
$contentStatus['draft'] = array( 'name' => 'draft', 'title'=> $L['draft'], 'disabled' => false, 'data' => array() );
$contentStatus['scheduled'] = array( 'name' => 'scheduled', 'title'=> $L['scheduled'], 'disabled' => false, 'data' => array() );
$contentStatus['pending'] = array( 'name' => 'pending', 'title'=> $L['pending'], 'disabled' => false, 'data' => array() );

$linkTypes['html-links'] = array( 'name' => 'html-links', 'title'=> $L['html-links'], 'disabled' => false, 'data' => array() );
$linkTypes['html-images'] = array( 'name' => 'html-images', 'title'=> $L['html-images'], 'disabled' => false, 'data' => array() );
$linkTypes['plaintext-urls'] = array( 'name' => 'plaintext-urls', 'title'=> $L['plaintext-urls'], 'disabled' => false, 'data' => array() );
$linkTypes['embedded-youtube-videos'] = array( 'name' => 'embedded-youtube-videos', 'title'=> $L['embedded-youtube-videos'], 'disabled' => false, 'data' => array() );
 
 
$form = array
(
	'external-links' => array
	(
		'title' => $L['external-links'],
		'data' => array(

			'generic-settings' => array( 
				'title' => null, 'data' => array
				(
					'enable-settings'=>array('label'=>$L['enable-these-settings'], 'type'=>'checkbox', 'name' => 'external[enable_settings]', 'value' => $external['enable_settings'], 'tip'=>null ),
					
					'open-links'=>array('label'=>$L['open-external-links-in-new-tab-window'], 'type'=>'checkbox', 'name' => 'external[open_links_new_tab]', 'value' => $external['open_links_new_tab'], 'tip'=>$L['open-external-links-in-new-tab-window-tip'] ),
					
					'nofollow-links'=>array('label'=>$L['nofollow-external-links'], 'type'=>'checkbox', 'name' => 'external[nofollow_links]', 'value' => ( isset( $external['nofollow_links'] ) ? $external['nofollow_links'] : null ), 'tip'=>$L['nofollow-external-links-tip'] ),
					
					'also-add-to-rel-attribute'=>array('label'=>$L['also-add-to-rel-attribute'], 'name' => 'external[add_rel][]', 'type'=>'select', 'value'=>( isset( $external['add_rel'] ) ? $external['add_rel'] : null ), 'tip'=>$L['press-ctrl-and-click-to-deselect-a-value'], 'firstNull' => false, 'data' => $addRelLinks, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3', 'multiple' => true ),
					
					'add-css-class'=>array('label'=>$L['add-css-class'], 'name' => 'external[css_class]', 'type'=>'text', 'value'=>( isset( $external['css_class'] ) ? $external['css_class'] : null ), 'tip'=>$L['add-css-class-tip'] ),

					//'keep-values'=>array('label'=>__( 'overwrite-existing-values' ), 'type'=>'checkbox', 'name' => 'external[overwrite_existing_values]', 'value' => $external['overwrite_existing_values'], 'tip'=>$L['overwrite-existing-values-tip'] ),
				)
			)
		)
	),
	
	'internal-links' => array
	(
		'title' => $L['internal-links'],
		'data' => array(
		
			'generic-settings' => array(
				'title' => null, 'data' => array
				(
					'enable-settings'=>array('label'=>$L['enable-these-settings'], 'type'=>'checkbox', 'name' => 'internal[enable_settings]', 'value' => $internal['enable_settings'], 'tip'=>null ),
					
					'open-links'=>array('label'=>$L['open-internal-links-in-new-tab-window'], 'type'=>'checkbox', 'name' => 'internal[open_links_new_tab]', 'value' => $external['open_links_new_tab'], 'tip'=>$L['open-internal-links-in-new-tab-window-tip'] ),
					
					'nofollow-links'=>array('label'=>$L['nofollow-internal-links'], 'type'=>'checkbox', 'name' => 'internal[nofollow_links]', 'value' => ( isset( $internal['nofollow_links'] ) ? $internal['nofollow_links'] : null ), 'tip'=>$L['nofollow-internal-links-tip'] ),
					
					'also-add-to-rel-attribute'=>array('label'=>$L['also-add-to-rel-attribute'], 'name' => 'internal[add_rel][]', 'type'=>'select', 'value'=>( isset( $internal['add_rel'] ) ? $internal['add_rel'] : null ), 'tip'=>$L['press-ctrl-and-click-to-deselect-a-value'], 'firstNull' => false, 'data' => $addRelLinks, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3', 'multiple' => true ),
					
					'add-css-class'=>array('label'=>$L['add-css-class'], 'name' => 'internal[css_class]', 'type'=>'text', 'value'=>( isset( $internal['css_class'] ) ? $internal['css_class'] : null ), 'tip'=>$L['add-css-class-tip'] ),

					//'keep-values'=>array('label'=>__( 'overwrite-existing-values' ), 'type'=>'checkbox', 'name' => 'internal[overwrite_existing_values]', 'value' => $internal['overwrite_existing_values'], 'tip'=>$L['overwrite-existing-values-tip'] ),
				)
			)
		)
	),

	'broken-link-checker-settings' => array
	(
		'title' => $L['broken-link-checker-settings'],
		'data' => array(

			'general-settings' => array(
				'title' => null, 'data' => array
				(
					'content-types'=>array(
						'label'=>$L['look-for-links-in'], 'name' => 'broken[content_types][]', 'type'=>'select', 'value'=>( isset( $broken['content_types'] ) ? $broken['content_types'] : null ), 'tip'=>__( 'broken-link-included-tip' ), 'firstNull' => false, 'data' => $contentTypes, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3', 'multiple' => true
					),
					
					'content-status'=>array(
						'label'=>$L['post-statuses'], 'name' => 'broken[content_status][]', 'type'=>'select', 'value'=>( isset( $broken['content_status'] ) ? $broken['content_status'] : null ), 'tip'=>__( 'broken-link-included-tip' ), 'firstNull' => false, 'data' => $contentStatus, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3', 'multiple' => true
					),
					
					'link-types'=>array(
						'label'=>$L['link-types'], 'name' => 'broken[link_types][]', 'type'=>'select', 'value'=>( isset( $broken['link_types'] ) ? $broken['link_types'] : null ), 'tip'=>__( 'broken-link-included-tip' ), 'firstNull' => false, 'data' => $linkTypes, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3', 'multiple' => true
					),
	
					'post-modified-date'=>array('label'=>$L['post-modified-date'], 'type'=>'checkbox', 'name' => 'broken[post_modified_date]', 'value' => ( isset( $broken['post_modified_date'] ) ? $broken['post_modified_date'] : false ), 'tip'=>$L['post-modified-date-tip'] ),
				)
			)
		)
	)	
);

unset( $ampThemesData, $ampThemes, $ampSettings );