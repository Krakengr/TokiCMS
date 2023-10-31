<?php defined('TOKICMS') or die('Hacking attempt...');

class AttributesGroups extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;
		
		if 
		( 
			( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) && !IsAllowedTo( 'manage-post-attributes' ) ) || !$Admin->Settings()::IsTrue( 'enable_post_attributes' )
		)
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'attribute-groups' ) . ' | ' . $Admin->SiteName() );
	}
}