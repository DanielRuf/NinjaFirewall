<?php
/* 2016-09-22 15:21:24 */
$title = 'Pare-Feu > Centralisation des Logs';
$close = 'Fermer';
$nfw_help = <<<'EOT'

<h3><strong>Centralisation des Logs</strong></h3>

<p>La Centralisation des Logs vous permet d’accéder, depuis votre site principal, au journal du pare-feu de chacun de vos sites protégés par NinjaFirewall. Vous n'avez plus besoin de vous connecter à chaque site pour analyser vos journaux. Vous pouvez vous connecter à un nombre illimité de sites, quelque soit leur version de NinjaFirewall : WP, <font color="#21759B">WP+</font>, Pro or <font color="red">Pro+</font>.</p>

<p><img src="static/bullet_off.gif">&nbsp;<strong>Clé privée :</strong> Cette clé sera utilisée pour créer votre clé publique. Entrez de 30 à 100 caractères ASCII, ou bien utilisez la clé générée aléatoirement par NinjaFirewall.</p>

<p><img src="static/bullet_off.gif">&nbsp;<strong>L'adresse IP de ce serveur :</strong> Seule cette adresse IPv4 ou IPv6 sera autorisée à se connecter aux sites distants. Si vous ne souhaitez pas de restriction d’accès par IP, veuillez entrer le caractère <code>*</code>.</p>

<p><img src="static/bullet_off.gif">&nbsp;<strong>Clé publique :</strong> Vous devez configurer vos sites distants avec cette clé. <a class="links" style="border-bottom:dotted 1px #FDCD25;" href="https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/">Consultez notre blog pour plus d'info</a>.</p>

<p><img src="static/bullet_off.gif">&nbsp;<strong>URL de vos sites distants :</strong> Entrez l'URL complète des sites protégés par NinjaFirewall auxquels vous souhaitez accéder à distance à partir du serveur principal.</p>

<p><img src="static/icon_warn.png">&nbsp;L'option de centralisation des logs fonctionnera même si vous désactivez NinjaFirewall. Si vous souhaitez complètement désactiver cette option, faites-le depuis ce menu.</p>

EOT;

