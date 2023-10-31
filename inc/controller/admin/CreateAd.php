<?php defined('TOKICMS') or die('Hacking attempt...');

class CreateAd extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-ads' ) )
		{
			Router::SetNotFound();
			return;
		}

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'add_ad' ) )
			Redirect();
		
		if ( empty( $_POST['adName'] ) )
		{
			$Admin->SetAdminMessage( __( 'enter-a-valid-title' ) );
			return;
		}
		
		$tmp = $this->db->from( null, "
		SELECT ad_order
		FROM `" . DB_PREFIX . "ads`
		WHERE (id_site = " . $Admin->GetSite() . ") AND (id_lang = " . $Admin->GetLang() . ")
		ORDER BY ad_order DESC
		LIMIT 1"
		)->single();

		$order = ( $tmp ? ( $tmp['ad_order'] + 1 ) : 1 );
		
		$dbarr = array(
			"id_site" 		=> $Admin->GetSite(),
			"id_lang" 		=> $Admin->GetLang(),
			"title" 		=> $_POST['adName'],
			"type" 			=> $_POST['adType'],
			"ad_pos" 		=> $_POST['adPosition'],
			"ad_code" 		=> htmlentities( $_POST['adCode'] ),
			"added_time" 	=> time(),
			"ad_order" 		=> $order
		);

		$id = $this->db->insert( 'ads' )->set( $dbarr, null, true );
		
		if ( $id )
		{
			Redirect( $Admin->GetUrl( 'edit-ad' . PS . 'id' . PS . $id ) );
		}
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'ads' ) );
		}
	}
}