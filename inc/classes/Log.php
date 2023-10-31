<?php defined('TOKICMS') or die('Hacking attempt...');

class Log
{
	private static $ip;
	
	public function __construct()
	{
		self::$ip = GetRealIp();
	}
	
	public static function Set( $title = '', $descr = '', $param = array(), $type = 'system', $uuid = 0, $siteId = SITE_ID )
	{
		//If this is not the default site, then we have to load its settings
		if ( $siteId != SITE_ID )
		{
			$s = new Settings( $siteId, false );
			$settings = $s::LogSettings();
		}
		
		else
		{
			$settings = Settings::LogSettings();
		}
		
		if ( empty( $settings ) || !$settings['enable_error_log'] )
		{
			return;
		}
		
		if ( !empty( $param ) )
		{
			$descr .= PHP_EOL;
			$descr .= 'With Parameters: ' . print_r( $param, true );
			$descr .= PHP_EOL;
		}
		
		$db = db();
		
		$dbarr = array(
			"id_site" 		=> $siteId,
			"user_id"    	=> $uuid,
			"title"    		=> $title,
			"descr"    		=> $descr,
			"added_time"	=> time(),
			"ip"    		=> self::$ip,
			"type"    		=> $type
		);

		$db->insert( 'logs' )->set( $dbarr );
	}
}