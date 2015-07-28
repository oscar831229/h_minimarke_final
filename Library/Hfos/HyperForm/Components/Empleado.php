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

class EmpleadoHyperComponent extends UserComponent {

	private static function _getNombreEmpleado($value){
		if($value!==null&&$value!==''){
			$emplado = BackCacher::getEmpleado($value);
			if($emplado!=false){
				return $emplado->getNombre();
			} else {
				return 'NO EXISTE EL EMPLEADO';
			}
		}
		return '';
	}

	public static function build($name, $component, $value=null, $context=null, $create=false){
		$nombreTercero = self::_getNombreTercero($value);
		$code = '<table cellspacing="0" cellpadding="0" class="empleadoCompleter">
			<tr>
				<td>'.Tag::textField(array($name, 'size' => '12', 'maxlength' => 20, 'value' => $value)).'</td>
				<td>
					'.Tag::textField(array($name.'_det', 'size' => 35, 'class' => 'empleadoDetalle', 'value' => $nombreTercero)).'
				</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addEmpleadoCompleter("'.$name.'", "'.$create.'", "'.$context.'")</script>';
		return $code;
	}

	public static function getDetail($value){
		if($value!=''){
			return $value.' / '.self::_getNombreEmpleado($value);
		} else {
			return '';
		}
	}

	public static function info(){
		return 'El documento de un empleado válido';
	}

}
