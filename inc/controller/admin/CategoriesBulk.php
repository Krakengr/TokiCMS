<?php defined('TOKICMS') or die('Hacking attempt...');

class CategoriesBulk extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

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

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			Redirect( $Admin->GetUrl( 'categories' ) );

		//This is to avoid accidental category deletion
		if ( !isset( $_POST['categoryBulkAction'] ) || ( $_POST['categoryBulkAction'] == '0' ) )
			Redirect( $Admin->GetUrl( 'categories' ) );

		//GET the default category
		$defCat = $this->db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "categories`
		WHERE (id_site = " . $Admin->GetSite() . ") AND (id_lang = " . $Admin->GetLang() . ") AND (id_blog = " . $Admin->GetBlog() . ") AND (is_default = 1)"
		)->single();

		if ( $_POST['categoryBulkAction'] == 'update' )
		{
			$this->db->update( "categories" )->where( 'id_site', $Admin->GetSite() )->where( 'id_lang', $Admin->GetLang() )->where( 'id_blog', $Admin->GetBlog() )->set( "hide_front", 0 );
			
			$this->db->update( "categories" )->where( 'id_site', $Admin->GetSite() )->where( 'id_lang', $Admin->GetLang() )->where( 'id_blog', $Admin->GetBlog() )->set( "hide_blog", 0 );
				
			if ( isset( $_POST['hide_front'] ) && !empty( $_POST['hide_front'] ) )
			{
				foreach( $_POST['hide_front'] as $catId => $a )
				{
					$this->db->update( "categories" )->where( 'id', $catId )->set( "hide_front", 1 );
				}
			}
			
			if ( isset( $_POST['hide_blog'] ) && !empty( $_POST['hide_blog'] ) )
			{
				foreach( $_POST['hide_blog'] as $catId => $a )
				{
					$this->db->update( "categories" )->where( 'id', $catId )->set( "hide_blog", 1 );
				}
			}
			
			//Change the default category
			if ( $_POST['default_cat'] !== $defCat['id'] )
			{
				//Set the default category
				$q = $this->db->update( "categories" )->where( 'id_site', $Admin->GetSite() )->where( 'id_lang', $Admin->GetLang() )->where( 'id_blog', $Admin->GetBlog() )->set( "is_default", 0 );
						
				//Now set the default category
				if ( $q )
				{
					$this->db->update( "categories" )->where( 'id', (int) $_POST['default_cat'] )->set( "is_default", 1 );
				}
			}
		}
		
		elseif ( ( $_POST['categoryBulkAction'] == 'delete' ) && !empty( $_POST['categories'] ) )
		{
			foreach ( $_POST['categories'] as $catId )
			{
				$Cat = GetCat( $catId, $Admin->GetSite() );
				
				//You can't delete the default category, sorry
				if ( !$Cat || $Cat['is_default'] )
				{
					continue;
				}
				
				$isChild = false;
				
				if ( $Cat['id_parent'] > 0 )
				{
					$def = $Cat['id_parent'];
					$isChild = true;
				}
				
				else
				{
					//Get the default category
					$def = GetBlogDefaultCategory( $Cat['id_blog'], $Cat['id_lang'] );
					
					if ( !$def )
					{
						continue;
					}
				}

				//Delete this category
				$this->db->delete( 'categories' )->where( "id", $catId )->run();
				
				//Update the posts
				$this->db->update( POSTS )->where( ( $isChild ? "id_sub_category" : "id_category" ), $catId )->set( "id_category", $def );
			}
		}
		
		$Admin->EmptyCaches();

		Redirect( $Admin->GetUrl( 'categories' ) );
	}
}