﻿<?php defined('TOKICMS') || die('Hacking attempt...'); ?><!DOCTYPE html>
<html lang="<?php echo Theme::Locale() ?>">
<head>
	<?php include( TOOLS_THEME_PHP_ROOT . 'head.php' ) ?>
</head>
    <body>
        <div class="main-container">
			<?php include( TOOLS_THEME_PHP_ROOT . 'out.php' ) ?>
        </div>
		<script>
			window.setTimeout(function(){
				window.location.href = "<?php echo $Url ?>";
			}, 3000);
		</script>
        <?php include( TOOLS_THEME_PHP_ROOT . 'footer.php' ) ?>
    </body>
</html>