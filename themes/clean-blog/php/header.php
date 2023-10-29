<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	
<title><?php echo Theme::HeaderTitle() ?></title>
<meta name="description" content="<?php echo Theme::Description() ?>">
<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_THEME ?>css/bootstrap.min.css" id="bootstrap-css">

<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_THEME ?>css/clean-blog.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTML_PATH_THEME ?>css/font-awesome.min.css">

<link rel='dns-prefetch' href='//fonts.googleapis.com' />

<link href='https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<style type='text/css'>.author-image{display:block;width:80px;height:80px;float:left;background-size:cover;border-radius:100%;text-indent:-9999px}p.published{color:#999}</style>

<?php echo Theme::HeaderCode() ?>
	
<?php if ( ThemeValue( 'enable-portfolio' ) ) : ?>
<style>.gallery-title{font-size:36px;color:#FFA500;text-align:center;font-weight:500;margin-bottom:70px}.gallery-title:after{content:"";position:absolute;width:22.5%;left:38.5%;height:45px;border-bottom:1px solid #5e5e5e}.btn-default:active .filter-button:active{background-color:#FFA500;color:#fff}.port-image{width:100%}.gallery_product{margin-bottom:30px}img{box-shadow:1px 1px 5px 1px rgba(0,0,0,.1);border-radius:5px}</style>
<?php endif ?>