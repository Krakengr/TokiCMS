<?php
if ( $Post->IsPage() )
	include ( 'post-type-page.php' );

else
	include ( 'post-type-post.php' );