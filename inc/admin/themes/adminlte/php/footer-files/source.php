<script type="application/javascript">
$(document).ready(function()
{
	var storeSelect = $("#storeSelect").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "<?php echo AJAX_ADMIN_PATH ?>get-stores/",
			data: function (params) {
				var query = {
					postSite: "<?php echo $Admin->GetSite() ?>",
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
			url: "<?php echo AJAX_ADMIN_PATH ?>get-users/",
			data: function (params) {
				var query = {
					postSite: "<?php echo $Admin->GetSite() ?>",
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
				html += '<span class="badge badge-pill badge-light">'+data.type+'</span>';
			}
			return html;
		}
	});
});
</script>

<script><!--
var search_replace_row = <?php echo ( !empty( $sourceSearchReplace ) ? ( count( $sourceSearchReplace ) + 1 ) : 0 ) ?>;

function addSearchReplaceField() {
	html  = '<tr id=\"searchReplace-row' + search_replace_row + '\">';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"search_replace[' + search_replace_row + '][search]\" value=\"\" placeholder=\"<?php echo __( 'search' ) ?>\" class=\"form-control\" /></td>';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"search_replace[' + search_replace_row + '][replace]\" value=\"\" placeholder=\"<?php echo __( 'replace' ) ?>\" class=\"form-control\" /></td>';
	html += '  <td class=\"text-left\"><button type=\"button\" onclick=\"$(\'#searchReplace-row' + search_replace_row + '\').remove();\" data-toggle=\"tooltip\" title=\"<?php echo __( 'remove' ) ?>\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';

	$('#searchReplaceFieldsTable tbody').append(html);
	
	search_replace_row++;
}
//--></script>

<script><!--
var xml_feed_row = 0;

function addCustomXMLField() {
	html  = '<tr id=\"xmlElement-row' + xml_feed_row + '\">';
    html += '  <td class=\"text-right\"><input type=\"text\" name=\"xml_feed_values[' + xml_feed_row + '][attribute]\" value=\"\" placeholder=\"<?php echo __( 'attribute' ) ?>\" class=\"form-control\" /></td>';
    html += '  <td class=\"text-right\"><select id=\"customFieldSelect\" class=\"form-control shadow-none\" style=\"width: 100%; height:36px;\" name=\"xml_feed_values[' + xml_feed_row + '][value]\" aria-label=\"Custom Fields\"><?php echo $xmlFieldsSelectionHtmlFooter ?></select></td>';
	html += '  <td class=\"text-left\"><button type=\"button\" onclick=\"$(\'#xmlElement-row' + xml_feed_row + '\').remove();\" data-toggle=\"tooltip\" title=\"<?php echo __( 'remove' ) ?>\" class=\"btn btn-danger\"><i class=\"fa fa-minus-circle\"></i></button></td>';
	html += '</tr>';

	$('#customXmlFieldsTable tbody').append(html);
	
	xml_feed_row++;
}
//--></script>