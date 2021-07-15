<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @author 		BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * HfosTime
 *
 * Genera información de fecha y hora amigables
 *
 */
class HfosTime extends UserComponent {

	/**
	 * Obtiene la hora actual en un mensaje más amigable
	 *
	 * @return string
	 */
	public static function getCurrentTime(){
		$momento = '';
		$m = date('i');
		$h = $g = date('G');
		if($m>=53&&$m<=57){
			if($h!=1){
				if($h>12){
					$h-=12;
				}
				$preMomento = 'Faltan 5 minutos para las '.$h;
			} else {
				$preMomento = 'Faltan 5 minutos para la una';
			}
		} else {
			if($m>=48&&$m<=52){
				if($h!=1){
					if($h>12){
						$h-=12;
					}
					$preMomento = 'Faltan 10 minutos para las '.$h;
				} else {
					$preMomento = 'Faltan 10 minutos para la una';
				}
			} else {
				if($m>=13&&$m<=17){
					if($h!=1){
						if($h>12){
							$h-=12;
						}
						if($h==0){
							$preMomento = 'Son las 12 y quince ';
						} else {
							$preMomento = 'Son las '.$h.' y quince ';
						}
					} else {
						$preMomento = 'Es la una y quince ';
					}
				} else {
					if($m>=27&&$m<=33){
						if($h!=1){
							if($h>12){
								$h-=12;
							}
							$preMomento = 'Son las '.$h.' y media ';
						} else {
							$preMomento = 'Es la una y media ';
						}
					} else {
						$preMomento = 'Son las '.date('g:i');
					}
				}
			}
		}
		$g = date('G');
		if($g<1){
			$momento = 'de la noche';
		} else {
			if($g<=4){
				$momento = 'de la madrugada';
			} else {
				if($g>4&&$g<12){
					$momento = 'de la mañana';
				} else {
					if($g==12){
						$momento = 'del medio día';
					} else {
						if($g>=13&&$g<19){
							$momento = 'de la tarde';
						} else {
							if($g>=19){
								$momento = 'de la noche';
							}
						}
					}
				}
			}
		}
		return $preMomento.' '.$momento;
	}

}