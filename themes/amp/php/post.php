<article class="recipe-article">
        <header>
		<?php if ( $Post->Blog( true ) ) : ?>
          <span class="ampstart-subtitle block px3 pt2 mb2"><?php echo $Post->Blog()->name ?></span>
		<?php endif ?>
          <h1 class="mb1 px3"><?php echo $Post->Title() ?></h1>

          <!-- Start byline -->
          <address class="ampstart-byline clearfix mb4 px3 h5">
            <time
              class="ampstart-byline-pubdate block bold my1"
              datetime="<?php echo $Post->Added()->c ?>"
              ><?php echo $Post->Added()->time ?></time
            >
          </address>
          <!-- End byline -->
		  
          <?php 
		  if ( !empty( $Post->HasVideo() ) && empty( $Post->Video()->fromContent ) )
		  {
			echo $Post->Video()->amp;
		  }
		elseif ( !empty( $Post->HasCoverImage() ) )
		{
			echo $Post->CoverAmp();
		} ?>
        </header>

        <section class="px3 mb4">
          <section class="mb4">
            <?php echo $Post->ContentAmp() ?>
          </section>
		  
		  <?php if( $Post->HasCommentsEnabled() ) : ?>
          <section class="recipe-comments">
            <h2 class="mb3"><?php echo $Post->NumComments() ?> <?php echo ( ( $Post->NumComments() > 1 ) ? __( 'responses' ) : __( 'response' ) )?></h2>
			<?php if ( $Post->Comments( true ) ) : ?>
            <ul class="list-reset">
			<?php foreach ( $Post->Comments() as $comm ) : ?>
              <li class="mb4">
                <h3 class="ampstart-subtitle"><?php echo $comm['name'] ?></h3>
                <span class="h5 block mb3"><?php echo $comm['niceTime'] ?></span>
                <?php echo $comm['comment'] ?>
              </li>
              <?php endforeach ?>
            </ul>
			<?php endif ?>
			
			<a href="<?php echo $Post->Url() ?>#comments" class="button add-comment">Add Comment</a>
		
          </section>
		  <?php endif ?>
		
		<?php if ( $Post->RelatedPosts( null, true ) ) : ?>
          <section class="ampstart-related-article-section">
            <h2 class="mb4"><?php echo __( 'you-might-also-like' ) ?></h2>
			<?php foreach ( $Post->RelatedPosts( 3 ) as $rel ) : ?>
			<?php if ( !empty( $rel->HasCoverImage() ) ) :
				echo $rel->CoverAmp();
			else : ?>
            <amp-img
              src="<?php echo THEME_AMP_HTML . 'assets/images/default-fallback-image.png' ?>"
              width="800"
              height="600"
              layout="responsive"
              alt=""
              class="mb1"
            ></amp-img>
			<?php endif ?>
            <h3 class="mb4"><a href="<?php echo $rel->Url() ?>" style="text-decoration: none;" title="<?php echo htmlspecialchars( $rel->Title() ) ?>"><?php echo $rel->Title() ?></a></h3>
			<?php endforeach ?>
          </section>
		  <?php endif ?>
		  
		  <?php AmpSocialMenu() ?>

          <section>
            <h2 class="mb3"><?php echo __( 'categories' ) ?></h2>
            <ul class="list-reset p0 m0 mb4">
              <li class="mb2">
                <a href="<?php echo $Post->Category()->url ?>" class="text-decoration-none h3"><?php echo $Post->Category()->name ?></a>
              </li>
			  <?php if ( $Post->SubCategory( true ) ) : ?>
              <li class="mb2">
                <a href="<?php echo $Post->SubCategory()->url ?>" class="text-decoration-none h3"><?php echo $Post->SubCategory()->name ?></a>
              </li>
			  <?php endif ?>
            </ul>
          </section>
        </section>
      </article>