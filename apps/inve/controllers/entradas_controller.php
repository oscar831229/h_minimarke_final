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
 * EntradasController
 *
 * Controlador de las entradas al almacén
 *
 */
class EntradasController extends HyperFormController
{

	static protected $_config = array(
		'model' => 'Movihead',
		'plural' => 'entradas al almacén',
		'single' => 'entrada al almacén',
		'genre' => 'F',
		'tabName' => 'Entrada',
		'preferedOrder' => 'numero DESC',
		'icon' => 'entradas.png',
		'ignoreButtons' => array(
			'import'
		),
		'fields' => array(
			'almacen' => array(
				'primary' => true,
				'single' => 'Almacén',
				'type' => 'relation',
				'relation' => 'Almacenes',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_almacen',
				'cacher' => array('BackCacher', 'getAlmacen'),
				'filters' => array('int')
			),
			'numero' => array(
				'single' => 'Número',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'primary' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'numero_comprob_contab' => array(
				'single' => 'Comprobante Contable',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'readOnly' => true,
				'notBrowse' => true,
				'filters' => array('int')
			),
			'n_pedido' => array(
				'single' => 'Orden de Compra',
				'type' => 'orden',
				'filters' => array('int')
			),
			'nit' => array(
				'single' => 'Proveedor',
				'type' => 'tercero',
				'size' => 10,
				'maxlength' => 14,
				'notNull' => true,
				'filters' => array('terceros')
			),
			'fecha' => array(
				'single' => 'Fecha',
				'type' => 'date',
				'default' => '',
				'filters' => array('date')
			),
			'f_entrega' => array(
				'single' => 'Fecha de Entrega',
				'type' => 'date',
				'default' => '',
				'notSearch' => true,
				'notBrowse' => true,
				'filters' => array('date')
			),
			'factura_c' => array(
				'single' => 'Factura No.',
				'type' => 'int',
				'size' => 10,
				'maxlength' => 10,
				'notNull' => true,
				'notBrowse' => true,
				'filters' => array('int')
			),
			'observaciones' => array(
				'single' => 'Observaciones',
				'type' => 'textarea',
				'rows' => 2,
				'cols' => 40,
				'notBrowse' => true,
				'notSearch' => true,
				'filters' => array('striptags')
			),
			'v_total' => array(
				'single' => 'Valor Total',
				'type' => 'decimal',
				'size' => 10,
				'maxlength' => 10,
				'decimals' => 2,
				'notSearch' => true,
				'readOnly' => true,
				'filters' => array('float')
			)
		),
		'detail' => array(
			'relation' => array('comprob', 'numero'),
			'model' => 'Movilin',
			'tabName' => 'Detalle',
			'fields' => array(
				'item' => array(
					'single' => 'Referencia',
					'type' => 'item',
					'notNull' => true,
					'size' => 7,
					'maxlength' => 15,
					'filters' => array('item')
				),
				'unidad' => array(
					'single' => 'Unidad',
					'type' => 'relation',
					'relation' => 'Unidad',
					'fieldRelation' => 'codigo',
					'detail' => 'nom_unidad',
					'size' => 8,
					'maxlength' => 15,
					'filters' => array('alpha')
				),
				'cantidad' => array(
					'single' => 'Cantidad',
					'notNull' => true,
					'type' => 'decimal',
					'size' => 7,
					'maxlength' => 15,
					'filters' => array('float')
				),
				/*'cantidad_rec' => array(
					'single' => 'A Recibir',
					'notNull' => true,
					'type' => 'decimal',
					'size' => 7,
					'maxlength' => 15,
					'filters' => array('float')
				),*/
				'valor' => array(
					'single' => 'Valor',
					'notNull' => true,
					'type' => 'decimal',
					'size' => 12,
					'maxlength' => 15,
					'filters' => array('float')
				),
				'iva' => array(
					'single' => 'Iva',
					'notNull' => true,
					'type' => 'closed-domain',
					'values' => array(
						'0' => '0',
						'5' => '5',
						'10' => '10',
						'16' => '16'
					),
					'useDummy' => false,
					'align' => 'right',
					'filters' => array('float')
				)
			),
			'keys' => array(
				'unique_index' => array(
					'item'
				)
			)
		),
		'extras' => array(
			0 => array(
				'relation' => array('comprob','almacen', 'numero'),
				'model' => 'Movih1',
				'tabName' => 'Totales',
				'fields' => array(
					'iva16r' => array(
						'single' => 'IVA 16% Retenido',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'iva16d' => array(
						'single' => 'IVA 16% Descontable',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'iva10r' => array(
						'single' => 'IVA 10% Retenido',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'iva10d' => array(
						'single' => 'IVA 10% Descontable',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'iva5r' => array(
						'single' => 'IVA 5% Retenido',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'iva5d' => array(
						'single' => 'IVA 5% Descontable',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'retencion' => array(
						'single' => 'Retención',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'ica' => array(
						'single' => 'ICA',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'horti' => array(
						'single' => 'Retención Hortifrutícula',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'cree' => array(
						'single' => 'Retención CREE',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'impo' => array(
						'single' => 'IVA Costo/Gasto',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'total_neto' => array(
						'single' => 'Total Entrada',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'saldo' => array(
						'single' => 'Total Impuestos',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					),
					'total' => array(
						'single' => 'Total a Pagar',
						'type' => 'decimal',
						'notNull' => false,
						'size' => 10,
						'maxlength' => 10,
						'filters' => array('decimal')
					)
				)
			)
		)
	);

	public function initialize(){
		$hortifruticula = Settings::get('hortifruticula');
		if($hortifruticula!='S'){
			unset(self::$_config['extras'][1]['fields']['horti']);
			unset(self::$_config['extras'][1]['fields']['spacer0']);
		}
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	public function getEditTitle($title, $record){
		return $title.' '.$record->getNumero();
	}

	protected function beforeIndex(){
		Tag::displayTo('almacen', '1');
	}

	protected function beforeNew(){

		//Almacen principal por Defecto
		Tag::displayTo('almacen', '1');

		$fecha = new Date();
		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierreI();
		$fechaCierre->addMonths(1);
		if(Date::isLater($fecha, $fechaCierre)){
			Tag::displayTo('fecha', $fechaCierre->getDate());
			Tag::displayTo('f_entrega', $fechaCierre->getDate());
		} else {
			Tag::displayTo('fecha', $fecha->getDate());
			Tag::displayTo('f_entrega', $fecha->getDate());
		}

		//Valores predeterminados para la pestaña de totales
		Tag::displayTo('iva16r', 0);
		Tag::displayTo('iva16d', 0);
		Tag::displayTo('iva10r', 0);
		Tag::displayTo('iva10d', 0);
		Tag::displayTo('iva5r', 0);
		Tag::displayTo('iva5d', 0);
		Tag::displayTo('retencion', 0);
		Tag::displayTo('ica', 0);
		Tag::displayTo('horti', 0);
		Tag::displayTo('cree', 0);
		Tag::displayTo('impo', 0);
		Tag::displayTo('total_neto', 0);
		Tag::displayTo('saldo', 0);
		Tag::displayTo('total', 0);
	}

	public function beforeEdit(){
		$request = ControllerRequest::getInstance();
		$almacen = $request->getParamRequest('almacen');
		$numero = $request->getParamRequest('numero');
		$comprob = "E".sprintf('%02s',$almacen);
		$criterio = $this->Criterio->findFirst("comprob='$comprob' AND numero='$numero'");
		if($criterio!=false){
			foreach($criterio->getAttributes() as $field){
				Tag::displayTo($field,$criterio->readAttribute($field));
			}
		}
		$movihead = $this->Movihead->findFirst("comprob='$comprob' AND numero='$numero'");
		if($movihead!=false){
			$fields = array(
				'retencion' => 'retencion',
				'ica' => 'ica',
				'iva' => 'iva16d',
				'descuento' => 'iva16r',
				'ivad' => 'iva10d',
				'ivam' => 'iva10r',
				'total_neto' => 'total_neto'
			);
			foreach($fields as $field => $nameField){
				Tag::displayTo($nameField, (int)$movihead->readAttribute($field));
			}
		}
		$movih1 = $this->Movih1->findFirst("comprob='$comprob' AND numero='$numero'");
		if($movih1!=false){
			$fields = array(
				'iva5' => 'iva5d',
				'retiva5' => 'iva5r',
				'reten1' => 'horti',
				'cree' => 'cree'
			);
			foreach($fields as $field => $nameField){
				Tag::displayTo($nameField, $movih1->readAttribute($field));
			}
		}
	}

	/**
	 * Recibe los valores de la entrada (Crea y Guarda)
	 *
	 * @return array
	 */
	public function saveAction(){

		$this->setResponse('json');

		$request = $this->getRequestInstance();
		$almacen = $request->getParamPost('almacen', 'int');
		$numero = $request->getParamPost('numero', 'int');
		$fecha = $request->getParamPost('fecha', 'date');

		try {

			$comprob = sprintf('E%02s', $almacen);
			$tatico = new Tatico($comprob, $numero, $fecha);

			//Crear el movimiento
			$movement = array(
				'Comprobante' => $comprob,
				'Fecha' => $fecha,
				'Almacen' => $almacen,
				'Nit' => $request->getParamPost('nit', 'terceros'),
				'NPedido' => $request->getParamPost('n_pedido', 'int'),
				'FechaVencimiento' => $fecha,
				'FechaEntrega' => $fecha,
				'FacturaC' => $request->getParamPost('factura_c', 'int'),
				'AlmacenDestino' => $almacen,
				'Estado' => 'C',
				'Observaciones' => $request->getParamPost('observaciones', 'striptags')
			);
			$addDetail = array();
			$removeDetail = array();

			//Criterio
			$criterio = array(
				'Sc' => $request->getParamPost('sc','alpha'),
				'Pr' => $request->getParamPost('pr','alpha'),
				'Maj' => $request->getParamPost('maj','alpha'),
				'Tra' => $request->getParamPost('tra','alpha'),
				'Up' => $request->getParamPost('up','alpha'),
				'Cte' => $request->getParamPost('cte','alpha'),
				'Fra' => $request->getParamPost('fra','alpha'),
				'Pd' => $request->getParamPost('pd','alpha')
			);
			$movement['Criterio'] = $criterio;

			//Totales
			$totales = array(
				'Iva16R' => $request->getParamPost('iva16r', 'float'),
				'Iva16D' => $request->getParamPost('iva16d', 'float'),
				'Iva10R' => $request->getParamPost('iva10r', 'float'),
				'Iva10D' => $request->getParamPost('iva10d', 'float'),
				'Iva5R' => $request->getParamPost('iva5r', 'float'),
				'Iva5D' => $request->getParamPost('iva5d', 'float'),
				'Retencion' => $request->getParamPost('retencion', 'float'),
				'Ica' => $request->getParamPost('ica', 'float'),
				'Horti' => $request->getParamPost('horti', 'float'),
				'Cree' => $request->getParamPost('cree', 'float'),
				'Impo' => $request->getParamPost('impo', 'float'),
			);
			$movement['Totales'] = $totales;

			if($request->isSetRequestParam('item')){
				$item = $request->getParamPost('item', 'alpha');
			} else {
				$item = array();
			}
			if($request->isSetRequestParam('cantidad')){
				$cantidad = $request->getParamPost('cantidad', 'float');
			} else {
				$cantidad = array();
			}
			if($request->isSetRequestParam('cantidad_rec')){
				$cantidad_rec = $request->getParamPost('cantidad_rec', 'float');
			} else {
				$cantidad_rec = array();
			}
			if($request->isSetRequestParam('valor')){
				$valor = $request->getParamPost('valor', 'float');
			} else {
				$valor = array();
			}
			if($request->isSetRequestParam('iva')){
				$iva = $request->getParamPost('iva', 'double');
			} else {
				$iva = array();
			}
			if($request->isSetRequestParam('action')){
				$action = $request->getParamPost('action', 'alpha');
			} else {
				$action = array();
			}

			for($i=0;$i<count($item);$i++){
				if(isset($action[$i])){
					if($action[$i]=='add'){
						$addDetail[] = array(
							'Item' => $item[$i],
							'Cantidad' => $cantidad[$i],
							'CantidadRecibida' => $cantidad[$i],
							'Valor' => $valor[$i],
							'Iva' => $iva[$i]
						);
					} else {
						if($action[$i]=='del'){
							$removeDetail[] = array(
								'Item' => $item[$i],
								'Cantidad' => $cantidad[$i],
								'CantidadRecibida' => $cantidad[$i],
								'Valor' => $valor[$i],
								'Iva' => $iva[$i]
							);
						}
					}
				}
			}
			$movement['Detail'] = $addDetail;
			$movement['removeDetail'] = $removeDetail;

			//Agregar a Tatico
			$tatico->addMovement($movement);
		}
		catch(TaticoException $te){
			return array(
				'status' => 'FAILED',
				'message' => $te->getMessage(),
				'code' => $te->getCode()
			);
		}
		catch(GardienException $te){
			return array(
				'status' => 'FAILED',
				'message' => $te->getMessage(),
				'code' => $te->getCode()
			);
		}

		$numeros = $tatico->getLastConsecutivos();
		$primaryKey = array(
			'comprob='.$comprob,
			'almacen='.$almacen,
			'numero='.$numeros['inve'],
		);

		return array(
			'status' => 'OK',
			'numero' => $numeros['inve'],
			'numeroComprobContab' => $numeros['contab'],
			'message' => 'Se grabó la entrada al almacén con el número: "'.$numeros['inve'].'"',
			'type' => 'insert',
			'primary' => join('&', $primaryKey)
		);
	}

	protected function beforeSearch(){
		$request = ControllerRequest::getInstance();
		$almacen = $request->getParamRequest('almacen');
		return array(
			"comprob = 'E".sprintf('%02s',$almacen)."'"
		);
	}

	/**
	 * Genera el nombre del item en el detalle
	 *
	 * @param	string $detail
	 * @return	array
	 */
	public function describeDetail($detail){
		$inve = BackCacher::getInve($detail->getItem());
		if($inve==false){
			$descripcion = '';
			$unidad = '';
		} else {
			$descripcion = $inve->getDescripcion();
			$unidad = $inve->getUnidad();
		}
		return array(
			'id' => $detail->getId(),
			'item' => $detail->getItem(),
			'item_det' => $descripcion,
			'unidad' => $unidad,
			'cantidad' => LocaleMath::round($detail->getCantidad(), 2),
			'cantidad_rec' => LocaleMath::round($detail->getCantidadRec(), 2),
			'valor' => LocaleMath::round($detail->getValor(), 2),
			'iva' => $detail->getIva()
		);
	}

	public function deleteAction(){
		$this->setResponse('json');
		$movement = array();
		$request = ControllerRequest::getInstance();
		if($request->isSetQueryParam('almacen')){
			$movement['Almacen'] = $request->getParamQuery('almacen', 'alpha');
		} else {
			$movement['Almacen'] = 1;
		}
		$movement['Comprobante'] = sprintf("E%02s", $movement['Almacen']);
		if($request->isSetQueryParam('numero')){
			$movement['Numero'] = $request->getParamQuery('numero', 'int');
		} else {
			return array(
				'status' => 'FAILED',
				'message' => 'No se encontró la salida del almacén a eliminar'
			);
		}
		try {
			$transaction = TransactionManager::getUserTransaction();

			$tatico = new Tatico($movement['Comprobante'], $movement['Numero']);
			$tatico->delMovement($movement);
		}
		catch(Exception $te){
			return array(
				'status' => 'FAILED',
				'message' => $te->getMessage()
			);
		}
		return array(
			'status' => 'OK',
			'message' => 'La entrada fue eliminada correctamente'
		);
	}

	/**
	 * Imprime el reporte de la entrada
	 *
	 * @return array
	 */
	public function doReportAction(){
		$this->setResponse('json');
		try {
			$reportType = $this->getPostParam('reportType', 'alpha');
			$codigoComprobante = $this->getPostParam('codigoComprobante', 'comprob');
			$codigoAlmacen = $this->getPostParam('codigoAlmacen', 'int');
			$numero = $this->getPostParam('numero', 'int');

			$fileUri = Tatico::getPrintUrl($reportType, $codigoComprobante, $codigoAlmacen, $numero);
			return array(
				'status' => 'OK',
				'file' => $fileUri
			);
		}
		catch(TaticoException $e){
			return array(
				'status' => 'FAILED',
				'message' => $e->getMessage()
			);
		}
	}

}
