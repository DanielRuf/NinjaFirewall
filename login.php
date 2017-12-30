<?php
/*
 +---------------------------------------------------------------------+
 | NinjaFirewall (Pro edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
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

// Prevent search engine from caching the login page:
if (! empty($_SERVER["HTTP_USER_AGENT"]) && preg_match('/Googlebot|Yahoo|msnbot|baidu/i', $_SERVER["HTTP_USER_AGENT"]) ) {
	header('HTTP/1.1 404 Not Found');
	header('Status: 404 Not Found');
	die('404 Not Found');
}

if (! @include __DIR__ . '/conf/options.php' ) {
	// Probably a fresh install; redirect it to the installer :
	if ( file_exists('install.php') ) {
		header('Location: install.php');
	} else {
		$msg = 'Error : cannot find  the <code>./conf/options.php</code> configuration file (#1).';
		warn_login($msg, 'error');
	}
	exit;
}

$nfw_options = unserialize($nfw_options);
date_default_timezone_set($nfw_options['timezone']);
require __DIR__ . '/lib/lang/' . $nfw_options['admin_lang'] . '/' . basename(__FILE__);

// Get the user defined IP (if any):
if (! defined( 'NFW_REMOTE_ADDR') ) {
	require_once __DIR__ . '/lib/misc.php';
	nfw_select_ip();
}

// "install/" directory left from previous upgrade (v1.x to v2.x) ?
if (is_dir('install/') ) {
	warn_login($lang['install_dir'], 'warning');
	exit;
}

if ( empty($nfw_options['admin_name']) || empty($nfw_options['admin_pass']) ) {
   if ( file_exists('install.php') ) {
      header('Location: install.php');
   } else {
		warn_login($lang['err_inst'], 'error');
   }
	exit;
}

if (version_compare(PHP_VERSION, '5.4', '<') ) {
	if (! session_id() ) {
		@ini_set('session.cookie_httponly', 1);
		@ini_set('session.use_only_cookies', 1);
		if ($_SERVER['SERVER_PORT'] == 443) {
			@ini_set('session.cookie_secure', 1);
		}
		session_start();
	}
} else {
	if (session_status() !== PHP_SESSION_ACTIVE) {
		@ini_set('session.cookie_httponly', 1);
		@ini_set('session.use_only_cookies', 1);
		if ($_SERVER['SERVER_PORT'] == 443) {
			@ini_set('session.cookie_secure', 1);
		}
		session_start();
	}
}

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
// Prevent caching :
header('Pragma: no-cache');
header('Cache-Control: no-store, max-age=0, must-revalidate, proxy-revalidate');
header('Expires: Mon, 01 Sep 2014 01:01:01 GMT');

// Clear installation flag:
$_SESSION['nfw_install'] = '';

// Brute force attack prevention :
$max_attempt = 5;
$max_bantime = 5; // minutes
$fdat = 0;
$file =  __DIR__ . '/nfwlog/cache/login_' . NFW_REMOTE_ADDR . '.php';
if ( file_exists($file) ) {
	// Check when it was last modified :
	$stat = stat($file);
	if ( time() - $stat['ctime'] > $max_bantime * 60 ) {
		// It is too old, delete it:
		unlink($file);
	} else {
		// Check its content :
		$fdat = file_get_contents($file, FILE_SKIP_EMPTY_LINES);
		// Did it exceed the threshold ?
		if ( $fdat >= $max_attempt ) {
			// Block it :
			login_page(2);
		}
	}
}

// Should we force SSL login ?
if ( $nfw_options['admin_ssl'] && $_SERVER['SERVER_PORT'] != 443 ) {
	// Force it:
   header('Location: https://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] );
   exit;
}

// Display the login page ?
if ( empty($_POST['admin_name']) || empty($_POST['admin_pass']) )  {
	login_page(0);
	exit;
}

// Verify username & password:
if ( verify_admin_login( $_POST['admin_name'], $_POST['admin_pass'], $nfw_options ) === true ) {

	// Write to log :
	@file_put_contents( __DIR__ . '/nfwlog/admin.php',
		date('[d/M/Y H:i:s O] ') . '[' . $nfw_options['admin_name'] . '] [' . NFW_REMOTE_ADDR . "] [OK]\n",
		FILE_APPEND | LOCK_EX);

	// Syslog?
	if (! empty( $nfw_options['syslog'] ) ) {
		@openlog( 'ninjafirewall', LOG_NDELAY|LOG_PID, LOG_USER );
		@syslog( LOG_NOTICE, "INFO: #". mt_rand(1000000, 9000000) .": Logged in administrator from ". NFW_REMOTE_ADDR . " on {$_SERVER['SERVER_NAME']}" );
		@closelog();
	}

	// Send an alert to the admin, if required :
	if ($nfw_options['admin_login_alert']) {
		if ($_SERVER['SERVER_PORT'] == 443) {
			$http = 'https';
		} else {
			$http = 'http';
		}
      $subject = 	'[NinjaFirewall] Admin console login';
      $message = 	"Someone just logged in to your NinjaFirewall admin interface:\n\n".
						"- IP   : ". NFW_REMOTE_ADDR . "\n" .
						"- Date : ". date('F j, Y @ g:i a') . ' (UTC '. date('O') . ")\n" .
						"- URL  : " . $http . "://". $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] . "\n\n" .
						'NinjaFirewall - https://nintechnet.com/' . "\n";

		$headers = 'From: "'. $nfw_options['admin_email'] .'" <'. $nfw_options['admin_email'] .'>' . "\r\n";
		$headers .= "Content-Transfer-Encoding: 7bit\r\n";
		$headers .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		mail($nfw_options['admin_email'], $subject, $message, $headers, '-f'. $nfw_options['admin_email']);
   }

	// If the user is still using an old SHA-1 password, we convert it
	// with password_hash() with the default algo (requires PHP >=5.5):
	if ( preg_match( '/^[0-9a-f]{40}$/', $nfw_options['admin_pass'] ) && function_exists( 'password_hash' ) ) {
		// The algorithmic cost can be user-defined in the .htninja file
		// E.g.: define('NF_PASSWORD_COST', 13);
		// Default is 10 and should be suitable for most hardware (e.g., single-core VPS)
		// but a cost of 13 would provide better security:
		if ( defined('NF_PASSWORD_COST') ) {
			$cost = (int) NF_PASSWORD_COST;
		} else {
			$cost = 10;
		}
		$new_password = password_hash( $_POST['admin_pass'], PASSWORD_DEFAULT, [ 'cost' => $cost ] );
		if ( $new_password !== false ) {
			// Update the hash in the option.php file:
			$nfw_options['admin_pass'] = $new_password;
			$fh = fopen( __DIR__ . '/conf/options.php', 'w' );
			fwrite( $fh, '<?php'. "\n\$nfw_options = <<<'EOT'\n". serialize( $nfw_options ) ."\nEOT;\n" );
			fclose( $fh );
		}
	}

	// Redirect to the index page :
	$_SESSION['nfadmpro'] = sha1( $nfw_options['admin_name'] );
	$_SESSION['timeout'] = time();
	$token = sha1( time() );
	$_SESSION['nftoken'] = sha1( $token . __DIR__ );
	session_write_close();
	header('Location: index.php?token=' . $token);
	exit;
}

// Wrong credentials : log and display the login page again
@file_put_contents( __DIR__ . '/nfwlog/admin.php',
	date('[d/M/Y H:i:s O] ') . '[' . htmlentities( $_POST['admin_name'] ) . '] [' .
	NFW_REMOTE_ADDR . "] [***FAILED***]\n",
	FILE_APPEND | LOCK_EX);

if ( file_exists($file) ) {
	$fdat = file_get_contents($file, FILE_SKIP_EMPTY_LINES);
}
@file_put_contents($file, $fdat+1, LOCK_EX);

login_page(1);

exit;

/* ------------------------------------------------------------------ */

function verify_admin_login( $admin_name, $admin_pass, $nfw_options ) {

	if ( $admin_name !== $nfw_options['admin_name'] ) {
		return false;
	}

	// Old SHA-1 hash?
	if ( preg_match( '/^[0-9a-f]{40}$/', $nfw_options['admin_pass'] ) ) {
		if ( sha1( $admin_pass ) === $nfw_options['admin_pass'] ) {
			return true;
		}
		return false;
	}

	// Bcrypt (PHP 5.5>=):
	return password_verify( $admin_pass, $nfw_options['admin_pass'] );

}

/* ------------------------------------------------------------------ */

function login_page($err) {

   global $max_bantime, $lang;

	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>NinjaFirewall : Admin login</title>
		<link href="static/styles.css" rel="stylesheet" type="text/css">
		<link rel="Shortcut Icon" type="image/gif" href="static/favicon.ico">
		<meta http-equiv="cache-control" content="no-store" />
		<meta http-equiv="expires" content="Mon, 01 Sep 2014 01:01:01 GMT" />
		<meta http-equiv="pragma" content="no-cache" />
	</head>
	<body bgcolor="white" <?php if ($err < 2) { echo 'onload="document.wl.username.focus();"'; } ?>>
   <br />
   <br />
   <p align="center"><img src="static/logo.png" width="192" height="62"></p>
   <?php
   if ( $_SERVER['QUERY_STRING'] == 'logout' ) {
		?>
		<center class="smallblack"><div class="success" style="width:290px;"><p><?php echo $lang['session_closed'] ?></p></div></center>
		<?php
   } elseif ( $_SERVER['QUERY_STRING'] == 'expired' ) {
		?>
		<center class="smallblack"><div class="warning" style="width:290px;"><p><?php echo $lang['session_expired'] ?></p></div></center>
		<?php
   } elseif ( $err == 2) {
		?>
		<center class="smallblack">
			<div class="error" style="width:450px;"><p><?php printf( $lang['banned'], $max_bantime) ?></p></div>

			<br /><br /><br /><br /><br />
			<font class="tinygrey">&copy; 2012-<?php echo date('Y') ?> <a style="border-bottom:dotted 1px #FDCD25;color:#999999;" href="https://nintechnet.com/" title="The Ninja Technologies Network">NinTechNet</a><br />The Ninja Technologies Network</font>
		</center>
		</body></html>
		<?php
		exit;
   } elseif ( $err == 1 ) {
		?>
		<center class="smallblack"><div class="error" style="width:290px;"><p><?php echo $lang['err_login'] ?></p></div></center>
		<?php
	}
	?>
   <br />
   <form method="post" action="login.php" name="wl">
		<center class="smallblack">
			<fieldset style="width:300px;border:1px solid #ffd821;"><legend><b>&nbsp;<?php echo $lang['admin_login'] ?>&nbsp;</b></legend>
			<table width="100%" align="center" border="0" class="smallblack" cellpadding="10" style="border:none;">
				<tr>
					<td align="right" width="50%"><?php echo $lang['username'] ?></td>
					<td width="50%"><input id="username" class="input" type="text" size="15" name="admin_name" maxlength="20" autocomplete="off"></td>
				</tr>
				<tr>
					<td align="right" width="50%"><?php echo $lang['password'] ?></td>
					<td width="50%"><input id="password" class="input" type="password" size="15" name="admin_pass" autocomplete="off"></td>
				</tr>
				<tr>
					<td width="50%">&nbsp;</td>
					<td align="center" width="50%"><input class="button" type="submit" value="<?php echo $lang['login'] ?>"></td>
				</tr>
			</table>
			</fieldset>
		</center>
	</form>
	<br />
	<br />
	<center class="tinygrey">&copy; 2012-<?php echo date('Y') ?> <a style="border-bottom:dotted 1px #FDCD25;color:#999999;" href="https://nintechnet.com/" title="The Ninja Technologies Network">NinTechNet</a><br />The Ninja Technologies Network</center>
	</body>
</html>
	<?php
   exit;
}

/* ------------------------------------------------------------------ */
function warn_login( $msg, $lev ) {

	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>NinjaFirewall</title>
		<link href="static/styles.css?<?php echo time() ?>" rel="stylesheet" type="text/css">
		<link rel="Shortcut Icon" type="image/gif" href="static/favicon.ico">
		<meta http-equiv="cache-control" content="no-store" />
		<meta http-equiv="expires" content="Mon, 01 Sep 2014 01:01:01 GMT" />
		<meta http-equiv="pragma" content="no-cache" />
	</head>
	<body bgcolor="white">
		<br /><br /><br /><br /><br /><br />
		<center class="smallblack"><div class="<?php echo $lev ?>" style="width:450px;"><p><?php echo $msg ?></div></center>
		<br /><br /><br /><br />
		<center class="tinygrey">&copy; 2012-<?php echo @date('Y') ?> <a style="border-bottom:dotted 1px #FDCD25;color:#999999;" href="https://nintechnet.com/" title="The Ninja Technologies Network">NinTechNet</a><br />The Ninja Technologies Network</center>
	</body>
</html>
	<?php
}

/* ------------------------------------------------------------------ */
// EOF
