<?php defined('TOKICMS') or die('Hacking attempt...');

class AddCategory extends Controller {
	
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
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'add-category' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'add-category' ) )
			Redirect();
		
		if ( empty( $_POST['categoryName'] ) )
		{
			$Admin->SetAdminMessage( __( 'enter-a-valid-title' ) );
			return;
		}
	
		//Get the default category (if we have a blog, lang etc.
		//If this is the first category for a blog, site, lang etc, we will set it as default
		$cat = $this->db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "categories`
		WHERE (id_site = " . $Admin->GetSite() . ") AND (id_lang = " . $Admin->GetLang() . ") AND (id_blog = " . $Admin->GetBlog() . ") AND (is_default = 1)"
		)->single();
		
		$slug = SetShortSef( 'categories', 'id', 'sef', CreateSlug( ( !empty( $_POST['categorySlug'] ) ? $_POST['categorySlug'] : $_POST['categoryName'] ) ) );
		
		$dbarr = array(
			"id_site" 			=> $Admin->GetSite(),
			"id_lang" 			=> $Admin->GetLang(),
			"id_blog" 			=> $Admin->GetBlog(),
			"name" 				=> $_POST['categoryName'],
			"sef"				=> $slug,
			"descr" 			=> $_POST['categoryDescription'],
			"id_parent" 		=> 0,
			"is_default" 		=> ( empty( $cat ) ? 1 : 0 )
		);

		$id = $this->db->insert( 'categories' )->set( $dbarr, null, true );

		if ( $id )
		{
			$Admin->EmptyCaches();
			
			Redirect( $Admin->GetUrl( 'edit-category' . PS . 'id' . PS . $id ) );
		}
		else
		{
			Redirect( $Admin->GetUrl( 'categories' ) );
		}
	}
}