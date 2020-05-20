<?php 
// Rekap Matakuliah layak Semester Pendek
// Author 	: Arisal Yanuarafi
// Start 	: 17 Juni 2012

   include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-mhs-u-sp");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
    $_SESSION['ThnAkd'] = $_GET['thnakd'];
  	$_SESSION['ProgramID']= $_GET['prg'];
	$_SESSION['ProdiID']= $_GET['prd'];


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
 <style>
table,font { font-family:'Trebuchet MS'; line-height:100%; }
.header{ font-family:'Trebuchet MS'; font-size:15px; line-height:90%; }
.garis {height:0px; line-height:0px;}
</style>
<table border=0>
<tr><td class=header align="center" colspan="4"><strong> BIRO ADMINISTRASI AKADEMIK & KEMAHASISWAAN (BAAK)<BR />
					REKAP MAHASISWA NILAI C KEBAWAH JURUSAN <?php
                    $jur = GetFields('prodi',"ProdiID",$_SESSION['ProdiID'],'Nama,JenjangID');
					$jenjang = GetaField('jenjang',"JenjangID",$jur[JenjangID],'Nama');
					echo $jur[Nama].' '.$jenjang; ?><br>Tahun Akademik <?php echo $_SESSION['ThnAkd']; ?> Program
					<?php 
					$nmProgram = GetaField('program',"ProgramID",$_SESSION['ProgramID'],'Nama');
					echo $nmProgram; ?></strong></td></tr>
 </table>
	<table border=1 width="800">
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
	<td class=ul1 align=center>$w1[JumlahMhsw] orang</td></tr>";
	}
	}
	}
	}
 ?>
	</tr></table>