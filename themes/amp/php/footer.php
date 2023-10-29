<footer class="ampstart-footer flex flex-column items-center px3">
      <nav class="ampstart-footer-nav">
		<?php ShowLegalPages( 'list-reset flex flex-wrap mb3', false, false, 'px1', 'text-decoration-none ampstart-label' ) ?>
      </nav>
      <small><?php echo __( 'copyright' ) ?> &copy; <a href="<?php echo Theme::SiteUrl() ?>"><?php echo Theme::SiteName() ?></a> ~ Powered By <a href="http://badtooth.studio/tokicms/" target="_blank">TokiCMS</a><p><a href="<?php echo $Post->Url() ?>"><?php echo __( 'view-desktop-version' ) ?></a></p></small>
    </footer>