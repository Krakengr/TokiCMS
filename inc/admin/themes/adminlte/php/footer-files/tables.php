<!-- Modal for Add a new Element (header) -->
<div id="newElementHeader" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal centered" role="document" style="padding-right: 17px; display: block; width:100%;">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title"><?php echo __( 'add-element' ) ?></h5>
</div>
<div class="modal-body">

<?php 
	if ( !empty( $genericTablesHeaderArray ) ) :
		foreach ( $genericTablesHeaderArray as $w => $elem ) : ?>
		<div class="card" id="<?php echo $w ?>">
			<div class="card-header border-transparent">
				<h4 class="card-title"><?php echo $elem['title'] ?></h4>
				<div class="card-tools">
					<div class="btn-group">
						<button type="button" id="addElementButton" data-id="<?php echo $w ?>" class="btn btn-tool addHeaderElementButton">
							<i class="fas fa-plus"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
		<?php endforeach ?>
	<?php endif ?>
	
</div>
<div class="modal-footer">
	<div class="text-left"><button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button></div>
</div>
<input type="hidden" id="AddTableIdHeader" value="">
</div>
</div>
</div>

<!-- Modal for Add a new Element (Cell) -->
<div id="newElementCell" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal centered modal-lg" role="document" style="padding-right: 17px; display: block; width:100%;">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title"><?php echo __( 'add-element' ) ?></h5>
</div>
<div class="modal-body">

	<div class="container-fluid">
		<div class="row">
		<?php 
		if ( !empty( $genericTablesArray ) ) :
			foreach ( $genericTablesArray as $w => $elem ) : ?>
			<div class="col-md-6 ml-auto">
				<div class="card" id="<?php echo $w ?>">
					<div class="card-header border-transparent">
						<h4 class="card-title"><?php echo $elem['title'] ?></h4>
						<div class="card-tools">
							<div class="btn-group">
								<?php if ( !empty( $elem['tip'] ) ) : ?><a href="#" class="btn btn-tool btn-sm" title="<?php echo $elem['tip'] ?>"><i class="fas fa-info-circle"></i></a><?php endif ?>
								
								<button type="button" data-id="<?php echo $w ?>" class="btn btn-tool addCellElementButton">
									<i class="fas fa-plus"></i>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endforeach ?>
		<?php endif ?>
		</div>
	</div>
	
</div>
<div class="modal-footer">
	<div class="text-left"><button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'cancel' ) ?></button></div>
</div>
<input type="hidden" id="AddTableIdCell" value="">
</div>
</div>
</div>

<script type='text/javascript'>
//Assing the color picker
$(function () {
	$('.color-picker').colorpicker();
});

$(document).ready(function()
{
	//Prevent enter key to submit the form
	$(window).keydown(function(event){
		if(event.keyCode == 13) {
			event.preventDefault();
			return false;
		}
	});
  
	<?php 
		if ( isset( $Form['data']['auto_insert_table'] ) && ( $Form['data']['auto_insert_table'] == 'posts-archive' ) )
		{
			if ( !empty( $Form['data']['show_target_tag_auto'] ) )
			{
				echo '$("#displayTargetTagDivAuto").removeClass("d-none");' . PHP_EOL;
			}
			
			elseif ( !empty( $Form['data']['show_target_blog_auto'] ) )
			{
				echo '$("#displayTargetBlogDivAuto").removeClass("d-none");' . PHP_EOL;
				echo '$("#displayTargetBlogAutoHelp").removeClass("d-none");' . PHP_EOL;
			}
			
			else
			{
				echo '$("#displayTargetCategoryDivAuto").removeClass("d-none");' . PHP_EOL;
			}
		}

		elseif ( !empty( $Form['data']['show_table_if'] ) )
		{
			switch( $Form['data']['show_table_if'] )
			{
				case 'category':      
					echo '$("#displayTargetCategoryDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'tag':      
					echo '$("#displayTargetTagDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'custom-post-type':      
					echo '$("#displayTargetCustomDiv").removeClass("d-none");' . PHP_EOL;
				break;

				default:
					echo '$("#displayTargetCategoryDiv").removeClass("d-none");' . PHP_EOL;
			}
		}
		
		elseif ( !empty( $Form['data']['hide_table_if'] ) )
		{
			switch( $Form['data']['hide_table_if'] )
			{      
				case 'category':      
					echo '$("#displayTargetCategoryHideDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'tag':      
					echo '$("#displayTargetTagHideDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'custom-post-type':      
					echo '$("#displayTargetCustomHideDiv").removeClass("d-none");' . PHP_EOL;
				break;
			}
		}
	?>
	
	var autoCatShow = $("#displayTargetCategoryAuto").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "<?php echo AJAX_ADMIN_PATH ?>filter-get-categories/",
			data: function (params) {
				var query = {
					postLang: "<?php echo $Admin->GetLang() ?>",
					postBlog: "<?php echo $Admin->GetBlog() ?>",
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
	
	var autoTagShow = $("#displayTargetTagAuto").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "<?php echo AJAX_ADMIN_PATH ?>filter-get-tags/",
			data: function (params) {
				var query = {
					postLang: "<?php echo $Admin->GetLang() ?>",
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
	
	var targetTagShow = $("#displayTargetTag").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "<?php echo AJAX_ADMIN_PATH ?>filter-get-tags/",
			data: function (params) {
				var query = {
					postLang: "<?php echo $Admin->GetLang() ?>",
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
		
	var targetTagHide = $("#displayTargetTagHide").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "<?php echo AJAX_ADMIN_PATH ?>filter-get-tags/",
			data: function (params) {
				var query = {
					postLang: "<?php echo $Admin->GetLang() ?>",
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
		
	var targetCategoryHide = $("#displayTargetCategoryHide").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "<?php echo AJAX_ADMIN_PATH ?>filter-get-categories/",
			data: function (params) {
				var query = {
					postLang: "<?php echo $Admin->GetLang() ?>",
					postBlog: "<?php echo $Admin->GetBlog() ?>",
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
		
	var targetCategoryShow = $("#displayTargetCategory").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "<?php echo AJAX_ADMIN_PATH ?>filter-get-categories/",
			data: function (params) {
				var query = {
					postLang: "<?php echo $Admin->GetLang() ?>",
					postBlog: "<?php echo $Admin->GetBlog() ?>",
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
	
	var targetBlogShow = $("#displayTargetBlogAuto").select2({
		placeholder: "",
		allowClear: true,
		theme: "bootstrap4",
		minimumInputLength: 2,
		ajax: {
			type: "POST",
			url: "<?php echo AJAX_ADMIN_PATH ?>filter-get-blogs/",
			data: function (params) {
				var query = {
					postLang: "<?php echo $Admin->GetLang() ?>",
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
	
	$("#showTableIfAuto").change(function()
	{
		$("#displayTargetCategoryDivAuto").addClass("d-none").fadeOut();
		$("#displayTargetTagDivAuto").addClass("d-none").fadeOut();
		$("#displayTargetBlogDivAuto").addClass("d-none").fadeOut();
		$("#displayTargetBlogAutoHelp").addClass("d-none").fadeOut();
		$("#loaderShowAuto").removeClass("d-none");
		
		$("#displayTargetCategoryAuto").val(null).trigger("change");
		$("#displayTargetBlogAuto").val(null).trigger("change");
		$("#displayTargetTagAuto").val(null).trigger("change");
			
		var id = $(this).val();
			
		id = id.trim();
		
		if ( id === 'tag' )
		{
			setTimeout(function(){
				$("#loaderShowAuto").addClass("d-none");
				$("#displayTargetTagDivAuto").removeClass("d-none");
			},500);
		}
		
		else if ( id === 'blog' )
		{
			setTimeout(function(){
				$("#loaderShowAuto").addClass("d-none");
				$("#displayTargetBlogAutoHelp").removeClass("d-none");
				$("#displayTargetBlogDivAuto").removeClass("d-none");
			},500);
		}
		
		else
		{
			setTimeout(function(){
				$("#loaderShowAuto").addClass("d-none");
				$("#displayTargetCategoryDivAuto").removeClass("d-none");
			},500);
		}
	});
	
	$("#showTableIf").change(function()
	{
		$("#displayTargetCategoryDiv").addClass("d-none").fadeOut();
		$("#displayTargetTagDiv").addClass("d-none").fadeOut();
		$("#displayTargetCustomDiv").addClass("d-none").fadeOut();
		$("#displayTargetBlogDiv").addClass("d-none").fadeOut();
		$("#loaderShow").removeClass("d-none");
		
		$("#displayTargetCategory").val(null).trigger("change");
		$("#displayTargetTag").val(null).trigger("change");
		$("#displayTargetBlog").val(null).trigger("change");
		$("#displayTargetCustom").val(null).trigger("change");
			
		var id = $(this).val();

		id = id.trim();
			
		if ( id === 'category' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#displayTargetCategoryDiv").removeClass("d-none");
			},500);
		}

		else if ( id === 'custom-post-type' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#displayTargetCustomDiv").removeClass("d-none");
			},500);
		}

		else if ( id === 'tag' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#displayTargetTagDiv").removeClass("d-none");
			},500);
		}
		
		else if ( id === 'blog' )
		{
			setTimeout(function(){
				$("#loaderShow").addClass("d-none");
				$("#displayTargetBlogDiv").removeClass("d-none");
			},500);
		}
			
		else
		{
			$("#loaderShow").addClass("d-none");
		}
	});
	
	$("#hideTableIf").change(function()
	{
		$("#displayTargetCategoryHideDiv").addClass("d-none").fadeOut();
		$("#displayTargetTagHideDiv").addClass("d-none").fadeOut();
		$("#displayTargetCustomHideDiv").addClass("d-none").fadeOut();
		$("#loaderHide").removeClass("d-none");
		
		$("#displayTargetCategoryHide").val(null).trigger("change");
		$("#displayTargetTagHide").val(null).trigger("change");
		$("#displayTargetCustomHide").val(null).trigger("change");
			
		var id = $(this).val();
			
		id = id.trim();
			
		if ( id === 'category' )
		{
			setTimeout(function(){
				$("#loaderHide").addClass("d-none");
				$("#displayTargetCategoryHideDiv").removeClass("d-none");
			},500);
		}

		else if ( id === 'custom-post-type' )
		{
			setTimeout(function(){
				$("#loaderHide").addClass("d-none");
				$("#displayTargetCustomHideDiv").removeClass("d-none");
			},500);
		}
			
		else if ( id === 'tag' )
		{
			setTimeout(function(){
				$("#loaderHide").addClass("d-none");
				$("#displayTargetTagHideDiv").removeClass("d-none");
			},500);
		}
			
		else
		{
			$("#loaderHide").addClass("d-none");
		}
	});
	
	$("#autoInsertTable").change(function()
	{
		$("#autoInsertFormGroupCategory").addClass("d-none");
		$("#autoInsertFormGroup").addClass("d-none");
		
		//Reset the select boxes
		$("#displayTargetTagAuto").val(null).trigger("change");
		$("#displayTargetCategoryAuto").val(null).trigger("change");
		$("#showTableIfAuto").val("category").trigger("change");
		$("#showTableIf").val(null).trigger("change");
		$("#displayTargetCategory").val(null).trigger("change");
		$("#displayTargetTag").val(null).trigger("change");
		$("#displayTargetCustom").val(null).trigger("change");
		$("#hideTableIf").val(null).trigger("change");
		$("#displayTargetCategoryHide").val(null).trigger("change");
		$("#displayTargetTagHide").val(null).trigger("change");
		$("#displayTargetCustomHide").val(null).trigger("change");
		
		var id = $(this).val();
		
		id = id.trim();
		
		if ( id !== '' )
		{
			if ( id === 'posts-archive' )
			{
				$("#autoInsertFormGroupCategory").removeClass("d-none");
				
				//Display the categories to avoid empty space
				setTimeout(function(){
					$("#displayTargetCategoryDivAuto").removeClass("d-none");
				},500);
			}
			
			else
			{
				$("#autoInsertFormGroup").removeClass("d-none");
			}
		}
	});
	
	$("#deleteFormTemplate").change(function() {
		if($(this).is(":checked")) 
		{
			if ( confirm(v2['deleteCheckBoxAlert']) )
				this.checked = true;
			else
				this.checked = false;
		}      
	});
	
	//Cancel text input for column
	$(".cancelTitleButton").click(function(e) {

		e.preventDefault();
		var id = $(this).data('id');
		
		cancelTitle(id);
	});
	
	//Save text input for column
	$(".saveTitleButton").click(function(e) {

		e.preventDefault();
		var id = $(this).data('id');
		var formId = '<?php echo (int) Router::GetVariable( 'key' ) ?>';
		
		saveTitle(id,formId);
	});
	
	//Add text input for column
	$(".changeTitleButton").click(function(e) {
		
		e.preventDefault();
		var id = $(this).data('id');
		
		changeTitle(id);
    });
	
	//$(".remElementButton").click(function (e)
	$("body").on("click", ".remElementButton", function(e)
	{
		e.preventDefault();
		
		var id = $(this).data('id');
		
		removeElement(id);
	});

	$("body").on("click", ".remColumnButton", function(e)
	{
		e.preventDefault();
		
		var id = $(this).data('id');
		var formId = '<?php echo (int) Router::GetVariable( 'key' ) ?>';
		
		removeColumn( id, formId );
	});
	
	//Add a new element into cell (Modal)
	$(".addColumnCellElement").click(function (e)
	{
		var id = $(this).data('id');
		
		addColumnCellElement(id, '');
	});
	
	//Add a new element into cell (Modal)
	$(".addColumnHeadElement").click(function (e)
	{
		var id = $(this).data('id');
		
		addColumnHeadElement(id);
	});
	
	//Add a new element into cell
	$(".addCellElementButton").click(function (e)
	{
		e.preventDefault();
	
		var id = $(this).data("id");
		var formId = '<?php echo (int) Router::GetVariable( 'key' ) ?>';

		var elId = $("#AddTableIdCell").val();
		
		var elemType = 'cell';
		
		if(typeof(elId) == "undefined" || ( elId === "" ) || ( elId === 0 ) )
		{
			alert("<?php echo __( 'an-error-happened' ) ?>");
			$("#newElementCell").modal("hide");
			return;
		}

		$.ajax({
			data: {id:id,formId:formId,elId:elId,type:elemType},
			type: 'POST',
			url: "<?php echo AJAX_ADMIN_PATH ?>add-table-element/",
			dataType: 'json',
			cache: false
		})
		.done(function(data){
			if ( data.status == 'ok' )
			{
				if ( data.html !== '' )
				{
					var itemPos = '#contentCellBuilder' + elId;
					
					$(itemPos).append(data.html);
				}
			}
			else
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'an-error-happened' ) ?>"
				})
			}
			
			$("#newElementCell").modal("hide");
		});
	});
	
	//Add a new element into header
	$(".addHeaderElementButton").click(function (e)
	{
		e.preventDefault();
	
		var id = $(this).data("id");
		var formId = '<?php echo (int) Router::GetVariable( 'key' ) ?>';

		var elId = $("#AddTableIdHeader").val();
		
		var elemType = 'header';
		
		if(typeof(elId) == "undefined" || ( elId === "" ) || ( elId === 0 ) )
		{
			alert("<?php echo __( 'an-error-happened' ) ?>");
			$("#newElementHeader").modal("hide");
			return;
		}

		$.ajax({
			data: {id:id,formId:formId,elId:elId,type:elemType},
			type: 'POST',
			url: "<?php echo AJAX_ADMIN_PATH ?>add-table-element/",
			dataType: 'json',
			cache: false
		})
		.done(function(data){
			if ( data.status == 'ok' )
			{
				if ( data.html !== '' )
				{
					var itemPos = '#contentHeaderBuilder' + elId;
					
					$(itemPos).append(data.html);
				}
			}
			else
			{
				Toast.fire({
					icon: "error",
					title: "<?php echo __( 'an-error-happened' ) ?>"
				})
			}
			
			$("#newElementHeader").modal("hide");
		});
	});
	
	//Expand All Cards
	$("#expandAll").click(function (e)
	{
		e.preventDefault();

		$("i", this).toggleClass("fa-expand fa-compress");
		$('.multi-collapse').CardWidget('toggle');

	});
	
	//Add a new column
	$("#addColumn").click(function (e)
	{
		e.preventDefault();
		
		var formId = '<?php echo (int) Router::GetVariable( 'key' ) ?>';

		$.ajax({
			data: {formId:formId},
			type: 'POST',
			url: "<?php echo AJAX_ADMIN_PATH ?>add-table-column/",
			dataType: 'json',
			cache: false
		})
		.done(function(data){
			if ( data.status == 'ok' )
			{
				if ( data.html !== '' )
				{
					var itemPos = '#formBuilder';
					
					$(itemPos).append(data.html);
				}
				
				if ( data.code !== '' )
				{
					$("#tablePreview").html(data.code);
				}
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
});

function addColumnCellElement(id, type)
{
	$("#AddTableIdCell").val(id);
	$("#newElementCell").modal("show");
}

function addColumnHeadElement(id)
{
	$("#AddTableIdHeader").val(id);
	$("#newElementHeader").modal("show");
}

function changeTitle(id)
{
	$("#elemntTitle" + id).addClass("d-none");
	$("#changeTitle" + id).addClass("d-none");
	$("#columnTitleDiv" + id).removeClass("d-none");
}

function saveTitle(id,formId)
{
	//Get the default title
	var orTitle = $("#elemntTitle" + id).text();
	var inputTitle = $("#elemntTitleInput" + id).val();

	$.ajax(
	{
		url: '<?php echo AJAX_ADMIN_PATH ?>change-column-title/',
		type: 'POST',
		data: {id:id,title:inputTitle,formId:formId},
		dataType: 'json',
		cache: false
	})
	.done(function(data)
	{
		if ( data.status == 'ok' )
		{
			$("#elemntTitle" + id).html(inputTitle);
			
			$("#elemntTitle" + id).removeClass("d-none");
			$("#changeTitle" + id).removeClass("d-none");
			$("#columnTitleDiv" + id).addClass("d-none");
		}
			
		if ( data.code !== '' )
		{
			$("#tablePreview").html(data.code);
		}
	})
	.fail(function(){
		$("#elemntTitle" + id).html(orTitle);
		
		$("#elemntTitle" + id).removeClass("d-none");
		$("#changeTitle" + id).removeClass("d-none");
		$("#columnTitleDiv" + id).addClass("d-none");
	});
}

function cancelTitle(id)
{
	//Get the default title
	var inputTitle = $("#elemntTitle" + id).text();

	$("#elemntTitle" + id).removeClass("d-none");
	$("#changeTitle" + id).removeClass("d-none");
	$("#columnTitleDiv" + id).addClass("d-none");
		
	//Re-enter the old title to the input field
	$("#elemntTitleInput" + id).val(inputTitle);
}

function removeColumn(id,formId)
{
	if( !confirm("<?php echo __( 'are-you-sure-you-want-to-remove-this-field' ) ?>"))
	{
        return false;
    }
	
	$.ajax({
		url: '<?php echo AJAX_ADMIN_PATH ?>remove-table-column/',
		type: 'POST',
		data: {id:id,formId:formId},
		dataType: 'json',
		cache: false
	})
	.done(function(data){
			
		if ( data.status == 'ok' )
		{
			$("#table-item-" + id).remove();
		}
			
		if ( data.code !== '' )
		{
			$("#tablePreview").html(data.code);
		}
	})
	.fail(function(){
		console.log("Remove error for id: " + id);
	});
}

function removeElement(id)
{
	if( !confirm("<?php echo __( 'are-you-sure-you-want-to-remove-this-element' ) ?>"))
	{
        return false;
    }
	
	$.ajax({
		url: '<?php echo AJAX_ADMIN_PATH ?>remove-table-element/',
		type: 'POST',
		data: {id:id},
		dataType: 'json',
		cache: false
	})
	.done(function(data){

		if ( data.status == 'ok' )
		{
			$("#element-item-" + id).remove();
		}
	})
	.fail(function(){
		console.log("Remove error for element id: " + id);
	});
};
</script>

<script type="application/javascript">
$(function(){'use strict'
	$('.connectedSortable').sortable({
		update: function(event, ui) {
			var formId = '<?php echo (int) Router::GetVariable( 'key' ) ?>';
			var idsInOrder = $(this).sortable('toArray', { attribute: 'data-id' });
			$.ajax({
				data: {ids:idsInOrder,formId:formId},
				type: 'POST',
				url: "<?php echo AJAX_ADMIN_PATH ?>sort-table/",
				dataType: 'json',
				cache: false
			})
			.done(function(data){
				if ( data.status == 'ok' )
				{
					if ( data.code !== '' )
					{
						$("#tablePreview").html(data.code);
					}
				
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
		},
		placeholder:'sort-highlight',
		connectWith:'.connectedSortable',
		handle:'.card-header, .nav-tabs',
		forcePlaceholderSize:true,
		dropOnEmpty: true,
		zIndex:999999
	});
	$('.connectedSortable .card-header').css('cursor','move');
});

$(function(){'use strict'
	$('.connectedSortable2').sortable({
		update: function(event, ui) {
			//var columnId = $(this).closest('div').attr("parent");
			var idsInOrder = $(this).sortable('toArray', { attribute: 'data-id' });

			$.ajax({
				data: {ids:idsInOrder},
				type: 'POST',
				url: "<?php echo AJAX_ADMIN_PATH ?>sort-columns/",
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
		},
		placeholder:'sort-highlight',
		//connectWith:'.connectedSortable2',
		handle:'.card-header, .nav-tabs',
		forcePlaceholderSize:true,
		dropOnEmpty: true,
		zIndex:999999
	});
	$('.connectedSortable2 .card-header').css('cursor','move');
});
</script>