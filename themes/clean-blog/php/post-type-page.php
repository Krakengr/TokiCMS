<!-- Page Header -->
<header class="intro-header">
    <div class="container" >
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="post-heading">
                    <h1><?php echo $Post->Title() ?></h1>
					<span class="meta"><?php echo ( $Post->BlogHtml() ? 'Posted in ' . $Post->BlogHtml() : '' ) ?></span>
                </div>

				<?php if ( $Post->HasCoverImage() || !ThemeValue( 'disable-fallback-image' ) ) : ?>
					<style>.intro-header {background-image: url('<?php echo ( $Post->HasCoverImage() ? $Post->CoverImage() : HTML_PATH_THEME . 'img/watch_wooden.jpg' ) ?>')}</style>
				
				<?php else : ?>
					<style>.intro-header {background-color: #1c0b02;}</style>
				<?php endif ?>
						
				<?php if ( ThemeValue( 'show-breadcrumbs' ) ) : ?>
				<div class="breadcrumb left">
					<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="<?php echo $Theme->SiteUrl() ?>"><?php echo __( 'home' ) ?></a></span> &#187;
							
					<?php if ( !empty( $Post->BlogUrl() ) ) : ?>
					<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="<?php echo $Post->BlogUrl() ?>"><?php echo $Post->BlogName() ?></a></span> &#187;<?php endif ?>

					<?php echo $Post->Title() ?>
				</div>
				<?php endif ?>
            </div>
        </div>
    </div>
</header>

<article>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
				<?php echo CleanPortfolio( $Post->Content() ) ?>
            </div>
        </div>
    </div>
</article>