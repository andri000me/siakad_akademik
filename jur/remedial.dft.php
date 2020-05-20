<?php
// Author: Irvandy Goutama
// Email: irvandygoutama@gmail.com
// Start Date; 17 Maret 2009

session_start();
// *** Parameters ***

// *** Main ***

$gos = (empty($_REQUEST['gos']))? 'DaftarRemedial' : $_REQUEST['gos'];
$gos();

// *** Functions ***

function DaftarRemedial()
{	include_once "../sisfokampus1.php";
	HeaderSisfoKampus("Laporan Daftar Remedial Mahasiswa");
	TampilkanJudul("Laporan Daftar Remedial Mahasiswa");
	

	$tahunstring =(empty($_SESSION['_remedialTahunID']))? "" : "and r.TahunID='$_SESSION[_remedialTahunID]'";
	$prodistring =(empty($_SESSION['_remedialProdiID']))? "" : "and mk.ProdiID='$_SESSION[_remedialProdiID]'";
	
	$s = "select r.MhswID, r.TahunID, m.Nama, jd.MKKode, jd.Nama as _MKNama, jd.SKS, r.Harga, r.Bayar
			from krsremedial r left outer join mhsw m on r.MhswID=m.MhswID and r.KodeID=m.KodeID
			left outer join jadwalremedial jd on r.JadwalRemedialID=jd.JadwalRemedialID and r.KodeID=jd.KodeID
			left outer join mk on mk.MKID=jd.MKID and mk.KodeID=jd.KodeID
			where r.KodeID='".KodeID."' $tahunstring $prodistring 
			order by m.MhswID";
	$r = _query($s);
	
	$s1 = "select distinct(r.MhswID) as _MhswID 
			from krsremedial r left outer join mhsw m on r.MhswID=m.MhswID and r.KodeID=m.KodeID
			left outer join jadwalremedial jd on r.JadwalRemedialID=jd.JadwalRemedialID and r.KodeID=jd.KodeID
			left outer join mk on mk.MKID=jd.MKID and mk.KodeID=jd.KodeID
			where r.KodeID='".KodeID."' $tahunstring $prodistring 
			group by r.MhswID order by m.Nama";
	$r1 = _query($s1);
	$n1 = _num_rows($r1);
	
	echo "<table class=box cellspacing=1 align=center width=100%>
			<form name='rem_form' action='?' method=POST onSubmit=\"\">
				<input type=hidden name='gos' value='CetakLaporanRemedialMahasiswa' \>
				<input type=hidden name='TahunID' value='$_SESSION[_remedialTahunID]' \>
				<input type=hidden name='ProdiID' value='$_SESSION[_remedialProdiID]' \>
				
				<tr><td colspan=10 align=center><input type=button name='Cetak' value='Cetak Laporan' onClick=\"this.form.submit()\">
									<input type=button name='Batal' value='Batal' onClick=\"self.close()\" ></td></tr>
				<tr><th class=ttl width=20>#</th>
					<th class=ttl width=80>NIM</th>
					<th class=ttl width=200>Nama</th>
					<th class=ttl width=>Mata Kuliah</th>
					<th class=ttl width=40>SKS</th>
					<th class=ttl width=90>Harga</th>
					<th class=ttl width=90>Sudah Bayar</th>
					<th class=ttl width=40>Bayar?</th>";
	$count = 0;
	$curID ='1knckosjkdfo2skdkj';
	while($w = _fetch_array($r))
	{	
		$MhswID = ''; $Nama = ''; $counting = ''; $gantibaris = '';
		$Bayar = ''; $Sisa = 0; $Biaya = 0; $DiBayar = 0;
		if($curID != $w['MhswID'])
		{	$w1 = _fetch_array($r1);
			$MhswID =  $w['MhswID'];
			$Nama = $w['Nama'];
			$curID = $MhswID;
			$count++;
			$counting = $count.".";
			$gantibaris = ($count != 1)? '<tr><td colspan=10><hr color=green size=1></hr></td></tr>' : '';
		}
		
		// Cek Bila Sudah Bayar Biaya Remedial atau belum
		$ss = "select bm.*
				from bipotmhsw bm
					left outer join bipot2 b2 on bm.BIPOT2ID=b2.BIPOT2ID
				where bm.MhswID='$w[MhswID]' 
					and bm.TahunID='$_SESSION[TahunID]' 
					and b2.Remedial = 'Y'
					and bm.KodeID='".KodeID."'
					and bm.NA = 'N'";
		$rr = _query($ss);
		
		// Cek Bila ada record biaya mahasiswa tentang Remedial
		if(_num_rows($rr) > 0)
		{	// Bila ada, cek apakah biaya tersebut sudah dibayarkan
			while($ww = _fetch_array($rr))
			{	$Sisa += ($ww['Jumlah'] * $ww['Besar']) - $ww['Dibayar'];
				$Biaya += $ww['Jumlah'] * $ww['Besar'];
				$Dibayar += $ww['Dibayar'];
			}
			if($Sisa > 0) $Bayar = 'N'; 
			else $Bayar = 'Y';
		}
		else 
		{	// Bila tidak ada, cek apa ada biaya yang seharusnya dikenakan (dengan kata lain: belum proses bipot)
			$sss = "select b2.* 
						from bipot2 b2
						where b2.Remedial = 'Y'";
			$rrr = _query($sss);
			if(_num_rows($rrr)> 0) $Bayar = 'N';
			else $Bayar = 'Y';
		}
		
		// Cek Untuk Biaya Khusus Remedial
		$NamaRem = "Remedial: $w[MKKode] - $w[_MKNama] - $w[SKS]";
		$BiayaRem = GetFields('bipotmhsw', "TambahanNama='$NamaRem' and TahunID='$_SESSION[_remTahun]' and MhswID='$w[MhswID]' and BIPOTNamaID=0 and KodeID", KodeID, '*');
		
		// Tampilkan Laporan Biaya Remedial
		$SisaRem = ($BiayaRem['Jumlah'] * $BiayaRem['Besar']) - $BiayaRem['Dibayar'];
		$Kesimpulan = ($SisaRem <= 0) && $Bayar == 'Y';
		$BayarStatus = ($Kesimpulan == true)? "<img src='../img/Y.gif'>" : "<img src='../img/N.gif'>";
		
		$TotalBiaya = $Biaya+($BiayaRem['Jumlah']*$BiayaRem['Besar']);
		$TotalDibayar = $Dibayar+$BiayaRem['Dibayar'];
		if($TotalBiaya > 0)
		{	$Harga = 'Rp '.number_format($TotalBiaya, 2, ',', '.');
			$Bayar = 'Rp '.number_format($TotalDibayar, 2, ',', '.');
		}
		else
		{	$Harga = '<i>Blm diproses</i>';
			$Bayar = '<i>Blm diproses</i>';
		}
		echo "	$gantibaris
				<tr><td class=ul1 align=center>$counting</td>
					<td class=ul1>$MhswID</td>
					<td class=ul1>$Nama</td>
					<td class=ul1>$w[_MKNama]<sup>$w[MKKode]</sup></td>
					<td class=ul1 align=center>$w[SKS]</td>
					<td class=ul1 align=right>$Harga</td>
					<td class=ul1 align=right>$Bayar</td>
					<td class=ul1 align=center>$BayarStatus</td></tr>
					";
	}
				
		echo "	</form>
		</table></p>";
	
}

function CetakLaporanRemedialMahasiswa()
{	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	include_once "../header_pdf.php";

	$pdf = new PDF('P', 'mm', 'A4');
	$pdf->SetTitle("Laporan Remedial Mahasiswa");
	$pdf->AddPage('P');
	
	BuatHeaderLap($pdf);
	TampilkanIsinya($pdf);
	
	$pdf->Output();
}

function BuatHeaderLap($p)
{	$t = 6;
	$p->SetFont('Helvetica', 'B', 9);
	$p->Cell(8, $t, 'No.', 1, 0, 'C');
	$p->Cell(23, $t, 'NIM', 1, 0, 'C');
	$p->Cell(45, $t, 'Nama', 1, 0, 'C');
	$p->Cell(97, $t, 'Mata Kuliah', 1, 0, 'C');
	$p->Cell(10, $t, 'SKS', 1, 0, 'C');
	$p->Cell(10, $t, 'Bayar', 1, 1, 'C');	
}

function TampilkanIsinya($p)
{	$tahunstring =(empty($_SESSION['_remedialTahunID']))? "" : "and r.TahunID='$_SESSION[_remedialTahunID]'";
	$prodistring =(empty($_SESSION['_remedialProdiID']))? "" : "and mk.ProdiID='$_SESSION[_remedialProdiID]'";
	
	$s = "select r.MhswID, r.TahunID, m.Nama, jd.MKKode, jd.Nama as _MKNama, jd.SKS, r.Harga, r.Bayar
			from krsremedial r left outer join mhsw m on r.MhswID=m.MhswID and r.KodeID=m.KodeID
			left outer join jadwalremedial jd on r.JadwalRemedialID=jd.JadwalRemedialID and r.KodeID=jd.KodeID
			left outer join mk on mk.MKID=jd.MKID and mk.KodeID=jd.KodeID
			where r.KodeID='".KodeID."' $tahunstring $prodistring 
			order by m.MhswID";
	$r = _query($s);
	
	$t = 6; $n = 0;
	while($w = _fetch_array($r))
	{	$MhswID = ''; $Nama = ''; $counting = ''; $gantibaris = '';
		$Bayar = ''; $Sisa = 0; $Biaya = 0; $DiBayar = 0;
		if($curID != $w['MhswID'])
		{	$MhswID =  $w['MhswID'];
			$Nama = $w['Nama'];
			$curID = $MhswID;
			$count++;
			$counting = $count.".";
			$gantibaris = ($count != 1)? '<tr><td colspan=10><hr color=green size=1></hr></td></tr>' : '';
		}
		
		// Cek Bila Sudah Bayar Biaya Remedial atau belum
		$ss = "select bm.*
				from bipotmhsw bm
					left outer join bipot2 b2 on bm.BIPOT2ID=b2.BIPOT2ID
				where bm.MhswID='$w[MhswID]' 
					and bm.TahunID='$_SESSION[TahunID]' 
					and b2.Remedial = 'Y'
					and bm.KodeID='".KodeID."'
					and bm.NA = 'N'";
		$rr = _query($ss);
		
		// Cek Bila ada record biaya mahasiswa tentang Remedial
		if(_num_rows($rr) > 0)
		{	// Bila ada, cek apakah biaya tersebut sudah dibayarkan
			while($ww = _fetch_array($rr))
			{	$Sisa += ($ww['Jumlah'] * $ww['Besar']) - $ww['Dibayar'];
				$Biaya += $ww['Jumlah'] * $ww['Besar'];
				$Dibayar += $ww['Dibayar'];
			}
			if($Sisa > 0) $Bayar = 'N'; 
			else $Bayar = 'Y';
		}
		else 
		{	// Bila tidak ada, cek apa ada biaya yang seharusnya dikenakan (dengan kata lain: belum proses bipot)
			$sss = "select b2.* 
						from bipot2 b2
						where b2.Remedial = 'Y'";
			$rrr = _query($sss);
			if(_num_rows($rrr)> 0) $Bayar = 'N';
			else $Bayar = 'Y';
		}
		
		// Cek Untuk Biaya Khusus Remedial
		$NamaRem = "Remedial: $w[MKKode] - $w[_MKNama] - $w[SKS]";
		$BiayaRem = GetFields('bipotmhsw', "TambahanNama='$NamaRem' and TahunID='$_SESSION[_remTahun]' and MhswID='$w[MhswID]' and BIPOTNamaID=0 and KodeID", KodeID, '*');
		
		// Tampilkan Laporan Biaya Remedial
		$SisaRem = ($BiayaRem['Jumlah'] * $BiayaRem['Besar']) - $BiayaRem['Dibayar'];
		$Kesimpulan = ($SisaRem <= 0) && $Bayar == 'Y';
		$BayarStatus = ($Kesimpulan == true)? "OK" : "x";
		
		$TotalBiaya = $Biaya+($BiayaRem['Jumlah']*$BiayaRem['Besar']);
		$TotalDibayar = $Dibayar+$BiayaRem['Dibayar'];
		if($TotalBiaya > 0)
		{	$Harga = 'Rp '.number_format($TotalBiaya, 2, ',', '.');
			$Bayar = 'Rp '.number_format($TotalDibayar, 2, ',', '.');
		}
		else
		{	$Harga = '<i>Blm diproses</i>';
			$Bayar = '<i>Blm diproses</i>';
		}
		$p->SetFont('Helvetica', '', 9);
		if($gantibaris=='GB') 
		{	$p->Cell(193, $t, '', 'B', 0);
			$p->Ln($t);
		}
		$p->Cell(8, $t, $counting, 'LB', 0, 'R');
		$p->Cell(23, $t, $MhswID, 'LB', 0, 'C');
		$p->Cell(45, $t, $Nama, 'LB', 0);
		$p->Cell(97, $t, $w['_MKNama']." ( ".$w['MKKode']." )", 'LB', 0);
		$p->Cell(10, $t, $w['SKS'], 'LB', 0, 'C');
		$p->Cell(10, $t, $BayarStatus, 'LBR', 0, 'C');
		$p->Ln($t);
		
	} 	
}

?>