<?php defined('TOKICMS') or die('Hacking attempt...');

//Bootstrap v4.xx
define( 'DISMISS', 'data-dismiss' );
define( 'TOGGLE', 'data-toggle' );
define( 'TARGET', 'data-target' );

#####################################################
#
# Input File function
#
#####################################################
function InputFile( $args, $echo = true )
{
	$name = $args['name'];
	$id = ( isset( $args['id'] ) ? $args['id'] : $name );
	$label = ( isset( $args['label'] ) ? $args['label'] : null );
	$labelInput = ( isset( $args['labelInput'] ) ? $args['labelInput'] : '' );
	$value = ( isset( $args['value'] ) ? $args['value'] : '' );
	$type = ( isset( $args['type'] ) ? $args['type'] : 'file' );
	$class = ( isset( $args['class'] ) ? $args['class'] : 'custom-file-input' );
	$accept = ( isset( $args['accept'] ) ? $args['accept'] : null );
	$addAfter = ( isset( $args['addAfter'] ) ? $args['addAfter'] : null );
	
	$html = '
	<div class="form-group">';
	
	if ( $label )
	{
		$html .= '<label for="' . $id . '">' . $label . '</label>';
	}
	
	$html .= '
		<div class="input-group">
			<div class="custom-file">
				<input type="' . $type . '" class="' . $class . '" id="' . $id . '" name="' . $name . '">
				<label class="custom-file-label" for="' . $id . '">' . $labelInput . '</label>
			</div>
			<!--
			<div class="input-group-append">
				<span class="input-group-text">Upload</span>
			</div>
			-->
		</div>';
	
	if ( $addAfter )
	{
		$html .= $addAfter;
	}
	
	$html .= '	
	</div>' . PHP_EOL;

	if ( $echo )
		echo $html;
	
	else
		return $html;
}

#####################################################
#
# Form Input function
#
#####################################################
function FormInput_( $args, $echo = true )
{
	$name = $args['name'];
	$id = ( isset( $args['id'] ) ? $args['id'] : $name );
	$value = ( isset( $args['value'] ) ? $args['value'] : null );
	$type = ( isset( $args['type'] ) ? $args['type'] : '' );
	$xtra = ( isset( $args['xtra'] ) ? $args['xtra'] : null );
	
	$html = '<input type="' . $type . '" id="' . $id . '" name="' . $name . '"';
	
	$html .= ( $value ? ' value="' . $value . '"' : '' );
	$html .= ( $xtra ? ' ' . $xtra : '' );
	$html .= '>' . PHP_EOL;
	
	if ( $echo )
		echo $html;
	
	else
		return $html;
}

#####################################################
#
# Form Input function
#
#####################################################
function FormInput( $args, $echo = true )
{
	$label = ( isset( $args['label'] ) ? $args['label'] : '' );
	$name = ( isset( $args['name'] ) ? $args['name'] : '' );
	$value = ( isset( $args['value'] ) ? $args['value'] : '' );
	$id = ( isset( $args['id'] ) ? $args['id'] : $label );
	$type = ( isset( $args['type'] ) ? $args['type'] : 'text' );
	$placeholder = ( isset( $args['placeholder'] ) ? $args['placeholder'] : null );
	$required = ( isset( $args['required'] ) ? $args['required'] : null );
	$disabled = ( isset( $args['disabled'] ) ? $args['disabled'] : null );
	$tip = ( isset( $args['tip'] ) ? $args['tip'] : null );
	$addBefore = ( isset( $args['addBefore'] ) ? $args['addBefore'] : null );
	$addAfter = ( isset( $args['addAfter'] ) ? $args['addAfter'] : null );
	$addFormTag = ( isset( $args['addFormTag'] ) ? $args['addFormTag'] : null );
	$addAfterInput = ( isset( $args['addAfterInput'] ) ? $args['addAfterInput'] : null );
	$xtra = ( isset( $args['xtra'] ) ? $args['xtra'] : null );
	$class = ( isset( $args['class'] ) ? $args['class'] : null );
	
	$html = '';
	
	if ( $addAfterInput )
	{
		$html .= '
		<div class="form-group row">
			<label for="' . $id . '" class="col-sm-2 col-form-label">' . $label . '</label>
			<div class="col-sm-10">';
	}
	$html .= '
		<input type="' . $type . '" class="form-control' . ( $class ? ' ' . $class : '' ) . '" name="' . $name . '" value="' . $value . '" id="' . $id . '"';
			
		$html .= ( $placeholder ? ' placeholder="' . $placeholder . '"' : '' ); 
			
		$html .= ( $required ? ' required' : '' );
			
		$html .= ( $disabled ? ' disabled' : '' );
				
		$html .= ( $xtra ? ' ' . $xtra : '' );
			
		$html .= '>';
			
		if ( $tip )
		{
			$html .= '<small' . ( $id ? ' id="' . $id . '"' : '' ) . ' class="form-text text-muted">' . $tip . '</small>';
		}
	
	if ( $addAfterInput )
	{
		$html .= '
			</div>
		</div>' . PHP_EOL;
	}
	
	if ( $echo )
		echo $html;

	else
		return $html;
}

#####################################################
#
# Button function
#
#####################################################
function Button( $args, $echo = true )
{
	$id = ( isset( $args['id'] ) ? $args['id'] : null );
	$dataId = ( isset( $args['data-id'] ) ? $args['data-id'] : null );
	$tag = ( isset( $args['tag'] ) ? $args['tag'] : 'button' );
	$label = ( isset( $args['label'] ) ? $args['label'] : null );
	$name = ( isset( $args['name'] ) ? $args['name'] : null );
	$align = ( isset( $args['align'] ) ? $args['align'] : null );
	$href = ( isset( $args['href'] ) ? $args['href'] : null );
	$value = ( isset( $args['value'] ) ? $args['value'] : null );
	$role = ( isset( $args['role'] ) ? $args['role'] : null );
	$title = ( isset( $args['title'] ) ? $args['title'] : '' );
	$type = ( isset( $args['type'] ) ? $args['type'] : 'button' );
	$class = ( isset( $args['class'] ) ? $args['class'] : '' );
	$dismissModal = ( ( isset( $args['dismiss'] ) && $args['dismiss'] ) ? true : false );
	$addFormDiv = ( ( isset( $args['form-group'] ) && $args['form-group'] ) ? true : false );
	$addBefore = ( isset( $args['addBefore'] ) ? $args['addBefore'] : null );
	$addAfter = ( isset( $args['addAfter'] ) ? $args['addAfter'] : null );
	$addAfterButton = ( isset( $args['addAfterButton'] ) ? $args['addAfterButton'] : null );
	$tip = ( isset( $args['tip'] ) ? $args['tip'] : null );
	
	$html = '';
	
	if ( $addBefore )
	{
		$html .= $addBefore;
	}
	
	if ( $addFormDiv )
	{
		$html .= '<div class="form-group">';
		
		if ( $label )
		{
			$html .= '<label class="form-check-label"' . ( $id ? ' for="' . $id . '"' : '' ) . '>' . $label . '</label>';
		}
		
		if ( $tip )
		{
			$html .= '<small' . ( $id ? ' id="' . $id . '"' : '' ) . ' class="form-text text-muted">' . $tip . '</small>';
		}
	}
	
	$html .= '<' . $tag . ' type="' . $type . '" class="btn ' . $class . '"';

	if ( $dismissModal )
		$html .= ' ' . DISMISS . '="modal"';

	if ( $id )
		$html .= ' id="' . $id . '"';

	if ( $dataId )
		$html .= ' data-id="' . $dataId . '"';
	
	if ( $name )
		$html .= ' name="' . $name . '"';
	
	if ( $value )
		$html .= ' value="' . $value . '"';
	
	if ( $align )
		$html .= ' align="' . $align . '"';
	
	if ( $role )
		$html .= ' role="' . $role . '"';
	
	if ( $href )
		$html .= ' href="' . $href . '"';

	$html .= '>';
	
	if ( $addAfterButton )
	{
		$html .= $addAfterButton;
	}

	$html .= $title;

	$html .= '</' . $tag . '>';
	
	if ( $addFormDiv )
	{
		$html .= '</div>';
	}
	
	if ( $addAfter )
	{
		$html .= $addAfter;
	}
	
	$html .= PHP_EOL;
	
	if ( $echo )
		echo $html;

	else
		return $html;
}

#####################################################
#
# Check Box function
#
#####################################################
function CheckBox( $args, $echo = true )
{
	$id 		= ( isset( $args['id'] ) ? $args['id'] : 'checkBox' );
	$label 		= ( isset( $args['label'] ) ? $args['label'] : '' );
	$divId 		= ( isset( $args['div-id'] ) ? $args['div-id'] : null );
	$dnone 		= ( isset( $args['dnone'] ) ? $args['dnone'] : null );
	$name 		= ( isset( $args['name'] ) ? $args['name'] : '' );
	$value 		= ( isset( $args['value'] ) ? $args['value'] : '1' );
	$checked 	= ( isset( $args['checked'] ) ? $args['checked'] : null );
	$class 		= ( isset( $args['class'] ) ? $args['class'] : null );
	$disabled 	= ( isset( $args['disabled'] ) ? $args['disabled'] : null );
	$tip 		= ( isset( $args['tip'] ) ? $args['tip'] : null );
	$radioData 	= ( isset( $args['radio-data'] ) ? $args['radio-data'] : null );
	$type 		= ( isset( $args['type'] ) ? $args['type'] : 'checkbox' );
	
	$html = '
	<div class="form-group' . ( $class ? ' ' . $class : '' ) . ( $dnone ? ' d-none' : '' ) . '"' . ( $divId ? ' id="' . $divId . '"' : '' ) . '>';
	
	if ( ( $type == 'radio' ) && is_array( $radioData ) && !empty( $radioData ) )
	{
		foreach( $radioData as $_ => $radio )
		{
			$html .= '
			<div class="form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="' . $radio['name'] . '" id="' . $_ . '" value="' . $radio['value'] . '"' . ( ( isset( $radio['checked'] ) && $radio['checked'] ) ? ' checked' : '' ) . ( ( isset( $radio['disabled'] ) && $radio['disabled'] ) ? ' disabled' : '' ) . '>
				  <label class="form-check-label" for="' . $_ . '">' . $radio['title'] . '</label>
			</div>' . PHP_EOL;
		}
	}
	
	else
	{
		$html .= '
		<div class="form-check">
			<input type="' . $type . '" name="' . $name . '" value="' . $value . '" class="form-check-input" id="' . $id . '"';
			
			if ( $checked )
			{
				$html .= ' checked';
			}
			
			if ( $disabled )
			{
				$html .= ' disabled';
			}
			
			$html .= '>';
			
			$html .= '
			<label class="form-check-label" for="' . $id . '">' . $label . '</label>';
			
			$html .= '
		</div>';
	}
	
	if ( $tip )
	{
		$html .= '<small id="' . $id . 'Help" class="form-text text-muted">' . $tip . '</small>';
	}
		
	$html .= '
	</div>' . PHP_EOL;
	
	if ( $echo )
		echo $html;

	else
		return $html;
}

#####################################################
#
# Nav Tabs function
#
#####################################################
function NavTabs( $args, $echo = true )
{
	$id = ( isset( $args['id'] ) ? $args['id'] : null );
	$class = ( isset( $args['class'] ) ? $args['class'] : 'nav-tabs' );
	$role = ( isset( $args['role'] ) ? $args['role'] : null );
	$lis = ( isset( $args['lis'] ) ? $args['lis'] : null );
	
	$html = '<ul class="nav ' . $class . '"' . ( $id ? ' id="' . $id . '"' : '' ) . ( $role ? ' role="' . $role . '"' : '' ) . '>' . PHP_EOL;
	
	if ( $lis )
	{
		foreach( $lis as $_ => $li )
		{
			$html .= '<li';
			
			$html .= ' class="' . ( isset( $li['class'] ) ? $li['class'] : 'nav-item' ) . '"';
			
			$html .= ' role="' . ( isset( $li['role'] ) ? $li['role'] : 'presentation' ) . '"';
			
			$html .= '>';
			
			$html .= '<a';
			
			$html .= ' class="' . ( isset( $li['link-class'] ) ? $li['link-class'] : 'nav-link' );
			
			$html .= ( ( isset( $li['active'] ) && $li['active'] ) ? ' active' : '' ) . '"';
			
			$html .= ( isset( $li['id'] ) ? ' id="' . $li['id'] . '"' : '' );
			
			$html .= ( isset( $li['toggle'] ) ? ' ' . TOGGLE . '="' . $li['toggle'] . '"' : '' );
			
			$html .= ( isset( $li['data-target'] ) ? ' ' . TARGET . '="#' . $li['data-target'] . '"' : '' );
			
			$html .= ' type="' . ( isset( $li['type'] ) ? $li['type'] : 'button' ) . '"';
			
			$html .= ( isset( $li['role'] ) ? ' role="' . $li['role'] . '"' : '' );
			
			$html .= ( isset( $li['aria-selected'] ) ? ' aria-selected="' . $li['aria-selected'] . '"' : '' );
			
			$html .= ' aria-controls="general">';
			
			$html .= $li['title'];
			
			$html .= '</a>';
		}
	}
	
	$html .= '</ul>';
	
	if ( $echo )
		echo $html;

	else
		return $html;
}

#####################################################
#
# Modal function
#
#####################################################
function Modal( $args, $echo = true )
{
	$_buttons = array(
		'cancel' => array( 'name' => __( 'cancel' ), 'type' => 'button', 'id' => null, 'class' => 'btn-default', 'dismiss' => true ),
		
		'insert' => array( 'name' => __( 'insert' ), 'type' => 'button', 'id' => 'insertMedia', 'class' => 'btn-primary disabled', 'dismiss' => false )
	);
	
	$title = $args['title'];
	$id = ( isset( $args['id'] ) ? $args['id'] : $title );
	$fade = ( isset( $args['fade'] ) ? $args['fade'] : null );
	$extra = ( isset( $args['extra'] ) ? ' ' . $args['extra'] : '' );
	$loader = ( isset( $args['loader'] ) ? $args['loader'] : null );
	$size = ( isset( $args['size'] ) ? 'modal-' . $args['size'] : '' );
	$body = ( isset( $args['body'] ) ? $args['body'] : '
		<div class="alert alert-success d-none success"></div>
		<div class="alert alert-danger d-none error"></div>
		<div id="post-detail"></div>' );
	$style = ( isset( $args['style'] ) ? $args['style'] : 'padding-right: 17px; display: block; width:100%;' );
	$buttons = ( ( isset( $args['buttons'] ) && ( is_null( $args['buttons'] ) || !is_null( $args['buttons'] ) ) ) ? $args['buttons'] : $_buttons );
	
	$html = '<!-- Modal start -->' . PHP_EOL;
	
	$html .= '
	<div class="modal' . ( $fade ? ' fade' : '' ) . ' ' . $id . '" id="' . $id . '" role="dialog" aria-hidden="true"' . $extra . '>
		<div class="modal-dialog ' . $size . '" role="document" style="' . $style . '">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">' . $title . '</h4>
				</div>
				<div class="modal-body">';

				if ( $loader ) 
				{
					$html .= '
					<div id="modal-loader" style="text-align: center;">
						<img src="' . HTML_ADMIN_PATH_THEME . 'assets/img/loading.gif">
					</div> ';
				}
				
				$html .= $body;
				
				$html .= '
				</div>
				
				<div class="modal-footer">';
				if ( !empty( $buttons ) )
				{
					foreach( $buttons as $id => $button )
					{
						$args = array(
								'id' => ( ( isset( $button['id'] ) && $button['id'] ) ? $button['id'] : null ),
								'dismiss' => ( ( isset( $button['dismiss'] ) && $button['dismiss'] ) ? true : false ),
								'data-id' => ( ( isset( $button['data-id'] ) && $button['data-id'] ) ? $button['id'] : null ),
								'type' => $button['type'],
								'class' => $button['class'],
								'title' => $button['name'],
						);
				
						$html .= Button( $args, false );

						//$html .= '<button type="' . $button['type'] . '" class="btn ' . $button['class'] . '"';
						
						//if ( isset( $button['dismiss'] ) && $button['dismiss'] )
						//	$html .= ' data-dismiss="modal"';
						
						//if ( isset( $button['id'] ) && $button['id'] )
						//	$html .= ' id="' . $button['id'] . '"';
						
						//if ( isset( $button['data-id'] ) && $button['data-id'] )
						//	$html .= ' data-id="' . $button['data-id'] . '"';
						
						//$html .= '>';

						//$html .= $button['name'];
						
						//$html .= '</button>' . PHP_EOL;
					}
				}
				$html .= '	
				</div>
			</div>
		</div>
	</div>';
	
	if ( $echo )
		echo $html;

	else
		return $html;
}

#####################################################
#
# Hidden Form Input function
#
#####################################################
function HiddenFormInput( $args, $echo = true )
{
	$name = $args['name'];
	$id = ( isset( $args['id'] ) ? $args['id'] : $name );
	$value = ( isset( $args['value'] ) ? $args['value'] : '' );
	$xtra = ( isset( $args['xtra'] ) ? $args['xtra'] : null );
	
	$html = '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . $value . '"';
	$html .= ( $xtra ? ' ' . $xtra : '' );
	$html .= '>' . PHP_EOL;
	
	if ( $echo )
		echo $html;
	
	else
		return $html;
}

#####################################################
#
# Bootstrap Table function
#
#####################################################
function BootstrapTable( $args, $echo = true )
{
	$args = array(
			'responsive' => ( isset( $args['responsive'] ) ? $args['responsive'] : false ),
			'hasHead' => ( ( isset( $args['headData'] ) && !empty( $args['headData'] ) ) ? true : false ),
			'hasBody' => ( ( isset( $args['bodyData'] ) && !empty( $args['bodyData'] ) ) ? true : false ),
			'headData' => ( isset( $args['headData'] ) ? $args['headData'] : null ),
			'bodyData' => ( isset( $args['bodyData'] ) ? $args['bodyData'] : null ),
			'class' => ( isset( $args['class'] ) ? $args['class'] : 'table-striped table-valign-middle' )
	);
	
	$html = '';
	
	if ( $args['responsive'] )
	{
		$html .= '
		<div class="table-responsive">';
	}
	
	$html .= '
	<table class="table ' . $args['class'] . '">';
	
	if ( $args['hasHead'] )
	{
		$html .= '
		<thead>';
		
		if ( $args['headData'] )
		{
			$html .= '
			<tr>';
			
			foreach ( $args['headData'] as $th )
			{
				$html .= '<th' . ( isset( $th['class'] ) ? ' class="' . $th['class'] . '"' : '' ) . '>' . $th['title'] . '</th>';
			}

			$html .= '
			</tr>';
		}
		
		$html .= '</thead>';
	}
	
	if ( $args['hasBody'] )
	{
		$html .= '
		<tbody>';
		
		if ( $args['bodyData'] )
		{
			foreach ( $args['bodyData'] as $tr )
			{
				$html .= '<tr>';
				
				foreach( $tr['td'] as $td )
				{
					$html .= '<td' . ( isset( $td['class'] ) ? ' class="' . $td['class'] . '"' : '' ) . '>' . $td['data'] . '</td>';
				}
				
				$html .= '</tr>';
			}
		}

		$html .= '
		</tbody>';
	}
	
	$html .= '</table>';
	
	if ( $args['responsive'] )
	{
		$html .= '
		</div>';
	}
	
	if ( $echo )
		echo $html;
	
	else
		return $html;
}

#####################################################
#
# Bootstrap Card function
#
#####################################################
function BootstrapCard( $args, $echo = true )
{
	$args = array(
			'body' => ( isset( $args['body'] ) ? $args['body'] : null ),
			'footer' => ( isset( $args['footer'] ) ? $args['footer'] : null ),
			'ids' => ( isset( $args['ids'] ) ? $args['ids'] : null ),
			'header' => ( isset( $args['header'] ) ? $args['header'] : null ),
			'tools' => ( isset( $args['tools'] ) ? $args['tools'] : null ),
			'header-class' => ( isset( $args['header-class'] ) ? $args['header-class'] : null ),
			'body-class' => ( isset( $args['body-class'] ) ? $args['body-class'] : null ),
			'card-class' => ( isset( $args['card-class'] ) ? $args['card-class'] : null )
	);
	
	$html = '
	<div class="card' . ( $args['card-class'] ? ' ' . $args['card-class'] : '' ) . '"';
	
	if ( $args['ids'] )
		$html .= ' ' . $args['ids'];
	
	$html .= '>';
	
	if ( $args['header'] )
	{
		$html .= '
			<div class="card-header' . ( $args['header-class'] ? ' ' . $args['header-class'] : '' ) . '">
                <h4 class="card-title">' . $args['header'] . '</h4>';
				
				
			if ( $args['tools'] )
			{
				$html .= '
				<div class="card-tools">
					' . $args['tools'] . '
				</div>';
			}
		
		$html .= '
           </div>';
		   
	}
	
	if ( $args['body'] )
	{
		$html .= '
			<div class="card-body' . ( $args['body-class'] ? ' ' . $args['body-class'] : '' ) . '">
				' . $args['body'] . '
			</div>';
	}
	
	if ( $args['footer'] )
	{
		$html .= '
			<div class="card-footer">
				' . $args['footer'] . '
			</div>';
	}

	$html .= '
	</div>';
	
	if ( $echo )
		echo $html;
	
	else
		return $html;
}