<?php defined('TOKICMS') or die('Hacking attempt...');

class AddAutoContentSource extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-auto-content' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'add-source' ) . ' | ' . $Admin->SiteName() );
		
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'add-source' ) )
			return;
		
		$auth = $this->getVariable( 'AuthUser' );

		$userId = $auth['id_member'];
		
		$customFields = $extraData = array();
	
		if ( isset( $_POST['custom_fields'] ) && !empty( $_POST['custom_fields'] ) && is_array( $_POST['custom_fields'] ) )
		{
			foreach( $_POST['custom_fields'] as $id => $cus )
				$customFields[$id] = array( 
					'value' => EscapeRegex( $cus['value'] ),
					'name' => Sanitize ( $cus['name'], false )
				);
		}

		$extraData['search_replace'] = ( ( isset( $_POST['search_replace'] ) && !empty( $_POST['search_replace'] ) && is_array( $_POST['search_replace'] ) ) ? $_POST['search_replace'] : array() );
	
		$extraData['regex'] = array(
			'rotate_ip_address' 	=> ( isset( $_POST['rotate_ip_address'] ) 	? true : false ),
			'crawl_as' 				=> ( isset( $_POST['crawl_as'] ) 	? Sanitize( $_POST['crawl_as'], false ) : 'normal' ),
			'regex_title' 			=> ( isset( $_POST['regex_title'] ) 		? EscapeRegex( $_POST['regex_title'] ) : '' ),
			'regex_descr' 			=> ( isset( $_POST['regex_descr'] ) 		? EscapeRegex( $_POST['regex_descr'] ) : ''),
			'regex_image' 			=> ( isset( $_POST['regex_image'] ) 		? EscapeRegex( $_POST['regex_image'] ) : '' ),
			'regex_content' 		=> ( isset( $_POST['regex_content'] ) 		? EscapeRegex( $_POST['regex_content'] ) : '' ),
			'regex_tags' 			=> ( isset( $_POST['regex_tags'] ) 			? EscapeRegex( $_POST['regex_tags'] ) : '' ),
			'regex_tags_container' 	=> ( isset( $_POST['regex_tags_container'] ) ? EscapeRegex( $_POST['regex_tags_container'] ) : '' ),
			'feed_url_wrapper' 		=> ( isset( $_POST['feed_url_wrapper'] ) 	? EscapeRegex( $_POST['feed_url_wrapper'] ) : '' ),
			'custom_fields' 		=> $customFields
		);
		
		$category 	= (int) $_POST['category'];
		$autoCat	= '';
		$xmlData	= array();
		
		if ( $_POST['source_type'] === 'xml' )
		{
			$xmlValues	= array();

			if ( !empty( $_POST['xml_feed_values'] ) )
			{
				foreach( $_POST['xml_feed_values'] as $xval )
				{
					$xmlValues[] = array(
						'attribute'	=> Sanitize( $xval['attribute'], false ),
						'value'		=> Sanitize( $xval['value'], false )
					);
				}
			}
		
			$xmlData = array(
				'file_type' 		=> ( isset( $_POST['xml_type'] ) 			? Sanitize( $_POST['xml_type'], false ) : '' ),
				'items_wrapper' 	=> ( isset( $_POST['xml_items_wrapper'] )	? Sanitize( $_POST['xml_items_wrapper'], false ) : '' ),
				'item_wrapper' 		=> ( isset( $_POST['xml_item_wrapper'] ) 	? Sanitize( $_POST['xml_item_wrapper'], false ) : '' ),
				'copy_xml_locally' 	=> ( isset( $_POST['copy_xml_locally'] ) 	? (int) $_POST['copy_xml_locally'] : 0 ),
				'id_store' 			=> ( !empty( $_POST['storeSelect'] ) 		? (int) $_POST['storeSelect'] : 0 ),
				'values'			=> $xmlValues
			);
		}
		
		if ( empty( $category ) )
		{
			$auto = $_POST['auto_category'];
			
			if ( empty( $auto ) )
			{
				$category 	= GetSiteDefaultCategory( $Admin->GetSite(), $Admin->GetLang() );
				$category	= $category['id'];
			}
			
			else
			{
				$autoCat = $auto;
			}
		}

		$dbarr = array(
			"id_site" 				=> $Admin->GetSite(),
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
			"added_time" 			=> time(),
			"set_original_date" 	=> ( isset( $_POST['set_original_date'] ) ? 'true' : 'false' ),
			"xml_data" 				=> json_encode( $xmlData, JSON_UNESCAPED_UNICODE )
		);
		
		$id = $this->db->insert( 'auto_sources' )->set( $dbarr, null, true );

		if ( $id )
		{
			Redirect( $Admin->GetUrl( 'edit-content-source' . PS . 'id' . PS . $id ) );
		}
		
		else
		{
			$Admin->SetAdminMessage( __( 'an-error-happened' ) );
			return;
		}
	}
}