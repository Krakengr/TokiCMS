<?php 
	$lang = CurrentLang();
	
	$title 	= __( 'page-not-found' );
	$body 	= __( 'page-not-found-content' );
	
	if ( !empty( $lang['data']['not_found_data'] ) )
	{
		$notFound = Json( $lang['data']['not_found_data'] );
		
		if ( !empty( $notFound['not_found_title'] ) )
		{
			$title 	= StripContent( $notFound['not_found_title'] );
		}

		if ( !empty( $notFound['not_found_message'] ) )
		{
			$body 	= StripContent( $notFound['not_found_message'] );
		}		
	}
?><section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?php echo $title ?></h1>
                <hr />
            </div>
        </div>
        
		<div class="row">
            <div class="col-md-12">
                <div class="alert bg--primary">
                    <div class="alert__body">
                        <?php echo $body ?>
                    </div>
                </div>
                
				<a href="<?php echo Router::GetVariable( 'siteRealUrl' ) ?>"><?php echo $L['home'] ?></a>
            </div>
        </div>
    </div>
</section>