<?php

$title = 'Firewall > Security Log';
$close = 'Close';
$nfw_help = <<<'EOT'

<h3><strong>View Log</strong></h3>
<p>The firewall log displays blocked and sanitised requests as well as some useful information. It has 6 columns:</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>DATE :</strong> date and time of the incident.</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>INCIDENT :</strong> unique incident number/ID as it was displayed to the blocked user.</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>LEVEL :</strong> level of severity (medium, high or critical), information (info, error, upload) and debugging mode (DEBUG_ON).</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>RULE :</strong> reference of the NinjaFirewall built-in security rule that triggered the action. A hyphen (<code>-</code>) instead of a number means it was a rule from your own Firewall Policies or Access Control page.</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>IP :</strong> the blocked user remote address.</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>REQUEST :</strong> the HTTP request including offending variables & values as well as the reason the action was logged.</p>
<p>The log can also be exported as a TSV (tab-separated values) text file.</p>

<hr class="dotted" size="1">

<h3><strong>Log Options</strong></h3>
<p><img src="static/bullet_off.gif">&nbsp;<strong>Enable firewall log:</strong> you can disable/enable the firewall log from this page.</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>Auto-rotate log:</strong> NinjaFirewall will rotate its log automatically on the very first day of each month. If your site is very busy, you may want to allow it to rotate the log when it reaches a certain size (MB) as well. By default, if will rotate the log each month or earlier, if it reaches 2 megabytes.
<br />
Rotated logs, if any, can be selected and viewed from the dropdown menu.</p>


<hr class="dotted" size="1">

<h3><strong>Centralized Logging</strong></h3>

<p>Centralized Logging lets you remotely access the firewall log of all your NinjaFirewall protected websites from one single installation. You do not need any more to log in to individual servers to analyse your log data. <a class="links" style="border-bottom:dotted 1px #FDCD25;" href="https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/">Consult our blog for more info about it</a>.</p>

<p><img src="static/bullet_off.gif">&nbsp;<strong>Enter your public key (optional):</strong> This is the public key that was created from your main server.</p>

<p><img src="static/icon_warn.png">&nbsp;Centralized Logging will keep working even if NinjaFirewall is disabled. Delete your public key if you want to disable it.</p>

<hr class="dotted" size="1">

<h3><strong>Syslog</strong></h3>

<p>In addition to the firewall's log, events can also be redirected to the syslog server (<code>LOG_USER</code> facility). If you have a shared hosting account, keep this option disabled as you do not have any access to the server's logs.</p>

<p>The logline uses the following format:</p>
<p><code>ninjafirewall[<font color="red">AA</font>]: <font color="red">BB</font>: #<font color="red">CCCCCC</font>: <i>Some event</i> from <font color="red">DD</font> on <font color="red">EE</font></code><p>
<ul>
	<li>AA: the process ID (PID).</li>
	<li>BB: the level of severity as it appears in the firewall\'s log. It can be <code>CRITICAL</CODE>, <CODE>HIGH</CODE>, <CODE>MEDIUM</CODE>, <CODE>INFO</CODE>, <CODE>UPLOAD</CODE> or <CODE>DEBUG_ON</CODE>.</li>
	<li>CCCCCC: the 7-digit incident ID.</li>
	<li>DD: the user IPv4 or IPv6 address.</li>
	<li>EE: the website (sub-)domain name.</li>
</ul>
Sample loglines:
<br />
<center>
	<textarea style="width:100%;height:90px;font-family:monospace;" wrap="off">Oct  3 01:53:51 www ninjafirewall[19054]: INFO: #2498192: Logged in administrator from 12.24.56.78 on somesite.com
Oct  3 02:01:56 www ninjafirewall[19054]: INFO: #1522694: Firewall log deleted by admin from 12.24.56.78 on somesite.com
Oct  3 14:02:20 www ninjafirewall[18270]: HIGH: #7167442: Cross-site scripting from fe80::6e88:14ff:fe3e:86f0 on anothersite.com
Oct  3 15:40:48 www ninjafirewall[19058]: CRITICAL: #2601781: ASCII character 0x00 (NULL byte) from fe80::6e88:14ff:fe3e:86f0 on anothersite.com</textarea>
</center>


EOT;

