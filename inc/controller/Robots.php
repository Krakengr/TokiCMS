<?php defined('TOKICMS') or die('Hacking attempt...');

class Robots extends Controller {
	
    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( !IsAllowedTo( 'view-site' ) )
		{
			//Don't include this file while on login or register
			if ( ( Router::WhereAmI() != 'login' ) || ( Router::WhereAmI() != 'register' ) )
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );

			$this->view();
			return;
		}
		
		if ( !Settings::IsTrue( 'enable_robots_txt' ) )
		{
			Router::SetNotFound();
			$this->view();
			return;
		}
		
		header('Content-Type:text/plain');
		
		echo $this->RobotsTxt(); 

		exit(0);
	}
	
	// Create the robots.txt file
	private function RobotsTxt()
	{
		$txt = '';

		$r = Settings::Robots();
		
		//return something
		if ( empty( $r ) )
		{
			if ( Settings::IsTrue( 'enable_sitemap' ) )
			{
				$txt .= 'Sitemap: ' . SITE_URL . 'sitemap_index.xml' . PHP_EOL;
			}
			
			return $txt;
		}

		if ( Settings::IsTrue( 'enable_sitemap' ) && $r['add_your_sitemap_in_the_robots_file'] )
		{
			$txt .= 'Sitemap: ' . SITE_URL . 'sitemap_index.xml' . PHP_EOL;
			
			//Don't forget the news sitemap
			if ( Settings::IsTrue( 'enable_news_sitemap' ) )
			{
				$langs = Settings::AllLangs();

				foreach ( $langs as $key => $lang )
				{
					$url = SITE_URL;
					
					if ( !$lang['lang']['is_default'] || ( $lang['lang']['is_default'] && !Settings::IsTrue( 'hide_default_lang_slug' ) ) )
					{
						$url .= $key . PS;
					}
					
					$txt .= 'Sitemap: ' . $url . 'sitemap-news.xml' . PHP_EOL;
				}
				
				unset( $langs );
			}
			
			$txt .= PHP_EOL;
		}
		
		$txt .= 'User-agent: *' . PHP_EOL;
		$txt .= 'Allow: /themes/' . PHP_EOL;
		$txt .= 'Allow: /inc/tools/' . PHP_EOL;
		
		$txt .= PHP_EOL;
		
		$txt .= 'Disallow: /login/' . PHP_EOL;
		$txt .= 'Disallow: /register/' . PHP_EOL;
		$txt .= 'Disallow: /forgot-password/' . PHP_EOL;
		$txt .= 'Disallow: /api/' . PHP_EOL;
		$txt .= 'Disallow: /license.txt' . PHP_EOL;
		$txt .= 'Disallow: /add-comment/' . PHP_EOL;
		$txt .= 'Disallow: /*?*' . PHP_EOL;
		$txt .= 'Disallow: /*?' . PHP_EOL;
		
		$txt .= PHP_EOL;
		
		if ( $r['show_google_bot_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: Googlebot' . PHP_EOL;
			$txt .= $r['show_google_bot_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_google_images_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: Googlebot-Image' . PHP_EOL;
			$txt .= $r['show_google_images_in_robots'] . ': /uploads/' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_media_partners_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: Mediapartners-Google' . PHP_EOL;
			$txt .= $r['show_media_partners_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_google_adsbot_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: AdsBot-Google' . PHP_EOL;
			$txt .= $r['show_google_adsbot_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_google_mobile_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: AdsBot-Google-Mobile' . PHP_EOL;
			$txt .= $r['show_google_mobile_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_bing_bot_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: Bingbot' . PHP_EOL;
			$txt .= $r['show_bing_bot_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_msn_bot_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: Msnbot' . PHP_EOL;
			$txt .= $r['show_msn_bot_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_msn_bot_media_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: msnbot-media' . PHP_EOL;
			$txt .= $r['show_msn_bot_media_in_robots'] . ': /uploads/' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_apple_bot_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: Applebot' . PHP_EOL;
			$txt .= $r['show_apple_bot_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_yandex_bot_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: Yandex' . PHP_EOL;
			$txt .= $r['show_yandex_bot_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_yandex_images_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: YandexImages' . PHP_EOL;
			$txt .= $r['show_yandex_images_in_robots'] . ': /uploads/' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_yahoo_search_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: Slurp' . PHP_EOL;
			$txt .= $r['show_yahoo_search_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_duckduckgo_search_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: DuckDuckBot' . PHP_EOL;
			$txt .= $r['show_duckduckgo_search_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_qwant_in_robots'] !== 'disable' )
		{
			$txt .= 'User-agent: Qwantify' . PHP_EOL;
			$txt .= $r['show_qwant_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}

		if ( $r['show_chinese_search_engines_in_robots'] !== 'disable' )
		{
			$txt .= '# Popular chinese search engines' . PHP_EOL;
			
			$txt .= 'User-agent: Baiduspider' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: Baiduspider/2.0' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: Baiduspider-video' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: Baiduspider-image' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: Sogou spider' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: Sogou web spider' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: Sosospider' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: Sosospider+' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: Sosospider/2.0' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: yodao' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: youdao' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: YoudaoBot' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= 'User-agent: YoudaoBot/1.0' . PHP_EOL;
			$txt .= $r['show_chinese_search_engines_in_robots'] . ': /' . PHP_EOL;
			
			$txt .= PHP_EOL;
			$txt .= '# Popular chinese search engines End' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_facebook_in_robots'] !== 'disable' )
		{
			$txt .= '# Facebook, Instagram, Whatsapp Crawling' . PHP_EOL;

			$txt .= 'User-agent: facebookexternalhit/1.0' . PHP_EOL;
			$txt .= $r['show_facebook_in_robots'] . ': /' . PHP_EOL;
			$txt .= 'User-agent: facebookexternalhit/1.1' . PHP_EOL;
			$txt .= $r['show_facebook_in_robots'] . ': /' . PHP_EOL;
			$txt .= 'User-agent: facebookplatform/1.0' . PHP_EOL;
			$txt .= $r['show_facebook_in_robots'] . ': /' . PHP_EOL;
			$txt .= 'User-agent: Facebot/1.0' . PHP_EOL;
			$txt .= $r['show_facebook_in_robots'] . ': /' . PHP_EOL;
			$txt .= 'User-agent: Visionutils/0.2' . PHP_EOL;
			$txt .= $r['show_facebook_in_robots'] . ': /' . PHP_EOL;
			$txt .= 'User-agent: datagnionbot' . PHP_EOL;
			$txt .= $r['show_facebook_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_twitter_in_robots'] !== 'disable' )
		{
			$txt .= '# Twitter Crawling' . PHP_EOL;
			
			$txt .= 'User-agent: Twitterbot' . PHP_EOL;
			$txt .= $r['show_twitter_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_linkedin_in_robots'] !== 'disable' )
		{
			$txt .= '# Linkedin Crawling' . PHP_EOL;
			
			$txt .= 'User-agent: LinkedInBot/1.0' . PHP_EOL;
			$txt .= $r['show_linkedin_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['show_pinterest_in_robots'] !== 'disable' )
		{
			$txt .= '# Pinterest Crawling' . PHP_EOL;
			
			$txt .= 'User-agent: Pinterest/0.1' . PHP_EOL;
			$txt .= $r['show_pinterest_in_robots'] . ': /' . PHP_EOL;
			$txt .= 'User-agent: Pinterest/0.2' . PHP_EOL;
			$txt .= $r['show_pinterest_in_robots'] . ': /' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['add_spam_backlink_blocker_in_the_robots_file'] )
		{
			$txt .= '# Spam Backlink Blocker' . PHP_EOL;
			$txt .= PHP_EOL;

			$txt .= 'Disallow: /feed/' . PHP_EOL;
			$txt .= 'Disallow: /feed/$' . PHP_EOL;
			$txt .= 'Disallow: */feed' . PHP_EOL;
			$txt .= 'Disallow: */feed$' . PHP_EOL;
			$txt .= 'Disallow: */author/*' . PHP_EOL;
			$txt .= 'Disallow: /author*' . PHP_EOL;
			$txt .= 'Disallow: /author/' . PHP_EOL;
			
			$txt .= PHP_EOL;
			
			$txt .= '# Spam Backlink Blocker End' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['add_bad_bot_blocker_in_the_robots_file'] )
		{
			$txt .= '# Block Bad Bots' . PHP_EOL;
			$txt .= PHP_EOL;

			$txt .= 'User-agent: DotBot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: GiftGhostBot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Seznam' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: PaperLiBot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Genieo ' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Dataprovider/6.101' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: DataproviderSiteExplorer' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Dazoobot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Diffbot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: DomainStatsBot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: DotBot/1.1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: dubaiindex' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: eCommerceBot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: ExpertSearchSpider' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Feedbin' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Fetch/2.0a' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: FFbot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: focusbot/1.1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: HuaweiSymantecSpider' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: HuaweiSymantecSpider/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: JobdiggerSpider' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: LemurWebCrawler' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: LipperheyLinkExplorer' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: LSSRocketCrawler/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: LYT.SRv1.5' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: MiaDev/0.0.1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Najdi.si/3.1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: BountiiBot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Experibot_v1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: bixocrawler' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: bixocrawler TestCrawler' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Crawler4j' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Crowsnest/0.5' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: CukBot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Dataprovider/6.92' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: DBLBot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Diffbot/0.1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Digg Deeper/v1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: discobot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: discobot/1.1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: discobot/2.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: discoverybot/2.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Dlvr.it/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: DomainStatsBot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: drupact/0.7' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Ezooms/1.0 ' . PHP_EOL; 
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: fastbot crawler beta 2.0  ' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;
		
			$txt .= 'User-agent: fastbot crawler beta 4.0  ' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: feedly social' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Feedly/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: FeedlyBot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Feedspot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Feedspotbot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Clickagy Intelligence Bot v2' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: classbot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: CISPA Vulnerability Notification' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: CirrusExplorer/1.1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Checksem/Nutch-1.10' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: CatchBot/5.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: CatchBot/3.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: CatchBot/2.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: CatchBot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: CamontSpider/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Buzzbot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Buzzbot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: BusinessSeek.biz_Spider' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: BUbiNG' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: 008/0.85' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: 008/0.83' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: 008/0.71' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: ^Nail' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: FyberSpider/1.3' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.1.6-beta5' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: g2reader-bot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.1.6-beta6' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.0.1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.0.2' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.0.4' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.0.5' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.0.9' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.1.5' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.1.3' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.2' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.5' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/2.6' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: FFbot/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.1.3-beta8' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.1.3-beta9' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.1.4-beta7' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.1.6-beta1' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.1.6-beta1 Yacy' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.1.6-beta2' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.1.6-beta3' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: findlinks/1.1.6-beta4' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: bixo' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: bixolabs/1.0' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Crawlera/1.10.2' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Dataprovider Site Explorer' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;
			
			$txt .= PHP_EOL;
			
			$txt .= '# Block Bad Bots End' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['add_bad_bot_blocker_in_the_robots_file'] )
		{
			$txt .= '# Backlink Protector' . PHP_EOL;
			$txt .= PHP_EOL;

			$txt .= 'User-agent: AhrefsBot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Alexibot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: MJ12bot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: SurveyBot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Xenu\'s' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: Xenu\'s Link Sleuth 1.1c' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: rogerbot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: SemrushBot' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: SemrushBot-SA' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: SemrushBot-BA' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: SemrushBot-SI' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: SemrushBot-SWA' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: SemrushBot-CT' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;

			$txt .= 'User-agent: SemrushBot-BM' . PHP_EOL;
			$txt .= 'Disallow: /' . PHP_EOL;
			
			$txt .= PHP_EOL;
			
			$txt .= '# Backlink Protector End' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['add_stop_crawling_in_the_robots_file'] )
		{
			$txt .= '# Stop crawler traps' . PHP_EOL;
			$txt .= PHP_EOL;

			$txt .= 'Disallow: /search/' . PHP_EOL;
			$txt .= 'Disallow: *&preview=*' . PHP_EOL;
			$txt .= 'Disallow: /preview/*' . PHP_EOL;
			$txt .= 'Disallow: /search' . PHP_EOL;
			
			$txt .= PHP_EOL;
			
			$txt .= '# Stop crawler traps' . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['add_ads_in_the_robots_file'] )
		{
			$txt .= '# Ads.txt' . PHP_EOL;

			$txt .= 'Allow: /ads.txt' . PHP_EOL;
			
			$txt .= PHP_EOL;
		}
		
		if ( $r['add_app_ads_in_the_robots_file'] )
		{
			$txt .= '# App-ads.txt' . PHP_EOL;

			$txt .= 'Allow: /app-ads.txt' . PHP_EOL;
			
			$txt .= PHP_EOL;
		}
		
		if ( $r['crawl_delay'] > 0 )
		{
			$txt .= 'Crawl-delay: ' . $r['crawl_delay'] . PHP_EOL;
			$txt .= PHP_EOL;
		}
		
		if ( $r['personal_code'] != '' )
		{
			$txt .= $r['personal_code'] . PHP_EOL;
		}

		$txt .= '# This robots.txt file was created by TokiCMS. https://tokicms.com/' . PHP_EOL;
		
		unset( $r );
		
		return $txt;
	}
}