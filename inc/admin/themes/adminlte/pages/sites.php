<div class="row">
  <div class="col-12">
			<div class="card mb-4">
			<div class="card-header">
				<i class="fas fa-table mr-1"></i>
				<?php echo $L['sites'] ?>
			</div>
				<div class="card-body">
					<div class="table-responsive">
			<form class="tab-content" id="form" method="post" action="" autocomplete="off">
				<table class="table table-bordered" id="blogsTable" width="100%" cellspacing="0">
					<thead class="thead-light">
						<tr>
							<th><?php echo $L['name'] ?></th>
							<th><?php echo $L['url'] ?></th>
							<th><?php echo $L['polylang'] ?></th>
							<th><?php echo $L['multiblog'] ?></th>
							<th><?php echo $L['maintenance-mode'] ?></th>
							<th><?php echo $L['disable'] ?> <a href="#" type="button" data-toggle="tooltip" data-placement="right" data-html="true" title="<?php echo __( 'disable-site-tip' ) ?>"><i class="bi bi-info-circle"></i></a></th>
							<th><?php echo $L['registrations'] ?></th>
						</tr>
					</thead>
					<tbody>
				<?php
				if ( !empty( $DataSites ) ) :
					foreach( $DataSites as $site ) :
						
						$message = '';
						$hosted  = 'self';//TODO
						
						if ( $site['hosted'] !== 'self' )
						{
							$host = Json( $site['hosted'] );
							
							if ( !empty( $host ) )
							{
								//TODO
								//$hosted = '';
							}
						}
						
						if ( !empty( $site['is_primary'] ) )
						{
							$message = ' [<em>' . $L['primary'] . '</em>]';
						}
						
						elseif ( empty( $site['is_primary'] ) && ( $hosted == 'self' ) )
						{
							$pingURL = ( !empty( $site['site_ping_url'] ) ? $site['site_ping_url'] : $site['url'] . $site['ping_slash'] . PS );
							
							$pingURL .= '?action=check&token=' . $site['site_secret'];

							$p = PingSite( $pingURL ); //Get the status

							$message = ' [<a href="javascript: void(0);" data-toggle="modal" data-target="#openModal' . $site['id'] . '">' . $L['php-code'] . '</a>]';
							
							$message .= ( ( !empty( $p ) && isset( $p['message'] ) && ( $p['message'] == 'Success' ) ) ? ' <span class="check" title ="' . $L['connection-test-succeeded'] . '">&#10004</span>' : ' <span class="cross" title ="' . $L['connection-test-failed'] . '">&#10060;</span>' );
						}
						
						elseif ( $hosted != 'self' )
						{
							if ( $hosted == 'blogger' )
							{
								$message = ' [<em>' . $L['hosted-on-blogger'] . '</em>]';
							}
							
							elseif ( $hosted == 'wordpress' )
							{
								$message = ' [<em>' . $L['hosted-on-wordpress-com'] . '</em>]';
							}
						}
					?>
						<tr>
							<td class="dt-body-center"><a href="<?php echo ADMIN_URI . ( !$site['is_primary'] ? '?site=' . $site['id'] : '' ) ?>"><?php echo $site['title'] ?></a><?php echo $message ?></td>
							<td class="dt-body-center"><a target="_blank" href="<?php echo $site['url'] ?>"><?php echo $site['url'] ?></a></td>
							<?php if ( $hosted != 'self' ) : ?>
							<td class="dt-body-center">-</td>
							<td class="dt-body-center">-</td>
							<td class="dt-body-center">-</td>
							<td class="dt-body-center">-</td>
							<td class="dt-body-center">-</td>
							<?php else : ?>
							<td class="dt-body-center"><input name="sites[<?php echo $site['id'] ?>][polylang]" value="1" type="checkbox" <?php echo ( ( $site['enable_multilang'] == 'true' ) ? 'checked' : '' ) ?> /></td>
							
							<td class="dt-body-center"><input name="sites[<?php echo $site['id'] ?>][multiblog]" value="1" type="checkbox" <?php echo ( ( $site['enable_multiblog'] == 'true' ) ? 'checked' : '' ) ?> /></td>
							
							<td class="dt-body-center"><input name="sites[<?php echo $site['id'] ?>][maintenance]" value="1" type="checkbox" <?php echo ( ( $site['enable_maintenance'] == 'true' ) ? 'checked' : '' ) ?> /></td>
							
							<td class="dt-body-center"><input name="sites[<?php echo $site['id'] ?>][disable]" value="1" type="checkbox" <?php echo ( $site['disabled'] ? 'checked' : '' ) ?> /></td>
							
							<td class="dt-body-center"><input name="sites[<?php echo $site['id'] ?>][registrations]" value="1" type="checkbox" <?php echo ( ( $site['enable_registration'] == 'true' ) ? 'checked' : '' ) ?> /></td>
							<?php endif ?>
						</tr>
				<?php endforeach ?>
				<?php endif ?>
					</tbody>
				</table>
			</div>
				</div>
			</div>
		</div>
		
	<input type="hidden" name="_token" value="<?php echo generate_token( 'sites' ) ?>">
	
	<div class="align-middle">
		<div class="float-left mt-1">
			<button type="submit" class="btn btn-primary btn-sm" name="save"><?php echo $L['save'] ?></button>
			<a class="btn btn-secondary btn-sm" href="<?php echo $Admin->Url() ?>" role="button"><?php echo $L['cancel'] ?></a>
		</div>
	</div>
	</form>

	<?php if ( !empty( $sites ) ) :
	foreach( $sites as $site ) :
	
		if ( ( $site['is_primary'] ) || ( $site['hosted'] != 'self' ) )
			continue;
	?>
	<!-- Modal #<?php echo $site['id'] ?> -->
	<div class="modal fade" id="openModal<?php echo $site['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="ModalLabel<?php echo $site['id'] ?>" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="ModalLabel<?php echo $site['id'] ?>"><?php echo sprintf( $L['php-code-for-site'], $site['title'] ) ?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<p><?php echo sprintf( $L['enter-php-code'], $site['title'] ) ?></p>
			<textarea id="textArea<?php echo $site['id'] ?>" class="form-control" rows="3" readonly><?php echo '<?php defined(\'TOKICMS\') or die(\'Hacking attempt...\'); // cannot be loaded directly
			
// SITE_ID will default to 1 in a single site configuration.
define(\'SITE_ID\', ' . $site['id'] . ' );

// If necessary, define the full URL of your site including the subdomain, if any. Don\'t forget the trailing slash!
define(\'SITE_URL\', \'' . $site['url'] . '\');

// Define the charset used by your site. If in any doubt, leave the default utf-8.
define( \'CHARSET\', \'UTF-8\' );

// MySQL settings. You need to get this info from your web host.
// Name of the database
define(\'DATABASE\', \'' . DATABASE . '\');

// Database username
define(\'DBUSERNAME\', \'' . DBUSERNAME . '\');

// Database password
define(\'DBPASSWORD\', \'' . DBPASSWORD . '\');

// MySQL hostname (it will usually be \'localhost\')
define(\'SERVER\', \'' . SERVER . '\');

// Needed only if multiple instances of this CMS are to be installed in the same database
//(please use only alphanumeric characters or underscore (NO hyphen))
define( \'DB_PREFIX\', \'' . DB_PREFIX . '\' );

// Admin settings.
// If set, you will have access to the admin panel.
//If not, this blog will not have an admin panel, meaning that you have it as a child site
define(\'ENABLE_ADMIN\', FALSE );

//If admin is enabled, you can set the slug here
//It helps to make login to Administration panel more smooth and easy, and to reduce the chances of being hacked.
//For instanse, if you want your admin panel to be http://mysite.com/admin222/ set \'admin222\' below
//Only numbers, dashes and letters allowed
define(\'ADMIN_SLUG\', null );

//If you want to change the slug for ping, change it below. It helps to protect your site and reduce the chances of overloading the system.
//For instanse, if you want your ping url to be http://mysite.com/my-ping/ set \'my-ping\' below
//Only numbers, dashes and letters allowed
define(\'PING_SLUG\', \'' . $site['ping_slash'] . '\' );

//Set the posts table name here
define(\'POSTS\', \'' . POSTS . '\');

//Set the users table name here
define(\'USERS\', \'' . USERS . '\');

// Main hash
// This unique key is needed for ping, and other routines
define(\'MAIN_HASH\', \'' . $site['site_secret'] . '\');

// Cache hash
// If the cache is enabled, you need to set a unique key here
define(\'CACHE_HASH\', \'' . $site['cache_hash'] . '\');

// Admin hash
// If the admin is enabled, you need to set a unique key here
define(\'ADMIN_HASH\', \'\');

// Update hash
// This unique key is needed only when you want to auto update your site
define(\'UPDATE_HASH\', \'' . $site['update_hash'] . '\');'; ?>
</textarea> <button type="button" id="button-<?php echo $site['id'] ?>" class="btn btn-outline-dark"><?php echo $L['copy'] ?></button>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $L['close'] ?></button>
		  </div>
		</div>
	  </div>
	</div>
	<script type="application/javascript">
	$("#button-<?php echo $site['id'] ?>").click(function(){
		$("#textArea<?php echo $site['id'] ?>").select();
		document.execCommand('copy');
	});
	</script>
<?php endforeach ?>
<?php endif ?>
</div>