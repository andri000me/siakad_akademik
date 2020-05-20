<HTML>
<HEAD>
<TITLE>Pesan dari System</TITLE>
<style type="text/css">
BODY {
  font-family: tahoma, verdana;
  font-size: 12pt;
}
.box {
  border: 1px solid silver;
  padding: 1px;
  font-size: 12pt;
}
</style>
</HEAD>
<BODY>

<script language="JavaScript">
<!--
window.resizeTo(400,300);
window.moveTo(100,100);
-->
</script>

<table class=box cellspacing=1 cellpadding=4 width=100%>
<tr><td><p><?php echo $_REQUEST['Pesan']; ?></p>
<hr size=1>
<input type=button name='Tutup' value='Oke' onClick="window.close()">
</td></tr>
</table>

</BODY>
</HTML>