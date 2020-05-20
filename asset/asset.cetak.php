<?php
session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb2.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

$namafile = "daftar.asset.xls";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=$namafile");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");

//  $lnk = "gos=AssetEdt&md=0&dsnid==AssetID="; 
  // Buat Header:
  $_f = explode(',', $fields);
  $hdr = ''; $brs = '';
  for ($i = 0; $i < sizeof($_f); $i++) {
    $hdr .= "<th class=ttl>". $_f[$i] . "</th>";
    $brs .= "<td class=cna=NA=>=".$_f[$i]."=</td>";
  }
  $whr = array();
  if (!empty($_SESSION['astkeycr']) && !empty($_SESSION['astcr'])) {
    if ($_SESSION['astkeycr'] == 'AssetID') {
			$whr[]  = "asset.$_SESSION[astkeycr] like '$_SESSION[astcr]%'";
		} else $whr[] = "asset.$_SESSION[astkeycr] like '%$_SESSION[astcr]%'";
  }
  $where = implode(' and ', $whr);
  $where = (empty($where))? '' : "and $where";
  $hom = (empty($_SESSION['klp'])) ? '' : "and KelompokID = '$_SESSION[klp]'";
  $hom .= (empty($_SESSION['LokasiID'])) ? $hom : " and asset.LokasiID = '$_SESSION[LokasiID]' ";
  $hom .= (empty($_SESSION['Pemakai'])) ? $hom : "and Pemakai = '$_SESSION[Pemakai]'";
  $hom .= (empty($_SESSION['Tahun'])) ? $hom : "and Tahun = '$_SESSION[Tahun]'";

  
  $s = "select asset.*, format(asset.HargaBeli, 0) as HrgBeli, ls.Nama as Lokasi from asset left outer join lokasiasset ls
    on asset.LokasiID = ls.LokasiID
    where asset.KodeID='$_SESSION[KodeID]' $where $hom
    order by asset.$_SESSION[asturt]";
   
  echo "<table class=box cellspacing=1 border=1 cellpadding=4>
    <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>ID</th>
    <th class=ttl>Inventaris ID</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Jumlah</th>
    <th class=ttl>Satuan</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>Tanggal Beli</th>
    <th class=ttl>Harga Beli</th>
    <th class=ttl>Lokasi</th>
    <th class=ttl>Pemakai</th>
    </tr>";
  $r = _query($s);$n;
  while ($w = _fetch_array($r)){
   ?>
   <tr>
	  <td class=inp1 width=18 align=right><?php echo $n?></td>
    <td class=cna=NA=><?php echo  $w[AssetID];?></a></td>
    <td class=cna=NA=><?php echo $w[InventarisID];?></td>
    <td class=cna=NA=><?php echo $w[Nama];?></td>
    <td class=cna=NA= align=center><?php echo $w[Jumlah];?></td>
    <td class=cna=NA= align=center><?php echo $w[Satuan];?></td>
    <td class=cna=NA= align=center><?php echo $w[Tahun];?></td>
    <td class=cna=NA=><?php echo $w[TglBeli];?></th>
    <td class=cna=NA= align=right><?php echo $w[HrgBeli];?></td>
    <td class=cna=NA=><?php echo (empty($w[Lokasi]) ? $w[LokasiID]:$w[Lokasi]);?></td>
    <td class=cna=NA=><?php echo $w[Pemakai];?></td>
	  </tr>
  <?php }
  echo "</table>";


?>
