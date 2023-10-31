<?php defined('TOKICMS') or die('Hacking attempt...');

class SitemapSettings extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-seo' ) ) || !$Admin->Settings()::IsTrue( 'enable_seo' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'sitemap-settings' ) )
			return;
		
		//Get the needed POST values
		$settings = $_POST['settings'];
		$sitemap = $_POST['sitemap'];
		
		$indexNowKey = null;
		
		$checkFileChild = $deleteFromChild = false;
		
		$sitemapSett = Json( $Admin->Settings()::Get()['sitemap_data'] );

		$pingEngines = array();
		
		//Check if we want IndexNow
		if ( isset( $sitemap['enable_indexnow'] ) )
		{
			if ( !isset( $sitemapSett['indexnow_key'] ) || empty( $sitemapSett['indexnow_key'] ) )
			{
				if( function_exists('hash_algos') && in_array( 'sha512', hash_algos() ) )
				{
					$indexNowKey = hash('sha512', uniqid( microtime(), true ) );
				}
				
				else
				{
					$indexNowKey = nonce( uniqid( microtime(), true ) );
				}
				
				$indexNowKey = substr( $indexNowKey, 1, 32 );
				
				if ( $Admin->IsDefaultSite() )
				{
					$file = ROOT . $indexNowKey . '.txt';
					
					file_put_contents( $file, $indexNowKey, LOCK_EX );
				}
				
				else
				{
					$checkFileChild = true;
				}
			}
			
			else
			{
				$indexNowKey = $sitemapSett['indexnow_key'];
				
				if ( $Admin->IsDefaultSite() )
				{
					$file = ROOT . $indexNowKey . '.txt';
					
					if ( !file_exists( $file ) )
					{
						file_put_contents( $file, $indexNowKey, LOCK_EX );
					}
				}
				
				else
				{
					$checkFileChild = true;
				}
			}
		}
		
		else
		{
			if ( isset( $sitemapSett['indexnow_key'] ) )
			{
				if ( $Admin->IsDefaultSite() )
				{
					$file = ROOT . $sitemapSett['indexnow_key'] . '.txt';

					if ( file_exists( $file ) )
					{
						@unlink( $file );
					}
				}
				
				else
				{
					$deleteFromChild = true;
				}
			}
		}

		if ( !empty( $sitemap['search_engines'] ) )
		{
			$_urls = explode( PHP_EOL, $sitemap['search_engines'] );
			
			if ( !empty( $_urls ) )
			{
				foreach ( $_urls as $_url )
				{
					if ( empty( $_url ) )
						continue;
					
					$pingEngines[] = $_url;
				}
			}
		}
		
		$settingsArray = array(
					'enable_sitemap' => ( isset( $settings['enable_sitemap'] ) ? "true" : "false" ),
					'notify_search_engines' => ( isset( $settings['notify_search_engines'] ) ? "true" : "false" ),
					'enable_news_sitemap' => ( isset( $settings['enable_news_sitemap'] ) ? "true" : "false" )
		);
				
		//We are going to create a new array, so there is no need to get the settings from the DB
		$n = array(
				'publication_name' => ( isset( $sitemap['publication_name'] ) ? Sanitize ( $sitemap['publication_name'], false ) : '' ),
				'include_orphan' => ( isset( $sitemap['include_orphan'] ) ? true : false ),
				'content_types' => ( ( isset( $sitemap['content_types'] ) && is_array( $sitemap['content_types'] ) ) ? array_values( $sitemap['content_types'] ) : array() ),
		);
		
		//We are going to create a new array, so there is no need to get the settings from the DB
		$s = array(
				'include_the_last_modification_time' => ( isset( $sitemap['include_the_last_modification_time'] ) ? true : false ),
				'limit_urls' => ( isset( $sitemap['limit_urls'] ) ? (int) $sitemap['limit_urls'] : 1000 ),
				'last_time_pinged' => ( isset( $sitemapSett['last_time_pinged'] ) ? $sitemapSett['last_time_pinged'] : 0 ),
				'include_homepage' => ( isset( $sitemap['include_homepage'] ) ? true : false ),
				'include_posts' => ( isset( $sitemap['include_posts'] ) ? true : false ),
				'include_pages' => ( isset( $sitemap['include_pages'] ) ? true : false ),
				'include_custom_post_types' => ( isset( $sitemap['include_custom_post_types'] ) ? true : false ),
				'include_blogs' => ( isset( $sitemap['include_blogs'] ) ? true : false ),
				'include_langs' => ( isset( $sitemap['include_langs'] ) ? true : false ),
				'include_categories' => ( isset( $sitemap['include_categories'] ) ? true : false ),
				'include_tags' => ( isset( $sitemap['include_tags'] ) ? true : false ),
				'include_authors' => ( isset( $sitemap['include_authors'] ) ? true : false ),
				'include_threads' => ( isset( $sitemap['include_threads'] ) ? true : false ),
				'include_products' => ( isset( $sitemap['include_products'] ) ? true : false ),
				
				//IndexNow
				'indexnow_key' => $indexNowKey,
				'enable_indexnow' => ( isset( $sitemap['enable_indexnow'] ) ? true : false ),
				'generic_end_point' => ( isset( $sitemap['generic_end_point'] ) ? true : false ),
				'indexnow_engines' => ( ( isset( $sitemap['indexnow_engines'] ) && is_array( $sitemap['indexnow_engines'] ) ) ? array_values( $sitemap['indexnow_engines'] ) : array() ),
				
				//Priorities
				'homepage_priority' => ( isset( $sitemap['homepage_priority'] ) ? Sanitize ( $sitemap['homepage_priority'], false ) : '0.5' ),
				'posts_priority' => ( isset( $sitemap['posts_priority'] ) ? Sanitize ( $sitemap['posts_priority'], false ) : '0.5' ),
				'pages_priority' => ( isset( $sitemap['pages_priority'] ) ? Sanitize ( $sitemap['pages_priority'], false ) : '0.5' ),
				'blogs_priority' => ( isset( $sitemap['blogs_priority'] ) ? Sanitize ( $sitemap['blogs_priority'], false ) : '0.5' ),
				'langs_priority' => ( isset( $sitemap['langs_priority'] ) ? Sanitize ( $sitemap['langs_priority'], false ) : '0.5' ),
				'categories_priority' => ( isset( $sitemap['categories_priority'] ) ? Sanitize ( $sitemap['categories_priority'], false ) : '0.5' ),
				'tags_priority' => ( isset( $sitemap['tags_priority'] ) ? Sanitize ( $sitemap['tags_priority'], false ) : '0.5' ),
				'products_priority' => ( isset( $sitemap['products_priority'] ) ? Sanitize ( $sitemap['products_priority'], false ) : '0.5' ),
				'threads_priority' => ( isset( $sitemap['threads_priority'] ) ? Sanitize ( $sitemap['threads_priority'], false ) : '0.5' ),
				'search_engines' => $pingEngines,
		);

		$settingsArray['sitemap_data'] = json_encode( $s );
		$settingsArray['news_sitemap_data'] = json_encode( $n );

		unset( $s );
		
		//Delete the cached files
		if ( $Admin->IsDefaultSite() )
		{
			DeleteFolderFiles( CACHE_SITEMAP_ROOT );
		}
		else
		{
			$Admin->PingChildSite( 'build-sitemap', null, null, $Admin->GetSite() );
		}

		$Admin->UpdateSettings( $settingsArray );
		
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		if ( $deleteFromChild )
		{
			$Admin->PingChildSite( 'delete-indexnow-file', null, null, $Admin->GetSite() );
		}
		
		if ( $checkFileChild )
		{
			$Admin->PingChildSite( 'check-indexnow-file', null, null, $Admin->GetSite() );
		}
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}