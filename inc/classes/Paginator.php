<?php defined('TOKICMS') or die('Hacking attempt...');

class Paginator 
{
	private static $variables = [];
	
	public function __construct()
	{
		self::SetVariable( 'maxItemsPerPage', (int) Settings::Get()['article_limit'] );
		self::SetVariable( 'totalItems', 0 );
		self::SetVariable( 'currentPage', 1 );
		self::SetVariable( 'pageUri', SITE_URL );
		self::SetVariable( 'numberOfPages', 1 );
		self::SetVariable( 'olderPageUrl', null );
		self::SetVariable( 'newerPageUrl', null );
		self::SetVariable( 'hasOlderNav', false );
		self::SetVariable( 'hasNewerNav', false );
		self::SetVariable( 'inAdmin', false );
	}

	public static function Run()
	{
		self::SetVariable( 'currentPage', ( is_numeric( Router::GetVariable( 'pageNum' ) ) && ( Router::GetVariable( 'pageNum' ) > 0 ) ? (int) Router::GetVariable( 'pageNum' ) : 1 ) );
		
		if ( self::$variables['totalItems'] > 0 )
		{
			self::$variables['numberOfPages'] = (int) ceil( self::$variables['totalItems'] / self::$variables['maxItemsPerPage'] );
		}

		//Frontend
		if ( !self::$variables['inAdmin'] )
		{
			self::$variables['pageUri'] = Router::GetVariable( 'siteRealUrl' );
			
			self::$variables['pageUri'] .= ( Router::GetVariable( 'isBlog' ) ? Router::GetVariable( 'blogKey' ) . PS : '' );
			
			self::$variables['pageUri'] .= ( Router::GetVariable( 'isTag' ) ? str_replace( '/', '', Router::GetVariable( 'tagSlug' ) ) . PS . Router::GetVariable( 'tagKey' ) . PS : '' );
			
			self::$variables['pageUri'] .= ( Router::GetVariable( 'isUser' ) ? 'author' . PS . Router::GetVariable( 'authorKey' ) . PS : '' );
			
			self::$variables['pageUri'] .= ( Router::GetVariable( 'isCat' ) ? str_replace( '/', '', Router::GetVariable( 'categorySlug' ) ) . PS . Router::GetVariable( 'categoryKey' ) . PS : '' );
			
			self::$variables['pageUri'] .= ( Router::GetVariable( 'isSubCat' ) ? Router::GetVariable( 'subCategoryKey' ) . PS : '' );
			
			self::$variables['pageUri'] .= 'page' . PS;
			
			//If we are in a page > 1
			if ( ( self::$variables['numberOfPages'] > 1 ) && ( self::$variables['currentPage'] > 1 ) )
			{
				//Previous
				self::$variables['hasNewerNav'] = ( self::$variables['currentPage'] > 1 ? true : false );
				
				self::$variables['hasOlderNav'] = ( self::$variables['currentPage'] < self::$variables['numberOfPages'] ? true : false );

				self::$variables['newerPageUrl'] = ( self::$variables['hasNewerNav'] ? self::$variables['pageUri'] . ( self::$variables['currentPage'] - 1 ) . PS : '' );

				self::$variables['olderPageUrl'] = ( self::$variables['hasOlderNav'] ? self::$variables['pageUri'] . ( self::$variables['currentPage'] + 1 ) . PS : '' );
			}

			//If we are on the homepage, we need a few things for pagination to work
			//Page 1 = homepage
			elseif ( self::$variables['currentPage'] < 2 )
			{
				if( ( Router::GetVariable( 'whereAmI' ) != 'home' ) && ( self::$variables['currentPage'] == 1 ) && 
					( self::$variables['numberOfPages'] > 1 ) )
				{
					self::$variables['hasOlderNav']  = true;
					
					self::$variables['olderPageUrl'] = self::$variables['pageUri'] . '2' . PS;
				}

				elseif ( 
					( Router::GetVariable( 'whereAmI' ) == 'home' ) && Settings::IsTrue( 'display_pagination_home' ) 
					&& 
					( self::$variables['numberOfPages'] > 1 )
				)
				{
					self::$variables['hasOlderNav']  = true;
					
					self::$variables['olderPageUrl'] = self::$variables['pageUri'] . '2' . PS;
				}
			}
		}
		
		else
		{
			global $Admin;
			
			$orderBy = ( Router::GetVariable( 'orderBy' ) ? Router::GetVariable( 'orderBy' ) : null );
			
			$url = $Admin->CurrentAction() . PS . ( Router::GetVariable( 'subAction' ) ? Router::GetVariable( 'subAction' ) . PS : '' ) . ( !empty( $orderBy ) ? 'sort' . PS . $orderBy . PS . Router::GetVariable( 'order' )  . PS : '' ) . 'page' . PS;

			if ( self::$variables['currentPage'] > 0 )
			{
				self::$variables['hasNewerNav'] = ( self::$variables['currentPage'] > 1 ? true : false );
				
				self::$variables['hasOlderNav'] = ( self::$variables['currentPage'] < self::$variables['numberOfPages'] ? true : false );

				self::$variables['newerPageUrl'] = ( self::$variables['hasNewerNav'] ? $Admin->GetUrl( $url . ( self::$variables['currentPage'] - 1 ), null, false, null, true ) : '#' );

				self::$variables['olderPageUrl'] = ( self::$variables['hasOlderNav'] ? $Admin->GetUrl( $url . ( self::$variables['currentPage'] + 1 ), null, false, null, true ) : '#' );
				
				self::$variables['firstPageUrl'] = ( self::$variables['hasNewerNav'] ? $Admin->GetUrl( $url . 1, null, false, null, true ) : '#' );
				
				self::$variables['lastPageUrl'] = ( self::$variables['hasOlderNav'] ? $Admin->GetUrl( $url . self::$variables['numberOfPages'], null, false, null, true ) : '#' );
				
				self::$variables['pageUri'] = $url;
			}
			
			else
			{
				if ( self::$variables['numberOfPages'] > 1 )
				{
					self::$variables['hasOlderNav'] = true;
					
					self::$variables['olderPageUrl'] = $Admin->GetUrl( self::$variables['pageUri'] . 2, null, false, null, true );
				}
			}
		}
	}
	
	public static function NumberOfPages()
	{
		return self::$variables['numberOfPages'];
	}
	
	public static function PageNumUri( $num )
	{
		if ( self::$variables['inAdmin'] )
		{
			global $Admin;
			
			return $Admin->GetUrl( self::$variables['pageUri'] . $num, null, false, null, true );
		}
		
		return self::$variables['pageUri'] . $num . PS;
	}
	
	public static function HasOlder()
	{
		return self::$variables['hasOlderNav'];
	}
	
	public static function CurrentPageOfTotal( $sep = ' / ' )
	{
		return self::$variables['currentPage'] . $sep . self::$variables['numberOfPages'];
	}
	
	public static function HasNewer()
	{
		return self::$variables['hasNewerNav'];
	}
	
	public static function NewerPageUrl()
	{
		return self::$variables['newerPageUrl'];
	}
	
	public static function FirstPageUrl()
	{
		return self::$variables['firstPageUrl'];
	}
	
	public static function LastPageUrl()
	{
		return self::$variables['lastPageUrl'];
	}
	
	public static function OlderPageUrl()
	{
		return self::$variables['olderPageUrl'];
	}
	
	public static function CurrentPage()
	{
		return self::$variables['currentPage'];
	}
	
	public static function GetVariable( $name )
	{
        return isset( self::$variables[$name] ) ? self::$variables[$name] : null;
    }
	
	public static function SetVariable( $name, $value )
	{
        self::$variables[$name] = $value;
    }
}