<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package		Front-Office
 * @copyright	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class HfosWheater extends UserComponent {

	public static function query(){
		$wheater = self::getActive();
		if(!$wheater){
			$db = getConnection();
			$dathot = $db->fetch_one('SELECT wheater, location FROM dathot LIMIT 1');
			ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)');
			$xmlData = @file_get_contents('http://clima.msn.com/RSS.aspx?wealocations='.$dathot['wheater'].'&weadegreetype=C&culture=es-US&setunit=C');
			if($xmlData){
				$xml = new DOMDocument();
				if(@$xml->loadXML($xmlData)){
					$xpath = new DOMXPath($xml);
					foreach($xpath->query('//rss/channel/item/description') as $description){
						$clima = strip_tags($description->textContent);
						$text = $description->textContent;
						if(($k=strpos($clima, 'Sensac'))!==false){
							$clima = substr($clima, 0, $k-1);
						} else {
							if(($k=strpos($clima, 'Posibilidad de'))!==false){
								$clima = substr($clima, 0, $k-1);
							} else {
								break;
							}
						}
						$clima = str_replace(array('&#176;C.', '&#176;C', '°C'), ' grados centígrados', $clima);
						$clima = str_replace(array('a.m.'), 'a m ', $clima);
						$clima = str_replace(array('p.m.'), 'p m ', $clima);
						$clima = str_replace(array('Mín'), 'Mínimo', $clima);
						$clima = str_replace(array('Máx'), 'Máximo', $clima);
						$clima = str_replace(array(':00', ':'), ' ', $clima);
						$clima = str_replace(array('(', ')'), '', $clima);
						$message = 'El clima de '.$dathot['location'].' es '.$clima.'.';
						break;
					}
					$status = 'Desconocido';
					$xmlCdData = new DOMDocument();
					if(@$xmlCdData->loadHTML($text)){
						$xpathCdData = new DOMXPath($xmlCdData);
						foreach($xpathCdData->query('//body/p/img') as $img){
							$status = $img->getAttribute('title');
						}
						$clima = addslashes($clima);
						unset($xpathCdData);
					}

					$wheater = new Wheater();
					$wheater->setDb('ramocol');
					$wheater->setToday(date('Y-m-d'));
					$wheater->setDate(date('G'));
					$wheater->setHour(date('G'));

					$sql = "INSERT INTO ".WHEATER_DB.".wheater (db, today, hour, content, original, status) VALUES
					('".JASMIN_DB_NAME."', '".date('Y-m-d')."', '".date('G')."', '$clima', '$text', '$status')";

					unset($clima);
					unset($status);
					unset($xmlCdData);
					unset($xpath);
				}
				unset($xmlData);
				unset($xml);
			}
		}
		unset($wheater);
	}

	public static function getActive(){
		$db = getConnection();
		$dathot = $db->fetch_one('SELECT wheater, location FROM dathot LIMIT 1');
		$wheater = $db->fetch_one("SELECT content,status FROM ".WHEATER_DB.".wheater WHERE db='".JASMIN_DB_NAME."' AND today='".date('Y-m-d')."' AND hour='".date('G')."' LIMIT 1");
		if($wheater){
			if($wheater['content']!=""){
				$message = 'El clima en '.$dathot['location'].' es '.$wheater['content'].'.';
				$status = $wheater['status'];
			}
		}
		if($status){
			$hour = (int) date('G');;
			if($hour>18||$hour<6){
				$night = '_night';
			} else {
				$night = '';
			}
			switch($status){
				case 'Despejado':
					$image = 'sunny';
					break;
				case 'Llovizna ligera':
				case 'Llovizna por la tarde':
				case 'Lluvia matutina':
					$image = 'shower1';
					break;
				case 'Mayormente nublado':
					$image = 'cloudy4';
					break;
				case 'Niebla':
					$image = 'cloudy2';
					break;
				case 'Nublado':
					$image = 'cloudy3';
					break;
				case 'Parcialmente nublado':
					$image = 'cloudy1';
					break;
				case 'Chubascos':
					$image = 'shower2';
					break;
				case 'Tormentas':
					$image = 'tstorm1';
					break;
				case 'Chubascos / Claros':
					$image = 'shower1';
					break;
				default:
					$image = 'dunno';
					$wheater['status'] = 'Desconocido';
					$wheater['content'] = 'No es posible determinar el clima en este momento';
			}
			$image.=$night;
			$wheater['image'] = $image;
			return $wheater;
		} else {
			return false;
		}
	}

}