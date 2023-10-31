<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Add Redirection Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

include ( ARRAYS_ROOT . 'generic-arrays.php');

$form = array
(
	'add-redirection' => array
	(
		'title' => $L['add-redirection'],
		'data' => array(
		
			'generic-details' => array( 
				'title' => null, 'data' => array
				(
					'title'=>array('label'=>$L['title'], 'name' => 'redir[title]', 'type'=>'text', 'value'=>null, 'tip'=>$L['redirect-title-tip'] ),
					
					'source-url'=>array( 'label'=>$L['source-url'], 'type'=>'text', 'name' => 'redir[source-url]', 'value' => null, 'tip'=>$L['source-url-tip'], 'placeholder' => '/my-post/' , 'required'=>true ),
					
					'target-url'=>array( 'label'=>$L['target-url'], 'type'=>'text', 'name' => 'redir[target-url]', 'value' => null, 'tip'=>$L['target-url-tip'], 'placeholder' => 'http://www.mynewsite.com/my-post/', 'required'=>true ),
					
					'when-matched'=>array('label'=>$L['when-matched'], 'name' => 'redir[when-matched]', 'type'=>'select', 'value'=>null, 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $redirMatchedOptions ),
					
					'add-http-code'=>array('label'=>$L['add-http-code'], 'name' => 'redir[add-http-code]', 'type'=>'select', 'value'=>null, 'tip'=>null, 'firstNull' => false, 'disabled' => false, 'data' => $redirHttpOptions ),
					
					'exclude-from-logs'=>array('label'=>$L['exclude-from-logs'], 'name' => 'redir[exclude-from-logs]', 'type'=>'checkbox', 'value'=>false, 'tip'=>null ),
				)
			),
		)
	)
);