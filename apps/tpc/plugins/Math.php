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

/**
 * MathPlugin
 *
 * Inicializa el LocaleMath e i18n antes de cada petición
 */
class MathPlugin extends ControllerPlugin {

	public function beforeStartRequest(){
		i18n::isUnicodeEnabled();
		LocaleMath::enableBcMath();
	}

}