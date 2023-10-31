<?php defined('TOKICMS') or die('Hacking attempt...');

class EditBlog extends Controller {
	
	private $blogLang;
	private $blogId;
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	//Let's check the categories for this blog
	//This function will create any missing categories
	private function CheckCategories()
	{
		global $Admin;
		
		$langs = $Admin->Settings()::AllLangsById();

		//We have this blog enabled everywhere, so let's check each language
		if ( $this->blogLang == 0 )
		{
			foreach( $langs as $id => $lang )
			{
				$cat = $this->db->from( 
				null, 
				"SELECT id
				FROM `" . DB_PREFIX . "categories`
				WHERE (id_lang = " . $id . ") AND (id_blog = " . $this->blogId . ") AND (is_default = 1)"
				)->single();
					
				//If we don't have a category for this language, let's create one
				if ( !$cat )
				{
					$sef = SetShortSef( 'categories', 'id', 'sef', CreateSlug( 'Uncategorized' ), $this->blogId, $Admin->GetSite(), true, $id );
					
					$dbarr = array(
						"id_site" 		=> $Admin->GetSite(),
						"id_lang" 		=> $id,
						"id_blog" 		=> $this->blogId,
						"is_default" 	=> 1,
						"name" 			=> 'Uncategorized',
						"sef" 			=> $sef
					);

					$this->db->insert( "categories" )->set( $dbarr );
				}
			}
		}
		
		//If this blog is for 
		elseif ( $this->blogLang > 0 )
		{
			// Get the cat
			$cat = $this->db->from( 
			null, 
			"SELECT id
			FROM `" . DB_PREFIX . "categories`
			WHERE (id_lang = " . $this->blogLang . ") AND (id_blog = " . $this->blogId . ") AND (is_default = 1)"
			)->single();

			//If we don't have a category for this language, let's create one
			if ( !$cat )
			{
				$sef = SetShortSef( 'categories', 'id', 'sef', CreateSlug( 'Uncategorized' ), $this->blogId, $Admin->GetSite(), true, $this->blogLang );
				
				$dbarr = array(
					"id_site" 		=> $Admin->GetSite(),
					"id_lang" 		=> $this->blogLang,
					"id_blog" 		=> $this->blogId,
					"is_default" 	=> 1,
					"name" 			=> 'Uncategorized',
					"sef" 			=> $sef
				);

				$this->db->insert( "categories" )->set( $dbarr );
			}
		}
	}
	
	private function Run() 
	{
		global $Admin;

		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-blogs' ) ) || !$Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) )
		{
			Router::SetNotFound();
			return;
		}

		$this->blogId = $id = (int) Router::GetVariable( 'key' );
		
		$Blog = GetBlog( null, $id, $Admin->GetSite(), null, null, false );
	
		if ( !$Blog )
			Redirect( $Admin->GetUrl( 'blogs' ) );
		
		if ( ( $Blog['id_lang'] > 0 ) && ( $Admin->GetLang() != $Blog['id_lang'] ) )
		{
			Redirect( $Admin->GetUrl( 'blogs' ) );
		}
		
		$blogTitle = $Blog['name'];

		$Blog['translation'] = array();
		
		$langKey = $Admin->LangKey();
		
		//Decode the translation data
		if ( !$Admin->IsDefaultLang() && !empty( $Blog['trans_data'] ) )
		{
			$temp = Json( $Blog['trans_data'] );
			
			if ( !empty( $temp ) && isset( $temp[$langKey] ) )
			{
				$Blog['translation'] = $temp[$langKey];
			}
		}
		
		Theme::SetVariable( 'headerTitle', __( 'edit-blog' ) . ': "' . $blogTitle . '" | ' . $Admin->SiteName() );

		$this->setVariable( 'Blog', $Blog );

		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
		{
			$q = "(p.id_site = " . $Admin->GetSite() . ") AND (p.id_lang = " . $Admin->GetLang() . ") AND (p.id_blog = " . $id . ") AND (p.post_type = 'page') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";

			$query = PostsDefaultQuery( $q, null, "p.title ASC" );

			$pages = $this->db->from( null, $query )->all();
			
			$this->setVariable( 'BlogPages', $pages );
			
			return;
		}

		// Verify if the token is correct
		if ( !verify_token( 'editBlog' . $id ) )
			Redirect( $Admin->GetUrl( 'blogs' ) );
		
		$this->blogLang = ( ( isset( $_POST['select-lang'] ) && !empty( $_POST['select-lang'] ) ) ? (int) $_POST['select-lang'] : 0 );

		//If we want to delete a blog, do it here
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete']  ) )
		{
			if ( $Blog['type'] != 'normal' )
			{
				$_settings = Json( $Admin->Settings()::Get()['extra_blogs'] );
			
				$_settings = ( !empty( $_settings ) ? $_settings : array() );
				
				$types = array();
				
				$_settings['types'] = ( isset( $_settings['types'] ) ? $_settings['types'] : array() );

				unset( $_settings['ids'][$id] );
				
				if ( !empty( $_settings['types'] ) )
				{
					foreach ( $_settings['types'] as $_ => $__ )
					{
						if ( $__['blogId'] == $id )
							continue;
						
						$types[$_] = $__;
					}
					
					$_settings['types'] = null;
				
					$_settings['types'] = $types;
				}				

				$settingsArray['extra_blogs'] = json_encode( $_settings );
				
				$Admin->UpdateSettings( $settingsArray );
			}
			
			//Delete this blog
			$this->db->delete( "blogs" )->where( "id_blog", $id )->run();
			
			//We have to update the posts
			$this->db->update( POSTS )->where( "id_blog", $id )->set( "id_blog", 0 );
			
			//And the categories
			$this->db->update( "categories" )->where( "id_blog", $id )->set( "id_blog", 0 );

			EmptyCaches();

			//Redirect to the dashboard
			@header('Location: ' . $Admin->GetUrl( 'blogs' ) );
			exit;
		}
		
		//Encode the translation data
		if ( !$Admin->IsDefaultLang() )
		{
			$temp = Json( $Blog['trans_data'] );

			$temp[$langKey]['name'] = Sanitize( $_POST['title'], false );
			$temp[$langKey]['slogan'] = Sanitize( $_POST['slogan'], false );
			$temp[$langKey]['description'] = Sanitize( $_POST['description'], false );
			
			//Keep the default values
			$_name = $Blog['name'];
			$_slogan = $Blog['slogan'];
			$_descr = $Blog['description'];
		}
		else
		{
			$temp = array();
			
			$_name = $_POST['title'];
			$_slogan = $_POST['slogan'];
			$_descr = $_POST['description'];
		}
		
		if ( $_name == '' )
			$_name = 'No Name';

		$slug = SetShortSef( 'blogs', 'id_blog', 'sef', CreateSlug( ( !empty( $_POST['slug'] ) ? $_POST['slug'] : $_name ) ), $id );
		
		$groups = ( ( isset( $_POST['membergroups'] ) && !empty( $_POST['membergroups'] ) && is_array( $_POST['membergroups'] ) ) ? $_POST['membergroups'] : array() );

		$blogType = $type = $_POST['select-type'];
		
		$parentType = $Admin->Settings()::Get()['parent_type'];
		
		if ( ( $parentType != 'normal' ) && ( $blogType == $parentType ) )
		{
			$Admin->SetErrorMessage( sprintf( __( 'blog-type-already-exists-parent' ), $parentType ) );
			$type = $Blog['type'];
		}
		
		$numOfPosts = (int) $_POST['article_limit'];
		
		$numOfPosts = ( ( empty( $numOfPosts ) || ( $numOfPosts > 100 ) ) ? $Admin->Settings()::Get()['article_limit'] : $numOfPosts );
		
		//We can now update the DB
		$dbarr = array(
			"id_lang" 			=> $this->blogLang,
			"name" 				=> $_name,
			"sef" 				=> $slug,
			"slogan" 			=> $_slogan,
			"description" 		=> $_descr,
			"frontpage_shows"	=> $_POST['frontpage-shows'],
			"frontpage_page" 	=> ( ( isset( $_POST['frontpage-page'] ) && !empty( $_POST['frontpage-page'] ) ) ? (int) $_POST['frontpage-page'] : 0 ),
			"theme" 			=> ( ( isset( $_POST['select-theme'] )  && !empty( $_POST['select-theme'] ) ) ? $_POST['select-theme'] : '' ),
			"frontpage" 		=> ( ( isset( $_POST['frontpage'] ) 	&& !empty( $_POST['frontpage'] ) ) ? 1 : 0 ),
			"news_sitemap" 		=> ( ( isset( $_POST['sitemap'] ) 		&& !empty( $_POST['sitemap'] ) ) ? 1 : 0 ),
			"disabled" 			=> ( ( isset( $_POST['disable'] ) 		&& !empty( $_POST['disable'] ) ) ? 1 : 0 ),
			"type" 				=> $type,
			"redirect" 			=> $_POST['redirect'],
			"enable_rss" 		=> ( ( isset( $_POST['enable_rss'] ) && !empty( $_POST['enable_rss'] ) ) ? 1 : 0 ),
			"hide_sitemap" 		=> ( ( isset( $_POST['hide_sitemap'] ) && !empty( $_POST['hide_sitemap'] ) ) ? 1 : 0 ),
			"groups_data" 		=> json_encode( $groups, JSON_UNESCAPED_UNICODE ),
			"trans_data" 		=> json_encode( $temp, JSON_UNESCAPED_UNICODE ),
			"custom_home_tmp" 	=> $_POST['home-template'],
			"custom_list_tmp" 	=> $_POST['list-template'],
			"custom_post_tmp" 	=> $_POST['post-template'],
			"article_limit" 	=> $numOfPosts,
			"dont_load_posts" 	=> ( ( isset( $_POST['dont_load_posts'] ) && !empty( $_POST['dont_load_posts'] ) ) ? 1 : 0 ),
        );

		$this->db->update( "blogs" )->where( 'id_blog', $id )->set( $dbarr );

		//Check the categories
		$this->CheckCategories();
		
		//Sub-blogs can't have the same type with the parent
		if ( ( $parentType === 'normal' ) || ( ( $parentType !== 'normal' ) && ( $blogType !== $parentType ) ) )
		{
			//Continue with blog's data
			$_settings = Json( $Admin->Settings()::Get()['extra_blogs'] );
			$_settings = ( !empty( $_settings ) ? $_settings : array() );
			$_settings['types'] = ( isset( $_settings['types'] ) ? $_settings['types'] : array() );
			$_settings['ids'] = ( isset( $_settings['ids'] ) ? $_settings['ids'] : array() );

			//For other than normal blogs, we need a few changes
			if ( $blogType !== 'normal' )
			{
				if ( !isset( $_settings['types'][$blogType] ) )
				{
					$_settings['types'][$blogType] = array();
				}
				
				//First delete this blog for the array.
				//This is needed in order to delete this blog from any other type
				if ( !empty( $_settings['types'] ) )
				{
					foreach( $_settings['types'] as $_type => $_arr )
					{
						if ( $_type == $blogType )
							continue;
						
						if ( !empty( $_arr ) )
						{
							foreach( $_arr as $_k => $_v )
							{
								if ( $_v == $id )
								{
									unset( $_settings['types'][$_type][$_k] );
								}
							}
						}
					}
				}

				if ( !in_array( $id, $_settings['types'][$blogType] ) )
				{
					array_push( $_settings['types'][$blogType], $id );
				}

				//This is easier
				$_settings['ids'][$id] = $blogType;
			}
			
			//So, we decided to make this blog "normal", so we have to delete it from this array
			else
			{
				if ( !empty( $_settings['types'] ) )
				{
					foreach( $_settings['types'] as $_type => $_arr )
					{
						if ( !empty( $_arr ) )
						{
							foreach( $_arr as $_k => $_v )
							{
								if ( $_v == $id )
								{
									unset( $_settings['types'][$_type][$_k] );
								}
							}
						}
					}
				}
				
				if ( isset( $_settings['ids'][$id] ) )
				{
					unset( $_settings['ids'][$id] );
				}
			}
			
			$settingsArray['extra_blogs'] = json_encode( $_settings );

			$Admin->UpdateSettings( $settingsArray );
		}

		$Admin->EmptyCaches();
		
		$Admin->DeleteSettingsCacheSite( 'blog' );

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( 'edit-blog' . PS . 'id' . PS . $id ) );
		exit;
	}
}