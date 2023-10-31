<?php defined('TOKICMS') or die('Hacking attempt...');

$L = $this->lang;

#####################################################
#
# Categories array
#
#####################################################
$_categories = array();

$query = array(
		'SELECT'	=>  'id, code, is_default, locale, title',

		'FROM'		=> DB_PREFIX . "languages",

		'WHERE'		=> "status = 'active' AND id_site = :site",
		
		'ORDER'		=> "lang_order ASC",

		'PARAMS' 	=> array( 'NO_PREFIX' => true ),

		'BINDS' 	=> array(
						array( 'PARAM' => ':site', 'VAR' => $this->siteID, 'FLAG' => 'INT' )
		)
);

$_langs = Query( $query, true );
	
if ( $_langs )
{
	//If the site has multiblog enabled, we need a bit more work
	if ( IsTrue( $this->adminSettings::Site()['enable_multiblog'] ) )
	{
		foreach( $_langs as $_lang )
		{
			//We need the blogs now
			$query = array(
				'SELECT'	=>  'id_blog, name',

				'FROM'		=> DB_PREFIX . "blogs",

				'WHERE'		=> "( id_lang = :lang OR id_lang = '0' ) AND id_site = :site",
				
				'ORDER'		=> "name ASC",

				'PARAMS' 	=> array( 'NO_PREFIX' => true ),

				'BINDS' 	=> array(
								array( 'PARAM' => ':lang', 'VAR' => $_lang['id'], 'FLAG' => 'INT' ),
								array( 'PARAM' => ':site', 'VAR' => $this->siteID, 'FLAG' => 'INT' )
				)
			);
			
			$_categories[$_lang['code']] = array(
										'name' => stripslashes( $_lang['title'] ),
										'id' => $_lang['id'],
										'type' => 'lang',
										'childs' => array()
			
			
			);

			$_blogs = Query( $query, true );

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
					
					$query = array(
						'SELECT' =>  "id, name",
						
						'FROM'	=> DB_PREFIX . 'categories',
						
						'PARAMS' => array( 'NO_PREFIX' => true ),
						
						'WHERE' => "id_parent = '0' AND id_lang = :lang AND id_blog = :blog",
						
						'BINDS'	=> array(
								array( 'PARAM' => ':lang', 'VAR' => $_lang['id'], 'FLAG' => 'INT' ),
								array( 'PARAM' => ':blog', 'VAR' => $_blog['id_blog'], 'FLAG' => 'INT' )
						),
					
						'ORDER'		=> 'name ASC'
					);

					$cats = Query( $query, true );
			
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
							
							$query = array(
								'SELECT' =>  "id, name",
									
								'FROM'	=> DB_PREFIX . 'categories',
									
								'PARAMS' => array( 'NO_PREFIX' => true ),
									
								'WHERE' => "id_parent = :cat",
									
								'BINDS'	=> array(
										array( 'PARAM' => ':cat', 'VAR' => $cat['id'], 'FLAG' => 'INT' )
								),
								
								'ORDER'		=> 'name ASC'
							);

							$subCats = Query( $query, true );
					
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
			
			$query = array(
					'SELECT' =>  "id, name",
						
					'FROM'	=> DB_PREFIX . 'categories',
						
					'PARAMS' => array( 'NO_PREFIX' => true ),
						
					'WHERE' => "id_parent = '0' AND id_lang = :lang AND id_blog = '0'",
						
					'BINDS'	=> array(
							array( 'PARAM' => ':lang', 'VAR' => $_lang['id'], 'FLAG' => 'INT' )
					),
					
					'ORDER'		=> 'name ASC'
			);

			$_cats = Query( $query, true );
			
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
					
					$query = array(
						'SELECT' =>  "id, name",
							
						'FROM'	=> DB_PREFIX . 'categories',
							
						'PARAMS' => array( 'NO_PREFIX' => true ),
							
						'WHERE' => "id_parent = :cat",
							
						'BINDS'	=> array(
								array( 'PARAM' => ':cat', 'VAR' => $_cat['id'], 'FLAG' => 'INT' )
						),
						
						'ORDER'		=> 'name ASC'
					);

					$subCats = Query( $query, true );
					
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
		
		unset( $_blogs );
	}
	
	else
	{
		foreach( $_langs as $_lang )
		{
			$query = array(
					'SELECT' =>  "id, name",
						
					'FROM'	=> DB_PREFIX . 'categories',
						
					'PARAMS' => array( 'NO_PREFIX' => true ),
						
					'WHERE' => "id_parent = '0' AND id_lang = :lang AND id_blog = '0'",
						
					'BINDS'	=> array(
							array( 'PARAM' => ':lang', 'VAR' => $_lang['id'], 'FLAG' => 'INT' )
					),
					
					'ORDER'		=> 'name ASC'
			);

			$_cats = Query( $query, true );
			
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
					
					$query = array(
						'SELECT' =>  "id, name",
							
						'FROM'	=> DB_PREFIX . 'categories',
							
						'PARAMS' => array( 'NO_PREFIX' => true ),
							
						'WHERE' => "id_parent = :cat",
							
						'BINDS'	=> array(
								array( 'PARAM' => ':cat', 'VAR' => $_cat['id'], 'FLAG' => 'INT' )
						),
						
						'ORDER'		=> 'name ASC'
					);

					$subCats = Query( $query, true );
					
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

unset( $_langs, $subCats, $_cats );

#####################################################
#
# Edit Video Playlist Form
#
#####################################################
if ( !is_null ( $this->currentAction ) && ( $this->currentAction == 'edit-playlist' ) )
	$playlistData = AdminGetVideoPlaylist( Router::GetVariable( 'key' ) );
else
	$playlistData = null;

$form = array
(
	'edit-playlist' => array
	(
		'title' => $L['edit-video-playlist'],
		'data' => array(
		
			'playlist-settings' => array( 
				'title' => null, 'data' => array
				(
					'playlist-title'=>array('label'=>$L['title'], 'type'=>'text', 'name' => 'title', 'value' =>( $playlistData ? $playlistData['title'] : null ), 'required' => true, 'tip'=>$L['the-title-how-it-appears']),
					'playlist-desc'=>array('label'=>$L['description'], 'type'=>'textarea', 'name' => 'descr', 'value' =>( $playlistData ? $playlistData['descr'] : null ), 'tip'=>null ),
					//'source-category'=>array( 'label'=>$L['category'], 'type'=>'select-group-multi', 'name' => 'category', 'value'=>( $playlistData ? $playlistData['id_category'] : null ), 'firstNull' => true, 'data' => $_categories, 'tip'=>$L['source-category-tip'] )
				)
			)
		)
	)
);

unset( $_categories, $playlistData );