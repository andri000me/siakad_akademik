<?php
// Author: Emanuel Setio Dewo
// 02/02/2006

// *** Functions ***
function DftrTahun() {
  global $_defmaxrow, $_FKartuUSM;
  include_once "class/lister.class.php";

  echo "<p><a href='?mnux=tahun&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]&gos=ThnEdt&md=1'>Tambah Tahun Akademik</a></p>";
  
  $pagefmt = "<a href='?mnux=tahun&gos=&THNSR==STARTROW='>=PAGE=</a>";
  $pageoff = "<b>=PAGE=</b>";
  
  $lister = new lister;
  $lister->tables = "tahun
    where KodeID='".KodeID."' and ProdiID='$_SESSION[prodi]' and ProgramID='$_SESSION[prid]'
    order by TahunID desc";
	//echo $lister->tables;
  $lister->fields = "*,
    date_format(TglKRSMulai, '%d-%m-%Y') as KRSMulai,
    date_format(TglKRSSelesai, '%d-%m-%Y') as KRSSelesai,
	date_format(TglKRSOnlineMulai, '%d-%m-%Y') as KRSOnlineMulai,
	date_format(TglKRSOnlineSelesai, '%d-%m-%Y') as KRSOnlineSelesai,
    date_format(TglUbahKRSMulai, '%d-%m-%Y') as UbahKRSMulai,
    date_format(TglUbahKRSSelesai, '%d-%m-%Y') as UbahKRSSelesai,
    date_format(TglCetakKSS1, '%d-%m-%Y') as CetakKSS1,
    date_format(TglCetakKSS2, '%d-%m-%Y') as CetakKSS2,
    date_format(TglCuti, '%d-%m-%Y') as Cuti,
    date_format(TglMundur, '%d-%m-%Y') as Mundur,
    date_format(TglBayarMulai, '%d-%m-%Y') as BayarMulai,
    date_format(TglBayarSelesai, '%d-%m-%Y') as BayarSelesai,
    date_format(TglKembaliUangKuliah, '%d-%m-%Y') as KembaliUangKuliah,
    date_format(TglKuliahMulai, '%d-%m-%Y') as KuliahMulai,
    date_format(TglKuliahSelesai, '%d-%m-%Y') as KuliahSelesai,
    date_format(TglUTSMulai, '%d-%m-%Y') as UTSMulai,
    date_format(TglUTSSelesai, '%d-%m-%Y') as UTSSelesai,
    date_format(TglUASMulai, '%d-%m-%Y') as UASMulai,
    date_format(TglUASSelesai, '%d-%m-%Y') as UASSelesai,
    date_format(TglNilai, '%d-%m-%Y') as Penilaian,
		date_format(TglAkhirKSS, '%d-%m-%Y') as AkhirKSS
     ";
  $lister->startrow = $_REQUEST['THNSR']+0;
  $lister->maxrow = $_defmaxrow;
  $lister->headerfmt = "<p><table class=box cellspacing=1 cellpadding=4>
    <tr>
    <th class=ttl>#</th>
    <th class=ttl>Tahun</th>
    <th class=ttl>Nama</th>
    <th class=ttl>KRS</th>
	<th class=ttl>KRS Online</th>
    <th class=ttl>Ubah KRS</th>
    <th class=ttl>Masa<br />Bayar</th>
    <th class=ttl>Masa Kuliah</th>
    <th class=ttl>Masa UTS</th>
    <th class=ttl>Masa UAS</th>
    <th class=ttl>Penilaian</th>
		<th class=ttl>Akhir Masa KSS</th>
    
    <th class=ttl>Buka</th>
    <th class=ttl>Reverse</th>
    <th class=ttl>Tutup</th>
    <th class=ttl>NA</th>
    </tr>";
  $lister->detailfmt = "<tr>
    <td class=inp1 width=18 align=right>=NOMER=</td>
    <td class=cna=NA= nowrap><a href=\"?mnux=tahun&gos=ThnEdt&md=0&tahun==TahunID=&prodi==ProdiID=&prid==ProgramID=\"><img src='img/edit.png' border=0>
    =TahunID=</a></td>
    <td class=cna=NA=>=Nama=</a></td>
    <td class=cna=NA=>=KRSMulai=<br />=KRSSelesai=</td>
		<td class=cna=NA=>=KRSOnlineMulai=<br />=KRSOnlineSelesai=</td>
    <td class=cna=NA=>=UbahKRSMulai=<br />=UbahKRSSelesai=</td>
    <td class=cna=NA=>=BayarMulai=<br />=BayarSelesai=</td>
    <td class=cna=NA=>=KuliahMulai=<br />=KuliahSelesai=</td>
    <td class=cna=NA=>=UTSMulai=<br />=UTSSelesai=</td>
    <td class=cna=NA=>=UASMulai=<br />=UASSelesai=</td>
    <td class=cna=NA=>=Penilaian=</td>
		<td class=cna=NA=>=AkhirKSS=&nbsp;</td>
    
    <td class=cna=NA=><a href='?mnux=tahun&gos=ThnPrc&tahun==TahunID=&prodi==ProdiID=&prid==ProgramID='><img src='img/gear.gif' width=20></a> =ProsesBuka=x</td>
    <td class=cna=NA=><a href='?mnux=tahun&gos=ThnPrcBipot&tahun==TahunID=&prodi==ProdiID=&prid==ProgramID='><img src='img/gear.gif' width=20></a></td>
    <td class=cna=NA=><a href='?mnux=tahun&gos=ThnPrcTutup&tahun==TahunID=&prodi==ProdiID=&prid==ProgramID='><img src='img/gear.gif' width=20></a> =ProsesTutup=x</td>
    <td class=cna=NA= align=center><img src='img/book=NA=.gif'></td>
    </tr>";
  $lister->footerfmt = "</table></p>";
  $halaman = $lister->WritePages ($pagefmt, $pageoff);
  $TotalNews = $lister->MaxRowCount;
  $usrlist = $lister->ListIt () .
    "<p>Halaman: $halaman<br>
    Total: $TotalNews</p>";
  echo $usrlist;
  /*
  <th class=ttl>Skrg KRS<br />utk Angk</th>
  <form action='tahun.angk.php' method=POST target=_blank>
    <input type=hidden name='KodeID' value='$_SESSION[KodeID]'>
    <input type=hidden name='TahunID' value='=TahunID='>
    <input type=hidden name='ProgramID' value='=ProgramID='>
    <input type=hidden name='ProdiID' value='=ProdiID='>
    <td class=ul><input type=text name='HanyaAngkatan' value='=HanyaAngkatan=' size=8 mexlength=8>
      <input type=submit name='Set' value='Set'></td>
    </form>
  */
}
function ThnEdt() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $w = GetFields('tahun', "KodeID='$_SESSION[KodeID]' and ProdiID='$_SESSION[prodi]' and ProgramID='$_SESSION[prid]' and TahunID",
      $_REQUEST['tahun'], '*');
    $jdl = "Edit Tahun Akademik";
    $_strthnid = "<input type=hidden name='tahun' value='$w[TahunID]'><b>$w[TahunID]</b>";
  }
  else {
    $w = array();
    $w['TahunID'] = '';
    $w['Nama'] = '';
    $w['TglKRSMulai'] = date('Y-m-d');
    $w['TglKRSSelesai'] = date('Y-m-d');
	$w['TglKRSOnlineMulai'] = date('Y-m-d');
	$w['TglKRSOnlineSelesai'] = date('Y-m-d');
    $w['TglUbahKRSMulai'] = date('Y-m-d');
    $w['TglUbahKRSSelesai'] = date('Y-m-d');
    $w['TglCetakKSS1'] = date('Y-m-d');
    $w['TglCetakKSS2'] = date('Y-m-d');
    $w['TglCuti'] = date('Y-m-d');
    $w['TglMundur'] = date('Y-m-d');
    $w['TglBayarMulai'] = date('Y-m-d');
    $w['TglBayarSelesai'] = date('Y-m-d');
    $w['TglAutodebetSelesai'] = date('Y-m-d');
    $w['TglAutodebetSelesai2'] = date('Y-m-d');
    $w['TglKembaliUangKuliah'] = date('Y-m-d');
    $w['TglKuliahMulai'] = date('Y-m-d');
    $w['TglKuliahSelesai'] = date('Y-m-d');
    $w['TglUTSMulai'] = date('Y-m-d');
    $w['TglUTSSelesai'] = date('Y-m-d');
    $w['TglUASMulai'] = date('Y-m-d');
    $w['TglUASSelesai'] = date('Y-m-d');
    $w['TglNilai'] = date('Y-m-d');
		$w['TglAkhirKSS'] = date('Y-m-d');
    $w['Catatan'] = '';
    $w['NA'] = 'N';
    $w['SP'] = 'N';
    $jdl = "Tambah Tahun Akademik";
    $_strthnid = "<input type=text name='TahunID' size=15 maxlength=10>";
  }
  // Opsi tanggal2
  $TglKRSMulai = GetDateOption($w['TglKRSMulai'], 'TglKRSMulai');
  $TglKRSSelesai = GetDateOption($w['TglKRSSelesai'], 'TglKRSSelesai');
  $TglKRSOnlineMulai = GetDateOption($w['TglKRSOnlineMulai'], 'TglKRSOnlineMulai');
  $TglKRSOnlineSelesai = GetDateOption($w['TglKRSOnlineSelesai'], 'TglKRSOnlineSelesai');
  $TglUbahKRSMulai = GetDateOption($w['TglUbahKRSMulai'], 'TglUbahKRSMulai');
  $TglUbahKRSSelesai = GetDateOption($w['TglUbahKRSSelesai'], 'TglUbahKRSSelesai');
  $TglCetakKSS1 = GetDateOption($w['TglCetakKSS1'], 'TglCetakKSS1');
  $TglCetakKSS2 = GetDateOption($w['TglCetakKSS2'], 'TglCetakKSS2');
  $TglCuti = GetDateOption($w['TglCuti'], 'TglCuti');
  $TglMundur = GetDateOption($w['TglMundur'], 'TglMundur');
  $TglBayarMulai = GetDateOption($w['TglBayarMulai'], 'TglBayarMulai');
  $TglBayarSelesai = GetDateOption($w['TglBayarSelesai'], 'TglBayarSelesai');
  $TglAutodebetSelesai = GetDateOption($w['TglAutodebetSelesai'], 'TglAutodebetSelesai');
  $TglAutodebetSelesai2 = GetDateOption($w['TglAutodebetSelesai2'], 'TglAutodebetSelesai2');
  $TglKembaliUangKuliah = GetDateOption($w['TglKembaliUangKuliah'], 'TglKembaliUangKuliah');
  $TglKuliahMulai = GetDateOption($w['TglKuliahMulai'], 'TglKuliahMulai');
  $TglKuliahSelesai = GetDateOption($w['TglKuliahSelesai'], 'TglKuliahSelesai');
  $TglUTSMulai = GetDateOption($w['TglUTSMulai'], 'TglUTSMulai');
  $TglUTSSelesai = GetDateOption($w['TglUTSSelesai'], 'TglUTSSelesai');
  $TglUASMulai = GetDateOption($w['TglUASMulai'], 'TglUASMulai');
  $TglUASSelesai = GetDateOption($w['TglUASSelesai'], 'TglUASSelesai');
  $TglNilai = GetDateOption($w['TglNilai'], 'TglNilai');
	$TglAkhirKSS = GetDateOption($w['TglAkhirKSS'], 'TglAkhirKSS');
  
  $snm = session_name(); $sid = session_id();
  $_na = ($w['NA'] == 'Y')? 'checked' : '';
  $_sp = ($w['SP'] == 'Y')? 'checked' : '';
  // Tampilkan formulir
  echo "<p><table class=box cellspacing=1>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='tahun'>
  <input type=hidden name='gos' value='ThnSav'>
  <input type=hidden name='md' value='$md'>
  
  <tr><th colspan=2 class=ttl>$jdl</th></tr>
  <tr><td class=inp1>Tahun Akademik</td><td class=ul>$_strthnid</td></tr>
  <tr><td class=inp1>Nama Tahun</td><td class=ul><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp1>Semester Pendek?</td><td class=ul><input type=checkbox name='SP' value='Y' $_sp></td></tr>  
  
  <tr><td colspan=2 class=ul><b>KRS</td></tr>
  <tr><td class=inp1>Mulai KRS</td><td class=ul>$TglKRSMulai</td></tr>
  <tr><td class=inp1>Selesai KRS</td><td class=ul>$TglKRSSelesai</td></tr>
  <tr><td class=inp1>Akhir Cetak KSS-KRS</td><td class=ul>$TglCetakKSS1</td></tr>
  
  <tr><td colspan=2 class=ul><b>KRS Online (Untuk Mahasiswa)</td></tr>
  <tr><td class=inp1>Mulai KRS Online</td><td class=ul>$TglKRSOnlineMulai</td></tr>
  <tr><td class=inp1>Selesai KRS Online</td><td class=ul>$TglKRSOnlineSelesai</td></tr>
  
  <tr><td colspan=2 class=ul><b>KPRS</td></tr>
  <tr><td class=inp1>Mulai KPRS</td><td class=ul>$TglUbahKRSMulai</td></tr>
  <tr><td class=inp1>Selesai KPRS</td><td class=ul>$TglUbahKRSSelesai</td></tr>
  <tr><td class=inp1>Akhir Cetak KSS-KPRS</td><td class=ul>$TglCetakKSS2</td></tr>
  
  <tr><td colspan=2 class=ul><b>Batas-batas</td></tr>
  <tr><td class=inp1>Batas Pengajuan Cuti</td><td class=ul>$TglCuti</td></tr>
  <tr><td class=inp1>Batas Mundur Kuliah</td><td class=ul>$TglMundur</td></tr>
  <tr><td class=inp1>Batas Pengambilan Kelebihan Uang Kuliah</td><td class=ul>$TglKembaliUangKuliah</td></tr>
  
  <tr><td colspan=2 class=ul><b>Masa Pembayaran</b></td></tr>
  <tr><td class=inp1>Mulai Pembayaran</td><td class=ul>$TglBayarMulai</td></tr>
  <tr><td class=inp1>Selesai Pembayaran</td><td class=ul>$TglBayarSelesai</td></tr>
  <tr><td class=inp1>Pembayaran Autodebet 1</td><td class=ul>$TglAutodebetSelesai</td></tr>
  <tr><td class=inp1>Pembayaran Autodebet 2</td><td class=ul>$TglAutodebetSelesai2</td></tr>
  
  <tr><td colspan=2 class=ul><b>Tanggal Kuliah</td></tr>
  <tr><td class=inp1>Mulai Kuliah</td><td class=ul>$TglKuliahMulai</td></tr>
  <tr><td class=inp1>Selesai Kuliah</td><td class=ul>$TglKuliahSelesai</td></tr>
  
  <tr><td colspan=2 class=ul><b>Tanggal Ujian Tengah Semester</b></td></tr>
  <tr><td class=inp1>Mulai UTS</td><td class=ul>$TglUTSMulai *) tidak boleh ubah dosen di jadwal lagi</td></tr>
  <tr><td class=inp1>Selesai UTS</td><td class=ul>$TglUTSSelesai</td></tr>
  
  <tr><td colspan=2 class=ul><b>Tanggal Ujian Akhir Semester</b></td></tr>
  <tr><td class=inp1>Mulai UAS</td><td class=ul>$TglUASMulai</td></tr>
  <tr><td class=inp1>Selesai UAS</td><td class=ul>$TglUASSelesai</td></tr>
  <tr><td class=inp1>Akhir Penilaian</td><td class=ul>$TglNilai</td></tr>
	<tr><td class=inp1>Tanggal Habis KSS</td><td class=ul>$TglAkhirKSS</td></tr>
  
  <tr><td class=inp1>Catatan</td><td class=ul><textarea name='Catatan' cols=40 rows=4>$w[Catatan]</textarea></td></tr>
  <tr><td class=inp1>Tidak aktif?</td><td class=ul><input type=checkbox name='NA' value='Y' $_na></td></tr>
  <tr><td colspan=2><input type=submit name='Simpan' value='Simpan'>
    <input type=reset name='Reset' value='Reset'>
    <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=tahun&gos=&$snm=$sid'\"></td></tr>
  </form></table>";
}
function ThnSav() {
  $md = $_REQUEST['md']+0;
  $Nama = sqling($_REQUEST['Nama']);
  $TglKRSMulai = "$_REQUEST[TglKRSMulai_y]-$_REQUEST[TglKRSMulai_m]-$_REQUEST[TglKRSMulai_d]";
  $TglKRSSelesai = "$_REQUEST[TglKRSSelesai_y]-$_REQUEST[TglKRSSelesai_m]-$_REQUEST[TglKRSSelesai_d]";
  $TglKRSOnlineMulai = "$_REQUEST[TglKRSOnlineMulai_y]-$_REQUEST[TglKRSOnlineMulai_m]-$_REQUEST[TglKRSOnlineMulai_d]";
  $TglKRSOnlineSelesai = "$_REQUEST[TglKRSOnlineSelesai_y]-$_REQUEST[TglKRSOnlineSelesai_m]-$_REQUEST[TglKRSOnlineSelesai_d]";
  $TglUbahKRSMulai = "$_REQUEST[TglUbahKRSMulai_y]-$_REQUEST[TglUbahKRSMulai_m]-$_REQUEST[TglUbahKRSMulai_d]";
  $TglUbahKRSSelesai = "$_REQUEST[TglUbahKRSSelesai_y]-$_REQUEST[TglUbahKRSSelesai_m]-$_REQUEST[TglUbahKRSSelesai_d]";
  $TglCetakKSS1 = "$_REQUEST[TglCetakKSS1_y]-$_REQUEST[TglCetakKSS1_m]-$_REQUEST[TglCetakKSS1_d]";
  $TglCetakKSS2 = "$_REQUEST[TglCetakKSS2_y]-$_REQUEST[TglCetakKSS2_m]-$_REQUEST[TglCetakKSS2_d]";
  $TglCuti = "$_REQUEST[TglCuti_y]-$_REQUEST[TglCuti_m]-$_REQUEST[TglCuti_d]";
  $TglMundur = "$_REQUEST[TglMundur_y]-$_REQUEST[TglMundur_m]-$_REQUEST[TglMundur_d]";
  $TglKembaliUangKuliah = "$_REQUEST[TglKembaliUangKuliah_y]-$_REQUEST[TglKembaliUangKuliah_m]-$_REQUEST[TglKembaliUangKuliah_d]";
  $TglBayarMulai = "$_REQUEST[TglBayarMulai_y]-$_REQUEST[TglBayarMulai_m]-$_REQUEST[TglBayarMulai_d]";
  $TglBayarSelesai = "$_REQUEST[TglBayarSelesai_y]-$_REQUEST[TglBayarSelesai_m]-$_REQUEST[TglBayarSelesai_d]";
  $TglAutodebetSelesai = "$_REQUEST[TglAutodebetSelesai_y]-$_REQUEST[TglAutodebetSelesai_m]-$_REQUEST[TglAutodebetSelesai_d]";
  $TglAutodebetSelesai2 = "$_REQUEST[TglAutodebetSelesai2_y]-$_REQUEST[TglAutodebetSelesai2_m]-$_REQUEST[TglAutodebetSelesai2_d]";
  $TglKuliahMulai = "$_REQUEST[TglKuliahMulai_y]-$_REQUEST[TglKuliahMulai_m]-$_REQUEST[TglKuliahMulai_d]";
  $TglKuliahSelesai = "$_REQUEST[TglKuliahSelesai_y]-$_REQUEST[TglKuliahSelesai_m]-$_REQUEST[TglKuliahSelesai_d]";
  $TglUTSMulai = "$_REQUEST[TglUTSMulai_y]-$_REQUEST[TglUTSMulai_m]-$_REQUEST[TglUTSMulai_d]";
  $TglUTSSelesai = "$_REQUEST[TglUTSSelesai_y]-$_REQUEST[TglUTSSelesai_m]-$_REQUEST[TglUTSSelesai_d]";
  $TglUASMulai = "$_REQUEST[TglUASMulai_y]-$_REQUEST[TglUASMulai_m]-$_REQUEST[TglUASMulai_d]";
  $TglUASSelesai = "$_REQUEST[TglUASSelesai_y]-$_REQUEST[TglUASSelesai_m]-$_REQUEST[TglUASSelesai_d]";
  $TglNilai = "$_REQUEST[TglNilai_y]-$_REQUEST[TglNilai_m]-$_REQUEST[TglNilai_d]";
	$TglAkhirKSS = "$_REQUEST[TglAkhirKSS_y]-$_REQUEST[TglAkhirKSS_m]-$_REQUEST[TglAkhirKSS_d]";
  
  $SP = (empty($_REQUEST['SP']))? 'N' : $_REQUEST['SP'];
  $Catatan = sqling($_REQUEST['Catatan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update tahun
      set Nama='$Nama',
      TglKRSMulai='$TglKRSMulai', TglKRSSelesai='$TglKRSSelesai',
			TglKRSOnlineMulai='$TglKRSOnlineMulai', TglKRSOnlineSelesai='$TglKRSOnlineSelesai',
      TglUbahKRSMulai='$TglUbahKRSMulai', TglUbahKRSSelesai='$TglUbahKRSSelesai',
      TglCetakKSS1='$TglCetakKSS1', TglCetakKSS2='$TglCetakKSS2',
      TglCuti='$TglCuti', TglMundur='$TglMundur',
      TglKembaliUangKuliah='$TglKembaliUangKuliah',
      TglBayarMulai='$TglBayarMulai', TglBayarSelesai='$TglBayarSelesai',
      TglAutodebetSelesai='$TglAutodebetSelesai',
      TglAutodebetSelesai2='$TglAutodebetSelesai2',
      TglKuliahMulai='$TglKuliahMulai', TglKuliahSelesai='$TglKuliahSelesai',
      TglUTSMulai='$TglUTSMulai', TglUTSSelesai='$TglUTSSelesai',
      TglUASMulai='$TglUASMulai', TglUASSelesai='$TglUASSelesai',
      TglNilai='$TglNilai', TglAkhirKSS='$TglAkhirKSS',
      Catatan='$Catatan', NA='$NA', SP='$SP', 
      TglEdit=now(),
      LoginEdit='$_SESSION[_Login]'
      where KodeID='$_SESSION[KodeID]' and ProgramID='$_SESSION[prid]'
      and ProdiID='$_SESSION[prodi]' and TahunID='$_REQUEST[tahun]' ";
    $r = _query($s);
    //echo $s;
  }
  else {
    $ada = GetFields("tahun", "KodeID='$_SESSION[KodeID]' and ProgramID='$_SESSION[prgid]' and ProdiID='$_SESSION[prodi]' and TahunID",
      $_REQUEST['TahunID'], '*');
    if (!empty($ada)) 
      echo ErrorMsg("Tidak Dapat Disimpan", "Tahun akademik <b>$_REQUEST[TahunID]</b> sudah ada dan tidak dapat dibuat lagi.");
    else {
      $s = "insert into tahun
        (TahunID, Nama, KodeID, ProgramID, ProdiID,
        TglKRSMulai, TglKRSSelesai,
				TglKRSOnlineMulai, TglKrsOnlineSelesai,
        TglUbahKRSMulai, TglUbahKRSSelesai,
        TglCuti, TglMundur,
        TglKembaliUangKuliah,
        TglBayarMulai, TglBayarSelesai,
        TglAutodebetSelesai,
        TglKuliahMulai, TglKuliahSelesai,
        TglUTSMulai, TglUTSSelesai,
        TglUASMulai, TglUASSelesai,
        TglNilai, TglAkhirKSS,
        Catatan, NA, TglBuat, LoginBuat)
        
        values ('$_REQUEST[TahunID]', '$Nama', '$_SESSION[KodeID]', '$_SESSION[prid]', '$_SESSION[prodi]',
        '$TglKRSMulai', '$TglKRSSelesai',
		'$TglKRSOnlineMulai', '$TglKRSOnlineSelesai',
        '$TglUbahKRSMulai', '$TglUbahKRSSelesai',
        '$TglCuti', '$TglMundur',
        '$TglKembaliUangKuliah',
        '$TglBayarMulai', '$TglBayarSelesai',
        '$TglAutodebetSelesai',
        '$TglKuliahMulai', '$TglKuliahSelesai',
        '$TglUTSMulai', '$TglUTSSelesai',
        '$TglUASMulai', '$TglUASSelesai',
        '$TglNilai', '$TglAkhirKSS',
        '$Catatan', '$NA', now(), '$_SESSION[_Login]')";
      $r = _query($s);
    }
  }
  // Buat yang lain tidak aktif
  if ($NA == 'N') {
    $s = "update tahun set NA='Y'
      where KodeID='$_SESSION[KodeID]' and ProgramID='$_SESSION[prid]' and ProdiID='$_SESSION[prodi]' and TahunID<>'$_REQUEST[tahun]' ";
    $r = _query($s);
    //echo $s;
  }
  DftrTahun();
}
function ThnPrc() {
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  // hitung jumlah proses
  $sj = "select m.MhswID
    from mhsw m
      left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    where m.ProdiID='$prodi' and m.ProgramID='$prid' and sm.Keluar='N'
      and m.KodeID='$_SESSION[KodeID]' ";
  $rj = _query($sj);
  $jml = _num_rows($rj);
  $n = 0;
  while ($w = _fetch_array($rj)) {
    $_SESSION['THN'.$prodi.$n] = $w['MhswID'];
    $n++;
  }
  $_SESSION['THN'.$prodi] = $n;
  $_SESSION['THN'.$prodi.'POS'] = 0;
  echo "<p>Sistem akan memproses <font size=+2>$jml</font> data</p>
    <p><IFRAME src='cetak/tahun.prc.php?gos=PRC&tahun=$tahun&prodi=$prodi&prid=$prid' frameborder=0>
    </IFRAME></p>
  ";
  
  DftrTahun();
}
function ThnPrc1() {
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  // Buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].html";
  $f = fopen($nmf, 'w');
  // Ambil data
  $s = "select m.MhswID, m.Nama, m.StatusMhswID, m.BIPOTID,
    m.ProgramID, m.ProdiID, k.KHSID, sm.Keluar
    from mhsw m
      left outer join khs k on m.MhswID=k.MhswID and k.TahunID='$tahun'
      left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    where m.ProdiID='$prodi' and m.ProgramID='$prid' and sm.Keluar='N' and k.KHSID is NULL
      and m.KodeID='$_SESSION[KodeID]'
    order by m.MhswID";
  $r = _query($s);
  $n = 0;
  // Tampilkan header html
  fwrite($f, "<HTML>
    <HEAD>
    <link href=\"../index.css\" rel=\"stylesheet\" type=\"text/css\">
    </HEAD>
    <BODY>");
  // Tampilkan proses
  $btn = "<input type=button name='Tutup' value='Tutup' onClick=\"javascript:window.close()\">";
  $def = GetaField('statusmhsw', 'Def', 'Y', 'StatusMhswID');
  fwrite($f, "<h1>Proses Buka Tahun Akademik</h1>");
  fwrite($f, "<p>Berikut adalah mahasiswa yang diproses. Mahasiswa dengan warna abu-abu berarti sudah diproses sebelumnya.<br />
    $btn</p> \n");
  fwrite($f, "<p><table class=box cellspacing=1 cellpadding=4>");
  fwrite($f, "<tr><th class=ttl>#</th>
    <th class=ttl>NPM</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Status</th>
    <th class=ttl>KHSID</th>
    </tr>");
  while ($w = _fetch_array($r)) {
    $n++;
    $c = (empty($w['KHSID']))? 'class=ul' : 'class=naY';
    $sesi = GetaField('khs', 'MhswID', $w['MhswID'], "max(Sesi)")+1;
    $sp = "insert into khs (TahunID, KodeID, ProgramID, ProdiID,
      MhswID, StatusMhswID, Sesi, BIPOTID,
      LoginBuat, TanggalBuat)
      values ('$tahun', '$_SESSION[KodeID]', '$w[ProgramID]', '$w[ProdiID]',
      '$w[MhswID]', '$def', '$sesi', '$w[BIPOTID]',
      '$_SESSION[_Login]', now()  )";
    $rp = _query($sp);
    fwrite($f, "<tr><td class=inp>$n</td>
      <td $c nowrap>$w[MhswID]</td>
      <td $c nowrap>$w[Nama]</td>
      <td $c nowrap>$def</td>
      <td $c nowrap>$w[KHSID]</td>
      </tr>");
  }
  fwrite($f, "</table></p>$btn
    </BODY>
    </HTML>");
  fclose($f);
  // update data tahun
  $st = "update tahun set ProsesBuka=ProsesBuka+1
    where TahunID='$tahun' and ProgramID='$prid' and ProdiID='$prodi'";
  $rt = _query($st);
  PopupMsg($nmf);
  // Tampilkan lagi tahun akd
  DftrTahun();
}
function ThnPrcBipot() {
  // Tahun ini
  $thn = $_REQUEST['tahun'];
  echo Konfirmasi("Konfirmasi Proses",
    "Anda akan memproses BIPOT Mhsw secara global.<br />
    Dalam proses ini biaya akan dihitung berdasarkan jumlah KRS Aktual yg diambil oleh mhsw.<br />
    Jumlah SKS yg diakui adalah jumlah SKS yg tertera dalam KSS (Kartu Studi Mhsw).
    <hr size=1 color=silver>
    Pilihan: <a href='?mnux=tahun'>Kembali</a> |
      <a href='?mnux=tahun&gos=PrcBipot&tahun=$thn&prid=$_SESSION[prid]&prodi=$_SESSION[prodi]'>Proses Bipot</a>");
}
function PrcBipot() {
  global $KodeID;
  $tahun = $_REQUEST['tahun'];
  $prid = $_REQUEST['prid'];
  $prodi = $_REQUEST['prodi'];
 
  // Ambil data
  $s = "select k.KHSID, k.MhswID, k.ProgramID, k.ProdiID, k.StatusMhswID
    from khs k
    where k.TahunID='$tahun'
      and k.ProgramID='$prid'
      and k.ProdiID='$prodi'
      and k.KodeID='$KodeID'
    order by k.MhswID";
  $r = _query($s);
  $jml = _num_rows($r);
  $_SESSION['BPT-TAHUN'] = $tahun;
  $_SESSION['BPT-PRID'] = $prid;
  $_SESSION['BPT-PRODI'] = $prodi;
  $_SESSION['BPT-JML'] = $jml;
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION['BPT-KHSID-'.$n] = $w['KHSID'];
    $_SESSION['BPT-MHSWID-'.$n] = $w['MhswID'];
  } 
  // Proses
  echo "<p>Akan diproses <font size=+1>$n</font> Mahasiswa.</p>
    <p><IFRAME src='cetak/tahun.prc.php?gos=PRCBIPOT&tahun=$tahun&prodi=$prodi&prid=$prid' height=200 frameborder=0>
    </IFRAME></p>";
}
function NextTahun($thn, $prid, $prodi) {
  $s0 = "select * from tahun
    where TahunID>'$thn'
      and ProdiID='$_SESSION[prodi]' and ProgramID='$_SESSION[prid]'
      and SP = 'N'
    order by TahunID
    limit 1";
  $r0 = _query($s0);
  if (_num_rows($r0) == 0) return '';
  else return _fetch_array($r0);
}
function ThnPrcTutup() {
  // Tampilkan lagi tahun akd
  $thn = $_REQUEST['tahun'];
  $next = NextTahun($thn, $_SESSION['prid'], $_SESSION['prodi']);
  if (empty($next))
    echo ErrorMsg("Tahun Akademik Belum Dibuat",
    "Untuk menutup tahun akademik <font size=+1>$thn</font> Anda harus terlebih dahulu
    membuat tahun akademik baru setelahnya.<br />
    Setelah ada tahun akademik selanjutnya, maka proses tutup tahun baru dapat dilakukan.
    <hr size=1 color=silver>
    Pilihan: <a href='?mnux=tahun'>Kembali</a>");
  else 
    echo Konfirmasi("Konfirmasi Pemrosesan",
    "Anda akan menutup tahun akademik <font size=+1>$thn</font>.<br />
    Data hutang mahasiswa akan ditransfer ke: <font size=+1>$next[TahunID]</font>
    ($next[Nama]).
    <hr size=1 color=silver>
    Pilihan: <a href='?mnux=tahun&gos=PrcTutup&tahun=$thn&tahun1=$next[TahunID]&prid=$next[ProgramID]&prodi=$next[ProdiID]'>Lanjutkan Proses</a> |
      <a href='?mnux=tahun'>Kembali</a>");
}
function PrcTutup() {
  $tahun = GetSetVar('tahun');
  $tahun1 = GetSetVar('tahun1');
  $prid = $_REQUEST['prid'];
  $prodi = $_REQUEST['prodi'];
  $s = "select KHSID, MhswID from khs 
    where TahunID='$tahun'
      and ProgramID='$prid'
      and ProdiID='$prodi'
    order by MhswID";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION['Tutup-MhswID-'.$prodi.$n] = $w['MhswID'];
    $_SESSION['Tutup-KHSID-'.$prodi.$n] = $w['KHSID'];
  }
  $_SESSION['Tutup-Max-'.$prodi] = $n;
  $_SESSION['Tutup-Pos-'.$prodi] = 0;
  // Account2
  $acc = GetFields('keusetup', 'NA', 'N', '*');
  $_SESSION['HutangNext'] = $acc['HutangNext'];
  $_SESSION['HutangPrev'] = $acc['HutangPrev'];
  $_SESSION['DepositNext'] = $acc['DepositNext'];
  $_SESSION['DepositPrev'] = $acc['DepositPrev'];
  $_SESSION['accDenda1'] = $acc['Denda1'];
  $_SESSION['accDenda2'] = $acc['Denda2'];
  // Besar Denda
  $prd = GetFields('prodi', 'ProdiID', $prodi, "Denda1,Denda2");
  $_SESSION['Denda1'] = $prd['Denda1'];
  $_SESSION['Denda2'] = $prd['Denda2'];
  echo "<p>Akan diproses <font size=+1>$n</font> Mahasiswa.</p>
    <p><IFRAME src='cetak/tahun.prc.php?gos=PRCTUTUP&tahun=$tahun&prodi=$prodi&prid=$prid' height=200 frameborder=0>
    </IFRAME></p>";
}


// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$gos = (empty($_REQUEST['gos']))? 'DftrTahun' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Tahun Akademik");
TampilkanPilihanProdiProgram('tahun', '');
if (!empty($_SESSION['prodi']) && !empty($_SESSION['prid']) && !empty($_SESSION['KodeID'])) {
  $gos();
}
?>
