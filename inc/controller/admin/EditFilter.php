<?php defined('TOKICMS') or die('Hacking attempt...');

class EditFilter extends Controller {
	
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
		
		$id = (int) Router::GetVariable( 'key' );
		
		$Filter = AdminFilter( $id );
		
		if ( !$Filter )
			Redirect( $Admin->GetUrl( 'filters' ) );
		
		$this->setVariable( 'Filter', $Filter );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'edit-filter_' . $id ) )
			return;
		
		$sef = CreateSlug( ( !empty( $_POST['sef'] ) ? $_POST['sef'] : $_POST['name'] ) );
		
		$order = ( ( isset( $_POST['sort_order'] ) && is_numeric( $_POST['sort_order'] ) ) ? $_POST['sort_order'] : 0 );
		
		$data = ( !empty( $Filter['groupData'] ) ? $Filter['groupData'] : array() );
		
		//Reset these values
		$data['targetCategoryDetails'] = $data['targetTagDetails'] = $data['targetCategoryHideDetails'] = $data['targetTagHideDetails'] = $data['sourceData'] = array();
		
		$data['source'] = ( isset( $_POST['source'] ) ? $_POST['source'] : null );
		$data['target'] = ( isset( $_POST['target'] ) ? $_POST['target'] : null );
		$data['target-att'] = ( isset( $_POST['attributeToLook'] ) ? $_POST['attributeToLook'] : null );
		$data['type'] = ( isset( $_POST['option-type'] ) ? $_POST['option-type'] : null );
		
		//Source options
		if ( isset( $_POST['source'] ) && !empty( $_POST['source'] ) )
		{
			if ( !empty( $_POST['attribute-group'] ) )
			{
				$data['sourceData'] = array(
					'id' => $_POST['attribute-group'],
					'source' => $_POST['source']
				);
			}
			
			elseif ( !empty( $_POST['attribute'] ) )
			{
				$data['sourceData'] = array(
					'id' => $_POST['attribute'],
					'source' => $_POST['source']
				);
			}
			
			elseif ( !empty( $_POST['customType'] ) )
			{
				$data['sourceData'] = array(
					'id' => $_POST['customType'],
					'source' => $_POST['source']
				);
			}
			
			elseif ( !empty( $_POST['manufacturerSource'] ) )
			{
				$data['sourceData'] = array(
					'id' 		=> $_POST['manufacturerSource'],
					'source' 	=> $_POST['source'],
					'order' 	=> ( isset( $_POST['manufacturerOrder'] ) ? $_POST['manufacturerOrder'] : null ),
					'arrange' 	=> ( isset( $_POST['manufacturerArrange'] ) ? $_POST['manufacturerArrange'] : null )
				);
			}
			
			elseif ( !empty( $_POST['storesSource'] ) )
			{
				$data['sourceData'] = array(
					'id' 		=> $_POST['storesSource'],
					'source' 	=> $_POST['source'],
					'order' 	=> ( isset( $_POST['merchantOrder'] ) ? $_POST['merchantOrder'] : null ),
					'arrange' 	=> ( isset( $_POST['merchantArrange'] ) ? $_POST['merchantArrange'] : null )
				);
			}
		}

		$data['stock-status'] = array( 
					'in-stock' 			=> ( isset( $_POST['inStock'] ) ? true : false ),
					'in-stock-text' 	=> ( isset( $_POST['inStockText'] ) ? $_POST['inStockText'] : '' ),
					'out-of-stock' 		=> ( isset( $_POST['outOfStock'] ) ? true : false ),
					'out-of-stock-text' => ( isset( $_POST['outOfStockText'] ) ? $_POST['outOfStockText'] : '' ),
					'on-backorder'	 	=> ( isset( $_POST['onBackorder'] ) ? true : false ),
					'on-backorder-text' => ( isset( $_POST['onBackorderText'] ) ? $_POST['onBackorderText'] : '' )
		);
		
		$data['tag'] = ( isset( $_POST['tag'] ) ? $_POST['tag'] : null );
		
		$data['tags-display'] = ( isset( $_POST['tagsDisplay'] ) ? $_POST['tagsDisplay'] : null );
		
		$data['tags-included'] = ( isset( $_POST['tags-included'] ) ? $_POST['tags-included'] : array() );
		
		$data['tags-excluded'] = ( isset( $_POST['tags-excluded'] ) ? $_POST['tags-excluded'] : array() );
		
		$data['tags-order'] = ( isset( $_POST['tagsOrder'] ) ? $_POST['tagsOrder'] : null );
		
		$data['tags-arrange'] = ( isset( $_POST['tagsArrange'] ) ? $_POST['tagsArrange'] : null );
		
		$data['category'] = ( isset( $_POST['category'] ) ? $_POST['category'] : null );
		
		$data['category-display'] = ( isset( $_POST['categoryDisplay'] ) ? $_POST['categoryDisplay'] : null );
		
		$data['categories-included'] = ( ( ( $_POST['categoryDisplay'] == 'selected' ) && isset( $_POST['categories-included'] ) ) ? $_POST['categories-included'] : null );
		
		$data['categories-excluded'] = ( ( ( $_POST['categoryDisplay'] == 'except' ) && isset( $_POST['categories-excluded'] ) ) ? $_POST['categories-excluded'] : null );
		
		$data['category-order'] = ( isset( $_POST['categoryOrder'] ) ? $_POST['categoryOrder'] : null );
		
		$data['category-arrange'] = ( isset( $_POST['categoryArrange'] ) ? $_POST['categoryArrange'] : null );
		
		$data['show-element-if'] = ( isset( $_POST['showElementIf'] ) ? $_POST['showElementIf'] : null );
		
		$data['show-element-option'] = ( isset( $_POST['showElementOption'] ) ? $_POST['showElementOption'] : null );
		
		$target = ( isset( $_POST['targetCategory'] ) ? 'category' : ( isset( $_POST['targetTag'] ) ? 'tag' : ( isset( $_POST['displayTargetCustom'] ) ? 'custom-post-type' : null ) ) );

		$targetId = ( isset( $_POST['targetCategory'] ) ? $_POST['targetCategory'] : ( isset( $_POST['targetTag'] ) ? $_POST['targetTag'] : ( isset( $_POST['displayTargetCustom'] ) ? $_POST['displayTargetCustom'] : null ) ) );

		if ( !empty( $targetId ) && ( $targetId > 0 ) )
		{
			$data['show-element-target'] = array(
					'target' => $target,
					'id' => $targetId
			);
		
			if ( $target == 'category' )
			{
				$s = AdminGetCategory( $targetId );

				if ( $s )
				{
					$data['targetCategoryDetails'] = array(
								'id' => $s['id'],
								'name' => $s['name'],
					);
				}
			}
			
			elseif ( $target == 'tag' )
			{
				$s = AdminGetTag( $targetId );
				
				if ( $s )
				{
					$data['targetTagDetails'] = array(
								'id' => $s['id'],
								'name' => $s['title'],
					);
				}
			}
		}
		
		$data['hide-element-if'] = ( ( isset( $_POST['hideElementIf'] ) && !empty( $_POST['hideElementIf'] ) ) ? $_POST['hideElementIf'] : null );
		$data['hide-element-option'] = ( isset( $_POST['hideElementOption'] ) ? $_POST['hideElementOption'] : null );
		
		$target = ( isset( $_POST['targetCategoryHide'] ) ? 'category' : ( isset( $_POST['targetTagHide'] ) ? 'tag' : ( isset( $_POST['displayTargetCustomHide'] ) ? 'custom-post-type' : null ) ) );
		
		if ( isset( $_POST['hideElementIf'] ) && !empty( $_POST['hideElementIf'] ) )
		{
			$targetId = ( isset( $_POST['targetCategoryHide'] ) ? $_POST['targetCategoryHide'] : ( isset( $_POST['targetTagHide'] ) ? $_POST['targetTagHide'] : ( isset( $_POST['displayTargetCustomHide'] ) ? $_POST['displayTargetCustomHide'] : null ) ) );
			
			$data['hide-element-target'] = array(
						'target' => $target,
						'id' => $targetId
			);
			
			if ( !empty( $targetId ) )
			{
				if ( $target == 'category' )
				{
					$s = AdminGetCategory( $targetId );
					
					if ( $s )
					{
						$data['targetCategoryHideDetails'] = array(
									'id' => $s['id'],
									'name' => $s['name'],
						);
					}
				}
				
				elseif ( $target == 'tag' )
				{
					$s = AdminGetTag( $targetId );
					
					if ( $s )
					{
						$data['targetTagHideDetails'] = array(
									'id' => $s['id'],
									'name' => $s['title'],
						);
					}
				}
			}
		}

		$query = array(
				'UPDATE' 	=> DB_PREFIX . "filter_group",
				'SET'		=> "name = :name, sef = :sef, group_order = :order, group_data = :data",
				'WHERE'		=>  "id = :id",
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				'BINDS' => array(
						array( 'PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' ),
						array( 'PARAM' => ':name', 'VAR' => $_POST['name'], 'FLAG' => 'STR' ),
						array( 'PARAM' => ':sef', 'VAR' => $sef, 'FLAG' => 'STR' ),
						array( 'PARAM' => ':order', 'VAR' => $order, 'FLAG' => 'INT' ),
						array( 'PARAM' => ':data', 'VAR' => json_encode( $data ), 'FLAG' => 'STR' )
				)
		);

		Query( $query, false, false, true );

		if ( isset( $_POST['filter'] ) && !empty( $_POST['filter'] ) && ( $_POST['hasFilters'] === 'true' ) )
		{
			$found = array();

			foreach ( $_POST['filter'] as $_id => $filter )
			{
				$query = array(
					'SELECT'	=>  'id',
					'FROM'		=> DB_PREFIX . "filters_data",
					'WHERE'		=> "id = :id",
					'PARAMS' 	=> array( 'NO_PREFIX' => true ),
					'BINDS' 	=> array(
								array( 'PARAM' => ':id', 'VAR' => $_id, 'FLAG' => 'INT' )
					)
				);
				
				$cus = Query( $query );

				if ( $cus )
				{
					$query = array(
							'UPDATE' 	=> DB_PREFIX . "filters_data",
							'SET'		=> "filter_order = :order, filter_name = :name",
							'WHERE'		=>  "id = :id",
							'PARAMS' 	=> array( 'NO_PREFIX' => true ),
							'BINDS' => array(
										array('PARAM' => ':id', 'VAR' => $cus['id'], 'FLAG' => 'INT' ),
										array('PARAM' => ':name', 'VAR' => $filter['name'], 'FLAG' => 'STR' ),
										array('PARAM' => ':order', 'VAR' => (int) $filter['sort_order'], 'FLAG' => 'INT' )
							)
					);

					Query( $query, false, false, true );

					array_push( $found, $_id );
				}
				
				else
				{
					$query = array(
						'INSERT'	=> "id_group, filter_order, filter_name",
							
						'VALUES' 	=> ":group, :order, :name",
							
						'INTO'		=> DB_PREFIX . "filters_data",
							
						'PARAMS' => array( 'NO_PREFIX' => true ),

						'BINDS' => array(
								array( 'PARAM' => ':group', 'VAR' => $id, 'FLAG' => 'INT' ),
								array('PARAM' => ':name', 'VAR' => $filter['name'], 'FLAG' => 'STR' ),
								array('PARAM' => ':order', 'VAR' => (int) $filter['sort_order'], 'FLAG' => 'INT' )
						)
					);

					$cusID = Query( $query, false, false, false, false, true );
					
					if ( $cusID )
					{
						array_push( $found, (int) $cusID );
					}
				}
			}

			//Delete any filter we don't have in 'found' array
			if ( !empty( $found ) )
			{
				$query = array(
					'DELETE' => DB_PREFIX . "filters_data",
					'WHERE'	=>  "id_group = :group AND id NOT IN (" . implode( ',', $found ) . ")",
					'PARAMS' => array( 'NO_PREFIX' => true ),
					'BINDS' 	=> array(
								array( 'PARAM' => ':group', 'VAR' => $id, 'FLAG' => 'INT' )
					)
				);

				Query( $query, false, false, true );
			}
		}
		else
		{
			//We don't want any filters, so delete them
			$query = array(
					'DELETE' => DB_PREFIX . "filters_data",
					'WHERE'	=>  "id_group = :group",
					'PARAMS' => array( 'NO_PREFIX' => true ),
					'BINDS' 	=> array(
								array( 'PARAM' => ':group', 'VAR' => $id, 'FLAG' => 'INT' )
					)
			);

			Query( $query, false, false, true );
		}
		
		Redirect( $Admin->GetUrl( 'edit-filter' . PS . 'id' . PS . $id ) );
		
		exit;
	}
}