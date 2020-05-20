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
.noborder{border:none;}
.text{
  mso-number-format:"\@";/*force text*/
}
td {mso-number-format:"\@";}
.ttl{text-transform: uppercase;}
</style>
<?php
// *** Parameter ***
$ProdiID = GetSetVar('ProdiID');
$TahunID = GetSetVar('WTahunID');

$Prodi = GetaField('prodi',"ProdiID", $ProdiID,"Nama");
$identitas = GetaField('identitas',"Kode",KodeID,"Nama");
// *** GO! ***
?>

<table class=bsc cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td colspan="6" class="noborder">Lampiran II: Surat Keputusan Rektor <?php echo $identitas;?><br>Nomor:</td>
    </tr>
    <tr><td class="noborder">&nbsp;</td>
    </tr>
    <tr><td class="noborder">&nbsp;</td></tr>
</table><table class=bsc cellpadding="0" cellspacing="0" border="1">
    <tr>
        <td colspan="2">Program Studi</td><td> : <?php echo $ProdiID.' - '.$Prodi;?></td>
    </tr>
    <tr>
        <td colspan="2">Tahun Akademik</td><td> : <?php echo $TahunID;?></td>
    </tr></table>
    <table class=bsc cellpadding="0" cellspacing="0" border="0">
    <tr class="noborder"><td class="noborder" colspan="2">&nbsp;</td>
    </tr></table>
<table class=bsc cellpadding="0" cellspacing="0" border="1">
	<tr>
        <th class="ttl">No</th>
        <th class="ttl">NIM</th>
        <th class="ttl">Nama</th>
        <th class="ttl">IPK</th>
        <th class="ttl">Tgl Masuk</th>
        <th class="ttl">Tgl Yudisium</th>
        <th class="ttl">PIN</th>
        <th class="ttl">NIRL</th>
        <th class="ttl">No. Transkrip</th>
        <th class="ttl">Jenis<br>Kelamin</th>
        <th class="ttl">Tempat Lahir</th>
        <th class="ttl">Tgl Lahir</th>
     </tr>
<?php

$whr = (empty($ProdiID) ? "" : " AND m.ProdiID='$ProdiID'  ");
$periodeID=GetaField('wisuda', "NA",N, 'LEFT( TglWisuda , 4)');
$s = "SELECT w.MhswID,w.PIN, m.Nama,m.FotoWisuda,m.Kelamin, m.TempatLahir, m.TanggalLahir,m.TanggalLahirIjazah, w.TglSidang as TglSidang, 
        w.NomerTranskrip, w.NomerSeri,w.NomorDuduk, m.Predikat, w.Judul, m.IPK, m.TotalSKS, w.Pembimbing, w.Pembimbing2, p.FakultasID,
        m.Alamat,m.Handphone,m.NamaAyah,m.NamaIbu,m.StatusAwalID,
        p.Nama as _PRD,pr.Nama as _PRG, s.Nama as _STS,m.ProdiID,m.ProgramID,p.JenjangID,m.TanggalMasuk,
        SUBSTR( p.FormatNIM, 9, LENGTH( p.FormatNIM ) -15 ) as FormatNIM, j.Nama AS _Jenjang
		from wisudawan w left outer join mhsw m on m.MhswID=w.MhswID
        left outer join prodi p on p.ProdiID=m.ProdiID
        left outer join program pr on pr.ProgramID=m.ProgramID
        left outer join statusawal s on s.StatusAwalID=m.StatusAwalID
        left outer join jenjang j on j.JenjangID=p.JenjangID
		where
				w.TahunID='$TahunID'
                $whr
		order by p.FakultasID,p.Urutan,m.MhswID";
		//order by p.FakultasID,p.Urutan,m.ProgramID DESC,m.MhswID";
$r = _query($s);
$n = 0;
$urutan=0;$NomerTranskrip=7857;$NomerSeri=27062;$FakultasID='';
while ($w = _fetch_array($r)) {
    $n++;
    $yudisium=date_create($w['TglSidang']);
    $lahir=date_create($w['TanggalLahir']);
    $masuk=date_create($w['TanggalMasuk']);
    //echo date_format($date,"Y/m/d H:i:s");
	?>
    <tr>
    	<td align="center" valign="middle"><?php echo $n?></td>
        <td align="center" valign="middle" class="text"><?php echo $w['MhswID']?></td>
        <td align="left" valign="top"><?php echo $w['Nama']?></td>
        <td align="left" valign="top"><?php echo $w['IPK']?></td>
        <td align="left" valign="middle"><?php echo date_format($masuk,"d/m/Y");?></td>
        <td align="left" valign="middle"><?php echo date_format($yudisium,"d/m/Y");?></td>
        <td align="left" valign="middle"><?php echo $w['PIN']?></td>
        <td align="left" valign="middle"><?php echo $w['NomerSeri']?></td>
        <td align="left" valign="middle"><?php echo $w['NomerTranskrip']?></td>
        <td align="center"><?php echo ($w['Kelamin']=='P' ? "L":"P"); ?></td>
        <td align="left" valign="middle"><?php echo $w['TempatLahir']?></td>
        <td align="center" valign="middle"><?php echo date_format($lahir,"d/m/Y")?></td>
     </tr>
<?php
	   //} // cek bayar wisuda
   
}
$pejabat = GetaField('identitas',"Kode",KodeID,"Pejabat");
?>
</table>
<table class=bsc cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="6">&nbsp;</td>
        <td colspan="2"><div style="margin-left:30px">Ditetapkan di Padang<br>
            Pada Tanggal <br>
            Rektor<br><br><br><br><br>
            ( <?php echo $pejabat?> )</div>



