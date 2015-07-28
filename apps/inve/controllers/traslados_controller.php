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
 * TrasladosController
 *
 * Controlador de los traslados entre almacenes
 *
 */
class TrasladosController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Movihead',
		'plural' => 'traslados entre almacenes',
		'single' => 'traslado entre almacenes',
		'genre' => 'M',
		'tabName' => 'Traslado',
		'preferedOrder' => 'numero DESC',
		'icon' => 'shipping.png',
		'ignoreButtons' => array(
			'import'
		),
		'fields' => array(
			'almacen' => array(
				'primary' => true,
				'single' => 'Almacén Origen',
				'type' => 'relation',
				'relation' => 'Almacenes',
				'fieldRelation' => 'codigo',
				'cacher' => array('BackCacher', 'getAlmacen'),
				'detail' => 'nom_almacen',
				'filters' => array('int')
			),
			'numero' => array(
				'single' => 'Número',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10,
				'primary' => true,
				'readOnly' => true,
				'filters' => array('int')
			),
			'numero_comprob_contab' => array(
				'single' => 'Comprobante Contable',
				'type' => 'text',
				'size' => 10,
				'maxlength' => 10,
				'readOnly' => true,
				'filters' => array('int')
			),
			'n_pedido' => array(
				'single' => 'Pedido',
				'type' => 'pedido',
				'size' => 10,
				'maxlength' => 10,
				'filters' => array('int')
			),
			'fecha' => array(
				'single' => 'Fecha',
				'type' => 'date',
				'default' => '',
				'filters' => array('date')
			),
			'almacen_destino' => array(
				'single' => 'Almacén Destino',
				'type' => 'relation',
				'relation' => 'Almacenes',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_almacen',
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
				'readOnly' => true,
				'notSearch' => true,
				'filters' => array('float')
			),
			'estado' => array(
				'single' => 'Estado',
				'type' => 'closed-domain',
				'size' => 1,
				'maxlength' => 1,
				'readOnly' => true,
				'values' => array(
					'C' => 'ACTIVO',
					'N' => 'ANULADO'
				),
				'filters' => array('onechar')
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
					'size' => 8,
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
					'size' => 10,
					'maxlength' => 15,
					'filters' => array('float')
				),
				'valor' => array(
					'single' => 'Valor',
					'notNull' => true,
					'type' => 'decimal',
					'size' => 12,
					'maxlength' => 15,
					'filters' => array('float')
				)
			),
			'keys' => array(
				'unique_index' => array(
					'item'
				)
			)
		)
	);

	protected function beforeIndex(){
		Tag::displayTo('almacen', '1');
	}

	protected function beforeNew(){

		//Almacen principal por defecto
		Tag::displayTo('almacen', '1');

		//Fecha por defecto ultimo día hábil del periodo activo
		$fecha = new Date();
		$empresa = $this->Empresa->findFirst();
		$fechaCierre = $empresa->getFCierreI();
		$fechaCierre->addMonths(1);
		if(Date::isLater($fecha, $fechaCierre)){
			Tag::displayTo('fecha', $fechaCierre->getDate());
		} else {
			Tag::displayTo('fecha', $fecha->getDate());
		}
	}

	public function saveAction(){
		$this->setResponse('json');
		$request = $this->getRequestInstance();
		$almacen = $request->getParamPost('almacen','alpha');
		$numero = $request->getParamPost('numero','int');
		$fecha = $request->getParamPost('fecha','date');
		$almacenDestino = $request->getParamPost('almacen_destino','alpha');
		try {
			$comprob = sprintf('T%02s',$almacen);
			$tatico = new Tatico($comprob, $numero, $fecha);
			$movement = array(
				'Comprobante' => $comprob,
				'Fecha' => $fecha,
				'Almacen' => $almacen,
				'AlmacenDestino' => $almacenDestino,
				'NPedido' => $request->getParamPost('n_pedido', 'int'),
				'Estado' => 'C',
				'VTotal' => $request->getParamPost('v_total', 'float'),
				'Observaciones' => $request->getParamPost('observaciones', 'striptags')
			);
			$addDetail = array();
			$removeDetail = array();
			$item = $request->isSetRequestParam('item') ? $request->getParamPost('item') : array();
			$cantidad = $request->isSetRequestParam('cantidad') ? $request->getParamPost('cantidad') : array();
			$cantidad_rec = $request->isSetRequestParam('cantidad_rec') ? $request->getParamPost('cantidad_rec') : array();
			$valor = $request->isSetRequestParam('valor') ? $request->getParamPost('valor') : array();
			$action = $request->isSetRequestParam('action') ? $request->getParamPost('action') : array();
			for($i=0;$i<count($item);$i++){
				if($action[$i]=='add'){
					$addDetail[] = array(
						'Item' => $item[$i],
						'Cantidad' => $cantidad[$i],
						'AlmacenDestino' => $almacenDestino,
						'Valor' => $valor[$i]
					);
				} else {
					if($action[$i]=='del'){
						$removeDetail[] = array(
							'Item' => $item[$i],
							'Cantidad' => $cantidad[$i],
							'AlmacenDestino' => $almacenDestino,
							'Valor' => $valor[$i]
						);
					}
				}
			}
			$movement['Detail'] = $addDetail;
			$movement['removeDetail'] = $removeDetail;
			$tatico->addMovement($movement);
		}
		catch(TaticoException $te){
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
			'message' => 'Se grabó el traslado del almacén con el número: "'.$numeros['inve'].'"',
			'type' => 'insert',
			'primary' => join('&', $primaryKey)
		);
	}

	protected function beforeSearch(){
		$request = ControllerRequest::getInstance();
		$almacen = $request->getParamRequest('almacen');
		return array(
			"comprob = 'T".sprintf('%02s',$almacen)."'"
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
			'valor' => LocaleMath::round($detail->getValor(), 2),
		);
	}

	public function deleteAction(){
		$response = ControllerResponse::getInstance();
		$request = ControllerRequest::getInstance();

		$response->setResponseType(ControllerResponse::RESPONSE_OTHER);
		$response->setResponseAdapter('json');
		View::setRenderLevel(View::LEVEL_NO_RENDER);

		$movement = array();
		if($request->isSetQueryParam('almacen')){
			$movement['Almacen'] = $request->getParamQuery('almacen', 'alpha');
		} else {
			$movement['Almacen'] = 1;
		}
		$movement['Comprobante'] = sprintf("T%02s", $movement['Almacen']);
		if($request->isSetQueryParam('numero')){
			$movement['Numero'] = $request->getParamQuery('numero', 'int');
		} else {
			$movement['Numero'] = 0;
		}
		try {
			$tatico = new Tatico($movement['Comprobante'], $movement['Numero']);
			$tatico->delMovement($movement);
		}
		catch(TaticoException $te){
			return array(
				'status' => 'FAILED',
				'message' => $te->getMessage(),
				'code' => $te->getCode()
			);
		}
		return array(
			'status' => 'OK',
			'message' => 'El traslado ha sido eliminado correctamente'
		);
	}

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

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}


}
