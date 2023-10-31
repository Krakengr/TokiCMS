<?php defined('TOKICMS') or die('Hacking attempt...');

class AdminStores extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'stores' ) . ' | ' . $Admin->SiteName() );
		
		$orderBy 	= ( ( Router::GetVariable( 'orderBy' ) && ( Router::GetVariable( 'orderBy' ) == 'url' ) ) ? 'url' : 'name' );
		$order 	 	= ( ( Router::GetVariable( 'order' ) && ( Router::GetVariable( 'order' ) == 'desc' ) ) ? 'desc' : 'asc' );
		$nextOrder 	= ( ( $order == 'asc' ) ? 'desc' : 'asc' );
		$arrow 	 	= ( ( $nextOrder == 'desc' ) ? 'down' : 'up' );
		$stores 	= array();
		$search		= Sanitize( $Admin->GetSearchString(), false );

		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
		{
			$data = $this->db->from( 
			null, 
			"SELECT v.id_store, v.id_image, v.name, v.url, v.description, t.name as typ
			FROM `" . DB_PREFIX . "stores` AS v
			LEFT JOIN `" . DB_PREFIX . "store_types` AS t ON t.id = v.id_type
			WHERE (v.id_site = " . $Admin->GetSite() . ") AND (v.id_parent = 0)"
			. ( !empty( $search ) ? " AND (v.name LIKE '%" . $search . "%' OR v.description LIKE '%" . $search . "%')" : '' ) . " ORDER BY v." . $orderBy . " " . strtoupper( $order )
			)->all();

			if ( $data )
			{
				foreach( $data as $d )
				{
					$stores[$d['id_store']] = $d;
					
					$ch = $this->db->from( 
					null, 
					"SELECT v.id_store, v.id_image, v.name, v.url, v.description, t.name as typ
					FROM `" . DB_PREFIX . "stores` AS v
					LEFT JOIN `" . DB_PREFIX . "store_types` AS t ON t.id = v.id_type
					WHERE (v.id_parent = " . $d['id_store'] . ")
					ORDER BY v.name ASC"
					)->all();
					
					if ( $ch )
					{
						foreach( $ch as $c )
						{
							$stores[$d['id_store']]['childs'][$c['id_store']] = $c;
						}
					}
					
					else
					{
						$stores[$d['id_store']]['childs'] = array();
					}
				}
			}
		}
		
		else
		{
			if ( !empty( $_POST['search'] ) )
			{
				$url = $Admin->CustomAdminUrl( $Admin->GetSite(), $Admin->GetLang(), $Admin->GetBlog(), $_POST['search'], null, $_POST['order'], $_POST['sort'] );
				Redirect( $url );
			}
			
			Redirect( $Admin->GetUrl( 'stores' ) );
		}
		
		$this->setVariable( 'Stores', 		$stores );
		$this->setVariable( 'order', 		$order );
		$this->setVariable( 'nextOrder', 	$nextOrder );
		$this->setVariable( 'isSearch', 	(!empty( $search ) ? true : false ) );
		$this->setVariable( 'arrow', 		$arrow );
		$this->setVariable( 'orderBy', 		$orderBy );
		$this->setVariable( 'search', 		$search );
	}
}