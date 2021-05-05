<?php
$groupaccess="denied";
$loginpage=0;
if(strpos($_SERVER['SCRIPT_NAME'],"index.php")===false){ } else {
	$loginpage=1;
}
if($disableCAS===0 && isset($_SESSION['phpCAS']['user']) && isset($_SESSION['phpCAS']['attributes']['Description'])) {
	$userid=$_SESSION['phpCAS']['user'];
	$thisdescription=$_SESSION['phpCAS']['attributes']['Description'];
	$groupList=$_SESSION['phpCAS']['attributes']['Description'];
	$groupaccess = $Streaming->CheckAuthGroupAccess($thisdescription,$groupList,$userid);
}elseif($disableCAS===1){
	$thisdescription=$userATTR['Description'];
	if(isset($userATTR['Groups'])) {
		$groupList=$userATTR['Groups'];
	} else {
		$groupList='';
	}
	$groupaccess = $Streaming->CheckAuthGroupAccess($thisdescription,$groupList,$userid);	
}
if($groupaccess==="allowed" && !isset($userlevel) && $loginpage===0) {
	$userlevel = $Streaming->UserLevel($userid);
}
?>
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="./css/style.css">
	<link rel="stylesheet" type="text/css" href="./css/theme-default.css">
	<link rel="stylesheet" type="text/css" href="./css/responsive.css">
	<?php
	if($loginpage===0) {
	?>
		<script src="./js/jquery-1.11.3.min.js"></script>
		<script src="./js/script.js"></script>
		<script src="./js/ajaxupload.js"></script>
		<script src="./js/jsmodal-1.0d.min.js"></script>
		<script src="./js/clipboard.min.js"></script>
		<script src="./js/accordian.js"></script>
		<script src="./js/jquery-ui.js"></script>
		<script src="./js/slick.min.js"></script>
	<?php
	}
	?>
</head>
<?php include "includeTag.php";?>
<body>
	<div id="topBanner">
		<div class="content">
			<img src="./img/logo.jpg" class="left"/>
			<nav class="sitenavigation right" data-html2canvas-ignore="true">
				<img class="mobilenavigation right" src="./img/menubutton.png" />
				<ul class="primary-site-navigation">
					<?php
					if(isset($userlevel) && $userlevel === "admin") { ?>
						<li>
							<a href="./AdminAccess.php">Admin Area</a>
						</li>						
					<?php } 
					if($loginpage===0) {
						if($disableCAS===1 || $groupaccess==="allowed"){
						?>
						<li>
							<a href="#">Media</a>
							<ul class="primary-site-navigation-secondary">
								<li><a href="./MediaLibrary.php">My Library</a></li>
								<li><a href="./MediaPlaylists.php">My Playlists</a></li>
							</ul>
						</li>
						<?php } ?>
						<li>
							<a href="./Support.php">Support</a>
						</li>
					<?php } else { ?>
						<li>
							<a href="./?login=true">Login</a>
						</li>					
					<?php } ?>
					<?php
					if($groupaccess==="allowed" && $loginpage===0) {
						if($disableCAS===1) { ?>
							<li><a href="./init.php?logme=out">Log Out</a></li>
						<?php } else { ?>
							<li><a href="./CASlogout.php">Log Out</a></li>
					<?php } 
					} ?>
				</ul>	
			</nav>
		</div>
	</div>
	<div id="pageHeader">
		<div class="content">
			<h1><?php
				if($loginpage===0) {
					echo "$PROJECT_TITLE";
				} else {
					echo "$PROJECT_TITLE_SHORT";
				}
				?>
			</h1>
		</div>
	</div>		