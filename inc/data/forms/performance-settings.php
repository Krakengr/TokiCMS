<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Performance Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

$site = $this->adminSettings::Site();

$headerCode = Json( $settings['header_code'] );
$footerCode = Json( $settings['footer_code'] );
$instant = Json( $settings['instantpage_settings'] );

// Cache type Data Array
$cacheTypeArray = array(
	'normal' => array( 'name' => 'normal', 'title'=> $L['normal'], 'disabled' => ( ( $settings['enable_cache'] == 'true' ) ? false : true ), 'data' => array() ),
	'advanced' => array( 'name' => 'advanced', 'title'=> $L['advanced'], 'disabled' => ( ( $settings['enable_cache'] == 'true' ) ? false : true ), 'data' => array() ),
	'advanced-compress' => array( 'name' => 'advanced-compress', 'title'=> $L['advanced-compress'], 'disabled' => ( ( $settings['enable_cache'] == 'true' ) ? false : true ), 'data' => array() ),
);

#####################################################
#
# Header Form array
#
#####################################################

//Add Code in header Form
$addHeaderCodeFormHtml = '
<div class="form-group row">
	<div class="col-md-10">
		<div class="table-responsive">
			<table id="addHeaderCodeTable" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<td class="text-left" style="width: 60%">' . __( 'code' ) . '</td>';

if ( $this->MultiLang() )
{
	$addHeaderCodeFormHtml .= '<td class="text-left" style="width: 40%">' . __( 'langs' ) . '</td>';
}

$addHeaderCodeFormHtml .= '
<td></td>
</tr>
</thead>
<tbody>';

$langsHtmlCode = null;

if ( $this->MultiLang() )
{
	$langs = $this->adminSettings::AllLangs();

	if ( !empty( $langs ) )
	{
		$langsHtmlCode .= '<select class="form-control" name="header_code[\' + header_code_row + \'][language]" aria-label="Language select"><option value="0">' . __( 'everywhere' ) . '</option>';
			
		foreach ( $langs as $k => $lang )
		{
			$langsHtmlCode .= '<option value="' . $lang['lang']['id'] . '" ' . ( ( !$this->IsDefaultLang() && ( $this->GetLang() == $lang['lang']['id'] ) ) ? 'selected' : '' ) . '>' . sprintf( $L['show-in-s-lang'], $lang['lang']['title'] ) . '</option>';
		}

		$langsHtmlCode .= '</select>';
	}
}

if ( !empty( $headerCode ) )
{
	foreach( $headerCode as $headerCodeID => $headerCodeSinge )
	{
		$addHeaderCodeFormHtml .= '<tr id="headerCode-row' . $headerCodeID . '">';
		$addHeaderCodeFormHtml .= '<td class="text-right"><textarea class="form-control" rows="3" name="header_code[' . $headerCodeID . '][code]" placeholder="Enter...">' . html_entity_decode( $headerCodeSinge['code'] ) . '</textarea></td>';
		
		if ( $this->MultiLang() )
		{
			$addHeaderCodeFormHtml .= '<td class="text-right"><select class="form-control" name="header_code[' . $headerCodeID . '][language]" aria-label="Language select"><option value="0" ' . ( ($headerCodeSinge['language'] == '0' ) ? 'selected' : '' ) . '>' . __( 'everywhere' ) . '</option>';
			
			if ( !empty( $langs ) )
			{
				foreach ( $langs as $k => $lang )
				{
					$addHeaderCodeFormHtml .= '<option value="' . $lang['lang']['id'] . '" ' . ( ($headerCodeSinge['language'] == $lang['lang']['id'] ) ? 'selected' : '' ) . '>' . sprintf( $L['show-in-s-lang'], $lang['lang']['title'] ) . '</option>';
				}
			}
			
			$addHeaderCodeFormHtml .= '</select></td>';
		}
		
		$addHeaderCodeFormHtml .= '<td class="text-left"><button type="button" onclick="$(\'#headerCode-row' . $headerCodeID . '\').remove();" data-toggle="tooltip" title="' . $L['remove'] . '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
		$addHeaderCodeFormHtml .= '</tr>' . PHP_EOL;
	}
}

$addHeaderCodeFormHtml .= '</tbody>
<tfoot>
<tr>
<td colspan="6"></td>
<td class="text-left"><button type="button" onclick="addHeaderCodeField();" data-toggle="tooltip" title="' . __( 'add-new-field' ) . '" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
</tr>
</tfoot>
</table>
</div><small id="addHeaderCodeTable" class="form-text text-muted">' . __( 'add-header-code-tip' ) . '</small></div></div>';

$addHeaderCodeScript = "<script><!--
var header_code_row = " . ( !empty( $headerCode ) ? ( count( $headerCode ) + 1 ) : 0 ) . ";

function addHeaderCodeField() {
	html  = '<tr id=\"headerCode-row' + header_code_row + '\">';
    html += '  <td class=\"text-right\"><textarea class=\"form-control\" rows=\"3\" name=\"header_code[' + header_code_row + '][code]\" placeholder=\"Enter...\"></textarea></td>';
    html += '  " . ( $langsHtmlCode ? "<td class=\"text-right\">" . $langsHtmlCode . "</td>" : '' ) . "';
	html += '  <td class=\"text-left\"><button type=\"button\" onclick=\"$(\'#headerCode-row' + header_code_row + '\').remove();\" data-toggle=\"tooltip\" title=\"" . $L['remove'] . "\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';

	$('#addHeaderCodeTable tbody').append(html);
	
	header_code_row++;
}
//--></script>" . PHP_EOL;

self::AddFooterCode( $addHeaderCodeScript );

#####################################################
#
# Footer Form array
#
#####################################################

//Add Code in the footer Form
$addFooterCodeFormHtml = '
<div class="form-group row">
	<div class="col-md-10">
		<div class="table-responsive">
			<table id="addFooterCodeTable" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<td class="text-left" style="width: 60%">' . __( 'code' ) . '</td>';

if ( $this->MultiLang() )
{
	$addFooterCodeFormHtml .= '<td class="text-left" style="width: 40%">' . __( 'langs' ) . '</td>';
}

$addFooterCodeFormHtml .= '
<td></td>
</tr>
</thead>
<tbody>';

$langsHtmlCode = null;

if ( $this->MultiLang() )
{
	//$langs = $this->adminSettings::AllLangs();

	if ( !empty( $langs ) )
	{
		$langsHtmlCode .= '<select class="form-control" name="footer_code[\' + footer_code_row + \'][language]" aria-label="Language select"><option value="0">' . __( 'everywhere' ) . '</option>';
			
			foreach ( $langs as $k => $lang )
			{
				$langsHtmlCode .= '<option value="' . $lang['lang']['id'] . '">' . sprintf( $L['show-in-s-lang'], $lang['lang']['title'] ) . '</option>';
			}
			
			$langsHtmlCode .= '</select>';
	}
}

if ( !empty( $footerCode ) )
{
	foreach( $footerCode as $footerCodeID => $footerCodeSinge )
	{
		$addFooterCodeFormHtml .= '<tr id="footerCode-row' . $footerCodeID . '">';
		$addFooterCodeFormHtml .= '<td class="text-right"><textarea class="form-control" rows="3" name="footer_code[' . $footerCodeID . '][code]" placeholder="Enter...">' . html_entity_decode( $footerCodeSinge['code'] ) . '</textarea></td>';
		
		if ( $this->MultiLang() )
		{
			$addFooterCodeFormHtml .= '<td class="text-right"><select class="form-control" name="footer_code[' . $footerCodeID . '][language]" aria-label="Language select"><option value="0" ' . ( ($footerCodeSinge['language'] == '0' ) ? 'selected' : '' ) . '>' . __( 'everywhere' ) . '</option>';
			
			if ( !empty( $langs ) )
			{
				foreach ( $langs as $k => $lang )
				{
					$addFooterCodeFormHtml .= '<option value="' . $lang['lang']['id'] . '" ' . ( ($footerCodeSinge['language'] == $lang['lang']['id'] ) ? 'selected' : '' ) . '>' . sprintf( $L['show-in-s-lang'], $lang['lang']['title'] ) . '</option>';
				}
			}
			
			$addFooterCodeFormHtml .= '</select></td>';
		}
		
		$addFooterCodeFormHtml .= '<td class="text-left"><button type="button" onclick="$(\'#footerCode-row' . $footerCodeID . '\').remove();" data-toggle="tooltip" title="' . $L['remove'] . '" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
		$addFooterCodeFormHtml .= '</tr>';
	}
}

$addFooterCodeFormHtml .= '</tbody>
<tfoot>
<tr>
<td colspan="6"></td>
<td class="text-left"><button type="button" onclick="addFooterCodeField();" data-toggle="tooltip" title="' . __( 'add-new-field' ) . '" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
</tr>
</tfoot>
</table>
</div><small id="addFooterCodeTable" class="form-text text-muted">' . __( 'add-footer-code-tip' ) . '</small></div></div>';

$addFooterCodeScript = "<script><!--
var footer_code_row = " . ( !empty( $footerCode ) ? ( count( $footerCode ) + 1 ) : 0 ) . ";

function addFooterCodeField() {
	html  = '<tr id=\"footerCode-row' + footer_code_row + '\">';
    html += '  <td class=\"text-right\"><textarea class=\"form-control\" rows=\"3\" name=\"footer_code[' + footer_code_row + '][code]\" placeholder=\"Enter...\"></textarea></td>';
    html += '  " . ( $langsHtmlCode ? "<td class=\"text-right\">" . $langsHtmlCode . "</td>" : '' ) . "';
	html += '  <td class=\"text-left\"><button type=\"button\" onclick=\"$(\'#footerCode-row' + footer_code_row + '\').remove();\" data-toggle=\"tooltip\" title=\"" . $L['remove'] . "\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';

	$('#addFooterCodeTable tbody').append(html);
	
	footer_code_row++;
}
//--></script>" . PHP_EOL;
/*
$addFooterCodeScript .= "<script>
$(document).ready(function()
{
	if($('#enableInstantLoad').is(':checked'))
	{
		$('#instantPcOnly').removeClass('d-none');
		$('#instantDelay').removeClass('d-none');
	}
	else
	{
		$('#instantPcOnly').addClass('d-none');
		$('#instantDelay').addClass('d-none');
	}
	
	$('#enableInstantLoad').click(function(event) {
		if(this.checked) {
			$('#instantPcOnly').removeClass('d-none');
			$('#instantDelay').removeClass('d-none');
		} else{
			$('#instantPcOnly').addClass('d-none');
			$('#instantDelay').addClass('d-none');
		}
	});
});
</script>" . PHP_EOL;*/

self::AddFooterCode( $addFooterCodeScript );

#####################################################
#
# Form array
#
#####################################################
$form = array
(
	'performance-settings' => array
	(
		'title' => $L['performance-settings'],
		'data' => array(

			'add-blank-icon' => array( 
				'title' => null, 'data' => array
				(
					'add-blank-icon'=>array('label'=>$L['add-blank-icon'], 'type'=>'checkbox', 'name' => 'settings[blank_icon]', 'value' => $settings['blank_icon'], 'tip'=>$L['add-blank-icon-tip'])
				)
			),

			'enable-cache' => array( 
				'title' => null, 'data' => array
				(
					'enable-cache'=>array('label'=>$L['enable-cache'], 'type'=>'checkbox', 'name' => 'settings[enable_cache]', 'value' => $settings['enable_cache'], 'tip'=>$L['enable-cache-tip'])
				)
			),
			
			'cache-type' => array( 
				'title' => null, 'data' => array
				(
					'cache-type'=>array('label'=>$L['cache-type'], 'type'=>'select', 'name' => 'settings[cache_type]', 'value' => $settings['cache_type'], 'tip'=>$L['cache-type-tip'], 'firstNull' => false, 'data' => $cacheTypeArray  )
				)
			),
			
			'cache-all-visitors' => array( 
				'title' => null, 'data' => array
				(
					'cache-visitors'=>array('label'=>$L['enable-caching-for-all-visitors'], 'type'=>'checkbox', 'name' => 'settings[cache_all_visitors]', 'value' => $settings['cache_all_visitors'], 'tip'=>$L['enable-caching-for-all-visitors-tip'])
				)
			),
			
			'cache-lifespan' => array( 
				'title' => null, 'data' => array
				(
					'num-posts'=>array('label'=>$L['cache-lifespan'], 'name' => 'settings[cache_time]', 'type'=>'num', 'value'=>$settings['cache_time'], 'tip'=>$L['cache-lifespan-tip'], 'min'=>'0', 'max'=>'999999'),
				)
			),
			
			'enable-lazyload' => array( 
				'title' => null, 'data' => array
				(
					'enable-lazyload'=>array('label'=>$L['enable-lazyloader'], 'type'=>'checkbox', 'name' => 'settings[enable_lazyloader]', 'value' => $settings['enable_lazyloader'], 'tip'=>$L['enable-lazyloader-tip'])
				)
			),
			
			'enable-preloading' => array( 
				'title' => null, 'data' => array
				(
					'enable-preloading'=>array('label'=>$L['enable-preloading'], 'type'=>'checkbox', 'name' => 'settings[enable_preloading]', 'value' => $settings['enable_preloading'], 'tip'=>$L['enable-preloading-tip'])
				)
			),
			
			'enable-instantpage' => array( 
				'title' => null, 'data' => array
				(
					'enable-instant-page-load'=>array('label'=>$L['enable-instant-page-load'], 'type'=>'checkbox', 'id' => 'enableInstantLoad', 'name' => 'settings[enable_instantpage]', 'value' => $settings['enable_instantpage'], 'tip'=>$L['enable-instant-page-load-tip']),
					
					//'enable-instant-page-mobile'=>array('label'=>$L['enable-on-pc-only'], 'type'=>'checkbox', 'div-id' => 'instantPcOnly', 'name' => 'instantpage[only_on_pc]', 'class' => 'd-none', 'value' => ( isset( $instant['only_on_pc'] ) ? $instant['only_on_pc'] : null ), 'tip'=>$L['enable-on-pc-only-tip'], 'dnone' => true ),
					
					//'instant-page-delay'=>array('label'=>$L['adjusting-the-delay-on-hover'], 'name' => 'instantpage[delay]', 'div-id' => 'instantDelay', 'type'=>'num', 'value'=>( isset( $instant['delay_time'] ) ? $instant['delay_time'] : 65 ), 'tip'=>$L['adjusting-the-delay-on-hover-tip'], 'min'=>'10', 'max'=>'1000', 'dnone' => true ),
				)
			),
			
			'instantpage-sett' => array( 
				'title' => null, 'data' => array
				(
					
				)
			)
		)
	),
	
	'add-header-code' => array
	(
		'title' => __( 'add-header-code' ),
		'data' => array
		(
			'add-header-code' => array( 
				'title' => null, 'data' => array
				(
					'header-code'=>array('label'=>null, 'name' => 'header-code', 'type'=>'custom-html', 'value'=>$addHeaderCodeFormHtml, 'tip'=>null ),
				)
			)
		)
	),
	
	'add-footer-code' => array
	(
		'title' => __( 'add-footer-code' ),
		'data' => array
		(
			'add-footer-code' => array( 
				'title' => null, 'data' => array
				(
					'header-code'=>array('label'=>null, 'name' => 'footer-code', 'type'=>'custom-html', 'value'=>$addFooterCodeFormHtml, 'tip'=>null ),
				)
			)
		)
	)
);