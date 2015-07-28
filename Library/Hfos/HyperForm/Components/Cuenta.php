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

class CuentaHyperComponent extends UserComponent {

	private static function _getNombreCuenta($value){
		if($value!==null&&$value!==''){
			$cuenta = BackCacher::getCuenta($value);
			if($cuenta!=false){
				return $cuenta->getNombre();
			} else {
				return 'NO EXISTE LA CUENTA';
			}
		}
		return '';
	}

	public static function build($name, $component, $value=null, $context=null){
		$nombreCuenta = self::_getNombreCuenta($value);
		$code = '<table cellspacing="0" cellpadding="0" class="cuentaCompleter">
			<tr>
				<td>'.Tag::numericField(array($name, 'size' => '12', 'maxlength' => 12, 'value' => $value)).'</td>
				<td>'.Tag::textField(array($name.'_det', 'size' => 35, 'class' => 'cuentaDetalle', 'value' => $nombreCuenta)).'</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addCuentaCompleter("'.$name.'", "'.$context.'")</script>';
		return $code;
	}

	public static function getDetail($value)
	{
		if ($value) {
			return $value . ' / ' . self::_getNombreCuenta($value);
		} else {
			return '';
		}
	}

	public static function info(){
		return 'Un código de cuenta contable válido';
	}

}