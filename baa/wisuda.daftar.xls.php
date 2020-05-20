<?php session_start();
// Daftar Wisudawan untuk BAAK
// Author	: Arisal Yanuarafi
// Start	: 6 Sept 2013
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-calon-wisudawan.xls");
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
td {mso-number-format:"\@";;}
</style>
<?php
// *** Parameter ***
$ProdiID = GetSetVar('ProdiID');
$TahunID = GetSetVar('WTahunID');

// *** GO! ***
?><table class=bsc cellpadding="0" cellspacing="0" border="1">
	<tr>
    	<th class="ttl">#</th>
        <th class="ttl">NPM</th>
        <th class="ttl">Nama</th>
        <th class="ttl">JK</th>
        <th class="ttl">Tempat Lahir</th>
        <th class="ttl">Tanggal Lahir</th>
        <th class="ttl">Nomer Transkrip</th>
        <th class="ttl">Nomer Seri</th>
        <th class="ttl">SKS</th>
        <th class="ttl">IPK</th>
        <th class="ttl">Yudisium</th>
        <th class="ttl">Tgl Yudisium</th>
        <th class="ttl">Judul</th>
        <th class="ttl">Pembimbing I</th>
        <th class="ttl">Pembimbing II</th>
     </tr>
<?php
$whr = (empty($ProdiID) ? "" : " AND m.ProdiID='$ProdiID'  ");
$s = "SELECT w.MhswID, m.Nama,m.Kelamin, m.TempatLahir, m.TanggalLahir,m.TanggalLahirIjazah, w.TglSidang as TglSidang, w.NomerTranskrip, w.NomerSeri, m.Predikat, w.Judul, m.IPK, m.TotalSKS, w.Pembimbing, w.Pembimbing2
		from wisudawan w left outer join mhsw m on m.MhswID=w.MhswID
		where
				w.TahunID='$TahunID'
			$whr
		order by m.ProdiID,m.ProgramID,m.MhswID";
$r = _query($s);
$n = 0;
while ($w = _fetch_array($r)) {
	if (!empty($w['MhswID']) && !empty($w['Nama'])) { $n++;
    $s1 = "SELECT k.BobotNilai,k.SKS from krs k,mk m,kurikulum u where
                    k.NA='N'   
                    AND  m.MKKode=k.MKKode 
                    AND u.KurikulumID=m.KurikulumID 
                    AND u.Nama like 'Kurikulum Default' 
                    AND k.BobotNilai > 0
                    AND k.Final = 'Y'
                    AND k.Tinggi = '*'
                    AND m.NA='N'
                    AND k.MhswID='$w[MhswID]' group by k.Nama";
    $r1 = _query($s1);$bobot=0;$sks=0;
    while ($w1 = _fetch_array($r1)) {
        $bobot += $w1['BobotNilai']*$w1['SKS'];
        $sks += $w1['SKS'];
    }
    $ipk = $bobot/$sks;
	?>
    <tr>
    	<td align="center" valign="middle"><?php echo $n?></td>
        <td align="center" valign="middle" class="text"><?php echo $w['MhswID']?></td>
        <td align="left" valign="top"><?php echo $w['Nama']?></td>
        <td align="left" valign="top"><?php echo ($w['Kelamin']=='P' ? "L":"P"); ?></td>
        <td align="left" valign="middle" class="text"><?php echo ucfirst($w['TempatLahir'])?></td>
        <td align="left" valign="middle" class="text"><?php echo (empty($w['TanggalLahirIjazah'])? TanggalFormat($w['TanggalLahir']):$w['TanggalLahirIjazah']);?></td>
        <td align="center" valign="middle"><?php echo $w['NomerTranskrip']?></td>
        <td align="center" valign="middle"><?php echo $w['NomerSeri']?></td>
        <td align="center" valign="middle"><?php echo $sks?></td>
        <td align="center" valign="middle"><?php echo number_format($ipk,2)?></td>
        <td align="center" valign="middle"><?php echo $w['Predikat']?></td>
        <td align="left" valign="middle" class="text"><?php echo TanggalFormat($w['TglSidang'])?></td>
        <td align="justify" valign="top" width="500"><?php echo FixStr($w['Judul'])?></td>
        <td align="left" valign="middle"><?php echo $w['Pembimbing']?></td>
        <td align="left" valign="middle"><?php echo $w['Pembimbing2']?></td>
     </tr>
<?php
	}
}


?>
</table>