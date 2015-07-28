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

class SocioHyperComponent extends UserComponent {

	private static function _getNombreSocio($value){
		if($value!==null&&$value!==''){
			$socio = EntityManager::get('Socios')->findFirst($value);
			if($socio!=false){
				return $socio->getNumeroAccion()." - ".$socio->getNombres()." ".$socio->getApellidos();
			} else {
				return 'NO EXISTE EL SOCIO';
			}
		}
		return '';
	}

	public static function build($name, $component, $value=null, $context=null){
		$nombreSocio = self::_getNombreSocio($value);
		$code = '<table cellspacing="0" cellpadding="0" class="socioCompleter">
			<tr>
				<td>'.Tag::numericField(array($name, 'size' => '12', 'maxlength' => 12, 'value' => $value)).'</td>
				<td>'.Tag::textField(array($name.'_det', 'size' => 35, 'class' => 'socioDetalle', 'value' => $nombreSocio)).'</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addSocioCompleter("'.$name.'", "'.$context.'")</script>';
		return $code;
	}

	public static function getDetail($value){
		if($value!=''){
			return $value.' / '.self::_getNombreSocio($value);
		} else {
			return '';
		}
	}

	public static function info(){
		return 'Un código de socio válido';
	} 

}