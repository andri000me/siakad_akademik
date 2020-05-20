<?php
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 11 Juni 2014  */
	if ($_SESSION['_LevelID'] != 1) die();
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";
	
	$ProdiID = $_GET['ProdiID'];
	$Status = $_GET['stat'];
	
	$thn = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
	$target = GetaField('pmbtarget', "PMBPeriodID = '$thn' and ProdiID", $ProdiID, "Target");
	$Lulus = GetaField('pmb lulus', "lulus.ProdiID='$ProdiID' and lulus.PMBPeriodID='$thn' and lulus.LulusUjian", 'Y',"count(DISTINCT(lulus.PMBID))");
	$sisa = $target - $Lulus + 0;
	
	$limit = ($Status == '1') ? "" : " limit $sisa";
	$s = "SELECT PMBID,MhswID,AplikanID,Nama,LulusUjian,NilaiUjian,
				Pilihan1, Pilihan2  from 
			pmb 
			where PMBPeriodID='$thn' and Pilihan1 = '$ProdiID' and ProdiID='' 
			group by PMBID order by NilaiUjian DESC $limit";
	$r = _query($s);$n=0;
	while ($w = _fetch_array($r)) {
	$AplikanID = GetaField('pmb', "PMBID='$w[PMBID]' and KodeID", KodeID, 'AplikanID');
			
				$s1 = "update pmb
				set ProdiID = '$ProdiID',
				LulusUjian = 'Y',
				LoginEdit = '$_SESSION[_Login]',
				TanggalEdit = now()
			where KodeID = '".KodeID."' and PMBID = '$w[PMBID]' ";
		  $r1 = _query($s1);
	
	include_once "../statusaplikan.lib.php";
	$check = GetaField('statusaplikanmhsw', "StatusAplikanID = 'LLS' and AplikanID='$AplikanID' and KodeID", KodeID, "AplikanID");
	if (empty($check)) {
  SetStatusAplikan('LLS', $check, GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID"));
	}
	$n++;
	
} // end while
echo $n;