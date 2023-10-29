<?php defined('TOKICMS') || die('Hacking attempt...'); 
/*
	Theme based on maintenance plugin for WP by WebFactory
	Plugin URI: https://wordpress.org/plugins/maintenance/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
?><!DOCTYPE html>
<html lang="<?php echo Theme::Locale() ?>">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, minimum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="" />
	<title><?php echo Theme::HeaderTitle() ?></title>
	<meta name="description" content="<?php echo Theme::Description() ?>"/>
	<link rel="dns-prefetch" href="//fonts.googleapis.com" />
	<link rel="dns-prefetch" href="//ajax.googleapis.com" />
	<link rel="dns-prefetch" href="//cdnjs.cloudflare.com" />

	<link rel="stylesheet" id="style-css" href="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/css/style-offline.css" media="all" />
	<link rel="stylesheet" id="fonts-css" href="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/css/fonts.css" media="all" />
	
	<style type="text/css">body {background-color: #111111}.preloader {background-color: #111111}body {font-family: Open Sans; }.site-title, .preloader i, .login-form, .login-form a.lost-pass, .btn-open-login-form, .site-content, .user-content-wrapper, .user-content, footer, .maintenance a{color: #ffffff;} a.close-user-content, #mailchimp-box form input[type="submit"], .login-form input#submit.button  {border-color:#ffffff} input[type="submit"]:hover{background-color:#ffffff} input:-webkit-autofill, input:-webkit-autofill:focus{-webkit-text-fill-color:#ffffff} body &gt; .login-form-container{background-color:#111111}.btn-open-login-form{background-color:#111111}input:-webkit-autofill, input:-webkit-autofill:focus{-webkit-box-shadow:0 0 0 50px #111111 inset}input[type='submit']:hover{color:#111111} #custom-subscribe #submit-subscribe:before{background-color:#111111} </style>
	<!--[if IE]>
	<script type="text/javascript" src="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/js/jquery.backstretch.min.js"></script>
	<![endif]-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open%20Sans:300,300italic,regular,italic,600,600italic,700,700italic,800,800italic:300">
</head>

<body class="maintenance ">
<div class="main-container">
	<div class="preloader"><i class="fi-widget" aria-hidden="true"></i></div>
		<div id="wrapper">
			<div class="center logotype">
				<header>
					<div class="logo-box istext" rel="home">
						<h1 class="site-title"><?php echo Theme::SiteName() ?></h1>
					</div>
				</header>
			</div>
			<div id="content" class="site-content">
				<div class="center">
				<?php if ( !empty( $Offline['maintenance_subject'] ) ) : ?>
					<h2 class="heading font-center" style="font-weight:300;font-style:normal"><?php echo $Offline['maintenance_subject'] ?></h2>
					<?php endif ?>
					<?php if ( !empty( $Offline['maintenance_text'] ) ) : ?>
					<div class="description" style="font-weight:300;font-style:normal">
						<p><?php echo $Offline['maintenance_text'] ?></p>
					</div>
					<?php endif ?>
				</div>
			</div>
		</div> <!-- end wrapper -->
		<?php if ( !empty( $Offline['footer_text'] ) ) : ?>
		<footer>
			<div class="center">
				<div style="font-weight:300;font-style:normal"><?php echo $Offline['footer_text'] ?></div>
			</div>
		</footer>
		<?php endif ?>
		<?php if ( !empty( $Offline['background_image'] ) ) : ?>
		<picture class="bg-img">
				<img src="<?php echo $Offline['background_image'] ?>">
		</picture>
		<?php endif ?>
	</div>
	<?php if ( Settings::IsTrue( 'enable_login_maintenance', 'site' ) ) : ?>
	<div class="login-form-container ">
		<div class="btn-open-login-form">
			<a href="<?php echo SITE_URL ?>login/" rel="nofollow"><i class="fi-lock"></i></a>
		</div>
	</div>
	<?php endif ?>
	<!--[if lte IE 10]>
	<script src='//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js?ver=5.9' id='jquery_ie-js'></script>
	<![endif]-->
	<!--[if !IE]><!-->
	<script src='//ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js?ver=3.6.0' id='jquery-core-js'></script>
	<script src='//cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.3.2/jquery-migrate.min.js?ver=3.3.2' id='jquery-migrate-js'></script>
	<!--<![endif]-->
	<script id='_frontend-js-extra'>
	var mtnc_front_options = {"body_bg":"<?php echo str_replace( '/', '\/', Settings::Maintenance()['background_image'] ) ?>","gallery_array":[],"blur_intensity":"5","font_link":["Open Sans:300,300italic,regular,italic,600,600italic,700,700italic,800,800italic:300"]};
	</script>
	<script src="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/js/jquery.frontend.js" id="_frontend-js"></script>
</body>
</html>
<!-- Powered by Toki CMS (https://badtooth.studio/tokicms/) -->