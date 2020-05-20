<?php
// Author: Irvandy Goutama
// 09 September 2009

function GetMonthName($integer, $language='INA')
{	$arrMonth = array();

	if($language = 'INA')
		$arrMonth = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
	else if($language = 'USA')
		$arrMonth = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	
	return $arrMonth[$integer-1];
}

function BreakString($pattern, $divider, $offset=1)
{	$temp = $pattern; $arrResult = array();
	while(!empty($temp))
	{
		$pos = strpos($temp, $divider);
		if($pos > 0) 
		{	$arrResult[] = substr($temp, 0, $pos);
			$temp = substr($temp, $pos);
		}
		
		if(strlen($temp) > $offset)
		{	$arrResult[] = substr($temp, 0, strlen($divider)+$offset);
			$temp = substr($temp, strlen($divider)+$offset);
		}
		else
		{	$arrResult[] = $temp;
			$temp = '';
		}
	}
	return $arrResult;
}

function GetDateInWords($string, $pattern='%Y-%m-%d', $language='INA')
{	$arr = BreakString($pattern, '%', 1);	
	$temp = $string;
	$result = '';
	
	$year = ''; $month = ''; $day = '';
	foreach($arr as $a)
	{	$length = 0;
		if($a=='%Y')
		{ 	$year = $substring = substr($temp, 0, 4);
			$length = 4;
		}
		else if($a=='%y')
		{ 	$substring = substr($temp, 0, 2);
			$year = (($year+0 < 30)? '20' : '19').$substring;
			$length = 2;
		}
		else if($a=='%m') 
		{	$month = $substring = GetMonthName(substr($temp, 0, 2), $language);
			$length = 2;
		}
		else if($a=='%d') 
		{	$day =  $substring = substr($temp, 0, 2);
			$length = 2;
		}
		else 
		{	$substring = $a;
			$length = strlen($a);
		}
	
		$temp = substr($temp, $length);
	}
	return $day.' '.$month.' '.$year;
}

function UbahKeRomawiLimit99($integer)
{	$arrRomanOnes = array('I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX');
	if($integer<10) return $arrRomanOnes[$integer-1]; 
	else
	{	if($integer<100)
		{	$arrRomanTens = array('X', 'XX', 'XXX', 'XL', 'L', 'LX', 'LXX', 'LXXX', 'XC');
			$integertens = floor($integer/10);
			return $arrRomanTens[$integertens-1].$arrRomanOnes[$integer-1];
		}
		else
		{	return 'FAIL';
		}
	}
}
?>
