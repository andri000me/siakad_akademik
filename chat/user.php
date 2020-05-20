<?php
error_reporting();
  session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";

$idleTime = 1800; // idle time in seconds
$time = time()-$idleTime;

$filter = ($_SESSION['_LevelID']!='1' ? "and k.Login not in ('')":"");
$s = "select distinct(s.user) as _Login,k.Nama as _user, l.Nama as _jabatan,s.LevelID  from session s
	left outer join karyawan k on s.user = k.Login and k.NA='N' $filter
	left outer join level l on k.LevelID = l.LevelID and l.LevelID !=120
	where sessionId != '".$_SESSION['_Session']."' and user != '".$_SESSION['_Login']."'  group by s.user order by s.LevelID, s.user DESC";
$q = _query($s);
if (_num_rows($q) == 0){
	echo "<div align=center class=loadingUser >tidak ada user yang sedang online</div>";
} else {
	while ($w = _fetch_array($q)){
		$name = str_replace(" ","%20",$w['_user']);
		if (!empty($w['_user'])) {
			$w['_user'] = str_replace(" ", "_", $w['_user']);
		echo "<div id='list_".$w['_Login']."' class=userList onMouseOver=hoverList('list_".$w['_Login']."') onMouseOut=outList('list_".$w['_Login']."') onclick=chatWith('".$w['_user']."')>".$w['_user']." <sup>".$w['_jabatan']."</sup></div>";
		}
		else {
		$dsn=GetFields('dosen',"NA='N' and Login",$w['_Login'],"concat(Gelar1,',',Gelar) as Nama");
		$mhs=GetFields('mhsw',"NA='N' and StatusMhswID='A' and MhswID",$w['_Login'],"concat(LEFT(Nama,9),'.. (Mhsw)') as Nama");
		$alumni=GetFields('mhsw',"NA='N' and StatusMhswID='L' and MhswID",$w['_Login'],"concat(LEFT(Nama,9),'.. (Alumni)') as Nama");
		$nmhs=GetFields('mhsw',"NA='N' and MhswID",$w['_Login'],"Nama");
		$ndsn=GetFields('dosen',"NA='N' and Login",$w['_Login'],"Nama");
		$_NamaMhsw = $nmhs['Nama'];
		$namaMhsw = substr($_NamaMhsw,0,10);
		$_NamaMhsw = ($_NamaMhsw=='' ? $namaMhsw : $_NamaMhsw); 
		$strL=(!empty($ndsn['Nama']))? $ndsn['Nama'] : $namaMhsw.'_'.$w['_Login'];
		$strL = ucwords(strtolower($strL));
		//$strL = substr($strL,0,10);
		$strNama = str_replace(" ","_",$strL);
		$strNama = str_replace(".","_",$strNama);

		$Nama = (!empty($dsn['Nama']))? $dsn['Nama'] : substr(ucwords(strtolower($_NamaMhsw)),0,15).'...';
		$Nama2 = (!empty($ndsn['Nama']))? $ndsn['Nama'] : $namaMhsw;
		$STRING = ucwords(strtolower(($w['LevelID']==120 || $w['LevelID']==121)? $w['_Login'] : $ndsn['Nama']));
			if (!empty($STRING)){
				echo "<div id='list_$w[_Login]' class='userList' onMouseOver=hoverList('list_$w[_Login]') onMouseOut=outList('list_$w[_Login]') onclick=chatWith('$strNama') title='".$_NamaMhsw."'>".$STRING." <sup>".$Nama.(!empty($alumni['Nama']) ? $alumni['Nama'] :"")."</sup></div>";
			}else{
				//$delete = _query("DELETE from session where user='$w[_Login]'");
			}
		}
	}
	?><script>$('.userboxcontent').slimScroll({
		height: '310px',
        alwaysVisible: true,
        disableFadeOut: true,
        touchScrollStep: 50
    });</script> <?php
}

?>
<script>
function hoverList(id){
	$('#'+id).css('background-color','#ecd1a6');
	$('#'+id).css('color','#FFFFFF');
}
function outList(id){
	$('#'+id).css('background-color','#FFFFFF');
	$('#'+id).css('color','#666666');
}
</script>
