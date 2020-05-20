<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
  if ($_SESSION['_LevelID']==60 || $_SESSION['_Login']=='auth0rized') $MhswID = sqling($_POST['MhswID']);
  $TahunID = sqling($_POST['TahunID']);
  $ProgramID = sqling($_POST['ProgramID']);
  $MaxSKS = 15;
  $ThnTinggi = GetaField('tahun',"TahunID != 'Tran-Manua' AND KodeID",KodeID,'max(TahunID)');
  // cek data session dan cek apakah pernah mengambil khs;
  if (!empty($_SESSION['_Login'])) {
  $ada = _num_rows(_query("SELECT KHSID,KodeID,TahunID,MhswID from khs  where KodeID='".KodeID."' and TahunID='".$TahunID."' and MhswID='".$MhswID."'"));
  if ($ada > 0) {
    echo ErrorMsg("Error",
      "Mahasiswa <b>$MhswID</b> sudah terdaftar utk Tahun <b>$TahunID</b>.<br />
      Silakan mengecek data mahasiswa, mungkin ada kesalahan.
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
  }
  else {
    $mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $MhswID,
      "Nama, ProgramID, ProdiID, BIPOTID, StatusMhswID");
    // Ambil semester terakhir mhsw
    $_sesiakhir = GetaField('khs', "KodeID='".KodeID."' and MhswID", $MhswID,
      "max(Sesi)");
	  
    if ($_sesiakhir > 0 and $_sesiakhir <= 1) {
				//mulai
			$bentrok = GetaField('khs',"MhswID='$MhswID' AND Sesi",$_sesiakhir,'COUNT(Sesi)');
			if ($bentrok > 1) {
				$s = "select KHSID,Sesi From khs where MhswID='$MhswID' AND Sesi='$_sesiakhir' order by KHSID DESC limit 1";
				$r = _query($s);
				while ($palingBaru = _fetch_array($r)) {
				$sesibaru = $palingBaru['Sesi']+1;
				$upd = "UPDATE khs set Sesi='$sesibaru' where KHSID='$palingBaru[KHSID]'";
				$update = _query($upd);
				$_sesiakhir = GetaField('khs', "KodeID='".KodeID."' and MhswID", $MhswID,
      									"max(Sesi)");
				}
			}
	// end;
	      $Sesi = $_sesiakhir+1;
     $MaxSKS = GetaField('prodi', "KodeID='".KodeID."' and ProdiID",
        $mhsw['ProdiID'], 'DefSKS');
    }
	if ($_sesiakhir > 1) {
			$bentrok = GetaField('khs',"MhswID='$MhswID' AND Sesi",$_sesiakhir,'COUNT(Sesi)');
			if ($bentrok > 1) {
				$s = "select KHSID,Sesi From khs where MhswID='$MhswID' AND Sesi='$_sesiakhir' order by KHSID DESC limit 1";
				$r = _query($s);
				while ($palingBaru = _fetch_array($r)) {
				$sesibaru = $palingBaru['Sesi']+1;
				$upd = "UPDATE khs set Sesi='$sesibaru' where KHSID='$palingBaru[KHSID]'";
				$update = _query($upd);
				$_sesiakhir = GetaField('khs', "KodeID='".KodeID."' and MhswID", $MhswID,
      									"max(Sesi)");
				}
			}
			$_genapganjil=$_sesiakhir-1;
	      $_khs = GetFields('khs', "KodeID='".KodeID."' and MhswID='$MhswID' and Sesi", 
        $_genapganjil, '*');
      $Sesi = $_sesiakhir+1;
      $IP = $khs['IPS'];
    // IP Semester

    // Maksimum SKS
    $IP = ($IP <= 0 ? 2.7:$IP);
      $MaxSKS = GetaField('maxsks', "KodeID='".KodeID."' 
        and DariIP <= $IP and $IP <= SampaiIP
        and ProdiID", $mhsw['ProdiID'], 'SKS')+0;
    }
	else {
      $Sesi = 1;
      $MaxSKS = GetaField('prodi', "KodeID='".KodeID."' and ProdiID",
        $mhsw['ProdiID'], 'DefSKS');
    }
    //$StatusMhswID = GetaField('statusmhsw', 'Def', 'Y', 'StatusMhswID');
    //$StatusMhswID = (empty($StatusMhswID))? 'A' : $StatusMhswID;
	$StatusMhswID = $mhsw['StatusMhswID'];
	
    // Simpan
    $s = "insert into khs
      (TahunID, KodeID, ProgramID, ProdiID, 
      MhswID, StatusMhswID,
      Sesi, IP, MaxSKS,
      LoginBuat, TanggalBuat, NA, SetujuPA, KonfirmasiKRS)
      values
      ('$TahunID', '".KodeID."', '$ProgramID', '$mhsw[ProdiID]',
      '$MhswID', 'A',
      '$Sesi', 0, '$MaxSKS',
      '$_SESSION[_Login]', now(), 'N','', 'N')";
    $r = _query($s);
    $s = "UPDATE mhsw set StatusMhswID='A',
          ProgramID='$ProgramID' where MhswID='$MhswID'";
    $r = _query($s);
    header('location:../../?mnux='.$_SESSION['mnux']);
  
}
}
else  header('location:../../?mnux='.$_SESSION['mnux']);
?>