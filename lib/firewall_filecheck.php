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

$log_dir = dirname( __DIR__ ) . '/nfwlog/cache/';
$nfmon_snapshot = $log_dir . 'nfilecheck_snapshot.php';
$nfmon_diff = $log_dir . 'nfilecheck_diff.php';
$err = ''; $success = '';

// Download File Check snapshot :
if ( isset( $_POST['dlsnap'] ) ) {
	if ( file_exists( $nfmon_snapshot ) ) {
		$stat = stat( $nfmon_snapshot );
		$data = "== NinjaFirewall File Check (snapshot)\n";
		$data.= "== {$_SERVER['HTTP_HOST']}\n";
		$data.= '== ' . date( 'M d, Y @ H:i:s O', $stat['ctime'] ) . "\n\n";
		$fh = fopen( $nfmon_snapshot, 'r' );
		while (! feof( $fh ) ) {
			$res = explode('::', fgets( $fh ) );
			if (! empty( $res[0][0] ) && $res[0][0] == '/' ) {
				$data .= $res[0] . "\n";
			}
		}
		fclose($fh);
		$data .= "\n== EOF\n";
		header('Content-Type: application/txt');
		header('Content-Length: '. strlen( $data ) );
		header('Content-Disposition: attachment; filename="'. $_SERVER['HTTP_HOST'] .'_snapshot.txt"');
		echo $data;
		exit;
	}
}
// Download File Check modified files list :
if ( isset( $_POST['dlmods'] ) ) {
	if ( file_exists( $nfmon_diff ) ) {
		$download_file = $nfmon_diff;
	} elseif ( file_exists( $nfmon_diff .'.php'  ) ) {
		$download_file = $nfmon_diff .'.php';
	} else {
		exit;
	}
	$stat = stat( $download_file );
	$data = "== NinjaFirewall File Check (diff)\n";
	$data.= "== {$_SERVER['HTTP_HOST']}\n";
	$data.= '== ' . date('M d, Y @ H:i:s O', $stat['ctime']) . "\n\n";
	$data.= '[+] = ' . _('New file') .
				'      [!] = ' . _('Modified file') .
				'      [-] = ' . _('Deleted file') .
				"\n\n";
	$fh = fopen( $download_file, 'r' );
	while (! feof( $fh ) ) {
		$res = explode( '::', fgets( $fh ) );
		if ( empty( $res[1] ) ) { continue; }
		// New file :
		if ( $res[1] == 'N' ) {
			$data .= '[+] ' . $res[0] . "\n";
		// Deleted file :
		} elseif ( $res[1] == 'D' ) {
			$data .= '[-] ' . $res[0] . "\n";
		// Modified file:
		} elseif ( $res[1] == 'M' ) {
			$data .= '[!] ' . $res[0] . "\n";
		}
	}
	fclose( $fh );
	$data .= "\n== EOF\n";
	header('Content-Type: application/txt');
	header('Content-Length: '. strlen( $data ) );
	header('Content-Disposition: attachment; filename="'. $_SERVER['HTTP_HOST'] .'_diff.txt"');
	echo $data;
	exit;
}

html_header();

?>
<div class="col-sm-12 text-left">
	<h3><?php echo _('Monitoring > File Check') ?></h3>
	<br />
<?php

// Ensure cache folder is writable :
if (! is_writable( './nfwlog/cache/' ) ) {
	echo '<div class="alert alert-danger text-left">'.
		_('Cannot write to the "./nfwlog/cache/" folder. Please make it writable.') .
		'</div></div>';
	html_footer();
	exit;
}

// Saved options ?
if (! empty( $_REQUEST['nfw_act'] ) ) {
	if ( $_REQUEST['nfw_act'] == 'delete' ) {
		// Delete de current snapshot file :
		if (file_exists( $nfmon_snapshot ) ) {
			unlink ( $nfmon_snapshot );
			$success = _('Snapshot file successfully deleted.');
			// Remove old diff file as well :
			if ( file_exists( $nfmon_diff . '.php' ) ) {
				unlink( $nfmon_diff . '.php' );
			}
			$nfw_options['snapdir'] = '';
		} else {
			$err =  _('You did not create any snapshot yet.');
		}
	} elseif ( $_REQUEST['nfw_act'] == 'create' ) {
		if (! $err = nf_sub_monitoring_create( $nfmon_snapshot ) ) {
			$success = _('Snapshot successfully created.');
			if (file_exists( $nfmon_diff ) ) {
				unlink( $nfmon_diff );
			}
		}
	} elseif ( $_REQUEST['nfw_act'] == 'scan' ) {
		// Scan disk for changes :
		if (! file_exists( $nfmon_snapshot ) ) {
			$err = _('You must create a snapshot first.');
		} else {

			$snapproc = microtime( true );
			$err = nf_sub_monitoring_scan( $nfmon_snapshot, $nfmon_diff );

			$nfw_options['snapproc'] = round( microtime( true ) - $snapproc, 2 );

			// Save processing time :
			save_config( $nfw_options, 'options' );

			if (! $err ) {
				if ( file_exists( $nfmon_diff ) ) {
					$err =  _('NinjaFirewall detected that changes were made to your files.');
					$changes = 1;
				} else {
					$success = _('No changes detected.');
				}
			}
		}
	}
}

if ( $err ) {
	echo '<div class="alert alert-danger text-left"><a class="close" '.
		'data-dismiss="alert" aria-label="close">&times;</a>'. $err .'</div>';
} elseif ( $success ) {
	echo '<div class="alert alert-success text-left"><a class="close" data-dismiss="alert"'.
		' aria-label="close">&times;</a>'. $success . '</div>';
}

if ( empty( $nfw_options['snapdir'] ) ) {
	$nfw_options['snapdir'] = '';
	if ( file_exists( $nfmon_snapshot ) ) {
		unlink( $nfmon_snapshot );
	}
}

// If we don't have a snapshopt, offer to create one :
if (! file_exists( $nfmon_snapshot ) ) {
	// Add NinjaFirewall's log and conf directories
	// to the exclusion list by default:
	$nfw_options['snapexclude'] =
		basename( dirname( __DIR__ ) ) .'/nfwlog/,'.
		basename( dirname( __DIR__ ) ) .'/conf/';
	?>
	<form method="post" name="monitor_form">
		<table width="100%" class="table table-nf">
			<tr>
				<td width="45%" align="left"><?php echo _('Create a snapshot of all files stored in that directory') ?></td>
				<td width="55%"><p><input class="form-control" type="text" name="snapdir" value="<?php
					if (! empty( $nfw_options['snapdir'] ) ) {
						echo htmlspecialchars( $nfw_options['snapdir'] );
					} else {
						echo htmlspecialchars( dirname( dirname( __DIR__ ) ) );
					}
					?>" required /></p>
				</td>
			</tr>
			<tr>
				<td width="45%" align="left" class="dotted"><?php echo _('Exclude the following files/folders (optional)') ?></td>
				<td width="55%" class="dotted"><p><input class="form-control" type="text" name="snapexclude" value="<?php echo htmlspecialchars( $nfw_options['snapexclude'] ) ?>" placeholder="<?php echo _('e.g.') .' /foo/bar/' ?>" /><br /><i><?php echo _('Full or partial case-sensitive string(s). Multiple values must be comma-separated.') ?></i></p></td>
			</tr>
			<tr>
				<td width="45%" align="left">&nbsp;</td>
				<td width="55%"><label><input type="checkbox" name="snapnoslink" value="1" checked="checked" />&nbsp;<?php echo _('Do not follow symbolic links (default)') ?></label></td>
			</tr>
		</table>

		<br />

		<input type="hidden" name="mid" value="<?php echo $GLOBALS['mid'] ?>" />
		<input type="hidden" name="nfw_act" value="create" />
		<center><input type="submit" name="save-changes" class="btn btn-md btn-success btn-25" value="<?php echo _('Create Snapshot') ?>" /></center>
	</form>
	</div>

	<?php
	html_footer();
	exit;
}

// We have a snapshot :
$stat = stat( $nfmon_snapshot );
$count = -2;
$fh = fopen( $nfmon_snapshot, 'r' );
while (! feof( $fh ) ) {
	fgets( $fh );
	++$count;
}
fclose( $fh );
// Look for new/mod/del files :
$res = array(); $new_file = array();
$del_file = array(); $mod_file = array();
// If no changes were detected, we display the last ones (if any) :
if (! file_exists( $nfmon_diff ) && file_exists( $nfmon_diff . '.php' ) ) {
	$nfmon_diff = $nfmon_diff . '.php';
}
if (file_exists( $nfmon_diff ) ) {
	$fh = fopen( $nfmon_diff, 'r' );
	while (! feof( $fh ) ) {
		$res = explode( '::', fgets( $fh ) );
		if ( empty( $res[1] ) ) { continue; }
		// New file :
		if ( $res[1] == 'N' ) {
			$s_tmp = explode(':', rtrim( $res[2] ) );
			$new_file[$res[0]] = $s_tmp[0] .':'.
				$s_tmp[1] .':'.
				$s_tmp[2] .':'.
				$s_tmp[3] .':'.
				date('Y-m-d H~i~s O', $s_tmp[4] ) .':'.
				date('Y-m-d H~i~s O', $s_tmp[5] );
		// Deleted file :
		} elseif ( $res[1] == 'D' ) {
			$del_file[$res[0]] = 1;
		// Modified file:
		} elseif ( $res[1] == 'M' ) {
			$s_tmp = explode(':', $res[2]);
			$mod_file[$res[0]] = $s_tmp[0] .':'.
				$s_tmp[1] .':'.
				$s_tmp[2] .':'.
				$s_tmp[3] .':'.
				date('Y-m-d H~i~s O', $s_tmp[4] ) .':'.
				date('Y-m-d H~i~s O', $s_tmp[5] ) .'::';
				$s_tmp = explode( ':', rtrim( $res[3] ) );
			$mod_file[$res[0]] .= $s_tmp[0] .':'.
				$s_tmp[1] .':'.
				$s_tmp[2] .':'.
				$s_tmp[3] .':'.
				date('Y-m-d H~i~s O', $s_tmp[4] ) .':'.
				date('Y-m-d H~i~s O', $s_tmp[5] );
		}
	}
	fclose( $fh );
	$mod = 1;
} else {
	$mod = 0;
}
?>
	<table width="100%" class="table table-nf">
		<tr>
			<td width="45%" align="left"><?php echo _('Last snapshot') ?></td>
			<td width="55%"><p>
				<p><?php printf( _('Created on: %s'), date('M d, Y @ H:i:s O', $stat['ctime']) ); ?></p>
				<p><?php printf( _('Total files: %s'), number_format($count) ); ?></p>

				<p><?php echo _('Directory:') ?> <code><?php echo htmlspecialchars( $nfw_options['snapdir'] ) ?></code></p>
				<?php
				if (! empty( $nfw_options['snapexclude'] ) ) {
					$res = @explode( ',', $nfw_options['snapexclude'] );
					echo '<p>'.  _('Exclusion:') .' ';
					foreach ( $res as $exc ) {
						echo '<code>'. htmlspecialchars( $exc ) .'</code>&nbsp;';
					}
					echo '</p>';
				}
				echo	'<p>'. _('Symlinks:') .' ';
				if ( empty( $nfw_options['snapnoslink'] ) ) {
					echo _('follow');
				} else {
					echo _('do not follow');
				}
				echo '</p>';
				if (! empty( $nfw_options['snapproc'] ) ) {
					echo '<p>' . sprintf( _('Processing time: %s seconds'), $nfw_options['snapproc'] ) . '</p>';
				}
				?>
				<form method="post">
					<p>
						<input type="submit" name="dlsnap" value="<?php echo _('Download Snapshot') ?>" class="btn btn-md btn-default btn-25" />&nbsp;&nbsp;&nbsp;<input type="submit" class="btn btn-md btn-default btn-25" onClick="return delit();" value="<?php echo _('Delete Snapshot') ?>" />
						<input type="hidden" name="nfw_act" value="delete" />
						<input type="hidden" name="mid" value="<?php echo $GLOBALS['mid'] ?>">
					</p>
				</form>
			</td>
		</tr>
		<tr>
			<td width="45%" align="left" class="dotted"><?php echo _('Last changes') ?></td>
			<td width="55%" class="dotted"><p>
			<?php
			// Show info about last changes, if any :
			if ($mod) {
			?>
				<p><?php printf( _('New files: %s'), count( $new_file ) ) ?></p>
				<p><?php printf( _('Deleted files: %s'), count( $del_file ) ) ?></p>
				<p><?php printf( _('Modified files: %s'), count( $mod_file ) ) ?></p>

				<form method="post">
					<p><input type="button" value="<?php echo _('View Changes') ?>" onClick="nftoggle();" class="btn btn-md btn-default btn-25" id="vcbtn" <?php
					if (! empty( $changes) ) {
						echo 'disabled="disabled" ';
					}
					?>/>&nbsp;&nbsp;&nbsp;<input type="submit" name="dlmods" value="<?php echo _('Download Changes') ?>" class="btn btn-md btn-default btn-25" /></p>
				</form>
				<br />


			<?php

				if ( empty( $changes ) ) {
					echo '<div id="changes_table" style="display:none">';
				} else {
					echo '<div id="changes_table">';
				}
				echo '<table border="0" width="100%">';
				$more_info = _('Click a file to get more info about it.');
				if ( $new_file ) {
					echo '<tr><td><br />';
					echo _('New files:') .' '. count( $new_file ) .'<br />';
					echo '<select name="sometext" multiple="multiple" class="form-control" style="height:150px" onClick="file_info(this.value, 1);">';
					foreach($new_file as $k => $v) {
						echo '<option value="' . htmlspecialchars( $v ) . '" title="' . htmlspecialchars( $k ) . '">' . htmlspecialchars( $k ) . '</option>';
					}
					echo '</select>
					<br /><i class="tinyblack">'. $more_info . '</i><br />
					<div id="table_new" style="display:none">
						<table class="table table-borderless" style="border:solid 1px #DFDFDF;">
							<tr>
								<td style="padding:3px;width:25%;">' . _('Size') .'</td>
								<td style="padding:3px" id="new_size"></td>
							</tr>
							<tr>
								<td style="padding:3px;width:25%;">' . _('Access') .'</td>
								<td style="padding:3px" id="new_chmod"></td>
							</tr>
							<tr>
								<td style="padding:3px;width:25%;">' . _('Uid / Gid') .'</td>
								<td style="padding:3px" id="new_uidgid"></td>
							</tr>
							<tr>
								<td style="padding:3px;width:25%;">' . _('Modify (mtime)') .'</td>
								<td style="padding:3px" id="new_mtime"></td>
							</tr>
							<tr>
								<td style="padding:3px;width:25%;">' . _('Change (ctime)') .'</td>
								<td style="padding:3px" id="new_ctime"></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>';
				}
				if ( $del_file ) {
					echo '
			<tr>
				<td><br />' . _('Deleted files:') .' '. count( $del_file ). '<br />' .
					'<select name="sometext" multiple="multiple" class="form-control" style="height:150px">';
					foreach( $del_file as $k => $v ) {
						echo '<option title="' . htmlspecialchars( $k ) . '">' . htmlspecialchars( $k ) . '</option>';
					}
					echo'</select>
				</td>
			</tr>';
				}
				if ( $mod_file ) {
					echo '
			<tr>
				<td><br />' . _('Modified files:') .' '. count( $mod_file ). '<br />'.
					'<select name="sometext" multiple="multiple" class="form-control" style="height:150px" onClick="file_info(this.value, 2);">';
					foreach($mod_file as $k => $v) {
						echo '<option value="' . htmlspecialchars( $v ) . '" title="' . htmlspecialchars( $k ) . '">' . htmlspecialchars( $k ) . '</option>';
					}
					echo'</select>
					<br /><i class="tinyblack">'. $more_info . '</i><br />
					<div id="table_mod" style="display:none">
						<table class="table table-borderless" style="border:solid 1px #DFDFDF;">
							<tr>
								<td style="padding:3px;width:25%;">&nbsp;</td>
								<td style="padding:3px"><b>' . _('Old') .'</b></td>
								<td style="padding:3px"><b>' . _('New') .'</b></td>
							</tr>
							<tr>
								<td style="padding:3px;width:25%;">' . _('Size') .'</td>
								<td style="padding:3px" id="mod_size"></td>
								<td style="padding:3px" id="mod_size2"></td>
							</tr>
							<tr>
								<td style="padding:3px;width:25%;">' . _('Access') .'</td>
								<td style="padding:3px" id="mod_chmod"></td>
								<td style="padding:3px" id="mod_chmod2"></td>
							</tr>
							<tr>
								<td style="padding:3px;width:25%;">' . _('Uid / Gid') .'</td>
								<td style="padding:3px" id="mod_uidgid"></td>
								<td style="padding:3px" id="mod_uidgid2"></td>
							</tr>
							<tr>
								<td style="padding:3px;width:25%;">' . _('Modify (mtime)') .'</td>
								<td style="padding:3px" id="mod_mtime"></td>
								<td style="padding:3px" id="mod_mtime2"></td>
							</tr>
							<tr>
								<td style="padding:3px;width:25%;">' . _('Change (ctime)') .'</td>
								<td style="padding:3px" id="mod_ctime"></td>
								<td style="padding:3px" id="mod_ctime2"></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>';
				}
			echo '</table></div>';

			} else {
				echo _('None');
			}
			?>
			</td>
		</tr>
	</table>

	<br />

	<form method="post">
		<input type="hidden" name="mid" value="<?php echo $GLOBALS['mid'] ?>">
		<input type="hidden" name="nfw_act" value="scan" />
		<p style="text-align:center;"><input type="submit" class="btn btn-md btn-success btn-25" value="<?php echo _('Scan System For File Changes') ?> &#187;" /></p>
	</form>
</div>

<?php
html_footer();

exit;

// ---------------------------------------------------------------------

function nf_sub_monitoring_create( $nfmon_snapshot ) {

	global $nfw_options;

	// Check POST data:
	if ( empty( $_POST['snapdir'] ) ) {
		return _('Enter the full path to the directory to be scanned.');
	}
	if ( strlen( $_POST['snapdir'] ) > 1 ) {
		$_POST['snapdir'] = trim( $_POST['snapdir'], ' ' );
		$_POST['snapdir'] = rtrim( $_POST['snapdir'], '/' );
	}
	if (! file_exists( $_POST['snapdir'] ) ) {
		return sprintf( _('The directory "%s" does not exist.'), htmlspecialchars( $_POST['snapdir'] ) );
	}
	if (! is_readable( $_POST['snapdir'] ) ) {
		return sprintf( 'The directory "%s" is not readable.', htmlspecialchars( $_POST['snapdir'] ) );
	}
	if ( isset( $_POST['snapnoslink'] ) ) {
		$snapnoslink = 1;
	} else {
		$snapnoslink = 0;
	}

	$snapexclude = '';
	if (! empty( $_POST['snapexclude'] ) ) {
		$_POST['snapexclude'] = trim( $_POST['snapexclude'] );
		$_POST['snapexclude'] = preg_replace( '/\s*,\s*/', ',', $_POST['snapexclude'] );
		$tmp = preg_quote( $_POST['snapexclude'], '/' );
		$snapexclude = str_replace( ',', '|', $tmp );
	}

	@ini_set( 'max_execution_time', 0 );
	$snapproc = microtime( true );

	if ( $fh = fopen( $nfmon_snapshot, 'w' ) ) {
		fwrite( $fh, '<?php die("Forbidden"); ?>' . "\n" );
		$res = scd( $_POST['snapdir'], $snapexclude, $fh, $snapnoslink );
		fclose( $fh );

		// Error ?
		if ( $res ) {
			if ( file_exists( $nfmon_snapshot ) ) {
				unlink( $nfmon_snapshot );
			}
			return $res;
		}

		// Save scan dir :
		$nfw_options['snapproc'] = round( microtime( true ) - $snapproc, 2 );
		$nfw_options['snapexclude'] = $_POST['snapexclude'];
		$nfw_options['snapdir'] = $_POST['snapdir'];
		$nfw_options['snapnoslink'] = $snapnoslink;

		$res = save_config( $nfw_options, 'options' );
		if (! empty( $res ) ) {
			return $res;
		}

	} else {
		return sprintf( 'Cannot write to "%s".', $nfmon_snapshot );
	}
}

// ---------------------------------------------------------------------

function scd( $snapdir, $snapexclude, $fh, $snapnoslink ) {

	if ( is_readable($snapdir ) ) {
		if ( $dh = opendir($snapdir ) ) {
			while ( FALSE !== ( $file = readdir( $dh ) ) ) {
				if ( $file == '.' || $file == '..') { continue; }
				$full_path = $snapdir . '/' . $file;
				if ( $snapexclude ) {
					if ( preg_match( "/$snapexclude/", $full_path ) ) { continue; }
				}
				if ( is_readable( $full_path ) ) {
					if ( $snapnoslink && is_link( $full_path ) ) { continue; }
					if ( is_dir( $full_path ) ) {
						scd( $full_path, $snapexclude, $fh, $snapnoslink );
					} elseif ( is_file( $full_path ) ) {
						$file_stat = stat( $full_path );
						fwrite( $fh, $full_path . '::' . sprintf ( "%04o", $file_stat['mode'] & 0777 ) .
							':' . $file_stat['uid'] . ':' .	$file_stat['gid'] . ':' . $file_stat['size'] .
							':' . $file_stat['mtime'] . ':' . $file_stat['ctime'] . "\n" );
					}
				}
			}
			closedir( $dh );
		} else {
			return sprintf( _('Cannot open "%s" directory'), htmlspecialchars( $snapdir ) );
		}
	} else {
		return sprintf( 'The directory "%s" is not readable.', htmlspecialchars( $snapdir ) );
	}
}

// ---------------------------------------------------------------------

function nf_sub_monitoring_scan( $nfmon_snapshot, $nfmon_diff ) {

	global $nfw_options;

	if ( empty( $nfw_options['enabled'] ) ) { return; }

	@ini_set( 'max_execution_time', 0 );

	if (! isset( $nfw_options['snapexclude'] ) || ! isset( $nfw_options['snapdir'] ) ||
		! isset( $nfw_options['snapnoslink'] ) ) {

		return sprintf( _('Missing options line %s, please try again.'), __LINE__ );
	}
	$tmp = preg_quote( $nfw_options['snapexclude'], '/' );
	$snapexclude = str_replace(',', '|', $tmp);

	if ( $fh = fopen( $nfmon_snapshot . '_tmp', 'w' ) ) {
		fwrite( $fh, '<?php die("Forbidden"); ?>' . "\n" );
		$res = scd( $nfw_options['snapdir'], $snapexclude, $fh, $nfw_options['snapnoslink'] );
		fclose( $fh );
	} else {
		return sprintf( 'Unable to create "%s".', $nfmon_snapshot . '_tmp' );
	}

	// Error ?
	if ( $res ) {
		if ( file_exists( $nfmon_snapshot . '_tmp' ) ) {
			unlink( $nfmon_snapshot . '_tmp' );
		}
		return $res;
	}

	// Compare both snapshots :

	$old_files = array(); $file = array(); $new_files = array();
	$modified_files = array(); $match = array();

	if (! $fh = fopen( $nfmon_snapshot, 'r' ) ) {
		return sprintf( _('Error reading old snapshot file.'), __LINE__ );
	}
	while (! feof( $fh ) ) {
		$match = explode( '::', rtrim(fgets( $fh ) ) . '::' );
		if (! empty( $match[1] ) ) {
			$old_files[$match[0]] = $match[1];
		}
	}
	fclose( $fh );

	if (! $fh = fopen( $nfmon_snapshot . '_tmp', 'r' ) ) {
		return sprintf( _('Error reading new snapshot file.'), __LINE__ );
	}
	while (! feof( $fh ) ) {
		$match = explode( '::', rtrim( fgets( $fh ) ) . '::' );

		if ( empty( $match[1] ) ) {
			continue;
		}
		// New file ?
		if ( empty( $old_files[$match[0]] ) ) {
			$new_files[$match[0]] = $match[1];
			continue;
		}
		// Modified file ?
		if ( $old_files[$match[0]] !=	$match[1] ) {
			 $modified_files[$match[0]] = $old_files[$match[0]] . '::' . $match[1];
		}
		// Delete it from old files list :
		unset( $old_files[$match[0]] );
	}
	fclose ( $fh );

	// Write changes to file, if any :
	if ( $new_files || $modified_files || $old_files ) {

		$fh = fopen( $nfmon_diff, 'w' );
		fwrite( $fh, '<?php die("Forbidden"); ?>' . "\n" );

		if ( $new_files ) {
			foreach ( $new_files as $fkey => $fvalue ) {
				fwrite( $fh, $fkey . '::N::' . $fvalue . "\n" );
			}
		}
		if ( $modified_files ) {
			foreach ( $modified_files as $fkey => $fvalue ) {
				fwrite( $fh, $fkey . '::M::' . $fvalue . "\n" );
			}
		}
		if ( $old_files ) {
			foreach ( $old_files as $fkey => $fvalue ) {
				fwrite( $fh, $fkey . '::D::' . $fvalue . "\n" );
			}
		}
		fclose( $fh );
		rename( $nfmon_snapshot . '_tmp', $nfmon_snapshot );

	} else {
		if ( file_exists( $nfmon_diff ) ) {
			// Keep last changes :
			rename( $nfmon_diff, $nfmon_diff. '.php' );
		}
		unlink( $nfmon_snapshot . '_tmp');
	}
}

// ---------------------------------------------------------------------
// EOF
