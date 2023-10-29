<!DOCTYPE html>
<html lang="<?php echo Theme::Locale() ?>">
<head>
	<?php include(THEME_DIR_PHP . 'header.php') ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-custom navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo Theme::SiteUrl() ?>"><?php echo Theme::SiteName() ?></a>
            </div>

			<?php Menu( CleanBlogMenu(), true, false, false ) ?>

        </div>
        <!-- /.container -->
    </nav>
	
	<?php
		if ( $WhereAmI == 'post' )
			include(THEME_DIR_PHP . 'page.php');
		else 
			include(THEME_DIR_PHP . 'home.php');
	?>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
				<?php if ( ThemeValue( 'show-social-menu-on-footer' ) ) :
						CleanSocialFooter();
					endif;
				?>
                    <div class="copyright text-muted">
						<p>Copyright &copy; <a href="<?php echo Theme::SiteUrl() ?>"><?php echo Theme::SiteName() ?></a></p>
						<p>Powered by <a href="https://badtooth.studio/tokicms/" target="_blank">Toki CMS</a></p>
						<p><?php echo Theme::LangSelector() ?></p>
					</div>
                </div>
            </div>
        </div>
    </footer>

<?php echo Theme::FooterCode() ?>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<?php if ( ThemeValue( 'enable-portfolio' ) ) : ?>
<script>
$(document).ready(function(){
	$(".filter-button").click(function(){
	var value = $(this).attr('data-filter');

	if(value == "all")
	{

	$('.filter').show('1000');
	}
	else
	{

	$(".filter").not('.'+value).hide('3000');
	$('.filter').filter('.'+value).show('3000');

	}
});

if ($(".filter-button").removeClass("active")) {
	$(this).removeClass("active");
}
$(this).addClass("active");
});</script>
<?php endif ?>

<?php if ( $WhereAmI == 'post' ) : ?>
<script>(function ($) {
    "use strict";
    $(document).ready(function(){
        // Creates Captions from Alt tags
        $(".container img").each(function() {
            // Let's put a caption if there is one
            if($(this).attr("alt"))
              $(this).wrap('')
              .after('<span class="caption text-muted">'+$(this).attr("alt")+'</span>');
        });
        
    });
}(jQuery));</script>
<?php endif ?>
</body>
</html>