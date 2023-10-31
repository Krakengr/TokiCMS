<script src="//code.jquery.com/jquery-3.1.1.min.js" async></script>
<script src="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/js/smooth-scroll.min.js" async></script>
<script src="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/js/scripts.js" async></script>

<?php if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v2' ) : ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif ?>

<?php if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v3' ) : ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo urlencode( Settings::Get()['recaptcha_site_key'] ) ?>" async defer></script>
<script>
grecaptcha.ready(() => {
	grecaptcha.execute( '<?php echo urlencode( Settings::Get()['recaptcha_site_key'] ) ?>', { action: 'contact' }).then(token => {
		document.querySelector('#recaptchaResponse').value = token;
	});
});
</script>
<?php endif ?>

<?php if ( !empty( Settings::Get()[ 'footer_code' ] ) && !Router::GetVariable( 'isAdmin' ) )
{
	$footer_code = Json( Settings::Get()[ 'footer_code' ] );
	
	$footerCode = '';

	if ( !empty( $footer_code ) && is_array( $footer_code ) )
	{
		$CurrentLang = CurrentLang();
		
		foreach( $footer_code as $_h => $__h )
		{
			if ( 
				( $__h['language'] == 0 ) || 
				( ( $__h['language'] > 0 ) && ( $__h['language'] == $CurrentLang['lang']['id'] ) )
			)
			{
				$footerCode .= html_entity_decode( $__h[ 'code' ] ) . PHP_EOL;
			}
					
			unset( $_h, $__h );
		}
	}

	unset( $footer_code );
}
?>