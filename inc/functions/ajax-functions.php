<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Add New Form Element function 
#
#####################################################

function AjaxShowNewForm()
{
	return 'OK';
}

#####################################################
#
# Remove Form Element function 
#
#####################################################
function AjaxRemFormElement()
{
	if ( empty( $_POST ) || !isset( $_POST['id'] ) )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$db = db();
	
	$q = $db->delete( 'form_elements' )->where( 'id', $_POST['id'] )->run();

	if ( !$q )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$formId = (int) $_POST['formId'];
	
	$Form = GetSingleForm( $formId );
	
	$code = ( $Form ? FormElementToHtml( $Form['elements'], true, false ) : null );
	
	return array( 'status' => 'ok', 'code' => $code, 'items' => ( !empty( $Form['elements'] ) ? count( $Form['elements'] ) : 0 ) );
}

#####################################################
#
# Remove an element from column
#
#####################################################
function AjaxRemTableColumnElement()
{
	if ( empty( $_POST ) || !isset( $_POST['id'] ) )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$db = db();
	
	$q = $db->delete( 'form_table_elements' )->where( 'id', $_POST['id'] )->run();

	if ( !$q )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	return array( 'status' => 'ok' );
}

#####################################################
#
# Remove Table Column function 
#
#####################################################
function AjaxRemTableColumn()
{
	if ( empty( $_POST ) || !isset( $_POST['id'] ) )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$db = db();
	
	$tbl = $db->from( 
	null, 
	"SELECT id_form
	FROM `" . DB_PREFIX . "form_elements`
	WHERE (id = " . (int) $_POST['id'] . ")"
	)->single();
	
	if ( !$tbl )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$q = $db->delete( 'form_elements' )->where( "id", $_POST['id'] )->run();

	if ( !$q )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	//Delete also any elements this column may have
	$db->delete( 'form_table_elements' )->where( "id_column", $_POST['id'] )->run();
	
	$Form = GetSingleForm( $tbl['id_form'] );
	
	$code = ( $Form ? BuildTablePreviewHtml( $Form['elements'], false ) : null );
	
	return array( 'status' => 'ok', 'code' => $code );
}

#####################################################
#
# New Table Column Element function 
#
#####################################################
function AjaxAddTableColumn()
{
	if ( empty( $_POST ) || !isset( $_POST['formId'] ) )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$formId = (int) $_POST['formId'];
	
	$db = db();
	
	$form = $db->from( 
	null, 
	"SELECT id, id_site
	FROM `" . DB_PREFIX . "forms`
	WHERE (id = " . $formId . ")"
	)->single();
	
	if ( !$form )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$temp_ = $db->from( 
	null, 
	"SELECT elem_order
	FROM `" . DB_PREFIX . "form_elements`
	WHERE (id_form = " . $formId . ")
	ORDER BY elem_order DESC
	LIMIT 1"
	)->single();
	
	$el_order 	= ( $temp_ ? ( $temp_['elem_order'] + 1 ) : 0 );
	
	$name 		= __( 'column' ) . ' ' . $el_order;
	
	$el = null;
	
	$dbarr = array(
		"id_form" 		=> $formId,
		"elem_order"	=> $el_order,
		"elem_id"		=> '',
		"data" 			=> json_encode( array() ),
		"elem_name" 	=> $name
	);

	$q = $db->insert( 'form_elements' )->set( $dbarr );
	
	if ( $q )
	{
		$el = $db->lastId();
	}
	
	if ( !$el )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}

	$html = '
	<div data-id="' . $el . '" id="table-item-' . $el . '" class="card multi-collapse">
		<div class="card-header bg-light">
			<h3 class="card-title">
				<span id="elemntTitle' . $el . '">' . $name . '</span>

				<div id="columnTitleDiv' . $el . '" class="btn-group d-none">
					<input placeholder="' . __( 'column-name' )  . '" class="form-control" type="text" id="elemntTitleInput' . $el . '" value="' . $name . '" />
					<button type="button" id="cancelTitle' . $el . '" data-id="' . $el . '" onclick="cancelTitle(' . $el . ');" class="btn btn-tool"><i class="fa fa-times"></i></button>
					<button type="button" id="saveTitle' . $el . '" data-id="' . $el . '" onclick="saveTitle(' . $el . ',' . $formId . ');" class="btn btn-tool"><i class="fa fa-check"></i></button>
				</div>
				<button type="button" id="changeTitle' . $el . '" data-id="' . $el . '" onclick="changeTitle(' . $el . ');" class="btn btn-tool">
					<i class="fas fa-edit"></i>
				</button>
			</h3>

			<div class="card-tools">
				<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
					<i class="fas fa-minus"></i>
				</button>

				<button type="button" id="close" onclick="removeColumn(' . $el . ',' . $formId . ');" data-id="' . $el . '" class="btn btn-tool">
					<i class="fas fa-times"></i>
				</button>
			</div>
		</div>
				
		<!-- Head -->
		<div class="card-body">
				
			<ul class="nav nav-tabs" id="tabs-header-' . $el . '-tab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="tab-header-' . $el . '-head-tab" data-toggle="pill" href="#tab-header-' . $el . '-head" role="tab" aria-controls="tab-header-' . $el . '-head" aria-selected="true">' . __( 'heading' ) . '</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="tab-header-' . $el . '-design-tab" data-toggle="pill" href="#tab-header-' . $el . '-design" role="tab" aria-controls="tab-header-' . $el . '-design" aria-selected="false">' . __( 'design' ) . '</a>
				</li>
			</ul>

			<div class="card-body">
				<div class="tab-content" id="tabs-header-' . $el . '-tabContent">
						
					<div class="tab-pane fade show active" parent="' . $el . '" id="tab-header-' . $el . '-head" role="tabpanel" aria-labelledby="tab-header-' . $el . '-tab">

						<section id="contentHeaderBuilder' . $el . '" class="connectedSortable2"></section>

						<button title="' . __( 'add-element' ) . '" onclick="addColumnHeadElement(' . $el . ');" data-id="' . $el . '" type="button" class="btn btn-tool">
							<i class="fas fa-plus"></i> ' . __( 'add-element' ) . '
						</button>
					</div>
				
					<div class="tab-pane fade" id="tab-header-' . $el . '-design" role="tabpanel" aria-labelledby="tab-' . $el . '-design-tab">
					</div>
				</div>
			</div>

		</div>
				
		<!-- Cell -->
		<div class="card-body">
					
			<ul class="nav nav-tabs" id="tabs-' . $el . '-tab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="tab-' . $el . '-cell-tab" data-toggle="pill" href="#tab-' . $el . '-cell" role="tab" aria-controls="tab-' . $el . '-cell" aria-selected="true">' . __( 'cell-template' ) . '</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="tab-' . $el . '-design-tab" data-toggle="pill" href="#tab-' . $el . '-design" role="tab" aria-controls="tab-' . $el . '-design" aria-selected="false">' . __( 'design' ) . '</a>
				</li>
			</ul>

			<div class="card-body">
				<div class="tab-content" id="tabs-' . $el . '-tabContent">
					<div class="tab-pane fade show active" parent="' . $el . '" id="tab-' . $el . '-cell" role="tabpanel" aria-labelledby="tab-' . $el . '-cell-tab">

						<section id="contentCellBuilder' . $el . '" class="connectedSortable2">
						</section>

						<button title="' . __( 'add-element' ) . '" data-id="' . $el . '" type="button" id="cell" onclick="addColumnCellElement(' . $el . ',\'cell\');" class="btn btn-tool">
							<i class="fas fa-plus"></i> ' . __( 'add-element' ) . '
						</button>
					</div>
				
					<div class="tab-pane fade" id="tab-' . $el . '-design" role="tabpanel" aria-labelledby="tab-' . $el . '-design-tab">
					</div>
				</div>
			</div>

		</div>
	</div>';
		
	$Form = GetSingleForm( $formId );
	
	$code = ( $Form ? BuildTablePreviewHtml( $Form['elements'], false ) : null );

	return array( 'status' => 'ok', 'id' => $el, 'html' => $html, 'code' => $code );
}

#####################################################
#
# New Table Element function 
#
#####################################################
function AjaxAddTableElement()
{
	require ( ARRAYS_ROOT . 'forms-arrays.php');
	
	if ( empty( $_POST ) || !isset( $_POST['id'] ) || !isset( $_POST['formId'] ) )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$db 	= db();
	$id 	= Sanitize( $_POST['id'], false );
	$type 	= Sanitize( $_POST['type'], false );
	$formId = (int) $_POST['formId'];
	$elId 	= (int) $_POST['elId'];
	
	$form = $db->from( 
	null, 
	"SELECT id, id_site
	FROM `" . DB_PREFIX . "forms`
	WHERE (id = " . $formId . ")"
	)->single();
	
	if ( !$form )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$arr = ( ( $type == 'header' ) ? $genericTablesHeaderArray : $genericTablesArray );

	if ( !isset( $arr[$id] ) )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$temp_ = $db->from( 
	null, 
	"SELECT elem_order
	FROM `" . DB_PREFIX . "form_table_elements`
	WHERE (id_column = " . $elId . ")
	ORDER BY elem_order DESC
	LIMIT 1"
	)->single();
	
	$el_order = ( $temp_ ? ( $temp_['elem_order'] + 1 ) : 0 );
	
	$dataArr = $arr[$id]['data'];
	
	$dataToAdd = array();
	
	$el = null;

	if ( !empty( $dataArr ) )
	{
		foreach( $dataArr as $t_id => $t )
		{
			$dataToAdd[$t_id] = ( isset( $t['value'] ) ? $t['value'] : null );
		}
	}
	
	$dbarr = array(
		"id_column" 	=> $elId,
		"elem_order"	=> $el_order,
		"elem_id"		=> $id,
		"data" 			=> json_encode( $dataToAdd, JSON_UNESCAPED_UNICODE ),
		"elem_type" 	=> $type
	);

	$q = $db->insert( 'form_table_elements' )->set( $dbarr );
	
	if ( $q )
	{
		$el = $db->lastId();
	}
	
	if ( !$el )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$html = '
		<div data-id="' . $el . '" class="card collapsed-card">
            <div class="card-header bg-light">
				<h3 class="card-title">
					' . __( $arr[$id]['title'] ) . '
				</h3>
				<div class="card-tools">
					<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
						<i class="fas fa-plus"></i>
					</button>
					<button type="button" id="close" data-id="' . $el . '" class="btn btn-tool">
						<i class="fas fa-times"></i>
					</button>
				</div>
            </div>
           <div class="card-body">';
			
			if ( !empty( $dataToAdd ) )
			{
				$html .= BuildFormElementHtml( $dataToAdd, $id, $el, $elId, $type );
			}

		$html .= '
			</div>
       </div>';

	return array( 'status' => 'ok', 'html' => $html );
}

#####################################################
#
# New Form Element function 
#
#####################################################
function AjaxAddFormElement()
{
	require ( ARRAYS_ROOT . 'forms-arrays.php');
	
	if ( empty( $_POST ) || !isset( $_POST['id'] ) || !isset( $_POST['formId'] ) )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$id 	= Sanitize( $_POST['id'], false );
	$formId = (int) $_POST['formId'];
	$db 	= db();
	
	$form = $db->from( 
	null, 
	"SELECT id, id_site
	FROM `" . DB_PREFIX . "forms`
	WHERE (id = " . $formId . ")"
	)->single();
	
	if ( !$form )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$arr = $genericFormsArray;
	
	if ( !isset( $arr[$id] ) )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$temp_ = $db->from( 
	null, 
	"SELECT elem_order
	FROM `" . DB_PREFIX . "form_elements`
	WHERE (id_form = " . $formId . ")
	ORDER BY elem_order DESC
	LIMIT 1"
	)->single();
	
	$el_order 	= ( $temp_ ? ( $temp_['elem_order'] + 1 ) : 0 );
	
	$dataArr 	= $arr[$id]['data'];
	
	$dataToAdd 	= array();
	
	$el 		= null;

	if ( !empty( $dataArr ) )
	{
		foreach( $dataArr as $t_id => $t )
		{
			$dataToAdd[$t_id] = $t['value'];
		}
	}
	
	$dbarr = array(
		"id_form" 		=> $formId,
		"elem_order"	=> $el_order,
		"elem_id"		=> $id,
		"data" 			=> json_encode( $dataToAdd, JSON_UNESCAPED_UNICODE )
	);

	$el = $db->insert( 'form_elements' )->set( $dbarr, null, true );
	
	if ( !$el )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}

	$html = '
		<div data-id="' . $el . '" class="card collapsed-card">
            <div class="card-header bg-light">
				<h3 class="card-title">
					' . __( $arr[$id]['title'] ) . '
				</h3>
				<div class="card-tools">
					<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
						<i class="fas fa-plus"></i>
					</button>
					<button type="button" id="close" data-id="' . $el . '" class="btn btn-tool">
						<i class="fas fa-times"></i>
					</button>
				</div>
            </div>
           <div class="card-body">';
			
			if ( !empty( $dataToAdd ) )
			{
				$html .= BuildFormElementHtml( $dataToAdd, $id, $el );
			}

		$html .= '
			</div>
       </div>';
	   
	$Form = GetSingleForm( $formId );
	
	$code = ( $Form ? FormElementToHtml( $Form['elements'], true, false ) : null );

	return array( 'status' => 'ok', 'id' => $id, 'html' => $html, 'code' => $code );
}

#####################################################
#
# Save the Column Name
#
#####################################################
function AjaxChangeColumnName()
{
	global $Admin;
	
	$arr = array(
			'status' => 'error',
			'message' => __( 'an-error-happened' )
	);
	
	$title 	= Sanitize( $_POST['title'], false );
	$id 	= (int) $_POST['id'];
	$db 	= db();
	
	$q = $db->update( "form_elements" )->where( 'id', $id )->set( "elem_name", $title );
	
	if ( !$q )
	{
		return $arr;
	}
	
	$Form = GetSingleForm( $_POST['formId'] );
	
	$code = ( $Form ? BuildTablePreviewHtml( $Form['elements'], false ) : null );
	
	return array(
		'status' 	=> 'ok',
		'message' 	=> __( 'data-updated' ),
		'code' 		=> $code
	);
	
}

#####################################################
#
# Save the Table Order function
#
#####################################################
function AjaxTableSortColumns()
{
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);

	$i 	= 0;
	
	$db = db();	
	
	foreach( $_POST['ids'] as $id )
	{
		//Update the DB
		$db->update( "form_table_elements" )->where( 'id', $id )->set( "elem_order", $i );
		
		$i++;
	}
	
	return array(
		'status' 	=> 'ok',
		'message' 	=> __( 'data-updated' )
	);
	
}

#####################################################
#
# Save the Table Order function
#
#####################################################
function AjaxTableSort()
{	
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);

	$i 	= 0;
	
	$db = db();	
	
	foreach( $_POST['ids'] as $id )
	{
		//Update the DB
		$db->update( "form_elements" )->where( 'id', $id )->set( "elem_order", $i );

		$i++;
	}
	
	$Form = GetSingleForm( $_POST['formId'] );
	
	$code = ( $Form ? BuildTablePreviewHtml( $Form['elements'], false ) : null );
	
	return array(
		'status' 	=> 'ok',
		'message' 	=> __( 'data-updated' ),
		'code' 		=> $code
	);
}

#####################################################
#
# Load Move Content
#
#####################################################
function AjaxLoadMoveContent()
{
	$from 		= Sanitize( $_POST['from'], false );
	$site 		= $_POST['site'];
	$fromId 	= $_POST['fromId'];
	$options 	= ( isset( $_POST['options'] ) ? $_POST['options'] : 'move' );
	$error 		= false;
	
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);
	
	if ( ( $from == 'orphan-posts' ) || ( $from == 'orphan-pages' ) || ( $from == 'orphan' ) )
	{
		$type = ( ( $from == 'orphan' ) ? 'all' : ( ( $from == 'orphan-pages' ) ? 'page' : 'post' ) );
		
		$count = CountPosts( $type, null, null, null, $site );
	}
	
	elseif ( $from == 'blog' )
	{
		$count = CountPosts( 'all', null, null, $fromId, $site );
	}
	
	elseif ( $from == 'category' )
	{
		$count = CountPosts( 'post', null, $fromId, null, $site );
	}
	
	elseif ( $from == 'site' )
	{
		$count = CountPosts( 'all', null, null, null, $fromId );
	}
	
	elseif ( $from == 'lang' )
	{
		$count = CountPosts( 'all', $fromId, null, null, $site );
	}

	else
	{
		$count = 0;
		$error = true;
	}
	
	if ( !$count || ( $count == 0 ) )
	{
		$arr['status'] = 'nothing-found';
		$arr['message'] = __( 'no-posts-found' );
			
		return $arr;
	}
	
	if ( $error )
	{
		return $arr;
	}
		
	$message  	 = '<p>' . __( 'dont-close-this-window-until-the-process-is-completed' ) . '<br />';
	$message  	.= sprintf( __( 'move-posts-num-found' ), $count ) . '<br />';

	$arr['message'] = $message;
		
	$arr = array(
		'status' 		=> 'ok',
		'message' 		=> $message,
		'totalItems' 	=> $count
	);
	
	return $arr;
}

#####################################################
#
# Move Content
#
#####################################################
function AjaxMoveContent()
{
	$from 			= Sanitize( $_POST['from'], false );
	$to 			= Sanitize( $_POST['to'], false );
	$fromId 		= $_POST['fromId'];
	$toId 			= $_POST['toId'];
	$site 			= $_POST['site'];
	$options 		= ( isset( $_POST['options'] ) ? $_POST['options'] : 'move' );
	$currentItems 	= 0;
	$totalItems 	= 0;
	$failerItems 	= 0;
	$itemsPerPage	= 10;
	
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);
	
	if ( ( $from == 'orphan-posts' ) || ( $from == 'orphan-pages' ) || ( $from == 'orphan' ) )
	{
		$type = ( ( $from == 'orphan' ) ? 'all' : ( ( $from == 'orphan-pages' ) ? 'page' : 'post' ) );
		
		MoveOrphanContent( $to, $toId, $type, $site, $itemsPerPage );
	}
	
	elseif ( $from == 'blog' )
	{
		MoveBlogContent( $fromId, $site, $toId, $options, $itemsPerPage );
	}
	
	elseif ( $from == 'category' )
	{
		MoveCatContent( $fromId, $toId, $itemsPerPage );
	}
	
	elseif ( $from == 'site' )
	{
		MoveSiteContent( $fromId, $toId, $itemsPerPage );
	}
	
	elseif ( $from == 'lang' )
	{
		MoveLangContent( $fromId, $toId, $itemsPerPage );
	}
	
	else
	{
		return $arr;
	}
	
	return array( 'status' => 'ok' );
}

#####################################################
#
# Save the Form Order function
#
#####################################################
function AjaxFormSort()
{
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);

	$i  = 0;
	$db = db();
	
	foreach( $_POST['ids'] as $id )
	{
		//Update the DB
		$db->update( "form_elements" )->where( 'id', $id )->set( "elem_order", $i );

		$i++;
	}
	
	$Form = GetSingleForm( $_POST['formId'] );
	
	$code = ( $Form ? FormElementToHtml( $Form['elements'], true, false ) : null );
	
	return array(
		'status' 	=> 'ok',
		'message' 	=> __( 'data-updated' ),
		'code' 		=> $code
	);
}

#####################################################
#
# New Category Form function 
#
#####################################################
function ajaxNewCategoryPost()
{
	if ( empty( $_POST ) || !isset( $_POST['postId'] ) )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$db   = db();
	
	$la = $db->from( 
	null, 
	"SELECT id_lang, id_blog, id_site
	FROM `" . DB_PREFIX . POSTS . "`
	WHERE (id_post = " . (int) $_POST['postId'] . ")"
	)->single();
	
	if ( !$post )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$title = ( empty( $_POST['title'] ) ? 'Uncategorized' : $_POST['title'] );
	
	//Let's add the new category
	$sef = SetShortSef( 'categories', 'id', 'sef', CreateSlug( ( !empty( $_POST['slug'] ) ? $_POST['slug'] : $title ) ), $post['id_blog'], $post['id_site'], true, $post['id_lang'] );
	
	$dbarr = array(
		"id_lang" 		=> $post['id_lang'],
		"id_site" 		=> $post['id_site'],
		"id_blog" 		=> $post['id_blog'],
		"is_default" 	=> 0,
		"name" 			=> $title,
		"sef" 			=> $sef,
		"descr" 		=> $_POST['descr']
	);

	$q = $db->insert( 'categories' )->set( $dbarr );
	
	$id = null;

	if ( $q )
	{
		$id = $db->lastId();
	}
	
	if ( !$id )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	return array( 'success' => __( 'new-category-created' ), 'title' => $title, 'id' => $id );
}

#####################################################
#
# New Category Form function 
#
#####################################################
function ajaxNewCategoryForm()
{
	if ( empty( $_POST ) || !isset( $_POST['post'] ) )
		return __( 'an-error-happened' );
	
	$data = '
	<div class="form-group">
		<label class="form-label" for="catTitle">' . __( 'title' ) . '</label>
		<input type="text" name="catTitle" id="newCatTitle" class="form-control mb-4" placeholder="' . __( 'enter-title' ) . '" value="" required>
		<small id="catTitleHelp" class="form-text text-muted">' . __( 'add-title-tip' ) . '</small>
	</div>';
	
	$data .= '
	<div class="form-group">
		<label class="form-label" for="catSlug">' . __( 'slug' ) . '</label>
		<input type="text" name="catSlug" id="newCatSlug" class="form-control mb-4" value="">
		<small id="catSlugHelp" class="form-text text-muted">' . __( 'category-slug-tip' ) . '</small>
	</div>';
	
	$data .= '
	<div class="form-group">
		<label class="form-label" for="catDescr">' . __( 'description' ) . '</label>
		<input type="text" name="catDescr" id="newCatDescr" class="form-control mb-4" value="">
		<small id="catDescrHelp" class="form-text text-muted">' . __( 'descr-tip' ) . '</small>
	</div>' . PHP_EOL;
	
	$data .= '
	<input type="hidden" id="newCatPostId" value="' . $_POST['post'] . '">' . PHP_EOL;

	return $data;
}

#####################################################
#
# Save the Logs function
#
#####################################################
function ajaxSaveAdminLogs()
{
	global $Admin;
	
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);
	
	$userId 	= ( isset( $_POST['user'] ) ? $_POST['user'] : $Admin->UserID() );
	$siteId 	= ( isset( $_POST['site'] ) ? $_POST['site'] : $Admin->GetSite() );
	$langId 	= ( isset( $_POST['lang'] ) ? $_POST['lang'] : null );
	$blogId 	= ( isset( $_POST['blog'] ) ? $_POST['blog'] : null );
	$showAll 	= ( isset( $_POST['showAll'] ) ? $_POST['showAll'] : null );
	
	$db 	= db();

	$data 	= AdminLogCounts( $userId, $siteId, $langId, $blogId, $showAll );
	
	if ( empty( $data ) || ( $data['totalNotes'] == 0 ) )
		return $arr;
	
	if ( isset( $data['comments'] ) && !empty( $data['comments'] ) && ( $data['comments']['num'] > 0 ) && !empty( $data['comments']['data'] ) )
	{
		foreach( $data['comments']['data'] as $com )
		{
			$dbarr = array(
				"id_member"		=> $userId,
				"id_comment"	=> $com['id'],
				"added_time"	=> time(),
				"id_site"		=> $siteId
			);

			$db->insert( 'log_comments' )->set( $dbarr );
		}
	}
	
	if ( isset( $data['logs'] ) && !empty( $data['logs'] ) && ( $data['logs']['num'] > 0 ) && !empty( $data['logs']['data'] ) )
	{
		foreach( $data['logs']['data'] as $log )
		{
			$dbarr = array(
				"id_member"		=> $userId,
				"id_log"		=> $log['id'],
				"added_time"	=> time(),
				"id_site"		=> $siteId
			);

			$db->insert( 'log_log' )->set( $dbarr );
		}
	}

	return array(
		'status' => 'ok',
		'message' => __( 'data-updated' )
	);
}

#####################################################
#
# Add a new Widget function
#
#####################################################
function ajaxAddWidget()
{
	global $Admin;
	
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);
	
	if ( !isset( $_POST['pos'] ) || !isset( $_POST['w'] ) || !isset( $_POST['site'] ) || !isset( $_POST['lang'] ) || !isset( $_POST['theme'] ) )
		return $arr;
	
	require ( ARRAYS_ROOT . 'generic-arrays.php');
	
	$pos = Sanitize( $_POST['pos'], false );
	$w = Sanitize( $_POST['w'], false );
	$theme = Sanitize( $_POST['theme'], false );
	
	if ( !isset( $builtInWidgets[$w] ) )
		return $arr;
	
	$db 	= db();
	
	$widget = $builtInWidgets[$w];
	
	$widg 	= $db->from( 
	null, 
	"SELECT widget_order
	FROM `" . DB_PREFIX . "widgets`
	WHERE (theme = :theme) AND (id_site = " . (int) $_POST['site'] . ") AND (id_lang = " . (int) $_POST['lang'] . ")
	ORDER BY widget_order DESC LIMIT 1",
	array( $theme => ':theme' )
	)->single();
	
	$order 	= ( $widg ? ( $widg['widget_order'] + 1 ) : 1 );
	$wId 	= null;
	
	$dbarr = array(
		"id_site"		=> (int) $_POST['site'],
		"id_lang"		=> (int) $_POST['lang'],
		"title"			=> $widget['title'],
		"type"			=> $widget['type'],
		"theme"			=> $theme,
		"theme_pos"		=> $pos,
		"added_time"	=> time(),
		"widget_order"	=> (int) $order,
		"build_in"		=> $w,
		"data"			=> json_encode( array() ),
	);

	$q = $db->insert( 'widgets' )->set( $dbarr );
		
	if ( $q )
	{
		$wId = $db->lastId();
	}
	
	if ( $wId )
	{
		$url = 'edit-widget' . PS . 'id' . PS . $wId;
		
		$html = '<div class="card collapsed-card" id="sort" data-id="' . $wId . '"><div class="card-header border-transparent"><h4 class="card-title">' . $widget['title'] . '</h4><div class="btn-group text-right" style="text-align: right !important;"><button type="button" class="btn btn-tool" data-toggle="dropdown"><i class="fas fa-edit"></i></button><div class="dropdown-menu dropdown-menu-right" role="menu"><a href="' . $Admin->GetUrl( 'edit-widget' . PS . 'id' . PS . $wId ) . '" class="dropdown-item">' . __( 'edit' ) . '</a><a href="' . $Admin->GetUrl( 'delete-widget' . PS . 'id' . PS . $wId ) . '" class="dropdown-item" onclick="return confirm(\'' . __( 'are-you-sure-you-want-to-delete-this-widget' ) . '\');">' . __( 'delete' ) . '</a></div></div></div></div><script>$(\'#sort .card-header\').css(\'cursor\',\'move\');</script>';
		
		return array(
			'status' => 'ok',
			'html' => $html,
			'pos' => $pos,
			'message' => __( 'widget-added' )
		);
	}
	else
	{
		return $arr;
	}
}

#####################################################
#
# Save the Widgets' Order function
#
#####################################################
function ajaxWidgetSort()
{
	global $Admin;
	
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);

	$i 			= 1;
	
	$pos 		= ( isset( $_POST['pos'] ) ? Sanitize( $_POST['pos'], false ) : 'primary' );
	$theme 		= ( isset( $_POST['theme'] ) ? Sanitize( $_POST['theme'], false ) : null );

	$disabled 	= ( ( $pos == 'inactive' ) ? 1 : 0 );
	
	$db 		= db();

	foreach( $_POST['ids'] as $id )
	{
		//Get some info first
		$widg = $db->from( 
		null, 
		"SELECT theme, disabled
		FROM `" . DB_PREFIX . "widgets`
		WHERE (id = " . $id . ")"
		)->single();
			
		if ( !$widg )
			continue;
		
		$sameTheme 	= ( $theme && ( $theme == $widg['theme'] ) );
		$canMove 	= ( $widg['disabled'] && ( $pos != 'inactive' ) && ( $theme != $widg['theme'] ) );
		
		$dbarr	 	= array( "widget_order" => (int) $i );
		
		if ( $sameTheme )
		{
			$dbarr["theme_pos"] = $pos;
			$dbarr["disabled"] 	= $disabled;
			
			if ( $canMove )
			{
				$dbarr["theme"] = $theme;
			}
		}
		
		else if ( $canMove )
		{
			$dbarr["theme_pos"] = $pos;
			$dbarr["theme"] 	= $theme;
			$dbarr["disabled"] 	= 0;
		}
		
		//We can update the DB
		$db->update( 'widgets' )->where( 'id', $id )->set( $dbarr );

		$i++;
	}

	$Admin->DeleteSettingsCacheSite( 'widgets' );

	return array(
		'status' => 'ok',
		'message' => __( 'data-updated' )
	);
}	

#####################################################
#
# Save the Dashboard function
#
#####################################################
function ajaxDashboardSort()
{
	global $Admin;
	
	$arr = array(
			'status' => 'error',
			'message' => __( 'an-error-happened' )
	);
	
	if ( empty( $_POST ) || !isset( $_POST['user'] ) || !isset( $_POST['site'] ) )
		return $arr;
	
	$usId 	= (int) $_POST['user'];
	
	$db 	= db();
	
	//Are we in a child site?
	if ( $_POST['site'] != SITE_ID )
	{
		$usr = $db->from( 
		null, 
		"SELECT id_cloned_member
		FROM `" . DB_PREFIX . "members_relationships`
		WHERE (id_member = " . $usId . ") AND (id_site = " . (int) $_POST['site'] . ")"
		)->single();
		
		if ( $usr )
			$usId = $usr['id_cloned_member'];
	}
	
	$usr = $db->from( 
	null, 
	"SELECT dashboard_data
	FROM `" . DB_PREFIX . USERS . "`
	WHERE (id_member = " . $usId . ")"
	)->single();
	
	if ( !$usr )
		return $arr;

	$data = Json( $usr['dashboard_data'] );
	
	if ( ( $_POST['action'] == 'close' ) && isset( $_POST['id'] ) && !empty( $_POST['id'] ) )
	{
		$id = Sanitize( $_POST['id'], false );
		
		$widgets = ( isset( $data['widgets'] ) ? $data['widgets'] : array() );
		
		$temp = array();
		
		if ( !empty( $widgets ) )
		{
			foreach( $widgets as $pos => $ids )
			{
				$temp[$pos] = array();
				
				foreach( $ids as $_id )
				{
					if ( $id == $_id )
					{
						continue;
					}
					
					$temp[$pos][] = $_id;
				}
			}
		}
		else
		{
			include ( ARRAYS_ROOT . 'admin-arrays.php');
			
			foreach( $leftData as $l_id => $l )
			{
				if ( $id == $l_id )
				{
					continue;
				}
				
				$temp['left'][] = $l_id;
			}
			
			foreach( $rightData as $l_id => $l )
			{
				if ( $id == $l_id )
				{
					continue;
				}
				
				$temp['right'][] = $l_id;
			}
		}
		
		$data['widgets'] = $temp;
	}
	
	elseif ( ( $_POST['action'] == 'minimize' ) && isset( $_POST['id'] ) && !empty( $_POST['id'] ) )
	{
		$mins = ( isset( $data['minimized'] ) ? $data['minimized'] : array() );
		
		$id = Sanitize( $_POST['id'], false );
		
		if ( !in_array( $id, $mins ) )
		{
			array_push( $mins, $id );
		}
			
		elseif ( in_array( $id, $mins ) )
		{
			$_mins = array();
			
			foreach( $mins as $min )
			{
				if ( $min == $id )
					continue;
				
				$_mins[] = $min;
			}
			
			$mins = $_mins;
		}

		$data['minimized'] = $mins;
	}

	elseif ( ( $_POST['action'] == 'sort' ) && isset( $_POST['pos'] ) && isset( $_POST['ids'] ) )
	{
		if ( empty( $_POST['ids'] ) || empty( $_POST['pos'] ) )
			return $arr;
		
		$widgets = ( isset( $data['widgets'] ) ? $data['widgets'] : array() );

		$pos = Sanitize( $_POST['pos'], false );
		
		//Clean everything for this position
		$widgets[$pos] = array();
		
		foreach( $_POST['ids'] as $id )
		{
			$widgets[$pos][] = Sanitize( $id, false );
		}
		
		$data['widgets'] = $widgets;
	}
	else
		return $arr;
	
	$db->update( USERS )->where( 'id_member', $usId )->set( "dashboard_data", json_encode( $data ) );
	
	return array(
			'status' => 'ok',
			'message' => __( 'data-updated' )
	);
}

#####################################################
#
# Get Details for XML Import Data function
#
#####################################################
function AjaxImportDetailsXML()
{
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);

	if ( !isset( $_POST['fileUrl'] ) || empty( $_POST['fileUrl'] ) )
	{
		return $arr;
	}
	
	if ( !Validate( $_POST['fileUrl'], 'url' ) )
	{
		$arr['message'] = __( 'please-enter-a-valid-url' );
		return $arr;
	}
	
	$fields = array();
	
	$db 	= db();
	
	$data = array(
		'crawl' 	=> array(),
		'data' 		=> array(),
		'replace' 	=> array(),
		'fields' 	=> array()
	);
	
	if ( !empty( $_POST['arr'] ) )
	{
		foreach( $_POST['arr'] as $ar )
		{
			foreach( $ar as $a => $r )
			{
				$data['crawl'][$a] = array( 'value' => EscapeRegex( $r ) );
			}
		}
	}
	
	if ( !empty( $_POST['crawl'] ) )
	{
		foreach( $_POST['crawl'] as $ar )
		{
			foreach( $ar as $a => $r )
			{
				$data['data'][$a] = array( 'value' => $r );
			}
		}
	}

	if ( !empty( $_POST['fields'] ) )
	{
		$data['fields'] = $_POST['fields'];
	}
	
	if ( !empty( $_POST['rpl'] ) )
	{
		$data['replace'] = $_POST['rpl'];
	}

	$name = pathinfo( $_POST['fileUrl'] );
	
	if ( empty( $name['extension'] ) || ( $name['extension'] !== 'xml' ) )
	{
		$arr['message'] = sprintf( __( 'file-is-not-a-valid-xml-file' ), $name['basename'] );
		return $arr;
	}

	$title = URLify( $name['filename'] ) . '.' . $name['extension'];
	
	$file = UPLOADS_ROOT . $title;
	
	$copyImages = ( ( isset( $_POST['copyImg'] ) && $_POST['copyImg'] ) ? 1 : 0 );
	
	$_imp = $db->from( 
	null, 
	"SELECT id, completed_time, completed
	FROM `" . DB_PREFIX . "imports`
	WHERE (file_url = :url)",
	array( $_POST['fileUrl'] => ':url' )
	)->single();
	
	$userId = ( ( !empty( $_POST['author'] ) && is_numeric( $_POST['author'] ) && ( $_POST['author'] > 0 ) ) ? $_POST['author'] : $_POST['user'] );

	if ( !$_imp )
	{
		$remote = CopyRemoteFile( $_POST['fileUrl'], $file, true );
		
		if ( !$remote || !file_exists( $file ) )
		{
			$arr['message'] = __( 'this-file-cannot-be-downloaded' );
			return $arr;
		}
		
		$dbarr = array(
			"id_site" 			=> $_POST['site'],
			"id_lang" 			=> $_POST['lang'],
			"id_category" 		=> $_POST['cat'],
			"prv_system" 		=> $_POST['system'],
			"filename" 			=> $title,
			"old_url" 			=> $_POST['oldUrl'],
			"copy_images" 		=> $copyImages,
			"id_blog" 			=> $_POST['blog'],
			"added_time" 		=> time(),
			"extra_data" 		=> json_encode( $data, JSON_UNESCAPED_UNICODE ),
			"file_url" 			=> $_POST['fileUrl'],
			"file_id" 			=> md5( $title ),
			"post_status" 		=> $_POST['type'],
			"id_member" 		=> $userId,
			"id_custom_type" 	=> $_POST['custom'],
		);

		$db->insert( 'imports' )->set( $dbarr );
	}
	else
	{
		if ( !file_exists( $file ) || ( filemtime( $file ) < ( time() - 3600 ) ) )
		{
			$remote = CopyRemoteFile( $_POST['fileUrl'], $file, true );
			
			if ( !$remote || !file_exists( $file ) )
			{
				$arr['message'] = __( 'this-file-cannot-be-downloaded' );
				return $arr;
			}
		}
		
		$prevUrl = ( !empty( $_POST['oldUrl'] ) ? LastTrailCheck( $_POST['oldUrl'] ) : '' );
		
		$dbarr = array(
			"id_site" 			=> $_POST['site'],
			"id_lang" 			=> $_POST['lang'],
			"id_category" 		=> $_POST['cat'],
			"prv_system" 		=> $_POST['system'],
			"old_url" 			=> $prevUrl,
			"copy_images" 		=> $copyImages,
			"id_blog" 			=> $_POST['blog'],
			"extra_data" 		=> json_encode( $data, JSON_UNESCAPED_UNICODE ),
			"post_status" 		=> $_POST['type'],
			"id_member" 		=> $userId,
			"id_custom_type" 	=> $_POST['custom'],
		);
		
		$db->update( "imports" )->where( 'id', $_imp['id'] )->set( $dbarr );
	}
	
	include ( ARRAYS_ROOT . 'generic-arrays.php');

	$importData = $importDataArray['xml'];
	
	require( CLASSES_ROOT . 'Import.php' );
	
	require( $importData['file'] );

	if ( !class_exists( $importData['class'] ) )
	{
		return $arr;
	}

	$IMP = new $importData['class'];
	
	$IMP->importFile = $file;
	
	$IMP->countItems();
	
	return array(
		'status' => 'ok',
		'items' => $IMP->totalItems,
		'completed' => $_imp['completed'],
		'date' => ( !empty( $_imp['completed_time'] ) ? postDate( $_imp['completed_time'], false ) : '' )
	);
}

#####################################################
#
# Get Details for Import Data function
#
#####################################################
function ajaxImportDetails()
{
	global $Admin;
	
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);
	
	if ( !isset( $_POST['id'] ) || empty( $_POST['id'] ) )
	{
		return $arr;
	}
	
	$db = db();
	
	$_imp = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "imports`
	WHERE (id = " . (int) $_POST['id'] . ")"
	)->single();
		
	if ( !$_imp )
	{
		return $arr;
	}
		
	include ( ARRAYS_ROOT . 'generic-arrays.php');
		
	if ( !isset( $importDataArray[$_POST['system']] ) || empty( $_imp['filename'] ) )
	{
		return $arr;
	}

	$importData = $importDataArray[$_POST['system']];

	$importFile = UPLOADS_ROOT . $_imp['filename'];

	if ( !file_exists( $importFile ) )
	{
		return array(
			'status' => 'error',
			'message' => sprintf( __( 'import-file-error' ), $_imp['filename'], UPLOADS_ROOT )
		);
	}

	require( CLASSES_ROOT . 'Import.php' );
	
	require( $importData['file'] );

	if ( !class_exists( $importData['class'] ) )
	{
		return $arr;
	}

	$IMP = new $importData['class'];
	
	$IMP->importFile = $importFile;
	
	$IMP->countItems();
	
	return array(
		'status' => 'ok',
		'items' => $IMP->totalItems,
		'completed' => $_imp['completed'],
		'date' => ( !empty( $_imp['completed_time'] ) ? postDate( $_imp['completed_time'], false ) : '' )
	);
}

#####################################################
#
# Reset an Import Job
#
#####################################################
function AjaxResetImport()
{
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);
	
	$okArr = array(
		'status' => 'ok',
		'message' => null
	);
	
	if ( !isset( $_POST['fileUrl'] ) || empty( $_POST['fileUrl'] ) )
	{
		return $arr;
	}
	
	$db = db();
	
	$q = $db->from( 
	null, 
	"SELECT id, completed, file_id
	FROM `" . DB_PREFIX . "imports`
	WHERE (file_url = :url) AND (id_site = " . (int) $_POST['site'] . ")",
	array( $_POST['fileUrl'] => ':url' )
	)->single();

	if ( !$q || empty( $q['file_id'] ) )
	{
		return $okArr;
	}
	
	$file = DB_DATA_ROOT . $q['file_id'] . '.php';
	
	@unlink( $file );
	
	$dbarr = array(
		"completed" 		=> 0,
		"completed_time" 	=> 0
	);

	$db->update( "imports" )->where( 'id', $q['id'] )->set( $dbarr );
	
	return $okArr;
}
#####################################################
#
# Import Content From XML function
#
#####################################################
function AjaxImportXMLContent()
{
	global $Admin;
	
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);
	
	if ( !isset( $_POST['fileUrl'] ) || empty( $_POST['fileUrl'] ) )
	{
		return $arr;
	}
	
	$db = db();
	
	$imp = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "imports`
	WHERE (file_url = :url)",
	array( $_POST['fileUrl'] => ':url' )
	)->single();

	if ( !$imp )
	{
		return $arr;
	}
		
	include ( ARRAYS_ROOT . 'generic-arrays.php');

	$importData = $importDataArray['xml'];

	$importFile = UPLOADS_ROOT . $imp['filename'];

	if ( !file_exists( $importFile ) )
	{
		return array(
			'status' => 'error',
			'message' => sprintf( __( 'import-file-error' ), $imp['filename'], UPLOADS_ROOT )
		);
	}
		
	require( CLASSES_ROOT . 'Import.php' );
	require( $importData['file'] );

	if ( !class_exists( $importData['class'] ) )
	{
		return $arr;
	}

	$IMP = new $importData['class'];

	$IMP->importID 		= $imp['id'];
	$IMP->importFile 	= $importFile;
	$IMP->genericXml 	= true;
	
	$IMP->process();
	
	return array(
		'status' 			=> 'ok',
		'message' 			=> $IMP->message,
		'totalItems' 		=> $IMP->totalItems,
		'maxItemsPerTime' 	=> $IMP->maxItemsPerTime,
		'currentItems' 		=> $IMP->countItem,
		'countItems' 		=> $IMP->countItems
	);
}

#####################################################
#
# Import Content function
#
#####################################################
function ajaxImportContent()
{
	global $Admin;
	
	$arr = array(
			'status' => 'error',
			'message' => __( 'an-error-happened' )
	);
	
	if ( !isset( $_POST['id'] ) || empty( $_POST['id'] ) )
	{
		return $arr;
	}
	
	$db = db();
	
	$imp = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "imports`
	WHERE (id = " . (int) $_POST['id'] . ")"
	)->single();
		
	if ( !$imp )
	{
		return $arr;
	}
		
	include ( ARRAYS_ROOT . 'generic-arrays.php');
		
	if ( !isset( $importDataArray[$_POST['system']] ) || empty( $imp['filename'] ) )
	{
		return $arr;
	}

	$importData = $importDataArray[$_POST['system']];

	$importFile = UPLOADS_ROOT . $imp['filename'];

	if ( !file_exists( $importFile ) )
	{
		return array(
			'status' => 'error',
			'message' => sprintf( __( 'import-file-error' ), $imp['filename'], UPLOADS_ROOT )
		);
	}
		
	require( CLASSES_ROOT . 'Import.php' );
	require( $importData['file'] );

	if ( !class_exists( $importData['class'] ) )
	{
		return $arr;
	}

	$IMP = new $importData['class'];
	$IMP->importID = (int) $_POST['id'];
	$IMP->importFile = $importFile;

	$IMP->process();
	
	$Admin->EmptyCaches();
	
	return array(
		'status' => 'ok',
		'message' => $IMP->message,
		'totalItems' => $IMP->totalItems,
		'maxItemsPerTime' => $IMP->maxItemsPerTime,
		'currentItems' => $IMP->countItem,
		'countItems' => $IMP->countItems
	);
}

#####################################################
#
# Map Single Post function
#
#####################################################
function ajaxMapSinglePost()
{
	$arr = array(
			'status' => 'error',
			'message' => __( 'an-error-happened' )
	);
	
	if ( !isset( $_POST['pId'] ) || !is_numeric( $_POST['pId'] ) || !isset( $_POST['sId'] ) )
	{
		return $arr;
	}

	$id = (int) $_POST['pId'];
	$siteId = (int) $_POST['siteId'];
	$post = $_POST['sId'];
	$url = $_POST['sUrl'];

	$db = db();
	
	//Get the site id from the post
	$q = $db->from( 
	null, 
	"SELECT p.id_post, p.id_site, p.post_type, d.ext_id
	FROM `" . DB_PREFIX . POSTS . "` as p
	WHERE (p.id_post = " . $id . ")
	LEFT JOIN `" . DB_PREFIX . "posts_data` AS d ON d.id_post = p.id_post"
	)->single();
	
	if ( !$q )
		return $arr;
	
	$file = ( ( $q['post_type'] == 'post' ) ? EXTERNAL_POSTS_FILE : EXTERNAL_PAGES_FILE );
	
	$postsData = OpenFileDB( $file );
	
	if ( empty( $postsData ) || !isset( $postsData[$siteId] ) || empty( $postsData[$siteId] ) || !isset( $postsData[$siteId][$post] ) )
		return $arr;
	
	if ( empty( $q['ext_id'] ) )
	{
		$dbarr = array(
			"ext_id" 		=> $post,
			"external_url" 	=> $url
		);

		$q = $db->update( "posts_data" )->where( 'id_post', $id )->set( $dbarr );
		
		if ( $q )
		{
			unset( $postsData[$siteId][$post] );
		}
	}
	else
	{
		unset( $postsData[$siteId][$post] );
	}
	
	WriteFileDB( $postsData, EXTERNAL_POSTS_FILE );

	return array(
		'status' => 'ok',
		'message' => __( 'data-updated' )
	);
}

#####################################################
#
# Save Blocks function
#
#####################################################
function ajaxSaveBlocks()
{
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);
	
	if ( !isset( $_POST['post'] ) || !is_numeric( $_POST['post'] ) || !isset( $_POST['data'] ) )
	{
		return $arr;
	}

	$id 	= (int) $_POST['post'];
	$user 	= (int) $_POST['user'];
	$html 	= GetBlocksHtmlData( $id );
	$html 	= ( !$html ? '' : $html );
	$db 	= db();
	
	//Get the site id from the post
	$q = $db->from( 
	null, 
	"SELECT id_site
	FROM `" . DB_PREFIX . POSTS . "`
	WHERE (id_post = " . $id . ")"
	)->single();
	
	$dbarr = array(
		"post_id" 		=> $id,
		"user_id" 		=> $user,
		"added_time" 	=> time(),
		"post" 			=> $html,
		"blocks" 		=> json_encode( $_POST['data'], JSON_UNESCAPED_UNICODE ),
		"id_site" 		=> ( $q ? $q['id_site'] : SITE_ID )
	);

	$itm = $db->insert( 'posts_autosaves' )->set( $dbarr );
	
	if ( !$itm )
		return $arr;
	
	return array(
		'status' => 'ok',
		'message' => __( 'data-updated' )
	);
}

#####################################################
#
# Search for Posts function
#
#####################################################
function ajaxGetSyncPosts()
{
	$arr = array( 'results' => array() );

	$siteId = (int) $_POST['postSite'];
	$langId = (int) $_POST['postLang'];
	$type 	= $_POST['postType'];
	
	$t = "(";

	if ( is_array( $type ) )
	{
		if ( in_array( 'posts', $type ) && !in_array( 'pages', $type ) )
			$t .= "p.post_type = 'post'";
		
		elseif ( in_array( 'pages', $type ) && !in_array( 'posts', $type ) )
			$t .= "p.post_type = 'page'";
			
		else
			$t .= "p.post_type = 'page' OR p.post_type = 'post'";
	}
	
	else
	{
		if ( $type == 'posts' )
			$t .= "p.post_type = 'post'";
		
		elseif ( $type == 'pages' )
			$t .= "p.post_type = 'page'";
			
		//Just in case...
		else
			$t .= "p.post_type = 'post'";
	}

	$t .= ")";
	
	$db = db();
	
	$q = $db->from( 
	null, 
	"SELECT id_post, title
	FROM `" . DB_PREFIX . POSTS . "`
	WHERE (p.id_site = " . $siteId . ") AND (p.id_lang = " . $langId . ") AND (p.post_status = 'published' OR p.post_status = 'draft') AND " . $t . "
	ORDER BY title ASC
	LIMIT " . HOMEPAGE_ITEMS
	)->all();
	
	if ( !$q )
	{
		return array(
			'error' => __( 'nothing-found' )
		);
	}
	
	foreach ( $q as $p )
	{
		$arr['results'][] = array(
				'disabled' => false,
				'id' => $p['id_post'],
				'text' => htmlspecialchars( stripslashes( $p['title'] ) )
		);
	}

	return $arr;
}

#####################################################
#
# Remove single variation function
#
#####################################################
function AjaxRemoveSingleVariation()
{
	$id = (int) $_POST['id'];
	
	$db = db();
	
	$v = $db->from( 
	null, 
	"SELECT id, id_parent
	FROM `" . DB_PREFIX . "post_variations_items`
	WHERE (id_post = " . $id . ")"
	)->single();
	
	if ( !$v )
	{
		return array(
			'status' => 'error'
		);
	}
	
	$db->delete( 'post_variations_items' )->where( "id", $v['id'] )->run();
	
	$q = $db->from( null, 
	"SELECT COUNT(id) as total
	FROM `" . DB_PREFIX . "post_variations_items`
	WHERE (id_parent = " . $v['id_parent'] . ")"
	)->total();
	
	return array(
		'status' => 'ok',
		'items'  => $q
	);
}

#####################################################
#
# Add single variation function
#
#####################################################
function AjaxAddSingleVariation()
{
	$gId 	= (int) $_POST['gId'];
	$postId = (int) $_POST['postId'];
	$vTitle = '';
	$html 	= '';
	$db 	= db();
	
	//Check if this is a valid group
	$q = $db->from( 
	null, 
	"SELECT id, title, id_site, id_lang
	FROM `" . DB_PREFIX . "post_variations`
	WHERE (id = " . $gId . ")"
	)->single();
	
	if ( !$q )
	{
		return array(
			'status' => 'error'
		);
	}
	
	$gTitle = StripContent( $q['title'] );
	
	//Check if this post is in another group
	$v = $db->from( 
	null, 
	"SELECT id, id_parent, title, ptitle
	FROM `" . DB_PREFIX . "post_variations_items`
	WHERE (id_post = " . $postId . ")"
	)->single();

	//If this post exists, then check the parent
	if ( $v && ( $v['id_parent'] != $gId ) )
	{
		$vTitle = $v['title'];
		$pTitle = $v['ptitle'];
		$vId 	= $v['id'];
		
		//Update this item and set the new parent
		$db->update( "post_variations_items" )->where( 'id', $v['id'] )->set( "id_parent", $gId );
	}
	
	else
	{
		$tmp = GetSinglePost( $postId, $q['id_site'], false );
		
		$pTitle = ( $tmp ? $tmp['title'] : '' );
		$url    = ( $tmp ? $tmp['postUrl'] : '' );
		
		$dbarr = array(
			"title" 	=> $vTitle,
			"sef" 		=> '',
			"var_order" => 0,
			"id_post" 	=> $postId,
			"id_parent" => $gId,
			"ptitle" 	=> $pTitle,
			"url" 		=> $url
		);

		$db->insert( 'post_variations_items' )->set( $dbarr );
	}
	
	//Now get all the variations from this group
	$vars = $db->from( 
	null, 
	"SELECT id, id_post, title, ptitle, var_order
	FROM `" . DB_PREFIX . "post_variations_items`
	WHERE (id_parent = " . $gId . ")
	ORDER BY var_order ASC"
	)->all();
	
	if ( !$vars )
	{
		return array(
			'status' => 'error'
		);
	}
	
	foreach( $vars as $var )
	{
		$html .= '
		<tr id="varField-row' . $var['id_post'] . '">
			<td class="text-center"><input type="text" id="varTitle" name="variations[' . $var['id_post'] . '][title]" class="form-control mb-4" placeholder="red, blue, 128GB, etc..." value="' . $var['title'] . '" /></td>
			<td class="text-center"><input type="text" id="varpostTitle" name="variations[' . $var['id_post'] . '][postTitle]" class="form-control mb-4" value="' . htmlspecialchars( $var['ptitle'] ) . '" disabled /></td>
			<td class="text-center"><input type="number" id="varOrder" name="variations[' . $var['id_post'] . '][order]" class="form-control mb-4" value="' . $var['var_order'] . '" min="0" /></td>
			<td class="text-center">
				<button type="button" id="removePostVarButton" title="' . __( 'remove' ) . '" data-id="' . $var['id_post'] . '" class="btn btn-danger btn-flat btn-sm removePostVarButton" data-toggle="tooltip" title="' . __( 'remove' ) . '"><i class="fa fa-minus-circle"></i></button>
			</td>
		</tr>';
	}

	return array(
		'status' => 'ok',
		'html'	=> $html
	);
}

#####################################################
#
# Add variation group function
#
#####################################################
function AjaxAddVariationGroup()
{
	$id   = (int) $_POST['id'];
	$lang = (int) $_POST['lang'];
	$site = (int) $_POST['site'];
	$db   = db();
	
	$q = $db->from( 
	null, 
	"SELECT id, title
	FROM `" . DB_PREFIX . "post_variations`
	WHERE (id_post = " . $id . ")"
	)->single();
	
	if ( $q )
	{
		$gId 	= $q['id'];
		$gTitle = StripContent( $q['title'] );
	}
	
	else
	{
		$tmp = GetSinglePost( $id, $site, false );
			
		if ( !$tmp )
		{
			return array(
				'status' => 'error'
			);
		}
		
		$gTitle = 'Group-' . GenerateRandomKey( 3  );
		$pTitle = $tmp['title'];
		
		$dbarr = array(
			"id_post" 	=> $id,
			"id_site" 	=> $site,
			"id_lang" 	=> $lang,
			"title" 	=> $gTitle,
			"sef" 		=> $tmp['sef']
		);

		$q = $db->insert( 'post_variations' )->set( $dbarr );
		
		if ( $q )
		{
			$gId = $db->lastId();
		}
		
		else
		{
			return array(
				'status' => 'error'
			);
		}	
	}
	
	//First check if we have this variation
	$v = $db->from( 
	null, 
	"SELECT id, title
	FROM `" . DB_PREFIX . "post_variations_items`
	WHERE (id_parent = " . $gId . ") AND (id_post = " . $id . ")"
	)->single();

	if ( $v )
	{
		$vId 	= $v['id'];
		$vTitle = $v['title'];
	}
	
	else
	{
		$vTitle = '';
		
		$vId 	= null;
		
		$dbarr = array(
			"title" 	=> $vTitle,
			"sef" 		=> '',
			"var_order" => 0,
			"id_post" 	=> $id,
			"id_parent"	=> $gId,
			"ptitle" 	=> $pTitle,
			"url" 		=> $tmp['postUrl']
		);

		$q = $db->insert( 'post_variations_items' )->set( $dbarr );
		
		if ( $q )
		{
			$vId = $db->lastId();
		}
		
		else
		{
			return array(
				'status' => 'error'
			);
		}
	}
	
	return array(
		'status' => 'ok',
		'gId'	 => $gId,
		'vId'	 => $vId,
		'vTitle' => $vTitle,
		'pTitle' => $pTitle,
		'gTitle' => $gTitle
	);
}

#####################################################
#
# Remove variation group function
#
#####################################################
function AjaxRemoveVariationGroup()
{
	$id = (int) $_POST['id'];
	
	$db = db();
	
	$q = $db->delete( 'post_variations' )->where( "id", $id )->run();
	
	if ( !$q )
	{
		return array(
			'status' => 'error'
		);
	}
	
	$db->delete( 'post_variations_items' )->where( "id_parent", $id )->run();
	
	return array(
		'status' => 'ok'
	);
}

#####################################################
#
# Search Posts Variants
#
#####################################################
function AjaxGetVariants()
{
	$arr = array( 'results' => array() );

	$lang 	= (int) $_POST['lang'];
	$site 	= (int) $_POST['site'];
	$key 	= $_POST['query'];
	$db 	= db();
	
	$res = $db->from( null, "
	SELECT id, title
	FROM `" . DB_PREFIX . "post_variations`
	WHERE (id_site = " . $site . ") AND (id_lang = " . $lang . ") AND title LIKE :query
	LIMIT " . 10,
	array( '%' . $key . '%' => ':query' )
	)->all();
	
	if ( $res )
	{
		foreach( $res as $p )
		{
			$arr['results'][] = array(
					'disabled' => false,
					'id' => $p['id'],
					'text' => StripContent( $p['title'] )
			);
		}
	}

	return $arr;
}

#####################################################
#
# Search for Posts with Prices function
#
#####################################################
function ajaxSearchForPostsPrices()
{
	$arr = array( 'results' => array() );

	$lang 	= (int) $_POST['lang'];
	$site 	= (int) $_POST['site'];
	$key 	= $_POST['query'];
	$db 	= db();
	
	$res = $db->from( null, "
	SELECT p.id_post, p.title, (SELECT COUNT(id_price) FROM `" . DB_PREFIX . "prices` as pr WHERE pr.id_post = p.id_post) as num_prices
	FROM `" . DB_PREFIX . POSTS . "` AS p
	WHERE (p.id_site = " . $site . ") AND (p.id_lang = " . $lang . ") AND (p.post_status = 'published') AND (p.post_type = 'post') AND (p.title LIKE :query)
	LIMIT " . 10,
	array( '%' . $key . '%' => ':query' )
	)->all();

	if ( $res )
	{
		foreach( $res as $p )
		{
			$arr['results'][] = array(
					'disabled' => false,
					'id' => $p['id_post'],
					'num' => $p['num_prices'],
					'text' => htmlspecialchars( stripslashes( $p['title'] ) )
			);
		}
	}

	return $arr;
}

#####################################################
#
# Search for Posts function
#
#####################################################
function ajaxSearchForPosts()
{	
	$arr	= array();
	
	$from 	= null;
	
	$db 	= db();

	$deepSearch = ( ( $_POST['deepSearch'] == 'false' ) ? false : true );
	
	$search = ( !empty( $_POST['search'] ) ? Sanitize( $_POST['search'], false ) : null );
	
	$langId = ( isset( $_POST['lang'] ) ? (int) $_POST['lang'] : null );
	$siteId = (int) $_POST['site'];
	$blogId = ( isset( $_POST['blog'] ) ? (int) $_POST['blog'] : null );
	
	$q = "";

	if ( !$deepSearch )
	{
		$q .= "(p.id_site = " . $siteId . ") AND (p.id_lang = " . $langId . ") AND (p.id_blog = " . $blogId . ") AND ";
	};
	
	$search = '%' . Sanitize ( $search, false ) . '%';
	
	$q .= "(p.post_type = 'post' OR p.post_type = 'page') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL) AND (p.title LIKE '" . $search . "' OR p.post LIKE '" . $search . "')";
	
	$query = PostsDefaultQuery( $q, HOMEPAGE_ITEMS, 'p.added_time DESC', null, false );

	//Query: posts
	$data = $db->from( null,
	$query
	)->all();
	
	$s = GetSettingsData( $siteId );

	if ( !$data )
	{
		return array(
			'error' => __( 'nothing-found' )
		);
	}

	$arr = array();
	
	foreach ( $data as $p )
	{
		$p = array_merge( $p, $s );
		$p = BuildPostVars( $p );
		
		$arr[] = array(
			'id' 		=> $p['id'],
			'title' 	=> StripContent( $p['title'] ),
			'postURL' 	=> $p['postUrl'],
			'postType' 	=> $p['postType'],
			'time' 		=> $p['added']['time'],
		);
	}
	
	unset( $data );
	
	return array(
		'status' => 'OK',
		'data' => $arr
	);
}

#####################################################
#
# Add/Update Ajax's enable status function
#
#####################################################
function ajaxEditApis()
{
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);
	
	$db = db();
	
	$disabled = ( ( $_POST['checked'] == 'true' ) ? 0 : 1 );
	
	//Update the DB
	$q = $db->update( "api_obj" )->where( 'id', (int) $_POST['id'] )->set( "disabled", $disabled );
	
	if ( $q )
	{
		$arr = array(
			'status' => 'ok',
			'message' => __( 'data-updated' )
		);
	}

	return $arr;
}

#####################################################
#
# Delete The Menu function
#
#####################################################
function ajaxDeleteMenu()
{
	$arr = array(
			'status' => 'error',
			'message' => __( 'an-error-happened' )
	);

	if ( empty( $_POST ) || !isset( $_POST['id'] ) || empty( $_POST['id'] ) || !is_numeric( $_POST['id'] ) )
		return $arr;
	
	$menuId = (int) $_POST['id'];
	
	$db 	= db();
	
	//First check if we have this menu
	$m = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "menus`
	WHERE (id_menu = " . $menuId . ")"
	)->single();
		
	if ( !$m )
		return $arr;
	
	$q = $db->delete( "menus" )->where( "id_menu", $menuId )->run();
	
	if ( !$q )
		return $arr;
	
	//Delete any item this manu had
	$db->delete( "menu_items" )->where( "id_menu", $menuId )->run();

	return array(
		'status' => 'ok',
		'message' => __( 'the-menu-has-been-deleted' )
	);
}

#####################################################
#
# Add/Update Menu Items function
#
#####################################################
function ajaxSaveMenu()
{
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);

	if ( empty( $_POST ) || !isset( $_POST['id'] ) || empty( $_POST['id'] ) || !is_numeric( $_POST['id'] ) || !isset( $_POST['ids'] ) || empty( $_POST['ids'] ) )
		return $arr;
	
	$db 	= db();
	
	$menuId = (int) $_POST['id'];
	
	$items = $_POST['ids'];

	$pos = ( ( isset( $_POST['ps'] ) && !empty( $_POST['ps'] ) ) ? Sanitize( $_POST['ps'], false ) : 'primary' );
	
	//First check if we have this menu
	$m = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "menus`
	WHERE (id_menu = " . $menuId . ")"
	)->single();
	
	if ( !$m )
		return $arr;
	
	else
	{
		$dbarr = array(
            "edited_time" 	=> time(),
			"location" 		=> $pos
        );

		$db->update( "menus" )->where( "id_menu", $menuId )->set( $dbarr );
	}

	foreach( $items as $id => $item )
	{
		if ( !isset( $item['id'] ) || empty( $item['id'] ) )
			continue;
		
		$newTab = ( ( ( is_string( $item['b'] ) && ( $item['b'] == 'false' ) ) || ( is_bool( $item['b'] ) && !$item['b'] ) ) ? 0 : 1 );
		
		$itmId = (int) $item['id'];
		
		$type = Sanitize( $item['t'], false );
		
		$menuItem = $db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "menu_items`
		WHERE (id_item = " . $itmId . ") AND (type = :type)",
		array( $type => ':type' )
		)->single();
		
		if ( !$menuItem )
		{
			$dbarr = array(
				"id_menu" 		=> $menuId,
				"id_item" 		=> $itmId,
				"id_parent" 	=> (int) $item['p'],
				"name" 			=> Sanitize( $item['tl'], false ),
				"label"			=> Sanitize( $item['l'], false ),
				"url" 			=> Sanitize( $item['u'], false ),
				"type" 			=> $type,
				"item_order" 	=> (int) $item['o'],
				"new_tab" 		=> $newTab
			);

			$q = $db->insert( 'menu_items' )->set( $dbarr );
		}
		
		else
		{
			$itemId = $menuItem['id'];
			
			$dbarr = array(
				"id_menu" 		=> $menuId,
				"id_item" 		=> $itmId,
				"id_parent" 	=> (int) $item['p'],
				"name" 			=> Sanitize( $item['tl'], false ),
				"label" 		=> Sanitize( $item['l'], false ),
				"url" 			=> Sanitize( $item['u'], false ),
				"type" 			=> $type,
				"item_order" 	=> (int) $item['o'],
				"new_tab" 		=> ( ( $item['b'] == 'false' ) ? 0 : 1 )
			);

			$db->update( 'menu_items' )->where( 'id', $itemId )->set( $dbarr );
		}
	}
	
	return array(
		'status' => 'ok',
		'message' => __( 'the-menu-has-been-updated' )
	);
}

#####################################################
#
# Save the Widgets Order function
#
#####################################################
function ajaxRemoveMenuItems()
{
	global $Admin;
	
	$arr = array(
			'status' => 'error',
			'message' => __( 'an-error-happened' )
	);
	
	if ( empty( $_POST ) || !isset( $_POST['menuid'] ) || !isset( $_POST['id'] ) )
		return $arr;
	
	$menuId = (int) $_POST['menuid'];
	$id 	= (int) $_POST['id'];
	$db 	= db();
	
	$itm = $db->from( 
	null, 
	"SELECT id
	FROM `" . DB_PREFIX . "menu_items`
	WHERE (id_menu = " . $menuId . ") AND (id_item = " . $id . ")"
	)->single();
	
	if ( !$itm )
		return $arr;
	
	$q = $db->delete( 'menu_items' )->where( "id", $itm['id'] )->run();
	
	//Now we have to find every child, update it and send it back
	if ( $q )
	{
		$itms = $db->from( 
		null, 
		"SELECT *
		FROM `" . DB_PREFIX . "menu_items`
		WHERE (id_menu = " . $menuId . ") AND (id_parent = " . $id . ")
		ORDER BY item_order ASC"
		)->all();
		
		if ( !$itms )
		{
			return array(
				'status' => 'ok',
				'message' => __( 'data-updated' )
			);
		}
		
		$html = null;
		
		foreach( $itms as $_itm )
		{
			//Update the DB
			$db->update( 'menu_items' )->where( 'id', $_itm['id'] )->set( 'id_parent', 0 );
				
			//Dont forget the child's children
			$children = $db->from( 
			null, 
			"SELECT *
			FROM `" . DB_PREFIX . "menu_items`
			WHERE (id_menu = " . $menuId . ") AND (id_parent = " . $_itm['id_item'] . ")
			ORDER BY item_order ASC"
			)->all();
			
			if ( !$children )
			{
				$html .= BuildMenuLi( $_itm['id_item'], $_itm['url'], $_itm['label'], $_itm['type'], __( $_itm['type'] ), $_itm['title_attr'], $_itm['new_tab'] );
			}
			
			else
			{
				$_children = '<ol>' . PHP_EOL;

				foreach( $children as $child )
				{
					$_children .= BuildMenuLi( $child['id_item'], $child['url'], $child['label'], $child['type'], __( $child['type'] ), $child['title_attr'], $child['new_tab'] );
				}
				
				$_children .= PHP_EOL . '</ol>' . PHP_EOL;
				
				$html .= BuildMenuLi( $_itm['id_item'], $_itm['url'], $_itm['label'], $_itm['type'], __( $_itm['type'] ), $_itm['title_attr'], $_itm['new_tab'], $_children );
			}
		}
		
		return array(
			'status' => 'ok',
			'message' => __( 'data-updated' ),
			'html' => $html
		);
	}
	
	return $arr;
}


#####################################################
#
# Add/Update Menu Items function
#
#####################################################
function ajaxAddMenuItems()
{
	$arr = array(
		'status' => 'error',
		'message' => __( 'an-error-happened' )
	);

	if ( empty( $_POST ) || !isset( $_POST['menuid'] ) || empty( $_POST['menuid'] ) || !is_numeric( $_POST['menuid'] ) || !isset( $_POST['group'] ) || empty( $_POST['group'] ) )
		return $arr;
	
	$group = Sanitize( $_POST['group'], false );
	
	//Custom urls don't have ids
	if ( ( $group != 'custom-link' ) && ( !isset( $_POST['ids'] ) || empty( $_POST['ids'] ) ) )
		return $arr;
	
	$menuId = (int) $_POST['menuid'];
	$ids 	= ( isset( $_POST['ids'] ) ? $_POST['ids'] : null );
	$db 	= db();
	$html 	= null;

	//Custom links works a bit different
	if ( $group == 'custom-link' )
	{
		if ( !Validate( $_POST['uri'], 'url' ) )
			return $arr;
		
		$url = Sanitize( $_POST['uri'], false );
		$text = Sanitize( $_POST['text'], false );
		
		//Check if this item already exists
		$itm = $db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "menu_items`
		WHERE (id_menu = " . $menuId . ") AND (url = '" . $url . "') AND (type = 'custom-link')"
		)->single();

		if ( $itm )
			return $arr;
		
		//Create a fake ID so we don't have to check it later in "Save Menu"
		$customId = GenerateRandomKey( 2, false, true );
		
		$html .= BuildMenuLi( (int) $customId, $url, $text, $group, __( $group ) );
		
		return array(
			'status' => 'ok',
			'html' => $html
		);
	}
	
	$tmp = $db->from( 
	null, 
	"SELECT id_site
	FROM `" . DB_PREFIX . "menus`
	WHERE (id_menu = " . $menuId . ")"
	)->single();
	
	if ( !$tmp )
	{
		return $arr;
	}
	
	$siteId = $tmp['id_site'];

	foreach( $ids as $id )
	{
		if ( empty( $id ) )
		{
			continue;
		}
		
		//Check if this item already exists
		$itm_ = $db->from( 
		null, 
		"SELECT id
		FROM `" . DB_PREFIX . "menu_items`
		WHERE (id_menu = " . $menuId . ") AND (id_item = " . $id . ") AND (type = '" . $group . "')"
		)->single();
			
		if ( $itm_ )
			continue;
	
		if ( $group == 'category' )
		{
			$cat = GetCategory( null, $id, $siteId, null, null, null, false );

			if ( $cat )
			{
				$html .= BuildMenuLi( $id, $cat['catUrl'], $cat['name'], $group, __( $group ) );
			}
		}
		
		if ( $group == 'content' )
		{
			$tmp = GetSinglePost( $id, $siteId, false );

			if ( $tmp )
			{
				$html .= BuildMenuLi( $id, $tmp['postUrl'], $tmp['title'], $group, __( $group ) );
				
				unset( $tmp );
			}
		}
	}
	
	if ( $html )
	{
		$arr = array(
			'status' => 'ok',
			'html' => $html
		);
	}

	return $arr;
}

#####################################################
#
# Get Top Tags For Ajax function
#
#####################################################
function AjaxGetTopTags()
{
	if ( !isset( $_POST['site'] ) || !isset( $_POST['lang'] ) )
		return;
	
	$siteId = (int) $_POST['site'];
	$langId = (int) $_POST['lang'];
	$cusId 	= ( isset( $_POST['cusId'] ) ? (int) $_POST['cusId'] : 0 );
	$arr 	= ( isset( $_POST['arr'] ) ? $_POST['arr'] : null );
	
	$tags = GetTopTags( $siteId, $langId, $cusId, 20, $arr );
	
	if ( empty( $tags ) )
	{
		return array( 'status' => 'error' );
	}
	
	return array( 'status' => 'ok', 'tags' => $tags );
}

#####################################################
#
# Get All Tags For Ajax function
#
#####################################################
function GetAjaxTags( $langId )
{
	$array = array();
	
	$checkForArr = false;
	
	if ( !empty( $arr ) )
	{
		foreach( $arr as $i )
		{
			$array[] = (int) $i;
		}
		
		if ( !empty( $array ) )
		{
			$checkForArr = true;
		}
	}
	
	$db   = db();
	
	$tags = array();
	
	$data = $db->from( 
	null, 
	"SELECT id, title, sef
	FROM `" . DB_PREFIX . "tags`
	WHERE (id_lang = " . (int) $langId . ")
	ORDER BY num_items DESC"
	)->all();

	if( $data ) 
	{
		foreach ( $data as $t )
		{
			$tags[] = $t['title'];
		}
		
		unset( $data );
	}
	
	return $tags;
}

#####################################################
#
# Upload new file via drag n drop function
#
#####################################################
function ajaxUploadDropFile()
{
	global $Admin;
	
	//Make sure we allow uploads
	if ( !IsAllowedTo( 'manage-attachments' ) )
	{
		return array( 
			'status' => 1,
			'message' => __( 'sorry-you-are-not-allowed-to-upload-files' )
		);
	}
	
	$tmp_name = $_FILES['file']['tmp_name'];
	$tmp_size = $_FILES['file']['size'];
	$uploadMaxFilesize = ParseFiseSize( ini_get('upload_max_filesize') );

	if ( ( $uploadMaxFilesize > 0 ) && ( $tmp_size > 0 ) && ( $tmp_size > $uploadMaxFilesize ) )
	{
		return array(
				'status' => 1,
				'message' => __( 'file-size-error' )
		);
	}
	
	$site = $_POST['site'];
	$lang = $_POST['lang'];
	$postId = $_POST['id'];
	
	$name = pathinfo( $_FILES['file']['name'] );
	
	$allowed = AllowedExt( $site );

	//Make sure we allow the extension
	if ( empty( $allowed ) || !in_array( $name['extension'], $allowed ) )
	{
		return array( 
				'status' => 1,
				'message' => __( 'error-this-extension-is-not-allowed' )
		);
	}
	
	$sefName = URLify( $name['filename'] );

	$fileName = $sefName . '.' . $name['extension'];
	
	$time = time();

	$idFolder = 0;
	
	$local = $Admin->ImageUpladDir( SITE_ID );
	
	$root = ( !empty( $local ) ? $local['root'] : null );
	
	$folder = FolderRootByDate( $time, $root );
	
	//Set the image's url
	$imgUrl = ( !empty( $local ) ? FolderUrlByDate( $time, $local['html'] ) : FolderUrlByDate( $time ) ) . $fileName;
	
	$imgRoot = $folder . $fileName;
	
	if( move_uploaded_file( $tmp_name, $imgRoot ) )
	{
		$imageID = addDbImage( $fileName, $folder, $site, $Admin->UserID(), 'post', 'full', $idFolder, $time, null, $lang, $postId );
		
		if ( $imageID )
		{
			$share = $Admin->ImageUpladDir( $site );
			
			//If we have child site(s), ask them to copy the image
			if ( !empty( $share ) && isset( $share['share'] ) && $share['share'] )
			{
				$Admin->PingChildSite( 'sync', 'image', null, $site, $imgUrl, $time );
			}
			
			//Create the smaller images
			CreateChildImgs( $imgRoot, $imageID, $time, $folder, $site, 0, 0, 'post' );
		}
		
		else
		{
			return array( 
				'status' => 1,
				'message' => __( 'an-error-happened' )
			);
		}
	}
	
	else
	{
		return array( 
			'status' => 1,
			'message' => __( 'an-error-happened' )
		);
	}
	
	return array( 
			'status'  => 0,
			'imageId' => $imageID,
			'message' => __( 'image-uploaded' )
	);
}


#####################################################
#
# Add a cover image to Price function
#
#####################################################
function AjaxAddCoverPrice()
{
	if ( empty( $_POST['imId'] ) || empty( $_POST['prId'] ) )
		return array( 'status' => 'error' );
	
	$imId 	= (int) $_POST['imId'];
	$prId 	= (int) $_POST['prId'];
	$db 	= db();
	
	$db->update( "prices" )->where( 'id_price', $prId )->set( "image_id", $imId );

	return array( 'status' => 'ok' );	
}

#####################################################
#
# Remove Price function
#
#####################################################
function AjaxRemovePrice()
{
	if ( !isset( $_POST['pid'] ) || empty( $_POST['pid'] ) )
		return array( 'status' => 'error' );
	
	$id = (int) $_POST['pid'];
	
	$db = db();
	
	//Check if this price exists
	$price = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "prices`
	WHERE (id_price = " . $id . ")"
	)->single();
	
	if ( !$price )
	{
		return array( 'status' => 'error' );
	}
	
	//Delete this price
	$db->delete( 'prices' )->where( "id_price", $id )->run();
	
	//Delete any other data this price may have
	$db->delete( 'price_info' )->where( "id_price", $id )->run();
	
	$db->delete( 'price_update_info' )->where( "id_price", $id )->run();

	return array( 'status' => 'ok' );	
}

#####################################################
#
# Edit Price function
#
#####################################################
function AjaxEditPrice()
{
	if ( !isset( $_POST['arr'] ) || empty( $_POST['arr'] ) )
		return array( 'status' => 'error' );
	
	$arr = array();
	
	foreach( $_POST['arr'] as $ar )
	{
		foreach( $ar as $key => $val )
		{
			$arr[$key] = array( 'value' => $val );
		}
	}
	
	$id = (int) $arr['prId']['value'];
	
	$db = db();
	
	//Check if this price exists
	$pr = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "prices`
	WHERE (id_price = " . $id . ")"
	)->single();
		
	if ( !$pr )
	{
		return array( 'status' => 'error' );
	}
	
	$start = ( !empty( $arr['startingPrice']['value'] ) ? 1 : 0 );
	
	$dbarr = array(
        "id_currency" 		=> (int) $arr['currId']['value'],
		"id_store" 			=> (int) $arr['storeId']['value'],
		"title" 			=> $arr['inputTitle']['value'],
		"sale_price" 		=> $arr['salePrice']['value'],
		"main_page_url" 	=> $arr['priceUrl']['value'],
		"aff_page_url" 		=> $arr['priceAffUrl']['value'],
		"link_text" 		=> $arr['linkTitle']['value'],
		"is_starting_price" => $start,
		"content" 			=> $arr['priceDescr']['value'],
		"extra_text" 		=> $arr['extraText']['value']
	);

	$q = $db->update( 'prices' )->where( 'id_price', $id )->set( $dbarr );

	if ( !$q )
	{
		return array( 'status' => 'error' );
	}
	
	$db->update( 'price_info' )->where( 'id_price', $id )->set( "last_time_updated", time() );
	
	//Reload this price
	$price = adminSinglePrice( $id );
	
	if( !empty( $price['main_page_url'] ) ) 
	{
		$url = '<a href="' . $price['main_page_url'] . '" target="_blank" rel="noopener noreferrer">' . $price['st'] . '</a>';
	}
	
	else
	{
		$url = $price['st'];
	}
	
	$retPrice 	= $price['sale_price'];
	$added 		= postDate( $price['time_added'] );
	$curr		= $price['cs'];
	$updated 	= ( !empty( $price['lu'] ) ? postDate( $price['lu'] ) : '-' );
	
	return array( 'status' => 'ok', 'url' => $url, 'price' => $retPrice, 'date' => $added, 'updated' => $updated, 'curr' => $curr );
}

#####################################################
#
# Add New Price function
#
#####################################################
function AjaxAddNewPrice()
{
	if ( !isset( $_POST['arr'] ) || empty( $_POST['arr'] ) )
		return array( 'status' => 'error' );
	
	$arr = array();
	
	$db = db();
	
	foreach( $_POST['arr'] as $ar )
	{
		foreach( $ar as $key => $val )
		{
			$arr[$key] = array( 'value' => $val );
		}
	}
	
	//Check if this price exists
	$price = $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "prices`
	WHERE (id_post = " . (int) $arr['postId']['value'] . ")
	AND (id_store = " . (int) $arr['storeId']['value'] . ")
	AND (id_currency = " . (int) $arr['currId']['value'] . ")"
	)->single();
	
	if ( $price )
	{
		return array( 'status' => 'exists' );
	}
	
	$start = ( !empty( $arr['startingPrice']['value'] ) ? 1 : 0 );
	
	$dbarr = array(
		"id_post" 				=> (int) $arr['postId']['value'],
		"id_currency" 			=> (int) $arr['currId']['value'],
		"id_site" 				=> (int) $arr['siteId']['value'],
		"id_store" 				=> (int) $arr['storeId']['value'],
		"user_id" 				=> (int) $arr['userId']['value'],
		"title" 				=> $arr['inputTitle']['value'],
		"time_added" 			=> time(),
		"main_page_url" 		=> $arr['priceUrl']['value'],
		"aff_page_url" 			=> $arr['priceAffUrl']['value'],
		"link_text" 			=> $arr['linkTitle']['value'],
		"is_starting_price" 	=> $start,
		"content" 				=> $arr['priceDescr']['value'],
		"extra_text" 			=> $arr['extraText']['value'],
		"sale_price" 			=> $arr['salePrice']['value']
	);
    
	$put = $db->insert( 'prices' )->set( $dbarr );

	if ( !$put )
	{
		return array( 'status' => 'error' );
	}
	
	$id = $db->lastId();
	
	//Insert this id into the info data
	$db->insert( 'price_info' )->set( "id_price", $id );
	
	return array( 'status' => 'ok', 'id' => $id );
}

#####################################################
#
# Edit Price Details function
#
#####################################################
function ajaxEditSinglePrice()
{var_dump( "OK" );exit;

	$id 	= ( isset( $_POST['id'] ) ? (int) $_POST['id'] : null );
	$store 	= ( isset( $_POST['store'] ) ? (int) $_POST['store'] : null );
	$curr 	= ( isset( $_POST['curr'] ) ? (int) $_POST['curr'] : null );
	$title 	= ( isset( $_POST['title'] ) ? $_POST['title'] : '' );
	$p 		= ( isset( $_POST['price'] ) ? $_POST['price'] : 0 );
	$url 	= ( isset( $_POST['url'] ) ? $_POST['url'] : '' );
	$aff 	= ( isset( $_POST['aff'] ) ? $_POST['aff'] : '' );

	if ( empty( $id ) || !is_numeric( $id ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	if ( !empty( $url ) && !Validate( $url, 'url' ) )
	{
		return array( 'error' => __( 'error-please-enter-valid-url' ) );
	}
	
	$db = db();
	
	$price = $db->from(
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "prices`
	WHERE (id_price = " . $id . ")"
	)->single();
	
	if ( !$price )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	$dbarr = array(
        "id_currency" 		=> $curr,
		"id_store" 			=> $store,
		"title" 			=> $title,
		"sale_price" 		=> $p,
		"main_page_url" 	=> $url,
		"aff_page_url" 		=> $aff
	);
	
	$q = $db->update( 'prices' )->where( 'id_price', $id )->set( $dbarr );
	
	if ( !$q )
	{
		return array( 'error' => __( 'data-update-error' ) );
	}
	
	$db->update( 'price_info' )->where( 'id_price', $id )->set( "last_time_updated", time() );
	
	$price = adminSinglePrice( $id );
	
	if( !empty( $price['main_page_url'] ) )
	{
		$url = '<a href="' . $price['main_page_url'] . '" target="_blank" rel="noopener noreferrer">' . $price['st'] . '</a>';
	}
	
	else
	{
		$url = $price['st'];
	}
	
	$retPrice 	= $price['sale_price'] . ' (' . $price['cs'] . ')';
	$added 		= postDate( $price['time_added'] );
	$updated 	= ( !empty( $price['lu'] ) ? postDate( $price['lu'] ) : '-' );
	
	return array( 'success' => __( 'data-updated' ), 'url' => $url, 'price' => $retPrice, 'date' => $added, 'updated' => $updated );
}

#####################################################
#
# Import External Image function
#
#####################################################
function ajaxImportExternalImage()
{
	global $Admin;
	
	//Make sure we allow uploads
	if ( !IsAllowedTo( 'manage-attachments' ) )
	{
		return array(
			'error' => __( 'sorry-you-are-not-allowed-to-upload-files' )
		);
	}
	
	$postId 	= ( isset( $_POST['post'] ) ? $_POST['post'] : null );
	$idFolder 	= ( isset( $_POST['parent'] ) ? $_POST['parent'] : 0 );
	$url 		= ( isset( $_POST['url'] ) ? $_POST['url'] : null );
	$url 		= CleanUri( $url ); //Remove any "?"
	
	if ( empty( $postId ) || !is_numeric( $postId ) || empty( $url ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	$val = Validate( $url, 'url' );
	
	if ( !$val )
	{
		return array( 'error' => __( 'error-please-enter-valid-url' ) );
	}
	
	$db = db();
	
	$postData = $db->from( 
	null, 
	"SELECT id_lang, id_blog, id_site
	FROM `" . DB_PREFIX . POSTS . "`
	WHERE (id_post = " . $postId . ")"
	)->single();
	
	//We need the post's data, we can't continue without it
	if ( empty( $postData ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	$name = pathinfo( $url );
	
	if ( empty( $name ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	$site = $postData['id_site'];
	$lang = $postData['id_lang'];
	
	$allowed = AllowedExt( $site );
	
	//Make sure we allow the extension
	if ( empty( $allowed ) || !in_array( $name['extension'], $allowed ) )
	{
		return array( 'error' => __( 'error-this-extension-is-not-allowed' ) );
	}
	
	$time = time();
	
	$local = $Admin->ImageUpladDir( SITE_ID );

	$root = ( !empty( $local ) ? $local['root'] : null );
	
	$folder = FolderRootByDate( $time, $root );
	
	$fileName = CopyImage( $url, $folder );

	$imgRoot = $folder . $fileName;
	
	if ( !$fileName || !file_exists( $imgRoot ) )
	{
		return array( 'error' => __( 'image-import-error' ) );
	}

	//Set the image's url
	$imgUrl = ( !empty( $local ) ? FolderUrlByDate( $time, $local['html'] ) : FolderUrlByDate( $time ) ) . $fileName;
	
	$imageID = addDbImage( $fileName, $folder, $site, $Admin->UserID(), 'post', 'full', $idFolder, $time, null, $lang, $postId );

	if ( $imageID )
	{
		$share = $Admin->ImageUpladDir( $site );
			
		//If we have child site(s), ask them to copy the image
		if ( !empty( $share ) && isset( $share['share'] ) && $share['share'] )
		{
			$Admin->PingChildSite( 'sync', 'image', null, $site, $imgUrl, $time );
		}
			
		//Create the smaller images
		CreateChildImgs( $imgRoot, $imageID, $time, $folder, $site, 0, 0, 'post' );
	}

	else
		return array( 'error' => __( 'an-error-happened' ) );

	return array( 'success' => __( 'image-uploaded' ) );
}

#####################################################
#
# Get the manufacturers function
#
#####################################################
function ajaxGetManufacturers()
{
	$arr = array( 'results' => array() );

	$siteId = (int) $_POST['postSite'];
	$key 	= $_POST['query'];
	
	$db 	= db();
	
	$res 	= $db->from( 
	null, 
	"SELECT *
	FROM `" . DB_PREFIX . "manufacturers`
	WHERE (id_site = " . $siteId . ") AND (title LIKE :query)",
	array( '%' . $key . '%' => ':query' )
	)->all();

	if ( $res )
	{
		foreach( $res as $p )
		{
			$arr['results'][] = array(
						'disabled' => false,
						'id' => $p['id'],
						'text' => htmlspecialchars( stripslashes( $p['title'] ) )
			);
		}
	}

	return $arr;
}

#####################################################
#
# Get the pages function
#
#####################################################
function ajaxGetFilterPages()
{
	$arr 	= array( 'results' => array() );
	$langId = (int) $_POST['postLang'];
	$key 	= $_POST['query'];
	$db 	= db();
	
	$res 	= $db->from( 
	null, 
	"SELECT id_post, title
	FROM `" . DB_PREFIX . POSTS . "`
	WHERE (id_lang = " . $langId . ") AND (post_type = 'page') AND (post_status = 'published') AND (title LIKE :query)",
	array( '%' . $key . '%' => ':query' )
	)->all();
	
	if ( $res )
	{
		foreach( $res as $p )
		{
			$arr['results'][] = array(
						'disabled' => false,
						'id' => $p['id_post'],
						'text' => htmlspecialchars( stripslashes( $p['title'] ) ),
			);
		}
	}

	return $arr;
}

#####################################################
#
# Get the tags function
#
#####################################################
function ajaxGetFilterTags()
{
	$arr = array( 'results' => array() );

	$langId = (int) $_POST['postLang'];
	$key 	= $_POST['query'];
	$db 	= db();
	
	$res 	= $db->from( 
	null, 
	"SELECT id, title
	FROM `" . DB_PREFIX . "tags`
	WHERE (id_lang = " . $langId . ") AND (title LIKE :query)",
	array( '%' . $key . '%' => ':query' )
	)->all();

	if ( $res )
	{
		foreach( $res as $p )
		{
			$arr['results'][] = array(
						'disabled' => false,
						'id' => $p['id'],
						'text' => htmlspecialchars( stripslashes( $p['title'] ) ),
			);
		}
	}

	return $arr;
}

#####################################################
#
# Get the blogs function
#
#####################################################
function ajaxGetFilterBlogs()
{
	$arr = array( 'results' => array() );

	$langId = (int) $_POST['postLang'];
	$key 	= $_POST['query'];
	$db 	= db();
	
	$res 	= $db->from( 
	null, 
	"SELECT id_blog, name
	FROM `" . DB_PREFIX . "blogs`
	WHERE (id_lang = 0 OR id_lang = " . $langId . ") AND (name LIKE :query)",
	array( '%' . $key . '%' => ':query' )
	)->all();
	
	if ( $res )
	{
		foreach( $res as $p )
		{
			$arr['results'][] = array(
					'disabled' => false,
					'id' => $p['id_blog'],
					'text' => StripContent( $p['name'] ),
			);
		}
	}

	return $arr;
}

#####################################################
#
# Clones a page/post function
#
#####################################################
function ajaxClonePost()
{
	$arr = array( 'status' => 'error' );

	$id 	= (int) $_POST['id']; //Post id
	$cId 	= (int) $_POST['cId']; //Category id
	$bId 	= (int) $_POST['bId']; //Blog id
	$lId 	= (int) $_POST['lId']; //Lang id
	$uId 	= (int) $_POST['userId']; //User id
	$sId 	= (int) $_POST['siteId']; //Site id
	$keep 	= ( ( $_POST['keep'] == 'true' ) ? true : false );
	$db 	= db();
	$isPage	= false;
	$html 	= '';

	if ( empty( $id ) || empty( $cId ) )
	{
		return $arr;
	}
	
	$tmp = GetSinglePost( $id, null, false, false, true );
	
	if ( empty( $tmp ) )
	{
		return $arr;
	}
	
	$lang = $db->from( null,
	"SELECT id, id_site
	FROM `" . DB_PREFIX . "languages`
	WHERE (id = " . $lId . ") AND (status = 'active')"
	)->single();
	
	if ( empty( $lang ) )
	{
		return $arr;
	}
	
	if ( $_POST['cId'] == '-1' )
	{
		$categoryId = $subCategoryId = 0;
		$isPage		= true;
	}
	
	else
	{
		$cat = $db->from( null, "
		SELECT id, id_parent
		FROM `" . DB_PREFIX . "categories`
		WHERE (id = " . $cId . ")"
		)->single();
		
		$categoryId 	= ( ( $cat && !empty( $cat['id_parent'] ) ) ? $cat['id_parent'] : ( $cat ? $cat['id'] : 0 ) );
		$subCategoryId 	= ( ( $cat && !empty( $cat['id_parent'] ) ) ? $cat['id'] : 0 );
	}
	
	$targetUserId = GetMemberRel( $uId, null, $lang['id_site'] );

	$dbarr = array(
		"id_site" 			=> $lang['id_site'],
		"id_lang" 			=> $lang['id'],
		"id_blog" 			=> $bId,
		"id_member" 		=> $targetUserId,
		"id_category" 		=> $categoryId,
		"id_sub_category" 	=> $subCategoryId,
		"title" 			=> $tmp['title'],
		"post" 				=> $tmp['postRaw'],
		"description" 		=> $tmp['description'],
		"post_status" 		=> 'draft',
		"added_time" 		=> ( $keep ? $tmp['added']['raw'] : time() ),
		"sef" 				=> SetShortSef( POSTS, 'id_post', 'sef', CreateSlug( $tmp['title'], true ), $tmp['id'] ),
		"id_category" 		=> $categoryId,
		"post_type" 		=> ( $isPage ? 'page' : 'post' ),
		"poster_ip" 		=> GetRealIp()
	);

	$pId = $db->insert( POSTS )->set( $dbarr, null, true );
	
	if ( !$pId )
	{
		return $arr;
	}
	
	$dbarr = array(
		"id_post" 	=> $pId,
		"clone_id" 	=> $id,
		"keep_date" => ( $keep ? 1 : 0 ),
		"value1" 	=> '',
		"value2" 	=> '',
		"value3" 	=> '',
		"value4" 	=> ''
	);

	$db->insert( "posts_data" )->set( $dbarr );
	
	//Clone any media this content has
	MoveMediaContent( $pId );
	
	return array( 'status' => 'ok', 'url' => sprintf( __( 'post-cloned-url' ), ADMIN_URI . 'edit-post/id/' . $pId . PS ) );
}

#####################################################
#
# Get the Blogs with categories function
#
#####################################################
function ajaxGetMoveSite()
{
	$arr = array( 'results' => 0, 'html' => '' );
	
	$id 	= (int) $_POST['id'];
	$isPage = ( ( !empty( $_POST['isPage'] ) && ( $_POST['isPage'] == 'true' ) ) ? true : false );
	$db 	= db();
	$html 	= '';
	$i 		= 0;
	
	$cats = Cats( $id, true );
	
	$tmp = $db->from( null,
	"SELECT enable_multilang, enable_multiblog
	FROM `" . DB_PREFIX . "sites`
	WHERE (id = " . $id . ")"
	)->single();

	$multiLang = ( ( $tmp && ( $tmp['enable_multilang'] == 'true' ) ) ? true : false );
	$multiBlog = ( ( $tmp && ( $tmp['enable_multiblog'] == 'true' ) ) ? true : false );
	
	if ( !$isPage )
	{
		$i++;
		$html .= '<option value="-1">' . __( 'no-category-post-to-a-page' ) . '</option>';
	}
	
	else
	{
		$i++;
		$html .= '<option value="-1">' . __( 'no-category-page' ) . '</option>';
	}
	
	if ( !empty( $cats ) )
	{
		foreach( $cats as $lang => $data )
		{
			foreach( $data as $key => $cat )
			{
				if ( $key == 'orphanCats' )
				{
					if ( $multiBlog )
					{
						$html .= '<optgroup label="' . __( 'categories' ) . '">';
					}

					foreach( $cat as $k => $c )
					{
						$i++;
				
						$html .= '<option value="' . $c['id'] . '">' . StripContent( $c['name'] ) . '</option>';
					}
					
					if ( $multiBlog )
					{
						$html .= '</optgroup>';
					}
				}
			
				else
				{
					$hasLangData = $hasBlogData = false;
					
					foreach( $cat as $k => $c )
					{
						if ( $multiBlog && !empty( $c['blogData'] ) )
						{
							$hasBlogData = true;
							
							$html .= '<optgroup label="' . $c['blogData']['blogName'] . ( ( $multiLang && !empty(  $c['langData'] ) ) ? ' (' . $c['langData']['langName'] . ')' : '' ) . '">';
							
							break;
						}
					}
					
					foreach( $cat as $k => $c )
					{
						if ( empty( $c['id'] ) )
						{
							continue;
						}
						
						$i++;
						$html .= '<option value="' . $c['id'] . '">' . StripContent( $c['name'] ) . '</option>';
					}
					
					if ( $hasBlogData )
					{
						$html .= '</optgroup>';
					}				
				}
			}
		}
	}

	return array( 'results' => $i, 'isPage' => $isPage, 'html' => $html  );
}

#####################################################
#
# Get the Blogs with categories function
#
#####################################################
function ajaxGetMoveBlogs()
{
	$arr = array( 'results' => 0, 'html' => '' );

	$id 	= (int) $_POST['id'];
	$isPage = ( ( !empty( $_POST['isPage'] ) && ( $_POST['isPage'] == 'true' ) ) ? true : false );
	$db 	= db();
	$html 	= '';
	$i 		= 0;

	$res 	= $db->from( 
	null, 
	"SELECT id, name
	FROM `" . DB_PREFIX . "categories`
	WHERE (id_blog = " . $id . ")
	ORDER BY name ASC"
	)->all();
	
	if ( !$isPage )
	{
		$i++;
		$html .= '<option value="-1">' . __( 'no-category-post-to-a-page' ) . '</option>';
	}
	
	else
	{
		$i++;
		$html .= '<option value="-1">' . __( 'no-category-page' ) . '</option>';
	}

	if ( $res )
	{
		foreach( $res as $p )
		{
			$i++;
			
			$html .= '<option value="' . $p['id'] . '">' . StripContent( $p['name'] ) . '</option>';
		}
	}

	return array( 'results' => $i, 'isPage' => $isPage, 'html' => $html  );
}

#####################################################
#
# Get the categories function
#
#####################################################
function ajaxGetFilterCategories()
{
	$arr = array( 'results' => array() );

	$langId = (int) $_POST['postLang'];
	$blogId = (int) $_POST['postBlog'];
	$key 	= $_POST['query'];
	$db 	= db();
	
	$res 	= $db->from( 
	null, 
	"SELECT id, name
	FROM `" . DB_PREFIX . "categories`
	WHERE (id_lang = " . $langId . ") AND (id_blog = " . $blogId . ") AND (name LIKE :query)",
	array( '%' . $key . '%' => ':query' )
	)->all();

	if ( $res )
	{
		foreach( $res as $p )
		{
			$arr['results'][] = array(
				'disabled' => false,
				'id' => $p['id'],
				'text' => htmlspecialchars( stripslashes( $p['name'] ) ),
			);
		}
	}

	return $arr;
}

#####################################################
#
# Get the stores function
#
#####################################################
function ajaxGetStores()
{
	$arr = array( 'results' => array() );

	$siteId		= (int) $_POST['postSite'];
	$parent 	= ( isset( $_POST['parentOnly'] ) ? $_POST['parentOnly'] : false );
	$storeId 	= ( isset( $_POST['storeId'] ) ? $_POST['storeId'] : null );
	$key 		= $_POST['query'];
	$db 		= db();
	
	$res 	= $db->from( 
	null, 
	"SELECT id_store, name
	FROM `" . DB_PREFIX . "stores`
	WHERE (id_site = " . $siteId . ") AND (name LIKE :query)" . ( $parent ? " AND (id_parent = 0)" : "" ) . ( $storeId ? " AND (id_store != " . $storeId . ")" : "" ),
	array( '%' . $key . '%' => ':query' )
	)->all();

	if ( $res )
	{
		foreach( $res as $p )
		{
			$arr['results'][] = array(
				'disabled' => false,
				'id' => $p['id_store'],
				'text' => htmlspecialchars( stripslashes( $p['name'] ) )
			);
		}
	}

	return $arr;
}

#####################################################
#
# Upload a new image within a post function
#
#####################################################
function ajaxPostImageUpload()
{
	global $Admin;
	
	//Make sure we allow uploads
	if ( !IsAllowedTo( 'manage-attachments' ) )
	{
		return array(
			'error' => __( 'sorry-you-are-not-allowed-to-upload-files' )
		);
	}
	
	$postID 	= ( ( isset( $_POST['post'] ) && is_numeric( $_POST['post'] ) ) ? (int) $_POST['post'] : null );
	$calledFrom = ( isset( $_POST['calledFrom'] ) ? $_POST['calledFrom'] : null );
	$site 		= ( ( isset( $_POST['site'] ) && is_numeric( $_POST['site'] ) ) ? (int) $_POST['site'] : null );
	$lang 		= ( ( isset( $_POST['lang'] ) && is_numeric( $_POST['lang'] ) ) ? (int) $_POST['lang'] : null );
	$imageID 	= null;
	$imageFound = false;
	$db 		= db();
		
	$imageType = ( !$calledFrom ? 'post' : ( ( $calledFrom == 'site-image' ) ? 'site' : ( ( $calledFrom == 'maintenance' ) ? 'maintenance' : 'post' ) ) );
	
	//We can't continue if this function called from an unknown place
	if ( !$postID && ( $calledFrom != 'site-image' ) && ( $calledFrom != 'maintenance' ) && ( $calledFrom != 'category' ) && ( $calledFrom != 'manufacturer' ) && ( $calledFrom != 'vendor' ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	if ( $postID )
	{
		$postData = $db->from( 
		null, 
		"SELECT id_lang, id_blog, id_site
		FROM `" . DB_PREFIX . POSTS . "`
		WHERE (id_post = " . $postID . ")"
		)->single();
		
		//We need the post's data, we can't continue without it
		if ( empty( $postData ) )
		{
			return array( 'error' => __( 'an-error-happened' ) );
		}
		
		$site = $postData['id_site'];
		$lang = $postData['id_lang'];
	}
	else
		$postID = 0;

	$idFolder = ( ( isset( $_POST['parent'] ) && is_numeric( $_POST['parent'] ) ) ? $_POST['parent'] : ( ( isset( $_POST['folderID'] ) && is_numeric( $_POST['folderID'] ) ) ? $_POST['folderID'] : 0 ) );

	$tmp_name = $_FILES['file']['tmp_name']['0'];
	
	$name = pathinfo( $_FILES['file']['name']['0'] );
	
	$allowed = AllowedExt( $site );

	//Make sure we allow the extension
	if ( empty( $allowed ) || !in_array( $name['extension'], $allowed ) )
	{
		return array( 'error' => __( 'error-this-extension-is-not-allowed' ) );
	}

	$sefName = URLify( $name['filename'] );

	$fileName = $sefName . '.' . $name['extension'];
	
	$arr = array();
	
	$time = time();
	
	//Check if we have this image already
	$imgData = $db->from( 
	null, 
	"SELECT id_image, added_time
	FROM `" . DB_PREFIX . "images`
	WHERE (filename = :name)",
	array( $fileName => ':name' )
	)->single();
	
	//Set the time from the image we already have
	if ( $imgData )
	{
		$time 	 	= $imgData['added_time'];
		$imageID 	= $imgData['id_image'];
		$imageFound = true;
	}

	$local = $Admin->ImageUpladDir( SITE_ID );
		
	$root = ( !empty( $local ) ? $local['root'] : null );
	
	$folder = FolderRootByDate( $time, $root );
	
	//Set the image's url
	$imgUrl = ( !empty( $local ) ? FolderUrlByDate( $time, $local['html'] ) : FolderUrlByDate( $time ) ) . $fileName;
	
	$imgRoot = $folder . $fileName;
	
	if( !$imgData && move_uploaded_file( $tmp_name, $imgRoot ) )
	{
		$imageID = addDbImage( $fileName, $folder, $site, $Admin->UserID(), $imageType, 'full', $idFolder, $time, null, $lang, $postID );
	}

	if ( !empty( $imageID ) )
	{
		$share = $Admin->ImageUpladDir( $site );

		//If we have child site(s), ask them to copy the image
		if ( !empty( $share ) && isset( $share['share'] ) && $share['share'] )
		{
			$Admin->PingChildSite( 'sync', 'image', null, $site, $imgUrl, $time );
		}
			
		//Create the smaller images
		CreateChildImgs( $imgRoot, $imageID, $time, $folder, $site, 0, 0, $imageType );
	}

	else
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	return array( 'success' => ( $imageFound ? __( 'file-exists' ) : __( 'image-uploaded' ) ) );
}

#####################################################
#
# Upload new import file function
#
#####################################################
function ajaxUploadImportFile()
{
	global $Admin;
	
	//Make sure we allow uploads
	if ( !IsAllowedTo( 'manage-attachments' ) )
	{
		return array(
			'status' => 1,
			'message' => __( 'sorry-you-are-not-allowed-to-upload-files' ),
			'importId' => 0
		);
	}

	$tmp_name = $_FILES['file']['tmp_name'];
	$tmp_size = $_FILES['file']['size'];
	$uploadMaxFilesize = ParseFiseSize( ini_get('upload_max_filesize') );

	if ( ( $uploadMaxFilesize > 0 ) && ( $tmp_size > 0 ) && ( $tmp_size > $uploadMaxFilesize ) )
	{
		return array(
				'status' => 1,
				'message' => __( 'file-size-error' ),
				'importId' => 0
		);
	}
	
	$name = pathinfo( $_FILES['file']['name'] );
	
	//For now, allow only these two files
	if ( ( $name['extension'] != 'xml' ) && ( $name['extension'] != 'zip' ) )
	{
		return array(
				'status' => 1,
				'message' => __( 'file-error' ),
				'importId' => 0
		);
	}
	
	$db 		= db();
	$sefName 	= URLify( $name['filename'] );
	$fileName 	= $sefName . '.' . $name['extension'];
	$importId 	= 0;
	$arr 		= array();
	$time 		= time();
	$site 		= ( ( isset( $_POST['site'] ) && is_numeric( $_POST['site'] ) ) ? $_POST['site'] : $Admin->GetSite() );
	$lang 		= ( ( isset( $_POST['lang'] ) && is_numeric( $_POST['lang'] ) ) ? $_POST['lang'] : $Admin->GetLang() );

	//Check if we already have this import
	$data = $db->from( 
	null, 
	"SELECT id
	FROM `" . DB_PREFIX . "imports`
	WHERE (id_site = " . $site . ") AND (filename = :name)",
	array( $fileName => ':name' )
	)->single();
	
	if ( $data )
	{
		$fileRoot = UPLOADS_ROOT . $fileName;
		
		//Check if the file is still there
		if ( file_exists( $fileRoot ) )
		{
			return array(
				'status' => 0,
				'message' => __( 'file-exists' ),
				'filename' => $fileName,
				'importId' => $data['id']
			);
		}
		
		//We've removed the file, so delete this DB key, we will add a new one later...
		else
		{
			$db->delete( 'imports' )->where( "id", $data['id'] )->run();
		}
	}
	
	$fileRoot = UPLOADS_ROOT . $fileName;

	if( move_uploaded_file( $tmp_name, $fileRoot ) )
	{
		$dbarr = array(
			"id_site" 		=> $site,
			"id_lang" 		=> $lang,
			"added_time" 	=> time(),
			"filename" 		=> $fileName,
			"file_id" 		=> md5( $fileName )
        );
            
		$put = $db->insert( 'imports' )->set( $dbarr );
			
		if ( $put )
		{
			$uploadId = $db->lastId();
			
			$arr = array(
				'status' => 0,
				'message' => __( 'file-uploaded' ),
				'filename' => $fileName,
				'importId' => $uploadId
			);
		}
		else
		{
			$arr = array(
				'status' => 1,
				'message' => __( 'file-upload-error' ),
				'importId' => 0
			);
		}
	
	}
	
	else
	{
		$arr = array(
				'status' => 1,
				'message' => __( 'file-upload-error' ),
				'importId' => 0
		);
	}
	
	return $arr;
}

#####################################################
#
# Insert Single Image to Editor function 
#
#####################################################
function ajaxInsertMediaToEditor()
{
	if ( !isset( $_POST['id'] ) && !isset( $_POST['fileId'] ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	global $Admin;
	
	$id = ( ( isset( $_POST['id'] ) && is_numeric( $_POST['id'] ) ) ? (int) $_POST['id'] : ( ( isset( $_POST['fileId'] ) && is_numeric( $_POST['fileId'] ) ) ? (int) $_POST['fileId'] : 0 ) );
	
	$db = db();
	
	$imgData = $db->from( 
	null, 
	"SELECT filename, width, height, added_time, id_parent, id_site, mime_type, trans_data
	FROM `" . DB_PREFIX . "images`
	WHERE (id_image = " . $id . ")"
	)->single();
	
	if ( empty( $imgData ) )
	{
		return array( 'error' => sprintf( __( 'image-could-not-be-found-error' ), $id ) );
	}
	
	$dateAdded = $imgData['added_time'];
	
	if ( $imgData['id_parent'] )
	{
		$imgParent = $db->from( 
		null, 
		"SELECT added_time
		FROM `" . DB_PREFIX . "images`
		WHERE (id_image = " . $imgData['id_parent'] . ")"
		)->single();
		
		if ( $imgParent )
			$dateAdded = $imgParent['added_time'];
		else
			return array( 'error' => __( 'an-error-happened' ) );
		
	}
	
	//Get the code of this language
	$_lang = $db->from( 
	null, 
	"SELECT code
	FROM `" . DB_PREFIX . "languages`
	WHERE (id = " . (int) $_POST['lang'] . ")"
	)->single();

	$langCode = ( $_lang ? $_lang['code'] : $Admin->LangCode() );

	//Get this image's url
	$folders = $Admin->ImageUpladDir( $imgData['id_site'] );

	$imgUrl = ( !empty( $folders ) ? $folders['html'] : null );
	
	$transData = Json( $imgData['trans_data'] );
	
	$transData = ( ( !empty( $transData ) && isset( $transData[$langCode] ) ) ? $transData[$langCode] : null );
	
	$arr = array( 'data' => array(
				'id' => $id,
				'url' => FolderUrlByDate( $dateAdded, $imgUrl ) . $imgData['filename'],
				'title' => ( $transData ? htmlentities( $transData['title'] ) : '' ),
				'alt' => ( $transData ? htmlentities( $transData['alt'] ) : '' ),
				'descr' => ( $transData ? htmlentities( $transData['descr'] ) : '' ),
				'caption' => ( $transData ? htmlentities( $transData['caption'] ) : '' ),
				'mime' => stripslashes( $imgData['mime_type'] ),
				'align' => ( !isset( $_POST['aling'] ) || empty( $_POST['aling'] ) ? 'none' : $_POST['aling'] ),
				'width' => $imgData['width'],
				'height' => $imgData['height']
		)
	);
	
	return $arr;
}

#####################################################
#
# Get Single Image Details for gallery modal function 
#
#####################################################
function ajaxSingleGalleryImageDetails()
{
	if ( !isset( $_POST['id'] ) || empty( $_POST['id'] ) || empty( $_POST['lang'] ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	$db = db();

	$imgData = $db->from( null, "
	SELECT im.id_image, im.filename, im.width, im.height, im.added_time, im.id_site, im.mime_type, im.file_ext, im.extra_data, im.trans_data, la.code
	FROM `" . DB_PREFIX . "images` as im
	LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = im.id_lang
	WHERE (im.id_image = " . (int) $_POST['id'] . ")"
	)->single();

	//We need the img data, so we can't continue without it
	if ( empty( $imgData ) )
	{
		return array( 'error' => sprintf( __( 'image-could-not-be-found-error' ), $_POST['id'] ) );
	}
	
	global $Admin;
	
	//Get the code of this language
	$_lang = $db->from( 
	null, 
	"SELECT code
	FROM `" . DB_PREFIX . "languages`
	WHERE (id = " . (int) $_POST['lang'] . ")"
	)->single();

	$langCode = ( $_lang ? $_lang['code'] : ( !empty( $imgData['code'] ) ? $imgData['code'] : $Admin->LangCode() ) );

	$transData = Json( $imgData['trans_data'] );

	$transData = ( ( !empty( $transData ) && isset( $transData[$langCode] ) ) ? $transData[$langCode] : null );

	$script = "<script type=\"text/javascript\"><!--
	var typingTimer;
	var doneTypingInterval = 500;
	var id = '" . $_POST['id'] . "';
	var lang = '" . $_POST['lang'] . "';
	var token = '';
	$('#modal-loader-2').hide();

	$('#imgGalleryTitle').on('input', function(e) {
		var what = 'title';
		var val = this.value;
		clearTimeout(typingTimer);
		typingTimer = setTimeout(doneTyping, doneTypingInterval, val, what);
	});
		
	$('#imgGalleryTitle').on('keydown', function(e) {
		clearTimeout(typingTimer);
	});
		
	$('#imgGalleryAlt').on('input', function(e) {
		var what = 'alt';
		var val = this.value;
		clearTimeout(typingTimer);
		typingTimer = setTimeout(doneTyping, doneTypingInterval, val, what);
	});
		
	$('#imgGalleryAlt').on('keydown', function(e) {
		clearTimeout(typingTimer);
	});
		
	$('#imgGalleryCaption').on('input', function(e) {
		var what = 'caption';
		var val = this.value;
		clearTimeout(typingTimer);
		typingTimer = setTimeout(doneTyping, doneTypingInterval, val, what);
	});
		
	$('#imgGalleryCaption').on('keydown', function(e) {
		clearTimeout(typingTimer);
	});
	
	$('#imgGalleryDescription').on('input', function(e) {
		var what = 'descr';
		var val = this.value;
		clearTimeout(typingTimer);
		typingTimer = setTimeout(doneTyping, doneTypingInterval, val, what);
	});
		
	$('#imgGalleryDescription').on('keydown', function(e) {
		clearTimeout(typingTimer);
	});
	
	$('#videoThumbnailUrl').on('input', function(e) {
		var what = 'videoThumpUrl';
		var val = this.value;
		clearTimeout(typingTimer);
		typingTimer = setTimeout(doneTyping, doneTypingInterval, val, what);
	});
		
	$('#videoThumbnailUrl').on('keydown', function(e) {
		clearTimeout(typingTimer);
	});
	
	$('#playLargeVideo').click(function() {  
        var val = $(this).prop('checked');
		var what = 'playLargeVideo';
		
		doneTyping(val, what);
	});
	
	$('#playVideo').click(function() {  
        var val = $(this).prop('checked');
		var what = 'playVideo';
		
		doneTyping(val, what);
	});
	
	$('#videoProgress').click(function() {  
        var val = $(this).prop('checked');
		var what = 'videoProgress';
		
		doneTyping(val, what);
	});
	
	$('#currentTime').click(function() {  
        var val = $(this).prop('checked');
		var what = 'currentTime';
		
		doneTyping(val, what);
	});
	
	$('#mute').click(function() {  
        var val = $(this).prop('checked');
		var what = 'mute';
		
		doneTyping(val, what);
	});
	
	$('#familyFriendly').click(function() {  
        var val = $(this).prop('checked');
		var what = 'familyFriendly';
		
		doneTyping(val, what);
	});
	
	$('#allowEmbed').click(function() {  
        var val = $(this).prop('checked');
		var what = 'allowEmbed';
		
		doneTyping(val, what);
	});
	
	$('#volume').click(function() {  
        var val = $(this).prop('checked');
		var what = 'volume';
		
		doneTyping(val, what);
	});
	
	$('#fileSettings').click(function() {  
        var val = $(this).prop('checked');
		var what = 'fileSettings';
		
		doneTyping(val, what);
	});
	
	$('#fullscreen').click(function() {  
        var val = $(this).prop('checked');
		var what = 'fullscreen';
		
		doneTyping(val, what);
	});
	
	$('#autoplay').click(function() {  
        var val = $(this).prop('checked');
		var what = 'autoplay';
		
		doneTyping(val, what);
	});
	
	$('#speed').click(function() {  
        var val = $(this).prop('checked');
		var what = 'speed';
		
		doneTyping(val, what);
	});
	
	$('#loop').click(function() {  
        var val = $(this).prop('checked');
		var what = 'loop';
		
		doneTyping(val, what);
	});
		
	function doneTyping (val, dt) {
		var input = val;
		var what = dt;
		$('#modal-loader-2').show();
		$.ajax(
		{
			url: '" . AJAX_ADMIN_PATH . "edit-image-details/',
			type: 'POST',
			data: {id:id,token:token,input:input,what:what,lang:lang},
			dataType: 'html',
			cache: false
		})
		.done(function(data)
		{
			$('#modal-loader-2').hide();
		 })
		 .fail(function(){
			$('#post-detail-2').html('Error. Please try again...');
			$('#modal-loader-2').hide();
		});
	};
	//--></script>";
	
	$selectAling = '';
	
	if ( $imgData['mime_type'] == 'image' ) 
	{
		$selectAling = '<div class="form-group">
					<label for="selectAling">' . __( 'alignment' ) . '</label>
					<select class="form-select form-select-sm" name="align" id="imgAlign">
						<option value="none" selected>' . __( 'none' ) . '</option>
						<option value="center">' . __( 'center' ) . '</option>
						<option value="left">' . __( 'left' ) . '</option>
						<option value="right">' . __( 'right' ) . '</option>
					</select>
				</div>';
	}

	//Get this image's url
	$folders = $Admin->ImageUpladDir( SITE_ID );
	
	$share = $Admin->ImageUpladDir( $imgData['id_site'] );
	
	$imgUrl = ( !empty( $folders ) ? $folders['html'] : null );
	
	$imageOrUrl = ( !empty( $share ) ? $share['html'] : $imgUrl );
	
	$xtraData = Json( $imgData['extra_data'] );
	
	$arr = array( 'data' => array(
						'url' => FolderUrlByDate( $imgData['added_time'], $imageOrUrl ) . $imgData['filename'],
						'title' => ( $transData ? htmlentities( $transData['title'] ) : '' ),
						'alt' => ( $transData ? htmlentities( $transData['alt'] ) : '' ),
						'descr' => ( $transData ? htmlentities( $transData['descr'] ) : '' ),
						'caption' => ( $transData ? htmlentities( $transData['caption'] ) : '' ),
						'mime' => stripslashes( $imgData['mime_type'] ),
						'ext' => stripslashes( $imgData['file_ext'] ),
						'id' => $imgData['id_image'],
						'width' => $imgData['width'],
						'height' => $imgData['height'],
						'script' => $script,
						'selectAling' => $selectAling,
						'videoThumbnailUrl' => ( ( !empty( $xtraData ) && isset( $xtraData['videoThumbnailUrl'] ) ) ? $xtraData['videoThumbnailUrl'] : HTML_ADMIN_PATH_THEME . PS .'assets' . PS . 'img' . PS . 'video-filetype.png' ),
						'playLargeVideo' => ( ( !empty( $xtraData ) && isset( $xtraData['playLargeVideo'] ) ) ? $xtraData['playLargeVideo'] : false ),
						'familyFriendly' => ( ( !empty( $xtraData ) && isset( $xtraData['familyFriendly'] ) ) ? $xtraData['familyFriendly'] : false ),
						'allowEmbed' => ( ( !empty( $xtraData ) && isset( $xtraData['allowEmbed'] ) ) ? $xtraData['allowEmbed'] : false ),
						'playVideo' => ( ( !empty( $xtraData ) && isset( $xtraData['playVideo'] ) ) ? $xtraData['playVideo'] : false ),
						'videoProgress' => ( ( !empty( $xtraData ) && isset( $xtraData['videoProgress'] ) ) ? $xtraData['videoProgress'] : false ),
						'currentTime' => ( ( !empty( $xtraData ) && isset( $xtraData['currentTime'] ) ) ? $xtraData['currentTime'] : false ),
						'mute' => ( ( !empty( $xtraData ) && isset( $xtraData['mute'] ) ) ? $xtraData['mute'] : false ),
						'settings' => ( ( !empty( $xtraData ) && isset( $xtraData['settings'] ) ) ? $xtraData['settings'] : false ),
						'volume' => ( ( !empty( $xtraData ) && isset( $xtraData['volume'] ) ) ? $xtraData['volume'] : false ),
						'fullscreen' => ( ( !empty( $xtraData ) && isset( $xtraData['fullscreen'] ) ) ? $xtraData['fullscreen'] : false ),
						'speed' => ( ( !empty( $xtraData ) && isset( $xtraData['speed'] ) ) ? $xtraData['speed'] : false ),
						'loop' => ( ( !empty( $xtraData ) && isset( $xtraData['loop'] ) ) ? $xtraData['loop'] : false ),
						'autoplay' => ( ( !empty( $xtraData ) && isset( $xtraData['autoplay'] ) ) ? $xtraData['autoplay'] : false ),
						'added' => postDate ( $imgData['added_time'] ),
						'childs' => array()
						)
	
	);
	
	$selectSize = '<div class="form-group">
					<label for="selectSize">' . __( 'size' ) . '</label>
					<select class="form-select form-select-sm" name="size" id="imgSize">
						<option value="' . $_POST['id'] . '" selected>' . __( 'original' ) . '</option>';
	
	$imgChilds = $db->from( 
	null, 
	"SELECT filename, width, height, id_image
	FROM `" . DB_PREFIX . "images`
	WHERE (id_parent = " . (int) $_POST['id'] . ")"
	)->all();
	
	if ( $imgChilds )
	{
		foreach( $imgChilds as $imgChild )
		{
			$arr['data']['childs'][] = array(
									'url' => FolderUrlByDate( $imgData['added_time'], $imgUrl ) . $imgChild['filename'],
									'width' => $imgChild['width'],
									'height' => $imgChild['height']
			
			);
			
			$selectSize .= '<option value="' . $imgChild['id_image'] . '">' . $imgChild['width'] . ' x ' . $imgChild['height'] . '</option>';
		}
		
		$arr['data']['thumb'] = $arr['data']['childs']['0']['url'];
	}
	else
	{
		$arr['data']['thumb'] = FolderUrlByDate( $imgData['added_time'], $imgUrl ) . $imgData['filename'];
	}
	
	$selectSize .= '</select>
				</div>';
				
				
	$arr['data']['selectSize'] = $selectSize;

	return $arr;
}

#####################################################
#
# Edit Single Gallery Image function 
#
#####################################################
function ajaxEditSingleImageDetails()
{
	if ( empty( $_POST ) || !isset( $_POST['id'] ) || !isset( $_POST['what'] ) || !isset( $_POST['input'] ) )
		echo __( 'an-error-happened' );
	
	global $Admin;
	
	if ( !IsAllowedTo( 'manage-attachments' ) )
	{
		return __( 'sorry-you-are-not-allowed-to-edit-this-item' );
	}
	
	$db = db();

	$imgData = $db->from( null, "
	SELECT im.extra_data, im.trans_data, la.code
	FROM `" . DB_PREFIX . "images` as im
	LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = im.id_lang
	WHERE (im.id_image = " . (int) $_POST['id'] . ")"
	)->single();
	
	$xtraData = ( ( $imgData && !empty( $imgData['extra_data'] ) ) ? Json( $imgData['extra_data'] ) : array() );
	
	$what = ( ( $_POST['what'] == 'title' ) ? 'title' : ( ( $_POST['what'] == 'alt' ) ? 'alt' : ( ( $_POST['what'] == 'caption' ) ? 'caption' : 'descr' ) ) );
	
	//$langCode = ( !empty( $imgData['code'] ) ? $imgData['code'] : $Admin->LangCode() );
	
	//Get the code of this language
	$_lang = $db->from( 
	null, 
	"SELECT code
	FROM `" . DB_PREFIX . "languages`
	WHERE (id = " . (int) $_POST['lang'] . ")"
	)->single();

	$langCode = ( $_lang ? $_lang['code'] : ( !empty( $imgData['code'] ) ? $imgData['code'] : $Admin->LangCode() ) );
	
	$transData = Json( $imgData['trans_data'] );
	
	if ( empty( $transData ) || !isset( $transData[$langCode] ) )
	{
		$transData[$langCode] = array(
			'title' 	=> '',
			'alt' 		=> '',
			'descr' 	=> '',
			'caption' 	=> '',
		);
	}

	$input = $_POST['input'];
	
	if ( $what == 'title' )
	{
		$transData[$langCode]['title'] = $input;
		
		$input = json_encode( $transData, JSON_UNESCAPED_UNICODE );
		
		$what = 'trans_data';
	}
	
	if ( $what == 'caption' )
	{
		$transData[$langCode]['caption'] = $input;
		
		$input = json_encode( $transData, JSON_UNESCAPED_UNICODE );
		
		$what = 'trans_data';
	}
	
	if ( $what == 'descr' )
	{
		$transData[$langCode]['descr'] = $input;
		
		$input = json_encode( $transData, JSON_UNESCAPED_UNICODE );
		
		$what = 'trans_data';
	}
	
	if ( $what == 'alt' )
	{
		$transData[$langCode]['alt'] = $input;
		
		$input = json_encode( $transData, JSON_UNESCAPED_UNICODE );
		
		$what = 'trans_data';
	}
	
	if ( $_POST['what'] == 'videoThumpUrl' )
	{
		$what = 'extra_data';
		
		$xtraData['videoThumbnailUrl'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'playLargeVideo' )
	{
		$what = 'extra_data';
		
		$xtraData['playLargeVideo'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'playVideo' )
	{
		$what = 'extra_data';
		
		$xtraData['playVideo'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'familyFriendly' )
	{
		$what = 'extra_data';
		
		$xtraData['familyFriendly'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'allowEmbed' )
	{
		$what = 'extra_data';
		
		$xtraData['allowEmbed'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'videoProgress' )
	{
		$what = 'extra_data';
		
		$xtraData['videoProgress'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'currentTime' )
	{
		$what = 'extra_data';
		
		$xtraData['currentTime'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'mute' )
	{
		$what = 'extra_data';
		
		$xtraData['mute'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'volume' )
	{
		$what = 'extra_data';
		
		$xtraData['volume'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'fileSettings' )
	{
		$what = 'extra_data';
		
		$xtraData['fileSettings'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'fullscreen' )
	{
		$what = 'extra_data';
		
		$xtraData['fullscreen'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'autoplay' )
	{
		$what = 'extra_data';
		
		$xtraData['autoplay'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'speed' )
	{
		$what = 'extra_data';
		
		$xtraData['speed'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	if ( $_POST['what'] == 'loop' )
	{
		$what = 'extra_data';
		
		$xtraData['loop'] = $input;
		
		$input = json_encode( $xtraData, JSON_UNESCAPED_UNICODE );
	}
	
	$db->update( "images" )->where( 'id_image', (int) $_POST['id'] )->set( $what, $input );
}

#####################################################
#
# Single Gallery Image function 
#
#####################################################
function ajaxSingleGalleryImage()
{
	if ( empty( $_POST ) || !isset( $_POST['id'] ) )
		return __( 'an-error-happened' );
	
	if ( !IsAllowedTo( 'manage-attachments' ) )
	{
		return __( 'sorry-you-are-not-allowed-to-edit-this-item' );
	}
	
	$data = '';
	
	$db = db();

	$imgData = $db->from( null, "
	SELECT im.trans_data, la.code
	FROM `" . DB_PREFIX . "images` as im
	LEFT JOIN `" . DB_PREFIX . "languages` AS la ON la.id = im.id_lang
	WHERE (im.id_image = " . (int) $_POST['id'] . ")"
	)->single();
	
	//We need the post data, so we can't continue without it
	if ( empty( $imgData ) )
	{
		return __( 'an-error-happened' );
	}
	
	global $Admin;
	
	$langCode = ( !empty( $imgData['code'] ) ? $imgData['code'] : $Admin->LangCode() );
	
	$transData = Json( $imgData['trans_data'] );
	
	$transData = ( ( !empty( $transData ) && isset( $transData[$langCode] ) ) ? $transData[$langCode] : null );

	$data .= '<div class="form-group">
		<label class="form-label" for="imgTitle">' . __( 'title' ) . '</label>
		<input type="text" name="imgTitle" id="imgTitle" class="form-control mb-4" placeholder="' . __( 'enter-title' ) . '" value="' . ( $transData ? htmlspecialchars( html_entity_decode( $transData['title'] ) ) : '' ) . '">
	</div>';
	
	$data .= '<div class="form-group">
		<label class="form-label" for="imgAlt">' . __( 'alt-text' ) . '</label>
		<input type="text" name="imgAlt" id="imgAlt" class="form-control mb-4" placeholder="' . __( 'alt-text' ) . '" value="' . ( $transData ? htmlspecialchars( html_entity_decode( $transData['alt'] ) ) : '' ) . '">
		<small id="imgAltHelp" class="form-text text-muted">' . __( 'alt-text-tip' ) . '</small>
	</div>';
	
	$data .= '<div class="form-group">
		<label class="form-label" for="imgDescr">' . __( 'image-descr' ) . '</label>
		<input type="text" name="imgDescr" id="imgDescr" class="form-control mb-4" placeholder="' . __( 'image-descr' ) . '" value="' . ( $transData ? htmlspecialchars( html_entity_decode( $transData['descr'] ) ) : '' ) . '">
		<small id="imgDescrHelp" class="form-text text-muted">' . __( 'image-descr-tip' ) . '</small>
	</div>' . PHP_EOL;
	
	$data .= "<script type=\"text/javascript\"><!--
		var typingTimer;
		var doneTypingInterval = 500;
		var id = '" . $_POST['id'] . "';
		var lang = '" . $Admin->GetLang() . "';
		var token = '" . $Admin->GetToken() . "';
		
		console.log(lang);
		
		$('#imgTitle').on('input', function(e) {
			var what = 'title';
			var value = this.value;
			clearTimeout(typingTimer);
			typingTimer = setTimeout(doneTyping, doneTypingInterval, value, what);
		});
		
		$('#imgTitle').on('keydown', function(e) {
			clearTimeout(typingTimer);
		});
		
		$('#imgAlt').on('input', function(e) {
			var what = 'alt';
			var value = this.value;
			clearTimeout(typingTimer);
			typingTimer = setTimeout(doneTyping, doneTypingInterval, value, what);
		});
		
		$('#imgAlt').on('keydown', function(e) {
			clearTimeout(typingTimer);
		});
		
		$('#imgDescr').on('input', function(e) {
			var what = 'descr';
			var value = this.value;
			clearTimeout(typingTimer);
			typingTimer = setTimeout(doneTyping, doneTypingInterval, value, what);
		});
		
		$('#imgDescr').on('keydown', function(e) {
			clearTimeout(typingTimer);
		});

		function doneTyping (val, dt) {
			var input = val;
			var what = dt;
			$('#modal-loader').show();
			$.ajax(
			{
				url: '" . AJAX_ADMIN_PATH . "edit-image-details/',
				type: 'POST',
				data: {id:id,token:token,input:input,what:what},
				dataType: 'html',
				cache: false
			})
			.done(function(data)
			{
				$('#modal-loader').hide();
			 })
			 .fail(function(){
				$('#post-detail').html('Error. Please try again...');
				$('#modal-loader').hide();
			});
		};
	//--></script>";

	return $data;
}

#####################################################
#
# Delete images/folders function 
#
#####################################################
function ajaxDeleteMediaData()
{
	if ( !isset( $_POST['data'] ) || empty( $_POST['data'] ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	if ( !IsAllowedTo( 'manage-attachments' ) )
	{
		return array( 'error' => __( 'sorry-you-are-not-allowed-to-do-this' ) );
	}
	
	global $Admin;
	
	$db = db();
	
	foreach( $_POST['data'] as $data )
	{
		$ex = _explode( $data, '::' );
		
		if ( empty( $ex ) || !isset( $ex['id'] ) || empty( $ex['id'] ) )
			continue;
		
		//Delete Image(s)
		if ( $ex['target'] == 'image' )
		{
			$imgData = $db->from( 
			null, 
			"SELECT filename, added_time, id_site
			FROM `" . DB_PREFIX . "images`
			WHERE (id_image = " . $ex['id'] . ")"
			)->single();
			
			//We've found the image, let's delete it
			if ( $imgData )
			{
				$local = $Admin->ImageUpladDir( SITE_ID );
	
				$root = ( !empty( $local ) ? $local['root'] : null );
	
				$file = FolderRootByDate( $imgData['added_time'], $root ) . $imgData['filename'];
				
				$q = $db->delete( 'images' )->where( "id_image", $ex['id'] )->run();
				
				//Delete also its childs
				if ( $q )
				{
					@unlink( $file );
					
					//Don't forget the child site
					if ( $imgData['id_site'] != SITE_ID )
					{
						$Admin->PingChildSite( 'delete-image', null, urlencode( $imgData['filename'] ), $imgData['id_site'], null, $imgData['added_time'] );
					}
					
					//Continue and delete the child images
					$imgs = $db->from( 
					null, 
					"SELECT filename, id_image
					FROM `" . DB_PREFIX . "images`
					WHERE (id_parent = " . $ex['id'] . ")"
					)->all();
					
					if ( $imgs )
					{
						foreach( $imgs as $img )
						{
							$file = FolderRootByDate( $imgData['added_time'], $root ) . $img['filename'];
							
							@unlink( $file );
						}
					}
					
					//Is this image on a child site? We have to delete it and from there
					if ( $imgData['id_site'] != SITE_ID )
						$Admin->PingChildSite( 'delete-childs', null, $ex['id'], $imgData['id_site'], null, $imgData['added_time'] );

					else
					{
						$db->delete( 'images' )->where( "id_parent", $ex['id'] )->run();
					}
					
					//Delete any post cover attached to this image
					DelCoverImage( null, $ex['id'] );
				}
			}
		}
		
		//Delete folder(s)
		if ( $ex['target'] == 'folder' )
		{
			$q = $db->delete( 'image_folders' )->where( "id", $ex['id'] )->run();
			
			if ( $q )
			{
				$db->update( 'images' )->where( 'id_folder', $ex['id'] )->set( 'id_folder', 0 );
			}
		}
	}

	return array( 'success' => __( 'file-folder-deleted-successfully' ) );
}

#####################################################
#
# Add a new folder function 
#
#####################################################
function ajaxCreateNewFolder()
{	
	if ( !isset( $_POST['folder'] ) || empty( $_POST['folder'] ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	if ( !IsAllowedTo( 'manage-attachments' ) )
	{
		return array( 'error' => __( 'sorry-you-are-not-allowed-to-do-this' ) );
	}
	
	if ( ( strlen( $_POST['folder'] ) < 2 ) || ( strlen( $_POST['folder'] ) > 50 ) )
	{
		return array( 'error' => __( 'folder-name-must-be-between-3-and-50-chars' ) );
	}
	
	$db 		= db();
	
	$isFolder 	= ( ( isset( $_POST['parent'] ) && is_numeric( $_POST['parent'] ) ) ? true : false );
	
	$postData = $db->from( 
	null, 
	"SELECT id_lang, id_blog, id_site
	FROM `" . DB_PREFIX . POSTS . "`
	WHERE (id_post = " . $_POST['post'] . ")"
	)->single();
	
	//We need the post data, so we can't continue without it
	if ( empty( $postData ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	$slug = SetShortSef( 'image_folders', 'id', 'sef', CreateSlug( $_POST['folder'], true ) );
	
	//Create a post and redirect it there
	$dbarr = array(
		"id_site" 	=> $postData['id_site'],
		"id_lang" 	=> $postData['id_lang'],
		"name" 		=> $_POST['folder'],
		"sef" 		=> $slug
	);
	
	if ( $isFolder )
	{
		$dbarr["id_parent"] = $_POST['parent'];
	}

	$put = $db->insert( 'image_folders' )->set( $dbarr );

	if ( $put )
	{
		return array( 'success' => __( 'folder-created-successfully' ) );
	}
	
	else
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
}

#####################################################
#
# Reset Single post's gallery function
#
#####################################################
function ajaxResetGallery()
{
	$postId = (int) $_POST['post'];
	
	if ( empty( $_POST['post'] ) )
	{
		return array( 'error' => __( 'an-error-happened' ) );
	}
	
	if ( !IsAllowedTo( 'manage-attachments' ) )
	{
		return array( 'error' => __( 'sorry-you-are-not-allowed-to-do-this' ) );
	}
	
	$db = db();
	
	$db->update( 'posts_data' )->where( 'id_post', $postId )->set( 'value3', json_encode( array() ) );
	
	return array( 'success' => __( 'ok' ) );
}

#####################################################
#
# Add/Update User's defaul image function
#
#####################################################
function ajaxUserImage()
{
	global $Admin;
	
	if ( !IsAllowedTo( 'manage-attachments' ) )
	{
		return array( 'error' => __( 'sorry-you-are-not-allowed-to-do-this' ) );
	}
	
	$tmp_name = $_FILES['file']['tmp_name'];
	
	$name = pathinfo( $_FILES['file']['name'] );
	
	$allowed = AllowedExt();

	//Make sure we allow the extension
	if ( empty( $allowed ) || !in_array( $name['extension'], $allowed ) )
	{
		return array( 'error' => __( 'error-this-extension-is-not-allowed' ) );
	}
	
	$db 	= db();
	
	$sefName = URLify( $name['filename'] );

	$fileName = $sefName . '.' . $name['extension'];
	
	$imageID = 0;
	
	$arr = array();
	
	$time = time();
	
	$local = $Admin->ImageUpladDir( SITE_ID );
	
	$root = ( !empty( $local ) ? $local['root'] : null );
	
	$folder = FolderRootByDate( $time, $root );
	
	//Set the image's url
	$imgUrl = ( !empty( $local ) ? FolderUrlByDate( $time, $local['html'] ) : FolderUrlByDate( $time ) ) . $fileName;

	$imgRoot = $folder . $fileName;
	
	$site = ( ( isset( $_POST['site'] ) && is_numeric( $_POST['site'] ) ) ? $_POST['site'] : $Admin->GetSite() );
	
	$share = $Admin->ImageUpladDir( $site );

	$url = FolderUrlByDate( $time );
	
	if ( !empty( $share ) && isset( $share['share'] ) && $share['share'] )
	{
		$url = FolderUrlByDate( $time, $share['html'] );
	}

	//Check if we already have this image
	$data = $db->from( 
	null, 
	"SELECT id_image, filename
	FROM `" . DB_PREFIX . "images`
	WHERE (id_member = " . (int) $_POST['userid'] . ") AND (img_type = 'user')"
	)->single();
	
	//We have an image for this member, so let's check if is the same
	if ( $data )
	{
		//This filename exists, so return its url
		if ( $data['filename'] == $fileName )
		{
			return array(
					'status' => 0,
					'message' => __( 'image-uploaded' ),
					'filename' => $fileName,
					'imageURL' => $url . $fileName,
					'imagePath' => $imgRoot,
			);
		}
		
		else
		{
			$db->update( 'images' )->where( 'id_member', (int) $_POST['userid'] )->where( 'img_type', 'user' )->where( 'img_status', 'full' )->set( 'id_member', 0 );
		}
	}

	if( move_uploaded_file( $tmp_name, $imgRoot ) )
	{
		$arr = array(
					'status' => 0,
					'message' => __( 'image-uploaded' ),
					'filename' => $fileName,
					'imageURL' => $url . $fileName,
					'imagePath' => $imgRoot,
		);
		
		$imageID = addDbImage( $fileName, $folder, $site, $_POST['userid'], 'user', 'full', 0, $time );
		
		if ( $imageID )
		{
			//If we have child site(s), ask them to copy the image locally
			if ( !empty( $share ) && isset( $share['share'] ) && $share['share'] )
			{
				$Admin->PingChildSite( 'sync', 'image', null, $site, $imgUrl, $time );
			}
		}
		
		CreateChildImgs( $imgRoot, $imageID, $time, $folder, $site, 0, 0, 'user' );
	}
	
	else
	{
		$arr = array(
					'status' => -1,
					'message' => __( 'an-error-happened' ),
					'filename' => null,
					'imageURL' => null,
					'imagePath' => null,
		);
	}
	
	return $arr;
}

/*
#####################################################
#
# Add/Update Maintenance Background Image function
#
#####################################################
function ajaxMaintenanceBackgroundImage()
{
	global $Admin;
	
	$tmp_name = $_FILES['file']['tmp_name'];
	
	$name = pathinfo( $_FILES['file']['name'] );
	
	$allowed = AllowedExt();

	//Make sure we allow the extension
	if ( empty( $allowed ) || !in_array( $name['extension'], $allowed ) )
	{
		return array( 'error' => __( 'error-this-extension-is-not-allowed' ) );
	}
	
	$sefName = URLify( $name['filename'] );

	$fileName = $sefName . '.' . $name['extension'];

	$imageID = 0;
	
	$arr = array();
	
	$time = time();
	
	$folder = FolderRootByDate( $time );

	$imgRoot = $folder . $fileName;
	
	$url = FolderUrlByDate( $time );
	
	$site = ( ( isset( $_POST['site'] ) && is_numeric( $_POST['site'] ) ) ? $_POST['site'] : $Admin->GetSite() );
	$lang = ( ( isset( $_POST['lang'] ) && is_numeric( $_POST['lang'] ) ) ? $_POST['lang'] : $Admin->GetLang() );

	//Check if we already have this image
	$query = array(
			'SELECT'	=>  'id_image',

			'FROM'		=> DB_PREFIX . "images",
					
			'WHERE'		=> "filename = :filename AND img_type = 'maintenance' AND id_site = :site",
					
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
					
			'BINDS' 	=> array(
						array( 'PARAM' => ':filename', 'VAR' => $fileName, 'FLAG' => 'STR' ),
						array( 'PARAM' => ':site', 'VAR' => $site, 'FLAG' => 'INT' )
			)
	);

	$data = Query( $query );

	if( move_uploaded_file( $tmp_name, $imgRoot ) )
	{
		$arr = array(
					'status' => 0,
					'message' => __( 'image-uploaded' ),
					'filename' => $fileName,
					'imageURL' => $url . $fileName,
					'imagePath' => $imgRoot,
		);
		
		list( $width, $height, $type ) = @getimagesize( $imgRoot );
		
		if ( file_exists( $imgRoot ) )
		{
			if ( $data )
			{
				$query = array(
					'UPDATE' 	=> DB_PREFIX . "images",
					'SET'		=> "width = :width, height = :height, mime_type = :mime_type, added_time = :time, filename = :filename, size = :size",
					'WHERE'		=>  "id_image = :id",
					'PARAMS' 	=> array( 'NO_PREFIX' => true ),
					'BINDS' => array(
								array('PARAM' => ':id', 'VAR' => $data['id_image'], 'FLAG' => 'INT' ),
								array('PARAM' => ':width', 'VAR' => $width, 'FLAG' => 'INT' ),
								array('PARAM' => ':height', 'VAR' => $height, 'FLAG' => 'INT' ),
								array('PARAM' => ':mime_type', 'VAR' => getimageTypes( $type ), 'FLAG' => 'STR' ),
								array('PARAM' => ':added_time', 'VAR' => $time, 'FLAG' => 'INT' ),
								array('PARAM' => ':filename', 'VAR' => $fileName, 'FLAG' => 'INT' ),
								array('PARAM' => ':size', 'VAR' => filesize( $imgRoot ), 'FLAG' => 'INT' )
					)
				);

				Query( $query, false, false, true );
				
				$imageID = $data['id_image'];
			}
			
			else
			{
				//Check if already have set an image as maintenance and delete it
				$query = array(
						'SELECT'	=>  'id_image',

						'FROM'		=> DB_PREFIX . "images",

						'WHERE'		=> "img_type = 'maintenance' AND img_status = 'full' AND id_site = :site",

						'PARAMS' 	=> array( 'NO_PREFIX' => true ),

						'BINDS' 	=> array(
									array( 'PARAM' => ':site', 'VAR' => $site, 'FLAG' => 'INT' )
						)
				);

				$_data = Query( $query );
				
				if ( $_data )
				{
					DeleteImage( $_data['id_image'] );
				}

				$imageID = addDbImage( $fileName, $folder, $site, $Admin->UserID(), 'maintenance', 'full', 1, $time );
			}
		}
		
		CreateChildImgs( $imgRoot, $imageID, $time, $folder, $site, 0, 0, 'maintenance' );

		$maint = Json( $Admin->Settings()::Site()['maintenance_data'] );
		
		$maint['background_image'] = $url . $fileName;
		
		//Update the site's settings
		$query = array(
			'UPDATE' 	=> DB_PREFIX . 'sites',
			'SET'		=> "maintenance_data = :data",
			'WHERE'		=>  "id = :id",
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
			'BINDS' 	=> array(
						array( 'PARAM' => ':data', 'VAR' => json_encode( $maint, JSON_UNESCAPED_UNICODE ), 'FLAG' => 'STR' ),
						array( 'PARAM' => ':id', 'VAR' => $site, 'FLAG' => 'INT' )
			)
		);

		Query( $query, false, false, true );
		
		$Admin->DeleteSettingsCacheSite();
	}
	
	else
	{
		$arr = array(
					'status' => -1,
					'message' => __( 'an-error-happened' ),
					'filename' => null,
					'imageURL' => null,
					'imagePath' => null,
		);
	}
	
	return $arr;
}

#####################################################
#
# Add/Update Site's default and Maintenance Background image function
#
#####################################################
function ajaxSiteImage()
{
	global $Admin;
	
	$tmp_name = $_FILES['file']['tmp_name'];
	
	$name = pathinfo( $_FILES['file']['name'] );
	
	$sefName = URLify( $name['filename'] );

	$fileName = $sefName . '.' . $name['extension'];

	$imageID = 0;
	
	$arr = array();
	
	$time = time();
	
	$folder = FolderRootByDate( $time );

	$imgRoot = $folder . $fileName;
	
	$url = FolderUrlByDate( $time );
	
	$site = ( ( isset( $_POST['site'] ) && is_numeric( $_POST['site'] ) ) ? $_POST['site'] : $Admin->GetSite() );
	$lang = ( ( isset( $_POST['lang'] ) && is_numeric( $_POST['lang'] ) ) ? $_POST['lang'] : $Admin->GetLang() );
			
	//Check if we already have this image
	$query = array(
			'SELECT'	=>  'id_image',

			'FROM'		=> DB_PREFIX . "images",
					
			'WHERE'		=> "filename = :filename AND img_type = 'site' AND id_site = :site",
					
			'PARAMS' 	=> array( 'NO_PREFIX' => true ),
					
			'BINDS' 	=> array(
							array( 'PARAM' => ':filename', 'VAR' => $fileName, 'FLAG' => 'STR' ),
							array( 'PARAM' => ':site', 'VAR' => $site, 'FLAG' => 'INT' )
			)
	);

	$data = Query( $query );

	if( move_uploaded_file( $tmp_name, $imgRoot ) )
	{
		$arr = array(
					'status' => 0,
					'message' => __( 'image-uploaded' ),
					'filename' => $fileName,
					'imageURL' => $url . $fileName,
					'imagePath' => $imgRoot,
		);
		
		list( $width, $height, $type ) = @getimagesize( $imgRoot );
		
		if ( file_exists( $imgRoot ) )
		{
			if ( $data )
			{
				$query = array(
					'UPDATE' 	=> DB_PREFIX . "images",
					'SET'		=> "width = :width, height = :height, mime_type = :mime_type, added_time = :time, filename = :filename, size = :size",
					'WHERE'		=>  "id_image = :id",
					'PARAMS' 	=> array( 'NO_PREFIX' => true ),
					'BINDS' => array(
								array('PARAM' => ':id', 'VAR' => $data['id_image'], 'FLAG' => 'INT' ),
								array('PARAM' => ':width', 'VAR' => $width, 'FLAG' => 'INT' ),
								array('PARAM' => ':height', 'VAR' => $height, 'FLAG' => 'INT' ),
								array('PARAM' => ':mime_type', 'VAR' => getimageTypes( $type ), 'FLAG' => 'STR' ),
								array('PARAM' => ':added_time', 'VAR' => $time, 'FLAG' => 'INT' ),
								array('PARAM' => ':filename', 'VAR' => $fileName, 'FLAG' => 'INT' ),
								array('PARAM' => ':size', 'VAR' => filesize( $imgRoot ), 'FLAG' => 'INT' )
					)
				);

				Query( $query, false, false, true );
				
				$imageID = $data['id_image'];
			}
			else
			{
				$imageID = addDbImage( $fileName, $folder, $site, $Admin->UserID(), 'site', 'full', 1, $time );
			}
		}

		$sizes = array( '75', '50', '25' );
			
		//Create three smaller copies from the original image
		foreach( $sizes as $_size )
		{
			//Calculate the img size
			$size = CalculateImgSize( $imgRoot, $_size );
				
			if ( empty( $size ) )
				continue;

			$newImg = $sefName . '-' . $size['f'] . '.' . $name['extension'];
				
			CreateImage( $imgRoot, $folder . $newImg, array( 'w' => $size['w'], 'h' => $size['h'] ) );

			if ( file_exists( $folder . $newImg ) ) 
				addDbImage( $newImg, $folder, $site, 0, 'site', 'cropped', 0, null, $imageID );
		}
		
		$Admin->DeleteSettingsCacheSite();
	}
	
	else
	{
		$arr = array(
					'status' => -1,
					'message' => __( 'an-error-happened' ),
					'filename' => null,
					'imageURL' => null,
					'imagePath' => null,
		);
	}
	
	return $arr;
}

#####################################################
#
# New Table Column Element function 
#
#####################################################
function AjaxAddTableColumn()
{
	if ( empty( $_POST ) || !isset( $_POST['formId'] ) )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$formId = (int) $_POST['formId'];
	
	$db = db();
	
	$form = $db->from( 
	null, 
	"SELECT id, id_site
	FROM `" . DB_PREFIX . "forms`
	WHERE (id = " . $formId . ")"
	)->single();
	
	if ( !$form )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}
	
	$temp_ = $db->from( 
	null, 
	"SELECT elem_order
	FROM `" . DB_PREFIX . "form_elements`
	WHERE (id_form = " . $formId . ")
	ORDER BY elem_order DESC
	LIMIT 1"
	)->single();
	
	$el_order 	= ( $temp_ ? ( $temp_['elem_order'] + 1 ) : 0 );
	
	$name 		= __( 'column' ) . ' ' . $el_order;
	
	$el = null;
	
	$dbarr = array(
		"id_form" 		=> $formId,
		"elem_order"	=> $el_order,
		"elem_id"		=> '',
		"data" 			=> json_encode( array() ),
		"elem_name" 	=> $name
	);

	$q = $db->insert( 'form_elements' )->set( $dbarr );
	
	if ( $q )
	{
		$el = $db->lastId();
	}
	
	if ( !$el )
	{
		return array(
			'error' => __( 'an-error-happened' )
		);
	}

	$html = '
	<div data-id="' . $el . '" id="table-item-' . $el . '" class="card multi-collapse">
		<div class="card-header bg-light">
			<h3 class="card-title">
				<span id="elemntTitle' . $el . '">' . $name . '</span>

				<div id="columnTitleDiv' . $el . '" class="btn-group d-none">
					<input placeholder="' . __( 'column-name' )  . '" class="form-control" type="text" id="elemntTitleInput' . $el . '" value="' . $name . '" />
					<button type="button" id="cancelTitle' . $el . '" data-id="' . $el . '" onclick="cancelTitle(' . $el . ');" class="btn btn-tool"><i class="fa fa-times"></i></button>
					<button type="button" id="saveTitle' . $el . '" data-id="' . $el . '" onclick="saveTitle(' . $el . ',' . $formId . ');" class="btn btn-tool"><i class="fa fa-check"></i></button>
				</div>
				<button type="button" id="changeTitle' . $el . '" data-id="' . $el . '" onclick="changeTitle(' . $el . ');" class="btn btn-tool">
					<i class="fas fa-edit"></i>
				</button>
			</h3>

			<div class="card-tools">
				<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
					<i class="fas fa-minus"></i>
				</button>

				<button type="button" id="close" onclick="removeColumn(' . $el . ',' . $formId . ');" data-id="' . $el . '" class="btn btn-tool">
					<i class="fas fa-times"></i>
				</button>
			</div>
		</div>
				
		<!-- Head -->
		<div class="card-body">
				
			<ul class="nav nav-tabs" id="tabs-header-' . $el . '-tab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="tab-header-' . $el . '-head-tab" data-toggle="pill" href="#tab-header-' . $el . '-head" role="tab" aria-controls="tab-header-' . $el . '-head" aria-selected="true">' . __( 'heading' ) . '</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="tab-header-' . $el . '-design-tab" data-toggle="pill" href="#tab-header-' . $el . '-design" role="tab" aria-controls="tab-header-' . $el . '-design" aria-selected="false">' . __( 'design' ) . '</a>
				</li>
			</ul>

			<div class="card-body">
				<div class="tab-content" id="tabs-header-' . $el . '-tabContent">
						
					<div class="tab-pane fade show active" parent="' . $el . '" id="tab-header-' . $el . '-head" role="tabpanel" aria-labelledby="tab-header-' . $el . '-tab">

						<section id="contentHeaderBuilder' . $el . '" class="connectedSortable2"></section>

						<button title="' . __( 'add-element' ) . '" onclick="addColumnHeadElement(' . $el . ');" data-id="' . $el . '" type="button" class="btn btn-tool">
							<i class="fas fa-plus"></i> ' . __( 'add-element' ) . '
						</button>
					</div>
				
					<div class="tab-pane fade" id="tab-header-' . $el . '-design" role="tabpanel" aria-labelledby="tab-' . $el . '-design-tab">
					</div>
				</div>
			</div>

		</div>
				
		<!-- Cell -->
		<div class="card-body">
					
			<ul class="nav nav-tabs" id="tabs-' . $el . '-tab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="tab-' . $el . '-cell-tab" data-toggle="pill" href="#tab-' . $el . '-cell" role="tab" aria-controls="tab-' . $el . '-cell" aria-selected="true">' . __( 'cell-template' ) . '</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="tab-' . $el . '-design-tab" data-toggle="pill" href="#tab-' . $el . '-design" role="tab" aria-controls="tab-' . $el . '-design" aria-selected="false">' . __( 'design' ) . '</a>
				</li>
			</ul>

			<div class="card-body">
				<div class="tab-content" id="tabs-' . $el . '-tabContent">
					<div class="tab-pane fade show active" parent="' . $el . '" id="tab-' . $el . '-cell" role="tabpanel" aria-labelledby="tab-' . $el . '-cell-tab">

						<section id="contentCellBuilder' . $el . '" class="connectedSortable2">
						</section>

						<button title="' . __( 'add-element' ) . '" data-id="' . $el . '" type="button" id="cell" onclick="addColumnCellElement(' . $el . ',\'cell\');" class="btn btn-tool">
							<i class="fas fa-plus"></i> ' . __( 'add-element' ) . '
						</button>
					</div>
				
					<div class="tab-pane fade" id="tab-' . $el . '-design" role="tabpanel" aria-labelledby="tab-' . $el . '-design-tab">
					</div>
				</div>
			</div>

		</div>
	</div>';
	
		<div data-id="' . $el . '" id="table-item-' . $el . '" class="card ">
			<div class="card-header bg-light">
				<h3 class="card-title">
					<span id="elemntTitle' . $el . '">' . $name . '</span>

					<div id="columnTitleDiv' . $el . '" class="btn-group d-none">
						<input placeholder="' . __( 'column-name' )  . '" class="form-control" type="text" id="elemntTitleInput' . $el . '" value="' . $name . '" />
						<button type="button" id="cancelTitle' . $el . '" data-id="' . $el . '" onclick="cancelTitle(' . $el . ');" class="btn btn-tool"><i class="fa fa-times"></i></button>
						<button type="button" id="saveTitle' . $el . '" data-id="' . $el . '" onclick="saveTitle(' . $el . ',' . $formId . ');" class="btn btn-tool"><i class="fa fa-check"></i></button>
					</div>
					<button type="button" id="changeTitle' . $el . '" data-id="' . $el . '" onclick="changeTitle(' . $el . ');" class="btn btn-tool">
						<i class="fas fa-edit"></i>
					</button>
				</h3>

				<div class="card-tools">						
						<button type="button" id="minimize" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-plus"></i>
						</button>

						<button type="button" id="close" onclick="removeElement(' . $el . ');" data-id="' . $el . '" class="btn btn-tool">
							<i class="fas fa-times"></i>
						</button>
					</div>
				</div>

				<ul class="nav nav-tabs" id="tabs-' . $el . '-tab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="tab-' . $el . '-cell-tab" data-toggle="pill" href="#tab-' . $el . '-cell" role="tab" aria-controls="tab-' . $el . '-cell" aria-selected="true">' . __( 'cell-template' ) . '</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="tab-' . $el . '-design-tab" data-toggle="pill" href="#tab-' . $el . '-design" role="tab" aria-controls="tab-' . $el . '-design" aria-selected="false">' . __( 'design' ) . '</a>
					</li>
				</ul>

				<div class="card-body">
					<div class="tab-content" id="tabs-' . $el . '-tabContent">
						<div class="tab-pane fade show active" id="tab-' . $el . '-cell" role="tabpanel" aria-labelledby="tab-' . $el . '-cell-tab">
							<section id="contentBuilder' . $el . '" class="connectedSortable2">
							
							</section>
							
							<button title="' . __( 'add-element' ) . '" data-id="' . $el . '" type="button" id="addColumnElement" class="btn btn-tool">
								<i class="fas fa-plus"></i> ' . __( 'add-element' ) . '
							</button>
						</div>
						<div class="tab-pane fade" id="tab-' . $el . '-design" role="tabpanel" aria-labelledby="tab-' . $el . '-design-tab">
						
						</div>
					</div>

				</div>
			</div>
		</div>';
		
	$Form = GetSingleForm( $formId );
	
	$code = ( $Form ? BuildTablePreviewHtml( $Form['elements'], false ) : null );

	return array( 'status' => 'ok', 'id' => $el, 'html' => $html, 'code' => $code );
}*/