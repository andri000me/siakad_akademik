<?php session_start();
 
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-mahasiswa.xls");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
?>
<style>
table,font { font-family:'Calibri'; line-height:100%; }
.header,ttl{ font-family:'Calibri'; font-size:14px; line-height:90%; }
.garis {height:0px; line-height:0px;}
.text{
  mso-number-format:"\@";/*force text*/
}
</style>
<?php
// *** Parameter ***
$query = $_POST['query'];

// *** GO! ***
?><table class=bsc cellpadding="0" cellspacing="0" border="1">
	<tr>
    	<th class="ttl">#</th>
        <th class="ttl">Prodi</th>
        <th class="ttl">NPM</th>
        <th class="ttl">Nama</th>
        <th class="ttl">JK</th>
        <th class="ttl">Tempat Lahir</th>
        <th class="ttl">Tanggal Lahir</th>
        <th class="ttl">Telepon</th>
        <th class="ttl">Handphone</th>
        <th class="ttl">Email</th>
        <th class="ttl">Alamat</th>
        <th class="ttl">Status Awal</th>
        <th class="ttl">Status Mahasiswa</th>
        <th class="ttl">Status Mahasiswa</th>
     </tr>
<?php
$s = "SELECT m.MhswID, m.Nama, m.StatusAwalID, m.StatusMhswID,
    m.Kelamin,
    m.Telepon, m.Handphone, m.Email, 
    m.ProgramID, m.ProdiID, m.Alamat, m.Kota, m.AsalSekolah,
   m.TempatLahir,date_format(m.TanggalLahir,'%d-%m-%Y') as TanggalLahir
	from mhsw m
	$query";
$r = _query($s);
$n = 0;
while ($w = _fetch_array($r)) {
	$n++;
	$StatusAwal = GetaField('statusawal',"StatusAwalID", $w['StatusAwalID'],"Nama");
	$StatusMhsw = GetaField('statusmhsw',"StatusMhswID", $w['StatusMhswID'],"Nama");
	?>
    <tr>
    	<td align="center" valign="top"><?php echo $n?></td>
        <td align="left" valign="top"><?php echo $w['ProgramID'].' - '.$w['ProdiID']?></td>
        <td align="center" valign="top" class="text"><?php echo $w['MhswID']?></td>
        <td align="left" valign="top"><?php echo $w['Nama']?></td>
        <td align="left" valign="top"><?php echo $w['Kelamin']?></td>
        <td align="left" valign="top"><?php echo ucfirst($w['TempatLahir'])?></td>
        <td align="left" valign="top" class="text"><?php echo $w['TanggalLahir']?></td>
        <td align="center" valign="top" class="text"><?php echo $w['Telepon']?></td>
        <td align="center" valign="top" class="text"><?php echo $w['Handphone']?></td>
        <td align="center" valign="top" class="text"><?php echo $w['Email']?></td>
        <td align="left" valign="top"><?php echo $w['Alamat']?></td>
        <td align="left" valign="top"><?php echo $StatusAwal; ?></td>
        <td align="left" valign="top"><?php echo $StatusMhsw;?></td>
        <td align="left" valign="top"><?php echo $w['AsalSekolah']?></td>
     </tr>
<?php
}
?>
</table>