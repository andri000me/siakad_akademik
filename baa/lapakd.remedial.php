<?
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";

$TahunID = GetSetVar('TahunID');
$_remedialTahunID = GetSetVar('_remedialTahunID');
$_remedialProdiID = GetSetVar('_remedialProdiID');
$periode1 = $_REQUEST['periode1_y'].'-'.$_REQUEST['periode1_m'].'-'.$_REQUEST['periode1_d'];
$periode2 = $_REQUEST['periode2_y'].'-'.$_REQUEST['periode2_m'].'-'.$_REQUEST['periode2_d'];	
$_SESSION[periode1] = (!empty($_REQUEST['periode1_y']))? $periode1 : $_SESSION[periode1];
$_SESSION[periode2] = (!empty($_REQUEST['periode2_y']))? $periode2 : $_SESSION[periode2];
$_remPage = GetSetVar('_remPage', 0);

if ($_REQUEST['gos'] != 'cetakLaporan'){
	echo "<HTML xmlns=\"http://www.w3.org/1999/xhtml\">
	<HEAD><TITLE>$title</TITLE>
	<META content=\"Emanuel Setio Dewo\" name=\"author\">
	<META content=\"Sisfo Sekolah\" name=\"description\">
	<link rel=\"stylesheet\" type=\"text/css\" href=\"../themes/$_Themes/index.css\" />
	";
}
$gos = (empty($_REQUEST['gos']))? 'TampilkanHalaman' : $_REQUEST['gos'];
$gos();

function TampilkanHalaman()
{	


	TampilkanHeader();
	if (!empty($_SESSION[periode1])){
		TampilkanRemedial();
	}

}

function TampilkanHeader()
{	
	TampilkanJudul('Laporan Remedial Per Periode');
	$prodiopt = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['_remedialProdiID'], "KodeID='".KodeID."'", 'ProdiID');
	$p1 = (!empty($_REQUEST[periode1_y]))? $_REQUEST['periode1_y'].'-'.$_REQUEST['periode1_m'].'-'.$_REQUEST['periode1_d'] : date('Y-m-d');
	$p2 = (!empty($_REQUEST[periode2_y]))? $_REQUEST['periode2_y'].'-'.$_REQUEST['periode2_m'].'-'.$_REQUEST['periode2_d'] : date('Y-m-d');
	$periode1 = getDateOption($p1,'periode1');
	$periode2 = getDateOption($p2,'periode2');

	echo "<table class=box cellspacing=1 align=center width=800>
			<form action='?' method=POST onSubmit=\"changePage(1, '$_SESSION[mnux]')\">
				<input type=hidden name='gos' value=''>
			<tr>
				<td width=220><font color=green><b>Tahun Akademik: </b></font></td>
				<td><input type=text name='TahunID' value='$_SESSION[TahunID]' size=4 maxlength=5 /></td>
			</tr>
			<tr>
				<td><font color=green><b> Program Studi: </b></font></td>
				<td><select name='_remedialProdiID'>$prodiopt</select\"></td>
			</tr>
			<tr>
				<td><font color=green><b> Periode Remedial : </b></font></td>
				<td>$periode1 s/d $periode2 <input type=submit name='Cari' value='Cari' ></td>
			</tr>
			</form>
		</table>";			
}

function TampilkanRemedial()
{	
	$TahunID = $_SESSION['TahunID'];
	$ProdiID = $_SESSION['_remedialProdiID'];
	$periode1 = $_SESSION[periode1];
	$periode2 = $_SESSION[periode2];	
	
	$prodistring = (empty($ProdiID))? "" : "and jd.ProdiID='$ProdiID'";
	$tahunstring = (empty($TahunID))? "" : "and a.TahunID='$TahunID'";
	$remperiode = "and (date_format(jd.TglTatapMuka1,'%Y-%m-%d') >= '$periode1' and date_format(jd.TglTatapMuka1,'%Y-%m-%d') <= '$periode2') or
						(date_format(jd.TglTatapMuka2,'%Y-%m-%d') >= '$periode1' and date_format(jd.TglTatapMuka2,'%Y-%m-%d') <= '$periode2')";
	// Paging Parameters
	$limit = 20;
	$start_page = $limit*$_SESSION['_remPage'];	
	$counting = $start_page;
	
	$s = "select distinct(a.MKID) as _MKID, a.MKKode, a.Nama, a.SKS, b.Sesi, 
				 concat('<sup>', date_format(jd.TglTatapMuka1, '%d'),'</sup>',
						date_format(jd.TglTatapMuka1, '%b'),
						'<sub>', date_format(jd.TglTatapMuka1, '%Y'),'</sub>') as _Tgl1,
				 concat('<sup>', date_format(jd.TglTatapMuka1, '%H'), ':', date_format(jd.TglTatapMuka1, '%i'), '</sup>',
						'&#8594'
						'<sub>', date_format(jd.AkhirTglTatapMuka1, '%H'), ':', date_format(jd.AkhirTglTatapMuka1, '%i'), '</sub>') as _Jam1,
				 concat('<sup>', date_format(jd.TglTatapMuka2, '%d'),'</sup>',
						date_format(jd.TglTatapMuka2, '%b'),
						'<sub>', date_format(jd.TglTatapMuka2, '%Y'),'</sub>') as _Tgl2,
				 concat('<sup>', date_format(jd.TglTatapMuka2, '%H'), ':', date_format(jd.TglTatapMuka2, '%i'), '</sup>',
						'&#8594'
						'<sub>', date_format(jd.AkhirTglTatapMuka2, '%H'), ':', date_format(jd.AkhirTglTatapMuka2, '%i'), '</sub>') as _Jam2,
				 concat('<sup>', date_format(jd.TglUjian, '%d'),'</sup>',
						date_format(jd.TglUjian, '%b'),
						'<sub>', date_format(jd.TglUjian, '%Y'),'</sub>') as _TglUjian,
				 concat('<sup>', date_format(jd.TglUjian, '%H'), ':', date_format(jd.TglUjian, '%i'), '</sup>',
						'&#8594'
						'<sub>', date_format(jd.AkhirTglUjian, '%H'), ':', date_format(jd.AkhirTglUjian, '%i'), '</sub>') as _JamUjian,
				 jd.JadwalRemedialID,
				 d.Nama as _Dosen
			from krs a left outer join khs b on a.KHSID=b.KHSID and a.KodeID=b.KodeID
						left outer join jadwalremedial jd on a.MKID=jd.MKID and a.KodeID=jd.KodeID $prodistring
						left outer join dosen d on jd.DosenID=d.Login and jd.KodeID=d.KodeID
						left outer join mk on mk.MKID=a.MKID and mk.KodeID=a.KodeID
			where (a.GradeNilai='D' or a.GradeNilai='E') $tahunstring $remperiode and a.Final='Y' and a.KodeID='".KodeID."'  
			group by a.MKID order by a.Nama
			limit $start_page, $limit";
	$r = _query($s);
	
	$s1 = "select distinct(a.MKID) as _MKID	from krs a left outer join khs b on a.KHSID=b.KHSID and a.KodeID=b.KodeID
														left outer join jadwalremedial jd on a.MKID=jd.MKID and a.KodeID=jd.KodeID
														left outer join mk on mk.MKID=a.MKID and mk.KodeID=a.KodeID
			where (a.GradeNilai='D' or a.GradeNilai='E') $tahunstring $prodistring and a.Final='Y' and a.KodeID='".KodeID."'
			group by a.MKID order by a.Nama
			";
	$r1 = _query($s1);
	$n = _num_rows($r1);
	
	//loadJavaScripts();	
											
	
	
	echo "<p>
			<table class=box cellspacing=1 align=center width=700>
			<form name=list action='?' method=POST>
			<input type=hidden name=gos value=cetakLaporan >
			<input type=hidden name=TahunID value=$_SESSION[TahunID] >
			<input type=hidden name=_remedialTahunID value=$_SESSION[_remedialTahunID] >
			<input type=hidden name=_remedialProdiID value=$_SESSION[_remedialProdiID] >
			<input type=hidden name=p1 value=$_SESSION[periode1] >
			<input type=hidden name=p2 value=$_SESSION[periode2]  >
				<tr>
				<td colspan=7 align=right><input type=submit value=Cetak Laporan /></td>
				</tr>
				<tr><th class=ttl width=20>#</th>
					<th class=ttl width=200>Nama Dosen</th>
					<th class=ttl>Mata Kuliah</th>
					<th class=ttl width=40>SKS</th>
					<th class=ttl width=80>Jum. Pert</th>
					<th class=ttl width=40>Ujian</th>
					<th class=ttl width=70>&sum Mhsw</th>
					";
	
	while($w = _fetch_array($r))
	{	$counting++;
		$s2 = "select count(a.MKID) as _countkrs from krs a left outer join remedial r on a.RemedialID=r.RemedialID
			where (a.GradeNilai='D' or a.GradeNilai='E') and a.TidakLengkap='N' and a.Final='Y' and a.KodeID='".KodeID."' and a.MKID='$w[_MKID]'
				and (r.TahunID='$_SESSION[_remedialTahunID]' or r.TahunID is NULL)			
			";
		$r2 = _query($s2);
		$w2 = _fetch_array($r2);
		
		$s3 = "select count(r.RemedialID) as _countkrs
					from remedial r left outer join remedial r2 on r.RemedialLanjutanID=r2.RemedialID
									left outer join krs k on k.KRSID=r.KRSID and k.KodeID=r.KodeID
				where (r.GradeNilai='D' or r.GradeNilai='E') and r.Final='Y' and r.RemedialLanjutanID=0 and r.KodeID='".KodeID."' and k.MKID='$w[_MKID]'
				    and (r.TahunID!='$_SESSION[_remedialTahunID]')";
		$r3 = _query($s3);
		$w3 = _fetch_array($r3);
		
		$remtahunstring = (empty($RemTahunID))? "" : "and r.TahunID='$RemTahunID'";
		$s1 = "select count(r.RemedialID) as _countrem 
				from remedial r left outer join jadwalremedial jd on r.JadwalRemedialID=jd.JadwalRemedialID and r.KodeID=jd.KodeID
				where r.KodeID='".KodeID."' $remtahunstring and jd.MKID='$w[_MKID]'"; 
		$r1 = _query($s1);
		$w1 = _fetch_array($r1);
		
		
		$_countrem = (empty($w1['_countrem']))? 0 : $w1['_countrem'];
		$dosen = strtoupper($w['_Dosen']);
		$style = (empty($w['JadwalRemedialID']))? 'nac' : 'ul1';
		$_totalcountkrs = $w2['_countkrs']+$w3['_countkrs'];
		
		echo "<tr><td class=$style>$counting.</td>
				<td class=$style>$dosen</td>
				<td class=$style align=center>$w[Nama]</td>
				<td class=$style align=center>$w[SKS]</td>
				<td class=$style align=center>2X</td>
				<td class=$style align=center>1X</td>
				<td class=$style align=center>$_countrem</td>
			 </tr>";
		echo "<tr><td bgcolor=silver height=1 colspan=11></td></tr>";
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

function cetakLaporan(){
	$TahunID = $_REQUEST['TahunID'];
	$ProdiID = $_REQUEST['_remedialProdiID'];
	$periode1 = $_REQUEST[p1];
	$periode2 = $_REQUEST[p2];	
	//echo $ProdiID;
	$tgl1 = explode('-',$periode1);
	$tgl2 = explode('-',$periode2);
	
	$periode1_y = $tgl1[0];
	$periode1_m = $tgl1[1];
	$periode1_d = $tgl1[2];
	
	$periode2_y = $tgl2[0];
	$periode2_m = $tgl2[1];
	$periode2_d = $tgl2[2];
	
	$p = new PDF('P','mm','A4');
	$p->AddPage();
	$p->SetFont('Helvetica', 'B', 12);
	$p->Cell(200,8,'Laporan Remedial Periode '.$periode1_d.'/'.$periode1_m.'/'.$periode1_y.' - '.$periode2_d.'/'.$periode2_m.'/'.$periode2_y,0,1,'C');
	
	//header//
	$t = 5;
	$p->SetFont('Arial', 'B', 7);
	$p->Cell(8,$t,'No',1,0,'C');
	$p->Cell(50,$t,'Nama Dosen',1,0,'C');
	$p->Cell(50,$t,'Mata Kuliah',1,0,'C');
	$p->Cell(15,$t,'SKS',1,0,'C');
	$p->Cell(25,$t,'Jum. Pertemuan',1,0,'C');
	$p->Cell(15,$t,'Ujian',1,0,'C');
	$p->Cell(25,$t,'Jum. Mahasiswa',1,1,'C');
	//////
	
	
	$prodistring = (empty($ProdiID))? "" : "and jd.ProdiID='$ProdiID'";
	$tahunstring = (empty($TahunID))? "" : "and a.TahunID='$TahunID'";
	$remperiode = "and (date_format(jd.TglTatapMuka1,'%Y-%m-%d') >= '$periode1' and date_format(jd.TglTatapMuka1,'%Y-%m-%d') <= '$periode2') or
						(date_format(jd.TglTatapMuka2,'%Y-%m-%d') >= '$periode1' and date_format(jd.TglTatapMuka2,'%Y-%m-%d') <= '$periode2')";
	// Paging Parameters
	$limit = 20;
	$start_page = $limit*$_SESSION['_remPage'];	
	$counting = $start_page;
	
	$s = "select distinct(a.MKID) as _MKID, a.MKKode, a.Nama, a.SKS, b.Sesi, 
				 concat('<sup>', date_format(jd.TglTatapMuka1, '%d'),'</sup>',
						date_format(jd.TglTatapMuka1, '%b'),
						'<sub>', date_format(jd.TglTatapMuka1, '%Y'),'</sub>') as _Tgl1,
				 concat('<sup>', date_format(jd.TglTatapMuka1, '%H'), ':', date_format(jd.TglTatapMuka1, '%i'), '</sup>',
						'&#8594'
						'<sub>', date_format(jd.AkhirTglTatapMuka1, '%H'), ':', date_format(jd.AkhirTglTatapMuka1, '%i'), '</sub>') as _Jam1,
				 concat('<sup>', date_format(jd.TglTatapMuka2, '%d'),'</sup>',
						date_format(jd.TglTatapMuka2, '%b'),
						'<sub>', date_format(jd.TglTatapMuka2, '%Y'),'</sub>') as _Tgl2,
				 concat('<sup>', date_format(jd.TglTatapMuka2, '%H'), ':', date_format(jd.TglTatapMuka2, '%i'), '</sup>',
						'&#8594'
						'<sub>', date_format(jd.AkhirTglTatapMuka2, '%H'), ':', date_format(jd.AkhirTglTatapMuka2, '%i'), '</sub>') as _Jam2,
				 concat('<sup>', date_format(jd.TglUjian, '%d'),'</sup>',
						date_format(jd.TglUjian, '%b'),
						'<sub>', date_format(jd.TglUjian, '%Y'),'</sub>') as _TglUjian,
				 concat('<sup>', date_format(jd.TglUjian, '%H'), ':', date_format(jd.TglUjian, '%i'), '</sup>',
						'&#8594'
						'<sub>', date_format(jd.AkhirTglUjian, '%H'), ':', date_format(jd.AkhirTglUjian, '%i'), '</sub>') as _JamUjian,
				 jd.JadwalRemedialID,
				 d.Nama as _Dosen
			from krs a left outer join khs b on a.KHSID=b.KHSID and a.KodeID=b.KodeID
						left outer join jadwalremedial jd on a.MKID=jd.MKID and a.KodeID=jd.KodeID $prodistring
						left outer join dosen d on jd.DosenID=d.Login and jd.KodeID=d.KodeID
						left outer join mk on mk.MKID=a.MKID and mk.KodeID=a.KodeID
			where (a.GradeNilai='D' or a.GradeNilai='E') $tahunstring $remperiode and a.Final='Y' and a.KodeID='".KodeID."'  
			group by a.MKID order by a.Nama
			limit $start_page, $limit";
	$r = _query($s);
	
	$s1 = "select distinct(a.MKID) as _MKID	from krs a left outer join khs b on a.KHSID=b.KHSID and a.KodeID=b.KodeID
														left outer join jadwalremedial jd on a.MKID=jd.MKID and a.KodeID=jd.KodeID
														left outer join mk on mk.MKID=a.MKID and mk.KodeID=a.KodeID
			where (a.GradeNilai='D' or a.GradeNilai='E') $tahunstring $prodistring and a.Final='Y' and a.KodeID='".KodeID."'
			group by a.MKID order by a.Nama
			";
	$r1 = _query($s1);
	$n = _num_rows($r1);
	
	while($w = _fetch_array($r))
	{	$counting++;
		$s2 = "select count(a.MKID) as _countkrs from krs a left outer join remedial r on a.RemedialID=r.RemedialID
			where (a.GradeNilai='D' or a.GradeNilai='E') and a.TidakLengkap='N' and a.Final='Y' and a.KodeID='".KodeID."' and a.MKID='$w[_MKID]'
				and (r.TahunID='$_SESSION[_remedialTahunID]' or r.TahunID is NULL)			
			";
		$r2 = _query($s2);
		$w2 = _fetch_array($r2);
		
		$s3 = "select count(r.RemedialID) as _countkrs
					from remedial r left outer join remedial r2 on r.RemedialLanjutanID=r2.RemedialID
									left outer join krs k on k.KRSID=r.KRSID and k.KodeID=r.KodeID
				where (r.GradeNilai='D' or r.GradeNilai='E') and r.Final='Y' and r.RemedialLanjutanID=0 and r.KodeID='".KodeID."' and k.MKID='$w[_MKID]'
				    and (r.TahunID!='$_SESSION[_remedialTahunID]')";
		$r3 = _query($s3);
		$w3 = _fetch_array($r3);
		
		$remtahunstring = (empty($RemTahunID))? "" : "and r.TahunID='$RemTahunID'";
		$s1 = "select count(r.RemedialID) as _countrem 
				from remedial r left outer join jadwalremedial jd on r.JadwalRemedialID=jd.JadwalRemedialID and r.KodeID=jd.KodeID
				where r.KodeID='".KodeID."' $remtahunstring and jd.MKID='$w[_MKID]'"; 
		$r1 = _query($s1);
		$w1 = _fetch_array($r1);
		
		
		$_countrem = (empty($w1['_countrem']))? 0 : $w1['_countrem'];
		$dosen = strtoupper($w['_Dosen']);
		$style = (empty($w['JadwalRemedialID']))? 'nac' : 'ul1';
		$_totalcountkrs = $w2['_countkrs']+$w3['_countkrs'];
	
		/// output ///
		$t = 5;
		$p->SetFont('Arial', 'B', 7);
		$p->Cell(8,$t,$counting.'.',1,0,'C');
		$p->Cell(50,$t,$dosen,1,0,'C');
		$p->Cell(50,$t,$w[Nama],1,0,'C');
		$p->Cell(15,$t,$w[SKS],1,0,'C');
		$p->Cell(25,$t,'2X',1,0,'C');
		$p->Cell(15,$t,'1X',1,0,'C');
		$p->Cell(25,$t,$_countrem,1,1,'C');
		
		
	}
	
	$p->output();

}

?>
