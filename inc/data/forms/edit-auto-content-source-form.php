<?php defined('TOKICMS') or die('Hacking attempt...');

$L = $this->lang;

$_categories = GetCategoriesList( $this->siteID );

#####################################################
#
# Add New Auto Content Source Form
#
#####################################################

global $autoSourceData;

//Get the Source data from the DB here
if ( !is_null ( $this->currentAction ) && ( $this->currentAction == 'edit-content-source' ) )
	$autoSourceData = AdminGetAutoSource( (int) Router::GetVariable( 'key' ) );
else
	$autoSourceData = null;

$Atts = AdminGetAttributes( $this->siteID, $this->langID, $this->blogID );

$scrapeDataSelection = array(
	'normal' 	=> array( 'name' => 'normal', 'title'=> $L['normal-crawl'], 'disabled' => false, 'data' => array() ),
	'desktop' 	=> array( 'name' => 'desktop', 'title'=> $L['crawl-as-googlebot-desktop'], 'disabled' => false, 'data' => array() ),
	'mobile' 	=> array( 'name' => 'mobile', 'title'=> $L['crawl-as-googlebot-mobile'], 'disabled' => false, 'data' => array() )
);

$customFieldsSelectionPre = array(
	'alias' 	=> array( 'name' => $L['title-alias'], 	'value' => 'pre::alias' ),
	'subtitle' 	=> array( 'name' => $L['subtitle'], 	'value' => 'pre::subtitle' ),
	'updated' 	=> array( 'name' => $L['updated'], 		'value' => 'pre::updated' ),
	'price' 	=> array( 'name' => $L['price'], 		'value' => 'pre::price' )
);

$autoCategorySelectionHtml = '
<div class="form-group row">
	<label for="autoCategory" class="col-sm-2 col-form-label">' . $L['auto-add-categories'] . '</label>
	<div class="col-md-8"><select class="form-control shadow-none" id="autoCategory" style="width: 50%; height:36px;" name="auto_category" aria-label="Auto Category Creation">
		<option value="0">' . $L['choose'] . '...</option>';

if ( !empty( $_categories ) )
{
	foreach( $_categories as $cc => $cd )
	{
		if ( $cd['type'] == 'lang' )
		{
			$autoCategorySelectionHtml .= '<option value="lang::' . $cd['id'] . '">' . $cd['name'] . '</option>';
			
			if ( !empty( $cd['childs'] ) )
			{
				foreach( $cd['childs'] as $chd )
				{
					if ( ( $chd['type'] == 'blog' ) && ( $chd['id'] > 0 ) )
					{
						$autoCategorySelectionHtml .= '<option value="blog::' . $chd['id'] . '">¦&nbsp;&nbsp;&nbsp;&nbsp;' . $chd['name'] . '</option>';
					}
				}
			}
		}
	}
}

$autoCategorySelectionHtml .= '</select>
	<small id="autoCategoryTip" class="form-text text-muted">' . $L['auto-add-categories-tip'] . '</small>
	</div>
</div>';

//$customFieldsSelectionHtml = '<select class="form-select shadow-none" style="width: 100%; height:36px;" name="custom_fields" aria-label="Custom Fields">';
$customFieldsSelectionHtml = '';//<optgroup label="&nbsp;' . $L['predefined-keys'] . '">';
					
//There are a few predefined keys from the db
foreach( $customFieldsSelectionPre as $cus => $cusDt )
	$customFieldsSelectionHtml .= '<option value="' . $cusDt['value'] . '">' . $cusDt['name'] . '</option>';

//$customFieldsSelectionHtml .= '</optgroup>';

if ( !empty( $Atts ) )
{
	$customFieldsSelectionHtml .= '<optgroup label="&nbsp;' . $L['post-attributes'] . '">';
	
	foreach( $Atts as $att )
		$customFieldsSelectionHtml .= '<option value="att::' . $att['id'] . '">' . stripslashes( $att['name'] ) . '</option>';

	$customFieldsSelectionHtml .= '</optgroup>';
}

//$customFieldsSelectionHtml .= '</select>';


//Set a few strings from the DB
$sourceExtraData = ( $autoSourceData ? Json( $autoSourceData['custom_data'] ) : null );
$sourceSearchReplace = ( $sourceExtraData ? $sourceExtraData['search_replace'] : null );
$sourceRegex = ( $sourceExtraData ? $sourceExtraData['regex'] : null );
$sourceRegexCustomFields = ( $sourceExtraData ? $sourceExtraData['regex']['custom_fields'] : null );

//Continue with the default HTML data
$setPostType['post'] = array( 'name' => 'post', 'title'=> $L['post'], 'disabled' => false, 'data' => array() );
$setPostType['page'] = array( 'name' => 'page', 'title'=> $L['page'], 'disabled' => false, 'data' => array() );

$setPostStatus['published'] = array( 'name' => 'published', 'title'=> $L['published'], 'disabled' => false, 'data' => array() );
$setPostStatus['draft'] = array( 'name' => 'draft', 'title'=> $L['draft'], 'disabled' => false, 'data' => array() );

//Post Buttons template
$postTmpltButtons = array(
	'post-title' => array( 'title' => $L['title'], 'var' => '{{title}} ' ),
	'post-description' => array( 'title' => $L['post-description'], 'var' => '{{description}} ' ),
	'post-content' => array( 'title' => $L['content'], 'var' => '{{content}} ' ),
	'image-url' => array( 'title' => $L['image-url'], 'var' => '{{image-url}} ' ),
	'source-url' => array( 'title' => $L['source-url'], 'var' => '{{source-url}} ' ),
	'read-more-tag' => array( 'title' => $L['read-more-tag'], 'var' => '{{more}} ' ),
);

//The default template value
$defaultTmlptValue = htmlentities( '{{description}}<br /><img src="{{image-url}}"><br />
	{{content}}<br />
<a href="{{source-url}}" target="_blank">' . $L['source'] . '</a>' );

//Author HTML selection
$contentAuthorHtml = '<div class="form-group row"><label for="postAuthor" class="col-sm-2 col-form-label">' . $L['post-author'] . '</label>
<div class="col-md-4"><select id="postAuthor" style="width: 100%; height:36px;" name="postAuthor" class="select2">';

//Insert the User into the select box
if ( $autoSourceData )
	$contentAuthorHtml .= '<option  value="' . $autoSourceData['user_id'] . '">' . ( !empty( $autoSourceData['real_name'] ) ? $autoSourceData['real_name'] : $autoSourceData['user_name'] ) . '</option>';

$contentAuthorHtml .= '</select>
<small id="postAuthor" class="form-text text-muted">' . $L['post-author-tip'] . '</small></div></div>';

//Search and replace HTML data
$searchReplaceFieldsHtml = '<div class="form-group row"><label for="searchReplaceFields" class="col-sm-2 col-form-label">' . $L['search'] . ' ' . $L['replace'] . '</label>
<div class="col-md-8"><div class="table-responsive">
<table id="searchReplaceFieldsTable" class="table table-striped table-bordered table-hover">
<thead>
<tr>
<td class="text-left">' . $L['search'] . '</td>
<td class="text-left">' . $L['replace'] . '</td>
<td></td>
</tr>
</thead>
<tbody>';

if ( !empty( $sourceSearchReplace ) )
{
	foreach( $sourceSearchReplace as $sourceSearchReplaceID => $sourceSearchReplaceSinge )
	{
		$searchReplaceFieldsHtml .= '<tr id="searchReplace-row' . $sourceSearchReplaceID . '">';
		$searchReplaceFieldsHtml .= '<td class="text-right"><input type="text" name="search_replace[' . $sourceSearchReplaceID . '][search]" value="' . $sourceSearchReplaceSinge['search'] . '" placeholder="' . $L['search'] . '" class="form-control" /></td>';
		$searchReplaceFieldsHtml .= '<td class="text-right"><input type="text" name="search_replace[' . $sourceSearchReplaceID . '][replace]" value="' . $sourceSearchReplaceSinge['replace'] . '" placeholder="' . $L['replace'] . '" class="form-control" /></td>';
		$searchReplaceFieldsHtml .= '<td class="text-left"><button type="button" onclick="$(\'#searchReplace-row' . $sourceSearchReplaceID . '\').remove();" data-toggle="tooltip" title="' . $L['remove'] . '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
		$searchReplaceFieldsHtml .= '</tr>';
	}
}

$searchReplaceFieldsHtml .= '</tbody>
<tfoot>
<tr>
<td colspan="6"></td>
<td class="text-left"><button type="button" onclick="addSearchReplaceField();" data-toggle="tooltip" title="' . $L['add-new-field'] . '" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
</tr>
</tfoot>
</table>
</div><small id="addSearchReplaceField" class="form-text text-muted">' . $L['search-replace-fields-tip'] . '</small></div></div>';

$searchReplaceFieldsScript = "<script><!--
var search_replace_row = " . ( !empty( $sourceSearchReplace ) ? ( count( $sourceSearchReplace ) + 1 ) : 0 ) . ";

function addSearchReplaceField() {
	html  = '<tr id=\"searchReplace-row' + search_replace_row + '\">';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"search_replace[' + search_replace_row + '][search]\" value=\"\" placeholder=\"" . $L['search'] . "\" class=\"form-control\" /></td>';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"search_replace[' + search_replace_row + '][replace]\" value=\"\" placeholder=\"" . $L['replace'] . "\" class=\"form-control\" /></td>';
	html += '  <td class=\"text-left\"><button type=\"button\" onclick=\"$(\'#searchReplace-row' + search_replace_row + '\').remove();\" data-toggle=\"tooltip\" title=\"" . $L['remove'] . "\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';

	$('#searchReplaceFieldsTable tbody').append(html);
	
	search_replace_row++;
}
//--></script>" . PHP_EOL;

$addSearchPostAuthorCode = '
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
</script>' . PHP_EOL;

$this->AddFooterCode( $searchReplaceFieldsScript );
$this->AddFooterCode( $addSearchPostAuthorCode );

//Set the Custom Regex HTML Data
$addCustomRegexHtml = '<div class="form-group row"><label for="customFields" class="col-sm-2 col-form-label">' . $L['custom-fields'] . '</label>
<div class="col-md-8"><div class="table-responsive">
<table id="customFieldsTable" class="table table-striped table-bordered table-hover">
<thead>
<tr>
<td class="text-left">' . $L['name'] . '</td>
<td class="text-left">' . $L['field'] . '</td>
<td class="text-left">' . $L['value'] . '</td>
<td></td>
</tr>
</thead>
<tbody>';

if ( $sourceRegexCustomFields && !empty( $sourceRegexCustomFields ) ) 
{
	foreach( $sourceRegexCustomFields as $regId => $regData )
	{
		$addCustomRegexHtml .= '<tr id="customField-row' . $regId . '">';
		$addCustomRegexHtml .= '  <td class="text-right"><input type="text" name="custom_fields[' . $regId . '][name]" value="' . $regData['name'] . '" placeholder="' . $L['price'] . '" class="form-control" /></td>';
		$addCustomRegexHtml .= '  <td class="text-right">';
		//$addCustomRegexHtml .= '  ' . CustomFieldsHtml( $regId );
		
		//We are going to rebuild the SELECT box
		$addCustomRegexHtml .= '<select class="form-select shadow-none" style="width: 100%; height:36px;" name="custom_fields[' . $regId . '][field]" aria-label="Custom Fields">';
		$addCustomRegexHtml .= '<optgroup label="&nbsp;' . $L['predefined-keys'] . '">';

		//There are a few predefined keys from the db
		foreach( $customFieldsSelectionPre as $cus => $cusDt )
			$addCustomRegexHtml .= '<option value="' . $cusDt['value'] . '" ' . ( ( !empty( $regData ) && ( $regData['field'] == $cusDt['value'] ) ) ? 'selected' : '' ) . '>' . $cusDt['name'] . '</option>';

		$addCustomRegexHtml .= '</optgroup>';

		if ( !empty( $Atts ) )
		{
			$addCustomRegexHtml .= '<optgroup label="&nbsp;' . $L['post-attributes'] . '">';
			
			foreach( $Atts as $att )
				$addCustomRegexHtml .= '<option value="att::' . $att['id'] . '" ' . ( ( !empty( $regData ) && ( $regData['field'] == 'att::' . $att['id'] ) ) ? 'selected' : '' ) . '>' . stripslashes( $att['name'] ) . '</option>';

			$addCustomRegexHtml .= '</optgroup>';
		}

		$addCustomRegexHtml .= '</select>';
		
		$addCustomRegexHtml .= '</td>';
		
		$addCustomRegexHtml .= '  <td class="text-right"><input type="text" name="custom_fields[' . $regId . '][value]" value="' . EscapeRegex( $regData['value'], true ) . '" placeholder="&lt;span class=\'product-price-total\'&gt;\d+\,?\d*&lt;/span&gt;" class="form-control" /></td>';
		$addCustomRegexHtml .= '  <td class="text-left"><button type="button" onclick="$(\'#customField-row' . $regId . '\').remove();" data-toggle="tooltip" title="' . $L['remove'] . '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
		$addCustomRegexHtml .= '</tr>';
	}
}
	
	
	
$addCustomRegexHtml .= '</tbody>
<tfoot>
<tr>
<td colspan="6"></td>
<td class="text-left"><button type="button" onclick="addCustomField();" data-toggle="tooltip" title="' . $L['add-custom-field'] . '" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
</tr>
</tfoot>
</table>
</div><small id="customFields" class="form-text text-muted">' . $L['custom-fields-tip'] . '</small></div></div>';

$addCustomFieldScript = "<script><!--
var custom_row = " . ( $sourceRegexCustomFields ? count( $sourceRegexCustomFields ) : 0 ) . ";

function addCustomField() {
	html  = '<tr id=\"customField-row' + custom_row + '\">';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"custom_fields[' + custom_row + '][name]\" value=\"\" placeholder=\"" . $L['title'] . "\" class=\"form-control\" /></td>';
	html += '  <td class=\"text-right\"><select id=\"customFieldSelect\" class=\"form-control shadow-none\" style=\"width: 100%; height:36px;\" name=\"custom_fields[' + custom_row + '][field]\" aria-label=\"Custom Fields\">" . $customFieldsSelectionHtml . "</select></td>';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"custom_fields[' + custom_row + '][value]\" value=\"\" placeholder=\"&lt;span class=\'product-price-total\'&gt;\d+\,?\d*&lt;/span&gt;\" class=\"form-control\" /></td>';
	html += '  <td class=\"text-left\"><button type=\"button\" onclick=\"$(\'#customField-row' + custom_row + '\').remove();\" data-toggle=\"tooltip\" title=\"" . $L['remove'] . "\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';

	$('#customFieldsTable tbody').append(html);
	
	custom_row++;
}
//--></script>" . PHP_EOL;

$this->AddFooterCode( $addCustomFieldScript );

#####################################################
#
# Edit Auto Content Source Form
#
#####################################################
$editDefaultTmpl = ( $autoSourceData ? $autoSourceData['post_template'] : $defaultTmlptValue );

//Create the Edit Source HTML Form
$form = array
(
	'edit-source' => array
	(
		'title' => $L['source-settings'],
		'data' => array(
		
			'source-settings' => array( 
				'title' => null, 'data' => array
				(
					'source-title'=>array('label'=>$L['title'], 'type'=>'text', 'name' => 'title', 'value' =>( $autoSourceData ? $autoSourceData['title'] : null ), 'required' => true, 'tip'=>$L['the-title-how-it-appears']),
					
					'source-url'=>array('label'=>$L['source-url'], 'type'=>'text', 'name' => 'url', 'value' =>( $autoSourceData ? $autoSourceData['url'] : null ), 'required' => true, 'tip'=>$L['auto-source-url-tip'], 'placeholder' => 'https://site.com/feed/'),
					
					'avoid-words'=>array('label'=>$L['words-to-avoid'], 'type'=>'text', 'name' => 'avoid_words', 'value' =>( $autoSourceData ? $autoSourceData['avoid_words'] : null ), 'tip'=>$L['words-to-avoid-tip'], 'placeholder' => 'word1, word2, word3'),
					
					'required-words'=>array('label'=>$L['required-words'], 'type'=>'text', 'name' => 'required_words', 'value' =>( $autoSourceData ? $autoSourceData['required_words'] : null ), 'tip'=>$L['required-words-tip'], 'placeholder' => 'word1, word2, word3'),
					
					'source-title-tags'=>array('label'=>$L['source-title-keywords-to-tags'], 'type'=>'num', 'name' => 'title_to_tags', 'value'=>( $autoSourceData ? $autoSourceData['add_tags'] : 0 ), 'tip'=>$L['source-title-keywords-to-tags-tip'], 'min'=>'0', 'max'=>'5'),
					
					'copy-images-locally'=>array('label'=>$L['copy-images-from-content-locally'], 'type'=>'checkbox', 'name' => 'copy_images', 'value' =>( $autoSourceData ? IsTrue( $autoSourceData['copy_images'] ) : null ), 'tip'=>$L['copy-images-from-content-locally-tip']),
					
					'skip-post-without-images'=>array('label'=>$L['skip-posts-that-dont-have-images'], 'type'=>'checkbox', 'name' => 'skip_posts_without_images', 'value' =>( $autoSourceData ? IsTrue( $autoSourceData['skip_posts_no_images'] ) : null ), 'tip'=>$L['skip-posts-that-dont-have-images-tip']),
					
					'remove-images'=>array('label'=>$L['remove-images-from-content'], 'type'=>'checkbox', 'name' => 'remove_images', 'value' =>( $autoSourceData ? IsTrue( $autoSourceData['remove_images'] ) : null ), 'tip'=>null),
					
					'set-cover-image'=>array('label'=>$L['set-the-first-image-as-cover-image'], 'type'=>'checkbox', 'name' => 'set_cover', 'value' =>( $autoSourceData ? IsTrue( $autoSourceData['set_first_image_cover'] ) : null ), 'tip'=>null),
					
					'use-original-date'=>array('label'=>$L['use-original-date-if-possible'], 'type'=>'checkbox', 'name' => 'set_original_date', 'value' =>( $autoSourceData ? IsTrue( $autoSourceData['set_original_date'] ) : null ), 'tip'=>null),
					
					'set-source-link'=>array('label'=>$L['set-source-link-at-the-end-of-the-post'], 'type'=>'checkbox', 'name' => 'set_source', 'value' =>( $autoSourceData ? IsTrue( $autoSourceData['set_source_link'] ) : null ), 'tip'=>null),
					
					'strip-html'=>array('label'=>$L['strip-html-from-content'], 'type'=>'checkbox', 'name' => 'strip_html', 'value' =>( $autoSourceData ? IsTrue( $autoSourceData['strip_html'] ) : null ), 'tip'=>$L['strip-html-from-content-tip']),
					
					'remove-links'=>array('label'=>$L['strip-links-from-the-post'], 'type'=>'checkbox', 'name' => 'strip_links', 'value' =>( $autoSourceData ? IsTrue( $autoSourceData['strip_links'] ) : null ), 'tip'=>$L['strip-links-from-the-post-tip']),
					
					'enable-auto-deletion'=>array('label'=>$L['enable-post-auto-deletion-feature-days'], 'type'=>'num', 'name' => 'auto_deletion', 'value'=>( $autoSourceData ? $autoSourceData['auto_delete_days'] : 0 ), 'tip'=>$L['enable-post-auto-deletion-feature-days-tip'], 'min'=>'0', 'max'=>'99'),
					
					'max-posts'=>array('label'=>$L['max-posts'], 'type'=>'num', 'name' => 'max_posts', 'value'=>( $autoSourceData ? $autoSourceData['max_posts'] : 0 ), 'tip'=>$L['max-posts-tip'], 'min'=>'0', 'max'=>'99'),
					
					'skip-posts'=>array('label'=>$L['skip-posts-older-than-days'], 'type'=>'num', 'name' => 'skip_older_posts', 'value'=>( $autoSourceData ? $autoSourceData['skip_posts_days'] : 0 ), 'tip'=>$L['skip-posts-older-than-days-tip'], 'min'=>'0', 'max'=>'99'),
					
					'search-replace'=>array('label'=>$L['search'], 'name' => 'search_replace', 'type'=>'custom-html', 'value'=>$searchReplaceFieldsHtml, 'tip'=>null ),				
				)
			),

			'post-settings' => array( 
				'title' => $L['post-settings'], 'data' => array
				(
					'post-type'=>array('label'=>$L['post-type'], 'type'=>'select', 'name' => 'post_type', 'value'=>( $autoSourceData ? $autoSourceData['post_type'] : null ), 'tip'=>$L['post-type-tip'], 'firstNull' => false, 'data' => $setPostType ),
					
					'post-status'=>array('label'=>$L['status'], 'type'=>'select', 'name' => 'post_status', 'value'=>( $autoSourceData ? $autoSourceData['post_status'] : null ), 'tip'=>$L['post-status-tip'], 'firstNull' => false, 'data' => $setPostStatus ),
					
					'source-category'=>array( 'label'=>$L['category'], 'type'=>'select-group-multi', 'name' => 'category', 'value'=>( $autoSourceData ? $autoSourceData['id_category'] : null ), 'firstNull' => true, 'data' => $_categories, 'tip'=>$L['source-category-tip'] ),
					
					'auto-category'=>array('label'=>$L['auto-add-categories'], 'name' => 'auto_category', 'type'=>'custom-html', 'value'=>$autoCategorySelectionHtml, 'tip'=>$L['auto-add-categories-tip'] ),
					
					'post-author'=>array('label'=>$L['post-author'], 'name' => 'post_author', 'type'=>'custom-html', 'value'=>$contentAuthorHtml, 'tip'=>$L['post-template-tip'] ),
					
					'post-template'=>array('label'=>$L['post-template'], 'name' => 'post_template', 'type'=>'textarea', 'value'=>$editDefaultTmpl, 'tip'=>$L['post-template-tip'], 'buttons' =>  $postTmpltButtons ),
				)
			),
			
			'advanced-settings' => array( 
				'title' => $L['advanced-crawl-settings'], 'tip' =>$L['advanced-crawl-settings-tip'], 'data' => array
				(
					'crawl-as'=>array('label'=>$L['crawl-as'], 'type'=>'select', 'name' => 'crawl_as', 'value'=>( $sourceRegex ? $sourceRegex['crawl_as'] : null ), 'tip'=>$L['crawl-as-tip'], 'firstNull' => false, 'data' => $scrapeDataSelection ),
					'rotate-ip-address'=>array('label'=>$L['rotate-ip-address-and-user-agent-to-scrape-data'], 'type'=>'checkbox', 'name' => 'rotate_ip_address', 'value' =>( $sourceRegex ? IsTrue( $sourceRegex['rotate_ip_address'] ) : null ), 'tip'=>$L['rotate-ip-address-and-user-agent-to-scrape-data-tip']),
					'hr'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					'post-title'=>array('label'=>$L['title'], 'type'=>'text', 'name' => 'regex_title', 'value' =>( $sourceRegex ? EscapeRegex( $sourceRegex['regex_title'], true ) : null ), 'placeholder' => 'e.g.  &lt;meta property=&quot;og:title&quot; content=&quot;([A-Za-z0-9-]+)&quot; /&gt; ', 'tip'=>null),
					'post-description'=>array('label'=>$L['description'], 'type'=>'text', 'name' => 'regex_descr', 'value' =>( $sourceRegex ? EscapeRegex( $sourceRegex['regex_descr'], true ) : null ), 'placeholder' => 'e.g. &lt;div id=&quot;description&quot;&gt;(.*)&lt;/div&gt;', 'tip'=>null),
					'post-image'=>array('label'=>$L['featured-image'], 'type'=>'text', 'name' => 'regex_image', 'value' =>( $sourceRegex ? EscapeRegex( $sourceRegex['regex_image'], true ) : null ), 'placeholder' => 'e.g. &lt;img.+src=[\'&quot;]([^\'&quot;]+)[\'&quot;].*&gt;', 'tip'=>null),
					'post-content'=>array('label'=>$L['content'], 'type'=>'text', 'name' => 'regex_content', 'value' =>( $sourceRegex ? EscapeRegex( $sourceRegex['regex_content'], true ) : null ), 'placeholder' => 'e.g. &lt;div class=&quot;content&quot;&gt;(.*)&lt;/div&gt;', 'tip'=>null),
					'post-tags-container'=>array('label'=>$L['tags-container'], 'type'=>'text', 'name' => 'regex_tags_container', 'value' =>( $sourceRegex ? EscapeRegex( $sourceRegex['regex_tags_container'], true ) : null ), 'placeholder' => 'e.g. <div>(.*)</div>', 'tip'=>$L['tags-container-tip']),
					'post-tags'=>array('label'=>$L['tags'], 'type'=>'text', 'name' => 'regex_tags', 'value' =>( $sourceRegex ? EscapeRegex( $sourceRegex['regex_tags'], true ) : null ), 'placeholder' => 'e.g. &lt;div class=&quot;tags&quot;&gt;(.*)&lt;/div&gt;', 'tip'=>$L['tags-preg-tip']),					
					'post-author'=>array('label'=>$L['post-author'], 'name' => 'post_author', 'type'=>'custom-html', 'value'=>$addCustomRegexHtml, 'tip'=>$L['post-template-tip'] ),
				)
			),
		)
	)
);

unset( $_categories, $sourceExtraData, $editDefaultTmpl, $sourceRegex, $sourceSearchReplace, $sourceCustomField, $sourceRegexCustomFields );