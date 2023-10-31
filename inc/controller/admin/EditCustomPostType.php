<?php defined('TOKICMS') or die('Hacking attempt...');

class EditCustomPostType extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'manage-post-types' ) )
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$Custom = AdminGetCustomType( $id );

		if ( !$Custom )
			Redirect( $Admin->GetUrl( 'custom-post-types' ) );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-custom-post-type' ) . ': "' . $Custom['title'] . '" | ' . $Admin->SiteName() );
		
		$types = GetAdminCustomTypes();
		
		$langs = array();
		
		if ( $Admin->MultiLang() )
		{
			$langs_ = $Admin->Settings()::AllLangs();
			
			if ( !empty( $langs_ ) )
			{
				foreach( $langs_ as $li => $la )
				{
					if ( $la['lang']['id'] == $Admin->DefaultLang()['id'] )
						continue;
					
					$langs[$li] = $la;
				}
			}
		}
		
		$this->setVariable( 'PostTypes', $types );
		$this->setVariable( 'Custom', $Custom );
		$this->setVariable( 'Langs', $langs );
		
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		if ( !verify_token( 'edit_custom_type_' . $Custom['id'] ) )
			return;
		
		//Delete this post type
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete'] ) )
		{
			DeleteCustomType( $id );
			
			$Admin->EmptyCaches();
		
			Redirect( $Admin->GetUrl( 'custom-post-types' ) );
		}

		$trans = ( ( isset( $_POST['trans'] ) && !empty( $_POST['trans'] ) && is_array( $_POST['trans'] ) ) ? $_POST['trans'] : array() );
		
		$slug = SetShortSef( 'post_types', 'id', 'sef', CreateSlug( ( !empty( $_POST['slug'] ) ? $_POST['slug'] : $_POST['title'] ), true ), $id );
		
		$dbarr = array(
			"title" 		=> $_POST['title'],
			"sef" 			=> $slug,
			"description" 	=> $_POST['description'],
			"id_image" 		=> (int) $_POST['customLogoFile'],
			"id_parent" 	=> (int) $_POST['postTypeParent'],
			"trans_data" 	=> json_encode( $trans, JSON_UNESCAPED_UNICODE )
		);
		
		$this->db->update( "post_types" )->where( 'id', $id )->set( $dbarr );

		Redirect( $Admin->GetUrl( 'edit-custom-post-type' . PS . 'id' . PS . $id ) );
	}
}