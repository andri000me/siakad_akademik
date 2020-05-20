<?php
error_reporting(0);
  session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";

function getOnlineStaff(){
	$idleTime = 1800; // idle time in seconds
	
	$time = time()-$idleTime;
	$s = "delete from session where sessionTime <= $time or user=''";
	$q = _query($s);
	
	$s = "select COUNT(s.sessionId) as _onlineusr from session s,karyawan k where s.sessionTime > $time and k.Login = s.user and s.LevelID not in ('100','120') and s.sessionId != '".$_SESSION['_Session']."' and user != '".$_SESSION['_Login']."' ";
	$q = _query($s);
	$w = _fetch_array($q);
	
	return $w['_onlineusr'];
}

function getOnlineMhs(){
	$idleTime = 600; // idle time in seconds
	
	$time = time()-$idleTime;
	$s = "delete from session where sessionTime <= $time or user='' and LevelID='120'";
	$q = _query($s);
	
	$s = "select distinct(s.user),COUNT(s.sessionId) as _onlineusr from session s,mhsw m where s.sessionTime > $time and s.user = m.MhswID and s.LevelID='120' and s.sessionId != '".$_SESSION['_Session']."' and user != '".$_SESSION['_Login']."'";
	$q = _query($s);
	$w = _fetch_array($q);
	
	return $w['_onlineusr'];
}
  
  function getOnlineDsn(){
	$idleTime = 1800; // idle time in seconds
	
	$time = time()-$idleTime;
	$s = "delete from session where sessionTime <= $time or user='' and LevelID='100'";
	$q = _query($s);
	
	$s = "select distinct(s.user),COUNT(s.sessionId) as _onlineusr from session s,dosen d where s.sessionTime > $time and s.user = d.Login and s.LevelID='100' and s.sessionId != '".$_SESSION['_Session']."' and user != '".$_SESSION['_Login']."'";
	$q = _query($s);
	$w = _fetch_array($q);
	
	return $w['_onlineusr'];
}
$onlineStaffNum = getOnlineStaff()+0;
$onlineMhswNum = getOnlineMhs()+0;
$onlineDsnNum = getOnlineDsn()+0;

echo "Staff (<b>$onlineStaffNum</b>)  Dosen (<b>$onlineDsnNum</b>) Mhsw(<b>$onlineMhswNum</b>)";
?>