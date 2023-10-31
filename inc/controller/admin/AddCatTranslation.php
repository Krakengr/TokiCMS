<?php defined('TOKICMS') or die('Hacking attempt...');

class AddCatTranslation extends Controller {
	
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
		
		$Cat = GetCat( $id, $Admin->GetSite() );

		if ( !$Cat )
			Redirect( $Admin->GetUrl( 'categories' ) );
		
		//Getvthe lang we want a new translation
		$lang = $Admin->GetLang();
		
		$langs = $Admin->Settings()::AllLangsById();

		//No lang? No fun...
		if ( empty( $langs ) || !isset( $langs[$lang] ) )
			Redirect( $Admin->GetUrl( 'categories' ) );

		//A few needed variables
		$defLang = $Admin->DefaultLang()['id'];
		$parent = $Cat['id_trans_parent'];
		$catLang = $Cat['id_lang'];
		$catSite = $Cat['id_site'];
		$catBlog = $Cat['id_blog'];
		$catTitle = $Cat['name'];
		$catDescr = $Cat['descr'];
		$catSlug = $Cat['sef'];
		$postType = $Cat['id_custom_type'];

		//If this is a translation, we should change the parent-child connection
		//We should have only one parent and this is the category with the default language
		if ( !empty( $parent ) && ( $parent > 0 ) )
		{
			$query = array(
				'UPDATE' 	=> DB_PREFIX . "categories",
				'SET'		=> "id_trans_parent = '0'",
				'WHERE'		=> "id = :id AND id_lang = :lang",
					
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
			'SELECT'	=> 'id, id_blog, id_site',

			'FROM'		=>	DB_PREFIX . "categories",
				
			'WHERE'		=> 'id_trans_parent = :parent AND id_lang = :lang',
				
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				
			'BINDS' 	=> array(
							array( 'PARAM' => ':parent', 'VAR' => $id, 'FLAG' => 'INT' ),
							array( 'PARAM' => ':lang', 'VAR' => $lang, 'FLAG' => 'INT' )
			)
		);

		$transp = Query( $query );
	
		//Redirect to this category
		if ( $transp )
		{
			$uri = CatEditUri( $id, $transp['id_blog'], $transp['id_site'], $lang, $defLang );
			
			Redirect( $uri );
		}
		
		//Create a categry and redirect it there
		$query = array(
			'INSERT'	=> "id_trans_parent, id_site, id_lang, id_blog, name, descr, sef",

			'VALUES' => ":parent, :site, :lang, :blog, :name, :descr, :sef",
				
			'INTO'		=> DB_PREFIX . "categories",
				
			'PARAMS' => array( 'NO_PREFIX' => true ),

			'BINDS' 	=> array(
						array( 'PARAM' => ':parent', 'VAR' => $id, 'FLAG' => 'INT' ),
						array( 'PARAM' => ':site', 'VAR' => $catSite, 'FLAG' => 'INT' ),
						array( 'PARAM' => ':lang', 'VAR' => $lang, 'FLAG' => 'INT' ),
						array( 'PARAM' => ':blog', 'VAR' => $catBlog, 'FLAG' => 'INT' ),
						array( 'PARAM' => ':name', 'VAR' => $catTitle, 'FLAG' => 'STR' ),
						array( 'PARAM' => ':descr', 'VAR' => $catDescr, 'FLAG' => 'STR' ),
						array( 'PARAM' => ':sef', 'VAR' => $catSlug . '-' . $lang, 'FLAG' => 'STR' )
			)
		);
	
		$newCatId = Query( $query, false, false, false, false, true );
	
		if ( $newCatId )
		{
			$uri = CatEditUri( $newCatId, $catBlog, $catSite , $lang, $defLang );
			Redirect( $uri );
		}
		
		else
			Redirect( $Admin->GetUrl( 'categories' ) );
	}
}