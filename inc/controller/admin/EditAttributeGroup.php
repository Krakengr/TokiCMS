<?php defined('TOKICMS') or die('Hacking attempt...');

class EditAttributeGroup extends Controller {
	
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

		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'manage-post-attributes' ) ) 
				|| !$Admin->Settings()::IsTrue( 'enable_post_attributes' ) 
		)
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$AttGroup = GetSingleAttributeGroup( $id );

		if ( !$AttGroup )
			Redirect( $Admin->GetUrl( 'attribute-groups' ) );

		Theme::SetVariable( 'headerTitle', __( 'edit-attribute-group' ) . ': "' . $AttGroup['name'] . '" | ' . $Admin->SiteName() );
		
		$this->setVariable( 'AttGroup', $AttGroup );
		
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'editAttGroup_' . $id ) )
			return;
		
		//Check if we no longer want this group
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete'] ) )
		{
			//Delete the group
			$q = $this->db->delete( 'post_attr_group' )->where( "id", $id )->run();

			//Delete this group's attributes
			if ( $q )
			{
				$attrs = $this->db->from( 
				null, 
				"SELECT *
				FROM `" . DB_PREFIX . "post_attributes`
				WHERE (id_group = " . $id . ")"
				)->all();
				
				if ( $attrs )
				{
					foreach ( $attrs as $attr )
					{
						$this->db->delete( 'post_attribute_data' )->where( "id_attr", $attr['id'] )->run();
					}
				}
				
				$this->db->delete( 'post_attributes' )->where( "id_group", $id )->run();
				
				//We've deleted everything, so go to the group list page
				Redirect( $Admin->GetUrl( 'attribute-groups' ) );
			}
			
			$Admin->SetErrorMessage( __( 'an-error-happened' ), 'warning' );
			
			//We couldn't delete the group, so redirect it to its edit page again
			Redirect( $Admin->GetUrl( 'edit-attribute-group' . PS . 'id' . PS . $id ) );
		}
	
		$blogID = $catID = 0;
		$langID = 1; //Just in case we could't get a valid language...
		
		$slug = SetShortSef( "post_attr_group", 'id', 'sef', CreateSlug( $_POST['name'] ), $id );
		
		if ( strpos( $_POST['where'], '||' ) !== false )
		{
			$t = explode( '||', $_POST['where'] );
			
			if ( !empty( $t ) )
			{
				$b = _explode( $t['0'], '::' );
				$l = _explode( $t['1'], '::' );
				$c = ( isset( $t['2'] ) ? _explode( $t['2'], '::' ) : 0 );
				
				$blogID = ( !empty( $b ) ? (int) $b['id'] : $blogID );
				$langID = ( !empty( $l ) ? (int) $l['id'] : $langID );
				$catID = ( !empty( $c ) ? (int) $c['id'] : $catID );
			}
		}
		
		$everyLang = ( isset( $_POST['enable_langs'] ) ? 1 : 0 );
		
		$dbarr = array(
			"id_lang" 			=> $langID,
			"id_site" 			=> $Admin->GetSite(),
			"id_blog" 			=> $blogID,
			"id_category" 		=> $catID,
			"name" 				=> $_POST['name'],
			"group_order" 		=> $_POST['order'],
			"id_custom_type" 	=> ( isset( $_POST['postType'] ) ? $_POST['postType'] : 0 ),
			"sef" 				=> $slug,
			"every_lang" 		=> $everyLang
		);

		$this->db->update( 'post_attr_group' )->where( 'id', $id )->set( $dbarr );
		
		Redirect( $Admin->GetUrl( 'edit-attribute-group' . PS . 'id' . PS . $id ) );
	}
}