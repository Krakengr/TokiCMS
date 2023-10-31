<script type="application/javascript">
	$(document).ready(function()
	{
		<?php if ( isset( $groupData['show-element-if'] ) )
		{
			switch($groupData['show-element-if'])
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
		
		//Show the categories to avoid a blank space
		else
		{
			echo '$("#displayTargetCategoryDiv").removeClass("d-none");' . PHP_EOL;
		}
		
		if ( isset( $groupData['hide-element-if'] ) )
		{
			switch($groupData['hide-element-if'])
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
				
				default:
					echo '$("#displayTargetCategoryHideDiv").removeClass("d-none");' . PHP_EOL;
			}
		}
		?>
		
		var store = $("#storesSource").select2({
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
		
		var manufacturer = $("#manufacturerSource").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>get-manufacturers/",
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

		var parent = $("#displayTargetPages").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>filter-get-pages/",
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
		
		var parent = $("#displayTargetPagesHide").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
				type: "POST",
				url: "<?php echo AJAX_ADMIN_PATH ?>filter-get-pages/",
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
		
		var parent = $("#displayTargetTag").select2({
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
		
		var parent = $("#displayTargetTagHide").select2({
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
		
		var parent = $("#displayTargetCategoryHide").select2({
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
		
		var parent = $("#displayTargetCategory").select2({
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
		
		<?php 
		if ( isset( $groupData['stock-status'] ) && !empty( $groupData['stock-status'] ) )
		{
			if ( empty( $groupData['stock-status']['in-stock'] ) )
			{
				echo '$("#inStockDisplayDiv").hide();' . PHP_EOL;
			}
			
			if ( empty( $groupData['stock-status']['out-of-stock'] ) )
			{
				echo '$("#outOfStockDisplayDiv").hide();' . PHP_EOL;
			}
			
			if ( empty( $groupData['stock-status']['on-backorder'] ) )
			{
				echo '$("#onBackorderDisplayDiv").hide();' . PHP_EOL;
			}
		}?>

		$("#inStockInput").click(function()
		{
			$("#inStockDisplayDiv").toggle(this.checked);
		});
		
		$("#outOfStockInput").click(function()
		{
			$("#outOfStockDisplayDiv").toggle(this.checked);
		});
		
		$("#onBackorder").click(function()
		{
			$("#onBackorderDisplayDiv").toggle(this.checked);
		});
		
		$("#hideElementIf").change(function()
		{
			$("#displayTargetCategoryHideDiv").addClass("d-none").fadeOut();
			$("#displayTargetTagHideDiv").addClass("d-none").fadeOut();
			$("#displayTargetPagesHideDiv").addClass("d-none").fadeOut();
			$("#displayTargetAttHideDiv").addClass("d-none").fadeOut();
			$("#displayTargetCustomHideDiv").addClass("d-none").fadeOut();
			$("#loader5").removeClass("d-none");
			
			var id = $(this).val();
			
			id = id.trim();
			
			if ( id === 'category' )
			{
				setTimeout(function(){
					$("#loader5").addClass("d-none");
					$("#displayTargetCategoryHideDiv").removeClass("d-none");
				},500);
			}
			
			else if ( id === 'page' )
			{
				setTimeout(function(){
					$("#loader5").addClass("d-none");
					$("#displayTargetPagesHideDiv").removeClass("d-none");
				},500);
			}
			
			else if ( id === 'custom-post-type' )
			{
				setTimeout(function(){
					$("#loader5").addClass("d-none");
					$("#displayTargetCustomHideDiv").removeClass("d-none");
				},500);
			}
			
			else if ( id === 'attribute' )
			{
				setTimeout(function(){
					$("#loader5").addClass("d-none");
					$("#displayTargetAttHideDiv").removeClass("d-none");
				},500);
			}
			
			else if ( id === 'tag' )
			{
				setTimeout(function(){
					$("#loader5").addClass("d-none");
					$("#displayTargetTagHideDiv").removeClass("d-none");
				},500);
			}
			
			else
			{
				$("#loader5").addClass("d-none");
			}
		});

		$("#showElementIf").change(function()
		{
			$("#displayTargetCategoryDiv").addClass("d-none").fadeOut();
			$("#displayTargetTagDiv").addClass("d-none").fadeOut();
			$("#displayTargetPagesDiv").addClass("d-none").fadeOut();
			$("#displayTargetAttDiv").addClass("d-none").fadeOut();
			$("#displayTargetCustomDiv").addClass("d-none").fadeOut();
			$("#loader1").removeClass("d-none");
			
			var id = $(this).val();
			
			id = id.trim();
			
			if ( id === 'category' )
			{
				setTimeout(function(){
					$("#loader1").addClass("d-none");
					$("#displayTargetCategoryDiv").removeClass("d-none");
				},500);
			}
			
			else if ( id === 'page' )
			{
				setTimeout(function(){
					$("#loader1").addClass("d-none");
					$("#displayTargetPagesDiv").removeClass("d-none");
				},500);
			}
			
			else if ( id === 'custom-post-type' )
			{
				setTimeout(function(){
					$("#loader1").addClass("d-none");
					$("#displayTargetCustomDiv").removeClass("d-none");
				},500);
			}
			
			else if ( id === 'attribute' )
			{
				setTimeout(function(){
					$("#loader1").addClass("d-none");
					$("#displayTargetAttDiv").removeClass("d-none");
				},500);
			}
			
			else if ( id === 'tag' )
			{
				setTimeout(function(){
					$("#loader1").addClass("d-none");
					$("#displayTargetTagDiv").removeClass("d-none");
				},500);
			}
			
			else
			{
				$("#loader1").addClass("d-none");
			}
		});
		
		$("#tagsDisplay").change(function()
		{
			$("#tagsIncludedDiv").addClass("d-none").fadeOut();
			$("#tagsExcludedDiv").addClass("d-none").fadeOut();
			$("#loader4").removeClass("d-none");
			
			var id = $(this).val();
			
			id = id.trim();
			
			if ( id === 'selected' )
			{
				setTimeout(function(){
					$("#loader4").addClass("d-none");
					$("#tagsIncludedDiv").removeClass("d-none").fadeIn();
				},500);
			}
			
			else if ( id === 'except' )
			{
				setTimeout(function(){
					$("#loader4").addClass("d-none");
					$("#tagsExcludedDiv").removeClass("d-none").fadeIn();
				},500);;
			}
			else
			{
				$("#loader4").addClass("d-none");
			}
		});
		
		<?php 
		if ( !empty( $groupData['category-display'] ) && ( $groupData['category-display'] == 'selected' ) )
		{
			echo '$("#categoryIncludedDiv").removeClass("d-none");' . PHP_EOL;
		}
		
		elseif ( !empty( $groupData['category-display'] ) && ( $groupData['category-display'] == 'except' ) )
		{
			echo '$("#categoryExcludedDiv").removeClass("d-none");' . PHP_EOL;
		}
		?>
		
		$("#categoryDisplay").change(function()
		{
			$("#categoryIncludedDiv").addClass("d-none").fadeOut();
			$("#categoryExcludedDiv").addClass("d-none").fadeOut();
			$("#loader3").removeClass("d-none");
			
			var id = $(this).val();
			
			id = id.trim();
			
			if ( id === 'selected' )
			{
				setTimeout(function(){
					$("#loader3").addClass("d-none");
					$("#categoryIncludedDiv").removeClass("d-none").fadeIn();
				},500);
			}
			
			else if ( id === 'except' )
			{
				setTimeout(function(){
					$("#loader3").addClass("d-none");
					$("#categoryExcludedDiv").removeClass("d-none").fadeIn();
				},500);
			}
			
			else
			{
				$("#loader3").addClass("d-none");
			}
		});
		
		<?php if ( isset( $groupData['source'] ) )
		{
			switch($groupData['source'])
			{      
				case 'category':      
					echo '$("#categoryDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'attribute-group':      
					echo '$("#attributeGroupDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'attribute':      
					echo '$("#attributeDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'merchants':      
					echo '$("#storesDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'manufacturers':      
					echo '$("#manufacturersDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'custom-post-type':      
					echo '$("#customTypeDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'stock-status':      
					echo '$("#stockDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'tag':      
					echo '$("#tagsDiv").removeClass("d-none");' . PHP_EOL;
				break;
				
				case 'custom-filters':      
					echo '$("#filterValues").removeClass("d-none");' . PHP_EOL;
					echo '$("#hasFilters").attr("value", "true");' . PHP_EOL;
				break;
			}
		}
		
		if ( isset( $groupData['target'] ) )
		{
			switch($groupData['target'])
			{      
				case 'attribute':      
					echo '$("#targetAttributeDiv").removeClass("d-none");' . PHP_EOL;
				break;
			}
		}
		?>
		
		//Target Options
		$("#targetOptions").change(function()
		{
			$("#targetAttributeDiv").addClass("d-none");
			$("#loader6").removeClass("d-none");
			
			var id = $(this).val();
			
			id = id.trim();
			
			if ( id === 'attribute' )
			{
				setTimeout(function(){
					$("#loader6").addClass("d-none");
					$("#targetAttributeDiv").removeClass("d-none").fadeIn();
				},800);
			}
			else
			{
				$("#loader6").addClass("d-none");
			}
			
		});
		
		//Source Options
		$("#sourceOptions").change(function()
		{
			$("#storesDiv").addClass("d-none");
			$("#manufacturersDiv").addClass("d-none");
			$("#attributeDiv").addClass("d-none");
			$("#categoryDiv").addClass("d-none");
			$("#tagsDiv").addClass("d-none");
			$("#priceOrderDiv").addClass("d-none");
			$("#stockDiv").addClass("d-none");
			$("#customTypeDiv").addClass("d-none");
			$("#filterValues").addClass("d-none");
			$("#hasFilters").attr("value", "false");

			var id = $(this).val();
			
			id = id.trim();
			
			if ( id !== 'custom-filters' )
			{
				$("#loader2").removeClass("d-none");
			}
			
			if ( id === 'attribute-group' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#attributeGroupDiv").removeClass("d-none").fadeIn();
				},800);
			}
			
			else if ( id === 'custom-filters' )
			{
				setTimeout(function(){
					$("#loader7").addClass("d-none");
					$("#filterValues").removeClass("d-none").fadeIn();
					$("#hasFilters").attr("value", "true");
				},800);
			}
			
			else if ( id === 'manufacturers' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#manufacturersDiv").removeClass("d-none").fadeIn();
				},800);
			}
			
			else if ( id === 'merchants' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#storesDiv").removeClass("d-none").fadeIn();
				},800);
			}
			
			else if ( id === 'attribute' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#attributeDiv").removeClass("d-none").fadeIn();
				},800);
			}
			
			else if ( id === 'custom-post-type' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#customTypeDiv").removeClass("d-none").fadeIn();
				},800);
			}
			
			else if ( id === 'stock-status' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#stockDiv").removeClass("d-none").fadeIn();
				},800);
			}
			
			else if ( id === 'prices' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#priceOrderDiv").removeClass("d-none").fadeIn();
				},800);
			}
			
			else if ( id === 'tag' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#tagsDiv").removeClass("d-none").fadeIn();
				},800);
			}
			
			else if ( id === 'category' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#categoryDiv").removeClass("d-none").fadeIn();
				},800);
			}
			else
			{
				$("#loader2").addClass("d-none");
			}
		});	
	});
	</script>