<?php
$changelog = <<<'EOT'

= 3.1.1 =
* Speed improvements. The latest set of security rules was optimized to drastically speed up the firewall engine.
* Tweaked two anti-XSS rules to prevent attempts to bypass them using HTML events inside truncated/unclosed HTML tags. Thanks to Sven Morgenroth for reporting the issue.
* [Pro+ Edition] The File Guard and Live Log functions were moved from the firewall main script to two separate scripts inside the /lib/ folder.
* Updated security rules.
* The MJ12bot user-agent was removed from the firewall blacklist. This bot DOES follow the robots.txt and hence there is no reason to blacklist it by default.

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
