<?php
/*
 +---------------------------------------------------------------------+
 | NinjaFirewall (Pro edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 |                                                                     |
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
if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

require (__DIR__ .'/lang/' . $nfw_options['admin_lang'] . '/' . basename(__FILE__) );

html_header();
echo '<br /><div class="warning"><p>' . $lang['pro_only'] . ' (<a class="links" style="border-bottom:1px dotted #FFCC25;" href="https://nintechnet.com/ninjafirewall/pro-edition/">'. $lang['lic_upgrade'] . '</a>).</p></div>';

?>
<br />
	<fieldset><legend>&nbsp;<b><?php echo $lang['cent_log'] ?></b>&nbsp;</legend>
		<table width="100%" class="smallblack" border="0" cellpadding="10" cellspacing="0">
			<tr>
				<td width="55%" align="left"><?php echo $lang['enable'] ?></td>
				<td width="45%">
					<p><label><input disabled="disabled" type="radio" checked="checked">&nbsp;<?php echo $lang['yes'] ?></label></p>
					<p><label><input disabled="disabled" type="radio">&nbsp;<?php echo $lang['no'] ?></label></p>
				</td>
			</tr>
		</table>

		<br />

		<table width="100%" class="smallblack" border="0" cellpadding="10" cellspacing="0">
			<tr>
				<td width="55%" align="left" class="dotted"><?php echo $lang['sec_key'] ?></td>
				<td width="45%" class="dotted">
					<input class="input" type="text" disabled="disabled" style="width:100%" value="<?php
						$key = htmlspecialchars( generate_clogs_seckey() );
						echo $key;
						?>" />
					<p><i><?php echo $lang['ascii'] ?></i></p>
				</td>
			</tr>

			<tr>
				<td width="55%" align="left" class="dotted"><?php echo $lang['server_ip'] .' ('. htmlspecialchars( $_SERVER['SERVER_ADDR'] ) . ')'?></td>
				<td width="45%" class="dotted">
					<input type="text" disabled="disabled" class="input" value="<?php echo htmlspecialchars( $_SERVER['SERVER_ADDR'] )   ?>" placeholder="<?php echo $lang['eg'] ?> 1.2.3.4" />
					<p><i><?php echo $lang['allowed_ip'] ?></i></p>
				</td>
			</tr>

			<tr>
				<td width="55%" align="left" class="dotted"><?php echo $lang['pub_key'] ?></td>
				<td width="45%" class="dotted">
					<code><?php echo sha1( $key ) .':'. htmlspecialchars( $_SERVER['SERVER_ADDR'] )  ?></code>
					<p><i><?php printf( $lang['blog_info'], '<a href="https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/" class="links" style="border-bottom:dotted 1px #FDCD25;">', '</a>' ) ?></i></p>
				</td>
			</tr>

			<tr>
				<td width="55%" align="left" class="dotted"><?php echo $lang['remote_url'] ?></td>
				<td width="45%" class="dotted">
					<textarea disabled="disabled" rows="8" style="background-color:#ffffff;font-family:monospace;font-size:13px;width:100%;border:1px solid #666666;" placeholder="http://example.org/index.php"></textarea>
					<p><i><?php echo $lang['one_per_line'] ?></i></p>
				</td>
			</tr>
		</table>
	</fieldset>

	<br />

	<center>
		<input type="submit" disabled="disabled" class="button" value="<?php echo $lang['save_options'] ?>">
	</center>

<br />
<?php

html_footer();

/* ------------------------------------------------------------------ */

function generate_clogs_seckey() {

	$key = '';
	for ( $i = 0; $i < 40; $i++ ) {
    $key .= chr( mt_rand( 33, 126 ) );
  }
  return $key;

}

/* ------------------------------------------------------------------ */
// EOF
