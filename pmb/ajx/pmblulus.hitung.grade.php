<?php
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 11 Juni 2014  */
	
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";
	
	$PMBPeriodID = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
	
	$s = "SELECT p.PMBID,a.NilaiRapor,a.NilaiSekolah from pmb p left outer join aplikan a on a.PMBID=p.PMBID and a.AplikanID=p.AplikanID where p.PMBPeriodID = '$PMBPeriodID'";
	$r = _query($s);$T=0;
	while ($w = _fetch_array($r)) {
		$Rapor = (($w['NilaiRapor'] < 10) ? ($w['NilaiRapor']*10) : $w['NilaiRapor'])*0.6;
		$Sekolah = (($w['NilaiSekolah'] < 10) ? ($w['NilaiSekolah']*10) : $w['NilaiSekolah'])*0.4;
		if ($Sekolah == 0 && $Rapor > 0) {
			$Grade = (($w['NilaiRapor'] < 10) ? ($w['NilaiRapor']*10) : $w['NilaiRapor']);
		}
		elseif ($Sekolah > 0 && $Rapor == 0) {
			$Grade = (($w['NilaiSekolah'] < 10) ? ($w['NilaiSekolah']*10) : $w['NilaiSekolah']);
		}
		else $Grade = $Rapor + $Sekolah + 0;
		$update = _query("UPDATE pmb set NilaiUjian = '$Grade' where PMBID = '$w[PMBID]' and PMBPeriodID = '$PMBPeriodID' limit 1");
		if ($Grade >= 65) $T++;
	}
	echo "$T";