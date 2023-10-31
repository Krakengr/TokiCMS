<?php

class Sitemap
{
    private 	$sitemapArray 		= [];
	private 	$sitemapNewsData 	= [];
	private 	$sitemapData;
	private 	$botId;
	private 	$db;
	private 	$guestsId;
	private 	$videoBlogId;
	private 	$videoSettings;
	private 	$enableVideosSitemap;
	private 	$enableNewsSitemap;
	private 	$lang;
	private 	$limit;
	private 	$includePosts;
	private 	$includePages;
	private 	$parentHasPosts 	= false;
	private 	$parentHasPages 	= false;
	private 	$videoHasPosts 		= false;
	public		$sitemapFile;

	function __construct()
	{
		$this->db 					= db();
		$this->botId 				= 6;
		$this->guestsId 			= 5;
		$this->sitemapData 			= Settings::Sitemap();
		$this->videoSettings 		= Settings::Video();
		$this->lang 				= Settings::LangData();
		$this->enableNewsSitemap 	= Settings::IsTrue( 'enable_news_sitemap' );
		$this->sitemapNewsData 		= Json( Settings::Get()['news_sitemap_data'] );
		$this->videoBlogId 			= $this->SitemapVideoBlog();
		$this->limit 				= ( ( isset( $this->sitemapData['limit_urls'] ) && is_numeric( $this->sitemapData['limit_urls'] ) ) ? 
										$this->sitemapData['limit_urls'] : 1000 );
		$this->includePosts 		= ( isset( $this->sitemapData['include_posts'] ) && $this->sitemapData['include_posts'] );
		$this->includePages 		= ( isset( $this->sitemapData['include_pages'] ) && $this->sitemapData['include_pages'] );
		$this->enableVideosSitemap 	= ( isset( $this->videoSettings['enable_videos_sitemap'] ) && $this->videoSettings['enable_videos_sitemap'] );
	}
	
	#####################################################
	#
	# Sitemap News List function
	#
	#####################################################
	private function SitemapNews()
	{
		if ( !$this->enableNewsSitemap || empty( $this->sitemapNewsData ) )
			return;

		$langs = Settings::AllLangsById();
		
		//this is not going to happen, but let's be safe
		if ( empty( $langs ) )
		{
			return;
		}

		foreach( $langs as $id => $lang )
		{
			$langCode = $lang['lang']['code'];
			
			$posts = $this->GetSiteMapNewsPosts( $lang['lang']['id'] );

			//New XML File
			$doc = new DOMDocument('1.0', 'UTF-8');

			// Friendly XML code
			$doc->formatOutput = true;

			//create "RSS" element
			$urlset = $doc->createElement("urlset");
			$rss_node = $doc->appendChild($urlset); //add RSS element to XML node
			$rss_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
			$rss_node->setAttribute("xmlns:news","http://www.google.com/schemas/sitemap-news/0.9");
			$rss_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
			$rss_node->setAttribute("xsi:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-news/0.9 http://www.google.com/schemas/sitemap-news/0.9/sitemap-news.xsd");
			
			//We will keep the files even if there are no posts to avoid "404 - file not found" errors
			if ( !empty( $posts ) )
			{
				foreach( $posts as $page )
				{
					$date = ( !empty( $page['updated'] ) ? $page['updated']['c'] : $page['added']['c'] );
					
					$channel = $doc->createElement("url");  
					$channel_node = $rss_node->appendChild($channel);
					
					$channel_node->appendChild($doc->createElement( "loc", $page['postUrl'] ) );
					
					$sec = $doc->createElement("news:news"); 
					$sec_node = $channel_node->appendChild( $sec );
					
					$pub = $doc->createElement("news:publication"); 
					$pub_node = $sec_node->appendChild( $pub );
					$pub_node->appendChild($doc->createElement( "news:name", htmlspecialchars( $this->sitemapNewsData['publication_name'] ) ) );
					$pub_node->appendChild($doc->createElement( "news:language", $langCode ) );

					$sec_node->appendChild($doc->createElement( "news:title", htmlspecialchars( $page['title'] ) ) );
					
					$sec_node->appendChild($doc->createElement( "news:publication_date", $page['added']['r'] ) );
					
					$sec_node->appendChild($doc->createElement( "news:keywords", htmlspecialchars( $page['category']['name'] ) ) );
				}
			}

			$cacheFile = SitemapCacheFile( 'news-sitemap-' . $langCode . '.xml' );
			
			//We will not add this file into the sitemap array, this is a special file and should be called differently
			$doc->save( $cacheFile );
		}
	}
	
	#####################################################
	#
	# Sitemap Video List function
	#
	#####################################################
	private function SitemapVideoList()
	{
		if ( !is_numeric( $this->videoBlogId ) || !$this->enableVideosSitemap || !$this->includePosts )
			return;

		$count = $this->CountSiteMapPosts( $this->videoBlogId, 'posts' );
		
		if ( $count == 0 )
			return;
		
		$hasPages = ( ( $count > $this->limit ) ? true : false );
		
		$numberOfPages = ceil( $count / $this->limit );
		
		$numberOfPages = (int) $numberOfPages;

		for ( $i = 0; $i < $numberOfPages; $i++ )
		{
			$from = ( ( $i == 0 ) ? 0 : ( ( $i * $this->limit ) - $this->limit ) );

			//Get the posts only for this blog	
			$posts = $this->GetSiteMapPosts( $this->videoBlogId, 'posts', null, true, $this->limit, $from );
			
			if ( empty( $posts ) )
				continue;
			
			$added = 0;
			
			//Create a new Dom
			$doc = new DOMDocument('1.0', 'UTF-8');

			//Non Friendly XML code
			$doc->formatOutput = false;

			//Create urlset element
			$urlset = $doc->createElement("urlset");
			$url_node = $doc->appendChild( $urlset );
			
			//set attributes
			$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
			$url_node->setAttribute("xmlns:video","http://www.google.com/schemas/sitemap-video/1.1");
			$url_node->setAttribute("xmlns:image","http://www.google.com/schemas/sitemap-image/1.1");

			foreach( $posts as $page )
			{
				$postExtras = ( ( !empty( $page['postExtras'] ) && isset( $page['postExtras']['video'] ) ) ? $page['postExtras']['video'] : null );
				
				if ( empty( $postExtras ) )
					continue;
				
				$added++;
				
				$duration = 0;
				
				if ( !empty( $postExtras['duration'] ) )
				{
					if ( is_numeric( $postExtras['duration']['min'] ) && is_numeric( $postExtras['duration']['sec'] ) )
					{
						$duration = ( ( $postExtras['duration']['min'] * 60 ) + $postExtras['duration']['sec'] );
					}
				}

				$date = ( !empty( $page['updated'] ) ? $page['updated']['c'] : $page['added']['c'] );

				$index_url = $doc->createElement( 'url' );
				$index_url->appendChild( $doc->createElement( 'loc', $page['postUrl'] ) );
				$index_url->appendChild( $doc->createElement( 'lastmod', $date ) );

				$vid = $index_url->appendChild($doc->createElement('video:video'));

				$vidTitle = $vid->appendChild($doc->createElement('video:title'));
				$vidTitleDt = $doc->createCDATASection(htmlspecialchars($page['title'] , ENT_QUOTES));
				$vidTitle->appendChild($vidTitleDt);

				$vid->appendChild($doc->createElement('video:publication_date', $page['added']['c']));
					
				$vidDecr = $vid->appendChild($doc->createElement('video:description'));
				$vidDecrDt = $doc->createCDATASection(htmlspecialchars($page['description'] , ENT_QUOTES));
				$vidDecr->appendChild($vidDecrDt);
					
				$vidLoc = $vid->appendChild($doc->createElement('video:player_loc', $postExtras['videoUrl'] ) );
					
				$vidLoc->setAttribute("allow_embed","no");
					
				$vid->appendChild( $doc->createElement('video:thumbnail_loc', ( !empty( $page['coverImage'] ) ? $page['coverImage']['default']['imageUrl'] : '' )));

				$vid->appendChild( $doc->createElement('video:duration', $duration ) );
					
				$vid->appendChild( $doc->createElement('video:view_count', $page['views']));
					
				$vid->appendChild( $doc->createElement('video:width', $postExtras['videoWidth'] ) );
					
				$vid->appendChild( $doc->createElement('video:height', $postExtras['videoHeight'] ) );

				//Category
				if ( $this->videoSettings['enable_categories_sitemap'] )
				{
					$vidCat = $vid->appendChild($doc->createElement('video:category'));
					$vidCatDt = $doc->createCDATASection(htmlspecialchars($page['category']['name'] , ENT_QUOTES));
					$vidCat->appendChild($vidCatDt);
				}

				//Tags
				if ( $this->videoSettings['enable_tags_sitemap'] && !empty( $page['tags'] ) )
				{
					foreach( $page['tags'] as $tag )
					{
						$vidTag = $vid->appendChild( $doc->createElement('video:tag') );
						$vidTagDt = $doc->createCDATASection( htmlspecialchars($tag['name'] , ENT_QUOTES ) );
						$vidTag->appendChild($vidTagDt);
					}
				}

				$vid->appendChild( $doc->createElement( 'video:family_friendly', ( ( isset( $postExtras['familyFriendly'] ) && $postExtras['familyFriendly'] ) ? 'yes' : ( ( isset( $postExtras['familyFriendly'] ) && !$postExtras['familyFriendly'] ) ? 'no' : 'yes' ) ) ) );

				$vidUser = $vid->appendChild($doc->createElement( 'video:uploader', $page['author']['name'] ) );
				$vidUser->setAttribute( "info", $page['author']['url'] );

				$urlset->appendChild($index_url);
			}
		
			if ( $added == 0 )
				continue;

			$this->videoHasPosts = true;
			
			$file = ( $hasPages ? 'sitemap-video-' . $i . '.xml' : 'sitemap-video.xml' );
			
			$filename = SitemapCacheFile ( $file );

			//Add this file into the array
			$this->BuildSitemapArray( $file );
			
			$doc->save( $filename );
		}
	}
	
	#####################################################
	#
	# Sitemap Post List function
	#
	# This function will get the posts that don't belong to any blog (aka "orphans")
	#
	#####################################################
	private function SitemapPostList()
	{
		if ( !$this->includePosts )
			return;

		//If the parent site is for videos, don't add it here but only if the video sitemap has data in it
		if ( $this->enableVideosSitemap && ( $this->videoBlogId == 0 ) && $this->videoHasPosts )
			return;
		
		$count = $this->CountSiteMapPosts( 0, 'posts' );
		
		if ( $count == 0 )
			return;
		
		$hasPages = ( ( $count > $this->limit ) ? true : false );
		
		$numberOfPages = ceil( $count / $this->limit );
		
		$numberOfPages = (int) $numberOfPages;
		
		$numPosts = 0;

		for ( $i = 0; $i < $numberOfPages; $i++ )
		{
			$from = ( ( $i == 0 ) ? 0 : ( ( $i * $this->limit ) - $this->limit ) );

			//Get the posts but skip video posts
			$posts = $this->GetSiteMapPosts( 0, 'posts', null, false, $this->limit, $from );
			
			if ( empty( $posts ) )
				continue;
			
			$numPosts++;
			
			//Create a new Dom
			$doc = new DOMDocument('1.0', 'UTF-8');

			//Non Friendly XML code
			$doc->formatOutput = false;

			//Create urlset element
			$urlset = $doc->createElement("urlset");
			$url_node = $doc->appendChild( $urlset );

			//set attributes
			$url_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
			$url_node->setAttribute("xmlns:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9");
			$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
			$url_node->setAttribute("xmlns:image","http://www.google.com/schemas/sitemap-image/1.1");

			foreach( $posts as $post )
			{
				$date = ( !empty( $post['updated'] ) ? $post['updated']['c'] : $post['added']['c'] );

				$index_url = $doc->createElement( 'url' );
				$index_url->appendChild( $doc->createElement( 'loc', $post['postUrl'] ) );
				
				if ( !empty( $post['coverImage'] ) )
				{
					$cover = $post['coverImage']['default'];
					
					//Append image
					$img = $index_url->appendChild( $doc->createElement( 'image:image' ) );
					$img->appendChild( $doc->createElement( 'image:loc', $cover['imageUrl'] ) );

					if ( !empty( $cover['imageTitle'] ) )
						$img->appendChild( $doc->createElement( 'image:title', htmlspecialchars( $cover['imageTitle'] ) ) );

					if ( !empty( $cover['imageCaption'] ) )
						$img->appendChild( $doc->createElement( 'image:caption', htmlspecialchars( $cover['imageCaption'] ) ) );
				}

				if ( $this->sitemapData['include_the_last_modification_time'] )
					$index_url->appendChild( $doc->createElement( 'lastmod', $date ) );

				$index_url->appendChild( $doc->createElement( 'changefreq', 'monthly'  ) );	

				if ( !empty( $this->sitemapData['posts_priority'] ) )
					$index_url->appendChild( $doc->createElement( 'priority', $this->sitemapData['posts_priority'] ) );

				$urlset->appendChild( $index_url );
			}
			
			$file = ( $hasPages ? 'sitemap-posts-' . $i . '.xml' : 'sitemap-posts.xml' );

			$filename = SitemapCacheFile ( $file );

			//Add this file into the array
			$this->BuildSitemapArray( $file );
			
			//Save the file
			$doc->save( $filename );
		}
		
		$this->parentHasPosts = ( ( $numPosts > 0 ) ? true : false );
	}
	
	#####################################################
	#
	# Sitemap Post List Per Blog function
	#
	# This function will get the posts that belong to a blog
	#
	#####################################################
	function SitemapBlogPostList()
	{
		if ( !$this->includePosts || !MULTIBLOG )
			return;
			
		$blogs = Settings::BlogsFullArray();

		if ( empty( $blogs ) )
			return;

		//Loop through the blogs
		foreach( $blogs as $b => $blog )
		{
			//If we have a videos blog, then skip it
			if ( $this->enableVideosSitemap && !is_null( $this->videoBlogId ) && ( $blog['id_blog'] == $this->videoBlogId ) )
				continue;
				
			//If we don't want this blog in sitemap, then skip it
			if ( $blog['hide_sitemap'] )
				continue;

			//Check if we have any group selected and skip this blog if guests are not allowed
			$groups = Json( $blog['groups_data'] );

			if ( !empty( $groups ) && ( !in_array( $this->botId, $groups  ) || !in_array( $this->guestsId, $groups ) ) )
				continue;

			$count = $this->CountSiteMapPosts( $blog['id_blog'], 'posts' );

			if ( $count == 0 )
				continue;

			$hasPages = ( ( $count > $this->limit ) ? true : false );
			
			$numberOfPages = ceil( $count / $this->limit );
			
			$numberOfPages = (int) $numberOfPages;

			for ( $i = 0; $i < $numberOfPages; $i++ )
			{
				$from = ( ( $i == 0 ) ? 0 : ( ( $i * $this->limit ) - $this->limit ) );

				//Get the posts only for this blog	
				$posts = $this->GetSiteMapPosts( $blog['id_blog'], 'posts', null, false, $this->limit, $from );
			
				if ( empty( $posts ) )
					continue;

				$doc = new DOMDocument('1.0', 'UTF-8');

				//Non Friendly XML code
				$doc->formatOutput = false;

				// create urlset element
				$urlset = $doc->createElement("urlset");
				$url_node = $doc->appendChild( $urlset );
				
				//set attributes
				$url_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
				$url_node->setAttribute("xmlns:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9");
				$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
				$url_node->setAttribute("xmlns:image","http://www.google.com/schemas/sitemap-image/1.1");

				foreach( $posts as $post )
				{
					$date = ( !empty( $post['updated'] ) ? $post['updated']['c'] : $post['added']['c'] );

					$index_url = $doc->createElement( 'url' );
					$index_url->appendChild( $doc->createElement( 'loc', $post['postUrl'] ) );
					
					if ( !empty( $post['coverImage'] ) )
					{
						$cover = $post['coverImage']['default'];
						
						//Append image
						$img = $index_url->appendChild( $doc->createElement( 'image:image' ) );
						$img->appendChild( $doc->createElement( 'image:loc', $cover['imageUrl'] ) );

						if ( !empty( $cover['imageTitle'] ) )
							$img->appendChild( $doc->createElement( 'image:title', htmlspecialchars( $cover['imageTitle'] ) ) );

						if ( !empty( $cover['imageCaption'] ) )
							$img->appendChild( $doc->createElement( 'image:caption', htmlspecialchars( $cover['imageCaption'] ) ) );
					}

					if ( $this->sitemapData['include_the_last_modification_time'] )
						$index_url->appendChild( $doc->createElement( 'lastmod', $date ) );
						
					$index_url->appendChild( $doc->createElement( 'changefreq', 'monthly'  ) );	

					if ( !empty( $this->sitemapData['posts_priority'] ) )
						$index_url->appendChild( $doc->createElement( 'priority', $this->sitemapData['posts_priority'] ) );

						$urlset->appendChild($index_url);
				}
				
				$file = ( $hasPages ? 'sitemap-' . $b . '-posts-' . $i . '.xml' : 'sitemap-' . $b . '-posts.xml' );

				$filename = SitemapCacheFile ( $file );

				//Add this file into the array
				$this->BuildSitemapArray( $file );

				$doc->save( $filename );
			}
		}
	}
	
	#####################################################
	#
	# Sitemap Page List function
	#
	# This function will get the pages that don't belong to any blog (aka "orphans")
	#
	#####################################################
	function SitemapPageList()
	{
		if ( !$this->includePages )
			return;
		
		$count = $this->CountSiteMapPosts( 0, 'pages' );
		
		if ( $count == 0 )
			return;
		
		$hasPages = ( ( $count > $this->limit ) ? true : false );
		
		$numberOfPages = ceil( $count / $this->limit );
		
		$numberOfPages = (int) $numberOfPages;
		
		$numPosts = 0;
		
		for ( $i = 0; $i < $numberOfPages; $i++ )
		{
			$from = ( ( $i == 0 ) ? 0 : ( ( $i * $this->limit ) - $this->limit ) );

			//Get the pages
			$posts = $this->GetSiteMapPosts( 0, 'pages', null, false, $this->limit, $from );

			if ( empty( $posts ) )
				continue;
			
			$numPosts++;

			$doc = new DOMDocument('1.0', 'UTF-8');
			
			//Non Friendly XML code
			$doc->formatOutput = false;

			// create urlset element
			$urlset = $doc->createElement("urlset");
			$url_node = $doc->appendChild( $urlset );

			//set attributes
			$url_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
			$url_node->setAttribute("xmlns:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9");
			$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
			$url_node->setAttribute("xmlns:image","http://www.google.com/schemas/sitemap-image/1.1");
				
			foreach( $posts as $post )
			{
				$date = ( !empty( $post['updated'] ) ? $post['updated']['c'] : $post['added']['c'] );

				$index_url = $doc->createElement( 'url' );
				$index_url->appendChild( $doc->createElement( 'loc', $post['postUrl'] ) );
				
				if ( !empty( $post['coverImage'] ) )
				{
					$cover = $post['coverImage']['default'];
						
					//Append image
					$img = $index_url->appendChild( $doc->createElement( 'image:image' ) );
					$img->appendChild( $doc->createElement( 'image:loc', $cover['imageUrl'] ) );

					if ( !empty( $cover['imageTitle'] ) )
						$img->appendChild( $doc->createElement( 'image:title', htmlspecialchars( $cover['imageTitle'] ) ) );

					if ( !empty( $cover['imageCaption'] ) )
						$img->appendChild( $doc->createElement( 'image:caption', htmlspecialchars( $cover['imageCaption'] ) ) );
				}

				if ( $this->sitemapData['include_the_last_modification_time'] )
					$index_url->appendChild( $doc->createElement( 'lastmod', $date ) );
						
				$index_url->appendChild( $doc->createElement( 'changefreq', 'monthly'  ) );	

				if ( !empty( $this->sitemapData['pages_priority'] ) )
					$index_url->appendChild( $doc->createElement( 'priority', $this->sitemapData['pages_priority'] ) );

				$urlset->appendChild($index_url);
			}
			
			$file = ( $hasPages ? 'sitemap-pages-' . $i . '.xml' : 'sitemap-pages.xml' );

			$filename = SitemapCacheFile ( $file );

			//Add this file into the array
			$this->BuildSitemapArray( $file );

			$doc->save( $filename );
		}
		
		$this->parentHasPages = ( ( $numPosts > 0 ) ? true : false );
	}
	
	#####################################################
	#
	# Sitemap Page List Per Blog function
	#
	# This function will get the pages for a blog
	#
	#####################################################
	function SitemapBlogPageList()
	{
		if ( !$this->includePages || !MULTIBLOG )
			return;
			
		$blogs = Settings::BlogsFullArray();
			
		if ( empty( $blogs ) )
			return;
			
		//Loop through the blogs
		foreach( $blogs as $b => $blog )
		{
			//If we don't want this blog in sitemap, then skip it
			if ( $blog['hide_sitemap'] )
				continue;
				
			//Check if we have any group selected and skip this blog if guests are not allowed
			$groups = Json( $blog['groups_data'] );

			if ( !empty( $groups ) && ( !in_array( $this->botId, $groups ) || !in_array( $this->guestsId, $groups ) ) )
				continue;
			
			$count = $this->CountSiteMapPosts( $blog['id_blog'], 'pages' );

			if ( $count == 0 )
				continue;

			$hasPages = ( ( $count > $this->limit ) ? true : false );
			
			$numberOfPages = ceil( $count / $this->limit );
			
			$numberOfPages = (int) $numberOfPages;

			for ( $i = 0; $i < $numberOfPages; $i++ )
			{
				$from = ( ( $i == 0 ) ? 0 : ( ( $i * $this->limit ) - $this->limit ) );

				//Get the pages only for this blog	
				$posts = $this->GetSiteMapPosts( $blog['id_blog'], 'pages', null, false, $this->limit, $from );
			
				if ( empty( $posts ) )
					continue;

				$doc = new DOMDocument('1.0', 'UTF-8');
				
				//Non Friendly XML code
				$doc->formatOutput = false;

				// create urlset element
				$urlset = $doc->createElement("urlset");
				$url_node = $doc->appendChild( $urlset );

				//set attributes
				$url_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
				$url_node->setAttribute("xmlns:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9");
				$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
				$url_node->setAttribute("xmlns:image","http://www.google.com/schemas/sitemap-image/1.1");
				
				foreach( $posts as $post )
				{
					$date = ( !empty( $post['updated'] ) ? $post['updated']['c'] : $post['added']['c'] );

					$index_url = $doc->createElement( 'url' );
					$index_url->appendChild( $doc->createElement( 'loc', $post['postUrl'] ) );
					
					if ( !empty( $post['coverImage'] ) )
					{
						$cover = $post['coverImage']['default'];

						//Append image
						$img = $index_url->appendChild( $doc->createElement( 'image:image' ) );
						$img->appendChild( $doc->createElement( 'image:loc', $cover['imageUrl'] ) );

						if ( !empty( $cover['imageTitle'] ) )
							$img->appendChild( $doc->createElement( 'image:title', htmlspecialchars( $cover['imageTitle'] ) ) );

						if ( !empty( $cover['imageCaption'] ) )
							$img->appendChild( $doc->createElement( 'image:caption', htmlspecialchars( $cover['imageCaption'] ) ) );
					}

					if ( $this->sitemapData['include_the_last_modification_time'] )
						$index_url->appendChild( $doc->createElement( 'lastmod', $date ) );
						
					$index_url->appendChild( $doc->createElement( 'changefreq', 'monthly'  ) );	

					if ( !empty( $this->sitemapData['pages_priority'] ) )
						$index_url->appendChild( $doc->createElement( 'priority', $this->sitemapData['pages_priority'] ) );

					$urlset->appendChild($index_url);
				}
				
				$file = ( $hasPages ? 'sitemap-' . $b . '-pages-' . $i . '.xml' : 'sitemap-' . $b . '-pages.xml' );
				
				$filename = SitemapCacheFile ( $file );

				//Add this file into the array
				$this->BuildSitemapArray( $file );

				$doc->save( $filename );
			}
		}
	}
	
	#####################################################
	#
	# Sitemap Index File function
	#
	#####################################################
	private function SitemapIndexFile()
	{
		$doc = new DOMDocument('1.0', 'UTF-8');

		//Non Friendly XML code
		$doc->formatOutput = false;

		// create urlset element
		$urlset = $doc->createElement("sitemapindex");
		$url_node = $doc->appendChild( $urlset );

		//set attributes
		$url_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
		$url_node->setAttribute("xmlns:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9");
		$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
		
		if ( empty( $this->sitemapArray ) )
			return;
		
		foreach( $this->sitemapArray as $file )
		{
			$url = SITE_URL . $file;

			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild($index_url);
		}
		
		if ( isset( $this->sitemapData['include_homepage'] ) && $this->sitemapData['include_homepage'] )
		{
			$url = SITE_URL . 'sitemap-home.xml';

			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild($index_url);
		}
		
		$filename = SitemapCacheFile ( 'sitemap_index.xml' );

		return $doc->save( $filename );
		
		if ( isset( $this->sitemapData['include_custom_post_types'] ) && $this->sitemapData['include_custom_post_types'] )
		{
			//TODO
			$url = SITE_URL . 'sitemap-custom-types.xml';
		}
			
		if ( MULTIBLOG && isset( $this->sitemapData['include_blogs'] ) && $this->sitemapData['include_blogs'] )
		{
			$url = SITE_URL . 'sitemap-blogs.xml';

			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild($index_url);
		
			$blogs = Settings::BlogsFullArray();
				
			if ( !empty( $blogs ) )
			{
				foreach( $blogs as $b => $blog )
				{
					//If we don't want this blog in sitemap, then skip it
					if ( $blog['hide_sitemap'] )
						continue;
						
					//Check if we have any group selected and skip this blog if guests are not allowed
					$groups = Json( $blog['groups_data'] );

					if ( !empty( $groups ) && ( !in_array( $this->botId, $groups  ) || !in_array( $this->guestsId, $groups ) ) )
						continue;
				
					if ( $this->includePosts )
					{
						$filename = 'sitemap-' . $b . '-posts.xml';
							
						if ( !empty( $this->sitemapArray ) && !in_array( $filename, $this->sitemapArray ) )
							continue;
							
						$url = SITE_URL . $filename;
					
						$index_url = $doc->createElement( 'sitemap' );
						$index_url->appendChild( $doc->createElement( 'loc', $url ) );
						$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

						$urlset->appendChild($index_url);
					}

					if ( $this->includePages )
					{
						$filename = 'sitemap-' . $b . '-pages.xml';

						if ( !empty( $this->sitemapArray ) && !in_array( $filename, $this->sitemapArray ) )
							continue;

						$url = SITE_URL . $filename;
					
						$index_url = $doc->createElement( 'sitemap' );
						$index_url->appendChild( $doc->createElement( 'loc', $url ) );
						$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

						$urlset->appendChild($index_url);
					}
				}
			}
		}
			
		if ( MULTILANG && isset( $this->sitemapData['include_langs'] ) && $this->sitemapData['include_langs'] )
		{
			$url = SITE_URL . 'sitemap-langs.xml';

			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild($index_url);
		}
		
		if ( $this->enableVideosSitemap && !empty( $this->sitemapArray ) && in_array( 'sitemap-video.xml', $this->sitemapArray ) )
		{
			$url = SITE_URL . 'sitemap-video.xml';

			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild( $index_url );
		}
			
		if ( $this->parentHasPosts )
		{
			$url = SITE_URL . 'sitemap-posts.xml';

			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild( $index_url );
		}

		if ( $this->parentHasPages )
		{
			$url = SITE_URL . 'sitemap-pages.xml';

			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild($index_url);
		}
				
		if ( isset( $this->sitemapData['include_authors'] ) && $this->sitemapData['include_authors'] )
		{
			$url = SITE_URL . 'sitemap-authors.xml';
				
			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild($index_url);
		}
			
		if ( isset( $this->sitemapData['include_categories'] ) && $this->sitemapData['include_categories'] )
		{
			$url = SITE_URL . 'sitemap-categories.xml';
				
			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild($index_url);
		}
			
		if ( isset( $this->sitemapData['include_tags'] ) && $this->sitemapData['include_tags'] )
		{
			$url = SITE_URL . 'sitemap-tags.xml';
				
			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild($index_url);
		}
			
		if ( isset( $this->sitemapData['include_homepage'] ) && $this->sitemapData['include_homepage'] )
		{
			$url = SITE_URL . 'sitemap-home.xml';

			$index_url = $doc->createElement( 'sitemap' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

			$urlset->appendChild($index_url);
		}

		$filename = SitemapCacheFile ( 'sitemap_index.xml' );

		return $doc->save( $filename );
}

	#####################################################
	#
	# Sitemap Blog List function
	#
	#####################################################
	function SitemapBlogList()
	{
		if ( !MULTIBLOG || !isset( $this->sitemapData['include_blogs'] ) || !$this->sitemapData['include_blogs'] )
			return;
			
		$blogs = Settings::BlogsFullArray();
			
		if ( empty( $blogs ) )
			return;

		//Get ALL the languages
		$langs = Settings::AllLangs();

		$doc = new DOMDocument('1.0', 'UTF-8');

		//Non Friendly XML code
		$doc->formatOutput = false;

		// create urlset element
		$urlset = $doc->createElement("urlset");
		$url_node = $doc->appendChild( $urlset );

		//set attributes
		$url_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
		$url_node->setAttribute("xmlns:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9");
		$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");

		foreach( $langs as $key => $row )
		{
			$siteUrl = SITE_URL;

			if ( MULTILANG && ( !Settings::IsTrue( 'hide_default_lang_slug' ) || ( Settings::IsTrue( 'hide_default_lang_slug' ) && !$row['lang']['is_default'] ) ) )
				$siteUrl .= $row['lang']['code'] . PS;
				
			foreach( $blogs as $bKey => $blog )
			{
				//If we don't want this blog in sitemap, then skip it
				if ( $blog['hide_sitemap'] )
					continue;
				
				//Check if we have any group selected and skip this blog if guests are not allowed
				$groups = Json( $blog['groups_data'] );

				if ( !empty( $groups ) && ( !in_array( $this->botId, $groups ) || !in_array( $this->guestsId, $groups ) ) )
					continue;
				
				//If this blog is not enabled for this language, skip it
				if ( ( $blog['id_lang'] > 0 ) && ( $blog['id_lang'] != $row['lang']['id'] ) )
					continue;
			
				$index_url = $doc->createElement( 'url' );
				$index_url->appendChild( $doc->createElement( 'loc', $siteUrl . $bKey . PS ) );
				$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );

				if ( !empty( $this->sitemapData['blogs_priority'] ) )
					$index_url->appendChild( $doc->createElement( 'priority', $this->sitemapData['blogs_priority'] ) );					
		
				$urlset->appendChild($index_url);
			}
		}

		$filename = SitemapCacheFile ( 'sitemap-blogs.xml' );

		//Add this file into the array
		$this->BuildSitemapArray( 'sitemap-blogs.xml' );
			
		return $doc->save( $filename );
	}
	
	#####################################################
	#
	# Sitemap Lang List function
	#
	#####################################################
	private function SitemapLangList()
	{
		if ( !MULTILANG || !isset( $this->sitemapData['include_langs'] ) || !$this->sitemapData['include_langs'] )
			return;

		//Get any other lang this site may have
		$langs = Settings::Langs();

		//If we don't have any other langs, then there is no point to continue...
		if ( empty( $langs ) )
			return;

		$doc = new DOMDocument('1.0', 'UTF-8');

		//Non Friendly XML code
		$doc->formatOutput = false;

		// create urlset element
		$urlset = $doc->createElement("urlset");
		$url_node = $doc->appendChild( $urlset );

		//set attributes
		$url_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
		$url_node->setAttribute("xmlns:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9");
		$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
			
		foreach( $langs as $id => $row )
		{
			//Add the lang's code into the URL
			$siteUrl = SITE_URL . $row['lang']['code'] . PS;

			$index_url = $doc->createElement( 'url' );
			$index_url->appendChild( $doc->createElement( 'loc', $siteUrl  ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );
				
			if ( !empty( $this->sitemapData['langs_priority'] ) )
				$index_url->appendChild( $doc->createElement( 'priority', $this->sitemapData['langs_priority'] ) );		

			$urlset->appendChild($index_url);
		}
			
		$filename = SitemapCacheFile ( 'sitemap-langs.xml' );
			
		//Add this file into the array
		$this->BuildSitemapArray( 'sitemap-langs.xml' );

		return $doc->save( $filename );
	}
	
	#####################################################
	#
	# Sitemap Home function
	#
	#####################################################
	private function SitemapHome()
	{
		if ( !isset( $this->sitemapData['include_homepage'] ) || !$this->sitemapData['include_homepage'] )
			return;
			
		$doc = new DOMDocument('1.0', 'UTF-8');

		//Non Friendly XML code
		$doc->formatOutput = false;
				
		// create urlset element
		$urlset = $doc->createElement("urlset");
		$url_node = $doc->appendChild( $urlset );

		//set attributes
		$url_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
		$url_node->setAttribute("xmlns:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9");
		$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
			
		$siteUrl = SITE_URL . ( !Settings::IsTrue( 'hide_default_lang_slug' ) ? $this->lang['lang']['code'] . PS : '' );

		$index_url = $doc->createElement( 'url' );
		$index_url->appendChild( $doc->createElement( 'loc', $siteUrl ) );
		$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );		

		$urlset->appendChild($index_url);
			
		$filename = SitemapCacheFile ( 'sitemap-home.xml' );
			
		//Add this file into the array
		$this->BuildSitemapArray( 'sitemap-home.xml' );
			
		return $doc->save( $filename );
}

	#####################################################
	#
	# Sitemap Categories List function
	#
	#####################################################
	private function SitemapCategoriesList()
	{
		if ( !isset( $this->sitemapData['include_categories'] ) || !$this->sitemapData['include_categories'] )
			return;
			
		$categories = Cats();
			
		if ( empty( $categories ) )
			return;

		$doc = new DOMDocument('1.0', 'UTF-8');
				
		//Non Friendly XML code
		$doc->formatOutput = false;
				
		// create urlset element
		$urlset = $doc->createElement("urlset");
		$url_node = $doc->appendChild( $urlset );

		//set attributes
		$url_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
		$url_node->setAttribute("xmlns:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9");
		$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");
			
		foreach( $categories as $lKey => $row )
		{
			foreach( $row as $key => $cats )
			{
				foreach( $cats as $id => $cat )
				{
					if ( !empty( $cat['groups'] ) && ( !in_array( $this->botId, $cat['groups']  ) || !in_array( $this->guestsId, $cat['groups'] ) ) )
						continue;
			
					//Categories have already the url, so we don't need to build it again
					$index_url = $doc->createElement( 'url' );
					$index_url->appendChild( $doc->createElement( 'loc', $cat['url'] ) );
					$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );

					if ( !empty( $this->sitemapData['categories_priority'] ) )
						$index_url->appendChild( $doc->createElement( 'priority', $this->sitemapData['categories_priority'] ) );				

					$urlset->appendChild( $index_url );
						
					if ( !empty( $cat['childs'] ) )
					{
						foreach( $cat['childs'] as $cid => $chCat )
						{
							if ( !empty( $chCat['groups'] ) && ( !in_array( $this->botId, $chCat['groups']  ) || !in_array( $this->guestsId, $chCat['groups'] ) ) )
								continue;
					
							$index_url = $doc->createElement( 'url' );
							$index_url->appendChild( $doc->createElement( 'loc', $chCat['url'] ) );
							$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );

							if ( !empty( $this->sitemapData['categories_priority'] ) )
								$index_url->appendChild( $doc->createElement( 'priority', $this->sitemapData['categories_priority'] ) );				

							$urlset->appendChild( $index_url );
						}
					}
				}
			}
		}

		$filename = SitemapCacheFile ( 'sitemap-categories.xml' );
			
		//Add this file into the array
		$this->BuildSitemapArray( 'sitemap-categories.xml' );
			
		return $doc->save( $filename );
	}
	
	#####################################################
	#
	# Sitemap Tags List function
	#
	#####################################################
	private function SitemapTagsList()
	{
		if ( !isset( $this->sitemapData['include_tags'] ) || !$this->sitemapData['include_tags'] )
			return;
		
		//Query: tags
		$tags = $this->db->from( null, "
		SELECT t.id_lang, t.sef, la.code as ls
		FROM `" . DB_PREFIX . "tags` as t
		INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = t.id_lang
		WHERE (t.id_site = " . SITE_ID . ")"
		)->all();

		if ( !$tags )
			return;
			
		$doc = new DOMDocument('1.0', 'UTF-8');

		//Non Friendly XML code
		$doc->formatOutput = false;

		// create urlset element
		$urlset = $doc->createElement("urlset");
		$url_node = $doc->appendChild( $urlset );

		//set attributes
		$url_node->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
		$url_node->setAttribute("xmlns:schemaLocation","http://www.sitemaps.org/schemas/sitemap/0.9");
		$url_node->setAttribute("xmlns","http://www.sitemaps.org/schemas/sitemap/0.9");

		foreach( $tags as $tag )
		{
			//Build the TAG url
			$url = SITE_URL;

			//Add the lang slug
			if ( MULTILANG && !empty( $tag['ls'] ) && ( !Settings::IsTrue( 'hide_default_lang_slug' ) || ( Settings::IsTrue( 'hide_default_lang_slug' ) && ( $tag['ls'] != $this->lang['lang']['code'] ) ) ) )
				$url .= $tag['ls'] . PS;
				
			//Add the tag slug
			$url .= $tag['sef'] . PS;
				
			//Append this tag to the doc
			$index_url = $doc->createElement( 'url' );
			$index_url->appendChild( $doc->createElement( 'loc', $url ) );
			$index_url->appendChild( $doc->createElement( 'lastmod', date( 'c', time() ) ) );

			if ( !empty( $this->sitemapData['tags_priority'] ) )
				$index_url->appendChild( $doc->createElement( 'priority', $this->sitemapData['tags_priority'] ) );				

			$urlset->appendChild($index_url);
		}

		$filename = SitemapCacheFile ( 'sitemap-tags.xml' );
			
		//Add this file into the array
		$this->BuildSitemapArray( 'sitemap-tags.xml' );
			
		return $doc->save( $filename );
	}
	
	#####################################################
	#
	# Load Sitemap function
	#
	#####################################################
	public function LoadSitemap()
	{
		if ( !$this->sitemapFile )
		{
			$sitemapDb = OpenFileDB( SITEMAP_ARRAY_FILE );
			
			$slug = str_replace( '/', '', Router::GetVariable( 'slug' ) );

			//Sorry, but we have nothing for you
			if ( empty( $sitemapDb ) || !in_array( $slug, $sitemapDb ) )
			{
				return false;
			}

			$cacheFile = SitemapCacheFile( $slug );
		}
		
		else
		{
			$cacheFile = SitemapCacheFile( $this->sitemapFile );
		}

		// If the file don't exists create a new file of each one
		// ValidCache function makes sure that we will get a fresh copy in case something went wrong with pinging
		if ( !ValidCache( $cacheFile ) )
			$this->BuildSitemap();

		//If the file still doesn't exist, then we can do nothing about it
		if ( !file_exists( $cacheFile ) )
		{
			return false;
		}

		header( 'Content-type: text/xml' );

		LoadXmlFile( $cacheFile );
		
		exit( 0 );
	}

	#####################################################
	#
	# Sitemap Pages List function
	#
	#####################################################
	function SitemapPagesList()
	{
		if ( MULTIBLOG )
			$this->SitemapBlogPageList();
			
		$this->SitemapPageList();
	}
	
	#####################################################
	#
	# Sitemap Posts List function
	#
	#####################################################
	private function SitemapPostsList()
	{
		if ( MULTIBLOG )
			$this->SitemapBlogPostList();
			
		$this->SitemapPostList();
	}
	
	#####################################################
	#
	# Sitemap Array function
	#
	#####################################################
	private function SitemapArray()
	{
		if ( !empty( $this->sitemapArray ) )
			WriteFileDB( $this->sitemapArray, SITEMAP_ARRAY_FILE );
	}
	
	#####################################################
	#
	# Build Sitemap function
	#
	#####################################################
	public function BuildSitemap()
	{
		$this->SitemapVideoList();
		$this->SitemapBlogList();
		$this->SitemapLangList();
		$this->SitemapPagesList();
		$this->SitemapPostsList();
		$this->SitemapCategoriesList();
		$this->SitemapTagsList();
		$this->SitemapIndexFile();
		$this->SitemapHome();
		$this->SitemapNews();

		//Save the array into the DB
		$this->SitemapArray();
	}
	
	#####################################################
	#
	# Sitemap Count Posts function
	#
	#####################################################
	private function CountSiteMapPosts( $blogId = null, $type = 'posts', $langId = null )
	{
		$t = ( ( $type == 'posts' ) ? 'post' : ( ( $type == 'pages' ) ? 'page' : 'post' ) );
		
		$q = "(p.id_site = " . SITE_ID . ")" . ( $langId ? " AND (p.id_lang = " . $langId . ")" : "" ) . ( $blogId ? " AND (p.id_blog = " . $blogId . ")" : "" ) . " AND (p.post_type = '" . $t . "') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";
		
		$count = $this->db->from( null, "SELECT count(p.id_post) as total FROM `" . DB_PREFIX . POSTS . "` as p LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post WHERE 1=1 AND " . $q )->total();

		return $count;
	}
	
	#####################################################
	#
	# News Sitemap Posts function
	#
	#####################################################
	private function GetSiteMapNewsPosts( $langId )
	{
		$cacheFile = CacheFileName( 'sitemap_news', null, $langId );
		
		//Get the data from the cache, if is valid
		if ( ValidOtherCache( $cacheFile ) )
		{
			$data = ReadCache( $cacheFile );
		}
		
		//Get the data and save it to the cache, if needed...
		else
		{
			$types = $this->sitemapNewsData['content_types'];
			
			$blogQ = ( $this->sitemapNewsData['include_orphan'] ? "" : " AND (p.id_blog != 0)" );
			
			$time  = ( time() - 172800 ); //-2 days
			
			$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $langId . ")" . $blogQ . " AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL) AND (p.added_time > " . $time . ")";
				
			$q .= ' AND (';

			if ( is_array( $types ) && !empty( $types ) )
			{
				if ( in_array( 'posts', $types ) && !in_array( 'pages', $types ) )
					$q .= "p.post_type = 'post'";
				
				elseif ( in_array( 'pages', $types ) && !in_array( 'posts', $types ) )
					$q .= "p.post_type = 'page'";
					
				else
					$q .= "p.post_type = 'page' OR p.post_type = 'post'";
			}
			
			else
			{
				if ( $types == 'posts' )
					$q .= "p.post_type = 'post'";
				
				elseif ( $types == 'pages' )
					$q .= "p.post_type = 'page'";
					
				//Just in case...
				else
					$q .= "p.post_type = 'post'";
			}

			$q .= ')';

			$query = PostsDefaultQuery( $q, 1000, 'p.added_time ASC', 'p.id_post' );
			
			//Query: posts
			$tmp = $this->db->from( null, $query )->all();
			
			if ( empty( $tmp ) )
			{
				$log = Settings::LogSettings();
				
				if ( !empty( $log ) && $log['enable_error_log'] && $log['enable_not_found_log'] )
				{
					$errorMessage = 'Sitemap News posts couldn\'t be fetched (Lang: ' . $langId . ')';
					
					if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
					{
						$errorData = 'Query: ' . PHP_EOL . $query;
					}
				
					Log::Set( $errorMessage, $errorData, $param, 'system' );
				}
				
				return null;
			}

			$i = 0;

			foreach ( $tmp as $p )
			{				
				$data[$i] = BuildPostVars( $p );
				
				$i++;
			}
			
			WriteOtherCacheFile( $data, $cacheFile );
		}
		
		return $data;
	}
	
	#####################################################
	#
	# Sitemap Posts function
	#
	#####################################################
	private function GetSiteMapPosts( $blogId = null, $type = 'posts', $langId = null, $loadExtras = false, $items = null, $from = null )
	{
		$numItems 	= ( $items ? $items : 1000 );
		
		$items 		= ( $from ? $from . ', ' : '' ) . $numItems;
		
		$t = ( ( $type == 'posts' ) ? 'post' : ( ( $type == 'pages' ) ? 'page' : 'post' ) );
		
		$q = "(p.id_site = " . SITE_ID . ")" . ( $langId ? " AND (p.id_lang = " . $langId . ")" : "" ) . ( $blogId ? " AND (p.id_blog = " . $blogId . ")" : "" ) . " AND (p.post_type = '" . $t . "') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";

		$query = PostsDefaultQuery( $q, $items, 'p.added_time ASC', 'p.id_post' );
			
		//Query: posts
		$tmp = $this->db->from( null, $query )->all();
		
		if ( empty( $tmp ) )
		{
			$log = Settings::LogSettings();
				
			if ( !empty( $log ) && $log['enable_error_log'] && $log['enable_not_found_log'] )
			{
				$errorMessage = 'Sitemap posts couldn\'t be fetched (Lang: ' . $langId . ', Blog: ' . $blogId . ')';
					
				if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
				{
					$errorData = 'Query: ' . PHP_EOL . $query;
				}
				
				Log::Set( $errorMessage, $errorData, $param, 'system' );
			}
				
			return null;
		}
		
		$data = array();
		
		$i = 0;
			
		foreach ( $tmp as $p )
		{				
			$data[$i] = BuildPostVars( $p );
				
			//To build the tables, we have to ask for more data
			if ( !empty( $loadExtras ) )
			{
				$data['posts'][$i]['xtraData'] = GetDataXtraPost( $p['id_post'] );
			}

			unset( $p );

			$i++;
		}

		return $data;
	}
	
	#####################################################
	#
	# Build Sitemap Array function
	#
	#####################################################
	private function BuildSitemapArray( $key )
	{
		if ( !empty( $key ) && !in_array( $key, $this->sitemapArray ) )
			array_push( $this->sitemapArray, $key );
	}
	
	//Get the videoBlog Id
	private function SitemapVideoBlog()
	{
		$parr = Settings::Get()['parent_type'];
		
		if ( $parr == 'videos' )
			return 0;
		
		$sett = Settings::Get()['extra_blogs'];
		
		if ( empty( $sett ) || !isset( $sett['types']['videos'] ) || empty( $sett['types']['videos']['blogId'] ) )
			return null;
			
		return $sett['types']['videos']['blogId'];
	}
}