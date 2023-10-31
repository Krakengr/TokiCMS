<?php defined('TOKICMS') or die('Hacking attempt...');

class Theme 
{
	private static $variables = [];
	
	public function __construct()
	{
		$lang = CurrentLang();
		
		//Set Site's information
		self::SetVariable( 'whereAmI', Router::WhereAmI() );
		self::SetVariable( 'lang', $lang );
		self::SetVariable( 'locale', $lang['lang']['locale'] );
		self::SetVariable( 'langDir', $lang['lang']['direction'] );
		self::SetVariable( 'siteName', $lang['data']['site_name'] );
		self::SetVariable( 'siteDescr', $lang['data']['site_description'] );
		self::SetVariable( 'siteSlogan', $lang['data']['site_slogan'] );
		self::SetVariable( 'pageNumber', Router::GetVariable( 'pageNum' ) );
		self::SetVariable( 'siteImage', $this->BuildSiteImage() );
		self::SetVariable( 'siteUrl', $this->BuildSiteUrl() );
		self::SetVariable( 'url', Router::GetVariable( 'url' ) );
		self::SetVariable( 'seoSettings', Settings::Seo() );
		self::SetVariable( 'enableRss', Settings::IsTrue( 'enable_rss' ) );
		
		//Continue setting the default vars
		self::SetVariable( 'noIndex', false );
		self::SetVariable( 'noFollow', false );
		self::SetVariable( 'noImage', false );
		self::SetVariable( 'noodp', false );
		self::SetVariable( 'noSnippet', false );
		self::SetVariable( 'noArchive', false );
		self::SetVariable( 'schemaIndex', 0 );
		self::SetVariable( 'userId', 0 );
		self::SetVariable( 'totalPages', 0 );
		self::SetVariable( 'totalItems', 0 );
		self::SetVariable( 'titleSeparator', '' );
		self::SetVariable( 'headerCode', '' );
		self::SetVariable( 'footerCode', '' );
		self::SetVariable( 'headerTitle', '' );
		self::SetVariable( 'headerDescr', '' );
		self::SetVariable( 'footerText', '' );
		self::SetVariable( 'customHeaderCode', '' );
		self::SetVariable( 'customFooterCode', '' );
		self::SetVariable( 'alternateCode', '' );
		self::SetVariable( 'postHeader', '' );
		self::SetVariable( 'ampCss', '' );
		self::SetVariable( 'schemaArray', array() );
		self::SetVariable( 'data', array() ); //Holds some needed values for the post, category, tag, author, etc...
	}

	//Some specific values must be filled manually
	public static function Build()
	{
		self::AlternateCode();
		self::CreateFooterCode();
		self::CreateFooterText();
		self::CreateHeaderDescr();
		self::CreateHeaderTitle();
		self::SetNoIndex();
		self::CreateHeaderCode();
	}
	
	###############################################################
	#
	# Create Header Text Function
	#
	###############################################################
	private static function CreateFooterText()
	{
		if ( Router::GetVariable( 'isAdmin' ) )
		{
			return;
		}
		
		$defaultLang = Settings::LangData();
		$currentLang = self::$variables['lang'];
		
		$text = ( !empty( $currentLang['data']['footer_text'] ) ? $currentLang['data']['footer_text'] : ( !empty( $defaultLang['settings']['footer_text'] ) ? $defaultLang['settings']['footer_text'] : null ) );
		
		if ( empty( $text ) )
		{
			return;
		}
		
		$search = array( '{{site-title}}', '{{site-slogan}}', '{{site-description}}', '{{site-url}}', '{{current-year}}', '{{copyright}}', '{{powered-by-toki-cms}}' );
		
		$replace = array( self::$variables['siteName'], self::$variables['siteSlogan'], self::$variables['siteDescr'], Router::GetVariable( 'siteRealUrl' ), date( 'Y', time() ), '&copy;', 'Powered by <a href="https://badtooth.studio/tokicms/" target="_blank" rel="noopener">Toki CMS</a>' );

		$text = trim( str_replace( $search, $replace, $text ) );
		
		self::$variables['footerText'] = StripContent( $text );
	}
	
	###############################################################
	#
	# Create Header Code Function
	#
	###############################################################
	private static function CreateHeaderCode()
	{
		//($hook = GetHook('in_header_start')) ? eval($hook) : null;
		
		if ( Router::GetVariable( 'isAdmin' ) )
		{
			global $Admin;
			
			self::$variables['headerCode'] = $Admin->HeaderCode();
			
			return;
		}
		
		if ( Router::NotFound() )
		{
			return;
		}
		
		$url = self::$variables['url'];
		
		$headerCode = '';
		
		$CurrentLang = self::$variables['lang'];
		
		if ( !Router::GetVariable( 'isAmp' ) )
		{
			$headerCode .= '
			<link href="' . TOOLS_HTML . 'theme_files/assets/frontend/css/blocks.css" rel="stylesheet" type="text/css" media="all" />' . PHP_EOL;
		}
		else
		{
			self::BuildAmpCss();
		}
		
		$headerCode .= '<!-- Primary Meta Tags -->' . PHP_EOL;
		
		if ( Router::GetVariable( 'isBrowsing' ) && ( self::$variables['pageNumber'] > 0 ) )
			$url .= 'page' . PS . self::$variables['pageNumber'] . PS;

		if ( Settings::IsTrue( 'search_engine_disallow' ) )
			$headerCode .= '<meta name="robots" content="noindex, nofollow" />' . PHP_EOL;
		else
		{
			if ( self::$variables['noIndex'] )
				$headerCode .= '<meta name="robots" content="noindex, follow" />' . PHP_EOL;
			
			else
			{
				$headerCode .= '<meta name="robots" content="index, follow" />' . PHP_EOL;
				$headerCode .= '<meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />' . 	PHP_EOL;
				$headerCode .= '<meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />' . PHP_EOL;
			}
		}
		
		if ( !empty( self::$variables['enableRss'] ) ) 
		{
			$headerCode .= '<link rel="alternate" type="application/rss+xml" title="' . htmlspecialchars( self::$variables['siteName'] ) . ' &raquo; Feed" href="' . Router::GetVariable( 'siteRealUrl' )  . 'feed' . PS . '" />' . PHP_EOL;

			if ( Router::GetVariable( 'isBlog' ) && !empty( self::$variables['data'] ) )
			{
				$headerCode .= '<link rel="alternate" type="application/rss+xml" title="' . htmlspecialchars( self::$variables['data']['blogName'] ) . ' &raquo; Feed" href="' . self::$variables['data']['blogUrl'] . 'feed' . PS . '" />' . PHP_EOL;
			}
		}
		
		if ( ( self::$variables['whereAmI'] == 'post' ) && !empty( self::$variables['data'] ) )
		{
			if ( StaticHomePage( false, self::$variables['data']['postId'] ) || ( !empty( self::$variables['data']['parentId'] ) && StaticHomePage( false, self::$variables['data']['parentId'] ) ) )
					$headerCode .= '<link rel="canonical" href="' . Router::GetVariable( 'siteRealUrl' ) . '" />' . PHP_EOL;
				
				else
					$headerCode .= '<link rel="canonical" href="' . self::$variables['data']['postUrl'] . '" />' . PHP_EOL;			
		}
		else
			$headerCode .= '<link rel="canonical" href="' . $url . '" />' . PHP_EOL;

		if ( Settings::IsTrue( 'enable_seo' ) )
		{
			$seoSettings = self::$variables['seoSettings'];
			
			if ( !empty( $seoSettings['google_site_verification'] ) )
				$headerCode .= html_entity_decode( htmlspecialchars_decode( $seoSettings['google_site_verification'] ) ) . PHP_EOL;
			
			if ( !empty( $seoSettings['msvalidate'] ) )
				$headerCode .= html_entity_decode( htmlspecialchars_decode( $seoSettings['msvalidate'] ) ) . PHP_EOL;
			
			if ( !empty( $seoSettings['yandex_verification'] ) )
				$headerCode .= html_entity_decode( htmlspecialchars_decode( $seoSettings['yandex_verification'] ) ) . PHP_EOL;
			
			if ( ( self::$variables['whereAmI'] == 'home' ) || ( self::$variables['whereAmI'] == 'category' ) || ( self::$variables['whereAmI'] == 'blog' ) || ( self::$variables['whereAmI'] == 'author' ) )
			{
				if ( self::PrevPage() )
					$headerCode .= '<link rel="prev" href="' . self::PrevPageUrl() . '" />' . PHP_EOL;
					
				if ( self::NextPage() )
					$headerCode .= '<link rel="next" href="' . self::NextPageUrl() . '" />' . PHP_EOL;
			}
			
			if ( ( self::$variables['whereAmI'] == 'post' ) )
			{
				$headerCode .= self::$variables['postHeader'];
			}

			//Add the social media code
			$headerCode .= self::FacebookCode( $url );
			$headerCode .= self::TwitterCode( $url );
			
			$headerCode .= PHP_EOL;
			
			//Add the Schema Data
			$headerCode .= self::Schema();
		}
		
		if ( Settings::Get()['referrer_policy'] != 'false' )
			$headerCode .= '<meta name="referrer" content="' . Settings::Get()['referrer_policy'] . '" />' . PHP_EOL;
			
			//TODO:
			//$this->headerCode .= '<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests;block-all-mixed-content">' . PHP_EOL;
		
		//Add the generator meta
		$headerCode .= '<meta name="generator" content="Toki CMS By BadTooth Studio" />' . PHP_EOL;
		
		//Add the site's icons
		$headerCode .= ThemeIcons( false );
		
		$headerCode .= self::$variables['alternateCode'] . PHP_EOL;
		
		if ( Settings::IsTrue( 'enable_honeypot' ) && !Router::GetVariable( 'isAmp' ) )
			$headerCode .= '<style>.ohhney{opacity: 0;position: absolute;top: 0;left: 0;height: 0;width: 0;z-index: -1;}</style>' . PHP_EOL;

		if ( Settings::IsTrue( 'allow_post_notifications' ) && ( self::$variables['whereAmI'] == 'post' ) && !Router::GetVariable( 'isAmp' ) )
			$headerCode .= '<style>.noselect {-webkit-touch-callout: none; /* iOS Safari */-webkit-user-select: none; /* Safari */-khtml-user-select: none; /* Konqueror HTML */-moz-user-select: none; /* Old versions of Firefox */-ms-user-select: none; /* Internet Explorer/Edge */user-select: none; /* currently supported by Chrome, Edge, Opera and Firefox */}.subscr-lazydev{padding:30px;display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.subscr-title{font-size:14px;font-weight:500}.subscr-desc{opacity:.6;margin-top:15px}.subscr-left{flex-basis:0%;flex-grow:1;max-width:100%;min-width:50px}.subscr-right{width:200px;margin-left:30px;text-align:center}.subscr-btn,.unsubscr-btn{display:block;text-align:center;padding:10px;border-radius:4px;background-color:#2C3E50;color:#fff;font-size:18px;margin-bottom:15px;cursor:pointer}.subscr-btn:hover{background:#26A65B}.unsubscr-btn:hover{background:#E74C3C}.subscr-info span{color:#e74c3c}@media screen and (max-width:590px){.subscr-lazydev{text-align:center;display:block}.subscr-right{width:100%;margin:15px 0 0 0}}#display_popup{font-size:20px;cursor:pointer}#popup_box{visibility:hidden;display:none;background:#fff;border:3px solid #666;width:50%;height:50%;position:fixed;left:35%;top:30%;box-shadow:0 0 10px 0 grey;font-family:helvetica}#popup_box #cancel_button{float:right;margin-top:4px;margin-bottom:4px;margin-right:5px;background-color:grey;border:none;color:#fff;padding:5px;border-radius:1000px;width:25px;border:1px solid #424242;box-shadow:0 0 10px 0 grey;cursor:pointer}#popup_box #info_text{padding:10px;clear:both;background-color:#fff;color:#6E6E6E}#popup_box #close_button{margin:0;padding:0;width:70px;height:30px;line-height:30px;font-size:16px;background-color:grey;color:#fff;border:none;margin-bottom:10px;border-radius:2px;cursor:pointer}</style>' . PHP_EOL;
		
		if ( !empty( Settings::Get()[ 'header_code' ] ) && !Router::GetVariable( 'isAmp' ) )
		{
			$header_code = Json( Settings::Get()[ 'header_code' ] );
			
			if ( !empty( $header_code ) && is_array( $header_code ) )
			{
				foreach( $header_code as $_h => $__h )
				{
					if ( 
						( $__h['language'] == 0 ) || 
						( ( $__h['language'] > 0 ) && ( $__h['language'] == $CurrentLang['lang']['id'] ) )
					)
					{
						$headerCode .= html_entity_decode( $__h[ 'code' ] ) . PHP_EOL;
					}
					
					unset( $_h, $__h );
				}
			}
			
			unset( $header_code );
		}
		
		if ( Settings::IsTrue( 'enable_auto_translate' ) && !Router::GetVariable( 'isAmp' ) )
		{
			$autoTransSettings = Json( Settings::Get()['auto_translate_settings'] );
			
			if ( !empty( $autoTransSettings ) )
			{
				$defaultLang = Settings::Lang();
				$currentLang = self::$variables['lang'];
				$currentCode = $currentLang['lang']['code'];
				
				if ( !empty( $autoTransSettings['auto_translate'] ) )
				{
					$auto_langs = ( !empty( $autoTransSettings['auto_langs'] ) ? Json( $autoTransSettings['auto_langs'] ) : null );
					
					if ( !empty( $auto_langs ) && isset( $auto_langs[$currentCode] ) )
					{
						$headerCode .= '
						<div id="google_translate_element2" style="display:none;"></div>
						<script type="text/javascript">
							function googleTranslateElementInit2() {
								new google.translate.TranslateElement({
									pageLanguage: \'' . $defaultLang['code'] . '\',
									autoDisplay: true
								}, \'google_translate_element2\');
							}
						</script>
						<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>';?>
						
						<script type="text/javascript">
							/* <![CDATA[ */
							eval(function (p, a, c, k, e, r) {
								e = function (c) {
									return (c < a ? '' : e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36))
								};
								if (!''.replace(/^/, String)) {
									while (c--) r[e(c)] = k[c] || e(c);
									k = [function (e) {
										return r[e]
									}];
									e = function () {
										return '\\w+'
									};
									c = 1
								}
								while (c--) if (k[c]) p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]);
								return p
							}('6 7(a,b){n{4(2.9){3 c=2.9("o");c.p(b,f,f);a.q(c)}g{3 c=2.r();a.s(\'t\'+b,c)}}u(e){}}6 h(a){4(a.8)a=a.8;4(a==\'\')v;3 b=a.w(\'|\')[1];3 c;3 d=2.x(\'y\');z(3 i=0;i<d.5;i++)4(d[i].A==\'B-C-D\')c=d[i];4(2.j(\'k\')==E||2.j(\'k\').l.5==0||c.5==0||c.l.5==0){F(6(){h(a)},G)}g{c.8=b;7(c,\'m\');7(c,\'m\')}}', 43, 43, '||document|var|if|length|function|GTranslateFireEvent|value|createEvent||||||true|else|doGTranslate||getElementById|google_translate_element2|innerHTML|change|try|HTMLEvents|initEvent|dispatchEvent|createEventObject|fireEvent|on|catch|return|split|getElementsByTagName|select|for|className|goog|te|combo|null|setTimeout|500'.split('|'), 0, {}))
							/* ]]> */
						</script>

				<?php
						$headerCode .= '<script type="text/javascript">doGTranslate("' . $defaultLang['code'] . '|' . $auto_langs[$currentCode]['code'] . '")</script>' . PHP_EOL;
					}
				}
				
				else
				{
					$checkedLangs = ( !empty( $autoTransSettings['checked_langs'] ) ? Json( $autoTransSettings['checked_langs'] ) : array() );
					
					if ( !empty( $checkedLangs ) )
					{
						$headerCode .= '
						<style>
						.google_translate_element{
							color:#fff !important;
							position:fixed;
							top:50px;
							left:5px;
							z-index:10000;
						}
						</style>' . PHP_EOL;
	
						$headerCode .= '
						<script type="text/javascript">
							function googleTranslateElementInit() {
								new google.translate.TranslateElement({
								  pageLanguage: \'' . $defaultLang['code'] . '\',
								  includedLanguages: \'' . implode(",", $checkedLangs) . '\',
								  layout: google.translate.TranslateElement.InlineLayout.SIMPLE
								}, \'google_translate_element\');
						  }
						</script>
						<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>' . PHP_EOL;
					}
				}
			}

			//TODO
			//Source: https://github.com/nofikoff/google-translate-js-in-one-click
			/*
			$headerCode .= '
			<style type="text/css">
			a.gflag {vertical-align: middle;font-size: 15px;padding: 0px;background-repeat: no-repeat;background-image: url("//gtranslate.net/flags/16.png");}a.gflag img {border: 0;}
			a.gflag:hover {background-image: url("//gtranslate.net/flags/16a.png");}#goog-gt-tt {display: none !important;}
			.goog-te-banner-frame {display: none !important;}.goog-te-menu-value:hover {text-decoration: none !important;}
			body {top: 0 !important;}#google_translate_element2 {display: none !important;}</style>';*/
		}
		
		if ( Settings::IsTrue( 'show_admin_bar' ) && ( IsAllowedTo( 'view-admin-bar' ) || IsAllowedTo( 'admin-site' ) ) && !Router::GetVariable( 'isAmp' ) )
			$headerCode .= '<style>body.show-admin-bar{margin-top:40px!important;position:relative}#admin_bar{height:40px;background-color:#333;position:fixed;top:0;left:0;width:100%;min-width:960px;font-family:Helvetica Neue,Helvetica,Arial,sans-serif;z-index:1000000;direction:ltr!important}#admin_bar,#admin_bar *{color:#fff;box-sizing:border-box}#admin_bar a{text-decoration:none;font-size:14px}#admin_bar ul{list-style:none}#admin_bar .admin-bar-container{display:block;width:100%;margin:0;padding:0 15px}#admin_bar .admin-bar-container:after,#admin_bar .admin-bar-container:before{content:"";display:table;clear:both}#admin_bar .admin-bar-logo{float:left;height:40px;margin-right:15px;padding:5px 0}#admin_bar .admin-bar-logo img{max-height:30px}#admin_bar .admin-navbar-nav{margin:0;padding:0 15px;float:left}#admin_bar .admin-navbar-nav li{position:relative;height:40px;line-height:40px}#admin_bar .admin-navbar-nav>li{float:left;margin:0 0 0 15px}#admin_bar .admin-navbar-nav>li:first-child{margin-left:0}#admin_bar .admin-navbar-nav>li>a{color:#ccc}#admin_bar .admin-navbar-nav>li>a:hover{color:#fff}#admin_bar .admin-navbar-nav .admin-bar-dropdown>a:after{content:"";border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #fff;display:inline-block;margin-left:5px;float:none}#admin_bar .admin-navbar-nav .admin-bar-dropdown:hover>.admin-bar-dropdown-menu{display:block}#admin_bar .admin-navbar-nav .admin-bar-dropdown-menu{position:absolute;top:100%;left:0;white-space:nowrap;margin:-1px 0 0;background-color:#fff;padding:5px 0;border-radius:0;z-index:1000;display:none;float:left;min-width:160px;list-style:none;font-size:14px;text-align:left;border:1px solid #ccc;box-shadow:0 6px 12px rgba(0,0,0,.175);background-clip:padding-box}#admin_bar .admin-navbar-nav .admin-bar-dropdown-menu:before{content:"";display:inline-block;border-left:5px solid transparent;border-right:5px solid transparent;border-bottom:8px solid #fff;position:absolute;left:3px;top:-8px}#admin_bar .admin-navbar-nav .admin-bar-dropdown-menu *{color:#333}#admin_bar .admin-navbar-nav .admin-bar-dropdown-menu li{line-height:1;padding:2px 0;height:auto}#admin_bar .admin-navbar-nav .admin-bar-dropdown-menu li a{display:block;padding:5px}#admin_bar .admin-navbar-nav .admin-bar-dropdown-menu li a:hover{background-color:#ccc}#admin_bar .admin-navbar-nav-right{float:right}#admin_bar .admin-navbar-nav-right .admin-bar-dropdown-menu{left:auto;right:0}#admin_bar .admin-navbar-nav-right .admin-bar-dropdown-menu:before{left:auto;right:3px}</style>' . PHP_EOL;
		
		if ( Settings::IsTrue( 'enable_reviews' ) && ( self::$variables['whereAmI'] == 'post' ) && !Router::GetVariable( 'isAmp' ) )
		{
			$headerCode .= '<link rel="stylesheet" href="' . TOOLS_HTML . 'jquery-bar-rating/dist/themes/css-stars.css">' . PHP_EOL;
			
			$headerCode .= '<style>.star-ratings h3 {font-size: 1.5em;line-height: 2;margin-top: 3em;color: #757575;}.star-ratings .stars .title {font-size: 14px;color: #cccccc;  line-height: 3;}.star-ratings .stars select {width: 120px;font-size: 16px;}.star-ratings .stars-main {float: left;}.start-ratings-main {margin-bottom: 3em;}@media print {.star-ratings h1 {  color: black;}.star-ratings .stars .title {color: black;}}.text-one-half,.text-two-third{width:100%;padding:15px}.text-one-half{width:48%}.text-one-third{width:30.66%}.text-two-third{width:65.33%}.text-one-fourth{width:22%}.text-three-fourth{width:74%}.text-one-fifth{width:16.8%}.text-two-fifth{width:37.6%}.text-three-fifth{width:58.4%}.text-four-fifth{width:79.2%}.text-one-sixth{width:13.33%}.text-five-sixth{width:82.67%}.text-one-half,.text-one-third,.text-two-third,.text-three-fourth,.text-one-fourth,.text-one-fifth,.text-two-fifth,.text-three-fifth,.text-four-fifth,.text-one-sixth,.text-five-sixth{position:relative;margin-right:4%;margin-bottom:5px;float:left}.text-column-last,.text-one-half:last-of-type,.text-one-third:last-of-type,.text-one-fourth:last-of-type,.text-one-fifth:last-of-type,.text-one-sixth:last-of-type{margin-right:0!important;clear:right}.lineheight20{font-size:15px}.padd20{padding:20px}.lightgreenbg{background-color:#eaf9e8}.lightredbg{background-color:#fff4f4}.mt15{margin-top:15px !important}.mb15{margin-bottom:15px !important}.font90{font-size:90%}.blackcolor{color:#111}.mb10{margin-bottom:10px !important}.blockstyle{display:block}.fontbold{font-weight:700}.font120{font-size:120%}.flowhidden{overflow:hidden}.user-rating{margin:0 auto 10px auto;}.user-rating .userstar.active{color:#ff8a00}.mb20{margin-bottom:20px !important}.h2-line{height:1px;background:rgba(206,206,206,.3);clear:both}.rv-flex-center-align{align-items:center;display:flex;flex-direction:row}.pl30{padding-left:30px !important}.pr30{padding-right:30px !important}.text-center{text-align:center}avrg-rating{margin-left:0;text-align:left}.orangecolor{color:#ff8a00}.font200{font-size:200%}.greycolor{color:grey}</style>' . PHP_EOL;
		}
		
		if ( Settings::IsTrue( 'enable_html5_video_player' ) && ( self::$variables['whereAmI'] == 'post' ) && !Router::GetVariable( 'isAmp' ) )
		{
			$headerCode .= '<link rel="stylesheet" href="https://cdn.plyr.io/3.7.2/plyr.css" />' . PHP_EOL;
		}
		
		if ( Settings::IsTrue( 'allow_favorite_posts' ) && ( self::$variables['whereAmI'] == 'post' ) && !Router::GetVariable( 'isAmp' ) )
			$headerCode .= '<style>span.favmod-add span {width: 156px;display: block;margin-left: 33px;padding-top: 4px;}span.favmod-unset span {width: 210px;display: block;margin-left: 33px;    padding-top: 4px;}span.faventry {margin: auto;position: absolute;left: 37%;margin-top: -50px;}.favmod {-webkit-box-sizing: content-box;-moz-box-sizing: content-box;box-sizing: content-box;  display: block;margin: 0 auto;width: 32px;height: 32px;padding: 4px;opacity: .5;}.favmod.active,.favmod:hover {opacity: 1;}.favmod.active:hover{opacity: .7;}.favmod-add,.favmod-unset {display: block;width: 100%;height: 100%;background: url(' . TOOLS_HTML . 'theme_files/assets/frontend/img/plus_fav.png);}.favmod-unset {background: url(' . TOOLS_HTML . 'theme_files/assets/frontend/img/minus_fav.png);}.favmod .favmod-unset,.favmod.active .favmod-add {display: none;}.favmod.active .favmod-unset {display: block;}.v_top_r a {margin-left: -25px;}</style>' . PHP_EOL;
		
		//($hook = GetHook('in_header_end')) ? eval($hook) : null;
		
		if ( !Router::GetVariable( 'isAdmin' ) && !Router::GetVariable( 'isAmp' ) )
		{
			$ads = GetAds( 'into-header' );
			
			if ( !empty( $ads ) )
			{
				foreach( $ads as $id => $ad ) 
				{
					$headerCode .= $ad['ad_code'] . PHP_EOL;
				}
				
			}
			
			unset( $ads );
		}
		
		//No code left behind
		$headerCode .= self::$variables['customHeaderCode'];
		
		self::$variables['headerCode'] = $headerCode;

		unset( $headerCode, $url );
	}

	###############################################################
	#
	# Create Header Title Function
	#
	###############################################################
	private static function CreateHeaderTitle()
	{
		//Admin theme title will be set in the controllers
		if ( Router::GetVariable( 'isAdmin' ) )
		{
			return;
		}
		
		$seoSettings = self::$variables['seoSettings'];
		
		$headerTitle = '';
		
		$whereAmI 	 = self::$variables['whereAmI'];

		//If we are on the homepage and have a page as frontpage, we should have the site's title, not the post's
		if ( ( Router::GetVariable( 'whereAmI' ) == 'post' ) && Router::GetVariable( 'isStaticHomePage' ) )
			$whereAmI = 'home';
		
		$pageNum = ( ( Router::GetVariable( 'isBrowsing' ) && ( self::$variables['pageNumber'] > 0 ) ) ? ' - ' . __( 'page' ) . ' ' . self::$variables['pageNumber'] : '' );
		
		if ( Router::NotFound() )
		{
			$lang = self::$variables['lang'];
			
			$title 	= __( 'page-not-found' );
			
			if ( !empty( $lang['data']['not_found_data'] ) )
			{
				$notFound = Json( $lang['data']['not_found_data'] );
				
				if ( !empty( $notFound['not_found_title'] ) )
				{
					$title 	= StripContent( $notFound['not_found_title'] );
				}		
			}

			$headerTitle = $title . ' | ' . self::$variables['siteName'];
		}
		
		//Maintenance enabled? Spread the word but only to guests
		elseif ( Settings::IsTrue( 'enable_maintenance', 'site' ) && ( empty( self::$variables['userId'] ) || !is_numeric( self::$variables['userId'] ) ) )
		{
			$maint = Json( Settings::Site()['maintenance_data'] );
			
			if ( isset( $maint['page_title'] ) && !empty( $maint['page_title'] ) )
				$headerTitle = $maint['page_title'];
			
			else
				$headerTitle = __( 'site-is-undergoing-maintenance' );
		}
		
		elseif ( Router::GetVariable( 'accessDenied' ) )
		{
			$headerTitle = __( 'access-denied' );
		}
		
		else
		{
			switch( $whereAmI )
			{
				case 'post':
				
					if ( !empty( self::$variables['data'] ) )
					{
						$time = ( !empty( self::$variables['data']['postDate'] ) ? self::$variables['data']['postDate'] : time() );
						
						$search = array( '{{post-title}}', '{{post-description}}', '{{post-date}}', '{{post-author}}', '{{post-id}}' );
						
						$replace = array( self::$variables['data']['postTitle'], self::$variables['data']['postDescription'], $time, self::$variables['data']['postAuthor'], self::$variables['data']['postId'] );
						
						if ( !self::$variables['data']['isPage'] )
						{
							if ( !empty( $seoSettings['posts_title_format'] ) )
							{
								$headerTitle = str_replace( $search, $replace, $seoSettings['posts_title_format'] );
							}
						}
						else
						{
							if ( !empty( $seoSettings['pages_title_format'] ) )
							{
								$headerTitle = str_replace( $search, $replace, $seoSettings['pages_title_format'] );
							}
						}
						
						if ( !$headerTitle )
							$headerTitle = self::$variables['data']['postTitle'] . ' | ' . self::$variables['siteName'];
					}
				break;
				
				case 'search':
					if ( !empty( $seoSettings['search_title_format'] ) )
					{
						$search = array( '{{search-term}}' );
						
						$replace = array( ( isset( $_POST['s'] ) ? htmlspecialchars( $_POST['s'] ) : '' ) );
						
						$headerTitle = str_replace( $search, $replace, $seoSettings['search_title_format'] );
					}
					else
						$headerTitle = __( 'search' ) . ' | ' . self::$variables['siteName'];
				break;
				
				case 'login':
					$headerTitle = __( 'login' ) . ' | ' . self::$variables['siteName'];
				break;
				
				case 'shortlink':
					$headerTitle = __( 'redirecting' ) . ' | ' . self::$variables['siteName'];
				break;
				
				case 'out':
					$headerTitle = __( 'redirecting' ) . ' | ' . self::$variables['siteName'];
				break;
				
				case 'customType':
					$headerTitle = self::$variables['data']['customTypeTitle'] . ' | ' . self::$variables['siteName'];
				break;
				
				case 'register':
					$headerTitle = __( 'register' ) . ' | ' . self::$variables['siteName'];
				break;
				
				case 'tag':
				
					$tName = self::$variables['data']['tagName'];
					$tDesc = self::$variables['data']['tagDescription'];
					
					if ( !empty( $seoSettings['tags_title_format'] ) )
					{
						$search = array( '{{tag-title}}', '{{tag-description}}', '{{page-num}}' );
						
						$replace = array( $tName, $tDesc, $pageNum );
						
						$headerTitle = str_replace( $search, $replace, $seoSettings['tags_title_format'] );
					}
					else
						$headerTitle = $tName . ' | ' . self::$variables['siteName'];
				break;
				
				case 'blog':

					$bName 	 = self::$variables['data']['blogName'];
					$bDescr  = self::$variables['data']['blogDescription'];
					$bSlogan = self::$variables['data']['blogSlogan'];

					if ( !empty( $seoSettings['blogs_title_format'] ) )
					{
						$search = array( '{{blog-name}}', '{{blog-slogan}}', '{{page-num}}', '{{blog-description}}'  );
						
						$replace = array( $bName, $bSlogan, $pageNum, $bDescr );
						
						$headerTitle = str_replace( $search, $replace, $seoSettings['blogs_title_format'] );
					}
					else
						$headerTitle = $bName . ' | ' . self::$variables['siteName'];
				break;
				
				case 'author':
					
					$uName = self::$variables['data']['userName'];
					
					if ( !empty( $seoSettings['authors_title_format'] ) )
					{
						$search = array( '{{author-name}}', '{{page-num}}' );
						
						$replace = array( $uName, $pageNum );
						
						$headerTitle = str_replace( $search, $replace, $seoSettings['authors_title_format'] );
					}
					else
						$headerTitle = StripContent( $uName ) . ' ' . __( 'archive' ) . ' | ' . self::$variables['siteName'];
				break;
				
				case 'category':

					$catName = ( Router::GetVariable( 'isSubCat' ) ? self::$variables['data']['subCategoryName'] : self::$variables['data']['categoryName'] );
					
					if ( !empty( $seoSettings['categories_title_format'] ) )
					{
						$search = array( '{{category-title}}', '{{page-num}}' );
						
						$replace = array( $catName, $pageNum );
						
						$headerTitle = str_replace( $search, $replace, $seoSettings['categories_title_format'] );
					}
					else
						$headerTitle = $catName . ' | ' . self::$variables['siteName'];
				break;

				default: //Home
					if ( !empty( $seoSettings['homepage_title_format'] ) )
					{
						$search = array( '{{site-title}}', '{{site-slogan}}', '{{site-description}}', '{{page-num}}' );
			
						$replace = array( self::$variables['siteName'], self::$variables['siteSlogan'], self::$variables['siteDescr'], $pageNum );

						$headerTitle = str_replace( $search, $replace, $seoSettings['homepage_title_format'] );
					}
					else
						$headerTitle = self::$variables['siteSlogan'];
				break;
			}
			
			$sep = self::TitleSeperator();

			$search = array( '{{site-title}}', '{{site-slogan}}', '{{site-description}}', '{{page-num}}', '{{sep}}', '{{items}}' );
		
			$replace = array( self::$variables['siteName'], self::$variables['siteSlogan'], self::$variables['siteDescr'], $pageNum, $sep[$seoSettings['title_seperator']]['code'], self::$variables['totalItems'] );
		
			$headerTitle = trim( str_replace( $search, $replace, $headerTitle ) );
			
			//Just in case pagenum still exists...
			$headerTitle = trim( str_replace( '{{page-num}}', '', $headerTitle ) );
		}

		self::$variables['headerTitle'] = trim( $headerTitle );
	}
	
	###############################################################
	#
	# Create Header Description Function
	#
	###############################################################
	private static function CreateHeaderDescr()
	{
		if ( Router::GetVariable( 'isAdmin' ) )
		{
			return;
		}
		
		$seoSettings = self::$variables['seoSettings'];
		
		$headerDescr = '';
		
		$whereAmI    = self::$variables['whereAmI'];
		
		if ( Router::NotFound() )
		{
			$headerDescr = __( 'page-not-found' );
		}
		
		//If we are on the homepage and have a page as frontpage, we should have the site's title, not the post's
		elseif ( ( $whereAmI == 'home' ) || ( ( $whereAmI == 'post' ) && Router::GetVariable( 'isStaticHomePage' ) ) )
		{
			if ( !empty( $seoSettings['homepage_meta_format'] ) )
			{
				$search = array( '{{site-title}}', '{{site-slogan}}', '{{site-description}}' );
		
				$replace = array( self::$variables['siteName'], self::$variables['siteSlogan'], self::$variables['siteDescr'] );

				$headerDescr = str_replace( $search, $replace, $seoSettings['homepage_meta_format'] );
			}
			else
			{
				$headerDescr = self::$variables['siteDescr'];
			}
		}
		
		elseif ( $whereAmI == 'category' )
		{
			$catName = ( Router::GetVariable( 'isSubCat' ) ? self::$variables['data']['subCategoryName'] : self::$variables['data']['categoryName'] );
			
			$catDescr  = ( Router::GetVariable( 'isSubCat' ) ? self::$variables['data']['subCategoryDescr'] : self::$variables['data']['categoryDescr'] );

			$catItems = self::$variables['totalItems'];
			
			$format = ( !empty( $catDescr ) ? $catDescr : ( !empty( $seoSettings['categories_meta_format'] ) ? $seoSettings['categories_meta_format'] : '' ) );
			
			$search = array( '{{category-title}}', '{{category-description}}', '{{post-count}}', '{{num-prices}}', '{{best-price}}' );// TODO
			$replace = array( $catName, $catDescr, $catItems, '', '' );
			
			$headerDescr = str_replace( $search, $replace, $format );
		}
			
		elseif ( $whereAmI == 'blog' )
		{
			$bName 	 = self::$variables['data']['blogName'];
			$bDescr  = self::$variables['data']['blogDescription'];
			$bSlogan = self::$variables['data']['blogSlogan'];

			if ( !empty( $seoSettings['blogs_meta_format'] ) )
			{
				$search = array( '{{blog-name}}', '{{blog-slogan}}', '{{blog-description}}' );

				$replace = array( $bName, $bSlogan, $bDescr );

				$headerDescr = str_replace( $search, $replace, $seoSettings['blogs_title_format'] );
			}
			else
				$headerDescr = $bDescr;
		}
		
		elseif ( $whereAmI == 'post' )
		{
			if ( !empty( self::$variables['data'] ) )
			{
				$headerDescr = self::$variables['data']['postDescription'];
			}
		}
		
		elseif ( $whereAmI == 'author' )
		{
			if ( !empty( self::$variables['data']['userName'] ) )
			{
				$headerDescr = StripContent( sprintf( __( 'search-posts-by-author' ), self::$variables['data']['userName'] ) );
			}
		}
		
		elseif ( $whereAmI == 'customType' )
		{
			if ( !empty( self::$variables['data']['customTypeDescription'] ) )
			{
				$headerTitle = self::$variables['data']['customTypeDescription'];
			}
		}

		//Add sitename to the description
		if ( empty( $headerDescr ) )
			$headerDescr = self::$variables['siteName'];
		
		if ( Router::GetVariable( 'accessDenied' ) )
		{
			$headerDescr = __( 'sorry-you-are-not-allowed-to-access-this-page' );
		}
		
		self::$variables['headerDescr'] = trim( $headerDescr );
		
		unset( $headerDescr );
	}
	
	###############################################################
	#
	# Create Footer Code Function
	#
	###############################################################
	private static function CreateFooterCode()
	{
		if ( Router::NotFound() || Router::GetVariable( 'isAdmin' ) )
		{
			return;
		}
		
		$seoSettings = self::$variables['seoSettings'];
		$CurrentLang = self::$variables['lang'];
		//$apiKeys 	 = Settings::ApiKeys();

		$footerCode = '';
		
		if ( Settings::IsTrue( 'load_jquery_cdn' ) )
			$footerCode .= '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" async></script>' . PHP_EOL;
		
		if ( Settings::IsTrue( 'enable_reviews' ) && ( self::$variables['whereAmI'] == 'post' ) )
		{
			$footerCode .= '<script type="text/javascript" src="' . TOOLS_HTML . 'jquery-bar-rating/dist/jquery.barrating.min.js" async></script>' . PHP_EOL;
			$footerCode .= '<script type="text/javascript">$(\'#rating-stars\').barrating({theme: \'css-stars\', showSelectedRating: false});</script>' . PHP_EOL;
		}

		if ( Settings::IsTrue( 'enable_cookie_concent' ) && !Router::GetVariable( 'isAdmin' ) )
		{
			$cookieData = Json( $CurrentLang['data']['cookie_data'] );
			
			$arr = array();
			
			if ( !empty( $cookieData ) )
			{
				$arr = array(
						'message' => $cookieData['consent_message'],
						'dismiss' => $cookieData['consent_dismiss'],
						'learnMore' => $cookieData['consent_more_txt'],
						'link' => $cookieData['consent_url'],
						'theme' => $cookieData['theme']
				
				);
				
				$footerCode .= '<!-- Begin Cookie Consent -->' . PHP_EOL;
				
				$footerCode .= '<script type="text/javascript">' . PHP_EOL;
				
				$footerCode .= 'window.cookieconsent_options = ' . json_encode( $arr, JSON_UNESCAPED_UNICODE ) . ';' . PHP_EOL;
				$footerCode .= '</script>' . PHP_EOL;

				$footerCode .= '<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js" async></script>' . PHP_EOL;
				
				$footerCode .= '<!-- End Cookie Consent -->' . PHP_EOL;
			}
			
			unset( $cookieData, $arr );
		}
		
		//Add Google Maps Code
		/*TODO
		if ( !empty( $apiKeys['gmaps'] ) && ( self::$variables['whereAmI'] == 'post' ) )
		{
			$footerCode .= '<div id="map"></div><script src="https://maps.googleapis.com/maps/api/js?key=' . $apiKeys['gmaps'] . '&callback=initMap&v=weekly" defer></script>' . PHP_EOL;
			
			$footerCode .= '<script type=\'text/javascript\'>
			function initMap() {
				const map = new google.maps.Map(document.getElementById("map"), {
					center: { lat: 00.00, lng: -00.00 },
					zoom: 8,
					mapTypeId: "satellite",
				});
				
				map.setTilt(45);
			}
			window.initMap = initMap;
			</script>' . PHP_EOL;
		}*/

		//Add Google Recaptcha code
		if ( ( Settings::Get()['enable_recaptcha'] != 'false' ) && !empty( Settings::Get()['recaptcha_site_key'] ) && !Router::GetVariable( 'isAdmin' ) )
		{
			//V2
			if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v2' )
			{
				$footerCode .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>' . PHP_EOL;
			}
			
			//V3
			else
			{
				$footerCode .= '<script src="https://www.google.com/recaptcha/api.js?render=' . Settings::Get()['recaptcha_site_key'] . '" async defer></script>' . PHP_EOL;
				
				$footerCode .= '<script type=\'text/javascript\'>
					grecaptcha.ready(() => {
						grecaptcha.execute(\'' . Settings::Get()['recaptcha_site_key'] . '\', { action: \'contact\' }).then(token => {
						  document.querySelector(\'#recaptchaResponse\').value = token;
						});
					});
				</script>' . PHP_EOL;
			}
			
		}
		
		if ( Settings::IsTrue( 'allow_post_notifications' ) && ( self::$variables['whereAmI'] == 'post' ) )
		{
			$UserId = self::$variables['userId'];
			
			if ( $UserId == 0 )
			{
				$footerCode .= '<script type=\'text/javascript\'>$(document).ready(function(){$("#display_popup").click(function(){showpopup();});$("#cancel_button").click(function(){hidepopup();}); $("#close_button").click(function(){hidepopup();});});function showpopup(){$("#popup_box").fadeToggle();$("#popup_box").css({"visibility":"visible","display":"block"});}function hidepopup(){$("#popup_box").fadeToggle();$("#popup_box").css({"visibility":"hidden","display":"none"});}</script>' . PHP_EOL;
				
				$footerCode .= '<script type=\'text/javascript\'>$(document).on(\'click\', \'.subscr-add\', function(e){
					e.preventDefault();
					var $this = $(this);
					var action = \'SubMod\';
					var email = $(\'#inputEmail\').val();
					var token = \'' . csrf::token() . '\';
					$.ajax({
						url: \'' . SITE_AJAX_URL . '\',
						type: \'POST\',
						dataType: \'json\',
						data: {postid: $this.data(\'id\'),token:token,action:action,email:email},
					})
					.done(function(json) {
						if (json[\'error\']) {
							alert(json[\'error\']);
						}
						
						if (json[\'added\']) {
							$("#subscr").hide();
							$("#unsubscr").show();
							$(".subscr-btn").hide();
							$(".unsubscr-btn").show();
							$(".subscr-btn").removeAttr("id");
							$(".unsubscr-btn").removeAttr("id");
							$(".unsubscr-btn").attr("id", "display_popup");
						}
						
						if (json[\'removed\']) {
							$("#subscr").show();
							$("#unsubscr").hide();
							$(".subscr-btn").show();
							$(".unsubscr-btn").hide();
							$(".subscr-btn").removeAttr("id");
							$(".unsubscr-btn").removeAttr("id");
							$(".subscr-btn").attr("id", "display_popup");
						}
					})
					.fail(function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					})
					.always(function() {hidepopup();});
				});</script>'. PHP_EOL;
			}
			else
			{
				$footerCode .= '<script type=\'text/javascript\'>$(document).on(\'click\', \'.subscr-add\', function(e){
					e.preventDefault();
					var $this = $(this);
					var action = \'SubMod\';
					var token = \'' . csrf::token() . '\';
					$.ajax({
						url: \'' . SITE_AJAX_URL . '\',
						type: \'POST\',
						dataType: \'json\',
						data: {postid: $this.data(\'id\'),token:token,action:action},
					})
					.done(function(json) {
						if (json[\'error\']) {
							alert(json[\'error\']);
						}
						
						if (json[\'added\']) {
							$(".subscr-btn").hide();
							$(".unsubscr-btn").show();
						}
						
						if (json[\'removed\']) {
							$(".subscr-btn").show();
							$(".unsubscr-btn").hide();
						}
					})
					.fail(function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					})
					.always(function() {});
				});</script>'. PHP_EOL;
			}
		}
		
		if ( Settings::IsTrue( 'allow_favorite_posts' ) && ( self::$variables['whereAmI'] == 'post' ) )
			$footerCode .= '<script type=\'text/javascript\'>$(document).on(\'click\', \'.favmod\', function(e){
				e.preventDefault();
				var $this = $(this);
				var action = \'FavMod\';
				var token = \'' . csrf::token() . '\';
				$.ajax({
					url: \'' . SITE_AJAX_URL . '\',
					type: \'POST\',
					dataType: \'json\',
					data: {postid: $this.data(\'id\'),token:token,action:action},
				})
				.done(function(json) {
					if (json[\'error\']) {
						alert(json[\'error\']);
					}
					
					if (json[\'success\']) {
						$this.toggleClass(\'active\');
					}
				})
				.fail(function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				})
				.always(function() {});
			});</script>'. PHP_EOL;
		
		if ( !empty( Settings::Get()[ 'footer_code' ] ) && !Router::GetVariable( 'isAdmin' ) )
		{
			$footer_code = Json( Settings::Get()[ 'footer_code' ] );
			
			if ( !empty( $footer_code ) && is_array( $footer_code ) )
			{
				foreach( $footer_code as $_h => $__h )
				{
					if ( 
						( $__h['language'] == 0 ) || 
						( ( $__h['language'] > 0 ) && ( $__h['language'] == $CurrentLang['lang']['id'] ) )
					)
					{
						$footerCode .= html_entity_decode( $__h[ 'code' ] ) . PHP_EOL;
					}
					
					unset( $_h, $__h );
				}
			}
			
			unset( $footer_code );
		}
		
		if ( Settings::IsTrue( 'enable_auto_translate' ) && !Router::GetVariable( 'isAmp' ) )
		{
			$autoTransSettings = Json( Settings::Get()['auto_translate_settings'] );
			
			if ( !empty( $autoTransSettings ) )
			{
				if ( !empty( $autoTransSettings['auto_translate'] ) )
				{
					//$footerCode .= '<div id="google_translate_element2" style="display:none;"></div>';
				}
				else
				{
					$checkedLangs = ( !empty( $autoTransSettings['checked_langs'] ) ? Json( $autoTransSettings['checked_langs'] ) : array() );
					
					if ( !empty( $checkedLangs ) )
					{
						$footerCode .= '<div id="google_translate_element" class="google_translate_element"></div>';
					}
				}
			}
		}

		if ( Settings::IsTrue( 'enable_html5_video_player' ) && ( self::$variables['whereAmI'] == 'post' ) && !Router::GetVariable( 'isAmp' ) )
		{
			$footerCode .= '<script src="https://cdn.plyr.io/3.7.8/plyr.js" async></script>' . PHP_EOL;
			$footerCode .= '<script>
			const player = new Plyr(\'video\');
			window.player = player;
			</script>' . PHP_EOL;
		}
		
		if ( Settings::IsTrue( 'show_admin_bar' ) )
			$footerCode .= self::ToolBar() . PHP_EOL;
		
		//This part of code is for the frontend only
		if ( !Router::GetVariable( 'isAdmin' ) )
		{
			if ( Settings::IsTrue( 'enable_lazyloader' ) )
				$footerCode .= '<script src="' . TOOLS_HTML . 'lazysizes-gh-pages/lazysizes.min.js" async></script>' . PHP_EOL;
		
			if ( Settings::IsTrue( 'enable_instantpage' ) )
			{
				$footerCode .= '<script src="' . TOOLS_HTML . 'quicklink/quicklink.js" async></script>' . PHP_EOL;
				$footerCode .= '<script>';
				
				//$footerCode .= 'document.body.setAttribute("data-instant-intensity", "mousedown' . ( Settings::Instantpage()['only_on_pc'] ? '-only' : '' ) . '");';
				
				$footerCode .= 'quicklink.listen();';

				$footerCode .= '</script>' . PHP_EOL;
			}

			if ( !empty( $seoSettings['tracking-codes']['google_analytics_ua'] ) )
			{
				$footerCode .= '<!-- Google Analytics Tag -->' . PHP_EOL;
				$footerCode .= '<script>' . PHP_EOL;
				$footerCode .= '(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
						  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
						  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
						  })(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');
			
						  ga(\'create\', \'' . $seoSettings['tracking-codes']['google_analytics_ua'] . '\', \'auto\');
						  ga(\'send\', \'pageview\');' . PHP_EOL;
			
				$footerCode .= '</script>' . PHP_EOL;
				$footerCode .= '<!-- End Google Analytics Tag -->' . PHP_EOL;
			}
		
			if ( !empty( $seoSettings['tracking-codes']['google_analytics_four'] ) )
			{
				$footerCode .= '<!-- Global site tag (gtag.js) - Google Analytics -->' . PHP_EOL;
				$footerCode .= '<script async src=\'https://www.googletagmanager.com/gtag/js?id=' . $seoSettings['tracking-codes']['google_analytics_four'] . '\'></script>' . PHP_EOL;
				
				$footerCode .= '
				<script>
					window.dataLayer = window.dataLayer || [];
					function gtag(){dataLayer.push(arguments);}
					gtag(\'js\', new Date());
					gtag(\'config\', \'' . $seoSettings['tracking-codes']['google_analytics_four'] . '\');' . PHP_EOL;
					
				$footerCode .= '</script>' . PHP_EOL;
				$footerCode .= '<!-- End Google Analytics (gtag.js) -->' . PHP_EOL;
			}
		
			if ( !empty( $seoSettings['tracking-codes']['facebook_pixel_id'] ) )
			{
				$footerCode .= '<!-- Facebook Pixel Code -->' . PHP_EOL;
				$footerCode .= '<script>' . PHP_EOL;
				$footerCode .= '!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
										n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
										n.push=n;n.loaded=!0;n.version=\'2.0\';n.queue=[];t=b.createElement(e);t.async=!0;
										t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
										document,\'script\',\'https://connect.facebook.net/en_US/fbevents.js\');
							
										fbq(\'init\', \'' . $seoSettings['tracking-codes']['facebook_pixel_id'] . '\');
										fbq(\'track\', \'PageView\');</script>
										<noscript><img height=\'1\' width=\'1\' style=\'display:none\'
										src=\'https://www.facebook.com/tr?id=' . $seoSettings['tracking-codes']['facebook_pixel_id'] . '&ev=PageView&noscript=1\'
										/></noscript>' . PHP_EOL;
				$footerCode .= '<!-- End Facebook Pixel Code -->' . PHP_EOL;
			}
		
			if ( !empty( $seoSettings['tracking-codes']['google_tag_manager_id'] ) )
			{
				$footerCode .= '<!-- Google Tag Manager -->' . PHP_EOL;
				$footerCode .= '<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
					new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
					j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
					\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
					})(window,document,\'script\',\'dataLayer\',\'' . $seoSettings['tracking-codes']['google_tag_manager_id'] . '\');</script>' . PHP_EOL;
				$tfooterCode .= '<!-- End Google Tag Manager -->' . PHP_EOL;
				
				$footerCode .= '<!-- Google Tag Manager (noscript) -->' . PHP_EOL;
				$footerCode .= '<noscript><iframe src=\'https://www.googletagmanager.com/ns.html?id=' . $seoSettings['tracking-codes']['google_tag_manager_id'] . '\' height=\'0\' width=\'0\' style=\'display:none;visibility:hidden\'></iframe></noscript>' . PHP_EOL;
				$footerCode .= '<!-- End Google Tag Manager (noscript) -->' . PHP_EOL;
			}
		
			$ads = GetAds( 'into-footer' );
			
			if ( !empty( $ads ) )
			{
				foreach( $ads as $id => $ad ) 
				{
					$footerCode .= $ad['ad_code'] . PHP_EOL;
				}
				
			}
			
			unset( $ads );
		}
		
		//No code left behind
		$footerCode .= self::$variables['customFooterCode'];

		self::$variables['footerCode'] = trim( $footerCode );
		
		unset( $footerCode );
		
		if ( Settings::IsTrue( 'enable_stats' ) && !Router::GetVariable( 'isAdmin' ) )
		{
			SimpleStats();
		}
	}
		
	###############################################################
	#
	# Schema Function
	#
	###############################################################
	private static function Schema()
	{
		if ( empty( Settings::Seo() ) || !isset( Settings::Seo()['enable_schema_markup'] ) || !Settings::Seo()['enable_schema_markup'] || Router::NotFound() )
			return '';
		
		$settings = Settings::Schema();
		
		if ( empty( $settings ) )
			return '';

		$social = SocialArray();
		$siteImage = SiteImage( true );
		
		$html = '<!-- Schemas Start -->' . PHP_EOL;

		$schemaContactPage = array();
		
		###############################################################
		#
		#
		# ContactPage Schema
		#
		#
		###############################################################
		if ( !empty( $settings['contact_page'] ) )
		{
			$page = Json( $settings['contact_page'] );
			
			if ( !empty( $page ) )
			{
				$schemaContactPage = array(
							'@context' => 'https://schema.org',
							'@type' => 'ContactPage',
							'url' => $page['url']
				);
				
				unset( $page );
			}
		}
		###############################################################
		#
		#
		# WebSite Schema
		#
		#
		###############################################################
		if ( isset( $settings['site_represents'] ) && ( $settings['site_represents'] != 'disable' ) )
		{
			$schemaWebSite = array(
							'@context' => 'https://schema.org',
							'@type' => 'WebSite',
							'@id' => self::$variables['siteUrl'] . '#website',
							'url' => self::$variables['siteUrl'],
							'inLanguage' => self::$variables['locale'],
							//'breadcrumb' => array( '@id' => $this->url . '#breadcrumb' ),
							'name' => htmlspecialchars( ( !empty( $settings['site_name'] ) ? $settings['site_name'] : self::$variables['siteName'] ) ),
							'alternateName' => htmlspecialchars( self::$variables['siteName'] ),
							'description' => htmlspecialchars( self::$variables['siteDescr'] ),
							'publisher' => array(
											'@context' => 'https://schema.org',
											'@type' => $settings['site_represents'],
											'@id' => self::$variables['siteUrl'] . '#' . strtolower( $settings['site_represents'] ),
											'name' => htmlspecialchars( $settings['site_name'] ),
											'url' => self::$variables['siteUrl']
							)
			);

			if ( !empty( $siteImage ) )
			{
				$schemaWebSite['creator']['logo'] = array(
										'@type' => 'ImageObject',
										'url' => $siteImage['url'],
										'width' => $siteImage['width'],
										'height' => $siteImage['height'],
										'@id' => self::$variables['siteUrl'] . '#logo'
				);
				
				$schemaWebSite['creator']['image'] = array(
										'@type' => 'ImageObject',
										'url' => $siteImage['url'],
										'width' => $siteImage['width'],
										'height' => $siteImage['height'],
										'@id' => self::$variables['siteUrl'] . '#logo'
				);
			}
			
			if ( !empty( $social ) )
			{
				$schemaWebSite['creator']['sameAs'] = array();
				
				foreach( $social as $id => $row )
				{
					$schemaWebSite['creator']['sameAs'][] = $row['url'];
				}
			}
		
			$schemaWebSite['potentialAction'] = array(
										'@type' => 'SearchAction',
										'target' => self::$variables['siteUrl'] . 'search' . PS . '{search_term_string}' . PS,
										'query-input' => 'required name=search_term_string'
			);
		}
		else
			$schemaWebSite = array();
		
		###############################################################
		#
		#
		# BreadcrumbList Schema
		#
		#
		###############################################################
		if ( isset( $settings['enable_breadcrumbs'] ) && !empty( $settings['enable_breadcrumbs'] ) )
		{
			$schemaBreadcrumb = array(
							'@context' => 'https://schema.org',
							'@type' => 'BreadcrumbList',
							//'@id' => self::$variables['siteUrl'] . '#breadcrumb',
							//'url' => self::$variables['siteUrl'],
							//'name' => 'Breadcrumb',
							'itemListElement' => array()
			);

			self::$variables['schemaArray'][] = array(
					'@type' => 'ListItem',
					'position' => self::$variables['schemaIndex'],
					'item' => array(
						//'@type' => 'WebPage',
						//'@id' => self::$variables['siteUrl'] . '#webpage',
						'@id' => self::$variables['siteUrl'],
						'name' => 'Home'
					)
			);
		}

		###############################################################
		#
		#
		# Main, Organization and WebPage Schema should be shown only on main pages
		#
		#
		###############################################################
		if ( ( self::$variables['whereAmI'] == 'home' ) || ( self::$variables['whereAmI'] == 'blog' ) && isset( $settings['site_represents'] ) && ( $settings['site_represents'] != 'disable' ) )
		{
			if ( self::$variables['whereAmI'] == 'home' )
			{
				###############################################################
				#
				#
				# Organization Schema
				#
				#
				###############################################################
				if ( !empty( $settings['organization_type'] ) )
				{
					$organizationMain = array(
									'@context' => 'https://schema.org',
									'@type' => $settings['organization_type'],
									'url' => self::$variables['siteUrl']
					);
					
					if ( !empty( $settings['contact_type'] ) && !empty( $settings['contact_number'] ) )
					{
						$organizationMain['contactPoint'] = array(
									'@type' => 'ContactPoint',
									'telephone' => $settings['contact_number'],
									'contactType' => $settings['contact_type']
						);
					}
				}
				else
					$organizationMain = array();
				
				###############################################################
				#
				#
				# Main Schema
				#
				#
				###############################################################
				$schemaMain = array(
								'@context' => 'https://schema.org',
								'@type' => $settings['site_represents'],
								'@id' => self::$variables['siteUrl'] . '#' . strtolower( $settings['site_represents'] ),
								'name' => htmlspecialchars( $settings['site_name'] ),
								'url' => self::$variables['siteUrl']
				);
				
				if ( !empty( $settings['contact_number'] ) )
				{
					$schemaMain['telephone'] = $settings['contact_number'];
				}
				
				if ( !empty( $siteImage ) )
				{
					$schemaMain['logo'] = array(
											'@type' => 'ImageObject',
											'url' => $siteImage['url'],
											'width' => $siteImage['width'],
											'height' => $siteImage['height'],
											'@id' => self::$variables['siteUrl'] . '#logo'
					);
				}
				
				if ( !empty( $social ) )
				{
					$schemaMain['sameAs'] = array();
					
					foreach( $social as $id => $row )
					{
						$schemaMain['sameAs'][] = $row['url'];
					}
				}
			}
			###############################################################
			#
			#
			# WebPage Schema
			#
			#
			###############################################################
			$schemaWebPage = array(
								'@context' => 'https://schema.org',
								'@type' => 'WebPage'
			);
			
			if ( !empty( $siteImage ) )
			{
				$schemaWebPage['image'] = array(
									'@type' => 'ImageObject',
									'url' => $siteImage['url'],
									'width' => $siteImage['width'],
									'height' => $siteImage['height']
				);
			}

			$schemaWebPage['name'] = htmlspecialchars( ( ( self::$variables['whereAmI'] == 'home' ) ? self::$variables['siteName'] : ( ( self::$variables['whereAmI'] == 'blog' ) ? self::$variables['data']['blogName'] : '' ) ) );

			$schemaWebPage['url'] = self::$variables['siteUrl'] . ( ( self::$variables['whereAmI'] == 'blog' ) ? self::$variables['data']['blogSef'] : '' );

			$schemaWebPage['description'] = htmlspecialchars( ( ( self::$variables['whereAmI'] == 'home' ) ? self::$variables['siteDescr'] : ( ( self::$variables['whereAmI'] == 'blog' ) ? self::$variables['data']['blogDescription'] : '' ) ) );
			
			$schemaWebPage['headline'] = htmlspecialchars( ( ( self::$variables['whereAmI'] == 'home' ) ? self::$variables['siteSlogan'] : ( ( self::$variables['whereAmI'] == 'blog' ) ? self::$variables['data']['blogSlogan'] : '' ) ) );

			$schemaWebPage['mainEntityOfPage'] = array(
							'@type' => 'WebPage',
							'@id' => ( ( self::$variables['whereAmI'] == 'blog' ) ? self::$variables['data']['blogUrl'] : self::$variables['siteUrl'] ) . '#webpage'
			);
			
			$schemaWebPage['publisher'] = array(
							'@type' => $settings['site_represents'],
							'@id' => self::$variables['siteUrl'] . '#' . strtolower( $settings['site_represents'] ),
							'name' => htmlspecialchars( $settings['site_name'] ),
							'url' => self::$variables['siteUrl'],
							'description' => htmlspecialchars( self::$variables['siteDescr'] )
			);
			
			if ( !empty( $siteImage ) )
			{
				$schemaWebPage['publisher']['logo'] = array(
									'@type' => 'ImageObject',
									'url' => $siteImage['url'],
									'width' => $siteImage['width'],
									'height' => $siteImage['height'],
									'@id' => self::$variables['siteUrl'] . '#logo'
				);
				
				$schemaWebPage['publisher']['image'] = array(
									'@type' => 'ImageObject',
									'url' => $siteImage['url'],
									'width' => $siteImage['width'],
									'height' => $siteImage['height'],
									'@id' => self::$variables['siteUrl'] . '#logo'
				);
			}
			
			if ( !empty( $social ) )
			{
				$schemaWebPage['publisher']['sameAs'] = array();
					
				foreach( $social as $id => $row )
				{
					$schemaWebPage['publisher']['sameAs'][] = $row['url'];
				}
			}

			$schemaWebPage['keywords'] = array();
			$schemaWebPage['primaryImageOfPage'] = array();
			
			$schemaWebPage['@id'] = ( ( self::$variables['whereAmI'] == 'blog' ) ? self::$variables['data']['blogUrl'] : self::$variables['siteUrl'] ) . '#webpage';

			if ( !empty( $social ) )
			{
				$schemaWebPage['sameAs'] = array();
				
				foreach( $social as $id => $row )
				{
					$schemaWebPage['sameAs'][] = $row['url'];
				}
			}
		}
		else
		{
			$schemaMain = array();
			$schemaWebPage = array();
		}
		
		###############################################################
		#
		#
		# Post/Page Schema
		#
		#
		###############################################################
		if ( self::$variables['whereAmI'] == 'post' )
		{
			$html .= self::PostSchema();
		}
		
		###############################################################
		#
		#
		# Add the breadcrumbs Schema
		#
		#
		###############################################################
		if ( $settings['enable_breadcrumbs'] )
		{
			$schemaBreadcrumb['itemListElement'] = array_values( self::$variables['schemaArray'] );
		
			$html .= '<script type="application/ld+json">' . json_encode( $schemaBreadcrumb, JSON_UNESCAPED_UNICODE ) . '</script>' . PHP_EOL;
		}
		
		if ( !empty( $schemaWebSite ) )
			$html .= '<script type="application/ld+json">' . json_encode( $schemaWebSite, JSON_UNESCAPED_UNICODE ) . '</script>' . PHP_EOL;
		
		if ( ( self::$variables['whereAmI'] == 'home' ) && !empty( $schemaMain ) )
		{
			$html .= '<script type="application/ld+json">' . json_encode( $schemaMain, JSON_UNESCAPED_UNICODE ) . '</script>' . PHP_EOL;
			
			unset( $schemaMain );
		}
		
		if ( ( self::$variables['whereAmI'] == 'home' ) || ( self::$variables['whereAmI'] == 'blog' ) && !empty( $schemaWebPage ) )
		{
			$html .= '<script type="application/ld+json">' . json_encode( $schemaWebPage, JSON_UNESCAPED_UNICODE ) . '</script>' . PHP_EOL;
			
			unset( $schemaWebPage );
		}
		
		if ( !empty( $schemaContactPage ) )
			$html .= '<script type="application/ld+json">' . json_encode( $schemaContactPage, JSON_UNESCAPED_UNICODE ) . '</script>' . PHP_EOL;
		
		if ( !empty( $organizationMain ) )
			$html .= '<script type="application/ld+json">' . json_encode( $organizationMain, JSON_UNESCAPED_UNICODE ) . '</script>' . PHP_EOL;
		
		unset( $schemaWebSite, $schemaBreadcrumb, $schemaMain, $schemaContactPage, $organizationMain );
		
		$html .= '<!-- Schemas END -->' . PHP_EOL;
		
		return $html;
	}
	
	###############################################################
	#
	# PostSchema Function
	#
	###############################################################
	private static function PostSchema()
	{
		$html = '';

		$schemaSettings = Settings::Schema();

		$breadcrumbData = $schemaSettings['breadcrumb-data'];
		
		$contactPage = ( !empty( $settings['contact_page'] ) ? Json( $settings['contact_page'] ) : null );
		
		//If we have enabled breadcrumbs, build the array
		//For now,$this works only in posts (generic)
		if ( $schemaSettings['enable_breadcrumbs'] && !empty( $breadcrumbData['breadcrumb_posts'] ) )
		{
			$postSettings = $breadcrumbData['breadcrumb_posts'];
			
			self::$variables['schemaIndex']++;
			
			//What do we want? The blog name maybe? First check if this is a post or the blog index page
			if ( ( $postSettings == 'blog' ) && Router::GetVariable( 'isBlog' ) )
			{
				//If this is a post, then the data is different
				if ( !empty( self::$variables['data']['blogUrl'] ) )
				{
					self::$variables['schemaArray'][] = array(
								'@type' => 'ListItem',
								'position' => self::$variables['schemaIndex'],
								'item' => array(
									'@id' => self::$variables['data']['blogUrl'],
									'name' => htmlspecialchars( self::$variables['data']['blogName'] )
								)
					);
				}
				
				elseif ( !empty( self::$variables['blogUrl'] ) )
				{
					self::$variables['schemaArray'][] = array(
								'@type' => 'ListItem',
								'position' => self::$variables['schemaIndex'],
								'item' => array(
									'@id' => self::$variables['blogUrl'],
									'name' => htmlspecialchars( self::$variables['blogName'] )
								)
					);
				}
			}
			
			//The category name?
			elseif ( ( $postSettings == 'category' ) && !self::$variables['data']['isPage'] )
			{
				self::$variables['schemaArray'][] = array(
								'@type' => 'ListItem',
								'position' => self::$variables['schemaIndex'],
								'item' => array(
									//'@type' => 'WebPage',
									//'@id' => $Post->CategoryUrl() . '#webpage',
									'@id' => self::$variables['data']['categoryUrl'],
									'name' => htmlspecialchars( self::$variables['data']['categoryName'] )
								)
					);
			}
			
			//the language?
			elseif ( $postSettings == 'language' )
			{
				//Don't add the default language if we want to hide it
				if ( Settings::IsTrue( 'hide_default_lang_slug' ) && ( self::$variables['data']['languageKey'] != Settings::Lang()['code'] ) )
				{
					self::$variables['schemaArray'][] = array(
								'@type' => 'ListItem',
								'position' => self::$variables['schemaIndex'],
								'item' => array(
									//'@type' => 'WebPage',
									//'@id' => Router::GetVariable( 'siteRealUrl' ) . '#webpage',
									'@id' => Router::GetVariable( 'siteRealUrl' ),
									'name' => htmlspecialchars( self::$variables['data']['languageName'] )
								)
					);
				}
			}
		}
		
		self::$variables['schemaIndex']++;
				
		//Add this item to schemaBreadcrumb
		self::$variables['schemaArray'][] = array(
							'@type' => 'ListItem',
							'position' => self::$variables['schemaIndex'],
							'item' => array(
								//'@type' => 'WebPage',
								//'@id' => $Post->PostUrl() . '#webpage',
								'@id' => self::$variables['data']['postUrl'],
								'name' => htmlspecialchars( self::$variables['data']['postTitle'] )
							)
		);
		
		//Continue with the Post's schema
		if ( !empty( self::$variables['data']['schemaCode'] ) )
		{
			$schemaP = self::$variables['data']['schemaCode'];
			
			if ( !empty( $schemaP ) && is_array( $schemaP ) )
			{
				foreach( $schemaP as $schema )
				{
					$html .= '<script type="application/ld+json">' . $schema . '</script>' . PHP_EOL;
				}
			}
		}

		return $html;
	}

	###############################################################
	#
	# ToolBar Function
	#
	###############################################################
	public static function ToolBar()
	{
		if ( !Settings::IsTrue( 'show_admin_bar' ) || ( !IsAllowedTo( 'view-admin-bar' ) && !IsAllowedTo( 'admin-site' ) ) )
			return;

		$html = '';

		$html .= '
		<nav id="admin_bar">
			<div class="admin-bar-container"><div class="admin-bar-logo"><a href="' . self::$variables['siteUrl'] . '" title="' . __( 'go-to-dashboard' ) . '">' . self::$variables['siteName'] . '</a></div>
				<ul class="admin-navbar-nav">
					<li class="admin-bar-dropdown"><a href="javascript:;" class="dropdown-toggle">' . __( 'administrator' ) . '</a>
						<ul class="admin-bar-dropdown-menu">
							<li><a href="' . ADMIN_URI . '">' . __( 'dashboard' ) . '</a></li>';
		
		if ( IsAllowedTo( 'admin-site' ) )
		{
			$html .= '
				<li><a href="' . ADMIN_URI . 'settings/">' . __( 'settings' ) . '</a></li>
				<li><a href="' . ADMIN_URI . 'performance/">' . __( 'performance' ) . '</a></li>
				<li><a href="' . ADMIN_URI . 'themes/">' . __( 'themes' ) . '</a></li>
				<li><a href="' . ADMIN_URI . 'plugins/">' . __( 'plugins' ) . '</a></li>
			';
		}
		
		$html .= '
			</ul>
		</li>';
		
		if ( IsAllowedTo( 'create-new-posts' ) || IsAllowedTo( 'manage-members' ) )
		{
			$html .= '<li class="admin-bar-dropdown"><a href="javascript:;" class="dropdown-toggle">' . __( 'new' ) . '</a>
				<ul class="admin-bar-dropdown-menu">';
		
			if ( IsAllowedTo( 'create-new-posts' ) )
				$html .= '
					<li><a href="' . ADMIN_URI . 'add-post/">' . __( 'post' ) . '</a></li>
					<li><a href="' . ADMIN_URI . 'add-page/">' . __( 'page' ) . '</a></li>';
			
			if ( IsAllowedTo( 'manage-members' ) )
				$html .= '<li><a href="' . ADMIN_URI . 'add-user/">' . __( 'user' ) . '</a></li>';
		
			$html .= '</ul></li>';
		}
		
		if ( self::$variables['whereAmI'] == 'post' )
		{
			$html .= '
			<li>
				<a href="' . ADMIN_URI . 'edit-post/id/' . self::$variables['data']['postId'] . '/">' . __( 'edit-this-post' ) . '</a>
			</li>';
		}
		
		$html .= '</ul>
		<ul class="admin-navbar-nav admin-navbar-nav-right">
		<li class="admin-bar-dropdown">
		<a href="#" class="dropdown-toggle">
		System Admin
		</a>
		<ul class="admin-bar-dropdown-menu">
		<li><a href="#">Profile</a></li>
		<li><a href="' . SITE_URL . 'logout/">' . __( 'logout' ) . '</a></li>
		</ul>
		</li>
		</ul>
		</div>
		</nav>';

		return $html;
	}
	
	public static function LangSelector()
	{
		if ( !MULTILANG )
			return '';
		
		$CurrentLang = CurrentLang();

		$langs = array();

		$html = '<select class="form-control inline-select" id="lang_select" name="lang_id">';
		
		$langList = Settings::AllLangs();

		if ( self::$variables['whereAmI'] == 'category' )
		{
			global $Category;
			
			if ( !empty( $Category ) && !empty( $Category['trans'] ) )
			{
				$catTrans = $Category['trans'];

				foreach ( $langList as $id => $la )
				{
					if ( isset( $catTrans[$id] ) )
					{
						$langs[$id] = $catTrans[$id]['url'];
						$html .= '<option value="' . $id . '" ' . ( ( $id == Settings::Lang()['code'] ) ? 'selected' : '' ) . '>' . $la['lang']['title'] . '</option>';
					}
					
					else
					{
						$langs[$id] = SITE_URL . $id . PS;
						$html .= '<option value="' . $id . '" ' . ( ( Router::GetVariable( 'isLang' ) && ( $la['lang']['id'] == Settings::Lang()['id'] ) ) ? 'selected' : '' ) . '>' . $la['lang']['title'] . '</option>';
					}
				}
			}
			else
			{
				foreach ( $langList as $id => $la )
				{
					$langs[$id] = SITE_URL . $id . PS;
						$html .= '<option value="' . $id . '" ' . ( ( $la['lang']['id'] == $Category['id_lang'] ) ? 'selected' : '' ) . '>' . $la['lang']['title'] . '</option>';
				}
			}
		}
		
		elseif ( self::$variables['whereAmI'] == 'post' )
		{
			global $Post;

			if ( $Post && !empty( $Post->Translations() ) )
			{
				$trans = $Post->Translations();

				foreach ( $langList as $id => $la )
				{
					if ( !empty( $trans ) && isset( $trans[$id] ) )
					{
						$langs[$id] = $trans[$id]['url'];
						$html .= '<option value="' . $id . '" ' . ( ( $la['lang']['id'] == $Post->Language()->id ) ? 'selected' : '' ) . '>' . $la['lang']['title'] . '</option>';
					}
					else
					{
						$langs[$id] = SITE_URL . $id . PS;
						$html .= '<option value="' . $id . '" ' . ( ( ( $la['lang']['id'] != $Post->Language()->id ) && Router::GetVariable( 'isLang' ) && ( $la['lang']['id'] == $CurrentLang['lang']['id'] ) ) ? 'selected' : '' ) . '>' . $la['lang']['title'] . '</option>';
					}
				}
			}
			else
			{
				foreach ( $langList as $id => $la )
				{
					$langs[$id] = SITE_URL . $id . PS;
						$html .= '<option value="' . $id . '" ' . ( ( $la['lang']['id'] == $Post->Language()->id ) ? 'selected' : '' ) . '>' . $la['lang']['title'] . '</option>';
				}
			}
		}
		else
		{
			//$langList string already contains the default language
			//However we need some work for the default language
			$langs[Settings::Lang()['code']] = SITE_URL . ( Settings::IsTrue( 'hide_default_lang_slug' ) ? '' : Settings::Lang()['code'] . PS );
			
			//The default lang is always selected if there is no other lang
			$html .= '<option value="' . Settings::Lang()['code'] . '" ' . ( !Router::GetVariable( 'isLang' ) ? 'selected' : '' ) . '>' . Settings::Lang()['title'] . '</option>';

			foreach ( $langList as $id => $la )
			{
				//Don't add the default lang twice
				if ( $id == Settings::Lang()['code'] )
					continue;
				
				$langs[$id] = SITE_URL . $id . PS;
				
				$html .= '<option value="' . $id . '" ' . ( ( Router::GetVariable( 'isLang' ) && ( $la['lang']['code'] == Router::GetVariable( 'langKey' ) ) ) ? 'selected' : '' ) . '>' . $la['lang']['title'] . '</option>';
			}
		}

		$html .= '</select>' . PHP_EOL;
				
		$html .= '<script type="text/javascript">' . PHP_EOL;
				
		$html .= 'var urls_langs = ' . json_encode( $langs, JSON_PRETTY_PRINT ) . ';' . PHP_EOL;
				
		$html .= 'document.getElementById( "lang_select" ).onchange = function() {' . PHP_EOL;
			
		$html .= 'location.href = urls_langs[this.value];' . PHP_EOL;
				
		$html .= '}</script>';

		return $html;
	}

	private function BuildSiteImage()
	{
		if ( Settings::IsTrue( 'blank_icon' ) )
			return 'data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=';
		
		$siteImage = SiteImage( true );
		
		if ( empty( $siteImage['url'] ) )
			return null;
		
		return $siteImage['url'];
	}
	
	public static function SiteUrl()
	{
		return self::$variables['siteUrl'];
	}
	
	public static function SiteImage()
	{
		return self::$variables['siteImage'];
	}
	
	public static function SiteSlogan()
	{
		return self::$variables['siteSlogan'];
	}
	
	public static function SiteName()
	{
		return self::$variables['siteName'];
	}
	
	public static function FooterCode()
	{
		return self::$variables['footerCode'];
	}
	
	public static function FooterText()
	{
		return self::$variables['footerText'];
	}
	
	public static function HeaderTitle()
	{
		return htmlspecialchars( self::$variables['headerTitle'] );
	}
	
	public static function Description()
	{
		return htmlspecialchars( self::$variables['headerDescr'] );
	}
	
	public static function HeaderCode()
	{
		return self::$variables['headerCode'];
	}
	
	public static function Locale()
	{
		if ( Router::GetVariable( 'isAdmin' ) )
		{
			global $Admin;
			
			return $Admin->Locale();
		}
		
		return self::$variables['locale'];
	}
	
	public static function LangDirection()
	{
		return self::$variables['langDir'];
	}

	public function AmpCss()
	{
		return self::$variables['ampCss'];
	}

	public static function GetData( $name )
	{
        return isset( self::$variables['data'][$name] ) ? self::$variables['data'][$name] : null;
    }
	
	public static function GetVariable( $name )
	{
        return isset( self::$variables[$name] ) ? self::$variables[$name] : null;
    }
	
	public static function SetVariable( $name, $value )
	{
        self::$variables[$name] = $value;
    }
	
	public static function AddVariable( $name, $value )
	{
        self::$variables[$name] .= $value;
    }
	
	public static function AddCustomCode( $code, $target = 'header' )
	{
		if ( $target = 'header' )
		{
			$header = self::$variables['customHeaderCode'];
			$header .= PHP_EOL . $code;
			self::$variables['customHeaderCode'] = $header;
		}
		
		else
		{
			$footer = self::$variables['customFooterCode'];
			$footer .= PHP_EOL . $code;
			self::$variables['customFooterCode'] = $footer;
		}
	}
	
	private static function TwitterCode( $url )
	{
		$html = '' . PHP_EOL;
		
		$html .= '<!-- Twitter -->' . PHP_EOL;
		$html .= '<meta property="twitter:url" content="' . $url . '" />' . PHP_EOL;
		$html .= '<meta property="twitter:description" content="' . htmlspecialchars( self::$variables['headerDescr'] ) . '" />' . PHP_EOL;
		
		$html .= '<meta property="twitter:title" content="' . htmlspecialchars( self::$variables['headerTitle'] ) . '" />' . PHP_EOL;
			
			
		if ( ( self::$variables['whereAmI'] != 'post' ) || empty( self::$variables['data'] ) )
			return $html;
	
		$html .= '<meta name="twitter:label1" content="Written by">' . PHP_EOL;
		$html .= '<meta name="twitter:data1" content="' . htmlspecialchars( self::$variables['data']['postAuthor'] ) . '">' . PHP_EOL;
		
		if ( !empty( self::$variables['data']['coverImage'] ) )
		{
			$html .= '<meta name="twitter:card" content="summary_large_image" />' . PHP_EOL;
			$html .= '<meta name="twitter:image" content="' . self::$variables['data']['coverImage']['imageUrl'] . '" />' . PHP_EOL;
		}
		else
			$html .= '<meta name="twitter:card" content="summary" />' . PHP_EOL;
		
		return $html;
	}
	
	private static function FacebookCode( $url )
	{
		$html = '';
		
		$html .= '<!-- Οpen Graph / Facebook -->' . PHP_EOL;
		$html .= '<meta property="og:locale" content="' . self::$variables['locale'] . '" />' . PHP_EOL;
		$html .= '<meta property="og:type" content="' . self::OgType() . '" />' . PHP_EOL;
		$html .= '<meta property="og:title" content="' . htmlspecialchars( self::$variables['headerTitle'] ) . '" />' . PHP_EOL;
		$html .= '<meta property="og:description" content="' . htmlspecialchars( self::$variables['headerDescr'] ) . '" />' . PHP_EOL;
		$html .= '<meta property="og:url" content="' . $url . '" />' . PHP_EOL;
		$html .= '<meta property="og:site_name" content="' . htmlspecialchars( self::$variables['siteName'] ) . '" />' . PHP_EOL;

		if ( ( self::$variables['whereAmI'] != 'post' ) || empty( self::$variables['data'] ) )
			return $html;
			
		if ( Settings::IsTrue( 'enable_seo' ) )
		{
			$seoSettings = self::$variables['seoSettings'];
			
			if ( isset( $seoSettings['facebook_profile'] ) && !empty( $seoSettings['facebook_profile'] ) )
			{
				$html .= '<meta property="article:publisher" content="' . $seoSettings['facebook_profile'] . '" />' . PHP_EOL;
			}
		}

		if ( !empty( self::$variables['data']['coverImage'] ) )
		{
			$img = self::$variables['data']['coverImage']['imageUrl'];
			
			$html .= '<meta property="og:image" content="' . $img . '" />' . PHP_EOL;
				
			if ( isset( $_SERVER["HTTPS"] ) && ( $_SERVER["HTTPS"] == 'on' ) && str_contains( $img, 'https' ) )
			{
				$html .= '<meta property="og:image:secure_url" content="' . $img . '" />' . PHP_EOL;
			}
				
			$html .= '<meta property="og:image:width" content="' . self::$variables['data']['coverImage']['imageWidth'] . '" />' . PHP_EOL;
			$html .= '<meta property="og:image:height" content="' . self::$variables['data']['coverImage']['imageHeight'] . '" />' . PHP_EOL;
			$html .= '<meta property="og:image:height" content="' . self::$variables['data']['coverImage']['imageHeight'] . '" />' . PHP_EOL;
		}

		return $html;
	}
	
	###############################################################
	#
	# Set Alternate Tag Function
	#
	###############################################################
	private static function alternateCode()
	{
		if ( !MULTILANG || Router::GetVariable( 'isAdmin' ) )
			return;
		
		//Set page number > 0 to avoid this code to be added on site.com/page/1/
		if ( Router::GetVariable( 'isBrowsing' ) && ( self::$variables['pageNumber'] > 0 ) )
			return;

		$html = '';
		
		if ( ( self::$variables['whereAmI'] == 'post' ) && !empty( self::$variables['data']['postTranslations'] ) )
		{
			$trans = self::$variables['data']['postTranslations'];

			foreach( $trans as $c => $t )
			{
				$html .= '<link rel="alternate" hreflang="' . $t['lang'] . '" href="' . $t['url'] . '" />' . PHP_EOL;
			}

			//Set the default value for the homepage
			if ( StaticHomePage( false, self::$variables['data']['postId'] ) )
			{
				$html .= '<link rel="alternate" hreflang="x-default" href="' . SITE_URL . '" />' . PHP_EOL;
			}
		}

		elseif 
		( 
			( self::$variables['whereAmI'] == 'category' )
			&& 
			(
				( Router::GetVariable( 'isSubCat' ) && !empty( self::$variables['data']['subTranslations'] ) )
				||
				( Router::GetVariable( 'isCat' ) && !empty( self::$variables['data']['catTranslations'] ) )
			)
		)
		{
			$trans = ( Router::GetVariable( 'isSubCat' ) ? self::$variables['data']['subTranslations'] : self::$variables['data']['catTranslations'] );

			foreach( $trans as $c => $t )
			{
				$html .= '<link rel="alternate" hreflang="' . $t['lang'] . '" href="' . rawurldecode( $t['url'] ) . '" />' . PHP_EOL;
			}
		}
		
		else
		{
			$langs = Settings::AllLangs();
		
			if ( empty( $langs ) )
				return;
			
			if ( MULTIBLOG && ( self::$variables['whereAmI'] == 'blog' ) )
			{
				$blogs = Settings::BlogsFullArray();
				$blogKey = Router::GetVariable( 'blogKey' );
			}

			$slugTrans = Settings::Trans();

			foreach( $langs as $key => $la )
			{
				$langUrl = SITE_URL . ( ( ( $key == Settings::Lang()['code'] ) &&  Settings::IsTrue( 'hide_default_lang_slug' ) ) ? '' : $la['lang']['code'] . PS );

				$_Url = null;

				if ( self::$variables['whereAmI'] == 'category' )
				{
					$_Url = $langUrl . str_replace( '/', '', Router::GetVariable( 'categorySlug' ) ) . PS . Router::GetVariable( 'categoryKey' ) . PS;
				}
						
				elseif ( self::$variables['whereAmI'] == 'home' )
				{
					$_Url = $langUrl;
				}
						
				elseif ( self::$variables['whereAmI'] == 'author' )
				{
					$_Url = $langUrl . 'author' . PS . Router::GetVariable( 'authorKey' ) . PS;
				}
						
				elseif ( self::$variables['whereAmI'] == 'tag' )
				{
					$_Url = $langUrl . str_replace( '/', '', Router::GetVariable( 'tagSlug' ) ) . PS . Router::GetVariable( 'tagKey' ) . PS;
				}
						
				elseif ( ( self::$variables['whereAmI'] == 'blog' ) && !empty( $blogs ) )
				{
					if ( isset( $blogs[$blogKey] ) && ( ( $blogs[$blogKey]['id_lang'] == '0' ) || ( $blogs[$blogKey]['id_lang'] == $la['lang']['id'] ) ) )
						$_Url = $langUrl . $blogKey . PS;
				}
						
				if ( $_Url )
					$html .= '<link rel="alternate" hreflang="' . $key . '" href="' . rawurldecode( $_Url ) . '" />' . PHP_EOL;
			}
				
			//Set the default value to the homepage
			//More info: https://developers.google.com/search/blog/2013/04/x-default-hreflang-for-international-pages
			if ( self::$variables['whereAmI'] == 'home' )
				$html .= '<link rel="alternate" hreflang="x-default" href="' . SITE_URL . '" />' . PHP_EOL;
		}
		
		self::$variables['alternateCode'] = $html;
	}
	
	###############################################################
	#
	# Title Seperator Function
	#
	###############################################################
	private static function TitleSeperator()
	{
		global $L;
		
		include ( ARRAYS_ROOT . 'seo-arrays.php');

		return $titleSeperatorArray;
	}
	
	###############################################################
	#
	# Set Header Title Function
	#
	###############################################################
	public static function SetHeaderTitle( $title = '' )
	{
		$sep = self::TitleSeperator();
		
		$titleSeparator = ( isset( $sep[Settings::Seo()['title_seperator']] ) ? $sep[Settings::Seo()['title_seperator']]['code'] : null );
		
		self::$variables['headerTitle'] = $title . ' ' . ( !empty( $titleSeparator ) ? $titleSeparator : '|' ) . ' ' . self::$variables['siteName'];
	}
	
	###############################################################
	#
	# OgType Function
	#
	###############################################################
	private static function OgType()
	{
		if ( self::$variables['whereAmI'] == 'blog' )
			$ogType = 'blog';
		
		elseif ( self::$variables['whereAmI'] == 'post' )
			$ogType = 'article';
		
		else 
			$ogType = 'website';
		
		return $ogType;
	}
	
	###############################################################
	#
	# Set No Index Function
	#
	###############################################################
	private static function SetNoIndex()
	{
		if ( Router::GetVariable( 'isAdmin' ) )
		{
			return;
		}
		
		if ( Router::NotFound() || Router::GetVariable( 'accessDenied' ) )
		{
			self::$variables['noIndex'] = true;
			return;
		}
		
		$seoSettings = Settings::Seo();

		if ( !Router::GetVariable( 'isAdmin' ) && Router::GetVariable( 'isBrowsing' ) && ( self::$variables['pageNumber'] > 0 ) && isset( $seoSettings['nofollow_tag_archive'] ) && IsTrue( $seoSettings['nofollow_tag_archive'] ) )
		{
			self::$variables['noIndex'] = true;
			return;
		}
			
		if ( ( self::$variables['whereAmI'] == 'category' ) && isset( $seoSettings['show_categories_search'] ) && IsFalse( $seoSettings['show_categories_search'] ) )
		{
			self::$variables['noIndex'] = true;
		}

		elseif ( ( self::$variables['whereAmI'] == 'blog' ) && isset( $seoSettings['show_blogs_search'] ) && IsFalse( $seoSettings['show_blogs_search'] ) )
		{
			self::$variables['noIndex'] = true;
		}
			
		elseif ( ( self::$variables['whereAmI'] == 'customType' ) && isset( $seoSettings['show_custom_post_types_search'] ) && IsFalse( $seoSettings['show_custom_post_types_search'] ) )
		{
			self::$variables['noIndex'] = true;
		}
			
		elseif ( ( self::$variables['whereAmI'] == 'search' ) || ( self::$variables['whereAmI'] == 'out' ) || ( self::$variables['whereAmI'] == 'login' ) || ( self::$variables['whereAmI'] == 'register' ) )
		{
			self::$variables['noIndex'] = true;
		}

		elseif ( ( self::$variables['whereAmI'] == 'author' ) && isset( $seoSettings['show_authors_search'] ) && IsFalse( $seoSettings['show_authors_search'] ) )
		{
			self::$variables['noIndex'] = true;
		}

		elseif ( ( self::$variables['whereAmI'] == 'tag' ) && isset( $seoSettings['show_tags_search'] ) && IsFalse( $seoSettings['show_tags_search'] ) )
		{
			self::$variables['noIndex'] = true;
		}
	}
	
	private static function NextPage()
	{
		if ( self::$variables['whereAmI'] == 'post' )
			return false;
		
		self::$variables['totalPages'] = ( ( self::$variables['totalItems'] > 0 ) ? (int) ceil( self::$variables['totalItems'] / HOMEPAGE_ITEMS ) : 0 );

		return ( ( ( self::$variables['totalPages'] > 1 ) && ( self::$variables['pageNumber'] < self::$variables['totalPages'] ) ) ? true : false );
	}
	
	private static function PrevPage()
	{
		if ( self::$variables['whereAmI'] == 'post' )
			return false;

		return ( ( self::$variables['pageNumber'] > 1 ) ? true : false );
	}
	
	private static function NextPageUrl()
	{
		if ( self::$variables['whereAmI'] == 'post' )
			return false;

		$url = self::$variables['url'];
		
		$url .= 'page' . PS . ( ( self::$variables['pageNumber'] < 2 ) ? 2 : ( self::$variables['pageNumber'] + 1 ) ) . PS;
		
		return $url;
	}
	
	private static function PrevPageUrl()
	{
		if ( self::$variables['whereAmI'] == 'post' )
			return false;

		$url = self::$variables['url'];

		$url .= ( ( self::$variables['pageNumber'] > 1 ) ? 'page' . PS . ( self::$variables['pageNumber'] - 1 ) . PS : '' );
		
		return $url;
	}
	
	private function BuildSiteUrl()
	{
		$siteUrl = SITE_URL;
		$lang = self::$variables['lang'];
		
		if ( !$lang['lang']['is_default'] || ( $lang['lang']['is_default'] && !Settings::IsTrue( 'hide_default_lang_slug' ) ) )
		{
			$siteUrl .= $lang['lang']['code'] . PS;
		}

		return $siteUrl;
	}
	
	private static function BuildAmpCss()
	{
		self::$variables['ampCss'] = @file_get_contents( TOOLS_HTML . 'theme_files/assets/frontend/css/blocks.css' ) . PHP_EOL;
		
		self::$variables['ampCss'] .= @file_get_contents( TOOLS_HTML . 'jquery-bar-rating/dist/themes/css-stars.css' ) . PHP_EOL;

		self::$variables['ampCss'] .= '.noselect {-webkit-touch-callout: none; /* iOS Safari */-webkit-user-select: none; /* Safari */-khtml-user-select: none; /* Konqueror HTML */-moz-user-select: none; /* Old versions of Firefox */-ms-user-select: none; /* Internet Explorer/Edge */user-select: none; /* currently supported by Chrome, Edge, Opera and Firefox */}.subscr-lazydev{padding:30px;display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.subscr-title{font-size:14px;font-weight:500}.subscr-desc{opacity:.6;margin-top:15px}.subscr-left{flex-basis:0%;flex-grow:1;max-width:100%;min-width:50px}.subscr-right{width:200px;margin-left:30px;text-align:center}.subscr-btn,.unsubscr-btn{display:block;text-align:center;padding:10px;border-radius:4px;background-color:#2C3E50;color:#fff;font-size:18px;margin-bottom:15px;cursor:pointer}.subscr-btn:hover{background:#26A65B}.unsubscr-btn:hover{background:#E74C3C}.subscr-info span{color:#e74c3c}@media screen and (max-width:590px){.subscr-lazydev{text-align:center;display:block}.subscr-right{width:100%;margin:15px 0 0 0}}#display_popup{font-size:20px;cursor:pointer}#popup_box{visibility:hidden;display:none;background:#fff;border:3px solid #666;width:50%;height:50%;position:fixed;left:35%;top:30%;box-shadow:0 0 10px 0 grey;font-family:helvetica}#popup_box #cancel_button{float:right;margin-top:4px;margin-bottom:4px;margin-right:5px;background-color:grey;border:none;color:#fff;padding:5px;border-radius:1000px;width:25px;border:1px solid #424242;box-shadow:0 0 10px 0 grey;cursor:pointer}#popup_box #info_text{padding:10px;clear:both;background-color:#fff;color:#6E6E6E}#popup_box #close_button{margin:0;padding:0;width:70px;height:30px;line-height:30px;font-size:16px;background-color:grey;color:#fff;border:none;margin-bottom:10px;border-radius:2px;cursor:pointer}' . PHP_EOL;
		
		self::$variables['ampCss'] .= '.star-ratings h3 {font-size: 1.5em;line-height: 2;margin-top: 3em;color: #757575;}.star-ratings .stars .title {font-size: 14px;color: #cccccc;  line-height: 3;}.star-ratings .stars select {width: 120px;font-size: 16px;}.star-ratings .stars-main {float: left;}.start-ratings-main {margin-bottom: 3em;}@media print {.star-ratings h1 {  color: black;}.star-ratings .stars .title {color: black;}}.text-one-half,.text-two-third{width:100%;padding:15px}.text-one-half{width:48%}.text-one-third{width:30.66%}.text-two-third{width:65.33%}.text-one-fourth{width:22%}.text-three-fourth{width:74%}.text-one-fifth{width:16.8%}.text-two-fifth{width:37.6%}.text-three-fifth{width:58.4%}.text-four-fifth{width:79.2%}.text-one-sixth{width:13.33%}.text-five-sixth{width:82.67%}.text-one-half,.text-one-third,.text-two-third,.text-three-fourth,.text-one-fourth,.text-one-fifth,.text-two-fifth,.text-three-fifth,.text-four-fifth,.text-one-sixth,.text-five-sixth{position:relative;margin-right:4%;margin-bottom:5px;float:left}.text-column-last,.text-one-half:last-of-type,.text-one-third:last-of-type,.text-one-fourth:last-of-type,.text-one-fifth:last-of-type,.text-one-sixth:last-of-type{margin-right:0!important;clear:right}.lineheight20{font-size:15px}.padd20{padding:20px}.lightgreenbg{background-color:#eaf9e8}.lightredbg{background-color:#fff4f4}.mt15{margin-top:15px !important}.mb15{margin-bottom:15px !important}.font90{font-size:90%}.blackcolor{color:#111}.mb10{margin-bottom:10px !important}.blockstyle{display:block}.fontbold{font-weight:700}.font120{font-size:120%}.flowhidden{overflow:hidden}.user-rating{margin:0 auto 10px auto;}.user-rating .userstar.active{color:#ff8a00}.mb20{margin-bottom:20px !important}.h2-line{height:1px;background:rgba(206,206,206,.3);clear:both}.rv-flex-center-align{align-items:center;display:flex;flex-direction:row}.pl30{padding-left:30px !important}.pr30{padding-right:30px !important}.text-center{text-align:center}avrg-rating{margin-left:0;text-align:left}.orangecolor{color:#ff8a00}.font200{font-size:200%}.greycolor{color:grey}' . PHP_EOL;
	}
}