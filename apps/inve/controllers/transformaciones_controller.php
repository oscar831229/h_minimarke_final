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
 * TransformacionesController
 *
 * Controlador de las ajustes al almacén
 *
 */
class TransformacionesController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Movihead',
		'plural' => 'transformaciones',
		'single' => 'transformación',
		'genre' => 'F',
		'tabName' => 'Transformación',
		'preferedOrder' => 'numero DESC',
		'icon' => 'sitemap.png',
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
			'fecha' => array(
				'single' => 'Fecha',
				'type' => 'date',
				'default' => '',
				'filters' => array('date')
			),
			'nota' => array(
				'single' => 'Tipo de Trasformación',
				'type' => 'closed-domain',
				'values' => array(
					'1N' => 'UNO A MUCHOS',
					'N1' => 'MUCHOS A UNO'
				),
				'filters' => array('alpha')
			),
			'item_objetivo' => array(
				'single' => 'Referencia Base/Destino',
				'type' => 'item',
				'notNull' => true,
				'extraField' => true,
				'size' => 7,
				'maxlength' => 15,
				'filters' => array('item')
			),
			'unidad_objetivo' => array(
				'single' 		=> 'Unidad',
				'type' 			=> 'relation',
				'relation' 		=> 'Unidad',
				'fieldRelation' => 'codigo',
				'detail' 		=> 'nom_unidad',
				'size' 			=> 8,
				'maxlength' 	=> 15,
				'notSearch' 	=> true,
				'extraField' 	=> true,
				'filters' 		=> array('alpha')
			),
			'cantidad_objetivo' => array(
				'single' => 'Cantidad',
				'notNull' => true,
				'type' => 'decimal',
				'size' => 10,
				'maxlength' => 15,
				'notSearch' => true,
				'extraField' => true,
				'filters' => array('float')
			),
			'v_total' => array(
				'single' => 'Valor Total',
				'type' => 'decimal',
				'size' => 10,
				'maxlength' => 10,
				'notSearch' => true,
				'showOnly' => true,
				'filters' => array('float')
			),
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
					'readonly' => true,
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

	public function initialize(){
		parent::setConfig(self::$_config);
		parent::initialize();
	}

	protected function beforeIndex(){
		Tag::displayTo('almacen', '1');
	}

	protected function beforeNew(){
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

	/**
	 * Metodo que guarda una trasformación
	 *
	 * @see HyperFormController::saveAction()
	 */
	public function saveAction(){
		//sprint_r($_REQUEST);
		$this->setResponse('json');
		$request = $this->getRequestInstance();
		$almacen = $request->getParamPost('almacen');
		$numero = $request->getParamPost('numero','int');
		$fecha = $request->getParamPost('fecha','date');
		$nota = $request->getParamPost('nota');
		try {

			$comprob = sprintf('R%02s',$almacen);
			$tatico = new Tatico($comprob, $numero, $fecha);

			$movement = array(
				'Comprobante' => $comprob,
				'Fecha' => $fecha,
				'Almacen' => $almacen,
				'Tipo' => $request->getParamPost('nota', 'alpha'),
				'ItemTarget' => $request->getParamPost('item_objetivo'),
				'CantidadTarget' => $request->getParamPost('cantidad_objetivo'),
				'VTotal' => $request->getParamPost('v_total','float'),
				'Estado' => 'C'
			);
			//print_r($movement);
			if($request->isSetRequestParam('item')){
				$item =  $request->getParamPost('item');
			}else{
				$item = array();
			}
			if($request->isSetRequestParam('cantidad')){
				$cantidad = $request->getParamPost('cantidad');
			}else{
				$cantidad = array();
			}
			if($request->isSetRequestParam('unidad')){
				$unidad = $request->getParamPost('unidad');
			}else{
				$unidad = array();
			}
			if($request->isSetRequestParam('valor')){
				$valor = $request->getParamPost('valor');
			}else{
				$valor = array();
			}
			if($request->isSetRequestParam('action')){
				$action = $request->getParamPost('action');
			}else{
				$action = array();
			}

			$valorBase = $request->getParamPost('v_total', 'double');
			$itemObjetivo = $request->getParamPost('item_objetivo');
			$cantidadObjetivo = $request->getParamPost('cantidad_objetivo');

			//calculamos los valores de transformaciones
			$calculos = Tatico::getCalcularTransformacion(array(
				'itemBase' => $itemObjetivo,
				'cantidad_objetivo' => $cantidadObjetivo,
				'items' => $item,
				'cantidades' => $cantidad,
				'valorTotal' => $valorBase,
				'nota' => $nota,
				'debug' => false
			));

			$addDetail = array();
			$removeDetail = array();
			for($i=0;$i<count($item);$i++){

				if(!isset($action[$i])){
					continue;
				}

				if(isset($calculos[$item[$i]]) && isset($calculos[$item[$i]]['valorUnitario'])){
					//Si solo hay una referencia se deja el valor base
					if(count($item) > 1){
					  if(isset($calculos[$item[$i]]['valorTotalPeso'])){
					    $valorTotal = $calculos[$item[$i]]['valorTotalPeso'];
					  } else {
					      return array(
			                'status' => 'FAILED',
			                'message' => "Al parecer sucedio algo con el calculo de la transformación >".print_r($calculos,1)
			              );
					  }
					}else{
						$valorTotal = $valorBase;
					}
				} else {
					$valorTotal = $valor[$i];
				}

				if($action[$i]=='add'){
					$addDetail[] = array(
						'Item' => $item[$i],
						'Cantidad' => $cantidad[$i],
						'Unidad' => $unidad[$i],
						'Valor' => $valorTotal
					);
				} else {
					if($action[$i]=='del'){
						$removeDetail[] = array(
							'Item' => $item[$i],
							'Cantidad' => $cantidad[$i],
							'Unidad' => $unidad[$i],
							'Valor' => $valor[$i],
						);
					}
				}
			}
			$movement['Detail'] = $addDetail;
			$movement['removeDetail'] = $removeDetail;

		      //print_r($calculos);
		      //print_r($movement);

			$tatico->addMovement($movement);
		}
		catch(TaticoException $te){
			return array(
				'status' => 'FAILED',
				'message' => $te->getMessage()
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
			'message' => 'Se grabó la trasformación con el número: "'.$numeros['inve'].'"',
			'type' => 'insert',
			'primary' => join('&', $primaryKey)
		);
	}

	/**
	* Metodo que borra una transformación
	*/
	public function deleteAction(){
		$this->setResponse('json');
		$movement = array();
		$request = ControllerRequest::getInstance();
		if($request->isSetQueryParam('almacen')){
			$movement['Almacen'] = $request->getParamQuery('almacen', 'alpha');
		} else {
			$movement['Almacen'] = 1;
		}
		$movement['Comprobante'] = sprintf("R%02s", $movement['Almacen']);
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
				'message' => $te->getMessage()
			);
		}
		return array(
			'status' => 'OK',
			'message' => 'La trasformación fue eliminada correctamente'
		);
	}

	/**
	 * Metodo que se ejecuta antes de buscar registros pero agrega condiciones si hay previas
	 */
	protected function beforeSearch($conditions=array()){
		$request = ControllerRequest::getInstance();
		$almacen = $request->getParamRequest('almacen', 'int');
		if(count($conditions)>0){
			$conditions[]= "comprob = 'R".sprintf('%02s',$almacen)."'";
		}
		$conditions[]= "comprob like 'R%'";
		return $conditions;
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
			'valor' => LocaleMath::round($detail->getValor(), 2)
		);
	}

	public function getExtraField($field, $record){
		$value = '';
		$details = Tatico::getMovementDetail($record->getComprob(), $record->getAlmacen(), $record->getNumero());
		foreach($details as $detail){
			if($detail->getNumLinea()==1){
				break;
			}
		}
		$inve = $this->Inve->findFirst("item='{$detail->getItem()}'");
		switch($field){
			case 'item_objetivo': $value = $detail->getItem();break;
			case 'unidad_objetivo': $value = $inve->getUnidad();break;
			case 'cantidad_objetivo': $value = abs($detail->getCantidad());break;
		}
		return $value;
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

}
