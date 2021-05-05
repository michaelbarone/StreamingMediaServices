<?php
require_once "init.php";
require "CASauth.php";
require "checkAuth.php";
$failed = 0;
$sortby = 'folder';
$entrycount=0;
$ListEntries = $Streaming->ListEntries($userid,$sortby);
if(!is_array($ListEntries) && strpos($ListEntries,'failed') !== false) {
	$failed = 1;
}
if($userid==='atcs-04'){
	$userATTR['FirstName']="Example";
	$userATTR['LastName']="Instructor";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php if($failed != 1) { echo "$PageTitlePrepend - " . $userATTR['FirstName'] . " " . $userATTR['LastName'] . "'s Media - $PROJECT_TITLE";}?></title>
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
				<h3><?php echo $userATTR['FirstName'] . " " . $userATTR['LastName'];?>'s Streaming Media Library
					<a href="#" type="button" class="btn btn-gold tutorial-link right" tutorial="library"><img class='left' style="height:20px;margin-right:5px;" src='./img/info.png' />Tutorial</a>
				</h3>
			</div>
			<div id="uploadarea" class="left">
				<fieldset id="videouploadfield">
					<legend>Drag & Drop a video file inside to upload (.mp4 .m4v or .mov only) <img class="btn-question" title="Information Popup" src="./img/question.gif" about="dropvideo" /></legend>
					<div class="videoupload" > </div>
					<span class="loadingimg" style="display:none;">Continue uploading media<br />Start processing media<br /> or click <img class="btn-refresh nofloat" title="Refresh this Page" src="./img/refresh.png" /> to refresh the page.</span>
					<br>
				</fieldset>
			</div>
			<div id="needsprocessingcontainer">
				<?php 
					$processthese = $Streaming->NeedsProcessing($userid);
					if(!empty($processthese)) {
						include('includeNeedsProcessing.php');
					}
				?>
			</div>
			<br class="clear"><br>
			<?php 
				$currentlyprocessing = $Streaming->CurrentlyProcessing($userid);
				if(!empty($currentlyprocessing)) {
			?>
				<div class="videolist currentlyprocessing">
					<div class="videolist header">
						<span>Media Being Processed: <img class="btn-question" title="Information Popup" src="./img/question.gif" about="processing" /><img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
					</div>
					<div class="videolist">
						<?php
							$thisurl = $Streaming->getUrl();
							foreach($currentlyprocessing as $entry) {
								$entrycount++;
								$h = $entry['filePathHash'];
								$thislink = $thisurl . '/PlayMedia.php?media=' . $h;
								$thisembedlink = $thisurl . '/EmbedMedia.php?media=' . $h;
								if(!isset($entry['filePoster']) || $entry['filePoster'] == '') { $entry['filePoster'] = "./img/defaultposter.png"; }
								?>
								<div class='videoentry'>
									<?php if($entry['disabled']==="1") { ?>
										<span class="disabled">This Media is currently Disabled.  Edit this to re-enable access.</span>
									<?php } ?>
									<div class="topbuttons">
										<form class="right" action="MediaDetails.php" method="post">
											<input type="hidden" value="<?php echo $h;?>" name="h" />
											<input type="hidden" value="1" name="edit" />
											<input type="submit" class="btn btn-warning" value="Edit" />
										</form>
										<a href="javascript:void(0)" id="move<?php echo $h;?>" class="btn btn-primary right" onclick="return GetMediaLibraryFolders('<?php echo $userid;?>','<?php echo $h;?>');"><img class='left' style="height:20px;margin-right:5px;" src='./img/folder.png' />Move to Folder</a>
										<p class="clear">Enqueued: <?php echo date("F d, Y h:i a",$entry['created']); ?></p>
										<p class="bold clear">This file is not yet ready to stream.</p>
									</div>
									<div class="left col2">
										<img src="<?php echo $entry['filePoster']; ?>" />
										<h2><?php echo $entry['fileTitle']; ?></h2>
										<p><?php echo $entry['fileInfo']; ?></p>
									</div>
									<p class="clear">Require Saclink Login to view?: <span class="bold"><?php echo ($entry['reqLogin']==="1") ? "YES" : "NO"; ?></span></p><br>
									<p class="link">
										<a href="javascript:void(0)" class="btn btn-primary clipboard" data-clipboard-target='#link<?php echo $entrycount;?>' defaulttext="Copy Direct Link">Copy Direct Link</a>
										<img class="btn-question" title="Information Popup" src="./img/question.gif" about="videolink" />
										<a href="<?php echo $thislink;?>" id='link<?php echo $entrycount;?>' target="_blank"><?php echo $thislink;?></a>
									</p>
									<br>
									<a href="javascript:void(0)" class='advancedToggle btn btn-default'>More Options</a>
									<div class="advanced hidden">
										<p class="link">
											<a href="javascript:void(0)" class="btn btn-primary clipboard" data-clipboard-target='#embed<?php echo $entrycount;?>' defaulttext="Copy Embed Code">Copy Embed Code</a>
											<img class="btn-question" title="Information Popup" src="./img/question.gif" about="embed" />
											<textarea id='embed<?php echo $entrycount;?>' style='width:75%; vertical-align:middle;' readonly><iframe width='640' height='360' src='<?php echo $thisembedlink;?>' frameborder='0' allowfullscreen=''></iframe></textarea>
										</p>
									</div>										
								</div>
								<?php
							}
						?>
					</div>
				</div>
				<br class="clear"><br>
			<?php
				}
				if(!empty($ListEntries)) {
			?>
			<div class="videolist header">
				<span>Available Streaming Media: <img class="btn-question" title="Information Popup" src="./img/question.gif" about="available" /><img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
				<br />
			</div>
			<div class="videolist">
				<?php
					$thisurl = $Streaming->getUrl();
					$ListEntriesArray = [];
					foreach($ListEntries as $entry) {
						if(!isset($entry['folder']) || $entry['folder'] == '') { $entry['folder'] = "Unsorted"; }
						$ListEntriesArray[$entry['folder']][$entry['indexID']] = $entry; 
					}
					echo "<ul class='accordian LibraryFolders'>";
					$thisurl = $Streaming->getUrl();
					$count=1;
					$howmany=0;
					foreach($ListEntriesArray as $foldername => $unitid) {
						$howmany=count($unitid);
						if($count===1) { echo "<li class='has-sub open'>"; } else { echo "<li class='has-sub'>"; }
						echo "<a href='#'><img class='left' src='./img/folder.png' /><span>$foldername ($howmany media files)</span></a>";
						if($count===1) { echo "<ul style='display:block;'>"; } else { echo "<ul>"; }
						foreach($unitid as $entry) {
							$h = $entry['filePathHash'];
							$thislink = $thisurl . '/PlayMedia.php?media=' . $h;
							$thisembedlink = $thisurl . '/EmbedMedia.php?media=' . $h;
							$entrycount++;
							if(!isset($entry['filePoster']) || $entry['filePoster'] == '') { $entry['filePoster'] = "./img/defaultposter.png"; }
							?>
							<div class='videoentry clear'>
								<?php if($entry['disabled']==="1") { ?>
									<span class="disabled">This Media is currently Disabled.  Edit this to re-enable access.</span>
								<?php } ?>
								<div class="topbuttons">
									<form class="" action="MediaDetails.php" method="post">
										<input type="hidden" value="<?php echo $h;?>" name="h" />
										<input type="hidden" value="1" name="edit" />
										<input type="submit" class="btn btn-warning" value="Edit" />
									</form>
									<form class="" action="Stats.php" method="post" target="_blank">
										<input type="hidden" value="<?php echo $h;?>" name="h" />
										<input type="submit" class="btn btn-info" value="Stats" />
									</form>
									<a href="javascript:void(0)" id="move<?php echo $h;?>" class="btn btn-primary" onclick="return GetMediaLibraryFolders('<?php echo $userid;?>','<?php echo $h;?>');"><img class='left' style="height:20px;margin-right:5px;" src='./img/folder.png' />Move to Folder</a>
									<p class="clear">Created: <?php echo date("F d, Y h:i a",$entry['created']); ?></p>
								</div>
								<div class="left col2">
									<a href="<?php echo $thislink;?>" class="posterlink" target="_blank"><img src="<?php echo $entry['filePoster']; ?>" /><img class="posterplay" src="./img/play.png" /></a>
									<h2><?php echo $entry['fileTitle']; ?></h2>
									<p><?php echo $entry['fileInfo']; ?></p>
								</div>
								<p class="clear">Require Saclink Login to view?: <span class="bold"><?php echo ($entry['reqLogin']==="1") ? "YES" : "NO"; ?></span></p><br>
								<p class="link">
									<a href="javascript:void(0)" class="btn btn-primary clipboard" data-clipboard-target='#link<?php echo $entrycount;?>' defaulttext="Copy Direct Link">Copy Direct Link</a>
									<img class="btn-question" title="Information Popup" src="./img/question.gif" about="videolink" />
									<a href="<?php echo $thislink;?>" id='link<?php echo $entrycount;?>' target="_blank"><?php echo $thislink;?></a>
								</p>
								<br>
								<a href="javascript:void(0)" class='advancedToggle btn btn-default'>More Options</a>
								<div class="advanced hidden">
									<p class="link">
										<a href="javascript:void(0)" class="btn btn-primary clipboard" data-clipboard-target='#embed<?php echo $entrycount;?>' defaulttext="Copy Embed Code">Copy Embed Code</a>
										<img class="btn-question" title="Information Popup" src="./img/question.gif" about="embed" />
										<textarea id='embed<?php echo $entrycount;?>' style='width:75%; vertical-align:middle;' readonly><iframe width='640' height='360' src='<?php echo $thisembedlink;?>' frameborder='0' allowfullscreen=''></iframe></textarea>
									</p>
								</div>
							</div>
							<?php
						}
						$count++;
						echo "</ul></li>";	
					}
				echo "</ul>";	
				?>					
			</div>
			<?php } else {
					if(empty($currentlyprocessing)) { ?>
						<div class="videolist">
							<h2>You have no available streaming media.  Upload media above to get started.</h2>
							<p class="info">If you would like to test this system and dont have any video file readily available, you can <a href="http://media1.csus.edu/prj/indiv/00sample/CSUS_sample_video.mp4" download="downloadfilename">Download our Sample Video for testing.</a></p>
						</div>
				<?php } else { ?>
						<div class="videolist">
							<h2>Your uploaded media is being processed and should be available soon.</h2>
						</div>
				<?php } ?>
						<hr />
						<div id="videocontainer">
							<center>
								<span style="font-weight:bold">Getting Started with Streaming Media Services:</span>
								<iframe width='640' height='360' src='https://acorn.webapps.csus.edu/EmbedMedia.php?media=77b493731fac39d18717a850413e64296973ceb2' frameborder='0' allowfullscreen=''></iframe>
							</center>
						</div>
				<?php }
			}
			?>
		</div>
		<?php include "includeFooter.php";?>
		<script type="text/javascript">
			$('.videoupload').ajaxupload({
				url:'uploadvideo.php',
				dropArea:'#videouploadfield',
				autoStart: true,
				finish:function(files, filesObj){
					document.getElementsByClassName('loadingimg')[0].style.display='block';
					setTimeout($("#needsprocessingcontainer").load('includeNeedsProcessing.php'),1000);
				}
			});
		</script>
	</body>
</html>