<?php defined('TOKICMS') or die('Hacking attempt...');

class PostsEditBulk extends Controller {
	
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
			Redirect( $Admin->GetUrl( 'posts' ) );
		
		print_r($_POST);exit;
		
		//This is to avoid accidental category deletion
		if ( !isset( $_POST['postsBulkAction'] ) || ( $_POST['postsBulkAction'] == '0' ) )
			Redirect( $Admin->GetUrl( 'posts' ) );

		
		
		//GET the default category
		$query = array(
			'SELECT'	=>  'id',

			'FROM'		=> DB_PREFIX . "categories",

			'WHERE'		=> "id_site = :id_site AND id_lang = :id_lang AND id_blog = :id_blog AND is_default = '1'",

			'PARAMS' 	=> array( 'NO_PREFIX' => true ),

			'BINDS' 	=> array(
							array( 'PARAM' => ':id_site', 'VAR' => $Admin->GetSite(), 'FLAG' => 'INT' ),
							array( 'PARAM' => ':id_lang', 'VAR' => $Admin->GetLang(), 'FLAG' => 'INT' ),
							array( 'PARAM' => ':id_blog', 'VAR' => $Admin->GetBlog(), 'FLAG' => 'INT' )
			)
		);

		$defCat = Query( $query );

		if ( $_POST['categoryBulkAction'] == 'update' )
		{
			$hideArray = array();
			
			$catsArray = ( ( isset( $_POST['cats_array'] ) && !empty( $_POST['cats_array'] ) ) ? $_POST['cats_array'] : null );
			
			if ( isset( $_POST['hide_front'] ) && !empty( $_POST['hide_front'] ) )
			{
				foreach( $_POST['hide_front'] as $catId => $a )
				{
					$hideArray[] = $catId;
					
					$query = array(
						'UPDATE' 	=> DB_PREFIX . "categories",
						'SET'		=> "hide_front = '1'",
						'WHERE'	 =>  "id = :id",
						'PARAMS' => array( 'NO_PREFIX' => true ),
						'BINDS'  => array(
								array( 'PARAM' => ':id', 'VAR' => $catId, 'FLAG' => 'INT' )
						)
					);

					Query( $query, false, false, true );
				}
			}
				
			if ( !empty( $catsArray ) )
			{
				foreach( $catsArray as $catId => $a )
				{
					if ( in_array( $catId, $hideArray ) )
						continue;
						
					$query = array(
							'UPDATE' 	=> DB_PREFIX . "categories",
							'SET'		=> "hide_front = '0'",
							'WHERE'	 =>  "id = :id",
							'PARAMS' => array( 'NO_PREFIX' => true ),
							'BINDS'  => array(
									array( 'PARAM' => ':id', 'VAR' => $catId, 'FLAG' => 'INT' )
							)
					);

					Query( $query, false, false, true );
				}
			}

			//There is nothing else to do
			if ( $_POST['default_cat'] == $defCat['id'] )
			{
				$Admin->EmptyCaches();
				
				Redirect( $Admin->GetUrl( 'categories' ) );
			}
			
			//Set the default category
			$query = array(
				'UPDATE' 	=> DB_PREFIX . "categories",
				'SET'		=> "is_default = '1'",
				'WHERE'	 =>  "id = :id",
				'PARAMS' => array( 'NO_PREFIX' => true ),
				'BINDS'  => array(
						array( 'PARAM' => ':id', 'VAR' => $_POST['default_cat'], 'FLAG' => 'INT' )
				)
			);

			$q = Query( $query, false, false, true );
			
			//Now change the previous category
			if ( $q )
			{
				$query = array(
					'UPDATE' 	=> DB_PREFIX . "categories",
					'SET'		=> "is_default = '0'",
					'WHERE'	 =>  "id = :id",
					'PARAMS' => array( 'NO_PREFIX' => true ),
					'BINDS'  => array(
							array( 'PARAM' => ':id', 'VAR' => $defCat['id'], 'FLAG' => 'INT' )
					)
				);

				Query( $query, false, false, true );
			}
		}
		
		if ( $_POST['categoryBulkAction'] == 'delete' )
		{
			if ( !isset( $_POST['del'] ) || empty( $_POST['del'] ) || !is_array( $_POST['del'] ) )
				return;
			
			foreach ( $_POST['del'] as $catId )
			{
				//You can't delete the default category, sorry
				if ( $catId == $defCat['id'] )
					continue;
				
				//Delete the category
				$query = array(
					'DELETE' => DB_PREFIX . "categories",
					'WHERE'	 =>  "id = :id",
					'PARAMS' => array( 'NO_PREFIX' => true ),
					'BINDS'  => array(
							array( 'PARAM' => ':id', 'VAR' => $catId, 'FLAG' => 'INT' )
					)
				);

				$q = Query( $query, false, false, true );
				
				if ( $q && $defCat )
				{
					UpdatePostsCatSubCat( $catId, $defCat['id'] );
				}
			}
		}
		
		$Admin->EmptyCaches();

		Redirect( $Admin->GetUrl( 'categories' ) );
	}
}