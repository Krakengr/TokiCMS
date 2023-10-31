<?php defined('TOKICMS') or die('Hacking attempt...');

class Feed extends Controller {
	
	private $isBlog 	 = false;
	private $blogId 	 = 0;
	private $blogKey 	 = null;
	private $rssSettings = null;
	private $blogName	 = null;
	private $blogDescr	 = null;
	private $blogSlogan	 = null;
	
    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( !IsAllowedTo( 'view-site' ) )
		{
			//Don't include this file while on login or register
			if ( ( Router::WhereAmI() != 'login' ) || ( Router::WhereAmI() != 'register' ) )
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );

			$this->view();
			return;
		}
		
		if ( !Settings::IsTrue( 'enable_rss' ) )
		{
			Router::SetNotFound();

			$this->view();
			return;
		}

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
		header("Content-Type: text/xml; charset=utf-8", true);
		
		if ( Router::GetVariable( 'isBlog' ) && !$this->GetBlog() )
		{
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
				
			$this->view();
			return;
		}
	
		$settings = Json( Settings::Get()['rss_settings'] );
			
		$this->rssSettings = ( isset( $settings['lang-' . $this->lang['lang']['id']]['blog-' . $this->blogId] ) ?
							$settings['lang-' . $this->lang['lang']['id']]['blog-' . $this->blogId]['data'] : null );

		$data = $this->GetPosts();

		if ( empty( $data ) )
		{
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
			
			$this->view();
			return;
		}
		
		$currUrl  = ( $this->isBlog ? $this->BlogUrl() : Router::GetVariable( 'siteRealUrl' ) );
		
		$currUrl .= 'feed' . PS;
		
		//New XML File
		$doc = new DOMDocument('1.0', 'UTF-8');

		// Friendly XML code
		$doc->formatOutput = true;

		//create "RSS" element
		$rss = $doc->createElement("rss");
		$rss_node = $doc->appendChild($rss); //add RSS element to XML node
		$rss_node->setAttribute("version","2.0"); //set RSS version
		$rss_node->setAttribute("xmlns:atom","http://www.w3.org/2005/Atom");
		$rss_node->setAttribute("xmlns:content","http://purl.org/rss/1.0/modules/content/");
		
		$channel = $doc->createElement("channel");  
		$channel_node = $rss_node->appendChild($channel);

		$channel_node->appendChild($doc->createElement("title", htmlspecialchars( $this->lang['data']['site_name'] . ( $this->isBlog ? ' [' . $this->blogName . ']' : '' ), ENT_QUOTES ) ) );
		
		$channel_node->appendChild($doc->createElement("link", Router::GetVariable( 'siteRealUrl' ) ) );
		
		$channel_node->appendChild($doc->createElement("description", htmlspecialchars( ( $this->isBlog ? $this->blogDescr : $this->lang['data']['site_description'] ), ENT_QUOTES ) ) );
		
		$channel_node->appendChild($doc->createElement("language", $this->lang['lang']['locale'] ));
		
		$atom = $doc->createElement("atom:link"); //add atom element inside the channel element
		$rss_atom = $channel_node->appendChild($atom);
		$rss_atom->setAttribute("rel","self");
		$rss_atom->setAttribute("type","application/rss+xml");
		$rss_atom->setAttribute("href",$currUrl);

		$search = array( '{{author-name}}', '{{author-link}}', '{{post-title}}', '{{post-link}}', '{{site-title}}', '{{site-slogan}}', '{{site-description}}', '{{site-link}}' );

		foreach( $data as $post )
		{
			$item_node = $channel_node->appendChild($doc->createElement("item"));
			
			$postContent = '';
			
			$postUrl = '<a rel="self" href="' . $post['postUrl'] . '">' . $post['title'] . '</a>';
			$siteUrl = '<a href="' . Router::GetVariable( 'siteRealUrl' ) . '">' . $this->lang['data']['site_name'] . '</a>';
			$userUrl = '<a href="' . $post['author']['url'] . '">' . $post['author']['name'] . '</a>';
			
			$replace = array( $post['author']['name'], $userUrl, $post['title'], $postUrl, $this->lang['data']['site_name'], $this->lang['data']['site_description'], $this->lang['data']['site_slogan'], $siteUrl );
			
			if ( $this->rssSettings && !empty( $this->rssSettings['header_code'] ) )
			{
				$postContent .= '<p>' . trim( str_replace( $search, $replace, html_entity_decode( $this->rssSettings['header_code'] ) ) ) . '</p>';
			}
			
			if ( !empty( $post['coverImage'] ) && !empty( $post['coverImage']['default'] ) )
				$postContent .= '<a rel="self" href="' . $post['postUrl'] . '"><img vspace="4" hspace="4" border="0" align="right" src="' . $post['coverImage']['default']['imageUrl'] . '"></a>';
			
			$postContent .= '<p>' . htmlspecialchars( ( Settings::IsTrue( 'show_full_post_rss' ) ? $post['content'] : $post['description'] ), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) . '</p>';
			
			if ( $this->rssSettings && !empty( $this->rssSettings['footer_code'] ) )
			{
				$postContent .= '<p>' . trim( str_replace( $search, $replace, html_entity_decode( $this->rssSettings['footer_code'] ) ) ) . '</p>';
			}

			$item_node->appendChild($doc->createElement( "title", htmlspecialchars( $post['title'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) ) );
			$item_node->appendChild($doc->createElement("link", $post['postUrl'] ));
			$item_node->appendChild($doc->createElement("description", $postContent ));
			$item_node->appendChild($doc->createElement("pubDate", $post['added']['r'] ) );
			$guid = $item_node->appendChild($doc->createElement("guid", $post['postUrl'] ));
			$guid->setAttribute("isPermaLink","true");
		}
		
		unset( $data );

		echo $doc->saveXML();

		exit(0);
	}
	
	private function GetPosts()
	{
		$numItems = ( ( $this->rssSettings && is_numeric( $this->rssSettings['post_limit'] ) ) ? $this->rssSettings['post_limit'] : ( !empty( HOMEPAGE_ITEMS ) ? HOMEPAGE_ITEMS : 20 ) );
		
		$cacheFile = CACHE_ROOT . 'content' . DS . 'feed-items_' . $numItems . '-langid_' . $this->lang['lang']['id'] . ( $this->isBlog ? '-blogid_' . $this->blogId : '' ) . '-siteid_' . SITE_ID;
		
		$cacheFile .= '-' . sha1( $cacheFile . CACHE_HASH ) . '.php';
		
		//Get the data from the cache, if is valid
		if ( ValidOtherCache( $cacheFile ) )
		{
			$data = ReadCache( $cacheFile );
			
			$this->items = $data['totalItems'];
		}
		
		//Get the data and save it to the cache, if needed...
		else
		{
			$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $this->lang['lang']['id'] . ") AND (p.id_blog = " . $this->blogId . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";
			
			$query = PostsDefaultQuery( $q, $numItems, 'p.added_time DESC', 'p.id_post' );
			
			//Query: posts
			$tmp = $this->db->from( null, $query )->all();

			if ( empty( $tmp ) )
			{
				$log = Settings::LogSettings();
				
				if ( !empty( $log ) && $log['enable_error_log'] && $log['enable_not_found_log'] )
				{
					$errorMessage = 'Feed posts couldn\'t be fetched';
					
					if ( isset( $log['include_database_query'] ) && $log['include_database_query'] )
					{
						$errorData = 'Query: ' . PHP_EOL . $query;
					}
				
					Log::Set( $errorMessage, $errorData, $param, 'system' );
				}
				
				return null;
			}
			
			// Query: total
			$this->items = $this->db->from( null, "SELECT count(p.id_post) as total FROM `" . DB_PREFIX . POSTS . "` as p LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post WHERE 1=1 AND " . $q )->total();
			
			$data = array(
				'posts' 		=> array(),
				'totalItems' 	=> $this->items
			);
			
			$i = 0;
			
			foreach ( $tmp as $p )
			{				
				$data['posts'][$i] 			= BuildPostVars( $p );
				$data['posts'][$i]['tags'] 	= GetPostTags( $p );

				unset( $p );
				
				$i++;
			}

			WriteOtherCacheFile( $data, $cacheFile );
		}
		
		if ( !empty( $data['posts'] ) )
		{
			return $data['posts'];
		}
	
		return null;
	}
	
	private function GetBlog()
	{
		if ( !Router::GetVariable( 'isBlog' ) )
			return;
		
		$this->blogKey 	= Sanitize( Router::GetVariable( 'blogKey' ), false );
		
		$this->blog 	= GetBlog( $this->blogKey, null, SITE_ID, $this->lang['lang']['id'] );

		if ( empty( $this->blog ) )
		{
			$log = Settings::LogSettings();
				
			if ( !empty( $log ) && $log['enable_error_log'] && $log['enable_not_found_log'] )
			{
				$errorMessage = 'Blog data couldn\'t be fetched (Key: ' . $this->blogKey . ', Lang: ' . $this->lang['lang']['id'] . ')';

				Log::Set( $errorMessage, $errorData, $param, 'system' );
			}
				
			return false;
		}	
		
		//Check if we have selected to redirect this whole blog
		if ( !empty( $this->blog['redirect'] ) )
		{
			@header("Location: " . $this->blog['redirect'], true, 301 );
			@exit;
		}
		
		if ( !empty( $this->blog['groups'] ) )
		{
			if ( !IsAllowedTo( 'admin-site' ) && !in_array( $this->userGroup, $this->blog['groups'] ) )
			{
				$this->noAccess = true;
				return false;
			}
		}
		
		//If this blog has translation data, set its correct values
		if ( !empty( $this->blog['trans'] ) && !empty( $this->blog['trans'][$this->lang['lang']['code']] ) )
		{
			$this->blogName   = $Blog['name'] 		 = $this->blog['trans'][$this->lang['lang']['code']]['name'];
			$this->blogDescr  = $Blog['description'] = $this->blog['trans'][$this->lang['lang']['code']]['description'];
			$this->blogSlogan = $Blog['slogan'] 	 = $this->blog['trans'][$this->lang['lang']['code']]['slogan'];
		}
		
		else
		{
			$this->blogName   = $this->blog['name'];
			$this->blogDescr  = $this->blog['description'];
			$this->blogSlogan = $this->blog['slogan'];
		}
		
		$this->blogId = $this->blog['id_blog'];
		
		$this->isBlog = true;

		return true;
	}
	
	#####################################################
	#
	# Builds the slug based on language settings function
	#
	#####################################################
	private function BlogUrl()
	{
		$url = SITE_URL;

		$defaultLang = Settings::LangData()['lang']['code'];
		$langCode 	 = $this->lang['lang']['code'];
		
		//Add the lang slug
		if ( MULTILANG && ( !Settings::IsTrue( 'hide_default_lang_slug' ) || 
		( Settings::IsTrue( 'hide_default_lang_slug' ) && ( $langCode != $defaultLang ) ) ) )
		{
			$url .= $langCode . PS;
		}
		
		$url .= $this->blog['sef'] . PS;

		return $url;
	}
}