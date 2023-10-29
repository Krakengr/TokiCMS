<?php defined('TOKICMS') or die('Hacking attempt...');

//Avoid index.php
Router::AddRoute( '^(?:\/)?index\.php$', 
	function()
	{
		@header( "Location: " . SITE_URL );
		@exit;
	}
);

//Admin Routes
if ( ENABLE_ADMIN )
{
	Router::AddRoute( '^(?:\/)?' . ADMIN_SLUG . '\/(([A-Za-z0-9-]+)\/)?(\?(site)=([0-9]+))?((;|\?)(blog)=([0-9]+))?((;|\?)(lang)=([0-9]+))?((;|\?)(search)=([A-Za-z0-9-_]+))?$', 
		function( $matches )
		{
			require ( ARRAYS_ROOT . 'admin-actions-arrays.php');
			
			Router::SetVariable( 'whereAmI', 'admin' );
			Router::SetVariable( 'slug', 'dashboard' );
			Router::SetVariable( 'controllerRoot', 'admin' );
			Router::SetVariable( 'isAdmin', true );
			Router::SetVariable( 'controller', 'Dashboard' );
			
			$parameters = array();

			if ( isset( $matches['2'] ) && !empty( $matches['2'] ) )
			{
				$slug = $matches['2'];

				Router::SetVariable( 'slug', $slug );
					
				if ( isset( $actionArray[$slug] ) )
				{
					Router::SetVariable( 'controller', $actionArray[$slug]['controller'] );
				}

				else
				{
					Router::SetNotFound();
					return;
				}
			}

			if ( isset( $matches['4'] ) && ( $matches['4'] == 'site' ) )
				$parameters['site'] = $matches['5'];
				
			if ( isset( $matches['8'] ) && ( $matches['8'] == 'blog' ) )
				$parameters['blog'] = $matches['9'];
				
			if ( isset( $matches['12'] ) && ( $matches['12'] == 'lang' ) )
				$parameters['lang'] = $matches['13'];
			
			if ( isset( $matches['16'] ) && ( $matches['16'] == 'search' ) )
				$parameters['search'] = $matches['17'];
			
			Router::SetVariable( 'parameters', $parameters );
		}
	);
	
	Router::AddRoute( '^(?:\/)?' . ADMIN_SLUG . '\/([A-Za-z0-9-]+)\/id\/([0-9]+)\/?(\?(site)=([0-9]+))?((;|\?)(blog)=([0-9]+))?((;|\?)(lang)=([0-9]+))?((;|\?)(draft)=([0-9]+))?$', 
		function( $matches )
		{
			require ( ARRAYS_ROOT . 'admin-actions-arrays.php');

			$slug = $matches['1'];
			$parameters = array();
			
			Router::SetVariable( 'whereAmI', 'admin' );
			Router::SetVariable( 'slug', $slug );
			Router::SetVariable( 'key', $matches['2'] );
			Router::SetVariable( 'controllerRoot', 'admin' );
			Router::SetVariable( 'isAdmin', true );

			if ( isset( $actionArray[$slug] ) )
			{
				Router::SetVariable( 'controller', $actionArray[$slug]['controller'] );
			}

			else
			{
				Router::SetNotFound();
				return;
			}

			if ( isset( $matches['4'] ) && ( $matches['4'] == 'site' ) )
				$parameters['site'] = $matches['5'];
				
			if ( isset( $matches['8'] ) && ( $matches['8'] == 'blog' ) )
				$parameters['blog'] = $matches['9'];
				
			if ( isset( $matches['12'] ) && ( $matches['12'] == 'lang' ) )
				$parameters['lang'] = $matches['13'];
			
			if ( isset( $matches['16'] ) && ( $matches['16'] == 'draft' ) )
				$parameters['draft'] = $matches['17'];
			
			Router::SetVariable( 'parameters', $parameters );
		}
	);
	
	Router::AddRoute( '^(?:\/)?' . ADMIN_SLUG . '\/([A-Za-z0-9-]+)\/(([A-Za-z0-9-]+)\/)?sort\/([A-Za-z0-9-]+)\/(asc|desc)\/((page)\/([0-9]+)\/)?(\?(site)=([0-9]+))?((;|\?)(blog)=([0-9]+))?((;|\?)(lang)=([0-9]+))?((;|\?)(search)=([A-Za-z0-9-_%]+))?((;|\?)(cat)=([0-9]+))?$', 
		function( $matches )
		{
			require ( ARRAYS_ROOT . 'admin-actions-arrays.php');

			$slug = $matches['1'];
			$parameters = array();
			
			Router::SetVariable( 'whereAmI', 'admin' );
			Router::SetVariable( 'slug', $slug );
			Router::SetVariable( 'subAction', ( isset( $matches['3'] ) ? $matches['3'] : null ) );
			Router::SetVariable( 'orderBy', $matches['4'] );
			Router::SetVariable( 'order', $matches['5'] );
			Router::SetVariable( 'controllerRoot', 'admin' );
			Router::SetVariable( 'isAdmin', true );

			if ( isset( $actionArray[$slug] ) )
			{
				Router::SetVariable( 'controller', $actionArray[$slug]['controller'] );
			}

			else
			{
				Router::SetNotFound();
				return;
			}
			
			if ( isset( $matches['7'] ) && ( $matches['7'] == 'page' ) )
				Router::SetVariable( 'pageNum', $matches['8'] );

			if ( isset( $matches['10'] ) && ( $matches['10'] == 'site' ) )
				$parameters['site'] = $matches['11'];
				
			if ( isset( $matches['14'] ) && ( $matches['14'] == 'blog' ) )
				$parameters['blog'] = $matches['15'];
				
			if ( isset( $matches['18'] ) && ( $matches['18'] == 'lang' ) )
				$parameters['lang'] = $matches['19'];
			
			if ( isset( $matches['22'] ) && ( $matches['22'] == 'search' ) )
				$parameters['search'] = $matches['23'];
			
			if ( isset( $matches['26'] ) && ( $matches['26'] == 'cat' ) )
				$parameters['category'] = $matches['27'];
			
			Router::SetVariable( 'parameters', $parameters );
		}
	);
	
	Router::AddRoute( '^(?:\/)?' . ADMIN_SLUG . '\/([A-Za-z0-9-]+)\/(([A-Za-z0-9-]+)\/)?((page|id)\/([0-9]+)\/)?((page)\/([0-9]+)\/)?(\?(site)=([0-9]+))?((;|\?)(blog)=([0-9]+))?((;|\?)(lang)=([0-9]+))?((;|\?)(cat)=([0-9]+))?$', 
		function( $matches )
		{
			require ( ARRAYS_ROOT . 'admin-actions-arrays.php');
		
			$slug = $matches['1'];
			$parameters = array();

			Router::SetVariable( 'whereAmI', 'admin' );
			Router::SetVariable( 'slug', $slug );
			Router::SetVariable( 'subAction', ( isset( $matches['3'] ) ? $matches['3'] : null ) );
			Router::SetVariable( 'controllerRoot', 'admin' );
			Router::SetVariable( 'isAdmin', true );

			if ( isset( $actionArray[$slug] ) )
			{
				Router::SetVariable( 'controller', $actionArray[$slug]['controller'] );
			}

			else
			{
				Router::SetNotFound();
				return;
			}

			//Add the Ajax function
			if ( !empty( $slug ) && ( $slug == 'ajax' ) )
			{
				if ( !empty( $matches['3'] ) )
				{
					Router::SetVariable( 'ajaxFunction', $matches['3'] );
				}
				
				if ( isset( $matches['5'] ) && ( $matches['5'] == 'id' ) )
					Router::SetVariable( 'key', $matches['6'] );
				
				if ( isset( $matches['5'] ) && ( $matches['5'] == 'page' ) )
					Router::SetVariable( 'pageNum', $matches['6'] );
				
				if ( isset( $matches['5'] ) && ( $matches['5'] == 'id' ) && isset( $matches['8'] ) && ( $matches['8'] == 'page' ) )
					Router::SetVariable( 'pageNum', $matches['9'] );
			}
			else
			{
				if ( isset( $matches['5'] ) )
					Router::SetVariable( 'pageNum', $matches['6'] );
			}

			if ( isset( $matches['11'] ) && ( $matches['11'] == 'site' ) )
				$parameters['site'] = $matches['12'];
				
			if ( isset( $matches['15'] ) && ( $matches['15'] == 'blog' ) )
				$parameters['blog'] = $matches['16'];
				
			if ( isset( $matches['19'] ) && ( $matches['19'] == 'lang' ) )
				$parameters['lang'] = $matches['20'];
			
			if ( isset( $matches['23'] ) && ( $matches['23'] == 'cat' ) )
				$parameters['category'] = $matches['24'];
			
			Router::SetVariable( 'parameters', $parameters );
		}
	);
}

//Add the ping slug
Router::AddRoute( '^(?:\/)?' . PING_SLUG . '\/\?(.*?)$', 
	function( $matches )
	{
		Router::SetVariable( 'whereAmI', 'ping' );
		Router::SetVariable( 'slug', 'ping' );
		Router::SetVariable( 'controller', 'Ping' );
	}
);

//Add the cron index
Router::AddRoute( '^(?:\/)?cron\.php?(.*?)$',
	function( $matches )
	{
		Router::SetVariable( 'whereAmI', 'cron' );
		Router::SetVariable( 'slug', 'cron' );
		Router::SetVariable( 'controller', 'Cron' );
		Router::SetVariable( 'checkSlash', false );
	}
);

//Add the "out" page
Router::AddRoute( '^(?:\/)?out\/([0-9]+)\/$', 
	function( $matches )
	{
		Router::SetVariable( 'whereAmI', 'out' );
		Router::SetVariable( 'slug', $matches['1'] );
		Router::SetVariable( 'controller', 'Out' );
	}
);

//Add the out slug for short links
$settings = Settings::LinkSettings();

if ( !empty( $settings ) && !empty( $settings['short-link-settings']['enable'] ) && !empty( $settings['short-link-settings']['base_slug_prefix'] ) )
{
	Router::AddRoute( '^(?:\/)?' . $settings['short-link-settings']['base_slug_prefix'] . '\/([A-Za-z0-9-]+)\/$', 
		function( $matches )
		{
			Router::SetVariable( 'whereAmI', 'shortlink' );
			Router::SetVariable( 'slug', $matches['1'] );
			Router::SetVariable( 'controller', 'ShortLink' );
		}
	);
}

//Add the feed
if ( Settings::IsTrue( 'enable_rss' ) )
{
	Router::AddRoute( '^(?:\/)?feed\/$', 
		function()
		{
			Router::SetVariable( 'whereAmI', 'feed' );
			Router::SetVariable( 'slug', 'feed' );
			Router::SetVariable( 'controller', 'Feed' );
		}
	);
}

//Sitemaps
if ( Settings::IsTrue( 'enable_sitemap' ) )
{
	//Redirect sitemap.xml file to index file
	Router::AddRoute( '^(?:\/)?sitemap\.xml$', 
		function()
		{
			@header('Location: ' . SITE_URL . 'sitemap_index.xml', true, 301 );
			exit;
		}
	);
	
	Router::AddRoute( '^(?:\/)?sitemap_index\.xml$', 
		function()
		{
			Router::SetVariable( 'whereAmI', 'sitemap_index' );
			Router::SetVariable( 'controller', 'SitemapIndex' );
			Router::SetVariable( 'slug', 'sitemap_index' );
			Router::SetVariable( 'checkSlash', false );
			Router::SetVariable( 'isSiteMap', true );
		}
	);
	
	Router::AddRoute( '^(?:\/)?sitemap-([^news])([A-Za-z0-9-]+)\.xml$', 
		function( $matches )
		{
			Router::SetVariable( 'whereAmI', 'sitemap_single' );
			Router::SetVariable( 'controller', 'SitemapFile' );
			Router::SetVariable( 'checkSlash', false );
			Router::SetVariable( 'isSiteMap', true );
			Router::SetVariable( 'sitemapKey', $matches['0'] );
			Router::SetVariable( 'slug', $matches['0'] );
		}
	);
	
	if ( Settings::IsTrue( 'enable_news_sitemap' ) )
	{
		Router::AddRoute( '^(?:\/)?sitemap-news\.xml$', 
			function()
			{
				Router::SetVariable( 'whereAmI', 'sitemap_news' );
				Router::SetVariable( 'controller', 'SitemapNews' );
				Router::SetVariable( 'slug', 'sitemap_news' );
				Router::SetVariable( 'checkSlash', false );
				Router::SetVariable( 'isSiteMap', true );
			}
		);
		
		//Sitemap News for other languages
		Router::AddRoute( '^(?:\/)?([A-Za-z0-9-]+)\/sitemap-news\.xml$', 
			function( $matches )
			{
				Router::SetVariable( 'slug', $matches['1'] );
				
				//We need a language for this
				if ( !Router::CheckLang() )
				{
					Router::SetNotFound();
					return;
				}
			
				Router::SetVariable( 'whereAmI', 'sitemap_news' );
				Router::SetVariable( 'controller', 'SitemapNews' );
				Router::SetVariable( 'slug', 'sitemap_news' );
				Router::SetVariable( 'checkSlash', false );
				Router::SetVariable( 'isSiteMap', true );
			}
		);
	}
}

//Api
if ( Settings::IsTrue( 'enable_api' ) )
{
	Router::AddRoute( '^(?:\/)?api\/([A-Za-z-]+)\/(([0-9]+)\/)?(\?(.*?))?$', 
		function( $matches )
		{
			Router::SetVariable( 'whereAmI', 'api' );
			Router::SetVariable( 'slug', $matches['1'] );
			Router::SetVariable( 'key', ( isset( $matches['3'] ) ? (int) $matches['3'] : null ) );
			Router::SetVariable( 'controller', 'Api' );
		}
	);
}

//Register
if ( Settings::IsTrue( 'enable_registration', 'site' ) )
{
	Router::AddRoute( '^(?:\/)?register\/$', 
		function()
		{
			Router::SetVariable( 'whereAmI', 'register' );
			Router::SetVariable( 'slug', 'register' );
			Router::SetVariable( 'controller', 'Register' );
			Router::SetVariable( 'includeFile', INC_ROOT . 'register.php' );
		}
	);
}

// Login, Logout, Forgot Password
if ( !Settings::IsTrue( 'disable_user_login' ) )
{
	//Forgot Password
	Router::AddRoute( '^(?:\/)?forgot-password\/$', 
		function()
		{
			Router::SetVariable( 'whereAmI', 'forgot-password' );
			Router::SetVariable( 'slug', 'forgot-password' );
			Router::SetVariable( 'controller', 'ForgotPassword' );
			Router::SetVariable( 'includeFile', INC_ROOT . 'forgot.php' );
		}
	);

	//Recovery Password
	Router::AddRoute( '^(?:\/)?recovery\/([A-Za-z0-9-]+)\/$', 
		function()
		{
			Router::SetVariable( 'whereAmI', 'recovery' );
			Router::SetVariable( 'slug', 'recovery' );
			Router::SetVariable( 'controller', 'Recovery' );
			Router::SetVariable( 'includeFile', INC_ROOT . 'recovery.php' );
		}
	);

	//Login
	Router::AddRoute( '^(?:\/)?login\/$', 
		function()
		{
			Router::SetVariable( 'whereAmI', 'login' );
			Router::SetVariable( 'slug', 'login' );
			Router::SetVariable( 'controller', 'Login' );
			Router::SetVariable( 'includeFile', INC_ROOT . 'login.php' );
		}
	);
	
	//LogOut
	Router::AddRoute( '^(?:\/)?logout\/$', 
		function()
		{
			Router::SetVariable( 'whereAmI', 'logout' );
			Router::SetVariable( 'slug', 'logout' );
			Router::SetVariable( 'controller', 'Logout' );
		}
	);
}

//Search
Router::AddRoute( '^(?:\/)?search\/$', 
	function()
	{
		Router::SetVariable( 'whereAmI', 'search' );
		Router::SetVariable( 'slug', 'search' );
		Router::SetVariable( 'controller', 'Search' );
		Router::SetVariable( 'isSearch', true );
	}
);

//Search in other languages
Router::AddRoute( '^(?:\/)?([A-Za-z0-9-]+)\/search\/$', 
	function( $matches )
	{
		Router::SetVariable( 'slug', $matches['1'] );

		//We need a language for this
		if ( !Router::CheckLang() )
		{
			Router::SetNotFound();
			return;
		}
		
		Router::SetVariable( 'whereAmI', 'search' );
		Router::SetVariable( 'slug', 'search' );
		Router::SetVariable( 'controller', 'Search' );
		Router::SetVariable( 'isSearch', true );
	}
);

//SiteAjax
Router::AddRoute( '^(?:\/)?ajax\.php(\?(.*?))?$',
	function()
	{
		Router::SetVariable( 'whereAmI', 'ajax' );
		Router::SetVariable( 'slug', 'ajax' );
		Router::SetVariable( 'controller', 'Ajax' );
		Router::SetVariable( 'checkSlash', false );
	}
);

//Contact form
Router::AddRoute( '^(?:\/)?contact-form\.php$',
	function()
	{
		Router::SetVariable( 'whereAmI', 'contact' );
		Router::SetVariable( 'slug', 'contact-form' );
		Router::SetVariable( 'controller', 'ContactForm' );
		Router::SetVariable( 'checkSlash', false );
	}
);

//Ads & App-Ads txt
if ( Settings::IsTrue( 'enable_ads' ) )
{
	Router::AddRoute( '^(?:\/)?ads\.txt$', 
		function()
		{
			Router::SetVariable( 'whereAmI', 'ads' );
			Router::SetVariable( 'slug', 'ads' );
			Router::SetVariable( 'controller', 'AdsTxt' );
			Router::SetVariable( 'checkSlash', false );
		}
	);
	
	Router::AddRoute( '^(?:\/)?app-ads\.txt$', 
		function()
		{
			Router::SetVariable( 'whereAmI', 'app-ads' );
			Router::SetVariable( 'slug', 'app-ads' );
			Router::SetVariable( 'controller', 'AppAdsTxt' );
			Router::SetVariable( 'checkSlash', false );
		}
	);
}

//Add comments
if ( !HIDE_COMMENTS )
{
	Router::AddRoute( '^(?:\/)?comment-post\.php$', 
	function()
	{
		Router::SetVariable( 'whereAmI', 'comment-post' );
		Router::SetVariable( 'slug', 'comment-post' );
		Router::SetVariable( 'controller', 'PostComment' );
		Router::SetVariable( 'checkSlash', false );
	}
);
}

//Robots txt
if ( Settings::IsTrue( 'enable_robots_txt' ) )
{
	Router::AddRoute( '^(?:\/)?robots\.txt$', 
		function()
		{
			Router::SetVariable( 'whereAmI', 'robots' );
			Router::SetVariable( 'slug', 'robots' );
			Router::SetVariable( 'controller', 'Robots' );
			Router::SetVariable( 'checkSlash', false );
		}
	);
}

if ( !Settings::IsTrue( 'disable_author_archives' ) )
{
	//Author Route for default language
	Router::AddRoute( '^(?:\/)?author\/([A-Za-z0-9-%]+)\/(page\/([0-9]+)\/)?$', 
		function( $matches )
		{
			Router::SetVariable( 'whereAmI', 'author' );
			Router::SetVariable( 'controller', 'Author' );
			Router::SetVariable( 'slug', $matches['1'] );
			Router::SetVariable( 'isUser', true );
			Router::SetVariable( 'authorKey', $matches['1'] );
			Router::SetVariable( 'pageNum', ( isset( $matches['3'] ) ? $matches['3'] : 1 ) );
			Router::SetVariable( 'isBrowsing', ( isset( $matches['3'] ) ? true : false ) );
		}
	);
}

//Posts Browsing. No Blogs/Langs/Categories
Router::AddRoute( '^(?:\/)?page\/([0-9]+)\/$', 
	function( $matches )
	{
		Router::SetVariable( 'slug', 'browse' );
		Router::SetVariable( 'whereAmI', 'home' );
		Router::SetVariable( 'controller', 'Browse' );
		Router::SetVariable( 'pageNum', $matches['1'] );
		Router::SetVariable( 'isBrowsing', true );
	}
);

//Blog-Language-Custom types, Short URLs, Feed and Post Route
Router::AddRoute( '^(?:\/)?([A-Za-z0-9-]+)\/((feed|amp|vote)\/)?$', 
	function( $matches )
	{
		Router::SetVariable( 'slug', $matches['1'] );
		
		//Maybe it's a post...
		if ( !Router::CheckBlog() && !Router::CheckLang() && !Router::CheckCustomTypes() && !Router::CheckShortUrls() )
		{
			Router::SetVariable( 'whereAmI', 'post' );
			Router::SetVariable( 'controller', 'Content' );
	
			if ( isset( $matches['3'] ) && ( $matches['3'] == 'amp' ) && Settings::IsTrue( 'enable_amp' ) )
			{
				Router::SetVariable( 'isAmp', true );
			}
		}

		else
		{
			//Add the feed
			if ( isset( $matches['3'] ) && ( $matches['3'] == 'feed' ) && Settings::IsTrue( 'enable_rss' ) )
			{
				Router::SetVariable( 'whereAmI', 'feed' );
				Router::SetVariable( 'controller', 'Feed' );
			}
		}
	}
);

// Preview Posts
Router::AddRoute( '^(?:\/)?(([A-Za-z0-9-]+)\/)?preview\/([0-9]+)\/(hash\/([A-Za-z0-9-]+)\/)?$',  
	function( $matches )
	{
		//We should have a valid lang here
		if ( !empty( $matches['2'] ) )
		{
			if ( !Router::CheckLang( $matches['1'] ) )
			{
				Router::SetNotFound();
				return;
			}
		}

		//Check the hash, if needed. If the site has login enabled, then the user must be logged in
		if 
		(
			!IS_DEFAULT && 
			( Settings::IsTrue( 'disable_user_login' ) && empty( $matches['5'] ) || 
			( Settings::IsTrue( 'disable_user_login' ) && !empty( $matches['5'] ) && ( !hash_equals( $matches['5'], PREVIEW_HASH ) ) ) )
		)
		{
			Router::SetNotFound();
			return;
		}

		Router::SetVariable( 'slug', 'preview' );
		Router::SetVariable( 'whereAmI', 'post' );
		Router::SetVariable( 'key', (int) $matches['3'] );
		Router::SetVariable( 'controller', 'Preview' );
	}
);

// Posts Browsing withing a lang or blog. No Categories
Router::AddRoute( '^(?:\/)?([A-Za-z0-9-]+)\/page\/([0-9]+)\/$', 
	function( $matches )
	{
		if ( !Router::CheckFirstSlug( $matches['1'] ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Router::SetVariable( 'slug', $matches['1'] );
		Router::SetVariable( 'pageNum', $matches['2'] );
		Router::SetVariable( 'isBrowsing', true );

		if ( Router::GetVariable( 'isBlog' ) )
		{
			Router::SetVariable( 'controller', 'Blog' );
			Router::SetVariable( 'whereAmI', 'blog' );
		}
		
		elseif ( Router::GetVariable( 'isLang' ) )
		{
			//Router::SetVariable( 'controller', 'Lang' );
			Router::SetVariable( 'controller', 'Home' );
		}
		
		elseif ( Router::GetVariable( 'isCustomType' ) )
		{
			Router::SetVariable( 'controller', 'CustomType' );
			Router::SetVariable( 'whereAmI', 'customType' );
		}
	}
);

if ( !Settings::IsTrue( 'disable_author_archives' ) )
{
	//Author page withing a lang
	Router::AddRoute( '^(?:\/)?([A-Za-z0-9-%]+)\/author\/([A-Za-z0-9-%]+)\/(page\/([0-9]+)\/)?$', 
		function( $matches )
		{
			if( !Router::CheckLang( $matches['1'] ) )
			{
				Router::SetNotFound();
				return;
			}
			
			Router::SetVariable( 'whereAmI', 'author' );
			Router::SetVariable( 'controller', 'Author' );
			Router::SetVariable( 'authorKey', $matches['2'] );
			Router::SetVariable( 'slug', $matches['2'] );
			Router::SetVariable( 'isUser', true );
			Router::SetVariable( 'pageNum', ( isset( $matches['4'] ) ? $matches['4'] : 0 ) );
			Router::SetVariable( 'isBrowsing', ( isset( $matches['4'] ) ? true : false ) );
		}
	);
}

// Posts, categories and tags (blog home also). Supports translated slugs, blog feeds and lang
Router::AddRoute( '^(?:\/)?([A-Za-z0-9-%]+)\/([A-Za-z0-9-]+)\/((page\/([0-9]+)\/)|(amp\/)|(vote\/))?$', 
	function( $matches )
	{
		Router::SetVariable( 'slug', $matches['2'] );
		Router::SetVariable( 'key', $matches['1'] );
		Router::CheckFirstSlug( $matches['1'] );
		
		//Maybe a blog withing a language?
		if ( Router::GetVariable( 'isLang' ) && Router::CheckFirstSlug( $matches['2'] ) )
		{
			Router::SetVariable( 'whereAmI', 'blog' );
			Router::SetVariable( 'controller', 'Blog' );
		}
		
		//Maybe a custom type?
		elseif ( Router::GetVariable( 'isCustomType' ) )
		{
			if ( Router::GetVariable( 'postSlug' ) == '/'  )
			{
				//Maybe is a post/page?
				Router::SetVariable( 'whereAmI', 'post' );
				Router::SetVariable( 'controller', 'Content' );
				Router::SetPostStatus( 'post' );
			}
				
			else
			{
				Router::SetNotFound();
				return;
			}
		}
		
		elseif ( !Router::CheckEtc() )
		{
			if ( Router::GetVariable( 'isBlog' ) || Router::GetVariable( 'isLang' ) )
			{
				if ( Router::GetVariable( 'postSlug' ) == '/'  )
				{
					//Maybe is a post/page?
					Router::SetVariable( 'whereAmI', 'post' );
					Router::SetVariable( 'controller', 'Content' );
					Router::SetPostStatus( 'post' );
				}
				
				else
				{
					Router::SetNotFound();
					return;
				}
			}
		
			elseif ( !Router::GetVariable( 'isBlog' ) && !Router::GetVariable( 'isLang' ) )
			{
				if ( Router::GetVariable( 'postSlug' ) !== '/'  )
				{
					//Maybe is a post/page?
					Router::SetVariable( 'whereAmI', 'post' );
					Router::SetVariable( 'controller', 'Content' );
					Router::SetPostStatus( 'post' );
				}
				
				//Maybe a child page? Let the controller check it
				else
				{
					Router::SetVariable( 'pageKey', $matches['1'] );
					Router::SetVariable( 'whereAmI', 'post' );
					Router::SetVariable( 'controller', 'Content' );
					Router::SetPostStatus( 'post' );
					//Router::SetNotFound();
					//return;
				}
			}
		}
		
		//Add the feed
		/*
		if ( isset( $matches['3'] ) && ( $matches['3'] == 'feed' ) && Settings::IsTrue( 'enable_rss' ) )
		{
			Router::SetVariable( 'whereAmI', 'feed' );
			Router::SetVariable( 'controller', 'Feed' );
		}*/

		if ( !empty( $matches['6'] ) )
		{
			Router::SetVariable( 'isAmp', true );
		}
		
		Router::SetVariable( 'pageNum', ( isset( $matches['5'] ) ? $matches['5'] : 1 ) );
		
		Router::SetVariable( 'isBrowsing', ( isset( $matches['5'] ) ? true : false ) );
	}
);

// Posts, categories and tags (for blog). Supports Lang for "orphan" posts. Supports translated slugs and subcategories
// Also supports child pages in blogs (and langs -no blogs)
Router::AddRoute( '^(?:\/)?([A-Za-z0-9-]+)\/([A-Za-z0-9-%]+)\/([A-Za-z0-9-]+)\/((page\/([0-9]+)\/)|(amp\/)|(vote\/))?$', 
	function( $matches )
	{
		if( !Router::CheckFirstSlug( $matches['1'] ) )
		{
			Router::SetNotFound();
			return;
		}

		//Maybe a blog withing a language?
		if ( Router::GetVariable( 'isLang' ) )
		{
			Router::CheckFirstSlug( $matches['2'] );
		}
		
		Router::SetVariable( 'slug', $matches['3'] );
		Router::SetVariable( 'key', $matches['2'] );

		if ( !Router::CheckEtc() )
		{
			if ( !Router::GetVariable( 'isLang' ) )
			{
				if ( Router::GetVariable( 'postSlug' ) !== '/' )
				{
					//Maybe is a post/page?
					Router::SetVariable( 'whereAmI', 'post' );
					Router::SetVariable( 'controller', 'Content' );
					Router::SetPostStatus( 'post' );
				}
				
				//Maybe a child page?
				else
				{
					Router::SetVariable( 'pageKey', $matches['2'] );
					Router::SetVariable( 'whereAmI', 'post' );
					Router::SetVariable( 'controller', 'Content' );
					Router::SetPostStatus( 'page' );
					//Router::SetNotFound();
					//return;
				}
			}
			
			else
			{
				if ( Router::GetVariable( 'isBlog' ) && ( Router::GetVariable( 'postSlug' ) === '/' ) )
				{
					//Maybe is a post/page?
					Router::SetVariable( 'whereAmI', 'post' );
					Router::SetVariable( 'controller', 'Content' );
					Router::SetPostStatus( 'post' );
				}
				
				//Maybe a child page?
				else
				{
					Router::SetVariable( 'pageKey', $matches['2'] );
					Router::SetVariable( 'whereAmI', 'post' );
					Router::SetVariable( 'controller', 'Content' );
					Router::SetPostStatus( 'page' );
					//Router::SetNotFound();
					//return;
				}
			}
		}
		
		//TODO: fix this
		if ( isset( $matches['7'] ) && ( ( $matches['7'] == 'amp' ) || ( $matches['7'] == 'amp/' ) ) )
		{
			Router::SetVariable( 'isAmp', true );
		}
		
		Router::SetVariable( 'pageNum', ( isset( $matches['6'] ) ? $matches['6'] : 1 ) );
		
		Router::SetVariable( 'isBrowsing', ( isset( $matches['6'] ) ? true : false ) );
	}
);

// Posts, categories and tags (for blog) withing a lang. Supports translated slugs. Supports subcategories for default language
// Also supports child pages in blogs withing a lang.
Router::AddRoute( '^(?:\/)?([A-Za-z0-9-]+)\/([A-Za-z0-9-%]+)\/([A-Za-z0-9-%]+)\/([A-Za-z0-9-]+)\/((page\/([0-9]+)\/)|(amp\/)|(vote\/))?$', 
	function( $matches )
	{
		if( !Router::CheckFirstSlug( $matches['1'] ) )
		{
			Router::SetNotFound();
			return;
		}
		
		//Maybe a blog withing a language?
		if ( Router::GetVariable( 'isLang' ) && Router::CheckFirstSlug( $matches['2'] ) )
		{
			Router::SetVariable( 'slug', $matches['4'] );
			Router::SetVariable( 'key', $matches['3'] );
		}
		
		elseif ( Router::GetVariable( 'isBlog' ) )
		{
			Router::SetVariable( 'slug', $matches['3'] );
			Router::SetVariable( 'key', $matches['2'] );
		}
		
		//No category/tag?
		if ( !Router::CheckEtc() )
		{
			if ( Router::GetVariable( 'isBlog' ) && ( Router::GetVariable( 'postSlug' ) !== '/' ) )
			{
				//Maybe is a post/page?
				Router::SetVariable( 'whereAmI', 'post' );
				Router::SetVariable( 'controller', 'Content' );
				Router::SetPostStatus( 'post' );
			}
			
			//Maybe a child page? Let the controller check it
			else
			{
				Router::SetVariable( 'pageKey', $matches['3'] );
				Router::SetVariable( 'whereAmI', 'post' );
				Router::SetVariable( 'controller', 'Content' );
				Router::SetPostStatus( 'page' );
				//Router::SetNotFound();
				//return;
			}
		}
		
		Router::SetVariable( 'subCategoryKey', ( ( !Router::GetVariable( 'isLang' ) && Router::GetVariable( 'isCat' ) && isset( $matches['4'] ) ) ? $matches['4'] : null ) );
		
		Router::SetVariable( 'isSubCat', ( ( !Router::GetVariable( 'isLang' ) && Router::GetVariable( 'isCat' ) && Router::GetVariable( 'subCategoryKey' ) ) ? true : false ) );

		if ( isset( $matches['8'] ) && Settings::IsTrue( 'enable_amp' ) )
		{
			Router::SetVariable( 'isAmp', true );
		}
		
		Router::SetVariable( 'pageNum', ( isset( $matches['7'] ) ? $matches['7'] : 0 ) );
		
		Router::SetVariable( 'isBrowsing', ( isset( $matches['7'] ) ? true : false ) );
	}
);

// Posts, categories and tags (for blog) withing a lang. Supports translated slugs. Supports subcategories for languages
Router::AddRoute( '^(?:\/)?([A-Za-z0-9-]+)\/([A-Za-z0-9-%]+)\/([A-Za-z0-9-%]+)\/([A-Za-z0-9-]+)\/([A-Za-z0-9-]+)\/(page\/([0-9]+)\/)?$', 
	function( $matches )
	{
		if( !Router::CheckFirstSlug( $matches['1'] ) )
		{
			Router::SetNotFound();
			return;
		}
		
		//Maybe a blog withing a language?
		if ( Router::GetVariable( 'isLang' ) && Router::CheckFirstSlug( $matches['2'] ) )
		{
			Router::SetVariable( 'slug', $matches['4'] );
			Router::SetVariable( 'key', $matches['3'] );
			Router::SetVariable( 'whereAmI', 'blog' );
			Router::SetVariable( 'controller', 'Blog' );
		}
		
		elseif ( Router::GetVariable( 'isBlog' ) )
		{
			Router::SetVariable( 'slug', $matches['3'] );
			Router::SetVariable( 'key', $matches['2'] );
		}
		
		//No category/tag?
		if ( !Router::CheckEtc() )
		{
			//Maybe is a post/page?
			Router::SetVariable( 'whereAmI', 'post' );
			Router::SetVariable( 'controller', 'Content' );
			Router::SetPostStatus( 'post' );
		}
		
		Router::SetVariable( 'subCategoryKey', ( ( !Router::GetVariable( 'isCat' ) && isset( $matches['5'] ) ) ? $matches['5'] : null ) );
		
		Router::SetVariable( 'isSubCat', ( ( Router::GetVariable( 'isCat' ) && Router::GetVariable( 'subCategoryKey' ) ) ? true : false ) );
		
		Router::SetVariable( 'pageNum', ( isset( $matches['7'] ) ? $matches['7'] : 0 ) );
		
		Router::SetVariable( 'isBrowsing', ( isset( $matches['7'] ) ? true : false ) );
	}
);
