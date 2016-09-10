<?php
/*
 +---------------------------------------------------------------------+
 | NinjaFirewall (Pro edition)                                         |
 |                                                                     |
 | (c) NinTechNet - http://nintechnet.com/                             |
 |                                                                     |
 +---------------------------------------------------------------------+
 | REVISION: 2016-08-17 17:18:43                                       |
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
 +---------------------------------------------------------------------+
*/
$http_err_code = array(
	  0 => "Unknown Error",
	100 => "Continue",
	101 => "Switching Protocols",
	200 => "OK",
	201 => "Created",
	202 => "Accepted",
	203 => "Non-Authoritative Information",
	204 => "No Content",
	205 => "Reset Content",
	206 => "Partial Content",
	300 => "Multiple Choices",
	301 => "Moved Permanently",
	302 => "Moved Temporarily",
	303 => "See Other",
	304 => "Not Modified",
	305 => "Use Proxy",
	306 => "(Unused)",
	307 => "Temporary Redirect",
	400 => "Bad Request",
	401 => "Unauthorized",
	402 => "Payment Required",
	403 => "Forbidden",
	404 => "Not Found",
	405 => "Method Not Allowed",
	406 => "Not Acceptable",
	407 => "Proxy Authentication Required",
	408 => "Request Timeout",
	409 => "Conflict",
	410 => "Gone",
	411 => "Length Required",
	412 => "Precondition Failed",
	413 => "Request Entity Too Large",
	414 => "Request-URI Too Long",
	415 => "Unsupported Media Type",
	416 => "Requested Range Not Satisfiable",
	417 => "Expectation Failed",
	500 => "Internal Server Error",
	501 => "Not Implemented",
	502 => "Bad Gateway",
	503 => "Service Unavailable",
	504 => "Gateway Timeout",
	505 => "HTTP Version Not Supported",
	// Misc :
	520 => "Proxy Publisher Failure",
	521 => "Web Server is down",
	522 => "Connection Time Out",
	523 => "Origin is unreachable",
	524 => "A Timeout occured",
);

/* ------------------------------------------------------------------ */

function nfw_select_ip() {
	// Ensure we have a proper and single IP (a user may use the .htninja file
	// to redirect HTTP_X_FORWARDED_FOR, which may contain more than one IP,
	// to REMOTE_ADDR):
	if (strpos($_SERVER['REMOTE_ADDR'], ',') !== false) {
		$nfw_match = array_map('trim', @explode(',', $_SERVER['REMOTE_ADDR']));
		foreach($nfw_match as $nfw_m) {
			if ( filter_var($nfw_m, FILTER_VALIDATE_IP) )  {
				define( 'NFW_REMOTE_ADDR', $nfw_m);
				break;
			}
		}
	}
	if (! defined('NFW_REMOTE_ADDR') ) {
		define('NFW_REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
	}
}

/* ------------------------------------------------------------------ */
// EOF
