<?php

// ===========================================
// Created on 24 Juli 2016 by Arisal Yanuarafi
// ===========================================
error_reporting(0);

include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=kliring-nilai.xls");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");

// *** Parameters ***
$_kliringProdi = GetSetVar('_kliringProdi');
$_kliringMhswID = GetSetVar('_kliringMhswID');
$_kliringKurikulum = GetSetVar('_kliringKurikulum');

$mhsw = GetFields("mhsw m
      left outer join dosen d on m.PenasehatAkademik = d.Login and d.KodeID='".KodeID."'
      left outer join prodi prd on prd.ProdiID = m.ProdiID and prd.KodeID='".KodeID."'
      left outer join program prg on prg.ProgramID = m.ProgramID and prg.KodeID='".KodeID."'",
    "m.KodeID='".KodeID."' and m.ProdiID='$_SESSION[_kliringProdi]' and m.MhswID", $_kliringMhswID,
      "m.*, prd.Nama as _PRD, prg.Nama as _PRG,
      d.Nama as DSN, d.Gelar");
if ($_SESSION['_LevelID']==100 && $mhsw['PenasehatAkademik']!=$_SESSION['_Login']) die('Maaf, data yang dimaksud tidak tersedia');

// *** Main ***
TampilkanHeader($mhsw);
RandomStringScript();
// validasi
if (!empty($_kliringProdi) && !empty($_kliringMhswID)) {
  $gos = (empty($_REQUEST['gos']))? 'PilihKurikulum' : $_REQUEST['gos'];
  $gos($mhsw);
}

// *** Functions ***
function TampilkanHeader($w) {
  $optprodi = ($_SESSION['_LevelID'] == 100)?
   			GetProdiUser2($_SESSION['_Login'], $_SESSION['_kliringProdi']) : 
   			GetProdiUser($_SESSION['_Login'], $_SESSION['_kliringProdi']);
  
  echo <<<SCR
  <table class=box cellspacing=1 align=center width=960> 
  <tr><td class=wrn width=2 rowspan=4></td>
      <td class=inp>NPM:</td>
      <td class=ul1>'$_SESSION[_kliringMhswID]
      </tr>
  <tr><td class=inp>Nama:</td>
      <td class=ul1 colspan=3><b>$w[Nama]</b></td>
      </tr>
  <tr><td class=inp>Program Studi:</td>
      <td class=ul>$w[_PRD] <sup>$w[ProdiID]</sup>&nbsp;</td>
      <td class=inp>Prg. Pendidikan:</td>
      <td class=ul>$w[_PRG] <sup>$w[ProgramID]</sup>&nbsp;</td>
      </tr>
  <tr><td class=inp>Penasehat Akd:</td>
      <td class=ul><b>$w[DSN] <sup>$w[Gelar]</sup>&nbsp;</b></td>
      <td class=inp>Masa Studi:</td>
      <td class=ul>$w[TahunID] &#8594; $w[BatasStudi]</td>
      </tr>
  </table>
SCR;
}
function PilihKurikulum($w){
   if (!empty($_SESSION['_kliringKurikulum'])){
    TampilkanKliring($w);
  }
}
function TampilkanKliring($mhsw){
$s = "SELECT m.MKKode,m.Nama,k.GradeNilai,m.SKS,m.MKSetara,m.MKID from mk m left outer join krs k on k.MKKode = m.MKKode and k.NA='N' AND  k.Final='Y' and k.Tinggi='*' and k.BobotNilai > 0 and k.MhswID = '$_SESSION[_kliringMhswID]'
            where m.KurikulumID='$_SESSION[_kliringKurikulum]' and m.ProdiID='$_SESSION[_kliringProdi]' and m.NA='N' order by Sesi,MKKode";
$r = _query($s);
  ?>
<table width="100%" border=1>
<tr>
<th class='ttl' colspan=5>Matakuliah Lama</th>
<th class='ttl' colspan=4>Matakuliah Baru</th>
</tr>
<tr>
<th class='ttl' width="3">No.</th>
<th class='ttl' width="90">Kode MK</th>
<th class='ttl'>Nama</th>
<th class='ttl' width="10">SKS</th>
<th class='ttl' width="3">NL</th>
<th class='ttl' width="90">Kode MK</th>
<th class='ttl' width="380">Nama</th>
<th class='ttl' width="10">SKS</th>
<th class='ttl' width="24">NL</th>
</tr>
<?php $n=0;
  while ($w = _fetch_array($r)){
    $n++;
    $table .= "<td>$n</td>";
    $set = TRIM($w['MKSetara'],".");
    $_MKKode= '';$_NamaMK ='';$_SKS = '';$_Nilai = '';$Nilai='';
    if(!empty($set)){
      $p = array();
      $p = explode(".", $set);
      $_setara = '';
      foreach ($p as $a) {
        $_setara .= "'$a',";
      }
      $_setara = substr($_setara, 0, -1);
      $s1 = "SELECT m.MKKode,m.Nama,k.GradeNilai,m.SKS,k.BobotNilai,k.KRSID from mk m left outer join krs k on k.MKKode = m.MKKode and k.NA='N' AND  k.Final='Y' and k.Tinggi='*' and k.BobotNilai > 0 and k.MhswID = '$_SESSION[_kliringMhswID]'
            where m.MKKode in ($_setara) and m.KurikulumID!='$_SESSION[_kliringKurikulum]' and m.ProdiID='$_SESSION[_kliringProdi]' order by k.BobotNilai";
      //echo $s1;die();
      $r1 = _query($s1);$nn=0;
      while ($w1 = _fetch_array($r1)){
        $nn++;
        $_MKKode .= ($nn > 0) ? "&raquo; $w1[MKKode] <br>":"&raquo; $w1[MKKode]";
        $_NamaMK .= ($nn > 0 ? "&raquo; $w1[Nama] <br>":"&raquo; $w1[Nama]");
        $_SKS .= ($nn > 0 ? "$w1[SKS] <br>":"$w1[SKS]");
        $_Nilai .= ($nn > 0 ? "$w1[GradeNilai] <br>":"$w1[GradeNilai]");
        $NilaiSetara = $w1['BobotNilai']; // Nilai dari matakuliah sebelumnya
        $KRSID = $w1['KRSID'];
      }
        $table .= "<td>$_MKKode</td>";
        $table .= "<td>$_NamaMK</td>";
        $table .= "<td>$_SKS</td>";
        $table .= "<td>$_Nilai</td>";
    }else{ // Bila tidak ada matakuliah setara
        $table .= "<td>-</td>";
        $table .= "<td>-</td>";
        $table .= "<td>-</td>";
        $table .= "<td>-</td>";
    }
    $NilaiMK = $w1['BobotNilai']; // Nilai matakuliah baru yang diambil (kurikulum yg dimaksud).
    $BobotNilai = ($NilaiMK > $NilaiSetara) ? $NilaiMK : $NilaiSetara; // Cari Nilai yg terbaik
    $GradeSekarang = (empty($w['GradeNilai']) ? "-":$w['GradeNilai']);

        // MK Baru
        $table .= "<td>$w[MKKode]</td>";
        $table .= "<td>$w[Nama]</td>";
        $table .= "<td>$w[SKS]</td>";
        $table .= "<td><b>$GradeSekarang</b></td></tr>";
        $BobotNilai='';$NilaiMK='';$NilaiSetara='';
  }
  echo $table;
  ?></table>
<?php } ?>