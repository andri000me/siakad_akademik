<?php
session_start();
include_once "../sisfokampus1.php";


HeaderSisfoKampus("Cari Ruang UTS");

// *** Parameters ***
$ProdiID = GetSetVar('ProdiID');
$frm = GetSetVar('frm');
$div = GetSetVar('div');
$UASRuangID = GetSetVar('UASRuangID');
$Ruangan = GetSetVar('UR');
$kapasitasR = GetSetVar('kR');
	$Tanggal = $_GET[Tanggal];
  	$Mulai = $_GET[Mulai];
  	$Selesai = $_GET[Selesai];
  	$Tahun = $_GET[Tahun];


// cek Ruangan dulu
if (empty($UASRuangID))
  die(ErrorMsg('Error', 
    "Masukkan terlebih dahulu Kode Ruang sebagai kata kunci pencarian.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <a href='#' onClick=\"javascript:toggleBox('$div', 0)\">Tutup</a>"));


$prd = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');

// *** Main ***
TampilkanJudul("Cari Ruang UTS- $prd <sup>($ProdiID)</sup><br /><font size=-1><a href='#' onClick=\"toggleBox('$div', 0)\">(&times; Close &times;)</a></font>");
TampilkanDaftar();

// *** Functions ***
function TampilkanDaftar() {
  $s = "select r.RuangID, r.Nama, r.KapasitasUjian, r.KampusID, r.KolomUjian
    from ruang r
    where r.KodeID = '".KodeID."'
      and r.RuangID like '%$_SESSION[UASRuangID]%'
      and r.NA = 'N'
      and INSTR(r.ProdiID, '.$_SESSION[ProdiID].') > 0
    order by r.KampusID, r.RuangID limit 5";
  $r = _query($s); $i = 0;
  $sisaKapasitas=($kapasitasR);
  echo "<table class=bsc cellspacing=1 width=100%>";
  echo "<tr>
    <th class=ttl>#</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Nama Ruang</th>
	<th class=ttl>Pilih</th>
    <th class=ttl width=60>Kapasitas</th>
	<th class=ttl>Terisi</th>
	<th class=ttl>MK / Kelas</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $i++;
	$w['BarisUjian'] = ($w['KolomUjian']!= 0)? ceil($w['KapasitasUjian']/$w['KolomUjian']) : $w['KapasitasUjian'];
	$w['KolomUjian'] = ($w['KolomUjian']!= 0)? $w['KolomUjian'] : 1;
	$s8="select left(jd.Nama,15) as NamaMK, kl.Nama as NMKelas, count(u.MhswID) as JmlMhsw, j.Kapasitas
    from jadwal jd, kelas kl, uasmhsw u, jadwaluas j
	  where 
      j.RuangID = '$w[RuangID]'
      and j.Tanggal = '$_GET[Tanggal]'
	  and j.TahunID = '$_GET[Tahun]'
	  and (('$_GET[Mulai]:00' <= j.JamMulai and j.JamMulai <= '$_GET[Selesai]:59')
      or  ('$_GET[Mulai]:00' <= j.JamSelesai and j.JamSelesai <= '$_GET[Selesai]:59'))
	  and u.JadwalUASID=j.JadwalUASID and jd.JadwalID=j.JadwalID and kl.KelasID=jd.NamaKelas
      ";
	$r8 = _query($s8);
	$w8= mysql_num_rows($r8);
	while ($w9 = _fetch_array($r8)) {
		if ($w9['JmlMhsw']>0) 
		{
			$JumlahMhsw=$w9['JmlMhsw'];
			echo "<tr bgcolor=yellow>";
			$KapasitasUjian=($w['KapasitasUjian'] - $w9['JmlMhsw'])+0;
			$NamaMK=$w9[NamaMK];
			$NMKelas=$w9[NMKelas];
		}
			else
		{
			echo "<tr>";
			$JumlahMhsw=$w9['JmlMhsw'];
			$KapasitasUjian=($w['KapasitasUjian'] - $w9['JmlMhsw'])+0;
			$NamaMK="-";
			$NMKelas=$w9[NMKelas];
		}
		}
	if ($KapasitasUjian>0) {
		echo <<<SCR
      
      <td class=inp width=20>$i </td>
      <td class=ul1 width=60>$w[RuangID]</td>
      <td class=ul1>
        <strong>$w[Nama]</strong></td>
				<td class=ul1 align=center>
        <a href="javascript:$_SESSION[frm].UASRuangID$_SESSION[UR].value='$w[RuangID]'; $_SESSION[frm].UASKapasitas$_SESSION[UR].value='$KapasitasUjian'; $_SESSION[frm].UASKolomUjian$_SESSION[UR].value='$w[KolomUjian]'; $_SESSION[frm].UASBarisUjian$_SESSION[UR].value='$w[BarisUjian]'; toggleBox('$_SESSION[div]', 0)"> &raquo;Pilih</a>
      </td>
      <td class=ul1 align=center><strong>$KapasitasUjian</strong></td>
	   <td class=ul1 align=center>$JumlahMhsw</td>
	   <td class=ul1 align=center>$NamaMK<br>$NMKelas</td>
      </tr>
SCR;
}
  }
  echo "</table>";
}

?>

</BODY>
</HTML>
