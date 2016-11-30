<?php
// +---------------------------------------------------------------------+
// | NinjaFirewall (Pro edition)                                         |
// |                                                                     |
// | (c) NinTechNet - http://nintechnet.com/                             |
// |                                                                     |
// +---------------------------------------------------------------------+
// | This program is free software: you can redistribute it and/or       |
// | modify it under the terms of the GNU General Public License as      |
// | published by the Free Software Foundation, either version 3 of      |
// | the License, or (at your option) any later version.                 |
// |                                                                     |
// | This program is distributed in the hope that it will be useful,     |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of      |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       |
// | GNU General Public License for more details.                        |
// +---------------------------------------------------------------------+

if ( strpos($_SERVER['SCRIPT_NAME'], '/nfwlog/') !== FALSE
	|| $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) { die('Forbidden'); }
if (defined('NFW_STATUS')) { return; }

$nfw_['fw_starttime'] = microtime(true);

// Optional NinjaFirewall configuration file
// ( see http://nintechnet.com/ninjafirewall/pro-edition/help/?htninja ) :
if ( @file_exists($nfw_['file'] = dirname($_SERVER['DOCUMENT_ROOT']) .'/.htninja') ||
	@file_exists($nfw_['file'] = $_SERVER['DOCUMENT_ROOT'] .'/.htninja') ) {
	$nfw_['res'] = @include($nfw_['file']);
	if ( $nfw_['res'] == 'ALLOW' ) {
		define( 'NFW_STATUS', 22 );
		unset($nfw_);
		return;
	}
	if ( $nfw_['res'] == 'BLOCK' ) {
		header('HTTP/1.1 403 Forbidden');
		header('Status: 403 Forbidden');
		die('403 Forbidden');
	}
}

if (! @include( __DIR__ . '/conf/options.php') ) {
	define( 'NFW_STATUS', 1 );
	unset($nfw_);
	return;
}

$nfw_['nfw_options'] = @unserialize($nfw_options);
if (! isset( $nfw_['nfw_options']['enabled']) ) {
	define( 'NFW_STATUS', 2 );
	unset($nfw_);
	return;
}

if (! empty($nfw_['nfw_options']['clogs_pubkey']) && isset($_POST['clogs_req']) ) {
	include( __DIR__ . '/lib/fw_centlog.php');
	fw_centlog(__DIR__ . '/nfwlog');
	exit;
}

if ( empty($nfw_['nfw_options']['enabled']) ) {
	define( 'NFW_STATUS', 22 );
	unset($nfw_);
	return;
}

if (! empty($nfw_['nfw_options']['response_headers']) && function_exists('header_register_callback')) {
	define('NFW_RESHEADERS', $nfw_['nfw_options']['response_headers']);
	if (! empty( $nfw_['nfw_options']['response_headers'][6] ) && ! empty( $nfw_['nfw_options']['csp_frontend_data'] ) ) {
		define( 'CSP_FRONTEND_DATA', $nfw_['nfw_options']['csp_frontend_data']);
	}
	header_register_callback('nfw_response_headers');
}

nfw_check_ip();

if (! empty($nfw_['nfw_options']['php_errors']) ) {
	@error_reporting(0);
	@ini_set('display_errors', 0);
}

if ( @$nfw_['nfw_options']['scan_protocol'] == 1 && $_SERVER['SERVER_PORT'] == 443 ) {
	define( 'NFW_STATUS', 22 );
	unset($nfw_);
	return;
}
if ( @$nfw_['nfw_options']['scan_protocol'] == 2 && $_SERVER['SERVER_PORT'] != 443 ) {
	define( 'NFW_STATUS', 22 );
	unset($nfw_);
	return;
}

if ( $_SERVER['SCRIPT_FILENAME'] == __DIR__ .'/index.php' || $_SERVER['SCRIPT_FILENAME'] == __DIR__ .'/login.php' || $_SERVER['SCRIPT_FILENAME'] == __DIR__ .'/install.php' ) {
	if (! @include( __DIR__ . '/conf/rules.php') ) {
		define( 'NFW_STATUS', 3 );
	} else {
		$nfw_['nfw_rules'] = @unserialize($nfw_rules);
		if (! isset( $nfw_['nfw_rules']['1']) ) {
			define( 'NFW_STATUS', 4 );
		} else {
			define( 'NFW_STATUS', 22 );
		}
	}
	unset($nfw_);
	return;
}

if (! empty($nfw_['nfw_options']['ban_ip']) ) {
	$nfw_['ipbk'] = __DIR__ .'/nfwlog/cache/ipbk.'. $_SERVER['SERVER_NAME'] .'_-_'. NFW_REMOTE_ADDR .'.php';
	if (file_exists($nfw_['ipbk']) ) {
		$nfw_['stat'] = stat($nfw_['ipbk']);
		if ( time() - $nfw_['stat']['mtime'] > $nfw_['nfw_options']['ban_time'] * 60 ) {
			@unlink($nfw_['ipbk']);
		} else {
			nfw_block(0);
		}
	}
}

if (! empty($nfw_['nfw_options']['request_method']) ) {
	if ( strpos('GETPOSTHEAD', $_SERVER['REQUEST_METHOD']) === false ) {
		nfw_log('REQUEST_METHOD is not allowed by policy', $_SERVER['REQUEST_METHOD'], 2, 0);
		nfw_block(2);
	}
}

if (! empty($nfw_['nfw_options']['no_host_ip']) && @filter_var(parse_url('http://'.$_SERVER['HTTP_HOST'], PHP_URL_HOST), FILTER_VALIDATE_IP) ) {
	nfw_log('HTTP_HOST is an IP', $_SERVER['HTTP_HOST'], 2, 0);
   nfw_block(2);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (! empty($nfw_['nfw_options']['referer_post']) && empty($_SERVER['HTTP_REFERER']) ) {
		nfw_log('POST method without HTTP_REFERER header', 'N/A', 1, 0);
		nfw_block(1);
	}
	$ua = ! empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'N/A';
	if (! empty($nfw_['nfw_options']['ua_mozilla']) && stripos($ua, 'Mozilla') === FALSE ) {
		nfw_log('POST method from user-agent without Mozilla-compatible signature', $ua, 1, 0);
		nfw_block(1);
	}
	if (! empty($nfw_['nfw_options']['ua_accept']) && empty($_SERVER['HTTP_ACCEPT']) ) {
		nfw_log('POST method without HTTP_ACCEPT header', 'N/A', 1, 0);
		nfw_block(1);
	}
	if (! empty($nfw_['nfw_options']['ua_accept_lang']) && empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
		nfw_log('POST method without HTTP_ACCEPT_LANGUAGE header', 'N/A', 1, 0);
		nfw_block(1);
	}
}

nfw_check_upload();

if (! include( __DIR__ . '/conf/rules.php') ) {
	define( 'NFW_STATUS', 3 );
	unset($nfw_);
	return;
}
$nfw_['nfw_rules'] = @unserialize($nfw_rules);
if (! isset( $nfw_['nfw_rules']['1']) ) {
	define( 'NFW_STATUS', 4 );
	unset($nfw_);
	return;
}

nfw_check_request( $nfw_['nfw_rules'], $nfw_['nfw_options'] );

if (! empty($nfw_['nfw_options']['get_sanitise']) && ! empty($_GET) ){
	$_GET = nfw_sanitise( $_GET, 'GET');
}
if (! empty($nfw_['nfw_options']['post_sanitise']) && ! empty($_POST) ){
	$_POST = nfw_sanitise( $_POST, 'POST');
}
if (! empty($nfw_['nfw_options']['request_sanitise']) && ! empty($_REQUEST) ){
	$_REQUEST = nfw_sanitise( $_REQUEST, 'REQUEST');
}
if (! empty($nfw_['nfw_options']['cookies_sanitise']) && ! empty($_COOKIE) ) {
	$_COOKIE = nfw_sanitise( $_COOKIE, 'COOKIE');
}
if (! empty($nfw_['nfw_options']['ua_sanitise']) && ! empty($_SERVER['HTTP_USER_AGENT']) ) {
	$_SERVER['HTTP_USER_AGENT'] = nfw_sanitise( $_SERVER['HTTP_USER_AGENT'], 'HTTP_USER_AGENT');
}
if (! empty($nfw_['nfw_options']['referer_sanitise']) && ! empty($_SERVER['HTTP_REFERER']) ) {
	$_SERVER['HTTP_REFERER'] = nfw_sanitise( $_SERVER['HTTP_REFERER'], 'HTTP_REFERER');
}
if (! empty($nfw_['nfw_options']['php_path_i']) && ! empty($_SERVER['PATH_INFO']) ) {
	$_SERVER['PATH_INFO'] = nfw_sanitise( $_SERVER['PATH_INFO'], 'PATH_INFO');
}
if (! empty($nfw_['nfw_options']['php_path_t']) && ! empty($_SERVER['PATH_TRANSLATED']) ) {
	$_SERVER['PATH_TRANSLATED'] = nfw_sanitise( $_SERVER['PATH_TRANSLATED'], 'PATH_TRANSLATED');
}
if (! empty($nfw_['nfw_options']['php_self']) && ! empty($_SERVER['PHP_SELF']) ) {
	$_SERVER['PHP_SELF'] = nfw_sanitise( $_SERVER['PHP_SELF'], 'PHP_SELF');
}

if (! empty($nfw_['dblink']) ) { @$nfw_['dblink']->close(); }
unset($nfw_);
define( 'NFW_STATUS', 22 );

return;

// =====================================================================

function nfw_check_ip() {

	if ( defined('NFW_REMOTE_ADDR') ) { return; }

	global $nfw_;

	if (strpos($_SERVER['REMOTE_ADDR'], ',') !== false) {
		// Ensure we have a proper and single IP (a user may use the .htninja file
		// to redirect HTTP_X_FORWARDED_FOR, which may contain more than one IP,
		// to REMOTE_ADDR):
		$nfw_['match'] = array_map('trim', @explode(',', $_SERVER['REMOTE_ADDR']));
		foreach($nfw_['match'] as $nfw_['m']) {
			if ( filter_var($nfw_['m'], FILTER_VALIDATE_IP) )  {
				define( 'NFW_REMOTE_ADDR', $nfw_['m']);
				break;
			}
		}
	}
	if (! defined('NFW_REMOTE_ADDR') ) {
		define('NFW_REMOTE_ADDR', htmlspecialchars($_SERVER['REMOTE_ADDR']) );
	}
}

// =====================================================================

function nfw_log($loginfo, $logdata, $loglevel, $ruleid) {

	global $nfw_;

	if ( $loglevel == 6) {
		$nfw_['num_incident'] = '0000000';
		$http_ret_code = '200';
	} else {
		if (! empty($nfw_['nfw_options']['debug']) ) {
			$nfw_['num_incident'] = '0000000';
			$loglevel = 7;
			$http_ret_code = '200';
		} else {
			$nfw_['num_incident'] = mt_rand(1000000, 9000000);
			$http_ret_code = $nfw_['nfw_options']['ret_code'];
		}
	}

	if (empty($nfw_['nfw_options']['logging']) ) {
		return;
	}

   if (strlen($logdata) > 200) { $logdata = mb_substr($logdata, 0, 200, 'utf-8') . '...'; }
	$res = '';
	$string = str_split($logdata);
	foreach ( $string as $char ) {
		if ( ord($char) < 32 || ord($char) > 126 ) {
			$res .= '%' . bin2hex($char);
		} else {
			$res .= $char;
		}
	}

	if (empty($nfw_['nfw_options']['tzset']) ) {
		date_default_timezone_set($nfw_['nfw_options']['timezone']);
	}

	$cur_month = date('Y-m');
	$log_file = __DIR__ . '/nfwlog/firewall_' . $cur_month;
	$log_file_ext = $log_file . '.php';

	if (! empty($nfw_['nfw_options']['log_rotate']) && @ctype_digit($nfw_['nfw_options']['log_maxsize']) ) {
		if ( file_exists($log_file_ext) ) {
			$log_stat = stat($log_file_ext);
			if ( $log_stat['size'] > $nfw_['nfw_options']['log_maxsize']) {
				$log_ext = 1;
				while ( file_exists($log_file . '.' . sprintf('%02d', $log_ext) . '.php' ) ) {
					$log_ext++;
				}
				rename($log_file_ext, $log_file . '.' . sprintf('%02d', $log_ext) . '.php');
			}
		}
	}

	if (! file_exists($log_file_ext) ) {
		$tmp = '<?php exit; ?>' . "\n";
	} else {
		$tmp = '';
	}

   @file_put_contents($log_file_ext,
      $tmp . '[' . time() . '] ' . '[' . round( (microtime(true) - $nfw_['fw_starttime']), 5) . '] ' .
      '[' . $_SERVER['SERVER_NAME'] . '] ' . '[#' . $nfw_['num_incident'] . '] ' .
      '[' . $ruleid . '] ' .
      '[' . $loglevel . '] ' . '[' . NFW_REMOTE_ADDR . '] ' .
      '[' . $http_ret_code . '] ' . '[' . $_SERVER['REQUEST_METHOD'] . '] ' .
      '[' . $_SERVER['SCRIPT_NAME'] . '] ' . '[' . $loginfo . '] ' .
      '[hex:' . array_shift( unpack('H*', $res) ) . ']' . "\n", FILE_APPEND | LOCK_EX);

}

// =====================================================================

function nfw_block( $lev ) {

	if ( defined('NFW_STATUS') ) { return; }

	global $nfw_;

	if (! empty($nfw_['nfw_options']['debug']) ) {
		return;
	}

	$http_codes = array(
      400 => '400 Bad Request', 403 => '403 Forbidden',
      404 => '404 Not Found', 406 => '406 Not Acceptable',
      500 => '500 Internal Server Error', 503 => '503 Service Unavailable',
   );

	if (empty($nfw_['num_incident']) ) { $nfw_['num_incident'] = '000000'; }
	$tmp = str_replace( '%%NUM_INCIDENT%%', $nfw_['num_incident'],  base64_decode($nfw_['nfw_options']['blocked_msg']) );
	$tmp = str_replace( '%%REM_ADDRESS%%', NFW_REMOTE_ADDR, $tmp );
	if (! headers_sent() ) {
		header('HTTP/1.0 ' . $http_codes[$nfw_['nfw_options']['ret_code']] );
		header('Status: ' .  $http_codes[$nfw_['nfw_options']['ret_code']] );
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate, proxy-revalidate');
		header('Expires: Mon, 01 Sep 2014 01:01:01 GMT');
	}

	echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">' . "\n" .
		'<html><head><title>' . $http_codes[$nfw_['nfw_options']['ret_code']] .
		'</title><style>body{font-family:sans-serif;font-size:13px;color:#000000;}</style><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body bgcolor="white">' . $tmp . '</body></html>';

	if ($lev > 0 && $lev < 4 && $nfw_['nfw_options']['ban_ip']) {
		if ( $nfw_['nfw_options']['ban_ip'] == 3 || ($nfw_['nfw_options']['ban_ip'] == 2 && $lev > 1) || ($nfw_['nfw_options']['ban_ip'] == 1 && $lev == 3) ) {
			touch( __DIR__ .'/nfwlog/cache/ipbk.'. $_SERVER['SERVER_NAME'] .'_-_'. NFW_REMOTE_ADDR .'.php');
			nfw_log('Banning IP for ' . $nfw_['nfw_options']['ban_time'] . ' minute(s)', 'User IP: '. NFW_REMOTE_ADDR, 6, 0);
		}
	}
	exit;
}

// =====================================================================

function nfw_check_upload() {

	if ( defined('NFW_STATUS') ) { return; }

	global $nfw_;

	$f_uploaded = nfw_fetch_uploads();
	$tmp = '';
	if ( empty($nfw_['nfw_options']['uploads']) ) {
		$tmp = '';
		foreach ($f_uploaded as $key => $value) {
			if (! $f_uploaded[$key]['name']) { continue; }
			$tmp .= $f_uploaded[$key]['name'] . ' (' . number_format($f_uploaded[$key]['size']) . ' bytes) ';
      }
      if ( $tmp ) {
			nfw_log('Blocked file upload attempt', rtrim($tmp, ' '), 3, 0);
			nfw_block(3);
		}
	} else {
		foreach ($f_uploaded as $key => $value) {
			if (! $f_uploaded[$key]['name']) { continue; }
			if ( $f_uploaded[$key]['size'] > $nfw_['nfw_options']['upload_maxsize'] ) {
				nfw_log('Attempt to upload a file > ' . ($nfw_['nfw_options']['upload_maxsize'] / 1024) .
					' KB' , $f_uploaded[$key]['name'] . ' (' . number_format($f_uploaded[$key]['size']) . ' bytes)', 1, 0);
				nfw_block(1);
			}
			$data = '';
			if ( $nfw_['nfw_options']['uploads'] == 2 ) {
				if (preg_match('/\.ht(?:access|passwd)|(?:php\d?|\.user)\.ini|\.ph(?:p[345]?|t|tml)\b/', $f_uploaded[$key]['name']) ) {
					nfw_log('Attempt to upload a script or system file', $f_uploaded[$key]['name'] . ' (' . number_format($f_uploaded[$key]['size']) . ' bytes)', 3, 0);
					nfw_block(3);
				}
				$data = file_get_contents($f_uploaded[$key]['tmp_name']);
				if (preg_match('`^\x7F\x45\x4C\x46`', $data) ) {
					nfw_log('Attempt to upload a Linux binary file (ELF)', $f_uploaded[$key]['name'] . ' (' . number_format($f_uploaded[$key]['size']) . ' bytes)', 3, 0);
					nfw_block(3);
				}
				if (preg_match('`<\?(?i:php|=)|#!/(?:usr|bin)/.+?\s|\s#include\s+<[\w/.]+?>|\b(?i:array_map|base64_(?:de|en)code|eval|file(?:_get_contents)?|fsockopen|gzinflate|move_uploaded_file|passthru|preg_replace|phpinfo|system|(?:shell_)?exec)\s*\(|\b(?:\$?_(COOKIE|ENV|FILES|(?:GE|POS|REQUES)T|SE(RVER|SSION))|HTTP_(?:(?:POST|GET)_VARS|RAW_POST_DATA)|GLOBALS)\s*[=\[]|\W\$\{\s*[\'"]\w+[\'"]`', $data) ) {
					nfw_log('Attempt to upload a script', $f_uploaded[$key]['name'] . ' (' . number_format($f_uploaded[$key]['size']) . ' bytes)', 3, 0);
					nfw_block(3);
				}
			}

			if ( $f_uploaded[$key]['size'] > 67 && $f_uploaded[$key]['size'] < 129 ) {
				if ( empty($data) ) {
					$data = file_get_contents( $f_uploaded[$key]['tmp_name'] );
				}
				if ( preg_match('`X5O!P%@AP' . '\[4\\\PZX54\(P\^\)7CC\)7}\$EIC' .
				                'AR-STANDARD-ANTIVI' . 'RUS-TEST-FILE!\$H' . '\+H\*' .
				                '[\x09\x10\x13\x20\x1A]*`', $data) ) {
					nfw_log('EICAR Standard Anti-Virus Test File blocked', $f_uploaded[$key]['name'] . ' (' . number_format($f_uploaded[$key]['size']) . ' bytes)', 3, 0);
					nfw_block(3);
				}
			}

			if (! empty($nfw_['nfw_options']['sanitise_fn']) ) {
				$tmp = '';
				$f_uploaded[$key]['name'] = preg_replace('/[^\w\.\-]/i', 'X', $f_uploaded[$key]['name'], -1, $count);
				if ($count) {
					$tmp = ' (sanitising '. $count . ' char. from filename)';
				}
				if ( $tmp ) {
					list ($kn, $is_arr, $kv) = explode('::', $f_uploaded[$key]['where']);
					if ( $is_arr ) {
						$_FILES[$kn]['name'][$kv] = $f_uploaded[$key]['name'];
					} else {
						$_FILES[$kn]['name'] = $f_uploaded[$key]['name'];
					}
				}
			}
			nfw_log('File upload detected, no action taken' . $tmp , $f_uploaded[$key]['name'] . ' (' . number_format($f_uploaded[$key]['size']) . ' bytes)', 5, 0);
		}
	}
}

// =====================================================================

function nfw_fetch_uploads() {

	$f_uploaded = array();
	$count = 0;
	foreach ($_FILES as $nm => $file) {
		if ( is_array($file['name']) ) {
			foreach($file['name'] as $key => $value) {
				$f_uploaded[$count]['name'] = $file['name'][$key];
				$f_uploaded[$count]['type'] = $file['type'][$key];
				$f_uploaded[$count]['size'] = $file['size'][$key];
				$f_uploaded[$count]['tmp_name'] = $file['tmp_name'][$key];
				$f_uploaded[$count]['where'] = $nm . '::1::' . $key;
				$count++;
			}
		} else {
			$f_uploaded[$count]['name'] = $file['name'];
			$f_uploaded[$count]['type'] = $file['type'];
			$f_uploaded[$count]['size'] = $file['size'];
			$f_uploaded[$count]['tmp_name'] = $file['tmp_name'];
			$f_uploaded[$count]['where'] = $nm . '::0::0' ;
			$count++;
		}
	}
	return $f_uploaded;
}

// =====================================================================

function nfw_check_request( $nfw_rules, $nfw_options ) {

	if ( defined('NFW_STATUS') ) { return; }

	global $nfw_;

	foreach ( $nfw_rules as $id => $rules ) {

		if ( empty( $rules['ena']) ) {
			continue;
		}

		$wherelist = explode('|', $rules['cha'][1]['whe']);

		foreach ($wherelist as $where) {

			if ( nfw_disabled_scan( $where, $nfw_options ) ) { continue; }

			// =================================================================
			if ( $where == 'RAW' ) {
				if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) { continue; }

				$RAW_POST = file_get_contents( 'php://input' );

				if ( nfw_matching( 'RAW', 'POST', $nfw_rules, $rules, 1, $id, $RAW_POST, $nfw_options ) ) {
					nfw_check_subrule( 'RAW', 'POST', $nfw_rules, $nfw_options, $rules, $id );
				}
				continue;
			}

			// =================================================================
			if ( $where == 'POST' || $where == 'GET' || $where == 'COOKIE' ||
				$where == 'SERVER' || $where == 'REQUEST' || $where == 'FILES' ||
				$where == 'SESSION'
			) {

				if ( empty($GLOBALS['_' . $where]) ) {continue;}

				foreach ($GLOBALS['_' . $where] as $key => $val) {

					if ( nfw_matching( $where, $key, $nfw_rules, $rules, 1, $id, null, $nfw_options ) ) {
						nfw_check_subrule( $where, $key, $nfw_rules, $nfw_options, $rules, $id );
					}

				}

				continue;
			}

			// =================================================================

			if ( isset( $_SERVER[$where] ) ) {

				if ( nfw_matching( 'SERVER', $where, $nfw_rules, $rules, 1, $id, null, $nfw_options ) ) {
					nfw_check_subrule( 'SERVER', $where, $nfw_rules, $nfw_options, $rules, $id );
				}
				continue;
			}

			// =================================================================

			$w = explode(':', $where);

			if ( empty($w[1]) || ! isset( $GLOBALS['_'.$w[0]][$w[1]] ) || nfw_disabled_scan( $w[0], $nfw_options ) ) {
				continue;
			}

			if ( nfw_matching( $w[0], $w[1], $nfw_rules, $rules, 1, $id, null, $nfw_options ) ) {
				nfw_check_subrule( $w[0], $w[1], $nfw_rules, $nfw_options, $rules, $id );
			}

			// =================================================================

		}

	}

}

// =====================================================================

function nfw_check_subrule( $w0, $w1, $nfw_rules, $nfw_options, $rules, $id ) {

	if ( isset( $rules['cha'][1]['cap'] ) ) {
		nfw_matching( $w0, $w1, $nfw_rules, $rules, 2, $id, null, $nfw_options );

	} else {
		$w = explode(':', $rules['cha'][2]['whe']);

		if (! isset( $w[1] ) ) {
			if ( $w[0] == 'RAW' ) {
				if ( nfw_disabled_scan( 'POST', $nfw_options) || $_SERVER['REQUEST_METHOD'] != 'POST' ) {
					return;
				}
				nfw_matching( 'POST', 'RAW', $nfw_rules, $rules, 2, $id, file_get_contents( 'php://input' ), $nfw_options );
				return;
			}
			$w[2] = $w[1] = $w[0];
			$w[0] = 'SERVER';
		} else {
			$w[2] = null;
		}

		if (! isset( $GLOBALS['_'.$w[0]][$w[1]] ) ) {
			return;
		}

		if ( nfw_disabled_scan( $w[0], $nfw_options, $w[2] ) ) {
			return;
		} else {
			nfw_matching( $w[0], $w[1], $nfw_rules, $rules, 2, $id, null, $nfw_options);
		}
	}

}

// =====================================================================

function nfw_disabled_scan( $where, $nfw_options, $extra = null ) {

	if ( $extra ) { $where = $extra; }

	if ( $where == 'POST' && empty($nfw_options['post_scan']) ||
		$where == 'GET' && empty($nfw_options['get_scan']) ||
		$where == 'COOKIE' && empty($nfw_options['cookies_scan']) ||
		$where == 'HTTP_USER_AGENT' && empty($nfw_options['ua_scan']) ||
		$where == 'HTTP_REFERER' && empty($nfw_options['referer_scan'])
	) {
		return 1;
	}
	return 0;
}

// =====================================================================

function nfw_matching( $where, $key, $nfw_rules, $rules, $subid, $id, $RAW_POST = null, $nfw_options ) {

	global $nfw_;

	if ( isset( $RAW_POST ) ) {
		$val = $RAW_POST;
	} else {
		$val = $GLOBALS['_'.$where][$key];
	}

	if ( is_array($val) ) {
		if ( isset( $nfw_['flattened'][$where][$key] ) ) {
			$val = $nfw_['flattened'][$where][$key];
		} else {
			$val = nfw_flatten( ' ', $val );
			$nfw_['flattened'][$where][$key] = $val;
		}
	}

	if ( $where == 'POST' && ! empty($nfw_options['post_b64']) && ! isset($nfw_['b64'][$where][$key]) && $val ) {
		nfw_check_b64($key, $val);
		$nfw_['b64'][$where][$key] = 1;
	}

	if ( isset( $rules['cha'][$subid]['exe'] ) ) {
		$val = @$rules['cha'][$subid]['exe']($val);
	}

	$t = '';

	if ( isset( $rules['cha'][$subid]['nor'] ) ) {
		$t .= 'N';
		if ( isset( $nfw_[$t][$where][$key] ) && ! isset( $rules['cha'][$subid]['exe'] ) ) {
			$val = $nfw_[$t][$where][$key];
		} else {
			$val = nfw_normalize( $val, $nfw_rules );
			if (! isset( $rules['cha'][$subid]['exe']) ) {
				$nfw_[$t][$where][$key] = $val;
			}
		}
	}

	if ( isset( $rules['cha'][$subid]['tra'] ) ) {
		$t .= 'T' . $rules['cha'][$subid]['tra'];
		if ( isset( $nfw_[$t][$where][$key] )  && ! isset( $rules['cha'][$subid]['exe'] ) ) {
			$val = $nfw_[$t][$where][$key];
		} else {
			$val = nfw_transform_string( $val, $rules['cha'][$subid]['tra'] );
			if (! isset( $rules['cha'][$subid]['exe']) ) {
				$nfw_[$t][$where][$key] = $val;
			}
		}
	}
	if ( empty( $rules['cha'][$subid]['noc']) ) {
		$t .= 'C';
		if ( isset( $nfw_[$t][$where][$key] ) && ! isset( $rules['cha'][$subid]['exe'] ) ) {
			$val = $nfw_[$t][$where][$key];
		} else {
			$val = nfw_compress_string( $val );
			if (! isset( $rules['cha'][$subid]['exe']) ) {
				$nfw_[$t][$where][$key] = $val;
			}
		}
	}

	if ( nfw_operator( $val, $rules['cha'][$subid]['wha'], $rules['cha'][$subid]['ope']	) ) {
		if ( isset( $rules['cha'][$subid+1]) ) {
			return 1;
		} else {
			if ( isset( $nfw_['flattened'][$where][$key] ) ) {
				nfw_log($rules['why'], $where .':' . $key . ' = ' . $nfw_['flattened'][$where][$key], $rules['lev'], $id);
			} elseif ( isset( $RAW_POST ) ) {
				nfw_log($rules['why'], $where .':' . $key . ' = ' . $RAW_POST, $rules['lev'], $id);
			} else {
				nfw_log($rules['why'], $where .':' . $key . ' = ' . $GLOBALS['_'.$where][$key], $rules['lev'], $id);
			}
			nfw_block($rules['lev']);
		}
	}
	return 0;
}

// =====================================================================

function nfw_operator( $val, $what, $op ) {

	if ( $op == 2 ) {
		if ( $val != $what ) {
			return true;
		}
	} elseif ( $op == 3 ) {
		if ( strpos($val, $what) !== FALSE ) {
			return true;
		}
	} elseif ( $op == 4 ) {
		if ( stripos($val, $what) !== FALSE ) {
			return true;
		}
	} elseif ( $op == 5 ) {
		if ( preg_match("`$what`", $val ) ) {
			return true;
		}
	} elseif ( $op == 6 ) {
		if (! preg_match("`$what`", $val) ) {
			return true;
		}
	} elseif ( $op == 7 ) {
		return true;

	} else {
		if ( $val == $what ) {
			return true;
		}
	}
}

// =====================================================================

function nfw_normalize( $string, $nfw_rules ) {

	if ( empty( $string ) ) {
		return;
	}

	$norm = rawurldecode( $string );
	if (! $norm ) {
		return $string;
	}

	if ( preg_match('/&(?:#x0*[0-9a-f]{2}|#0*[12]?[0-9]{2}|amp|[lg]t|nbsp|quot)(?!;|\d)/i', $norm) ) {
		$norm = preg_replace('/&(#x0*[0-9a-f]{2}|#0*[12]?[0-9]{2}|amp|[lg]t|nbsp|quot)(?!;|\d)/i', '&\1;', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	if ( preg_match('/\\\x[a-f0-9]{2}/i', $norm) ) {
		$norm = preg_replace_callback('/\\\x([a-f0-9]{2})/i', 'nfw_hex2ascii', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	$norm = nfw_html_decode( $norm );
	if (! $norm ) {
		return $string;
	}

	if ( preg_match('/&#x?[0-9a-f]+;/i', $norm) ) {
		$norm = preg_replace('/(&#x?[0-9a-f]+;)/i', '', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	if ( preg_match( '/(?:%|\\\)u[0-9a-f]{4}/i', $norm ) ) {
		$norm = preg_replace_callback('/(?:%|\\\)(u[0-9a-f]{4})/i', 'nfw_udecode', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	if ( empty( $nfw_rules[2]['ena'] ) ) {
		$norm = preg_replace('/\x0|%00/', '', $norm);
		if (! $norm ) {
			return $string;
		}
	}

	return $norm;
}

// =====================================================================

function nfw_html_decode( $norm ) {

	global $nfw_;

	$nfw_['entity_in'] = array (
		'&Tab;','&NewLine;','&excl;','&quot;','&QUOT;','&num;','&dollar;',
		'&percnt;','&amp;','&AMP;','&apos;','&lpar;','&rpar;','&ast;',
		'&midast;','&plus;','&comma;','&period;','&sol;','&colon;','&semi;',
		'&lt;','&LT;','&equals;','&gt;','&GT;','&quest;','&commat;','&lsqb;',
		'&lbrack;','&bsol;','&rsqb;','&rbrack;','&Hat;','&lowbar;','&grave;',
		'&DiacriticalGrave;','&lcub;','&lbrace;','&verbar;','&vert;','&VerticalLine;',
		'&rcub;','&rbrace;','&nbsp;','&NonBreakingSpace;','&nvlt;','&nvgt;',"\xa0",
	);

	$nfw_['entity_out'] = array (
		'','','!','"','"','#','$','%','&','&',"'",'(',')','*','*','+',',','.','/',
		':',';','<','<','=','>','>','?','@','[','[','\\',']',']','^','_','`','`',
		'{','{','|','|','|','}','}',' ',' ','','',' '
	);

	$normout = str_replace( $nfw_['entity_in'], $nfw_['entity_out'], $norm);
	$normout = html_entity_decode( $normout, ENT_QUOTES, 'UTF-8' );

	return $normout;

}

// =====================================================================

function nfw_compress_string( $string, $where = null ) {

	if ( $where == 1 ) {
		$replace = ' ';
	} else {
		$replace = '';
	}

	$string = str_replace( array( "\x09", "\x0a","\x0b", "\x0c", "\x0d"),
				$replace, $string);
	$string = trim ( preg_replace('/\x20{2,}/', ' ', $string) );
	return $string;

}

// =====================================================================

function nfw_transform_string( $string, $where ) {

	if ( $where == 1 ) {
		// Heavily modified version of JsShrink (http://vrana.github.io/JsShrink/)
		$norm = trim( preg_replace_callback('((^([^a-z/&|#]*)|([\'"])(?:\\\\.|[^\n\3\\\\])*?\3|(?:[0-9a-z_$]+)|.)'.
			'(?:\s|--[^\n]*+\n|/\*(?:[^*!]|\*(?!/))*+\*/)*'.
			'(?:(?:\#|--(?:[\x00-\x20\x7f]|$)|/\*$)[^\n]*+\n|/\*!(?:\d{5})?|\*/|/\*(?:[^*!]|\*(?!/))*+\*/)*)si',
			'nfw_delcomments1',  $string . "\n") );
		$norm = preg_replace('/[\'"]\x20*\+?\x20*[\'"]/', '', $norm);
		$norm = strtolower( str_replace(	array('+', "'", '"', "(", ')', '`', ',', ';'), ' ', $norm) );

	} elseif ( $where == 2 ) {
		$norm = trim( preg_replace_callback('((^|([\'"])(?:\\\\.|[^\n\2\\\\])*?\2|(?:[0-9a-z_$]+)|.)'.
			'(?://[^\n]*+\n|/\*(?:[^*]|\*(?!/))*+\*/)*)si',
			'nfw_delcomments2',  $string . "\n") );
		$norm = preg_replace( array('/[\n\r\t\f\v]/', '`/\*\s*\*/`', '/[\'"`]\x20*[+.]?\x20*[\'"`]/'),
				array('', ' ', ''), $norm);
	} elseif ( $where == 3 ) {
		$norm = preg_replace( array('`/(\./)+`','`/{2,}`', '`/(.+?)/\.\./\1\b`'), array('/', '/', '/\1'), $string );
	}

	return $norm;

}

// =====================================================================

function nfw_delcomments1 ( $match ) {

	if (! empty($match[2]) ) { return ' '; }
	if ( $match[0] != $match[1] ) {
		return $match[1]. ' ';
	}
	return $match[1];

}

function nfw_delcomments2 ( $match ) {

	if ( $match[0] != $match[1] ) {
		return $match[1]. ' ';
	}
	return $match[1];

}

// =====================================================================

function nfw_udecode( $match ) {

	return @json_decode('"\\'.$match[1].'"');

}

// =====================================================================

function nfw_hex2ascii( $match ) {

	return chr( '0x'.$match[1] );

}

// =====================================================================

function nfw_flatten( $glue, $pieces ) {

	if ( defined('NFW_STATUS') ) { return; }

	$ret = array();

   foreach ($pieces as $r_pieces) {
      if ( is_array($r_pieces)) {
         $ret[] = nfw_flatten($glue, $r_pieces);
      } else {
			if (! empty($r_pieces) ) {
				$ret[] = $r_pieces;
			}
      }
   }
   return implode($glue, $ret);
}

// =====================================================================

function nfw_check_b64( $key, $string ) {

	if ( defined('NFW_STATUS') || strlen($string) < 4 ) { return; }

	$decoded = base64_decode($string);
	if ( strlen($decoded) < 4 ) { return; }

	if ( preg_match( '`\b(?:\$?_(COOKIE|ENV|FILES|(?:GE|POS|REQUES)T|SE(RVER|SSION))|HTTP_(?:(?:POST|GET)_VARS|RAW_POST_DATA)|GLOBALS)\s*[=\[)]|\b(?i:array_map|assert|base64_(?:de|en)code|chmod|curl_exec|(?:ex|im)plode|error_reporting|eval|file(?:_get_contents)?|f(?:open|write|close)|fsockopen|function_exists|gzinflate|md5|move_uploaded_file|ob_start|passthru|preg_replace|phpinfo|stripslashes|strrev|(?:shell_)?exec|substr|system|unlink)\s*\(|\becho\s*[\'"]|<(?i:a[\s/]|applet|div|embed|i?frame(?:set)?|img|meta|marquee|object|script|textarea)\b|\W\$\{\s*[\'"]\w+[\'"]|<\?(?i:php|=)|(?i:(?:\b|\d)select\b.+?from\b.+?(?:\b|\d)where|(?:\b|\d)insert\b.+?into\b|(?:\b|\d)union\b.+?(?:\b|\d)select\b|(?:\b|\d)update\b.+?(?:\b|\d)set\b)`', $decoded) ) {
		nfw_log('BASE64-encoded injection', 'POST:' . $key . ' = ' . $string, '3', 0);
		nfw_block(3);
	}
}

// =====================================================================

function nfw_sanitise( $str, $msg ) {

	if ( defined('NFW_STATUS') ) { return; }

	if (! isset($str) ) { return null; }

	global $nfw_;

	if (is_string($str) ) {
		if ($msg == 'COOKIE') {
			$str2 = str_replace(	array('\\', "'", "\x00", "\x1a", '`', '<'),
				array('\\\\', "\\'", '-', '-', '\\`', '&lt;'),	$str);
		} elseif (! empty($nfw_['dblink']) ) {
			$str2 = $nfw_['dblink']->real_escape_string($str);
			$str2 = str_replace(	array(  '`', '<', '>'), array( '\\`', '&lt;', '&gt;'),	$str2);
		} else {
			$str2 = str_replace(	array('\\', "'", '"', "\x0d", "\x0a", "\x00", "\x1a", '`', '<', '>'),
				array('\\\\', "\\'", '\\"', '-', '-', '-', '-', '\\`', '&lt;', '&gt;'),	$str);
		}
		if (! empty($nfw_['nfw_options']['debug']) ) {
			if ($str2 != $str) {
				nfw_log('Sanitising user input', $msg . ': ' . $str, 7, 0);
			}
			return $str;
		}
		if ($str2 != $str) {
			nfw_log('Sanitising user input', $msg . ': ' . $str, 6, 0);
		}
		return $str2;

	} else if (is_array($str) ) {
		foreach($str as $key => $value) {
			if ($msg == 'COOKIE') {
				$key2 = str_replace(	array('\\', "'", "\x00", "\x1a", '`', '<', '>'),
					array('\\\\', "\\'", '-', '-', '\\`', '&lt;', '&gt;'),	$key, $occ);
			} else {
				$key2 = str_replace(	array('\\', "'", '"', "\x0d", "\x0a", "\x00", "\x1a", '`', '<', '>'),
					array('\\\\', "\\'", '\\"', '-', '-', '-', '-', '&#96;', '&lt;', '&gt;'),	$key, $occ);
			}
			if ($occ) {
				unset($str[$key]);
				nfw_log('Sanitising user input', $msg . ': ' . $key, 6, 0);
			}
			$str[$key2] = nfw_sanitise($value, $msg);
		}
		return $str;
	}
}

// =====================================================================

function nfw_response_headers() {

	if (! defined('NFW_RESHEADERS') ) { return; }
	$NFW_RESHEADERS = NFW_RESHEADERS;

	$rewrite = array();

	if (! empty( $NFW_RESHEADERS[0] ) ) {
		foreach (headers_list() as $header) {
			if (strpos($header, 'Set-Cookie:') === false) { continue; }
			if (stripos($header, '; httponly') !== false) {
				$rewrite[] = $header;
				continue;
			}
			$rewrite[] = $header . '; httponly';
		}
		if (! empty($rewrite) ) {
			header_remove('Set-Cookie');
			foreach($rewrite as $cookie) {
				header($cookie, false);
			}
		}
	}

	if (! empty( $NFW_RESHEADERS[1] ) ) {
		header('X-Content-Type-Options: nosniff');
	}

	if (! empty( $NFW_RESHEADERS[2] ) ) {
		if ($NFW_RESHEADERS[2] == 1) {
			header('X-Frame-Options: SAMEORIGIN');
		} else {
			header('X-Frame-Options: DENY');
		}
	}

	if (! empty( $NFW_RESHEADERS[3] ) ) {
		header('X-XSS-Protection: 1; mode=block');
	}

	if (! empty( $NFW_RESHEADERS[6] ) ) {
		header('Content-Security-Policy: ' . CSP_FRONTEND_DATA);
	}

	if ( empty($NFW_RESHEADERS[4] ) ) { return; }

	if ( $_SERVER['SERVER_PORT'] != 443 &&
	(! isset( $_SERVER['HTTP_X_FORWARDED_PROTO']) ||
	$_SERVER['HTTP_X_FORWARDED_PROTO'] != 'https') ) {
		return;
	}
	if ($NFW_RESHEADERS[4] == 1) {
		$max_age = 'max-age=2628000';
	} elseif ($NFW_RESHEADERS[4] == 2) {
		$max_age = 'max-age=15768000';
	} elseif ($NFW_RESHEADERS[4] == 3) {
		$max_age = 'max-age=31536000';
	} elseif ($NFW_RESHEADERS[4] == 4) {
		$max_age = 'max-age=0';
	}
	if (! empty( $NFW_RESHEADERS[5] ) ) {
		$max_age .= ' ; includeSubDomains';
	}
	header('Strict-Transport-Security: '. $max_age);
}

// =====================================================================
// EOF
