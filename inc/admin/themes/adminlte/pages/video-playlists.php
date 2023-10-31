<div class="row">
  <div class="col-12">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['video-playlists'] ?>
			</div>
				<div class="card-body">
					<div class="table-responsive">
			<form class="tab-content" id="form" method="post" action="" autocomplete="off">
				<table class="table table-bordered" id="playTable" width="100%" cellspacing="0">
					<thead class="thead-light">
						<tr>
							<th scope="col"><?php echo $L['name'] ?></th>
							<th scope="col"><?php echo $L['description'] ?></th>
							<th scope="col"><?php echo $L['url'] ?></th>
							<th scope="col"><?php echo $L['date'] ?></th>
						</tr>
					</thead>
					<tbody>
				<?php $playlists = Playlists();
				if ( !empty( $playlists ) ) :
					foreach( $playlists as $playlist ) :
				?>
						<tr>
							<td  class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-playlist' . PS . 'id' . PS . $playlist['id'] ) ?>"><?php echo $playlist['title'] ?></a></td>
							<td  class="dt-body-center"><?php echo $playlist['descr'] ?></td>
							<td  class="dt-body-center"><?php echo $playlist['source_play_url'] ?></td>
							<td  class="dt-body-center"><?php echo postDate( $playlist['added_time'] )  ?></td>
						</tr>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
				</div>
			</div>
		</div>

	<div class="align-middle">
		<div class="float-left mt-1">
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'add-playlist' ) ?>" role="button"><?php echo $L['add-new-video-playlist'] ?></a>
		</div>
	</div>
	</form>
</div>