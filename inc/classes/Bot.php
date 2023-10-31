<?php defined('TOKICMS') or die('Hacking attempt...');

define('HTTP_URL_REPLACE', 1);				// Replace every part of the first URL when there's one of the second URL
define('HTTP_URL_JOIN_PATH', 2);			// Join relative paths
define('HTTP_URL_JOIN_QUERY', 4);			// Join query strings
define('HTTP_URL_STRIP_USER', 8);			// Strip any user authentication information
define('HTTP_URL_STRIP_PASS', 16);			// Strip any password authentication information
define('HTTP_URL_STRIP_AUTH', 32);			// Strip any authentication information
define('HTTP_URL_STRIP_PORT', 64);			// Strip explicit port numbers
define('HTTP_URL_STRIP_PATH', 128);			// Strip complete path
define('HTTP_URL_STRIP_QUERY', 256);		// Strip query string
define('HTTP_URL_STRIP_FRAGMENT', 512);		// Strip any fragments (#identifier)
define('HTTP_URL_STRIP_ALL', 1024);			// Strip anything but scheme and host

class Bot
{
	private $rUrl;
	private $data;
	public  $url;
	public  $status;
	public  $options;
	public  $useCookies = false;

	public function process() 
	{
		$this->encode_url();
		$this->curl_url();
	}
	
	public function data() 
	{
		return $this->data;
	}
	
	private function curl_url() 
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $this->rUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // GIVE UP AFTER 10 SECONDS
		
		$header = array( 
			"Connection:keep-alive",
			"accept:text/html, Application/xhtml+xml, */*",
			"Pragma:no-cache",
			"accept-language:en-US,en;q=0.8"
		);
		
		$agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36';

		if ( !empty( $this->options['randomIp']['value'] ) )
		{
			$ip = $this->ip_rand();
			
			$agent = $this->user_agent_rand();

			$header[] = "REMOTE_ADDR: " . $ip;
			$header[] = "HTTP_X_FORWARDED_FOR: " . $ip;
			$header[] = "HTTP_X_REAL_IP: " . $ip;
			$header[] = "x-forwarded-for: " . $ip;
			$header[] = "Client-ip: " . $ip;
			$header[] = "user-agent: " . $agent;
		}
		
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
		
		if ( !empty( $this->options['crawlAsGoogleBot']['value'] ) && ( $this->options['crawlAsGoogleBot']['value'] == 'desktop' ) )
		{
			curl_setopt($ch, CURLOPT_REFERER, "https://www.google.com/");
			
			curl_setopt($ch, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
		}
		
		elseif ( !empty( $this->options['crawlAsGoogleBot']['value'] ) && ( $this->options['crawlAsGoogleBot']['value'] == 'mobile' ) )
		{
			curl_setopt($ch, CURLOPT_REFERER, "https://www.google.com/");
			
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
		}
		
		else
		{
			curl_setopt( $ch, CURLOPT_USERAGENT, $agent );
		}

		if ( $this->useCookies )
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			
			$root = DATA_ROOT . 'bot' . DS;
			
			if ( !is_dir( $root ) )
			{
				@mkdir( $root );
			}
			
			$cookie_file = $root . md5( $this->url ) . ".txt";
			curl_setopt($ch, CURLOPT_COOKIESESSION, true);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		}
		else
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		}
		
		if(curl_exec($ch) === false)
		{
			echo 'Curl error: ' . curl_error($ch);
			return false;
		}
		
		$data = curl_exec($ch);

		$removeScripts	     = ( !empty( $this->options['removeScripts']['value'] ) ? true : false );
		$removeLineBreaks 	 = ( !empty( $this->options['removeLineBreaks']['value'] ) ? false : true );
		$removeStyles 		 = ( !empty( $this->options['removeStyles']['value'] ) ? true : false );
		$serverSideScripts   = ( !empty( $this->options['removeServerSide']['value'] ) ? true : false );
		$removeSmartyScripts = ( !empty( $this->options['removeSmarty']['value'] ) ? true : false );
		
		if ( $removeScripts || !$removeLineBreaks || $removeStyles || $serverSideScripts || $removeSmartyScripts )
		{
			$data = $this->clean( $data, true, $removeScripts, $removeStyles, $serverSideScripts, false, false, $removeSmartyScripts );
		}
		
		$this->data = $data;

		$this->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		
		unset( $data );
	}

	public function match( $pattern, $data = null )
	{
		$data = ( empty( $data ) ? $this->data : $data );
		
		preg_match_all("#\s*" . $pattern . "\s*#iU", $data, $arr );
		
		//Try again...
		if ( empty( $arr['1'] ) )
		{
			preg_match_all("/" . $pattern . "/siU", $data, $arr );
		}

		if ( !empty( $arr ) && isset( $arr['1']['0'] ) )
		{
			if ( count ( $arr['1'] ) >= 3 )
				return $arr['1'];
			
			return $arr['1']['0'];
		}
		
		return null;
    }
	
	public function getElementsByTagName( $tag = 'meta' )
	{
		if ( empty( $this->data ) )
		{
			return null;
		}
		
		preg_match_all( '/<[\s]*' . $tag . '[\s]*(name|property)="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $this->data, $match );
		
		if ( empty( $match['3'] ) )
		{
			return null;
		}
		
		$arr = array();
		
		foreach( $match['0'] as $row => $value )
		{
			$arr[$match['2'][$row]] = $match['3'][$row];
		}
		
		return $arr;
	}
	
	private function encode_url() 
	{
		$parts = parse_url( $this->url );
		$parts['path'] = ( isset( $parts['path'] ) ? implode('/', array_map('urlencode', explode('/', $parts['path']))) : '' );
		$this->rUrl = urldecode( $this->http_build_url($this->url, $parts) );
	}
	
	private function ip_rand()
	{
		$ip  = '1' . rand(10, 99) . '.';
		$ip .= rand(101, 999) . '.';
		$ip .= rand(101, 999) . '.';
		$ip .= rand(10, 99);
		
		return $ip;
	}
	
	private function user_agent_rand()
	{
		$user_agents = array(
			#Chrome
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36',
			'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36',
			'Mozilla/5.0 (Windows NT 5.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36',
			'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36',
			'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36',
			'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36',
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36',
			'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36',
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
			'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
			#Firefox
			'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1)',
			'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
			'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
			'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko',
			'Mozilla/5.0 (Windows NT 6.2; WOW64; Trident/7.0; rv:11.0) like Gecko',
			'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
			'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)',
			'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko',
			'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
			'Mozilla/5.0 (Windows NT 6.1; Win64; x64; Trident/7.0; rv:11.0) like Gecko',
			'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)',
			'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)',
			'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)',
			'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36','Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:24.0) Gecko/20100101 Firefox/24.0',
			'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/91.0.4472.114 Safari/537.36',
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.1 Safari/605.1.15","Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:77.0) Gecko/20100101 Firefox/77.0'
		);
		
		return array_rand( $user_agents, 1 );
	}
	
	/**
     * Cleans the html of any none-html information.
     *
     * @param string $str
     * @return string
     */
    protected function clean($str, $preserveLineBreaks = true, $removeScripts = false, $removeStyles = true, $serverSideScripts = false, $removeSpace = false, $removeCData = false, $removeSmartyScripts = false )
    {

        // remove white space before closing tags
        $str = mb_eregi_replace("'\s+>", "'>", $str);
        $str = mb_eregi_replace('"\s+>', '">', $str);
		
        // clean out the \n\r
        $replace = ' ';
		
        if ($preserveLineBreaks) {
            $replace = '&!10;';
        }
        
		$str = str_replace(["\r\n", "\r", "\n"], $replace, $str);
        
		// strip the doctype
        $str = mb_eregi_replace("<!doctype(.*?)>", '', $str);
        
		// strip out comments
        $str = mb_eregi_replace("<!--(.*?)-->", '', $str);
		
		if ($removeCData) {
			// strip out cdata
			$str = mb_eregi_replace("<!\[CDATA\[(.*?)\]\]>", '', $str);
		}
		
		if ($removeSpace) {
			// strip out space
			$str = mb_eregi_replace('/\s+/', '', $str);
			
		}
		
        // strip out <script> tags
        if ($removeScripts) 
		{
            $str = mb_eregi_replace("<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>", '', $str);
            $str = mb_eregi_replace("<\s*script\s*>(.*?)<\s*/\s*script\s*>", '', $str);
        }
		
        // strip out <style> tags
        if ($removeStyles) {
            $str = mb_eregi_replace("<\s*style[^>]*[^/]>(.*?)<\s*/\s*style\s*>", '', $str);
            $str = mb_eregi_replace("<\s*style\s*>(.*?)<\s*/\s*style\s*>", '', $str);
        }
		
        // strip out server side scripts
        if ($serverSideScripts) {
            $str = mb_eregi_replace("(<\?)(.*?)(\?>)", '', $str);
        }
		
        // strip smarty scripts
		if ($removeSmartyScripts) {
			$str = mb_eregi_replace("(\{\w)(.*?)(\})", '', $str);
		}
		
        return $str;
    }
	
	// Build an URL
	// The parts of the second URL will be merged into the first according to the flags argument. 
	// 
	// @param	mixed			(Part(s) of) an URL in form of a string or associative array like parse_url() returns
	// @param	mixed			Same as the first argument
	// @param	int				A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE is the default
	// @param	array			If set, it will be filled with the parts of the composed url like parse_url() would return 
	public function http_build_url($url, $parts=array(), $flags=HTTP_URL_REPLACE, &$new_url=false)
	{
		$keys = array('user','pass','port','path','query','fragment');
			
		// HTTP_URL_STRIP_ALL becomes all the HTTP_URL_STRIP_Xs
		if ($flags & HTTP_URL_STRIP_ALL)
		{
			$flags |= HTTP_URL_STRIP_USER;
			$flags |= HTTP_URL_STRIP_PASS;
			$flags |= HTTP_URL_STRIP_PORT;
			$flags |= HTTP_URL_STRIP_PATH;
			$flags |= HTTP_URL_STRIP_QUERY;
			$flags |= HTTP_URL_STRIP_FRAGMENT;
		}
		
		// HTTP_URL_STRIP_AUTH becomes HTTP_URL_STRIP_USER and HTTP_URL_STRIP_PASS
		else if ($flags & HTTP_URL_STRIP_AUTH)
		{
			$flags |= HTTP_URL_STRIP_USER;
			$flags |= HTTP_URL_STRIP_PASS;
		}
			
		// Parse the original URL
		$parse_url = parse_url($url);
			
		// Scheme and Host are always replaced
		if (isset($parts['scheme']))
			$parse_url['scheme'] = $parts['scheme'];
		
		if (isset($parts['host']))
			$parse_url['host'] = $parts['host'];
			
		// (If applicable) Replace the original URL with it's new parts
		if ($flags & HTTP_URL_REPLACE)
		{
			foreach ($keys as $key)
			{
				if (isset($parts[$key]))
					$parse_url[$key] = $parts[$key];
			}
		}
		else
		{
			// Join the original URL path with the new path
			if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH))
			{
				if (isset($parse_url['path']))
					$parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
				else
					$parse_url['path'] = $parts['path'];
			}
				
			// Join the original query string with the new query string
			if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY))
			{
				if (isset($parse_url['query']))
					$parse_url['query'] .= '&' . $parts['query'];
				else
					$parse_url['query'] = $parts['query'];
			}
		}
				
		// Strips all the applicable sections of the URL
		// Note: Scheme and Host are never stripped
		foreach ($keys as $key)
		{
			if ($flags & (int)constant('HTTP_URL_STRIP_' . strtoupper($key)))
				unset($parse_url[$key]);
		}
			
			
		$new_url = $parse_url;
			
		return 
			((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
			.((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') .'@' : '')
			.((isset($parse_url['host'])) ? $parse_url['host'] : '')
			.((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
			.((isset($parse_url['path'])) ? $parse_url['path'] : '')
			.((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
			.((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '')
			;
	}
}
