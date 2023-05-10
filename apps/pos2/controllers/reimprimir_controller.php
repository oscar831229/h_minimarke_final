<?php

/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Point Of Sale
 * @copyright 	BH-TECK Inc. 2009-2014
 * @version		$Id$
 */


class ReimprimirController extends ApplicationController {

	public function indexAction(){
		$this->loadModel('Salon');
		$datos = $this->Datos->findFirst();
		$this->setParamToView('facturas', $this->Factura->find("fecha='{$datos->getFecha()}' AND estado='A'"));
	}

	public function reprintAction($numero, $salon, $tipo, $prefijo){

		$this->setResponse('view');
		$salon = $this->filter($salon, 'int');
		$numero = $this->filter($numero, 'int');
		$tipo_venta = $this->filter($tipo, 'onechar');
		$prefijo = $this->filter($prefijo, 'alpha');

		if($tipo=='O'||$tipo=='F'){
			$conditions = '';
			if($tipo=='O'){
				$conditions = "tipo_venta IN ('H', 'P', 'U', 'C', 'S')";
			} else {
				if($tipo=='F'){
					$conditions = "tipo_venta = 'F' AND prefijo_facturacion = '$prefijo'";
				}
			}
			$conditions = "consecutivo_facturacion = '$numero' AND salon_id = '$salon' AND $conditions";
		} else {
			$conditions = "comanda = '$numero' AND salon_id = '$salon'";
		}

		$factura = $this->Factura->findFirst(array($conditions, 'columns' => 'account_master_id,cuenta'));
		if($factura!=false){
			$this->redirect('factura/index/'.$factura->cuenta.'/'.$factura->account_master_id.'?reprint');
		} else {
			Flash::error('No existe la orden/factura');
		}
	}

	public function existsAction($id, $salon, $tipo_venta, $prefijo){
		$this->setResponse('xml');
		$salon = $this->filter($salon, 'int');
		$id = $this->filter($id, 'int');
		$tipo_venta = $this->filter($tipo_venta, 'onechar');
		$prefijo = $this->filter($prefijo, 'alpha');
		if($tipo_venta=='O'){
			$tipo_venta = "tipo_venta IN ('H', 'P', 'U', 'C', 'S')";
			$conditions = "consecutivo_facturacion = '$id' AND salon_id = '$salon' AND $tipo_venta";
		} else {
			$tipo_venta = "tipo_venta = 'F'";
			$conditions = "consecutivo_facturacion = '$id' AND salon_id = '$salon' AND prefijo_facturacion = '$prefijo' AND $tipo_venta";
		}

		$existe = $this->Factura->count($conditions);
		if($existe){
			return "yes";
		} else {
			return "no";
		}
	}

}