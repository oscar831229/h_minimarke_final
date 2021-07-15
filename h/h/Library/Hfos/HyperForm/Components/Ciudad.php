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

class CiudadHyperComponent extends UserComponent
{

	private static $_cache = array();

	public static function build($name, $component, $value=null, $context=null)
	{
		$nombreCiudad = '';
		if($value!==null){
			$value = (int) $value;
			if ($value >= 0) {
				if (!isset(self::$_cache[$value])) {
					$location = EntityManager::getEntityInstance('Location')->findFirst($value);
					self::$_cache[$value] = $location;
				} else {
					$location = self::$_cache[$value];
				}
				if ($location != false) {
					$nombreCiudad = $location->getName();
				}
			}
		}
		$code = Tag::hiddenField(array($name, 'value' => $value)).' '.Tag::textField(array($name.'_det', 'size' => 45, 'value' => $nombreCiudad));
		$code .= '<div id="'.$name.'_choices" class="autocomplete"></div>';
		$code .= '<script type="text/javascript">HfosCommon.addLocationCompleter("'.$name.'", "'.$context.'")</script>';
		return $code;
	}

	public static function getDetail($value)
	{
		if ($value !== null) {
			$value = (int) $value;
			if (!isset(self::$_cache[$value])) {
				$location = EntityManager::getEntityInstance('Location')->findFirst($value);
				self::$_cache[$value] = $location;
			} else {
				$location = self::$_cache[$value];
			}
			if ($location->getId() > 0) {
				$territory = $location->getTerritory();
				return '<img src="' . Core::getInstancePath() . 'img/flags/' . $territory->getIso3166() . '.gif">&nbsp;' . $location->getName().' / '.$location->getZone()->getName().' / '.$territory->getName();
			} else {
				return $location->getName();
			}
		} else {
			return '';
		}
	}

	public static function info()
	{
		return 'Un código de ciudad válido';
	}

}