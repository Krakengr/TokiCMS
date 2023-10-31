<section class="height-80 text-center">
        <div class="container pos-vertical-center">
            <div class="row">
                <div class="col-md-7 col-lg-5">
                    <h2><?php echo $L['login'] ?></h2>
					<?php if ( $notifyMessage ) : ?>
					<div class="alert bg--error">
                        <div class="alert__body">
                            <?php echo $notifyMessage ?>
                        </div>
                    </div>
					<?php endif ?>
					<?php if ( !$hideForm ) : ?>
                    <p><?php echo $L['please-enter-your-username-and-password-below-to-login'] ?></p>
                    <form method="post" role="form" id="form_login" action="" autocomplete="off" accept-charset="UTF-8">
                        <div class="row">
                            <div class="col-md-12">
                                <input name="uu" type="text" placeholder="<?php echo $L['username'] ?>" autocomplete="off" autofocus value="<?php echo ( isset( $_POST['uu'] ) ? htmlspecialchars( $_POST['uu'] ) : '' ) ?>" <?php echo ( $disableButtons ? 'disabled' : '' ) ?> />
                            </div>
                            <div class="col-md-12">
                                <input name="up" type="password" placeholder="<?php echo $L['password'] ?>" autocomplete="off" value="" <?php echo ( $disableButtons ? 'disabled' : '' ) ?> />
                            </div>
							
							<?php if ( ( Settings::Get()['show_captcha_in_forms']  == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms']  == 'login-form' ) ) : ?>
							
								<?php if ( Settings::IsTrue( 'enable_honeypot' ) ) : ?>
								<div class="col-md-12">
									<input class="ohhney" autocomplete="off" type="text" id="name" name="name" placeholder="Your name here">
								</div>
								
								<div class="col-md-12">
									<input class="ohhney" autocomplete="off" type="email" id="email" name="email" placeholder="Your e-mail here">
								</div>
							<?php endif ?>
							
							<?php if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v2' ) : ?>
								<div class="col-md-12">
									<div class="g-recaptcha" data-sitekey="<?php echo Settings::Get()['recaptcha_site_key'] ?>"></div>
								</div>
							<?php endif ?>
							
							<?php if ( Settings::Get()['enable_recaptcha'] == 'google-recaptcha-v3' ) : ?>
								<div class="col-md-12">
									<input type="hidden" name="recaptcha_response" value="" id="recaptchaResponse">
								</div>
							<?php endif ?>
							
						<?php endif ?>
						
						<?php TermsOfServiceAgreement( 'login-form' ) ?>
						
						<input name="remember_me" type="hidden" value="1">
						<input name="_token" type="hidden" value="<?php echo csrf::token() ?>">
						
							<div class="col-md-12">
                                <button class="btn btn--primary type--uppercase" type="submit" <?php echo ( $disableButtons ? 'disabled' : '' ) ?>><?php echo $L['login'] ?></button>
                            </div>
                        </div>
                    </form>
					<?php endif ?>
                    <span class="type--fine-print block">
					<a href="<?php echo SITE_URL ?>">&larr; <?php echo $L['go-back-to-home'] ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
					<?php if ( Settings::IsTrue( 'enable_registration', 'site' ) ) : ?>
                        <a href="<?php echo SITE_URL ?>register/"><?php echo $L['register'] ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
					<?php endif ?>
					<a href="<?php echo SITE_URL ?>forgot-password/"><?php echo $L['forgot-password'] ?></a>
                    </span>
                    
                    <hr />
                    
                </div>
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>