<?php session_start();
// Daftar Wisudawan untuk BAAK
// Author   : Arisal Yanuarafi
// Start    : 6 Sept 2013

// *** Parameter ***
$ProdiID = GetSetVar('ProdiID');
$TahunID = GetSetVar('WTahunID');
//$_SESSION['WTahunID'] = $_SESSION['WTahunID'];

// *** GO! ***
TampilkanJudul('Calon Wisudawan');
$opttahun = GetOption2('wisuda', "concat(TahunID,' - ',Nama)", 'TahunID', $_SESSION['WTahunID'],
    "KodeID='".KodeID."'", "TahunID",1); //bila sudah final, hapus comment ini
/*$opttahun = GetOption2('ta', "concat(TahunID)", 'TahunID', $_SESSION['WTahunID'],
    "KodeID='".KodeID."' group by TahunID", "TahunID");  */
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
<?php $Printer = (!empty($TahunID))? "<p align=center><a href='baa/wisuda.daftar.valid.xls.php?ProdiID=$ProdiID&WTahunID=$TahunID'><img src='../img/excel.png'> Download</a> || <a href='baa/wisuda.daftar.valid.SK.php?ProdiID=$ProdiID&WTahunID=$TahunID'><img src='../img/excel.png'> SK Predikat</a>
|| <a href='baa/wisuda.daftar.valid.NIRL.php?ProdiID=$ProdiID&WTahunID=$TahunID'><img src='../img/excel.png'> SK NIRL</a></p>" : ''; 

echo $Printer;?>

<table class=bsc cellpadding="0" cellspacing="0">
    <tr>
        <th class="ttl">#</th>
        <th class="ttl">NPM</th>
        <th class="ttl">Nama</th>
        <th class="ttl">Tempat Lahir</th>
        <th class="ttl">Tanggal Lahir</th>
        <th class="ttl">Yudisium</th>
        <th class="ttl">Tgl Yudisium</th>
        <th class="ttl">Judul</th>
     </tr>
<?php
$s = "SELECT w.MhswID, m.Nama,m.Foto,m.Kelamin, m.TempatLahir, m.TanggalLahir,m.TanggalLahirIjazah, w.TglSidang as TglSidang, w.NomerTranskrip, w.NomerSeri, m.Predikat, w.Judul, m.IPK, m.TotalSKS, w.Pembimbing, w.Pembimbing2, p.FakultasID,p.Nama as _PRD,pr.Nama as _PRG, s.Nama as _STS,m.ProdiID
        from wisudawan w left outer join mhsw m on m.MhswID=w.MhswID
        left outer join prodi p on p.ProdiID=m.ProdiID
        left outer join program pr on pr.ProgramID=m.ProgramID
        left outer join statusawal s on s.StatusAwalID=m.StatusAwalID
        where
                w.TahunID='$TahunID'
                and m.ProdiID='$ProdiID'
        order by p.FakultasID,p.KodeLama,m.ProgramID DESC,m.MhswID";
$r = _query($s);
$n = 0;
while ($w = _fetch_array($r)) {
    $n++;
    $s1 = "SELECT max(k.BobotNilai) as BobotNilai,m.SKS from krs k,mk m,kurikulum u where
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
    $sks = ($w['ProdiID']=='PGSD') ? HitungSKSPGSD($w['MhswID']) : $sks;
    $ipk = ($w['ProdiID']=='PGSD') ? HitungIPKPGSD($w['MhswID']) : $ipk;

    
    ?>
    <tr>
        <td align="center" valign="middle"><?php echo $n?></td>
        <td align="center" valign="middle"><?php echo $w['MhswID']?></td>
        <td align="left" valign="top"><?php echo $w['Nama']?></td>
        <td align="left" valign="middle"><?php echo ucfirst($w['TempatLahir'])?></td>
        <td align="left" valign="middle"><?php echo (!empty($w['TanggalLahirFinal']))? $w['TanggalLahirFinal'] : TanggalFormat($w['TanggalLahir'])?></td>
        <td align="center" valign="middle"><?php echo $w['Predikat']?></td>
        <td align="left" valign="middle"><?php echo TanggalFormat($w['TglSidang'])?></td>
        <td align="justify" valign="top"><?php echo FixStr($w['Judul'])?><br>
                                        Pembimbing I: <?php echo $w['Pembimbing']?></td>
        <td align="center" valign="middle"><?php echo $_urutan?></td>
        <td align="center" valign="middle"><?php echo $nomertranskrip?></td>
        <td align="center" valign="middle"><?php echo $nomerseri?></td>
     </tr>
<?php

}
?>
</table>