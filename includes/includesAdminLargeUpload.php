<div class="videolist currentlyprocessing">
	<div class="videolist header">
		<span>Custom Large File Uploads: <img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
	</div>
	<div class="videolist">
		<span class="left">Build File Title for Manual Copy to Transcode Folder:</span><br />
		<form id="createFileTitle" method="post" class="left">
			<input type="hidden" value="LargeUpload" name="LargeUpload"/>
			<table>
				<tr><td>Userid</td><td><input type="text" name="userid" size="40" required /></td></tr>
				<tr><td>Filename</td><td><input type="text" name="fileName" size="40" required /></td></tr>
				<tr><td>File Title</td><td><input type="text" name="fileTitle" size="40" required /></td></tr>
				<tr><td>File Info</td><td><textarea name="fileInfo" cols="43" rows="5"></textarea></td></tr>
				<tr><td>Require Saclink Authentication for viewing</td><td><input type="checkbox" name="reqLogin" value="1"></td></tr>
			</table>
			<input type="submit" class="btn btn-success btn-sml submit" value="Submit"><img class="submit hidden" src="./img/loading.gif" />
		</form>
		<br class="clear"/><br />
		<span id="fileTitle">
			<?php if(isset($fileTitle)) {
				echo $fileTitle;
				echo "<br>";
				echo "Use this above file title when manually copying the large media file to the transcode server watch folder";
				echo "<br> This entry has been placed in the user's view so they can share the link.  It will be marked as available after the transcode process completes";
			} ?>
		</span>
	</div>
</div>