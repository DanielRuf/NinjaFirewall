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

if (! isset( $nfw_['nfw_options']['enabled']) ) {
	header('HTTP/1.1 404 Not Found');
	header('Status: 404 Not Found');
	exit;
}

// ---------------------------------------------------------------------
function fw_centlog( $log_dir ) {

	global $nfw_;

	$pubkey = explode( ':', $nfw_['nfw_options']['clogs_pubkey'], 2 );

	// IP restriction ?
	if ( isset( $pubkey[1]) &&  $pubkey[1] != '*' ) {
		// Fetch user IP and compare it with the allowed one:
		nfw_check_ip();

		if ( NFW_REMOTE_ADDR != $pubkey[1] ) {
			nfw_log('Centralized logging: IP not allowed', NFW_REMOTE_ADDR, 6, 0);
			fw_centlog_die();
		}
	}

	// Check the hash key:
	if ( empty( $pubkey[0] ) || sha1( $_POST['clogs_req'] ) !== $pubkey[0] ) {
		nfw_log('Centralized logging: public key rejected', NFW_REMOTE_ADDR, 6, 0);
		fw_centlog_die();
	}

	// Find the log and return its content :
	if ( empty( $nfw_['nfw_options']['tzset'] ) ) {
		date_default_timezone_set( $nfw_['nfw_options']['timezone'] );
	}
	$cur_month = date('Y-m');
	$log_file = $log_dir .'/firewall_' . $cur_month . '.php';

	// No log:
	if (! file_exists( $log_file ) ) {
		exit('1:');
	}

	// Error while reading the log?
	$data = file( $log_file, FILE_SKIP_EMPTY_LINES );
	if ( $data === false ) {
		exit('2:');
	}

	// Return the log content:
	echo '0:~*~:' . base64_encode( json_encode( $data ) );
	exit;
}

// ---------------------------------------------------------------------

function fw_centlog_die() {

	header('HTTP/1.1 406 Not Acceptable');
	header('Status: 406 Not Acceptable');

}

// ---------------------------------------------------------------------
// EOF
