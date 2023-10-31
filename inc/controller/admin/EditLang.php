<?php defined('TOKICMS') or die('Hacking attempt...');

class EditLang extends Controller {
	
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

		if ( !IsAllowedTo( 'admin-site' ) || !IsAllowedTo( 'manage-languages' ) || !$Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) )
		{
			Router::SetNotFound();
			return;
		}

		$id = (int) Router::GetVariable( 'key' );
		
		$Lang = GetLang( $id, $Admin->GetSite() );

		if ( !$Lang )
			Redirect( $Admin->GetUrl( 'langs' ) );
		
		$this->setVariable( 'Lang', $Lang );
		$this->setVariable( 'NotFound', Json( $Lang['not_found_data'] ) );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-lang' ) . ': "' . $Lang['title'] . '" | ' . $Admin->SiteName() );

		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		// Verify if the token is correct
		if ( !verify_token( 'editLang' . $id ) )
			Redirect( $Admin->GetUrl( 'langs' ) );

		//If we want to delete a language, do it now
		if ( isset( $_POST['delete'] ) && !empty( $_POST['delete']  ) )
		{
			//Get the site's default language's ID
			$q = $this->db->from( 
			null, 
			"SELECT id
			FROM `" . DB_PREFIX . "languages`
			WHERE (id_site = " . $Admin->GetSite() . ") AND (is_default = 1)"
			)->single();
			
			//We can't delete the default language
			//Normally this can not be happen, but better safe than sorry
			if ( $q['id'] == $id )
			{
				@header('Location: ' . $Admin->GetUrl( 'langs' ) );
				exit;
			}

			//Delete this language
			$this->db->delete( 'languages' )->where( "id", $id )->run();
			
			//Delete this language's config data as well
			$this->db->delete( 'languages_config' )->where( "id_lang", $id )->run();
		
			//We need to update any posts this language may has
			$this->db->update( POSTS )->where( 'id_lang', $id )->set( "id_lang", $q['id'] );
			
			//...the categories
			$this->db->update( "categories" )->where( 'id_lang', $id )->set( "id_lang", $q['id'] );
			
			//...the tags
			$this->db->update( "tags" )->where( 'id_lang', $id )->set( "id_lang", $q['id'] );
						
			//...the comments
			$this->db->update( "comments" )->where( 'id_lang', $id )->set( "id_lang", $q['id'] );
			
			//...the images
			$this->db->update( "images" )->where( 'id_lang', $id )->set( "id_lang", $q['id'] );

			//...the images' folders
			$this->db->update( "image_folders" )->where( 'id_lang', $id )->set( "id_lang", $q['id'] );

			//...the images' galleries
			$this->db->update( "image_galleries" )->where( 'id_lang', $id )->set( "id_lang", $q['id'] );

			//...the menus
			$this->db->update( "menus" )->where( 'id_lang', $id )->set( "id_lang", $q['id'] );

			//...the playlists
			$this->db->update( "playlists" )->where( 'id_lang', $id )->set( "id_lang", $q['id'] );
			
			//...the blogs
			$this->db->update( "blogs" )->where( 'id_lang', $id )->set( "id_lang", 0 );

			//Redirect to the langs
			@header('Location: ' . $Admin->GetUrl( 'langs' ) );
			
			exit;
		}

		//We can update the DB
		$dbarr = array(
			"title" 	=> $_POST['title'],
			"locale" 	=> $_POST['locale'],
			"direction" => $_POST['textDirection']
        );

		$q = $this->db->update( "languages" )->where( "id", $id )->set( $dbarr );
		
		$notFound = array();
		
		if ( $q )
		{
			if ( isset( $_POST['notfound'] ) && !empty( $_POST['notfound'] ) )
			{
				$notFound['not_found_title'] 	= Sanitize( $_POST['notfound']['not_found_title'], false );
				$notFound['not_found_message'] 	= Sanitize( $_POST['notfound']['not_found_message'], false );
			}
			
			$shortname = ( isset( $_POST['ext'][$_POST['comment_sys']] ) ? SafeFormField( $_POST['ext'][$_POST['comment_sys']], true ) : '' );

			//Update the language settings too
			$dbarr = array(
				"date_format" 			=> $_POST['date_format'],
				"time_format" 			=> $_POST['time_format'],
				"site_name" 			=> $_POST['site_name'],
				"site_description" 		=> $_POST['site_description'],
				"ext_comm_system" 		=> SafeFormField( $_POST['comment_sys'], true ),
				"ext_comm_shortname" 	=> $shortname,
				"site_slogan" 			=> $_POST['site_slogan'],
				"not_found_data"		=> json_encode( $notFound, JSON_UNESCAPED_UNICODE )
			);

			$this->db->update( "languages_config" )->where( "id_lang", $id )->set( $dbarr );
		}
		
		$Admin->DeleteSettingsCacheSite( 'settings' );

		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( 'edit-lang' . PS . 'id' . PS . $id ) );
		exit;
	}
}