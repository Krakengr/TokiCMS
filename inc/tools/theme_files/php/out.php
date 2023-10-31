<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?php echo __( 'hold-on' ) ?></h1>
                <hr />
            </div>
        </div>
		<div class="row">
			<div class="col-md-12">
				<div class="alert bg--secondary">
					<div class="alert__body">
						<span style="margin-top: 10px;"><img src="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/img/loading.gif" alt="loading" /></span> <?php echo sprintf( __( 'you-are-being-redirected' ), $Url ) ?></span>
					</div>
				</div>
			</div>
		</div>
    </div>
</section>

<?php if ( empty( $Meta ) ) : ?>
<script>
window.setTimeout(function(){
	window.location.href = "<?php echo $Url ?>";
}, 3000);
</script>
<?php endif ?>