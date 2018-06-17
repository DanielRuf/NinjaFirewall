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

// Required, otherwise "X-Content-Type-Options: nosniff"
// header will block access to this script:
header("Content-Type: text/javascript");

// i18n:
if ( @include '../conf/options.php' ) {
	$nfw_options = unserialize( $nfw_options );
	require_once '../lib/locale.php';
}

if ( empty( $_GET['mid'] ) ) {
	$mid = 10;
} else {
	$mid = (int) $_GET['mid'];
}
// =====================================================================
?>
function disconnect( who ) {
	if ( confirm( "<?php printf( _('Close the session for [%s]?'), '"+who+"' ) ?>") ) {
		return true;
	}
	return false;
}

<?php
// =====================================================================
// Summary > Statistics:

if ( $mid == 10 ) {
	?>
	function popup(url,width,height,scroll_bar) {
		height=height+20;width=width+20;
		var str = "height=" + height + ",innerHeight=" + height;
		str += ",width=" + width + ",innerWidth=" + width;
		if (window.screen){
			var ah = screen.availHeight - 30;
			var aw = screen.availWidth -10;
			var xc = (aw - width) / 2;
			var yc = (ah - height) / 2;
			str += ",left=" + xc + ",screenX=" + xc;
			str += ",top=" + yc + ",screenY=" + yc;
			if (scroll_bar) {
				str += ",scrollbars=no";
			}else {
				str += ",scrollbars=yes";
			}
			str += ",status=no,location=no,resizable=yes";
		}
		win = open(url, "nfpop", str);
		setTimeout("win.window.focus()",300);
	}
<?php
// =====================================================================
// Summary > Statistics:

} elseif ( $mid == 11 ) {
	?>
	function stat_redir( where, token ) {
		if ( where == '' ) { return false; }
		document.location.href='?mid=11&token='+ token + '&xtr=' + where;
	}
<?php
// =====================================================================
// Account > Options

} elseif ( $mid == 20 ) {
	?>
	function ssl_warn() {
		<?php
		// Obviously, if we are already in HTTPS mode, we don't send any warning:
		if ($_SERVER['SERVER_PORT'] == 443 ) {
		?>
			return true;
		<?php
		} else {
		?>
			if (document.nf_options.admin_ssl.checked == false) { return true;}
			if (confirm("<?php echo _('Warning: ensure that you can access your admin console over HTTPS before enabling this option, otherwise you will lock yourself out of your site. Continue?') ?>") ) {
				return true;
			}
			return false;
		<?php
		}
		?>
	}
<?php
// =====================================================================
// Firewall > Options

} elseif ( $mid == 30 ) {
	?>
	function preview_msg( rem_addr ) {
		var t1 = document.option_form.blocked_msg.value.replace('%%REM_ADDRESS%%', rem_addr);
		var t2 = t1.replace('%%NUM_INCIDENT%%','1234567');
		document.getElementById('out_msg').innerHTML = t2 + '<br /><br /><br />';
		jQuery("#td_msg").slideDown();
		document.getElementById('btn_msg').value = "<?php echo _('Refresh preview') ?>";
	}
	function default_msg() {
		document.option_form.blocked_msg.value = document.option_form.def_msg.value;
	}
	function chktime(){
		if ( (document.option_form.ban_time.value) && (!document.option_form.ban_time.value.match(/^[1-9][0-9]?[0-9]?$/)) ){
			alert("<?php echo _('You can only use digits from 1 to 999.') ?>");
			document.option_form.ban_time.value = document.option_form.ban_time.value.substring(0, document.option_form.ban_time.value.length-1);
		}
	}
	function baninput(what) {
		if (what == 0) {
			document.option_form.ban_time.disabled = true;
		} else {
			document.option_form.ban_time.disabled = false;
			document.option_form.ban_time.select();
		}
	}
	function chkflds(){
		if ( (document.option_form.ban_ip.value > 0) && (! document.option_form.ban_time.value.match(/^[1-9][0-9]?[0-9]?$/)) ){
			alert("<?php echo _('Please enter the number of minutes IPs should be banned.') ?>");
			document.option_form.ban_time.select();
			return false;
		}
		return true;
	}

<?php
// =====================================================================
// Firewall > Policies

} elseif ( $mid == 31 ) {
?>
	function nfw_switch_tabs(tab) {
		if ( tab == 1 ) {
			jQuery("#basic-options").show(); jQuery("#tab-1").addClass("active");
			jQuery("#intermediate-options").hide(); jQuery("#tab-2").removeClass("active");
			jQuery("#advanced-options").hide(); jQuery("#tab-3").removeClass("active");

		} else if ( tab == 2 ) {
			jQuery("#basic-options").hide(); jQuery("#tab-1").removeClass("active");
			jQuery("#intermediate-options").show(); jQuery("#tab-2").addClass("active");
			jQuery("#advanced-options").hide(); jQuery("#tab-3").removeClass("active");

		} else if ( tab ==3 ) {
			jQuery("#basic-options").hide(); jQuery("#tab-1").removeClass("active");
			jQuery("#intermediate-options").hide(); jQuery("#tab-2").removeClass("active");
			jQuery("#advanced-options").show(); jQuery("#tab-3").addClass("active");
		}
	}
	function sanitise_warn(cbox) {
		if(cbox.checked) {
			if (confirm("<?php echo _('Any character that is not a letter [a-zA-Z], a digit [0-9], a dot [.], a hyphen [-] or an underscore [_] will be removed from the filename and replaced with the substitution character. Continue?') ?>")){
				return true;
			}
			return false;
		}
	}
	function is_number(id) {
		var e = document.getElementById(id);
		if (! e.value ) { return }
		if (! /^[0-9]+$/.test(e.value) ) {
			alert("<?php echo _('Please enter numbers only.') ?>");
			e.value = e.value.substring(0, e.value.length-1);
		}
	}
	function san_onoff(what) {
		if (what == 0) {
			document.fwrules.sanid.disabled = true;
			document.fwrules.sizeid.disabled = true;
			document.fwrules.subs.disabled = true;
		} else {
			document.fwrules.sanid.disabled = false;
			document.fwrules.sizeid.disabled = false;
			document.fwrules.subs.disabled = false;
		}
	}
	function csp_onoff(what, csp) {
		if (what == 0) {
			document.getElementById(csp).readOnly = true;
		} else {
			document.getElementById(csp).readOnly = false;
			document.getElementById(csp).focus();
		}
	}
	function restore() {
		if (confirm("<?php echo _('All fields will be restored to their default values. Continue?') ?>")){
			return true;
		}else{
			return false;
		}
	}
<?php
// =====================================================================
// Firewall > Access Control

} elseif ( $mid == 32 ) {
	require_once '../lib/constants.php';
	?>
	function nfw_switch_tabs(tab) {
		if ( tab == 1 ) {
			jQuery("#general-ac").show(); jQuery("#tab-1").addClass("active");
			jQuery("#geolocation-ac").hide(); jQuery("#tab-2").removeClass("active");
			jQuery("#ip-ac").hide(); jQuery("#tab-3").removeClass("active");
			jQuery("#url-ac").hide(); jQuery("#tab-4").removeClass("active");
			jQuery("#bot-ac").hide(); jQuery("#tab-5").removeClass("active");

		} else if ( tab == 2 ) {
			jQuery("#general-ac").hide(); jQuery("#tab-1").removeClass("active");
			jQuery("#geolocation-ac").show(); jQuery("#tab-2").addClass("active");
			jQuery("#ip-ac").hide(); jQuery("#tab-3").removeClass("active");
			jQuery("#url-ac").hide(); jQuery("#tab-4").removeClass("active");
			jQuery("#bot-ac").hide(); jQuery("#tab-5").removeClass("active");

		} else if ( tab ==3 ) {
			jQuery("#general-ac").hide(); jQuery("#tab-1").removeClass("active");
			jQuery("#geolocation-ac").hide(); jQuery("#tab-2").removeClass("active");
			jQuery("#ip-ac").show(); jQuery("#tab-3").addClass("active");
			jQuery("#url-ac").hide(); jQuery("#tab-4").removeClass("active");
			jQuery("#bot-ac").hide(); jQuery("#tab-5").removeClass("active");


		} else if ( tab == 4 ) {
			jQuery("#general-ac").hide(); jQuery("#tab-1").removeClass("active");
			jQuery("#geolocation-ac").hide(); jQuery("#tab-2").removeClass("active");
			jQuery("#ip-ac").hide(); jQuery("#tab-3").removeClass("active");
			jQuery("#url-ac").show(); jQuery("#tab-4").addClass("active");
			jQuery("#bot-ac").hide(); jQuery("#tab-5").removeClass("active");


		} else if ( tab == 5 ) {
			jQuery("#general-ac").hide(); jQuery("#tab-1").removeClass("active");
			jQuery("#geolocation-ac").hide(); jQuery("#tab-2").removeClass("active");
			jQuery("#ip-ac").hide(); jQuery("#tab-3").removeClass("active");
			jQuery("#url-ac").hide(); jQuery("#tab-4").removeClass("active");
			jQuery("#bot-ac").show(); jQuery("#tab-5").addClass("active");

		}
	}
<?php
// =====================================================================
} elseif ( $mid == 38 ) {
	?>
	function file_info(what, where) {

		// Because we use a "multiple" select for aesthetic purposes
		// but don't want the user to select multiple files, we focus
		// only on the currently selected one:
		var current_item = jQuery('#select-'+ where ).prop('selectedIndex');
		jQuery('#select-'+ where ).prop('selectedIndex',current_item);

		// New file :
		if (where == 1) {

			var nfo = what.split(':');
			document.getElementById('new_size').innerHTML = nfo[3];
			document.getElementById('new_chmod').innerHTML = nfo[0];
			document.getElementById('new_uidgid').innerHTML = nfo[1] + ' / ' + nfo[2];
			document.getElementById('new_mtime').innerHTML = nfo[4].replace(/~/g, ':');
			document.getElementById('new_ctime').innerHTML = nfo[5].replace(/~/g, ':');
			jQuery('#table_new').slideDown();

		// Modified file :
		} else if (where == 2) {

			var all = what.split('::');
			var nfo = all[0].split(':');
			var nfo2 = all[1].split(':');
			document.getElementById('mod_size').innerHTML = nfo[3];
			if (nfo[3] != nfo2[3]) {
				document.getElementById('mod_size2').innerHTML = '<font color="red">'+ nfo2[3] +'</font>';
			} else {
				document.getElementById('mod_size2').innerHTML = nfo2[3];
			}
			document.getElementById('mod_chmod').innerHTML = nfo[0];
			if (nfo[0] != nfo2[0]) {
				document.getElementById('mod_chmod2').innerHTML = '<font color="red">'+ nfo2[0] +'</font>';
			} else {
				document.getElementById('mod_chmod2').innerHTML = nfo2[0];
			}
			document.getElementById('mod_uidgid').innerHTML = nfo[1] + ' / ' + nfo[2];
			if ( (nfo[1] != nfo2[1]) || (nfo[2] != nfo2[2]) ) {
				document.getElementById('mod_uidgid2').innerHTML = '<font color="red">'+ nfo2[1] + '/' + nfo2[2] +'</font>';
			} else {
				document.getElementById('mod_uidgid2').innerHTML = nfo2[1] + ' / ' + nfo2[2];
			}
			document.getElementById('mod_mtime').innerHTML = nfo[4].replace(/~/g, ':');
			if (nfo[4] != nfo2[4]) {
				document.getElementById('mod_mtime2').innerHTML = '<font color="red">'+ nfo2[4].replace(/~/g, ':') +'</font>';
			} else {
				document.getElementById('mod_mtime2').innerHTML = nfo2[4].replace(/~/g, ':');
			}
			document.getElementById('mod_ctime').innerHTML = nfo[5].replace(/~/g, ':');
			if (nfo[5] != nfo2[5]) {
				document.getElementById('mod_ctime2').innerHTML = '<font color="red">'+ nfo2[5].replace(/~/g, ':') +'</font>';
			} else {
				document.getElementById('mod_ctime2').innerHTML = nfo2[5].replace(/~/g, ':');
			}
			jQuery('#table_mod').slideDown();
		}
	}

	function delit() {
		if (confirm("<?php echo _('Delete the current snapshot?') ?>") ) {
			return true;
		}
		return false;
	}

	function nftoggle() {
		jQuery('#changes_table').slideDown();
		document.getElementById('vcbtn').disabled = true;
	}
<?php
// =====================================================================
} elseif ( $mid == 36 ) {
	?>

	function is_number(id) {
		var e = document.getElementById(id);
		if (! e.value ) { return }
		if (! /^[0-9]+$/.test(e.value) ) {
			alert("<?php echo _('Please enter a number only.') ?>");
			e.value = e.value.substring(0, e.value.length-1);
		}
	}
	var what;
	function check_key() {
		// Ignore the request if user only wants to delete the key:
		if (what == 1) { return true; }
		var pubkey = document.frmlog2.elements["nfw_options[clogs_pubkey]"];
		if (! pubkey.value.match( /^[a-f0-9]{40}:(?:[a-f0-9:.]{3,39}|\*)$/) ) {
			pubkey.focus();
			alert("<?php echo _('Your public key is not valid.') ?>");
			return false;
		}
	}

	function filter_log() {
		// Clear the log :
		document.frmlog.txtlog.value = '       DATE         INCIDENT  LEVEL     RULE     IP            REQUEST\n';
		// Prepare the regex :
		var nf_tmp = '';
		if ( document.frmlog.nf_crit.checked == true ) { nf_tmp += 'CRITICAL|'; }
		if ( document.frmlog.nf_high.checked == true ) { nf_tmp += 'HIGH|'; }
		if ( document.frmlog.nf_med.checked == true )  { nf_tmp += 'MEDIUM|'; }
		if ( document.frmlog.nf_upl.checked == true )  { nf_tmp += 'UPLOAD|'; }
		if ( document.frmlog.nf_nfo.checked == true )  { nf_tmp += 'INFO|'; }
		if ( document.frmlog.nf_dbg.checked == true )  { nf_tmp += 'DEBUG_ON|'; }
		// Return if empty :
		if ( nf_tmp == '' ) {
			document.frmlog.txtlog.value = "\n > <?php echo _('No records were found that match the specified search criteria.') ?>";
			return true;
		}
		// Put it all together :
		var nf_reg = new RegExp('^\\S+\\s+\\S+\\s+\\S+\\s+' + '(' + nf_tmp.slice(0, - 1) + ')' + '\\s');
		var nb = 0;
		var decodearray;
		for ( i = 0; i < myArray.length; ++i ) {
			decodearray = decodeURIComponent(myArray[i]);
			if ( document.frmlog.nf_today.checked == true ) {
				if (! decodearray.match(myToday) ) { continue;}
			}
			if ( decodearray.match(nf_reg) ) {
				// Display it :
				document.frmlog.txtlog.value += decodearray + '\n';
				++nb;
			}
		}
		if ( nb == 0 ) {
			document.frmlog.txtlog.value = "\n > <?php echo _('No records were found that match the specified search criteria.') ?>";
		}
	}

<?php
}
// =====================================================================
// EOF
