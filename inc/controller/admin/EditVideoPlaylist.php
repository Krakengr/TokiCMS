<?php defined('TOKICMS') or die('Hacking attempt...');

class EditVideoPlaylist extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-video-content' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Admin->SetFile( ADMIN_THEME_PHP_ROOT . 'page.php' );
		
		$id = (int) Router::GetVariable( 'key' );
		
		$PlayList = AdminGetVideoPlaylist( $id );

		if ( !$PlayList )
			Redirect( $Admin->GetUrl( 'video-playlists' ) );
		
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		if ( !verify_token( 'edit-playlist' ) )
			return;
	
		$query = array(
				'UPDATE' 	=> DB_PREFIX . "playlists",
				'SET'		=> "id_site = :site, title = :title, descr = :descr",
				'WHERE'		=>  "id = :id",
				'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				'BINDS' => array(
						array('PARAM' => ':id', 'VAR' => $id, 'FLAG' => 'INT' ),
						array('PARAM' => ':site', 'VAR' => $Admin->GetSite(), 'FLAG' => 'INT' ),
						array('PARAM' => ':title', 'VAR' => $_POST['title'], 'FLAG' => 'STR' ),
						array('PARAM' => ':descr', 'VAR' => $_POST['descr'], 'FLAG' => 'STR' )
				)
		);

		Query( $query, false, false, true );
		
		Redirect( $Admin->GetUrl( 'edit-playlist' . PS . 'id' . PS . $id ) );
	}
}