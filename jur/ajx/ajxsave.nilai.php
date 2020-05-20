<?php session_start(); 
	include_once "../../dwo.lib.php";
  	include_once "../../db.mysql.php";
  	include_once "../../connectdb.php";
  	include_once "../../parameter.php";
  	include_once "../../cekparam.php";
if ($_SESSION['_LevelID']==42 || $_SESSION['_LevelID']==100 || $_SESSION['_LevelID']==1 || $_SESSION['_LevelID']==20) {
	if (sqling($_GET['whr1']) > 0) {
		$s = "UPDATE krs set ".sqling($_GET['field'])." = '".sqling($_GET['data'])."', LoginEdit='$_SESSION[_Login]', TanggalEdit=now() where KRSID='".sqling($_GET['whr1'])."' and MhswID='".sqling($_GET['whr2'])."'";
		$r = _query($s);
	}
}
$krs = GetFields('krs',"KRSID", sqling($_GET['whr1']), "*");
HitungNilai($krs);

function HitungNilai($krs) {
//function HitungNilai1($jadwalid, $jdwl) {
  $jadwalid = $krs['JadwalID'];
  $jdwl = GetFields('jadwal', 'JadwalID', $jadwalid, '*');
  // lihat persentase Tugas Mandiri
  if ($jdwl['TugasMandiri'] > 0) {
    // Ambil jumlah tugas2 utk distribusi nilai tugas
    $TGS = GetFields('krs', "KRSID='".$krs['KRSID']."' and JadwalID", $jadwalid,
      "sum(Tugas1) as T1, sum(Tugas2) as T2, sum(Tugas3) as T3, sum(Tugas4) as T4, sum(Tugas5) as T5");
    $_T1 = ($TGS['T1'] > 0)? 1 : 0;
    $_T2 = ($TGS['T2'] > 0)? 1 : 0;
    $_T3 = ($TGS['T3'] > 0)? 1 : 0;
    $_T4 = ($TGS['T4'] > 0)? 1 : 0;
    $_T5 = ($TGS['T5'] > 0)? 1 : 0;
    $JumlahTugas = $_T1 + $_T2 + $_T3 + $_T4 + $_T5;
    // Distribusikan persentase tugas
    $PersenTugas = $jdwl['TugasMandiri'] / $JumlahTugas;
    $SisaTugas = $jdwl['TugasMandiri'] % $JumlahTugas;
    $_fld = array();
    for ($i = 1; $i <= 5; $i++) {
      $fld = "_T$i";
      $_PersenTugas = ($$fld == 1)? $PersenTugas : 0;
      $jdwl["Tugas$i"] = $_PersenTugas;
      //$persen = ($i == 1)? $PersenTugas + $SisaTugas : $PersenTugas;
      $_fld[] = "Tugas$i=$_PersenTugas";
    }
    $fld = implode(', ', $_fld);
    $s0 = "update jadwal set $fld where JadwalID=$jadwalid";
    $r0 = _query($s0);
  }
  // Proses
  $s = "select * from krs where JadwalID=$jadwalid and KRSID='$krs[KRSID]' order by MhswID";
  $r = _query($s);
  $countPresensi = GetaField('presensi', 'JadwalID', $jadwalid, 'count(PresensiID)');
  while ($w = _fetch_array($r)) {
	$Presensi = ($countPresensi == 0)? 0 : $w['_Presensi']/$countPresensi*100;
    $nilai = ($w['Tugas1'] * $jdwl['Tugas1']) +
      ($w['Tugas2'] * $jdwl['Tugas2']) +
      ($w['Tugas3'] * $jdwl['Tugas3']) +
      ($w['Tugas4'] * $jdwl['Tugas4']) +
      ($w['Tugas5'] * $jdwl['Tugas5']) +
      ($Presensi * $jdwl['Presensi']) +
      ($w['UTS'] * $jdwl['UTS']) +
      ($w['UAS'] * $jdwl['UAS'])
      ;
    $nilai = ($nilai / 100) +0;
    if ($jdwl['Responsi'] > 0) {
      $nilai = ($nilai * (100 - $jdwl['Responsi'])/100) +
        ($w['Responsi'] * ($jdwl['Responsi'])/100);
    }
    $ProdiID = GetaField('mhsw', "MhswID", $w['MhswID'], "ProdiID");
	$bulat = round($nilai);

    // Hanya bila nilai Presensi, Tugas, UTS dan UAS Besar dari Nol
    $Tugas = $w['Tugas1'] + $w['Tugas2'] + $w['Tugas3'] + $w['Tugas4'] + $w['Tugas5'];
    if($Presensi>0 && $Tugas >0 && $w['UTS']>0 && $w['UAS']>0){
    $arrgrade = GetFields('nilai', 
      "KodeID='$_SESSION[KodeID]' and NilaiMin <= $bulat and $bulat <= NilaiMax and ProdiID",
      $ProdiID, "Nama, Bobot");
    // Simpan
    $s1 = "update krs set NilaiAkhir='$nilai', GradeNilai='$arrgrade[Nama]', BobotNilai='$arrgrade[Bobot]'
      where KRSID=$w[KRSID]";
    $r1 = _query($s1);
  } // END hanya bila nilai presensi, tugas, uts, uas besar dari nol

    // Bila tidak sama dengan nilai lama, maka disimpan di histori perbaikan nilai.
    if ($krs['GradeNilai']!=$arrgrade['Nama']) {
      $s1 = "INSERT INTO koreksinilai(Tanggal, TahunID, 
                  KRSID, MhswID, MKID, GradeLama, GradeNilai, Pejabat, Jabatan, 
                  Modul, LoginBuat, TglBuat)
            values
                  (now(), '$krs[TahunID]', 
                  '$w[KRSID]', '$w[MhswID]', '$jdwl[MKID]', '$krs[GradeNilai]', '$arrgrade[Nama]', '$_SESSION[_Nama]', 'Dosen', 
                  'KoreksiNilai-Dosen', '$_SESSION[_Login]', now())";
      $r1 = _query($s1);
    }
    
	echo number_format($nilai,2);;
  }
}


