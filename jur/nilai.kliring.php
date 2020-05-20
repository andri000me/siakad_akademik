<?php

// ===========================================
// Created on 24 Juli 2016 by Arisal Yanuarafi
// ===========================================
error_reporting(0);

// *** Parameters ***
$_kliringProdi = GetSetVar('_kliringProdi');
$_kliringMhswID = GetSetVar('_kliringMhswID');
$_kliringKurikulum = GetSetVar('_kliringKurikulum');

$mhsw = GetFields("mhsw m
      left outer join dosen d on m.PenasehatAkademik = d.Login and d.KodeID='".KodeID."'
      left outer join prodi prd on prd.ProdiID = m.ProdiID and prd.KodeID='".KodeID."'
      left outer join program prg on prg.ProgramID = m.ProgramID and prg.KodeID='".KodeID."'",
    "m.KodeID='".KodeID."' and m.ProdiID='$_SESSION[_kliringProdi]' and m.MhswID", $_kliringMhswID,
      "m.*, prd.Nama as _PRD, prg.Nama as _PRG,
      d.Nama as DSN, d.Gelar");
//if ($_SESSION['_LevelID']==100 && $mhsw['PenasehatAkademik']!=$_SESSION['_Login']) die('Maaf, data yang dimaksud tidak tersedia');

// *** Main ***
TampilkanJudul("Kliring Nilai");
TampilkanHeader($mhsw);
RandomStringScript();
// validasi
if (!empty($_kliringProdi) && !empty($_kliringMhswID)) {
  $gos = (empty($_REQUEST['gos']))? 'PilihKurikulum' : $_REQUEST['gos'];
  $gos($mhsw);
}

// *** Functions ***
function TampilkanHeader($w) {
  $optprodi = ($_SESSION['_LevelID'] == 100)?
   			GetProdiUser2($_SESSION['_Login'], $_SESSION['_kliringProdi']) : 
   			GetProdiUser($_SESSION['_Login'], $_SESSION['_kliringProdi']);
  
  echo <<<SCR
  <table class=box cellspacing=1 align=center width=960>
  <form name='frmKliring' action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  
  <tr><td class=wrn width=2 rowspan=4></td>
  <td class=inp>Program Studi:</td>
      <td class=ul1><select name='_kliringProdi'>$optprodi</select></td>
      <td class=inp>NPM:</td>
      <td class=ul1><input type='text' value='$_SESSION[_kliringMhswID]' name='_kliringMhswID' size=30 maxlength=13> 
      <input type=submit name='btnKirim' value='Cari Data' /></td>
      </tr>
  <tr><td class=inp>Nama:</td>
      <td class=ul1 colspan=3>$w[Nama]</td>
      </tr>
  <tr><td class=inp>Program Studi:</td>
      <td class=ul>$w[_PRD] <sup>$w[ProdiID]</sup>&nbsp;</td>
      <td class=inp>Prg. Pendidikan:</td>
      <td class=ul>$w[_PRG] <sup>$w[ProgramID]</sup>&nbsp;</td>
      </tr>
  <tr><td class=inp>Penasehat Akd:</td>
      <td class=ul>$w[DSN] <sup>$w[Gelar]</sup>&nbsp;</td>
      <td class=inp>Masa Studi:</td>
      <td class=ul>$w[TahunID] &#8594; $w[BatasStudi]</td>
      </tr>
  </form>
  </table>
SCR;
}
function PilihKurikulum($w){
  echo "<form name='frmKliring' action='?' method=POST><table class=box cellspacing=1 align=center width=960>";
  $s6 = "select KurikulumID,KurikulumKode,Nama
    from kurikulum
    where ProdiID = '$w[ProdiID]' order by Nama";
$r6 = _query($s6);
    $optkurikulum = "<option value=''></option>";
    while($w6 = _fetch_array($r6))
    {  $ck = ($w6['KurikulumID'] == $_SESSION['_kliringKurikulum'])? "selected" : '';
       $optkurikulum .=  "<option value='$w6[KurikulumID]' $ck>$w6[Nama]</option>";
    }
    $_inputKurikulum = "<select name='_kliringKurikulum' onChange='this.form.submit()'>$optkurikulum</select>";
    echo "<tr><td class='inp'>Pilih Kurikulum</td><td class='ul1'>$_inputKurikulum</td></tr></table></form>";

  if (!empty($_SESSION['_kliringKurikulum'])){
    TampilkanKliring($w);
  }
}
function TampilkanKliring($mhsw){
$s = "SELECT m.MKKode,m.Nama,k.GradeNilai,m.SKS,m.MKSetara,m.MKID, k.KRSID from mk m left outer join krs k on k.MKID = m.MKID and k.NA='N' AND  k.Final='Y' and k.Tinggi='*' and k.BobotNilai > 0 and k.MhswID = '$_SESSION[_kliringMhswID]'
            where m.KurikulumID='$_SESSION[_kliringKurikulum]' and m.ProdiID='$_SESSION[_kliringProdi]' and m.NA='N' order by Sesi,MKKode";
$r = _query($s);
  ?>
  <a href='<?php echo $_SESSION['mnux']?>.xls.php' target='_blank'>Cetak</a>
<table width="100%">
<tr>
<th class='ttl' colspan=5>Matakuliah Lama</th>
<th class='ttl' colspan=6>Matakuliah Baru</th>
</tr>
<tr>
<th class='ttl' width="3">No.</th>
<th class='ttl' width="90">Kode MK</th>
<th class='ttl'>Nama</th>
<th class='ttl' width="10">SKS</th>
<th class='ttl' width="3">NL</th>
<th class='ttl' width="90">Kode MK</th>
<th class='ttl' width="380">Nama</th>
<th class='ttl' width="10">SKS</th>
<th class='ttl' width="24">NL</th>
<th class='ttl' width="20">Opsi</th>
<th class='ttl' width="20">Aksi</th>
</tr>
<?php $n=0;
  while ($w = _fetch_array($r)){
    $n++;
    $table .= "<td>$n</td>";
    $set = TRIM($w['MKSetara'],".");
    $_MKKode= '';$_NamaMK ='';$_SKS = '';$_Nilai = '';$Nilai='';
    if(!empty($set)){
      $p = array();
      $p = explode(".", $set);
      $_setara = '';
      foreach ($p as $a) {
        $_setara .= "'$a',";
      }
      $_setara = substr($_setara, 0, -1);
      $s1 = "SELECT m.MKKode,m.Nama,k.GradeNilai,m.SKS,k.BobotNilai,k.KRSID from mk m left outer join krs k on k.MKID = m.MKID and k.NA='N' AND  k.Final='Y' and k.Tinggi='*' and k.BobotNilai > 0 and k.MhswID = '$_SESSION[_kliringMhswID]'
            where m.MKKode in ($_setara) and m.KurikulumID!='$_SESSION[_kliringKurikulum]' and m.ProdiID='$_SESSION[_kliringProdi]' and k.NA='N' order by k.BobotNilai";
      //echo $s1;die();
      $r1 = _query($s1);$nn=0;
      while ($w1 = _fetch_array($r1)){
        $nn++;
        $_MKKode .= ($nn > 0) ? "&raquo; $w1[MKKode] <br>":"&raquo; $w1[MKKode]";
        $_NamaMK .= ($nn > 0 ? "&raquo; $w1[Nama] <br>":"&raquo; $w1[Nama]");
        $_SKS .= ($nn > 0 ? "$w1[SKS] <br>":"$w1[SKS]");
        $_Nilai .= ($nn > 0 ? "$w1[GradeNilai] <br>":"$w1[GradeNilai]");
        $NilaiSetara = $w1['BobotNilai']; // Nilai dari matakuliah sebelumnya
        $KRSID = $w1['KRSID'];
      }
        $table .= "<td>$_MKKode</td>";
        $table .= "<td>$_NamaMK</td>";
        $table .= "<td>$_SKS</td>";
        $table .= "<td>$_Nilai</td>";
    }else{ // Bila tidak ada matakuliah setara
        $table .= "<td>-</td>";
        $table .= "<td>-</td>";
        $table .= "<td>-</td>";
        $table .= "<td>-</td>";
    }
    $NilaiMK = $w1['BobotNilai']; // Nilai matakuliah baru yang diambil (kurikulum yg dimaksud).
    $BobotNilai = ($NilaiMK > $NilaiSetara) ? $NilaiMK : $NilaiSetara; // Cari Nilai yg terbaik
    $GradeSekarang = (empty($w['GradeNilai']) ? "-":$w['GradeNilai']);
    $optnilai = GetOption2('nilai', "Nama", 'Bobot desc',
    $BobotNilai, "KodeID='".KodeID."' and ProdiID='$mhsw[ProdiID]'", 'Bobot');
    $optnilai .= "<option value='NA'> ..(X)..</option>";

        // MK Baru
        $table .= "<td>$w[MKKode]<br>$w[MKID]</td>";
        $table .= "<td>$w[Nama]</td>";
        $table .= "<td>$w[SKS]</td>";
        $table .= "<td><b>$GradeSekarang</b></td><td><select id='nilai_$n' class='nones'>$optnilai</select></td><td><span id='bt_$n'><input type='button' class='btn btn-xs' value='Confrm' onClick=\"javascript:saveKliring('$n','MhswID=$_SESSION[_kliringMhswID]&KRSID=$w[KRSID]&MKID=$w[MKID]');\"></span></td></tr>";
        $BobotNilai='';$NilaiMK='';$NilaiSetara='';
  }
  echo $table;
  ?></table>
<script>
function saveKliring(mkid,opt){
  var nilai = $("#nilai_"+mkid).val();
  ajxSave('jur/ajx/ajxsave.kliringnilai','Bobot='+nilai+'&MKID='+mkid+'&'+opt,"bt_"+mkid);
}
</script>
<?php } ?>