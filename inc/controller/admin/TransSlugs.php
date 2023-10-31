<?php defined('TOKICMS') or die('Hacking attempt...');

class TransSlugs extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-languages' ) || !$Admin->Settings()::IsTrue( 'translate_slugs' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'slug-translation' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'slug-translation' ) )
			return;
		
		$trans = $_POST['trans'];

		//Load the data from the DB
		$transData = $Admin->Settings()::Trans();

		//Add the translation, but only if we have multilang enabled
		$transData[$Admin->LangKey()] = array(
			'post_filter_trans' => strtolower( rawurlencode( RemoveSpecialChars( $trans['post_filter_trans'] ) ) ),
			'category_filter_trans' => strtolower( rawurlencode( RemoveSpecialChars( $trans['category_filter_trans'] ) ) ),
			'tags_filter_trans' => strtolower( rawurlencode( RemoveSpecialChars( $trans['tags_filter_trans'] ) ) )
		);

		$settingsArray = array( 'trans_data' => json_encode( $transData, JSON_UNESCAPED_UNICODE ) );
		
		$Admin->UpdateSettings( $settingsArray );
		
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}