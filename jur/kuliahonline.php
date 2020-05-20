<?php

// *** Parameters ***
$pid = GetSetVar('pid');
// *** Main ***

TampilkanJudul("Perkuliahan Online");
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Edit() {
  $JadwalID = GetSetVar('JadwalID');
  $jdwl = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join hari hr on j.HariID = hr.HariID
    left outer join hari hruas on hruas.HariID = date_format(j.UASTanggal, '%w')
    left outer join hari hruts on hruts.HariID = date_format(j.UTSTanggal, '%w')
    left outer join jenisjadwal jj on jj.JenisJadwalID = j.JenisJadwalID 
	LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
	", 
    "j.JadwalID", $JadwalID,
    "j.*, concat(d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
    prd.Nama as _PRD, hr.Nama as _HR, hruas.Nama as _HRUAS,hruts.Nama as _HRUTS,j.HariID as _HariID,
    LEFT(j.JamMulai, 5) as _JM, LEFT(j.JamSelesai, 5) as _JS,
    LEFT(j.UASJamMulai, 5) as _JMUAS, LEFT(j.UASJamSelesai, 5) as _JSUAS,
    LEFT(j.UTSJamMulai, 5) as _JMUTS, LEFT(j.UTSJamSelesai, 5) as _JSUTS,
    date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
	jj.Nama as _NamaJenisJadwal, jj.Tambahan, k.Nama AS namaKelas
    ");
  // Cek apakah jadwal valid?
  if (empty($jdwl)) 
    die(ErrorMsg('Error',
      "Jadwal tidak ditemukan.<br />
      Mungkin jadwal sudah dihapus.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" >"));
  // Cek apakah sudah di-finalisasi?
  if ($jdwl['Final'] == 'Y')
    die(ErrorMsg('Error',
      "Jadwal sudah difinalisasi.<br />
      Anda sudah tidak dapat mengubah data ini lagi.
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" >"));
  // Jika sudah valid semua, maka tampilkan menu edit yg sebenarnya
 
 Edits($jdwl);
  
}
function Edits($jdwl) {
  PresensiScript();
  TampilkanHeader($jdwl);
  TampilkanPresensi($jdwl);
}
function TampilkanPresensi($jdwl) {
  if($_SESSION['_LevelID'] == 100) {
   $NIDN = GetaField('dosen', "Login",$_SESSION[_Login],"NIDN");
   $s = "Select j.DosenID from jadwal j
    left outer join jadwaldosen jd on jd.JadwalID=j.JadwalID
   where (j.DosenID = '$_SESSION[_Login]' or jd.DosenID = '$_SESSION[_Login]') and j.JadwalID='$jdwl[JadwalID]' group by j.JadwalID";
   $r = _query($s);
   $w = _fetch_array($r);
	if(empty($w[DosenID]))
	   die(ErrorMsg("Anda tidak berhak mengakses data presensi dari Mata Kuliah: <b>$jdwl[Nama], Hari: $jdwl[_HRUAS], Jam: $jdwl[_JM] - $jdwl[_JS]</b>. 
					<br>Bila anda seharusnya berhak mengakses data ini, harap menghubungi Kepala Prodi."));
  }
  ?>
  <div class="box-content">
              <ul class="dashboard-list">
<?php 
$s = "SELECT if(l.Tipe='dosen', d.Nama,m.Nama) as _Nama, 
			IF(l.Tipe='dosen',CONCAT('foto/dosen/kecil/',d.Foto),CONCAT('foto/kecil/',m.Foto)) as _Foto,
			IF(l.Tipe='dosen',concat('-'),m.MhswID) as _Login, l.Tipe,
			 l.Posting, l.Waktu  from
		kuliahonline_log l left outer join dosen d on d.Login=l.Login 
		left outer join mhsw m on m.MhswID=l.Login
		where l.PresensiID='$pid' and l.JadwalID='$jdwl[JadwalID]' order by l.LogID ";
$r = _query($s);
while ($w = _fetch_array($r)){ 
$label = ($w['Tipe']=='dosen')? "success":"info";
$info = ($w['Tipe']=='dosen')? "Dosen":"Mahasiswa"; */
?>
                <li>
                  <div class="box-content span3">
                  <a href="#">
                    <img class="dashboard-avatar" alt="<?php echo $w['_Nama'];?>" src="<?php echo $w['_Foto'];?>"></a>
                    <strong>Nama:</strong> <a href="#"><?php echo $w['_Nama'];?>
                  </a><br>
                  <strong>Login:</strong> <?php echo $w['_Login'];?><br>
                  <strong>Waktu:</strong> <?php echo $w['Waktu'];?><br>
                  <strong>Status:</strong> <span class="label label-<?php $label;?>"><?php $info;?></span>                                  
                  </div>
                  <div class="box-content span9">
                  <p style="text-align:left!important;font-size: 17px"><?php nl2br($w['Posting']);?></p>
                  </div>
                </li>
                <div class="clear"></div>
<?php } ?>
              </ul>
            </div>
  <?php
  $bck = $_SERVER['REQUEST_URI'];
  echo "<div class='clear'></div>
  <hr>
  <form method='POST' action='jur/kuliahonline.edt.php'>
  <input type='hidden' name='bck' value='$bck'>
  <input type='hidden' name='pid' value='$_SESSION[pid]'>
  <div class=box align=center style='padding:10px'>
  Untuk diskusi tentang materi ini, gunakan kolom berikut:<br>
  <textarea cols=30 placeholder='ketik tanggapan anda disini...'></textarea> <input type=submit class='btn btn-primary' value='Kirim'> 
  </div>
  </form>";
}
function TampilkanHeader($jdwl) {
  $TagTambahan = ($jdwl['Tambahan'] == 'Y')? "<b>( $jdwl[_NamaJenisJadwal] )</b>" : "";
  $JadwalUTS = GetFields("jadwaluts j left outer join hari h on h.HariID = date_format(j.Tanggal, '%w')","JadwalID",$jdwl[JadwalID],"date_format(j.Tanggal,'%d-%m-%Y') TGL, h.Nama as Hari,j.JamMulai,j.JamSelesai");
  $JadwalUAS = GetFields("jadwaluas j left outer join hari h on h.HariID = date_format(j.Tanggal, '%w')","JadwalID",$jdwl[JadwalID],"date_format(j.Tanggal,'%d-%m-%Y') TGL, h.Nama as Hari,j.JamMulai,j.JamSelesai");
  echo "<table class=box cellspacing=0 align=center width=800>
  <tr><td class=inp width=100>Thn Akademik:</td>
      <td class=ul>$jdwl[TahunID]</td>
      <td class=inp width=100>Program Studi:</td>
      <td class=ul>$jdwl[_PRD] <sup>$jdwl[ProdiID]</sup></td>
      </tr>
  <tr><td class=inp>Matakuliah:</td>
      <td class=ul>$jdwl[Nama] $TagTambahan<sup>$jdwl[MKKode]</sup></td>
      <td class=inp>Dosen:</td>
      <td class=ul>$jdwl[DSN]</td>
      </tr>
  <tr><td class=inp>SKS:</td>
      <td class=ul>$jdwl[SKS]</td>
      <td class=inp>Kelas:</td>
      <td class=ul>$jdwl[namaKelas] <sup>$jdwl[ProgramID]</sup></td>
      </tr>
  <tr><td class=inp rowspan=2>Jadwal Kuliah:</td>
      <td class=ul rowspan=2>".$jdwl['_HR']." <sup>".$jdwl['_JM']."</sup>&#8594;<sub>".$jdwl['_JS']."</sub></td>
      <td class=inp>Jadwal UTS:</td>
      <td class=ul> ".$JadwalUTS["Hari"].", ".$JadwalUTS["TGL"].",<sup>".$JadwalUTS["JamMulai"]."</sup>&#8594;<sub>".$JadwalUTS["JamSelesai"]."</sub></td>
       </tr>
       <tr>
       <td class=inp>Jadwal UAS:</td>
      <td class=ul> ".$JadwalUAS["Hari"].", ".$JadwalUAS["TGL"].",<sup>".$JadwalUAS["JamMulai"]."</sup>&#8594;<sub>".$JadwalUAS["JamSelesai"]."</sub>
      				</td>
                    </tr>
  </table>";
 $s = "select p.*,
    date_format(p.Tanggal, '%d-%m-%Y') as _Tanggal,
    date_format(p.Tanggal, '%w') as _Hari,
    d.Nama as DSN, d.Gelar,
    h.Nama as _HR,
    left(p.JamMulai, 5) as _JM, left(p.JamSelesai, 5) as _JS,
      (select sum(Nilai)
      from presensimhsw 
      where PresensiID=p.PresensiID) as JmlHadir
    from presensi p
      left outer join hari h on h.HariID = date_format(p.Tanggal, '%w')
      left outer join dosen d on d.Login = p.DosenID and d.KodeID = '".KodeID."'
    where p.PresensiID = '$_SESSION[pid]'
    order by p.Pertemuan";
  $r = _query($s);
  $w = _fetch_array($r);
  echo "<table class=box cellspacing=0 align=center width=800>
  <tr><td class=inp width=100>Materi:</td>
      <td class=ul>$w[Catatan]</td>
      </tr>
  <tr><td class=inp width=100>Pemberi Kuliah:</td>
      <td class=ul>$w[DSN] <sup>$w[Gelar]</sup></td>
      </tr>
  <tr><td class=inp width=100>Bahan Ajar:</td>
      <td class=ul>...</td>
      </tr>
  <tr><td class=inp width=100>Instruksi/Kuis:</td>
      <td class=ul>$w[Materi]</td>
      </tr>
  </table>";
}

function PresensiScript() {
  echo <<<SCR
  <script>
  function KuliahOnline(jid) {
    lnk = "$_SESSION[mnux].kuliahonline.php?jid="+jid;
    win2 = window.open(lnk, "", "width=500, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function PrsnEdit(md, jid, pid) {
    lnk = "$_SESSION[mnux].edit.php?md="+md+"&jid="+jid+"&pid="+pid;
    win2 = window.open(lnk, "", "width=500, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function PrsnMhswEdit(pid) {
    lnk = "$_SESSION[mnux].mhswedit.php?pid="+pid;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
    function PrsnMateriEdit(jid,pid) {
    lnk = "$_SESSION[mnux].materiedit.php?pid="+pid+"&jid="+jid;
    win2 = window.open(lnk, "", "width=450, height=200,top=300,left=420, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

function GetOption4($table, $key, $Fields, $Label, $Nilai, $Separator, $whr = '', $antar='<br />') {
  $_whr = (empty($whr))? '' : "and $whr";
  $s = "select $key, $Fields
    from $table
    where NA='N' $_whr order by $key";
  $r = _query($s);
  $_arrNilai = explode($Separator, $Nilai);
  $str = '';
  while ($w = _fetch_array($r)) {
    $_ck = (array_search($w[$key], $_arrNilai) === false)? '' : 'selected';
    $str .= "<option value='$w[$key]' $_ck>$w[$Label]</option>";
	//$str .= "<input type=checkbox name='".$key."[]' value='$w[$key]' $_ck> $w[$Label]$antar";
  }
  return $str;
}
?>