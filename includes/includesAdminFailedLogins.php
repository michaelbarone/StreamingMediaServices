<div class="videolist currentlyprocessing">
	<div class="videolist header">
		<span>Failed Logins: <img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
	</div>
	<div class="videolist">
		<table>
			<tr>
				<th>Username</th>
				<th>Access Type</th>
				<th>Timestamp</th>
				<th>IP</th>
			</tr>
		<?php
			$recentLogins = $Streaming->GetUserLogIns("failed");
			foreach($recentLogins as $entry) {
				?>
					<tr>
						<td><a href="http://saclinksvc.webapps.csus.edu/Account/<?php echo $entry['userid']; ?>/" target="_blank"><?php echo $entry['userid']; ?></a></td>
						<td><?php echo $entry['access']; ?></td>
						<td><?php echo date('Y-m-d h:i:s a',$entry['Timestamp']); ?></td>
						<td><?php echo $entry['ip']; ?></td>
					</tr>
				<?php
			}
		?>
		</table>
	</div>
</div>