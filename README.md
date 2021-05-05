# SMS
Streaming Media Server Front End

This front end allows users to upload media files and enter meta data for media before passing to our Adobe Media Server to be transcoded and moved to our streaming media hosting store.

=========

Server Requirements:

php
sqlite with php/pdo enabled

set higher values for the following in php.ini:
	php_value upload_max_filesize 2048M
	php_value post_max_size 2048M
	
(2048M = 2 gigabytes, set appropriately)

this max file size will need to be set in either of these locations for the upload script:
	./uploadvideo.php >> $max_file_size
	or see the "maxFileSize" javascript call in ./listMedia.php 

=========




updates to a few site based variables in ./init.php


updates needed to ./assets/streaming.class.php

	check general comments in this file to see what needs to be updated for your setup.  some important areas are listed below

	media information:
		check for function with network paths and update to your final storage location for your media
	


CAS auth:
	rename or copy the following files and update settings within:
	CASconfig-Default.php => CASconfig.php
	CASlogout-Default.php => CASlogout.php
	
	CAS will pass the $userid value which will be the user identifier, either username or usernumber, whatever you want to use for user identification
		if CAS is not used, you will need to pass $userid with your own auth handler


Authentication Check:
	checkAuth.php needs to be updated if not using CAS or if you have customized the login requirements.
		
	
Customizing the front end:

	there is a ./css/theme-default.css which outlines all the text/background/etc colors and images.  make a copy of this named "theme-mytheme.css" and make sure to include it in the ./includeHeader.php file under the theme-default entry.

	modifying the header and footer:
	there are ./includeHeader.php and ./includeFooter.php files that are included on each page.  modify these as needed to modify all pages as once.







The MIT License (MIT)

Copyright (c) 2015 Michael Barone --- mbarone000@gmail.com

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.