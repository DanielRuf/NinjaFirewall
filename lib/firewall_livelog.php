<?php
/*
 +---------------------------------------------------------------------+
 | NinjaFirewall (Pro edition)                                         |
 |                                                                     |
 | (c) NinTechNet - http://nintechnet.com/                             |
 |                                                                     |
 +---------------------------------------------------------------------+
 | REVISION: 2015-03-14 00:28:38                                       |
 +---------------------------------------------------------------------+
 | IMPORTANT: This is a commercial software product, please read its   |
 |            license agreement. If you did not receive a copy, please |
 |            consult : <http://ninjafirewall.com/license_pro.txt>     |
 |            This notice may not be removed from the source code.     |
 +---------------------------------------------------------------------+
*/
if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

// Load current language file :
require ('./lib/lang/' . $nfw_options['admin_lang'] . '/' . basename(__FILE__) );

html_header();
echo '<br /><div class="warning"><p>' . $lang['pro_only'] . ' (<a class="links" style="border-bottom:1px dotted #FFCC25;" href="http://ninjafirewall.com/pro/order.php">'. $lang['lic_upgrade'] . '</a>).</p></div>';
?>
<br />
<fieldset><legend>&nbsp;<b><?php echo $lang['livelog'] ?></b>&nbsp;</legend>
	<table width="100%" class="smallblack" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="width:100%;text-align:center;">
				<i class="tinyblack" id="loading">&nbsp;</i><br />
				<textarea style="background-color:#ffffff;width:95%;height:250px;border:1px solid #FDCD25;padding:4px;font-family:monospace;font-size:13px;" wrap="off" disabled="disabled"></textarea>
				<center>
					<p>
						<label><input type="radio" disabled="disabled"><?php echo $lang['on'] ?></label>&nbsp;&nbsp;<label><input type="radio" checked="checked" disabled="disabled"><?php echo $lang['off'] ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lang['refresh'] ?>
						<select class="input" disabled="disabled">
						<option selected="selected">5 <?php echo $lang['seconds'] ?></option>
						</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $lang['cls'] ?>" disabled="disabled" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="checkbox" checked="checked" disabled="disabled"><?php echo $lang['scroll'] ?></label>
					</p>
				</center>
			</td>
		</tr>
	</table>
	<div align="right"><i class="tinyblack"><?php echo $lang['whitelisted'] ?></i></div>
</fieldset>
<br />
<br />
<fieldset><legend>&nbsp;<b><?php echo $lang['options'] ?></b>&nbsp;</legend>
	<table width="100%" class="smallblack" border="0" cellpadding="10" cellspacing="0">
		<tr>
			<td width="30%" align="left"><?php echo $lang['log_format'] ?></td>
			<td width="70%" align="left">
				<p><label><input type="radio" disabled="disabled" checked="checked"><code>[%time] %name %client &quot;%method %uri&quot; &quot;%referer&quot; &quot;%ua&quot; &quot;%forward&quot; &quot;%server&quot;</code></label></p>
				<p><label><input type="radio" disabled="disabled"><?php echo $lang['custom'] ?> </label><input type="text" class="input" size="45" disabled="disabled"></p>
				<i class="tinyblack"><?php echo $lang['help'] ?></i>
			</td>
		</tr>
		<tr>
			<td width="30%" align="left"><?php echo $lang['display'] ?></td>
			<td width="70%" align="left">
				<select class="input" disabled="disabled">
					<option><?php echo $lang['httphttps'] ?></option>
					<option><?php echo $lang['http'] ?></option>
					<option><?php echo $lang['https'] ?></option>
				</select>
			</td>
		</tr>
	</table>
</fieldset>
<center><p><input type="submit" disabled="disabled" value="<?php echo $lang['save'] ?>" /></p></center>
<?php

html_footer();

/* ------------------------------------------------------------------ */
// EOF
