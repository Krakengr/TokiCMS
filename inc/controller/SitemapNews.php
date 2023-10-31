<?php defined('TOKICMS') or die('Hacking attempt...');

class SitemapNews extends Controller
{
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
		
		//Don't continue if we have not enabled this feature
		if ( !Settings::IsTrue( 'enable_news_sitemap' ) )
		{
			Router::SetNotFound();

			$this->view();
			return;
		}
		
		$cacheFile = 'news-sitemap-' . $this->lang['lang']['code'] . '.xml';

		LoadSitemap( $cacheFile );
		
		exit( 0 );
	}
}