<?php defined('TOKICMS') || die('Hacking attempt...') ?><!DOCTYPE html> 
<html lang="<?php echo Theme::Locale() ?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo Theme::HeaderTitle() ?></title>
		<meta name="description" content="<?php echo Theme::Description() ?>">
		<link rel="icon" href="<?php echo Theme::SiteImage() ?>" sizes="32x32" />
		<?php echo Theme::HeaderCode() ?>
	</head>
	<body class="<?php if ( $Post->IsPage() ) : ?>page-template-default page page-id-<?php echo $Post->PostID() ?><?php else : ?>post-template-default single single-post postid-<?php echo $Post->PostID() ?> single-format-standard<?php endif ?>">
		<div class="wrap">
			<div class="header">
				<h1><a href="<?php echo Theme::SiteUrl() ?>"><?php echo Theme::SiteName() ?></a></h1>
				<div class="description"><?php echo Theme::SiteSlogan() ?></div>
			</div>	
	<div id="content">
	<?php if ( $Post->IsPage() ) : ?>
		<div id="post-<?php echo $Post->PostID() ?>" class="post-<?php echo $Post->PostID() ?> page type-page status-publish format-standard hentry">
	<?php else : ?>
		<div id="post-<?php echo $Post->PostID() ?>" class="post-<?php echo $Post->PostID() ?> post type-post status-publish format-standard hentry category-<?php echo $Post->CategoryKey() ?>">
	<?php endif ?>
			<h1><?php echo $Post->Title() ?></h1>
			
			<div class="meta">
				<em><?php echo __( 'posted' ) . ' ' . __( 'on' ) ?></em> <time datetime="<?php echo $Post->Date( 'c' ) ?>"><?php echo $Post->Date() ?></time></time>
				<em><?php echo __( 'by' ) ?></em> <?php echo $Post->UserName() ?> &bull; 
				<span><a href="#comments" class="comments-link" ><?php echo __( 'comments' ) ?></a></span> &bull; 
	</div>			
						
			<div class="entry">
				<?php echo $Post->Content() ?>
			</div>

			<div class="postmetadata">
			<?php if ( !empty( $Post->Tags() ) ) : ?>
				<p><?php echo __( 'tags' ) ?>: <?php foreach( $Post->Tags() as $tag ) : ?><a href="<?php echo $tag['url'] ?>" rel="tag"><?php echo $tag['name'] ?></a>,<?php endforeach ?></p>	
			<?php endif ?>
				<p><?php echo __( 'posted' ) . ' ' . __( 'in' ) ?> <?php echo $Post->CategoryHtml() ?></p>
				
			</div>
			
		</div>
		
<div class="nav nav-post nav-single">
	
	<?php if ( $Post->HasPreviousPost() ) :
			$nxt = $Post->PreviousPost();
		?>
		<div class="nav-previous"><a href="<?php echo $nxt->Url() ?>" rel="prev"><span class="meta-nav" aria-hidden="true"><?php echo $L['previous-post'] ?></span> <br/><span class="post-title"><?php echo $nxt->Title() ?></span></a></div>
	<?php endif ?>
	
	<?php if ( $Post->HasNextPost() ) : 
				$nxt = $Post->NextPost();
		?>
		<div class="nav-next"><a href="<?php echo $nxt->Url() ?>" rel="next"><span class="meta-nav" aria-hidden="true"><?php echo $L['next-post'] ?></span> <br/><span class="post-title"><?php echo $nxt->Title() ?></span></a></div>
	<?php endif ?>
</div>

<?php if( $Post->ShowComments() ) : ?>
	<h2 id="comments"><?php echo ( ( $Post->NumComments() > 0 ) ? $Post->NumComments() . ' ' . __( 'responses' ) : __( 'respond' ) ) ?></h2>
	
	<?php if ( !empty( $Post->Comments() ) ) : ?>
	<ol class="commentlist">
	<?php 
	foreach( $Post->Comments() as $comm ) : 
		$i = 1;
	?><li class="comment <?php echo ( ( $i % 2 ) ? 'odd' : 'even' ) ?> thread-<?php echo ( ( $i % 2 ) ? 'odd' : 'even' ) ?> depth-1" id="comment-<?php echo $comm['id'] ?>">
			<div id="div-comment-<?php echo $comm['id'] ?>" class="comment-body">
				<div class="comment-author vcard">
					<cite class="fn"><?php echo $comm['name'] ?></cite> <span class="says"><?php echo __( 'says' ) ?>:</span>
				</div>
		
				<div class="comment-meta commentmetadata">
					<a href="#comment-<?php echo $comm['id'] ?>"><?php echo $comm['time'] ?></a>
				</div>

				<?php echo $comm['comment'] ?>
			</div>
		</li><!-- #comment-## -->
		
	<?php $i++; endforeach; ?>		
	</ol>
	<?php endif ?>
	
	<div id="respond"></div>
<?php endif ?>

	</div>
	
	<div class="footer">
		<p>&copy; <?php echo date( 'Y', time() ) ?> <?php echo Theme::SiteName() ?></p>
	</div>

	</div>
	</body>
</html>
<!-- Powered by Toki CMS (https://badtooth.studio/tokicms/) -->