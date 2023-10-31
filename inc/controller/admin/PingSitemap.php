<?php defined('TOKICMS') or die('Hacking attempt...');

class PingSitemap extends Controller {
	
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

		$sitemapSett = Json( $Admin->Settings()::Get()['sitemap_data'] );
		
		$sitemapUrl = $Admin->SiteUrl() . 'sitemap_index.xml';

		if ( $Admin->Settings()::IsTrue( 'notify_search_engines' ) && !empty( $sitemapSett && isset( $sitemapSett['search_engines'] ) && !empty( $sitemapSett['search_engines'] ) ) )
		{
			//Don't ping the sitemap multiple times
			if ( !isset( $sitemapSett['last_time_pinged'] ) || ( empty( $sitemapSett['last_time_pinged'] ) || ( $sitemapSett['last_time_pinged'] < ( time() + 3600 ) ) ) )
			{
				$q = SitemapSubmit( $sitemapUrl, $sitemapSett['search_engines'] );

				if ( $q )
				{
					$sitemapSett['last_time_pinged'] = time();
					
					$settingsArray = array( 'sitemap_data' => json_encode( $sitemapSett ) );
					
					$Admin->UpdateSettings( $settingsArray );
			
					$Admin->DeleteSettingsCacheSite( 'settings' );
				}
			}
		}

		Redirect( $Admin->GetUrl( 'sitemap-settings' ) );
	}
}