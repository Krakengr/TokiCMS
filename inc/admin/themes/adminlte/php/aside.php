<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<a href="<?php echo ( ( MULTISITE && ( $Admin->GetSite() != SITE_ID ) ) ? $Admin->GetUrl() : ADMIN_URI ) ?>" class="brand-link">
		<span class="brand-text font-weight-light"><?php echo $Admin->SiteName() ?></span>
	</a>

	<div class="sidebar">
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
				<?php foreach ( $Admin->Menu() as $id => $nav ) :
					if ( !empty( $nav['items'] ) ) :
						foreach( $nav['items'] as $_id => $item ) : 
							if ( !$item['show'] )
								continue;
				?>
				<?php if ( !empty( $item['child'] ) ) : ?>
				<!--<li class="nav-header"><?php echo strtoupper( $item['title'] ) ?></li>-->
				<?php endif ?>
				<li class="nav-item <?php echo ( $item['collapsed'] ? 'menu-open' : '' ) ?>">
					<a href="<?php echo $item['href'] ?>" class="nav-link <?php echo ( $item['collapsed'] ? 'active' : '' ) . ( ( empty( $item['child'] ) && $item['current'] ) ? 'active' : '' ) ?>">
						<?php echo $item['icon'] ?>
						<p>
							<?php echo $item['title'] ?>
							<?php echo ( empty( $item['child'] ) ? '' : '<i class="right fas fa-angle-left"></i>' ) ?>
							<?php if ( isset( $item['num-info'] ) && is_numeric( $item['num-info'] ) && ( $item['num-info'] > 0 ) ) : ?>
								<span class="badge badge-info right"><?php echo $item['num-info'] ?></span>
							<?php endif ?>
						</p>
					</a>
					<?php if ( !empty( $item['child'] ) ) : ?>
					<ul class="nav nav-treeview">
					<?php foreach( $item['child'] as $childId => $child ) : 
							if ( !$child['show'] )
								continue; 
					?>
						<li class="nav-item">
							<a href="<?php echo $child['href'] ?>" class="nav-link <?php echo ( $child['current'] ? ' active' : '' ) ?>">
								<i class="far fa-circle nav-icon <?php echo ( $child['current'] ? 'text-warning' : 'text-info' ) ?>"></i>
								<p><?php echo $child['title'] ?></p>
							</a>
						</li>
					<?php endforeach ?>
					</ul>
					<?php endif ?>
				</li>
			<?php endforeach ?>
			<?php endif ?>
			<?php endforeach ?>

			</ul>
		</nav>
	</div>
</aside>