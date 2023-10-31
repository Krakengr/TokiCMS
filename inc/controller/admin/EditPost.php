<?php defined('TOKICMS') or die('Hacking attempt...');

class EditPost extends Controller {
	
	private $id;
	private $post;
	private $defaultLangId;
	private $postLangId;
	private $postBlogId;
	private $postSiteId;
	private $oldSiteId;
	private $postUri;
	private $postRawData;
	private $postSiteMoveUrl;
	private $postBuildedContent;
	private $drafts;
	private $postDate;
	private $postType;
	private $postTitle;
	private $oldSlug;
	private $newSlug;
	private $postSiteUrl;
	private $postStatus;
	private $autoPublishToSocial;
	private $parentId;
	private $langId;
	private $langCode;
	private $status;
	private $xtraData;
	private $userId;
	private $postUrl;
	private $blocksData;
	private $postContent;
	private $postPreviewUri;
	private $postKeepDate;
	private $postCloneId;
	private $postDraftUri;
	private $postFormat = 0;
	private $subCategoryId = 0;
	private $categoryId = 0;
	private $oldCategoryId = 0;
	private $oldSubCategoryId = 0;
	private $moveToSite = false;
	private $pingEngines = false;
	private $publishPost = false;
	private $minorEdit = false;
	private $preload = false;
	private $disableComments = false;
	private $copyExternalImages = false;
	private $postIsPublished = false;
	private $tags = [];
	private $customTags = [];
	private $customTypes = [];
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
	
		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	#####################################################
	#
	# Run function
	#
	#####################################################
	private function Run() 
	{
		global $Admin, $Post;

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-own-posts' ) && !IsAllowedTo( 'manage-posts' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$this->id = (int) Router::GetVariable( 'key' );
		
		/*
		//Query: post
		$tmp = $this->db->from( null, PostDefaultQuery( "(p.id_post = " . $this->id . ")" ) )->single();
		
		$s = GetSettingsData( $tmp['id_site'] );
		
		if ( !$tmp || !$s )
		{
			Redirect( $Admin->GetUrl( 'posts' ) );
		}
		
		$this->postRawData 	= array_merge( $tmp, $s );
		$this->post 		= BuildFullPostVars( $this->postRawData );
		$Post 				= new Post( $this->post );*/
		
		try
		{
			$post 					= new GetPost;
			$post->id 				= $this->id;
			$post->siteId 			= $Admin->GetSite();
			$post->cache 			= false;
			$post->build 			= false;
			$post->anyStatus 		= true;
			$post->getTopPosts 		= false;
			$post->getRelatedPosts 	= false;
			$post->getNextPrevPosts = false;
			
			$this->post				= $post->GetPost();
			$this->postRawData		= $post->tmp;
		}
			
		catch( Exception $e )
		{
			Redirect( $Admin->GetUrl( 'posts' ) );
		}

		$Post = new Post( $this->post );
		
		Theme::SetVariable( 'headerTitle', ( $Post->IsPage() ? __( 'edit-page' ) : __( 'edit-post' ) ) . ': "' . $Post->Title() . '" | ' . $Admin->SiteName() );

		//Set the current user ID for the current site
		$this->userId = $Admin->UserID();
		
		$drafts = $Admin->Settings()::Get()['drafts_data'];

		$this->drafts = ( !empty( $drafts ) ? Json( $drafts ) : null );

		//Check if the user can edit posts other than their own
		if ( !IsAllowedTo( 'manage-posts' ) && IsAllowedTo( 'manage-own-posts' ) )
		{
			if ( $Post->User()->id != $this->userId )
			{
				Redirect( $Admin->GetUrl( 'posts' ) );
			}
		}

		$this->setVariable( 'Post', $Post );
		$this->setVariable( 'Drafts', $this->drafts );

		//Get the default language
		$this->defaultLangId 	= $Admin->DefaultLang()['id'];

		//Grab some post info
		$this->postBlogId 		= ( !empty( $Post->Blog()->id ) ? $Post->Blog()->id : 0 );
		$this->postSiteId 		= $Post->Site()->id;
		$this->postLangId 		= $Post->Language()->id;
		$this->postStatus 		= $Post->Status();
		$this->postSiteUrl 		= $Post->Site()->url;
		$this->xtraData 		= $Post->ExtraData();
		$this->postUrl 			= $Post->Url();
		$this->langCode 		= ( !empty( $Post->Language()->key ) ? $Post->Language()->key : $Admin->LangKey() );
		$this->oldSubCategoryId = ( !empty( $Post->SubCategory()->id ) ? $Post->SubCategory()->id : 0 );
		$this->oldCategoryId 	= ( !empty( $Post->Category()->id ) ? $Post->Category()->id : 0 );
		$this->postCloneId 		= $this->postRawData['clone_id'];
		$this->postKeepDate 	= $this->postRawData['keep_date'];

		//Create the URI for redirection, we will need it later
		$this->postUri 			= $this->PostEditUri();
		
		$this->postDraftUri 	= $this->PostEditUri( true );
		
		//Create the preview URI for this post
		if( $this->postStatus !== 'published' )
		{
			$this->postPreviewUri = AdminPostPreviewUri( $this->id, $Post->Language()->key );
		}
		else
		{
			$this->postPreviewUri = null;
		}

		$this->setVariable( 'PreviewUri', 		$this->postPreviewUri );
		$this->setVariable( 'PostEditDraftUri', $this->postDraftUri );
		
		//Start editing the post
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
		{
			//Get some data from the POST
			$sub = ( isset ( $_POST['subcat'] ) ? (int) $_POST['subcat'] : null );
			$cat = ( isset ( $_POST['category'] ) ? (int) $_POST['category'] : null );

			//$this->postFormat 	= ( isset( $_POST['postFormat'] ) ? (int) $_POST['postFormat'] : 0 );
			
			$this->postType 		= ( isset( $_POST['postType'] ) ? $_POST['postType'] : 'post' );
			
			$this->oldSlug 			= Sanitize ( $_POST['post_old_slug'], false );

			$this->minorEdit 		= ( isset( $_POST['minor_edit'] ) ? true : false );
			
			$this->disableComments 	= ( isset( $_POST['postExtra']['disable_comments'] ) ? true : false );

			$this->categoryId 		= ( ( is_numeric( $cat ) && ( $cat > 0 ) ) ? $cat : null );
			
			$this->subCategoryId 	= ( ( is_numeric( $sub ) && ( $sub > 0 ) ) ? $sub : 0 );
			
			$this->parentId 		= ( ( isset ( $_POST['parent'] ) && !empty( $_POST['parent'] ) ) ? (int) $_POST['parent'] : 0 );
			
			$this->customTypes 		= ( ( isset ( $_POST['customType'] ) && !empty( $_POST['customType'] ) ) ? $_POST['customType'] : array() );

			$this->langId 			= ( isset( $_POST['language'] ) ? $_POST['language'] : ( isset( $_POST['post_lang_id'] ) ? $_POST['post_lang_id'] : $Admin->GetLang() ) );
			
			$this->copyExternalImages 	= ( isset( $_POST['copyRemoteImages'] ) ? true : false );
			
			//Set the post's status
			$this->status 			= ( ( isset( $_POST['publish'] ) && ( $this->postStatus != 'published' ) ) ? 'published' : ( isset( $_POST['save-draft'] ) ? 'draft' : $_POST['status'] ) );

			//Set the post's date
			$postDate = ( isset( $_POST['date'] ) && !empty( $_POST['date'] ) ? $_POST['date'] . ' ' . 
					( isset( $_POST['hoursPublished'] ) && !empty( $_POST['hoursPublished'] ) ? $_POST['hoursPublished'] : '00' ) . ':' .
					( isset( $_POST['minutesPublished'] ) && !empty( $_POST['minutesPublished'] ) ? $_POST['minutesPublished'] : '00' ) . ':00'
					: null 
			);
			
			$this->postDate  = ( $postDate ? strtotime( $postDate ) : time() );
			
			$this->postTitle = CleanContent( trim( $_POST['title'] ) );
			
			if ( $this->postDate > time() )
			{
				$this->status = 'scheduled';
			}

			//Continue with the editing
			$this->Edit();
		}
		
		else
		{
			//Maybe we need a redirection?
			//Put this in here, to avoid any redirection when updating the post
			if ( 
				( $this->postLangId != $Admin->GetLang() )
				||
				( $this->postSiteId != $Admin->GetSite() )
				||
				( ( $this->postBlogId > 0 ) && ( $this->postBlogId != $Admin->GetBlog() ) )
			)
			{
				Redirect( $this->postUri );
			}
		}
	}

	#####################################################
	#
	# Post Edit function
	#
	#####################################################
	private function Edit()
	{
		global $Admin;

		//Check the content
		$this->PostContent();

		//Check the category, this is just a safe check
		$this->Category();
			
		//Check if we want to move this post
		$this->Move();
		
		//Set the post's tags and custom tags, if any
		$this->Tags();

		//Set the post's extra data
		$this->XtraData();
		
		//Set the post's attributes
		$this->Attributes();

		//Remove deals we no longer need
		$this->DealsRemove();
		
		//Check if we have deals
		$this->DealsList();
		
		//Check if we have a short Url
		$this->EditShortUrl();

		//Create the slug
		//TODO: Improve this and do more tests
		$temp 			= ( !empty( $_POST['slug'] ) ? $_POST['slug'] : $this->postTitle );
		
		$this->newSlug 	= SetShortSef( POSTS, 'id_post', 'sef', CreateSlug( $temp, true ), $this->id );
		
		//Update the time only on published posts
		$editedTime 	= ( $this->postIsPublished ? time() : 0 );
		
		//Set the time only on published/cloned posts, otherwise keep it updated
		$postDate 		= ( ( $this->postIsPublished || $this->postKeepDate ) ? $this->postDate : time() );
			
		if ( $this->copyExternalImages )
		{
			$this->postContent = GetExternalImagesFromContent( $this->postContent );
		}
		
		//Build the PUT array
		$dbarr = array(
			"id_site" 			=> $this->postSiteId,
			"id_lang" 			=> $this->langId,
			"id_blog" 			=> $this->postBlogId,
			"title" 			=> $this->postTitle,
			"post" 				=> $this->postContent,
			"content" 			=> $this->postBuildedContent,
			"blocks"			=> $this->blocksData,
			"description" 		=> CleanContent( trim( $_POST['description'] ) ),
			"post_status" 		=> $this->status,
			"added_time" 		=> $postDate,
			"edited_time" 		=> $editedTime,
			"sef" 				=> $this->newSlug,
			"id_category" 		=> $this->categoryId,
			"id_sub_category" 	=> $this->subCategoryId,
			"post_type" 		=> $this->postType,
			"id_parent" 		=> $this->parentId,
			"cover_img" 		=> json_encode( array() ),
			"id_member" 		=> $this->userId,
			"id_member_updated" => $this->userId,
			"disable_comments" 	=> ( $this->disableComments ? 1 : 0 ),
			"id_page_parent" 	=> ( isset( $_POST['pageParent'] ) ? (int) $_POST['pageParent'] : 0 ),
			"page_order" 		=> ( isset( $_POST['page_order'] ) ? (int) $_POST['page_order'] : 0 )
        );

		$q = $this->db->update( POSTS )->where( 'id_post', $this->id )->set( $dbarr );

		if ( !$q )
		{
			$Admin->SetAdminMessage( __( 'post-edit-error' ) );
			return;
		}

		// Add/Remove the tags, if the post is not a page of course
		if ( $this->postType != 'page' )
		{
			AddTags( $this->tags, $this->id, $this->langId, $this->postSiteId, 0 );
			
			if ( !empty( $this->customTags ) )
			{
				foreach( $this->customTags as $cus => $tags )
				{
					AddTags( $tags, $this->id, $this->langId, $this->postSiteId, $cus );
				}
			}
		}

		//Build the args array, its values are needed at some functions
		$args = array(
			'postId'			=> $this->id,
			'siteId' 			=> $this->postSiteId,
			'langId' 			=> $this->langId,
			'blogId' 			=> $this->postBlogId,
			'postDate' 			=> $postDate,
			'userId' 			=> $this->userId,
			'coverImageId' 		=> ( ( isset( $_POST['coverImageID'] ) && !empty( $_POST['coverImageID'] ) ) ? $_POST['coverImageID'] : null ),
			'postFormat' 		=> $this->postFormat,
			'customTypes' 		=> $this->customTypes,
			'postType' 			=> $this->postType,
			'langCode' 			=> $this->langCode,
			'copyRemoteImage' 	=> ( isset( $_POST['copyRemoteImage'] ) ? true : false ),
			'externalImage' 	=> ( ( isset( $_POST['externalImage'] ) && !empty( $_POST['externalImage'] ) ) ? $_POST['externalImage'] : null ),
			'variations' 		=> ( ( isset( $_POST['variations'] ) && !empty( $_POST['variations'] ) ) ? $_POST['variations'] : null ),
			'variationParent' 	=> ( ( isset( $_POST['variationParent'] ) && !empty( $_POST['variationParent'] ) ) ? $_POST['variationParent'] : null ),
			'title' 			=> $this->postTitle,
			'description' 		=> $_POST['description']
		);

		//Check for any variations
		Variations( $args );
		
		//Link any custom type(s)
		CheckCustomTypes( $this->id, $this->customTypes );
		
		CoverImage( $args );//TODO: Remove/Edit this function
		
		UpdateRemotePost( $args );
		
		//Clean/Rebuild the caches
		Preload( $this->id, $this->postSiteId );

		//Set the post's cover image, if any
		PostImage( $args );

		//Add the cover img array here
		//Why that? To have the cover image details cached and avoid multiple DB requests
		$coverImg = PostCoverImage( $args );
		
		if ( !empty( $coverImg ) )
		{
			$this->db->update( POSTS )->where( 'id_post', $this->id )->set( "cover_img", json_encode( $coverImg, JSON_UNESCAPED_UNICODE ) );
		}
		
		//Check the images for local copies etc...
		$this->CheckPostImages();
		
		//Check for drafts
		$this->Drafts();
		
		//Update Post Stats
		UpdatePostStats( $args );
		
		//Set the post's Schema data, if any
		$this->Schema();
		
		//Update other post's languages if there is any change
		$this->UpdateLangs();

		//This is a new post? If so, we have to delete cache files and ping search engines
		if ( $this->publishPost )
		{
			//We ping inform search engines, but only once and only if we want to notify the search engines
			if ( $this->pingEngines )
			{
				$submit = AdminPingSearchEngines( $this->postSiteUrl . 'sitemap_index.xml' );
				
				if ( $submit )
				{
					$Admin->SetErrorMessage( sprintf( __( 'sitemap-submit-error' ), $submit ) );
				}
			}
			
			//$this->preload = true;

			$this->AutoPublishToSocial();
			
			$message = sprintf( __( 'post-published-view-post' ) , $this->postUrl );
			
			$Admin->SetErrorMessage( $message, 'info' );
		}
		
		else
		{
			if ( !$this->moveToSite )
			{
				if ( $this->postIsPublished )
				{
					$message = sprintf( __( 'post-updated-view-post' ) , $this->postUrl );
				
					$Admin->SetErrorMessage( $message, 'info' );
					
					//Add the short link for this post
					$this->ShortLink();
				}
				else
				{
					$message = sprintf( __( 'post-draft-updated-view-post' ), $this->postPreviewUri );//$this->postUrl
				
					$Admin->SetErrorMessage( $message, 'info' );
				}
			}
			else
			{
				$Admin->SetErrorMessage( __( 'the-post-has-been-successfully-moved' ), 'info' );
			}
		}
		
		//Don't forget the sitemap
		if ( $this->postIsPublished && $Admin->Settings()::IsTrue( 'enable_sitemap' ) )
		{
			if ( $Admin->IsDefaultSite() )
			{
				BuildSitemap();
			}
			else
			{
				$Admin->PingChildSite( 'build-sitemap', null, null, $this->postSiteId );
			}
		}

		//Add the redirection but only if we don't move this post to another site
		if ( 
			!$this->moveToSite && $this->postIsPublished && $Admin->Settings()::IsTrue( 'enable_redirect' ) 
			&& ( $this->oldSlug !== $this->newSlug )
		)
		{
			$redirSet = Json( $Admin->Settings()::Get()['redirection_data'] );
			
			$this->Redirs( $redirSet );
		}
	
		//If we have moved the post, we should do some more work and update/delete some data
		if ( $this->moveToSite )
		{
			//Move the tags now
			$this->MoveTags();
			
			//Move/copy the images
			$this->MoveImages();
			
			//Move Child Posts
			$this->MoveChildPosts();
			
			//Add a redirection if needed
			$this->AddMoveRedir();
			
			//Delete any home data for this site
			$Admin->EmptyCaches( $this->postSiteId );
			
			//Delete the post's cache file
			$cacheFile = PostCacheFile( $this->newSlug, null, $this->langCode );
			
			if ( file_exists( $cacheFile ) )
				@unlink( $cacheFile );
		}
		
		//Delete the post's cache file
		$Admin->DeleteFileCache( $this->id, $this->postSiteId, $this->newSlug, $this->langCode );

		$Admin->EmptyCaches();
		
		//We are almost done. Let's check if this post has been moved
		if ( $this->moveToSite )
		{
			//This post is gone now. Forget about it and move on...
			Redirect( $Admin->GetUrl( ( ( $this->postType == 'post' ) ? 'posts' : 'pages' ) ) );
		}
		
		//Finally...
		Redirect( $this->postUri );
	}

	#####################################################
	#
	# Update/Edit Other Posts language function
	#
	#####################################################
	private function UpdateLangs()
	{
		//There is no change
		if ( $this->langId == $this->postLangId )
			return;
		
		//If the user changed the post's language, change any post has the current language
		$this->db->update( POSTS )->where( 'id_lang', $this->langId )->set( "id_lang", $this->postLangId );
	}

	#####################################################
	#
	# Update/Edit Post Short Link function
	#
	#####################################################
	private function EditShortUrl()
	{
		if ( !isset( $_POST['shortUrlSlug'] ) )
			return;
		
		global $Admin;
		
		//Create the short link for this post
		$settings = $Admin->Settings()::LinkSettings();
		
		//Don't bother to update the link if there is no need
		if ( !empty( $settings ) && !empty( $settings['short-link-settings'] ) && $settings['short-link-settings']['enable'] )
		{
			$x = $this->db->from( null, "
			SELECT id, short_link
			FROM `" . DB_PREFIX . "links`
			WHERE (id_post = " . $this->id . ") AND (id_site = " . $this->postSiteId . ")"
			)->single();
				
			if ( $x && ( $x['short_link'] != $_POST['shortUrlSlug'] ) )
			{
				$this->db->update( "links" )->where( 'id', $x['id'] )->set( "short_link", $_POST['shortUrlSlug'] );
			}
		}
	}
	
	#####################################################
	#
	# Post Short Link function
	#
	#####################################################
	private function ShortLink()
	{
		global $Admin;
		
		//Create the short link for this post
		$settings = $Admin->Settings()::LinkSettings();
		
		if ( !empty( $settings ) && !empty( $settings['short-link-settings'] ) && $settings['short-link-settings']['enable'] && ( ( ( $this->postType == 'page' ) && !empty( $settings['short-link-settings']['page_shortlinks'] ) ) || ( ( $this->postType == 'post' ) && !empty( $settings['short-link-settings']['post_shortlinks'] ) ) ) )
		{
			if ( ( $this->postType == 'page' ) || ( ( $this->postType == 'post' ) && ( empty( $settings['short-link-settings']['category'] ) || ( !empty( $settings['short-link-settings']['category'] ) && ( $this->categoryId == $settings['short-link-settings']['category'] ) ) ) ) )
			{
				$x = $this->db->from( null, "
				SELECT id 
				FROM `" . DB_PREFIX . "links`
				WHERE (id_post = " . $this->id . ") AND (id_site = " . $this->postSiteId . ")"
				)->single();
				
				if ( !$x )
				{
					$p = GetSinglePost( $this->id , null, false );

					if ( $p )
					{
						$dbarr = array(
							"id_post" 		=> $this->id ,
							"id_site" 		=> $this->postSiteId,
							"id_member" 	=> $this->userId,
							"added_time" 	=> time(),
							"title" 		=> $this->postTitle,
							"descr" 		=> 'Short link for "' . $this->postTitle . '"',
							"short_link" 	=> generate_short_key( $this->postSiteId ),
							'url'			=> $p['postUrl']
						);

						$this->db->insert( "links" )->set( $dbarr );
					}
				}
			}
		}
	}
	
	#####################################################
	#
	# Post Redirections function
	#
	#####################################################
	private function Redirs( $arr )
	{
		if ( !empty( $arr ) && isset( $arr['monitor_permalink_changes'] ) && $arr['monitor_permalink_changes'] )
		{
			$redir = $this->db->from( 
			null, 
			"SELECT id, target
			FROM `" . DB_PREFIX . "redirs`
			WHERE (id_site = " . $this->postSiteId . ") AND (slug = :slug)",
			array( $this->oldSlug => ':slug' )
			)->single();
				
			if ( !$redir )
			{
				$dbarr = array(
					"id_site" 		=> $this->postSiteId,
					"title" 		=> sprintf( __( 'auto-redirection-for' ), $this->postTitle ),
					"slug" 			=> $this->oldSlug,
					"target" 		=> $this->newSlug,
					"added_time" 	=> time()
				);

				$this->db->insert( 'redirs' )->set( $dbarr );
			}
				
			else
			{
				if ( $redir['target'] !== $this->newSlug )
				{
					$this->db->update( "redirs" )->where( 'id', $redir['id'] )->set( "target", $this->newSlug );
				}
			}
		}
	}
	
	#####################################################
	#
	# Post Redirection Uri function
	#
	#####################################################
	private function PostEditUri( $draft = null )
	{
		$uri = ADMIN_URI . 'edit-post' . PS . 'id' . PS . $this->id . PS;
		
		$hasSite = false;
		$hasLang = false;
		$hasBlog = false;
		
		if ( $this->postSiteId && ( $this->postSiteId != SITE_ID ) )
		{
			$uri .= '?site=' . $this->postSiteId;
			$hasSite = true;
		}
		
		if ( $this->postBlogId && ( $this->postBlogId > 0 ) )
		{
			$uri .= ( $hasSite ? ';' : '?' ) . 'blog=' . $this->postBlogId;
			$hasBlog = true;
		}
		
		if ( $this->postLangId != $this->defaultLangId )
		{
			$uri .= ( ( $hasSite || $hasBlog ) ? ';' : '?' ) . 'lang=' . $this->postLangId;
			$hasLang = true;
		}
		
		if ( $draft )
		{
			$uri .= ( ( $hasSite || $hasBlog || $hasLang ) ? ';' : '?' ) . 'draft={draftid}';
		}

		return $uri;
	}

	#####################################################
	#
	# Category checking function
	#
	#####################################################
	private function Category()
	{
		//If we have set a subCategory, we need its parent too
		if ( !empty( $this->subCategoryId ) && empty( $this->categoryId ) )
		{
			$cat = $this->db->from( 
			null, 
			"SELECT id_parent
			FROM `" . DB_PREFIX . "categories`
			WHERE (id = " . $this->subCategoryId . ")"
			)->single();
			
			if ( !$cat )
			{
				$this->categoryId = $this->SiteDefaultCategory();
				$this->subCategoryId = 0;
			}
			
			else
			{
				$this->categoryId = $cat['id_parent'];
			}
		}
		
		elseif ( empty( $this->subCategoryId ) && empty( $this->categoryId ) )
		{
			$this->categoryId = $this->SiteDefaultCategory();
		}
	}
	
	#####################################################
	#
	# Post Move function
	#
	#####################################################
	
	#TODO: There is a bug here that changes the target site's post to the previous language.
	#	   or the bug could be in "MoveChildPosts()" function below
	private function Move()
	{
		global $Admin;
		
		if ( empty( $_POST['movePostType'] ) )
		{
			return;
		}
		
		//Do this here to avoid repeating "if/else" later
		$this->postType = 'post';
		
		if ( $_POST['movePostType'] == 'site' )
		{
			//Get the category id
			if ( $_POST['move-post-site'] == '-1' )
			{
				$catId 				= null;
				$this->postType 	= 'page';
				$this->parentId 	= 0;
				$this->categoryId 	= 0;
			}
			
			else
			{
				$catId 				= ( !empty( $_POST['move-post-site'] ) ? (int) $_POST['move-post-site'] : 0 );
			}

			$siteId 	= ( !empty( $_POST['movePostId'] ) ? (int) $_POST['movePostId'] : 0 );
			
			$orSiteId 	= $this->postSiteId;
			
			if ( empty( $siteId ) && empty( $catId ) )
			{
				$Admin->SetErrorMessage( __( 'the-post-couldn-t-be-moved' ) );
				return;
			}
			
			if ( !empty( $catId ) )
			{
				$tmp = $this->db->from( null,
				"SELECT id_site, id_lang, id_blog
				FROM `" . DB_PREFIX . "categories`
				WHERE (id = " . $catId . ")"
				)->single();
				
				if ( $tmp )
				{
					$this->postSiteId 	= $tmp['id_site'];
					$this->postBlogId 	= $tmp['id_blog'];
					$this->langId 		= $tmp['id_lang'];
					$this->categoryId 	= $catId;
				}
			}
			
			//"PlanB"
			else
			{
				$this->postSiteId 	= $siteId;
				
				//Try to get the same language at first
				$lang 				= GetSiteLang( $this->langId, $this->postSiteId );
				
				//If the above fails, get the default language of the target site
				$this->langId 		= ( !empty( $lang ) ? $lang : GetSiteDefaultLanguage( $this->postSiteId ) );
				
				//Set the blog as 0
				$this->postBlogId 	= 0;
				
				if ( empty( $catId ) && $this->postType != 'page' )
				{
					//Get the default category from this site
					$childSiteCat 		= GetSiteDefaultCategory( $this->postSiteId, $this->langId );

					//At least we tried
					$this->categoryId 	= ( $childSiteCat ? $childSiteCat['id'] : 0 );
				}
			}
			
			//Set the subCategory as 0
			$this->subCategoryId = 0;
			
			$this->moveToSite = true;
			
			//Set the user id from the target site
			$targetUserId = GetMemberRel( $this->userId, $orSiteId, $this->postSiteId );

			if ( $targetUserId )
			{
				$this->userId = $targetUserId;
			}

			$this->postSiteMoveUrl = $Admin->GetSiteUrl( $this->postSiteId );

			//Move the comments
			$this->MovePostComments();
		}
		
		//Well, this is easier...
		elseif ( $_POST['movePostType'] == 'blog' )
		{
			$blogId = ( !empty( $_POST['movePostId'] ) ? (int) $_POST['movePostId'] : 0 );
			
			//Get the category id
			if ( $_POST['move-post-site'] == '-1' )
			{
				$catId 				= null;
				$this->postType 	= 'page';
				$this->parentId 	= 0;
				$this->categoryId 	= 0;
			}
			
			else
			{
				$catId 	= ( !empty( $_POST['move-post-blog'] ) ? (int) $_POST['move-post-blog'] : 0 );
			}
			
			if ( empty( $blogId ) && empty( $catId ) )
			{
				$Admin->SetErrorMessage( __( 'the-post-couldn-t-be-moved' ) );
				return;
			}
			
			if ( !empty( $catId ) )
			{
				$tmp = $this->db->from( null,
				"SELECT id_site, id_lang, id_blog
				FROM `" . DB_PREFIX . "categories`
				WHERE (id = " . $catId . ")"
				)->single();
				
				if ( $tmp )
				{
					$this->postBlogId 	= $tmp['id_blog'];
					$this->categoryId 	= $catId;
					$this->postSiteId 	= $tmp['id_site'];
				}
			}
			
			//"PlanB"
			else
			{
				$this->postBlogId = $blogId;
				
				//We need the site id
				$tmp = $this->db->from( null,
				"SELECT id_site
				FROM `" . DB_PREFIX . "blogs`
				WHERE (id_blog = " . $this->postBlogId . ")"
				)->single();
				
				if ( $tmp )
				{
					$this->postSiteId 	= $tmp['id_site'];
				}
				
				else
				{
					$Admin->SetErrorMessage( __( 'the-post-couldn-t-be-moved' ) );
					return;
				}
				
				if ( empty( $catId ) && ( $this->postType != 'page' ) )
				{
					$cat = GetBlogDefaultCategory( $this->postBlogId, $this->langId );
					
					if ( $cat )
					{
						$this->categoryId = $cat['id'];
					}
				}
			}
		}
	}
	
	#####################################################
	#
	# Child Post(s) Move function
	#
	#####################################################
	private function MoveChildPosts()
	{
		global $Admin;
		
		//If we want to keep childs, set their parent as "0"
		if ( !isset( $_POST['movePostChildsSelection'] ) )
		{
			$this->db->update( POSTS )->where( 'id_parent', $this->id )->set( 'id_parent', '0' );
			return;
		}
		
		$data = $this->db->from( 
		null, 
		"SELECT p.id_post, p.title, la.code AS ls
		FROM `" . DB_PREFIX . POSTS . "` AS p
		INNER JOIN `" . DB_PREFIX . "languages` AS la ON la.id = p.id_lang
		WHERE (p.id_parent = " . $this->id . ")"
		)->all();
		
		if ( !$data )
			return;
		
		$error = $tempErrors = null;
		
		foreach( $data as $p )
		{
			$targLang = $this->db->from( 
			null, 
			"SELECT id
			FROM `" . DB_PREFIX . "languages`
			WHERE (code = '" . $p['ls'] . "') AND (id_site = " . $this->postSiteId . ") AND (status = 'active')"
			)->single();
			
			$targetLang = ( $targLang ? $targLang['id'] : $this->langId );
			
			$targetCat = GetSiteDefaultCategory( $this->postSiteId, $targetLang );
			
			//Let's update the post
			$dbarr = array(
				"id_site"	 		=> $this->postSiteId,
				"id_lang" 			=> $targetLang,
				"id_blog" 			=> 0,
				"id_category" 		=> $targetCat,
				"id_custom_type" 	=> 0,
				"id_sub_category" 	=> 0,
				"id_member_updated" => 0
			);

			$q = $this->db->update( POSTS )->where( 'id_post', $p['id_post'] )->set( $dbarr );

			if ( $q )
			{
				$this->MoveImages( $p['id_post'] );
				$this->MoveTags( $p['id_post'] );
				$this->AddMoveRedir( $p['id_post'] );
			}
			else
			{
				$tempErrors .= $p['title'] . ' (ID: ' . $p['id_post'] . ')' . PHP_EOL;
			}
		}
		
		if ( $tempErrors ) 
		{
			$error = __( 'the-following-posts-couldn-t-be-moved' ) . PHP_EOL;
			$error .= $tempErrors;
		}
		else
		{
			$error = __( 'child-posts-have-been-successfully-moved' );
		}

		$Admin->SetErrorMessage( $error, 'info' );
	}
	
	#####################################################
	#
	# Comments Move function
	#
	#####################################################
	private function MovePostComments( $postId = null )
	{
		$postId = ( $postId ? $postId : $this->id );
		
		$comms = $this->db->from( 
		null, 
		"SELECT id, user_id
		FROM `" . DB_PREFIX . "comments`
		WHERE (id_post = " . $postId . ")"
		)->all();
		
		if ( !$comms )
			return;
		
		foreach( $comms as $comm )
		{
			$dbarr = array(
				"id_site" => $this->postSiteId,
				"id_lang" => $this->langId,
				"id_blog" => $this->postBlogId
			);

			$q = $this->db->update( 'comments' )->where( 'id', $comm['id'] )->set( $dbarr );
			
			//Update the members
			if ( $q && ( $comm['user_id'] > 0 ) )
			{
				$member = $this->db->from( 
				null, 
				"SELECT id_cloned_member AS m
				FROM `" . DB_PREFIX . "members_relationships`
				WHERE (id_member = " . $comm['user_id'] . ") AND (id_site = " . $this->postSiteId . ")"
				)->single();
					
				if ( $member )
				{
					$this->db->update( 'comments' )->where( 'id', $comm['id'] )->set( "user_id", $member['m'] );
				}
			}
		}
	}

	#####################################################
	#
	# Tags Move function
	#
	#####################################################
	private function MoveTags( $postId = null )
	{
		global $Admin;
		
		$postId = ( $postId ? $postId : $this->id );
		
		$_tags = $this->db->from( 
		null, 
		"SELECT taxonomy_id, id_relation
		FROM `" . DB_PREFIX . "tags_relationships`
		WHERE (object_id = " . $postId . ")"
		)->all();
	
		if ( $_tags )
		{
			foreach( $_tags as $_tag )
			{
				//First get the details from the DB
				$orTag = $this->db->from( 
				null, 
				"SELECT title, sef
				FROM `" . DB_PREFIX . "tags`
				WHERE (id = " . $_tag['taxonomy_id'] . ")"
				)->single();
				
				//If the tag doesn't exist, delete its relation
				if ( !$orTag )
				{
					$this->db->delete( 'tags_relationships' )->where( "id_relation", $_tag['id_relation'] )->run();
					
					continue;
				}
				
				//Update the relation for this tag
				$this->db->update( "tags_relationships" )->where( 'id_relation', $_tag['id_relation'] )->set( "id_site", $this->postSiteId );
			}
		}
	}
	
	#####################################################
	#
	# Image(s) Move function
	#
	#####################################################
	private function MoveImages( $postId = null )
	{
		global $Admin;
		
		$postId = ( $postId ? $postId : $this->id );
		
		//Move the cover image first
		MovePostCoverImage( $postId, $this->postSiteId );
		
		//Let's search for any images first
		$data = $this->db->from( 
		null, 
		"SELECT id_image, id_site
		FROM `" . DB_PREFIX . "images`
		WHERE (id_post = " . $postId . ")"
		)->all();
		
		if ( empty( $data ) )
			return;
		
		//Check if the current site is the default
		$isDefaultSite = $Admin->IsDefaultSite();
		
		$isTargetDefault = $Admin->GetSiteDefaultStatus( $this->postSiteId );
		
		$share = $Admin->ImageUpladDir( $this->postSiteId );
		
		//We have nothing to do here, because the image is already in the parent site
		if ( $isTargetDefault )
			return;
		
		//Check if these images exist in the target site
		foreach( $data as $im )
		{
			CheckImageExists( $im['id_image'], $this->postSiteId  );
		}
	}
	
	#####################################################
	#
	# Add a redirection to a new site function
	#
	#####################################################
	private function AddMoveRedir( $postId = null )
	{
		global $Admin;
		
		if ( !$Admin->Settings()::IsTrue( 'enable_redirect' ) )
			return;
		
		$postId = ( $postId ? $postId : $this->id );
		
		//Call the post again to grab its URL
		$Post 	= GetSinglePost( $postId, null, false );
		
		$url 	= $Post['postUrl'];
		$title 	= $Post['title'];
		$slug 	= $Post['sef'];
		
		unset( $Post );
		
		$redir = $this->db->from( 
		null, 
		"SELECT id, target
		FROM `" . DB_PREFIX . "redirs`
		WHERE (id_site = " . $this->postSiteId . ") AND (slug = :slug)",
		array( $slug => ':slug' )
		)->single();

		if ( $redir )
		{
			//Don't bother to update it if the url is the same
			if ( $url !== $redir['target'] )
			{
				$this->db->update( "redirs" )->where( 'id', $redir['id'] )->set( "target", $url );
			}
		}
		else
		{
			$dbarr = array(
				"id_site" 		=> $this->postSiteId,
				"title" 		=> sprintf( __( 'auto-redirection-for' ), $title ),
				"slug" 			=> $slug,
				"target" 		=> $url,
				"added_time" 	=> time()
			);

			$this->db->insert( 'redirs' )->set( $dbarr );
		}
	}
	
	#####################################################
	#
	# Post Schema function
	#
	#####################################################
	private function Schema()
	{
		if ( empty( $_POST['schema'] ) )
			return;
		//Here we can edit schema(s)
		EditSchemaFromForm( $_POST['schema'], $this->id );
	}

	#####################################################
	#
	# Post Tags function
	#
	#####################################################
	private function Tags()
	{
		// Add/Remove the tags, if the post is not a page of course
		if ( $this->moveToSite || ( $this->postType == 'page' ) )
			return;
		
		//Add the "normal" tags
		$this->tags = ( !empty( $_POST['tag'] ) ? json_decode( $_POST['tag'], true ) : null );
		
		if ( !empty( $_POST['customTags'] ) )
		{
			foreach( $_POST['customTags'] as $id => $t )
			{
				$this->customTags[$id] = Json( $t );
			}
		}
	}

	#####################################################
	#
	# Post Extra Data function
	#
	#####################################################
	private function XtraData()
	{
		global $Admin;
		
		//Get the current settings
		$settings = $Admin->Settings();
		
		//Get a few needed data fields from $xtraData
		$xtraDataSeo = ( isset( $this->xtraData['seo'] ) ? $this->xtraData['seo'] : array() );
		$xtraDataVideo = ( isset( $this->xtraData['video'] ) ? $this->xtraData['video'] : array() );
		$attributesData = array();//( isset( $this->xtraData['attributes'] ) ? $this->xtraData['attributes'] : array() );
		$xtraPostData = ( isset( $this->xtraData['post'] ) ? $this->xtraData['post'] : array() );
		
		//Set the post social media status here
		$xtraPostData['social_auto_published'] = ( isset( $xtraPostData['social_auto_published'] ) ? $xtraPostData['social_auto_published'] : false );
		
		//Set the post published status here
		$xtraPostData['post_published'] = ( isset( $xtraPostData['post_published'] ) ? $xtraPostData['post_published'] : false );
		
		//Check if we want to publish this post
		if ( isset( $_POST['publish'] ) && ( $this->postStatus != 'published' ) )
		{
			$this->publishPost = true;

			//Set the publish status
			if ( !$xtraPostData['post_published'] )
				$xtraPostData['post_published'] = true;
		}
		
		elseif ( ( $this->postStatus == 'published' ) && !$xtraPostData['post_published'] )
			$xtraPostData['post_published'] = true;
		
		$this->postIsPublished = $xtraPostData['post_published'];

		//Check if we want and should ping the search engines
		if (
			$this->publishPost && 
			!$settings::IsTrue( 'search_engine_disallow' ) &&
			$settings::IsTrue( 'notify_search_engines' )
			&& (
				!isset( $xtraPostData['search_engines_pinged'] ) 
				|| ( isset( $xtraPostData['search_engines_pinged'] ) && !$xtraPostData['search_engines_pinged'] )
			)
		)
		{
			//Don't ping search engines if we have maintenance mode enabled
			if ( !$settings::IsTrue( 'enable_maintenance', 'site' ) )
			{
				$this->pingEngines = true;
				$xtraPostData['search_engines_pinged'] = true;
			}
		}

		//Maybe we don't want to ping search engines. Do it here to have this option checked in draft posts
		if ( isset ( $_POST['postExtra']['dont_ping_search_engines'] ) && !empty( $_POST['postExtra']['dont_ping_search_engines'] ) )
		{
			$this->pingEngines = false;
			$xtraPostData['search_engines_pinged'] = false;
			$xtraPostData['dont_ping_search_engines'] = true;
		}

		//Here we can set the gallery images
		if ( isset( $_POST['gallery'] ) && !empty( $_POST['gallery'] ) )
		{
			foreach( $_POST['gallery'] as $idGalleryImg => $galleryImg )
			{
				$gallery[] = $idGalleryImg;
			}
		}
		else
			$gallery = array();
		
		//Do we have graph data?
		if ( isset ( $_POST['graph'] ) && !empty( $_POST['graph'] ) )
		{
			$xtraDataSeo['graph'] = array(
				'title' => Sanitize ( $_POST['graph']['title'], false ),
				'description' => Sanitize ( $_POST['graph']['description'], false ),
				'image' => ( isset( $_POST['graph']['graphImageFile'] ) ? Sanitize ( $_POST['graph']['graphImageFile'], false ) : '' )
			);
		}
		//We don't have any data from the $_POST, but check if we have something in the DB but we have disabled the graph for now, so we need to keep it as it is
		else
			$xtraDataSeo['graph'] = ( isset( $xtraDataSeo['graph'] ) ? $xtraDataSeo['graph'] : array() );

		//Do we have SEO data?
		if ( isset ( $_POST['seo'] ) && !empty( $_POST['seo'] ) )
		{
			$xtraDataSeo['seo'] = array(
				'noindex' => ( isset( $_POST['seo']['noindex'] ) ? true : false ),
				'nofollow' => ( isset( $_POST['seo']['nofollow'] ) ? true : false ),
				'noimageindex' => ( isset( $_POST['seo']['noimageindex'] ) ? true : false ),
				'noodp' => ( isset( $_POST['seo']['noodp'] ) ? true : false ),
				'nosnippet' => ( isset( $_POST['seo']['nosnippet'] ) ? true : false ),
				'noarchive' => ( isset( $_POST['seo']['noarchive'] ) ? true : false )
			);
		}
		
		//We don't have any data from the $_POST, but check if there is something in the DB but we have disabled the seo for now, so we need to keep it as it is
		else
			$xtraDataSeo['seo'] = ( isset( $xtraDataSeo['seo'] ) ? $xtraDataSeo['seo'] : array() );

		//Let's check if we have video data
		if ( isset ( $_POST['video'] ) && !empty( $_POST['video'] ) )
		{
			$xtraDataVideo = array(
				'id_playlist' => ( isset( $_POST['video']['playlist'] ) ? $_POST['video']['playlist'] : 0 ),
				'video_url' => ( isset( $_POST['video']['url'] ) ? $_POST['video']['url'] : null ),
				'embed_code' => ( isset( $_POST['video']['embed_code'] ) ? htmlentities( $_POST['video']['embed_code'] ) : '' ),
				'family_friendly' => ( isset( $_POST['video']['family_friendly'] ) ? true : false ),
				'duration' => 'PT' . ( isset( $_POST['video']['duration_min'] ) ? $_POST['video']['duration_min'] : '0' ) . 'M' . ( isset( $_POST['video']['duration_sec'] ) ? $_POST['video']['duration_sec'] : '0' ) . 'S',
				'video_height' => ( isset( $_POST['video']['height'] ) ? $_POST['video']['height'] : '0' ),
				'video_width' => ( isset( $_POST['video']['width'] ) ? $_POST['video']['width'] : '0' )
			);
		}
		else
			$xtraDataVideo = array();
	
		//Don't forget the extra data
		//Check first if we have one. This is a fail-safe checking, maybe we have imported the post and forgot its data. We can't continue without it.
		$xtraId = $this->db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "posts_data`
		WHERE (id_post = " . $this->id . ")"
		)->single();
		
		//Do the same for products data
		$prId = $this->db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "posts_product_data`
		WHERE (id_post = " . $this->id . ")"
		)->single();
		
		//If we don't have this value, add it now.
		if ( !$prId )
		{
			$q = $this->db->insert( 'posts_product_data' )->set( array( "id_post" => $this->id ) );
			
			$prId = ( $q ? $this->db->lastId() : 0 );
		}
		
		else
		{
			$prId = $prId['id'];
		}

		//If we don't have an extra value, add it now.
		if ( !$xtraId )
		{
			$dbarr = array(
				"id_post" 	=> $this->id,
				"keep_date" => ( $this->publishPost ? 1 : 0 ),
				"value1" 	=> '[]',
				"value2" 	=> '[]',
				"value3" 	=> '[]',
				"value4" 	=> '[]'
			);

			$q = $this->db->insert( 'posts_data' )->set( $dbarr );
			
			if ( $q )
			{
				$xtraId = $this->db->lastId();
			}
			else
			{
				return;
			}
		}
		
		else
		{
			$xtraId = $xtraId['id'];
		}
			
		//Let's take care of extra data, such as allow post comments etc
		//We do it here before add the "search_engines_pinged" to array
		if ( isset ( $_POST['postExtra'] ) && !empty( $_POST['postExtra'] ) )
		{
			//Add the value as it is or set it as false
			//because maybe we had set it before but now we ahve temporarily disabled a particular tool
			$xtraPostData = array(
				'subtitle' => ( isset( $_POST['postExtra']['subtitle'] ) ? htmlentities( $_POST['postExtra']['subtitle'] ) : '' ),
				
				'modified_reason' => ( isset( $_POST['postExtra']['modified_reason'] ) ? htmlentities( $_POST['postExtra']['modified_reason'] ) : '' ),

				'disable_comments' => ( isset( $_POST['postExtra']['disable_comments'] ) ? true :
									( ( !$settings::IsTrue( 'enable_comments' ) && isset( $xtraPostData['disable_comments'] ) ) ? $xtraPostData['disable_comments'] : false ) ),

				'disable_amp' => ( isset( $_POST['postExtra']['disable_amp'] ) ? true : 
									( ( !$settings::IsTrue( 'enable_amp' ) && isset( $xtraPostData['disable_amp'] ) ) ? $xtraPostData['disable_amp'] : false ) ),
									
				'disable_ads' => ( isset( $_POST['postExtra']['disable_ads'] ) ? true : 
									( ( !$settings::IsTrue( 'enable_ads' ) && isset( $xtraPostData['disable_ads'] ) ) ? $xtraPostData['disable_ads'] : false ) ),
										
				'enable_redirection' => ( isset( $_POST['postExtra']['enable_redirection'] ) ? true : 
									( ( !$settings::IsTrue( 'enable_redirect' ) && isset( $xtraPostData['enable_redirection'] ) ) ? $xtraPostData['enable_redirection'] : false ) ),
										
				'redirection_type' => ( isset( $_POST['postExtra']['redirection_type'] ) ? $_POST['postExtra']['redirection_type'] : 
									( ( !$settings::IsTrue( 'enable_redirect' ) && isset( $xtraPostData['redirection_type'] ) ) ? $xtraPostData['redirection_type'] : '' ) ),

				'post_published' => $xtraPostData['post_published'],

				'search_engines_pinged' => ( isset( $xtraPostData['search_engines_pinged'] ) ? $xtraPostData['search_engines_pinged'] : false ),
				
				'dont_ping_search_engines' => ( isset( $xtraPostData['dont_ping_search_engines'] ) ? $xtraPostData['dont_ping_search_engines'] : false )
			);
		}
		
		$ext 	= ( ( isset( $_POST['postExtra']['redirection_url'] ) && Validate( $_POST['postExtra']['redirection_url'], 'url' ) ) ? $_POST['postExtra']['redirection_url'] : '' );
		
		$extId 	= ( isset( $_POST['postExtra']['ext_id'] ) ? $_POST['postExtra']['ext_id'] : '' );
		
		$man 	= ( isset( $_POST['manufacturer'] ) ? (int) $_POST['manufacturer'] : 0 );
		
		$priceListTitle = ( isset( $_POST['postExtra']['priceListTitle'] ) ? $_POST['postExtra']['priceListTitle'] : '' );
		
		$allowVoting = ( isset( $_POST['postExtra']['allowVoting'] ) ? 1 : 0 );
		
		$addPriceNum = ( isset( $_POST['postExtra']['addPriceNum'] ) ? 1 : 0 );
		
		$hideOnHome = ( isset( $_POST['postExtra']['hideOnHome'] ) ? 1 : 0 );
		
		//Now it is time to add/update the extra data
		$dbarr = array(
			"value1" 		=> json_encode( $xtraDataVideo ), //Video
			"value2" 		=> json_encode( $xtraDataSeo ), //SEO
			"value3" 		=> json_encode( $gallery ), //Gallery
			"value4" 		=> json_encode( $xtraPostData ), //Post
			"external_url" 	=> $ext, //Post external Url
			"man_id" 		=> $man, //Manufacturer
			"ext_id" 		=> $extId,
			"allow_voting" 	=> $allowVoting,
			"prices_title" 	=> $priceListTitle,
			"hide_on_home" 	=> $hideOnHome,
			"add_price_num" => $addPriceNum
		);

		$this->db->update( "posts_data" )->where( 'id', $xtraId )->set( $dbarr );
	}
	
	#####################################################
	#
	# Post Edit function
	#
	# This function will check it there is any missing image on the server
	# according to current site's settings
	#
	#####################################################
	private function CheckPostImages()
	{
		global $Admin;
		
		if ( $Admin->IsDefaultSite() )
			return;
		
		$local = $Admin->ImageUpladDir( SITE_ID );

		$share = $Admin->ImageUpladDir( $this->postSiteId );
		
		// If this site doesn't have sharing enabled, don't continue
		if ( empty( $share ) || !isset( $share['share'] ) || !$share['share'] )
			return;
		
		$root = ( !empty( $local ) ? $local['root'] : null );

		$PostContent = StripContent( $this->postContent );
		
		$imgs = array();

		//Get the images from the content
		//Maybe an image from another post has been added later, so we have to check for this image too
		preg_match_all( '/\[image.+id="([0-9]+)".*\]/iU', $PostContent, $matches );

		if ( !empty( $matches['1'] ) )
		{
			foreach( $matches['1'] as $match )
			{
				if ( !empty( $match ) )
				{
					array_push( $imgs, $match );
				}
			}
		}
		
		//Get the videos from the content
		//Maybe an videos from another post added later, we have to check that image too
		preg_match_all('/\[video.+id="([0-9]+)".*]/iU', $PostContent, $matches );

		if ( !empty( $matches['1'] ) )
		{
			foreach( $matches['1'] as $match )
			{
				if ( !empty( $match ) )
				{
					array_push( $imgs, $match );
				}
			}
		}

		//Get the images
		$_img = $this->db->from( 
		null, 
		"SELECT id_image
		FROM `" . DB_PREFIX . "images`
		WHERE (id_post = " . $this->id . ")" . ( !empty( $imgs ) ? " AND (id_image NOT IN ('" . implode( "', '" , $imgs ) . "'))" : '' )
		)->all();

		if ( $_img )
		{
			foreach( $_img as $im )
			{
				$imId = $im['id_image'];
				
				if ( !in_array( $imId, $imgs ) )
				{
					array_push( $imgs, $imId );
				}
			}
		}
		
		//Get the cover image also
		$_img = $this->db->from( 
		null, 
		"SELECT image_id
		FROM `" . DB_PREFIX . "image_attachments`
		WHERE (post_id = " . $this->id . ")"
		)->single();
		
		if ( $_img )
		{
			array_push( $imgs, $_img['image_id'] );
		}

		if ( empty( $imgs ) )
			return;
		
		foreach( $imgs as $img )
		{
			//Get the image 
			$imgDt = $this->db->from( 
			null, 
			"SELECT id_image, filename, added_time, id_parent
			FROM `" . DB_PREFIX . "images`
			WHERE (id_image = " . $img . ")"
			)->single();
					
			if ( !$imgDt )
				continue;
			
			if ( empty( $imgDt['id_parent'] ) ) 
			{
				$childs = GetChildImages( $imgDt['id_image'] );
			}
			
			else
			{
				$imgDt = $this->db->from( 
				null, 
				"SELECT id_image, filename, added_time
				FROM `" . DB_PREFIX . "images`
				WHERE (id_image = " . $imgDt['id_parent'] . ")"
				)->single();
				
				$childs = GetChildImages( $imgDt['id_image'] );
			}
			
			$imgUrl = FolderUrlByDate( $imgDt['added_time'], $local['html'] ) . $imgDt['filename'];
			
			$Admin->PingChildSite( 'sync', 'image', null, $this->postSiteId, $imgUrl, $imgDt['added_time'] );
			
			if ( !empty( $childs ) )
			{
				foreach( $childs as $child )
				{
					$imgUrl = FolderUrlByDate( $imgDt['added_time'], $local['html'] ) . $child['filename'];
			
					$Admin->PingChildSite( 'sync', 'image', null, $this->postSiteId, $imgUrl, $imgDt['added_time'] );
				}
			}
		}
	}
		
	#####################################################
	#
	# Check and update the post's content Function
	#
	#####################################################
	private function PostContent()
	{
		global $Admin;
		
		$this->postContent = CleanContent( $_POST['content'] );
		
		if ( $Admin->Settings()::Get()['html_editor'] == 'editor-js' )
		{
			//Check if we an autosaved data
			$p = $this->db->from( 
			null, 
			"SELECT post, blocks
			FROM `" . DB_PREFIX . "posts_autosaves`
			WHERE (post_id = " . $this->id . ")
			ORDER BY added_time DESC
			LIMIT 1"
			)->single();
			
			if ( $p )
			{
				$this->blocksData 			= $p['blocks'];
				$this->postBuildedContent 	=  CleanContent( $p['post'] );
			}
			
			else
			{
				$this->postBuildedContent 	= CreatePostContent( $this->postContent, $this->postTitle, false, $this->post );
			}
		}
		
		//Clear the blocks data
		else
		{
			$this->blocksData			= '';
			$this->postBuildedContent 	= CreatePostContent( $this->postContent, $this->postTitle, false, $this->post );
		}
	}
		
	#####################################################
	#
	# Auto Publish to Social Media function
	#
	# TODO
	#####################################################
	private function AutoPublishToSocial()
	{
		global $Admin;

		if ( !$Admin->Settings()::IsTrue( 'enable_social_auto_publish' ) )
			return;
		
		$socialData = Json( $Admin->Settings()::Get()['auto_social_data'] );
		
		if ( empty( $socialData ) )
			return;
		
		$xtraPostData = ( isset( $this->xtraData['post'] ) ? $this->xtraData['post'] : array() );
	}
	
	#####################################################
	#
	# Post's Attributes function
	#
	#####################################################
	private function Attributes()
	{
		if ( !isset ( $_POST['att'] ) || empty( $_POST['att'] ) )
			return;
		
		foreach( $_POST['att'] as $id => $att )
		{
			if ( !empty( $att ) )
			{
				foreach( $att as $_ => $at )
				{
					if ( !is_array( $at ) )
					{
						//First check if have this data
						$p = $this->db->from( 
						null, 
						"SELECT id
						FROM `" . DB_PREFIX . "post_attribute_data`
						WHERE (id = " . $_ . ")"
						)->single();
						
						if ( $p )
						{
							if ( !empty( $at ) )
							{
								$this->db->update( "post_attribute_data" )->where( 'id', $_ )->set( "value", $at );
							}
							
							//If this value is empty, we are free to delete it
							else
							{
								$this->db->delete( 'post_attribute_data' )->where( "id", $_ )->run();
							}
						}
					}
					
					//if this is array, then this is new data
					else
					{
						foreach( $at as $__ => $at_ )
						{
							if ( empty( $at_ ) )
							{
								continue;
							}
							
							$dbarr = array(
								"id_attr" 	=> $id,
								"id_post" 	=> $this->id,
								"value" 	=> $at_
							);

							$this->db->insert( 'post_attribute_data' )->set( $dbarr );
						}
					}
				}
			}
		}
	}
	
	#####################################################
	#
	# Post's Deals List function
	#
	#####################################################
	private function DealsList()
	{
		if ( !isset ( $_POST['dealList'] ) || empty( $_POST['dealList'] ) )
			return;

		foreach( $_POST['dealList'] as $_id => $_p )
		{
			//We can't add a price without some data
			if ( empty( $_p['storeId'] ) || empty( $_p['currId'] ) )
				continue;
			
			$price = ( !empty( $_p['salePrice'] ) ? $_p['salePrice'] : 0 );
			
			$free = ( ( $price > 0 ) ? 0 : 1 );
			
			//Don't add the same price twice
			$p = $this->db->from( 
			null, 
			"SELECT id_price
			FROM `" . DB_PREFIX . "prices`
			WHERE (id_site = " . $this->postSiteId . ") AND (id_post = " . $this->id . ") AND (id_store = " . $_p['storeId'] . ")
			AND (id_currency = " . $_p['currId'] . ") AND (type = 'coupon')"
			)->single();

			if ( $p )
				continue;
			
			$time = ( !empty( $_p['date'] ) ? strtotime( $_p['date'] ) : time() );
			$exp = ( !empty( $_p['exp'] ) ? strtotime( $_p['exp'] ) : 0 );
			
			$dbarr = array(
				"id_post" 			=> $this->id,
				"id_currency" 		=> $_p['currId'],
				"id_site" 			=> $this->postSiteId,
				"id_store" 			=> $_p['storeId'],
				"user_id" 			=> $this->userId,
				"title" 			=> $_p['priceTitle'],
				"time_added" 		=> time(),
				"sale_price" 		=> $_p['salePrice'],
				"main_page_url" 	=> $_p['dealUrl'],
				"aff_page_url" 		=> $_p['dealAffUrl'],
				"type" 				=> 'coupon',
				"coupon_type" 		=> $_p['type'],
				"discount_title" 	=> $_p['amountTxt'],
				"description" 		=> $_p['descr'],
				"coupon_code" 		=> $_p['couponCode'],
				"available_since" 	=> $time,
				"expire_time" 		=> $exp,
				"is_free" 			=> $free
			);

			$id = $this->db->insert( 'prices' )->set( $dbarr, null, true );

			if ( $id )
			{
				$this->db->insert( 'price_info' )->set( array( "id_price" => $id ) );
			}
		}
	}
	
	#####################################################
	#
	# Remove Deals function
	#
	#####################################################
	private function DealsRemove()
	{
		global $Admin;
		
		$hasPrices = ( ( $Admin->IsEnabled( 'multivendor-marketplace' ) || $Admin->IsEnabled( 'compare-prices' ) || $Admin->IsEnabled( 'coupons-and-deals' ) ) ? true : false );
		
		if ( !$hasPrices )
			return;

		if ( !isset( $_POST['dealsDb'] ) || empty( $_POST['dealsDb'] ) )
		{
			//Maybe we want to remove all the deals?
			//Make sure we don't adding a new deal
			if ( !isset( $_POST['dealList'] ) )
			{
				$q = $this->db->from( 
				null, 
				"SELECT id_price
				FROM `" . DB_PREFIX . "prices`
				WHERE (id_post = " . $this->id . ") AND (type = 'coupon')"
				)->all();
				
				if ( $q )
				{
					foreach( $q as $p )
					{
						$this->db->delete( 'price_info' )->where( "id_price", $p['id_price'] )->run();
						
						$this->db->delete( 'price_update_info' )->where( "id_price", $p['id_price'] )->run();

						$this->db->delete( 'prices' )->where( "id_price", $p['id_price'] )->run();
					}
				}
			}
			
			return;
		}
		
		$foundPrices = array();

		foreach( $_POST['dealsDb'] as $pId )
		{
			array_push( $foundPrices, $pId );
		}
		
		// Get the prices, if any
		$q = $this->db->from( 
		null, 
		"SELECT id_price
		FROM `" . DB_PREFIX . "prices`
		WHERE (id_post = " . $this->id . ") AND (type = 'coupon') AND (id_price NOT IN (" . implode( ',', $foundPrices ) . "))"
		)->all();

		if ( !$q )
			return;

		foreach( $q as $p )
		{
			$this->db->delete( 'price_info' )->where( "id_price", $p['id_price'] )->run();
			
			$this->db->delete( 'price_update_info' )->where( "id_price", $p['id_price'] )->run();

			$this->db->delete( 'prices' )->where( "id_price", $p['id_price'] )->run();
		}
	}
	
	#####################################################
	#
	# Post Drafts function
	#
	#####################################################
	private function Drafts()
	{
		if ( empty( $this->drafts ) || !isset( $this->drafts['enable_post_drafts'] ) 
				|| !$this->drafts['enable_post_drafts'] || !IsAllowedTo( 'save-drafts' ) )
			return;
		
		$q = null;
		
		//If this is a minor edit, update the last key
		if ( $this->minorEdit )
		{
			// Get the data
			$q = $this->db->from( 
			null, 
			"SELECT id
			FROM `" . DB_PREFIX . "posts_autosaves`
			WHERE (post_id = " . $this->id . ") AND (user_id = " . $this->userId . ") AND (draft_type = 'manual')"
			)->single();
		}

		//This is a minor update and we've found its data, so we have to update this input
		if ( $q )
		{
			$dbarr = array(
				"title" 		=> $this->postTitle,
				"post" 			=> $this->postContent,
				"edited_time" 	=> time(),
				"draft_type" 	=> 'manual'
			);

			$this->db->update( "posts_autosaves" )->where( 'id', $q['id'] )->set( $dbarr );
		}
		
		//Nothing found, continue...
		else
		{
			$dbarr = array(
				"post_id" 		=> $this->id,
				"user_id" 		=> $this->userId,
				"id_site" 	 	=> $this->postSiteId,
				"title" 		=> $this->postTitle,
				"post" 			=> $this->postContent,
				"added_time" 	=> time(),
				"draft_type" 	=> 'manual'
			);

			$this->db->insert( 'posts_autosaves' )->set( $dbarr );
		}
	}
	
	#####################################################
	#
	# Get the default category of the current site/lang/blog function
	#
	# Useful when a page is converted to post
	#
	#####################################################
	private function SiteDefaultCategory()
	{
		if ( $this->postType == 'page' )
			return 0;
		
		$data = $this->db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "categories`
		WHERE (id_site = " . $this->postSiteId . ") AND (id_lang = " . $this->postLangId . ")
		AND (id_blog = " . $this->postBlogId . ") AND (is_default = 1)"
		)->single();
		
		return ( $data ? $data['id'] : 0 );
	}
}