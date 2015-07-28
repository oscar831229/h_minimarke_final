<?php

session_start();

require "../hfos/locale.php";

if(!isset($_SESSION['unxfec'])){
	$fecha = time();
} else {
	$fecha = $_SESSION['unxfec'];
}

header("Expires: Fri, 21 Jun 2008 00:00:01 GMT", true);
header("Content-type: image/png");
header("Pragma: cache", true);
header("Cache-control: store,cache", true);
if(!file_exists("../temp/ical$fecha.png")){
	$im = imagecreatefrompng('ical.png');
	$im_dest = imagecreatetruecolor(128, 128);
	imagealphablending($im_dest, false);
	$negro = imagecolorallocate($im, 54, 54, 54);
	$blanco = imagecolorallocate($im, 252, 252, 252);
	$mes = strtoupper(strftime("%B", $fecha));
	imagettftext($im, 11, 10, 18, 51, $blanco, '../font/Aquabase.ttf', $mes);
	imagettftext($im, 44, 10, 32, 103, $negro, '../font/Aquabase.ttf', sprintf("%02d", date("d", $fecha)));
	imagecopy($im_dest, $im, 0, 0, 0, 0, 128, 128);

	imagesavealpha($im_dest, true);
	imagepng($im_dest, "../temp/ical$fecha.png", 7);
}

readfile("../temp/ical$fecha.png");

?>
