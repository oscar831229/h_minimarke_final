<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Persé
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class MoneyChange extends UserComponent {

	private static $_wasError = false;

	public static function getCurrency($currencyId){
		if($currencyId!=18){
			$fecha = date('Y-m-d');
			$hora = date('G');
			self::$_wasError = false;
			$currencyData = self::getModel('Currencies')->findFirst($currencyId);
			if($currencyData){
				$currency = self::getModel('CurrencyHistory')->findFirst("currencies_id='$currencyId' AND query_date='$fecha' AND hour='$hora'");
				if(!$currency){
					ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)');
					$html = @file_get_contents('http://www.google.com/finance/converter?a=1&from='.$currencyData->getCode().'&to=COP');
					if($html!=false){
						$dom = new DOMDocument();
						@$dom->loadHTML($html);
						$xpath = new DOMXPath($dom);
						$list = $xpath->query('//span[@class="bld"]');
						foreach($list as $l){
							$valor = str_replace(',', '', substr($l->nodeValue, 0, strpos($l->nodeValue, ' ')));
							$currencyHistory = new CurrencyHistory();
							$currencyHistory->setCurrenciesId($currencyId);
							$currencyHistory->setQueryDate($fecha);
							$currencyHistory->setHour($hora);
							$currencyHistory->setValor($valor);
							if($currencyHistory->save()==false){
								Flash::error('No se pudo obtener el factor de conversión para '.i18n::strtoupper($currencyData->getNameEs()));
								self::$_wasError = true;
								return 1;
							}
							Session::set('convSymbol', $currencyData->getCode());
							return (double) $valor;
						}
					}
				} else {
					Session::set('convSymbol', $currencyData->getCode());
					return (double) $currency->getValor();
				}
			} else {
				self::$_wasError = true;
				Flash::error('No se pudo obtener el factor de conversión para '.i18n::strtoupper($currencyData->getNameEs()));
				return 1;
			}
		} else {
			return 1;
		}
	}

	public static function _warnAccuracy($translate){
		Flash::notice($translate['convUse']);
	}

	public static function wasError(){
		return self::$_wasError;
	}

	public static function setCurrencyFromLocale(){
		switch(Session::get('locale')){
			case 'en_US':
				Session::set('currencyId', 1);
				Session::set('currencyFactor', MoneyChange::getCurrency(1));
				break;
			case 'fr_FR':
				Session::set('currencyId', 9);
				Session::set('currencyFactor', MoneyChange::getCurrency(9));
				break;
			case 'es_CO':
			default:
				Session::set('currencyId', 18);
				Session::set('currencyFactor', 1);
				break;
		}
	}

}
