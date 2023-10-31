<?php defined('TOKICMS') or die('Hacking attempt...');

class Dashboard extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		if ( !IsAllowedTo( 'view-dashboard' ) && !IsAllowedTo( 'admin-site' ) )
		{
			Router::SetNotFound();
		}
		
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
		global $Admin;
		
		Theme::SetVariable( 'headerTitle', htmlspecialchars( $Admin->HeaderTitle() . ' | ' . $Admin->SiteName() ) );

		if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) || !isset( $_POST['widgets'] ) )
			return;
		
		include ( ARRAYS_ROOT . 'admin-arrays.php');
		
		$userData = $Admin->UserDashData();
		
		$temp = array();
		
		if ( empty( $_POST['widgets'] ) )
		{
			$userData['widgets'] = array();
		}
		
		else
		{
			$widgets = array_keys( $_POST['widgets'] );

			if ( isset( $userData['widgets'] ) && ( ( isset( $userData['widgets']['left'] ) && !empty( $userData['widgets']['left'] ) ) || ( isset( $userData['widgets']['right'] ) && !empty( $userData['widgets']['right'] ) ) ) )
			{
				$_temp = array();

				foreach( $userData['widgets'] as $pos => $ids )
				{
					$temp[$pos] = array();

					foreach( $ids as $_id )
					{
						if ( !in_array( $_id, $widgets ) )
						{
							continue;
						}
						
						$temp[$pos][] = $_id;
						$_temp[] = $_id;
					}
				}
				
				if ( !empty( $widgets ) && !empty( $_temp ) )
				{
					foreach( $widgets as $widget )
					{
						if ( in_array( $widget, $_temp ) )
						{
							continue;
						}
						
						$temp['left'][] = $widget;
					}
				}
			}

			else
			{
				foreach( $widgets as $widget )
				{
					$temp['left'][] = $widget;
				}
			}

			$userData['widgets'] = $temp;
		}

		$this->db->update( USERS )->where( 'id_member', $Admin->UserID() )->set( "dashboard_data", json_encode( $userData ) );

		Redirect( $Admin->GetUrl() );
	}
}