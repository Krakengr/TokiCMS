<?php defined('TOKICMS') or die('Hacking attempt...');

require_once ( CLASSES_ROOT . 'Editor.php' );

class EditTheme extends Controller {
	
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
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-themes' ) ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$themeId = Router::GetVariable( 'subAction' );

		//We need an ID to work with
		if ( empty( $themeId ) )
		{
			Redirect( $Admin->GetUrl( 'themes' ) );
		}
		
		$dt = LoadThemes( 'normal', ( $Admin->IsDefaultSite() ? false : true ) );

		if ( !isset( $dt[$themeId] ) )
		{
			$Admin->SetErrorMessage( __( 'an-error-happened' ) );
			Redirect( $Admin->GetUrl( 'themes' ) );
		}

		$code = $Admin->LangCode();
		
		$themesData = Settings::Themes();
		
		$data = $dt[$themeId];
		
		if ( isset( $themesData[$themeId]['options'][$code] ) )
		{
			$data['db'] = $themesData[$themeId]['options'][$code];
		}
		
		Theme::SetVariable( 'headerTitle', __( 'edit-theme' ) . ': "' . $data['title'] . '" | ' . $Admin->SiteName() );
		
		$this->setVariable( 'ThemeDt', $data );
		
		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
		
		if ( !verify_token( 'edit-theme_' . $themeId ) )
			return;

		$arr = array();

		foreach ( $_POST as $id => $set )
		{
			if ( ( $id == '_token' ) || ( $id == 'save' ) )
			{
				continue;
			}
			
			$arr[$id] = $set;
		}

		//Set the new settings for this theme
		$themesData[$themeId]['options'][$code] = $arr;

		//Add back the auto-menu settings, if any
		//$themesData[$themeId]['auto-menu'][$code] = $autoMenu;
		
		$settingsArray = array( 'themes_data' => json_encode( $themesData ) );

		$Admin->UpdateSettings( $settingsArray );

		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		Redirect( $Admin->GetUrl( 'edit-theme' . PS . $themeId ) );
	}
}