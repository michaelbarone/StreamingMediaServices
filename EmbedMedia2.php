<?php
require_once "init.php";
$failed = 0;
$mustLogin = 0;
$showDLLink=0;
if (isset($_GET['media'])) {
    $filePathHash = $_GET['media'];
    $ShowEntry = $Streaming->ShowEntry($filePathHash,"yes");
    if(!is_array($ShowEntry) && strpos($ShowEntry,'failed') !== false) {
        //failed found in ShowEntry return
        $failed = 1;
    }
    if(isset($ShowEntry['reqLogin']) && $ShowEntry['reqLogin']===1){
        $mustLogin = 1;
        require "CASauth.php";
    }    
} else {
    $failed = 1;
}
/*
if($mustLogin===1){
    $thistitle=$ShowEntry['filetitle'];
    $thisuser=$ShowEntry['userid'];
    // log entry if login required to watch video.  logging for non-auth videos located in streaming.class.php
    if(!isset($userid) || $userid===0){
        if(isset($_SESSION['userid'])) {
            $userid = $_SESSION['userid'];
        } elseif(isset($_SESSION['phpCAS']['user'])) {
            $userid = $_SESSION['phpCAS']['user'];
        } else {
            $userid = 1;
        }
    }
    $Streaming->MediaWatched($filePathHash,$userid,"embed");
    $log->LogInfo("Play Media File '$thistitle' ($filePathHash) uploaded by user:$thisuser was loaded for viewing by user:$userid in embed mode ");    
}
*/
$showTitle=0;
$showInfo=0;
$showAllText=1;
$showNoText=0;
$showPlayLink=0;
if(isset($_GET['showTitle'])) {
	$showTitle=1;
	$showAllText=0;
}
if(isset($_GET['showInfo'])) {
	$showInfo=1;
	$showAllText=0;
}
if(isset($_GET['showAllText'])) {
	$showAllText=1;
	$showNoText=0;
}
if(isset($_GET['showNoText'])) {
	$showNoText=1;
	$showAllText=0;
}
if(isset($_GET['showPlayLink'])) {
	$showPlayLink=1;
	$showDLLink=0;
}
if(isset($_GET['showDLLink'])) {
	$showDLLink=1;
	$showPlayLink=0;
}
// add in settings for setting video height/width
// add in settings for total size settings (including title and info)
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<style>
			* { margin:0; }
			#videotitle, #videoinfo { margin: 5px 5px !important; }
			#strobeplayer {
				width:267px;
				height:150px;
				margin-right:2.5px;
				float:left;
			}
		</style>
		<script src="./js/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="./player/lib/StrobeMediaPlayer.js"></script>
		<script type="text/javascript" src="./player/lib/jquery.strobemediaplayback.js"></script>
		<script type="text/javascript" src="./player/lib/jquery.strobemediaplaybackchrome.js"></script>
		<script type="text/javascript" src="./player/lib/swfobject.js"></script>		
	</head>
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
        <div id="videocontainer">
            <?php
            if($ShowEntry['postprocessed']===0) {
                echo "<p>This media file is not ready to stream yet.<br /><br />  This was enqueded for processing " . date("F d Y h:i:s a",$ShowEntry['created']) . ".  If it has been longer than 2 days, please contact <a style='display:inline-block;' href='mailto:web-courses@csus.edu?Subject=Support%20for%20SMS%20Streaming%20Media%20---%20Processing%20Issue%20-%20$filePathHash' target='_top'>web-courses@csus.edu</a>.</p>";
			} elseif($ShowEntry['disabled']==='1') {
				echo "<p>Access to this media has been disabled by the media owner.</p>";
			} else { ?>
				<div id="strobeplayer">
					<div class="strobemediaplayback" 
						 id="strobemediaplayback"
						 data-smp-favorFlashOverHtml5Video="true">Your browser does not support this video element.					
					</div>			
				
				
				
				
					<?php /*
					<object width="533.33333" height="300">
						<param name="movie" value="https://acorn.webapps.csus.edu/player/StrobeMediaPlayback.swf?src=http://video2.csus.edu/hds-vod/<?php echo $ShowEntry['filepath'];?>_M.f4m&amp;poster=<?php echo $ShowEntry['fileposter'];?>&amp;bufferingOverlay=false&amp;optimizeInitialIndex=true&amp;minContinuousPlayback=30&amp;bufferTime=12&amp;initialBufferTime=8&amp;expandedBufferTime=10" />
							<param name="allowFullScreen" value="true" />
						<!--[if !IE]>-->
							<object type="application/x-shockwave-flash" data="https://acorn.webapps.csus.edu/player/StrobeMediaPlayback.swf?src=http://video2.csus.edu/vod/<?php echo $ShowEntry['filepath'];?>_P.f4m&amp;poster=<?php echo $ShowEntry['fileposter'];?>&amp;bufferingOverlay=false&amp;optimizeInitialIndex=true&amp;minContinuousPlayback=30&amp;bufferTime=12&amp;initialBufferTime=8&amp;expandedBufferTime=10">
								<param name="allowFullScreen" value="true" />
						<!--<![endif]-->
						<div>
							<p>
								<video height="100%" width="100%" controls="" poster="<?php echo $ShowEntry['fileposter'];?>">
								<source src="http://video2.csus.edu/hls-vod/<?php echo $ShowEntry['filepath'];?>_M.m3u8" type="video/f4v">
								Your browser does not support Flash Player 10.1 or the HTML5 video element.
								</video>
							</p>        
						</div>
						<!--[if !IE]>-->
							</object>
						<!--<![endif]-->
					</object>
					*/ ?>
				</div>
				<?php if(($showTitle===1 || $showAllText===1) && $showNoText===0) { ?>
				<div id="videotitle">
					<p style='font-weight:bold;'><?php echo $ShowEntry['filetitle'];?></p>
				</div>
				<?php } ?>
				<?php if(($showInfo===1 || $showAllText===1) && $showNoText===0) { ?>
				<div id="videoinfo">
					<p><?php echo $ShowEntry['fileinfo'];?></p>
				</div>
				<?php } if($showPlayLink===1) { ?>
				<br>
				
				
				<?php // add in build url function here so its not hard coded ?>
				
				
				<a href="https://acorn.webapps.csus.edu/PlayMedia.php?media=<?php echo $filePathHash; ?>" target="_blank">Direct link to playback</a>
				<?php } elseif($showDLLink===1) { ?>
				<br>
				<a href="http://video2.csus.edu/hls-vod/<?php echo $ShowEntry['filepath'];?>_M.m3u8" target="_blank">Click Here if the video does not play</a>			
				<?php }
			}?>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
				$.fn.adaptiveexperienceconfigurator.rules.push(
					function(context, options){
						context.isFirefox = context.userAgent.match(/Firefox/i) != null;
						// try to get tls urls
						if (context.isAndroid && (context.isTablet || context.isPhone)) {
							context.setOption(options, "src", "http://video2.csus.edu/hls-vod/<?php echo $ShowEntry['filepath'];?>_M.m3u8");
						} else if (context.isiPad || context.isiPhone) {
							context.setOption(options, "src", "http://video2.csus.edu/hls-vod/<?php echo $ShowEntry['filepath'];?>_M.m3u8");
						} else if (context.isFirefox) {
							context.setOption(options, "src", "http://video2.csus.edu/vod/<?php echo $ShowEntry['filepath'];?>_P.f4m");
						} else {
							context.setOption(options, "src", "http://video2.csus.edu/hds-vod/<?php echo $ShowEntry['filepath'];?>_M.f4m");
						}
					}
				);
				

				<?php
				$hasCaption=0;
				if(isset($hasCaption) && $hasCaption===1) { ?>
					var subs = JSON.stringify({
					subtitles: [
						{
							//src: "https://acorn.webapps.csus.edu/player/The_Terminator_1984_roNy.srt", // subtitles source
							//src: "https://acorn.webapps.csus.edu/subs/<?php echo $ShowEntry['filepath'];?>.srt",
							//src: "https://acorn.webapps.csus.edu/subs/openroad.srt",
							// try to get tls urls
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
				<?php } ?>
				
				var options={
					swf: "https://acorn.webapps.csus.edu/player/StrobeMediaPlayback.swf",
					id: "StreamingVideo",
					name: "StreamingVideo",
					width: '100%',
					height: '100%',				
					poster: "<?php echo $ShowEntry['fileposter'];?>",
					playButtonOverlay: true,
					verbose: true,
				<?php
				if(isset($hasCaption) && $hasCaption===1) { ?>
					plugin_subs: "https://acorn.webapps.csus.edu/player/SubtitlesPlugin.swf", // enable Subtitles plugin (you can find it in the plugins folder)
					subs_namespace: "org.denivip.osmf.subtitles", // don't change this string!
					subs_src: encodeURIComponent(subs), // pass our params
				<?php } ?>
					allowScriptAccess: "always"
					// , favorFlashOverHtml5Video: false
				};

				var $player = $("#strobemediaplayback").strobemediaplayback(options);
				var $chrome = $player.strobemediaplaybackchrome(options);

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
		</script>
        <?php } ?>
    </div>
</html>
