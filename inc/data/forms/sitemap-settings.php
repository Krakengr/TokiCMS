<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Sitemap Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

$sitemap = Json( $settings['sitemap_data'] );

include ( ARRAYS_ROOT . 'seo-arrays.php');

$newsSitemapData = Json( $settings['news_sitemap_data'] );

$ampContentTypes = $sitemapPriorities = $indexNowEngines = array();

$ampContentTypes['posts'] = array( 'name' => 'posts', 'title'=> $L['posts'], 'disabled' => false, 'data' => array() );
$ampContentTypes['pages'] = array( 'name' => 'pages', 'title'=> $L['pages'], 'disabled' => false, 'data' => array() );

for ( $i = 0; $i < 10; $i++ )
	$sitemapPriorities['0.' . $i] = array( 'name' => '0.' . $i, 'title'=> '0.' . $i, 'disabled' => false, 'data' => array() );

$sitemapPriorities['1.0'] = array( 'name' => '1.0', 'title'=> '1.0', 'disabled' => false, 'data' => array() );

foreach ( $indexNowSearchEngines as $id => $data )
{
	$indexNowEngines[$data['name']] = array( 'name' => $data['name'], 'title'=> $data['title'], 'disabled' => false, 'data' => array() );
}

$sitemapUris = '';

if ( isset( $sitemap['search_engines'] ) && !empty( $sitemap['search_engines'] ) )
{
	$sitemapUris = implode( PHP_EOL, $sitemap['search_engines'] );
}
else
{
	$sitemapUris = 'https://www.google.com/ping?sitemap={{url}}';
}

$form = array
(
	'sitemap' => array
	(
		'title' => $L['sitemap-settings'],
		'data' => array(
		
			'sitemap-settings' => array( 
				'title' => $L['sitemap-settings'], 'tip'=>null, 'data' => array
				(
					'enable-sitemap'=>array('label'=>$L['enable-sitemap'], 'type'=>'checkbox', 'name' => 'settings[enable_sitemap]', 'value' => $settings['enable_sitemap'], 'tip'=>sprintf( $L['enable-sitemap-tip'], $this->SiteUrl() . 'sitemap_index.xml', $this->GetUrl( 'ping-sitemap' ), ( ( isset( $sitemap['last_time_pinged'] ) && !empty( $sitemap['last_time_pinged'] ) ) ? postDate( $sitemap['last_time_pinged'] ) : 'never' ) ) ),
					'notify-search-engines'=>array('label'=>$L['notify-search-engines'], 'type'=>'checkbox', 'name' => 'settings[notify_search_engines]', 'value' => $settings['notify_search_engines'], 'tip'=>$L['notify-search-engines-tip'] ),
					
					'last-modification-time'=>array('label'=>$L['include-the-last-modification-time'], 'type'=>'checkbox', 'name' => 'sitemap[include_the_last_modification_time]', 'value' => ( isset( $sitemap['include_the_last_modification_time'] ) ? $sitemap['include_the_last_modification_time'] : null ), 'tip'=>$L['include-the-last-modification-time-tip'] ),
					
					'search-engines'=>array('label'=>$L['search-engines'], 'type'=>'textarea', 'name' => 'sitemap[search_engines]', 'value' => $sitemapUris, 'tip'=>$L['search-engines-ping-tip']),
					
					'limit-urls'=>array('label'=>$L['limit-sitemap-entries'], 'name' => 'sitemap[limit_urls]', 'type'=>'num', 'value'=>( isset( $sitemap['limit_urls'] ) ? $sitemap['limit_urls'] : 1000 ), 'tip'=>$L['limit-sitemap-entries-tip'], 'min'=>'500', 'max'=>'10000'),
				)
				
			),
			/*
			'google-news-sitemap' => array(
				'title' => $L['google-news-sitemap'], 'tip'=>null, 'data' => array
				(
					'enable-google-news-sitemap'=>array('label'=>$L['enable-google-news-sitemap'], 'type'=>'checkbox', 'name' => 'settings[enable_news_sitemap]', 'value' => $settings['enable_news_sitemap'], 'tip'=>sprintf( $L['enable-google-news-sitemap-tip'], SITE_URL . 'sitemap-news.xml' ) ),
					'publication_name'=>array('label'=>$L['publication-name'], 'type'=>'text', 'name' => 'sitemap[publication_name]', 'value' =>( isset( $newsSitemapData['publication_name'] ) ? $newsSitemapData['publication_name'] : null ), 'tip'=>$L['publication-name-tip']),
					'include-orphan'=>array('label'=>$L['include-orphan-posts'], 'type'=>'checkbox', 'name' => 'sitemap[include_orphan]', 'value' => ( isset( $newsSitemapData['include_orphan'] ) ? $newsSitemapData['include_orphan'] : true ), 'tip'=>$L['include-orphan-posts-tip'] ),
					
					'content-types'=>array('label'=>$L['content-types'], 'name' => 'sitemap[content_types][]', 'type'=>'select', 'value'=>( isset( $newsSitemapData['content_types'] ) ? $newsSitemapData['content_types'] : null ), 'tip'=>$L['content-news-sitemap-types-tip'], 'firstNull' => false, 'data' => $ampContentTypes, 'id' => 'slcAmp', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
				)
				
			),
			
			'sitemap-content' => array(
				'title' => $L['sitemap-content'], 'tip'=>null, 'data' => array
				(
					'include-homepage'=>array('label'=>sprintf( $L['include-s'], $L['homepage'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_homepage]', 'value' => ( isset( $sitemap['include_homepage'] ) ? $sitemap['include_homepage'] : true ), 'tip'=>null ),
					'include-posts'=>array('label'=>sprintf( $L['include-s'], $L['posts'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_posts]', 'value' => ( isset( $sitemap['include_posts'] ) ? $sitemap['include_posts'] : true ), 'tip'=>null ),
					'include-pages'=>array('label'=>sprintf( $L['include-s'], $L['pages'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_pages]', 'value' => ( isset( $sitemap['include_pages'] ) ? $sitemap['include_pages'] : true ), 'tip'=>null ),
					'include-blogs'=>array('label'=>sprintf( $L['include-s'], $L['blogs'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_blogs]', 'value' => ( isset( $sitemap['include_blogs'] ) ? $sitemap['include_blogs'] : true ), 'tip'=>null, 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? false : true ) ),
					'include-langs'=>array('label'=>sprintf( $L['include-s'], $L['langs'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_langs]', 'value' => ( isset( $sitemap['include_langs'] ) ? $sitemap['include_langs'] : true ), 'tip'=>null, 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multilang', 'site' ) ? false : true ) ),
					'include-categories'=>array('label'=>sprintf( $L['include-s'], $L['categories'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_categories]', 'value' => ( isset( $sitemap['include_categories'] ) ? $sitemap['include_categories'] : null ), 'tip'=>null ),
					'include-tags'=>array('label'=>sprintf( $L['include-s'], $L['tags'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_tags]', 'value' => ( isset( $sitemap['include_tags'] ) ? $sitemap['include_tags'] : null ), 'tip'=>null )
				)
				
			),
			
			'sitemap-priorities' => array(
				'title' => $L['sitemap-priorities'], 'tip'=>null, 'data' => array
				(
					'homepage'=>array('label'=>$L['homepage'], 'name' => 'sitemap[homepage_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['homepage_priority'] ) ? $sitemap['homepage_priority'] : '1.0' ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $sitemapPriorities ),
					'posts'=>array('label'=>$L['posts'], 'name' => 'sitemap[posts_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['posts_priority'] ) ? $sitemap['posts_priority'] : '0.6' ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $sitemapPriorities ),
					'pages'=>array('label'=>$L['pages'], 'name' => 'sitemap[pages_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['pages_priority'] ) ? $sitemap['pages_priority'] : '0.6' ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $sitemapPriorities ),
					'blogs'=>array('label'=>$L['blogs'], 'name' => 'sitemap[blogs_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['blogs_priority'] ) ? $sitemap['blogs_priority'] : '0.3' ), 'tip'=>null, 'firstNull' => false, 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? false : true ), 'data' => $sitemapPriorities ),
					'langs'=>array('label'=>$L['langs'], 'name' => 'sitemap[langs_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['langs_priority'] ) ? $sitemap['langs_priority'] : '0.3' ), 'tip'=>null, 'firstNull' => false, 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multilang', 'site' ) ? false : true ), 'data' => $sitemapPriorities ),
					'categories'=>array('label'=>$L['categories'], 'name' => 'sitemap[categories_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['categories_priority'] ) ? $sitemap['categories_priority'] : '0.3' ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $sitemapPriorities ),
					'tags'=>array('label'=>$L['tags'], 'name' => 'sitemap[tags_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['tags_priority'] ) ? $sitemap['tags_priority'] : '0.6' ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $sitemapPriorities ),
					//'products'=>array('label'=>$L['products'], 'name' => 'sitemap[products_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['products_priority'] ) ? $sitemap['products_priority'] : '0.6' ), 'tip'=>null, 'firstNull' => false, 'disabled' => ( $settings['enable_store'] ? false : true ), 'data' => $sitemapPriorities ),
					//'threads'=>array('label'=>$L['threads'], 'name' => 'sitemap[threads_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['threads_priority'] ) ? $sitemap['threads_priority'] : '0.6' ), 'tip'=>null, 'firstNull' => false, 'disabled' => ( $settings['enable_forum'] ? false : true ), 'data' => $sitemapPriorities ),
				
				)
				
			)*/
		)
	),
	
	'google-news-sitemap' => array
	(
		'title' => $L['google-news-sitemap'],
		'data' => array(
		
			'google-news-sitemap' => array( 
				'title' => $L['google-news-sitemap'], 'tip'=>null, 'data' => array
				(
					'enable-google-news-sitemap'=>array('label'=>$L['enable-google-news-sitemap'], 'type'=>'checkbox', 'name' => 'settings[enable_news_sitemap]', 'value' => $settings['enable_news_sitemap'], 'tip'=>sprintf( $L['enable-google-news-sitemap-tip'], SITE_URL . 'sitemap-news.xml' ) ),
					'publication_name'=>array('label'=>$L['publication-name'], 'type'=>'text', 'name' => 'sitemap[publication_name]', 'value' =>( isset( $newsSitemapData['publication_name'] ) ? $newsSitemapData['publication_name'] : null ), 'tip'=>$L['publication-name-tip']),
					'include-orphan'=>array('label'=>$L['include-orphan-posts'], 'type'=>'checkbox', 'name' => 'sitemap[include_orphan]', 'value' => ( isset( $newsSitemapData['include_orphan'] ) ? $newsSitemapData['include_orphan'] : true ), 'tip'=>$L['include-orphan-posts-tip'] ),
					
					'content-types'=>array('label'=>$L['content-types'], 'name' => 'sitemap[content_types][]', 'type'=>'select', 'value'=>( isset( $newsSitemapData['content_types'] ) ? $newsSitemapData['content_types'] : null ), 'tip'=>$L['content-news-sitemap-types-tip'], 'firstNull' => false, 'data' => $ampContentTypes, 'id' => 'slcAmp', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ),
						'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
				)
				
			),
		)
	),
	
	'indexnow-content' => array
	(
		'title' => $L['indexnow'],
		'data' => array(
		
			'sitemap-content' => array( 
				'title' => null, 'tip'=>__( 'indexnow-tip' ), 'data' => array
				(
					'enable-indexnow'=>array('label'=>$L['enable-indexnow'], 'type'=>'checkbox', 'name' => 'sitemap[enable_indexnow]', 'value' => ( isset( $sitemap['enable_indexnow'] ) ? $sitemap['enable_indexnow'] : false ), 'tip'=>__( 'enable-indexnow-tip') ),
					
					'use-the-generic-end-point'=>array('label'=>__( 'use-the-generic-end-point' ), 'type'=>'checkbox', 'name' => 'sitemap[generic_end_point]', 'value' => ( isset( $sitemap['generic_end_point'] ) ? $sitemap['generic_end_point'] : true ), 'tip'=>__( 'use-the-generic-end-point-tip' ) ),
					
					'search-engine'=>array('label'=>$L['select-search-engine'], 'name' => 'sitemap[indexnow_engines][]', 'type'=>'select', 'value'=>( isset( $sitemap['indexnow_engines'] ) ? $sitemap['indexnow_engines'] : null ), 'tip'=>$L['select-search-engine-tip'], 'firstNull' => false, 'data' => $indexNowEngines, 'id' => 'slcAmp', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ), 'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
				)
				
			)
		)
	),

	'sitemap-content' => array
	(
		'title' => $L['sitemap-content'],
		'data' => array(
		
			'sitemap-content' => array( 
				'title' => $L['sitemap-content'], 'tip'=>null, 'data' => array
				(
					'include-homepage'=>array('label'=>sprintf( $L['include-s'], $L['homepage'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_homepage]', 'value' => ( isset( $sitemap['include_homepage'] ) ? $sitemap['include_homepage'] : true ), 'tip'=>null ),
					'include-posts'=>array('label'=>sprintf( $L['include-s'], $L['posts'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_posts]', 'value' => ( isset( $sitemap['include_posts'] ) ? $sitemap['include_posts'] : true ), 'tip'=>null ),
					'include-pages'=>array('label'=>sprintf( $L['include-s'], $L['pages'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_pages]', 'value' => ( isset( $sitemap['include_pages'] ) ? $sitemap['include_pages'] : true ), 'tip'=>null ),
					'include-custom-types'=>array('label'=>sprintf( $L['include-s'], $L['custom-post-types'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_custom_post_types]', 'value' => ( isset( $sitemap['include_custom_post_types'] ) ? $sitemap['include_custom_post_types'] : true ), 'tip'=>null ),
					'include-blogs'=>array('label'=>sprintf( $L['include-s'], $L['blogs'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_blogs]', 'value' => ( isset( $sitemap['include_blogs'] ) ? $sitemap['include_blogs'] : true ), 'tip'=>null, 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? false : true ) ),
					'include-langs'=>array('label'=>sprintf( $L['include-s'], $L['langs'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_langs]', 'value' => ( isset( $sitemap['include_langs'] ) ? $sitemap['include_langs'] : true ), 'tip'=>null, 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multilang', 'site' ) ? false : true ) ),
					'include-categories'=>array('label'=>sprintf( $L['include-s'], $L['categories'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_categories]', 'value' => ( isset( $sitemap['include_categories'] ) ? $sitemap['include_categories'] : null ), 'tip'=>null ),
					'include-tags'=>array('label'=>sprintf( $L['include-s'], $L['tags'] ), 'type'=>'checkbox', 'name' => 'sitemap[include_tags]', 'value' => ( isset( $sitemap['include_tags'] ) ? $sitemap['include_tags'] : null ), 'tip'=>null )
				)
				
			)
		)
	),
	
	'sitemap-priorities' => array
	(
		'title' => $L['sitemap-priorities'],
		'data' => array(
		
			'sitemap-priorities' => array( 
				'title' => $L['sitemap-priorities'], 'tip'=>null, 'data' => array
				(
					'homepage'=>array('label'=>$L['homepage'], 'name' => 'sitemap[homepage_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['homepage_priority'] ) ? $sitemap['homepage_priority'] : '1.0' ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $sitemapPriorities ),
					'posts'=>array('label'=>$L['posts'], 'name' => 'sitemap[posts_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['posts_priority'] ) ? $sitemap['posts_priority'] : '0.6' ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $sitemapPriorities ),
					'pages'=>array('label'=>$L['pages'], 'name' => 'sitemap[pages_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['pages_priority'] ) ? $sitemap['pages_priority'] : '0.6' ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $sitemapPriorities ),
					'blogs'=>array('label'=>$L['blogs'], 'name' => 'sitemap[blogs_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['blogs_priority'] ) ? $sitemap['blogs_priority'] : '0.3' ), 'tip'=>null, 'firstNull' => false, 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? false : true ), 'data' => $sitemapPriorities ),
					'langs'=>array('label'=>$L['langs'], 'name' => 'sitemap[langs_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['langs_priority'] ) ? $sitemap['langs_priority'] : '0.3' ), 'tip'=>null, 'firstNull' => false, 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multilang', 'site' ) ? false : true ), 'data' => $sitemapPriorities ),
					'categories'=>array('label'=>$L['categories'], 'name' => 'sitemap[categories_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['categories_priority'] ) ? $sitemap['categories_priority'] : '0.3' ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $sitemapPriorities ),
					'tags'=>array('label'=>$L['tags'], 'name' => 'sitemap[tags_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['tags_priority'] ) ? $sitemap['tags_priority'] : '0.6' ), 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $sitemapPriorities ),
					//'products'=>array('label'=>$L['products'], 'name' => 'sitemap[products_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['products_priority'] ) ? $sitemap['products_priority'] : '0.6' ), 'tip'=>null, 'firstNull' => false, 'disabled' => ( $settings['enable_store'] ? false : true ), 'data' => $sitemapPriorities ),
					//'threads'=>array('label'=>$L['threads'], 'name' => 'sitemap[threads_priority]', 'type'=>'select', 'value'=>( isset( $sitemap['threads_priority'] ) ? $sitemap['threads_priority'] : '0.6' ), 'tip'=>null, 'firstNull' => false, 'disabled' => ( $settings['enable_forum'] ? false : true ), 'data' => $sitemapPriorities ),
				)
				
			)
		)
	)

);

unset( $robots, $robotsTxtData, $seperatorData, $sitemapPriorities );
