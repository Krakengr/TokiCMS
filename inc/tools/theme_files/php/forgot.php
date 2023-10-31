<section class="height-80 text-center">
        <div class="container pos-vertical-center">
            <div class="row">
                <div class="col-md-7 col-lg-5">
                    <h2><?php echo $L['forgot-password'] ?></h2>
					<?php if ( $notifyMessage ) : ?>
					<div class="alert bg--<?php echo $notifyType ?>">
                        <div class="alert__body">
                            <?php echo $notifyMessage ?>
                        </div>
                    </div>
					<?php endif ?>
                    <p><?php echo $L['if-forgotten-your-login-details-please-enter-your-username-or-email-address-below'] ?></p>
                    <form method="post" role="form" id="form_login" action="" autocomplete="off" accept-charset="UTF-8">
                        <div class="row">
                            <div class="col-md-12">
                                <input name="uu" type="text" placeholder="<?php echo $L['username-email'] ?>" autocomplete="off" autofocus value="<?php echo ( isset( $_POST['uu'] ) ? htmlspecialchars( $_POST['uu'] ) : '' ) ?>" <?php echo ( $disableButtons ? 'disabled' : '' ) ?> />
                            </div>

							<?php if ( Settings::IsTrue( 'enable_honeypot' ) && ( ( Settings::Get()['show_captcha_in_forms']  == 'everywhere' ) || ( Settings::Get()['show_captcha_in_forms']  == 'login' ) ) )	: ?>
							<div class="col-md-12">
								<input class="ohhney" autocomplete="chrome-off" list="autocompleteOff" type="text" id="name" name="name" placeholder="Your name here">
							</div>
							
							<div class="col-md-12">
								<input class="ohhney" autocomplete="chrome-off" list="autocompleteOff" type="email" id="email" name="email" placeholder="Your e-mail here">
							</div>
						<?php endif ?>

						<input name="_token" type="hidden" value="<?php echo csrf::token() ?>">
						
							<div class="col-md-12">
                                <button class="btn btn--primary type--uppercase" type="submit" <?php echo ( $disableButtons ? 'disabled' : '' ) ?>><?php echo $L['reset-password'] ?></button>
                            </div>
                        </div>
                    </form>
                    <span class="type--fine-print block">
					<a href="<?php echo SITE_URL ?>">&larr; <?php echo $L['go-back-to-home'] ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
					<?php if ( Settings::IsTrue( 'enable_registration', 'site' ) ) : ?>
                        <a href="<?php echo SITE_URL ?>register/"><?php echo $L['register'] ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
					<?php endif ?>
					<a href="<?php echo SITE_URL ?>login/"><?php echo $L['login'] ?></a>
                    </span>
                    <hr />
                </div>
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>