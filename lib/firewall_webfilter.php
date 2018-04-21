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

html_header();

?>
<div class="col-sm-12 text-left">
	<h3><?php echo _('Monitoring > Web Filter') ?></h3>
	<br />
	<div class="alert alert-warning text-left"><?php
		printf(
			_('This feature is only available in the <a href="%s">Pro+ Edition</a> of NinjaFirewall.'),
			'https://nintechnet.com/ninjafirewall/pro-edition/'
		);
	?></div>

	<table width="100%" class="table table-nf">
		<tr>
			<td width="45%" align="left"><?php echo _('Enable Web Filter') ?></td>
			<td width="55%">
				<p><label><input type="radio" checked disabled />&nbsp;<?php echo _('Yes') ?></label></p>
				<p><label><input type="radio" disabled />&nbsp;<?php echo _('No') .' '. _('(default)') ?></label></p>
			</td>
		</tr>
	</table>

	<div id="wf_table" style="border:1px #ddd solid;">
		<table width="100%" class="table table-borderless">
			<tr>
				<td width="35%" align="center">
					<?php echo _('Search HTML page for:') ?>
					<br />
					<input type="text" disabled placeholder="<?php echo _('e.g.') ?> &lt;iframe" class="form-control" />
					<p><i><?php echo _('Min. 4, max. 150 characters.') ?></i></p>
					<br />
					<select class="form-control">
						<option value=""><?php echo _('Suggested keywords...') ?></option>
						<optgroup label="=== HTML / CSS"></optgroup>
						<option value="<iframe" title="<iframe">&lt;iframe</option>
						<option value="display:none" title="display:none">display:none</option>
						<option value="'hidden'" title="'hidden'">'hidden'</option>
						<option value='http-equiv="refresh"' title='http-equiv="refresh"'>http-equiv="refresh"</option>
						<option value="style.display" title="style.display">style.display</option>
						<option value="multipart/form-data" title="multipart/form-data">multipart/form-data</option>
						<optgroup label="=== JAVASCRIPT"></optgroup>
						<?php
						echo ';
						<option value="%u'.'00" title="%u'.'00">%u'.'00</option>
						<option value="\u'.'00" title="\u'.'00">\u'.'00</option>
						<option value=".appendChild" title=".appendChild">.appendChild</option>
						<option value="Activ'.'eXObject" title="Active'.'XObject">Active'.'XObject</option>
						<option value="encodeURIComponent" title="encodeURIComponent">encodeURIComponent</option>
						<option value="ev'.'al(" title="ev'.'al(">e'.'val(</option>
						<option value=".replace" title=".replace">.replace</option>
						<option value="une'.'scape" title="unes'.'cape">unes'.'cape</option>
						';
						?>
						<optgroup label="=== ERRORS"></optgroup>
						<option value="Fatal error:" title="Fatal error:">Fatal error:</option>
						<option value="Parse error:" title="Parse error:">Parse error:</option>
						<option value="<title>404 Not Found" title="<title>404 Not Found">&lt;title>404 Not Found</option>
						<option value="You have an error in your SQL syntax" title="You have an error in your SQL syntax">You have an error in your SQL syntax</option>
						<optgroup label="=== SHELL SCRIPTS"></optgroup>
						<option value="<?php echo htmlspecialchars( $_SERVER["DOCUMENT_ROOT"] ) ?>" title="<?php echo htmlspecialchars( $_SERVER["DOCUMENT_ROOT"] ) ?>"><?php echo htmlspecialchars( $_SERVER["DOCUMENT_ROOT"] ) ?></option>
						<?php
						echo '
						<option value="Hacked by" title="Hacked by">Hacked by</option>
						<option value="<title>ph'.'pinfo()" title="<title>php'.'info()">&lt;title>ph'.'pinfo()</option>
						<option value="Directory List" title="Directory List">Directory List</option>
						<option value="FTP brute" title="FTP brute">FTP brute</option>
						<option value="Run command" title="Run command">Run command</option>
						<option value="Dump database" title="Dump database">Dump database</option>
						<option value="Files'.'Man" title="File'.'sMan">Files'.'Man</option>
						<option value="Self remove" title="Self remove">Self remove</option>
						<option value="un'.'ame -a" title="un'.'ame -a">un'.'ame -a</option>
						<option value="c99m'.'adshell" title="c99'.'madshell">c99m'.'adshell</option>
						<option value="r57s'.'hell" title="r57s'.'hell">r57s'.'hell</option>
						<option value="c99s'.'hell" title="c99s'.'hell">c99s'.'hell</option>
						<option value="Open_ba'.'sedir" title="Open_'.'basedir">Open_b'.'asedir</option>
						<option value="phpMi'.'niAdmin" title="phpMi'.'niAdmin">phpM'.'iniAdmin</option>
						<option value="<title>Login - Adm'.'iner" title="<title>Login - Ad'.'miner">&lt;title>Login - Ad'.'miner</option>
						';
						?>
					</select>
				</td>

				<td width="30%" align="center"><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="<?php echo _('Add') ?> &#187;" disabled />
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="&#171; <?php echo _('Remove') ?>" disabled />
					<br />
					<br />
					<br />
					<p><label><input type="radio" disabled checked />&nbsp;<?php echo _('Case-sensitive') .' '. _('(default)') ?></label></p>
					<p><label><input type="radio" disabled />&nbsp;<?php echo _('Case-insensitive') ?></label></p>
				</td>
				<td width="35%" align="center">
				<?php echo _('Keywords to search:') ?>
				<br />
				<select multiple="multiple" class="form-control" disabled style="height:200px;">
				</select>
				</td>
			</tr>
		</table>

		<table width="100%" class="table table-borderless">
			<tr>
				<td align="left" width="40%"><?php echo _('Email alert') ?></td>
				<td align="left" width="60%">
				<?php
				$select = '<select disabled class="form-control" style="width:150px;display:inline">
					<option value="30"'. selected($nfw_options['wf_alert'], 30, 1) .'>'. _('30-minute') .'</option>
				</select>';
				printf( _('Do not send me more than one email alert in a %s interval.'), $select );
				?>
				<br />
				<i><?php echo _('Clicking the "Save Changes" button below will reset the current timer.') ?></i>
				<br />
				<br />
				<label><input type="checkbox" checked disabled >&nbsp;<?php echo _('Attach the HTML page output to the email alert') .' '. _('(default)') ?></label>
				</td>
			</tr>
		</table>
	</div>

	<br />

	<input type="hidden" name="mid" value="<?php echo $GLOBALS['mid'] ?>">
	<input type="hidden" name="post" value="1">
	<center><input type="submit" name="save-changes" disabled class="btn btn-md btn-success btn-25" value="<?php echo _('Save Changes') ?>"></center>

</div>
<?php

html_footer();

// ---------------------------------------------------------------------
// EOF
