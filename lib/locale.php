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

if ( empty( $nfw_options ) && ! defined('NFW_INSTALLER') ) { die( 'Forbidden' ); }

if ( empty( $nfw_options['admin_lang'] ) || ! preg_match( '/^[a-z]{2}_[A-Z]{2}$/', $nfw_options['admin_lang'] ) ) {
	return;
}
// I18N:
putenv( "LANG={$nfw_options['admin_lang']}" );
setlocale( LC_MESSAGES, "{$nfw_options['admin_lang']}.utf-8" );
setlocale( LC_CTYPE, "{$nfw_options['admin_lang']}.utf-8" );
$domain = "ninjafirewall_pro-{$nfw_options['admin_lang']}";
bindtextdomain( $domain, dirname( __DIR__ ) . '/locale' );
textdomain( $domain );
// ---------------------------------------------------------------------
// EOF
