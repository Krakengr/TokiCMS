<?php defined('TOKICMS') or die('Hacking attempt...');

// No cache headers
header( 'Expires: Sat, 1 Jan 2000 01:00:00 GMT');
header( 'Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
header( 'Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0' );
header( 'Pragma: no-cache');
header( 'Vary: Accept-Encoding');
header( 'X-Content-Type-Options: nosniff');
header( 'X-Frame-Options: SAMEORIGIN');

class Ajax extends Controller
{
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
	}
	
	private function Run() 
	{
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-own-posts' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'create-new-posts' ) )
			exit;
		
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			exit;

		global $Admin, $L;

		$token = ( isset( $_SESSION['token'] ) ? $_SESSION['token'] : null );
		
		$ajaxAction = Router::GetVariable( 'ajaxFunction' );
		
		require( FUNCTIONS_ROOT . 'ajax-functions.php' );

		//Load the theme's function file, if any
		if ( file_exists( ADMIN_THEME_PHP_ROOT . 'functions.php' ) )
			include( ADMIN_THEME_PHP_ROOT . 'functions.php' );

		//If the theme has its own nonce string, check it here
		$nonce = ( isset( $nonce ) && $nonce ? $nonce : ( isset( $_POST['token'] ) ? $_POST['token'] : ( isset( $_POST['_token'] ) ? $_POST['_token'] : null ) ) );

		if ( !empty( $ajaxAction ) )
		{
			$data = array();
			
			$defaulLang = $Admin->DefaultLang();
			
			//Save a new draft post
			if ( $ajaxAction == 'add-draft' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( $this->AddDraftPost() );
			}
			
			//Save the post's data
			if ( $ajaxAction == 'auto-draft' )
			{
				if ( !isset( $_POST['id'] ) || empty( $_POST['id'] ) )
					exit;
				
				$dbarr = array(
					"post_id" 		=> (int) $_POST['id'],
					"user_id"		=> (int) $_POST['user'],
					"id_site" 		=> (int) $_POST['site'],
					"added_time"	=> time(),
					"title" 		=> $_POST['title'],
					"post" 			=> $_POST['content']
				);
					
				$put = $this->db->insert( 'posts_autosaves' )->set( $dbarr );
					
				if ( !$put )
				{
					echo json_encode( array( 'status' => 'error' ) );
					exit;
				}
				
				echo json_encode( array( 'status' => 'ok' ) );

				exit;
			}
			
			//Show Theme's details
			if ( $ajaxAction == 'theme' )
			{
				header( 'Content-Type: text/html; charset=UTF-8' );

				if ( !isset( $_SESSION['token'] ) || !hash_equals( $nonce, $_SESSION['token'] ) )
				{
					echo __( 'an-error-happened-refresh-page' );
					exit;
				}
				
				if ( !isset( $_POST['id'] ) || empty( $_POST['id'] ) )
				{
					echo __( 'an-error-happened' );
					exit;
				}

				$Theme = LoadTheme( $_POST['id'] );
				
				if ( !$Theme )
				{
					echo __( 'an-error-happened' );
					exit;
				}

				//Reload the Admin's settings
				$Admin->SetAdminSettings();

				include( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'ajax-quick-theme-info.php' );
				
				echo $string;
				
				exit;
			}
			
			//Save Block data
			if ( $ajaxAction == 'save-blocs' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				echo json_encode( ajaxSaveBlocks() );
				exit;
			}
			
			//Edit Theme's details
			if ( $ajaxAction == 'theme-edit' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( $this->EditThemeInfo() );
			}
			
			//Clone a post
			if ( $ajaxAction == 'clone-post' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( ajaxClonePost() );
			}
			
			//Edit APIs
			if ( $ajaxAction == 'apis-edit' )
			{
				if ( !isset( $_POST['id'] ) || empty( $_POST['id'] ) )
					exit;
				
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxEditApis() );
				exit;
			}
			
			//Quick sort widgets
			if ( $ajaxAction == 'sort-widgets' )
			{
				if ( !isset( $_POST['ids'] ) || empty( $_POST['ids'] ) )
					exit;
				
				echo json_encode( ajaxWidgetSort() );
				
				exit;
			}
			
			//Quick sort Form Elemets
			if ( $ajaxAction == 'sort-form' )
			{
				if ( !isset( $_POST['ids'] ) || empty( $_POST['ids'] ) )
					exit;
				
				echo json_encode( AjaxFormSort() );
				
				exit;
			}
			
			//Quick sort Table Columns
			if ( $ajaxAction == 'sort-table' )
			{
				if ( !isset( $_POST['ids'] ) || empty( $_POST['ids'] ) )
					exit;
				
				echo json_encode( AjaxTableSort() );
				
				exit;
			}
			
			//Change Column name
			if ( $ajaxAction == 'change-column-title' )
			{
				if ( !isset( $_POST['id'] ) || empty( $_POST['id'] ) )
					exit;
				
				echo json_encode( AjaxChangeColumnName() );
				
				exit;
			}
			
			//Quick sort Column Elemets
			if ( $ajaxAction == 'sort-columns' )
			{
				if ( !isset( $_POST['ids'] ) || empty( $_POST['ids'] ) )
					exit;
				
				echo json_encode( AjaxTableSortColumns() );
				
				exit;
			}

			//Remove a form Element
			if ( $ajaxAction == 'remove-form-element' )
			{
				echo json_encode( AjaxRemFormElement() );
				
				exit;
			}
			
			//Load Move Content
			if ( $ajaxAction == 'load-move-content' )
			{
				echo json_encode( AjaxLoadMoveContent() );
				
				exit;
			}
			
			//Move Content
			if ( $ajaxAction == 'move-content' )
			{
				echo json_encode( AjaxMoveContent() );
				
				exit;
			}
			
			//Add a form Element
			if ( $ajaxAction == 'add-form-element' )
			{
				echo json_encode( AjaxAddFormElement() );
				
				exit;
			}
			
			//Remove a column from table
			if ( $ajaxAction == 'remove-table-column' )
			{
				echo json_encode( AjaxRemTableColumn() );
				
				exit;
			}
			
			//Remove an element from column
			if ( $ajaxAction == 'remove-table-element' )
			{
				echo json_encode( AjaxRemTableColumnElement() );
				
				exit;
			}
			
			//Add a table Element
			if ( $ajaxAction == 'add-table-element' )
			{
				echo json_encode( AjaxAddTableElement() );
				
				exit;
			}
			
			//Add a column in table
			if ( $ajaxAction == 'add-table-column' )
			{
				echo json_encode( AjaxAddTableColumn() );
				
				exit;
			}
			
			//Remove single variation
			if ( $ajaxAction == 'remove-single-variation' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( AjaxRemoveSingleVariation() );
				
				exit;
			}
			
			//Add single variation
			if ( $ajaxAction == 'add-single-variation' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( AjaxAddSingleVariation() );
				
				exit;
			}
			
			//Remove variation group
			if ( $ajaxAction == 'remove-variation-group' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( AjaxRemoveVariationGroup() );
				
				exit;
			}
			
			//Add variation group
			if ( $ajaxAction == 'add-variation-group' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( AjaxAddVariationGroup() );
				
				exit;
			}
			
			//Get post variants
			if ( $ajaxAction == 'get-variants' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( AjaxGetVariants() );
				
				exit;
			}
			
			//Quick sort widgets
			if ( $ajaxAction == 'add-widget' )
			{
				echo json_encode( ajaxAddWidget() );
				
				exit;
			}
			
			//Add price cover image
			if ( $ajaxAction == 'add-cover-price' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );

				echo json_encode( AjaxAddCoverPrice() );
				exit;
			}
			
			//Quick edit a price
			if ( $ajaxAction == 'edit-single-price' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );

				echo json_encode( ajaxEditSinglePrice() );
				exit;
			}
			
			//Quick remove a price
			if ( $ajaxAction == 'remove-single-price' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );

				echo json_encode( AjaxRemovePrice() );
				exit;
			}
			
			//Quick add a price
			if ( $ajaxAction == 'add-new-price' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );

				echo json_encode( AjaxAddNewPrice() );
				exit;
			}
			
			//Quick edit a price
			if ( $ajaxAction == 'edit-price' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );

				echo json_encode( AjaxEditPrice(), JSON_UNESCAPED_UNICODE );
				exit;
			}
			
			//Get sinlge price details
			if ( $ajaxAction == 'get-single-price' )
			{
				if ( !is_numeric( $_POST['id'] ) )
					exit;

				$Price = adminSinglePrice( $_POST['id'] );
				
				if ( !$Price )
				{
					echo json_encode( array( 'status' => 'error' ) );
					exit;
				}
				
				echo json_encode( array( 'status' => 'ok', 'data' => $Price ), JSON_UNESCAPED_UNICODE );
				
				exit;
			}
			
			//Quick edit a post
			if ( $ajaxAction == 'quick-edit-post' )
			{
				if ( !hash_equals( $nonce, $_SESSION['token'] ) )
					exit;
				
				if ( !is_numeric( $_POST['post'] ) )
					exit;
				
				echo 'TODO';
				exit;
				$tmp = GetSinglePost( $_POST['post'], null, false );
				
				if ( !$tmp )
					exit;
				
				header( 'Content-Type: text/html; charset=UTF-8' );
				
				include( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'ajax-quick-post-template.php' );
				
				echo $string;
				exit;
			}
			
			//Save the Logs
			if ( $ajaxAction == 'add-logs' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );

				echo json_encode( ajaxSaveAdminLogs() );
				exit;
			}
			
			//Save the Dashboard order
			if ( $ajaxAction == 'dashboard-sort' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );

				echo json_encode( ajaxDashboardSort() );
				exit;
			}
			
			//Get Import details for XML file
			if ( $ajaxAction == 'import-details-xml' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );
				
				echo json_encode( AjaxImportDetailsXML() );
				exit;
			}
			
			//Get Import details
			if ( $ajaxAction == 'import-details' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );
				
				echo json_encode( ajaxImportDetails() );
				exit;
			}
			
			//Reset Import
			if ( $ajaxAction == 'import-reset' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );
				
				echo json_encode( AjaxResetImport() );
				exit;
			}
			
			//Import content from XML
			if ( $ajaxAction == 'import-content-xml' )
			{
				if ( empty( $_POST ) )
					exit;
				
				@set_time_limit(600);
				@ini_set('default_socket_timeout', 900);
				@ini_set('memory_limit', '256M');
				
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				echo json_encode( AjaxImportXMLContent() );
				exit;
			}
			
			//Import content
			if ( $ajaxAction == 'import-content' )
			{
				if ( empty( $_POST ) )
					exit;
				
				@set_time_limit(600);
				@ini_set('default_socket_timeout', 900);
				@ini_set('memory_limit', '256M');
				
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				echo json_encode( ajaxImportContent() );
				exit;
			}
			
			//Upload Import file
			if ( $ajaxAction == 'import-file-upload' )
			{
				if ( empty( $_FILES ) || empty( $_POST ) )
					exit;
				
				@set_time_limit(600);
				@ini_set('default_socket_timeout', 900);
				@ini_set('memory_limit', '256M');
				
				header( 'Content-Type: application/json; charset=UTF-8' );

				echo json_encode( ajaxUploadImportFile() );
				exit;
			}
			
			//Upload user profile image
			if ( $ajaxAction == 'user-logo-upload' )
			{
				if ( empty( $_FILES ) || empty( $_POST ) )
					exit;
				
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxUserImage() );
				exit;
			}

			//Upload the site's maintenance background
			if ( $ajaxAction == 'maintenance-background-upload' )
			{
				if ( empty( $_FILES ) || empty( $_POST ) )
					exit;
				
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxMaintenanceBackgroundImage() );
				exit;
			}
			
			//Upload the site's logo
			if ( $ajaxAction == 'logo-upload' )
			{
				if ( empty( $_FILES ) || empty( $_POST ) )
					exit;
				
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxSiteImage() );
				exit;
			}
			
			//Upload using drag 'n drop
			if ( $ajaxAction == 'drop-media-upload' )
			{
				if ( empty( $_FILES ) || empty( $_POST ) )
					exit;
				
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxUploadDropFile() );
				exit;
			}
			
			//Get the users
			if ( $ajaxAction == 'get-users' )
			{
				if ( empty( $_POST ) || !isset( $_POST['postSite'] ) )
					exit;
				
				header( 'Content-Type: application/json; charset=UTF-8' );
				$arr = array( 'results' => array() );
				
				$postSite = (int) $_POST['postSite'];
				$key = $_POST['query'];
				
				$res = $this->db->from( 
				null, 
				"SELECT id_member, user_name
				FROM `" . DB_PREFIX . "members`
				WHERE (id_site = " . $postSite . ") AND (user_name LIKE :query)
				ORDER BY user_name ASC",
				array( '%' . $key . '%' => ':query' )
				)->all();

				if ( $res )
				{
					foreach( $res as $p )
					{
						$arr['results'][] = array(
							'disabled' => false,
							'id' => $p['id_member'],
							'text' => htmlspecialchars( stripslashes( $p['user_name'] ) ),
							'type' => ''
						);
					}
				}
				
				echo json_encode($arr);
				exit;
			}
			
			//Show Single Image Details
			if ( $ajaxAction == 'edit-gallery-image' )
			{
				if ( empty( $_POST ) || !isset( $_POST['id'] ) )
					exit;
				
				echo ajaxSingleGalleryImage();
				exit;
			}
			
			//Get Single Image Details
			if ( $ajaxAction == 'media-get-single' )
			{
				if ( empty( $_POST ) || !isset( $_POST['id'] ) )
					exit;
				
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxSingleGalleryImageDetails() );
				exit;
			}
			
			//Get New Category Form
			if ( $ajaxAction == 'add-category-form' )
			{
				if ( empty( $_POST ) || !isset( $_POST['post'] ) )
					exit;
				
				header( 'Content-Type: text/html; charset=UTF-8' );
				echo ajaxNewCategoryForm();
				exit;
			}
			
			//Add New Category Form
			if ( $ajaxAction == 'add-new-category-form' )
			{
				if ( empty( $_POST ) || !isset( $_POST['postId'] ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxNewCategoryPost() );
				exit;
			}
			
			//Edit Single Image Details
			if ( $ajaxAction == 'edit-image-details' )
			{
				if ( empty( $_POST ) || !isset( $_POST['id'] ) )
					exit;
				
				ajaxEditSingleImageDetails();
				exit;
			}
			
			//Reset the Gallery
			if ( $ajaxAction == 'reset-gallery' )
			{
				if ( empty( $_POST ) )
					exit;
				
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxResetGallery() );
				exit;
			}
			
			//Search the images
			if ( $ajaxAction == 'media-manager-search' )
			{				
				include_once( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'media-gallery.php' );
				include_once( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'media-manager-javascript.php' );
				echo frontMedia();
				echo searchGraphMedia();			
				exit;
			}
			
			//Get the images (Post Gallery)
			if ( $ajaxAction == 'media-manager-gallery' )
			{
				include_once( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'media-gallery.php' );
				include_once( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'media-manager-javascript.php' );
				echo frontMedia( 'gallery' );
				echo frontGraphMedia( 'gallery' );
				
				exit;
			}
			
			//Get the images (Post Editor)
			if ( $ajaxAction == 'media-manager-editor' )
			{
				include_once( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'media-gallery.php' );
				include_once( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'media-manager-javascript.php' );
				echo frontMedia( 'editor' );
				echo frontGraphMedia( 'editor' );
				
				exit;
			}
			
			//Get the images (Graph)
			if ( $ajaxAction == 'media-manager-graph' )
			{
				include_once( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'media-gallery.php' );
				include_once( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'media-manager-javascript.php' );
				echo frontMedia();
				echo frontGraphMedia();
				
				exit;
			}
			
			//Create a new folder
			if ( $ajaxAction == 'media-folder' )
			{
				include_once( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'media-gallery.php' );
				include_once( ADMIN_THEME_PAGES_ROOT . 'ajax-templates' . DS . 'media-manager-javascript.php' );
				echo frontMedia();
				echo frontGraphMedia();
				exit;
			}
			
			//Create a new folder
			if ( $ajaxAction == 'create-manager-folder' )
			{
				echo json_encode( ajaxCreateNewFolder() );
				exit;
			}
			
			//Import an external image
			if ( $ajaxAction == 'import-external-image' )
			{
				echo json_encode( ajaxImportExternalImage() );
				exit;
			}
			
			//Upload an new image
			if ( $ajaxAction == 'media-manager-upload' )
			{
				echo json_encode( ajaxPostImageUpload() );
				exit;
			}
			
			//Insert media in editor
			if ( $ajaxAction == 'insert-media-editor' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxInsertMediaToEditor() );
				exit;
			}
			
			//Get Blogs with categories for move posts
			if ( $ajaxAction == 'get-move-blogs' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxGetMoveBlogs() );
				exit;
			}
			
			//Get Site with blogs and categories for move posts
			if ( $ajaxAction == 'get-move-site' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxGetMoveSite() );
				exit;
			}
			
			//Search for Posts
			if ( $ajaxAction == 'search-posts' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxSearchForPosts() );
				exit;
			}
			
			//Search for Posts with Prices
			if ( $ajaxAction == 'search-posts-prices' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxSearchForPostsPrices(), JSON_UNESCAPED_UNICODE );
				exit;
			}
			
			//Delete a folder/image
			if ( $ajaxAction == 'media-manager-delete' )
			{
				echo json_encode( ajaxDeleteMediaData() );
				exit;
			}
			
			//Add Menu Items
			if ( $ajaxAction == 'add-menu-item' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				echo json_encode( ajaxAddMenuItems() );
				exit;
			}
			
			//Remove Menu Items
			if ( $ajaxAction == 'rem-menu-item' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				echo json_encode( ajaxRemoveMenuItems() );
				exit;
			}
			
			//Delete the Menu
			if ( $ajaxAction == 'delete-menu' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				echo json_encode( ajaxDeleteMenu() );
				exit;
			}
			
			//Save the Menu
			if ( $ajaxAction == 'save-menu' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				echo json_encode( ajaxSaveMenu() );
				exit;
			}
			
			//Get the manufacturers
			if ( $ajaxAction == 'get-manufacturers' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( ajaxGetManufacturers() );
			}
			
			//Get the stores
			if ( $ajaxAction == 'get-stores' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( ajaxGetStores() );
			}
			
			//Search for posts
			if ( $ajaxAction == 'search-sync-posts' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( ajaxGetSyncPosts() );
			}
			
			//Add form to editor
			if ( $ajaxAction == 'add-form-in-editor' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( AjaxShowNewForm() );
			}			
			
			//Get sinlge price details
			if ( $ajaxAction == 'map-single-post' )
			{
				if ( !is_numeric( $_POST['pId'] ) || empty( $_POST['sId'] ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );
				echo json_encode( ajaxMapSinglePost() );
				exit;
			}
			
			//Get the categories for filters
			if ( $ajaxAction == 'filter-get-categories' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( ajaxGetFilterCategories() );
			}
			
			//Get the blogs for filters
			if ( $ajaxAction == 'filter-get-blogs' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( ajaxGetFilterBlogs() );
			}
			
			//Get the tags for filters
			if ( $ajaxAction == 'filter-get-tags' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( ajaxGetFilterTags() );
			}
			
			//Get the pages for filters
			if ( $ajaxAction == 'filter-get-pages' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( ajaxGetFilterPages() );
			}
			
			//Get the posts
			if ( $ajaxAction == 'get-posts' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$data = json_encode( $this->GetPosts() );
			}
			
			//Search for a price
			if ( $ajaxAction == 'search-prices' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$arr = array( 'results' => array() );
				
				$siteId = (int) $_POST['postSite'];
				$key 	= $_POST['query'];
				
				$res = $this->db->from( null, "
				SELECT p.id_price, p.title, p.sale_price, s.name as st, po.title as ppt, la.code as cd, c.name as cu, c.code as cc, c.symbol as cs, c.format as cf, c.exchange_rate as cr
				FROM `" . DB_PREFIX . "prices` as p
				INNER JOIN `" . DB_PREFIX . "stores` AS s ON s.id_store = p.id_store
				INNER JOIN `" . DB_PREFIX . POSTS . "` AS po ON po.id_post = p.id_post
				INNER JOIN `" . DB_PREFIX . "languages` AS la ON la.id = po.id_lang
				INNER JOIN `" . DB_PREFIX . "currencies` AS c ON c.id = p.id_currency
				WHERE (p.id_site = " . $siteId . ") AND (p.title LIKE :query)",
				array( '%' . $key . '%' => ':query' )
				)->all();

				if ( $res )
				{
					foreach( $res as $p )
					{
						$arr['results'][] = array(
							'disabled' 	=> false,
							'id' 		=> $p['id_price'],
							'store' 	=> $p['st'],
							'price' 	=> ( !empty( $p['sale_price'] ) ? formatPrice( $p['sale_price'], $p['cf'] ) : 0 ),
							'text' 		=> ( !empty( $p['title'] ) ? StripContent( $p['title'] ) : StripContent( $p['ppt'] ) ),
						);
					}
				}

				echo json_encode( $arr, JSON_UNESCAPED_UNICODE );
				exit;
			}
			
			//Get the categories
			if ( $ajaxAction == 'get-categories' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$arr = array( 'results' => array() );
				
				$langId = (int) $_POST['lang'];
				$siteId = (int) $_POST['site'];
				$blogId = (int) $_POST['blog'];
				$catId 	= (int) $_POST['catID'];
				$key 	= $_POST['query'];
				
				if ( $defaulLang['id'] == $langId )
					exit;
				
				$res = $this->db->from( null, "
				SELECT id, name
				FROM `" . DB_PREFIX . "categories`
				LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = p.id_lang
				WHERE (id_lang = " . $langId . ") AND (id_blog = " . $blogId . ") AND (id_site = " . $siteId . ") AND (id_parent = 0) AND (name LIKE :query)",
				array( '%' . $key . '%' => ':query' )
				)->all();

				if ( $res )
				{
					foreach( $res as $p )
					{
						$arr['results'][] = array(
							'disabled' => false,
							'id' => $p['id'],
							'text' => htmlspecialchars( stripslashes( $p['name'] ) )
						);
					}
				}

				$data = json_encode($arr);
			}
			
			//Create the Slug
			if ( $ajaxAction == 'create-slug' )
			{
				header( 'Content-Type: text/html; charset=UTF-8' );
				
				$data = AdminEditPostSlug( $_POST['slug_id'], $_POST['name'] );
			}
			
			//Get the slug
			if ( $ajaxAction == 'slug' )
			{
				header( 'Content-Type: text/html; charset=UTF-8' );

				$data = SetShortSef( POSTS, 'id_post', 'sef', CreateSlug( $_POST['slug'], true ) );
			}
			
			//Change post's language
			if ( $ajaxAction == 'change-post-language' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );
				
				$aa = adminEditPostLang( $_POST['lang_meta_current_language'], $_POST['post_id'] );

				$data = json_encode($aa);
			}
			
			//Get the top tags
			if ( $ajaxAction == 'get-top-tags' )
			{
				if ( empty( $_POST ) )
					exit;

				header( 'Content-Type: application/json; charset=UTF-8' );

				echo json_encode( AjaxGetTopTags(), JSON_UNESCAPED_UNICODE );
				exit;
			}
			
			//Get the tags
			if ( $ajaxAction == 'tags' )
			{
				header( 'Content-Type: application/json; charset=UTF-8' );

				//Get the language, we shouldnt have "$_POST['lang']" if the multilang is off
				$lang = (int) ( isset( $_POST['lang'] ) ? $_POST['lang'] : ( isset( $_POST['lang2'] ) ? $_POST['lang2'] : $Admin->DefaultLang()['id'] ) );
				
				$postType = (int) ( ( isset( $_POST['psttp'] ) && !empty( $_POST['psttp'] ) ) ? $_POST['psttp'] : 0 );
				
				//$arr = ( ( isset( $_POST['tparr'] ) && !empty( $_POST['tparr'] ) ) ? $_POST['tparr'] : null );
				
				$data = json_encode( GetAjaxTags( $lang, $postType ), JSON_UNESCAPED_UNICODE );
			}
			
			//Upload Cover
			if ( $ajaxAction == 'upload-cover' )
			{
				if ( empty( $_FILES ) || empty( $_POST ) )
					exit;
				
				$postId = (int) $_POST['post_id'];
				
				$tmp = GetSinglePost( $postId, null, false );
			
				if ( !$tmp )
					exit;
				
				$Post 		= new Post( $tmp );
				$tmp_name 	= $_FILES['file']['tmp_name'];
				$siteId 	= $Post->Site()->id;
				$postId 	= $Post->PostID();
				$blogId 	= $Post->Blog()->id;
				$langId 	= $Post->Language()->id;
				$postDate 	= ( empty( $Post->Added()->raw ) ? time() : $Post->Added()->raw );
				$folder 	= imgFolderRoot( $postDate );
				
				$name = pathinfo( $_FILES['file']['name'] );

				$fileName = URLify( $name['filename'] ) . '.' . $name['extension'];
				
				if( move_uploaded_file( $tmp_name, $folder . $fileName ) )
				{
					$this->db->delete( 'images' )->where( "id_post", $postId )->where( "img_type", 'cover' )->run();
				
					addLocalImage( $fileName, $postId, $langId, $blogId, $siteId, $Admin->UserID(), 'cover', $Post->Title(), $postDate );
				}
				
				if ( is_file( $folder . $fileName ) )
				{
					$img = new SimpleImage;
					
					$img->load( $folder . $fileName );
						
					$img->scale( 75 );
					
					$img->save( $folder . $fileName );
				}

				exit;
			}
	
			echo $data;

			exit(0);
		}
	}
	
	#####################################################
	#
	# Get the posts function
	#
	#####################################################
	private function GetPosts()
	{
		global $Admin;
		
		$arr = array( 'results' => array() );

		$langId 	= ( isset( $_POST['postLang'] ) ? (int) $_POST['postLang'] : null );
		$postId 	= ( isset( $_POST['postID'] ) ? (int) $_POST['postID'] : null );
		$siteId 	= (int) $_POST['postSite'];
		$postType 	= ( isset( $_POST['postType'] ) ? Sanitize ( $_POST['postType'], false ) : null );
		$key 		= $_POST['query'];
		$parent 	= ( isset( $_POST['getParent'] ) ? IsTrue( $_POST['getParent'] ) : false );
		$pageParent = ( isset( $_POST['pageParent'] ) ? $_POST['pageParent'] : false );
		$getDrafts 	= ( isset( $_POST['getDrafts'] ) ? IsTrue( $_POST['getDrafts'] ) : false );
		
		$defaulLang = $Admin->GetDefaultLanguage( $siteId );

		//There should only one parent for translations
		if ( !$parent && !$pageParent && $langId && ( $defaulLang == $langId ) )
			return array( 'results' => array() );
		
		$q  = "(id_site = " . $siteId . ")";
		
		$q .= ( $langId 	? " AND (id_lang = " . $langId . ")" : "" );
		$q .= ( !$parent 	? " AND (id_parent = 0)" : "" );
		$q .= ( $pageParent ? " AND (id_page_parent = 0)" : "" );
		$q .= ( $postType 	? " AND (post_type = '" . $postType . "')" : "" );
		$q .= ( $postId 	? " AND (id_post != " . $postId . ")" : "" );
		$q .= ( !$getDrafts ? " AND (post_status = 'published')" : "" );
		$q .= " AND (title LIKE :query)";
		
		$res = $this->db->from( null, "
		SELECT id_post, title, post_status
		FROM `" . DB_PREFIX . POSTS . "`
		WHERE " . $q,
		array( '%' . $key . '%' => ':query' )
		)->all();

		if ( $res )
		{
			foreach( $res as $p )
			{
				$arr['results'][] = array(
						'disabled' => false,
						'id' => $p['id_post'],
						'text' => htmlspecialchars( stripslashes( $p['title'] ) ),
						'type' => $p['post_status']
				);
			}
		}
				
		return $arr;
	}
	
	#####################################################
	#
	# Add a new draft post function
	#
	#####################################################
	private function AddDraftPost()
	{
		global $Admin;
		
		if ( empty( $_POST['title'] ) )
			return array( 'status' => 'error', 'type' => 'warning', 'message' => __( 'error-please-fill-the-required-fields' ) . ': ' . __( 'title' ) );
		
		if ( empty( $_POST['post'] ) )
			return array( 'status' => 'error', 'type' => 'warning', 'message' => __( 'error-please-fill-the-required-fields' ) . ': ' . __( 'content' ) );
		
		global $Admin;
		
		$langId = ( isset( $_POST['lang_id'] ) ? $_POST['lang_id'] : $Admin->GetLang() );
		$blogId = ( isset( $_POST['blog_id'] ) ? $_POST['blog_id'] : $Admin->GetBlog() );
		$siteId = ( isset( $_POST['site_id'] ) ? $_POST['site_id'] : $Admin->GetSite() );
		$userId = ( isset( $_POST['user_id'] ) ? $_POST['user_id'] : $Admin->UserID() );
		
		$slug 	= SetShortSef( POSTS, 'id_post', 'sef', CreateSlug( $_POST['title'], true ) );
		
		$dbarr = array(
			"id_lang" 		=> $langId,
			"id_blog" 		=> $blogId,
			"id_member" 	=> $userId,
			"id_site" 		=> $siteId,
			"post" 			=> $_POST['post'],
			"sef" 			=> $slug,
			"title" 		=> $_POST['title'],
			"poster_ip" 	=> GetRealIp(),
			"post_status" 	=> 'draft',
			"added_time" 	=> time()
        );
            
		$put = $this->db->insert( POSTS )->set( $dbarr );
		
		if ( $put )
		{
			$id = $this->db->lastId();
			
			return array( 'status' => 'ok', 'type' => 'success', 'message' => sprintf( __( 'post-successfully-added-click-to-edit-it' ), $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $id ) ) );
		}
		else
		{
			return array( 'status' => 'error', 'type' => 'warning', 'message' => __( 'form-submit-error' ) );
		}
	}
	
	#####################################################
	#
	# Edit theme's info function
	#
	#####################################################
	private function EditThemeInfo()
	{
		global $Admin;

		if ( !isset( $_POST['id'] ) || empty( $_POST['id'] ) || !isset( $_POST['post'] ) )
			return array( 'status' => 'error', 'message' => __( 'an-error-happened-refresh-page' ) );

		$Theme = LoadTheme( $_POST['id'] );
	
		if ( !$Theme )
			return array( 'status' => 'error', 'message' => __( 'an-error-happened' ) );
		
		$siteId  = (int) $_POST['site'];
		
		$themeId = $_POST['id'];
		
		//Continue and load the site's settings
		$Settings = new Settings( $site, false );

		$themesData = $Settings::Themes();

		$arr = array();

		foreach ( $_POST['post'] as $set )
		{
			if ( $set['name'] == 'id' )
				continue;
			
			if ( ( strpos( $set['name'], '::' ) === false ) )
			{
				$arr[$set['name']] = $set['value'];
			}
			else
			{
				$e = explode( '::', $set['name'] );

				$arr[$e['0']][$e['1']] = $set['value'];
			}
		}
		
		//Let's keep the auto-menu settings here for a moment
		$autoMenu = ( isset( $themesData[$themeId]['auto-menu'] ) ? $themesData[$themeId]['auto-menu'] : null );
		
		//Set the new settings for this theme
		$themesData[$themeId] = $arr;
		
		//Add back the auto-menu settings, if any
		if ( !empty( $autoMenu ) )
		{
			$themesData[$themeId]['auto-menu'] = $autoMenu;
		}

		$settingsArray = array( 'themes_data' => json_encode( $themesData ) );

		$Admin->UpdateSettings( $settingsArray, $siteId );

		$Admin->DeleteSettingsCacheSite( 'settings' );

		return array( 'status' => 'ok', 'message' => __( 'data-updated' ) );
	}
}