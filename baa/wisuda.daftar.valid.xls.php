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
</style>
<?php
// *** Parameter ***
$ProdiID = GetSetVar('ProdiID');
$TahunID = GetSetVar('WTahunID');

$Prodi = GetaField('prodi',"ProdiID", $ProdiID,"Nama");
$identitas = GetaField('identitas',"Kode",KodeID,"Nama");
// *** GO! ***
?>

<table class=bsc cellpadding="0" cellspacing="0" border="1">
    <tr>
        <td colspan="6" class="noborder">Lampiran II: Surat Keputusan Rektor <?php echo $identitas;?><br>Nomor:</td>
    </tr>
    <tr><td class="noborder">&nbsp;</td>
    </tr>
    <tr><td class="noborder">&nbsp;</td></tr>

    <tr>
        <td colspan="2">Program Studi</td><td> : <?php echo $ProdiID.' - '.$Prodi;?></td>
    </tr>
    <tr>
        <td colspan="2">Tahun Akademik</td><td> : <?php echo $TahunID;?></td>
    </tr>
    <tr><td class="noborder">&nbsp;</td>
    </tr>
	<tr>
        <th class="ttl">No</th>
        <th class="ttl">NPM</th>
        <th class="ttl">Nama</th>
        <th class="ttl">JK</th>
        <th class="ttl">Tempat Lahir</th>
        <th class="ttl">Tgl Lahir</th>
        
        <th class="ttl">Nomor Transkrip</th>
        <th class="ttl">NIRL</th>
        <th class="ttl">Yudisium</th>
        <th class="ttl">Tgl Yudisium</th>
        <th class="ttl">Judul</th>
        <th class="ttl">Pembimbing I</th>
        <th class="ttl">Pembimbing II</th>
        <th class="ttl">Alamat</th>
        <th class="ttl">Handphone</th>
        <th class="ttl">Nama Ayah</th>
        <th class="ttl">Nama Ibu</th>
     </tr>
<?php
//if ($_SESSION['_LevelID']==1){ $update = _query("UPDATE wisudawan set NomorRegister2='',NomorRegister1='',Seri='' where TahunID='$TahunID'"); }

$whr = (empty($ProdiID) ? "" : " AND m.ProdiID='$ProdiID'  ");
$periodeID=GetaField('wisuda', "NA",N, 'LEFT( TglWisuda , 4)');
$s = "SELECT w.MhswID, m.Nama,m.FotoWisuda,m.Kelamin, m.TempatLahir, m.TanggalLahir,m.TanggalLahirIjazah, w.TglSidang as TglSidang, 
        w.NomerTranskrip, w.NomerSeri,w.NomorDuduk, m.Predikat, w.Judul, m.IPK, m.TotalSKS, w.Pembimbing, w.Pembimbing2, p.FakultasID,
        m.Alamat,m.Handphone,m.NamaAyah,m.NamaIbu,m.StatusAwalID,
        p.Nama as _PRD,pr.Nama as _PRG, s.Nama as _STS,m.ProdiID,m.ProgramID,p.JenjangID,
        SUBSTR( p.FormatNIM, 9, LENGTH( p.FormatNIM ) -15 ) as FormatNIM, j.Nama AS _Jenjang
		from wisudawan w left outer join mhsw m on m.MhswID=w.MhswID
        left outer join prodi p on p.ProdiID=m.ProdiID
        left outer join program pr on pr.ProgramID=m.ProgramID
        left outer join statusawal s on s.StatusAwalID=m.StatusAwalID
        left outer join jenjang j on j.JenjangID=p.JenjangID
		where
				w.TahunID='$TahunID'
                $whr
		order by p.FakultasID,p.Urutan,FIELD(m.ProgramID,'R','N'),m.MhswID";
		//order by p.FakultasID,p.Urutan,m.ProgramID DESC,m.MhswID";
$r = _query($s);
$n = 0;
$urutan=0;$NomerTranskrip=7857;$NomerSeri=27062;$FakultasID='';
while ($w = _fetch_array($r)) {
    //$ck_bayar = GetaField('bipotmhsw',"NA='N' and Nama='Uang Wisuda' and MhswID",$w['MhswID'],"TahunID")+0;
   	//if ($ck_bayar > 0 || $w['FakultasID']=='08'){
	if (!empty($w['MhswID']) && !empty($w['Nama'])) { $n++;
    $s1 = "SELECT MAX(k.BobotNilai) as BobotNilai,m.SKS from krs k,mk m,kurikulum u where
                    k.NA='N'   
                    AND  m.MKKode=k.MKKode 
                    AND u.KurikulumID=m.KurikulumID 
                    AND u.Nama like 'Kurikulum Default' 
                    AND k.BobotNilai > 0
                    AND k.Tinggi = '*'
                    AND m.NA='N'
                    AND k.MhswID='$w[MhswID]' group by k.Nama";
    $r1 = _query($s1);$bobot=0;$sks=0;
    while ($w1 = _fetch_array($r1)) {
        $bobot += $w1['BobotNilai']*$w1['SKS'];
        $sks += $w1['SKS'];
    }
    $ipk = $bobot/$sks;
    $ipk = number_format($ipk,2,".", ",");
    //$sks = ($w['ProdiID']=='PGSD') ? HitungSKSPGSD($w['MhswID']) : $sks;
    //$ipk = ($w['ProdiID']=='PGSD') ? HitungIPKPGSD($w['MhswID']) : $ipk;
	
    //Proses No. Ijazah by Arisal Yanuarafi
        if ($FakultasID!=$w['FakultasID']){
        	$urutan=1;
        	$FakultasID = $w['FakultasID'];
        }else {  $urutan++; }
        
        /*
        $NomorSeri = GetaField('wisudawan w left outer join mhsw m on m.MhswID=w.MhswID left outer join prodi p on p.ProdiID=m.ProdiID', "p.JenjangID",$w['JenjangID'],"MAX(Seri)")+1;
        $NomorRegister1 = GetaField('wisudawan w left outer join mhsw m on m.MhswID=w.MhswID left outer join prodi p on p.ProdiID=m.ProdiID', "p.JenjangID='$w[JenjangID]' and p.FakultasID",$w['FakultasID'],"MAX(NomorRegister1)")+1;
        $NomorRegister2 = GetaField('wisudawan w left outer join mhsw m on m.MhswID=w.MhswID', "m.ProgramID='$w[ProgramID]' and ProdiID",$w['ProdiID'],"MAX(NomorRegister2)")+1;
        $_NomorSeri = str_pad($NomorSeri, 5,"0",STR_PAD_LEFT);
        $_NomorRegister1 = str_pad($NomorRegister1, 4,"0",STR_PAD_LEFT);
        $_NomorRegister2 = str_pad($NomorRegister2, 4,"0",STR_PAD_LEFT);
        $Mandiri = ($w['ProgramID']=='M') ? ".5":"";
        $PPKHB = ($w['ProgramID']=='P') ? ".P":"";
        $nomertranskrip = $_NomorRegister1.'/T.'.$w['_Jenjang'].'.'.$w['FormatNIM'].$Mandiri.$PPKHB.'.'.$_NomorRegister2.'/'.$periodeID;
        $nomerseri = "T. $_NomorSeri";
        if ($_SESSION['_Login']=='auth0rized'){ $update = _query("UPDATE wisudawan set NomerTranskrip='$nomertranskrip', NomerSeri='$nomerseri', Seri='$NomorSeri', NomorDuduk='$urutan',NomorRegister2='$_NomorRegister2',NomorRegister1='$_NomorRegister1' where MhswID='$w[MhswID]'"); }
        //if ($_SESSION['_Login']=='auth0rized'){ $update = _query("UPDATE wisudawan set NomerSeri='$nomerseri',Seri='$NomorSeri', IPK='$ipk' where MhswID='$w[MhswID]'"); }
        */
	?>
    <tr>
    	<td align="center" valign="middle"><?php echo $n?></td>
        <td align="center" valign="middle" class="text"><?php echo $w['MhswID']?></td>
        <td align="left" valign="top"><?php echo $w['Nama']?></td>
        <td align="left" valign="top"><?php echo ($w['Kelamin']=='P' ? "L":"P"); ?></td>
        <td align="left" valign="middle" class="text"><?php echo ucfirst($w['TempatLahir'])?></td>
        <td align="left" valign="middle" class="text"><?php echo TanggalFormat($w['TanggalLahir']); ?></td>
        <td align="center" valign="middle"><?php echo $w['NomerTranskrip'] ?></td>
        <td align="center" valign="middle"><?php echo $w['NomerSeri'] ?></td>
        <td align="center" valign="middle"><?php echo $w['Predikat']?></td>
        <td align="left" valign="middle" class="text"><?php echo TanggalFormat($w['TglSidang'])?></td>
        <td align="justify" valign="top" width="500"><?php echo $w['Judul']?></td>
        <td align="left" valign="middle"><?php echo $w['Pembimbing']?></td>
        <td align="left" valign="middle"><?php echo $w['Pembimbing2']?></td>
        <td align="left" valign="middle"><?php echo $w['Alamat']?></td>
        <td align="left" valign="middle"><?php echo $w['Handphone']?></td>
        <td align="left" valign="middle"><?php echo $w['NamaAyah']?></td>
        <td align="left" valign="middle"><?php echo $w['NamaIbu']?></td>
        
     </tr>
<?php
	   //} // cek bayar wisuda
   }
}
?>
</table>