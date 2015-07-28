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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

class ReportsController extends ApplicationController {

	public $fecha_final;
	public $fecha_inicial;
	public $salon_id;
	public $centro_costo;
	public $comandaInicial;
	public $comandaFinal;
	public $tipoItem;
	public $usuarioId;

	public function initialize(){
		$this->setPersistance(true);
		$this->loadModel('Datos', 'Salon', 'UsuariosPos');
	}

	public function indexAction(){

	}

	public function huespedesAction(){
		$this->setResponse('view');
	}

	public function ventaPlatoAction($salonId, $tipoItem, $fechaInicial='', $fechaFinal=''){
		$this->setResponse('view');
		$this->loadModel('AccountMaster', 'Account', 'MenusItems');
		$this->tipoItem = $tipoItem;
		$this->fecha_inicial = $this->filter($fechaInicial, "date");
		$this->fecha_final = $this->filter($fechaFinal, "date");
		$this->salon_id = $this->filter($salonId, 'int');
	}

	public function cortesiasAction($salonId, $fechaInicial='', $fechaFinal=''){
		$this->setResponse('view');
		$this->loadModel('Factura', 'TipoVenta');
		$this->fecha_inicial = $this->filter($fechaInicial, "date");
		$this->fecha_final = $this->filter($fechaFinal, "date");
		$this->salon_id = $this->filter($salonId, 'int');
	}

	public function ventaPlatoHtmlAction($salonId, $tipoItem, $fechaInicial='', $fechaFinal=''){
		$this->setResponse('view');
		$this->tipoItem = substr($tipoItem, 0, 1);
		$this->fecha_inicial = $this->filter($fechaInicial, 'date');
		$this->fecha_final = $this->filter($fechaFinal, 'date');
		$this->salon_id = $this->filter($salonId, 'int');
	}

	public function venta_plato_cajeroAction($salon_id, $fecha_inicial, $fecha_final){
		$this->setResponse('view');
		$this->loadModel('AccountMaster', 'Account', 'MenusItems');
		$this->fecha_inicial = $this->filter($fecha_inicial, 'date');
		$this->fecha_final = $this->filter($fecha_final, 'date');
		$this->salon_id = $this->filter($salon_id, 'int');
	}

	public function cuadre_cajaAction($salonId, $fecha_inicial, $fecha_final){
		$this->setResponse('view');
		$this->loadModel('AccountCuentas', 'Factura', 'Habitacion', 'TipoVenta', 'PagosFactura');
		$this->fecha_inicial = $this->filter($fecha_inicial, 'date');
		$this->fecha_final = $this->filter($fecha_final, 'date');
		$this->salon_id = $this->filter($salonId, 'int');
	}

	public function cuadre_caja_todosAction($salonId, $fechaInicial, $fechaFinal){
		$this->setResponse('view');
		$this->loadModel('AccountCuentas', 'Factura', 'Habitacion', 'TipoVenta', 'HabitacionHistorico', 'PagosFactura');
		$this->fecha_inicial = $this->filter($fechaInicial, 'date');
		$this->fecha_final = $this->filter($fechaFinal, 'date');
		$this->salon_id = $this->filter($salonId, 'int');
	}

	public function mas_vendidosAction($salonId, $fechaInicial, $fechaFinal){
		$this->setResponse('view');
		$this->fecha_inicial = $this->filter($fechaInicial, 'date');
		$this->fecha_final = $this->filter($fechaFinal, 'date');
		$this->salon_id = $this->filter($salonId, 'int');
	}

	public function menos_vendidosAction($salonId, $fechaInicial, $fechaFinal){
		$this->setResponse('view');
		$this->fecha_inicial = $this->filter($fechaInicial, 'date');
		$this->fecha_final = $this->filter($fechaFinal, 'date');
		$this->salon_id = $this->filter($salonId, 'int');
	}

	public function mayor_utilidadAction($salonId, $fechaInicial, $fechaFinal){
		$this->setResponse('view');
		$this->fecha_inicial = $this->filter($fechaInicial, 'date');
		$this->fecha_final = $this->filter($fechaFinal, 'date');
		$this->salon_id = $this->filter($salonId, 'int');
	}

	public function menor_utilidadAction($salonId, $fechaInicial, $fechaFinal){
		$this->setResponse('view');
		$this->fecha_inicial = $this->filter($fechaInicial, 'date');
		$this->fecha_final = $this->filter($fechaFinal, 'date');
		$this->salon_id = $this->filter($salonId, 'int');
	}

	public function facturasHtmlAction($salonId, $fechaInicial, $fechaFinal){
		$this->setResponse('view');
		$this->fecha_inicial = $this->filter($fechaInicial, 'date');
		$this->fecha_final = $this->filter($fechaFinal, 'date');
		$this->salon_id = $this->filter($salonId, 'int');
	}

	public function cuadreCajaTodosHtmlAction($salonId, $tipoItem, $fechaInicial='', $fechaFinal=''){
		$this->setResponse('view');
		$this->tipoItem = $this->filter($tipoItem, 'onechar');
		$this->fecha_inicial = $this->filter($fechaInicial, 'date');
		$this->fecha_final = $this->filter($fechaFinal, 'date');
		$this->salon_id = $this->filter($salonId, 'int');
		$this->loadModel('AccountMaster', 'Account', 'MenusItems', 'Factura', 'FormasPago');
	}

	public function saldosInventariosAction($salonId, $tipoItem, $fechaInicial='', $fechaFinal=''){
		$this->setResponse('view');
		$this->tipoItem = $this->filter($tipoItem, 'onechar');
		$this->fecha_inicial = $this->filter($fechaInicial, 'date');
		$this->fecha_final = $this->filter($fechaFinal, 'date');
		$this->salon_id = $this->filter($salonId, 'int');

		$interpos3 = new InterfasePOS3(false);
		$interpos3->setVerbose(true);
		$descargue = $interpos3->getItemsToDownload($this->fecha_inicial);

		foreach($descargue as $usuarioId => $usuarioItems){
			foreach($usuarioItems as $numeroAlmacen => $items){
				foreach($items as $codigo => $item){
					$descargue[$usuarioId][$numeroAlmacen][$codigo]['otrosCantidad'] = 0;
					$descargue[$usuarioId][$numeroAlmacen][$codigo]['otrosCantidadU'] = 0;
				}
			}
			foreach($descargue as $usuarioOtherId => $usuarioOtherItems){
				if($usuarioOtherId!=$usuarioId){
					foreach($usuarioOtherItems as $numeroAlmacen => $items){
						foreach($items as $codigo => $item){
							if(isset($descargue[$usuarioId][$numeroAlmacen][$codigo])){
								$descargue[$usuarioId][$numeroAlmacen][$codigo]['otrosCantidad']+= $item['cantidad'];
								$descargue[$usuarioId][$numeroAlmacen][$codigo]['otrosCantidadU']+= $item['cantidadu'];
							}
						}
					}
				}
			}
		}

		$this->loadModel('UsuariosPos');

		$this->setParamToView('descargue', $descargue);
	}

	public function printOrderByNumberAction($num, $salon, $tipo=''){
		$this->setResponse('view');
		$num = (int) $num;
		if($tipo=='O'){
			$tipo_venta = "('P', 'H', 'C', 'U')";
		} else {
			$tipo_venta = "('F')";
		}
		$factura = $this->Factura->findFirst("consecutivo_facturacion='$num' AND salon_id='$salon' AND tipo_venta in $tipo_venta");
		if($factura){
			if($this->AccountCuentas->findFirst("numero='$num' AND account_master_id='{$factura->account_master_id}'")){
				echo "<script type='text/javascript'>";
				echo "window.location = '".Utils::getKumbiaURL("prefactura/index/{$this->AccountCuentas->account_master_id}:{$this->AccountCuentas->cuenta}?documento=".$this->AccountCuentas->clientes_cedula."&rp=1")."'";
				echo "</script>";
			} else {
				if($factura->tipo_venta=='P'||$factura->tipo_venta=='H'){
					Flash::error("El número de orden de servicio $num no existe (1)");
				} else {
					Flash::error("El número de factura $num no existe (1)");
				}
			}
		} else {
			Flash::error("El número de orden de servicio $num no existe (2)");
		}
	}

	/**
	 * Reporte de Comandas
	 *
	 * @param string $salonId
	 * @param integer $comandaInicial
	 * @param integer $comandaFinal
	 */
	public function recordedMovementAction($salonId, $fechaInicial='', $fechaFinal='', $comandaInicial='', $comandaFinal='', $usuarioId=0){
		$this->fecha_inicial = $this->filter($fechaInicial, 'date');
		$this->fecha_final = $this->filter($fechaFinal, 'date');
		$comandaInicial = $this->filter($comandaInicial, "int");
		$comandaFinal = $this->filter($comandaFinal, "int");
		$usuarioId = $this->filter($usuarioId, "int");
		if($comandaFinal<$comandaInicial){
			Flash::error("El numero de comanda inicial debe ser menor al final");
			$this->routeTo("action: index");
		} else {
			$this->salon_id = $salonId;
			$this->comandaInicial = $comandaInicial;
			$this->comandaFinal = $comandaFinal;
			$this->usuarioId = $usuarioId;
			$this->setResponse("view");
			$this->loadModel('HabitacionHistorico');
		}
	}

}

