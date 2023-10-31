<?php defined('TOKICMS') or die('Hacking attempt...');

global $dbh;

//Include this file, its function is still being used by a few controllers
require_once ( FUNCTIONS_ROOT 	. 'deprecated-db-functions.php' );
#####################################################
#
# Returns the default post query function
#
#####################################################
function PostDefaultQuery( $q = null, $siteId = null )
{
	return "SELECT p.*, c.name AS cat_name, c.sef AS cat_sef, c.id AS cat_id, c.cat_color, c.groups_data as cat_groups, su.name AS sub_name, su.sef AS sub_sef, su.id AS sub_id, su.cat_color AS sub_color, su.groups_data as sub_groups, b.sef AS blog_sef, b.name AS blog_name, b.trans_data AS blog_trans, b.groups_data AS blog_groups, u.real_name as real_name, u.user_name, u.image_data as user_img, u.trans_data, la.code AS ls, la.title AS lt, la.locale AS ll, la.flagicon, d.value1 as extra_val, d.ext_id, d.external_url as ext_url, d.last_time_commented as lstc, d.hide_on_home as hide_home, d.clone_id, d.keep_date, ld.id as dlid, ld.code as dlc, ld.title as dlt, ld.locale as dll, lc.date_format, lc.time_format, (SELECT COUNT(id) FROM `" . DB_PREFIX . "comments` as cm WHERE cm.id_post = p.id_post AND cm.status = 'approved') as numcomm, t.title as ctl, t.sef as cts, t.trans_data as ctt, t.description as ctd, pa.sef as parent_sef, pa.title as parent_title
	FROM `" . DB_PREFIX . POSTS . "` AS p
	INNER JOIN `" . DB_PREFIX . "languages`  as la ON la.id = p.id_lang
	INNER JOIN `" . DB_PREFIX . "languages_config` as lc ON lc.id_lang = p.id_lang
	INNER JOIN `" . DB_PREFIX . USERS . "`   as u ON u.id_member = p.id_member
	INNER JOIN `" . DB_PREFIX . "languages`  as ld ON ld.id_site = p.id_site AND ld.is_default = 1
	LEFT JOIN  `" . DB_PREFIX . "categories` as c ON c.id = p.id_category
	LEFT JOIN  `" . DB_PREFIX . "categories` as su ON su.id = p.id_sub_category
	LEFT JOIN  `" . DB_PREFIX . "blogs` 	 as b ON b.id_blog = p.id_blog
	LEFT JOIN  `" . DB_PREFIX . "posts_data` as d ON d.id_post = p.id_post
	LEFT JOIN  `" . DB_PREFIX . "post_types` as t ON t.id = p.id_custom_type
	LEFT JOIN `" . DB_PREFIX . POSTS . "`    as pa ON pa.id_post = p.id_page_parent
	WHERE " . ( $siteId ? "(p.id_site = " . $siteId . ") AND " : "" ) . ( $q ? $q : "(p.post_type = 'post' OR p.post_type = 'page') AND (p.post_status = 'published')" );
}

#####################################################
#
# Returns the default posts query function
#
#####################################################
function PostsDefaultQuery( $q, $limit = 1, $order = null, $group = null, $fullQuery = true, $xtra = null )
{
	$query = "SELECT p.id_post, p.id_blog, p.id_site, p.id_parent, p.id_page_parent, p.added_time, p.id_member, p.id_lang, p.title, COALESCE(p.description, SUBSTRING(p.post, 1, 180)) AS description, p.disable_comments, p.sef, p.views, p.num_comments, p.post_type, p.post_status, p.cover_img, p.post, p.content, c.name AS cat_name, c.sef AS cat_sef, c.id AS cat_id, c.cat_color, su.name AS sub_name, su.sef AS sub_sef, su.id AS sub_id, su.cat_color AS sub_color, u.real_name as real_name, u.user_name, u.image_data as user_img, b.sef AS blog_sef, b.name AS blog_name, b.trans_data AS blog_trans, b.groups_data AS blog_groups, u.trans_data, la.code AS ls, la.title AS lt, la.locale AS ll, la.flagicon, d.ext_id, d.external_url as ext_url, d.value1 as extra_val, pa.sef as parent_sef, pa.title as parent_title, ";
	
	if ( $fullQuery )
	{
		$query .= "s.url, s.enable_multilang as multilang, s.enable_multiblog as multiblog, s.enable_multisite as multisite, s.title as st, cnf.value as hide_lang, cnf2.value as enable_comments, cnf3.value as disable_author_archives, cnf4.value as comments_data, ";
	}
	
	$query .= "d.last_time_commented as lstc, ld.id as dlid, ld.code as dlc, ld.title as dlt, ld.locale as dll, lc.date_format, lc.time_format, (SELECT COUNT(id) FROM `" . DB_PREFIX . "comments` as cm WHERE cm.id_post = p.id_post AND cm.status = 'approved') as numcomm
	FROM `" . DB_PREFIX . POSTS . "` AS p
	INNER JOIN `" . DB_PREFIX . "languages`  		as la ON la.id = p.id_lang
	INNER JOIN `" . DB_PREFIX . "languages_config` 	as lc ON lc.id_lang = p.id_lang
	INNER JOIN `" . DB_PREFIX . USERS . "`   		as u ON u.id_member = p.id_member";
	
	if ( $fullQuery )
	{
		$query .= "
		INNER JOIN `" . DB_PREFIX . "sites`  as s ON s.id = p.id_site
		INNER JOIN `" . DB_PREFIX . "config` as cnf ON cnf.id_site 	 = p.id_site AND cnf.variable  = 'hide_default_lang_slug'
		INNER JOIN `" . DB_PREFIX . "config` as cnf2 ON cnf2.id_site = p.id_site AND cnf2.variable = 'enable_comments'
		INNER JOIN `" . DB_PREFIX . "config` as cnf3 ON cnf3.id_site = p.id_site AND cnf3.variable = 'disable_author_archives'
		INNER JOIN `" . DB_PREFIX . "config` as cnf4 ON cnf4.id_site = p.id_site AND cnf4.variable = 'comments_data'";
	}
	
	if ( $xtra )
	{
		$query .= "
		" . $xtra . "
		";
	}
	
	$query .= "
	INNER JOIN 	`" . DB_PREFIX . "languages`  	as ld ON ld.id_site = p.id_site AND ld.is_default = 1
	LEFT JOIN   `" . DB_PREFIX . "categories` 	as c ON c.id = p.id_category
	LEFT JOIN  	`" . DB_PREFIX . "categories` 	as su ON su.id = p.id_sub_category
	LEFT JOIN  	`" . DB_PREFIX . "blogs` 	 	as b ON b.id_blog = p.id_blog
	LEFT JOIN  	`" . DB_PREFIX . "posts_data` 	as d ON d.id_post = p.id_post
	LEFT JOIN   `" . DB_PREFIX . POSTS . "`     as pa ON pa.id_post = p.id_page_parent
	WHERE 1=1 AND " . $q . ( $group ? " GROUP BY " . $group : "" ) . ( $order ? " ORDER BY " . $order : "" ) . ( $limit ? " LIMIT " . $limit : "" );
	
	return $query;
}

//Set if the DB is already connected otherwise set a new connection
function db() 
{
	global $db;

	//Check if we have an active DB connenction
	if ( !$db )
	{
		$db = new Database();
	}

	return $db;
}

function dbLoad()
{
	global $dbh;
	
	if ( !is_null ( $dbh ) )
	{
		return $dbh;
	}
	
	try
	{
		if ( !defined('SERVER') || !defined('DATABASE') || !defined( 'DBUSERNAME' ) )
		{
            die();
        }
		
		$dbh = new PDO
		(
			"mysql:host=" . SERVER . ";dbname=" . DATABASE . ";charset=utf8mb4",
			DBUSERNAME,
			DBPASSWORD
		);

		$dbh->query('SET CHARACTER SET utf8mb4');
		$dbh->query('SET NAMES utf8mb4');
		$dbh->query('SET sql_mode=""');
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);			
	} catch (PDOException $e) {
		die("Could not establish a connection to DB.");
	}
	
	return $dbh;
}