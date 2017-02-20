<?php
/* 2015-02-05 22:59:34 */
$title = 'Pare-Feu > Journal de Sécurité';
$close = 'Fermer';
$nfw_help = <<<'EOT'

<h3><strong>Visualisation Journal</strong></h3>
<p>Le journal du pare-feu affiche les requêtes HTTP qui ont été bloquées ou nettoyées ainsi que d'autres informations utiles. Il a six colonnes&nbsp;:</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>DATE :</strong> date et heure de l'incident.</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>INCIDENT :</strong> le numéro d'incident (ID) unique. Par défaut, il sera aussi affiché à l'utilisateur dont la requête aura été bloquée par le pare-feu.</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>LEVEL :</strong> sévérité des attaques (moyen, élevé ou critique), information (info, erreur, téléchargement) et mode débogage (DEBUG_ON).</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>RULE :</strong> le numéro de référence de la règle de sécurité qui a été utilisée pour bloquer la requête HTTP. Un trait d'union (<code>-</code>) à la place d'un numéro signifie que la règle provient soit de votre configuration personnelle "Politique" ou "Contrôle d'Accès" du menu "Pare-Feu".</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>IP :</strong> l'adresse IP de l'utilisateur bloqué.</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>REQUEST :</strong> la requête HTTP avec ses variables et valeurs, ainsi que la raison qui a déclenché l'incident.</p>
<p>Le journal peut également être exporté dans un fichier au format TSV (valeurs séparées par des tabulations).</p>
<hr class="dotted" size="1">

<h3><strong>Options du journal</strong></h3>
<p><img src="static/bullet_off.gif">&nbsp;<strong>Activer le journal :</strong> permet de (dés)activer le journal.</p>
<p><img src="static/bullet_off.gif">&nbsp;<strong>Rotation automatique du journal :</strong> NinjaFirewall effectue une rotation automatique du journal le premier jour de chaque mois. Vous pouvez le configurer pour que la rotation soit effectuée plus tôt, dans le cas où le journal aurait atteint une certaine taille (Mo). Par défaut, cette taille est fixée à 2 Mo.
</br />
Les journaux des mois précédents sont accessibles depuis le menu déroulant situé au dessus de la fenêtre.</p>


<hr class="dotted" size="1">

<h3><strong>Centralisation des Logs</strong></h3>

<p>La Centralisation des Logs vous permet d’accéder, depuis votre site principal, au journal du pare-feu de chacun de vos sites protégés par NinjaFirewall. Vous n'avez plus besoin de vous connecter à chaque site pour analyser vos journaux. <a class="links" style="border-bottom:dotted 1px #FDCD25;" href="https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/">Consultez notre blog pour plus d'informations à ce sujet</a>.</p>

<p><img src="static/bullet_off.gif">&nbsp;<strong>Entrez votre clé publique (optionnel):</strong> Il s'agit de la clé qui a été créée depuis le serveur principal.</p>

<p><img src="static/icon_warn.png">&nbsp;L'option de centralisation des logs fonctionnera même si vous désactivez NinjaFirewall. Si vous souhaitez complètement désactiver cette option, supprimez votre clé publique.</p>


EOT;
