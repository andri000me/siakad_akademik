<div id="page-top-outer">    
<div id="page-top">
	<div id="logo">
	<a href=""><img src="img/logo.png" height="80" style="margin-top:-10px" alt="" /></a>
	</div>
	<div class="clear"></div>

</div>
</div>	
<div class="nav-outer-repeat"> 
<div class="nav-outer"> 
<?php
	if (!empty($_SESSION['_Session'])) {
	
      $NamaLevel = GetaField('level', 'LevelID', $_SESSION['_LevelID'], 'Nama');

      if (!empty($_SESSION['mdlid'])) {
        $_strMDLID = GetaField('mdl', "MdlID", $_SESSION['mdlid'], "concat(MdlGrpID, ' &raquo; ', Nama)");
     //   echo "<div class=MenuDirectory>Menu: $_strMDLID</div>";
      }
     // echo "<div class=NamaLogin>Login: <b>$_SESSION[_Nama]</b> ($NamaLevel) &raquo; <a href='?mnux=loginprc&gos=lout'>Logout</a></div>
	//		<div class=WaktuServer><b>Waktu server:</b> <span id='clock' title='".date('m d, Y H:i:s')."'>&nbsp;</span>&nbsp;</div>";
	//		echo '<script type="text/javascript" src="chat/js/chat.js"></script>';
			$_SESSION['username'] = $_SESSION['_Login']; // Must be already set
			$tombolChat = "<div id='onlineUser' onClick='javascript:openUser()'></div>";
			 cekSession();
	?>		 
		<div id="nav-right">
			<div class="showhide-account2"><b>Waktu server:</b> <span id='clock' title='<?php echo date('m d, Y H:i:s'); ?> '> &nbsp; </span></div>
			<div class="nav-divider">&nbsp;</div>
			
			<a href="?mnux=loginprc&gos=lout" id="logout"><img src="img/nav_logout.gif" width="64" height="14" alt="" /></a>
			<div class="clear">&nbsp;</div>
		</div>
		<div class="nav">
		<div class="showhide-account">Selamat Datang : <?php echo $_SESSION['_Nama']; ?></b><span> (<?php echo $NamaLevel; ?>)</span></div>
		</div>
	<?php	
    //  if (empty($_REQUEST['BypassMenu'])) include "menusis.php";
    } else {
		echo '<script>
			$("#userbox").css("display","none");
		</script>';
	}
	?>
</div>
</div>