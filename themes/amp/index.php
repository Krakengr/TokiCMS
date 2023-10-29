<?php defined('TOKICMS') || die( 'Error...' ); ?><!DOCTYPE html>
<!---
Copyright 2017 The AMP Start Authors. All Rights Reserved.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at: http://www.apache.org/licenses/LICENSE-2.0
Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS-IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
-->
<html lang="<?php echo Theme::Locale() ?>" amp>
  <head>
    <?php include( THEME_AMP_DIR_PHP . 'header.php' ) ?>
  </head>
  <body>
    <!-- Start Navbar -->
    <?php include( THEME_AMP_DIR_PHP . 'navbar.php' ) ?>
	<!-- End Navbar -->
	 
	<?php if ( Settings::Amp()['enable_auto_ads'] && !empty( Settings::Amp()['codes']['google_ad_client_code'] ) ) : ?>
	<amp-auto-ads type="adsense" data-ad-client="<?php echo Settings::Amp()['codes']['google_ad_client_code'] ?>">
	</amp-auto-ads>
	<?php endif ?>

    <main id="content" role="main" class="">
		<?php include( THEME_AMP_DIR_PHP . 'post.php' ) ?>
    </main>

    <!-- Start Footer -->
    <?php include( THEME_AMP_DIR_PHP . 'footer.php' ) ?>
    <!-- End Footer -->
	
	<?php if ( !empty( Settings::Amp()['codes']['google_analytics_code'] ) ) : ?>
	<amp-analytics type="googleanalytics">
	<script type="application/json">{
	  "vars": {
		"account": "<?php echo Settings::Amp()['codes']['google_analytics_code'] ?>"
	  },
	  "triggers": {
		"trackPageview": {
		  "on": "visible",
		  "request": "pageview"
		}
	  }
	}</script>
	</amp-analytics>
	<?php endif ?>
  </body>
</html>