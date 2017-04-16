<?php

$title = 'Firewall > Centralized Loggging';
$close = 'Close';
$nfw_help = <<<'EOT'

<h3><strong>Centralized Logging</strong></h3>

<p>Centralized Logging lets you remotely access the firewall log of all your NinjaFirewall protected websites from one single installation. You do not need any more to log in to individual servers to analyse your log data. There is no limit to the number of websites you can connect to, and they can be running any edition of NinjaFirewall: WP, <font color="#21759B">WP+</font>, Pro or <font color="red">Pro+</font>.</p>

<p><img src="static/bullet_off.gif">&nbsp;<strong>Secret key:</strong> The secret key will be used to generate your public key. Enter at least 30 ASCII characters, or use the one randomly created by NinjaFirewall.</p>

<p><img src="static/bullet_off.gif">&nbsp;<strong>This server's IP address:</strong> As an additional protection layer, you can restrict access to the remote website(s) to the main server's IP only. You can use IPv4 or IPv6. If you do not want any IP restriction, enter the <code>*</code> character instead.</p>

<p><img src="static/bullet_off.gif">&nbsp;<strong>Public key:</strong> This is the public key that you will need to upload to each remote website (<a class="links" style="border-bottom:dotted 1px #FDCD25;" href="https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/">consult our blog for more info about it</a>).</p>

<p><img src="static/bullet_off.gif">&nbsp;<strong>Remote websites URL:</strong> Enter the full URL of your NinjaFirewall protected website(s) that you want to remotely access from the main server.</p>

<p><img src="static/icon_warn.png">&nbsp;Centralized Logging will keep working even if NinjaFirewall is disabled. Use this menu if you want to disable it.</p>

EOT;

