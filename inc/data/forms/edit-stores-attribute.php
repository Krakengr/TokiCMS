<?php defined('TOKICMS') or die('Hacking attempt...');

#####################################################
#
# Edit Stores Attribute Form
#
#####################################################
global $Att;

$Att = ΑdminGetSingleStoresAttribute( (int) Router::GetVariable( 'key' ) );

$checkHtml = '
<div class="form-check">
	<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
	<label class="form-check-label" for="deleteCheckBox">
		' . __( 'delete' ) . '
	</label>
	<small id="deleteCheckBoxHelp" class="form-text text-muted">' . __( 'delete-post-attribute-tip' ) . '</small>
</div>';

$transHtml = '';

if ( $this->MultiLang() )
{
	$Langs = $this->SiteOtherLangs();
	$trans = Json( $Att['trans_data'] );
	$transHtml = '
	<hr />
	<div class="form-group">
		<label for="inputType">' . __( 'translations' ) . '</label>
		<div class="table-responsive">';
		if ( !empty( $Langs ) )
		{
			$transHtml .= '
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<td class="text-left"><strong>' . __( 'lang' ) . '</strong></td>
						<td class="text-right"><strong>' . __( 'value') . '</strong></a></td>
					</tr>
				</thead>
				<tbody>';
				
				foreach( $Langs as $lData )
				{
					$lId = $lData['id'];
					
					$transHtml .= '
					<tr>
						<td class="text-left"><strong>' . $lData['title'] . '</strong></td>
						<td class="text-right"><input type="text" class="form-control" name="trans[' . $lId . ']" value="' . ( ( !empty( $trans ) && isset( $trans['lang-' . $lId] ) ) ? $trans['lang-' . $lId]['value'] : '' ) . '"></td>
					</tr>';
				}
				
				$transHtml .= '
				</tbody>
			</table>';
		}
		else
		{
			$transHtml .= '
			<div class="alert alert-warning" role="alert">' . __( 'no-other-langs-found-tip' ) . '</div>';
		}
		$transHtml .= '
		</div>
	</div>';
}

#####################################################
#
# Edit Store Attribute Form
#
#####################################################

$form = array
(
	'generic-settings' => array
	(
		'title' => __( 'edit-stores-attribute' ),
		'col' => 12,
		'data' => array(
		
			'name' => array( 
				'title' => null, 'data' => array
				(
					'name'=>array('label'=>__( 'name' ), 'type'=>'text', 'name' => 'name', 'value' => htmlspecialchars( $Att['name'] ), 'tip'=>__('the-title-how-it-appears' ) )
				)
			),
			
			'order' => array( 
				'title' => null, 'data' => array
				(
					'name'=>array('label'=>__( 'order' ), 'type'=>'num', 'min' => 0, 'max'=> 99, 'name' => 'order', 'value' => $Att['attr_order'], 'tip'=>null )
				)
			),
			
			'trans' => array(
				'title' => null, 'data' => array
				(
					'trans'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>$transHtml, 'tip'=>null, 'disabled' => false )
				)
			),
			
			'hr' => array(
				'title' => null, 'data' => array
				(
					'hr'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>'<hr />', 'tip'=>null, 'disabled' => false )
				)
			),
			
			'delete' => array(
				'title' => null, 'data' => array
				(
					'delete'=>array('label'=>null, 'name' => null, 'type'=>'custom-html', 'value'=>$checkHtml, 'tip'=>null, 'disabled' => false )
				)
			)
		)
	)
);