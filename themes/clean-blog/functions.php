<?php
function CleanSocialFooter()
{
	$CurrentLang = CurrentLang();
	
	$code = $CurrentLang['lang']['code'];
	
	$socialData = $CurrentLang['data']['social'];
	
	if ( empty( $socialData ) )
		return null;
	
	$html = '
	<ul class="list-inline text-center">';
	
	foreach ( $socialData as $id => $social )
	{
		if ( empty( $social ) )
			continue;
		
		$html .= '
		<li>
			<a target="_blank" href="' . $social . '">
                <span class="fa-stack fa-lg">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-' . $id . ' fa-stack-1x fa-inverse"></i>
                </span>
            </a>
        </li>';
	}
	
	if ( Settings::IsTrue( 'enable_rss' ) ) 
	{
		$html .= '
		<li>
            <a href="' . Router::GetVariable( 'siteRealUrl' ) . 'feed' . PS . '">
                <span class="fa-stack fa-lg">
					<i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-rss fa-stack-1x fa-inverse"></i>
                </span>
            </a>
        </li>';
	}
	
	$html .= '
	</ul>';
	
	echo $html;
}

function CleanBlogMenu()
{
	return array(
			'container'      		 => 'div',
			'container_class'		 => 'collapse navbar-collapse',
			'container_id'    		 => 'bs-navbar-collapse',
			'menu_class'      		 => 'nav navbar-nav navbar-right',
			'full_depth'           	 => false,
			'limit_pages'          	 => 3,
			'items_class'   	  	 => 'item',
			'home_class'   	  		 => 'first',
			'current_class'  		 => 'active'
	);
}

function CleanBlogNav()
{
	return array(
			'container'      		 => null,
			'links_wrap'      		 => '',
			'menu_class'      		 => 'd-flex justify-content-end mb-4',
			'link_wrap'      		 => '<a class="%s" href="%s">%s</a>',
			'previous_class'   	  	 => 'btn btn-primary text-uppercase pull-right',
			'next_class'   	  	 	 => 'btn btn-primary text-uppercase pull-left',
	);
}

function CleanPortfolio( $content )
{
	global $Post;
	
	$CurrentLang = CurrentLang();
	
	$pageId = ThemeValue( 'portfolio-page' );
	
	if ( 
		!ThemeValue( 'enable-portfolio' ) || empty( $pageId ) || !is_numeric( $pageId ) || !$Post
		|| ( ( $Post->PostId() != $pageId ) || ( $Post->ParentId() != $pageId ) )
	)
		return $content;
	
	$blog = ThemeValue( 'portfolio-blog' );
	
	$numItems = ( ( empty( ThemeValue( 'post-limit' ) ) || !is_numeric( ThemeValue( 'post-limit' ) ) ) ? HOMEPAGE_ITEMS : ThemeValue( 'post-limit' ) );
	
	$cats = Cats();
	$code = $CurrentLang['lang']['code'];
	
	if ( empty( $cats ) || !isset( $cats[$code] ) )
		return;

	$buttons = $temp = '';
	
	$html = '<div align="center">%s</div> <br /> %s';
	
	$buttons .= ' <button class="btn btn-default filter-button" data-filter="all">' . __( 'all' ) . '</button>';
	
	$blogId = ( ( !empty( $blog ) && is_numeric( $blog ) && MULTIBLOG ) ? $blog : null );
	
	foreach ( $cats[$code] as $catSef => $cat ) 
	{
		$cacheFile = CacheFileName( 'home-posts_cat-' . $catSef, null, $CurrentLang['lang']['id'], null, null, $numItems, $code );
			
		if ( ValidOtherCache( $cacheFile ) )
		{
			$data = ReadCache( $cacheFile );
		}
		
		else
		{
			$db = db();
			
			$data = array();

			$q = GetPosts( 'latest', $numItems, $CurrentLang['lang']['id'], null, $cat['id'], null, false );

			$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $CurrentLang['lang']['id'] . ") AND (p.id_category = " . $cat['id'] . ") AND (p.post_type = 'post') AND (p.post_status = 'published')";

			$query = PostsDefaultQuery( $q, $numItems, 'p.added_time DESC', null, false );

			//Query: posts
			$tmp = $db->from( null, $query )->all();

			if ( $tmp )
			{
				$s = GetSettingsData( SITE_ID );
			
				if ( empty( $s ) )
				{
					continue;
				}
					
				$i = 0;
			
				foreach ( $tmp as $p )
				{
					$p = array_merge( $p, $s );
				
					$data[$i] = BuildPostVars( $p );			
					$xtraData = GetDataXtraPost( $p['id_post'] );
						
					//We need only the external url
					$data[$i]['externalUrl'] = ( isset( $xtraData['postData']['external_url'] ) ? $xtraData['postData']['external_url'] : null );
						
					$i++;
				}
					
				WriteOtherCacheFile( $data, $cacheFile );
			}
		}
			
		if ( empty( $data ) )
			continue;
			
		//Add the button if we have posts
		$buttons .= '<button class="btn btn-default filter-button" data-filter="' . $catSef . '">' . $cat['name'] . '</button> ';
			
		foreach ( $data as $post )
		{
			$temp .= '
			<div class="gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter ' . $catSef . '">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">' . $post['title'] . '</h5>
						<p class="card-text">' . $post['description'] . '</p>';
							
						if ( !empty( $post['externalUrl'] ) )
							$temp .= '<a target="_blank" href="' . $post['externalUrl'] . '" class="btn btn-primary">' . __( 'visit-website' ) . '</a>';
						
						else
							$temp .= '<a href="' . $post['postUrl'] . '" class="btn btn-primary">' . __( 'read-more' ) . '</a>';
					
					$temp .= '
					</div>
				</div>
			</div>';
		}
	}
	
	$html = sprintf( $html, $buttons, $temp );

	$content = str_replace( '{{gallery}}', $html, $content );
	
	//We don't need these values anymore
	unset( $html, $buttons, $temp );
	
	return $content;
}