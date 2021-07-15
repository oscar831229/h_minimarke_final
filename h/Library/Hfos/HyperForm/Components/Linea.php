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

class LineaHyperComponent extends UserComponent {

	private static function _getNombreLinea($value){
		if($value!==null&&$value!==''){
			$linea = BackCacher::getLinea($value);
			if($linea!=false){
				return $linea->getDescripcion();
			} else {
				return 'NO EXISTE EL LINEA';
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
	public static function build($name, $component, $value=null, $context=null){
		$nombreLinea = self::_getNombreLinea($value);
		$code = '<table cellspacing="0" cellpadding="0" class="lineaCompleter">
			<tr>
				<td>'.Tag::numericField(array($name, 'size' => '7', 'maxlength' => 15, 'value' => $value)).'</td>
				<td>
					'.Tag::textField(array($name.'_det', 'size' => 30, 'class' => 'lineaDetalle', 'value' => $nombreLinea)).'
				</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addLineaCompleter("'.$name.'", "'.$context.'")</script>';
		return $code;
	}

	public static function getDetail($value){
		if($value!=''){
			return $value.' / '.self::_getNombreLinea($value);
		} else {
			return '';
		}
	}

	public static function info(){
		return 'Una código de línea de producto válido';
	}

}
