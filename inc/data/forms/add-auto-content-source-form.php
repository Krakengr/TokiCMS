<?php defined('TOKICMS') or die('Hacking attempt...');

$L = $this->lang;

$_categories = GetCategoriesList( $this->siteID );

#####################################################
#
# Add New Auto Content Source Form
#
#####################################################

//Get the Source data from the DB here
if ( !is_null ( $this->currentAction ) && ( $this->currentAction == 'edit-content-source' ) )
	$autoSourceData = AdminGetAutoSource( $this->router->UrlKey() );
else
	$autoSourceData = null;

$Atts = AdminGetAttributes( $this->siteID, $this->langID, $this->blogID );

$sourceTypeArray = array(
	'rss' 		=> array( 'name' => 'rss', 			'title'=> $L['rss-feed'], 		'disabled' => false, 'data' => array() ),
	'multi' 	=> array( 'name' => 'multi', 		'title'=> $L['multiple-sources-feed'], 'disabled' => false, 'data' => array() ),
	'html' 		=> array( 'name' => 'html', 		'title'=> $L['html'], 			'disabled' => false, 'data' => array() ),
	'xml' 		=> array( 'name' => 'xml', 			'title'=> $L['xml'], 			'disabled' => false, 'data' => array() )
);

$sourceXmlTypeArray = array(
	'sitemap' 	=> array( 'name' => 'sitemap', 	'title'=> $L['sitemap-file'], 		'disabled' => false, 'data' => array() ),
	'feed' 		=> array( 'name' => 'feed', 	'title'=> $L['xml-product-feed'], 	'disabled' => false, 'data' => array() ),
	'index' 	=> array( 'name' => 'index', 	'title'=> $L['sitemap-index-file'], 'disabled' => false, 'data' => array() )
);

$customFieldsSelectionPre = array(
	'alias' 	=> array( 'name' => $L['title-alias'], 	'value' => 'pre::alias' ),
	'subtitle' 	=> array( 'name' => $L['subtitle'], 	'value' => 'pre::subtitle' ),
	'updated' 	=> array( 'name' => $L['updated'], 		'value' => 'pre::updated' ),
	'price' 	=> array( 'name' => $L['price'], 		'value' => 'pre::price' ),
);

$xmlFieldsSelection = array(
	'title' 			=> array( 'name' => $L['title'], 				'value' => 'pre::title' ),
	'subtitle' 			=> array( 'name' => $L['subtitle'], 			'value' => 'pre::subtitle' ),
	'content' 			=> array( 'name' => $L['content'], 				'value' => 'pre::content' ),
	'added' 			=> array( 'name' => $L['added'], 				'value' => 'pre::added' ),
	'category' 			=> array( 'name' => $L['category'], 			'value' => 'pre::category' ),
	'subcategory' 		=> array( 'name' => $L['subcategory'], 			'value' => 'pre::subcategory' ),
	'cover' 			=> array( 'name' => $L['cover-image'], 			'value' => 'pre::cover' ),
	'uid' 				=> array( 'name' => $L['unique-identifier'],	'value' => 'pre::uid' ),
	'url' 				=> array( 'name' => $L['url'],					'value' => 'pre::url' ),
	'manufacturer' 		=> array( 'name' => $L['manufacturer'],			'value' => 'pre::manufacturer' ),
	'in-stock' 			=> array( 'name' => $L['in-stock'],				'value' => 'pre::in-stock' ),
	'availability' 		=> array( 'name' => $L['availability'],			'value' => 'pre::availability' ),
	'updated' 			=> array( 'name' => $L['updated'], 				'value' => 'pre::updated' ),
	'current-price' 	=> array( 'name' => $L['current-price'], 		'value' => 'pre::current-price' ),
	'discount-price' 	=> array( 'name' => $L['discount-price'], 		'value' => 'pre::discount-price' ),
	'rating' 			=> array( 'name' => $L['rating'], 				'value' => 'pre::rating' )
);

$scrapeDataSelection = array(
	'normal' 	=> array( 'name' => 'normal', 'title'=> $L['normal-crawl'], 'disabled' => false, 'data' => array() ),
	'desktop' 	=> array( 'name' => 'desktop', 'title'=> $L['crawl-as-googlebot-desktop'], 'disabled' => false, 'data' => array() ),
	'mobile' 	=> array( 'name' => 'mobile', 'title'=> $L['crawl-as-googlebot-mobile'], 'disabled' => false, 'data' => array() )
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

//XML Selection Fields
$xmlFieldsSelectionHtml = '<optgroup label="&nbsp;' . $L['post-settings'] . '">';

//There are a few predefined keys from the db
foreach( $xmlFieldsSelection as $cus => $cusDt )
{
	$xmlFieldsSelectionHtml .= '<option value="' . $cusDt['value'] . '">' . $cusDt['name'] . '</option>';
}

$xmlFieldsSelectionHtml .= '</optgroup>';

//Select Store HTML selection
$selectStoreHtml = '<div id="storeSelection" class="form-group row d-none"><label for="storeSelection" class="col-sm-2 col-form-label">' . $L['choose-store'] . '</label>
<div class="col-md-4"><select id="storeSelect" style="width: 100%; height:36px;" name="storeSelect" class="select2"></select>
<small id="storeSelectTip" class="form-text text-muted">' . $L['import-choose-store-tip'] . '</small></div></div>';

$selectStoreHtml2 = '<div id="storeSelection2" class="form-group row d-none"><label for="storeSelection2" class="col-sm-2 col-form-label">' . $L['choose-store'] . '</label>
<div class="col-md-4"><select id="storeSelect2" style="width: 100%; height:36px;" name="storeSelect2" class="select2"></select>
<small id="storeSelect2Tip" class="form-text text-muted">' . $L['import-choose-store-tip'] . '</small></div></div>';

//HTML/RSS Selection Fields
$customFieldsSelectionHtml = '<optgroup label="&nbsp;' . $L['post-settings'] . '">';

//There are a few predefined keys from the db
foreach( $customFieldsSelectionPre as $cus => $cusDt )
{
	$customFieldsSelectionHtml .= '<option value="' . $cusDt['value'] . '">' . $cusDt['name'] . '</option>';
}

$customFieldsSelectionHtml .= '</optgroup>';

if ( !empty( $Atts ) )
{
	$customFieldsSelectionHtml .= $xmlFieldsSelectionHtml .= '<optgroup label="&nbsp;' . $L['post-attributes'] . '">';
	
	foreach( $Atts as $att )
	{
		$customFieldsSelectionHtml .= $xmlFieldsSelectionHtml .= '<option value="att::' . $att['id'] . '">' . stripslashes( $att['name'] ) . '</option>';
	}

	$customFieldsSelectionHtml .= $xmlFieldsSelectionHtml .= '</optgroup>';
}

//Set a few strings from the DB
$sourceExtraData 			= ( $autoSourceData ? Json( $autoSourceData['custom_data'] ) : null );
$sourceSearchReplace 		= ( $sourceExtraData ? $sourceExtraData['search_replace'] : null );
$sourceRegex 				= ( $sourceExtraData ? $sourceExtraData['regex'] : null );
$sourceRegexCustomFields 	= ( $sourceExtraData ? $sourceExtraData['regex']['custom_fields'] : null );

//Continue with the default HTML data
$setPostType['post'] = array( 'name' => 'post', 'title'=> $L['post'], 'disabled' => false, 'data' => array() );
$setPostType['page'] = array( 'name' => 'page', 'title'=> $L['page'], 'disabled' => false, 'data' => array() );

$setPostStatus['published'] = array( 'name' => 'published', 'title'=> $L['published'], 'disabled' => false, 'data' => array() );
$setPostStatus['draft'] = array( 'name' => 'draft', 'title'=> $L['draft'], 'disabled' => false, 'data' => array() );

//Post Buttons template
$postTmpltButtons = array(
	'post-title' 		=> array( 'title' => $L['title'], 				'var' => '{{title}} ' ),
	'post-description' 	=> array( 'title' => $L['post-description'], 	'var' => '{{description}} ' ),
	'post-content' 		=> array( 'title' => $L['content'], 			'var' => '{{content}} ' ),
	'image-url' 		=> array( 'title' => $L['image-url'], 			'var' => '{{image-url}} ' ),
	'source-url' 		=> array( 'title' => $L['source-url'], 			'var' => '{{source-url}} ' ),
	'read-more-tag' 	=> array( 'title' => $L['read-more-tag'], 		'var' => '{{more}} ' ),
);

//The default template value
$defaultTmlptValue = htmlentities( '{{description}}<br /><img src="{{image-url}}"><br />
	{{content}}<br />
<a href="{{source-url}}" target="_blank">' . $L['source'] . '</a>' );

//Multi sources tip
$multiSourcesTip = '<div id="multiSourcesTip" class="form-group row d-none"><label class="col-sm-2 col-form-label">&nbsp;</label><div class="col-md-8"><div class="alert alert-info" role="alert">' . __( 'multiple-sources-feed-tip' ) . '</div></div></div>';

//Author HTML selection
$contentAuthorHtml = '<div class="form-group row"><label for="postAuthor" class="col-sm-2 col-form-label">' . $L['post-author'] . '</label>
<div class="col-md-4"><select id="postAuthor" style="width: 100%; height:36px;" name="postAuthor" class="select2">';

//Insert the User into the select box
if ( $autoSourceData )
	$contentAuthorHtml .= '<option  value="' . $autoSourceData['user_id'] . '">' . ( !empty( $autoSourceData['real_name'] ) ? $autoSourceData['real_name'] : $autoSourceData['user_name'] ) . '</option>';

$contentAuthorHtml .= '</select>
<small id="postAuthor" class="form-text text-muted">' . $L['post-author-tip'] . '</small></div></div>';

//Set the Posts XML Data
$addCustomXmlDataHtml = '<div id="customXmlFields" class="form-group row d-none"><label for="customXmlFields" class="col-sm-2 col-form-label">' . $L['data-fields'] . '</label>
<div class="col-md-8"><div class="table-responsive">
<table id="customFieldsTable" class="table table-striped table-bordered table-hover">
<thead>
<tr>
<td class="text-left">' . $L['attribute'] . '</td>
<td class="text-left">' . $L['value'] . '</td>
<td></td>
</tr>
</thead>
<tbody></tbody>
<tfoot>
<tr>
<td colspan="6"></td>
<td class="text-left"><button type="button" onclick="addCustomXMLField();" data-toggle="tooltip" title="' . $L['add-field'] . '" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
</tr>
</tfoot>
</table>
</div><small id="customxmlFieldsTip" class="form-text text-muted">' . $L['xml-data-fields-tip'] . '</small></div></div>';

$xmlFeedFieldsScript = "<script><!--
var xml_feed_row = 0;

function addCustomXMLField() {
	html  = '<tr id=\"xmlElement-row' + xml_feed_row + '\">';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"xml_feed_values[' + xml_feed_row + '][attribute]\" value=\"\" placeholder=\"" . $L['attribute'] . "\" class=\"form-control\" /></td>';
    html += '  <td class=\"text-right\"><select id=\"customFieldSelect\" class=\"form-control shadow-none\" style=\"width: 100%; height:36px;\" name=\"xml_feed_values[' + xml_feed_row + '][value]\" aria-label=\"Custom Fields\">" . $xmlFieldsSelectionHtml . "</select></td>';
	html += '  <td class=\"text-left\"><button type=\"button\" onclick=\"$(\'#xmlElement-row' + xml_feed_row + '\').remove();\" data-toggle=\"tooltip\" title=\"" . $L['remove'] . "\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';

	$('#customFieldsTable tbody').append(html);
	
	xml_feed_row++;
}
//--></script>" . PHP_EOL;

//Search and replace HTML data
$searchReplaceFieldsHtml = '<div id="searchReplace" class="form-group row d-none"><label for="searchReplaceFields" class="col-sm-2 col-form-label">' . $L['search-and-replace'] . '</label>
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

self::AddFooterCode( $searchReplaceFieldsScript );
self::AddFooterCode( $xmlFeedFieldsScript );

//Set the Custom Regex HTML Data
$addCustomRegexHtml = '<div id="customFields" class="form-group row d-none"><label for="customFields" class="col-sm-2 col-form-label">' . $L['custom-fields'] . '</label>
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

$addSearchPostAuthorCode = '<script>
$(document).ready(function()
{
	var storeSelect = $("#storeSelect").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "' . AJAX_ADMIN_PATH . 'get-stores/",
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
			var html = data.text;
			return html;
		}
	});
	
	var storeSelect2 = $("#storeSelect2").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "' . AJAX_ADMIN_PATH . 'get-stores/",
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
			var html = data.text;
			return html;
		}
	});
	
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

$this->AddFooterCode( $addCustomFieldScript );
$this->AddFooterCode( $addSearchPostAuthorCode );

//Create the Add Source HTML Form
$form = array
(
	'add-source' => array
	(
		'title' => $L['source-settings'],
		'data' => array(
		
			'source-selection' => array( 
				'title' => null, 'data' => array
				(
					'source-title'=>array('label'=>$L['title'], 'type'=>'text', 'name' => 'title', 'value' =>null, 'required' => true, 'tip'=>$L['the-title-how-it-appears']),
					'source-url'=>array('label'=>$L['source-url'], 'type'=>'text', 'name' => 'url', 'value' =>null, 'required' => true, 'tip'=>$L['auto-source-url-tip'], 'placeholder' => 'https://site.com/feed/'),
					
					'source-type'=>array( 'label'=>$L['source-type'], 'type'=>'select', 'name' => 'source_type', 'value'=>null, 'firstNull' => true, 'data' => $sourceTypeArray, 'tip'=>$L['source-type-tip'] ),
					
					'multi-link-wrapper'=>array('label'=>$L['feed-url-wrapper'], 'type'=>'text', 'name' => 'feed_url_wrapper', 'value' =>null, 'tip'=>$L['feed-url-wrapper-tip'], 'placeholder' => '<div class=&quot;source&quot;><a href=&quot;(.*?)&quot;>Source</a></div>', 'dnone' => true, 'div-id' => 'feedUrlWrapper' ),
					
					'multi-tip'=>array('label'=>null, 'name' =>null, 'type'=>'custom-html', 'value'=>$multiSourcesTip, 'tip'=>null ),
				)
			),
			
			'xml-settings' => array( 
				'title' => $L['xml-settings'], 'dnone' => true, 'div-id' => 'xmlSettingsDiv', 'data' => array
				(
					'xml-type'=>array( 'label'=>$L['xml-file-type'], 'type'=>'select', 'name' => 'xml_type', 'value'=>null, 'firstNull' => false, 'data' => $sourceXmlTypeArray, 'tip'=>$L['xml-file-type-tip'] ),
					
					'copy-xml-locally'=>array('label'=>$L['copy-xml-file-locally'], 'type'=>'num', 'name' => 'copy_xml_locally', 'value'=>0, 'tip'=>$L['copy-xml-file-locally-tip'], 'min'=>'0' ),
					
					'store'=>array('label'=>null, 'name' =>null, 'type'=>'custom-html', 'value'=>$selectStoreHtml, 'tip'=>null),

					'items-wrapper'=>array('label'=>$L['items-wrapper'], 'type'=>'text', 'name' => 'xml_items_wrapper', 'value' =>null, 'tip'=>$L['xml-items-wrapper-tip'], 'placeholder' => 'channel', 'dnone' => true, 'div-id' => 'xmlItemsWrapper' ),
					
					'single-item-wrapper'=>array('label'=>$L['single-item-wrapper'], 'type'=>'text', 'name' => 'xml_item_wrapper', 'value' =>null, 'tip'=>$L['xml-single-item-wrapper-tip'], 'placeholder' => 'item', 'dnone' => true, 'div-id' => 'xmlSingleItemWrapper' ),
					
					'data-values'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>$addCustomXmlDataHtml, 'tip'=>null )
				)
			),
			
			'source-settings' => array( 
				'title' => $L['source-settings'], 'dnone' => true, 'div-id' => 'sourceSettingDiv', 'data' => array
				(
					'avoid-words'=>array('label'=>$L['words-to-avoid'], 'type'=>'text', 'name' => 'avoid_words', 'value' =>null, 'tip'=>$L['words-to-avoid-tip'], 'placeholder' => 'word1, word2, word3' ),
					
					'required-words'=>array('label'=>$L['required-words'], 'type'=>'text', 'name' => 'required_words', 'value' =>null, 'tip'=>$L['required-words-tip'], 'placeholder' => 'word1, word2, word3', 'dnone' => true),
					
					'source-title-tags'=>array('label'=>$L['source-title-keywords-to-tags'], 'type'=>'num', 'name' => 'title_to_tags', 'value'=>0, 'tip'=>$L['source-title-keywords-to-tags-tip'], 'min'=>'0', 'max'=>'5'),
					
					'copy-images-locally'=>array('label'=>$L['copy-images-from-content-locally'], 'type'=>'checkbox', 'name' => 'copy_images', 'value' =>null, 'tip'=>$L['copy-images-from-content-locally-tip']),
					
					'skip-post-without-images'=>array('label'=>$L['skip-posts-that-dont-have-images'], 'type'=>'checkbox', 'name' => 'skip_posts_without_images', 'value' =>null, 'tip'=>$L['skip-posts-that-dont-have-images-tip'] ),
					
					'remove-images'=>array('label'=>$L['remove-images-from-content'], 'type'=>'checkbox', 'name' => 'remove_images', 'value' =>null, 'tip'=>null),
					
					'set-cover-image'=>array('label'=>$L['set-the-first-image-as-cover-image'], 'type'=>'checkbox', 'name' => 'set_cover', 'value' =>null, 'tip'=>null),
					
					'use-original-date'=>array('label'=>$L['use-original-date-if-possible'], 'type'=>'checkbox', 'name' => 'set_original_date', 'value' =>null, 'tip'=>null),
					
					'set-source-link'=>array('label'=>$L['set-source-link-at-the-end-of-the-post'], 'type'=>'checkbox', 'name' => 'set_source', 'value' =>null, 'tip'=>null),

					'strip-html'=>array('label'=>$L['strip-html-from-content'], 'type'=>'checkbox', 'name' => 'strip_html', 'value' =>null, 'tip'=>$L['strip-html-from-content-tip']),
					
					'remove-links'=>array('label'=>$L['strip-links-from-the-post'], 'type'=>'checkbox', 'name' => 'strip_links', 'value' =>null, 'tip'=>$L['strip-links-from-the-post-tip']),

					'enable-auto-deletion'=>array('label'=>$L['enable-post-auto-deletion-feature-days'], 'type'=>'num', 'name' => 'auto_deletion', 'value'=>0, 'tip'=>$L['enable-post-auto-deletion-feature-days-tip'], 'min'=>'0', 'max'=>'99'),
					
					'max-posts'=>array('label'=>$L['max-posts'], 'type'=>'num', 'name' => 'max_posts', 'value'=>0, 'tip'=>$L['max-posts-tip'], 'min'=>'0', 'max'=>'99', 'dnone' => true, 'div-id' => 'maxPostsSelection'),
					
					'skip-posts'=>array('label'=>$L['skip-posts-older-than-days'], 'type'=>'num', 'name' => 'skip_older_posts', 'value'=>0, 'tip'=>$L['skip-posts-older-than-days-tip'], 'min'=>'0', 'max'=>'99', 'dnone' => true, 'div-id' => 'skipPostsSelection'),
					
					'search-replace'=>array('label'=>$L['search'], 'name' => 'search_replace', 'type'=>'custom-html', 'value'=>$searchReplaceFieldsHtml, 'tip'=>null )
				)
			),

			'post-settings' => array( 
				'title' => $L['post-settings'], 'dnone' => true, 'div-id' => 'postSettingDiv', 'data' => array
				(
					'post-type'=>array('label'=>$L['post-type'], 'type'=>'select', 'name' => 'post_type', 'value'=>null, 'tip'=>$L['post-type-tip'], 'firstNull' => false, 'data' => $setPostType ),
					'post-status'=>array('label'=>$L['status'], 'type'=>'select', 'name' => 'post_status', 'value'=>null, 'tip'=>$L['post-status-tip'], 'firstNull' => false, 'data' => $setPostStatus ),
					'source-category'=>array( 'label'=>$L['category'], 'type'=>'select-group-multi', 'name' => 'category', 'value'=>null, 'firstNull' => true, 'data' => $_categories, 'tip'=>$L['source-category-tip'] ),
					'auto-category'=>array('label'=>$L['auto-add-categories'], 'name' => 'auto_category', 'type'=>'custom-html', 'value'=>$autoCategorySelectionHtml, 'tip'=>$L['auto-add-categories-tip'] ),
					'post-author'=>array('label'=>$L['post-author'], 'name' => 'post_author', 'type'=>'custom-html', 'value'=>$contentAuthorHtml, 'tip'=>$L['post-template-tip'] ),
					'store2'=>array('label'=>null, 'name' =>null, 'type'=>'custom-html', 'value'=>$selectStoreHtml2, 'tip'=>null),
					'post-template'=>array('label'=>$L['post-template'], 'name' => 'post_template', 'type'=>'textarea', 'value'=>$defaultTmlptValue, 'tip'=>$L['post-template-tip'], 'buttons' =>  $postTmpltButtons ),
				)
			),
			
			'advanced-settings' => array( 
				'title' => $L['advanced-crawl-settings'], 'dnone' => true, 'div-id' => 'advCrawlSettingDiv', 'tip' =>$L['advanced-crawl-settings-tip'], 'data' => array
				(
					'crawl-as'=>array('label'=>$L['crawl-as'], 'type'=>'select', 'name' => 'crawl_as', 'value'=>null, 'tip'=>$L['crawl-as-tip'], 'firstNull' => false, 'data' => $scrapeDataSelection ),
					'rotate-ip-address'=>array('label'=>$L['rotate-ip-address-and-user-agent-to-scrape-data'], 'type'=>'checkbox', 'name' => 'rotate_ip_address', 'value' =>null, 'tip'=>$L['rotate-ip-address-and-user-agent-to-scrape-data-tip']),
					'hr'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					'post-title'=>array('label'=>$L['title'], 'type'=>'text', 'name' => 'regex_title', 'value' =>null, 'placeholder' => 'e.g.  &lt;meta property=&quot;og:title&quot; content=&quot;([A-Za-z0-9-]+)&quot; /&gt; ', 'tip'=>null),
					'post-description'=>array('label'=>$L['description'], 'type'=>'text', 'name' => 'regex_descr', 'value' =>null, 'placeholder' => 'e.g. &lt;div id=&quot;description&quot;&gt;(.*)&lt;/div&gt;', 'tip'=>null),
					'post-image'=>array('label'=>$L['featured-image'], 'type'=>'text', 'name' => 'regex_image', 'value' =>null, 'placeholder' => 'e.g. &lt;img.+src=[\'&quot;]([^\'&quot;]+)[\'&quot;].*&gt;', 'tip'=>null),
					'post-content'=>array('label'=>$L['content'], 'type'=>'text', 'name' => 'regex_content', 'value' =>null, 'placeholder' => 'e.g. &lt;div class=&quot;content&quot;&gt;(.*)&lt;/div&gt;', 'tip'=>null),
					
					'post-tags-container'=>array('label'=>$L['tags-container'], 'type'=>'text', 'name' => 'regex_tags_container', 'value' =>null, 'placeholder' => 'e.g. <div>(.*)</div>', 'tip'=>$L['tags-container-tip']),
					
					'post-tags'=>array('label'=>$L['tags'], 'type'=>'text', 'name' => 'regex_tags', 'value' =>null, 'placeholder' => 'e.g. &lt;div class=&quot;tags&quot;&gt;(.*)&lt;/div&gt;', 'tip'=>null),
					
					'post-author'=>array('label'=>$L['post-author'], 'name' => 'post_author', 'type'=>'custom-html', 'value'=>$addCustomRegexHtml, 'tip'=>$L['post-template-tip'] ),
				)
			)
		)
	)
);

unset( $_categories, $sourceExtraData, $editDefaultTmpl, $sourceRegex, $sourceSearchReplace, $sourceCustomField, $sourceRegexCustomFields );