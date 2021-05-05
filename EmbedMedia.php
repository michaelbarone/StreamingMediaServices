<?php
require_once "init.php";
$failed = 0;
$mustLogin = 0;
$clipStartTime = "NaN";
$clipEndTime = "NaN";
if (isset($_GET['clipStartTime']) && is_numeric($_GET['clipStartTime']) && $_GET['clipStartTime'] > 0) {
	$clipStartTime = $_GET['clipStartTime'];
}
if (isset($_GET['clipEndTime']) && is_numeric($_GET['clipEndTime']) && $_GET['clipEndTime'] > 0) {
	$clipEndTime = $_GET['clipEndTime'];
}
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<style>
			* { margin:0; }
			#videotitle, #videoinfo { margin: 5px 5px !important; }
			#videocontainer {
			    width: 100%;
				height: 100%;
				position: absolute;
				overflow: hidden;
			}
			#strobeplayer {
				width:100%;
				height:100%;
				#margin-right:2.5px;
				#float:left;
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
				</div>
			<?php }?>
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
					clipStartTime: "<?php echo $clipStartTime;?>",
					clipEndTime: "<?php echo $clipEndTime;?>",
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