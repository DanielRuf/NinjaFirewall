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

// Print error during the installation process:
@error_reporting( E_ALL );
@ini_set( 'display_errors',1 );

define('NFW_INSTALLER', true);

// Start session:
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

// Don't cache anything:
header('Pragma: no-cache');
header('Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate, proxy-revalidate');
header('Expires: Mon, 01 Sep 2014 01:01:01 GMT');
$nfw_options = array();
// Load translation files:
if (! empty( $_POST['admin_lang'] ) ) {
	$nfw_options['admin_lang'] = $_POST['admin_lang'];
} elseif (! empty( $_SESSION['admin_lang'] ) ) {
	$nfw_options['admin_lang'] = $_SESSION['admin_lang'];
}
require_once __DIR__ .'/lib/locale.php';

if ( file_exists( __DIR__ .'/conf/options.php' ) && empty( $_SESSION['nfw_install'] ) ) {
	install_error(
		sprintf(
			_('You already have a %s file. If you want to re-install NinjaFirewall, delete it first.'),
			'"conf/options.php"'
		)
	);
	exit;
}
if (! file_exists(__DIR__ .'/conf/.rules.php') ) {
	install_error(
		sprintf(
			_('Missing %s file. Please re-install NinjaFirewall.'),
			'"conf/.rules.php"'
		)
	);
	exit;
}

// Set the installation flag :
$_SESSION['nfw_install'] = 1;

require __DIR__ .'/conf/.rules.php';
$nfw_rules = unserialize( $nfw_rules_new );
// Required constants :
require __DIR__ .'/lib/constants.php';
if ( NFW_EDN == 2 ) {
	$nfedn = 'Pro+ Edition';
} else {
	$nfedn = 'Pro Edition';
}
require_once __DIR__ .'/lib/misc.php';


if ( empty( $_REQUEST['nfw_act'] ) || ! preg_match( '/^\d+$/', $_REQUEST['nfw_act'] ) ) {
	$_REQUEST['nfw_act'] = 1;
}

// PHP INFO ?
if ( $_REQUEST['nfw_act'] == 99 ) {
	phpinfo( 33 );
	exit;
}
// Firewall test ?
if ( $_REQUEST['nfw_act'] == 98 ) {
	nfw_activation_test();
	exit;
}

if ( $_REQUEST['nfw_act'] == 2 ) {
	if ( empty($_POST['timezone']) ) {
		$_SESSION['timezone'] = 'UTC';
	} else {
		$_SESSION['timezone'] = $_POST['timezone'];
	}
	if ( empty($_POST['admin_lang']) ) {
		$_SESSION['admin_lang'] = 'en_US';
	} else {
		$_SESSION['admin_lang'] = $_POST['admin_lang'];
	}
	$mid = 20;
	nfw_system_requirements();

} else if ( $_REQUEST['nfw_act'] == 3 ) {
	$mid = 30;
	nfw_changelog();

} else if ( $_REQUEST['nfw_act'] == 4 ) {
	$mid = 40;
	nfw_license();

} else if ( $_REQUEST['nfw_act'] == 5 ) {
	$mid = 50;
	// Firewall test failure:
	if (!empty($_REQUEST['nfw_test']) ) {
		$mid = 60;
		nfw_integration(0);
		exit;
	}
	if (empty($_POST['save']) ) {
		nfw_admin_setup(0);
	} else {
		nfw_admin_setup_save();
		$mid = 60;
		nfw_integration(0);
	}

} else if ( $_REQUEST['nfw_act'] == 6 ) {
	$mid = 70;
	nfw_integration_save();
	nfw_activation();

} else {
	// Installer intro:
	$mid = 10;
	nfw_regional_settings();
}

exit;

// =====================================================================

function nfw_regional_settings() {

	// Fetch all language files available:
	$i18n_list = array( 'en_US' ); // Default
	$locale_dir = __DIR__ .'/locale';
	$i18n_dirs = glob( "{$locale_dir}/*", GLOB_ONLYDIR );
	foreach ( $i18n_dirs as $i18n_dir ) {
		if ( preg_match( '`/([a-z]{2}_[A-Z]{2})$`', $i18n_dir, $match ) ) {
			if ( file_exists( "{$locale_dir}/{$match[1]}/LC_MESSAGES/ninjafirewall_pro-{$match[1]}.mo" ) ) {
					$i18n_list[] = $match[1];
			}
		}
	}
	sort( $i18n_list );

	// Make sure the Gettext extension is installed, otherwise we warn
	// and disable the possibility to change the language:
	$disabled = '';
	$warn_msg = '';
	if (! function_exists( 'gettext' ) ) {
		$warn_msg = '<br />'. glyphicon('warning') .' '.  _('The PHP Gettext extension is missing on your server. Please install it if you want to change languages.');
		$disabled = ' disabled';
	}

	if ( empty( $_SESSION['admin_lang'] ) ) {
		// Load en_US language file by default :
		$tmp_lang = 'en_US';
	} else {
		$tmp_lang = $_SESSION['admin_lang'];
	}

	install_header();

	$zonelist = array('UTC', 'Africa/Abidjan', 'Africa/Accra', 'Africa/Addis_Ababa', 'Africa/Algiers', 'Africa/Asmara', 'Africa/Asmera', 'Africa/Bamako', 'Africa/Bangui', 'Africa/Banjul', 'Africa/Bissau', 'Africa/Blantyre', 'Africa/Brazzaville', 'Africa/Bujumbura', 'Africa/Cairo', 'Africa/Casablanca', 'Africa/Ceuta', 'Africa/Conakry', 'Africa/Dakar', 'Africa/Dar_es_Salaam', 'Africa/Djibouti', 'Africa/Douala', 'Africa/El_Aaiun', 'Africa/Freetown', 'Africa/Gaborone', 'Africa/Harare', 'Africa/Johannesburg', 'Africa/Kampala', 'Africa/Khartoum', 'Africa/Kigali', 'Africa/Kinshasa', 'Africa/Lagos', 'Africa/Libreville', 'Africa/Lome', 'Africa/Luanda', 'Africa/Lubumbashi', 'Africa/Lusaka', 'Africa/Malabo', 'Africa/Maputo', 'Africa/Maseru', 'Africa/Mbabane', 'Africa/Mogadishu', 'Africa/Monrovia', 'Africa/Nairobi', 'Africa/Ndjamena', 'Africa/Niamey', 'Africa/Nouakchott', 'Africa/Ouagadougou', 'Africa/Porto-Novo', 'Africa/Sao_Tome', 'Africa/Timbuktu', 'Africa/Tripoli', 'Africa/Tunis', 'Africa/Windhoek', 'America/Adak', 'America/Anchorage', 'America/Anguilla', 'America/Antigua', 'America/Araguaina', 'America/Argentina/Buenos_Aires', 'America/Argentina/Catamarca', 'America/Argentina/ComodRivadavia', 'America/Argentina/Cordoba', 'America/Argentina/Jujuy', 'America/Argentina/La_Rioja', 'America/Argentina/Mendoza', 'America/Argentina/Rio_Gallegos', 'America/Argentina/Salta', 'America/Argentina/San_Juan', 'America/Argentina/San_Luis', 'America/Argentina/Tucuman', 'America/Argentina/Ushuaia', 'America/Aruba', 'America/Asuncion', 'America/Atikokan', 'America/Atka', 'America/Bahia', 'America/Barbados', 'America/Belem', 'America/Belize', 'America/Blanc-Sablon', 'America/Boa_Vista', 'America/Bogota', 'America/Boise', 'America/Buenos_Aires', 'America/Cambridge_Bay', 'America/Campo_Grande', 'America/Cancun', 'America/Caracas', 'America/Catamarca', 'America/Cayenne', 'America/Cayman', 'America/Chicago', 'America/Chihuahua', 'America/Coral_Harbour', 'America/Cordoba', 'America/Costa_Rica', 'America/Cuiaba', 'America/Curacao', 'America/Danmarkshavn', 'America/Dawson', 'America/Dawson_Creek', 'America/Denver', 'America/Detroit', 'America/Dominica', 'America/Edmonton', 'America/Eirunepe', 'America/El_Salvador', 'America/Ensenada', 'America/Fort_Wayne', 'America/Fortaleza', 'America/Glace_Bay', 'America/Godthab', 'America/Goose_Bay', 'America/Grand_Turk', 'America/Grenada', 'America/Guadeloupe', 'America/Guatemala', 'America/Guayaquil', 'America/Guyana', 'America/Halifax', 'America/Havana', 'America/Hermosillo', 'America/Indiana/Indianapolis', 'America/Indiana/Knox', 'America/Indiana/Marengo', 'America/Indiana/Petersburg', 'America/Indiana/Tell_City', 'America/Indiana/Vevay', 'America/Indiana/Vincennes', 'America/Indiana/Winamac', 'America/Indianapolis', 'America/Inuvik', 'America/Iqaluit', 'America/Jamaica', 'America/Jujuy', 'America/Juneau', 'America/Kentucky/Louisville', 'America/Kentucky/Monticello', 'America/Knox_IN', 'America/La_Paz', 'America/Lima', 'America/Los_Angeles', 'America/Louisville', 'America/Maceio', 'America/Managua', 'America/Manaus', 'America/Marigot', 'America/Martinique', 'America/Matamoros', 'America/Mazatlan', 'America/Mendoza', 'America/Menominee', 'America/Merida', 'America/Mexico_City', 'America/Miquelon', 'America/Moncton', 'America/Monterrey', 'America/Montevideo', 'America/Montreal', 'America/Montserrat', 'America/Nassau', 'America/New_York', 'America/Nipigon', 'America/Nome', 'America/Noronha', 'America/North_Dakota/Center', 'America/North_Dakota/New_Salem', 'America/Ojinaga', 'America/Panama', 'America/Pangnirtung', 'America/Paramaribo', 'America/Phoenix', 'America/Port-au-Prince', 'America/Port_of_Spain', 'America/Porto_Acre', 'America/Porto_Velho', 'America/Puerto_Rico', 'America/Rainy_River', 'America/Rankin_Inlet', 'America/Recife', 'America/Regina', 'America/Resolute', 'America/Rio_Branco', 'America/Rosario', 'America/Santa_Isabel', 'America/Santarem', 'America/Santiago', 'America/Santo_Domingo', 'America/Sao_Paulo', 'America/Scoresbysund', 'America/Shiprock', 'America/St_Barthelemy', 'America/St_Johns', 'America/St_Kitts', 'America/St_Lucia', 'America/St_Thomas', 'America/St_Vincent', 'America/Swift_Current', 'America/Tegucigalpa', 'America/Thule', 'America/Thunder_Bay', 'America/Tijuana', 'America/Toronto', 'America/Tortola', 'America/Vancouver', 'America/Virgin', 'America/Whitehorse', 'America/Winnipeg', 'America/Yakutat', 'America/Yellowknife', 'Arctic/Longyearbyen', 'Asia/Aden', 'Asia/Almaty', 'Asia/Amman', 'Asia/Anadyr', 'Asia/Aqtau', 'Asia/Aqtobe', 'Asia/Ashgabat', 'Asia/Ashkhabad', 'Asia/Baghdad', 'Asia/Bahrain', 'Asia/Baku', 'Asia/Bangkok', 'Asia/Beirut', 'Asia/Bishkek', 'Asia/Brunei', 'Asia/Calcutta', 'Asia/Choibalsan', 'Asia/Chongqing', 'Asia/Chungking', 'Asia/Colombo', 'Asia/Dacca', 'Asia/Damascus', 'Asia/Dhaka', 'Asia/Dili', 'Asia/Dubai', 'Asia/Dushanbe', 'Asia/Gaza', 'Asia/Harbin', 'Asia/Ho_Chi_Minh', 'Asia/Hong_Kong', 'Asia/Hovd', 'Asia/Irkutsk', 'Asia/Istanbul', 'Asia/Jakarta', 'Asia/Jayapura', 'Asia/Jerusalem', 'Asia/Kabul', 'Asia/Kamchatka', 'Asia/Karachi', 'Asia/Kashgar', 'Asia/Kathmandu', 'Asia/Katmandu', 'Asia/Kolkata', 'Asia/Krasnoyarsk', 'Asia/Kuala_Lumpur', 'Asia/Kuching', 'Asia/Kuwait', 'Asia/Macao', 'Asia/Macau', 'Asia/Magadan', 'Asia/Makassar', 'Asia/Manila', 'Asia/Muscat', 'Asia/Nicosia', 'Asia/Novokuznetsk', 'Asia/Novosibirsk', 'Asia/Omsk', 'Asia/Oral', 'Asia/Phnom_Penh', 'Asia/Pontianak', 'Asia/Pyongyang', 'Asia/Qatar', 'Asia/Qyzylorda', 'Asia/Rangoon', 'Asia/Riyadh', 'Asia/Saigon', 'Asia/Sakhalin', 'Asia/Samarkand', 'Asia/Seoul', 'Asia/Shanghai', 'Asia/Singapore', 'Asia/Taipei', 'Asia/Tashkent', 'Asia/Tbilisi', 'Asia/Tehran', 'Asia/Tel_Aviv', 'Asia/Thimbu', 'Asia/Thimphu', 'Asia/Tokyo', 'Asia/Ujung_Pandang', 'Asia/Ulaanbaatar', 'Asia/Ulan_Bator', 'Asia/Urumqi', 'Asia/Vientiane', 'Asia/Vladivostok', 'Asia/Yakutsk', 'Asia/Yekaterinburg', 'Asia/Yerevan', 'Atlantic/Azores', 'Atlantic/Bermuda', 'Atlantic/Canary', 'Atlantic/Cape_Verde', 'Atlantic/Faeroe', 'Atlantic/Faroe', 'Atlantic/Jan_Mayen', 'Atlantic/Madeira', 'Atlantic/Reykjavik', 'Atlantic/South_Georgia', 'Atlantic/St_Helena', 'Atlantic/Stanley', 'Australia/ACT', 'Australia/Adelaide', 'Australia/Brisbane', 'Australia/Broken_Hill', 'Australia/Canberra', 'Australia/Currie', 'Australia/Darwin', 'Australia/Eucla', 'Australia/Hobart', 'Australia/LHI', 'Australia/Lindeman', 'Australia/Lord_Howe', 'Australia/Melbourne', 'Australia/NSW', 'Australia/North', 'Australia/Perth', 'Australia/Queensland', 'Australia/South', 'Australia/Sydney', 'Australia/Tasmania', 'Australia/Victoria', 'Australia/West', 'Australia/Yancowinna', 'Europe/Amsterdam', 'Europe/Andorra', 'Europe/Athens', 'Europe/Belfast', 'Europe/Belgrade', 'Europe/Berlin', 'Europe/Bratislava', 'Europe/Brussels', 'Europe/Bucharest', 'Europe/Budapest', 'Europe/Chisinau', 'Europe/Copenhagen', 'Europe/Dublin', 'Europe/Gibraltar', 'Europe/Guernsey', 'Europe/Helsinki', 'Europe/Isle_of_Man', 'Europe/Istanbul', 'Europe/Jersey', 'Europe/Kaliningrad', 'Europe/Kiev', 'Europe/Lisbon', 'Europe/Ljubljana', 'Europe/London', 'Europe/Luxembourg', 'Europe/Madrid', 'Europe/Malta', 'Europe/Mariehamn', 'Europe/Minsk', 'Europe/Monaco', 'Europe/Moscow', 'Europe/Nicosia', 'Europe/Oslo', 'Europe/Paris', 'Europe/Podgorica', 'Europe/Prague', 'Europe/Riga', 'Europe/Rome', 'Europe/Samara', 'Europe/San_Marino', 'Europe/Sarajevo', 'Europe/Simferopol', 'Europe/Skopje', 'Europe/Sofia', 'Europe/Stockholm', 'Europe/Tallinn', 'Europe/Tirane', 'Europe/Tiraspol', 'Europe/Uzhgorod', 'Europe/Vaduz', 'Europe/Vatican', 'Europe/Vienna', 'Europe/Vilnius', 'Europe/Volgograd', 'Europe/Warsaw', 'Europe/Zagreb', 'Europe/Zaporozhye', 'Europe/Zurich', 'Indian/Antananarivo', 'Indian/Chagos', 'Indian/Christmas', 'Indian/Cocos', 'Indian/Comoro', 'Indian/Kerguelen', 'Indian/Mahe', 'Indian/Maldives', 'Indian/Mauritius', 'Indian/Mayotte', 'Indian/Reunion', 'Pacific/Apia', 'Pacific/Auckland', 'Pacific/Chatham', 'Pacific/Easter', 'Pacific/Efate', 'Pacific/Enderbury', 'Pacific/Fakaofo', 'Pacific/Fiji', 'Pacific/Funafuti', 'Pacific/Galapagos', 'Pacific/Gambier', 'Pacific/Guadalcanal', 'Pacific/Guam', 'Pacific/Honolulu', 'Pacific/Johnston', 'Pacific/Kiritimati', 'Pacific/Kosrae', 'Pacific/Kwajalein', 'Pacific/Majuro', 'Pacific/Marquesas', 'Pacific/Midway', 'Pacific/Nauru', 'Pacific/Niue', 'Pacific/Norfolk', 'Pacific/Noumea', 'Pacific/Pago_Pago', 'Pacific/Palau', 'Pacific/Pitcairn', 'Pacific/Ponape', 'Pacific/Port_Moresby', 'Pacific/Rarotonga', 'Pacific/Saipan', 'Pacific/Samoa', 'Pacific/Tahiti', 'Pacific/Tarawa', 'Pacific/Tongatapu', 'Pacific/Truk', 'Pacific/Wake', 'Pacific/Wallis', 'Pacific/Yap');

	// Get current timezone :
	$current_tz = @date_default_timezone_get();

	printf( '<h3>'. _('Welcome to NinjaFirewall %s installation!') .'</h3>', $GLOBALS['nfedn'] );
	?>
	<br />
	<form method="post">
	<h4><?php echo _('Regional Settings') ?></h4>
	<table width="100%" class="table table-nf">
		<tr>
			<td width="55%" align="left"><?php echo _('Select a display language') ?></td>
			<td width="45%" align="left">
				<select name="admin_lang" class="form-control">
					<?php
					foreach( $i18n_list as $i18n ) {
						echo '<option value ="' . htmlspecialchars( $i18n ) . '"';
						if ( $i18n == $tmp_lang ) {
							echo ' selected';
						}
						echo $disabled . '>' . htmlspecialchars( $i18n ) . '</option>';
					}
					?>
				</select>
				<?php
				if (! empty( $warn_msg ) ) {
					echo $warn_msg;
				}
				?>
			</td>
		</tr>
		<tr>
			<td width="55%" align="left"><?php echo _('Display dates and times using the following timezone') ?></td>
			<td width="45%" align="left">
				<select name="timezone" class="form-control">
				<?php
					foreach ( $zonelist as $tz_place ) {
						echo '<option value ="'. $tz_place .'"';
						if ( $current_tz == $tz_place ) {
							echo ' selected';
						}
						date_default_timezone_set( $tz_place );
						echo '>'. $tz_place .' (' .date('O'). ')</option>';
					}
					?>
				</select>
			</td>
		</tr>
	</table>
	<p style="text-align:right"><input type="submit" class="btn btn-md btn-success btn-30" value="<?php echo _('Next') . ' &#187;' ?>" /></p>
	<input type="hidden" name="nfw_act" value="2" />
	</form>
	<?php

	install_footer();
}

// =====================================================================

function nfw_system_requirements() {

	install_header();
	$critical = 0;
	?>
	<form method="post">
		<h4><?php echo _('System requirements') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="45%" align="left"><?php echo _('PHP version') ?></td>
			<?php
			// We need at least PHP 5.3 :
			if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
				?>
				<td width="10%" align="center"><?php echo glyphicon('error') ?></td>
				<td width="45%" align="left"><?php
				printf(
					_('NinjaFirewall requires PHP v5.3 or greater, but your current version is %s.'),
					htmlspecialchars( PHP_VERSION )
				)
				?></td>
			</tr>
			<?php
			$critical = 1;
			} else {
			?>
				<td width="10%" align="center"><?php echo glyphicon('success') ?></td>
				<td width="45%" align="left"><?php echo htmlspecialchars( PHP_VERSION ) .' ('. htmlspecialchars( strtoupper(PHP_SAPI) ) .')' ?></td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td width="45%" align="left"><?php echo _('Operating system') ?></td>
			<?php
			// We don't do Windows :
			if ( PATH_SEPARATOR == ';' ) {
				?>
				<td width="10%" align="center"><?php echo glyphicon('error') ?></td>
				<td width="45%" align="left"><?php echo _('NinjaFirewall is not compatible with Microsoft Windows.') ?></td>
			</tr>
			<?php
			$critical = 1;
			} else {
			?>
				<td width="10%" align="center"><?php echo glyphicon('success') ?></td>
				<td width="45%" align="left"><?php echo htmlspecialchars( PHP_OS ) ?></td>
			</tr>
			<?php
			}
			?>

			<tr>
				<td width="45%" align="left"><?php echo 'safe_mode' ?></td>
			<?php
			// Yes, there are still some people who have SAFE_MODE enabled with PHP 5.3+!
			if ( ini_get('safe_mode') ) {
				?>
				<td width="10%" align="center"><?php echo glyphicon('error') ?></td>
				<td width="45%" align="left"><?php echo _('You have "safe_mode" enabled, please disable it. This feature has been DEPRECATED as of PHP 5.3 and REMOVED as of PHP 5.4.') ?></td>
			</tr>
			<?php
			$critical = 1;
			} else {
			?>
				<td width="10%" align="center"><?php echo glyphicon('success') ?></td>
				<td width="45%" align="left"><?php echo _('Disabled') ?></td>
			</tr>
			<?php
			}
			?>

			<tr>
				<td width="45%" align="left"><?php echo 'auto_prepend_file' ?></td>
			<?php
			// Warn if auto_prepend_file is in used :
			if ( $tmp = ini_get('auto_prepend_file') ) {
				?>
				<td width="10%" align="center"><?php echo glyphicon('warning') ?></td>
				<td width="45%" align="left"><?php
				printf(
					_('"auto_prepend_file" is already in use: %s'. '<br />'.
					'Because NinjaFirewall needs to use this directive, it will orverride your current one.'),  htmlspecialchars( $tmp ) ) ?></td>
			</tr>
			<?php
			} else {
			?>
				<td width="10%" align="center"><?php echo glyphicon('success') ?></td>
				<td width="45%" align="left"><?php echo _('Unused') ?></td>
			</tr>
			<?php
			}
			?>

			<tr>
				<td width="45%" align="left"><?php echo 'magic_quotes_gpc' ?></td>
			<?php
			// Deprecated as of PHP 5.3 :
			if ( get_magic_quotes_gpc() ) {
				?>
				<td width="10%" align="center"><?php echo glyphicon('error') ?></td>
				<td width="45%" align="left"><?php echo _('You have "magic_quotes_gpc" enabled, please disable it, this feature has been DEPRECATED as of PHP 5.3 and REMOVED as of PHP 5.4.') ?></td>
			</tr>
			<?php
			$critical = 1;
			} else {
			?>
				<td width="10%" align="center"><?php echo glyphicon('success') ?></td>
				<td width="45%" align="left"><?php echo _('Disabled') ?></td>
			</tr>
			<?php
			}
			?>

			<tr>
				<td width="45%" align="left"><?php echo 'cURL' ?></td>
			<?php
			// Needed for updates and license (we cancel the installation if not found) :
			if (! function_exists('curl_init') ) {
				?>
				<td width="10%" align="center"><?php echo glyphicon('error') ?></td>
				<td width="45%" align="left"><?php echo _('Your PHP configuration does not have "cURL" support. Please install it.') ?></td>
			</tr>
			<?php
			$critical = 1;
			} else {
			?>
				<td width="10%" align="center"><?php echo glyphicon('success') ?></td>
				<td width="45%" align="left"><?php echo _('Installed') ?></td>
			</tr>
			<?php
			}
			?>

			<tr>
				<td width="45%" align="left"><?php echo 'ZipArchive' ?></td>
			<?php
			// Needed to unpack updates (we only issue a warning, because NF can still work without it) :
			if (! class_exists('ZipArchive') ) {
				?>
				<td width="10%" align="center"><?php echo glyphicon('warning') ?></td>
				<td width="45%" align="left"><?php echo _('Your PHP configuration does not have the "ZipArchive" class. You can still install NinjaFirewall, but you will not be able to use one-click updates.') ?></td>
			</tr>
			<?php
			} else {
			?>
				<td width="10%" align="center"><?php echo glyphicon('success') ?></td>
				<td width="45%" align="left"><?php echo _('Installed') ?></td>
			</tr>
			<?php
			}
			?>

			<tr>
				<td width="45%" align="left"><?php echo _('Configuration folder') ?></td>
			<?php
			// Configuration directory must be writable :
			if (! is_writable( './conf/' ) ) {
				?>
				<td width="10%" align="center"><?php echo glyphicon('error') ?></td>
				<td width="45%" align="left"><?php printf( _('The %s folder is not writable. Please change its permissions.'), '"conf/"' ) ?></td>
			</tr>
			<?php
			$critical = 1;
			} else {
			?>
				<td width="10%" align="center"><?php echo glyphicon('success') ?></td>
				<td width="45%" align="left"><?php printf( _('The %s folder is writable'), '"conf/"' ) ?></td>
			</tr>
			<?php
			}
			?>

			<tr>
				<td width="45%" align="left"><?php echo _('Log folder') ?></td>
			<?php
			// Log directory must be writable :
			if (! is_writable( './nfwlog/' ) ) {
				?>
				<td width="10%" align="center"><?php echo glyphicon('error') ?></td>
				<td width="45%" align="left"><?php printf( _('The %s folder is not writable. Please change its permissions.'), '"nfwlog/"' ) ?></td>
			</tr>
			<?php
			$critical = 1;
			} else {
			?>
				<td width="10%" align="center"><?php echo glyphicon('success') ?></td>
				<td width="45%" align="left"><?php printf( _('The %s folder is writable'), '"nfwlog/"' ) ?></td>
			</tr>
			<?php
			}
			?>
		</table>

	<?php
	if (! $critical ) {
	?>
		<p style="text-align:right"><input type="submit" class="btn btn-md btn-success btn-30" value="<?php echo _('Next') .' &#187;' ?>" /></p>
		<input type="hidden" name="nfw_act" value="3" />
	<?php
	} else {
	?>
		<div class="alert alert-danger text-left"><?php echo _('NinjaFirewall installation cannot proceed. Please change your system settings to match the  requirements.') ?></div>
	<?php
		// clear installation flag :
		$_SESSION['nfw_install'] = '';
	}
	?>
	</form>
	<?php
	install_footer();

}

// =====================================================================

function nfw_changelog() {

	install_header();

	if (! file_exists( __DIR__ .'/changelog.php' ) ) {
		?>
		<br />
		<div class="alert alert-danger text-left"><?php printf( _('Cannot find the %s file. Make sure your version of NinjaFirewall is not corrupted, or download it again from nintechnet.com website. Installation aborted.'), '"changelog.php"' ) ?></div>
		<?php
		install_footer();
		exit;
	}
	require __DIR__ .'/changelog.php';
	?>
	<form method="post">
		<h4><?php echo _('Release Notes') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="100%" align="center"><textarea class="form-control" style="height:300px;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?php
					echo htmlspecialchars( $changelog )
				?></textarea></td>
			</tr>
		</table>

		<p style="text-align:right"><input type="submit" class="btn btn-md btn-success btn-30" value="<?php echo _('Next') .' &#187;' ?>" /></p>
		<input type="hidden" name="nfw_act" value="4" />
	</form>
	<?php
	install_footer();
}

// =====================================================================

function nfw_license() {

	install_header();

	if (! file_exists( __DIR__ . '/license.txt' ) ) {
		?>
		<br />
		<div class="alert alert-danger text-left"><?php printf( _('Cannot find the %s file. Make sure your version of NinjaFirewall is not corrupted, or download it again from nintechnet.com website. Installation aborted.'), '"license.txt"' ) ?></div>
		<?php
		install_footer();
		exit;
	}
	$license = file_get_contents( __DIR__ .'/license.txt' );

	?>
	<form method="post">
		<h4><?php echo _('License') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="100%" align="center"><textarea class="form-control" style="height:300px;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?php
					echo htmlspecialchars( $license )
				?></textarea></td>
			</tr>
		</table>

		<p style="text-align:right"><input type="submit" class="btn btn-md btn-success btn-30" value="<?php echo _('Next') .' &#187;' ?>" /></p>
		<input type="hidden" name="nfw_act" value="5" />
	</form>
	<?php

	install_footer();
}

// =====================================================================

function nfw_admin_setup( $err ) {

	install_header();

	$admin_name = '';$admin_pass = ''; $lic = '';
	$admin_pass2 = '';$admin_email = '';
	if (! empty( $_SESSION['admin_name'] ) ) {
		$admin_name = $_SESSION['admin_name'];
	}
	if (! empty( $_SESSION['admin_pass'] ) ) {
		$admin_pass = $_SESSION['admin_pass'];
	}
	if (! empty( $_SESSION['admin_pass2'] ) ) {
		$admin_pass2 = $_SESSION['admin_pass2'];
	}
	if (! empty( $_SESSION['admin_email'] ) ) {
		$admin_email = $_SESSION['admin_email'];
	}
	if (! empty( $_SESSION['lic'] ) ) {
		$lic = $_SESSION['lic'];
	}
	?>
	<script>
	function check_fields() {
		if (! document.admin_form.admin_name.value) {
			alert("<?php echo _('Please enter the administrator name.') ?>");
			document.admin_form.admin_name.focus();
			return false;
		}
		if (!document.admin_form.admin_name.value.match(/^\w{6,20}$/)) {
			alert("<?php echo _('The administrator name can only contain from 6 to 20 alpha-numeric characters and underscore (_).') ?>");
			document.admin_form.admin_name.focus();
			return false;
		}
		if (! document.admin_form.admin_pass.value) {
			alert("<?php echo _('Please enter the administrator password.') ?>");
			document.admin_form.admin_pass.focus();
			return false;
		}
		if (document.admin_form.admin_pass.value.length < 6) {
			alert("<?php echo _('The administrator password must contain at least 6 characters.') ?>");
			document.admin_form.admin_pass.focus();
			return false;
		}
		if (! document.admin_form.admin_pass2.value) {
			alert("<?php echo _('Please retype the administrator password.') ?>");
			document.admin_form.admin_pass2.focus();
			return false;
		}
		if (document.admin_form.admin_pass.value != document.admin_form.admin_pass2.value) {
			alert("<?php echo _('The administrator password must be the same in both fields.') ?>");
			document.admin_form.admin_pass2.select();
			return false;
		}
		if (! document.admin_form.admin_email.value) {
			alert("<?php echo _('Please enter a valid administrator email address.') ?>");
			document.admin_form.admin_email.focus();
			return false;
		}
		if (! document.admin_form.lic.value) {
			alert("<?php echo _('Please enter your NinjaFirewall Pro+ license key.') ?>");
			document.admin_form.lic.focus();
			return false;
		}
		return true;
	}
	</script>

	<?php
	if ( $err ) {
		 echo '<br /><div class="alert alert-danger text-left">'. $err .'</div>';
	}
	?>

	<form method="post" name="admin_form" onSubmit="return check_fields();">
		<h4><?php echo _('Administrator') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="55%" align="left"><?php echo _('Administrator name (6 to 20 characters)') ?></td>
				<td width="45%" align="left">
					<input class="form-control" maxlength="20" name="admin_name" type="text" value="<?php echo htmlentities( $admin_name ) ?>" required />
				</td>
			</tr>

			<tr>
				<td width="55%" align="left"><?php echo _('Administrator password (at least 6 characters)') ?></td>
				<td width="45%" align="left">
					<input class="form-control" name="admin_pass" type="password" value="<?php echo htmlentities( $admin_pass ) ?>" required />
				</td>
			</tr>

			<tr>
				<td width="55%" align="left"><?php echo _('Retype administrator password') ?></td>
				<td width="45%" align="left">
					<input class="form-control" name="admin_pass2" type="password" value="<?php echo htmlentities( $admin_pass2 ) ?>" required />
				</td>
			</tr>

			<tr>
				<td width="55%" align="left"><?php echo _('Administrator email') ?></td>
				<td width="45%" align="left">
					<input class="form-control" maxlength="500" name="admin_email" type="email" value="<?php echo htmlentities( $admin_email ) ?>" required />
				</td>
			</tr>

			<?php if (NFW_EDN == 2) { ?>
			<tr>
				<td width="55%" align="left"><?php echo _('Enter your NinjaFirewall Pro+ license key') ?></td>
				<td width="45%" align="left">
					<input class="form-control" maxlength="500" name="lic" type="text" value="<?php echo htmlentities( $lic ) ?>" required />
				</td>
			</tr>
			<?php } ?>

		</table>

		<p style="text-align:right"><input type="submit" class="btn btn-md btn-success btn-30" value="<?php echo _('Next') .' &#187;' ?>" /></p>
		<input type="hidden" name="nfw_act" value="5" />
		<input type="hidden" name="save" value="1" />
	</form>
	<?php

	install_footer();

}

// =====================================================================

function nfw_admin_setup_save() {

	// Fetch default configuration :
	$nfw_options = fw_conf_options();

	$error_msg = 0;
	$_SESSION['admin_name']  = @$_POST['admin_name'];
	$_SESSION['admin_pass']  = @$_POST['admin_pass'];
	$_SESSION['admin_pass2'] = @$_POST['admin_pass2'];
	$_SESSION['admin_email'] = @$_POST['admin_email'];
	$_SESSION['lic']         = @trim( @$_POST['lic'] );

	if ( empty ($_POST['admin_name'] ) || ! preg_match('/^\w{6,20}$/', $_POST['admin_name'] ) ) {
		$error_msg = _('The administrator name can only contain from 6 to 20 alpha-numeric characters and underscore (_).');
		goto ADMIN_SAVE_END;
	} else {
		$nfw_options['admin_name'] = $_POST['admin_name'];
	}

	if ( empty( $_POST['admin_pass'] ) || strlen( $_POST['admin_pass'] ) < 6 ) {
		$error_msg = _('The administrator password must contain at least 6 characters.');
		goto ADMIN_SAVE_END;
	} else {
		// PHP <5.5: create a SHA-1 hash
		if (! function_exists( 'password_hash' ) || version_compare( PHP_VERSION, '5.5', '<' ) ) {
			$nfw_options['admin_pass'] = sha1( $_POST['admin_pass'] );
		// PHP >=5.5: use password_hash
		} else {
			$nfw_options['admin_pass'] = password_hash( $_POST['admin_pass'], PASSWORD_DEFAULT, array( 'cost' => 10 ) );
		}
	}

	// PHP filter_var does not accept local domain email addresses (e.g., foo@localhost),
	// therefore we allow anything that matches "/^\w+@\w+$/":
	if ( empty( $_POST['admin_email'] ) || ( ! preg_match('/^\w+@\w+$/', $_POST['admin_email'] ) &&
		! filter_var( $_POST['admin_email'], FILTER_VALIDATE_EMAIL ) ) ) {

		$error_msg = _('Please enter a valid administrator email address.');
		goto ADMIN_SAVE_END;
	} else {
		$nfw_options['admin_email'] = $_POST['admin_email'];
	}

	if ( NFW_EDN != 2 ) {
		goto ADMIN_SAVE_END;
	}

	if ( empty( $_POST['lic'] ) ) {
		$error_msg = _('Please enter your NinjaFirewall Pro+ license key.');
		goto ADMIN_SAVE_END;
	}
	$_POST['lic'] = trim( $_POST['lic'] );
	$_SESSION['lic'] = $_POST['lic'];
	$error_msg = '';

	$domain = strtolower( $_SERVER['HTTP_HOST'] );
	$data  = 'action=checklicense';
	$data .= '&host=' . urlencode( $domain );
	$data .= '&name=' . urlencode( strtolower( $_SERVER['SERVER_NAME'] ) );
	$data .= '&lic=' . urlencode( $_POST['lic'] );
	$data .= '&ver=' . urlencode( NFW_ENGINE_VERSION );
	$nfw_res = account_license_connect( $data );

	// cURL error ?
	if (! empty( $nfw_res['curl'] ) ) {
		if ( $nfw_res['curl'] == 1 ) {
			$error_msg = _('Unable to connect to NinjaFirewall server to check the license.');
		} elseif ( $nfw_res['curl'] == 2 ) {
			$error_msg = _('Unable to connect to NinjaFirewall server: HTTP error.');
		} elseif ( $nfw_res['curl'] == 3 ) {
			$error_msg = _('Unable to connect to NinjaFirewall server: no response or empty response.');
		} elseif ( $nfw_res['curl'] == 4 ) {
			$error_msg = _('Unable to connect to NinjaFirewall server: unexpected response.');
		}
		goto ADMIN_SAVE_END;
	}
	// Parse results :
	if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $nfw_res['exp'] ) ) {
		$nfw_options['lic'] = $_POST['lic'];
		$nfw_options['lic_exp'] = $nfw_res['exp'];
	} elseif (! empty( $nfw_res['err'] ) ) {
		$error_msg = sprintf( _('Your license is not valid (#%s).'), $nfw_res['err'] );
	} else {
		$error_msg = _('Unable to connect to NinjaFirewall server: unknown error.');
	}

ADMIN_SAVE_END:

	if ( $error_msg ) {
		nfw_admin_setup( $error_msg );
		exit;
	}

	// save conf and rules
	$nfw_options['timezone'] = $_SESSION['timezone'];
	$nfw_options['admin_lang'] = $_SESSION['admin_lang'];

	$nfw_rules = fw_conf_rules();

	// Add the DOCUMENT_ROOT :
	if ( strlen( $_SERVER['DOCUMENT_ROOT'] ) > 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', $_SERVER['DOCUMENT_ROOT'] );
	} elseif ( strlen( getenv( 'DOCUMENT_ROOT' ) ) > 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', getenv( 'DOCUMENT_ROOT' ) );
	} else {
		$nfw_rules[NFW_DOC_ROOT]['ena']  = 0;
	}

	// The Pro+ edition does not need rule #531 :
	if ( NFW_EDN == 2 ) {
		if ( isset( $nfw_rules[531] ) ) {
			unset( $nfw_rules[531] );
		}
	}

	// Enable PHP object injection rules (since v3.2.11):
	$nfw_rules[NFW_OBJECTS]['ena'] = 1;

	// Save options:
	$res = save_config( $nfw_options, 'options' );
	if (! empty( $res ) ) {
		nfw_admin_setup( $res );
		exit;
	}

	// And rules:
	$res = save_config( $nfw_rules, 'rules' );
	if (! empty( $res ) ) {
		// Delete options file :
		unlink( './conf/options.php' );
		nfw_admin_setup( $res );
		exit;
	}

	unset( $_SESSION['admin_pass'] );
	unset( $_SESSION['admin_pass2'] );
	unset( $_SESSION['lic'] );

	// OK
	return;
}

// =====================================================================

function nfw_integration( $err ) {

	// Let's try to detect the system configuration :
	$s1 = ''; $s2 = ''; $s3 = '';
	$s4 = ''; $s5 = ''; $s7 = '';
	if ( defined( 'HHVM_VERSION' ) ) {
		// HHVM
		$http_server = 7;
		$s7 = _(' (recommended)');
		$htaccess = 0;
		$php_ini = 0;
	} elseif ( preg_match( '/apache/i', PHP_SAPI ) ) {
		// Apache running mod_php5/7 :
		$http_server = 1;
		$s1 = _(' (recommended)');
		$htaccess = 1;
		$php_ini = 0;
	} elseif ( preg_match( '/litespeed/i', PHP_SAPI ) ) {
		// Because Litespeed can handle PHP INI and mod_php-like .htaccess,
		// we will create both of them as we have no idea which one should be used:
		$http_server = 4;
		$php_ini = 1;
		$htaccess = 1;
		$s4 = _(' (recommended)');
	} else {
		// PHP CGI: we will only require a PHP INI file:
		$php_ini = 1;
		$htaccess = 0;
		// Try to find out the HTTP server :
		if ( preg_match( '/apache/i', $_SERVER['SERVER_SOFTWARE'] ) ) {
			$http_server = 2;
			$s2 = _(' (recommended)');
		} elseif ( preg_match( '/nginx/i', $_SERVER['SERVER_SOFTWARE'] ) ) {
			$http_server = 3;
			$s3 = _(' (recommended)');
		} else {
			// Mark it as unknown, that is not important :
			$http_server = 5;
			$s5 = _(' (recommended)');
		}
	}

	// By default, NinjaFirewall will protect the directory above its installation folder :
	if (! empty( $_SESSION['document_root'] ) ) {
		$document_root = $_SESSION['document_root'];
	} else {
		$document_root  = dirname( __DIR__ );
	}

	install_header();

	?>
	<script>
	function check_fields() {
		if (! document.integration_form.document_root.value) {
			alert('<?php echo _('Please enter the directory to be protected by NinjaFirewall.') ?>');
			document.integration_form.document_root.focus();
			return false;
		}
		var ischecked = 0;
		for (var i = 0; i < document.integration_form.php_ini_type.length; ++i) {
			if(document.integration_form.php_ini_type[i].checked) {
				ischecked = 1;
				break;
			}
		}
		// Dont warn if user selected Apache/mod_php5/7 or HHVM
		if (! ischecked && document.integration_form.http_server.value != 1 && document.integration_form.http_server.value != 7 ) {
			alert('<?php echo _('Please select the PHP initialization file supported by your server.') ?>');
			return false;
		}
		return true;
	}
	function ini_toggle(what) {
		if (what == 1) {
			document.getElementById('trini').style.display = 'none';
			document.getElementById('hhvm').style.display = 'none';
		} else if(what == 7) {
			document.getElementById('trini').style.display = 'none';
			document.getElementById('hhvm').style.display = '';
		} else {
			document.getElementById('trini').style.display = '';
			document.getElementById('hhvm').style.display = 'none';
		}
	}
	</script>

	<?php
	if ($err) {
		echo '<br /><div class="alert alert-danger text-left">'. $err .'</div>';
	}
	?>
	<form method="post" name="integration_form" onSubmit="return check_fields();">
		<h4><?php echo _('Integration') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="45%" align="left">
					<?php printf( _('Enter the directory to be protected by NinjaFirewall. By default, it is <code>%s</code>'), htmlspecialchars( dirname( __DIR__ ) ) ) ?>
				</td>
				<td width="10%">&nbsp;</td>
				<td width="45%" align="left">
					<input class="form-control" name="document_root" type="text" value="<?php echo htmlentities( $document_root ) ?>">
				</td>
			</tr>
			<tr>
				<td width="45%" align="left"><?php echo _('Select your HTTP server and PHP SAPI') ?></td>
				<td width="10%">&nbsp;</td>
				<td width="45%" align="left">
					<select class="form-control" name="http_server" onchange="ini_toggle(this.value);">
						<option value="1"<?php _selected($http_server, 1) ?>>Apache + PHP module<?php echo $s1 ?></option>
						<option value="2"<?php _selected($http_server, 2) ?>>Apache + CGI/FastCGI<?php echo $s2 ?></option>
						<option value="6"<?php _selected($http_server, 6) ?>>Apache + suPHP</option>
						<option value="3"<?php _selected($http_server, 3) ?>>Nginx + <?php echo _('CGI or PHP-FPM') . $s3 ?></option>
						<option value="4"<?php _selected($http_server, 4) ?>>Litespeed<?php echo $s4 ?></option>
						<option value="5"<?php _selected($http_server, 5) ?>><?php echo _('Other webserver') . ' + CGI/FastCGI' . $s5 ?></option>
						<option value="7"<?php _selected($http_server, 7) ?>><?php echo _('Other webserver') . ' + HHVM' . $s7 ?></option>
					</select>
					<br />
					<a title="<?php echo _('Click to view your server PHP configuration') ?>" href="javascript:popup('?nfw_act=99',640,500,0);"><?php echo _('View PHPINFO') ?></a>
					<?php
					if ( $http_server == 7 ) {
						echo '<p id="hhvm">';
					} else {
						echo '<p id="hhvm" style="display:none;">';
					}
					?>
					<a href="https://blog.nintechnet.com/installing-ninjafirewall-with-hhvm-hiphop-virtual-machine/" target="_blank"><?php echo _('Please check our blog if you want to install NinjaFirewall on HHVM.') ?></a></p>
				</td>
			</tr>
			<?php
			// We check in the document root if there is already a PHP INI file :
			$f1 = $f2 = $f3 = $php_ini_type = '';
			if ( file_exists( dirname( __DIR__ ) .'/php.ini' ) ) {
				if ( empty( $_SESSION['php_ini_type'] ) ) {
					$f1 = _(' (recommended)');
				}
				$php_ini_type = 1;
			} elseif ( file_exists( dirname( __DIR__ ) .'/.user.ini') ) {
				if ( empty( $_SESSION['php_ini_type'] ) ) {
					$f2 = _(' (recommended)');
				}
				$php_ini_type = 2;
			} elseif ( file_exists( dirname( __DIR__ ) .'/php5.ini' ) ) {
				if (empty( $_SESSION['php_ini_type'] ) ) {
					$f3 = _(' (recommended)');
				}
				$php_ini_type = 3;
			}
			if ( $http_server == 1 || $http_server == 7 ) {
				// We don't need PHP INI if the server is running Apache/mod_php5/7 or HHVM :
				echo '<tr id="trini" style="display:none;">';
			} else {
				echo '<tr id="trini">';
			}
			?>
				<td width="45%" align="left"><?php echo _('Select the PHP initialization file supported by your server') ?><br />&nbsp;</td>
				<td width="10%">&nbsp;</td>
				<td width="45%" align="left">
					<p><label><input type="radio" name="php_ini_type" value="1"<?php _checked($php_ini_type, 1) ?>>&nbsp;<code>php.ini</code></label><?php echo $f1 ?><br /><i>&nbsp;<?php echo _('Used by most shared hosting accounts') ?>.</i></p>
					<p><label><input type="radio" name="php_ini_type" value="2"<?php _checked($php_ini_type, 2) ?>>&nbsp;<code>.user.ini</code></label><?php echo $f2 ?><br /><i>&nbsp;<?php echo _('Used by most dedicated/VPS servers, as well as shared hosting accounts that do not support php.ini') ?> (<a href="http://php.net/manual/en/configuration.file.per-user.php"><?php echo _('more info') ?></a>).</i></p>
					<p><label><input type="radio" name="php_ini_type" value="3"<?php _checked($php_ini_type, 3) ?>>&nbsp;<code>php5.ini</code></label><?php echo $f3 ?><br /><i>&nbsp;<?php echo _('A few shared hosting accounts. Seldom used') ?>.</i></p>
				</td>
			</tr>
		</table>

		<p style="text-align:right"><input type="submit" class="btn btn-md btn-success btn-30" value="<?php echo _('Next') .' &#187;' ?>" /></p>
		<input type="hidden" name="nfw_act" value="6" />
	</form>
	<?php
	install_footer();
	exit;
}

// =====================================================================

function nfw_integration_save() {

	$error_msg = 0;

	// Directory to monitor :
	$_SESSION['document_root'] = @$_POST['document_root'];
	if ( empty( $_SESSION['document_root'] ) ) {
		$error_msg = _('Please enter the directory to be protected by NinjaFirewall.');
		goto INTEGRATION_SAVE_END;
	}
	if (! is_dir( $_SESSION['document_root'] ) ) {
		$error_msg = sprintf(
			'%s is not a valid directory.',
			'<code>'.$_SESSION['document_root'] .'</code>'
		);
		goto INTEGRATION_SAVE_END;
	}

	// Chrooted document root:
	if ( $_SESSION['document_root'] == '/' ) {
		$tmp_dc = '/';
	} else {
		$_SESSION['document_root'] = rtrim( $_SESSION['document_root'], '/' );
		$tmp_dc = $_SESSION['document_root'] . '/';
	}

	// NinjaFirewall must be installed in that directory :
	if ( strpos( __FILE__, $tmp_dc ) === FALSE ) {
		$error_msg = _('The directory to protect must include NinjaFirewall installation folder.');
		goto INTEGRATION_SAVE_END;
	}

	// HTTP server type:
	// 1: Apache + PHP5 module
	// 2: Apache + CGI/FastCGI
	// 3: Nginx
	// 4: Litespeed (either LSAPI or Apache-style configuration directives (php_value)
	// 5: Other + CGI/FastCGI
	// 6: Apache + suPHP
	// 7: Other + HHVM
	if ( empty( $_POST['http_server'] ) || ! preg_match( '/^[1-7]$/', $_POST['http_server'] ) ) {
		$error_msg = _('Select your HTTP server and PHP SAPI');
		goto INTEGRATION_SAVE_END;
	}

	// We must have a PHP INI type, except if the server is running Apache/mod_php5/7 or HHVM:
	if ( preg_match( '/^[2-6]$/', $_POST['http_server'] ) ) {
		if ( empty( $_POST['php_ini_type'] ) || ! preg_match( '/^[1-3]$/', $_POST['php_ini_type'] ) ) {
			$error_msg = _('Select the PHP initialization file supported by your server');
			goto INTEGRATION_SAVE_END;
		}
	} else {
		$_POST['php_ini_type'] = 0;
	}
	$_SESSION['http_server'] = $_POST['http_server'];
	$_SESSION['php_ini_type'] = $_POST['php_ini_type'];

INTEGRATION_SAVE_END:

	if ( $error_msg ) {
		nfw_integration( $error_msg );
		exit;
	}
}

// =====================================================================

function nfw_activation() {

	if ( empty( $_SESSION['http_server'] ) || empty( $_SESSION['document_root'] ) ) {
		nfw_integration( sprintf( _('Error #%s'), 122 ) );
		exit;
	}
	if ( empty( $_SESSION['php_ini_type'] ) && preg_match( '/^[2-6]$/', $_POST['http_server'] ) ) {
		nfw_integration( sprintf( _('Error #%s'), 123 ) );
		exit;
	}

	install_header('', '');

	if ( $_SESSION['php_ini_type'] == 1 ) {
		$php_file = 'php.ini';
	} elseif ( $_SESSION['php_ini_type'] == 2 ) {
		$php_file = '.user.ini';
	} elseif ( $_SESSION['php_ini_type'] == 3 ) {
		$php_file = 'php5.ini';
	} else {
		$php_file = 0;
	}

	?>
	<h4><?php echo _('Activation') ?></h4>
	<table width="100%" class="table table-nf">
		<tr>
			<td width="100%" valign="top">
			<?php
			$fdata = '';
			$height = '';
			$bullet = '&#9679;&nbsp;';
			// Apache mod_php5/7 : only .htaccess changes are required :
			if ( $_SESSION['http_server'] == 1 ) {
				echo '<p>'. sprintf( _('In order to protect your site, NinjaFirewall needs some specific directives to be added to your <code>%s</code> file.'), '.htaccess' ) . '</p>';
				if ( file_exists( $_SESSION['document_root'] . '/.htaccess') ) {
					// Edit it :
					printf( $bullet . _('Please add the following <font color="red">red lines</font> of code to your <code>%s</code> file. All other lines, if any, are the actual content of the file and should not be changed:'), $_SESSION['document_root'] . '/.htaccess' );
					$color_start = '<font color="red">'; $color_end = '</font>';
					$fdata = "\n<font color='#aaa'>" . htmlentities( file_get_contents( $_SESSION['document_root'] . '/.htaccess' ) ) .'</font>';
				} else {
					// Create it :
					printf( $bullet . _('Please create a <code>%s</code> file, and add the following lines of code to it:'), $_SESSION['document_root'] . '/.htaccess' );
					$color_start = '';
					$color_end = '';
				}

				echo '<br /><br /><center><pre class="form-control" style="text-align:left;overflow:auto;width:90%;font-family:monospace;height:200px;">' . "\n" .
				$color_start . '# BEGIN NinjaFirewall' . "\n" .
				'&lt;IfModule mod_php' . PHP_MAJOR_VERSION . '.c&gt;' . "\n" .
				'   php_value auto_prepend_file ' . __DIR__ . '/firewall.php' . "\n" .
				'&lt;/IfModule&gt;' . "\n" .
				'# END NinjaFirewall' . "\n" .
				$color_end . $fdata . "\n" .
				'</pre></center><br />';

			// Litespeed : we create both INI and a .htaccess files as we have
			// no way to know which one will be used :
			} elseif ( $_SESSION['http_server'] == 4 ) {
				echo '<p>'. sprintf( _('In order to protect your site, NinjaFirewall needs some specific directives to be added to your <code>%s</code> and <code>%s</code> files.'), '.htaccess', $php_file ) .'</p>';

				if ( file_exists( $_SESSION['document_root'] .'/.htaccess' ) ) {
					// Edit it :
					printf($bullet . _('Please add the following <font color="red">red lines</font> of code to your <code>%s</code> file. All other lines, if any, are the actual content of the file and should not be changed:'), $_SESSION['document_root'] . '/.htaccess' );
					$color_start = '<font color="red">'; $color_end = '</font>';
					$fdata = "\n<font color='#aaa'>" . htmlentities( file_get_contents( $_SESSION['document_root'] . '/.htaccess' ) ) . '</font>';
				} else {
					// Create it :
					printf($bullet . _('Please create a <code>%s</code> file, and add the following lines of code to it:'), $_SESSION['document_root'] . '/.htaccess' );
					$color_start = $color_end = '';
				}

				echo '<br /><br /><center><pre class="form-control" style="text-align:left;overflow:auto;width:90%;font-family:monospace;height:200px;">' . "\n" .
				$color_start . '# BEGIN NinjaFirewall' . "\n" .
				'php_value auto_prepend_file ' . __DIR__ . '/firewall.php' . "\n" .
				'# END NinjaFirewall' . "\n" .
				$color_end . $fdata . "\n" .
				'</pre></center><br />';

				echo '<br /><br />';

				$fdata = ''; $height = '';
				if ( file_exists( $_SESSION['document_root'] .'/'. $php_file ) ) {
					// Edit it :
					printf( $bullet . _('Please add the following <font color="red">red lines</font> of code to your <code>%s</code> file. All other lines, if any, are the actual content of the file and should not be changed:'), $_SESSION['document_root'] . '/' . $php_file );
					$color_start = '<font color="red">'; $color_end = '</font>';
					$fdata = "\n<font color='#aaa'>" . htmlentities( file_get_contents( $_SESSION['document_root'] .'/'. $php_file ) ) . '</font>';
				} else {
					// Create it :
					printf( $bullet . _('Please create a <code>%s</code> file, and add the following lines of code to it:'), $_SESSION['document_root'] . '/' . $php_file );
					$color_start = ''; $color_end = '';
				}

				echo '<br /><br /><center><pre class="form-control" style="text-align:left;overflow:auto;width:90%;font-family:monospace;height:200px;">' . "\n" .
				$color_start . '; BEGIN NinjaFirewall' . "\n" .
				'auto_prepend_file = ' . __DIR__ . '/firewall.php' . "\n" .
				'; END NinjaFirewall' . "\n" .
				$color_end . $fdata . "\n" .
				'</pre></center><br />';

			// HHVM
			} elseif ( $_SESSION['http_server'] == 7 ) {
				echo '<p><a href="https://blog.nintechnet.com/installing-ninjafirewall-with-hhvm-hiphop-virtual-machine/" target="_blank">'. _('Please check our blog if you want to install NinjaFirewall on HHVM.') .'</a></p>' . $bullet . _('Add the following code to your <code>/etc/hhvm/php.ini</code> file, and restart HHVM afterwards:') .'
				<br /><br />';
				echo '<pre class="form-control" style="text-align:left;overflow:auto;height:200px;font-family:monospace;"><font color="red">auto_prepend_file = ' . __DIR__ . '/firewall.php</font></pre>
				<br />';

			// Other servers (nginx etc) :
			} else {

				// Apache + suPHP : we create both INI and .htaccess files as we need
				// to add the suPHP_ConfigPath directive (otherwise the INI will not
				// apply recursively) :
				if ( $_SESSION['http_server'] == 6 ) {
					echo '<p>' . sprintf( _('In order to protect your site, NinjaFirewall needs some specific directives to be added to your <code>%s</code> and <code>%s</code> files.'), '.htaccess', $php_file ) . '</p>';
					if ( file_exists( $_SESSION['document_root'] .'/.htaccess') ) {
						// Edit it :
						printf( $bullet . _('Please add the following <font color="red">red lines</font> of code to your <code>%s</code> file. All other lines, if any, are the actual content of the file and should not be changed:'), $_SESSION['document_root'] . '/.htaccess' );
						$color_start = '<font color="red">'; $color_end = '</font>';
						$fdata = "\n<font color='#aaa'>" . htmlentities( file_get_contents( $_SESSION['document_root'] . '/.htaccess' ) ) . '</font>';
					} else {
						// Create it :
						printf( $bullet . _('Please create a <code>%s</code> file, and add the following lines of code to it:'), $_SESSION['document_root'] . '/.htaccess' );
						$color_start = ''; $color_end = '';
					}

					echo '<br /><br /><center><pre class="form-control" style="text-align:left;overflow:auto;width:90%;font-family:monospace;height:200px;">' . "\n" .
					$color_start . '# BEGIN NinjaFirewall' . "\n" .
					'&lt;IfModule mod_suphp.c&gt;' . "\n" .
					'   suPHP_ConfigPath ' . rtrim( $_SESSION['document_root'], '/' ) . "\n" .
					'&lt;/IfModule&gt;' . "\n" .
					'# END NinjaFirewall' . "\n" .
					$color_end . $fdata . "\n" .
					'</pre></center><br />';
					echo '<br /><br />';
					$fdata = ''; $height = '';
				// Apache + suPHP
				} else {
					echo '<p>' . sprintf( _('In order to protect your site, NinjaFirewall needs some specific directives to be added to your <code>%s</code> file.'), $php_file) . '</p>';
				}
				if ( file_exists( $_SESSION['document_root'] .'/'. $php_file ) ) {
					// Edit it :
					printf( $bullet . _('Please add the following <font color="red">red lines</font> of code to your <code>%s</code> file. All other lines, if any, are the actual content of the file and should not be changed:'), $_SESSION['document_root'] . '/' . $php_file );
					$color_start = '<font color="red">'; $color_end = '</font>';
					$fdata = "\n<font color='#aaa'>" . htmlentities( file_get_contents( $_SESSION['document_root'] .'/'. $php_file ) ) . '</font>';
				} else {
					// Create it :
					printf( $bullet . _('Please create a <code>%s</code> file, and add the following lines of code to it:'), $_SESSION['document_root'] .'/'. $php_file );
					$color_start = $color_end = '';
				}

				echo '<br /><br /><center><pre class="form-control" style="text-align:left;overflow:auto;width:90%;font-family:monospace;height:200px;">' . "\n" .
				$color_start . '; BEGIN NinjaFirewall' . "\n" .
				'auto_prepend_file = ' . __DIR__ . '/firewall.php' . "\n" .
				'; END NinjaFirewall' . "\n" .
				$color_end . $fdata . "\n" .
				'</pre></center><br />';
			}
			?>
					<p><?php echo _('After making those changes, please click on the button below to test NinjaFirewall.') ?></p>
				</td>
			</tr>
		</table>

		<p style="text-align:right"><input type="button" class="btn btn-md btn-success btn-30" value="<?php echo _('Test NinjaFirewall') ?> &#187;" name="test_nfw" onclick="popup('?nfw_act=98',600,450,0)" /></p>

	<?php
	install_footer();
	exit;
}

// =====================================================================

function nfw_activation_test() {

	global $err_fw;

	if ( $_SESSION['php_ini_type'] == 1 ) {
		$php_file = 'php.ini';
	} elseif ( $_SESSION['php_ini_type'] == 2 ) {
		$php_file = '.user.ini';
	} else {
		$php_file = 'php5.ini';
	}

	?><html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="cache-control" content="no-store" />
	<meta http-equiv="expires" content="Mon, 01 Sep 2014 01:01:01 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
	<title><?php echo _('NinjaFirewall: Installation') ?></title>
	<link rel="Shortcut Icon" type="image/gif" href="static/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
	<link href="static/bootstrap.min.css?ver=<?php echo NFW_ENGINE_VERSION ?>" rel="stylesheet" type="text/css">
	<link href="static/styles.css?ver=<?php echo NFW_ENGINE_VERSION ?>" rel="stylesheet" type="text/css">
	<script>function goback(){window.opener.location.href='?nfw_act=5&nfw_test=1';window.close();}</script>
</head>
<body>
<div id="main_nr" class="ntn-section">
	<div class="container">
		<div class="row ">
			<div  class="col-sm-12">
				<table width="100%" class="table table-nf">
					<tr>
						<td>
						<?php
						// The firewall is not loaded:
						if (! defined( 'NFW_STATUS' ) ) {
						?>
							<center>
								<h3><?php echo glyphicon('error') ?>&nbsp;<?php echo _('Failed!') ?></h3>
								<p><?php echo _('The firewall is not loaded.') ?></p>
							</center>
							<p><?php echo _('Suggestions:') ?></p>

							<?php
							if ( $_SESSION['http_server'] == 1 ) {
								// User choosed Apache/mod_php instead of CGI/FCGI:
								?>
								<p>&#9679;&nbsp;<?php echo _('You selected <code>Apache + PHP5 module</code> as your HTTP server and PHP SAPI. Maybe your HTTP server is <code>Apache + CGI/FastCGI</code>?') ?> <a style="cursor:pointer" onClick="goback();"><?php echo _('You can go back and try to select another HTTP server type') ?></a>.</p>
							<?php
							} else {
								// Very likely a PHP INI issue:
								if ( $_SESSION['php_ini_type'] == 2 ) {
								?>
									<p>&#9679;&nbsp;<?php echo _(	'You have selected <code>.user.ini</code> as your PHP initialization file. Unlike <code>php.ini</code>, <code>.user.ini</code> files are not reloaded immediately by PHP, but every 5 minutes. If this is your own server, restart Apache (or PHP-FPM if you are running nginx) to force PHP to reload it, otherwise please wait a few minutes and then, click the button below to run the firewall test again.') ?></p>
								<?php
								}
								// User choosed Apache/CGI instead of mod_php:
								if ( $_SESSION['http_server'] == 2 ) {
								?>
									<p>&#9679;&nbsp;<?php echo _('You selected <code>Apache + CGI/FastCGI</code> as your HTTP server &amp; PHP SAPI. Maybe your HTTP server is <code>Apache + PHP5 module</code>?') ?> <a style="cursor:pointer" onClick="goback();"><?php echo _('You can go back and try to select another HTTP server type') ?></a>.</p>
								<?php
								}
								?>
								<p>&#9679;&nbsp;<?php echo _('You may have selected the wrong PHP initialization file.') ?> <a style="cursor:pointer" onClick="goback();"><?php echo _('You can go back and try to select another INI file') ?></a>.</p>
							<?php
							}
							?>
						</td>
					</tr>
				</table>
				<p style="text-align:center"><?php echo glyphicon('help') ?>&nbsp;<strong><?php echo _('Need help? Check our blog:') ?></strong>
				<br />
				<a href="https://blog.nintechnet.com/troubleshoot-ninjafirewall-installation-problems/" target="_blank">Troubleshoot NinjaFirewall installation problems</a>.</p>
				<br />
				<p style="text-align:center"><input type="button" class="btn btn-md btn-warning btn-35" value="<?php echo _('Test again') ?>" onClick="location.reload(); " /></p>

				<?php
				} elseif ( NFW_STATUS != 22 ) {
					// Firewall is loaded, but returned and error:
					if ( empty( $err_fw[NFW_STATUS] ) ) {
						$msg = 'Unknown error #' . NFW_STATUS;
					} else {
						$msg = $err_fw[NFW_STATUS];
					}
				?>
							<center>
								<h3><?php echo glyphicon('error') ?>&nbsp;<?php echo _('Error!') ?></h3>
								<p><?php echo _('NinjaFirewall is loaded but returned the following error:') ?></p>
								<p><?php echo $msg ?></p>
								<p><?php echo _('You may need to restart the installer to fix that error.') ?></p>
							</center>
						</td>
					</tr>
				</table>
				<p style="text-align:center"><input type="button" class="btn btn-md btn-success btn-35" value="<?php echo _('Close') ?>" onClick="window.close();" /></p>
				<?php
				// Everything is fine. We redirect user to the login page:
				} else {
				?>
							<center>
								<h3><?php echo glyphicon('success') ?>&nbsp;<?php echo _('It works!') ?></h3>
								<p><?php echo _('Congratulations, NinjaFirewall is up and running.') ?></p>
								<p><?php echo _('Click the button below to be redirected to the administration console.') ?></p>
							</center>
						</td>
					</tr>
				</table>

				<p style="text-align:center"><input type="button" class="btn btn-md btn-success btn-35" value="<?php echo _('Next') .  ' &#187;' ?>" onClick="opener.location.href='login.php';window.close();" /></p>
				<?php

					// Send an email to the admin with links and info about NinjaFirewall:
					if (! empty( $_SESSION['admin_email'] ) ) {
						$subject = '[NinjaFirewall] ' . _('Quick Start, FAQ & Troubleshooting Guide');

						$message = _('Hi,') . "\n\n";

						$message.= _("This is NinjaFirewall's installer. Below are some helpful info ".
										"and links you may consider reading before using NinjaFirewall.") . "\n\n";

						$message.= '1. ' . _('Troubleshooting:') . "\n";
						$message.= 'https://nintechnet.com/ninjafirewall/pro-edition/help/?troubleshooting ' . "\n\n";

						$message.= _('-Failed installation ("Error: the firewall is not loaded")?') . "\n";
						$message.= _('-How to disable NinjaFirewall?') . "\n";
						$message.= _('-Blocked visitors?') . "\n";
						$message.= _('-I lost my administrator password. How can I recover it?') . "\n\n";

						$message.= '2. ' . _('NinjaFirewall (Pro/Pro+ Edition) troubleshooter script:') . "\n";
						$message.= 'https://nintechnet.com/share/pro-check.txt ' . "\n\n";
						$message.=  _('-Rename this file to "pro-check.php".') . "\n";
						$message.=  _('-Upload it into yourwebsite root folder.') . "\n";
						$message.=  _('-Goto http://YOUR WEBSITE/pro-check.php.') . "\n";
						$message.=  _('-Delete it afterwards.') . "\n\n";

						$message.= '3. '. _('FAQ:') . "\n";
						$message.= 'https://nintechnet.com/ninjafirewall/pro-edition/help/?faq ' . "\n\n";

						$message.= _('-Do I need root privileges to install NinjaFirewall?') . "\n";
						$message.= _('-Does it work with Nginx?') . "\n";
						$message.= _('-Do I need to alter my PHP scripts?') . "\n";
						$message.= _('-Will NinjaFirewall detect the correct IP of my visitors if '.
										'I am behind a CDN service like Cloudflare or Incapsula?') . "\n";
						$message.= _('-Will it slow down my site?') . "\n";
						$message.= _('-Is there any Microsoft Windows version?') . "\n";
						$message.= _('-Can I add/write my own security rules?') . "\n";
						$message.= _('-Can I migrate my site(s) with NinjaFirewall installed?') . "\n\n";

						$message.= '4. '. _('Must Read:') . "\n\n";

						$message.= _('-An introduction to NinjaFirewall filtering engine:') . "\n";
						$message.= 'https://blog.nintechnet.com/introduction-to-ninjafirewall-filtering-engine/ ' . "\n\n";

						$message.= _('-Testing NinjaFirewall without blocking your visitors:') . "\n";
						$message.= 'https://blog.nintechnet.com/testing-ninjafirewall-without-blocking-your-visitors/ ' . "\n\n";

						$message.= _('-Add your own code to the firewall: the ".htninja" file:') . "\n";
						$message.= 'https://nintechnet.com/ninjafirewall/pro-edition/help/?htninja ' . "\n\n";

						$message.= _('-Upgrading to PHP 7 with NinjaFirewall installed:') . "\n";
						$message.= 'https://blog.nintechnet.com/upgrading-to-php-7-with-ninjafirewall-installed/ ' . "\n\n";

						$message.= _('-Securing a Joomla! installation with NinjaFirewall:') . "\n";
						$message.= 'https://blog.nintechnet.com/securing-a-joomla-installation-with-ninjafirewall-pro/ ' . "\n\n";

						$message.= '5. '. _('Help & Support Links:') . "\n\n";

						$message.= _('-Each page of NinjaFirewall includes a contextual help: click on the "Help" menu tab located in the upper right corner of the corresponding page.') . "\n";
						$message.= _('-Updates info are available via Twitter:') . " https://twitter.com/nintechnet \n";
						$message.= '-NinjaFirewall website: https://nintechnet.com/ ' . "\n";
						$message.= _('-Help Desk (Premium users only):'). ' https://nintechnet.com/helpdesk/ ' . "\n";

						$headers = 'From: "'. $_SESSION['admin_email'] .'" <'. $_SESSION['admin_email'] .'>' . "\r\n";
						$headers .= "Content-Transfer-Encoding: 7bit\r\n";
						$headers .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
						$headers .= "MIME-Version: 1.0\r\n";

						mail( $_SESSION['admin_email'], $subject, $message, $headers, '-f'. $_SESSION['admin_email'] );
					}
					session_destroy();
				}
			?>
			</div>
		</div>
	</div>
</div>
</body>
</html><?php

	exit;
}

// =====================================================================

function install_header() {

	?><!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta name="author" content="NinTechNet">
	<meta http-equiv="cache-control" content="no-store" />
	<meta http-equiv="expires" content="Mon, 01 Sep 2014 01:01:01 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
	<title><?php echo _('NinjaFirewall: Installation') ?></title>
	<link rel="Shortcut Icon" type="image/gif" href="static/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
	<link href="static/bootstrap.min.css?ver=<?php echo NFW_ENGINE_VERSION ?>" rel="stylesheet" type="text/css">
	<link href="static/styles.css?ver=<?php echo NFW_ENGINE_VERSION ?>" rel="stylesheet" type="text/css">
	<script>window.name='nfwmain';function popup(url,width,height,scroll_bar) {height=height+20;width=width+20;var str = "height=" + height + ",innerHeight=" + height;str += ",width=" + width + ",innerWidth=" + width;if (window.screen){var ah = screen.availHeight - 30;var aw = screen.availWidth -10;var xc = (aw - width) / 2;var yc = (ah - height) / 2;str += ",left=" + xc + ",screenX=" + xc;str += ",top=" + yc + ",screenY=" + yc;if (scroll_bar) {str += ",scrollbars=no";}else {str += ",scrollbars=yes";}str += ",status=no,location=no,resizable=yes";}win = open(url, "nfpop", str);setTimeout("win.window.focus()",1300);}</script>
</head>
<body>
<div id="main_nr" class="ntn-section">
	<div class="container">
		<div class="row ">
			<!-- form -->
			<div  class="col-sm-12">
				<img src="static/logo_200.png" class="center-block">
	<?php

}

// =====================================================================

function install_footer( $no_copyright = 0 ) {
	?>
					</div>
				</div>
			</div>
		</div>
		<div id="footer" class="ntn-section">
			<div class="container footer-font">
				<div class="row">
					<div  class="col-sm-12 text-center">
					<?php
					if ( empty( $no_copyright ) ) {
						echo "NinjaFirewall {$GLOBALS['nfedn']} v". NFW_ENGINE_VERSION .' ('.
						sprintf( _('security rules: %s'), NFW_RULES_VERSION ) .')<br />'.
						'&copy; 2011-'. date('Y') .' <a href="https://nintechnet.com/">The Ninja Technologies Network</a>';
					}
					?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html><?php

   exit;
}

// =====================================================================

function fw_conf_options() {

	// Populate the options:

	$nfw_options = array (

		'engine_version' 		=> NFW_ENGINE_VERSION,
		'rules_version'		=> NFW_RULES_VERSION,

		// Account > Options:
		'admin_login_alert'	=> 1,
		'admin_ssl' 			=> 0,
		'login_updates'		=>	1,

		// Firewall > Options:
		'enabled' 				=> 1,
		'debug' 					=> 0,
		'ret_code' 				=> 403,
		'blocked_msg' 			=> base64_encode( NFW_DEFAULT_MSG ),
		'ban_ip' 				=> 0,
		'ban_time' 				=> 0,

		// Firewall > Access Control:
		'admin_wl'				=>	0,
		'admin_wl_session'	=>	0,
		'ac_ip' 					=> 1,
		'ac_ip_2' 				=> 0,
		'ac_scan_loopback'	=> 1,
		'ac_method' 			=> 'GETPOSTHEADPUTDELETEPATCH',
		'ac_geoip' 				=> 0,
		'ac_geoip_db' 			=> 1,
		'ac_geoip_db2' 		=> '',
		'ac_geoip_cn' 			=> '',
		'ac_geoip_ninja' 		=> 0,
		'ac_allow_ip' 			=> 0,
		'ac_block_ip' 			=> 0,
		'ac_rl_on' 				=> 0,
		'ac_rl_time' 			=> 30,
		'ac_rl_conn' 			=> 10,
		'ac_rl_intv' 			=> 5,
		'ac_bl_url'				=> 0,
		'ac_wl_url' 			=> 0,
		'ac_bl_bot' 			=> NFW_BOT_LIST,

		// Access Control logs:
		'ac_geoip_log' 		=> 1,
		'ac_allow_ip_log' 	=> 0,
		'ac_block_ip_log' 	=> 1,
		'ac_rl_log' 			=> 1,
		'ac_bl_url_log' 		=> 1,
		'ac_bl_bot_log' 		=> 1,
		'ac_wl_url_log' 		=> 0,

		// Firewall > Web Filter:
		'wf_enable' 			=> 0,
		'wf_pattern' 			=> 0,
		'wf_case' 				=> 0,
		'wf_alert' 				=> 30,
		'wf_attach' 			=> 1,

		// Firewall > Policies:
		'scan_protocol' 		=> 3,
		'uploads' 				=> 2,
		'substitute' 			=> 'X',
		'sanitise_fn' 			=> 0,
		'upload_maxsize' 		=> 0, // Defined below since v3.2.12
		'get_scan' 				=> 1,
		'get_sanitise' 		=> 0,
		'post_scan'				=> 1,
		'post_sanitise' 		=> 0,
		'post_b64' 				=> 1,
		'request_sanitise'	=> 0,
		'cookies_scan' 		=> 1,
		'cookies_sanitise'	=> 0,
		'ua_scan' 				=> 1,
		'ua_sanitise' 			=> 1,
		'ua_mozilla' 			=> 0,
		'ua_accept' 			=> 0,
		'ua_accept_lang' 		=> 0,
		'block_bots'			=> 0,	// Pro edn only :
		'referer_scan' 		=> 0,
		'referer_sanitise'	=> 1,
		'referer_post' 		=> 0,
		'php_errors' 			=> 1,
		'php_self' 				=> 1,
		'php_path_t' 			=> 1,
		'php_path_i' 			=> 1,
		'no_host_ip' 			=> 0,
		'request_method' 		=> 0,

		// Firewall > File Guard:
		'fg_enable' 			=> 0,
		'fg_mtime'				=> 10,
		'fg_exclude'			=>	'',

		// Firewall > Log:
		'logging' 				=> 1,
		'log_rotate' 			=> 1,
		'log_maxsize' 			=> 2097152,
		'log_line'				=>	1500,
		// v3.2.12:
		'syslog'					=>	0,
	);
	// Some compatibility checks:
	// 1. header_register_callback(): requires PHP >=5.4
	// 2. headers_list() and header_remove(): some hosts may disable
	if ( function_exists('header_register_callback') && function_exists('headers_list')
		&& function_exists('header_remove') ) {

		// Security headers:
		$nfw_options['response_headers'] = '000300000';
	}
	$nfw_options['referrer_policy_enabled'] = 0;

	// Try to get the current PHP configuration value for "upload_max_filesize":
	$nfw_options['upload_maxsize'] = return_bytes( ini_get('upload_max_filesize') );
	if ( empty( $nfw_options['upload_maxsize'] ) ) {
		// Set it to 10MB (10240 KB):
		$nfw_options['upload_maxsize'] = 10240;
	}

	return $nfw_options;
}

// =====================================================================

function fw_conf_rules() {

	// Populate the custom rules:

	global $nfw_rules;

	// Try to get the document root :
	if ( strlen( $_SERVER['DOCUMENT_ROOT'] ) > 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', $_SERVER['DOCUMENT_ROOT'] );
		$nfw_rules[NFW_DOC_ROOT]['ena'] = 1;
	} elseif ( strlen( getenv( 'DOCUMENT_ROOT' ) ) > 5 ) {
		$nfw_rules[NFW_DOC_ROOT]['cha'][1]['wha'] = str_replace( '/', '/[./]*', getenv( 'DOCUMENT_ROOT' ) );
		$nfw_rules[NFW_DOC_ROOT]['ena'] = 1;
	} else {
		$nfw_rules[NFW_DOC_ROOT]['ena'] = 0;
	}

	$nfw_rules[NFW_WRAPPERS]['ena'] 	= 1;
	$nfw_rules[NFW_OBJECTS]['ena'] = 0;
	$nfw_rules[NFW_NULL_BYTE]['ena'] = 1;
	$nfw_rules[NFW_ASCII_CTRL]['ena'] = 0;
	$nfw_rules[NFW_LOOPBACK]['ena'] = 0;

	return $nfw_rules;

}

// =====================================================================

function account_license_connect( $data ) {

	// Check license validity (Pro+ edition only) :

	global $domain;
	require_once __DIR__ .'/lib/misc.php';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, 'NinjaFirewall/' . NFW_ENGINE_VERSION .
													':'. NFW_EDN .'; '. $domain );
	curl_setopt( $ch, CURLOPT_ENCODING, '');
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_URL, 'http://'. NFW_UPDATE .'/index.php' );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt ($ch, CURLOPT_HEADER, 0);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1 );

	if ( ( $content = curl_exec( $ch ) ) === FALSE ) {
		@curl_close( $ch );
		$package['curl'] = 1;
		return $package;
	}
	$response = curl_getinfo( $ch );
	curl_close( $ch );

	// Errors ?
	if ( $response['http_code'] != 200 ) {
		$package['curl'] = 2;
		return $package;
	}
	if (! $content ) {
		$package['curl'] = 3;
		return $package;
	}

	$package = @unserialize( $content );
	if (! isset( $package['exp']) || ! isset($package['err'] ) ) {
		$package['curl'] = 4;
		return $package;
	}

	// Looks good :
	$package['curl'] = 0;
	return $package;
}

// =====================================================================

function _selected( $var, $val) {

	if ( $var == $val ) {
		echo " selected='selected'";
	}

}

// =====================================================================

function _checked( $var, $val) {

	if ( $var == $val ) {
		echo " checked='checked'";
	}

}

// =====================================================================

function install_error( $msg ) {

	// If there were an error, make sure NFW_ENGINE_VERSION is defined
	// before calling install_header():
	if (! defined('NFW_ENGINE_VERSION') ) {
		define('NFW_ENGINE_VERSION', time() );
	}
	install_header();
	echo '<br /><div class="alert alert-danger text-left">'. $msg .'</div>';
	install_footer(1);
	exit;

}

// =====================================================================
// EOF
