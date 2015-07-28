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
 * ReferenciasController
 *
 * Controlador de las referencias
 *
 */
class ReferenciasController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Inve',
		'plural' => 'referencias',
		'single' => 'referencia',
		'genre' => 'F',
		'preferedOrder' => 'descripcion',
		'tabName' => 'General',
		'icon' => 'beans.png',
		'fields' => array(
			'item' => array(
				'single' => 'Código',
				'type' => 'text',
				'size' => 15,
				'maxlength' => 15,
				'primary' => true,
				'filters' => array('alpha')
			),
			'descripcion' => array(
				'single' => 'Descripción',
				'type' => 'text',
				'size' => 60,
				'maxlength' => 60,
				'filters' => array('striptags', 'extraspaces')
			),
			'linea' => array(
				'single' => 'Línea de Producto',
				'type' => 'relation',
				'relation' => 'Lineas',
				'fieldRelation' => 'linea',
				'detail' => 'nombre',
				'filters' => array('alpha')
			),
			'unidad' => array(
				'single' => 'Unidad',
				'type' => 'relation',
				'relation' => 'Unidad',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_unidad',
				'filters' => array('alpha')
			),
			'unidad_porcion' => array(
				'single' => 'Unidad Porción',
				'type' => 'relation',
				'relation' => 'Unidad',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_unidad',
				'notSearch' => true,
				'notBrowse' => true,
				'notNull' => true,
				'filters' => array('alpha')
			),
			'peso' => array(
				'single' => 'Peso',
				'type' => 'decimal',
				'size' => 14,
				'maxlength' => 14,
				'notSearch' => true,
				'notBrowse' => true,
				'notNull' => true,
				'filters' => array('float')
			),
			'saldo_actual' => array(
				'single' => 'Saldo Actual',
				'type' => 'decimal',
				'size' => 14,
				'maxlength' => 14,
				'notSearch' => true,
				'notBrowse' => true,
				'readOnly' => true,
				'filters' => array('float')
			),
			'volumen' => array(
				'single' => 'Número Tragos Botella',
				'type' => 'decimal',
				'size' => 14,
				'maxlength' => 14,
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('float')
			),
			'fisico' => array(
				'single' => 'Saldo Físico',
				'type' => 'decimal',
				'size' => 14,
				'maxlength' => 14,
				'notSearch' => true,
				'notBrowse' => true,
				'readOnly' => true,
				'filters' => array('float')
			),
			'precio_venta_m' => array(
				'single' => 'Precio de Venta',
				'type' => 'decimal',
				'size' => 14,
				'maxlength' => 14,
				'decimals' => 2,
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('float')
			),
			'costo_actual' => array(
				'single' => 'Costo Actual',
				'type' => 'decimal',
				'size' => 14,
				'maxlength' => 14,
				'notSearch' => true,
				'notBrowse' => true,
				'readOnly' => true,
				'filters' => array('float')
			),
			'producto' => array(
				'single' => 'Tipo de Referencia',
				'type' => 'relation',
				'relation' => 'Producto',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_producto',
				'filters' => array('alpha')
			),
			'iva' => array(
				'single' => 'Porcentaje IVA',
				'type' => 'closed-domain',
				'values' => array(
					'0' => '0 %',
					'7' => '7 %',
					'10' => '10 %',
					'16' => '16 %'
				),
				'useDummy' => false,
				'align' => 'right',
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('float')
			),
			'iva_venta' => array(
				'single' => 'Porcentaje IVA Venta',
				'type' => 'closed-domain',
				'values' => array(
					'0' => '0 %',
					'7' => '7 %',
					'10' => '10 %',
					'16' => '16 %'
				),
				'useDummy' => false,
				'align' => 'right',
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('int')
			),
			/*'por_recibir' => array(
				'single' => 'Prec Venta por Trago',
				'type' => 'decimal',
				'size' => 14,
				'maxlength' => 14,
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('float')
			),*/
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'values' => array(
					'A' => 'ACTIVO',
					'I' => 'INACTIVO'
				),
				'filters' => array('alpha')
			)
		),
		'extras' => array(
			0 => array(
				'partial' => 'stocks',
				'tabName' => 'Stocks',
			)
		)
	);

	public function beforeNew(){
		$almacenes = $this->Almacenes->find("estado='A'");
		$this->setParamToView('almacenes', $almacenes);
		$this->setParamToView('state', 'new');
		Tag::displayTo('estado', 'A');
	}

	public function beforeEdit(){
		$stocks = array();
		$item = $this->getPostParam('item', 'alpha');
		$almacenes = $this->Almacenes->find("estado='A'");
		foreach($almacenes as $almacen){
			$inveStock = $this->InveStocks->findFirst("item='$item' AND almacen='{$almacen->getCodigo()}'");
			if($inveStock==false){
				$stocks[$almacen->getCodigo()]['minimo'] = 0;
				$stocks[$almacen->getCodigo()]['maximo'] = 0;
			} else {
				$stocks[$almacen->getCodigo()]['minimo'] = $inveStock->getMinimo();
				$stocks[$almacen->getCodigo()]['maximo'] = $inveStock->getMaximo();
			}
			$stocks[$almacen->getCodigo()]['saldo'] = Tatico::getSaldo($item, $almacen->getCodigo());
			$stocks[$almacen->getCodigo()]['promedio'] = Tatico::getSaldoPromedio($item, $almacen->getCodigo());
		}
		$this->setParamToView('stocksAlmacenes', $stocks);
		$this->setParamToView('almacenes', $almacenes);
		$this->setParamToView('state', 'edit');
	}

	private function _actualizarStocks($transaction, $record){
		$numero = 0;
		$almacenes = $this->getPostParam('almacen', 'int');
		$minimo = $this->getPostParam('minimo', 'float');
		$maximo = $this->getPostParam('maximo', 'float');
		foreach($almacenes as $codigoAlmacen){
			if(isset($minimo[$numero])&&isset($maximo[$numero])){
				$inveStock = $this->InveStocks->findFirst("almacen='$codigoAlmacen' AND item='{$record->getItem()}'");
				if($minimo[$numero]>0||$maximo[$numero]>0){
					if($minimo[$numero]>$maximo[$numero]){
						$almacen = BackCacher::getAlmacen($codigoAlmacen);
						$this->appendMessage('El stock mínimo es mayor al stock máximo en el almacén '.$almacen->getNomAlmacen());
						return false;
					} else {
						if($inveStock==false){
							$inveStock = new InveStocks();
						}
						$inveStock->setTransaction($transaction);
						$inveStock->setItem($record->getItem());
						$inveStock->setAlmacen($codigoAlmacen);
						$inveStock->setMinimo($minimo[$numero]);
						$inveStock->setMaximo($maximo[$numero]);
						if($inveStock->save()==false){
							foreach($inveStock->getMessages() as $message){
								$this->appendMessage('Stock: '.$message->getMessage());
								return false;
							}
						}
					}
				} else {
					if($inveStock!=false){
						$inveStock->setTransaction($transaction);
						$inveStock->delete();
					}
				}
			}
			$numero++;
		}
		return true;
	}

	public function afterInsert($transaction, $record){
		return $this->_actualizarStocks($transaction, $record);
	}

	public function afterUpdate($transaction, $record){
		return $this->_actualizarStocks($transaction, $record);
	}

	/**
	 * Busca por codigo de item datos para facturar
	 * @param mixer nit
	 * @param mixer codigo
	 * @param mixer contrato
	 * @return JSON 
	 */
	public function queryByItemAction()
	{
		$this->setResponse('json');
		
		$nit 			= $this->getQueryParam('nit', 'alpha');
		$numeroItem 	= $this->getQueryParam('codigo', 'alpha');
		$numeroContrato = $this->getQueryParam('contrato', 'alpha');
		
		$inve = $this->Inve->findFirst("item='$numeroItem'");
		
		if ($inve!=false) {

			if (!$inve->getPrecioVentaM()) {
				return array(
					'status' => 'FAILED',
					'message' => 'No se ha definido el precio de venta de la referencia '.$numeroItem.' ('.$inve->getDescripcion().')'
				);
			} else {
				
				//Verificamos si esta en lista de precios esa referencia con ese tercero y ese contrato
				$listaPrecios = $this->ListaPrecios->findFirst("referencia='$numeroItem' AND nit='$nit' AND contrato='$numeroContrato'");
				
				$precio = $inve->getPrecioVentaM();
				if ($listaPrecios!=false) {
					$precio = $listaPrecios->getPrecioVenta();
				}
				
				return array(
					'status' => 'OK',
					'nombre' => $inve->getDescripcion(),
					'precio' => $precio
				);
			}
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'No existe la referencia '.$numeroItem
			);
		}
	} 

	public function queryByNameAction(){
		$this->setResponse('json');
		$response = array();
		$nombre = $this->getPostParam('nombre', 'extraspaces');
		if($nombre!=''){
			$items = $this->Inve->find(array("descripcion LIKE '".$nombre."%' AND estado='A'", 'order' => 'descripcion', 'limit' => '13'));
			foreach($items as $item){
				$response[] = array(
					'value' => $item->getItem(),
					'selectText' => $item->getDescripcion(),
					'optionText' => $item->getDescripcion()
				);
			}
		}
		return $response;
	}

	public function initialize(){
		if(!Gardien::hasAppAccess('FC')){
			unset(self::$_config['fields']['precio_venta']);
			unset(self::$_config['fields']['iva_venta']);
		}
		parent::setConfig(self::$_config);
		parent::initialize();
	}

}
