<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Edit Store Form
#
#####################################################
global $Admin, $Store;

$id 			= (int) Router::GetVariable( 'key' );
$Store 			= GetStore( $id, $this->GetSite() );
$defaultImage 	= HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS . 'default.svg';

$scrapeDataSelection = array(
	'normal' 	=> array( 'name' => 'normal', 'title'=> __( 'normal-crawl' ), 'disabled' => false, 'data' => array() ),
	'desktop' 	=> array( 'name' => 'desktop', 'title'=> __( 'crawl-as-googlebot-desktop' ), 'disabled' => false, 'data' => array() ),
	'mobile' 	=> array( 'name' => 'mobile', 'title'=> __( 'crawl-as-googlebot-mobile' ), 'disabled' => false, 'data' => array() )
);

$StoreImage = null;

if ( $Store && !empty( $Store['data']['id_image'] ) )
{
	$StoreImage = GetMainImageUrl( $Store['data']['id_image'] );
}

$Atts 		= AdminGetStoreAttributes( $this->siteID, $this->langID );
$postAtts 	= AdminGetAttributes( $this->GetSite(), $this->GetLang(), $this->GetBlog(), null );
$jsonData 	= ( !empty( $Store['data']['json_data'] ) ? Json( $Store['data']['json_data'] ) : null );

$customFieldsSelectionPre = array(
	'price-old' => array( 'name' => __( 'regular-price' ) . ' (' . __( 'price-old' ) . ')', 'value' => 'price-old' ),
	'current-price' => array( 'name' => __( 'current-price' ) . ' (' . __( 'discount-price' ) . ')', 'value' => 'current-price' ),
	'discount' => array( 'name' => __( 'percent' ) . ' (' . __( 'discount' ) . ')', 'value' => 'discount' ),
	'discount-start' => array( 'name' => __( 'discount-start-date' ), 'value' => 'discount-start' ),
	'discount-end' => array( 'name' => __( 'discount-end-date' ), 'value' => 'discount-end' ),
	'in-stock' => array( 'name' => __( 'in-stock' ), 'value' => 'in-stock' ),
	'title' => array( 'name' => __( 'title' ), 'value' => 'title' )
);

$customUrlFieldsSelectionHtml  = '<option value="custom">' . __( 'custom' ) . '</option>';

if ( !empty( $postAtts ) )
{
	$customUrlFieldsSelectionHtml .= '<optgroup label="&nbsp;' . __( 'post-attributes' ) . '">';
	
	foreach( $postAtts as $postAtt )
	{
		$customUrlFieldsSelectionHtml .= '<option value="' . $postAtt['id'] . '">' . $postAtt['name'] . ' (' . $postAtt['gn'] . ')</option>';
	}
	
	$customUrlFieldsSelectionHtml .= '</optgroup>';
}

$customFieldsSelectionHtml = '<optgroup label="&nbsp;' . __( 'predefined-keys' ) . '">';

//There are a few predefined keys from the db
foreach( $customFieldsSelectionPre as $cus => $cusDt )
	$customFieldsSelectionHtml .= '<option value="' . $cusDt['value'] . '">' . $cusDt['name'] . '</option>';

$customFieldsSelectionHtml .= '</optgroup>';

if ( !empty( $Atts ) )
{
	$customFieldsSelectionHtml .= '<optgroup label="&nbsp;' . __( 'stores-attributes' ) . '">';
	
	foreach( $Atts as $att )
	{
		$attTrans = ( !empty( $att['trans_data'] ) ? Json( $att['trans_data'] ) : null );
		$attName = ( ( !empty( $attTrans ) && ( $att['id_lang'] != $this->langID ) && isset( $attTrans['lang-' . $this->langID] ) ) ? $attTrans['lang-' . $this->langID]['value'] : $att['name'] );
		
		$customFieldsSelectionHtml .= '<option value="att::' . $att['id'] . '">' . stripslashes( $attName ) . '</option>';
	}

	$customFieldsSelectionHtml .= '</optgroup>';
}

$lastId 	= 0;
$lastJsonId = 0;

$uploadHtml = '
<div class="container">
	<div class="row">
		<div class="col-lg-4 col-sm-12 p-0 pr-2">';

		$uploadHtml .= HiddenFormInput( array( 'name' => 'logoFile', 'value' => $StoreImage ? $Store['data']['id_image'] : '' ), false );
		
		$args = array(
				'id' => 'buttonRemoveLogo',
				'title' => '<i class="fa fa-trash"></i> ' . __( 'remove-logo' ),
				'class' => 'btn-danger float-right' . ( $StoreImage ? '' : ' d-none' )
			
		);
			
		$uploadHtml .= Button( $args, false );
		
		$uploadHtml .= '
			<button type="button" class="btn btn-primary float-left" data-toggle="modal" data-target="#addImage" id="featuredImageModal"><i class="far fa-image"></i> ' . __( 'add-media' ) . '</button>
		</div>
		<div class="col-lg-8 col-sm-12 p-0 text-center">
			<img id="logoPreview" width="400" class="img-fluid img-thumbnail" alt="' . __( 'featured-image' ) . ' ' . __( 'preview' ). '" src="' . ( !$StoreImage ? $defaultImage : $StoreImage ) . '" />
		</div>
	</div>
</div>';

$checkHtml = '
<div class="form-check">
	<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
	<label class="form-check-label" for="deleteCheckBox">
		' . __( 'delete' ) . '
	</label>
	<small id="deleteCheckBoxHelp" class="form-text text-muted">' . __( 'delete-store-tip' ) . '</small>
</div>';

//Set the Custom Regex HTML Data
$addCustomRegexHtml = '<div class="form-group row"><label for="customFields" class="col-sm-2 col-form-label">' . __( 'custom-fields' ) . '</label>
<div class="col-md-12"><div class="table-responsive">
<table id="customFieldsTable" class="table table-striped table-bordered table-hover">
<thead>
<tr>
<th class="text-left col-sm-3">' . __( 'name' ). '</th>
<th class="text-left col-sm-3">' . __( 'field' ). '</th>
<th class="text-left col-sm-6">' . __( 'value' ). '</th>
<th class="text-left col-sm-2"></th>
</tr>
</thead>
<tbody>';

$jsonDataHtml = '
<div id="jsonDataDiv" class="' . ( empty( $Store['data']['retrieve_json_data'] ) ? 'd-none' : '' ) . '">
	<div class="form-group">
		<label for="jsonUrlInput">' . __( 'url' ) . '</label>
		<input value="' . ( !empty( $jsonData['url'] ) ? $jsonData['url'] : '' ) . '" type="text" class="form-control" name="json_url" id="jsonUrlInput" placeholder="http://site.com/api/exampe.json?data={{data-id}}&id=posts">
	</div>
	
	<div class="form-group row">
		<label for="customJsonFieldsTable" class="col-sm-2 col-form-label">' . __( 'url-fields' ) . '</label>
		<div class="col-md-12">
			<div class="table-responsive">
				<table id="customJsonFieldsTable" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="text-left col-sm-3">' . __( 'name' ). '</th>
							<th class="text-left col-sm-3">' . __( 'field' ). '</th>
							<th class="text-left col-sm-3">' . __( 'key' ). '</th>
							<th class="text-left col-sm-6">' . __( 'value' ). '</th>
							<th class="text-left col-sm-2"></th>
						</tr>
					</thead>
					<tbody>';
					
					if ( !empty( $jsonData['values'] ) )
					{
						foreach( $jsonData['values'] as $vid => $vdt )
						{
							$jsonDataHtml .= '
							<tr id="customJsonField-row' . $vid . '">
								<td class="text-right"><input type="text" name="json_fields[' . $vid . '][name]" value="' . $vdt['name'] . '" placeholder="Custom Value" class="form-control" /></td>
								<td class="text-right"><select id="customJsonSelect" class="form-select shadow-none" style="width: 100%; height:36px;" name="json_fields[' . $vid . '][field]" aria-label="Custom Fields">
								<option value="custom"' . ( ( $vdt['field'] == 'custom' ) ? '' : ' selected' ) . '>' . __( 'custom' ) . '</option>';
								
								if ( !empty( $postAtts ) )
								{
									$jsonDataHtml .= '<optgroup label="&nbsp;' . __( 'post-attributes' ) . '">';
									
									foreach( $postAtts as $postAtt )
									{
										$jsonDataHtml .= '<option value="' . $postAtt['id'] . '"' . ( ( $vdt['field'] == $postAtt['id'] ) ? ' selected' : '' ) . '>' . $postAtt['name'] . ' (' . $postAtt['gn'] . ')</option>';
									}
									
									$jsonDataHtml .= '</optgroup>';
								}
								
								$jsonDataHtml .= '
								</select></td>
								<td class="text-right"><input type="text" class="form-control" name="json_fields[' . $vid . '][key]" id="jsonKeyInput" placeholder="data-id" value="' . $vdt['key'] . '"></td>
								<td class="text-right"><input type="text" class="form-control" name="json_fields[' . $vid . '][value]" id="jsonValueInput" placeholder="eg: latest-games" value="' . $vdt['value'] . '"' . ( ( $vdt['value'] == 'custom' ) ? '' : ' disabled' ) . '></td>
								<td class="text-left"><button type="button" onclick="$(\'#customJsonField-row' . $vid . '\').remove();" data-toggle="tooltip" title="' . __( 'remove' ) . '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
							</tr>';
						
							$lastJsonId++;
						}
					}
					
				$jsonDataHtml .= '
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6"></td>
							<td class="text-left"><button type="button" onclick="addCustomJsonField();" data-toggle="tooltip" title="' . __( 'add-custom-field' ) . '" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
						</tr>
					</tfoot>
				</table>
			</div>
			<small id="customJsonFieldsTip" class="form-text text-muted">' . __( 'json-url-custom-fields-tip' ) . '</small>
		</div>
	</div>
</div>';

if ( isset( $Store['pregs'] ) && !empty( $Store['pregs'] ) )
{
	foreach( $Store['pregs'] as $regId => $regData )
	{
		$addCustomRegexHtml .= '<tr id="customField-row' . $regId . '">';
		$addCustomRegexHtml .= '  <td class="text-right"><input type="text" name="custom_fields[' . $regId . '][name]" value="' . $regData['name'] . '" placeholder="' . __( 'price' ) . '" class="form-control" /></td>';
		$addCustomRegexHtml .= '  <td class="text-right">';
		
		//Rebuild the SELECT box
		$addCustomRegexHtml .= '<select class="form-select shadow-none" style="width: 100%; height:36px;" name="custom_fields[' . $regId . '][field]" aria-label="Custom Fields">';
		$addCustomRegexHtml .= '';//'<optgroup label="&nbsp;' . __( 'predefined-keys' ) . '">';

		//There are a few predefined keys from the db
		foreach( $customFieldsSelectionPre as $cus => $cusDt )
			$addCustomRegexHtml .= '<option value="' . $cusDt['value'] . '" ' . ( ( !empty( $regData ) && ( $regData['key_value'] == $cusDt['value'] ) ) ? 'selected' : '' ) . '>' . $cusDt['name'] . '</option>';

		//$addCustomRegexHtml .= '</optgroup>';
		
		/*
		if ( !empty( $Atts ) )
		{
			$addCustomRegexHtml .= '<optgroup label="&nbsp;' . __( 'stores-attributes' ) . '">';
			
			foreach( $Atts as $att )
				$addCustomRegexHtml .= '<option value="att::' . $att['id'] . '" ' . ( ( !empty( $regData ) && ( $regData['key_value'] == 'att::' . $att['id'] ) ) ? 'selected' : '' ) . '>' . stripslashes( $att['name'] ) . '</option>';

			$addCustomRegexHtml .= '</optgroup>';
		}
		*/
		
		$addCustomRegexHtml .= '</select>';
		
		$addCustomRegexHtml .= '</td>';
		
		$addCustomRegexHtml .= '  <td class="text-right"><textarea class="form-control" placeholder="&lt;span class=\'product-price-total\'&gt;(\d+\,?\d*)&lt;/span&gt;" name="custom_fields[' . $regId . '][value]" rows="3">' . EscapeRegex( $regData['reg_data'], true ) . '</textarea></td>';
		
		$addCustomRegexHtml .= '  <td class="text-left"><button type="button" onclick="$(\'#customField-row' . $regId . '\').remove();" data-toggle="tooltip" title="' . __( 'remove' ) . '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
		
		$addCustomRegexHtml .= '</tr>';
	}
	
	$stDb = $this->db->from( 
	null, 
	"SELECT id
	FROM `" . DB_PREFIX . "stores_data`
	ORDER BY id DESC
	LIMIT 1"
	)->single();
	
	$lastId = ( $stDb ? $stDb['id'] : 9999990 );
}

$addCustomRegexHtml .= '</tbody>
<tfoot>
<tr>
<td colspan="6"></td>
<td class="text-left"><button type="button" onclick="addCustomField();" data-toggle="tooltip" title="' . __( 'add-custom-field' ) . '" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
</tr>
</tfoot>
</table>
</div><small id="customFields" class="form-text text-muted">' . __( 'stores-custom-fields-tip' ) . '</small></div></div>';

$parentHtml = '
<div class="form-group row">
	<label class="col-sm-2 col-form-label" for="parentStore">' . __( 'parent' ) . '</label>
	<div class="col-md-10">
		<select class="form-control select2" data-placeholder="' . __( 'choose-store' ) . '" name="parentStore" id="parentStore">';
		
		if ( !empty( $Store['data']['id_parent'] ) )
		{
			$parentHtml .= '<option value="' . $Store['data']['id_parent'] . '">' . $Store['data']['pr'] . '</option>';
		}
		
		$parentHtml .= '</select>
		<small id="parentStoreHelp" class="form-text text-muted">' . __( 'parent-store-tip' ) . '</small>
	</div>
</div>';

$addCustomFieldScript = "<script type=\"text/javascript\">
$(document).ready(function()
{
	var parentStore = $('#parentStore').select2({
		placeholder: '',
		allowClear: true,
		theme: 'bootstrap4',
		minimumInputLength: 2,
		ajax: {
			type: 'POST',
			url: '" . AJAX_ADMIN_PATH . "get-stores/',
			data: function (params) {
				var query = {
					postSite: '" . $this->siteID . "',
					storeId: '" . Router::GetVariable( 'key' ) . "',
					parentOnly: true,
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
});
</script>" . PHP_EOL;

$addCustomFieldScript .= "
<script type=\"text/javascript\"><!--
var custom_row = " . ( $lastId + 1 ) . ";

function addCustomField() {
	html  = '<tr id=\"customField-row' + custom_row + '\">';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"custom_fields[' + custom_row + '][name]\" value=\"\" placeholder=\"" . __( 'price' ) . "\" class=\"form-control\" /></td>';
	html += '  <td class=\"text-right\"><select id=\"customFieldSelect\" class=\"form-select shadow-none\" style=\"width: 100%; height:36px;\" name=\"custom_fields[' + custom_row + '][field]\" aria-label=\"Custom Fields\">" . $customFieldsSelectionHtml . "</select></td>';
    html += '  <td class=\"text-right\"><textarea class=\"form-control\" placeholder=\"&lt;span class=\'product-price-total\'&gt;(\d+\.?\d*)&lt;/span&gt;\" name=\"custom_fields[' + custom_row + '][value]\" rows=\"3\"></textarea></td>';
	html += '  <td class=\"text-left\"><button type=\"button\" onclick=\"$(\'#customField-row' + custom_row + '\').remove();\" data-toggle=\"tooltip\" title=\"" . __( 'remove' ) . "\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';

	$('#customFieldsTable tbody').append(html);
	
	custom_row++;
}
//--></script>" . PHP_EOL;

$addCustomFieldScript .= "
<script type=\"text/javascript\"><!--
var custom_json_row = " . ( $lastJsonId + 1 ) . ";

function addCustomJsonField() {
	html  = '<tr id=\"customJsonField-row' + custom_json_row + '\">';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"json_fields[' + custom_json_row + '][name]\" value=\"\" placeholder=\"Custom Value\" class=\"form-control\" /></td>';
	html += '  <td class=\"text-right\"><select id=\"customJsonSelect\" class=\"form-select shadow-none\" style=\"width: 100%; height:36px;\" name=\"json_fields[' + custom_json_row + '][field]\" aria-label=\"Custom Fields\">" . $customUrlFieldsSelectionHtml . "</select></td>';
	html += '  <td class=\"text-right\"><input type=\"text\" class=\"form-control\" name=\"json_fields[' + custom_json_row + '][key]\" id=\"jsonKeyInput\" placeholder=\"data-id\"></td>';
    html += '  <td class=\"text-right\"><input type=\"text\" class=\"form-control\" name=\"json_fields[' + custom_json_row + '][value]\" id=\"jsonValueInput\" placeholder=\"eg: latest-games\"></td>';
	html += '  <td class=\"text-left\"><button type=\"button\" onclick=\"$(\'#customJsonField-row' + custom_json_row + '\').remove();\" data-toggle=\"tooltip\" title=\"" . __( 'remove' ) . "\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';

	$('#customJsonFieldsTable tbody').append(html);
	
	custom_json_row++;
}
//--></script>" . PHP_EOL;

$this->AddFooterCode( $addCustomFieldScript );

$attData = AdminGetStoresAttributesData( null, $id );

//Attributes Data HTML
$transHtml = '
<div class="form-group">
	<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<td class="text-left"><strong>' . __( 'attribute' ) . '</strong></td>
					<td class="text-right"><strong>' . __( 'value') . '</strong></a></td>
				</tr>
			</thead>
			<tbody>';

			if ( !empty( $Atts ) )
			{
				foreach( $Atts as $att )
				{
					$attTrans = ( !empty( $att['trans_data'] ) ? Json( $att['trans_data'] ) : null );
					
					$attName = ( ( !empty( $attTrans ) && ( $att['id_lang'] != $this->langID ) && isset( $attTrans['lang-' . $this->langID] ) ) ? $attTrans['lang-' . $this->langID]['value'] : $att['name'] );
						
					$transHtml .= '
					<tr>
						<td class="text-left"><strong>' . stripslashes( $attName ) . '</strong></td>
						<td class="text-right"><textarea class="form-control" id="' . $att['id'] . '" name="att[' . $att['id'] . ']" rows="2">' . ( ( !empty( $attData ) && isset( $attData[$att['id']] ) ) ? $attData[$att['id']]['value'] : '' ) . '</textarea></td>
					</tr>';
					
					//<input type="text" class="form-control" name="att[' . $att['id'] . ']" value="' . ( ( !empty( $attData ) && isset( $attData[$att['id']] ) ) ? $attData[$att['id']]['value'] : '' ) . '">
				}
			}
			
			$transHtml .= '
			</tbody>
		</table>
	</div>
</div>';

#####################################################
#
# Edit Store Form
#
#####################################################

$form = array
(
	'generic-settings' => array
	(
		'title' => __( 'edit-store' ),
		'col' => 12,
		'data' => array(
		
			'name' => array( 
				'title' => null, 'data' => array
				(
					'name'=>array('label'=>__( 'name' ), 'type'=>'text', 'name' => 'name', 'value' => htmlspecialchars( $Store['data']['name'] ), 'tip'=>__('the-title-how-it-appears' ) )
				)
			),
			
			'sef' => array( 
				'title' => null, 'data' => array
				(
					'sef'=>array('label'=>__( 'slug' ), 'type'=>'text', 'name' => 'sef', 'value' => htmlspecialchars( $Store['data']['sef'] ), 'tip'=>__('slug-tip' ) )
				)
			),
			
			'url' => array( 
				'title' => null, 'data' => array
				(
					'url'=>array('label'=>__( 'url' ), 'type'=>'text', 'name' => 'url', 'value' => htmlspecialchars( $Store['data']['url'] ), 'tip'=>__('slug-tip' ) )
				)
			),
			
			'description' => array( 
				'title' => null, 'data' => array
				(
					'description'=>array('label'=>__( 'description' ), 'type'=>'textarea', 'name' => 'description', 'value' => htmlspecialchars( $Store['data']['description'] ), 'tip'=>__('descr-tip' ) )
				)
			),
			
			'profile' => array( 
				'title' => null, 'data' => array
				(
					'profile'=>array('label'=>__( 'store-profile' ), 'type'=>'textarea', 'name' => 'post', 'rows' => '6', 'value' => htmlspecialchars( $Store['data']['post'] ), 'tip'=>__('store-profile-tip' ) )
				)
			),
			
			'parent' => array( 
				'title' => null, 'data' => array
				(
					'parent'=>array('label'=>null, 'name' => 'parent', 'type'=>'custom-html', 'value'=> $parentHtml, 'tip'=>null )
				)
			),
			
			'hr' => array(
				'title' => null, 'data' => array
				(
					'hr-logo'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false )
				)
			),
			
			'logo' => array(
				'title' => null, 'data' => array
				(
					'upload-logo'=>array('label'=>null, 'name' => 'manufacturer_image', 'type'=>'custom-html', 'value'=>$uploadHtml, 'tip'=>null, 'disabled' => false )
				)
			),
			
			'hr' => array(
				'title' => null, 'data' => array
				(
					'hr'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false )
				)
			),
			
			'delete' => array(
				'title' => null, 'data' => array
				(
					'delete'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>$checkHtml, 'tip'=>null, 'disabled' => false )
				)
			)
		)
	),
	
	'crawl-settings' => array
	(
		'title' => __( 'crawl-settings' ),
		'data' => array(
			'crawl-settings' => array(
				'title' => null, 'tip' => __( 'store-crawl-settings-tip' ), 'data' => array
				(
					'crawl-as'=>array('label'=>__( 'crawl-as' ), 'type'=>'select', 'name' => 'crawl_as', 'value'=>$Store['data']['scrape_as'], 'tip'=>__( 'crawl-as-tip' ), 'firstNull' => false, 'data' => $scrapeDataSelection ),
					'rotate-ip-address'=>array('label'=>__( 'rotate-ip-address-and-user-agent-to-scrape-data' ), 'type'=>'checkbox', 'name' => 'rotate_ip_address', 'value' => ( !empty( $Store['data']['rotate_ip'] ) ? true : false ), 'tip'=> __( 'rotate-ip-address-and-user-agent-to-scrape-data-tip' ) ),
					
					'hr0'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'retrieve-json-data-from-an-api'=>array('label'=>__( 'retrieve-json-data-from-an-api' ), 'type'=>'checkbox', 'name' => 'retrieve_json_data', 'value' => ( !empty( $Store['data']['retrieve_json_data'] ) ? true : false ), 'tip'=> __( 'rotate-ip-address-and-user-agent-to-scrape-data-tip' ) ),
					
					'json-div'=>array('label'=>null, 'name' => 'json_div', 'type'=>'custom-html', 'value'=>$jsonDataHtml, 'tip'=>null, 'disabled' => false ),
					
					'hr1'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'crawl-settings'=>array('label'=>null, 'name' => 'settings', 'type'=>'custom-html', 'value'=>$addCustomRegexHtml, 'tip'=>null ),
				)
			)
		)
	),
	
	'stores-attributes' => array
	(
		'title' => __( 'stores-attributes' ),
		'data' => array(
			'other-settings' => array(
				'title' => null, 'tip' => null, 'data' => array
				(
					'attributes'=>array('label'=>null, 'name' => 'stores-attributes', 'type'=>'custom-html', 'value'=>$transHtml, 'tip'=>null ),
				)
			)
		)
	)
);