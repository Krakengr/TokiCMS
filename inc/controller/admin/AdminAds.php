<?php defined('TOKICMS') or die('Hacking attempt...');

class AdminAds extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-ads' ) ) || !$Admin->Settings()::IsTrue( 'enable_ads' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$ads = GetAdminAds();
		
		$this->setVariable( "ads", $ads );
	}
}