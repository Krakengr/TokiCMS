<?php defined('TOKICMS') or die('Hacking attempt...');

class AutomaticTranslator extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-languages' ) || !$Admin->Settings()::IsTrue( 'enable_auto_translate' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'automatic-translator' ) . ' | ' . $Admin->SiteName() );
		
		$autoTransSettings 					= Json( $Admin->Settings()::Get()['auto_translate_settings'] );
		$autoTransSettings['auto_langs'] 	= ( !empty( $autoTransSettings['auto_langs'] ) ? Json( $autoTransSettings['auto_langs'] ) : array() );
		$autoTransSettings['checked_langs'] = ( !empty( $autoTransSettings['checked_langs'] ) ? Json( $autoTransSettings['checked_langs'] ) : array() );
		
		$langs = ( $Admin->MultiLang() ? $Admin->OtherLangs() : null );

		$this->setVariable( 'autoTrans', $autoTransSettings );
		$this->setVariable( 'otherLangs', $langs );
		
		//Don't do anything if there is no POST
		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !verify_token( 'automatic-translator' ) )
			return;
		
		$dataLangs = $checkLangs = array();

		if ( !empty( $langs ) && !empty( $_POST['autolangs'] ) )
		{
			foreach( $_POST['autolangs'] as $la )
			{
				foreach( $langs as $lang )
				{
					if ( $lang['lang']['id'] == $la )
					{
						$dataLangs[$lang['lang']['code']] = array(
							'id' 	=> $lang['lang']['id'],
							'code' 	=> $lang['lang']['code'],
							'title' => $lang['lang']['title']
						);
						
						continue;
					}
				}
			}
		}
		
		if ( !empty( $langs ) && !empty( $_POST['checkLangs'] ) )
		{
			foreach( $_POST['checkLangs'] as $la => $_ )
			{
				array_push( $checkLangs, $la );
			}
		}
		
		//We are going to create a new array, so there is no need to get the settings from the DB
		$s = array(
			'auto_translate' 	=> ( isset( $_POST['auto_translate'] ) ? true : false ),
			'auto_langs' 		=> json_encode( $dataLangs, JSON_UNESCAPED_UNICODE ),
			'checked_langs' 	=> json_encode( $checkLangs, JSON_UNESCAPED_UNICODE ),
			'options'			=> array()
		);
		
		$settingsArray['auto_translate_settings'] = json_encode( $s );

		//Update the rest of the settings
		$Admin->UpdateSettings( $settingsArray );
		
		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}