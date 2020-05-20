<?php session_start();
// Daftar Wisudawan untuk BAAK
// Author	: Arisal Yanuarafi
// Start	: 6 Sept 2013

// *** Parameter ***
$ProdiID = GetSetVar('ProdiID');
$TahunID = GetSetVar('WTahunID');
//$_SESSION['WTahunID'] = $_SESSION['WTahunID'];

// *** GO! ***
TampilkanJudul('Calon Wisudawan');
$opttahun = GetOption2('wisuda', "concat(TahunID,' - ',Nama)", 'TahunID', $_SESSION['WTahunID'],
    "KodeID='".KodeID."'", "TahunID",1); //bila sudah final, hapus comment ini
/*$opttahun = GetOption2('ta', "concat(TahunID)", 'TahunID', $_SESSION['WTahunID'],
    "KodeID='".KodeID."' group by TahunID", "TahunID");	 */
$optprodi = GetProdiUser($_SESSION['_Login'],$ProdiID);
?><form action="?" method="post">
<table class=bsc cellpadding="0" cellspacing="0">
	<tr>
    	<td class='inp'>Tahun Wisuda:</td>
        <td class='ul1'><select name='WTahunID' onChange="this.form.submit()"><?php echo $opttahun?></select></td>
        <td class='inp'>Prodi:</td>
        <td class='ul1'><select name='ProdiID' onChange="this.form.submit()"><?php echo $optprodi?></select></td>
    </tr>
</table></form>
<?php $Printer = (!empty($TahunID))? "<p align=center><a href='baa/wisuda.daftar.valid.xls.php?ProdiID=$ProdiID&WTahunID=$TahunID'><img src='../img/excel.png'> Download</a> (Hanya yg sudah divalidasi Fakultas)</p>" : ''; 

echo $Printer;?>

<table class=bsc cellpadding="0" cellspacing="0" width='100%'>
	<tr>
    	<th class="ttl">#</th>
        <th class="ttl">NPM</th>
        <th class="ttl">Nama</th>
        <th class="ttl">Tempat Lahir</th>
        <th class="ttl">Tanggal Lahir</th>
        <th class="ttl">SKS</th>
        <th class="ttl">IPK</th>
        <th class="ttl">Yudisium</th>
        <th class="ttl">Tgl Yudisium</th>
        <th class="ttl">Judul</th>
        <th class="ttl">Valid<br>?</th>
        <th class="ttl">Bayar<br>?</th>
     </tr>
<?php
$prd = (!empty($ProdiID)) ? "AND m.ProdiID='$ProdiID' ":"";
$s = "SELECT w.MhswID, m.Nama, m.TempatLahir, m.TanggalLahir, m.ProdiID,m.TanggalLahirIjazah, w.TglUjian as TglSidang, m.Predikat, w.Judul, m.IPK, m.TotalSKS, w.Pembimbing, w.Pembimbing2
		from tugasakhir w left outer join mhsw m on m.MhswID=w.MhswID
		where
				w.TahunID='$TahunID'
			$prd
		order by w.TAID DESC";
$r = _query($s);
$n = 0;
while ($w = _fetch_array($r)) {
	$n++;
    $s1 = "SELECT MAX(k.BobotNilai) as BobotNilai,k.SKS from krs k,mk m,kurikulum u where
                    k.NA='N'   
                    AND  m.MKKode=k.MKKode 
                    AND u.KurikulumID=m.KurikulumID 
                    AND u.Nama like 'Kurikulum Default' 
                    AND k.BobotNilai > 0
                    AND k.Tinggi = '*'
                    AND m.NA='N'
                    AND k.MhswID='$w[MhswID]' group by m.Nama";
    $r1 = _query($s1);$bobot=0;$sks=0;
    while ($w1 = _fetch_array($r1)) {
        $bobot += $w1['BobotNilai']*$w1['SKS'];
        $sks += $w1['SKS'];
    }
    $ipk = $bobot/$sks;
    $sks = ($w['ProdiID']=='PGSD') ? HitungSKSPGSD($w['MhswID']) : $sks;
    $ipk = ($w['ProdiID']=='PGSD') ? HitungIPKPGSD($w['MhswID']) : $ipk;
    $Prasyarat = GetaField('wisudawan', "Predikat != '' and TahunID = '$TahunID' and MhswID", $w['MhswID'], "PrasyaratLengkap");
    $Pembayaran = GetaField('bipotmhsw', "NA='N' and BIPOTNamaID='19' and MhswID", $w['MhswID'], "Dibayar");
    $_Pembayaran = ($Pembayaran > 0) ? "<b>Bayar</b>" : ""; 
    $_Prasyarat = ($Prasyarat=='Y') ? "<b>Valid</b>" : "";
	?>
    <tr>
    	<td align="center" valign="middle"><?php echo $n?></td>
        <td align="center" valign="middle"><?php echo $w['MhswID']?></td>
        <td align="left" valign="top"><?php echo $w['Nama']?></td>
        <td align="left" valign="middle"><?php echo ucfirst($w['TempatLahir'])?></td>
        <td align="left" valign="middle"><?php echo (!empty($w['TanggalLahirIjazah']))? $w['TanggalLahirIjazah'] : TanggalFormat($w['TanggalLahir'])?></td>
        <td align="center" valign="middle"><?php echo $sks?></td>
        <td align="center" valign="middle"><?php echo number_format($ipk,2)?></td>
        <td align="center" valign="middle"><?php echo $w['Predikat']?></td>
        <td align="left" valign="middle"><?php echo TanggalFormat($w['TglSidang'])?></td>
        <td align="justify" valign="top"><?php echo FixStr($w['Judul'])?><br>
        								Pembimbing I: <?php echo $w['Pembimbing']?></td>
        <td align="center" valign="middle"><?php echo $_Prasyarat?></td>
        <td align="center" valign="middle"><?php echo $_Pembayaran?></td>
     </tr>
<?php
}
?>
</table>