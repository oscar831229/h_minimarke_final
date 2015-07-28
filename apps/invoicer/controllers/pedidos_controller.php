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
 * PedidosController
 *
 * Controlador de los pedidos
 *
 */
class PedidosController extends ApplicationController {

	public function buscarAction(){
		$this->setResponse('view');

		$codigoAlmacen = $this->getPostParam('almacenConsulta', 'int');
		$estado = $this->getPostParam('estadoPedido', 'onechar');

		$comprob = sprintf('P%02s', $codigoAlmacen);
		$pedidos = $this->Movihead->find(array("comprob='$comprob' AND estado='$estado'", "columns" => "almacen,almacen_destino,numero,fecha,estado"));
		$this->setParamToView('pedidos', $pedidos);

		View::renderPartial('resultados');
	}

	public function consultarAction(){

		$this->setResponse('view');

		$codigoAlmacen = Settings::get('almacen_venta');
		Tag::displayTo('almacenConsulta', $codigoAlmacen);

		$fecha = new Date();
		$comprob = sprintf('P%02s', $codigoAlmacen);
		$pedidos = $this->Movihead->find(array("comprob='$comprob' AND estado='A'"));
		foreach($pedidos as $pedido){
			if(Date::isEarlier($pedido->getFVence(), $fecha)){
				$pedido->setEstado('C');
				if($pedido->save()==false){
					foreach($pedido->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}
			}
		}

		$pedidos = $this->Movihead->find(array("comprob='$comprob' AND estado='A'", "columns" => "almacen,almacen_destino,numero,fecha,estado"));
		$this->setParamToView('pedidos', $pedidos);

		$this->setParamToView('almacenes', $this->Almacenes->find("estado='A'"));
		$this->setParamToView('estados', array(
			'A' => 'ABIERTO',
			'C' => 'CERRADO'
		));
	}

}
