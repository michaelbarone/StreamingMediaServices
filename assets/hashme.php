<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
require_once "../init.php";
if (isset($_GET['hashme'])) {
	$hashthis = $_GET['hashme'];
	$hash = $Streaming->CreateHash($hashthis);
	echo "the hash for $hashthis is: $hash";
	echo "<br><br>";
	echo "time is now: " . time();
	exit;
}
?>