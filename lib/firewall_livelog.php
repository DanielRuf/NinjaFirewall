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
	<h3><?php echo _('Logs > Live Log') ?></h3>
	<br />
	<div class="alert alert-warning text-left"><?php
		printf(
			_('This feature is only available in the <a href="%s">Pro+ Edition</a> of NinjaFirewall.'),
			'https://nintechnet.com/ninjafirewall/pro-edition/'
		);
	?></div>

		<table width="100%" class="table table-nf">
			<tr>
				<td style="width:100%;">
					<center>
						<i id="loading">&nbsp;</i>
						<br />
						<textarea disabled class="form-control" style="background-color:#ffffff;width:95%;height:250px;font-family:monospace;font-size:14px;" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?php echo _('No traffic yet, please wait...'); echo "\n"; ?></textarea>
						<br />
						<label><input type="radio" disabled>&nbsp;<?php echo _('On') ?></label>&nbsp;&nbsp;
						<label><input type="radio" disabled checked="checked" />&nbsp;<?php echo _('Off') ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php echo _('Refresh rate:') ?>
						<select class="form-control" style="width:130px;display:inline" disabled>
							<option value="5000" selected >5 <?php echo _('seconds') ?></option>
						</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" disabled class="btn btn-md btn-default btn-25" value="<?php echo _('Clear screen') ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label><input type="checkbox" disabled checked />&nbsp;<?php echo _('Autoscrolling') ?></label>
					</center>
				</td>
			</tr>
		</table>
	<div align="right"><i><?php echo _('Live Log will not show connections from the administrator.') ?></i></div>

	<br />

		<h4><?php echo _('Options') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="45%" align="left"><?php echo _('Log format') ?></td>
				<td width="55%" align="left">
					<p>
						<label><input type="radio" disabled checked />&nbsp;<?php echo _('Default') ?>&nbsp;</label><input type="text" class="form-control" value="<?php echo '[%time] %name %client &quot;%method %uri&quot; &quot;%referrer&quot; &quot;%ua&quot; &quot;%forward&quot; &quot;%host&quot;' ?>" readonly disabled autocomplete="off">
					</p>
					<p>
						<label><input type="radio" disabled >&nbsp;<?php echo _('Custom') ?>&nbsp;</label><input type="text" class="form-control" disabled autocomplete="off" />
					</p>
					<i><?php echo _('See contextual help for available log format.') ?></i>
				</td>
			</tr>
			<tr>
				<td width="45%" align="left"><?php echo _('Display') ?></td>
				<td width="55%" align="left">
					<select disabled class="form-control">
						<option value="0"><?php echo _('HTTP and HTTPS traffic (default)') ?></option>
					</select>
				</td>
			</tr>
		</table>
		<center><p><input type="submit" disabled class="btn btn-md btn-success btn-25" value="<?php echo _('Save Options') ?>" /></p></center>
</div>
<?php

html_footer();

// ---------------------------------------------------------------------
// EOF
