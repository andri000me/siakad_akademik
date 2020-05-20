<?php
session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Cari Mahasiswa");

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$frm = GetSetVar('frm');
$div = GetSetVar('div');
$NamaMhsw = GetSetVar('NamaMhsw');
$TahunID = GetSetVar('TahunID');

// cek Nama Dosen dulu
if (empty($NamaMhsw))
  die(ErrorMsg('Error', 
    "Masukkan terlebih dahulu Nama Mahasiswa sebagai kata kunci pencarian.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <a href='#' onClick=\"javascript:toggleBox('$div', 0)\">Tutup</a>"));


$prd = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');

// *** Main ***
TampilkanJudul("Cari Mahasiswa - $prd <sup>($ProdiID)</sup><br /><font size=-1><a href='#' onClick=\"toggleBox('$div', 0)\">(&times; Close &times;)</a></font>");
TampilkanDaftar();

// *** Functions ***
function TampilkanDaftar() {
  
  /*
  $s = "select m.MhswID, m.Nama as NamaMhsw, m.TahunID, m.NA, m.Predikat
    from mhsw m	left outer join tugasakhir ta on ta.MhswID=m.MhswID
    where m.KodeID = '".KodeID."'
      and m.NA = 'N'
      and m.Nama like '%$_SESSION[NamaMhsw]%'
      and m.ProdiID = '$_SESSION[ProdiID]'
      and ta.MhswID=m.MhswID
      and ta.TahunID='$_SESSION[TahunID]'
      and NOT EXISTS(SELECT w.MhswID from wisudawan w where w.MhswID=m.MhswID)
    order by m.Nama"; // Mendaftarkan wisudawan baru bisa setelah ybs terdaftar di TA.
    */
  $s = "select m.MhswID, m.Nama as NamaMhsw, m.TahunID, m.NA, m.Predikat
    from mhsw m 
    where m.KodeID = '".KodeID."'
      and m.NA = 'N'
      and m.Nama like '%$_SESSION[NamaMhsw]%'
      and m.ProdiID = '$_SESSION[ProdiID]'
      and NOT EXISTS(SELECT w.MhswID from wisudawan w where w.MhswID=m.MhswID)
    order by m.Nama"; // Mendaftarkan wisudawan baru bisa setelah ybs terdaftar di TA.
  $r = _query($s); $i = 0;
  
  echo "<table class=bsc cellspacing=1 width=100%>";
  echo "<tr>
    <th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama Mahasiswa</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $i++;
    if ($w['NA'] == 'Y') {
      $c = "class=nac";
      $d = "$w[Nama] <sup>$w[Gelar]</sup>";
    }
    else {
      $c = "class=ul";
	  /*$TA=GetFields("ta left outer join dosen d on d.Login=ta.Pembimbing and d.NA='N'
	  					left outer join dosen dd on dd.Login=ta.Pembimbing2 and dd.NA='N'", "MhswID", $w[MhswID],"ta.Judul,ta.TglUjian,concat(d.Gelar1,' ',d.Nama,', ',d.Gelar) as Pembimbing, concat(dd.Gelar1,' ',dd.Nama,', ',dd.Gelar) as Pembimbing2");*/
	$TA=GetFields("tugasakhir ta", "ta.MhswID", $w[MhswID],"ta.*");
		$judulTA=FixStr($TA['Judul']);
    $judulTA = str_replace("'", "\'", $judulTA);
    $judulTA = str_replace('"', "\'", $judulTA);
		$Predikat = (empty($w['Predikat']))? Predikat($w['MhswID']) : $w['Predikat'];

    // $_SESSION[frm].Predikat.value='$Predikat'; // Script ini dihilangkan sementara
    
      $d = "<a 	
					href=\"javascript:$_SESSION[frm].MhswID.value='$w[MhswID]';
				  $_SESSION[frm].TglSidang.value='$TA[TglUjian]';
          $_SESSION[frm].TglMulai.value='$TA[TglMulai]';
          $_SESSION[frm].TglDaftar.value='$TA[TglDaftar]';
          $_SESSION[frm].TglSelesai.value='$TA[TglSelesai]';
				  $_SESSION[frm].pembimbing.value='$TA[Pembimbing]';
				   $_SESSION[frm].pembimbing2.value='$TA[Pembimbing2]';
				   
				  $_SESSION[frm].Judul.value='$judulTA';
				  $_SESSION[frm].NamaMhsw.value='$w[NamaMhsw]';
				  toggleBox('$_SESSION[div]', 0);\">
        &raquo;
        $w[NamaMhsw]</a>
        <sup>$w[Gelar]</sup>";
    }
    echo <<<SCR
      <tr>
      <td class=inp width=20>$i</td>
      <td $c width=100 align=center>$w[MhswID]</td>
      <td $c>$d</td>
      <td class=ul width=20 align=center><img src='../img/book$w[NA].gif' /></td>
      </tr>
SCR;
  }
  echo "</table>";
}

?>


