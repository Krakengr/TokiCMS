<?php defined('TOKICMS') or die('Hacking attempt...');

class EditSchema extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $getSchemaData;

		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-seo' ) ) || !$Admin->Settings()::IsTrue( 'enable_seo' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );

		$key = (int) Router::GetVariable( 'key' );

		if ( is_null( $getSchemaData ) || empty( $getSchemaData ) )
			Redirect( $Admin->GetUrl( 'schemas' ) );
	
		Theme::SetVariable( 'headerTitle', __( 'edit-schema' ) . ': "' . $getSchemaData['schemaData']['title'] . '" | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
	
		if ( !verify_token( 'edit-schema' ) )
			return;
		
		$loadData = true;

		require_once( FUNCTIONS_ROOT . 'schema-functions.php' );
	
		include ( ARRAYS_ROOT . 'seo-arrays.php');
		
		$data = $_POST['schema'];
		
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete'] ) )
		{
			//Delete this schema from the DB
			$this->db->delete( 'schemas' )->where( "id", $key )->run();
				
			//Redirect to the lang's edit page
			@header('Location: ' . ADMIN_URI . 'schemas' . PS );
			exit;
		}
		
		$enableOn = _explode( $data['enable_on'], '::' );
		$excludeOn = _explode( $data['exclude_from'], '::' );
	
		$s = array(
				'enableOn' => array( $enableOn ),
				'excludeOn' => array( $excludeOn ),
				'data' => array(
						'article-type' => ( isset( $data['fields']['article-type'] ) ? $data['fields']['article-type'] : null ),
						'title' => ( isset( $data['fields']['title'] ) ? $data['fields']['title'] : null ),
						'name' => ( isset( $data['fields']['name'] ) ? $data['fields']['name'] : null ),
						'isbn' => ( isset( $data['fields']['isbn'] ) ? $data['fields']['isbn'] : null ),
						'total-time' => ( isset( $data['fields']['total-time'] ) ? $data['fields']['total-time'] : null ),
						'embed-url' => ( isset( $data['fields']['embed-url'] ) ? $data['fields']['embed-url'] : null ),
						'video-duration' => ( isset( $data['fields']['video-duration'] ) ? $data['fields']['video-duration'] : null ),
						'video-expires-on' => ( isset( $data['fields']['video-expires-on'] ) ? $data['fields']['video-expires-on'] : null ),
						'video-interaction-count' => ( isset( $data['fields']['video-interaction-count'] ) ? $data['fields']['video-interaction-count'] : null ),
						'book-format' => ( isset( $data['fields']['book-format'] ) ? $data['fields']['book-format'] : null ),
						'book-edition' => ( isset( $data['fields']['book-edition'] ) ? $data['fields']['book-edition'] : null ),
						'price' => ( isset( $data['fields']['price'] ) ? $data['fields']['price'] : null ),
						'price-currency' => ( isset( $data['fields']['price-currency'] ) ? $data['fields']['price-currency'] : null ),
						'country' => ( isset( $data['fields']['country'] ) ? $data['fields']['country'] : null ),
						'availability-status' => ( isset( $data['fields']['availability-status'] ) ? $data['fields']['availability-status'] : null ),
						'reference-link' => ( isset( $data['fields']['reference-link'] ) ? $data['fields']['reference-link'] : null ),
						'author-name' => ( isset( $data['fields']['author-name'] ) ? $data['fields']['author-name'] : null ),
						'image' =>( isset( $data['fields']['image'] ) ? $data['fields']['image'] : null ),
						'description' => ( isset( $data['fields']['description'] ) ? $data['fields']['description'] : null ),
						'page-url' => ( isset( $data['fields']['page-url'] ) ? $data['fields']['page-url'] : null ),
						'headline' => ( isset( $data['fields']['headline'] ) ? $data['fields']['headline'] : null ),
						'alternative-headline' => ( isset( $data['fields']['alternative-headline'] ) ? $data['fields']['alternative-headline'] : null ),
						'article-body' => ( isset( $data['fields']['article-body'] ) ? $data['fields']['article-body'] : null ),
						'published-date' => ( isset( $data['fields']['published-date'] ) ? $data['fields']['published-date'] : null ),
						'modified-date' => ( isset( $data['fields']['modified-date'] ) ? $data['fields']['modified-date'] : null ),
						'publisher-name' => ( isset( $data['fields']['publisher-name'] ) ? $data['fields']['publisher-name'] : null ),
						'publisher-logo' => ( isset( $data['fields']['publisher-logo'] ) ? $data['fields']['publisher-logo'] : null ),
						'course-code' => ( isset( $data['fields']['course-code'] ) ? $data['fields']['course-code'] : null ),
						'course-provider' => ( isset( $data['fields']['course-provider'] ) ? $data['fields']['course-provider'] : null ),
						'course-instance-name' => ( isset( $data['fields']['course-instance-name'] ) ? $data['fields']['course-instance-name'] : null ),
						'course-instance-description' => ( isset( $data['fields']['course-instance-description'] ) ? $data['fields']['course-instance-description'] : null ),
						'mode' => ( isset( $data['fields']['mode'] ) ? $data['fields']['mode'] : null ),
						'status' => ( isset( $data['fields']['status'] ) ? $data['fields']['status'] : null ),
						'location-name' => ( isset( $data['fields']['location-name'] ) ? $data['fields']['location-name'] : null ),
						'street-address' => ( isset( $data['fields']['street-address'] ) ? $data['fields']['street-address'] : null ),
						'locality' => ( isset( $data['fields']['locality'] ) ? $data['fields']['locality'] : null ),
						'postal-code' => ( isset( $data['fields']['postal-code'] ) ? $data['fields']['postal-code'] : null ),
						'region' => ( isset( $data['fields']['region'] ) ? $data['fields']['region'] : null ),
						'attendance-mode' => ( isset( $data['fields']['attendance-mode'] ) ? $data['fields']['attendance-mode'] : null ),
						'start-date' => ( isset( $data['fields']['start-date'] ) ? $data['fields']['start-date'] : null ),
						'end-date' => ( isset( $data['fields']['end-date'] ) ? $data['fields']['end-date'] : null ),
						'online-course-url' => ( isset( $data['fields']['online-course-url'] ) ? $data['fields']['online-course-url'] : null ),
						'course-organizer-name' => ( isset( $data['fields']['course-organizer-name'] ) ? $data['fields']['course-organizer-name'] : null ),
						'course-organizer-url' => ( isset( $data['fields']['course-organizer-url'] ) ? $data['fields']['course-organizer-url'] : null ),
						'course-location-name' => ( isset( $data['fields']['course-location-name'] ) ? $data['fields']['course-location-name'] : null ),
						'course-location-address' => ( isset( $data['fields']['course-location-address'] ) ? $data['fields']['course-location-address'] : null ),
						'organizer-name' => ( isset( $data['fields']['organizer-name'] ) ? $data['fields']['organizer-name'] : null ),
						'organizer-url' => ( isset( $data['fields']['organizer-url'] ) ? $data['fields']['organizer-url'] : null ),
						'valid-from' => ( isset( $data['fields']['valid-from'] ) ? $data['fields']['valid-from'] : null ),
						'offer-url' => ( isset( $data['fields']['offer-url'] ) ? $data['fields']['offer-url'] : null ),
						'performer' => ( isset( $data['fields']['performer'] ) ? $data['fields']['performer'] : null ),
						'rating' => ( isset( $data['fields']['rating'] ) ? $data['fields']['rating'] : null ),
						'review-count' => ( isset( $data['fields']['review-count'] ) ? $data['fields']['review-count'] : null ),
						'claim-reviewed' => ( isset( $data['fields']['claim-reviewed'] ) ? $data['fields']['claim-reviewed'] : null ),
						'claim-url-original' => ( isset( $data['fields']['claim-url-original'] ) ? $data['fields']['claim-url-original'] : null ),
						'claim-url-other' => ( isset( $data['fields']['claim-url-other'] ) ? $data['fields']['claim-url-other'] : null ),
						'rating-value' => ( isset( $data['fields']['rating-value'] ) ? $data['fields']['rating-value'] : null ),
						'rating-value-worst' => ( isset( $data['fields']['rating-value-worst'] ) ? $data['fields']['rating-value-worst'] : null ),
						'rating-value-best' => ( isset( $data['fields']['rating-value-best'] ) ? $data['fields']['rating-value-best'] : null ),
						'alternate-name' => ( isset( $data['fields']['alternate-name'] ) ? $data['fields']['alternate-name'] : null ),
						'event-type' => ( isset( $data['fields']['event-type'] ) ? $data['fields']['event-type'] : null ),
						//'custom-text' => ( isset( $data['fields']['custom-text'] ) ? $data['fields']['custom-text'] : null ),
						//'custom-number' => ( isset( $data['fields']['custom-number'] ) ? $data['fields']['custom-number'] : null ),
				),
				'custom-data' => array()
		);
		
		//Update the schema
		$dbarr = array(
			"title" 			=> $data['title'],
			"data" 				=> json_encode( $s ),
			"enable_on" 		=> ( isset( $enableOn['target'] ) ? $enableOn['target'] : '' ),
			"enable_on_id" 		=> ( ( isset( $enableOn['id'] ) && !empty( $enableOn['id'] ) ) ? $enableOn['id'] : 0 ),
			"exclude_from" 		=> ( isset( $exludeOn['target'] ) ? $exludeOn['target'] : '' ),
			"exclude_from_id" 	=> ( ( isset( $exludeOn['id'] ) && !empty( $exludeOn['id'] ) ) ? $exludeOn['id'] : 0 )
		);

		$this->db->update( 'schemas' )->where( 'id', $key )->set( $dbarr );

		//Delete the cache file so the new data to be loaded on the site
		$Admin->DeleteSettingsCacheSite( 'settings' );

		//Redirect to the same page
		Redirect( $Admin->GetUrl( 'edit-schema' . PS . 'id' . PS . $key ) );
	}
}