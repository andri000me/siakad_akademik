<?php
// Author : Irvandy Goutama
// Email  : irvandygoutama@gmail.com
// Start  : 10 Juli 2009

// *** Parameters ***
$tahunkalender = GetSetVar('tahunkalender', date('Y'));
$bulankalender = GetSetVar('bulankalender', date('m'));

// *** Main ***
TampilkanJudul("Kalender Tahunan");

$gos = (empty($_REQUEST['gos']))? 'Kalender' : $_REQUEST['gos'];
$gos($tahunkalender, $bulankalender);

function Kalender($tahun, $bulan)
{	$arrTahun = GetTahunArray($tahun-5, $tahun+5);
	$bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
	echo "<p><table class=bsc cellspacing=1 align=center>";
	echo "<td class=menuitem width=25 align=center><a href='?mnux=$_SESSION[mnux]&gos=&tahunkalender=".($tahun-10)."&bulankalender=$bulan'><<</a></td>";
	foreach ($arrTahun as $a) {
		$sel = ($tahun == $a)? 'class=menuaktif' : 'class=menuitem';
		echo "<td $sel width=60 align=center><a href='?mnux=$_SESSION[mnux]&gos=&tahunkalender=$a&bulankalender=$bulan'>$a</a></td>";
	}
	echo "<td class=menuitem width=25 align=center><a href='?mnux=$_SESSION[mnux]&gos=&tahunkalender=".($tahun+10)."&bulankalender=$bulan'>>></a></td>";
	echo "</table>";
	
	echo "<table class=bsc cellspacing=1 align=center>";
	$arrBulan = GetBulanArray();
	foreach ($arrBulan as $a) {
		$sel = (($bulan+0) == $a)? 'class=menuaktif' : 'class=menuitem';
		echo "<td $sel width=60 align=center><a href='?mnux=$_SESSION[mnux]&gos=&tahunkalender=$tahun&bulankalender=$a'>".UbahKeBulanIndonesia($a)."</a></td>";
	}
	echo "</table></p>";
	
	echo "<table class=bsc cellspacing=1 width=900>";
	echo "<td width=80%>
		  <table class=bsc cellspacing=1 border=1 align=left width=700>";
	echo "<tr><td colspan=8><input type=button name='TambahJenisLibur' value='Tambah Jenis Hari Libur' onClick=\"EditJenisLibur(1)\">
							<input type=button name='TambahLibur' value='Tambah Hari Libur' onClick=\"SetLibur('$tahun-$bulan-$ix', 1)\"></td></tr>";
	
    // Buat header list hari
	$arrNamaHari = GetNamaHariArray();
	echo "<tr>";
	foreach ($arrNamaHari as $a)
	{	echo "<td class=ul1 width=80 align=center><b>$a</b></td>";
	}
	echo "</tr>";
	
	$lastdayofmonth = date('t', strtotime($tahun.'-'.$bulan.'-01'));

	// Ambil semua tanggal yang memiliki hari libur
	$arrTanggalPenting = array();
	$s = "select hl.TanggalMulai, hl.TanggalSelesai, hl.Keterangan, jl.Warna, 
			RIGHT(hl.TanggalMulai, 2) as _StartDate, RIGHT(hl.TanggalSelesai, 2) as _EndDate, 
			SUBSTRING(hl.TanggalMulai, 6, 2) as _StartMonth, SUBSTRING(hl.TanggalSelesai, 6, 2) as _EndMonth, 
			hl.HariLiburID, hl.TidakAdaKuliah 
			from harilibur hl left outer join jenislibur jl on hl.JenisLiburID=jl.JenisLiburID and jl.KodeID='".KodeID."'
			where hl.KodeID='".KodeID."' and (LEFT(hl.TanggalMulai, 7) = '$tahun-$bulan' or LEFT(hl.TanggalSelesai, 7) = '$tahun-$bulan') 
			and hl.TanggalMulai <= hl.TanggalSelesai and hl.NA='N'";
	$r = _query($s);
     
	while($w = _fetch_array($r))
	{	if($w['_StartMonth'] != $bulan) $w['_StartDate'] = '01'; 
		if($w['_EndMonth'] != $bulan) $w['_EndDate'] = $lastdayofmonth; 
		
		for($d = $w['_StartDate']+0; $d <= $w['_EndDate']+0; $d++)
			$arrTanggalPenting[$d][] = implode('|', array($w['TanggalMulai'], $w['TanggalSelesai'], $w['Keterangan'], $w['Warna'], $w['TidakAdaKuliah'], $w['HariLiburID']));
	}
	
	// Buat isi dari bulan dan tahun yang dipilih
	echo "<tr>";
	for($i = 1; $i <= $lastdayofmonth; $i++)
	{	$ix = str_pad($i, 2, '0', STR_PAD_LEFT);
		$hari = date('w', strtotime($tahun.'-'.$bulan.'-'.$ix));
		
		if($i == 1)
		{	for($j = 0; $j < $hari; $j++)
			{	echo "<td bgcolor=lightgrey></td>";
			}
		}
		
		$color = ''; $adakuliah = ''; $ListLibur = ''; $arrElemen = array();
		if($hari == 0) $adakuliah = "red"; 
		if(!empty($arrTanggalPenting[$i]))
		{	foreach($arrTanggalPenting[$i] as $atp)
			{
				$arrElemen = explode('|', $atp);
				if($arrElemen[4] == 'Y') $adakuliah = 'red';
				$ListLibur .= "<a href='#_self' onClick=\"SetLibur('$tahun-$bulan-$ix', 0, '$arrElemen[5]')\"<div align=center style='border:thin solid red; background-color:$arrElemen[3]'><sup>$arrElemen[2]</sup></div></a>";
			}
		}		
		$number = "<font color='$adakuliah'>$ix</font>$ListLibur"; 
		
		echo "<td class=ul1 align=center height=50>$number</td>";
		
		if($hari == 6)
		{	echo "</tr>
			  <tr>";
		}
	}
	for($j = $hari+1; $j < 7; $j++)
	{	echo "<td bgcolor=lightgrey></td>";
	}
	echo "</tr>";
	
	
	
	echo "	</table>
			<script>
				function SetLibur(tanggal, md, id)
				{	lnk = '$_SESSION[mnux].libur.php?TanggalID='+tanggal+'&md='+md+'&id='+id;
					win2 = window.open(lnk, '', 'width=600, height=400, resizable, scrollbars, status');
					if (win2.opener == null) childWindow.opener = self;
				}
				function EditJenisLibur(md, id)
				{	lnk = '$_SESSION[mnux].jenislibur.php?md='+md+'&id='+id;
					win2 = window.open(lnk, 0, 'width=600, height=400, resizable, scrollbars, status');
					if (win2.opener == null) childWindow.opener = self;
				}
			</script>
		</td>";
			
	echo "<td><table class=bsc cellspacing=1 border=1 align=right width=110%>
			  <tr><td class=ul1 colspan=2><b>Keterangan Jenis Hari Libur</b></td></tr>";
	
	$s = "select * from jenislibur where KodeID='".KodeID."'";
	$r = _query($s);
	$n = 0;
	while($w = _fetch_array($r))
	{	$n++;
		echo "<tr><td class=ul1 width=15><input type=text name='ColorBox$n' style='background-color:$w[Warna]' size=1 maxlength=0>
				  <td class=ul1><a href='#' onClick=\"EditJenisLibur(0, '$w[JenisLiburID]')\">$w[Nama]</a></td></tr>";
	}
	echo "</table>
		  <sup>Catatan: Tanggal yang ditandai merah menandakan bahwa hari itu tidak diperkenankan memiliki jadwal ujian/kuliah</sup>
		  </td>";
	echo "</table>";
}

function GetTahunArray($start, $end)
{	$arrResult = array();
	for($i = $start; $i <= $end; $i++) $arrResult[] = $i;
	return $arrResult;
}

function GetBulanArray()
{	$arrResult = array();
	for($i = 1; $i <= 12; $i++) $arrResult[] = $i;
	return $arrResult;
}

function GetNamaHariArray()
{	return array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
}

function UbahKeBulanIndonesia($integer)
{	$arrBulan = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	return $arrBulan[$integer-1];
}
?>
