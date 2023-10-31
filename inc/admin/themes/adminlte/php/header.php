<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel='dns-prefetch' href='//fonts.googleapis.com' />

<title><?php echo Theme::HeaderTitle() ?></title>
<meta name="robots" content="noindex,nofollow" />
<meta name="description" content="Copyright <?php echo date( 'Y', time() ) ?> &amp;copy; BadTooth Studio. Version: <?php echo TOKI_VERSION ?>">

<!-- Blank Favicon icon -->
<link rel="shortcut icon" href="data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=" type="image/png">

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

<link rel="stylesheet" href="<?php echo HTML_ADMIN_PATH_THEME ?>assets/css/fontawesome-all.min.css">

<link rel="stylesheet" href="<?php echo HTML_ADMIN_PATH_THEME ?>assets/css/bootstrap-icons.css">	

<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

<link rel="stylesheet" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

<link rel="stylesheet" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/sweetalert2/bootstrap-4.min.css">

<link rel="stylesheet" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">


<link rel="stylesheet" href="<?php echo HTML_ADMIN_PATH_THEME ?>assets/css/adminlte.min.css?v=3.2.0">
<!--
<link rel="stylesheet" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
-->
<link rel="stylesheet" href="<?php echo HTML_ADMIN_PATH_THEME ?>assets/css/custom.css">

<link rel="stylesheet" type="text/css" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/select2/dist/css/select2.min.css" />

<link rel="stylesheet" type="text/css" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/select2/dist/css/select2-bootstrap4.min.css" />

<link rel="stylesheet" type="text/css" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/multicheck/multicheck.css" />

<link rel="stylesheet" type="text/css" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/DataTables/dataTables.bootstrap4.css" />

<link rel="stylesheet" type="text/css" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/bootstrap-colorpicker/bootstrap-colorpicker.min.css" />

<?php if ( ( $Admin->CurrentAction() == 'edit-post' ) || ( $Admin->CurrentAction() == 'add-post' ) || ( $Admin->CurrentAction() == 'add-page' ) || ( $Admin->CurrentAction() == 'edit-comment' ) || ( $Admin->CurrentAction() == 'edit-theme' ) ) : ?>
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/tagify/tagify.css">
	<link rel="stylesheet" type="text/css" href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" />
	
	<?php if ( $Admin->Settings()::Get()['html_editor'] == 'simplemde' ) : ?>
		<!-- EasyMDE Editor -->
		<link href="<?php echo TOOLS_HTML ?>easy-markdown-editor/src/css/easymde.css" rel="stylesheet">
	<?php endif ?>
	
	<?php if ( $Admin->Settings()::Get()['html_editor'] == 'tinymce' ) : ?>
		<!-- EasyMDE Editor -->
		<link href="<?php echo TOOLS_HTML ?>tinymce/css/lightmode-toolbar.css" rel="stylesheet">
	<?php endif ?>
	
	<?php if ( $Admin->Settings()::Get()['html_editor'] == 'simple' ) : ?>
		<style>.editor-toolbar { background: #f1f1f1; font-size: 1.2em; padding: 5px; border-color: #ddd;}.editor-toolbar::before { margin-bottom: 2px !important }.editor-toolbar::after { margin-top: 2px !important }</style>
	<?php endif ?>	
<?php endif ?>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/js/jquery.min.js"></script>

<script src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/js/jquery-ui.min.js"></script>

<?php if ( $Admin->CurrentAction() == 'dashboard' ) : ?>
<link href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/Chart.js/chart.min.css" rel="stylesheet">
<?php endif ?>

<?php if ( ( $Admin->CurrentAction() == 'reply-mail' ) || ( $Admin->CurrentAction() == 'forward-mail' ) ) : ?>
<link href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/summernote/summernote-bs4.min.css" rel="stylesheet">
<?php endif ?>

<?php if ( $Admin->CurrentAction() == 'import' ) : ?>
<link href="<?php echo HTML_ADMIN_PATH_THEME ?>plugins/bs-stepper/bs-stepper.min.css" rel="stylesheet">
<?php endif ?>

<?php if ( ( $Admin->CurrentAction() == 'menus' ) || ( $Admin->CurrentAction() == 'edit-menu' ) ) : ?>
<style>.menu-list{list-style:none;margin:0;overflow:auto;padding:0}.menu-list>li{border-radius:2px;background-color:#f8f9fa;border-left:2px solid #e9ecef;color:#495057;margin-bottom:2px;padding:10px}.menu-list>li:last-of-type{margin-bottom:0}.menu-list>li>input[type=checkbox]{margin:0 10px 0 5px}.menu-list>li .text{display:inline-block;font-weight:600;margin-left:5px}.menu-list>li .badge{font-size:.7rem;margin-left:10px}.menu-list>li .tools{color:#dc3545;display:none;float:right}.menu-list>li .tools>.fa,.menu-list>li .tools>.fab,.menu-list>li .tools>.fad,.menu-list>li .tools>.fal,.menu-list>li .tools>.far,.menu-list>li .tools>.fas,.menu-list>li .tools>.ion,.menu-list>li .tools>.svg-inline--fa{cursor:pointer;margin-right:5px}.menu-list>li:hover .tools{display:inline-block}.menu-list>li.done{color:#697582}.menu-list>li.done .text{font-weight:500;text-decoration:line-through}.menu-list>li.done .badge{background-color:#adb5bd!important}.menu-list .primary{border-left-color:#007bff}.menu-list .secondary{border-left-color:#6c757d}.menu-list .success{border-left-color:#28a745}.menu-list .info{border-left-color:#17a2b8}.menu-list .warning{border-left-color:#ffc107}.menu-list .danger{border-left-color:#dc3545}.menu-list .light{border-left-color:#f8f9fa}.menu-list .dark{border-left-color:#343a40}.menu-list .lightblue{border-left-color:#3c8dbc}.menu-list .navy{border-left-color:#001f3f}.menu-list .olive{border-left-color:#3d9970}.menu-list .lime{border-left-color:#01ff70}.menu-list .fuchsia{border-left-color:#f012be}.menu-list .maroon{border-left-color:#d81b60}.menu-list .blue{border-left-color:#007bff}.menu-list .indigo{border-left-color:#6610f2}.menu-list .purple{border-left-color:#6f42c1}.menu-list .pink{border-left-color:#e83e8c}.menu-list .red{border-left-color:#dc3545}.menu-list .orange{border-left-color:#fd7e14}.menu-list .yellow{border-left-color:#ffc107}.menu-list .green{border-left-color:#28a745}.menu-list .teal{border-left-color:#20c997}.menu-list .cyan{border-left-color:#17a2b8}.menu-list .white{border-left-color:#fff}.menu-list .gray{border-left-color:#6c757d}.menu-list .gray-dark{border-left-color:#343a40}.menu-list .handle{cursor:move;display:inline-block;margin:0 5px}li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div {border-color: #999;}ol {max-width: 450px;padding-left: 25px;}ol.sortable,ol.sortable ol {list-style-type: none;}.sortable li.mjs-nestedSortable-collapsed > ol {display: none;}.sortable li.mjs-nestedSortable-branch > div > .disclose {display: inline-block;}.menu-list>li {border: 1px solid black;background: #fff;}ol li{border-radius:2px;background: #f8f9fa;border-left:2px solid #e9ecef;color:#495057;margin-bottom:2px;padding:10px}.mjs-nestedSortable-error{background:#fbe3e4;border-color:transparent}#tree{width:550px;margin:0}.placeholder{background:#fbe3e1!important;height:50px!important;visibility:visible!important;margin:0 0 -10px 0;outline:1.5px dashed #fbe3e1}li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div{border-color:#999}.disclose,.expandEditor{cursor:pointer;width:20px;display:none}.sortable li.mjs-nestedSortable-collapsed>ol{display:none}.sortable li.mjs-nestedSortable-branch>div>.disclose{display:inline-block}.sortable span.ui-icon{display:inline-block;margin:0;padding:0}.menuDiv{background:#EBEBEB}</style>
<?php endif ?>

<?php if ( $Admin->Settings()::IsTrue( 'enable_html5_video_player' ) ) : ?>
<link href="https://cdn.plyr.io/3.7.2/plyr.css" rel="stylesheet">
<?php endif ?>

<style>#addImage {z-index: 1051 !important;}table.table td .intools{color:#dc3545;display:none;float:left}table.table td:hover .intools{display:inline}.tagify--outside{border: 0;}

.tagify--outside .tagify__input{
  order: -1;
  flex: 100%;
  border: 1px solid var(--tags-border-color);
  margin-bottom: 1em;
  transition: .1s;
}

.tagify--outside .tagify__input:hover{ border-color:var(--tags-hover-border-color); }
.tagify--outside.tagify--focus .tagify__input{
  transition:0s;
  border-color: var(--tags-focus-border-color);
}
.tooltip { z-index: 1180 !important; }</style>

<?php echo $Admin->HeaderCode() ?>