<?php defined('TOKICMS') or die('Hacking attempt...');

class ShortLink extends Controller {
	
    public function process() 
	{
		global $Lang;
		
		$Lang = $this->lang;
		
		if ( !IsAllowedTo( 'view-site' ) )
		{
			//Don't include this file while on login or register
			if ( Router::WhereAmI() != 'login' )
				Router::SetIncludeFile( INC_ROOT . 'no-access.php' );

			$this->view();
			return;
		}

		$this->setVariable( 'Lang', $this->lang );
		$this->Run();
		Theme::Build();
		$this->view();
	}
	
	public function Run()
	{
		$settings = Settings::LinkSettings();
		
		if ( empty( $settings ) || empty( $settings['short-link-settings'] ) || !$settings['short-link-settings']['enable'] )
		{
			Router::SetNotFound();
			return;
		}
		
		$id = Router::GetVariable( 'slug' );
		
		Router::SetIncludeFile( INC_ROOT . 'out.php' );

		//Query: link
		$data = $this->db->from( null, "
		SELECT id, id_post, url, link_data
		FROM `" . DB_PREFIX . "links`
		WHERE (short_link = :link) AND (status = 'active') AND (id_site = " . SITE_ID . ")",
		array( $id => ':link' )
		)->single();

		if ( !$data || empty( $data['url'] ) )
		{
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
			return;
		}
		
		$linkData = ( !empty( $data['link_data'] ) ? Json( $data['link_data'] ) : null );
		
		$dbarr = array(
			"last_time_viewed" 	=> time(),
			"num_views" 		=> "num_views + 1"
        );

		$this->db->update( 'links' )->where( 'id', $data['id'] )->set( $dbarr );
		
		if ( !empty( $settings['short-link-settings']['redirection'] ) )
		{
			$meta = false;
			
			$redir = $settings['short-link-settings']['redirection'];
			
			if ( $redir == 'direct' )
			{
				@header( "Location: " . $data['url'] );
			}
			
			elseif ( $redir == 'meta-refresh' )
			{
				$meta = true;
			}

			else
			{
				@header( "Location: " . $data['url'], true, (int) $redir );
			}

			$this->setVariable( 'Url', $data['url'] );
			$this->setVariable( 'Meta', $meta );
			
		}
		
		else
		{
			
		}
	}
}