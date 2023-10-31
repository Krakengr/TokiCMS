<?php defined('TOKICMS') or die('Hacking attempt...');

class Settings
{
    protected static $data;
	protected static $siteID;
	protected static $cacheFile;
	protected static $cache;
	protected static $imgFolder;

	function __construct( $siteID = null, $cache = true )
	{
		self::$siteID = ( !$siteID ? SITE_ID : $siteID );
		self::$cache = $cache;
		self::$cacheFile = CACHE_ROOT . 'settings' . '_site-' . self::$siteID . '-' . sha1 ( 'settings' . self::$siteID . CACHE_HASH ) . '.php';

		if ( $cache && file_exists( self::$cacheFile ) && filemtime( self::$cacheFile ) > ( time() - 86400 ) )
			self::$data = ReadCache( self::$cacheFile );
		
		else
			self::$data = self::Settings();
	}

	public static function Get()
	{
		return self::$data['settings'];
	}

	public static function IsTrue( $var, $sub = null )
	{
		if ( $sub )
			return ( ( isset( self::$data[$sub][$var] ) && ( self::$data[$sub][$var] == 'true' ) ) ? true : false );
		
		else
			return ( ( isset( self::$data['settings'][$var] ) && ( self::$data['settings'][$var] == 'true' ) ) ? true : false );
	}
	
	public static function ActiveTheme()
	{
		return self::$data['settings']['theme'];
	}
	
	public static function Site()
	{
		return self::$data['site'];
	}
	
	public static function Sites()
	{
		return self::$data['sites'];
	}
	
	public static function LogSettings()
	{
		return self::$data['json']['logSettings'];
	}
	
	public static function Lang( $config = false )
	{
		return ( $config ? self::$data['lang_extra'] : self::$data['lang'] );
	}
	
	public static function LangData()
	{
		return self::$data['langData'];
	}
	
	public static function Maintenance()
	{
		return self::$data['json']['maintenance'];
	}
	
	public static function RegisteredSlugs()
	{
		return self::$data['json']['registeredSlugs'];
	}

	public static function Json()
	{
		return self::$data['json'];
	}
	
	public static function BlogsArray()
	{
		return self::$data['blogsArray'];
	}
	
	public static function BlogsArrayByLang()
	{
		return self::$data['blogsArrayByLang'];
	}
	
	public static function BlogsFullArrayByLang()
	{
		return self::$data['blogsFullArrayByLang'];
	}
	
	public static function BlogsFullArrayById()
	{
		return self::$data['blogsFullArrayById'];
	}
	
	public static function BlogsFullArray()
	{
		return self::$data['blogsFullArray'];
	}
	
	public static function LangsArray()
	{
		return self::$data['langsArray'];
	}
	
	public static function VideoPlayer()
	{
		return self::$data['json']['videoPlayerSettings'];
	}
	
	public static function Amp()
	{
		return self::$data['json']['ampSettings'];
	}
	
	public static function ShortLinksSettings()
	{
		return ( isset( self::$data['json']['linkSettings']['short-link-settings'] ) ? self::$data['json']['linkSettings']['short-link-settings'] : array() );
	}
	
	public static function LinkSettings()
	{
		return self::$data['json']['linkSettings'];
	}
	
	public static function Embed()
	{
		return self::$data['json']['embedSettings'];
	}
	
	public static function PrivacySettings()
	{
		return self::$data['json']['privacySettings'];
	}
	
	public static function Seo()
	{
		return self::$data['json']['seoSettings'];
	}
	
	public static function Comments()
	{
		return self::$data['json']['commentSettings'];
	}
	
	public static function LegalPages()
	{
		return self::$data['json']['legalPages'];
	}
	
	public static function ApiKeys()
	{
		return self::$data['json']['apiKeys'];
	}
	
	public static function Robots()
	{
		return self::$data['json']['robotsSettings'];
	}
	
	public static function Sitemap()
	{
		return self::$data['json']['sitemapSettings'];
	}
	
	public static function Instantpage()
	{
		return self::$data['json']['instantpageSettings'];
	}
	
	public static function Plugins()
	{
		return self::$data['json']['pluginsSettings'];
	}
	
	public static function Video()
	{
		return self::$data['json']['videoSettings'];
	}
	
	public static function Themes()
	{
		return self::$data['json']['themesSettings'];
	}
	
	public static function SiteImage( $default = true )
	{
		return ( ( $default && isset( self::$data['settings']['siteImage']['default'] ) ) ? self::$data['settings']['default'] : self::$data['settings']['siteImage'] );
	}
	
	public static function SiteImgSrcSet()
	{
		return self::$data['settings']['siteSrcSet'];
	}
	
	public static function Schema()
	{
		return self::$data['json']['schemaSettings'];
	}
	
	public static function Trans()
	{
		return self::$data['json']['transSettings'];
	}
	
	public static function Langs()
	{
		return self::$data['langs'];
	}
	
	public static function OtherLangs()
	{
		return self::$data['otherLangs'];
	}
	
	public static function CustomTypes()
	{
		return self::$data['customTypes'];
	}
	
	public static function ChildCustomTypes()
	{
		return self::$data['childCustomTypes'];
	}
	
	public static function AllLangs()
	{
		return self::$data['allLangs'];
	}
	
	public static function AllLangsById()
	{
		return self::$data['allLangsById'];
	}
	
	private static function Settings()
	{
		$db = db();
		
		$s = array();

		//Query: config
		$data = $db->from( null, "SELECT * FROM `" . DB_PREFIX . "config` WHERE (id_site = " . self::$siteID . ")" 
		)->all();
		
		$regSlugs = RegisteredSlugs();

		//($hook = GetHook('get_settings_start')) ? eval($hook) : null;

		foreach( $data as $row )
		{
			$s['settings'][$row['variable']] = $row['value'];
		}
		
		self::$imgFolder = ( !empty( $s['settings']['images_html'] ) ? $s['settings']['images_html'] : SITE_URL . 'uploads' . PS );
		
		//Add the decoded JSON data for faster access
		$s['json'] = array(
			'ampSettings' 			=> Json( $s['settings']['amp_data'] ),
			'instantpageSettings' 	=> Json( $s['settings']['instantpage_settings'] ),
			'commentSettings' 		=> Json( $s['settings']['comments_data'] ),
			'seoSettings' 			=> Json( $s['settings']['seo_data'] ),
			'apiKeys' 				=> Json( $s['settings']['api_keys'] ),
			'robotsSettings' 		=> Json( $s['settings']['robots_data'] ),
			'sitemapSettings'		=> Json( $s['settings']['sitemap_data'] ),
			'pluginsSettings' 		=> Json( $s['settings']['plugins_data'] ),
			'videoSettings' 		=> Json( $s['settings']['video_data'] ),
			'themesSettings' 		=> Json( $s['settings']['themes_data'] ),
			'transSettings' 		=> Json( $s['settings']['trans_data'] ),
			'schemaSettings' 		=> Json( $s['settings']['schema_data'] ),
			'legalPages' 			=> Json( $s['settings']['legal_pages'] ),
			'privacySettings' 		=> Json( $s['settings']['privacy_settings'] ),
			'contactPage' 			=> Json( $s['settings']['contact_page'] ),
			'logSettings' 			=> Json( $s['settings']['log_settings'] ),
			'embedSettings' 		=> Json( $s['settings']['embedder_data'] ),
			'linkSettings' 			=> Json( $s['settings']['link_manager_options'] ),
			'videoPlayerSettings' 	=> Json( $s['settings']['video_settings'] )
		);

		//Get the site's settings (default)
		//Query: site
		$s['site'] = $db->from( null, "SELECT * FROM `" . DB_PREFIX . "sites` WHERE (id = " . self::$siteID . ")" 
		)->single();

		//Add the decoded JSON maintenance data for faster access
		$s['json']['maintenance'] = Json( $s['site']['maintenance_data'] );
		
		//Add the share data
		$s['json']['shareData'] = Json( $s['site']['share_data'] );
		
		//Set the default site
		$s['settings']['isDefaultSite'] = $s['site']['is_primary'];
		
		//Add the preview hash
		$s['settings']['previewHash'] = $s['site']['preview_hash'];
		
		//Add the decoded JSON site image data
		$s['settings']['siteImage'] = Json( $s['settings']['site_image'] );
		
		//Add the site image src-set data
		$s['settings']['siteSrcSet'] = self::SetSiteImgSrcSet( $s['settings']['siteImage'] );

		$s['langData'] = $s['langsArray'] = $s['allLangs'] = $s['allLangsById'] = $s['blogsArray'] = $s['blogsFullArray'] = $s['blogsFullArrayById'] = $s['blogsArrayByLang'] = $s['blogsFullArrayByLang'] = $s['otherLangs'] = $s['customTypes'] = $s['childCustomTypes'] = array();

		//Grab other sites
		//Query: sites
		$data = $db->from( null, "
		SELECT *
		FROM `" . DB_PREFIX . "sites`
		WHERE (id != " . self::$siteID . ") AND (disabled = 0)
		ORDER BY title ASC" 
		)->all();

		if ( $data )
		{
			foreach( $data as $row )
			{
				$s['sites'][$row['id']] = $row;
			}
		}
		else
			$s['sites'] = array();

		//Get the default language
		//Query: language
		$l = $db->from( null, 
		"SELECT * FROM `" . DB_PREFIX . "languages` WHERE (id_site = " . self::$siteID . ") AND (is_default = 1)" 
		)->single();

		if ( $l )
		{
			$s['lang'] = $l;
			
			//Get the default language's settings
			$s['lang_extra'] = $db->from( null, 
			"SELECT * FROM `" . DB_PREFIX . "languages_config` WHERE (id_lang = " . $l['id'] . ")" 
			)->single();

			$s['lang_extra']['social'] = ( !empty( $s['lang_extra']['social'] ) ? Json( $s['lang_extra']['social'] ) : array() );
			
			$s['langData'] = array( 'lang' => $s['lang'], 'settings' => $s['lang_extra'] );
			
			//Add its code to the array
			$s['langsArray'][] = $s['lang']['code'];
			
			//Add the lang code in the registered slugs
			$regSlugs[] = $s['lang']['code'];
		}
		
		//We need ALL the languages in a different way, so get all the languages
		$data = $db->from( null,
		"SELECT *
		FROM `" . DB_PREFIX . "languages`
		WHERE (id_site = " . self::$siteID . ") AND (status = 'active')
		ORDER BY code ASC" 
		)->all();

		foreach( $data as $row )
		{
			$l = $db->from( null, 
			"SELECT * FROM `" . DB_PREFIX . "languages_config` WHERE (id_lang = " . $row['id'] . ")" 
			)->single();

			//Add the social media here
			$l['social'] = ( !empty( $l['social'] ) ? Json( $l['social'] ) : array() );
			
			$s['allLangs'][$row['code']] = array( 'lang' => $row, 'data' => $l );
			$s['allLangsById'][$row['id']] = array( 'lang' => $row, 'settings' => $l );
		}

		//Grab Any other Language(s)
		$data = $db->from( null, 
		"SELECT *
		FROM `" . DB_PREFIX . "languages`
		WHERE (id_site = " . self::$siteID . ") AND (is_default = 0)
		ORDER BY code ASC" 
		)->all();

		if ( $data )
		{
			foreach( $data as $l )
			{
				//Get this language's data
				$lang_extra = $db->from( null, 
				"SELECT * FROM `" . DB_PREFIX . "languages_config` WHERE (id_lang = " . $l['id'] . ")" 
				)->single();

				$lang_extra['social'] = ( !empty( $lang_extra['social'] ) ? Json( $lang_extra['social'] ) : array() );
				
				//Fill the arrays with data
				$s['langs'][$l['id']] = array(
					'lang' => $l, 
					'settings' => $lang_extra
				);

				$s['otherLangs'][$l['code']] = array(
					'lang' => $l, 
					'settings' => $lang_extra
				);

				$s['langsArray'][] = $l['code'];
			}
		}
		else
			$s['langs'] = array();
		
		//Grab Custom Types
		$data = $db->from( null, 
		"SELECT sef, id_parent
		FROM `" . DB_PREFIX . "post_types`
		WHERE (id_site = " . self::$siteID . ")" 
		)->all();

		if ( $data )
		{
			foreach( $data as $c )
			{
				if ( empty( $c['id_parent'] ) )
				{
					$s['customTypes'][] = $c['sef'];
				}
				else
				{
					$s['childCustomTypes'][] = $c['sef'];
				}
				
				$regSlugs[] = $c['sef'];
			}
		}
		
		//Grab Blogs
		$data = $db->from( null, 
		"SELECT *
		FROM `" . DB_PREFIX . "blogs`
		WHERE (id_site = " . self::$siteID . ") AND (disabled = 0)" 
		)->all();

		if ( $data )
		{
			foreach( $data as $b )
			{
				$s['blogsArrayByLang'][$b['id_lang']][] = $b['sef'];
				$s['blogsFullArrayByLang'][$b['id_lang']][$b['sef']] = $b;
				$s['blogsArray'][] = $b['sef'];
				$s['blogsFullArray'][$b['sef']] = $b;
				$s['blogsFullArrayById'][$b['id_blog']] = $b;
				
				//Add the blog slug in registered slugs
				$regSlugs[] = $b['sef'];
			}
		}
		
		$s['json']['registeredSlugs'] = $regSlugs;

		RunHooks( 'settings_end' );

		if ( self::$cache )
			WriteCache( $s, self::$cacheFile );
		
		return $s;
	}
	
	//Return the Site Img Srcset
	private static function SetSiteImgSrcSet( $arr )
	{
		$array = array(
			'srcset' => '',
			'sizes' => '',
			'srcFull' => ''
		);
		
		if ( empty( $arr ) )
			return $array;
		
		$num = count( $arr );
	
		$set = $sizes = '';
		
		$coverFull = 'srcset="';

		$i = 0;
		
		$imageWidth = ( isset( $arr['default']['width'] ) ? $arr['default']['width'] : 0 );
	
		foreach( $arr as $_ar => $ar )
		{
			$i++;
			
			$coverFull 	.= $ar['url'] . ' ' . ( isset( $ar['width'] ) ? $ar['width'] : '0' ) . 'w';
			$set 		.= $ar['url'] . ' ' . ( isset( $ar['width'] ) ? $ar['width'] : '0' ) . 'w';
			
			if ( $i < $num )
			{
				$coverFull  .= ', ' . PHP_EOL;
				$set 		.= ', ' . PHP_EOL;
			}
		}
		
		if ( !empty( $imageWidth ) ) 
		{
			$coverFull .= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
			
			$sizes .=  '(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px';
		}

		if ( $num > 1 )
		{
			$i = 0;
			
			$set = $sizes = '';
		
			$coverFull = 'srcset="';
		
			foreach( $arr  as $_ar => $ar )
			{
				$i++;
				
				$coverFull 	.= $ar['url'] . ' ' . ( isset( $ar['width'] ) ? $ar['width'] : '0' ) . 'w';
				$set 		.= $ar['url'] . ' ' . ( isset( $ar['width'] ) ? $ar['width'] : '0' ) . 'w';
				
				if ( $i < $num )
				{
					$coverFull  .= ', ' . PHP_EOL;
					$set 		.= ', ' . PHP_EOL;
				}
			}
			
			if ( !empty( $imageWidth ) ) 
			{
				$coverFull .= '" sizes="(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px"';
				$sizes .= '(max-width: ' . $imageWidth . 'px) 100vw, ' . $imageWidth . 'px';
			}
		}

		return array(
			'srcset' => $set,
			'sizes' => $sizes,
			'srcFull' => $coverFull
		);
	}
	
	//Return the folder based on date
	private static function FolderUrlByDate( $date )
	{
		
		if ( !is_numeric( $date ) )
			$date = time();
	
		$y_letter = date( 'Y', $date );
		$m_letter = date( 'm', $date);

		return self::$imgFolder . $y_letter . PS . $m_letter . PS;
	}
}