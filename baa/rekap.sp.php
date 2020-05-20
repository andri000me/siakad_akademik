<?php 
// Rekap Matakuliah layak Semester Pendek
// Author 	: Arisal Yanuarafi
// Start 	: 17 Juni 2012

  	$TahunID = GetSetVar('ThnAkd');
	$ProgramID = GetSetVar('ProgramID');
	$ProdiID = GetSetVar('ProdiID');
TampilkanJudul("Rekap Matakuliah untuk Semester Pendek");


//option tahun
	 $s="SELECT DISTINCT(TahunID) as TahunID from tahun order by TahunID DESC";
	 $r=_query($s);
	 $optTahun = "<option value=''></option>";
		 while ($w=_fetch_array($r)) {
		 if ($_SESSION['ThnAkd']==$w['TahunID']) {	 		
	 		$optTahun .= "<option value='$w[TahunID]' selected>$w[TahunID]</option>";
		}
		else $optTahun .= "<option value='$w[TahunID]'>$w[TahunID]</option>";
		 }
//option program
	$s="SELECT ProgramID,Nama from program order by ProgramID";
	 $r=_query($s);
	 $optProgram = "<option value=''></option>";
		 while ($w=_fetch_array($r)) {
		 	if ($_SESSION['ProgramID']==$w['ProgramID']) {	 		
	 		$optProgram .= "<option value='$_SESSION[ProgramID]' selected>$_SESSION[ProgramID] - $w[Nama]</option>";
	 		}
		 else $optProgram .= "<option value='$w[ProgramID]'>$w[ProgramID] - $w[Nama]</option>";
		 }
//option prodi
	$s="SELECT ProdiID,Nama from prodi order by ProdiID";
	 $r=_query($s);
	 $optProdi = "<option value=''></option>";
		 while ($w=_fetch_array($r)) {
		 	 if ($_SESSION['ProdiID']==$w['ProdiID']) {	 		
	 		$optProdi .= "<option value='$_SESSION[ProdiID]' selected>$_SESSION[ProdiID] - $w[Nama]</option>";
	 		}
			else $optProdi .= "<option value='$w[ProdiID]'>$w[ProdiID] - $w[Nama]</option>";
		 }
?>
<form action="?" method="post">
<table class="box" width="800">
	<tr><td class="inp">Tahun Akd:</td><td class="ul1"><select name="ThnAkd"><?php echo $optTahun; ?></select></td>
    	<td class="inp">Pilihan Prodi:</td><td class="ul1"> <select name="ProdiID"><?php echo $optProdi; ?></select></td>
  	<td class="inp">Program ID:</td><td class="ul1"><select name="ProgramID"><?php echo $optProgram; ?></select></td>
    	<td class="ul1"><input type="submit" value="Submit"></td></tr></table>      
</form>
<?php 
			$s = "SELECT DISTINCT (k.MKID), k.MKKode, k.Nama AS NamaMK
			FROM krs k,  mk i
			WHERE k.TahunID = '$_SESSION[ThnAkd]'
			AND k.BobotNilai < 3
			AND i.MKID = k.MKID
			AND i.ProdiID = '$_SESSION[ProdiID]'
			AND k.Nama not like 'Praktikum%'
			AND k.Nama not like 'Pratikum%'
			ORDER BY i.ProdiID, k.MKKode";
	$r		=_query($s);
	$num 	=_num_rows($r);
	if ($num>0) {
	$n=0;
	?>
	<table class="bsc" width="800">
    <?php
	echo "<tr><td colspan=8 align=center><a href=baa/rekap.sp.xls.php?thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&prd=$_SESSION[ProdiID]><img src='img/printer2.gif'></a></td></tr>";
	?>
    <tr>
    <th class="ttl" align="center" width="40">No.</th>
    <th class="ttl" align="center" width="120">Kode MK</th>
    <th class="ttl" align="center">Nama Matakuliah</th>
    <th class="ttl" align="center" width="90">Jumlah Mhsw</th>
  
    <?php 
	while ($w=_fetch_array($r)) {
	$s1 = "SELECT count(r.MhswID) as JumlahMhsw
			FROM krs r,mhsw m
			WHERE r.MKID='$w[MKID]'
			AND r.TahunID= '$_SESSION[ThnAkd]'
			AND m.ProgramID = '$_SESSION[ProgramID]'
			AND m.MhswID = r.MhswID
			AND r.BobotNilai < 3";
	
	$r1 = _query($s1);
	while ($w1=_fetch_array($r1)) {
	if ($w1['JumlahMhsw'] > 9) {
	$n++;
	echo "<tr><td class=inp align=center>$n</td>
	<td class=ul1 align=center title='MKID: $w[MKID]'>$w[MKKode]</td>
	<td class=ul1>$w[NamaMK]</td>
	<td class=ul1 align=right><sub>$w1[JumlahMhsw]</sub> <a href=baa/rekap.sp.detail.xls.php?thnakd=$_SESSION[ThnAkd]&prg=$_SESSION[ProgramID]&prd=$_SESSION[ProdiID]&mk=$w[MKID]><img src='img/printer2.gif'></a></td></tr>";
	}
	}
	}
	} ?>
	</tr></table>