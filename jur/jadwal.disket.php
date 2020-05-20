<?php
// Author: Emanuel Setio Dewo
// www.sisfokampus.net
// 15 Juli 2006

// *** Functions ***
function DftrJdwl() {
  $arrmetode = array('web', 'disket');
  $s = "select j.JadwalID, j.MKID, j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.HariID, j.RuangID,
    j.JamMulai, j.JamSelesai, h.Nama as HR, j.Penilaian, j.Final,
    concat(d.Nama, ', ', d.Gelar) as DSN
    from jadwal j
	  left outer join dosen d on j.DosenID=d.Login
	  left outer join hari h on j.HariID=h.HariID
	where j.TahunID='$_SESSION[tahun]'
	  and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0
	  and INSTR(j.ProgramID, '.$_SESSION[prid].')>0
	  and j.JadwalSer = 0
	order by d.Nama, j.HariID";
  $r = _query($s); $n = 0;
  echo "<p><table class=box>
    <tr><th class=ttl>#</th>
	<th class=ttl>Nama Dosen</th>
	<th class=ttl>Kode</th>
	<th class=ttl>Matakuliah</th>
	<th class=ttl>Kelas</th>
	<th class=ttl>SKS</th>
	<th class=ttl>Ruang</th>
	<th class=ttl>Hari</th>
	<th class=ttl>Jam</th>
	<th class=ttl>Penilaian</th>
	<th class=ttl>Label Disket</th>
	<th class=ttl>Download</th>
	<th class=ttl>Program</th>
	</tr>";
  while ($w = _fetch_array($r)) {
    $n++;
	  $optmetode = GetOptionPenilaian($arrmetode, $w['Penilaian'], 'Penilaian');
	  $c = ($w['Final'] == 'Y')? 'class=nac' : 'class=ul';
	  $donlot = ($w['Final'] == 'Y')? "&nbsp;" :
	    "<a href='jadwal.disket.sav.php?gos=DownloadFileNilai&JadwalID=$w[JadwalID]&LGN=$_SESSION[_Login]&KodeID=$_SESSION[KodeID]'><img src='img/disket.gif'></a>";
    echo "<tr><td class=inp>$n</td>
	  <td $c>$w[DSN]</td>
	  <td $c>$w[MKKode]</td>
	  <td $c>$w[Nama]</td>
	  <td $c>$w[NamaKelas]</td>
	  <td $c align=right>$w[SKS]</td>
	  <td $c>$w[RuangID]</td>
	  <td $c>$w[HR]</td>
	  <td $c>$w[JamMulai]~$w[JamSelesai]</td>
	  
	  <form action='jadwal.disket.sav.php' method=POST target=_blank>
	  <input type=hidden name='JadwalID' value='$w[JadwalID]'>
	  <input type=hidden name='gos' value='SimpanPenilaian'>
	  <td class=ul>$optmetode</td>
	  </form>
	  <td class=ul align=center><a href='cetak/cetak.label.disket.php?JadwalID=$w[JadwalID]&tahun=$_SESSION[tahun]&prodi=$_SESSION[prodi]&prid=$_SESSION[prid]&asal=1' title='Cetak Label Disket'>Label</a></td>
	  <td class=ul align=center>$donlot</td>
	  <td class=ul align=center><a href=prg/isinilai.exe>Prg</a></td>
	  </tr>";
  }
  echo "</table></p>";
}
function GetOptionPenilaian($arr, $def, $key) {
  $a = '';
  for ($i=0; $i<sizeof($arr); $i++) {
  	$val = $arr[$i];
    $sel = ($val == $def)? 'selected' : '';
	$a .= "<option value='$val' $sel>$val</option>";
  }
  return "<select name='$key' onChange='this.form.submit()'>$a</select>";
}

// *** Parameters ***
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$tahun = GetSetVar('tahun');
$gos = (empty($_REQUEST['gos']))? 'DftrJdwl' : $_REQUEST['gos'];

// *** Main ***
$NTahun = NamaTahun($tahun);
TampilkanJudul("Penjadwalan Kuliah $NTahun");
TampilkanTahunProdiProgram('jadwal.disket', '');
if (!empty($_SESSION['prodi']) && !empty($_SESSION['prid']) && !empty($_SESSION['KodeID']) && !empty($tahun)) {
  $gos();
}
?>
