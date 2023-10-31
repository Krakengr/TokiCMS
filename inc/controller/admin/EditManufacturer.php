<?php defined('TOKICMS') or die('Hacking attempt...');

class EditManufacturer extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $Man;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-manufacturers' ) ) || ( !$Admin->IsEnabled( 'coupons-and-deals' ) && !$Admin->IsEnabled( 'compare-prices' ) && !$Admin->IsEnabled( 'multivendor-marketplace' ) && !$Admin->IsEnabled( 'store' ) ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		$id = (int) Router::GetVariable( 'key' );
		
		if ( !$Man )
		{
			$Admin->SetErrorMessage( __( 'nothing-found' ), 'warning' );
			Redirect( $Admin->GetUrl( 'manufacturers' ) );
		}

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'edit-manufacturer' ) )
			return;
		
		if ( isset( $_POST['delete'] ) )
		{
			//Delete the manufacturer
			$query = array(
				'DELETE' => DB_PREFIX . "manufacturers",
				'WHERE'	=>  "id = :id",
				'PARAMS' => array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
						array( 'PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' )
				)
			);

			Query( $query, false, false, true );
				
			Redirect( $Admin->GetUrl( 'manufacturers' ) );
		}

		$sef = CreateSlug( ( !empty( $_POST['sef'] ) ? $_POST['sef'] : $_POST['name'] ) );
		
		$imageId = ( !empty( $_POST['manufactLogoFile'] ) ? (int) $_POST['manufactLogoFile'] : 0 );
		
		$query = array(
				'UPDATE' 	=> DB_PREFIX . "manufacturers",
				'SET'		=> "title = :title, sef = :sef, id_image = :image",
				'WHERE'		=>  "id = :id",
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				'BINDS' => array(
								array('PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' ),
								array('PARAM' => ':title', 'VAR' => $_POST['name'], 'FLAG' => 'STR' ),
								array('PARAM' => ':sef', 'VAR' => $sef, 'FLAG' => 'STR' ),
								array( 'PARAM' => ':image', 'VAR' => $imageId, 'FLAG' => 'INT' )
				)
		);

		Query( $query, false, false, true );
		
		Redirect( $Admin->GetUrl( 'edit-manufacturer' . PS . 'id' . PS . $id ) );
		exit;
	}
}