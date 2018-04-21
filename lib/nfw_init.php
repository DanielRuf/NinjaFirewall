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

if ( version_compare($nfw_options['engine_version'], NFW_ENGINE_VERSION, '<') ) {
	// v2.0.3 update ----------------------------------------------------
	if (empty($nfw_options['response_headers']) ) {
		if ( function_exists('header_register_callback') && function_exists('headers_list') && function_exists('header_remove') ) {
			// We enable X-XSS-Protection and X-Content-Type-Options flag:
			$nfw_options['response_headers'] = '01010000';
		}
	}
	// v2.0.5 update ----------------------------------------------------
	if ( version_compare( $nfw_options['engine_version'], '2.0.5', '<' ) ) {
		$nfw_options['admin_wl'] = 0;
		$nfw_options['admin_wl_session'] = 0;
		$nfw_options['fg_exclude'] = '';
	}
	// v3.1 update (file guard) -----------------------------------------
	if ( version_compare( $nfw_options['engine_version'], '3.1', '<' ) ) {
		// Convert the current value for regex use:
		if (! empty($nfw_options['fg_exclude']) ) {
			$nfw_options['fg_exclude'] = preg_quote( $nfw_options['fg_exclude'], '`');
		}
	}
	// v3.2 update ------------------------------------------------------
	if ( version_compare( $nfw_options['engine_version'], '3.2', '<' ) ) {
		if ( function_exists('header_register_callback') && function_exists('headers_list') && function_exists('header_remove') ) {
			if (! empty( $nfw_options['response_headers'] ) && strlen( $nfw_options['response_headers'] ) == 6 ) {
				$nfw_options['response_headers'] .= '00';
			}
		}
	}
	// v3.3 update ------------------------------------------------------
	if ( version_compare( $nfw_options['engine_version'], '3.3', '<' ) ) {
		// Enable check updates:
		$nfw_options['login_updates'] = 1;
		// List of files to delete in the ./static folder:
		$static_files = array( 'bar-critical.png', 'bar-high.png', 'bar-medium.png', 'bullet_off.gif', 'bullet_on.gif', 'icon_error.png', 'icon_help.png', 'icon_ok.png', 'icon_warn.png', 'login.png', 'logo.png', 'logopro_60.png', 'logout.png', 'opensans.woff', 'p_icon_nf.png', 'p_icon_nm.png', 'p_icon_nr.png', 'twitter_ntn.png' );
		foreach ( $static_files as $file ) {
			unlink( dirname( __DIR__ ) ."/static/{$file}" );
		}
		// Recursively delete all files from the './lib/lang/' folder:
		if ( is_dir( dirname( __DIR__ ) .'/lib/lang' ) ) {
			recursively_delete( dirname( __DIR__ ) .'/lib/lang' );
		}
		// Locale conversion:
		if ( $nfw_options['admin_lang'] == 'en' ) {
			$nfw_options['admin_lang'] = 'en_US';
		} elseif ( $nfw_options['admin_lang'] == 'fr' ) {
			$nfw_options['admin_lang'] = 'fr_FR';
		}
	}

	// ------------------------------------------------------------------

	// Adjust engine version :
	$nfw_options['engine_version'] = NFW_ENGINE_VERSION;

	$update_options = 1;
}


if ( version_compare($nfw_options['rules_version'], NFW_RULES_VERSION, '<') ) {
	// Get the new set of rules :
	require('./conf/.rules.php');
	$nfw_rules_new = unserialize($nfw_rules_new);

	foreach ( $nfw_rules_new as $new_key => $new_value ) {
		foreach ( $new_value as $key => $value ) {
			// if that rule exists already, we keep its 'ena' flag value
			// as it may have been changed by the user with the rules editor :
			// v3.x:
			if ( ( isset( $nfw_rules[$new_key]['ena'] ) ) && ( $key == 'ena' ) ) {
				$nfw_rules_new[$new_key]['ena'] = $nfw_rules[$new_key]['ena'];
			}
			// v2.x:
			if ( ( isset( $nfw_rules[$new_key]['on'] ) ) && ( $key == 'ena' ) ) {
				$nfw_rules_new[$new_key]['ena'] = $nfw_rules[$new_key]['on'];
			}
		}
	}
	// v2.x:
	if ( isset( $nfw_rules[NFW_DOC_ROOT]['what'] ) ) {
		$nfw_rules_new[NFW_DOC_ROOT]['cha'][1]['wha']= str_replace( '/', '/[./]*', $nfw_rules[NFW_DOC_ROOT]['what'] );
		$nfw_rules_new[NFW_DOC_ROOT]['ena']	= $nfw_rules[NFW_DOC_ROOT]['on'];
	// v3.x:
	} else {
		$nfw_rules_new[NFW_DOC_ROOT]['cha'][1]['wha']= $nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'];
		$nfw_rules_new[NFW_DOC_ROOT]['ena']	= $nfw_rules[NFW_DOC_ROOT]['ena'];
	}

	// v2.0.1 / 20140927 update -----------------------------------------
	// We delete rules #151 and #152
	if ( version_compare( $nfw_options['rules_version'], '20140927', '<' ) ) {
		if ( isset($nfw_rules_new[151]) ) {
			unset($nfw_rules_new[151]);
		}
		if ( isset($nfw_rules_new[152]) ) {
			unset($nfw_rules_new[152]);
		}
	}
	// ------------------------------------------------------------------

	// Save rules :
	$res = save_config( $nfw_rules_new, 'rules' );
	if (! empty( $res ) ) {
		echo $res;
	}

	// Adjust rules version :
	$nfw_options['rules_version'] = NFW_RULES_VERSION;

	$update_options = 1;
}
// Updates options if needed :
if (! empty($update_options) ) {
	// Save options :
	$res = save_config( $nfw_options, 'options' );
	if (! empty( $res ) ) {
		echo $res;
	}
}

// Do some housework if needed :
nfw_housework();

// ---------------------------------------------------------------------

function nfw_housework() {

	global $nfw_options;

	$cache = './nfwlog/cache/';

	// Flush temporarily blocked IPs :
	if (! empty($nfw_options['ban_ip']) ) {
		if (file_exists( $cache . 'ip_bk_flushed.php' ) ) {
			$stat = stat($cache . 'ip_bk_flushed.php');
			// Flush it if older than 1 hour :
			if ( time() - $stat['mtime'] > 3600 ) {
				$fh = fopen($cache . 'ip_bk_flushed.php' , 'w');
				fclose($fh);
				$glob = glob($cache ."ipbk*.php");
				if (is_array($glob) ) {
					foreach($glob as $file) {
						$stat = stat($file);
						if ( time() - $stat['mtime'] > $nfw_options['ban_time'] * 60 ) {
							unlink($file);
						}
					}
				}
			}
		} else {
			$fh = fopen($cache . 'ip_bk_flushed.php' , 'w');
			fclose($fh);
		}
	}
}

// ---------------------------------------------------------------------

function recursively_delete( $dir ) {

	if ( is_dir( $dir ) ) {
		$files = scandir( $dir );
		foreach ( $files as $file ) {
			if ( $file == '.' || $file == '..' ) { continue; }
			if ( is_dir( "$dir/$file" ) ) {
				recursively_delete( "$dir/$file" );
			} else {
				unlink( "$dir/$file" );
			}
		}
		rmdir( $dir );
	}
}
// ---------------------------------------------------------------------

// EOF
