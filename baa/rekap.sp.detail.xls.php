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
	$_SESSION['ProdiID']= $_GET['prd'];
	$_SESSION['MKID'] = $_GET['mk'];
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-mhs-u-sp");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
$mk = GetFields('mk',"MKID",$_SESSION[MKID],'Nama');
	$s1 = "SELECT r.MhswID,m.Nama,r.NilaiAkhir,r.GradeNilai
			FROM krs r,mhsw m
			WHERE r.MKID='$_SESSION[MKID]'
			AND r.TahunID= '$_SESSION[ThnAkd]'
			AND m.ProgramID = '$_SESSION[ProgramID]'
			AND m.MhswID = r.MhswID
			AND r.BobotNilai < 3 order by r.NilaiAkhir";
	
	$r1 = _query($s1);
 ?>
 <style>
table,font { font-family:'Trebuchet MS'; line-height:100%; }
.header{ font-family:'Trebuchet MS'; font-size:15px; line-height:90%; }
.garis {height:0px; line-height:0px;}
</style>
<table border=0>
<tr><td class=header align="center" colspan="5"><strong> BIRO ADMINISTRASI AKADEMIK & KEMAHASISWAAN (BAAK)<BR />
					REKAP MAHASISWA NILAI C KEBAWAH <br>MATAKULIAH: <?php echo $mk[Nama]; ?><br>Tahun Akademik <?php echo $_SESSION['ThnAkd']; ?> Program
					<?php 
					$nmProgram = GetaField('program',"ProgramID",$_SESSION['ProgramID'],'Nama');
					echo $nmProgram; ?></strong></td></tr>
 </table>
 <table border=1 style="border-collapse:collapse" class=bsc width=800>
  	<tr valign="middle" >
	<th align="center" ><b>No.</th>
		<th align="center" ><b>No. BP</th>
		<th align="center" ><b>Nama Mahasiswa</th>
        <th align="center" ><b>Nilai Akhir</th>
        <th align="center" ><b>Nilai Huruf</th>
	</tr>
<?php
$n = 0;
while ($w=_fetch_array($r1)) {
$n++;
echo "<tr>
			<td align=center>$n</td>
			<td align=left>$w[MhswID]</td>
			<td>$w[Nama]</td>
			<td align=center>$w[NilaiAkhir]</td>
			<td align=center>$w[GradeNilai]</td>
			</tr>";
}

	