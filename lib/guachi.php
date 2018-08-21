<?php
/*
            ____                  _     _
           / ___|_   _  __ _  ___| |__ (_)
          | |  _| | | |/ _` |/ __| '_ \| |
          | |_| | |_| | (_| | (__| | | | |
           \____|\__,_|\__,_|\___|_| |_|_|
Copyright (c) 2014  Díaz  Víctor  aka  (Máster Vitronic)
Copyright (c) 2018  Díaz  Víctor  aka  (Máster Vitronic)
<vitronic2@gmail.com>   <mastervitronic@vitronic.com.ve>
*/


/** esto deberia implementarlo en una clase
 *
 * Modo paranoia, si se llama directo a este script
 * lo mando a la entrada
 */
function paranoia() {
    if (empty($_SERVER['HTTP_REFERER'])) {
        require_once ROOT . 'modulos' . DS . 'error.php';
        exit();
    }
}

function format_money($money, $locale, $remove = false) {
    $a = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    return $remove ? str_replace($remove, '', $a->format($money)) : $a->format($money);
}

function money2number($money) {
    return str_replace(',', '.', str_replace('.', '', $money));
}

function array_to_object($array) {
    return (object) $array;
}

function object_to_array($object) {
    return (array) $object;
}

function fecha_sql($fecha) {
    return date('Y-m-d', strtotime($fecha));
}

function set_header_content_type_img($file){
    //Number to Content Type
    $ntct = Array( "1" => "image/gif",
                   "2" => "image/jpeg",
                   "3" => "image/png",
                   "6" => "image/bmp",
                   "17" => "image/ico");
    header('Content-type: ' . $ntct[exif_imagetype($file)]);
}
/**
 * Orden natural de un array de dos dimenciones
 *
 * @access public
 * @return array Array ordenado de manera natural
 */
function natsort2d(&$aryInput) {
  $aryTemp = $aryOut = array();
  foreach ($aryInput as $key=>$value) {
    reset($value);
    $aryTemp[$key]=current($value);
  }
  natsort($aryTemp);
  foreach ($aryTemp as $key=>$value) {
    $aryOut[] = $aryInput[$key];
  }
  $aryInput = $aryOut;
} 
/*
	Banker's Rounding v1.01, 2006-08-15
	Copyright 2006 Michael Boone
	mike@Xboonedocks.net (remove the X)
	http://boonedocks.net/

	Provided under the GNU General Public License
	Contact me for use outside the bounds of that license

	---------------------------------------------------------------
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	The GNU General Public License can be found at:
	http://www.gnu.org/copyleft/gpl.html
	---------------------------------------------------------------

	Release History:
	2006-01-05: v1.00: Initial Release
	2006-08-15: v1.01: Updated with faster even/odd test

*/

function bround($dVal,$iDec) {
	// banker's style rounding or round-half-even
	// (round down when even number is left of 5, otherwise round up)
	// $dVal is value to round
	// $iDec specifies number of decimal places to retain
	static $dFuzz=0.00001; // to deal with floating-point precision loss
	$iRoundup=0; // amount to round up by

	$iSign=($dVal!=0.0) ? intval($dVal/abs($dVal)) : 1;
	$dVal=abs($dVal);

	// get decimal digit in question and amount to right of it as a fraction
	$dWorking=$dVal*pow(10.0,$iDec+1)-floor($dVal*pow(10.0,$iDec))*10.0;
	$iEvenOddDigit=floor($dVal*pow(10.0,$iDec))-floor($dVal*pow(10.0,$iDec-1))*10.0;

	if (abs($dWorking-5.0)<$dFuzz) $iRoundup=($iEvenOddDigit & 1) ? 1 : 0;
	else $iRoundup=($dWorking>5.0) ? 1 : 0;

	return $iSign*((floor($dVal*pow(10.0,$iDec))+$iRoundup)/pow(10.0,$iDec));
}
/**
 * retorna true si es un entero
 * @return boolean
 *
 */
function isInteger($input){
    //return(ctype_digit(strval($input)));
    return is_int($input) || is_float($input);
}

/*setea en modo cache a ezsql*/
function cache_sql($cbd, $timeout = false) {
    $cache_timeout = ($timeout !== false)?$timeout : 24;
    $cbd->cache_dir = ROOT.'cache_sql';
    $cbd->use_disk_cache = true;
    $cbd->cache_queries = true;
    $cbd->cache_timeout = $cache_timeout;
}

function millitime() {
  $microtime = microtime();
  $comps = explode(' ', $microtime);
  // Note: Using a string here to prevent loss of precision
  // in case of "overflow" (PHP converts it to a double)
  return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
}
