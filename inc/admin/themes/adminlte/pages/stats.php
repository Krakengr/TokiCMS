<?php aggregate_old_data() ?>

<div class="row">
	<div class="col-lg-6">
	
	<div class="card">
		<div class="card-header border-0">
			<div class="d-flex justify-content-between">
				<h3 class="card-title"><?php echo __( 'visits-stats' ) ?> [<?php echo $Stats['dateLabel'] ?>]</h3>
			</div>
		</div>
		<div class="card-body">
			<div class="position-relative mb-4">
				<canvas id="visits-chart" height="200"></canvas>
			</div>
			
			<div class="d-flex flex-row justify-content-end">
				<span class="mr-2">
					<i class="fas fa-square text-info"></i> <?php echo __( 'hits' ) ?>
				</span>
				<span>
					<i class="fas fa-square text-gray"></i> <?php echo __( 'visits' ) ?>
				</span>
			</div>
		</div>
	</div>
	
	<div class="card">
		<div class="card-header border-0">
				<h3 class="card-title"><?php echo __( 'browser-info' ) ?></h3>
		</div>
		<div class="card-body">
			<div class="position-relative mb-4">
				<canvas id="browseChart" height="200"></canvas>
			</div>
			<div class="d-flex flex-row justify-content-end">
		<?php if ( !empty( $Stats['browserData'] ) ) : 
				foreach ( $Stats['browserData'] as $id => $browser ) : ?>
				<span class="mr-2">
					<i class="fas fa-square" style="color: <?php echo $browser['color'] ?>"></i> <?php echo $browser['label'] ?>
				</span>
				<?php endforeach ?>
		<?php endif ?>
			</div>
		</div>
	</div>

	<div class="card">
		<div class="card-header border-0">
			<h3 class="card-title"><?php echo __( 'content' ) ?></h3>
		</div>
		
		<div class="card-body table-responsive p-0">
			<table class="table table-striped table-valign-middle">
				<thead>
					<tr>
						<th><?php echo __( 'page' ) ?></th>
						<th><?php echo __( 'hits' ) ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( !empty( $Stats['pages'] ) ) : 
					foreach( $Stats['pages'] as $page => $hits ) : ?>
					<tr>
						<td><?php echo $page ?></td>
						<td><?php echo $hits ?></td>
					</tr>
				<?php endforeach ?>
				<?php else : ?>
					<div class="alert alert-warning">
						<?php echo __( 'nothing-found' ) ?>
					</div>
				<?php endif ?>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="card">
		<div class="card-header border-0">
			<h3 class="card-title"><?php echo __( 'language' ) ?></h3>
		</div>
		
		<div class="card-body table-responsive p-0">
			<?php table_total( 'language', $Stats ) ?>
		</div>
	</div>
</div>

<div class="col-lg-6">

	<div class="card">
		<div class="card-header border-0">
			<h3 class="card-title"><?php echo __( 'summary' ) ?> [<?php echo $Stats['dateLabel'] ?>]</h3>
		</div>
		
		<?php
		if( !isset( $Stats['pages'] ) || empty( $Stats['pages'] ) )
		{
			$hits = $visits = $ips = '0';
		}
		else
		{
			$hits = format_number( array_sum( $Stats['pages'] ), 0 );	// total page hits
			$visits = format_number( array_sum( $Stats['visits']['remote_ip'] ), 0 );
			$ips = format_number( sizeof( $Stats['visits']['remote_ip'] ), 0 );
		}
		?>
		<div class="card-body">
			<div class="d-flex justify-content-between align-items-center border-bottom mb-3">
				<p class="text-success text-md">
					<?php echo __( 'hits' ) ?>
				</p>
				<p class="d-flex flex-column text-right">
					<span class="font-weight-bold text-muted">
						<?php echo $hits ?>
					</span>
				</p>
			</div>

			<div class="d-flex justify-content-between align-items-center border-bottom mb-3">
				<p class="text-info text-md">
					<?php echo __( 'visits' ) ?>
				</p>
				
				<p class="d-flex flex-column text-right">
					<span class="font-weight-bold text-muted">
						<?php echo $visits ?>
					</span>
				</p>
			</div>

			<div class="d-flex justify-content-between align-items-center mb-0">
				<p class="text-gray text-md">
					<?php echo __( 'unique-ip' ) ?>
				</p>
				<p class="d-flex flex-column text-right">
					<span class="font-weight-bold text-muted">
						<?php echo $ips ?>
					</span>
				</p>
			</div>
		</div>
	</div>
	
	<div class="card">
		<div class="card-header border-0">
				<h3 class="card-title"><?php echo __( 'os-info' ) ?></h3>
		</div>
		<div class="card-body">
			<div class="position-relative mb-4">
				<canvas id="osChart" height="200"></canvas>
			</div>
			<div class="d-flex flex-row justify-content-end">
		<?php if ( !empty( $Stats['osData'] ) ) : 
				foreach ( $Stats['osData'] as $id => $platform ) : ?>
				<span class="mr-2">
					<i class="fas fa-square" style="color: <?php echo $platform['color'] ?>"></i> <?php echo $platform['label'] ?>
				</span>
				<?php endforeach ?>
		<?php endif ?>
			</div>
		</div>
	</div>
	
	<div class="card">
		<div class="card-header">
			<h3 class="card-title">
				<?php echo __( 'referrers' ) ?>
			</h3>
			<div class="card-tools">
				<ul class="nav nav-pills ml-auto">
					<li class="nav-item">
						<a class="nav-link active" href="#referrers-data" data-toggle="tab"><?php echo __( 'data' ) ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#referrers-summary" data-toggle="tab"><?php echo __( 'summary' ) ?></a>
					</li>
				</ul>
			</div>
		</div>
		
		<div class="card-body">
			<div class="tab-content p-0">
				<div class="tab-pane active justify-content-between" id="referrers-data" style="position: relative; height: 300px;">
					<div class="table-responsive">
						<?php table_total( 'search_terms', $Stats ) ?>
					</div>
					
					<div class="table-responsive">
						<?php table_total( 'domain', $Stats ) ?>
					</div>
					
					<div class="table-responsive">
						<?php table_total( 'referrer', $Stats ) ?>
					</div>
				</div>
				
				<div class="tab-pane" id="referrers-summary" style="position: relative; height: 300px;">
				<?php 
					$sources = ( isset( $Stats['visits']['source'] ) ? $Stats['visits']['source'] : null );
					
					if ( !empty( $sources ) ):
					
						$total = array_sum( $sources );

						$data = array(
							'direct' => format_number( 100 * $sources['direct'] / $total ),
							'referral' => format_number( 100 * $sources['referrer'] / $total ),
							'search' => format_number( 100 * $sources['search_terms'] / $total )
						);
						
						arsort( $data );
						
						foreach( $data as $k => $v ) : ?>
						
						<div class="d-flex justify-content-between align-items-center border-bottom mb-3">
							<p class="text-gray text-md">
								<?php echo htmlspecialchars( __( $k ) ) ?>
							</p>
							<p class="d-flex flex-column text-right">
								<span class="font-weight-bold text-muted">
									<?php echo $v ?>
								</span>
							</p>
						</div>
						<?php endforeach ?>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="card">
		<div class="card-header border-0">
			<h3 class="card-title"><?php echo __( 'unique-ip' ) ?></h3>
		</div>
		
		<div class="card-body table-responsive p-0">
			<table class="table table-striped table-valign-middle">
				<thead>
					<tr>
						<th><?php echo __( 'ip-address' ) ?></th>
						<th><?php echo __( 'hits' ) ?></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( !empty( $Stats['visits']['remote_ip'] ) ) : 
					foreach( $Stats['visits']['remote_ip'] as $ip => $hits ) :
				?>
					<tr>
						<td><?php echo $ip ?></td>
						<td><?php echo $hits ?></td>
					</tr>
				<?php endforeach ?>
				<?php endif ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

</div>