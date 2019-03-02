<?php
/*
 +---------------------------------------------------------------------+
 | NinjaFirewall (WP Edition)                                          |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
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
 +---------------------------------------------------------------------+ i18n+ / sa
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

/* ------------------------------------------------------------------ */
// Load and save default config

function nfw_load_default_conf() {

	$nfw_rules = array();

	// We first delete all scheduled tasks if any (reinstallation etc):
	if ( wp_next_scheduled( 'nfwgccron' ) ) {
		wp_clear_scheduled_hook( 'nfwgccron' );
	}
	if ( wp_next_scheduled('nfscanevent') ) {
		wp_clear_scheduled_hook('nfscanevent');
	}
	if ( wp_next_scheduled('nfsecupdates') ) {
		wp_clear_scheduled_hook('nfsecupdates');
	}
	if ( wp_next_scheduled('nfdailyreport') ) {
		wp_clear_scheduled_hook('nfdailyreport');
	}

	if (! empty( $_SESSION['temp_admin_email'] ) ) {
		$alert_email = $_SESSION['temp_admin_email'];
	} else {
		$alert_email = get_option('admin_email');
	}

	$nfw_options = array(
		// ---------------------------------------------------------------
		// The next 6 keys must always be present because they are used
		// by the nfw_validate_option() function to check whether $nfw_options
		// is corrupted or not:
		'enabled'			=> 1,
		'blocked_msg'		=> base64_encode(NFW_DEFAULT_MSG),
		'logo'				=> plugins_url() . '/ninjafirewall/images/ninjafirewall_75.png',
		'ret_code'			=> 403,
		'scan_protocol'	=> 3,
		'get_scan'			=> 1,
		// ---------------------------------------------------------------
		'anon_ip'			=> 0,
		'debug'				=> 0,
		'uploads'			=> 1,
		'sanitise_fn'		=> 0,
		'get_sanitise'		=> 0,
		'post_scan'			=> 1,
		'post_sanitise'	=> 0,
		'cookies_scan'		=> 1,
		'cookies_sanitise'=> 0,
		'ua_scan'			=> 1,
		'ua_sanitise'		=> 1,
		'referer_scan'		=> 0,
		'referer_sanitise'=> 1,
		'referer_post'		=> 0,
		'no_host_ip'		=> 0,
		'allow_local_ip'	=> 0,
		'php_errors'		=> 1,
		'php_self'			=> 1,
		'php_path_t'		=> 1,
		'php_path_i'		=> 1,
		'wp_dir'				=> '/wp-admin/(?:css|images|includes|js)/|' .
									'/wp-includes/(?:(?:css|images|js(?!/tinymce/wp-tinymce\.php)|theme-compat)/|[^/]+\.php)|' .
									'/'. basename(WP_CONTENT_DIR) .'/(?:uploads|blogs\.dir)/',
		'no_post_themes'	=> 0,
		'force_ssl'			=> 0,
		'disallow_edit'	=> 0,
		'disallow_mods'	=> 0,
		// 3.8.2:
		'disable_error_handler'	=> 0,

		'wl_admin'			=> 1,
		// v1.0.4
		'a_0' 				=> 1,
		'a_11' 				=> 1,
		'a_12' 				=> 1,
		'a_13' 				=> 0,
		'a_14' 				=> 0,
		'a_15' 				=> 1,
		'a_16' 				=> 0,
		'a_21' 				=> 1,
		'a_22' 				=> 1,
		'a_23' 				=> 0,
		'a_24' 				=> 0,
		'a_31' 				=> 1,
		// v1.3.3 :
		'a_41' 				=> 1,
		// v1.3.4 :
		'a_51' 				=> 1,
		'sched_scan'		=> 0,
		'report_scan'		=> 0,
		// v1.7 (daily report cronjob) :
		'a_52' 				=> 1,
		// v3.4:
		'a_53' 				=> 1,

		'alert_email'	 	=> $alert_email,
		// v1.1.0 :
		'alert_sa_only'	=> 1,
		'nt_show_status'	=> 1,
		'post_b64'			=> 1,
		// v3.6.7:
		'disallow_creation'	=> 0,
		// v3.7.2:
		'disallow_settings'	=> 1,

		// v1.1.2 :
		'no_xmlrpc'			=> 0,
		// v1.7 :
		'no_xmlrpc_multi'	=> 0,
		// v3.3.2
		'no_xmlrpc_pingback'=> 0,

		// v1.1.3 :
		'enum_archives'	=> 0,
		'enum_login'		=> 0,
		// v1.1.6 :
		'request_sanitise'=> 0,
		// v1.2.1 :
		'fg_enable'			=>	0,
		'fg_mtime'			=>	10,
		'fg_exclude'		=>	'',
		// Log:
		'auto_del_log'		=>	0,
		// Updates :
		'enable_updates'	=>	1,
		'sched_updates'	=>	1,
		'notify_updates'	=>	1,
		// Centralized Logging:
		'clogs_enable'		=>	0,
		'clogs_pubkey'		=>	'',

		'rate_notice'		=>	time() + 86400 * 15,
	);
	// v1.3.1 :
	// Some compatibility checks:
	// 1. header_register_callback(): requires PHP >=5.4
	// 2. headers_list() and header_remove(): some hosts may disable them.
	if ( function_exists('header_register_callback') && function_exists('headers_list') && function_exists('header_remove') ) {
		// X-XSS-Protection:
		$nfw_options['response_headers'] = '000300000';
	}
	$nfw_options['referrer_policy_enabled'] = 0;

	define('NFUPDATESDO', 2);
	@nf_sub_updates();

	if (! $nfw_rules = @unserialize(NFW_RULES) ) {
		$err_msg = '<p><strong>'. __('Error: The installer cannot download the security rules from wordpress.org website.', 'ninjafirewall') . '</strong></p>';
		$err_msg.= '<ol><li>'. __('The server may be temporarily down or you may have network connectivity problems? Please try again in a few minutes.', 'ninjafirewall') . '</li>';
		$err_msg.= '<li>'. __('NinjaFirewall downloads its rules over an HTTPS secure connection. Maybe your server does not support SSL? You can force NinjaFirewall to use a non-secure HTTP connection by adding the following directive to your <strong>wp-config.php</strong> file:', 'ninjafirewall') . '<p><code>define("NFW_DONT_USE_SSL", 1);</code></p></li></ol>';
		exit("<br /><div class='error notice is-dismissible'>{$err_msg}</div></div></div></div></div></body></html>");
	}

	$nfw_options['engine_version'] = NFW_ENGINE_VERSION;
	$nfw_options['rules_version']  = NFW_NEWRULES_VERSION; // downloaded rules

	if ( strlen( $_SERVER['DOCUMENT_ROOT'] ) > 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', $_SERVER['DOCUMENT_ROOT'] );
	} elseif ( strlen( getenv( 'DOCUMENT_ROOT' ) ) > 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', getenv( 'DOCUMENT_ROOT' ) );
	} else {
		$nfw_rules[NFW_DOC_ROOT]['ena'] = 0;
	}

	// Enable PHP object injection rules (since v3.5.3):
	$nfw_rules[NFW_OBJECTS]['ena'] = 1;

	// ------------------------------------------------------------------
	// Update DB options and rules **BEFORE** (re)enabling scheduled tasks
	// (the garbage collect should be ran/scheduled last):
	nfw_update_option( 'nfw_options', $nfw_options);
	nfw_update_option( 'nfw_rules', $nfw_rules);
	// ------------------------------------------------------------------
	nfw_get_blogtimezone();
	wp_schedule_event( strtotime( date('Y-m-d 00:00:05', strtotime("+1 day")) ), 'daily', 'nfdailyreport');
	wp_schedule_event( time() + 3600, 'hourly', 'nfsecupdates');
	wp_schedule_event( time() + 1800, 'hourly', 'nfwgccron' );

	$_SESSION['default_conf'] = 1;

}
/* ------------------------------------------------------------------ */
// EOF //
