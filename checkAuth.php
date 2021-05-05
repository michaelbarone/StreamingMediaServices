<?php
if(!isset($userid)) {
	if(isset($_SESSION['userid'])) {
		$userid = $_SESSION['userid'];
	} elseif(isset($_SESSION['phpCAS']['user'])) {
		$userid = $_SESSION['phpCAS']['user'];
	}
}
if(!isset($userATTR)) {
	if(isset($_SESSION['phpCAS']['attributes'])) {
		$userATTR = $_SESSION['phpCAS']['attributes'];
	}
}
if(!isset($userATTR) && !isset($userid)) {
	$groupaccess="denied";
} else {
	$groupaccess = $Streaming->CheckAuthGroupAccess($userATTR['Description'],$userATTR['Groups'],$userid);
}
if($groupaccess==="allowed") {
	$checkAgreeTerms = $Streaming->checkAgreeToTerms("$userid",$userATTR['FirstName'],$userATTR['LastName']);
	$userlevel = $Streaming->UserLevel($userid);
}elseif($groupaccess==="denied") {
	$log->LogWarn("Unauthorized Access by $userid due to $groupaccess");
	header( "Location: $BASE_Directory/?groupDenied=true" );
	exit;
}elseif($groupaccess==="deniednobeta") {
	$log->LogWarn("Unauthorized Access by $userid due to $groupaccess");
	header( "Location: $BASE_Directory/?BetaAccess=false" );
	exit;
}
?>