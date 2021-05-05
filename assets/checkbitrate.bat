@echo off
setlocal EnableDelayedExpansion

SET ffpath=C:\sites\Acorn\assets
SET inputpath=C:\sites\Acorn\upload\%1\%2
SET outputpath=%inputpath%


for %%f in (%inputpath%\*.f4v) do (

	REM echo %%~nxf
	REM call ffprobe and grab video info, create .info text file for each f4v file
	call "%ffpath%\ffprobe.exe" -v quiet -print_format json -show_format "%inputpath%\%%~nxf" > "%outputpath%\%%~nxf.info" 2>&1

	
	REM check .info for bitrate and create bitrate text file for each video
	set c=0	
	for /f "tokens=1,2 delims=:, " %%a in (' find ":" ^< "%outputpath%\%%~nxf.info" ') do (
	   set /a c+=1
	   set val[!c!]=%%~a = %%~b
	)
	for /L %%b in (1,1,!c!) do echo !val[%%b]!|findstr /lic:"bit_rate" >nul && echo !val[%%b]! > "%outputpath%\%%~nxf.bitrate" 2>&1


)