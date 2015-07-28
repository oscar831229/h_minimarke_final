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

class OrdenHyperComponent extends UserComponent {

	/**
	 * Método que permite crear un campo para consultar ordenes de compra
	 *
	 * @param	string $name
	 * @param	string $component
	 * @param	string $value
	 * @param	string $context
	 * @return	string
	 */
	public static function build($name, $component, $value=null, $context=null){
		$code = '<table cellspacing="0" cellpadding="0" class="ordenCompleter">
			<tr>
				<td>'.Tag::numericField(array($name, 'size' => '7', 'maxlength' => 15, 'value' => $value)).'</td>
				<td>
					<a href="#" class="query-orden" onclick="Tatico.queryOrden(this); return false">Consultar</a>
				</td>
			</tr>
		</table>';
		return $code;
	}

	public static function getDetail($value){
		return $value;
	}

	public static function info(){
		return 'Un número de orden de compra existente';
	}

}
