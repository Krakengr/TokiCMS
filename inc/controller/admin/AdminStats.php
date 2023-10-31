<?php defined('TOKICMS') or die('Hacking attempt...');

class AdminStats extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $filters;
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'view-stats' ) ) || !$Admin->Settings()::IsTrue( 'enable_stats' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'stats' ) . ' | ' . $Admin->SiteName() );
		
		include_once ( FUNCTIONS_ROOT .  'stats-functions.php' );
		
		//$x_max = days_in_month( $filters['mo'], $filters['yr'] );

		$arr = $visits = $hits = array();

		for( $h = 0; $h <= 24; $h++ )
		{
			$visits[$h] = $hits[$h] = 0;
		}
		
		$loaded_data = load_data( $filters, false );

		if ( empty( $loaded_data ) )
			return $this->setVariable( 'Stats', null );

		foreach( $loaded_data['visits']['start_time'] as $ts => $data )
		{
			$hn = intval( substr( $ts, 0, 2 ) );
			$visits[ $hn ] += $data['visits'];
			$hits[ $hn ] += $data['hits'];
		}
		
		$arr['dateLabel'] = htmlspecialchars( date_label( $filters ) );

		$per = __( 'hour' );
		
		$arr['visits'] = $loaded_data['visits'];
		$arr['pages'] = $loaded_data['pages'];

		$arr['vtitle'] = htmlspecialchars( __( 'visits' ) . '/' . $per );
		$arr['htitle'] = htmlspecialchars( __( 'hits' ) . '/' . $per );
		
		$arr['days'] = array_keys( $visits );
		
		foreach( $visits as $x => $y )
		{
			$arr['v'][] = $y;
			$arr['h'][] = $hits[$x];
		}
	
		$arr['browserData'] = $arr['browserArr'] = $arr['browserColor'] = $arr['browserHits'] = array();
		$arr['osData'] = $arr['osArr'] = $arr['osColor'] = $arr['osHits'] = array();
		
		//Platforms
		if ( isset( $loaded_data['visits']['platform'] ) && !empty( $loaded_data['visits']['platform'] ) )
		{
			$arr['osData'] = data_percent( 'platform', $loaded_data );
			
			if ( !empty( $arr['osData'] ) )
			{
				foreach( $arr['osData'] as $id => $platform )
				{
					$arr['osArr'][] = $platform['label'];
					$arr['osColor'][] = $platform['color'];
					$arr['osHits'][] = $platform['hits'];
				}
			}
		}
		
		//Browsers
		if ( isset( $loaded_data['visits']['browser'] ) && !empty( $loaded_data['visits']['browser'] ) )
		{
			$arr['browserData'] = data_percent( 'browser', $loaded_data );
			
			if ( !empty( $arr['browserData'] ) )
			{
				foreach( $arr['browserData'] as $id => $browser )
				{
					$arr['browserArr'][] = $browser['label'];
					$arr['browserColor'][] = $browser['color'];
					$arr['browserHits'][] = $browser['hits'];
				}
			}
		}
		
		unset( $loaded_data, $hits, $visits );
		
		$this->setVariable( 'Stats', $arr );
	}
}