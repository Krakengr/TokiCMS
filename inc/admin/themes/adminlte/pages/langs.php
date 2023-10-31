<div class="page-header d-flex justify-content-between align-items-right">
  <div><a class="btn btn-primary text-uppercase" href="<?php echo $Admin->GetUrl( 'add-lang' ) ?>"> <i class="fa fa-plus me-2"> </i><?php echo $L['add-new'] ?></a></div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
	<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['langs'] ?>
			</div>
	<div class="card-body">
      <div class="table-responsive">
		<form class="tab-content" id="form" method="post" action="" autocomplete="off">
				<table class="table table-bordered" id="langsTable" width="100%" cellspacing="0">
					<thead class="thead-light">
						<tr>
							<th><?php echo $L['name'] ?></th>
							<th><?php echo $L['locale'] ?></th>
							<th><?php echo $L['code'] ?></th>
							<th><?php echo $L['default'] ?></th>
							<th><?php echo $L['order'] ?></th>
							<th><?php echo $L['posts'] ?></th>
							<th><?php echo $L['flag'] ?></th>
						</tr>
					</thead>
					<tbody>
				<?php $langs = Langs( $Admin->GetSite(), false );
				if ( !empty( $langs ) ) :
					foreach( $langs as $key => $lang ) : ?>
						<tr>
							<td  class="dt-body-center"><a href="<?php echo ( $lang['is_default'] ? $Admin->GetUrl( 'language' ) : $Admin->GetUrl( 'edit-lang' . PS . 'id' . PS . $lang['id'] ) ) ?>"><?php echo $lang['title'] ?></a></td>
							<td  class="dt-body-center"><?php echo $lang['locale'] ?></td>
							<td  class="dt-body-center"><?php echo $lang['code'] ?></td>
							<td><input type="radio"<?php echo ( !$lang['is_default'] ? ' title="' . $L['select-as-default'] . '"' : '' ) ?> name="default_lang" value="<?php echo $lang['id'] ?>" <?php echo ( $lang['is_default'] ? 'checked' : '' ) ?>></td>
							<td><input style="width: 4em" value="<?php echo $lang['lang_order'] ?>" type="number" name="lang-order[<?php echo $lang['id'] ?>]" step="any" min="1" max="99"><span style="display: none;"><?php echo $lang['lang_order'] ?></span></td>
							<td><?php echo $lang['num'] ?></td>
							<td><img src="<?php echo  SITE_URL . 'languages' . PS . 'flags' . PS . $lang['flagicon'] ?>" title="<?php echo $lang['title'] ?>" alt="<?php echo $lang['title'] ?>" width="16" height="11" style="width: 16px; height: 11px;" /></td>
						</tr>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
		
	<input type="hidden" name="_token" value="<?php echo generate_token( 'langs' ) ?>">
	
	<div class="align-middle">
		<div class="float-left mt-1">
			<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
		</div>
	</div>
	</form>
</div>
</div>
</div>
</div>