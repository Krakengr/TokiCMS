<?php defined('TOKICMS') or die('Hacking attempt...');

class SitemapIndex extends Controller {
	
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

		//Don't continue if we don't want OR have sitemaps
		if ( !Settings::IsTrue( 'enable_sitemap' ) || !LoadSitemap( 'sitemap_index.xml' ) )
		{ 
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
			$this->view();
		}
		
		exit( 0 );
	}
}