<?php defined('TOKICMS') or die('Hacking attempt...');

class AddWidget extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

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
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		// Verify if the token is correct
		if ( !verify_token( 'add_widget' ) )
			return;

		$query = array(
				'INSERT'	=>  "id_site, id_lang, title, type, data, added_time, theme_pos, theme",
				
				'VALUES' => ":id_site, :id_lang, :title, :type, :data, :added_time, :theme_pos, :theme",
				
				'INTO'		=> DB_PREFIX . "widgets",
				
				'PARAMS' => array(
					'NO_PREFIX' => true 
				),

				'BINDS' 	=> array(
							array(
								'PARAM' => ':id_site',
								'VAR' => $Admin->GetSite(),
								'FLAG' => 'INT'
							),
							array(
								'PARAM' => ':id_lang',
								'VAR' => $Admin->GetLang(),
								'FLAG' => 'INT'
							),
							array(
								'PARAM' => ':title',
								'VAR' => $_POST['widgetName'],
								'FLAG' => 'STR'
							),
							array(
								'PARAM' => ':type',
								'VAR' => $_POST['widgetType'],
								'FLAG' => 'STR'
							),
							array(
								'PARAM' => ':data',
								'VAR' => $_POST['widgetCode'],
								'FLAG' => 'STR'
							),
							array(
								'PARAM' => ':added_time',
								'VAR' => time(),
								'FLAG' => 'INT'
							),
							array(
								'PARAM' => ':theme_pos',
								'VAR' => ( ( isset( $_POST['widgetThemePos'] ) && !empty( $_POST['widgetThemePos'] ) ) ? $_POST['widgetThemePos'] : 'primary' ),
								'FLAG' => 'STR'
							),
							array(
								'PARAM' => ':theme',
								'VAR' => $Admin->Settings()::Get()['theme'],
								'FLAG' => 'STR'
							)
						)
		);
	
		$data = Query( $query, false, false, false, false, true );
		
		if ( $data )
		{
			//Redirect to the widget's edit page
			@header('Location: ' . $Admin->GetUrl( 'edit-widget' . PS . 'id' . PS . $data ) );
			exit;
		}
		else
		{
			@header('Location: ' . $Admin->GetUrl( 'widgets' ) );
			$Admin->SetAdminMessage( __( 'widget-add-error' ) );
			return;
		}
	}
}