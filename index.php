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

if (! @include __DIR__ . '/conf/options.php' ) {
	header('Location: login.php?1');
	exit;
}
$nfw_options = unserialize( $nfw_options );

// I18N:
require_once __DIR__ .'/lib/locale.php';

// Required constants :
require __DIR__ . '/lib/constants.php';

require_once __DIR__ . '/lib/misc.php';
// Get the user defined IP (if any):
if (! defined( 'NFW_REMOTE_ADDR') ) {
	nfw_select_ip( $nfw_options );
}

date_default_timezone_set( $nfw_options['timezone'] );

// Start session :
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

// Don't cache anything :
header('Pragma: no-cache');
header('Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate, proxy-revalidate');
header('Expires: Mon, 01 Sep 2014 01:01:01 GMT');

if ( $_SERVER['QUERY_STRING'] == 'logout') {
	session_destroy();
   header('Location: login.php?logout');
   exit;
}

if ( empty( $_SESSION['timeout'] ) || empty( $_SESSION['nfadmpro'] ) ||
	empty( $_SESSION['nftoken'] ) || empty( $_REQUEST['token'] ) ) {

	if ( empty( $_SESSION['first_run'] ) ) {
		session_destroy();
	}
   header('Location: login.php?2');
   exit;
}

if ( $_SESSION['nftoken'] !== sha1( $_REQUEST['token'] . __DIR__ ) ||
	! preg_match( '/^[a-f0-9]{40}$/', $_REQUEST['token'] ) ) {
	session_destroy();
   header('Location: login.php?3');
   exit;
}

if ( ( $_SESSION['timeout'] + 7200 ) < time() ) {
	session_destroy();
   header('Location: login.php?expired');
   exit;
}
$_SESSION['timeout'] = time();

if ( $_SESSION['nfadmpro'] !== sha1( $nfw_options['admin_name'] ) ) {
	session_destroy();
   header('Location: login.php?4');
   exit;
}

// Should we force SSL login?
if ( $nfw_options['admin_ssl'] && $_SERVER['SERVER_PORT'] != 443 ) {
	// Force it:
   header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}" );
   exit;
}

require __DIR__ . '/conf/rules.php';
$nfw_rules = unserialize( $nfw_rules );

// Used for updates :
require __DIR__ . '/lib/nfw_init.php';

if (! isset( $_SESSION['ver'] ) ) {
	$_SESSION['vapp'] = $_SESSION['ver'] = 0;
}

if ( empty( $_REQUEST['mid'] ) || ! ctype_digit( $_REQUEST['mid'] ) ) {
	$mid = 10;
} else {
	$mid = $_REQUEST['mid'];
}

// Changelog pop-up window :
if ( $mid == 99 ) {
	nfw_changelog();

//	Summary > Statstistics:
} elseif ( $mid == 11 ) {
	require __DIR__ . '/lib/summary_stats.php';

//	Account > Options :
} elseif ( $mid == 20 ) {
	require __DIR__ . '/lib/account_options.php';

// Account > License :
} elseif ( $mid == 21 ) {
	require __DIR__ . '/lib/account_license.php';

// Account > Updates :
} elseif ( $mid == 22 ) {
	require __DIR__ . '/lib/account_updates.php';

// Firewall > Options
} elseif ( $mid == 30 ) {
	require __DIR__ . '/lib/firewall_options.php';

// Firewall > Policies
} elseif ( $mid == 31 ) {
	require __DIR__ . '/lib/firewall_policies.php';

// Firewall > Access Control
} elseif ( $mid == 32 ) {
	require __DIR__ . '/lib/firewall_access_control.php';

// Firewall > Rules Editor
} elseif ( $mid == 35 ) {
	require __DIR__ . '/lib/firewall_rules_editor.php';

// Monitoring > File Guard
} elseif ( $mid == 33 ) {
	require __DIR__ . '/lib/firewall_fileguard.php';

// Monitoring > File Check
} elseif ( $mid == 38 ) {
	require __DIR__ . '/lib/firewall_filecheck.php';

// Monitoring > Web Filter
} elseif ( $mid == 34 ) {
	require __DIR__ . '/lib/firewall_webfilter.php';

// Logs > Firewall Log
} elseif ( $mid == 36 ) {
	require __DIR__ . '/lib/firewall_log.php';

// Logs > Live Log
} elseif ( $mid == 37 ) {
	require __DIR__ . '/lib/firewall_livelog.php';

// Logs > Centralized Logging
} elseif ( $mid == 39 ) {
	require __DIR__ . '/lib/firewall_centlog.php';

} elseif ( $mid == 90 ) {
   raw_admin_log();

} elseif ( $mid == 91 ) {
	flush_admin_log();
   raw_admin_log();

//	Summary > Overview:
} else {
	require __DIR__ . '/lib/summary_overview.php';
}

exit;

// =====================================================================

function is_nf_enabled() {

	global $nfw_options;

	// Check whether NF is running.

	// No communication from the firewall :
	if (! defined('NFW_STATUS') ) {
		define('NF_DISABLED', 10);
		return;
	}

	// NF was disabled by the admin :
	if ( isset($nfw_options['enabled']) && $nfw_options['enabled'] == '0' ) {
		define('NF_DISABLED', 5);
		return;
	}

	// There is another instance of NinjaFirewall firewall running,
	// maybe in the parent directory:
	if (NFW_STATUS == 20 || NFW_STATUS == 21 || NFW_STATUS == 23) {
		define('NF_DISABLED', 10);
		return;
	}

	// OK :
	if (NFW_STATUS == 22) {
		define('NF_DISABLED', 0);
		return;
	}

	// Err :
	define('NF_DISABLED', NFW_STATUS);
	return;

}

// =====================================================================

function checked( $var, $val) {

	if ( $var == $val ) {
		echo " checked='checked'";
	}

}

// =====================================================================

function selected( $var, $val, $ret = 0 ) {

	if ( $var == $val ) {
		if (! $ret ) {
			echo " selected='selected'";
		} else {
			return " selected='selected'";
		}
	}

}

// =====================================================================

function disabled( $var, $val) {

	if ( $var == $val ) {
		echo " disabled='disabled'";
	}

}
// =====================================================================

function readonly( $var, $val) {

	if ( $var == $val ) {
		echo " readonly='readonly'";
	}

}

// =====================================================================
// Open and display the admin log in a pop-up window.

function raw_admin_log() {

	global $nfw_options;

	$log = __DIR__ .'/nfwlog/admin.php';
	$line = '';

	// Open the log:
	if ( file_exists( $log ) ) {
		$fsize = filesize( $log );

		// It looks empty, return an error:
		if ( $fsize < 5 ) {
			$line = _('The admin log is empty.');
			$err = 1;

		// Fetch its content:
		} else {
			$fh = fopen( $log, 'r' );
			while (! feof( $fh ) ) {
				$line .= htmlspecialchars( fgets( $fh ) );
			}
			fclose ( $fh );
		}

	// Missing log:
	} else {
		$line = sprintf( _('Unable to open logfile: %s'), '/nfwlog/admin.php' );
		$err = 1;
	}

	?><html lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<title>NinjaFirewall</title>
			<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
			<link href="static/bootstrap.min.css" rel="stylesheet" type="text/css">
			<link href="static/styles.css" rel="stylesheet" type="text/css">
			<?php
			// If the use has their own CSS style sheet, load it:
			if ( file_exists( 'static/user.css' ) ) {
				echo '<link href="static/user.css" rel="stylesheet" type="text/css">'."\n";
			}
			?>
			<script>function dellog(){if (confirm("<?php echo _('Delete log') ?>?")){return true;}else{return false;}}</script>
		</head>

		<body>
			<div id="main_nr" class="ntn-section text-center" style="padding-top:10px">
				<div class="container">
					<div class="row" style="padding:10px">
						<?php
						if ( empty( $err ) ) {
							echo '<h4>nfwlog/admin.php (' . sprintf('%s bytes', number_format( $fsize ) ) .')</h4>';
						}
						?>
						<textarea class="form-control" style="height:300px;font-family:Consolas,Monaco,monospace;"><?php
						echo $line;
						?></textarea>
						<?php
						if (! empty( $err ) ) {
							echo '</div></div></div></body></html>';
							exit;
						}
						?>
						<br />
						<form method="post" onsubmit="return dellog();">
							<input type="hidden" name="mid" value="91" />
							<input class="btn btn-md btn-danger btn-25" type="submit" value="<?php echo _('Delete log') ?>" />
							&nbsp;&nbsp;&nbsp;
							<input class="btn btn-md btn-success btn-25" type="button" value="<?php echo _('Close') ?>" onClick="window.close();" />
						</form>
					</div>
				</div>
			</div>
			<script src="static/bootstrap.min.js<?php echo '?ver='. NFW_ENGINE_VERSION ?>"></script>
		</body>
	</html><?php

	exit;
}

// =====================================================================

function flush_admin_log() {

	global $nfw_options;

	if ( $fh = fopen( __DIR__ . '/nfwlog/admin.php', 'w' ) ) {
		fwrite( $fh, date('[d/M/Y H:i:s O] ') . "[{$nfw_options['admin_name']}]".
		'['. NFW_REMOTE_ADDR . '] ' ."[OK]\n" );
		fclose($fh);
	}
}

// =====================================================================

function html_header( $load = '' ) {

	global $nfw_options;

	if ( NFW_EDN == 1 ) {
		$p = ' <sup style="color:red">Pro+</sup>';
	} else {
		$p = '';
	}

   $menu = array(
      10 => _('Summary > Overview'),
      11 => _('Summary > Statistics'),

      20 => _('Account > Options'),
      21 => _('Account > License'),
      22 => _('Account > Updates'),

      30 => _('Firewall > Options'),
      31 => _('Firewall > Policies'),
      32 => _('Firewall > Access Control'),
      35 => _('Firewall > Rules Editor'),

      33 => _('Monitoring > File Guard'),
      38 => _('Monitoring > File Check'),
      34 => _('Monitoring > Web Filter'),

      36 => _('Logs > Firewall Log'),
      37 => _('Logs > Live Log'),
      39 => _('Logs > Centralized Logging'),
   );

	$summary_active = ''; $account_active = ''; $firewall_active = '';
	$monitoring_active = ''; $log_active = '';

	if ( $GLOBALS['mid'] == 10 || $GLOBALS['mid'] == 11 ) {
		$summary_active = " active";
	} elseif( $GLOBALS['mid'] == 20 || $GLOBALS['mid'] == 21 || $GLOBALS['mid'] == 22 ) {
		$account_active = " active";
	} elseif ( $GLOBALS['mid'] == 30 || $GLOBALS['mid'] == 31 ||
		$GLOBALS['mid'] == 32 || $GLOBALS['mid'] == 35 ) {
		$firewall_active = " active";
	} elseif( $GLOBALS['mid'] == 33 || $GLOBALS['mid'] == 34 || $GLOBALS['mid'] == 38 ) {
		$monitoring_active = " active";
	} elseif( $GLOBALS['mid'] == 36 || $GLOBALS['mid'] == 37 || $GLOBALS['mid'] == 39 ) {
		$log_active = " active";
	}

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta name="author" content="NinTechNet">
	<meta http-equiv="cache-control" content="no-store" />
	<meta http-equiv="expires" content="Mon, 01 Sep 2014 01:01:01 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
	<title>NinjaFirewall: <?php echo $menu[$GLOBALS['mid']] ?></title>
	<link rel="Shortcut Icon" type="image/gif" href="static/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
	<link href="static/bootstrap.min.css<?php echo '?ver='. NFW_ENGINE_VERSION ?>" rel="stylesheet" type="text/css">
	<link href="static/styles.css<?php echo '?ver='. NFW_ENGINE_VERSION ?>" rel="stylesheet" type="text/css">
	<?php
	// If the use has their own CSS style sheet, load it:
	if ( file_exists( 'static/user.css' ) ) {
		echo '<link href="static/user.css?ver='. NFW_ENGINE_VERSION .'" rel="stylesheet" type="text/css">'."\n";
	}
	?>
	<script src="static/nintechnet.js.php<?php echo '?ver='. NFW_ENGINE_VERSION ?>&mid=<?php echo (int)$GLOBALS['mid'] ?>"></script>
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	<?php
	if ( $load == 'chartjs' ) {
		echo "<script src='static/vendor/Chart.min.js?ver=". NFW_ENGINE_VERSION ."'></script>\n";
	}
	?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>

<body>
<!-- Navigation panel -->
<nav class="navbar navbar-default navbar-fixed-top nav_shadow" role="navigation">
	<img class="brand-logo" src="static/logo_45.png">
	<div class="container-fluid">
		<!-- Mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-main">
				<span class="sr-only"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<!-- Main navbar -->
		<div class="collapse navbar-collapse" id="navbar-collapse-main">
			<ul class="nav navbar-nav navbar-right">

				<li class="dropdown<?php echo $summary_active ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo _('Summary') ?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="?mid=10&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Overview') ?></a></li>
						<li><a href="?mid=11&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Statistics') ?></a></li>
					</ul>
				</li>

				<li class="dropdown<?php echo $account_active ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo _('Account') ?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="?mid=20&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Options') ?></a></li>
						<li><a href="?mid=21&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('License') ?></a></li>
						<li><a href="?mid=22&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Updates') ?></a></li>
					</ul>
				</li>

				<li class="dropdown<?php echo $firewall_active ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo _('Firewall') ?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="?mid=30&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Options') ?></a></li>
						<li><a href="?mid=31&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Policies') ?></a></li>
						<li><a href="?mid=32&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Access Control') .$p ?></a></li>
						<li><a href="?mid=35&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Rules Editor') ?></a></li>
					</ul>
				</li>

				<li class="dropdown<?php echo $monitoring_active ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo _('Monitoring') ?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="?mid=33&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('File Guard') .$p ?></a></li>
						<li><a href="?mid=38&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('File Check') ?></a></li>
						<li><a href="?mid=34&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Web Filter') .$p ?></a></li>
					</ul>
				</li>

				<li class="dropdown<?php echo $log_active ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo _('Logs') ?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="?mid=36&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Firewall Log') ?></a></li>
						<li><a href="?mid=37&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Live Log') .$p ?></a></li>
						<li><a href="?mid=39&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Centralized Logging') .$p ?></a></li>
					</ul>
				</li>

				<li><p class="navbar-btn"><button class="btn btn-info btn-sm" title="Click to display the contextual help for this page" data-toggle="modal" data-target="#modal-help"><?php echo _('Help') ?></button>&nbsp;&nbsp;</p></li>

				<li><p class="navbar-btn"><a href="?logout" onclick="return disconnect('<?php echo $nfw_options['admin_name'] ?>');" class="btn btn-warning btn-sm" title="Click to log out"><?php echo _('Logout') ?></a>&nbsp;&nbsp;</p></li>

			</ul>
		</div>
	</div>
</nav>
<!-- End navigation panel -->

<!-- Help panel -->
<div id="modal-help" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $menu[$GLOBALS['mid']] ?></h4>
      </div>
      <div class="modal-body"><?php
        require './lib/help.php';
        show_help();
        ?></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal"><?php echo _('Close') ?></button>
      </div>
    </div>
  </div>
</div>

<div id="main_nr" class="ntn-section">
	<div class="container">
		<div class="row ">
<?php

}

// =====================================================================

function html_footer() {

	?>
			</div> <!-- row -->
	</div><!-- container-->
</div> <!-- Main -->

<!-- Footer -->
<div id="footer" class="ntn-section">
	<div class="container footer-font">

		<div class="row">
			<div class="col-xs-6 col-sm-4 col-md-2">
				<div>
					<?php echo _('Summary') ?>
				</div>
				<div>
					<a href="?mid=10&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Overview') ?></a><br />
					<a href="?mid=11&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Statistics') ?></a><br />
					<br />
					<br />
				</div>
				<br />&nbsp;
			</div>

			<div class="col-xs-6 col-sm-4 col-md-2">
				<div>
					<?php echo _('Account') ?>
				</div>
				<div>
					<a href="?mid=20&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Options') ?></a><br />
					<a href="?mid=21&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('License') ?></a><br />
					<a href="?mid=21&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Updates') ?></a><br />
					<br />
				</div>
				<br />&nbsp;
			</div>

			<div class="col-xs-6 col-sm-4 col-md-2">
				<div>
					<?php echo _('Firewall') ?>
				</div>
				<div>
					<a href="?mid=30&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Options') ?></a><br />
					<a href="?mid=31&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Policies') ?></a><br />
					<a href="?mid=32&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Access Control') ?></a><br />
					<a href="?mid=35&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Rules Editor') ?></a>
				</div>
				<br />&nbsp;
			</div>

			<div class="col-xs-6 col-sm-4 col-md-2">
				<div>
					<?php echo _('Monitoring') ?>
				</div>
				<div>
					<a href="?mid=33&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('File Guard') ?></a><br />
					<a href="?mid=38&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('File Check') ?></a><br />
					<a href="?mid=34&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Web Filter') ?></a><br />
					<br />
				</div>
				<br />&nbsp;
			</div>

			<div class="col-xs-6 col-sm-4 col-md-2">
				<div>
					<?php echo _('Logs') ?>
				</div>
				<div>
					<a href="?mid=36&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Firewall Log') ?></a><br />
					<a href="?mid=37&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Live Log') ?></a><br />
					<a href="?mid=39&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('Centralized Logging') ?></a><br />
					<br />
				</div>
			</div>

			<div class="col-xs-6 col-sm-4 col-md-2">
				<div>
					<a style="color:#f7f7f7" href="?logout"><?php echo _('Updates info:') ?></a><br />
				</div>
				<div>
					<a href="https://twitter.com/nintechnet" title="NinjaFirewall updates info on Twitter"><img border="0" src="static/twitter.png"></a>
				</div>
				<br /><?php
					echo '<a href="https://blog.nintechnet.com/ninjafirewall-general-data-protection-regulation-compliance/">'. _('GDPR Compliance') .'</a>';
				?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="text-center">
				<?php
					echo 'NinjaFirewall v'. NFW_ENGINE_VERSION .' ('.
						sprintf( _('security rules: %s'), NFW_RULES_VERSION ) .')<br />'.
						'&copy; 2011-'. date('Y') .' <a href="https://nintechnet.com/">The Ninja Technologies Network</a>';
				?>
				</div>
			</div>
		</div>

	</div>
</div><!-- footer -->

<!-- jQuery and bootstrap JS plugins -->
<script src="static/jquery.js<?php echo '?ver='. NFW_ENGINE_VERSION ?>"></script>
<script src="static/bootstrap.min.js<?php echo '?ver='. NFW_ENGINE_VERSION ?>"></script>

<?php
	if ( $GLOBALS['mid'] == 10 || $GLOBALS['mid'] == 20 || $GLOBALS['mid'] == 30 ||
	$GLOBALS['mid'] == 31 || $GLOBALS['mid'] == 35) {
	?>
<script>
	$(document).ready(function(){
		$('[data-toggle="popover"]').popover({animation:false, trigger:'hover', placement:'top'});
	});
</script>
	<?php
	}
	?>
</body>
</html><?php

	exit;

}

// =====================================================================
// EOF
