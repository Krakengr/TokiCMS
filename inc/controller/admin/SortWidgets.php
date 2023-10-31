<?php defined('TOKICMS') or die('Hacking attempt...');

class SortWidgets extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
	
		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-widgets' ) )
		{
			Router::SetNotFound();
			return;
		}

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		print_r($_POST);exit;
	
		//Get the default category (if we have a blog, lang etc.
		//If this is the first category for a blog, site, lang etc, we will set it as default
		$query = array(
				'SELECT'	=>  'id',
				
				'FROM'		=> DB_PREFIX . "categories",
				
				'WHERE'		=> "id_site = :id_site AND id_lang = :id_lang AND id_blog = :id_blog AND is_default = '1'",
				
				'PARAMS' 	=> array(
								'NO_PREFIX' => true 
							),
				
				'BINDS' 	=> array(
								array(
									'PARAM' => ':id_site',
									'VAR' => $Admin->GetSite(),
									'FLAG' => 'INT'
								),
								
								array(
									'PARAM' => ':id_lang',
									'VAR' => $Admin->GetLang(),
									'FLAG' => 'INT'
								),
								
								array(
									'PARAM' => ':id_blog',
									'VAR' => $Admin->GetBlog(),
									'FLAG' => 'INT'
								)
							)
		);

		$cat = Query( $query );
		$catID = ( !empty( $cat ) ? $cat['id'] : 0 );

		$slug = SetShortSef( DB_PREFIX . 'categories', 'id', 'sef', CreateSlug( ( !empty( $_POST['categorySlug'] ) ? $_POST['categorySlug'] : $_POST['categoryName'] ) ) );

		$query = array(
				'INSERT'	=>  "id_site, id_lang, id_blog, name, sef, descr, id_parent, is_default",
				
				'VALUES' => ":id_site, :id_lang, :id_blog, :name, :sef, :descr, :id_parent, :is_default",
				
				'INTO'		=> DB_PREFIX . "categories",
				
				'PARAMS' => array(
					'NO_PREFIX' => true 
				),

				'BINDS' 	=> array(
							array(
								'PARAM' => ':id_site',
								'VAR' => $Admin->GetSite(),
								'FLAG' => 'INT'
							),
							array(
								'PARAM' => ':id_lang',
								'VAR' => $Admin->GetLang(),
								'FLAG' => 'INT'
							),
							array(
								'PARAM' => ':id_blog',
								'VAR' => $Admin->GetBlog(),
								'FLAG' => 'INT'
							),
							array(
								'PARAM' => ':name',
								'VAR' => $_POST['categoryName'],
								'FLAG' => 'STR'
							),
							array(
								'PARAM' => ':sef',
								'VAR' => $slug,
								'FLAG' => 'STR'
							),
							array(
								'PARAM' => ':descr',
								'VAR' => $_POST['categoryDescription'],
								'FLAG' => 'STR'
							),
							array(
								'PARAM' => ':id_parent',
								'VAR' => $_POST['categoryParent'],
								'FLAG' => 'INT'
							),
							array(
								'PARAM' => ':is_default',
								'VAR' => ( empty( $catID ) ? 1 : 0 ),
								'FLAG' => 'INT'
							)
						)
		);
	
		$data = Query( $query, false, false, false, false, true );

		if ( $data )
		{
			Redirect( $Admin->GetUrl( 'edit-category' . PS . 'id' . PS . $data ) );
		}
		else
		{
			Redirect( $Admin->GetUrl( 'categories' ) );
		}
	}
}