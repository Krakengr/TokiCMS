<header class="topbar" data-navbarbg="skin5">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header" data-logobg="skin5">
			<a class="navbar-brand" href="<?php echo $Admin->GetUrl() ?>"><?php echo $Admin->SiteName() ?></a>
            <!-- ============================================================== -->
            <!-- Toggle which is visible on mobile only -->
            <!-- ============================================================== -->
            <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
				<i class="ti-menu ti-close"></i>
			</a>
        </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->				
                    <ul class="navbar-nav float-start me-auto">
                        <li class="nav-item d-none d-lg-block"><a
                                class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)"
                                data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>
						<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'view-site' ) ) : ?>
						<li class="nav-item d-none d-lg-block"><a class="nav-link" target="_blank" href="<?php echo $Admin->SiteUrl() ?>"><i class="fa fa-home fa-fw"></i> <?php echo $L['visit-site'] ?></a></li><?php endif ?>
						
						<?php if ( IsAllowedTo( 'admin-site' ) ) : ?>
						<li class="nav-item d-none d-lg-block"><a class="nav-link" href="<?php echo $Admin->GetUrl( 'delete-cache' ) ?>"><i class="fa fa-eraser"></i> <?php echo $L['delete-cache'] ?></a></li><?php endif ?>
						
						<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'create-new-posts' ) || IsAllowedTo( 'manage-posts' ) || IsAllowedTo( 'manage-languages' ) 
									|| IsAllowedTo( 'manage-blogs' ) || IsAllowedTo( 'manage-sites' ) || IsAllowedTo( 'manage-members' ) ):
						?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-none d-md-block"><i class="fa fa-plus"></i> <?php echo $L['new'] ?> <i class="fa fa-angle-down"></i></span>
                                <span class="d-block d-md-none"><i class="fa fa-plus"></i></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'create-new-posts' ) || IsAllowedTo( 'manage-posts' ) ) : ?>
								<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-post' ) ?>"><?php echo $L['post'] ?></a></li>
								<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-page' ) ?>"><?php echo $L['page'] ?></a></li>
							<?php endif ?>

							<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-languages' ) ) : ?>
								<?php if ( $Admin->MultiLang() ) : ?><li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-lang' ) ?>"><?php echo $L['lang'] ?></a></li><?php endif ?>
							<?php endif ?>
							
							<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-blogs' ) ) : ?>
								<?php if ( $Admin->MultiBlog() ) : ?><li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-blog' ) ?>"><?php echo $L['blog'] ?></a></li><?php endif ?>
							<?php endif ?>
							
							<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-sites' ) ) : ?>
								<?php if ( MULTISITE ) : ?><li><a class="dropdown-item" href="<?php echo ADMIN_URI ?>add-site/"><?php echo $L['site'] ?></a></li><?php endif ?>
							<?php endif ?>
							<?php if ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-members' ) ) : ?>
								<li><a class="dropdown-item" href="<?php echo ADMIN_URI ?>add-user/"><?php echo $L['user'] ?></a></li>
							<?php endif ?>
                            </ul>
                        </li>
						<?php endif ?>
						
						<?php if ( MULTISITE && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-sites' ) ) ) : 
							$sites = $Admin->Sites();
						?>
						<li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-none d-md-block"><i class="fas fa-sitemap fa-fw"></i> <?php echo $L['sites'] ?> <i class="fa fa-angle-down"></i></span>
                                <span class="d-block d-md-none"><i class="fa fa-plus"></i></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<li><a class="dropdown-item" href="<?php echo ADMIN_URI ?>add-site/"><?php echo $L['add-new-site'] ?></a></li>
							<li><hr class="dropdown-divider"></li>
							<li><a class="dropdown-item" href="<?php echo ADMIN_URI ?>"><?php echo ( ( $Admin->GetSite() == SITE_ID ) ? '<i class="fa fa-arrow-circle-right fa-fw"></i>' : '' ) ?> <?php echo $Admin->DefaultSiteName() ?></a></li>
							<?php
							if ( !empty( $sites ) ) :
									foreach ( $sites as $singeSite ) : ?>
									<li><a class="dropdown-item" href="<?php echo ADMIN_URI ?>?site=<?php echo $singeSite['id'] ?>"><?php echo ( ( $Admin->GetSite() == $singeSite['id'] ) ? '<i class="fa fa-arrow-circle-right fa-fw"></i>' : '' ) ?> <?php echo $singeSite['title'] ?></a></li>
							<?php unset( $singeSite ); endforeach; endif; ?>
                            </ul>
                        </li>
						<?php endif ?>
						
						<?php if ( $Admin->MultiLang() && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-languages' ) ) ) : ?>
						<li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-none d-md-block"><i class="fas fa-language fa-fw"></i> <?php echo $L['langs'] ?> <i class="fa fa-angle-down"></i></span>
                                <span class="d-block d-md-none"><i class="fa fa-plus"></i></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'langs' ) ?>"><?php echo $L['langs'] ?></a></li>
							<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'lang-settings' ) ?>"><?php echo $L['settings'] ?></a></li>
							<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-lang' ) ?>"><?php echo $L['add-new-language'] ?></a></li>
							<li><hr class="dropdown-divider"></li>
							<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'lang', $Admin->DefaultLang()['id'], true ) ?>"><?php echo ( ( $Admin->GetLang() == $Admin->DefaultLang()['id'] ) ? '<i class="fa fa-arrow-circle-right fa-fw"></i>' : '' ) ?> <?php echo $Admin->DefaultLang()['title'] ?></a></li>
							<?php $Langs = $Admin->OtherLangs(); 
								if ( !empty( $Langs ) ) : ?>
							<?php foreach( $Langs as $lId => $lData ) : ?>
								<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'lang', $lId, true ) ?>"><?php echo ( ( $Admin->GetLang() == $lId ) ? '<i class="fa fa-arrow-circle-right fa-fw"></i>' : '' ) ?> <?php echo $lData['lang']['title'] ?></a></li>
							<?php unset( $lId, $lData ); endforeach; unset( $Langs ); endif; ?>
                            </ul>
                        </li>
						<?php endif ?>
						
						<?php if ( $Admin->MultiBlog() && ( IsAllowedTo( 'admin-site' ) || IsAllowedTo( 'manage-blogs' ) ) ) : ?>
						<li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-none d-md-block"><i class="fas fa-boxes fa-fw"></i> <?php echo $L['blogs'] ?> <i class="fa fa-angle-down"></i></span>
                                <span class="d-block d-md-none"><i class="fa fa-plus"></i></span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'add-blog' ) ?>"><?php echo $L['add-new-blog'] ?></a></li>
							<?php $Blogs = $Admin->GetBlogs();
								if ( !empty( $Blogs ) ) : ?>
								<li><hr class="dropdown-divider"></li>
							<?php foreach( $Blogs as $bId => $bData ) : ?>
								<li><a class="dropdown-item" href="<?php echo $Admin->GetUrl( 'blog', $bData['id_blog'], true ) ?>"><?php echo ( ( $Admin->GetBlog() == $bData['id_blog'] ) ? '<i class="fa fa-arrow-circle-right fa-fw"></i>' : '' ) ?> <?php echo $bData['name'] ?></a></li>
							<?php unset( $bData ); endforeach; unset( $Blogs ); endif; ?>
                            </ul>
                        </li>
						<?php endif ?>
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -- >
                        <li class="nav-item search-box"> <a class="nav-link waves-effect waves-dark"
                                href="javascript:void(0)"><i class="ti-search"></i></a>
                            <form class="app-search position-absolute">
                                <input type="text" class="form-control" placeholder="Search &amp; enter"> <a
                                    class="srh-btn"><i class="ti-close"></i></a>
                            </form>
                        </li>-->
                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-end">
                        <!-- ============================================================== -->
                        <!-- Comment -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-bell font-24"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End Comment -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- Messages -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" id="2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                 <i class="font-24 mdi mdi-comment-processing"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end mailbox animated bounceInDown" aria-labelledby="2">
                                <ul class="list-style-none">
                                    <li>
                                        <div class="">
                                            <!-- Message -->
                                            <a href="javascript:void(0)" class="link border-top">
                                                <div class="d-flex no-block align-items-center p-10">
                                                    <span class="btn btn-success btn-circle"><i
                                                            class="ti-calendar"></i></span>
                                                    <div class="ms-2">
                                                        <h5 class="mb-0">Event today</h5>
                                                        <span class="mail-desc">Just a reminder that event</span>
                                                    </div>
                                                </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)" class="link border-top">
                                                <div class="d-flex no-block align-items-center p-10">
                                                    <span class="btn btn-info btn-circle"><i
                                                            class="ti-settings"></i></span>
                                                    <div class="ms-2">
                                                        <h5 class="mb-0">Settings</h5>
                                                        <span class="mail-desc">You can customize this template</span>
                                                    </div>
                                                </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)" class="link border-top">
                                                <div class="d-flex no-block align-items-center p-10">
                                                    <span class="btn btn-primary btn-circle"><i
                                                            class="ti-user"></i></span>
                                                    <div class="ms-2">
                                                        <h5 class="mb-0">Pavan kumar</h5>
                                                        <span class="mail-desc">Just see the my admin!</span>
                                                    </div>
                                                </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)" class="link border-top">
                                                <div class="d-flex no-block align-items-center p-10">
                                                    <span class="btn btn-danger btn-circle"><i
                                                            class="fa fa-link"></i></span>
                                                    <div class="ms-2">
                                                        <h5 class="mb-0">Luanch Admin</h5>
                                                        <span class="mail-desc">Just see the my new admin!</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </ul>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End Messages -->
                        <!-- ============================================================== -->

                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <!--<img src="<?php echo HTML_ADMIN_PATH_THEME ?>assets/images/users/1.jpg" alt="user" class="rounded-circle" width="31">-->
								<i class="fas fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end user-dd animated" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="javascript:void(0)"><i class="ti-user me-1 ms-1"></i>
                                    My Profile</a>
                                <a class="dropdown-item" href="javascript:void(0)"><i class="ti-wallet me-1 ms-1"></i>
                                    My Balance</a>
                                <a class="dropdown-item" href="javascript:void(0)"><i class="ti-email me-1 ms-1"></i>
                                    Inbox</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)"><i
                                        class="ti-settings me-1 ms-1"></i> Account Setting</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo SITE_URL . 'logout' . PS ?>"><i
                                        class="fa fa-power-off me-1 ms-1"></i> Logout</a>
                                <div class="dropdown-divider"></div>
                                <div class="ps-4 p-10"><a href="javascript:void(0)"
                                        class="btn btn-sm btn-success btn-rounded text-white">View Profile</a></div>
                            </ul>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>