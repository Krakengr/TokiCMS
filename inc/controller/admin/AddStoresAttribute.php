<?php defined('TOKICMS') or die('Hacking attempt...');

class AddStoresAttribute extends Controller {
	
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

		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			Redirect( $Admin->GetUrl( 'stores-attributes' ) );
	
		if ( !verify_token( 'add_stores_attribute' ) )
			Redirect( $Admin->GetUrl( 'stores-attributes' ) );
		
		$slug = SetShortSef( DB_PREFIX . 'stores_attributes', 'id', 'sef', CreateSlug( $_POST['name'], true ), null, null, false );
		
		//This is simple, because we don't have to check if we already have the same data in the db
		//You can add as much as you want
		$query = array(
					'INSERT'	=> "name, sef, attr_order, trans_data, id_site, id_lang",
					
					'VALUES' => ":name, :sef, :order, :trans, :site, :lang",
					
					'INTO'		=> DB_PREFIX . "stores_attributes",
					
					'PARAMS' => array( 'NO_PREFIX' => true ),

					'BINDS' 	=> array(
								array( 'PARAM' => ':name', 'VAR' => $_POST['name'], 'FLAG' => 'STR' ),
								array( 'PARAM' => ':sef', 'VAR' => $slug, 'FLAG' => 'STR' ),
								array( 'PARAM' => ':order', 'VAR' => $_POST['order'], 'FLAG' => 'INT' ),
								array( 'PARAM' => ':trans', 'VAR' => json_encode( array() ), 'FLAG' => 'STR' ),
								array( 'PARAM' => ':site', 'VAR' => $Admin->GetSite(), 'FLAG' => 'INT' ),
								array( 'PARAM' => ':lang', 'VAR' => $Admin->GetLang(), 'FLAG' => 'INT' )
					)
		);
		
		$data = Query( $query, false, false, false, false, true );

		if ( $data )
		{
			Redirect( $Admin->GetUrl( 'edit-stores-attribute' . PS . 'id' . PS . $data ) );
		}
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'stores-attributes' ) );
		}
	}
}