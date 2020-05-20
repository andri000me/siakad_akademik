<?php
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
CetakLabelJadwal();
include_once "disconnectdb.php";

function CetakLabelJadwal(){
	global $_lf, $_HeaderPrn;
	
	$tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid  = $_REQUEST['prid'];	
	$jadwalid = $_REQUEST['JadwalID'];
	$asal = $_REQUEST['asal']+0;
	$labelJdwl = HOME_FOLDER  .  DS . "tmp/labeljdwl.dwoprn";
	$mrg = str_pad(' ', 5, ' ');
	
	$jenisctk = ($asal > 0) ? "and j.JadwalID = $jadwalid" : "";
	
	$s = "select j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli, mk.Sesi, j.JenisJadwalID, j.DosenID,
    jj.Nama as JenisJadwal, concat(d.Nama, ', ', d.Gelar) as DSN,
    h.Nama as HR, time_format(j.JamMulai, '%H:%i') as JM, time_format(j.JamSelesai, '%H:%i') as JS
    from jadwal j
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on j.DosenID=d.Login
      left outer join hari h on j.HariID=h.HariID
      left outer join mk mk on j.MKID=mk.MKID
    where j.TahunID='$tahun'
      and INSTR(j.ProgramID, '.$prid.') > 0
      and INSTR(j.ProdiID, '.$prodi.') > 0
			$jenisctk
    order by j.MKKode, j.NamaKelas, j.JenisJadwalID";
  $r = _query($s);
	//echo "<pre>$s</pre>";
	//exit;
	$f = fopen($labelJdwl, 'w');
	$n = 0;
	
	fwrite($f, $_HeaderPrn);
  while ($w = _fetch_array($r)) {
    $n++;
		$rps = ($w['JenisJadwalID'] == 'K') ? '' : "($w[JenisJadwalID])";
    fwrite($f, chr(27).chr(15));
    fwrite($f, $mrg . NamaTahun($tahun).$_lf);
    fwrite($f, chr(27).chr(119).'0'.$_lf);
    fwrite($f, chr(27).chr(15));
    fwrite($f, $mrg . $w['MKKode'].' '.$w['Nama'].' '.$rps.$_lf);
    fwrite($f, $mrg . "KELAS : " . $w['NamaKelas'].$_lf);
    fwrite($f, $mrg . $w['DosenID'].' '.$w['DSN'].$_lf);
		fwrite($f, $mrg . $w['HR'].', '.$w['JM'].' - '.$w['JS'].$_lf);
    fwrite($f, chr(27).chr(18));
    fwrite($f, $_HeaderPrn);
    fwrite($f, $_lf.$_lf.$_lf);
  }
  fwrite($f, chr(27).chr(18).chr(67).chr(66));
  fclose($f);
  include_once "dwoprn.php";
  DownloadDWOPRN($labelJdwl);
}
?>
