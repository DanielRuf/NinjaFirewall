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

$log_dir = './nfwlog/';
$monthly_log = 'firewall_'. date( 'Y-m' ) .'.php';

// Create it, if it does not exist:
if ( ! file_exists( $log_dir . $monthly_log ) ) {
	nf_sub_log_create( $log_dir . $monthly_log );
}

// Export log?
if ( isset( $_GET['nfw_export'] ) && ! empty( $_GET['nfw_logname'] ) ) {
	nf_sub_log_export( $log_dir );
}

// Make sure the current monthly log and dir are writable
// or display a warning:
if (! is_writable( $log_dir . $monthly_log ) ) {
	$write_err = sprintf(
		_('The current log (%s) is not writable. Please change its permission.'),
		htmlspecialchars( $log_dir . $monthly_log )
	);

} elseif (! is_writable( $log_dir ) ) {
	$write_err = sprintf(
		_('The log directory (%s) is not writable. Please change its permission.'),
		htmlspecialchars($log_dir )
	);
}

// Get the list of local logs:
global $available_logs;
$available_logs = nf_sub_log_find_local( $log_dir );

// Options:
if (! empty( $_POST['nfw_act'] ) ) {

	$ok_msg = '';

	// Save options:
	if ( $_POST['nfw_act'] == 'save_options') {

		$err_msg = nf_sub_log_save_options( $nfw_options );
		if (! $err_msg ) {
			$ok_msg = _('Your changes were saved.');
		}

	// Save/delete public key:
	} elseif ( $_POST['nfw_act'] == 'pubkey') {

		// Clear the key ?
		if (isset( $_POST['delete_pubkey'] ) ) {
			$_POST['nfw_options']['clogs_pubkey'] = '';
			$ok_msg = _('Your public key has been deleted.');
		} else {
			$ok_msg = _('Your public key has been saved.');
		}

		$err_msg = nf_sub_log_save_pubkey( $nfw_options );
		if (! $err_msg && ! $ok_msg ) {
			$ok_msg = _('Your changes were saved.');
		}
	}
}

// We will only display the last $max_lines lines,
// and will warn about it if the log is bigger :
if ( empty( $nfw_options['log_line'] ) || ! ctype_digit( $nfw_options['log_line'] ) ) {
	$max_lines = $nfw_options['log_line'] = 1500;
} else {
	$max_lines = $nfw_options['log_line'];
}

// View, delete, download etc actions:
if ( isset( $_GET['nfw_logname'] ) ) {

	// Delete selected log :
	if ( isset( $_GET['nfw_delete'] ) ) {

		$err_msg = nf_sub_log_delete( $_GET['nfw_logname'], $log_dir, $monthly_log );
		if (! $err_msg ) {
			$ok_msg = _('The selected log was deleted.');
		}
		// Delete its name from the list:
		unset( $available_logs[$_GET['nfw_logname']] );
		// Fall back to the current month log:
		$_GET['nfw_logname'] = $monthly_log;
		$available_logs[$_GET['nfw_logname']] = 1;
		krsort( $available_logs );
	}

	$data = nf_sub_log_read_local( $_GET['nfw_logname'], $log_dir, $max_lines-1 );
}

if ( isset( $_GET['nfw_logname'] ) && ! empty( $available_logs[$_GET['nfw_logname']] ) ) {

	$selected_log = $_GET['nfw_logname'];
} else {
	// Something wrong here, show the current month log instead:
	$selected_log = $monthly_log;
	$data = nf_sub_log_read_local( $monthly_log, $log_dir, $max_lines-1 );
}

html_header();

?>
<div class="col-sm-12 text-left">
	<h3><?php echo _('Logs > Firewall Log') ?></h3>
	<br />
<?php

// Display all error and notice messages:
if ( ! empty( $write_err ) ) {
	echo '<div class="alert alert-danger text-left"><a class="close" '.
		'data-dismiss="alert" aria-label="close">&times;</a>'. $write_err .'</div>';
}

if ( ! empty( $ok_msg ) ) {
	echo '<div class="alert alert-success text-left"><a class="close" data-dismiss="alert"'.
		' aria-label="close">&times;</a>'. $ok_msg . '</div>';
}
if ( ! empty( $err_msg ) ) {
	echo '<div class="alert alert-danger text-left"><a class="close" '.
		'data-dismiss="alert" aria-label="close">&times;</a>'. $err_msg .'</div>';
}
if ( isset( $data['lines'] ) && $data['lines'] > $max_lines ) {
	echo '<div class="alert alert-warning text-left"><a class="close" '.
		'data-dismiss="alert" aria-label="close">&times;</a>'.
		sprintf(
			_('Your log has %s lines. I will display the last %s lines only.'),
			$data['lines'], $max_lines
		) .'</div>';
}

?>
	<table width="100%" class="table table-nf">
		<tr>
			<td>

				<center><?php echo _('Viewing:') ?> <select name="nfw_logname" class="form-control" onChange='window.location="?mid=<?php echo $GLOBALS['mid'] ?>&token=<?php echo $_REQUEST['token'] ?>&nfw_logname=" + this.value;' style="width:250px;display:inline">';
<?php

// Add select box:
foreach ( $available_logs as $log_name => $tmp ) {
	echo '<option value="' . $log_name . '"';
	if ( $selected_log == $log_name ) {
		echo ' selected';
	}
	$log_stat = stat( $log_dir . $log_name );
	echo '>' . str_replace( '.php', '', $log_name ) .' ('. number_format( $log_stat['size'] ) .' '. _('bytes') . ')</option>';
}

echo '</select>';
// Enable export/delete buttons only if it is not empty:
if ( ! empty( $data['lines'] ) ) {
	echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="btn btn-md btn-default btn-25" value="'. _('Export') .'" onclick=\'window.location="?mid='. $GLOBALS['mid'] .'&token='. $_REQUEST['token'] .'&nfw_export=1&nfw_logname='. $selected_log .'"\'>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="btn btn-md btn-default btn-25" value="'. _('Delete') .'" onclick=\'if (confirm("'. _('Delete log?') .'")){window.location="?mid='. $GLOBALS['mid'] .'&token='. $_REQUEST['token'] .'&nfw_logname='. $selected_log .'&nfw_delete=1"}\'>';
} else {
	echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="btn btn-md btn-default btn-25" disabled="disabled" value="'. _('Export') .'" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="btn btn-md btn-default btn-25" disabled="disabled" value="'.  _('Delete') .'"  />';
}
echo '</center>';

$levels = array( '', 'MEDIUM', 'HIGH', 'CRITICAL', 'ERROR', 'UPLOAD', 'INFO', 'DEBUG_ON' );

?>
<script>
// We remove the '&nfw_delete=1' query string because if the user reloaded the page,
// that would delete the log again:
var url = window.location.href;
window.history.replaceState({}, document.title, url.replace( /&nfw_delete=1/, '' ) );

var myToday = '<?php echo date( 'd/M/y') ?>';
var myArray = new Array();
<?php

$i = 0;
$logline = '';
$severity = array( 0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);

if ( isset( $data['log'] ) && is_array( $data['log'] ) ) {
	foreach ( $data['log'] as $line ) {
		if ( preg_match( '/^\[(\d{10})\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(#\d{7})\]\s+\[(\d+)\]\s+\[(\d)\]\s+\[([\d.:a-fA-Fx, ]+?)\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(hex:|b64:)?(.+)\]$/', $line, $match ) ) {
			if ( empty( $match[4]) ) { $match[4] = '-'; }
			if ( $match[10] == 'hex:' ) { $match[11] = pack('H*', $match[11]); }
			if ( $match[10] == 'b64:' ) { $match[11] = base64_decode( $match[11]); }
			$res = date( 'd/M/y H:i:s', $match[1] ) . '  ' . $match[3] . '  ' .
			str_pad( $levels[$match[5]], 8 , ' ', STR_PAD_RIGHT) .'  ' .
			str_pad( $match[4], 4 , ' ', STR_PAD_LEFT) . '  ' . str_pad( $match[6], 15, ' ', STR_PAD_RIGHT) . '  ' .
			$match[7] . ' ' . $match[8] . ' - ' .	$match[9] . ' - [' . $match[11] . '] - ' . $match[2];
			echo 'myArray[' . $i . '] = "' . rawurlencode($res) . '";' . "\n";
			$logline .= htmlentities( $res ."\n" );
			++$i;
			// Keep track of severity levels :
			$severity[$match[5]] = 1;
		}
	}
}
?>
</script>
				<br />
				<form name="frmlog">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="100%" align="center">
								<textarea name="txtlog" class="form-control" style="background-color:#ffffff;width:95%;height:250px;font-family:monospace;font-size:14px;" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?php
								if ( ! empty( $logline ) ) {
									echo '       DATE         INCIDENT  LEVEL     RULE     IP            REQUEST' . "\n";
									echo $logline;
								} else {
									if (! empty( $data['err_msg'] ) ) {
										echo "\n\n > {$data['err_msg']}";
									} else {
										echo "\n\n > ". _('The selected log is empty');
									}
								}
								?></textarea>
								<br />
								<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_today" onClick="filter_log();">&nbsp;<?php echo _('Today') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
								<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_crit" onClick="filter_log();"<?php checked($severity[3], 1) ?>>&nbsp;<?php echo _('Critical') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
								<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_high" onClick="filter_log();"<?php checked($severity[2], 1) ?>>&nbsp;<?php echo _('High') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
								<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_med" onClick="filter_log();"<?php checked($severity[1], 1) ?>>&nbsp;<?php echo _('Medium') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
								<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_upl" onClick="filter_log();"<?php checked($severity[5], 1) ?>>&nbsp;<?php echo _('Uploads') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
								<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_nfo" onClick="filter_log();"<?php checked($severity[6], 1) ?>>&nbsp;<?php echo _('Info') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
								<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_dbg" onClick="filter_log();"<?php checked($severity[7], 1) ?>>&nbsp;<?php echo _('Debug') ?></label>
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>

	<br />
<?php

// Log options:
nf_sub_log_options( $max_lines );

echo '</div>';

html_footer();

// ---------------------------------------------------------------------

function nf_sub_log_options( $max_lines ) {

	// Need to refresh options :
	global $nfw_options;

	if ( empty( $nfw_options['logging'] ) ) {
		$nfw_options['logging'] = 0;
		$img = glyphicon('warning');
	} else {
		$nfw_options['logging'] = 1;
		$img = '&nbsp;';
	}
	if ( empty( $nfw_options['log_rotate'] ) ) {
		$nfw_options['log_rotate'] = 0;
		$nfw_options['log_maxsize'] = 2;
	} else {
		// Default : rotate at the end of the month OR if bigger than 5MB
		$nfw_options['log_rotate'] = 1;
		if ( empty( $nfw_options['log_maxsize'] ) || ! ctype_digit( $nfw_options['log_maxsize'] ) ) {
			$nfw_options['log_maxsize'] = 2;
		} else {
			$nfw_options['log_maxsize'] = intval( $nfw_options['log_maxsize'] / 1048576 );
			if (empty( $nfw_options['log_maxsize'] ) ) {
				$nfw_options['log_maxsize'] = 2;
			}
		}
	}
	if ( empty( $nfw_options['syslog'] ) ) {
		$nfw_options['syslog'] = 0;
	} else {
		$nfw_options['syslog'] = 1;
	}
?>
	<form method="post" action="?mid=<?php echo $GLOBALS['mid'] ?>">
		<input type="hidden" name="token" value="<?php echo $_REQUEST['token'] ?>" />
		<h4><?php echo _('Options') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="40%" align="left"><?php echo _('Enable firewall log') ?></td>
				<td width="5%" align="center"><?php echo $img ?></td>
				<td width="55%" align="left">
					<p><label><input type="radio" name="logging" value="1"<?php checked($nfw_options['logging'], 1) ?>>&nbsp;<?php echo _('Yes') .' '. _('(default)') ?></label></p>
					<p><label><input type="radio" name="logging" value="0"<?php checked($nfw_options['logging'], 0) ?>>&nbsp;<?php echo _('No') ?></label></p>
				</td>
			</tr>
			<tr>
				<td width="40%" align="left"><?php echo _('Auto-rotate log') ?></td>
				<td width="5%" align="center">&nbsp;</td>
				<td width="55%" align="left">
					<p><label><input type="radio" name="log_rotate" value="1"<?php checked($nfw_options['log_rotate'], 1) ?>>&nbsp;<?php printf(_('1st day of the month, or if bigger than %s MB'), '</label>&nbsp;<input class="form-control" style="width:70px;display:inline" min="1" id="sizeid" name="log_maxsize" size="2" maxlength="2" value="' . $nfw_options['log_maxsize'] . '" onkeyup="is_number(\'sizeid\')" type="number">') ?> <?php echo _('(default)') ?></p>
					<p><label><input type="radio" name="log_rotate" value="0"<?php checked($nfw_options['log_rotate'], 0) ?>>&nbsp;<?php echo _('1st day of the month, regardless of its size') ?></label></p>
				</td>
			</tr>
			<tr>
				<td width="40%" align="left"><?php echo _('Show the most recent') ?></td>
				<td width="5%" align="center">&nbsp;</td>
				<td width="55%" align="left">
					<p><?php
						$lines = '<input name="nfw_options[log_line]" step="50" min="50" value="'. $max_lines .'" class="form-control" style="width:90px;display:inline" type="number">';
						printf( _('%s lines'), $lines );
					 ?></p>
				</td>
			</tr>
			<tr>
				<td width="40%" align="left"><?php echo _('Syslog') ?></td>
				<td width="5%" align="center">&nbsp;</td>
				<td width="55%" align="left">
				<?php
				// Ensure that openlog() and syslog() are not disabled:
				if (! function_exists( 'syslog' ) || ! function_exists( 'openlog' ) ) {
					$nfw_options['syslog'] = 0;
					$syslog_msg = _('Your server configuration is not compatible with that option.');
					$enabled = 0;
				} else {
					$syslog_msg = _('See contextual help before enabling this option.');
					$enabled = 1;
				}
				?>
					<p><label><input type="checkbox" name="nfw_options[syslog]" value="1"<?php checked( $nfw_options['syslog'], 1 ) ?><?php disabled( $enabled, 0 )?> />&nbsp;<?php echo _('Write events to the Syslog server too') ?></label>
					<br />
					<i><?php echo $syslog_msg ?></i>
				</td>
			</tr>
		</table>

		<input type="hidden" name="nfw_act" value="save_options" />
		<center><input type="submit" class="btn btn-md btn-success btn-25" name="savelog" value="<?php echo _('Save Options') ?>"></center>
	</form>
	<br />

	<a name="clogs"></a>
	<form name="frmlog2" method="post" action="?mid=<?php echo $GLOBALS['mid'] ?>" onsubmit="return check_key();">
		<input type="hidden" name="token" value="<?php echo $_REQUEST['token'] ?>" />
		<h4><?php echo _('Centralized Logging') ?></h4>
		<?php
		if ( empty( $nfw_options['clogs_pubkey'] ) || ! preg_match( '/^[a-f0-9]{40}:(?:[a-f0-9:.]{3,39}|\*)$/', $nfw_options['clogs_pubkey'] ) ) {
			$nfw_options['clogs_pubkey'] = '';
		}
		?>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="45%" align="left"><?php echo _('Enter your public key (optional)') ?></td>
				<td width="55%" align="left">
					<input class="form-control" type="text" style="width:100%" maxlength="80" name="nfw_options[clogs_pubkey]" value="<?php echo htmlspecialchars( $nfw_options['clogs_pubkey'] ) ?>" autocomplete="off" />
					<br />
					<i><?php
						printf(
							_('<a href="%s">Consult our blog</a> if you want to enable centralized logging.'),
							'https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/'
						);
					?></i>
					<p>
					<input type="submit" class="btn btn-sm btn-success btn-25" name="save_pubkey" onclick="what=0" value="<?php echo _('Save Public Key') ?>">
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="submit" class="btn btn-sm btn-danger btn-25" name="delete_pubkey"<?php disabled($nfw_options['clogs_pubkey'], '' ) ?> onclick="what=1" value="<?php echo _('Delete Public Key') ?>">
					</p>
				</td>
			</tr>
		</table>

		<input type="hidden" name="nfw_act" value="pubkey" />
	</form>
<?php
}

// ---------------------------------------------------------------------

function nf_sub_log_create( $log ) {

	// Create an empty log :
	file_put_contents( $log, "<?php exit; ?>\n" );

}

// ---------------------------------------------------------------------

function nf_sub_log_delete( $log, $log_dir, $monthly_log ) {

	global $nfw_options;
	$err_msg = '';

	if (! preg_match( '/^(firewall_\d{4}-\d\d(?:\.\d+)?\.)php$/', $log ) ) {
		$err_msg = _('Unable to delete the log') . ' (#1)';
	}
	if (! file_exists( $log_dir . $log) ) {
		$err_msg = _('Unable to delete the log') . ' (#2)';
	}

	$incident = mt_rand(1000000, 9000000);

	if (! $err_msg ) {
		// Delete the requested log:
		@unlink( $log_dir . $log );
		// Write the event to the current log:
		if (! file_exists( $log_dir . $monthly_log ) ) {
			nf_sub_log_create( $log_dir . $monthly_log );
		}
		$fh = fopen($log_dir . $monthly_log, 'a');
		fwrite( $fh, '[' . time() . '] [0] [' . $_SERVER['SERVER_NAME'] .
			'] [#'. $incident .'] [0] [6] ' . '[' . NFW_REMOTE_ADDR . '] ' .
			'[200 OK] ' . '[' . $_SERVER['REQUEST_METHOD'] . '] ' .
			'[' . $_SERVER['SCRIPT_NAME'] . '] ' . '[Log deleted by admin] ' .
			'[' . $nfw_options['admin_name'] . ': ' . $log . ']' . "\n"
		);
		fclose($fh);

		// Syslog?
		if (! empty( $nfw_options['syslog'] ) ) {
			@openlog( 'ninjafirewall', LOG_NDELAY|LOG_PID, LOG_USER );
			@syslog( LOG_NOTICE, "INFO: #{$incident}: Firewall log deleted by admin from ". NFW_REMOTE_ADDR . " on {$_SERVER['SERVER_NAME']}" );
			@closelog();
		}
	}

	return $err_msg;
}

// ---------------------------------------------------------------------

function nf_sub_log_find_local( $log_dir ) {

	// Find all available logs :
	$available_logs = array();
	if ( is_dir( $log_dir ) ) {
		if ( $dh = opendir( $log_dir ) ) {
			while ( ( $file = readdir( $dh ) ) !== false ) {
				if (preg_match( '/^(firewall_(\d{4})-(\d\d)(?:\.\d+)?\.php)$/', $file, $match ) ) {
					$available_logs[$match[1]] = 1;
				}
			}
			closedir($dh);
		}
	}
	krsort( $available_logs );

	return $available_logs;
}

// ---------------------------------------------------------------------

function nf_sub_log_save_options( $nfw_options ) {

	global $nfw_options;

	if (! empty( $_POST['savelog'] ) ) {
		// Update options :
		if (empty( $_POST['logging'] ) ) {
			$nfw_options['logging'] = 0;
		} else {
			$nfw_options['logging'] = 1;
		}
		if ( empty( $_POST['log_rotate'] ) ) {
			$nfw_options['log_rotate'] = 0;
			$nfw_options['log_maxsize'] = 2 * 1048576;
		} else {
			$nfw_options['log_rotate'] = 1;
			if ( empty( $_POST['log_maxsize'] ) || ! preg_match('/^([1-9]?[0-9])$/', $_POST['log_maxsize'] ) ) {
				$nfw_options['log_maxsize'] = 2 * 1048576;
			} else {
				$nfw_options['log_maxsize'] = $_POST['log_maxsize'] * 1048576;
			}
		}
		if ( empty( $_POST['nfw_options']['log_line']) || $_POST['nfw_options']['log_line'] < 50 || ! ctype_digit( $_POST['nfw_options']['log_line'] ) ) {
			$nfw_options['log_line'] = 1500;
		} else {
			$nfw_options['log_line'] = $_POST['nfw_options']['log_line'];
		}
		if (empty( $_POST['nfw_options']['syslog'] ) ) {
			$nfw_options['syslog'] = 0;
		} else {
			$nfw_options['syslog'] = 1;
		}

		// Save options:
		$res = save_config( $nfw_options, 'options' );
		if (! empty( $res ) ) {
			return $res;
		}
	}
}

// ---------------------------------------------------------------------

function nf_sub_log_save_pubkey( $nfw_options ) {

	global $nfw_options;

	if ( empty( $_POST['nfw_options']['clogs_pubkey'] ) ||
		! preg_match( '/^[a-f0-9]{40}:(?:[a-f0-9:.]{3,39}|\*)$/', $_POST['nfw_options']['clogs_pubkey'] ) ) {
		$nfw_options['clogs_pubkey'] = '';
	} else {
		$nfw_options['clogs_pubkey'] = $_POST['nfw_options']['clogs_pubkey'];
	}

	// Save options:
	$res = save_config( $nfw_options, 'options' );
	if (! empty( $res ) ) {
		return $res;
	}
}

// ---------------------------------------------------------------------

function nf_sub_log_read_local( $log, $log_dir, $max_lines ) {

	if (! preg_match( '/^(firewall_\d{4}-\d\d(?:\.\d+)?\.)php$/', trim( $log ) ) ) {
		die( _('Error') );
	}

	$data = array();
	$data['type'] = 'local';

	if (! file_exists( $log_dir . $log ) ) {
		$data['err_msg'] = _('You do not have any log for the current month yet.');
		return $data;
	}

	$data['log'] = file( $log_dir . $log, FILE_SKIP_EMPTY_LINES );

	if ( $data['log'] === false ) {
		$data['err_msg'] = _('Cannot open the log for read operation.');
		return $data;
	}
	if ( strpos( $data['log'][0], '<?php' ) !== FALSE ) {
		unset( $data['log'][0] );
	}
	// Keep only the last $max_lines:
	$data['lines'] = count( $data['log'] );
	if ( $max_lines < $data['lines'] ) {
		for ($i = 0; $i < ( $data['lines'] - $max_lines ); ++$i ) {
			unset( $data['log'][$i] ) ;
		}
	}

	if ( $data['lines'] == 0 ) {
		$data['err_msg'] = _('The selected log is empty.');
	}

	return $data;
}

// ---------------------------------------------------------------------

function nf_sub_log_export( $log_dir ) {

	$log = trim( $_GET['nfw_logname'] );
	if (! preg_match( '/^(firewall_\d{4}-\d\d(?:\.\d+)?\.)php$/', $log, $match ) ) {
		die('Unknown request (#1)');
	}
	$name = $match[1];
	if (! file_exists( $log_dir . $log ) ) {
		die('Unknown request (#2)');
	}
	$data = file( $log_dir . $log );
	$res = "Date\tIncident\tLevel\tRule\tIP\tRequest\tEvent\tHost\n";
	$levels = array( '', 'MEDIUM', 'HIGH', 'CRITICAL', 'ERROR', 'UPLOAD', 'INFO', 'DEBUG_ON' );
	$severity = array( 0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
	foreach( $data as $line ) {
		if ( preg_match( '/^\[(\d{10})\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(#\d{7})\]\s+\[(\d+)\]\s+\[(\d)\]\s+\[([\d.:a-fA-Fx, ]+?)\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(hex:|b64:)?(.+)\]$/', $line, $match ) ) {
			if ( empty( $match[4]) ) { $match[4] = '-'; }
			if ( $match[10] == 'hex:' ) { $match[11] = pack('H*', $match[11]); }
			if ( $match[10] == 'b64:' ) { $match[11] = base64_decode( $match[11]); }
			$res .= date( 'd/M/y H:i:s', $match[1] ) . "\t" . $match[3] . "\t" .
			$levels[$match[5]] . "\t" . $match[4] . "\t" . $match[6] . "\t" .
			$match[7] . ' ' . $match[8] . "\t" .	$match[9] .
			' - [' . $match[11] . "]\t" . $match[2] . "\n";
		}
	}
	header('Content-Type: text/tab-separated-values');
	header('Content-Length: '. strlen( $res ) );
	header('Content-Disposition: attachment; filename="' . $name . 'tsv"');
	echo $res;
	exit;

}

// ---------------------------------------------------------------------
// EOF
