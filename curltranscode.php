<?php
// remote_addr this is our archive server.  only requests from the archive server should be processed
if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === "130.86.242.108") {
	if(isset($_GET) && isset($_GET['userid']) && isset($_GET['title'])) {
		$curltranscode=1;
		require_once "init.php";
		$userid = $_GET['userid'];
		$title = $_GET['title'];
		$timenow = time();
		$filePath = "prj/indiv/$userid/$title";
		$log->LogInfo("CURL transcode complete sent by user:$userid for file:$title ");
		$statement = $Streaming->streamingdb->prepare("UPDATE Media_Library SET postprocessed = '$timenow' WHERE userid = '$userid' AND filePath = '$filePath'");
		$statement->execute();
	}
}
?>
