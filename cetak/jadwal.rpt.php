<?php
// Author: Emanuel Setio Dewo
// 26 May 2006
// http://www.sisfokampus.net
session_start();
include "../sisfokampus.php";
include_once "jadwal.lib.php";
include_once "jadwal.prn.php";
$gos = $_REQUEST['gos'];
if (!empty($gos)) $gos();
?>
