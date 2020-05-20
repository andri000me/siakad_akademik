<?php
//created by: Arisal Yanuarafi
//created on May 2012
TampilkanJudul('Rekap Mahasiswa Aktif <sup> (script updated on 2016-01-26 03:31 Perlengkaan Universitas Ekasakti.)</sup>');
echo "<div class='well'>Update log: <ul><li>Memperbaiki perhitungan pada kolom jumlah</li><li>Memasukkan mahasiswa beasiswa dan Program Pascasarjana ke daftar yang telah bayar</li></ul></div>";
  	$TahunID = GetSetVar('ThnAkd');
	$ProgramID = GetSetVar('ProgramID');
	$StatusAwalID = GetSetVar('StatusAwalID');
	$TahunAwal = (substr($TahunID,0,4))-8;
	$_SESSION['_Bayar'] = ($_REQUEST['_Bayar'] == 'Y')? 'Y' : 'N';
	$_SESSION['_KRS'] = $_REQUEST['_KRS'];
	$_SESSION['TahunAwal'] = $TahunAwal;
 if (!empty($_SESSION['ThnAkd'])) {
 $s="select distinct(left(TahunID,4)) as TahunID from mhsw where TahunID >= $TahunAwal and TahunID <= $_SESSION[ThnAkd] and TahunID not like 'Tran%' order by TahunID";
 $r=_query($s);
 $n=_num_rows($r);
 $n++;
 }
 ?>
 <form action=? name='pilihan' method="post"><table width=900 class=box><tr>
 <td class=inp>Tahun Akademik:</td>
 <?php 
   $s0 = "select DISTINCT(TahunID) from tahun where TahunID>20032 and TahunID != 'Tran-Manua' order by TahunID DESC";
  $r0 = _query($s0);
  $opttahun = "<option value=''></option>";
  while($w0 = _fetch_array($r0))
  {  $ck = ($w0['TahunID'] == $_SESSION['ThnAkd'])? "selected" : '';
     $opttahun .=  "<option value='$w0[TahunID]' $ck>$w0[TahunID]</option>";
  }
  $optprog  = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['ProgramID'], "KodeID='".KodeID."'", 'ProgramID');
  $optStatAwal  = GetOption2('statusawal', "concat(StatusAwalID, ' - ', Nama)", 'StatusAwalID', $_SESSION['StatusAwalID'], "KodeID='".KodeID."'", 'StatusAwalID');
  //cek pembayaran
  $ckgel = ($_SESSION['_Bayar'] == 'Y')? 'checked' : '';
  		$whr = array();
		if ($_SESSION['_Bayar'] == 'Y')   $whr[] = "((h.Bayar>0) or m.StatusAwalID='M' or m.StatusAwalID='S' or m.ProgramID='M' or m.ProgramID='P' or p.FakultasID='08')";
	//cek krs	
  			$optKRS = "<option value=''></option>";
		if ($_SESSION['_KRS']==1) 
		{ 	$optKRS .= "<option value='1' selected>Sudah isi KRS</option>";
			$whr[]= "h.SKS>0";
		}
		else $optKRS .= "<option value='1'>Sudah isi KRS</option>";
		if ($_SESSION['_KRS']==2) 
		{ 	$optKRS .= "<option value='2' selected>Belum isi KRS</option>";
			$whr[] = "h.SKS=0";
		}
		else $optKRS .= "<option value='2'>Belum isi KRS</option>";
		if ($_SESSION['_KRS']==3) 
		{ 	$optKRS .= "<option value='3' selected>KRS disetujui PA</option>";
			$whr[] = "h.SetujuPA='Y'";
		}
		else $optKRS .= "<option value='3'>KRS disetujui PA</option>";
		if ($_SESSION['_KRS']==4) 
		{ 	$optKRS .= "<option value='4' selected>KRS blm disetujui PA</option>";
			$whr[] = "h.SetujuPA!='Y'";
		}
		else $optKRS .= "<option value='4'>KRS blm disetujui PA</option>";
		if (!empty($_SESSION['ProgramID'])) $whr[] = "m.ProgramID='$_SESSION[ProgramID]'";
		if (!empty($_SESSION['ThnAkd'])) $whr[] = "h.TahunID like '$_SESSION[ThnAkd]%'";
		if (!empty($_SESSION['StatusAwalID'])) $whr[] = "m.StatusAwalID like '$_SESSION[StatusAwalID]'";
		
		  	$_whr = implode(' and ', $whr);
 			$_whr = (empty($_whr))? '' : ' and ' . $_whr;
  ?>
  <td class=ul1>
  <select name='ThnAkd' onChange='this.form.submit()'><?php echo $opttahun; ?></select></td>
  <td class=inp>Program:</td>
  <td class=ul1><select onChange='this.form.submit()' name='ProgramID'><?php echo $optprog; ?></select></td>
  <td class=ul1>Hanya yang sudah bayar? <?php echo "<input type=checkbox name='_Bayar' value='Y'  $ckgel onClick='this.form.submit()'>"; ?></td>
  <td class=inp>Status KRS</td>
  <td class=ul1><?php echo "<select name='_KRS' onChange='this.form.submit()'>$optKRS"; ?></select></td>
  <td> <a href=<?php echo"baa/rekap.mhs.reg.xls.php?thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&byr=$_SESSION[_Bayar]&krs=$_SESSION[_KRS]"?>><img src="img/printer2.gif" /></a></td>
  </tr>
  <tr><td class="inp">Status Awal</td><td><select name='StatusAwalID' onChange="this.form.submit()"><?php echo $optStatAwal; ?></select></table></form>

<?php  if (!empty($_SESSION['ThnAkd'])) { ?>
 <table border=1 style="border-collapse:collapse" class=bsc width=900>
 	<tr valign="middle" bgcolor="#CCCCCC">
	<th rowspan=2 align="center" ><b>FAKULTAS</th>
		<th rowspan=2 align="center" ><b>JURUSAN</th>
		<th colspan="<?php echo $n; ?>" align="center"><b>ANGKATAN</th>
		<th rowspan="2" align="center"><b>Jumlah</th>
	</tr>
	<th align="center" bgcolor="#CCCCCC">
	<b>&laquo;<?php echo $TahunAwal;?>
	</td>
	<?php
	while ($w=_fetch_array($r)) {
	echo "<td align=center bgcolor=#CCCCCC><b>$w[TahunID]</td>";
	}
	?>
	<?php
	$s1="select p.FakultasID, p.ProdiID,p.Nama as NMProdi,j.Nama as Prog from prodi p, jenjang j where j.JenjangID=p.JenjangID and p.NA='N' order by p.FakultasID, p.ProdiID";
	$r1=_query($s1);
	while ($w1=_fetch_array($r1)) {
	$Prodi=$w1['ProdiID'];
	echo "<tr valign='middle' >";
	$currentFak = GetaField('fakultas',"FakultasID",$w1[FakultasID],'Nama');
	if ($savFak != $currentFak) {
	$jmlProdi = GetaField('prodi',"NA='N' and FakultasID", $w1[FakultasID],'COUNT(ProdiID)');
	$nmFakultas = GetaField('fakultas',"FakultasID",$w1[FakultasID],'Nama');
	echo "<td rowspan=$jmlProdi align=center><strong>$nmFakultas</strong></td>";
	$savFak = $currentFak;
	}
	?>
	<td align="center"><?php echo $w1['NMProdi']; ?></td>
		<?php 
		$tot = 0;
		$s2="SELECT count( h.MhswID ) as JML
			FROM khs h, mhsw m
			left outer join prodi p on p.ProdiID=m.ProdiID
			WHERE m.MhswID = h.MhswID 
			AND m.TahunID < $TahunAwal
			$_whr
			AND m.ProdiID LIKE '$Prodi' ";

		$r2=_query($s2);
		$Jmldribu = 0;
		while ($w2=_fetch_array($r2)) {
		$Jmldribu = $Jmldribu + $w2[JML];
		$bwhTahunAwal = $w2['JML'] + 0;
		$tot += $bwhTahunAwal;
		} 
		?>
		<td align="center">
		<?php 
		if (!empty($Jmldribu)) { echo "<a href='baa/rekap.mhs.reg.detail.php?prd=$Prodi&thn=k2000&thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&byr=$_SESSION[_Bayar]&krs=$_SESSION[_KRS]' target='_blank'><b>$bwhTahunAwal</b></a>"; }
		else echo $Jmldribu; 
		?>
		</td>
		<?php

		
		 	$s="select distinct(left(TahunID,4)) as TahunID from mhsw where TahunID >= $TahunAwal and TahunID<=$_SESSION[ThnAkd] order by TahunID";
 			$r=_query($s);
		while ($w=_fetch_array($r)) {
			$s2="SELECT count( h.MhswID ) as JML
			FROM khs h left outer join mhsw m on m.MhswID = h.MhswID
			left outer join prodi p on p.ProdiID=m.ProdiID
			WHERE 
			m.TahunID like '$w[TahunID]%'
			$_whr
			AND m.ProdiID LIKE '$Prodi' ";
			$r2=_query($s2);
			$THNID =$w[TahunID];
			$Jmlaribu = 0;
			while ($w2=_fetch_array($r2)) {
			$Jmlaribu = $Jmlaribu + $w2[JML];
			$tot = $tot + $Jmlaribu;
			} 
			?>
			<td align="center">
			<?php 
			if (!empty($Jmlaribu)) { echo "<a href='baa/rekap.mhs.reg.detail.php?prd=$Prodi&thn=$THNID&thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&byr=$_SESSION[_Bayar]&krs=$_SESSION[_KRS]' target='_blank'><b>$Jmlaribu</b></a>"; }
			else echo $Jmlaribu; 
			?>
			</td>
			<?php } ?>
			<td align="center">
			<?php if (!empty($tot)) echo "<a href='baa/rekap.mhs.reg.detail.php?prd=$Prodi&thn=&thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&byr=$_SESSION[_Bayar]&krs=$_SESSION[_KRS]' target='_blank'>"; 
			echo '<b>'.$tot.'</b></a>'; $_tot=$_tot + $tot;?>
			</td>
		</tr>
		
		<?php } ?>
		<tr bgcolor="#CCCCCC" valign="middle"><td align=center colspan='2'><strong>Jumlah</strong></td>
		<?php
			$s2="SELECT count( h.MhswID ) as JML
			FROM khs h, mhsw m
			left outer join prodi p on p.ProdiID=m.ProdiID
			WHERE m.MhswID = h.MhswID
			AND m.TahunID < $TahunAwal
			$_whr";
			$r2=_query($s2);
			$THNID =$w[TahunID];
			$Jmlaribu = 0;
			while ($w2=_fetch_array($r2)) {
			$Jmlaribu = $w2[JML]+0;
			} 
			?>
			<td align="center"><?php if (!empty($Jmlaribu)) echo "<a href='baa/rekap.mhs.reg.detail.php?prd=&thn=k2000&thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&byr=$_SESSION[_Bayar]&krs=$_SESSION[_KRS]' target='_blank'>"; ?><strong><?php 
			echo $Jmlaribu; ?></strong></a></td>
	<?php 

		$s="select distinct(left(TahunID,4)) as TahunID from mhsw where TahunID >=$TahunAwal and TahunID<=$_SESSION[ThnAkd] order by TahunID";
 			$r=_query($s);
		while ($w=_fetch_array($r)) {
			$s2="SELECT count( h.MhswID ) as JML
			FROM khs h, mhsw m
			left outer join prodi p on p.ProdiID=m.ProdiID
			WHERE m.MhswID = h.MhswID
			AND m.TahunID like '$w[TahunID]%'
			$_whr";
			$r2=_query($s2);
			$THNID =$w['TahunID'];
			$Jmlaribu = 0;
			while ($w2=_fetch_array($r2)) {
			$Jmlaribu = $w2['JML']+0;
			} 
			?>
			<td align="center"><?php if (!empty($Jmlaribu)) echo "<a href='baa/rekap.mhs.reg.detail.php?prd=&thn=$THNID&thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&byr=$_SESSION[_Bayar]&krs=$_SESSION[_KRS]' target='_blank'>"; ?><strong><?php 
			echo $Jmlaribu; ?></strong></a></td>
			<?php } ?>
			
			
		<td align="center"><?php if (!empty($_tot)) echo "<a href='baa/rekap.mhs.reg.detail.php?prd=&thn=&thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&byr=$_SESSION[_Bayar]&krs=$_SESSION[_KRS]' target='_blank'>";  
		echo '<b>'.$_tot.'</b></a>'; ?></td></tr>
		</table>
		<br /><br />
		<?php } ?>
		