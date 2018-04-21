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

$critical = 0; $high = 0; $medium = 0; $slow = 0; $benchmark = 0;
$tot_bench = 0; $speed = 0; $upload = 0; $banned_ip = 0;
$fast = 1000;

// Which monthly log should we read?
$xtr = @$_GET['xtr'];
if ( empty( $xtr ) || ! preg_match( '/^firewall_\d{4}-\d{2}\.php$/', $xtr ) ) {
	$xtr = 'firewall_' . date('Y-m') . '.php';
	$fw_log = "./nfwlog/{$xtr}";
}
$fw_log = "./nfwlog/{$xtr}";

html_header( 'chartjs' );

?>
<div class="col-sm-12 text-left">
	<h3><?php echo _('Summary > Statistics') ?></h3>
	<br />
<?php


if (! file_exists( $fw_log ) ) {
	goto NO_STATS_FILE;
}

if ( $fh = @fopen( $fw_log, 'r' ) ) {
	while (! feof( $fh ) ) {
		$line = fgets( $fh );
		if (preg_match('/^\[.+?(?:\s.\d{4})?\]\s+\[(.+?)\]\s+(?:\[.+?\]\s+){3}\[([1-6])\]/', $line, $match ) ) {
			if ( $match[2] == 1) {
				++$medium;
			} elseif ( $match[2] == 2 ) {
				++$high;
			} elseif ( $match[2] == 3 ) {
				++$critical;
			} elseif ( $match[2] == 5 ) {
				++$upload;
			} elseif ( $match[2] == 6 ) {
				if ( strpos( $line, 'Banning IP' ) !== false ) {
					++$banned_ip;
				}
				continue;
			}
			if ( $match[1]) {
				if ( $match[1] > $slow ) {
					$slow = $match[1];
				}
				if ( $match[1] < $fast ) {
					$fast = $match[1];
				}
				$speed += $match[1];
				++$tot_bench;
			}
		}
	}
	fclose( $fh );
} else {
	echo '<div class="alert alert-danger text-left">
		<a class="close" data-dismiss="alert" aria-label="close">&times;</a>'.
		sprintf(	_('Unable to open logfile: %s'),	htmlspecialchars( $fw_log ) ) .'</div>';
	summary_stats_combo( $xtr );
	echo '</div>';
	html_footer();
	exit;
}

NO_STATS_FILE:

$total = $critical + $high + $medium;
$c = $critical; $h = $high; $m = $medium;

if ( $total == 1 ) { $fast = $slow; }

if (! $total ) {
	echo '<div class="alert alert-warning text-left">
		<a class="close" data-dismiss="alert" aria-label="close">&times;</a>'.
		_('You do not have any statistics for the current month.') .'</div>';
	$fast = 0;

} else {
	$coef = 100 / $total;
	$critical = round( $critical * $coef, 2 );
	$high = round( $high * $coef, 2 );
	$medium = round( $medium * $coef, 2 );
	// Avoid divide error :
	if ( $tot_bench ) {
		$speed = round( $speed / $tot_bench, 4 );
	} else {
		$fast = 0;
	}
}
// Prepare select box:
$ret = summary_stats_combo( $xtr );

?>
	<table width="100%" class="table table-nf">
		<tr>
			<td width="45%" align="left"><?php echo _('Statistics period') ?></td>
			<td width="55%" align="left"><?php echo $ret ?></td>
		</tr>
		<tr>
			<td width="45%" align="left"><?php echo _('Blocked threats') ?></td>
			<td width="55%" align="left"><?php echo $total ?></td>
		</tr>
		<tr>
			<td width="45%" align="left"><?php echo _('Threats level') ?></td>
			<td width="55%" align="left">
				<canvas id="myChart"></canvas>
			</td>
		</tr>
		<tr>
			<td width="45%" align="left"><?php echo _('Uploaded files') ?></td>
			<td width="55%" align="left"><?php echo $upload ?></td>
		</tr>
		<tr>
			<td width="45%" align="left"><?php echo _('Banned IP addresses') ?></td>
			<td width="55%" align="left"><?php echo $banned_ip ?></td>
		</tr>
		<tr>
			<td width="45%" align="left"><?php echo _('Average time per request') ?></td>
			<td width="55%" align="left"><?php echo $speed .' '. _('seconds') ?></td>
		</tr>
		<tr>
			<td width="45%" align="left"><?php echo _('Fastest request') ?></td>
			<td width="55%" align="left"><?php echo round($fast, 4) .' '. _('seconds') ?></td>
		</tr>
		<tr>
			<td width="45%" align="left"><?php echo _('Slowest request') ?></td>
			<td width="55%" align="left"><?php echo round($slow, 4) .' '. _('seconds') ?></td>
		</tr>
	</table>

	<script>
		var horizontalBarChartData = {
			labels: [
				"<?php echo _('Critical') .' '. $critical ?>%",
				"<?php echo _('High') .' '. $high ?>%",
				"<?php echo _('Medium') .' '. $medium ?>%"
			],
			datasets: [{
				label: '<?php echo _('Blocked threats') ?>',
				backgroundColor: ["#c9302c", "#ec971f","#ECE81F"],
				data: [<?php echo "{$c}, {$h}, {$m}" ?>],
				borderColor: ['#8C2C2A', '#c9302c', '#ec971f'],
				borderWidth: 1
           } ]
		};
		window.onload = function() {
			var ctx = document.getElementById("myChart").getContext("2d");
			window.myHorizontalBar = new Chart(ctx, {
				type: 'horizontalBar',
				data: horizontalBarChartData,
				options: {
					tooltips: {
						backgroundColor: '#333',
					},
					legend: {
						display: false,
					},
					responsive: true,
					scales: {
						xAxes: [{
							ticks: {
								beginAtZero: true
							}
						}]
					}
				}
			});
		};
	</script>
</div>

<?php

html_footer();

// ---------------------------------------------------------------------
function summary_stats_combo( $xtr ) {

	// Find all available logs :
	$avail_logs = array();
	if ( is_dir( './nfwlog/' ) ) {
		if ( $dh = opendir( './nfwlog/' ) ) {
			while ( ( $file = readdir( $dh ) ) !== false ) {
				if (preg_match( '/^(firewall_(\d{4})-(\d\d)\.php)$/', $file, $match ) ) {
					$log_stat = stat( "./nfwlog/{$file}" );
					if ( $log_stat['size'] < 10 ) { continue; }
					$month = date('F', mktime(0, 0, 0, $match[3], 1, 2000) );
					$avail_logs[$match[1] ] = $month .' '. $match[2];
				}
			}
			closedir( $dh );
		}
	}
	krsort( $avail_logs );

	$ret = '<form>
		<select class="form-control" name="xtr" onChange="return stat_redir(this.value, \''. $_REQUEST['token'] .'\');">
			<option value="">' . _('Select monthly stats to view') . '</option>';
   foreach ($avail_logs as $file => $text) {
      $ret .= '<option value="' . $file . '"';
      if ($file === $xtr ) {
         $ret .= ' selected';
      }
      $ret .= ">{$text}</option>";
   }
   $ret .= '</select>
		</form>';
	return $ret;
}

// ---------------------------------------------------------------------
// EOF

