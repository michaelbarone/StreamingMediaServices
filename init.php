<?php
if(!isset($_SESSION)) {
	session_start();
}

/**
 * force https
 * */
$forceHTTPS=0;
if($forceHTTPS===1 && (!isset($curltranscode) || $curltranscode!=1)) {
	if (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS'] || $_SERVER['HTTPS']==="off") {
		$url = 'https://' . $_SERVER['HTTP_HOST']
						  . $_SERVER['REQUEST_URI'];
		header('Location: ' . $url);
		exit;
	}
}

/** 
 *use this area for offline testing without cas
 * set to 0 for normal usage
 * set to 1 to bypass cas login and use the user info below
 * */
$disableCAS=1;
if($disableCAS===1) {
	$userid = "mbarone";
	$userATTR[] = '';
	$userATTR['Description'] = "faculty";
	$userATTR['FirstName'] = "This";
	$userATTR['LastName'] = "Guy";
	$userATTR['Groups'] = "Admin";
	$_SESSION['userid']="$userid";
}
/** 
 *use this area for testing
 * set to 0 for normal usage
 * set to 1 for testing
 * */
$enableTesting=0;
if($enableTesting===1){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// logging level options: DEBUG, INFO, WARN, ERROR, FATAL, OFF 
// default:   $LOGGINGLEVEL="INFO";
$LOGGINGLEVEL="INFO";

// update these site variables
// project title will be part of the tab/window title, and also displayed in the header bar
$PROJECT_TITLE = "Streaming Media Services";

$PROJECT_TITLE_SHORT = "SMS";

// this will prepend the page title when hovering over the tab or window this is displayed in
$PageTitlePrepend = "CSUS";

// the base directory is for your webserver setting, this is set as follows:
// http://ourdomain.com/thissite   =>  $BASE_Directory = "/thissite";
// http://thissite.ourdomain.com   =>  $BASE_Directory = "";
$BASE_Directory = "/StreamingMediaServices";



 
/** 
 *
 *     end of settings
 * 
 * */
if(!isset($ASSETS)) {
	$found = false;
	$path = './assets';
	while(!$found){
		if(file_exists($path)){ 
			$found = true;
			$ASSETS = $path;
		}
		else{ $path = '../'.$path; }
	}
}
if(!isset($LIB)) {
	$found = false;
	$path = './lib';
	while(!$found){
		if(file_exists($path)){ 
			$found = true;
			$LIB = $path;
		}
		else{ $path = '../'.$path; }
	}
}
require_once "$LIB/KLogger.php";
$date = date('Y-m-d');
// klogger options: DEBUG, INFO, WARN, ERROR, FATAL, OFF
$log = new KLogger ( $ASSETS."/logs/SMS-log-$date.log" , "KLogger::$LOGGINGLEVEL" );

// Do database work that throws an exception
//$log->LogError("An exception was thrown in ThisFunction()");
 
// Print out some information
//$log->LogInfo("Internal Query Time: $time_ms milliseconds");
 
// Print out the value of some variables
//$log->LogDebug("Loaded Somethings from " . $_SERVER['SCRIPT_FILENAME']);
//$log->LogFatal("Fatal: User could not open DB: $e->getMessage().  from " . basename(__FILE__));

if(isset($_GET['logme']) && $_GET['logme'] === "out") {
	session_destroy();
	header( "Location: $BASE_Directory/" );
}
if(!isset($UPLOAD)) {
	$found = false;
	$path = './upload';
	while(!$found){
		if(file_exists($path)){ 
			$found = true;
			$UPLOAD = $path;
		}
		else{ $path = '../'.$path; }
	}
}
require_once $ASSETS . "/streaming.class.php";
$Streaming = new Streaming($ASSETS,$UPLOAD,$BASE_Directory,$log);
$Streaming->sessionCreate();
$Streaming->PageView();
?>