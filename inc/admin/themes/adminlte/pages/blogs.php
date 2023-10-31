<div class="row">
  <div class="col-12">
	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo __( 'blogs' ) ?>
			</div>
				<div class="card-body">
					<div class="table-responsive">
				<table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
					<thead class="thead-light">
						<tr>
							<th><?php echo __( 'name' ) ?></th>
							<th><?php echo __( 'slug' ) ?></th>
							<th><?php echo __( 'description' ) ?></th>
							<th><?php echo __( 'type' ) ?></th>
							<th><?php echo __( 'posts' ) ?></th>
							<th><?php echo __( 'comments' ) ?></th>
						</tr>
					</thead>

					<tbody>
				<?php
				if ( !empty( $siteBlogs ) ) :
				
					foreach( $siteBlogs as $blog ) : ?>
						<tr>
							<td  class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-blog' . PS . 'id' . PS . $blog['id_blog'] ) ?>"><?php echo $blog['name'] ?></a></td>
							<td  class="dt-body-center"><?php echo $blog['sef'] ?></td>
							<td  class="dt-body-center"><?php echo $blog['description'] ?></td>
							<td  class="dt-body-center"><?php echo __( $blog['type'] ) ?></td>
							<td><?php echo $blog['blog_posts'] ?> (<strong><?php echo $blog['unapproved_posts'] ?></strong> <?php echo __( 'unapproved' ) ?>)</td>
							<td><?php echo $blog['num_comments'] ?> (<strong><?php echo $blog['unapproved_comments'] ?></strong> <?php echo __( 'unapproved' ) ?>)</td>
						</tr>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
				</div>
			</div>
		</div>
		
	<input type="hidden" name="_token" value="<?php echo generate_token( 'blogs' ) ?>">
	
	<div class="align-middle">
		<div class="float-left mt-1">
			<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
		</div>
	</div>
	</form>
</div>