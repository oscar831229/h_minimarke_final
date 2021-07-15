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
 * @author 		BH-TECK Inc. 2009-2011
 * @version		$Id$
 */

require_once 'InvoicingException.php';

/**
 * Social
 *
 * Class of Invoicing to all aplication of Hfos
 *
 */
abstract class Invoicing extends UserComponent
{

	public static function factory($adapterName)
	{
		$basePath = 'Library/Hfos/Invoicing/';
		if (class_exists($adapterName) == false) {
			$className = 'Invoicing' . $adapterName;
			$filePath = $basePath . 'Adapter/' . $adapterName . '.php';
			if (file_exists($filePath) == true) {
				require_once $filePath;
			} else {
				throw new InvoicingException('The adapter "' . $filePath . '" not exists');
			}
		}
		if (class_exists($className) == true) {
			return new $className();
		} else {
			throw new InvoicingException('The adapter class ' . $className . ' not exists');
		}
	}
}
