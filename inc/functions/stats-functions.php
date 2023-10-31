<?php defined('TOKICMS') or die('Hacking attempt...');

global $field_names, $filters, $browsers, $browser_details, $browser_colors, $platform_colors, $platforms, $platform_details;

$platforms = array(
	// 0 is reserved for unknown;
	1 => 'Win',
	2 => 'Linux',
	3 => 'Mac',
	4 => 'Android',
	5 => 'FreeBSD',
	6 => 'iPod',
	7 => 'iPad',
	8 => 'iPhone'
);

$platform_details = array(
	1 => array( 'display_name' => 'Windows' ),
	3 => array( 'display_name' => 'Macintosh' )
);

$platform_colors = array(
	0 => '#1f2d3d', // 0 is reserved for unknown;
	1 => '#007bff',
	3 => '#17a2b8',
	4 => '#39cccc',
	5 => '#605ca8',
	6 => '#3c8dbc',
	7 => '#6610f2',
	8 => '#d81b60'

);

$field_names = array(
	'remote_ip' => __( 'ip-address' ),
	'search_terms' => __( 'search-terms' ),
	'domain' => __( 'source-domain' ),
	'referrer' => __( 'referrer' ),
	'resource' => __( 'page' ),
	'country' => __( 'country' ),
	'language' => __( 'language' ),
	'browser' => __( 'browser' ),
	'version' => __( 'version' ),
	'platform' => __( 'operating-system' ),
	'source' => __( 'visit-source' )
);

$browser_colors = array(
	0 => '#1f2d3d', // 0 is reserved for unknown;
	2 => '#f39c12',
	3 => '#00a65a',
	4 => '#f56954',
	5 => '#3c8dbc',
	6 => '#3c8dbc',
	7 => '#00c0ef',
	22 => '#d2d6de'

);

$browsers = array(
		// 0 is reserved for unknown;
		// 1 is reserved for bots;
		2 => 'Firefox',
		3 => 'MSIE',
		4 => 'Chrome',
		5 => 'Opera',
		6 => 'Opera Mini',
		7 => 'Safari',
		8 => 'Epiphany',
		9 => 'Fennec',
		10 => 'Iceweasel',
		11 => 'Minefield',
		12 => 'Minimo',
		13 => 'Flock',
		14 => 'Firebird',
		15 => 'Phoenix',
		16 => 'Camino',
		17 => 'Chimera',
		18 => 'Thunderbird',
		19 => 'Netscape',
		20 => 'OmniWeb',
		21 => 'Iron',
		22 => 'Chromium',
		23 => 'iCab',
		24 => 'Konqueror',
		25 => 'Midori',
		26 => 'DoCoMo',
		27 => 'Lynx',
		28 => 'Links',
		29 => 'lwp-request',
		30 => 'w3m',
		31 => 'Wget'
);

$browser_details = array(
	3 => array( 'display_name' => 'Internet Explorer', 'regex' => 'MSIE ([\d\.]+)' ),
	5 => array( 'regex' => '(?:Version/|Opera )([\d\.]+)' ),
	6 => array( 'regex' => 'Opera Mini(?: |/)([\d\.]+)' ),
	7 => array( 'regex' => '(?:Safari|Version)/([\d\.]+)' ),
	19 => array( 'regex' => 'Netscape[0-9]?/([\d\.]+)' ),
	24 => array( 'platform' => 'Linux' ),
	28 => array( 'regex' => '\(([\d\.]+)' ),
	29 => array( 'display_name' => 'libwww Perl library', 'regex' => 'lwp-request/(.*)$' )
);

$filters = array();

$filters['yr'] = date( 'Y' );
$filters['mo'] = date( 'n' );
$filters['dy'] = date( 'd' );
$filters['dy'] = valid_dy( $filters['dy'], $filters['mo'], $filters['yr'] );

function table_total( $id, $loaded_data, $echo = true )
{
	global $filters, $field_names, $Stats;
	
	// only show the table if the field isn't being filtered
	if( isset( $filters[$id] ) || empty( $loaded_data ) )
		return;

	$title = $field_names[$id];
	
	if( $id == 'resource' )
		$data = &$loaded_data['pages'];
	else
		$data = &$loaded_data['visits'][$id];
	
	$data = (array) $data;	// in case it's empty
	
	arsort( $data );

	$new_filters = $filters;
	$max_rows = 50;
	
	$size = sizeof( $data );
	if( isset( $data[''] ) )
		$size --;	// don't count empty values
	
	$html = '
	<table class="table table-striped table-valign-middle">
		<thead>
			<tr>
				<th>' . $title . '</th>
				<th>' . __( 'hits' ) . '</th>
			</tr>
		</thead>
		<tbody>';
	
	$pos = 0;
	
	foreach ( $data as $key => $hits )
	{
		if( $key == '' )
			continue;

		$new_filters[$id] = $key;

		$html .= '
		<tr>
			<td>';
			
			if ( $id == 'referrer' )
				$html .= '<a class="goto ext" href="' . htmlspecialchars( $key ) . '" rel="external noreferrer">&rarr;</a> ';

			$html .= filter_link( $new_filters, $key );
		
		$html .= '
			</td>
			<td>' . format_number( $hits, 0 ) . '</td>
		</tr>';
	}
	
	$html .= '
	</table>';
	
	if ( !$echo )
		return $html;
	
	echo $html;
}

function aggregate_old_data()
{
	global $Admin;
	
	$stats = Json( Settings::Get()['stats_data'] );
	
	if ( empty( $stats ) || !isset( $stats['aggregate_data'] ) || ( $stats['aggregate_data'] == 0 ) )
		return;
	
	$db = db();
	
	$after = $stats['aggregate_data'];

	// start from the earliest month to aggregate
	$yr = intval( date('Y') );
	$mo = intval( date('n') ) - $after - 1;
	
	while( $mo < 1 ) {
		$yr --;
		$mo += 12;
	}

	if( isset( $stats['last_aggregated'] ) && $stats['last_aggregated']['yr'] >= $yr && $stats['last_aggregated']['mo'] >= $mo )
		return;		// we're already up to date
	
	$stats['last_aggregated'] = array( 'yr' => $yr, 'mo' => $mo );
	
	$settingsArray = array( 'stats_data' => json_encode( $stats, JSON_UNESCAPED_UNICODE ) );

	$Admin->UpdateSettings( $settingsArray );
	
	$data = $db->from( null, "
	SELECT MIN(date) as date
	FROM `" . DB_PREFIX . "stats`
	LIMIT 1"
	)->single();

	if ( !$data )
		return;

	preg_match( '/^(\d{4})-(\d{2})/', $data['date'], $matches );

	$min_yr = intval( $matches['1'] );
	$min_mo = intval( $matches['2'] );
		
	// is the earliest data within cutoff range?
	if( gmmktime( 0, 0, 0, $mo, 1, $yr ) < gmmktime( 0, 0, 0, $min_mo, 1, $min_yr ) )
		return;

	while( true )
	{
		$data = load_data( array( 'yr' => $yr, 'mo' => $mo ) );
		$data = gzdeflate( serialize( $data ) );
		
		// put into archive
		$dbarr = array(
			"yr" 	=> $yr,
			"mo"    => $mo,
			"data" 	=> $data
        );
            
		$db->insert( 'stats_archive' )->set( $dbarr );

		$endofmonth = $yr . "-" . $mo . "-" . days_in_month( $mo, $yr );
		$startmonth = $yr . "-" . $mo . "-01";
		
		// delete raw data
		$db->delete( 'stats' )->where( "date", $startmonth, false, '>=' )->where( "date", $endofmonth, false, '<=' )->run();

		if( $yr == $min_yr && $mo == $min_mo )
			break;
		
		$mo --;
		if( $mo < 1 ) {
			$yr --;
			$mo += 12;
		}
	}

	$db->optimize( "stats" );
}

function browser_name_from_id( $id )
{
	global $browsers, $browser_details;
	
	$id = intval( $id );
	if( isset( $browsers[$id] ) )
		return isset( $browser_details[$id]['display_name'] ) ? $browser_details[$id]['display_name'] : $browsers[$id];
		
	return '';
}

function get_date_filter( $yr, $mo, $dy = false ) {
	$mo = sprintf( '%02d', $mo );
	$dy = $dy ? sprintf( '%02d', $dy ) : '';
	
	if( !$dy && $yr == date('Y') && $mo == date('m') )
		return '_';
	
	return "$yr-$mo" . ( $dy ? "-$dy" : '' );
}

function filter_url( $_filters ) {
	if ( !is_array( $_filters ) )
		return '';
	
	$shown_first = false;
	$str = '';
	$cleaned_filters = $_filters;
	
	unset( $cleaned_filters['yr'], $cleaned_filters['mo'], $cleaned_filters['dy'] );
	
	$yr = isset( $_filters['yr'] ) ?  $_filters['yr'] : date('Y'); 
	$mo = isset( $_filters['mo'] ) ?  $_filters['mo'] : date('m');
	$dy = isset( $_filters['dy'] ) ? $_filters['dy'] : false;
	$date = get_date_filter( $yr, $mo, $dy );
	
	if( $date != '_' )
		$cleaned_filters['date'] = $date;
	
	$sep = '?';
	foreach ( $cleaned_filters as $key => $value ) {
		$str .= $sep . 'filter_'. $key . '=' . rawurlencode( $value );
		$sep = '&amp;';
	}
	
	return $str;
}

function filter_link( $_filters, $text, $is_archive = false )
{
	if( isset( $_filters['referrer'] ) || isset( $_filters['resource'] ) )
		$text = urldecode( $text );

	$text = htmlspecialchars( $text );

	// avoid super-long referrer strings
	if( strlen( $text ) > 100 )
		$text = substr( $text, 0, 100 ) . '&hellip;';
	
	// cannot filter archives
	if( $is_archive )
		return $text;

	$url = filter_url( $_filters );
	return "<a href='./$url' class='filter'>$text</a>";
}

function get_value_label( $field, $key )
{
	if( ! $key )
		return __( 'unknown' );
	
	if( $field == 'country' )
		return country_name( $key );
	
	if( $field == 'browser' )
		return browser_name_from_id( $key );
	
	if( $field == 'platform' )
		return platform_name_from_id( $key );
	
	return $key;
}

function platform_name_from_id( $id )
{
	global $platforms, $platform_details;
	
	$id = intval( $id );
	
	if( isset( $platforms[$id] ) )
		return isset( $platform_details[$id]['display_name'] ) ? $platform_details[$id]['display_name'] : $platforms[$id];
		
	return '';
}

function get_all_browser_names()
{
	global $browsers;
	
	$result = array();
	
	foreach( array_keys( $browsers ) as $id )
		$result[$id] = browser_name_from_id( $id );

	return $result;
}
	
function get_all_platform_names()
{
	global  $platforms;
	
	$result = array();
	
	foreach( array_keys( $platforms ) as $id )
		$result[$id] = platform_name_from_id( $id );

	return $result;
}
	

function parse_user_agent( $_ua )
{
	global $platforms, $browsers, $browser_details;
	
	$default_version_regex = '/([\d\.]+)';
	$result = array( 'browser' => 0, 'version' => '', 'platform' => 0 );
		
	$bots = array( 'crawl', 'bot', 'bloglines', 'dtaagent', 'feedfetcher', 'ia_archiver', 'java', 'larbin', 'mediapartners', 'metaspinner', 'searchmonkey', 'slurp', 'spider', 'teoma', 'ultraseek', 'waypath', 'yacy', 'yandex', 'scoutjet', 'harvester', 'facebookexternal', 'mail.ru/', 'urllib', 'validator', 'whatweb', 'bingpreview', 'gomezagent', 'nutch', 'WordPress', 'EC2LinkFinder', 'panopta.com', 'alwaysonline', 'heritrix', 'ichiro', 'netvibes', 'genieo', 'siteexplorer', 'developers.google.com' );
		
	foreach( $bots as $str ) {
		if( stripos( $_ua, $str ) !== false ) {
			$result['browser'] = 1;
			return $result;	// no need to bother with the rest
		}
	}
	
		$platform_matches = array();

	foreach( $platforms as $id => $name ) {
			if( strpos( $_ua, $name ) !== false )
				$platform_matches[] = $id;	// value = platform id
		}
		
		if( $platform_matches )
			$result['platform'] = guess_platform( $platform_matches );

		$browser_matches = array();

		foreach ( $browsers as $id => $name ) {
			$details = isset( $browser_details[$id] ) ? $browser_details[$id] : array();
			if ( strpos( $_ua, $name ) !== false ) {
				$ver = '';
				$regex = isset( $details['regex'] ) ? $details['regex'] : $name . $default_version_regex;
				preg_match( '!' . $regex . '!', $_ua, $b );
				if ( $b ) {
					$ver = $b[1];
					if ( isset( $details['platform'] ) ) 
						$result['platform'] = $details['platform'];
				}

				$browser_matches[$id] = $ver;	// key = browser id, value = version
			}
		}
		
		if( $browser_matches )
		{
			$result['browser'] = guess_browser( array_keys( $browser_matches ) );
			$result['version'] = $browser_matches[ $result['browser'] ];
		}

		return $result;	// an array of ( 'browser' (ID), 'version', 'platform' (ID) )
}

function guess_browser( $arr )
{
	if( count( $arr ) == 1 )
		return $arr[0];

	$lowest = array( 3, 5, 7, 22 );	// "MSIE", "Opera", "Safari", "Chromium" appear in many other browsers strings
	
	foreach( $lowest as $low ) {
		$key = array_search( $low, $arr );
		if( count( $arr ) > 1 && $key !== false )
			unset( $arr[$key] );
	}
	return max( $arr );
}
	
function guess_platform( $arr )
{
	// currently, we just use the highest integer, because that works. So iPad trumps Macintosh, for example.
	// But this may need more speicific rules for some UAs
	return max( $arr );
}

function format_number( $_number, $_dp = 1 ) {
	$decimal = __( '.', 'decimal_point' );
	$thousands = __( ' ', 'thousands_separator' );
	$str = number_format( $_number, $_dp, $decimal, $thousands );
	if ( $str == '0'.$decimal.'0' && $_dp == 1 ) {
		$str2 = number_format( $_number, 2, $decimal, $thousands );
		if ( $str2 != '0'.$decimal.'00' ) {
			return $str2;
		}
	}
	return $str;
}

function data_percent( $id, $loaded_data )
{
	global $filters, $field_names, $browser_colors, $platform_colors;
	
	$data = ( isset( $loaded_data['visits'][$id] ) ? $loaded_data['visits'][$id] : null );
	
	if( empty( $data ) )
		return;
	
	arsort( $data );
	
	$new_filters = $filters;
	$max_rows = 50;
	
	$arr = array();
	
	$size = sizeof( $data );
	$total = array_sum( $data );

	$pos = 0;
	
	foreach ( $data as $key => $hits )
	{
		$new_filters[$id] = $key;

		$pct = $total ? ( $hits / $total * 100 ) : 0;
	
		$label = get_value_label( $id, $key );
		
		$color = '';
		
		if ( $id == 'browser' )
		{
			$color = ( isset( $browser_colors[$key] ) ? $browser_colors[$key] : $browser_colors['0'] );
		}
		
		if ( $id == 'platform' )
		{
			$color = ( isset( $platform_colors[$key] ) ? $platform_colors[$key] : $platform_colors['0'] );
		}
		
		$num = format_number( $pct );
		
		$arr[$key] = array(
					'label' => $label,
					'color' => $color,
					'hits' => $num,
					'versions' => array()
		);
		
		if ( $id == 'browser' && $key != '' && ( isset( $loaded_data['visits']['version'][$key] ) ) )
		{
			arsort( $loaded_data['visits']['version'][$key] );
			
			foreach ( $loaded_data['visits']['version'][$key] as $key2 => $hits2 )
			{
				$pct = ( $total > 0 ) ? $hits2 / $total * 100 : 0;

				$arr[$key]['versions'][$key2] = array(
							'ver' => $key2,
							'hits' => format_number( $pct )
				);
			}
		}
		
		$pos++;
		if ( $pos >= $max_rows ) break;
	}
	
	return $arr;
}

function date_label( $_array, $_dy_override = null )
{
	$yr = $_array['yr'];
	$mo = isset( $_array['mo'] ) ? $_array['mo'] : null;
	$dy = isset( $_array['dy'] ) ? $_array['dy'] : null;
	if ( $_dy_override === false )
		$dy = null;
	elseif ( $_dy_override > 0 )
		$dy = valid_dy( $_dy_override, $mo, $yr );
	
	if ( $dy != null && $mo != null )
		return gmstrftime( '%a %d %b %Y', gmmktime( 12, 0, 0, $mo, $dy, $yr ) );
	
	if ( $mo != null )
		return gmstrftime( '%b %Y', gmmktime( 12, 0, 0, $mo, 1, $yr ) );
	
	return $yr;
}

function load_data( $_filters, $fullMonth = true )
{
	global $Admin, $field_names;
	
	$db = db();
	
	$fields = array_keys( $field_names );
	
	$yr = intval( $_filters['yr'] );
	$mo = intval( $_filters['mo'] );
	$dy = isset( $_filters['dy'] ) ? intval( $_filters['dy'] ) : false;
	
	if ( $fullMonth )
	{
		unset( $_filters['dy'] );
		$dy = false;
	}
	
	// work out date range
	$d0 = $dy ? $dy : 1;
	$dn = $dy ? $dy : days_in_month( $mo, $yr );
	
	$start_ts 	= gmmktime( 0, 0, 0, $mo, $d0, $yr );
	$end_ts 	= gmmktime( 0, 0, 0, $mo, $dn, $yr );
	$start_date = gmdate( 'Y-m-d', $start_ts );
	$end_date 	= gmdate( 'Y-m-d', $end_ts );
	
	$date_query = ( $start_date == $end_date ) ? "(date = '" . $start_date . "')" : "(date >= '" . $start_date . "') AND (date <= '" . $end_date . "')";
	
	$where = "(id_site = " . $Admin->GetSite() . ") AND (browser != 1) AND " . $date_query;
	
	foreach ( $fields as $key )
	{
		if( !isset( $_filters[$key] ) )
			continue;
			
		$v = $_filters[$key];
		
		$where .= ' AND ' . $key . ' LIKE';
		
		// resource is tricky
		if( $key == 'resource' )
		{
			$where .= ' %' . $v . '%';
		}
		
		else
		{
			$where .= " '" . $v . "'";
		}
	}

	$query = "SELECT * FROM `" . DB_PREFIX . "stats` WHERE " . $where;
	
	$result = $db->from( null, $query )->all();

	// we also need date/time data for visits
	$extra_fields = isset( $_filters['dy'] ) ? array( 'start_time' ) : array( 'date' );
	
	return parse_data( $result, array_merge( $fields, $extra_fields ), $_filters );
}

function parse_data( $_result, $_fields, $_filters )
{
	if ( !$_result )
		return null;

	$visits = $pages = array();

	$source = array( 'search_terms' => 0, 'referrer' => 0, 'direct' => 0 );

	foreach( $_result as $row )
	{
		// extract individual page info
		$resources = explode( "\n", $row['resource'] );

		// if filtering by resource, things are a bit more complicated
		$filtering_resource = isset( $_filters['resource'] );
		$hits = $filtering_resource ? 0 : $row['hits'];

		foreach( $resources as $r )
		{
			if( empty( $r ) )
				continue;

			list( $time, $resource ) = explode( ' ', $r, 2 );
			
			$resource = trim( $resource ); 

			// if filtering by page then ignore everything else but that page
			if( $filtering_resource )
			{
				if( $resource == $_filters['resource'] )
					$hits += 1;
				
				else
					continue;
			}

			if( isset( $pages[$resource] ) )
				$pages[$resource] ++;
			
			else
				$pages[$resource] = 1;
		}

		if ( isset( $row['search_terms'] ) && isset( $row['referrer'] ) )
		{
			if ( ! empty( $row['search_terms'] ) )
				$source['search_terms']++;
			
			elseif ( ! empty( $row['referrer'] ) )
				$source['referrer']++;
			
			else
				$source['direct']++;
		}
		
		if( array_sum( $source ) )
			$visits['source'] = $source;
		
		// add up info for other fields, with a few tweaks
		foreach ( $_fields as $field )
		{
			if ( !isset( $row[$field] ) || $field == 'resource' )	// resource has been dealt with already
				continue;
			
			$value = $row[$field];
			
			// save both hits as well as visits
			if( $field == 'date' || $field == 'start_time' )
			{	
				if ( isset( $visits[$field][$value] ) )
				{
					$visits[$field][$value]['visits'] ++;
					$visits[$field][$value]['hits'] += $hits;
				}
				
				else
				{
					$visits[$field][$value] = array( 'hits' => $hits, 'visits' => 1 );
				}
			}
			
			// these items don't have an "Unknown" category
			if( in_array( $field, array( 'search_terms', 'referrer', 'domain' ) ) && empty( $value ) )
				continue;
			
			// store version as Browser => array( version => hits )
			if ( $field == 'version' ) {
				$browser = $row['browser'];
				if ( !isset( $visits[$field][$browser] ) ) 
					$visits[$field][$browser] = array();
				
				if ( isset( $visits[$field][$browser][$value] ) )
					$visits[$field][$browser][$value] ++;
				else
					$visits[$field][$browser][$value] = 1;
				continue;
			}

			if ( isset( $visits[$field][$value] ) )
				$visits[$field][$value] ++;
			else
				$visits[$field][$value] = 1;
		}
	}

	return array( 'visits' => $visits, 'pages' => $pages );
}

function valid_mo( $_mo )
{
	return max( 1, min( 12, intval( $_mo ) ) );
}

function valid_yr( $_yr )
{
	return max( 1970, min( 3000, intval( $_yr ) ) );
}

function days_in_month( $_mo, $_yr )
{
	return date( 'j', mktime( 12, 0, 0, $_mo + 1, 0, $_yr ) );
}

function valid_dy( $_dy, $_mo, $_yr )
{
	$dy = max( 1, min( date( 'j', gmmktime( 12, 0, 0, $_mo + 1, 0, $_yr ) ), intval( $_dy ) ) );
	
	if ( $_yr == date( 'Y' ) && $_mo == date( 'n' ) )
		$dy = min( date( 'j' ), $dy );

	return $dy;
}

/**
* Try to work out the original client IP address.
* If all we end up with is a private IP, discard it.
*/
function determine_remote_ip()
{
	// headers to look for, in order of priority
	$headers_to_check = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED_HOST', 'REMOTE_ADDR' );

	foreach( $headers_to_check as $header ) {
		if( empty( $_SERVER[$header] ) )
			continue;

		$ips = explode( ',', $_SERVER[$header] );
		foreach( $ips as $ip ) {
			$ip = trim( $ip );
			if( $ip && ! preg_match( '/^(10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.|192\.168\.)/i', $ip ) )	// private network IPs
				return $ip;
		}
	}
		
	return '';
}
	
/**
* Try to work out the requested resource.
*/
function determine_resource() {
	if( isset( $_SERVER['REQUEST_URI'] ) )
		return $_SERVER['REQUEST_URI'];
	
	elseif( isset( $_SERVER['SCRIPT_NAME'] ) )
		return $_SERVER['SCRIPT_NAME'] . ( empty( $_SERVER['QUERY_STRING'] ) ? '' : '?' . $_SERVER['QUERY_STRING'] );
	
	elseif( isset( $_SERVER['PHP_SELF'] ) )
		return $_SERVER['PHP_SELF'] . ( empty( $_SERVER['QUERY_STRING'] ) ? '' : '?' . $_SERVER['QUERY_STRING'] );
	
	return '';
}

/**
* Determines the visitor's country based on their IP address.
* You can supply your own GeoIP information (two-letter country code) by
* definining a constant SIMPLE_STATS_GEOIP_COUNTRY containing this value.
*/
function determine_country( $_ip ) {
	return '';
}

function determine_language() {
	// Capture up to the first delimiter (comma found in Safari)
	if ( !empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) && preg_match( "/([^,;]*)/", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $langs ) )
		return strtolower( $langs[0] );
	
	return '';
}
	
/**
* Detects referrals from search engines and tries to determine the search terms.
*/
function determine_search_terms( $_url ) {
	if( empty( $_url['host'] ) || empty( $_url['query'] ) )
		return;

	$sniffs = array( // host string, query portion containing search terms, parameterised url to decode
			array( 'images.google', 'q', 'prev' ),
			array( 'yahoo.', 'p' ),
			array( 'yandex.', 'text' ),
			array( 'rambler.', 'words' ),
			// generic
			array( '.', 'q' ),
			array( '.', 'query' )
	);

	$search_terms = '';

	foreach ( $sniffs as $sniff ) {
		if ( strpos( strtolower( $_url['host'] ), $sniff[0] ) !== false ) {
			parse_str( $_url['query'], $q );

			if ( isset( $sniff[2] ) && isset( $q[$sniff[2]] ) ) {
				$decoded_url = parse_url( $q[ $sniff[2] ] );
					
				if ( isset( $decoded_url['query'] ) )
					parse_str( $decoded_url['query'], $q );
			}

			if ( isset( $q[ $sniff[1] ] ) ) {
				$search_terms = trim( stripslashes( $q[ $sniff[1] ] ) );
				break;
			}
		}
	}

	return $search_terms;
}

function utf8encode( $_str ) {
	$encoding = mb_detect_encoding( $_str );
	
	if ( $encoding == false || strtoupper( $encoding ) == 'UTF-8' || strtoupper( $encoding ) == 'ASCII' )
		return $_str;

	return iconv( $encoding, 'UTF-8', $_str );
}

function parse_version( $version, $parts = 2 ) {
	$value = implode( '.', array_slice( explode( '.', $version ), 0, $parts ) );
	
	// skip trailing zeros - most browsers have rapid release cycles now
	if( substr( $value, -2 ) == '.0' )
		$value = substr_replace( $value, '', -2 );
	
	return $value;
}