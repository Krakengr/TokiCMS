<?php if ( !Settings::IsTrue( 'enable_registrations' ) ) : ?>
		<section>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?php echo $L['register'] ?></h1>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert bg--error">
                        <div class="alert__body">
                            <?php echo $L['registration-is-disabled'] ?>
                        </div>
                    </div>
                    <a href="<?php echo SITE_URL ?>">&larr; <?php echo $L['go-back-to-home'] ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
					 <a href="<?php echo SITE_URL ?>login/"><?php echo $L['login'] ?></a>
                </div>
            </div>
        </div>
    </section>
	<?php elseif ( Settings::IsTrue( 'enable_maintenance', 'site' ) ) : ?>
	<section>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?php echo $L['register'] ?></h1>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert bg--error">
                        <div class="alert__body">
                            <?php echo $L['registration-unavailable-site-maintenance-mode'] ?>
                        </div>
                    </div>
                    <a href="<?php echo SITE_URL ?>">&larr; <?php echo $L['go-back-to-home'] ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
					 <a href="<?php echo SITE_URL ?>login/"><?php echo $L['login'] ?></a>
                </div>
            </div>
        </div>
    </section>
	<?php else : ?>
        <section class="height-80 text-center">
        <div class="container pos-vertical-center">
            <div class="row">
                <div class="col-md-7 col-lg-5">
                    <h2><?php echo $L['register'] ?></h2>
                    <p><?php echo $L['please-enter-your-information-to-register-for-an-account'] ?></p>
					<?php if ( $notifyMessage ) : ?>
					<div class="alert bg--<?php echo $notifyType ?>">
                        <div class="alert__body">
                            <?php echo $notifyMessage ?>
                        </div>
                    </div>
					<?php endif ?>
                    <form method="post" role="form" id="form_login" action="<?php echo SITE_URL ?>register/" autocomplete="off" accept-charset="UTF-8" >
					<?php echo ( $disableButtons ? '<fieldset disabled>' : '' ) ?>
                        <div class="row">
                            <div class="col-md-12">
                                <input name="uu" type="text" placeholder="<?php echo $L['username'] ?>" autocomplete="off" list="autocompleteOff" autofocus value="<?php echo ( isset( $_POST['uu'] ) ? htmlspecialchars( $_POST['uu'] ) : '' ) ?>" required />
                            </div>
                            <div class="col-md-12">
                                <input name="up" type="password" autocomplete="chrome-off" placeholder="<?php echo $L['password'] ?>" list="autocompleteOff" value="" required />
                            </div>
							
							<div class="col-md-12">
                                <input name="up2" type="password" autocomplete="chrome-off" placeholder="<?php echo $L['confirm-password'] ?>" list="autocompleteOff" value="" required />
                            </div>
							
							<div class="col-md-12">
                                <input name="um" type="email" placeholder="<?php echo $L['email'] ?>" value="<?php echo ( isset( $_POST['um'] ) ? htmlspecialchars( $_POST['um'] ) : '' ) ?>" required />
                            </div>
							
							<?php if ( ( Settings::Get()['show_captcha_in_forms']  == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms']  == 'registration-form' ) ) : ?>
							
							<?php if ( Settings::IsTrue( 'enable_honeypot' ) ) : ?>
							<div class="col-md-12">
								<input class="ohhney" autocomplete="chrome-off" list="autocompleteOff" type="text" name="name" placeholder="Your name here">
							</div>
							
							<div class="col-md-12">
								<input class="ohhney" autocomplete="chrome-off" list="autocompleteOff" type="email" name="email" placeholder="Your e-mail here">
							</div>
						<?php endif ?>
						
						<?php if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v2' ) : ?>
							<div class="col-md-12">
								<div class="g-recaptcha" data-sitekey="<?php echo Settings::Get()['recaptcha_site_key'] ?>"></div>
							</div>
						<?php endif ?>
							
						<?php if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v3' ) : ?>
							<div class="col-md-12">
								<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
							</div>
						<?php endif ?>

						<?php endif ?>
						
						<?php TermsOfServiceAgreement( 'registration-form' ) ?>
						
						<?php RegistrationAgreement() ?>
						
						<?php PrivacyPolicyAgreement() ?>
						
						<input name="_token" type="hidden" value="<?php echo csrf::token() ?>">
						
							<div class="col-md-12">
                                <button class="btn btn--primary type--uppercase" type="submit"><?php echo $L['register'] ?></button>
                            </div>
                        </div>
					<?php echo ( $disableButtons ? '</fieldset>' : '' ) ?>
                    </form>
                    <span class="type--fine-print block">
					<a href="<?php echo SITE_URL ?>">&larr; <?php echo $L['go-back-to-home'] ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
					<a href="<?php echo SITE_URL ?>login/"><?php echo $L['login'] ?></a>
                    </span>
                    
                    <hr>
                    
                </div>
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
		<?php endif ?>