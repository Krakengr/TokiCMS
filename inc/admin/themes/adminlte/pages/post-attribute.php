<?php 
	$groups = GetAdminAttributeGroups();
?>
<div class="row">
  <div class="col-12">
  <div class="card">
  <div class="card-body">
		<form class="tab-content" id="form" method="post" action="" autocomplete="off">
			<div class="form-row">
				<div class="form-group col-md-9">
					<h4><?php echo $L['edit-post-attribute'] ?> "<?php echo $Att['name'] ?>"</h4>
						
					<div class="form-group">
						<label for="inputName"><?php echo $L['name'] ?></label>
						<input type="text" class="form-control" name="name" id="inputName" value="<?php echo htmlspecialchars( $Att['name'] ) ?>">
						<small id="nameHelp" class="form-text text-muted"><?php echo $L['the-title-how-it-appears'] ?></small>
					</div>

					<div class="form-group">
						<label for="inputGroup"><?php echo $L['attribute-group'] ?></label>
						<select name="group" class="form-control" id="inputGroup" >
						<?php foreach( $groups as $group ) : ?>
							<option value="<?php echo $group['id'] ?>" <?php echo ( ( $Att['id_group'] == $group['id'] ) ? 'selected' : '' ) ?>><?php echo $group['name'] ?></option>
						<?php endforeach ?>
						</select>
					</div>

					<div class="form-group">
					  <label class="form-label" for="order"><?php echo $L['sort-order'] ?></label>
					  <input class="form-control" value="<?php echo $Att['attr_order'] ?>" type="number" name="order" step="any" min="1" max="99">
					</div>
					
					<?php 
						if ( $Admin->MultiLang() && !empty( $Langs ) ) : 
							$trans = Json( $Att['trans_data'] );
					?>
					<hr />
					<div class="form-group">
						<label for="inputType"><?php echo $L['translations'] ?></label>
						<div class="table-responsive">
							<table class="table table-bordered table-hover">
								<thead>
									<tr>
										<td class="text-left"><strong><?php echo $L['lang'] ?></strong></td>
										<td class="text-right"><strong><?php echo $L['value'] ?></strong></a></td>
									</tr>
								</thead>
								<tbody>
								<?php foreach( $Langs as $lData ) : 
										$lId = $lData['id'];
								?>
									<tr>
										<td class="text-left"><strong><?php echo $lData['title'] ?></strong></td>
										<td class="text-right"><input type="text" class="form-control" name="trans[<?php echo $lId ?>]" value="<?php echo ( ( !empty( $trans ) && isset( $trans['lang-' . $lId] ) ) ? $trans['lang-' . $lId]['value'] : '' ) ?>"></td>
									</tr>
								<?php endforeach ?>
								</tbody>
							</table>
						</div>
					</div>
					<?php endif ?>
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
						<small id="deleteHelp" class="form-text text-muted"><?php echo $L['delete-post-attribute-tip'] ?></small>
					</div>

					<input type="hidden" name="_token" value="<?php echo generate_token( 'editPostAtt_' . $Att['id'] ) ?>">
					
					<div class="align-middle">
						<div class="float-left mt-1">
							<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
							<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'post-attributes' ) ?>" role="button"><?php echo $L['cancel'] ?></a>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
</div>
</div>