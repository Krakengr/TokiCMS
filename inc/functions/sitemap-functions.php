<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Sitemap Submit function
#
#####################################################
function SitemapSubmit( $sitemap_url, $urlArr = array() )
{
	$urls = array();
	
	$message = null;
	
	$sitemap = Json( Settings::Get()['sitemap_data'] );
	
	if ( empty( $urlArr ) )
	{
		if ( !empty( $sitemap ) && isset( $sitemap['search_engines'] ) && !empty( $sitemap['search_engines'] ) && is_array( $sitemap['search_engines'] ) )
		{
			foreach( $sitemap['search_engines'] as $url )
			{
				$urls[] = str_replace( '{{url}}', htmlentities( $sitemap_url ), $url ); //sprintf( $url, urlencode( $sitemap_url ) );
			}
		}
	}
	else
	{
		foreach( $urlArr as $url )
		{
			$urls[] = str_replace( '{{url}}', htmlentities( $sitemap_url ), $url ); //sprintf( $url, urlencode( $sitemap_url ) );
		}
	}
	
	if ( isset( $sitemap['enable_indexnow'] ) && $sitemap['enable_indexnow'] && !empty( $sitemap['indexnow_key'] ) )
	{
		if ( isset( $sitemap['generic_end_point'] ) && $sitemap['generic_end_point'] )
		{
			$end_point = 'https://api.indexnow.org/indexnow?url={{url}}&key={{key}}';
			
			$url = str_replace( array( '{{url}}', '{{key}}'), array( htmlentities( $sitemap_url ), $sitemap['indexnow_key'] ), $end_point );
			
			array_push( $urls, $url );
		}
		
		elseif ( isset( $sitemap['indexnow_engines'] ) && !empty( $sitemap['indexnow_engines'] ) )
		{
			include ( ARRAYS_ROOT . 'seo-arrays.php');
			
			foreach( $indexNowSearchEngines as $id => $search )
			{
				if ( in_array( $id, $sitemap['indexnow_engines'] ) )
				{
					$url = $search['url'];
					
					$urls[] = str_replace( array( '{{url}}', '{{key}}'), array( htmlentities( $sitemap_url ), $sitemap['indexnow_key'] ), $url );
				}
			}
		}
	}

	if ( empty( $urls ) )
		return false;
	
	foreach( $urls as $url )
	{
		$ping = PingSitemapFile( $url );

		if ( !$ping )
		{
			$message .= $url . '<br />';
		}
		else
		{
			//$message .= $url . ' submit success.' . PHP_EOL;
		}
	}

	return $message;
}

#####################################################
#
# Load Sitemap function
#
#####################################################
function PingSitemapFile( $url )
{
	//require_once ( CLASSES_ROOT . 'simple_html_dom.php' );
	
	//echo file_get_html( $url )->plaintext;
	
	return @file_get_contents( $url	);
}

#####################################################
#
# Load Sitemap function
#
#####################################################
function LoadSitemap( $file = null )
{
	require_once ( CLASSES_ROOT . 'Sitemap.php' );

	$sitemap = new Sitemap;

	$sitemap->sitemapFile = $file;

	$sitemap->LoadSitemap();
}

#####################################################
#
# Build Sitemap function
#
#####################################################
function BuildSitemap()
{
	require_once ( CLASSES_ROOT . 'Sitemap.php' );

	$sitemap = new Sitemap;

	$sitemap->BuildSitemap();	
}