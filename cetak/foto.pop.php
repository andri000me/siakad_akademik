<?php
include "../sisfokampus.php";
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  include "parameter.php";
  include "cekparam.php";
?>

<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD><TITLE><?php echo $_Institution; ?></TITLE>
  <META content="Emanuel Setio Dewo" name="author">
  <META content="Sisfo Kampus" name="description">
  <link href="index.css" rel="stylesheet" type="text/css">
  </HEAD>
<BODY>

<?php
 $MhswID = $_REQUEST['mhswid'];
 $FotoID = $_REQUEST['foto'];
 $foto = FileFotoMhsw($MhswID, $FotoID);
  // Tampilkan
  echo "<p><table class=box cellspacing=2 cellpadding=4>
    <td class=box style='padding: 2pt'><img src='$foto' width=120 height=150></td></tr>
  </table>";
?>
