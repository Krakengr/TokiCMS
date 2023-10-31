<?php defined('TOKICMS') or die('Hacking attempt...');

class EditStoresAttribute extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $Att;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-stores' ) ) || ( !$Admin->IsEnabled( 'coupons-and-deals' ) && !$Admin->IsEnabled( 'compare-prices' ) && !$Admin->IsEnabled( 'multivendor-marketplace' ) && !$Admin->IsEnabled( 'store' ) ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );

		$id = (int) Router::GetVariable( 'key' );
	
		if ( !$Att )
		{
			Redirect( $Admin->GetUrl( 'stores-attributes' ) );
		}

		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'edit-stores-attribute' ) )
			return;
		
		//Check if we no longer want this group
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete'] ) )
		{
			//Delete the attribute
			$query = array(
					'DELETE' => DB_PREFIX . "stores_attributes",
					'WHERE'	=>  "id = :id",
					'PARAMS' => array( 'NO_PREFIX' => true ),
					'BINDS' 	=> array(
									array( 'PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' )
					)
			);

			$q = Query( $query, false, false, true );
			
			if ( $q )
			{
				$query = array(
					'DELETE' => DB_PREFIX . "store_attribute_data",
					'WHERE'	=>  "id_attr = :id",
					'PARAMS' => array( 'NO_PREFIX' => true ),
					'BINDS' 	=> array(
								array( 'PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' )
					)
				);

				Query( $query, false, false, true );
			
				Redirect( $Admin->GetUrl( 'stores-attributes' ) );
			}
			
			else
			{
				$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
				Redirect( $Admin->GetUrl( 'edit-stores-attribute' . PS . 'id' . PS . $id ) );
			}
		}
	
		if ( !empty( $_POST['trans'] ) )
		{
			$trans = array();
			
			foreach( $_POST['trans'] as $lId => $val )
			{
				if ( empty( $val ) )
					continue;
				
				$trans['lang-' . $lId] = array( 'value' => $val );
			}
		}
		else
			$trans = array();
		
		$slug = SetShortSef( DB_PREFIX . 'stores_attributes', 'id', 'sef', CreateSlug( $_POST['name'], true ), $id, null, false );
	
		$query = array(
				'UPDATE' 	=> DB_PREFIX . "stores_attributes",
				'SET'		=> "name = :name, sef = :sef, attr_order = :order, trans_data = :trans",
				'WHERE'		=>  "id = :id",
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				'BINDS' => array(
								array( 'PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' ),
								array( 'PARAM' => ':name', 'VAR' => $_POST['name'], 'FLAG' => 'STR' ),
								array( 'PARAM' => ':sef', 'VAR' => $slug, 'FLAG' => 'STR' ),
								array( 'PARAM' => ':order', 'VAR' => $_POST['order'], 'FLAG' => 'INT' ),
								array( 'PARAM' => ':trans', 'VAR' => json_encode( $trans, JSON_UNESCAPED_UNICODE ), 'FLAG' => 'STR' )
				)
		);

		Query( $query, false, false, true );

		Redirect( $Admin->GetUrl( 'edit-stores-attribute' . PS . 'id' . PS . $id ) );
	}
}