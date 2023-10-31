<?php defined('TOKICMS') or die('Hacking attempt...');

class App {
	private   		 $site;
	private   		 $auth;
	private   		 $permissions;
	private   		 $timeper;
	private   		 $userGroup;
	private   		 $hooks = [];
	private   		 $theme;
	private   		 $hasStaticCache;
	private   		 $staticCacheFile;
    protected 		 $controller;
	protected static $vars = [];
	

    public function __construct() 
	{
		$this->controller 		= new Controller;
		$this->theme 	  		= THEME_MAIN;
		$this->hasStaticCache   = ( ( DEBUG_MODE || !ENABLE_CACHE || ( CACHE_TYPE == 'normal' ) ) ? false : true );
    }
	
	public function route()
	{
		global $L;

		//Make Permissions and Group/UserId globbaly
		self::$vars['Permissions'] 		= $this->permissions;
		self::$vars['Generic'] 			= array();
		self::$vars['TimePermissions'] 	= $this->timeper;
		self::$vars['LoadThemeLang'] 	= false;
		self::$vars['UserGroup'] 		= ( $this->IsLoggedIn() ? $this->auth['id_group'] : $this->userGroup );
		self::$vars['UserId'] 			= ( $this->IsLoggedIn() ? $this->auth['id_member'] : 0 );
		
		RunHooks( 'before_site_load' );

		$router    	= new Router;
		$Paginator 	= new Paginator;
		$Editor  	= null;
		
		//Load the routes
		require ( INC_ROOT . 'routes.php' );

		//Run the router
		$router->Run();
		
		//Grab the language key and the default language code
		$langKey = Router::GetVariable( 'langKey' ); //Current key
		$defaultlangKey = Settings::LangData()['lang']['code']; //Default Key
		
		//Set the current language
		self::$vars['CurrentLang'] = Settings::AllLangs()[$langKey];

		//Set up the Theme
		$Theme = new Theme();
		
		//Check for a blog. We do it here, so if the blog has an extra theme, to be enabled before all
		$this->CheckBlog();
		
		//Now we can define theme related paths
		define( 'THEME_DIR', THEME_MAIN_DIR . $this->theme . DS );
		define( 'THEME_DIR_PHP', THEME_DIR . 'php' . DS );
		define( 'HTML_PATH_THEME', SITE_URL . 'themes' . PS . $this->theme . PS );
		
		//Theme Data
		self::$vars['ThemeData'] = ( isset( Settings::Themes()[$this->theme]['options'][$langKey] ) ? Settings::Themes()[$this->theme]['options'][$langKey] : ( isset( Settings::Themes()[$this->theme]['options'][$defaultlangKey] ) ? Settings::Themes()[$this->theme]['options'][$defaultlangKey] : null ) );
		
		//It there is no tralinig slash, add one
		if ( Router::GetVariable( 'checkSlash' ) )
		{
			CheckLast();
		}
		
		self::$vars['CurrentTheme'] = $this->theme;

		//The theme can have a functions file
		//Load the file if exists now to load the functions
		if ( file_exists( THEME_DIR . 'functions.php' ) )
			include_once( THEME_DIR . 'functions.php' );

		//Load the Admin class before call the controller
		if ( Router::GetVariable( 'isAdmin' ) )
		{
			if ( $this->IsLoggedIn() )
			{
				if ( !IsAllowedTo( 'view-dashboard' ) && !IsAllowedTo( 'admin-site' ) )
				{
					@header('Location: ' . SITE_URL );
					exit;
				}
				
				global $Admin;
				
				RunHooks( 'in_admin_start' );

				require_once( CLASSES_ROOT . 'Admin.php' );
				require_once( CLASSES_ROOT . 'Editor.php' );				
				
				$Admin = new Admin;
				$Admin->auth = $this->auth;

				require_once( FUNCTIONS_ROOT . 'admin-functions.php' );
				require_once( FUNCTIONS_ROOT . 'admin-posts-functions.php' );
				require_once( FUNCTIONS_ROOT . 'bootstrap-functions.php' );
				require_once( FUNCTIONS_ROOT . 'admin-theme-functions.php' );
				
				$Admin->Run();
				
				$Editor = new Editor;
				
				RunHooks( 'in_admin_end' );
				
				//Tell the Paginator that we are browsing the admin panel
				Paginator::SetVariable( 'inAdmin', true );
				Paginator::SetVariable( 'maxItemsPerPage', 20 );
			}
			else
			{
				@header('Location: ' . SITE_URL . 'login' . PS );
				exit;
			}
		}

		//It will be 'null' if there is no route match
		$controller 	= $this->Controller();
		$controllerRoot = Router::GetVariable( 'controllerRoot' );
		$controllerFile = CONTROLLER_ROOT . ( $controllerRoot ? $controllerRoot . DS : '' ) . $controller . '.php';

		if ( $controller && file_exists( $controllerFile ) )
		{
			require_once ( $controllerFile );
		}
		else
		{
			Error404();
			$controller = 'Noting';
			require_once ( CONTROLLER_ROOT . $controller . '.php' );
		}

		$this->controller = new $controller;

		//Pass a few things to the controller
		$this->controller->setVariable( 'Route', Router::GetVariable( 'controller' ) );
		$this->controller->setVariable( 'WhereAmI', Router::GetVariable( 'whereAmI' ) );
		$this->controller->setVariable( 'SiteUrl', Router::GetVariable( 'siteRealUrl' ) );
		//$this->controller->setVariable( 'Hooks', $Hooks );
		$this->controller->setVariable( 'Editor', $Editor );
		$this->controller->setVariable( 'DefaultLangId', Settings::Lang()['id'] );
		$this->controller->setVariable( 'Token', Csrf::token() );
		$this->controller->setVariable( 'isValid', Csrf::all());
		
		$this->controller->siteLang = $L;
		$this->controller->lang 	= self::$vars['CurrentLang'];
		
		if ( !empty( self::$vars['Generic'] ) )
		{
			foreach ( self::$vars['Generic'] as $item => $value )
			{
				$this->controller->setVariable( $item, $value );
			}
		}

		if ( $router::GetVariable( 'isAdmin' ) && isset( $Admin ) && !is_null( $Admin ) )
		{
			$this->controller->SetVariable( 'Admin', $Admin );
		}
		
		//Theme::Build();
    }

	//Checks if there is a blog requested and if has a theme enabled. Otherwise, set the theme to the site's default
	private function CheckBlog()
	{
		if ( !MULTIBLOG || !Router::GetVariable( 'isBlog' ) )
			return;
		
		$Blog = GetBlog( Router::GetVariable( 'blogKey' ), null, SITE_ID, self::$vars['CurrentLang']['lang']['id'] );

		if ( empty( $Blog ) )
		{
			Router::SetNotFound();
			$this->controller->setVariable( 'WhereAmI', '404' );
			return;
		}
		
		if ( !empty( $Blog['theme'] ) )
			$this->theme = $Blog['theme'];
		
		unset( $Blog );
	}

	//Checks if the site is set as "Offline"
	private function Controller()
	{
		if ( is_null( Router::GetVariable( 'controller' ) ) )
			return null;
		
		$controller = Router::GetVariable( 'controller' );
		$whereAmI = Router::GetVariable( 'whereAmI' );
		
		if ( Settings::IsTrue( 'enable_maintenance', 'site' ) && !IsAllowedTo( 'admin-site' ) )
		{
			if ( ( $whereAmI != 'login' ) && ( $whereAmI != 'register' ) && ( $whereAmI != 'forgot-password' ) && ( $whereAmI != 'recovery' ) && ( $whereAmI != 'ping' ) )
				$controller = 'Offline';
		}

		return $controller;
	}

    public function auth()
	{   
        $AuthUser = null;
		
        if( \Delight\Cookie\Cookie::exists( 'Auth' ) )
		{
			list( $selector, $authenticator ) = explode( ':', \Delight\Cookie\Cookie::get( 'Auth' ) );

			if ( !empty( $selector ) && !empty( $authenticator ) )
			{
				$db = db();
				
				//Query: token
				$Auth = $db->from( null, "
				SELECT *
				FROM `" . DB_PREFIX . "auth_tokens`
				WHERE (selector = :sel) AND expires > CURDATE()",
				array( $selector => ':sel' )
				)->single();

				if( !$Auth )
				{
					$this->DestroySession();
					$AuthUser = null;
				}
			
				else
				{
					if ( hash_equals( $Auth['token'], hash('sha256', base64_decode( $authenticator ) ) ) )
					{
						$query = "SELECT u.*, m.group_permissions as permissions, m.time_permissions as timeper
						FROM `" . DB_PREFIX . USERS . "` AS u
						LEFT JOIN `" . DB_PREFIX . "membergroup_relation` as m ON m.id_group = u.id_group AND m.id_site = u.id_site
						WHERE (u.id_member = " . $Auth['userid'] . ")";

						//Query: user
						$AuthUser = $db->from( null, $query )->single();

						if ( !$AuthUser )
						{
							$this->DestroySession( $Auth['id'] );
							$AuthUser = null;
						}
					}
					else
					{
						$this->DestroySession( $Auth['id'] );
						$AuthUser = null;
					}
				}
			}
        }

        return $AuthUser;
    }
	
	public static function GetVar( $name, $generic = false )
	{
		if ( $generic )
		{
			return isset( self::$vars['Generic'][$name] ) ? self::$vars['Generic'][$name] : null;
		}
		
		else
		{
			return isset( self::$vars[$name] ) ? self::$vars[$name] : null;
		}
    }
	
	public static function SetVar( $name, $value )
	{
        return self::$vars['Generic'][$name] = $value;
    }
	
	public static function SetThemeVars( $vars )
	{
        self::$vars['ThemeData'] = $vars;
    }
	
	public static function SetThemeLang( $load = false )
	{
        self::$vars['LoadThemeLang'] = $load;
    }

    public function process()
	{
		if ( DEBUG_MODE )
		{
			// Load time init
			$startTime = microtime();
			$startTime = explode(' ', $startTime);
			$startTime = $startTime[1] + $startTime[0];
			self::$vars['StartTime'] = $startTime;
		}
		
		if ( DEBUG_MODE || !ENABLE_CACHE || ( CACHE_TYPE == 'normal' ) )
			ob_start();
		
		else
		{
			if ( CACHE_TYPE == 'advanced-compress' )
				ob_start('minifyHTML');
			
			else
				ob_start();
		}

		$this->auth       		 = $this->auth();
		$this->permissions       = $this->Permissions();
		
		$this->route();
		
		//Load the static file, if is valid
		$this->LoadStaticCache();
		
		if ( DEBUG_MODE )
		{
			$startTime = self::$vars['StartTime'];
	
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];

			self::$vars['TotalTime'] = round( ( $time - $startTime ), 4 );
			
			self::$vars['ShowDegubNotice'] = ( ( $this->IsLoggedIn() && $this->CheckAuth() ) ? true : false );
		}

		$this->controller->setVariable("AuthUser", $this->auth );
		$this->controller->setVariable("hasStaticCache", $this->hasStaticCache );
		$this->controller->setVariable("staticCacheFile", $this->staticCacheFile );
		
		$this->controller->process();
    }
	
	private function CheckAuth( $group = 1 )
	{
		if ( !empty( $this->auth ) && isset( $this->auth['id_member'] ) && \Delight\Cookie\Cookie::exists('Auth') && ( $this->auth['id_group'] == $group ) )
			return true;
		
		return false;
	}
	
	private function IsLoggedIn()
	{
		if ( !empty( $this->auth ) && isset( $this->auth['id_member'] ) && \Delight\Cookie\Cookie::exists('Auth') && $this->auth['is_activated'] )
			return true;

		return false;
	}
	
	private function LoadStaticCache()
	{
		if ( !$this->hasStaticCache )
			return;
		
		//Don't bother if we don't want logged in users
		if ( !Settings::IsTrue( 'cache_all_visitors' ) && $this->IsLoggedIn() )
			return;

		$this->staticCacheFile = $this->StaticCacheFile();

		if ( ValidCache( $this->staticCacheFile ) )
		{
			include( $this->staticCacheFile );

			ob_end_flush();

			exit(0);
		}
	}
	
	private function StaticCacheFile()
	{
		if ( $this->IsLoggedIn() && Settings::IsTrue( 'cache_all_visitors' ) )
		{
			$userId = $AuthUser['id_member'];
		}
		else
			$userId = 0;

		if ( Router::WhereAmI() == 'post' )
		{
			$cacheFile = PostCacheFile( Sanitize( Router::GetVariable( 'slug' ), false ), null, $this->lang['lang']['code'], Router::GetVariable( 'isAmp' ), ( Router::GetVariable( 'isAmp' ) ? null : Settings::Get()['theme'] ), true );
		}
		
		else
		{
			$uri = GetUri();
		
			if ( empty( $uri ) || ( $uri == 'index.php' ) )
				$key = 'home';

			else
			{
				$key = '';

				if ( strpos ( $uri, '/' ) !== false )
				{
					$keys = explode( '/', $uri );
					$count = count( $keys );
					$key = implode( '-', $keys );
					$key = rtrim($key, "-");
				}
			
				else
					$key = $uri;
			}
			
			$cacheFile = CacheFileName( 'static-' . $key . ( Router::GetVariable( 'isAmp' ) ? null : '-theme_' . Settings::Get()['theme'] ), null, $this->lang['lang']['id'], null, null, null, $this->lang['lang']['code'] );
		}
		
		return $cacheFile;
	}
	
	private function Permissions()
	{
		$permissions = $timeper = null;

		//If the user is logged in, get their permissions
		if ( $this->IsLoggedIn() && !empty( $this->auth['permissions'] ) )
		{
			$permissions = ( ( $this->auth['permissions'] != 'all' ) ? Json( $this->auth['permissions'] ) : $this->auth['permissions'] );
			
			$timeper = ( ( $this->auth['timeper'] != 'none' ) ? Json( $this->auth['timeper'] ) : $this->auth['timeper'] );
		}
		
		//Maybe a bot or a guest?
		else
		{
			if ( IsBot() )
			{
				$id = 6;//( isset( Settings::MemberGroups()['search-engines'] ) ? Settings::MemberGroups()['search-engines']['id'] : 0 );
			}
			
			else
			{
				$id = 5;//( isset( Settings::MemberGroups()['guests'] ) ? Settings::MemberGroups()['guests']['id'] : 0 );
			}

			$data = OpenFileDB ( GUESTS_PERMISSIONS_FILE );
			
			if ( !isset( $data[$id] ) )
			{
				$db = db();
	
				//Query: membergroup
				$s = $db->from( null, "
				SELECT group_permissions, time_permissions
				FROM `" . DB_PREFIX . "membergroup_relation`
				WHERE (id_group = :id) AND (id_site = " . SITE_ID . ")",
				array( $id => ':id' )
				)->single();
				

				if ( $s )
				{
					$permissions = Json( $s['group_permissions'] );
					$timeper = Json( $s['time_permissions'] );
				}
				
				$data[$id] = array( 'permissions' => $permissions, 'timeper' => $timeper );
				
				WriteFileDB ( $data, GUESTS_PERMISSIONS_FILE );
			}
			
			else
			{
				$permissions = $data[$id]['permissions'];
				$timeper = $data[$id]['timeper'];
			}
			
			$this->userGroup = $id;
			$this->timeper   = $timeper;
		}

		return $permissions;
	}
	
	private function DestroySession( $id = null )
	{
		unset( $_COOKIE['Auth'] );
		setcookie( 'Auth', '', time() - (86400 * 530), "/" );
		//session_unset();
		//session_destroy();

		if ( $id )
		{
			$db = db();

			//Delete any old keys from this user
			$db->delete( 'auth_tokens' )->where( 'id', $id )->run();
		}
	}

    public function __destruct() {}
}