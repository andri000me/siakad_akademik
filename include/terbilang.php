<?php
// Author: E. Setio Dewo, setio_dewo@sisfokampus.net
// Juni 2004

$unitid = array('', 'satu ', 'dua ', 'tiga ', 'empat ', 'lima ', 'enam ', 'tujuh ', 'delapan ', 'sembilan ');

function cDecimal($str='') {
  //echo "<b>$str</b><br>";
  global $unitid;
  $ret = '';
  $num = $str;
  //settype($num, 'integer');
  //settype($num, 'string');
  if (strlen($num) == 0) $ret = '';
  else {
    for ($i = 0; $i < strlen($num); $i++) {
      $n = substr($num, $i, 1);
      $ret = ($n == 0) ? $ret.'kosong ' : $ret.$unitid[$n];
    }
  }
  $ret = (!empty($ret)) ? ' koma ' . $ret : $ret;
  return $ret;
}
function Num2IndWords($num, $WordIdx) {
  global $unitid;
  $ret = '';
  $LargeNum = array('', 'ribu ', 'juta ', 'milliar ', 'triliun ', 'quadriliun ');
  $Ten = array('', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh');
  $Twenty = array(10=>'sepuluh ', 11=>'sebelas ', 12=>'dua belas ', 13=>'tiga belas ', 14=>'empat belas ', 
    15=>'lima belas ', 16=>'enam belas ', 17=>'tujuh belas ', 18=>'delapan belas ', 19=>'sembilan belas ');
  // mulai
  $n = $num;
  if ($num > 99) {
    $nQty = floor($n / 100);
    $n = $n % 100;
    $ret = ($nQty == 1) ? 'seratus ' : $unitid[$nQty] . 'ratus ';
  }
  if ((1 <= $n) and ($n <= 9)) $ret = $ret . $unitid[$n];
  elseif ((10 <= $n) and ($n <= 19)) $ret = $ret . $Twenty[$n];
  elseif ((20 <= $n) and ($n <= 99)) {
    $nQty = floor($n / 10);
    $nMod = $n % 10;
    $ret = $ret . $Ten[$nQty -1];
    $ret = ($nMod != 0) ? $ret .' '.$unitid[$nMod] : $ret.' ';
  } 
  else {
    $ret = (!empty($ret)) ? $ret : '';
  }
  $ret = (($WordIdx == 1) and ($ret == 'satu ')) ? 'se' . $LargeNum[$WordIdx] : $ret . $LargeNum[$WordIdx];
  return $ret;
}
function SpellNumberID($num = 0) {
  global $unitid;
  $ret = '';
  if ($num > 999999999999999999) die('Wah angkanya kebesaran tuh...');
  $numi = number_format($num, 2, '.', '');
  $neg = false;
  $numi = str_replace('-', '', $numi);
  $idx = strpos($numi, '.');
  //echo $idx;
  $idx = ($idx == 0)? strlen($numi)+1 : $idx;
  //echo $idx;
  $dec = substr($numi, $idx+1, strlen($numi)-$idx);
  $dec = ($dec == 0) ? '' : cDecimal($dec);
  $str = substr($numi, 0, $idx);
  //echo $str.'<br>';
  $idx = 0;
  $i = strlen($str);
  while ($i > 0) {
    if ($i < 3) {
      $ret = Num2IndWords(substr($str, 0, $i), $idx) . $ret;
      $str = '';
    }
    else {
      $ret = Num2IndWords(substr($str, $i-3, 3), $idx) . $ret;
      //echo substr($str, $i-3, 3).'<br>';
      $str = substr($str, 0, $i-3);
      //echo "$str<br>";
    }
    $i = strlen($str);
    $idx++;
  }
  $ret = $ret . $dec;
  return $ret;
}
function SpellNumberEN($num = 0) {
}

//echo Num2IndWords(125, 1) . "<br>";
/*
$num = 219555.78;
echo "$num<br>";
echo SpellNumberID($num);
*/
?>
