<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Get Filters function
#
#####################################################
function GetFilters( $id, $langId, $type = 'category', $siteId = SITE_ID )
{
	$db = db();
	
	//Query: filter group
	$f = $db->from( null, "
	SELECT id, sef, name, id_lang, group_data
	FROM `" . DB_PREFIX . "filter_group`
	WHERE (id_site = " . $siteId . ")
	ORDER BY group_order ASC"
	)->all();
		
	if ( !$f )
		return null;
		
	$arr = array();
		
	foreach ( $f as $a )
	{
		$data = ( !empty( $a['group_data'] ) ? Json( $a['group_data'] ) : array() );
			
		//We can't continue if we have nothing to work with
		if ( empty( $data ) )
		{
			continue;
		}
			
		//Don't show it if we have a valid option
		if ( !isset( $data['show-element-target'] ) || empty( $data['show-element-target']['id'] ) || empty( $data['show-element-if'] ) )
		{
			continue;
		}
			
		//Don't show it here, we want only filters for a category
		elseif ( ( $type == 'category' ) && ( $data['show-element-if'] !== 'category' ) )
		{
			continue;
		}
		
		//Don't show it here, we want only filters for a tag
		elseif ( ( $type == 'tag' ) && ( $data['show-element-if'] !== 'tag' ) )
		{
			continue;
		}

		//Don't show it here, we want only filters for a particular category
		elseif ( ( $type == 'category' ) && ( $data['show-element-if'] == 'category' ) && !empty( $data['show-element-target']['id'] ) )
		{
			if ( ( $data['show-element-option'] == 'is-not-equal' ) && ( $data['show-element-target']['target'] == 'category' ) &&
				( $data['show-element-target']['id'] == $id )
			)
			{
				continue;
			}
		}

		//Don't show it here, we want only filters for a particular category
		elseif ( ( $type == 'category' ) && !empty( $data['hide-element-if'] ) && ( $data['hide-element-if'] == 'category' ) 
				&& !empty( $data['hide-element-target']['id'] ) )
		{
			if ( ( $data['hide-element-option'] == 'is-equal' ) && ( $data['hide-element-target']['target'] == 'category' ) &&
				( $data['hide-element-target']['id'] == $id )
			)
			{
				continue;
			}
		}
		
		//Don't show it here, we want only filters for a particular tag
		elseif ( ( $type == 'tag' ) && ( $data['show-element-if'] == 'tag' ) && !empty( $data['show-element-target']['id'] ) )
		{
			if ( ( $data['show-element-option'] == 'is-not-equal' ) && ( $data['show-element-target']['target'] == 'tag' ) &&
				( $data['show-element-target']['id'] == $id )
			)
			{
				continue;
			}
		}
		
		//Don't show it here, we want only filters for a particular tag
		elseif ( ( $type == 'tag' ) && !empty( $data['hide-element-if'] ) && ( $data['hide-element-if'] == 'tag' ) 
				&& !empty( $data['hide-element-target']['id'] ) )
		{
			if ( ( $data['hide-element-option'] == 'is-equal' ) && ( $data['hide-element-target']['target'] == 'tag' ) &&
				( $data['hide-element-target']['id'] == $id )
			)
			{
				continue;
			}
		}

		$name = $a['name'];
			
		if ( ( $a['id_lang'] != $langId ) && !empty( $data ) && isset( $data['trans'] ) && !empty( $data['trans'] ) )
		{
			$name = ( isset( $data['trans']['lang-' . $langId] ) ? $data['trans']['lang-' . $langId]['name'] : $name );
		}
			
		$arr[$a['sef']] = array(
				'id' 		=> $a['id'],
				'name' 		=> $name,
				'type' 		=> $data['type'],
				'stock' 	=> $data['stock-status'],
				'target' 	=> ( isset( $data['target'] ) ? $data['target'] : null ),
				'targetId' 	=> ( ( isset( $data['target-att'] ) && !empty( $data['target-att'] ) ) ? $data['target-att'] : null ),
				'filters' 	=> array()
		);

		//Get the source
		if ( !empty( $data['source'] ) )
		{
			//Get the filters
			if ( $data['source'] === 'custom-filters' )
			{
				//Query: filter data
				$fs = $db->from( null, "
				SELECT id, filter_name, trans_data
				FROM `" . DB_PREFIX . "filters_data`
				WHERE (id_group = " . $a['id'] . ")
				ORDER BY filter_order ASC"
				)->all();

				if ( $fs )
				{
					foreach( $fs as $s )
					{
						$name = $s['filter_name'];

						$trans = ( !empty( $s['trans_data'] ) ? Json( $s['trans_data'] ) : array() );

						if ( ( $a['id_lang'] != $langId ) && !empty( $trans ) )
						{
							$name = ( isset( $trans['lang-' . $langId] ) ? $trans['lang-' . $langId]['name'] : $name );
						}

						$arr[$a['sef']]['filters'][] = array(
							'id' 	=> $s['id'],
							'name' 	=> $name
						);
					}
				}
			}
			
			//Get the categories
			elseif ( $data['source'] === 'category' )
			{
				$all 	= ( ( $data['category-display'] === 'all' ) ? true : false );
				//$catId  = ( ( (int) $data['category'] > 0 ) ? (int) $data['category'] : null );
				$parent = ( ( $data['category-display'] === 'parent' ) ? true : false );

				$order 	= ( ( $data['category-order'] === 'name' ) ? "ca.name" : ( ( $data['category-order'] === 'order' ) ? "ca.id" : "ca.num_items" ) );
					
				$where  = "ca.id_parent = '0' AND ca.id_lang = '" . $langId . "'";
				
				$arrange = ( ( $data['category-arrange'] === 'asc' ) ? "ASC" : "DESC" );
					
				//Let's see if we want all the categories
				if ( $all )
				{
					//Query: categories
					$cats = $db->from( null, "
					SELECT ca.*, la.code as ls, la.locale as lc
					FROM `" . DB_PREFIX . "categories` AS ca
					LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = ca.id_lang
					WHERE " . $where . "
					ORDER BY " . $order . ' ' . $arrange
					)->all();
				}
				//Then we should show/hide some categories
				else
				{
					$display = $hide = null;
					
					if ( ( $data['category-display'] === 'selected' ) && is_array( $data['categories-included'] ) && !empty( $data['categories-included'] ) )
					{
						$display = $data['categories-included'];
					}
					
					if ( ( $data['category-display'] === 'except' ) && is_array( $data['categories-excluded'] ) && !empty( $data['categories-excluded'] ) )
					{
						$hide = $data['categories-excluded'];
					}

					//$where .= ( $catId ? " AND ca.id = :cat" : "" );
					$where .= ( $display ? " AND ca.id IN (" . implode(',', $display ) . ")" : "" );
					$where .= ( $hide 	 ? " AND ca.id NOT IN (" . implode(',', $hide ) . ")" : "" );
					
					//Query: categories
					$cats = $db->from( null, "
					SELECT ca.*, la.code as ls, la.locale as lc
					FROM `" . DB_PREFIX . "categories` AS ca
					LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = ca.id_lang
					WHERE " . $where . "
					ORDER BY " . $order . ' ' . $arrange
					)->all();
				}
				
				$cats = Query( $query, true );
				
				if ( $cats )
				{
					foreach( $cats as $cat )
					{
						$catUrl = BuildCategoryUrl( $cat, $cat['ls'] );
						
						$trans = CategoryTrans( $cat, $cat['ls'], Router::GetVariable( 'siteRealUrl' ), $cat['lc'] );
						
						$arr[$a['sef']]['filters'][$cat['id']] = array(
								'id' => $cat['id'],
								'name' => stripslashes( $cat['name'] ),
								'description' => stripslashes( $cat['descr'] ),
								'groups' => ( !empty( $cat['groups_data'] ) ? Json( $cat['groups_data'] ) : null ),
								'url' => $catUrl,
								'childs' => array(),
								'trans' => $trans
						);

						if ( !$parent )
						{
							$order 	= ( ( $data['category-order'] === 'name' ) ? "name" : ( ( $data['category-order'] === 'order' ) ? "id" : "num_items" ) );
							
							//Get the subcategories, if any
							//Query: categories
							$subs = $db->from( null, "
							SELECT *
							FROM `" . DB_PREFIX . "categories`
							WHERE (id_parent = " . $cat['id'] . ")
							ORDER BY " . $order . ' ' . $arrange
							)->all();

							if ( $subs )
							{
								foreach ( $subs as $sub )
								{
									$trans = CategoryTrans( $sub, $cat['ls'], Router::GetVariable( 'siteRealUrl' ), $cat['lc'] );

									$categories[$cat['id']]['childs'][] = array(
											'id' => $sub['id'],
											'name' => stripslashes( $sub['name'] ),
											'description' => stripslashes( $sub['descr'] ),
											'groups' => ( !empty( $sub['groups_data'] ) ? Json( $sub['groups_data'] ) : null ),
											'url' => $catUrl . $sub['sef'] . PS,
											'trans' => $trans
									);
								}
							}
						}
					}
				}
			}
		}
	}

	return $arr;
}