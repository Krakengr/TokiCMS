<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Auto Menu Settings Form
#
#####################################################
$themeId = $this->ActiveTheme();

$dt = $this->Settings()::Themes();

$code = $this->LangCode();

$dt = ( ( isset( $dt[$themeId] ) && isset( $dt[$themeId]['auto-menu'][$code] ) && !empty( $dt[$themeId]['auto-menu'][$code] ) ) 
		? $dt[$themeId]['auto-menu'][$code] : null );

$tip = ( !$this->IsDefaultLang() ? __( 'auto-menu-language-tip' ) : '' );

$blogs = $this->db->from( null, "
SELECT b.*, (SELECT COUNT(id_post) FROM `" . DB_PREFIX . POSTS . "` as p WHERE p.id_blog = b.id_blog AND p.id_lang = b.id_lang AND p.post_status = 'published') as numposts, (SELECT COUNT(id) FROM `" . DB_PREFIX . "comments` as cm WHERE cm.id_blog = b.id_blog AND cm.id_lang = b.id_lang AND cm.status = 'approved') as numcomm
FROM `" . DB_PREFIX . "blogs` AS b
WHERE (b.id_site = " . $this->siteID . ") AND (b.disabled = 0 OR b.disabled IS NULL)"
)->all();

$types = GetAdminCustomTypes();

$blogsContent = $catsContent = $pagesContent = $cusContent = array();

$q = "(p.id_site = " . $this->siteID . ") AND (p.id_lang = " . $this->langID . ") AND (p.post_type = 'page') AND (p.post_status = 'published') AND (d.external_url = '' OR d.external_url IS NULL)";

$query = PostsDefaultQuery( $q, null, "p.title ASC" );

$pages = $this->db->from( null, $query )->all();

if ( !empty( $pages ) )
{	
	foreach( $pages as $page )
	{
		$pagesContent[$page['id_post']] = array( 'name' => $page['id_post'], 'title' => $page['title'] . ( !empty( $page['blog_name'] ) ? ' [' . $page['blog_name'] . ']' : '' ), 'disabled' => false, 'data' => array() );
	}
}

if ( !empty( $types ) )
{
	foreach( $types as $type )
	{
		$cusContent[$type['id']] = array( 'name' => $type['id'], 'title'=> $type['title'], 'disabled' => false, 'data' => array() );
	}
}

if ( !empty( $blogs ) )
{
	foreach( $blogs as $key => $blog )
	{
		$name = $blog['name'];

		if ( !$this->IsDefaultLang() )
		{
			$temp = Json( $blog['trans_data'] );

			if ( !empty( $temp ) && isset( $temp[$this->LangKey()] ) )
			{
				$name = $temp[$this->LangKey()]['name'];
			}
		}
		
		$blogsContent[$blog['id_blog']] = array( 'name' => $blog['id_blog'], 'title'=> $name, 'disabled' => false, 'data' => array() );
	}
}

$cats = GetAdminCategories( 'name', 'ASC', true, false );

if ( !empty( $cats ) )
{
	foreach( $cats as $cat )
	{
		$catsContent[$cat['id']] = array( 'name' => $cat['id'], 'title'=> $cat['name'], 'disabled' => false, 'data' => array() );
	}
}

//Add Custom Links HTML data
$searchReplaceFieldsHtml = '
<div class="form-group row"><label for="searchReplaceFields" class="col-sm-2 col-form-label">' . __( 'custom-links' ) . '</label>
<div class="col-md-8"><div class="table-responsive">
<table id="customLinksFieldsTable" class="table table-striped table-bordered table-hover">
<thead>
<tr>
<td class="text-left">' . __( 'url' ) . '</td>
<td class="text-left">' . __( 'label' ) . '</td>
<td class="text-left">' . __( 'target' ) . '</td>
<td></td>
</tr>
</thead>
<tbody>';

if ( !empty( $dt['custom_links'] ) )
{
	foreach( $dt['custom_links'] as $customId => $customLink )
	{
		$searchReplaceFieldsHtml .= '<tr id="customLink-row' . $customId . '">';
		$searchReplaceFieldsHtml .= '<td class="text-right"><input type="text" name="custom_link[' . $customId . '][url]" value="' . $customLink['url'] . '" placeholder="https://" class="form-control" /></td>';
		$searchReplaceFieldsHtml .= '<td class="text-right"><input type="text" name="custom_link[' . $customId . '][title]" value="' . $customLink['title'] . '" placeholder="Click me" class="form-control" /></td>';
		
		$searchReplaceFieldsHtml .= '<td class="text-right"><select class="custom-select" name="custom_link[' . $customId . '][target]\"><option value="self" ' . ( ( $customLink['target'] == 'self' ) ? 'selected' : '' ) . '>_self</option><option value="blank" ' . ( ( $customLink['target'] == 'blank' ) ? 'selected' : '' ) . '>_blank</option><option value="top" ' . ( ( $customLink['target'] == 'top' ) ? 'selected' : '' ) . '>_top</option></select></td>';
		
		$searchReplaceFieldsHtml .= '<td class="text-left"><button type="button" onclick="$(\'#customLink-row' . $customId . '\').remove();" data-toggle="tooltip" title="' . __( 'remove' ) . '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
		$searchReplaceFieldsHtml .= '</tr>';
	}
}

$searchReplaceFieldsHtml .= '</tbody>
<tfoot>
<tr>
<td colspan="6"></td>
<td class="text-left"><button type="button" onclick="addCustomLinkField();" data-toggle="tooltip" title="' . __( 'add-new-field') . '" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
</tr>
</tfoot>
</table>
</div><small id="addCustomLinkField" class="form-text text-muted">' . __( 'custom-links-target-tip' ) . '</small></div></div>';

$searchReplaceFieldsScript = "<script><!--
var custom_links_row = " . ( !empty( $dt['custom_links'] ) ? ( count( $dt['custom_links'] ) + 1 ) : 0 ) . ";

function addCustomLinkField() {
	html  = '<tr id=\"customLink-row' + custom_links_row + '\">';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"custom_link[' + custom_links_row + '][url]\" value=\"\" placeholder=\"https://\" class=\"form-control\" /></td>';
	html += '  <td class=\"text-right\"><input type=\"text\" name=\"custom_link[' + custom_links_row + '][title]\" value=\"\" placeholder=\"Click me\" class=\"form-control\" /></td>';
    html += '  <td class=\"text-right\"><select class=\"custom-select\" name=\"custom_link[' + custom_links_row + '][target]\"><option value=\"self\">_self</option><option value=\"blank\">_blank</option><option value=\"top\">_top</option></select></td>';
	html += '  <td class=\"text-left\"><button type=\"button\" onclick=\"$(\'#customLink-row' + custom_links_row + '\').remove();\" data-toggle=\"tooltip\" title=\"" . __( 'remove' ) . "\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';

	$('#customLinksFieldsTable tbody').append(html);
	
	custom_links_row++;
}
//--></script>" . PHP_EOL;

self::AddFooterCode( $searchReplaceFieldsScript );

# Form
$form = array
(
	'auto-menu-settings' => array
	(
		'title' => __( 'auto-menu-settings' ),
		'data' => array
		(
			'settings' => array(
				'title' => null, 'tip' => sprintf( __( 'auto-menu-settings-tip' ), $tip ), 'data' => array
				(
					'disable-menu'=>array( 'label'=> __( 'disable-menu' ), 'type'=>'checkbox', 'name' => 'menu[disable_menu]', 'value' => ( isset( $dt['disable_menu'] ) ? $dt['disable_menu'] : null ), 'tip'=>__( 'disable-menu-tip' ) ),
				
					'show-home'=>array( 'label'=>__( 'show-home-button' ), 'type'=>'checkbox', 'name' => 'menu[show_home]', 'value' => ( isset( $dt['show_home'] ) ? $dt['show_home'] : null ), 'tip'=>null ),
					
					'show-login-logout-links'=>array( 'label'=>__( 'show-login-logout-links' ), 'type'=>'checkbox', 'name' => 'menu[show_login_logout]', 'value' => ( isset( $dt['show_login_logout'] ) ? $dt['show_login_logout'] : null ), 'tip'=> __( 'show-login-logout-links-tip' ) ),
					
					'hr-0'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'show-blogs'=>array('label'=>__( 'show-blogs' ), 'name' => 'menu[show_blogs][]', 'type'=>'select', 'value'=>( isset( $dt['show_blogs'] ) ? $dt['show_blogs'] : null ), 'tip'=>__( 'show-blogs-tip' ), 'firstNull' => false, 'data' => $blogsContent, 'id' => 'slcAmp', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ), 'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>', 'disabled' => ( $this->multiBlogEnabled ? false : true ) ),

					'show-blogs-cats'=>array( 'label'=>__( 'show-blogs-cats' ), 'type'=>'checkbox', 'name' => 'menu[show_blog_cats]', 'value' => ( isset( $dt['show_blog_cats'] ) ? $dt['show_blog_cats'] : null ), 'tip'=> __( 'show-blogs-cats-tip' ), 'disabled' => ( $this->multiBlogEnabled ? false : true ) ),
					
					'limit-blog-categories'=>array( 'label'=>__( 'limit-blog-categories' ), 'type'=>'num', 'min' => 0, 'name' => 'menu[limit_blog_categories]', 'value' => ( isset( $dt['limit_blog_categories'] ) ? $dt['limit_blog_categories'] : 0 ), 'tip'=>__( 'limit-blog-categories-tip' ), 'disabled' => ( $this->multiBlogEnabled ? false : true ) ),
					
					'current-blog-pages-only'=>array( 'label'=>__( 'current-blog-pages-only' ), 'type'=>'checkbox', 'name' => 'menu[only_current_blog_pages]', 'value' => ( isset( $dt['only_current_blog_pages'] ) ? $dt['only_current_blog_pages'] : null ), 'tip'=>__( 'current-blog-pages-only-tip' ), 'disabled' => ( $this->multiBlogEnabled ? false : true ) ),
					
					'hr-1'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'show-custom-post-types'=>array('label'=>__( 'show-custom-post-types' ), 'name' => 'menu[show_custom_types][]', 'type'=>'select', 'value'=>( isset( $dt['show_custom_types'] ) ? $dt['show_custom_types'] : null ), 'tip'=>__( 'show-custom-post-types-tip' ), 'firstNull' => false, 'data' => $cusContent, 'id' => 'slcAmps1', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ), 'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
					
					'show-childs-custom-post-types'=>array( 'label'=>__( 'show-childs-custom-post-types' ), 'type'=>'checkbox', 'name' => 'menu[show_child_custom_types]', 'value' => ( isset( $dt['show_child_custom_types'] ) ? $dt['show_child_custom_types'] : null ), 'tip'=>__( 'show-childs-custom-post-types-tip' ) ),
					
					'hr-2'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),

					'show-pages'=>array('label'=>__( 'show-pages' ), 'name' => 'menu[show_pages][]', 'type'=>'select', 'value'=>( isset( $dt['show_pages'] ) ? $dt['show_pages'] : null ), 'tip'=>__( 'show-pages-tip' ), 'firstNull' => false, 'data' => $pagesContent, 'id' => 'slcAmps2', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ), 'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),

					'show-pages-as-childs'=>array( 'label'=>__( 'show-pages-as-childs' ), 'type'=>'checkbox', 'name' => 'menu[show_pages_as_childs]', 'value' => ( isset( $dt['show_pages_as_childs'] ) ? $dt['show_pages_as_childs'] : null ), 'tip'=>__( 'show-pages-as-childs-tip' ) ),
					
					'pages-more-title'=>array( 'label'=>__( 'pages-more-title' ), 'type'=>'text', 'name' => 'menu[pages_more_title]', 'value' => ( isset( $dt['pages_more_title'] ) ? $dt['pages_more_title'] : __( 'more' ) ), 'tip'=>__( 'pages-more-title-tip' ) ),
					
					'hr-3'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),

					'show-categories'=>array('label'=>__( 'show-categories' ), 'name' => 'menu[show_categories][]', 'type'=>'select', 'value'=>( isset( $dt['show_categories'] ) ? $dt['show_categories'] : null ), 'tip'=>__( 'show-categories-tip' ), 'firstNull' => false, 'data' => $catsContent, 'id' => 'slcAmps', 'class' => 'form-control select2 form-select shadow-none mt-3', 'multiple' => true, 'extraKeys' => array( 'name' => 'data-dropdown-css-class', 'data' => 'select2-purple' ), 'addBefore' => '<div class="select2-purple">', 'addAfter' => '</div>' ),
					
					'show-categories-as-childs'=>array( 'label'=>__( 'show-categories-as-childs' ), 'type'=>'checkbox', 'name' => 'menu[show_categories_as_childs]', 'value' => ( isset( $dt['show_categories_as_childs'] ) ? $dt['show_categories_as_childs'] : null ), 'tip'=> __( 'show-categories-as-childs-tip' ) ),
					
					'categories-button-title'=>array( 'label'=>__( 'categories-button-title' ), 'type'=>'text', 'name' => 'menu[categories_button_title]', 'value' => ( isset( $dt['categories_button_title'] ) ? $dt['categories_button_title'] : __( 'categories' ) ), 'tip'=>__( 'categories-button-title-tip' ) ),
					
					'show-sub-categories'=>array( 'label'=>__( 'show-sub-categories' ), 'type'=>'checkbox', 'name' => 'menu[show_child_categories]', 'value' => ( isset( $dt['show_child_categories'] ) ? $dt['show_child_categories'] : null ), 'tip'=>__( 'show-sub-categories-tip' ) ),
					
					'hide-empty-categories'=>array( 'label'=>__( 'hide-empty-categories' ), 'type'=>'checkbox', 'name' => 'menu[hide_empty_categories]', 'value' => ( isset( $dt['hide_empty_categories'] ) ? $dt['hide_empty_categories'] : null ), 'tip'=>__( 'hide-empty-categories-tip' ) ),
					
					'hr-4'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'custom-links'=>array('label'=>__( 'custom-links' ), 'name' => 'search_replace', 'type'=>'custom-html', 'value'=>$searchReplaceFieldsHtml, 'tip'=>null ),
					
					'show-links-as-childs'=>array( 'label'=>__( 'show-links-as-childs' ), 'type'=>'checkbox', 'name' => 'menu[show_links_as_childs]', 'value' => ( isset( $dt['show_links_as_childs'] ) ? $dt['show_links_as_childs'] : null ), 'tip'=>__( 'show-links-as-childs-tip' ) ),
					
					'links-more-title'=>array( 'label'=>__( 'links-more-title' ), 'type'=>'text', 'name' => 'menu[links_more_title]', 'value' => ( isset( $dt['links_more_title'] ) ? $dt['links_more_title'] : __( 'more' ) ), 'tip'=>__( 'links-more-title-tip' ) )
				)
			)
		)
	)
);