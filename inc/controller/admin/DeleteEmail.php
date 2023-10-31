<?php defined('TOKICMS') or die('Hacking attempt...');

class DeleteEmail extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $Query;
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'view-emails' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$id = (int) Router::GetVariable( 'key' );
		
		$Email = GetSingleEmail( $id );

		if ( !$Email )
			Redirect( $Admin->GetUrl( 'emails' ) );
		
		//This will move this mail into deleted section
		if ( $Email['status'] != 'deleted' )
		{
			$this->db->update( "mails" )->where( 'id', $id )->set( "status", "deleted" );
		}
		
		//Remove this mail from the DB
		else
		{
			$this->db->delete( 'mails' )->where( "id", $id )->run();
		}
		
		Redirect( $Admin->GetUrl( 'emails' ) );
	}
}