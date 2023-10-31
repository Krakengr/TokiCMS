<meta charset="utf-8">
<title><?php echo Theme::HeaderTitle() ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="robots" content="max-image-preview:large, noindex, noarchive" />
<link rel="dns-prefetch" href="//fonts.googleapis.com" />
<link rel="dns-prefetch" href="//code.jquery.com" />

<link href="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />
<link href="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/css/theme.css" rel="stylesheet" type="text/css" media="all" />
<link href="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all" />
<link href="<?php echo TOOLS_HTML ?>theme_files/assets/frontend/css/custom.css" rel="stylesheet" type="text/css" media="all" />

<link href="https://fonts.googleapis.com/css?family=Open+Sans:200,300,400,400i,500,600,700%7CMerriweather:300,300i" rel="stylesheet"/>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>

<link rel="icon" type="image/x-icon" href="data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAYAAABPYyMiAAAABmJLR0T///////8JWPfcAAAACXBIWXMAAABIAAAASABGyWs+AAAAF0lEQVRIx2NgGAWjYBSMglEwCkbBSAcACBAAAeaR9cIAAAAASUVORK5CYII=" />
<meta name="theme-color" content="#ffffff"/>
		
<?php if ( Settings::IsTrue( 'enable_honeypot' ) )	: ?>
	<style>.ohhney{opacity: 0;position: absolute;top: 0;left: 0;height: 0;width: 0;z-index: -1;}</style>
<?php endif ?>

<?php if ( !empty( $Meta ) && !empty( $Url ) ) : ?>
<meta http-equiv="refresh" content="3; URL=<?php echo $Url ?>" />
<?php endif ?>

<?php 
if ( !empty( Settings::Get()[ 'header_code' ] ) )
{
	$header_code = Json( Settings::Get()[ 'header_code' ] );
	
	$headerCode = '';

	if ( !empty( $header_code ) && is_array( $header_code ) )
	{
		$CurrentLang = CurrentLang();
				
		foreach( $header_code as $_h => $__h )
		{
			if ( 
				( $__h['language'] == 0 ) || 
				( ( $__h['language'] > 0 ) && ( $__h['language'] == $CurrentLang['lang']['id'] ) )
			)
			{
				$headerCode .= html_entity_decode( $__h[ 'code' ] ) . PHP_EOL;
			}

			unset( $_h, $__h );
		}
	}

	unset( $header_code );
}
?>