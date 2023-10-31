<?php defined('TOKICMS') or die('Hacking attempt...');

class AddPostTranslation extends Controller {
	
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

		$id = (int) Router::GetVariable( 'key' );
		
		$Post = AdminSinglePost( $id );

		if ( !$Post )
			Redirect( $Admin->GetUrl( 'posts' ) );
		
		//Getvthe lang we want a new translation
		$lang = $Admin->GetLang();
		
		$langs = $Admin->Settings()::AllLangsById();

		//No lang? No fun...
		if ( empty( $langs ) || !isset( $langs[$lang] ) )
			Redirect( $Admin->GetUrl( 'posts' ) );
		
		//A few needed variables
		$defLang = $Admin->DefaultLang()['id'];
		$parent = $Post->ParentId();
		$postLang = $Post->LanguageId();
		$postSite = $Post->SiteId();
		$postBlog = $Post->BlogId();
		$postTitle = $Post->Title();
		$postPost = $Post->PostRaw();
		$postDescr = $Post->Description();
		$postSlug = $Post->PostSef();
		$postType = $Post->PostType();

		//If this is a translation, we should change the parent-child connection
		//We should have only one parent and this is the post with the default language
		if ( !empty( $parent ) && ( $parent > 0 ) )
		{
			$query = array(
				'UPDATE' 	=> DB_PREFIX . POSTS,
				'SET'		=> "id_parent = '0'",
				'WHERE'		=> "id_post = :id AND id_lang = :lang",
					
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
					
				'BINDS' 	=> array(
							array( 'PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':lang', 'VAR' => $defLang, 'FLAG' => 'INT' )
				)
			);

			Query( $query, false, false, true );
		}
	
		//Do we have a translation already?
		$query = array(
			'SELECT'	=> 'id_post, id_blog, id_site',
				
			'FROM'		=>	DB_PREFIX . POSTS,
				
			'WHERE'		=> 'id_parent = :parent AND id_lang = :lang',
				
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				
			'BINDS' 	=> array(
							array( 'PARAM' => ':parent', 'VAR' => $id, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':lang', 'VAR' => $lang, 'FLAG' => 'INT' )
			)
		);

		$transp = Query( $query );
	
		//Redirect to this post
		if ( $transp )
		{
			$uri = AdminPostEditUri( $transp['id_post'], $transp['id_blog'], $transp['id_site'], $lang, $defLang );
			
			Redirect( $uri );
		}
		
		$auth = $this->getVariable( 'AuthUser' );
		
		//Create a post and redirect it there
		$query = array(
				'INSERT'	=> "id_parent, id_site, id_lang, id_blog, poster_ip, id_member, title, post, description, sef, post_status, post_type",
				
				'VALUES' 	=> ":parent, :id_site, :id_lang, :id_blog, :poster_ip, :id_member, :title, :post, :description, :sef, :status, :type",
				
				'INTO'		=> DB_PREFIX . POSTS,
				
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),

				'BINDS' 	=> array(
							array( 'PARAM' => ':parent', 'VAR' => $id, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':id_site', 'VAR' => $postSite, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':id_lang', 'VAR' => $lang, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':id_blog', 'VAR' => $postBlog, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':poster_ip', 'VAR' => GetRealIp(), 'FLAG' => 'STR' ),
							array( 'PARAM' => ':id_member', 'VAR' => $auth['id_member'], 'FLAG' => 'INT' ),
							array( 'PARAM' => ':title', 'VAR' => $postTitle, 'FLAG' => 'STR' ),
							array( 'PARAM' => ':post', 'VAR' => $postPost, 'FLAG' => 'STR' ),
							array( 'PARAM' => ':description', 'VAR' => $postDescr, 'FLAG' => 'STR' ),
							array( 'PARAM' => ':sef', 'VAR' => $postSlug . '-' . $langs[$lang]['lang']['code'], 'FLAG' => 'STR' ),
							array( 'PARAM' => ':status', 'VAR' => 'draft', 'FLAG' => 'STR' ),
							array( 'PARAM' => ':type', 'VAR' => $postType, 'FLAG' => 'STR' )
			)
		);
	
		$postId = Query( $query, false, false, false, false, true );
	
		if ( $postId )
		{
			$uri = AdminPostEditUri( $postId, $postBlog, $postSite, $lang, $defLang );
			Redirect( $uri );
		}
		
		else
			Redirect( $Admin->GetUrl( 'posts' ) );
	}
}