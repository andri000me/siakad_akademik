<?php
// Author: Irvandy Goutama
// Email: irvandygoutama@gmail.com
// Start Date; 17 Maret 2009

session_start();

// *** Changable Parameters ***
  

// *** Parameters ***

$_remTahun = GetSetVar('_remTahun');
$_remProdi = GetSetVar('_remProdi');
$_remProg = GetSetVar('_remProg');
$_remPage = GetSetVar('_remPage', 0);

// *** Main ***

TampilkanJudul("Remedial Mahasiswa");
$gos = (empty($_REQUEST['gos']))? 'TampilkanHalaman' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function loadJavaScripts()
{	echo "<script>
			function changePage(page, mnux)
			{	prevpage = page-1;
				self.location = '?mnux='+mnux+'&_remPage='+prevpage;
			}
			function fnDaftarRemedial() {
				lnk = '$_SESSION[mnux].dft.php';
				win2 = window.open(lnk, '', 'width=900, height=800, scrollbars, status');
				if (win2.opener == null) childWindow.opener = self;
			}
			function fnCetakJadwal() {
				lnk = '$_SESSION[mnux].cetakjadwal.php?JRID=0';
				win2 = window.open(lnk, '', 'width=900, height=800, scrollbars, status, resizable');
				if (win2.opener == null) childWindow.opener = self;
			}
			function fnCetakPeserta() {
				lnk = '$_SESSION[mnux].cetakpeserta.php?JRID=0';
				win2 = window.open(lnk, '', 'width=900, height=800, scrollbars, status, resizable');
				if (win2.opener == null) childWindow.opener = self;
			}
			function fnGagalRemedial() {
				lnk = '$_SESSION[mnux].dftgagal.php';
				win2 = window.open(lnk, '', 'width=900, height=800, scrollbars, status, resizable');
				if (win2.opener == null) childWindow.opener = self;
			}
			function fnEditRemedial(md, id)
			{	lnk = '$_SESSION[mnux].edit.php?md='+md+'&id='+id;
				win2 = window.open(lnk, '', 'width=900, height=500, scrollbars, status');
				if (win2.opener == null) childWindow.opener = self;
			}
			function fnEditMhsw(id)
			{	lnk = '$_SESSION[mnux].dftmhsw.php?id='+id;
				win2 = window.open(lnk, '', 'width=900, height=500, scrollbars, status');
				if (win2.opener == null) childWindow.opener = self;
			}
			function fnEditPresensi(pid)
			{	lnk = '$_SESSION[mnux].presensi.php?pid='+pid;
				win2 = window.open(lnk, '', 'width=900, height=500, scrollbars, status');
				if (win2.opener == null) childWindow.opener = self;
			}
			function fnIsiNilai(id)
			{	lnk = '$_SESSION[mnux].nilai.php?id='+id;
				win2 = window.open(lnk, '', 'width=900, height=700, scrollbars, status');
				if (win2.opener == null) childWindow.opener = self;
			}
		</script>";		
}

function TampilkanHalaman()
{	TampilkanHeader();
	
	if(!empty($_SESSION['_remTahun'])) 
	{	TampilkanRemedial();
	}
}

function TampilkanHeader()
{	$optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['_remProdi'], "KodeID='".KodeID."'", 'ProdiID');
	$optprog = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_remProg'], "KodeID='".KodeID."'", 'ProgramID');
	echo "<table class=box cellspacing=1 align=center width=800>
			<form action='?' method=POST onSubmit=\"changePage(1, '$_SESSION[mnux]')\">
				<input type=hidden name='gos' value=''>
			<tr><td class=wrn width=2 rowspan=5></td>
			  <td class=inp>Tahun Akd:</td>
			  <td class=ul1><input type=text name='_remTahun' value='$_SESSION[_remTahun]' size=5 maxlength=5 /></td>
			  <td class=inp>Prg. Pendidikan:</td>
			  <td class=ul1><select name='_remProg'>$optprog</select></td>
			  <td class=inp>Program Studi:</td>
			  <td class=ul1 colspan=3><select name='_remProdi'>$optprodi</select></td>
			  </tr>
			<tr><td colspan=6>
					<input type=submit name='Kirim' value='Kirim Parameter'>
					<input type=reset name='Reset' value='Reset Parameter'>
					<input type=button name='Tambah' value='Tambah Jadwal Remedial' onClick=\"fnEditRemedial(1, 0)\">
				</td></tr>
			</form>
		</table>";			
}

function TampilkanRemedial()
{	
	$TahunID = $_SESSION['_remTahun'];
	$ProdiID = $_SESSION['_remProdi'];
	$ProgramID = $_SESSIOn['_remProg'];
	
	$whr_prodi = (empty($ProdiID))? "" : "and ProdiID='$ProdiID'";
	$whr_tahun = (empty($TahunID))? "" : "and TahunID='$TahunID'";
	$whr_prog = (empty($ProgramID))? "" : "and ProgramID='$ProgramID'"; 
	
	// Paging Parameters
	$limit = 20;
	$start_page = $limit*$_SESSION['_remPage'];	
	$counting = $start_page;
	
	$s = "select * from jadwalremedial 
			where KodeID='".KodeID."'
				$whr_tahun
				$whr_prodi
				$whr_prog";
	$r = _query($s);
	
	loadJavaScripts();	
											
	echo "<p><table class=box cellspacing=1 align=center width=900>
			<form action='?' method=POST>
				
				<tr><td class=ul1 align=center>
						<input type=button name='Laporan' value='Laporan Daftar Remedial Mahasiswa' onClick=\"fnDaftarRemedial()\">
						<input type=button name='CetakJadwal' value='Cetak Semua Jadwal' onClick=\"fnCetakJadwal()\">
						<input type=button name='CetakMahasiswa' value='Cetak Semua Peserta Remedial' onClick=\"fnCetakPeserta()\">
						<input type=button name='Gagal' value='Cetak Mahasiswa Gagal' onClick=\"fnGagalRemedial()\"></td>
						</tr>
			</form>	
		</table></p>";
	
	
	echo "<p><table class=box cellspacing=1 align=center width=900>
			<form action='?' method=POST>
				<tr><th class=ttl width=20 colspan=2>#</th>
					<th class=ttl width=200>Matakuliah</th>
					<th class=ttl width=40>SKS</th>
					<th class=ttl width=120>Tanggal Penting</th>
					<th class=ttl width=70>&sum Mhsw</th>
					<th class=ttl width=70>Nilai</th>
					";
	
	while($w = _fetch_array($r))
	{	$n++;
		$TanggalPenting = AmbilTanggalPenting($w['JadwalRemedialID']);
		$DetailMhsw = "$w[JmlhMhsw]
						<br><a href='#self' onClick=\"fnEditMhsw('$w[JadwalRemedialID]')\"><sup>Daftar Mhsw</sup></a>";
		echo "<tr><td class=inp width=10>$n</td>
					<td class=ul1 width=10><a href='#' onClick=\"fnEditRemedial(0, '$w[JadwalRemedialID]')\"><img src='img/edit.png'></a></td>
					<td class=ul1 width=200>$w[Nama]</td>
					<td class=ul1 width=40 align=center>$w[SKS]</td>
					<td class=ul1>$TanggalPenting</td>
					<td class=ul1 width=70 align=center>$DetailMhsw</td>
					<td class=ul1 width=70 align=center><a href='#' onClick=\"fnIsiNilai('$w[JadwalRemedialID]')\"><img src='img/edit.jpg'></a></td>
			  </tr>";
	}
	// Paging
	$totalpage = floor(($n/$limit))+1;
	$fontpage = ($_SESSION['_remPage']+1 == 1)? '<font color=red>1</font>' : '<font color=green>1</font>';
	$tempmnux = $_SESSION['mnux'];
	$pagestring = "<a href='#' onClick=\"changePage(1, '$tempmnux', this.form);\">$fontpage</a>";
	for($j=2; $j <= $totalpage; $j++)
	{	$fontpage = ($j==$_SESSION['_remPage']+1)? '<font color=red>' : '<font color=green>'; 
		$pagestring .= ", <a href='#' onClick=\"changePage($j, '$tempmnux', this.form);\">$fontpage$j</font></a>";
	}
	
	//$nextstartpage = $start_page+1;
	echo "
		
		<tr>
			<td class=ul1 colspan=10 align=center><font color=green><b>Hal:</b></font> $pagestring</td>
		</tr>
		<tr>
			<td class=ul1 colspan=10 align=center><font color=green><b>Total:</b></font> <b>$n</b></td>
		</tr>";
		
	echo "</form>
		</table></p>
		";
}

function AmbilTanggalPenting($jrid)
{	$returnstring = '';
	$JmlhMhsw = GetaField('jadwalremedial', "JadwalRemedialID='$jrid' and KodeID", KodeID, "JmlhMhsw");
	
	$s = "select * from presensiremedial where JadwalRemedialID='$jrid' and KodeID='".KodeID."'";
	$r = _query($s);
	$n = 0;
	while($w = _fetch_array($r))
	{	$returnstring.= "&bull; $w[Tanggal] ".str_replace('-',':', $w['JamMulai'])."-".str_replace('-',':', $w['JamSelesai'])." - <b>$w[Keterangan]</b> ".
			(($JmlhMhsw > 0)? "<a href='#self' onClick=\"fnEditPresensi('$w[PresensiRemedialID]')\"><sub>Absensi</sub></a><br>" : "<br>");
		$n++;
	}
	return $returnstring;
}

?>