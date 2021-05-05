<?php
require_once "init.php";
require "CASauth.php";
require "checkAuth.php";
if(isset($userid)) {
header( "Location: $BASE_Directory/MediaLibrary.php" );	
} else {
header( "Location: $BASE_Directory/" );	
}
?>