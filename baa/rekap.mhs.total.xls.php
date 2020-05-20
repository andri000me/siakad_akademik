<?php 
//created by: Arisal Yanuarafi
//created on May 2012
   include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
    $_SESSION['ThnAkd'] = $_GET['thnakd'];
  	$_SESSION['ProgramID']= $_GET['prg'];
	 $_SESSION['_Bayar'] = ($_GET['byr'] == 'Y')? 'Y' : 'N';
    		$whr = array();
		//cek bayar
		if ($_SESSION['_Bayar'] == 'Y')   $whr[] = "(h.Bayar>0 or h.Potongan>0)";
		//cek sks
			//cek krs	
				$_SESSION['_KRS'] = $_GET['krs'];
				if ($_SESSION['_KRS']==1) 
				{ 
					$whr[]= "h.SKS>0";
				}
				elseif ($_SESSION['_KRS']==2) 
				{ 
					$whr[] = "h.SKS=0";
				}
		  	$_whr = implode(' and ', $whr);
 			$_whr = (empty($_whr))? '' : ' and ' . $_whr;
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-mhs");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
 if (!empty($_SESSION['ThnAkd'])) {
 $s="select distinct(left(TahunID,4)) as TahunID from mhsw where TahunID>1999 and TahunID<=$_SESSION[ThnAkd] order by TahunID";
 $r=_query($s);
 $n=_num_rows($r);
 $n++;
 }
 $_n=$n+3;
 ?>
 <style>
table,font { font-family:Trebuchet MS; line-height:100%; }
.header{ font-family:Trebuchet MS; font-size:20px; line-height:90%; }
.garis {height:0px; line-height:0px;}
</style>
<?php  if (!empty($_SESSION['ThnAkd'])) { ?>

 <table border=1 style="border-collapse:collapse" class=bsc width=800>
 <tr><td class=header align="center" colspan="<?php echo $_n; ?>"><strong> BIRO ADMINISTRASI AKADEMIK & KEMAHASISWAAN (BAAK)<BR />
					REKAPITULASI MAHASISWA REGISTRASI TAHUN AKADEMIK <?php echo $_SESSION['ThnAkd']; ?> <br />
					<?php 
					$nmProgram = GetaField('program',"ProgramID",$_SESSION['ProgramID'],'Nama');
					echo $nmProgram; ?></strong></td></tr>
 	<tr valign="middle" >
	<th rowspan=2 align="center" ><b>FAKULTAS</th>
		<th rowspan=2 align="center" ><b>JURUSAN</th>
		<th colspan="<?php echo $n; ?>" align="center" bgcolor=#CCCCCC><b>ANGKATAN</th>
		<th rowspan="2" align="center" bgcolor=#CCCCCC><b>Jumlah</th>
	</tr>
	<th align="center" bgcolor="#CCCCCC">
	<b><2000</b>
	</th>
	<?php
	while ($w=_fetch_array($r)) {
	echo "<td align=center bgcolor=#CCCCCC><b>$w[TahunID]</td>";
	}
	?>
	<?php
	$s1="select p.FakultasID, p.ProdiID,p.Nama as NMProdi,j.Nama as Prog from prodi p, jenjang j where j.JenjangID=p.JenjangID order by p.FakultasID, p.ProdiID";
	$r1=_query($s1);
	while ($w1=_fetch_array($r1)) {
	$Prodi=$w1['ProdiID'];
	echo "<tr valign='middle' >";
	$currentFak = GetaField('fakultas',"FakultasID",$w1[FakultasID],'Nama');
	if ($savFak != $currentFak) {
	$jmlProdi = GetaField('prodi',"FakultasID", $w1[FakultasID],'COUNT(ProdiID)');
	$nmFakultas = GetaField('fakultas',"FakultasID",$w1[FakultasID],'Nama');
	echo "<td rowspan=$jmlProdi align=center><strong>$nmFakultas</strong></td>";
	$savFak = $currentFak;
	}
	?>
	<td align="center"><?php echo $w1[NMProdi].' '.$w1[Prog].'<br>'.$w1[ProdiID]; ?></td>
		<?php 
		$s2="SELECT count( h.MhswID ) as JML
			FROM khs h, mhsw m
			WHERE m.MhswID = h.MhswID
			AND m.TahunID < 2000
			AND h.TahunID LIKE '$_SESSION[ThnAkd]'
			AND m.ProgramID LIKE '$_SESSION[ProgramID]'
			$_whr
			AND m.ProdiID LIKE '$Prodi'
";
		$r2=_query($s2);
		$Jmldribu = 0;
		while ($w2=_fetch_array($r2)) {
		$Jmldribu = $Jmldribu + $w2[JML];
		} 
		?>
		<td align="center">
		<?php 
		if (!empty($Jmldribu)) { echo "<b>$Jmldribu</b>"; }
		else echo $Jmldribu; 
		?>
		</td>
		<?php

		$tot = 0;
		 	$s="select distinct(left(TahunID,4)) as TahunID from mhsw where TahunID>1999 and TahunID<=$_SESSION[ThnAkd] order by TahunID";
 			$r=_query($s);
		while ($w=_fetch_array($r)) {
			$s2="SELECT count( h.MhswID ) as JML
			FROM khs h, mhsw m
			WHERE m.MhswID = h.MhswID
			AND m.TahunID like '$w[TahunID]%'
			AND h.TahunID LIKE '$_SESSION[ThnAkd]'
			AND m.ProgramID LIKE '$_SESSION[ProgramID]'
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
			if (!empty($Jmlaribu)) { echo "<b>$Jmlaribu</b>"; }
			else echo "$Jmlaribu"; 
			?>
			</td>
			<?php } ?>
			<td align="center">
			<?php echo "<b>$tot</b>"; $_tot=$_tot + $tot;?>
			</td>
		</tr>
		
		<?php } ?>
		<tr bgcolor="#CCCCCC" valign="middle"><td align=center colspan='2'><strong>Jumlah</strong></td>
		<?php
			$s2="SELECT count( h.MhswID ) as JML
			FROM khs h, mhsw m
			WHERE m.MhswID = h.MhswID
			AND m.TahunID < 2000
			AND h.TahunID LIKE '$_SESSION[ThnAkd]'
			AND m.ProgramID LIKE '$_SESSION[ProgramID]'
			$_whr";
			$r2=_query($s2);
			$THNID =$w[TahunID];
			$Jmlaribu = 0;
			while ($w2=_fetch_array($r2)) {
			$Jmlaribu = $w2[JML]+0;
			} 
			?>
			<td align="center"><strong><?php echo "$Jmlaribu"; ?></strong></td>
	<?php 

		$s="select distinct(left(TahunID,4)) as TahunID from mhsw where TahunID>1999 and TahunID<=$_SESSION[ThnAkd] order by TahunID";
 			$r=_query($s);
		while ($w=_fetch_array($r)) {
			$s2="SELECT count( h.MhswID ) as JML
			FROM khs h, mhsw m
			WHERE m.MhswID = h.MhswID
			AND m.TahunID like '$w[TahunID]%'
			AND h.TahunID LIKE '$_SESSION[ThnAkd]'
			AND m.ProgramID LIKE '$_SESSION[ProgramID]'
			$_whr";
			$r2=_query($s2);
			$THNID =$w[TahunID];
			$Jmlaribu = 0;
			while ($w2=_fetch_array($r2)) {
			$Jmlaribu = $w2[JML]+0;
			} 
			?>
			<td align="center"><strong><?php echo $Jmlaribu; ?></strong></td>
			<?php } ?>
			
			
		<td align="center"><?php echo '<b>'.$_tot.'</b>'; ?></td></tr>
		</table>
		<br /><br />
		<?php } ?>