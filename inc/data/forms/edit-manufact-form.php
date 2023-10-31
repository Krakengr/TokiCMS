<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Edit Manufacturer Form
#
#####################################################
global $Admin, $Man;

//Make sure that we have this key in the DB
$query = array(
		'SELECT'	=>  "*",

		'FROM'		=> DB_PREFIX . "manufacturers",
				
		'PARAMS' => array( 'NO_PREFIX' => true ),
				
		'WHERE' => "id_site = :site AND id = :id",
				
		'BINDS' 	=> array(
						array( 'PARAM' => ':site', 'VAR' => $this->GetSite(), 'FLAG' => 'INT' ),
						array( 'PARAM' => ':id', 'VAR' => (int) Router::GetVariable( 'key' ), 'FLAG' => 'INT' )
		)
);
			
// Get the data
$Man = Query( $query );

$defaultImage = HTML_ADMIN_PATH_THEME . 'assets' . PS . 'img' . PS . 'default.svg';

$manufactImage = null;

if ( $Man )
{
	if ( !empty( $Man['id_image'] ) )
	{
		$manufactImage = GetMainImageUrl( $Man['id_image'] );
	}
}

$uploadHtml = '
<div class="container">
	<div class="row">
		<div class="col-lg-4 col-sm-12 p-0 pr-2">';

		$uploadHtml .= HiddenFormInput( array( 'name' => 'manufactLogoFile', 'value' => $manufactImage ? $Man['id_image'] : '' ), false );
		
		$args = array(
				'id' => 'buttonRemoveLogo',
				'title' => '<i class="fa fa-trash"></i> ' . __( 'remove-logo' ),
				'class' => 'btn-danger float-right' . ( $manufactImage ? '' : ' d-none' )
			
		);
			
		$uploadHtml .= Button( $args, false );
		
		$uploadHtml .= '
			<button type="button" class="btn btn-primary float-left" data-toggle="modal" data-target="#addImage" id="manufactImageModal"><i class="far fa-image"></i> ' . __( 'add-media' ) . '</button>
		</div>
		<div class="col-lg-8 col-sm-12 p-0 text-center">
			<img id="manufactLogoPreview" width="400" class="img-fluid img-thumbnail" alt="' . __( 'manufacturer-logo' ) . ' ' . __( 'preview' ). '" src="' . ( !$manufactImage ? $defaultImage : $manufactImage ) . '" />
		</div>
	</div>
</div>';

$form = array
(
	'add-manufacturer' => array
	(
		'title' => __( 'add-new-manufacturer' ),
		'col' => 12,
		'data' => array(
		
			'name' => array( 
				'title' => null, 'data' => array
				(
					'name'=>array('label'=>__( 'name' ), 'type'=>'text', 'name' => 'name', 'value' =>$Man['title'], 'tip'=>__('the-title-how-it-appears' ) )
				)
			),
			
			'sef' => array( 
				'title' => null, 'data' => array
				(
					'name'=>array('label'=>__( 'slug' ), 'type'=>'text', 'name' => 'sef', 'value' =>$Man['sef'], 'tip'=>__('slug-tip' ) )
				)
			),
			
			'hr' => array(
				'title' => null, 'data' => array
				(
					'hr-logo'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false )
				)
			),
			
			'logo' => array(
				'title' => null, 'data' => array
				(
					'upload-logo'=>array('label'=>null, 'name' => 'manufacturer_image', 'type'=>'custom-html', 'value'=>$uploadHtml, 'tip'=>null, 'disabled' => false )
				)
			)
		)
	)
);