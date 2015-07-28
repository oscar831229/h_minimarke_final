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
 * @copyright 	BH-TECK Inc. 2009-2012
 * @version		$Id$
 */

/**
 * TaticoController
 *
 * Interface externa para acceder al componente Tatico
 *
 */
class TaticoController extends WebServiceController
{

	/**
	 * Consulta un código y devuelve los datos de la referencia ó el contenido de una receta
	 *
	 * @return array
	 */
	public function getReferenciaOrRecetaAction()
	{
		$codigoItem = $this->getQueryParam('codigoItem', 'alpha');
		$almacen = $this->getQueryParam('almacen', 'int');
        $tipoDetalle = $this->getQueryParam('tipoDetalle', 'alpha');
		return Tatico::getReferenciaOrReceta($codigoItem, $almacen, $tipoDetalle);
	}

	/**
	 * Obtiene la información de una referencia
	 *
	 * @return array
	 */
	public function getReferenciaAction()
	{
		$codigoItem = $this->getQueryParam('codigoItem', 'alpha');
		$almacen = $this->getQueryParam('almacen', 'int');
		return Tatico::getReferencia($codigoItem, $almacen);
	}

	/**
	 * Obtiene el saldo de una referencia
	 *
	 * @return array
	 */
	public function getSaldoReferenciaAction()
	{
		$codigoItem = $this->getQueryParam('codigoItem', 'alpha');
		$almacen = $this->getQueryParam('almacen', 'int');
		return Tatico::getSaldoReferencia($codigoItem, $almacen);
	}

	/**
	 * Obtiene los datos de una orden de compra
	 *
	 * @return array
	 */
	public function getOrdenDeCompraAction()
	{
		$orden = $this->getQueryParam('nPedido', 'int');
		$almacen = $this->getQueryParam('nAlmacen', 'int');
		return Tatico::getOrdenDeCompra($almacen, $orden);
	}

	/**
	 * Obtiene los datos de un pedido
	 *
	 * @return array
	 */
	public function getPedidoAction()
	{
		$pedido = $this->getQueryParam('nPedido','int');
		$almacen = $this->getQueryParam('nAlmacen', 'int');
		return Tatico::getPedido($almacen, $pedido);
	}

	/**
	 * Consulta referencias por su codigo
	 *
	 * @return array
	 */
	public function queryItemByDescriptionAction()
	{
		$response = array();
		$descripcion = $this->getPostParam('descripcion', 'extraspaces');
		if ($descripcion != '') {
			$inve = $this->Inve->find('descripcion LIKE \''.$descripcion.'%\'', 'order: descripcion', 'limit: 13');
			foreach ($inve as $inve) {
				$response[] = array(
					'value' => $inve->getItem(),
					'selectText' => $inve->getDescripcion(),
					'optionText' => $inve->getItem().' - '.$inve->getDescripcion()
				);
			}
		}
		return $response;
	}

	/**
	 * Consulta nits por su razon social o nombre
	 *
	 * @return array
	 */
	public function queryNitByRazsocAction()
	{
		$response = array();
		$razsoc = $this->getPostParam('razsoc', 'extraspaces');
		if($razsoc!=''){
			$nits = $this->Nits->find('nombre LIKE \'%'.$razsoc.'%\'', 'order: nombre', 'limit: 13');
			foreach($nits as $nit){
				$response[] = array(
					'value' => $nit->getNit(),
					'selectText' => $nit->getNombre(),
					'optionText' => $nit->getNit().' - '.$nit->getNombre(),
					'ica' => (float)$nit->getApAereo(),
					'usuarioTipo' => 'C',
					'nitTipo' => $nit->getEstadoNit(),
					'nitAutoRet' => $nit->getAutoret()
				);
			}
		}
		return $response;
	}

	/**
	 * Obtiene la razón social de un tercero a partir de su nombre
	 *
	 * @return string
	 */
	public function getRazsocByNitAction(){
		$nit = $this->getPostParam('nit', 'alpha');
		return Tatico::getRazsocByNit($nit);
	}

	/**
	 * Obtiene una lista de los items para el calculo
	 *
	 * @return array
	 */
	private function _getItemList()
	{
		$items = json_decode(str_replace("\\", "", $this->getPostParam('items')));
		$iva = json_decode(str_replace("\\", "", $this->getPostParam('iva')));
		$valor = json_decode(str_replace("\\", "", $this->getPostParam('valor')));

		$itemsList = array();
		$numberItems = count($items);
		for ($i = 0; $i < $numberItems; $i++) {
			$itemsList[] = array(
				'Item' => $this->filter($items[$i], 'alpha'),
				'Iva' => $this->filter($iva[$i], 'int'),
				'Valor' => $this->filter($valor[$i], 'double')
			);
		}
		return $itemsList;
	}

	/**
	 * Calcula los impuestos de una entrada con respecto al proveedor y almacen
	 *
	 * @return array
	 */
	public function getTaxesAction()
	{
		$tipo = $this->getPostParam('tipo', 'onechar');
		$nit = $this->getPostParam('nit', 'terceros');
		$almacen = $this->getPostParam('almacen', 'int');

		$itemsList = $this->_getItemList();
		return Tatico::getTaxes($itemsList, $tipo, $almacen, $nit);
	}

	/**
	 * Imprime el detalle del calculo de los impuestos de la entrada
	 *
	 */
	public function getDetailedCalculationAction()
	{

		$tipo = $this->getPostParam('tipo', 'onechar');
		$nit = $this->getPostParam('nit', 'terceros');
		$almacen = $this->getPostParam('almacen', 'int');

		$itemsList = $this->_getItemList();
		Tatico::setTaxesDebug(true);
		$response = Tatico::getTaxes($itemsList, $tipo, $almacen, $nit);
		if($response['status']=='FAILED'){
			$response['message'] = 'No se pudo obtener el detalle del calculo: '.$response['message'];
		}

		return $response;
	}

	/**
	 * Metodo que calcula las transformaciones por action
	 *
	 * @return array
	 */
	public function getCalcularTransformacionAction()
	{
	    $items 				= json_decode(str_replace("\\", "", $this->getPostParam('items')));
	  	$cantidades 		= json_decode(str_replace("\\", "", $this->getPostParam('cantidades')));
	  	$valorTotal	 		= $this->getPostParam('valorBase', 'float');
		$itemBase 			= $this->getPostParam('itemBase', 'alpha');
		$cantidad_objetivo 	= $this->getPostParam('cantidad_objetivo', 'double');
		$nota 				= $this->getPostParam('nota');

		if (empty($itemBase)) {
			return array(
				'status'  => 'FAILED',
				'message' => 'Debe ingresar items base'
			);
		}

		if (!is_array($items)) {
			return array(
				'status'  => 'FAILED',
				'message' => 'Debe ingresar items en el detalle'
			);
		}

		if (!$cantidad_objetivo) {
			return array(
				'status'  => 'FAILED',
				'message' => 'Debe ingresar una cantidad al item base'
			);
		}

		if (!is_array($cantidades)) {
			return array(
				'status'  => 'FAILED',
				'message' => 'Debe ingresar cantidades al item en el detalle'
			);
		}

		if (!$valorTotal) {
			return array(
				'status'  => 'FAILED',
				'message' => 'La referencia base no tiene existencias suficientes en almacén'
			);
		}

		try {
			$calculos = Tatico::getCalcularTransformacion(array(
				'itemBase' 			=> $itemBase,
				'cantidad_objetivo' => $cantidad_objetivo,
				'items' 			=> $items,
				'cantidades' 		=> $cantidades,
				'valorTotal' 		=> $valorTotal,
				'nota' 				=> $nota
			));
		} catch(TaticoException $e) {
			return array(
				'status'  => 'FAILED',
				'message' => $e->getMessage()
			);
		}
		return array(
			'status'  => 'OK',
			'message' => 'Se calculó los nuevos datos de transformación',
			'datos'   => $calculos
		);
	}

	/**
	 * Recalcula los saldos de todas las referencias en todos los almacenes
	 */
	public function recalculateBalancesAction()
	{
		try {
			$transaction = TransactionManager::getUserTransaction();
			set_time_limit(0);
			TaticoKardex::setFastRecalculate(true);
			foreach($this->Almacenes->find() as $almacen){
				foreach($this->Inve->find() as $inve){
					TaticoKardex::show($inve->getItem(), $almacen->getCodigo(), '1999-01-01');
				}
			}
			$transaction->commit();
		}
		catch(TransactionFailed $e){
			Flash::error($e->getMessage());
		}
	}

	/**
	 * Recalcula los totales de movihead
	 */
	public function recalculateTotalsAction()
	{
		set_time_limit(0);
		#ActiveRecord::disableEvents(true);
		$empresa = $this->Empresa->findFirst();
		$fCierrei = $empresa->getFCierrei();
		foreach ($this->Movihead->find() as $movihead){
			$totalNeto = 0;
			$conditions = "comprob='{$movihead->getComprob()}' AND numero='{$movihead->getNumero()}' AND almacen='{$movihead->getAlmacen()}' AND almacen=almacen_destino";
			foreach($this->Movilin->find($conditions) as $movilin){
				if($movilin->getCantidad()<0){
					$totalNeto-=$movilin->getValor();
				} else {
					$totalNeto+=$movilin->getValor();
				}
			}
			if ($movihead->getEstado()==''||$movihead->getEstado()=='A'){
				if(Date::isEarlier($movihead->getFecha(), $fCierrei)){
					$movihead->setEstado('C');
				} else {
					$movihead->setEstado('A');
				}
			}
			$movihead->setTotalNeto($totalNeto);
			if ($movihead->save()==false){
				foreach($movihead->getMessages() as $message){
					Flash::error($message->getMessage());
				}
			}
		}
	}

	/**
	 * Construye los stocks del sistema apartir del historico de saldos
	 *
	 */
	public function buildStocksAction()
	{
		set_time_limit(0);
		foreach ($this->Almacenes->find() as $almacen) {
			foreach ($this->Inve->find() as $inve) {
				$inveStock = $this->InveStocks->findFirst("almacen='{$almacen->getCodigo()}' AND item='{$inve->getItem()}'");
				if ($inveStock == false) {
					$saldoPromedio = Tatico::getSaldoPromedio($inve->getItem(), $almacen->getCodigo());
					if ($saldoPromedio > 0) {
						$minimo = $saldoPromedio / 3;
						$maximo = $saldoPromedio + $minimo;
						$inveStock = new InveStocks();
						$inveStock->setAlmacen($almacen->getCodigo());
						$inveStock->setItem($inve->getItem());
						$inveStock->setMinimo($minimo);
						$inveStock->setMaximo($maximo);
						if ($inveStock->save() == false) {
							foreach ($inveStock->getMessages() as $message) {
								Flash::error($message->getMessage());
							}
						}
					}
				}
			}
		}
	}

	public function arreglaTrasladosAction()
	{
		$data = array();
		try {

			$transaction = TransactionManager::getUserTransaction();
			$this->Movihead->setTransaction($transaction);
			$this->Movilin->setTransaction($transaction);
			foreach ($this->Movihead->find("comprob LIKE 'T%' AND fecha >='2012-02-01'") as $movihead) {

				$conditions = "comprob='{$movihead->getComprob()}' AND almacen='{$movihead->getAlmacen()}' AND numero='{$movihead->getNumero()}' AND almacen=almacen_destino";
				$numEqual = $this->Movilin->count($conditions);

				$conditions = "comprob='{$movihead->getComprob()}' AND almacen='{$movihead->getAlmacen()}' AND numero='{$movihead->getNumero()}' AND almacen<>almacen_destino";
				$numNotEqual = $this->Movilin->count($conditions);

				if ($numEqual > 0 && !$numNotEqual) {
					$data[] = "comprob='{$movihead->getComprob()}' AND almacen='{$movihead->getAlmacen()}' AND numero='{$movihead->getNumero()}'<br/>";
					foreach($this->Movilin->find("comprob='{$movihead->getComprob()}' AND almacen='{$movihead->getAlmacen()}' AND numero='{$movihead->getNumero()}'") as $movilin){
						$movilin->setAlmacenDestino($movihead->getAlmacenDestino());
						if ($movilin->save() == false) {
							foreach ($movilin->getMessages() as $message) {
								$transaction->rollback($message->getMessage());
							}
						}
					}
				}

			}

			$transaction->commit();

		} catch (TransactionManager $e) {
			$data[] = "Error: " . $e->getMessage();
		}
		return $data;
	}

	public function getRodizioReferenciasAction()
	{
		$response = array();
		$inve = $this->Inve->find(array('rodizio = "S"', 'order' => 'descripcion'));
		foreach ($inve as $inve) {
			$response[] = array(
				'item' => $inve->getItem(),
				'descripcion' => $inve->getDescripcion(),
				'unidad' => $inve->getUnidad(),
			);
		}
		return $response;
	}

}
