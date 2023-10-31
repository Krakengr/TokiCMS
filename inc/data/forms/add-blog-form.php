<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# New Blog Form
#
#####################################################
global $Admin;

$L = $this->lang;

$settings = $this->adminSettings::Get();

require ( ARRAYS_ROOT . 'generic-arrays.php');

$blogsLangsData = array();

$blogsLangsData['all'] = array( 'name' => 0, 'title'=>$L['everywhere'], 'disabled' => false, 'data' => null );

$langs = Langs( $this->GetSite(), false, false );

if ( !empty( $langs ) )
{
	foreach ( $langs as $key => $lang )
	{
		$blogsLangsData[$key] = array( 'name' => $lang['id'], 'title'=>sprintf($L['on-lang-only'], $lang['title'] ), 'disabled' => false,
			'data' => array( 'name' => 'data-flag', 'value' => SITE_URL . 'languages' . PS . 'flags' . PS . $key . '.png' ) );
	}
}

$form = array
(
	'add-blog' => array
	(
		'title' => $L['add-new-blog'],
		'col' => 6,
		'data' => array(
		
			'blog-title' => array( 
				'title' => null, 'data' => array
				(
					'blog-title'=>array('label'=>$L['title'], 'type'=>'text', 'name' => 'title', 'value' =>null, 'tip'=>$L['the-title-how-it-appears'])
				)
			),
			
			'blog-sef' => array( 
				'title' => null, 'data' => array
				(
					'blog-sef'=>array('label'=>$L['slug'], 'type'=>'text', 'name' => 'slug', 'value' =>null, 'tip'=>$L['slug-tip'])
				)
			),
			
			'blog-slogan' => array( 
				'title' => null, 'data' => array
				(
					'blog-slogan'=>array('label'=>$L['blog-slogan'], 'type'=>'text', 'name' => 'slogan', 'value' =>null, 'tip'=>$L['blog-slogan-tip'])
				)
			),
			
			'blog-descr' => array( 
				'title' => null, 'data' => array
				(
					'blog-sef'=>array('label'=>$L['description'], 'type'=>'textarea', 'name' => 'description', 'value' =>null, 'tip'=>$L['descr-tip'])
				)
			),
			
			'frontpage' => array( 
				'title' => null, 'data' => array
				(
					'frontpage'=>array('label'=>$L['show-on-frontpage'], 'type'=>'checkbox', 'name' => 'frontpage', 'value' =>null, 'tip'=>$L['show-blog-on-frontpage-tip'])
				)
			),
			
			'news-sitemap' => array( 
				'title' => null, 'data' => array
				(
					'news-sitemap'=>array('label'=>$L['enable-in-news-sitemap'], 'type'=>'checkbox', 'name' => 'sitemap', 'value' =>null, 'tip'=>$L['enable-in-news-sitemap-tip'])
				)
			),
			
			'enable-rss' => array( 
				'title' => null, 'data' => array
				(
					'enable-rss'=>array('label'=>$L['enable-rss'], 'type'=>'checkbox', 'name' => 'enable_rss', 'value' =>null, 'tip'=>$L['rss-blog-tip'])
				)
			),
			
			'select-language' => array( 
				'title' => null, 'data' => array
				(
					'select-language'=>array('label'=>$L['blog-is-enabled'], 'name' => 'select-lang', 'type'=>'select', 'value'=>null, 'tip'=>$L['blog-is-enabled-tip'], 'firstNull' => false, 'data' => $blogsLangsData, 'id' => 'slcCountry', 'class' => 'selectpicker' ),
				)
			),
		)
	)
);

$code = '<script type="application/javascript">
$(document).ready(function() 
{
  $(\'#blogsTable\').DataTable( {
	columnDefs: [
    { orderable: false, targets: [-1, -4] },
	{ className: \'text-center\', targets: [1,2,3,4] }
  ],
    "paging":   true,
    "ordering": true,
	"searching": false,
    "info":     false
    } );
});</script>' . PHP_EOL;

$this->AddFooterCode( $code );