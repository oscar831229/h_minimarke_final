<?php

class HfosDate extends UserComponent {

	public static function getLocale(){
		/*if(!$time){
			$time = $_SESSION['unxfec'];
		}
		if(!isset($_SESSION['ld'][$time])){
			$ftime = strftime($format, $time);
			$ftime = ucwords($ftime);
			$ftime = str_replace(' De ', ' de ', $ftime);
			$_SESSION['ld'][$time] = $ftime;
			return $ftime;
		} else {
			return $_SESSION['ld'][$time];
		}*/
		$date = new Date();
		echo $date->getLocaleDate('full');
	}

}