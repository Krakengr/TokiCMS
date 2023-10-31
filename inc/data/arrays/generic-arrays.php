<?php defined('TOKICMS') or die('Hacking attempt...');

global $L;

//Api Permissions Array
$apiPermissions = array(

	'add-post' => array( 'name' => 'add-post', 'title' => $L['api-create-new-posts'], 'tip' => $L['api-create-new-posts-tip'], 'disabled' => false, 'method' => 'POST' ),
			
	'add-variations' => array( 'name' => 'add-variations', 'title' => $L['api-create-new-variations'], 'tip' => $L['api-create-new-variations-tip'], 'disabled' => false, 'method' => 'POST' ),
			
	'add-page' => array( 'name' => 'add-page', 'title' => $L['create-new-pages'], 'tip' => null, 'disabled' => false, 'method' => 'POST' ),

	'add-store' => array( 'name' => 'add-store', 'title' => $L['create-new-stores'], 'tip' => null, 'disabled' => false, 'method' => 'POST' ),
			
	'add-comment' => array( 'name' => 'add-comment', 'title' => $L['create-new-comments'], 'tip' => null, 'disabled' => false, 'method' => 'POST' ),
			
	'add-member' => array( 'name' => 'add-member', 'title' => $L['create-new-members'], 'tip' => null, 'disabled' => false, 'method' => 'POST' ),
			
	'add-link' => array( 'name' => 'add-link', 'title' => $L['create-new-links'], 'tip' => null, 'disabled' => false, 'method' => 'POST' ),
			
	'add-prices' => array( 'name' => 'add-prices', 'title' => $L['add-new-prices'], 'tip' => null, 'disabled' => false, 'method' => 'POST' ),
			
	'links' => array( 'name' => 'links', 'title' => $L['get-links'], 'tip' => null, 'disabled' => false, 'method' => 'GET' ),
			
	'prices' => array( 'name' => 'prices', 'title' => $L['get-prices'], 'tip' => null, 'disabled' => false, 'method' => 'GET' ),
			
	'currencies' => array( 'name' => 'currencies', 'title' => $L['get-currencies'], 'tip' => null, 'disabled' => false, 'method' => 'GET' ),

	'posts' => array( 'name' => 'posts', 'title' => $L['get-posts'], 'tip' => null, 'disabled' => false, 'method' => 'GET' ),
			
	'pages' => array( 'name' => 'pages', 'title' => $L['get-pages'], 'tip' => null, 'disabled' => false, 'method' => 'GET' ),
			
	'blogs' => array( 'name' => 'blogs', 'title' => $L['get-blogs'], 'tip' => null, 'disabled' => false, 'method' => 'GET' ),
			
	'users' => array( 'name' => 'users', 'title' => $L['get-users'], 'tip' => null, 'disabled' => false, 'method' => 'GET' ),

	'categories' => array( 'name' => 'categories', 'title' => $L['get-categories'], 'tip' => null, 'disabled' => false, 'method' => 'GET' ),
			
	'comments' => array( 'name' => 'comments', 'title' => $L['get-comments'], 'tip' => null, 'disabled' => false, 'method' => 'GET' ),

	'tags' => array( 'name' => 'tags', 'title' => $L['get-tags'], 'tip' => null, 'disabled' => false, 'method' => 'GET' )
);

//Sort permissions alphabetically
asort( $apiPermissions );

//groupPermissions Array
$groupPermissions = array(
			'admin-site' => array( 'name' => 'admin-site', 'title' => $L['admin-site'], 'tip' => $L['admin-site-tip'], 'disabled' => false ),

			'import-content' => array( 'name' => 'import-content', 'title' => $L['can-import-content'], 'tip' => $L['can-import-content-tip'], 'disabled' => false ),

			'create-new-posts' => array( 'name' => 'create-new-posts', 'title' => $L['create-new-posts'], 'tip' => $L['create-new-posts-tip'], 'disabled' => false ),

			'lock-posts' => array( 'name' => 'lock-posts', 'title' => $L['lock-posts'], 'tip' => $L['lock-posts-tip'], 'disabled' => false ),

			'manage-ads' => array( 'name' => 'manage-ads', 'title' => $L['manage-ads'], 'tip' => $L['manage-ads-tip'], 'disabled' => false ),

			'manage-api' => array( 'name' => 'manage-api', 'title' => $L['manage-api'], 'tip' => $L['manage-api-tip'], 'disabled' => false ),

			'manage-blogs' => array( 'name' => 'manage-blogs', 'title' => $L['manage-blogs'], 'tip' => $L['manage-blogs-tip'], 'disabled' => false ),

			'manage-forum' => array( 'name' => 'manage-forum', 'title' => $L['manage-forum'], 'tip' => $L['manage-forum-tip'], 'disabled' => false ),
			
			'manage-themes' => array( 'name' => 'manage-themes', 'title' => $L['manage-themes'], 'tip' => $L['manage-themes-tip'], 'disabled' => false ),

			'manage-attachments' => array( 'name' => 'manage-attachments', 'title' => $L['manage-attachments'], 'tip' => $L['manage-attachments-tip'], 'disabled' => false ),

			'manage-sites' => array( 'name' => 'manage-sites', 'title' => $L['manage-sites'], 'tip' => $L['manage-sites-tip'], 'disabled' => false ),
			
			'manage-filters' => array( 'name' => 'manage-filters', 'title' => $L['manage-filters'], 'tip' => $L['manage-filters-tip'], 'disabled' => false ),

			'manage-languages' => array( 'name' => 'manage-languages', 'title' => $L['manage-languages'], 'tip' => $L['manage-languages-tip'], 'disabled' => false ),

			'manage-members' => array( 'name' => 'manage-members', 'title' => $L['manage-members'], 'tip' => $L['manage-members-tip'], 'disabled' => false ),

			'manage-posts' => array( 'name' => 'manage-posts', 'title' => $L['manage-posts'], 'tip' => $L['manage-posts-tip'], 'disabled' => false ),
			
			'manage-forms' => array( 'name' => 'manage-forms', 'title' => $L['manage-forms'], 'tip' => $L['manage-forms-tip'], 'disabled' => false ),
			
			'manage-classifieds' => array( 'name' => 'manage-classifieds', 'title' => $L['manage-classifieds'], 'tip' => $L['manage-classifieds-tip'], 'disabled' => false ),
			
			'create-new-classified' => array( 'name' => 'create-new-classified', 'title' => $L['create-new-classified'], 'tip' => $L['create-new-classified-tip'], 'disabled' => false ),
			
			'manage-own-classified' => array( 'name' => 'manage-own-classified', 'title' => $L['manage-own-classified'], 'tip' => $L['manage-own-classified-tip'], 'disabled' => false ),

			'manage-coupons-deals' => array( 'name' => 'manage-coupons-deals', 'title' => $L['manage-coupons-deals'], 'tip' => $L['manage-coupons-deals-tip'], 'disabled' => false ),
			
			'manage-links' => array( 'name' => 'manage-links', 'title' => $L['manage-links'], 'tip' => $L['manage-links-tip'], 'disabled' => false ),
			
			'manage-prices' => array( 'name' => 'manage-prices', 'title' => $L['manage-prices'], 'tip' => $L['manage-prices-tip'], 'disabled' => false ),

			'manage-stores' => array( 'name' => 'manage-stores', 'title' => $L['manage-stores'], 'tip' => $L['manage-stores-tip'], 'disabled' => false ),
			
			'create-new-store' => array( 'name' => 'create-new-store', 'title' => $L['create-new-store'], 'tip' => $L['create-new-store-tip'], 'disabled' => false ),
			
			'manage-own-store' => array( 'name' => 'manage-own-store', 'title' => $L['manage-own-store'], 'tip' => $L['manage-own-store-tip'], 'disabled' => false ),
			
			'manage-manufacturers' => array( 'name' => 'manage-manufacturers', 'title' => $L['manage-manufacturers'], 'tip' => $L['manage-manufacturers-tip'], 'disabled' => false ),

			'manage-redirections' => array( 'name' => 'manage-redirections', 'title' => $L['manage-redirections'], 'tip' => $L['manage-redirections-tip'], 'disabled' => false ),

			'manage-seo' => array( 'name' => 'manage-seo', 'title' => $L['manage-seo'], 'tip' => $L['manage-seo-tip'], 'disabled' => false ),

			'manage-widgets' => array( 'name' => 'manage-widgets', 'title' => $L['manage-widgets'], 'tip' => $L['manage-widgets-tip'], 'disabled' => false ),

			'manage-auto-content' => array( 'name' => 'manage-auto-content', 'title' => $L['manage-auto-content'], 'tip' => $L['manage-auto-content-tip'], 'disabled' => false ),

			'manage-post-attributes' => array( 'name' => 'manage-post-attributes', 'title' => $L['manage-post-attributes'], 'tip' => $L['manage-post-attributes-tip'], 'disabled' => false ),

			'manage-post-types' => array( 'name' => 'manage-post-types', 'title' => $L['manage-post-types'], 'tip' => $L['manage-post-types-tip'], 'disabled' => false ),

			'manage-video-content' => array( 'name' => 'manage-video-content', 'title' => $L['manage-video-content'], 'tip' => $L['manage-video-content-tip'], 'disabled' => false ),

			'manage-comments' => array( 'name' => 'manage-comments', 'title' => $L['manage-comments'], 'tip' => $L['manage-comments-tip'], 'disabled' => false ),
			
			'manage-own-account' => array( 'name' => 'manage-own-account', 'title' => $L['manage-own-account'], 'tip' => $L['manage-own-account-tip'], 'disabled' => false ),

			'manage-own-posts' => array( 'name' => 'manage-own-posts', 'title' => $L['manage-own-posts'], 'tip' => $L['manage-own-posts-tip'], 'disabled' => false ),

			'manage-own-comments' => array( 'name' => 'manage-own-comments', 'title' => $L['manage-own-comments'], 'tip' => $L['manage-own-comments-tip'], 'disabled' => false ),

			'manage-own-posts-comments' => array( 'name' => 'manage-own-posts-comments', 'title' => $L['manage-own-posts-comments'], 'tip' => $L['manage-own-posts-comments-tip'], 'disabled' => false ),

			'move-posts' => array( 'name' => 'move-posts', 'title' => $L['move-posts'], 'tip' => $L['move-posts-tip'], 'disabled' => false ),
			
			'save-drafts' => array( 'name' => 'save-drafts', 'title' => $L['save-drafts-of-new-posts'], 'tip' => $L['save-drafts-of-new-posts-tip'], 'disabled' => false ),
			
			'read-comments' => array( 'name' => 'read-comments', 'title' => $L['read-comments'], 'tip' => $L['read-comments-tip'], 'disabled' => false ),

			'post-comments' => array( 'name' => 'post-comments', 'title' => $L['post-comments'], 'tip' => $L['post-comments-tip'], 'disabled' => false ),

			'auto-publish-comments' => array( 'name' => 'auto-publish-comments', 'title' => $L['auto-publish-comments'], 'tip' => $L['auto-publish-comments-tip'], 'disabled' => false ),

			'search-posts' => array( 'name' => 'search-posts', 'title' => $L['search-posts'], 'tip' => $L['search-posts-tip'], 'disabled' => false ),
			
			'view-prices' => array( 'name' => 'view-prices', 'title' => $L['view-prices'], 'tip' => $L['view-prices-tip'], 'disabled' => false ),
			
			'view-logs' => array( 'name' => 'view-logs', 'title' => $L['view-logs'], 'tip' => $L['view-logs-tip'], 'disabled' => false ),

			'view-deals-coupons' => array( 'name' => 'view-deals-coupons', 'title' => $L['view-deals-coupons'], 'tip' => $L['view-deals-coupons-tip'], 'disabled' => false ),

			'view-site' => array( 'name' => 'view-site', 'title' => $L['view-site'], 'tip' => $L['view-site-tip'], 'disabled' => false ),

			'view-lighter-version' => array( 'name' => 'view-lighter-version', 'title' => $L['view-lighter-version'], 'tip' => $L['view-lighter-version-tip'], 'disabled' => false ),

			'view-posts' => array( 'name' => 'view-posts', 'title' => $L['view-posts'], 'tip' => $L['view-posts-tip'], 'disabled' => false ),
			
			'view-attachments' => array( 'name' => 'view-attachments', 'title' => $L['view-attachments'], 'tip' => $L['view-attachments-tip'], 'disabled' => false ),

			'view-post-description' => array( 'name' => 'view-post-description', 'title' => $L['view-post-description'], 'tip' => $L['view-post-description-tip'], 'disabled' => false ),

			'view-stats' => array( 'name' => 'view-stats', 'title' => $L['view-stats'], 'tip' => $L['view-stats-tip'], 'disabled' => false ),
			
			'view-emails' => array( 'name' => 'view-emails', 'title' => $L['view-emails'], 'tip' => $L['view-emails-tip'], 'disabled' => false ),

			'view-mlist' => array( 'name' => 'view-mlist', 'title' => $L['view-mlist'], 'tip' => $L['view-mlist-tip'], 'disabled' => false ),

			'view-dashboard' => array( 'name' => 'view-dashboard', 'title' => $L['view-dashboard'], 'tip' => $L['view-dashboard-tip'], 'disabled' => false ),

			'view-admin-bar' => array( 'name' => 'view-admin-bar', 'title' => $L['view-admin-bar'], 'tip' => $L['view-admin-bar-tip'], 'disabled' => false ),

			'view-who-online' => array( 'name' => 'view-who-online', 'title' => $L['view-who-online'], 'tip' => $L['view-who-online-tip'], 'disabled' => false ),
);

//Sort permissions alphabetically
asort( $groupPermissions );

//Model Product Array
$modelProductArray = array(
	'model' => array( 'name' => 'model', 'title'=> __( 'model' ), 'tip' => null, 'disabled' => false, 'dbname' => 'model' ),
);

//External Comments Array
$externalCommentsArray = array( 'none' => array( 'title' => $L['none'] ), 'disqus' => array( 'title' => $L['disqus'], 'label' => $L['disqus-shortname'], 'tip' => $L['disqus-shortname-tip'] ), 'intensedebate' => array( 'title' => $L['intensedebate'], 'label' => $L['intensedebate'], 'tip' => $L['intensedebate-shortname-tip'] ), 'fb-comments' => array( 'title' => $L['facebook-comments'], 'label' => $L['facebook-comments'], 'tip' => $L['facebook-comments-shortname-tip'] ) );

//blogTypesArray Array
$blogTypesArray = array( 'normal' => array( 'title' => $L['normal'] ), 'store' => array( 'title' => $L['store'] ), 'forum' => array( 'title' => $L['forum'] ), 'videos' => array( 'title' => $L['videos'] ), 'coupons-and-deals' => array( 'title' => $L['coupons-and-deals'] ), 'multivendor-marketplace' => array( 'title' => $L['multivendor-marketplace'] ), 'compare-prices' => array( 'title' => $L['compare-prices'] ), 'classifieds' => array( 'title' => $L['classifieds'] ) );

//Source of tags Display Array
$sourceTagsDisplayArray = array( 'all' => array( 'title' => $L['all'] ), 'selected' => array( 'title' => $L['only-selected'] ), 'except' => array( 'title' => $L['except-selected'] ) );

//Source of category Display Array
$sourceCategoryDisplayArray = array( 'all' => array( 'title' => $L['all'] ), 'parent' => array( 'title' => $L['only-parent'] ), 'selected' => array( 'title' => $L['only-selected'] ), 'except' => array( 'title' => $L['except-selected'] ) );

//Source of select If Array
$sourceOptionsSelectIfArray = array( 'category' => array( 'title' => $L['category'] ), 'tag' => array( 'title' => $L['tag'] ), 'custom-post-type' => array( 'title' => $L['custom-post-type'] )/*, 'attribute ' => array( 'title' => $L['attribute'] ), 'page' => array( 'title' => $L['page'] )*/ );

//Source of select If Array for categories/tags/blogs only
$sourceOptionsCatSelectIfArray = array( 'category' => array( 'title' => $L['category'] ), 'blog' => array( 'title' => $L['blog'] ), 'tag' => array( 'title' => $L['tag'] ) );

//Target of filters Array
$targetOptionsArray = array( 'categories' => array( 'title' => $L['categories'] ), 'tags' => array( 'title' => $L['tags'] ), 'merchants' => array( 'title' => $L['merchants'] ), 'manufacturers' => array( 'title' => $L['manufacturers'] ), 'attribute' => array( 'title' => $L['attribute'] ), 'prices' => array( 'title' => $L['prices'] ) );

//Source of options Array
$sourceOptionsArray = array( 'custom-filters' => array( 'title' => $L['custom-filters'] ), 'category' => array( 'title' => $L['category'] ), 'tag' => array( 'title' => $L['tag'] ), 'custom-post-type' => array( 'title' => $L['custom-post-type'] ), 'stock-status' => array( 'title' => $L['stock-status'] ), 'merchants' => array( 'title' => $L['merchants'] ), 'manufacturers' => array( 'title' => $L['manufacturers'] ), 'ratings' => array( 'title' => $L['ratings'] ) /*'attribute' => array( 'title' => $L['attribute'] ), 'prices' => array( 'title' => $L['prices'] ),  'attribute-group' => array( 'title' => $L['attribute-group'] )*/);

//Sort Types Array
$sortTypesArray = array( 'checkbox' => array( 'title' => $L['checkbox'] ), 'checkbox-multiple' => array( 'title' => $L['checkbox-multiple'] ), 'radio' => array( 'title' => $L['radio'] ), 'dropdown' => array( 'title' => $L['dropdown'] ), 'dropdown-multiple' => array( 'title' => $L['dropdown-multiple'] ), 'slider' => array( 'title' => $L['slider'] ) );

//Site Hosts Array
$siteHosts = array(
		'self' => array( 'name' => 'self', 'title'=> $L['self-hosted'], 'disabled' => false, 'data' => array() ),
		'blogger' => array( 'name' => 'blogger', 'title'=> $L['hosted-on-blogger'], 'disabled' => false, 'data' => array() ),
		'wordpress' => array( 'name' => 'wordpress', 'title'=> $L['hosted-on-wordpress-com'], 'disabled' => false, 'data' => array() ),
);

//links Redirection Array
$linksRedirectionArray = array(
		'307' => array( 'name' => '307', 'title'=> $L['307-temporary'], 'disabled' => false, 'data' => array() ),
		'302' => array( 'name' => '302', 'title'=> $L['302-temporary'], 'disabled' => false, 'data' => array() ),
		'301' => array( 'name' => '301', 'title'=> $L['301-permanent'], 'disabled' => false, 'data' => array() ),
		'meta-refresh' => array( 'name' => 'meta-refresh', 'title'=> $L['meta-refresh'], 'disabled' => false, 'data' => array() ),
		'javascript' => array( 'name' => 'javascript', 'title'=> $L['javascript'], 'disabled' => false, 'data' => array() )
);

//Auto-Embed content sources
$EmbedContentSources = array(
	'youtube' => array( 'name' => 'youtube', 'title'=> $L['youtube'], 'disabled' => false, 'data' => array() ),
	'reddit' => array( 'name' => 'reddit', 'title'=> $L['reddit'], 'disabled' => false, 'data' => array() ),
	'twitter' => array( 'name' => 'twitter', 'title'=> $L['twitter'], 'disabled' => false, 'data' => array() ),
	'dailymotion' => array( 'name' => 'dailymotion', 'title'=> $L['dailymotion'], 'disabled' => false, 'data' => array() ),
	'youku' => array( 'name' => 'youku', 'title'=> $L['youku'], 'disabled' => false, 'data' => array() ),
	'vimeo' => array( 'name' => 'vimeo', 'title'=> $L['vimeo'], 'disabled' => false, 'data' => array() ),
	'fb-videos' => array( 'name' => 'fb-videos', 'title'=> $L['fb-videos'], 'disabled' => false, 'data' => array() ),
	'fb-posts' => array( 'name' => 'fb-posts', 'title'=> $L['fb-posts'], 'disabled' => false, 'data' => array() ),
	'instagram' => array( 'name' => 'instagram', 'title'=> $L['instagram'], 'disabled' => false, 'data' => array() ),
	'nytimes' => array( 'name' => 'nytimes', 'title'=> $L['nytimes'], 'disabled' => false, 'data' => array() ),
	'tik-tok' => array( 'name' => 'tik-tok', 'title'=> $L['tik-tok'], 'disabled' => false, 'data' => array() ),
	'veoh' => array( 'name' => 'veoh', 'title'=> __( 'veoh' ), 'disabled' => false, 'data' => array() )
);

//Sort sources alphabetically
asort( $EmbedContentSources );

//Auto-Embed content sources
$EmbedHtmlData = array(
		'youtube' => array( 'url' => 'https://www.youtube.com/watch?v={{id}}', 'embed-url' => 'https://www.youtube.com/embed/{{id}}{{start}}{{end}}', 'allow' => 'accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture', 'amp-iframe' => '<amp-youtube width="{{width}}" height="{{height}}" layout="responsive" data-videoid="{{id}}"></amp-youtube>', 'amp-alt-iframe' => '<amp-iframe width="{{width}}" height="{{height}}" layout="responsive" sandbox="allow-scripts allow-same-origin allow-presentation" src="{{url}}"></amp-iframe>', 'extra-html' => 'allowfullscreen' ),
		
		'twitter' => array( 'url' => null, 'embed-url' => null, 'allow' => null, 'amp-iframe' => '<amp-twitter width="{{width}}" height="{{height}}" layout="responsive" data-videoid="{{id}}"></amp-twitter>', 'amp-alt-iframe' => null, 'extra-html' => null ),
		
		'fb-videos' => array( 'url' => 'https://www.facebook.com/plugins/video.php?height={{height}}&href={{url}}&show_text=true&width={{width}}', 'allow' => 'autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share', 'amp-iframe' => '<amp-iframe width="{{width}}" height="{{height}}" layout="responsive" src="{{url}}"></amp-iframe>', 'amp-alt-iframe' => null, 'amp-url' => 'https://www.facebook.com/plugins/video.php?href={{url}}&show_text=0&amp;{{width}}', 'extra-html' => null  ),

		'fb-posts' => array( 'allow' => 'autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share', 'url' => 'https://www.facebook.com/plugins/post.php?href={{url}}&show_text=true&width={{width}}', 'amp-url'=> 'https://www.facebook.com/plugins/post.php?href={{url}}&show_text=0&amp;{{width}}', 'amp-alt-iframe' => null, 'amp-iframe' => '<amp-iframe width="{{width}}" height="{{height}}" layout="responsive" src="{{url}}"></amp-iframe>', 'extra-html' => null ),
		
		'tik-tok' => array( 'url' => null, 'embed-url' => 'https://www.tiktok.com/embed/v2/{{id}}', 'allow' => null, 'amp-iframe' => '<amp-tiktok width="{{width}}" height="{{height}}" layout="responsive" data-src="{{url}}"></amp-tiktok>', 'amp-alt-iframe' => null, 'extra-html' => null ),

		'reddit' => array( 'url' => null, 'embed-url' => null, 'allow' => null, 'amp-iframe' => '<amp-iframe width="{{width}}" height="{{height}}" sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox" layout="responsive" resizable frameborder="0" src="https://embed.redditmedia.com/widgets/card.html?amp=1&created={{time}}&url={{url}}"><div overflow>Click To Expand</div><amp-img layout="fill" src="{{img}}&width=640&crop=smart" placeholder></amp-img></amp-iframe>', 'amp-alt-iframe' => null, 'extra-html' => null ),
		
		'instagram' => array( 'url' => null, 'embed-url' => null, 'allow' => null, 'amp-iframe' => '<amp-instagram data-shortcode="{{url}}" data-captioned width="{{width}}" height="{{height}}" layout="responsive"></amp-instagram>', 'amp-alt-iframe' => null, 'extra-html' => null ),
		
		'vimeo' => array( 'url' => null, 'embed-url' => 'https://player.vimeo.com/video/{{id}}?autoplay={{autoplay}}', 'allow' => 'autoplay; fullscreen', 'amp-iframe' => '<amp-iframe width="{{width}}" height="{{height}}" layout="responsive" src="https://player.vimeo.com/video/{{id}}"></amp-iframe>', 'amp-alt-iframe' => null, 'extra-html' => null ),
		
		'dailymotion' => array( 'url' => null, 'embed-url' => 'https://www.dailymotion.com/embed/video/{{id}}?autoplay={{autoplay}}', 'allow' => 'autoplay; fullscreen', 'amp-iframe' => '<amp-iframe width="{{width}}" height="{{height}}" layout="responsive" src="//www.dailymotion.com/embed/video/{{id}}"></amp-iframe>', 'amp-alt-iframe' => null, 'extra-html' => null ),
		
		'youku' => array( 'url' => null, 'embed-url' => '//player.youku.com/embed/{{id}}?autoplay={{autoplay}}', 'allow' => 'autoplay; fullscreen', 'amp-iframe' => '<amp-iframe width="{{width}}" height="{{height}}" layout="responsive" src="//player.youku.com/embed/{{id}}"></amp-iframe>', 'amp-alt-iframe' => null, 'extra-html' => null ),
		
		'nytimes-videos' => array( 'url' => null, 'embed-url' => 'https://www.nytimes.com/video/players/offsite/index.html?videoId={{id}}', 'allow' => null, 'amp-iframe' => '<amp-iframe width="{{width}}" height="{{height}}" layout="responsive" src="//player.youku.com/embed/{{id}}"></amp-iframe>', 'amp-alt-iframe' => null, 'extra-html' => 'title="New York Times Video - Embed Player"' ),
		
		'nytimes-posts' => array( 'url' => null, 'embed-url' => 'https://www.nytimes.com/svc/oembed/html/?url={{url}}', 'allow' => null, 'amp-iframe' => '<amp-iframe width="{{width}}" height="{{height}}" layout="responsive" src="{{url}}"></amp-iframe>', 'amp-alt-iframe' => null, 'extra-html' => null ),
		
		'generic' => array( 'url' => null, 'embed-url' => '{{url}}', 'allow' => 'autoplay; fullscreen', 'amp-iframe' => '<amp-iframe width="{{width}}" height="{{height}}" layout="responsive" src="{{url}}"></amp-iframe>', 'amp-alt-iframe' => null, 'extra-html' => null ),
);

//validMediaTypes Array
$validMediaTypes = array(
		'text' => array( 'name' => 'text', 'title' => $L['text'], 'data' => array( 
			'css', 'txt', 'doc', 'docx', 'odt', 'pdf', 'rtf', 'tex', 'wpd' )
		),
		'image' => array( 'name' => 'image', 'title' => $L['image'], 'data' => array(
			'jpg', 'jpeg', 'tif', 'tiff', 'png', 'gif', 'bmp', 'ico', 'jfif', 'pjpeg', 'pjp', 'webp' )
		),
		'audio' => array( 'name' => 'audio', 'title' => $L['audio'], 'data' => array(
			'omg', 'mp3', 'flac', 'saf', 'm4r', 'midi', 'mid', 'ogg', 'wav', 'm4a', 'ac3', 'aiff', 'm3u', 'aa3', 'aac', 'alac' )
		),
		'video' => array( 'name' => 'video', 'title' => $L['video'], 'data' => array(
			'mp4', 'mov', 'wmv', 'avi', 'avchd', 'flv', 'mkv', 'webm', 'mpg', 'mp4', 'mpeg', 'qt' )
		),
		'application' => array( 'name' => 'application', 'title' => $L['application'], 'data' => array(
			'bat', 'exe', 'bin', 'dmg', 'app' )
		),
		'compressed' => array( 'name' => 'compressed', 'title' => $L['compressed'], 'data' => array(
			'bz', 'cab', 'gz', 'iso', 'pak', 'rar', 'rpm', 'tar', 'tbz', 'tgz', 'zip', 'zipx' )
		)
);

//adsPosition Array
$adsPosition = array(
	
	'custom' => array( 'name' => 'custom', 'title' => $L['custom'], 'tip' => null, 'disabled' => false ),
	
	'top' => array( 'name' => 'top', 'title' => $L['at-the-top-of-the-site'], 'tip' => null, 'disabled' => false ),
	
	'end' => array( 'name' => 'end', 'title' => $L['at-the-end-of-the-site'], 'tip' => null, 'disabled' => false ),
	
	'sidebar' => array( 'name' => 'sidebar', 'title' => $L['sidebar'], 'tip' => null, 'disabled' => false ),
	
	'above-headline' => array( 'name' => 'above-headline', 'title' => $L['above-headline'], 'tip' => null, 'disabled' => false ),
	
	'post-beginning' => array( 'name' => 'post-beginning', 'title' => $L['at-the-beginning-of-the-post'], 'tip' => null, 'disabled' => false ),
	
	'post-middle' => array( 'name' => 'post-middle', 'title' => $L['at-the-middle-of-the-post'], 'tip' => null, 'disabled' => false ),
	
	'post-end' => array( 'name' => 'post-end', 'title' => $L['at-the-end-of-the-post'], 'tip' => null, 'disabled' => false ),
	
	'post-lists' => array( 'name' => 'post-lists', 'title' => $L['post-lists'], 'tip' => null, 'disabled' => false ),
	
	'into-header' => array( 'name' => 'into-header', 'title' => $L['into-header'], 'tip' => null, 'disabled' => false ),
	
	'into-footer' => array( 'name' => 'into-footer', 'title' => $L['into-footer'], 'tip' => null, 'disabled' => false ),
);

$registeredSlugs = array( 'page', 'login', 'logout', 'register', 'forgot-password', 'sitemap', 'feed', 'profile', 'search', 'admin', 'out', 'api', ADMIN_SLUG );

//adsBoxPosition Array
$adsBoxPosition = array(
	'left' => array( 'name' => 'left', 'title' => $L['left'], 'tip' => null, 'disabled' => false ),
	'right' => array( 'name' => 'right', 'title' => $L['right'], 'tip' => null, 'disabled' => false ),
	'center' => array( 'name' => 'center', 'title' => $L['center'], 'tip' => null, 'disabled' => false )
);

//adsTypes Array
$adsTypes = array(
	'plain-text' => array( 'name' => 'plain-text', 'title' => $L['plain-text-and-code'], 'tip' => $L['plain-text-and-code-tip'], 'disabled' => false ),
	'dummy' => array( 'name' => 'dummy', 'title' => $L['dummy'], 'tip' => $L['dummy-ad-tip'], 'disabled' => false ),
	'image' => array( 'name' => 'image', 'title' => $L['image-ad'], 'tip' => $L['image-ad-tip'], 'disabled' => false )
);

//secFormArray Array
$secFormArray = array(
	'everywhere' => array( 'name' => 'everywhere', 'title' => $L['everywhere'], 'tip' => null, 'disabled' => false ),
	'login-form' => array( 'name' => 'login-form', 'title' => $L['login-form'], 'tip' => null, 'disabled' => false ),
	'registration-form' => array( 'name' => 'registration-form', 'title' => $L['registration-form'], 'tip' => null, 'disabled' => false ),
	'comment-form' => array( 'name' => 'comment-form', 'title' => $L['comment-form'], 'tip' => null, 'disabled' => false ),
	'lost-password-form' => array( 'name' => 'lost-password-form', 'title' => $L['lost-password-form'], 'tip' => null, 'disabled' => false ),
	'contact-form' => array( 'name' => 'contact-form', 'title' => $L['contact-form'], 'tip' => null, 'disabled' => false )
);

//groupTypes Array
$groupTypes = array(
	'private-group' => array( 'name' => 'private-group', 'title' => $L['private-group'], 'tip' => null, 'disabled' => false ),
	
	'requestable-group' => array( 'name' => 'requestable-group', 'title' => $L['requestable-group'], 'tip' => null, 'disabled' => false ),
	
	'free-group' => array( 'name' => 'free-group', 'title' => $L['free-group'], 'tip' => null, 'disabled' => false ),
	
	'post-based-group' => array( 'name' => 'post-based-group', 'title' => $L['post-based-group'], 'tip' => null, 'disabled' => false ),
);

//widget Types Array
$widgetTypes = array(
		'simple' => array( 'name' => 'simple', 'title' => $L['simple-widget'], 'tip' => null, 'disabled' => false ),
		'html' => array( 'name' => 'html', 'title' => $L['html-widget'], 'tip' => null, 'disabled' => false ),
		'php' => array( 'name' => 'php', 'title' => $L['php-widget'], 'tip' => null, 'disabled' => false ),
		'ad' => array( 'name' => 'ad', 'title' => $L['ad-widget'], 'tip' => null, 'disabled' => false ),
		'built-in' => array( 'name' => 'built-in', 'title' => $L['built-in-widget'], 'tip' => null, 'disabled' => false ),
);

//builtInwidget Array
$builtInWidgets = array(
	'latest-posts' => array( 'name' => 'latest-posts', 'title' => $L['latest-posts'], 'tip' => null, 'disabled' => false, 'type' => 'built-in' ),
	'top-posts' => array( 'name' => 'top-posts', 'title' => $L['top-posts'], 'tip' => null, 'disabled' => false, 'type' => 'built-in' ),
	'latest-comments' => array( 'name' => 'latest-comments', 'title' => $L['latest-comments'], 'tip' => null, 'disabled' => false, 'type' => 'built-in' ),
	'user-cp' => array( 'name' => 'user-cp', 'title' => $L['user-cp'], 'tip' => null, 'disabled' => false, 'type' => 'built-in' ),
	'categories-list' => array( 'name' => 'categories-list', 'title' => $L['categories-list'], 'tip' => null, 'disabled' => false, 'type' => 'built-in' ),
	'tags-list' => array( 'name' => 'tags-list', 'title' => $L['tags-list'], 'tip' => null, 'disabled' => false, 'type' => 'built-in' ),
	'search-form' => array( 'name' => 'search-form', 'title' => $L['search-form'], 'tip' => null, 'disabled' => false, 'type' => 'built-in' ),
	'languages-list' => array( 'name' => 'languages-list', 'title' => $L['languages-list'], 'tip' => null, 'disabled' => false, 'type' => 'built-in' ),
	'simple' => array( 'name' => 'simple', 'title' => $L['simple-widget'], 'tip' => null, 'disabled' => false, 'type' => 'simple' ),
	'html' => array( 'name' => 'html', 'title' => $L['html-widget'], 'tip' => null, 'disabled' => false, 'type' => 'html' ),
	'php' => array( 'name' => 'ad', 'title' => $L['php-widget'], 'tip' => null, 'disabled' => false, 'type' => 'php' ),
	'ad' => array( 'name' => 'ad', 'title' => $L['ad-widget'], 'tip' => null, 'disabled' => false, 'type' => 'ad' ),
);

//widget Types Array
$widgetVisibilityOptions = array(
	'page' => array( 'name' => 'page', 'title' => $L['page-visibility-widget'], 'tip' => null, 'disabled' => false ),
	'post' => array( 'name' => 'post', 'title' => $L['post-visibility-widget'], 'tip' => null, 'disabled' => false ),
	'category' => array( 'name' => 'category', 'title' => $L['category-visibility-widget'], 'tip' => null, 'disabled' => false ),
	'home' => array( 'name' => 'home', 'title' => $L['home-visibility-widget'], 'tip' => null, 'disabled' => false ),
	'tag' => array( 'name' => 'tag', 'title' => $L['tag-visibility-widget'], 'tip' => null, 'disabled' => false ),
	'archive' => array( 'name' => 'archive', 'title' => $L['archive-visibility-widget'], 'tip' => null, 'disabled' => false ),
);

//socialNetworksArray
$socialNetworksArray = array(
	'twitter' => array( 'name' => 'twitter', 'title'=> $L['twitter'], 'disabled' => false ),
	'facebook' => array( 'name' => 'facebook', 'title'=> $L['facebook'], 'disabled' => false ),
	'instagram' => array( 'name' => 'instagram', 'title'=> $L['instagram'], 'disabled' => false ),
	'youtube' => array( 'name' => 'youtube', 'title'=> $L['youtube'], 'disabled' => false ),
	'gitlab' => array( 'name' => 'gitlab', 'title'=> $L['gitlab'], 'disabled' => false ),
	'github' => array( 'name' => 'github', 'title'=> $L['github'], 'disabled' => false ),
	'linkedin' => array( 'name' => 'linkedin', 'title'=> $L['linkedin'], 'disabled' => false ),
	'codepen' => array( 'name' => 'codepen', 'title'=> $L['codepen'], 'disabled' => false ),
	'mastodon' => array( 'name' => 'mastodon', 'title'=> $L['mastodon'], 'disabled' => false ),
	'vk' => array( 'name' => 'vk', 'title'=> $L['vk'], 'disabled' => false ),
	'soundcloud' => array( 'name' => 'soundcloud', 'title'=> $L['soundcloud'], 'disabled' => false ),
	'tumblr' => array( 'name' => 'tumblr', 'title'=> $L['tumblr'], 'disabled' => false ),
	'wikipedia' => array( 'name' => 'wikipedia', 'title'=> $L['wikipedia'], 'disabled' => false ),
	'pinterest' => array( 'name' => 'pinterest', 'title'=> $L['pinterest'], 'disabled' => false )
);

//importDataArray Array
$importDataArray = array(
	'choose' => array( 'name' => 'choose', 'title'=> $L['choose'], 'disabled' => false, 'class' => null, 'file' => null ),

	'xml' => array( 'name' => 'xml', 'title'=> $L['xml-file'], 'disabled' => false, 'class' => 'XML', 'file' => CLASSES_ROOT . 'import' . DS . 'XML.class.php' ),
	
	'wordpress' => array( 'name' => 'wordpress', 'title'=> $L['wordpress'], 'disabled' => false, 'class' => 'WP', 'file' => CLASSES_ROOT . 'import' . DS . 'WP.class.php' )
);

//redirMatchedOptions Array
$redirMatchedOptions = array(
	'redirect-to-url' => array( 'name' => 'redirect-to-url', 'title'=> $L['redirect-to-url'], 'disabled' => false, 'data' => array() ),
	'redirect-to-random' => array( 'name' => 'redirect-to-random', 'title'=> $L['redirect-to-random'], 'disabled' => false, 'data' => array() ),
	'pass-through' => array( 'name' => 'pass-through', 'title'=> $L['pass-through'], 'disabled' => false, 'data' => array() ),
	'error-404' => array( 'name' => 'error-404', 'title'=> $L['error-404'], 'disabled' => false, 'data' => array() ),
);

//Post Editor Array
$postEditorOptions = array(
			'simplemde' => array( 'name' => 'simplemde', 'title'=> $L['easymde-markdown-editor'], 'disabled' => false, 
			'data' => array(),
			'default-values' => 
				array(
				'toolbar' => '"bold", "italic", "heading", "|", "quote", "unordered-list", "|", "image", "code", "horizontal-rule"',

				'tab-size' => '2',
				'auto-save' => '60',
				'enable-auto-drafts' => false,
				'spell-checker' => false,
				'plugins' => 'disable',
				)
			),
			
			'tinymce' => array( 'name' => 'tinymce', 'title'=> $L['tinymce'], 'disabled' => false, 
			'data' => array(),
			'default-values' => 
				array(
					'toolbar' => 'formatselect bold italic forecolor backcolor removeformat | bullist numlist table | blockquote alignleft aligncenter alignright | link unlink pagebreak image code',
					
					'tab-size' => '2',
					'auto-save' => '60',
					'enable-auto-drafts' => false,
					'spell-checker' => false,
					'plugins' => 'code autolink image link pagebreak advlist lists textpattern table',
				)
			),
			
			'simple' => array( 'name' => 'simple', 'title'=> $L['simple-editor'], 'disabled' => false, 
			'data' => array(),
			'default-values' => 
				array(
					'toolbar' => 'disable',
					'tab-size' => 'disable',
					'auto-save' => '60',
					'enable-auto-drafts' => false,
					'spell-checker' => 'disable',
					'plugins' => 'disable',
				)
			),
			
			'editor-js' => array( 'name' => 'editor-js', 'title'=> $L['editor-js'], 'disabled' => false, 'data' => array(),
			'default-values' => 
				array(
					'toolbar' => 'disable',
					'tab-size' => 'disable',
					'auto-save' => '60',
					'enable-auto-drafts' => false,
					'spell-checker' => 'disable',
					'plugins' => 'disable',
				)
			)
			//'quill-bubble' => array( 'name' => 'quill-bubble', 'title'=> $L['quill-bubble'], 'disabled' => false, 'data' => array() ),
);

//redirHttpOptions Array
$redirHttpOptions = array(
			'disable' => array( 'name' => 'disable', 'title'=> $L['disable'], 'disabled' => false, 'data' => array() ),
			'301' => array( 'name' => '301', 'title'=> '301 Moved Permanently', 'disabled' => false, 'data' => array() ),
			'302' => array( 'name' => '302', 'title'=> '302 Found (Previously "Moved temporarily")', 'disabled' => false, 'data' => array() ),
			'303' => array( 'name' => '303', 'title'=> '303 See Other', 'disabled' => false, 'data' => array() ),
			'304' => array( 'name' => '304', 'title'=> '304 Not Modified', 'disabled' => false, 'data' => array() ),
			'307' => array( 'name' => '307', 'title'=> '307 Temporary Redirect', 'disabled' => false, 'data' => array() ),
			'308' => array( 'name' => '308', 'title'=> '308 Permanent Redirect', 'disabled' => false, 'data' => array() ),
			'400' => array( 'name' => '400', 'title'=> '400 Bad Request', 'disabled' => false, 'data' => array() ),
			'401' => array( 'name' => '401', 'title'=> '401 Unauthorized', 'disabled' => false, 'data' => array() ),
			'403' => array( 'name' => '403', 'title'=> '403 Forbidden', 'disabled' => false, 'data' => array() ),
			'404' => array( 'name' => '404', 'title'=> '404 Not Found', 'disabled' => false, 'data' => array() ),
			'410' => array( 'name' => '410', 'title'=> '410 Gone', 'disabled' => false, 'data' => array() ),
			'418' => array( 'name' => '418', 'title'=> '418 I\'m a teapot', 'disabled' => false, 'data' => array() ),
			'451' => array( 'name' => '451', 'title'=> '451 Unavailable For Legal Reasons', 'disabled' => false, 'data' => array() ),
			'500' => array( 'name' => '500', 'title'=> '500 Internal Server Error', 'disabled' => false, 'data' => array() ),
			'501' => array( 'name' => '501', 'title'=> '501 Not Implemented', 'disabled' => false, 'data' => array() ),
			'502' => array( 'name' => '502', 'title'=> '502 Bad Gateway', 'disabled' => false, 'data' => array() ),
			'503' => array( 'name' => '503', 'title'=> '503 Service Unavailable', 'disabled' => false, 'data' => array() ),
			'504' => array( 'name' => '504', 'title'=> '504 Gateway Timeout', 'disabled' => false, 'data' => array() ),
);

//organizationTypes Array
$organizationTypes = array(
					'Organization' => array( 'title' => $L['organization'] ),
					'Corporation' => array( 'title' => $L['corporation'] ),
					'Airline' => array( 'title' => $L['airline'] ),
					'Consortium' => array( 'title' => $L['consortium'] ),
					'EducationalOrganization' => array( 'title' => $L['educational-organization'] ),
					'CollegeOrUniversity' => array( 'title' => '&mdash; ' . $L['college-or-university'] ),
					'ElementarySchool' => array( 'title' => '&mdash; ' . $L['elementary-school'] ),
					'HighSchool' => array( 'title' => '&mdash; ' . $L['high-school'] ),
					'MiddleSchool' => array( 'title' => '&mdash; ' . $L['middle-school'] ),
					'Preschool' => array( 'title' => '&mdash; ' . $L['pre-school'] ),
					'School' => array( 'title' => $L['school'] ),
					'GovernmentOrganization' => array( 'title' => $L['government-organization'] ),
					'MedicalOrganization' => array( 'title' => $L['medical-organization'] ),
					'DiagnosticLab' => array( 'title' => '&mdash; ' . $L['diagnostic-lab'] ),
					'VeterinaryCare' => array( 'title' => '&mdash; ' . $L['veterinary-care'] ),
					'NGO' => array( 'title' => $L['ngo'] ),
					'PerformingGroup' => array( 'title' => $L['performing-group'] ),
					'DanceGroup' => array( 'title' => '&mdash; ' . $L['dance-group'] ),
					'MusicGroup' => array( 'title' => '&mdash; ' . $L['music-group'] ),
					'TheaterGroup' => array( 'title' => '&mdash; ' . $L['theater-group'] ),
					'NewsMediaOrganization' => array( 'title' => $L['news-media-organization'] ),
					'Project' => array( 'title' => $L['project'] ),
					'ResearchProject' => array( 'title' => '&mdash; ' . $L['research-project'] ),
					'FundingAgency' => array( 'title' => '&mdash; ' . $L['funding-agency'] ),
					'SportsOrganization' => array( 'title' => $L['sports-organization'] ),
					'SportsTeam' => array( 'title' => '&mdash; ' . $L['sports-team'] ),
					'LibrarySystem' => array( 'title' => $L['library-system'] ),
					'WorkersUnion' => array( 'title' => $L['workers-union'] )
);

$schemaRepresents = array(
	'person' 			=> array( 'name' => 'person', 'title'=> $L['personal-website'], 'disabled' => false, 'data' => array() ),
	'other' 			=> array( 'name' => 'Otherbusiness', 'title'=> $L['other-business'], 'disabled' => false, 'data' => array() ),
	'organization' 		=> array( 'name' => 'organization', 'title'=> $L['organization'], 'disabled' => false, 'data' => array() ),
	'small-business' 	=> array( 'name' => 'Smallbusiness', 'title'=> $L['community-website'], 'disabled' => false, 'data' => array() ),
	'webshop' 			=> array( 'name' => 'Webshop', 'title'=> $L['webshop'], 'disabled' => false, 'data' => array() )
);

$countries = array('afghanistan' => array( 'title' => 'Afghanistan' ), 'albania' => array( 'title' => 'Albania' ), 'algeria' => array( 'title' => 'Algeria' ), 'american-samoa' => array( 'title' => 'American Samoa' ), 'andorra' => array( 'title' => 'Andorra' ), 'angola' => array( 'title' => 'Angola' ), 'anguilla' => array( 'title' => 'Anguilla' ), 'antarctica' => array( 'title' => 'Antarctica' ), 'antigua-and-barbuda' => array( 'title' => 'Antigua and Barbuda' ), 'argentina' => array( 'title' => 'Argentina' ), 'armenia' => array( 'title' => 'Armenia' ), 'aruba' => array( 'title' => 'Aruba' ), 'australia' => array( 'title' => 'Australia' ), 'austria' => array( 'title' => 'Austria' ), 'azerbaijan' => array( 'title' => 'Azerbaijan' ), 'bahamas' => array( 'title' => 'Bahamas' ), 'bahrain' => array( 'title' => 'Bahrain' ), 'bangladesh' => array( 'title' => 'Bangladesh' ), 'barbados' => array( 'title' => 'Barbados' ), 'belarus' => array( 'title' => 'Belarus' ), 'belgium' => array( 'title' => 'Belgium' ), 'belize' => array( 'title' => 'Belize' ), 'benin' => array( 'title' => 'Benin' ), 'bermuda' => array( 'title' => 'Bermuda' ), 'bhutan' => array( 'title' => 'Bhutan' ), 'bolivia' => array( 'title' => 'Bolivia' ), 'bosnia-and-herzegowina' => array( 'title' => 'Bosnia and Herzegowina' ), 'botswana' => array( 'title' => 'Botswana' ), 'bouvet-island' => array( 'title' => 'Bouvet Island' ), 'brazil' => array( 'title' => 'Brazil' ), 'british-indian-ocean-territory' => array( 'title' => 'British Indian Ocean Territory' ), 'brunei-darussalam' => array( 'title' => 'Brunei Darussalam' ), 'bulgaria' => array( 'title' => 'Bulgaria' ), 'burkina-faso' => array( 'title' => 'Burkina Faso' ), 'burundi' => array( 'title' => 'Burundi' ), 'cambodia' => array( 'title' => 'Cambodia' ), 'cameroon' => array( 'title' => 'Cameroon' ), 'canada' => array( 'title' => 'Canada' ), 'cape-verde' => array( 'title' => 'Cape Verde' ), 'cayman-islands' => array( 'title' => 'Cayman Islands' ), 'central-african-republic' => array( 'title' => 'Central African Republic' ), 'chad' => array( 'title' => 'Chad' ), 'chile' => array( 'title' => 'Chile' ), 'china' => array( 'title' => 'China' ), 'christmas-island' => array( 'title' => 'Christmas Island' ), 'cocos-keeling-islands' => array( 'title' => 'Cocos (Keeling) Islands' ), 'colombia' => array( 'title' => 'Colombia' ), 'comoros' => array( 'title' => 'Comoros' ), 'congo' => array( 'title' => 'Congo' ), 'congo-democratic-republic' => array( 'title' => 'Congo, the Democratic Republic of the' ), 'cook-islands' => array( 'title' => 'Cook Islands' ), 'costa-rica' => array( 'title' => 'Costa Rica' ), 'cote-divoire' => array( 'title' => 'Cote d\'Ivoire' ), 'croatia-hrvatska' => array( 'title' => 'Croatia (Hrvatska)' ), 'cuba' => array( 'title' => 'Cuba' ), 'cyprus' => array( 'title' => 'Cyprus' ), 'czech-republic' => array( 'title' => 'Czech Republic' ), 'denmark' => array( 'title' => 'Denmark' ), 'djibouti' => array( 'title' => 'Djibouti' ), 'dominica' => array( 'title' => 'Dominica' ), 'dominican-republic' => array( 'title' => 'Dominican Republic' ), 'east-timor' => array( 'title' => 'East Timor' ), 'ecuador' => array( 'title' => 'Ecuador' ), 'egypt' => array( 'title' => 'Egypt' ), 'el-salvador' => array( 'title' => 'El Salvador' ), 'equatorial-guinea' => array( 'title' => 'Equatorial Guinea' ), 'eritrea' => array( 'title' => 'Eritrea' ), 'estonia' => array( 'title' => 'Estonia' ), 'ethiopia' => array( 'title' => 'Ethiopia' ), 'falkland-islands-malvinas' => array( 'title' => 'Falkland Islands (Malvinas)' ), 'faroe-islands' => array( 'title' => 'Faroe Islands' ), 'fiji' => array( 'title' => 'Fiji' ), 'finland' => array( 'title' => 'Finland' ), 'france' => array( 'title' => 'France' ), 'france-metropolitan' => array( 'title' => 'France Metropolitan' ), 'french-guiana' => array( 'title' => 'French Guiana' ), 'french-polynesia' => array( 'title' => 'French Polynesia' ), 'french-southern-territories' => array( 'title' => 'French Southern Territories' ), 'gabon' => array( 'title' => 'Gabon' ), 'gambia' => array( 'title' => 'Gambia' ), 'georgia' => array( 'title' => 'Georgia' ), 'germany' => array( 'title' => 'Germany' ), 'ghana' => array( 'title' => 'Ghana' ), 'gibraltar' => array( 'title' => 'Gibraltar' ), 'greece' => array( 'title' => 'Greece' ), 'greenland' => array( 'title' => 'Greenland' ), 'grenada' => array( 'title' => 'Grenada' ), 'guadeloupe' => array( 'title' => 'Guadeloupe' ), 'guam' => array( 'title' => 'Guam' ), 'guatemala' => array( 'title' => 'Guatemala' ), 'guinea' => array( 'title' => 'Guinea' ), 'guinea-bissau' => array( 'title' => 'Guinea-Bissau' ), 'guyana' => array( 'title' => 'Guyana' ), 'haiti' => array( 'title' => 'Haiti' ), 'heard-and-mc-donald-islands' => array( 'title' => 'Heard and Mc Donald Islands' ), 'holy-see-vatican-city-state' => array( 'title' => 'Holy See (Vatican City State)' ), 'honduras' => array( 'title' => 'Honduras' ), 'hong-kong' => array( 'title' => 'Hong Kong' ), 'hungary' => array( 'title' => 'Hungary' ), 'iceland' => array( 'title' => 'Iceland' ), 'india' => array( 'title' => 'India' ), 'indonesia' => array( 'title' => 'Indonesia' ), 'iran-islamic-republic' => array( 'title' => 'Iran (Islamic Republic of)' ), 'iraq' => array( 'title' => 'Iraq' ), 'ireland' => array( 'title' => 'Ireland' ), 'israel' => array( 'title' => 'Israel' ), 'italy' => array( 'title' => 'Italy' ), 'jamaica' => array( 'title' => 'Jamaica' ), 'japan' => array( 'title' => 'Japan' ), 'jordan' => array( 'title' => 'Jordan' ), 'kazakhstan' => array( 'title' => 'Kazakhstan' ), 'kenya' => array( 'title' => 'Kenya' ), 'kiribati' => array( 'title' => 'Kiribati' ), 'korea-democratic-peoples-republic' => array( 'title' => 'Korea, Democratic People\'s Republic of' ), 'korea-republic' => array( 'title' => 'Korea, Republic of' ), 'kuwait' => array( 'title' => 'Kuwait' ), 'kyrgyzstan' => array( 'title' => 'Kyrgyzstan' ), 'lao-peoples-democratic-republic' => array( 'title' => 'Lao, People\'s Democratic Republic' ), 'latvia' => array( 'title' => 'Latvia' ), 'lebanon' => array( 'title' => 'Lebanon' ), 'lesotho' => array( 'title' => 'Lesotho' ), 'liberia' => array( 'title' => 'Liberia' ), 'libyan-arab-jamahiriya' => array( 'title' => 'Libyan Arab Jamahiriya' ), 'liechtenstein' => array( 'title' => 'Liechtenstein' ), 'lithuania' => array( 'title' => 'Lithuania' ), 'luxembourg' => array( 'title' => 'Luxembourg' ), 'macau' => array( 'title' => 'Macau' ), 'macedonia-former-yugoslav-republic' => array( 'title' => 'Macedonia, The Former Yugoslav Republic of' ), 'madagascar' => array( 'title' => 'Madagascar' ), 'malawi' => array( 'title' => 'Malawi' ), 'malaysia' => array( 'title' => 'Malaysia' ), 'maldives' => array( 'title' => 'Maldives' ), 'mali' => array( 'title' => 'Mali' ), 'malta' => array( 'title' => 'Malta' ), 'marshall-islands' => array( 'title' => 'Marshall Islands' ), 'martinique' => array( 'title' => 'Martinique' ), 'mauritania' => array( 'title' => 'Mauritania' ), 'mauritius' => array( 'title' => 'Mauritius' ), 'mayotte' => array( 'title' => 'Mayotte' ), 'mexico' => array( 'title' => 'Mexico' ), 'micronesia-federated-states' => array( 'title' => 'Micronesia, Federated States of' ), 'moldova-republic' => array( 'title' => 'Moldova, Republic of' ), 'monaco' => array( 'title' => 'Monaco' ), 'mongolia' => array( 'title' => 'Mongolia' ), 'montserrat' => array( 'title' => 'Montserrat' ), 'morocco' => array( 'title' => 'Morocco' ), 'mozambique' => array( 'title' => 'Mozambique' ), 'myanmar' => array( 'title' => 'Myanmar' ), 'namibia' => array( 'title' => 'Namibia' ), 'nauru' => array( 'title' => 'Nauru' ), 'nepal' => array( 'title' => 'Nepal' ), 'netherlands' => array( 'title' => 'Netherlands' ), 'netherlands-antilles' => array( 'title' => 'Netherlands Antilles' ), 'new-caledonia' => array( 'title' => 'New Caledonia' ), 'new-zealand' => array( 'title' => 'New Zealand' ), 'nicaragua' => array( 'title' => 'Nicaragua' ), 'niger' => array( 'title' => 'Niger' ), 'nigeria' => array( 'title' => 'Nigeria' ), 'niue' => array( 'title' => 'Niue' ), 'norfolk-island' => array( 'title' => 'Norfolk Island' ), 'northern-mariana-islands' => array( 'title' => 'Northern Mariana Islands' ), 'norway' => array( 'title' => 'Norway' ), 'oman' => array( 'title' => 'Oman' ), 'pakistan' => array( 'title' => 'Pakistan' ), 'palau' => array( 'title' => 'Palau' ), 'panama' => array( 'title' => 'Panama' ), 'papua-new-guinea' => array( 'title' => 'Papua New Guinea' ), 'paraguay' => array( 'title' => 'Paraguay' ), 'peru' => array( 'title' => 'Peru' ), 'philippines' => array( 'title' => 'Philippines' ), 'pitcairn' => array( 'title' => 'Pitcairn' ), 'poland' => array( 'title' => 'Poland' ), 'portugal' => array( 'title' => 'Portugal' ), 'puerto-rico' => array( 'title' => 'Puerto Rico' ), 'qatar' => array( 'title' => 'Qatar' ), 'reunion' => array( 'title' => 'Reunion' ), 'romania' => array( 'title' => 'Romania' ), 'russian-federation' => array( 'title' => 'Russian Federation' ), 'rwanda' => array( 'title' => 'Rwanda' ), 'saint-kitts-and-nevis' => array( 'title' => 'Saint Kitts and Nevis' ), 'saint-lucia' => array( 'title' => 'Saint Lucia' ), 'saint-vincent-and-grenadines' => array( 'title' => 'Saint Vincent and the Grenadines' ), 'samoa' => array( 'title' => 'Samoa' ), 'san-marino' => array( 'title' => 'San Marino' ), 'sao-tome-and-principe' => array( 'title' => 'Sao Tome and Principe' ), 'saudi-arabia' => array( 'title' => 'Saudi Arabia' ), 'senegal' => array( 'title' => 'Senegal' ), 'seychelles' => array( 'title' => 'Seychelles' ), 'sierra-leone' => array( 'title' => 'Sierra Leone' ), 'singapore' => array( 'title' => 'Singapore' ), 'slovakia-slovak-republic' => array( 'title' => 'Slovakia (Slovak Republic)' ), 'slovenia' => array( 'title' => 'Slovenia' ), 'solomon-islands' => array( 'title' => 'Solomon Islands' ), 'somalia' => array( 'title' => 'Somalia' ), 'south-africa' => array( 'title' => 'South Africa' ), 'south-georgia-and-south-sandwich-islands' => array( 'title' => 'South Georgia and the South Sandwich Islands' ), 'spain' => array( 'title' => 'Spain' ), 'sri-lanka' => array( 'title' => 'Sri Lanka' ), 'st-helena' => array( 'title' => 'St. Helena' ), 'st-pierre-and-miquelon' => array( 'title' => 'St. Pierre and Miquelon' ), 'sudan' => array( 'title' => 'Sudan' ), 'suriname' => array( 'title' => 'Suriname' ), 'svalbard-and-jan-mayen-islands' => array( 'title' => 'Svalbard and Jan Mayen Islands' ), 'swaziland' => array( 'title' => 'Swaziland' ), 'sweden' => array( 'title' => 'Sweden' ), 'switzerland' => array( 'title' => 'Switzerland' ), 'syrian-arab-republic' => array( 'title' => 'Syrian Arab Republic' ), 'taiwan-province-china' => array( 'title' => 'Taiwan, Province of China' ), 'tajikistan' => array( 'title' => 'Tajikistan' ), 'tanzania-united-republic' => array( 'title' => 'Tanzania, United Republic of' ), 'thailand' => array( 'title' => 'Thailand' ), 'togo' => array( 'title' => 'Togo' ), 'tokelau' => array( 'title' => 'Tokelau' ), 'tonga' => array( 'title' => 'Tonga' ), 'trinidad-and-tobago' => array( 'title' => 'Trinidad and Tobago' ), 'tunisia' => array( 'title' => 'Tunisia' ), 'turkey' => array( 'title' => 'Turkey' ), 'turkmenistan' => array( 'title' => 'Turkmenistan' ), 'turks-and-caicos-islands' => array( 'title' => 'Turks and Caicos Islands' ), 'tuvalu' => array( 'title' => 'Tuvalu' ), 'uganda' => array( 'title' => 'Uganda' ), 'ukraine' => array( 'title' => 'Ukraine' ), 'united-arab-emirates' => array( 'title' => 'United Arab Emirates' ), 'united-kingdom' => array( 'title' => 'United Kingdom' ), 'united-states' => array( 'title' => 'United States' ), 'united-states-minor-outlying-islands' => array( 'title' => 'United States Minor Outlying Islands' ), 'uruguay' => array( 'title' => 'Uruguay' ), 'uzbekistan' => array( 'title' => 'Uzbekistan' ), 'vanuatu' => array( 'title' => 'Vanuatu' ), 'venezuela' => array( 'title' => 'Venezuela' ), 'vietnam' => array( 'title' => 'Vietnam' ), 'virgin-islands-british' => array( 'title' => 'Virgin Islands (British)' ), 'virgin-islands-us' => array( 'title' => 'Virgin Islands (U.S.)' ), 'wallis-and-futuna-islands' => array( 'title' => 'Wallis and Futuna Islands' ), 'western-sahara' => array( 'title' => 'Western Sahara' ), 'yemen' => array( 'title' => 'Yemen' ), 'yugoslavia' => array( 'title' => 'Yugoslavia' ), 'zambia' => array( 'title' => 'Zambia' ), 'zimbabwe' => array( 'title' => 'Zimbabwe' ) );

//Languages Array
$langs = array('za' => array( 'lang' => 'za','name' => 'Afrikaans','locale' => 'af','code' => 'af', 'icon' => 'za.png'), 'arab' => array( 'lang' => 'arab','name' => 'العربية','locale' => 'ar','code' => 'ar', 'icon' => 'arab.png'), 'ma' => array( 'lang' => 'ma','name' => 'العربية المغربية','locale' => 'ary','code' => 'ar', 'icon' => 'ma.png'), 'in' => array( 'lang' => 'in','name' => 'తెలుగు','locale' => 'te','code' => 'te', 'icon' => 'in.png'), 'az' => array( 'lang' => 'az','name' => 'گؤنئی آذربایجان','locale' => 'azb','code' => 'az', 'icon' => 'az.png'), 'by' => array( 'lang' => 'by','name' => 'Беларуская мова','locale' => 'bel','code' => 'be', 'icon' => 'by.png'), 'bg' => array( 'lang' => 'bg','name' => 'български','locale' => 'bg_BG','code' => 'bg', 'icon' => 'bg.png'), 'bd' => array( 'lang' => 'bd','name' => 'বাংলা','locale' => 'bn_BD','code' => 'bn', 'icon' => 'bd.png'), 'tibet' => array( 'lang' => 'tibet','name' => 'Tibetan','locale' => 'bo','code' => 'bo', 'icon' => 'tibet.png'), 'ba' => array( 'lang' => 'ba','name' => 'Bosanski','locale' => 'bs_BA','code' => 'bs', 'icon' => 'ba.png'), 'catalonia' => array( 'lang' => 'catalonia','name' => 'Català','locale' => 'ca','code' => 'ca', 'icon' => 'catalonia.png'), 'ph' => array( 'lang' => 'ph','name' => 'Tagalog','locale' => 'tl','code' => 'tl', 'icon' => 'ph.png'), 'kurdistan' => array( 'lang' => 'kurdistan','name' => 'کوردی','locale' => 'ckb','code' => 'ku', 'icon' => 'kurdistan.png'), 'cz' => array( 'lang' => 'cz','name' => 'Čeština','locale' => 'cs_CZ','code' => 'cs', 'icon' => 'cz.png'), 'wales' => array( 'lang' => 'wales','name' => 'Cymraeg','locale' => 'cy','code' => 'cy', 'icon' => 'wales.png'), 'dk' => array( 'lang' => 'dk','name' => 'Dansk','locale' => 'da_DK','code' => 'da', 'icon' => 'dk.png'), 'ch' => array( 'lang' => 'ch','name' => 'Deutsch','locale' => 'de_CH_informal','code' => 'de', 'icon' => 'ch.png'), 'de' => array( 'lang' => 'de','name' => 'Deutsch','locale' => 'de_DE_formal','code' => 'de', 'icon' => 'de.png'), 'bt' => array( 'lang' => 'bt','name' => 'Dzongkha','locale' => 'dzo','code' => 'dz', 'icon' => 'bt.png'), 'gr' => array( 'lang' => 'gr','name' => 'Ελληνικά','locale' => 'el','code' => 'el', 'icon' => 'gr.png'), 'au' => array( 'lang' => 'au','name' => 'English','locale' => 'en_AU','code' => 'en', 'icon' => 'au.png'), 'ca' => array( 'lang' => 'ca','name' => 'English','locale' => 'en_CA','code' => 'en', 'icon' => 'ca.png'), 'gb' => array( 'lang' => 'gb','name' => 'English','locale' => 'en_GB','code' => 'en', 'icon' => 'gb.png'), 'nz' => array( 'lang' => 'nz','name' => 'English','locale' => 'en_NZ','code' => 'en', 'icon' => 'nz.png'), 'us' => array( 'lang' => 'us','name' => 'English','locale' => 'en_US','code' => 'en', 'icon' => 'us.png'), 'za' => array( 'lang' => 'za','name' => 'English','locale' => 'en_ZA','code' => 'en', 'icon' => 'za.png'), 'esperanto' => array( 'lang' => 'esperanto','name' => 'Esperanto','locale' => 'eo','code' => 'eo', 'icon' => 'esperanto.png'), 'ar' => array( 'lang' => 'ar','name' => 'Español','locale' => 'es_AR','code' => 'es', 'icon' => 'ar.png'), 'cl' => array( 'lang' => 'cl','name' => 'Español','locale' => 'es_CL','code' => 'es', 'icon' => 'cl.png'), 'co' => array( 'lang' => 'co','name' => 'Español','locale' => 'es_CO','code' => 'es', 'icon' => 'co.png'), 'cr' => array( 'lang' => 'cr','name' => 'Español','locale' => 'es_CR','code' => 'es', 'icon' => 'cr.png'), 'es' => array( 'lang' => 'es','name' => 'Español','locale' => 'es_ES','code' => 'es', 'icon' => 'es.png'), 'gt' => array( 'lang' => 'gt','name' => 'Español','locale' => 'es_GT','code' => 'es', 'icon' => 'gt.png'), 'mx' => array( 'lang' => 'mx','name' => 'Español','locale' => 'es_MX','code' => 'es', 'icon' => 'mx.png'), 'pe' => array( 'lang' => 'pe','name' => 'Español','locale' => 'es_PE','code' => 'es', 'icon' => 'pe.png'), 've' => array( 'lang' => 've','name' => 'Español','locale' => 'es_VE','code' => 'es', 'icon' => 've.png'), 'ee' => array( 'lang' => 'ee','name' => 'Eesti','locale' => 'et','code' => 'et', 'icon' => 'ee.png'), 'basque' => array( 'lang' => 'basque','name' => 'Euskara','locale' => 'eu','code' => 'eu', 'icon' => 'basque.png'), 'ir' => array( 'lang' => 'ir','name' => 'فارسی','locale' => 'fa_IR','code' => 'fa', 'icon' => 'ir.png'), 'fi' => array( 'lang' => 'fi','name' => 'Suomi','locale' => 'fi','code' => 'fi', 'icon' => 'fi.png'), 'be' => array( 'lang' => 'be','name' => 'Nederlands','locale' => 'nl_BE','code' => 'nl', 'icon' => 'be.png'), 'quebec' => array( 'lang' => 'quebec','name' => 'Français','locale' => 'fr_CA','code' => 'fr', 'icon' => 'quebec.png'), 'fr' => array( 'lang' => 'fr','name' => 'Français','locale' => 'fr_FR','code' => 'fr', 'icon' => 'fr.png'), 'it' => array( 'lang' => 'it','name' => 'Italiano','locale' => 'it_IT','code' => 'it', 'icon' => 'it.png'), 'scotland' => array( 'lang' => 'scotland','name' => 'Gàidhlig','locale' => 'gd','code' => 'gd', 'icon' => 'scotland.png'), 'galicia' => array( 'lang' => 'galicia','name' => 'Galego','locale' => 'gl_ES','code' => 'gl', 'icon' => 'galicia.png'), 'af' => array( 'lang' => 'af','name' => 'پښتو','locale' => 'ps','code' => 'ps', 'icon' => 'af.png'), 'il' => array( 'lang' => 'il','name' => 'עברית','locale' => 'he_IL','code' => 'he', 'icon' => 'il.png'), 'hr' => array( 'lang' => 'hr','name' => 'Hrvatski','locale' => 'hr','code' => 'hr', 'icon' => 'hr.png'), 'hu' => array( 'lang' => 'hu','name' => 'Magyar','locale' => 'hu_HU','code' => 'hu', 'icon' => 'hu.png'), 'am' => array( 'lang' => 'am','name' => 'Հայերեն','locale' => 'hy','code' => 'hy', 'icon' => 'am.png'), 'id' => array( 'lang' => 'id','name' => 'Basa Jawa','locale' => 'jv_ID','code' => 'jv', 'icon' => 'id.png'), 'is' => array( 'lang' => 'is','name' => 'Íslenska','locale' => 'is_IS','code' => 'is', 'icon' => 'is.png'), 'jp' => array( 'lang' => 'jp','name' => '日本語','locale' => 'ja','code' => 'ja', 'icon' => 'jp.png'), 'ge' => array( 'lang' => 'ge','name' => 'ქართული','locale' => 'ka_GE','code' => 'ka', 'icon' => 'ge.png'), 'dz' => array( 'lang' => 'dz','name' => 'Taqbaylit','locale' => 'kab','code' => 'kab', 'icon' => 'dz.png'), 'kz' => array( 'lang' => 'kz','name' => 'Қазақ тілі','locale' => 'kk','code' => 'kk', 'icon' => 'kz.png'), 'kh' => array( 'lang' => 'kh','name' => 'ភាសាខ្មែរ','locale' => 'km','code' => 'km', 'icon' => 'kh.png'), 'kr' => array( 'lang' => 'kr','name' => '한국어','locale' => 'ko_KR','code' => 'ko', 'icon' => 'kr.png'), 'la' => array( 'lang' => 'la','name' => 'ພາສາລາວ','locale' => 'lo','code' => 'lo', 'icon' => 'la.png'), 'lt' => array( 'lang' => 'lt','name' => 'Lietuviškai','locale' => 'lt_LT','code' => 'lt', 'icon' => 'lt.png'), 'lv' => array( 'lang' => 'lv','name' => 'Latviešu valoda','locale' => 'lv','code' => 'lv', 'icon' => 'lv.png'), 'mk' => array( 'lang' => 'mk','name' => 'македонски јазик','locale' => 'mk_MK','code' => 'mk', 'icon' => 'mk.png'), 'mn' => array( 'lang' => 'mn','name' => 'Монгол хэл','locale' => 'mn','code' => 'mn', 'icon' => 'mn.png'), 'my' => array( 'lang' => 'my','name' => 'Bahasa Melayu','locale' => 'ms_MY','code' => 'ms', 'icon' => 'my.png'), 'mm' => array( 'lang' => 'mm','name' => 'Ruáinga','locale' => 'rhg','code' => 'rhg', 'icon' => 'mm.png'), 'no' => array( 'lang' => 'no','name' => 'Norsk Nynorsk','locale' => 'nn_NO','code' => 'nn', 'icon' => 'no.png'), 'np' => array( 'lang' => 'np','name' => 'नेपाली','locale' => 'ne_NP','code' => 'ne', 'icon' => 'np.png'), 'nl' => array( 'lang' => 'nl','name' => 'Nederlands','locale' => 'nl_NL_formal','code' => 'nl', 'icon' => 'nl.png'), 'occitania' => array( 'lang' => 'occitania','name' => 'Occitan','locale' => 'oci','code' => 'oc', 'icon' => 'occitania.png'), 'pl' => array( 'lang' => 'pl','name' => 'Ślōnskŏ gŏdka','locale' => 'szl','code' => 'szl', 'icon' => 'pl.png'), 'br' => array( 'lang' => 'br','name' => 'Português','locale' => 'pt_BR','code' => 'pt', 'icon' => 'br.png'), 'pt' => array( 'lang' => 'pt','name' => 'Português','locale' => 'pt_PT_ao90','code' => 'pt', 'icon' => 'pt.png'), 'ro' => array( 'lang' => 'ro','name' => 'Română','locale' => 'ro_RO','code' => 'ro', 'icon' => 'ro.png'), 'ru' => array( 'lang' => 'ru','name' => 'Татар теле','locale' => 'tt_RU','code' => 'tt', 'icon' => 'ru.png'), 'lk' => array( 'lang' => 'lk','name' => 'සිංහල','locale' => 'si_LK','code' => 'si', 'icon' => 'lk.png'), 'sk' => array( 'lang' => 'sk','name' => 'Slovenčina','locale' => 'sk_SK','code' => 'sk', 'icon' => 'sk.png'), 'si' => array( 'lang' => 'si','name' => 'Slovenščina','locale' => 'sl_SI','code' => 'sl', 'icon' => 'si.png'), 'al' => array( 'lang' => 'al','name' => 'Shqip','locale' => 'sq','code' => 'sq', 'icon' => 'al.png'), 'rs' => array( 'lang' => 'rs','name' => 'Српски језик','locale' => 'sr_RS','code' => 'sr', 'icon' => 'rs.png'), 'se' => array( 'lang' => 'se','name' => 'Svenska','locale' => 'sv_SE','code' => 'sv', 'icon' => 'se.png'), 'pf' => array( 'lang' => 'pf','name' => 'Reo Tahiti','locale' => 'tah','code' => 'ty', 'icon' => 'pf.png'), 'th' => array( 'lang' => 'th','name' => 'ไทย','locale' => 'th','code' => 'th', 'icon' => 'th.png'), 'tr' => array( 'lang' => 'tr','name' => 'Türkçe','locale' => 'tr_TR','code' => 'tr', 'icon' => 'tr.png'), 'cn' => array( 'lang' => 'cn','name' => '中文 (中国)','locale' => 'zh_CN','code' => 'zh', 'icon' => 'cn.png'), 'ua' => array( 'lang' => 'ua','name' => 'Українська','locale' => 'uk','code' => 'uk', 'icon' => 'ua.png'), 'pk' => array( 'lang' => 'pk','name' => 'اردو','locale' => 'ur','code' => 'ur', 'icon' => 'pk.png'), 'uz' => array( 'lang' => 'uz','name' => 'Oʻzbek','locale' => 'uz_UZ','code' => 'uz', 'icon' => 'uz.png'), 'vn' => array( 'lang' => 'vn','name' => 'Tiếng Việt','locale' => 'vi','code' => 'vi', 'icon' => 'vn.png'), 'hk' => array( 'lang' => 'hk','name' => '中文 (香港)','locale' => 'zh_HK','code' => 'zh', 'icon' => 'hk.png'), 'tw' => array( 'lang' => 'tw','name' => '中文 (台灣)','locale' => 'zh_TW','code' => 'zh', 'icon' => 'tw.png') );

//Locales Array
$locales = array('africa-abidjan' => array('key' => 'Africa/Abidjan', 'value' => 'Africa/Abidjan (GMT+0:00)' ), 'africa-accra' => array('key' => 'Africa/Accra', 'value' => 'Africa/Accra (GMT+0:00)' ), 'africa-addis_ababa' => array('key' => 'Africa/Addis_Ababa', 'value' => 'Africa/Addis Ababa (GMT+3:00)' ), 'africa-algiers' => array('key' => 'Africa/Algiers', 'value' => 'Africa/Algiers (GMT+1:00)' ), 'africa-asmara' => array('key' => 'Africa/Asmara', 'value' => 'Africa/Asmara (GMT+3:00)' ), 'africa-bamako' => array('key' => 'Africa/Bamako', 'value' => 'Africa/Bamako (GMT+0:00)' ), 'africa-bangui' => array('key' => 'Africa/Bangui', 'value' => 'Africa/Bangui (GMT+1:00)' ), 'africa-banjul' => array('key' => 'Africa/Banjul', 'value' => 'Africa/Banjul (GMT+0:00)' ), 'africa-bissau' => array('key' => 'Africa/Bissau', 'value' => 'Africa/Bissau (GMT+0:00)' ), 'africa-blantyre' => array('key' => 'Africa/Blantyre', 'value' => 'Africa/Blantyre (GMT+2:00)' ), 'africa-brazzaville' => array('key' => 'Africa/Brazzaville', 'value' => 'Africa/Brazzaville (GMT+1:00)' ), 'africa-bujumbura' => array('key' => 'Africa/Bujumbura', 'value' => 'Africa/Bujumbura (GMT+2:00)' ), 'africa-cairo' => array('key' => 'Africa/Cairo', 'value' => 'Africa/Cairo (GMT+2:00)' ), 'africa-casablanca' => array('key' => 'Africa/Casablanca', 'value' => 'Africa/Casablanca (GMT+1:00)' ), 'africa-ceuta' => array('key' => 'Africa/Ceuta', 'value' => 'Africa/Ceuta (GMT+1:00)' ), 'africa-conakry' => array('key' => 'Africa/Conakry', 'value' => 'Africa/Conakry (GMT+0:00)' ), 'africa-dakar' => array('key' => 'Africa/Dakar', 'value' => 'Africa/Dakar (GMT+0:00)' ), 'africa-dar_es_salaam' => array('key' => 'Africa/Dar_es_Salaam', 'value' => 'Africa/Dar es Salaam (GMT+3:00)' ), 'africa-djibouti' => array('key' => 'Africa/Djibouti', 'value' => 'Africa/Djibouti (GMT+3:00)' ), 'africa-douala' => array('key' => 'Africa/Douala', 'value' => 'Africa/Douala (GMT+1:00)' ), 'africa-el_aaiun' => array('key' => 'Africa/El_Aaiun', 'value' => 'Africa/El Aaiun (GMT+1:00)' ), 'africa-freetown' => array('key' => 'Africa/Freetown', 'value' => 'Africa/Freetown (GMT+0:00)' ), 'africa-gaborone' => array('key' => 'Africa/Gaborone', 'value' => 'Africa/Gaborone (GMT+2:00)' ), 'africa-harare' => array('key' => 'Africa/Harare', 'value' => 'Africa/Harare (GMT+2:00)' ), 'africa-johannesburg' => array('key' => 'Africa/Johannesburg', 'value' => 'Africa/Johannesburg (GMT+2:00)' ), 'africa-juba' => array('key' => 'Africa/Juba', 'value' => 'Africa/Juba (GMT+2:00)' ), 'africa-kampala' => array('key' => 'Africa/Kampala', 'value' => 'Africa/Kampala (GMT+3:00)' ), 'africa-khartoum' => array('key' => 'Africa/Khartoum', 'value' => 'Africa/Khartoum (GMT+2:00)' ), 'africa-kigali' => array('key' => 'Africa/Kigali', 'value' => 'Africa/Kigali (GMT+2:00)' ), 'africa-kinshasa' => array('key' => 'Africa/Kinshasa', 'value' => 'Africa/Kinshasa (GMT+1:00)' ), 'africa-lagos' => array('key' => 'Africa/Lagos', 'value' => 'Africa/Lagos (GMT+1:00)' ), 'africa-libreville' => array('key' => 'Africa/Libreville', 'value' => 'Africa/Libreville (GMT+1:00)' ), 'africa-lome' => array('key' => 'Africa/Lome', 'value' => 'Africa/Lome (GMT+0:00)' ), 'africa-luanda' => array('key' => 'Africa/Luanda', 'value' => 'Africa/Luanda (GMT+1:00)' ), 'africa-lubumbashi' => array('key' => 'Africa/Lubumbashi', 'value' => 'Africa/Lubumbashi (GMT+2:00)' ), 'africa-lusaka' => array('key' => 'Africa/Lusaka', 'value' => 'Africa/Lusaka (GMT+2:00)' ), 'africa-malabo' => array('key' => 'Africa/Malabo', 'value' => 'Africa/Malabo (GMT+1:00)' ), 'africa-maputo' => array('key' => 'Africa/Maputo', 'value' => 'Africa/Maputo (GMT+2:00)' ), 'africa-maseru' => array('key' => 'Africa/Maseru', 'value' => 'Africa/Maseru (GMT+2:00)' ), 'africa-mbabane' => array('key' => 'Africa/Mbabane', 'value' => 'Africa/Mbabane (GMT+2:00)' ), 'africa-mogadishu' => array('key' => 'Africa/Mogadishu', 'value' => 'Africa/Mogadishu (GMT+3:00)' ), 'africa-monrovia' => array('key' => 'Africa/Monrovia', 'value' => 'Africa/Monrovia (GMT+0:00)' ), 'africa-nairobi' => array('key' => 'Africa/Nairobi', 'value' => 'Africa/Nairobi (GMT+3:00)' ), 'africa-ndjamena' => array('key' => 'Africa/Ndjamena', 'value' => 'Africa/Ndjamena (GMT+1:00)' ), 'africa-niamey' => array('key' => 'Africa/Niamey', 'value' => 'Africa/Niamey (GMT+1:00)' ), 'africa-nouakchott' => array('key' => 'Africa/Nouakchott', 'value' => 'Africa/Nouakchott (GMT+0:00)' ), 'africa-ouagadougou' => array('key' => 'Africa/Ouagadougou', 'value' => 'Africa/Ouagadougou (GMT+0:00)' ), 'africa-porto-novo' => array('key' => 'Africa/Porto-Novo', 'value' => 'Africa/Porto-Novo (GMT+1:00)' ), 'africa-sao_tome' => array('key' => 'Africa/Sao_Tome', 'value' => 'Africa/Sao Tome (GMT+0:00)' ), 'africa-tripoli' => array('key' => 'Africa/Tripoli', 'value' => 'Africa/Tripoli (GMT+2:00)' ), 'africa-tunis' => array('key' => 'Africa/Tunis', 'value' => 'Africa/Tunis (GMT+1:00)' ), 'africa-windhoek' => array('key' => 'Africa/Windhoek', 'value' => 'Africa/Windhoek (GMT+2:00)' ), 'america-adak' => array('key' => 'America/Adak', 'value' => 'America/Adak (GMT-10:00)' ), 'america-anchorage' => array('key' => 'America/Anchorage', 'value' => 'America/Anchorage (GMT-9:00)' ), 'america-anguilla' => array('key' => 'America/Anguilla', 'value' => 'America/Anguilla (GMT-4:00)' ), 'america-antigua' => array('key' => 'America/Antigua', 'value' => 'America/Antigua (GMT-4:00)' ), 'america-araguaina' => array('key' => 'America/Araguaina', 'value' => 'America/Araguaina (GMT-3:00)' ), 'america-argentina-buenos_aires' => array('key' => 'America/Argentina/Buenos_Aires', 'value' => 'America/Argentina/Buenos Aires (GMT-3:00)' ), 'america-argentina-catamarca' => array('key' => 'America/Argentina/Catamarca', 'value' => 'America/Argentina/Catamarca (GMT-3:00)' ), 'america-argentina-cordoba' => array('key' => 'America/Argentina/Cordoba', 'value' => 'America/Argentina/Cordoba (GMT-3:00)' ), 'america-argentina-jujuy' => array('key' => 'America/Argentina/Jujuy', 'value' => 'America/Argentina/Jujuy (GMT-3:00)' ), 'america-argentina-la_rioja' => array('key' => 'America/Argentina/La_Rioja', 'value' => 'America/Argentina/La Rioja (GMT-3:00)' ), 'america-argentina-mendoza' => array('key' => 'America/Argentina/Mendoza', 'value' => 'America/Argentina/Mendoza (GMT-3:00)' ), 'america-argentina-rio_gallegos' => array('key' => 'America/Argentina/Rio_Gallegos', 'value' => 'America/Argentina/Rio Gallegos (GMT-3:00)' ), 'america-argentina-salta' => array('key' => 'America/Argentina/Salta', 'value' => 'America/Argentina/Salta (GMT-3:00)' ), 'america-argentina-san_juan' => array('key' => 'America/Argentina/San_Juan', 'value' => 'America/Argentina/San Juan (GMT-3:00)' ), 'america-argentina-san_luis' => array('key' => 'America/Argentina/San_Luis', 'value' => 'America/Argentina/San Luis (GMT-3:00)' ), 'america-argentina-tucuman' => array('key' => 'America/Argentina/Tucuman', 'value' => 'America/Argentina/Tucuman (GMT-3:00)' ), 'america-argentina-ushuaia' => array('key' => 'America/Argentina/Ushuaia', 'value' => 'America/Argentina/Ushuaia (GMT-3:00)' ), 'america-aruba' => array('key' => 'America/Aruba', 'value' => 'America/Aruba (GMT-4:00)' ), 'america-asuncion' => array('key' => 'America/Asuncion', 'value' => 'America/Asuncion (GMT-3:00)' ), 'america-atikokan' => array('key' => 'America/Atikokan', 'value' => 'America/Atikokan (GMT-5:00)' ), 'america-bahia' => array('key' => 'America/Bahia', 'value' => 'America/Bahia (GMT-3:00)' ), 'america-bahia_banderas' => array('key' => 'America/Bahia_Banderas', 'value' => 'America/Bahia Banderas (GMT-6:00)' ), 'america-barbados' => array('key' => 'America/Barbados', 'value' => 'America/Barbados (GMT-4:00)' ), 'america-belem' => array('key' => 'America/Belem', 'value' => 'America/Belem (GMT-3:00)' ), 'america-belize' => array('key' => 'America/Belize', 'value' => 'America/Belize (GMT-6:00)' ), 'america-blanc-sablon' => array('key' => 'America/Blanc-Sablon', 'value' => 'America/Blanc-Sablon (GMT-4:00)' ), 'america-boa_vista' => array('key' => 'America/Boa_Vista', 'value' => 'America/Boa Vista (GMT-4:00)' ), 'america-bogota' => array('key' => 'America/Bogota', 'value' => 'America/Bogota (GMT-5:00)' ), 'america-boise' => array('key' => 'America/Boise', 'value' => 'America/Boise (GMT-7:00)' ), 'america-cambridge_bay' => array('key' => 'America/Cambridge_Bay', 'value' => 'America/Cambridge Bay (GMT-7:00)' ), 'america-campo_grande' => array('key' => 'America/Campo_Grande', 'value' => 'America/Campo Grande (GMT-4:00)' ), 'america-cancun' => array('key' => 'America/Cancun', 'value' => 'America/Cancun (GMT-5:00)' ), 'america-caracas' => array('key' => 'America/Caracas', 'value' => 'America/Caracas (GMT-4:00)' ), 'america-cayenne' => array('key' => 'America/Cayenne', 'value' => 'America/Cayenne (GMT-3:00)' ), 'america-cayman' => array('key' => 'America/Cayman', 'value' => 'America/Cayman (GMT-5:00)' ), 'america-chicago' => array('key' => 'America/Chicago', 'value' => 'America/Chicago (GMT-6:00)' ), 'america-chihuahua' => array('key' => 'America/Chihuahua', 'value' => 'America/Chihuahua (GMT-7:00)' ), 'america-costa_rica' => array('key' => 'America/Costa_Rica', 'value' => 'America/Costa Rica (GMT-6:00)' ), 'america-creston' => array('key' => 'America/Creston', 'value' => 'America/Creston (GMT-7:00)' ), 'america-cuiaba' => array('key' => 'America/Cuiaba', 'value' => 'America/Cuiaba (GMT-4:00)' ), 'america-curacao' => array('key' => 'America/Curacao', 'value' => 'America/Curacao (GMT-4:00)' ), 'america-danmarkshavn' => array('key' => 'America/Danmarkshavn', 'value' => 'America/Danmarkshavn (GMT+0:00)' ), 'america-dawson' => array('key' => 'America/Dawson', 'value' => 'America/Dawson (GMT-7:00)' ), 'america-dawson_creek' => array('key' => 'America/Dawson_Creek', 'value' => 'America/Dawson Creek (GMT-7:00)' ), 'america-denver' => array('key' => 'America/Denver', 'value' => 'America/Denver (GMT-7:00)' ), 'america-detroit' => array('key' => 'America/Detroit', 'value' => 'America/Detroit (GMT-5:00)' ), 'america-dominica' => array('key' => 'America/Dominica', 'value' => 'America/Dominica (GMT-4:00)' ), 'america-edmonton' => array('key' => 'America/Edmonton', 'value' => 'America/Edmonton (GMT-7:00)' ), 'america-eirunepe' => array('key' => 'America/Eirunepe', 'value' => 'America/Eirunepe (GMT-5:00)' ), 'america-el_salvador' => array('key' => 'America/El_Salvador', 'value' => 'America/El Salvador (GMT-6:00)' ), 'america-fort_nelson' => array('key' => 'America/Fort_Nelson', 'value' => 'America/Fort Nelson (GMT-7:00)' ), 'america-fortaleza' => array('key' => 'America/Fortaleza', 'value' => 'America/Fortaleza (GMT-3:00)' ), 'america-glace_bay' => array('key' => 'America/Glace_Bay', 'value' => 'America/Glace Bay (GMT-4:00)' ), 'america-goose_bay' => array('key' => 'America/Goose_Bay', 'value' => 'America/Goose Bay (GMT-4:00)' ), 'america-grand_turk' => array('key' => 'America/Grand_Turk', 'value' => 'America/Grand Turk (GMT-5:00)' ), 'america-grenada' => array('key' => 'America/Grenada', 'value' => 'America/Grenada (GMT-4:00)' ), 'america-guadeloupe' => array('key' => 'America/Guadeloupe', 'value' => 'America/Guadeloupe (GMT-4:00)' ), 'america-guatemala' => array('key' => 'America/Guatemala', 'value' => 'America/Guatemala (GMT-6:00)' ), 'america-guayaquil' => array('key' => 'America/Guayaquil', 'value' => 'America/Guayaquil (GMT-5:00)' ), 'america-guyana' => array('key' => 'America/Guyana', 'value' => 'America/Guyana (GMT-4:00)' ), 'america-halifax' => array('key' => 'America/Halifax', 'value' => 'America/Halifax (GMT-4:00)' ), 'america-havana' => array('key' => 'America/Havana', 'value' => 'America/Havana (GMT-5:00)' ), 'america-hermosillo' => array('key' => 'America/Hermosillo', 'value' => 'America/Hermosillo (GMT-7:00)' ), 'america-indiana-indianapolis' => array('key' => 'America/Indiana/Indianapolis', 'value' => 'America/Indiana/Indianapolis (GMT-5:00)' ), 'america-indiana-knox' => array('key' => 'America/Indiana/Knox', 'value' => 'America/Indiana/Knox (GMT-6:00)' ), 'america-indiana-marengo' => array('key' => 'America/Indiana/Marengo', 'value' => 'America/Indiana/Marengo (GMT-5:00)' ), 'america-indiana-petersburg' => array('key' => 'America/Indiana/Petersburg', 'value' => 'America/Indiana/Petersburg (GMT-5:00)' ), 'america-indiana-tell_city' => array('key' => 'America/Indiana/Tell_City', 'value' => 'America/Indiana/Tell City (GMT-6:00)' ), 'america-indiana-vevay' => array('key' => 'America/Indiana/Vevay', 'value' => 'America/Indiana/Vevay (GMT-5:00)' ), 'america-indiana-vincennes' => array('key' => 'America/Indiana/Vincennes', 'value' => 'America/Indiana/Vincennes (GMT-5:00)' ), 'america-indiana-winamac' => array('key' => 'America/Indiana/Winamac', 'value' => 'America/Indiana/Winamac (GMT-5:00)' ), 'america-inuvik' => array('key' => 'America/Inuvik', 'value' => 'America/Inuvik (GMT-7:00)' ), 'america-iqaluit' => array('key' => 'America/Iqaluit', 'value' => 'America/Iqaluit (GMT-5:00)' ), 'america-jamaica' => array('key' => 'America/Jamaica', 'value' => 'America/Jamaica (GMT-5:00)' ), 'america-juneau' => array('key' => 'America/Juneau', 'value' => 'America/Juneau (GMT-9:00)' ), 'america-kentucky-louisville' => array('key' => 'America/Kentucky/Louisville', 'value' => 'America/Kentucky/Louisville (GMT-5:00)' ), 'america-kentucky-monticello' => array('key' => 'America/Kentucky/Monticello', 'value' => 'America/Kentucky/Monticello (GMT-5:00)' ), 'america-kralendijk' => array('key' => 'America/Kralendijk', 'value' => 'America/Kralendijk (GMT-4:00)' ), 'america-la_paz' => array('key' => 'America/La_Paz', 'value' => 'America/La Paz (GMT-4:00)' ), 'america-lima' => array('key' => 'America/Lima', 'value' => 'America/Lima (GMT-5:00)' ), 'america-los_angeles' => array('key' => 'America/Los_Angeles', 'value' => 'America/Los Angeles (GMT-8:00)' ), 'america-lower_princes' => array('key' => 'America/Lower_Princes', 'value' => 'America/Lower Princes (GMT-4:00)' ), 'america-maceio' => array('key' => 'America/Maceio', 'value' => 'America/Maceio (GMT-3:00)' ), 'america-managua' => array('key' => 'America/Managua', 'value' => 'America/Managua (GMT-6:00)' ), 'america-manaus' => array('key' => 'America/Manaus', 'value' => 'America/Manaus (GMT-4:00)' ), 'america-marigot' => array('key' => 'America/Marigot', 'value' => 'America/Marigot (GMT-4:00)' ), 'america-martinique' => array('key' => 'America/Martinique', 'value' => 'America/Martinique (GMT-4:00)' ), 'america-matamoros' => array('key' => 'America/Matamoros', 'value' => 'America/Matamoros (GMT-6:00)' ), 'america-mazatlan' => array('key' => 'America/Mazatlan', 'value' => 'America/Mazatlan (GMT-7:00)' ), 'america-menominee' => array('key' => 'America/Menominee', 'value' => 'America/Menominee (GMT-6:00)' ), 'america-merida' => array('key' => 'America/Merida', 'value' => 'America/Merida (GMT-6:00)' ), 'america-metlakatla' => array('key' => 'America/Metlakatla', 'value' => 'America/Metlakatla (GMT-9:00)' ), 'america-mexico_city' => array('key' => 'America/Mexico_City', 'value' => 'America/Mexico City (GMT-6:00)' ), 'america-miquelon' => array('key' => 'America/Miquelon', 'value' => 'America/Miquelon (GMT-3:00)' ), 'america-moncton' => array('key' => 'America/Moncton', 'value' => 'America/Moncton (GMT-4:00)' ), 'america-monterrey' => array('key' => 'America/Monterrey', 'value' => 'America/Monterrey (GMT-6:00)' ), 'america-montevideo' => array('key' => 'America/Montevideo', 'value' => 'America/Montevideo (GMT-3:00)' ), 'america-montserrat' => array('key' => 'America/Montserrat', 'value' => 'America/Montserrat (GMT-4:00)' ), 'america-nassau' => array('key' => 'America/Nassau', 'value' => 'America/Nassau (GMT-5:00)' ), 'america-new_york' => array('key' => 'America/New_York', 'value' => 'America/New York (GMT-5:00)' ), 'america-nipigon' => array('key' => 'America/Nipigon', 'value' => 'America/Nipigon (GMT-5:00)' ), 'america-nome' => array('key' => 'America/Nome', 'value' => 'America/Nome (GMT-9:00)' ), 'america-noronha' => array('key' => 'America/Noronha', 'value' => 'America/Noronha (GMT-2:00)' ), 'america-north_dakota-beulah' => array('key' => 'America/North_Dakota/Beulah', 'value' => 'America/North Dakota/Beulah (GMT-6:00)' ), 'america-north_dakota-center' => array('key' => 'America/North_Dakota/Center', 'value' => 'America/North Dakota/Center (GMT-6:00)' ), 'america-north_dakota-new_salem' => array('key' => 'America/North_Dakota/New_Salem', 'value' => 'America/North Dakota/New Salem (GMT-6:00)' ), 'america-nuuk' => array('key' => 'America/Nuuk', 'value' => 'America/Nuuk (GMT-3:00)' ), 'america-ojinaga' => array('key' => 'America/Ojinaga', 'value' => 'America/Ojinaga (GMT-7:00)' ), 'america-panama' => array('key' => 'America/Panama', 'value' => 'America/Panama (GMT-5:00)' ), 'america-pangnirtung' => array('key' => 'America/Pangnirtung', 'value' => 'America/Pangnirtung (GMT-5:00)' ), 'america-paramaribo' => array('key' => 'America/Paramaribo', 'value' => 'America/Paramaribo (GMT-3:00)' ), 'america-phoenix' => array('key' => 'America/Phoenix', 'value' => 'America/Phoenix (GMT-7:00)' ), 'america-port-au-prince' => array('key' => 'America/Port-au-Prince', 'value' => 'America/Port-au-Prince (GMT-5:00)' ), 'america-port_of_spain' => array('key' => 'America/Port_of_Spain', 'value' => 'America/Port of Spain (GMT-4:00)' ), 'america-porto_velho' => array('key' => 'America/Porto_Velho', 'value' => 'America/Porto Velho (GMT-4:00)' ), 'america-puerto_rico' => array('key' => 'America/Puerto_Rico', 'value' => 'America/Puerto Rico (GMT-4:00)' ), 'america-punta_arenas' => array('key' => 'America/Punta_Arenas', 'value' => 'America/Punta Arenas (GMT-3:00)' ), 'america-rainy_river' => array('key' => 'America/Rainy_River', 'value' => 'America/Rainy River (GMT-6:00)' ), 'america-rankin_inlet' => array('key' => 'America/Rankin_Inlet', 'value' => 'America/Rankin Inlet (GMT-6:00)' ), 'america-recife' => array('key' => 'America/Recife', 'value' => 'America/Recife (GMT-3:00)' ), 'america-regina' => array('key' => 'America/Regina', 'value' => 'America/Regina (GMT-6:00)' ), 'america-resolute' => array('key' => 'America/Resolute', 'value' => 'America/Resolute (GMT-6:00)' ), 'america-rio_branco' => array('key' => 'America/Rio_Branco', 'value' => 'America/Rio Branco (GMT-5:00)' ), 'america-santarem' => array('key' => 'America/Santarem', 'value' => 'America/Santarem (GMT-3:00)' ), 'america-santiago' => array('key' => 'America/Santiago', 'value' => 'America/Santiago (GMT-3:00)' ), 'america-santo_domingo' => array('key' => 'America/Santo_Domingo', 'value' => 'America/Santo Domingo (GMT-4:00)' ), 'america-sao_paulo' => array('key' => 'America/Sao_Paulo', 'value' => 'America/Sao Paulo (GMT-3:00)' ), 'america-scoresbysund' => array('key' => 'America/Scoresbysund', 'value' => 'America/Scoresbysund (GMT-1:00)' ), 'america-sitka' => array('key' => 'America/Sitka', 'value' => 'America/Sitka (GMT-9:00)' ), 'america-st_barthelemy' => array('key' => 'America/St_Barthelemy', 'value' => 'America/St Barthelemy (GMT-4:00)' ), 'america-st_johns' => array('key' => 'America/St_Johns', 'value' => 'America/St Johns (GMT-4:30)' ), 'america-st_kitts' => array('key' => 'America/St_Kitts', 'value' => 'America/St Kitts (GMT-4:00)' ), 'america-st_lucia' => array('key' => 'America/St_Lucia', 'value' => 'America/St Lucia (GMT-4:00)' ), 'america-st_thomas' => array('key' => 'America/St_Thomas', 'value' => 'America/St Thomas (GMT-4:00)' ), 'america-st_vincent' => array('key' => 'America/St_Vincent', 'value' => 'America/St Vincent (GMT-4:00)' ), 'america-swift_current' => array('key' => 'America/Swift_Current', 'value' => 'America/Swift Current (GMT-6:00)' ), 'america-tegucigalpa' => array('key' => 'America/Tegucigalpa', 'value' => 'America/Tegucigalpa (GMT-6:00)' ), 'america-thule' => array('key' => 'America/Thule', 'value' => 'America/Thule (GMT-4:00)' ), 'america-thunder_bay' => array('key' => 'America/Thunder_Bay', 'value' => 'America/Thunder Bay (GMT-5:00)' ), 'america-tijuana' => array('key' => 'America/Tijuana', 'value' => 'America/Tijuana (GMT-8:00)' ), 'america-toronto' => array('key' => 'America/Toronto', 'value' => 'America/Toronto (GMT-5:00)' ), 'america-tortola' => array('key' => 'America/Tortola', 'value' => 'America/Tortola (GMT-4:00)' ), 'america-vancouver' => array('key' => 'America/Vancouver', 'value' => 'America/Vancouver (GMT-8:00)' ), 'america-whitehorse' => array('key' => 'America/Whitehorse', 'value' => 'America/Whitehorse (GMT-7:00)' ), 'america-winnipeg' => array('key' => 'America/Winnipeg', 'value' => 'America/Winnipeg (GMT-6:00)' ), 'america-yakutat' => array('key' => 'America/Yakutat', 'value' => 'America/Yakutat (GMT-9:00)' ), 'america-yellowknife' => array('key' => 'America/Yellowknife', 'value' => 'America/Yellowknife (GMT-7:00)' ), 'antarctica-casey' => array('key' => 'Antarctica/Casey', 'value' => 'Antarctica/Casey (GMT+11:00)' ), 'antarctica-davis' => array('key' => 'Antarctica/Davis', 'value' => 'Antarctica/Davis (GMT+7:00)' ), 'antarctica-dumontdurville' => array('key' => 'Antarctica/DumontDUrville', 'value' => 'Antarctica/DumontDUrville (GMT+10:00)' ), 'antarctica-macquarie' => array('key' => 'Antarctica/Macquarie', 'value' => 'Antarctica/Macquarie (GMT+11:00)' ), 'antarctica-mawson' => array('key' => 'Antarctica/Mawson', 'value' => 'Antarctica/Mawson (GMT+5:00)' ), 'antarctica-mcmurdo' => array('key' => 'Antarctica/McMurdo', 'value' => 'Antarctica/McMurdo (GMT+13:00)' ), 'antarctica-palmer' => array('key' => 'Antarctica/Palmer', 'value' => 'Antarctica/Palmer (GMT-3:00)' ), 'antarctica-rothera' => array('key' => 'Antarctica/Rothera', 'value' => 'Antarctica/Rothera (GMT-3:00)' ), 'antarctica-syowa' => array('key' => 'Antarctica/Syowa', 'value' => 'Antarctica/Syowa (GMT+3:00)' ), 'antarctica-troll' => array('key' => 'Antarctica/Troll', 'value' => 'Antarctica/Troll (GMT+0:00)' ), 'antarctica-vostok' => array('key' => 'Antarctica/Vostok', 'value' => 'Antarctica/Vostok (GMT+6:00)' ), 'arctic-longyearbyen' => array('key' => 'Arctic/Longyearbyen', 'value' => 'Arctic/Longyearbyen (GMT+1:00)' ), 'asia-aden' => array('key' => 'Asia/Aden', 'value' => 'Asia/Aden (GMT+3:00)' ), 'asia-almaty' => array('key' => 'Asia/Almaty', 'value' => 'Asia/Almaty (GMT+6:00)' ), 'asia-amman' => array('key' => 'Asia/Amman', 'value' => 'Asia/Amman (GMT+2:00)' ), 'asia-anadyr' => array('key' => 'Asia/Anadyr', 'value' => 'Asia/Anadyr (GMT+12:00)' ), 'asia-aqtau' => array('key' => 'Asia/Aqtau', 'value' => 'Asia/Aqtau (GMT+5:00)' ), 'asia-aqtobe' => array('key' => 'Asia/Aqtobe', 'value' => 'Asia/Aqtobe (GMT+5:00)' ), 'asia-ashgabat' => array('key' => 'Asia/Ashgabat', 'value' => 'Asia/Ashgabat (GMT+5:00)' ), 'asia-atyrau' => array('key' => 'Asia/Atyrau', 'value' => 'Asia/Atyrau (GMT+5:00)' ), 'asia-baghdad' => array('key' => 'Asia/Baghdad', 'value' => 'Asia/Baghdad (GMT+3:00)' ), 'asia-bahrain' => array('key' => 'Asia/Bahrain', 'value' => 'Asia/Bahrain (GMT+3:00)' ), 'asia-baku' => array('key' => 'Asia/Baku', 'value' => 'Asia/Baku (GMT+4:00)' ), 'asia-bangkok' => array('key' => 'Asia/Bangkok', 'value' => 'Asia/Bangkok (GMT+7:00)' ), 'asia-barnaul' => array('key' => 'Asia/Barnaul', 'value' => 'Asia/Barnaul (GMT+7:00)' ), 'asia-beirut' => array('key' => 'Asia/Beirut', 'value' => 'Asia/Beirut (GMT+2:00)' ), 'asia-bishkek' => array('key' => 'Asia/Bishkek', 'value' => 'Asia/Bishkek (GMT+6:00)' ), 'asia-brunei' => array('key' => 'Asia/Brunei', 'value' => 'Asia/Brunei (GMT+8:00)' ), 'asia-chita' => array('key' => 'Asia/Chita', 'value' => 'Asia/Chita (GMT+9:00)' ), 'asia-choibalsan' => array('key' => 'Asia/Choibalsan', 'value' => 'Asia/Choibalsan (GMT+8:00)' ), 'asia-colombo' => array('key' => 'Asia/Colombo', 'value' => 'Asia/Colombo (GMT+5:30)' ), 'asia-damascus' => array('key' => 'Asia/Damascus', 'value' => 'Asia/Damascus (GMT+2:00)' ), 'asia-dhaka' => array('key' => 'Asia/Dhaka', 'value' => 'Asia/Dhaka (GMT+6:00)' ), 'asia-dili' => array('key' => 'Asia/Dili', 'value' => 'Asia/Dili (GMT+9:00)' ), 'asia-dubai' => array('key' => 'Asia/Dubai', 'value' => 'Asia/Dubai (GMT+4:00)' ), 'asia-dushanbe' => array('key' => 'Asia/Dushanbe', 'value' => 'Asia/Dushanbe (GMT+5:00)' ), 'asia-famagusta' => array('key' => 'Asia/Famagusta', 'value' => 'Asia/Famagusta (GMT+2:00)' ), 'asia-gaza' => array('key' => 'Asia/Gaza', 'value' => 'Asia/Gaza (GMT+2:00)' ), 'asia-hebron' => array('key' => 'Asia/Hebron', 'value' => 'Asia/Hebron (GMT+2:00)' ), 'asia-ho_chi_minh' => array('key' => 'Asia/Ho_Chi_Minh', 'value' => 'Asia/Ho Chi Minh (GMT+7:00)' ), 'asia-hong_kong' => array('key' => 'Asia/Hong_Kong', 'value' => 'Asia/Hong Kong (GMT+8:00)' ), 'asia-hovd' => array('key' => 'Asia/Hovd', 'value' => 'Asia/Hovd (GMT+7:00)' ), 'asia-irkutsk' => array('key' => 'Asia/Irkutsk', 'value' => 'Asia/Irkutsk (GMT+8:00)' ), 'asia-jakarta' => array('key' => 'Asia/Jakarta', 'value' => 'Asia/Jakarta (GMT+7:00)' ), 'asia-jayapura' => array('key' => 'Asia/Jayapura', 'value' => 'Asia/Jayapura (GMT+9:00)' ), 'asia-jerusalem' => array('key' => 'Asia/Jerusalem', 'value' => 'Asia/Jerusalem (GMT+2:00)' ), 'asia-kabul' => array('key' => 'Asia/Kabul', 'value' => 'Asia/Kabul (GMT+4:30)' ), 'asia-kamchatka' => array('key' => 'Asia/Kamchatka', 'value' => 'Asia/Kamchatka (GMT+12:00)' ), 'asia-karachi' => array('key' => 'Asia/Karachi', 'value' => 'Asia/Karachi (GMT+5:00)' ), 'asia-kathmandu' => array('key' => 'Asia/Kathmandu', 'value' => 'Asia/Kathmandu (GMT+5:45)' ), 'asia-khandyga' => array('key' => 'Asia/Khandyga', 'value' => 'Asia/Khandyga (GMT+9:00)' ), 'asia-kolkata' => array('key' => 'Asia/Kolkata', 'value' => 'Asia/Kolkata (GMT+5:30)' ), 'asia-krasnoyarsk' => array('key' => 'Asia/Krasnoyarsk', 'value' => 'Asia/Krasnoyarsk (GMT+7:00)' ), 'asia-kuala_lumpur' => array('key' => 'Asia/Kuala_Lumpur', 'value' => 'Asia/Kuala Lumpur (GMT+8:00)' ), 'asia-kuching' => array('key' => 'Asia/Kuching', 'value' => 'Asia/Kuching (GMT+8:00)' ), 'asia-kuwait' => array('key' => 'Asia/Kuwait', 'value' => 'Asia/Kuwait (GMT+3:00)' ), 'asia-macau' => array('key' => 'Asia/Macau', 'value' => 'Asia/Macau (GMT+8:00)' ), 'asia-magadan' => array('key' => 'Asia/Magadan', 'value' => 'Asia/Magadan (GMT+11:00)' ), 'asia-makassar' => array('key' => 'Asia/Makassar', 'value' => 'Asia/Makassar (GMT+8:00)' ), 'asia-manila' => array('key' => 'Asia/Manila', 'value' => 'Asia/Manila (GMT+8:00)' ), 'asia-muscat' => array('key' => 'Asia/Muscat', 'value' => 'Asia/Muscat (GMT+4:00)' ), 'asia-nicosia' => array('key' => 'Asia/Nicosia', 'value' => 'Asia/Nicosia (GMT+2:00)' ), 'asia-novokuznetsk' => array('key' => 'Asia/Novokuznetsk', 'value' => 'Asia/Novokuznetsk (GMT+7:00)' ), 'asia-novosibirsk' => array('key' => 'Asia/Novosibirsk', 'value' => 'Asia/Novosibirsk (GMT+7:00)' ), 'asia-omsk' => array('key' => 'Asia/Omsk', 'value' => 'Asia/Omsk (GMT+6:00)' ), 'asia-oral' => array('key' => 'Asia/Oral', 'value' => 'Asia/Oral (GMT+5:00)' ), 'asia-phnom_penh' => array('key' => 'Asia/Phnom_Penh', 'value' => 'Asia/Phnom Penh (GMT+7:00)' ), 'asia-pontianak' => array('key' => 'Asia/Pontianak', 'value' => 'Asia/Pontianak (GMT+7:00)' ), 'asia-pyongyang' => array('key' => 'Asia/Pyongyang', 'value' => 'Asia/Pyongyang (GMT+9:00)' ), 'asia-qatar' => array('key' => 'Asia/Qatar', 'value' => 'Asia/Qatar (GMT+3:00)' ), 'asia-qostanay' => array('key' => 'Asia/Qostanay', 'value' => 'Asia/Qostanay (GMT+6:00)' ), 'asia-qyzylorda' => array('key' => 'Asia/Qyzylorda', 'value' => 'Asia/Qyzylorda (GMT+5:00)' ), 'asia-riyadh' => array('key' => 'Asia/Riyadh', 'value' => 'Asia/Riyadh (GMT+3:00)' ), 'asia-sakhalin' => array('key' => 'Asia/Sakhalin', 'value' => 'Asia/Sakhalin (GMT+11:00)' ), 'asia-samarkand' => array('key' => 'Asia/Samarkand', 'value' => 'Asia/Samarkand (GMT+5:00)' ), 'asia-seoul' => array('key' => 'Asia/Seoul', 'value' => 'Asia/Seoul (GMT+9:00)' ), 'asia-shanghai' => array('key' => 'Asia/Shanghai', 'value' => 'Asia/Shanghai (GMT+8:00)' ), 'asia-singapore' => array('key' => 'Asia/Singapore', 'value' => 'Asia/Singapore (GMT+8:00)' ), 'asia-srednekolymsk' => array('key' => 'Asia/Srednekolymsk', 'value' => 'Asia/Srednekolymsk (GMT+11:00)' ), 'asia-taipei' => array('key' => 'Asia/Taipei', 'value' => 'Asia/Taipei (GMT+8:00)' ), 'asia-tashkent' => array('key' => 'Asia/Tashkent', 'value' => 'Asia/Tashkent (GMT+5:00)' ), 'asia-tbilisi' => array('key' => 'Asia/Tbilisi', 'value' => 'Asia/Tbilisi (GMT+4:00)' ), 'asia-tehran' => array('key' => 'Asia/Tehran', 'value' => 'Asia/Tehran (GMT+3:30)' ), 'asia-thimphu' => array('key' => 'Asia/Thimphu', 'value' => 'Asia/Thimphu (GMT+6:00)' ), 'asia-tokyo' => array('key' => 'Asia/Tokyo', 'value' => 'Asia/Tokyo (GMT+9:00)' ), 'asia-tomsk' => array('key' => 'Asia/Tomsk', 'value' => 'Asia/Tomsk (GMT+7:00)' ), 'asia-ulaanbaatar' => array('key' => 'Asia/Ulaanbaatar', 'value' => 'Asia/Ulaanbaatar (GMT+8:00)' ), 'asia-urumqi' => array('key' => 'Asia/Urumqi', 'value' => 'Asia/Urumqi (GMT+6:00)' ), 'asia-ust-nera' => array('key' => 'Asia/Ust-Nera', 'value' => 'Asia/Ust-Nera (GMT+10:00)' ), 'asia-vientiane' => array('key' => 'Asia/Vientiane', 'value' => 'Asia/Vientiane (GMT+7:00)' ), 'asia-vladivostok' => array('key' => 'Asia/Vladivostok', 'value' => 'Asia/Vladivostok (GMT+10:00)' ), 'asia-yakutsk' => array('key' => 'Asia/Yakutsk', 'value' => 'Asia/Yakutsk (GMT+9:00)' ), 'asia-yangon' => array('key' => 'Asia/Yangon', 'value' => 'Asia/Yangon (GMT+6:30)' ), 'asia-yekaterinburg' => array('key' => 'Asia/Yekaterinburg', 'value' => 'Asia/Yekaterinburg (GMT+5:00)' ), 'asia-yerevan' => array('key' => 'Asia/Yerevan', 'value' => 'Asia/Yerevan (GMT+4:00)' ), 'atlantic-azores' => array('key' => 'Atlantic/Azores', 'value' => 'Atlantic/Azores (GMT-1:00)' ), 'atlantic-bermuda' => array('key' => 'Atlantic/Bermuda', 'value' => 'Atlantic/Bermuda (GMT-4:00)' ), 'atlantic-canary' => array('key' => 'Atlantic/Canary', 'value' => 'Atlantic/Canary (GMT+0:00)' ), 'atlantic-cape_verde' => array('key' => 'Atlantic/Cape_Verde', 'value' => 'Atlantic/Cape Verde (GMT-1:00)' ), 'atlantic-faroe' => array('key' => 'Atlantic/Faroe', 'value' => 'Atlantic/Faroe (GMT+0:00)' ), 'atlantic-madeira' => array('key' => 'Atlantic/Madeira', 'value' => 'Atlantic/Madeira (GMT+0:00)' ), 'atlantic-reykjavik' => array('key' => 'Atlantic/Reykjavik', 'value' => 'Atlantic/Reykjavik (GMT+0:00)' ), 'atlantic-south_georgia' => array('key' => 'Atlantic/South_Georgia', 'value' => 'Atlantic/South Georgia (GMT-2:00)' ), 'atlantic-st_helena' => array('key' => 'Atlantic/St_Helena', 'value' => 'Atlantic/St Helena (GMT+0:00)' ), 'atlantic-stanley' => array('key' => 'Atlantic/Stanley', 'value' => 'Atlantic/Stanley (GMT-3:00)' ), 'australia-adelaide' => array('key' => 'Australia/Adelaide', 'value' => 'Australia/Adelaide (GMT+10:30)' ), 'australia-brisbane' => array('key' => 'Australia/Brisbane', 'value' => 'Australia/Brisbane (GMT+10:00)' ), 'australia-broken_hill' => array('key' => 'Australia/Broken_Hill', 'value' => 'Australia/Broken Hill (GMT+10:30)' ), 'australia-darwin' => array('key' => 'Australia/Darwin', 'value' => 'Australia/Darwin (GMT+9:30)' ), 'australia-eucla' => array('key' => 'Australia/Eucla', 'value' => 'Australia/Eucla (GMT+8:45)' ), 'australia-hobart' => array('key' => 'Australia/Hobart', 'value' => 'Australia/Hobart (GMT+11:00)' ), 'australia-lindeman' => array('key' => 'Australia/Lindeman', 'value' => 'Australia/Lindeman (GMT+10:00)' ), 'australia-lord_howe' => array('key' => 'Australia/Lord_Howe', 'value' => 'Australia/Lord Howe (GMT+11:00)' ), 'australia-melbourne' => array('key' => 'Australia/Melbourne', 'value' => 'Australia/Melbourne (GMT+11:00)' ), 'australia-perth' => array('key' => 'Australia/Perth', 'value' => 'Australia/Perth (GMT+8:00)' ), 'australia-sydney' => array('key' => 'Australia/Sydney', 'value' => 'Australia/Sydney (GMT+11:00)' ), 'europe-amsterdam' => array('key' => 'Europe/Amsterdam', 'value' => 'Europe/Amsterdam (GMT+1:00)' ), 'europe-andorra' => array('key' => 'Europe/Andorra', 'value' => 'Europe/Andorra (GMT+1:00)' ), 'europe-astrakhan' => array('key' => 'Europe/Astrakhan', 'value' => 'Europe/Astrakhan (GMT+4:00)' ), 'europe-athens' => array('key' => 'Europe/Athens', 'value' => 'Europe/Athens (GMT+2:00)' ), 'europe-belgrade' => array('key' => 'Europe/Belgrade', 'value' => 'Europe/Belgrade (GMT+1:00)' ), 'europe-berlin' => array('key' => 'Europe/Berlin', 'value' => 'Europe/Berlin (GMT+1:00)' ), 'europe-bratislava' => array('key' => 'Europe/Bratislava', 'value' => 'Europe/Bratislava (GMT+1:00)' ), 'europe-brussels' => array('key' => 'Europe/Brussels', 'value' => 'Europe/Brussels (GMT+1:00)' ), 'europe-bucharest' => array('key' => 'Europe/Bucharest', 'value' => 'Europe/Bucharest (GMT+2:00)' ), 'europe-budapest' => array('key' => 'Europe/Budapest', 'value' => 'Europe/Budapest (GMT+1:00)' ), 'europe-busingen' => array('key' => 'Europe/Busingen', 'value' => 'Europe/Busingen (GMT+1:00)' ), 'europe-chisinau' => array('key' => 'Europe/Chisinau', 'value' => 'Europe/Chisinau (GMT+2:00)' ), 'europe-copenhagen' => array('key' => 'Europe/Copenhagen', 'value' => 'Europe/Copenhagen (GMT+1:00)' ), 'europe-dublin' => array('key' => 'Europe/Dublin', 'value' => 'Europe/Dublin (GMT+0:00)' ), 'europe-gibraltar' => array('key' => 'Europe/Gibraltar', 'value' => 'Europe/Gibraltar (GMT+1:00)' ), 'europe-guernsey' => array('key' => 'Europe/Guernsey', 'value' => 'Europe/Guernsey (GMT+0:00)' ), 'europe-helsinki' => array('key' => 'Europe/Helsinki', 'value' => 'Europe/Helsinki (GMT+2:00)' ), 'europe-isle_of_man' => array('key' => 'Europe/Isle_of_Man', 'value' => 'Europe/Isle of Man (GMT+0:00)' ), 'europe-istanbul' => array('key' => 'Europe/Istanbul', 'value' => 'Europe/Istanbul (GMT+3:00)' ), 'europe-jersey' => array('key' => 'Europe/Jersey', 'value' => 'Europe/Jersey (GMT+0:00)' ), 'europe-kaliningrad' => array('key' => 'Europe/Kaliningrad', 'value' => 'Europe/Kaliningrad (GMT+2:00)' ), 'europe-kiev' => array('key' => 'Europe/Kiev', 'value' => 'Europe/Kiev (GMT+2:00)' ), 'europe-kirov' => array('key' => 'Europe/Kirov', 'value' => 'Europe/Kirov (GMT+3:00)' ), 'europe-lisbon' => array('key' => 'Europe/Lisbon', 'value' => 'Europe/Lisbon (GMT+0:00)' ), 'europe-ljubljana' => array('key' => 'Europe/Ljubljana', 'value' => 'Europe/Ljubljana (GMT+1:00)' ), 'europe-london' => array('key' => 'Europe/London', 'value' => 'Europe/London (GMT+0:00)' ), 'europe-luxembourg' => array('key' => 'Europe/Luxembourg', 'value' => 'Europe/Luxembourg (GMT+1:00)' ), 'europe-madrid' => array('key' => 'Europe/Madrid', 'value' => 'Europe/Madrid (GMT+1:00)' ), 'europe-malta' => array('key' => 'Europe/Malta', 'value' => 'Europe/Malta (GMT+1:00)' ), 'europe-mariehamn' => array('key' => 'Europe/Mariehamn', 'value' => 'Europe/Mariehamn (GMT+2:00)' ), 'europe-minsk' => array('key' => 'Europe/Minsk', 'value' => 'Europe/Minsk (GMT+3:00)' ), 'europe-monaco' => array('key' => 'Europe/Monaco', 'value' => 'Europe/Monaco (GMT+1:00)' ), 'europe-moscow' => array('key' => 'Europe/Moscow', 'value' => 'Europe/Moscow (GMT+3:00)' ), 'europe-oslo' => array('key' => 'Europe/Oslo', 'value' => 'Europe/Oslo (GMT+1:00)' ), 'europe-paris' => array('key' => 'Europe/Paris', 'value' => 'Europe/Paris (GMT+1:00)' ), 'europe-podgorica' => array('key' => 'Europe/Podgorica', 'value' => 'Europe/Podgorica (GMT+1:00)' ), 'europe-prague' => array('key' => 'Europe/Prague', 'value' => 'Europe/Prague (GMT+1:00)' ), 'europe-riga' => array('key' => 'Europe/Riga', 'value' => 'Europe/Riga (GMT+2:00)' ), 'europe-rome' => array('key' => 'Europe/Rome', 'value' => 'Europe/Rome (GMT+1:00)' ), 'europe-samara' => array('key' => 'Europe/Samara', 'value' => 'Europe/Samara (GMT+4:00)' ), 'europe-san_marino' => array('key' => 'Europe/San_Marino', 'value' => 'Europe/San Marino (GMT+1:00)' ), 'europe-sarajevo' => array('key' => 'Europe/Sarajevo', 'value' => 'Europe/Sarajevo (GMT+1:00)' ), 'europe-saratov' => array('key' => 'Europe/Saratov', 'value' => 'Europe/Saratov (GMT+4:00)' ), 'europe-simferopol' => array('key' => 'Europe/Simferopol', 'value' => 'Europe/Simferopol (GMT+3:00)' ), 'europe-skopje' => array('key' => 'Europe/Skopje', 'value' => 'Europe/Skopje (GMT+1:00)' ), 'europe-sofia' => array('key' => 'Europe/Sofia', 'value' => 'Europe/Sofia (GMT+2:00)' ), 'europe-stockholm' => array('key' => 'Europe/Stockholm', 'value' => 'Europe/Stockholm (GMT+1:00)' ), 'europe-tallinn' => array('key' => 'Europe/Tallinn', 'value' => 'Europe/Tallinn (GMT+2:00)' ), 'europe-tirane' => array('key' => 'Europe/Tirane', 'value' => 'Europe/Tirane (GMT+1:00)' ), 'europe-ulyanovsk' => array('key' => 'Europe/Ulyanovsk', 'value' => 'Europe/Ulyanovsk (GMT+4:00)' ), 'europe-uzhgorod' => array('key' => 'Europe/Uzhgorod', 'value' => 'Europe/Uzhgorod (GMT+2:00)' ), 'europe-vaduz' => array('key' => 'Europe/Vaduz', 'value' => 'Europe/Vaduz (GMT+1:00)' ), 'europe-vatican' => array('key' => 'Europe/Vatican', 'value' => 'Europe/Vatican (GMT+1:00)' ), 'europe-vienna' => array('key' => 'Europe/Vienna', 'value' => 'Europe/Vienna (GMT+1:00)' ), 'europe-vilnius' => array('key' => 'Europe/Vilnius', 'value' => 'Europe/Vilnius (GMT+2:00)' ), 'europe-volgograd' => array('key' => 'Europe/Volgograd', 'value' => 'Europe/Volgograd (GMT+3:00)' ), 'europe-warsaw' => array('key' => 'Europe/Warsaw', 'value' => 'Europe/Warsaw (GMT+1:00)' ), 'europe-zagreb' => array('key' => 'Europe/Zagreb', 'value' => 'Europe/Zagreb (GMT+1:00)' ), 'europe-zaporozhye' => array('key' => 'Europe/Zaporozhye', 'value' => 'Europe/Zaporozhye (GMT+2:00)' ), 'europe-zurich' => array('key' => 'Europe/Zurich', 'value' => 'Europe/Zurich (GMT+1:00)' ), 'indian-antananarivo' => array('key' => 'Indian/Antananarivo', 'value' => 'Indian/Antananarivo (GMT+3:00)' ), 'indian-chagos' => array('key' => 'Indian/Chagos', 'value' => 'Indian/Chagos (GMT+6:00)' ), 'indian-christmas' => array('key' => 'Indian/Christmas', 'value' => 'Indian/Christmas (GMT+7:00)' ), 'indian-cocos' => array('key' => 'Indian/Cocos', 'value' => 'Indian/Cocos (GMT+6:30)' ), 'indian-comoro' => array('key' => 'Indian/Comoro', 'value' => 'Indian/Comoro (GMT+3:00)' ), 'indian-kerguelen' => array('key' => 'Indian/Kerguelen', 'value' => 'Indian/Kerguelen (GMT+5:00)' ), 'indian-mahe' => array('key' => 'Indian/Mahe', 'value' => 'Indian/Mahe (GMT+4:00)' ), 'indian-maldives' => array('key' => 'Indian/Maldives', 'value' => 'Indian/Maldives (GMT+5:00)' ), 'indian-mauritius' => array('key' => 'Indian/Mauritius', 'value' => 'Indian/Mauritius (GMT+4:00)' ), 'indian-mayotte' => array('key' => 'Indian/Mayotte', 'value' => 'Indian/Mayotte (GMT+3:00)' ), 'indian-reunion' => array('key' => 'Indian/Reunion', 'value' => 'Indian/Reunion (GMT+4:00)' ), 'pacific-apia' => array('key' => 'Pacific/Apia', 'value' => 'Pacific/Apia (GMT+14:00)' ), 'pacific-auckland' => array('key' => 'Pacific/Auckland', 'value' => 'Pacific/Auckland (GMT+13:00)' ), 'pacific-bougainville' => array('key' => 'Pacific/Bougainville', 'value' => 'Pacific/Bougainville (GMT+11:00)' ), 'pacific-chatham' => array('key' => 'Pacific/Chatham', 'value' => 'Pacific/Chatham (GMT+13:45)' ), 'pacific-chuuk' => array('key' => 'Pacific/Chuuk', 'value' => 'Pacific/Chuuk (GMT+10:00)' ), 'pacific-easter' => array('key' => 'Pacific/Easter', 'value' => 'Pacific/Easter (GMT-5:00)' ), 'pacific-efate' => array('key' => 'Pacific/Efate', 'value' => 'Pacific/Efate (GMT+11:00)' ), 'pacific-enderbury' => array('key' => 'Pacific/Enderbury', 'value' => 'Pacific/Enderbury (GMT+13:00)' ), 'pacific-fakaofo' => array('key' => 'Pacific/Fakaofo', 'value' => 'Pacific/Fakaofo (GMT+13:00)' ), 'pacific-fiji' => array('key' => 'Pacific/Fiji', 'value' => 'Pacific/Fiji (GMT+12:00)' ), 'pacific-funafuti' => array('key' => 'Pacific/Funafuti', 'value' => 'Pacific/Funafuti (GMT+12:00)' ), 'pacific-galapagos' => array('key' => 'Pacific/Galapagos', 'value' => 'Pacific/Galapagos (GMT-6:00)' ), 'pacific-gambier' => array('key' => 'Pacific/Gambier', 'value' => 'Pacific/Gambier (GMT-9:00)' ), 'pacific-guadalcanal' => array('key' => 'Pacific/Guadalcanal', 'value' => 'Pacific/Guadalcanal (GMT+11:00)' ), 'pacific-guam' => array('key' => 'Pacific/Guam', 'value' => 'Pacific/Guam (GMT+10:00)' ), 'pacific-honolulu' => array('key' => 'Pacific/Honolulu', 'value' => 'Pacific/Honolulu (GMT-10:00)' ), 'pacific-kiritimati' => array('key' => 'Pacific/Kiritimati', 'value' => 'Pacific/Kiritimati (GMT+14:00)' ), 'pacific-kosrae' => array('key' => 'Pacific/Kosrae', 'value' => 'Pacific/Kosrae (GMT+11:00)' ), 'pacific-kwajalein' => array('key' => 'Pacific/Kwajalein', 'value' => 'Pacific/Kwajalein (GMT+12:00)' ), 'pacific-majuro' => array('key' => 'Pacific/Majuro', 'value' => 'Pacific/Majuro (GMT+12:00)' ), 'pacific-marquesas' => array('key' => 'Pacific/Marquesas', 'value' => 'Pacific/Marquesas (GMT-10:30)' ), 'pacific-midway' => array('key' => 'Pacific/Midway', 'value' => 'Pacific/Midway (GMT-11:00)' ), 'pacific-nauru' => array('key' => 'Pacific/Nauru', 'value' => 'Pacific/Nauru (GMT+12:00)' ), 'pacific-niue' => array('key' => 'Pacific/Niue', 'value' => 'Pacific/Niue (GMT-11:00)' ), 'pacific-norfolk' => array('key' => 'Pacific/Norfolk', 'value' => 'Pacific/Norfolk (GMT+12:00)' ), 'pacific-noumea' => array('key' => 'Pacific/Noumea', 'value' => 'Pacific/Noumea (GMT+11:00)' ), 'pacific-pago_pago' => array('key' => 'Pacific/Pago_Pago', 'value' => 'Pacific/Pago Pago (GMT-11:00)' ), 'pacific-palau' => array('key' => 'Pacific/Palau', 'value' => 'Pacific/Palau (GMT+9:00)' ), 'pacific-pitcairn' => array('key' => 'Pacific/Pitcairn', 'value' => 'Pacific/Pitcairn (GMT-8:00)' ), 'pacific-pohnpei' => array('key' => 'Pacific/Pohnpei', 'value' => 'Pacific/Pohnpei (GMT+11:00)' ), 'pacific-port_moresby' => array('key' => 'Pacific/Port_Moresby', 'value' => 'Pacific/Port Moresby (GMT+10:00)' ), 'pacific-rarotonga' => array('key' => 'Pacific/Rarotonga', 'value' => 'Pacific/Rarotonga (GMT-10:00)' ), 'pacific-saipan' => array('key' => 'Pacific/Saipan', 'value' => 'Pacific/Saipan (GMT+10:00)' ), 'pacific-tahiti' => array('key' => 'Pacific/Tahiti', 'value' => 'Pacific/Tahiti (GMT-10:00)' ), 'pacific-tarawa' => array('key' => 'Pacific/Tarawa', 'value' => 'Pacific/Tarawa (GMT+12:00)' ), 'pacific-tongatapu' => array('key' => 'Pacific/Tongatapu', 'value' => 'Pacific/Tongatapu (GMT+13:00)' ), 'pacific-wake' => array('key' => 'Pacific/Wake', 'value' => 'Pacific/Wake (GMT+12:00)' ), 'pacific-wallis' => array('key' => 'Pacific/Wallis', 'value' => 'Pacific/Wallis (GMT+12:00)' ), 'utc' => array('key' => 'UTC', 'value' => 'UTC (GMT+0:00)' ) );