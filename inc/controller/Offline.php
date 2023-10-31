<?php defined('TOKICMS') or die('Hacking attempt...');

class Offline extends Controller {
	
    public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		//Don't include this file while on login or register
		if ( ( Router::WhereAmI() != 'login' ) && ( Router::WhereAmI() != 'register' ) && ( Router::WhereAmI() != 'forgot-password' ) )
			Router::SetIncludeFile( INC_ROOT . 'offline.php' );
		
		//Check if the post has an external URL redirection
		if ( Router::GetVariable( 'whereAmI' ) == 'post' )
		{
			$slug = Sanitize( Router::GetVariable( 'slug' ), false );

			$query = PostDefaultQuery( "(p.id_site = " . SITE_ID . ") AND (p.sef = :sef) AND (p.post_type = 'post' OR p.post_type = 'page') AND (p.post_status = 'published') AND (b.disabled = 0 OR b.disabled IS NULL)" );
			
			$binds = array( $slug => ':sef' );
			
			//Query: post
			$tmp = $this->db->from( null, $query, $binds )->single();
			
			if ( !empty( $tmp ) )
			{
				$pData = BuildFullPostVars( $tmp );
				
				if ( !empty( $pData['externalUrl'] ) )
				{
					@header("Location: " . $pData['externalUrl'], true, 301 );
					@exit;
				}
			}
		
			unset( $tmp, $query, $pData );
		}
		
		$Offline = Json( Settings::Site()['maintenance_data'] );
		
		$this->setVariable( 'Offline', $Offline );

		$this->view();
	}
}