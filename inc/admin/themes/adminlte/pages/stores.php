<div class="row">
  <div class="col-12">
  
	<div class="container-fluid">
		<form action="<?php echo $Admin->GetUrl( 'stores' ) ?>" method="post" id="post" role="form">
			<div class="row">
				<div class="col-md-10 offset-md-1">
					<div class="row">
						<div class="col-6">
							<div class="form-group">
								<label><?php echo __( 'search' ) ?></label>
								<div class="input-group input-group-default">
									<input name="search" type="search" class="form-control form-control-default" placeholder="<?php echo __( 'type-your-keywords-here' ) ?>" value="<?php echo $search ?>">
									<div class="input-group-append">
										<button type="submit" class="btn btn-default btn-default">
											<i class="fa fa-search"></i>
										</button>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-3">
							<div class="form-group">
								<label><?php echo __( 'order-by' ) ?></label>
								<select name="order" class="form-control" style="width: 100%;">
									<option value="name"<?php echo ( ( $isSearch && ( $orderBy == 'name' ) ) ? ' selected' : '' ) ?>><?php echo __( 'name' ) ?></option>
									<option value="url"<?php echo ( ( $isSearch && ( $orderBy == 'url' ) ) ? ' selected' : '' ) ?>><?php echo __( 'url' ) ?></option>
								</select>
							</div>
						</div>
						
						<div class="col-3">
							<div class="form-group">
								<label><?php echo __( 'sort-order' ) ?></label>
								<select name="sort" class="form-control" style="width: 100%;">
									<option value="asc"<?php echo ( ( $isSearch && ( $order == 'asc' ) ) ? ' selected' : '' ) ?>><?php echo __( 'asc' ) ?></option>
									<option value="desc"<?php echo ( ( $isSearch && ( $order == 'desc' ) ) ? ' selected' : '' ) ?>><?php echo __( 'desc' ) ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>

	<form class="tab-content" id="form" method="post" action="" autocomplete="off">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['stores'] ?>
			</div>
				<div class="card-body">
					<div class="table-responsive">
				<table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
					<thead class="thead-light">
						<tr>
							<th><a href="<?php echo $Admin->GetUrl( $Admin->CurrentAction(), null, false, ( array( 'sort', 'name', $nextOrder ) ) ) ?>"><?php echo $L['name'] ?><?php if ( $orderBy && ( $orderBy == 'name' ) ) : ?> <i class="expandable-table-caret fas fa-caret-<?php echo $arrow ?> fa-fw"></i></a><?php endif ?></th>
							<th><?php echo $L['description'] ?></th>
							<th><a href="<?php echo $Admin->GetUrl( $Admin->CurrentAction(), null, false, ( array( 'sort', 'url', $nextOrder ) ) ) ?>"><?php echo $L['url'] ?><?php if ( $orderBy && ( $orderBy == 'url' ) ) : ?> <i class="expandable-table-caret fas fa-caret-<?php echo $arrow ?> fa-fw"></i></a><?php endif ?></th>
							<th><?php echo $L['type'] ?></th>
						</tr>
					</thead>

					<tbody>
				<?php
				if ( !empty( $Stores ) ) :
					foreach( $Stores as $key => $store ) : ?>
						<tr>
							<td class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-store' . PS . 'id' . PS . $key ) ?>"><?php echo $store['name'] ?></a></td>
							<td><?php echo $store['description'] ?></td>
							<td><?php echo $store['url'] ?></td>
							<td><?php echo $store['typ'] ?></td>
						</tr>
					<?php 
					if ( !empty( $store['childs'] ) ) :
						foreach( $store['childs'] as $c => $child ) : ?>
						<tr>
							<td  class="dt-body-center"><a href="<?php echo $Admin->GetUrl( 'edit-store' . PS . 'id' . PS . $c ) ?>">¦&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $child['name'] ?></a></td>
							<td><?php echo $child['description'] ?></td>
							<td><?php echo $child['url'] ?></td>
							<td><?php echo $child['typ'] ?></td>
						</tr>
						<?php endforeach ?>
					<?php endif ?>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
				</div>
			</div>
		</div>
		
	<input type="hidden" name="_token" value="<?php echo generate_token( 'stores' ) ?>">
	
	<div class="align-middle">
		<div class="float-left mt-1">
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->GetUrl( 'add-store' ) ?>" role="button"><?php echo $L['add-new-store'] ?></a>
		</div>
	</div>
	<!--
	<div class="align-middle">
		<div class="float-left mt-1">
			<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
		</div>
	</div>-->
	</form>
</div>