<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Link Checker Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get()['link_manager_options'];

$settings = Json( $settings );

$short = ( isset( $settings['short-link-settings'] ) ? $settings['short-link-settings'] : array() );
$broken = ( isset( $settings['broken-link-settings'] ) ? $settings['broken-link-settings'] : array() );
$internal = ( isset( $settings['internal-link-settings'] ) ? $settings['internal-link-settings'] : array() );
$external = ( isset( $settings['external-link-settings'] ) ? $settings['external-link-settings'] : array() );

$contentTypes = $contentStatus = $linkTypes = $addRelLinks = $redirection = $catsContent = $groupsAllowed = 
$autoGeneratedPathType = $autoGeneratedPathCase = $adsContent = array();

$ads = GetAdminAds();

$adsContent['disabled'] = array( 'name' => '0', 'title'=> __( 'disabled' ), 'disabled' => false, 'data' => array() );

if ( !empty( $ads ) )
{
	$adsContent['random'] = array( 'name' => '-1', 'title'=> __( 'random' ), 'disabled' => false, 'data' => array() );
	
	foreach( $ads as $ad )
	{
		$adsContent[$ad['id']] = array( 'name' => $ad['id'], 'title'=> $ad['title'], 'disabled' => false, 'data' => array() );
	}
}

$cats = GetAdminCategories( 'name', 'ASC', true );

if ( !empty( $cats ) )
{
	foreach( $cats as $cat )
	{
		$name = $cat['name'] . ( !empty( $cat['blogName'] ) ? ' (' . __( 'blog' ) . ': ' . $cat['blogName'] . ')' : '' );
		
		$catsContent[$cat['id']] = array( 'name' => $cat['id'], 'title'=> $name, 'disabled' => false, 'data' => array() );
	}
}

//Group Settings
$groups = AdminGroups( $this->GetSite(), false );

foreach( $groups as $key => $row )
{
	//Admins can view everything, so don't bother with this group
	if ( $row['id_group'] == 1 )
		continue;
	
	$groupsAllowed[$row['id_group']] = array( 'name' => $row['id_group'], 'title'=> $row['group_name'], 'disabled' => false, 'data' => array() );
}

$autoGeneratedPathType['alphanumeric'] = array( 'name' => 'alphanumeric', 'title'=> __( 'alphanumeric' ), 'disabled' => false, 'data' => array() );
$autoGeneratedPathType['alphabetical'] = array( 'name' => 'alphabetical', 'title'=> __( 'alphabetical' ), 'disabled' => false, 'data' => array() );
$autoGeneratedPathType['numeric'] = array( 'name' => 'numeric', 'title'=> __( 'numeric' ), 'disabled' => false, 'data' => array() );

$autoGeneratedPathCase['any'] = array( 'name' => 'any', 'title'=> __( 'any' ), 'disabled' => false, 'data' => array() );
$autoGeneratedPathCase['lowercase'] = array( 'name' => 'lowercase', 'title'=> __( 'lowercase' ), 'disabled' => false, 'data' => array() );
$autoGeneratedPathCase['uppercase'] = array( 'name' => 'uppercase', 'title'=> __( 'uppercase' ), 'disabled' => false, 'data' => array() );

$redirection['direct'] = array( 'name' => 'direct', 'title'=> $L['direct'], 'disabled' => false, 'data' => array() );
$redirection['301'] = array( 'name' => '301', 'title'=> $L['301-permanent'], 'disabled' => false, 'data' => array() );
$redirection['302'] = array( 'name' => '302', 'title'=> $L['302-temporary'], 'disabled' => false, 'data' => array() );
$redirection['307'] = array( 'name' => '307', 'title'=> $L['307-temporary'], 'disabled' => false, 'data' => array() );
$redirection['meta-refresh'] = array( 'name' => 'meta-refresh', 'title'=> $L['meta-refresh'], 'disabled' => false, 'data' => array() );
$redirection['javascript'] = array( 'name' => 'javascript', 'title'=> $L['javascript'], 'disabled' => false, 'data' => array() );

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
					'enable-settings'=>array('label'=>$L['enable-these-settings'], 'type'=>'checkbox', 'name' => 'external[enable_settings]', 'value' => ( isset( $external['enable_settings'] ) ? $external['enable_settings'] : null ), 'tip'=>null ),
					
					'open-links'=>array('label'=>$L['open-external-links-in-new-tab-window'], 'type'=>'checkbox', 'name' => 'external[open_links_new_tab]', 'value' => ( isset( $external['open_links_new_tab'] ) ? $external['open_links_new_tab'] : null ), 'tip'=>$L['open-external-links-in-new-tab-window-tip'] ),
					
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
					'enable-settings'=>array('label'=>$L['enable-these-settings'], 'type'=>'checkbox', 'name' => 'internal[enable_settings]', 'value' => ( isset( $internal['enable_settings'] ) ? $internal['enable_settings'] : null ), 'tip'=>null ),
					
					'open-links'=>array('label'=>$L['open-internal-links-in-new-tab-window'], 'type'=>'checkbox', 'name' => 'internal[open_links_new_tab]', 'value' => ( isset( $internal['open_links_new_tab'] ) ? $internal['open_links_new_tab'] : null ), 'tip'=>$L['open-internal-links-in-new-tab-window-tip'] ),
					
					'nofollow-links'=>array('label'=>$L['nofollow-internal-links'], 'type'=>'checkbox', 'name' => 'internal[nofollow_links]', 'value' => ( isset( $internal['nofollow_links'] ) ? $internal['nofollow_links'] : null ), 'tip'=>$L['nofollow-internal-links-tip'] ),
					
					'also-add-to-rel-attribute'=>array('label'=>$L['also-add-to-rel-attribute'], 'name' => 'internal[add_rel][]', 'type'=>'select', 'value'=>( isset( $internal['add_rel'] ) ? $internal['add_rel'] : null ), 'tip'=>$L['press-ctrl-and-click-to-deselect-a-value'], 'firstNull' => false, 'data' => $addRelLinks, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3', 'multiple' => true ),
					
					'add-css-class'=>array('label'=>$L['add-css-class'], 'name' => 'internal[css_class]', 'type'=>'text', 'value'=>( isset( $internal['css_class'] ) ? $internal['css_class'] : null ), 'tip'=>$L['add-css-class-tip'] ),

					//'keep-values'=>array('label'=>__( 'overwrite-existing-values' ), 'type'=>'checkbox', 'name' => 'internal[overwrite_existing_values]', 'value' => $internal['overwrite_existing_values'], 'tip'=>$L['overwrite-existing-values-tip'] ),
				)
			)
		)
	),
	
	'short-links-settings' => array
	(
		'title' => $L['short-links-settings'],
		'data' => array(

			'general-settings' => array(
				'title' => null, 'data' => array
				(
					'enable'=>array('label'=>$L['enable'], 'type'=>'checkbox', 'name' => 'short[enable]', 'value' => ( isset( $short['enable'] ) ? $short['enable'] : null ), 'tip'=>null ),
					
					'hr-0'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'redirection'=>array(
						'label'=>$L['redirection'], 'name' => 'short[redirection]', 'type'=>'select', 'value'=>( isset( $short['redirection'] ) ? $short['redirection'] : 'direct' ), 'tip'=>__( 'redirection-tip' ), 'firstNull' => false, 'data' => $redirection, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3'
					),
					
					'base-slug-prefix'=>array('label'=>$L['base-slug-prefix'], 'name' => 'short[base_slug_prefix]', 'type'=>'text', 'value'=>( isset( $short['base_slug_prefix'] ) ? $short['base_slug_prefix'] : 'go' ), 'tip'=>$L['base-slug-prefix-tip'] ),
					
					'slug-character-count'=>array('label'=>$L['slug-character-count'], 'name' => 'short[slug_character_count]', 'type'=>'num', 'value'=>( isset( $short['slug_character_count'] ) ? $short['slug_character_count'] : 4 ), 'tip'=>$L['slug-character-count-tip'], 'min'=>'2', 'max'=>'10'),
					
					'auto-generated-path-type'=>array('label'=>$L['auto-generated-path-type'], 'name' => 'short[autogenerated_path_type]', 'type'=>'select', 'value'=>( isset( $short['autogenerated_path_type'] ) ? $short['autogenerated_path_type'] : 'alphanumeric' ), 'tip'=>$L['auto-generated-path-type-tip'], 'firstNull' => false, 'data' => $autoGeneratedPathType, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3' ),
					
					'auto-generated-path-case'=>array('label'=>$L['auto-generated-path-case'], 'name' => 'short[autogenerated_path_case]', 'type'=>'select', 'value'=>( isset( $short['autogenerated_path_case'] ) ? $short['autogenerated_path_case'] : 'any' ), 'tip'=>$L['auto-generated-path-case-tip'], 'firstNull' => false, 'data' => $autoGeneratedPathCase, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3' ),
					
					'hr-1'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'enable-tracking'=>array('label'=>$L['enable-tracking'], 'type'=>'checkbox', 'name' => 'short[enable_tracking]', 'value' => ( isset( $short['enable_tracking'] ) ? $short['enable_tracking'] : false ), 'tip'=>$L['enable-tracking-tip'] ),
					
					'enable-google-analytics'=>array('label'=>$L['enable-google-analytics'], 'type'=>'checkbox', 'name' => 'short[enable_google_analytics]', 'value' => ( isset( $short['enable_google_analytics'] ) ? $short['enable_google_analytics'] : false ), 'tip'=>$L['enable-google-analytics-tip'] ),
					
					'global-head-scripts'=>array('label'=>$L['global-head-scripts'], 'name' => 'short[global_head_scripts]', 'type'=>'textarea', 'value'=>( isset( $short['global_head_scripts'] ) ? $short['global_head_scripts'] : '' ), 'tip'=>$L['global-head-scripts-tip'] ),
					
					'filter-robots'=>array('label'=>$L['filter-robots'], 'type'=>'checkbox', 'name' => 'short[filter_robots]', 'value' => ( isset( $short['filter_robots'] ) ? $short['filter_robots'] : false ), 'tip'=>$L['filter-robots-tip'] ),
					
					'hr-2'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'post-shortlinks'=>array('label'=>$L['post-shortlinks'], 'type'=>'checkbox', 'name' => 'short[post_shortlinks]', 'value' => ( isset( $short['post_shortlinks'] ) ? $short['post_shortlinks'] : false ), 'tip'=>$L['post-shortlinks-tip'] ),

					'category'=>array('label'=>$L['category'], 'name' => 'short[category]', 'type'=>'select', 'value'=>( isset( $short['category'] ) ? $short['category'] : null ), 'tip'=>$L['short-links-category-tip'], 'firstNull' => true, 'data' => $catsContent, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3' ),
					
					'page-shortlinks'=>array('label'=>$L['page-shortlinks'], 'type'=>'checkbox', 'name' => 'short[page_shortlinks]', 'value' => ( isset( $short['page_shortlinks'] ) ? $short['page_shortlinks'] : false ), 'tip'=>$L['page-shortlinks-tip'] ),
					
					'hr-3'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false, 'hide' => ( !$this->adminSettings::IsTrue( 'enable_link_manager' ) ? true : false ) ),
					
					'show-ads'=>array('label'=>$L['show-ads'], 'name' => 'short[show_ads]', 'type'=>'select', 'value'=>( isset( $short['show_ads'] ) ? $short['show_ads'] : null ), 'tip'=>$L['show-ads-tip'], 'firstNull' => false, 'data' => $adsContent, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3', 'hide' => ( !$this->adminSettings::IsTrue( 'enable_link_manager' ) ? true : false ) ),
					
					'membergroups-ads'=>array('label'=>$L['membergroups'], 'name' => 'short[ads_group][]', 'type'=>'select', 'value'=>( isset( $short['ads_group'] ) ? $short['ads_group'] : null ), 'tip'=>$L['show-ads-groups-tip'], 'firstNull' => false, 'data' => $groupsAllowed, 'id' => 'slcGroupsAds', 'hide' => ( !$this->adminSettings::IsTrue( 'enable_link_manager' ) ? true : false ), 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ), 'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
					
					'hr-4'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'enable-public-links'=>array('label'=>$L['enable-public-links'], 'type'=>'checkbox', 'name' => 'short[enable_public_links]', 'value' => ( isset( $short['enable_public_links'] ) ? $short['enable_public_links'] : false ), 'tip'=>$L['enable-public-links-tip'] ),
					
					'membergroups-allowed'=>array('label'=>$L['membergroups-allowed'], 'name' => 'short[allow_group][]', 'type'=>'select', 'value'=>( isset( $short['allow_group'] ) ? $short['allow_group'] : null ), 'tip'=>$L['membergroups-allowed-public-links-tip'], 'firstNull' => false, 'data' => $groupsAllowed, 'id' => 'slcReviews', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
					'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
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