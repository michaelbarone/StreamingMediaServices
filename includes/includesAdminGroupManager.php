<div class="videolist currentlyprocessing">
	<div class="videolist header">
		<span>Group Manager: <img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
	</div>
	<div class="videolist">
		<span class="left">Create a New Group:</span><br />
		<form id="createGroup" method="post" class="left">
			<input type="hidden" value="CreateGroup" name="CreateGroup"/>
			<table>
				<tr><td>Group Name</td><td><input type="text" name="groupName" size="40" required /></td></tr>
				<tr><td>Group Type</td><td><select name="groupType">
											  <option value="Login" selected>Login</option>
											  <option value="Annotate">Annotate</option>
											  <option value="MediaGroup">MediaGroup</option>
											</select></td></tr>
			</table>
			<input type="submit" class="btn btn-success btn-sml submit" value="Add"><img class="submit hidden" src="./img/loading.gif" />
		</form>
		<br class="clear"/><br />
	</div>
</div>

<div class="videolist currentlyprocessing">
	<div class="videolist header">
		<span>Groups: <img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
	</div>
	<div class="videolist">
		<table>
			<tr>
				<th>Group Name</th>
				<th>Group Type</th>
				<th>Edit Group</th>
			</tr>
		<?php
			$groups = $Streaming->ListGroups();
			foreach($groups as $entry) {
				?>
					<tr>
						<td><?php echo $entry['groupName']; ?></td>
						<td><?php echo $entry['groupType']; ?></td>
						<td>
							<a href="javascript:void(0)" id="users<?php echo $entry['indexID'];?>" class="btn btn-warning right" onclick="return ListGroupUsers('<?php echo $entry['indexID'];?>');">Edit Users</a>
						</td>
					</tr>
				<?php
			}
		?>
		</table>
	</div>
</div>