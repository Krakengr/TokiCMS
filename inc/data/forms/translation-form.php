<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Translate Slugs Settings Form
#
#####################################################
$L = $this->lang;

$settings = $this->adminSettings::Get();

$trans = null;

if ( $this->adminSettings::IsTrue( 'translate_slugs' ) )
{
	$trans = Json( $this->adminSettings::Get()['trans_data'] );

	$trans = ( isset( $trans[$this->LangKey()] ) ? $trans[$this->LangKey()] : null );
}

$form = array
(
	'translation' => array
	(
		'title' => $L['url-slug-translation'],
		'hide' => ( $this->adminSettings::IsTrue( 'translate_slugs' ) ? false : true ),
		'data' => array(
	
			'slugs-filter' => array( 
				'title' => null, 'tip'=>sprintf( $L['lang-info-translation'], $this->LangName() ), 'data' => array
				(
					'post-filter-trans'=>array('label'=>$L['post-filter-translation'], 'name' => 'trans[post_filter_trans]', 'type'=>'text', 'value'=>( isset( $trans['post_filter_trans'] ) ? rawurldecode( $trans['post_filter_trans'] ) : null ), 'tip'=>sprintf( $L['filter-translation-tip'], $L['post'], $L['post'], strtolower( $L['post'] ) ), 'placeholder' => 'post' )
				)
			),
			
			'categories-filter' => array( 
				'title' => null, 'data' => array
				(
					'categories-filter-trans'=>array('label'=>$L['category-filter-translation'], 'name' => 'trans[category_filter_trans]', 'type'=>'text', 'value'=>( isset( $trans['category_filter_trans'] ) ? rawurldecode( $trans['category_filter_trans'] ) : null ), 'tip'=>sprintf( $L['filter-translation-tip'], $L['category'], $L['category'], strtolower( $L['category'] ) ), 'placeholder' => 'category' )
				)
			),
			
			'tags-filter' => array( 
				'title' => null, 'data' => array
				(
					'tags-filter-trans'=>array('label'=>$L['tag-filter-translation'], 'name' => 'trans[tags_filter_trans]', 'type'=>'text', 'value'=>( isset( $trans['tags_filter_trans'] ) ? rawurldecode( $trans['tags_filter_trans'] ) : null ), 'tip'=>sprintf( $L['filter-translation-tip'], $L['tag'], $L['tag'], strtolower( $L['tag'] ) ), 'placeholder' => 'tag' )
				)
			)
		)
	)
);