<?php defined('TOKICMS') or die('Hacking attempt...');

class Email extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		$this->Run();

		$this->view();
	}
	
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
		
		$this->setVariable( 'Email', $Email );
		
		Theme::SetVariable( 'headerTitle', __( 'read-mail' ) . ': "' . htmlspecialchars( $Email['subject'] ) . '" | ' . $Admin->SiteName() );
		
		//Query: mail log
		$q = $this->db->from( null, "
		SELECT *
		FROM `" . DB_PREFIX . "log_emails`
		WHERE (id_mail = " . $id . ") AND (id_member = " . $Admin->UserID() . ") AND (id_site = " . $Admin->GetSite() . ")"
		)->single();

		if ( !$q )
		{
			//Set this item as read
			$dbarr = array(
				"added_time"	=> time(),
				"id_mail" 		=> $id,
				"id_site" 		=> $Admin->GetSite(),
				"id_member" 	=> $Admin->UserID()
			);
			
			$this->db->insert( 'log_emails' )->set( $dbarr );
		}
	}
}