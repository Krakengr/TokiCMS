<?php defined('TOKICMS') or die('Hacking attempt...');

class AppAdsTxt extends Controller {
	
    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( !IsAllowedTo( 'view-site' ) )
		{
			//Don't include this file while on login or register
			if ( ( Router::WhereAmI() != 'login' ) || ( Router::WhereAmI() != 'register' ) )
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );

			$this->view();
			return;
		}
		
		$adSettings = Json( Settings::Get()['ads_data'] );

		if ( !isset( $adSettings['enable_app_ad_txt'] ) || !$adSettings['enable_app_ad_txt'] )
		{
			Router::SetNotFound();
			$this->view();
			return;
		}

		header('Content-Type:text/plain');
		
		echo html_entity_decode( $adSettings['app_ad_txt_content'] ); 

		exit(0);
	}
}