<?php

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Proses BIPOT Mhsw");

include_once "../keu/bayarmhsw.lib.php";

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Prosesnya' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Prosesnya() {
  $max = $_SESSION['_bptMax']+0;
  $max = ($max == 0)? 10 : $max;
  $page = $_SESSION['_bptPage']+0;
  
  $mulai = $max * $page;
  
  $s = "select h.*, m.Nama as NamaMhsw
    from khs h
      left outer join mhsw m on m.MhswID = h.MhswID and m.KodeID = '".KodeID."'
    where h.KodeID = '".KodeID."'
      and h.TahunID = '$_SESSION[TahunID]'
    order by h.MhswID
    limit $mulai, $max";
  $r = _query($s);
  $jml = _num_rows($r);
  if ($jml > 0) {
    while ($w = _fetch_array($r)) {
      $_SESSION['_bptCounter']++;
      $jml = ProsesBIPOT($w['MhswID'], $w['TahunID'])+0;
      $_jml = number_format($jml);
      echo "
      <script>
      parent.fnProgress($_SESSION[_bptCounter], '$w[MhswID]', '$w[NamaMhsw]', '$_jml');
      </script>";
    }
    $_SESSION['_bptPage']++;
    $tmr = 1;
    echo <<<ESD
    <script>
    window.onload=setTimeout("window.location='../$_SESSION[mnux].prc.php'", $tmr);
    </script>
ESD;
  }
  else {
    echo <<<ESD
    <script>
    parent.fnSelesai('$_SESSION[TahunID]', $_SESSION[_bptCounter]);
    </script>
ESD;
  }
}
function ProsesBIPOT($MhswID, $TahunID) {
  // Ambil data
  $mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $MhswID, "*");
  $khs = GetFields('khs', "KodeID = '".KodeID."' and TahunID = '$TahunID' and MhswID", $MhswID, "*");
  // Ambil BIPOT-nya
  $s = "select * 
    from bipot2 
    where BIPOTID = '$mhsw[BIPOTID]'
      and Otomatis = 'Y'
      and NA = 'N'
    order by TrxID, Prioritas";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $oke = true;
    // Apakah sesuai dengan status awalnya?
    $pos = strpos($w['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
    $oke = $oke && !($pos === false);

	// Apakah sesuai dengan status mahasiswanya?
    $pos = strpos($w['StatusMhswID'], ".".$khs['StatusMhswID'].".");
    $oke = $oke && !($pos === false);
	
    // Apakah grade-nya?
    if ($oke) {
      if ($w['GunakanGradeNilai'] == 'Y') {
        $pos = strpos($w['GradeNilai'], ".".$mhsw['GradeNilai'].".");
        $oke = $oke && !($pos === false);
      }
    }
	
	// Apakah Jumlah SKS Tahun ini mencukupi?
	if ($oke) {
	  if ($w['GunakanGradeIPK'] == 'Y') {
		if($khs['SKS'] < GetaField('gradeipk', "IPKMin <= $mhsw[IPK] and $mhsw[IPK] <= IPKMax and KodeID", KodeID, 'SKSMin')) $oke = false;
		else $oke = true;
	  }
	}
	
	// Apakah Grade IPK-nya OK?
	if ($oke) {
      if ($w['GunakanGradeIPK'] == 'Y') {
        $pos = strpos($w['GradeIPK'], ".".GetaField('gradeipk', "IPKMin <= $mhsw[IPK] and $mhsw[IPK] <= IPKMax and KodeID", KodeID, 'GradeIPK').".");
        $oke = $oke && !($pos === false);
      }
    }
    
	// Apakah dimulai pada sesi ini?
    if ($oke) {
      if ($w['MulaiSesi'] <= $khs['Sesi'] or $w['MulaiSesi'] == 0) $oke = true;
	  else $oke = false;
    }
	
    // Simpan data
    if ($oke) {
      // Cek, sudah ada atau belum? Kalau sudah, ambil ID-nya
      $ada = GetaField('bipotmhsw',
        "KodeID='".KodeID."' and MhswID = '$mhsw[MhswID]'
        and PMBMhswID = 1
        and TahunID='$khs[TahunID]' and BIPOT2ID",
        $w['BIPOT2ID'], "BIPOTMhswID") +0;
      // Cek apakah memakai script atau tidak?
      if ($w['GunakanScript'] == 'Y') BipotGunakanScript($mhsw, $khs, $w, $ada, 1);
      // Jika tidak perlu pakai script
      else {
        // Jika tidak ada duplikasi, maka akan di-insert. Tapi jika sudah ada, maka dicuekin aja.
        if ($ada == 0) {
          // Simpan
          $Nama = GetaField('bipotnama', 'BIPOTNamaID', $w['BIPOTNamaID'], 'Nama');
          $s1 = "insert into bipotmhsw
            (KodeID, COAID, PMBMhswID, MhswID, TahunID,
            BIPOT2ID, BIPOTNamaID, Nama, TrxID,
            Jumlah, Besar, Dibayar,
            Catatan, NA,
            LoginBuat, TanggalBuat)
            values
            ('".KodeID."', '$w[COAID]', 1, '$mhsw[MhswID]', '$khs[TahunID]',
            '$w[BIPOT2ID]', '$w[BIPOTNamaID]', '$Nama', '$w[TrxID]',
            1, '$w[Jumlah]', 0,
            'Auto', 'N',
            '$_SESSION[_Login]', now())";
          $r1 = _query($s1);
        }// end $ada=0
      } // end if $ada
    }   // end if $oke
  }     // end while
  $jml = HitungUlangBIPOTMhsw($MhswID, $TahunID);
  return $jml;
}
?>
