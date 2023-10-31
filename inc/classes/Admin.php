<?php defined('TOKICMS') or die('Hacking attempt...');

class Admin
{
	private $themeFile;
	private $formFile;
	private $formData;
	private $langCode;
	private $adminSettings;
	private $defaultSiteName;
	private $currentAction;
	private $currentLang;
	private $siteUrl;
	private $adminUri;
	private $draftID 			= 0;
	private $langID 			= 1;
	private $siteID 			= SITE_ID;
	private $blogID 			= 0;
	private $isChildSite 		= false;
	private $multiBlogEnabled 	= false;
	private $multiLangEnabled 	= false;
	private $multiSiteEnabled 	= false;
	private $siteIsSelfHosted 	= true;
	private $blogsData 			= [];
	private $dashBoardWidgets 	= [];
	private $adminMessages 		= [];
	private $searchString;
	private $catId;
	private $siteHost;
	private $headerTitle;
	private $blogName;
	private $blogType;
	private $siteName;
	private $menuItems;
	private $sites;
	private $headerCode;
	private $footerCode;
	private $adminMessage;
	private $adminMessageType;
	private $maintenanceTasks;
	private $scheduledTasks;
	private $hash;
	private $adminLogCounts;
	private $activeTheme;
	private $themes;
	private $lang;
	public  $db;
	public  $disableFormButtons = false;
	public  $themesUri;
	public  $newsUri;
	public 	$settings;
	public 	$auth;
	
	public function Run()
	{
		global $L;
		
		$this->lang 			= $L;
		$this->db				= db();
		$this->Token();
		$this->adminMessage 	= '';
		$this->currentAction 	= Router::GetVariable( 'slug' );
		$this->themeFile		= $this->SetAdminThemeFile();
		$this->maintenanceTasks = $this->AdminMaintenanceTasks();
		$this->scheduledTasks 	= $this->AdminScheduledTasks();
		$this->SetAdminUrl();
		$this->adminSettings 	= $this->SetAdminSettings();
		
		$this->SetBlogData();
		
		//Set the DraftID
		$this->SetDraftID();
		
		$this->langID 			= $this->SetLangID();
		
		//Set the Host
		$this->SetHost();
		
		$this->headerTitle 		= $this->SetHeaderTitle();
		$this->formData 		= $this->SetAdminFormData();
		$this->ButtonsArray();

		//$this->themesUri 		= 'https://badtooth.studio/tokicms/';
		$this->newsUri 			= 'https://badtooth.studio/tokicms/feed/';
		
		//Other Jobs
		$this->DefaultDashBoardWidgets();
		$this->UpdatePostsViews();
		$this->UpdateSiteStats();
		$this->SetPingSlashAndUpdateHash();
		$this->AdminLogCounts();
		
		if ( isset( $_SESSION['admin_error_message'] ) && !empty( $_SESSION['admin_error_message'] ) )
		{
			foreach( $_SESSION['admin_error_message'] as $mess )
			{
				array_push( $this->adminMessages, $mess );
			}
			
			$_SESSION['admin_error_message'] = array();
		}

	}
	
	#####################################################
	#
	# Get Admin Log Counts function
	#
	#####################################################
	private function AdminLogCounts()
	{
		$this->adminLogCounts = AdminLogCounts();
	}
		
	#####################################################
	#
	# Set Admin Form Data function
	#
	#####################################################
	private function SetAdminFormData()
	{
		require ( ARRAYS_ROOT . 'admin-actions-arrays.php');

		if ( !is_null ( $this->currentAction ) && ( isset( $actionArray[$this->currentAction]['form'] ) ) )
		{
			if ( !is_null( $actionArray[$this->currentAction]['form'] ) && file_exists( FORMS_ROOT . $actionArray[$this->currentAction]['form'] ) )
			{
				//Load the form file
				require ( FORMS_ROOT . $actionArray[$this->currentAction]['form'] );
				
				$token = ( ( isset( $actionArray[$this->currentAction]['token'] ) && !is_null( $actionArray[$this->currentAction]['token'] ) ) ? $actionArray[$this->currentAction]['token'] : $this->currentAction );

				//Return the proper array
				return array( 'form' => $form, 'token' => $token );
			}
		}

		return null;
	}
	
	#####################################################
	#
	# Set Admin Theme File function
	#
	#####################################################
	private function SetAdminThemeFile()
	{
		require ( ARRAYS_ROOT . 'admin-actions-arrays.php');
		
		$action = $this->currentAction;
		
		$file = '';
		
		if ( !is_null ( $action ) && ( isset( $actionArray[$action] ) ) )
		{
			$data = $actionArray[$action];
			
			if ( isset( $data['dir'] ) && !empty( $data['dir'] ) )
			{
				$file .= ( ( isset( $data['is_plugin'] ) && !empty( $data['is_plugin'] ) ) ? PLUGINS_ROOT : '' );
				
				$file .= $data['dir'] . DS . $data['file'];
			}
			
			else
			{
				$file .= ADMIN_THEME_PAGES_ROOT . $data['file'];
			}
		}

		if ( !empty( $file ) && file_exists( $file ) )
		{
			return $file;
		}

		return ADMIN_THEME_PAGES_ROOT . 'dashboard.php';
	}
	
	#####################################################
	#
	# Get Admin Notify Message function
	#
	#####################################################
	public function GetAdminMessage( $returnType = false )
	{
		return ( $returnType ? $this->adminMessageType : $this->adminMessage );
	}
	
	#####################################################
	#
	# Get Admin Notify Messages function
	#
	#####################################################
	public function GetAdminMessages()
	{
		return $this->adminMessages;
	}

	#####################################################
	#
	# Get Menu Items function
	#
	#####################################################
	public function Menu()
	{
		return $this->menuItems;
	}
	
	#####################################################
	#
	# Get Current Settings function
	#
	#####################################################
	public function Settings()
	{
		return $this->adminSettings;
	}

	#####################################################
	#
	# Get Log Counts function
	#
	#####################################################
	public function GetLogCounts()
	{
		return $this->adminLogCounts;
	}
	
	#####################################################
	#
	# Get User ID function
	#
	#####################################################
	public function UserID()
	{
		$id = $this->auth['id_member'];
		
		//Get the user's ID for this site
		if ( $this->isChildSite )
		{
			//Query: member relationship
			$usr = $this->db->from( null, 
			"SELECT id_cloned_member
			FROM `" . DB_PREFIX . "members_relationships`
			WHERE (id_member = " . $this->auth['id_member'] . ") AND (id_site = " . $this->siteID . ")"
			)->single();
		
			if ( $usr )
			{
				$id = $usr['id_cloned_member'];
			}
		}
		
		return $id;
	}
	
	#####################################################
	#
	# Get DashBoard Widgets function
	#
	#####################################################
	public function DashBoardWidgets()
	{
		return $this->dashBoardWidgets;
	}
	
	#####################################################
	#
	# Set Default DashBoard Widgets function
	#
	#####################################################
	private function DefaultDashBoardWidgets()
	{
		$this->dashBoardWidgets =
		array(
			
			'stats' => array( 'function' => 'AdminStats', 'title' => __( 'stats' ), 'allow' => array( 'admin-site', 'view-stats' ), 'disable' => ( $this->Settings()::IsTrue( 'enable_stats' ) ? false : true ) ),
		
			'at-glance' => array( 'function' => 'AtAGlanse', 'title' => __( 'at-a-glance' ), 'allow' => array( 'admin-site', 'view-stats' ), 'disable' => false ),
		
			'top-posts' => array( 'function' => 'TopDashboardPosts', 'title' => __( 'top-posts' ), 'allow' => array( 'admin-site', 'manage-posts', 'manage-own-posts' ), 'disable' => false ),
		
			'latest-posts' => array( 'function' => 'LatestDashboardPosts', 'title' => __( 'latest-posts' ), 'allow' => array( 'admin-site', 'manage-posts', 'manage-own-posts' ), 'disable' => false ),
		
			'latest-news-and-releases' => array( 'function' => 'LatestNewsUpdates', 'title' => __( 'latest-news-and-releases' ) ),
		
			'quick-draft' => array( 'function' => 'CreatePostDashboard', 'title' => __( 'quick-draft' ), 'allow' => array( 'admin-site', 'manage-posts', 'create-new-posts' ), 'disable' => false ),
		
			'latest-comments' => array( 'function' => 'LatestDashboardComments', 'title' => __( 'latest-comments' ), 'allow' => array( 'admin-site', 'manage-comments', 'create-new-comments' ), 'disable' => false ),
		
			'latest-logs' => array( 'function' => 'LatestDashboardLogs', 'title' => __( 'logs' ), 'allow' => array( 'admin-site'), 'disable' => false )
		);
	}
	
	#####################################################
	#
	# Get User DashBoard Data function
	#
	#####################################################
	public function UserDashData()
	{
		//Query: member dashboard data
		$usr = $this->db->from( null, 
		"SELECT dashboard_data
		FROM `" . DB_PREFIX . USERS . "`
		WHERE (id_member = " . $this->UserID() . ")"
		)->single();

		return ( !$usr ? array() : Json( $usr['dashboard_data'] ) );
	}

	#####################################################
	#
	# Set Theme File function
	#
	#####################################################
	public function SetFile( $file )
	{
		return $this->themeFile = $file;
	}
	
	#####################################################
	#
	# Get Current Action function
	#
	#####################################################
	public function CurrentAction()
	{
		return $this->currentAction;
	}
	
	#####################################################
	#
	# Get Form File function
	#
	#####################################################
	public function FormFile()
	{
		return $this->formFile;
	}
	
	#####################################################
	#
	# Get Form Data function
	#
	#####################################################
	public function FormData()
	{
		return $this->formData;
	}
	
	#####################################################
	#
	# Get Form Buttons Disable Status function
	#
	#####################################################
	public function DisableFormButtons()
	{
		return $this->disableFormButtons;
	}
	
	#####################################################
	#
	# Get Theme File function
	#
	#####################################################
	public function ThemeFile()
	{
		return $this->themeFile;
	}
	
	#####################################################
	#
	# Get Header Code function
	#
	#####################################################
	public function HeaderCode()
	{
		return html_entity_decode( htmlspecialchars_decode( $this->headerCode ) );
	}
	
	#####################################################
	#
	# Get the Footer Code function
	#
	#####################################################
	public function FooterCode()
	{
		return html_entity_decode( htmlspecialchars_decode( $this->footerCode ) );
	}
	
	#####################################################
	#
	# Get Header Title function
	#
	#####################################################
	public function HeaderTitle()
	{
		return $this->headerTitle;
	}
	
	#####################################################
	#
	# Get Page Title function
	#
	#####################################################
	public function PageTitle()
	{
		return htmlspecialchars( $this->headerTitle );
	}
	
	#####################################################
	#
	# Set Header Title function
	#
	#####################################################
	private function SetHeaderTitle()
	{
		if ( $this->currentAction && isset( $this->lang[$this->currentAction] ) )
			return $this->lang[$this->currentAction];
		
		return __( $this->currentAction );
	}

	#####################################################
	#
	# Get Current Sitename function
	#
	#####################################################
	public function SiteName()
	{
		return htmlspecialchars( $this->siteName );
	}
	
	#####################################################
	#
	# Get Site's default language function
	#
	#####################################################
	public function DefaultLang()
	{
		return $this->adminSettings::Lang();
	}
	
	#####################################################
	#
	# Get Site's default language ID function
	#
	#####################################################
	public function DefaultLangId()
	{
		return $this->adminSettings::Lang()['id'];
	}
	
	#####################################################
	#
	# Get Current Locale function
	#
	#####################################################
	public function Locale()
	{
		if ( !$this->multiLangEnabled || !$this->IsDefaultLang() )
			return Settings::LangData()['lang']['locale'];
		
		return $this->currentLang['lang']['locale'];
	}
	
	#####################################################
	#
	# Get Current Language's Code function
	#
	#####################################################
	public function LangCode()
	{
		if ( !$this->multiLangEnabled || $this->IsDefaultLang() )
			return Settings::LangData()['lang']['code'];
		
		return $this->currentLang['lang']['code'];
	}
	
	#####################################################
	#
	# Get Current Language Title function
	#
	#####################################################
	public function LangName()
	{
		if ( !$this->IsDefaultLang() )
		{
			return $this->currentLang['lang']['title'];
		}
		
		return $this->adminSettings::Lang()['title'];
	}

	#####################################################
	#
	# Get Current Date Format function
	#
	#####################################################
	public function DateFormat()
	{
		if ( !$this->IsDefaultLang() )
		{
			return $this->currentLang['settings']['date_format'];
		}
		
		return $this->adminSettings::LangData()['settings']['date_format'];
	}

	#####################################################
	#
	# Check if the current lang is the default function
	#
	#####################################################
	public function IsDefaultLang()
	{
		return ( ( $this->langID == $this->adminSettings::Lang()['id'] ) ? true : false );
	}
	
	#####################################################
	#
	# Check if the current site is the default function
	#
	#####################################################
	public function IsDefaultSite()
	{
		return ( !$this->isChildSite ? true : false );
	}
	
	#####################################################
	#
	# Check if the URL has a blog function
	#
	#####################################################
	public function NoBlog()
	{
		return ( ( $this->blogID == 0 ) ? true : false );
	}
	
	#####################################################
	#
	# Set Current Site Id function
	#
	#####################################################
	public function GetSite()
	{
		return $this->siteID;
	}
	
	#####################################################
	#
	# Get the current Token function
	#
	#####################################################
	public function GetToken()
	{
		return $this->hash;
	}
	
	#####################################################
	#
	# Get the search string function
	#
	#####################################################
	public function GetSearchString()
	{
		return urldecode( $this->searchString );
	}
	
	#####################################################
	#
	# Get the category Id function
	#
	#####################################################
	public function GetCatId()
	{
		return $this->catId;
	}
	
	#####################################################
	#
	# Add header Code function
	#
	#####################################################
	public function AddHeaderCode( $code )
	{
		$this->headerCode .= $code . PHP_EOL;
	}
	
	#####################################################
	#
	# Add Foote Code function
	#
	#####################################################
	public function AddFooterCode( $code )
	{
		$this->footerCode .= $code . PHP_EOL;
	}
	
	#####################################################
	#
	# Set Current Lang Id function
	#
	#####################################################
	public function GetLang()
	{
		return $this->langID;
	}
	
	#####################################################
	#
	# Get All The Blogs function
	#
	#####################################################
	public function GetBlogs()
	{
		return $this->adminSettings::BlogsFullArray();
	}
	
	#####################################################
	#
	# Get Main Site Name function
	#
	#####################################################
	public function GetMainSiteName()
	{
		return Settings::Site()['title'];
	}
	
	#####################################################
	#
	# Get every Other Lang of the parent site function
	#
	#####################################################
	public function OtherLangs()
	{
		return Settings::Langs();
	}
	
	#####################################################
	#
	# Get every Othe Lang of the current site function
	#
	#####################################################
	public function SiteOtherLangs()
	{
		//Query: languages
		$tmp = $this->db->from( null, 
		"SELECT id, title, code
		FROM `" . DB_PREFIX . "languages`
		WHERE (id != " . $this->langID . ") AND (id_site = " . $this->siteID . ")"
		)->all();

		return $tmp;
	}

	#####################################################
	#
	# Get Current Lang's Code function
	#
	#####################################################
	public function LangKey()
	{
		return $this->langCode;
	}
	
	#####################################################
	#
	# Get Current's Blog ID function
	#
	#####################################################
	public function GetBlog()
	{
		return $this->blogID;
	}
	
	#####################################################
	#
	# Get Current's Draft ID function
	#
	#####################################################
	public function GetDraft()
	{
		return $this->draftID;
	}

	#####################################################
	#
	# Get Current Site URL function
	#
	#####################################################
	public function SiteUrl()
	{
		return $this->siteUrl;
	}
	
	#####################################################
	#
	# Get Admin's Url function
	#
	#####################################################
	public function Url()
	{
		return $this->adminUri;
	}
	
	#####################################################
	#
	# Get Current Site Multilang Status function
	#
	#####################################################
	public function MultiLang()
	{
		return $this->multiLangEnabled;
	}
	
	#####################################################
	#
	# Get Current Blog Name function
	#
	#####################################################
	public function BlogName()
	{
		return $this->blogName;
	}

	#####################################################
	#
	# Check if this is the video Blog function
	#
	#####################################################
	public function IsVideoBlog()
	{
		return $this->IsEnabled( 'videos' );
	}
	
	#####################################################
	#
	# Check if a blog or parent site is different than "normal" function
	#
	#####################################################
	public function IsEnabled( $type )
	{
		if ( !empty( $type ) )
		{
			if ( $this->blogID == 0 )
			{
				if ( $this->adminSettings::Get()['parent_type'] == $type )
					return true;
			}
			
			else
			{
				/*
				$blogs = $this->adminSettings::BlogsFullArrayById();

				if
				( 
					!empty( $blogs )
					&& isset( $blogs[$this->blogID] )
					&& ( $blogs[$this->blogID]['type'] == $type )
				)
				{
					return true;
				}
				*/
				$blogsData = Json( $this->adminSettings::Get()['extra_blogs'] );

				if
				( 
					!empty( $blogsData ) 
					&& !empty( $blogsData['types'] )
					&& !empty( $blogsData['types'][$type] ) 
					&& in_array( $this->blogID, $blogsData['types'][$type] )
				)
				{
					return true;
				}
				
			}
		}
		
		return false;
	}
	
	#####################################################
	#
	# Check if a feature is enabled function
	#
	#####################################################
	public function HasEnabled( $type )
	{
		if ( $this->adminSettings::Get()['parent_type'] == $type )
			return true;
	
		$blogsData = Json( $this->adminSettings::Get()['extra_blogs'] );

		if ( !empty( $blogsData ) && !empty( $blogsData['types'] ) && isset( $blogsData['types'][$type] )
			&& !empty( $blogsData['types'][$type] ) )
			return true;
		
		return false;
	}
	
	#####################################################
	#
	# Get Current Lang Data function
	#
	#####################################################
	public function CurrentLang()
	{
		return $this->currentLang;
	}
	
	#####################################################
	#
	# Get Current Site Hosted Status function
	#
	#####################################################
	public function SiteIsSelfHosted()
	{
		return $this->siteIsSelfHosted;
	}
	
	#####################################################
	#
	# Get Current Site Host function
	#
	#####################################################
	public function SiteHost()
	{
		return $this->siteHost;
	}
	
	#####################################################
	#
	# Get Current Site Multiblog Status function
	#
	#####################################################
	public function MultiBlog()
	{
		return $this->multiBlogEnabled;
	}
	
	#####################################################
	#
	# Get Default's Site Name function
	#
	#####################################################
	public function DefaultSiteName()
	{
		return $this->defaultSiteName;
	}
	
	#####################################################
	#
	# Check if the site has open graph enabled function
	#
	#####################################################
	public function OpenGraph()
	{
		if ( $this->adminSettings::IsTrue( 'enable_seo' ) && isset( $this->adminSettings::Seo()['enable_open_graph'] ) )
			return $this->adminSettings::Seo()['enable_open_graph'];
		
		return false;
	}
	
	#####################################################
	#
	# Check if the site has schema enabled function
	#
	#####################################################
	public function Schema()
	{
		if ( $this->adminSettings::IsTrue( 'enable_seo' ) && !empty( $this->adminSettings::Seo() ) && isset( $this->adminSettings::Seo()['enable_schema_markup'] ) )
			return $this->adminSettings::Seo()['enable_schema_markup'];
		
		return false;
	}
	
	#####################################################
	#
	# Set Current Draft Id function
	#
	#####################################################
	private function SetDraftID()
	{
		$parameters = Router::GetVariable( 'parameters' );

		if ( !empty( $parameters ) && isset( $parameters['draft'] ) && !empty( $parameters['draft'] ) )
		{
			$this->draftID = $parameters['draft'];
		}
	}
	
	#####################################################
	#
	# Set The Host function
	#
	#####################################################
	private function SetHost()
	{
		$host = Json( $this->adminSettings::Site()['hosted'] );
		
		$host = isset( $host[$this->langCode]['blog-' . $this->blogID] ) ? $host[$this->langCode]['blog-' . $this->blogID] : 'self';

		$this->siteIsSelfHosted = ( $host === 'self' );
		
		$this->siteHost = $host;
	}
	
	#####################################################
	#
	# Set Current Lang Id function
	#
	#####################################################
	private function SetLangID()
	{
		$parameters = Router::GetVariable( 'parameters' );

		if ( !empty( $parameters ) && isset( $parameters['lang'] ) && !empty( $parameters['lang'] ) )
		{
			//Set this lang as current
			if ( isset( $this->adminSettings::AllLangsById()[$parameters['lang']] ) )
				$this->currentLang = $this->adminSettings::AllLangsById()[$parameters['lang']];
			
			//The given lang ID is wrong...
			else
			{
				@header('Location: ' . ADMIN_URI );
				exit;
			}
			
			$this->langCode = $this->currentLang['lang']['code'];

			return $parameters['lang'];
		}

		//Return the default lang ID
		$this->currentLang = $this->adminSettings::LangData();
		$this->langCode = $this->currentLang['lang']['code'];
		return $this->adminSettings::LangData()['lang']['id'];
	}
	
	#####################################################
	#
	# Buttons Array function
	#
	#####################################################
	private function ButtonsArray()  
	{
		$L = $this->lang;
		$lang = $this->adminSettings::Lang();
		$settings = $this->adminSettings;
		
		include ( ARRAYS_ROOT . 'menus-arrays.php');
		
		//Add the stats into the array
		if ( $settings::IsTrue( 'enable_stats' ) )
			$buttonsArray['core']['items']['stats'] = $statsArray;

		//Add the posts into the array
		$buttonsArray['core']['items']['posts'] = $postsArray;
		
		//Add the comments into the array
		$buttonsArray['core']['items']['comments'] = $commentsArray;
		
		//Add the users into the array
		$buttonsArray['core']['items']['users'] = $usersArray;
		
		//Form Tools
		if ( $settings::IsTrue( 'enable_forms' ) )
			$buttonsArray['core']['items']['forms'] = $formsArray;
		
		//Add the tools into the array
		$buttonsArray['core']['items']['tools'] = $toolsArray;
		
		//Add the emails into the array
		$buttonsArray['core']['items']['emails'] = $emailsArray;
		
		if ( $settings::IsTrue( 'enable_redirect' ) )
			$buttonsArray['core']['items']['redirects'] = $redirectionsArray;

		if ( $this->IsEnabled( 'coupons-and-deals' ) || $this->IsEnabled( 'multivendor-marketplace' ) || $this->IsEnabled( 'store' ) || $this->IsEnabled( 'compare-prices' ) )
			$buttonsArray['core']['items']['stores'] = $storesArray;
		
		if ( $this->IsEnabled( 'coupons-and-deals' ) || $this->IsEnabled( 'multivendor-marketplace' ) || $this->IsEnabled( 'store' ) || $this->IsEnabled( 'compare-prices' ) )
			$buttonsArray['core']['items']['manufacturers'] = $manufacturersArray;
		/*
		if ( $this->IsEnabled( 'coupons-and-deals' ) || $this->IsEnabled( 'multivendor-marketplace' ) || $this->IsEnabled( 'compare-prices' ) )
			$buttonsArray['core']['items']['vendors'] = $vendorsArray;
		*/
		//if ( MULTILANG )
		if ( $settings::IsTrue( 'enable_multilang', 'site' ) )
			$buttonsArray['core']['items']['langs'] = $langsArray;
			
		//if ( MULTIBLOG )
		if ( $settings::IsTrue( 'enable_multiblog', 'site' ) )
		{
			$buttonsArray['core']['items']['blogs'] = $blogsArray;
			//$buttonsArray['core']['items']['videos'] = $videosArray;
			$buttonsArray['core']['items']['forums'] = $forumArray;
		}
		
		if ( $this->IsVideoBlog() )
		{
			$buttonsArray['core']['items']['videos'] = $videosArray;
		}
		
		//Social Media Auto Publish
		if ( $settings::IsTrue( 'enable_social_auto_publish' ) )
			$buttonsArray['core']['items']['social-media-auto-publish'] = $socialAutoPublish;
		
		//Link Manager 
		if ( $settings::IsTrue( 'enable_link_manager' ) )
			$buttonsArray['core']['items']['link-manager'] = $linkManagerArray;
		
		//Auto Content
		if ( $settings::IsTrue( 'enable_autoblog' ) )
			$buttonsArray['core']['items']['autocontent'] = $AutoContentArray;
		
		//Only the parent site can have a site's list
		if ( !$this->isChildSite && MULTISITE )
			$buttonsArray['core']['items']['sites'] = $sitesArray;
		
		//SEO Tools
		if ( $settings::IsTrue( 'enable_seo' ) )
			$buttonsArray['core']['items']['seo'] = $seoArray;
		
		//Ads Tools
		if ( $settings::IsTrue( 'enable_ads' ) )
			$buttonsArray['core']['items']['ads'] = $adsArray;
		
		//Maintenance
		$buttonsArray['core']['items']['maintenance'] = $maintenanceArray;
		
		RunHooks( 'in_admin_menu', $buttonsArray['core']['items'] );
		
		$this->menuItems = $buttonsArray;
	}
	
	#####################################################
	#
	# Get Current URL function
	#
	#####################################################
	public function GetUrl( $string = null, $id = null, $getAction = false, $extraValues = array(), $getCat = false )
	{
		$parameters = Router::GetVariable( 'parameters' );
		$hasSite = $hasLang = $hasBlog = $hasDraft = $hasSearch = false;
		
		$url = ADMIN_URI;
		
		$url .=  ( ( $string && !$id ) ? $string . PS : '' ) . ( ( $getAction && ( $this->currentAction != 'dashboard' ) ) ? $this->currentAction . PS : '' );
		
		if ( !empty( $extraValues ) )
		{
			foreach( $extraValues as $extraValue )
			{
				$url .= $extraValue . PS;
			}
		}
		
		if ( !empty( $parameters ) && !$id )
		{
			if ( isset( $parameters['site'] ) && !empty( $parameters['site'] ) )
			{
				$hasSite = true;
				$url .= '?site=' . $parameters['site'];
			}
			
			if ( isset( $parameters['blog'] ) && !empty( $parameters['blog'] ) )
			{
				$hasBlog = true;
				$url .= ( $hasSite ? ';' : '?' ) . 'blog=' . $parameters['blog'];
			}
			
			if ( isset( $parameters['lang'] ) && !empty( $parameters['lang'] ) )
			{
				$hasLang = true;
				$url .= ( $hasSite || $hasBlog ? ';' : '?' ) . 'lang=' . $parameters['lang'];
			}
			/*
			if ( isset( $parameters['draft'] ) && !empty( $parameters['draft'] ) && ( $string != 'draft' ) )
			{
				$hasDraft = true;
				$url .= ( $hasSite || $hasBlog || $hasLang ? ';' : '?' ) . 'draft=' . $parameters['draft'];
			}
			
			if ( isset( $parameters['search'] ) && !empty( $parameters['search'] ) && ( $string != 'search' ) )
			{
				$hasSearch = true;
				$url .= ( $hasSite || $hasBlog || $hasLang || $hasDraft ? ';' : '?' ) . 'search=' . $parameters['search'];
			}
			*/
			if ( isset( $parameters['category'] ) && !empty( $parameters['category'] ) && $getCat )
			{
				$url .= ( ( $hasSite || $hasLang || $hasBlog || $hasSearch ) ? ';' : '?' ) . 'cat=' . $parameters['category'];
			}
		}
		
		else
		{
			if ( isset( $parameters['site'] ) && !empty( $parameters['site'] ) && ( $string != 'site' ) )
			{
				$hasSite = true;
				$url .= '?site=' . $parameters['site'];
			}
			
			elseif ( $string == 'site' )
			{
				$hasSite = true;
				$url .= '?' . $string . '=' . $id;
			}
				
			if ( isset( $parameters['blog'] ) && !empty( $parameters['blog'] ) && ( $string != 'blog' ) )
			{
				$hasBlog = true;
				$url .= ( ( $hasSite || ( $string == 'site' ) ) ? ';' : '?' )  . 'blog=' . $parameters['blog'];
			}
			
			elseif ( $string == 'blog' )
			{
				$hasBlog = true;
				$url .= ( ( $hasSite || ( $string == 'site' ) ) ? ';' : '?' ) . $string . '=' . $id;
			}
				
			if ( isset( $parameters['lang'] ) && !empty( $parameters['lang'] ) && ( $string != 'lang' ) )
			{
				$hasLang = true;
				$url .= ( $hasSite || $hasBlog ? ';' : '?' ) . 'lang=' . $parameters['lang'];
			}
			
			elseif ( $string == 'lang' )
			{
				$hasLang = true;
				$url .= ( ( $hasSite || $hasBlog || ( $string == 'site' ) || ( $string == 'blog' ) ) ? ';' : '?' ) . $string . '=' . $id;
			}
			
			if ( isset( $parameters['draft'] ) && !empty( $parameters['draft'] ) && ( $string != 'draft' ) )
			{
				$hasDraft = true;
				$url .= ( $hasSite || $hasBlog || $hasLang ? ';' : '?' ) . 'draft=' . $parameters['draft'];
			}
			
			if ( isset( $parameters['search'] ) && !empty( $parameters['search'] ) && ( $string != 'search' ) )
			{
				$hasSearch = true;
				$url .= ( $hasSite || $hasBlog || $hasLang || $hasDraft ? ';' : '?' ) . 'search=' . $parameters['search'];
			}
			
			if ( isset( $parameters['category'] ) && !empty( $parameters['category'] ) )
			{
				$url .= ( ( $hasSite || $hasLang || $hasBlog || $hasSearch ) ? ';' : '?' ) . 'cat=' . $parameters['category'];
			}
		}

		return $url;
	}
	
	#####################################################
	#
	# Set Current Admin URL function
	#
	#####################################################
	public function SetAdminUrl()
	{
		$parameters = Router::GetVariable( 'parameters' );

		$this->adminUri = ADMIN_URI;
		
		if ( !empty( $parameters ) )
		{
			$hasSite = $hasLang = $hasBlog = $hasSearch = false;
			
			if ( isset( $parameters['site'] ) && !empty( $parameters['site'] ) )
			{
				$this->adminUri .= '?site=' . $parameters['site'];
				$this->siteID = $parameters['site'];
				$hasSite = true;
			}
			
			if ( isset( $parameters['blog'] ) && !empty( $parameters['blog'] ) )
			{
				$this->adminUri .= ( ( $hasSite || $hasLang ) ? ';' : '?' ) . 'blog=' . $parameters['blog'];
				$this->blogID = $parameters['blog'];
				$hasBlog = true;
			}
			
			if ( isset( $parameters['lang'] ) && !empty( $parameters['lang'] ) )
			{
				$this->adminUri .= ( ( $hasSite || $hasBlog ) ? ';' : '?' ) . 'lang=' . $parameters['lang'];
				$this->langID = $parameters['lang'];
				$hasLang = true;
			}
			
			if ( isset( $parameters['search'] ) && !empty( $parameters['search'] ) )
			{
				$this->adminUri .= ( ( $hasSite || $hasLang || $hasBlog ) ? ';' : '?' ) . 'search=' . $parameters['search'];
				$this->searchString = urlencode( $parameters['search'] );
				$hasSearch = true;
			}
			
			if ( isset( $parameters['category'] ) && !empty( $parameters['category'] ) )
			{
				$this->adminUri .= ( ( $hasSite || $hasLang || $hasBlog || $hasSearch ) ? ';' : '?' ) . 'cat=' . $parameters['category'];
				
				$this->catId 	 = $parameters['category'];
			}
		}
	}
	
	#####################################################
	#
	# Update Current Admin URL function
	#
	#####################################################
	public function CustomAdminUrl( $site = null, $lang = null, $blog = null, $search = null, $category = null, $order = null, $by = 'asc' )
	{
		$parameters = Router::GetVariable( 'parameters' );

		$adminUri 	= ADMIN_URI;
		
		$adminUri  .= ( ( $this->currentAction != 'dashboard' ) ? $this->currentAction . PS : '' );
		
		$order	 	= ( !empty( $order ) ? 'sort' . PS . $order . PS . $by . PS : '' );
		
		$adminUri  .= ( !empty( $order ) ? $order : '' );
		
		if ( !empty( $parameters ) )
		{
			$hasSite = $hasLang = $hasBlog = $hasSearch = false;
			
			if ( !empty( $site ) )
			{
				$adminUri .= '?site=' . $parameters['site'];
				$hasSite = true;
			}
			
			if ( !empty( $blog ) )
			{
				$adminUri .= ( $hasSite ? ';' : '?' ) . 'blog=' . $blog;
				$hasBlog = true;
			}
			
			if ( !empty( $lang ) )
			{
				$adminUri .= ( $hasSite || $hasBlog ? ';' : '?' ) . 'lang=' . $lang;
				$hasLang = true;
			}
			
			if ( !empty( $search ) )
			{
				$adminUri 			.= ( ( $hasSite || $hasLang || $hasBlog ) ? ';' : '?' ) . 'search=' . $search;
				$this->searchString = urlencode( $search );
				$hasSearch 			= true;
			}
			
			if ( !empty( $category ) )
			{
				$adminUri 		.= ( ( $hasSite || $hasLang || $hasBlog || $hasSearch ) ? ';' : '?' ) . 'cat=' . $category;
				$this->catId 	 = $category;
			}
		}
		
		return $adminUri;
	}
	
	#####################################################
	#
	# Set Current Admin Settings function
	#
	#####################################################
	public function SetAdminSettings()
	{
		global $Settings;
		
		$this->siteName = Settings::Site()['title'];
		$this->defaultSiteName = Settings::Site()['title'];
		
		$this->sites = $sites = $Settings::Sites();
		
		//Multisite is global, always check the main's site settings
		if ( $Settings::IsTrue( 'enable_multisite', 'site' ) )
			$this->multiSiteEnabled = true;
	
		//If we are browsing a child site, we need its settings into a different string
		if ( ( $this->siteID != SITE_ID ) )
		{
			if ( isset( $sites[$this->siteID] ) )
			{
				//Set this site's name
				$this->siteName = $sites[$this->siteID]['title'];

				//Load this site's settings
				$Settings = new Settings( $this->siteID );
				
				//Set the new settings
				$this->adminSettings = $Settings;

				$this->isChildSite = true;
			}
			
			else
			{
				@header('Location: ' . ADMIN_URI );
				exit;
			}
		}

		if ( $Settings::IsTrue( 'enable_multiblog', 'site' ) )
			$this->multiBlogEnabled = true;
			
		if ( $Settings::IsTrue( 'enable_multilang', 'site' ) )
			$this->multiLangEnabled = true;

		$this->siteUrl = $Settings::Site()['url'];
		
		$this->activeTheme = $Settings::Get()['theme'];
		
		$this->themes = $Settings::Themes();

		return $Settings;
	}
	
	#####################################################
	#
	# Set Current Admin Token function
	#
	#####################################################
	private function Token()
	{
		//Don't create a new hash key if there is an AJAX call
		if ( !is_null ( Router::GetVariable( 'slug' ) ) && ( ( Router::GetVariable( 'slug' ) == 'ajax-posts' ) || ( Router::GetVariable( 'slug' ) == 'ajax' ) ) )
			return;
		
		$this->hash = GenerateRandomKey( 10 );
		$_SESSION['token'] = $this->hash;
	}
	
	#####################################################
	#
	# Set the name and type of the blog function
	#
	#####################################################
	private function SetBlogData()
	{
		if ( !$this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) )
			return null;

		if ( $this->blogID > 0 )
		{
			$name = null;

			$Blogs = $this->adminSettings::BlogsFullArray();
			
			if ( !empty( $Blogs ) ) 
			{
				foreach( $Blogs as $bId => $bData )
				{
					if ( $bData['id_blog'] == $this->blogID )
					{
						$name = $bData['name'];
						break;
					}
				}
			}
		
			//Empty name means we don't have this blog
			$this->blogName = $name;
		}

		$data = Json( $this->adminSettings::Get()['extra_blogs'] );
		
		if ( !empty( $data ) )
		{
			if ( isset( $data['types']['store']['blogId'] ) )
			{
				$this->blogsData['hasStoreEnabled'] = true;
				$this->blogsData['storeId'] = $data['types']['store']['blogId'];
			}
			
			if ( isset( $data['types']['videos']['blogId'] ) )
			{
				$this->blogsData['hasVideosEnabled'] = true;
				$this->blogsData['videosId'] = $data['types']['videos']['blogId'];
			}
			
			if ( isset( $data['types']['coupons-and-deals']['blogId'] ) )
			{
				$this->blogsData['hasCouponsEnabled'] = true;
				$this->blogsData['couponsId'] = $data['types']['coupons-and-deals']['blogId'];
			}
			
			if ( isset( $data['types']['multivendor-marketplace']['blogId'] ) )
			{
				$this->blogsData['hasMarketplaceEnabled'] = true;
				$this->blogsData['marketplaceId'] = $data['types']['multivendor-marketplace']['blogId'];
			}
		}
	}
	
	#####################################################
	#
	# Set Admin Scheduled Tasks function
	#
	#####################################################
	private function AdminScheduledTasks()
	{
		//This array contains all the tasks scheduled by TOKICMS
		return array(
				'daily-maintenance' => array( 'function' => 'DailyMaintenance', 'name' => __ ( 'daily-maintenance' ) ),
				'fetch-tokicms-news' => array( 'function' => 'FetchTokiNews', 'name' => __ ( 'fetch-tokicms-news' ) ),
				'backup-db' => array( 'function' => 'BackupDB2', 'name' => __ ( 'backup-db' ) ),
		);
	}
	
	#####################################################
	#
	# Get Active Theme function
	#
	#####################################################
	public function ActiveTheme()
	{
		return $this->activeTheme;
	}
	
	#####################################################
	#
	# Get Site's Themes function
	#
	#####################################################
	public function SiteThemes()
	{
		return $this->themes;
	}
	
	#####################################################
	#
	# Add Admin Maintenance Task function
	#
	#####################################################
	public function AddMaintenanceTask( $taskID = null, $taskData = array() )
	{
		if ( empty( $taskID ) || empty( $taskData ) )
			return;
		
		if ( !isset( $this->maintenanceTasks[$taskID] ) )
			$this->maintenanceTasks[$taskID] = $taskData;
	}
	
	#####################################################
	#
	# Get Admin Maintenance Tasks function
	#
	#####################################################
	public function MaintenanceTasks()
	{
		return $this->maintenanceTasks;
	}
	
	#####################################################
	#
	# Get MultiSite Status function
	#
	#####################################################
	public function MultiSite()
	{
		return $this->multiSiteEnabled;
	}
	
	#####################################################
	#
	# Get Sites function
	#
	#####################################################
	public function Sites()
	{
		return $this->sites;
	}
	
	#####################################################
	#
	# Get Admin Maintenance Tasks function
	#
	#####################################################
	private function AdminMaintenanceTasks()
	{
		//This array contains all the maintenance tasks
		return array(
				'empty-file-cache' 		 => array( 'function' => 'EmptyCaches', 'name' => __ ( 'empty-file-cache' ) ),
				'recount-all-statistics' => array( 'function' => 'RecountStatistics', 'name' => __ ( 'recount-all-statistics' ) ),
				'optimize-db-tables' 	=> array( 'function' => 'OptimizeDB', 'name' => __ ( 'optimize-db-tables' ) ),
				'backup-db' 			=> array( 'function' => 'BackupDB', 'name' => __ ( 'backup-db' ) ),
				'get-database' 			=> array( 'function' => 'GetRemoteDB', 'name' => __ ( 'get-database' ) ),
				'push-database' 		=> array( 'function' => 'PushDatabase', 'name' => __ ( 'push-database' ) ),
				'pull-database' 		=> array( 'function' => 'PullDatabase', 'name' => __ ( 'pull-database' ) ),
				'sync-comments'			=> array( 'function' => 'SyncComments', 'name'	=> __( 'sync-comments' ) ),
				'export-comments'		=> array( 'function' => 'ExportComments', 'name'	=> __( 'export-comments' ) )
		);
	}
	
	#####################################################
	#
	# Build Vars For Admin function
	#
	#####################################################
	public function Variables()
	{
		return null;
	}

	#####################################################
	#
	# Get Admin Stats function
	#
	#####################################################
	public function Counts()
	{
		$cacheFile = CacheFileName( 'admin-dash-counts-userid_' . $this->UserID(), null, $this->langID, null, null, null, null, $this->siteID );
		
		if ( ValidOtherCache( $cacheFile, 1800 ) )
		{
			$arr = ReadCache( $cacheFile );
		}
		
		//Get the data and save it to the cache, if needed...
		else
		{
			
			$arr = array();
			
			$q = "(id_site = " . $this->siteID . ") AND (id_lang = " . $this->langID . ") AND (id_blog = " . $this->blogID . ")";

			//Count the posts
			$tmp = $this->db->from( null, 
			"SELECT count(id_post) as total
			FROM `" . DB_PREFIX . POSTS . "`
			WHERE " . $q . " AND (post_type = 'post')"
			)->total();

			$arr['postsCount'] = ( $tmp ? $tmp : 0 );
			
			//Count the pages
			$tmp = $this->db->from( null, 
			"SELECT count(id_post) as total
			FROM `" . DB_PREFIX . POSTS . "`
			WHERE " . $q . " AND (post_type = 'page')"
			)->total();

			$arr['pagesCount'] = ( $tmp ? $tmp : 0 );
			
			//Count the comments
			$tmp = $this->db->from( null, 
			"SELECT count(id) as total
			FROM `" . DB_PREFIX . "comments`
			WHERE (status = 'approved') AND (id_lang = " . $this->langID . ") AND (id_site = " . $this->siteID . ") AND (id_blog = " . $this->blogID . ")"
			)->total();
			
			$arr['commentsCount'] 	= ( $tmp ? $tmp : 0 );
			$arr['blogsCount'] 		= 0;
			$arr['langsCount'] 		= 1;
			
			//Count blogs
			if ( $this->multiBlogEnabled )
			{
				$tmp = $this->db->from( null, 
				"SELECT count(id_blog) as total
				FROM `" . DB_PREFIX . "blogs`
				WHERE (disabled = 0) AND (id_site = " . $this->siteID . ") AND "
				. ( ( $this->multiLangEnabled && ( $this->langID == $this->adminSettings::Lang()['id'] ) )
				? "(id_lang = " . $this->langID . " OR id_lang = 0)" : "(id_lang = " . $this->langID . ")" )
				)->total();

				$arr['blogsCount'] = ( $tmp ? $tmp : 0 );
			}

			//Count the langs
			if ( $this->multiLangEnabled )
			{
				$tmp = $this->db->from( null, 
				"SELECT count(id) as total
				FROM `" . DB_PREFIX . "languages`
				WHERE (status = 'active') AND (id_site = " . $this->siteID . ")"
				)->total();

				$arr['langsCount'] = ( $tmp ? $tmp : 0 );
			}
		}

		return $arr;
	}

	#####################################################
	#
	# Delete Settings Cache File
	#
	#####################################################
	public function DeleteSettingsCacheSite( $file = 'settings' )
	{
		DelCacheFiles( $file );
		
		//Don't forget to delete this site's caches
		if ( $this->isChildSite )
			$this->PingChildSite( 'clean-cache', $file );
	}
	
	#####################################################
	#
	# Delete Child's Data Cache File
	#
	#####################################################
	public function DeleteChildDataCacheSite( $file = 'permissions' )
	{
		if ( !$this->isChildSite )
			return;
		
		$this->PingChildSite( 'clean-datafile', null, $file );
	}
	
	#####################################################
	#
	# Empty Caches function
	#
	#####################################################
	public function EmptyCaches( $siteId = null )
	{
		if ( ( !$siteId && $this->isChildSite ) || ( $siteId && $siteId != SITE_ID ) )
		{
			$this->PingChildSite( 'clean-cache', 'all' );
		}
		
		DelCacheFiles( null, true );
	}
	
	#####################################################
	#
	# Delete Images function
	#
	#####################################################
	public function DeleteImages( $imageId, $siteId = null )
	{
		if ( ( !$siteId && $this->isChildSite ) || ( $siteId && $siteId != SITE_ID ) )
		{
			$this->PingChildSite( 'delete-images', null, $imageId );
		}
		
		DeleteImage( $imageId );
	}
	
	#####################################################
	#
	# Delete Single Post File Cache function
	#
	#####################################################
	public function DeleteFileCache( $postId, $siteId, $postKey = null, $langKey = null )
	{
		if ( $siteId == SITE_ID )
		{
			DeletePostCaches( $postKey, $langKey, $this->adminSettings::Get()['theme'] );
		}
		
		else
		{
			$this->PingChildSite( 'delete-post-cache', null, $postId, $siteId );
		}
	}
	
	#####################################################
	#
	# Set Admin Message Function
	#
	#####################################################
	public function SetAdminMessage( $message, $type = 'warning' )
	{
		$this->adminMessageType  = $type;
		$this->adminMessage 	.= $message;
	}
	
	#####################################################
	#
	# Set Admin Error Message Function
	#
	#####################################################
	public function SetErrorMessage( $message, $type = 'warning' )
	{
		if ( !isset( $_SESSION['admin_error_message'] ) )
			$_SESSION['admin_error_message'] = array();
		
		$arr = array(
			'type' 		=> $type,
			'message' 	=> $message
		);
		
		array_push( $_SESSION['admin_error_message'], $arr );
	}
	
	#####################################################
	#
	# Set Admin Site Function
	#
	#####################################################
	public function SetSite( $id )
	{
		if ( !is_numeric( $id ) )
			return;
		
		//Make sure we have this site
		$sites = Settings::Sites();
		
		if ( !isset( $sites[$id] ) )
			return;
		
		$this->siteID = $id;
	}
	
	#####################################################
	#
	# Get a site's URL Function
	#
	#####################################################
	public function GetSiteUrl( $id )
	{
		if ( !is_numeric( $id ) )
			return null;
		
		//Make sure we have this site
		$site = $this->db->from( null, 
		"SELECT url
		FROM `" . DB_PREFIX . "sites`
		WHERE (id = " . $id . ")"
		)->single();

		return ( $site ? $site['url'] : null );
	}
	
	#####################################################
	#
	# Get a site's Default Status Function
	#
	#####################################################
	public function GetSiteDefaultStatus( $id )
	{
		if ( !is_numeric( $id ) )
			return false;
		
		$site = $this->db->from( null, 
		"SELECT is_primary
		FROM `" . DB_PREFIX . "sites`
		WHERE (id = " . $id . ")"
		)->single();

		return ( $site ? $site['is_primary'] : null );
	}
	
	#####################################################
	#
	# Get Default Language Function
	#
	#####################################################
	public function GetDefaultLanguage( $siteId = null )
	{
		//If no multilingual, return the current ID
		if ( !$this->multiLangEnabled )
			return $this->langID;
		
		//If no site ID specified, return the current default lang
		if ( !$siteId )
			return $this->adminSettings::LangData()['lang']['id'];
		
		$l = $this->db->from( null, 
		"SELECT id
		FROM `" . DB_PREFIX . "languages`
		WHERE (id_site = " . (int) $siteId . ") AND (is_default = 1)"
		)->single();

		return ( $l ? $l['id'] : $this->adminSettings::LangData()['lang']['id'] );
	}
	
	#####################################################
	#
	# Set Child Site Status Function
	#
	#####################################################
	public function SetChildSite( $status = true )
	{
		if ( !is_bool( $status ) )
			return;
		
		$this->isChildSite = $status;
	}
	
	#####################################################
	#
	# Get Server Load function
	#
	#####################################################
	public function GetServerLoad()
	{
		$load = null;
		
		if ( !$this->isChildSite )
		{
			$load = GetServerLoad();
		}
		else
		{
			$load = $this->PingChildSite( 'server-load' );
			$load = ( !empty( $load['message'] ) ? $load['message'] : null );
		}

		return $load;
	}
	
	#####################################################
	#
	# Get Themes Positions function
	#
	#####################################################
	public function ThemePosition()
	{
		$pos = array();
		
		if ( !$this->isChildSite )
		{
			if ( !empty( ThemeValue( 'widget-position' ) ) )
			{
				$pos = ( isset( ThemeValue( 'widget-position' )['0'] ) ? ThemeValue( 'widget-position' )['0'] : ThemeValue( 'widget-position' ) );
			}
		}
		else
		{
			$temp = $this->PingChildSite( 'theme-position' );
			
			if ( $temp && isset( $temp['message'] ) && ( $temp['message'] == 'Success' ) && isset( $temp['data'] ) )
				$pos = Json( $temp['data'] );
		}
		
		return $pos;
	}

	#####################################################
	#
	# Get Site Dirs function
	#
	#####################################################
	public function ImageUpladDir( $siteId )
	{
		//Load the default settings
		$S = new Settings( SITE_ID, false );

		//There is no need to load the settings if the current site is the default
		if ( !$this->multiSiteEnabled || ( $siteId == SITE_ID ) )
		{
			return array(
				'html' => ( ( $S::Get()['images_html'] != '' ) ? $S::Get()['images_html'] : SITE_URL . 'uploads' . PS ),
				
				'root' => ( ( $S::Get()['images_root'] != '' ) ? $S::Get()['images_root'] : ROOT . 'uploads' . DS ),
				
				'share' => false
			);
		}

		//Continue and load the site's settings if needed
		$Settings = new Settings( $siteId, false );
		
		//This is not going to happen, but better safe than sorry
		if ( !$Settings )
		{
			return array(
				'html' => ( !empty( $S::Get()['images_html'] ) ? $S::Get()['images_html'] : $S::Site()['url'] . 'uploads' . PS ),
				
				'root' => ( !empty( $S::Get()['images_root'] ) ? $S::Get()['images_root'] : $S::Site()['url'] . 'uploads' . DS ),
				
				'share' => false
			);
		}

		$data = Json( $Settings::Site()['share_data'] );

		if ( empty( $data ) || !isset( $data['sync_uploads'] ) || !$data['sync_uploads'] )
		{
			return array(
				'html' => ( !empty( $Settings::Get()['images_html'] ) ? $Settings::Get()['images_html'] : $Settings::Site()['url'] . 'uploads' . PS ),
				
				'root' => ( !empty( $Settings::Get()['images_root'] ) ? $Settings::Get()['images_root'] : $Settings::Site()['url'] . 'uploads' . DS ),
				
				'share' => false
			);
		}

		return array(
				'html' => ( !empty( $S::Get()['images_html'] ) ? $S::Get()['images_html'] : $S::Site()['url'] . 'uploads' . PS ),
				'root' => ( !empty( $S::Get()['images_root'] ) ? $S::Get()['images_root'] : $S::Site()['url'] . 'uploads' . DS ),
				'share' => true,
		);
	}
	
	#####################################################
	#
	# Ping Child Site function
	#
	#####################################################
	public function PingChildSite( $action, $type = null, $key = null, $siteId = null, $url = null, $time = null )
	{
		//Don't waste time if multisite is disabled
		if ( !$this->multiSiteEnabled )
			return;

		if ( !$siteId && !$this->isChildSite )
			return;

		//Don't continue if this is the parent site
		if ( $siteId && ( $siteId == SITE_ID ) )
			return;
		
		$siteId = ( $siteId ? $siteId : $this->siteID );
		
		$site = $this->db->from( null, 
		"SELECT url, site_secret, site_ping_url, ping_slash
		FROM `" . DB_PREFIX . "sites`
		WHERE (id = " . (int) $siteId . ")"
		)->single();
		
		if ( !$site )
		{
			$this->SetAdminMessage( __( 'an-error-happened' ) );
			return;
		}

		$pingUrl  = ( !empty( $site['site_ping_url'] ) ? $site['site_ping_url'] : $site['url'] . $site['ping_slash'] . PS );
		
		$pingUrl .= '?token=' . $site['site_secret'] . '&action=' . $action;
		
		$pingUrl .= ( $type ? '&type=' . $type : '' );
		
		$pingUrl .= ( $key ? '&key=' . $key : '' );

		$pingUrl .= ( $url ? '&url=' . urlencode( $url ) : '' );
		
		$pingUrl .= ( $time ? '&time=' . $time : '' );
		
		return PingSite( $pingUrl );
	}
	
	#####################################################
	#
	# Update Posts Views function
	#
	# This function is being used to update the page views in the DB from a static file
	#
	#####################################################
	private function UpdatePostsViews()
	{
		//Keep pinging to dashboard only
		if ( $this->currentAction != 'dashboard' )
			return;
		
		if ( !$this->isChildSite )
		{
			UpdatePostsViews();
		}
		
		//Let's ping the site to update its stats
		else
		{
			$this->PingChildSite( 'update-views' );
		}
	}
	
	#####################################################
	#
	# Update Posts Views function
	#
	# This function is being used to update the page views in the DB from a static file
	#
	#####################################################
	private function UpdateSiteStats()
	{
		if ( !$this->adminSettings::IsTrue( 'enable_stats' ) )
			return;
		
		//Keep pinging to dashboard only
		if ( $this->currentAction != 'dashboard' )
			return;
		
		if ( !$this->isChildSite )
		{
			UpdateSiteStats();
		}
		
		//Let's ping the site to update its stats
		else
		{
			$this->PingChildSite( 'update-stats' );
		}
	}
	
	#####################################################
	#
	# Update Sitemaps function
	#
	#####################################################
	public function UpdateSitemaps()
	{
		if ( !$this->adminSettings::IsTrue( 'enable_seo' ) )
			return;
		
		if ( !$this->isChildSite )
		{
			BuildSitemap();
		}
		else
		{
			$this->PingChildSite( 'build-sitemap' );
		}
	}
	
	#####################################################
	#
	# Update Site's Ping Slash function
	#
	# This function is being used to insert into the DB the ping slash of the site and its update hash
	#
	#####################################################
	private function SetPingSlashAndUpdateHash()
	{
		//Keep pinging to dashboard only
		if ( $this->currentAction != 'dashboard' )
			return;
		
		//Check the ping slash and add it to the DB
		if ( empty( $this->adminSettings::Site()['ping_slash'] ) )
		{
			if ( !$this->isChildSite )
			{
				if ( !empty( PING_SLUG )  )
				{
					
					$this->db->update( "sites" )->where( 'id', $this->siteID )->set( "ping_slash", PING_SLUG );
				}
			}
			
			else
			{
				$this->PingChildSite( 'update-ping-slash' );
			}
			
			$this->DeleteSettingsCacheSite();
		}

		//Check the update hash and add it to the DB
		if ( empty( $this->adminSettings::Site()['update_hash'] ) )
		{
			if ( !$this->isChildSite )
			{
				if ( !empty( UPDATE_HASH )  )
				{
					$this->db->update( "sites" )->where( 'id', $this->siteID )->set( "update_hash", UPDATE_HASH );
				}
			}
			
			else
			{
				$this->PingChildSite( 'update-hash-update' );
			}
			
			$this->DeleteSettingsCacheSite();
		}
	}
	
	#####################################################
	#
	# Update Settings function (Generic)
	#
	# This function is being used to update the settings in the DB
	#
	#####################################################
	public function UpdateSettings( $array, $siteId = null )
	{
		if ( empty( $array ) )
			return;
		
		$db = db();
		
		$siteId = ( $siteId ? $siteId : $this->siteID );
		
		foreach ( $array as $key => $value )
		{
			$this->db->update( "config" )->where( 'id_site', $siteId )->where( 'variable', $key )->set( "value", $value );
		}
	}
}