<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Cookie Consent Form
#
#####################################################

$L = $this->lang;

$cookie = Json( $this->currentLang['settings']['cookie_data'] );

$cookieOptions = array();

$imgPath = TOOLS_HTML . 'theme_files' . PS . 'assets' . PS . 'backend' . PS . 'img' . PS;

$cookieOptions['dark-bottom'] = array( 'name' => 'dark-bottom', 'title'=> $L['dark-bottom'], 'disabled' => false, 'data' => array() );
$cookieOptions['dark-floating'] = array( 'name' => 'dark-floating', 'title'=> $L['dark-floating'], 'disabled' => false, 'data' => array() );
$cookieOptions['dark-top'] = array( 'name' => 'dark-top', 'title'=> $L['dark-top'], 'disabled' => false, 'data' => array() );$cookieOptions['light-bottom'] = array( 'name' => 'light-bottom', 'title'=> $L['light-bottom'], 'disabled' => false, 'data' => array() );
$cookieOptions['light-floating'] = array( 'name' => 'light-floating', 'title'=> $L['light-floating'], 'disabled' => false, 'data' => array() );
$cookieOptions['light-top'] = array( 'name' => 'light-top', 'title'=> $L['light-top'], 'disabled' => false, 'data' => array() );

//Set the Custom HTML Data
$addCustomFileHtml = '
<div class="form-group row">
	<label for="fileInput" class="col-sm-2 col-form-label">' . $L['theme-options'] . '</label>
	<div class="d-flex flex-wrap">
		<figure style="margin-right:10px"><img src="' . $imgPath . 'dark-bottom.png" alt="dark-bottom" style="border: solid 1px #212529;width:150px" /> <figcaption class="uk-thumbnail-caption">' .$L['dark-bottom']. '</figcaption></figure>
		
		<figure style="margin-right:10px"><img src="' . $imgPath . 'dark-floating.png" alt="dark-floating" style="border: solid 1px #212529;width:150px" /> <figcaption class="uk-thumbnail-caption">' . $L['dark-floating'] . '</figcaption></figure>
		
		<figure style="margin-right:10px"><img src="' . $imgPath . 'dark-top.png" alt="dark-top" style="border: solid 1px #212529;width:150px" /> <figcaption class="uk-thumbnail-caption">' . $L['dark-top'] . '</figcaption></figure>
		
		<figure style="margin-right:10px"><img src="' . $imgPath . 'light-bottom.png" alt="light-bottom" style="border: solid 1px #212529;width:150px" /> <figcaption class="uk-thumbnail-caption">' . $L['light-bottom'] . '</figcaption></figure>
		
		<figure style="margin-right:10px"><img src="' . $imgPath . 'light-floating.png" alt="light-floating" style="border: solid 1px #212529;width:150px" /> <figcaption class="uk-thumbnail-caption">' . $L['light-floating'].  '</figcaption></figure>
		
		<figure style="margin-right:10px"><img src="' . $imgPath . 'light-top.png" alt="light-top" style="border: solid 1px #212529;width:150px" /> <figcaption class="uk-thumbnail-caption">' . $L['light-top'] . '</figcaption></figure>
	</div>
<div>';

$form = array
(
	'cookie-consent' => array
	(
		'title' => $L['cookie-consent-settings'],
		'data' => array(
		
			'cookie-settings' => array( 
				'title' => null, 'data' => array
				(
					'choose-theme'=>array('label'=>$L['choose-theme'], 'name' => null, 'type'=>'custom-html', 'value'=>$addCustomFileHtml, 'tip'=>null ),
					
					'cookie-theme'=>array('label'=>$L['theme'], 'name' => 'theme', 'type'=>'select', 'value'=>( isset( $cookie['theme'] ) ? $cookie['theme'] : null ), 'tip'=>null, 'firstNull' => false, 'data' => $cookieOptions, 'id' => 'slcAmp', 'class' => 'form-select shadow-none mt-3', 'multiple' => false ),
				
					'consent-message'=>array('label'=>$L['cookie-consent-message'], 'type'=>'textarea', 'name' => 'consent_message', 'value' =>( isset( $cookie['consent_message'] ) ? StripContent( $cookie['consent_message'] ) : '' ), 'required' => false, 'tip'=> $L['cookie-consent-message-tip'] ),
					
					'cookie-consent-url'=>array('label'=>$L['cookie-consent-url'], 'type'=>'text', 'name' => 'consent_url', 'value' =>( isset( $cookie['consent_url'] ) ? $cookie['consent_url'] : '' ), 'required' => false, 'tip'=> $L['cookie-consent-url-tip'] ),
					
					'cookie-consent-more-txt'=>array('label'=>$L['cookie-consent-more-txt'], 'type'=>'text', 'name' => 'consent_more_txt', 'value' =>( isset( $cookie['consent_more_txt'] ) ? $cookie['consent_more_txt'] : '' ), 'required' => false, 'tip'=> $L['cookie-consent-more-txt-tip'] ),
					
					'cookie-dismiss'=>array('label'=>$L['cookie-consent-dismiss-button'], 'type'=>'text', 'name' => 'consent_dismiss', 'value' =>( isset( $cookie['consent_dismiss'] ) ? $cookie['consent_dismiss'] : '' ), 'required' => false, 'tip'=> $L['cookie-consent-dismiss-button-tip'] )
				)
			)
		)
	)
);
