<?php defined('TOKICMS') or die('Hacking attempt...');

class Content extends Controller
{
	private $userGroup;
	private $data 		= [];
	private $themeData  = [];
	private $blog;
	private $slug;
	private $pageKey;
	private $headerCode	= '';
	private $noAccess  	= false;
	private $noIndex   	= false;
	private $noFollow  	= false;
	private $noImage   	= false;
	private $noodp     	= false;
	private $noSnippet 	= false;
	private $noArchive 	= false;
	private $blogName	= null;
	private $blogDescr	= null;
	private $blogSlogan	= null;
	
    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( !IsAllowedTo( 'view-site' ) )
		{
			//Don't include this file while on login or register
			if ( Router::WhereAmI() != 'login' )
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );

			$this->view();
			return;
		}
		
		//$AuthUser = $this->getVariable( 'AuthUser' );
		
		$this->userGroup = UserGroup();
		
		//Check if there is a blog
		if ( Router::GetVariable( 'isBlog' ) && !$this->GetBlog() )
		{
			$this->SetNoAccess();
		}
		
		$this->slug 	= Sanitize( Router::GetVariable( 'slug' ), false );
		
		$this->pageKey 	= Sanitize( Router::GetVariable( 'pageKey' ), false );
		
		//Try to load the post
		if ( !$this->GetSinglePost() )
		{
			//Check if we have any redirecion before show the error page
			$this->CheckRedirects();

			$this->SetNoAccess();
		}
		
		//Set a few more values for the header
		Theme::SetVariable( 'data', 	  $this->themeData );
		Theme::SetVariable( 'noIndex', 	  $this->noIndex );
		Theme::SetVariable( 'noFollow',   $this->noFollow );
		Theme::SetVariable( 'noImage', 	  $this->noImage );
		Theme::SetVariable( 'noodp', 	  $this->noodp );
		Theme::SetVariable( 'noSnippet',  $this->noSnippet );
		Theme::SetVariable( 'noArchive',  $this->noArchive );
		Theme::Build();
		
		$this->view();
	}
	
	private function GetContent()
	{
		try
		{
			$post 				= new GetPost;
			$post->lang 		= $this->lang;
			$post->siteId 		= SITE_ID;
			$post->amp 			= Router::GetVariable( 'isAmp' );
			$post->slug 		= $this->slug;
			$post->cache 		= true;
			$post->addPostViews = true;
			$this->data 		= $post->GetPost();
			
			unset( $post );
		}
		
		catch( Exception $e )
		{
		  //
		}

		/*
		$cacheFile = PostCacheFile( $this->slug, null, $this->lang['lang']['code'], Router::GetVariable( 'isAmp' ), ( Router::GetVariable( 'isAmp' ) ? null : Settings::Get()['theme'] ) );
		
		if ( ValidOtherCache( $cacheFile ) )
		{
			$this->data = ReadCache( $cacheFile );
			UpdateFilePostViews ( $this->data['id'] );
		}
		
		else
		{
			$query = PostDefaultQuery( "(p.id_site = " . SITE_ID . ") AND (p.sef = :sef) AND (p.post_type = 'post' OR p.post_type = 'page') AND (p.post_status = 'published') AND (b.disabled = 0 OR b.disabled IS NULL)" );
			
			$binds = array( $this->slug => ':sef' );
			
			//Query: post
			$tmp = $this->db->from( null, $query, $binds )->single();
			
			if ( empty( $tmp ) )
			{
				return;
			}

			$s = GetSettingsData( $tmp['id_site'] );
		
			if ( empty( $s ) )
			{
				return null;
			}
			
			$tmp = array_merge( $tmp, $s );
				
			UpdatePostViews( $tmp['id_post'] );
			
			$this->data = BuildFullPostVars( $tmp );

			//Create the header code
			$this->HeaderCode();
			
			//We need to check if there is any Schema for this post and add it to the cache
			$this->data['schemaCode'] = $this->CheckPostSchema();
		
			//Also cache the header code
			$this->data['headerCode'] = $this->headerCode;
			
			WriteOtherCacheFile( $this->data, $cacheFile, true );

			unset( $tmp );
		}
		
		if ( !empty( $this->data['headerCode'] ) )
		{
			Theme::SetVariable( 'postHeader', $this->data['headerCode'] );
		}
		
		return true;*/
	}
	
	private function GetSinglePost()
	{
		global $Post;
		
		$this->GetContent();

		if ( empty( $this->data ) )
			return false;
		
		//If we have added an external URL for this post, then let's redirect it there
		if ( !empty( $this->data['externalUrl'] ) )
		{
			@header("Location: " . $this->data['externalUrl'], true, 301 );
			@exit;
		}

		//Don't continue if this group has no access here
		if ( !IsAllowedTo( 'admin-site' ) )
		{
			if ( !empty( $this->data['catGroups'] ) || !empty( $this->data['blogGroups'] ) || !empty( $this->data['subCatGroups'] ) )
			{
				if ( !empty( $this->data['catGroups'] ) )
				{
					if ( !empty( $this->userGroup ) && !in_array( $this->userGroup, $this->data['catGroups'] ) )
					{
						$this->noAccess = true;
						return false;
					}
				}
					
				if ( !empty( $this->data['subCatGroups'] ) )
				{
					if ( !empty( $this->userGroup ) && !in_array( $this->userGroup, $this->data['subCatGroups'] ) )
					{
						$this->noAccess = true;
						return false;
					}
				}
				
				if ( !empty( $this->data['blogGroups'] ) )
				{
					if ( !empty( $this->userGroup ) && !in_array( $this->userGroup, $this->data['blogGroups'] ) )
					{
						$this->noAccess = true;
						return false;
					}
				}
			}
		}
		
		//Add the header code
		if ( !empty( $this->data['headerCode'] ) )
		{
			Theme::SetVariable( 'postHeader', $this->data['headerCode'] );
		}

		//Add content after post
		if ( !empty( $this->lang['data']['after_content_text'] ) )
		{
			$content = '<p>' . StripContent( $this->lang['data']['after_content_text'] ) . '</p>';
			
			$this->data['content']  .= $content;
			$this->data['post'] 	.= $content;
		}

		//If we have set a static page as front page and this is the page, we have to redirect the user there
		$this->CheckHomePage();
		
		//Check if the page has a parent and send the user there
		$this->CheckParent();
		
		//Check if the post has a redirection and send the user there
		$this->CheckPostRedirection();
		
		//Check if the post has not AMP mode enabled
		$this->CheckPostAmpStatus();
		
		//We need to check if this post has the correct url
		$this->CheckPostUrl();
		
		//Check if we have to serve a lighter version or deny the access
		$this->IsAllowedToView();
		
		//Activate the Post Class
		$Post = new Post( $this->data );
		
		$this->setVariable( 'Post', $Post );

		//Check if we want Ads in post
		$this->InPostAds();
		
		//Check if we want a contact form
		$this->CheckContactFormPage();
		
		//Replace any [contact-form] in post.
		$this->CheckContactFormInPost();
		
		//Check if this post has noIndex
		$this->NoIndex();

		//Don't forget the header data
		$arr = array(
			'postUrl'   		=> $Post->Url(),
			'parentId'  		=> $Post->ParentId(),
			'postId' 			=> $Post->PostId(),
			'postTitle'			=> $Post->Title(),
			'postDate'			=> $Post->Added()->raw,
			'postDateC'			=> $Post->Added()->c,
			'postDescription'	=> $Post->Description(),
			'isPage'			=> $Post->IsPage(),
			'postAuthor'		=> $Post->Author()->name,
			'postTranslations'  => $Post->Translations(),
			'languageKey'		=> $Post->Language()->key,
			'languageName'		=> $Post->Language()->name,
			'categoryUrl'		=> ( $Post->IsPage() ? null : $Post->Category()->url ),
			'categoryId'		=> ( $Post->IsPage() ? null : $Post->Category()->id ),
			'categoryName'		=> ( $Post->IsPage() ? null : $Post->Category()->name ),
			'hasCoverImage'		=> $Post->HasCoverImage(),
			'coverImage'		=> ( !empty( $Post->Cover()['default'] ) ? $Post->Cover()['default'] : null ),
			'schemaCode'		=> $this->data['schemaCode']
		);
		
		$this->themeData = array_merge( $this->themeData, $arr );
		
		unset( $arr, $this->data, $Post );

		return true;
	}
	
	//Check if this URL is correct
	private function CheckParent()
	{
		if ( ( $this->data['postType'] !== 'page' ) || empty( $this->data['pageParentId'] ) || empty( $this->data['pageParentSef'] ) )
		{
			return;
		}

		//Check if the URL is correct
		if ( $this->data['pageParentSef'] !== $this->pageKey )
		{
			@header("Location: " . $this->data['postUrl'], true, 301 );
			@exit;
		}
	}
	
	//Get the blog
	private function GetBlog()
	{
		global $Blog;
		
		if ( !Router::GetVariable( 'isBlog' ) )
			return;
		
		$this->blog = GetBlog( Router::GetVariable( 'blogKey' ), null, SITE_ID, $this->lang['lang']['id'] );

		if ( empty( $this->blog ) )
		{
			$log = Settings::LogSettings();
				
			if ( !empty( $log ) && !empty( $log['enable_error_log'] ) && !empty( $log['enable_not_found_log'] ) )
			{
				$errorMessage = 'Blog data couldn\'t be fetched (Key: ' . Router::GetVariable( 'blogKey' ) . ', Lang: ' . $this->lang['lang']['id'] . ')';

				Log::Set( $errorMessage, $errorData, $param, 'system' );
			}

			return false;
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
			$this->blogName   			= $this->blog['trans'][$this->lang['lang']['code']]['name'];
			$this->blogDescr  			= $this->blog['trans'][$this->lang['lang']['code']]['description'];
			$this->blogSlogan 			= $this->blog['trans'][$this->lang['lang']['code']]['slogan'];

			$this->blog['name'] 		= $this->blogName;
			$this->blog['description'] 	= $this->blogDescr;
			$this->blog['slogan'] 	 	= $this->blogSlogan;
		}
		
		else
		{
			$this->blogName   = $this->blog['name'];
			$this->blogDescr  = $this->blog['description'];
			$this->blogSlogan = $this->blog['slogan'];
		}
		
		$this->themeData = array(
			'blogId' 			=> $this->blog['id_blog'],
			'blogName'			=> $this->blogName,
			'blogDescription'	=> $this->blogDescr,
			'blogSlogan'		=> $this->blogSlogan,
			'blogSef'			=> $this->blog['sef'],
			'blogPosts'			=> $this->blog['num_posts'],
			'blogComments'		=> $this->blog['num_comments'],
			'blogUrl'			=> $this->BlogUrl()
		);
		
		$Blog = $this->blog;
		
		return true;
	}
	/*
	private function CheckPostSchema()
	{
		$where = "(";
	
		$where .= " enable_on = '" . ( $this->data['postType'] == 'post'? 'all-posts' : 'all-pages' ) . "'";
		
		if ( MULTIBLOG && !empty( $this->data['blog']['id'] ) && ( $this->data['blog']['id'] > 0 ) )
			$where .= " OR ( enable_on = 'blog' AND enable_on_id = '" . $this->data['blog']['id'] . "' )";
		
		else
			$where .= " OR enable_on = '" . ( $this->data['postType'] == 'post' ? 'orphan-posts' : 'orphan-pages' ) . "'";
		
		if ( MULTILANG )
			$where .= " OR ( enable_on = 'lang' AND enable_on_id = '" . $this->data['language']['id'] . "' )";
		
		$where .= ")";
		
		$where .= " AND NOT (";
		
		$where .= " (exclude_from = '" . ( $this->data['postType'] == 'post'? 'all-posts' : 'all-pages' ) . "')";
		
		if ( MULTIBLOG && !empty( $this->data['blog']['id'] ) && ( $this->data['blog']['id'] > 0 ) )
			$where .= " OR (exclude_from = 'blog' AND exclude_from_id = '" . $this->data['blog']['id'] . "')";
		
		else
			$where .= " OR (exclude_from = '" . ( $this->data['postType'] == 'post'? 'orphan-posts' : 'orphan-pages' ) . "')";
		
		if ( MULTILANG )
			$where .= " OR (exclude_from = 'lang' AND exclude_from_id = '" . $this->data['language']['id'] . "')";
		
		$where .= ")";
		
		//Query: schemas
		$tmp = $this->db->from( null, "
		SELECT fixed_data
		FROM `" . DB_PREFIX . "schemas`
		WHERE (id_site = " . SITE_ID . ") AND " . $where
		)->all();
		
		if ( empty( $tmp ) )
		{
			return null;
		}
		
		$code = array();
		
		foreach ( $tmp as $t )
		{
			if ( empty( $t['fixed_data'] ) )
			{
				continue;
			}
			
			$code[] = $t['fixed_data'];
		}
		
		return $code;
	}
	
	private function HeaderCode()
	{
		$isAmp = Router::GetVariable( 'isAmp' );
		
		$headerCode = PHP_EOL . '<!-- Post Data -->' . PHP_EOL;

		//We are going to add the AMP link here
		if ( $this->data['hasAmp'] && !$isAmp )
		{
			$headerCode .= '<link rel="amphtml" href="' . $this->data['ampUrl'] . '">' . PHP_EOL;
		}

		if ( !empty( $this->data['previous'] ) )
		{
			$headerCode .= '<link rel="prev" title="' . htmlspecialchars($this->data['previous']['title'] ) . '" href="' . $this->data['previous']['postUrl'] . '" />' . PHP_EOL;
		}

		if ( !empty( $this->data['next'] ) )
		{
			$headerCode .= '<link rel="next" title="' . htmlspecialchars( $this->data['next']['title'] ) . '" href="' . $this->data['next']['postUrl'] . '" />' . PHP_EOL;
		}
			
		if ( !$this->data['isPage'] && !empty( $this->data['category'] ) )
			$headerCode .= '<meta property="article:section" content="' . htmlspecialchars( $this->data['category']['name'] ) . '" />' . PHP_EOL;

		if ( !$this->data['isPage'] && !empty( $this->data['subcategory'] ) )
			$headerCode .= '<meta property="article:section" content="' . htmlspecialchars( $this->data['subcategory']['name'] ) . '" />' . PHP_EOL;

		if ( !empty( $this->data['tags'] ) )
		{
			foreach ( $this->data['tags'] as $tag )
				$headerCode .= '<meta property="article:tag" content="' . htmlspecialchars( $tag['name'] ) . '" />' . PHP_EOL;
		}

		//Let's take care of our video
		if ( !empty( $this->data['video'] ) )
		{
			//If we have a video, add a few more lines here
			//Cuurently and for testing purposes, only Youtube is being supported
			$headerCode .= '<meta property="og:video" content="' . $this->data['video']['url'] . '" />' . PHP_EOL;
			$headerCode .= '<meta property="og:video:type" content="text/html" />' . PHP_EOL;
			$headerCode .= '<meta property="ya:ovs:upload_date" content="' .  $this->data['added']['r'] . '" />' . PHP_EOL;
			
			if ( isset( $this->data['video']['width'] ) && !empty( $this->data['video']['width'] ) )
			{
				$headerCode .= '<meta property="og:video:width" content="' . $this->data['video']['width'] . '" />' . PHP_EOL;
			}
			
			if ( isset( $this->data['video']['height'] ) && !empty( $this->data['video']['height'] ) )
			{
				$headerCode .= '<meta property="og:video:height" content="' . $this->data['video']['height'] . '" />' . PHP_EOL;
			}
			
			if ( isset( $this->data['video']['duration'] ) && !empty( $this->data['video']['duration'] ) )
			{
				$headerCode .= '<meta property="og:video:duration" content="' . $this->data['video']['duration'] . '" />' . PHP_EOL;
			}
			
			if ( isset( $this->data['video']['adult'] ) && !empty( $this->data['video']['adult'] ) )
			{
				$headerCode .= '<meta property="ya:ovs:adult" content="' . $this->data['video']['adult'] . '" />' . PHP_EOL;
			}
			
			if ( isset( $this->data['video']['allow_embed'] ) && !empty( $this->data['video']['allow_embed'] ) )
			{
				$headerCode .= '<meta property="ya:ovs:allow_embed" content="' . $this->data['video']['allow_embed'] . '" />' . PHP_EOL;
			}
		}

		$headerCode .= '<meta name="author" content="' . htmlspecialchars( $this->data['author']['name'] ) . '" />' . PHP_EOL;

		$headerCode .= '<meta property="article:published_time" content="' . $this->data['added']['r'] . '" />' . PHP_EOL;

		if ( !empty( $this->data['updated'] ) )
			$headerCode .= '<meta property="article:modified_time" content="' . $this->data['updated']['r'] . '" />' . PHP_EOL;
		
		$headerCode .= PHP_EOL;
		
		$this->headerCode = $headerCode;
		
		unset( $headerCode );
	}
	*/
	private function NoIndex()
	{
		$seoSettings = Settings::Seo();
		$Post 		 = $this->getVariable( 'Post' );
		
		//TODO: Test this value
		/*$whereAmI 	 = Router::WhereAmI();
		if ( ( $whereAmI == 'thread' ) && isset( $seoSettings['show_threads_search'] ) && IsFalse( $seoSettings['show_threads_search'] ) )
		elseif ( ( self::$variables['whereAmI'] == 'product' ) && isset( $seoSettings['show_products_search'] ) && IsFalse( $seoSettings['show_products_search'] ) )
		{
			$this->noIndex = true;
		}
		*/
		
		if ( ( $Post->PostType() == 'post' ) && isset( $seoSettings['show_posts_search'] ) && IsFalse( $seoSettings['show_posts_search'] ) )
		{
			$this->noIndex = true;
		}
		
		elseif ( ( $Post->PostType() == 'page' ) && isset( $seoSettings['show_pages_search'] ) && IsFalse( $seoSettings['show_pages_search'] ) )
		{
			$this->noIndex = true;
		}

		elseif ( ( $Post->PostType() == 'product' ) && IsFalse( $seoSettings['show_products_search'] ) )
		{
			$this->noIndex = true;
		}
		
		elseif ( ( $Post->PostType() == 'topic' ) && IsFalse( $seoSettings['show_threads_search'] ) )
		{
			$this->noIndex = true;
		}
		
		//Post specific settings
		else
		{
			$postSeo = $Post->ExtraData( 'seo' );

			if ( !empty( $postSeo['seo'] ) )
			{
				if ( isset( $postSeo['seo']['noindex'] ) && !empty( $postSeo['seo']['noindex'] ) )
				{
					$this->noIndex = true;
				}
							
				if ( isset( $postSeo['seo']['nofollow'] ) && !empty( $postSeo['seo']['nofollow'] ) )
				{
					$this->noFollow = true;
				}
							
				if ( isset( $postSeo['seo']['noimageindex'] ) && !empty( $postSeo['seo']['noimageindex'] ) )
				{
					$this->noImage = true;
				}
				
				if ( isset( $postSeo['seo']['noodp'] ) && !empty( $postSeo['seo']['noodp'] ) )
				{
					$this->noodp = true;
				}
							
				if ( isset( $postSeo['seo']['nosnippet'] ) && !empty( $postSeo['seo']['nosnippet'] ) )
				{
					$this->noSnippet = true;
				}
							
				if ( isset( $postSeo['seo']['noarchive'] ) && !empty( $postSeo['seo']['noarchive'] ) )
				{
					$this->noArchive = true;
				}
			}

			unset( $postSeo );
		}
	}
	
	private function CheckContactFormInPost()
	{
		$count = 0;
		
		$Post = $this->getVariable( 'Post' );
		
		$temp = $Post->Content();
		
		$html = ContactForm( false );

		//Contact Form
		$temp = preg_replace('/\[contact-form]/i', $html, $temp, -1, $count );
		
		if ( $count > 0 )
		{
			//Set the new content
			$Post->SetContent( $temp );
		
			//Overwrite the previous Variable
			$this->setVariable( 'Post', $Post );
		}
		
		unset( $temp, $Post, $html );
	}
	
	#####################################################
	#
	# Check if this page can have a contact form function
	#
	#####################################################
	private function CheckContactFormPage()
	{
		$Post = $this->getVariable( 'Post' );
		
		if ( !$Post->IsPage() )
		{
			return;
		}
		
		$settings = Settings::PrivacySettings();
		
		if ( empty( $settings ) || !isset( $settings['add_contact_form_to_contact_page'] ) || !$settings['add_contact_form_to_contact_page'] )
		{
			return;
		}
		
		$page = Json( Settings::Get()['contact_page'] );
	
		$code = CurrentLang()['lang']['code'];
		
		if ( empty( $page ) || !isset( $page[$code] ) || empty( $page[$code] ) )
		{
			return;
		}
		
		if ( $page[$code]['id'] != $Post->PostId() )
		{
			return;
		}
		
		$temp = $Post->Content() . PHP_EOL;
		
		$content = $temp . ContactForm( false );
	
		//Set the new content
		$Post->SetContent( $content );
		
		//Overwrite the previous Variable
		$this->setVariable( 'Post', $Post );

		//Free up some memory
		unset( $content, $settings, $page, $temp );
	}

	#####################################################
	#
	# Check and Correct a post's URL function
	#
	#####################################################
	public function CheckPostUrl()
	{
		if ( Router::GetVariable( 'isBlog' ) && !empty( $this->blog ) )
		{
			$blogId = $this->blog['id_blog'];
		}
		else
			$blogId = null;

		$redirect 	= false;

		$lang 		= Settings::LangData();

		$langId 	= $this->lang['lang']['id'];
		
		$postType 	= $this->data['postType'];
		
		$postUrl 	= $this->data['postUrl'];

		//If the post is from another language, send it there
		if ( MULTILANG && ( $this->data['language']['id'] != $langId ) )
		{
			$redirect = true;
		}

		//If the URL gives a blog, but the site has multiblog disabled, send it to the parent site
		//This shouldn't be ever happen, but better safe than sorry
		if ( !MULTIBLOG && Router::GetVariable( 'isBlog' ) )
		{
			$redirect = true;
		}

		//If the post belongs to a blog but we try to access it from somewhere else, send 'em there
		if ( MULTIBLOG && !empty( $this->data['blog']['id'] ) && ( empty( $blogId ) || ( $this->data['blog']['id'] != $blogId ) ) )
		{
			$redirect = true;
		}

		//If the post belongs to a blog, but the URL doesn't provide one, correct it now
		if ( MULTIBLOG && !empty( $this->data['blog']['id'] ) && !Router::GetVariable( 'isBlog' ) )
		{
			$redirect = true;
		}
		
		//What if we try to load a page inside a blog but this page doesn't belong there?
		if ( MULTIBLOG && ( empty( $this->data['blog'] ) || empty( $this->data['blog']['id'] ) ) && Router::GetVariable( 'isBlog' ) )
		{
			$redirect = true;
		}

		//If the post is being called as a page, correct it now
		if ( ( Settings::Get()['posts_filter'] !== '/' ) && ( $postType == 'post' ) && ( Router::GetVariable( 'postStatus' ) == 'page' ) )
		{
			$redirect = true;
		}

		//... the same goes for the pages, but not for the static page we set as homepage
		if ( ( Settings::Get()['posts_filter'] !== '/' ) && ( $postType == 'page' ) && ( Router::GetVariable( 'postStatus' ) == 'post' ) )
		{
			if ( !StaticHomePage( false, $this->data['id'] ) || !StaticHomePage( false, $this->data['parentId'] ) )
			{
				$redirect = true;
			}
		}

		if ( $redirect )
		{
			@header("Location: " . $postUrl, true, 301 );
			@exit;
		}
		
		return;
	}
	
	#####################################################
	#
	# Add the ads in post function
	#
	#####################################################
	private function InPostAds()
	{
		if ( !Settings::IsTrue( 'enable_ads' ) )
			return;
	
		$Post 	  = $this->getVariable( 'Post' );
		
		$adMiddle = GetAds( 'post-middle', 1, $Post->PostType() );
		$adTop 	  = GetAds( 'post-beginning', 1, $Post->PostType() );
		$adBottom = GetAds( 'post-end', 1, $Post->PostType() );
	
		$top = $bottom = '';
	
		if ( empty( $adTop ) && empty( $adMiddle ) && empty( $adBottom ) )
			return;
	
		if ( !empty( $adTop ) )
		{
			$top .= PHP_EOL . '<!--top ad-->' . PHP_EOL;

			$top .= '<div style="float:' . $adTop['align'] . ';margin:12px;' . ( ( $adTop['width'] > 0 ) ? 'width:' . $adTop['width'] . 'px;' : '' ) . ( ( $adTop['height'] > 0 ) ? 'height:' . $adTop['height'] . 'px;' : '' ) . ( ( $adTop['type'] == 'dummy' ) ? 'background-color:#33475b;' : '' ) . '">';
						
			if ( $adTop['type'] == 'plain-text' )
				$top .= $adTop['ad_code'];
								
			if ( $adTop['type'] == 'image' )
				$top .= '<img src="' . $adTop['img_url'] . '" align="' . $adTop['align'] . '" />';

			$top .= '</div>' . PHP_EOL;
		}
	
		if ( !empty( $adBottom ) )
		{
			$bottom .= PHP_EOL . '<!--top ad-->' . PHP_EOL;

			$bottom .= '<div style="float:' . $adBottom['align'] . ';margin:12px;' . ( ( $adBottom['width'] > 0 ) ? 'width:' . $adBottom['width'] . 'px;' : '' ) . ( ( $adBottom['height'] > 0 ) ? 'height:' . $adBottom['height'] . 'px;' : '' ) . ( ( $adBottom['type'] == 'dummy' ) ? 'background-color:#33475b;' : '' ) . '">';
						
			if ( $adBottom['type'] == 'plain-text' )
				$bottom .= $adBottom['ad_code'];

			if ( $adBottom['type'] == 'image' )
				$bottom .= '<img src="' . $adBottom['img_url'] . '" align="' . $adBottom['align'] . '" />';

			$bottom .= '</div>' . PHP_EOL;
		}
	
		$temp = $Post->Content();
	
		if ( !empty( $adMiddle ) )
		{
			$count = substr_count( $temp, '</p>' );

			if ( $count >= 6 )
			{
				$half = floor( $count / 2 );

				$content = explode( "</p>", $temp );

				$temp = '';

				$i = 0;

				foreach ( $content as $p ) 
				{
					if ( empty( $p ) )
						continue;

					$i++;

					if ( $i == $half )
					{
						$temp .= PHP_EOL . '<!--middle ad-->' . PHP_EOL;
							
						$temp .= '<div style="float:' . $adMiddle['align'] . ';margin:12px;' . ( ( $adMiddle['width'] > 0 ) ? 'width:' . $adMiddle['width'] . 'px;' : '' ) . ( ( $adMiddle['height'] > 0 ) ? 'height:' . $adMiddle['height'] . 'px;' : '' ) . ( ( $adMiddle['type'] == 'dummy' ) ? 'background-color:#33475b;' : '' ) . '">';
							
						if ( $adMiddle['type'] == 'plain-text' )
							$temp .= $adMiddle['ad_code'];

						if ( $adMiddle['type'] == 'image' )
							$temp .= '<img src="' . $adMiddle['img_url'] . '" align="' . $adMiddle['align'] . '" />';
									
						$temp .= '</div>' . PHP_EOL;
					}
					
					$temp .= trim( $p ) . '</p>' . PHP_EOL;
				}

				unset( $content );
			}
		}

		$content = $top . $temp . $bottom;
	
		//Set the new content
		$Post->SetContent( $content );
		
		//Overwrite the previous Variable
		$this->setVariable( 'Post', $Post );
	
		//Free up some memory
		unset( $adMiddle, $top, $temp, $bottom, $adTop, $adBottom, $content, $Post );
	}
	
	#####################################################
	#
	# Check if the post has AMP function disabled
	#
	#####################################################
	public function CheckPostAmpStatus()
	{
		if ( !Router::GetVariable( 'isAmp' ) )
			return;

		if ( !empty( Settings::Amp()['content_types'] ) )
		{
			$types = Settings::Amp()['content_types'];
				
			if ( ( $this->data['postType'] == 'page' ) && in_array( 'pages', $types ) )
				return;

			elseif ( ( $this->data['postType'] == 'post' ) && in_array( 'posts', $types ) )
				return;
		}

		$url = $this->data['postUrl'];

		@header("Location: " . $url, true, 301 );
		@exit;
	}
	
	#####################################################
	#
	# Check if the post has a redirection URL function
	#
	#####################################################
	public function CheckPostRedirection()
	{
		if ( empty( $this->data['xtraData'] ) || !isset( $this->data['xtraData']['post'] ) || empty( $this->data['xtraData']['post'] ) )
			return;
		
		if ( !$this->data['xtraData']['post']['enable_redirection'] || empty( $this->data['xtraData']['post']['redirection_url'] ) )
			return;

		@header("Location: " . $this->data['xtraData']['post']['redirection_url'], true, ( !empty( $this->data['xtraData']['post']['redirection_type'] ) ? $this->data['xtraData']['post']['redirection_type'] : 301 ) );
		
		@exit;
	}
	
	#####################################################
	#
	# Check if the user can view this post function
	#
	#####################################################
	public function CheckRedirects()
	{
		if ( !Settings::IsTrue( 'enable_redirect' ) )
			return;

		$redir = CheckRedirect( Sanitize( $_SERVER['REQUEST_URI'], false ) );

		if ( $redir )
		{
			UpdateRedirHits( $redir['id'] );
			
			if ( $redir['when_matched'] == 'error-404')
			{
				return;
			}
			
			elseif ( $redir['when_matched'] == 'redirect-to-random' )
			{
				$post = GetRandPosts( $this->currentLang['lang']['id'], 1 );

				if ( $post && !empty( $post['url'] ) )
				{
					@header('Location: ' . $post['url'] );
				}
			}

			elseif ( $redir['when_matched'] == 'redirect-to-url' )
			{
				if ( $redir['http_code'] != 'disable')
				{
					$code = (int) $redir['http_code'];
					
					@header('Location: ' . $redir['target'], TRUE, $code );
				}
					
				else
				{
					@header('Location: ' . $redir['target'] );
				}
			}
			
			//Add the response code
			if ( $redir['http_code'] !== 'disable')
			{
				$code = (int) $redir['http_code'];
				http_response_code( $code );
			}
			
			exit;
		}
		
		//Maybe there is a changed slug?
		$slugToCheck = Sanitize( Router::GetVariable( 'slug' ), false );

		if ( empty( $slugToCheck ) || ( $slugToCheck == '' ) )
			return;
		
		CheckChangedSlug( $slugToCheck );
	}
	
	#####################################################
	#
	# Check if the user can view this post function
	#
	#####################################################
	public function IsAllowedToView()
	{
		//If we don't want to show the whole content, let's change it here
		if ( !IsAllowedTo( 'view-posts' ) )
		{
			$pp = '';
			
			if ( IsAllowedTo( 'view-post-description' ) )
			{
				$pp .= ( !empty( $this->data['description'] ) ? $this->data['description'] : generateDescr( $this->data['post'], 150 ) );
			}
			
			$pp .= '<div class="restricted"><p>' . sprintf( __( 'restricted-content' ), SITE_URL . 'register/', SITE_URL . 'login/' ) . '</p></div>';
			
			$this->data['post'] = $pp;
		}
		
		if ( IsAllowedTo( 'view-lighter-version' ) && !$this->CheckAuth() )
		{
			Router::SetIncludeFile( INC_ROOT . 'light-theme.php' );
		}
	}
	
	public function CheckHomePage()
	{
		if ( StaticHomePage() && StaticHomePage( false, $this->data['id'] ) )
		{
			@header("Location: " . $this->siteUrl, true, 301 );
			@exit;
		}
		
		//Do the same for every lang but the default
		if ( 
			Router::GetVariable( 'isLang' ) && ( Settings::Lang()['id'] != $this->lang['lang']['id'] ) 
			&& 
				isset( $this->data['parentId'] ) && !empty( $this->data['parentId'] ) 
			&&
			( Settings::Get()['front_static_page'] == $this->data['parentId'] )
		)
		{
			@header("Location: " . $this->siteUrl, true, 301 );
			@exit;
		}
	}
	
	public function SetNoAccess()
	{
		if ( !$this->noAccess )
		{
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
		}
		else
		{
			Router::SetIncludeFile( INC_ROOT . 'no-access.php' );
			Router::SetVariable( 'accessDenied', true );
		}
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