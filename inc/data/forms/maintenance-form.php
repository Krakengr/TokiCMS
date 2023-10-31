<?php defined('TOKICMS') or die('Hacking attempt...');
#####################################################
#
# Maintenance Data Form
#
#####################################################
$this->disableFormButtons = true;

$L = $this->lang;

$disqusCode = $this->currentLang['settings']['disqus_shortname'];

$disqusFile = ( !empty( $disqusCode ) ? md5( $disqusCode . CACHE_HASH ) . '.xml' : null );
$dirFile	= BACKUPS_ROOT . $disqusFile;
$htmlFile	= SITE_URL . PS . 'data' . PS . 'backups' . PS . $disqusFile;
$tip		= ( ( !empty( $disqusFile ) && file_exists( $dirFile ) ) ? sprintf( __( 'export-comments-file-tip' ), $htmlFile ) : '' );

$dbBackupHtml = '
<div class="form-group row">
	<div class="col-sm-2">' . $L['backup-db'] . '</div>
	<div class="col-sm-10">
		<div class="form-group">
			<div class="custom-control custom-switch">
				<input class="custom-control-input" type="checkbox" name="db[structure]" id="backupDbTableStructure" checked>
				<label class="custom-control-label" for="backupDbTableStructure">' . $L['save-the-table-structure'] . '</label>
			</div>
		</div>
		
		<div class="form-group">
			<div class="custom-control custom-switch">
				<input class="custom-control-input" type="checkbox" name="db[data]" id="backupDbTableData" checked>
				<label class="custom-control-label" for="backupDbTableData">' . $L['save-the-table-data'] . '</label>
			</div>
		</div>
		
		<div class="form-group">
			<div class="custom-control custom-switch">
				<input class="custom-control-input" type="checkbox" name="db[gzip]" id="backupDbTableGzip">
				<label class="custom-control-label" for="backupDbTableGzip">' . $L['compress-the-file-with-gzip'] . '</label>
			</div>
		</div>
		
		<button type="submit" class="btn btn-secondary" id="backupDb" name="func[backup-db]">
			' . $L['download'] . '
		</button>
		
		<br /><small id="backupDb" class="form-text text-muted">' . $L['backup-db-maintenance-tip'] . '</small>
		
	</div>
</div>';

$form = array
(
	'maintenance-settings' => array
	(
		'title' => $L['system-maintenance'],
		'data' => array(
			'maintenance-settings' => array(
				'title' => null, 'tip' =>$L['system-maintenance-tip'], 'data' => array
				(
					'empty-file-cache'=>array('label'=>$L['empty-file-cache'], 'name' => 'empty-file-cache', 'type'=>'button', 'value'=>null, 'tip'=>$L['empty-file-cache-tip'] ),
					'recount-all-statistics'=>array('label'=>$L['recount-all-statistics'], 'name' => 'recount-all-statistics', 'type'=>'button', 'value'=>null, 'tip'=>$L['recount-all-statistics-tip'] ),
					'sync-comments'=>array('label'=>$L['sync-comments'], 'name' => 'sync-comments', 'type'=>'button', 'value'=>null, 'tip'=>$L['sync-comments-tip'], 'hide' => ( empty( $this->currentLang['settings']['disqus_shortname'] ) ? true : false ) ),
					'export-comments'=>array('label'=>$L['export-comments'], 'name' => 'export-comments', 'type'=>'button', 'value'=>null, 'tip'=> sprintf( $L['export-comments-tip'], $tip ), 'hide' => ( empty( $this->currentLang['settings']['disqus_shortname'] ) ? true : false ) ),
					'optimize-db-tables'=>array('label'=>$L['optimize-db-tables'], 'name' => 'optimize-db-tables', 'type'=>'button', 'value'=>null, 'tip'=>$L['optimize-db-tables-tip'] ),
					'backup-db'=>array('label'=>$L['backup-db'], 'name' => 'backup-db', 'type'=>'custom-html', 'value'=>$dbBackupHtml, 'tip'=>$L['backup-db-tip'] ),
				)
			)
		)
	),
	
	'sync-database' => array
	(
		'title' => $L['sync-database'],
		'hide' => ( !$this->siteIsSelfHosted ? false : true ),
		'data' => array
		(
			'sync-settings' => array(
				'title' => null, 'tip' =>$L['sync-database-tip'], 'data' => array
				(
					'get-database'=>array('label'=>$L['get-data'], 'name' => 'get-database', 'type'=>'button', 'value'=>null, 'tip'=>$L['get-data-tip'] ),
					'push-database'=>array('label'=>$L['push'], 'name' => 'push-database', 'type'=>'button', 'value'=>null, 'tip'=>$L['push-database-tip'] ),
					'pull-database'=>array('label'=>$L['pull'], 'name' => 'pull-database', 'type'=>'button', 'value'=>null, 'tip'=>$L['pull-database-tip'] ),
				)
			)
		)
	)
);