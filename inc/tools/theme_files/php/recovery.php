<section class="height-80 text-center">
        <div class="container pos-vertical-center">
            <div class="row">
                <div class="col-md-7 col-lg-5">
                    <h2><?php echo $L['reset-password'] ?></h2>
					<?php if ( $notifyMessage ) : ?>
					<div class="alert bg--<?php echo $notifyType ?>">
                        <div class="alert__body">
                            <?php echo $notifyMessage ?>
                        </div>
                    </div>
					<?php endif ?>
                    <p><?php echo $L['enter-your-new-password-below'] ?></p>
                    <form method="post" role="form" id="form_login" action="" autocomplete="off" accept-charset="UTF-8">
                        <div class="row">							
							<div class="col-md-12">
                                <input name="up" type="password" autocomplete="chrome-off" placeholder="<?php echo $L['password'] ?>" list="autocompleteOff" value="" <?php echo ( $disableButtons ? 'disabled' : '' ) ?> required />
                            </div>
							
							<div class="col-md-12">
                                <input name="up2" type="password" autocomplete="chrome-off" placeholder="<?php echo $L['confirm-password'] ?>" list="autocompleteOff" value="" <?php echo ( $disableButtons ? 'disabled' : '' ) ?> required />
                            </div>

							<?php if ( ( Settings::Get()['show_captcha_in_forms']  == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms']  == 'lost-password-form' ) )	: ?>
							
							<?php if ( Settings::IsTrue( 'enable_honeypot' ) ) : ?>
							<div class="col-md-12">
								<input class="ohhney" autocomplete="chrome-off" list="autocompleteOff" type="text" id="name" name="name" placeholder="Your name here">
							</div>
							
							<div class="col-md-12">
								<input class="ohhney" autocomplete="chrome-off" list="autocompleteOff" type="email" id="email" name="email" placeholder="Your e-mail here">
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
						
						<?php TermsOfServiceAgreement( 'lost-password-form' ) ?>

						<input name="_token" type="hidden" value="<?php echo csrf::token() ?>">
						
							<div class="col-md-12">
                                <button class="btn btn--primary type--uppercase" type="submit" <?php echo ( $disableButtons ? 'disabled' : '' ) ?>><?php echo $L['reset-password'] ?></button>
                            </div>
                        </div>
                    </form>
                    <span class="type--fine-print block">
					<a href="<?php echo SITE_URL ?>">&larr; <?php echo $L['go-back-to-home'] ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                    <a href="<?php echo SITE_URL ?>forgot-password/"><?php echo $L['forgot-password'] ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
					<a href="<?php echo SITE_URL ?>login/"><?php echo $L['login'] ?></a>
                    </span>
                    <hr />
                </div>
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>