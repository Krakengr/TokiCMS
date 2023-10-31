<div class="page-header d-flex justify-content-between align-items-right">
  <div><a class="btn btn-primary text-uppercase" href="<?php echo $Admin->GetUrl( 'add-source' ) ?>"> <i class="fa fa-plus me-2"> </i><?php echo $L['add-new'] ?></a></div>
</div>

<div class="row">
  <div class="col-12">
	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['sources'] ?>
			</div>
				<div class="card-body">
					<div class="table-responsive">
				<table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
					<thead class="thead-light">
						<tr>
							<th><?php echo $L['name'] ?></th>
							<th><?php echo $L['url'] ?></th>
							<th><?php echo $L['user'] ?></th>
							<th><?php echo $L['source-type'] ?></th>
							<th><?php echo $L['post-type'] ?></th>
							<th><?php echo $L['added'] ?></th>
							<th><?php echo $L['posts'] ?></th>
						</tr>
					</thead>
					<!--<tfoot>
						<tr>
							<th><?php echo $L['name'] ?></th>
							<th><?php echo $L['url'] ?></th>
							<th><?php echo $L['user'] ?></th>
							<th><?php echo $L['source-type'] ?></th>
							<th><?php echo $L['post-type'] ?></th>
							<th><?php echo $L['added'] ?></th>
							<th><?php echo $L['posts'] ?></th>
						</tr>
					</tfoot>-->
					<tbody>
				<?php $sources = Sources( $Admin->GetSite() );
				if ( !empty( $sources ) ) :
					foreach( $sources as $source ) : 
					
					$type = '';
					
					if ( $source['source_type'] == 'xml' )
					{
						$type 	 = 'XML';
						$xmlData = Json( $source['xml_data'] );
						
						if ( !empty( $xmlData['file_type'] ) )
						{
							$type .= ' (' . $xmlData['file_type'] . ')';
						}
					}
					
					elseif ( $source['source_type'] == 'html' )
					{
						$type = 'HTML';
					}
					
					elseif ( $source['source_type'] == 'rss' )
					{
						$type = 'RSS';
					}
					
					elseif ( $source['source_type'] == 'multi' )
					{
						$type = __( 'multiple-sources-feed' );
					}
					?>
						<tr>
							<td  class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-content-source' . PS . 'id' . PS . $source['id'] ) ?>"><?php echo $source['title'] ?></a></td>
							<td  class="dt-body-center"><?php echo $source['url'] ?></td>
							<td  class="dt-body-center"><?php echo ( !empty( $source['real_name'] ) ? $source['real_name'] : $source['user_name'] ) ?></td>
							<td  class="dt-body-center"><?php echo $type ?></td>
							<td  class="dt-body-center"><?php echo $L[$source['post_type']] ?></td>
							<td  class="dt-body-center"><?php echo postDate( $source['added_time'] ) ?></td>
							<td><?php echo $source['posts_num'] ?></td>
						</tr>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
				</div>
			</div>
		</div>
		
	<input type="hidden" name="_token" value="<?php echo generate_token( 'sources' ) ?>">
	
	<div class="align-middle">
		<div class="float-left mt-1">
			<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
		</div>
	</div>
	</form>
</div>