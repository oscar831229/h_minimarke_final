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

class TerceroHyperComponent extends UserComponent {

	private static function _getNombreTercero($value){
		if($value!==null&&$value!==''){
			$nit = BackCacher::getTercero($value);
			if($nit!=false){
				return $nit->getNombre();
			} else {
				return 'NO EXISTE EL TERCERO';
			}
		}
		return '';
	}

	public static function build($name, $component, $value=null, $context=null, $create=false){
		$nombreTercero = self::_getNombreTercero($value);
		$code = '<table cellspacing="0" cellpadding="0" class="terceroCompleter">
			<tr>
				<td>'.Tag::textField(array($name, 'size' => '12', 'maxlength' => 20, 'value' => $value)).'</td>
				<td>
					'.Tag::textField(array($name.'_det', 'size' => 35, 'class' => 'terceroDetalle', 'value' => $nombreTercero)).'
				</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addTerceroCompleter("'.$name.'", "'.$create.'", "'.$context.'")</script>';
		return $code;
	}

	public static function getDetail($value){
		if($value!=''){
			$nombreTercero = self::_getNombreTercero($value);
			if(i18n::strlen($nombreTercero)>40){
				$nombreTercero = i18n::substr($nombreTercero, 0, 40);
			}
			return $value.' / '.$nombreTercero;
		} else {
			return '';
		}
	}

	public static function info(){
		return 'El documento de un tercero válido';
	}

}
