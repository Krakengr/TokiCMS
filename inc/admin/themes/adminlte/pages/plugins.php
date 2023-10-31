<div class="container-fluid">
	<div class="row">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['plugins'] ?>
			</div>

				<div class="card-body">
					<div class="table-responsive">
			<form class="tab-content" id="form" method="post" action="" autocomplete="off">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead class="thead-light">
						<tr>
							<th><?php echo $L['name'] ?></th>
							<th><?php echo $L['description'] ?></th>
							<th><?php echo $L['author'] ?></th>
							<th><?php echo $L['compatible'] ?></th>
							<th><?php echo $L['enable'] ?></th>
						</tr>
					</thead>
					<tbody>
				<?php if ( !empty( $Plugins ) ) :
					foreach( $Plugins as $key => $plugin ) : ?>
						<tr>
							<td class="dt-body-center"><?php echo $plugin['title'] ?> (v<?php echo $plugin['version'] ?>)</td>
							<td class="dt-body"><?php echo $plugin['description'] ?></td>
							<td class="dt-body-center"><a href="<?php echo ( !empty( $plugin['link'] ) ? $plugin['link'] : '#' ) ?>"><?php echo $plugin['author'] ?></a></td>
							<td class="dt-body-center"><?php echo ( $plugin['isCompatible'] ? '&#10004;' : '&#10060;' ) ?></td>
							<td class="dt-body-center"><div class="custom-control custom-switch"><input type="checkbox" name="plugin-enable[<?php echo $key ?>]" class="custom-control-input" id="pluginSwitch<?php echo $key ?>" <?php echo ( ( !empty( $pluginsDb ) && isset( $pluginsDb[$key] ) && ( $pluginsDb[$key]['status'] == 'active' ) ) ? 'checked' : '' ) ?>><label class="custom-control-label" for="pluginSwitch<?php echo $key ?>"></label></div></td>
						</tr>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
				</div>
			</div>
		</div>
		
	<input type="hidden" name="_token" value="<?php echo generate_token( 'plugins' ) ?>">
	
	<div class="align-middle">
		<div class="float-left mt-1">
			<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
		</div>
	</div>
	</form>
</div>