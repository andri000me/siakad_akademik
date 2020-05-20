<?php
include "../sisfokampus.php";
//include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
include_once "mhsw.hdr.php";
  
function CariNPM(){
  Global $arrID;
  echo "<p><table class=box cellpadding=4 cellspacing=1>
  <form action=? method=POST>
  <input type=hidden name=mnux value=inq.status>
  <input type=hidden name=gos value=TampilStatus>
  <tr><td class=inp1 colspan=2><b>$arrID[Nama]</b></td></tr>
  <tr><td class=inp>NPM  :</td><td class=ul><input type=text name=mhswcrid value='$_SESSION[mhswcrid]' maxlength=20 size=20></td></tr>
  <tr><td class=ul colspan=2><input type=submit name=mhswcr value=NPM></td></tr>
  </form>
  </table></p>
  ";
}

function MhswDet() {
  $m = GetFields("mhsw m
    left outer join program prg on m.ProgramID=prg.ProgramID
    left outer join prodi prd on m.ProdiID=prd.ProdiID
    left outer join statusmhsw sm on m.StatusMhswID=sm.StatusMhswID
    left outer join statusawal sa on m.StatusAwalID=sa.StatusAwalID
    left outer join bipot bpt on m.BIPOTID=bpt.BIPOTID",
    "m.MhswID", $_SESSION['mhswcrid'],
    "m.*, prg.Nama as PRG, prd.Nama as PRD, bpt.Nama as BPT, sm.Nama as SM, sa.Nama as SA");
  $hdr = "TampilkanHeader".$_SESSION['UkuranHeader'];
  $hdr($m, 'mhsw.inq', 'MhswDet','0');
  return $m;
}

function TampilStatus(){
  $s = "select khs.StatusMhswID, st.Nama, khs.TahunID from khs
        left outer join mhsw m on khs.MhswID = m.MhswID
		left outer join statusmhsw st on khs.StatusMhswID = st.StatusMhswID
		where khs.MhswID = '$_SESSION[mhswcrid]'";
		
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr>
	  <th class=ttl>#</th>
	  <th class=ttl>Semester</th>
      <th class=ttl>Status</th>
	</tr>";
	$n = 1;
	while($w = _fetch_array($r)){
	  //$c = GetFields('mhsw left outer join statusmhsw st on mhsw.StatusMhswID = st.StatusMhswID','MhswID',$_SESSION['mhswcrid'],'st.Nama,st.Keluar' );
      $cs2 = ($w['Nama'] == 'Pasif') ? 'nac' : 'ul';
	  echo "<tr><td class=inp1>$n.</td>
	        <td class=$cs2>$w[TahunID]</td>
			<td class=$cs2>$w[Nama]</td></tr>";
	  $n++;
	}
	echo "</table></p>";
}

function StatusSaatIni(){
  $c = GetFields('mhsw left outer join statusmhsw st on mhsw.StatusMhswID = st.StatusMhswID','MhswID',$_SESSION['mhswcrid'],'st.Nama,st.Keluar' );
  $cs = ($c['Keluar'] == 'Y') ? 'nac' : 'ul';
  echo "<table class=box cellpadding=4 cellspacing=1>
       <tr><td class=ttl>Status Mahasiswa Saat Ini</td>
       <td class=$cs>&nbsp;&nbsp;&nbsp;<b>$c[Nama]</b>&nbsp;&nbsp;&nbsp;</td></tr></table>";
}

//Parameter
$mhswcrid = GetSetVar('mhswcrid');
$UkuranHeader = GetSetVar('UkuranHeader', 'Besar');
//$gos = (empty($_REQUEST['gos']))? 'CariNPM' : $_REQUEST['gos'];

$cek = GetaField('mhsw','MhswID',$_SESSION['mhswcrid'],'MhswID');

TampilkanJudul('History Mahasiswa Bolos');
//$gos();
CariNPM();

if(!empty($_SESSION['mhswcrid']) and !empty($cek)){
  $m = MhswDet();
  StatusSaatIni();
  TampilStatus();
}  
include "disconnectdb.php";
?>
