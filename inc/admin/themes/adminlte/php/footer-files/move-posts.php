<script type='text/javascript'>

function sleep(ms) {
	return new Promise(resolve => setTimeout(resolve, ms));
}

async function moveItems(from,to,fromId,toId,options,numItems,totalItems)
{
	let num;
	var site 	= '<?php echo $Admin->GetSite() ?>';
	var message = '';
	
	num = totalItems / numItems;
	num = Math.ceil( num );
	
	for (let i=1; i<=num; i++)  
	{
		var current = ( i * numItems );
		var percentComplete = Math.ceil( ( current / totalItems ) * 100 );
		
		if ( percentComplete >= 100 )
		{
			percentComplete = 100;
		}
		
		await sleep(500);  

		$("#progressBarDiv").children(".progress-bar").width(percentComplete + "%");
		
		$.ajax(
		{
			url: '<?php echo AJAX_ADMIN_PATH ?>move-content/',
			type: 'POST',
			data: {from:from,to:to,fromId:fromId,toId:toId,site:site,options:options},
			dataType: 'json',
			cache: false
		})
		.done(function(data)
		{
			console.log("Done...");
		})
		.fail(function(){
			console.log("Move Content Error...");
		});
		
		//break;
	}
	
	await sleep(1000);  
	done('ok');
}

async function loadItems(from,to,fromId,toId,options)
{
	$("#progressBarDiv").removeClass("d-none");
	$("#moveButton").prop("disabled", true);
	var site = '<?php echo $Admin->GetSite() ?>';
	var totalItems = 0;
	var current = 0;
	var num = 0;
	var message = '';
	var status;
	var numItems = 10;

	$.ajax(
	{
		url: '<?php echo AJAX_ADMIN_PATH ?>load-move-content/',
		type: 'POST',
		data: {from:from,to:to,fromId:fromId,toId:toId,site:site,options:options},
		dataType: 'json',
		cache: false
	})
	.done(function(data)
	{
		status = data.status;
		
		if (status == 'ok') 
		{
			totalItems 	= data.totalItems;
		
			if ( totalItems == 0 )
			{
				return;
			}
			
			if (data.message)
			{
				$("#post-status").append(data.message);
			}
			
			moveItems(from,to,fromId,toId,options,numItems,totalItems);
		}

		else if (status == 'nothing-found') 
		{
			if (data.message)
			{
				console.log(data.message);
				$("#post-status").append(data.message);
			}
			else
			{
				console.log("Nothing Found Error...");
			}
			
			done(status);
		}
		
		else 
		{
			if (data.message)
			{
				console.log(data.message);
				$("#post-status").append(data.message);
			}
			else
			{
				console.log("Error...");
			}
			
			done(status);
		}
	})
	.fail(function(){
		done('error');
	});
}

function done(status)
{
	$("#progressBarDiv").addClass("d-none");

	$("#moveButton").prop("disabled", false);
	
	$("#post-status").addClass("d-none");
		
	if ( status == 'ok' )
	{
		$("#post-success").removeClass("d-none");
	}
	else if ( status == 'nothing-found' )
	{
		$("#post-no-content").removeClass("d-none");
	}
	
	else
	{
		$("#post-error").removeClass("d-none");
	}
}

$(document).ready(function()
{
	$("#moveButton").click(function (e)
	{
		e.preventDefault();
		
		var inputFrom = $("#inputFrom option:selected").val();
		var inputTarget = $("#inputTarget option:selected").val();
		
		var fromBlog = $("#fromBlog option:selected").val();
		var toBlog = $("#toBlog option:selected").val();
		var toBlogOptions = $("#toBlogOptions option:selected").val();
		
		var fromLang = $("#fromLang option:selected").val();
		var toLang = $("#toLang option:selected").val();
		
		var fromSite = $("#fromSite option:selected").val();
		var toSite = $("#toSite option:selected").val();
		
		var fromCategory = $("#fromCategory option:selected").val();
		var toCategory = $("#toCategory option:selected").val();
		
		$("#post-error").addClass("d-none").fadeOut();
		$("#post-success").addClass("d-none").fadeOut();
		
		inputFrom = inputFrom.trim();
		inputTarget = inputTarget.trim();
		
		var from = inputFrom;
		var to = inputFrom;
		var fromId;
		var toId;
		var targetId;
		var options = toBlogOptions;
		
		//Return if nothing selected
		if(typeof(inputFrom) == "undefined" || ( inputFrom === "" ) )
		{
			return;
		}
		
		//Move from blog
		if ( inputFrom === 'blog' )
		{
			if ( ( typeof(fromBlog) == "undefined" ) || ( fromBlog === "" ) )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'no-source-selected' ) ?>"
				})
				return;
			}
			
			if ( ( typeof(toBlog) == "undefined" ) || ( toBlog === "" ) )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'no-target-selected' ) ?>"
				})
				return;
			}
			
			if ( fromBlog == toBlog )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'source-target-same-error' ) ?>"
				})
				return;
			}

			fromId = fromBlog;
			toId = toBlog;
		}
		
		//Move from site
		else if ( inputFrom === 'site' )
		{
			if ( ( typeof(fromSite) == "undefined" ) || ( fromSite === "" ) )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'no-source-selected' ) ?>"
				})
				return;
			}
			
			if ( ( typeof(toSite) == "undefined" ) || ( toSite === "" ) )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'no-target-selected' ) ?>"
				})
				return;
			}
			
			if ( fromSite == toSite )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'source-target-same-error' ) ?>"
				})
				return;
			}

			fromId = fromSite;
			toId = toSite;
		}
		
		//Move from lang
		else if ( inputFrom === 'lang' )
		{
			if ( ( typeof(fromLang) == "undefined" ) || ( fromLang === "" ) )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'no-source-selected' ) ?>"
				})
				return;
			}
			
			if ( ( typeof(toLang) == "undefined" ) || ( toLang === "" ) )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'no-target-selected' ) ?>"
				})
				return;
			}
			
			if ( fromLang == toLang )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'source-target-same-error' ) ?>"
				})
				return;
			}

			fromId = fromLang;
			toId = toLang;
		}
		
		//Move from category
		else if ( inputFrom === 'category' )
		{
			if ( ( typeof(fromCategory) == "undefined" ) || ( fromCategory === "" ) )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'no-source-selected' ) ?>"
				})
				return;
			}
			
			if ( ( typeof(toCategory) == "undefined" ) || ( toCategory === "" ) )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'no-target-selected' ) ?>"
				})
				return;
			}
			
			if ( fromCategory == toCategory )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'source-target-same-error' ) ?>"
				})
				return;
			}
			
			fromId = fromCategory;
			toId = toCategory;
		}
		
		//Move from orphans
		else if ( ( inputFrom === 'orphan-posts' ) || ( inputFrom === 'orphan-pages' ) || ( inputFrom === 'orphan' ) )
		{
			if ( ( typeof(inputTarget) == "undefined" ) || ( inputTarget === "" ) )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'no-target-selected' ) ?>"
				})
				return;
			}
	
			else if ( inputTarget === 'category' )
			{
				toId = $('#toCategoryOrphan option:selected').val();
			}
			
			else if ( inputTarget === 'blog' )
			{
				toId = $('#targetOrphanBlogDiv option:selected').val();
			}
			
			else if ( inputTarget === 'lang' )
			{
				toId = $('#targetOrphanLangDiv option:selected').val();
			}
			
			else if ( inputTarget === 'site' )
			{
				toId = $('#targetOrphanSiteDiv option:selected').val();
			}
			else
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'an-error-happened' ) ?>"
				})
				
				return;
			}
			
			//Make sure we have a target selected
			if ( ( typeof(toId) == "undefined" ) || ( toId === "" ) )
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'no-target-selected' ) ?>"
				})
				return;
			}
			
			to = inputTarget;
			
			fromId = 0;
		}
		
		//Finally, we can ask them if they really want to move the posts
		if( !confirm("<?php echo __( 'are-you-sure' ) ?>"))
		{
            return false;
        }
		
		$("#post-status").html("");
		
		loadItems(from,to,fromId,toId,options);
	});
	
	$("#toBlog").change(function()
	{
		$("#blogPostSelection").addClass("d-none").fadeOut();
		
		$("#toBlogOptions").val("move").trigger("change");
		
		var id = $(this).val();

		id = id.trim();
		
		if ( ( id !== "" ) && ( id != 0 ) )
		{
			$("#blogPostSelection").removeClass("d-none");
		}
	});

	$("#inputTarget").change(function()
	{
		$("#targetOrphanCategoryDiv").addClass("d-none").fadeOut();
		$("#targetOrphanBlogDiv").addClass("d-none").fadeOut();
		$("#targetOrphanLangDiv").addClass("d-none").fadeOut();
		$("#targetOrphanSiteDiv").addClass("d-none").fadeOut();
		$("#blogPostSelection").addClass("d-none").fadeOut();
		
		$("#toCategoryOrphan").val(null).trigger("change");
		$("#toBlogOrphan").val(null).trigger("change");
		$("#toLangOrphan").val(null).trigger("change");
		$("#toSiteOrphan").val(null).trigger("change");
		
		$("#post-success").addClass("d-none").fadeOut();
		$("#post-no-content").addClass("d-none").fadeOut();
		$("#post-error").addClass("d-none").fadeOut();
		
		$("#loaderShow").removeClass("d-none");
		
		var id = $(this).val();

		id = id.trim();
		
		if ( id === 'category' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#targetOrphanCategoryDiv").removeClass("d-none");
			},500);
		}
		
		else if ( id === 'blog' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#targetOrphanBlogDiv").removeClass("d-none");
			},500);
		}
		
		else if ( id === 'lang' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#targetOrphanLangDiv").removeClass("d-none");
			},500);
		}
		
		else if ( id === 'site' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#targetOrphanSiteDiv").removeClass("d-none");
			},500);
		}
		
		else
		{
			$("#loaderShow").addClass("d-none");
		}
	});
	
	$("#inputFrom").change(function()
	{
		$("#targetOptionsNoOrphan").addClass("d-none").fadeOut();
		$("#targetOptionsOrphan").addClass("d-none").fadeOut();
		$("#targetBlogDiv").addClass("d-none").fadeOut();
		$("#targetLangDiv").addClass("d-none").fadeOut();
		$("#targetSiteDiv").addClass("d-none").fadeOut();
		$("#targetCategoryDiv").addClass("d-none").fadeOut();
		
		$("#targetOrphanCategoryDiv").addClass("d-none").fadeOut();
		$("#targetOrphanBlogDiv").addClass("d-none").fadeOut();
		$("#targetOrphanLangDiv").addClass("d-none").fadeOut();
		$("#targetOrphanSiteDiv").addClass("d-none").fadeOut();
		
		$("#inputTarget").val(null).trigger("change");
		
		$("#fromCategory").val(null).trigger("change");
		$("#toCategory").val(null).trigger("change");
		
		$("#fromSite").val(null).trigger("change");
		$("#toSite").val(null).trigger("change");
		
		$("#fromLang").val(null).trigger("change");
		$("#toLang").val(null).trigger("change");
		
		$("#fromBlog").val(null).trigger("change");
		$("#toBlog").val(null).trigger("change");

		var id = $(this).val();

		id = id.trim();

		$("#loaderShow").removeClass("d-none");
		
		if ( ( id === 'orphan-posts' ) || ( id === 'orphan-pages' ) || ( id === 'orphan' ) )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#targetOptionsNoOrphan").removeClass("d-none");
			},500);
		}
		
		else if ( id === 'blog' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#targetBlogDiv").removeClass("d-none");
			},500);
		}
		
		else if ( id === 'category' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#targetCategoryDiv").removeClass("d-none");
			},500);
		}
		
		else if ( id === 'lang' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#targetLangDiv").removeClass("d-none");
			},500);
		}
		
		else if ( id === 'site' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#targetSiteDiv").removeClass("d-none");
			},500);
		}

		else
		{
			$("#loaderShow").addClass("d-none");
		}
	});
});
</script>