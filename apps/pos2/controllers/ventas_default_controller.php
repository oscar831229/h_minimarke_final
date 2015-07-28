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

class Ventas_DefaultController extends ApplicationController
{

	public function indexAction()
	{
		$this->setTemplateAfter("admin_menu");
		$this->loadModel('TipoVenta');
	}

	public function getOpcionesAction($id)
	{
		$this->setResponse('view');
		$id = $this->filter($id, 'onechar');
		$ventaDefault = $this->VentasDefault->findFirst("tipo_venta_id='$id'");
		if($ventaDefault==false){
			Tag::displayTo('cedula', '');
			Tag::displayTo('valorMinimo', 0);
		} else {
			Tag::displayTo('cedula', $ventaDefault->getCedula());
			Tag::displayTo('valorMinimo', $ventaDefault->getValorMinimo());

			$cliente = $this->Clientes->findFirst($ventaDefault->getCedula());
			if($cliente==false){
				Tag::displayTo('nombre', 'NO EXISTE EL CLIENTE');
			} else {
				Tag::displayTo('nombre', $cliente->nombre);
			}
		}
		$this->setParamToView('tipoVenta', $id);
	}

	public function guardarAction(){
		$controllerRequest = $this->getRequestInstance();
		if($controllerRequest->isSetPostParam('tipoVenta')){
			$tipoVenta = $this->getPostParam('tipoVenta', 'onechar');
			$ventaDefault = $this->VentasDefault->findFirst("tipo_venta_id='$tipoVenta'");
			$cedula = $this->getPostParam('cedula', 'alpha', 'extraspaces');
			if($cedula==''){
				if($ventaDefault!=false){
					$ventaDefault->delete();
					Flash::success('Se actualizaron las opciones del tipo de pedido correctamente');
				}
			} else {
				$valorMinimo = $this->getPostParam('valorMinimo', 'double');
				if($ventaDefault==false){
					$ventaDefault = new VentasDefault();
					$ventaDefault->setTipoVentaId($tipoVenta);
				}
				$ventaDefault->setCedula($cedula);
				$ventaDefault->setValorMinimo($valorMinimo);
				if($ventaDefault->save()==false){
					foreach($ventaDefault->getMessages() as $message){
						Flash::error($message->getMessage());
					}
				}
				Flash::success('Se actualizaron las opciones del tipo de pedido correctamente');
			}
		}
		$this->routeToAction('index');
	}

	/**
	 * Busca un cliente en la base de datos de hotel por su nombre
	 *
	 */
	public function queryCustomersAction()
	{
		$this->setResponse('view');
		echo '<ul>';
		$controllerRequest = ControllerRequest::getInstance();
		$nombre = $controllerRequest->getParamPost('nombre', 'extraspaces');
		if ($nombre) {
			$clientes = $this->Clientes->find("nombre like '%".preg_replace("/[ ]+/", "%", $nombre)."%'", 'limit: 10', 'order: nombre');
			foreach($clientes as $cliente){
				echo "<li id='", $cliente->cedula, "'>", $cliente->nombre, "</li>\n";
			}
		}
		echo '</ul>';
	}

}