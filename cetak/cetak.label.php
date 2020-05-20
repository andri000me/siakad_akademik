<?
include "../sisfokampus.php";
function TampilkanJenisLabel(){
  echo "<script type=\"text/javascript\">
        function mhsw(){
          $('a#amhsw').hide();
          $('p#mhsw').show('slow');
        }
        </script>";
  $chck1 = '';
  $chck2 = '';
  
  if ($_SESSION['alamat'] == 1)  $chck1 = "checked";
  else  $chck2 = "checked";
  CheckFormScript("DariNPM,SampaiNPM");
	echo "<p><table class=box cellpadding=4 cellspacing=1>
				<tr><th class=ttl>#</th><th class=ttl>Jenis Label</th><th class=ttl>Cetak</th></tr>
				<tr><td class=inp>1.</td><td class=ul>Label Map untuk Absen</td><td class=ul><a href=cetak/cetak.label.jdwl.php?tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]><img src=img/printer.gif></a></td></tr>
				<tr><td class=inp>2.</td><td class=ul>Label untuk Disket Nilai</td><td class=ul><a href=cetak/cetak.label.disket.php?tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]><img src=img/printer.gif></a></td></tr>
				<tr><td class=inp>3.</td><td class=ul>Label Untuk Mhsw</td><td class=ul><a href='javascript:mhsw()' id='amhsw'><img src=img/printer.gif></a>
        
        <p class=ul colspan=3 style='display : none' id='mhsw'><table class=box cellspacing=1 cellpadding=4>
        <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
        <input type=hidden name='mnux' value='cetak.label.mhsw'>
        <input type=hidden name='gos' value='CetakLabelMHSW'>
        <input type=hidden name='tahun' value='$_SESSION[tahun]'>
        <tr>
        <td class=inp>Dari NPM :</td>
        <td class=ul><input type=text name='DariNPM' value='$_SESSION[DariNPM]' size=20 maxlength=50></td>
        <td class=inp>Sampai NPM :</td>
        <td class=ul><input type=text name='SampaiNPM' value='$_SESSION[SampaiNPM]' size=20 maxlength=50></td>
        <td class=ul><input type=radio name=alamat value=1 $chck1>Alamat&nbsp<input type=radio name=alamat value=0 $chck2>Tanpa Alamat</td>
        <td class=ul><input type=submit Name='Cetak' value='Cetak'></td>
        </form></table></p>
        
        </td></tr>
        </table></p>";
}


//Parameter
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$alamat = GetSetVar('alamat', 1);
$DariNPM = GetSetVar('DariNPM');
$SampaiNPM = GetSetVar('SampaiNPM');

//Tampilkan
TampilkanJudul('Cetak Label');
TampilkanTahunProdiProgram('cetak.label', '');
//$gos = (empty($_SESSION['gos'])) 
if (!empty($_SESSION['tahun']) && !empty($_SESSION['prodi']) && !empty($_SESSION['prid'])) {
TampilkanJenisLabel();
//CetakLabelJadwal();
}

?>
