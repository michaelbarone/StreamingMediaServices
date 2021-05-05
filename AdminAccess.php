<?php
require_once "init.php";
require "CASauth.php";
require "checkAuth.php";
$failed = 0;
$sortby = 'fileTitle';
$tab = 'Support';
if(isset($_GET)){
	if(isset($_GET['sortby'])) {
		$sortby = $_GET['sortby'];
	}
	if(isset($_GET['tab'])) {
		$tab = $_GET['tab'];
	}	
}
if(!isset($userlevel)) {
	$userlevel = $Streaming->UserLevel($userid);
}
if(!isset($userlevel) || $userlevel != "admin") {
	header("Location: ./MediaLibrary.php");
	exit;
}
if(isset($_POST['selecteduserid']) && $_POST['selecteduserid'] != '') {
	$selecteduserid = $_POST['selecteduserid'];
}
if(isset($_POST['selectedfilehash']) && $_POST['selectedfilehash'] != '') {
	$selectedfilehash = $_POST['selectedfilehash'];
	$selectedfile = $Streaming->ShowEntrySupport($selectedfilehash);
	$selecteduserid=$selectedfile['userid'];
}
if(isset($_POST['LargeUpload']) && $_POST['LargeUpload'] != '') {
	$tab = "LargeUpload";
	$thisuserid = $_POST['userid'];
	$filePath = $_POST['fileName'];
	$fileTitle = $_POST['fileTitle'];
	$fileInfo = $_POST['fileInfo'];
	$reqLogin = (isset($_POST['reqLogin']) && $_POST['reqLogin'] === "1") ? 1 : 0;
	$fileTitle = $Streaming->AddEntry($thisuserid,$filePath,$fileTitle,$fileInfo,"",$reqLogin,"yes",$userid);
}
if(isset($_POST['CreateGroup']) && $_POST['CreateGroup'] != '') {
	$tab = "GroupManager";
	$groupName = $_POST['groupName'];
	$groupType = $_POST['groupType'];
	$fileTitle = $Streaming->AddGroup($groupName,$groupType);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CSUS Streaming - Administration</title>
		<?php include "includeHeader.php";?>
		<div id="content" class="content">
			<?php
				if($failed > 0) {
			?>
					<div id="videotitle">
						<h3>Error encountered.</h3>
					</div>				
			<?php
				} else {
			?>
			<div id="videotitle">
				<h3>Streaming Media Administration</h3>
				<div id="tabButtons">
					<a href="?tab=Support" class="btn btn-warning btn-sml left">Support</a>
					<a href="?tab=LargeUpload" class="btn btn-warning btn-sml left">Large Upload</a>
					<a href="?tab=GroupManager" class="btn btn-warning btn-sml left">Group Manager</a>
					<a href="?tab=RecentMediaPlays" class="btn btn-info btn-sml left">Recent Media Plays</a>
					<a href="?tab=RecentLogins" class="btn btn-info btn-sml left">Recent Logins</a>
					<a href="?tab=FailedLogins" class="btn btn-danger btn-sml left">Failed Logins</a>
				</div>
			</div>
			<br /><br class="clear"/>
			<?php if(isset($tab) && $tab==='Support') {
					include "./includes/includesAdminSupport.php";
				} elseif($tab==='LargeUpload') {
					include "./includes/includesAdminLargeUpload.php";
				} elseif($tab==='GroupManager') {
					include "./includes/includesAdminGroupManager.php";
				} elseif($tab==='RecentMediaPlays') {
					include "./includes/includesAdminRecentMediaPlays.php";
				} elseif($tab==='RecentLogins') {
					include "./includes/includesAdminRecentLogins.php";
				} elseif($tab==='FailedLogins') {
					include "./includes/includesAdminFailedLogins.php";
				}
			}
			?>
		</div>
		<?php include "includeFooter.php";?>
	</body>
</html>