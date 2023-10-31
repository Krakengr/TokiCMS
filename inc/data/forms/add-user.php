<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Add User Form
#
#####################################################
$L = $this->lang;

$roleData = array();

$groups = AdminGroups( $this->GetSite(), false );

if ( !empty( $groups ) )
{
	foreach ( $groups as $group )
		$roleData[$group['id_group']] = array( 'name' => $group['id_group'], 'title'=>$group['group_name'], 'disabled' => false );
}

$form = array
(
	'add-user' => array
	(
		'title' => $L['add-user'],
		'data' => array(
		
			'generic-details' => array( 
				'title' => null, 'data' => array
				(
					'username'=>array('label'=>$L['username'], 'name' => 'username', 'type'=>'text', 'value'=>( ( isset( $_POST ) && isset( $_POST['username'] ) ) ? Sanitize( $_POST['username'], false ) : null ), 'required'=>true, 'tip'=>null ),
					
					'user-role'=>array('label'=>$L['role'], 'name' => 'role', 'type'=>'select', 'value'=>null, 'tip'=>null, 'firstNull' => false, 'data' => $roleData ),
					
					'password'=>array( 'label'=>$L['password'], 'type'=>'password', 'name' => 'password', 'value' => null, 'tip'=>$L['new-password-tip'], 'placeholder' => null, 'required'=>true ),
					
					'confirm-password'=>array( 'label'=>$L['confirm-password'], 'type'=>'password', 'name' => 'password2', 'value' => null, 'tip'=>null, 'placeholder' => null, 'required'=>true ),

					'email'=>array( 'label'=>$L['email'], 'type'=>'text', 'name' => 'email', 'value' => ( ( isset( $_POST ) && isset( $_POST['email'] ) ) ? Sanitize( $_POST['email'], false ) : null ), 'tip'=>null, 'placeholder' => null, 'required'=>true ),
				)
			),
		)
	)
);