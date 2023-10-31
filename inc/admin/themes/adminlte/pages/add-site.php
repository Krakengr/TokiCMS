<?php require ( ARRAYS_ROOT . 'generic-arrays.php') ?>
<div class="container-fluid">
	<div class="row">
		<form class="tab-content" id="form" method="post" action="" autocomplete="off">
			<div class="form-row">
			<div class="form-group col-md-8">
				<h4><?php echo $L['add-new-site'] ?></h4>
					
				<div class="form-group">
					<label for="inputTitle"><?php echo $L['site-title'] ?></label>
					<input type="text" class="form-control" name="title" id="inputTitle" value="" required>
					<small id="titleHelp" class="form-text text-muted"><?php echo $L['add-title-tip'] ?></small>
				</div>

				<div class="form-group">
					<label for="inputUrl"><?php echo $L['site-url'] ?></label>
					<input type="text" class="form-control" id="inputUrl" name="url" value="" required>
					<small id="urlHelp" class="form-text text-muted"><?php echo $L['site-url-tip'] ?></small>
				</div>
				
				<div class="form-group">
					<label for="inputLanguage"><?php echo $L['choose-language'] ?>:</label>
					<select name="site_lang" class="form-control selectpicker" id="slcCountry" >
						<option value="default"><?php echo $L['default'] ?></option>
						<?php 
							$langs = Langs( $Admin->GetSite(), false, false );
								if ( !empty( $langs ) ) :
									foreach ( $langs as $lang ):
						?>
						<option value="<?php echo $lang['id'] ?>" data-flag="<?php echo SITE_URL . 'languages' . PS . 'flags' . PS . $key . '.png' ?>"><?php echo $lang['title'] ?></option>
						<?php endforeach; endif; ?>
					</select>
					<small id="languageHelp" class="form-text text-muted"><?php echo $L['add-language-new-site-tip'] ?></strong></small>
				</div>
				
				<div class="form-check">
					<input class="form-check-input" type="checkbox" value="1" name="copy_settings">
					<label class="form-check-label" for="settingsSite">
						<?php echo $L['copy-the-settings'] ?>
					</label>
					<small id="settingsHelp" class="form-text text-muted"><?php echo $L['copy-the-settings-tip'] ?></small>
				</div>

				<div class="form-group">
					<label for="inputHosted"><?php echo $L['site-is'] ?>:</label>
					<select name="select-host" class="form-control selectpicker" id="slcHosts" >
						<?php foreach ( $siteHosts as $key => $row ) : ?>
						<option value="<?php echo $key ?>"><?php echo $row['title'] ?></option>
						<?php endforeach ?>
					</select>
					<small id="hostHelp" class="form-text text-muted"><?php echo $L['site-is-hosted-tip'] ?></strong></small>
				</div>
				
				<div id="loader2" class="row mb-3 d-none">
					<label for="input-group-loader" class="col-sm-2 col-form-label">&nbsp;</label>
					<div class="col-sm-2">
						<div class="form-group">
							<img class="form-check" src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/ajax-loader.gif">
						</div>
					</div>
				</div>
				
				<div class="d-none" id="selfHostedData">

					<div class="form-group">
						<label for="inputPing"><?php echo $L['ping-slash'] ?></label>
						<input type="text" class="form-control" id="inputPing" name="ping" value="ping" required >
						<small id="pingHelp" class="form-text text-muted"><?php echo $L['ping-slash-tip'] ?></small>
					</div>
					
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="1" name="enable_polylang">
						<label class="form-check-label" for="polylangSite">
							<?php echo $L['enable-polylang-mode'] ?>
						</label>
						<small id="polylangHelp" class="form-text text-muted"><?php echo $L['polylang-tip'] ?></small>
					</div>
					
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="1" name="enable_multiblog">
						<label class="form-check-label" for="multiblogSite">
							<?php echo $L['enable-multiblog-mode'] ?>
						</label>
						<small id="multiblogHelp" class="form-text text-muted"><?php echo $L['multiblog-tip'] ?></small>
					</div>
					
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="1" name="enable_maintenance">
						<label class="form-check-label" for="maintenanceSite">
							<?php echo $L['enable-maintenance-mode'] ?>
						</label>
						<small id="maintenanceHelp" class="form-text text-muted"><?php echo $L['enable-maintenance-tip'] ?></small>
					</div>
					
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="1" name="enable_registration">
						<label class="form-check-label" for="registrationSite">
							<?php echo $L['enable-registration'] ?>
						</label>
						<small id="registrationHelp" class="form-text text-muted"><?php echo $L['enable-registration-tip'] ?></small>
					</div>
				</div>

				<div class="d-none" id="bloggerData">
					<hr />
					<h3 class="h4"><?php echo $L['blogger-api'] ?></h3>
					<div class="form-group">
						<label for="inputBloggerApi"><?php echo $L['blogger-api'] ?></label>
						<input type="text" class="form-control" id="inputBloggerApi" name="bloggerApi" value="">
						<small id="bloggerApiHelp" class="form-text text-muted"><?php echo $L['blogger-api-tip'] ?></small>
					</div>
					
					<div class="form-group">
						<label for="inputBloggerBlogId"><?php echo $L['blog-id'] ?></label>
						<input type="text" class="form-control" id="inputBloggerBlogId" name="bloggerBlogId" value="">
						<small id="bloggerBlogIdHelp" class="form-text text-muted"><?php echo $L['blogger-blog-id-tip'] ?></small>
					</div>
				</div>
				
				<div class="d-none" id="wpData">
					<hr />
					<h3 class="h4"><?php echo $L['wordpress-api'] ?></h3>
					<div class="form-group">
						<label for="inputWpClientApi"><?php echo $L['client-id'] ?></label>
						<input type="text" class="form-control" id="inputWpClientApi" name="wordpressClientApi" value="">
						<small id="wpClientApiHelp" class="form-text text-muted"><?php echo $L['wordpress-api-tip'] ?></small>
					</div>
					
					<div class="form-group">
						<label for="inputWpClientSecret"><?php echo $L['client-secret'] ?></label>
						<input type="text" class="form-control" id="inputWpClientSecret" name="wordpressClientSecret" value="">
					</div>
					
					<div class="form-group">
						<label for="inputWpBlogId"><?php echo $L['blog-id'] ?></label>
						<input type="text" class="form-control" id="inputWpBlogId" name="wpBlogId" value="">
						<small id="wpBlogIdHelp" class="form-text text-muted"><?php echo $L['wp-blog-id-tip'] ?></small>
					</div>
				</div>
			</div>
		</div>
		<hr />
		<div class="form-row">
			<div class="form-group col-md-6">
				<input type="hidden" name="_token" value="<?php echo generate_token( 'add-site' ) ?>">
					
				<div class="align-middle">
					<div class="float-left mt-1">
						<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
						<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'sites' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>