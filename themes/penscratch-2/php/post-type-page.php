<?php
if ( !empty( ThemeValue( 'post-meta' ) ) )
	$keys = array_keys( ThemeValue( 'post-meta' ) );
else
	$keys = array();
?><article id="post-<?php echo $Post->PostId() ?>" class="post-<?php echo $Post->PostId() ?> page type-page status-publish hentry">
	<?php if ( !StaticHomePage( false, $Post->PostId() ) || ( $Post->ParentId() && !StaticHomePage( false, $Post->ParentId() ) ) ) : ?>
	<header class="entry-header">
		<h1 class="entry-title"><?php echo $Post->Title() ?></h1>
	</header><!-- .entry-header -->
	<?php endif ?>
	<div class="entry-content">
		<?php echo $Post->Content() ?>
	</div><!-- .entry-content -->
	<footer class="entry-footer">
	<?php if ( in_array( 'edit-link', $keys ) && CanEditPost() ) : ?>
		<span class="edit-link"><a class="post-edit-link" href="<?php echo EditPostLink() ?>"><?php echo __( 'edit' ) ?> <span class="screen-reader-text">"<?php echo $Post->Title() ?>"</span></a></span>
	<?php endif ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->