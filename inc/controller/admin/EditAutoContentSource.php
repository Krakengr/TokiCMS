<?php defined('TOKICMS') or die('Hacking attempt...');

class EditAutoContentSource extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-auto-content' ) )
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$autoSourceData = AdminGetAutoSource( (int) Router::GetVariable( 'key' ) );
		
		if ( !$autoSourceData )
			Redirect( $Admin->GetUrl( 'auto-content-sources' ) );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-source' ) . ': "' . $autoSourceData['title'] . '" | ' . $Admin->SiteName() );
		
		$Categories = GetCategoriesList( $Admin->GetSite() );

		$Atts		= AdminGetAttributes( $Admin->GetSite(), $Admin->GetSite(), $Admin->GetBlog() );
		
		$this->setVariable( 'autoSourceData', $autoSourceData );
		$this->setVariable( 'Categories', $Categories );
		$this->setVariable( 'Atts', $Atts );

		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
	
		if ( !verify_token( 'edit-content-source' ) )
			return;
		
		$auth = $this->getVariable( 'AuthUser' );

		$userId = $auth['id_member'];
	
		//Let's loop through custom fields so we can sanitize it properly
		if ( isset( $_POST['custom_fields'] ) && !empty( $_POST['custom_fields'] ) && is_array( $_POST['custom_fields'] ) )
		{
			$customFields = array();

			foreach( $_POST['custom_fields'] as $cusDt )
			{
				$customFields[] = array( 
					'name' 	=> Sanitize ( $cusDt['name'], false ),
					'field' => Sanitize ( $cusDt['field'], false ),
					'value' => EscapeRegex( $cusDt['value'] ) 
				);
			}
		}
		else
			$customFields = array();
		
		//Do the same for the Search-Replace data
		if ( isset( $_POST['search_replace'] ) && !empty( $_POST['search_replace'] ) && is_array( $_POST['search_replace'] ) )
		{
			$searchReplace = array();
			
			foreach( $_POST['search_replace'] as $searchId => $searchDt )
				$searchReplace[$searchId] = array( 
					'search' => Sanitize ( $searchDt['search'], false ),
					'replace' => Sanitize ( $searchDt['replace'], false )
				);
		}
		else
			$searchReplace = array();

		$extraData['search_replace'] = $searchReplace;
	
		//Create the regex data array
		$extraData['regex'] = array(
			'rotate_ip_address' 	=> ( isset( $_POST['rotate_ip_address'] ) ? true : false ),
			'crawl_as' 				=> ( isset( $_POST['crawl_as'] ) 		? Sanitize( $_POST['crawl_as'], false ) : 'normal' ),
			'regex_title' 			=> ( isset( $_POST['regex_title'] ) 	? EscapeRegex( $_POST['regex_title'] ) : '' ),
			'regex_descr' 			=> ( isset( $_POST['regex_descr'] ) 	? EscapeRegex( $_POST['regex_descr'] ) : '' ),
			'regex_image' 			=> ( isset( $_POST['regex_image'] ) 	? EscapeRegex( $_POST['regex_image'] ) : '' ),
			'regex_content' 		=> ( isset( $_POST['regex_content'] ) 	? EscapeRegex( $_POST['regex_content'] ) : '' ),
			'regex_tags' 			=> ( isset( $_POST['regex_tags'] ) 		? EscapeRegex( $_POST['regex_tags'] ) : '' ),
			'regex_tags_container' 	=> ( isset( $_POST['regex_tags_container'] ) ? EscapeRegex( $_POST['regex_tags_container'] ) : '' ),
			'feed_url_wrapper' 		=> ( isset( $_POST['feed_url_wrapper'] ) 	? EscapeRegex( $_POST['feed_url_wrapper'] ) : '' ),
			'custom_fields' 		=> $customFields
		);
		
		$category 	= (int) $_POST['category'];
		$autoCat	= '';
		
		
		$xmlData	= array(
		
		
		);
		
		if ( empty( $category ) )
		{
			$auto = $_POST['auto_category'];
			
			if ( empty( $auto ) )
			{
				$category = GetSiteDefaultCategory( $Admin->GetSite(), $Admin->GetLang() );
			}
			
			else
			{
				$autoCat = $auto;
			}
		}
		
		$dbarr = array(
			"user_id" 				=> ( isset( $_POST['postAuthor'] ) ? $_POST['postAuthor'] : $userId ),
			"id_category" 			=> $category,
			"id_store" 				=> ( !empty( $_POST['storeSelect2'] ) ? (int) $_POST['storeSelect2'] : 0 ),
			"title" 				=> $_POST['title'],
			"url" 					=> $_POST['url'],
			"auto_category" 		=> $autoCat,
			"avoid_words" 			=> ( !empty( $_POST['avoid_words'] ) ? Sanitize( $_POST['avoid_words'], false ) : '' ),
			"required_words" 		=> ( !empty( $_POST['required_words'] ) ? Sanitize( $_POST['required_words'], false ) : '' ),
			"copy_images" 			=> ( isset( $_POST['copy_images'] ) ? 'true' : 'false' ),
			"set_first_image_cover" => ( isset( $_POST['set_cover'] ) ? 'true' : 'false' ),
			"strip_html" 			=> ( isset( $_POST['strip_html'] ) ? 'true' : 'false' ),
			"remove_images" 		=> ( isset( $_POST['remove_images'] ) ? 'true' : 'false' ),
			"strip_links" 			=> ( isset( $_POST['strip_links'] ) ? 'true' : 'false' ),
			"skip_posts_no_images" 	=> ( isset( $_POST['skip_posts_without_images'] ) ? 'true' : 'false' ),
			"is_multiple_source" 	=> ( isset( $_POST['multiple_source'] ) ? 'true' : 'false' ),
			"set_source_link" 		=> ( isset( $_POST['set_source'] ) ? 'true' : 'false' ),
			"add_tags" 				=> ( isset( $_POST['title_to_tags'] ) ? (int) $_POST['title_to_tags'] : 0 ),
			"post_status" 			=> ( !empty( $_POST['post_status'] ) ? Sanitize( $_POST['post_status'], false ) : '' ),
			"source_type" 			=> ( !empty( $_POST['source_type'] ) ? Sanitize( $_POST['source_type'], false ) : 'rss' ),
			"post_type" 			=> ( !empty( $_POST['post_type'] ) ? Sanitize( $_POST['post_type'], false ) : '' ),
			"auto_delete_days" 		=> (int) $_POST['auto_deletion'],
			"max_posts" 			=> (int) $_POST['max_posts'],
			"skip_posts_days" 		=> $_POST['skip_older_posts'],
			"post_template" 		=> $_POST['post_template'],
			"custom_data" 			=> json_encode( $extraData, JSON_UNESCAPED_UNICODE ),
			"set_original_date" 	=> ( isset( $_POST['set_original_date'] ) ? 'true' : 'false' ),
			"xml_data" 				=> json_encode( $xmlData, JSON_UNESCAPED_UNICODE )
		);

		$this->db->update( "auto_sources" )->where( 'id', $id )->set( $dbarr );
		
		Redirect( $Admin->GetUrl( 'edit-content-source' . PS . 'id' . PS . $id ) );
	}
}