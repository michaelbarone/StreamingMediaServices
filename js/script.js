function goBack() {
    window.history.back();
}

function close_window() {
	window.open('','_parent',''); 
	window.close();
}

function GetMediaLibraryFolders(userid,mediaHash) {
	$.ajax({
		url: 'curl.php?request=GetMediaLibraryFolders&param=' + userid + '&mediaHash=' + mediaHash + '',
		success: function(data) {
			var thisdata = data;
			Modal.open({
				content: thisdata
			});	
		}
	});
};

function CreateFolder(userid,mediaHash) {
	var thisvalue = $('#createFolderName').val();
	if(thisvalue!=''){
		$.ajax({
			url: 'curl.php?request=CreateMediaLibraryFolder&param=' + userid + '&folderName=' + thisvalue + '',
			success: function(data) {
				$('#nofolders').remove();
				var appendthis = "<a href='javascript:void(0)' class='btn btn-primary' style='margin:5px !important;' onclick=\"return ChooseFolderFor('" + mediaHash + "','" + thisvalue + "');\"><img class='left' style='height:20px;width:20px;margin-right:5px;' src='./img/folder.png' />Move to: " + thisvalue + "</a><br />";
				$("#FolderListContainer").append( appendthis );
			}
		});
	}
}

function ChooseFolderFor(thisid,folderName) {
	$.ajax({
		url: 'curl.php?request=ChooseFolderFor&param=' + thisid + '&folderName=' + folderName + '',
		success: function(data) {
			var id = '#move' + thisid + '';
			$(id).text("Moved to " + folderName);
			$(id).attr('class', 'btn btn-success right');
			Modal.close();
		}
	});
};

function ListGroupUsers(groupid) {
	$.ajax({
		url: 'curl.php?request=ListGroupUsers&param=' + groupid + '',
		success: function(data) {
			var thisdata = data;
			Modal.open({
				content: thisdata
			});	
		}
	});
};

function AddUserToGroup(userid,groupid) {
	var thisvalue = $('#addusertogroup').val();
	if(thisvalue!=''){
		var param = userid + "," + groupid;
		$.ajax({
			url: 'curl.php?request=AddUserToGroup&param=' + param + '',
			success: function(data) {
				$('#nofolders').remove();
				var appendthis = "<a href='javascript:void(0)' class='btn btn-primary' style='margin:5px !important;' onclick=\"return ChooseFolderFor('" + mediaHash + "','" + thisvalue + "');\"><img class='left' style='height:20px;width:20px;margin-right:5px;' src='./img/folder.png' />Move to: " + thisvalue + "</a><br />";
				$("#GroupUserListContainer").append( appendthis );
			}
		});
	}
}

/*
function dropvidaddcontent(){
							"
							<div id='strobeplayer'> \
							<object>\
							<param name='movie' value='http://www2.csus.edu/video/assets/players/StrobeMediaPlayback.swf?src=http://video2.csus.edu/hds-vod/$$$replacewithvideopath$$$_M.f4m&amp;poster=$$$replacewithvideoposter$$$&amp;bufferingOverlay=false&amp;optimizeInitialIndex=true&amp;minContinuousPlayback=30&amp;bufferTime=12&amp;initialBufferTime=8&amp;expandedBufferTime=10' />\
							<param name='allowFullScreen' value='true' />\
							<!--[if !IE]>-->\
								<object type='application/x-shockwave-flash' data='http://www2.csus.edu/video/assets/players/StrobeMediaPlayback.swf?src=http://video2.csus.edu/vod/$$$replacewithvideopath$$$_P.f4m&amp;poster=$$$replacewithvideoposter$$$&amp;bufferingOverlay=false&amp;optimizeInitialIndex=true&amp;minContinuousPlayback=30&amp;bufferTime=12&amp;initialBufferTime=8&amp;expandedBufferTime=10'>\
								<param name='allowFullScreen' value='true' />\
							<!--<![endif]-->\
							<div>\
								<p>\
									<video width='auto' height='100%' controls='' poster='$$$replacewithvideoposter$$$'>\
									<source src='http://video2.csus.edu/hls-vod/$$$replacewithvideopath$$$_M.m3u8'>\
									Your browser does not support Flash Player 10.1 or the HTML5 video element. \
									</video> \
								</p> \
							</div> \
							<!--[if !IE]>--> \
								</object> \
							<!--<![endif]--> \
							</object> \
							</div> \
							";
}
*/

function InfoPopup(thisinfo) {
	switch(thisinfo) {
		case "dropvideo":
			var thiscontent = "To get started, please drag and drop a .mp4, .m4v or .mov file into the designated area.\
							<br><br> You can also use the Browse button to search your computer or phone for .mp4, .m4v or .mov files to upload.\
							<br><br><br><br><center><iframe width='640' height='360' src='https://acorn.webapps.csus.edu/EmbedMedia.php?media=77b493731fac39d18717a850413e64296973ceb2&clipEndTime=60' frameborder='0' allowfullscreen=''></iframe></center>\
							";
			break;
			
		case "embed":
			var thiscontent = "The embed code can be used if you would like to include the streaming video directly on a webpage you have created. <br><br> Copy everything inside of the 'object' tags and paste that in your website.  The video should be displayed when you load the page, and you can click play to begin the stream.";
			break;
			
		case "videolink":
			var thiscontent = "This link can be emailed or posted in a SacCT course or other webpage.  Users will only have access to this single video using this link.";
			break;
		
		case "available":
			var thiscontent = "Available Streaming Media will list all of your media that is ready to be streamed by users.  You can organize these items into folders and use the links to share them.\
								<br><br><br><br><center><iframe width='640' height='360' src='https://acorn.webapps.csus.edu/EmbedMedia.php?media=77b493731fac39d18717a850413e64296973ceb2&clipStartTime=140' frameborder='0' allowfullscreen=''></iframe></center>\
								";
			break;

		case "processing":
			var thiscontent = "Media Being Processed will show you any media that is currently being processed for our streaming servers.  This may take some time depending on how long your video is and how many other videos were enqueued for processing before yours.  You can share this video now, but it will not be playable until processing is complete.  Once processing is completed and your video is ready to be streamed, the video entry will be displayed in the Available Media section below.\
								<br><br><br><br><center><iframe width='640' height='360' src='https://acorn.webapps.csus.edu/EmbedMedia.php?media=77b493731fac39d18717a850413e64296973ceb2&clipStartTime=120&clipEndTime=266' frameborder='0' allowfullscreen=''></iframe></center>\
								";
			break;

		case "needsinfo":
			var thiscontent = "Before your uploaded video can be processed by our system for streaming, a little more information is needed. \
								<br><br>  You can change the title of the video that is displayed to viewers along with a custom description and option to choose a custom poster frame.\
								<br><br><br><br><center><iframe width='640' height='360' src='https://acorn.webapps.csus.edu/EmbedMedia.php?media=77b493731fac39d18717a850413e64296973ceb2&clipStartTime=60&clipEndTime=120' frameborder='0' allowfullscreen=''></iframe></center>\
								";
			break;

		case "userselection":
			var thiscontent = "Click the refresh button to update the user list with any new users of the system.";
			break;
			
		case "createplaylist":
			var thiscontent = "";
			break;

		case "editplaylist":
			var thiscontent = "";
			break;
	}

	Modal.open({
		content: thiscontent,
		width: '50%'
	});
};

function TutorialPopup(topic) {
	switch(topic) {
		case "library":
			var thiscontent = "<iframe src='./includeInfo.php?tutorial=library'></iframe>";
			break;

		case "playlist":
			var thiscontent = "<iframe src='./includeInfo.php?tutorial=playlist'></iframe>";
			break;
	}
	Modal.open({
		content: thiscontent,
		width: '85%',
		height:'85%'
	});
};	
	
function validateFileForms(){
	var t = document.getElementById('filetitle');
	var f = document.getElementById('fileinfo');
	var t = document.getElementById('terms');
	if (t.checked && f.value != '' && t.value != ''){
		return;
	}else{
		setTimeout( function(){ $('input.submit').show();$('label.submit').show();$('img.submit').hide();},1000 );
	}
}

function validatePlaylistForms(){
	var n = document.getElementById('playlistName');
	if (n.value != ''){
		return;
	}else{
		setTimeout( function(){ $('input.submit').show();$('label.submit').show();$('img.submit').hide();},1000 );
	}
}

$(document).ready(function() {
	$('.tutorial-link').click(function() {
		var tutorial = $(this).attr("tutorial");
		TutorialPopup(tutorial);
	});
	$('img.btn-question').click(function() {
		var thisinfo = $(this).attr("about");
		InfoPopup(thisinfo);
	});
	$('img.btn-refresh').click(function() {
		location.reload();
	});
	$('input.btn.submit').click(function() {
		$('label.submit').hide();
		$(this).hide();
		$('img.submit').show();
	});
	$('.advancedToggle').click(function() {
		if ($(this).next().is(':hidden')) {
			 $(this).next().slideDown();
			 $(this).addClass('active');
		} else {
			 $(this).next().slideUp();
			 $(this).removeClass('active');
		}
	});	
	
	var clipboardSnippets = new Clipboard('.btn.clipboard');
	var clipboardtimeout;
	clipboardSnippets.on('success', function(e) {
		clearTimeout(clipboardtimeout);
		clearClipboardButton();
		$(e.trigger).attr('class', 'btn btn-success clipboard');
		$(e.trigger).text('Copied to Clipboard');
		e.clearSelection();
		clipboardtimeout = setTimeout(clearClipboardButton,12000);
	});
	clipboardSnippets.on('error', function(e) {
		clearTimeout(clipboardtimeout);
		clearClipboardButton();
		$(e.trigger).attr('class', 'btn btn-danger clipboard');
		$(e.trigger).text('Automatic Copy Failed, please select the text and copy all of the code manually');
		e.clearSelection();
		clipboardtimeout = setTimeout(clearClipboardButton,12000);
	});
	
});

function clearClipboardButton() {
	$('.btn-success.clipboard').text($('.btn-success.clipboard').attr('defaulttext'));
	$('.btn-danger.clipboard').text($('.btn-danger.clipboard').attr('defaulttext'));
	$('.btn-success.clipboard').attr('class', 'btn btn-primary clipboard');
	$('.btn-danger.clipboard').attr('class', 'btn btn-primary clipboard');
}