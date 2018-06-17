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

if ( empty( $nfw_options ) && ! defined('NFW_INSTALLER') ) {
	die( 'Forbidden' );
}

// ---------------------------------------------------------------------
define('NFW_ENGINE_VERSION', '3.3.3');
define('NFW_RULES_VERSION', '20180603.1');
define('NFW_EDN', 1);

if (! defined( 'NFW_UPDATE' ) ) {
	define('NFW_UPDATE', 'pro.nintechnet.com');
}
// ---------------------------------------------------------------------

// Used by the admin script and the installer :
$err_fw = array(
	1	=>	_('Unable to find NinjaFirewall options file (conf/options.php).'),
	2	=>	_('Unable to read NinjaFirewall options file (conf/options.php).'),
	3	=>	_('Unable to find NinjaFirewall rules file (conf/rules.php).'),
	4	=>	_('Unable to read NinjaFirewall rules file (conf/rules.php).'),
	5	=>	sprintf( _('Firewall has been disabled from the <a href="%s">administration console</a>.'), '?mid=30&token='. htmlspecialchars( @$_REQUEST['token'] ) ),
	10	=>	_('Unable to communicate with the firewall. Please check your settings.'),
);

define( 'NFW_NULL_BYTE', 2);
define( 'NFW_ASCII_CTRL', 500);
define( 'NFW_DOC_ROOT', 510);
define( 'NFW_WRAPPERS', 520);
define( 'NFW_OBJECTS', 525);
define( 'NFW_LOOPBACK', 540);
// Pro Edtion only :
define('NFW_SCAN_BOTS', 531);
// Pro+ Edtion only :
define('NFW_BOT_LIST', 'acunetix|AhrefsBot|backdoor|bandit|' .
	'blackwidow|BOT for JCE|core-project|dts agent|emailmagnet|' .
	'exploit|extract|flood|grabber|harvest|httrack|havij|hunter|indy library|' .
	'LoadTimeBot|mfibot|Microsoft URL Control|Miami Style|morfeus|' .
	'nessus|NetLyzer|pmafind|scanner|semrushbot|siphon|spbot|sqlmap|' .
	'survey|teleport|updown_tester|xovibot|zgrap|zmap'
);

define( 'NFW_DEFAULT_MSG', '<br /><br /><br /><br /><center>'. _('Sorry ' .
	'<b>%%REM_ADDRESS%%</b>, your request cannot be processed.<br />' .
	'For security reasons, it was blocked and logged.<br /><br />' .
	'If you believe this was an error please contact the<br />' .
	'webmaster and enclose the following incident ID:') .'<br />' .
	'<br />[ <b>#%%NUM_INCIDENT%%</b> ]</center>'
);

// ---------------------------------------------------------------------
// EOF

