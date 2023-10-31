<?php
	$v2Arr = array(
		'deleteCheckBoxAlert' => __( 'are-you-sure-this-action-cannot-be-undone' ),
		'confirmAlert' => __( 'are-you-sure-this-action-cannot-be-undone' ),
		'confirmAlert2' => __( 'are-you-sure' ),
		'siteLogoDefaultImg' => HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS . 'default.svg',
		'token' => $Admin->GetToken(),
		'siteID' => $Admin->GetSite(),
		'blogID' => $Admin->GetBlog(),
		'langId' => $Admin->GetLang(),
		'ajaxUploadUri' => AJAX_ADMIN_PATH . 'logo-upload/',
		'draftUri' => AJAX_ADMIN_PATH . 'add-draft/',
		'editThemeUri' => AJAX_ADMIN_PATH . 'theme-edit/',
		'themeUri' => AJAX_ADMIN_PATH . 'theme/',
	);
	
	$v2Arr = json_encode( $v2Arr, JSON_UNESCAPED_UNICODE );
	
	$db = db();
?>

<script type="text/javascript" id='values'>
/* <![CDATA[ */
	var confirmChange = false;
	var v2 = <?php echo $v2Arr ?>;
/* ]]> */
</script>

<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/jquery-knob/jquery.knob.min.js"></script>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/moment/moment.min.js"></script>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/sweetalert2/sweetalert2.min.js"></script>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!--
<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
-->
<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/multicheck/datatable-checkbox-init.js"></script>
<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/multicheck/jquery.multicheck.js"></script>
<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/DataTables/datatables.min.js"></script>
<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/select2/dist/js/select2.full.min.js"></script>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/js/adminlte.js?v=3.2.0"></script>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/js/scripts.js"></script>

<script type='text/javascript'>
$(function () {
    $('#cp').colorpicker();
});

var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 4000
});
</script>

<?php
if ( ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) && IsAllowedTo( 'admin-site' ) && isset( $Admin->Settings()::LogSettings()['disable_javascript_call_scheduled_tasks'] ) && !$Admin->Settings()::LogSettings()['disable_javascript_call_scheduled_tasks'] ) : 
	
	$cronUrl = CRON_URL;
	
	if ( !$Admin->IsDefaultSite() )
	{
		$cronUrl .= '&site=' . $Admin->GetSite();
	}
?>
<script>
	setInterval(
		function (){
			$.get('<?php echo $cronUrl ?>',function (){});
		},10000
	);
</script>
<?php endif ?>

<?php if ( $Admin->Settings()::IsTrue( 'enable_html5_video_player' ) ) : ?>
<script src="https://cdn.plyr.io/3.7.2/plyr.js"></script>

<script type='text/javascript'>
const player = new Plyr('video');
window.player = player;
</script>
<?php endif ?>

<script type='text/javascript'>
$(document).ready(function()
{
	$("#logButton").click(function (e)
	{
		e.preventDefault();
		
		var items 	= "<?php echo $Admin->GetLogCounts()['totalNotes'] ?>";
		var user 	= "<?php echo $Admin->UserID() ?>";
		var site 	= "<?php echo $Admin->GetSite() ?>";
		var lang 	= "<?php echo $Admin->GetLang() ?>";
		var blog 	= "<?php echo $Admin->GetBlog() ?>";
		var showAll = "<?php echo $Admin->Settings()::IsTrue( 'parent_site_shows_everything' ) ?>";
		
		if ( items == 0 )
			return;

		$.ajax({
			url: '<?php echo AJAX_ADMIN_PATH ?>add-logs/',
			type: 'POST',
			data: {site:site,user:user,lang:lang,blog:blog,showAll:showAll},
			cache: false
		})
		.done(function(data){
			$("#logBadge").addClass("d-none");
		})
		.fail(function(){
			console.log("Add logs error");
		});
	});
});
</script>

<?php 

if ( ( $Admin->CurrentAction() == 'posts' ) || ( $Admin->CurrentAction() == 'pages' ) )
{
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'posts.php' );
}

if ( $Admin->CurrentAction() == 'edit-theme' )
{
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'edit-theme.php' );
}

if ( $Admin->CurrentAction() == 'add-source' )
{
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'sources.php' );
}

if ( $Admin->CurrentAction() == 'edit-content-source' )
{
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'source.php' );
}

if ( ( Router::GetVariable( 'subAction' ) == 'sync' ) && ( ( $Admin->CurrentAction() == 'posts' ) || ( $Admin->CurrentAction() == 'pages' ) ) )
{
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'map-posts.php' );
}

if ( $Admin->CurrentAction() == 'move-posts' )
{
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'move-posts.php' );
}

if ( $Admin->CurrentAction() == 'edit-lang' )
{
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'language.php' );
}
?>

<?php if ( $Admin->CurrentAction() == 'post-settings' ) : ?>
	<script type="text/javascript"><!--
	function check()
	{
		var check = $("#front-posts option:selected").val();
		$("#static-page-div").addClass("d-none");
		check = check.trim();

		if ( check === 'static-page' )
		{
			$("#static-page-div").removeClass("d-none");
		}
		else
		{
			$("#static-page").val('').trigger("change");
		}
	}
	//--></script>
<?php endif ?>

<?php 
if ( ( $Admin->CurrentAction() == 'add-filter' ) || ( $Admin->CurrentAction() == 'edit-filter' ) ) :

	//Get the last ID
	$f = $db->from( 
	null, 
	"SELECT id
	FROM `" . DB_PREFIX . "filters_data`
	WHERE (id_group = " . $Filter['id'] . ")
	ORDER BY id DESC
	LIMIT 1"
	)->single();
	
	$filter_row = ( $f ? ( $f['id'] + 1 ) : 0 );

	if ( $Admin->CurrentAction() == 'edit-filter' )
	{		
		include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'edit-filter.php' );
	}
	?>
	
	<script type="text/javascript"><!--
		var filter_row = <?php echo $filter_row ?>;

		$('#button-filter').on('click', function() {
			html = '<tr id="filter-row-' + filter_row + '">';
			html += '  <td class="text-start" style="width: 70%;"><input type="hidden" name="filter[' + filter_row + '][filter_id]" value=""/>';
			html += '  <div class="input-group">';
			html += '    <input type="text" name="filter[' + filter_row + '][name]" value="" placeholder="<?php echo __( 'filter-name' ) ?>" id="input-filter-' + filter_row + '-1" class="form-control"/>';
			html += '  </div>';
			html += '  <div id="error-filter-' + filter_row + '-1" class="invalid-feedback"></div>';

			  html += '  </td>';
			html += '  <td class="text-end"><input type="text" name="filter[' + filter_row + '][sort_order]" value="" placeholder="<?php echo __( 'sort-order' ) ?>" id="input-sort-order" class="form-control"/></td>';
			html += '  <td class="text-end"><button type="button" onclick="$(\'#filter-row-' + filter_row + '\').remove();" data-bs-toggle="tooltip" title="<?php echo __( 'remove' ) ?>" class="btn btn-danger"><i class="fas fa-minus-circle"></i></button></td>';
			html += '</tr>';

			$('#filter tbody').append(html);

			filter_row++;
		});
//--></script>
<?php endif ?>

<?php if ( ( $Admin->CurrentAction() == 'logs' ) || ( $Admin->CurrentAction() == 'task-log' ) ) : ?>
<script type='text/javascript'>
$(function () {
	$('#logsTable').DataTable({
		columnDefs: [
			{ "searchable": true, "targets": [0,1] },
			{ "orderable": false, "targets": [2] },
			{ className: 'text-center', targets: [0,1,2,3,4] }
		],
		order: [[3, 'desc']],
		"paging": true,
		"lengthChange": false,
		"searching": false,
		"ordering": true,
		"info": false,
		"autoWidth": false,
		"responsive": true,
    });
});
</script>
<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'comments' ) : ?>
<script type='text/javascript'>
$(function () {
	$('#commentsTable').DataTable({
		columnDefs: [
			{ "searchable": true, "targets": [0,1] },
			{ "orderable": false, "targets": [-1] },
			{ className: 'text-center', targets: [0,1,2,3,4] }
		],
		order: [[3, 'desc']],
		"paging": true,
		"lengthChange": false,
		"searching": true,
		"ordering": true,
		"info": false,
		"autoWidth": false,
		"responsive": true,
    });
});
</script>
<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'import' )
{
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'import.php' );
} ?>

<?php if ( $Admin->CurrentAction() == 'add-site' ) : ?>

<script type="application/javascript">
	$(document).ready(function()
	{
		$("#selfHostedData").removeClass("d-none");
		
		$("#slcHosts").change(function()
		{
			$("#selfHostedData").addClass("d-none").fadeOut();
			$("#bloggerData").addClass("d-none").fadeOut();
			$("#wpData").addClass("d-none").fadeOut();
			$("#loader2").removeClass("d-none");
			$("#inputBloggerClientApi").val("");
			$("#inputBloggerClientSecret").val("");
			$("#inputWpClientApi").val("");
			$("#inputWpClientSecret").val("");
			
			var id = $(this).val();
			
			id = id.trim();

			if ( id === 'self' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#selfHostedData").removeClass("d-none");
				},500);
			}
			
			else if ( id === 'blogger' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#bloggerData").removeClass("d-none");
				},500);
			}
			
			else if ( id === 'wordpress' )
			{
				setTimeout(function(){
					$("#loader2").addClass("d-none");
					$("#wpData").removeClass("d-none");
				},500);
			}

			else
			{
				$("#loader2").addClass("d-none");
			}
		});
	});
</script>		
<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'stats' ) : ?>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/Chart.js/chart.bundle.min.js"></script>

<script type='text/javascript'>
	var visitChartCanvas = document.getElementById('visits-chart').getContext('2d');

	var visitChartData =
	{
		labels: [<?php echo implode( ',', $Stats['days'] ) ?>],
		datasets:[{
			backgroundColor: '#17a2b8',
			borderColor: '#17a2b8',
			pointBorderColor:'#17a2b8',
			pointBackgroundColor:'#17a2b8',
			fill:false,
			label: "<?php echo $Stats['htitle'] ?>",
			data:[<?php echo implode( ',', $Stats['h'] ) ?>]
		},
		{
			backgroundColor: '#adb5bd',
			borderColor: '#adb5bd',
			pointBorderColor:'#adb5bd',
			pointBackgroundColor:'#adb5bd',
			fill:false,
			label: "<?php echo $Stats['vtitle'] ?>",
			data:[<?php echo implode( ',', $Stats['v'] ) ?>]
		}]
	}

	var visitChartOptions={
		maintainAspectRatio:false,responsive:true,legend:{display:false},tooltips:{mode:'index',intersect:true},hover:{mode:'index',intersect:true},legend:{display:false},scales: {
		yAxes: [{
			ticks: {
				beginAtZero: true,
				stepSize: 1
			}
		}]
	}}
	var visitChart = new Chart(visitChartCanvas,{type:'bar',data:visitChartData,options:visitChartOptions})
	
	//Browsers
	var browserChartCanvas=$('#browseChart').get(0).getContext('2d')
	var pieData={labels:['<?php echo implode( '\',\'', $Stats['browserArr'] ) ?>'],datasets:[{data:[<?php echo implode( ',', $Stats['browserHits'] ) ?>],backgroundColor:['<?php echo implode( '\',\'', $Stats['browserColor'] ) ?>']}]}
	var pieOptions={legend:{display:false}}
	var pieChart=new Chart(browserChartCanvas,{type:'doughnut',data:pieData,options:pieOptions})
	
	//Platforms (OS)
	var osChartCanvas=$('#osChart').get(0).getContext('2d')
	var pieOsData={labels:['<?php echo implode( '\',\'', $Stats['osArr'] ) ?>'],datasets:[{data:[<?php echo implode( ',', $Stats['osHits'] ) ?>],backgroundColor:['<?php echo implode( '\',\'', $Stats['osColor'] ) ?>']}]}
	var pieOsOptions={legend:{display:false}}
	var pieOsChart=new Chart(osChartCanvas,{type:'doughnut',data:pieOsData,options:pieOsOptions})
</script>
<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'dashboard' ) : ?>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/Chart.js/chart.bundle.min.js"></script>

<?php AdminStatsChart() ?>

<?php Modal( AdminDashboardWidgets() ) ?>

<?php include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'dashboard.php' ) ?>

<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'edit-form' ) : 
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'forms.php' );
endif ?>

<?php if ( $Admin->CurrentAction() == 'edit-table' ) : 
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'tables.php' );
endif ?>

<?php if ( $Admin->CurrentAction() == 'automatic-translator' ) : 
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'automatic-translator.php' );
endif ?>

<?php if ( $Admin->CurrentAction() == 'categories' ) : 
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'categories.php' );
endif ?>

<?php if ( ( $Admin->CurrentAction() == 'edit-post' ) || ( $Admin->CurrentAction() == 'add-post' ) || ( $Admin->CurrentAction() == 'add-page' ) || ( $Admin->CurrentAction() == 'edit-comment' ) ) : ?>

    <script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    
	<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/choices/public/assets/scripts/choices.min.js"></script>
	
	<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/jquery.repeatable/jquery.repeatable.js"></script>
	
	<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/tagify/jQuery.tagify.min.js"></script>

	<script src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/js/post.js"></script>
		
	<?php
	if ( ( $Admin->CurrentAction() == 'edit-post' ) && $canViewAttachments ) : 
		
		$modal = array(
				'title' => __( 'image-manager' ),
				'id' => 'addImage',
				'extra' => 'data-replace="true", data-focus-on="input:first"',
				'size' => 'xl',
				'fade' => true,
				'loader' => true,
		);

		Modal( $modal );
		
		endif;
		
	endif;
	?>
	
	<script type="text/javascript">		
		$("#publishButton").on("click", function(e)
		{
			confirmChange = false;
		});
		
		$("#draftButton").on("click", function(e)
		{
			confirmChange = false;
		});
		
		$("#updateButton").on("click", function(e)
		{
			confirmChange = false;
		});
		
		$("#deleteButton").on("click", function(e)
		{
			confirmChange = false;
		});
	</script>

<?php 
if ( $Admin->CurrentAction() == 'edit-comment' ) : 
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'comment.php' );
endif;
?>

<?php 
if ( $Admin->CurrentAction() == 'edit-post' ) : 

	if ( $hasCoupons )
	{
		include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'coupons.php' );
	}
	
	if ( $hasPrices )
	{
		include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'prices.php' );
	}
	
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'post.php' );
?>

<?php if ( $canManufact ) : ?>
	<script type="application/javascript">
	$(document).ready(function() {
		var manufacturer = $("#selManufacturer").select2({
			placeholder: "",
			allowClear: true,
			theme: "bootstrap4",
			minimumInputLength: 2,
			ajax: {
					type: "POST",
					url: "<?php echo AJAX_ADMIN_PATH ?>get-manufacturers/",
					data: function (params) {
						var query = {
							postSite: "<?php echo $Post->Site()->id ?>",
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
	</script>
	<?php endif ?>
<?php endif ?>

<?php 
if ( ( $Admin->CurrentAction() == 'edit-post' ) || ( $Admin->CurrentAction() == 'add-post' ) ) :
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'tags.php' );
endif ?>

<?php if ( $Admin->CurrentAction() == 'emails' ) :
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'mails.php' );
endif; ?>

<?php if ( ( $Admin->CurrentAction() == 'reply-mail' ) || ( $Admin->CurrentAction() == 'forward-mail' ) ) : ?>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/summernote/summernote-bs4.min.js"></script>

<?php include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'mail.php' );
endif; ?>

<?php if ( ( $Admin->CurrentAction() == 'api' ) && !empty( $apisArray ) ) : ?>
<script type="application/javascript">
$( document ).ready(function() 
{
	$('<?php echo implode( ",", $apisArray ) ?>').change(function() {
		var id = $(this).val();
		$.ajax({
            url: '<?php echo AJAX_ADMIN_PATH ?>apis-edit/',
            method: 'post',
			dataType: 'json',
			cache: false,
            data: {checked: this.checked, id: id},
			success: function (data)
            {
				if ( data.status == 'ok' ) 
				{
					Toast.fire({
						icon: "success",
						title: data.message
					})
				} else 
				{
					Toast.fire({
						icon: "error",
						title: "<?php echo __( 'an-error-happened' ) ?>"
					})
				}
            }
        });
	});
});
</script>
<?php endif ?>

<?php if ( ( $Admin->CurrentAction() == 'menus' ) || ( $Admin->CurrentAction() == 'edit-menu' ) ) : ?>
<script src="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/nestedSortable/jquery.mjs.nestedSortable.js"></script>

<?php include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'menu.php' ) ?>

<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'widgets' ) : 
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'widgets.php' );
endif; ?>

<?php if ( $Admin->CurrentAction() == 'edit-vendor' ) : 

	Modal(
		array(
			'title' => __( 'image-manager' ),
			'id' => 'addImage',
			'size' => 'xl',
			'fade' => true,
			'loader' => true,
		)
	);
?>

<script type="application/javascript">
	$(document).on('click', '#featuredImageModal', function(e)
	{
			e.preventDefault();
			var post = '0';
			var action = ''; //TODO?
			var calledFrom = 'vendor';
			var token = '<?php echo $Admin->GetToken() ?>';
			$('#post-detail').html(''); 
			$('#modal-loader').show();  
			$.ajax(
			{
				url: '<?php echo AJAX_ADMIN_PATH ?>media-manager-gallery/',
				type: 'POST',
				data: {action:action,post:post,token:token,calledFrom:calledFrom},
				dataType: 'html',
				cache: false
			})
			.done(function(data)
			{
				console.log(data);	
				$('#post-detail').html('');    
				$('#post-detail').html(data);
				$('#modal-loader').hide();
			 })
			 .fail(function(){
				$('#post-detail').html('Error. Please try again...');
				$('#modal-loader').hide();
			});
	});
		
		$("#buttonRemoveLogo").on("click", function()
		{
			$("#logoPreview").attr("src", "");
			$("#logoFile").val('');
			$("#buttonRemoveLogo").addClass("d-none");
			$("#logoPreview").attr("src", "<?php echo HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS ?>default.svg");
		});
</script>
<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'edit-store' ) : 

	Modal(
		array(
			'title' => __( 'image-manager' ),
			'id' => 'addImage',
			'size' => 'xl',
			'fade' => true,
			'loader' => true,
		)
	);
	
	include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'edit-store.php' );

endif; ?>

<?php if ( $Admin->CurrentAction() == 'edit-category' )  : 

	Modal(
		array(
			'title' => __( 'image-manager' ),
			'id' => 'addImage',
			'size' => 'xl',
			'fade' => true,
			'loader' => true,
		)
	);
?>

<?php include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'edit-category.php' ) ?>

<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'edit-custom-post-type' ) : 

	Modal(
		array(
			'title' => __( 'image-manager' ),
			'id' => 'addImage',
			'size' => 'xl',
			'fade' => true,
			'loader' => true,
		)
	);
?>

<script type="application/javascript">
	$(document).on('click', '#customImageModal', function(e)
	{
			e.preventDefault();
			var post = '0';
			var action = ''; //TODO?
			var calledFrom = 'custom-type';
			var token = '<?php echo $Admin->GetToken() ?>';
			$('#post-detail').html(''); 
			$('#modal-loader').show();  
			$.ajax(
			{
				url: '<?php echo AJAX_ADMIN_PATH ?>media-manager-gallery/',
				type: 'POST',
				data: {action:action,post:post,token:token,calledFrom:calledFrom},
				dataType: 'html',
				cache: false
			})
			.done(function(data)
			{
				console.log(data);	
				$('#post-detail').html('');    
				$('#post-detail').html(data);
				$('#modal-loader').hide();
			 })
			 .fail(function(){
				$('#post-detail').html('Error. Please try again...');
				$('#modal-loader').hide();
			});
	});
		
		$("#buttonRemoveLogo").on("click", function()
		{
			$("#customLogoPreview").attr("src", "");
			$("#customLogoFile").val('');
			$("#buttonRemoveLogo").addClass("d-none");
			$("#customLogoPreview").attr("src", "<?php echo HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS ?>default.svg");
		});
</script>
<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'edit-manufacturer' ) : 

	Modal(
		array(
			'title' => __( 'image-manager' ),
			'id' => 'addImage',
			'size' => 'xl',
			'fade' => true,
			'loader' => true,
		)
	);
?>

<script type="application/javascript">
	$(document).on('click', '#manufactImageModal', function(e)
	{
			e.preventDefault();
			var post = '0';
			var action = ''; //TODO?
			var calledFrom = 'manufacturer';
			var token = '<?php echo $Admin->GetToken() ?>';
			$('#post-detail').html(''); 
			$('#modal-loader').show();  
			$.ajax(
			{
				url: '<?php echo AJAX_ADMIN_PATH ?>media-manager-gallery/',
				type: 'POST',
				data: {action:action,post:post,token:token,calledFrom:calledFrom},
				dataType: 'html',
				cache: false
			})
			.done(function(data)
			{
				console.log(data);	
				$('#post-detail').html('');    
				$('#post-detail').html(data);
				$('#modal-loader').hide();
			 })
			 .fail(function(){
				$('#post-detail').html('Error. Please try again...');
				$('#modal-loader').hide();
			});
	});
		
		$("#buttonRemoveLogo").on("click", function()
		{
			$("#manufactLogoPreview").attr("src", "");
			$("#manufactLogoFile").val('');
			$("#buttonRemoveLogo").addClass("d-none");
			$("#manufactLogoPreview").attr("src", "<?php echo HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS ?>default.svg");
		});
</script>
<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'settings' ) : 

	Modal(
		array(
			'title' => __( 'image-manager' ),
			'id' => 'addImage',
			'size' => 'xl',
			'fade' => true,
			'loader' => true,
		)
	);
	?>
	
	<?php include( ADMIN_THEME_PHP_ROOT . 'footer-files' . PS . 'settings.php' ) ?>
	
	<?php endif ?>

	<script type="application/javascript">
		//Notify the user if accidentally pressed the refresh button
		window.onbeforeunload = function () {
			if (confirmChange) {
				return "<?php echo __( 'reload-page-alert' ) ?>";
			}
		}
	</script>