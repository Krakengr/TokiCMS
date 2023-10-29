<!-- Page Header -->
<header class="intro-header">
    <div class="container" >
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="post-heading">
                    <h1><?php echo $Post->Title() ?></h1>
					<span class="meta">Posted in <?php echo $Post->Category()->html ?> <?php echo __( 'by' ) ?> <?php echo $Post->AuthorHtml() ?> <?php echo __( 'on' ) ?> <time datetime="<?php echo $Post->Added()->c ?>"><?php echo $Post->Added()->time ?></time></span>
                </div>

				<?php if ( $Post->HasCoverImage() || !ThemeValue( 'disable-fallback-image' ) ) : ?>
				<style>.intro-header {background-image: url('<?php echo ( $Post->HasCoverImage() ? $Post->CoverImage( false ) : HTML_PATH_THEME . 'img/watch_wooden.jpg' ) ?>')}</style>

				<?php else : ?>
				<style>.intro-header {background-color: #1c0b02;}</style>
				<?php endif ?>
						
				<?php if ( ThemeValue( 'show-breadcrumbs' ) ) : ?>
				<div class="breadcrumb left">
					<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="<?php echo Theme::SiteUrl() ?>"><?php echo __( 'home' ) ?></a></span> &#187;
							
					<?php if ( !empty( $Post->Blog() ) ) : ?>
					<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="<?php echo $Post->Blog()->url ?>"><?php echo $Post->Blog()->name ?></a></span> &#187;<?php endif ?>
							
					<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="<?php echo $Post->Category()->url ?>"><?php echo $Post->Category()->name ?></a></span> &#187;
					<?php echo $Post->Title() ?>
				</div>
				<?php endif ?>
            </div>
        </div>
    </div>
</header>

<!-- Post Content -->
<article>
	<div class="container">
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
				<?php echo $Post->Content() ?>
				<hr />
				<div class="toolbox">
					<span class="category"><i class="fa fa-folder"></i> <?php echo $Post->CategoryHtml() ?></span>					
					<?php Tags( true, null, '<span class="tags"><i class="fa fa-tags"></i> %s</span>' ) ?>
					<?php if ( ThemeValue( 'enable-share-posts-button' ) ) : ?>
						<span class="share pull-right">
							<a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo $Post->Url( true ) ?>&t=<?php echo $Post->Title( true ) ?>" onclick="window.open(this.href, 'facebook-share', 'width=550,height=255');return false;"><i class="fa fa-facebook"></i></a> 
							<a target="_blank" href="https://twitter.com/share?url=<?php echo $Post->Url( true ) ?>&text=<?php echo $Post->Title( true ) ?>" onclick="window.open(this.href, 'twitter-share', 'width=550,height=500');return false;"><i class="fa fa-twitter"></i></a> 
						</span>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
</article>