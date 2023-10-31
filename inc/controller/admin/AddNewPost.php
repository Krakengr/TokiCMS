<?php defined('TOKICMS') or die('Hacking attempt...');

class AddNewPost extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-own-posts' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'create-new-posts' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( Router::GetVariable( 'slug' ) ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is a POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'add-content' ) )
			Redirect( $Admin->Url() );
		
		//Set the current user ID for the current site
		$userId 	= $Admin->UserID();
		$defLang 	= $Admin->DefaultLang()['id'];
		$lang 		= $_POST['post_lang_id'];
		$site 		= $_POST['post_site_id'];
		$blog 		= ( ( isset( $_POST['blogId'] ) && !empty( $_POST['blogId'] ) ) ? (int) $_POST['blogId'] : $_POST['post_blog_id'] );
		
		//Grab the data from the POST
		$postType = ( isset( $_POST['postType'] ) ? $_POST['postType'] : 'post' );
		$postFormat = ( isset( $_POST['postFormat'] ) ? $_POST['postFormat'] : 0 );
		$categoryId = 0;
		$subCategoryId = 0;
		
		if ( ( $postType != 'page' ) && isset ( $_POST['category'] ) )
		{
			$cat = _explode( $_POST['category'], '::' );
			
			if ( $cat['target'] == 'cat' )
			{
				$categoryId = ( isset( $cat['id'] ) ? (int) $cat['id'] : 0 );
				
				$nfo = $this->db->from( 
				null, 
				"SELECT id_site, id_lang, id_blog
				FROM `" . DB_PREFIX . "categories`
				WHERE (id = " . $categoryId . ")"
				)->single();
				
				if ( $nfo )
				{
					$lang = $nfo['id_lang'];
					$site = $nfo['id_site'];
					$blog = $nfo['id_blog'];
				}
			}
			
			else
			{
				$subCategoryId = ( isset( $cat['id'] ) ? (int) $cat['id'] : 0 );
				
				$nfo = $this->db->from( 
				null, 
				"SELECT id_parent, id_site, id_lang, id_blog
				FROM `" . DB_PREFIX . "categories`
				WHERE (id = " . $subCategoryId['id'] . ")"
				)->single();
				
				if ( $nfo )
				{
					$categoryId = $nfo['id_parent'];
					$lang = $nfo['id_lang'];
					$site = $nfo['id_site'];
					$blog = $nfo['id_blog'];
				}
			}
		}
		
		//Set the post's date
		$postDate = ( isset( $_POST['date'] ) && !empty( $_POST['date'] ) ? $_POST['date'] . ' ' . 
					( isset( $_POST['hoursPublished'] ) && !empty( $_POST['hoursPublished'] ) ? $_POST['hoursPublished'] : '00' ) . ':' .
					( isset( $_POST['minutesPublished'] ) && !empty( $_POST['minutesPublished'] ) ? $_POST['minutesPublished'] : '00' ) . ':00'
					: null 
		);
			
		$postDate = ( $postDate ? strtotime( $postDate ) : time() );
		
		$dbarr = array(
			"id_site" 			=> $site,
			"id_lang" 			=> $lang,
			"id_blog" 			=> $blog,
			"title" 			=> $_POST['title'],
			"post" 				=> $_POST['content'],
			"description" 		=> $_POST['description'],
			"post_status" 		=> 'draft',
			"added_time" 		=> $postDate,
			"sef" 				=> '',
			"id_category" 		=> $categoryId,
			"post_type" 		=> $postType,
			"poster_ip" 		=> GetRealIp(),
			"id_member" 		=> $userId,
			"id_sub_category" 	=> $subCategoryId
		);

		$id = $this->db->insert( POSTS )->set( $dbarr, null, true );

		if ( !$id )
		{
			$Admin->SetAdminMessage( __( 'post-add-error' ) );
			return;
		}
		
		$slug = SetShortSef( POSTS, 'id_post', 'sef', CreateSlug( $_POST['title'], true ), $id );
		
		$this->db->update( POSTS )->where( 'id_post', $id )->set( "sef", $slug );
		
		if ( $blog > 0 )
		{
			$this->db->update( "blogs" )->where( "id_blog", $blog )->increase( "num_posts" );
		}
		
		if ( $postType == 'post' )
		{
			if ( $categoryId > 0 )
			{
				$this->db->update( "categories" )->where( "id", $categoryId )->increase( "num_items" );
			}
			
			if ( $subCategoryId > 0 )
			{
				$this->db->update( "categories" )->where( "id", $subCategoryId )->increase( "num_items" );
			}
			
			if ( !empty( $_POST['tags'] ) )
			{
				$tags = json_decode( $_POST['tags'], true );
			
				AddTags( $tags, $id, $lang, $site, 0 );
			}
		}
		
		$dbarr = array(
			"id_post" 	=> $id,
			"value1" 	=> '',
			"value2" 	=> '',
			"value3" 	=> '',
			"value4" 	=> ''
		);

		$this->db->insert( "posts_data" )->set( $dbarr );
		
		//Add the post product data
		$this->db->insert( "posts_product_data" )->set( array( "id_post" => $id ) );
		
		//Create the URI for redirection
		$url = AdminPostEditUri( $id, $blog, $site, $lang, $defLang );

		Redirect( $url );
	}
}
