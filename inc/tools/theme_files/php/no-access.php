<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?php echo __( 'access-denied' ) ?></h1>
                <hr />
            </div>
        </div>
		<div class="row">
			<div class="col-md-12">
				<div class="alert bg--primary">
					<div class="alert__body">
						<?php echo __( 'sorry-you-are-not-allowed-to-access-this-page' ) ?>
					</div>
				</div>
				
				<a href="<?php echo Router::GetVariable( 'siteRealUrl' ) ?>"><?php echo $L['home'] ?></a>
			</div>
		</div>
    </div>
</section>