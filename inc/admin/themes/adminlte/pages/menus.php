<div class="row">

	<?php if ( !empty( $Menus ) ) : ?>
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><?php echo __( 'select-menu' ) ?></h3>
			</div>
			
			<div class="card-body">
				<div class="content info-box">
					<div class="form-group ">
						<label><?php echo __( 'select-a-menu-to-edit' ) ?>:</label>
						<form onSubmit="return menuUri();" class="form-inline ">
							<select class="form-control mr-3">
							<?php foreach( $Menus as $menu ) : ?>
								<option id="url" name="url" value="<?php echo $Admin->GetUrl( 'edit-menu' . PS . 'id' . PS . $menu['id_menu'] ) ?>" <?php echo ( ( ( $Admin->CurrentAction() == 'edit-menu' ) && ( Router::GetVariable( 'key' ) == $menu['id_menu'] ) ) ? 'selected' : '' ) ?>><?php echo $menu['title'] ?></option>
							<?php endforeach ?>
							</select>
							<button class="btn btn-sm btn-default btn-menu-select float-right mr-1"><?php echo __( 'select' ) ?></button>
						</form> 
					</div>
					<script>
					function menuUri()
					{
						var url = document.getElementById("url").value;
						location.href = url;
						return false;
					}
					</script>
				</div>
				
				<?php if ( $Admin->CurrentAction() == 'edit-menu' ) : ?>
				<p class="text-primary">
					<a href="<?php echo $Admin->GetUrl( 'menus' ) ?>"><?php echo __( 'create-new-menu' ) ?></a>
				</p>
				<?php endif ?>
			</div>
		</div>
	</div>
	<?php endif ?>

	<div class="col-md-4">
		<div class="card bg-light card-tabs">
			<div class="card-header">
				<h3 class="card-title">
					<?php echo __( 'add-menu-items' ) ?>
				</h3>
			</div>

			<div class="card-body">
				<div class="cat-form">
					<p><?php echo __( 'select-categories-blogs-add-custom-links-to-menus' ) ?></p>

						<div class="panel-group" id="menu-items">
							<div class="card">
								<a class="nav-link btn-default active" data-toggle="collapse" href="#categories-list" data-parent="#menu-items"><?php echo __( 'categories' ) ?> <span class="dropdown-toggle float-right"></span></a>

								<div id="categories-list" class="collapse show" aria-labelledby="categories-list" data-parent="#menu-items">
									<div class="card-body">	
									<?php $cats = GetAdminCategories();
										if ( !empty( $cats ) ) : ?>							
										<div class="form-group" style="height:250px;overflow-y:scroll;">
										<?php foreach( $cats as $id => $cat ) : ?>
												<div class="form-check">
													<input class="form-check-input" type="checkbox" name="select-category[]" value="<?php echo $cat['id'] ?>" <?php echo ( ( $Admin->CurrentAction() != 'edit-menu' ) ? 'disabled' : '' ) ?>>
													<label class="form-check-label"><?php echo $cat['name'] ?></label>
												</div>
											<?php endforeach ?>
										</div>	
										<div class="item-list-footer">
											<label class="btn btn-sm btn-default"><input type="checkbox" id="select-all-categories"> <?php echo __( 'select-all' ) ?></label>
											<div class="form-group">
												<button type="button" class="btn btn-default btn-sm" id="add-categories"><?php echo __( 'add-to-menu' ) ?></button>
											</div>
										</div>
										<?php else : ?>
										<div class="alert alert-warning"><?php echo __( 'nothing-found' ) ?></div>
										<?php endif ?>
									</div>						
								</div>
							</div>

							<div class="card">
								<a class="nav-link btn-default" data-toggle="collapse" href="#content-list" data-parent="#menu-items"><?php echo __( 'pages' ) ?> <span class="dropdown-toggle float-right"></span></a>
								
								<div id="content-list" class="collapse" aria-labelledby="menu-items" data-parent="#menu-items">
									<div class="card-body">							
										<div class="form-group p-2">
											<div class="form-check">
												<input class="form-check-input" type="checkbox" value="" id="deepSearch">
												<label class="form-check-label" for="deepSearch">
													<?php echo __( 'search-in-all-langs-sites' ) ?>
												</label>
											</div>
										</div>
					
										<div class="mb-3 p-3">
											<label for="searchContent" class="form-label"><?php echo __( 'search-existing-content' ) ?></label>
											<input type="text" class="form-control" id="searchContent" name="search" value="">
										</div>
					
										<div class="query-notice text-secondary" id="query-notice-message" style="display: none;">
											<em class="query-notice-default"><?php echo __( 'no-search-term-specified' ) . ' ' . __( 'showing-recent-items' ) ?></em>
											<em class="query-notice-hint screen-reader-text"><?php echo __( 'search-or-select-an-item' ) ?></em>
										</div>
					
										<div class="search-waiting" id="search-waiting" style="display: none;">
											<span class="spinner"><img src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/img/loading.gif"></span>
										</div>

										<div id="search-results" class="query-results" tabindex="0" style="display: none;">
										</div>
					
										<div id="latest-posts" class="latest-posts" tabindex="0" style="display: none;">
										</div>	
											
										<div id="content-footer" class="item-list-footer" style="display: none;">
											<label class="btn btn-sm btn-default btn-sm"><input type="checkbox" id="select-all-pages"> <?php echo $L['select-all'] ?></label>
											
											<div class="form-group">
												<button type="button" id="add-pages" class="pull-right btn btn-default btn-sm"><?php echo $L['add-to-menu'] ?></button>
											</div>
										</div>
									</div>						
								</div>
							</div>
							
							<div class="card">
									<a class="nav-link btn-default" data-toggle="collapse" href="#custom-links" data-parent="#menu-items"><?php echo __( 'custom-links' ) ?> <span class="dropdown-toggle float-right"></span></a>
								<div id="custom-links" class="collapse" aria-labelledby="custom-links" data-parent="#menu-items">
									<div class="card-body">					
										<div class="item-list-body">
											<div class="form-group">
												<label><?php echo $L['url'] ?></label>
												<input type="url" id="linkurl" class="form-control" value="" placeholder="https://">
											</div>
											
											<div class="form-group">
												<label><?php echo $L['link-text'] ?></label>
												<input type="text" id="linktext" class="form-control" value="" placeholder="">
											</div>
										</div>	
										<div id="content-footer" class="item-list-footer form-group">
											<button type="button" class="pull-right btn btn-default btn-sm" id="add-custom-link"><?php echo $L['add-to-menu'] ?></button>
										</div>
									</div>
								</div>
							</div>
						</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-8">
		<div class="card card-default">
			<div class="card-header">
				<h3 class="card-title">
					<?php echo __( 'menu-structure' ) ?>
				</h3>
			</div>

			<div class="card-body">
				<?php if ( $Admin->CurrentAction() == 'menus' ) : ?>
				<h4><?php echo __( 'create-new-menu' ) ?></h4>
				<form method="post" class="form-horizontal" action="<?php echo $Admin->GetUrl( 'add-menu' ) ?>">
					<div class="row">
						<div class="col-sm-12">
							<label><?php echo __( 'name' ) ?></label>
						</div>
						
						<div class="col-sm-6">
							<div class="form-group">							
								<input type="text" name="title" class="form-control" required>
							</div>
						</div>
						
						<div class="col-sm-6 text-right">
							<button class="btn btn-sm btn-primary"><?php echo __( 'create-menu' ) ?></button>
						</div>
					</div>
					
					<input type="hidden" name="_token" value="<?php echo generate_token( 'add-menu' ) ?>">
				</form>
			
				<?php elseif ( $Admin->CurrentAction() == 'edit-menu' ) : ?>

				<div id="menu-content">
					<h4><span><?php echo __( 'menu-structure' ) ?></span></h4>
					
					<div id="menuContent p-4">
						<?php BuildMenuHtml( $Menu['items'] ) ?>
					</div>
					
					<div class="menulocation p-4">
						<h4><span><?php echo __( 'menu-position' ) ?></span></h4>
						<?php if ( empty( ThemeValue( 'menu-position' ) ) ) : ?>
							<p><i class="icon fas fa-info"></i> <?php echo $L['your-theme-does-not-natively-support-menus'] ?></p>
						<?php else :
							$pos = ( isset( ThemeValue( 'menu-position' )['0'] ) ? ThemeValue( 'menu-position' )['0'] : ThemeValue( 'menu-position' ) );
						?>
						<div class="p-4">
						<?php foreach( $pos as $k => $w ) : ?>
							<div class="form-check">
								<input class="form-check-input" id="menuPos" type="radio" name="<?php echo $w['name'] ?>" value="<?php echo $k ?>">
								<label class="form-check-label"><?php echo $w['name'] ?></label>
							</div>
						<?php endforeach ?>
						</div>
						<?php endif ?>
					</div>
					
					<div class="p-3">
						<button class="btn btn-sm btn-primary mr-1" id="saveMenu"><?php echo __( 'save' ) ?></button>
						
						<button class="btn btn-sm btn-danger float-right" id="deleteMenu"><?php echo __( 'delete' ) ?></button>
					</div>										
				</div>
		
			<?php endif ?>
			</div>
		</div>
	</div>
</div>