<?php if ( ( $Admin->CurrentAction() == 'edit-post' ) && !$Post->IsPage() ) : ?>
<script type="application/javascript">
//Post main tags
var input = document.querySelector('#tags'),
	tagifyT = new Tagify(input, {
			whitelist:[],
			dropdown : {
				position		: "input",
				classname     	: "color-blue",
				enabled       	: 0, // show the dropdown immediately on focus
				maxItems      	: 10,
				fuzzySearch		: false,   	// match only suggestions that starts with the typed characters
				position      	: "text", 	// place the dropdown near the typed text
				closeOnSelect 	: true,  	// keep the dropdown open after selecting a suggestion
				highlightFirst	: true,
				userInput		: false
			}
		}
	),
controller;

$(document).on('click', '[data-tag-suggestion="true"]', function () {
	tagifyT.addTags([this.innerText]);
});

tagifyT.on('input', function(e){
	var val = e.detail.value; 
	tagifyT.whitelist = null; // reset the whitelist
	controller && controller.abort();
	controller = new AbortController();
		
	tagifyT.loading(true).dropdown.hide();
		
	$.post('<?php echo AJAX_ADMIN_PATH ?>tags/', 
		{ 'slug': val, 'psttp': 0, 'lang': '<?php echo $Admin->GetLang() ?>', 'site': '<?php echo $Admin->GetSite() ?>' }, 
			function( data ) {
			tagifyT.whitelist = data;
			tagifyT.loading(false).dropdown.show(val);
		}
	);
});
	
//Clean the whitelist
tagifyT.on('add', function(e){
	tagifyT.whitelist = [];
});
</script>

<script type="application/javascript">
<?php 
if ( !empty( $types ) ) :
	
	foreach( $types as $type ) : ?>
	
    $(document).on('click', '[data-cus<?php echo $type['id'] ?>-suggestion="true"]', function () {
		var id  = $(this).data('id');
		var tag = $(this).data('tag');
		
		tagify<?php echo $type['id'] ?>.addTags([this.innerText]);
    });

	//Custom post type tags 
	var input = document.querySelector('#customtags<?php echo $type['id'] ?>'),
		tagify<?php echo $type['id'] ?> = new Tagify(input, {
				whitelist:[],
				dropdown : {
					position		: "input",
					classname     	: "color-blue",
					enabled       	: 0, // show the dropdown immediately on focus
					maxItems      	: 10,
					fuzzySearch		: false,    // match only suggestions that starts with the typed characters
					position      	: "text", // place the dropdown near the typed text
					closeOnSelect 	: true, // keep the dropdown open after selecting a suggestion
					highlightFirst	: true,
					userInput		: false
				}
			}
		),
	controller;
	
	tagify<?php echo $type['id'] ?>.on('input', function(e){
		var val = e.detail.value; 
		tagify<?php echo $type['id'] ?>.whitelist = null; // reset the whitelist
		controller && controller.abort();
		controller = new AbortController();
		
		tagify<?php echo $type['id'] ?>.loading(true).dropdown.hide();
		
		$.post('<?php echo AJAX_ADMIN_PATH ?>tags/', 
			{ 'slug': val, 'psttp': <?php echo $type['id'] ?>, 'lang': '<?php echo $Admin->GetLang() ?>', 'site': '<?php echo $Admin->GetSite() ?>' }, 
				function( data ) {
				tagify<?php echo $type['id'] ?>.whitelist = data; // update whitelist Array in-place
				tagify<?php echo $type['id'] ?>.loading(false).dropdown.show(val); // render the suggestions dropdown
			}
		);
	});
	
	//Clean the whitelist
	tagify<?php echo $type['id'] ?>.on('add', function(e){
		tagify<?php echo $type['id'] ?>.whitelist = [];
	});
	
	<?php endforeach ?>
	
<?php endif ?>
</script>

<script type="application/javascript">
$(document).ready ( function () {

	$(document).on('click','.customType_check',function()
	{
		var cusList = [];
		var checkboxes = document.querySelectorAll('input[id=customType]:checked');
		
		for (var i = 0; i < checkboxes.length; i++) {
			cusList.push(checkboxes[i].value)
		}
	
		var id 			= $(this).data('id');
		var parent 		= $(this).data('parent');
		var isChecked 	= $(this).is(':checked');
		
		if ( isChecked )
		{
			$("#at_tagify_custom" + id + "_suggestions").addClass("d-none");
			getTopCustomTags(id);
		}

		if ( cusList.length > 0 )
		{			
			$("#cusTagsHelp").removeClass("d-none");
		}
		else
		{
			$("#cusTagsHelp").addClass("d-none");
		}
	});
});

function getTopCustomTags(id)
{
	var lang = '<?php echo $Admin->GetLang() ?>';
	var site = '<?php echo $Admin->GetSite() ?>';

	$.ajax(
	{
		url: '<?php echo AJAX_ADMIN_PATH ?>get-top-tags/',
		type: 'POST',
		data: {cusId:id,site:site,lang:lang},
		dataType: 'json',
		cache: false
	})
	.done(function(data)
	{
		var html = '';

		if ( data.status === "ok" )
		{
			for (let i = 0; i < data.tags.length; ++i)
			{
				var text = ( ( data.tags[i].num_items > 100 ) ? '1.4' : ( ( data.tags[i].num_items > 75 ) ? '1.3' : ( ( data.tags[i].num_items > 50 ) ? '1.2' : ( ( data.tags[i].num_items > 25 ) ? '1.1' : '1.0' ) ) ) );

				html += '<a href="javascript: void(0);"><span style="font-size: ' + text + 'em;" class="cursor-pointer link-black" data-cus' + id + '-suggestion="true" data-id="' + data.tags[i].id + '" data-tag="' + data.tags[i].id + '">' + data.tags[i].title + '</span></a> ';
			}
		}
		
		$('#kt_tagify_custom' + id + '_suggestions').html('');
		$('#kt_tagify_custom' + id + '_suggestions').html(html);
	});
}

/*
function cleanCustomTagsList()
{
	if ( tagifyCs === undefined )
		return;
	
	tagifyCs.removeAllTags();
}

function populateCustomTagsList(id)
{
	var tag = returnTagName(id);
	console.log(tag);return;
	if ( tag === undefined )
		return;
	
	tag.on('input', function(e){
		var val = e.detail.value; 
		tag.whitelist = null; // reset the whitelist
		controller && controller.abort();
		controller = new AbortController();
		
		tag.loading(true).dropdown.hide();
		
		$.post('<?php echo AJAX_ADMIN_PATH ?>tags/', 
			{ 'slug': val, 'tparr': arr, 'lang': '<?php echo $Admin->GetLang() ?>', 'site': '<?php echo $Admin->GetSite() ?>' }, 
				function( data ) {
				tag.whitelist = data; // update whitelist Array in-place
				tag.loading(false).dropdown.show(val); // render the suggestions dropdown
			}
		);
	});
	
	//Clean the whitelist
	tag.on('add', function(e){
		tag.whitelist = [];
	});
	
	tag.on('change', function(e){
		var input = document.querySelector('#customtags').value;
		
		if ( input.length > 0 )
		{
			var parse = JSON.parse(input);
			var attachArray = [];
		
			for(var i=0; i < parse.length; i++) {
				attachArray.push( parse[i].value );
			}
			
			$('#customIds' + id).val( JSON.stringify( attachArray ) );
		}
	});
	
	tag.on('remove', function(e){
		var input = document.querySelector('#customtags').value;
		
		if ( input.length > 0 )
		{
			var attachArray = [];
			
			var parse = JSON.parse(input);
	
			for(var i=0; i < parse.length; i++) {
				attachArray.push( parse[i].value );
			}
			
			$('#customIds' + id).val( JSON.stringify( attachArray ) );
		}
		
		$('#customIds' + id).val( '' );
	});
}*/
</script>
<?php endif ?>