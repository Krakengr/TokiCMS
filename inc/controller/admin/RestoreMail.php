<?php defined('TOKICMS') or die('Hacking attempt...');

class RestoreMail extends Controller {

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

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'view-emails' ) )
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$Email = GetSingleEmail( $id );

		if ( !$Email )
			Redirect( $Admin->GetUrl( 'emails' ) );
		
		if ( $Email['status'] == 'deleted' )
		{
			$this->db->update( "mails" )->where( 'id', $id )->set( "status", $Email['default_status'] );
		}
		
		Redirect( $Admin->GetUrl( 'emails' ) );
	}
}