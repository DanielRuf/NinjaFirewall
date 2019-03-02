<?php
// +-------------------------------------------------------------------+
// | NinjaFirewall (Pro Edition)                                       |
// |                                                                   |
// | (c) NinTechNet - https://nintechnet.com/                          |
// |                                                                   |
// +-------------------------------------------------------------------+
// | This program is free software: you can redistribute it and/or     |
// | modify it under the terms of the GNU General Public License as    |
// | published by the Free Software Foundation, either version 3 of    |
// | the License, or (at your option) any later version.               |
// |                                                                   |
// | This program is distributed in the hope that it will be useful,   |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of    |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the     |
// | GNU General Public License for more details.                      |
// +-------------------------------------------------------------------+

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

$div_error = '<div class="alert alert-danger text-left"><a class="close" '.
				'data-dismiss="alert" aria-label="close">&times;</a>%s</div>';
$div_success = '<div class="alert alert-success text-left"><a class="close" '.
				'data-dismiss="alert" aria-label="close">&times;</a>%s</div>';
$twitter = _('Updates info:') .'&nbsp;<a href="https://twitter.com/nintechnet">'.
				'<img border="0" src="static/twitter.png"></a>';
$succ_img  = glyphicon( 'success' ) .'&nbsp;';
$err_img  = glyphicon( 'error' ) .'&nbsp;';
$warn_img = glyphicon( 'warning' ) .'&nbsp;';

html_header();
?>
<div class="col-sm-12 text-left">
	<h3><?php echo _('Account > Updates') ?></h3>
	<br />
<?php

$nfw_update  = 0;
$update_file = './nfwlog/cache/nfw_update.php';
$zip_file    = './nfwlog/cache/nfw_update_zip.php';
$extract_path = dirname( __DIR__ );

if (! function_exists( 'curl_init' ) ) {
	printf(
		$div_error,
		_('NinjaFirewall cannot check for updates because your PHP configuration does not have cURL support.')
	);
	echo '</div>';
	html_footer();
	exit;
}

// Update requested ?
if (! empty( $_POST['nfw_update'] ) ) {

	// Ensure the cache dir is writable :
	if (! is_writable( './nfwlog/cache/' ) ) {
		printf(
			$div_error,
			sprintf(
				_('The "%s" directory is not writable. Operation aborted.'),
				'./nfwlog/cache/'
			)
		);

		goto NFW_AU_END;
	}

	// Ensure NF's root directory is writable:
	if (! is_writable( dirname( __DIR__ ) ) ) {
		printf(
			$div_error,
			sprintf(
				_('The "%s" directory is not writable. Operation aborted.'),
				htmlspecialchars( dirname( __DIR__ ) )
			)
		);
		goto NFW_AU_END;
	}

	if (! $res = download_account_update() ) {
		if ( file_exists( $update_file ) ) {
			unlink( $update_file );
		}
		goto NFW_AU_END;
	}

	// Include the update script:
	if (! file_exists( $update_file ) ) {
		printf( $div_error, _('Unable to download or save the update.') );
		goto NFW_AU_END;
	}
	// Load update script:
	require $update_file;
	// Ensure we get the version:
	if (! defined('NF_UPDATE_VERSION') ) {
		printf(
			$div_error,
			_('Unable to retrieve the downloaded file version. Operation aborted.')
		);
		unlink( $update_file );
		goto NFW_AU_END;
	}
	// Double check the new version :
	if ( version_compare( NFW_ENGINE_VERSION, NF_UPDATE_VERSION, '>=' ) ) {
		printf(
			$div_error,
			sprintf(
				_('Your current version is %s and downloaded version is %s too.'),
				htmlentities( NFW_ENGINE_VERSION ),
				htmlentities( NF_UPDATE_VERSION )
			)
		);
		unlink( $update_file );
		goto NFW_AU_END;
	}

	// Double check edn :
	if ( NFW_EDN != NF_UPDATE_EDN ) {
		printf(
			$div_error,
			_('The downloaded update does not match your NinjaFirewall edition. Update is not possible.')
		);
		unlink( $update_file );
		goto NFW_AU_END;
	}

	// What are we supposed to do ?
	if ( $_POST['what'] == 1 ) {
		// Display change log :
		$nfw_update = 1;
		?>
		<form method="post" name="update_form">
			<table width="100%" class="table table-nf">
				<tr>
					<td width="100%" align="center">
						<div align="left"><?php
						printf(
							_('You are about to update from version %s to %s.') .' '.
							_('Please review the changelog below and click the button to proceed.'),
							'<strong>'. htmlentities( NFW_ENGINE_VERSION ) .'</strong>',
							'<strong>'. htmlentities( NF_UPDATE_VERSION ) .'</strong>'
						) ?></div>
						<br /><textarea class="form-control" rows="12"><?php
							echo htmlentities( $changelog );
						?></textarea>
					</td>
				</tr>
			</table>
			<center>
				<input type="hidden" name="what" value="2" />
				<p><input class="btn btn-md btn-success" type="submit" name="nfw_update" value="<?php echo _('Install this update') ?>" /></p>
			</center>
		</form>
	</div>
		<?php
		html_footer();
		exit;

	} elseif ( $_POST['what'] == 2 ) {
		// Update :
		$nfw_update = 2;

		// Ensure we can extract the ZIP file :
		if (! class_exists( 'ZipArchive' ) ) {
			printf(
				$div_error,
				_('NinjaFirewall cannot extract the update because your PHP configuration does not have the ZipArchive class.')
			);
			if ( file_exists( $update_file ) ) {
				unlink( $update_file );
			}
			goto NFW_AU_END;
		}

		$res = unpack_account_update();

		// Delete temp files :
		if ( file_exists( $update_file ) ) {
			unlink( $update_file );
		}
		if ( file_exists( $zip_file ) ) {
			unlink( $zip_file );
		}

		// Failed ?
		if (! $res) {
			goto NFW_AU_END;
		}

		// Success :
		$_SESSION['ver'] = 0;
		printf( $div_success, _('The update was successful.') );

		?>
		<form method="post" action="?token=<?php echo $_REQUEST['token'] ?>&ver=<?php echo NFW_ENGINE_VERSION ?>">
			<input class="btn btn-sm btn-success" type="submit" value="<?php echo _('Click here to finalize') ?>">
			<input type="hidden" name="mid" value="<?php echo $GLOBALS['mid'] ?>" />
		</form>
	</div>
		<?php
		html_footer();
		exit;
	}

} else {

	// Request to check again for updates ?
	if (! empty( $_POST['nfw_checkupdate'] ) ) {
		$_SESSION['ver'] = 0;
	}

	// Check for update if we haven't done so yet :
	if ( $_SESSION['ver'] < 1 ) {
		$res = check_account_update();
		if (! $res ) {
			// Could not get updates info :
			$connect_err = 1;
			goto NFW_AU_END;
		}
	}
	if (! empty( $_POST['nfw_checkupdate'] ) && version_compare( NFW_ENGINE_VERSION, $_SESSION['vapp'], '>=' ) ) {
		printf( $div_success, _('Your version is up to date.') );
	}
}

NFW_AU_END:
$is_update = 0;
?>
	<form method="post" name="update_form">
		<table width="100%" class="table table-nf">
			<tr>
			<?php
				// A new version is available:
				if ( version_compare( NFW_ENGINE_VERSION, $_SESSION['vapp'], '<' ) ) {
					$icon = $warn_img;
					$msg = sprintf(
						_('A new version is available: v%s'),
						$_SESSION['vapp']
					);
					$is_update = 1;
				} else {
					// Error while attempting to connect to the update server:
					if (! empty( $connect_err ) ) {
						$icon = $err_img;
						$msg = _('Unable to retrieve updates information from NinjaFirewall server');
					// OK:
					} else {
						$icon = $succ_img;
						$msg = _('Your version is up to date.');
					}
				}
			?>
				<td width="40%" align="left"><?php echo _('Available updates') ?></td>
				<td width="5%" align="center"><?php echo $icon ?></td>
				<td width="55%"><?php echo $msg ?></td>
			</tr>
			<tr>
				<td width="40%">&nbsp;</td>
				<td width="5%">&nbsp;</td>
				<td width="55%">
			<?php
			//  If we have an update available, show the 'download' button :
			if ( $is_update ) {
				echo '<input type="hidden" name="what" value="1" />';
				echo '<input class="btn btn-sm btn-success" type="submit" name="nfw_update" value="'. _('Download this update') .'" />&nbsp;&nbsp;&nbsp;';
			}
			?>
					<input class="btn btn-sm btn-success" type="submit" name="nfw_checkupdate" value="<?php echo _('Check for updates') ?>" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="mid" value="<?php echo $GLOBALS['mid'] ?>">
	</form>
</div>
<?php

html_footer();

// ---------------------------------------------------------------------

function check_account_update() {

	global $div_error;

	require_once './lib/misc.php';
	global $http_err_code;

	if (! NFW_UPDATE ) {
		printf(
			$div_error,
			_('Connections to NinjaFirewall server have been disabled by the user.')
		);
		return 0;
	}

	// If your server can't remotely connect to a SSL port, add this
	// to your ".htninja" script: define('NFW_DONT_USE_SSL', 1);
	if ( defined( 'NFW_DONT_USE_SSL' ) ) {
		$proto = "http://";
	} else {
		$proto = "https://";
	}

	$data  = 'action=checkversion';
	$data .= '&edn='. urlencode( NFW_EDN );

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; NinjaFirewall/' .
					NFW_ENGINE_VERSION . ':' . NFW_EDN . ')' );
	curl_setopt( $ch, CURLOPT_ENCODING, '');
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
	curl_setopt( $ch, CURLOPT_URL, $proto. NFW_UPDATE . '/index.php' );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt ($ch, CURLOPT_HEADER, 0);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

	if ( ($content = curl_exec($ch)) === FALSE ) {
		// cURL error (connection, timeout etc) :
		$CURL_ERR = curl_error( $ch );
		printf(
			$div_error,
			sprintf(
				_('Unable to connect to NinjaFirewall server: %s.'),
				htmlspecialchars( $CURL_ERR )
			)
		);
		@curl_close( $ch );
		return 0;
	}

	$response = curl_getinfo( $ch );
	curl_close($ch);

	// HTTP error ?
	if ( $response['http_code'] != 200 ) {
		printf(
			$div_error,
			sprintf(
				_('The server returned the following HTTP code: %s %s.'),
				(int) $response['http_code'],
				$http_err_code[$response['http_code']]
			)
		);
		return 0;
	}

	$tmp = @unserialize( $content );

	if (! empty( $tmp[NFW_EDN] ) ) {
		$_SESSION['vapp'] = $tmp[NFW_EDN];
	} else {
		$_SESSION['vapp'] = NFW_ENGINE_VERSION;
	}
	$_SESSION['ver'] = 1;
	return 1;

}

// ---------------------------------------------------------------------
function download_account_update() {

	global $div_error, $nfw_options, $update_file;

	require_once './lib/misc.php';
	global $http_err_code;

	$data  = 'action=update';
	$data .= '&edn=' . urlencode( NFW_EDN );
	$data .= '&ver=' . urlencode( NFW_ENGINE_VERSION );

	// pro+ edn only :
	if ( NFW_EDN == 2 ) {
		$data .= '&host=' . urlencode( strtolower( $_SERVER['HTTP_HOST'] ) );
		$data .= '&name=' . urlencode( strtolower( $_SERVER['SERVER_NAME'] ) );
		$data .= '&lic=' . urlencode( $nfw_options['lic'] );
	}

	if (! NFW_UPDATE ) {
		printf(
			$div_error,
			_('Connections to NinjaFirewall server have been disabled by the user.')
		);
		return 0;
	}

	// If your server can't remotely connect to a SSL port, add this
	// to your ".htninja" script: define('NFW_DONT_USE_SSL', 1);
	if ( defined( 'NFW_DONT_USE_SSL' ) ) {
		$proto = "http://";
	} else {
		$proto = "https://";
	}

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; NinjaFirewall/' .
					NFW_ENGINE_VERSION . ':' . NFW_EDN . ')' );
	curl_setopt( $ch, CURLOPT_ENCODING, '');
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
	curl_setopt( $ch, CURLOPT_URL, $proto. NFW_UPDATE .'/index.php' );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_HEADER, 0);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

	if ( ($content = @unserialize(curl_exec($ch)) ) === FALSE ) {
		// cURL error (connection, timeout etc) :
		$CURL_ERR = curl_error( $ch );
		printf(
			$div_error,
			sprintf(
				_('Unable to connect to NinjaFirewall server: %s.'),
				htmlspecialchars( $CURL_ERR )
			)
		);
		@curl_close( $ch );
		return 0;
	}

	$response = curl_getinfo( $ch );
	curl_close( $ch );

	// HTTP error ?
	if ( $response['http_code'] != 200 ) {
		printf(
			$div_error,
			sprintf(
				_('The server returned the following HTTP code: %s %s.'),
				(int) $response['http_code'],
				$http_err_code[$response['http_code']]
			)
		);
		return 0;
	}

	if (! empty($content['err'] ) ) {
		if ( $content['err'] > 10 ) {
			printf(
				$div_error,
				sprintf(
					_('Your license is not valid (#%s).'),
					htmlspecialchars( $content['err'] )
				)
			);
		} else {
			printf(
				$div_error,
				sprintf(
					_('The remote server returned the following error: %s.'),
					htmlspecialchars( $content['err'] )
				)
			);
		}
		return 0;
	}

	if ( empty( $content['file'] ) ) {
		printf( $div_error, _('The remote server did not return any data.') );
		return 0;
	}

	// Ensure we have a PHP script :
	if (! preg_match( '/^<\?php\s/', $content['file'] ) ) {
		$content = '';
		printf( $div_error, _('The remote server did not return the expected data.') );
		return 0;
	}

	// Save it :
	@file_put_contents( $update_file, $content['file'], LOCK_EX );

	// OK :
	return 1;
}

// ---------------------------------------------------------------------
function unpack_account_update() {

	global $div_error, $update_file, $package, $zip_file, $extract_path;

	// Decode the content :
	$tmp_data = base64_decode( $package );
	// Ensure we have a ZIP header :
	if (! preg_match( '/^\x50\x4b\x03\x04/', $tmp_data ) ) {
		$tmp_data = '';
		printf( $div_error, _('Downloaded file is not a valid ZIP archive.') );
		return 0;
	}
	// Save data to a ZIP file :
	@file_put_contents( $zip_file, $tmp_data, LOCK_EX );

	// Extract it :
	$zip = new ZipArchive;
	if ( $zip->open( $zip_file ) === TRUE ) {
		$zip->extractTo( $extract_path );
		$zip->close();

	} else {
		unlink( $zip_file );
		printf( $div_error, _('Unable to extract the ZIP archive.') );
		return 0;
	}

	// OK
	return 1;
}

// ---------------------------------------------------------------------
// EOF
