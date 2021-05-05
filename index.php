<?php
$groupDenied = false;
$noBeta = false;
require_once "init.php";
if(isset($_GET) && !empty($_GET)) {
	if(isset($_GET['login']) && $_GET['login'] == 'true') {
		header( 'Location: ./Login.php' );
	}
	if(isset($_GET['groupDenied']) && $_GET['groupDenied'] == 'true') {
		$groupDenied = true;
	} elseif(isset($_GET['BetaAccess']) && $_GET['BetaAccess'] == 'false') {
		$noBeta = true;
	}
}
$failed = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo "$PageTitlePrepend - Home - SMS";?></title>
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
				if($groupDenied===true) {
				?>
					<div id="videotitle">
						<h3>This service is currently only available for certain user types including Faculty and Staff.</h3>
						<h4>Please contact <a href="mailto:web-courses@csus.edu">web-courses@csus.edu</a> if you should have access to this resource.</h4>
					</div>
				<?php
				} elseif($noBeta===true) {
					$url = "http://saclinksvc.webapps.csus.edu/Account/$userid/";
					$xml = @file_get_contents("$url");
					if($xml === FALSE) {
						$fname = "";
						$lname = "";
					} else {
						$xml = json_decode($xml);			
						$fname = "$xml->FirstName";
						$lname = "$xml->LastName";
					}
				?>
					<div id="videotitle">
						<h3><?php echo "Hi $fname $lname,";?></h3>
						<h3>Your login attempt has been logged.</h3>
						<h3>This service is currently in closed beta.</h3>
						<h4>Please contact <a href="mailto:web-courses@csus.edu">web-courses@csus.edu</a> to request access to this resource.</h4>
					</div>				
				<?php
				}
				?>		
				<br />
				<div class="pagelist">
					<div class="pagelist header">
						<span>Welcome to CSUS SMS</span>
					</div>
					<div class='pageentry'>
						<h4>You must login to use the SMS</h4>
						<a class="btn btn-success btn-lg" href="./?login=true">Login</a>
						<br><br>
						<h3>CSUS SMS is currently in closed beta.  Please contact <a href="mailto:web-courses@csus.edu">web-courses@csus.edu</a> to request access to this resource.</h3>
						<br>
					</div>
				</div>
				<br class="clear"><br>
			<?php
			} 
			?>
		</div>
		<?php include "includeFooter.php";?>
	</body>
</html>