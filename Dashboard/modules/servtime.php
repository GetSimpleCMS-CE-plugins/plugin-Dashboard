<?php
/**
 * Module Name: Server Time
 * Module ID: servtime
 * Description: Displays current local and server time/date.
 * Version: 1.0
 * Default W: 4
 * Default H: 2
 */
?>

<?php
$serverTimezone = null;
$serverISO = null;

if (defined('GSTIMEZONE') && GSTIMEZONE != '') {
	$serverTimezone = GSTIMEZONE;
	date_default_timezone_set($serverTimezone);
	$serverISO = date('c');
}
?>

<style>
.gs-time-panel {
	font-family: system-ui, sans-serif;
}

.gs-time-panel h3 {
	margin-top: 0;
	margin-bottom: 10px;
}

.time-row {
	display: flex;
	justify-content: space-between;
	margin-bottom: 5px;
}

.time-label {
	font-weight: 600;
	color: #555;
}

.time-value {
	font-family: monospace;
}

.diff-row {
	margin-top: 5px;
	padding-top: 5px;
	border-top: 1px solid #eee;
	font-size:.8em;
}

.time-warning {
	background: #fff3cd;
	border: 1px solid #ffeeba;
	padding: 10px;
	border-radius: 6px;
	color: #856404;
	margin-bottom: 12px;
}
</style>

<div class="gs-time-panel">
	<h3><svg xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;" width="28" height="28" viewBox="0 0 20 20"><rect width="20" height="20" fill="none"/><path fill="currentColor" d="M10.475 2.17a.75.75 0 0 0-.95 0l-2.75 2.25a.75.75 0 0 0 .95 1.16L10 3.72l2.275 1.86a.75.75 0 1 0 .95-1.16zm2.75 13.41l-2.75 2.25a.75.75 0 0 1-.95 0l-2.75-2.25a.75.75 0 0 1 .95-1.16L10 16.28l2.275-1.861a.75.75 0 1 1 .95 1.16M10.75 8.75a.75.75 0 1 1-1.5 0a.75.75 0 0 1 1.5 0M10 12a.75.75 0 1 0 0-1.5a.75.75 0 0 0 0 1.5m6-2.5a1.5 1.5 0 0 1 3 0v1a1.5 1.5 0 0 1-3 0zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 1 0v-1a.5.5 0 0 0-.5-.5m-11-1A1.5 1.5 0 0 0 5 9.5v1a1.5 1.5 0 0 0 3 0v-1A1.5 1.5 0 0 0 6.5 8M6 9.5a.5.5 0 0 1 1 0v1a.5.5 0 0 1-1 0zm6 0a1.5 1.5 0 0 1 3 0v1a1.5 1.5 0 0 1-3 0zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 1 0v-1a.5.5 0 0 0-.5-.5m-11-1A1.5 1.5 0 0 0 1 9.5v1a1.5 1.5 0 0 0 3 0v-1A1.5 1.5 0 0 0 2.5 8M2 9.5a.5.5 0 0 1 1 0v1a.5.5 0 0 1-1 0z"/></svg> Time Status</h3>

	<div class="time-row">
		<div class="time-label">Local Time (<span id="localTimezone"></span>)</div>
		<div class="time-value" id="localTime"></div>
	</div>

	<?php if ($serverTimezone): ?>
		<div class="time-row">
			<div class="time-label">
				Server Time (<?php echo htmlspecialchars($serverTimezone); ?>)
			</div>
			<div class="time-value">
				<?php 
					// FORMAT STRING (Server Time)
					echo date('H:i (Y-m-d)');
				?>
			</div>
		</div>
	<?php else: ?>
		<div class="time-warning">
			⚠ Server timezone is not set.<br>
			Please define <code>GSTIMEZONE</code> in <code>gsconfig.php</code>
		</div>
	<?php endif; ?>

	<?php if ($serverISO): ?>
		<div class="time-row diff-row">
			<div class="time-label">Time Difference</div>
			<div class="time-value" id="timeDiff"></div>
		</div>
	<?php endif; ?>
</div>

<script>
function updateAllTimes() {

	const now = new Date();
	
	const localTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
	document.getElementById('localTimezone').textContent = localTz;

	// FORMAT STRING (Local Time)
	//const format = "Y-m-d, H:i:s";
	const format = "H:i:s (Y-m-d)";
	//  end

	function pad(n) {
		return n < 10 ? '0' + n : n;
	}

	function formatDate(date, format) {
		return format
			.replace(/Y/g, date.getFullYear())
			.replace(/m/g, pad(date.getMonth() + 1))
			.replace(/d/g, pad(date.getDate()))
			.replace(/H/g, pad(date.getHours()))
			.replace(/i/g, pad(date.getMinutes()))
			.replace(/s/g, pad(date.getSeconds()));
	}

	// Update local time display
	document.getElementById('localTime').textContent =
		formatDate(now, format);

	<?php if ($serverTimezone): ?>

	// ---- TIMEZONE DIFFERENCE CALCULATION ----
	const clientOffset = -now.getTimezoneOffset();

	const serverOffset = <?php
		$dt = new DateTime("now", new DateTimeZone($serverTimezone));
		echo $dt->getOffset() / 60;
	?>;

	const diffMinutes = clientOffset - serverOffset;

	let hours = Math.floor(Math.abs(diffMinutes) / 60);
	let minutes = Math.abs(diffMinutes) % 60;

	let text;

	if (diffMinutes === 0) {
		text = "Same timezone";
	} else {
		text = hours + "h " + minutes + "m " +
			(diffMinutes > 0 ? "ahead" : "behind");
	}

	document.getElementById('timeDiff').textContent = text;

	<?php endif; ?>
}

// Run immediately
updateAllTimes();

// Update every second
setInterval(updateAllTimes, 1000);
</script>