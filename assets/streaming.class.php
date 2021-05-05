<?php
/*
 *	Copyright (c) 2015 Michael Barone --- mbarone000@gmail.com
 */
class Streaming {
	
	function __construct($ASSETS,$UPLOAD,$BASEDIR,$log){
		$this->transcodefoldername = "00transcode";
		$this->transcodeservershare = "\\\\IRT-ARCHIVE-6\Transcode";

		$this->log=$log;
		$this->ASSETS = $ASSETS;
		$this->UPLOAD = $UPLOAD;
		$this->BASEDIR = $BASEDIR;
		$this->FILEPATH = dirname(__FILE__);
		define('DS', DIRECTORY_SEPARATOR);
		
		// check CAS-default files have been renamed/copied to make usable, give error if not
		
		
		if (!file_exists($this->ASSETS . DS . "streaming.db")) { 
			$this->CreateStreamingDB();
		} else {
			try {
				$this->streamingdb = new PDO('sqlite:' . $this->ASSETS . DS . 'streaming.db');
			} catch (PDOException $e) {
				$this->log->LogError("Database: streaming.db could not be opened: ". $e->getMessage());
				echo "Fatal: User could not open streaming.db: ". $e->getMessage();
				exit;
			}
		}
		if (!file_exists($this->ASSETS . DS . "stats.db")) { 
			$this->CreateStatsDB();
		} else {
			try {
				$this->statsdb = new PDO('sqlite:' . $this->ASSETS . DS . 'stats.db');
			} catch (PDOException $e) {
				$this->log->LogError("Database: stats.db could not be opened: ". $e->getMessage());
				echo "Fatal: User could not open stats.db: ". $e->getMessage();
				exit;
			}
		}
	}

/*
 * General application settings
 */ 	
	
	function GetUploadDir() {
		$uploaddir = "$this->UPLOAD";
		return $uploaddir;
	}	
	function GetTranscodeDir() {
		$transcodedir = $this->UPLOAD . DS . $this->transcodefoldername . DS;
		return $transcodedir;
	}
	function getUrl() {
		$url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
		$url = $url . $this->BASEDIR;
		return $url;
	}

	
/*
 * user information and authentication functions
 */ 
	
	function CheckAuthGroupAccess($groups,$groupsList,$userid) {
		$groupaccess = "denied";
		$checkme = $this->GetBetween("(",")","$groups");
		$checkthis = explode(",",$checkme);
		$allow = array("Staff","Faculty","Emeritus");
		foreach($checkthis as $item) {
			if($item === "" || $item === " ") { continue; }
			if(in_array("$item",$allow)) {
				$groupaccess = "allowed";
			}		
		}
		// this will add any users present in the userlevel function below, ie for special admins that are not faculy/staff
		if($this->UserLevel($userid)!=="none"){
			$groupaccess = "allowed";
		}

		// logic to only accept beta users
		$betalist = array("mbarone",
							"sls-mb",
							"sls-ckm",
							"yvera",
							"cvera",
							"mkay",
							"shawn.sumner",
							"mark.wilson",
							"rodrigue",
							"atcs-04",
							"atcs-03",
							"tuffer",
							"mayedac",
							"vasst",
							"odonnell",
							"chris.boosalis",
							"qian",
							"nelsen",
							"munguia",
							"ryan",
							"wes",
							"lmurphy",
							"michael.elfant",
							"carolyng",
							"fdillon",
							"tashirol",
							"smckay",
							"iko",
							"travis",
							"binod.pokhrel",
							"ekee",
							"lojom",
							"sjperez",
							"dahlquid",
							"mikelee",
							"jane.bruner",
							"ck852",
							"storres",
							"welkleyd",
							"castagna"
						);
		if(in_array("$userid",$betalist)) {
			$groupaccess = "allowed";
		} else {
			$groupaccess = "deniednobeta";	
		}


		/*
		// logic to deny specific users
		$denylist = array("saclinks");
		if(in_array("$userid",$denylist)) {
			$groupaccess = "denied";
		}
		*/

		/*
		 * this might be causing false negatives, some faculty/staff are also students
		if($groupaccess === "allowed") {
			$deny = array("Student","Applicant");
			foreach($checkthis as $item) {
				if($item === "" || $item === " ") { continue; }
				if(in_array("$item",$deny)) {
					$groupaccess = "deniednobeta";
				}
			}
		}*/
		// $asdf = array("univ-tbl-irt-managers","irt-managers");
		// array_push($asdf, "irt-staff","IRT-LeadershipTeam","irt-exec","irt-acs-managers","IRT-LeadershipTeam");
		$asdf = array("atcs-sacct-static","atcs-sacct-static1");
		foreach($asdf as $item) {
			if (strpos($groupsList, "$item") !== false) {
				//echo "<br>this person<br>has $item";
				//$groupaccess="deniednobeta";
			}
		}
		
		
		
		if($groupaccess==='allowed'){
			if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin']==='0') {
				$this->log->LogInfo("User $userid has been $groupaccess access to SMS");
				$this->UserLoggedIn($userid,$groupaccess);
				$this->sessionAddUsername($userid,$groupaccess);
				$_SESSION['userid']="$userid";
				$_SESSION['loggedin']='1';
			}
		}else{
			$_SESSION['userid']="$userid";
			$this->log->LogWarn("User $userid has been $groupaccess access to SMS");
			$this->UserLoggedIn($userid,$groupaccess);
			$this->sessionAddUsername($userid,$groupaccess);
		}
		return $groupaccess;		
	}
	
	// needs to change to usergroups
	function UserLevel($userid) {
		$userlevel = "none";
		$admins = array("mbarone","sls-mb","mayedac");
		$general = array("atcs-04");
		if(in_array("$userid",$admins)) {
			$userlevel = "admin";
		} elseif(in_array("$userid",$general)) {
			$userlevel = "general";
		}
		return $userlevel;
	}
	
	function AddUser($userid,$namef,$namel) {
		$timenow = time(); 
		$statement = $this->streamingdb->prepare("INSERT OR IGNORE INTO Users (userid,namef,namel,created) VALUES (:userid,:namef,:namel,:created)");
		try {
			$statement->execute(array(':userid'=>$userid,
										':namef'=>$namef,
										':namel'=>$namel,
										':created'=>$timenow
										));
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
	}
	
	function agreeToTerms($userid) {
		$timenow = time(); 
		$statement = $this->streamingdb->prepare("UPDATE Users SET agreeToTerms = :agreeToTerms WHERE userid = :userid");
		try {
			$statement->execute(array(':agreeToTerms'=>$timenow,
							':userid'=>$userid
							));
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
	}

	function checkAgreeToTerms($userid,$namef='',$namel='') {
		$statement = $this->streamingdb->prepare("SELECT agreeToTerms from Users WHERE userid='$userid'");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		if(empty($result) && $namef!='') {
			$agreed = "failed";
			$this->AddUser($userid,$namef,$namel);
		} elseif($result['0']['agreeToTerms']==='0'){
			$agreed = "failed";
		} elseif($result['0']['agreeToTerms']!=='0'){
			$agreed = $result['0']['agreeToTerms'];
			return $agreed;
		}		
		// at some point will need logic: elseif($result['0']['agreeToTerms'] < recentTermsUpdateTime) { $agreed = "failed"; }
		if(isset($agreed) && $agreed==="failed" && $namef!=''){
			header("Location: ./Terms.php");	
		} else {
			return $agreed;
		}
	}

	
	// may not need this function now after auto users create on login/checkAgreeToTerms
	// still on admin page
	function ReturnUsers() {
		// returns $userarray in the following format:  $userarray['userid']['namef']
													//	$userarray['userid']['namel']
													//	$userarray['userid']['userid']
		$statement = $this->streamingdb->prepare("SELECT * from Users ORDER BY namel ASC");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		$userarray = array();
		foreach($result as $row) {
			$userarray[$row['userid']]['namef'] = "$row[namef]";
			$userarray[$row['userid']]['namel'] = "$row[namel]";
			$userarray[$row['userid']]['userid'] = "$row[userid]";
			$userarray[$row['userid']]['created'] = "$row[created]";
			$userarray[$row['userid']]['agreeToTerms'] = "$row[agreeToTerms]";
		}
		return $userarray;
	}


	function AddGroup($groupName,$groupType) {
		$groupName = $this->sanitize($groupName);
		$groupType = $this->sanitize($groupType);
		$statement = $this->streamingdb->prepare("INSERT OR IGNORE INTO Groups (groupName,groupType) VALUES (:groupName,:groupType)");
		try {
			$statement->execute(array(':groupName'=>$groupName,
										':groupType'=>$groupType
										));
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
	}

	function ListGroups(){
		$statement = $this->streamingdb->prepare("SELECT * from Groups ORDER BY groupType ASC");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		return $result;		
	}

	function ListGroupUsers($groupID){
		$statement = $this->streamingdb->prepare("SELECT * from Groups_Users WHERE groupID=$groupID ORDER BY manager DESC");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		return $result;		
	}
	
	function AddGroupUser($groupID,$userid,$manager) {
		$groupID = $this->sanitize($groupID);
		$userid = $this->sanitize($userid);
		$manager = $this->sanitize($manager);
		$statement = $this->streamingdb->prepare("INSERT OR IGNORE INTO Groups_Users (groupID,userid,manager) VALUES (:groupID,:userid,:manager)");
		try {
			$statement->execute(array(':groupID'=>$groupID,
										':userid'=>$userid,
										':manager'=>$manager
										));
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
	}

	function RemoveGroupUser($groupID,$userid) {
		$groupID = $this->sanitize($groupID);
		$userid = $this->sanitize($userid);
		$statement = $this->streamingdb->prepare("DELETE FROM Groups_Users WHERE groupID=:groupID AND userid=:userid)");
		try {
			$statement->execute(array(':groupID'=>$groupID,
										':userid'=>$userid
										));
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
	}
	

/*
 *  File handling section.  this outlines where the final encoded videos will be stored and accessed from
 *  also builds embed code for placing on any html page.  this will need to be edited to your environment
 */
	
	function AddEntry($userid,$filepath,$filetitle,$fileinfo,$fileposter,$reqLogin,$adhoc="no",$adhocUserid="none") {

		// check if filepath or filetitle already exist in db for user.  this may cause encoding/renaming issues
		// return error if filepath or filetitle exist
	
	
		// sanitize... see sanitize function below
		$userid = $this->sanitize($userid);
		$filepath = $this->sanitize($filepath);
		$filetitle = $this->sanitize($filetitle);
		$fileinfo = $this->sanitize($fileinfo);
		$fileposter = $this->sanitize($fileposter);

		$timenow = time();
		$path = "prj/indiv/$userid/";
		$filepathnew = str_replace("@", "", $filepath);
		$filepathnew = str_replace("(", "", $filepathnew);
		$filepathnew = str_replace(")", "", $filepathnew);
		$filepathnew = preg_replace('/\.(?=.*\.)/', '_', $filepathnew);
		$filepathnew = $timenow . preg_replace('/\s+/', '_', $filepathnew);
		$thisfilepath = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filepathnew);
		$thispath = $path . "$thisfilepath";
		$transcodedir = $this->GetTranscodeDir();
		$uploaddir = $this->GetUploadDir() . DS . $userid . DS ;
		$filePathHash = $this->CreateHash($thispath);
		if($adhoc==="yes"){
			$origSize='';
		} else {
			$origSize = filesize("$uploaddir$filepath");
			$origSize = $origSize >= 0 ? $origSize : 4*1024*1024*1024 + $origSize;
		}
		$statement = $this->streamingdb->prepare("INSERT INTO Media_Library (userid,filePath,filePathHash,fileTitle,fileInfo,filePoster,created,reqLogin,origSize) VALUES (:userid,:filepath,:filePathHash,:filetitle,:fileinfo,:fileposter,:created,:reqLogin,:origSize)");
		$this->log->LogInfo("Media File Processing Starting for $uploaddir$filepath ($filePathHash) at filesize:$origSize ");
		try {
			$statement->execute(array(':userid'=>$userid,
										':filepath'=>$thispath,
										':filePathHash'=>$filePathHash,
										':filetitle'=>$filetitle,
										':fileinfo'=>$fileinfo,
										':fileposter'=>$fileposter,
										':created'=>$timenow,
										':reqLogin'=>$reqLogin,
										':origSize'=>$origSize
										));
		} catch(PDOException $e) {
			$this->log->LogError("Media File Processing for $uploaddir$filepath ($filePathHash) encountered an error adding to the database: $e->getMessage()");
			return "Statement failed: " . $e->getMessage();
		}
		$filename = "$userid@$timenow@$filepathnew";
		if($adhoc==="yes"){
			$this->log->LogInfo("Media File Adhoc Add started by user:$adhocUserid for user:$userid and file:$filename ($filePathHash)");
			return "$filename";
		} else {
			$this->log->LogInfo("Media File Processing Renaming $uploaddir$filepath to $transcodedir$filename ($filePathHash)");
			rename("$uploaddir$filepath", "$transcodedir$filename");
			touch("$transcodedir$filename", time());
			$pathtotranscode = substr_replace($this->FILEPATH,"",-6);
			$pathtotranscode = $pathtotranscode . "upload" . DS . $this->transcodefoldername . DS . "$filename";
			$bat_file = $this->FILEPATH . DS . "copytopostprocess.bat " . escapeshellarg($pathtotranscode) . " " . escapeshellarg($this->transcodeservershare);
			$this->log->LogInfo("Media File Processing Attempting to move to file $transcodedir$userid@$timenow@$filepathnew ($filePathHash) to transcode server");
			shell_exec("$bat_file");
			$this->log->LogInfo("Media File Processing Successfully Completed by $userid -- $filepath ($filePathHash), $filetitle, $fileinfo");
			return "success";
		}
	}

	function EditEntry($userid,$filepath,$filetitle,$fileinfo,$fileposter,$reqLogin,$disabled) {
		$userid = $this->sanitize($userid);
		$filepath = $this->sanitize($filepath);
		$filetitle = $this->sanitize($filetitle);
		$fileinfo = $this->sanitize($fileinfo);
		$fileposter = $this->sanitize($fileposter);
		$filePathHash = $this->CreateHash($filepath);
		$statement = $this->streamingdb->prepare("UPDATE Media_Library SET fileTitle = :filetitle, fileInfo = :fileinfo, filePoster = :fileposter, reqLogin = :reqLogin, disabled = :disabled WHERE filePathHash = :filePathHash AND filePath = :filepath");
		try {
			$statement->execute(array(':filepath'=>$filepath,
							':filePathHash'=>$filePathHash,
							':filetitle'=>$filetitle,
							':fileinfo'=>$fileinfo,
							':fileposter'=>$fileposter,
							':reqLogin'=>$reqLogin,
							':disabled'=>$disabled
							));
		} catch(PDOException $e) {
			$this->log->LogError("Edit Media File Error ($filePathHash) by $userid -- new info: fileTitle = '$filetitle', fileInfo = '$fileinfo', filePoster = '$fileposter', reqLogin = '$reqLogin', disabled = '$disabled', error = ".$e->getMessage());	
			return "Statement failed: " . $e->getMessage();
		}
		$this->log->LogInfo("Edit Media File Successfully ($filePathHash) by $userid -- new info: fileTitle = '$filetitle', fileInfo = '$fileinfo', filePoster = '$fileposter', reqLogin = '$reqLogin', disabled = '$disabled'");	
	}
	
	

/*
 * MediaLibraryFolders functions
 */	

	function CreateMediaLibraryFolder($params) {
		$params = explode("@@@",$params);
		$userid = $params[0];
		$folderName = $params[1];
		$folderName = $this->sanitize($folderName);
		$statement = $this->streamingdb->prepare("INSERT INTO Media_Library_Folders (userid,folderName) VALUES (:userid,:folderName)");
		try {
			$statement->execute(array(':userid'=>$userid,
										':folderName'=>$folderName
										));
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
	}	

	function GetMediaLibraryFolders($userid) {
		$statement = $this->streamingdb->prepare("SELECT * from Media_Library_Folders WHERE userid = '$userid'");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		return $result;
	}


	function ChooseFolderFor($params) {
		$params = explode("@@@",$params);
		$mediaHash = $params[0];
		$userid = $params[1];
		$folderName = $params[2];
		$userid = $this->sanitize($userid);
		$folderName = $this->sanitize($folderName);		
		$statement = $this->streamingdb->prepare("UPDATE Media_Library SET folder = :folderName WHERE filePathHash = '$mediaHash'");
		try {
			$statement->execute(array(':folderName'=>$folderName));
		} catch(PDOException $e) {
			$this->log->LogError("Edit Media File Error ($mediaHash) by $userid -- new info: folder = '$folderName', error = ".$e->getMessage());	
			return "Statement failed: " . $e->getMessage();
		}
		$this->log->LogInfo("Edit Media File Successfully ($mediaHash) by $userid -- new info: folderName = '$folderName'");	
	}

	
	
	// need delete function, remove line from Media_Library_Folders and update folder if userid and folder = that of removed
	
	
	
	// need add media to media_library_folder.  like edit media above
	
	
	

/*
 * Playlist functions
 */	

	function CreatePlaylist($userid,$playlistName) {
		$timenow = time();
		$playlistName = $this->sanitize($playlistName);
		$hashthis = $playlistName . $timenow;
		$playlistHash = $this->CreateHash($hashthis);
		$statement = $this->streamingdb->prepare("INSERT INTO Media_Playlists (userid,playlistName,playlistHash,created) VALUES (:userid,:playlistName,:playlistHash,:created)");
		try {
			$statement->execute(array(':userid'=>$userid,
										':playlistName'=>$playlistName,
										':playlistHash'=>$playlistHash,
										':created'=>$timenow
										));
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
	}


	function EditPlaylist($userid,$playlistName,$playlistHash,$mediaHashes) {
		$userid = $this->sanitize($userid);
		$playlistName = $this->sanitize($playlistName);
		$playlistHash = $this->sanitize($playlistHash);
		$mediaHashes = $this->sanitize($mediaHashes);
		$statement = $this->streamingdb->prepare("UPDATE Media_Playlists SET playlistName = :playlistName, mediaHashes = :mediaHashes WHERE playlistHash = '$playlistHash' AND userid = '$userid'");
		try {
			$statement->execute(array(':playlistName'=>$playlistName,
							':mediaHashes'=>$mediaHashes
							));
		} catch(PDOException $e) {
			$this->log->LogError("Edit Playlist Error ($playlistHash) by $userid -- new info: mediaHashes = '$mediaHashes', playlistName = '$playlistName', error = ".$e->getMessage());	
			return "Statement failed: " . $e->getMessage();
		}
		$this->log->LogInfo("Edit Media File Successfully ($playlistHash) by $userid -- new info: mediaHashes = '$mediaHashes', playlistName = '$playlistName'");	
	}

	// not done
	function GetPlaylist($playlistHash) {
		$statement = $this->streamingdb->prepare("SELECT * from Media_Playlists WHERE playlistHash = '$playlistHash'");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		return $result;
	}


	// not done
	function ListPlaylists($userid,$sort='') {
		if(isset($sort) && $sort != '') {
			$statement = $this->streamingdb->prepare("SELECT * from Media_Playlists WHERE userid = '$userid' ORDER BY $sort DESC");
		} else {
			$statement = $this->streamingdb->prepare("SELECT * from Media_Playlists WHERE userid = '$userid'");
		}
		// might need to do a join to make the below foreach easier

		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		// foreach result, make array[playlistName][mediaHash]
		return $result;
	}	
	
	
	
/*
 * Page handling functions. pulls info to create media availability and processing views
 */

	function NeedsProcessing($userid) {
		$processthese = Array();
		$uploaddir = $this->GetUploadDir() . "/$userid/";
		$exclude = array( ".","..","Thumbs.db","poster" );
		if (is_dir($uploaddir)) {
			$files = scandir($uploaddir);
			foreach($files as $file){
				if(!in_array($file,$exclude)){
					$processthese[] = $file;
				}
			}
		}
		return $processthese;
	}

	function CurrentlyProcessing($userid) {
		$statement = $this->streamingdb->prepare("SELECT * from Media_Library WHERE userid = '$userid' AND postprocessed = '0'");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		return $result;
	}
	
	function ListEntries($userid,$sort='') {
		// list all video entries per userid
		// called on MediaLibrary.php
		
		if(isset($sort) && $sort != '') {
			$statement = $this->streamingdb->prepare("SELECT * from Media_Library WHERE userid = '$userid' AND postprocessed > 0 ORDER BY $sort ASC");
		} else {
			$statement = $this->streamingdb->prepare("SELECT * from Media_Library WHERE userid = '$userid' AND postprocessed > 0");
		}
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		return $result;
	}

	function ListRecentEntries() {
		$statement = $this->streamingdb->prepare("SELECT * from Media_Library ORDER BY indexID DESC LIMIT 100");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		return $result;
	}
	
	function ShowEntry($filePathHash,$embed="no") {
		// pull info for single video page
		// called on PlayMedia.php and EmbedMedia.php
		$statement = $this->streamingdb->prepare("SELECT * from Media_Library WHERE filePathHash = '$filePathHash'");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			$this->log->LogError("Play Media File with filePathHash: $filePathHash encountered an error opening the database: $e->getMessage()");
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();

		if(isset($result[0]['userid'])) {
			$thisuserid = $result[0]['userid'];
		} else {
			$this->log->LogError("Play Media File with filePathHash: $filePathHash did not return a proper owner userid from the db");
			return "failed 2";
		}
		
		$filePath=$result[0]['filePath'];
		//$hashmatch = $this->CheckHash($filePathHash,$filePath);
		//if($hashmatch){
			if(!isset($result[0]['filePoster']) || $result[0]['filePoster'] == '') { $result[0]['filePoster'] = "./img/defaultposter.png"; }
			$thisfile = $result[0]['fileTitle'];
			if($result[0]['reqLogin']!=='1'){
				//only log if login not required.  otherwise view gets logged on playmedia.php
				// need to doublecheck this function to ensure a logged in user gets logged properly without the need to refresh
				if(isset($_SESSION['userid'])) {
					$userid = $_SESSION['userid'];
				} elseif(isset($_SESSION['phpCAS']['user'])) {
					$userid = $_SESSION['phpCAS']['user'];
				} elseif(!isset($_SESSION['phpCAS']['user'])) {
					// set bit to only check if user logged in, not prompt for login
					$checkCASlogged=1;
					require "CASauth.php";
					if(isset($_SESSION['phpCAS']['user'])) {
						$userid = $_SESSION['phpCAS']['user'];
					} else {
						$userid=0;
					}
				} else {
					$userid=0;
				}
				if($embed==="yes"){
					$comments = "embed";
				} else {
					$comments = '';
				}
				$this->MediaWatched($filePathHash,$userid,$comments);
				if($userid===0){
					$this->log->LogInfo("Play Media File '$thisfile' ($filePathHash) uploaded by user:$thisuserid was loaded for viewing without Login Required");
				} else {
					$this->log->LogInfo("Play Media File '$thisfile' ($filePathHash) uploaded by user:$thisuserid was loaded for viewing without Login Required by userid:$userid");
				}
			}
			return array(
				'userid'		=> $result[0]['userid'],
				'filetitle'		=> $result[0]['fileTitle'],
				'fileinfo'		=> $result[0]['fileInfo'],
				'filepath'		=> $result[0]['filePath'],
				'fileposter'	=> $result[0]['filePoster'],
				'created'		=> $result[0]['created'],
				'postprocessed'	=> $result[0]['postprocessed'],
				'reqLogin'		=> $result[0]['reqLogin'],
				'disabled'		=> $result[0]['disabled'],
				'folder'		=> $result[0]['folder']
			);			
		//} else {
		//	$this->log->LogWarn("Play Media File with filePathHash of $filePathHash did not match the filePath of $filePath uploaded by user:$thisuserid");
		//	return "failed 3";
		//}
	}

	function ShowEntrySupport($filePathHash) {
		// pull info for single video page
		// called on AdminAccess.php and mediadetails.php
		$statement = $this->streamingdb->prepare("SELECT * from Media_Library WHERE filePathHash = '$filePathHash'");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		if(!isset($result[0]['filePoster']) || $result[0]['filePoster'] == '') { $result[0]['filePoster'] = "./img/defaultposter.png"; }
		$thisfile = $result[0]['fileTitle'];
		return array(
			'userid'		=> $result[0]['userid'],
			'filetitle'		=> $result[0]['fileTitle'],
			'fileinfo'		=> $result[0]['fileInfo'],
			'filepath'		=> $result[0]['filePath'],
			'fileposter'	=> $result[0]['filePoster'],
			'created'		=> $result[0]['created'],
			'postprocessed'	=> $result[0]['postprocessed'],
			'reqLogin'		=> $result[0]['reqLogin'],
			'disabled'		=> $result[0]['disabled'],
			'folder'		=> $result[0]['folder']
		);			
	}



	
	
	
/*
 * testing quality check functions, not implemented yet
 */ 
	
	function CheckQuality($userid,$file) {
		//$uploaddir = $this->GetUploadDir() . "\\$userid\\";
		$cmdthis = escapeshellcmd("C:/sites/Acorn/assets/ffprobe.exe -v quiet -print_format json -show_format C:/sites/Acorn/upload/$userid/$file");
		//echo $cmdthis;
		$output = "test";
		passthru("$cmdthis", $output);
		
		
		return $output;
		
	}

	
	function CheckQualityFile($userid,$file) {
		$cmdthis = escapeshellcmd("C:/sites/Acorn/assets/checkbitrate.bat $userid $file");
		//echo $cmdthis;
		$output = "test";
		passthru("$cmdthis", $output);
		
		$someoutput = "testing this function";
		return $someoutput;
	}

/*
 * end quality check section
 */ 


	
	






/*
 * General helper functions
 */
 
	function GetBetween($var1="",$var2="",$pool){
		$temp1 = strpos($pool,$var1)+strlen($var1);
		$result = substr($pool,$temp1,strlen($pool));
		$dd=strpos($result,$var2);
		if($dd == 0){
			$dd = strlen($result);
		}
		return substr($result,0,$dd);
	} 

	
	function AddPoster($userid,$file) {
		
		
	}

	function CheckEntries($userid) {
		// reconcile entries in db vs in the file system..  remove from db if no video file exists
	}
 
	function sanitize($data) {
		require_once "./lib/HTMLPurifier.includes.php";
		require_once "./lib/HTMLPurifier.autoload.php";
		if(!isset($HTMLPurConfig) || !isset($HTMLPurifier)) {
			$HTMLPurConfig = HTMLPurifier_Config::createDefault();
			$HTMLPurifier = new HTMLPurifier($HTMLPurConfig);
		}
		$data = $HTMLPurifier->purify($data);
		return $data;
	}	
	
	function CreateHash($hashinput){
		$thishash = openssl_digest("$hashinput", 'sha1');
		//$thishash = openssl_digest("$hashinput", 'sha256');
		return $thishash;
	}
	
	function CheckHash($currenthash,$hashinput){
		$testhash = $this->CreateHash($hashinput);
		if($currenthash == $testhash){
			return true;
		} else {
			return false;
		}
	}

/*
 * Stats functions
 */	

 
	function getMediaPlaybackTime($mediaHash) {
		$statement = $this->statsdb->prepare("SELECT Timestamp,currentTime,totalTime from MediaPlaybackTime WHERE mediaHash = '$mediaHash' ORDER BY Timestamp ASC");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		$thearray = array();
		$i=0;
		if(!isset($result[0]['totalTime'])) { return "failed"; }
		for($i=1;$i<($result[0]['totalTime']+1);$i++){
			$thearray[$i]['temp']='';
		}
		foreach($result as $thisresult){
			$thearray[$thisresult['currentTime']][$thisresult['Timestamp']]=$thisresult['currentTime'];
			
			/*		
			$thearray[$i]['mediaHash'] = $thisresult['mediaHash'];
			$thearray[$i]['userid'] = $thisresult['userid'];
			$thearray[$i]['Timestamp'] = $thisresult['Timestamp'];
			$thearray[$i]['ip'] = $thisresult['ip'];
			$thearray[$i]['browser'] = $thisresult['browser'];
			$thearray[$i]['browserv'] = $thisresult['browserv'];
			$thearray[$i]['os'] = $thisresult['os'];
			$thearray[$i]['comments'] = $thisresult['comments'];
			$i++;*/
		}
		$thearray['totalTime']=$thisresult['totalTime'];
		return $thearray;		
		
	}
 
	function MediaPlaybackTime($mediaPlayback,$userid='') {
		$mediaexplode = explode("@@@",$mediaPlayback);
		$mediaPlayback = json_decode($mediaexplode[0], true);
		$sessionID=session_id();
		$ip=$_SERVER['REMOTE_ADDR'];
		if(!isset($userid) || $userid ==="") {
			if(isset($_SESSION['userid']) && $_SESSION['userid'] != "") {
				$userid = $_SESSION['userid'];
			} elseif(isset($_SESSION['phpCAS']['user']) && $_SESSION['phpCAS']['user'] != "") {
				$userid = $_SESSION['phpCAS']['user'];
			} else {
				$userid = 1;
			}
		}
		$mediaHash=$mediaexplode[1];
		foreach($mediaPlayback as $mediaPlaybackTime){
			$timestamp=$mediaPlaybackTime[0]['timestamp'];
			$currenttime=$mediaPlaybackTime[0]['currentTime'];
			$totaltime=$mediaPlaybackTime[0]['totalTime'];
			if($currenttime<0) { continue; }
			/*
			print_r($mediaPlaybackTime[0]);
			echo "<br>timestamp=" . $timestamp . "<br>";
			echo "<br>currentTime=" . $currenttime . "<br>";
			echo "<br>totalTime=" . $totaltime . "<br>";
			echo "<br>$userid<br>";
			echo "<br>mediaHash=$mediaHash<br>";
			echo "<br>" . $sessionID . "<br>";
			echo "<br>ip=" . $ip . "<br>";
			*/
			$statement = $this->statsdb->prepare("INSERT INTO MediaPlaybackTime (mediaHash,userid,sessionID,ip,Timestamp,currentTime,totalTime) VALUES (:mediaHash,:userid,:sessionID,:ip,:Timestamp,:currentTime,:totalTime)");
			try {
				$statement->execute(array(':mediaHash'=>$mediaHash,
											':userid'=>$userid,
											':sessionID'=>$sessionID,
											':ip'=>$ip,
											':Timestamp'=>$timestamp,
											':currentTime'=>$currenttime,
											':totalTime'=>$totaltime
											));
			} catch(PDOException $e) {
				return "Statement failed: " . $e->getMessage();
			}
		}
	}
 
 
	function MediaWatched($mediaHash,$userid='',$comments='') {
		if(!isset($userid) || $userid ==="") {
			if(isset($_SESSION['userid']) && $_SESSION['userid'] != "") {
				$userid = $_SESSION['userid'];
			} elseif(isset($_SESSION['phpCAS']['user']) && $_SESSION['phpCAS']['user'] != "") {
				$userid = $_SESSION['phpCAS']['user'];
			} else {
				$userid = 1;
			}
		}
		if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=''){
			$comments = $comments . "," . $_SERVER['HTTP_REFERER'];
		}
		require_once('lib/UserAgentParser.php');
		if(!isset($ua)){
			$ua = parse_user_agent();
		}		
		$timenow = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		$browser = $ua['browser'];
		$browserv = $ua['version'];
		$os = $ua['platform'];
		$statement = $this->statsdb->prepare("INSERT INTO MediaPlay (mediaHash,userid,Timestamp,ip,browser,browserv,os,comments) VALUES (:mediaHash,:userid,:Timestamp,:ip,:browser,:browserv,:os,:comments)");
		try {
			$statement->execute(array(':mediaHash'=>$mediaHash,
										':userid'=>$userid,
										':Timestamp'=>$timenow,
										':ip'=>$ip,
										':browser'=>$browser,
										':browserv'=>$browserv,
										':os'=>$os,										
										':comments'=>$comments
										));
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
	}
 
	function UserLoggedIn($userid,$access) {
		require_once('lib/UserAgentParser.php');
		if(!isset($ua)){
			$ua = parse_user_agent();
		}
		$timenow = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		$browser = $ua['browser'];
		$browserv = $ua['version'];
		$os = $ua['platform'];
		$sessionID = session_id();
		$statement = $this->statsdb->prepare("INSERT INTO UserLogin (userid,sessionID,access,Timestamp,ip,browser,browserv,os) VALUES (:userid,:sessionID,:access,:Timestamp,:ip,:browser,:browserv,:os)");
		try {
			$statement->execute(array(':userid'=>$userid,
										':sessionID'=>$sessionID,
										':access'=>$access,
										':Timestamp'=>$timenow,
										':ip'=>$ip,
										':browser'=>$browser,
										':browserv'=>$browserv,
										':os'=>$os
										));
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
	}
	
	function GetUserLogIns($type='none',$lastts='0',$limit='100') {
		if($type==='none') {
			if($lastts==='0'){
				$statement = $this->statsdb->prepare("SELECT * from UserLogin ORDER BY Timestamp DESC LIMIT $limit");
			} else {
				$statement = $this->statsdb->prepare("SELECT * from UserLogin ORDER BY Timestamp DESC WHERE Timestamp < '$lastts' LIMIT $limit");
			}
		}elseif($type==='failed') {
			if($lastts==='0'){
				$statement = $this->statsdb->prepare("SELECT * from UserLogin WHERE access != 'allowed' ORDER BY Timestamp DESC LIMIT $limit");
			} else {
				$statement = $this->statsdb->prepare("SELECT * from UserLogin WHERE access != 'allowed' ORDER BY Timestamp DESC WHERE Timestamp < '$lastts' LIMIT $limit");
			}
		}
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		$thearray = array();
		$i=0;
		foreach($result as $thisresult){
			$thearray[$i]['userid'] = $thisresult['userid'];
			$thearray[$i]['access'] = $thisresult['access'];
			$thearray[$i]['Timestamp'] = $thisresult['Timestamp'];
			$thearray[$i]['ip'] = $thisresult['ip'];
			$thearray[$i]['browser'] = $thisresult['browser'];
			$thearray[$i]['browserv'] = $thisresult['browser'];
			$thearray[$i]['os'] = $thisresult['os'];			
			$i++;
		}
		return $thearray;
	}

	function GetMediaPlays($lastts='0',$limit='100') {
		if($lastts==='0'){
			$statement = $this->statsdb->prepare("SELECT * from MediaPlay ORDER BY Timestamp DESC LIMIT $limit");
		} else {
			$statement = $this->statsdb->prepare("SELECT * from MediaPlay ORDER BY Timestamp DESC WHERE Timestamp < '$lastts' LIMIT $limit");
		}
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		$thearray = array();
		$i=0;
		foreach($result as $thisresult){
			$thearray[$i]['mediaHash'] = $thisresult['mediaHash'];
			$thearray[$i]['userid'] = $thisresult['userid'];
			$thearray[$i]['Timestamp'] = $thisresult['Timestamp'];
			$thearray[$i]['ip'] = $thisresult['ip'];
			$thearray[$i]['browser'] = $thisresult['browser'];
			$thearray[$i]['browserv'] = $thisresult['browser'];
			$thearray[$i]['os'] = $thisresult['os'];
			$thearray[$i]['comments'] = $thisresult['comments'];
			$i++;
		}
		return $thearray;
	}	

	function GetMediaStats($mediaHash) {
		$statement = $this->statsdb->prepare("SELECT * from MediaPlay WHERE mediaHash = '$mediaHash' ORDER BY Timestamp ASC");
		try {
			$statement->execute();
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
		$result = $statement->fetchAll();
		$thearray = array();
		$i=0;
		foreach($result as $thisresult){
			$thearray[$i]['mediaHash'] = $thisresult['mediaHash'];
			$thearray[$i]['userid'] = $thisresult['userid'];
			$thearray[$i]['Timestamp'] = $thisresult['Timestamp'];
			$thearray[$i]['ip'] = $thisresult['ip'];
			$thearray[$i]['browser'] = $thisresult['browser'];
			$thearray[$i]['browserv'] = $thisresult['browserv'];
			$thearray[$i]['os'] = $thisresult['os'];
			$thearray[$i]['comments'] = $thisresult['comments'];
			$i++;
		}
		return $thearray;
	}




// new stuff


	function sessionCreate() {
		// add browser info to session, check if same, if not log out due to security
		if(!isset($_SESSION['setSessioninfo']) || $_SESSION['setSessioninfo']!=1){	
			require_once('lib/UserAgentParser.php');
			if(!isset($ua)){
				$ua = parse_user_agent();
			}
			$timenow = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			$browser = $ua['browser'];
			$browserv = $ua['version'];
			$os = $ua['platform'];
			$sessionID = session_id();
			$statement = $this->statsdb->prepare("INSERT INTO Session_Store (sessionID,Timestamp,ip,browser,browserv,os) VALUES (:sessionID,:Timestamp,:ip,:browser,:browserv,:os)");
			try {
				$statement->execute(array(':sessionID'=>$sessionID,
											':Timestamp'=>$timenow,
											':ip'=>$ip,
											':browser'=>$browser,
											':browserv'=>$browserv,
											':os'=>$os
											));
			} catch(PDOException $e) {
				return "Statement failed: " . $e->getMessage();
			}
			$_SESSION['setSessioninfo']=1;
		}
	}

	function sessionAddUsername($userid,$status='none') {
		if(!isset($_SESSION['setSessionUserid']) || $_SESSION['setSessionUserid']!=1){
			$timenow = time();
			$userid = $this->sanitize($userid);
			$sessionID = session_id();
			$statement = $this->statsdb->prepare("INSERT INTO Session_Userids (sessionID,userid,status,Timestamp) VALUES (:sessionID,:userid,:status,:Timestamp)");
			try {
				$statement->execute(array(':sessionID'=>$sessionID,
											':userid'=>$userid,
											':status'=>$status,
											':Timestamp'=>$timenow
											));
			} catch(PDOException $e) {
				return "Statement failed: " . $e->getMessage();
			}
			$_SESSION['setSessionUserid']=1;
		}
	}
	
	function PageView() {
		$timenow = time();
		$sessionID = session_id();
		$page = $_SERVER["PHP_SELF"];
		$noTrackPages = array("uploadimg.php","uploadvideo.php");
		foreach($noTrackPages as $thispage) {
			if (strpos($page, $thispage) !== false) {
				return;
			}
		}
		$params = $_SERVER["QUERY_STRING"];
		$referrer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
		$statement = $this->statsdb->prepare("INSERT INTO Page_Views (sessionID,Timestamp,page,params,referrer) VALUES (:sessionID,:Timestamp,:page,:params,:referrer)");
		try {
			$statement->execute(array(':sessionID'=>$sessionID,
										':Timestamp'=>$timenow,
										':page'=>$page,
										':params'=>$params,
										':referrer'=>$referrer
										));
		} catch(PDOException $e) {
			return "Statement failed: " . $e->getMessage();
		}
	}









	
	
/*
 * db creation functions and current db schema
 */
	
	function CreateStreamingDB() {
		// if DB not found, use this to create DB and setup schema
		$this->log->LogWarn("Database: streaming.db not found, trying to create now");
		try {
			$this->streamingdb = new PDO('sqlite:' . $this->ASSETS . '/streaming.db');
		} catch (PDOException $e) {
			$this->log->LogError("Database could not be created: $e->getMessage()");
			echo "Fatal: User could not open DB: $e->getMessage()";
			exit;
		}
		
		$query = "CREATE TABLE IF NOT EXISTS Media_Library (
										`indexID`	INTEGER PRIMARY KEY AUTOINCREMENT,
										`userid`	TEXT NOT NULL,
										`filePath`	TEXT NOT NULL UNIQUE,
										`filePathHash`	TEXT,
										`fileTitle`	TEXT NOT NULL,
										`fileInfo`	TEXT,
										`filePoster`	TEXT,
										`created`	INTEGER NOT NULL,
										`postprocessed`	INTEGER NOT NULL DEFAULT 0,
										`reqLogin`	INTEGER NOT NULL DEFAULT 0,
										`origSize`	INTEGER NOT NULL DEFAULT 0,
										`disabled`	INTEGER NOT NULL DEFAULT 0,
										`folder`	TEXT
									)";
		$statement = $this->streamingdb->prepare($query);
		$statement->execute();

		$query = "CREATE TABLE `Media_Library_Folders` (
										`indexID`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
										`userid`	TEXT NOT NULL,
										`folderName`	TEXT NOT NULL
									)";
		$statement = $this->streamingdb->prepare($query);
		$statement->execute();

		$query = "CREATE TABLE IF NOT EXISTS Users (
										`indexID`	INTEGER PRIMARY KEY AUTOINCREMENT,
										`userid`	TEXT NOT NULL UNIQUE,
										`namef`	TEXT NOT NULL,
										`namel`	TEXT NOT NULL,
										`created` INTEGER NOT NULL DEFAULT 0,
										`agreeToTerms` INTEGER NOT NULL DEFAULT 0
									)";
		$statement = $this->streamingdb->prepare($query);
		$statement->execute();
		
		$query = "CREATE TABLE IF NOT EXISTS Media_Playlists (
										`indexID`	INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE,
										`userid`	TEXT NOT NULL,
										`playlistName`	TEXT NOT NULL,
										`playlistHash`	TEXT NOT NULL UNIQUE,
										`mediaHashes`	TEXT,
										`created`	TEXT NOT NULL
									)";		
		$statement = $this->streamingdb->prepare($query);
		$statement->execute();
	
		$query = "CREATE TABLE IF NOT EXISTS `Groups_Users` (
										`indexID`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
										`groupID`	INTEGER NOT NULL,
										`userid`	TEXT NOT NULL,
										`manager`	INTEGER NOT NULL DEFAULT 0
									)";			
		$statement = $this->streamingdb->prepare($query);
		$statement->execute();		

		$query = "CREATE TABLE IF NOT EXISTS `Groups` (
										`indexID`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
										`groupName`	TEXT NOT NULL UNIQUE,
										`groupType`	TEXT NOT NULL,
										`groupOptions`	TEXT
									)";
		$statement = $this->streamingdb->prepare($query);
		$statement->execute();	
		
		$this->log->LogWarn("Database: streaming.db has been created");
	}
	function CreateStatsDB() {
		$this->log->LogWarn("Database: stats.db not found, trying to create now");	
		try {
			$this->statsdb = new PDO('sqlite:' . $this->ASSETS . '/stats.db');
		} catch (PDOException $e) {
			$this->log->LogError("Database: stats.db could not be created: $e->getMessage()");
			echo "Fatal: User could not open DB: $e->getMessage()";
			exit;
		}	

		$query = "CREATE TABLE IF NOT EXISTS UserLogin (
										`indexID`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
										`userid`	TEXT NOT NULL,
										`sessionID`	TEXT NOT NULL,
										`access`	TEXT NOT NULL,
										`Timestamp`	INTEGER NOT NULL,
										`ip` TEXT NOT NULL DEFAULT 0,
										`browser` TEXT NOT NULL,
										`browserv` TEXT NOT NULL,
										`os` TEXT NOT NULL
									)";
		$statement = $this->statsdb->prepare($query);
		$statement->execute();
		
		$query = "CREATE TABLE IF NOT EXISTS MediaPlay (
										`indexID`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
										`mediaHash`	TEXT NOT NULL,
										`userid`	TEXT NOT NULL DEFAULT 0,
										`Timestamp`	INTEGER NOT NULL,
										`ip` TEXT NOT NULL DEFAULT 0,
										`browser` TEXT NOT NULL,
										`browserv` TEXT NOT NULL,
										`os` TEXT NOT NULL,										
										`comments` TEXT
									)";
		$statement = $this->statsdb->prepare($query);
		$statement->execute();		


		$query = "CREATE TABLE IF NOT EXISTS MediaPlaybackTime (
										`indexID`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
										`mediaHash`	TEXT NOT NULL,
										`userid`	TEXT NOT NULL DEFAULT 0,
										`sessionID`	TEXT NOT NULL,
										`ip`	TEXT NOT NULL DEFAULT 0,
										`Timestamp`	INTEGER NOT NULL,
										`currentTime`	INTEGER NOT NULL,
										`totalTime`	INTEGER NOT NULL
									)";
		$statement = $this->statsdb->prepare($query);
		$statement->execute();			

		$query = "CREATE TABLE IF NOT EXISTS Session_Userids (
										`indexID`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
										`sessionID`	TEXT NOT NULL UNIQUE,
										`userid`	TEXT NOT NULL,
										`status`	TEXT NOT NULL DEFAULT 0,
										`Timestamp`	INTEGER NOT NULL
									)";
		$statement = $this->streamingdb->prepare($query);
		$statement->execute();

		$query = "CREATE TABLE IF NOT EXISTS Session_Store (
										`indexID`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
										`sessionID`	TEXT NOT NULL UNIQUE,
										`Timestamp`	INTEGER NOT NULL,
										`ip`	TEXT NOT NULL DEFAULT 0,
										`browser`	TEXT,
										`browserv`	TEXT,
										`os`	TEXT
									)";
		$statement = $this->streamingdb->prepare($query);
		$statement->execute();
	
		$query = "CREATE TABLE IF NOT EXISTS Page_Views (
										`indexID`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
										`sessionID`	TEXT,
										`Timestamp`	INTEGER NOT NULL,
										`page`	TEXT NOT NULL DEFAULT 0,
										`params`	TEXT,
										`referrer`	TEXT
									)";
		$statement = $this->streamingdb->prepare($query);
		$statement->execute();
		
		$this->log->LogWarn("Database: stats.db has been created");
	}
}


/*

groups table

indexID
groupName - admin, beta, denied
groupType - login, annotate, edit(name?)
groupOption - (admin-levels?) (login-denied/allowed) -- not currently implemented.


groups_users
indexID
groupID (indexID for group above)
userid (userid that is in the group, 1 entry per userid)
manager (binary (0 or 1) this will allow multiple people to easily manage the group)


Groups_Types
indexID
groupType (login, annotate, edit, etc)
description (visible description of type, for add/request group options)



add default groups on install
admin -- login -- allowed
denied -- login -- denied




*/
?>