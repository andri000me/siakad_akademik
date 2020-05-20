<?php
// Author: Emanuel Setio Dewo
// 07/04/2006
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
?>
<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD><TITLE><?php echo $_Institution; ?></TITLE>
  <META content="Emanuel Setio Dewo" name="author">
  <META content="Sisfo Kampus" name="description">
  <link href="../themes/default/index.css" rel="stylesheet" type="text/css">
  </HEAD>
<BODY>


<?php
  TampilkanJudul("Daftar Isi Paket Matakuliah");
  $MKPaketID = $_REQUEST['MKPaketID'];
  $paket = GetFields("mkpaket", "MKPaketID", $MKPaketID, '*');
  $kur = GetaField('kurikulum', 'KurikulumID', $paket['KurikulumID'], 'Nama');
  $prd = GetaField('prodi', 'ProdiID', $_REQUEST['ProdiID'], 'Nama');
  $prg = GetaField('program', 'ProgramID', $_REQUEST['ProgramID'], 'Nama');
  // tampilkan header
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp>Tahun Akademik</td><td class=ul>$_REQUEST[TahunID]</td></tr>
  <tr><td class=inp>Nama Paket</td><td class=ul>$paket[Nama]</td></tr>
  <tr><td class=inp>Kurikulum</td><td class=ul>$kur</td></tr>
  <tr><td class=inp>Program/Program Studi</td><td class=ul>$prg / $prd</td></tr>
  <tr><td class=inp>Pilihan</td><td class=ul><input type=button name='Tutup' value='Tutup' onClick=\"window.close()\"></td></tr>
  </table></p>";
  
  // tampilkan isi paket
  $s = "select mpi.*, mk.MKKode, mk.Nama, mk.SKS
    from mkpaketisi mpi
      left outer join mk on mpi.MKID=mk.MKID
    where mpi.MKPaketID='$MKPaketID'
    order by mk.MKKode";
  $r = _query($s); $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode MK</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl>SKS</th>
    <th class=ttl>Terjadwal?</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $ada = GetaField('jadwal', "TahunID='$_REQUEST[TahunID]' 
      and INSTR(ProgramID, '.$_REQUEST[ProgramID].')>0
      and INSTR(ProdiID, '.$_REQUEST[ProdiID].')>0 and MKKode",
      $w['MKKode'], "count(JadwalID)")+0;
    $c = ($ada > 0)? "class=ul" : "class=nac";
    $yn = ($ada > 0)? 'Y' : 'N';
    echo "<tr><td class=inp>$n</td>
      <td $c>$w[MKKode]</td>
      <td $c>$w[Nama]</td>
      <td $c align=right>$w[SKS]</td>
      <td class=ul align=center><img src='img/$yn.gif'> $ada kelas</td>
      </tr>";
  }
  echo "</table></p>";
?>

<p><input type=button name="Tutup" value="Tutup" onClick="window.close()"></p>

<?php include_once "disconnectdb.php"; ?>
<div class=footer>
<hr size=1 color=silver>
<p>Powered by Sisfo Kampus 2006</p>
</div>

</BODY>
</HTML>