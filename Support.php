<?php
require_once "init.php";
$failed = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo "$PageTitlePrepend - Support - $PROJECT_TITLE";?></title>
		<?php include "includeHeader.php";?>
		<div id="content" class="content">
			<?php
			if($failed > 0) {
			?>
				<div id="videotitle">
					<h3>Error encountered.</h3>
				</div>				
			<?php
			} else { ?>
				<br />
				<div class="videolist">
					<div class="videolist header">
						<span>CSUS Streaming Media Services Support</span>
					</div>
					<div class='videoentry'>
						<ul class="accordian">
						   <li class='has-sub open'><a href='#'><span>Support and Request Information</span></a>
							  <ul style="display:block;">
								 <li class='has-sub open'><a href='#'><span>Contact for Direct support</span></a>
									<ul style="display:block;">
									   <li><span>Please email <a style="display:inline-block;" href="mailto:web-courses@csus.edu?Subject=Support%20for%20SMS%20Streaming%20Media" target="_top">web-courses@csus.edu</a> if you experience any problems or have questions about this service.</span></li>
									</ul>
								 </li>
								 <li class='has-sub'><a href='#'><span>Playback Issues?  Here is a list of currently supported browsers.</span></a>
									<ul>
										<li><span>Please email <a style="display:inline-block;" href="mailto:web-courses@csus.edu?Subject=SMS%20Streaming%20Issues" target="_top">web-courses@csus.edu</a> if you experience any problems or have questions about this service.</span></li>
										<li><span>Currently, only the recent versions of the following browsers are fully supported: Chrome, Firefox, Safari, Internet Explorer 10+</span></li>
										<li><span>If you are having issues with playback, please follow one of these links to download the newest version of these browsers:<br />
												<a class="btn btn-info left" href="https://www.google.com/chrome/browser/desktop/" target="_blank">Chrome</a>
												<a class="btn btn-info left" href="https://www.mozilla.org/en-US/firefox/new/" target="_blank">Firefox</a>
												<a class="btn btn-info left" href="https://support.apple.com/downloads/safari" target="_blank">Safari</a>
												<a class="btn btn-info left" href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" target="_blank">Internet Explorer</a><br /><br />
										</span></li>
									</ul>
								 </li>								 
								 <?php if($disableCAS===1 || $groupaccess==="allowed") { ?>
								 <li class='has-sub'><a href='#'><span>What if my saclink username changes and I cannot access my media?</span></a>
									<ul>
									   <li><span>Please email <a style="display:inline-block;" href="mailto:web-courses@csus.edu?Subject=Saclink%20Change%20for%20SMS%20Streaming%20Media" target="_top">web-courses@csus.edu</a> and let us know your old saclink and your new saclink usernames so we can add your media to your new account name.</span></li>
									</ul>
								 </li>
								 <li class='has-sub'><a href='#'><span>How can I request more features or make existing features better?</span></a>
									<ul>
									   <li><span>Please email <a style="display:inline-block;" href="mailto:web-courses@csus.edu?Subject=Feature%20Request%20for%20SMS%20Streaming%20Media" target="_top">web-courses@csus.edu</a> and tell us what you think about this service.  We intend on continually adding features, let us know what you think is important.</span></li>
									</ul>
								 </li>
								 <?php } ?>
							  </ul>
						   </li>
							<?php if($disableCAS===1 || $groupaccess==="allowed") { ?>
						   <li class='has-sub'><a href='#'><span>Creating and Managing Media</span></a>
							  <ul>
								 <li class='has-sub'><a href='#'><span>What types of media can I upload?</span></a>
									<ul>
									   <li><span>Currently, only .mp4, .m4v, and .mov files can be uploaded.  This includes files recorded from Android and Apple mobile devices.</span></li>
									</ul>
								 </li>
								 <li class='has-sub'><a href='#'><span>What if I have other types of media including .rm files?</span></a>
									<ul>
									   <li><span>Here are the instructions for converting .rm files to mp4.</span></li>
									</ul>
								 </li>								 
								 <li class='has-sub'><a href='#'><span>What happens when I upload my media?</span></a>
									<ul>
									   <li><span>After your file is uploaded to the streaming system, it needs to get processed for streaming to multiple devices.  This may take some time depending on how big the file is, and how many files were enqueued for processing before this one.</span></li>
									</ul>
								 </li>
								 <li class='has-sub'><a href='#'><span>What if I encounter problems?</span></a>
									<ul>
									   <li><span>Please email <a style="display:inline-block;" href="mailto:web-courses@csus.edu?Subject=Support%20for%20SMS%20Streaming%20Media" target="_top">web-courses@csus.edu</a> if you experience any problems or have questions about this service.</span></li>
									</ul>
								 </li>
							  </ul>
						   </li>
						   <li class='has-sub'><a href='#'><span>Sharing Media</span></a>
							  <ul>
								 <li class='has-sub'><a href='#'><span>How do I share media?</span></a>
									<ul>
									   <li><span>Once your video is being processed, you will have a shareable link to this video and also embed codes for any website or SacCT course you may want to share the video with.</span></li>
									</ul>
								 </li>
								 <li class='has-sub'><a href='#'><span>How secure is my Shared Media?</span></a>
									<ul>
									   <li><span>Since your media will be streamed to the people viewing it, they do not get to download a copy for offline viewing.  The viewers can only access your media with a direct link, meaning they cannot browse your content without you sharing the link.  Additionally, you can restrict users to just those who have official Saclink usernames, and require them to login before viewing the media.</span></li>
									</ul>
								 </li>
							  </ul>
						   </li>						   
						   <li class='has-sub'><a href='#'><span>Legal Safety and Copyright</span></a>
							  <ul>
								 <li class='has-sub'><a href='#'><span>What is subject to Copyright?</span></a>
									<ul>
									   <li><span>Please see the following document about copyright at CSUS: <a href="http://www.csus.edu/atcs/quikrefsite/PDFs/copyright_qr_2up.pdf" target="_blank">CSUS quick reference for Copyright</a><br />
										Generally, the following outlines what is subject to copyright:
										<br />--Audiovisual works, such as TV shows, movies, and online videos
										<br />--Sound recordings and musical compositions
										<br />--Written works, such as lectures, articles, books, and musical compositions
										<br />--Visual works, such as paintings, posters, and advertisements
										<br />--Video games and computer software
										<br />--Dramatic works, such as plays and musicals
										<br /><br />Ideas, facts, and processes are not subject to copyright. In order to be eligible for copyright protection, a work must be both creative and fixed in a tangible medium. Names and titles are not, by themselves, subject to copyright.
									   </span></li>
									</ul>
								 </li>
								 <li class='has-sub'><a href='#'><span>Can I use Copyright-protected work without infringing?</span></a>
									<ul>
									   <li><span>
									   In some circumstances, it is possible to use a copyright-protected work without infringing the owner’s copyright. For more about this, you may want to learn about <a href="https://www.youtube.com/yt/copyright/fair-use.html#yt-copyright-protection" target="_blank">fair use</a>.
										<br /><b style="font-weight:bold !important">Your video can still be claimed by a copyright owner, even if you have...</b>
										<br />--Given credit to the copyright owner
										<br />--Refrained from monetizing the infringing video
										<br />--Noticed similar videos that appear on YouTube or other streaming services
										<br />--Purchased the content on iTunes, a CD, or DVD
										<br />--Recorded the content yourself from TV, a movie theater, or the radio
										<br />--Stated that “no copyright infringement is intended"
										<br /><br />Some content creators choose to make their work available for reuse with certain requirements. For more about this, you may wish to learn about the <a href="https://creativecommons.org/about" target="_blank">Creative Commons license</a>.
									   </span></li>
									</ul>
								 </li>
							  </ul>
						   </li>
						   <li class='has-sub'><a href='#'><span>Terms of Service</span></a>
							  <ul>
								 <li><a href='terms.php' target="_blank"><span>Open the Terms of Service in a new window.</span></a></li>
							  </ul>
						   </li>						   
							<?php } ?>
						</ul>						
						
						<?php if($disableCAS===1 || $groupaccess==="allowed") { ?>
						<br><br><hr>
						<div id="videocontainer">
							<center>
								<span>Getting Started with Streaming Media Services:</span><br>
								<iframe width='640' height='360' src='https://acorn.webapps.csus.edu/EmbedMedia.php?media=77b493731fac39d18717a850413e64296973ceb2' frameborder='0' allowfullscreen=''></iframe>
							</center>
						</div>
						<?php } ?>
					</div>
				</div>
				<br class="clear"><br>
			<?php
			} 
			?>
		</div>
		<?php include "includeFooter.php";?>
	</body>
</html>