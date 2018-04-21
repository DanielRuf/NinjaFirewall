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

$changelog = <<<'EOT'

= 3.3 =
* NinjaFirewall has a new user interface. You can even customize it with your own style sheet. See our blog for more info: http://nin.link/nfui/
* Internationalization is now using the GNU Gettext. You can translate NinjaFirewall into your language by using the PO file available in "./locale/ninjafirewall_pro.pot".
* [Pro+ Edition] Improved the "Access Control > Whitelist the Administrator" option. In a few cases, when the option was enabled, the admin was not whitelisted upon login.
* Updated the contextual help.
* Several small fixes and improvements.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Updated security rules.

= 3.2.14 =
* Added "IP Anonymization" option. It will anonymize IP addresses in the firewall log by removing their last 3 characters. See "Firewall > Options > IP Anonymization".
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Minor fixes.
* Updated security rules.

= 3.2.13 =
* On servers running PHP 5.5 or above, NinjaFirewall will no longer user SHA1 for the administrator password, but the "password_hash()" function with the best algorithm available (currently bcrypt). To convert your current password, simply log out and log in again after applying this update.
* The "Uploads > Allow, but block scripts, ELF and system files" firewall policy was renamed to "Allow, but block dangerous files" and will also block dangerous SVG files. Therefore, the complete list of blocked files is now: scripts (PHP, CGI, Ruby, Python, bash/shell), C/C++ source code, binaries (MZ/PE/NE and ELF formats), system files (.htaccess, .htpasswd and PHP INI) and SVG files containing Javascript/XML events.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Minor fixes.
* Updated security rules.

= 3.2.12 =
* In addition to the firewall log, all events can also be redirected to the server Syslog. See our blog for more info: http://nin.link/syslog/
* By default, the "Maximum allowed file size" policy will use the same value as the PHP `upload_max_filesize` directive or, if not available, it will be set to 10 megabytes.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Minor fixes.
* Updated security rules.

= 3.2.11 =
* Multidimensional arrays in the $_FILES superglobal are now fully supported.
* It is possible to select which superglobal the "Block serialized PHP objects" policy can apply to (see "Firewall > Policies > PHP > Block serialized PHP objects in the following global variables"). By default, all but "COOKIE" will be enabled.
* The "Sanitise filenames" policy will not allow the use of the slash character "/" as a substitution character because it is the directory separator in Unix-like systems.
* Updated security rules.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Minor fixes.

= 3.2.10 =
* The substitution character used to sanitise filenames can be changed (see "Firewall > Policies > Uploads > Sanitise filenames > Substitution character").
* The "X-Content-Type-Options" firewall policy will be disabled by default when installing NinjaFirewall.
* When creating the snapshot, "File Check" will remove any whitespace character preceding or following the excluded folders name.
* Improved uploaded script detection to prevent false positives.
* Minor fixes (typos etc) and several small adjustments.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Updated security rules.

= 3.2.9 =
* Updated security rules.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.

= 3.2.8 =
* Updated security rules.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.

= 3.2.7 =
* Updated security rules.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.

= 3.2.6 =
* Updated security rules.
* Added two new comparison operators to the firewall fitering engine.
* The "Block PHP built-in wrappers" firewall policy has been extended to "expect://", "file://", "phar://" and "zip://" streams. Previously, it covered only "php://" and "data://" streams.
* All "textarea" HTML elements will turn browsers spell checking off to prevent annoying highlighting.
* The "Block ASCII character 0x00" and "Block ASCII control characters" policies will no longer apply to COOKIE to prevent false positives.
* Minor fixes and adjustments.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* [Pro+ Edition] Added "PATCH" method to the "Firewall > Access Control > HTTP Methods" section.

= 3.2.5 =
* Updated security rules.
* Tweaked list of suspicious bots to prevent potential false-positives.
* Improved PHP scripts detection.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.

= 3.2.4 =
* Added an option to block serialized PHP objects found inside a GET or POST request, cookies, user agent and referrer variables (see the "Firewall > Policies > PHP" section).
* Improved PHP scripts detection to cover more extensions and to prevent, in some rare cases, uploaded images to be wrongly detected as PHP scripts.
* [Pro+ Edition] The "File Guard" files/folders exclusion list can contain now up to 255 characters (vs 155 previously).
* [Pro+ Edition] The Access Control rate limiting feature will always return a "429 Too Many Requests" HTTP status code.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Updated security rules.

= 3.2.3 =
* Updated security rules.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.

= 3.2.2 =
* Improved the filtering engine cache for better reliability and speed.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Updated security rules.
* Fixed "Cache-Control" header in the firewall blocked message.
* [Pro+ Edition] Fixed a "Undefined index: lic_exp" PHP notice.
* Fixed a few CSS issues with Webkit-based browsers.

= 3.2.1 =
* Updated security rules.
* Added "max_execution_time" directive to "File Check" to prevent time-out.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Fixed a bug in the "Summary > Statistics" page where the "Average time per request" field could report a wrong value.
* Fixed a few CSS issues with Webkit-based browsers (Opera, Chrome/Chromium, Safari).
* The "Block scripts, ELF and system files upload" will also block Microsoft executable files (MZ header).
* Minor fixes and adjustments.

= 3.2 =
* Added a new "Content-Security-Policy" option to the "Firewall Policies > HTTP response headers" section.
* [Pro+ Edition] Added a new feature: "Centralized Logging". It allows you to remotely access the firewall log of all your NinjaFirewall protected websites from one single installation, without having to log in to individual servers to analyse your log data (see our blog for more info about that: http://nin.link/centlog/ ).
* [Pro+ Edition] Added "PUT" and "DELETE" methods to the "NinjaFirewall > Access Control > HTTP Methods" section.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Fixed a bug in the firewall log: blocked threats were not hex-decoded before exporting the log.
* The "X-Content-Type-Options" header will be enabled by default with new installations of NinjaFirewall.
* Updated security rules.
* Minor fixes and adjustments.

= 3.1.8 =
* Updated security rules and improved XSS evasion techniques detection.
* [Pro+ Edition] Fixed a bug where notifications sent or displayed by NinjaFirewall were showing the load balancer IP when an alternate address was defined in the "Access Control > Source IP" section.
* Blocked threats written to the firewall log will be hexencoded, to lower false positives from antivirus scanners.
* Minor fixes and adjustments.

= 3.1.7 =
* Updated security rules.

= 3.1.6 =
* Updated security rules.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.
* Fixed a bug affecting the admin dashboard token.
* Minor fixes and adjustments.

= 3.1.5 =
* Updated security rules.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.

= 3.1.4 =
* Updated security rules to protect against a critical Magento vulnerability (CVE-2016-4010).
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.

= 3.1.3 =
* Updated security rules.
* Minor fixes and adjustments.
* Added a warning in the "Overview" page if a PHP opcode cache is enabled.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.

= 3.1.2 =
* Added an option to select the number of log lines to display (see "Firewall > Security Log > Log Options").
* The "X-XSS-Protection" and "HttpOnly flag" options from the "Firewall Policies" page will be enabled by default with new installations of NinjaFirewall.
* The "Firewall Policies" sanitise options (GET, COOKIE etc) will replace all "<" and ">" characters with their corresponding HTML entities "&lt;" and "&gt;".
* Minor fixes and adjustments.
* [Pro+ Edition] It is possible to exclude multiple files/folders in the "File Guard" options page (multiple values must be comma-separated).
* Updated security rules.

= 3.1.1 =
* Speed improvements. The latest set of security rules was optimized to drastically speed up the firewall engine.
* Tweaked two anti-XSS rules to prevent attempts to bypass them using HTML events inside truncated/unclosed HTML tags. Thanks to Sven Morgenroth for reporting the issue.
* [Pro+ Edition] The File Guard and Live Log functions were moved from the firewall main script to two separate scripts inside the /lib/ folder.
* Updated security rules.
* The MJ12bot user-agent was removed from the firewall blacklist. This bot DOES follow the robots.txt and hence there is no reason to blacklist it.

= 3.1 =
* [Pro+ Edition] Geolocation access control can apply to the whole site or to some specific URLs only (e.g., /script.php etc). See
  the "Firewall > Access Control > Geolocation Access Control > Geolocation should apply to the whole site or specific URLs" option.
* Added an option to the "Firewall Log" page to export the log as a TSV (tab-separated values) text file.
* The "Delete" button from the "Firewall Log" page was moved above the textarea, beside the "Export" new button, and can be used to delete the currently viewed log.
* Fixed a PHP warning in the firewall script.
* Minor fixes.
* Updated security rules.
* We launched NinjaFirewall Referral Program. If you are interested in joining the program, please consult: http://nin.link/referral/

= 3.0.1 =
* Updated security rules.

= 3.0 =
* This is a major update: NinjaFirewall has a brand new, powerful and awesome filtering engine. Please see our blog for a complete description: http://nin.link/sensei/
* Added many new security rules.
* Minor fixes.
* [Pro+ Edition] Updated IPv4/IPv6 GeoIP databases.

EOT;
