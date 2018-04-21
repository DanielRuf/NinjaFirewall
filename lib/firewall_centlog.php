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
	<h3><?php echo _('Logs > Centralized Logging') ?></h3>
	<br />
	<div class="alert alert-warning text-left"><?php
		printf(
			_('This feature is only available in the <a href="%s">Pro+ Edition</a> of NinjaFirewall.'),
			'https://nintechnet.com/ninjafirewall/pro-edition/'
		);
	?></div>

		<table width="100%" class="table table-nf">
			<tr>
				<td width="45%" align="left"><?php echo _('Enable Centralized Logging') ?></td>
				<td width="55%">
					<p><label><input type="radio" checked disabled />&nbsp;<?php echo _('Yes') ?></label></p>
					<p><label><input type="radio" disabled />&nbsp;<?php echo _('No') .' '. _('(default)') ?></label></p>
				</td>
			</tr>
		</table>

		<div id="clogs_table" style="border:1px #ddd solid;">

			<table width="100%" class="table table-borderless">
				<tr>
					<td width="45%" align="left"><?php echo _('Secret key') ?></td>
					<td width="55%">
						<input class="form-control" type="text" disabled value="<?php echo htmlentities( generate_clogs_seckey() ) ?>"  autocomplete="off" />
						<p><i><?php echo _('From 30 to 100 ASCII printable characters.') ?></i></p>
					</td>
				</tr>

				<tr>
					<td width="45%" align="left"><?php echo _("This server IP address") . ' ('. htmlspecialchars( $_SERVER['SERVER_ADDR'] ) . ')' ?></td>
					<td width="55%">
						<input type="text" class="form-control" disabled placeholder="<?php echo _('e.g.') ?> 1.2.3.4" />
						<p><i><?php echo _("Only this IP address (IPv4 or IPv6) will be allowed to connect to the remote websites. If you don't want to restrict the access by IP, enter the <code>*</code> character instead.") ?></i></p>
					</td>
				</tr>

				<tr>
					<td width="45%" align="left"><?php echo _('Public key') ?></td>
					<td width="55%" id="pubkey">
						<input type="text" class="form-control" value="<?php echo sha1( 'disabled' ) ?>" disabled />
						<p><i><?php
							printf(
								_('Add this key to the remote websites. <a href="%s">Consult our blog</a> for more info.'),
								'https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/'
							);
						?></i></p>
					</td>
				</tr>

				<tr>
					<td width="45%" align="left"><?php echo _('Remote websites URL') ?></td>
					<td width="55%">
						<textarea disabled rows="8" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="form-control" placeholder="http://example.org/index.php"></textarea>
						<p><i><?php echo _('Enter one URL per line, including the protocol (<code>http://</code> or <code>https://</code>). Only ASCII URLs are accepted.') ?></i></p>
					</td>
				</tr>
			</table>

		</div>

		<br />

		<center>
			<input type="submit" disabled class="btn btn-md btn-success btn-25" value="<?php echo _('Save Changes') ?>" />
		</center>
	</form>
</div>
<?php

html_footer();

// ---------------------------------------------------------------------

function generate_clogs_seckey() {

	$key = '';
	for ( $i = 0; $i < 40; ++$i ) {
    $key .= chr( mt_rand( 33, 126 ) );
  }
  return $key;

}

// ---------------------------------------------------------------------
// EOF
