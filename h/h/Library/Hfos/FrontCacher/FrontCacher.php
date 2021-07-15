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
 * FrontCacher
 *
 * Cachea en memoria registros consultados durante un proceso largo
 * de tal forma que solo se consulten una vez en la base de datos
 *
 */
class FrontCacher extends UserComponent {

	/**
	 * Cargos cacheados
	 *
	 * @var array
	 */
	private static $_cargos = array();

	/**
	 * Obtiene un cargo del BackCacher
	 *
	 * @param	string $codigoCargo
	 * @return	Cargos
	 */
	public static function getCargo($codigoCargo){
		if(!isset(self::$_cargos[$codigoCargo])){
			self::$_cargos[$codigoCargo] = self::getModel('Cargos')->findFirst("codcar='$codigoCargo'");
		}
		return self::$_cargos[$codigoCargo];
	}

}