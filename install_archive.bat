@echo off
mkdir temp
robocopy upload temp /E
cd temp
set PATH=%PATH%;%ProgramFiles%\7-Zip\
7z a -mx0 -r -tzip -aoa mhadmin.zip *
cd ..
copy /Y temp\mhadmin.zip install.zip
rd /s /q temp
exit;
