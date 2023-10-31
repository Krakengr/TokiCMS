<?php 
	$types = GetAdminCustomTypes();
	$_cats = AdminGetCategoriesFull();
?>
<div class="row">
  <div class="col-12">
  <div class="card">
  <div class="card-body">
		<form class="tab-content" id="form" method="post" action="" autocomplete="off">
			<div class="form-row">
				<div class="form-group col-md-12">
					<h4><?php echo $L['edit-attribute-group'] ?> "<?php echo $AttGroup['name'] ?>"</h4>
						
					<div class="form-group">
						<label for="inputName"><?php echo $L['name'] ?></label>
						<input type="text" class="form-control" name="name" id="inputName" value="<?php echo htmlspecialchars( $AttGroup['name'] ) ?>">
						<small id="nameHelp" class="form-text text-muted"><?php echo $L['the-title-how-it-appears'] ?></small>
					</div>
					
					<?php if( !empty( $types ) ) : ?>
					<div class="form-group">
						<label for="inputPostType"><?php echo $L['post-type'] ?></label>
						<select name="postType" class="form-control" id="inputPostType" >
						<?php foreach( $types as $type ) : ?>
							<option value="<?php echo $type['id'] ?>" <?php echo ( ( $AttGroup['id_custom_type'] == $type['id'] ) ? 'selected' : '' ) ?>><?php echo $type['title'] ?></option>
						<?php endforeach ?>
						</select>
						<div class="form-text"><?php echo $L['select-attribute-group-types-tip'] ?></div>
					</div>
					<?php endif ?>
					
					<?php if ( $Admin->MultiLang() || $Admin->MultiBlog() ) : ?>
					<div class="form-group">
						<label for="inputWhere"><?php echo ( $Admin->MultiBlog() ? $L['blog'] : $L['lang'] ) ?></label>
						<select name="where" class="form-control" id="inputWhere" >
						<?php foreach( $_cats as $lCode => $lData ) :
								if ( $Admin->MultiBlog() ) : ?>
								<optgroup label="<?php echo $lData['name'] ?>">
									<option value="blog::0||lang::<?php echo $lData['id'] ?>" <?php echo ( ( ( $AttGroup['id_blog'] == 0 ) && ( $AttGroup['id_lang'] == $lData['id'] ) ) ? 'selected' : '' ) ?>>---<?php echo $L['no-blog'] ?></option>
									<?php if ( !empty( $lData['childs'] ) ) :
									
										foreach( $lData['childs'] as $cId => $cData ) :
											if ( $cData['type'] != 'blog' )
												continue;
										?>
											<option value="blog::<?php echo $cId ?>||lang::<?php echo $lData['id'] ?>" <?php echo ( ( ( $AttGroup['id_blog'] == $cId ) && ( $AttGroup['id_lang'] == $lData['id'] ) ) ? 'selected' : '' ) ?>><?php echo $cData['name'] ?></option>

										<?php if ( !empty( $cData['childs'] ) ) :
											foreach( $cData['childs'] as $_cId => $_cData ) :
												if ( $_cData['type'] != 'cat' )
													continue;
										?>
											<option value="blog::<?php echo $cId ?>||lang::<?php echo $lData['id'] ?>||cat::<?php echo $_cData['id'] ?>" <?php echo ( ( $AttGroup['id_category'] == $_cData['id'] ) ? 'selected' : '' ) ?>>¦&nbsp;&nbsp;<?php echo $_cData['name'] ?></option>
											<?php endforeach ?>
											
											<?php if ( !empty( $_cData['childs'] ) ) :
												foreach( $_cData['childs'] as $__cId => $__cData ) : ?>
												<option value="blog::<?php echo $cId ?>||lang::<?php echo $lData['id'] ?>||cat::<?php echo $__cData['id'] ?>" <?php echo ( ( $AttGroup['id_category'] == $__cData['id'] ) ? 'selected' : '' ) ?>>&nbsp;&nbsp;¦&nbsp;&nbsp;<?php echo $__cData['name'] ?></option>
												<?php endforeach ?>
											<?php endif ?>
										<?php endif ?>
										<?php endforeach ?>
									<?php endif ?>
								</optgroup>
								<?php else : ?>
									<option value="lang::<?php echo $lData['id'] ?>" <?php echo ( ( $AttGroup['id_lang'] == $lData['id'] ) ? 'selected' : '' ) ?>><?php echo $lData['title'] ?></option>
								<?php endif ?>
						<?php endforeach ?>
						</select>
						<div class="form-text"><?php echo ( $Admin->MultiBlog() ? $L['select-attribute-group-blog-tip'] : $L['select-attribute-group-lang-tip'] ) ?></div>
					</div>
					<?php else : ?>
						<input type="hidden" name="where" value="lang::<?php echo $Admin->DefaultLang()['id'] ?>">
					<?php endif ?>
					
					
					<?php 
						/*if ( $Admin::MultiLang() ) :
							$Langs = Langs( $Admin::GetSite(), false );
					?>
					<div class="form-group">
						<label for="inputLanguage"><?php echo $L['language'] ?></label>
						<select name="language" class="form-control" id="inputLanguage" >
						<?php foreach( $Langs as $lCode => $lData ) : ?>
							<option value="<?php echo $lData['id'] ?>" <?php echo ( ( $AttGroup['id_lang'] == $lData['id'] ) ? 'selected' : '' ) ?>><?php echo $lData['title'] ?></option>
						<?php endforeach ?>
						</select>
						<div class="form-text"><?php echo $L['select-attribute-group-lang-tip'] ?></div>
					</div>
					<?php else : ?>
						<input type="hidden" name="language" value="<?php echo $Admin::DefaultLang()['lang']['id'] ?>">
					<?php endif ?>
					
					<?php if ( $Admin::MultiBlog() ) : ?>
					<div class="form-group">
						<label for="inputBlog"><?php echo $L['blog'] ?></label>
						<select name="blog" class="form-control" id="inputBlog" >
							<option value="0">---</option>
						<?php 
							$Blogs = Blogs( $Admin::GetSite(), false );
							if ( !empty( $Blogs ) ) : 
								foreach( $Blogs as $bId => $bData ) : ?>
								<option value="<?php echo $bData['id_blog'] ?>" <?php echo ( ( $AttGroup['id_blog'] == $bData['id_blog'] ) ? 'selected' : '' ) ?>><?php echo $bData['name'] ?></option>
							<?php endforeach ?>
						<?php endif ?>
						</select>
						<div class="form-text"><?php echo $L['select-attribute-group-blog-tip'] ?></div>
					</div>
					<?php else : ?>
						<input type="hidden" name="blog" value="0">
					<?php endif */ ?>
					
					<div class="form-group">
					  <label class="form-label" for="order"><?php echo $L['sort-order'] ?></label>
					  <input class="form-control" value="<?php echo $AttGroup['group_order'] ?>" type="number" name="order" step="any" min="1" max="99">
					</div>
					
					<?php /*
						if ( $Admin->MultiLang() ) : 
							$Langs = $Admin->OtherLangs();
							$trans = Json( $AttGroup['trans_data'] );
					?>
					<hr />
					<div class="form-group">
						<label for="inputType"><?php echo $L['translations'] ?></label>
						<div class="table-responsive">
						<?php if ( !empty( $Langs ) ) : ?>
							<table class="table table-bordered table-hover">
								<thead>
									<tr>
										<td class="text-left"><strong><?php echo $L['lang'] ?></strong></td>
										<td class="text-right"><strong><?php echo $L['value'] ?></strong></a></td>
									</tr>
								</thead>
								<tbody>
								<?php foreach( $Langs as $lId => $lData ) : ?>
									<tr>
										<td class="text-left"><strong><?php echo $lData['lang']['title'] ?></strong></td>
										<td class="text-right"><input type="text" class="form-control" name="trans[<?php echo $lId ?>]" value="<?php echo ( ( !empty( $trans ) && isset( $trans[$lId] ) ) ? $trans[$lId]['value'] : '' ) ?>"></td>
									</tr>
								<?php endforeach ?>
								</tbody>
							</table>
							<?php endif ?>
						</div>
					</div>
					<?php endif */ ?>
				</div>
			</div>
			
			<hr />
			
			<div class="form-row">
				<div class="form-group col-md-6">
					<div class="form-check">
						<input class="form-check-input" type="checkbox" value="1" name="delete" id="deleteCheckBox" >
						<label class="form-check-label" for="deleteCheckBox">
							<?php echo $L['delete'] ?>
						</label>
						<small id="titleHelp" class="form-text text-muted"><?php echo $L['delete-attribute-group-tip'] ?></small>
					</div>

					<input type="hidden" name="_token" value="<?php echo generate_token( 'editAttGroup_' . $AttGroup['id'] ) ?>">
					
					<div class="align-middle">
						<div class="float-left mt-1">
							<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
							<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'attribute-groups' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
</div>
</div>