<?php
require_once "init.php";
$failed = 0;
$mustLogin = 0;
$playlist = 0;
if (isset($_GET['playlist'])) {
	$playlist = 1;
	$playlistHash = $_GET['playlist'];
	$playlistEntry = $Streaming->GetPlaylist($playlistHash);
	$hashes = explode(",",$playlistEntry[0]['mediaHashes']);
}
if (isset($_GET['media']) || $hashes[0]!='') {
	if(isset($_GET['media'])) {
		$filePathHash = $_GET['media'];
	}else {
		$filePathHash = $hashes[0];
	}
	$ShowEntry = $Streaming->ShowEntry($filePathHash);
	if(!is_array($ShowEntry) && strpos($ShowEntry,'failed') !== false) {
		$failed = 1; //failed found in ShowEntry return
	}
	if(isset($ShowEntry['reqLogin']) && $ShowEntry['reqLogin']==='1'){
		$mustLogin = 1;
		require "CASauth.php";
	}
} else {
    $failed = 1;
}
if($mustLogin===1){
	$thistitle=$ShowEntry['filetitle'];
	$thisuser=$ShowEntry['userid'];
	// log entry if login required to watch video.  logging for non-auth videos located in streaming.class.php
	if(!isset($userid)){
		if(isset($_SESSION['userid'])) {
			$userid = $_SESSION['userid'];
		} elseif(isset($_SESSION['phpCAS']['user'])) {
			$userid = $_SESSION['phpCAS']['user'];
		} else {
			$userid = 0;
		}
	}
	$Streaming->MediaWatched($filePathHash,$userid);
	$log->LogInfo("Play Media File '$thistitle' ($filePathHash) uploaded by user:$thisuser was loaded for viewing by user:$userid ");	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CSUS Streaming - <?php echo $ShowEntry['filetitle'];?></title>
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
				<?php if($playlist>0) { ?>
				<style>
					#Playlistentries {
						margin: 60px 0;
					}
					
					#videocontainer {
					    left: 0;
						margin: 0 15px !important;
						width: 60%;
					}
				</style>
				<div id="playlist">
					<ul id="Playlistentries" class="playlistContainer right currentlyprocessing col3">
					<div class='videolist header'><span>Playlist - <?php echo $playlistEntry[0]['playlistName'];?></span></div>
					<?php
						$i=0;
						foreach($hashes as $hash) {
							if($hash==='') { continue; }
							$editEntry = $Streaming->ShowEntrySupport($hash);
							if($filePathHash===$hash) { $selected="class='selected'"; } else { $selected=''; }
							echo "<a href='?media=$hash&playlist=$playlistHash'><li $selected><img src='" . $editEntry['fileposter'] . "'/> <span>" . $editEntry['filetitle'] . "</span><br>" . $editEntry['fileinfo'] . "</li></a>";
							$i++;
						}
					?>
					</ul>
				</div>
				<?php } ?>
			<div id="videocontainer">
				<div id="videotitle">
					<h3><?php echo $ShowEntry['filetitle'];?></h3>
				</div>
				<?php
				if($ShowEntry['postprocessed']==='0') {
					echo "<p>This media file is not ready to stream yet.<br /><br />  This was enqueded for processing " . date("F d Y h:i:s a",$ShowEntry['created']) . ".  If it has been longer than 2 days, please contact <a style='display:inline-block;' href='mailto:web-courses@csus.edu?Subject=Support%20for%20SMS%20Streaming%20Media%20---%20Processing%20Issue%20-%20$filePathHash' target='_top'>web-courses@csus.edu</a>.</p>";
				} elseif($ShowEntry['disabled']==='1') {
					echo "<p>Access to this media has been disabled by the media owner.</p>";
				} else { ?>
				<div id="strobeplayer">
					<object id="thisstrobeplayer">
						<param name="movie" value="https://acorn.webapps.csus.edu/player/StrobeMediaPlayback.swf?src=http://video2.csus.edu/hds-vod/<?php echo $ShowEntry['filepath'];?>_M.f4m&amp;poster=<?php echo $ShowEntry['fileposter'];?>&amp;bufferingOverlay=false&amp;optimizeInitialIndex=true&amp;minContinuousPlayback=30&amp;bufferTime=12&amp;initialBufferTime=8&amp;expandedBufferTime=10" />
							<param name="allowFullScreen" value="true" />
						<!--[if !IE]>-->
							<object type="application/x-shockwave-flash" data="https://acorn.webapps.csus.edu/player/StrobeMediaPlayback.swf?src=http://video2.csus.edu/vod/<?php echo $ShowEntry['filepath'];?>_P.f4m&amp;poster=<?php echo $ShowEntry['fileposter'];?>&amp;bufferingOverlay=false&amp;optimizeInitialIndex=true&amp;minContinuousPlayback=30&amp;bufferTime=12&amp;initialBufferTime=8&amp;expandedBufferTime=10">
								<param name="allowFullScreen" value="true" />
						<!--<![endif]-->
						<div>
							<p>
								<video height="100%" width="100%" controls="" poster="<?php echo $ShowEntry['fileposter'];?>">
								<source src="http://video2.csus.edu/hls-vod/<?php echo $ShowEntry['filepath'];?>_M.m3u8">
								Your browser does not support Flash Player 10.1 or the HTML5 video element. 
								</video>
							</p>		
						</div>
						<!--[if !IE]>-->
							</object>
						<!--<![endif]-->
					</object>
				</div>

				
				
				
				<div id="videoinfo">
					<p><?php echo $ShowEntry['fileinfo'];?></p>
				</div>

				<br class="clear">
				
				
					<!-- strobe section -->
				
					<script type="text/javascript" src="./player/lib/StrobeMediaPlayer.js"></script>
					<script type="text/javascript" src="./player/lib/jquery.strobemediaplayback.js"></script>
					<script type="text/javascript" src="./player/lib/jquery.strobemediaplaybackchrome.js"></script>
					<script type="text/javascript" src="./player/lib/swfobject.js"></script>
					
					
					<div>
						<div>
							<span id="currentFile" > ... </span><br />
							<span id="currentTime" /> ... </span> : <span id="duration" /> ... </span>
						</div>   
						<a href="#" id="play-pause">Play/Pause</a>
					</div>
					<div class="playercontainer">
						<div class="strobemediaplayback" 
							 id="strobemediaplayback"
							 data-smp-favorFlashOverHtml5Video="true">Alternative content					
						</div>
					</div>

				<script type="text/javascript">
					$(document).ready(function() {
						$.fn.adaptiveexperienceconfigurator.rules.push(
							function(context, options){
								context.isFirefox = context.userAgent.match(/Firefox/i) != null;
								if (context.isAndroid && (context.isTablet || context.isPhone)) {
									// context.setOption(options, "src", "http://video2.csus.edu/vod/<?php echo $ShowEntry['filepath'];?>_P.f4m");
									context.setOption(options, "src", "http://video2.csus.edu/hls-vod/<?php echo $ShowEntry['filepath'];?>_M.m3u8");
								} else if (context.isiPad || context.isiPhone) {
									context.setOption(options, "src", "http://video2.csus.edu/hls-vod/<?php echo $ShowEntry['filepath'];?>_M.m3u8");
								} else if (context.isFirefox) {
									// context.setOption(options, "favorFlashOverHtml5Video", "true");
									context.setOption(options, "src", "http://video2.csus.edu/vod/<?php echo $ShowEntry['filepath'];?>_P.f4m");
									// context.setOption(options, "src", "http://players.edgesuite.net/videos/big_buck_bunny/bbb_448x252.ogg");
								} else {
									// context.setOption(options, "favorFlashOverHtml5Video", "false");
									// context.setOption(options, "src", "http://video2.csus.edu/hls-vod/<?php echo $ShowEntry['filepath'];?>_M.m3u8");
									context.setOption(options, "src", "http://video2.csus.edu/hds-vod/<?php echo $ShowEntry['filepath'];?>_M.f4m");
									// context.setOption(options, "src", "http://players.edgesuite.net/videos/big_buck_bunny/bbb_448x252.mp4");
								}
							}
						);
						
						/* if subs */
						var subs = JSON.stringify({
						subtitles: [
							{
								//src: "https://acorn.webapps.csus.edu/player/The_Terminator_1984_roNy.srt", // subtitles source
								//src: "https://acorn.webapps.csus.edu/subs/<?php echo $ShowEntry['filepath'];?>.srt",
								//src: "https://acorn.webapps.csus.edu/subs/openroad.srt",
								//src: "http://video2.csus.edu/hds-vod/<?php echo $ShowEntry['filepath'];?>.srt",
								src: "http://www.csus.edu/vid/<?php echo $ShowEntry['filepath'];?>.srt",
								label: "English", // label for menu
								language: "en" // language (service params)
							}
						],
						config: {
							fontSize: 28,
							fontColor: 0xffffff,
							position: 40
						}
						});
						/* end if subs  */
						
						var options={
							swf: "https://acorn.webapps.csus.edu/player/StrobeMediaPlayback.swf",
							id: "StreamingVideo",
							name: "StreamingVideo",
							width: '100%',
							height: '100%',				
							poster: "<?php echo $ShowEntry['fileposter'];?>",
							playButtonOverlay: true,
							verbose: true,
							/* if subs */
							plugin_subs: "https://acorn.webapps.csus.edu/player/SubtitlesPlugin.swf", // enable Subtitles plugin (you can find it in the plugins folder)
							subs_namespace: "org.denivip.osmf.subtitles", // don't change this string!
							subs_src: encodeURIComponent(subs), // pass our params
							/* end if subs */
							allowScriptAccess: "always"
							// , favorFlashOverHtml5Video: false
						};

						var $player = $("#strobemediaplayback").strobemediaplayback(options);
						var $chrome = $player.strobemediaplaybackchrome(options);

						$("#play-pause").bind("click", $player, onPlayPauseClick);
						$player.bind("timeupdate", playbackLog, onTimeUpdate);
					});
				
					var playbackLog = {};
					var playbackLogCount = 0;
					var ct = 0;
					var pbtimer;

					function sendPlaybackLog(sendtoplaybackLog){
						$.ajax({
							url: 'curl.php?request=MediaPlaybackTime&param=' + JSON.stringify(sendtoplaybackLog) + '&mediaHash=<?php echo $filePathHash;?>',
							success: function(data) {
							},
							error: function(data, errorThrown) {
								var param = encodeURIComponent('ERROR: could not sendPlaybackLog: ' + errorThrown + ' additional data: ' + data + '');
								$.ajax({
									url: 'curl.php?request=LogThis&logType=LogError&param=' + param,
									success: function(data) {
									}		
								});
							}
						});
					}

					function onTimeUpdate(event){
						$("#currentTime").html(this.currentTime);
						timenow = Math.floor((new Date).getTime()/1000);
						currenttime = Math.floor(this.currentTime/10);
						clearTimeout(pbtimer);
						pbtimer = setTimeout(function(){
												playbackLogCount=0;
												var sendtoplaybackLog = playbackLog;
												playbackLog = {};
												sendPlaybackLog(sendtoplaybackLog);
											}, 5000);
						if(ct != currenttime){
							playbackLog[playbackLogCount] = [{'timestamp': timenow, 'currentTime': currenttime, 'totalTime': Math.floor(this.duration/10)}];
							ct = currenttime;
							playbackLogCount++;
							if(playbackLogCount > 5) {
								playbackLogCount=0;
								var sendtoplaybackLog = playbackLog;
								playbackLog = {};
								sendPlaybackLog(sendtoplaybackLog);
							}
						}
					}				
				
					function onPlayPauseClick(event){
						event.preventDefault();
						var video = event.data[0];
						if (video.paused) {
							video.play();
						} else {
							video.pause();
						}
						return false;
					}
				</script>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php include "includeFooter.php";?>
	</body>
</html>