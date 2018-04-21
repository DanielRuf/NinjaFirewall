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
	<h3><?php echo _('Monitoring > File Guard') ?></h3>
	<br />
	<div class="alert alert-warning text-left"><?php
		printf(
			_('This feature is only available in the <a href="%s">Pro+ Edition</a> of NinjaFirewall.'),
			'https://nintechnet.com/ninjafirewall/pro-edition/'
		);
	?></div>

	<table width="100%" class="table table-nf">
		<tr>
			<td width="45%" align="left"><?php echo _('Enable File Guard') ?></td>
			<td width="55%">
				<p><label><input type="radio" checked disabled />&nbsp;<?php echo _('Yes') ?></label></p>
				<p><label><input type="radio" disabled />&nbsp;<?php echo _('No') .' '. _('(default)') ?></label></p>
			</td>
		</tr>
	</table>

	<div id="fg_table" style="border:1px #ddd solid;">
		<table width="100%" class="table table-borderless">
			<tr>
				<td width="45%" align="left"><?php echo _('Real-time detection') ?></td>
				<td width="55%">
					<?php
					printf(
						'Monitor file activity and send an alert when someone is accessing a PHP script that was modified or created less than %s hour(s) ago.',
						'<input class="form-control" style="width:90px;display:inline" maxlength="2" size="2" min="1" value="10" disabled type="number" />'
					);
					?>
				</td>
			</tr>
			<tr>
				<td width="45%" align="left"><?php echo _('Exclude the following files/folders (optional)') ?></td>
				<td width="55%"><p><input class="form-control" maxlength="255" type="text" disabled placeholder="<?php echo _('e.g.') ?> /foo/bar/cache/" /><br /><i><?php echo _('Full or partial case-sensitive string(s), max. 255 characters. Multiple values must be comma-separated.') ?></i></p></td>
			</tr>
		</table>
	</div>

	<br />


	<center><input type="submit" disabled name="save-changes" class="btn btn-md btn-success btn-25" value="<?php echo _('Save Changes') ?>"></center>

</div>
<?php

html_footer();

// ---------------------------------------------------------------------
// EOF
