<?php defined('TOKICMS') or die('Hacking attempt...');

class AddManufacturer extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-manufacturers' ) ) || ( !$Admin->IsEnabled( 'coupons-and-deals' ) && !$Admin->IsEnabled( 'compare-prices' ) && !$Admin->IsEnabled( 'multivendor-marketplace' ) && !$Admin->IsEnabled( 'store' ) ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'add-manufacturer' ) )
			return;
		
		$sef = CreateSlug( ( !empty( $_POST['sef'] ) ? $_POST['sef'] : $_POST['name'] ) );
			
		//Make sure that we don't have this name in the DB
		$query = array(
				'SELECT'	=>  "id",
				
				'FROM'		=> DB_PREFIX . "manufacturers",
				
				'PARAMS' => array( 'NO_PREFIX' => true ),
				
				'WHERE' => "id_site = :id AND sef = :sef",
				
				'BINDS' 	=> array(
								array( 'PARAM' => ':id', 'VAR' => $Admin->GetSite(), 'FLAG' => 'INT' ),
								array( 'PARAM' => ':sef', 'VAR' => $sef, 'FLAG' => 'STR' )
				)
		);
			
		// Get the data
		$q = Query( $query );
		
		if ( $q )
		{
			$url = $Admin->GetUrl( 'edit-manufacturer' . PS . 'id' . PS . $q['id'] );
			$Admin->SetAdminMessage( sprintf( __( 'key-same-name-found-in-the-db' ), $url ) );
			return;
		}

		$query = array(
					'INSERT'	=> "id_site, title, sef",
					
					'VALUES' => ":site, :title, :sef",
					
					'INTO'		=> DB_PREFIX . "manufacturers",
					
					'PARAMS' => array( 'NO_PREFIX' => true ),

					'BINDS' 	=> array(
								array( 'PARAM' => ':site', 'VAR' => $Admin->GetSite(), 'FLAG' => 'INT' ),
								array( 'PARAM' => ':title', 'VAR' => $_POST['name'], 'FLAG' => 'STR' ),
								array( 'PARAM' => ':sef', 'VAR' => $sef, 'FLAG' => 'STR' )
					)
		);
		
		$data = Query( $query, false, false, false, false, true );

		if ( $data )
		{
			Redirect( $Admin->GetUrl( 'edit-manufacturer' . PS . 'id' . PS . $data ) );
		}
		
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'manufacturers' ) );
		}
		
		exit;
	}
}