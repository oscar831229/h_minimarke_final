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

class ComprobHyperComponent extends UserComponent {

	private static function _getNombreComprob($value){
		if($value!==null&&$value!==''){
			$comprob = BackCacher::getComprob($value);
			if($comprob!=false){
				return $comprob->getNomComprob();
			} else {
				return 'NO EXISTE EL COMPROBANTE';
			}
		}
		return '';
	}

	public static function build($name, $component, $value=null, $context=null){
		$nombreComprob = self::_getNombreComprob($value);
		$code = '<table cellspacing="0" cellpadding="0" class="comprobCompleter">
			<tr>
				<td>'.Tag::textField(array($name, 'size' => '12', 'maxlength' => 12, 'value' => $value)).'</td>
				<td>'.Tag::textField(array($name.'_det', 'size' => 35, 'class' => 'comprobDetalle', 'value' => $nombreComprob)).'</td>
			</tr>
		</table>
		<script type="text/javascript">HfosCommon.addComprobCompleter("'.$name.'", "'.$context.'")</script>';
		return $code;
	}

	public static function getDetail($value){
		if($value!=''){
			return $value.' / '.self::_getNombreComprob($value);
		} else {
			return '';
		}
	}

	public static function info(){
		return 'Un código de comprobante válido';
	}

}