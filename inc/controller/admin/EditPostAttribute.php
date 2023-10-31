<?php defined('TOKICMS') or die('Hacking attempt...');

class EditPostAttribute extends Controller {
	
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
		
		if 
		( 
			( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'manage-post-attributes' ) ) || !$Admin->Settings()::IsTrue( 'enable_post_attributes' )
		)
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
	
		$Att = ΑdminGetSinglePostAttribute( $id );
		
		if ( !$Att )
		{
			Redirect( $Admin->GetUrl( 'post-attributes' ) );
		}
		
		$Langs = ( $Admin->MultiLang() ? $Admin->SiteOtherLangs() : null );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-attribute-group' ) . ': "' . $Att['name'] . '" | ' . $Admin->SiteName() );
		
		$this->setVariable( 'Att', $Att );
		$this->setVariable( 'Langs', $Langs );
			
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'editPostAtt_' . $id ) )
			return;
		
		//Check if we no longer want this group
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete'] ) )
		{
			//Delete the attribute
			$q = $this->db->delete( 'post_attributes' )->where( "id", $id )->run();
			
			if ( $q )
				Redirect( $Admin->GetUrl( 'post-attributes' ) );
			
			else
				Redirect( $Admin->GetUrl( 'edit-post-attribute' . PS . 'id' . PS . $id ) );
		}
	
		if ( !empty( $_POST['trans'] ) )
		{
			$trans = array();
			
			foreach( $_POST['trans'] as $lId => $val )
			{
				if ( empty( $val ) )
					continue;
				
				$trans['lang-' . $lId] = array( 'value' => $val );
			}
		}
		else
			$trans = array();
		
		$slug = SetShortSef( 'post_attributes', 'id', 'sef', CreateSlug( $_POST['name'], true ), $id, null, false );
		
		$dbarr = array(
			"id_group" 		=> $_POST['group'],
			"name" 			=> $_POST['name'],
			"sef" 			=> $slug,
			"attr_order" 	=> $_POST['order'],
			"trans_data" 	=> json_encode( $trans, JSON_UNESCAPED_UNICODE )
		);

		$this->db->update( 'post_attributes' )->where( 'id', $id )->set( $dbarr );

		Redirect( $Admin->GetUrl( 'edit-post-attribute' . PS . 'id' . PS . $id ) );
	}
}