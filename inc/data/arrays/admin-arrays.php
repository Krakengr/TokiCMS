<?php defined('TOKICMS') or die('Hacking attempt...');

$leftData = array(
		'stats' => array( 'function' => 'AdminStats', 'title' => __( 'stats' ) ),
		'at-glance' => array( 'function' => 'AtAGlanse', 'title' => __( 'at-a-glance' ) ),
		'top-posts' => array( 'function' => 'TopDashboardPosts', 'title' => __( 'top-posts' ) ),
		'latest-posts' => array( 'function' => 'LatestDashboardPosts', 'title' => __( 'latest-posts' ) ),
		'latest-news-and-releases' => array( 'function' => 'LatestNewsUpdates', 'title' => __( 'latest-news-and-releases' ) )
);
		
$rightData = array(
		'quick-draft' => array( 'function' => 'CreatePostDashboard', 'title' => __( 'quick-draft' ) ),
		'latest-comments' => array( 'function' => 'LatestDashboardComments', 'title' => __( 'latest-comments' ) ),
		'latest-logs' => array( 'function' => 'LatestDashboardLogs', 'title' => __( 'logs' ) )
);
