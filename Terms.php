<?php
require_once "init.php";
require "CASauth.php";
$failed = 0;
$dateAgreed = "failed";
if(isset($_POST['iAgree'])) {
	$inputthis = $Streaming->agreeToTerms("$userid");
	header("Location: ./MediaLibrary.php");
	exit();
}
if(isset($userid)){
	$dateAgreed = $Streaming->checkAgreeToTerms("$userid");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CSUS Streaming - <?php echo $uploadfilename;?></title>
		<?php include "includeHeader.php";?>
		<div id="content" class="content">
			<?php
				if($failed > 0) {
			?>
			<div id="videotitle">
				<button onclick="goBack()" class="right btn btn-warning">Go Back</button>
				<h3>Error encountered. <?php echo $failed;?></h3>
			</div>
			<?php
				} else {
			?>
			<div id="videotitle">
				<h3>Terms of Service</h3>
			</div>
			<form method="post" action="">
				<input type="hidden" name="userid" value="<?php echo $userid;?>">
				<input type="hidden" name="iAgree" value="1">
				<p>
				Each person with access to CSU, Sacramento’s computing resources is responsible for their appropriate use and by their use agrees to comply with all applicable university, college, and departmental policies and regulations, and with applicable City, State and Federal laws and regulations, as well as with the acceptable use policies of affiliated networks and systems.
				</p>
				<p>
				The CSU respects freedom of expression in electronic communications on its computing and networking systems. Although this electronic speech has broad protections, all University community members are expected to use the information technology facilities considerately with the understanding that the electronic dissemination of information may be available to a broad and diverse audience including those outside the university. (Responsible Use Policy 8105.0 3.7)
				</p>
				<p>
				Users who publish or maintain information on CSU information assets are responsible for ensuring that information they post complies with applicable laws, regulations, and CSU/campus policies concerning copyrighted material and fair use of intellectual property. (Responsible Use Policy § 8105.0 4.1.8  |  CSU Chancellor’s Office Handbook of Copyright & Fair Use - <a href="http://www.calstate.edu/gc/docs/copyrightmanual.pdf" target="_blank">http://www.calstate.edu/gc/docs/copyrightmanual.pdf</a> )
				</p>
				<p>
				General Standards for the Acceptable Use of Computer Resources: Failure to uphold the following General Standards for the Acceptable Use of Computer Resources constitutes a violation of this policy and may be subject of account suspension and the removal of the associated media.
				</p>
				<?php
					if($dateAgreed==='failed'){ ?>
						<p>By clicking the below I Agree button, you agree and are bound to the above terms.</p>
						<input type="submit" class="btn btn-success submit" value="I Agree"><img class="submit hidden" src="./img/loading.gif" />
					<?php } else { ?>
						<br />
						<hr />
						<br />
						<p style="font-weight:bold">I, <?php echo $userATTR['FirstName'] . " " . $userATTR['LastName']; ?>, agreed to these terms on <?php echo date("F d, Y h:i:s a", $dateAgreed); ?></p>
					<?php } ?>
			</form>
			<?php } ?>
		</div>
		<?php include "includeFooter.php";?>
	</body>
</html>
