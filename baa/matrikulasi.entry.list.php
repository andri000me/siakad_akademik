<?php
//Author: Irvandy Goutama
//Start Date: 8 March 2009

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Matrikulasi Mahasiswa");

$matri_tahun = GetSetVar('matri_tahun', '');

TampilkanJudul("List Kelompok Berdasarkan Tahun Akademik");
$gos = (empty($_REQUEST['gos']))? "ListKelompok" : $_REQUEST['gos'];
$gos();

// Functions

function loadJavaScripts()
{	echo "<SCRIPT LANGUAGE='JavaScript'>
			function fnEditKelompok(md, id) {
				lnk = '../$_SESSION[mnux].entry.edt.php?md='+md+'&kid='+id;
				//alert(lnk);
				win2 = window.open(lnk, '', 'width=620, height=300, scrollbars, status');
				if (win2.opener == null) childWindow.opener = self;
				}
		 </script>
	";		
}

function ListKelompok()
{	
	loadJavaScripts();

	$opttahun = GetOption2('tahun', "TahunID", 'TahunID', $_SESSION['matri_tahun'], '', 'TahunID');
	$commonwidth = 25;
	$kelaminwidth = 3*$commonwidth;
	$prodiwidth = $n*$commonwidth;
	$nilaiwidth = 2*$commonwidth;
	
	$s = "select ProdiID, Nama from prodi where KodeID='".KodeID."' order by ProdiID";
	$r = _query($s);
	$n = _num_rows($r);
	
	while($w = _fetch_array($r))
	{	$prodititle.="<th class=ttl width=$commonwidth>$w[ProdiID]</th>";
	}
	
	$ss = "select * from matrikulasi where TahunID='$_SESSION[matri_tahun]' and KodeID='".KodeID."' order by Nama";
	$rr = _query($ss);
	$nn = _num_rows($rr)+2;
	
	// Total statistik berdasarkan kelamin
	$s1 = "select m.MhswID, m.Kelamin
				from mhsw m left outer join matrikulasi mk on m.MatriID=mk.MatriID  
				where m.KodeID='".KodeID."' and mk.KodeID='".KodeID."' and mk.TahunID='$_SESSION[matri_tahun]'";
	$r1 = _query($s1);
	$pria = 0; $wanita = 0; $nogender = 0;
		
	while($w1 = _fetch_array($r1))
	{	if($w1['Kelamin']=='P') $pria++;
		else if($w1['Kelamin']=='W') $wanita++;
		else $nogender++;
	}
	
	// Total statistik berdasarkan prodi
	$s1 = "select p.ProdiID, count(m.ProdiID) as _count from prodi p 
				left outer join mhsw m on p.ProdiID=m.ProdiID
				left outer join matrikulasi mk on m.MatriID=mk.MatriID
				where m.KodeID='".KodeID."' and mk.KodeID='".KodeID."' and p.KodeID='".KodeID."' and mk.TahunID='$_SESSION[matri_tahun]' group by m.ProdiID";
	$r1 = _query($s1);
	$w1 = _fetch_array($r1);
	$r = _query($s);
	$prodistring='';
	while($w=_fetch_array($r))
	{	if($w1['ProdiID'] == $w['ProdiID'])
		{	$prodistring .= "$w[ProdiID]: <b>$w1[_count]</b> &bull; ";
			$w1 = _fetch_array($r1);
		}
		else
		{	$prodistring .= "$w[ProdiID]: <b>0</b> &bull; ";
		}
	}
	
	// Total statistik berdasarkan nilai
	$s1 = "select MhswID, NilaiUjian 
				from mhsw m left outer join matrikulasi mk on m.MatriID=mk.MatriID
				where mk.TahunID='$_SESSION[matri_tahun]' and m.KodeID='".KodeID."' and mk.KodeID='".KodeID."'";
	$r1 = _query($s1);
	$n1 = _num_rows($r1);
	$total = 0;
	while($w1 = _fetch_array($r1))
	{	$total += $w1['NilaiUjian'];
	}
	$average = ($n1==0)? 0 : number_format($total/$n1, 2);
	
	// Make the title names
	echo "<table class=box cellspacing=1 align=center width=800>
			<form action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]' />
				<input type=hidden name='gos' value='' />
				
				<tr><td class=inp>Tahun Akademik:</td>
					<td class=ul1><input type=text name='matri_tahun' value='$_SESSION[matri_tahun]' size=3 maxlength=10>
											<input type=submit name='Set' value='Set'></td></tr>
			</form>
		  </table>
		  
		  <table class=box cellspacing=1 align=center width=800>	
				<tr><td class=ul1 align=center colspan=10><font color=green><b>STATISTIK TOTAL SELURUH KELOMPOK:</b><font></td></tr>
				<tr><td class=inp>Total Peserta:</td>
					<td class=ul1><b>$n1</b></td>
				<tr><td class=inp>Berdasarkan Kelamin:</td>
					<td class=ul1>Pria: <b>$pria</b> &bull; Wanita: <b>$wanita</b></td></tr>
				<tr><td class=inp>Berdasarkan Prodi:</td>
					<td class=ul1>$prodistring</td></tr>
				<tr><td class=inp>Berdasarkan Nilai:</td>
					<td class=ul1>Total Nilai: <b>$total</b> &bull; Rata-rata Nilai: <b>$average</b></td></tr> 
		</table>							
			
		<table class=box cellspacing=1 align=center width=800>
				<tr><td>$nbsp</td></tr>
				<tr><th class=ttl rowspan=2 width=15>#</th>
					<th class=ttl rowspan=2 width=80>Nama Kelompok</th>
					<th class=ttl rowspan=2 width=30>Kapasitas Sekarang</th>
					<th class=ttl rowspan=2 width=30>Kapasitas Maksimum</th>
					<td rowspan=$nn width=5>&nbsp</th>
					<th class=ttl colspan=3 width=$kelaminwidth>Berdasarkan Kelamin</th>
					<td rowspan=$nn width=5>&nbsp</th>
					<th class=ttl colspan=$n width=$prodiwidth>Berdasarkan Program Studi</th>
					<td rowspan=$nn width=5>&nbsp</th>
					<th class=ttl colspan=2 width=$nilaiwidth>Berdasarkan Nilai</th>
					<td rowspan=$nn width=5>&nbsp</th>
				</tr>
				<tr>
					<th class=ttl width=$commonwidth><img src='../img/P.bmp'></img></th>
					<th class=ttl width=$commonwidth><img src='../img/W.bmp'></img></th>
					<th class=ttl width=$commonwidth>&times</th>
					$prodititle
					<th class=ttl width=$commonwidth>Total</th>
					<th class=ttl width=$commonwidth>Rata2</th>
				</tr>";
				
	$counting = 0;
	
	// Fill the table
	while($ww = _fetch_array($rr))
	{	$counting++;
		echo "<tr>
				<td class=ul1 align=right><a href='#' onClick=\"javascript:fnEditKelompok(0, $ww[MatriID])\" />
					<img title='Edit Kelompok' src='../img/edit.png' /></a> $counting.</td>
				<td class=ul1>$ww[Nama]</td>
				<td class=ul1 align=center>$ww[KapasitasSekarang]</td>
				<td class=ul1 align=center>$ww[KapasitasMaksimum]</td>";
		
		$s1 = "select MhswID, Kelamin from mhsw where MatriID='$ww[MatriID]' and KodeID='".KodeID."'";
		$r1 = _query($s1);
		$pria = 0; $wanita = 0; $nogender = 0;
		
		while($w1 = _fetch_array($r1))
		{	if($w1['Kelamin']=='P') $pria++;
			else if($w1['Kelamin']=='W') $wanita++;
			else $nogender++;
		}
		
		echo " <td class=ul1 align=center>$pria</td>
				<td class=ul1 align=center>$wanita</td>
				<td class=ul1 align=center>$nogender</td>";
		
		$s1 = "select m.ProdiID, p.Nama, count(m.ProdiID) as _count 
					from prodi p left outer join mhsw m on m.ProdiID=p.ProdiID
				where m.MatriID='$ww[MatriID]' and m.KodeID='".KodeID."' and p.KodeID='".KodeID."' group by p.ProdiID order by p.ProdiID";
		$r1 = _query($s1);
		
		$r = _query($s);
		
		$w1 = _fetch_array($r1);
		while($w = _fetch_array($r))
		{	if($w1['ProdiID'] == $w['ProdiID'])
			{	echo "<td class=ul1 align=center>$w1[_count]</td>";
				$w1 = _fetch_array($r1);
			}
			else
			{	echo "<td class=ul1 align=center>0</td>";
			}
		}
		
		$s2 = "select MhswID, NilaiUjian from mhsw where MatriID='$ww[MatriID]' and KodeID='".KodeID."'";
		$r2 = _query($s2);
		$n2 = _num_rows($r2);
		$total = 0;
		while($w2 = _fetch_array($r2))
		{	$total += $w2['NilaiUjian'];
		}
		
		echo "<td class=ul1 align=center>$total</td>";
		$average = ($n2==0)? 0 : number_format($total/$n2, 2); 
		echo "<td class=ul1 align=center>$average</td>";
		echo "</tr>";
	}
	
	echo "	   <tr><td class=ul1 colspan=20 align=center><input type=button name='Kembali' value='Kembali' onClick=\"self.close()\" ></td></tr>
		 <table>";
					
}
?>