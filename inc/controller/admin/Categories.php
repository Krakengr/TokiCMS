<?php defined('TOKICMS') or die('Hacking attempt...');

class Categories extends Controller {
	
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
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-posts' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$orderBy = ( Router::GetVariable( 'orderBy' ) ? Router::GetVariable( 'orderBy' ) : 'name' );
		$order = ( ( Router::GetVariable( 'order' ) && ( Router::GetVariable( 'order' ) == 'asc' ) ) ? 'DESC' : 'ASC' );
		$arrow = ( ( $order && ( $order == 'desc' ) ) ? 'down' : 'up' );
	
		$currentUrl = $Admin->CurrentAction() . PS . ( Router::GetVariable( 'subAction' ) ? Router::GetVariable( 'subAction' ) . PS : '' ) . ( $orderBy ? 'sort' . PS . $orderBy . PS . Router::GetVariable( 'order' )  . PS : '' );
	
		$showAll = ( $Admin->Settings()::IsTrue( 'parent_site_shows_everything' ) && ( $Admin->GetBlog() == 0 ) );
	
		$showAllSites = ( ( $showAll && MULTISITE && $Admin->IsDefaultSite() ) ? true : false );
	
		$langs = $Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) ? Langs( $Admin->GetSite(), false ) : array();
		
		//Query 
        $tmp = $this->db->from( null, "SELECT ca.id, ca.name, ca.sef, ca.descr, ca.id_site, ca.id_blog, ca.is_default, ca.cat_color, ca.hide_front, ca.hide_blog, ca.id_trans_parent, ca.id_lang, ca.num_items as numItems, la.code as ls, la.title as lt, la.locale as lc, b.name as bn, b.sef as bs, s.url, s.title as st
		FROM `" . DB_PREFIX . "categories` as ca 
		LEFT JOIN `" . DB_PREFIX . "languages` as la ON la.id = ca.id_lang 
		LEFT JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = ca.id_blog 
		LEFT JOIN `" . DB_PREFIX . "sites` as s ON s.id = ca.id_site 
		WHERE 1=1 AND ca.id_parent = 0" . ( $showAllSites ? null : " AND ca.id_site = " . $Admin->GetSite() ) . ( $showAll ? null : " AND ca.id_lang = " . $Admin->GetLang() . " AND ca.id_blog = " . $Admin->GetBlog() ) . " 
		ORDER BY ca.is_default, ca." . $orderBy . " " . $order )->all();

		$cats = array();
		
		if ( !empty( $tmp ) )
		{
			$i = 0;
			
			$siteUrl = $Admin->SiteUrl();
			
			foreach ( $tmp as $cat )
			{
				$url 	= BuildCategoryUrl( $cat, $cat['ls'] );
				$trans 	= CategoryTrans( $cat, $cat['ls'], $siteUrl, $cat['lc'] );
			
				$cats[$i] = array(
					'id'	 			=> $cat['id'],
					'name' 				=> stripslashes( $cat['name'] ),
					'descr' 			=> stripslashes( $cat['descr'] ),
					'slug' 				=> $cat['sef'],
					'siteId' 			=> $cat['id_site'],
					'blogName' 			=> stripslashes( $cat['bn'] ),
					'siteName' 			=> stripslashes( $cat['st'] ),
					'blogSef' 			=> $cat['bs'],
					'blogId' 			=> $cat['id_blog'],
					'transParent' 		=> $cat['id_trans_parent'],
					'items' 			=> $cat['numItems'],
					'color' 			=> $cat['cat_color'],
					'lang' 				=> ( $cat['lt'] ),
					'langId' 			=> $cat['id_lang'],
					'langCode' 			=> $cat['ls'],
					'hiddenFrontPage' 	=> $cat['hide_front'],
					'hiddenBlogPage' 	=> $cat['hide_blog'],
					'isDefault' 		=> ( $cat['is_default'] ? true : false ),
					'url' 				=> $url,
					'trans' 			=> $trans,
					'childs' 			=> array()
				);
				
				$subs = $this->db->from( null, "SELECT ca.id, ca.name, ca.sef, ca.descr, ca.cat_color, ca.hide_front, ca.id_trans_parent, ca.id_lang, ca.num_items as numItems, la.code as ls, la.title as lt, la.locale as lc, b.name as bn, b.sef as bs, s.url, s.title as st
				FROM `" . DB_PREFIX . "categories` as ca
				LEFT JOIN `" . DB_PREFIX . "languages` as la ON la.id = ca.id_lang
				LEFT JOIN `" . DB_PREFIX . "blogs` as b ON b.id_blog = ca.id_blog
				LEFT JOIN `" . DB_PREFIX . "sites` as s ON s.id = ca.id_site
				WHERE ca.id_parent = " . $cat['id'] . "
				ORDER BY ca." . $orderBy . " " . $order )->all();

				if ( $subs )
				{
					foreach( $subs as $sub )
					{
						$url = BuildCategoryUrl( $sub, $cat['ls'], false, true );
			
						$trans = CategoryTrans( $sub, $cat['ls'], $siteUrl, $sub['lc'] );
							
						$cats[$i]['childs'][] = array(
							'id' => $sub['id'],
							'name' => stripslashes( $sub['name'] ),
							'descr' => stripslashes( $sub['descr'] ),
							'slug' => $sub['sef'],
							'blogName' => stripslashes( $sub['bn'] ),
							'blogSef' => $sub['bs'],
							'blogId' => $cat['id_blog'],
							'transParent' => $cat['id_trans_parent'],
							'items' => $sub['numItems'],
							'color' => $sub['cat_color'],
							'lang' => ( $sub['lt'] ),
							'langId' => $sub['id_lang'],
							'langCode' => $sub['ls'],
							'hiddenFrontPage' => $sub['hide_front'],
							'isDefault' => false,
							'url' => $url,
							'trans' => $trans
						);
					}
				}
				
				$i++;
			}
			
			unset( $tmp );
		}
		
		Theme::SetVariable( 'headerTitle', __( 'categories' ) . ' | ' . $Admin->SiteName() );

		$this->setVariable( "cats", $cats );
		$this->setVariable( "langs", $langs );
		$this->setVariable( "showAllSites", $showAllSites );
		$this->setVariable( "showAll", $showAll );
		$this->setVariable( "currentUrl", $currentUrl );
		$this->setVariable( "arrow", $arrow );
		$this->setVariable( "order", $order );
		$this->setVariable( "orderBy", $orderBy );
	}
}