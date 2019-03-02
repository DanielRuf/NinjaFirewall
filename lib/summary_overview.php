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

html_header();

?>
<div class="col-sm-12 text-left">
	<h3><?php echo _('Summary > Overview') ?></h3>
	<br />

<?php
// Delete admin log?
if ( isset( $_POST['del_admin_log'] ) ) {
	@flush_admin_log();
	echo '<div class="alert alert-success text-left"><a class="close" data-dismiss="alert" aria-label="close">&times;</a>'. _('The log was deleted.') . '</div>';
}

// Is NF enabled/working?
if (! defined('NF_DISABLED') ) {
	is_nf_enabled();
}
if ( NF_DISABLED ) {
	if (! empty( $GLOBALS['err_fw'][NF_DISABLED] ) ) {
		$msg = $GLOBALS['err_fw'][NF_DISABLED];
	} else {
		$msg = 'Unknown error #' . NF_DISABLED;
	}

	echo '<div class="alert alert-danger text-left"><a class="close" data-dismiss="alert" aria-label="close">&times;</a>'.
		_('Warning, NinjaFirewall is not working and your site is not protected.') .
		'<br />' .
		sprintf( _('Error message: %s'), "<i>$msg</i>" ) .
		'</div>';

	?>
<table width="100%" class="table table-nf">
	<tr valign="middle">
		<td width="40%"><?php echo _('Firewall') ?></td>
		<td width="5%" align="center"><?php echo glyphicon( 'error' ) ?></td>
		<td width="55%"><?php
			echo _('Warning, NinjaFirewall is not working and your site is not protected.')
		?></td>
	</tr>
	<?php
} else {
	?>
<table width="100%" class="table table-nf">
	<tr valign="middle">
		<td width="40%"><?php echo _('Firewall') ?></td>
		<td width="5%">&nbsp;</td>
		<td width="55%"><?php echo _('Enabled') ?></td>
	</tr>
	<?php
}

// Check for update:
$connect_err = 0;
if ( $_SESSION['ver'] < 1 && NFW_UPDATE && ! empty( $nfw_options['login_updates'] ) ) {
	$tmp = '';
	if ( function_exists('curl_init') ) {

		// If your server can't remotely connect to a SSL port, add this
		// to your ".htninja" script: define('NFW_DONT_USE_SSL', 1);
		if ( defined( 'NFW_DONT_USE_SSL' ) ) {
			$proto = "http://";
		} else {
			$proto = "https://";
		}

		$data  = 'action=checkversion';
		$data .= '&edn=' . NFW_EDN;
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; NinjaFirewall/' . NFW_ENGINE_VERSION . ':' . NFW_EDN . ')' );
		curl_setopt( $ch, CURLOPT_ENCODING, '');
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
		curl_setopt( $ch, CURLOPT_URL, $proto. NFW_UPDATE .'/index.php' );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$tmp = @unserialize( curl_exec($ch) );
		$response = curl_getinfo( $ch );
		curl_close($ch);

		if ( empty($tmp[NFW_EDN]) || $response['http_code'] != 200 ) {
			$_SESSION['vapp'] = NFW_ENGINE_VERSION;
			$connect_err = 1;
		} else {
			$_SESSION['vapp'] = $tmp[NFW_EDN];
		}
		$_SESSION['ver'] = 1;
	}
}

?>
	<tr valign="middle">
		<td width="40%"><?php echo _('License expiration') ?></td>
		<td width="5%" align="center">&nbsp;</td>
		<td width="55%"><?php echo _('N/A') ?></td>
	</tr>

	<tr valign="middle">
		<td width="40%"><?php echo _('Version') ?></td>
		<td width="5%" align="center">
<?php

if ( $_SESSION['ver'] == 0 ) {
?>
			<?php echo glyphicon( 'warning' ) ?>
		</td>
		<td width="55%">
			<?php echo _('Unable to retrieve updates information from NinjaFirewall server.') ?>
		</td>
	</tr>
<?php

} else {

	if ( version_compare( NFW_ENGINE_VERSION, $_SESSION['vapp'], '<' ) ) {
		?>
		<?php echo glyphicon( 'warning' ) ?></td>
		<td width="55%">
			<a href="?mid=22&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('A new version is available.') .' '. _('Click here to update.') ?></a>
		</td>
	</tr>
		<?php
	} else {
		// HTTP error while checking the version ?
		if ( $connect_err ) {
			?>
			<?php echo glyphicon( 'warning' ) ?></td>
			<td width="55%">
				<?php echo _('Unable to retrieve updates information from NinjaFirewall server.') ?>
			</td>
		</tr>
      <?php
		} else {
			?>&nbsp;</td>
			<td width="55%">Pro Edition <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-changelog" title="<?php echo _('Changelog') ?>"><?php
				echo NFW_ENGINE_VERSION .'</a> '.
				sprintf(
					_('(upgrade to %s)'),
					'<a href="https://nintechnet.com/ninjafirewall/pro-edition/">Pro+ Edition</a>'
				);
			?></td>
			</tr>
			<?php
		}
	}
}

// Centralized logging: remote server
if ( ! empty( $nfw_options['clogs_pubkey'] ) ) {
	$err_msg = ''; $ok_msg = '';
	if (! preg_match( '/^[a-f0-9]{40}:([a-f0-9:.]{3,39}|\*)$/', $nfw_options['clogs_pubkey'], $match ) ) {
		$err_msg = sprintf(
			_('The public key is invalid. Please %scheck your configuration%s.'),
			'<a href="?mid=36&token='. $_REQUEST['token'] .'#clogs">', '</a>'
		);

	} else {
		if ( $match[1] == '*' ) {
			$ok_msg = _('No IP address restriction.');

		} elseif ( filter_var( $match[1], FILTER_VALIDATE_IP ) ) {
			$ok_msg = sprintf(
				_('IP address %s is allowed to access NinjaFirewall\'s log on this server.'),
				htmlspecialchars( $match[1])
			);

		} else {
			$err_msg = sprintf(
				_('The whitelisted IP is not valid. Please %scheck your configuration%s.'),
				'<a href="?mid=36&token='. $_REQUEST['token'] .'#clogs">', '</a>'
			);
		}
	}
	?>
	<tr>
		<td width="40%"><?php echo _('Centralized Logging') ?></th>
		<?php
		if ( $err_msg ) {
			?>
		<td width="5%" align="center"><?php echo glyphicon( 'warning' ) ?></td>
		<td width="55%"><?php printf( _('Error: %s'), $err_msg) ?></td>
	</tr>
		<?php
		$err_msg = '';
	} else {
		?>
			<td width="5%">&nbsp;</td>
			<td width="55%"><a href="?mid=36&token=<?php echo $_REQUEST['token'] ?>#clogs"><?php echo _('Enabled'); echo "</a>. $ok_msg"; ?></td>
		</tr>
	<?php
	}
}

// Is debug mode on?
if ( $nfw_options['debug'] ) {
	?>
	<tr valign="middle">
		<td width="40%"><?php echo _('Debugging') ?></td>
		<td width="5%" align="center"><?php echo glyphicon( 'warning' ) ?></td>
		<td width="55%">
			<?php printf(
				_('NinjaFirewall is running in <i>Debug Mode</i>, do not forget to <a href="%s">disable it</a> before going live.'),
				 '?mid=30&token='.$_REQUEST['token']
			) ?>
		</td>
	</tr>
	<?php
}
// Is logging on?
if (! $nfw_options['logging'] ) {
	?>
	<tr valign="middle">
		<td width="40%"><?php echo _('Firewall Log') ?></td>
		<td width="5%" align="center"><?php echo glyphicon( 'warning' ) ?></td>
		<td width="55%">
			<?php printf(
				_('The log is disabled.') .' <a href="%s">'. _('Click here to re-enable it') .'</a>.',
				"?mid=36&token={$_REQUEST['token']}"
			) ?>
		</td>
	</tr>
	<?php
}

$IPlink = "?mid=32&token={$_REQUEST['token']}#source-ip";

// Check IP and warn if localhost or private IP:
if (! filter_var( NFW_REMOTE_ADDR, FILTER_VALIDATE_IP,
	FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) ) {
	?>
	<tr valign="middle">
		<td width="40%"><?php echo _('Source IP') ?></td>
		<td width="5%" align="center"><?php echo glyphicon( 'warning' ) ?></td>
		<td width="55%"><?php
			echo sprintf( _('You have a private IP: %s'), htmlspecialchars(NFW_REMOTE_ADDR) ) .'<br />'.
			sprintf( _('If your site is behind a reverse proxy or a load balancer, ' .
				'ensure that the <a href="%s">Source IP</a> is setup accordingly.'),
				$IPlink
			) ?>
		</td>
	</tr>
	<?php
}

// Look for CDN's (Incapsula/Cloudflare) and warn the user about using
// the correct IPs, unless it was added to the access control list:
if (! empty( $_SERVER["HTTP_CF_CONNECTING_IP"] ) ) {
	if ( NFW_REMOTE_ADDR != $_SERVER["HTTP_CF_CONNECTING_IP"] ) {
		// CloudFlare :
		?>
		<tr valign="middle">
			<td width="40%"><?php echo _('CDN detection') ?></td>
			<td width="5%" align="center"><?php echo glyphicon( 'warning' ) ?></td>
			<td width="55%">
				<?php
				if ( NFW_EDN == 1 ) {
					echo sprintf( _('%s detected: you seem to be using %s CDN services.'), 'HTTP_CF_CONNECTING_IP', 'Cloudflare') .' '.
					sprintf( _('Ensure that you have setup your HTTP server or PHP to forward the correct visitor IP, otherwise use the <a href="%s">.htninja configuration file</a>.'), $IPlink );
				} else {
					echo sprintf( _('%s detected: you seem to be using %s CDN services.'), 'HTTP_CF_CONNECTING_IP', 'Cloudflare') .' '.
					sprintf( _('Ensure that the <a href="%s">Source IP</a> is setup accordingly.'), $IPlink );
				}
				?>
			</td>
		</tr>
		<?php
	}
}
if (! empty( $_SERVER["HTTP_INCAP_CLIENT_IP"] ) ) {
	if ( NFW_REMOTE_ADDR != $_SERVER["HTTP_INCAP_CLIENT_IP"] ) {
		// Incapsula :
		?>
		<tr valign="middle">
			<td width="40%"><?php echo _('CDN detection') ?></td>
			<td width="5%" align="center"><?php echo glyphicon( 'warning' ) ?></td>
			<td width="55%">
				<?php
				if ( NFW_EDN == 1 ) {
					echo sprintf( _('%s detected: you seem to be using %s CDN services.'), 'HTTP_INCAP_CLIENT_IP', 'Incapsula') .' '.
					sprintf( _('Ensure that you have setup your HTTP server or PHP to forward the correct visitor IP, otherwise use the <a href="%s">.htninja configuration file</a>.'), $IPlink );
				} else {
					echo sprintf( _('%s detected: you seem to be using %s CDN services.'), 'HTTP_INCAP_CLIENT_IP', 'Incapsula') .' '.
					sprintf( _('Ensure that the <a href="%s">Source IP</a> is setup accordingly.'), $IPlink );
				}
				?>
			</td>
		</tr>
		<?php
	}
}

// Check if the ./nfwlog/ directory is writable:
if (! is_writable( './nfwlog' ) ) {
	?>
	<tr valign="middle">
		<td width="40%"><?php echo _('Log Directory') ?></td>
		<td width="5%" align="center"><?php echo glyphicon( 'error' ) ?></span></td>
		<td width="55%"><?php
			printf( _('Warning, the "%s" directory is not writable, please chmod it to 0777 or equivalent.'), '/nfwlog/' );
		?></td>
	</tr>
	<?php
}

// Check if the ./nfwlog/cache/ directory is writable:
if (! is_writable( './nfwlog/cache/' ) ) {
	?>
	<tr valign="middle">
		<td width="40%"><?php echo _('Cache Directory') ?></td>
		<td width="5%" align="center"><?php echo glyphicon( 'error' ) ?></td>
		<td width="55%"><?php
			printf( _('Warning, the "%s" directory is not writable, please chmod it to 0777 or equivalent.'), '/nfwlog/cache/' );
		?></td>
	</tr>
	<?php
}

// Check if the ./conf/ directory is writable:
if (! is_writable( './conf' ) ) {
	?>
	<tr valign="middle">
		<td width="40%"><?php echo _('Configuration Directory') ?></td>
		<td width="5%" align="center"><?php echo glyphicon( 'error' ) ?></td>
		<td width="55%"><?php
			printf( _('Warning, the "%s" directory is not writable, please chmod it to 0777 or equivalent.'), '/conf/' );
		?></td>
	</tr>
	<?php
}
// Optional NinjaFirewall .htninja configuration file
// ( see https://nintechnet.com/ninjafirewall/pro-edition/help/?htninja ) :
$doc_root = rtrim( $_SERVER['DOCUMENT_ROOT'], '/' );
if ( @file_exists( $file = dirname( $doc_root ) . '/.htninja') ||
		@file_exists( $file = $doc_root . '/.htninja') ) {
	?>
	<tr>
		<td width="40%"><?php echo _('Optional configuration file') ?></td>
		<td width="5%" align="center">&nbsp;</td>
		<td><code><?php echo htmlspecialchars( $file ) ?></code></td>
	</tr>
	<?php
}

// Check if admin is whitelisted:
if (! empty( $nfw_options['admin_wl'] ) && ! empty( $_COOKIE['ninjadmin'] ) &&
	! empty( $nfw_options['admin_wl_session'] ) ) {

	if ( sha1( $_COOKIE['ninjadmin'] . $nfw_options['admin_pass'] ) === $nfw_options['admin_wl_session'] ) {
		$whitelisted = _('You are whitelisted.');
	}
}
if ( empty( $whitelisted ) ) {
	?>
	<tr valign="middle">
		<td width="40%"><?php echo _('Administrator') ?></td>
		<td width="5%" align="center"><?php echo glyphicon( 'warning' ) ?></td>
		<td width="55%"><a href="?mid=32&token=<?php echo $_REQUEST['token'] ?>"><?php echo _('You are not whitelisted.') ?></a></td>
	</tr>
	<?php
} else {
	?>
	<tr valign="middle">
		<td width="40%"><?php echo _('Administrator') ?></td>
		<td width="5%" align="center">&nbsp;</td>
		<td width="55%"><a href="?mid=32&token=<?php echo $_REQUEST['token'] ?>"><?php echo $whitelisted ?></a></td>
	</tr>
	<?php
}

// Check admin log:
if ( file_exists( './nfwlog/admin.php' ) ) {
	$nfw_stat = file( './nfwlog/admin.php', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

	// Skip the last line, it is us, unless there is only one connection:
	if ( count( $nfw_stat ) > 1 ) {
		array_pop( $nfw_stat );
	}
	while ( $nfw_stat ) {
		// Get last connection :
		$line = array_pop( $nfw_stat );
		if ( preg_match('/^\[([^\s]+)\s+([^\s]+).+?\]\s+\[(.+?)\]\s+\[(.+?)\]\s+\[OK/', $line, $match) ) {
			break;
		}
	}
	if (! empty( $match[1] ) ) {
		?>
		<tr valign="middle">
			<td width="40%"><?php echo _('Last login') ?></td>
			<td width="5%" align="center">&nbsp;</td>
			<td width="55%"><a href="javascript:void(0)" data-toggle="modal" data-target="#modal-admin-log" title="<?php echo _('Admin Log') ?>"><?php
				echo htmlspecialchars( $match[3] ) .' ('. htmlspecialchars( $match[4] ) .')</a> ~ '.
				str_replace( '/', '-', $match[1] ) .' @ '. $match[2] ?></td>
		</tr>
		<?php
	}
}
?>
	</table>
</div>

<!-- Admin log -->
<div id="modal-admin-log" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo _('Admin Log') ?></h4>
      </div>
      <div class="modal-body"><?php
        @raw_admin_log();
        ?></div>
      <div class="modal-footer">
			<form method="post" onsubmit="return dellog();">
				<input type="hidden" name="mid" value="<?php echo $GLOBALS['mid'] ?>" />
				<input type="hidden" name="del_admin_log" value="1" />
				<input class="btn btn-md btn-info btn-sm" type="button" value="<?php echo _('Close') ?>" data-dismiss="modal" />
				&nbsp;&nbsp;&nbsp;
				<input class="btn btn-md btn-danger btn-sm" type="submit" value="<?php echo _('Delete log') ?>" />
			</form>
      </div>
    </div>
  </div>
</div>
<!-- Changelog -->
<div id="modal-changelog" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo _('Changelog') ?></h4>
      </div>
      <div class="modal-body"><?php
        include 'changelog.php';
        ?>
			<textarea class="form-control" style="height:400px;font-family:Consolas,Monaco,monospace;resize:vertical" wrap="on"><?php echo htmlspecialchars( $changelog ); ?></textarea>
		</div>
      <div class="modal-footer">
			<input class="btn btn-md btn-info btn-sm" type="button" value="<?php echo _('Close') ?>" data-dismiss="modal" />
      </div>
    </div>
  </div>
</div>
<?php

html_footer();

// ---------------------------------------------------------------------
// EOF

