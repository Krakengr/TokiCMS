<?php defined('TOKICMS') or die('Hacking attempt...');

class Themes extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$themes = LoadThemes( 'normal', ( $Admin->IsDefaultSite() ? false : true ) );

		$this->setVariable( 'Themes', $themes );
		
		Theme::SetVariable( 'headerTitle', __( 'themes' ) . ' | ' . $Admin->SiteName() );
		
		//It's time to update the themes data since there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
		{
			$code = $Admin->LangCode();
			
			//Get the settings from the DB
			$themesData = $Admin->SiteThemes();
		
			$themesTempData = array();
			
			if ( !empty( $themes ) )
			{
				foreach( $themes as $id => $theme )
				{
					if ( isset( $themesData[$id] ) )
					{
						$themesTempData[$id] = $themesData[$id];
					}
					
					else
					{
						$data = $theme['data'];
						
						if ( isset( $data['settings'] ) && !empty( $data['settings'] ) )
						{
							$arr = array();
							
							foreach ( $data['settings'] as $_set => $set )
							{
								$arr[$_set] = ( isset( $set['default-value'] ) ? $set['default-value'] : null );
							}
							
							$themesTempData[$id]['options'][$code] = $arr;
						}
					}
				}
			}

			$settingsArray = array( 'themes_data' => json_encode( $themesTempData ) );

			$Admin->UpdateSettings( $settingsArray );
			
			$Admin->DeleteSettingsCacheSite( 'settings' );
			
			$Admin->DeleteSettingsCacheSite( 'themes_site' );

			return;
		}
		
		if ( !verify_token( 'themes' ) )
			return;

		/*
		//If this theme is new and we don't have its data, add it now
		if ( !isset( $themesData[$_POST['default_theme']] ) )
		{
			$theme = LoadTheme( $_POST['default_theme'] );
			
			if ( !empty( $theme ) )
			{
				$data = $theme['data'];
				
				if ( isset( $data['settings'] ) && !empty( $data['settings'] ) )
				{
					$arr = array();
					
					foreach ( $data['settings'] as $_set => $set )
					{
						$arr[$_set] = $set['default-value'];
					}
					
					$themesData[$_POST['default_theme']] = $arr;
					
					$settingsArray = array( 'themes_data' => json_encode( $themesData ) );
					
					$Admin->UpdateSettings( $settingsArray );
					
					$Admin->DeleteSettingsCacheSite( 'settings' );
				}
			}
		}*/
		
		//Don't continue if we already have this theme as default
		if ( $_POST['default_theme'] == $Admin->Settings()::Get()['theme'] )
		{
			//Redirect to the same page
			@header('Location: ' . $Admin->GetUrl( null, null, true ) );
			exit;
		}

		$settingsArray = array( 'theme' => Sanitize ( $_POST['default_theme'], false, false ) );

		//Update the rest of the settings
		$Admin->UpdateSettings( $settingsArray );
		
		//Delete Cache File
		$Admin->DeleteSettingsCacheSite( 'settings' );
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( null, null, true ) );
		exit;
	}
}