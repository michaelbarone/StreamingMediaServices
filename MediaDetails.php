<?php
require_once "init.php";
require "CASauth.php";
require "checkAuth.php";
$failed = 0;
$inputsuccess = 0;
$edit=0;
$playlist=0;
$userlevel = $Streaming->UserLevel($userid);
if(isset($_POST) && !empty($_POST)) {
	if(isset($_POST['filename'])) {
		$uploadfilename = $_POST['filename'];
		$tempfiletitle = preg_replace('/\\.[^.\\s]{3,4}$/', '', "$uploadfilename");
		/*
		$videoquality = $Streaming->CheckQualityFile($userid,$uploadfilename);
		print_r($videoquality);
		// need to see why this is not returning info.
		//$videoquality = $Streaming->CheckQuality($userid,$uploadfilename);
		//print_r($videoquality);
		*/

	}elseif(isset($_POST['edit']) && $_POST['edit'] == 1 && isset($_POST['h'])) {
		$edit=1;
		$editEntry = $Streaming->ShowEntrySupport($_POST['h']);
		if($userid !== $editEntry['userid'] && $userlevel!=="admin") {
			$failed = 5;
			$log->LogWarn("UNATHORIZED ACCESS: user:$userid tried to edit media:" . $_POST['h'] . " without access.");
		}
	}elseif(isset($_POST['details']) && $_POST['details'] == 1) {
		$userid = $_POST['userid'];
		$filepath = $_POST['filepath'];
		$uploadfilename = $_POST['filepath'];
		$filetitle = $_POST['filetitle'];
		$fileinfo = $_POST['fileinfo'];
		$fileposter = $_POST['fileposter'];
		$reqLogin = (isset($_POST['reqLogin']) && $_POST['reqLogin'] === "1") ? 1 : 0;
		$disabled = (isset($_POST['disabled']) && $_POST['disabled'] === "1") ? 1 : 0;
		$edit = (isset($_POST['edit']) && $_POST['edit'] === "1") ? 1 : 0;
		if($edit===1){
			$inputthis = $Streaming->EditEntry("$userid","$filepath","$filetitle","$fileinfo","$fileposter","$reqLogin","$disabled");
		} else {
			$processthese = $Streaming->NeedsProcessing($userid);
			if(empty($processthese) || !in_array($uploadfilename,$processthese)) {
				$failed = 1;
				break;
			}
			$inputthis = $Streaming->AddEntry("$userid","$filepath","$filetitle","$fileinfo","$fileposter","$reqLogin");
		}
		
		if(strpos($inputthis,'failed') !== false) {
			$failed = 1;
			break;
		}
		$inputsuccess = 1;
		header('Location: MediaLibrary.php');
	}elseif(isset($_POST['type']) && $_POST['type'] == 'playlist' && isset($_POST['h'])) {
		$playlist=1;
		$playlistEntry = $Streaming->GetPlaylist($_POST['h']);
		if($userid !== $playlistEntry[0]['userid'] && $userlevel!=="admin") {
			$failed = 5;
			$log->LogWarn("UNATHORIZED ACCESS: user:$userid tried to edit playlist:" . $_POST['h'] . " without access.");
		}
	}elseif(isset($_POST['playlist']) && $_POST['playlist'] == 'save') {
		$userid = $_POST['userid'];
		$playlistName = $_POST['playlistName'];
		$playlistHash = $_POST['h'];
		$mediaHashes = $_POST['mediaHashes'];
		$inputthis = $Streaming->EditPlaylist("$userid","$playlistName","$playlistHash","$mediaHashes");
		if(strpos($inputthis,'failed') !== false) {
			$failed = 1;
			break;
		}
		$inputsuccess = 2;
		header('Location: MediaPlaylists.php');
	} else {
		$failed = 1;
	}
} else {
	$failed = 1;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>CSUS Streaming - <?php echo $uploadfilename;?></title>
		<?php include "includeHeader.php";?>
		<script src="./js/jquery.ddslick.min.js"></script>
		<script>
			$(document).ready(function () {
				$('.imgupload').ajaxupload({
					url:'uploadimg.php',
					dropArea:'#imguploadfield',
					autoStart: true,
					maxFiles: 1,
					finish:function(files, filesObj){
						$('.loadingimg').show();
						$('.dd-selected-value').attr('value', './upload/<?php echo $userid;?>/poster/' + files);
					}
				});
				$('#posterselect').ddslick({
					selectText: "Select a Poster Frame for this video",
					onSelected: function(selectedData){
						if(selectedData.selectedData.value=="upload") {
							$('#imguploadfield').show();
						} else {
							$('#imguploadfield').hide();
							$('.imgupload').ajaxupload('clear');
							$('.loadingimg').hide();
						}
					}
				});
				$('.dd-selected-value').attr('name', 'fileposter');
			});			
		</script>
		<style>
			#filetitle {
				width: 25%;
				min-width: 250px;
			}
			.dd-option-image, .dd-selected-image {
				max-width: 100px;
			}
			.dd-option-selected {
				display:none;
			}
			.optional {
				margin: 5px;
			}
			.dd-option-text {
				cursor:pointer;
			}
			#imguploadfield {
				min-height:70px;
			}
			.ax-preview {
				width:250px;
			}
		</style>
		<div id="content" class="content">
			<?php
				if($failed == 10) {
			?>
			<div id="videotitle">
				<button onclick="goBack()" class="right btn btn-warning">Go Back</button>
				<h3>Low input video quality</h3>
			</div>
			<?php
				} elseif($failed == 20) {
			?>
			<div id="videotitle">
				<button onclick="goBack()" class="right btn btn-warning">Go Back</button>
				<h3>High input quality, needs custom processing</h3>
			</div>
			<?php
				} elseif($failed == 5) {
			?>
			<div id="videotitle">
				<button onclick="goBack()" class="right btn btn-warning">Go Back</button>
				<h3>You do not have access to edit this media.  This attempt has been logged.</h3>
			</div>
			<?php
				} elseif($failed > 0) {
			?>
			<div id="videotitle">
				<button onclick="goBack()" class="right btn btn-warning">Go Back</button>
				<h3>Error encountered.</h3>
			</div>
			<?php
				} elseif($inputsuccess == 1) {
			?>
			<div id="videotitle">
				<h3>File Details have been saved, this page will refresh automatically.</h3><img src="./img/loading.gif" />
				<script>setTimeout(function () { window.location.href = "MediaLibrary.php"; }, 1000);</script>
			</div>
			<?php
				} elseif($inputsuccess == 2) {
			?>
			<div id="videotitle">
				<h3>Playlist has been updated, this page will refresh automatically.</h3><img src="./img/loading.gif" />
				<script>setTimeout(function () { window.location.href = "MediaPlaylists.php"; }, 1000);</script>
			</div>			
			<?php
				} elseif($edit > 0) {
			?>
			<div id="videotitle">
				<span class="submit-holder right"><label class="btn btn-success submit" for="submit-form">SAVE</label><img class="submit hidden" src="./img/loading.gif" /></span><button onclick="goBack()" class="right btn btn-warning">Go Back</button>
				<h3>Edit File Details for: <?php echo $editEntry['filetitle'];?></h3>
			</div>
			
			<form method="post" action="">
				<input type="hidden" name="userid" value="<?php echo $userid;?>">
				<input type="hidden" name="filepath" value="<?php echo $editEntry['filepath'];?>">
				<input type="hidden" name="details" value="1">
				<input type="hidden" name="edit" value="1">
				
				<div class="videoentry">
					<p class='inputlabelreq'>&nbsp;Video Title (Displayed to viewers): <input type="text" id="filetitle" name="filetitle" value="<?php echo $editEntry['filetitle'];?>" required="required"></p>
					<br>
					<p class='inputlabelreq'><input type="checkbox" id="terms" name="terms" value="terms" checked="checked" required="required"> I have either created this media file or I have taken appropriate steps to ensure the use of this media file is not against any law.  I take full responsibility for any copyright material I upload to this system as agreed to in the <a href="Terms.php" target="_blank">Terms of Service Agreement</a>.</p>
				</div>
				<br>Optional:<br>
				
				<div class="left optional">
					<p>Poster Frame:</p>
					<select id="posterselect" name="fileposter">
						<?php if(isset($editEntry['fileposter']) && $editEntry['fileposter']!='') { ?>
							<option value="<?php echo $editEntry['fileposter'];?>"
									data-imagesrc="<?php echo $editEntry['fileposter'];?>">Current Poster
							</option>									
						<?php } ?>
						<option value="./img/defaultposter.png"
								data-imagesrc="./img/defaultposter.png">Poster 1
						</option>
						<option value="./img/defaultposter2.png"
								data-imagesrc="./img/defaultposter2.png">Poster 2
						</option>
						<option value="./img/defaultposter3.png"
								data-imagesrc="./img/defaultposter3.png">Poster 3
						</option>
						<option value="upload">Upload Custom Poster
						</option>
					</select>
					<fieldset id="imguploadfield" class="hidden">
						<legend>Drag & Drop a Poster Frame Image (.png or .jpg)<br>Recommended Dimensions: 1280 x 720 pixels</legend>
						<div class="imgupload" > </div>
						<span class="loadingimg" class="hidden" style="display:block;width:350px;">When ready, select the SAVE button.<br>To upload a different poster, click the remove button then upload a new poster or pick one of the default options.</span>
						<br>
					</fieldset>
				</div>				
				<div class="left optional">
					<p>Video Description:</p>
					<textarea id="fileinfo" name="fileinfo" cols="50" rows="8"><?php echo $editEntry['fileinfo'];?></textarea>
				</div>
				<br class="clear"><br>				
				<p><input type="checkbox" name="reqLogin" value="1" <?php echo ($editEntry['reqLogin']==="1") ? "checked='checked'":"";?>> Require Saclink Login for viewing</p>
				<p><input type="checkbox" name="disabled" value="1" <?php echo ($editEntry['disabled']==="1") ? "checked='checked'":"";?>> Disable access to this media.</p>
				<br>
				<input type="submit" id="submit-form" class="btn btn-success submit" value="SAVE" onclick="validateFileForms()"><img class="submit hidden" src="./img/loading.gif" />
			</form>
			<?php
				} elseif($playlist > 0) {
			?>
			<div id="videotitle">
				<span class="submit-holder right"><label class="btn btn-success submit" for="submit-form">SAVE</label><img class="submit hidden" src="./img/loading.gif" /></span><button onclick="goBack()" class="right btn btn-warning">Go Back</button>
				<h3>Edit Playlist: <img class="btn-question" title="Information Popup" src="./img/question.gif" about="editplaylist" /></h3>
			</div>
			
			<form method="post" action="">
				<input type="hidden" name="userid" value="<?php echo $userid;?>">
				<input type="hidden" name="h" value="<?php echo $playlistEntry[0]['playlistHash'];?>">
				<input type="hidden" name="playlist" value="save">
				<input type="hidden" id="inputMediaHashes" name="mediaHashes" value="<?php echo $playlistEntry[0]['mediaHashes'];?>">
				
				<p class='inputlabelreq'>Playlist Title:
				<input type="text" id="playlistName" name="playlistName" value="<?php echo $playlistEntry[0]['playlistName'];?>" required="required">
				</p>
				<br>
				<script>
					$(function() {
					$( "#libraryentries, #Playlistentries" ).sortable({
						receive: function( event, ui ) {
							var playlistHashes = '';
							$('#Playlistentries').each(function(){
								$(this).find('li').each(function(){
									playlistHashes+=$(this).attr('value') + ",";
								});
							});
							$('#inputMediaHashes').val(playlistHashes);
						},
						stop: function( event, ui ) {
							var playlistHashes = '';
							$('#Playlistentries').each(function(){
								$(this).find('li').each(function(){
									playlistHashes+=$(this).attr('value') + ",";
								});
							});
							$('#inputMediaHashes').val(playlistHashes);
						},
						connectWith: ".playlistContainer"
					}).disableSelection();
					});
				</script>				
				<p>To add content to this playlist, drag and drop media from your library on the right, to the playlist on the left.  You can re-order the playlist items before clicking Submit.</p>
				<ul id="Playlistentries" class="playlistContainer left currentlyprocessing col3 opaque">
					<div class='videolist header'><span>This Playlist (<?php echo $playlistEntry[0]['playlistName'];?>)</span></div>
					<?php
						$hashes = explode(",",$playlistEntry[0]['mediaHashes']);
						foreach($hashes as $hash) {
							if($hash==='') { continue; }
							$editEntry = $Streaming->ShowEntrySupport($hash);
							echo "<li value='$hash'><img src='" . $editEntry['fileposter'] . "'/> <span>" . $editEntry['filetitle'] . "</span><br>" . $editEntry['fileinfo'] . "</li>";
						}
					?>
				</ul>
				<div class="videolist left currentlyprocessing col2 opaque">
					<div class='videolist header'><span>My Library</span></div>
					<?php
						$ListEntries = $Streaming->ListEntries($userid);
						$ListEntriesArray = '';
						foreach($ListEntries as $entry) {
							if (in_array($entry['filePathHash'], $hashes)) {
								continue;
							}
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
							echo "<a href='#'><img class='left' src='./img/folder.png' /><span>$foldername</span></a>";
							if($count===1) { echo "<ul id='libraryentries' style='display:block;' class='playlistContainer'>"; } else { echo "<ul id='libraryentries' class='playlistContainer'>"; }
							foreach($unitid as $entry) {
								if($entry['filePoster']=='') {
									$theposter = "./img/defaultposter.png"; 
								} else { 
									$theposter = $entry['filePoster'];
								}
								echo "<li value='" . $entry['filePathHash'] . "'><img src='" . $theposter . "'/> <span>" . $entry['fileTitle'] . "</span><br>" . $entry['fileInfo'] . "</li>";
								$entrycount++;
							}
							$count++;
							echo "</ul></li>";	
						}
					echo "</ul>";
					?>
				</div>
				
				<br class="clear">
				<br class="clear">
				
				<input type="submit" id="submit-form" class="btn btn-success submit" value="SAVE" onclick="validatePlaylistForms()"><img class="submit hidden" src="./img/loading.gif" />
			</form>
			<?php
				} else {
			?>
			<div id="videotitle">
				<span class="submit-holder right"><label class="btn btn-success submit" for="submit-form">SAVE</label><img class="submit hidden" src="./img/loading.gif" /></span><button onclick="goBack()" class="right btn btn-warning">Go Back</button>
				<h3>Input File Details for: <?php echo "$uploadfilename";?></h3>
			</div>
		
			
			<form method="post" action="">
				<input type="hidden" name="userid" value="<?php echo $userid;?>">
				<input type="hidden" name="filepath" value="<?php echo $uploadfilename;?>">
				<input type="hidden" name="details" value="1">


				<div class="videoentry">
					<p class='inputlabelreq'>&nbsp;Video Title (Displayed to viewers): <input type="text" id="filetitle" name="filetitle" value="<?php echo $tempfiletitle;?>" required="required"></p>
					<br>
					<p class='inputlabelreq'><input type="checkbox" id="terms" name="terms" value="terms" required="required"> I have either created this media file or I have taken appropriate steps to ensure the use of this media file is not against any law.  I take full responsibility for any copyright material I upload to this system as agreed to in the <a href="Terms.php" target="_blank">Terms of Service Agreement</a>.</p>
				</div>
				<br>Optional:<br>
				
				<div class="left optional">
					<p>Poster Frame:</p>
					<select id="posterselect" name="fileposter">
						<option value="./img/defaultposter.png"
								data-imagesrc="./img/defaultposter.png">Poster 1
						</option>
						<option value="./img/defaultposter2.png"
								data-imagesrc="./img/defaultposter2.png">Poster 2
						</option>
						<option value="./img/defaultposter3.png"
								data-imagesrc="./img/defaultposter3.png">Poster 3
						</option>
						<option value="upload">Upload Custom Poster
						</option>
					</select>
					<fieldset id="imguploadfield" class="hidden">
						<legend>Drag & Drop a Poster Frame Image (.png or .jpg)<br>Recommended Dimensions: 1280 x 720 pixels</legend>
						<div class="imgupload" > </div>
						<span class="loadingimg" class="hidden" style="display:block;width:350px;">When ready, select the SAVE button.<br>To upload a different poster, click the remove button then upload a new poster or pick one of the default options.</span>
						<br>
					</fieldset>
				</div>			
				<div class="left optional">
					<p>Video Description:</p>
					<textarea id="fileinfo" name="fileinfo" cols="50" rows="8"></textarea>
				</div>
				<br class="clear"><br>

				<p><input type="checkbox" name="reqLogin" value="1"> Require Saclink Login for viewing</p>
				<br><br>
				
				<br><br>
				
				<input type="submit" id="submit-form" class="btn btn-success submit" value="SAVE" onclick="validateFileForms()"><img class="submit hidden" src="./img/loading.gif" />
			</form>
			<?php } ?>
		</div>
		<?php include "includeFooter.php";?>
	</body>
</html>
