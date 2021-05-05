<?php
require_once "init.php";
$failed = 0;
if(isset($statsExampleSwitch) && $statsExampleSwitch == 1) {
	$userid = "ExampleView";
	$userlevel = '';
} else {
	$statsExampleSwitch = 0;
	$groupaccess="denied";
	require "CASauth.php";
	require "checkAuth.php";
	if($disableCAS===0 && isset($_SESSION['phpCAS']['user']) && isset($_SESSION['phpCAS']['attributes']['Description'])) {
		$userid=$_SESSION['phpCAS']['user'];
		$thisdescription=$_SESSION['phpCAS']['attributes']['Description'];
		$groupList=$_SESSION['phpCAS']['attributes']['Description'];
	}elseif($disableCAS===1){
		$thisdescription=$userATTR['Description'];
		if(isset($userATTR['Groups'])) {
			$groupList=$userATTR['Groups'];
		} else {
			$groupList='';
		}
	}
	$groupaccess = $Streaming->CheckAuthGroupAccess($thisdescription,$groupList,$userid);
	if($groupaccess==="allowed" && !isset($userlevel)) {
		$userlevel = $Streaming->UserLevel($userid);
	}
}
if((isset($_POST) && !empty($_POST)) || $statsExampleSwitch == 1) {
	if(isset($_POST['h']) || $statsExampleSwitch == 1) {
		if($statsExampleSwitch == 1) {
			$h="8d8686c631973e4a3267697de7e670aa463b329e";
		} else {
			$h=$_POST['h'];			
		}
		$mediainfo = $Streaming->ShowEntrySupport("$h");
		if(($userid !== $mediainfo['userid'] && $userlevel!=="admin") && $statsExampleSwitch == 0) {
			$failed = 5;
			$log->LogWarn("UNATHORIZED ACCESS: user:$userid tried to view Stats for media:" . $h . " without access.");
		}
		$stats = $Streaming->GetMediaStats("$h");
		$mediaplaybacktime = $Streaming->getMediaPlaybackTime("$h");
		//other logging needed?
		$log->LogInfo("Media Stats accessed by user:$userid for media:" . $h . " ");

		if(!empty($stats)){
			$sundayViews=0;
			$mondayViews=0;
			$tuesdayViews=0;
			$wednesdayViews=0;
			$thursdayViews=0;
			$fridayViews=0;
			$saturdayViews=0;
			$hour00views=0;
			$hour01views=0;
			$hour02views=0; 
			$hour03views=0; 
			$hour04views=0; 
			$hour05views=0; 
			$hour06views=0; 
			$hour07views=0; 
			$hour08views=0; 
			$hour09views=0; 
			$hour10views=0; 
			$hour11views=0; 
			$hour12views=0; 
			$hour13views=0; 
			$hour14views=0; 
			$hour15views=0; 
			$hour16views=0; 
			$hour17views=0; 
			$hour18views=0; 
			$hour19views=0; 
			$hour20views=0; 
			$hour21views=0; 
			$hour22views=0; 
			$hour23views=0; 
			$viewsOverTime[] = array();
			$osviews[] = array();
			$browserviews[] = array();
			
			foreach($stats as $view) {
				$thisdate = date("F j, Y",$view['Timestamp']);
				$viewsOverTime[$thisdate][$view['Timestamp']]=$view['userid'];
				
				$thisos=($view['os']=='null') ? 'unknown' : $view['os'];
				$osviews[$thisos][$view['Timestamp']]=$view['userid'];
				
				$thisbrowser=($view['browser']=='null') ? 'unknown' : $view['browser'];
				$thisbrowserv=($view['browserv']=='null') ? 'unknown' : $view['browserv'];
				
				$browserviews[$thisbrowser][$thisbrowserv][$view['Timestamp']]='1';
				
				$thisday = date("l",$view['Timestamp']);
				switch($thisday) {
					case 'Sunday':
						$sundayViews++;
						break;
					case 'Monday':
						$mondayViews++;
						break;
					case 'Tuesday':
						$tuesdayViews++;
						break;
					case 'Wednesday':
						$wednesdayViews++;
						break;
					case 'Thursday':
						$thursdayViews++;
						break;
					case 'Friday':
						$fridayViews++;
						break;
					case 'Saturday':
						$saturdayViews++;
						break;

				}
				$thishour = date("H",$view['Timestamp']);
				switch($thishour) {
					case '00':
						$hour00views++; 
						break;
					case '01':
						$hour01views++; 
						break;
					case '02':
						$hour02views++; 
						break;
					case '03':
						$hour03views++; 
						break;
					case '04':
						$hour04views++; 
						break;
					case '05':
						$hour05views++; 
						break;
					case '06':
						$hour06views++; 
						break;
					case '07':
						$hour07views++; 
						break;
					case '08':
						$hour08views++; 
						break;
					case '09':
						$hour09views++; 
						break;
					case '10':
						$hour10views++; 
						break;
					case '11':
						$hour11views++; 
						break;
					case '12':
						$hour12views++; 
						break;
					case '13':
						$hour13views++; 
						break;
					case '14':
						$hour14views++; 
						break;
					case '15':
						$hour15views++; 
						break;
					case '16':
						$hour16views++; 
						break;
					case '17':
						$hour17views++; 
						break;
					case '18':
						$hour18views++; 
						break;
					case '19':
						$hour19views++; 
						break;
					case '20':
						$hour20views++; 
						break;
					case '21':
						$hour21views++; 
						break;
					case '22':
						$hour22views++; 
						break;
					case '23':
						$hour23views++; 
						break;
				}
			}
			$playBackHeatMapArray='';
			$playBackHeatMapSpline='';
			$count=0;
			if(isset($mediaplaybacktime)&& $mediaplaybacktime != "failed") {
				foreach($mediaplaybacktime as $thistime => $key){
					if($thistime=="totalTime" || $thistime < 0){
						continue;
					} else {
						if($count!=0){ $playBackHeatMapArray.=",";$playBackHeatMapSpline.=", "; }
						$playBackHeatMapSpline.=count($key)-1;
						$playBackHeatMapArray.="[" . $thistime*10 . ",0," . (count($key)-1) . "]";
						$count++;
					}
				}
			}
		}
	} else {
		$failed = 1;
		$log->LogWarn("UNATHORIZED ACCESS: user:$userid tried to view Stats but did not include fileHash.");
	}
} else {
	$failed = 1;
	$log->LogWarn("UNATHORIZED ACCESS: user:$userid tried to view Stats but did not have post data.");	
}
$timenow = time();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CSUS Streaming - Viewing Statistics for: <?php echo $mediainfo['filetitle']; ?></title>
		<?php if(count($stats)>0) {?>
		<div id="pdfcontent">
		<?php } ?>
		<?php include "includeHeader.php";?>
		<script src="./js/highcharts.js"></script>
		<script src="./js/highcharts/modules/heatmap.js"></script>
		<script src="./js/highcharts/modules/data.js"></script>
		<script src="./js/jspdf.min.js"></script>
		<script src="./js/html2canvas.js"></script>
		<script src="./js/html2canvas.svg.js"></script>
		<script src="./js/rgbcolor.js"></script>
		<script src="./js/canvg.js"></script>
		<?php if(count($stats)>0) {?>
		<style>
			html, body {
			  position: relative !important;
			}
		</style>
		<?php } ?>
		<div id="content" class="content">
			<?php
				if($failed == 5) {
			?>
			<div id="videotitle">
				<button onclick="close_window()" class="right btn btn-warning">Close</button>
				<h3>You do not have access to edit this media.  This attempt has been logged.</h3>
			</div>
			<?php
				} elseif($failed > 0) {
			?>
			<div id="videotitle">
				<button onclick="close_window()" class="right btn btn-warning">Close</button>
				<h3>Error encountered.</h3>
			</div>
			<?php } else { ?>
			<div id="videotitle">
				<button onclick="close_window()" class="right btn btn-warning" data-html2canvas-ignore="true">Close</button>
				<?php if(count($stats)>0 && $statsExampleSwitch == 0) {?>
				<button onclick="javascript:downloadPDF();" class="right btn btn-primary" data-html2canvas-ignore="true">Download PDF</button>
				<?php } ?>
				<h3>Viewing Statistics for: <?php echo $mediainfo['filetitle']; ?><br>
				Statistics as of: <?php echo date("F d, Y g:ia",$timenow); ?></h3>
			</div>
			<div id="stats">
				<div class="col4">
					<fieldset>
						<legend>
							Created Date
						</legend>
						<?php echo date("F d, Y g:ia",$mediainfo['created']); ?>
					</fieldset>	
				</div>	

				<div class="col4">
					<fieldset>
						<legend>
							Total Views
						</legend>
						<?php echo count($stats); ?>
					</fieldset>	
				</div>

				<?php if(count($stats)===0) {?>
					<br class="clear" />
					<h3>Additional info will be available after this video has been viewed.</h3>
				
				<?php } else { ?>
				
				
				<div class="col4">
					<fieldset>
						<legend>
							First View
						</legend>
						<?php echo date("F d, Y g:ia",$stats[0]['Timestamp']); ?>
					</fieldset>	
				</div>	

				<div class="col4">
					<fieldset>
						<legend>
							Latest View
						</legend>
						<?php 
						$lastentry = end($stats);
						echo date("F d, Y g:ia",$lastentry['Timestamp']); ?>
					</fieldset>	
				</div>	
				<br class="clear" />
				<br class="clear" />
				<?php if(isset($mediaplaybacktime['totalTime'])) {	?>
				<div id="playBackHeatMap" style="width:100%; height:220px;"></div>
				<?php } ?>
				<br class="clear" />
				<div id="osViews" class="col2" style="height:400px;"></div>
				<div id="browserViews" class="col2" style="height:400px;"></div>
				<br class="clear" />
				<br class="clear" />				
				<div id="viewsOverTime" style="width:100%; height:400px;"></div>				
				<br class="clear" />
				<div id="viewsByDay" style="width:100%; height:400px;"></div>	
				<br class="clear" />
				<div id="viewsByHour" style="width:100%; height:400px;"></div>
				<br class="clear" />
				<script>
					function createCharts() {
						$('#osViews').highcharts({
							chart: {
								animation: false,
								backgroundColor: 'rgba(230,226,209,.75)',
								plotBackgroundColor: null,
								plotBorderWidth: 0,
								plotShadow: false
							},
							title: {
								text: 'Views By OS'
							},
							tooltip: {
								pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
							},
							plotOptions: {
								pie: {
									//allowPointSelect: true,
									dataLabels: {
										enabled: true,
										style: {
											fontWeight: 'bold !important',
											color: 'black',
											textShadow: '0px 1px 2px white'
										}
									},
									center: ['50%', '50%']
								}
							},
							series: [{
								type: 'pie',
								name: 'OS Share',
								innerSize: '50%',
								data:[<?php 
									$i=0;
									foreach($osviews as $thisOS => $item) {
										if($thisOS=='0' || $thisOS==''){ continue; }
										$count=0;
										foreach($item as $viewTime => $viewUser) {
											$count++;
										}
										if($i!==0){
											echo ", ";
										}
										echo "['$thisOS',";
									
										echo " $count]";
										$i++;
									}
								?>]
							}]
						});						
						
						
						var colors=Highcharts.getOptions().colors,
						brightness;
						$('#browserViews').highcharts({
							chart: {
								animation: false,
								backgroundColor: 'rgba(230,226,209,.75)',
								type: 'pie'
							},
							title: {
								text: 'Views By Browser'
							},
							plotOptions: {
								pie: {
									shadow: false,
									center: ['50%', '50%']
								}
							},
							tooltip: {
								pointFormat: '<b>{point.percentage:.1f}%</b>'
							},
							series: [{
								name: 'Browsers',
								data: [<?php 
									$i=0;
									foreach($browserviews as $thisbrowser => $item) {
										if($thisbrowser=='0' || $thisbrowser==''){ continue; }
										$versions = array_keys($item);
										$count=0;
										$vercount = '0';
										foreach($versions as $theversion) {
											if($theversion==''){ continue; }
											$veramount = count($item[$theversion]);
											if($veramount===0) { continue; }
											$vercount += $veramount;
											$count++;
										}
										if($veramount>0 && $count>0){
											if($i!==0){
												echo ", ";
											}
											echo "{'name': '$thisbrowser',";
											echo "y: $vercount,";
											echo "'color':colors[$i]}";
											$i++;
										}
									}
								?>],
								size: '60%',
								dataLabels: {
									formatter: function () {
										return this.y > 2 ? this.point.name : null;
									},
									color: '#ffffff',
									distance: -30
								}
							}, {
								name: 'Versions',
								data: [<?php 
									$i=0;
									$browservlist='';
									foreach($browserviews as $thisbrowser => $item) {
										if($thisbrowser=='0' || $thisbrowser==''){ continue; }
										if($i!==0){
											echo ", ";
										}
										$versions = array_keys($item);
										$count=0;
										$includecomma="";
										foreach($versions as $theversion) {
											if($theversion==''){ continue; }
											if($count!==0){
												$includecomma = ", ";
											}
											$veramount = count($item[$theversion]);
											if($veramount===0) { continue; }
											$brightness=.2-($count/count($versions))/5;
											echo "$includecomma{'name': '$thisbrowser $theversion',";
											echo "y: $veramount,";
											echo "'color':Highcharts.Color(colors[$i]).brighten($brightness).get()}";
											$count++;
										}
										if($veramount>0 && $count>0){
											$i++;
										}
									}
								?>],
								size: '80%',
								innerSize: '60%',
								tooltip: {
									pointFormat: '<b>{point.percentage:.1f}%</b>'
								}
							}]
						});


						$('#viewsOverTime').highcharts({
							title: {
								text: 'Views Over Time'
							},
							chart: {
								animation: false,
								backgroundColor: 'rgba(230,226,209,.75)'
							},							
							xAxis: {
								categories: [
									<?php 
									$i=0;
									foreach($viewsOverTime as $viewDate => $item) {
										if($viewDate=='0'){ continue; }
										if($i!==0){
											echo ",'";
										} else {
											echo "'";
										}
										print_r($viewDate);
										echo "'";
										$i++;
									}?>
								]
							},
							yAxis: {
								min: 0,
								allowDecimals: false,
								title: {
									text: 'Views'
								}
							},
							legend: {
								enabled: true
							},
							series: [{
								type: 'column',
								name: 'Total Users',
								data: [<?php 
									$i=0;
									foreach($viewsOverTime as $viewDate => $item) {
										if($viewDate=='0'){ continue; }
										$count=0;
										foreach($item as $viewTime => $viewUser) {
											//if($viewUser!='0'){ continue; }
											$count++;
										}
										if($i!==0){
											echo ", ";
										}									
										echo $count;
										$i++;
									}
								?>]								
							}, {
								type: 'spline',
								name: 'Anonymous Users',
								data: [<?php 
									$i=0;
									foreach($viewsOverTime as $viewDate => $item) {
										if($viewDate=='0'){ continue; }
										$count=0;
										foreach($item as $viewTime => $viewUser) {
											if($viewUser!='0'){ continue; }
											$count++;
										}
										if($i!==0){
											echo ", ";
										}									
										echo $count;
										$i++;
									}
								?>]							
							}, {
								type: 'spline',
								name: 'Authenticated Users',
								data: [<?php 
									$i=0;
									foreach($viewsOverTime as $viewDate => $item) {
										if($viewDate=='0'){ continue; }
										$count=0;
										foreach($item as $viewTime => $viewUser) {
											if($viewUser=='0'){ continue; }
											$count++;
										}
										if($i!==0){
											echo ", ";
										}									
										echo $count;
										$i++;
									}
								?>]
							}]
						});					
						
						$('#viewsByDay').highcharts({
							chart: {
								animation: false,
								backgroundColor: 'rgba(230,226,209,.75)',
								type: 'column'
							},
							title: {
								text: 'Views By Day of Week'
							},
							xAxis: {
								type: 'category',
								labels: {
									rotation: -45,
									style: {
										fontSize: '13px',
										fontFamily: 'Verdana, sans-serif'
									}
								}
							},
							yAxis: {
								min: 0,
								allowDecimals: false,
								title: {
									text: 'Views'
								}
							},
							legend: {
								enabled: false
							},
							series: [{
								name: 'Views',
								data: [
									['Sunday', <?php echo $sundayViews;?>],
									['Monday', <?php echo $mondayViews;?>],
									['Tuesday', <?php echo $tuesdayViews;?>],
									['Wednesday', <?php echo $wednesdayViews;?>],
									['Thursday', <?php echo $thursdayViews;?>],
									['Friday', <?php echo $fridayViews;?>],
									['Saturday', <?php echo $saturdayViews;?>]
								]
							}]
						});
						
						$('#viewsByHour').highcharts({
							chart: {
								animation: false,
								backgroundColor: 'rgba(230,226,209,.75)',
								type: 'column'
							},
							title: {
								text: 'Views By Hour of Day'
							},
							xAxis: {
								type: 'category',
								labels: {
									rotation: -45,
									style: {
										fontSize: '13px',
										fontFamily: 'Verdana, sans-serif'
									}
								}
							},
							yAxis: {
								min: 0,
								allowDecimals: false,
								title: {
									text: 'Views'
								}
							},
							legend: {
								enabled: false
							},
							series: [{
								name: 'Views',
								data: [
									['12am-1am', <?php echo $hour00views;?>],
									['1am-2am', <?php echo $hour01views;?>],
									['2am-3am', <?php echo $hour02views;?>],
									['3am-4am', <?php echo $hour03views;?>],
									['4am-5am', <?php echo $hour04views;?>],
									['5am-6am', <?php echo $hour05views;?>],
									['6am-7am', <?php echo $hour06views;?>],
									['7am-8am', <?php echo $hour07views;?>],
									['8am-9am', <?php echo $hour08views;?>],
									['9am-10am', <?php echo $hour09views;?>],
									['10am-11am', <?php echo $hour10views;?>],
									['11am-12pm', <?php echo $hour11views;?>],
									['12pm-1pm', <?php echo $hour12views;?>],
									['1pm-2pm', <?php echo $hour13views;?>],
									['2pm-3pm', <?php echo $hour14views;?>],
									['3pm-4pm', <?php echo $hour15views;?>],
									['4pm-5pm', <?php echo $hour16views;?>],
									['5pm-6pm', <?php echo $hour17views;?>],
									['6pm-7pm', <?php echo $hour18views;?>],
									['7pm-8pm', <?php echo $hour19views;?>],
									['8pm-9pm', <?php echo $hour20views;?>],
									['9pm-10pm', <?php echo $hour21views;?>],
									['10pm-11pm', <?php echo $hour22views;?>],
									['11pm-11:59pm', <?php echo $hour23views;?>]
								]
							}]
						});

						
						<?php if(isset($mediaplaybacktime['totalTime'])) {	?>						
						$('#playBackHeatMap').highcharts({

						   chart: {
								type: 'heatmap',
								marginTop: 40,
								marginBottom: 80,
								plotBorderWidth: 1,
								height: 220
							},

							title: {
								text: 'Playback Timeline Heatmap (Minute:Second)'
							},

							xAxis: {
								allowDecimals: false,
								startOnTick: false,
								title: "Minutes",
								min: 10,
								max: <?php echo $mediaplaybacktime['totalTime']*10; ?>,
								labels: {
									formatter: function () {
										hours = Math.floor(this.value / 3600);
										timeleft = this.value - hours * 3600;
										minutes = Math.floor(timeleft / 60);
										seconds = timeleft - minutes * 60;
										if(seconds=='0') { 
											seconds = '00'; 
										}else if(seconds<'10') { seconds = "0" + seconds; }
										if(minutes<'10') { minutes = "0" + minutes; }
										if(hours>0){
											printtime = hours + ":" + minutes + ":" + seconds;
										} else {
											printtime = minutes + ":" + seconds;
										}
										return printtime;
									}
								}
							},

							yAxis: {
								title: null,
								height: 100,
								labels: { enabled: false }
							},

							colorAxis: {
								min: 0,
								reversed: false,
								startOnTick: false,
								stops: [
									[0, '#FFFFFF'],
									[0.2, '#FCE294'],
									[0.8, '#00573C'],
									[1, '#00573C']
								],
							},

							legend: {
								reversed: false,
								align: 'right',
								layout: 'vertical',
								margin: 0,
								verticalAlign: 'top',
								y: 22,
								symbolHeight: 100
							},

							tooltip: {
								enabled: false,
								formatter: function () {
									return '<b>' + this.point.value + '</b> total views for second: <b>' + this.point.y + '</b>';
								}
							},
							
							series: [{
								name: 'Views Per Second',
								borderWidth: 0,
								data: [<?php echo $playBackHeatMapArray;?>],
								dataLabels: {
									enabled: false
								},
								turboThreshold: 0,
								colsize: 10,
								states: { hover: false }
							}]
						});





					<?php /*
						$('#playBackHeatMap').highcharts({

							chart: {
								marginTop: 40,
								marginBottom: 80,
								//plotBorderWidth: 1,
								//height: 220
							},

							title: {
								text: 'Playback Timeline Heatmap (Minute:Second)'
							},

							xAxis: [{
								allowDecimals: false,
								//startOnTick: false,
								endOnTick: false,
								title: "Minutes",
								min: 10,
								//max: <?php echo $mediaplaybacktime['totalTime']*10/60; ?>,
								//ceiling: <?php echo $mediaplaybacktime['totalTime']*10/60; ?>,
								tickInterval: 10,
								minPadding: 0,
								maxPadding: 0,
								labels: {
									formatter: function () {
										hours = Math.floor(this.value / 3600);
										timeleft = this.value - hours * 3600;
										minutes = Math.floor(timeleft / 60);
										seconds = timeleft - minutes * 60;
										if(seconds=='0') { 
											seconds = '00'; 
										}else if(seconds<'10') { seconds = "0" + seconds; }
										if(minutes<'10') { minutes = "0" + minutes; }
										if(hours>0){
											printtime = hours + ":" + minutes + ":" + seconds;
										} else {
											printtime = minutes + ":" + seconds;
										}
										return printtime;
									}
								}
							},{
								//startOnTick: false,
								//linkedTo: 0,
								allowDecimals: false,
								min: 1,
								//max: <?php echo $mediaplaybacktime['totalTime']*10; ?>,
								ceiling: <?php echo $mediaplaybacktime['totalTime']; ?>,
								tickInterval: 1,
								minPadding: 0,
								maxPadding: 0,
								tickLength: 0,
								offset: 0,
								endOnTick: false,
								labels: { enabled: false }
							}],

							yAxis: [{
								title: null,
								height: 100,
								labels: { enabled: false }
							},{
								title: null,
								labels: { enabled: false }
							}],

							colorAxis: {
								min: 0,
								reversed: false,
								startOnTick: false,
								stops: [
									[0, '#FFFFFF'],
									[0.2, '#FCE294'],
									[0.8, '#00573C'],
									[1, '#00573C']

						//			[0, '#FFFFFF'],
						//			[0.4, '#F9DC57'],
						//			[0.8, '#c4463a'],
						//			[1, '#c4463a']
								],
							},

							legend: {
								reversed: false,
								align: 'right',
								layout: 'vertical',
								margin: 0,
								verticalAlign: 'top',
								y: 22,
								symbolHeight: 100
							},

							tooltip: {
								shared: true,
								enabled: false,
								formatter: function () {
									return '<b>' + this.point.value + '</b> total views for second: <b>' + this.point.y + '</b>';
								}
							},

							series: [{
								name: 'Viewing %',
								type: 'spline',
								color: 'black',
								states: { hover: { enabled:false } },
								yAxis: 1,
								xAxis: 1,
								borderWidth: 0,
								showInLegend: false,
								zIndex: 1,
								data: [<?php echo $playBackHeatMapSpline;?>]
							},{
								type: 'heatmap',
								name: 'Views Per Second',
								borderWidth: 0,
								data: [<?php echo $playBackHeatMapArray;?>],
								chart: {
									marginTop: 0,
									marginBottom: 0,
								},
								dataLabels: {
									enabled: false
								},
								turboThreshold: 0,
								linkedTo: ':previous',
								colsize: 10,
								states: { hover: false }							
							}]
						}); 
						*/ ?>
						<?php }	?>	
					};
					createCharts();
				</script>
				<?php } ?>
			</div>
			<?php } ?>
			<br class="clear" /><br />
		</div>
		<?php if(count($stats)>0) {?>
		</div>
		<?php } ?>
		<div data-html2canvas-ignore="true">
			<?php include "includeFooter.php";?>
		</div>
	</body>
	<div id="pdfGenNotice" style="width:100%;height:100%;top:0;left:0;position:fixed;color:#eeeeee;background:rgba(20,20,20,.85);z-index:1000;display:none;" data-html2canvas-ignore="true">
		<br><br>
		<center><h1 style="font-weight:bold;font-size:25pt;">Generating PDF <img style="height:30px;" src="./img/loading.gif"/></h1></center>
	</div>
	<script>
		(function($){
			$.fn.extend({
				contains: function(str) {
					return this.filter(function(){
						return $(this).html().indexOf(str) != -1;
					});
				}
			});
		})(jQuery);
		function downloadPDF(){
			$('#pdfGenNotice').show();
			var width = 1350;
			var height = window.innerHeight;
			var bgcolor = $('html').css("background");
			$('link[href="./css/responsive.css"]')[0].disabled=true;
			$("#content").css('background-image', 'none');
			$("#content").css('box-shadow', 'none');
			$("#content").css('-webkit-box-shadow', 'none');
			$("html").css('background', '#fff');
			$("body").css('background', '#fff');
			$("html").css('width', width+'px');
			$("body").css('width', width+'px');
			createCharts();
			$('text').contains('Highcharts.com').remove();
			setTimeout(function() { PassPDF(width,height,bgcolor);},1100);
		}
		function PassPDF(width,height,bgcolor){
			canvg();
			html2canvas($('#pdfcontent')[0], {
				logging:true,
				imageTimeout:500,
				width:width,
				background:'#fff',
				onrendered: function(canvas) {
					document.body.appendChild(canvas);
					$('canvas').css('width', width+'px');
					var pdf = new jsPDF('p','mm','legal' );
					var options = { pagesplit: true }; // not currently implemented				
					pdf.addHTML(canvas, 0, 0, function() {
						pdf.save("Viewing Stats for <?php echo $mediainfo['filetitle']; ?> - <?php echo date("F d, Y g:ia",$timenow); ?>.pdf");
						$("html").css('width', '100%');
						$("body").css('width', '100%');
						$('link[href="./css/responsive.css"]')[0].disabled=false;
						$("html").css('background', bgcolor);
						$("body").css('background', bgcolor);
						$("#content").css('background-image', '');
						// maybe remove old canvas instead of hide
						$('canvas').hide();
						$('img.submit').hide();
						$('input.submit').show();
						$('#pdfGenNotice').hide();
						createCharts();
						//location.reload();
					});
				}
			});
		}
	</script>
</html>