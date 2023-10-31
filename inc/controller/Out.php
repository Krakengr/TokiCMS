<?php defined('TOKICMS') or die('Hacking attempt...');

class Out extends Controller {
	
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
		$this->view();
	}
	
	public function Run()
	{
		//$host = parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST );
		
		//$url = preg_quote( GetTheHostName( SITE_URL ) );
		
		Router::SetIncludeFile( INC_ROOT . 'out.php' );
		
		$id = (int) Router::GetVariable( 'slug' );

		//Query: price
		$data = $this->db->from( null, "SELECT main_page_url, aff_page_url FROM `" . DB_PREFIX . "prices`
		WHERE (id_price = :id)", array( $id => ':id' ) )->single();

		if ( !$data )
		{
			Router::SetNotFound();
			$this->setVariable( 'WhereAmI', '404' );
			return;
		}
		
		$dbarr = array(
			"last_time_viewed" => time(),
			"views" => "views + 1"
        );

		$this->db->update( 'prices' )->where( 'id_price', $id )->set( $dbarr );
		
		$url = ( !empty( $data['aff_page_url'] ) ? $data['aff_page_url'] : $data['main_page_url'] );
		
		$this->setVariable( 'Url', $url );
	}
}