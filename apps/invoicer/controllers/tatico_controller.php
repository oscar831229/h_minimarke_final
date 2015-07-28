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

/**
 * TaticoController
 *
 * Interface externa para acceder al componente Tatico
 *
 */
class TaticoController extends ApplicationController {

	/**
	 * Obtiene los datos de un pedido
	 *
	 * @return array
	 */
	public function getPedidoAction()
	{
		$this->setResponse('json');
		
		$nit 			= $this->getQueryParam('nit');
		$contrato 		= $this->getQueryParam('numeroPedido');
		$numeroPedido 	= $this->getQueryParam('numeroPedido','int');
		$codigoAlmacen 	= Settings::get('almacen_venta');
		
		$taticoPedido = Tatico::getPedido($codigoAlmacen, $numeroPedido);
		if ($numeroPedido && !$contrato) {
			return $taticoPedido;
		} else {
			Core::importFromLibrary('Hfos/Invoicing/Adapter','IN.php');
			return InvoicingIN::getCheckPedidoInvoicer($taticoPedido, $contrato, $nit);
		}
	}
}
