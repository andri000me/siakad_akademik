<?php
// Author : Arisal Yanuarafi	
// Email  : arisal.yanuarafi@yahoo.com	
// Start  : 27 September 2012

// *** Parameters ***
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
$_TahunID = GetSetVar('TahunID');
$_ProgramID = GetSetVar('_ProgramID');
$_TProdiID = GetSetVar('_TProdiID');

// *** Main ***
$namafile = "KRS_Nol_Bayar.xls";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=$namafile");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");

	
$whrProdi=(empty($_SESSION['_TProdiID']))? '' : "and m.ProdiID = '$_SESSION[_TProdiID]' ";
  $s = "SELECT h.KHSID,h.MhswID,m.Nama,m.TahunID,h.Biaya,h.Potongan,h.Bayar
FROM khs h, mhsw m
WHERE m.MhswID = h.MhswID
AND h.TahunID = '$_SESSION[TahunID]'
AND h.Bayar =0
AND h.Potongan =0
$whrProdi
AND m.ProgramID = '$_SESSION[_ProgramID]'";
	
	
  $r = _query($s); $n=0;
  $dsn = 'laskdjfoaiurhfasdlasdkjf';
  $jmlrow= _num_rows($r);
  
if ($jmlrow>0) {	  
  echo "<table class=box cellspacing=1  border=1 align=center width=800>
  
  <form action='$_SESSION[mnux].hapus.php' method=POST target='_blank'>";
while ($w = _fetch_array($r)) {
    $n++;
	$id=$w[MhswID]+0;

 	$tr = "<tr>";

?>
    <tr class=ttl><th bgcolor="#CCCCCC">No</th>
    <th bgcolor="#CCCCCC">No. BP</th>
    <th bgcolor="#CCCCCC">Nama</th>
    <th bgcolor="#CCCCCC">Thn Masuk</th>
    <th bgcolor="#CCCCCC">Total Bayar<br />Semua Sesi</th>
    <th bgcolor="#CCCCCC">Biaya<br />Sesi ini</th>
    <th bgcolor="#CCCCCC">Potongan<br />Sesi ini</th>
    <th bgcolor="#CCCCCC">Bayar<br />Sesi ini</th>
	</tr>

<?php
	echo $tr;
	echo "	<input type=hidden name='mhswid[]' value='$n' />
      	<input type=hidden name='Mhsw_$n' value='$w[MhswID]' />
		<input type=hidden name='khsid[]' value='$n' />
		<input type=hidden name='KHSID_$n' value='$w[KHSID]' />
		<input type=hidden name='tahunid[]' value='$n' />
		<input type=hidden name='TahunID_$n' value='$_SESSION[TahunID]' />
		";
$totBayar= GetaField ("khs","MhswID",$w['MhswID'],'sum(Bayar)');
$_totBayar = number_format($totBayar, 0, ',', '.');
$biaya = number_format($w[Biaya], 0, ',', '.');
      echo "<td align=center width=25>$n</td>";
echo " <td  class=ul>$w[MhswID]
        </td><td>$w[Nama]</td><td  class=ul align=center>$w[TahunID]</td><td  class=ul align=right>$_totBayar</td><td  class=ul align=right>$biaya</td><td  class=ul align=right>$w[Potongan]</td><td  class=ul align=right>$w[Bayar]</td>";
echo "</tr>";
$s2 = "Select k.MKKode,k.JadwalID,m.Nama,m.SKS,GradeNilai,k.TanggalEdit from krs k,mk m where k.MhswID='$w[MhswID]' and k.TahunID='$_SESSION[TahunID]' and m.MKID=k.MKID";
$r2=_query($s2);
$jrow=_num_rows($r2);
$_jrow=$jrow+1;
if (!empty($jrow)){
echo "<tr><td colspan=8><table align=center class=box >
		<tr><td rowspan=$_jrow valign=middle class=ul><b><u>KRS yang diambil :</u></b></td><th>MKKode</th><th>Nama MK</th><th>SKS</th><th>Nilai</th><th>Nama Dosen</th></tr>
		";
	while ($w2= _fetch_array($r2)) {	
	$jdwlDosenID = GetaField ('jadwal',"JadwalID",$w2['JadwalID'],'DosenID');
	$nmDosen=GetFields("dosen","NIDN",$jdwlDosenID,'Nama,Gelar');
	echo "<tr title='Tanggal Terakhir Edit KRS: $w2[TanggalEdit]'><td class=ul>$w2[MKKode]</td><td class=ul>$w2[Nama]</td><td class=ul align=center>$w2[SKS]</td><td class=ul align=center>$w2[GradeNilai]</td><td class=ul align=right><i>$nmDosen[Nama]</i>  <sup>$nmDosen[Gelar] </sup></td></tr>";
	  }
  echo "</table>";
  }
  else { echo "<tr><td colspan=8 align=center>
		<font color=red>tidak ada data KRS </font></td></tr>"; }
  }
  echo "</td></tr></table>";
  echo "</form>";
  }


?>
