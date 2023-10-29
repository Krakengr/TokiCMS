<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="footer-widgets clear">
		<?php Widgets( 'footer-1', GetThemeArgs( 'footer-1' ) ) ?><!-- .widget-area -->

		<?php Widgets( 'footer-2', GetThemeArgs( 'footer-2' ) ) ?><!-- .widget-area -->

		<?php Widgets( 'footer-3', GetThemeArgs( 'footer-3' ) ) ?><!-- .widget-area -->
	</div><!-- .sidebar-widgets -->
	<div class="site-info-wrapper clear">
		<?php if ( ThemeValue( 'show-social-menu-on-footer' ) ) : ?>
		<nav class="jetpack-social-navigation jetpack-social-navigation-svg" aria-label="Social Links Menu">
			<div class="menu-social-links-container">
				<?php PenSocialFooter() ?>
			</div>
		</nav><!-- .social-navigation -->
		<?php endif ?>
		<div class="site-info">
			<?php echo Theme::FooterText() ?>
		</div><!-- .site-info -->
	</div><!-- .site-info-wrapper -->
</footer><!-- #colophon -->