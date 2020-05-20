<?php
error_reporting(0);

function barasiah($arg){
 $safe=mysql_real_escape_string($arg);
 return $safe;
}

function _connect($h, $u, $p) {
  $r = mysql_connect($h, $u, $p) or die("Gagal terkoneksi dengan database server <b>$h</b>");
  return $r;
}
function _select_db($db, $con) {
  return mysql_select_db($db, $con) or die("Gagal membuka database <b>$db</b>.");
}
function _query($s='') {
  $r = mysql_query($s);
  return $r;
}
function _fetch_array($r) {
  $w = mysql_fetch_array($r);
  return $w;
}
function _num_rows($r) {
  return mysql_num_rows($r);
}
function _fetch_row($r) {
  return mysql_fetch_row($r);
}
function _num_fields($r) {
  return mysql_num_fields($r);
}
function _field_name($r, $pos=0) {
  return mysql_field_name($r, $pos);
}
function _affected_rows() {
  return mysql_affected_rows();
}
function _result($r, $brs=0, $fld='') {
  $w = @mysql_result($r, $brs, $fld);
  return $w;
}
?>
