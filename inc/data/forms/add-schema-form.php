<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Add Schema Form
#
#####################################################
$L = $this->lang;

include ( ARRAYS_ROOT . 'seo-arrays.php');

$schemaTypes = array();

foreach( $schemaTypesArray as $id => $row )
	$schemaTypes[$id] = array( 'name' => $row['name'], 'title'=> $row['title'], 'disabled' => false, 'data' => array() );
	
//Grab the Langs
$langsBlogForm = Langs( $this->GetSite(), false, false );

//Grab the Blogs
$blogs = $this->db->from( null, "
SELECT * FROM `" . DB_PREFIX . "blogs`
WHERE (id_lang = " . $this->GetLang() . " OR id_lang = 0) AND (id_site = " . $this->GetSite() . ")
ORDER BY name ASC" )->all();

//Build Languages data
$langsData = array();

if ( !empty( $langsBlogForm ) )
{
	foreach ( $langsBlogForm as $key => $formLang )
	{
		$langsData[$key] = array( 'name' => 'lang::' . $formLang['id'], 'title'=>sprintf($L['on-lang-only'], $formLang['title'] ), 'disabled' => false, 'data' => array() );
		
		unset( $key, $formLang );
	}
}

//Build Blogs data
$blogsData = array();

if ( !empty( $blogs ) )
{
	foreach ( $blogs as $blog )
	{
		$blogsData[$blog['sef']] = array( 'name' => 'blog::' . $blog['id_blog'], 'title'=>sprintf($L['on-blog-only'], $blog['name'] ), 'disabled' => false, 'data' => array() );
	}
}

$schemaOn = array(
				'frontpage' => array( 'name' => $L['frontpage'], 'data' => array(
						'frontpage' => array( 'name' => 'frontpage', 'title'=> $L['frontpage'], 'disabled' => false, 'data' => array() ),
						'every-blog-frontpage' => array( 'name' => 'every-blog-frontpage', 'title'=> $L['every-blog-frontpage'], 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? false : true ), 'data' => array() ),
						'every-language-frontpage' => array( 'name' => 'every-language-frontpage', 'title'=> $L['every-language-frontpage'], 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multilang', 'site' ) ? false : true ), 'data' => array() )
					)
				),
				'langs' => array( 'name' => $L['langs'], 'data' => $langsData ),
				'blogs' => array( 'name' => $L['blogs'], 'data' => $blogsData ),
				'posts' => array( 'name' => $L['posts'], 'data' => array(
						'show-on-posts' => array( 'name' => 'all-posts', 'title'=> $L['all-posts'], 'disabled' => false, 'data' => array() ),
						'show-on-orphan-posts' => array( 'name' => 'orphan-posts', 'title'=> $L['orphan-posts'], 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? false : true ), 'data' => array() )
					)
				),
				'pages' => array( 'name' => $L['pages'], 'data' => array(
										'show-on-pages' => array( 'name' => 'all-pages', 'title'=> $L['all-pages'], 'disabled' => false, 'data' => array() ),
										'show-on-orphan-pages' => array( 'name' => 'orphan-pages', 'title'=> $L['orphan-pages'], 'disabled' => ( $this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? false : true ), 'data' => array() )
									)
				),
				'products' => array( 'name' => $L['products'], 'data' => array(
										'show-on-products' => array( 'name' => 'all-products', 'title'=> $L['all-products'], 'disabled' => ( !$this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? true : false ), 'data' => array() )
									)
				),
				'threads' => array( 'name' => $L['threads'], 'data' => array(
										'show-on-threads' => array( 'name' => 'all-threads', 'title'=> $L['all-threads'], 'disabled' => ( !$this->adminSettings::IsTrue( 'enable_multiblog', 'site' ) ? true : false ), 'data' => array() )
									)
				),
);

#####################################################
#
# Add Schema Form
#
#####################################################
$form = array
(
	'schema' => array
	(
		'title' => $L['add-schema'],
		'data' => array
		(
			'schema-options' => array(
				'title' => null, 'data' => array
				(
					'title'=>array( 'label'=>$L['title'], 'type'=>'text', 'name' => 'schema[title]', 'value' => null, 'tip'=>null, 'required'=>true ),
					'select-the-schema-type'=>array('label'=>$L['select-the-schema-type'], 'type'=>'select', 'name' => 'schema[schema_type]', 'value'=>null, 'tip'=>null, 'firstNull' => false, 'data' => $schemaTypes ),
					'enable-on'=>array('label'=>$L['enable-on'], 'type'=>'select-group', 'name' => 'schema[enable_on]', 'value'=>null, 'tip'=>$L['enable-on-tip'], 'firstNull' => true, 'data' => $schemaOn ),
					'exclude-from'=>array('label'=>$L['exclude-from'], 'type'=>'select-group', 'name' => 'schema[exclude_from]', 'value'=>null, 'tip'=>$L['exclude-from-tip'], 'firstNull' => true, 'data' => $schemaOn ),
				)
			)
		)
	)
);