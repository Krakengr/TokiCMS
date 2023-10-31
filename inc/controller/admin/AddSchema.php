<?php defined('TOKICMS') or die('Hacking attempt...');

class AddSchema extends Controller {
	
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
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-seo' ) ) || !$Admin->Settings()::IsTrue( 'enable_seo' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'add-schema' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'add-schema' ) )
			return;
		
		$db = db();
		
		//Get the needed POST values
		$data = $_POST['schema'];
		
		$enableOn = _explode( $data['enable_on'], '::' );
		$exludeOn = _explode( $data['exclude_from'], '::' );
		
		$s = array(
				'enableOn' => array( $enableOn ),
				'exludeOn' => array( $exludeOn ),
				'data' => array(),
				'custom-data' => array()
		);
		
		//INSERT the new schema into the DB
		$dbarr = array(
			"id_site" 			=> $Admin->GetSite(),
			"title" 			=> $data['title'],
			"type" 				=> $data['schema_type'],
			"enable_on" 		=> ( isset( $enableOn['target'] ) ? $enableOn['target'] : '' ),
			"enable_on_id" 		=> ( ( isset( $enableOn['id'] ) && !empty( $enableOn['id'] ) ) ? $enableOn['id'] : 0 ),
			"exclude_from" 		=> ( isset( $exludeOn['target'] ) ? $exludeOn['target'] : '' ),
			"exclude_from_id" 	=> ( ( isset( $exludeOn['id'] ) && !empty( $exludeOn['id'] ) ) ? $exludeOn['id'] : 0 ),
			"data" 				=> json_encode( $s ),
			"added_time" 		=> time()
		);

		$id = $this->db->insert( 'schemas' )->set( $dbarr, null, true );
		
		if ( $id )
		{
			$Admin->DeleteSettingsCacheSite( 'settings' );
			Redirect( $Admin->GetUrl( 'edit-schema' . PS . 'id' . PS . $id ) );
		}
		else
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			Redirect( $Admin->GetUrl( 'schemas' ) );
		}		
	}
}