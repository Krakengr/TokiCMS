<!-- Page Header -->
    <!-- Set your background image for this header on the line below. -->
    <header class="intro-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                        <div class="page-heading">
                            <h1><?php echo Theme::SiteName() ?></h1>
                            <hr class="small">
                            <span class="subheading"><?php echo Theme::SiteSlogan() ?></span>
                        </div>
                        <style>.intro-header {background-image: url('<?php echo HTML_PATH_THEME ?>img/retro.jpg')}</style>
                        <div style="display:none" class="breadcrumb "><a href="<?php echo Theme::SiteUrl() ?>"><?php echo $L['home'] ?></a></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
<div class="container">
  <div class="row">
    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
		<?php if ( $Listings ) :
				foreach ( $Listings as $post): ?>		
			<div class="post-preview">
        <a href="<?php echo $post->Url() ?>">
          <h2 class="post-title"><?php echo $post->Title() ?></h2>
        </a>
        <p class="post-meta"><?php echo $L['posted'] ?> <?php echo $L['in'] ?> <?php echo $post->CategoryHtml() ?> <?php echo $L['by'] ?> <?php echo $post->Author()->html ?> <?php echo __( 'on' ) ?> <time datetime="<?php echo $post->Added()->c ?>"><?php echo $post->Added()->time ?></time></p>
        <?php echo $post->Description() ?>
      </div>
      <hr />
<?php endforeach; ?>

	<!-- Pager -->
	<?php Pagination( CleanBlogNav(), true ) ?>

	<?php else : ?>
	<p><?php echo $L['no-posts-found'] ?></p>
	<?php endif ?>
	</div>
  </div>
</div>  