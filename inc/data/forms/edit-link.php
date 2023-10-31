<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Edit Link Form
#
#####################################################
global $Link;

//Make sure that we have this key in the DB
$Link = $this->db->from( 
null, 
"SELECT *
FROM `" . DB_PREFIX . "links`
WHERE (id = " . (int) Router::GetVariable( 'key' ) . ") AND (id_site = " . $this->GetSite() . ")"
)->single();

include ( ARRAYS_ROOT . 'generic-arrays.php');

$addRelLinks = array();

$linksRedirection = array(
	'default' => array( 'name' => 'default', 'title'=> $L['default'], 'disabled' => false, 'data' => array() ),
	'direct' => array( 'name' => 'direct', 'title'=> $L['direct'], 'disabled' => false, 'data' => array() )
);

foreach ( $linksRedirectionArray as $key => $redir )
{
	$linksRedirection[$key] = array( 'name' => $redir['name'], 'title'=> $redir['title'], 'disabled' => false, 'data' => array() );
}

$addRelLinks['noopener'] = array( 'name' => 'noopener', 'title'=> $L['add-noopener'], 'disabled' => false, 'data' => array() );
$addRelLinks['noreferrer'] = array( 'name' => 'noreferrer', 'title'=> $L['add-noreferrer'], 'disabled' => false, 'data' => array() );
$addRelLinks['external'] = array( 'name' => 'external', 'title'=> $L['add-external'], 'disabled' => false, 'data' => array() );
$addRelLinks['ugc'] = array( 'name' => 'ugc', 'title'=> $L['add-ugc'], 'disabled' => false, 'data' => array() );

$LinkData = Json( $Link['link_data'] );

$form = array
(
	'edit-link' => array
	(
		'title' => __( 'edit-link' ),
		'col' => 8,
		'data' => array(
		
			'generic' => array( 
				'title' => null, 'data' => array
				(
					'link-title'=>array('label'=>__( 'name' ), 'type'=>'text', 'name' => 'title', 'value' =>$Link['title'], 'tip'=>__( 'category-name-tip' ), 'required' => true ),
					
					'url'=>array('label'=>__( 'target-url' ), 'type'=>'text', 'name' => 'url', 'value' =>$Link['url'], 'tip'=> null, 'required' => true ), 
					
					'description'=>array('label'=>__( 'description' ), 'type'=>'textarea', 'name' => 'description', 'value' =>$Link['descr'], 'tip'=>__( 'add-new-link-descr-tip' ) ), 
					'no-follow'=>array('label'=>__( 'no-follow' ), 'type'=>'checkbox', 'name' => 'no-follow', 'value' => $LinkData['no_follow'], 'tip'=>__( 'add-new-link-nofollow-tip' ) ),
					
					'sponsored'=>array('label'=>__( 'sponsored' ), 'type'=>'checkbox', 'name' => 'sponsored', 'value' => $LinkData['sponsored'], 'tip'=>__( 'add-new-link-sponsored-tip' ) ),
					
					'also-add-to-rel-attribute'=>array('label'=>$L['also-add-to-rel-attribute'], 'name' => 'add_rel[]', 'type'=>'select', 'value'=>( isset( $LinkData['add_rel'] ) ? $LinkData['add_rel'] : null ), 'tip'=>$L['press-ctrl-and-click-to-deselect-a-value'], 'firstNull' => false, 'data' => $addRelLinks, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3', 'multiple' => true ),
					
					'redirection'=>array(
						'label'=>$L['redirection'], 'name' => 'redirection', 'type'=>'select', 'value'=>$LinkData['redirection'], 'tip'=>__( 'add-new-link-redirection-tip' ), 'firstNull' => false, 'data' => $linksRedirection, 'id' => '', 'class' => 'form-control form-select shadow-none mt-3'
					),
					
					'hr-2'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'disable'=>array('label'=>__( 'disable-link' ), 'type'=>'checkbox', 'name' => 'disable-link', 'value' => ( ( $Link['status'] == 'inactive' ) ? true : false ), 'tip'=>__( 'disable-link-tip' ) ),
				)
			)
		)
	)
);