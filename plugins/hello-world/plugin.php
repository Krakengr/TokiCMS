<?php defined('TOKICMS') or die('Hacking attempt...');

global $L;

// Add the string into the language
$L['hello-world'] = 'Hello World';

function add_admin_route_function( $menu )
{
	$menu['hello-world'] = array(
		'controller' => 'Dashboard', 'file' => 'form.php', 'form' => null, 'dir' => 'hello-world', 'is_plugin' => true
	);
	
	return $menu;
}

//The menu array
function add_admin_menu( $menu )
{
	global $Admin;

	$menu['hello-world'] = array
	(
		'title' => __( 'hello-world' ),
		'href' => '#',
		'show' => true,
		'collapsed' => false,
		'newtab' => ( ( $Admin->CurrentAction() && ( $Admin->CurrentAction() == 'hello-world' ) ) ? true : false ),
		'class' => 'nav-link',
		'icon' => '<div class="sb-nav-link-icon"><i class="fab fa-fort-awesome"></i></div>',
		'child' => array(
			'hello-world' => array
			(
				'title' => __( 'hello-world' ),
				'href' => $Admin->GetUrl( 'hello-world' ),
				'show' => true,
				'newtab' => false,
				'current' => ( ( $Admin->CurrentAction() && ( $Admin->CurrentAction() == 'hello-world' ) ) ? true : false ),
				'class' => 'nav-link',
				'icon' => '<div class="sb-nav-link-icon"><i class="fab fa-fort-awesome"></i></div>',
				'child' => array()
			)
		)
	);
	
	return $menu;
}