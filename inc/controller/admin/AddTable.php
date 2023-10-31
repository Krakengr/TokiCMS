<?php defined('TOKICMS') or die('Hacking attempt...');

class AddTable extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-forms' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		Theme::SetVariable( 'headerTitle', __( 'add-new-table' ) . ' | ' . $Admin->SiteName() );

		//Don't do anything if there is no POST
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !verify_token( 'add-table' )  )
			return;
		
		if ( empty( $_POST['name'] ) )
		{
			$Admin->SetAdminMessage( __( 'enter-a-valid-title' ) );
			return;
		}

		$tableType  = ( ( $_POST['select-template'] == 'price' ) ? 'price' : ( ( $_POST['select-template'] == 'product' ) ? 'product' : '' ) );
		
		$templateId = ( ( $tableType === '' ) ? 0 : (int) $_POST['select-template'] );
		
		$rows = 3;
		
		$dbarr = array(
			"id_site" 		=> $Admin->GetSite(),
			"id_member" 	=> $Admin->UserID(),
			"added_time" 	=> time(),
			"title" 		=> $_POST['name'],
			"descr" 		=> $_POST['description'],
			"form_type" 	=> 'table',
			"table_type" 	=> $tableType,
			"form_data" 	=> json_encode( array() )
		);
		
		$formId = $this->db->insert( 'forms' )->set( $dbarr, null, true );

		if ( $formId )
		{
			//If this is not blank, then we have a custom type and needs some work to do
			if ( $tableType !== '' ) 
			{
				for ( $i = 0; $i < $rows; $i++ )
				{
					$name = __( 'column' ) . ' ' . $i;
					
					$dbarr = array(
						"id_form" 		=> $formId,
						"elem_order" 	=> $i,
						"elem_id" 		=> '',
						"data" 			=> json_encode( array() ),
						"elem_name" 	=> $name,
					);

					$this->db->insert( 'form_elements' )->set( $dbarr );
				}
			}
			
			elseif ( is_numeric( $_POST['select-template'] ) && ( $templateId > 0 ) )
			{
				$t = $this->db->from( 
				null, 
				"SELECT data
				FROM `" . DB_PREFIX . "form_templates`
				WHERE (id = " . $templateId . ") AND (id_site = " . $Admin->GetSite() . ")"
				)->single();
				
				if ( $t )
				{
					$els = Json( $t['data'] );
					
					if ( !empty( $els ) && !empty( $els['elements'] ) )
					{
						$el_order = 0;
						
						foreach ( $els['elements'] as $el )
						{
							$dbarr = array(
								"id_form" 		=> $formId,
								"elem_order" 	=> $el_order,
								"elem_id" 		=> $el['id'],
								"data" 			=> json_encode( $el['data'], JSON_UNESCAPED_UNICODE )
							);
							
							$this->db->insert( 'form_elements' )->set( $dbarr );

							$el_order++;
						}
					}
				}
			}

			Redirect( $Admin->GetUrl( 'edit-table' . PS . 'id' . PS . $formId ) );
		}
		
		else
		{
			$Admin->SetAdminMessage( __( 'an-error-occurred' ) );
			return;
		}
	}
}