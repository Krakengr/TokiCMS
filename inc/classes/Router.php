<?php defined('TOKICMS') or die('Hacking attempt...');

class Router
{
	protected static $routes = [];
	protected static $variables = [];
	
	public function __construct()
	{
		self::SetVariable( 'langsArray',  Settings::LangsArray() );
		self::SetVariable( 'defaultLang', Settings::Lang() );
		self::SetVariable( 'blogsArray',  Settings::BlogsArray() );
		self::SetVariable( 'slugTrans',   Settings::Trans() );
		self::SetVariable( 'customTypes', Settings::CustomTypes() );
		
		//Set the default Language before run
		self::SetVariable( 'langKey', self::$variables['defaultLang']['code'] );
		
		//Build the current Url
		self::BuildSiteUrl();
		
		//Set the necessary slugs
		self::TransSlugs();
		
		//Continue setting the default vars
		self::SetVariable( 'whereAmI', 'home' );
		self::SetVariable( 'postStatus', 'page' );
		self::SetVariable( 'isAmp', false );
		self::SetVariable( 'accessDenied', false );
		self::SetVariable( 'isStaticHomePage', false );
		self::SetVariable( 'isStaticBlogPage', false );
		self::SetVariable( 'parameters', array() );
		self::SetVariable( 'isCustomType', false );
		self::SetVariable( 'isSiteMap', false );
		self::SetVariable( 'isBrowsing', false );
		self::SetVariable( 'isSearch', false );
		self::SetVariable( 'isCat', false );
		self::SetVariable( 'isTag', false );
		self::SetVariable( 'isBlog', false );
		self::SetVariable( 'isUser', false );
		self::SetVariable( 'isSubCat', false );
		self::SetVariable( 'noIndex', false );
		self::SetVariable( 'isLang', false );
		self::SetVariable( 'notFound', false );
		self::SetVariable( 'isAdmin', false );
		self::SetVariable( 'includeFile', null );
		self::SetVariable( 'customHomeTemplate', null );
		self::SetVariable( 'customListTemplate', null );
		self::SetVariable( 'customPostTemplate', null );
		self::SetVariable( 'key', null );
		self::SetVariable( 'slug', null );
		self::SetVariable( 'controllerRoot', null );
		self::SetVariable( 'categoryKey', null );
		self::SetVariable( 'subCategoryKey', null );
		self::SetVariable( 'blogKey', null );
		self::SetVariable( 'pageKey', null );
		self::SetVariable( 'authorKey', null );
		self::SetVariable( 'ajaxFunction', null );
		self::SetVariable( 'subAction', null );
		self::SetVariable( 'tagKey', null );
		self::SetVariable( 'checkSlash', true ); //Always check for trailing slash
		self::SetVariable( 'httpMessage', 'OK' );
		self::SetVariable( 'controller', 'Home' );
		self::SetVariable( 'httpCode', 200 );
		self::SetVariable( 'pageNum', 0 );
		self::SetVariable( 'draftId', 0 );
		self::SetVariable( 'orderBy', null );
		self::SetVariable( 'order', null );
		self::SetVariable( 'key', null );
	}
	
	public function Run()
	{	
		//Grab the url path
		$path = strtolower( $this->GetUri() );

		//If the path is empty, stop here
		if ( ( $path == '' ) || ( $path == PS ) )
		  return;

		//Let's search our routes for any match
		foreach( self::$routes as $route )
		{
			$expression = '';
			
			if ( isset( $route['start'] ) && !is_null( $route['start'] ) )
				$expression .= $route['start'];
			else
				$expression .= '/';
			
			$expression .= $route['expression'];
			
			if ( isset( $route['end'] ) && !is_null( $route['end'] ) )
				$expression .= $route['end'];
			
			else
				$expression .= '/';

			if ( preg_match( $expression . 'i', $path, $matches ) )
			{
				
				if ( $route['function'] && is_callable( $route['function'] ) ) 
				{
					call_user_func( $route['function'], $matches );
				}

				break;
			}
		}

		if ( empty( self::$variables['slug'] ) )
		{
			self::SetNotFound();
			return;
		}

		//Rebuild the URL
		$this->BuildSiteUrl( true );
	}
	
	/**
	* Function to add a new route
	* @param string $expression    Route string or expression
	* @param callable $function    Function to call
	* @param string $start 		   If you to add something at the beginning of the expression, otherwise will be "/"
	* @param string $end 		   If you to add something at the end of the expression, otherwise will be "/"
	*
	*/
	public static function AddRoute( $expression, $function = null, $start = null, $end = null )
	{
		$array = array(
		  'expression'  => $expression,
		  'function'    => $function,
		  'start' 		=> $start,
		  'end' 		=> $end
		);
		
		self::$routes[] = $array;
	}

	public static function BuildSiteUrl( $depth = false )
	{
		$siteUrl = SITE_URL;
		
		$langKey = self::$variables['langKey'];

		if ( !Settings::IsTrue( 'hide_default_lang_slug' ) )
		{
			$siteUrl .= $langKey . PS;
		}
		
		self::$variables['siteRealUrl'] = $siteUrl;
		
		if ( $depth )
		{
			//Set a temporary variable to store this site URL, avoiding other slugs
			$_S = SITE_URL;
			
			//Add the lang slug to this temporary variable
			if ( ( $langKey != Settings::Lang()['code'] ) || ( ( $langKey == Settings::Lang()['code'] ) && !Settings::IsTrue( 'hide_default_lang_slug' ) ) )
			{
				$_S .= $langKey . PS;
			}
			
			self::$variables['siteRealUrl'] = $_S;
			
			if ( self::$variables['whereAmI'] == 'category' )
			{
				$siteUrl .= str_replace( '/', '', self::$variables['categorySlug'] ) . PS . self::$variables['categoryKey'] . PS;
			}
				
			elseif ( self::$variables['whereAmI'] == 'author' )
			{
				$siteUrl .= 'author' . PS . self::$variables['authorKey'] . PS;
			}
				
			elseif ( self::$variables['whereAmI'] == 'tag' )
			{
				$siteUrl .= str_replace( '/', '', self::$variables['tagSlug'] ) . PS . self::$variables['tagKey'] . PS;
			}
			
			elseif ( self::$variables['whereAmI'] == 'blog' )
			{
				$siteUrl .= self::$variables['blogKey'] . PS;
			}

			elseif ( self::$variables['whereAmI'] == 'post' )
			{
				if ( self::$variables['isBlog'] )
					$siteUrl .= self::$variables['blogKey'] . PS;
				
				if ( self::$variables['postSlug'] != '/' )
					$siteUrl .= str_replace( '/', '', self::$variables['postSlug'] ) . PS;

				$siteUrl .= self::$variables['slug'] . PS;
			}
		}

		self::$variables['url'] = rawurldecode( $siteUrl );
	}
	
	public static function CheckBlog( $slug = null )
	{
		$slug = ( !$slug ? self::$variables['slug'] : $slug );
		
		if ( MULTIBLOG && in_array( $slug, self::$variables['blogsArray'] ) )
		{
			self::$variables['whereAmI'] = 'blog';
			self::$variables['controller'] = 'Blog';
			self::$variables['isBlog'] = true;
			self::$variables['blogKey'] = $slug;
			
			//Add the custom post templates, if any
			self::$variables['customPostTemplate'] = ( !empty( Settings::BlogsFullArray()[$slug]['custom_post_tmp'] ) ? Settings::BlogsFullArray()[$slug]['custom_post_tmp'] : null );
			
			self::$variables['customHomeTemplate'] = ( !empty( Settings::BlogsFullArray()[$slug]['custom_home_tmp'] ) ? Settings::BlogsFullArray()[$slug]['custom_home_tmp'] : null );
			
			self::$variables['customListTemplate'] = ( !empty( Settings::BlogsFullArray()[$slug]['custom_list_tmp'] ) ? Settings::BlogsFullArray()[$slug]['custom_list_tmp'] : null );

			return true;
		}
		
		return false;
	}
	
	//Check it this is a short URL
	public static function CheckShortUrls()
	{
		if ( Settings::IsTrue( 'enable_link_manager' ) )
		{
			$lSet = Settings::LinkSettings();
			
			if ( !empty( $lSet['short-link-settings']['enable'] ) && empty( $lSet['short-link-settings']['base_slug_prefix'] ) )
			{
				$db = db();

				$x = $db->from( null, "
				SELECT id
				FROM `" . DB_PREFIX . "links`
				WHERE (short_link = :slug) AND (id_site = " . SITE_ID . ") AND (status = 'active')",
				array( self::$variables['slug'] => ':slug' )
				)->single();

				if ( $x )
				{
					self::$variables['whereAmI'] = 'shortlink';
					self::$variables['controller'] = 'ShortLink';
					
					return true;
				}
			}
		}
		
		return false;
	}
	
	public static function CheckCustomTypes( $slug = null )
	{
		$slug = ( !$slug ? self::$variables['slug'] : $slug );
		
		if ( !empty( self::$variables['customTypes'] ) && in_array( $slug, self::$variables['customTypes'] ) )
		{
			self::$variables['whereAmI'] = 'customType';
			self::$variables['controller'] = 'CustomType';
			self::$variables['isCustomType'] = true;

			return true;
		}
		
		return false;
	}
	
	public static function CheckLang( $slug = null )
	{
		$slug = ( !$slug ? self::$variables['slug'] : $slug );
		
		if ( in_array( $slug, self::GetVariable( 'langsArray' ) ) )
		{
			self::$variables['isLang'] = true;
			self::$variables['whereAmI'] = 'home';
			self::$variables['controller'] = 'Home';//'Lang';
			self::$variables['langKey'] = $slug;
			
			//Update the slugs based on $this language
			self::UpdateTransSlugs();

			return true;
		}
		
		return false;
	}
	
	public static function TransSlugs()
	{
		$defaultLang = self::GetVariable( 'defaultLang' );
		$slugTrans = self::GetVariable( 'slugTrans' );

		if ( MULTILANG && !empty( $slugTrans ) && isset( $slugTrans[$defaultLang['code']] ) )
		{
			self::$variables['categorySlug'] = ( ( isset( $slugTrans[$defaultLang['code']]['category_filter_trans'] ) && !empty( $slugTrans[$defaultLang['code']]['category_filter_trans'] ) )
									? '/' . $slugTrans[$defaultLang['code']]['category_filter_trans'] . '/' : Settings::Get()['categories_filter'] );
									
			self::$variables['postSlug'] = ( ( isset( $slugTrans[$defaultLang['code']]['post_filter_trans'] ) && !empty( $slugTrans[$defaultLang['code']]['post_filter_trans'] ) )
								? '/' . $slugTrans[$defaultLang['code']]['post_filter_trans'] . '/' : Settings::Get()['posts_filter'] );
			
			self::$variables['tagSlug'] = ( ( isset( $slugTrans[$defaultLang['code']]['tags_filter_trans'] ) && !empty( $slugTrans[$defaultLang['code']]['tags_filter_trans'] ) )
								? '/' . $slugTrans[$defaultLang['code']]['tags_filter_trans'] . '/' : Settings::Get()['tags_filter'] );
		}
		
		else
		{
			self::$variables['categorySlug'] = Settings::Get()['categories_filter'];
			self::$variables['postSlug'] = Settings::Get()['posts_filter'];
			self::$variables['tagSlug'] = Settings::Get()['tags_filter'];
		}
	}
	
	private static function UpdateTransSlugs()
	{
		$slugTrans = self::GetVariable( 'slugTrans' );
		
		if ( !MULTILANG || empty( $slugTrans ) || !self::GetVariable( 'isLang' ) )
			return;
		
		$langKey = self::GetVariable( 'langKey' );
		
		if ( isset( $slugTrans[$langKey] ) )
		{
			if ( isset( $slugTrans[$langKey]['category_filter_trans'] ) && !empty( $slugTrans[$langKey]['category_filter_trans'] ) )
				self::$variables['categorySlug'] = '/' . $slugTrans[$langKey]['category_filter_trans'] . '/';

			if ( isset( $slugTrans[$langKey]['post_filter_trans'] ) && !empty( $slugTrans[$langKey]['post_filter_trans'] ) )
				self::$variables['postSlug'] = '/' . $slugTrans[$langKey]['post_filter_trans'] . '/';
			
			if ( isset( $slugTrans[$langKey]['tags_filter_trans'] ) && !empty( $slugTrans[$langKey]['tags_filter_trans'] ) )
				self::$variables['tagSlug'] = '/' . $slugTrans[$langKey]['tags_filter_trans'] . '/';
		}
	}
	
	public static function CheckFirstSlug( $key )
	{
		if ( MULTIBLOG && !empty( self::$variables['blogsArray'] ) && in_array( $key, self::$variables['blogsArray'] ) )
		{
			self::$variables['blogKey'] = $key;
			self::$variables['isBlog'] = true;
		}
		
		elseif ( self::CheckLang( $key ) )
		{
			self::$variables['langKey'] = $key;
			self::$variables['isLang'] = true;
			
			//Update the slugs based on this language
			self::UpdateTransSlugs();
		}
		
		elseif ( !empty( self::$variables['customTypes'] ) && in_array( $key, self::$variables['customTypes'] ) )
		{
			self::$variables['customTypeKey'] = $key;
			self::$variables['isCustomType'] = true;
		}

		else
			return false;
		
		return true;
	}
	
	public static function CheckEtc( $k = null )
	{
		$key = ( empty( $k ) ? self::$variables['key'] : $k );
		$key = rawurldecode( $key );

		//Check if we want a post
		if ( ( self::$variables['postSlug'] !== '/' ) && ( $key == str_replace( '/', '', rawurldecode( self::$variables['postSlug'] ) ) ) )
		{
			self::$variables['whereAmI'] = 'post';
			self::$variables['controller'] = 'Content';
			self::$variables['postStatus'] = 'post';
		}

		//Check if we want a category
		elseif ( $key == str_replace( '/', '', rawurldecode( self::$variables['categorySlug'] ) ) )
		{
			self::$variables['whereAmI'] = 'category';
			self::$variables['controller'] = 'Category';
			self::$variables['categoryKey'] = self::$variables['slug'];
			self::$variables['isCat'] = true;
		}

		//Check if we want a tag
		elseif ( $key == str_replace( '/', '', rawurldecode( self::$variables['tagSlug'] ) ) )
		{
			self::$variables['whereAmI'] = 'tag';
			self::$variables['controller'] = 'Tag';
			self::$variables['tagKey'] = self::$variables['slug'];
			self::$variables['isTag'] = true;
		}
		
		//Check if we want a custom post type
		elseif ( !empty( self::$variables['customTypes'] ) && in_array( $key, self::$variables['customTypes'] ) )
		{
			self::$variables['whereAmI'] = 'customType';
			self::$variables['controller'] = 'CustomType';
			self::$variables['customTypeKey'] = $key;
			self::$variables['isCustomType'] = true;
		}
		
		else
			return false;

		return true;
	}
	
	public static function SetNotFound()
	{
		self::$variables['notFound'] = true;
		self::$variables['httpMessage'] = 'Not Found';
		self::$variables['controller'] = null;
		self::$variables['httpCode'] = 404;
	}
	
	public static function SetPostStatus( $string )
	{
		self::$variables['postStatus'] = $string;
	}

	public function Parameters()
	{
		return $this->parameters;
	}
	
	public static function SetWhereAmI( $string )
	{
		self::$variables['whereAmI'] = $string;
	}
	
	public static function SetIncludeFile ( $file )
	{
		self::$variables['includeFile'] = $file;
	}
	
	public static function SetVariable( $name, $value )
	{
        self::$variables[$name] = $value;
    }
	
	public static function WhereAmI()
	{
        return self::$variables['whereAmI'];
    }
	
	public static function NotFound()
	{
        return self::$variables['notFound'];
    }
    
    public static function GetVariable( $name )
	{
        return isset( self::$variables[$name] ) ? self::$variables[$name] : null;
    }
	
	public static function SetStaticHomePage( $static = true )
	{
		self::$variables['isStaticHomePage'] = $static;
	}
	
	public static function SetStaticBlogPage( $static = true )
	{
		self::$variables['isStaticBlogPage'] = $static;
	}
	
	public function GetUri( $trailing = false, $q = false )
	{
		$uri = Sanitize ( $_SERVER['REQUEST_URI'], false );
		
		if ( $q )
		{
			if ( strpos ( $uri, '?' ) !== false )
			{
				$ur = explode ( '?', $uri );
				
				$uri = ( !empty( $ur['0'] ) ? $ur['0'] : $uri );
			}
		}
		
		$base = GetBase();
				
		if ( $base !== '/' )
		{
			if ( $trailing )
				$url = str_replace( $base, '/', $uri );
			
			else
				$url = str_replace( $base, '', $uri );
		}
		else
			$url = $uri;
				
		return $url;
  }

}