<?php
if ( !empty( ThemeValue( 'post-meta' ) ) )
	$keys = array_keys( ThemeValue( 'post-meta' ) );
else
	$keys = array();
?><article id="post-<?php echo $Post->PostId() ?>" class="post-<?php echo $Post->PostId() ?> post type-post <?php echo ( ( $Post->HasCoverImage() && ThemeValue( 'display-featured-image' ) ) ? 'has-post-thumbnail' : '' ) ?> status-publish format-standard hentry">
	<header class="entry-header">
		<h1 class="entry-title"><?php echo $Post->Title() ?></h1>
		
		<?php if ( $Post->HasCoverImage() && ThemeValue( 'display-featured-image' ) ) : ?>
		<div class="entry-thumbnail">
			<?php echo $Post->CoverImage( true ) ?>
		</div>
		<?php endif ?>
		
		<div class="entry-meta">
			<?php if ( in_array( 'post-date', $keys ) ) : ?><span class="posted-on"><time datetime="<?php echo $Post->Added()->c ?>"><?php echo $Post->Added()->time ?></time></span><?php endif ?><?php if ( in_array( 'author', $keys ) ) : ?><span class="byline"><span class="author vcard"><span class="sep"> ~ </span><?php echo $Post->AuthorHtml() ?></span></span><?php endif ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php echo $Post->Content() ?>
	</div><!-- .entry-content -->
	
	<footer class="entry-footer">
		<?php if ( in_array( 'categories', $keys ) ) : ?>
		<span class="cat-links">Posted in <?php echo $Post->CategoryHtml() ?><?php if ( $Post->SubCategoryHtml() ) : ?>, <?php echo $Post->SubCategoryHtml() ?><?php endif ?></span>
		<?php endif ?>

		<?php echo $Post->Tags( false, true ) ?>
		
		<?php if ( in_array( 'edit-link', $keys ) && CanEditPost( $Post ) ) : ?>
		<span class="sep"> ~ </span><span class="edit-link"><a class="post-edit-link" href="<?php echo EditPostLink() ?>"><?php echo __( 'edit' ) ?> <span class="screen-reader-text">"<?php echo $Post->Title() ?>"</span></a></span>
		<?php endif ?>
		<?php AddToFavoritesButton() ?>
		<?php SubscribeButton() ?>
	</footer><!-- .entry-footer -->

	<?php if ( ThemeValue( 'display-author-details-box' ) ) : ?>
	<div class="entry-author">
		<?php if ( !empty( $Post->AuthorProfileImage() ) && in_array( 'author-image', $keys ) ) : ?>
		<div class="author-avatar">
			<img alt='' src='<?php echo $Post->AuthorProfileImage() ?>' class='avatar avatar-60 grav-hashed grav-hijack' height='60' width='60' loading='lazy'/>
		</div><!-- .author-avatar -->
		<?php endif ?>
		<div class="author-heading">
			<h2 class="author-title">Published by <span class="author-name"><?php echo $Post->Author()->name ?></span></h2>
		</div><!-- .author-heading -->

		<p class="author-bio"><?php echo $Post->AuthorBio() ?> <?php if ( UserArchives() ) : ?><a class="author-link" href="<?php echo $Post->Author()->url ?>" rel="author"> View all posts by <?php echo $Post->Author()->name ?></a><?php endif ?>
		</p><!-- .author-bio -->
	</div><!-- .entry-auhtor -->
	<?php endif ?>
</article><!-- #post-## -->

<?php if ( ThemeValue( 'display-post-navigation' ) ) : ?>
<nav class="navigation post-navigation" aria-label="Posts">
	<h2 class="screen-reader-text">Post navigation</h2>
	<div class="nav-links"><?php 
	if ( $Post->PreviousPost( true ) ) :
		$nxt = $Post->PreviousPost();
	?><div class="nav-previous"><a href="<?php echo $nxt->Url() ?>" rel="prev"><span class="meta-nav">&lsaquo; <?php echo __( 'previous' ) ?></span><?php echo $nxt->Title() ?></a></div><?php endif ?>
	<?php if ( $Post->NextPost( true ) ) : 
			$nxt = $Post->NextPost();
	?><div class="nav-next"><a href="<?php echo $nxt->Url() ?>" rel="next"><span class="meta-nav"><?php echo __( 'next' ) ?> &rsaquo;</span><?php echo $nxt->Title() ?></a></div><?php endif ?></div>
</nav>
<?php endif ?>

<?php if ( in_array( 'comments', $keys ) && $Post->HasCommentsEnabled() ) : ?>
<?php Comments( null, ThemeValue( 'remove-zero-comments-on-post' ) ) ?>
<?php endif ?>