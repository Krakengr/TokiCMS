<?php
	require ( FUNCTIONS_ROOT 	. 'schema-functions.php' );
	$schemas = AdminGetPostSchemas( $Post );
?>
<h5><?php echo $L['schema-settings'] ?></h5>
<div class="mb-3">
<?php if ( empty( $schemas ) ) : ?>
	<div class="alert alert-warning" role="alert">
		<?php echo $L['nothing-found'] ?>
	</div>
<?php else : ?>

	<div class="alert alert-info" role="alert">
		<?php echo $L['how-to-edit-custom-schema-data-tip'] ?>
	</div>

	<?php 
		if ( count( $schemas ) == 1 ) : 
			$schemaFormData = SingleSchemaData( null, $schemas['0'] );
	?>
		<?php echo SchemaDataToHtml( $schemaFormData['formData'], true, $schemas['0']['id'], $schemaFormData['schemaData'] ) ?>
		
	<?php else : ?>
	<ul class="nav nav-tabs" id="schemasTab" role="tablist">
	<?php $i = 0;
			foreach ( $schemas as $schema ) :
				$i++;
	?>
		<li class="nav-item" role="presentation">
			<button class="nav-link <?php echo ( ( $i == 1 ) ? 'active' : '' ) ?>" id="tab-<?php echo $schema['id'] ?>" data-bs-toggle="tab" data-bs-target="#schema-tab<?php echo $schema['id'] ?>" type="button" role="tab" aria-controls="schema-tab<?php echo $schema['id'] ?>" aria-selected="false"><?php echo $schema['title'] ?></button>
		</li>
	<?php endforeach ?>
	</ul>
	<div class="tab-content" id="schemasTabContent">
	<?php $i = 0;
			foreach ( $schemas as $schema ) :
				$i++;
				$schemaFormData = SingleSchemaData( null, $schema );
	?>
		<div class="tab-pane show <?php echo ( ( $i == 1 ) ? 'active' : '' ) ?>" id="schema-tab<?php echo $schema['id'] ?>" role="tabpanel" aria-labelledby="schema-<?php echo $schema['id'] ?>">
			<?php echo SchemaDataToHtml( $schemaFormData['formData'], true, $schema['id'], $schemaFormData['schemaData'] ) ?>
		</div>
	  <?php unset( $schemaFormData, $schema ); endforeach; ?>
	</div>
	<?php endif ?>
<?php endif ?>
</div>