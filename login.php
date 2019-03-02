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

// Prevent search engine from caching the login page:
if (! empty($_SERVER["HTTP_USER_AGENT"]) &&
	preg_match('/Googlebot|Yahoo|msnbot|baidu/i', $_SERVER["HTTP_USER_AGENT"]) ) {
	header('HTTP/1.1 404 Not Found');
	header('Status: 404 Not Found');
	die('404 Not Found');
}

$success = '<div class="alert alert-success text-center"><a href="" '.
	'class="close" data-dismiss="alert" aria-label="close">&times;</a>%s</div>';
$danger = '<div class="alert alert-danger text-center"><a href="" '.
	'class="close" data-dismiss="alert" aria-label="close">&times;</a>%s</div>';
$warning = '<div class="alert alert-warning text-center"><a href="" '.
	'class="close" data-dismiss="alert" aria-label="close">&times;</a>%s</div>';

if (! file_exists( __DIR__ . '/conf/options.php' ) ) {
	// Probably a fresh install; redirect it to the installer :
	if ( file_exists('install.php') ) {
		header('Location: install.php');
	} else {
		$msg = 'Error: Cannot find  the "./conf/options.php" configuration file.';
		html_header();
		html_body( 1, sprintf( $danger, $msg ) );
		html_footer();
	}
	exit;
}

// Retrieve options:
include __DIR__ . '/conf/options.php';
$nfw_options = unserialize( $nfw_options );
date_default_timezone_set( $nfw_options['timezone'] );

// Should we force SSL login?
if ( $nfw_options['admin_ssl'] && $_SERVER['SERVER_PORT'] != 443 ) {
	// Redirect:
   header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}" );
   exit;
}

require_once __DIR__ .'/lib/locale.php';
require __DIR__ . '/lib/constants.php';
require_once __DIR__ . '/lib/misc.php';

// Get the user defined IP (if any):
if (! defined( 'NFW_REMOTE_ADDR') ) {
	nfw_select_ip( $nfw_options );
}

// There's the "install" directory left from previous upgrade (v1.x to v2.x) ?
if ( is_dir( 'install/' ) ) {
	// Stop here:
	html_header();
	html_body( 1, sprintf(
		$danger,
		_('The following directory was found: "install/". Please delete'.
		' it and reload this page to access the login page.')
	) );
	html_footer();
	exit;
}

// Make sure there is an admin name + pass:
if ( empty( $nfw_options['admin_name'] ) || empty( $nfw_options['admin_pass'] ) ) {
   if ( file_exists('install.php') ) {
      header('Location: install.php');
   } else {
		// Stop here:
		html_header();
		html_body( 1, sprintf( $danger, _('Error : cannot find the "install.php" file.') ) );
		html_footer();
   }
	exit;
}

// Start a PHP session:
@ini_set('session.cookie_httponly', 1);
@ini_set('session.use_only_cookies', 1);
if ( $_SERVER['SERVER_PORT'] == 443 ) {
	@ini_set('session.cookie_secure', 1);
}
if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
	if (! session_id() ) {
		session_start();
	}
} else {
	if ( session_status() !== PHP_SESSION_ACTIVE ) {
		session_start();
	}
}

// Security headers + no caching:
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Pragma: no-cache');
header('Cache-Control: no-store, max-age=0, must-revalidate, proxy-revalidate');
header('Expires: Mon, 01 Sep 2014 01:01:01 GMT');
// Clear installation flag:
unset( $_SESSION['nfw_install'] );

// Brute-force protection:
$bfp_log =  __DIR__ . '/nfwlog/cache/login_' . NFW_REMOTE_ADDR . '.php';
if ( login_protection( $bfp_log )  === true ) {
	// Stop here:
	html_header();
	html_body( 1, sprintf( $danger, sprintf(
		_('Too many failed login attempts, you have been banned for %s minutes.'),
		MAX_BAN_TIME
	) ) );
	html_footer();
	exit;
}

// Display the login page?
$msg = '';
if ( empty( $_POST['admin_name'] ) || empty( $_POST['admin_pass'] ) )  {
	if (! empty( $_SERVER['QUERY_STRING'] ) ) {
		if ( $_SERVER['QUERY_STRING'] == 'logout' ) {
			$msg = sprintf( $success, _('Your session has been closed.') );

		} elseif ( $_SERVER['QUERY_STRING'] == 'expired' ) {
			$msg = sprintf( $warning, _('Your session has expired.') );
		}
	}
	html_header();
	html_body( 0, $msg );
	html_footer();
	exit;
}

// Verify username & password:
if ( verify_admin_login( $_POST['admin_name'], $_POST['admin_pass'], $nfw_options ) === true ) {

	// Write to log:
	@file_put_contents( __DIR__ . '/nfwlog/admin.php',
		date('[d/M/Y H:i:s O] ') . "[{$nfw_options['admin_name']}] [" .
			NFW_REMOTE_ADDR . "] [OK]\n",
		FILE_APPEND | LOCK_EX);

	// Syslog?
	if (! empty( $nfw_options['syslog'] ) ) {
		@openlog( 'ninjafirewall', LOG_NDELAY|LOG_PID, LOG_USER );
		@syslog(
			LOG_NOTICE,
			"INFO: #". mt_rand(1000000, 9000000) .": Logged in administrator from ".
			NFW_REMOTE_ADDR . " on {$_SERVER['SERVER_NAME']}"
		);
		@closelog();
	}

	// Send an alert to the admin, if required:
	if (! empty( $nfw_options['admin_login_alert'] ) ) {
		email_admin( $nfw_options );
	}

	// If the user is still using an old SHA-1 password, we convert it
	// with password_hash() with the default algo (requires PHP >=5.5):
	if ( preg_match( '/^[0-9a-f]{40}$/', $nfw_options['admin_pass'] ) &&
		function_exists( 'password_hash' ) && version_compare( PHP_VERSION, '5.5', '>=' ) ) {
		// The algorithmic cost can be user-defined in the .htninja file
		// E.g.: define('NF_PASSWORD_COST', 13);
		// Default is 10 and should be suitable for most hardware (e.g., single-core VPS)
		// but a cost of 13 would provide better security:
		if ( defined('NF_PASSWORD_COST') ) {
			$cost = (int) NF_PASSWORD_COST;
		} else {
			$cost = 10;
		}
		$new_password = password_hash(
			$_POST['admin_pass'],
			PASSWORD_DEFAULT,
			array( 'cost' => $cost )
		);
		if ( $new_password !== false ) {
			// Update the hash in the option.php file:
			$nfw_options['admin_pass'] = $new_password;
			save_config( $nfw_options, 'options' );
		}
	}

	// Redirect to the index page :
	$_SESSION['nfadmpro'] = sha1( $nfw_options['admin_name'] );
	$_SESSION['timeout'] = time();
	// Simple nonce used to prevent XSS etc:
	$token = bin2hex( openssl_random_pseudo_bytes( 20 ) );
	$_SESSION['nftoken'] = sha1( $token . __DIR__ );
	session_write_close();
	header('Location: index.php?token=' . $token);
	exit;
}

// Log failed login attempt:
@file_put_contents( __DIR__ . '/nfwlog/admin.php',
	date('[d/M/Y H:i:s O] ') . '[' . htmlentities( $_POST['admin_name'] ) . '] [' .
	NFW_REMOTE_ADDR . "] [***FAILED***]\n",
	FILE_APPEND | LOCK_EX);

// Brute-force protection:
$bfp_count = 0;
if ( file_exists( $bfp_log) ) {
	$bfp_count = trim( file_get_contents( $bfp_log, FILE_SKIP_EMPTY_LINES ) );
}
@file_put_contents( $bfp_log, ++$bfp_count, LOCK_EX );

// Disply login error:
html_header();
html_body( 0, sprintf( $danger, _('Wrong username or password.') .'<br />'. sprintf( _('Use <a href="%s">this script</a> if you need to reset your password.'), 'https://nintechnet.com/share/pro2-reset.txt' ) ) );
html_footer();

exit;

// ---------------------------------------------------------------------
// Login page brute-force protection.

function login_protection( $file ) {

	// Those 2 constants can be defined in the .htninja if needed:
	if (! defined( 'MAX_ATTEMPT' ) ) {
		define( 'MAX_ATTEMPT', 5 );
	}
	if (! defined( 'MAX_BAN_TIME' ) ) {
		define( 'MAX_BAN_TIME', 5 ); // minutes
	}

	if ( file_exists( $file ) ) {
		// Check when it was last modified :
		$ctime = filectime( $file );
		if ( time() - $ctime > MAX_BAN_TIME * 60 ) {
			// It is too old, delete it:
			unlink( $file );
		} else {
			// Check its content :
			$fdat = file_get_contents( $file, FILE_SKIP_EMPTY_LINES );
			// Did it exceed the threshold ?
			if ( $fdat >= MAX_ATTEMPT ) {
				// Block it :
				return true;
			}
		}
	}
	return false;
}

// =====================================================================
// Verify the admin password.

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

// =====================================================================
// Send a email to the admin when someone logs in.

function email_admin( $nfw_options ) {

	if ($_SERVER['SERVER_PORT'] == 443) {
		$http = 'https';
	} else {
		$http = 'http';
	}
	$subject = 	_('[NinjaFirewall] Admin console login'). " ({$_SERVER['SERVER_NAME']})";
	$message = 	_('Someone just logged in to your NinjaFirewall admin interface:') ."\n\n".
					'-IP: '. NFW_REMOTE_ADDR . "\n" .
					'-'. _('Date:') .' '. date('F j, Y @ g:i a') . ' (UTC '. date('O') . ")\n" .
					"-URL: {$http}://{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}\n\n" .
					'NinjaFirewall - https://nintechnet.com/' . "\n" .
					'Help Desk (Premium users only): https://nintechnet.com/helpdesk/' . "\n";

	$headers = 'From: "'. $nfw_options['admin_email'] .'" <'.
					$nfw_options['admin_email'] .'>' . "\r\n";
	$headers .= "Content-Transfer-Encoding: 7bit\r\n";
	$headers .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	mail(
		$nfw_options['admin_email'],
		$subject,
		$message,
		$headers,
		"-f{$nfw_options['admin_email']}"
	);
}

// =====================================================================

function html_header() {

	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>NinjaFirewall : <?php echo _('Admin login') ?></title>
	<link href="static/styles.css<?php echo '?ver='. NFW_ENGINE_VERSION ?>" rel="stylesheet" type="text/css">
	<link href="static/bootstrap.min.css<?php echo '?ver='. NFW_ENGINE_VERSION ?>" rel="stylesheet" type="text/css">
	<?php
	// If the use has their own CSS style sheet, load it:
	if ( file_exists( 'static/user.css' ) ) {
		echo '<link href="static/user.css?ver='. NFW_ENGINE_VERSION .'" rel="stylesheet" type="text/css">'."\n";
	}
	?>
	<link rel="Shortcut Icon" type="image/gif" href="static/favicon.ico">
	<meta http-equiv="cache-control" content="no-store" />
	<meta http-equiv="expires" content="Mon, 01 Sep 2014 01:01:01 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
</head>
<body>
	<?php
}
// =====================================================================
// Display the HTML body and potential error messages.

function html_body( $blocked = 0, $message = null ) {

	?>
	<div id="main_nr" class="ntn-section">
		<div class="container">
			<div class="row ">
				<!-- login form -->
				<div  class="col-sm-12">
					<img src="static/logo_200.png" class="center-block">
					<br />
					<div class="container">
						<div class="row">
							<div class="col-sm-6 col-sm-offset-3 well">
								<form role="form" class="form-horizontal" method="post" autocomplete="off">
								<fieldset>
									<legend><?php echo _('Admin login') ?></legend>
									<?php
									if ( isset( $message ) ) {
										echo $message;
									}
									if (! $blocked ) {
									?>
										<div class="form-group">
											<div class="col-md-12">
												<label class="control-label"><?php echo _('Username:') ?></label>
											</div>
											<div class="col-md-12">
												<input required class="form-control" name="admin_name" type="text" value="<?php
												if (! empty( $_POST['admin_name'] ) ) {
													echo htmlspecialchars( $_POST['admin_name'] );
												}
												?>" autocomplete="off" autocapitalize="none" autofocus spellcheck="false" />
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-12">
												<label class="control-label"><?php echo _('Password:') ?></label>
											</div>
											<div class="col-md-12">
												<input required class="form-control" name="admin_pass" type="password" value="" autocomplete="off" />
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-12">
												<input name="submitform" type="submit" class="btn btn-lg btn-success btn-100 active" value="<?php echo _('Login') ?>" />
											</div>
										</div>
									<?php
									}
									?>
								</fieldset>
								</form>
							</div>
						</div>
					</div>
				</div>	<!-- login form -->
				<div class="col-sm-12 text-center footer-font">
					<p><?php echo '&copy; 2011-'. date('Y') .' <a href="https://nintechnet.com/">The Ninja Technologies Network</a>'; ?></p>
				</div>
			</div>
		</div>
	</div>
	<?php
}

// =====================================================================

function html_footer() {

	?>
	<script src="static/jquery.js<?php echo '?ver='. NFW_ENGINE_VERSION ?>"></script>
	<script src="./static/bootstrap.min.js<?php echo '?ver='. NFW_ENGINE_VERSION ?>"></script>
</body>
</html>
	<?php

}

// =====================================================================
// EOF
