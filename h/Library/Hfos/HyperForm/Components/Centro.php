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

class CentroHyperComponent extends UserComponent {

	private static function _getNombreCentro($value){
		if($value!==null&&$value!==''){
			$centro = BackCacher::getCentro($value);
			if($centro!=false){
				return $centro->getNomCentro();
			} else {
				return 'NO EXISTE EL CENTRO';
			}
		}
		return '';
	}

	public static function build($name, $component, $value=null, $context=null){
		$nombreCentro = self::_getNombreCentro($value);
		$code = '<table cellspacing="0" cellpadding="0" class="centroCompleter">
			<tr>
				<td>'.Tag::numericField(array($name, 'size' => '12', 'maxlength' => 12, 'value' => $value)).'</td>
				<td>'.Tag::textField(array($name.'_det', 'size' => 35, 'class' => 'centroDetalle', 'value' => $nombreCentro)).'</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addCentroCompleter("'.$name.'", "'.$context.'")</script>';
		return $code;
	}

	public static function getDetail($value){
		if($value!=''){
			return $value.' / '.self::_getNombreCentro($value);
		} else {
			return '';
		}
	}

	public static function info(){
		return 'Un código de cuenta contable válido';
	}

}