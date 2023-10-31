<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# New Post Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

$form = array
(
	'add-post' => array
	(
		'title' => $L['add-post'],
		'col' => 6,
		'data' => array(
		
			'post-title' => array( 
				'title' => null, 'data' => array
				(
					'blog-title'=>array('label'=>$L['title'], 'type'=>'text', 'name' => 'title', 'value' =>null, 'tip'=>$L['the-title-how-it-appears'])
				)
			),
			
			'post-sef' => array( 
				'title' => null, 'data' => array
				(
					'post-sef'=>array('label'=>$L['slug'], 'type'=>'text', 'name' => 'slug', 'value' =>null, 'tip'=>$L['slug-tip'])
				)
			),
			
			'blog-slogan' => array( 
				'title' => null, 'data' => array
				(
					'blog-slogan'=>array('label'=>$L['blog-slogan'], 'type'=>'text', 'name' => 'slogan', 'value' =>null, 'tip'=>$L['blog-slogan-tip'])
				)
			),
			
			'blog-descr' => array( 
				'title' => null, 'data' => array
				(
					'blog-sef'=>array('label'=>$L['description'], 'type'=>'textarea', 'name' => 'description', 'value' =>null, 'tip'=>$L['descr-tip'])
				)
			),
			
			'frontpage' => array( 
				'title' => null, 'data' => array
				(
					'frontpage'=>array('label'=>$L['show-on-frontpage'], 'type'=>'checkbox', 'name' => 'frontpage', 'value' =>null, 'tip'=>$L['show-blog-on-frontpage-tip'])
				)
			),
			
			'news-sitemap' => array( 
				'title' => null, 'data' => array
				(
					'news-sitemap'=>array('label'=>$L['enable-in-news-sitemap'], 'type'=>'checkbox', 'name' => 'sitemap', 'value' =>null, 'tip'=>$L['enable-in-news-sitemap-tip'])
				)
			),
			
			'enable-rss' => array( 
				'title' => null, 'data' => array
				(
					'enable-rss'=>array('label'=>$L['enable-rss'], 'type'=>'checkbox', 'name' => 'enable_rss', 'value' =>null, 'tip'=>$L['rss-blog-tip'])
				)
			),
			
			'select-language' => array( 
				'title' => null, 'data' => array
				(
					'select-language'=>array('label'=>$L['blog-is-enabled'], 'name' => 'select-lang', 'type'=>'select', 'value'=>null, 'tip'=>$L['blog-is-enabled-tip'], 'firstNull' => false, 'data' => $blogsLangsData, 'id' => 'slcCountry', 'class' => 'selectpicker' ),
				)
			),
		)
	)
);
