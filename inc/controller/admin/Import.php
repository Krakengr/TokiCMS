<?php defined('TOKICMS') or die('Hacking attempt...');

class Import extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );
		
		@set_time_limit(600);
		@ini_set('mysql.connect_timeout', -1);
		@ini_set('default_socket_timeout', 900);
		@ini_set('memory_limit', '512M');

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin, $L;
		
		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'import-content' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		include ( ARRAYS_ROOT . 'generic-arrays.php');
		
		Theme::SetVariable( 'headerTitle', __( 'import-content' ) . ' | ' . $Admin->SiteName() );
		
		$_categories = array();
		
		$_langs = $this->db->from( 
		null, 
		"SELECT id, code, is_default, locale, title
		FROM `" . DB_PREFIX . "languages`
		WHERE (id_site = " . $Admin->GetSite() . ") AND (status = 'active')
		ORDER BY lang_order ASC"
		)->all();
	
		if ( $_langs )
		{
			//If the site has multiblog enabled, we need a bit more work
			if ( $Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) )
			{
				foreach( $_langs as $_lang )
				{
					//We need the blogs now
					$_blogs = $this->db->from( 
					null, 
					"SELECT id_blog, name
					FROM `" . DB_PREFIX . "blogs`
					WHERE (id_site = " . $Admin->GetSite() . ") AND (id_lang = " . $_lang['id'] . ")
					ORDER BY name ASC"
					)->all();
					
					$_categories[$_lang['code']] = array(
						'name' => stripslashes( $_lang['title'] ),
						'id' => $_lang['id'],
						'type' => 'lang',
						'childs' => array()
					);

					if ( $_blogs )
					{
						foreach( $_blogs as $_blog )
						{
							$_categories[$_lang['code']]['childs'][$_blog['id_blog']] = array(
								'name' => stripslashes( $_blog['name'] ),
								'id' => $_blog['id_blog'],
								'type' => 'blog',
								'childs' => array()
							
							);
							
							$cats = $this->db->from( 
							null, 
							"SELECT id, name
							FROM `" . DB_PREFIX . "categories`
							WHERE (id_parent = 0) AND (id_blog = " . $_blog['id_blog'] . ") AND (id_lang = " . $_lang['id'] . ")
							ORDER BY name ASC"
							)->all();

							if ( $cats )
							{
								foreach ( $cats as $cat )
								{
									$_categories[$_lang['code']]['childs'][$_blog['id_blog']]['childs'][$cat['id']] = array(
										'name' => stripslashes( $cat['name'] ),
										'id' => $cat['id'],
										'type' => 'cat',
										'childs' => array()
									);
									
									$subCats = $this->db->from( 
									null, 
									"SELECT id, name
									FROM `" . DB_PREFIX . "categories`
									WHERE (id_parent = " . $cat['id'] . ")
									ORDER BY name ASC"
									)->all();

									if ( $subCats )
									{
										foreach ( $subCats as $sub )
										{
											$_categories[$_lang['code']]['childs'][$_blog['id_blog']]['childs'][$cat['id']]['childs'][$sub['id']] = array(
												'name' => stripslashes( $sub['name'] ),
												'type' => 'sub',
												'id' => $sub['id'],
											);
										}
									}
								}
							}
						}
					}
					
					$_cats = $this->db->from( 
					null, 
					"SELECT id, name
					FROM `" . DB_PREFIX . "categories`
					WHERE (id_parent = 0) AND (id_blog = 0) AND (id_lang = " . $_lang['id'] . ")
					ORDER BY name ASC"
					)->all();

					$_categories[$_lang['code']]['childs']['orphanCats'] = array(
						'name' => $L['orphan-categories'],
						'type' => 'blog',
						'id' => '0',
						'childs' => array()
					);
					
					if ( $_cats )
					{
						foreach ( $_cats as $_cat )
						{
							$_categories[$_lang['code']]['childs']['orphanCats']['childs'][$_cat['id']] = array(
								'name' => stripslashes( $_cat['name'] ),
								'type' => 'cat',
								'id' => $_cat['id'],
								'childs' => array()
							);
							
							$subCats = $this->db->from( 
							null, 
							"SELECT id, name
							FROM `" . DB_PREFIX . "categories`
							WHERE (id_parent = " . $_cat['id'] . ")
							ORDER BY name ASC"
							)->all();

							if ( $subCats )
							{
								foreach ( $subCats as $sub )
								{
									$_categories[$_lang['code']]['childs']['orphanCats']['childs'][0]['childs'][$sub['id']] = array(
										'name' => stripslashes( $sub['name'] ),
										'type' => 'sub',
										'id' => $sub['id']
									);
								}
							}
						}
					}
				}
			}
			
			else
			{
				foreach( $_langs as $_lang )
				{
					$_cats = $this->db->from( 
					null, 
					"SELECT id, name
					FROM `" . DB_PREFIX . "categories`
					WHERE (id_parent = 0) AND (id_blog = 0) AND (id_lang = " . $_lang['id'] . ")
					ORDER BY name ASC"
					)->all();

					$_categories[$_lang['code']]['childs']['orphanCats'] = array(
						'name' => $L['orphan-categories'],
						'type' => 'blog',
						'id' => '0',
						'childs' => array()
							
					);
					
					if ( $_cats )
					{
						foreach ( $_cats as $_cat )
						{
							$_categories[$_lang['code']]['childs']['orphanCats']['childs'][$_cat['id']] = array(
								'name' => stripslashes( $_cat['name'] ),
								'type' => 'cat',
								'id' => $_cat['id'],
								'childs' => array()
							
							);
							
							$subCats = $this->db->from( 
							null, 
							"SELECT id, name
							FROM `" . DB_PREFIX . "categories`
							WHERE (id_parent = " . $_cat['id'] . ")
							ORDER BY name ASC"
							)->all();

							if ( $subCats )
							{
								foreach ( $subCats as $sub )
								{
									$_categories[$_lang['code']]['childs']['orphanCats']['childs'][0]['childs'][$sub['id']] = array(
										'name' => stripslashes( $sub['name'] ),
										'type' => 'sub',
										'id' => $sub['id']
									);
								}
							}
						}
					}
				}
			}
		}

		$importSystems = $customTypes = $postTypes = array();

		$postTypes['default'] = array( 'name' => 'default', 'title'=> $L['default-as-it-is'], 'disabled' => false, 'data' => array() );
		$postTypes['post'] = array( 'name' => 'post', 'title'=> $L['post'], 'disabled' => false, 'data' => array() );
		$postTypes['page'] = array( 'name' => 'page', 'title'=> $L['page'], 'disabled' => false, 'data' => array() );

		foreach( $importDataArray as $key => $row )
		{
			$importSystems[$key] = array( 'name' => $key, 'title'=> $row['title'], 'disabled' => false, 'data' => array() );
		}

		$types = $this->db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . "post_types`
		WHERE (id_site = " . $Admin->GetSite() . ")
		ORDER BY title ASC"
		)->all();

		if ( $types )
		{
			foreach( $types as $type )
			{
				$customTypes[$type['id']] = array( 'name' => $type['id'], 'title'=> $type['title'], 'disabled' => false, 'data' => array() );
			}
		}
		
		$Blogs = ( $Admin->MultiBlog() ? $Admin->GetBlogs() : null );
		
		$this->setVariable( 'Blogs', $Blogs );
		$this->setVariable( 'importSystems', $importSystems );
		$this->setVariable( 'postTypes', $postTypes );
		$this->setVariable( 'Categories', $_categories );
		$this->setVariable( 'customTypes', $customTypes );
		
		unset( $_langs, $subCats, $_cats, $customTypes, $_categories, $postTypes, $Blogs );

		//Don't do anything if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;
	}
}