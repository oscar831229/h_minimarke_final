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
 * AjustesController
 *
 * Controlador de ajustes al almacen
 *
 */
class AjustesController extends HyperFormController {

	static protected $_config = array(
		'model' => 'Movihead',
		'plural' => 'ajustes a inventarios',
		'single' => 'ajuste a inventario',
		'genre' => 'M',
		'tabName' => 'Ajuste',
		'preferedOrder' => 'numero DESC',
		'icon' => 'ajustes.png',
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
				'type' => 'int',
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
			'fecha' => array(
				'single' => 'Fecha',
				'type' => 'date',
				'default' => '',
				'filters' => array('date')
			),
			'centro_costo' => array(
				'single' => 'Centro de Costo',
				'type' => 'relation',
				'relation' => 'Centros',
				'fieldRelation' => 'codigo',
				'detail' => 'nom_centro',
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
					'size' => 7,
					'maxlength' => 13,
					'filters' => array('float')
				),
				'valor' => array(
					'single' => 'Valor',
					'notNull' => true,
					'type' => 'decimal',
					'size' => 10,
					'maxlength' => 15,
					'filters' => array('float')
				),
				'tipo' => array(
					'single' => 'Operación',
					'type' => 'closed-domain',
					'align' => 'right',
					'notNull' => true,
					'useDummy' => false,
					'values' => array(
						'RESTAR' => 'RESTAR',
						'SUMAR' => 'SUMAR'
					),
					'filters' => array('alpha')
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
		$almacen = $request->getParamPost('almacen', 'alpha');
		$numero = $request->getParamPost('numero', 'int');
		$fecha = $request->getParamPost('fecha', 'date');
		try {
			$comprob = sprintf('A%02s',$almacen);
			$tatico = new Tatico($comprob, $numero, $fecha);
			$movement = array(
				'Comprobante' => $comprob,
				'Fecha' => $fecha,
				'Almacen' => $almacen,
				'CentroCosto' => $request->getParamPost('centro_costo','int'),
				'Estado' => 'C',
				'Observaciones' => $request->getParamPost('observaciones', 'striptags'),
				'VTotal' => $request->getParamPost('v_total','float')
			);
			$addDetail = array();
			$removeDetail = array();
			$item = $request->isSetRequestParam('item') ? $request->getParamPost('item') : array();
			$cantidad = $request->isSetRequestParam('cantidad') ? $request->getParamPost('cantidad') : array();
			$cantidad_rec = $request->isSetRequestParam('cantidad_rec') ? $request->getParamPost('cantidad_rec') : array();
			$valor = $request->isSetRequestParam('valor') ? $request->getParamPost('valor') : array();
			$action = $request->isSetRequestParam('action') ? $request->getParamPost('action') : array();
			$tipo = $request->isSetRequestParam('tipo') ? $request->getParamPost('tipo') : array();
			for($i=0;$i<count($item);$i++){
				if(isset($action[$i])){
                    if (!isset($tipo[$i])) {
                        $tipo[$i]="SUMA";
                    }
					if($action[$i]=='add'){
						$addDetail[] = array(
							'Item' => $item[$i],
							'Cantidad' => $cantidad[$i],
							'CantidadRecibida' => $cantidad[$i],
							'Valor' => $valor[$i],
							'Tipo' => $tipo[$i]
						);
					} else {
						if($action[$i]=='del'){
							$removeDetail[] = array(
								'Item' => $item[$i],
								'Cantidad' => $cantidad[$i],
								'CantidadRecibida' => $cantidad[$i],
								'Valor' => $valor[$i],
								'Tipo' => $tipo[$i]
							);
						}
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
			'message' => 'Se grabó el ajuste al almacén con el número: "'.$numeros['inve'].'"',
			'type' => 'insert',
			'primary' => join('&', $primaryKey)
		);
	}

	protected function beforeSearch(){
		$request = ControllerRequest::getInstance();
		$almacen = $request->getParamRequest('almacen');
		return array(
			"comprob = 'A".sprintf('%02s',$almacen)."'"
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
		$movement['Comprobante'] = sprintf("A%02s", $movement['Almacen']);
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
			'message' => 'El ajuste fue eliminado correctamente'
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
		if($detail->getCantidad()<0){
			$tipo = 'RESTAR';
			$cantidad = LocaleMath::round(-$detail->getCantidad(), 2);
			$valor = LocaleMath::round(-$detail->getValor(), 2);
		} else {
			$tipo = 'SUMAR';
			$cantidad = LocaleMath::round($detail->getCantidad(), 2);
			$valor = LocaleMath::round($detail->getValor(), 2);
		}
		return array(
			'id' => $detail->getId(),
			'item' => $detail->getItem(),
			'item_det' => $descripcion,
			'unidad' => $unidad,
			'cantidad' => $cantidad,
			'valor' => $valor,
			'tipo' => $tipo
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

}
