<?php
/*
 +---------------------------------------------------------------------+
 | NinjaFirewall (Pro edition)                                         |
 |                                                                     |
 | (c) NinTechNet - http://nintechnet.com/                             |
 |                                                                     |
 +---------------------------------------------------------------------+
 | This program is free software: you can redistribute it and/or       |
 | modify it under the terms of the GNU General Public License as      |
 | published by the Free Software Foundation, either version 3 of      |
 | the License, or (at your option) any later version.                 |
 |                                                                     |
 | This program is distributed in the hope that it will be useful,     |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of      |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       |
 | GNU General Public License for more details.                        |
 +---------------------------------------------------------------------+
*/
if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

// Load current language file :
require (__DIR__ .'/lang/' . $nfw_options['admin_lang'] . '/' . basename(__FILE__) );

$log_dir = './nfwlog/';
$monthly_log = 'firewall_' . date( 'Y-m' ) . '.php';

// Create it, if it does not exist:
if ( ! file_exists( $log_dir . $monthly_log ) ) {
	nf_sub_log_create( $log_dir . $monthly_log );
}

// Export log?
if ( isset($_GET['nfw_export']) && ! empty($_GET['nfw_logname']) ) {
	nf_sub_log_export( $log_dir );
}

// Make sure the current monthly log and dir are writable
// or display a warning:
if (! is_writable( $log_dir . $monthly_log ) ) {
	$write_err = sprintf( $lang['log_readonly'], htmlspecialchars( $log_dir . $monthly_log ) );
} elseif (! is_writable( $log_dir ) ) {
	$write_err = sprintf( $lang['dir_readonly'], htmlspecialchars($log_dir ) );
}

// Get the list of local logs:
global $available_logs;
$available_logs = nf_sub_log_find_local( $log_dir );

// Options:
if (! empty( $_POST['nfw_act']) ) {

	$ok_msg = '';

	// Save options:
	if ( $_POST['nfw_act'] == 'save_options') {

		$err_msg = nf_sub_log_save_options( $nfw_options );
		if (! $err_msg ) {
			$ok_msg = $lang['saved_conf'];
		}

	// Save/delete public key:
	} elseif ( $_POST['nfw_act'] == 'pubkey') {

		// Clear the key ?
		if (isset( $_POST['delete_pubkey'] ) ) {
			$_POST['nfw_options']['clogs_pubkey'] = '';
			$ok_msg = $lang['deleted_key'];
		} else {
			$ok_msg = $lang['saved_key'];
		}

		$err_msg = nf_sub_log_save_pubkey( $nfw_options );
		if (! $err_msg && ! $ok_msg ) {
			$ok_msg = $lang['saved_conf'];
		}
	}
	// Reload options:
	@include(__DIR__ . '/conf/options.php');
}

// We will only display the last $max_lines lines,
// and will warn about it if the log is bigger :
if ( empty($nfw_options['log_line']) || ! ctype_digit($nfw_options['log_line']) ) {
	$max_lines = $nfw_options['log_line'] = 1500;
} else {
	$max_lines = $nfw_options['log_line'];
}

// View, delete, download etc actions:
if ( isset( $_GET['nfw_logname'] ) ) {

	// Delete selected log :
	if ( isset($_GET['nfw_delete']) ) {

		$err_msg = nf_sub_log_delete( $_GET['nfw_logname'], $log_dir, $monthly_log );
		if (! $err_msg ) {
			$ok_msg = $lang['log_deleted'];
		}
		// Delete its name from the list:
		unset( $available_logs[$_GET['nfw_logname']] );
		// Fall back to the current month log:
		$_GET['nfw_logname'] = $monthly_log;
		$available_logs[$_GET['nfw_logname']] = 1;
		krsort($available_logs);
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
// Some JS code:
nf_sub_log_js_header();

// Display all error and notice messages:
if ( ! empty( $write_err ) ) {
	echo '<br /><div class="error"><p>' . $lang['error'] . ': ' . $write_err . '</p></div>';
}

if ( ! empty( $ok_msg ) ) {
	echo '<br /><div class="success"><p>' . $ok_msg . '.</p></div>';
}
if ( ! empty( $err_msg ) ) {
	echo '<br /><div class="error"><p>' . $err_msg . '.</p></div>';
}
if ( isset( $data['lines'] ) && $data['lines'] > $max_lines ) {
	echo '<br /><div class="warning"><p>' . $lang['note'] . ': ' .
			sprintf( $lang['too_big'], $data['lines'], $max_lines ) . '</p></div>';
}

?>
<br />
<fieldset><legend>&nbsp;<b><?php echo $lang['log'] ?></b>&nbsp;</legend>
	<center><?php echo $lang['viewing'] ?> <select name="nfw_logname" class="input" onChange='window.location="?mid=<?php echo $GLOBALS['mid'] ?>&token=<?php echo $_REQUEST['token'] ?>&nfw_logname=" + this.value;'>';
<?php

// Add select box:
foreach ($available_logs as $log_name => $tmp) {
	echo '<option value="' . $log_name . '"';
	if ( $selected_log == $log_name ) {
		echo ' selected';
	}
	$log_stat = stat($log_dir . $log_name);
	echo '>' . str_replace('.php', '', $log_name) .' ('. number_format($log_stat['size']) .' '. $lang['bytes'] . ')</option>';
}
echo '</select>';
// Enable export/delete buttons only if it is not empty:
if ( ! empty( $data['lines'] ) ) {
	echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="'. $lang['js_exp_log'] .'" onclick=\'window.location="?mid='. $GLOBALS['mid'] .'&token='. $_REQUEST['token'] .'&nfw_export=1&nfw_logname='. $selected_log .'"\'>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="'. $lang['js_del_log'] .'" onclick=\'if (confirm("'. $lang['js_confirm'] .'")){window.location="?mid='. $GLOBALS['mid'] .'&token='. $_REQUEST['token'] .'&nfw_delete=1&nfw_logname='. $selected_log .'"}\'>';
} else {
	echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="button" disabled="disabled" value="'. $lang['js_exp_log'] .'" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" disabled="disabled" value="'.  $lang['js_del_log'] .'"  />';
}
echo '</center>';

$levels = array( '', 'medium', 'high', 'critical', 'error', 'upload', 'info', 'DEBUG_ON' );

?>

<script>
var myToday = '<?php echo date( 'd/M/y') ?>';
var myArray = new Array();
<?php

$i = 0;
$logline = '';
$severity = array( 0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);

if ( isset( $data['log'] ) && is_array( $data['log'] ) ) {
	foreach ( $data['log'] as $line ) {
		if ( preg_match( '/^\[(\d{10})\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(#\d{7})\]\s+\[(\d+)\]\s+\[(\d)\]\s+\[([\d.:a-fA-F, ]+?)\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(hex:)?(.+)\]$/', $line, $match ) ) {
			if ( empty( $match[4]) ) { $match[4] = '-'; }
			if ( $match[10] == 'hex:' ) { $match[11] = pack('H*', $match[11]); }
			$res = date( 'd/M/y H:i:s', $match[1] ) . '  ' . $match[3] . '  ' .
			str_pad( $levels[$match[5]], 8 , ' ', STR_PAD_RIGHT) .'  ' .
			str_pad( $match[4], 4 , ' ', STR_PAD_LEFT) . '  ' . str_pad( $match[6], 15, ' ', STR_PAD_RIGHT) . '  ' .
			$match[7] . ' ' . $match[8] . ' - ' .	$match[9] . ' - [' . $match[11] . '] - ' . $match[2];
			echo 'myArray[' . $i . '] = "' . rawurlencode($res) . '";' . "\n";
			$logline .= htmlentities( $res ."\n" );
			$i++;
			// Keep track of severity levels :
			$severity[$match[5]] = 1;
		}
	}
}
?>
function filter_log() {
	// Clear the log :
	document.frmlog.txtlog.value = '       DATE         INCIDENT  LEVEL     RULE     IP            REQUEST\n';
	// Prepare the regex :
	var nf_tmp = '';
	if ( document.frmlog.nf_crit.checked == true ) { nf_tmp += 'critical|'; }
	if ( document.frmlog.nf_high.checked == true ) { nf_tmp += 'high|'; }
	if ( document.frmlog.nf_med.checked == true )  { nf_tmp += 'medium|'; }
	if ( document.frmlog.nf_upl.checked == true )  { nf_tmp += 'upload|'; }
	if ( document.frmlog.nf_nfo.checked == true )  { nf_tmp += 'info|'; }
	if ( document.frmlog.nf_dbg.checked == true )  { nf_tmp += 'DEBUG_ON|'; }
	// Return if empty :
	if ( nf_tmp == '' ) {
		document.frmlog.txtlog.value = '\n > <?php echo $lang['js_nomatch'] ?>';
		return true;
	}
	// Put it all together :
	var nf_reg = new RegExp('^\\S+\\s+\\S+\\s+\\S+\\s+' + '(' + nf_tmp.slice(0, - 1) + ')' + '\\s');
	var nb = 0;
	var decodearray;
	for ( i = 0; i < myArray.length; i++ ) {
		decodearray = decodeURIComponent(myArray[i]);
		if ( document.frmlog.nf_today.checked == true ) {
			if (! decodearray.match(myToday) ) { continue;}
		}
		if ( decodearray.match(nf_reg) ) {
			// Display it :
			document.frmlog.txtlog.value += decodearray + '\n';
			nb++;
		}
	}
	if ( nb == 0 ) {
		document.frmlog.txtlog.value = '\n > <?php echo $lang['js_nomatch'] ?>';
	}
}
</script>
<br>
	<form name="frmlog">
		<table width="100%" class="smallblack" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100%" align="center">
					<textarea name="txtlog" style="background-color:#ffffff;width:95%;height:250px;border:1px solid #FDCD25;padding:4px;font-family:monospace;font-size:13px;" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?php
					if ( ! empty( $logline ) ) {
						echo '       DATE         INCIDENT  LEVEL     RULE     IP            REQUEST' . "\n";
						echo $logline;
					} else {
						if (! empty( $data['err_msg'] ) ) {
							echo "\n\n > {$data['err_msg']}";
						} else {
							echo "\n\n > {$lang['empty_log']}";
						}
					}
					?></textarea>
					<br />
					<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_today" onClick="filter_log();"><?php echo $lang['today'] ?></label>&nbsp;&nbsp;
					<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_crit" onClick="filter_log();"<?php checked($severity[3], 1) ?>><?php echo $lang['critical'] ?></label>&nbsp;&nbsp;
					<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_high" onClick="filter_log();"<?php checked($severity[2], 1) ?>><?php echo $lang['high'] ?></label>&nbsp;&nbsp;
					<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_med" onClick="filter_log();"<?php checked($severity[1], 1) ?>><?php echo $lang['medium'] ?></label>&nbsp;&nbsp;
					<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_upl" onClick="filter_log();"<?php checked($severity[5], 1) ?>><?php echo $lang['uploads'] ?></label>&nbsp;&nbsp;
					<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_nfo" onClick="filter_log();"<?php checked($severity[6], 1) ?>><?php echo $lang['info'] ?></label>&nbsp;&nbsp;
					<label><input <?php disabled( $logline, '' ) ?> type="checkbox" name="nf_dbg" onClick="filter_log();"<?php checked($severity[7], 1) ?>><?php echo $lang['debug'] ?></label>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<br />
<br />
<?php

// Log options:
nf_sub_log_options($max_lines);

html_footer();

/* ------------------------------------------------------------------ */

function nf_sub_log_options($max_lines) {

	// Need to refresh options :
	global $nfw_options;
	global$lang;

	if ( empty($nfw_options['logging']) ) {
		$nfw_options['logging'] = 0;
		$img = '<img src="static/icon_warn.png" border="0" width="21" heigt="21">';
	} else {
		$nfw_options['logging'] = 1;
		$img = '&nbsp;';
	}
	if ( empty($nfw_options['log_rotate']) ) {
		$nfw_options['log_rotate'] = 0;
		$nfw_options['log_maxsize'] = 2;
	} else {
		// Default : rotate at the end of the month OR if bigger than 5MB
		$nfw_options['log_rotate'] = 1;
		if ( empty($nfw_options['log_maxsize']) || ! ctype_digit($nfw_options['log_maxsize']) ) {
			$nfw_options['log_maxsize'] = 2;
		} else {
			$nfw_options['log_maxsize'] = intval( $nfw_options['log_maxsize'] / 1048576);
			if (empty( $nfw_options['log_maxsize']) ) {
				$nfw_options['log_maxsize'] = 2;
			}
		}
	}
?>
<form method="post" action="?mid=<?php echo $GLOBALS['mid'] ?>">
<input type="hidden" name="token" value="<?php echo $_REQUEST['token'] ?>" />
<fieldset><legend>&nbsp;<b><?php echo $lang['log_options'] ?></b>&nbsp;</legend>
	<table width="100%" class="smallblack" border="0" cellpadding="10" cellspacing="0">
		<tr>
			<td width="55%" align="left"><?php echo $lang['enable_log'] ?></td>
			<td width="45%" align="left">
				<p><label><input type="radio" name="logging" value="1"<?php checked($nfw_options['logging'], 1) ?>>&nbsp;<?php echo $lang['yes'] . $lang['default'] ?></label></p>
				<p><label><input type="radio" name="logging" value="0"<?php checked($nfw_options['logging'], 0) ?>>&nbsp;<?php echo $lang['no'] ?></label>&nbsp;<?php echo $img ?></p>
			</td>
		</tr>
		<tr>
			<td width="55%" align="left" class="dotted"><?php echo $lang['auto_rotate'] ?></td>
			<td width="45%" align="left" class="dotted">
				<p><label><input type="radio" name="log_rotate" value="1"<?php checked($nfw_options['log_rotate'], 1) ?>>&nbsp;<?php printf($lang['rotate_size'], '</label>&nbsp;<input class="input" id="sizeid" name="log_maxsize" size="2" maxlength="2" value="' . $nfw_options['log_maxsize'] . '" onkeyup="is_number(\'sizeid\')" type="text">') ?> <?php echo $lang['default'] ?></p>
				<p><label><input type="radio" name="log_rotate" value="0"<?php checked($nfw_options['log_rotate'], 0) ?>>&nbsp;<?php echo $lang['rotate'] ?></label></p>
			</td>
		</tr>
		<tr>
			<td width="55%" align="left" class="dotted"><?php echo $lang['max_line_1'] ?></td>
			<td width="45%" align="left" class="dotted">
				<p><input name="nfw_options[log_line]" step="50" min="50" value="<?php echo $max_lines ?>" class="input" type="number"> <?php echo $lang['max_line_2'] ?></p>
			</td>
		</tr>
	</table>
</fieldset>
	<br />
	<input type="hidden" name="nfw_act" value="save_options" />
	<center><input type="submit" class="button" name="savelog" value="<?php echo $lang['save_conf'] ?>"></center>
</form>
<br />

<a name="clogs"></a>
<form name="frmlog2" method="post" action="?mid=<?php echo $GLOBALS['mid'] ?>" onsubmit="return check_key();">
<input type="hidden" name="token" value="<?php echo $_REQUEST['token'] ?>" />
<fieldset><legend>&nbsp;<b><?php echo $lang['cent_log'] ?></b>&nbsp;</legend>
	<?php

	if ( empty( $nfw_options['clogs_pubkey'] ) || ! preg_match( '/^[a-f0-9]{40}:(?:[a-f0-9:.]{3,39}|\*)$/', $nfw_options['clogs_pubkey'] ) ) {
		$nfw_options['clogs_pubkey'] = '';
	}

	?>
	<table width="100%" class="smallblack" border="0" cellpadding="10" cellspacing="0">
		<tr>
			<td width="55%" align="left"><?php echo $lang['enter_key'] ?></td>
			<td width="45%" align="left">
				<input class="input" type="text" style="width:100%" maxlength="80" name="nfw_options[clogs_pubkey]" value="<?php echo htmlspecialchars( $nfw_options['clogs_pubkey'] ) ?>" autocomplete="off" />
				<br /><i><?php printf( $lang['blog_doc'], '<a href="https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/" class="links" style="border-bottom:dotted 1px #FDCD25;">', '</a>' ) ?></i>
			</td>
		</tr>
	</table>

</fieldset>
<br />
<center>
	<input type="submit" class="button" name="save_pubkey" onclick="what=0" value="<?php echo $lang['save_key'] ?>">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="submit" class="button" name="delete_pubkey"<?php disabled($nfw_options['clogs_pubkey'], '' ) ?> onclick="what=1" value="<?php echo $lang['delete_key'] ?>">
</center>
<input type="hidden" name="nfw_act" value="pubkey" />
</form>
<br />

<?php
}

/* ------------------------------------------------------------------ */
function nf_sub_log_js_header() {

	global $lang;

?>
<script>
function is_number(id) {
	var e = document.getElementById(id);
	if (! e.value ) { return }
	if (! /^[0-9]+$/.test(e.value) ) {
		alert("<?php echo $lang['js_digit'] ?>");
		e.value = e.value.substring(0, e.value.length-1);
	}
}
var what;
function check_key() {
	// Ignore the request if user only wants to delete the key:
	if (what == 1) { return true; }
	var pubkey = document.frmlog2.elements["nfw_options[clogs_pubkey]"];
	if (! pubkey.value.match( /^[a-f0-9]{40}:(?:[a-f0-9:.]{3,39}|\*)$/) ) {
		pubkey.focus();
		alert("<?php echo $lang['invalid_key'] ?>");
		return false;
	}
}
</script>
<?php
}

/* ------------------------------------------------------------------ */

function nf_sub_log_create( $log ) {

	// Create an empty log :
	file_put_contents( $log, "<?php exit; ?>\n" );

}

/* ------------------------------------------------------------------ */

function nf_sub_log_delete( $log, $log_dir, $monthly_log ) {

	global $nfw_options, $lang;
	$err_msg = '';

	if (! preg_match( '/^(firewall_\d{4}-\d\d(?:\.\d+)?\.)php$/', $log ) ) {
		$err_msg = $lang['cannot_delete'] . ' (#1)';
	}
	if (! file_exists( $log_dir . $log) ) {
		$err_msg = $lang['cannot_delete'] . ' (#2)';
	}
	if (! $err_msg ) {
		// Delete the requested log:
		@unlink($log_dir . $log);
		// Write the event to the current log:
		if (! file_exists($log_dir . $monthly_log) ) {
			nf_sub_log_create( $log_dir . $monthly_log );
		}
		$fh = fopen($log_dir . $monthly_log, 'a');
		fwrite( $fh, '[' . time() . '] [0] [' . $_SERVER['SERVER_NAME'] .
			'] [#0000000] [0] [6] ' . '[' . NFW_REMOTE_ADDR . '] ' .
			'[200 OK] ' . '[' . $_SERVER['REQUEST_METHOD'] . '] ' .
			'[' . $_SERVER['SCRIPT_NAME'] . '] ' . '[Log deleted by admin] ' .
			'[' . $nfw_options['admin_name'] . ': ' . $log . ']' . "\n"
		);
		fclose($fh);
	}

	return $err_msg;

}

/* ------------------------------------------------------------------ */

function nf_sub_log_find_local( $log_dir ) {

	// Find all available logs :
	$available_logs = array();
	if ( is_dir( $log_dir ) ) {
		if ( $dh = opendir( $log_dir ) ) {
			while ( ($file = readdir($dh) ) !== false ) {
				if (preg_match( '/^(firewall_(\d{4})-(\d\d)(?:\.\d+)?\.php)$/', $file, $match ) ) {
					$available_logs[$match[1]] = 1;
				}
			}
			closedir($dh);
		}
	}
	krsort($available_logs);

	return $available_logs;
}

/* ------------------------------------------------------------------ */

function nf_sub_log_save_options( $nfw_options ) {

	global $lang, $nfw_options;

	if (! empty($_POST['savelog']) ) {
		// Update options :
		if (empty( $_POST['logging']) ) {
			$nfw_options['logging'] = 0;
		} else {
			$nfw_options['logging'] = 1;
		}
		if ( empty($_POST['log_rotate']) ) {
			$nfw_options['log_rotate'] = 0;
			$nfw_options['log_maxsize'] = 2 * 1048576;
		} else {
			$nfw_options['log_rotate'] = 1;
			if ( empty($_POST['log_maxsize']) || ! preg_match('/^([1-9]?[0-9])$/', $_POST['log_maxsize']) ) {
				$nfw_options['log_maxsize'] = 2 * 1048576;
			} else {
				$nfw_options['log_maxsize'] = $_POST['log_maxsize'] * 1048576;
			}
		}
		if ( empty($_POST['nfw_options']['log_line']) || $_POST['nfw_options']['log_line'] < 50 || ! ctype_digit($_POST['nfw_options']['log_line']) ) {
			$nfw_options['log_line'] = 1500;
		} else {
			$nfw_options['log_line'] = $_POST['nfw_options']['log_line'];
		}

		if ( $err_msg = nfw_write_conf() ) {
			return $err_msg;
		}
	}
}

/* ------------------------------------------------------------------ */

function nf_sub_log_save_pubkey( $nfw_options ) {

	global $nfw_options;

	if ( empty( $_POST['nfw_options']['clogs_pubkey'] ) ||
		! preg_match( '/^[a-f0-9]{40}:(?:[a-f0-9:.]{3,39}|\*)$/', $_POST['nfw_options']['clogs_pubkey'] ) ) {
		$nfw_options['clogs_pubkey'] = '';
	} else {
		$nfw_options['clogs_pubkey'] = $_POST['nfw_options']['clogs_pubkey'];
	}

	if ( $err_msg = nfw_write_conf() ) {
		return $err_msg;
	}
}

/* ------------------------------------------------------------------ */

function nf_sub_log_read_local( $log, $log_dir, $max_lines ) {

	global $lang;

	if (! preg_match( '/^(firewall_\d{4}-\d\d(?:\.\d+)?\.)php$/', trim( $log ) ) ) {
		die( $lang['error'] );
	}

	$data = array();
	$data['type'] = 'local';

	if (! file_exists( $log_dir . $log ) ) {
		$data['err_msg'] = $lang['missing_log'];
		return $data;
	}

	$data['log'] = file( $log_dir . $log, FILE_SKIP_EMPTY_LINES );

	if ( $data['log'] === false ) {
		$data['err_msg'] = $lang['cannot_open'];
		return $data;
	}
	if ( strpos( $data['log'][0], '<?php' ) !== FALSE ) {
		unset( $data['log'][0] );
	}
	// Keep only the last $max_lines:
	$data['lines'] = count( $data['log'] );
	if ( $max_lines < $data['lines'] ) {
		for ($i = 0; $i < ( $data['lines'] - $max_lines); $i++ ) {
			unset( $data['log'][$i] ) ;
		}
	}

	if ( $data['lines'] == 0 ) {
		$data['err_msg'] = $lang['empty_log'];
	}

	return $data;

}

/* ------------------------------------------------------------------ */

function nfw_write_conf() {

	global $lang, $nfw_options;

	// Config file must be writable :
	if (! is_writable('./conf/options.php') ) {
		return $lang['error_conf' ];
	}

	// Save changes :
	if (! $fh = fopen('./conf/options.php', 'w') ) {
		return $lang['error_conf' ];
	}
	fwrite($fh, '<?php' . "\n\$nfw_options = <<<'EOT'\n" . serialize( $nfw_options ) . "\nEOT;\n" );
	fclose($fh);

}

/* ------------------------------------------------------------------ */

function nf_sub_log_export( $log_dir ) {

	$log = trim($_GET['nfw_logname']);
	if (! preg_match( '/^(firewall_\d{4}-\d\d(?:\.\d+)?\.)php$/', $log, $match ) ) {
		die('Unknown request (#1)');
	}
	$name = $match[1];
	if (! file_exists( $log_dir . $log) ) {
		die('Unknown request (#2)');
	}
	$data = file( $log_dir . $log);
	$res = "Date\tIncident\tLevel\tRule\tIP\tRequest\tEvent\tHost\n";
	$levels = array( '', 'medium', 'high', 'critical', 'error', 'upload', 'info', 'DEBUG_ON' );
	$severity = array( 0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
	foreach( $data as $line ) {
		if ( preg_match( '/^\[(\d{10})\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(#\d{7})\]\s+\[(\d+)\]\s+\[(\d)\]\s+\[([\d.:a-fA-F, ]+?)\]\s+\[.+?\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[(hex:)?(.+)\]$/', $line, $match ) ) {
			if ( empty( $match[4]) ) { $match[4] = '-'; }
			if ( $match[10] == 'hex:' ) { $match[11] = pack('H*', $match[11]); }
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

/* ------------------------------------------------------------------ */
// EOF
