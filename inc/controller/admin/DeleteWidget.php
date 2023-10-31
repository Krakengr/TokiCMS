<?php defined('TOKICMS') or die('Hacking attempt...');

class DeleteWidget extends Controller {
	
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
		
		//Don't do anything if there is a POST REQUEST
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
			return;
		
		$id = (int) Router::GetVariable( 'key' );

		$query = array(
				'DELETE' 	=> DB_PREFIX . "widgets",
				'WHERE'		=> "id = :id",
				'PARAMS'	=> array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
							array( 'PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' )
				)
		);

		Query( $query, false, false, true );

		//Redirect to the widgets
		@header('Location: ' . $Admin->GetUrl( 'widgets' ) );
		exit;
	}
}