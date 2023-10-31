<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Get the site's or current blog's type function
#
#####################################################
function GetSiteBlogType()
{
	$type = Settings::Get()['parent_type'];
	
	if ( MULTIBLOG && Router::GetVariable( 'isBlog' ) )
	{
		$blog = GetBlog( Router::GetVariable( 'blogKey' ), null, SITE_ID, CurrentLang()['lang']['id'] );
		
		$type = ( !empty( $blog ) ? $blog['type'] : 'normal' );
	}
	
	return $type;
}

#####################################################
#
# Checks if the user archives is enabled function
#
#####################################################
function UserArchives()
{
	return ( Settings::IsTrue( 'disable_author_archives' ) ? false : true );
}

#####################################################
# 
# Gets the categories function
#
#####################################################
function GetSiteCategories()
{
	global $Blog;
	
	$blogId = ( ( Router::GetVariable( 'isBlog' ) && !empty( $Blog ) ) ? $Blog['id_blog'] : 0 );
	
	$db = db();
	
	$categories = array();
	
	$query = "SELECT c.*, la.code as ls, b.sef as blog_sef, cnf.value as hide_lang, cnf2.value as categories_filter, cnf3.value as trans_data, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_category = c.id AND p.id_lang = c.id_lang AND p.post_status = 'published') as items
	FROM `" . DB_PREFIX . "categories` AS c
	INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
	INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = c.id_site AND ld.is_default = 1
	INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = c.id_site
	INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = c.id_site AND cnf.variable = 'hide_default_lang_slug'
	INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = c.id_site AND cnf2.variable = 'categories_filter'
	INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = c.id_site AND cnf3.variable = 'trans_data'
	LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
	WHERE (c.id_parent = 0) AND (c.id_site = " . SITE_ID . ") AND (c.id_lang = " . CurrentLang()['lang']['id'] . ") AND (c.id_blog = " . $blogId . ") ORDER BY name ASC";

	//Query: categories
	$cats = $db->from( null, $query )->all();

	if ( $cats )
	{
		foreach ( $cats as $cat )
		{
			$categories[] = array(
					'id' => $cat['id'],
					'items' => $cat['items'],
					'name' => stripslashes( $cat['name'] ),
					'description' => stripslashes( $cat['descr'] ),
					'url' => BuildCategoryUrl( $cat, $cat['ls'] ),
					'trans' => CategoryTrans( $cat, $cat['ls'], $cat['url'], $cat['ls'] ),
					'image' => BuildImageArray( $cat['id_image'] ),
					'groups' => ( !empty( $cat['groups_data'] ) ? Json( $cat['groups_data'] ) : null )
			);
		}
	}
}

#####################################################
#
# Add the ads in post function
#
#####################################################
function InPostAds( $post, $type )
{
	if ( !Settings::IsTrue( 'enable_ads' ) )
		return $post;

	$adMiddle = GetAds( 'post-middle', 1, $type );
	$adTop 	  = GetAds( 'post-beginning', 1, $type );
	$adBottom = GetAds( 'post-end', 1, $type );
	
	$top = $bottom = $content = $temp = '';
	
	if ( empty( $adTop ) && empty( $adMiddle ) && empty( $adBottom ) )
		return $post;
	
	if ( !empty( $adTop ) )
	{
		$top .= PHP_EOL . '<!--top ad-->' . PHP_EOL;

		$top .= '<div style="float:' . $adTop['align'] . ';margin:12px;' . ( ( $adTop['width'] > 0 ) ? 'width:' . $adTop['width'] . 'px;' : '' ) . ( ( $adTop['height'] > 0 ) ? 'height:' . $adTop['height'] . 'px;' : '' ) . ( ( $adTop['type'] == 'dummy' ) ? 'background-color:#33475b;' : '' ) . '">';

		if ( $adTop['type'] == 'plain-text' )
			$top .= $adTop['ad_code'];

		if ( $adTop['type'] == 'image' )
			$top .= '<img src="' . $adTop['img_url'] . '" align="' . $adTop['align'] . '" />';

		$top .= '</div>' . PHP_EOL;
	}
	
	if ( !empty( $adBottom ) )
	{
		$bottom .= PHP_EOL . '<!--top ad-->' . PHP_EOL;

		$bottom .= '<div style="float:' . $adBottom['align'] . ';margin:12px;' . ( ( $adBottom['width'] > 0 ) ? 'width:' . $adBottom['width'] . 'px;' : '' ) . ( ( $adBottom['height'] > 0 ) ? 'height:' . $adBottom['height'] . 'px;' : '' ) . ( ( $adBottom['type'] == 'dummy' ) ? 'background-color:#33475b;' : '' ) . '">';
						
		if ( $adBottom['type'] == 'plain-text' )
			$bottom .= $adBottom['ad_code'];

		if ( $adBottom['type'] == 'image' )
			$bottom .= '<img src="' . $adBottom['img_url'] . '" align="' . $adBottom['align'] . '" />';

		$bottom .= '</div>' . PHP_EOL;
	}
	
	if ( !empty( $adMiddle ) )
	{
		$count = substr_count( $post, '</p>' );

		if ( $count >= 6 )
		{
			$half = floor( $count / 2 );

			$tmp = explode( "</p>", $post );

			$i = 0;

			foreach ( $tmp as $p ) 
			{
				if ( empty( $p ) )
					continue;

				$i++;

				if ( $i == $half )
				{
					$temp .= PHP_EOL . '<!--middle ad-->' . PHP_EOL;

					$temp .= '<div style="float:' . $adMiddle['align'] . ';margin:12px;' . ( ( $adMiddle['width'] > 0 ) ? 'width:' . $adMiddle['width'] . 'px;' : '' ) . ( ( $adMiddle['height'] > 0 ) ? 'height:' . $adMiddle['height'] . 'px;' : '' ) . ( ( $adMiddle['type'] == 'dummy' ) ? 'background-color:#33475b;' : '' ) . '">';

					if ( $adMiddle['type'] == 'plain-text' )
						$temp .= $adMiddle['ad_code'];
					
					if ( $adMiddle['type'] == 'image' )
						$temp .= '<img src="' . $adMiddle['img_url'] . '" align="' . $adMiddle['align'] . '" />';

					$temp .= '</div>' . PHP_EOL;
				}

				$temp .= trim( $p ) . '</p>' . PHP_EOL;
			}
		}
	}
	
	else
	{
		$temp = $post;
	}

	$content = $top . $temp . $bottom;
	
	return $content;
}

#####################################################
#
# Build the text of a table element
#
#####################################################
function BuildTableImageHtml( $img, $class = null, $style = null, $data = null, $url = null, $id = null )
{
	$html = '';
	
	$has_link = false;
	
	$img_html = '<img class="' . ( !empty( $class ) ? StripContent( $class ) : ( ( !empty( $data ) && !empty( $data['class'] ) ) ? StripContent( $data['class'] ) : '' ) ) . ( !empty( $id ) ? ' span' . $id : '' ) . '"';
		
	if ( !empty( $style ) )
	{
		$img_html .= ' style="';
			
		foreach( $style as $s => $t )
		{
			if ( empty( $t ) || ( $t == 'null' ) )
				continue;

			$img_html .= $s . ':' . $t . ';';
		}
			
		$img_html .= '"';
	}

	if ( !empty( $data ) )
	{
		$img = ( ( empty( $img ) && !empty( $data['display-placeholder'] ) ) ? TOOLS_HTML . 'theme_files/assets/frontend/img/default-fallback-image.png' : $img );
			
		if ( !empty( $img ) )
		{				
			if ( !empty( $data['on-click'] ) && ( $data['on-click'] !== 'nothing' ) )
			{
				$has_link = true;
					
				if ( $data['on-click'] === 'open-cover-new-tab' )
				{
					$html .= '<a href="' . $img . '"' . ( ( $data['on-click'] === 'open-page-new-tab' ) ? ' target="_blank" rel="nofollow"' : '' ) . '>';
				}
					
				else
				{
					$html .= '<a href="' . $url . '"' . ( ( $data['on-click'] === 'open-page-new-tab' ) ? ' target="_blank" rel="nofollow"' : '' ) . '>';
				}
			}
		}
	}
	
	if ( empty( $img ) )
		return null;
	
	$html .= $img_html . ' src="' . $img . '">';
	
	if ( $has_link )
	{
		$html .= '</a>';
	}

	return $html;
}

#####################################################
#
# Build the space of a table element
#
#####################################################
function BuildTableSpaceHtml( $width = null )
{
	if ( empty( $width ) )
		return null;
	
	return '<span style="margin-left:' . $width . ';"></span>';
}

#####################################################
#
# Build the space of a table element
#
#####################################################
function BuildTableBulletHtml( $style = null )
{
	$html = '<span';
	
	
	if ( !empty( $style ) )
	{
		$html .= ' style="';
		
		foreach( $style as $s => $t )
		{
			if ( empty( $t ) )
				continue;

			$html .= $s . ':' . $t . ';';
		}
		
		$html .= '"';
	}
	
	$html .= '>&bull;</span>';
	
	return $html;
}

#####################################################
#
# Build the header of a table element
#
#####################################################
function BuildTableHeaderHtml( $data, $class = null )
{
	$type = ( ( !empty( $data ) && !empty( $data['type'] ) ) ? $data['type'] : 'span' );

	$html = '<' . $type;
	
	$html .= ( !empty( $class ) ? ' class="' . StripContent( $class ) . '"' : ( ( !empty( $data ) && !empty( $data['class'] ) ) ? ' class="' . StripContent( $data['class'] ) . '"' : '' ) );
	
	$html .= '>';
	
	$html .= StripContent( $data['value'] );
	
	$html .= '</' . $type . '>';
	
	return $html;
}

#####################################################
#
# Build the text of a table element
#
#####################################################
function BuildTableTextHtml( $value, $class = null, $style = null, $data = null, $url = '#', $id = null )
{
	$html = StripContent( $value );
	
	$type = ( ( !empty( $data ) && !empty( $data['type'] ) ) ? $data['type'] : 'span' );

	if ( !empty( $class ) || !empty( $data ) )
	{
		$html = '<' . $type;
		
		$html .= ( !empty( $class ) ? ' class="' . StripContent( $class ) . '"' : ( ( !empty( $data ) && !empty( $data['html-class'] ) ) ? ' class="' . StripContent( $data['html-class'] ) . '"' : ( !empty( $id ) ? ' class="span' . $id . '"' : '' ) ) );
		
		if ( !empty( $style ) )
		{
			$html .= ' style="';
			
			foreach( $style as $s => $t )
			{
				if ( empty( $t ) || ( $t == 'null' ) )
					continue;

				$html .= $s . ':' . $t . ';';
			}
			
			$html .= '"';
		}
		
		$html .= '>';
		
		if ( !empty( $data ) )
		{
			if ( !empty( $data['link-title'] ) )
			{
				$html .= '<a href="' . $url . '"' . ( !empty( $data['new-page'] ) ? ' target="_blank"' : '' ) . '>';
				
				$html .= StripContent( $value );
				
				$html .= '</a>';
			}
			
			elseif ( !empty( $data['on-click'] ) && ( $data['on-click'] !== 'nothing' ) )
			{
				$html .= '<a href="' . $url . '"' . ( ( $data['on-click'] === 'go-to-archive-page-new-tab' ) ? ' target="_blank" rel="nofollow"' : '' ) . '>';
				
				$html .= StripContent( $value );
				
				$html .= '</a>';
			}
			
			else
			{
				$html .= StripContent( $value );
			}
		}
		
		else
		{
			$html .= StripContent( $value );
		}

		$html .= '</' . $type . '>';
	}

	return $html;
}

#####################################################
#
# Build the text of a table element
#
#####################################################
function BuildTablePriceHtml( $arr, $class = null, $style = null, $data = null, $url = null, $id = null )
{
	if ( empty( $arr ) )
		return null;

	//$pr = min( $arr );TODO
	
	$html = '';
	
	foreach( $arr as $p )
	{
		if ( !empty( $data['currency'] ) && is_numeric( $data['currency'] ) && ( $data['currency'] != $p['currencyId'] ) )
		{
			continue;
		}
		
		$title = $p['priceFixed'];
		
		$title = ( ( ( $data['price-template'] === 'regular-price' ) && !empty( $p['regPriceRaw'] ) ) ? $p['regPriceFixed'] : ( ( ( $data['price-template'] === 'regular-sale-price' ) && !empty( $p['regPriceRaw'] ) ) ? $p['regPriceFixed'] . ' &#8725; ' . $p['priceFixed'] : ( ( ( $data['price-template'] === 'sale-regular-price' ) && !empty( $p['regPriceRaw'] ) ) ? $p['priceFixed'] . ' &#8725; ' . $p['regPriceFixed'] : $title ) ) );
		
		if ( !empty( $data['on-click'] ) && ( $data['on-click'] !== 'nothing' ) )
		{
			$html .= '<a href="' . ( ( $data['on-click'] === 'open-store-new-tab' ) ? $p['outUrl'] : $url ) . '"' . ( ( $data['on-click'] === 'open-page-new-tab' ) ? ' target="_blank"' : ( ( $data['on-click'] === 'open-store-new-tab' ) ? ' target="_blank" rel="nofollow"' : '' ) ) . '>';
				
			$html .= $title;
				
			$html .= '</a>';
		}
			
		else
		{
			$html .= $title;
		}
		
		break;
	}
	
	return $html;
}

#####################################################
#
# Build the HTML for table function
#
#####################################################
function BuildHtmlTable( $table, $data )
{
	//We need all these values
	if ( empty( $data ) || empty( $table ) || empty( $table['elements'] ) )
		return null;
	
	$html = '
		<style type="text/css">';
		
		foreach ( $table['elements'] as $id => $elem ) 
		{
			foreach( $elem['head']['data'] as $el => $dat )
			{
				if ( !empty( $dat['style'] ) )
				{
					$has_hover = false;
					
					$html .= '
					.span' . $el . ' {
					';
					
					foreach( $dat['style'] as $s => $t )
					{
						if ( empty( $t ) || ( $t == 'null' ) 
							|| ( $s == 'border-color-on-hover' ) || ( $s == 'background-color-on-hover' )
						)
							continue;

						$html .= $s . ': ' . $t . ';
						';
						
						if ( !empty( $s ) && ( ( $s == 'border-color-on-hover' ) || ( $s == 'background-color-on-hover' ) ) )
						{
							$has_hover = true;
						}
					}
					
					$html .= '
					}
					';
					
					//Add the hover effect
					if ( $has_hover )
					{
						$html .= '
							.span' . $el . ':hover {
						';
						
						foreach( $dat['style'] as $s => $t )
						{
							if ( ( $s == 'border-color-on-hover' ) || ( $s == 'background-color-on-hover' ) )
							{
								if ( $s == 'background-color-on-hover' )
								{
									$html .= 'background: ' . $t . ';
									';
								}
								
								if ( $s == 'border-color-on-hover' )
								{
									$html .= 'border-color: ' . $t . ';
									';
								}
							}
						}
						
						$html .= '
						}
						';
					}
				}
			}
			
			foreach( $elem['body']['data'] as $el => $dat )
			{
				if ( !empty( $dat['style'] ) )
				{
					$has_hover = false;
					
					$html .= '
					.span' . $el . ' {
					';
					
					foreach( $dat['style'] as $s => $t )
					{
						if ( empty( $t ) || ( $t == 'null' ) 
							|| ( $t == 'border-color-on-hover' ) || ( $t == 'background-color-on-hover' )
						)
							continue;

						$html .= $s . ': ' . $t . ';
						';
						
						if ( !empty( $s ) && ( ( $s == 'border-color-on-hover' ) || ( $s == 'background-color-on-hover' ) ) )
						{
							$has_hover = true;
						}
					}
					
					$html .= '
					}
					';
					
					//Add the hover effect
					if ( $has_hover )
					{
						$html .= '
							.span' . $el . ':hover {
						';
						
						foreach( $dat['style'] as $s => $t )
						{
							if ( ( $s == 'border-color-on-hover' ) || ( $s == 'background-color-on-hover' ) )
							{
								if ( $s == 'background-color-on-hover' )
								{
									$html .= 'background: ' . $t . ';
									';
								}
								
								if ( $s == 'border-color-on-hover' )
								{
									$html .= 'border-color: ' . $t . ';
									';
								}
							}
						}
						
						$html .= '
						}
						';
					}
				}
			}
		}
		
		$html .= '
		</style>
		';
		
		//Build the table html data
		$html .= '
		<table' . ( !empty( $table['form_data']['table_css'] ) ? ' class="' . StripContent( $table['form_data']['table_css'] ) . '"' : '' ) . '>
			<thead>
				<tr>';
				
				foreach ( $table['elements'] as $id => $elem ) 
				{
					$html .= '
					<th';
					
					if ( !empty( $elem['head']['style'] ) )
					{
						$html .= ' style="';
						
						foreach( $elem['head']['style'] as $s => $t )
						{
							if ( empty( $t ) )
								continue;
							
							$html .= $s . ':' . $t . ';';
						}
						
						$html .= '"';
					}
						
					$html .= '>';
						
					foreach( $elem['head']['data'] as $el => $dat )
					{
						if ( !empty( $dat['data'] ) )
						{
							if ( ( $dat['id'] == 'space' ) && !empty( $dat['data']['width'] ) )
							{
								$html .= BuildTableSpaceHtml ( $dat['data']['width'] );
							}
							
							if ( $dat['id'] == 'dot' )
							{
								$html .= BuildTableBulletHtml( $dat['data'] );
							}
							
							if ( $dat['id'] == 'header' )
							{
								$html .= BuildTableHeaderHtml( $dat['data'], $dat['data']['class'] );
							}
							
							if ( $dat['id'] == 'text' )
							{
								$html .= BuildTableTextHtml( $dat['data']['value'], $dat['data']['class'] );
							}
						}
					}

					$html .= '
					</th>';					
				}
			
			$html .= '
				</tr>
			</thead>
			<tbody>';
			
			foreach( $data as $post )
			{
				$img = null;
				
				if ( !empty( $post['hasCoverImage'] ) && !empty( $post['coverImage']['default'] ) )
				{
					$img = $post['coverImage']['default']['imageUrl'];
				}
		
				$html .= '
				<tr>';
				
				foreach ( $table['elements'] as $id => $elem ) 
				{
					$html .= '<td';
					
					if ( !empty( $elem['body']['style'] ) )
					{
						$html .= ' style="';
						
						foreach( $elem['body']['style'] as $s => $t )
						{
							if ( empty( $t ) )
								continue;
							
							$html .= $s . ':' . $t . ';';
						}
						
						$html .= '"';
					}
						
					$html .= '>';
						
					foreach( $elem['body']['data'] as $el => $dat )
					{
						if ( ( $dat['id'] == 'space' ) && !empty( $dat['data']['width'] ) )
						{
							$html .= BuildTableSpaceHtml ( $dat['data']['width'] );
						}
						
						if ( ( $dat['id'] == 'price' ) && !empty( $post['pricesData'] ) )
						{
							$html .= BuildTablePriceHtml( $post['pricesData'], null, null, $dat['data'], $post['postUrl'], $el );
						}
						
						if ( ( $dat['id'] == 'attribute' ) && !empty( $dat['data']['attribute'] ) && !empty( $post['attributes'] ) )
						{
							$att = (int) $dat['data']['attribute'];
							
							$attrs = $post['attributes'];

							if ( isset( $attrs[$att] ) && !empty( $attrs[$att]['value'] ) )
							{
								$html .= BuildTableTextHtml( $attrs[$att]['value'], null, null, $dat['data'], null, $el );
							}
							
							unset( $attrs, $att );
						}
						//pricesData
						if ( $dat['id'] == 'cover-image' )
						{
							$html .= BuildTableImageHtml( $img, null, null, $dat['data'], $post['postUrl'], $el );
						}
						
						if ( $dat['id'] == 'dot' )
						{
							$html .= BuildTableBulletHtml( $dat['data'] );
						}
						
						if ( $dat['id'] == 'title' )
						{
							$html .= BuildTableTextHtml( $post['title'], null, null, $dat['data'], $post['postUrl'], $el );
						}
						
						if ( $dat['id'] == 'category' && !empty( $post['category'] ) )
						{
							$html .= BuildTableTextHtml( $post['category']['name'], null, null, $dat['data'], $post['category']['url'], $el );
						}
					}
					
					$html .= '
					</td>
					';
				}
				
				$html .= '
				</tr>';
			}
			
		$html .= '
			</tbody>
		</table>';
		
	return $html;
}

#####################################################
#
# Get a list of prices
#
#####################################################
function PriceListHtml( $id, $post = null )
{
	$db = db();
	
	$ps = GetPricesData( $id, 'normal', true, $post );

	if ( !$ps )
		return null;
	
	$html = '';

	//Query: price title
	$pst = $db->from( 
	null, 
	"SELECT prices_title
	FROM `" . DB_PREFIX . "posts_data`
	WHERE (id_post = " . $id . ")"
	)->single();
	
	//Check if we have a title here
	if ( $pst && !empty( $pst['prices_title'] ) )
	{
		$html .= '<h3>' . StripContent( $pst['prices_title'] ) . '</h3>';
	}

	$html .= '<ul>';

	foreach( $ps as $p )
	{
		$html .= '<li><a href="' . $p['outUrl'] . '" target="_blank" rel="nofollow">' . $p['title'] . ' - ' . $p['storeName'];

		if ( $p['salePriceRaw'] > 0 )
		{
			$html .= ' - ';
					
			if ( $p['notFound'] )
			{
				$html .= '<del title="Not Found">';
			}
					
			if ( $p['startingPrice'] )
			{
				$html .= __( 'from' ) . ' ';
			}
					
			$html .= $p['priceFixed'];
					
			if ( $p['notFound'] )
			{
				$html .= '</del>';
			}

		}

		$html .= '</a></li>';
	}

	$html .= '</ul>';
	
	return $html;
}

#####################################################
#
# Convert a form element to HTML function
#
#####################################################
function FormElementToHtml( $data, $safeMode = false, $echo = false )
{
	if ( empty( $data ) )
		return null;

	$html = '';
	
	foreach( $data as $id => $code )
	{
		$html .= '<div class="form-group">';
		
		if ( isset( $code['data']['display-label'] ) )
		{
			$html .= '<label for="' . $code['data']['name'] . '" class="col-sm-2 col-form-label">' . $code['data']['label'] . '</label>';
		}
		
		if ( $code['elementId'] == 'header' )
		{
			$html .= '<' . $code['data']['type'] . ' class="' . $code['data']['class'] . '" id="' . $code['data']['name'] . '">' . $code['data']['value'] . '</' . $code['data']['type'] . '>';
		}
		
		if ( $code['elementId'] == 'button' )
		{
			$html .= '<button id="' . $code['data']['name'] . '" type="' . $code['data']['type'] . '" class="' . $code['data']['class'] . '"' . ( $safeMode ? ' onclick="return false;"' : ' name="' . $code['data']['name'] . '" value="' . $code['data']['value'] . '"' ) . '>' . $code['data']['button-name'] . '</button>';
		}
		
		if ( ( isset( $code['data']['prepend'] ) && !empty( $code['data']['prepend'] ) ) || ( isset( $code['data']['append'] ) && !empty( $code['data']['append'] ) ) )
		{
			$html .= '
			<div class="input-group">';
		}
		
		if ( isset( $code['data']['prepend'] ) && !empty( $code['data']['prepend'] ) )
		{
			$html .= '
			<div class="input-group-prepend">
				<span class="input-group-text">' . $code['data']['prepend'] . '</span>
			</div>';
		}
		
		if ( $code['elementId'] == 'text-field' )
		{
			$html .= '<input id="' . $code['data']['name'] . '" class="' . $code['data']['class'] . '" type="text" placeholder="' . $code['data']['placeholder'] . '" ' . ( ( isset( $code['data']['limit-length'] ) && ( $code['data']['limit-length'] > 0 ) ) ? ' maxlength="' . $code['data']['limit-length'] . '"' : '' ) . ( $safeMode ? '' : ' name="' . $code['data']['name'] . '"' . ( isset( $code['data']['required'] ) ? ' required' : '' ) ) . '>';
		}
		
		if ( $code['elementId'] == 'text-area' )
		{
			$html .= '<textarea class="' . $code['data']['class'] . '" id="' . $code['data']['name'] . '" ' . ( ( isset( $code['data']['limit-length'] ) && ( $code['data']['limit-length'] > 0 ) ) ? ' maxlength="' . $code['data']['limit-length'] . '"' : '' ) . ( $safeMode ? '' : 'name="' . $code['data']['name'] . '"' . ( isset( $code['data']['required'] ) ? ' required' : '' ) ) . ' rows="' . $code['data']['rows'] . '" placeholder="' . $code['data']['placeholder'] . '"></textarea>';
		}
		
		if ( $code['elementId'] == 'password' )
		{
			$html .= '<input id="' . $code['data']['name'] . '" class="' . $code['data']['class'] . '" type="password" placeholder="' . $code['data']['placeholder'] . '" ' . ( $safeMode ? '' : ' name="' . $code['data']['name'] . '"' . ( isset( $code['data']['required'] ) ? ' required' : '' ) ) . '>';
		}
		
		if ( isset( $code['data']['append'] ) && !empty( $code['data']['append'] ) )
		{
			$html .= '
			<div class="input-group-append">
				<span class="input-group-text">' . $code['data']['append'] . '</span>
			</div>';
		}
		
		if ( ( isset( $code['data']['prepend'] ) && !empty( $code['data']['prepend'] ) ) || ( isset( $code['data']['append'] ) && !empty( $code['data']['append'] ) ) )
		{
			$html .= '
			</div>';
		}
		
		if ( isset( $code['data']['help-text'] ) && !empty( $code['data']['help-text'] ) )
		{
			$html .= '<small>' . $code['data']['help-text'] . '</small>';
		}
		
		$html .= '</div>';
	}
	
	if ( !$echo )
		return $html;
	
	echo $html;
}

#####################################################
#
# Simple Stats function
#
#####################################################
function SimpleStats()
{
	if ( !Settings::IsTrue( 'enable_stats' ) )
		return;
	
	global $db;
	
	$tz = new DateTimeZone( Settings::Get()['timezone_set'] );
	$datetime = new DateTime( 'now', $tz );
	
	$stats = Json( Settings::Get()['stats_data'] );
	
	$log_user_agents = ( isset( $stats['log_full_user_agent_string'] ) ? $stats['log_full_user_agent_string'] : false );
	$log_bots = ( isset( $stats['log_visits_from_robots'] ) ? $stats['log_visits_from_robots'] : false );
	$ignore_ips = ( ( isset( $stats['ignore_ips'] ) && !empty( $stats['ignore_ips'] ) ) ? $stats['ignore_ips'] : null );
	
	if ( !defined( 'SIMPLE_STATS_PATH' ) )
	{
		define( 'SIMPLE_STATS_PATH', INC_ROOT . 'tools' . DS . 'simple-stats' . DS );
	}
	
	include_once ( SIMPLE_STATS_PATH .  'ua.php' );
	
	include_once ( FUNCTIONS_ROOT .  'stats-functions.php' );
	
	$data = array();
	
	$base = GetBase();
	
	$ua = new SimpleStatsUA();
	
	$CurrentLang = CurrentLang();
	
	$data['remote_ip'] = substr( determine_remote_ip(), 0, 39 );
	
	// check whether to ignore this hit
	if ( $ignore_ips )
	{
		$ignored = explode( PHP_EOL, $stats['ignore_ips'] );
		
		if ( !empty( $ignored ) && in_array( $data['remote_ip'], $ignored ) )
			return;
	}
	
	$data['resource'] = substr( utf8encode( determine_resource() ), 0, 255 );
	
	$data['resource'] = PS . str_replace( $base, '', $data['resource'] );
	
	if ( !isset( $_SERVER['HTTP_USER_AGENT'] ) || empty( $_SERVER['HTTP_USER_AGENT'] ) )
		return;
	
	$browser = $ua->parse_user_agent( $_SERVER['HTTP_USER_AGENT'] );
	$data['platform'] = $browser['platform'];
	$data['browser']  = $browser['browser'];
	$data['version']  = substr( parse_version( $browser['version'] ), 0, 15 );
	
	if ( $data['browser'] == 1 && !$log_bots )
		return;
	
	$date = $data['date'] = $datetime->format( 'Y-m-d' );
	$time = $datetime->format( 'H:i:s' );
	
	if ( $log_user_agents )
		$data['user_agent'] = substr( $_SERVER['HTTP_USER_AGENT'], 0, 255 );
	
	$resource = $time . ' ' . $data['resource'];
	$ip = $data['remote_ip'];
	
	//Don't add this site as referrer
	$referrer = ( ( isset( $_SERVER['HTTP_REFERER'] ) && !str_contains( $_SERVER['HTTP_REFERER'], GetBase() ) ) ? $_SERVER['HTTP_REFERER'] : '' );

	if ( empty( $db->dbConnected ) )
	{
		$hashIP = md5( $ip );
		
		$stats = array();
		
		$dbStats = OpenFileDB( STATS_FILE );
		
		$dbStats[$date] = ( isset( $dbStats[$date] ) ? $dbStats[$date] : array() );

		if ( isset( $dbStats[$date][$hashIP] ) )
		{
			$hits = $dbStats[$date][$hashIP]['hits'];
			$res = $dbStats[$date][$hashIP]['resource'];
			
			if ( empty( $res ) )
			{
				$res = array();
			}
			
			array_push( $res, $resource );
			//$res[] = $resource;

			$dbStats[$date][$hashIP]['hits'] = ( $hits + 1 );
			$dbStats[$date][$hashIP]['resource'] = $res;
			$dbStats[$date][$hashIP]['end_time'] = $time;
		}
		
		else
		{
			$_db = $dbStats[$date][$hashIP];
			
			if ( !$log_user_agents )
				$data['user_agent']  = '';

			$data['country']  		= ''; // always 2 chars, no need to truncate
			$data['language'] 		= substr( determine_language(), 0, 255 );
			$data['referrer'] 		= $referrer;
			$url 					= parse_url( $data['referrer'] );
			$data['referrer']		= substr( utf8encode( $data['referrer'] ), 0, 511 );
			$data['domain']   		= isset( $url['host'] ) ? substr( preg_replace( '/^www\./', '', $url['host'] ), 0, 255 ) : '';
			$data['search_terms'] 	= substr( utf8encode( determine_search_terms( $url ) ), 0, 255 );
					
			// this isn't actually used at present, but storing local timestamps without a GMT reference is asking for trouble
			$data['offset'] = $datetime->getOffset() / 60; // store in minutes
			
			foreach ( $data as $key => $value )
			{
				if ( $key == 'resource' )
					continue;
				
				$_db[$key] = $value;
			}
			
			$res = array();

			array_push( $res, $resource );
			
			$_db['hits'] = 1;
			$_db['resource'] = $res;
			$_db['start_time'] = $time;
			$_db['end_time'] = $time;
			$_db['time'] = $time;
			
			$dbStats[$date][$hashIP] = $_db;
			
			unset( $_db );
		}

		WriteFileDB ( $dbStats, STATS_FILE );
		
		unset( $hashIP, $data, $dbStats );
		
		return;
	}
	
	else
	{
		$db = db();
		
		$c = ' ';
		
		$binds = array();
		
		if ( $log_user_agents )
		{
			$c .= "AND (user_agent = '" . $data['user_agent'] . "') ";
		}
		
		else
		{
			foreach ( array( 'browser', 'version', 'platform' ) as $key )
			{
				$c .= "AND (" . $key . " = '" . $data[$key] . "') ";
			}
		}
		
		$c .= "AND (TIMEDIFF( '" .  $time . "', start_time ) < '00:30:00')";
		
		$query = "SELECT id FROM `" . DB_PREFIX . "stats` WHERE (id_site = " . SITE_ID . ") AND (date = '" . $date . "')
		AND (remote_ip = '" . $ip . "')" . $c;

		//Query: stats
		$stat = $db->from( null, $query, $binds )->single();

		if ( $stat )
		{
			$dbarr = array(
				"resource"  => array( "CONCAT( resource, :resource, '\\n' )", $resource ),
				"end_time"  => $time,
				"hits" 		=> "hits + 1"
			);
		
			$db->update( 'stats' )->where( 'id', $stat['id'] )->set( $dbarr );
			
			return;
		}
		
		else
		{			
			$data['country']  = ''; // always 2 chars, no need to truncate
			$data['language'] = substr( determine_language(), 0, 255 );
			$data['referrer'] = $referrer;
			$url = parse_url( $data['referrer'] );
			$data['referrer'] = substr( utf8encode( $data['referrer'] ), 0, 511 );
			$data['domain']   = isset( $url['host'] ) ? substr( preg_replace( '/^www\./', '', $url['host'] ), 0, 255 ) : '';
			$data['search_terms'] = substr( utf8encode( determine_search_terms( $url ) ), 0, 255 );
					
			// this isn't actually used at present, but storing local timestamps without a GMT reference is asking for trouble
			$data['offset'] = $datetime->getOffset() / 60; // store in minutes
			
			$dbarr = array();
			
			foreach ( $data as $c => $v )
			{
				if ( $c == 'resource' )
					continue;
				
				$dbarr[$c] = $v;
				
			}
			
			$dbarr["resource"] 		= array( "CONCAT( :resource, '\\n' )", $resource );
			$dbarr["end_time"] 		= $time;
			$dbarr["start_time"] 	= $time;
			$dbarr["id_site"] 		= SITE_ID;
			$dbarr["hits"] 	 		= 1;
		
			$db->insert( 'stats' )->set( $dbarr );
		}
	}
}

#####################################################
#
# Get the embed code of a custom link function
#
#####################################################
function TextLinkEmbed( $title, $description, $url, $target = '_self', $image = null, $siteName = null )
{
	$themeValues 	= ( !empty( ThemeValue( 'link-content' ) ) ? ThemeValue( 'link-content' ) : array() );
	$themeValues 	= ( isset( $themeValues['0'] ) ? $themeValues['0'] : $themeValues );
	$title 			= StripContent( $title );
	$description 	= StripContent( $description );
	$description 	= strip_tags( $description, '<ul><li><em><p>' );
	
	$imgClass	 	= ( !empty( $themeValues['image_class'] ) ? !empty( $themeValues['image_class'] ) . ' ' : '' ) . ( Settings::IsTrue( 'enable_lazyloader' ) ? 'lazyload' : '' );
	
	if ( !empty( $themeValues['container_wrap'] ) )
	{
		if ( !empty( $themeValues['image_wrap'] ) && !empty( $image ) )
		{
			$image = '<img src="' . $image . '"' . ( !empty( $imgClass ) ? ' class="' . $imgClass . '"' : '' ) . ' alt="' . htmlspecialchars( $title ) . '" />';
			
			$image = sprintf( $themeValues['image_wrap'], $image );
		}
			
		$html = sprintf( $themeValues['container_wrap'], $url, $target, $title, $description, $siteName, $image );
	}
		
	else
	{
		$html  = '<blockquote>';
		$html .= $description;
		$html .= ' - <a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
		$html .= '</blockquote>';
	}
	
	return $html;
}

#####################################################
#
# Get the iframe code function
#
#####################################################
function IFrame( $param, $amp = false, $autoPlay = false )
{
	include ( ARRAYS_ROOT . 'generic-arrays.php');
	
	$themeValues = ( !empty( ThemeValue( 'theme-video' ) ) ? ThemeValue( 'theme-video' ) : array() );
	$themeValues = ( !empty( $themeValues['0'] ) ? $themeValues['0'] : $themeValues );

	$settings 	 = ( Settings::IsTrue( 'enable_media_embedder' ) ? Json( Settings::Get()['embedder_data'] ) : array() );
	
	//Default Args
	$args = array(
		'url' 		 	 => '',
		'source' 		 => '',
		'id' 		 	 => '',
		'start' 	 	 => '',
		'end' 	 	 	 => '',
		'img' 	 	 	 => '',
		'time' 	 	 	 => '',
		'autoplay' 		 => ( $autoPlay ? '1' : '0' ),
		
		'width' => ( ( isset( $settings['default_video_player_width'] ) && ( (int) $settings['default_video_player_width'] > 0 ) ) ? $settings['default_video_player_width'] : '100%' ),
		
		'height' => ( ( isset( $settings['default_video_player_height'] ) && ( (int) $settings['default_video_player_height'] > 0 ) ) ? $settings['default_video_player_height'] : '600' ),
		
		'ampWidth' => ( ( isset( $settings['default_video_player_width_amp'] ) && ( (int) $settings['default_video_player_width_amp'] > 0 ) ) ? $settings['default_video_player_width_amp'] : '600' ),
		
		'ampHeight' => ( ( isset( $settings['default_video_player_height_amp'] ) && ( (int) $settings['default_video_player_height_amp'] > 0 ) ) ? $settings['default_video_player_height_amp'] : '400' )
	);
	
	$args = array_merge( $args, $param );
	
	if ( empty( $args['width'] ) )
	{
		$args['width'] = '100%';
	}
	
	if ( empty( $args['ampWidth'] ) )
	{
		$args['ampWidth'] = '100%';
	}
	
	if ( empty( $args['height'] ) )
	{
		$args['height'] = '600';
	}
	
	if ( empty( $args['ampHeight'] ) )
	{
		$args['ampHeight'] = '400';
	}
	
	//We can't continue with an empty ID or embed url
	if ( empty( $args['source'] ) || ( empty( $args['id'] ) && empty( $args['url'] ) ) )
		return;
	
	//Also, we can't continue without valid data
	if ( !isset( $EmbedHtmlData[$args['source']] ) )
		return;

	$sourceArgs = $EmbedHtmlData[$args['source']];

	$array_search  	= array( '{{id}}', '{{url}}', '{{start}}', '{{end}}', '{{width}}', '{{height}}', '{{autoplay}}', '{{img}}', '{{time}}' );
	$array_replace 	= array( $args['id'], $args['url'], $args['start'], $args['end'], $args['width'], $args['height'], $args['autoplay'], $args['img'], $args['time'] );
	
	$embedUrl 		= str_replace( $array_search, $array_replace, $sourceArgs['embed-url'] );
	$ampIframe 	 	= str_replace( $array_search, $array_replace, $sourceArgs['amp-iframe'] );
	$ampAltIframe 	= str_replace( $array_search, $array_replace, $sourceArgs['amp-alt-iframe'] );
	
	//Amp Iframe code
	if ( $amp )
	{
		if ( ( !empty( $args['start'] ) || !empty( $args['end'] ) ) && !empty( $sourceArgs['amp-alt-iframe'] ) )
		{
			$html = $ampAltIframe;
		}
		else
		{
			$html = $ampIframe;
		}
	}
	
	//Iframe html code
	else
	{
		$iframe  = '<iframe width="' . $args['width'] . '" height="' . $args['height'] . '" ';
			
		if ( isset( $themeValues['iframe_class'] ) )
			$iframe .= 'class="' . $themeValues['iframe_class'] . '" ';
			
		$iframe .= 'src="' . $embedUrl . '" frameborder="0" ';
			
		if ( !empty( $sourceArgs['allow'] ) )
			$iframe .= 'allow="' . $sourceArgs['allow'] . '" ';
		
		if ( $sourceArgs['extra-html'] )
			$iframe .= $sourceArgs['extra-html'];
		
		if ( isset( $themeValues['iframe_style'] ) )
			$iframe .= 'style="' . $themeValues['iframe_style'] . '"';

		$iframe .= '></iframe>';
		
		if ( !empty( $args['orUrl'] ) && isset( $settings['show_original_link'] ) && IsTrue( $settings['show_original_link'] ) )
		{
			$iframe .= '<br /><small>' . __( 'source' ) . ': <a target="_blank" rel="noopener" href="' . $args['orUrl'] . '">' . $args['orUrl'] . '</a></small>';
		}
		
		if ( !empty( $themeValues['iframe_wrap'] ) )
		{
			$html = sprintf( $themeValues['iframe_wrap'], $iframe );
		}
		else
		{
			$html = $iframe;
		}
	}
	
	unset( $themeValues, $iframe, $settings );

	return $html;
}

#####################################################
#
# Get the site image function
#
#####################################################
function SiteImage( $full = false )
{
	$img = null;
	
	if ( !Settings::IsTrue( 'blank_icon' ) )
	{
		$ims = Settings::Get()['siteImage'];

		if ( isset( $ims['default'] ) && !empty( $ims['default'] ) )
		{
			$img = ( $full ? $ims['default'] : $ims['default']['url'] );
		}
	}
	
	return $img;
}

#####################################################
#
# Build the rel icons function
#
#####################################################
function ThemeIcons( $echo = false )
{
	$html = '';
	
	if ( Settings::IsTrue( 'blank_icon' ) )
	{
		$img = 'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=';
		
		$html .= '<link rel="icon" href="' . $img . '" sizes="32x32" />' . PHP_EOL;
		$html .= '<link rel="apple-touch-icon" href="' . $img . '" />' . PHP_EOL;
	}
	else
	{
		$ims = Settings::Get()['siteImage'];

		foreach( Settings::Get()['siteImage'] as $siz => $img )
		{
			if ( $siz == 'default' )
			{
				$html .= '<link rel="shortcut icon" href="' . $img['url'] . '"';
				$html .= ( !empty( $img['type'] ) ? ' type="image/' . $img['ext'] . '"' : '' );
				$html .= ' />' . PHP_EOL;
				
				$html .= '<link rel="apple-touch-icon" href="' . $img['url'] . '" />' . PHP_EOL;
				//$html .= '<meta name="msapplication-TileImage" content="' . $img['url'] . '" />' . PHP_EOL;
			}
			else
			{
				$html .= '<link rel="icon" href="' . $img['url'] . '" sizes="' . $img['width'] . 'x' . $img['height'] . '" />' . PHP_EOL;
			}
		}
	}
	
	if ( $echo )
		echo $html;
	else
		return $html;
}

#####################################################
#
# Check if the given value exists function
#
#####################################################
function ThemeValue( $str )
{
	if ( empty( $str ) )
		return null;
	
	$ThemeData = APP::GetVar( 'ThemeData' );

	if( isset( $ThemeData[$str] ) )
		return $ThemeData[$str];

	else 
        return null;
}

#####################################################
#
# Static Home Page function
#
# Checks if the given id is the homepage, returns home page's ID or return its status
#
#####################################################
function StaticHomePage( $getId = false, $id = null )
{
	$pageId = (int) Settings::Get()['front_static_page'];
	
	if ( ( Settings::Get()['front_page'] === 'static-page' ) && ( $pageId > 0 ) )
	{
		if ( $id && is_numeric( $id ) )
			return ( ( $pageId == $id ) ? true : false );
		
		elseif ( $getId )
			return $pageId;
		
		else
			return true;
	}
	
	return false;
}


#####################################################
#
# Check if the given value exists function
#
#####################################################
function AddThemeValue( $str )
{
	if ( empty( $str ) )
		return;
	
	$ThemeData = APP::GetVar( 'ThemeData' );
	
	if ( is_array( $str ) )
	{
		$ThemeData[key( $str )] = array_values( $str );
	}
	
	else
	{
		if ( isset( $ThemeData[$str] ) )
			return;

		array_push( $ThemeData, $str );
	}
	
	APP::SetThemeVars( $ThemeData );
}

#####################################################
#
# Get the ADS function
#
#####################################################
function GetAds( $pos = null, $num = null, $type = null, $random = false )
{
	$UserGroup = UserGroup();
	$CurrentLang = CurrentLang();
	
	$adSettings = Json( Settings::Get()['ads_data'] );
	
	$random = ( ( !empty( $adSettings ) && $adSettings['rotate_ads'] ) ? true : $random );
	
	$cacheFile = CacheFileName( 'ads', null, $CurrentLang['lang']['id'] );
		
	if ( ValidCache( $cacheFile ) )
	{
		$data = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		$query = "SELECT title, type, ad_pos, ad_align, ad_img_url, ad_code, exclude_ads, groups_data, width, height
		FROM `" . DB_PREFIX . "ads` WHERE (id_site = " . SITE_ID . ") AND (id_lang = " . $CurrentLang['lang']['id'] . ") AND (disabled = 0) ORDER BY ad_order DESC";
		
		//Query: ads
		$ads = $db->from( null, $query )->all();

		if ( !$ads )
		{
			return;
		}
		
		$data = array();
		
		foreach ( $ads as $ad )
		{
			$data[$ad['ad_pos']][] = array(
				'title' => HtmlChars( $ad['title'] ),
				'type' => $ad['type'],
				'align' => $ad['ad_align'],
				'img_url' => $ad['ad_img_url'],
				'width' => $ad['width'],
				'height' => $ad['height'],
				'ad_code' => html_entity_decode( $ad['ad_code'] ),
				'exclude' => Json( $ad['exclude_ads'] ),
				'groups' => Json( $ad['groups_data'] )
			);
		}

		WriteCacheFile( $data, $cacheFile );
	}
	
	if ( empty( $data ) )
	{
		return null;
	}
	
	$temp = array();
	
	foreach ( $data as $adPos => $_ad )
	{
		foreach ( $_ad as $ad )
		{
			if ( !empty( $ad['groups'] ) && !in_array( $UserGroup, $ad['groups'] ) )
				continue;
			
			$temp[$adPos][] = $ad;
		}
	}

	$data = $temp;
		
	unset( $temp );

	//Get the ad according to its type
	if ( $type )
	{
		$temp = array();
		
		foreach ( $data as $adPos => $_ad )
		{
			foreach ( $_ad as $ad )
			{
				if 
				( 
					!empty( $ad['exclude'] ) && 
					(
						( ( $type == 'page' ) && in_array( 'pages', $ad['exclude'] ) ) 
						|| 
						( ( $type == 'post' ) && in_array( 'posts', $ad['exclude'] ) )
					)
				)
					continue;
			
				$temp[$adPos][] = $ad;
			}
		}
		
		$data = $temp;
		
		unset( $temp );
	}
	
	if ( !empty( $pos ) )
	{
		if ( !isset( $data[$pos] ) )
			return null;

		$data = $data[$pos];
	}
	
	if ( !empty( $data ) && $num && is_numeric( $num ) )
	{
		if ( $random && ( $num == 1 ) )
		{
			shuffle( $data );
			
			$data = $data['0']; //end( $data ); //$data['0'];
		}
		
		else
			$data = array_slice( $data, 0, $num );
	}

	return $data;
}

#####################################################
#
# Get current theme's position arguments function
#
#####################################################
function GetThemeArgs( $str )
{
	if ( empty( $str ) )
		return null;
	
	$ThemeData = APP::GetVar( 'ThemeData' );

	if ( !empty( $ThemeData['widget-position'] ) )
	{
		$pos = ( isset( $ThemeData['widget-position']['0'] ) ? $ThemeData['widget-position']['0'] : $ThemeData['widget-position'] );
		
		if ( isset( $pos[$str] ) && isset( $pos[$str]['args'] ) )
			return $pos[$str]['args'];
	}
	
	return null;
}

#####################################################
#
# Check if we are in a language function
#
#####################################################
function InLang()
{
	$CurrentLang = CurrentLang();
	
	if ( $CurrentLang['lang']['id'] == Settings::Lang()['id'] )
		return false;

	return true;
}

#####################################################
#
# Get the Registration agreement page function
#
#####################################################
function RegistrationAgreementPage()
{
	$CurrentLang = CurrentLang();
	
	$code = $CurrentLang['lang']['code'];
	$regPage = Settings::Json()['regPage'];
	
	if ( empty( $regPage ) || !isset( $regPage[$code] ) || empty( $regPage[$code] ) )
		return null;
	
	return $regPage[$code];
}

#####################################################
#
# Get the contact us page function
#
#####################################################
function ContactPage()
{
	$CurrentLang = CurrentLang();
	
	$code = $CurrentLang['lang']['code'];
	$contactPage = Settings::Json()['contactPage'];
	
	if ( empty( $contactPage ) || !isset( $contactPage[$code] ) || empty( $contactPage[$code] ) )
		return null;
	
	return $contactPage[$code];
}

#####################################################
#
# Get the Privacy page function
#
#####################################################
function PrivacyPage()
{
	$CurrentLang = CurrentLang();
	
	$code = $CurrentLang['lang']['code'];
	$privacyPage = Settings::Json()['privacyPage'];
	
	if ( empty( $privacyPage ) || !isset( $privacyPage[$code] ) || empty( $privacyPage[$code] ) )
		return null;
	
	return $privacyPage[$code];
}

#####################################################
#
# Return the image function
#
# TODO: Remove
#####################################################
function PictureTag( $class = '', $fullPicTag = true, $lazy = null, $decoding = null, $amp = false, $maxWidth = null, $maxHeight = null, $echo = true )
{
	global $Post;
	
	if ( !$Post || !$Post->HasCoverImage() )
		return null;
	
	$postTitle = $Post->Title();

	$arr = $Post->Cover();
	
	if ( empty( $arr ) )
		return;
	
	$themeValues = ( !empty( ThemeValue( 'theme-image' ) ) ? ThemeValue( 'theme-image' ) : null );
	$themeValues = ( isset( $themeValues['0'] ) ? $themeValues['0'] : $themeValues );
	
	if ( !empty( $themeValues ) && isset( $themeValues['image_class'] ) )
	{
		$class = $themeValues['image_class'];
	}

	$num = count( $arr );
	
	$lazy = ( $lazy ? $lazy : Settings::IsTrue( 'enable_lazyloader' ) );
	
	$html = '';
	
	$imageWidth = ( isset( $arr['default']['imageWidth'] ) ? $arr['default']['imageWidth'] : ( isset( $arr['default']['width'] ) ? $arr['default']['width'] : '' ) );

	if ( $fullPicTag && !$amp )
	{
		$html .= '<picture' . ( !empty( $class ) ? ' class="' . $class . '" ' : '' ) . '>';
		
		$html .= '<source type="image/' . $arr['default']['mimeType'] . '" srcset="' . PHP_EOL;
		
		$i = 0;
		
		foreach( $arr as $_ar => $ar )
		{
			$i++;
			
			$html .= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : ( isset( $ar['width'] ) ? $ar['width'] : '0' ) ) . 'w';
			
			if ( $i < $num )
				$html .= ', ' . PHP_EOL;
		}
		
		if ( !empty( $imageWidth ) ) 
		{
			$html .= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw,' . $imageWidth . 'px"';
		}
		
		$html .= ' />';
	}
	
	$caption = ( ( isset( $arr['default']['imageCaption'] ) && !empty( $arr['default']['imageCaption'] ) ) ? $arr['default']['imageCaption'] : null );

	$html .= '<' . ( $amp ? 'amp-img ' : 'img ' );
	
	$class .= ( ( !$amp && $lazy ) ? ' lazyload' : '' );

	$alt = ( ( isset( $arr['default']['imageAlt'] ) && !empty( $arr['default']['imageAlt'] ) ) ? $arr['default']['imageAlt'] : $postTitle );

	$alt = htmlspecialchars( $alt );

	$width = ( is_numeric( $maxWidth ) ? $maxWidth : ( isset( $arr['default']['imageWidth'] ) ? $arr['default']['imageWidth'] : ( isset( $arr['default']['width'] ) ? $arr['default']['width'] : null ) ) ); 
	
	$height = ( is_numeric( $maxHeight ) ? $maxHeight : ( isset( $arr['default']['imageHeight'] ) ? $arr['default']['imageHeight'] : ( isset( $arr['default']['height'] ) ? $arr['default']['height'] : null ) ) ); 
	
	$html .= ( !empty( $class ) ? 'class="' . $class . '" ' : '' ) . ( $width ? 'width="' . $width . '" ' : '' ) . ( $height ? 'height="' . $height . '" ' : '' ) . ( ( !$amp && $lazy ) ? 'data-' : '' ) . 'src="' . $arr['default']['imageUrl'] . '" alt="' . $alt . '"';

	if ( $num > 1 )
	{
		$html .= ' srcset="' . PHP_EOL;
		
		$i = 0;
	
		foreach( $arr as $_ar => $ar )
		{
			$i++;
			
			$html .= $ar['imageUrl'] . ' ' . ( isset( $ar['imageWidth'] ) ? $ar['imageWidth'] : ( isset( $ar['width'] ) ? $ar['width'] : '0' ) ) . 'w';
			
			if ( $i < $num )
				$html .= ', ' . PHP_EOL;
		}
		
		if ( !empty( $imageWidth ) ) 
		{
			$html .= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
		}
	}
	
	if ( $amp )
	{
		$html .= ' layout="responsive">';
		$html .= '</amp-img>';
	}
	
	else
	{
		if ( $lazy )
			$html .= ' loading="lazy"';
		
		if ( $decoding )
			$html .= ' decoding="' . $decoding . '"';

		$html .= ' />';
	}
	
	if ( $fullPicTag && !$amp )
		$html .= '</picture>';

	if ( $echo )
		echo $html;
	
	else
		return $html;
}

#####################################################
#
# Check if the user can edit a post function
#
#####################################################
function CanEditPost( $p = null )
{
	global $Post;
	
	$UserId = UserId();
	
	$postUserId = ( $p ? $p->Author()->id : ( !is_null( $Post ) ? $Post->Author()->id : null ) );
	
	if ( empty( $postUserId ) )
	{
		return false;
	}
	
	if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) )
		return true;
	
	if ( IsAllowedTo( 'manage-own-posts' ) && !empty( $UserId ) && ( $UserId === $postUserId ) )
		return true;
	
	return false;
}

#####################################################
#
# Return the edit post's link function
#
#####################################################
function EditPostLink( $p = null )
{
	global $Post;
	
	$UserId = UserId();
	
	$link = null;
	
	$postUserId = ( $p ? $p->Author()->id : ( !is_null( $Post ) ? $Post->Author()->id : null ) );
	
	$postId = ( $p ? $p->PostId() : ( !is_null( $Post ) ? $Post->PostId() : null ) );
	
	if( IsAllowedTo( 'admin-site' ) )
		$link = ADMIN_URI . 'edit-post/id/' . $postId . '/';
	
	elseif ( IsAllowedTo( 'manage-posts' ) )
	{
		if ( IsAllowedTo( 'view-admin-bar' ) )
			$link = ADMIN_URI . 'edit-post/id/' . $postId . '/';
	}
	
	elseif ( IsAllowedTo( 'manage-own-posts' ) && !empty( $UserId ) && !empty( $postUserId ) && ( $UserId == $postUserId ) )
	{
		if ( IsAllowedTo( 'view-admin-bar' ) )
			$link = ADMIN_URI . 'edit-post/id/' . $postId . '/';
	}
	
	return $link;
}

#####################################################
#
# Return the html for the tags function
#
# $sep = the tag seperator, eg ',' or ' ' or '-' etc.
#####################################################
function Tags( $echo = true, $sep = null, $container = null, $class = null, $rel = 'tag' )
{
	if ( Router::WhereAmI() != 'post' )
		return null;
		
	global $Post;
	
	if ( !$Post || empty( $Post->Tags() ) )
		return null;
	
	$_tags = '';
	
	$tags = $Post->Tags();
	
	$num = count( $tags );
	
	$i = 0;
	
	foreach( $tags as $tag )
	{
		$i++;
		
		$_tags .= '<a href="' . $tag['url'] . '" ' . ( !empty( $rel ) ? 'rel="' . $rel . '"' : '' ) . ( $class ? ' class="' . $class . '"' : '' ) . '>' . $tag['name'] . '</a>' . ( ( $sep && ( $i < $num ) ) ? $sep : '' );
	}
	
	if ( $container )
	{
		$html = sprintf( $container, $_tags );
	}
	
	else
		$html = $_tags;

	unset( $tags );
	
	if ( $echo )
		echo $html;
	
	else
		return $html;
}

#####################################################
#
# Widgets function
#
#####################################################
function Widgets( $pos = 'primary', $param = array(), $echo = true, $checkOnly = false )
{
	global $Theme, $Post;
	
	$UserGroup = UserGroup();
	$CurrentLang = CurrentLang();
	
	$themeName = App::GetVar( 'CurrentTheme' );
	
	$cacheFile = CacheFileName( 'widgets-pos_' . $pos, null, $CurrentLang['lang']['id'] );

	if ( ValidCache( $cacheFile ) )
	{
		$data = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();		
		
		$query = "SELECT *
		FROM `" . DB_PREFIX . "widgets`
		WHERE (id_site = " . SITE_ID . ") AND (id_lang = " . $CurrentLang['lang']['id'] . ") AND (disabled = 0) AND (theme_pos = :pos) AND (theme = :theme)";
		
		
		$binds = array( ':pos' => $pos, ':theme' => $themeName );
		$binds = array(
			$pos 		=> ':pos',
			$themeName 	=> ':theme'
		);
		//Query: widgets
		$q = $db->from( null, $query, $binds )->all();

		if ( empty( $q ) )
			return null;
		
		foreach( $q as $a )
		{
			$data[$a['id']] = $a;
			$data[$a['id']]['groups'] = ( !empty( $a['groups_data'] ) ? Json( $a['groups_data'] ) : null );
			
			if ( empty( $a['id_ad'] ) )
				continue;
			
			//Query: ads
			$ads = $db->from( null, "
			SELECT *
			FROM `" . DB_PREFIX . "ads`
			WHERE (id = " . $a['id_ad'] . ")
			ORDER BY ad_order DESC"
			)->all();
			
			if ( $ads )
			{
				foreach( $ads as $ad )
				{
					$data[$a['id']]['ads'][$ad['id']] = $ad;
					$data[$a['id']]['ads'][$ad['id']]['exclude_ads'] = ( !empty( $ad['exclude_ads'] ) ? Json( $ad['exclude_ads'] ) : null );
					$data[$a['id']]['ads'][$ad['id']]['groups'] = ( !empty( $ad['groups'] ) ? Json( $ad['groups'] ) : null );
				}
			}
		}

		WriteCacheFile( $data, $cacheFile );
	}
	
	//Return if we just want to check if there are widgets enabled
	if( $checkOnly )
		return;
	
	$args = array(
			'container'      		 => '',
			'container_class'		 => '',
			'container_id'    	 	 => '',
			'before_widget' 		 => '<div id="%1$s" class="widget %2$s">',
			'after_widget' 			 => '</div>',
			'widget_class' 			 => '',
			'before_title' 	 		 => '<h2 class="widget-title">',
			'after_title'   	  	 => '</h2>',
			'widget_items_wrap' 	 => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			'widget_item_wrap' 	 	 => '<li id="%1$s" class="%2$s">%3$s</li>',
			'widget_items_name' 	 => '<h3 class="%1$s">%2$s</h3>',
			'widget_items_class'   	 => 'widget-content',
			'widget_items_counter' 	 => ' (%1$s)',
			'widget_item_class'   	 => '',
			'widget_item_id'   	 	 => 'widget',
			'widget_items_id'   	 => 'widgets',
			'widget_link_class'   	 => ''
	);
	
	if ( !empty( $param ) ) 
	{
		$args = array_merge( $args, $param );
	}
	
	$html = $widgets = '';
	
	if ( !empty( $args['container'] ) )
	{
		$html .= '<' . $args['container'] . ( !empty( $args['container_class'] ) ? ' class="' . $args['container_class'] . '"' : '' ) . ( !empty( $args['container_id'] ) ? ' id="' . $args['container_id'] . '"' : '' ) . '>';
	}
	
	foreach( $data as $w )
	{
		if ( EnabledOn( $w['enable_on'] ) || DisabledOn( $w['exclude_from'] ) )
			continue;
		
		if ( !empty( $w['groups'] ) && !in_array( $UserGroup, $w['groups'] ) )
			continue;
		
		$widget = '';
		
		if ( !empty( $args['before_widget'] ) && ( $w['type'] != 'php' ) && ( $w['build_in'] != 'search-form' ) )
		{
			$wid = ( !empty( $w['build_in'] ) ? $w['build_in'] : 'widget' ) . '-' . $w['id'];
			
			$wcl = ( !empty( $w['widget_class'] ) ? $w['widget_class'] . ' ' : '' ) . 'widget_' . $w['type'];
			
			$html .= sprintf( $args['before_widget'], $wid, $wcl );
			
			$html .= $args['before_title'] . $w['title'] . $args['after_title'];
		}

		if ( !empty( $w['build_in'] ) )
		{
			$wid = $args['widget_item_id'] . '-' . $w['id'];
			
			$wcl = $args['widget_item_class'];
				
			if ( $w['build_in'] == 'search-form' )
			{
				$temp = '<div style="text-align: center;">' . SearchForm() . '</div>';
				
				$widget .= sprintf( $args['widget_item_wrap'], $wid, $wcl, $temp );
			}
			
			if ( $w['build_in'] == 'latest-posts' )
			{				
				$posts = WidgetPost( (int) $w['num'] );

				if ( !empty( $posts ) )
				{
					foreach( $posts as $post )
					{
						if ( !empty( $args['widget_items_name'] ) )
						{
							$title 	 = sprintf( $args['widget_items_name'], $args['widget_item_class'], $post['title'] );
						}
						else
						{
							$title 	 = $post['title'];
						}
						
						$widget .= sprintf( $args['widget_item_wrap'], $args['widget_link_class'], $post['postUrl'], $title );
					}
				}
			}
			
			
			elseif ( $w['build_in'] == 'top-posts' )
			{
				$posts = WidgetPost( (int) $w['num'], 'top' );

				if ( !empty( $posts ) )
				{
					foreach( $posts as $post )
					{
						if ( !empty( $args['widget_items_name'] ) )
						{
							$title 	 = sprintf( $args['widget_items_name'], $args['widget_item_class'], $post['title'] );
						}
						else
						{
							$title 	 = $post['title'];
						}
						
						$widget .= sprintf( $args['widget_item_wrap'], $args['widget_link_class'], $post['postUrl'], $title );
					}
				}
			}

			elseif ( $w['build_in'] == 'latest-comments' )
			{
				$comm = WidgetComments( (int) $w['num'] );

				if ( !empty( $comm ) )
				{
					foreach( $comm as $id => $com )
					{
						$name 	 = $com['name'] . ' ' . __( 'on' ) . ' ' . $com['title'];
						
						if ( !empty( $args['widget_items_name'] ) )
						{
							$title 	 = sprintf( $args['widget_items_name'], $args['widget_item_class'], $name );
						}
						else
						{
							$title 	 = $name;
						}
						
						$widget .= sprintf( $args['widget_item_wrap'], $args['widget_link_class'], $com['url'], $title );
					}
				}
			}
			
			elseif ( $w['build_in'] == 'languages-list' )
			{
				$temp = '<div>' . WidgetLangs( $w['show_dropdown_list'], $args ) . '</div>';
				
				if ( !$w['show_dropdown_list'] )
				{
					$widget .= sprintf( $args['widget_item_wrap'], $wid, $wcl, $temp );
				}
				else
				{
					$html .= $temp;
				}
			}
			
			elseif ( $w['build_in'] == 'categories-list' )
			{
				$temp = WidgetCategories( $w['show_num_posts'], $w['show_dropdown_list'], $args['widget_items_class'], $args['widget_item_class'], $args['widget_link_class'] );
				
				if ( !$w['show_dropdown_list'] )
				{
					$widget .= sprintf( $args['widget_item_wrap'], $wid, $wcl, $temp );
				}
				else
				{
					$html .= $temp;
				}
			}
			
			elseif ( $w['build_in'] == 'tags-list' )
			{
				$temp = WidgetTags( $w['show_num_posts'], $w['show_dropdown_list'] );
				
				if ( !$w['show_dropdown_list'] )
				{
					$widget .= sprintf( $args['widget_item_wrap'], $wid, $wcl, $temp );
				}
				else
				{
					$html .= $temp;
				}
			}
		}
		
		else
		{
			if ( $w['type'] == 'php' )
			{
				$code = eval( html_entity_decode( $w['data'] ) );
				$widget .= ( is_callable( $w['function_name'] ) ? call_user_func( $w['function_name'] ) : null );
			}
			
			elseif ( $w['type'] == 'html' )
			{
				$widget .= html_entity_decode( htmlspecialchars_decode( $w['data'] ) );
			}
			
			elseif ( $w['type'] == 'simple' )
			{
				$widget .= '<p>' . html_entity_decode( htmlspecialchars_decode( $w['data'] ) ) . '</p>';
			}
			
			elseif ( ( $w['type'] == 'ad' ) && !empty( $w['ads'] ) )
			{
				foreach( $w['ads'] as $ad )
				{
					if ( !empty( $ad['exclude_ads'] ) )
					{
						if ( !empty( $Post ) && ( ( $Post->IsPage() && in_array( 'pages', $ad['exclude_ads'] ) ) || ( !$Post->IsPage() && in_array( 'posts', $ad['exclude_ads'] ) ) ) )
							continue;
					}
					
					if ( !empty( $ad['groups'] ) && !in_array( $UserGroup, $ad['groups'] ) )
						continue;
					
					$widget .= '<div style="float:' . $ad['ad_align'] . ';margin:12px;' . ( ( $ad['width'] > 0 ) ? 'width:' . $ad['width'] . 'px;' : '' ) . 
								( ( $ad['height'] > 0 ) ? 'height:' . $ad['height'] . 'px;' : '' ) . ( ( $ad['type'] == 'dummy' ) ? 'background-color:#33475b;' : '' ) . '">';
					
					if ( $ad['type'] == 'plain-text' )
						$widget .= html_entity_decode( $ad['ad_code'] );
					
					if ( $ad['type'] == 'image' )
						$widget .= '<img src="' . $ad['ad_img_url'] . '" align="' . $ad['ad_align'] . '" />';
				}
			}
		}
		
		$html .= sprintf( $args['widget_items_wrap'], $args['widget_items_class'], $widget );
			
		if ( !empty( $args['after_widget'] ) && ( $w['type'] != 'php' ) && ( $w['build_in'] != 'search-form' ) )
			$html .= $args['after_widget'] . PHP_EOL;
	}
	
	if ( !empty( $args['container'] ) )
		$html .= '</' . $args['container'] . '>' . PHP_EOL;
	
	if ( $echo )
		echo $html;
	else
		return $html;
}

#####################################################
#
# Widget List function
#
#####################################################
function Widget( $html = '' )
{
	if ( empty( $html ) )
		return '';
	
	$args = array(
			'container'      		 => ( ( !empty( $param ) && ( isset( $param['container'] ) || is_null( $param['container'] ) ) ) ? $param['container'] : '' ),
			'container_class'		 => ( ( !empty( $param ) && isset( $param['container_class'] ) ) ? $param['container_class'] : '' ),
			'container_id'    	 	 => ( ( !empty( $param ) && isset( $param['container_id'] ) ) ? $param['container_id'] : '' ),
			'before_widget' 		 => ( ( !empty( $param ) && isset( $param['before_widget'] ) ) ? $param['before_widget'] : '<div id="%1$s" class="widget %2$s">' ),
			'after_widget' 			 => ( ( !empty( $param ) && isset( $param['after_widget'] ) ) ? $param['after_widget'] : '/div>' ),
			
			'before_title' 	 		 => ( ( !empty( $param ) && isset( $param['before_title'] ) ) ? $param['before_title'] : '<h3 class="widget-title">' ),
			'after_title'   	  	 => ( ( !empty( $param ) && isset( $param['after_title'] ) ) ? $param['after_title'] : '</h3>' ),
	);

	$nav = '';
	$links = '';
	
	if ( !empty( $args['container'] ) )
		$nav .= '<' . $args['container'] . ( !empty( $args['container_class'] ) ? ' class="' . $args['container_class'] . '"' : '' ) . ( !empty( $args['container_role'] ) ? ' role="' . $args['container_role'] . '"' : '' ) . ' aria-label="' . __( 'posts' ) . '">';

	if ( Paginator::NumberOfPages() > 1 )
	{
		if ( Paginator::HasNewer() )
		{
			$url = sprintf( $args['link_wrap'], $args['next_class'], Paginator::NewerPageUrl(), $args['next_link_title'] );

			if ( !empty( $args['links_wrap'] ) )
				$links .= sprintf( $args['links_wrap'], $args['next_class'], $url ) . PHP_EOL;
			else
				$links .= $url;
		}
		
		if ( Paginator::HasOlder() )
		{
			$url = sprintf( $args['link_wrap'], $args['previous_class'], Paginator::OlderPageUrl(), $args['previous_link_title'] );
			
			if ( !empty( $args['links_wrap'] ) )
				$links .= sprintf( $args['links_wrap'], $args['previous_class'], $url ) . PHP_EOL;
			else
				$links .= $url;
		}
	}
	
	$nav .= sprintf( $args['items_wrap'], $args['menu_id'], $args['menu_class'] . ( ( !empty( $args['empty_previous_class'] ) && !Paginator::HasNewer() ) ? ' ' . $args['empty_previous_class'] : '' ), $links ) . PHP_EOL;
	
	if ( !empty( $args['container'] ) )
		$nav .= '</' . $args['container'] . '>' . PHP_EOL;

	if ( $echo )
	{
		echo $nav;
		unset( $nav );
	}
	
	else
		return $nav;
}

#####################################################
#
# Widgets Tags function
#
#####################################################
function WidgetTags( $numPosts = true, $dropDown = true, $items = null, $arr = false )
{
	$CurrentLang = CurrentLang();
	
	$html = '';
	
	$cacheFile = CacheFileName( 'widgets-tags', null, $CurrentLang['lang']['id'] );

	if ( ValidCache( $cacheFile ) )
	{
		$data = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		$query = "SELECT t.*, la.code as ls, la.locale as lc
		FROM `" . DB_PREFIX . "tags` AS t
		LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = t.id_lang
		WHERE " . ( ( MULTILANG && !Settings::IsTrue( 'share_tags_langs' ) ) ? "(t.id_lang = " . $CurrentLang['lang']['id'] . ") AND " : "" ) . "(t.num_items > 0)
		ORDER BY t.num_items DESC" . ( $items ? " LIMIT " . $items : "" );

		//Query: tags
		$tags = $db->from( null, $query )->all();
		
		if ( empty( $tags ) )
			return null;
		
		WriteCacheFile( $tags, $cacheFile );
	}
	
	if ( $arr )
	{
		return $tags;
	}
	
	if ( $dropDown )
	{
		$tagsArr = array();
		
		$html .= '<select id="tag_select" name="tag_id">';
		
		$html .= '<option value="0">...</option>';

		foreach( $tags as $tag )
		{
			$tagUrl = BuildTagUrl( $tag, $CurrentLang['lang']['code'] );
			
			$tagsArr[$tag['id']] = $tagUrl;
					
			$html .= '<option value="' . $tag['id'] . '" ' . ( ( Router::GetVariable( 'isTag' ) && ( $Tag['id'] == $tag['id'] ) ) ? 'selected' : '' ) . '>' . $tag['title'] . ( $numPosts ? ' (' . $tag['num_items'] . ')' : '' ) . '</option>';
		}

		$html .= '</select>' . PHP_EOL;

		$html .= '<script type="text/javascript">' . PHP_EOL;
					
		$html .= 'var urls_tags = ' . json_encode( $tagsArr, JSON_PRETTY_PRINT ) . ';' . PHP_EOL;
					
		$html .= 'document.getElementById( "tag_select" ).onchange = function() {' . PHP_EOL;
				
		$html .= 'location.href = urls_tags[this.value];' . PHP_EOL;
					
		$html .= '}</script>';
	}
	
	else
	{
		$html .= '<div class="tags-links">';

		foreach( $tags as $tag )
		{
			$tagUrl = BuildTagUrl( $tag, $CurrentLang['lang']['code'] );
			
			if ( Router::GetVariable( 'isTag' ) && ( $Tag['id'] == $tag['id'] ) )
				$html .= '<strong><a href="' . $tagUrl . '">' . $tag['title'] . ( $numPosts ? ' (' . $tag['num_items'] . ')' : '' ) . '</a></strong>';
			
			else
				$html .= '<a href="' . $tagUrl . '">' . $tag['title'] . ( $numPosts ? ' (' . $tag['num_items'] . ')' : '' ) . '</a>';
		}

		$html .= '</div>';
	}
	
	return $html;
}

#####################################################
#
# Widgets Categories function
#
#####################################################
function WidgetCategories( $numPosts = true, $dropDown = true, $ulClass = null, $liClass = null, $linkClass = null )
{
	global $Category;
	
	$CurrentLang = CurrentLang();
	
	$html = '';
	
	$cacheFile = CacheFileName( 'widgets-categories', null, $CurrentLang['lang']['id'] );
	
	$catId = ( ( Router::GetVariable( 'isCat' ) && !empty( $Category['id'] ) ) ? $Category['id'] : null );

	if ( ValidCache( $cacheFile ) )
	{
		$data = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		$query = "SELECT c.*, p.sef as parent_sef, p.name as parent_name, la.code as ls, b.sef as blog_sef, cnf.value as hide_lang, cnf2.value as categories_filter, cnf3.value as trans_data, s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, ld.code as dlc, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_sub_category = c.id AND p.id_lang = c.id_lang AND p.post_status = 'published') as numposts
		FROM `" . DB_PREFIX . "categories` AS c
		INNER JOIN `" . DB_PREFIX . "categories` as p ON p.id = c.id_parent
		INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = c.id_lang
		INNER JOIN `" . DB_PREFIX . "languages` as ld ON ld.id_site = c.id_site AND ld.is_default = 1
		INNER JOIN `" . DB_PREFIX . "sites` as s ON s.id = c.id_site
		INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site = c.id_site AND cnf.variable = 'hide_default_lang_slug'
		INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = c.id_site AND cnf2.variable = 'categories_filter'
		INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = c.id_site AND cnf3.variable = 'trans_data'
		LEFT  JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = c.id_blog
		WHERE (ca.id_parent = 0) AND (ca.id_lang = " . $CurrentLang['lang']['id'] . ") AND (ca.id_site = " . SITE_ID . ")
		ORDER BY ca.name ASC";

		//Query: categories
		$tmp = $db->from( null, $query )->all();
		
		if ( empty( $tmp ) )
			return null;
		
		$data = array();
		
		$i = 0;
		
		foreach ( $tmp as $cat )
		{
			$data[$i] = array(
					'id' => $cat['id'],
					'name' => stripslashes( $cat['name'] ),
					'descr' => stripslashes( $cat['descr'] ),
					'slug' => $cat['sef'],
					'siteId' => $cat['id_site'],
					'blogName' => stripslashes( $cat['bn'] ),
					'siteName' => stripslashes( $cat['st'] ),
					'blogSef' => $cat['bs'],
					'transParent' => $cat['id_trans_parent'],
					'items' => $cat['numItems'],
					'color' => $cat['cat_color'],
					'lang' => ( $cat['lt'] ),
					'langId' => $cat['id_lang'],
					'langCode' => $cat['ls'],
					'postLimit' => $cat['article_limit'],
					'image' => BuildImageArray( $cat['id_image'] ),
					'groups' => Json( $cat['groups_data'] ),
					'hiddenFrontPage' => $cat['hide_front'],
					'isDefault' => ( $cat['is_default'] ? true : false ),
					'url' => BuildCategoryUrl( $cat, $cat['ls'] ),
					'filters' => GetFilters( $cat['id'], $cat['id_lang'] ),
					'trans' => CategoryTrans( $cat, $cat['ls'], $cat['url'], $cat['ls'] ),
					'childs' => array()
			);
			
			$i++;
		}
		
		WriteCacheFile( $cats, $cacheFile );
	}

	if ( $dropDown )
	{
		$catsArr = array();
		
		$html .= '<select id="cat_select" name="cat_id">';
		
		$html .= '<option value="0">...</option>';

		foreach( $cats as $catt )
		{
			$catsArr[$catt['id']] = $catt['url'];
					
			$html .= '<option value="' . $catt['id'] . '" ' . ( ( $catId && ( $catId == $catt['id'] ) ) ? 'selected' : '' ) . '>' . $catt['name'] . ( $numPosts ? ' (' . $catt['num_items'] . ')' : '' ) . '</option>';
		}

		$html .= '</select>' . PHP_EOL;

		$html .= '<script type="text/javascript">' . PHP_EOL;
					
		$html .= 'var urls_cats = ' . json_encode( $catsArr, JSON_PRETTY_PRINT ) . ';' . PHP_EOL;
					
		$html .= 'document.getElementById( "cat_select" ).onchange = function() {' . PHP_EOL;
				
		$html .= 'location.href = urls_cats[this.value];' . PHP_EOL;
					
		$html .= '}</script>';
	}
	
	else
	{
		$html .= '<ul' . ( $ulClass ? ' class="' . $ulClass . '"' : '' ) . '>';

		foreach( $cats as $catt )
		{
			if ( $catId && ( $catId == $catt['id'] ) )
				$html .= '<li' . ( $liClass ? ' class="' . $liClass . '"' : '' ) . '><strong><a' . ( $linkClass ? ' class="' . $linkClass . '"' : '' ) . ' href="' . $catt['url'] . '">' . $catt['name'] . ( $numPosts ? ' (' . $catt['items'] . ')' : '' ) . '</a></strong></li>';
			
			else
				$html .= '<li' . ( $liClass ? ' class="' . $liClass . '"' : '' ) . '><a' . ( $linkClass ? ' class="' . $linkClass . '"' : '' ) . ' href="' . $catt['url'] . '">' . $catt['name'] . ( $numPosts ? ' (' . $catt['items'] . ')' : '' ) . '</a></li>';
		}

		$html .= '</ul>';
	}
	
	return $html;
}

#####################################################
#
# Widgets Langs function
#
#####################################################
function WidgetLangs( $dropDown = true )
{
	$CurrentLang = CurrentLang();

	$langList = Settings::AllLangs();
	
	$html = '';
	
	$isLang = Router::GetVariable( 'isLang' );

	if ( $dropDown )
	{
		$langs = array();
		
		$html .= '<select id="lang_select" name="lang_id">';

		$langs[Settings::Lang()['code']] = SITE_URL . ( Settings::IsTrue( 'hide_default_lang_slug' ) ? '' : Settings::Lang()['code'] . PS );

		//The default lang is always selected if there is no other lang
		$html .= '<option value="' . Settings::Lang()['code'] . '" ' . ( !$isLang ? 'selected' : '' ) . '>' . Settings::Lang()['title'] . '</option>';

		foreach ( $langList as $id => $la )
		{
			//Don't add the default lang twice
			if ( $id == Settings::Lang()['code'] )
				continue;
				
			$langs[$id] = SITE_URL . $id . PS;
					
			$html .= '<option value="' . $id . '" ' . ( ( $isLang && ( $la['lang']['code'] == Router::GetVariable( 'langKey' ) ) ) ? 'selected' : '' ) . '>' . $la['lang']['title'] . '</option>';
		}

		$html .= '</select>' . PHP_EOL;

		$html .= '<script type="text/javascript">' . PHP_EOL;
					
		$html .= 'var urls_langs = ' . json_encode( $langs, JSON_PRETTY_PRINT ) . ';' . PHP_EOL;
					
		$html .= 'document.getElementById( "lang_select" ).onchange = function() {' . PHP_EOL;
				
		$html .= 'location.href = urls_langs[this.value];' . PHP_EOL;
					
		$html .= '}</script>';
	}
	
	else
	{
		$html .= '<ul>';
		
		if ( Settings::Lang()['code'] == $CurrentLang['lang']['code'] )
			$html .= '<li><strong><a href="' . SITE_URL . ( Settings::IsTrue( 'hide_default_lang_slug' ) ? '' : Settings::Lang()['code'] . PS ) . '">' . Settings::Lang()['title'] . '</a></strong></li>';
		
		else
			$html .= '<li><a href="' . SITE_URL . ( Settings::IsTrue( 'hide_default_lang_slug' ) ? '' : Settings::Lang()['code'] . PS ) . '">' . Settings::Lang()['title'] . '</a></li>';
		
		foreach ( $langList as $id => $la )
		{
			//Don't add the default lang twice
			if ( $id == Settings::Lang()['code'] )
				continue;
			
			if ( $id == $CurrentLang['lang']['code'] )
				$html .= '<li><strong><a href="' . SITE_URL . $id . PS . '">' . $la['lang']['title'] . '</a></strong></li>';
			
			else
				$html .= '<li><a href="' . SITE_URL . $id . PS . '">' . $la['lang']['title'] . '</a></li>';
		}
		
		$html .= '</ul>';
	}
	
	return $html;
}

#####################################################
#
# Widgets comments function
#
#####################################################
function WidgetComments( $num = 5 )
{
	$CurrentLang = CurrentLang();

	$cacheFile = CacheFileName( 'widgets-comments', null, $CurrentLang['lang']['id'], null, null, $num );

	if ( ValidCache( $cacheFile ) )
	{
		$data = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();
		
		$query = "SELECT co.id, co.name, co.added_time, p.sef, p.title, b.sef as blog_sef, b.name as blog_name, COALESCE(u.real_name, u.user_name) as user_name, u.image_data, la.code as ls, la.title as lt, la.locale as ll, la.flagicon
		FROM `" . DB_PREFIX . "comments` AS co
		LEFT JOIN `" . DB_PREFIX . "blogs` AS b ON b.id_blog = co.id_blog
		LEFT JOIN `" . DB_PREFIX . POSTS . "` AS p ON p.id_post = co.id_post
		LEFT JOIN `" . DB_PREFIX . USERS . "` AS u ON u.id_member = co.user_id
		LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = co.id_lang
		WHERE (co.status = 'approved') AND (co.id_lang = " . $CurrentLang['lang']['id'] . ") AND (co.id_site = " . SITE_ID . ")
		ORDER BY co.added_time DESC LIMIT " . $num;

		//Query: comments
		$q = $db->from( null, $query )->all();
	
		if ( !$q )
		{
			return null;
		}
		
		$data = array();
			
		$url = Router::GetVariable( 'siteRealUrl' );
			
		foreach ( $q as $p )
		{
			$pUrl =  BuildPostUrl( $p, $CurrentLang['lang']['code'], $url, false );
			$data[$p['id']]['url'] = $pUrl . '#comment-' . $p['id'];
			$data[$p['id']]['name'] = $p['name'];
			$data[$p['id']]['title'] = $p['title'];
			$data[$p['id']]['date'] = postDate( $p['added_time'], false );
			$data[$p['id']]['niceTime'] = niceTime( $p['added_time'] );
		}

		WriteCacheFile( $data, $cacheFile );
	}

	return $data;
}

#####################################################
#
# Widgets Posts function
#
#####################################################
function WidgetPost( $num = 5, $sort = 'latest' )
{
	$CurrentLang = CurrentLang();
	
	$cacheFile = CacheFileName( 'widgets-posts_' . $sort, null, $CurrentLang['lang']['id'], null, null, $num );

	if ( ValidCache( $cacheFile ) )
	{
		$data = readCache( $cacheFile );
	}
	
	else
	{
		$db = db();
	
		$data = array();
		
		$q = "(p.id_site = " . SITE_ID . ") AND (p.id_lang = " . $CurrentLang['lang']['id'] . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";
			
		$query = PostsDefaultQuery( $q, $num, ( ( $sort == 'top' ) ? 'p.views' : 'p.added_time' ) . ' DESC', 'p.id_post' );

		//Query: pages
		$tmp = $db->from( null, $query )->all();

		if ( empty( $tmp ) )
		{
			return null;
		}

		foreach( $tmp as $p )
		{
			$data[] = BuildPostVars( $p );
		}

		WriteCacheFile( $data, $cacheFile );
	}
	
	return $data;
}

#####################################################
#
# Disabled On function
#
#####################################################
function DisabledOn( $string )
{
	global $Post;
	
	$CurrentLang = CurrentLang();

	if ( empty( $string ) )
		return false;
	
	if ( ( ( $string == 'page' ) || ( $string == 'post' ) ) && is_null( $Post ) )
		return true;
	
	if ( ( $string == 'page' ) && !is_null( $Post ) && $Post->IsPage() )
		return true;
		
	if ( ( $string == 'post' ) && !is_null( $Post ) && !$Post->IsPage() )
		return true;
	
	if ( ( $string == 'home' ) && ( Router::WhereAmI() == 'home' ) )
		return true;
		
	if ( ( $string == 'tag' ) && Router::GetVariable( 'isTag' ) )
		return true;
		
	if ( ( $string == 'category' ) && ( Router::GetVariable( 'isCat' ) || Router::GetVariable( 'isSubCat' ) ) )
		return true;
		
	if ( ( $string == 'archive' ) && ( Router::GetVariable( 'pageNum' ) > 1 ) )
		return true;
	
	return false;
}

#####################################################
#
# Show Debug Info function
#
#####################################################
function ShowDebugInfo()
{
	if ( DEBUG_MODE && IsAllowedTo( 'admin-site' ) && !Router::GetVariable( 'isAdmin' ) )
	{ 
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $startTime), 4);
		echo '<p align="center">' . __( 'debug-mode-warning' ) . '<br />' . sprintf( __( 'page-generated-in-time' ), $total_time ) . '</p>';
	}
}

#####################################################
#
# Enabled On function
#
#####################################################
function EnabledOn( $string )
{
	global $Post;
	
	$CurrentLang = CurrentLang();

	if ( empty( $string ) )
		return false;
	
	if ( ( ( $string == 'page' ) || ( $string == 'post' ) ) && is_null( $Post ) )
		return true;
	
	if ( ( $string == 'page' ) && !is_null( $Post ) && $Post->IsPage() )
		return true;
		
	if ( ( $string == 'post' ) && !is_null( $Post ) && !$Post->IsPage() )
		return true;
	
	if ( ( $string == 'home' ) && ( Router::WhereAmI() == 'home' ) )
		return true;
		
	if ( ( $string == 'tag' ) && Router::GetVariable( 'isTag' ) )
		return true;
		
	if ( ( $string == 'category' ) && ( Router::GetVariable( 'isCat' ) || Router::GetVariable( 'isSubCat' ) ) )
		return true;
		
	if ( ( $string == 'archive' ) && ( Router::GetVariable( 'pageNum' ) > 1 ) )
		return true;
	
	return false;
}

#####################################################
#
# Social array function
#
#####################################################
function SocialArray()
{
	$CurrentLang = CurrentLang();
	
	$code = $CurrentLang['lang']['code'];
	
	$socialData = $CurrentLang['data']['social'];
	
	$arr = array();
	
	if ( empty( $socialData ) )
		return $arr;
	
	foreach ( $socialData as $id => $social )
	{
		if ( empty( $social ) )
			continue;
		
		$arr[$id] = array( 'url' => $social, 'name' => ucfirst( $id ) );
	}
	
	return $arr;
}

#####################################################
#
# Search Button function
#
#####################################################
function SearchForm( $param = array(), $echo = true )
{
	if ( !IsAllowedTo( 'search-posts' ) )
		return;

	$search_wrap = '
	<label>
		<span class="screen-reader-text">' . __( 'search-for' ) . ':</span>
		<input type="search" class="search-field" placeholder="' . __( 'search' ) . ' &hellip;" value="%s" name="s" />
	</label>
	<input type="submit" class="search-submit" value="' . __( 'search' ) . '" />' . PHP_EOL;
	
	$args = array(
		'form_class' => ( ( !empty( $param ) && isset( $param['form_class'] ) ) ? $param['form_class'] : 'search' ),
		'search_wrap' => ( ( !empty( $param ) && isset( $param['search_wrap'] ) ) ? $param['search_wrap'] : $search_wrap ),
		'form_wrap' => ( ( !empty( $param ) && isset( $param['form_wrap'] ) ) ? $param['form_wrap'] : '<form role="search" method="post" class="%s" action="%s">%s</form>' )
	);
	
	$action = Router::GetVariable( 'siteRealUrl' ) . 'search' . PS;
	
	$value = ( isset( $_POST['s'] ) ? htmlspecialchars( $_POST['s'] ) : '' );
	
	$searchWrap = sprintf( $args['search_wrap'], $value );

	$html = sprintf( $args['form_wrap'], $args['form_class'], $action, $searchWrap ) . PHP_EOL;

	if ( $echo )
		echo $html;
	else
		return $html;
}

#####################################################
#
# Subscribe Button function
#
#####################################################
function SubscribeButton()
{
	global $Post;
	
	$UserGroup = UserGroup();
	$UserId = UserId();
	
	if ( !Settings::IsTrue( 'allow_post_notifications' ) || !$Post )
		return;
	
	if ( !IsEnabledTo( 'allow_post_notifications_in' ) )
		return;
	
	$groups = Json( Settings::Get()['allow_notifications_group'] );
	
	$fav = null;
	
	$db = db();

	if ( ( $UserGroup == 1 ) || ( !empty( $groups ) && in_array( $UserGroup, $groups ) ) ) :
		
		$binds = null;
		
		if ( $UserId == 0 )
		{
			$binds = array( GetRealIp() => ':ip' );
		}
		
		$query = "SELECT id_relation
		FROM `" . DB_PREFIX . "posts_subscriptions`
		WHERE (post_id = " . $Post->PostId() . ") AND (user_id = " . $UserId . ")" . ( ( $UserId == 0 ) ? " AND ip = :ip" : "" );
		
		$fav = $db->from( null, $query, $binds )->single();
	?>
	<!-- Subscribe to post -->
	<div id="block_sub_<?php echo $Post->PostId() ?>" class="subscr-lazydev noselect">
		<div class="subscr-left">
			<div class="subscr-title"><?php echo __( 'you-will-receive-update-notifications-by-email' ) ?></div>
			<div class="subscr-desc"></div>
		</div>
		<div class="subscr-right">
			<div class="subscr-btn<?php echo ( ( $UserId == 0 ) ? '' : ' subscr-add' ) ?>" <?php echo ( ( $UserId == 0 && !$fav ) ? ' id="display_popup"' : '' ) ?> data-id="<?php echo $Post->PostId() ?>" <?php echo ( $fav ? 'style="display: none; visibility: visible;"' : '' ) ?>><?php echo __( 'subscribe' ) ?></div>
			<div class="unsubscr-btn<?php echo ( ( $UserId == 0 ) ? '' : ' subscr-add' ) ?>" <?php echo ( ( $UserId == 0 && $fav ) ? ' id="display_popup"' : '' ) ?> data-id="<?php echo $Post->PostId() ?>" <?php echo ( $fav ? '' : 'style="display: none; visibility: visible;"' ) ?>><?php echo __( 'unsubscribe' ) ?></div>
			
			<?php if ( Settings::IsTrue( 'show_subscribers_num' ) ) : ?>
			<div class="subscr-info"><?php echo __( 'subscribers' ) ?>: <span><?php echo $Post->Subs() ?></span></div>
			<?php endif ?>
		</div>
	</div><!-- /Subscribe to post -->

	<?php if ( $UserId == 0 ) : ?>
	<div id="popup_box">
		<input type="button" id="cancel_button" value="X">
		<p id="info_text"><?php echo ( $fav ? __( 'enter-your-email-address-to-remove-your-subscription' ) : __( 'enter-your-email-address-to-subscribe-to-this-post-and-receive-email-notifications' ) ) ?></p>
		<center>
			<p><input type="email" name="email" id="inputEmail" value="" required /></p>
			<input type="button" id="subscr" class="subscr-add" data-id="<?php echo $Post->PostId() ?>" value="<?php echo __( 'subscribe' ) ?>" <?php echo ( $fav ? 'style="display: none; visibility: visible;"' : '' ) ?>>
			<input type="button" id="unsubscr" class="subscr-add" data-id="<?php echo $Post->PostId() ?>" value="<?php echo __( 'unsubscribe' ) ?>" <?php echo ( $fav ? '' : 'style="display: none; visibility: visible;"' ) ?>>
			<input type="button" id="close_button" value="<?php echo __( 'close' ) ?>">
		</center>
	</div>
	<?php endif;
	endif;
}

#####################################################
#
# Add to favorites Button function
#
#####################################################
function AddToFavoritesButton()
{
	global $Post;
	
	$UserGroup = UserGroup();
	$UserId = UserId();
	
	if ( !Settings::IsTrue( 'allow_favorite_posts' ) || !$Post )
		return;
	
	if ( !IsEnabledTo( 'allow_favorite_posts_in' ) )
		return;
	
	$db = db();
	
	$groups = Json( Settings::Get()['allow_favorite_posts_group'] );

	if ( ( $UserGroup == 1 ) || ( !empty( $groups ) && in_array( $UserGroup, $groups ) ) ) :
		
		$query = "SELECT id_relation
		FROM `" . DB_PREFIX . "posts_favorites`
		WHERE (post_id = " . $Post->PostId() . ") AND (user_id = " . $UserId . ")";
		
		$fav = $db->from( null, $query )->single();
	?><!-- Add to favorites -->
	<div class="text-center">
		<span class="faventry">
			<a href="#" class="favmod<?php echo ( $fav ? ' active' : '' ) ?>" data-id="<?php echo $Post->PostId() ?>">
				<span class="favmod-add" title="<?php echo __( 'add-to-favorites' ) ?>"><span><?php echo __( 'add-to-favorites' ) ?></span></span>
				<span class="favmod-unset" title="<?php echo __( 'remove-from-favorites' ) ?>"><span><?php echo __( 'remove-from-favorites' ) ?></span></span>
			</a>
		</span>
	</div><!-- / Add to favorites -->
	<?php endif;
}

#####################################################
#
# Pagination function
#
#####################################################
function Pagination( $param = array(), $showNums = false, $echo = true )
{
	global $Post;
	
	if ( !Settings::IsTrue( 'display_pagination_home' ) && ( Router::WhereAmI() == 'home' ) && ( !Router::GetVariable( 'isLang' ) || !Router::GetVariable( 'isBlog' ) ) )
		return;

	$args = array(
			'container'      		 => ( ( !empty( $param ) && ( isset( $param['container'] ) || is_null( $param['container'] ) ) ) ? $param['container'] : 'nav' ),
			'container_class'		 => ( ( !empty( $param ) && isset( $param['container_class'] ) ) ? $param['container_class'] : 'navigation posts-navigation' ),
			'container_role'    	 => ( ( !empty( $param ) && isset( $param['container_role'] ) ) ? $param['container_role'] : 'navigation' ),
			'menu_class'      		 => ( ( !empty( $param ) && isset( $param['menu_class'] ) ) ? $param['menu_class'] : 'nav-links' ),
			'menu_id'        		 => ( ( !empty( $param ) && isset( $param['menu_id'] ) ) ? $param['menu_id'] : '' ),
			'before'          		 => ( ( !empty( $param ) && isset( $param['before'] ) ) ? $param['before'] : '' ),
			'after'          		 => ( ( !empty( $param ) && isset( $param['after'] ) ) ? $param['after'] : '' ),

			'num_links_dots_wrap' 	 => ( ( !empty( $param ) && isset( $param['num_links_dots_wrap'] ) ) ? $param['num_links_dots_wrap'] : '<span class="page-numbers dots">&hellip;</span>' ),
			
			'num_links_wrap' 		 => ( ( !empty( $param ) && isset( $param['num_links_wrap'] ) ) ? $param['num_links_wrap'] : '<a class="page-numbers" href="%s">%d</a>' ),
			
			'current_page_num_wrap' => ( ( !empty( $param ) && isset( $param['current_page_num_wrap'] ) ) ? $param['current_page_num_wrap'] : '<span aria-current="page" class="page-numbers current">%d</span>' ),
			
			'empty_previous_class' 	 => ( ( !empty( $param ) && isset( $param['empty_previous_class'] ) ) ? $param['empty_previous_class'] : '' ),
			
			'items_wrap'      		 => ( ( !empty( $param ) && isset( $param['items_wrap'] ) ) ? $param['items_wrap'] : '<div id="%s" class="%s">%s</div>' ),
			
			'links_wrap'     		 => ( ( !empty( $param ) && ( isset( $param['links_wrap'] ) || is_null( $param['links_wrap'] ) ) ) ? $param['links_wrap'] : '<div class="%s">%s</div>' ),
			
			'link_wrap'     		 => ( ( !empty( $param ) && isset( $param['link_wrap'] ) ) ? $param['link_wrap'] : '<a class="%s" href="%s">%s</a>' ),
			
			'previous_link_wrap'     => ( ( !empty( $param ) && isset( $param['previous_link_wrap'] ) ) ? $param['previous_link_wrap'] : null ),
			
			'next_link_wrap'     	 => ( ( !empty( $param ) && isset( $param['next_link_wrap'] ) ) ? $param['next_link_wrap'] : null ),
			
			'previous_link_title' 	 => ( ( !empty( $param ) && isset( $param['previous_link_title'] ) ) ? $param['previous_link_title'] : __( 'older-posts' ) ),
			
			'next_link_title' 	 	 => ( ( !empty( $param ) && isset( $param['next_link_title'] ) ) ? $param['next_link_title'] : __( 'newer-posts' ) ),
			
			'previous_class'   	  	 => ( ( !empty( $param ) && isset( $param['previous_class'] ) ) ? $param['previous_class'] : 'nav-previous' ),
			
			'next_class'   	  	 	 => ( ( !empty( $param ) && isset( $param['next_class'] ) ) ? $param['next_class'] : 'nav-next' ),
	);
	
	$nav = '';
	$links = '';
	
	if ( !empty( $args['before'] ) )
		$nav .= $args['before'] . PHP_EOL;
	
	if ( !empty( $args['container'] ) )
		$nav .= '<' . $args['container'] . ( !empty( $args['container_class'] ) ? ' class="' . $args['container_class'] . '"' : '' ) . ( !empty( $args['container_role'] ) ? ' role="' . $args['container_role'] . '"' : '' ) . ' aria-label="' . __( 'posts' ) . '">';

	if ( Paginator::NumberOfPages() > 1 )
	{
		if ( $showNums && Paginator::HasOlder() )
		{
			$url = sprintf( $args['link_wrap'], $args['previous_class'], ( $showNums ? Paginator::NewerPageUrl() : Paginator::OlderPageUrl() ), $args['previous_link_title'] );
			
			if ( !empty( $args['previous_link_wrap'] ) )
			{
				$url = sprintf( $args['previous_link_wrap'], $args['previous_class'], Paginator::NewerPageUrl(), $args['previous_link_title'] );
			}

			if ( !empty( $args['links_wrap'] ) )
				$links .= sprintf( $args['links_wrap'], $args['previous_class'], $url ) . PHP_EOL;
			
			else
				$links .= $url;
		}
		
		if ( Paginator::HasNewer() )
		{
			$url = sprintf( $args['link_wrap'], $args['next_class'], ( $showNums ? Paginator::OlderPageUrl() : Paginator::NewerPageUrl() ), $args['next_link_title'] );
			
			if ( $showNums )
			{
				for ( $i = ( Paginator::CurrentPage() - 3 ); $i <= ( Paginator::CurrentPage() + 3 ); $i++)
				{
					if ( ( $i >= 1 ) && ( $i <= Paginator::NumberOfPages() ) )
					{
						if ( ( $i == Paginator::CurrentPage() ) && !empty( $args['current_page_num_wrap'] ) )
						{
							$links .= sprintf( $args['current_page_num_wrap'], $i ) . PHP_EOL;
						}
						
						elseif ( ( $i != Paginator::CurrentPage() ) && !empty( $args['num_links_wrap'] ) )
						{
							$links .= sprintf( $args['num_links_wrap'], Paginator::PageNumUri( $i ), $i ) . PHP_EOL;
						}
					}
				}
			}
			
			if ( !empty( $args['links_wrap'] ) )
				$links .= sprintf( $args['links_wrap'], $args['next_class'], $url ) . PHP_EOL;
			else
				$links .= $url;
		}
		
		if ( !$showNums && Paginator::HasOlder() )
		{
			$url = sprintf( $args['link_wrap'], $args['previous_class'], ( $showNums ? Paginator::NewerPageUrl() : Paginator::OlderPageUrl() ), $args['previous_link_title'] );
			
			if ( !empty( $args['next_link_wrap'] ) )
			{
				$url = sprintf( $args['next_link_wrap'], $args['previous_class'], Paginator::NewerPageUrl(), $args['previous_link_title'] );
			}

			if ( !empty( $args['links_wrap'] ) )
				$links .= sprintf( $args['links_wrap'], $args['previous_class'], $url ) . PHP_EOL;
			else
				$links .= $url;
		}
	}
	
	$nav .= sprintf( $args['items_wrap'], $args['menu_id'], $args['menu_class'] . ( ( !empty( $args['empty_previous_class'] ) && !Paginator::HasNewer() ) ? ' ' . $args['empty_previous_class'] : '' ), $links ) . PHP_EOL;
	
	if ( !empty( $args['container'] ) )
		$nav .= '</' . $args['container'] . '>' . PHP_EOL;
	
	if ( !empty( $args['after'] ) )
		$nav .= $args['after'] . PHP_EOL;

	if ( $echo )
	{
		echo $nav;
		unset( $nav );
	}
	
	else
		return $nav;
}

#####################################################
#
# Comment Post Input Form function
#
#####################################################
function ShowLegalPages( $navClass = 'col-md-6', $linksOnly = false, $lisOnly = false, $liClass = null, $linksClass = null, $echo = true )
{
	$pages = Settings::LegalPages();
	
	$code = CurrentLang()['lang']['code'];
	
	$contactPage = Json( Settings::Get()['contact_page'] );
	
	if ( empty( $pages ) && !empty( $contactPage ) )
		return;
	
	$html = '';
	
	if ( !$linksOnly && !$lisOnly )
	{
		$html .= '<ul' . ( $navClass ? ' class="' . $navClass . '"' : '' ) . '>';
	}
	
	if ( isset( $pages['terms'] ) && isset( $pages['terms'][$code] ) && !empty( $pages['terms'][$code] ) )
	{
		$url = $pages['terms'][$code]['url'];

		$html .= ( ( !$linksOnly && !$lisOnly ) ? '' : ( $lisOnly ? '<li' . ( $liClass ? ' class="' . $liClass . '"' : '' ) : '' ) . '>' ) . '<a' . ( $linksClass ? ' class="' . $linksClass . '"' : '' ) . ' href="' . $url . '">' . __( 'terms-of-service' ) . '</a>' . ( ( !$linksOnly && !$lisOnly ) ? '' : ( $lisOnly ? '</li>' : '' ) );
	}
	
	if ( isset( $pages['privacy'] ) && isset( $pages['privacy'][$code] ) && !empty( $pages['privacy'][$code] ) )
	{
		$url = $pages['privacy'][$code]['url'];
		
		$html .= ( ( !$linksOnly && !$lisOnly ) ? '' : ( $lisOnly ? '<li' . ( $liClass ? ' class="' . $liClass . '"' : '' ) : '' ) . '>' ) . '<a' . ( $linksClass ? ' class="' . $linksClass . '"' : '' ) . ' href="' . $url . '">' . __( 'privacy' ) . '</a>' . ( ( !$linksOnly && !$lisOnly ) ? '' : ( $lisOnly ? '</li>' : '' ) );
	}
	
	$contactPage = ( ( !empty( $contactPage ) && isset( $contactPage[$code] ) ) ? $contactPage[$code] : null );
	
	if ( $contactPage )
	{
		$html .= ( ( !$linksOnly && !$lisOnly ) ? '' : ( $lisOnly ? '<li' . ( $liClass ? ' class="' . $liClass . '"' : '' ) : '' ) . '>' ) . '<a' . ( $linksClass ? ' class="' . $linksClass . '"' : '' ) . ' href="' . $contactPage['url'] . '">' . __( 'contact-us' ) . '</a>' . ( ( !$linksOnly && !$lisOnly ) ? '' : ( $lisOnly ? '</li>' : '' ) );
	}
	
	if ( !$linksOnly && !$lisOnly )
	{
		$html .= '
		</ul>';
	}

	if ( !$echo )
		return $html;
	
	else
		echo $html;
}

#####################################################
#
# Privacy Policy Agreement Form function
#
#####################################################
function PrivacyPolicyAgreement( $arg = '<div class="%s"><div class="form-check">%s</div></div>', $divClass = 'col-md-12', $inputClass = 'form-check-input', $labelClass = 'form-check-label', $echo = true )
{
	if ( !Settings::IsTrue( 'require_accept_privacy_policy' ) )
		return false;
	
	$pages = Settings::LegalPages();
	
	$code = CurrentLang()['lang']['code'];
	
	if ( empty( $pages ) || !isset( $pages['privacy'] ) || !isset( $pages['privacy'][$code] ) || empty( $pages['privacy'][$code] ) )
		return;
	
	$url = $pages['privacy'][$code]['url'];
	
	$temp = '
		<input class="' . $inputClass . '" type="checkbox" value="1" name="privacy-policy-agreement" id="PrivacyPolicyAgreement" required="required">
		<label class="' . $labelClass . '" for="PrivacyPolicyAgreement">' . sprintf( __( 'i-agree-to-the-privacy-policy' ), $url ) . '</label>';

	$html = sprintf( $arg, $divClass, $temp );
	
	if ( !$echo )
		return $html;
	
	else
		echo $html;
}

#####################################################
#
# Registration Agreement Form function
#
#####################################################
function RegistrationAgreement( $arg = '<div class="%s"><div class="form-check">%s</div></div>', $divClass = 'col-md-12', $inputClass = 'form-check-input', $labelClass = 'form-check-label', $echo = true )
{
	if ( !Settings::IsTrue( 'require_accept_reg_agreement' ) )
		return false;
	
	$pages = Settings::LegalPages();
	
	$code = CurrentLang()['lang']['code'];
	
	if ( empty( $pages ) || !isset( $pages['registration'] ) || !isset( $pages['registration'][$code] ) || empty( $pages['registration'][$code] ) )
		return;
	
	$url = $pages['registration'][$code]['url'];
	
	$temp = '
		<input class="' . $inputClass . '" type="checkbox" value="1" name="registration-agreement" id="RegistrationAgreement" required="required">
		<label class="' . $labelClass . '" for="RegistrationAgreement">' . sprintf( __( 'i-agree-to-the-registration-agreement' ), $url ) . '</label>';

	$html = sprintf( $arg, $divClass, $temp );
	
	if ( !$echo )
		return $html;
	
	else
		echo $html;
}

#####################################################
#
# Terms of Service Agreement Form function
#
#####################################################
function TermsOfServiceAgreement( $form = 'comment-form', $arg = '<div class="%s"><div class="form-check">%s</div></div>', $divClass = 'col-md-12', $inputClass = 'form-check-input', $labelClass = 'form-check-label', $echo = true )
{
	$settings = Settings::PrivacySettings();
	
	if ( empty( $settings ) || !isset( $settings['require_users_agree_terms_of_service'] ) || !isset( $settings['show_required_terms_in'] ) || !$settings['require_users_agree_terms_of_service'] )
		return;
	
	if ( ( $settings['show_required_terms_in'] != 'everywhere' ) && ( $settings['show_required_terms_in'] != $form ) )
		return;

	$pages = Settings::LegalPages();
	
	$code = CurrentLang()['lang']['code'];
	
	if ( empty( $pages ) || !isset( $pages['terms'] ) || empty( $pages['terms'] ) || !isset( $pages['terms'][$code] ) || empty( $pages['terms'][$code] ) )
		return;

	$url = $pages['terms'][$code]['url'];
	$title = $pages['terms'][$code]['title'];
	
	$temp = '
		<input class="' . $inputClass . '" type="checkbox" value="1" name="terms-of-service" id="TermsOfServiceAgreement" required="required">
		<label class="' . $labelClass . '" for="TermsOfServiceAgreement">' . sprintf( __( 'i-accept-the-terms-of-service' ), $url ) . '</label>';

	$html = sprintf( $arg, $divClass, $temp );
	
	if ( !$echo )
		return $html;
	
	else
		echo $html;
}

#####################################################
#
# Contact Form function
#
#####################################################
function ContactForm( $echo = true )
{
	$settings = Settings::PrivacySettings();
	
	$UserId = UserId();

	$html = '
	<div id="contact" class="contact-area">
		<form action="' . SITE_URL . 'contact-form.php" method="post" id="contact-form" class="contact-form" novalidate>';
		
	$html .= '<span id="notes">' . __( 'required-fields-are-marked' ) . ' <span class="required">*</span><br />';
	
	$html .= '<br />';
	
	$html .= '<label for="name">' . __( 'name' ) . ' <span class="required">*</span></label><br />';
	$html .= '<input id="name" name="aname" type="text" value="" size="30" maxlength="245" required /><br />';
	
	$html .= '<label for="subject">' . __( 'subject' ) . ' <span class="required">*</span></label><br />';
	$html .= '<input id="subject" name="subject" type="text" value="" size="30" maxlength="245" /><br />';

	$html .= '<label for="email">' . __( 'email' ) . ' <span class="required">*</span></label><br />';
	$html .= '<input id="email" name="aemail" type="email" value="" size="30" maxlength="100" aria-describedby="email-notes" required /><br />';

	$html .= '<label for="message">' . __( 'message' ) . ' <span class="required">*</span></label><br />';
	$html .= '<textarea id="message" name="message" cols="45" rows="8" maxlength="65525" required></textarea><br />';

	if ( Settings::IsTrue( 'enable_honeypot' ) && 
		( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'contact-form' ) ) )
	{
		$html .= '<input class="ohhney" autocomplete="off" type="text" id="name" name="name" placeholder="Your name here">';
		
		$html .= '<input class="ohhney" autocomplete="off" type="email" id="email" name="email" placeholder="Your e-mail here">';
		}

	if ( !empty( $settings ) && isset( $settings['require_users_agree_terms_of_service'] ) && isset( $settings['show_required_terms_in'] ) && $settings['require_users_agree_terms_of_service'] )
	{
		if ( ( $settings['show_required_terms_in'] == 'everywhere' ) || ( $settings['show_required_terms_in'] == 'contact-form' ) )
		{
			$pages = Settings::LegalPages();
	
			$code = CurrentLang()['lang']['code'];
	
			if ( !empty( $pages ) && isset( $pages['terms'] ) && !empty( $pages['terms'] ) 
					&& isset( $pages['terms'][$code] ) && !empty( $pages['terms'][$code] ) )
			{
				$url = $pages['terms'][$code]['url'];
				$title = $pages['terms'][$code]['title'];
	
				$html .= '
					<input class="check-input" type="checkbox" value="1" name="terms-of-service" id="TermsOfServiceAgreement">
					<label class="form-check-label" for="TermsOfServiceAgreement">' . sprintf( __( 'i-accept-the-terms-of-service' ), $url ) . '</label>' . PHP_EOL;
			}
				
			//Add this to avoid any errors when adding a comment
			else
			{
				$html .= '
					<input class="check-input" type="checkbox" value="1" name="terms-of-service" id="TermsOfServiceAgreement">
					<label class="form-check-label" for="TermsOfServiceAgreement">' . sprintf( __( 'i-accept-the-terms-of-service' ), '#' ) . '</label>' . PHP_EOL;
			}
		}
	}
		
	if ( ( Settings::Get()['enable_recaptcha'] != 'false' ) && 
		( ( Settings::Get()['show_captcha_in_forms'] == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms'] == 'contact-form' ) ) && ( !Settings::IsTrue( 'hide_captcha_logged_in_users' ) || ( Settings::IsTrue( 'hide_captcha_logged_in_users' ) && ( empty( $UserId ) || ( $UserId == 0 ) ) ) ) && !empty( Settings::Get()[ 'recaptcha_site_key'] ) )
	{
		if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v2' )
		{
			$html .= '<div class="g-recaptcha" data-sitekey="' . Settings::Get()['recaptcha_site_key'] . '"></div>' . PHP_EOL;
		}
		else
		{
			$html .= '<input type="hidden" name="recaptcha_response" id="recaptchaResponse">' . PHP_EOL;
		}
	}
		
	$html .= '<input name="submit" type="submit" id="submit" class="submit" value="' . __( 'submit' ) . '" />' . PHP_EOL;
		
	$html .= '<input type="hidden" name="_token" value="' . csrf::token() . '" />';

	$html .= '
	</form>
	</div>';

	if ( $echo )
		echo $html;

	else
		return $html;
}