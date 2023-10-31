<?php defined('TOKICMS') or die('Hacking attempt...');

class AddFilter extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-filters' ) ) || ( !$Admin->IsEnabled( 'coupons-and-deals' ) && !$Admin->IsEnabled( 'compare-prices' ) && !$Admin->IsEnabled( 'multivendor-marketplace' ) && !$Admin->IsEnabled( 'store' ) ) )
		{
			Router::SetNotFound();
			return;
		}

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'add-filter' ) )
			return;
		
		$sef = CreateSlug( ( !empty( $_POST['sef'] ) ? $_POST['sef'] : $_POST['name'] ) );
			
		//Make sure that we don't have this name in the DB
		$query = array(
				'SELECT'	=>  "id",
				
				'FROM'		=> DB_PREFIX . "filter_group",
				
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
			$url = $Admin->GetUrl( 'edit-filter' . PS . 'id' . PS . $q['id'] );
			$Admin->SetAdminMessage( sprintf( __( 'key-same-name-found-in-the-db' ), $url ) );
			return;
		}

		$order = ( ( isset( $_POST['sort_order'] ) && is_numeric( $_POST['sort_order'] ) ) ? $_POST['sort_order'] : 0 );

		$query = array(
				'INSERT'	=> "id_site, id_lang, name, sef, group_order",
				'VALUES' => ":site, :lang, :name, :sef, :order",
				'INTO'		=> DB_PREFIX . "filter_group",
				'PARAMS' => array( 'NO_PREFIX' => true ),

				'BINDS' 	=> array(
							array( 'PARAM' => ':site', 'VAR' => $Admin->GetSite(), 'FLAG' => 'INT' ),
							array( 'PARAM' => ':lang', 'VAR' => $Admin->GetLang(), 'FLAG' => 'INT' ),
							array( 'PARAM' => ':name', 'VAR' => $_POST['name'], 'FLAG' => 'STR' ),
							array( 'PARAM' => ':sef', 'VAR' => $sef, 'FLAG' => 'STR' ),
							array( 'PARAM' => ':order', 'VAR' => $order, 'FLAG' => 'INT' )
				)
		);
		
		$data = Query( $query, false, false, false, false, true );

		if ( $data )
		{
			if ( isset( $_POST['filter'] ) && !empty( $_POST['filter'] ) )
			{
				foreach ( $_POST['filter'] as $filter )
				{
					$query = array(
							'INSERT'	=> "id_group, filter_order, filter_name, trans_data",
							'VALUES' => ":group, :order, :name, :data",
							'INTO'		=> DB_PREFIX . "filters_data",
							'PARAMS' => array( 'NO_PREFIX' => true ),

							'BINDS' 	=> array(
										array( 'PARAM' => ':group', 'VAR' => $data, 'FLAG' => 'INT' ),
										array( 'PARAM' => ':order', 'VAR' => (int) $filter['sort_order'], 'FLAG' => 'INT' ),
										array( 'PARAM' => ':name', 'VAR' => $filter['name'], 'FLAG' => 'STR' ),
										array( 'PARAM' => ':data', 'VAR' => '{}', 'FLAG' => 'STR' )
							)
					);
					
					Query( $query, false, false, true );
				}
			}
			
			$Admin->DeleteSettingsCacheSite( 'filters' );
			
			Redirect( $Admin->GetUrl( 'edit-filter' . PS . 'id' . PS . $data ) );
		}
		
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'filters' ) );
		}
		
		exit;
	}
}