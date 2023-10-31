<?php defined('TOKICMS') or die('Hacking attempt...');

//V2

#####################################################
#
# General Settings Array
#
#####################################################
//General Settings Array to check if the current action exists in this one
//this is being used for the menu only, to set the current tab, etc.
$settingsArrayFilter = array( 'settings', 'advanced-settings', 'post-settings', 'performance', 'privacy', 'widgets',
'language', 'security', 'themes', 'plugins', 'amp-settings', 'menus', 'cookie-consent', 'auto-menu-settings' );

$buttonsArray = array(
	'core' => array
	(
		'title' => $L['core'],
		'href' => '#',
		'show' => true,
		'haspopup' => false,
		'newtab' => false,
		'class' => 'sb-sidenav-menu-heading',
		'icon' => '',
		'items' => array
		(
			'dashboard' => array
			(
				'title' => $L['dashboard'],
				'href' => $this->GetUrl(),
				'show' => true,
				'newtab' => false,
				'collapsed' => false,
				'current' => ( ( $this->currentAction && ( $this->currentAction == 'dashboard' ) ) ? true : false ),
				'class' => 'nav-link',
				'icon' => '<i class="nav-icon fas fa-tachometer-alt"></i>',
				'child' => array()
			),

			'settings' => array
			(
				'title' => $L['settings'],
				'href' => '#',
				'show' => ( IsAllowedTo( 'admin-site' ) ? true : false ),
				'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $settingsArrayFilter ) ) ? true : false ),
				'newtab' => false,
				'class' => 'nav-link',
				'icon' => '<i class="nav-icon fas fa-screwdriver"></i>',
				'child' => array(

					'settings' => array
					(
						'title' => $L['general-settings'],
						'href' => $this->GetUrl( 'settings' ),
						'show' => ( IsAllowedTo( 'admin-site' ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'settings' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-tune-vertical"></i>',
						'child' => array()
					),

					'advanced-settings' => array
					(
						'title' => $L['advanced-settings'],
						'href' => $this->GetUrl( 'advanced-settings' ),
						'show' => ( IsAllowedTo( 'admin-site' ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'advanced-settings' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-tune"></i>',
						'child' => array()
					),
					
					'language-settings' => array
					(
						'title' => $L['lang-settings'],
						'href' => $this->GetUrl( 'language' ),
						'show' => ( ( IsAllowedTo( 'manage-languages' ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'language' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-translate"></i>',
						'child' => array()
					),

					'post-settings' => array
					(
						'title' => $L['post-comments-settings'],
						'href' => $this->GetUrl( 'post-settings' ),
						'show' => ( IsAllowedTo( 'manage-posts' ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'post-settings' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-vector-arrange-above"></i>',
						'child' => array()
					),
					
					'auto-menu' => array
					(
						'title' => $L['auto-menu'],
						'href' => $this->GetUrl( 'auto-menu-settings' ),
						'show' => ( ( IsAllowedTo( 'admin-site' ) && $this->adminSettings::IsTrue( 'enable_auto_menu' ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'auto-menu-settings' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-vector-difference-ab"></i>',
						'child' => array()
					),
					
					'cookie-consent' => array
					(
						'title' => $L['cookie-consent'],
						'href' => $this->GetUrl( 'cookie-consent' ),
						'show' => ( ( $this->adminSettings::IsTrue( 'enable_cookie_concent' ) && IsAllowedTo( 'admin-site' ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'cookie-consent' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-block-helper"></i>',
						'child' => array()
					),
					
					'menus' => array
					(
						'title' => $L['menus'],
						'href' => $this->GetUrl( 'menus' ),
						'show' => ( ( !$this->adminSettings::IsTrue( 'enable_auto_menu' ) && IsAllowedTo( 'admin-site' ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'menus' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-vector-line"></i>',
						'child' => array()
					),
					
					'themes' => array
					(
						'title' => $L['themes'],
						'href' => $this->GetUrl( 'themes' ),
						'show' => ( ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-themes' ) ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'themes' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-vector-difference-ab"></i>',
						'child' => array()
					),
					
					'plugins' => array
					(
						'title' => $L['plugins'],
						'href' => $this->GetUrl( 'plugins' ),
						'show' => ( ( IsAllowedTo( 'admin-site' ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'plugins' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-arrow-expand-all"></i>',
						'child' => array()
					),
					
					'widgets' => array
					(
						'title' => $L['widgets'],
						'href' => $this->GetUrl( 'widgets' ),
						'show' => ( ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-widgets' ) ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'widgets' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-apps"></i>',
						'child' => array()
					),
					
					'privacy' => array
					(
						'title' => $L['privacy'],
						'href' => $this->GetUrl( 'privacy' ),
						'show' => ( ( IsAllowedTo( 'admin-site' ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'privacy' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-treasure-chest"></i>',
						'child' => array()
					),
					
					'security' => array
					(
						'title' => $L['security'],
						'href' => $this->GetUrl( 'security' ),
						'show' => ( ( IsAllowedTo( 'admin-site' ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'security' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-sign-caution"></i>',
						'child' => array()
					),

					'performance' => array
					(
						'title' => $L['performance'],
						'href' => $this->GetUrl( 'performance' ),
						'show' => ( ( IsAllowedTo( 'admin-site' ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'performance' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-speedometer"></i>',
						'child' => array()
					),
					
					'amp-settings' => array
					(
						'title' => $L['amp-settings'],
						'href' => $this->GetUrl( 'amp-settings' ),
						'show' => ( ( $this->adminSettings::IsTrue( 'enable_amp' ) && IsAllowedTo( 'admin-site' ) && $this->siteIsSelfHosted ) ? true : false ),
						'newtab' => false,
						'current' => ( ( $this->currentAction && ( $this->currentAction == 'amp-settings' ) ) ? true : false ),
						'class' => 'nav-link',
						'icon' => '<i class="mdi mdi-tablet-ipad"></i>',
						'child' => array()
					),
				)
			),
		)
	)
);

#####################################################
#
# Auto Content Array
#
#####################################################
$AutoContentArrayFilter = array( 'auto-content-settings', 'auto-content-sources', 'add-source', 'add-source-xml' );

$AutoContentArray = array
(
	'title' => $L['auto-content'],
	'href' => '#',
	'show' => ( ( $this->adminSettings::IsTrue( 'enable_autoblog' ) && ( IsAllowedTo( 'manage-auto-content' ) || IsAllowedTo( 'admin-site' ) ) ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $AutoContentArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-file-contract"></i>',
	'child' => array(
		'auto-content-settings' => array
		(
			'title' => $L['auto-content-settings'],
			'href' => $this->GetUrl( 'auto-content-settings' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'auto-content-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-tune"></i>',
			'child' => array()
		),
		
		'auto-content-sources' => array
		(
			'title' => $L['sources'],
			'href' => $this->GetUrl( 'auto-content-sources' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'auto-content-sources' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-boxes"></i>',
			'child' => array()
		),
		
		'add-content-source' => array
		(
			'title' => $L['add-source'],
			'href' => $this->GetUrl( 'add-source' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-source' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Forum Array
#
#####################################################
$forumArrayFilter = array( 'forums', 'forum-settings' );

$forumArray = array
(
	'title' => $L['forums'],
	'href' => '#',
	'show' => ( ( $this->IsEnabled( 'forum' ) && ( IsAllowedTo( 'manage-forum' ) || IsAllowedTo( 'admin-site' ) ) && $this->siteIsSelfHosted ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $forumArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-comments"></i>',
	'child' => array(
		'forums' => array
		(
			'title' => $L['forums'],
			'href' => $this->GetUrl( 'forums' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'forums' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-boxes"></i>',
			'child' => array()
		),
		
		'forum-settings' => array
		(
			'title' => $L['settings'],
			'href' => $this->GetUrl( 'forum-settings' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'forum-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		),
		
		
	)
);

#####################################################
#
# Users Array
#
#####################################################
$usersArrayFilter = array( 'users', 'add-user', 'membergroups', 'add-membergroup' );

$usersArray = array
(
	'title' => $L['users'],
	'href' => '#',
	'show' => ( ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'view-mlist' ) || IsAllowedTo( 'manage-members' ) ) && $this->siteIsSelfHosted ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $usersArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-users"></i>',
	'child' => array(
		'users' => array
		(
			'title' => $L['users'],
			'href' => $this->GetUrl( 'users' ),
			'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'view-mlist' ) || IsAllowedTo( 'manage-members' ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'users' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-account"></i>',
			'child' => array()
		),
		
		'membergroups' => array
		(
			'title' => $L['membergroups'],
			'href' => $this->GetUrl( 'membergroups' ),
			'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'view-mlist' ) || IsAllowedTo( 'manage-members' ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'membergroups' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-account-multiple"></i>',
			'child' => array()
		),

		'add-user' => array
		(
			'title' => $L['add-a-new-user'],
			'href' => $this->GetUrl( 'add-user' ),
			'show' => ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-members' ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-user' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		),
		
		'add-membergroup' => array
		(
			'title' => $L['add-membergroup'],
			'href' => $this->GetUrl( 'add-membergroup' ),
			'show' => ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-members' ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-membergroup' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-account-multiple-plus"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Forms Array
#
#####################################################
$formsArrayFilter = array( 'forms', 'tables', 'add-form', 'add-table', 'form-templates' );

$formsArray = array
(
	'title' => $L['forms-and-tables'],
	'href' => '#',
	'show' => ( ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-forms' ) ) && $this->siteIsSelfHosted ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $formsArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-table"></i>',
	'child' => array(
		
		'forms' => array
		(
			'title' => $L['forms'],
			'href' => $this->GetUrl( 'forms' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'forms' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => null,
			'child' => array()
		),
		
		'form-templates' => array
		(
			'title' => $L['form-templates'],
			'href' => $this->GetUrl( 'form-templates' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'form-templates' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => null,
			'child' => array()
		),
		
		'tables' => array
		(
			'title' => $L['tables'],
			'href' => $this->GetUrl( 'tables' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'tables' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => null,
			'child' => array()
		),
		
		'add-form' => array
		(
			'title' => $L['add-new-form'],
			'href' => $this->GetUrl( 'add-form' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-form' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => null,
			'child' => array()
		),
		
		'add-table' => array
		(
			'title' => $L['add-new-table'],
			'href' => $this->GetUrl( 'add-table' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-table' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => null,
			'child' => array()
		)
	)
);

#####################################################
#
# Emails Array
#
#####################################################
$emailsArrayFilter = array( 'emails', 'compose-mail' );

$totalMails = AdminTotalEmailsCount();

$emailsArray = array
(
	'title' 	=> $L['emails'],
	'href' 		=> '#',
	'show' 		=> ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'view-emails' ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $emailsArrayFilter ) ) ? true : false ),
	'newtab' 	=> false,
	'class' 	=> 'nav-link',
	'icon' 		=> '<i class="nav-icon fas fa-envelope"></i>',
	'num-info' 	=> $totalMails,
	'child' => array(
		'emails' => array
		(
			'title' => $L['emails'],
			'href' => $this->GetUrl( 'emails' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'emails' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => null,
			'child' => array()
		),
		
		'compose-mail' => array
		(
			'title' => $L['compose'],
			'href' => $this->GetUrl( 'compose-mail' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'compose-mail' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Sites Array
#
#####################################################
$sitesArrayFilter = array( 'sites', 'add-site' );

$sitesArray = array
(
	'title' => $L['sites'],
	'href' => '#',
	'show' => ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-sites' ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $sitesArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-boxes"></i>',
	'child' => array(
		'sites' => array
		(
			'title' => $L['sites'],
			'href' => $this->GetUrl( 'sites' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'sites' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-boxes"></i>',
			'child' => array()
		),
		
		'add-site' => array
		(
			'title' => $L['add-new-site'],
			'href' => $this->GetUrl( 'add-site' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-site' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Tools Array
#
#####################################################
$toolsArrayFilter = array( 'tools', 'import', 'media-embedder', 'automatic-translator', 'api', 'notifications' );

$toolsArray = array
(
	'title' => $L['tools'],
	'href' => '#',
	'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-video-content' ) || IsAllowedTo( 'import-content' ) || IsAllowedTo( 'manage-widgets' ) || IsAllowedTo( 'manage-api' ) ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $toolsArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-toolbox"></i>',
	'child' => array(
		'tools' => array
		(
			'title' => $L['tools'],
			'href' => $this->GetUrl( 'tools' ),
			'show' => ( IsAllowedTo( 'admin-site' ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'tools' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-text-shadow"></i>',
			'child' => array()
		),
		
		'automatic-translator' => array
		(
			'title' => $L['automatic-translator'],
			'href' => $this->GetUrl( 'automatic-translator' ),
			'show' => ( ( $this->adminSettings::IsTrue( 'enable_auto_translate' ) && $this->siteIsSelfHosted && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-languages' ) ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'automatic-translator' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-video"></i>',
			'child' => array()
		),
		
		'media-embedder' => array
		(
			'title' => $L['media-embedder'],
			'href' => $this->GetUrl( 'media-embedder' ),
			'show' => ( IsAllowedTo( 'admin-site' ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'media-embedder' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-video"></i>',
			'child' => array()
		),
		
		'api' => array
		(
			'title' => $L['api'],
			'href' => $this->GetUrl( 'api' ),
			'show' => ( ( $this->adminSettings::IsTrue( 'enable_api' ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-api' ) ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'api' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-buffer"></i>',
			'child' => array()
		),
		/*
		'notifications' => array
		(
			'title' => $L['notifications'],
			'href' => $this->GetUrl( 'notifications' ),
			'show' => ( IsAllowedTo( 'admin-site' ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'notifications' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-numeric"></i>',
			'child' => array()
		),
		*/
		'import' => array
		(
			'title' => $L['import'],
			'href' => $this->GetUrl( 'import' ),
			'show' => ( IsAllowedTo( 'import-content' ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'import' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-network-upload"></i>',
			'child' => array()
		),
	)
);

#####################################################
#
# Posts Array
#
#####################################################
$postsArrayFilter = array( 'posts', 'pages', 'categories', 'edit-category', 'tags', 'edit-tag', 'edit-post', 'add-post', 'edit-page', 'move-posts', 'custom-post-types', 'post-attributes', 'add-attribute', 'attribute-groups', 'add-attribute-group', 'edit-attribute-group', 'edit-post-attribute', 'filters' );

$postsArray = array
(
	'title' => $L['content'],
	'href' => '#',
	'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'manage-own-posts' ) || IsAllowedTo( 'create-new-posts' ) ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $postsArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-copy"></i>',
	'child' => array(
		'posts' => array
		(
			'title' => $L['posts'],
			'href' => $this->GetUrl( 'posts' ),
			'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'manage-own-posts' ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'posts' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-library-books"></i>',
			'child' => array()
		),
		
		'pages' => array
		(
			'title' => $L['pages'],
			'href' => $this->GetUrl( 'pages' ),
			'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'manage-own-posts' ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'pages' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-note-multiple"></i>',
			'child' => array()
		),
		
		'categories' => array
		(
			'title' => $L['categories'],
			'href' => $this->GetUrl( 'categories' ),
			'show' =>  ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'categories' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-lan-connect"></i>',
			'child' => array()
		),

		'add-post' => array
		(
			'title' => $L['add-post'],
			'href' => $this->GetUrl( 'add-post' ),
			'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'create-new-posts' ) || IsAllowedTo( 'manage-own-posts' ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-post' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-library-plus"></i>',
			'child' => array()
		),
		
		'add-page' => array
		(
			'title' => $L['add-page'],
			'href' => $this->GetUrl( 'add-page' ),
			'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'create-new-posts' ) || IsAllowedTo( 'manage-own-posts' ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-page' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-library-plus"></i>',
			'child' => array()
		),

		'move-posts' => array
		(
			'title' => $L['bulk-move-posts'],
			'href' => $this->GetUrl( 'move-posts' ),
			'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'move-posts' ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'move-posts' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-rotate-3d"></i>',
			'child' => array()
		),

		'custom-post-types' => array
		(
			'title' => $L['custom-post-types'],
			'href' => $this->GetUrl( 'custom-post-types' ),
			'show' => ( ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'manage-post-types' ) ) && $this->siteIsSelfHosted ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'custom-post-types' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-cube-unfolded"></i>',
			'child' => array()
		),

		'post-filters' => array
		(
			'title' => $L['filters'],
			'href' => $this->GetUrl( 'filters' ),
			'show' => ( ( ( $this->IsEnabled( 'coupons-and-deals' ) || $this->IsEnabled( 'compare-prices' ) || $this->IsEnabled( 'multivendor-marketplace' ) || $this->IsEnabled( 'store' ) ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-filters' ) ) && $this->siteIsSelfHosted ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'filters' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-cube-unfolded"></i>',
			'child' => array()
		),

		'post-attributes' => array
		(
			'title' => $L['post-attributes'],
			'href' => $this->GetUrl( 'post-attributes' ),
			'show' => ( ( $this->adminSettings::IsTrue( 'enable_post_attributes' ) && $this->siteIsSelfHosted && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'manage-post-attributes' ) ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'post-attributes' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-lan-connect"></i>',
			'child' => array()
		),
		
		'attribute-groups' => array
		(
			'title' => $L['attribute-groups'],
			'href' => $this->GetUrl( 'attribute-groups' ),
			'show' => ( ( $this->adminSettings::IsTrue( 'enable_post_attributes' ) && $this->siteIsSelfHosted && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'manage-post-attributes' ) ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'attribute-groups' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-view-module"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Redirection Array
#
#####################################################
$redirectionsArrayFilter = array( 'redirection-settings', 'add-redirection', 'redirections' );

$redirectionsArray = array
(
	'title' => $L['redirection'],
	'href' => '#',
	'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-redirections' ) ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $redirectionsArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-angle-double-right"></i>',
	'child' => array(
		'redirections' => array
		(
			'title' => $L['redirections'],
			'href' => $this->GetUrl( 'redirections' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'redirections' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-angle-double-right"></i>',
			'child' => array()
		),

		'settings' => array
		(
			'title' => $L['settings'],
			'href' => $this->GetUrl( 'redirection-settings' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'redirection-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-tune-vertical"></i>',
			'child' => array()
		),
		
		'add-redirection' => array
		(
			'title' => $L['add-redirection'],
			'href' => $this->GetUrl( 'add-redirection' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-redirection' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		),
	)
);

#####################################################
#
# Manufacturers Settings Array
#
#####################################################
$manufacturersArrayFilter = array( 'add-manufacturer', 'manufacturers', 'edit-manufacturer' );

$manufacturersArray = array
(
	'title' => $L['manufacturers'],
	'href' => '#',
	'show' => ( ( ( $this->IsEnabled( 'coupons-and-deals' ) || $this->IsEnabled( 'compare-prices' ) || $this->IsEnabled( 'multivendor-marketplace' ) || $this->IsEnabled( 'store' ) ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-manufacturers' ) ) && $this->siteIsSelfHosted ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $manufacturersArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-table"></i>',
	'child' => array(
	
		'manufacturers' => array
		(
			'title' => $L['manufacturers'],
			'href' => $this->GetUrl( 'manufacturers' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'manufacturers' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-lan-connect"></i>',
			'child' => array()
		),
		
		'add-manufacturer' => array
		(
			'title' => $L['add-new-manufacturer'],
			'href' => $this->GetUrl( 'add-manufacturer' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-manufacturer' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		)
	)
);
/*
#####################################################
#
# Vendors Settings Array
#
#####################################################
$vendorsArrayFilter = array( 'add-vendor', 'vendors', 'edit-vendor', 'vendor-attributes', 'add-vendor-attribute', 'vendor-attribute-groups', 'add-vendor-attribute-group' );

$vendorsArray = array
(
	'title' => $L['vendors'],
	'href' => '#',
	'show' => ( ( ( $this->IsEnabled( 'coupons-and-deals' ) || $this->IsEnabled( 'compare-prices' ) ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-vendors' ) ) ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $vendorsArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-warehouse"></i>',
	'child' => array(
	
		'vendors' => array
		(
			'title' => $L['vendors'],
			'href' => $this->GetUrl( 'vendors' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'vendors' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-lan-connect"></i>',
			'child' => array()
		),
		
		'add-vendor' => array
		(
			'title' => $L['add-new-vendor'],
			'href' => $this->GetUrl( 'add-vendor' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-vendor' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		),
		
		'vendors-attributes' => array
		(
			'title' => $L['vendors-attributes'],
			'href' => $this->GetUrl( 'vendors-attributes' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'vendors-attributes' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-lan-connect"></i>',
			'child' => array()
		),
		
		'attribute-groups' => array
		(
			'title' => $L['attribute-groups'],
			'href' => $this->GetUrl( 'vendors-attribute-groups' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'vendors-attribute-groups' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-view-module"></i>',
			'child' => array()
		)
	)
);
*/
#####################################################
#
# Stores Settings Array
#
#####################################################
$storesArrayFilter = array( 'add-store', 'stores', 'edit-store', 'stores-attributes', 'add-stores-attribute', 'edit-stores-attribute' );

$storesArray = array
(
	'title' => $L['stores'],
	'href' => '#',
	'show' => ( ( ( $this->IsEnabled( 'coupons-and-deals' ) || $this->IsEnabled( 'compare-prices' ) || $this->IsEnabled( 'multivendor-marketplace' ) || $this->IsEnabled( 'store' ) ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-stores' ) || IsAllowedTo( 'create-new-store' ) || IsAllowedTo( 'manage-own-store' ) ) && $this->siteIsSelfHosted ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $storesArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-store"></i>',
	'child' => array(
	
		'stores' => array
		(
			'title' => $L['stores'],
			'href' => $this->GetUrl( 'stores' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'stores' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-lan-connect"></i>',
			'child' => array()
		),
		
		'add-store' => array
		(
			'title' => $L['add-new-store'],
			'href' => $this->GetUrl( 'add-store' ),
			'show' => ( ( IsAllowedTo( 'manage-stores' ) || IsAllowedTo( 'create-new-store' ) ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-store' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		),
		
		'stores-attributes' => array
		(
			'title' => $L['stores-attributes'],
			'href' => $this->GetUrl( 'stores-attributes' ),
			'show' => ( IsAllowedTo( 'manage-stores' ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'stores-attributes' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-lan-connect"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Video Settings Array
#
#####################################################
$videosArrayFilter = array( 'add-playlist', 'video-playlists', 'edit-playlist' );

$videosArray = array
(
	'title' => $L['video-playlists'],
	'href' => '#',
	'show' => ( ( $this->IsEnabled( 'videos' ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-video-content' ) ) && $this->siteIsSelfHosted ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $videosArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-play"></i>',
	'child' => array(
	
		'video-playlists' => array
		(
			'title' => $L['video-playlists'],
			'href' => $this->GetUrl( 'video-playlists' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'video-playlists' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-lan-connect"></i>',
			'child' => array()
		),
		
		'add-playlist' => array
		(
			'title' => $L['add-new-video-playlist'],
			'href' => $this->GetUrl( 'add-playlist' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-playlist' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Multilang Array
#
#####################################################
$langsArrayFilter = array( 'langs', 'add-lang', 'lang-settings', 'slug-translation' );

$langsArray = array
(
	'title' => $L['langs'],
	'href' => '#',
	'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-languages' ) ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $langsArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-flag"></i>',
	'child' => array(
		'langs' => array
		(
			'title' => $L['langs'],
			'href' => $this->GetUrl( 'langs' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'langs' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-language"></i>',
			'child' => array()
		),
		
		'add-lang' => array
		(
			'title' => $L['add-new-language'],
			'href' => $this->GetUrl( 'add-lang' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-lang' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fa fa-plus"></i>',
			'child' => array()
		),
		
		'settings' => array
		(
			'title' => $L['settings'],
			'href' => $this->GetUrl( 'lang-settings' ),
			'show' => ( $this->siteIsSelfHosted ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'lang-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-tune-vertical"></i>',
			'child' => array()
		),
		
		'translate-url-slugs' => array
		(
			'title' => $L['translate-url-slugs'],
			'href' => $this->GetUrl( 'slug-translation' ),
			'show' => ( ( $this->adminSettings::IsTrue( 'translate_slugs' ) && $this->siteIsSelfHosted ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'slug-translation' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-case-sensitive-alt"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Link Manager Array
#
#####################################################
$linkManagerArrayFilter = array( 'broken-link-checker', 'link-manager', 'link-manager-settings', 'links', 'add-link' );

$linkManagerArray = array
(
	'title' => $L['link-manager'],
	'href' => '#',
	'show' => ( ( $this->adminSettings::IsTrue( 'enable_link_manager' ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-links' ) ) ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $linkManagerArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-link"></i>',
	'child' => array(
	
		'settings' => array
		(
			'title' => $L['settings'],
			'href' => $this->GetUrl( 'link-manager-settings' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'link-manager-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-view-module"></i>',
			'child' => array()
		),
		
		'links' => array
		(
			'title' => $L['links'],
			'href' => $this->GetUrl( 'links' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'links' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-numeric"></i>',
			'child' => array()
		),

		'broken-link-checker' => array
		(
			'title' => $L['broken-link-checker'],
			'href' => $this->GetUrl( 'broken-link-checker' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'broken-link-checker' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-numeric"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Social Media Auto Publish Array
#
#####################################################
$socialAutoPublishArrayFilter = array( 'social-media-auto-publish-settings' );

$socialAutoPublish = array
(
	'title' => $L['social-media-auto-publish'],
	'href' => '#',
	'show' => ( ( $this->adminSettings::IsTrue( 'enable_social_auto_publish' ) && ( IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'admin-site' ) ) ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $socialAutoPublishArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-hashtag"></i>',
	'child' => array(
		'settings' => array
		(
			'title' => $L['settings'],
			'href' => $this->GetUrl( 'social-media-auto-publish-settings' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'social-media-auto-publish-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-view-module"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# MultiBlogs Array
#
#####################################################
$blogsArrayFilter = array( 'blogs', 'add-blog' );

$blogsArray = array
(
	'title' => $L['blogs'],
	'href' => '#',
	'show' => ( ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-blogs' ) ) && $this->siteIsSelfHosted ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $blogsArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-cubes"></i>',
	'child' => array(
		'blogs' => array
		(
			'title' => $L['blogs'],
			'href' => $this->GetUrl( 'blogs' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'blogs' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-view-module"></i>',
			'child' => array()
		),
		
		'add-lang' => array
		(
			'title' => $L['add-new-blog'],
			'href' => $this->GetUrl( 'add-blog' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-blog' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# SEO Array
#
#####################################################
$seoArrayFilter = array( 'schema-settings', 'seo-settings', 'schemas', 'add-schema', 'sitemap-settings', 'robots-txt-settings', 'video-settings' );
$schemasSettings = $this->adminSettings::Seo();
$showSchemas = ( ( isset( $schemasSettings['enable_schema_markup'] ) &&  $schemasSettings['enable_schema_markup'] ) ? true : false );

$seoArray = array
(
	'title' => $L['seo'],
	'href' => '#',
	'show' => ( ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-seo' ) ) && $this->siteIsSelfHosted ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $seoArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fab fa-searchengin"></i>',
	'child' => array(
		'seo-settings' => array
		(
			'title' => $L['seo-settings'],
			'href' => $this->GetUrl( 'seo-settings' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'seo-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-tune-vertical"></i>',
			'child' => array()
		),
		
		'sitemap-settings' => array
		(
			'title' => $L['sitemap-settings'],
			'href' => $this->GetUrl( 'sitemap-settings' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'sitemap-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-sitemap"></i>',
			'child' => array()
		),
		
		'video-settings' => array
		(
			'title' => $L['seo-videos-settings'],
			'href' => $this->GetUrl( 'video-settings' ),
			'show' => ( $this->HasEnabled( 'videos' ) ? true : false ),
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'video-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-filmstrip"></i>',
			'child' => array()
		),
		
		'robots-txt-settings' => array
		(
			'title' => $L['robots-txt-settings'],
			'href' => $this->GetUrl( 'robots-txt-settings' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'robots-txt-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-robot"></i>',
			'child' => array()
		),
		
		'schema-settings' => array
		(
			'title' => $L['schema-settings'],
			'href' => $this->GetUrl( 'schema-settings' ),
			'show' => $showSchemas,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'schema-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-screwdriver"></i>',
			'child' => array()
		),
		
		'schemas' => array
		(
			'title' => $L['schemas'],
			'href' => $this->GetUrl( 'schemas' ),
			'show' => $showSchemas,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'schemas' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-code-parentheses"></i>',
			'child' => array()
		),
		
		'add-schema' => array
		(
			'title' => $L['add-schema'],
			'href' => $this->GetUrl( 'add-schema' ),
			'show' => $showSchemas,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'add-schema' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="fas fa-plus"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Ads Array
#
#####################################################
$adsArrayFilter = array( 'ads', 'ad-settings' );

$adsArray = array
(
	'title' => $L['ads'],
	'href' => '#',
	'show' => ( ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-ads' ) ) && $this->siteIsSelfHosted ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $adsArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-money-bill"></i>',
	'child' => array(
	
		'manage-ads' => array
		(
			'title' => $L['manage-ads'],
			'href' => $this->GetUrl( 'ads' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'ads' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-calendar-multiple"></i>',
			'child' => array()
		),
		
		'ad-settings' => array
		(
			'title' => $L['ad-settings'],
			'href' => $this->GetUrl( 'ad-settings' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'ad-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-screwdriver"></i>',
			'child' => array()
		)
	)
);

#####################################################
#
# Maintenance Array
#
#####################################################
$maintenanceArrayFilter = array( 'scheduled-tasks', 'maintenance', 'maintenance-settings', 'logs', 'task-log' );

$maintenanceArray = array
(
	'title' => $L['maintenance'],
	'href' => '#',
	'show' => ( IsAllowedTo( 'admin-site' ) ? true : false ),
	'collapsed' => ( ( $this->currentAction && in_array( $this->currentAction, $maintenanceArrayFilter ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-list"></i>',
	'child' => array(
	
		'maintenance-settings' => array
		(
			'title' => $L['settings'],
			'href' => $this->GetUrl( 'maintenance-settings' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'maintenance-settings' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '',
			'child' => array()
		),
		
		'maintenance' => array
		(
			'title' => $L['maintenance'],
			'href' => $this->GetUrl( 'maintenance' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'maintenance' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '',
			'child' => array()
		),
		
		'logs' => array
		(
			'title' => $L['system-log'],
			'href' => $this->GetUrl( 'logs' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'logs' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-bell"></i>',
			'child' => array()
		),
		
		'scheduled-tasks' => array
		(
			'title' => $L['scheduled-tasks'],
			'href' => $this->GetUrl( 'scheduled-tasks' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'scheduled-tasks' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '<i class="mdi mdi-calendar-multiple"></i>',
			'child' => array()
		),
		
		'task-log' => array
		(
			'title' => $L['task-log'],
			'href' => $this->GetUrl( 'task-log' ),
			'show' => true,
			'newtab' => false,
			'current' => ( ( $this->currentAction && ( $this->currentAction == 'task-log' ) ) ? true : false ),
			'class' => 'nav-link',
			'icon' => '',
			'child' => array()
		)
	)
);

#####################################################
#
# Stats Array
#
#####################################################
$statsArrayFilter = array( 'stats' );

$statsArray = array
(
	'title' => $L['stats'],
	'href' => $this->GetUrl( 'stats' ),
	'show' => ( ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'view-stats' ) ) && $this->adminSettings::IsTrue( 'enable_stats' ) && $this->siteIsSelfHosted ) ? true : false ),
	'collapsed' => false,
	'current' => ( ( $this->currentAction && ( $this->currentAction == 'stats' ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-chart-area"></i>',
	'child' => null
);

#####################################################
#
# Comments Array
#
#####################################################
$commentsArrayFilter = array( 'comments' );

$commentsArray = array
(
	'title' => $L['comments'],
	'href' => $this->GetUrl( 'comments' ),
	'show' => ( ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-comments' ) || IsAllowedTo( 'manage-own-comments' ) ) ? true : false ),
	'collapsed' => false,
	'current' => ( ( $this->currentAction && ( $this->currentAction == 'comments' ) ) ? true : false ),
	'newtab' => false,
	'class' => 'nav-link',
	'icon' => '<i class="nav-icon fas fa-comments"></i>',
	'child' => null
);