<?php
$string = '
<form>
	<div class="form-group">
		<label class="form-label required" for="postTitle">' . $L['title'] . '</label>
		<input type="text" id="postTitle" name="title" class="form-control mb-4" placeholder="' . $L['enter-title'] . '" value="' . $Post->Title() . '">
	</div>
					
	<div class="form-group">
		<label for="postSlug">' . $L['permalink'] . '</label>
		<input type="text" id="current-slug" class="form-control" name="slug" placeholder="' . $L['leave-empty-for-autocomplete'] . '" value="' . $Post->PostSef() . '">
	</div>
					
	<div class="form-group">
		<label for="postDescription">' . $L['description'] . '</label>
		<textarea class="form-control" rows="4" placeholder="' . $L['enter-a-short-snippet-from-your-post'] . '" name="description" cols="50" id="description">' . $Post->Description() . '</textarea>
	</div>
					
	<div class="form-group">
		<div class="card shadow-sm mb-4">
			<div class="card-header">
				<h4 class="card-heading">' . $L['category'] . '</h4>
			</div>
			
			<div class="card-body">
				<div class="mb-4">
					<div class="current_language_cats">
						<select class="select2 form-select shadow-none" style="width: 100%; height:36px;" name="category" aria-label="Category select">';

							$cats = adminCategoriesPost( $Post->Post()['langId'], $Post->Post()['blogID'], $Post->Post()['siteId'] );
											
							if ( !empty( $cats ) )
							{
								foreach ( $cats as $id => $cat )
								{
									if ( !empty( $cat['childs'] ) )
									{
										$string .= '<optgroup label="&nbsp;' . htmlspecialchars( $cat['name'] ) . '">';
											
										foreach( $cat['childs'] as $cid => $child )
											$string .= '<option value="sub::' . $cid . '" ' . ( ( !empty( $Post->Post()['subCategoryId'] ) && ( $Post->Post()['subCategoryId'] == $cid ) ) ? 'selected' : '' ) . '>' . $child['name'] . '</option>';
															
											$string .= '</optgroup>';
									} 
									
									else
									{
										$string .= '<option value="cat::' . $id . '" ' . ( ( !empty( $Post->Post()['categoryId'] ) && ( $Post->Post()['categoryId'] == $id ) ) ? 'selected' : '' ) . ( ( empty( $Post->Post()['categoryId'] ) && $cat['default'] ) ? 'selected' : '' ) . '>' . $cat['name'] . '</option>';
									}
														
									unset( $cat ); 
								}
							}
												
							$string .= '
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>';

	//Post Type
	$string .= '
	<div class="form-group">
		<div class="card shadow-sm mb-4">
			<div class="card-header">
				<h4 class="card-heading">' . $L['post-type'] . '</h4>
			</div>
			
			<div class="card-body">
				<div class="form-check">
					<select class="form-select shadow-none" style="width: 100%; height:36px;" name="postFormat" aria-label="Select Format">';
					
					$types = adminCustomTypes( null, $Post->Post()['siteId'] );
					
					foreach( $types as $type )
						$string .= '<option value="' . $type['id'] . '" ' . ( ( !empty( $Post->Post()['customId'] ) && ( $Post->Post()['customId'] == $type['id'] ) ) ? 'selected' : '' ) . ( ( empty( $Post->Post()['customId'] ) && $type['is_default'] ) ? 'selected' : '' ) . '>' . $type['title'] . '</option>';

					$string .= '
					</select>
				</div>
			</div>
		</div>
	</div>';

	$string .= '
	<script type=\'text/javascript\'>
		// Generate slug when the user type the title 
		$(\'#postTitle\').keyup(function(e) {
			$.post(\'' . AJAX_ADMIN_PATH . 'slug/\', 
				{ \'slug\': $(this).val() }, 
			
				function( data ) {
					$(\'#current-slug\').val(data);
				}
			);
		});
	</script>
</form>';

unset( $Post, $cats, $types );