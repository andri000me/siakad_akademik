<?php
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 11 Juni 2014  */
	
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";
	
	$PMBID = $_GET['PMBID'];
	$Status = $_GET['Status'];
	$AplikanID = GetaField('pmb', "PMBID='$PMBID' and KodeID", KodeID, 'AplikanID');
	if (!empty($Status)) {
		$LulusUjian = ($Status == 'N' ? "N":"Y");
		if ($LulusUjian =='Y') 	
		{		$ProdiID = $Status;
		
				$s = "update pmb
				set ProdiID = '$ProdiID',
				LulusUjian = '$LulusUjian',
				LoginEdit = '$_SESSION[_Login]',
				TanggalEdit = now()
			where KodeID = '".KodeID."' and PMBID = '$PMBID' ";
		  $r = _query($s);
	
	include_once "../statusaplikan.lib.php";
	$check = GetaField('statusaplikanmhsw', "StatusAplikanID = 'LLS' and AplikanID='$AplikanID' and KodeID", KodeID, "AplikanID");
	if (empty($check)) {
  SetStatusAplikan('LLS', $check, GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID"));
	}
	$thn = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
	$target = GetaField('pmbtarget', "PMBPeriodID = '$thn' and ProdiID", $Status, "Target");
	$Lulus = GetaField('pmb lulus', "lulus.ProdiID='$Status' and lulus.PMBPeriodID='$thn' and lulus.LulusUjian", 'Y',"count(DISTINCT(lulus.PMBID))");
	$sisa = $target - $Lulus + 0;
  echo "<span class='label label-success'>Lulus ! Quota: $sisa</span>";
		}
		else 
		{
			echo "<span class='label'>Tidak Lulus !</span>";
			$s = "update pmb
					set ProdiID = '$ProdiID',
					LulusUjian = '$LulusUjian',
					LoginEdit = '$_SESSION[_Login]',
					TanggalEdit = now()
				where KodeID = '".KodeID."' and PMBID = '$PMBID' ";
			  $r = _query($s);
		}
	}