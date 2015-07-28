<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

class Empresas extends ActiveRecord {

	/**
	 * Metodo inicializador de la Entidad
	 */
	protected function initialize(){
		$config = CoreConfig::readFromActiveApplication('app.ini', 'ini');
		if(isset($config->pos->hotel)){
			$this->setSchema($config->pos->hotel);
		} else {
			$this->setSchema('hotel2');
		}
	}

}
