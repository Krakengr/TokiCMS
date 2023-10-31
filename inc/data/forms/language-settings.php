<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Languages Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

include ( ARRAYS_ROOT . 'generic-arrays.php');

$localesData = array();

foreach ( $locales as $key => $locale )
	$localesData[$locale['key']] = array( 'name' => $locale['key'], 'title'=> $locale['value'], 'disabled' => false, 'data' => array() );

$direction = array( 'ltr' => array( 'name' => 'ltr', 'title'=> $L['left-to-right'] ), 'rtl' => array( 'name' => 'rtl', 'title'=> $L['right-to-left'] ) );

$form = array
(
	'language' => array
	(
		'title' => $L['language-and-timezone'],
		'data' => array(
		
			'language-settings' => array( 
				'title' => null, 'data' => array
				(
					'title'=>array( 'label'=>$L['title'], 'type'=>'text', 'name' => 'lang[title]', 'value' => $this->currentLang['lang']['title'], 'tip'=>$L['add-title-tip'] ),
					'locale'=>array( 'label'=>$L['locale'], 'type'=>'text', 'name' => 'lang[locale]', 'value' => $this->currentLang['lang']['locale'], 'tip'=>$L['with-the-locales-you-can-set-the-regional-user-interface'] ),
					'date-format'=>array( 'label'=>$L['date-format'], 'type'=>'text', 'name' => 'lang[date_format]', 'value' => $this->currentLang['settings']['date_format'], 'tip'=>$L['current-format'] . ': <strong>' . date( $this->currentLang['settings']['date_format'] , time() ) . '</strong>' ),
					'time-format'=>array( 'label'=>$L['time-format'], 'type'=>'text', 'name' => 'lang[time_format]', 'value' => $this->currentLang['settings']['time_format'], 'tip'=>$L['current-format'] . ': <strong>' . date( $this->currentLang['settings']['time_format'] , time() ) . '</strong>' ),
					'direction'=>array('label'=>$L['text-direction'], 'name' => 'lang[direction]', 'type'=>'select', 'value'=>$this->currentLang['lang']['direction'], 'tip'=>$L['text-direction-tip'], 'firstNull' => false, 'data' => $direction ),
					'timezone'=>array('label'=>$L['timezone'], 'name' => 'settings[timezone_set]', 'type'=>'select', 'value'=>$settings['timezone_set'], 'tip'=>$L['select-timezone-tip'], 'firstNull' => false, 'data' => $localesData ),
					'id'=>array( 'label' => null, 'name' => 'langID', 'type' => 'hidden', 'value' => $this->currentLang['lang']['id'] )
				)
			)
		)
	)
);