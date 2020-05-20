<?php error_reporting(E_ALL);
if (empty($_GET['s'])){
echo '<table width=900 style="height:600px; margin:0 auto" align=center>
<tr><th class=ttl>Dari</th><th class=ttl>Kepada</th><th class=ttl>Pesan</th><th class=ttl>Waktu</th></tr>
<tbody id="LogChat"></tbody>
</table>';
$_SESSION['LastChat'] = GetaField('chat',"id>",1,"MAX(id)")-200;
?>
<script>
  function showlog(){
    $.ajax({
      url: "chat/log.php?s=1",
      cache: false,
      success: function(html){
      $("#LogChat").append(html);
      }
    });
    setTimeout(showlog,20000);
  }
  showlog();
</script>
<?php
}else{
  session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  CekLog($_SESSION['LastChat']);
}
function CekLog($last){
  $s = "SELECT * from chat where id > $last limit 200";
  $r = _query($s);
  while ($w = _fetch_array($r)){
    $lastChat .= "<tr><td><span style='display:none'>$_SESSION[LastChat]</span> $w[froms]</td><td>$w[tos]</td><td>$w[message]</td><td>$w[sent]</td></tr>";
  }
  $_SESSION['LastChat'] = GetaField('chat',"id>",1,"MAX(id)");
  echo $lastChat;
}
?>

