<?php

AddThemeValue( default_widget_zones() );

AddThemeValue( theme_image() );
AddThemeValue( theme_tags() );

function theme_image()
{
	return array(
		'theme-image' => array
		(
			'image_wrap' 	=> '<div class="wp-block-image">%s</div>',
			'figure_class' 	=> 'aling%s',
			'decoding' 		=> 'async',
			'has_lazy_mode' => true,
			'figure_id' 	=> 'attachment_%d',
			'caption_class' => 'wp-caption-text',
			'caption_id' 	=> 'caption-attachment-%d',
			'image_class' 	=> 'align%s size-large wp-image-%d',
			'cover_class' 	=> 'attachment-penscratch-2-featured size-penscratch-2-featured wp-post-image'
		)
	);
}

function theme_tags()
{
	return array(
		'theme-tags' => array
		(
			'tags_wrap' 	=> '<span class="tags-links">%s</span>',
			'tag_wrap' 		=> '<a href="%1$s" rel="tag">%2$s</a>',
			'tag_sep' 		=> ''
		)
	);
}

function default_widget_zones() {
	return array(
		'widget-position' => array
		(
			'footer-1' => array(
			'name' => __( 'footer-1'),
			'id' => 'footer-1',
			'description' => __( "Widgetized footer 1" ),
			'args' => array (
				'before_widget' => '<div class="widget-area">',
				'after_widget' => '</div>',
				'before_title' => '<h1 class="widget-title">',
				'after_title' => '</h1>',
				'widget_items_class' 	=> 'toc',
				'widget_item_class' 	=> 'topic-name',
				'widget_link_class' 	=> 'topic-item',
				'widget_items_wrap' 	=> '<div class="%1$s"><ul>%2$s</ul></div>',
				'widget_item_wrap' 		=> '<li><a class="%1$s" href="%2$s">%3$s</a></li>',
				'widget_items_name' 	=> ''
				)
			),
			'footer-2' => array(
			'name' => __('footer-2' ),
			'id' => 'footer-2',
			'description' => __("Widgetized footer 2" ),
			'args' => array (
				'before_widget' => '<div class="widget-area">',
				'after_widget' => '</div>',
				'widget_ul_class' => '',
				'before_title' => '<h1 class="widget-title">',
				'after_title' => '</h1>',
				'widget_items_class' 	=> 'toc',
				'widget_item_class' 	=> 'topic-name',
				'widget_link_class' 	=> 'topic-item',
				'widget_items_wrap' 	=> '<div class="%1$s"><ul>%2$s</ul></div>',
				'widget_item_wrap' 		=> '<li><a class="%1$s" href="%2$s">%3$s</a></li>',
				'widget_items_name' 	=> ''
				)
			),
			'footer-3' => array(
			'name' => __('footer-3' ),
			'id' => 'footer-3',
			'description' => __("Widgetized footer 3" ),
			'args' => array (
				'before_widget' => '<div class="widget-area">',
				'after_widget' => '</div>',
				'widget_ul_class' => '',
				'before_title' => '<h1 class="widget-title">',
				'after_title' => '</h1>',
				'widget_items_class' 	=> 'toc',
				'widget_item_class' 	=> 'topic-name',
				'widget_link_class' 	=> 'topic-item',
				'widget_items_wrap' 	=> '<div class="%1$s"><ul>%2$s</ul></div>',
				'widget_item_wrap' 		=> '<li><a class="%1$s" href="%2$s">%3$s</a></li>',
				'widget_items_name' 	=> ''
				)
			),
			'sidebar' => array(
			'name' => __('sidebar' ),
			'id' => 'sidebar',
			'description' => __("Widgetized sidebar" ),
			'args' => array (
				'before_widget' => '<div id="secondary" class="widget-area" role="complementary">',
				'after_widget' => '</div>',
				'before_title' => '<h1 class="widget-title">',
				'after_title' => '</h1>',
				'widget_items_class' 	=> 'toc',
				'widget_item_class' 	=> 'topic-name',
				'widget_link_class' 	=> 'topic-item',
				'widget_items_wrap' 	=> '<div class="%1$s"><ul>%2$s</ul></div>',
				'widget_item_wrap' 		=> '<li><a class="%1$s" href="%2$s">%3$s</a></li>',
				'widget_items_name' 	=> ''
				)
			)
		)
	);
}

function ThemeHomeMenu()
{
	return array(
			'container'      		 => 'div',
			'container_class'		 => 'menu-main-menu-container',
			'container_id'    		 => 'navigation',
			'menu_class'      		 => 'menu',
			'menu_id'      			 => 'menu-categories-navigation',
			'item_class'   	  	 	 => 'menu-item menu-item-type-taxonomy menu-item-object-category',
			'single_item_wrap' 		 => '<li id="%1$s" class="%2$s">%3$s</li>'
	);
}

function PenSocialFooter()
{
	$CurrentLang = CurrentLang();
	
	$code = $CurrentLang['lang']['code'];
	
	$socialData = $CurrentLang['data']['social'];
	
	if ( empty( $socialData ) )
		return null;
	
	$html = '
	<ul id="menu-social-links" class="menu">';
	
	foreach ( $socialData as $id => $social )
	{
		if ( empty( $social ) )
			continue;

		$html .= '
		<li id="menu-item-' . $id . '" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-' . $id . '"><a href="' . $social . '"><span class="screen-reader-text">' . ucfirst( $id ) . '</span><svg class="icon icon-' . $id . '" aria-hidden="true" role="img"> <use href="#icon-' . $id . '" xlink:href="#icon-' . $id . '"></use> </svg></a></li>';
	}
	
	if ( Settings::IsTrue( 'enable_rss' ) ) 
	{
		$html .= '
		<li id="menu-item-feed" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-feed"><a href="' . Router::GetVariable( 'siteRealUrl' ) . 'feed' . PS . '"><span class="screen-reader-text">RSS</span><svg class="icon icon-feed" aria-hidden="true" role="img"> <use href="#icon-feed" xlink:href="#icon-feed"></use> </svg></a></li>';
	}
	
	$html .= '
	</ul>';
	
	echo $html;
}