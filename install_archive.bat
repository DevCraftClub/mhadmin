@echo off
mkdir temp
robocopy upload temp /E
cd temp
set PATH=%PATH%;%ProgramFiles%\7-Zip\
7z a -t7z -m0=lzma -mx=9 -mfb=64 -md=32m -ms=on mhadmin.zip *
cd ..
copy /Y temp\mhadmin.zip install.zip
rd /s /q temp
exit;
