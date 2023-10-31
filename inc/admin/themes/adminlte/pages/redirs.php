<div class="row">
  <div class="col-12">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['redirections'] ?>
			</div>
				<div class="card-body">
					<div class="table-responsive">
				<table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th><?php echo $L['url'] ?></th>
							<th><?php echo $L['code'] ?></th>
							<th><?php echo $L['hits'] ?></th>
							<th><?php echo $L['added'] ?></th>
							<th><?php echo $L['last-accessed'] ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php echo $L['url'] ?></th>
							<th><?php echo $L['code'] ?></th>
							<th><?php echo $L['hits'] ?></th>
							<th><?php echo $L['added'] ?></th>
							<th><?php echo $L['last-accessed'] ?></th>
						</tr>
					</tfoot>
					<tbody>
				<?php $redirs = GetRedirections( $Admin->GetSite(), false );
				if ( !empty( $redirs ) ) :
					foreach( $redirs as $redir ) : ?>
						<tr>
							<td  class="dt-body-center"><p><a href="<?php echo $Admin->GetUrl( 'edit-redirection' . PS . 'id' . PS . $redir['id'] ) ?>"><?php echo ( !empty( $redir['slug'] ) ? $redir['slug'] : $redir['uri'] ) ?></a><br /><?php echo $redir['target'] ?><br /><span class="shownext"><a href="<?php echo $Admin->GetUrl( 'edit-redirection' . PS . 'id' . PS . $redir['id'] ) ?>"><?php echo $L['edit'] ?></a> | <a href="<?php echo $Admin->GetUrl( 'delete-redirection' . PS . 'id' . PS . $redir['id'] ) ?>" onclick="return confirm_alert(this);"><?php echo $L['delete'] ?></a></span></p></td>
							<td  class="dt-body-center"><?php echo $redir['http_code'] ?></td>
							<td  class="dt-body-center"><?php echo $redir['views'] ?></td>
							<td  class="dt-body-center"><?php echo postDate ( $redir['added_time'] ) ?></td>
							<td><?php echo ( !empty( $redir['last_time_viewed'] ) ? postDate ( $redir['last_time_viewed'] ) : '-' ) ?></td>
						</tr>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
				</div>
			</div>
		</div>
</div>