@echo off
mkdir temp
robocopy upload temp /E
cd temp
set PATH=%PATH%;%ProgramFiles%\7-Zip\
7z a -mx10 -r -tzip -aoa temp.zip *
cd ..
copy /Y temp\temp.zip install.zip
rd /s /q temp
exit;
