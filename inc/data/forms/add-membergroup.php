<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Add Membergroup Form
#
#####################################################
$L = $this->lang;

require ( ARRAYS_ROOT . 'generic-arrays.php');
/*
$permHtml = '
<div class="form-group">
	<label for="inputDescription">' . $L['permissions'] . '</label>';
	
	foreach ( $groupPermissions as $id => $per )
	{
		$permHtml .= '
		<div class="form-check form-switch">
			<input class="form-check-input" type="checkbox" name="permissions[' . $per['name'] . ']" id="Permission-' . $id . '">
			<label class="form-check-label" for="Permission-' . $id . '">' . $per['title']  . '</label> ';
			
		$permHtml .= ( $per['tip'] ? '<a href="#" type="button" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-html="true" title="' . $per['tip'] . '"><i class="me-2 mdi mdi-information"></i></a>' : '' );
		
		$permHtml .= '</div>';
	}
	
$permHtml .= '</div>';
*/

$form = array
(
	'add-membergroup' => array
	(
		'title' => $L['add-membergroup'],
		'data' => array(
		
			'membergroup-details' => array( 
				'title' => null, 'data' => array
				(
					'title'=>array('label'=>$L['title'], 'name' => 'name', 'type'=>'text', 'value'=>( ( isset( $_POST ) && isset( $_POST['name'] ) ) ? Sanitize( $_POST['name'], false ) : null ), 'required'=>true, 'tip'=>null ),
					
					'description'=>array( 'label'=>$L['description'], 'type'=>'textarea', 'name' => 'description', 'value' =>null, 'tip'=>null ),
					
					'max-personal-messages'=>array( 'label'=>$L['max-personal-messages'], 'type'=>'num', 'name' => 'max_messages', 'value' => 0, 'tip'=>$L['max-personal-messages-tip'], 'step' => 'any', 'min' => '0', 'max'=>'10000' ),
					
					'required-posts'=>array( 'label'=>$L['required-posts'], 'type'=>'num', 'name' => 'min_posts', 'value' => 0, 'tip'=>$L['required-posts-tip'], 'step' => 'any', 'min' => '-1', 'max'=>'10000' ),
					
					//'permissions'=>array('label'=>null, 'name' => 'permissions', 'type'=>'custom-html', 'value'=>$permHtml, 'tip'=>null, 'disabled' => false )
				)
			)
		)
	)
);