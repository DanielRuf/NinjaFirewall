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


// Saved options?
if (! empty( $_POST['post'] ) ) {
	$err_msg = save_account_options();
}

// Display headers after the save_account_options()
// because it can set a cookie to whitelist the admin:
html_header();
?>
<div class="col-sm-12 text-left">
	<h3><?php echo _('Account > Options') ?></h3>
	<br />
<?php

if (! empty( $err_msg ) ) {
	echo '<div class="alert alert-danger text-left"><a class="close" data-dismiss="alert" aria-label="close">&times;</a>'. $err_msg .'</div>';
} elseif ( isset( $err_msg ) ) {
	echo '<div class="alert alert-success text-left"><a class="close" data-dismiss="alert" aria-label="close">&times;</a>'. _('Your changes were saved.') . '</div>';
}

?>
<form method="post" name="nf_options">
	<h4><?php echo _('Login password') ?></h4>
	<table width="100%" class="table table-nf">
		<tr>
			<td width="40%" align="left"><?php echo _('Password length must be at least 6 characters') ?></td>
			<td width="5%">&nbsp;</td>
			<td width="55%" align="left">
				<p><?php echo _('Old password') ?>&nbsp;<input type="password" class="form-control" size="20" name="old_admin_pass"></p>
				<p><?php echo _('New password') ?>&nbsp;<input type="password" class="form-control" size="20" name="new_admin_pass"></p>
			  <p><?php echo _('Verify new password') ?>&nbsp;<input type="password" class="form-control" size="20" name="new_admin_pass_2"></p>
			</td>
		</tr>
	</table>

	<br />

	<h4><?php echo _('Contact') ?></h4>
	<table width="100%" class="table table-nf">
		<tr>
			<td width="40%" align="left"><?php echo _('Enter your email address where NinjaFirewall will send you alerts and notifications') ?></td>
			<td width="5%">&nbsp;</td>
			<td width="55%" align="left"><?php echo _('Email address') ?>&nbsp;<input type="email" required class="form-control" value="<?php echo htmlspecialchars( $nfw_options['admin_email'] ) ?>" name="admin_email"></td>
		</tr>
	</table>

	<br />

<?php
// Get current timezone :
$zonelist = timezone_list();
$current_tz = @date_default_timezone_get();

?>
	<h4><?php echo _('Regional Settings') ?></h4>
	<table width="100%" class="table table-nf">
		<tr>
			<td width="40%" align="left"><?php echo _('Display dates and times using the following timezone') ?></td>
			<td width="5%">&nbsp;</td>
			<td width="55%" align="left">
				<select name="timezone" class="form-control">
				<?php
				foreach ( $zonelist as $tz_place ) {
					echo '<option value ="' . $tz_place . '"';
					if ( $current_tz == $tz_place ) { echo ' selected'; }
					date_default_timezone_set( $tz_place );
					echo '>'. $tz_place .' (' .date('O'). ')</option>';
				}
				?>
				</select>
			</td>
		</tr>

		<?php
		// Fetch all language files available:
		if ( empty( $nfw_options['admin_lang'] ) || ! preg_match( '/^[a-z]{2}_[A-Z]{2}$/', $nfw_options['admin_lang'] ) ) {
			$nfw_options['admin_lang'] = 'en_US';
		}
		$i18n_list = array( 'en_US' ); // Default
		$locale_dir = dirname( __DIR__ ) . '/locale';
		$i18n_dirs = glob( "{$locale_dir}/*", GLOB_ONLYDIR );
		foreach ( $i18n_dirs as $i18n_dir ) {
			if ( preg_match( '`/([a-z]{2}_[A-Z]{2})$`', $i18n_dir, $match ) ) {
				if ( file_exists( "{$locale_dir}/{$match[1]}/LC_MESSAGES/ninjafirewall_pro-{$match[1]}.mo" ) ) {
					// Author's name & credits must be included
					// in a "author.nfo" file (first line only):
					if ( file_exists( "{$locale_dir}/{$match[1]}/LC_MESSAGES/author.nfo" ) ) {
						$file_content = file(
							"{$locale_dir}/{$match[1]}/LC_MESSAGES/author.nfo",
							FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
						$author = ' ('. _('from') .' '. trim( $file_content[0] ) .')';
					} else {
						$author = '';
					}
					$i18n_list[] = $match[1] . $author;
				}
			}
		}
		sort( $i18n_list );

		// Make sure the Gettext extension is installed, otherwise we warn
		// and disable the possibility to change the language:
		$disabled = '';
		$warn_msg = '';
		if (! function_exists( 'gettext' ) ) {
			$warn_msg = glyphicon('help', _('The PHP Gettext extension is missing on your server. Please install it if you want to change languages.') );
			$disabled = ' disabled';
		}
		?>
		<tr>
			<td width="40%" align="left"><?php echo _('Select a display language') ?></td>
			<td width="5%"><?php echo $warn_msg ?></td>
			<td width="55%" align="left">
				<select name="admin_lang" class="form-control">
					<?php
					foreach( $i18n_list as $i18n ) {
						echo '<option value ="' . htmlspecialchars( $i18n ) . '"';
						if ( $i18n == $nfw_options['admin_lang'] ) {
							echo ' selected';
						}
						echo $disabled . '>' . htmlspecialchars( $i18n ) . '</option>';
					}
					?>
				</select>
			</td>
		</tr>
	</table>

	<br />

	<h4><?php echo _('Appearance') ?></h4>
	<table width="100%" class="table table-nf">
		<tr>
			<td width="40%" align="left"><?php echo _('Loaded style sheets') ?></td>
			<td width="5%">&nbsp;</td>
			<td width="55%" align="left"><?php
			echo 'Bootstrap 3 + NinjaFirewall';
			if ( file_exists( 'static/user.css' ) ) {
				echo ' + '. sprintf( _('user style sheet (%s)'), '<code>static/user.css</code>' ). '<br /><br />';
			} else {
				echo '<br /><br /><i>';
				printf(
					_('If you want to customize NinjaFirewall UI and load your own style sheet, <a href="%s">consult this article</a>.'),
					'https://blog.nintechnet.com/ninjafirewall-pro-new-ui/#user-style'
				);
				echo '</i>';
			}
			?>
			</td>
		</tr>
	</table>

	<br />

	<h4><?php echo _('Login') ?></h4>
	<table width="100%" class="table table-nf">
		<tr>
			<td width="40%" align="left"><?php echo _('Send me an email whenever someone logs in to my NinjaFirewall admin account') ?></td>
			<td width="5%">&nbsp;</td>
			<td width="55%" align="left">
				<label><input type="checkbox" name="admin_login_alert"<?php checked($nfw_options['admin_login_alert'], 1) ?>>&nbsp;<?php echo _('Yes').' '. _('(default)') ?></label>
			</td>
		</tr>
		<tr>
			<td width="40%" align="left"><?php echo _('Always log in using a secure connection (HTTPS)') ?></td>
			<td width="5%">&nbsp;</td>
			<td width="55%" align="left">
				<label><input type="checkbox" name="admin_ssl"<?php checked($nfw_options['admin_ssl'], 1) ?> onclick="return ssl_warn();">&nbsp;<?php echo _('Yes') ?></label>
			</td>
		</tr>
		<?php
		if (! isset( $nfw_options['login_updates'] ) ) {
			$nfw_options['login_updates'] = 1;
		}
		?>
		<tr>
			<td width="40%" align="left"><?php echo _('Automatically check for updates when I log in') ?></td>
			<td width="5%" align="center">&nbsp;</td>
			<td width="55%"><label><input type="checkbox" name="login_updates"<?php checked($nfw_options['login_updates'], 1) ?> />&nbsp;<?php echo _('Yes') .' '. _('(default)') ?></label></td>
		</tr>
	</table>

	<br />
	<center>
		<input type="submit" class="btn btn-md btn-success btn-25" value="<?php echo _('Save changes') ?>">
	</center>
	<input type="hidden" name="mid" value="<?php echo $GLOBALS['mid'] ?>">
	<input type="hidden" name="post" value="1">
	<br />

</form>
</div>
<?php

html_footer();

// ---------------------------------------------------------------------

function save_account_options() {

	global $nfw_options;

	$err_msg = '';

	// Password changed :
   $old_admin_pass   = @$_POST['old_admin_pass'];
   $new_admin_pass   = @$_POST['new_admin_pass'];
   $new_admin_pass_2 = @$_POST['new_admin_pass_2'];

   if ( $old_admin_pass || $new_admin_pass_2 || $new_admin_pass ) {
		if (! $old_admin_pass || ! $new_admin_pass_2 || ! $new_admin_pass ) {
			$err_msg .= '<p>'. _('If you want to change your login password, please enter the current one ' .
							'and twice your new password. Otherwise, leave those fields blank.') .'<p>';

		} else if ( $new_admin_pass_2 !== $new_admin_pass ) {
			$err_msg .= '<p>'. _('The new password must be the same in both fields.') .'</p>';

		} else if ( $new_admin_pass_2 === $new_admin_pass ) {
			// Old SHA-1 hash?
			if ( preg_match( '/^[0-9a-f]{40}$/', $nfw_options['admin_pass'] ) ) {
				$encoded = sha1( $old_admin_pass );
				if ( $encoded !== $nfw_options['admin_pass'] ) {
					$encoded = false;
				}
			// Bcrypt hash?
			} else {
				$encoded = password_verify( $old_admin_pass, $nfw_options['admin_pass'] );
			}

			if ( $encoded === false ) {
				$err_msg .= '<p>'. _('The old password is not correct.') .'</p>';

			} else if ( strlen( $new_admin_pass ) < 6 ) {
				$err_msg .= '<p>'. _('The new password must contain at least 6 characters.') .'</p>';

			} else {
				// PHP <5.5: create SHA-1 hash
				if (! function_exists( 'password_hash' ) || version_compare( PHP_VERSION, '5.5', '<' ) ) {
					$nfw_options['admin_pass'] = sha1( $new_admin_pass );
				// PHP >=5.5: use password_hash
				} else {
					// The algorithmic cost can be user-defined in the .htninja file
					// E.g.: define('NF_PASSWORD_COST', 13);
					// Default is 10 and should be suitable for most hardware (e.g., single-core VPS)
					// but a cost of 13 would provide better security:
					if ( defined('NF_PASSWORD_COST') ) {
						$cost = (int) NF_PASSWORD_COST;
					} else {
						$cost = 10;
					}
					$nfw_options['admin_pass'] = password_hash( $new_admin_pass, PASSWORD_DEFAULT, [ 'cost' => $cost  ] );
				}
			}
		}
	}

	// PHP filter_var does not accept local domain email addresses (e.g., foo@localhost),
	// therefore we allow anything that matches "/^\w+@\w+$/":
	if ( empty( $_POST['admin_email'] ) || ( ! preg_match('/^\w+@\w+$/', $_POST['admin_email'] ) &&
		! filter_var( $_POST['admin_email'], FILTER_VALIDATE_EMAIL ) ) ) {

		$err_msg .= '<p>'. _('Please enter a valid email address.') .'</p>';
	} else {
		$nfw_options['admin_email'] = $_POST['admin_email'];
	}


	// Regional Settings :
	if (! empty( $_POST['timezone'] ) && @date_default_timezone_set( $_POST['timezone'] ) ) {
		$nfw_options['timezone'] = $_POST['timezone'];
	}

	if ( empty( $_POST['admin_lang'] ) || ! preg_match( '/^[a-z]{2}_[A-Z]{2}$/', $_POST['admin_lang'] ) ) {
		$nfw_options['admin_lang'] = 'en_US';
	} else {
		$nfw_options['admin_lang'] = $_POST['admin_lang'];
	}


	// Login:
	if ( isset( $_POST['admin_login_alert'] ) && $_POST['admin_login_alert']  == 'on' ) {
		$nfw_options['admin_login_alert'] = 1;
	} else {
		$nfw_options['admin_login_alert'] = 0;
	}
	if ( isset( $_POST['admin_ssl'] ) && $_POST['admin_ssl']  == 'on' ) {
		$nfw_options['admin_ssl'] = 1;
	} else {
		$nfw_options['admin_ssl'] = 0;
	}
	if ( isset( $_POST['login_updates'] ) && $_POST['login_updates']  == 'on' ) {
		$nfw_options['login_updates'] = 1;
	} else {
		$nfw_options['login_updates'] = 0;
	}

	$res = save_config( $nfw_options, 'options' );
	if (! empty( $res ) ) {
		return $res;
	}

	return $err_msg;

}

// ---------------------------------------------------------------------

function timezone_list() {

	return array('UTC', 'Africa/Abidjan', 'Africa/Accra', 'Africa/Addis_Ababa', 'Africa/Algiers', 'Africa/Asmara', 'Africa/Asmera', 'Africa/Bamako', 'Africa/Bangui', 'Africa/Banjul', 'Africa/Bissau', 'Africa/Blantyre', 'Africa/Brazzaville', 'Africa/Bujumbura', 'Africa/Cairo', 'Africa/Casablanca', 'Africa/Ceuta', 'Africa/Conakry', 'Africa/Dakar', 'Africa/Dar_es_Salaam', 'Africa/Djibouti', 'Africa/Douala', 'Africa/El_Aaiun', 'Africa/Freetown', 'Africa/Gaborone', 'Africa/Harare', 'Africa/Johannesburg', 'Africa/Kampala', 'Africa/Khartoum', 'Africa/Kigali', 'Africa/Kinshasa', 'Africa/Lagos', 'Africa/Libreville', 'Africa/Lome', 'Africa/Luanda', 'Africa/Lubumbashi', 'Africa/Lusaka', 'Africa/Malabo', 'Africa/Maputo', 'Africa/Maseru', 'Africa/Mbabane', 'Africa/Mogadishu', 'Africa/Monrovia', 'Africa/Nairobi', 'Africa/Ndjamena', 'Africa/Niamey', 'Africa/Nouakchott', 'Africa/Ouagadougou', 'Africa/Porto-Novo', 'Africa/Sao_Tome', 'Africa/Timbuktu', 'Africa/Tripoli', 'Africa/Tunis', 'Africa/Windhoek', 'America/Adak', 'America/Anchorage', 'America/Anguilla', 'America/Antigua', 'America/Araguaina', 'America/Argentina/Buenos_Aires', 'America/Argentina/Catamarca', 'America/Argentina/ComodRivadavia', 'America/Argentina/Cordoba', 'America/Argentina/Jujuy', 'America/Argentina/La_Rioja', 'America/Argentina/Mendoza', 'America/Argentina/Rio_Gallegos', 'America/Argentina/Salta', 'America/Argentina/San_Juan', 'America/Argentina/San_Luis', 'America/Argentina/Tucuman', 'America/Argentina/Ushuaia', 'America/Aruba', 'America/Asuncion', 'America/Atikokan', 'America/Atka', 'America/Bahia', 'America/Barbados', 'America/Belem', 'America/Belize', 'America/Blanc-Sablon', 'America/Boa_Vista', 'America/Bogota', 'America/Boise', 'America/Buenos_Aires', 'America/Cambridge_Bay', 'America/Campo_Grande', 'America/Cancun', 'America/Caracas', 'America/Catamarca', 'America/Cayenne', 'America/Cayman', 'America/Chicago', 'America/Chihuahua', 'America/Coral_Harbour', 'America/Cordoba', 'America/Costa_Rica', 'America/Cuiaba', 'America/Curacao', 'America/Danmarkshavn', 'America/Dawson', 'America/Dawson_Creek', 'America/Denver', 'America/Detroit', 'America/Dominica', 'America/Edmonton', 'America/Eirunepe', 'America/El_Salvador', 'America/Ensenada', 'America/Fort_Wayne', 'America/Fortaleza', 'America/Glace_Bay', 'America/Godthab', 'America/Goose_Bay', 'America/Grand_Turk', 'America/Grenada', 'America/Guadeloupe', 'America/Guatemala', 'America/Guayaquil', 'America/Guyana', 'America/Halifax', 'America/Havana', 'America/Hermosillo', 'America/Indiana/Indianapolis', 'America/Indiana/Knox', 'America/Indiana/Marengo', 'America/Indiana/Petersburg', 'America/Indiana/Tell_City', 'America/Indiana/Vevay', 'America/Indiana/Vincennes', 'America/Indiana/Winamac', 'America/Indianapolis', 'America/Inuvik', 'America/Iqaluit', 'America/Jamaica', 'America/Jujuy', 'America/Juneau', 'America/Kentucky/Louisville', 'America/Kentucky/Monticello', 'America/Knox_IN', 'America/La_Paz', 'America/Lima', 'America/Los_Angeles', 'America/Louisville', 'America/Maceio', 'America/Managua', 'America/Manaus', 'America/Marigot', 'America/Martinique', 'America/Matamoros', 'America/Mazatlan', 'America/Mendoza', 'America/Menominee', 'America/Merida', 'America/Mexico_City', 'America/Miquelon', 'America/Moncton', 'America/Monterrey', 'America/Montevideo', 'America/Montreal', 'America/Montserrat', 'America/Nassau', 'America/New_York', 'America/Nipigon', 'America/Nome', 'America/Noronha', 'America/North_Dakota/Center', 'America/North_Dakota/New_Salem', 'America/Ojinaga', 'America/Panama', 'America/Pangnirtung', 'America/Paramaribo', 'America/Phoenix', 'America/Port-au-Prince', 'America/Port_of_Spain', 'America/Porto_Acre', 'America/Porto_Velho', 'America/Puerto_Rico', 'America/Rainy_River', 'America/Rankin_Inlet', 'America/Recife', 'America/Regina', 'America/Resolute', 'America/Rio_Branco', 'America/Rosario', 'America/Santa_Isabel', 'America/Santarem', 'America/Santiago', 'America/Santo_Domingo', 'America/Sao_Paulo', 'America/Scoresbysund', 'America/Shiprock', 'America/St_Barthelemy', 'America/St_Johns', 'America/St_Kitts', 'America/St_Lucia', 'America/St_Thomas', 'America/St_Vincent', 'America/Swift_Current', 'America/Tegucigalpa', 'America/Thule', 'America/Thunder_Bay', 'America/Tijuana', 'America/Toronto', 'America/Tortola', 'America/Vancouver', 'America/Virgin', 'America/Whitehorse', 'America/Winnipeg', 'America/Yakutat', 'America/Yellowknife', 'Arctic/Longyearbyen', 'Asia/Aden', 'Asia/Almaty', 'Asia/Amman', 'Asia/Anadyr', 'Asia/Aqtau', 'Asia/Aqtobe', 'Asia/Ashgabat', 'Asia/Ashkhabad', 'Asia/Baghdad', 'Asia/Bahrain', 'Asia/Baku', 'Asia/Bangkok', 'Asia/Beirut', 'Asia/Bishkek', 'Asia/Brunei', 'Asia/Calcutta', 'Asia/Choibalsan', 'Asia/Chongqing', 'Asia/Chungking', 'Asia/Colombo', 'Asia/Dacca', 'Asia/Damascus', 'Asia/Dhaka', 'Asia/Dili', 'Asia/Dubai', 'Asia/Dushanbe', 'Asia/Gaza', 'Asia/Harbin', 'Asia/Ho_Chi_Minh', 'Asia/Hong_Kong', 'Asia/Hovd', 'Asia/Irkutsk', 'Asia/Istanbul', 'Asia/Jakarta', 'Asia/Jayapura', 'Asia/Jerusalem', 'Asia/Kabul', 'Asia/Kamchatka', 'Asia/Karachi', 'Asia/Kashgar', 'Asia/Kathmandu', 'Asia/Katmandu', 'Asia/Kolkata', 'Asia/Krasnoyarsk', 'Asia/Kuala_Lumpur', 'Asia/Kuching', 'Asia/Kuwait', 'Asia/Macao', 'Asia/Macau', 'Asia/Magadan', 'Asia/Makassar', 'Asia/Manila', 'Asia/Muscat', 'Asia/Nicosia', 'Asia/Novokuznetsk', 'Asia/Novosibirsk', 'Asia/Omsk', 'Asia/Oral', 'Asia/Phnom_Penh', 'Asia/Pontianak', 'Asia/Pyongyang', 'Asia/Qatar', 'Asia/Qyzylorda', 'Asia/Rangoon', 'Asia/Riyadh', 'Asia/Saigon', 'Asia/Sakhalin', 'Asia/Samarkand', 'Asia/Seoul', 'Asia/Shanghai', 'Asia/Singapore', 'Asia/Taipei', 'Asia/Tashkent', 'Asia/Tbilisi', 'Asia/Tehran', 'Asia/Tel_Aviv', 'Asia/Thimbu', 'Asia/Thimphu', 'Asia/Tokyo', 'Asia/Ujung_Pandang', 'Asia/Ulaanbaatar', 'Asia/Ulan_Bator', 'Asia/Urumqi', 'Asia/Vientiane', 'Asia/Vladivostok', 'Asia/Yakutsk', 'Asia/Yekaterinburg', 'Asia/Yerevan', 'Atlantic/Azores', 'Atlantic/Bermuda', 'Atlantic/Canary', 'Atlantic/Cape_Verde', 'Atlantic/Faeroe', 'Atlantic/Faroe', 'Atlantic/Jan_Mayen', 'Atlantic/Madeira', 'Atlantic/Reykjavik', 'Atlantic/South_Georgia', 'Atlantic/St_Helena', 'Atlantic/Stanley', 'Australia/ACT', 'Australia/Adelaide', 'Australia/Brisbane', 'Australia/Broken_Hill', 'Australia/Canberra', 'Australia/Currie', 'Australia/Darwin', 'Australia/Eucla', 'Australia/Hobart', 'Australia/LHI', 'Australia/Lindeman', 'Australia/Lord_Howe', 'Australia/Melbourne', 'Australia/NSW', 'Australia/North', 'Australia/Perth', 'Australia/Queensland', 'Australia/South', 'Australia/Sydney', 'Australia/Tasmania', 'Australia/Victoria', 'Australia/West', 'Australia/Yancowinna', 'Europe/Amsterdam', 'Europe/Andorra', 'Europe/Athens', 'Europe/Belfast', 'Europe/Belgrade', 'Europe/Berlin', 'Europe/Bratislava', 'Europe/Brussels', 'Europe/Bucharest', 'Europe/Budapest', 'Europe/Chisinau', 'Europe/Copenhagen', 'Europe/Dublin', 'Europe/Gibraltar', 'Europe/Guernsey', 'Europe/Helsinki', 'Europe/Isle_of_Man', 'Europe/Istanbul', 'Europe/Jersey', 'Europe/Kaliningrad', 'Europe/Kiev', 'Europe/Lisbon', 'Europe/Ljubljana', 'Europe/London', 'Europe/Luxembourg', 'Europe/Madrid', 'Europe/Malta', 'Europe/Mariehamn', 'Europe/Minsk', 'Europe/Monaco', 'Europe/Moscow', 'Europe/Nicosia', 'Europe/Oslo', 'Europe/Paris', 'Europe/Podgorica', 'Europe/Prague', 'Europe/Riga', 'Europe/Rome', 'Europe/Samara', 'Europe/San_Marino', 'Europe/Sarajevo', 'Europe/Simferopol', 'Europe/Skopje', 'Europe/Sofia', 'Europe/Stockholm', 'Europe/Tallinn', 'Europe/Tirane', 'Europe/Tiraspol', 'Europe/Uzhgorod', 'Europe/Vaduz', 'Europe/Vatican', 'Europe/Vienna', 'Europe/Vilnius', 'Europe/Volgograd', 'Europe/Warsaw', 'Europe/Zagreb', 'Europe/Zaporozhye', 'Europe/Zurich', 'Indian/Antananarivo', 'Indian/Chagos', 'Indian/Christmas', 'Indian/Cocos', 'Indian/Comoro', 'Indian/Kerguelen', 'Indian/Mahe', 'Indian/Maldives', 'Indian/Mauritius', 'Indian/Mayotte', 'Indian/Reunion', 'Pacific/Apia', 'Pacific/Auckland', 'Pacific/Chatham', 'Pacific/Easter', 'Pacific/Efate', 'Pacific/Enderbury', 'Pacific/Fakaofo', 'Pacific/Fiji', 'Pacific/Funafuti', 'Pacific/Galapagos', 'Pacific/Gambier', 'Pacific/Guadalcanal', 'Pacific/Guam', 'Pacific/Honolulu', 'Pacific/Johnston', 'Pacific/Kiritimati', 'Pacific/Kosrae', 'Pacific/Kwajalein', 'Pacific/Majuro', 'Pacific/Marquesas', 'Pacific/Midway', 'Pacific/Nauru', 'Pacific/Niue', 'Pacific/Norfolk', 'Pacific/Noumea', 'Pacific/Pago_Pago', 'Pacific/Palau', 'Pacific/Pitcairn', 'Pacific/Ponape', 'Pacific/Port_Moresby', 'Pacific/Rarotonga', 'Pacific/Saipan', 'Pacific/Samoa', 'Pacific/Tahiti', 'Pacific/Tarawa', 'Pacific/Tongatapu', 'Pacific/Truk', 'Pacific/Wake', 'Pacific/Wallis', 'Pacific/Yap');
}

// ---------------------------------------------------------------------
// EOF
