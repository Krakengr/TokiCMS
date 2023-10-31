<?php defined('TOKICMS') or die('Hacking attempt...');

class Controller {
    protected 	$variables;
	//protected 	$staticCacheFile;
	//protected 	$hasStaticCache;
	public 		$theme;
    public 		$db;
	public 		$lang;
	public 		$siteLang;
    
    public function __construct()
	{
		global $db;
		
        $this->variables        = [];
        $this->db               = new Database();
		//$this->hasStaticCache   = ( ( DEBUG_MODE || !ENABLE_CACHE || ( CACHE_TYPE == 'normal' ) ) ? false : true );
		$this->lang        		= null;
		$db 					= $this->db;
    }
	
	public function view()
	{
		global $L, $Items, $Post;
		
		//Set the variables
		foreach ( $this->variables as $key => $value)
			${$key} = $value;

		//Set the total and max items to paginator
		if ( !empty( $Items ) && is_numeric( $Items ) )
		{
			Paginator::SetVariable( 'totalItems', $Items );
		
			Paginator::SetVariable( 'maxItemsPerPage', ( ( !empty( $ItemsPerPage ) && is_numeric( $ItemsPerPage ) ) ? $ItemsPerPage : (int) Settings::Get()['article_limit'] ) );
		}
	
		//Calculate the pages
		Paginator::Run();
		
		$WhereAmI = Router::WhereAmI();

		//Do a few settings before continue
		DoChecks();
		
		//Set the headers according to the router
		SetHeaders();

		// Nothing found? Spread the word
		if ( Router::NotFound() )
		{
			Error404();
			require( INC_ROOT . '404.php');
			exit;
		}

		//Check if we need an special page and include it
		elseif ( Router::GetVariable( 'includeFile' ) )
		{
			$notifyMessage = ( isset( $this->variables['notifyMessage'] ) && !empty( $this->variables['notifyMessage'] ) ? $this->getVariable( 'notifyMessage' ) : null );
			
			$disableButtons = ( isset( $this->variables['disableButtons'] ) && !empty( $this->variables['disableButtons'] ) ? $this->getVariable( 'disableButtons' ) : null );
			
			if ( file_exists( Router::GetVariable( 'includeFile' ) ) )
				include( Router::GetVariable( 'includeFile' ) );
			
			else
				require( INC_ROOT . '404.php');
			
			exit;
		}

		//Load the admin theme
		elseif ( Router::GetVariable( 'isAdmin' ) )
		{
			//$hook

			require( ADMIN_THEME . 'index.php');
		}

		//Load the amp theme, if there is a need for that
		elseif ( Router::GetVariable( 'isAmp' ) && THEME_AMP_DIR )
		{
			//Check if the theme folder exists
			if ( !file_exists( THEME_AMP_DIR . 'index.php' ) )
			{
				die( sprintf( __( 'the-theme-directory-does-not-exist' ), THEME_AMP ) );
			}
			
			//$hook
			
			if ( file_exists( THEME_AMP_DIR . 'functions.php' ) )
			{
				include( THEME_AMP_DIR . 'functions.php' );
			}
			
			require( THEME_AMP_DIR . 'index.php' );
			
			$this->CachePageEnd();
		}

		//Just load the default theme
		else
		{
			if ( !file_exists( THEME_DIR . 'index.php' ) )
			{
				die( sprintf( __( 'the-theme-directory-does-not-exist' ), THEME_MAIN ) );
			}
			
			//$hook
			
			require( THEME_DIR . 'index.php' );
				
			$this->CachePageEnd();
		}
	}
	
	public function CheckAuth( $group = 1 )
	{
		$AuthUser = $this->getVariable( 'AuthUser' );
		
		if ( !empty( $AuthUser ) && isset( $AuthUser['id_member'] ) && \Delight\Cookie\Cookie::exists('Auth') && ( $AuthUser['id_group'] == $group ) )
			return true;
		
		return false;
	}

	private function CachePageEnd()
	{
		if ( !$this->getVariable( 'hasStaticCache' ) || empty( $this->getVariable( 'staticCacheFile' ) ) )
			return;
		
		//Don't save the cacheFile if we don't want logged in users
		if ( !Settings::IsTrue( 'cache_all_visitors' ) && $this->IsLoggedIn() )
			return;
		
		$fp = fopen( $this->getVariable( 'staticCacheFile' ), 'w');  //open file for writing

		fwrite( $fp, ob_get_contents() . '<!-- This website is powered by TokiCMS. Read more: https://badtooth.studio/tokicms/ - cached@' . time() . ' -->' );
		fclose( $fp ); //Close file pointer
		
		ob_end_flush();
	}

	public function IsLoggedIn()
	{
		$AuthUser = $this->getVariable( 'AuthUser' );
		
		if ( !empty( $AuthUser ) && isset( $AuthUser['id_member'] ) && \Delight\Cookie\Cookie::exists('Auth') && $AuthUser['is_activated'] )
			return true;

		return false;
	}

	public function setVariable($name, $value) {
        $this->variables[$name] = $value;
        return $this;
    }
    
    public function getVariable($name) {
        return isset($this->variables[$name]) ? $this->variables[$name] : null;
    }

    public function __destruct(){}
}