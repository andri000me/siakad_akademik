<?php
  session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  $mdlid = GetSetVar('mdlid');
  $timezone = "Asia/Jakarta";
  
if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
$loadTime = date('m d, Y H:i:s');
  
  //disini mulai untuk log system by Arisal Yanuarafi
  function cekSession(){
  	/*$s = "select * from session where sessionId = '".$_SESSION['_Session']."' and user = '".$_SESSION['_Login']."'";
	$q = _query($s);
	$w = _fetch_array($q);
	if (mysql_num_rows($q) == 0){
		$s2 = "insert into session (sessionId,user,address,sessionTime) values ('".$_SESSION['_Session']."', '".$_SESSION['_Login']."', '".$_SERVER['REMOTE_ADDR']."', '".time()."')";
		$q2 = _query($s2);

	} else {
		$s2 = "update session set sessionTime = '".time()."' where sessionId = '".$w[sessionId]."'";
		$q2 = _query($s2);
		   // $hosts = gethostbynamel('');

		 //	$ipLokal = $hosts[0];
 		//if (($_SERVER[REQUEST_URI] != "/sisfo/?mnux=baa/prc.ipk&gos=Proses") && ($_SERVER[REQUEST_URI] != "/sisfo/index.php?mnux=baa/prc.ipk&gos=Proses") && ($_SESSION['mnux'] != "syslog") && ($_SERVER[REQUEST_URI] != "/sisfo/?") && ($_SERVER[REQUEST_URI] != "/sisfo/") && ($_SERVER[REQUEST_URI] != "/sisfo/index.php?")) {
  				//$ipGlobal = $_SERVER['REMOTE_ADDR'];
  		//	$s="insert into log (Login,LevelID,IPGlobal,IPLokal,Script) values ('$_SESSION[_Login]','$_SESSION[_LevelID]','$ipGlobal','-','$_SERVER[REQUEST_URI]')";
 				// $r=_query($s);	
		//}
	} */
	
  }
 ?>
 
<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD><TITLE>Sistem Informasi Akademik <?php echo $_Institution; ?></TITLE>
  <META http-equiv="cache-control" content="max-age=0">
  <META http-equiv="pragma" content="no-cache">
  <META http-equiv="expires" content="0" />
  <META http-equiv="content-type" content="text/html; charset=UTF-8">
  
  <META content="Arisal" name="author" />
  

  <link rel="stylesheet" type="text/css" href="../themes/default/index.css" />
  <link rel="stylesheet" type="text/css" href="../themes/default/ddcolortabs.css" />
	<link type="text/css" rel="stylesheet" media="all" href="../chat/css/chat.css" />
	<link type="text/css" rel="stylesheet" media="all" href="../chat/css/screen.css" />
	
	<!--[if lte IE 7]>
	<link type="text/css" rel="stylesheet" media="all" href="chat/css/screen_ie.css" />
	<style>
	.footer {
		clear: both;
		text-align: center;
		padding: 4px;
		background: transparent url(themes/default/img/bot_bg.jpg) repeat-x scroll;
		border-top: 1px solid #DDD;
		border-bottom: 1px solid #DDD;
		bottom:0px;
		position:absolute;
		width:100%;
	}
	.chatboxcontent {
		width:225px;
		padding:7px;
	}
	</style>
	<![endif]-->
	<script type="text/javascript">
<!--

 function unhide(divID) {
 var item = document.getElementById(divID);
 if (item) {
 item.className=(item.className=='arisal_hide')?'arisal_unhide':'arisal_hide';
 }
 }

//-->
</script>
	
	<script type="text/javascript" src="../chat/js/jquery-1.2.6.min.js"></script>
 <script type="text/javascript" language="javascript" src="../include/js/dropdowntabs.js"></script>

  <!-- <script type="text/javascript" language="javascript" src="include/js/jquery.js"></script> -->
  <script type="text/javascript" language="javascript" src="../floatdiv.js"></script>
  <script type="text/javascript" language="javascript" src="../include/js/drag.js"></script>
  <link rel="stylesheet" type="text/css" href="../themes/default/drag.css" />
  
  <link href="../fb/facebox.css" media="screen" rel="stylesheet" type="text/css" />
  <script src="../fb/facebox.js" language='javascript' type="text/javascript"></script>
  
  <script type="text/javascript" language="javascript" src="../include/js/boxcenter.js"></script>
  <script type="text/javascript" language="javascript" src="../clock.js"></script>
  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $('a[rel*=facebox]').facebox();
	  $("input[type=button]").attr("class","buttons");
	  $("input[type=submit]").attr("class","buttons");
	  $("input[type=reset]").attr("class","buttons");
    })
  </script>
  <!--<script type="text/javascript" language="javascript" src="include/js/jquery.autocomplete.js"></script>-->
  <!--<script type="text/javascript" language="javascript" src="include/js/jtip.js"></script>-->
  </HEAD>
<BODY onLoad="setClock('<?php print $loadTime ?>'); setInterval('updateClock()', 1000 );document.getElementById('userlogin').focus(); " >
<div id="main_container">
  <?php
  if (!empty($_SESSION['_Session'])) {
    include "header.php";
}
    if (!empty($_SESSION['_Session'])) {
	      $NamaLevel = GetaField('level', 'LevelID', $_SESSION['_LevelID'], 'Nama');

      if (!empty($_SESSION['mdlid'])) {
        $_strMDLID = GetaField('mdl', "MdlID", $_SESSION['mdlid'], "concat(MdlGrpID, ' &raquo; ', Nama)");
        echo "<div class=MenuDirectory>Menu: $_strMDLID</div>";
	echo "";
      }
	  $PesanLogin = GetaField('pesanlogin', 'PesanID', '1', 'Pesan');
	  $LogoutButton = ($_SESSION['_LevelID']==120)? '' : "&raquo; <a href='?mnux=loginprc&gos=lout'>Logout</a>";
      echo "<div class=NamaLogin>Login: <b>$_SESSION[_Nama]</b> ($NamaLevel) $LogoutButton</div>";
	   echo "<div class=WaktuServer><b>Waktu server:</b> <span id='clock' title='".date('m d, Y H:i:s')."'>&nbsp;</span>&nbsp;</div>";
						echo '<script type="text/javascript" src="chat/js/chat.js"></script>';
		$tombolChat = "<div id='onlineUser' onClick='javascript:openUser()'></div>";		

			 $NM = str_replace(" ","_",$_SESSION['_Nama']);
					$_SESSION['username'] = $NM; // Must be already set

			 cekSession();
      if (empty($_REQUEST['BypassMenu'])) include "menusis.php";
    } else {
		echo '<script>
			$("#userbox").css("display","none");
					</script>';
			
	}

    echo "<div class=isi>";

    if (file_exists($_SESSION['mnux'].'.php')) {
      // cek apakah berhak mengakses? Harus dicek 1 per 1 karena mungkin 1 modul tersedia bagi banyak level
      $sboleh = "select * from mdl where Script='$_SESSION[mnux]'";
      $rboleh = _query($sboleh); $ktm = -1;
      if (_num_rows($rboleh) > 0) {
        while ($wboleh = _fetch_array($rboleh)) {
          $pos = strpos($wboleh['LevelID'], ".$_SESSION[_LevelID].");
          if ($pos === false) {}
          else $ktm = 1;
        }
        if ($ktm <= 0) {
          echo ErrorMsg("Anda Tidak Berhak",
            "Anda tidak berhak mengakses modul ini.<br />
            Hubungi Sistem Administrator untuk memperoleh informasi lebih lanjut.
            <hr size=1>
            Pilihan: <a href='?mnux=&slnt=loginprc&slntx=lout'>Logout</a>");
        }
        else {include_once 'dikti.php';
		echo "<br><br>";}
      }  else {include_once 'dikti.php';
		echo "<br><br>";}
      include_once "../disconnectdb.php";
    }
    else include_once 'dikti.php';
  ?>
 
  </div>
  <?php echo $tombolChat.$_SESSION['username'] ?>  
  <div class="pengumuman"><marquee onMouseOver="this.stop();" onMouseOut="this.start();" scrolldelay="150"><?php
  $PesanLogin = str_replace("##"," <img src='img/logo_min.png'> ",$PesanLogin);
  echo $PesanLogin; ?></marquee></div>



  <script>
  JSFX_FloatDiv("divInfo", 0, 200).flt();
  </script>

</BODY>
</HTML>
