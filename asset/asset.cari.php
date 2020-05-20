<?php
function tampilkancariasset($mnux='asset/asset', $add=1){
  global $arrID;
	$optkel = GetOption2("kelompokasset", "concat(KelompokID, ' - ', Nama)", "KelompokID", $_SESSION['klp'], '', 'KelompokID');
    $ck_nama = ($_SESSION['asturt'] == 'Nama')? 'checked' : '';
    $ck_id = ($_SESSION['asturt'] == 'AssetID')? 'checked' : '';
    $s = "SELECT Tahun from asset group by Tahun order by Tahun DESC";
    $r = _query($s);$optTahun='<option></option>';
    while ($w = _fetch_array($r)){
      $sl = ($w['Tahun']==$_SESSION['Tahun'])? "selected":"";
      $optTahun .= "<option value='$w[Tahun]' $sl>$w[Tahun]</option>";
    }
    $s = "SELECT Pemakai from asset group by Pemakai order by Pemakai DESC";
    $r = _query($s);$optPemakai='<option></option>';
    while ($w = _fetch_array($r)){
      $sl = ($w['Pemakai']==$_SESSION['Pemakai'])? "selected":"";
      $optPemakai .= "<option value='$w[Pemakai]' $sl>$w[Pemakai]</option>";
    }
    $stradd = ($add == 0)? '' : "<tr><td class=ul>Pilihan:</td>
    <td class=ul><a href='?mnux=asset/asset&gos=AssetEdt&md=1' class='btn btn-info'>Tambah Asset</a> <a href='asset/asset.cetak.php' class='btn btn-primary' target='_blank'>Cetak</a></td></tr>";

  $lks = GetOption2('lokasiasset', "concat(LokasiID, ' - ', Nama)", 'LokasiID', $_SESSION['LokasiID'], '',   'LokasiID');
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='asset/asset'>
  <input type=hidden name='astpage' value='1'>
  <tr><td class=ul colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp>Cari Asset:</td>
  <td class=ul><input type=text name='astcr' value='$_SESSION[astcr]' size=18 maxlengh=10>
    <input type=submit name='astkeycr' value='Nama'>
    <input type=submit name='astkeycr' value='AssetID'>
    <input type=submit name='astkeycr' value='Reset'></td></tr>
  <tr><td class=inp>Urut berdasarkan:</td><td class=ul>
    <input type=radio name='asturt' value='AssetID' $ck_id>ID
    <input type=radio name='asturt' value='Nama' $ck_nama> Nama
    <input type=submit name='Urutkan' value='Urutkan'></td></tr>
  <tr><td class=inp> Kelompok :</td><td class=ul><select name='klp' OnChange='this.form.submit()'>$optkel</select></tr>
  <tr><td class=inp> Tahun :</td><td class=ul><select name='Tahun' OnChange='this.form.submit()'>$optTahun</select></tr>
  <tr><td class=inp> Lokasi:</td><td class=ul><select name='LokasiID' OnChange='this.form.submit()'>$lks</select>
  <tr><td class=inp> Pemakai :</td><td class=ul><select name='Pemakai' OnChange='this.form.submit()'>$optPemakai</select></tr>
  $stradd
  </form></table></p>";

}

// =======================
function DaftarAst($mnux='', $lnk='', $fields='') {
  global $_defmaxrow, $_FKartuUSM;
  include_once "class/dwolister.class.php";
  
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

  $lst = new dwolister;
  $lst->maxrow = 20;
  $lst->page = $_SESSION['astpage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$mnux&gos=&astpage==PAGE='>=PAGE=</a>";
  $lst->tables = "asset left outer join lokasiasset ls
    on asset.LokasiID = ls.LokasiID
    where asset.KodeID='$_SESSION[KodeID]' $where $hom
    order by asset.$_SESSION[asturt]";
  $lst->fields = "asset.*, format(asset.HargaBeli, 0) as HrgBeli, ls.Nama as Lokasi ";
  $lst->headerfmt = "<p><table class=box cellspacing=1 border=0 cellpadding=4>
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
	  <th class=ttl>NA</th>
	  <th class=ttl>Cetak</th>
    </tr>";
  $lst->detailfmt = "<tr>
	  <td class=inp1 width=18 align=right>=NOMER=</td>
    <td class=cna=NA=><a href=\"?mnux=$mnux&$lnk\"><img src='img/edit.png' border=0>&nbsp;=AssetID=</a></td>
    <td class=cna=NA=>=InventarisID=</td>
    <td class=cna=NA=>=Nama=</td>
    <td class=cna=NA= align=center>=Jumlah=</td>
    <td class=cna=NA= align=center>=Satuan=</td>
    <td class=cna=NA= align=center>=Tahun=</td>
    <td class=cna=NA=>=TglBeli=</th>
    <td class=cna=NA= align=right>=HrgBeli=</td>
    <td class=cna=NA=>=Lokasi=</td>
    <td class=cna=NA=>=Pemakai=</td>
	  <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
	  <td class=cna=NA=><a href=\"$mnux.laporan.php?AssetID==AssetID=\" target='_blank'>Cetak</a></td>
	  </tr>";
  $lst->footerfmt = "</table></p>";
  echo $lst->TampilkanData();
  $halaman = $lst->TampilkanHalaman();
  $total = $lst->MaxRowCount;
  $total = number_format($total);
  echo "<p>Halaman : " . $halaman . "<br />" .
    "Total: ". $total . "</p>";
}

?>
