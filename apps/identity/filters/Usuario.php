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

class UsuarioFilter
//implements FilterInterface
{

	/**
 	 * Ejecuta el filtro
 	 *
 	 * @param	string $str
 	 * @return	string
 	 */
	public function execute($str){
		return i18n::strtolower(preg_replace('/[^a-zA-Z0-9\.]/', '', $str));
	}

}
