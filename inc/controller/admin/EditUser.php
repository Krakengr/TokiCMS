<?php defined('TOKICMS') or die('Hacking attempt...');

class EditUser extends Controller {
	
	public function process() 
	{
		$this->setVariable( 'Lang', $this->lang );

		$this->Run();
		
		Theme::Build();

		$this->view();
	}
	
	private function Run() 
	{
		global $Admin;

		if ( !IsAllowedTo( 'admin-site' ) && !IsAllowedTo( 'manage-members' ) )
		{
			Router::SetNotFound();
			return;
		}
		
		$Auth = $this->getVariable( 'AuthUser' );

		$id = (int) Router::GetVariable( 'key' );
		
		$User = AdminGetSingleUser( $id );

		if ( !$User )
			Redirect( $Admin->GetUrl( 'users' ) );
		
		Theme::SetVariable( 'headerTitle', __( 'edit-user' ) . ': "' . $User['user_name'] . '" | ' . $Admin->SiteName() );
		
		$this->setVariable( 'User', $User );

		//Don't continue if there is no POST
		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
			return;

		// Verify that the token is correct
		if ( !verify_token( 'edit_user_' . $id ) )
			Redirect( $Admin->GetUrl( 'users' ) );
		
		//If the user is an Admin, make sure that is the same person who's make changes
		if ( ( $User['id_group'] == '1' ) && ( $User['id_member'] == $Auth['id_member'] ) )
		{
			//Check it the password is correct
			$userPass = sha1( $_POST['currentPassword'] . $User['password_hash'] );
		
			if ( !hash_equals( $User['passwd'], $userPass ) )
			{
				$Admin->SetAdminMessage( __( 'the-following-errors-occurred-when-trying-to-save-your-profile' ) . '<br />' . __( 'wrong-password-tip' ) );
				return;
			}
		}
		
		$imageArr = array();
		
		//If this field is empty, means that the user has deleted its profile image
		if ( !isset( $_POST['ProfileImageFile'] ) || empty( $_POST['ProfileImageFile'] ) )
		{
			$imageUrl = '';
			
			$this->db->update( 'images' )->where( 'id_member', $id )->where( 'img_type', 'user' )->where( 'img_status', 'full' )->set( 'id_member', 0 );
		}

		else
		{
			$imageUrl = Sanitize( $_POST['ProfileImageFile'], false );

			//If this is a child site, let's check if we want to share images
			if ( !$Admin->IsDefaultSite() )
			{
				$_site = $Admin->Settings()::Site();
			
				$share = ( ( isset( $_site['share_data'] ) && !empty( $_site['share_data'] ) ) ? Json( $_site['share_data'] ) : array() );
			
				if ( isset( $share['sync_uploads'] ) && $share['sync_uploads'] )
				{
					$tmp = $this->db->from( 
					null, 
					"SELECT added_time
					FROM `" . DB_PREFIX . "images`
					WHERE (id_member = " . $id . ") AND (img_type = 'user') AND (img_status = 'full')"
					)->single();
					
					if ( $tmp )
					{
						$pingUrl = ( !empty( $_site['site_ping_url'] ) ? $_site['site_ping_url'] : $_site['url'] . $_site['ping_slash'] . PS );
						
						$pingUrl .= '?token=' . $_site['site_secret'] . '&action=sync&type=image&url=' . urlencode( $imageUrl ) . '&time=' . $tmp['added_time'];
						
						PingSite( $pingUrl );
					}
				}
			}
		}
		
		$imageArr 		= AdminGetUserImages( $id, $Admin->GetSite() );
		$trans 			= ( isset( $_POST['trans'] ) && !empty( $_POST['trans'] ) ? $_POST['trans'] : array() );
		$socialData 	= ( !empty( $User['social_data'] ) ? Json( $User['social_data'] ) : array() );
		$name 			= ( !isset( $_POST['nickname'] ) ? $User['real_name'] : Sanitize( $_POST['nickname'], false ) );
		$bio 			= ( !isset( $_POST['user_bio'] ) ? $User['user_bio'] : Sanitize( $_POST['user_bio'], false ) );
		$email 			= ( Validate( $_POST['email'] ) ? Sanitize( $_POST['email'], false ) : $User['email_address'] );
		$passwordHash 	= $User['password_hash'];
		$passwd 		= $User['passwd'];
		$changedPass 	= false;
		
		if ( isset( $_POST['social'] ) && !empty( $_POST['social'] ) )
		{
			$socialKey 		= array_keys( $_POST['social'] );
			$socialKey 		= ( isset( $socialKey['0'] ) ? $socialKey['0'] : $socialKey );
			$socialValues 	= array_values( $_POST['social'] );
			$socialValues 	= ( isset( $socialValues['0'] ) ? $socialValues['0'] : $socialValues );
		}
		else
			$socialKey = null;
		
		if ( $socialKey )
			$socialData[$socialKey] = $socialValues;
		
		//You can't disable your own status
		if ( $User['id_member'] == $Auth['id_member'] )
			$enabled = 1;
		
		//Only an admin can change other admin(s) status
		elseif ( ( $User['id_group'] == '1' ) && ( $Auth['id_group'] == '1' ) )
			$enabled = ( ( isset( $_POST['status'] ) && ( $_POST['status'] == 'enabled' ) ) ? 1 : 0 );
		
		//Even if you can edit users, you can't change the status of admin(s)
		elseif ( ( $User['id_group'] != '1' ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-members' ) ) )
			$enabled = ( ( isset( $_POST['status'] ) && ( $_POST['status'] == 'enabled' ) ) ? 1 : 0 );
		
		else
			$enabled = 1;
		
		//You can't change your own group
		if ( $User['id_member'] == $Auth['id_member'] )
			$group = $Auth['id_group'];
		
		//Only an admin can change other admin(s) group
		elseif ( ( $User['id_group'] == '1' ) && ( $Auth['id_group'] == '1' ) )
			$group = (int) $_POST['group'];
		
		//Even if you can edit users, you can't change the status of admin(s)
		elseif ( ( $User['id_group'] != '1' ) && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-members' ) ) )
			$group = (int) $_POST['group'];
		
		else
			$group = $User['id_group'];

		//Do we want to change the password?
		if ( !empty( $_POST['newPass'] ) && !empty( $_POST['newPass2'] ) && hash_equals( $_POST['newPass'], $_POST['newPass2'] ) )
		{
			$passwordHash = GenerateRandomKey( 8 );
			$password = Sanitize( $_POST['newPass'], false );
			$passwd = sha1( $password . $passwordHash );
			$changedPass = true;
		}
		
		$dbarr = array(
			"real_name" 	=> $name,
			"user_bio" 		=> $bio,
			"email_address" => $email,
			"passwd" 		=> $passwd,
			"password_hash" => $passwordHash,
			"is_activated" 	=> $enabled,
			"image_data" 	=> json_encode( $imageArr, 		JSON_UNESCAPED_UNICODE ),
			"trans_data" 	=> json_encode( $trans, 		JSON_UNESCAPED_UNICODE ),
			"social_data" 	=> json_encode( $socialData, 	JSON_UNESCAPED_UNICODE ),
			"id_group" 		=> $group
		);
		
		$q = $this->db->update( USERS )->where( 'id_member', $id )->set( $dbarr );

		//If the user has changed his OWN password, log him out
		if ( $q && $changedPass && ( $User['id_member'] == $Auth['id_member'] ) )
		{
			@header('Location: ' . SITE_URL . 'logout' . PS );
			exit;
		}
		
		//Redirect to the same page
		@header('Location: ' . $Admin->GetUrl( 'edit-user' . PS . 'id' . PS . $id ) );
		exit;
	}
}