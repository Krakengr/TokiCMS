<?php defined('TOKICMS') or die('Hacking attempt...');

class SchemaSettings extends Controller {
	
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
		
		Theme::SetVariable( 'headerTitle', __( 'schema-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'schema-settings' ) )
			return;
		
		$Posts = $this->getVariable( 'Posts' );
		
		//Get the needed POST values
		$schema = $_POST['schema'];
		
		$settingsArray = $social = $contactPage = array();
		
		//Check if there is any social network and build the array
		//if ( isset( $_POST['social'] ) && !empty( $_POST['social'] ) )
		//{
		//	foreach ( $_POST['social'] as $_id => $_social )
		//	{
		//		$social[$_id] = Sanitize ( $_social, false );
		//	}
		//}

		//Check if we want a contact page and build the array
		if ( isset( $schema['contact_page'] ) && !empty( $schema['contact_page'] ) )
		{
			$post = GetSinglePost( $schema['contact_page'], null, false );
			
			if ( $post )
			{
				$contactPage = array(
					'id' 	=> $post['id'],
					'title' => $post['title'],
					'url' 	=> $post['postUrl']
				);
			}
		}
		
		//We are going to create a new array, so there is no need to get the settings from the DB
		$s = array(
			'site_represents' => ( isset( $schema['site_represents'] ) ? Sanitize ( $schema['site_represents'], false ) : 'disable' ),
			'site_name' => ( isset( $schema['site_name'] ) ? Sanitize ( $schema['site_name'], false ) : '' ),
			'site_logo' => ( isset( $schema['site_logo'] ) ? Sanitize ( $schema['site_logo'], false ) : '' ),
			'organization_type' => ( isset( $schema['organization_type'] ) ? Sanitize ( $schema['organization_type'], false ) : '' ),
			'enable_breadcrumbs' => ( isset( $schema['enable_breadcrumbs'] ) ? true : false ),
			'contact_type' => ( isset( $schema['contact_type'] ) ? Sanitize ( $schema['contact_type'], false ) : 'disable' ),
			'contact_page' => json_encode( $contactPage, JSON_UNESCAPED_UNICODE ),
			'contact_number' => ( isset( $schema['contact_number'] ) ? Sanitize ( $schema['contact_number'], false ) : '' ),
			'breadcrumb-data' => array(
				'breadcrumb_posts' => ( isset( $schema['breadcrumb_posts'] ) ? Sanitize ( $schema['breadcrumb_posts'], false ) : '' )
			),
			//'social-media' => $social
		);
		
		$settingsArray['schema_data'] = json_encode( $s );

		$Admin->UpdateSettings( $settingsArray );
		
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}