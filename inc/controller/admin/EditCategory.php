<?php defined('TOKICMS') or die('Hacking attempt...');

class EditCategory extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) )
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$Cat = GetCategory( null, $id, $Admin->GetSite(), null, null, null, false );

		if ( !$Cat )
			Redirect( $Admin->GetUrl( 'categories' ) );
		
		$cats 	= GetAdminCategories();
		$Langs 	= ( $Admin->MultiLang() ? $Admin->OtherLangs() : null );
		$Blogs 	= ( $Admin->MultiBlog() ? $this->GetBlogs() : null );
		
		$this->setVariable( 'Cat', $Cat );
		$this->setVariable( 'dataLangs', $Langs );
		$this->setVariable( 'dataBlogs', $Blogs );
		$this->setVariable( 'cats', $cats );
		
		$defLang = $Admin->GetDefaultLanguage();

		//Create the URI for redirection, we may need it later
		$uri = CatEditUri( $id, $Cat['id_blog'], $Cat['id_site'], $Cat['id_lang'], $defLang );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-category' ) . ': "' . $Cat['name'] . '" | ' . $Admin->SiteName() );
	
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
		{
			if ( !verify_token( 'edit_category_' . $id ) )
				Redirect( $uri );
			
			$imageId = ( !empty( $_POST['catLogoFile'] ) ? (int) $_POST['catLogoFile'] : 0 );
			
			$lang = $Cat['id_lang'];
			$site = $Cat['id_site'];
			$blog = $Cat['id_blog'];
			
			$defCat = $this->db->from( 
			null, 
			"SELECT id
			FROM `" . DB_PREFIX . "categories`
			WHERE (id_site = " . $site . ") AND (id_lang = " . $lang . ") AND (id_blog = " . $blog . ") AND (is_default = 1)"
			)->single();
			
			//Delete this category
			if ( isset( $_POST['delete'] ) )
			{
				$q = $Query->DeleteCategory( $id );
				
				if ( $q && $defCat )
				{
					UpdatePostsCatSubCat( $id, $defCat['id'] );
					
					$Admin->SetErrorMessage( __( 'category-successfully-removed' ), 'info' );
					
					Redirect( $Admin->GetUrl( 'categories' ) );
				}
				
				//Nothing happened, return to the previous page
				$Admin->SetErrorMessage( __( 'category-delete-error' ) );
				Redirect( $uri );
			}

			//Move the category, but only if is not the default
			if ( !$Cat['is_default'] && !empty( $_POST['move-category'] ) )
			{
				//We no longer have this category, so update the posts
				if ( $defCat )
				{
					UpdatePostsCatSubCat( $id, $defCat['id'] );
				}

				$move = _explode( $_POST['move-category'], '::' );
				
				if ( ( $move['target'] == 'site' ) && !empty( $move['id'] ) )
				{
					$site = $move['id'];
					
					//We need the default lang for this site
					$siteDefLang = $this->db->from( 
					null, 
					"SELECT id
					FROM `" . DB_PREFIX . "languages`
					WHERE (id_site = " . $site . ") AND (is_default = 1)"
					)->single();
					
					$lang = ( $siteDefLang ? $siteDefLang['id'] : $lang );
				}
				
				elseif ( ( $move['target'] == 'blog' ) && !empty( $move['id'] ) )
				{
					$blog = $move['id'];
				}
				
				elseif ( ( $move['target'] == 'lang' ) && !empty( $move['id'] ) )
				{
					$lang = $move['id'];
				}
			}
			
			$slug = ( !empty( $_POST['slug'] ) ? $_POST['slug'] : $_POST['title'] );
			
			$slug = SetShortSef( 'categories', 'id', 'sef', CreateSlug( $slug, true ), $id );
			
			$groups = ( ( isset( $_POST['membergroups'] ) && !empty( $_POST['membergroups'] ) && is_array( $_POST['membergroups'] ) ) ? $_POST['membergroups'] : array() );
			
			$numOfPosts = (int) $_POST['article_limit'];
		
			$numOfPosts = ( ( empty( $numOfPosts ) || ( $numOfPosts > 100 ) ) ? $Admin->Settings()::Get()['article_limit'] : $numOfPosts );
			
			$dbarr = array(
				"id_lang" 			=> $lang,
				"id_blog" 			=> $blog,
				"id_site" 			=> $site,
				"name" 				=> $_POST['title'],
				"sef" 				=> $slug,
				"descr" 			=> $_POST['description'],
				"hide_front" 		=> ( isset( $_POST['hideFront'] ) ? 1 : 0 ),
				"hide_blog" 		=> ( isset( $_POST['hideBlogPage'] ) ? 1 : 0 ),
				"id_parent" 		=> $_POST['categoryParent'],
				"id_trans_parent" 	=> ( isset( $_POST['transParent'] ) ? $_POST['transParent'] : 0 ),
				"id_image" 			=> $imageId,
				"groups_data" 		=> json_encode( $groups, JSON_UNESCAPED_UNICODE ),
				"cat_color" 		=> $_POST['color'],
				"article_limit" 	=> $numOfPosts
			);
			
			$this->db->update( 'categories' )->where( 'id', $id )->set( $dbarr );
			
			$Admin->EmptyCaches();
			$Admin->DeleteSettingsCacheSite( 'category' );

			Redirect( $uri );
		}
		
		else
		{
			//Maybe we need a redirection?
			//Put this in here, to avoid any redirection when updating the category
			if ( ( $Cat['id_lang'] != $Admin->GetLang() ) || ( ( $Cat['id_blog'] > 0 ) && ( $Cat['id_blog'] != $Admin->GetBlog() ) ) )
			{
				Redirect( $uri );
			}

			$Cat['name'] = stripslashes( $Cat['name'] );
		}
	}
	
	private function GetBlogs()
	{
		global $Admin;
		
		$_blogs = $this->db->from( null, "
		SELECT id_blog, name
		FROM `" . DB_PREFIX . "blogs`
		WHERE (id_site = " . $Admin->GetSite() . ") AND (disabled = 0) AND (id_lang = " . $Admin->GetLang() . " OR id_lang = 0)
		ORDER BY name ASC"
		)->all();
		
		return $_blogs;
	}
}