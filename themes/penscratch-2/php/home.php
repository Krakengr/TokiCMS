<?php if ( $Listings ) :
	foreach ( $Listings as $post) : 
	
	$srcset = $img = null;
	
	if ( !empty( $post->HasCoverImage() ) )
	{
		$cover 	= $post->CoverImage( false, 'default', false );
		$alt 	= htmlspecialchars( $post->Title() );
		$img 	= $cover->imageUrl;
		$height = $cover->imageHeight;
		$width 	= $cover->imageWidth;
		$srcset = $post->CoverSrc()->srcFull;
	}
	?>
	<article id="post-<?php echo $post->PostId() ?>" class="post-<?php echo $post->PostId() ?> post type-post status-publish format-standard hentry category-<?php echo $post->Category()->key ?>">
		<header class="entry-header">
			<h1 class="entry-title"><a href="<?php echo $post->Url() ?>" rel="bookmark"><?php echo $post->Title() ?></a></h1>
			<?php if ( $post->HasCoverImage() ) : ?>
			<div class="entry-thumbnail">
				<a href="<?php echo $post->Url() ?>">
					<?php echo $post->CoverImage( true ); ?>
				</a>
			</div>
		<?php endif ?>
		</header><!-- .entry-header -->
		<div class="entry-meta">
			<span class="posted-on"><a href="<?php echo $post->Url() ?>" rel="bookmark"><time datetime="<?php echo $post->Added()->c ?>"><?php echo $post->Added()->time ?></time></a></span><span class="byline"><?php echo $post->CategoryHtml() ?><?php if ( $post->SubCategoryHtml() ) : ?>, <?php echo $post->SubCategoryHtml() ?><?php endif ?></span><span class="byline"><span class="author vcard"><span class="sep"> ~ </span><?php echo $post->AuthorHtml() ?></span></span>
		</div><!-- .entry-meta -->
		<div class="entry-content">
			<p><?php echo $post->Description() ?> <a href="<?php echo $post->Url() ?>" class="more-link"><?php echo __( 'read-more' ) ?> <span class="screen-reader-text"><?php echo $post->Title() ?></span> <span class="meta-nav">&rarr;</span></a></p>
		</div><!-- .entry-content -->
	</article><!-- #post-<?php echo $post->PostId() ?> -->
	<?php endforeach ?>
	
	<?php if ( Paginator::NumberOfPages() > 1 ) : ?>
	<nav class="navigation posts-navigation" aria-label="Navigation">
		<h2 class="screen-reader-text">Post Navigation</h2>
		<div class="nav-links"><?php if ( Paginator::HasOlder() ) : ?><div class="nav-previous"><a href="<?php echo Paginator::OlderPageUrl() ?>" ><?php echo __( 'older' ) ?></a></div><?php endif ?><?php if ( Paginator::HasNewer() ) : ?><div class="nav-next"><a href="<?php echo Paginator::NewerPageUrl() ?>" ><?php echo __( 'newer' ) ?></a></div><?php endif ?></div>
	</nav>
	<?php endif ?>
	
<?php else : ?>
	<p><?php echo __( 'no-posts-found' ) ?></p>
<?php endif ?>