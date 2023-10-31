<nav class="main-header navbar navbar-expand navbar-dark navbar-light">
	<ul class="navbar-nav">
		<li class="nav-item">
			<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
		</li>
		<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'view-site' ) ) : ?>
		<li class="nav-item">
			<a class="nav-link" target="_blank" href="<?php echo $Admin->SiteUrl() ?>">
				<span class="d-none d-xl-block"><i class="fa fa-home fa-fw"></i> <?php echo __( 'visit-site' ) ?></span>
				<span class="d-xl-none"><i class="fas fa-home fa-fw"></i></span>
			</a>
		</li>
		<?php endif ?>
		
		<?php if ( IsAllowedTo( 'admin-site' ) ) : ?>
		<li class="nav-item d-none d-xl-block">
			<a class="nav-link" href="<?php echo $Admin->GetUrl( 'delete-cache' ) ?>">
				<span class="d-none d-xl-block"><i class="fas fa-eraser fa-fw"></i> <?php echo $L['delete-cache'] ?> <i class="fa fa-angle-down"></i></span>
				<span class="d-xl-none"><i class="fas fa-eraser"></i></span>
			</a>
		</li>
		<?php endif ?>

		<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'create-new-posts' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'manage-languages' ) || IsAllowedTo( 'manage-blogs' ) || IsAllowedTo( 'manage-sites' ) || IsAllowedTo( 'manage-members' ) ):
		?>
        <li class="nav-item dropdown">
			<a class="nav-link" data-toggle="dropdown" href="#">
				<span class="d-none d-md-block"><i class="fa fa-plus fa-fw"></i> <?php echo $L['new'] ?> <i class="fa fa-angle-down"></i></span>
                <span class="d-block d-md-none"><i class="fa fa-plus"></i></span>
			</a>
			
			<ul class="dropdown-menu dropdown-menu-sm dropdown-menu-center" aria-labelledby="navbarDropdown">
				<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'create-new-posts' ) || IsAllowedTo( 'manage-posts' ) ) : ?>
				<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-post' ) ?>"><?php echo $L['post'] ?></a></li>
				<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-page' ) ?>"><?php echo $L['page'] ?></a></li>
				<?php endif ?>

				<?php if ( $Admin->MultiLang() && $Admin->SiteIsSelfHosted() && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-languages' ) ) ) : ?>
					<li>
						<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-lang' ) ?>"><?php echo $L['lang'] ?></a>
					</li>
				<?php endif ?>

				<?php if ( $Admin->MultiBlog() && $Admin->SiteIsSelfHosted() && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-blogs' ) ) ) : ?>
					<li>
						<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-blog' ) ?>"><?php echo $L['blog'] ?></a>
					</li>
				<?php endif ?>

				<?php if ( MULTISITE && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-sites' ) ) ) : ?>
					<li>
						<a class="dropdown-item" href="<?php echo ADMIN_URI ?>add-site/"><?php echo $L['site'] ?></a>
					</li>
				<?php endif ?>
				
				<?php if ( $Admin->SiteIsSelfHosted() && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-members' ) ) ) : ?>
					<li>
						<a class="dropdown-item" href="<?php echo ADMIN_URI ?>add-user/"><?php echo $L['user'] ?></a>
					</li>
				<?php endif ?>
            </ul>
        </li>
		<?php endif ?>

		<?php if ( MULTISITE && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-sites' ) ) ) : 
				$sites = $Admin->Sites();
		?>
		<li class="nav-item dropdown">
			<a class="nav-link" data-toggle="dropdown" href="#">
				<span class="d-none d-xl-block"><i class="fas fa-sitemap fa-fw"></i> <?php echo $L['sites'] ?> <i class="fa fa-angle-down"></i></span>
				<span class="d-xl-none"><i class="fas fa-sitemap"></i></span>
			</a>
            
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li>
					<a class="dropdown-item" href="<?php echo ADMIN_URI ?>add-site/"><?php echo $L['add-new-site'] ?></a>
				</li>
				<div class="dropdown-divider"></div>
				<li>
					<a class="dropdown-item" href="<?php echo ADMIN_URI ?>"><?php echo ( ( $Admin->GetSite() == SITE_ID ) ? '<i class="fa fa-arrow-circle-right fa-fw"></i>' : '' ) ?> <?php echo $Admin->DefaultSiteName() ?></a>
				</li>
				<?php
				if ( !empty( $sites ) ) :
					foreach ( $sites as $singeSite ) :
				?>
					<li>
						<a class="dropdown-item" href="<?php echo ADMIN_URI ?>?site=<?php echo $singeSite['id'] ?>"><?php echo ( ( $Admin->GetSite() == $singeSite['id'] ) ? '<i class="fa fa-arrow-circle-right fa-fw"></i>' : '' ) ?> <?php echo $singeSite['title'] ?></a>
					</li>
				<?php unset( $singeSite ); endforeach; endif; ?>
			</ul>
        </li>
		<?php endif ?>

		<?php if ( $Admin->MultiLang() && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-languages' ) ) ) : ?>
			<li class="nav-item dropdown">
				<a class="nav-link" data-toggle="dropdown" href="#">
					<span class="d-none d-md-block"><i class="fas fa-language fa-fw"></i> <?php echo $L['langs'] ?> <i class="fa fa-angle-down"></i></span>
                    <span class="d-block d-md-none"><i class="fa fa-language"></i></span>
                </a>
                
				<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
					<li>
						<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'langs' ) ?>"><?php echo $L['langs'] ?></a>
					</li>
					<li>
						<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'lang-settings' ) ?>"><?php echo $L['settings'] ?></a>
					</li>
					
					<li>
						<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-lang' ) ?>"><?php echo $L['add-new-language'] ?></a>
					</li>
					
					<div class="dropdown-divider"></div>
					
					<li>
						<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'lang', $Admin->DefaultLang()['id'], true ) ?>"><?php echo ( ( $Admin->GetLang() == $Admin->DefaultLang()['id'] ) ? '<i class="fa fa-arrow-circle-right fa-fw"></i>' : '' ) ?> <?php echo $Admin->DefaultLang()['title'] ?></a>
					</li>
					
					<?php 
					$Langs = $Admin->OtherLangs(); 
					if ( !empty( $Langs ) ) :
						foreach( $Langs as $lId => $lData ) :
					?>
						<li>
							<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'lang', $lId, true ) ?>"><?php echo ( ( $Admin->GetLang() == $lId ) ? '<i class="fa fa-arrow-circle-right fa-fw"></i>' : '' ) ?> <?php echo $lData['lang']['title'] ?></a>
						</li>
					<?php unset( $lId, $lData ); endforeach; unset( $Langs ); endif; ?>
                </ul>
            </li>
			<?php endif ?>

			<?php if ( $Admin->MultiBlog() && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-blogs' ) ) ) : ?>
			<li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <span class="d-none d-md-block"><i class="fas fa-boxes fa-fw"></i> <?php echo $L['blogs'] ?> <i class="fa fa-angle-down"></i></span>
                    <span class="d-block d-md-none"><i class="fa fa-boxes"></i></span>
                </a>
                
				<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
					<li>
						<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-blog' ) ?>"><?php echo $L['add-new-blog'] ?></a>
					</li>
					
					<?php
					$Blogs = $Admin->GetBlogs();
					if ( !empty( $Blogs ) ) :
					?>
					<div class="dropdown-divider"></div>
					<?php foreach( $Blogs as $bId => $bData ) :
					?>
						<li>
							<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'blog', $bData['id_blog'], true ) ?>"><?php echo ( ( $Admin->GetBlog() == $bData['id_blog'] ) ? '<i class="fa fa-arrow-circle-right fa-fw"></i>' : '' ) ?> <?php echo $bData['name'] ?></a>
						</li>
					<?php unset( $bData ); endforeach; unset( $Blogs ); endif; ?>
                </ul>
            </li>
			<?php endif ?>
			
			
			<li class="nav-item dropdown d-block d-md-none">
				<a class="nav-link" data-toggle="dropdown" href="#">
					<i class="fa fa-user"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-end user-dd animated" aria-labelledby="navbarDropdown">
				<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-own-account' ) || IsAllowedTo( 'manage-members' ) ) :?>
					<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'edit-user' . PS . 'id' . PS . $Admin->UserID() ) ?>">
						<i class="ti-settings me-1 ms-1"></i>
						Account Setting
					</a>
					<div class="dropdown-divider"></div>
				<?php endif ?>
					<a class="dropdown-item" href="<?php echo SITE_URL . 'logout' . PS ?>">
						<i class="fa fa-power-off me-1 ms-1"></i>
						<?php echo __( 'logout' ) ?>
					</a>
				</ul>
			</li>
	</ul>

	<ul class="navbar-nav ml-auto">
	<?php /*
		<li class="nav-item dropdown">
			<a class="nav-link" data-toggle="dropdown" href="#">
				<i class="far fa-comments"></i>
				<span class="badge badge-danger navbar-badge">3</span>
			</a>
			<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
				<a href="#" class="dropdown-item">
					<div class="media">
						<img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
						<div class="media-body">
							<h3 class="dropdown-item-title">
								Brad Diesel
								<span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
							</h3>
							<p class="text-sm">Call me whenever you can...</p>
							<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
						</div>
					</div>
				</a>
				
				<div class="dropdown-divider"></div>
				<a href="#" class="dropdown-item">
				<div class="media">
				<img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
				<div class="media-body">
				<h3 class="dropdown-item-title">
				John Pierce
				<span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
				</h3>
				<p class="text-sm">I got your message bro</p>
				<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
				</div>
				</div>

				</a>
				<div class="dropdown-divider"></div>
				<a href="#" class="dropdown-item">

<div class="media">
<img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
<div class="media-body">
<h3 class="dropdown-item-title">
Nora Silvester
<span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
</h3>
<p class="text-sm">The subject goes here</p>
<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
</div>
</div>

</a>
<div class="dropdown-divider"></div>
<a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
</div>
</li>*/?>

<?php BuildLogNavHtml() ?>

<li class="nav-item dropdown d-none d-md-block">
    <a class="nav-link dropdown-toggle text-muted" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
		<!--<img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">-->
		<i class="fas fa-user"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end user-dd animated" aria-labelledby="navbarDropdown">
	<!--
        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-user me-1 ms-1"></i>
            My Profile
		</a>
        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-wallet me-1 ms-1"></i>
            My Balance
		</a>
        <a class="dropdown-item" href="javascript:void(0)"><i class="ti-email me-1 ms-1"></i>
            Inbox
		</a>
        
		<div class="dropdown-divider"></div>
	-->
		<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-own-account' ) || IsAllowedTo( 'manage-members' ) ):
		?>
		<a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'edit-user' . PS . 'id' . PS . $Admin->UserID() ) ?>">
			<i class="ti-settings me-1 ms-1"></i>
			Account Setting
		</a>
        <div class="dropdown-divider"></div>
		<?php endif ?>
        <a class="dropdown-item" href="<?php echo SITE_URL . 'logout' . PS ?>">
			<i class="fa fa-power-off me-1 ms-1"></i>
			<?php echo __( 'logout' ) ?>
		</a>
    </ul>
</li>
</ul>
</nav>