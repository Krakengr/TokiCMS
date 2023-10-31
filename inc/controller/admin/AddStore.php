<?php defined('TOKICMS') or die('Hacking attempt...');

class AddStore extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-stores' ) ) || ( !$Admin->IsEnabled( 'coupons-and-deals' ) && !$Admin->IsEnabled( 'compare-prices' ) && !$Admin->IsEnabled( 'multivendor-marketplace' ) && !$Admin->IsEnabled( 'store' ) ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'add-store' ) )
			return;
		
		$sef = CreateSlug( ( !empty( $_POST['sef'] ) ? $_POST['sef'] : $_POST['name'] ) );

		$query = array(
					'INSERT'	=> "id_site, id_type, name, sef, url",
					
					'VALUES' => ":site, :type, :name, :sef, :url",
					
					'INTO'		=> DB_PREFIX . "stores",
					
					'PARAMS' => array( 'NO_PREFIX' => true ),

					'BINDS' 	=> array(
								array( 'PARAM' => ':site', 'VAR' => $Admin->GetSite(), 'FLAG' => 'INT' ),
								array( 'PARAM' => ':type', 'VAR' => 1, 'FLAG' => 'INT' ),
								array( 'PARAM' => ':name', 'VAR' => $_POST['name'], 'FLAG' => 'STR' ),
								array( 'PARAM' => ':sef', 'VAR' => $sef, 'FLAG' => 'STR' ),
								array( 'PARAM' => ':url', 'VAR' => $_POST['url'], 'FLAG' => 'STR' )
					)
		);
		
		$data = Query( $query, false, false, false, false, true );

		if ( $data )
		{
			Redirect( $Admin->GetUrl( 'edit-store' . PS . 'id' . PS . $data ) );
		}
		
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'stores' ) );
		}
		
		exit;
	}
}