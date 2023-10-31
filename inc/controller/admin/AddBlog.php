<?php defined('TOKICMS') or die('Hacking attempt...');

class AddBlog extends Controller {
	
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
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-blogs' ) ) || !$Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) )
		{
			Router::SetNotFound();
			return;
		}

		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'add-blog' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'add-blog' ) )
			return;
		
		if ( empty( $_POST['title'] ) )
		{
			$Admin->SetAdminMessage( __( 'enter-a-valid-title' ) );
			return;
		}
	
		$db = db();

		$sef = SetShortSef( 'blogs', 'id_blog', 'sef', CreateSlug( ( !empty( $_POST['slug'] ) ? $_POST['slug'] : $_POST['title'] ) ) );
	
		$langID = ( ( isset( $_POST['select-language'] ) && !empty( $_POST['select-language'] ) ) ? $_POST['select-language'] : 0 );
		
		$dbarr = array(
			"id_site" 		=> $Admin->GetSite(),
			"id_lang" 		=> $langID,
			"name" 			=> $_POST['title'],
			"sef" 			=> $sef,
			"description" 	=> $_POST['description'],
			"slogan" 		=> $_POST['slogan'],
			"frontpage" 	=> ( ( isset( $_POST['frontpage'] ) && !empty( $_POST['frontpage'] ) ) ? 1 : 0 ),
			"news_sitemap" 	=> ( ( isset( $_POST['sitemap'] ) && !empty( $_POST['sitemap'] ) ) ? 1 : 0 ),
			"enable_rss" 	=> ( ( isset( $_POST['enable_rss'] ) && !empty( $_POST['enable_rss'] ) ) ? 1 : 0 )
		);

		$id = $this->db->insert( "blogs" )->set( $dbarr, null, true );
		
		if ( !$id )
		{
			$Admin->SetAdminMessage( __( 'blog-add-error' ) );
			return;
		}
		
		$sef = SetShortSef( 'categories', 'id', 'sef', CreateSlug( 'Uncategorized' ) );
		
		//Create a default category for this blog
		$dbarr = array(
			"id_site" 		=> $Admin->GetSite(),
			"id_lang" 		=> ( ( $langID > 0 ) ? $langID : $Admin->GetLang() ),
			"id_blog" 		=> $id,
			"is_default" 	=> 1,
			"name" 			=> 'Uncategorized',
			"sef" 			=> $sef
		);

		$this->db->insert( "categories" )->set( $dbarr );
		
		//Don't forget categories for every other language
		if ( $langID > 0 )
		{
			$langs = $Admin->Settings()::AllLangsById();
			
			foreach( $langs as $lId => $lang )
			{
				//Skit the previous language, we already have a category
				if ( $lId == $langID )
				{
					continue;
				}
				
				// Get the cat
				$cat = $this->db->from( 
				null, 
				"SELECT id
				FROM `" . DB_PREFIX . "categories`
				WHERE (id_lang = " . $lId . ") AND (id_blog = " . $id . ") AND (is_default = 1)"
				)->single();
					
				//If we don't have a category for this language, let's create one
				if ( !$cat )
				{
					$sef = SetShortSef( 'categories', 'id', 'sef', CreateSlug( 'Uncategorized' ), $id, $Admin->GetSite(), true, $lId );
					
					$dbarr = array(
						"id_site" 		=> $Admin->GetSite(),
						"id_lang" 		=> $lId,
						"id_blog" 		=> $id,
						"is_default" 	=> 1,
						"name" 			=> 'Uncategorized',
						"sef" 			=> $sef
					);

					$this->db->insert( "categories" )->set( $dbarr );
				}
			}
		}
	
		$Admin->DeleteSettingsCacheSite( 'settings' );
	
		//Redirect to the blog's edit page
		@header('Location: ' . $Admin->GetUrl( 'edit-blog' . PS . 'id' . PS . $id ) );
		exit;
	}
}