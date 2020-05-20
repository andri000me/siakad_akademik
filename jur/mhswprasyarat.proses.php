<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Aktivitas Dosen");

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$MKID = GetSetVar('MKID');
$_praMKID = GetSetVar('_praMKID');
$_praMKKode = GetSetVar('_praMKKode');
$_praSKS = GetSetVar('_praSKS');
$_praKur = GetSetVar('_praKur');
$_praSesi = GetSetVar('_praSesi');
$_praSKSMin = GetSetVar('_praSKSMin');
$_praIPKMin = GetSetVar('_praIPKMin');
$_praPrasyarat = GetSetVar('_praPrasyarat');
$_praFile = GetSetVar('_praFile');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Proses' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Proses() {
  $_max = 100;
  $_praPrc = GetSetVar('_praPrc');
  $_praCnt = GetSetVar('_praCnt');
  $_dari = $_praPrc * $_max;
  
  $s = "select m.MhswID, m.Nama
    from mhsw m
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$_SESSION[ProdiID]'
      and m.StatusMhswID = 'A'
    order by m.MhswID
    limit $_dari, $_max";
  //die($s);
  $r = _query($s);
  
  $jml = _num_rows($r);
  if ($jml > 0) {
    while ($w = _fetch_array($r)) {
      $_SESSION['_praCnt']++;
      // Proses satu per satu
      $MhswID = $w['MhswID'];
      $Nama = $w['Nama'];
      $oke = true; $psn = '';
      // Apakah ada SKS Minimalnya?
      $oke = CheckSKSMin($MhswID, $psn);
      
      // Apakah ada IP Minimalnya?
      if ($oke) {
        $oke = CheckIPMin($MhswID, $psn);
      }

      // Apakah ada MK Prasyaratnya?
      if ($oke) {
        $oke = CheckPrasyarat($MhswID, $psn);
      }
      echo <<<ESD
      <script>
      self.parent.Progresnya($_SESSION[_praCnt], '$MhswID', '$Nama', '$psn');
      </script>
ESD;
      // Jika memenuhi syarat
      if ($oke == true) {
        $f = fopen("../".$_SESSION['_praFile'].".txt", 'a');
        fwrite($f, "$MhswID|$Nama|Oke\r\n");
        fclose($f);
      }
      else { // Jika tidak memenuhi syarat
        $f = fopen("../".$_SESSION['_praFile']."_gagal.txt", 'a');
        fwrite($f, "$MhswID|$Nama|$psn\r\n");
        fclose($f);
      }
    }
    
    // Nex Process
    $_SESSION['_praPrc']++;
    $time = 10;
    echo <<<ESD
    <script>
    <!--
    //window.setTimeout("location='$_SESSION[mnux].proses.php?gos=Proses&_praPrc=$_praPrc&_praCnt=$_SESSION[_praCnt]'", $time);
    window.setTimeout("location='../$_SESSION[mnux].proses.php'", $time);
    //-->
    </script>
ESD;
  }
  else {
    echo "
    <script>
    self.parent.Selesai();
    </script>
    ";
  }
}
function CheckSKSMin($MhswID, &$psn) {
  $JmlSKS = GetaField('krs', "MhswID='$MhswID' and KodeID", KodeID, "sum(SKS)")+0;
  if ($JmlSKS >= $_SESSION['_praSKSMin']) {
    return TRUE;
  }
  else {
    $psn .= "SKS ($JmlSKS) tidak mencukupi. ";
    return FALSE;
  }
}
function CheckIPMin($MhswID, &$psn) {
  $IPK = GetaField('krs', "MhswID='$MhswID' and KodeID", KodeID,
    "sum(BobotNilai*SKS)/sum(SKS)")+0;
  if ($IPK >= $_SESSION['_praIPKMin']) {
    return TRUE;
  }
  else {
    $psn .= "IPK ($IPK) tidak mencukupi. ";
    return FALSE;
  }
}
function CheckPrasyarat($MhswID, &$psn) {
  $arr = explode(',', $_SESSION['_praPrasyarat']);
  $oke = true; $_p = '';
  foreach ($arr as $_pra) {
    $_pra = TRIM($_pra);
    $pra = explode(':', $_pra);
    $nilai = GetaField('krs', "MhswID='$MhswID' and MKKode", $pra[0], "BobotNilai")+0;
    if ($nilai > $pra[2]) {
    }
    else {
      $oke = false;
      $_p .= "Prasyarat $pra[0] tidak terpenuhi. ";
    }
  }
  $psn = $_p;
  return $oke;
  //$arrPrasyarat[] = $w['MKKode'].':'.$w['Nilai'].':'.$w['Bobot'];
}
?>
