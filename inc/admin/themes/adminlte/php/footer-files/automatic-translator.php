<script type="application/javascript">
$(document).ready(function()
{
	$("#enable-auto-translate").click(function(e)
	{
		if( $(this).is(':checked') )
		{
			$('#selectLanguages').removeClass("d-none");
			$('#checkLanguagesTables').addClass("d-none");
		}
		
		else
		{
			$('#selectLanguages').addClass("d-none");
			$('#checkLanguagesTables').removeClass("d-none");
		}
	});
});
</script>