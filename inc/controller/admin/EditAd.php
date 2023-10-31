<?php defined('TOKICMS') or die('Hacking attempt...');

class EditAd extends Controller {
	
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

		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-ads' ) ) || !$Admin->Settings()::IsTrue( 'enable_ads' ) )
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$Ad = GetAd( $id, $Admin->GetLang(), $Admin->GetSite() );
		
		if ( !$Ad )
			Redirect( $Admin->GetUrl( 'ads' ) );
		
		$this->setVariable( 'Ad', $Ad );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-ad' ) . ': "' . $Ad['title'] . '" | ' . $Admin->SiteName() );
		
		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'edit_ad_' . $id ) )
			Redirect( $Admin->GetUrl( 'ads' ) );
		
		$code = ( isset( $_POST['adCode'] ) ? htmlentities( $_POST['adCode'] ) : $Ad['ad_code'] );
		
		$groups = ( ( isset( $_POST['membergroups'] ) && !empty( $_POST['membergroups'] ) && is_array( $_POST['membergroups'] ) ) ? $_POST['membergroups'] : array() );
		
		$exclude = ( ( isset( $_POST['content_types'] ) && !empty( $_POST['content_types'] ) && is_array( $_POST['content_types'] ) ) ? $_POST['content_types'] : array() );
		
		$dbarr = array(
            "title" 		=> $_POST['adName'],
			"type" 			=> $_POST['adType'],
			"ad_pos" 		=> $_POST['adPosition'],
			"ad_code" 		=> $code,
			"exclude_ads" 	=> json_encode( $exclude, JSON_UNESCAPED_UNICODE ),
			"groups_data" 	=> json_encode( $groups, JSON_UNESCAPED_UNICODE ),
			"ad_img_url" 	=> ( isset( $_POST['imgUrl'] ) ? $_POST['imgUrl'] : '' ),
			"width" 		=> (int) $_POST['width'],
			"height" 		=> (int) $_POST['height'],
			"disabled" 		=> ( isset( $_POST['disable'] ) ? 1 : 0 ),
			"ad_align" 		=> $_POST['adAlign']
        );

		$this->db->update( "ads" )->where( "id", $id )->set( $dbarr );
		
		$Admin->EmptyCaches();

		Redirect( $Admin->GetUrl( 'edit-ad' . PS . 'id' . PS . $id ) );
	}
}