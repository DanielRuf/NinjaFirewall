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

function show_help() {

	// ------------------------------------------------------------------
	// Summary > Overview
	if ( $GLOBALS['mid'] == 10 ) {

		echo _('The "Overview" page is NinjaFirewall\'s dashboard. It is very important that you keep an eye on it because any potential warning or error will be printed there.');

	}
	// ------------------------------------------------------------------
	// Summary > Statistics
	elseif ( $GLOBALS['mid'] == 11 ) {

		echo _('Statistics are taken from the current firewall log. They display the number of threats, their level as well as some benchmarks showing the time NinjaFirewall took, in seconds, to proceed each request it has blocked.');

	}
	// ------------------------------------------------------------------
	// Account > Options
	elseif ( $GLOBALS['mid'] == 20 ) {

		echo '<h3><strong>'. _('Login password') .'</strong></h3>';
		echo _('You can change your admin password using this form. It must contains at least 6 characters.');

		echo '<h3><strong>'. _('Contact') .'</strong></h3>';
		echo _('You can change your contact email addresss where NinjaFirewall will send you alerts and notifications.');

		echo '<h3><strong>'. _('Regional Settings') .'</strong></h3>';
		echo _('You can change the timezone (used for email notifications and the firewall log) and select your language.');

		echo '<h3><strong>'. _('Appearance') .'</strong></h3>';
		echo _('You can use your own style sheet to customize NinjaFirewall UI.');

		echo '<h3><strong>'. _('Login') .'</strong></h3>';
		echo '<p>'. _('An email can be sent to you whenever someone logs in to your NinjaFirewall admin console. It is enabled by default.') .'</p>';
		echo '<p>'._('You can force a secure connection to the login page. Ensure that you can access your admin console over HTTPS before enabling this option, otherwise you will lock yourself out.') .'</p>';
		echo '<p>'._('NinjaFirewall can check for available updates each time you log in to the admin dashboard.') .'</p>';

	}
	// ------------------------------------------------------------------
	// Account > License
	elseif ( $GLOBALS['mid'] == 21 ) {

		echo '<h3><strong>'. _('Current License') .'</strong></h3>';

		echo _("Your license is valid until the indicated expiration date. If you don't renew it after this date, NinjaFirewall will keep working and protecting your website as usual, but updates/upgrades will stop. You can renew your license from NinTechNet.com's website 30 days before its expiration date.");

	}
	// ------------------------------------------------------------------
	// Account > Updates
	elseif ( $GLOBALS['mid'] == 22 ) {

		echo '<h3><strong>'. _('Available updates') .'</strong></h3>';

		echo _('After installing NinjaFirewall, all available updates and upgrades can be installed from this page.') .'<br />';
		echo _('You can also follow us on Twitter (@nintechnet) to get immediate notifications about updates.');

	}
	// ------------------------------------------------------------------
	// Firewall > Options
	elseif ( $GLOBALS['mid'] == 30 ) {

		echo '<h3><strong>'. _('General') .'</strong></h3>';

		echo '<p><strong>'._('Firewall protection').':</strong> '. _('This option allows you to disable NinjaFirewall. Your site will remain unprotected until you enable it again.') .
		'<br />'.
		_('Note that if you are using the <code>.htninja</code> file, it will not be disabled. If you want to disable it too, you either need to rename or delete it.') . '</p>';

		echo '<p><strong>'._('Debug mode').':</strong> '. _('In Debug mode, NinjaFirewall will not block or sanitise suspicious requests but will only log them (the firewall log will display <code>DEBUG_ON</code> in the LEVEL column).') .
		'<br />'.
		_('We recommend to run it in Debug Mode for at least 24 hours after installing it on a new site and then to keep an eye on the firewall log during that time. If you notice a false positive in the log, you can simply use NinjaFirewall\'s Rules Editor to disable the security rule that was wrongly triggered.') . '</p>';

		echo '<p><strong>'. _('HTTP error code and blocked user message to return').':</strong> '. _('Lets you customize the HTTP error code returned by NinjaFirewall when blocking a dangerous request and the message to display to the user. You can use any HTML tags and 2 built-in variables:') .'</p>'.
		'<ul>
			<li><code>%%REM_ADDRESS%%</code>:'.' '. _('The blocked user IP.') .'</li>
			<li><code>%%NUM_INCIDENT%%</code>:'.' '. _('The unique incident number as it will appear in the firewall log "INCIDENT" column.') .'</li>
		</ul>';

		echo '<p><strong>'. _('IP anonymization').':</strong> '. _('This option will anonymize IP addresses in the firewall log by removing their last 3 characters. It does not apply to private IP addresses.') .
		'<br />' .
		_('Note that it will affect only IP addresses written to the firewall log after enabling this option.') .'</p>'.

		'<p>'. glyphicon('warning') .'&nbsp;'. _('If you are redirecting events to the syslog server (Logs > Firewall Log), IP addresses will be anonymized too.') .'</p>';

		echo '<h3><strong>'. _('Banned IP addresses') .'</strong></h3>';

		echo '<p><strong>'. _('Ban offending IP').':</strong> '. _('In addition to rejecting the request, NinjaFirewall can also ban the offending IP depending on the level of the severity. If you decide to ban IPs, use the submenu to select the time that IPs will be banned (from 1 to max 999 minutes).') .
		'<br />' .
		_('To unban one or more IPs, select it/them in the listbox and click on the "Save Changes" button.') .' '.	_('By default, IPs are not banned.') .'</p>';

	}
	// ------------------------------------------------------------------
	// Firewall > Policies
	elseif ( $GLOBALS['mid'] == 31 ) {

		echo '<p>'. _('Because NinjaFirewall sits in front of your application, it can hook, scan and sanitise all PHP requests, HTTP variables, headers and IPs before they reach your site: <code>$_GET</code>, <code>$_POST</code>, <code>$_COOKIE</code>, <code>$_REQUEST</code>, <code>$_FILES</code>, <code>$_SERVER</code> in either or both HTTP & HTTPS mode.').
		'<br />'.
		_('Use the options below to enable, disable or to tweak these rules according to your needs.');

		echo '<h3><strong>'. _('Scan and Sanitise') .'</strong></h3>'.
		'<p>'. _('You can choose to scan and reject dangerous content but also to sanitise requests and variables. Those 2 actions are different and can be combined together for better security.').
		'<p><strong>'. _('Scan') .':</strong> '. _('If anything suspicious is detected, NinjaFirewall will block the request and return an HTTP error code and message. The user request will fail and the connection will be closed immediately.') .'</p>'.
		'<p><strong>'. _('Sanitise') .':</strong> '. _('This option will not block but sanitise the user request by escaping characters that can be used to exploit vulnerabilities (<code>\'</code>, <code>"</code>, <code>\</code>, <code>\n</code>, <code>\r</code>, <code>`</code>, <code>\x1a</code>, <code>\x00</code>, <code>*</code> and <code>?</code>) and replacing <code>&lt;</code> and <code>&gt;</code> with their corresponding HTML entities (<code>&amp;lt;</code>, <code>&amp;gt;</code>). If it is a variable, i.e. <code>?name=value</code>, both its name and value will be sanitised.') .' '. _('This action will be performed when the filtering process is over, right before NinjaFirewall forwards the request to your PHP script.') .'</p>';

		echo '<h3><strong>'. _('HTTP / HTTPS') .'</strong></h3>'.
		'<p><strong>'. _('Enable NinjaFirewall for') .':</strong> '. _('Whether to filter HTTP and/or HTTPS traffic.') .'</p>';

		echo '<h3><strong>'. _('Uploads') .'</strong></h3>'.
		'<p><strong>'. _('Allow uploads') .':</strong> '. _('You can allow/disallow uploads, or allow uploads but block scripts (PHP, CGI, Ruby, Python, bash/shell), C/C++ source code, binaries (MZ/PE/NE and ELF formats), system files (<code>.htaccess</code>, <code>.htpasswd</code> and PHP INI) and SVG files containing Javascript/XML events.') .'</p>'.
		'<p><strong>'. _('Sanitise filenames') .':</strong> '. _('Any character that is not a letter <code>a-zA-Z</code>, a digit <code>0-9</code>, a dot <code>.</code>, a hyphen <code>-</code> or an underscore <code>_</code> will be removed from the filename and replaced with the substitution character.') .'</p>'.
		'<p><strong>'. _('Maximum allowed file size') .':</strong> '. _('If you allow uploads, you can select the maximum size of an uploaded file. Any file bigger than this value will be rejected. Note that if your PHP configuration uses the <code>upload_max_filesize</code> directive, it will be used before NinjaFirewall.') .'</p>';

		echo '<hr />';

		echo '<h3><strong>'. _('HTTP GET variable') .'</strong></h3>'.
		'<p>'. sprintf( _('Whether to scan and/or sanitise the %s variable.'), '<code>GET</code>' ) .'</p>';

		echo '<h3><strong>'. _('HTTP POST variable') .'</strong></h3>'.
		'<p>'. sprintf( _('Whether to scan and/or sanitise the %s variable.'), '<code>POST</code>' ) .'</p>'.
		'<p><strong>'. _('Decode base64-encoded POST variable') .':</strong> '. _('NinjaFirewall will decode and scan base64 encoded values in order to detect obfuscated malicious code. This option is only available for the <code>POST</code> variable.') .'</p>';

		echo '<h3><strong>'. _('HTTP REQUEST variable') .'</strong></h3>'.
		'<p>'. sprintf( _('Whether to scan and/or sanitise the %s variable.'), '<code>REQUEST</code>' ) .'</p>';

		echo '<h3><strong>'. _('Cookies') .'</strong></h3>'.
		'<p>'. sprintf( _('Whether to scan and/or sanitise the %s variable.'), '<code>COOKIE</code>' ) .'</p>';

		echo '<h3><strong>'. _('HTTP_USER_AGENT server variable') .'</strong></h3>'.
		'<p>'. sprintf( _('Whether to scan and/or sanitise the %s variable.'), '<code>HTTP_USER_AGENT</code>' ) .'</p>'.
		'<p><strong>'. _('Block POST requests from User-Agents that do not have') .':</strong> '. _('Those 3 options can help to block crawlers, scrappers and spambots.') .'</p>'.
		'<p><strong>'. _('Block suspicious bots/scanners') .' (Pro Edition):</strong> '. _('This option will block crawlers, scrappers and spambots.') .'</p>';

		echo '<h3><strong>'. _('HTTP_REFERER server variable') .'</strong></h3>'.
		'<p>'. sprintf( _('Whether to scan and/or sanitise the %s variable.'), '<code>HTTP_REFERER</code>' ) .'</p>'.
		'<p><strong>'. _('Block POST requests that do not have an HTTP_REFERER header') .':</strong> '. _('This option will block any <code>POST</code> request that does not have a referrer header (<code>HTTP_REFERER</code> variable). If you need external applications to post to your scripts (e.g., Paypal IPN etc), you are advised to keep this option disabled otherwise they will likely be blocked (unless you added them to your IP or URL Access Control whitelist).') .'</p>';

		echo '<h3><strong>'. _('HTTP response headers') .'</strong></h3>'.
		'<p>'. _('In addition to filtering incoming requests, NinjaFirewall can also hook the HTTP response in order to alter its headers. Those modifications can help to mitigate threats such as XSS, phishing and clickjacking attacks.'). '</p>'.

		'<p><strong>'. _('Set X-Content-Type-Options to protect against MIME type confusion attacks') .':</strong> '. _('This header will send the nosniff value to instruct the browser to disable content or MIME sniffing and to use the content-type returned by the server. Some browsers try to guess (sniff) and override the content-type by looking at the content itself which, in some cases, could lead to security issues such as MIME Confusion Attacks.') .'</p>'.

		'<p><strong>'. _('Set X-Frame-Options to protect against clickjacking attempts') .':</strong> '. _('This header indicates a policy whether a browser must not allow to render a page in a <code>&lt;frame&gt;</code> or <code>&lt;iframe&gt;</code>. Hosts can declare this policy in the header of their HTTP responses to prevent clickjacking attacks, by ensuring that their content is not embedded into other pages or frames. NinjaFirewall accepts two different values:') .'</p>'.
		'<ul>'.
			'<li>'. _('<code>SAMEORIGIN</code>: A browser receiving content with this header must not display this content in any frame from a page of different origin than the content itself.') .'</li>'.
			'<li>'. _('<code>DENY</code>: A browser receiving content with this header must not display this content in any frame.') .'</li>'.
		'</ul>'.

		'<p><strong>'. _('Set X-XSS-Protection (IE/Edge, Chrome, Opera and Safari browsers)') .':</strong> '. _('This header allows browsers to identify and block XSS attacks by preventing malicious scripts from executing. It is enabled by default on all compatible browsers.') .'</p>'.
		'<p>'. glyphicon('warning') .'&nbsp;'. _('If a visitor disable their browser\'s XSS filter, you cannot re-enable it with that option.') .'</p>'.

		'<p><strong>'. _('Force HttpOnly flag on all cookies to mitigate XSS attacks') .':</strong> '. _('Adding this flag to cookies helps to mitigate the risk of cross-site scripting by preventing them from being accessed through client-side scripts. NinjaFirewall can hook all cookies sent by your blog, its plugins or any other PHP script, add the <code>HttpOnly</code> flag if it is missing, and re-inject those cookies back into your server HTTP response headers right before they are sent to your visitors.') .'</p>'.
		'<p>'. glyphicon('warning') .'&nbsp;'. _('If your PHP scripts send cookies that need to be accessed from JavaScript, you should keep that option disabled.') .'</p>'.

		'<p><strong>'. _('Set Strict-Transport-Security (HSTS) to enforce secure connections to the server') .':</strong> '. _('This policy enforces secure HTTPS connections to the server. Web browsers will not allow the user to access the web application over insecure HTTP protocol. It helps to defend against cookie hijacking and Man-in-the-middle attacks. Most recent browsers support HSTS headers.') .'</p>'.

		'<p><strong>'. _('Set Content-Security-Policy') .':</strong> '. _('This policy helps to mitigate threats such as XSS, phishing and clickjacking attacks. It covers JavaScript, CSS, HTML frames, web workers, fonts, images, objects (Java, ActiveX, audio and video files), and other HTML5 features.') .'</p>'.

		'<p><strong>'. _('Set Referrer-Policy') .':</strong> '. _('This HTTP header governs which referrer information, sent in the Referer header, should be included with requests made.') .'</p>';

		echo '<h3><strong>'. _('PHP') .'</strong></h3>'.
		'<p><strong>'. _('Block PHP built-in wrappers in GET, POST, HTTP_USER_AGENT, HTTP_REFERER and cookies') .':</strong> '. _('PHP has several wrappers for use with the filesystem functions. It is possible for an attacker to use them to bypass firewalls and various IDS to exploit remote and local file inclusions. This option lets you block any script attempting to pass a <code>expect://</code>, <code>file://</code>, <code>phar://</code>, <code>php://</code>, <code>zip://</code> or <code>data://</code> stream inside a <code>GET</code> or <code>POST</code> request, cookies, user agent and referrer variables.') .'</p>'.

		'<p><strong>'. _('Block serialized PHP objects in the following global variables') .':</strong> '. _('Object Serialization is a PHP feature used by many applications to generate a storable representation of a value. However, some insecure PHP applications and plugins can turn that feature into a critical vulnerability called PHP Object Injection. This option can block serialized PHP objects found inside a <code>GET</code> or <code>POST</code> request, cookies, user agent and referrer variables.') .'</p>'.

		'<p><strong>'. _('Hide PHP notice and error messages') .':</strong> '. _('This option lets you hide errors returned by your scripts. Such errors can leak sensitive informations which can be exploited by hackers.') .'</p>'.

		'<p><strong>'. _('Sanitise PHP_SELF, PATH_TRANSLATED, PATH_INFO') .':</strong> '. _('This option can sanitise any dangerous characters found in those 3 server variables to prevent various XSS and database injection attempts.') .'</p>';

		echo '<h3><strong>'. _('Various') .'</strong></h3>'.
		'<p><strong>'. _('Block the DOCUMENT_ROOT server variable in HTTP request') .':</strong> '. _('This option will block scripts attempting to pass the <code>DOCUMENT_ROOT</code> server variable in a <code>GET</code> or <code>POST</code> request. Hackers use shell scripts that often need to pass this value, but most legitimate programs do not.') .'</p>'.

		'<p><strong>'. _('Block ASCII character 0x00 (NULL byte)') .':</strong> '. _('This option will reject any <code>GET</code> or <code>POST</code> request, <code>HTTP_USER_AGENT</code>, <code>REQUEST_URI</code>, <code>PHP_SELF</code>, <code>PATH_INFO</code>, <code>HTTP_REFERER</code> variables containing the ASCII character 0x00 (NULL byte). Such a character is dangerous and should always be rejected.') .'</p>'.

		'<p><strong>'. _('Block ASCII control characters 1 to 8 and 14 to 31') .':</strong> '. _('This option will reject any <code>GET</code> or <code>POST</code> request, <code>HTTP_USER_AGENT</code>, <code>HTTP_REFERER</code> variables containing ASCII characters from 1 to 8 and 14 to 31.') .'</p>'.

		'<p><strong>'. _('Block localhost IP in GET/POST request') .':</strong> '. _('This option will block any <code>GET</code> or <code>POST</code> request containing the localhost IP. It can be useful to block SQL dumpers and various hackers shell scripts.') .'</p>'.

		'<p><strong>'. _('Block HTTP requests with an IP in the HTTP_HOST header') .':</strong> '. _('This option will reject any request using an IP instead of a domain name in the <code>Host</code> header of the HTTP request. Unless you need to connect to your site using its IP address, (e.g. http://172.16.0.1/index.php), enabling this option will block a lot of hackers scanners because such applications scan IPs rather than domain names.') .'</p>'.

		'<p><strong>'. _('Accept the following HTTP methods') .':</strong> '. _('By default, NinjaFirewall will accept all HTTP methods.') .'</p>';

	}
	// ------------------------------------------------------------------
	// Firewall > Access Control
	elseif ( $GLOBALS['mid'] == 32 ) {

		echo _('Access Control is a powerful set of directives that can be used to allow or restrict access to your website based on many criteria.') .' '.
		_('To make better use of them, it is important to understand NinjaFirewall\'s directives processing order.').
		'<br />'.
		_('Because NinjaFirewall is a PHP firewall, its Access Control options apply to PHP scripts, not to static elements (e.g., images, JS, CSS etc). Depending on your configuration, they can also apply to HTML pages.').
		'<br /><br />'.
		'<p>'. _('Processing order:') .'</p>'.
		'<p><strong>'. _('Incoming HTTP request:') .'</strong></p>'.
		'<ol>'.
			'<li>'. _('The <code>.htninja</code> file.') .'</li>'.
			'<li>'. _('Temporarily  banned IP addresses.') .'</li>'.
			'<li style="color:red">'. _('Access Control:') .'</li>'.
			'<ol>'.
				'<li>'. _('Allowed IPs') .'.</li>'.
				'<li>'. _('Blocked IPs') .'.</li>'.
				'<li>'. _('Allowed URLs') .'.</li>'.
				'<li>'. _('Blocked URLs') .'.</li>'.
				'<li>'. _('Bot Access Control') .'.</li>'.
				'<li>'. _('Geolocation') .'.</li>'.
				'<li>'. _('Rate Limiting') .'.</li>'.
			'</ol>'.
			'<li>'. _('File Guard') .'.</li>'.
			'<li>'. _('NinjaFirewall built-in rules and policies') .'.</li>'.
		'</ol>'.
		'<p><strong>'. _('Response body:') .'</strong></p>'.
		'<ol>'.
			'<li>'. _('HTTP response headers') .'.</li>'.
			'<li>'. _('Web Filter') .'.</li>'.
		'</ol>';

		echo '<hr />';

		echo '<h3><strong>'. _('Administrator') .'</strong></h3>'.
		'<p><strong>'. _('Whitelist the Administrator') .':</strong> '. _('When enabling this option, NinjaFirewall will whitelist you with a browser cookie (valid for one-year) so that you will never be blocked by the firewall. You will remain whitelisted even if you log out of the administration console.') .'</p>';

		echo '<h3><strong>'. _('Source IP') .'</strong></h3>'.
		'<p><strong>'. _('Retrieve visitors IP address from') .':</strong> '. _('This option should be used if you are behind a reverse proxy, a load balancer or using a CDN, in order to tell NinjaFirewall which IP it should use. By default, it will rely on <code>REMOTE_ADDR</code>. If you want it to use <code>HTTP_X_FORWARDED_FOR</code> or any other similar variable, it is absolutely necessary to ensure that it is reliable (i.e., setup by your own load balancer/reverse proxy) because it can be easily spoofed. If that variable includes more than one IP, only the left-most (the original client) will be checked. If it does not include any IP, NinjaFirewall will fall back to <code>REMOTE_ADDR</code>.'). '</p>'.
		'<p><strong>'. _('Scan traffic coming from localhost and private IP address spaces') .':</strong> '. _('This option will allow the firewall to scan traffic from all non-routable private IPs (IPv4 and IPv6) as well as the localhost IP. We recommend to keep it enabled if you have a private network (2 or more servers interconnected).') .'</p>';

		echo '<h3><strong>'. _('HTTP Methods') .'</strong></h3>'.
		'<p><strong>'. _('All Access Control directives should apply to the following HTTP methods') .':</strong> '. _('This option lets you select the HTTP method(s). All Access Control directives (Geolocation, IPs, bots and URLs) will only apply to the selected methods. It does not apply to the Firewall Policies options, which use their own ones.') .'</p>';

		echo '<hr />';

		echo '<h3><strong>'. _('Geolocation Access Control') .'</strong></h3>'.
		'<p><strong>'. _('Retrieve ISO 3166 country code from') .':</strong> '. _('This is the two-letter country code that is used to define a country (e.g., US, UK, FR, DE etc), based on the visitors IP. NinjaFirewall can either retrieve it from its database, or from a predefined PHP variable added by your HTTP server (e.g., <code>GEOIP_COUNTRY_CODE</code>).') .'</p>'.

		'<p><strong>'. _('Available countries') .':</strong> '. sprintf( _('You can add/remove any country from the two listboxes. For more information about some specific ISO 3166 codes (A1, A2, AP, EU etc), you may want to consult the <a href="%s">MaxMind GeoIP online help</a>. By default, no country is blocked.'), 'https://dev.maxmind.com/geoip/legacy/codes/iso3166/' ) .'</p>'.

		'<p><strong>'. _('Geolocation should apply to the whole site or specific URLs only?') .':</strong> '. _('whether geolocation should apply to the every PHP scripts on your website or to specific URLs only (e.g., <code>/foobar.php</code>). Leave all fields empty if you want it to apply to the whole site.') .'</p>'.

		'<p><strong>'. _('Add NINJA_COUNTRY_CODE to PHP headers') .':</strong> '. _('After retrieving the two-letter country code, NinjaFirewall can add it to the PHP headers in the <code>$_SERVER["NINJA_COUNTRY_CODE"]</code> server variable. If you have an application, theme or a plugin that needs to know your visitors location, simply use that variable.') .'</p>'.
		_('PHP code example to use in your application to geolocate your visitors:') .
		'<br />'.
		'<center>'.
			'<textarea class="form-control" style="height:120px;font-family:monospace;" wrap="off">'.
				'if (! empty($_SERVER["NINJA_COUNTRY_CODE"]) &&' ."\n".
				'   $_SERVER["NINJA_COUNTRY_CODE"] != "--" ) {' ."\n\n".
				'   echo "'. _('Your country code is:') .'" $_SERVER["NINJA_COUNTRY_CODE"];' ."\n".
				'}'.
			'</textarea>'.
		'</center>'.
		'<br />';

		echo '<p>'. glyphicon('warning') .'&nbsp;'. _('If NinjaFirewall cannot find the two-letter country code, it will replace it with two hyphens (<code>--</code>).') .'</p>';

		echo '<hr />';

		echo '<h3><strong>'. _('IP Access Control') .'</strong></h3>';
		echo _('You can permanently allow/block an IP, a part of it or a whole range of IP addresses. IPv4 and IPv6 are fully supported by NinjaFirewall.') .
		' '. _('You must at least enter the first 3 characters of an IP:').
		'<br /><br />'.
		'<strong>'. _('Full IP:') .'</strong>'.
		'<ul>'.
			'<li>'. _('<code>1.2.3.123</code> will only match IP <code><font color="red">1.2.3.123</font></code>') .'</li>'.
		'</ul>'.
		'<p><strong>'. _('Partial IP:') .'</strong></p>'.
		'<ul>'.
			'<li>'. _('<code>1.2.3.</code> will match any IP address starting with <code>1.2.3.</code> (from <code><font color="red">1.2.3.</font>0</code> to <code><font color="red">1.2.3.</font>255</code>), but will not match <code>2<font color="red">1.2.3.</font>0</code>') .'</li>'.
			'<li>'. _('<code>1.2.3</code> will match any IP address starting with <code>1.2.3</code> (from <code><font color="red">1.2.3</font>.0</code> to <code><font color="red">1.2.3</font>.255</code>, and also <code><font color="red">1.2.3</font>4.56</code> etc), but will not match <code>4.<font color="red">1.2.3</font></code>'). '</li>'.
		'</ul>'.
		'<strong>'. _('IP ranges using CIDR notation:') .'</strong>'.
		'<ul>'.
			'<li>'. sprintf( _('%s or %s.'), '<code>66.155.0.0/17</code>', '<code>2c0f:f248::/32</code>' ) .'</li>'.
		'</ul>'.

		'<p><strong>'. _('Rate limiting') .':</strong> '. _('This option allows you to slow down aggressive bots, crawlers, web scrapers or even small attacks. Any IP reaching the defined threshold will be banned from 1 to 999 seconds. Note that the purpose of this feature is not to permanently block an IP but rather to temporarily prevent it from accessing the site and abusing your system resources. If you want to permanently block an IP, use the blacklist instead. By default, Rate Limiting is turned off.') .'</p>'.
		'<p>'. glyphicon('warning') .'&nbsp;'. _('IPs temporarily banned by the Rate Limiting option can be unblocked immediately by clicking either the "Save Changes" or "Restore Default Values" buttons at the bottom of this page.') .'</p>';

		echo '<hr />';

		echo '<h3><strong>'. _('URL Access Control') .'</strong></h3>';
		echo '<p>'. _('You can permanently allow or block any access to one or more PHP scripts based on their path, relative to the web root (<code>SCRIPT_NAME</code>). You can enter either a full or partial path (case-sensitive).') .'</p>'.
		'<ul>'.
			'<li>'. _('<code>/foo/bar.php</code> will allow/block any access to the <code>bar.php</code> script located inside a <code>/foo/</code> directory (<code>http://domain.tld/foo/bar.php</code>, <code>http://domain.tld/another/directory/foo/bar.php</code> etc).') .'</li>'.
			'<li>'. _('<code>/foo/</code> will allow/block access to all PHP scripts located inside a <code>/foo/</code> directory.') .'</li>'.
		'</ul>';

		echo '<hr />';

		echo '<h3><strong>'. _('Bot Access Control') .'</strong></h3>';
		echo '<p>'. _('You can block bots, scanners and various crawlers based on the <code>HTTP_USER_AGENT</code> variable. You can enter either a full or partial name (case-insensitive).') .'</p>';

		echo '<hr />';

		echo '<h3><strong>'. _('Log events') .'</strong></h3>';
		echo '<p>'. _('You can enable/disable firewall logging for each access control directive separately.') .'</p>';

		echo '<hr />';

		echo '<i style="color:grey">'. _('NinjaFirewall includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com">http://www.maxmind.com</a>.') .'</i>';

	}
	// ------------------------------------------------------------------
	// Firewall > Rules Editor
	elseif ( $GLOBALS['mid'] == 35 ) {

		echo '<p>' ._('In addition to the Firewall Policies and Access Control directives, NinjaFirewall includes also a large set of built-in rules used to protect your site against the most common vulnerabilities and hacking attempts. They are always enabled and you cannot edit them, but if you notice that your visitors are wrongly blocked by some of those rules, you can use the Rules Editor to disable them individually:'). '</p>'.
		'<ul>'.
			'<li>'. _('Check your firewall log and find the rule ID you want to disable (it is displayed in the <code>RULE</code> column).'). '</li>'.
			'<li>'. _('Select its ID from the enabled rules list below and click the "Disable it" button.'). '</li>'.
		'</ul>'.
		'<p>'. glyphicon('warning') .'&nbsp;'. _('If the <code>RULE</code> column from your log shows a hyphen <code>-</code> instead of a number, that means that the rule can be changed in your Firewall Policies page.'). '</p>';

	}
	// ------------------------------------------------------------------
	// Monitoring > File Guard
	elseif ( $GLOBALS['mid'] == 33 ) {

		echo '<p>'. _('File Guard can detect, in real-time, any access to a PHP script that was recently modified or created, and alert you about this.') .'</p>'.

		'<p>'. _('If a hacker uploaded a shell script to your site (or injected a backdoor into an already existing file) and tried to directly access that file using his browser or a script, NinjaFirewall would hook the HTTP request and immediately detect that the file was recently modified/created. It would send you a detailed alert (script name, IP, request, date and time). Alerts will be sent to the contact email address defined in the "Account &gt; Options" menu.') .'</p>'.

		'<p>'. _('Modifications detected by NinjaFirewall include file modifications as well as file permissions, and ownership changes.') .'</p>'.

		'<p>'. _('If you do not want to monitor a folder, you can exclude its full path or a part of it (e.g., <code>/var/www/public_html/cache/</code>, <code>/cache/</code> etc). NinjaFirewall will compare this value to the <code>$_SERVER["SCRIPT_FILENAME"]</code> server variable and, if it matches, will ignore it. Multiple values must be comma-separated.') .'</p>';

	}
	// ------------------------------------------------------------------
	// Monitoring > File Check
	elseif ( $GLOBALS['mid'] == 38 ) {

		echo '<p>'. _('File Check lets you perform file integrity monitoring upon request.') .
		'<br />'.
		_('You need to create a snapshot of all your files and then, at a later time, you can scan your system to compare it with the previous snapshot. Any modification will be immediately detected: file content, file permissions, file ownership, timestamp as well as file creation and deletion.') .'</p>';

		echo '<p><strong>'. _('Create a snapshot of all files stored in that directory') .':</strong> '. _('By default, the directory is set to NinjaFirewall\'s parent directory.') .'</p>';

		echo '<p><strong>'. _('Exclude the following files/folders').':</strong> '. _('You can enter a directory or a file name (e.g., <code>/foo/bar/</code>), or a part of it (e.g., <code>foo</code>). Or you can exclude a file extension (e.g., <code>.css</code>). Multiple values must be comma-separated (e.g., <code>/foo/bar/,.css,.png</code>).') .'</p>';

		echo '<p><strong>'. _('Do not follow symbolic links').':</strong> '. _('By default, NinjaFirewall will not follow symbolic links.') .'</p>';

	}
	// ------------------------------------------------------------------
	// Monitoring > Web Filter
	elseif ( $GLOBALS['mid'] == 34 ) {

		echo _('If NinjaFirewall can hook and scan incoming requests, it can also hook the response body (i.e., the output of the HTML page right before it is sent to your visitors browser) and search it for some specific keywords. Such a filter can be useful to detect hacking or malware patterns injected into your HTML code (text strings, spam links, malicious JavaScript code), hackers shell script, redirections and even errors (PHP/MySQL errors). Some suggested keywords are included.').
		'<br />'.
		_('In the case of a positive detection, NinjaFirewall will not block the response body but will send you an alert by email.') .
		'<br /><br />'.
		'<p><strong>'. _('Search HTML page for:') .'</strong> '. _('You can enter any keyword from 4 to 150 characters and select whether the search will be case sensitive or not.'). '</p>'.
		'<p><strong>'. _('Email Alerts') .':</strong> '. _('You can use the notification throttling option to limit the frequency of alerts sent to you (and written to the firewall log) and select whether you want NinjaFirewall to send you the whole HTML source of the page where the keyword was found. Alerts will be sent to the contact email address defined in the "Account &gt; Options" menu.').'</p>';

		echo '<p>'. glyphicon('warning') .'&nbsp;'. _('Response body filtering can be resource-intensive. Try to limit the number of keywords to what you really need (&lt;10) and/or, if possible, prefer case sensitive to case insensitive filtering.'). '</p>';

	}
	// ------------------------------------------------------------------
	// Logs > Firewall Log
	elseif ( $GLOBALS['mid'] == 36 ) {

		echo '<p>'. _('The firewall log displays blocked and sanitised requests as well as some useful information. It has 6 columns:') .'</p>'.
		'<ul>'.
		'<li><code>DATE</code>: '. _('Date and time of the incident.') .'</li>'.
		'<li><code>INCIDENT</code>: '. _('Unique incident number/ID as it was displayed to the blocked user.') .'</li>'.
		'<li><code>LEVEL</code>: '. _('Level of severity (medium, high or critical), information (info, error, upload) and debugging mode (DEBUG_ON).') .'</li>'.
		'<li><code>RULE</code>: '. _('Reference of the NinjaFirewall built-in security rule that triggered the action. A hyphen (<code>-</code>) instead of a number means it was a rule from your own Firewall Policies or Access Control page.') .'</li>'.
		'<li><code>IP</code>: '. _('The blocked user remote address.') .'</li>'.
		'<li><code>REQUEST</code>: '. _('The HTTP request including offending variables & values as well as the reason the action was logged.') .'</li>'.
		'</ul>'.
		'<p>'. _('The log can also be exported as a TSV (tab-separated values) text file.') .'</p>';

		echo '<h3><strong>'. _('Options') .'</strong></h3>'.
		'<p><strong>'. _('Enable firewall log') .':</strong> '. _('You can disable/enable the firewall log from this page.'). '</p>'.
		'<p><strong>'. _('Auto-rotate log') .':</strong> '. _('NinjaFirewall will rotate its log automatically on the very first day of each month. If your site is very busy, you may want to allow it to rotate the log when it reaches a certain size (MB) as well. By default, if will rotate the log each month or earlier, if it reaches 2 megabytes.').
		'<br />'.
		_('Rotated logs, if any, can be selected and viewed from the dropdown menu.'). '</p>'.

		'<p><strong>'. _('Show the most recent') .':</strong> '. _('If the log is too large, only the last 1500 lines will be displayed. It can be changed with this option.'). '</p>';

		echo '<p><strong>'. _('Syslog') .':</strong> '. _('In addition to the firewall log, events can also be redirected to the syslog server (<code>LOG_USER</code> facility). If you have a shared hosting account, keep this option disabled as you do not have any access to the server logs.'). '</p>'.
		'<p>'. _('The logline uses the following format:') .'</p>'.
		'<p><code>ninjafirewall[<font color="red">AA</font>]: <font color="red">BB</font>: #<font color="red">CCCCCC</font>: <i>Some event</i> from <font color="red">DD</font> on <font color="red">EE</font></code><p>'.
		'<ul>'.
			'<li>AA: '. _('the process ID (PID).') .'</li>'.
			'<li>BB: '. _('the level of severity as it appears in the firewall log. It can be <code>CRITICAL</CODE>, <CODE>HIGH</CODE>, <CODE>MEDIUM</CODE>, <CODE>INFO</CODE>, <CODE>UPLOAD</CODE> or <CODE>DEBUG_ON</CODE>.') .'</li>'.
			'<li>CCCCCC: '. _('the 7-digit incident ID.') .'</li>'.
			'<li>DD: '. _('the user IPv4 or IPv6 address.') .'</li>'.
			'<li>EE: '. _('the website (sub-)domain name.') .'</li>'.
		'</ul>'.
		'Sample loglines:'.
		'<br />'.
		'<center>'.
			'<textarea class="form-control" style="height:120px;font-family:monospace;" wrap="off">Oct  3 01:53:51 www ninjafirewall[19054]: INFO: #2498192: Logged in administrator from 12.24.56.78 on somesite.com
Oct  3 02:01:56 www ninjafirewall[19054]: INFO: #1522694: Firewall log deleted by admin from 12.24.56.78 on somesite.com
Oct  3 14:02:20 www ninjafirewall[18270]: HIGH: #7167442: Cross-site scripting from fe80::6e88:14ff:fe3e:86f0 on anothersite.com
Oct  3 15:40:48 www ninjafirewall[19058]: CRITICAL: #2601781: ASCII character 0x00 (NULL byte) from fe80::6e88:14ff:fe3e:86f0 on anothersite.com</textarea>'.
		'</center>';

		echo '<h3><strong>'. _('Centralized Logging') .'</strong></h3>'.
		'<p>'. _('Centralized Logging lets you remotely access the firewall log of all your NinjaFirewall protected websites from one single installation. You do not need any more to log in to individual servers to analyse your log data.') .'</p>'.
		'<p><strong>'. _('Enter your public key (optional)') .':</strong> '. _('This is the public key that was created from your main server.'). '</p>'.
		'<p>'. glyphicon('warning') .'&nbsp;'. _('Centralized Logging will keep working even if NinjaFirewall is disabled. Delete your public key if you want to disable it.'). '</p>';

	}
	// ------------------------------------------------------------------
	// Logs > Live Log
	elseif ( $GLOBALS['mid'] == 37 ) {

		echo '<p>'. _('Live Log lets you watch your blog traffic in real time, just like the Unix <code>tail -f</code> command. Note that requests sent to static elements like JS/CSS files and images are not managed by NinjaFirewall.') .'</p>'.
		'<p>'. _('You can enable/disable the monitoring process, change the refresh rate, clear the screen, enable automatic vertical scrolling, change the log format and select which traffic you want to view (HTTP/HTTPS).') .' '. _('You can also apply filters to include or exclude files and folders (REQUEST_URI).') .'</p>'.

		'<p><strong>'. _('Log Format'). '</strong>: '.
		'<p>'. _('You can easily customize the log format. Possible values are:') .'</p>'.
		'<ul>'.
			'<li><code>%time</code>: '. _('The server date, time and timezone.') .'</li>'.
			'<li><code>%name</code>: '. _('Authenticated user (HTTP basic auth), if any.') .'</li>'.
			'<li><code>%client</code>: '. _('The client REMOTE_ADDR. If you are behind a load balancer or CDN, this will be its IP.') .'</li>'.
			'<li><code>%method</code>: '. _('HTTP method (e.g., GET, POST).') .'</li>'.
			'<li><code>%uri</code>: '. _('The URI which was given in order to access the page (REQUEST_URI).') .'</li>'.
			'<li><code>%referrer</code>: '. _('The referrer (HTTP_REFERER), if any.') .'</li>'.
			'<li><code>%ua</code>: '. _('The user-agent (HTTP_USER_AGENT), if any.') .'</li>'.
			'<li><code>%forward</code>: '. _('HTTP_X_FORWARDED_FOR, if any. If you are behind a load balancer or CDN, this will likely be the visitor true IP.') .'</li>'.
			'<li><code>%host</code>: the requested host (HTTP_HOST), if any.</li>'.
		'</ul>'.
		_('Additionally, you can include any of the following characters:') .' <code>"</code>, <code>%</code>, <code>[</code>, <code>]</code>, <code>space</code> and lowercase letters <code>a-z</code>.'.

		'<p>'. glyphicon('warning') .'&nbsp;'. _('If you are using the optional <code>.htninja</code> configuration file to whitelist your IP, the Live Log feature will not work.') .'</p>';

	}
	// ------------------------------------------------------------------
	// Logs > Centralized Logging
	elseif ( $GLOBALS['mid'] == 39 ) {

		echo '<p>'. _('Centralized Logging lets you remotely access the firewall log of all your NinjaFirewall protected websites from one single installation. You do not need any more to log in to individual servers to analyse your log data. There is no limit to the number of websites you can connect to, and they can be running any edition of NinjaFirewall: WP, WP+, Pro or Pro+.') .'</p>'.

		'<p><strong>'. _('Secret key'). ':</strong> '. _('The secret key will be used to generate your public key. Enter at least 30 ASCII characters, or use the one randomly created by NinjaFirewall.') .'</p>'.

		'<p><strong>'. _('This server IP address'). ':</strong> '. _('As an additional protection layer, you can restrict access to the remote website(s) to the main server\'s IP only. You can use IPv4 or IPv6. If you do not want any IP restriction, enter the <code>*</code> character instead.') .'</p>'.

		'<p><strong>'. _('Public key'). ':</strong> '. _('This is the public key that you will need to upload to each remote website.') .'</p>'.

		'<p><strong>'. _('Remote websites URL'). ':</strong> '. _('Enter the full URL of your NinjaFirewall protected website(s) that you want to remotely access from the main server.') .'</p>'.

		'<p>'. glyphicon('warning') .'&nbsp;'. _('Centralized Logging will keep working even if NinjaFirewall is disabled. Use this menu if you want to disable it.'). '</p>';

	}
	// ------------------------------------------------------------------
	// Error?
	else {
		echo _('No help available for this page.');
	}
}
// ---------------------------------------------------------------------
// EOF
