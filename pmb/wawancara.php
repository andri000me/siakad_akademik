<?php
	//Author: Irvandy Goutama
	// Start: 8 Januari 2009
	// Email: irvandygoutama@gmail.com
	
	// *** Parameters ***
	$gelombang = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
	$md = $_REQUEST['md'];
	$_wawanEntry = GetSetVar('_wawanEntry', '');
	
	// *** Main ***
	TampilkanJudul("Wawancara Mahasiswa - Gelombang $gelombang");
	if (empty($gelombang)) {
		echo ErrorMsg("Error",
			"Tidak ada gelombang PMB yang aktif.<br />
			Aktifkan salah satu gelombang terlebih dahulu.<br />
			Untuk mengaktifkan: <a href='?mnux=pmbsetup'>Modul PMB Setup</a>");
	}
	else {
		$gos = (empty($_REQUEST['gos']))? 'DaftarWawancara' : $_REQUEST['gos'];
		$gos($gelombang);
	}
	
	// *** Helper Functions ***
	function GetOptionsFromData($sourceArray, $chosen)
	{	
			$optresult = "";
			if($chosen == '' or empty($chosen))	
			{ 	$optresult .= "<option value='' selected></option>"; }
			else { $optresult .= "<option value=''></option>"; }
			for($i=0; $i < count($sourceArray); $i++)
			{	if($chosen == $sourceArray[$i])
				{	$optresult .= "<option value='$sourceArray[$i]' selected>$sourceArray[$i]</option>"; }
				else
				{ 	$optresult .= "<option value='$sourceArray[$i]'>$sourceArray[$i]</option>"; }
			}
			return $optresult;
	}
	
	// *** Functions ***
	
	
	function DaftarWawancara($gelombang)
	{	
		echo "<table class=box cellspacing=1 align=center>
				<form action='?' method=POST>
				<input type=hidden name='mnux' value='$_SESSION[mnux]' />
				<input type=hidden name='gos' value='' />
								
				<tr>
					<td class=inp>Cari Calon Mahasiswa: </td>	
					<td class=ul1><input type=text name='_wawanEntry' value='$_SESSION[_wawanEntry]'/></td>
					<td class=ul1><input type=submit name='Cari' value='Cari'>
						<input type=button name='Reset' value='Reset'
						onClick=\"location='?mnux=$_SESSION[mnux]&gos=&_wawanEntry='\" /></td>
				</tr>
				<tr>
					<td></td>
					<td class=ul1 colspan=4>*) Masukkan nama atau No.PMB dari calon mahasiswa</td>
				</tr>
				</form>
			</table>";
		if(!empty($_SESSION['_wawanEntry']))
		{	ListWawancara($gelombang);
		}
		else
		{	ListLastSetWawancara($gelombang);
		}
	}
	
	function loadJavaScripts()
	{
		echo "<script>
				function fnEditCama(gel, md, id) {
					lnk = '$_SESSION[mnux].edt.php?gel='+gel+'&md='+md+'&wPMB='+id;
					win2 = window.open(lnk, '', 'width=1000, height=800, scrollbars, status');
					if (win2.opener == null) childWindow.opener = self;
					}
				</script>";
	}
	
	function ListLastSetWawancara($gelombang)
	{	$s1 = "select w.WawancaraID, w.RuangID, p.Nama as _Pewawancara, p2.Nama as _Pewawancara2, pmb.Nama, w.PMBID, w.HasilWawancara, 
				date_format(w.Tanggal, '%d') as _Tanggal,
				date_format(w.Tanggal, '%b') as _Bulan,
				date_format(w.Tanggal, '%Y') as _Tahun, 
				date_format(w.JamMulaiWawancara, '%H:%i') as _Jam,
				date_format(w.JamSelesaiWawancara, '%H:%i') as _AkhirJam
				from wawancara w 
					left outer join pmb pmb on w.PMBID=pmb.PMBID
					left outer join presenter p on w.Pewawancara=p.PresenterID
					left outer join presenter p2 on w.Pewawancara2=p2.PresenterID 
				where w.PMBPeriodID='$gelombang' and w.KodeID='".KodeID."' order by w.TanggalBuat DESC limit 20";
		$r1 = _query($s1);
		
		loadJavaScripts();
		
		echo "<p>
					<table class=box cellspacing=1 cellpadding=4 width=900>
					<tr>
						<th class=ttl width=80>No. PMB (Daftar)</th>
						<th class=ttl width=200 >Nama Calon Mahasiswa</th>
						<th class=ttl width=80>Tgl.</th>
						<th class=ttl width=100>Jam</th>
						<th class=ttl width=80>Ruang</th>
						<th class=ttl width=240>Pewawancara</th>
						<th class=ttl wdith=100>Hasil Wawancara</th>
						<th class=ttl width=20></th>
					</tr>
				";
		while($w1 = _fetch_array($r1))
		{	$Pewawancara = $w1['_Pewawancara'];
			$Pewawancara = (empty($w1['_Pewawancara2']))? $Pewawancara : $Pewawancara.', '.$w1['_Pewawancara2'];
			echo "<tr>
					<td class=ul1 rowspan=$n_row valign=center align=center><b>$w1[PMBID]</b></td>
					<td class=ul1 rowspan=$n_row valign=center align=center><b>$w1[Nama]</b></td>";
	
			echo "
					<td class=ul1 align=center><sup>$w1[_Tanggal]</sup> $w1[_Bulan]<sub> $w1[_Tahun]</sub></td>
					<td class=ul1 align=center><sup>$w1[_Jam]</sup> s/d <sub>$w1[_AkhirJam]</sub></td>
					<td class=ul1 align=center>$w1[RuangID]</td>
					<td class=ul1 >$Pewawancara</td>
					<td class=ul1 align=center><b>$w1[HasilWawancara]</b></td>
					<td class=ul1 ><a href=\"javascript:fnEditCama('$gelombang', 0, '$w1[WawancaraID]')\"><img src='img/edit.png' border=0></a></td>
					</tr>
					";
		}				
		echo "</table></p>";
	}
	
	function ListWawancara($gelombang)
	{	$s1 = "select * from `pmb` 
			where (PMBID like '%$_SESSION[_wawanEntry]%'
				or Nama like '%$_SESSION[_wawanEntry]%')
				and PMBPeriodID='$gelombang'
				and KodeID='".KodeID."'
			order by PMBID";
		$r1 = _query($s1);
		$n1 = _num_rows($r1);
		$x1 = 0;
		
		loadJavaScripts();
		
		if($n1==0)
		{	// Jika nama calon mahasiswa/no.PMB tidak diketemukan dalam table pmb
			if(!empty($_REQUEST['_wawanEntry'])) 
			{	echo "<br>Nama/No. Pmb: <b>$_REQUEST[_wawanEntry]</b> tidak diketemukan. Silakan mencoba lagi"; }
		}
		else 
		{	
			echo "<p>
					<table class=box cellspacing=1 cellpadding=4 width=800>
					<tr>
						<th class=ttl width=80 rowspan=2 valign=center>No. PMB (Daftar)</th>
						<th class=ttl width=200 rowspan=2 valign=center>Nama Calon Mahasiswa</th>
						<th class=ttl width=20 rowspan=2 valign=center>Prodi</th>
						<th class=ttl width=600 colspan=7>Data Wawancara</th>
					</tr>
					<tr>
						<th class=ttl width=20 align=center>#</th>
						<th class=ttl width=80>Tgl.</th>
						<th class=ttl width=240>Pewawancara</th>
						<th class=ttl width=20>Saran</th>
						<th class=ttl width=100>Rekomendasi</th>
						<th class=ttl width=20></th>
						<th class=ttl width=20></th>
					</tr>
				";
		
			while($w1=_fetch_array($r1))
			{
				// Jika nama calon mahasiswa/no.PMB ditemukan
				$s = "select * from `wawancara` 
					where PMBID = '$w1[PMBID]' and KodeID='".KodeID."' and PMBPeriodID='$gelombang'";
				$r = _query($s);
				$n = _num_rows($r);
				$x = 0;
			
			if($n==0)
			{	
				echo "<tr>
						<td class=ul1 align=center><b>$w1[PMBID]</b></td>
						<td class=ul1 align=center><b>$w1[Nama]</b></td>
						<td class=ul1 valign=center align=center>$w1[Pilihan1]<br><hr color=green><br>$w1[Pilihan2]</td>
					<td class=ul1 colspan=4 align=center><b> - Belum ada wawancara -</b></td>
					<td colspan=3 align=right>		
						<input type=button name='Tambah' value='Tambah Wawancara'
							onClick=\"javascript:fnEditCama('$gelombang', 1, '$w1[PMBID]')\" />
					</td>
					</tr>
					<tr>
						<td colspan=10>
							<hr color=green width=100%>
						</td>
					</tr>";
			}
			else
			{
				if($n<3) { $n_row = 3; }
				else { $n_row = $n+1; }
				
				//Set Variable bila Nama/No.PMB ada/tidak ada di dalam table `wawancara`
				
				$x1++; 		
	
				echo "<tr>
						<td class=ul1 rowspan=$n_row valign=center align=center><b>$w1[PMBID]</b></td>
						<td class=ul1 rowspan=$n_row valign=center align=center><b>$w1[Nama]</b></td>
						<td class=ul1 rowspan=$n_row valign=center align=center>$w1[Pilihan1]<br><hr color=green><br>$w1[Pilihan2]</td>";
						
				while($arr = _fetch_array($r))
				{	$x++;
					
					if($n==0)	{ $n_row = 2; }
					else if($n==1)	{	$n_row = 2; }
					else {	$n_row=1;	}
					
					$tanggalwawancara= substr($arr['TanggalWawancara'], 0, 16);
					echo "
						<td class=ul1 rowspan=$n_row valign=center align=right>$x.</td>
						<td class=ul1 rowspan=$n_row valign=center align=center>$tanggalwawancara</td>
						<td class=ul1 rowspan=$n_row valign=center >$arr[Pewawancara] , $arr[Pewawancara2]</td>
						<td class=ul1 rowspan=$n_row valign=center align=center>$arr[SaranProgram]</td>
						<td class=ul1 rowspan=$n_row valign=center align=center>$arr[HasilWawancara]</td>
						<td class=ul1 rowspan=$n_row valign=center ><a href=\"javascript:fnEditCama('$gelombang', 0, '$arr[WawancaraID]')\"><img src='img/edit.png' border=0></a></td>
						<td class=ul1 rowspan=$n_row valign=center ></td>
						</tr>
						<tr>
						";
					if($n < 2) { echo "</tr><tr>"; }
				}
				
				
				
					echo "	<td colspan=7 align=right>		
						<input type=button name='Tambah' value='Tambah Wawancara'
							onClick=\"javascript:fnEditCama('$gelombang', 1, '$w1[PMBID]')\" />
						</td>
					</tr>
					<tr>
						<td colspan=10>
							<hr color=green width=100%>
						</td>
					</tr>
					";
				}
			}
			echo	"</table></p>";
		}
	}
?>