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

$iso_csv = __DIR__ .'/share/iso3166.csv';

html_header();
?>
<div class="col-sm-12 text-left">
	<h3><?php echo _('Firewall > Access Control') ?></h3>
	<br />

	<div class="alert alert-warning text-left"><?php
		printf(
			_('This feature is only available in the <a href="%s">Pro+ Edition</a> of NinjaFirewall.'),
			'https://nintechnet.com/ninjafirewall/pro-edition/'
		);
	?></div>

	<ul class="nav nav-tabs">
		<li id="tab-1" class="active" onClick="nfw_switch_tabs(1)"><a class="dropdown-toggle" href="#"><?php echo _('General') ?></a></li>
		<li id="tab-2"><a href="#" onClick="nfw_switch_tabs(2)"><?php echo _('Geolocation') ?></a></li>
		<li id="tab-3"><a href="#" onClick="nfw_switch_tabs(3)"><?php echo _('IP Access Control') ?></a></li>
		<li id="tab-4"><a href="#" onClick="nfw_switch_tabs(4)"><?php echo _('URL Access Control') ?></a></li>
		<li id="tab-5"><a href="#" onClick="nfw_switch_tabs(5)"><?php echo _('Bot Access Control') ?></a></li>
	</ul>
	<br />

	<!-- General Access Control -->

	<div id="general-ac">


	<h4><?php echo _('Administrator') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="40%" align="left"><?php echo _('Whitelist the Administrator') ?></td>
				<td width="5%" align="center">&nbsp;</td>
				<td width="55%">
					<p><label><input type="radio" disabled />&nbsp;<?php echo _('Yes') ?></label></p>
					<p><label><input type="radio" checked disabled />&nbsp;<?php echo _('No') .' '. _('(default)') ?></label></p>
				</td>
			</tr>
			<tr>
				<td width="40%" align="left"><?php printf( _('Current status for user %s'), '<code>' . $nfw_options['admin_name'] . '</code>') ?></td>
				<td width="5%" align="center"><?php
					echo glyphicon('warning');
				?></td>
				<td width="55%"><?php
					echo _('You are not whitelisted.');
				?></td>
			</tr>
		</table>

	<a name="source-ip"></a>
	<br />

	<h4><?php echo _('Source IP') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="40%" align="left"><?php echo _('Retrieve visitors IP address from') ?></td>
				<td width="5%" align="center">&nbsp;</td>
				<td width="55%" align="left">
					<p><label><input type="radio" disabled checked />&nbsp;REMOTE_ADDR (<?php echo htmlspecialchars($_SERVER['REMOTE_ADDR']) ?>)</label></p>

					<p><label><input type="radio" disabled />&nbsp;HTTP_X_FORWARDED_FOR<?php
					if (! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
						echo ' ('. htmlspecialchars( $_SERVER['HTTP_X_FORWARDED_FOR'] ) .')';
					}
					?></label></p>
					<p><label><input type="radio" disabled />&nbsp;<?php echo _('Other') ?></label>&nbsp;<input class="form-control" type="text" style="display:inline;" placeholder="<?php echo _('e.g.') ?> HTTP_CLIENT_IP" disabled /></p>
				</td>
			</tr>

			<tr>
				<td width="40%" align="left"><?php echo _('Scan traffic coming from localhost and private IP address spaces') ?></td>
				<td width="5%" align="center">&nbsp;</td>
				<td width="55%" align="left">
					<p><label><input type="radio" disabled checked />&nbsp;<?php echo _('Yes') .' '. _('(default)') ?></label></p>

					<p><label><input type="radio" disabled />&nbsp;<?php echo _('No') ?></label></p>
				</td>
			</tr>
		</table>

	<br />

	<h4><?php echo _('HTTP Methods') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="40%" align="left"><?php echo _('All Access Control directives should apply to the following HTTP methods') ?></td>
				<td width="5%" align="center">&nbsp;</td>
				<td width="55%">
					<p><label><input type="checkbox" checked disabled />&nbsp;GET <?php echo _('(default)') ?></label></p>
					<p><label><input type="checkbox" checked disabled />&nbsp;POST <?php echo _('(default)') ?></label></p>
					<p><label><input type="checkbox" checked disabled />&nbsp;HEAD <?php echo _('(default)') ?></label></p>
					<p><label><input type="checkbox" checked disabled />&nbsp;PUT <?php echo _('(default)') ?></label></p>
					<p><label><input type="checkbox" checked disabled />&nbsp;DELETE <?php echo _('(default)') ?></label></p>
					<p><label><input type="checkbox" checked disabled />&nbsp;PATCH <?php echo _('(default)') ?></label></p>
				</td>
			</tr>
		</table>

	</div>

	<!-- GeoIP Access Control -->

	<div id="geolocation-ac" style="display:none">

		<h4><?php echo _('Geolocation Access Control') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="40%" align="left"><?php echo _('Enable Geolocation') ?></td>
				<td width="5%" align="center">&nbsp;</td>
				<td width="55%">
					<p><label><input type="radio" disabled />&nbsp;<?php echo _('Yes') ?></label></p>

					<p><label><input type="radio" checked disabled />&nbsp;<?php echo _('No') .' '. _('(default)') ?></label></p>

				</td>
			</tr>
		</table>

		<div id="geotable" style="border:1px #ddd solid;">

			<table width="100%" class="table table-borderless">
				<tr>
					<td width="40%" align="left"><?php echo _('Retrieve ISO 3166 country code from') ?></td>
					<td width="5%" align="center"><?php
					if (! empty( $no_db) || ! empty( $no_var ) ) {
						echo glyphicon('error');
					}
					?></td>
					<td width="55%" style="vertical-align:top;">
						<p><label><input type="radio" checked disabled />&nbsp;NinjaFirewall <?php echo _('(default)') ?></label></p>
						<p><label><input type="radio" disabled />&nbsp;<?php echo _('PHP variable') ?></label> <input type="text"  placeholder="<?php echo _('e.g.') ?> GEOIP_COUNTRY_CODE" class="form-control" style="display:inline" disabled /></p>
					</td>
				</tr>
			</table>

			<br />

			<table width="100%" class="table table-borderless">
				<tr>
					<td width="35%" align="center" valign="top" style="vertical-align:top;"><?php echo _('Available countries:') ?><br />
						<select multiple size="8" class="form-control" style="height:200px">
						<?php
						$csv_array = file( $iso_csv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
						foreach ($csv_array as $line) {
							if ( preg_match( '/^(\w\w),"(.+?)"$/', $line, $match ) ) {
								echo '<option title="' . htmlspecialchars( $match[2] ) . '" value="' . htmlspecialchars( $match[1] ) . '">' . htmlspecialchars( $match[1] ) . ' ' . htmlspecialchars( $match[2] ) . '</option>';
							}
						}
						?>
						</select>
					</td>

					<td width="30%" align="center">
						<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="<?php echo _('Block') ?> &#187;" disabled />
						<br />
						<br />
						<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="&#171; <?php echo _('Unblock') ?>" disabled />
						<br />
						<br />
						<br />
						<br />
						<label><input type="checkbox" checked disabled />&nbsp;<?php echo _('Log event') .' '. _('(default)') ?></label>
					</td>

					<td width="35%" align="center" style="vertical-align:top;"><?php echo _('Blocked countries:') ?><br />
						<select multiple="multiple" size="8" class="form-control" style="height:200px" disabled></select>
					</td>
				</tr>
			</table>

			<br />

			<table width="100%" class="table table-borderless">
				<tr>
					<td width="35%" align="left">
						<?php echo _('Geolocation should apply to the whole site or specific URLs only?') ?>
						<br />
						<br />
						<i><?php echo _('(leave it blank if you want geolocation to apply to the whole site)') ?></i>
					</td>
					<td width="30%" align="center">
						<input type="text" class="form-control" style="width:200px;" placeholder="<?php echo _('e.g.') ?> /script.php" disabled />
						<br />
						<i><?php echo _('Full or partial case-sensitive URL.') ?></i>
						<br /><br />
						<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="<?php echo _('Block') ?> &#187;" disabled />
						<br /><br />
						<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="&#171; <?php echo _('Unblock') ?>" disabled />
					</td>
					<td width="35%" align="center">
						<?php echo _('Blocked URLs:') ?>
						<br />
						<select multiple="multiple" size="8" disabled class="form-control" style="height:200px">
						</select>
					</td>
				</tr>
			</table>

			<br />

			<table width="100%" class="table table-borderless">
				<tr>
					<td width="40%" align="left"><?php echo _('Add NINJA_COUNTRY_CODE to PHP headers') ?></td>
					<td width="5%" align="center">
					<td width="55%" align="left">
						<p><label><input type="radio" disabled />&nbsp;<?php echo _('Yes') ?></label></p>
						<p><label><input type="radio" checked disabled />&nbsp; <?php echo _('No') .' '. _('(default)') ?></label></p>
					</td>
				</tr>
			</table>

		</div>
		<br />

	</div>

	<!-- IP Access Control -->

	<div id="ip-ac" style="display:none">

	<h4><?php echo _('IP Access Control') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="25%" align="left"><?php echo _('Allow the following IPs') ?></td>
				<td width="35%" align="center">
					<input type="text" class="form-control" disabled style="width:200px;" value="" placeholder="<?php echo _('e.g.') ?> 1.2.3.4 or 1.2.3" />
					<br />
					<i><?php echo _('Full or partial IPv4/IPv6 address.') ?></i>
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="<?php echo _('Allow') ?> &#187;" disabled />
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="&#171; <?php echo _('Discard') ?>" disabled/>
					<br />
					<br />
					<label><input type="checkbox" checked disabled />&nbsp;<?php echo _('Log event') ?></label>
				</td>
				<td width="40%" align="center">
					<?php echo _('Allowed IPs:') ?>
					<br />
					<select multiple="multiple" size="8" disabled class="form-control" style="height:200px">
					</select>
					<br />&nbsp;
				</td>
			</tr>

			<tr>
				<td width="25%" align="left"><?php echo _('Block the following IPs') ?></td>
				<td width="35%" align="center">
					<input type="text" class="form-control" disabled style="width:200px;" value="" placeholder="<?php echo _('e.g.') ?> 1.2.3.4 or 1.2.3" />
					<br />
					<i><?php echo _('Full or partial IPv4/IPv6 address.') ?></i>
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="<?php echo _('Block') ?> &#187;" disabled />
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="&#171; <?php echo _('Unblock') ?>" disabled />
					<br />
					<br />
					<label><input type="checkbox" checked disabled />&nbsp;<?php echo _('Log event') .' '. _('(default)') ?></label>
				</td>
				<td width="40%" align="center">
					<?php echo _('Blocked IPs:') ?>
					<br />
					<select multiple="multiple" size="8" disabled class="form-control" style="height:200px">
					</select><br />&nbsp;
				</td>
			</tr>

			<tr>
				<td width="25%" align="left"><?php echo _('Rate limiting') ?></td>
				<td width="35%" align="center">

					<p style="line-height:45px;">

					<input type="radio" disabled >&nbsp;
					<?php
					$string = 'Block for %s seconds any IP with more than %s connections within a %s interval.';
					$a = '<input type="number" min="1" class="form-control" style="width:90px;display:inline" name="ac_rl_time" value="30" disabled size="2" maxlength="3" />';
					$b = '<input type="number" min="1" class="form-control" style="width:90px;display:inline" id="acrlconn" name="ac_rl_conn" value="10" size="2" maxlength="3" disabled />';
					$c = '<select class="form-control" style="width:150px;display:inline" disabled><option value="5" selected>'. _('5-second') .'</option></select>';
					printf( $string, $a, $b, $c );
					?>

					</p>

					<p>
					<label><input type="checkbox" disabled checked />&nbsp;<?php echo _('Log event') . ' ' . _('(default)') ?></label>
					</p>

				</td>
				<td width="40%" align="center">

					<label><input type="radio" checked disabled />&nbsp;<?php echo _('Disabled') . ' ' . _('(default)') ?></label>
				</td>
			</tr>

		</table>

	</div>


	<!-- URL Access Control -->

	<div id="url-ac" style="display:none">

	<h4><?php echo _('URL Access Control') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="25%" align="left"><?php echo _('Allow access to the following URL (SCRIPT_NAME)') ?></td>
				<td width="35%" align="center">
					<input type="text" class="form-control" disabled maxlength="45" style="width:200px;" value="" placeholder="<?php echo _('e.g.') ?> /script.php" />
					<br />
					<i><?php echo _('Full or partial case-insensitive string.') ?></i>
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="<?php echo _('Allow') ?> &#187;" disabled />
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="&#171; <?php echo _('Discard') ?>" disabled />
					<br />
					<br />
					<label><input type="checkbox" checked disabled />&nbsp;<?php echo _('Log event') ?></label>
				</td>
				<td width="40%" align="center">
					<?php echo _('Allowed URLs:') ?>
					<br />
					<select multiple="multiple" size="8" disabled class="form-control" style="height:200px">
					</select>
					<br />&nbsp;
				</td>
			</tr>
			<tr>
				<td width="25%" align="left"><?php echo _('Block access to the following URL (SCRIPT_NAME)') ?></td>
				<td width="35%" align="center">
					<input type="text" class="form-control" disabled style="width:200px;" placeholder="<?php echo _('e.g.') ?> /cache/" />
					<br />
					<i><?php echo _('Full or partial case-sensitive URL.') ?></i>
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="<?php echo _('Block') ?> &#187;" disabled />
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="&#171; <?php echo _('Unblock') ?>" disabled />
					<br />
					<br />
					<label><input type="checkbox" checked disabled />&nbsp;<?php echo _('Log event') . ' ' . _('(default)') ?></label>
				</td>
				<td width="40%" align="center">
					<?php echo _('Blocked URLs:') ?>
					<br />
					<select multiple="multiple" size="8" disabled class="form-control" style="height:200px">
					</select>
				</td>
			</tr>

		</table>

	</div>


	<!-- Bot Access Control -->

	<div id="bot-ac" style="display:none">

	<h4><?php echo _('Bot Access Control') ?></h4>
		<table width="100%" class="table table-nf">
			<tr>
				<td width="25%" align="left"><?php echo _('Reject the following bots (HTTP_USER_AGENT)') ?></td>
				<td width="35%" align="center">
					<input type="text" class="form-control" disabled style="width:200px;" value="" placeholder="<?php echo _('e.g.') ?> BOT for JCE" />
					<br />
					<i><?php echo _('Full or partial case-insensitive string.') ?></i>
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="<?php echo _('Block') ?> &#187;" disabled />
					<br /><br />
					<input type="button" style="width:150px" class="btn btn-md btn-default btn-25" value="&#171; <?php echo _('Unblock') ?>" disabled />
					<br />
					<br />
					<label><input type="checkbox" checked disabled />&nbsp;<?php echo _('Log event') . ' ' . _('(default)') ?></label>
				</td>
				<td width="40%" align="center">
					<?php echo _('Blocked bots:') ?>
					<br />
					<select multiple="multiple" size="8" disabled class="form-control" style="height:200px">
					</select>
					<p>
					<a href="javascript:void(0)"><?php echo _('Restore default list') ?></a>
					</p>
				</td>
			</tr>

		</table>

	</div>

	<br />

	<center>
		<input type="submit" name="save-changes" class="btn btn-md btn-success btn-25" value="<?php echo _('Save Changes') ?>" disabled />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" name="restore-changes" class="btn btn-md btn-default btn-25" value="<?php echo _('Restore Default Values') ?>" disabled />
	</center>

</div>

<?php

html_footer();

// ---------------------------------------------------------------------
// EOF
