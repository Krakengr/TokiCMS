<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Add Lang Form
#
#####################################################
global $Admin;

$L = $this->lang;

$settings = $this->adminSettings::Get();

require ( ARRAYS_ROOT . 'generic-arrays.php');

$langsData = array();

$langKeys = Langs( $this->GetSite(), false, true );

if ( !empty( $langs ) )
{
	foreach ( $langs as $key => $lang )
	{
		//The lang code must be unique
		if ( !empty( $langKeys ) && in_array( $lang['code'], $langKeys ) )
			continue;
			
		$langsData[$key] = array( 'name' => $key, 'title'=>'<img src="' . SITE_URL . 'languages' . PS . 'flags' . PS . $lang['icon'] . '" class="img-flag" /> ' . $lang['name'] . ' - ' . $lang['locale'], 'disabled' => false,
			'data' => array( 'name' => 'data-flag', 'value' => SITE_URL . 'languages' . PS . 'flags' . PS . $key . '.png' ) );
	}
}

//Get the last order from the db
$order = $this->db->from( 
null, 
"SELECT lang_order
FROM `" . DB_PREFIX . "languages`
WHERE (id_site = " . $Admin->GetSite() . ")
ORDER BY lang_order DESC
LIMIT 1"
)->single();

$min = ( $order ? $order['lang_order'] + 1 : 1 );

$form = array
(
	'add-language' => array
	(
		'title' => $L['add-new-language'],
		'col' => 6,
		'data' => array(
		
			'new-language' => array( 
				'title' => null, 'data' => array
				(
					'new-language'=>array('label'=>$L['choose-language'], 'name' => 'new-lang', 'type'=>'select', 'value'=>null, 'tip'=>null,
					'firstNull' => true, 'data' => $langsData, 'class' => 'custom-select form-control-border' ),
				)
			),

			'text-direction' => array( 
				'title' => null, 'data' => array
				(
					'text-direction'=>array('label'=>$L['text-direction'], 'name' => 'text_direction', 'type'=>'radio', 'value'=>null, 'tip'=>$L['text-direction-tip'],
					'data' => array( 
							'left-to-right' => array( 'name' => 'text-direction', 'value' => 'ltr', 'title'=>$L['left-to-right'], 'disabled' => false, 'checked' => true ),
							'right-to-left' => array( 'name' => 'text-direction', 'value' => 'rtl', 'title'=>$L['right-to-left'], 'disabled' => false, 'checked' => false )
					) ),
				)
			),
			
			'order' => array( 
				'title' => null, 'data' => array
				(
					'order'=>array('label'=>$L['lang-order'], 'name' => 'order', 'type'=>'num', 'value'=> $min, 'tip'=>$L['lang-order-tip'], 'min'=> $min, 'max'=>'99')
				)
			),
			
			'translate' => array( 
				'title' => null, 'data' => array
				(
					'translate'=>array('label'=>$L['copy-translate-language'], 'type'=>'checkbox', 'name' => 'copy-translate', 'value' => null, 'tip'=>$L['copy-translate-language-tip'] ),
				)
			)

		)
	)
);
