<?php defined('TOKICMS') or die('Hacking attempt...');

class Blogs extends Controller {
	
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
		
		if ( ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-blogs' ) ) || !$Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		Theme::SetVariable( 'headerTitle', __( 'blogs' ) . ' | ' . $Admin->SiteName() );
		
		$langKey 	= $Admin->LangKey();
		$langId 	= $Admin->GetLang();
		$defLang 	= $Admin->IsDefaultLang();
		
		$blogs 		= array();
		
		$i 			= 0;
		
		//Query: blogs
		$tmp = $this->db->from( null, "
		SELECT b.*, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_blog = b.id_blog AND p.id_lang = " . $langId . " AND p.post_status = 'published') as blog_posts, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_blog = b.id_blog AND p.id_lang = " . $langId . " AND p.post_status = 'pending') as unapproved_posts, (SELECT COUNT(id) FROM `" . DB_PREFIX . "comments` as cm WHERE cm.id_blog = b.id_blog AND cm.id_lang = " . $langId . " AND cm.status = 'approved') as num_comments, (SELECT COUNT(id) FROM `" . DB_PREFIX . "comments` as cm WHERE cm.id_blog = b.id_blog AND cm.id_lang = " . $langId . " AND cm.status = 'pending') as unapproved_comments
		FROM `" . DB_PREFIX . "blogs` AS b
		WHERE (b.id_lang = " . $Admin->GetLang() . " OR b.id_lang = 0) AND (b.id_site = " . $Admin->GetSite() . ")
		ORDER BY b.name ASC" )->all();
		
		if ( !empty( $tmp ) )
		{
			foreach ( $tmp as $blog )
			{
				$blogs[$i] = $blog;
				
				if ( !$defLang )
				{
					$temp = Json( $blog['trans_data'] );

					if ( !empty( $temp ) && isset( $temp[$langKey] ) )
					{
						$blogs[$i]['name'] 			= $temp[$langKey]['name'];
						$blogs[$i]['description'] 	= $temp[$langKey]['description'];
					}
				}
				
				$i++;
			}
		}

		$this->setVariable( 'siteBlogs', $blogs );
	}
}