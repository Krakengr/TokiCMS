<div class="row">
  <div class="col-12">
    <div class="card">
	
	<div><a class="btn btn-primary text-uppercase float-right" href="<?php echo $Admin->GetUrl( 'add-category' ) ?>"> <i class="fa fa-plus me-2"> </i><?php echo $L['add-new'] ?></a></div>
	
		<?php if ( empty( $cats ) ) : ?>
			<div class="alert alert-warning" role="alert">
				<?php echo $L['nothing-found'] ?>
			</div>
		<?php else : ?>

		<form id="catsBulkForm" method="post" action="<?php echo $Admin->GetUrl( 'categories-bulk' ) ?>" role="form">
		<div class="card-body table-responsive p-0">
        <table class="table table-centered text-nowrap mb-0">
          <thead class="thead-light">
            <tr>
			<th style="width: 3%" class="d-none d-xl-table-cell">
                <label class="customcheckbox mb-3">
                    <input type="checkbox" id="mainCheckbox" />
                    <span class="checkmark"></span>
                </label>
            </th>
            <th class="text-left" scope="col" style="width: 15%"><a href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . ( Router::GetVariable( 'subAction' ) ? PS . Router::GetVariable( 'subAction' ) : '' ), null, false, ( array( 'sort', 'name', $order ) ) ) ?>"><?php echo $L['title'] ?><?php if ( $orderBy && ( $orderBy == 'name' ) ) : ?> <i class="expandable-table-caret fas fa-caret-<?php echo $arrow ?> fa-fw"></i></a><?php endif ?></th>
			
			<th class="text-center d-none d-xl-table-cell" scope="col" style="width: 10%" ><?php echo $L['description'] ?></th>
            <th class="text-center" scope="col" style="width: 5%" ><?php echo $L['slug'] ?></th>
			
			<?php if ( $showAllSites ) : ?>
			<th class="text-center d-none d-xl-table-cell" scope="col" style="width: 10%"><a href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . ( Router::GetVariable( 'subAction' ) ? PS . Router::GetVariable( 'subAction' ) : '' ), null, false, ( array( 'sort', 'site', $order ) ) ) ?>"><?php echo $L['site'] ?><?php if ( $orderBy && ( $orderBy == 'site' ) ) : ?> <i class="expandable-table-caret fas fa-caret-<?php echo $arrow ?> fa-fw"></i></a><?php endif ?></th>

			<?php if ( $Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) && ( $Admin->GetBlog() == 0 ) && $showAll ) : ?>
			<th class="text-center d-none d-xl-table-cell" scope="col" style="width: 5%"><a href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . ( Router::GetVariable( 'subAction' ) ? PS . Router::GetVariable( 'subAction' ) : '' ), null, false, ( array( 'sort', 'blog', $order ) ) ) ?>"><?php echo $L['blog'] ?><?php if ( $orderBy && ( $orderBy == 'blog' ) ) : ?> <i class="expandable-table-caret fas fa-caret-<?php echo $arrow ?> fa-fw"></i><?php endif ?></a></th><?php endif ?>
			<?php endif ?>
	
			<?php if ( $Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) ) :					
					if ( !empty( $langs ) ) :
						foreach( $langs as $lId => $lData ) : ?>
							<th class="text-center d-none d-xl-table-cell" scope="col" style="width: 5%"><?php echo ( file_exists( FLAGS_ROOT . $lData['flagicon'] ) ? '<img src="' . $lData['flagurl'] . '" />' : $lData['title'] ) ?></th>
						<?php unset( $lId, $lData ); endforeach; endif; ?>
			<?php endif ?>
			<th class="text-center" scope="col" style="width: 5%"><?php echo $L['default'] ?></th>
			<th class="text-center d-table-cell" scope="col" style="width: 5%"><?php echo $L['exclude-from-front-page'] ?></th>
			<?php if ( $Admin->GetBlog() > 0 ) : ?>
			<th class="text-center d-table-cell" scope="col" style="width: 5%"><?php echo $L['exclude-from-blog-page'] ?></th>
			<?php endif ?>
            <th class="text-center d-none d-xl-table-cell" scope="col" style="width: 5%"><?php echo $L['count'] ?></th>

            <th class="text-center d-xl-none" scope="col" style="width: 5%"><?php echo $L['actions'] ?></th>
            </tr>
          </thead>
          <tbody class="customtable">
		 
		<?php foreach ( $cats as $cat )
		{
			BuildCategoriesTable( $cat, $showAll, $showAllSites, $langs );
			
			if ( !empty( $cat['childs'] ) )
			{
				foreach( $cat['childs'] as $child )
				{
					BuildCategoriesTable( $child, $showAll, $showAllSites, $langs, true );
				}
			}
		}
		?>
          </tbody>
        </table>

		<div class="col-md-12 p-0">
			<span class="me-2" id="categoryBulkAction">
			<select class="form-select d-inline w-auto" name="categoryBulkAction">
				<option value="0"><?php echo $L['select'] ?></option>
				<option value="update"><?php echo $L['update'] ?></option>
				<option value="delete"><?php echo $L['delete'] ?></option>
			  </select>
			  <button class="btn btn-sm btn-outline-primary align-top"><?php echo $L['apply'] ?></button>
			</span>
		</div>
		  </div>
	</form>
	
	<?php endif ?>
  
	</div>
  </div>
</div>