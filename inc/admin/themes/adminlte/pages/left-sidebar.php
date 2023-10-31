<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
    
		<!-- Sidebar navigation-->
        <nav class="sidebar-nav">
			<?php foreach ( $Admin->Menu() as $id => $nav ) :
					if ( !empty( $nav['items'] ) ) :
						foreach( $nav['items'] as $_id => $item ) : 
							if ( !$item['show'] )
								continue;
			?><ul id="sidebarnav" class="pt-4">
				<li class="sidebar-item"> <a class="sidebar-link <?php echo ( empty( $item['child'] ) ? '' : 'has-arrow' ) ?> waves-effect waves-dark" href="<?php echo $item['href'] ?>" aria-expanded="<?php echo ( $item['collapsed'] ? 'true' : 'false' ) ?>"><?php echo $item['icon'] ?><span class="hide-menu"><?php echo $item['title'] ?></span></a>
				<?php if ( !empty( $item['child'] ) ) : ?>
					<ul aria-expanded="<?php echo ( $item['collapsed'] ? 'true' : 'false' ) ?>" class="collapse first-level">
						<?php foreach( $item['child'] as $childId => $child ) : 
								if ( !$child['show'] )
									continue; 
						?><li class="sidebar-item"><a href="<?php echo $child['href'] ?>" class="sidebar-link<?php echo ( $child['current'] ? ' active' : '' ) ?>"><?php echo $child['icon'] ?><span class="hide-menu"> <?php echo $child['title'] ?></span></a></li>
						
						<?php endforeach ?>
					</ul>
					<?php endif ?>
				</li>
			</ul>
			<?php endforeach ?>
			<?php endif ?>
			<?php endforeach ?>
		</nav>
		<!-- End Sidebar navigation -->
    </div>
	<!-- End Sidebar scroll-->
</aside>