<div class="page-header d-flex justify-content-between align-items-right">
<ul class="list-inline text-sm">
    <li class="list-inline-item" id="navtab"><a class="text-gray-600 <?php echo ( ( Router::GetVariable( 'subAction' ) == 'pending' ) ? 'active' : 'text-reset' ) ?>" href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . '/pending' ) ?>"><i class="fas fa-clock me-2"></i> <?php echo $L['pending'] ?></a> (<?php echo $counts['postsPending'] ?>)</li>
    <li class="list-inline-item" id="navtab"><a class="text-gray-600 <?php echo ( ( Router::GetVariable( 'subAction' ) == 'deleted' ) ? 'active' : 'text-reset' ) ?>" href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . '/deleted' ) ?>"><i class="fas fa-trash-alt me-2"></i> <?php echo $L['deleted'] ?></a> (<?php echo $counts['postsDeleted'] ?>)</li>
</ul>
  <div><a class="btn btn-primary text-uppercase" href="<?php echo $Admin->GetUrl( 'add-' . $type ) ?>"> <i class="fa fa-plus me-2"> </i><?php echo __( 'add-new' ) ?></a></div>
</div>
<div class="row">
  <div class="col-12">
  
  <form action="<?php echo $Admin->GetUrl( 'posts' ) ?>" method="post" id="post" role="form">
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
									<option value="title"<?php echo ( ( $isSearch && ( $orderBy == 'title' ) ) ? ' selected' : '' ) ?>><?php echo __( 'title' ) ?></option>
									<option value="category"<?php echo ( ( $isSearch && ( $orderBy == 'category' ) ) ? ' selected' : '' ) ?>><?php echo __( 'category' ) ?></option>
									
									<option value="date"<?php echo ( ( $isSearch && ( $orderBy == 'date' ) ) ? ' selected' : '' ) ?>><?php echo __( 'date' ) ?></option>
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
	<ul class="nav nav-tabs mb-5" role="tablist">
        <li class="nav-item" id="navtab"><a class="nav-link <?php echo ( !Router::GetVariable( 'subAction' ) ? 'active' : 'text-reset' ) ?>" href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() ) ?>"><?php echo ( !$isPost ? $L['all-pages'] : $L['all-posts'] ) ?> (<?php echo $counts['postsAll'] ?>)</a></li>
		
        <li class="nav-item" id="navtab"><a class="nav-link <?php echo ( empty( $counts['postsPublished'] ) ? 'disabled' : 'text-reset' ) . ( ( Router::GetVariable( 'subAction' ) == 'published' ) ? ' active' : '' ) ?>" href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . '/published' ) ?>"><?php echo $L['published'] ?> (<?php echo $counts['postsPublished'] ?>)</a></li>
		
		<li class="nav-item" id="navtab"><a class="nav-link <?php echo ( empty( $counts['postsDraft'] ) ? 'disabled' : 'text-reset' ) . ( ( Router::GetVariable( 'subAction' ) == 'draft' ) ? ' active' : '' ) ?>" href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . '/draft' ) ?>" tabindex="-1" aria-disabled="true"><?php echo $L['drafts'] ?> (<?php echo $counts['postsDraft'] ?>)</a></li>
		
		<?php if ( !$isSelfHosted ) : ?>
		<li class="nav-item" id="navtab"><a class="nav-link <?php echo ( empty( $countSyncs ) ? 'disabled' : 'text-reset' ) . ( ( Router::GetVariable( 'subAction' ) == 'sync' ) ? ' active' : '' ) ?>" href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . '/sync' ) ?>" tabindex="-1" aria-disabled="true"><?php echo $L['sync'] ?> (<?php echo $countSyncs ?>)</a></li>
		<?php endif ?>
    </ul>
    <div class="card">
      <div class="table-responsive">
		<?php if ( empty( $data ) ) : ?>
			<div class="alert alert-warning" role="alert">
				<?php echo $L['nothing-found'] ?>
			</div>
		<?php else : ?>
		<form id="postsBulkForm" method="post" action="<?php echo $Admin->GetUrl( 'posts-bulk' ) ?>" role="form">

        <table class="table table-centered mb-0 elements-list">
          <thead class="thead-light">
            <tr>
			<?php if ( !$isOnSync ) : ?>
			<th>
                <label class="customcheckbox mb-3">
                    <input type="checkbox" id="mainCheckbox" />
                    <span class="checkmark"></span>
                </label>
            </th>
			<?php endif ?>
            <th class="border-0" scope="col"><a href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . ( Router::GetVariable( 'subAction' ) ? PS . Router::GetVariable( 'subAction' ) : '' ), null, false, ( array( 'sort', 'title', $order ) ) ) ?>"><?php echo $L['title'] ?><?php if ( $orderBy && ( $orderBy == 'title' ) ) : ?> <i class="expandable-table-caret fas fa-caret-<?php echo $arrow ?> fa-fw"></i></a><?php endif ?></th>
			
			<?php if ( $showAllSites && !$isOnSync ) : ?>
			<th class="border-0 text-center d-sm-table-cell" scope="col"><a href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . ( Router::GetVariable( 'subAction' ) ? PS . Router::GetVariable( 'subAction' ) : '' ), null, false, ( array( 'sort', 'site', $order ) ) ) ?>"><?php echo $L['site'] ?><?php if ( $orderBy && ( $orderBy == 'site' ) ) : ?> <i class="expandable-table-caret fas fa-caret-<?php echo $arrow ?> fa-fw"></i></a><?php endif ?></th>
			<?php endif ?>
			
			<?php if ( $isPost && !$isOnSync ) : ?>
			
			<th class="border-0 text-center d-none d-sm-table-cell" scope="col"><a href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . ( Router::GetVariable( 'subAction' ) ? PS . Router::GetVariable( 'subAction' ) : '' ), null, false, ( array( 'sort', 'category', $order ) ) ) ?>"><?php echo $L['category'] ?><?php if ( $orderBy && ( $orderBy == 'category' ) ) : ?> <i class="expandable-table-caret fas fa-caret-<?php echo $arrow ?> fa-fw"></i></a><?php endif ?></th>

			<?php if ( !$isOnSync && $Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) &&
					( $Admin->GetBlog() == 0 ) && $showAll ) : ?>
			<th class="border-0 d-none d-sm-block d-sm-table-cell" scope="col"><a href="<?php echo $Admin->GetUrl( $Admin->CurrentAction() . ( Router::GetVariable( 'subAction' ) ? PS . Router::GetVariable( 'subAction' ) : '' ), null, false, ( array( 'sort', 'blog', $order ) ) ) ?>"><?php echo $L['blog'] ?><?php if ( $orderBy && ( $orderBy == 'blog' ) ) : ?> <i class="expandable-table-caret fas fa-caret-<?php echo $arrow ?> fa-fw"></i></a><?php endif ?></th>
			<?php endif ?>
			
			<?php endif ?>
				
			<?php if ( !$isOnSync && $Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) ) :
					
					if ( !empty( $langs ) ) :
						foreach( $langs as $lId => $lData ) : ?>
							<th class="border-0 text-center d-none d-sm-block d-sm-table-cell" scope="col"><?php echo ( file_exists( FLAGS_ROOT . $lData['flagicon'] ) ? '<img src="' . $lData['flagurl'] . '" />' : $lData['title'] ) ?></th>
						<?php unset( $lId, $lData ); endforeach; endif; ?>
			<?php endif ?>
			
			<?php if ( !$isOnSync ) : ?>
			<th class="border-0 text-center d-sm-table-cell" scope="col"><a href="<?php echo $Admin->GetUrl( $Admin->CurrentAction(), null, false, ( array( 'sort', 'date', $order ) ) ) ?>"><?php echo $L['date'] ?><?php if ( !$orderBy || ( $orderBy && ( $orderBy == 'date' ) ) ) : ?> <i class="expandable-table-caret fas fa-caret-<?php echo $arrow ?> fa-fw"></i></a><?php endif ?></th>
			<?php endif ?>
            <th><?php echo $L['actions'] ?></th>
            </tr>
          </thead>
          <tbody class="customtable">
		  <?php
			foreach ( $data as $post ) : ?>
            <tr <?php if ( $isOnSync ) : ?>id="post-row<?php echo $post['id'] ?>"<?php endif ?>>
			<?php if ( !$isOnSync ) : ?>
              <td>
                <label class="customcheckbox">
                <input type="checkbox" class="listCheckbox" name="posts[]" value="<?php echo $post['id'] ?>" />
                <span class="checkmark"></span>
                </label>
              </td>
			  <?php endif ?>
             <td class="dt-body-center"><?php echo ( !empty( $post['pageParentId'] ) ? '— ' : '' ) ?><a href="<?php echo $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $post['id'] ) ?>"><?php echo ( empty( $post['title'] ) ? $L['empty-title'] : $post['title'] ) ?></a> <?php echo ( ( !Router::GetVariable( 'subAction' ) && ( $post['postStatus'] != 'published' ) ) ? '<strong>- ' . $L[$post['postStatus']] . '</strong>' : '' ) ?><?php if ( !empty( $post['externalUrl'] ) ) : ?> <span class="text-sm text-muted"><em><?php echo $post['externalUrl'] ?></em></span><?php endif ?></td>
			 
			<?php if ( $isPost && !$isOnSync ) : 
				
				$catName = '-';
				
				if ( !empty( $post['category']['name'] ) )
				{
					$tmp = $Admin->CustomAdminUrl( $Admin->GetSite(), $Admin->GetLang(), $Admin->GetBlog(), null, $post['category']['id'] );
					
					$catName = '<a href="' . $tmp . '">' . $post['category']['name'] . '</a>';
				}
			?>

				<td class="pt-3 d-none text-center d-none d-sm-block d-sm-table-cell"><?php echo $catName ?></td>
				
				<?php if ( $Admin->Settings()::IsTrue( 'enable_multiblog', 'site' ) && ( $Admin->GetBlog() == 0 ) && $showAll ) : ?>
				<td class="pt-3 d-none text-center d-none d-sm-block d-sm-table-cell"><?php echo ( ( !empty( $post['blog'] ) && !empty( $post['blog']['name'] ) ) ? $post['blog']['name'] : '-' ) ?></td>
				<?php endif ?>
			
			<?php endif ?>
				
				<?php 
				if ( $showAllSites && !$isOnSync ) : 
					$siteLink = ( ( $post['site']['id'] == SITE_ID ) ? $post['site']['name'] : '<a href="' . ADMIN_URI . 'posts/?site=' . $post['site']['id'] . '">' . $post['site']['name'] . '</a>' );
				?>
				<td class="pt-3 d-none text-center d-none d-sm-block d-sm-table-cell"><?php echo $siteLink ?></td>
				<?php endif ?>
				
				<?php if ( !$isOnSync && $Admin->Settings()::IsTrue( 'enable_multilang', 'site' ) && !empty( $langs ) ) :
				
						$translations = $post['trans'];

						foreach( $langs as $lId => $lData ) :
							$icon = '<i class="fa fa-edit"></i>';
							$title = sprintf( $L['edit-s-translation'], $lData['title'] );
							
							$editUri = '#';
							
							//This is the parent post, has always an edit link
							if ( $post['language']['id'] == $lData['id'] )
							{
								$title = $L['edit-post'];
								$editUri = $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $post['id'] );
							}
							
							else
							{
								if ( !empty( $translations ) )
								{
									if ( isset( $translations[$lId] ) )
										$editUri = $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $translations[$lId]['id'] );
									
									//Translations can only be added to parent page, so we must check that we have its ID
									elseif ( isset( $translations[$Admin->DefaultLang()['code']] ) )
									{
										$icon = '<i class="fa fa-plus-circle"></i>';
										$title = sprintf( $L['add-s-translation'], $lData['title'] );
										$editUri = ADMIN_URI . 'add-translation' . PS . 'id' . PS . $translations[$Admin->DefaultLang()['code']]['id'] . PS . '?';
										
										$editUri .= ( $Admin->IsDefaultSite() ? '' : 'site=' . $Admin->GetSite() . ';' );
										
										$editUri .= 'lang=' . $lData['id'];
									}
									
									else
									{
										$icon = '<i class="fa fa-plus-circle"></i>';
										$title = sprintf( $L['add-s-translation'], $lData['title'] );
										$editUri = ADMIN_URI . 'add-translation' . PS . 'id' . PS . $post['id'] . PS . '?';
										
										$editUri .= ( $Admin->IsDefaultSite() ? '' : 'site=' . $Admin->GetSite() . ';' );
										
										$editUri .= 'lang=' . $lData['id'];
									}
								}
								
								else
								{
									$icon = '<i class="fa fa-plus-circle"></i>';
									$title = sprintf( $L['add-s-translation'], $lData['title'] );
									$editUri = ADMIN_URI . 'add-translation' . PS . 'id' . PS . $post['id'] . PS . '?';
										
									$editUri .= ( $Admin->IsDefaultSite() ? '' : 'site=' . $Admin->GetSite() . ';' );
										
									$editUri .= 'lang=' . $lData['id'];
								}
							}
				?>
					<td class="pt-3 d-none text-center d-none d-sm-block d-sm-table-cell"><a title="<?php echo $title ?>" href="<?php echo $editUri ?>"><?php echo $icon ?></a></td>
				<?php unset( $lId, $lData ); endforeach; endif; ?>
				
				<?php if ( !$isOnSync ) : ?>
				<td><p style="font-size: 0.8em" class="m-0 text-center text-muted"><?php echo ( !empty( $post['added']['time'] ) ? $post['added']['time'] : '' ) ?></p></td>
				<?php endif ?>
				
				<?php if ( !$isOnSync ) : ?>
				<td class="table-action">
				<?php if ( Router::GetVariable( 'subAction' ) != 'deleted' ) : ?>
				<a href="<?php echo $post['postUrl'] ?>" target="_blank" class="action-icon" title="<?php echo $L['view'] ?>"> <i class="bi bi-eye"></i></a>
				<?php endif ?>
				
                <a href="<?php echo $Admin->GetUrl( 'edit-post' . PS . 'id' . PS . $post['id'] ) ?>" class="action-icon" title="<?php echo $L['edit'] ?>"> <i class="bi bi-pencil"></i></a>
				
				<a href="javascript: void(0);" title="<?php echo $L['quick-edit'] ?>" data-toggle="modal" data-target="#editPostModal" id="quickEdit" data-id="<?php echo $post['id'] ?>"> <i class="bi bi-postcard"></i></a>
				
				<?php if ( ( Router::GetVariable( 'subAction' ) != 'deleted' ) && ( $Admin->MultiBlog() || $Admin->MultiSite() ) ) : ?>
				<a href="javascript: void(0);" title="<?php echo $L['clone'] ?>" class="clonePost" data-id="<?php echo $post['id'] ?>"> <i class="bi bi-clipboard"></i></a>
				<?php endif ?>
				
				<?php if ( Router::GetVariable( 'subAction' ) == 'deleted' ) : ?>
					<a href="<?php echo $Admin->GetUrl( 'restore-post' . PS . 'id' . PS . $post['id'] ) ?>" id="restorePost" title="<?php echo $L['restore'] ?>" class="action-icon" role="button" onclick="return confirm_alert2()"><i class="bi bi-arrow-counterclockwise"></i></a>
					
					<a href="<?php echo $Admin->GetUrl( 'remove-post' . PS . 'id' . PS . $post['id'] ) ?>" id="deletePost" title="<?php echo $L['delete-permanently'] ?>" class="action-icon" role="button" onclick="return confirm_alert()"><i class="bi bi-trash"></i></a>
				
				<?php else : ?>
					<a href="<?php echo $Admin->GetUrl( 'delete-post' . PS . 'id' . PS . $post['id'] ) ?>" id="deletePost" title="<?php echo $L['delete'] ?>" class="action-icon" role="button" onclick="return confirm_alert2()"><i class="bi bi-trash"></i></a>
				<?php endif ?>
              </td>
			  <?php else : ?>
			  <td class="table-action"><a href="javascript: void(0);" title="<?php echo $L['quick-edit'] ?>" class="quickSearch" data-id="<?php echo $post['id'] ?>" data-url="<?php echo $post['url'] ?>"> <i class="bi bi-postcard"></i></a></td>
			  <?php endif ?>
            </tr>
		<?php endforeach ?>
          </tbody>
        </table>

		<div class="col-md-12">
			<span class="me-2" id="postsBulkAction">
			<?php if ( !$isOnSync ) : ?>
			<select class="form-select form-select-sm d-inline w-auto" name="postsBulkAction">
				<option><?php echo $L['bulk-actions'] ?></option>
				<option><?php echo $L['delete'] ?></option>
			  </select>
			  <button class="btn btn-sm btn-outline-primary align-top"><?php echo $L['apply'] ?></button>
			<?php endif ?>
			</span>
		
		<?php if ( !$isOnSync ) : ?>
		<nav aria-label="Page navigation">
		  <ul class="pagination justify-content-center">
		<?php $url = $currentUrl . 'page' . PS;
		
		if ( Paginator::NumberOfPages() > 1 ) : ?>
			<li class="page-item <?php echo ( Paginator::HasNewer() ? '' : 'disabled' ) ?>"><a class="page-link" href="<?php echo Paginator::FirstPageUrl() ?>">&lsaquo;&lsaquo;</a></li>
			
			<li class="page-item <?php echo ( Paginator::HasNewer() ? '' : 'disabled' ) ?>"><a class="page-link" href="<?php echo Paginator::NewerPageUrl() ?>"><?php echo __( 'previous' ) ?></a></li>
			<?php 
				for ( $i = ( Paginator::CurrentPage() - 5 ); $i <= ( Paginator::CurrentPage() + 5 ); $i++) :
					if ( ( $i >= 1 ) && ( $i <= Paginator::NumberOfPages() ) ) : 
						if ( $i == Paginator::CurrentPage() ) : ?>
							<li class="page-item active"><span class="page-link"><?php echo $i ?><span class="sr-only">(<?php echo __( 'current' ) ?>)</span></span></li>
						<?php else : ?>
							<li class="page-item"><a class="page-link" href="<?php echo Paginator::PageNumUri( $i ) ?>"><?php echo $i ?></a></li>
						<?php endif ?>
					<?php endif ?>
			<?php endfor ?>
			<li class="page-item <?php echo ( Paginator::HasOlder() ? '' : 'disabled' ) ?>"><a class="page-link" href="<?php echo Paginator::OlderPageUrl() ?>"><?php echo $L['next'] ?></a></li>
			<li class="page-item <?php echo ( Paginator::HasOlder() ? '' : 'disabled' ) ?>"><a class="page-link" href="<?php echo Paginator::LastPageUrl() ?>">&rsaquo;&rsaquo;</a></li>
		<?php endif ?>
		  </ul>
		</nav>
		<?php endif ?>
		</div>
		
		<input type="hidden" name="action" value="<?php echo $type ?>" />
		<input type="hidden" name="subaction" value="<?php echo Router::GetVariable( 'subAction' ) ?>" />
		<?php if ( $isOnSync ) : ?>
			<input type="hidden" id="postsIds" name="filesToEdit[]" value="" />
		<?php endif ?>
	</form>
	<?php endif ?>
    </div>
	</div>
  </div>
</div>