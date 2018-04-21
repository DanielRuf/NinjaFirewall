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
	<h3><?php echo _('Firewall > Rules Editor') ?></h3>
	<br />
<?php

// Saved options ?
if (! empty($_POST) ) {
	$err_msg = ''; $is_update = ''; $ok_msg = '';
	if ( isset( $_POST['sel_e_r'] ) ) {
		if ( $_POST['sel_e_r'] < 1 ) {
			$err_msg = _('You did not select a rule.');
		} else if ( $_POST['sel_e_r'] == 2 || $_POST['sel_e_r'] > 499 && $_POST['sel_e_r'] < 600 ) {
			$err_msg = _('To change this rule, use the "Firewall Policies" menu.');
		} else if (! isset( $nfw_rules[$_POST['sel_e_r']] ) ) {
			$err_msg = _('This rule does not exist.');
		} else {
			$nfw_rules[$_POST['sel_e_r']]['ena'] = 0;
			$is_update = 1;
			$ok_msg = sprintf( _('Rule #%s has been disabled'), (int) $_POST['sel_e_r'] );
		}
	} else if ( isset( $_POST['sel_d_r'] ) ) {
		if ( $_POST['sel_d_r'] < 1 ) {
			$err_msg = _('You did not select a rule.');
		} else if ( $_POST['sel_d_r'] == 2 || $_POST['sel_d_r'] > 499 && $_POST['sel_d_r'] < 600 ) {
			$err_msg = _('To change this rule, use the "Firewall Policies" menu.');
		} else if (! isset( $nfw_rules[$_POST['sel_d_r']] ) ) {
			$err_msg = _('This rule does not exist.');
		} else {
			$nfw_rules[$_POST['sel_d_r']]['ena'] = 1;
			$is_update = 1;
			$ok_msg = sprintf( _('Rule #%s has been enabled'), (int) $_POST['sel_d_r'] );
		}
	}
   if ( $is_update ) {
		$err_msg = save_firewall_rules_editor();
	}
	if ($err_msg) {
      echo '<div class="alert alert-danger text-left"><a class="close" '.
		'data-dismiss="alert" aria-label="close">&times;</a>'. $err_msg .'</div>';
   } else {
      echo '<div class="alert alert-success text-left"><a class="close" data-dismiss="alert"'.
		' aria-label="close">&times;</a>'. $ok_msg . '</div>';
   }
}

$disabled_rules = array();
$enabled_rules = array();

if ( empty( $nfw_rules ) ) {
	echo '<div class="alert alert-danger text-left">'.
		_('Fatal error: No rules found. Reinstall NinjaFirewall to solve the problem.') .
		'</div>';
	echo '</div>';
	html_footer();
}

foreach ( $nfw_rules as $rule_key => $rule_value ) {

	// Ingore firewall policies:
	if ( $rule_key == 2 || $rule_key > 499 && $rule_key < 600 ) {
		continue;
	}

	if (! empty( $nfw_rules[$rule_key]['ena'] ) ) {
		$enabled_rules[] =  $rule_key;
	} else {
		$disabled_rules[] = $rule_key;
	}
}
?>
	<table width="100%" class="table table-nf">
		<tr>
			<td width="40%" align="left"><?php echo _('Select the rule you want to enable or disable') ?></td>
			<td width="5%" align="center">&nbsp;</td>
			<td width="55%" align="left">
				<form method="post">
					<select name="sel_e_r" class="form-control">
					<option value="0"><?php echo _('Enabled rules:') . ' ' . count( $enabled_rules ) ?></option>
					<?php
					sort($enabled_rules);
					$count = 0;
					$desr = '';
					foreach ( $enabled_rules as $key ) {
						if ( $key < 100 ) {
							$desc = ' '. _('Remote/local file inclusion');
						} elseif ( $key < 150 ) {
							$desc = ' '. _('Cross-site scripting');
						} elseif ( $key < 200 ) {
							$desc = ' '. _('Code injection');
						} elseif (  $key > 249 && $key < 300 ) {
							$desc = ' '. _('SQL injection');
						} elseif ( $key < 350 ) {
							$desc = ' '. _('Various vulnerability');
						} elseif ( $key < 400 ) {
							$desc = ' '. _('Backdoor/shell');
						} elseif ( $key > 999 && $key < 1300 ) {
							$desc = ' '. _('Application specific');
						}
						echo '<option value="' . htmlspecialchars( $key ) . '">' . _('Rule #') . htmlspecialchars( $key ) . $desc . '</option>';
						++$count;
					}
					?>
					</select>
					<br />
					<input class="btn btn-sm btn-success btn-35" type="submit" value="<?php echo _('Disable it') ?>"<?php disabled( $count, 0) ?>>
				</form>

				<br />
				<br />

				<form method="post">
					<select name="sel_d_r" class="form-control">
					<option value="0"><?php echo _('Disabled rules:') . ' ' . count( $disabled_rules ) ?></option>
					<?php
					$count = 0;
					sort($disabled_rules);
					foreach ( $disabled_rules as $key ) {
						if ( $key < 100 ) {
								$desc = ' '. _('Remote/local file inclusion');
							} elseif ( $key < 150 ) {
								$desc = ' '. _('Cross-site scripting');
							} elseif ( $key < 200 ) {
								$desc = ' '. _('Code injection');
							} elseif (  $key > 249 && $key < 300 ) {
								$desc = ' '. _('SQL injection');
							} elseif ( $key < 350 ) {
								$desc = ' '. _('Various vulnerability');
							} elseif ( $key < 400 ) {
								$desc = ' '. _('Backdoor/shell');
							} elseif ( $key > 999 && $key < 1300 ) {
								$desc = ' '. _('Application specific');
							}
							echo '<option value="' . htmlspecialchars( $key ) . '">' . _('Rule #') . htmlspecialchars( $key ) . $desc . '</option>';
							++$count;
						}
					?>
					</select>
					<br />
					<input class="btn btn-sm btn-success btn-35" type="submit" value="<?php echo _('Enable it') ?>"<?php disabled( $count, 0) ?>>
				</form>
			</td>
		</tr>
	</table>
</div>
<?php

html_footer();

// ---------------------------------------------------------------------

function save_firewall_rules_editor() {

	global $nfw_rules;

	// Save rules:
	$res = save_config( $nfw_rules, 'rules' );
	if (! empty( $res ) ) {
		return $res;
	}

	return;
}

// ---------------------------------------------------------------------
// EOF
