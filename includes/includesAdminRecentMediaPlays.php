<div class="videolist currentlyprocessing">
	<div class="videolist header">
		<span>Recent Media Plays: <img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
	</div>
	<div class="videolist">
		<table>
			<tr>
				<th>Stats</th>
				<th>MediaHash</th>
				<th>Username</th>
				<th>Timestamp</th>
				<th>IP</th>
				<th>Comments</th>
			</tr>
		<?php
			$recentPlays = $Streaming->GetMediaPlays();
			foreach($recentPlays as $entry) {
				?>
					<tr>
						<td>
							<form class="right" action="Stats.php" method="post" target="_blank">
								<input type="hidden" value="<?php echo $entry['mediaHash'];?>" name="h" />
								<input type="submit" class="btn btn-info" value="Stats" />
							</form>
						</td>
						<td><?php echo $entry['mediaHash']; ?></td>
						<td><a href="http://saclinksvc.webapps.csus.edu/Account/<?php echo $entry['userid']; ?>/" target="_blank"><?php echo $entry['userid']; ?></a></td>
						<td><?php echo date('Y-m-d h:i:s a',$entry['Timestamp']); ?></td>
						<td><?php echo $entry['ip']; ?></td>
						<td><?php echo $entry['comments']; ?></td>
					</tr>
				<?php
			}
		?>
		</table>
	</div>
</div>