<?php defined('TOKICMS') or die('Hacking attempt...');

class EditWidget extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-widgets' ) )
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$Widget = GetWidget( $id, $Admin->GetSite() );

		if ( !$Widget )
			Redirect( $Admin->GetUrl( 'widgets' ) );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-widget' ) . ': "' . $Widget['title'] . '" | ' . $Admin->SiteName() );
		
		$this->setVariable( 'Widget', $Widget );
		
		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		// Verify if the token is correct
		if ( !verify_token( 'edit_widget_' . $id ) )
			Redirect( $Admin->GetUrl( 'widgets' ) );
		
		//If we want to delete a widget, do it here
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete']  ) )
		{
			$this->db->delete( 'widgets' )->where( "id", $id )->run();
	
			//Redirect to the dashboard
			@header('Location: ' . $Admin->GetUrl( 'widgets' ) );
			exit;
		}
		
		$num = ( ( isset( $_POST['num'] ) && is_numeric( $_POST['num'] ) ) ? $_POST['num'] : 5 );
		
		$num = ( ( $num > 10 ) || ( $num == 0 ) ? 5 : $num );
		
		$groups = ( ( isset( $_POST['membergroups'] ) && !empty( $_POST['membergroups'] ) && is_array( $_POST['membergroups'] ) ) ? $_POST['membergroups'] : array() );
		
		//We can update the DB
		$dbarr = array(
			"title" 				=> $_POST['widgetName'],
			"type" 					=> $_POST['widgetType'],
			"enable_on" 			=> $_POST['widgetVisibilityShow'],
			"exclude_from" 			=> ( ( $_POST['widgetVisibilityHide'] == $_POST['widgetVisibilityShow'] ) ? '' : $_POST['widgetVisibilityHide'] ),
			"data" 					=> ( isset( $_POST['widgetCode'] ) ? htmlentities( $_POST['widgetCode'] ) : '' ),
			"build_in" 				=> ( isset( $_POST['built-in'] ) ? $_POST['built-in'] : '' ),
			"num" 					=> $num,
			"show_num_posts" 		=> ( isset( $_POST['showPostNum'] ) ? 1 : 0 ),
			"show_dropdown_list" 	=> ( isset( $_POST['dropDown'] ) ? 1 : 0 ),
			"function_name" 		=> ( isset( $_POST['functionName'] ) ? $_POST['functionName'] : '' ),
			"groups_data" 			=> json_encode( $groups, JSON_UNESCAPED_UNICODE ),
			"disabled" 				=> ( isset( $_POST['disable'] ) ? 1 : 0 ),
			"id_ad" 				=> ( isset( $_POST['widgetAd'] ) ? $_POST['widgetAd'] : 0 ),
			"theme_pos" 			=> ( ( isset( $_POST['widgetThemePos'] ) && !empty( $_POST['widgetThemePos'] ) ) ? $_POST['widgetThemePos'] : 'primary' )
		);
		
		$this->db->update( "widgets" )->where( 'id', $id )->set( $dbarr );
		
		$Admin->DeleteSettingsCacheSite( 'widgets' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( 'edit-widget' . PS . 'id' . PS . $id ) );
		exit;
	}
}