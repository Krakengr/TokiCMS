<?php defined('TOKICMS') or die('Hacking attempt...');

class EditStore extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $Store;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-stores' ) ) || ( !$Admin->IsEnabled( 'coupons-and-deals' ) && !$Admin->IsEnabled( 'compare-prices' ) && !$Admin->IsEnabled( 'multivendor-marketplace' ) && !$Admin->IsEnabled( 'store' ) ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-store' ) . ': "' . $Store['data']['name'] . '" | ' . $Admin->SiteName() );
		
		$id = (int) Router::GetVariable( 'key' );

		if ( !$Store )
			Redirect( $Admin->GetUrl( 'stores' ) );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'edit-store' ) )
			return;
		
		if ( isset( $_POST['delete'] ) )
		{
			//Delete the store
			$q = $this->db->delete( 'stores' )->where( "id_store", $id )->run();
			
			if ( $q )
			{
				//Delete its data
				$this->db->delete( 'stores_data' )->where( "id_store", $id )->run();

				//Delete any prices from this vendor
				$prices = $this->db->from( 
				null, 
				"SELECT id_price
				FROM `" . DB_PREFIX . "prices`
				WHERE (id_store = " . $id . ")"
				)->all();

				if ( $prices )
				{
					foreach ( $prices as $price )
					{
						DeletePrice( $price['id_price'] );
					}
				}
			}
			
			Redirect( $Admin->GetUrl( 'stores' ) );
		}
		
		$sef 		= SetShortSef( 'stores', 'id_store', 'sef', CreateSlug( ( !empty( $_POST['sef'] ) ? $_POST['sef'] : $_POST['name'] ), true ), $id );
		$imageId 	= ( !empty( $_POST['logoFile'] ) ? (int) $_POST['logoFile'] : 0 );
		$jsonArr	= array(
			'url' 		=> null,
			'values'	=> array()
		);
		
		if ( isset( $_POST['retrieve_json_data'] ) )
		{
			$jsonArr['url'] = $_POST['json_url'];
			
			if ( !empty( $_POST['json_fields'] ) )
			{
				foreach( $_POST['json_fields'] as $l => $v )
				{
					$field 	= $v['field'];
					$value	= ( isset( $v['value'] ) ? $v['value'] : '' );

					if ( is_numeric( $field ) )
					{
						$field = (int) $v['field'];
						
						$attDb = $this->db->from( 
						null, 
						"SELECT name
						FROM `" . DB_PREFIX . "post_attributes`
						WHERE (id = " . $field . ")"
						)->single();
						
						$value 	= ( $attDb ? $attDb['name'] : $value );
					}

					$jsonArr['values'][$l] = array(
						'name' 	=> $v['name'],
						'key' 	=> $v['key'],
						'value' => $value,
						'field' => $field
					);
				}
			}
		}

		$dbarr = array(
            "name" 					=> $_POST['name'],
            "sef" 					=> $sef,
            "id_image" 				=> $imageId,
            "url" 					=> ( !Validate( $_POST['url'], 'url' ) ? '' : Sanitize( $_POST['url'], false ) ),
            "description" 			=> $_POST['description'],
            "post" 					=> $_POST['post'],
            "id_parent" 			=> ( !empty( $_POST['parentStore'] ) ? (int) $_POST['parentStore'] : 0 ),
            "scrape_as" 			=> $_POST['crawl_as'],
			"rotate_ip" 			=> ( isset( $_POST['rotate_ip_address'] ) ? 1 : 0 ),
			"retrieve_json_data" 	=> ( isset( $_POST['retrieve_json_data'] ) ? 1 : 0 ),
			"json_data" 			=> json_encode( $jsonArr, JSON_UNESCAPED_UNICODE )
        );

		$q = $this->db->update( "stores" )->where( "id_store", $id )->set( $dbarr );

		if ( !$q )
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'edit-store' . PS . 'id' . PS . $id ) );
		}
		
		$foundCustom = $foundAtt = array();

		//Update the custom data
		if ( !empty( $_POST['custom_fields'] ) && is_array( $_POST['custom_fields'] ) )
		{
			foreach( $_POST['custom_fields'] as $custId => $custVal )
			{
				$regex = EscapeRegex( $custVal['value'] );
				
				$cus = $this->db->from( 
				null, 
				"SELECT id
				FROM `" . DB_PREFIX . "stores_data`
				WHERE (id_store = " . $id . ") AND (id = " . $custId . ")"
				)->single();

				if ( $cus )
				{
					$dbarr = array(
						"name" 		=> $custVal['name'],
						"key_value" => $custVal['field'],
						"reg_data" 	=> $regex
					);

					$this->db->update( "stores_data" )->where( "id", $cus['id'] )->set( $dbarr );
					
					//$foundCustom[] = $cus['id'];
					array_push( $foundCustom, $cus['id'] );
				}
				
				else
				{
					$dbarr = array(
						"id_store" 		=> $id,
						"name" 			=> $custVal['name'],
						"key_value" 	=> $custVal['field'],
						"reg_data" 		=> $regex
					);
            
					$cusId = $this->db->insert( 'stores_data' )->set( $dbarr, null, true );

					if ( $cusId )
					{
						array_push( $foundCustom, $cusId );
					}
				}
			}
		}

		//Delete any custom data we don't have in 'foundCustom' array
		if ( !empty( $foundCustom ) )
		{
			$query = array(
				'DELETE' => DB_PREFIX . "stores_data",
				'WHERE'	=>  "(id_store = " . $id . ") AND id NOT IN (" . implode( ',', $foundCustom ) . ")",
				'PARAMS' => array( 'NO_PREFIX' => true )
			);

			Query( $query, false, false, true );
		}
		
		//Update the attributes
		if ( !empty( $_POST['att'] ) && is_array( $_POST['att'] ) )
		{
			foreach( $_POST['att'] as $attId => $attVal )
			{
				$att = $this->db->from( 
				null, 
				"SELECT id
				FROM `" . DB_PREFIX . "store_attribute_data`
				WHERE (id_store = " . $id . ") AND (id_attr = " . $attId . ")"
				)->single();

				if ( $att )
				{
					$this->db->update( "store_attribute_data" )->where( "id", $att['id'] )->set( "value", $attVal );

					array_push( $foundAtt, $att['id'] );
				}
				
				else
				{
					$dbarr = array(
						"id_attr" 	=> $attId,
						"id_store" 	=> $id,
						"value" 	=> $attVal
					);
            
					$atId = $this->db->insert( 'store_attribute_data' )->set( $dbarr, null, true );

					if ( $atId )
					{
						array_push( $foundAtt, $atId );
					}
				}
			}
		}
		
		/*
		//Delete any attribute data we don't have in 'foundAtt' array
		if ( !empty( $foundAtt ) )
		{
			$query = array(
				'DELETE' => DB_PREFIX . "store_attribute_data",
				'WHERE'	=>  "id_store = :id AND id_attr NOT IN ('" . implode( ',', $foundAtt ) . "')",
				'PARAMS' => array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
							array( 'PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' )
				)
			);

			Query( $query, false, false, true );
		}
		*/
		
		Redirect( $Admin->GetUrl( 'edit-store' . PS . 'id' . PS . $id ) );
		exit;
	}
}