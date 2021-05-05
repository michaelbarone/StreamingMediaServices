<?php
if(!isset($processthese)) {
	require_once("init.php");
	require_once("checkAuth.php");
	$processthese = $Streaming->NeedsProcessing($userid);
	if($processthese == "failed" || empty($processthese)) {
		exit;
	}
}
?>
<div class="videolist needsprocessing right">
	<div class='videolist header'>
		<span>Info Needed Before Processing: <img class="btn-question" title="Information Popup" src="./img/question.gif" about="needsinfo" /><img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
	</div>
	<div class="videolist">
	<?php
		$uploaddir = $Streaming->GetUploadDir() . "/$userid/";
		foreach($processthese as $file) {
			?>
			<div class='videoentry'>
				<h2 class="left"><?php echo $file; ?></h2>
				<form class="right" action="MediaDetails.php" method="post">
					<input type="hidden" value="<?php echo $file;?>" name="filename" />
					<input type="submit" class="btn btn-danger" value="Process Video" />
				</form>
				<p class="clear">Uploaded: <?php echo date("F d, Y h:i a", filemtime($uploaddir.$file)); ?></p>
			</div>
			<?php
		}
	?>
	</div>
</div>
<script>
$(document).ready(function() {
	$('img.btn-question').click(function() {
		var thisinfo = $(this).attr("about");
		InfoPopup(thisinfo);
	});
});
</script>