<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Cari Mahasiswa");

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$frm = GetSetVar('frm');
$div = GetSetVar('div');
$NamaMhsw = GetSetVar('NamaMhsw');

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
  /*$s = "select m.MhswID, m.Nama as NamaMhsw, m.TahunID, m.NA
    from mhsw m
    where m.KodeID = '".KodeID."'
      and m.NA = 'N'
      and m.Nama like '%$_SESSION[NamaMhsw]%'
      and m.ProdiID = '$_SESSION[ProdiID]'
    order by m.Nama";*/
	$filter_prodi = (empty($_SESSION['ProdiID']))? "" : "and m.ProdiID='$_SESSION[ProdiID]'";
	$filter_tahun = (empty($_SESSION['TahunID']))? "" : "and k.TahunID='$_SESSION[TahunID]'";
	
  $s = "select DISTINCT(m.MhswID), m.Nama as NamaMhsw, m.TahunID, m.NA, 
			pk.NamaPerusahaan, pk.AlamatPerusahaan, pk.KotaPerusahaan, pk.TeleponPerusahaan, 
			pk.NamaPekerjaan, pk.Deskripsi
		from krs k 
			left outer join mk mk on mk.MKID=k.MKID
			left outer join mhsw m on m.MhswID=k.MhswID
			left outer join praktekkerja pk on pk.MhswID=k.MhswID and pk.Final='Y' and pk.TahunID=(select MAX(TahunID) from praktekkerja pk2 where pk2.MhswID=k.MhswID and pk2.TahunID < '$_SESSION[TahunID]' and pk.KodeID='".KodeID."')
			left outer join dosen d on pk.Pembimbing=d.Login and d.KodeID='".KodeID."'
		where m.KodeID = '".KodeID."'
			and m.NA = 'N'
			and m.Nama like '%$_SESSION[NamaMhsw]%'
			$filter_prodi
			$filter_tahun
			and mk.Komprehensif='Y'";
  $r = _query($s); $i = 0;
  
  echo "<table class=bsc cellspacing=1 width=100%>";
  echo "<tr>
    <th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama Mahasiswa</th>
    <th class=ttl>Sudah<br>Bayar?</th>
	<th class=ttl>Nama Perusahaan<br> Sebelumnya</th>
	<th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $i++;
    // Cek Bila Sudah Bayar Biaya Praktek Kerja atau belum
	$ss = "select bm.*
			from bipotmhsw bm
				left outer join bipot2 b2 on bm.BIPOT2ID=b2.BIPOT2ID
			where bm.MhswID='$w[MhswID]' 
				and bm.TahunID='$_SESSION[TahunID]' 
				and b2.PraktekKerja = 'Y'
				and bm.KodeID='".KodeID."'
				and bm.NA = 'N'";
	$rr = _query($ss);
	
	// Cek Bila ada record biaya mahasiswa tentang Praktek Kerja
	if(_num_rows($rr) > 0)
	{	// Bila ada, cek apakah biaya tersebut sudah dibayarkan
		$Sisa = 0;
		while($ww = _fetch_array($rr))
		{	$Sisa += ($ww['Jumlah'] * $ww['Besar']) - $ww['Dibayar'];
		}
		if($Sisa > 0) $Bayar = 'N'; 
		else $Bayar = 'Y';
	}
	else 
	{	// Bila tidak ada, cek apa ada biaya yang seharusnya dikenakan (dengan kata lain: belum proses bipot)
		$sss = "select b2.* 
					from bipot2 b2
					where b2.PraktekKerja = 'Y'";
		$rrr = _query($sss);
		if(_num_rows($rrr)> 0) $Bayar = 'N';
		else $Bayar = 'Y';
	}
	
	// Apa Mahasiswa inactive
	if ($w['NA'] == 'Y') {
      $c = "class=nac";
      $d = "$w[Nama] <sup>$w[Gelar]</sup>";
	}
    else {
      $c = "class=ul";
      
	  if($Bayar == 'Y')
		$link = "<a href=\"javascript:$_SESSION[frm].MhswID.value='$w[MhswID]';$_SESSION[frm].NamaMhsw.value='$w[NamaMhsw]';toggleBox('$_SESSION[div]', 0)\">";
	  
	  $clink = (!empty($link))? "</a>" : "";
	  
	  $d = "
        &raquo;
		$link
        $w[NamaMhsw]
		$clink
        <sup>$w[Gelar]</sup>";
	  
	  if($Bayar == 'Y')
	  { if(!empty($w['NamaPerusahaan']))
		{
			$link2 ="<a href=\"javascript:$_SESSION[frm].MhswID.value='$w[MhswID]';
				   $_SESSION[frm].NamaMhsw.value='$w[NamaMhsw]';
				   $_SESSION[frm].NamaPerusahaan.value='$w[NamaPerusahaan]';
				   $_SESSION[frm].AlamatPerusahaan.value='$w[AlamatPerusahaan]';
				   $_SESSION[frm].KotaPerusahaan.value='$w[KotaPerusahaan]';
				   $_SESSION[frm].TeleponPerusahaan.value='$w[TeleponPerusahaan]';
				   $_SESSION[frm].NamaPekerjaan.value='$w[NamaPekerjaan]';
				   $_SESSION[frm].Deskripsi.value='$w[Deskripsi]';
				   toggleBox('$_SESSION[div]', 0)\">"; 
		  $clink2 = (!empty($link2))? "</a>" : "";
		  $e = "$link2
				$w[NamaPerusahaan]
				$clink2";
		}
		else $e = ' - ';
	  }
	  else $e = ' - ';
    }
	
	echo <<<SCR
      <tr>
      <td class=inp width=20>$i</td>
      <td $c width=100 align=center>$w[MhswID]</td>
      <td $c>$d</td>
	  <td class=ul width=20 align=center><img src='../img/$Bayar.gif' /></td>
	  <td $c align=center>$e</td>
      <td class=ul width=20 align=center><img src='../img/book$w[NA].gif' /></td>
      </tr>
SCR;
  }
  echo "</table>";
}

?>


</BODY>
</HTML>
