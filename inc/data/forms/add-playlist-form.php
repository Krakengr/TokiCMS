<?php defined('TOKICMS') or die('Hacking attempt...');

$L = $this->lang;

#####################################################
#
# Categories array
#
#####################################################
$catsHtmlCode = '
<div class="form-group row">
	<label for="category" class="col-sm-2 col-form-label">' . $L['category'] . '</label>
	<div class="col-sm-10">';

$catsHtmlCode .= '<select class="form-control" name="category" aria-label="Category select">';

$query = array(
	'SELECT' =>  "id, name, is_default",
	'FROM'	=> DB_PREFIX . 'categories',
	'PARAMS' => array( 'NO_PREFIX' => true ),
	'WHERE' => "id_lang = :lang AND id_blog = :blog AND id_parent = 0",
	'BINDS'	=> array(
			array( 'PARAM' => ':lang', 'VAR' => $this->langID, 'FLAG' => 'INT' ),
			array( 'PARAM' => ':blog', 'VAR' => $this->blogID, 'FLAG' => 'INT' )
	),

	'ORDER' => 'name ASC'
);

$cats = Query( $query, true );

if ( $cats )
{
	foreach( $cats as $cat )
	{
		
		$catsHtmlCode .= '<option value="' . $cat['id'] . '" ' . ( $cat['is_default'] ? 'selected' : '' ) . '>' . stripslashes( $cat['name'] ) . '</option>';
				
		$query = array(
			'SELECT' =>  "id, name",
			'FROM'	=> DB_PREFIX . 'categories',
			'PARAMS' => array( 'NO_PREFIX' => true ),
			'WHERE' => "id_parent = :id",
			'BINDS'	=> array(
					array( 'PARAM' => ':id', 'VAR' => $cat['id'], 'FLAG' => 'INT' )
			),

			'ORDER' => 'name ASC'
		);

		$subs = Query( $query, true );
		
		if ( $subs )
		{
			foreach( $subs as $sub )
			{
				$catsHtmlCode .= '¦&nbsp;&nbsp;&nbsp;&nbsp;<option value="' . $sub['id'] . '">' . stripslashes( $sub['name'] ) . '</option>';
			}
		}
	}
}

$catsHtmlCode .= '</select><small id="categoryHelp" class="form-text text-muted">' . $L['source-category-playlist-tip'] . '</small></div></div>';			

unset( $cats );
#####################################################
#
# Add New Video Playlist Form
#
#####################################################
$form = array
(
	'add-playlist' => array
	(
		'title' => $L['add-new-video-playlist'],
		'data' => array(
		
			'playlist-settings' => array( 
				'title' => null, 'tip' => __( 'add-new-playlist-tip' ), 'data' => array
				(
					'playlist-title'=>array('label'=>$L['title'], 'type'=>'text', 'name' => 'title', 'value' =>null, 'required' => true, 'tip'=>$L['the-title-how-it-appears']),
					
					'playlist-desc'=>array('label'=>$L['description'], 'type'=>'textarea', 'name' => 'descr', 'value' =>null, 'tip'=>null ),
					
					'hr'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false ),
					
					'h2'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<h5>' . __( 'youtube-automatic-post-generator' ) . '</h5><small class="form-text text-muted">' . sprintf( __( 'youtube-automatic-post-generator-tip' ), $this->GetUrl( 'advanced-settings' ) ) . '</small><hr />', 'tip'=>null, 'disabled' => false ),
					
					'playlist-url'=>array('label'=>$L['url'], 'type'=>'text', 'name' => 'url', 'value' =>null, 'tip'=>null, 'placeholder' => 'https://www.youtube.com/playlist?list=xxxxxxxxxxxxxxxxxxxxxxx' ),
					
					'grab-videos'=>array('label'=>$L['automatically-post-videos-as-content-on-your-site'], 'type'=>'checkbox', 'name' => 'grab_videos', 'value' => false, 'tip'=>$L['automatically-post-videos-as-content-tip'] ),
					
					'set-video-date'=>array('label'=>$L['set-the-video-date-as-post-date'], 'type'=>'checkbox', 'name' => 'set_date_from_video', 'value' => false, 'tip'=>$L['set-the-video-date-as-post-date-tip'] ),
					
					'source-category'=>array('label'=>null, 'name' => 'category', 'type'=>'custom-html', 'value'=>$catsHtmlCode, 'tip'=>null )
				)
			)
		)
	)
);