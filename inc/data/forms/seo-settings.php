<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Seo Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

include ( ARRAYS_ROOT . 'seo-arrays.php');

$robots = Json( $settings['robots_data'] );
$sitemap = Json( $settings['sitemap_data'] );
$seoSettings = Json( $settings['seo_data'] );

#####################################################
#
# Seo Buttons Array(s)
#
#####################################################
$seperatorData = $sitemapPriorities = $robotsTxtData = array();

$homeButtons = $postButtons = $categoriesButtons = $tagsButtons = $searchButtons = $authorButtons = $blogsButtons = array(
	'site-title' => array( 'title' => $L['site-title'], 'var' => '{{site-title}} ' ),
	'site-slogan' => array( 'title' => $L['site-slogan'], 'var' => '{{site-slogan}} ' ),
	'site-description' => array( 'title' => $L['site-description'], 'var' => '{{site-description}} ' ),
	'page-number' => array( 'title' => $L['page-number'], 'var' => '{{page-num}} ' ),
	//'current-number-page' => array( 'title' => $L['current-number-page'], 'var' => '{{current-page-num}} ' ),
	'sep' => array( 'title' => $L['seperator'], 'var' => '{{sep}} ' ),
);

$rssButtons = array(
	'author-link' => array( 'title' => $L['author-link'], 'var' => '{{author-link}} ' ),
	'author-name' => array( 'title' => $L['author-name'], 'var' => '{{author-name}} ' ),
	'post-link' => array( 'title' => $L['post-link'], 'var' => '{{post-link}} ' ),
	'post-title' => array( 'title' => $L['post-title'], 'var' => '{{post-title}} ' ),
	'post-date' => array( 'title' => $L['post-date'], 'var' => '{{post-date}} ' ),
	'site-link' => array( 'title' => $L['site-link'], 'var' => '{{site-link}} ' ),
	'site-title' => array( 'title' => $L['site-title'], 'var' => '{{site-title}} ' ),
	'site-slogan' => array( 'title' => $L['site-slogan'], 'var' => '{{site-slogan}} ' ),
	'site-description' => array( 'title' => $L['site-description'], 'var' => '{{site-description}} ' ),
);

$postButtonsTemp = array(
	'post-title' => array( 'title' => $L['post-title'], 'var' => '{{post-title}} ' ),
	'post-description' => array( 'title' => $L['post-description'], 'var' => '{{post-description}} ' ),
	'post-author' => array( 'title' => $L['post-author'], 'var' => '{{post-author}} ' ),
	'post-date' => array( 'title' => $L['post-date'], 'var' => '{{post-date}} ' ),
	'post-id' => array( 'title' => $L['post-id'], 'var' => '{{post-id}} ' ),
);

$categoryButtonsTemp = array(
	'category-title' => array( 'title' => $L['category-title'], 'var' => '{{category-title}} ' ),
	'category-description' => array( 'title' => $L['category-description'], 'var' => '{{category-description}} ' )
);

$authorButtonsTemp = array(
	'author-name' => array( 'title' => $L['author-name'], 'var' => '{{author-name}} ' )
);

$blogsButtonsTemp = array(
	'blog-title' => array( 'title' => $L['blog-name'], 'var' => '{{blog-name}} ' ),
	'blog-slogan' => array( 'title' => $L['blog-slogan'], 'var' => '{{blog-slogan}} ' ),
	'blog-description' => array( 'title' => $L['blog-description'], 'var' => '{{blog-description}} ' ),
);

$tagsButtonsTemp = array(
	'tag-title' => array( 'title' => $L['tag-title'], 'var' => '{{tag-title}} ' ),
	'tag-description' => array( 'title' => $L['tag-description'], 'var' => '{{tag-description}} ' )
);

$searchButtonsTemp = array(
	'search-term' => array( 'title' => $L['search-term'], 'var' => '{{search-term}} ' )
);

$blogsButtons = array_merge( $blogsButtonsTemp, $blogsButtons );
$postButtons = array_merge( $postButtonsTemp, $postButtons );
$categoriesButtons = array_merge( $categoryButtonsTemp, $categoriesButtons );
$authorButtons = array_merge( $authorButtonsTemp, $authorButtons );
$tagsButtons = array_merge( $tagsButtonsTemp, $tagsButtons );
$searchButtons = array_merge( $searchButtonsTemp, $searchButtons );

unset( $postButtonsTemp, $categoryButtonsTemp, $authorButtonsTemp, $tagsButtonsTemp, $searchButtonsTemp, $rssButtonsTemp, $blogsButtonsTemp );

foreach( $titleSeperatorArray as $id => $row )
	$seperatorData[$id] = array( 'name' => $id, 'title'=> $row['code'] . ' (' . $row['title'] . ')', 'disabled' => false, 'data' => array() );

#####################################################
#
# robots.txt Data Array
#
#####################################################
$robotsTxtData['disable'] = array( 'name' => 'disable', 'title'=> $L['disable'], 'disabled' => false, 'data' => array() );
$robotsTxtData['disallow'] = array( 'name' => 'disallow', 'title'=> $L['disallow'], 'disabled' => false, 'data' => array() );
$robotsTxtData['allow'] = array( 'name' => 'allow', 'title'=> $L['allow'], 'disabled' => false, 'data' => array() );

for ( $i = 0; $i < 10; $i++ )
	$sitemapPriorities['0.' . $i] = array( 'name' => '0.' . $i, 'title'=> '0.' . $i, 'disabled' => false, 'data' => array() );

$sitemapPriorities['1.0'] = array( 'name' => '1.0', 'title'=> '1.0', 'disabled' => false, 'data' => array() );

$form = array
(
	'general-settings' => array
	(
		'title' => $L['general-settings'],
		'data' => array(
			'general-settings' => array(
				'title' => null, 'data' => array
				(
					'title-seperator'=>array('label'=>$L['title-seperator'], 'type'=>'select', 'name' => 'settings[title_seperator]', 'value'=>( isset( $seoSettings['title_seperator'] ) ? $seoSettings['title_seperator'] : null ), 'tip'=>$L['title-seperator-tip'], 'firstNull' => false, 'data' => $seperatorData ),
					
					'remove-short-words'=>array('label'=>$L['automatically-remove-short-words'], 'type'=>'num', 'name' => 'settings[remove_short_words]', 'value'=>( isset( $seoSettings['remove_short_words'] ) ? $seoSettings['remove_short_words'] : 0 ), 'tip'=>$L['automatically-remove-short-words-tip'], 'min'=>'0', 'max'=>'4'),
					
					'facebook-profile-url'=>array('label'=>$L['facebook-profile-url'], 'name' => 'settings[facebook_profile]', 'type'=>'text', 'value'=>( isset( $seoSettings['facebook_profile'] ) ? $seoSettings['facebook_profile'] : null ), 'tip'=>$L['facebook-profile-url-tip'], ),
					
					'add-alt-on-images'=>array('label'=>$L['add-alt-on-images'], 'type'=>'checkbox', 'name' => 'settings[add_alt_on_images]', 'value' => ( isset( $seoSettings['add_alt_on_images'] ) ? $seoSettings['add_alt_on_images'] : null ), 'tip'=>$L['add-alt-on-images-tip'] ),
					
					'nofollow-tag-on-pages'=>array('label'=>$L['nofollow-tag-on-archive-pages'], 'type'=>'checkbox', 'name' => 'settings[nofollow_tag_archive]', 'value' => ( isset( $seoSettings['nofollow_tag_archive'] ) ? $seoSettings['nofollow_tag_archive'] : null ), 'tip'=>$L['nofollow-tag-on-archive-pages-tip'] ),
					
					//'open-external-links-in-new-tab-window'=>array('label'=>$L['open-external-links-in-new-tab-window'], 'type'=>'checkbox', 'name' => 'settings[open_external_links_new_tab]', 'value' => ( isset( $seoSettings['open_external_links_new_tab'] ) ? $seoSettings['open_external_links_new_tab'] : null ), 'tip'=>$L['open-external-links-in-new-tab-window-tip'] ),
					
					//'nofollow-external-links'=>array('label'=>$L['nofollow-external-links'], 'type'=>'checkbox', 'name' => 'settings[nofollow_external_links]', 'value' => ( isset( $seoSettings['nofollow_external_links'] ) ? $seoSettings['nofollow_external_links'] : null ), 'tip'=>$L['nofollow-external-links-tip'] )
				)
			),/*
			
			'seo-tools' => array(
				'title' => $L['seo-tools'], 'data' => array
				(
					'open-graph'=>array('label'=>$L['open-graph-per-post'], 'type'=>'checkbox', 'name' => 'settings[enable_open_graph]', 'value' => ( isset( $seoSettings['enable_open_graph'] ) ? $seoSettings['enable_open_graph'] : null ), 'tip'=>$L['open-graph-per-post-tip'] ),
					
					'enable-schema'=>array('label'=>$L['enable-schema-markup'], 'type'=>'checkbox', 'name' => 'settings[enable_schema_markup]', 'value' => ( isset( $seoSettings['enable_schema_markup'] ) ? $seoSettings['enable_schema_markup'] : null ), 'tip'=>$L['enable-schema-markup-tip'] ),
				)
				
			),
			
			'tracking-codes' => array(
				'title' => $L['tracking-codes'], 'tip' =>null, 'data' => array
				(
					'google-analytics-four'=>array('label'=>$L['google-analytics-four'], 'name' => 'settings[google_analytics_four]', 'type'=>'text', 'value'=>( isset( $seoSettings['tracking-codes']['google_analytics_four'] ) ? $seoSettings['tracking-codes']['google_analytics_four'] : null ), 'tip'=>sprintf( $L['more-info-s'], 'https://support.google.com/analytics/answer/10089681' ), 'placeholder' => 'G-XXXXXXXXXX' ),
					
					'google-analytics-ua'=>array('label'=>$L['google-analytics-ua'], 'name' => 'settings[google_analytics_ua]', 'type'=>'text', 'value'=>( isset( $seoSettings['tracking-codes']['google_analytics_ua'] ) ? $seoSettings['tracking-codes']['google_analytics_ua'] : null ), 'tip'=>sprintf( $L['more-info-s'], 'https://support.google.com/analytics/answer/1032385' ), 'placeholder' => 'UA-XXXXXXXX-X' ),
					
					'facebook-pixel-id'=>array('label'=>$L['facebook-pixel-id'], 'name' => 'settings[facebook_pixel_id]', 'type'=>'text', 'value'=>( isset( $seoSettings['tracking-codes']['facebook_pixel_id'] ) ? $seoSettings['tracking-codes']['facebook_pixel_id'] : null ), 'tip'=>sprintf( $L['more-info-s'], 'https://www.facebook.com/business/help/742478679120153/' ), 'placeholder' => '1234567890' ),
					
					'google-tag-manager-id'=>array('label'=>$L['google-tag-manager-id'], 'name' => 'settings[google_tag_manager_id]', 'type'=>'text', 'value'=>( isset( $seoSettings['tracking-codes']['google_tag_manager_id'] ) ? $seoSettings['tracking-codes']['google_tag_manager_id'] : null ), 'tip'=>sprintf( $L['more-info-s'], 'https://support.google.com/tagmanager/answer/6103696' ), 'placeholder' => 'GTM-XXXXXX' ),
				)
			),

			'title-formats' => array(
				'title' => $L['title-formats'], 'tip' =>$L['title-formats-tip'], 'data' => array
				(
					'homepage-format'=>array('label'=>$L['homepage'], 'name' => 'settings[homepage_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['homepage_title_format'] ) ? $seoSettings['homepage_title_format'] : null ), 'tip'=>$L['homepage-format-tip'], 'buttons' =>  $homeButtons ),
					'page-format'=>array('label'=>$L['pages'], 'name' => 'settings[pages_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['pages_title_format'] ) ? $seoSettings['pages_title_format'] : null ), 'tip'=>$L['pages-format-tip'], 'buttons' =>  $postButtons ),
					'post-format'=>array('label'=>$L['posts'], 'name' => 'settings[posts_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['posts_title_format'] ) ? $seoSettings['posts_title_format'] : null ), 'tip'=>$L['posts-format-tip'], 'buttons' =>  $postButtons ),
					'blog-format'=>array('label'=>$L['blogs'], 'name' => 'settings[blogs_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['blogs_title_format'] ) ? $seoSettings['blogs_title_format'] : null ), 'tip'=>$L['blogs-format-tip'], 'buttons' =>  $blogsButtons ),
					'category-format'=>array('label'=>$L['categories'], 'name' => 'settings[categories_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['categories_title_format'] ) ? $seoSettings['categories_title_format'] : null ), 'tip'=>$L['categories-format-tip'], 'buttons' =>  $categoriesButtons ),
					'author-format'=>array('label'=>$L['authors'], 'name' => 'settings[authors_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['authors_title_format'] ) ? $seoSettings['authors_title_format'] : null ), 'tip'=>$L['authors-format-tip'], 'buttons' =>  $authorButtons ),
					'tag-format'=>array('label'=>$L['tags'], 'name' => 'settings[tags_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['tags_title_format'] ) ? $seoSettings['tags_title_format'] : null ), 'tip'=>$L['tags-format-tip'], 'buttons' =>  $tagsButtons ),
					'search-format'=>array('label'=>$L['search-results'], 'name' => 'settings[search_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['search_title_format'] ) ? $seoSettings['search_title_format'] : null ), 'tip'=>$L['search-format-tip'], 'buttons' =>  $searchButtons ),
					//'thread-format'=>array('label'=>$L['threads'], 'name' => 'settings[threads_title_format]', 'type'=>'text', 'value'=>$settings['threads_title_format'], 'tip'=>$L['threads-format-tip'], 'disabled' => ( $settings['enable_forum'] ? false : true ) ),
					//'product-format'=>array('label'=>$L['products'], 'name' => 'settings[products_title_format]', 'type'=>'text', 'value'=>$settings['products_title_format'], 'tip'=>$L['products-format-tip'], 'disabled' => ( $settings['enable_store'] ? false : true ) ),
				)
			),
					
			'search-engine-visibility' => array(
				'title' => $L['show-in-search-results'], 'data' => array
				(
					'pages'=>array('label'=>$L['pages'], 'type'=>'checkbox', 'name' => 'settings[show_pages_search]', 'value' => ( isset( $seoSettings['show_pages_search'] ) ? $seoSettings['show_pages_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['pages'] ) ),
					'posts'=>array('label'=>$L['posts'], 'type'=>'checkbox', 'name' => 'settings[show_posts_search]', 'value' => ( isset( $seoSettings['show_posts_search'] ) ? $seoSettings['show_posts_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['posts'] ) ),
					'categories'=>array('label'=>$L['categories'], 'type'=>'checkbox', 'name' => 'settings[show_categories_search]', 'value' => ( isset( $seoSettings['show_categories_search'] ) ? $seoSettings['show_categories_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['categories'] ) ),
					'tags'=>array('label'=>$L['tags'], 'type'=>'checkbox', 'name' => 'settings[show_tags_search]', 'value' => ( isset( $seoSettings['show_tags_search'] ) ? $seoSettings['show_tags_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['tags'] ) ),
					'author'=>array('label'=>$L['authors'], 'type'=>'checkbox', 'name' => 'settings[show_authors_search]', 'value' => ( isset( $seoSettings['show_authors_search'] ) ? $seoSettings['show_authors_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['authors'] ) )
				)
			),
/*
			'rss-settings' => array( 
				'title' => $L['rss-settings'], 'tip' =>$L['rss-settings-tip'], 'data' => array
				(
					'rss-header'=>array('label'=>$L['rss-header'], 'type'=>'textarea', 'name' => 'settings[rss_header_code]', 'value' => ( isset( $seoSettings['rss_header_code'] ) ? $seoSettings['rss_header_code'] : null ), 'tip'=>null, 'buttons' =>  $rssButtons ),
					'rss-footer'=>array('label'=>$L['rss-footer'], 'type'=>'textarea', 'name' => 'settings[rss_footer_code]', 'value' => ( isset( $seoSettings['rss_footer_code'] ) ? $seoSettings['rss_footer_code'] : null ), 'tip'=>null, 'buttons' =>  $rssButtons )
				)
			),* /
			'site-verification-services' => array( 
				'title' => $L['site-verification-services'], 'tip'=>$L['site-verification-services-tip'], 'data' => array
				(
					'google'=>array('label'=>$L['google'], 'name' => 'settings[google_site_verification]', 'type'=>'text', 'value'=>( isset( $seoSettings['google_site_verification'] ) ? $seoSettings['google_site_verification'] : null ), 'tip'=>null, 'placeholder' => '<meta name=&quot;google-site-verification&quot; content=&quot;xxxx&quot; />' ),
					'bing'=>array('label'=>$L['bing'], 'name' => 'settings[msvalidate]', 'type'=>'text', 'value'=>( isset( $seoSettings['msvalidate'] ) ? $seoSettings['msvalidate'] : null ), 'tip'=>null, 'placeholder' => '<meta name=&quot;msvalidate.01&quot; content=&quot;xxxx&quot; />' ),
					'yandex'=>array('label'=>$L['yandex'], 'name' => 'settings[yandex_verification]', 'type'=>'text', 'value'=>( isset( $seoSettings['yandex_verification'] ) ? $seoSettings['yandex_verification'] : null ), 'tip'=>null, 'placeholder' => '<meta name=&quot;yandex-verification&quot; content=&quot;xxxx&quot; />' ),
				)
				
			)*/
		)
	),
	
	
	'seo-tools' => array
	(
		'title' => $L['seo-tools'],
		'data' => array(
			'seo-tools' => array(
				'title' => null, 'data' => array
				(
					'open-graph'=>array('label'=>$L['open-graph-per-post'], 'type'=>'checkbox', 'name' => 'settings[enable_open_graph]', 'value' => ( isset( $seoSettings['enable_open_graph'] ) ? $seoSettings['enable_open_graph'] : null ), 'tip'=>$L['open-graph-per-post-tip'] ),
					
					'enable-schema'=>array('label'=>$L['enable-schema-markup'], 'type'=>'checkbox', 'name' => 'settings[enable_schema_markup]', 'value' => ( isset( $seoSettings['enable_schema_markup'] ) ? $seoSettings['enable_schema_markup'] : null ), 'tip'=>$L['enable-schema-markup-tip'] ),
				)
				
			)
		)
	),
	
	'tracking-codes' => array
	(
		'title' => $L['tracking-codes'],
		'data' => array(
			'seo-tools' => array(
				'title' => null, 'data' => array
				(
					'google-analytics-four'=>array('label'=>$L['google-analytics-four'], 'name' => 'settings[google_analytics_four]', 'type'=>'text', 'value'=>( isset( $seoSettings['tracking-codes']['google_analytics_four'] ) ? $seoSettings['tracking-codes']['google_analytics_four'] : null ), 'tip'=>sprintf( $L['more-info-s'], 'https://support.google.com/analytics/answer/10089681' ), 'placeholder' => 'G-XXXXXXXXXX' ),
					
					'google-analytics-ua'=>array('label'=>$L['google-analytics-ua'], 'name' => 'settings[google_analytics_ua]', 'type'=>'text', 'value'=>( isset( $seoSettings['tracking-codes']['google_analytics_ua'] ) ? $seoSettings['tracking-codes']['google_analytics_ua'] : null ), 'tip'=>sprintf( $L['more-info-s'], 'https://support.google.com/analytics/answer/1032385' ), 'placeholder' => 'UA-XXXXXXXX-X' ),
					
					'facebook-pixel-id'=>array('label'=>$L['facebook-pixel-id'], 'name' => 'settings[facebook_pixel_id]', 'type'=>'text', 'value'=>( isset( $seoSettings['tracking-codes']['facebook_pixel_id'] ) ? $seoSettings['tracking-codes']['facebook_pixel_id'] : null ), 'tip'=>sprintf( $L['more-info-s'], 'https://www.facebook.com/business/help/742478679120153/' ), 'placeholder' => '1234567890' ),
					
					'google-tag-manager-id'=>array('label'=>$L['google-tag-manager-id'], 'name' => 'settings[google_tag_manager_id]', 'type'=>'text', 'value'=>( isset( $seoSettings['tracking-codes']['google_tag_manager_id'] ) ? $seoSettings['tracking-codes']['google_tag_manager_id'] : null ), 'tip'=>sprintf( $L['more-info-s'], 'https://support.google.com/tagmanager/answer/6103696' ), 'placeholder' => 'GTM-XXXXXX' )
				)
				
			)
		)
	),
	
	'title-formats' => array
	(
		'title' => $L['title-formats'],
		'data' => array(
			'seo-tools' => array(
				'title' => null, 'tip' =>$L['title-formats-tip'], 'data' => array
				(
					'homepage-format'=>array('label'=>$L['homepage'], 'name' => 'settings[homepage_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['homepage_title_format'] ) ? $seoSettings['homepage_title_format'] : null ), 'tip'=>$L['homepage-format-tip'], 'buttons' =>  $homeButtons ),
					'page-format'=>array('label'=>$L['pages'], 'name' => 'settings[pages_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['pages_title_format'] ) ? $seoSettings['pages_title_format'] : null ), 'tip'=>$L['pages-format-tip'], 'buttons' =>  $postButtons ),
					'post-format'=>array('label'=>$L['posts'], 'name' => 'settings[posts_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['posts_title_format'] ) ? $seoSettings['posts_title_format'] : null ), 'tip'=>$L['posts-format-tip'], 'buttons' =>  $postButtons ),
					'blog-format'=>array('label'=>$L['blogs'], 'name' => 'settings[blogs_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['blogs_title_format'] ) ? $seoSettings['blogs_title_format'] : null ), 'tip'=>$L['blogs-format-tip'], 'buttons' =>  $blogsButtons ),
					'category-format'=>array('label'=>$L['categories'], 'name' => 'settings[categories_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['categories_title_format'] ) ? $seoSettings['categories_title_format'] : null ), 'tip'=>$L['categories-format-tip'], 'buttons' =>  $categoriesButtons ),
					'author-format'=>array('label'=>$L['authors'], 'name' => 'settings[authors_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['authors_title_format'] ) ? $seoSettings['authors_title_format'] : null ), 'tip'=>$L['authors-format-tip'], 'buttons' =>  $authorButtons ),
					'tag-format'=>array('label'=>$L['tags'], 'name' => 'settings[tags_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['tags_title_format'] ) ? $seoSettings['tags_title_format'] : null ), 'tip'=>$L['tags-format-tip'], 'buttons' =>  $tagsButtons ),
					'search-format'=>array('label'=>$L['search-results'], 'name' => 'settings[search_title_format]', 'type'=>'text', 'value'=>( isset( $seoSettings['search_title_format'] ) ? $seoSettings['search_title_format'] : null ), 'tip'=>$L['search-format-tip'], 'buttons' =>  $searchButtons ),
					//'thread-format'=>array('label'=>$L['threads'], 'name' => 'settings[threads_title_format]', 'type'=>'text', 'value'=>$settings['threads_title_format'], 'tip'=>$L['threads-format-tip'], 'disabled' => ( $settings['enable_forum'] ? false : true ) ),
					//'product-format'=>array('label'=>$L['products'], 'name' => 'settings[products_title_format]', 'type'=>'text', 'value'=>$settings['products_title_format'], 'tip'=>$L['products-format-tip'], 'disabled' => ( $settings['enable_store'] ? false : true ) ),
				)
				
			)
		)
	),
	
	'meta-formats' => array
	(
		'title' => $L['meta-formats'],
		'data' => array(
			'seo-tools' => array(
				'title' => null, 'tip' =>$L['meta-formats-tip'], 'data' => array
				(
					'homepage-meta'=>array('label'=>$L['homepage'], 'name' => 'settings[homepage_meta_format]', 'type'=>'textarea', 'value'=>( isset( $seoSettings['homepage_meta_format'] ) ? $seoSettings['homepage_meta_format'] : null ), 'tip'=>$L['homepage-format-tip'], 'buttons' => $homeButtons ),
					'page-meta'=>array('label'=>$L['pages'], 'name' => 'settings[pages_meta_format]', 'type'=>'textarea', 'value'=>( isset( $seoSettings['pages_meta_format'] ) ? $seoSettings['pages_meta_format'] : null ), 'tip'=>$L['pages-format-tip'], 'buttons' =>  $postButtons ),
					'post-meta'=>array('label'=>$L['posts'], 'name' => 'settings[posts_meta_format]', 'type'=>'textarea', 'value'=>( isset( $seoSettings['posts_meta_format'] ) ? $seoSettings['posts_meta_format'] : null ), 'tip'=>$L['posts-format-tip'], 'buttons' =>  $postButtons ),
					'blog-meta'=>array('label'=>$L['blogs'], 'name' => 'settings[blogs_meta_format]', 'type'=>'textarea', 'value'=>( isset( $seoSettings['blogs_meta_format'] ) ? $seoSettings['blogs_meta_format'] : null ), 'tip'=>$L['blogs-format-tip'], 'buttons' =>  $blogsButtons ),
					'category-meta'=>array('label'=>$L['categories'], 'name' => 'settings[categories_meta_format]', 'type'=>'textarea', 'value'=>( isset( $seoSettings['categories_meta_format'] ) ? $seoSettings['categories_meta_format'] : null ), 'tip'=>$L['categories-format-tip'], 'buttons' =>  $categoriesButtons ),
					'author-meta'=>array('label'=>$L['authors'], 'name' => 'settings[authors_meta_format]', 'type'=>'textarea', 'value'=>( isset( $seoSettings['authors_meta_format'] ) ? $seoSettings['authors_meta_format'] : null ), 'tip'=>$L['authors-format-tip'], 'buttons' =>  $authorButtons ),
					'tag-meta'=>array('label'=>$L['tags'], 'name' => 'settings[tags_meta_format]', 'type'=>'textarea', 'value'=>( isset( $seoSettings['tags_meta_format'] ) ? $seoSettings['tags_meta_format'] : null ), 'tip'=>$L['tags-format-tip'], 'buttons' =>  $tagsButtons ),
					'search-meta'=>array('label'=>$L['search-results'], 'name' => 'settings[search_meta_format]', 'type'=>'textarea', 'value'=>( isset( $seoSettings['search_meta_format'] ) ? $seoSettings['search_meta_format'] : null ), 'tip'=>$L['search-format-tip'], 'buttons' =>  $searchButtons ),
					//'thread-format'=>array('label'=>$L['threads'], 'name' => 'settings[threads_title_format]', 'type'=>'text', 'value'=>$settings['threads_title_format'], 'tip'=>$L['threads-format-tip'], 'disabled' => ( $settings['enable_forum'] ? false : true ) ),
					//'product-format'=>array('label'=>$L['products'], 'name' => 'settings[products_title_format]', 'type'=>'text', 'value'=>$settings['products_title_format'], 'tip'=>$L['products-format-tip'], 'disabled' => ( $settings['enable_store'] ? false : true ) ),
				)
				
			)
		)
	),
	
	'search-engine-visibility' => array
	(
		'title' => $L['show-in-search-results'],
		'data' => array(
			'seo-tools' => array(
				'title' => null, 'data' => array
				(
					'pages'=>array('label'=>$L['pages'], 'type'=>'checkbox', 'name' => 'settings[show_pages_search]', 'value' => ( isset( $seoSettings['show_pages_search'] ) ? $seoSettings['show_pages_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['pages'] ) ),
					'posts'=>array('label'=>$L['posts'], 'type'=>'checkbox', 'name' => 'settings[show_posts_search]', 'value' => ( isset( $seoSettings['show_posts_search'] ) ? $seoSettings['show_posts_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['posts'] ) ),
					'categories'=>array('label'=>$L['categories'], 'type'=>'checkbox', 'name' => 'settings[show_categories_search]', 'value' => ( isset( $seoSettings['show_categories_search'] ) ? $seoSettings['show_categories_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['categories'] ) ),
					'blogs'=>array('label'=>$L['blogs'], 'type'=>'checkbox', 'name' => 'settings[show_blogs_search]', 'value' => ( isset( $seoSettings['show_blogs_search'] ) ? $seoSettings['show_blogs_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['blogs'] ) ),
					'custom-types'=>array('label'=>$L['custom-post-types'], 'type'=>'checkbox', 'name' => 'settings[show_custom_post_types_search]', 'value' => ( isset( $seoSettings['show_custom_post_types_search'] ) ? $seoSettings['show_custom_post_types_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['custom-post-types'] ) ),
					'tags'=>array('label'=>$L['tags'], 'type'=>'checkbox', 'name' => 'settings[show_tags_search]', 'value' => ( isset( $seoSettings['show_tags_search'] ) ? $seoSettings['show_tags_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['tags'] ) ),
					'author'=>array('label'=>$L['authors'], 'type'=>'checkbox', 'name' => 'settings[show_authors_search]', 'value' => ( isset( $seoSettings['show_authors_search'] ) ? $seoSettings['show_authors_search'] : null ), 'tip'=>sprintf( $L['show-in-search-results-tips'], $L['authors'] ) )
				)
				
			)
		)
	),
	
	'site-verification-services' => array
	(
		'title' => $L['site-verification-services'],
		'data' => array(
			'seo-tools' => array(
				'title' => null, 'tip' => $L['site-verification-services-tip'], 'data' => array
				(
					'google'=>array('label'=>$L['google'], 'name' => 'settings[google_site_verification]', 'type'=>'text', 'value'=>( isset( $seoSettings['google_site_verification'] ) ? htmlspecialchars_decode( $seoSettings['google_site_verification'] ) : null ), 'tip'=>null, 'placeholder' => '<meta name=&quot;google-site-verification&quot; content=&quot;xxxx&quot; />' ),
					'bing'=>array('label'=>$L['bing'], 'name' => 'settings[msvalidate]', 'type'=>'text', 'value'=>( isset( $seoSettings['msvalidate'] ) ? htmlspecialchars_decode( $seoSettings['msvalidate'] ) : null ), 'tip'=>null, 'placeholder' => '<meta name=&quot;msvalidate.01&quot; content=&quot;xxxx&quot; />' ),
					'yandex'=>array('label'=> $L['yandex'], 'name' => 'settings[yandex_verification]', 'type'=>'text', 'value'=>( isset( $seoSettings['yandex_verification'] ) ? htmlspecialchars_decode( $seoSettings['yandex_verification'] ) : null ), 'tip'=>null, 'placeholder' => '<meta name=&quot;yandex-verification&quot; content=&quot;xxxx&quot; />' ),
				)
				
			)
		)
	),
);