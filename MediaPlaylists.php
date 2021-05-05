<?php
require_once "init.php";
require "CASauth.php";
require "checkAuth.php";
$failed = 0;
$inputsuccess = 0;
if(isset($_POST) && !empty($_POST)) {
	if(isset($_POST['newplaylist'])) {
		$newplaylist = $_POST['newplaylist'];
		$inputthis = $Streaming->CreatePlaylist("$userid","$newplaylist");
				if(strpos($inputthis,'failed') !== false) {
			$failed = 1;
			break;
		}
		$inputsuccess = 1;
	} else {
		$failed = 1;
	}
} else {
	$sortby = 'playlistName';
	if(isset($_GET['sortby'])) {
		$sortby = $_GET['sortby'];
	}
	$ListPlaylists = $Streaming->ListPlaylists($userid,$sortby);
	if(!is_array($ListPlaylists) && strpos($ListPlaylists,'failed') !== false) {
		$failed = 1;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php if($failed != 1) { echo "$PageTitlePrepend - " . $userATTR['FirstName'] . " " . $userATTR['LastName'] . "'s Playlists - $PROJECT_TITLE";}?></title>
		<?php include "includeHeader.php";?>
		<div id="content" class="content">
			<?php
				if($failed > 0) {
			?>
					<div id="videotitle">
						<h3>Error encountered.</h3>
					</div>				
			<?php
				} elseif($inputsuccess > 0) {
			?>
			<div id="videotitle">
				<h3>The Playlist is being created, this page will refresh automatically.</h3><img src="./img/loading.gif" />
				<script>setTimeout(function () { window.location.href = "MediaPlaylists.php"; }, 3000);</script>
			</div>
			<?php
				} else {
			?>
			<div id="videotitle">
				<h3><?php echo $userATTR['FirstName'] . " " . $userATTR['LastName'];?>'s Streaming Media Playlists</h3>
			</div>
			<div class="videolist currentlyprocessing col2">
				<div class='videolist header'>
					<span>Create New Playlist: <img class="btn-question" title="Information Popup" src="./img/question.gif" about="createplaylist" /></span>
				</div>
				<div class="videolist">
					<div class='videoentry'>
						<h2 class="left"></h2>
						<form action="" method="post">
							<input style="width:50%;" type="text" value="" name="newplaylist" />
							<input type="submit" class="btn btn-success" value="Create Playlist" />
						</form>
					</div>
				</div>
			</div>

			<br class="clear"><br>
			<?php 
				if(!empty($ListPlaylists)) {
			?>
			<div class="videolist header">
				<span>Playlists: <img class="btn-question" title="Information Popup" src="./img/question.gif" about="createplaylist" /><img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
				Sort By: <a href="?sortby=playlistName" <?php if($sortby == 'playlistName') { echo "class='selected'"; }?>>Playlist Name</a> | <a href="?sortby=created" <?php if($sortby == 'created') { echo "class='selected'"; }?>>Created Date</a>
			</div>
			<div class="videolist">
				<?php
					$thisurl = $Streaming->getUrl();
					$entrycount=0;
					foreach($ListPlaylists as $entry) {
						$entrycount++;
						$h = $entry['playlistHash'];
						$thislink = $thisurl . '/PlayMedia.php?playlist=' . $h;
						if(!isset($entry['filePoster']) || $entry['filePoster'] == '') { $entry['filePoster'] = "./img/defaultposter.png"; }
						?>
						<div class='videoentry'>
							<?php /*
							<!-- accordian wrapper to show playlist items? -->
							*/ ?>
							<form class="right" action="MediaDetails.php" method="post">
								<input type="hidden" value="<?php echo $h;?>" name="h" />
								<input type="hidden" value="playlist" name="type" />
								<input type="submit" class="btn btn-warning" value="Edit" />
							</form>
							<h2><?php echo $entry['playlistName']; ?></h2>							
							<p class="clear">Created: <?php echo date("F d, Y h:i a",$entry['created']); ?></p>
							<p class="link">
								<a href="javascript:void(0)" class="btn btn-primary clipboard" data-clipboard-target='#link<?php echo $entrycount;?>' defaulttext="Copy Direct Link">Copy Direct Link</a>
								<img class="btn-question" title="Information Popup" src="./img/question.gif" about="videolink" />
								<a href="<?php echo $thislink;?>" id='link<?php echo $entrycount;?>' target="_blank"><?php echo $thislink;?></a>
							</p>
						</div>
						<?php
					}
				?>
			</div>
			<?php } else { ?>
					<div class="videolist">
						<h2>You have no Playlists.  Create a Playlist above to get started.</h2>
					</div>
				<?php }
				}
			?>
		</div>
		<?php include "includeFooter.php";?>
	</body>
</html>
