<?php defined('TOKICMS') or die('Hacking attempt...');

class GetPost
{
	protected $data;
	protected $postId;
	protected $postUrl;
	protected $postTags;
	protected $siteData;
	protected $siteUrl;
	private	  $db;
	public	  $tmp;
	public	  $id;
	public	  $siteId;
	public	  $slug;
	public	  $lang;
	public	  $amp;
	public	  $query;
	public	  $cache 			= false;
	public	  $isSingle 		= false;
	public	  $build 			= false;
	public	  $anyStatus 		= false;
	public	  $buildFullArr		= true;
	public	  $getRelatedPosts 	= true;
	public	  $getTopPosts 		= true;
	public	  $getNextPrevPosts = true;
	public	  $addPostViews 	= false;

	public function __construct()
	{
		$this->db 		= db();
		$this->amp 		= Router::GetVariable( 'isAmp' );
		$this->lang 	= CurrentLang();
	}
	
	#####################################################
	#
	# Build the full post array function
	#
	#####################################################
	private function BuildFullPostVars()
	{
		$data = BuildPostVars( $this->tmp );

		if ( !$this->buildFullArr )
		{
			$this->data = $data;
			return;
		}
		
		$hasAmp 		= $data['hasAmp'];
		$extra 			= $this->GetDataXtraPost();
		$prices 		= $this->GetPricesData( 'normal', true );
		$deals  		= $this->GetPricesData( 'coupon', true );
		$this->postUrl 	= $data['postUrl'];
		
		$arr = array(
			'content'	 		=> CreatePostContent( $data['postRaw'], $data['title'], false, $this->tmp ),
			//Save some time and create the AMP content only if it's necessary
			'ampContent' 		=> ( $hasAmp && $this->amp ? CreatePostContent( $data['postRaw'], $data['title'], true, $this->tmp ) : null ),
			'ampCoverImage' 	=> ( $hasAmp && $this->amp ? BuildAmpCoverImageHtml( $data ) : null ),
			'subs'	 			=> $this->PostSubs(),
			'comments'	 		=> ( $data['commentsEnabled'] ? $this->GetPostComments() : array() ),
			'author' 			=> BuildAuthorData( $this->tmp, true ),
			'video'				=> $this->VideoDataBuild(),
			'customTypes'		=> $this->GetCustomAssocs(),
			'images'			=> $this->GetPostImages(),
			'rating'			=> $this->PostRating(),
			'catGroups'			=> ( !empty( $this->tmp['cat_groups'] ) ? Json( $this->tmp['cat_groups'] ) : null ),
			'blogGroups'		=> ( !empty( $this->tmp['blog_groups'] ) ? Json( $this->tmp['blog_groups'] ) : null ),
			'subCatGroups'		=> ( !empty( $this->tmp['sub_groups'] ) ? Json( $this->tmp['sub_groups'] ) : null ),
			'trans'	 			=> $this->PostTrans(),
			'tags'	 			=> $this->GetPostTags(),
			'tagsHtml'	 		=> $this->BuildPostTagsHtml(),
			'previous'			=> ( ( $data['isPage'] || !$this->getNextPrevPosts ) ? null : $this->GetNextPrevPost() ),
			'next'				=> ( ( $data['isPage'] || !$this->getNextPrevPosts ) ? null : $this->GetNextPrevPost( false ) ),
			'relatedPosts'		=> ( ( $data['isPage'] || !$this->getRelatedPosts )  ? null : $this->GetRelatedPosts() ),
			'topPosts'			=> ( ( $data['isPage'] || !$this->getTopPosts ) 	 ? null : $this->GetTopPosts() ),
			'hasPrices'			=> ( !empty( $prices ) ? true : false ),
			'hasDeals'			=> ( !empty( $deals ) ? true : false ),
			'pricesData'		=> $prices,
			'dealsData'			=> $deals,
			'xtraData'			=> $extra,
			'schemaCode'		=> $this->CheckPostSchema(),
			'blocksData'		=> $this->GetBlocksData(),
			'attributes'		=> $this->GetPostAttributes(),
			'variations'		=> $this->GetPostVariations(),
			'schemas'			=> $this->BuildPostSchemas(),
			'schemaData'		=> ( !empty( $this->tmp['schema_data'] ) ? Json( $this->tmp['schema_data'] ) : null ),
			'postData' 			=> ( isset( $extra['postData'] ) ? $extra['postData'] : null ),
		);

		$this->data = array_merge( $data, $arr );
		
		unset( $data, $arr, $extra, $deals, $prices, $comm );
	}
	
	#####################################################
	#
	# Get the post's Schema function
	#
	#####################################################
	private function CheckPostSchema()
	{
		$where = "(";
	
		$where .= " enable_on = '" . ( $this->tmp['post_type'] == 'post'? 'all-posts' : 'all-pages' ) . "'";
		
		if ( IsTrue( $this->siteData['multiblog'] ) && isset( $this->tmp['id_blog'] ) && ( $this->tmp['id_blog'] > 0 ) )
			$where .= " OR ( enable_on = 'blog' AND enable_on_id = '" . $this->tmp['id_blog'] . "' )";
		
		else
			$where .= " OR enable_on = '" . ( $this->tmp['post_type'] == 'post' ? 'orphan-posts' : 'orphan-pages' ) . "'";
		
		if ( IsTrue( $this->siteData['multilang'] ) )
			$where .= " OR ( enable_on = 'lang' AND enable_on_id = '" . $this->tmp['id_lang'] . "' )";
		
		$where .= ")";
		
		$where .= " AND NOT (";
		
		$where .= " (exclude_from = '" . ( $this->tmp['post_type'] == 'post'? 'all-posts' : 'all-pages' ) . "')";
		
		if ( IsTrue( $this->siteData['multiblog'] ) && isset( $this->tmp['id_blog'] ) && ( $this->tmp['id_blog'] > 0 ) )
			$where .= " OR (exclude_from = 'blog' AND exclude_from_id = '" . $this->tmp['id_blog'] . "')";
		
		else
			$where .= " OR (exclude_from = '" . ( $this->tmp['post_type'] == 'post'? 'orphan-posts' : 'orphan-pages' ) . "')";
		
		if ( IsTrue( $this->siteData['multilang'] ) )
			$where .= " OR (exclude_from = 'lang' AND exclude_from_id = '" . $this->tmp['id_lang'] . "')";
		
		$where .= ")";

		//Query: schemas
		$tmp = $this->db->from( null, "
		SELECT fixed_data
		FROM `" . DB_PREFIX . "schemas`
		WHERE (id_site = " . $this->tmp['id_site'] . ") AND " . $where
		)->all();
		
		if ( empty( $tmp ) )
		{
			return null;
		}
		
		$code = array();
		
		foreach ( $tmp as $t )
		{
			if ( empty( $t['fixed_data'] ) )
			{
				continue;
			}
			
			$code[] = $t['fixed_data'];
		}
		
		return $code;
	}
	
	#####################################################
	#
	# Build the header code function
	#
	#####################################################
	private function HeaderCode()
	{
		$headerCode = PHP_EOL . '<!-- Post Data -->' . PHP_EOL;

		//We are going to add the AMP link here
		if ( $this->data['hasAmp'] && !$this->amp )
		{
			$headerCode .= '<link rel="amphtml" href="' . $this->data['ampUrl'] . '">' . PHP_EOL;
		}

		if ( !empty( $this->data['previous'] ) )
		{
			$headerCode .= '<link rel="prev" title="' . htmlspecialchars($this->data['previous']['title'] ) . '" href="' . $this->data['previous']['postUrl'] . '" />' . PHP_EOL;
		}

		if ( !empty( $this->data['next'] ) )
		{
			$headerCode .= '<link rel="next" title="' . htmlspecialchars( $this->data['next']['title'] ) . '" href="' . $this->data['next']['postUrl'] . '" />' . PHP_EOL;
		}
			
		if ( !$this->data['isPage'] && !empty( $this->data['category'] ) )
			$headerCode .= '<meta property="article:section" content="' . htmlspecialchars( $this->data['category']['name'] ) . '" />' . PHP_EOL;

		if ( !$this->data['isPage'] && !empty( $this->data['subcategory'] ) )
			$headerCode .= '<meta property="article:section" content="' . htmlspecialchars( $this->data['subcategory']['name'] ) . '" />' . PHP_EOL;

		if ( !empty( $this->data['tags'] ) )
		{
			foreach ( $this->data['tags'] as $tag )
				$headerCode .= '<meta property="article:tag" content="' . htmlspecialchars( $tag['name'] ) . '" />' . PHP_EOL;
		}

		//Let's take care of our video
		if ( !empty( $this->data['video'] ) )
		{
			//If we have a video, add a few more lines here
			//Cuurently and for testing purposes, only Youtube is being supported
			$headerCode .= '<meta property="og:video" content="' . $this->data['video']['url'] . '" />' . PHP_EOL;
			$headerCode .= '<meta property="og:video:type" content="text/html" />' . PHP_EOL;
			$headerCode .= '<meta property="ya:ovs:upload_date" content="' .  $this->data['added']['r'] . '" />' . PHP_EOL;
			
			if ( isset( $this->data['video']['width'] ) && !empty( $this->data['video']['width'] ) )
			{
				$headerCode .= '<meta property="og:video:width" content="' . $this->data['video']['width'] . '" />' . PHP_EOL;
			}
			
			if ( isset( $this->data['video']['height'] ) && !empty( $this->data['video']['height'] ) )
			{
				$headerCode .= '<meta property="og:video:height" content="' . $this->data['video']['height'] . '" />' . PHP_EOL;
			}
			
			if ( isset( $this->data['video']['duration'] ) && !empty( $this->data['video']['duration'] ) )
			{
				$headerCode .= '<meta property="og:video:duration" content="' . $this->data['video']['duration'] . '" />' . PHP_EOL;
			}
			
			if ( isset( $this->data['video']['adult'] ) && !empty( $this->data['video']['adult'] ) )
			{
				$headerCode .= '<meta property="ya:ovs:adult" content="' . $this->data['video']['adult'] . '" />' . PHP_EOL;
			}
			
			if ( isset( $this->data['video']['allow_embed'] ) && !empty( $this->data['video']['allow_embed'] ) )
			{
				$headerCode .= '<meta property="ya:ovs:allow_embed" content="' . $this->data['video']['allow_embed'] . '" />' . PHP_EOL;
			}
		}

		$headerCode .= '<meta name="author" content="' . htmlspecialchars( $this->data['author']['name'] ) . '" />' . PHP_EOL;

		$headerCode .= '<meta property="article:published_time" content="' . $this->data['added']['r'] . '" />' . PHP_EOL;

		if ( !empty( $this->data['updated'] ) )
			$headerCode .= '<meta property="article:modified_time" content="' . $this->data['updated']['r'] . '" />' . PHP_EOL;
		
		$headerCode .= PHP_EOL;
		
		return $headerCode;
	}
	
	#####################################################
	#
	# Build the post's schemas function
	#
	# Do it here, so we can cache the results
	#
	#####################################################
	private function BuildPostSchemas()
	{
		//TODO
		return null;
		
		$data = GetPostSchema( $this->tmp );

		if ( empty( $data ) )
			return '';

		//include ( ARRAYS_ROOT . 'seo-arrays.php');

		$schemas = array();
		
		$html = '';

		foreach( $data as $schema )
		{
			//$i = 0;
			
			//$scmData = Json( $schema['data'] );
			//$scmXtraData = $scmData['custom-data'];
			
			//$scmData = $scmData['data'];
			
			$schemas[] = ReturnSchemaByType( $this->tmp, $schema );
		}
		
		//print_r($schemas);
		//exit;
		//return $html;
}

	
	#####################################################
	#
	# Get Post Variations function
	#
	#####################################################
	private function GetPostVariations()
	{
		//First check if we have an item with this id and get its parent	
		$v = $this->db->from( null, "
		SELECT id_parent
		FROM `" . DB_PREFIX . "post_variations_items`
		WHERE (id_post = " . $this->postId . ")"
		)->single();

		if( !$v ) 
			return null;
		
		//Now get the info of this parent
		$p = $this->db->from( null, 
		"SELECT id, title, sef, description, trans_data
		FROM `" . DB_PREFIX . "post_variations`
		WHERE (id = " . $v['id_parent'] . ")"
		)->single();
		
		if( !$p ) 
			return null;
		
		$data = array(
			'id' 		 	=> $p['id'],
			'title' 	 	=> StripContent( $p['title'] ),
			'sef' 	 	  	=> StripContent( $p['sef'] ),
			'description' 	=> StripContent( $p['description'] ),
			'trans' 		=> Json( $p['trans_data'] ),
			'variations'  	=> array()	
		);
		
		//Now get every item from this parent
		$vars = $this->db->from( null, "
		SELECT *
		FROM `" . DB_PREFIX . "post_variations_items`
		WHERE (id_parent = " . $v['id_parent'] . ")
		ORDER BY var_order ASC"
		)->all();

		if ( !$vars )
			return $data;
		
		foreach ( $vars as $var )
		{
			$data['variations'][] = array(
				'id'					=> $var['id'],
				'order' 				=> $var['var_order'],
				'imageId'				=> $var['id_image'],
				'postId'				=> $var['id_post'],
				'parentId'				=> $var['id_parent'],
				'sku'					=> $var['sku'],
				'quantity'				=> $var['quantity'],
				'subtrackStock'			=> $var['subtrack_stock'],
				'frontendVisibility'	=> $var['frontend_visibility'],
				'salePrice'				=> $var['sale_price'],
				'weight'				=> $var['weight'],
				'points'				=> $var['points'],
				'subtrackPrice'			=> $var['subtrack_price'],
				'subtrackWeight'		=> $var['subtrack_weight'],
				'subtrackPoints'		=> $var['subtrack_points'],
				'title'					=> StripContent( $var['title'] ),
				'postTitle'				=> StripContent( $var['ptitle'] ),
				'sef'					=> StripContent( $var['sef'] ),
				'url'					=> StripContent( $var['url'] )
			);
		}

		return $data;
	}
	
	#####################################################
	#
	# Get Post Attribute function
	#
	#####################################################
	private function GetPostAttributes()
	{
		//Query: attributes
		$a = $this->db->from( null, 
		"SELECT a.id, a.value, a.id_attr, g.id as gid, g.name as gname, p.name, p.trans_data
		FROM `" . DB_PREFIX . "post_attribute_data` AS a
		LEFT JOIN `" . DB_PREFIX . "post_attributes` as p ON p.id = a.id_attr
		LEFT JOIN `" . DB_PREFIX . "post_attr_group` as g ON g.id = p.id_group
		WHERE 1=1 AND (a.id_post = " . $this->postId . ") ORDER BY g.group_order ASC"
		)->all();

		if ( !$a )
			return null;
		
		$data = array();
		
		foreach( $a as $at )
		{
			$data[$at['id_attr']] = array(
				'id' 		=> $at['id'],
				'groupId' 	=> $at['gid'],
				'attrId' 	=> $at['id_attr'],
				'value' 	=> StripContent( $at['value'] ),
				'name' 		=> StripContent( $at['name'] ),
				'group' 	=> StripContent( $at['gname'] ),
				'trans' 	=> ( !empty( $at['trans_data'] ) ? Json( $at['trans_data'] ) : null ),
			);
		}

		return $data;
	}

	#####################################################
	#
	# Get Blocks Data function
	#
	#####################################################
	private function GetBlocksData()
	{
		$data = null;

		//Query: Blocks data
		$x = $this->db->from( null, "
		SELECT blocks
		FROM `" . DB_PREFIX . POSTS . "`
		WHERE (id_post = " . $this->postId . ")"
		)->single();

		if ( !$x )
			return $data;

		if ( !empty( $x['blocks'] ) )
		{
			$temp = Json( $x['blocks'] );
			
			if ( !empty( $temp ) && isset( $temp['blocks'] ) )
			{
				$data = $temp['blocks'];
			}
		}

		return $data;
	}

	
	#####################################################
	#
	# Get Top Posts function
	#
	#####################################################
	private function GetTopPosts( $limit = 10  )
	{
		$postId = ( isset( $this->tmp['id_post'] ) ? $this->tmp['id_post'] : 0 );
		$blogId = ( isset( $this->tmp['id_blog'] ) ? $this->tmp['id_blog'] : 0 );
		$langId = ( isset( $this->tmp['id_lang'] ) ? $this->tmp['id_lang'] : 0 );
		
		$q = "(p.post_type = 'post') AND (p.post_status = 'published') AND (p.id_blog = " . $blogId . ") AND (p.id_lang = " . $langId . ")";
			
		$query = PostsDefaultQuery( $q, $limit, "p.views DESC", null, false );

		//Query: posts
		$tmp = $this->db->from( null, $query )->all();
		
		if ( !$tmp )
		{
			return null;
		}
		
		$data = array();
		
		foreach ( $tmp as $p )
		{
			$p = array_merge( $p, $this->siteData );
			
			$data[] = BuildPostVars( $p );
		}
		
		return $data;
	}
	
	#####################################################
	#
	# Get Related Posts function
	#
	#####################################################
	private function GetRelatedPosts( $limit = 10 )
	{
		$blogId = ( isset( $this->tmp['id_blog'] ) ? $this->tmp['id_blog'] : 0 );
		$langId = ( isset( $this->tmp['id_lang'] ) ? $this->tmp['id_lang'] : 0 );
		$catId = ( isset( $this->tmp['id_category'] ) ? $this->tmp['id_category'] : 0 );
		
		$q = "(p.id_post != " . $this->tmp['id_post'] . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (p.id_blog = " . $blogId . ") AND (p.id_lang = " . $langId . ") AND (p.id_category = " . $catId . ")";
			
		$query = PostsDefaultQuery( $q, $limit, "p.id_post DESC", null, false );

		//Query: posts
		$tmp = $this->db->from( null, $query )->all();
		
		if ( !$tmp )
		{
			return null;
		}
		
		$data = array();
		
		foreach ( $tmp as $p )
		{			
			$p = array_merge( $p, $this->siteData );
			
			$data[] = BuildPostVars( $p );
		}
		
		return $data;
	}
	
	#####################################################
	#
	# Get the Next and/or Previous Post function
	#
	#####################################################
	private function GetNextPrevPost( $previous = true )
	{
		$blogId = ( isset( $this->tmp['id_blog'] ) ? $this->tmp['id_blog'] : 0 );
		$langId = ( isset( $this->tmp['id_lang'] ) ? $this->tmp['id_lang'] : 0 );
		
		$q = "(p.id_post " . ( $previous ? '<' : '>' ) . " " . $this->tmp['id_post'] . ") AND (p.post_type = 'post') AND (p.post_status = 'published') AND (p.id_blog = " . $blogId . ") AND (p.id_lang = " . $langId . ")";
			
		$query = PostsDefaultQuery( $q, 1, "p.id_post " . ( $previous ? 'DESC' : 'ASC' ), null, false );
		
		//Query: post
		$tmp = $this->db->from( null, $query )->single();

		if ( !$tmp )
		{
			return null;
		}

		return BuildPostVars( array_merge( $tmp, $this->siteData ) );
	}
	
	#####################################################
	#
	# Returns the html post tags function
	#
	#####################################################
	private function BuildPostTagsHtml()
	{
		if ( empty( $this->postTags ) )
		{
			return null;
		}
		
		$themeValues = ( !empty( ThemeValue( 'theme-tags' ) ) ? ThemeValue( 'theme-tags' ) : array() );
		$themeValues = ( isset( $themeValues['0'] ) ? $themeValues['0'] : $themeValues );

		$_tags = '';
		$class = ( !empty( $themeValues['tag_class'] ) ? $themeValues['tag_class'] : null );
		
		$num = count( $this->postTags );
		
		$i = 0;
		
		foreach( $this->postTags as $tag )
		{
			$i++;
			
			if ( !empty( $themeValues['tag_wrap'] ) )
			{
				$_tags .= sprintf( $themeValues['tag_wrap'], $tag['url'], $tag['name'] );
			}
			
			else
			{
				$_tags .= '<a href="' . $tag['url'] . '" rel="tag"' . ( $class ? ' class="' . $class . '"' : '' ) . '>' . $tag['name'] . '</a>';
			}
			
			$_tags .= ( ( !empty( $themeValues['tag_sep'] ) && ( $i < $num ) ) ? $themeValues['tag_sep'] : '' );
		}

		if ( !empty( $themeValues['tags_wrap'] ) )
		{
			$html = sprintf( $themeValues['tags_wrap'], $_tags );
		}
		
		else
			$html = $_tags;
		
		return $html;
	}
	
	#####################################################
	#
	# Gets Assoc Tags function
	#
	#####################################################
	private function GetTheTags( $type = 0 )
	{
		//Query: tags
		$tmp = $this->db->from( null, "
		SELECT *
		FROM `" . DB_PREFIX . "tags_relationships`
		WHERE (object_id = " . $this->postId . ")
		GROUP BY taxonomy_id"
		)->all();
		
		if ( empty( $tmp ) )
			return false;

		$tags = array();
		
		foreach ( $tmp as $t )
		{
			//Query: tag
			$tag = $this->db->from( null, "
			SELECT id, sef, title, num_items
			FROM `" . DB_PREFIX . "tags`
			WHERE (id = " . $t['taxonomy_id'] . ")"
			)->single();
		
			if ( !empty( $tag ) )
			{
				$tags[] = array( 
					'id' => $tag['id'],
					'name' => StripContent( $tag['title'] ),
					'sef' => $tag['sef'],
					'numItems' => $tag['num_items']
				);

				unset( $tag );
			}
		}

		return $tags;
	}
	
	#####################################################
	#
	# Get Post Tags function
	#
	#####################################################
	private function GetPostTags()
	{
		$id_custom_type = ( isset( $this->tmp['id_custom_type'] ) ? $this->tmp['id_custom_type'] : 0 );
		
		$tags = $this->GetTheTags( $id_custom_type );
		
		$data = array();

		if ( !empty( $tags ) )
		{
			foreach( $tags as $id => $tag )
			{
				$data[] = array( 
					'name' => stripslashes( $tag['name'] ), 
					'sef' => $tag['sef'],
					'url' => LangSlugUrl( $this->tmp ) . ltrim( TagFilter( $this->tmp['ls'] ), '/' ) . $tag['sef'] . PS
				);
			}
		}
		
		$this->postTags = $data;
		
		return $data;
	}
	
	#####################################################
	#
	# Get Post Translations function
	#
	#####################################################
	private function PostTrans()
	{
		$data = array();

		$data[$this->tmp['ls']] = array(
			'url' 	=> $this->postUrl,
			'id' 	=> $this->tmp['id_post'],
			'title' => $this->tmp['title'],
			'lang' 	=> $this->tmp['ll']
		);

		//Is this a child post? We have to do some work here
		if ( !empty( $this->tmp['id_parent'] ) )
		{
			//Query: post parent
			$parent = $this->db->from( null, 
			"SELECT p.sef, p.title, p.id_post, b.sef as blog_sef, la.code as ls, la.locale as lc
			FROM `" . DB_PREFIX . POSTS . "` as p
			INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = p.id_lang
			LEFT JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = p.id_blog
			WHERE (p.id_post = " . $this->tmp['id_parent'] . ") AND (p.post_type = '" . $this->tmp['post_type'] . "') AND (p.post_status = 'published')"
			)->single();
			
			if ( !empty( $parent ) )
			{
				$url = ( ( isset( $this->tmp['url'] ) && !empty( $this->tmp['url'] ) ) ? $this->tmp['url'] : SITE_URL );
				
				if ( IsTrue( $this->tmp['multilang'] ) && !empty( $parent['ls'] ) && ( !IsTrue( $this->tmp['hide_lang'] ) || ( IsTrue( $this->tmp['hide_lang'] ) && ( $parent['ls'] != $this->tmp['ls'] ) ) ) )
					$url .= $parent['ls'] . PS;
				
				if ( !StaticHomePage( false, $parent['id_post'] ) )
				{
					$url .= ( IsTrue( $this->tmp['multiblog'] ) && !empty( $parent['blog_sef'] ) ? $parent['blog_sef'] . PS : '' ) . ltrim( PostFilter( $parent['ls'] ), '/' ) . $parent['sef'] . PS;
				}
				
				$data[$parent['ls']] = array (
					'url' 	=> $url,
					'id' 	=> $parent['id_post'],
					'title' => $parent['title'],
					'lang' 	=> $parent['lc']
				);
			}
			
			//Now check for other posts that have the same parent
			$childs = $this->db->from( null, 
			"SELECT p.sef, p.title, p.id_post, b.sef as blog_sef, la.code as ls, la.locale as lc
			FROM `" . DB_PREFIX . POSTS . "` as p
			INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = p.id_lang
			LEFT JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = p.id_blog
			WHERE (p.id_parent = " . $this->tmp['id_parent'] . ") AND (p.post_type = '" . $this->tmp['post_type'] . "') AND (p.post_status = 'published') AND (p.id_post != " . $this->tmp['id_post'] . ")"
			)->all();
			
			if ( $childs )
			{
				foreach( $childs as $child )
				{
					$url = ( ( isset( $this->tmp['url'] ) && !empty( $this->tmp['url'] ) ) ? $this->tmp['url'] : SITE_URL );
				
					if ( IsTrue( $this->tmp['multilang'] ) && !empty( $child['ls'] ) && ( !IsTrue( $this->tmp['hide_lang'] ) 
						|| ( IsTrue( $this->tmp['hide_lang'] ) && ( $child['ls'] != $this->tmp['ls'] ) ) ) 
					)
					{
						$url .= $child['ls'] . PS;
					}
						
					if ( !StaticHomePage( false, $this->tmp['id_parent'] ) )
					{
					
						$url .= ( IsTrue( $this->tmp['multiblog'] ) && !empty( $child['blog_sef'] ) ? $child['blog_sef'] . PS : '' ) . ltrim( PostFilter( $child['ls'] ), '/' ) . $child['sef'] . PS;
					}
				
					$data[$child['ls']] = array (
						'url' 	=> $url,
						'id' 	=> $child['id_post'],
						'title' => $child['title'],
						'lang'	=> $child['lc']
					);
				}
			}
		}
		//This will be easier
		else
		{
			//Now check for other posts that have the same parent
			$childs = $this->db->from( null, 
			"SELECT p.sef, p.title, p.id_post, b.sef as blog_sef, la.code as ls, la.locale as lc
			FROM `" . DB_PREFIX . POSTS . "` as p
			INNER JOIN `" . DB_PREFIX . "languages` as la ON la.id = p.id_lang
			LEFT JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = p.id_blog
			WHERE (p.id_parent = " . $this->tmp['id_post'] . ") AND (p.post_type = '" . $this->tmp['post_type'] . "') AND (p.post_status = 'published')"
			)->all();
			
			if ( $childs )
			{
				foreach( $childs as $child )
				{
					$url = ( ( isset( $this->tmp['url'] ) && !empty( $this->tmp['url'] ) ) ? $this->tmp['url'] : SITE_URL );
				
					if ( IsTrue( $this->tmp['multilang'] ) && !empty( $child['ls'] ) && ( !IsTrue( $this->tmp['hide_lang'] ) 
						|| ( IsTrue( $this->tmp['hide_lang'] ) && ( $child['ls'] != $this->tmp['ls'] ) ) ) 
					)
					{
						$url .= $child['ls'] . PS;
					}
					
					if ( !StaticHomePage( false, $this->tmp['id_post'] ) )
					{
						$url .= ( IsTrue( $this->tmp['multiblog'] ) && !empty( $child['blog_sef'] ) ? $child['blog_sef'] . PS : '' ) . ltrim( PostFilter( $child['ls'] ), '/' ) . $child['sef'] . PS;
					}
					
					$data[$child['ls']] = array (
						'url' 	=> $url,
						'id' 	=> $child['id_post'],
						'lang' 	=> $child['lc']
					);
				}
			}
		}

		return $data;
	}
	
	#####################################################
	#
	# Get Post Rating function
	#
	#####################################################
	private function PostRating()
	{
		if ( !IsTrue( $this->tmp['enable_reviews'] ) )
			return null;

		//Query: rating
		$tmp = $this->db->from( null, "
		SELECT ROUND(AVG(rating), 1) as numRating
		FROM `" . DB_PREFIX . "comments`
		WHERE (id_post = " . $this->tmp['id_post'] . ")"
		)->single();
		
		return ( !empty( $tmp ) ? $tmp['numRating'] : null );
	}
	
	#####################################################
	#
	# Get Post Images function
	#
	#####################################################
	private function GetPostImages()
	{
		//Query: images
		$imgs = $this->db->from( null, 
		"SELECT * FROM `" . DB_PREFIX . "images` WHERE (id_post = " . $this->postId . ") AND (img_type = 'post') AND (aproved = 1)"
		)->all();
		
		if ( empty( $imgs ) )
			return false;
		
		foreach( $imgs as $img )
		{
			$data[] = array(
				'id' => $img['id_image'],
				'filename' => stripslashes( $img['filename'] ),
				'title' => stripslashes( $img['title'] ),
				'alt' => stripslashes( $img['alt'] ),
				'descr' => stripslashes( $img['descr'] ),
				'caption' => stripslashes( $img['caption'] ),
				'width' => $img['width'],
				'height' => $img['height'],
				'size' => $img['size'],
				'imageUrl' => ( !empty( $img['external_url'] ) ? $img['external_url'] : FolderUrlByDate( $img['added_time'] ) . stripslashes( $img['filename'] ) )
			);
		}

		return $data;
	}

	#####################################################
	#
	# Get Post Custom Types function
	#
	#####################################################
	private function GetCustomAssocs()
	{
		//Query: post types
		$cus = $this->db->from( null, 
		"SELECT id_post_type FROM `" . DB_PREFIX . "post_types_relationships` WHERE (post_id = " . $this->postId . ")"
		)->all();

		if( !$cus ) 
			return null;
		
		$data = array();
		
		foreach ( $cus as $c )
		{
			//Query: post type
			$cu = $this->db->from( null, 
			"SELECT * FROM `" . DB_PREFIX . "post_types` WHERE (id = " . $c['id_post_type'] . ")"
			)->single();

			if ( $cu )
			{
				$data[$cu['id']] = array(
					'id'  	  	  => $cu['id'],
					'title' 	  => StripContent( $cu['title'] ),
					'sef' 	  	  => StripContent( $cu['sef'] ),
					'description' => StripContent( $cu['description'] ),
					'trans_data'  => Json( $cu['trans_data'] ),
					'image'  	  => $cu['id_image'],
					'tags'		  => $this->GetCusAssocTags( $cu['id'] )
				);

				unset( $cu );
			}
		}

		unset( $cus );
		
		return $data;
	}
	
	#####################################################
	#
	# Get Post Custom Posts Tags function
	#
	#####################################################
	private function GetCusAssocTags( $type = 0 )
	{
		//Query: tags
		$data = $this->db->from( null, 
		"SELECT r.taxonomy_id, p.id_site, s.url, t.sef
		FROM `" . DB_PREFIX . "tags_relationships` AS r
		INNER JOIN `" . DB_PREFIX . POSTS . "`   as p ON p.id_post = r.object_id
		INNER JOIN `" . DB_PREFIX . "post_types` as t ON t.id = r.id_custom_type
		INNER JOIN `" . DB_PREFIX . "sites` 	 as s ON s.id = p.id_site
		WHERE (r.object_id = " . $this->postId . ") AND (r.id_custom_type = " . (int) $type . ")"
		)->all();

		if( !$data )
		{
			return null;
		}
		
		$tags = array();
		
		foreach ( $data as $b )
		{
			$tag = $this->db->from( null, "
			SELECT id, title, sef
			FROM `" . DB_PREFIX . "tags`
			WHERE (id = " . (int) $b['taxonomy_id'] . ")"
			)->single();
			
			if ( $tag )
			{
				$tags[] = array( 
					'name' => StripContent( $tag['title'] ),
					'sef'  => $tag['sef'],
					'url'  => $b['url'] . ( !empty( $b['sef'] ) ? $b['sef'] . PS . $tag['sef'] . PS : '' ),
					'id'   => $tag['id']
				);
				
				unset( $tag );
			}
		}

		unset( $data );

		return $tags;
	}

	#####################################################
	#
	# Get Post Subscribers function
	#
	#####################################################
	private function PostSubs()
	{
		if ( !IsTrue( $this->tmp['post_not'] ) )
			return null;
		
		// Query: total subscribers
		return $this->db->from( null, "
		SELECT count(*) as total FROM `" . DB_PREFIX . "posts_subscriptions` WHERE (post_id = " . $this->postId . ")"
		)->total();	
	}

	#####################################################
	#
	# Build the video data function
	#
	#####################################################
	private function VideoDataBuild()
	{
		$data = array();
		
		if ( isset( $this->tmp['xtraData']['video'] ) && !empty( $this->tmp['xtraData']['video'] ) )
		{
			$temp = $this->tmp['xtraData']['video'];
			
			$data = array(
				'id' 			=> $temp['videoID'],
				'playlist'		=> $temp['playlistId'],
				'url' 			=> $temp['videoUrl'],
				'fromContent'   => false,
				'embed' 		=> ( isset( $temp['embed_code'] ) ? html_entity_decode( trim( $temp['embed_code'] ) ) : null ),
			);

			if ( empty( $data['embed'] ) && !empty( $temp['videoID'] ) )
			{
				$args = array(
						'source'  => 'generic',
						'width'   => $temp['videoWidth'],
						'height'  => $temp['videoHeight'],
						'id'  	  => $temp['videoID'],
						'url'  	  => 'https://www.youtube.com/embed/' . $temp['videoID'] //Only Youtube for now
				);
				
				$data['embed'] = IFrame( $args, false );
				$data['amp'] = IFrame( $args, true );
				$data['fromContent'] = false;
			}
			
			unset( $temp );
		}
			
		else 
		{
			$data = CheckVideoContent( $this->tmp );
		}
		
		return $data;
	}
	
	#####################################################
	#
	# Get Post Prices function
	#
	#####################################################
	private function GetPricesData( $type = 'normal', $orderByPrice = true )
	{
		//Query: prices
		$tmp = $this->db->from( null, 
		"SELECT p.*, s.name as st, po.title as ppt, la.code as cd,
		c.name as cu, c.code as cc, c.symbol as cs, c.format as cf, c.exchange_rate as cr,
		pi.last_time_updated as lu,	pi.last_time_checked as lc,	pi.num_retries as lr, pi.not_found as ln, pi.in_stock as ls
		FROM `" . DB_PREFIX . "prices` AS p
		INNER JOIN `" . DB_PREFIX . "stores` AS s ON s.id_store = p.id_store
		INNER JOIN `" . DB_PREFIX . POSTS . "` as po ON p.id_post = po.id_post
		INNER JOIN `" . DB_PREFIX . "languages` AS la ON la.id = po.id_lang
		INNER JOIN `" . DB_PREFIX . "currencies` AS c ON c.id = p.id_currency
		LEFT JOIN `" . DB_PREFIX . "price_info` AS pi ON pi.id_price = p.id_price
		WHERE (p.id_post = " . $this->postId . ") AND (type = '" . $type . "')
		GROUP BY p.id_price
		ORDER BY p." . ( $orderByPrice ? 'sale_price' : 'pri_order' ) . " ASC"
		)->all();
		
		if ( !$tmp )
			return null;

		$i = 0;
		
		$data = array();
		
		foreach( $tmp as $p )
		{
			$i++;
			
			$data[$i] = GetSinglePricesData( $p['id_price'] );
		}

		return $data;
	}
	
	#####################################################
	#
	# Get Xtra Content function
	#
	#####################################################
	private function GetDataXtraPost()
	{
		//Query: post data
		$tmp = $this->db->from( null, 
		"SELECT d.*, m.title
		FROM `" . DB_PREFIX . "posts_data` AS d
		LEFT JOIN `" . DB_PREFIX . "manufacturers` as m ON m.id = d.man_id
		WHERE 1=1 AND (d.id_post = " . $this->postId . ")"
		)->single();

		if ( empty( $tmp ) )
			return null;

		$data = array();
		
		if ( !empty( $tmp['man_id'] ) )
		{
			$data['manufacturer'] = array( 'id' => $tmp['man_id'], 'title' => StripContent( $tmp['title'] ) );
		}
		else
		{
			$data['manufacturer'] = null;
		}
		
		//value1 is the video data
		if ( !empty( $tmp['value1'] ) )
		{
			$temp = Json( $tmp['value1'] );

			if ( !empty( $temp ) )
			{
				if ( !empty( $temp['video_url'] ) )
					parse_str( parse_url( $temp['video_url'], PHP_URL_QUERY ), $videoVars );
				else
					$videoVars = null;

				$data['video'] = array(
						'playlistId' 	 => ( isset( $temp['id_playlist'] ) ? $temp['id_playlist'] : null ),
						'videoUrl' 		 =>  ( isset( $temp['video_url'] ) ? $temp['video_url'] : null ),
						'embedCode' 	 =>  ( isset( $temp['embed_code'] ) ? html_entity_decode( $temp['embed_code'] ) : null ),
						'videoID' 		 =>  ( isset( $videoVars['v'] ) ? $videoVars['v'] : null ),
						'familyFriendly' =>  ( isset( $temp['family_friendly'] ) ? $temp['family_friendly'] : false ),
						'durationRaw' 	 =>  ( isset( $temp['duration'] ) ? $temp['duration'] : null ),
						'duration' 		 =>  ( isset( $temp['duration'] ) ? FormatDuration( $temp['duration'] ) : null ),
						'videoHeight'	 =>  ( isset( $temp['video_height'] ) ? $temp['video_height'] : null ),
						'videoWidth' 	 =>  ( isset( $temp['video_width'] ) ? $temp['video_width'] : null ),
						'playlistUrl' 	 =>  ( isset( $temp['playlist_url'] ) ? $temp['playlist_url'] : null ),
				);
			}
		}
		
		//value2 is the SEO data
		if ( !empty( $tmp['value2'] ) )
		{
			$temp = Json( $tmp['value2'] );

			if ( !empty( $temp ) )
			{
				$data['seo']['seo'] = $temp['seo'];
				$data['seo']['graph'] = $temp['graph'];
			}
		}
		
		//value3 is the Gallery data
		if ( !empty( $tmp['value3'] ) )
		{	
			$temp = Json( $tmp['value3'] );
			
			if ( !empty( $temp ) )
			{
				$gallery = array();
				
				foreach( $temp as $idImg )
				{
					//Query: image
					$gImg = $this->db->from( null, 
					"SELECT filename, title, alt, descr, caption, added_time, width, height
					FROM `" . DB_PREFIX . "images`
					WHERE (id_image = " . $idImg . ")"
					)->single();
		
					if ( !empty( $gImg ) )
					{
						$gallery[$idImg] = array(
							'url' => FolderUrlByDate( $gImg['added_time'] ) . stripslashes( $gImg['filename'] ),
							'title' => $gImg['title'],
							'alt' => $gImg['alt'],
							'descr' => $gImg['descr'],
							'caption' => $gImg['caption'],
							'added_time' => $gImg['added_time'],
							'width' => $gImg['width'],
							'height' => $gImg['height'],
							'childs' => array()
						);

						//Query: images
						$gChildImg = $this->db->from( null, 
						"SELECT filename, width, height FROM `" . DB_PREFIX . "images`
						WHERE (id_parent = " . $idImg . ")"
						)->all();

						if ( !empty( $gChildImg ) )
						{
							foreach( $gChildImg as $gChild )
							{
								$gallery[$idImg]['childs'][] = array(
									'url' => FolderUrlByDate( $gImg['added_time'] ) . stripslashes( $gChild['filename'] ),
									'width' => $gChild['width'],
									'height' => $gChild['height'],
								);
							}
						}
					}
				}
				
				$data['gallery'] = $gallery;
			}
		}
		
		//value4 is the Post data
		if ( !empty( $tmp['value4'] ) )
		{	
			$data['postData'] = Json( $tmp['value4'] );
		}
		
		$data['addPriceNum'] = ( !empty( $tmp['add_price_num'] ) ? true : false );
		$data['allowVoting'] = ( !empty( $tmp['allow_voting'] ) ? true : false );
		$data['pricesTitle'] = StripContent( $tmp['prices_title'] );

		return $data;
	}
	
	#####################################################
	#
	# Get Post Comment function
	#
	#####################################################
	function GetPostComments()
	{
		$co = ( !empty( $this->tmp['comments_data'] ) ? Json( $this->tmp['comments_data'] ) : null );
		
		if ( !empty( $co ) && !empty( $co['sort_by'] ) )
		{
			if ( $co['sort_by'] == 'older-first' )
			{
				$sort_by = "co.added_time ASC";
			}
			
			elseif ( $co['sort_by'] == 'newer-first' )
			{
				$sort_by = "co.added_time DESC";
			}
			
			else
			{
				$sort_by = "co.id DESC";
			}
		}
		
		else
		{
			$sort_by = "co.added_time DESC";
		}
		
		//Query: comments
		$tmp = $this->db->from( null, "
		SELECT co.*, COALESCE(u.real_name, u.user_name) AS user_name, u.image_data
		FROM `" . DB_PREFIX . "comments` AS co
		LEFT JOIN `" . DB_PREFIX . USERS . "`   as u ON u.id_member = co.user_id
		WHERE (co.id_post = " . $this->postId . ") AND (co.status = 'approved')
		GROUP BY co.id
		ORDER BY " . $sort_by
		)->all();

		if ( !$tmp )
		{
			return null;
		}

		$data = $imageData = array();

		foreach ( $tmp as $c )
		{
			$dt = Json( $c['rating_data'] );
			
			$image = TOOLS_HTML . 'theme_files/assets/frontend/img/default-fallback-image.png';
			
			if ( !empty( $c['image_data'] ) )
			{
				$imageData = Json( $c['image_data'] );
				
				if ( !empty( $imageData ) && isset( $imageData['default'] ) )
				{
					$image = $imageData['default']['imageUrl'];
				}
			}
			
			$data[] = array(
				'id' 		=> $c['id'],
				'status' 	=> $c['status'],
				'parentId' 	=> $c['id_parent'],
				'userId' 	=> $c['user_id'],
				'ip' 		=> $c['ip'],
				'imageData' => $imageData,
				'imageUrl' 	=> $image,
				'url'		=> $c['url'],
				'email'		=> $c['email'],
				'time'		=> postDate( $c['added_time'], false ),
				'niceTime'	=> niceTime( $c['added_time'] ),
				'timeRaw'	=> $c['added_time'],
				'name'		=> ( !empty( $c['user_name'] ) ? $c['user_name'] : $c['name'] ),
				'rTime'		=> date( 'r', $c['added_time'] ),
				'timeC'		=> postDate( $c['added_time'], true ),
				'rating'	=> $c['rating'],
				'comment'	=> CreatePostContent( $c['comment'], $this->tmp['title'], false, $this->tmp, true ),
				'reviewPos'	=> ( !empty( $dt ) ? CreatePostContent( $dt['pos'], $this->tmp['title'], false, $this->tmp, true ) : null ),
				'reviewNeg' => ( !empty( $dt ) ? CreatePostContent( $dt['neg'], $this->tmp['title'], false, $this->tmp, true ) : null )
			);
		}

		return $data;
	}
	
	private function GetPostById()
	{
		$cacheFile = CacheFileName( 'single-post_' . $this->id, null, null, null, null, null, null, ( $this->siteId ? $this->siteId : SITE_ID ) );
		
		if ( $this->cache && ValidOtherCache( $cacheFile ) )
		{
			$this->data = ReadCache( $cacheFile );
			
			if ( $this->addPostViews )
			{
				UpdateFilePostViews ( $this->data['id'] );
			}
		}
		
		else
		{
			$query = PostDefaultQuery( ( $this->siteId ? "(p.id_site = " . $this->siteId . ") AND " : "" ) . "(p.id_post = :id) AND (p.post_type = 'post' OR p.post_type = 'page')" . ( !$this->anyStatus ? " AND (p.post_status = 'published')" : "" ) . " AND (b.disabled = 0 OR b.disabled IS NULL)" );
			
			//Query: post
			$tmp = $this->db->from( null,
			$query,
			array( $this->id => ':id' )
			)->single();
			
			if ( empty( $tmp ) )
			{
				$this->query = $query;
				throw new Exception( 'Content not found in database by id [' . $this->id . ']' );
				return null;
			}

			$s = GetSettingsData( $tmp['id_site'] );
		
			if ( empty( $s ) )
			{
				return null;
			}
			
			$this->tmp 		= array_merge( $tmp, $s );
			$this->siteData = $s;
			$this->postId 	= $this->tmp['id_post'];
			//$this->siteUrl	= ( !empty( $this->tmp['url'] ) ? $this->tmp['url'] : SITE_URL );
			
			unset( $tmp, $s );
			
			if ( $this->addPostViews )
			{
				UpdatePostViews( $this->postId );
			}
			
			$this->BuildFullPostVars();

			//Also build the header code here, so we can cache it
			$this->data['headerCode'] = $this->HeaderCode();
			
			if ( $this->cache )
			{
				WriteOtherCacheFile( $this->data, $cacheFile, true );
			}
		}
	}
	
	private function GetPostBySlug()
	{
		$cacheFile = PostCacheFile( $this->slug, null, $this->lang['lang']['code'], $this->amp, ( $this->amp ? null : Settings::Get()['theme'] ) );
		
		if ( $this->cache && ValidOtherCache( $cacheFile ) )
		{
			$this->data = ReadCache( $cacheFile );
			
			if ( $this->addPostViews )
			{
				UpdateFilePostViews ( $this->data['id'] );
			}
		}
		
		else
		{
			$query = PostDefaultQuery( ( !empty( $this->siteId ) ? "(p.id_site = " . $this->siteId . ") AND " : "" ) . "(p.sef = :sef) AND (p.post_type = 'post' OR p.post_type = 'page')" . ( $this->anyStatus ? "" : " AND (p.post_status = 'published')" ) . " AND (b.disabled = 0 OR b.disabled IS NULL)" );

			//Query: post
			$tmp = $this->db->from( null,
			$query,
			array( $this->slug => ':sef' )
			)->single();
			
			if ( empty( $tmp ) )
			{
				$this->query = $query;
				throw new Exception( 'Content not found in database by key [' . $this->slug . ']' );
				return null;
			}

			$s = GetSettingsData( $tmp['id_site'] );
		
			if ( empty( $s ) )
			{
				return null;
			}
			
			$this->tmp 		= array_merge( $tmp, $s );
			$this->siteData = $s;			
			$this->postId 	= $this->tmp['id_post'];
			//$this->siteUrl	= ( !empty( $this->tmp['url'] ) ? $this->tmp['url'] : SITE_URL );
			
			unset( $tmp, $s );
			
			if ( $this->addPostViews )
			{
				UpdatePostViews( $this->postId );
			}
			
			$this->BuildFullPostVars();

			//Also build the header code here, so we can cache it
			$this->data['headerCode'] = $this->HeaderCode();
			
			if ( $this->cache )
			{
				WriteOtherCacheFile( $this->data, $cacheFile, true );
			}
		}
	}
	
	public function GetPost()
	{
		if ( !empty( $this->id ) )
		{
			$this->GetPostById();
		}
		
		elseif ( !empty( $this->slug ) )
		{
			$this->GetPostBySlug();
		}
		
		if ( empty( $this->data ) )
		{
			return null;
		}

		return ( $this->build ? new Post( $this->data ) : $this->data );
	}
}