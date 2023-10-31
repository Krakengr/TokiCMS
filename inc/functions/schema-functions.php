<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Build Schema function
#
#####################################################
function SchemaDataToArray( $data, $post )
{
	include( ARRAYS_ROOT . 'seo-arrays.php');
	include( ARRAYS_ROOT . 'generic-arrays.php');
	
	$schema = ( !empty( $post['schemaData'] ) ? $post['schemaData'] : array() );
	$repr 	= ( ( !empty( $schema['site_represents'] ) && isset( $schemaRepresents[$schema['site_represents']] ) ) ? $schemaRepresents[$schema['site_represents']]['name'] : 'WebPage' );

	$arr = array(
		'@context' => 'http://schema.org'
	);
	
	if ( !empty( $data['article-type'] ) )
	{
		$arr['@type'] = ( isset( $schemaArticleTypesArray[$data['article-type']] ) ? $schemaArticleTypesArray[$data['article-type']]['property'] : ucfirst( $data['article-type'] ) );
	}
	
	if ( !empty( $data['page-url'] ) )
	{
		$tmp = array();
			
		if ( $data['page-url'] == 'post-url' )
		{
			$tmp = array(
				'@type' => $repr,
				'@id' 	=> $post['postUrl']
			);
		}
		
		elseif ( ( $data['page-url'] == 'custom-text' ) && isset( $data['custom']['page-url'] ) )
		{
			$tmp = array(
				'@type' => $repr,
				'@id' 	=> $data['custom']['page-url']
			);
		}
		
		if ( !empty( $tmp ) )
		{
			$arr['mainEntityOfPage'] = $tmp;
		}
	}

	//Add the Headline
	if ( !empty( $data['headline'] ) )
	{
		$tmp = '';
			
		if ( $data['headline'] == 'post-title' )
		{
			$tmp = $post['title'];
		}
		
		if ( !empty( $tmp ) )
		{
			$arr['headline'] = $tmp;
		}
	}
	
	//Add the alternative headline
	if ( !empty( $data['alternative-headline'] ) )
	{
		$tmp = '';
			
		if ( ( $data['alternative-headline'] == 'post-subtitle' ) && !empty( $post['subTitle'] ) )
		{
			$tmp = $post['subTitle'];
		}
		
		if ( !empty( $tmp ) )
		{
			$arr['alternativeHeadline'] = $tmp;
		}
	}
		
	//Add the description
	if ( !empty( $data['description'] ) )
	{
		$tmp = '';
			
		if ( $data['description'] == 'post-description' )
		{
			$tmp = $post['description'];
		}
		
		if ( !empty( $tmp ) )
		{
			$arr['description'] = $tmp;
		}
	}
	
	//Add the article body
	if ( !empty( $data['article-body'] ) )
	{
		$tmp = '';
			
		if ( $data['article-body'] == 'post-content' )
		{
			$tmp = htmlentities( $post['content'] );
		}
		
		if ( !empty( $tmp ) )
		{
			$arr['articleBody'] = $tmp;
		}
	}
		
	//Add the published date
	if ( !empty( $data['published-date'] ) )
	{
		$tmp = '';
			
		if ( $data['published-date'] == 'publish-date' )
		{
			$tmp = $post['added']['c'];
		}
		
		if ( !empty( $tmp ) )
		{
			$arr['datePublished'] = $tmp;
		}
	}
		
	//Add the modified date
	if ( !empty( $data['modified-date'] ) )
	{
		$tmp = null;
			
		if ( ( $data['modified-date'] == 'last-modified-date' ) && !empty( $post['updated']['c'] ) )
		{
			$tmp = $post['updated']['c'];
		}
			
		if ( !empty( $tmp ) )
		{
			$arr['dateModified'] = $tmp;
		}
	}
		
	//Add the published
	if ( !empty( $data['publisher-name'] ) )
	{
		$tmp = null;
			
		if ( $data['publisher-name'] == 'site-title' )
		{
			$tmp = array(
				'@type' => 'Organization',
				'name' 	=> $post['site']['name']
			);
		}
			
		if ( $data['author-name'] == 'author-name' )
		{
			$tmp = array(
				'@type' => 'Organization',
				'name' 	=> $post['author']['name']
			);
		}
			
		//Add the publisher logo
		if ( !empty( $data['publisher-logo'] ) )
		{
			if ( ( $data['publisher-logo'] == 'site-logo' ) && !empty( $post['site']['image']['default'] ) )
			{
				$tmp['logo'] = array(
					'@type' 	=> 'ImageObject',
					'url' 		=> $post['site']['image']['default']['url'],
					'height' 	=> $post['site']['image']['default']['height'],
					'width' 	=> $post['site']['image']['default']['width']
				);
			}
	
			elseif ( ( $data['publisher-logo'] == 'post-featured-image' ) && !empty( $post['coverImage']['default']['imageUrl'] ) )
			{
				$tmp['logo'] = array(
					'@type' 	=> 'ImageObject',
					'url' 		=> $post['coverImage']['default']['imageUrl'],
					'height' 	=> $post['coverImage']['default']['imageHeight'],
					'width' 	=> $post['coverImage']['default']['imageWidth']
				);
			}
			
			elseif ( ( $data['publisher-logo'] == 'author-image' ) && !empty( $post['author']['coverImg']['default']['imageUrl'] ) )
			{
				$tmp['logo'] = array(
					'@type' 	=> 'ImageObject',
					'url' 		=> $post['author']['coverImg']['default']['imageUrl'],
					'height' 	=> $post['author']['coverImg']['default']['imageHeight'],
					'width' 	=> $post['author']['coverImg']['default']['imageWidth']
				);
			}
		}
			
		if ( !empty( $tmp ) )
		{
			$arr['publisher'] = $tmp;
		}
	}
		
	//Add the author
	if ( !empty( $data['author-name'] ) )
	{
		$tmp = null;
			
		if ( $data['author-name'] == 'site-title' )
		{
			$tmp = array(
				'@type' => 'Person',
				'name' 	=> $post['site']['name'],
				'url' 	=> $post['site']['url']
			);
		}
			
		if ( $data['author-name'] == 'author-name' )
		{
			$tmp = array(
				'@type' => 'Person',
				'name' 	=> $post['author']['name'],
				'url' 	=> $post['site']['url']
			);
		}
			
		if ( !empty( $tmp ) )
		{
			$arr['author'] = $tmp;
		}
	}
		
	//Add the image
	if ( !empty( $data['image'] ) )
	{
		$tmp = array();

		if ( ( $data['image'] == 'image-url' ) && !empty( $post['coverImage']['default']['imageUrl'] ) )
		{
			$tmp = array(
				'@type' 	=> 'ImageObject',
				'url' 		=> $post['coverImage']['default']['imageUrl'],
				'height' 	=> $post['coverImage']['default']['imageHeight'],
				'width' 	=> $post['coverImage']['default']['imageWidth']
			);
		}
			
		if ( !empty( $tmp ) )
		{
			$arr['image'] = $tmp;
		}
	}
		
	return $arr;
}

//Convert a given array into HTML function
function SchemaDataToHtml( $data = null, $postLoaded = false, $schemaId = 0, $_schemaData = null )
{
	if ( !$data )
		return null;
	
	$i = 0;
	
	$_schemaData = ( $postLoaded && $_schemaData ? Json( $_schemaData['data'] ) : null );
	
	$schemaData = ( $postLoaded && $_schemaData && isset( $_schemaData['data'] ) ? $_schemaData['data'] : null );
	$schemaCustonData = ( $postLoaded && $_schemaData && isset( $_schemaData['custom-data'] ) ? $_schemaData['custom-data'] : null );
	
	unset( $_schemaData );

	$html = '<div class="row mb-3">' . PHP_EOL;
	
	foreach( $data as $id => $row )
	{
		$i++;
		
		if ( $i == 1 )
		{
			$html .= '<div class="col-lg-6">' . PHP_EOL;
		}

		$html .= '<div class="form-group">' . PHP_EOL;
		$html .= '	<label for="' . $id . '">' . $row['label'] . ( $row['tip'] ? ' <span type="button" data-toggle="tooltip" data-placement="top" title="' . str_replace( '"', "''", $row['tip'] ) . '"><i class="me-2 mdi mdi-information-outline"></i></span>' : '' ) . '</label>' . PHP_EOL;
		
		/*if ( $postLoaded && !empty( $row['value'] ) &&
				( ( $row['value'] == 'custom-text' ) || ( $row['value'] == 'custom-date' ) || ( $row['value'] == 'custom-number' ) )
		)
		{
			if ( $row['value'] == 'custom-text' )
				$html .= '	<input type="text" class="form-control" id="' . $id . '" name="schema[' . $schemaId . '][custom][' . $id . ']" value="">' . PHP_EOL;
			
			elseif ( $row['value'] == 'custom-date' )
				$html .= '	<input type="text" name="schema[' . $schemaId . '][custom][' . $id . ']" class="form-control postDatepicker" value="' . date( 'm/d/Y', time() ) . '" id="postDatepicker" placeholder="mm/dd/Y">';
				
			elseif ( $row['value'] == 'custom-number' )
				$html .= '	<input value="2" class="form-control" type="number" name="schema[' . $schemaId . '][custom][' . $id . ']" step="any" min="0">';
				
		}
		
		else
		{*/
			if ( $row['type'] == 'text' )
				$html .= '	<input type="text" class="form-control" id="' . $id . '" name="' . ( $postLoaded ? 'schema[' . $schemaId . '][' . $id . ']' : $row['name'] ) . '" value="' . $row['value'] . '" disabled >' . PHP_EOL;
			
			if ( $row['type'] == 'textarea' )
				$html .= '	<textarea class="form-control" id="' . $id . '" name="' . ( $postLoaded ? 'schema[' . $schemaId . '][' . $id . ']' : $row['name'] ) . '" rows="3">' . $row['value'] . '</textarea>' . PHP_EOL;
			
			if ( $row['type'] == 'select-group' )
			{
				$html .= '<select ' . ( isset( $row['disabled'] ) && $row['disabled'] ? 'disabled' : '' ) . ' name="' . ( $postLoaded ? 'schema[' . $schemaId . '][' . $id . ']' : $row['name'] ) . '" class="form-control ' . ( isset( $row['class'] ) ? $row['class'] : '' ) . '" id="schema-' . ( isset( $row['id'] ) ? $row['id'] : $id ) . '" ' . ( isset( $row['extraKeys'] ) && !empty($row['extraKeys'] ) ? $row['extraKeys']['name'] . '= "' . $row['extraKeys']['data'] . '"' : '' ) . '>' . PHP_EOL;
				
				if ( $row['firstNull'] )
					$html .= '<option value="">' . __( 'choose' ) . '...</option>' . PHP_EOL;

				if ( !empty( $row['data'] ) )
				{
					foreach( $row['data'] as $id_ => $r )
					{
		
						$html .= '<optgroup label="' . $r['name'] . '">' . PHP_EOL;
						
						if ( !empty( $r['data'] ) )
						{
							foreach( $r['data'] as $id__ => $option )
							{
								$html .= '<option value="' . $option['name'] . '" ' . ( ( isset( $option['disabled'] ) && $option['disabled'] ) ? 'disabled' : '' ) . ' ' . ( ( $row['value'] == $option['name'] ) ? 'selected' : '' ) . ' ' . ( isset( $option['data'] ) && !empty( $option['data'] ) ? $option['data']['name'] . '="' . $option['data']['value'] . '"' : '' ) . '>' . $option['title'] . '</option>' . PHP_EOL;
							}
						}
					}
				}
				
				$html .= '</select>' . PHP_EOL;
			}
			
			if ( $row['type'] == 'select-group-multi' )
			{
				$html .= 'Multi';
				/*<select <?php echo ( isset( $key['disabled'] ) && $key['disabled'] ? 'disabled' : '' ) ?> name="<?php echo $key['name'] ?>" class="form-control <?php echo ( isset( $key['class'] ) ? $key['class'] : '' ) ?>" id="<?php echo ( isset( $key['id'] ) ? $key['id'] : $id_ ) ?>" <?php echo ( isset( $key['extraKeys'] ) && !empty($key['extraKeys'] ) ? $key['extraKeys']['name'] . '= "' . $key['extraKeys']['data'] . '"' : '' ) ?>>
						<?php if ( $key['firstNull'] ) : ?>
							<option value=""><?php echo $L['choose'] ?>...</option>
						<?php endif ?>
						<?php if ( !empty( $key['data'] ) ) : ?>
							<?php foreach( $key['data'] as $id___ => $row ) : ?>
								<optgroup label="<?php echo $row['name'] ?>">
								<?php if ( !empty( $row['childs'] ) ) : ?>
									<?php foreach( $row['childs'] as $id____ => $childs1 ) : ?>
										<?php if ( !empty( $childs1['childs'] ) ) : ?>
											<?php if ( $childs1['type'] == 'blog' ) : ?>
												<optgroup label="&nbsp;-<?php echo $childs1['name'] ?>">
											<?php endif ?>
											<?php foreach( $childs1['childs'] as $id_____ => $childs2 ) : ?>
												<option value="<?php echo $childs2['id'] ?>" <?php echo ( ( $key['value'] == $childs2['id'] ) ? 'selected' : '' ) ?>>&nbsp;&nbsp;<?php echo ( !empty( $childs2['childs'] ) ? '<strong>' . $childs2['name'] . '</strong>': $childs2['name'] ) ?></option>
											
												<?php if ( !empty( $childs2['childs'] ) ) : ?>
													<?php foreach( $childs2['childs'] as $id______ => $child ) : ?>
														<option value="<?php echo $child['id'] ?>" <?php echo ( ( $key['value'] == $child['id'] ) ? 'selected' : '' ) ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;---<?php echo $child['name'] ?></option>
													<?php endforeach ?>
												<?php endif ?>
											<?php endforeach ?>
										<?php endif ?>

									<?php endforeach ?>
								<?php endif ?>
						
							<?php endforeach ?>
						<?php endif ?>
						</select>*/
			}
			
			if ( $row['type'] == 'select' )
			{
				$html .= '<select ' . ( isset( $row['disabled'] ) && $row['disabled'] ? 'disabled' : '' ) . ' name="' . ( $postLoaded ? 'schema[' . $schemaId . '][' . $id . ']' : $row['name'] ) . '" class="form-control ' . ( isset( $row['class'] ) ? $row['class'] : '' ) . '" ' . ( isset( $row['multiple'] ) && $row['multiple'] ? 'multiple' : '' ) . ' id="schema-' . ( isset( $row['id'] ) ? $row['id'] : $id ) . '" ' . ( isset( $row['extraKeys'] ) && !empty($row['extraKeys'] ) ? $row['extraKeys']['name'] . '= "' . $row['extraKeys']['data'] . '"' : '' ) . '>' . PHP_EOL;
				
				if ( $row['firstNull'] )
					$html .= '<option value="">' . __( 'choose' ) . '...</option>' . PHP_EOL;
					
				if ( !empty( $row['data'] ) )
				{
					foreach( $row['data'] as $id_ => $option )
					{
						$html .= '<option value="' . $option['name'] . '" ' . ( ( isset( $option['disabled'] ) && $option['disabled'] ) ? 'disabled' : '' ) . ' ' .  ( ( ( $row['value'] == $option['name'] ) && !is_array( $row['value'] ) ) ? 'selected' : '' ) . ' ' . ( ( is_array( $row['value'] ) && !empty( $row['value'] ) && in_array( $option['name'], $row['value'] ) ) ? 'selected' : '' ) . ' ' . ( isset( $option['data'] ) && !empty( $option['data'] ) ? $option['data']['name'] . '="' . $option['data']['value'] . '"' : '' ) . '>' . $option['title'] . '</option>' . PHP_EOL;
					}
				}
				
				$html .= '</select>' . PHP_EOL;
			}
			
			if ( $postLoaded && !empty( $row['value'] ) &&
				( ( $row['value'] == 'custom-text' ) || ( $row['value'] == 'custom-date' ) || ( $row['value'] == 'custom-number' ) )
		)
			{
				$html .= '	<label for="' . $id . '-' . $row['value'] . '">' . sprintf( __( 'enter-custom-data' ), __( $row['value'] ) ) . '</strong></label>' . PHP_EOL;
				
				if ( $row['value'] == 'custom-text' )
					$html .= '	<input type="text" class="form-control" id="' . $id . '" name="schema[' . $schemaId . '][custom][' . $id . ']" value="' . ( ( $schemaCustonData && isset( $schemaCustonData[$id] ) ) ? $schemaCustonData[$id] : '' ) . '">' . PHP_EOL;
				
				elseif ( $row['value'] == 'custom-date' )
					$html .= '	<input type="text" name="schema[' . $schemaId . '][custom][' . $id . ']" class="form-control postDatepicker" value="' . date( 'm/d/Y', time() ) . '" id="postDatepicker" placeholder="mm/dd/Y">';
					
				elseif ( $row['value'] == 'custom-number' )
					$html .= '	<input value="' . ( ( $schemaCustonData && isset( $schemaCustonData[$id] ) ) ? $schemaCustonData[$id] : '' ) . '" class="form-control" type="number" name="schema[' . $schemaId . '][custom][' . $id . ']" step="any" min="0">';
					
				$html .= '<hr />';
					
			}
			
			//if ( $row['tip'] )
				//$html .= '	<small class="form-text text-muted">' . $row['tip'] . '</small>' . PHP_EOL;
		//}
		
		$html .= '</div>' . PHP_EOL;
		
		if ( $i % 4 )
		{
			$html .= '</div>' . PHP_EOL;
			
			$i = 0;
		}
	}
	
	$html .= '</div>';
	
	return $html;
}

//Get a schema and build its form data function
function SingleSchemaData( $id, $data = null )
{
	global $L;
	
	$db = db();
	
	// Don't request the data if we already have it
	if ( !$data )
	{
		//Get the schema Data
		$schemaData = $db->from( 
		null, 
		"SELECT id, title, type, data
		FROM `" . DB_PREFIX . "schemas`
		WHERE (id = " . (int) $id . ")"
		)->single();
	}
	
	else
		$schemaData = $data;
	
	//We can't continue without a valid schema
	if ( !$schemaData )
		return null;

	//Get the Currencies
	$currencies = $db->from( 
	null, 
	"SELECT id, name, code
	FROM `" . DB_PREFIX . "currencies`
	ORDER BY name ASC"
	)->all();
	
	include ( ARRAYS_ROOT . 'seo-arrays.php');
	include ( ARRAYS_ROOT . 'generic-arrays.php');
	
	$schemaArticleTypes = $availabilityArray = $currenciesArray = $countriesArray = array();

	$schemaArrayData = ( !empty( $schemaData['data'] ) ? json_decode( $schemaData['data'], true ) : null );

	$schemaFieldsData = ( $schemaArrayData ? $schemaArrayData['data'] : null );

	foreach( $schemaArticleTypesArray as $id => $row )
		$schemaArticleTypes[$id] = array( 'name' => $row['name'], 'title'=> $row['title'], 'disabled' => false, 'data' => array() );
		
	foreach( $countries as $id => $row )
		$countriesArray[$id] = array( 'name' => $id, 'title'=> $row['title'], 'disabled' => false, 'data' => array() );

	foreach( $currencies as $row )
		$currenciesArray[$row['id']] = array( 'name' => $row['id'], 'title'=> $row['name'] . ' (' . $row['code'] . ')', 'disabled' => false, 'data' => array() );

	foreach( $schemaAvailabilityArray as $id => $row )
		$availabilityArray[$id] = array( 'name' => $row['name'], 'title'=> $row['title'], 'disabled' => false, 'data' => array() );
	#####################################################
	#
	# Schemas Arrays
	#
	#####################################################
	$schemaFieldsArray = array(
					'site-meta' => array( 'name' => $L['site-meta'], 'data' => array(
											'site-title' => array( 'name' => 'site-title', 'title'=> $L['site-title'], 'disabled' => false, 'data' => array() ),
											'site-slogan' => array( 'name' => 'site-slogan', 'title'=> $L['site-slogan'], 'disabled' => false, 'data' => array() ),
											'site-url' => array( 'name' => 'site-url', 'title'=> $L['site-url'], 'disabled' => false, 'data' => array() )
										)
					),
					'post-meta' => array( 'name' => $L['post-meta'], 'data' => array(
											'title' => array( 'name' => 'post-title', 'title'=> $L['title'], 'disabled' => false, 'data' => array() ),
											'content' => array( 'name' => 'post-content', 'title'=> $L['content'], 'disabled' => false, 'data' => array() ),
											'description' => array( 'name' => 'post-description', 'title'=> $L['description'], 
											'disabled' => false, 'data' => array() ),
											'subtitle' => array( 'name' => 'post-subtitle', 'title'=> $L['subtitle'], 
											'disabled' => false, 'data' => array() ),
											'url' => array( 'name' => 'post-url', 'title'=> $L['url'], 'disabled' => false, 'data' => array() ),
											'author' => array( 'name' => 'author-name', 'title'=> $L['author-name'], 'disabled' => false, 'data' => array() ),
											'publish-date' => array( 'name' => 'publish-date', 'title'=> $L['publish-date'], 'disabled' => false, 'data' => array() ),
											'last-modified-date' => array( 'name' => 'last-modified-date', 'title'=> $L['last-modified-date'], 'disabled' => false, 'data' => array() ),
										)
					)
	);
					
	$schemaImageFieldsArray = array(
					'site-meta' => array( 'name' => $L['site-meta'], 'data' => array(
											'site-logo' => array( 'name' => 'site-logo', 'title'=> $L['site-logo'], 'disabled' => false, 'data' => array() )
										)
					),
					'post-meta' => array( 'name' => $L['post-meta'], 'data' => array(
											'featured-image' => array( 'name' => 'post-featured-image', 'title'=> $L['featured-image'], 'disabled' => false, 'data' => array() ),
											'author-image' => array( 'name' => 'post-author-image', 'title'=> $L['author-image'], 'disabled' => false, 'data' => array() )
										)
					),
					
					'custom-info' => array( 'name' => $L['add-custom-info'], 'data' => array(
											'image-url' => array( 'name' => 'image-url', 'title'=> $L['image-url'], 'disabled' => false, 'data' => array() )
										)
					),
	);

	//Add a custom info to the main fields array
	$schemaFieldsArray['custom-info'] = array( 'name' => $L['add-custom-info'], 'data' => array(
											'text' => array( 'name' => 'custom-text', 'title'=> $L['text'], 'disabled' => false, 'data' => array() )
										)
	);

	#####################################################
	#
	# Edit Schema Form
	#
	#####################################################
	$schemaDataToReturn = array();

	//Article schema
	if ( $schemaData['type'] == 'article' )
	{
		$schemaDataToReturn = array(
			'article-type'=>array('label'=>$L['article-type'], 'type'=>'select', 'name' => 'schema[fields][article-type]', 'value'=>( isset( $schemaFieldsData['article-type'] ) ? $schemaFieldsData['article-type'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaArticleTypes ),
			
			'author-name'=>array('label'=>$L['author-name'], 'type'=>'select-group', 'name' => 'schema[fields][author-name]', 'value'=>( isset( $schemaFieldsData['author-name'] ) ? $schemaFieldsData['author-name'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'image'=>array('label'=>$L['image'], 'type'=>'select-group', 'name' => 'schema[fields][image]', 'value'=>( isset( $schemaFieldsData['image'] ) ? $schemaFieldsData['image'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaImageFieldsArray ),
			
			'description'=>array('label'=>$L['description'], 'type'=>'select-group', 'name' => 'schema[fields][description]', 'value'=>( isset( $schemaFieldsData['description'] ) ? $schemaFieldsData['description'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'article-body'=>array('label'=>$L['article-body'], 'type'=>'select-group', 'name' => 'schema[fields][article-body]', 'value'=>( isset( $schemaFieldsData['article-body'] ) ? $schemaFieldsData['article-body'] : null ), 'tip'=>$L['article-body-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'page-url'=>array('label'=>$L['page-url'], 'type'=>'select-group', 'name' => 'schema[fields][page-url]', 'value'=>( isset( $schemaFieldsData['page-url'] ) ? $schemaFieldsData['page-url'] : null ), 'tip'=>$L['page-url-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'headline'=>array('label'=>$L['headline'], 'type'=>'select-group', 'name' => 'schema[fields][headline]', 'value'=>( isset( $schemaFieldsData['headline'] ) ? $schemaFieldsData['headline'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'alternative-headline'=>array('label'=>$L['alternative-headline'], 'type'=>'select-group', 'name' => 'schema[fields][alternative-headline]', 'value'=>( isset( $schemaFieldsData['alternative-headline'] ) ? $schemaFieldsData['alternative-headline'] : null ), 'tip'=>$L['alternative-headline-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'published-date'=>array('label'=>$L['published-date'], 'type'=>'select-group', 'name' => 'schema[fields][published-date]', 'value'=>( isset( $schemaFieldsData['published-date'] ) ? $schemaFieldsData['published-date'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			'modified-date'=>array('label'=>$L['modified-date'], 'type'=>'select-group', 'name' => 'schema[fields][modified-date]', 'value'=>( isset( $schemaFieldsData['modified-date'] ) ? $schemaFieldsData['modified-date'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			'publisher-name'=>array('label'=>$L['publisher-name'], 'type'=>'select-group', 'name' => 'schema[fields][publisher-name]', 'value'=>( isset( $schemaFieldsData['publisher-name'] ) ? $schemaFieldsData['publisher-name'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			'publisher-logo'=>array('label'=>$L['publisher-logo'], 'type'=>'select-group', 'name' => 'schema[fields][publisher-logo]', 'value'=>( isset( $schemaFieldsData['publisher-logo'] ) ? $schemaFieldsData['publisher-logo'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaImageFieldsArray ),
		);
	}

	//Event schema
	elseif( $schemaData['type'] == 'event' )
	{
		$schemaEventTypes = $schemaEventStatus = $schemaEventAttendanceFields = array();
		
		//Clone the main fields array 
		$schemaEventFieldsArray = $schemaEventAttendanceFieldsArray = $schemaAttendanceDateData = $schemaFieldsArray;
		
		foreach( $schemaAttendanceModeArray as $id => $row )
			$schemaEventAttendanceFields[$id] = array( 'name' => $row['name'], 'title'=> $row['title'], 'disabled' => false, 'data' => array() );
		
		foreach( $schemaCourseStatusArray as $id => $row )
			$schemaEventStatus[$id] = array( 'name' => $row['name'], 'title'=> $row['title'], 'disabled' => false, 'data' => array() );

		foreach( $schemaEventTypesArray as $id => $row )
			$schemaEventTypes[$id] = array( 'name' => $row['name'], 'title'=> $row['title'], 'disabled' => false, 'data' => array() );
			
		//Add the event field array
		$schemaEventAttendanceFieldsArray['event-status'] = array( 'name' => $L['status'], 'data' => $schemaEventAttendanceFields );

		//Add a custom info to the event fields array
		$schemaEventAttendanceFieldsArray['custom-info'] = array( 'name' => $L['add-custom-info'], 'data' => array(
											'text' => array( 'name' => 'custom-text', 'title'=> $L['text'], 'disabled' => false, 'data' => array() )
										)
					);
			
		//Add the event field array
		$schemaEventFieldsArray['event-status'] = array( 'name' => $L['status'], 'data' => $schemaEventStatus );

		//Add a custom info to the event fields array
		$schemaEventFieldsArray['custom-info'] = array( 'name' => $L['add-custom-info'], 'data' => array(
											'text' => array( 'name' => 'custom-text', 'title'=> $L['text'], 'disabled' => false, 'data' => array() )
										)
					);
					
		//Add a custom info to the event fields array
		$schemaAttendanceDateData['custom-info'] = array( 'name' => $L['add-custom-info'], 'data' => array(
											'date' => array( 'name' => 'custom-date', 'title'=> $L['date'], 'disabled' => false, 'data' => array() ),
											'text' => array( 'name' => 'custom-text', 'title'=> $L['text'], 'disabled' => false, 'data' => array() )
										)
					);

		$schemaDataToReturn = array(
			'event-type'=>array('label'=>$L['event-type'], 'type'=>'select', 'name' => 'schema[fields][event-type]', 'value'=>( isset( $schemaFieldsData['event-type'] ) ? $schemaFieldsData['event-type'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaEventTypes ),
			
			'name'=>array('label'=>$L['name'], 'type'=>'select-group', 'name' => 'schema[fields][name]', 'value'=>( isset( $schemaFieldsData['name'] ) ? $schemaFieldsData['name'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'description'=>array('label'=>$L['description'], 'type'=>'select-group', 'name' => 'schema[fields][description]', 'value'=>( isset( $schemaFieldsData['description'] ) ? $schemaFieldsData['description'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'image'=>array('label'=>$L['image'], 'type'=>'select-group', 'name' => 'schema[fields][image]', 'value'=>( isset( $schemaFieldsData['image'] ) ? $schemaFieldsData['image'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaImageFieldsArray ),
			
			'status'=>array('label'=>$L['status'], 'type'=>'select-group', 'name' => 'schema[fields][status]', 'value'=>( isset( $schemaFieldsData['status'] ) ? $schemaFieldsData['status'] : null ), 'tip'=>$L['schema-status-tip'], 'firstNull' => true, 'data' => $schemaEventFieldsArray ),
			
			'attendance-mode'=>array('label'=>$L['attendance-mode'], 'type'=>'select-group', 'name' => 'schema[fields][attendance-mode]', 'value'=>( isset( $schemaFieldsData['attendance-mode'] ) ? $schemaFieldsData['attendance-mode'] : null ), 'tip'=>$L['event-attendance-tip'], 'firstNull' => true, 'data' => $schemaEventAttendanceFieldsArray ),
			
			'event-start-date'=>array('label'=>$L['start-date'], 'type'=>'select-group', 'name' => 'schema[fields][start-date]', 'value'=>( isset( $schemaFieldsData['start-date'] ) ? $schemaFieldsData['start-date'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaAttendanceDateData ),
			
			'event-end-date'=>array('label'=>$L['end-date'], 'type'=>'select-group', 'name' => 'schema[fields][end-date]', 'value'=>( isset( $schemaFieldsData['end-date'] ) ? $schemaFieldsData['end-date'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaAttendanceDateData ),
			
			'location-name'=>array('label'=>$L['location-name'], 'type'=>'select-group', 'name' => 'schema[fields][location-name]', 'value'=>( isset( $schemaFieldsData['location-name'] ) ? $schemaFieldsData['location-name'] : null ), 'tip'=>$L['location-name-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'street-address'=>array('label'=>$L['street-address'], 'type'=>'select-group', 'name' => 'schema[fields][street-address]', 'value'=>( isset( $schemaFieldsData['street-address'] ) ? $schemaFieldsData['street-address'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'locality'=>array('label'=>$L['locality'], 'type'=>'select-group', 'name' => 'schema[fields][locality]', 'value'=>( isset( $schemaFieldsData['locality'] ) ? $schemaFieldsData['locality'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'postal-code'=>array('label'=>$L['postal-code'], 'type'=>'select-group', 'name' => 'schema[fields][postal-code]', 'value'=>( isset( $schemaFieldsData['postal-code'] ) ? $schemaFieldsData['postal-code'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'region'=>array('label'=>$L['region'], 'type'=>'select-group', 'name' => 'schema[fields][region]', 'value'=>( isset( $schemaFieldsData['region'] ) ? $schemaFieldsData['region'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'country'=>array('label'=>$L['country'], 'type'=>'select', 'name' => 'schema[fields][country]', 'value'=>( isset( $schemaFieldsData['country'] ) ? $schemaFieldsData['country'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $countriesArray ),
			
			'availability'=>array('label'=>$L['availability'], 'type'=>'select', 'name' => 'schema[fields][availability-status]', 'value'=>( isset( $schemaFieldsData['availability-status'] ) ? $schemaFieldsData['availability-status'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $availabilityArray ),
			
			'price'=>array('label'=>$L['price'], 'type'=>'select-group', 'name' => 'schema[fields][price]', 'value'=>( isset( $schemaFieldsData['price'] ) ? $schemaFieldsData['price'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'price-currency'=>array('label'=>$L['currency'], 'type'=>'select', 'name' => 'schema[fields][price-currency]', 'value'=>( isset( $schemaFieldsData['price-currency'] ) ? $schemaFieldsData['price-currency'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $currenciesArray ),
			
			'valid-from'=>array('label'=>$L['valid-from'], 'type'=>'select-group', 'name' => 'schema[fields][valid-from]', 'value'=>( isset( $schemaFieldsData['valid-from'] ) ? $schemaFieldsData['valid-from'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaAttendanceDateData ),
			
			'online-ticket-url'=>array('label'=>$L['online-ticket-url'], 'type'=>'select-group', 'name' => 'schema[fields][offer-url]', 'value'=>( isset( $schemaFieldsData['offer-url'] ) ? $schemaFieldsData['offer-url'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'performer'=>array('label'=>$L['performer'], 'type'=>'select-group', 'name' => 'schema[fields][performer]', 'value'=>( isset( $schemaFieldsData['performer'] ) ? $schemaFieldsData['performer'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'organizer-name'=>array('label'=>$L['organizer-name'], 'type'=>'select-group', 'name' => 'schema[fields][organizer-name]', 'value'=>( isset( $schemaFieldsData['organizer-name'] ) ? $schemaFieldsData['organizer-name'] : null ), 'tip'=>$L['organizer-name-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'organizer-url'=>array('label'=>$L['organizer-url'], 'type'=>'select-group', 'name' => 'schema[fields][organizer-url]', 'value'=>( isset( $schemaFieldsData['organizer-url'] ) ? $schemaFieldsData['organizer-url'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
		);
	}

	//Course schema
	elseif( $schemaData['type'] == 'course' )
	{
		$schemaCourseFormat = $schemaAttendanceMode = array();
		
		//Clone the main fields array 
		$schemaCourseFieldsArray = $schemaAttendanceModeData = $schemaAttendanceDateData = $schemaCourseRateData = $schemaFieldsArray;
		
		foreach( $schemaCourseStatusArray as $id => $row )
			$schemaCourseFormat[$id] = array( 'name' => $row['name'], 'title'=> $row['title'], 'disabled' => false, 'data' => array() );
			
		foreach( $schemaAttendanceModeArray as $id => $row )
			$schemaAttendanceMode[$id] = array( 'name' => $row['name'], 'title'=> $row['title'], 'disabled' => false, 'data' => array() );

		//Add the course field array
		$schemaCourseFieldsArray['course-status'] = array( 'name' => $L['course-status'], 'data' => $schemaCourseFormat );
		
		//Add the course field array
		$schemaAttendanceModeData['course-attendance-mode'] = array( 'name' => $L['course-attendance-mode'], 'data' => $schemaAttendanceMode );
		
		//Add a custom info to the course fields array
		$schemaAttendanceDateData['custom-info'] = array( 'name' => $L['add-custom-info'], 'data' => array(
											'date' => array( 'name' => 'custom-date', 'title'=> $L['date'], 'disabled' => false, 'data' => array() ),
											'text' => array( 'name' => 'custom-text', 'title'=> $L['text'], 'disabled' => false, 'data' => array() )
										)
					);
					
		//Add a custom info to the course fields array
		$schemaAttendanceDateData['custom-info'] = array( 'name' => $L['add-custom-info'], 'data' => array(
											'date' => array( 'name' => 'custom-date', 'title'=> $L['date'], 'disabled' => false, 'data' => array() ),
											'text' => array( 'name' => 'custom-text', 'title'=> $L['text'], 'disabled' => false, 'data' => array() )
										)
					);

		//Add a custom info to the course fields array
		$schemaCourseRateData['custom-info'] = array( 'name' => $L['add-custom-info'], 'data' => array(
											'num' => array( 'name' => 'custom-number', 'title'=> $L['number'], 'disabled' => false, 'data' => array() ),
											'text' => array( 'name' => 'custom-text', 'title'=> $L['text'], 'disabled' => false, 'data' => array() )
										)
					);
					
		$schemaDataToReturn = array(
			'title'=>array('label'=>$L['course-title'], 'type'=>'select-group', 'name' => 'schema[fields][title]', 'value'=>( isset( $schemaFieldsData['title'] ) ? $schemaFieldsData['title'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'description'=>array('label'=>$L['description'], 'type'=>'select-group', 'name' => 'schema[fields][description]', 'value'=>( isset( $schemaFieldsData['description'] ) ? $schemaFieldsData['description'] : null ), 'tip'=>$L['course-description-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-code'=>array('label'=>$L['course-code'], 'type'=>'select-group', 'name' => 'schema[fields][course-code]', 'value'=>( isset( $schemaFieldsData['course-code'] ) ? $schemaFieldsData['course-code'] : null ), 'tip'=>$L['course-code-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-provider'=>array('label'=>$L['course-provider'], 'type'=>'select-group', 'name' => 'schema[fields][course-course-provider]', 'value'=>( isset( $schemaFieldsData['course-provider'] ) ? $schemaFieldsData['course-provider'] : null ), 'tip'=>$L['course-provider-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-instance-name'=>array('label'=>$L['course-instance-name'], 'type'=>'select-group', 'name' => 'schema[fields][course-instance-name]', 'value'=>( isset( $schemaFieldsData['course-instance-name'] ) ? $schemaFieldsData['course-instance-name'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-instance-description'=>array('label'=>$L['course-instance-description'], 'type'=>'select-group', 'name' => 'schema[fields][course-instance-description]', 'value'=>( isset( $schemaFieldsData['course-instance-description'] ) ? $schemaFieldsData['course-instance-description'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-mode'=>array('label'=>$L['course-mode'], 'type'=>'select-group', 'name' => 'schema[fields][course-mode]', 'value'=>( isset( $schemaFieldsData['course-mode'] ) ? $schemaFieldsData['course-mode'] : null ), 'tip'=>$L['course-mode-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-image'=>array('label'=>$L['image'], 'type'=>'select-group', 'name' => 'schema[fields][image]', 'value'=>( isset( $schemaFieldsData['image'] ) ? $schemaFieldsData['image'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaImageFieldsArray ),
			
			'status'=>array('label'=>$L['course-status'], 'type'=>'select-group', 'name' => 'schema[fields][status]', 'value'=>( isset( $schemaFieldsData['status'] ) ? $schemaFieldsData['status'] : null ), 'tip'=>$L['course-status-tip'], 'firstNull' => true, 'data' => $schemaCourseFieldsArray ),
			
			'course-attendance-mode'=>array('label'=>$L['attendance-mode'], 'type'=>'select-group', 'name' => 'schema[fields][attendance-mode]', 'value'=>( isset( $schemaFieldsData['attendance-mode'] ) ? $schemaFieldsData['attendance-mode'] : null ), 'tip'=>$L['course-attendance-mode-tip'], 'firstNull' => true, 'data' => $schemaAttendanceModeData ),
			
			'course-start-date'=>array('label'=>$L['start-date'], 'type'=>'select-group', 'name' => 'schema[fields][course-start-date]', 'value'=>( isset( $schemaFieldsData['course-start-date'] ) ? $schemaFieldsData['course-start-date'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaAttendanceDateData ),
			
			'course-end-date'=>array('label'=>$L['end-date'], 'type'=>'select-group', 'name' => 'schema[fields][course-end-date]', 'value'=>( isset( $schemaFieldsData['course-end-date'] ) ? $schemaFieldsData['course-end-date'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaAttendanceDateData ),
			
			'online-course-url'=>array('label'=>$L['online-course-url'], 'type'=>'select-group', 'name' => 'schema[fields][online-course-url]', 'value'=>( isset( $schemaFieldsData['online-course-url'] ) ? $schemaFieldsData['online-course-url'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-organizer-name'=>array('label'=>$L['course-organizer-name'], 'type'=>'select-group', 'name' => 'schema[fields][course-organizer-name]', 'value'=>( isset( $schemaFieldsData['course-organizer-name'] ) ? $schemaFieldsData['course-organizer-name'] : null ), 'tip'=>$L['course-organizer-name-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-organizer-url'=>array('label'=>$L['course-organizer-url'], 'type'=>'select-group', 'name' => 'schema[fields][course-organizer-url]', 'value'=>( isset( $schemaFieldsData['course-organizer-url'] ) ? $schemaFieldsData['course-organizer-url'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-location-name'=>array('label'=>$L['course-location-name'], 'type'=>'select-group', 'name' => 'schema[fields][course-location-name]', 'value'=>( isset( $schemaFieldsData['course-location-name'] ) ? $schemaFieldsData['course-location-name'] : null ), 'tip'=>$L['course-location-name-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-location-address'=>array('label'=>$L['course-location-address'], 'type'=>'select-group', 'name' => 'schema[fields][course-location-address]', 'value'=>( isset( $schemaFieldsData['course-location-address'] ) ? $schemaFieldsData['course-location-address'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-price'=>array('label'=>$L['price'], 'type'=>'select-group', 'name' => 'schema[fields][price]', 'value'=>( isset( $schemaFieldsData['price'] ) ? $schemaFieldsData['price'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'course-price-currency'=>array('label'=>$L['currency'], 'type'=>'select', 'name' => 'schema[fields][price-currency]', 'value'=>( isset( $schemaFieldsData['price-currency'] ) ? $schemaFieldsData['price-currency'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $currenciesArray ),
			
			'valid-from'=>array('label'=>$L['valid-from'], 'type'=>'select-group', 'name' => 'schema[fields][valid-from]', 'value'=>( isset( $schemaFieldsData['valid-from'] ) ? $schemaFieldsData['valid-from'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaAttendanceDateData ),
			
			'offer-url'=>array('label'=>$L['offer-url'], 'type'=>'select-group', 'name' => 'schema[fields][offer-url]', 'value'=>( isset( $schemaFieldsData['offer-url'] ) ? $schemaFieldsData['offer-url'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'offer-availability'=>array('label'=>$L['availability'], 'type'=>'select', 'name' => 'schema[fields][availability-status]', 'value'=>( isset( $schemaFieldsData['availability-status'] ) ? $schemaFieldsData['availability-status'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $availabilityArray ),
			
			'performer'=>array('label'=>$L['performer'], 'type'=>'select-group', 'name' => 'schema[fields][performer]', 'value'=>( isset( $schemaFieldsData['performer'] ) ? $schemaFieldsData['performer'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'reference-link'=>array('label'=>$L['reference-link'], 'type'=>'select-group', 'name' => 'schema[fields][reference-link]', 'value'=>( isset( $schemaFieldsData['reference-link'] ) ? $schemaFieldsData['reference-link'] : null ), 'tip'=>$L['reference-link-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'rating'=>array('label'=>$L['rating'], 'type'=>'select-group', 'name' => 'schema[fields][rating]', 'value'=>( isset( $schemaFieldsData['rating'] ) ? $schemaFieldsData['rating'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaCourseRateData ),
			
			'review-count'=>array('label'=>$L['review-count'], 'type'=>'select-group', 'name' => 'schema[fields][review-count]', 'value'=>( isset( $schemaFieldsData['review-count'] ) ? $schemaFieldsData['review-count'] : null ), 'tip'=>$L['review-count-tip'], 'firstNull' => true, 'data' => $schemaCourseRateData ),
		);
		
	}

	//Video Object schema
	elseif( $schemaData['type'] == 'video-object' )
	{
		//Add the video field array
		$schemaVideoFieldsArray = array(
					'post-meta' => array( 'name' => $L['post-meta'], 'data' => array(
											'post-views' => array( 'name' => 'post-views', 'title'=> $L['post-views'], 'disabled' => false, 'data' => array() )
										)
					),
					
					'custom-info' => array( 'name' => $L['add-custom-info'], 'data' => array(
											'text' => array( 'name' => 'custom-text', 'title'=> $L['text'], 'disabled' => false, 'data' => array() )
										)
					),
		);
		
		$schemaDataToReturn = array(
			'video-title'=>array('label'=>$L['video-title'], 'type'=>'select-group', 'name' => 'schema[fields][title]', 'value'=>( isset( $schemaFieldsData['title'] ) ? $schemaFieldsData['title'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'video-description'=>array('label'=>$L['video-description'], 'type'=>'select-group', 'name' => 'schema[fields][description]', 'value'=>( isset( $schemaFieldsData['description'] ) ? $schemaFieldsData['description'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'video-thumbnail'=>array('label'=>$L['video-thumbnail'], 'type'=>'select-group', 'name' => 'schema[fields][image]', 'value'=>( isset( $schemaFieldsData['image'] ) ? $schemaFieldsData['image'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaImageFieldsArray ),
			
			'video-upload-date'=>array('label'=>$L['video-upload-date'], 'type'=>'select-group', 'name' => 'schema[fields][published-date]', 'value'=>( isset( $schemaFieldsData['published-date'] ) ? $schemaFieldsData['published-date'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'publisher-name'=>array('label'=>$L['publisher-name'], 'type'=>'select-group', 'name' => 'schema[fields][publisher-name]', 'value'=>( isset( $schemaFieldsData['publisher-name'] ) ? $schemaFieldsData['publisher-name'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			'publisher-logo'=>array('label'=>$L['publisher-logo'], 'type'=>'select-group', 'name' => 'schema[fields][publisher-logo]', 'value'=>( isset( $schemaFieldsData['publisher-logo'] ) ? $schemaFieldsData['publisher-logo'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaImageFieldsArray ),
			
			'content-url'=>array('label'=>$L['content-url'], 'type'=>'select-group', 'name' => 'schema[fields][url]', 'value'=>( isset( $schemaFieldsData['url'] ) ? $schemaFieldsData['url'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'embed-url'=>array('label'=>$L['embed-url'], 'type'=>'select-group', 'name' => 'schema[fields][embed-url]', 'value'=>( isset( $schemaFieldsData['embed-url'] ) ? $schemaFieldsData['embed-url'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'video-duration'=>array('label'=>$L['video-duration'], 'type'=>'select-group', 'name' => 'schema[fields][video-duration]', 'value'=>( isset( $schemaFieldsData['video-duration'] ) ? $schemaFieldsData['video-duration'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'video-expires-on'=>array('label'=>$L['video-expires-on'], 'type'=>'select-group', 'name' => 'schema[fields][video-expires-on]', 'value'=>( isset( $schemaFieldsData['video-expires-on'] ) ? $schemaFieldsData['video-expires-on'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'video-interaction-count'=>array('label'=>$L['video-interaction-count'], 'type'=>'select-group', 'name' => 'schema[fields][video-interaction-count]', 'value'=>( isset( $schemaFieldsData['video-interaction-count'] ) ? $schemaFieldsData['video-interaction-count'] : null ), 'tip'=>$L['video-interaction-count-tip'], 'firstNull' => true, 'data' => $schemaVideoFieldsArray )
		);
	}

	//Claim Review
	elseif ( $schemaData['type'] == 'claim-review' )
	{
		//Clone the main fields array 
		$schemaClaimFieldsArray = $schemaFieldsArray;
		
		//Add a custom info to the course fields array
		$schemaClaimFieldsArray['custom-info'] = array( 'name' => $L['add-custom-info'], 'data' => array(
											'num' => array( 'name' => 'custom-number', 'title'=> $L['number'], 'disabled' => false, 'data' => array() ),
											'text' => array( 'name' => 'custom-text', 'title'=> $L['text'], 'disabled' => false, 'data' => array() )
										)
					);
		
		$schemaDataToReturn = array(
			'article-type'=>array('label'=>$L['article-type'], 'type'=>'select', 'name' => 'schema[fields][article-type]', 'value'=>( isset( $schemaFieldsData['article-type'] ) ? $schemaFieldsData['article-type'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaArticleTypes ),
			
			'author-name'=>array('label'=>$L['author-name'], 'type'=>'select-group', 'name' => 'schema[fields][author-name]', 'value'=>( isset( $schemaFieldsData['author-name'] ) ? $schemaFieldsData['author-name'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'image'=>array('label'=>$L['image'], 'type'=>'select-group', 'name' => 'schema[fields][image]', 'value'=>( isset( $schemaFieldsData['image'] ) ? $schemaFieldsData['image'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaImageFieldsArray ),
			
			'description'=>array('label'=>$L['description'], 'type'=>'select-group', 'name' => 'schema[fields][description]', 'value'=>( isset( $schemaFieldsData['description'] ) ? $schemaFieldsData['description'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'url'=>array('label'=>$L['url'], 'type'=>'select-group', 'name' => 'schema[fields][url]', 'value'=>( isset( $schemaFieldsData['url'] ) ? $schemaFieldsData['url'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'published-date'=>array('label'=>$L['published-date'], 'type'=>'select-group', 'name' => 'schema[fields][published-date]', 'value'=>( isset( $schemaFieldsData['published-date'] ) ? $schemaFieldsData['published-date'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'claim-reviewed'=>array('label'=>$L['claim-reviewed'], 'type'=>'select-group', 'name' => 'schema[fields][claim-reviewed]', 'value'=>( isset( $schemaFieldsData['claim-reviewed'] ) ? $schemaFieldsData['claim-reviewed'] : null ), 'tip'=>$L['claim-reviewed-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),

			'claim-url-original'=>array('label'=>$L['claim-url-original'], 'type'=>'select-group', 'name' => 'schema[fields][claim-url-original]', 'value'=>( isset( $schemaFieldsData['claim-url-original'] ) ? $schemaFieldsData['claim-url-original'] : null ), 'tip'=>$L['claim-url-original-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'claim-url-other'=>array('label'=>$L['claim-url-other'], 'type'=>'select-group', 'name' => 'schema[fields][claim-url-other]', 'value'=>( isset( $schemaFieldsData['claim-url-other'] ) ? $schemaFieldsData['claim-url-other'] : null ), 'tip'=>$L['claim-url-other-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'rating-value'=>array('label'=>$L['rating-value'], 'type'=>'select-group', 'name' => 'schema[fields][rating-value]', 'value'=>( isset( $schemaFieldsData['rating-value'] ) ? $schemaFieldsData['rating-value'] : null ), 'tip'=>$L['rating-value-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),

			'rating-value-worst'=>array('label'=>$L['rating-value-worst'], 'type'=>'select-group', 'name' => 'schema[fields][rating-value-worst]', 'value'=>( isset( $schemaFieldsData['rating-value-worst'] ) ? $schemaFieldsData['rating-value-worst'] : null ), 'tip'=>$L['rating-value-worst-tip'], 'firstNull' => true, 'data' => $schemaClaimFieldsArray ),
			
			'rating-value-best'=>array('label'=>$L['rating-value-best'], 'type'=>'select-group', 'name' => 'schema[fields][rating-value-best]', 'value'=>( isset( $schemaFieldsData['rating-value-best'] ) ? $schemaFieldsData['rating-value-best'] : null ), 'tip'=>$L['rating-value-best-tip'], 'firstNull' => true, 'data' => $schemaClaimFieldsArray ),
			
			'alternate-name'=>array('label'=>$L['alternate-name'], 'type'=>'select-group', 'name' => 'schema[fields][alternate-name]', 'value'=>( isset( $schemaFieldsData['alternate-name'] ) ? $schemaFieldsData['alternate-name'] : null ), 'tip'=>$L['alternate-name-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
		);
	}

	//Book schema
	elseif( $schemaData['type'] == 'book' )
	{
		$schemaBookFormat = array();

		foreach( $schemaBookFormatArray as $id => $row )
			$schemaBookFormat[$id] = array( 'name' => $row['name'], 'title'=> $row['title'], 'disabled' => false, 'data' => array() );
			
		//Clone the main fields array 
		$schemaBooksFieldsArray = $schemaFieldsArray;

		//Add the books field array
		$schemaBooksFieldsArray['book-format'] = array( 'name' => $L['book-format'], 'data' => $schemaBookFormat );

		//Add a custom info to the books fields array
		$schemaBooksFieldsArray['custom-info'] = array( 'name' => $L['add-custom-info'], 'data' => array(
											'text' => array( 'name' => 'custom-text', 'title'=> $L['text'], 'disabled' => false, 'data' => array() )
										)
					);
		
		$schemaDataToReturn = array(
			'title'=>array('label'=>$L['title'], 'type'=>'select-group', 'name' => 'schema[fields][title]', 'value'=>( isset( $schemaFieldsData['title'] ) ? $schemaFieldsData['title'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'image'=>array('label'=>$L['image'], 'type'=>'select-group', 'name' => 'schema[fields][image]', 'value'=>( isset( $schemaFieldsData['image'] ) ? $schemaFieldsData['image'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaImageFieldsArray ),
			
			'author-name'=>array('label'=>$L['author-name'], 'type'=>'select-group', 'name' => 'schema[fields][author-name]', 'value'=>( isset( $schemaFieldsData['author-name'] ) ? $schemaFieldsData['author-name'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'url'=>array('label'=>$L['url'], 'type'=>'select-group', 'name' => 'schema[fields][url]', 'value'=>( isset( $schemaFieldsData['url'] ) ? $schemaFieldsData['url'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'isbn'=>array('label'=>$L['isbn'], 'type'=>'select-group', 'name' => 'schema[fields][isbn]', 'value'=>( isset( $schemaFieldsData['isbn'] ) ? $schemaFieldsData['isbn'] : null ), 'tip'=>$L['isbn-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'book-format'=>array('label'=>$L['book-format'], 'type'=>'select-group', 'name' => 'schema[fields][book-format]', 'value'=>( isset( $schemaFieldsData['book-format'] ) ? $schemaFieldsData['book-format'] : null ), 'tip'=>$L['book-format-tip'], 'firstNull' => true, 'data' => $schemaBooksFieldsArray ),
			
			'book-edition'=>array('label'=>$L['book-edition'], 'type'=>'select-group', 'name' => 'schema[fields][book-edition]', 'value'=>( isset( $schemaFieldsData['book-edition'] ) ? $schemaFieldsData['book-edition'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'offer-price'=>array('label'=>$L['offer-price'], 'type'=>'select-group', 'name' => 'schema[fields][price]', 'value'=>( isset( $schemaFieldsData['price'] ) ? $schemaFieldsData['price'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'offer-price-currency'=>array('label'=>$L['offer-price-currency'], 'type'=>'select', 'name' => 'schema[fields][price-currency]', 'value'=>( isset( $schemaFieldsData['price-currency'] ) ? $schemaFieldsData['price-currency'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $currenciesArray ),
			
			'offer-eligible-country'=>array('label'=>$L['offer-eligible-country'], 'type'=>'select', 'name' => 'schema[fields][country]', 'value'=>( isset( $schemaFieldsData['country'] ) ? $schemaFieldsData['country'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $countriesArray ),
			
			'offer-availability-status'=>array('label'=>$L['offer-availability-status'], 'type'=>'select', 'name' => 'schema[fields][availability-status]', 'value'=>( isset( $schemaFieldsData['availability-status'] ) ? $schemaFieldsData['availability-status'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $availabilityArray ),
			
			'reference-link'=>array('label'=>$L['reference-link'], 'type'=>'select-group', 'name' => 'schema[fields][reference-link]', 'value'=>( isset( $schemaFieldsData['reference-link'] ) ? $schemaFieldsData['reference-link'] : null ), 'tip'=>$L['reference-link-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray )
		);
	}

	//how to schema
	elseif( $schemaData['type'] == 'how-to' )
	{
		$schemaDataToReturn = array(
			'name'=>array('label'=>$L['name'], 'type'=>'select-group', 'name' => 'schema[fields][name]', 'value'=>( isset( $schemaFieldsData['name'] ) ? $schemaFieldsData['name'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'description'=>array('label'=>$L['description'], 'type'=>'select-group', 'name' => 'schema[fields][description]', 'value'=>( isset( $schemaFieldsData['description'] ) ? $schemaFieldsData['description'] : null ), 'tip'=>null, 'firstNull' => true, 'data' => $schemaFieldsArray ),
			
			'total-time'=>array('label'=>$L['total-time'], 'type'=>'select-group', 'name' => 'schema[fields][total-time]', 'value'=>( isset( $schemaFieldsData['total-time'] ) ? $schemaFieldsData['total-time'] : null ), 'tip'=>$L['total-time-tip'], 'firstNull' => true, 'data' => $schemaFieldsArray )
		);
		
	}
	
	return array( 'schemaData' => $schemaData, 'formData' => $schemaDataToReturn );
}