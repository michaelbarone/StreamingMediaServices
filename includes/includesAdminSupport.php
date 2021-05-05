<script>
	function ChooseUser(){
		document.getElementById("chooseuser").submit();
	}
</script>
<div class="videolist currentlyprocessing">
	<div class="videolist header">
		<span>Support Selectors:</span>
	</div>
	<span class="left">Select by User:</span>
	<form id="chooseuser" method="post" class="left">
	<?php
		$userarray = $Streaming->ReturnUsers();
		echo "<select name='selecteduserid' onchange='ChooseUser()'>";
		echo "<option disabled selected='selected'>Choose User by Lastname:</option>";
		foreach ($userarray as $user) {
			echo "<option value='" . $user['userid'] . "'>" . $user['namel'] . ", " . $user['namef'] . " - " . $user['userid'] . "</option>";
		}
		echo "</select>";
		echo "<select name='selecteduserid' onchange='ChooseUser()'>";
		echo "<option disabled selected='selected'>Choose User by userid:</option>";
		$userarrays = $userarray;
		ksort($userarrays);
		foreach ($userarrays as $user) {
			echo "<option value='" . $user['userid'] . "'>" . $user['userid'] . " - " . $user['namel'] . ", " . $user['namef'] . "</option>";
		}
		echo "</select>";					
	?>
	</form><img src="./img/refresh.png" onclick="RefreshUserTable();" id="RefreshUsersTable" class="left" style="height:25px;margin:0 10px;cursor:pointer;" title="Update the User List" />
	<img class="btn-question" src="./img/question.gif" about="userselection" />
	<br class="clear"/><br />
	<span class="left">Select by File Path Hash:</span>
	<form id="choosefilehash" method="post" class="left">
		<input type="text" name="selectedfilehash" size="40"/>
		<input type="submit" value="Submit" class="btn btn-success btn-sml">
	</form>
	<br class="clear"/><br />
	<?php
		if(isset($selecteduserid)) { ?>
			<div class="videolist">
				<h2><?php echo $userarray[$selecteduserid]['namef'] . " " . $userarray[$selecteduserid]['namel'] . " - [" . $selecteduserid . "]";?> is currently selected.</h2>
				<p>Account Created on:  <?php echo date("F d, Y h:i:s a",$userarray[$selecteduserid]['created']); ?></p>
				<p>Agreed to Terms on:  <?php echo date("F d, Y h:i:s a",$userarray[$selecteduserid]['agreeToTerms']); ?></p>
			</div>						
		<?php
		}
	?>
</div>
<br class="clear"><br>
<?php
	if(isset($selectedfile)) { ?>
		<div class="videolist currentlyprocessing">
			<div class="videolist header">
				<span>Selected Media File: <img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
			</div>
			<div class="videolist">
				<?php
					$thisurl = $Streaming->getUrl();
					$h = $selectedfilehash;
					$thislink = $thisurl . '/PlayMedia.php?media=' . $h;
					$thisembedlink = $thisurl . '/EmbedMedia.php?media=' . $h;
					if(!isset($selectedfile['fileposter']) || $selectedfile['fileposter'] == '') { $selectedfile['fileposter'] = "./img/defaultposter.png"; }
				?>
						<div class='videoentry clear'>
							<?php if($selectedfile['disabled']==="1") { ?>
								<span class="disabled">This Media is currently Disabled.  Edit this Media to re-enable access.</span>
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
								<p class="clear">Created: <?php echo date("F d, Y h:i a",$selectedfile['created']); ?></p>
							</div>
							<div class="left col2">
								<a href="<?php echo $thislink;?>" class="posterlink" target="_blank"><img src="<?php echo $selectedfile['fileposter']; ?>" /></a>
								<h2><?php echo $selectedfile['filetitle']; ?></h2>
								<p><?php echo $selectedfile['fileinfo']; ?></p>
							</div>
							<p class="clear">Require Saclink Login to view?: <span class="bold"><?php echo ($selectedfile['reqLogin']==="1") ? "YES" : "NO"; ?></span></p><br>
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
									<textarea id='embed<?php echo $entrycount;?>' style='width:75%; vertical-align:middle;' readonly><iframe width='560' height='450' src='<?php echo $thisembedlink;?>' frameborder='0' allowfullscreen=''></iframe></textarea>
								</p>
							</div>
						</div>
			</div>
		</div>
<?php }
if(isset($selecteduserid)) {

	$processthese = $Streaming->NeedsProcessing($selecteduserid);
	if(!empty($processthese)) {
		include('includeNeedsProcessing.php');
	}
	?>
	<br class="clear"><br>
	<?php
		$currentlyprocessing = $Streaming->CurrentlyProcessing($selecteduserid);
		if(!empty($currentlyprocessing)) {
	?>
		<div class="videolist currentlyprocessing">
			<div class="videolist header">
				<span>Media Being Processed: <img class="btn-question" title="Information Popup" src="./img/question.gif" about="processing" /><img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
			</div>
			<div class="videolist">
				<?php
					$entrycount=0;
					$thisurl = $Streaming->getUrl();
					foreach($currentlyprocessing as $entry) {
						$entrycount++;
						$h = $entry['filePathHash'];
						$thislink = $thisurl . '/PlayMedia.php?media=' . $h;
						$thisembedlink = $thisurl . '/EmbedMedia.php?media=' . $h;
						if(!isset($entry['filePoster']) || $entry['filePoster'] == '') { $entry['filePoster'] = "./img/defaultposter.png"; }
						?>
						<div class='videoentry'>
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
									<textarea id='embed<?php echo $entrycount;?>' style='width:75%; vertical-align:middle;' readonly><iframe width='560' height='450' src='<?php echo $thisembedlink;?>' frameborder='0' allowfullscreen=''></iframe></textarea>
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
			$ListEntries = $Streaming->ListEntries($selecteduserid,$sortby);
			if(!empty($ListEntries)) {
		?>
		<div class="videolist header">
			<span>Available Streaming Media: <img class="btn-question" title="Information Popup" src="./img/question.gif" about="available" /><img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
			<br />
		</div>
		<div class="videolist">
			<?php
				$thisurl = $Streaming->getUrl();
				$ListEntriesArray = '';
				foreach($ListEntries as $entry) {
					if(!isset($entry['folder']) || $entry['folder'] == '') { $entry['folder'] = "Unsorted"; }
					$ListEntriesArray[$entry['folder']][$entry['indexID']] = $entry; 
				}
				echo "<ul class='accordian LibraryFolders'>";
				$thisurl = $Streaming->getUrl();
				$count=1;
				$howmany=0;
				$entrycount=0;
				foreach($ListEntriesArray as $foldername => $unitid) {
					$howmany=count($unitid);
					if($count===1) { echo "<li class='has-sub open'>"; } else { echo "<li class='has-sub'>"; }
					echo "<a href='#'><img class='left' src='./img/folder.png' /><span>$foldername ($howmany media files)</span></a>";
					if($count===1) { echo "<ul style='display:block;'>"; } else { echo "<ul>"; }
					foreach($unitid as $entry) {
						$h = $entry['filePathHash'];
						$thislink = $thisurl . '/PlayMedia.php?media=' . $h;
						$thisembedlink = $thisurl . '/EmbedMedia.php?media=' . $h;
						if(!isset($entry['filePoster']) || $entry['filePoster'] == '') { $entry['filePoster'] = "./img/defaultposter.png"; }
						?>
						<div class='videoentry clear'>
							<?php if($entry['disabled']==="1") { ?>
								<span class="disabled">This Media is currently Disabled.  Edit this Media to re-enable access.</span>
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
									<textarea id='embed<?php echo $entrycount;?>' style='width:75%; vertical-align:middle;' readonly><iframe width='560' height='450' src='<?php echo $thisembedlink;?>' frameborder='0' allowfullscreen=''></iframe></textarea>
								</p>
							</div>
						</div>
						<?php
						$entrycount++;
					}
					$count++;
					echo "</ul></li>";	
				}
			echo "</ul>";	
			?>					
		</div>
		<?php } else { ?>
			<div class="videolist">
				<h2><?php echo $userarray[$selecteduserid]['namef'] . " " . $userarray[$selecteduserid]['namel'] . " - [" . $selecteduserid . "]";?> has no available streaming media.</h2>
			</div>
		<?php }
} else {
	$recententries = $Streaming->ListRecentEntries();
	if(!empty($recententries)) { ?>
	<div class="videolist currentlyprocessing">
		<div class="videolist header">
			<span>Recent Entries:<img class="btn-refresh" title="Refresh this Page" src="./img/refresh.png" /></span>
		</div>
		<div class="videolist">
			<?php
				$entrycount=0;
				$thisurl = $Streaming->getUrl();
				foreach($recententries as $entry) {
					$entrycount++;
					$h = $entry['filePathHash'];
					$thislink = $thisurl . '/PlayMedia.php?media=' . $h;
					$thisembedlink = $thisurl . '/EmbedMedia.php?media=' . $h;
					if(!isset($entry['filePoster']) || $entry['filePoster'] == '') { $entry['filePoster'] = "./img/defaultposter.png"; }
					?>
					<div class='videoentry'>
						<span style="left"><?php echo $userarray[$entry['userid']]['namef'] ." ". $userarray[$entry['userid']]['namel'] ." - ". $entry['userid'];?></span>
						<div class="topbuttons">
							<form class="right" action="MediaDetails.php" method="post">
								<input type="hidden" value="<?php echo $h;?>" name="h" />
								<input type="hidden" value="1" name="edit" />
								<input type="submit" class="btn btn-warning" value="Edit" />
							</form>
							<?php if($entry['postprocessed']>'0'){ ?>
							<form class="" action="Stats.php" method="post" target="_blank">
								<input type="hidden" value="<?php echo $h;?>" name="h" />
								<input type="submit" class="btn btn-info" value="Stats" />
							</form>
							<a href="javascript:void(0)" id="move<?php echo $h;?>" class="btn btn-primary" onclick="return GetMediaLibraryFolders('<?php echo $userid;?>','<?php echo $h;?>');"><img class='left' style="height:20px;margin-right:5px;" src='./img/folder.png' />Move to Folder</a>
							<p class="clear">Created: <?php echo date("F d, Y h:i a",$entry['created']); ?></p>	
							<p class="clear">Done Processing: <?php echo date("F d, Y h:i a",$entry['postprocessed']); ?></p>	
							<?php } else { ?>
							<a href="javascript:void(0)" id="move<?php echo $h;?>" class="btn btn-primary right" onclick="return GetMediaLibraryFolders('<?php echo $userid;?>','<?php echo $h;?>');"><img class='left' style="height:20px;margin-right:5px;" src='./img/folder.png' />Move to Folder</a>
							<p class="clear">Enqueued: <?php echo date("F d, Y h:i a",$entry['created']); ?></p>
							<p class="bold clear">This file is not yet ready to stream.</p>
							<?php } ?>
							<p class="clear">Uploaded File Size: <?php echo round($entry['origSize']/1024/1024,2) . "MB"; ?></p>
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
								<textarea id='embed<?php echo $entrycount;?>' style='width:75%; vertical-align:middle;' readonly><iframe width='560' height='450' src='<?php echo $thisembedlink;?>' frameborder='0' allowfullscreen=''></iframe></textarea>
							</p>
						</div>										
					</div>
					<?php
				}
			?>
		</div>
	</div>
<?php }
} ?>