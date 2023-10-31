<?php
	include ( ARRAYS_ROOT . 'forms-arrays.php');
	
	include ( ARRAYS_ROOT . 'generic-arrays.php');
	
	$atts = AdminGetAttributes( $Admin->GetSite(), $Admin->GetLang(), $Admin->GetBlog() );
	
	$custom = AdminCustomTypes( null, $Admin->GetSite() );
	
	$tableElements = $genericTablesArray;

	if ( $Form['table-type'] == 'price' )
	{
		
	}
?>
<form id="form" method="post" action="" autocomplete="off">
	<div class="container-fluid">
		
		<div class="col-md-10">
		
			<?php 
			if ( $Form['table-type'] == 'price' )
			{
				include( ADMIN_THEME_PAGES_ROOT . 'table-templates' . DS . 'price-table.php' );
			}
			
			elseif ( $Form['table-type'] == 'product' )
			{
				include( ADMIN_THEME_PAGES_ROOT . 'table-templates' . DS . 'product-table.php' );
			}
			
			else
			{
				include( ADMIN_THEME_PAGES_ROOT . 'table-templates' . DS . 'default-table.php' );
			}
			?>
			
			<!-- Preview Card - ->
			<div class="card card-default">
				<div class="card-header">
					<h3 class="card-title">
						<?php echo __( 'preview' ) ?>
					</h3>
				</div>

				<div class="card-body" id="demoForm">
					<?php //FormElementToHtml( $Form['elements'], true, true ) ?>
				</div>
			</div>-->

		</div>
	</div>
	
	<div class="col-md-10">
	<div class="card card-primary">
		<div class="card-body">
		<div class="align-middle">
			<div class="float-left mt-1">
				<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
				<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'tables' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
			</div>
		</div>
		</div>
	</div>
		</div>
	<input type="hidden" name="_token" value="<?php echo generate_token( 'edit_table_' . $Form['id'] ) ?>">
</form>