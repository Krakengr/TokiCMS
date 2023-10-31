<?php defined('TOKICMS') or die('Hacking attempt...');

class DeleteCategory extends Controller {

	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
	
		$this->Run();

		$this->view();
	}
	
	#####################################################
	#
	# Run function
	#
	#####################################################
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

		//Don't delete the default category
		if ( !$Cat || $Cat['is_default'] )
			Redirect( $Admin->GetUrl( 'categories' ) );
		
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
				Redirect( $Admin->GetUrl( 'categories' ) );
		}
		
		//Delete this category
		$query = array(
			'DELETE' => DB_PREFIX . "categories",
			'WHERE'	=>  "id = :id",
			'PARAMS' => array( 'NO_PREFIX' => true ),
			'BINDS' 	=> array(
				array( 'PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' )
			)
		);

		Query( $query, false, false, true );
		
		//Update the posts
		$query = array(
			'UPDATE' 	=> DB_PREFIX . POSTS,
			'SET'		=> "id_category = :cat",
			'WHERE'		=> ( $isChild ? "id_sub_category" : "id_category" ) . " = :id",
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
			'BINDS' 	=> array(
				array( 'PARAM' => ':id',  'VAR' => $id, 'FLAG' => 'INT' ),
				array( 'PARAM' => ':cat', 'VAR' => $def, 'FLAG' => 'INT' )
			)
		);

		Query( $query, false, false, true );
		
		Redirect( $Admin->GetUrl( 'categories' ) );
	}
}