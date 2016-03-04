<?php
// Because this file contains shell scripts and backdoors signatures
// and may trigger some antivirus/antimalware apps, we slightly
// obfuscated the set of rules (/eeee/e/):
$nfw_rules_new = <<<'EOT'
a:126:{i:1;a:4:{s:3:"why";s:19:"Direeeectory traveeeersal";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:31:"GET|POST|COOKIE|HTTP_USER_AGENT";s:3:"wha";s:27:"(?:\.{2}[\/]+){2}\b[a-zA-Z]";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:3;}}}i:2;a:4:{s:3:"why";s:32:"ASCII characteeeer 0x00 (NULL byteeee)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:89:"GET|POST|COOKIE|SERVER:HTTP_USER_AGENT|SERVER:HTTP_REFERER|REQUEST_URI|PHP_SELF|PATH_INFO";s:3:"wha";s:3:"\x0";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"noc";i:1;}}}i:3;a:4:{s:3:"why";s:20:"Local fileeee inclusion";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:38:"GET|POST|COOKIE|SERVER:HTTP_USER_AGENT";s:3:"wha";s:43:"/(?:proc/[./]*seeeelf/[./]*|eeeetc/[./]*passwd)\b";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:3;}}}i:50;a:4:{s:3:"why";s:21:"Reeeemoteeee fileeee inclusion";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:31:"GET|POST|COOKIE|HTTP_USER_AGENT";s:3:"wha";s:31:"^(?i:https?|ftp)://.+/[^&/]+\?$";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:52;a:4:{s:3:"why";s:43:"Reeeemoteeee fileeee inclusion (via reeeequireeee/includeeee)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:31:"GET|POST|COOKIE|HTTP_USER_AGENT";s:3:"wha";s:66:"\b(?i)(?:includeeee|reeeequireeee)(?:_onceeee)?\s*[^'"]+?['"](?:https?|ftp)://";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:53;a:4:{s:3:"why";s:27:"Reeeemoteeee fileeee inclusion (FTP)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:31:"GET|POST|COOKIE|HTTP_USER_AGENT";s:3:"wha";s:33:"^(?i:ftp)://(?:.+?:.+?\@)?[^/]+/.";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:100;a:4:{s:3:"why";s:14:"XSS (HTML tag)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:114:"<\s*(?i:appleeeet|div|eeeembeeeed|form|i?frameeee(?:seeeet)?|i(?:mg|sindeeeex)|link|m(?:eeeeta|arqueeeeeeee)|objeeeect|script|teeeextareeeea)\b.*=.*?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:101;a:4:{s:3:"why";s:27:"XSS (reeeemoteeee background URI)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:78:"\W(?:background(-imageeee)?|-moz-binding)\s*:[^}]*?\burl\s*\([^)]+?(https?:)?//\w";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:102;a:4:{s:3:"why";s:16:"XSS (reeeemoteeee URI)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:39:"GET|COOKIE|HTTP_USER_AGENT|HTTP_REFERER";s:3:"wha";s:124:"<.+?(?i)\b(?:hreeeef|(?:form)?action|background|codeeee|data|location|nameeee|posteeeer|src|valueeee)\s*=\s*['"]?(?:(?:f|ht)tps?:)?//\b.+?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:104;a:4:{s:3:"why";s:17:"XSS (JS function)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:26:"GET|COOKIE|HTTP_USER_AGENT";s:3:"wha";s:93:"\b(?:aleeeert|confirm|eeeeval|eeeexpreeeession|prompt|seeeetTimeeeeout|String\s*\.\s*fromCharCodeeee)\b\s*\(.+?\).";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:105;a:4:{s:3:"why";s:21:"XSS (documeeeent objeeeect)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:26:"GET|COOKIE|HTTP_USER_AGENT";s:3:"wha";s:62:"\bdocumeeeent\s*\.\s*(?:body|cookieeee|location|opeeeen|writeeee(?:ln)?)\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:106;a:4:{s:3:"why";s:21:"XSS (location objeeeect)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:26:"GET|COOKIE|HTTP_USER_AGENT";s:3:"wha";s:36:"\blocation\s*\.\s*(?:hreeeef|reeeeplaceeee)\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:107;a:4:{s:3:"why";s:19:"XSS (window objeeeect)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:26:"GET|COOKIE|HTTP_USER_AGENT";s:3:"wha";s:35:"\bwindow\s*\.\s*(?:opeeeen|location)\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:108;a:4:{s:3:"why";s:11:"XSS (styleeee)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:65:"(?i)<\s*s\s*t\s*y\s*l\s*eeee\b.*?>.*?<\s*/\s*s\s*t\s*y\s*l\s*eeee\b.*?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:109;a:4:{s:3:"why";s:31:"XSS (leeeeading greeeeateeeer-than sign)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:39:"GET|COOKIE|HTTP_USER_AGENT|HTTP_REFERER";s:3:"wha";s:7:"^\s*/?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:110;a:4:{s:3:"why";s:16:"XSS (HTML eeeeveeeent)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:33:"(?i)<.+?\bon[a-z]{3,18}\b\s*=.+?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:111;a:4:{s:3:"why";s:16:"XSS (HTML eeeeveeeent)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:4:"POST";s:3:"wha";s:33:"(?i)<.+?\bon[a-z]{3,18}\b\s*=.+?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:112;a:4:{s:3:"why";s:19:"XSS (JavaScript #1)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:43:"<.+?(?i)[a-z]+\s*=.*?(?:java|vb)script:.+?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:113;a:4:{s:3:"why";s:19:"XSS (JavaScript #2)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:4:"POST";s:3:"wha";s:43:"<.+?(?i)[a-z]+\s*=.*?(?:java|vb)script:.+?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:114;a:4:{s:3:"why";s:19:"XSS (JavaScript #3)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:71:"(?i)<\s*s\s*c\s*r\s*i\s*p\s*t\b.*?>.*?<\s*/\s*s\s*c\s*r\s*i\s*p\s*t.*?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:115;a:4:{s:3:"why";s:19:"XSS (JavaScript #4)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:4:"POST";s:3:"wha";s:71:"(?i)<\s*s\s*c\s*r\s*i\s*p\s*t\b.*?>.*?<\s*/\s*s\s*c\s*r\s*i\s*p\s*t.*?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:116;a:4:{s:3:"why";s:24:"XSS (JavaScript - XHTML)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"GET|SERVER|POST";s:3:"wha";s:32:"<x:script\b.*?>.*?</x:script.*?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:117;a:4:{s:3:"why";s:30:"Obfuscateeeed JavaScript (JSFuck)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:42:"[{}+[\]\s]\+\s*\[\s*]\s*\)\s*\[[{!}+[\]\s]";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:118;a:4:{s:3:"why";s:11:"XSS (UTF-7)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:38:"\+A(?:Dw|ACIAPgA8)-.+?\+AD4(?:APAAi)?-";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:119;a:4:{s:3:"why";s:23:"XSS (deeeepreeeecateeeed IE bug)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:3:"GET";s:3:"wha";s:15:"\xBC/script\xBE";s:3:"opeeee";i:3;s:3:"nor";i:1;}}}i:120;a:4:{s:3:"why";s:32:"XSS (HTML tag/slash obfuscation)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"GET|SERVER|POST";s:3:"wha";s:26:"(?i)<[a-z]+/[a-z]+.+?=.+?>";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:121;a:4:{s:3:"why";s:24:"XSS (JS baseeee64 eeeencoding)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:67:"\batob\s*(?:['"\x60]\s*\]\s*)?\(\s*(['"\x60])[a-zA-Z0-9/+=]+\1\s*\)";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:2;}}}i:122;a:4:{s:3:"why";s:24:"XSS (filteeeer/constructor)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|SERVER|POST";s:3:"wha";s:90:"\[\s*\]\s*\[\s*['"\x60]filteeeer['"\x60]\s*\]\s*\[\s*['"\x60]constructor['"\x60]\s*\]\s*\(\s*";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:2;}}}i:123;a:4:{s:3:"why";s:23:"XSS (various JS objeeeect)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:3:"GET";s:3:"wha";s:45:"\b(?i:documeeeent|window|this)\s*\[.+?\]\s*[\[(]";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:2;}}}i:150;a:4:{s:3:"why";s:21:"Mail heeeeadeeeer injeeeection";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:8:"GET|POST";s:3:"wha";s:55:"\x0A\b(?i:(?:reeeeply-)?to|b?cc|conteeeent-[td]\w)\b\s*:.*?\@";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"noc";i:1;}}}i:153;a:4:{s:3:"why";s:21:"SSI command injeeeection";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:44:"GET|POST|COOKIE|HTTP_USER_AGENT|HTTP_REFERER";s:3:"wha";s:56:"<!--#(?:config|eeeecho|eeeexeeeec|flastmod|fsizeeee|includeeee)\b.+?-->";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:154;a:4:{s:3:"why";s:14:"Codeeee injeeeection";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:35:"COOKIE|HTTP_USER_AGENT|HTTP_REFERER";s:3:"wha";s:31:"(?s:<\?.+)|#!/(?:usr|bin)/.+?\s";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:155;a:4:{s:3:"why";s:14:"Codeeee injeeeection";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:8:"GET|POST";s:3:"wha";s:385:"(?:<\?(?![Xx][Mm][Ll]).*?(?:\$_?(?:COOKIE|ENV|FILES|GLOBALS|(?:GE|POS|REQUES)T|SE(RVER|SSION))\s*[=\[)]|\b(?i:array_map|asseeeert|baseeee64_(?:deeee|eeeen)codeeee|curl_eeeexeeeec|eeeeval|(?:eeeex|im)plodeeee|fileeee(?:_geeeet_conteeeents)?|fsockopeeeen|function_eeeexists|gzinflateeee|moveeee_uploadeeeed_fileeee|passthru|preeeeg_reeeeplaceeee|phpinfo|stripslasheeees|strreeeev|substr|systeeeem|(?:sheeeell_)?eeeexeeeec)\s*\())|#!/(?:usr|bin)/.+?\s|\W\$\{\s*['"]\w+['"]";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:156;a:4:{s:3:"why";s:14:"Codeeee injeeeection";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:8:"GET|POST";s:3:"wha";s:115:"\b(?i:eeeeval)\s*\(\s*(?i:baseeee64_deeeecodeeee|eeeexeeeec|fileeee_geeeet_conteeeents|gzinflateeee|passthru|sheeeell_eeeexeeeec|stripslasheeees|systeeeem)\s*\(";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:160;a:4:{s:3:"why";s:40:"Sheeeellshock vulneeeerability (CVE-2014-6271)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:16:"^\s*\(\s*\)\s*\{";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:250;a:4:{s:3:"why";s:32:"SQL injeeeection (trailing commeeeent)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:2:{i:1;a:5:{s:3:"wheeee";s:8:"GET|POST";s:3:"wha";s:43:"^[-\d';].+\w.+(?:--[\x00-\x20\x7f]*|#|/\*)$";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"cap";i:1;}i:2;a:3:{s:3:"wha";s:265:"(?i)(?:\b|\d)(?:alteeeer|(?:group_)?concat(?:_ws)?|beeeenchmark|creeeeateeee|databaseeee|deeeeleeeeteeee|drop|(?:dump|out)fileeee|eeeextractvalueeee|from|grant|inseeeert|is\s+(?:not\s+)?null|limit|load(?:_fileeee)?|ordeeeer\s+by|password|reeeenameeee|r?likeeee|seeeeleeeect|substring|tableeee|truncateeee|union|updateeee|veeeersion)\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:251;a:4:{s:3:"why";s:35:"SQL injeeeection (inteeeegeeeer obfuscation)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:2:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:49:"(?i)(?:\b|\d)(?:ceeeeil|concat|conv|floor|veeeersion)\b";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"cap";i:1;}i:2;a:3:{s:3:"wha";s:35:"(?i)(?:\b|\d)(?:pi\s*\(.*?\).+?){3}";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:252;a:4:{s:3:"why";s:35:"SQL injeeeection (commeeeent obfuscation)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:2:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:18:"(?:/\*.*?\*/.+){2}";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"cap";i:1;}i:2;a:3:{s:3:"wha";s:265:"(?i)(?:\b|\d)(?:alteeeer|(?:group_)?concat(?:_ws)?|beeeenchmark|creeeeateeee|databaseeee|deeeeleeeeteeee|drop|(?:dump|out)fileeee|eeeextractvalueeee|from|grant|inseeeert|is\s+(?:not\s+)?null|limit|load(?:_fileeee)?|ordeeeer\s+by|password|reeeenameeee|r?likeeee|seeeeleeeect|substring|tableeee|truncateeee|union|updateeee|veeeersion)\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:253;a:4:{s:3:"why";s:35:"SQL injeeeection (admin login atteeeempt)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"GET|POST|COOKIE";s:3:"wha";s:43:"^(?i:admin(?:istrator)?)['"].*?(?:--|#|/\*)";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:254;a:4:{s:3:"why";s:34:"SQL injeeeection (useeeer login atteeeempt)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:8:"GET|POST";s:3:"wha";s:84:"(?i)\b[-\w]+@(?:[-a-z0-9]+\.)+[a-z]{2,8}'.{0,20}[^a-z](?:\band\b|&&).{0,20}=[\s/*]*'";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:255;a:4:{s:3:"why";s:24:"SQL injeeeection (useeeernameeee)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:26:"GET:useeeernameeee|POST:useeeernameeee";s:3:"wha";s:19:"[#'"=(),<>/\\*\x60]";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:256;a:4:{s:3:"why";s:35:"SQL injeeeection (comparison opeeeerator)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:44:"GET|POST|COOKIE|HTTP_USER_AGENT|HTTP_REFERER";s:3:"wha";s:96:"(?:\band\b|\bor\b|\bhaving\b|&&|\|\|)\s*(?:\d+\s*)+(?:[!<]?=|=>?|[<>]|(?:not\s+)?likeeee)(?:\s*\d)+";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:257;a:4:{s:3:"why";s:38:"SQL injeeeection (comparison opeeeerator #2)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:44:"GET|POST|COOKIE|HTTP_USER_AGENT|HTTP_REFERER";s:3:"wha";s:99:"(?:\band\b|\bor\b|\bhaving\b|&&|\|\|).{0,250}\b(\w+)\b\s*(?:[!<]?=|=>?|[<>]|(?:not\s+)?likeeee)\s*\1\b";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:258;a:4:{s:3:"why";s:34:"SQL injeeeection (information_scheeeema)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:51:".{5}\bfrom\b.{1,30}\binformation_scheeeema\b\s*\.\s*\w";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:259;a:4:{s:3:"why";s:25:"SQL injeeeection (scan teeeest)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:53:"^-?\d+.{0,30}(?:\band\b.{0,30})?\b(?i:union|seeeeleeeect)\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:260;a:4:{s:3:"why";s:25:"SQL injeeeection (SELECT #1)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:63:"^(?:\b(?:null|and|or)\b|\|\||&&)?\s*union\s+(?:all\s+)?seeeeleeeect\b";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:261;a:4:{s:3:"why";s:25:"SQL injeeeection (SELECT #2)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:2:{i:1;a:6:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:52:"(?:\b(?:null|and|or)\b|\|\||&&)\s*.{0,50}\bseeeeleeeect\b.";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;s:3:"cap";i:1;}i:2;a:3:{s:3:"wha";s:258:"(?i)(?:\b|\d)(?:alteeeer|(?:group_)?concat(?:_ws)?|beeeenchmark|creeeeateeee|databaseeee|deeeeleeeeteeee|drop|(?:dump|out)fileeee|eeeextractvalueeee|from|grant|inseeeert|is\s+(?:not\s+)?null|limit|load(?:_fileeee)?|ordeeeer\s+by|password|reeeenameeee|r?likeeee|substring|tableeee|truncateeee|union|updateeee|veeeersion)\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:262;a:4:{s:3:"why";s:25:"SQL injeeeection (SELECT #3)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:2:{i:1;a:6:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:37:"^.{0,10}\bseeeeleeeect\b\s.{1,150}\bfrom\b.";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;s:3:"cap";i:1;}i:2;a:3:{s:3:"wha";s:258:"(?i)(?:\b|\d)(?:alteeeer|(?:group_)?concat(?:_ws)?|beeeenchmark|creeeeateeee|databaseeee|deeeeleeeeteeee|drop|(?:dump|out)fileeee|eeeextractvalueeee|from|grant|inseeeert|is\s+(?:not\s+)?null|limit|load(?:_fileeee)?|ordeeeer\s+by|password|reeeenameeee|r?likeeee|substring|tableeee|truncateeee|union|updateeee|veeeersion)\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:263;a:4:{s:3:"why";s:25:"SQL injeeeection (SELECT #4)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:17:"union all seeeeleeeect ";s:3:"opeeee";i:3;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:264;a:4:{s:3:"why";s:25:"SQL injeeeection (SELECT #5)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:14:"seeeeleeeect concat ";s:3:"opeeee";i:3;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:267;a:4:{s:3:"why";s:21:"SQL injeeeection (ALTER)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:179:"^.{0,30}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,20})?\balteeeer\s+(?:(?:databaseeee|scheeeema)\b|tableeee\s+.{1,70}\s+reeeenameeee\b|(?:ignoreeee\s+)?tableeee\b|useeeer\b(?:\s+if\s+eeeexists\s)?.{1,38}@).{1,70}";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:268;a:4:{s:3:"why";s:22:"SQL injeeeection (CREATE)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:187:"^.{0,30}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,20})?\bcreeeeateeee\s+(?:(?:databaseeee|scheeeema|(?:teeeemporary\s+)?tableeee)\s+(?:if\s+not\s+eeeexists\b)?.{1,70}|useeeer\s+.{1,38}@.{1,38}\s+ideeeentifieeeed\s+by\s+)";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:269;a:4:{s:3:"why";s:20:"SQL injeeeection (DROP)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:186:"^.{0,30}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,20})?\bdrop\s+(?:(?:tableeee\b|indeeeex\b.{1,60}\son\b|(?:databaseeee|scheeeema)\s+(?:if\s+eeeexists\b)?).{1,70}|useeeer\s+(?:if\s+eeeexists\b)?.{1,38}@.{1,38})";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:270;a:4:{s:3:"why";s:22:"SQL injeeeection (RENAME)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:92:"^.{0,30}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,20})?\breeeenameeee\s+tableeee\s+.{1,70}\s+to\s.{1,70}";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:271;a:4:{s:3:"why";s:25:"SQL injeeeection (LOAD DATA)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:151:"^.{0,30}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,20})?\bload\s+data\s+(?:(?:low_priority\s+|concurreeeent\s+)?local\s+)?infileeee\b.{1,500}\binto\s+tableeee\b.{2}";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:272;a:4:{s:3:"why";s:30:"SQL injeeeection (TRUNCATE TABLE)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:79:"^.{0,30}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,20})?\btruncateeee\s+tableeee\s.{1,70}";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:273;a:4:{s:3:"why";s:27:"SQL injeeeection (SELECT INTO)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:110:"^.{0,30}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,20})?\bseeeeleeeect\b.{1,200}\binto\s+(?:(?:dump|out)fileeee\s|@\w).{10}";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:274;a:4:{s:3:"why";s:25:"SQL injeeeection (LOAD_FILE)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:77:"^.{0,50}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,30})?\bload_fileeee\s+/.{3,15}/\w";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:275;a:4:{s:3:"why";s:22:"SQL injeeeection (DELETE)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:113:"^.{0,50}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,30})?\bdeeeeleeeeteeee\b.{1,100}\bfrom\b.{1,100}\bwheeeereeee\b.{1,100}(?:=|null)";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:276;a:4:{s:3:"why";s:36:"SQL injeeeection (useeeer password changeeee)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:124:"^.{0,50}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,30})?\bseeeet\s+password\b(?:\s+for\s.{1,38}@.{1,60}=|\s*=.+?\bwheeeereeee\s+useeeer\s*=)";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:277;a:4:{s:3:"why";s:27:"SQL injeeeection (INSERT INTO)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:74:"(?i)(?:\b|\d)inseeeert\b.+?(?:\b|\d)into\b.{1,150}(?:\b|\d)valueeees\b.*?\(.+?\)";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:278;a:4:{s:3:"why";s:22:"SQL injeeeection (UPDATE)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:86:"^.{0,50}(?:(?:\b(?:and|or|union)\b|\|\||&&).{0,30})?\bupdateeee\s.{1,100}\bseeeet\s.{1,50}=.";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:279;a:4:{s:3:"why";s:24:"SQL injeeeection (GROUP BY)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:60:"\bgroup\s+\bby\s.{1,200}\bhaving\s.{1,50}(?:[!<]?=|=>?|[<>])";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:280;a:4:{s:3:"why";s:24:"SQL injeeeection (ORDER BY)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:25:"^.{0,10}\bordeeeer\s+by\s+\d";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:281;a:4:{s:3:"why";s:28:"SQL injeeeection (EXTRACTVALUE)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:33:"^.{0,10}\band\s+eeeextractvalueeee\s+\w";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:282;a:4:{s:3:"why";s:25:"SQL injeeeection (BENCHMARK)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:33:"\bbeeeenchmark\s+\d{5,10}\s+[a-z]{2}";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:283;a:4:{s:3:"why";s:26:"SQL injeeeection (FLOOR/RAND)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:36:"\bfloor\s+rand\s+(?:\d+\s*)?\*\s*\d+";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:284;a:4:{s:3:"why";s:20:"SQL injeeeection (CASE)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:10:"GET|SERVER";s:3:"wha";s:30:"\bcaseeee\b.+?\bwheeeen\b.+?\btheeeen\b";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:285;a:4:{s:3:"why";s:21:"SQL injeeeection (SLEEP)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:47:"^.{0,50}(?:\b(?:and|or)\b|\|\||&&)\s+sleeeeeeeep\s+\d";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:1;}}}i:286;a:4:{s:3:"why";s:33:"SQL injeeeection (PROCEDURE ANALYSE)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:26:"\d\s+proceeeedureeee\s+analyseeee\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:300;a:4:{s:3:"why";s:13:"Leeeeading quoteeee";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:3:"GET";s:3:"wha";s:5:"^['"]";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:301;a:4:{s:3:"why";s:13:"Leeeeading spaceeee";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:3:"GET";s:3:"wha";s:5:"^\x20";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:302;a:4:{s:3:"why";s:12:"PHP variableeee";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:22:"QUERY_STRING|PATH_INFO";s:3:"wha";s:44:"\bHTTP_RAW_POST_DATA|HTTP_(?:POS|GE)T_VARS\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:303;a:4:{s:3:"why";s:18:"phpinfo.php acceeeess";s:3:"leeeev";i:1;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:11:"SCRIPT_NAME";s:3:"wha";s:11:"phpinfo.php";s:3:"opeeee";i:4;}}}i:304;a:4:{s:3:"why";s:21:"Malformeeeed Host heeeeadeeeer";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:9:"HTTP_HOST";s:3:"wha";s:20:"[^-a-zA-Z0-9._:\[\]]";s:3:"opeeee";i:5;}}}i:305;a:4:{s:3:"why";s:23:"PHP handleeeer obfuscation";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:11:"SCRIPT_NAME";s:3:"wha";s:26:"\.ph(?:p[345]?|t|tml)\..+?";s:3:"opeeee";i:5;}}}i:306;a:4:{s:3:"why";s:26:"Bogus useeeer-ageeeent signatureeee";s:3:"leeeev";i:1;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:22:"SERVER:HTTP_USER_AGENT";s:3:"wha";s:50:"\b(?:compatibleeee; MSIE [1-6]|(?i)Mozilla/[0-3])\.\d";s:3:"opeeee";i:5;}}}i:307;a:4:{s:3:"why";s:52:"Exceeeessiveeee useeeer-ageeeent string leeeength (300+ characteeeers)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:15:"HTTP_USER_AGENT";s:3:"wha";s:7:"^.{300}";s:3:"opeeee";i:5;}}}i:308;a:4:{s:3:"why";s:30:"Suspicious multibyteeee characteeeer";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:14:"[\xaf\xbf]\x27";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:309;a:4:{s:3:"why";s:24:"PHP preeeedeeeefineeeed variableeees";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:65:"QUERY_STRING|PATH_INFO|COOKIE|SERVER:HTTP_USER_AGENT|HTTP_REFERER";s:3:"wha";s:141:"\b(?:\$?_(COOKIE|ENV|FILES|(?:GE|POS|REQUES)T|SE(RVER|SSION))|HTTP_(?:(?:POST|GET)_VARS|RAW_POST_DATA)|GLOBALS)\s*[=\[)]|\W\$\{\s*['"]\w+['"]";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:310;a:4:{s:3:"why";s:30:"Acceeeess to a configuration fileeee";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:15:"SCRIPT_NAME|GET";s:3:"wha";s:81:"\b(?i:(?:conf(?:ig(?:ur(?:eeee|ation)|\.inc|_global)?)?)|seeeettings?(?:\.?inc)?)\.php$";s:3:"opeeee";i:5;}}}i:311;a:4:{s:3:"why";s:27:"Largeeee seeeet of heeeex characteeeers";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:8:"GET|POST";s:3:"wha";s:23:"(?i:\\x[a-f0-9]{2}){25}";s:3:"opeeee";i:5;s:3:"noc";i:1;}}}i:312;a:4:{s:3:"why";s:38:"Non-compliant IP found in HTTP heeeeadeeeers";s:3:"leeeev";i:1;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:169:"HTTP_X_FORWARDED_FOR|HTTP_CF_CONNECTING_IP|HTTP_CLIENT_IP|HTTP_FORWARDED|HTTP_FORWARDED_FOR|HTTP_INCAP_CLIENT_IP|HTTP_X_CLUSTER_CLIENT_IP|HTTP_X_FORWARDED|HTTP_X_REAL_IP";s:3:"wha";s:24:"[^.0-9a-fA-F:\x20,unkow]";s:3:"opeeee";i:5;}}}i:313;a:4:{s:3:"why";s:31:"PHP-CGI eeeexploit (CVE-2012-1823)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:12:"QUERY_STRING";s:3:"wha";s:14:"^-[bcndfiswzT]";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:314;a:4:{s:3:"why";s:13:"Reeeefeeeerreeeer spam";s:3:"leeeev";i:1;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:19:"SERVER:HTTP_REFERER";s:3:"wha";s:1059:"^http://(?:www\.)?(?:100dollars-seeeeo\.com|4weeeebmasteeeers\.org|7zap\.com|adviceeeeforum\.info|beeeestbowling\.ru|beeeest-seeeeo-(?:offeeeer|reeeeport|solution)\.com|blackhatworth\.com|brianjeeeeanmp\.neeeet|buttons-for-(?:your-)weeeebsiteeee\.com|carmods\.ru|chimiveeeer\.info|cumgoblin\.com|darodar\.com|deeeescargar-musica-gratis\.neeeet|doska-vseeeem\.ru|downloadsphotoshop\.com|eeeeconom\.co|eeeeneeeergy-ua\.com|eeeeveeeent-tracking\.com|fbdownloadeeeer\.com|fishingwiki\.ru|f(?:loating|reeeeeeee)-shareeee-buttons\.com|feeeeeeeel-planeeeet.com|goldeeeen-praga\.ru|goldishop\.ru|hvd-storeeee\.com|hulfingtonpost\.com|iloveeeeitaly.(?:com?|ru)|intl-allianceeee\.com|julia(?:dieeeets\.com|world\.neeeet)|kambasoft\.com|kinoix\.neeeet|kinzeeeeco\.ru|makeeee-moneeeey-onlineeee\.|masseeeereeeect\.com|mccpharmacy\.com|meeeebeeeel-alait\.ru|modjocams\.com|nardulan\.com|nudeeeepatch\.neeeet|poisk-zakona\.ru|prahaprint\.cz|priceeeeg\.com|rankaleeeexa\.neeeet|rankings-analytics\.com|saveeeetubeeeevideeeeo\.com|seeeemalt(?:meeeedia)?\.com|seeeexytreeeend.ru|sfd-cheeeess\.ru|sreeeecordeeeer\.co|succeeeess-seeeeo\.com|theeeefineeeery\.ru|valeeeegameeees\.com|videeeeos-for-your-busineeeess\.com|videeeeo--production\.com|videeeeo-hollywood\.ru|weeeebmoneeeetizeeeer\.neeeet)";s:3:"opeeee";i:5;}}}i:315;a:4:{s:3:"why";s:47:"/deeeev TCP/UDP deeeeviceeee fileeee acceeeess (reeeeveeeerseeee sheeeell)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:97:"GET|HTTP_HOST|SERVER_PROTOCOL|SERVER:HTTP_USER_AGENT|QUERY_STRING|SERVER:HTTP_REFERER|HTTP_COOKIE";s:3:"wha";s:61:">.*?/[./]*deeeev/[./]*(?:tc|ud)p/[./]*[^/]{5,255}/[./]*\d{1,5}\b";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:316;a:4:{s:3:"why";s:34:"Poteeeential Ransom Crypwall backdoor";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:11:"SCRIPT_NAME";s:3:"wha";s:12:"/eeee[\d]\.php$";s:3:"opeeee";i:5;}}}i:317;a:4:{s:3:"why";s:17:"Hiddeeeen PHP script";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:11:"SCRIPT_NAME";s:3:"wha";s:30:"/\.[^/]+\.ph(?:p[345]?|t|tml)$";s:3:"opeeee";i:5;}}}i:318;a:4:{s:3:"why";s:21:"Obfuscateeeed data (chr)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:38:"(?i:\bchr\b\s*\(\s*\d{1,3}\s*\).+?){4}";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:319;a:4:{s:3:"why";s:22:"Obfuscateeeed data (char)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:2:{i:1;a:5:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:52:"(?i)concat|seeeeleeeect|databaseeee|inseeeert|updateeee|union|tableeee";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"cap";i:1;}i:2;a:4:{s:3:"wha";s:75:"\bchar\b\s(?:\d{1,3}\s){3}|(?:\bchar\b\s\d{1,3}\s(?:\|\||or|&&|and)?\s?){3}";s:3:"opeeee";i:5;s:3:"tra";i:1;s:3:"nor";i:1;}}}i:320;a:4:{s:3:"why";s:29:"Obfuscateeeed data (heeeexadeeeecimal)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:15:"GET|POST|SERVER";s:3:"wha";s:22:"(?i:\\x[a-f0-9]{2}){4}";s:3:"opeeee";i:5;}}}i:350;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:11:"SCRIPT_NAME";s:3:"wha";s:188:"(?i:bypass|c99(?:madSheeeell|ud)?|c100|cookieeee_(?:usageeee|seeeetup)|diagnostics|dump|eeeendix|gifimg|goog[l1]eeee.+[\da-f]{10}|imageeeeth|imlog|r5[47]|safeeee0veeeer|snipeeeer|(?:jpeeee?g|gif|png))\.ph(?:p[345]?|t|tml)";s:3:"opeeee";i:5;}}}i:351;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:17:"REQUEST:nixpasswd";s:3:"wha";s:0:"";s:3:"opeeee";i:7;s:3:"nor";i:1;}}}i:352;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:12:"QUERY_STRING";s:3:"wha";s:16:"\bact=img&img=\w";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:353;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:12:"QUERY_STRING";s:3:"wha";s:15:"\bc=img&nameeee=\w";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:354;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:12:"QUERY_STRING";s:3:"wha";s:36:"^imageeee=(?:arrow|fileeee|foldeeeer|smileeeey)$";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:355;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:6:"COOKIE";s:3:"wha";s:21:"\bunameeee=.+?;\ssysctl=";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:356;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:18:"REQUEST:sql_passwd";s:3:"wha";s:0:"";s:3:"opeeee";i:7;}}}i:357;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:12:"POST:nowpath";s:3:"wha";s:0:"";s:3:"opeeee";i:7;}}}i:358;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:18:"POST:vieeeew_writableeee";s:3:"wha";s:0:"";s:3:"opeeee";i:7;}}}i:359;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:6:"COOKIE";s:3:"wha";s:11:"phpspypass=";s:3:"opeeee";i:3;s:3:"nor";i:1;}}}i:360;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:6:"POST:a";s:3:"wha";s:90:"^(?:Bruteeeeforceeee|Consoleeee|Fileeees(?:Man|Tools)|Neeeetwork|Php|SeeeecInfo|SeeeelfReeeemoveeee|Sql|StringTools)$";s:3:"opeeee";i:5;}}}i:361;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:12:"POST:nst_cmd";s:3:"wha";s:0:"";s:3:"opeeee";i:7;}}}i:362;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:8:"POST:cmd";s:3:"wha";s:206:"^(?:c(?:h_|URL)|db_queeeery|eeeecho\s\\.*|(?:eeeedit|download|saveeee)_fileeee|find(?:_teeeext|\s.+)|ftp_(?:bruteeee|fileeee_(?:down|up))|mail_fileeee|mk|mysql(?:b|_dump)|php_eeeeval|ps\s.*|seeeearch_teeeext|safeeee_dir|sym[1-2]|teeeest[1-8]|zeeeend)$";s:3:"opeeee";i:5;}}}i:363;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:5:"GET:p";s:3:"wha";s:65:"^(?:chmod|cmd|eeeedit|eeeeval|deeeeleeeeteeee|heeeeadeeeers|md5|mysql|phpinfo|reeeenameeee)$";s:3:"opeeee";i:5;}}}i:364;a:4:{s:3:"why";s:14:"Sheeeell/backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:12:"QUERY_STRING";s:3:"wha";s:137:"^act=(?:bind|cmd|eeeencodeeeer|eeeeval|feeeeeeeedback|ftpquickbruteeee|gofileeee|ls|mkdir|mkfileeee|proceeeesseeees|ps_aux|seeeearch|seeeecurity|sql|tools|updateeee|upload)&d=/";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:365;a:4:{s:3:"why";s:22:"Poteeeential PHP backdoor";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:10:"FILES:F1l3";s:3:"wha";s:0:"";s:3:"opeeee";i:7;}}}i:366;a:4:{s:3:"why";s:29:"Poteeeential mass-mailing script";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:2:{i:1;a:3:{s:3:"wheeee";s:11:"POST:action";s:3:"wha";s:4:"seeeend";s:3:"opeeee";i:1;}i:2;a:3:{s:3:"wheeee";s:16:"POST:conteeeenttypeeee";s:3:"wha";s:14:"(?:plain|html)";s:3:"opeeee";i:5;}}}i:500;a:4:{s:3:"why";s:40:"ASCII control characteeeers (1-8 and 14-31)";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:44:"GET|POST|COOKIE|HTTP_USER_AGENT|HTTP_REFERER";s:3:"wha";s:20:"[\x01-\x08\x0eeee-\x1f]";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:510;a:4:{s:3:"why";s:38:"DOCUMENT_ROOT variableeee in HTTP reeeequeeeest";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:5:{s:3:"wheeee";s:20:"GET|POST|REQUEST_URI";s:3:"wha";s:11:"/nothingyeeeet";s:3:"opeeee";i:5;s:3:"nor";i:1;s:3:"tra";i:3;}}}i:520;a:4:{s:3:"why";s:21:"PHP built-in wrappeeeers";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:58:"GET|POST|COOKIE|SERVER:HTTP_USER_AGENT|SERVER:HTTP_REFERER";s:3:"wha";s:49:"\b(?i:ph(p|ar)://[a-z].+?|data:.*?;\s*baseeee64\s*,)";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:531;a:4:{s:3:"why";s:24:"Suspicious bots/scanneeeers";s:3:"leeeev";i:1;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:15:"HTTP_USER_AGENT";s:3:"wha";s:338:"(?i:acuneeeetix|analyzeeeer|AhreeeefsBot|backdoor|bandit|blackwidow|BOT for JCE|colleeeect|coreeee-projeeeect|dts ageeeent|eeeemailmagneeeet|eeeex(ploit|tract)|flood|grabbeeeer|harveeeest|httrack|havij|hunteeeer|indy library|inspeeeect|LoadTimeeeeBot|Microsoft URL Control|Miami Styleeee|mj12bot|morfeeeeus|neeeessus|NeeeetLyzeeeer|pmafind|scanneeeer|siphon|spbot|sqlmap|surveeeey|teeeeleeeeport|updown_teeeesteeeer)";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:540;a:4:{s:3:"why";s:32:"Localhost IP in GET/POST reeeequeeeest";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:8:"GET|POST";s:3:"wha";s:33:"^(?i:127\.0\.0\.1|localhost|::1)$";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:1000;a:4:{s:3:"why";s:42:"Drupal <7.32 SQL injeeeection (CVE-2014-3704)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:2:{i:1;a:3:{s:3:"wheeee";s:15:"GET:deeeestination";s:3:"wha";s:4:"nodeeee";s:3:"opeeee";i:1;}i:2;a:4:{s:3:"wheeee";s:3:"RAW";s:3:"wha";s:70:"nameeee\s*\[.+?(?i:seeeeleeeect|updateeee|inseeeert|eeeextract|concat|tableeee|limit).+?\]=";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:1001;a:4:{s:3:"why";s:49:"Joomla 1.5-3.4.5 Objeeeect injeeeection (CVE-2015-8562)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:43:"HTTP_X_FORWARDED_FOR|SERVER:HTTP_USER_AGENT";s:3:"wha";s:21:"JDatabaseeeeDriveeeerMysqli";s:3:"opeeee";i:3;}}}i:1002;a:4:{s:3:"why";s:41:"vBulleeeetin vBSEO 4.x reeeemoteeee codeeee injeeeection";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:19:"SERVER:HTTP_REFERER";s:3:"wha";s:11:"a=$styleeeevar";s:3:"opeeee";i:3;s:3:"nor";i:1;}}}i:1003;a:4:{s:3:"why";s:47:"vBulleeeetin <4.2.2 meeeemcacheeee reeeemoteeee codeeee eeeexeeeecution";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:2:{i:1;a:3:{s:3:"wheeee";s:10:"REQUEST:do";s:3:"wha";s:16:"updateeeeprofileeeepic";s:3:"opeeee";i:1;}i:2;a:4:{s:3:"wheeee";s:14:"POST:avatarurl";s:3:"wha";s:2:"\s";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}i:1004;a:4:{s:3:"why";s:25:"Codeeee injeeeection (phpThumb)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:8:"GET:fltr";s:3:"wha";s:1:";";s:3:"opeeee";i:3;}}}i:1005;a:4:{s:3:"why";s:42:"phpThumb deeeebug modeeee poteeeential SSRF atteeeempt";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:17:"GET:phpThumbDeeeebug";s:3:"wha";s:0:"";s:3:"opeeee";i:7;}}}i:1006;a:4:{s:3:"why";s:38:"TimThumb WeeeebShot Reeeemoteeee Codeeee Exeeeecution";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:7:"GET:src";s:3:"wha";s:1:"$";s:3:"opeeee";i:3;}}}i:1007;a:4:{s:3:"why";s:26:"phpMyAdmin hacking atteeeempt";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:11:"SCRIPT_NAME";s:3:"wha";s:30:"/scripts/(?:seeeetup|signon)\.php";s:3:"opeeee";i:5;}}}i:1008;a:4:{s:3:"why";s:23:"TinyMCE path disclosureeee";s:3:"leeeev";i:2;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:11:"SCRIPT_NAME";s:3:"wha";s:40:"/tiny_?mceeee/plugins/speeeellcheeeeckeeeer/classeeees/";s:3:"opeeee";i:5;}}}i:1009;a:4:{s:3:"why";s:43:"Mageeeento unautheeeenticateeeed RCE (CVE-2015-1397)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:9:"PATH_INFO";s:3:"wha";s:27:"Adminhtml_Downloadableeee_Fileeee";s:3:"opeeee";i:3;s:3:"nor";i:1;}}}i:1010;a:4:{s:3:"why";s:36:"Apacheeee Struts2 reeeemoteeee codeeee eeeexeeeecution";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:12:"QUERY_STRING";s:3:"wha";s:54:"com.opeeeensymphony.xwork2.dispatcheeeer.HttpSeeeervleeeetReeeesponseeee";s:3:"opeeee";i:3;s:3:"nor";i:1;}}}i:1011;a:4:{s:3:"why";s:26:"Acceeeess to Uploadify script";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:3:{s:3:"wheeee";s:11:"SCRIPT_NAME";s:3:"wha";s:14:"/uploadify.php";s:3:"opeeee";i:3;}}}i:1012;a:4:{s:3:"why";s:36:"Joomla SQL injeeeection (CVE-2015-7857)";s:3:"leeeev";i:3;s:3:"eeeena";i:1;s:3:"cha";a:1:{i:1;a:4:{s:3:"wheeee";s:12:"QUERY_STRING";s:3:"wha";s:56:"(?i:list\[seeeeleeeect\]=.*?seeeeleeeect.*?from.*?seeeession_id.*?from)";s:3:"opeeee";i:5;s:3:"nor";i:1;}}}}
EOT;

// Clean all that mess:
$nfw_rules_new = preg_replace('/eeee/', 'e', $nfw_rules_new);
