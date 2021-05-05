@echo off
set copyto=%2
set copyfile=%1
echo %copyfile% %copyto% >> copyprocess.txt
move %copyfile% %copyto%
