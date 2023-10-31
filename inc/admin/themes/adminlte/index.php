<!DOCTYPE html>
<html lang="<?php echo Theme::Locale() ?>">
<head>
	<?php include( ADMIN_THEME_PHP_ROOT . 'header.php' ) ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
	<div class="wrapper">
	
		<?php include( ADMIN_THEME_PHP_ROOT . 'navbar.php' ) ?>
	
		<?php include( ADMIN_THEME_PHP_ROOT . 'aside.php' ) ?>

		<div class="content-wrapper">
			<?php include( ADMIN_THEME_PHP_ROOT . 'content-header.php' ) ?>

			<section class="content">
				<?php include( ADMIN_THEME_PHP_ROOT . 'content.php' ) ?>
			</section>
		</div>

<?php include( ADMIN_THEME_PHP_ROOT . 'footer.php' ) ?>

</div>

<?php include( ADMIN_THEME_PHP_ROOT . 'footer-code.php' ) ?>

<?php echo $Admin->FooterCode() ?>

</body>
</html>