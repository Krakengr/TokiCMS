<?php defined('TOKICMS') or die('Hacking attempt...');

class EditTable extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $Query;
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-forms' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$id = (int) Router::GetVariable( 'key' );
		
		$Form = GetSingleForm( $id );

		if ( !$Form )
			Redirect( $Admin->GetUrl( 'tables' ) );
		
		//Make sure we have the correct type
		if ( $Form['type'] == 'form' )
		{
			Redirect( $Admin->GetUrl( 'edit-form' . PS . 'id' . PS . $id ) );
		}
		
		Theme::SetVariable( 'headerTitle', __( 'edit-table' ) . ': "' . $Form['name'] . '" | ' . $Admin->SiteName() );
		
		$this->setVariable( 'Form', $Form );

		//Don't do anything if there is no POST
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !verify_token( 'edit_table_' . $id ) )
			return;
		
		//Maybe we want to delete this table?
		if ( isset( $_POST['delete'] ) )
		{
			$q = $this->db->delete( 'forms' )->where( "id", $id )->run();

			if ( $q )
			{
				//Delete also any elements this table may have
				$this->db->delete( 'form_elements' )->where( "id_form", $id )->run();		
			}

			Redirect( $Admin->GetUrl( 'tables' ) );
		}
		
		$optionId = ( !empty( $_POST['displayTargetCustom'] ) ? $_POST['displayTargetCustom'] : 0 );
		
		$templName = ( !empty( $_POST['formTemplateName'] ) ? $_POST['formTemplateName'] : $_POST['title'] );
		
		$templateName = ( isset( $_POST['save-template'] ) ? $templName : ( isset( $_POST['delete-template'] ) ? '' : ( isset( $Form['data']['template_name'] ) ? $Form['data']['template_name'] : '' ) ) );
		
		$templateId = ( isset( $Form['data']['template_id'] ) ? $Form['data']['template_id'] : 0 );
		
		$targetCategory = $targetTag = $targetTagHide = $targetCategoryHide = $targetCategoryAuto = $targetTagAuto = $targetBlogAuto = array();
		
		$showIf = ( isset( $_POST['show-table-if'] ) ? Sanitize( $_POST['show-table-if'], false ) : '' );
		$hideIf = ( isset( $_POST['hide-table-if'] ) ? Sanitize( $_POST['hide-table-if'], false ) : '' );
		$pos	= ( isset( $_POST['auto-insert-table'] ) ? Sanitize( $_POST['auto-insert-table'], false ) : '' );
		$option	= ( isset( $_POST['show-element-option'] ) ? Sanitize( $_POST['show-element-option'], false ) : '' );
		
		if ( isset( $_POST['targetCategoryAuto'] ) && !empty( $_POST['targetCategoryAuto'] ) )
		{
			$s = AdminGetCategory( $_POST['targetCategoryAuto'] );

			if ( $s )
			{
				$targetCategoryAuto = array(
						'id' => $s['id'],
						'name' => $s['name']
				);
				
				$optionId = $s['id'];
				
				$showIf = 'category';
			}
		}
		
		elseif ( isset( $_POST['targetCategory'] ) && !empty( $_POST['targetCategory'] ) )
		{
			$s = AdminGetCategory( $_POST['targetCategory'] );

			if ( $s )
			{
				$targetCategory = array(
					'id' 	=> $s['id'],
					'name' 	=> $s['name']
				);
				
				$optionId = $s['id'];
			}
		}
		
		if ( isset( $_POST['targetCategoryHide'] ) && !empty( $_POST['targetCategoryHide'] ) )
		{
			if ( empty( $targetCategory ) || ( $targetCategory['id'] != $_POST['targetCategoryHide'] ) )
			{
				$s = AdminGetCategory( $_POST['targetCategoryHide'] );

				if ( $s )
				{
					$targetCategoryHide = array(
						'id' => $s['id'],
						'name' => $s['name']
					);
				}
			}
		}
		
		if ( isset( $_POST['targetBlogAuto'] ) && !empty( $_POST['targetBlogAuto'] ) )
		{
			$s = AdminGetBlog( $_POST['targetBlogAuto'] );

			if ( $s )
			{
				$targetBlogAuto = array(
					'id' 	=> $s['id_blog'],
					'name' 	=> $s['name']
				);
				
				$optionId = $s['id_blog'];
				
				$showIf = 'blog';
			}
		}
		
		if ( isset( $_POST['targetTagAuto'] ) && !empty( $_POST['targetTagAuto'] ) )
		{
			$s = AdminGetTag( $_POST['targetTagAuto'] );

			if ( $s )
			{
				$targetTagAuto = array(
					'id' => $s['id'],
					'name' => $s['title']
				);
				
				$optionId = $s['id'];
				
				$showIf = 'tag';
			}
		}
		
		elseif ( isset( $_POST['targetTag'] ) && !empty( $_POST['targetTag'] ) )
		{
			$s = AdminGetTag( $_POST['targetTag'] );

			if ( $s )
			{
				$targetTag = array(
					'id' => $s['id'],
					'name' => $s['title']
				);
				
				$optionId = $s['id'];
			}
		}
		
		if ( isset( $_POST['targetTagHide'] ) && !empty( $_POST['targetTagHide'] ) )
		{
			if ( empty( $targetTag ) || ( $targetTag['id'] != $_POST['targetTagHide'] ) )
			{
				$s = AdminGetTag( $_POST['targetTagHide'] );

				if ( $s )
				{
					$targetTagHide = array(
						'id' => $s['id'],
						'name' => $s['title']
					);
				}
			}
		}

		//Create the settings array
		$s = array(
			'table_css' 					=> Sanitize( $_POST['table-css'], false ),
			'auto_insert_table' 			=> ( isset( $_POST['auto-insert-table'] ) ? Sanitize( $_POST['auto-insert-table'], false ) : '' ),
			'show_table_if' 				=> $showIf,
			'hide_table_if' 				=> $hideIf,
			'show_table_option' 			=> ( isset( $_POST['show-element-option'] ) ? Sanitize( $_POST['show-element-option'], false ) : '' ),
			'hide_table_option' 			=> ( isset( $_POST['hide-element-option'] ) ? Sanitize( $_POST['hide-element-option'], false ) : '' ),
			'display_custom_type' 			=> ( isset( $_POST['displayTargetCustom'] ) ? Sanitize( $_POST['displayTargetCustom'], false ) : 0 ),
			'hide_custom_type' 				=> ( isset( $_POST['displayTargetCustomHide'] ) ? Sanitize( $_POST['displayTargetCustomHide'], false ) : 0 ),
			
			'max_items' 					=> ( ( isset( $_POST['limit'] ) && ( is_numeric( $_POST['limit'] ) && ( $_POST['limit'] > 0 ) ) ) ? (int) $_POST['limit'] : HOMEPAGE_ITEMS ),

			'show_target_category_auto' 	=> $targetCategoryAuto,
			'show_target_tag_auto' 			=> $targetTagAuto,
			'show_target_blog_auto' 		=> $targetBlogAuto,
			'show_target_category' 			=> $targetCategory,
			'show_target_tag'				=> $targetTag,
			'hide_target_category'			=> $targetCategoryHide,
			'hide_target_tag'				=> $targetTagHide,
		);
		
		//If this value is empty, then the select options are empty, so we have to empty every other option aswell
		if ( empty( $optionId ) )
		{
			$s['auto_insert_table'] = $s['show_table_if'] = $s['hide_table_if'] = $showIf = $pos = $option = '';
		}
		
		$groups = ( ( isset( $_POST['membergroups'] ) && !empty( $_POST['membergroups'] ) && is_array( $_POST['membergroups'] ) ) ? $_POST['membergroups'] : array() );
				
		$disabled = ( isset( $_POST['disable'] ) ? 1 : 0 );
		
		
		$dbarr = array(
			"title" 			=> $_POST['title'],
			"groups_data" 		=> json_encode( $groups, JSON_UNESCAPED_UNICODE ),
			"disabled" 			=> $disabled,
			"form_data" 		=> json_encode( $s, JSON_UNESCAPED_UNICODE ),
			"form_pos" 			=> $pos,
			"show_if" 			=> $showIf,
			"show_if_option" 	=> $option,
			"show_if_id" 		=> $optionId
		);

		$this->db->update( 'forms' )->where( 'id', $id )->set( $dbarr );
	
		//Update the cell data
		if ( !empty( $_POST['cell'] ) )
		{
			foreach ( $_POST['cell'] as $celid => $cel )
			{
				//Check if we have this item
				$elem = $this->db->from( 
				null, 
				"SELECT id, data
				FROM `" . DB_PREFIX . "form_elements`
				WHERE (id = " . $celid . ") AND (id_form = " . $id . ")"
				)->single();
					
				if ( !$elem )
					continue;
				
				$data = ( !empty( $elem['data'] ) ? Json( $elem['data'] ) : array() );
				
				foreach( $cel as $c_ => $c__ )
				{
					$data['cell'][$c_] = htmlspecialchars_decode( $c__ );
				}
				
				$data = json_encode( $data, JSON_UNESCAPED_UNICODE );
		
				//Update the DB
				$this->db->update( 'form_elements' )->where( 'id', $elem['id'] )->set( "data", $data );
			}
			
		}
		
		//Update the style data
		if ( !empty( $_POST['style'] ) )
		{
			foreach ( $_POST['style'] as $stid => $st )
			{
				//Check if we have this item
				$tel = $this->db->from( 
				null, 
				"SELECT id
				FROM `" . DB_PREFIX . "form_elements`
				WHERE (id = " . $stid . ")"
				)->single();
				
				if ( !$tel )
					continue;
				
				$styleArr = array();
				
				foreach( $st as $st_ => $st__ )
				{
					$styleArr[$st_] = htmlspecialchars_decode( $st__ );
				}
				
				$data = json_encode( $styleArr, JSON_UNESCAPED_UNICODE );
		
				//Update the DB
				$this->db->update( 'form_table_elements' )->where( 'id', $stid )->set( "style", $data );
			}
		}
		
		//Update the cell data
		if ( !empty( $_POST['header'] ) )
		{
			foreach ( $_POST['header'] as $heid => $hed )
			{
				//Check if we have this item
				$elem = $this->db->from( 
				null, 
				"SELECT id, data
				FROM `" . DB_PREFIX . "form_elements`
				WHERE (id = " . $heid . ") AND (id_form = " . $id . ")"
				)->single();
				
				if ( !$elem )
					continue;
				
				$data = ( !empty( $elem['data'] ) ? Json( $elem['data'] ) : array() );
				
				foreach( $hed as $h_ => $h__ )
				{
					$data['header'][$h_] = htmlspecialchars_decode( $h__ );
				}
				
				$data = json_encode( $data, JSON_UNESCAPED_UNICODE );
		
				//Update the DB
				$this->db->update( 'form_elements' )->where( 'id', $elem['id'] )->set( "data", $data );
			}
		}

		//Update any elemets this table may have
		if ( !empty( $_POST['element'] ) )
		{
			foreach ( $_POST['element'] as $elid => $el )
			{
				//Check if we have this item
				$elem = $this->db->from( 
				null, 
				"SELECT id
				FROM `" . DB_PREFIX . "form_elements`
				WHERE (id = " . $elid . ") AND (id_form = " . $id . ")"
				)->single();
				
				if ( !$elem )
					continue;
				
				if ( empty( $el ) )
					continue;
				
				foreach( $el as $l => $el_ )
				{
					$elArr = array();
					
					if ( !empty( $el_ ) )
					{
						foreach( $el_ as $el__ => $el___ )
						{
							$elArr[$el__] = htmlspecialchars_decode( $el___ );
						}
					}

					$data = json_encode( $elArr, JSON_UNESCAPED_UNICODE );
				
					//Update the DB
					$this->db->update( 'form_table_elements' )->where( 'id', $l )->set( "data", $data );
				}
			}
		}
		
		$Admin->EmptyCaches();

		Redirect( $Admin->GetUrl( 'edit-table' . PS . 'id' . PS . $id ) );
	}
}