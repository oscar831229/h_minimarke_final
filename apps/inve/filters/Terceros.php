<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class TercerosFilter
//implements FilterInterface
{

	/**
 	 * Ejecuta el filtro
 	 *
 	 * @param string $str
 	 * @return string
 	 */
	public function execute($str){
		if(preg_match('/[0-9A-Za-z]+/', $str, $matches)){
			return i18n::strtoupper($matches[0]);
		} else {
			return '';
		}
	}

}
