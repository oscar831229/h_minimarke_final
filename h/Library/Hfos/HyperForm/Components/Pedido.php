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

class PedidoHyperComponent extends UserComponent {

	/**
	 * Método que permite consultar pedidos a almacenes
	 *
	 * @param string $name
	 * @param string $component
	 * @param string $value
	 * @param string $context
	 * @return string
	 */
	public static function build($name, $component, $value=null, $context=null){
		$code = '<table cellspacing="0" cellpadding="0" class="pedidoCompleter">
			<tr>
				<td>'.Tag::numericField(array($name, 'size' => '7', 'maxlength' => 15, 'value' => $value)).'</td>
				<td>
					<a href="#" class="query-pedido" onclick="Tatico.queryPedido(this); return false">Consultar</a>
				</td>
			</tr>
		</table>';
		return $code;
	}

	public static function getDetail($value){
		return $value;
	}

	public static function info(){
		return 'Un número de pedido existente';
	}

}
