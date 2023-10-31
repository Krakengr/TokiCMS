<?php defined('TOKICMS') or die('Hacking attempt...');

class StoresAttributes extends Controller {
	
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

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
	}
}