<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# General Settings Form
#
#####################################################

$L = $this->lang;

include ( ARRAYS_ROOT . 'generic-arrays.php');

$socialData = $hosts = $commentsys = array();

$socialMedia = $this->currentLang['settings']['social'];

$data = Json( $this->adminSettings::Site()['maintenance_data'] );

$apiKeys = Json( $this->adminSettings::Get()['api_keys'] );

$footer_text = ( !empty( $this->currentLang['settings']['footer_text'] ) ? $this->currentLang['settings']['footer_text'] : ( !empty( $this->adminSettings::LangData()['settings']['footer_text'] ) ? $this->adminSettings::LangData()['settings']['footer_text'] : '' ) );

$after_content_text = ( !empty( $this->currentLang['settings']['after_content_text'] ) ? $this->currentLang['settings']['after_content_text'] : '' );

$ext = ( isset( $apiKeys[$this->LangKey()]['blog-' . $this->blogID] ) ? $apiKeys[$this->LangKey()]['blog-' . $this->blogID] : array() );

$backgImage = ( ( isset( $data['background_image'] ) && !empty( $data['background_image'] ) ) ? $data['background_image'] :  null );

$extSys 	= $this->currentLang['settings']['ext_comm_system'];
$extSysName = $this->currentLang['settings']['ext_comm_shortname'];

//Create the social network data
//Do it like this, so it will be updated every time we add a new social network in the "socialNetworksArray"
foreach( $socialNetworksArray as $sId => $sRow )
{
	$socialData[$sRow['name']] = array( 'label' => $sRow['title'], 'type' => 'text', 'name' => 'social[' . $sRow['name'] . ']', 'value' => ( isset( $socialMedia[$sRow['name']] ) ? $socialMedia[$sRow['name']] : null ), 'tip' => null );
}

$defaultImage = HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS . 'default.svg';

$siteImage = ( isset( $this->adminSettings::Get()['siteImage']['default'] ) ? $this->adminSettings::Get()['siteImage']['default']['url'] : null );

$footerButtons = array(
	'site-title' => array( 'title' => $L['site-title'], 'var' => '{{site-title}} ' ),
	'site-slogan' => array( 'title' => $L['site-slogan'], 'var' => '{{site-slogan}} ' ),
	'site-description' => array( 'title' => $L['site-description'], 'var' => '{{site-description}} ' ),
	'site-url' => array( 'title' => $L['site-url'], 'var' => '{{site-url}} ' ),
	'current-year' => array( 'title' => $L['current-year'], 'var' => '{{current-year}} ' ),
	'copyright' => array( 'title' => '&copy;', 'var' => '{{copyright}} ' ),
	'powered-by-toki-cms' => array( 'title' => 'Powered by TokiCMS', 'var' => '{{powered-by-toki-cms}} ' ),
);

$uploadMaintHtml = '
<div class="row">
	<div class="col-2">
		<label for="background-image" class="">' . __( 'background-image' ) . '</label>
	</div>';

$uploadMaintHtml .= '
	<div class="col-3">';
		
$uploadMaintHtml .= '
	<button type="button" class="btn btn-primary float-left" data-toggle="modal" data-target="#addImage" id="siteBackgModal"><i class="far fa-image"></i> ' .  __( 'add-media' ) . '</button>';
			
$args = array(
	'id' => 'buttonRemoveBackg',
	'title' => '<i class="fa fa-trash"></i> ' . __( 'remove-image' ),
	'class' => 'btn-danger float-right' . ( $backgImage ? '' : ' d-none' )
);

$uploadMaintHtml .= Button( $args, false );

$uploadMaintHtml .= '
	</div>

	<div class="col-6">
		<img id="backgPreview" width="200" class="img-fluid img-thumbnail" alt="' . $L['background-image'] . ' ' . $L['preview'] . '" src="' . ( $backgImage ? $backgImage : $defaultImage ). '" />
	</div>';
		
	$uploadMaintHtml .= HiddenFormInput( array( 'name' => 'siteBackgFile', 'value' => ( $backgImage ? $backgImage : '' ) ), false );

$uploadMaintHtml .= '	
</div>';

$uploadHtml = '
<div class="container">
	<div class="row">
		<div class="col-lg-4 col-sm-12 p-0 pr-2">';

		$uploadHtml .= HiddenFormInput( array( 'name' => 'siteLogoFile', 'value' =>  $siteImage ? $this->adminSettings::Get()['siteImage']['default']['id'] : '' ), false );
		
		$args = array(
				'id' => 'buttonRemoveLogo',
				'title' => '<i class="fa fa-trash"></i> ' . __( 'remove-logo' ),
				'class' => 'btn-danger float-right' . ( $siteImage ? '' : ' d-none' )
			
		);
			
		$uploadHtml .= Button( $args, false );
		
		$uploadHtml .= '
			<button type="button" class="btn btn-primary float-left" data-toggle="modal" data-target="#addImage" id="siteImageModal"><i class="far fa-image"></i> ' .  __( 'add-media' ) . '</button>
		</div>
		<div class="col-lg-8 col-sm-12 p-0 text-center">
			<img id="siteLogoPreview" width="400" class="img-fluid img-thumbnail" alt="' . $L['site-logo'] . ' ' . $L['preview'] . '" src="' . ( !$siteImage ? $defaultImage : $siteImage ) . '" />
		</div>
	</div>
</div>';

foreach ( $siteHosts as $key => $row )
{
	$hosts[$key] = array( 'name' => $key, 'title'=> $row['title'] , 'disabled' => false, 'data' => array() );
}

foreach ( $externalCommentsArray as $key => $row )
{
	$commentsys[$key] = array( 'name' => $key, 'title'=> $row['title'] , 'disabled' => false, 'data' => array() );
}

$form = array
(
	'site-information' => array
	(
		'title' => $L['site-information'],
		'data' => array
		(
			'site-information' => array(
				'title' => null, 'data' => array
				(
					'title'=>array( 'label'=>$L['site-title'], 'type'=>'text', 'name' => 'lang[site_name]', 'value' => htmlspecialchars_decode( $this->currentLang['settings']['site_name'] ), 'tip'=>null ),
					
					'slogan'=>array( 'label'=>$L['site-slogan'], 'type'=>'text', 'name' => 'lang[site_slogan]', 'value' => htmlspecialchars_decode( $this->currentLang['settings']['site_slogan'] ), 'tip'=>$L['use-this-field-to-add-a-catchy-phrase'] ),
					
					'description'=>array( 'label'=>$L['site-description'], 'type'=>'text', 'name' => 'lang[site_description]', 'value' => htmlspecialchars_decode( $this->currentLang['settings']['site_description'] ), 'tip'=>$L['you-can-add-a-site-description'] ),
					
					'comment-sys'=>array('label'=>$L['comment-systems'], 'type'=>'select', 'name' => 'lang[comment_sys]', 'value'=>$extSys, 'tip'=>$L['comment-systems-tip'], 'firstNull' => false, 'data' => $commentsys ),
					
					'disqus-shortname'=>array('label'=>$L['disqus-shortname'], 'name' => 'lang[disqus_shortname]', 'type'=>'text', 'value'=>( ( $extSys == 'disqus' ) ? $extSysName : '' ), 'tip'=>$L['disqus-shortname-tip'], 'disabled' => false, 'hide' => ( !$this->IsDefaultLang() ? true : false ), 'dnone' => ( ( $extSys == 'disqus' ) ? false : true ), 'div-id' => 'disqus' ),
					
					'fb-comments'=>array('label'=>$L['facebook-comments'], 'name' => 'lang[facebook_shortname]', 'type'=>'text', 'value'=>( ( $extSys == 'fb-comments' ) ? $extSysName : '' ), 'tip'=>$L['facebook-comments-shortname-tip'], 'disabled' => false, 'hide' => ( !$this->IsDefaultLang() ? true : false ), 'dnone' => ( ( $extSys == 'fb-comments' ) ? false : true ), 'div-id' => 'fb-comments' ),
					
					'intensedebate'=>array('label'=>$L['intensedebate'], 'name' => 'lang[intensedebate_shortname]', 'type'=>'text', 'value'=>( ( $extSys == 'intensedebate' ) ? $extSysName : '' ), 'tip'=>$L['intensedebate-shortname-tip'], 'disabled' => false, 'hide' => ( !$this->IsDefaultLang() ? true : false ), 'dnone' => ( ( $extSys == 'intensedebate' ) ? false : true ), 'div-id' => 'intensedebate' ),
					
					'content-after-post'=>array('label'=>$L['content-after-post'], 'name' => 'lang[after_content_text]', 'type'=>'textarea', 'value'=>htmlspecialchars_decode( $after_content_text ), 'tip'=>$L['content-after-post-tip'] ),
					
					'footer-copyright-text'=>array('label'=>$L['footer-copyright-text'], 'name' => 'lang[footer_text]', 'type'=>'textarea', 'value'=>htmlspecialchars_decode( $footer_text ), 'tip'=>$L['footer-copyright-text-tip'], 'buttons' =>  $footerButtons ),
				)
			)
		)
			
	),
	
	'site-maintenance' => array
	(
		'title' => $L['maintenance-mode'],
		'hide' => ( !$this->siteIsSelfHosted ? true : false ),
		'data' => array
		(
			'maintenance-mode' => array(
				'title' => null, 'data' => array
				(
					'enable-maintenance-mode'=>array('label'=>$L['enable-maintenance-mode'], 'name' => 'site[enable_maintenance]', 'type'=>'checkbox', 'value'=>$this->adminSettings::Site()['enable_maintenance'], 'tip'=>null ),
					
					'enable-login-maintenance'=>array('label'=>$L['enable-login-maintenance'], 'name' => 'site[enable_login_maintenance]', 'type'=>'checkbox', 'value'=>$this->adminSettings::Site()['enable_login_maintenance'], 'tip'=>null ),
					
					'page-title'=>array('label'=>$L['page-title'], 'type'=>'text', 'name' => 'data[page_title]', 'value' => ( isset( $data['page_title'] ) ? htmlspecialchars_decode( $data['page_title'] ) : $L['site-is-undergoing-maintenance'] ), 'tip'=>null),
					
					'maintenance-subject'=>array('label'=>$L['maintenance-subject'], 'type'=>'text', 'name' => 'data[maintenance_subject]', 'value' => ( isset( $data['maintenance_subject'] ) ? htmlspecialchars_decode( $data['maintenance_subject'] ) : $L['planned-maintenance-in-progress'] ), 'tip'=>null),
					
					'maintenance-text'=>array('label'=>$L['maintenance-text'], 'type'=>'textarea', 'name' => 'data[maintenance_text]', 'value' => ( isset( $data['maintenance_text'] ) ? htmlspecialchars_decode( $data['maintenance_text'] ) : $L['site-will-be-available-soon-thank-you-for-your-patience'] ), 'tip'=>null),
					
					'footer-text'=>array('label'=>$L['footer-text'], 'type'=>'text', 'name' => 'data[footer_text]', 'value' => ( isset( $data['footer_text'] ) ? htmlspecialchars_decode( $data['footer_text'] ) : '&copy; My Site ' . date ('Y', time() ) ), 'tip'=>null),
					
					'upload-site-logo'=>array('label'=>null, 'name' => 'settings[site_default_image]', 'type'=>'custom-html', 'value'=>$uploadMaintHtml, 'tip'=>null, 'disabled' => false )
				)
			)
		)
	),
	
	'host-settings' => array
	(
		'title' => $L['host-settings'],
		'hide' => ( !$this->siteIsSelfHosted ? false : true ),
		'data' => array
		(
		
			'host-settings' => array(
				'title' => null, 'data' => array
				(
					'hosts'=>array('label'=>$L['site-is'], 'type'=>'select', 'name' => 'siteHost', 'value'=>$this->siteHost, 'tip'=>$L['site-is-hosted-tip'], 'firstNull' => false, 'data' => $hosts ),
				)
			),
		
			'blogger-settings' => array(
				'title' => null, 'hide' => ( ( $this->siteHost == 'blogger' ) ? false : true ), 'data' => array
				(
					'blogger-api'=>array('label'=>$L['blogger-api'], 'type'=>'text', 'name' => 'bloggerApi', 'value' => ( isset( $ext['blogger']['api'] ) ? htmlspecialchars_decode( $ext['blogger']['api'] ) : '' ), 'tip'=>$L['blogger-api-tip'] ),
					
					'blogger-oauth'=>array('label'=>$L['blogger-oauth-2-token'], 'type'=>'text', 'name' => 'bloggerOath', 'value' => ( isset( $ext['blogger']['oath2'] ) ? htmlspecialchars_decode( $ext['blogger']['oath2'] ) : '' ), 'tip'=>$L['blogger-oauth-2-token-tip'] ),
					
					'blogger-blog-id'=>array('label'=>$L['blog-id'], 'type'=>'text', 'name' => 'bloggerBlogId', 'value' => ( isset( $ext['blogger']['blog-id'] ) ? htmlspecialchars_decode( $ext['blogger']['blog-id'] ) : '' ), 'tip'=>$L['blogger-blog-id-tip'] ),
				)
			),
			
			'wp-settings' => array(
				'title' => $L['wordpress-api'], 'hide' => ( ( $this->siteHost == 'wordpress' ) ? false : true ), 'data' => array
				(
					'wp-client-id'=>array('label'=>$L['client-id'], 'type'=>'text', 'name' => 'wordpressClientApi', 'value' => ( isset( $ext['wordpress']['client-id'] ) ? htmlspecialchars_decode( $ext['blogger']['api'] ) : '' ), 'tip'=>$L['wordpress-api-tip'] ),
					
					'wp-client-secret'=>array('label'=>$L['client-secret'], 'type'=>'text', 'name' => 'wordpressClientSecret', 'value' => ( isset( $ext['wordpress']['client-secret'] ) ? htmlspecialchars_decode( $ext['wordpress']['client-secret'] ) : '' ), 'tip'=>null ),
					
					'wp-blog-id'=>array('label'=>$L['blog-id'], 'type'=>'text', 'name' => 'wpBlogId', 'value' => ( isset( $ext['wordpress']['blog-id'] ) ? htmlspecialchars_decode( $ext['wordpress']['blog-id'] ) : '' ), 'tip'=>$L['wp-blog-id-tip'] ),
				)
			),
		)
	),
	
	'general-tools' => array
	(
		'title' => $L['general-tools'],
		'data' => array
		(
			'site-settings' => array(
				'title' => null, 'data' => array
				(
					'enable-multiblog-mode'=>array('label'=>$L['enable-multiblog-mode'], 'name' => 'site[enable_multiblog]', 'type'=>'checkbox', 'value'=>$this->adminSettings::Site()['enable_multiblog'], 'tip'=>$L['multiblog-tip'], 'disabled' => false, 'hide' => ( !$this->siteIsSelfHosted ? true : false ) ),
					'enable-multisite-mode'=>array('label'=>$L['enable-multisite-mode'], 'name' => 'site[enable_multisite]', 'type'=>'checkbox', 'value'=>$this->adminSettings::Site()['enable_multisite'], 'tip'=>$L['multisite-tip'], 'disabled' => false, 'hide' => ( ( !$this->siteIsSelfHosted || $this->isChildSite ) ? true : false ) ),
					'enable-polylang-mode'=>array('label'=>$L['enable-polylang-mode'], 'name' => 'site[enable_multilang]', 'type'=>'checkbox', 'value'=>$this->adminSettings::Site()['enable_multilang'], 'tip'=>$L['polylang-tip'], 'disabled' => false )
				)
			)
		)
	),
	
	'site-social-media' => array
	(
		'title' => $L['social-profiles'],
		'hide' => ( !$this->siteIsSelfHosted ? true : false ),
		'data' => array
		(
			'site-social' => array(
				'title' => null, 'tip' =>$L['social-profiles-info'], 'data' => $socialData
			)
		)
	),
	
	'site-logo' => array
	(
		'title' => $L['site-logo'],
		'hide' => ( !$this->siteIsSelfHosted ? true : false ),
		'data' => array
		(
			'site-logo' => array(
				'title' => null, 'data' => array
				(
					'upload-site-logo'=>array('label'=>null, 'name' => 'settings[site_default_image]', 'type'=>'custom-html', 'value'=>$uploadHtml, 'tip'=>null, 'disabled' => false )
				)
			)
		)
	)
);