<?php defined('TOKICMS') or die('Hacking attempt...');

class Preview extends Controller {
	
	private $themeData;
	
    public function process() 
	{
		global $Lang;
		
		$Lang = $this->lang;
		
		if ( !IsAllowedTo( 'view-site' ) )
		{
			//Don't include this file while on login or register
			if ( Router::WhereAmI() != 'login' )
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );

			$this->view();
			return;
		}

		$this->setVariable( 'Lang', $this->lang );
		$this->GetPost();
		Theme::Build();
		$this->view();
	}
	
	public function GetPost()
	{
		$Post = GetSinglePost( Router::GetVariable( 'key' ), SITE_ID, false, true, true);

		if ( !$Post )
		{
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
			return;
		}

		//Check the language if is correct
		if ( MULTILANG && ( $Post->Language()->id != $this->lang['lang']['id'] ) )
		{
			$url = SITE_URL;

			//Add the lang slug
			if ( !Settings::IsTrue( 'hide_default_lang_slug' ) || ( Settings::IsTrue( 'hide_default_lang_slug' ) && ( $Post->Language()->key != Settings::Lang()['code'] ) ) )
			{
				$url .= $Post->Language()->key . PS;
			}

			$url .= 'preview' . PS . $Post->PostId() . PS;

			@header("Location: " . $url );
			@exit;
		}

		if ( $Post->IsPublished() )
		{
			@header("Location: " . $Post->Url() );
			@exit;
		}
			
		if ( $Post->Status() == 'deleted' )
		{
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
			return;
		}
		
		//Don't forget the header data
		$arr = array(
			'postUrl'   		=> $Post->Url(),
			'parentId'  		=> $Post->ParentId(),
			'postId' 			=> $Post->PostId(),
			'postTitle'			=> $Post->Title(),
			'postDate'			=> $Post->Added()->raw,
			'postDateC'			=> $Post->Added()->c,
			'postDescription'	=> $Post->Description(),
			'isPage'			=> $Post->IsPage(),
			'postAuthor'		=> $Post->Author()->name,
			'postTranslations'  => $Post->Translations(),
			'languageKey'		=> $Post->Language()->key,
			'languageName'		=> $Post->Language()->name,
			'categoryUrl'		=> ( $Post->IsPage() ? null : $Post->Category()->url ),
			'categoryId'		=> ( $Post->IsPage() ? null : $Post->Category()->id ),
			'categoryName'		=> ( $Post->IsPage() ? null : $Post->Category()->name ),
			'hasCoverImage'		=> $Post->HasCoverImage(),
			'coverImage'		=> ( !empty( $Post->Cover()['default'] ) ? $Post->Cover()['default'] : null ),
		);

		Theme::SetVariable( 'data', $arr );
		$this->setVariable( 'Post', $Post );
		$this->setVariable( 'Listings', null );
		Theme::Build();
		
		unset( $Post, $arr );
	}
}