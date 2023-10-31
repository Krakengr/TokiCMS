<?php defined('TOKICMS') or die('Hacking attempt...');

class LinkManagerSettings extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) || !$Admin->Settings()::IsTrue( 'enable_link_manager' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );

		Theme::SetVariable( 'headerTitle', __( 'link-manager-settings' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !verify_token( 'link-manager-settings' ) )
			return;
		
		$prefix_slug = '';	

		if ( !empty( $_POST['short']['base_slug_prefix'] ) )
		{
			//We can't use "out" as slug prefix
			if ( in_array( $_POST['short']['base_slug_prefix'], $Admin->Settings()::RegisteredSlugs() ) )
			{
				$prefix_slug = 'go';
				
				$Admin->SetErrorMessage( __( 'base-slug-prefix-error' ), 'warning' );
			}
			
			else
			{
				$prefix_slug = CreateSlug( $_POST['short']['base_slug_prefix'] );
			}
		}
		
		//We are going to create a new array, so there is no need to get the settings from the DB
		$broken = array(
				'content_types' => ( ( isset( $_POST['broken']['content_types'] ) && is_array( $_POST['broken']['content_types'] ) ) ? array_values( $_POST['broken']['content_types'] ) : array() ),
				
				'content_status' => ( ( isset( $_POST['broken']['content_status'] ) && is_array( $_POST['broken']['content_status'] ) ) ? array_values( $_POST['broken']['content_status'] ) : array() ),
				
				'link_types' => ( ( isset( $_POST['broken']['link_types'] ) && is_array( $_POST['broken']['link_types'] ) ) ? array_values( $_POST['broken']['link_types'] ) : array() ),
				
				'post_modified_date' => ( isset( $_POST['broken']['post_modified_date'] ) ? true : false )
		);
		
		$short = array(
			'enable' => ( isset( $_POST['short']['enable'] ) ? true : false ),
			
			'redirection' => ( isset( $_POST['short']['redirection'] ) ? Sanitize ( $_POST['short']['redirection'], false ) : '' ),
				
			'base_slug_prefix' => $prefix_slug,
		
			'slug_character_count' => ( ( isset( $_POST['short']['slug_character_count'] ) && is_numeric( $_POST['short']['slug_character_count'] ) && ( $_POST['short']['slug_character_count'] >= 2 ) ) ? (int) $_POST['short']['slug_character_count'] : 4 ),
				
			'enable_tracking' => ( isset( $_POST['short']['enable_tracking'] ) ? true : false ),
				
			'enable_google_analytics' => ( isset( $_POST['short']['enable_google_analytics'] ) ? true : false ),
				
			'global_head_scripts' => ( isset( $_POST['short']['global_head_scripts'] ) ? Sanitize ( $_POST['short']['global_head_scripts'], false ) : '' ),
				
			'filter_robots' => ( isset( $_POST['short']['filter_robots'] ) ? true : false ),
				
			'post_shortlinks' => ( isset( $_POST['short']['post_shortlinks'] ) ? true : false ),
				
			'category' => ( isset( $_POST['short']['category'] ) ? Sanitize ( $_POST['short']['category'], false ) : '' ),
			
			'autogenerated_path_type' => ( isset( $_POST['short']['autogenerated_path_type'] ) ? Sanitize ( $_POST['short']['autogenerated_path_type'], false ) : 'alphanumeric' ),
			
			'autogenerated_path_case' => ( isset( $_POST['short']['autogenerated_path_case'] ) ? Sanitize ( $_POST['short']['autogenerated_path_case'], false ) : 'any' ),
			
			'page_shortlinks' => ( isset( $_POST['short']['page_shortlinks'] ) ? true : false ),
				
			'enable_public_links' => ( isset( $_POST['short']['enable_public_links'] ) ? true : false ),
	
			'allow_group' => ( ( isset( $_POST['short']['allow_group'] ) && is_array( $_POST['short']['allow_group'] ) ) ? array_values( $_POST['short']['allow_group'] ) : array() ),
			
			'show_ads' => ( isset( $_POST['short']['show_ads'] ) ? Sanitize ( $_POST['short']['show_ads'], false ) : '' ),
			
			'ads_group' => ( ( isset( $_POST['short']['ads_group'] ) && is_array( $_POST['short']['ads_group'] ) ) ? array_values( $_POST['short']['ads_group'] ) : array() )
		);
		
		$internal = array(
				'enable_settings' => ( isset( $_POST['internal']['enable_settings'] ) ? true : false ),
				'open_links_new_tab' => ( isset( $_POST['internal']['open_links_new_tab'] ) ? true : false ),
				'nofollow_links' => ( isset( $_POST['internal']['nofollow_links'] ) ? true : false ),
				'overwrite_existing_values' => ( isset( $_POST['internal']['overwrite_existing_values'] ) ? true : false ),
				'css_class' => ( isset( $_POST['internal']['css_class'] ) ? Sanitize ( $_POST['internal']['css_class'], false ) : '' ),
				'add_rel' => ( ( isset( $_POST['internal']['add_rel'] ) && is_array( $_POST['internal']['add_rel'] ) ) ? array_values( $_POST['internal']['add_rel'] ) : array() )
		);
		
		$external = array(
				'enable_settings' => ( isset( $_POST['external']['enable_settings'] ) ? true : false ),
				'open_links_new_tab' => ( isset( $_POST['external']['open_links_new_tab'] ) ? true : false ),
				'nofollow_links' => ( isset( $_POST['external']['nofollow_links'] ) ? true : false ),
				'overwrite_existing_values' => ( isset( $_POST['external']['overwrite_existing_values'] ) ? true : false ),
				'css_class' => ( isset( $_POST['external']['css_class'] ) ? Sanitize ( $_POST['external']['css_class'], false ) : '' ),
				'add_rel' => ( ( isset( $_POST['external']['add_rel'] ) && is_array( $_POST['external']['add_rel'] ) ) ? array_values( $_POST['external']['add_rel'] ) : array() )
		);
		
		//This is the final array contains all the above arrays
		$s = array(
			'internal-link-settings' 	=> $internal,
			'external-link-settings' 	=> $external,
			'short-link-settings' 		=> $short,
			'broken-link-settings' 		=> $broken
		);
		
		//Get the array ready
		$settingsArray['link_manager_options'] = json_encode( $s );

		//Update the settings
		$Admin->UpdateSettings( $settingsArray );
		
		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}