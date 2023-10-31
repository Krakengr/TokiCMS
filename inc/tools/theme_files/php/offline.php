<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?php echo $Offline['maintenance_subject'] ?></h1>
                <hr />
            </div>
        </div>
		<div class="row">
			<div class="col-md-12">
				<div class="alert bg--primary">
					<div class="alert__body">
						<?php echo $Offline['maintenance_text'] ?>
					</div>
				</div>
			<?php if ( Settings::IsTrue( 'enable_login_maintenance', 'site' ) ) : ?>
				<a href="<?php echo SITE_URL ?>login/"><?php echo $L['login'] ?></a>
			<?php endif ?>
			</div>
		</div>
    </div>
</section>