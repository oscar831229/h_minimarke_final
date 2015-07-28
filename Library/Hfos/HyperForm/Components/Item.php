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

class ItemHyperComponent extends UserComponent
{

    /**
     * @param $value
     * @return string
     */
    private static function _getNombreItem($value)
    {
		if ($value!==null && $value!=='') {
			$inve = BackCacher::getInve($value);
			if ($inve != false) {
				return $inve->getDescripcion();
			} else {
				return 'NO EXISTE EL ITEM';
			}
		}
		return '';
	}

	/**
	 * Método que crea un campo autocomplete de referencias
	 *
	 * @param string $name
	 * @param string $component
	 * @param string $value
	 * @param string $context
	 * @return string
	 */
	public static function build($name, $component, $value=null, $context=null)
    {
		$nombreItem = self::_getNombreItem($value);
		$code = '<table cellspacing="0" cellpadding="0" class="itemCompleter">
			<tr>
				<td>' . Tag::textField(array($name, 'size' => '7', 'maxlength' => 15, 'value' => $value)).'</td>
				<td>
					' . Tag::textField(array($name.'_det', 'size' => 30, 'class' => 'itemDetalle', 'value' => $nombreItem)).'
				</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addItemCompleter("'.$name.'", "'.$context.'")</script>';
		return $code;
	}

    /**
     * @param $value
     * @return string
     */
    public static function getDetail($value)
    {
		if ($value!='') {
			return $value.' / '.self::_getNombreItem($value);
		}
		return '';
	}

    /**
     * @return string
     */
    public static function info()
    {
		return 'El documento de un item válido';
	}
}
