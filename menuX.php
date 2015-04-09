<?php session_start();
$destUrl=$_GET['src'];
print  ('<object data='.$destUrl);
print (' type="text/xhtml" width="100%" height="600px"></object><br>');
