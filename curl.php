<?php
require_once "init.php";
if (isset($_GET['request']) && isset($_GET['param'])) {
    $request = $_GET['request'];
	$param = $_GET['param'];
}else{
    exit;
}
$allowedRequests = array("MediaPlaybackTime"
							, "LogThis"
							, "ChooseFolderFor"
							, "GetMediaLibraryFolders"
							, "CreateMediaLibraryFolder"
							, "ListGroupUsers"
							, "AddUserToGroup"
						);
$openRequests = array( 	// openRequests include any allowedRequests that can be sent by un-athenticated users.
						//
						//
						// MediaPlaybackTime is open so un-athenticated viewers still pass playback stats
					"MediaPlaybackTime"						
					);
if(in_array("$request",$allowedRequests)) {
	if(!in_array("$request",$openRequests)) {
		require "checkAuth.php";
	}
	if($request == "LogThis"){
		$logType = $_GET['logType'];
		$log->$logType($param);
		exit;
	} elseif($request == "MediaPlaybackTime"){
		$param = $param . "@@@" . $_GET['mediaHash'];
	} elseif($request == "ChooseFolderFor"){
		$param = $param . "@@@" . $userid . "@@@" . $_GET['folderName'];
	} elseif($request == "CreateMediaLibraryFolder") {
		$param = $param . "@@@" . $_GET['folderName'];
	} else {
		$log->LogInfo("CURL Request sent by $userid -- request:$request  parameter:$param");
	}
} else {
	require "checkAuth.php";
	$log->LogWarn("INVALID CURL Request sent by $userid -- request:$request  parameter:$param");
	exit;
}
$requesting = $Streaming->{$request}($param);
if($request == "GetMediaLibraryFolders") {
	$mediaHash = $_GET['mediaHash'];
	?>
	<div class="videolist currentlyprocessing">
		<div class='videolist header'>
			<span>Create New Folder:</span>
		</div>
		<div class="videolist">
			<div class='videoentry'>
				<h2 class="left"></h2>
				<input id="createFolderName" style="width:50%;" type="text" name="folderName" /><a href='javascript:void(0)' class='btn btn-success' style='margin:5px !important;' onclick='return CreateFolder("<?php echo $userid;?>","<?php echo $mediaHash;?>");'>Create Folder</a><br />
			</div>
		</div>
	</div>	
	<br />
	<div id="FolderListContainer" class="videolist currentlyprocessing">
		<div class='videolist header'><span>Folders:</span></div>
		<?php
			if(empty($requesting)) {
				echo "<span id='nofolders'>No folders.  Create a new one above to get started.</span>";
			} else {
				foreach($requesting as $entry) {
					echo "<a href='javascript:void(0)' class='btn btn-primary' style='margin:5px !important;' onclick=\"return ChooseFolderFor('" . $mediaHash . "','" . $entry['folderName'] . "');\"><img class='left' style='height:20px;width:20px;margin-right:5px;' src='./img/folder.png' />Move to: " . $entry['folderName'] . "</a><br />";
				}
			}
		?>
	</div>
	<?php
} else if($request == "ListGroupUsers") {
	$group = $_GET['request'];
	?>
	<div class="videolist currentlyprocessing">
		<div class='videolist header'>
			<span>Add User to Group:</span>
		</div>
		<div class="videolist">
			<div class='videoentry'>
				<h2 class="left"></h2>
				<input id="addusertogroup" style="width:50%;" type="text" name="userid" /><a href='javascript:void(0)' class='btn btn-success' style='margin:5px !important;' onclick='return AddUserToGroup("<?php echo $userid;?>","<?php echo $group;?>");'>Add User</a><br />
			</div>
		</div>
	</div>	
	<br />
	<div id="FolderListContainer" class="videolist currentlyprocessing">
		<div class='videolist header'><span>Userlist:</span></div>
		<?php
			if(empty($requesting)) {
				echo "<span id='nofolders'>No Group Users.  Search and add users above.</span>";
			} else { ?>
				<table>
				<th>Users</th>
				<th>Options</th> <?php
				foreach($requesting as $entry) {
					echo "<tr><td>";
					echo $entry['userid'] ." -- manager: ".$entry['manager'];
					echo "</td><td><a href='javascript:void(0)' class='btn btn-danger'>Remove</a></td></tr>";
					//echo "<a href='javascript:void(0)' class='btn btn-primary' style='margin:5px !important;' onclick=\"return ChooseFolderFor('" . $mediaHash . "','" . $entry['folderName'] . "');\"><img class='left' style='height:20px;width:20px;margin-right:5px;' src='./img/folder.png' />Move to: " . $entry['folderName'] . "</a><br />";
				}
				echo "</table>";
			}
		?>
	</div>
	<?php	
}
return $requesting;
?>