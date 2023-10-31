<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Categories array
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

include ( ARRAYS_ROOT . 'generic-arrays.php');

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
	if ( $this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) )
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

//Set the Custom Upload HTML Data
$addCustomFileHtml = '
<div class="form-group row">
	<label for="fileInput" class="col-sm-2 col-form-label">' . $L['choose-file'] . '</label>
	<div class="col-md-8">
		<input id="importXmlInputFile" class="form-control" type="file" name="uploadFile" accept=".zip,.xml">
		<input type="hidden" name="importId" id="importId" value="">
		<small id="importXmlInputFile" class="form-text text-muted">' . sprintf( $L['max-file-size'], ini_get('upload_max_filesize') ) . '</small>
	</div>
<div>
<!--<button class="btn btn-primary" id="loading_btn" type="button" disabled>
    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    Uploading...
</button>-->
<script type="application/javascript">
$(document).ready(function()
{
	$("button[id=\'submitButton\']").attr("disabled", "disabled").button(\'refresh\');
	
	$("#importXmlInputFile").on(\'change\',function()
	{
		$(\'#submitSpinner\').removeClass(\'d-none\');
		var formData = new FormData();	
		formData.append(\'token\', "' . $this->GetToken() . '");
		formData.append(\'site\', "' . $this->GetSite() . '");
		formData.append(\'lang\', "' . $this->GetLang() . '");
		formData.append(\'file\', $(this)[0].files[0]);
		$.ajax(
		{
			url: "' . AJAX_ADMIN_PATH . 'import-file-upload/",
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false
		})
		.always(function() {
			$(\'#submitSpinner\').addClass(\'d-none\');
		})
		.done(function(data)
		{
			$(\'#submitSpinner\').addClass(\'d-none\');
			if (data.status==0) 
			{
				$("#importId").attr(\'value\',data.importId);
				$("#submitButton").removeAttr("disabled", "disabled").button(\'refresh\');
				//$("button[id=\'submitButton\']").removeAttr("disabled", "disabled").button(\'refresh\');
			} else 
			{
				showAlert(data.message);
			}
		});
	});
});
</script>';

//Author HTML selection
$contentAuthorHtml = '<div class="form-group row"><label for="postAuthor" class="col-sm-2 col-form-label">' . $L['post-author'] . '</label>
<div class="col-md-4"><select id="postAuthor" style="width: 100%; height:36px;" name="postAuthor" class="select2"></select>
<small id="postAuthor" class="form-text text-muted">' . $L['post-author-tip'] . '</small></div></div>
<script>$(document).ready(function()
{
	var parent = $("#postAuthor").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "' . AJAX_ADMIN_PATH . 'get-users/",
			data: function (params) {
				var query = {
					postSite: "' . $this->siteID . '",
					query: params.term
				}
				
				return query;
			},
			processResults: function (data) {
				return data;
			}
		},
		escapeMarkup: function(markup) {
			return markup;
		},
		templateResult: function(data) {
			var html = data.text
			if (data.type=="draft") {
				html += \'<span class="badge badge-pill badge-light">\'+data.type+\'</span>\';
			}
			return html;
		}
	});
});
</script>';
#####################################################
#
# Import Form
#
#####################################################
$importSystems = $customTypes = $postTypes = array();

$postTypes['default'] = array( 'name' => 'default', 'title'=> $L['default-as-it-is'], 'disabled' => false, 'data' => array() );
$postTypes['post'] = array( 'name' => 'post', 'title'=> $L['post'], 'disabled' => false, 'data' => array() );
$postTypes['page'] = array( 'name' => 'page', 'title'=> $L['page'], 'disabled' => false, 'data' => array() );

foreach( $importDataArray as $key => $row )
	$importSystems[$key] = array( 'name' => $key, 'title'=> $row['title'], 'disabled' => false, 'data' => array() );

$query = array(
		'SELECT'	=>  '*',

		'FROM'		=> DB_PREFIX . "post_types",
				
		'WHERE'		=> "id_site = :id_site",
				
		'ORDER'		=> 'title ASC',
				
		'PARAMS' 	=> array( 'NO_PREFIX' => true ),
				
		'BINDS' 	=> array(
							array( 'PARAM' => ':id_site', 'VAR' => $this->GetSite(), 'FLAG' => 'INT' )
		)
);

$types = Query( $query, true );

if ( $types )
{
	foreach( $types as $type )
		$customTypes[$type['id']] = array( 'name' => $type['id'], 'title'=> $type['title'], 'disabled' => false, 'data' => array() );
}

$form = array
(
	'import' => array
	(
		'title' => $L['import-settings'],
		'data' => array
		(
			'tools-settings' => array(
				'title' => null, 'tip' =>$L['import-tip'], 'data' => array
				(
					'choose-system'=>array('label'=>$L['system'], 'name' => 'system', 'type'=>'select', 'value'=>null, 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $importSystems ),
					'post-author'=>array('label'=>$L['post-author'], 'name' => 'post_author', 'type'=>'custom-html', 'value'=>$contentAuthorHtml, 'tip'=>$L['post-template-tip'] ),
					'source-category'=>array( 'label'=>$L['category'], 'type'=>'select-group-multi', 'name' => 'category', 'value'=>null, 'firstNull' => true, 'data' => $_categories, 'tip'=>$L['import-category-tip'] ),
					'custom-type'=>array('label'=>$L['custom-post-type'], 'name' => 'custom_post_type', 'type'=>'select', 'value'=>null, 'tip'=>$L['custom-post-types-import-tip'], 'firstNull' => true, 'disabled' => false, 'data' => $customTypes ),
					'post-type'=>array('label'=>$L['post-type'], 'name' => 'post_type', 'type'=>'select', 'value'=>null, 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $postTypes ),
					'copy-images'=>array('label'=>$L['copy-images-locally'], 'name' => 'copy_images', 'type'=>'checkbox', 'value'=>null, 'tip'=>$L['copy-images-locally-tip'], 'disabled' => false ),
					'old-url'=>array('label'=>$L['your-old-url'], 'type'=>'text', 'name' => 'old_url', 'value' =>null, 'tip'=>$L['your-old-url-tip'] ),
					'file-input'=>array('label'=>null, 'name' => 'file', 'type'=>'custom-html', 'value'=>$addCustomFileHtml, 'tip'=>null ),
				)
			)
		)
	)
);

unset( $_categories );