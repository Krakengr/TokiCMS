<script type="application/javascript">
$(document).ready(function(){

function ClearValues()
{
	$("#searchContent").val("");
	$("#deepSearch").prop("checked", false);
}

function NothingFound()
{
	var nothing = "<div class=\"alert alert-warning\"><?php echo __( 'nothing-found' ) ?></div>";
	$("#search-results").hide();
	$("#content-footer").hide();
	$("#latest-posts").show();
	$("#latest-posts").html(nothing);
}

function escapeString(string)
{
	return string.replace(/&/g, '&amp;').replace(/>/g, '&gt;').replace(/</g, '&lt;').replace(/"/g, '&quot;').replace(/\'/g, '&quot;');
}

function ShowHtmlPosts(data, where)
{
	if ( data == "" )
	{
		NothingFound();
		return;
	}

	var htmlData = "";
	
	htmlData += "<div class=\"form-group\" style=\"height:250px;overflow-y:scroll;\">";

	$.each(data, function(i, item) {

		htmlData += "<div class=\"form-check\"><input class=\"form-check-input\" type=\"checkbox\" name=\"select-content[]\" value=\""+item["id"]+"\"><label class=\"form-check-label\">"+item["title"];
		
		if ( item["postType"] == "page" )
		{
			htmlData += " <span class=\"text-secondary\">Page</span>";
		}
			
		htmlData += " <span class=\"text-indigo\"><em>["+item["time"]+"]</em></span></label></div>";
	});

	htmlData += "</div>";
	
	$("#content-footer").show();
	
	if ( where == "latest" )
	{
		$("#search-results").hide();
		$("#latest-posts").show();
		$("#latest-posts").html(htmlData);
	}
	else
	{
		$("#latest-posts").hide();
		$("#search-results").show();
		$("#search-results").html(htmlData);
	}
}

function ShowLatest(deepSearch, search, siteId, blogId, langId)
{
	var where = "latest";
	$("#query-notice-message").show();
	
	$.ajax(
	{
		url: "<?php echo AJAX_ADMIN_PATH ?>search-posts/",
		type: "POST",
		data: {deepSearch:deepSearch,search:search,site:siteId,blog:blogId,lang:langId},
		dataType: "json",
		complete: function() {
			//
		},
		success: function(json) {			
			if (json["error"]) {
				NothingFound();
			}

			if ( json["data"] )
			{
				ShowHtmlPosts(json["data"], where);
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function SearchPosts (value, deep, site, lang, blog) {
	var where = "search";
	$("#search-waiting").show();
	$("#query-notice-message").hide();
	$.ajax(
	{
		url: "<?php echo AJAX_ADMIN_PATH ?>search-posts/",
		type: "POST",
		data: {deepSearch:deep,search:value,site:site,blog:blog,lang:lang},
		dataType: "json",
		cache: false
	})
	.done(function(json)
	{
		$("#search-waiting").hide();
		if (json["error"]) {
			NothingFound();
		}

		if ( json["data"] )
		{
			ShowHtmlPosts(json["data"], where);
		}
	});
};

function ShowPosts() {
	var typingTimer;
	var typingInterval = 500;
	var deep = $("#deepSearch").is(":checked");
	var siteId = "<?php echo $Admin->GetSite() ?>";
	var langId = "<?php echo $Admin->GetLang() ?>";
	var blogId = "<?php echo $Admin->GetBlog() ?>";
	
	ShowLatest(deep, null, siteId, blogId, langId);

	$("#searchContent").on("input", function(e) {
		var val = this.value;
		clearTimeout(typingTimer);
		typingTimer = setTimeout(SearchPosts, typingInterval, val, deep, siteId, langId, blogId);
	});
		
	$("#searchContent").on("keydown", function(e) {
		clearTimeout(typingTimer);
	});
};

ClearValues();

ShowPosts();
});
</script>

<script type="application/javascript">
	$(document).ready(function(){
		
		var menuid = '<?php echo $Menu['id_menu'] ?>';
		
		$('#select-all-pages').click(function(event) {   
			if(this.checked) {
				$('input[name="select-content[]"]').prop('checked', true);
			} else{
				$('input[name="select-content[]"]').prop('checked', false);
			}
		});
		
		$('#select-all-categories').click(function(event) {   
			if(this.checked)
			{
				$('#categories-list :checkbox').each(function() {
					this.checked = true;                        
				});
			}
			else {
				$('#categories-list :checkbox').each(function() {
					this.checked = false;                        
				});
			}
		});
		
		$('#add-custom-link').click(function()
		{
			var text = $("#linktext").val();
			var uri = $("#linkurl").val();
			var group = 'custom-link';
			
			if( (url == "" || url == null) && (text == "" || text == null) )
			{
				return;
			}
			
			$.ajax({
				type:"post",
				data: {menuid:menuid,group:group,text:text,uri:uri},
				url: '<?php echo AJAX_ADMIN_PATH ?>add-menu-item/',	
				success:function(res){
					if ( res.status == 'ok' ) 
					{
						$('.menu-list').append(res.html);
					}
				}
			})
		});
		
		$('#add-pages').click(function(){
			var menuid = '<?php echo $Menu['id_menu'] ?>';
			var n = $('input[name="select-content[]"]:checked').length;
			var array = $('input[name="select-content[]"]:checked');
			var group = 'content';
			var ids = [];
			var idsInMenu = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});
			var idsAll = [];
			
			idsInMenu.forEach(function(item)
			{
				if(typeof(item.id) != "undefined" && item.id !== null)
				{
					idsAll.push(item.id);
				}
			});
			
			for(i=0;i<n;i++)
			{
				var idToAdd = array.eq(i).val();

				if( jQuery.inArray( idToAdd, idsAll ) === -1 )
				{
					ids[i] = idToAdd;
				}
			}
			
			$('input[name="select-content[]"]').prop('checked', false);
			$('#select-all-pages').prop('checked', false);
			
			if(ids.length == 0){
				return false;
			}
			
			$.ajax({
				type:"post",
				data: {menuid:menuid,ids:ids,group:group},
				url: '<?php echo AJAX_ADMIN_PATH ?>add-menu-item/',	
				success:function(res){
					if ( res.status == 'ok' ) 
					{
						$('.menu-list').append(res.html);
					}
				}
			})
		});	
			
	
		$('#add-categories').click(function(){
			var menuid = '<?php echo $Menu['id_menu'] ?>';
			var n = $('input[name="select-category[]"]:checked').length;
			var array = $('input[name="select-category[]"]:checked');
			var group = 'category';
			var ids = [];
			var idsInMenu = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});
			var idsAll = [];
			
			idsInMenu.forEach(function(item)
			{
				if(typeof(item.id) != "undefined" && item.id !== null)
				{
					idsAll.push(item.id);
				}
			});
			
			for(i=0;i<n;i++)
			{
				var idToAdd = array.eq(i).val();//ids[i] = array.eq(i).val();

				if( jQuery.inArray( idToAdd, idsAll ) === -1 )
				{
					ids[i] = idToAdd;
				}
			}
			
			$('input[name="select-category[]"]').prop('checked', false);
			$('#select-all-categories').prop('checked', false);
			
			if(ids.length == 0){
				return false;
			}
			
			$.ajax({
				type:"post",
				data: {menuid:menuid,ids:ids,group:group},
				url: '<?php echo AJAX_ADMIN_PATH ?>add-menu-item/',	
				success:function(res){
					if ( res.status == 'ok' ) 
					{
						$('.menu-list').append(res.html);
					}
				}
			})
		});
		
		$('#deleteMenu').click(function(){
			if( !confirm("<?php echo __( 'are-you-sure-this-action-cannot-be-undone' ) ?>"))
			{
				return false;
			}

			var id = '<?php echo $Menu['id_menu'] ?>';
			var url = "<?php echo $Admin->GetUrl( 'menus' ) ?>";

			$.ajax({
				type:"post",
				data: {id:id},
				url: '<?php echo AJAX_ADMIN_PATH ?>delete-menu/',	
				success:function(res){
					
					if ( res.status == 'ok' )
					{
						$(location).attr('href',url);
					}
					else
					{
						Toast.fire({
							icon: "error",
							title: "<?php echo __( 'an-error-happened' ) ?>"
						})
					}
				}
			});
		});
		
		$('.item-rm').click(function(){
			var id = $(this).data("id");
			var menuid = '<?php echo $Menu['id_menu'] ?>';
			
			$.ajax({
				type:"post",
				data: {menuid:menuid,id:id},
				url: '<?php echo AJAX_ADMIN_PATH ?>rem-menu-item/',	
				success:function(res){
					
					if ( res.status == 'ok' ) 
					{
						$("#menuItem_"+id+"").remove();
						
						if(typeof(res.html) != "undefined" && res.html !== null)
						{
							$(".menu-list").append(res.html);
						}
					}
				}
			});
		});

		$('#saveMenu').click(function(){
			var id = '<?php echo $Menu['id_menu'] ?>';
			var idsInOrder = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 3});

			var posit = $('input[id="menuPos"]:checked').val();

			if(typeof(posit) != "undefined" && posit !== null)
			{
				var po = posit;
			}
			else
			{
				var po = 'primary';
			}
			
			var items = [];
			var order = 0;
			console.log(idsInOrder );
			idsInOrder.forEach(function(item)
			{
				if(typeof(item.id) != "undefined" && item.id !== null)
				{
					var itemId = item.id;
					
					var parent_id = item.parent_id;
					
					var idToLook = "#menuItem_"+itemId;
					
					var li = $(idToLook);
					
					var type = li.attr("data-id");
					
					var title = li.find('input[id="titleAttr"]').val();
					var label = li.find('input[id="navLabel"]').val();
					var url = li.find('input[id="navUrl"]').val();
					var tab = li.find('input[id="blankTarget"]').is(':checked');

					items[itemId] = { id: itemId, p: parent_id, t: type, tl: title, l: label, u: url, b: tab, o: order };
					
					order++;
				}
			});

			$.ajax({
				data: {id:id,ids:items,ps:po},
				type: 'POST',
				url: "<?php echo AJAX_ADMIN_PATH ?>save-menu/",
				dataType: 'json',
				cache: false
				})
				.done(function(data){
					if ( data.status == 'ok' )
					{
						Toast.fire({
							icon: "success",
							title: data.message
						})
					}
					else
					{
						Toast.fire({
							icon: "error",
							title: "<?php echo __( 'an-error-happened' ) ?>"
						})
					}
				});
		});
	
	
		$('.sortable').nestedSortable({
			/*
			update: function(event, ui) {},
			relocate: function(){},
			*/
			start: function(e, ui){
				ui.placeholder.height(ui.helper[0].scrollHeight);
			},
			forcePlaceholderSize: true,
			handle: '.handle',
			placeholder: 'placeholder',
			items: 'li',
			maxLevels: 3,
			isTree: true,
			expandOnHover: 700,
			tabSize: 40,
			opacity: .6,
			tolerance: 'pointer',
			helper:'clone',
			startCollapsed: false,
			revert: 300,
			toleranceElement: '> div',
			zIndex: 999999
		});
	});
</script>