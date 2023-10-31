<?php defined('TOKICMS') or die('Hacking attempt...');

//Site basic Ajax functions

#####################################################
#
# Get Posts Function
#
#####################################################
function AjaxGetPosts()
{
	header( 'Content-Type: application/json; charset=UTF-8' );
	
	$navPage = ( ( isset( $_POST['page'] ) && is_numeric( $_POST['page'] ) && ( $_POST['page'] > 0 ) ) ? (int) $_POST['page'] : 1 );
	
	$blogId = ( ( isset( $_POST['blog'] ) && is_numeric( $_POST['blog'] ) && ( $_POST['blog'] > 0 ) ) ? (int) $_POST['blog'] : 0 );
	
	$langId = ( ( isset( $_POST['lang'] ) && is_numeric( $_POST['lang'] ) && ( $_POST['lang'] > 0 ) ) ? (int) $_POST['lang'] : Settings::Lang()['id'] );
	
	$from = ( ( $navPage * HOMEPAGE_ITEMS ) - HOMEPAGE_ITEMS );
}

#####################################################
#
# Subscription Add Function
#
#####################################################
function SubMod()
{
	$UserId = UserId();
	
	header( 'Content-Type: application/json; charset=UTF-8' );

	if ( !isset( $_POST['postid'] ) || !isset( $_POST['token'] ) || !VerifySessionToken( 'submod', $_POST['token'] ) )
	{
		echo json_encode( array( 'error' => __( 'an-error-happened-refresh-page' ) ) );
		return;
	}
		
	if ( !isset( $UserId ) || empty( $UserId ) )
	{
		if ( !isset( $_POST['email'] ) || empty( $_POST['email'] ) || !Validate( $_POST['email'] ) )
		{
			echo json_encode( array( 'error' => __( 'error-please-enter-valid-email-address' ) ) );
			return;
		}
			
		$userLoggedIn = false;
	}
		
	else
		$userLoggedIn = true;
		
	$query = array(
			'SELECT'	=>  'id_relation',
			
			'FROM'		=> DB_PREFIX . "posts_subscriptions",
			
			'PARAMS' => array( 'NO_PREFIX' => true ),
			
			'WHERE' => "post_id = :post AND " . ( $userLoggedIn ? 'user_id = :user' : 'email = :email' ),
			
			'BINDS' 	=> array(
							array( 'PARAM' => ':post', 'VAR' => (int) $_POST['postid'], 'FLAG' => 'INT' )
			)
		
	);
		
	if ( $userLoggedIn )
		$query['BINDS'][] = array( 'PARAM' => ':user', 'VAR' => $UserId, 'FLAG' => 'INT' );
		
	else
		$query['BINDS'][] = array( 'PARAM' => ':email', 'VAR' => $_POST['email'], 'FLAG' => 'STR' );
		
	$fav = Query( $query );

	//If the data is found, delete it
	if ( $fav )
	{
		$query = array(
					'DELETE' => DB_PREFIX . "posts_subscriptions",
					'WHERE'	=>  "post_id = :post AND " . ( $userLoggedIn ? 'user_id = :user' : 'email = :email' ),
					'PARAMS' => array( 'NO_PREFIX' => true ),
					'BINDS' 	=> array(
								array( 'PARAM' => ':post', 'VAR' => (int) $_POST['postid'], 'FLAG' => 'INT' )
					)
		);
			
		if ( $userLoggedIn )
			$query['BINDS'][] = array( 'PARAM' => ':user', 'VAR' => $UserId, 'FLAG' => 'INT' );
		
		else
			$query['BINDS'][] = array( 'PARAM' => ':email', 'VAR' => $_POST['email'], 'FLAG' => 'STR' );

		Query( $query, false, false, true );
		
		$query = array(
				'SELECT'	=>  "COUNT(*)",
				
				'FROM'		=> DB_PREFIX . 'posts_subscriptions',
				
				'WHERE'	=>  "post_id = :post",
			
				'PARAMS' => array( 'NO_PREFIX' => true ),
		
				'BINDS' 	=> array(
					array( 'PARAM' => ':post', 'VAR' => (int) $_POST['postid'], 'FLAG' => 'INT' )
				)
		);
			
		$count = Query( $query, false, false, false, true );

		echo json_encode( array( 'removed' => __( 'post-successfully-removed' ), 'count' => $count ) );
	}
		
	//Insert a new key
	else
	{
		$query = array(
				'INSERT'	=> "post_id, user_id, email, ip",

				'VALUES' 	=> ":post, :user, :email, :ip",

				'INTO'		=> DB_PREFIX . "posts_subscriptions",

				'PARAMS' => array( 'NO_PREFIX' => true ),

				'BINDS' => array(
							array( 'PARAM' => ':post', 'VAR' => (int) $_POST['postid'], 'FLAG' => 'INT' ),
							array( 'PARAM' => ':user', 'VAR' => ( $userLoggedIn ? $UserId : 0 ), 'FLAG' => 'INT' ),
							array( 'PARAM' => ':email', 'VAR' => ( $userLoggedIn ? '' : $_POST['email'] ), 'FLAG' => 'STR' ),
							array( 'PARAM' => ':ip', 'VAR' => ( $userLoggedIn ? '' : GetRealIp() ), 'FLAG' => 'STR' )
				)
		);

		Query( $query, false, false, true );
		
		$query = array(
				'SELECT'	=>  "COUNT(*)",
				
				'FROM'		=> DB_PREFIX . 'posts_subscriptions',
				
				'WHERE'	=>  "post_id = :post",
			
				'PARAMS' => array( 'NO_PREFIX' => true ),
		
				'BINDS' 	=> array(
					array( 'PARAM' => ':post', 'VAR' => (int) $_POST['postid'], 'FLAG' => 'INT' )
				)
		);
			
		$count = Query( $query, false, false, false, true );

		echo json_encode( array( 'added' => __( 'post-successfully-added' ), 'count' => $count ) );
	}
}
	
#####################################################
#
# Add to favorites function
#
#####################################################
function FavMod()
{
	$UserId = UserId();
	
	header( 'Content-Type: application/json; charset=UTF-8' );
		
	if ( !isset( $_POST['postid'] ) || !isset( $_POST['token'] ) || !VerifySessionToken( 'favmod', $_POST['token'] ) )
	{
		echo json_encode( array( 'error' => __( 'an-error-happened-refresh-page' ) ) );
		return;
	}
		
	//Add to favorites only works for logged in members
	if ( !isset( $UserId ) || empty( $UserId ) )
	{
		echo json_encode( array( 'error' => __( 'members-only-restricted-item-warning' ) ) );
		return;
	}

	$query = array(
		'SELECT'	=>  'id_relation',
			
		'FROM'		=> DB_PREFIX . "posts_favorites",
			
		'PARAMS' => array( 'NO_PREFIX' => true ),
			
		'WHERE' => "post_id = :post AND user_id = :user",
			
		'BINDS' 	=> array(
						array( 'PARAM' => ':post', 'VAR' => (int) $_POST['postid'], 'FLAG' => 'INT' ),
						array( 'PARAM' => ':user', 'VAR' => $UserId, 'FLAG' => 'INT' )
		)
		
	);
		
	$fav = Query( $query );

	//If the key exists, remove it
	if ( $fav )
	{
		$query = array(
				'DELETE' => DB_PREFIX . "posts_favorites",
				'WHERE'	=>  "post_id = :post AND user_id = :user",
				'PARAMS' => array( 'NO_PREFIX' => true ),
				'BINDS' 	=> array(
							array( 'PARAM' => ':post', 'VAR' => (int) $_POST['postid'], 'FLAG' => 'INT' ),
							array( 'PARAM' => ':user', 'VAR' => $UserId, 'FLAG' => 'INT' )
				)
		);

		Query( $query, false, false, true );
			
		echo json_encode( array( 'success' => __( 'post-successfully-removed' ) ) );
	}
		
	//Insert a new key
	else
	{
		$query = array(
					'INSERT'	=> "post_id, user_id",

					'VALUES' 	=> ":post, :user",

					'INTO'		=> DB_PREFIX . "posts_favorites",

					'PARAMS' => array( 'NO_PREFIX' => true ),

					'BINDS' => array(
							array( 'PARAM' => ':post', 'VAR' => (int) $_POST['postid'], 'FLAG' => 'INT' ),
							array( 'PARAM' => ':user', 'VAR' => $UserId, 'FLAG' => 'INT' )
					)
		);
	
		Query( $query, false, false, true );
			
		echo json_encode( array( 'success' => __( 'post-successfully-added' ) ) );
	}
}