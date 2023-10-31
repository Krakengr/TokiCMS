<script type="application/javascript">
$(document).ready(function()
{
	//Check for changes in XML type selection
	$("#xml-type").change(function()
	{
		var id = $(this).val();
		id = id.trim();
		
		$('#customXmlFields').addClass('d-none');
		$('#searchReplace').addClass('d-none');
		$('#customFields').addClass('d-none');
		$('#postSettingDiv').addClass('d-none');
		$('#advCrawlSettingDiv').addClass('d-none');
		$('#sourceSettingDiv').addClass('d-none');
		$('#xmlSingleItemWrapper').addClass('d-none');
		$('#xmlItemsWrapper').addClass('d-none');
		$('#maxPostsSelection').addClass('d-none');
		$('#skipPostsSelection').addClass('d-none');
		$('#feedUrlWrapper').addClass('d-none');
		$('#storeSelection').addClass('d-none');
		$('#storeSelection2').addClass('d-none');
		$('#storeSelection').val(null).trigger('change');
		$('#storeSelection2').val(null).trigger('change');

		if ( id == "feed" )
		{
			$('#customXmlFields').removeClass('d-none');
			$('#xmlSingleItemWrapper').removeClass('d-none');
			$('#xmlItemsWrapper').removeClass('d-none');
			$('#storeSelection').removeClass('d-none');
		}
		
		else if ( ( id == "index" ) || ( id == "sitemap" ) )
		{
			$('#searchReplace').removeClass('d-none');
			$('#customFields').removeClass('d-none');
			$('#postSettingDiv').removeClass('d-none');
			$('#advCrawlSettingDiv').removeClass('d-none');
			$('#sourceSettingDiv').removeClass('d-none');
			$('#storeSelection2').removeClass('d-none');
			$('#maxPostsSelection').removeClass('d-none');
			$('#skipPostsSelection').removeClass('d-none');
		}
	});
	
	//Check for changes in source type selection
	$("#source-type").change(function()
	{
		$('#customXmlFields').addClass('d-none');
		$('#searchReplace').addClass('d-none');
		$('#customFields').addClass('d-none');
		$('#postSettingDiv').addClass('d-none');
		$('#advCrawlSettingDiv').addClass('d-none');
		$('#xmlSettingsDiv').addClass('d-none');
		$('#sourceSettingDiv').addClass('d-none');
		$('#multiSourcesTip').addClass('d-none');
		$('#maxPostsSelection').addClass('d-none');
		$('#feedUrlWrapper').addClass('d-none');
		$('#skipPostsSelection').addClass('d-none');
		$('#xml-type').prop('selectedIndex', 0);
		$('#storeSelection').addClass('d-none');
		$('#storeSelection').val(null).trigger('change');
		$('#storeSelection2').addClass('d-none');
		$('#storeSelection2').val(null).trigger('change');

		var id = $(this).val();
		id = id.trim();

		if ( ( id === "rss" ) || ( id === "html" ) || ( id === "xml" ) )
		{
			if ( id !== "html" )
			{
				$('#maxPostsSelection').removeClass('d-none');
				$('#skipPostsSelection').removeClass('d-none');
			}
			
			if ( id === "xml" )
			{
				$('#xmlSettingsDiv').removeClass('d-none');
				$('#storeSelection').removeClass('d-none');
			}
			
			$('#storeSelection2').removeClass('d-none');
			$('#searchReplace').removeClass('d-none');
			$('#customFields').removeClass('d-none');
			$('#postSettingDiv').removeClass('d-none');
			$('#advCrawlSettingDiv').removeClass('d-none');
			$('#sourceSettingDiv').removeClass('d-none');
		}
		
		else if ( id === "multi" )
		{
			$('#multiSourcesTip').removeClass('d-none');
			$('#feedUrlWrapper').removeClass('d-none');
		}
	});
});
</script>