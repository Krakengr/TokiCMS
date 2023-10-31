<?php defined('TOKICMS') or die('Hacking attempt...');

class VideoPlaylists extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();

		$this->view();
	}
	
	private function Run() 
	{
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-video-content' ) )
		{
			Router::SetNotFound();
			return;
		}
	}
}